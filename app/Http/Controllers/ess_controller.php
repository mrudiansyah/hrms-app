<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use DateTime;
use Auth;

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
}
#endregion
