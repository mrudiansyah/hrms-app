<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use DateTime;
use Auth;
use PDF;

class ess_controller extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }
    #region Slip & index
    function index()
    {
        $today = date('Y-m-d');
        $tb_utility = DB::table('tb_utilities')->where('atribut', 'expired_password')->where('status', '1')->count();
        if ($tb_utility > 0) {
            $locked = Auth::user()->locked_status;
            $expired_date = Auth::user()->expired_date;
            if ($locked == 1) {
                return abort(403, 'Forbidden');
            }
            if ($expired_date < $today) {
                return redirect('/ChangePassword');
            }
        }
        $email = Auth::user()->email;
        $tb_employee = DB::table('tb_emails')
            ->leftjoin('tb_employees', 'tb_employees.id', '=', 'tb_emails.id_employee')
            ->where('email_address', $email)->get();
        foreach ($tb_employee as $dt) {
            $id = $dt->id_employee;
            $pin = $dt->PIN;
            $badgenumber = $dt->badgenumber;
        }
        //return $email;
        date_default_timezone_set("Asia/Jakarta");
        $Tgl = date('Y-m-d');

        $tb_employee = DB::table('tb_employees')
            ->leftjoin('tb_departments', 'tb_departments.id', '=', 'tb_employees.dept_id')
            ->leftjoin('tb_positions', 'tb_positions.id', '=', 'tb_employees.position_id')
            ->leftjoin('tb_work_contract', 'tb_work_contract.id_employee', '=', 'tb_employees.id')
            ->leftjoin('tb_work_shift', 'tb_work_shift.id', '=', 'tb_work_contract.id_work_shift')
            ->leftjoin('tb_work_group', 'tb_work_group.id', '=', 'tb_work_shift.id_work_group')
            ->leftJoin('tb_employee_detail', 'tb_employee_detail.id_employee', '=', 'tb_employees.id')
            ->where([['tb_employees.id', $id]])->limit(1)->get(['tb_employees.*', 'tb_departments.dept_code', 'tb_departments.dept_name', 'tb_departments.divisi', 'tb_positions.position_name', 'tb_work_contract.id_work_shift as id_shift', 'tb_employee_detail.agama', 'tb_employee_detail.tanggal_lahir', 'tb_employee_detail.tempat_lahir', 'tb_employee_detail.nomor_telepon', 'tb_employee_detail.golongan_darah', 'tb_work_shift.shift_code as shift_code', 'tb_work_group.id as id_work_group', 'tb_work_shift.start_implement', 'tb_work_group.cycle_day']);
        $shift_code = '';
        foreach ($tb_employee as $dt) {
            $shift_code = $dt->shift_code;
            $tgl1 = new DateTime($dt->start_implement);
            $tgl2 = new DateTime($Tgl);
            $diffdays = $tgl2->diff($tgl1)->days;
            $cycle = $dt->cycle_day;
            $diffcycle = Floor($diffdays / $cycle);
            $modcycle = $diffdays % $cycle;
            $modcycle++;

            $tabel = DB::table('tb_work_cycle')
                ->leftjoin('tb_work_time', 'tb_work_time.id', '=', 'tb_work_cycle.id_work_time')
                ->where('tb_work_cycle.id_work_group', $dt->id_work_group)
                ->where('tb_work_cycle.days', $modcycle)
                ->get();
            foreach ($tabel as $dt1) {
                $cshift = date('H:i', strtotime($dt1->check_in)) . ' - ' . date('H:i', strtotime($dt1->check_out));
                if ($dt1->is_advance == 0)
                    $tanggal = $Tgl;
                else
                    $tanggal = date('Y-m-d', strtotime('-1 days', strtotime($Tgl)));
                if ($dt1->id_work_time > 0) {
                    $check_in = $tanggal . ' ' . $dt1->check_in;
                    $check_out = $Tgl . ' ' . $dt1->check_out;
                } else {
                    $check_in = '';
                    $check_out = '';
                }

            }
            //Return Checkin Range
            if (isset($check_in) && $check_in != '' && $check_in != $check_out) {
                $ncdatein = $check_in;
                $ncdateindown = date('Y-m-d H:i:s', strtotime('-3 hours', strtotime($ncdatein)));
                $ncdateinup = date('Y-m-d H:i:s', strtotime('+5 hours', strtotime($ncdatein)));
            } else {
                $ncdateindown = '';
                $ncdateinup = '';
            }
            //Return Checkout Range
            if (isset($check_out) && $check_out != '' && $check_in != $check_out) {
                $ncdateout = $check_out;
                $ncdateoutdown = date('Y-m-d H:i:s', strtotime('-2 hours', strtotime($ncdateout)));
                $ncdateoutup = date('Y-m-d H:i:s', strtotime('+5 hours', strtotime($ncdateout)));
            } else {
                $ncdateoutdown = '';
                $ncdateoutup = '';
            }
            $checkin_act = '-';
            $checkout_act = '-';
            $checkin_acts = DB::table('tb_iclock')->where('badgenumber', $dt->badgenumber)->where('checktime', '>=', $ncdateindown)->where('checktime', '<=', $ncdateinup)->value('checktime');
            $checkout_acts = DB::table('tb_iclock')->where('badgenumber', $dt->badgenumber)->where('checktime', '>=', $ncdateoutdown)->where('checktime', '<=', $ncdateoutup)->value('checktime');
            if ($checkin_acts != '')
                $checkin_act = date('H:i', strtotime($checkin_acts));
            if ($checkout_acts != '')
                $checkout_act = date('H:i', strtotime($checkout_acts));
        }

        $id_shift = '';
        foreach ($tb_employee as $dt) {
            $id_shift = $dt->id_shift;
            $leader_id = $dt->leader_id;
        }
        $tb_photo = DB::table('tb_photos')->where('id_employee', '0')->get();
        foreach ($tb_photo as $dt_photo) {
            $photo = $dt_photo->nama_photo;
        }
        $tb_photo = DB::table('tb_photos')->where('id_employee', $id)->orderby('id', 'desc')->limit('1')->get();
        foreach ($tb_photo as $dt_photo) {
            $photo = $dt_photo->nama_photo;
        }
        $tb_employee_family = DB::table('tb_employee_family')->where([['id_employee', $id]])->where('status', '1')->get();
        $tb_domicile = DB::table('tb_domiciles')->where([['id_employee', $id], ['status', '1']])->get();
        $tb_address_darurat = DB::table('tb_address_darurat')->where([['id_employee', $id]])->where('status', '1')->get();
        $tb_bagian = DB::table('tb_bagians')->where([['id_employee', $id]])->orderby('implement', 'desc')->limit(1)->get();
        $tb_status = DB::table('tb_statuses')->where('id_employee', $id)->orderby('id', 'desc')->get();
        $tb_address = DB::table('tb_addresses')->where([['id_employee', $id], ['status', '1']])->get();
        $tb_education = DB::table('tb_educations')->where('id_employee', $id)->orderby('graduate_year', 'desc')->get();
        $tb_experience = DB::table('tb_experiences')->where('id_employee', $id)->orderby('finish_year', 'desc')->get();
        $tb_skill = DB::table('tb_skills')->where('id_employee', $id)->get();

        $leader_name = '';
        $tb_leader = DB::table('tb_employees')->where('id', $leader_id)->get();
        foreach ($tb_leader as $dt3) {
            $leader_name = $dt3->employee_name;
        }
        $tb_employee_leave = DB::table('tb_employee_leaves')->where([['id_employee', $id], ['extend', '>=', $Tgl], ['status', '1'], ['start', '<=', $today], ['end', '>=', $today]])->get();
        foreach ($tb_employee_leave as $dt) {
            $start = $dt->start;
            $end = $dt->end;
            $extend = $dt->extend;
            $tb_leaves = DB::table('tb_leaves')->where('id_leave', $dt->id)->orderby('start_leave', 'desc')->get();
        }
        $tb_utility = DB::table('tb_utilities')->where('atribut', 'ess_leave')->get(['status']);
        foreach ($tb_utility as $dt) {
            $ess_leave = $dt->status;
        }

        return view('page_ess/profile', ['photo' => $photo, 'tb_employee_family' => $tb_employee_family, 'tb_domicile' => $tb_domicile, 'tb_address_darurat' => $tb_address_darurat, 'tb_bagian' => $tb_bagian, 'tb_status' => $tb_status, 'id_employee' => $id, 'tb_employee' => $tb_employee, 'tb_employee_leave' => $tb_employee_leave, 'checkin_act' => $checkin_act, 'checkout_act' => $checkout_act, 'cshift' => $cshift, 'leader_id' => $leader_id, 'leader_name' => $leader_name, 'shift_code' => $shift_code, 'id_employee' => $id, 'PIN' => $pin, 'tb_address' => $tb_address, 'tb_education' => $tb_education, 'tb_experience' => $tb_experience, 'tb_skill' => $tb_skill, 'tb_leaves' => $tb_leaves, 'email' => $email, 'juduls' => 'Profile', 'menu' => 'employees', 'ess_leave' => $ess_leave]);
    }
    function employee_slip()
    {
        $email = Auth::user()->email;
        $tb_employee = DB::table('tb_emails')
            ->leftjoin('tb_employees', 'tb_employees.id', '=', 'tb_emails.id_employee')
            ->where('email_address', $email)->get();
        foreach ($tb_employee as $dt) {
            $id_employee = $dt->id_employee;
            $NIK = $dt->NIK;
            $employee_name = $dt->employee_name;
            $tb_salary_excel = DB::table('tb_salary_excel')->where('NIK', $NIK)->where('send_mail', '1')->orderby('periode', 'desc')->limit(6)->get();
        }

        $tb_utility = DB::table('tb_utilities')->where('atribut', 'ess_leave')->get(['status']);
        foreach ($tb_utility as $dt) {
            $ess_leave = $dt->status;
        }
        $tb1 = DB::table('tb_salary_summary')->where('id_employee', $id_employee)->where('mail_status', '1')->orderby('periode', 'desc')->get();
        if (request()->user()->hasRole('root') || request()->user()->hasRole('ess')) {
            return view('page_ess/slip_gaji', ['tb_salary_excel' => $tb_salary_excel, 'tb1' => $tb1, 'NIK' => $NIK, 'employee_name' => $employee_name, 'menu' => 'slip_gaji', 'juduls' => 'Slip Gaji', 'ess_leave' => $ess_leave]);
        } else {
            return abort(403, 'Anda tidak punya akses');
        }
    }
    function slip($periode, $id_employee)
    {
        $email = Auth::user()->email;
        $tb_employee = DB::table('tb_emails')
            ->where('email_address', $email)->get();
        foreach ($tb_employee as $dt) {
            $id_employee_Login = $dt->id_employee;
        }
        if ($id_employee_Login != $id_employee) {
            return "Gagal Download, Apakah ini NIK Anda....? Silahkan hubungi HR.";
        } else {
            $kategori = DB::table('tb_salary_contract')->where('id_employee', $id_employee)->value('tipe_kontrak');
            $thn = date('Y', strtotime($periode . '-01'));
            $bln = date('F', strtotime($periode . '-01'));
            $periode_text = $thn . ' ' . $bln;

            $tb_salary_summary_employee = $this->tb_salary_summary_employee($periode, $id_employee);
            $view = '';
            if ($kategori == 'SAI')
                $view = "page_ess/salary_slip_sai";
            if ($kategori == 'MAGANG')
                $view = "page_ess/salary_slip_magang";
            if ($view == '')
                return abort(403, 'Under Maintenance');
            $FileName = 'SLIP.PDF';
            if ($kategori == 'PSAB' || $kategori == 'PKL')
                $pdf = PDF::loadview($view, ['tb_salary_summary_employee' => $tb_salary_summary_employee, 'periode_text' => $periode_text, 'thn' => $thn, 'menu' => 'salary_slip'])->setPaper(array(0, 0, 280, 355));
            else
                $pdf = PDF::loadview($view, ['tb_salary_summary_employee' => $tb_salary_summary_employee, 'periode_text' => $periode_text, 'thn' => $thn, 'menu' => 'salary_slip'])->setPaper(array(0, 0, 280, 630));
            return $pdf->stream($FileName);

        }
    }
    public function tb_salary_summary_employee($periode, $id_employee)
    {
        $tabel = DB::table('tb_salary_summary')
            ->leftjoin('tb_salary_contract', 'tb_salary_contract.id_employee', '=', 'tb_salary_summary.id_employee');
        if ($periode != '0')
            $tabel = $tabel->where('periode', $periode);
        $tabel = $tabel->where('tb_salary_summary.id_employee', $id_employee)
            ->orderby('tb_salary_summary.periode', 'desc')
            ->get(['tb_salary_summary.*', 'tb_salary_contract.NIK', 'tb_salary_contract.nama_karyawan', 'tb_salary_contract.department as dept_code', 'tb_salary_contract.jabatan as nama_jabatan', 'tb_salary_contract.status_pajak', 'tb_salary_contract.total_salary', 'tb_salary_contract.tipe_kontrak', 'tb_salary_contract.upah_harian as upah_psab', 'tb_salary_contract.tunjangan_harian as tunjangan_pkl']);
        return $tabel;
    }
    function employee_slip_temp_download($periode, $NIK)
    {
        $email = Auth::user()->email;
        $tb_employee = DB::table('tb_emails')
            ->leftjoin('tb_employees', 'tb_employees.id', '=', 'tb_emails.id_employee')
            ->where('email_address', $email)->get();
        foreach ($tb_employee as $dt) {
            $NIK_Login = $dt->NIK;
        }
        if ($NIK_Login != $NIK) {
            return "Gagal Download, Apakah ini NIK Anda....? Silahkan hubungi HR.";
        } else {
            $thn = date('Y', strtotime($periode . '-01'));
            $bln = date('m', strtotime($periode . '-01'));
            $kalendar = CAL_GREGORIAN;
            $hariawal = date('Y-m-d', strtotime($thn . '-' . $bln . '-01'));
            $hariakhir = cal_days_in_month($kalendar, $bln, $thn);
            $bulan = date('F', strtotime($thn . '-' . $bln . '-01'));
            $akhirbulan = date('Y-m-d', strtotime($thn . '-' . $bln . '-' . $hariakhir));
            $periode_text = date('F Y', strtotime($thn . '-' . $bln . '-01'));

            $tb_slip = DB::table('tb_salary_excel')->where('periode', $periode)
                ->where('NIK', $NIK)
                ->get();

            $is_magang = 0;
            $tb_employee = DB::table('tb_employees')->where('NIK', $NIK)->where('position_id', 19)->where('status', '1')->get();
            foreach ($tb_employee as $dt) {
                $is_magang = 1;
            }

            $FileName = 'SLIP_GAJI ' . $periode . ' ' . $NIK . '.PDF';
            if ($is_magang == 0)
                $pdf = PDF::loadview('page_ess/slip_gaji_mail', ['tb_slip' => $tb_slip, 'thn' => $thn, 'bln' => $bln, 'periode_text' => $periode_text, 'akhirbulan' => $akhirbulan, 'periode' => $periode, 'hariawal' => $hariawal, 'NIK' => $NIK, 'menu' => 'overtimes'])->setPaper(array(0, 0, 280, 620));
            else
                $pdf = PDF::loadview('page_ess/slip_gaji_magang', ['tb_slip' => $tb_slip, 'thn' => $thn, 'bln' => $bln, 'periode_text' => $periode_text, 'akhirbulan' => $akhirbulan, 'periode' => $periode, 'hariawal' => $hariawal, 'NIK' => $NIK, 'menu' => 'overtimes'])->setPaper(array(0, 0, 280, 620));
            return $pdf->stream($FileName);
        }

        //return view('page/user/m_report/slip_overtime_mail',['tb_sum'=>$tb_sum,'tb_slip'=>$tb_slip,'thn'=>$thn,'bln'=>$bln,'judul'=>$judul,'akhirbulan'=>$akhirbulan,'periode'=>$periode,'hariawal'=>$hariawal,'id_employee'=>$id_employee,'menu'=>'overtimes']);
    }
    function employee_slip_ot()
    {
        $email = Auth::user()->email;
        $tb_employee = DB::table('tb_emails')
            ->leftjoin('tb_employees', 'tb_employees.id', '=', 'tb_emails.id_employee')
            ->leftjoin('tb_positions', 'tb_positions.id', '=', 'tb_employees.position_id')
            ->where('email_address', $email)->get();
        foreach ($tb_employee as $dt) {
            $id_employee = $dt->id_employee;
            $NIK = $dt->NIK;
            $employee_name = $dt->employee_name;
            $position_index = $dt->position_index;
        }
        $tb_summary_overtime = DB::table('tb_summary_overtime')
            ->leftjoin('tb_rapel_khusus', 'tb_rapel_khusus.id_summary', '=', 'tb_summary_overtime.id')
            ->where('send_mail', '1')
            ->where('tb_summary_overtime.id_employee', $id_employee)
            ->orderby('periode', 'desc')
            ->limit(6)
            ->get(['tb_summary_overtime.*', 'tb_rapel_khusus.rapel_gaji', 'tb_rapel_khusus.rapel_ot', 'tb_rapel_khusus.pph21_rapel', 'tb_rapel_khusus.rapel_total']);

        $tb_utility = DB::table('tb_utilities')->where('atribut', 'ess_leave')->get(['status']);
        foreach ($tb_utility as $dt) {
            $ess_leave = $dt->status;
        }
        $tb1 = DB::table('tb_ot_summary')->where('id_employee', $id_employee)->where('distribute', '1')->orderby('periode', 'desc')->get();
        if ($position_index < 3) {
            $status = '1';
        } else {
            $status = '2';
        }
        if (request()->user()->hasRole('root') || request()->user()->hasRole('ess')) {
            return view('page_ess/slip_overtime', ['tb_summary_overtime' => $tb_summary_overtime, 'tb1' => $tb1, 'NIK' => $NIK, 'employee_name' => $employee_name, 'status' => $status, 'menu' => 'slip_gaji', 'juduls' => 'Slip Overtime', 'ess_leave' => $ess_leave]);
        } else {
            return abort(403, 'Anda tidak punya akses');
        }
    }

}
#endregion
