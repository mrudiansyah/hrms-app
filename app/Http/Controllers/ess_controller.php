<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
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
    function document($id_doc){
        $tb_training_document=DB::table('tb_training_document')->where('doc_level','0')->orderby('id','desc')->get();
        $related_file=DB::table('tb_training_document')->where('id',$id_doc)->get();
        $file_name='';
        $document_name='';
        foreach($related_file as $dt){
            $file_name=$dt->file_name;
            $document_name=$dt->document_name;
        }
        if (request()->user()->hasRole('root')||request()->user()->hasRole('training')){
            return view('page_ess/training_document',['tb_training_document'=>$tb_training_document,'id_doc'=>$id_doc,'file_name'=>$file_name,'document_name'=>$document_name,'menu'=>'training_tools','juduls'=>'Training Documents']);
        //}elseif (request()->user()->hasRole('ess')){
            //return view('page/training/document',['tb_training_document'=>$tb_training_document,'site'=>$this->site,'menu'=>'training_tools','juduls'=>'Training Documents']);
        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }
    function document_upload(Request $data){
        $admin=Auth::user()->name;
        $namaFile='';
        if($data->document_name=='')return redirect()->back()->with(['success'=>'Document Name can not be null']);
        if($data->id_document==''){
            $file = $data->file('training_doc');
            if($file=='')return redirect()->back()->with(['success'=>'File can not be null']);
            $namaFile = $file->getClientOriginalName();
            $tb_training_document=DB::table('tb_training_document')->where('file_name',$namaFile)->where('doc_level','0')->count();
            if($tb_training_document>0)return redirect()->back()->with(['success'=>'File already exists']);

            $terupload = $file->move(storage_path('app/public'), $namaFile);
            if($terupload){
                $add=DB::table('tb_training_document')->insert([
                    'document_name'=>$data->document_name,
                    'file_name'=>$namaFile,
                    'doc_level'=>'0',
                    'admin'=>$admin,
                ]);
                if($add){
                    return redirect()->back()->with(['success'=>'Upload Sukses']);
                }
            }
        }else{
            $update=DB::table('tb_training_document')->where('id',$data->id_document)->update([
                'document_name'=>$data->document_name,
                'admin'=>$admin,
            ]);
            if($update)return redirect()->back()->with(['success'=>'Update Sukses']);
        }
        return redirect()->back()->with(['success'=>'There is no change']);
    }
    function delete_document(Request $data){
        $tb_training_document=DB::table('tb_training_document')->where('id',$data->id)->get();
        foreach($tb_training_document as $dt){
            $file_path = storage_path('app/public/' . $dt->file_name); 
            if(file_exists($file_path)){ 
                unlink($file_path); 
            }
        }
        $delete=DB::table('tb_training_document')->where('id',$data->id)->delete();
        if($delete){
            $hasil='Sukses';
        }
        else $hasil='Gagal';
        return $hasil;
    }
    function document_download($id){
        if (request()->user()->hasRole('ess')){
            $tb_training_document=DB::table('tb_training_document')->where('id',$id)->get();
            foreach($tb_training_document as $dt){
                $file_path = storage_path('app/public/' . $dt->file_name);
                if(file_exists($file_path)){ 
                    return Storage::download('public/'.$dt->file_name);
                }else{
                    return redirect()->back()->with(['success'=>'File is not available']);
                }
            }
        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }
    function training_invitation(){
        if (request()->user()->hasRole('ess')){
            $email=Auth::user()->email;
            $tb_employee=DB::table('tb_emails')
            ->leftjoin('tb_employees','tb_employees.id','=','tb_emails.id_employee')
            ->where('email_address',$email)->get();
            foreach($tb_employee as $dt){
                $id_employee=$dt->id_employee;
            }
            $tb_training_invitation=DB::table('tb_training_invitation')
            ->leftjoin('tb_training_schedule','tb_training_schedule.id','=','tb_training_invitation.id_training_schedule')
            ->leftjoin('tb_training_list','tb_training_list.id','=','tb_training_schedule.id_training')
            ->leftjoin('tb_skill_type','tb_skill_type.id','=','tb_training_list.id_type')
            ->leftjoin('tb_training_participant','tb_training_participant.id_training_invitation','=','tb_training_invitation.id')
            ->leftjoin('tb_related_test','tb_related_test.id_training_schedule','=','tb_training_schedule.id')
            ->where('tb_training_invitation.id_employee',$id_employee)
            //->orderby('tb_training_schedule.id','desc')->get(['tb_training_schedule.*','tb_training_list.training_name','tb_skill_type.skill_type']);
            ->orderby('tb_training_schedule.id','desc')
            ->get(['tb_training_invitation.*','tb_training_schedule.id as id_training_schedule','tb_training_schedule.id_training','tb_training_schedule.nara_sumber','tb_training_schedule.tanggal','tb_training_schedule.start','tb_training_schedule.finish','tb_training_list.training_name','tb_skill_type.skill_type','tb_training_participant.free_test','tb_training_participant.post_test','tb_training_participant.passing_grade','tb_training_participant.progress','tb_training_participant.grade_status','tb_related_test.id_test','tb_training_participant.id as idparticipant']);
            return view('page/training/training_invitation',['tb_training_invitation'=>$tb_training_invitation,'page_status'=>'','menu'=>'training_activity','juduls'=>'Training Schedule']);
        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }
    function training_schedule($id_training_invitation){
        $email=Auth::user()->email;
        $admin=Auth::user()->name;
        $time=date('Y-m-d h:i:s');
        $tanggal=date('Y-m-d');
        $start=$time;
        $finish=$time;

        $id_employee=DB::table('tb_emails')->where('email_address',$email)->value('id_employee');
        $id_training_schedule=DB::table('tb_training_invitation')->where('id_employee',$id_employee)->where('id',$id_training_invitation)->value('id_training_schedule');

        $cek_aktual = DB::table('tb_training_actual')
        ->where('id_training_schedule',$id_training_schedule)
        ->where('is_delete','0')
        ->count();
        if($cek_aktual==0){
            $tb_training_schedule=DB::table('tb_training_schedule')->where('id',$id_training_schedule)->get();
            foreach($tb_training_schedule as $dt){
                $add_actual=DB::table('tb_training_actual')->insert([
                    'id_training_schedule'=>$dt->id,
                    'nara_sumber'=>$dt->nara_sumber,
                    'tanggal_aktual'=>$tanggal,
                    'start_aktual'=>$start,
                    'finish_aktual'=>$finish,
                    'in_class'=>0,
                    'admin'=>$admin,
                    'created_at'=>$time,
                ]);
            }
        }
        $id_training_actual = DB::table('tb_training_actual')->where('id',$id_training_schedule)->where('is_delete','0')->value('id');

        $cek_participant=DB::table('tb_training_participant')
        ->where('id_training_invitation',$id_training_invitation)
        ->where('is_delete','0')
        ->count();
        if($cek_participant==0){
            $tb_training_invitation=DB::table('tb_training_invitation')
            ->where('tb_training_invitation.id',$id_training_invitation)
            ->get();
            foreach($tb_training_invitation as $dt){
                $add_participant=DB::table('tb_training_participant')->insert([
                    'id_training_actual'=>$id_training_actual,
                    'id_training_invitation'=>$dt->id,
                    'id_employee'=>$dt->id_employee,
                    'NIK'=>$dt->NIK,
                    'nama_karyawan'=>$dt->nama_karyawan,
                    'department'=>$dt->department,
                    'jabatan'=>$dt->jabatan,
                    'admin'=>$admin,
                    'created_at'=>$time
                ]);
            }
        }
        $id_training_participant=DB::table('tb_training_participant')->where('id_training_invitation',$id_training_invitation)->value('id');

        $tb_questions=DB::table('tb_question as tq')
        ->leftjoin('tb_related_test as rt','rt.id_test','=','tq.id_training_test')
        ->leftjoin('tb_training_schedule as ts','ts.id','=','rt.id_training_schedule')
        ->where('ts.id',$id_training_schedule)->get('tq.*');
        foreach($tb_questions as $dt2){
            $add_pre=DB::table('tb_free_test')->insert([
                'id_participant'=>$id_training_participant,
                'id_question'=>$dt2->id,
                'answer_code'=>$dt2->answer_code,
                'admin'=>$admin
            ]);
            $add_post=DB::table('tb_post_test')->insert([
                'id_participant'=>$id_training_participant,
                'id_question'=>$dt2->id,
                'answer_code'=>$dt2->answer_code,
                'admin'=>$admin
            ]);
        }
        return redirect('/Training/Actual/'.$id_training_actual.'/0');
        // return redirect()->back();

    }
    function training_actual($id,$id_doc){

        $tb_training_actual=DB::table('tb_training_actual')->where('id',$id)->get();
        foreach($tb_training_actual as $dt){
            $id_training_schedule=$dt->id_training_schedule;
        }
        $email=Auth::user()->email;
        $tb_employee=DB::table('tb_emails')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_emails.id_employee')
        ->where('email_address',$email)->get();
        foreach($tb_employee as $dt){
            $id_employee=$dt->id_employee;
            $pin=$dt->PIN;
        }
        
        if (request()->user()->hasRole('ess')){
            $tb_employee=DB::table('tb_training_invitation')->orderby('nama_karyawan','asc')->get();

            $tb_training_actual=DB::table('tb_training_actual')
            ->leftjoin('tb_training_schedule','tb_training_schedule.id','=','tb_training_actual.id_training_schedule')
            ->leftjoin('tb_training_list','tb_training_list.id','=','tb_training_schedule.id_training')
            ->leftjoin('tb_skill_type','tb_skill_type.id','=','tb_training_list.id_type')
            ->where('tb_training_actual.id',$id)
            ->orderby('tb_training_actual.id','desc')->get(['tb_training_actual.*','tb_training_list.training_name','tb_skill_type.skill_type','tb_training_schedule.nara_sumber']);
            //return $tb_training_actual;
            foreach($tb_training_actual as $dt){
                if($dt->nara_sumber=='')$type_training='Virtual';
                else $type_training='InClass';
            }
            $tb_training_participant=DB::table('tb_training_participant')
            ->where('id_training_actual',$id)
            ->where('id_employee',$id_employee)
            ->orderby('id','desc')->get();
            foreach($tb_training_participant as $dt){
                $id_participant=$dt->id;
                $data['pre_test']=$dt->free_test;
                $data['post_test']=$dt->post_test;
                $data['grade_status']=$dt->grade_status;
            }

            $tb_related_document=DB::table('tb_related_document')
            ->leftjoin('tb_training_document','tb_training_document.id','=','tb_related_document.id_document')
            ->leftjoin('tb_training_schedule','tb_training_schedule.id','=','tb_related_document.id_training_schedule')
            ->leftjoin('tb_training_actual','tb_training_actual.id_training_schedule','=','tb_training_schedule.id')
            ->where('tb_training_actual.id',$id)->orderby('id','desc')->get(['tb_related_document.*','tb_training_document.document_name','tb_training_document.file_name','tb_training_document.id as id_doc']);
            
            $related_file=DB::table('tb_related_document')            
            ->leftjoin('tb_training_document','tb_training_document.id','=','tb_related_document.id_document')
            ->where('id_document',$id_doc)->get();
            $file_name='';
            foreach($related_file as $dt){
                $file_name=$dt->file_name;
            }
            //return $id_doc;
            $tb_training_test=DB::table('tb_training_test')->orderby('test_name','asc')->get();
            $tb_related_test=DB::table('tb_related_test')
            ->leftjoin('tb_training_test','tb_training_test.id','=','tb_related_test.id_test')
            ->where('id_training_schedule',$id_training_schedule)->get(['tb_related_test.*','tb_training_test.test_name','tb_training_test.minutes','tb_training_test.passing_grade']);
            
            return view('page/training/training_participant',['tb_training_participant'=>$tb_training_participant,'data'=>$data,'tb_training_actual'=>$tb_training_actual,'tb_employee'=>$tb_employee,'tb_training_test'=>$tb_training_test,'tb_related_document'=>$tb_related_document,'tb_related_test'=>$tb_related_test,'id_training'=>$id,'id_participant'=>$id_participant,'id_doc'=>$id_doc,'file_name'=>$file_name,'type_training'=>$type_training,'menu'=>'training_activity','juduls'=>'Training Schedule']);
        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }
    function free_test($id_participant){
        $email=Auth::user()->email;
        $tb_employee=DB::table('tb_emails')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_emails.id_employee')
        ->where('email_address',$email)->get();
        foreach($tb_employee as $dt){
            $id_employee=$dt->id_employee;
        }
        $tb_training_participant=DB::table('tb_training_participant')->where('id',$id_participant)->get();
        foreach($tb_training_participant as $dt2){
            $id_employee2=$dt2->id_employee;
        }
        if($id_employee==$id_employee2){
            $tb_training_test=DB::table('tb_training_test')
            ->leftjoin('tb_related_test','tb_training_test.id','=','tb_related_test.id_test')
            ->leftjoin('tb_training_schedule','tb_training_schedule.id','=','tb_related_test.id_training_schedule')
            ->leftjoin('tb_training_actual','tb_training_actual.id_training_schedule','=','tb_training_schedule.id')
            ->leftjoin('tb_training_participant','tb_training_actual.id','=','tb_training_participant.id_training_actual')
            ->where('tb_training_participant.id',$id_participant)
            ->get(['tb_training_test.*','tb_training_actual.tanggal_aktual','tb_training_participant.free_test','tb_training_participant.id as id_participant','tb_training_participant.id_training_actual']);
            foreach($tb_training_test as $dt){
                $id_test=$dt->id;
                $id_training_actual=$dt->id_training_actual;
            }
            //return $id_participant;
            $tb_question=DB::table('tb_question')
            ->leftjoin('tb_free_test','tb_free_test.id_question','=','tb_question.id')
            ->leftjoin('tb_training_participant','tb_training_participant.id','=','tb_free_test.id_participant')
            ->where('id_training_test',$id_test)
            ->where('tb_training_participant.id',$id_participant)
            ->orderby('index_question','asc')->get(['tb_question.*','tb_free_test.answer_actual','tb_free_test.answer_status']);
            foreach($tb_training_test as $dt){
                $test_name=$dt->test_name;
                $id_participant=$dt->id_participant;
                $passing_grade=$dt->passing_grade;
            }
            //return $id_participant;
            return view('page/training/training_free_test',['tb_training_test'=>$tb_training_test,'test_name'=>$test_name,'tb_question'=>$tb_question,'id_test'=>$id_test,'id_participant'=>$id_participant,'passing_grade'=>$passing_grade,'id_training_actual'=>$id_training_actual,'menu'=>'training_tools','juduls'=>'Training Documents']);
        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }
    function simpan_free_test(request $data){
        $hasil="There is no change";
        date_default_timezone_set("Asia/Jakarta");
        $now=date('Y-m-d H:i:s');
        $tb_test=DB::table('tb_free_test')->where('id_participant',$data->idparticipant)->where('id_question',$data->idquestion)->get();
        #region NEW

        $id_participant = $data->idparticipant;
        $check_telat = DB::table('tb_training_actual as a')
        ->leftJoin('tb_training_participant as b','b.id_training_actual','a.id')
        ->where('b.id',$id_participant)->select('a.tanggal_aktual','a.finish_aktual')->get();
        foreach($check_telat as $r){
            $finish_date = $r->tanggal_aktual;
            $finish_time = $r->finish_aktual; 
        }
        $finish = date($finish_date.' '.$finish_time);
        #endregion
        // dd($now.'     '.$finish);
        foreach($tb_test as $dt){
            $answer_code=$dt->answer_code;
            if($answer_code==$data->isi)$status='1';
            else $status='0';
            $now > $finish ? $telat ='1' : $telat = '0';
            $update=DB::table('tb_free_test')->where('id_participant',$data->idparticipant)->where('id_question',$data->idquestion)->update([
                'answer_actual'=>$data->isi,
                'answer_status'=>$status,
                'answer_late' => $telat,
                'created_at'=>$now
            ]);
            if($update)$hasil="Sukses";
        }
        
        $id_test=$data->idtest;
        $passing_grade=$data->passinggrade;
        $id_participant=$data->idparticipant;

        $email=Auth::user()->email;
        $tb_employee=DB::connection('mysql')->table('tb_emails')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_emails.id_employee')
        ->where('email_address',$email)->get();
        foreach($tb_employee as $dt){
            $id_employee=$dt->id_employee;
        }
        $tb_question=DB::table('tb_question')
        ->leftjoin('tb_free_test','tb_free_test.id_question','=','tb_question.id')
        ->leftjoin('tb_training_participant','tb_training_participant.id','=','tb_free_test.id_participant')
        ->where('id_training_test',$id_test)
        ->where('tb_training_participant.id',$id_participant)
        // ->where('tb_free_test.answer_late',0)
        ->orderby('index_question','asc')->get(['tb_question.*','tb_free_test.answer_actual','tb_free_test.answer_status','tb_free_test.answer_late']);
        $no=1;
        $total=0;
        $benar=0;
        $percent='';
        $status_grade=0;
        $konten='';
        // $tokens = Sesion
        foreach($tb_question as $dt){
            $no++;
            $total++;
            $konten.="<div class='col-md-12 col-lg-12 col-xs-12'>";
                $konten.="<div class='box box-default'>";
                    $konten.="<div class='box-header with-border'>";
                        $konten.="<h3 class='box-title'>";
                            $konten.="<b class='label label-info'>".$dt->index_question."</b>";
                        $konten.="</h3>";
                        $konten.="<div class='pull-right'>";	

                        if($dt->answer_code==$dt->answer_actual && $dt->answer_late == 0){
                            $benar++;
                            //$konten.="<i class='fa fa-thumbs-o-up'></i> ";
                            // $konten.="<b class='badge bg-green'>".$dt->answer_actual."</b>";
                        }elseif($dt->answer_code!=$dt->answer_actual&&$dt->answer_actual!=''){
                            // $konten.="<b class='badge bg-red'>".$dt->answer_actual."</b>";
                        }
                        $percent=$benar*100/$total;
                        if($percent>=$passing_grade)$status_grade=1;
                        else $status_grade=0;
                        $konten.="</div>";
                    $konten.="</div>";
                    $konten.="<div class='box-body row'>";
                        $konten.=$dt->question;
                            $konten.="<div class='form-group'>";
                                $konten.="<div class='radio pilihan' data-idquestion='".$dt->id."' data-isi='A'><label><input type='radio' name='pilihan' id='".$dt->index_question."#".$id_participant."' value='A'";if($dt->answer_actual=='A')$konten.=' checked';$konten.=">".$dt->option_a."</label></div>";
                                $konten.="<div class='radio pilihan' data-idquestion='".$dt->id."' data-isi='B'><label><input type='radio' name='pilihan' id='".$dt->index_question."#".$id_participant."' value='B'";if($dt->answer_actual=='B')$konten.=' checked';$konten.=">".$dt->option_b."</label></div>";
                                $konten.="<div class='radio pilihan' data-idquestion='".$dt->id."' data-isi='C'><label><input type='radio' name='pilihan' id='".$dt->index_question."#".$id_participant."' value='C'";if($dt->answer_actual=='C')$konten.=' checked';$konten.=">".$dt->option_c."</label></div>";
                                $konten.="<div class='radio pilihan' data-idquestion='".$dt->id."' data-isi='D'><label><input type='radio' name='pilihan' id='".$dt->index_question."#".$id_participant."' value='D'";if($dt->answer_actual=='D')$konten.=' checked';$konten.=">".$dt->option_d."</label></div>";
                            $konten.="</div>";
                    $konten.="</div>";
                $konten.="</div>";
            $konten.="</div>";
        }
        $konten.="<input type='hidden' value='".$percent."' id='percent'>";
        $konten.="<input type='hidden' value='".$status_grade."' id='statusgrade'>";
        $progress = 2;
        
         DB::table('tb_training_participant')->where('id',$data->idparticipant)->update([
            'free_test'=>$percent,
            'progress'=>$progress,
            'passing_grade'=>$passing_grade,
            'grade_status'=>$status_grade
        ]);
        return $konten;
    }
    function post_test($id_participant){
        $email=Auth::user()->email;
        $tb_employee=DB::connection('mysql')->table('tb_emails')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_emails.id_employee')
        ->where('email_address',$email)->get();
        foreach($tb_employee as $dt){
            $id_employee=$dt->id_employee;
        }
        $tb_training_participant=DB::connection('training')->table('tb_training_participant')->where('id',$id_participant)->get();
        //return $tb_training_participant;
        foreach($tb_training_participant as $dt2){
            $id_employee2=$dt2->id_employee;
        }
        if($id_employee==$id_employee2){
            $tb_training_test=DB::connection('training')->table('tb_training_test')
            ->leftjoin('tb_related_test','tb_training_test.id','=','tb_related_test.id_test')
            ->leftjoin('tb_training_schedule','tb_training_schedule.id','=','tb_related_test.id_training_schedule')
            ->leftjoin('tb_training_actual','tb_training_actual.id_training_schedule','=','tb_training_schedule.id')
            ->leftjoin('tb_training_participant','tb_training_actual.id','=','tb_training_participant.id_training_actual')
            ->where('tb_training_participant.id',$id_participant)
            ->get(['tb_training_test.*','tb_training_actual.tanggal_aktual','tb_training_participant.post_test','tb_training_participant.id as id_participant','tb_training_participant.id_training_actual']);
            foreach($tb_training_test as $dt){
                $id_test=$dt->id;
                $id_training_actual=$dt->id_training_actual;
            }
            $tb_question=DB::connection('training')->table('tb_question')
            ->leftjoin('tb_post_test','tb_post_test.id_question','=','tb_question.id')
            ->leftjoin('tb_training_participant','tb_training_participant.id','=','tb_post_test.id_participant')
            ->where('id_training_test',$id_test)
            ->where('tb_training_participant.id',$id_participant)
            ->orderby('index_question','asc')->get(['tb_question.*','tb_post_test.answer_actual','tb_post_test.answer_status']);
            foreach($tb_training_test as $dt){
                $test_name=$dt->test_name;
                $id_participant=$dt->id_participant;
                $passing_grade=$dt->passing_grade;
            }
            //return $tb_question;
            return view('page/training/training_post_test',['tb_training_test'=>$tb_training_test,'test_name'=>$test_name,'tb_question'=>$tb_question,'id_test'=>$id_test,'id_participant'=>$id_participant,'passing_grade'=>$passing_grade,'id_training_actual'=>$id_training_actual,'site'=>$this->site,'menu'=>'training_tools','juduls'=>'Training Documents']);
        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }
    function simpan_post_test(request $data){
        $hasil="There is no change";
        date_default_timezone_set("Asia/Jakarta");
        $now=date('Y-m-d H:i:s');
        #region NEW
        $id_participant = $data->idparticipant;
        $check_telat = DB::connection('training')->table('tb_training_actual as a')
        ->leftJoin('tb_training_participant as b','b.id_training_actual','a.id')
        ->where('b.id',$id_participant)->select('a.tanggal_aktual','a.finish_aktual')->get();
        foreach($check_telat as $r){
            $finish_date = $r->tanggal_aktual;
            $finish_time = $r->finish_aktual; 
        }
        $finish = date($finish_date.' '.$finish_time);
        #endregion
        $tb_test=DB::connection('training')->table('tb_post_test')->where('id_participant',$data->idparticipant)->where('id_question',$data->idquestion)->get();
        foreach($tb_test as $dt){
            $answer_code=$dt->answer_code;
            if($answer_code==$data->isi)$status='1';
            else $status='0';
            $now > $finish ? $telat ='1' : $telat = '0';

            $update=DB::connection('training')->table('tb_post_test')->where('id_participant',$data->idparticipant)->where('id_question',$data->idquestion)->update([
                'answer_actual'=>$data->isi,
                'answer_status'=>$status,
                'answer_late' => $telat,
                'created_at'=>$now
            ]);
            if($update)$hasil="Sukses";
        }
        
        $id_test=$data->idtest;
        $passing_grade=$data->passinggrade;
        $id_participant=$data->idparticipant;

        $email=Auth::user()->email;
        $tb_employee=DB::connection('mysql')->table('tb_emails')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_emails.id_employee')
        ->where('email_address',$email)->get();
        foreach($tb_employee as $dt){
            $id_employee=$dt->id_employee;
        }
        $tb_question=DB::connection('training')->table('tb_question')
        ->leftjoin('tb_post_test','tb_post_test.id_question','=','tb_question.id')
        ->leftjoin('tb_training_participant','tb_training_participant.id','=','tb_post_test.id_participant')
        ->where('id_training_test',$id_test)
        ->where('tb_training_participant.id',$id_participant)
        ->orderby('index_question','asc')->get(['tb_question.*','tb_post_test.answer_actual','tb_post_test.answer_status']);
        $no=1;
        $total=0;
        $benar=0;
        $percent='';
        $status_grade=0;
        $konten='';
        foreach($tb_question as $dt){
            $no++;
            $total++;
            $konten.= "<div class='col-md-12 col-lg-12 col-xs-12'>";
                $konten.="<div class='box box-default'>";
                    $konten.="<div class='box-header with-border'>";
                        $konten.="<h3 class='box-title'>";
                            $konten.="<b class='label label-info'>".$dt->index_question."</b>";
                        $konten.="</h3>";
                        $konten.=$dt->question;
                        $konten.="<div class='pull-right'>";	

                        if($dt->answer_code==$dt->answer_actual){
                            $benar++;
                            //$konten.="<i class='fa fa-thumbs-o-up'></i> ";
                            // $konten.="<b class='badge bg-green'>".$dt->answer_actual."</b>";
                        }elseif($dt->answer_code!=$dt->answer_actual&&$dt->answer_actual!=''){
                            // $konten.="<b class='badge bg-red'>".$dt->answer_actual."</b>";
                        }
                        $percent=$benar*100/$total;
                        if($percent>=$passing_grade)$status_grade=1;
                        else $status_grade=0;
                        $konten.="</div>";
                    $konten.="</div>";
                    $konten.="<div class='box-body'>";
                        $konten.="<div class='form-group col-lg-6'>";
                        $konten.="<b>A</b> <div class='radio pilihan' data-idquestion='".$dt->id."' data-isi='A'><label><input type='radio' name='pilihan".$dt->index_question."' id='".$dt->index_question."#".$id_participant."' value='A'";if($dt->answer_actual=='A')$konten.=' checked';$konten.=">".$dt->option_a."</label></div></div>";
                        $konten.=" <div class='form-group col-lg-6'><b>B</b><div class='radio pilihan' data-idquestion='".$dt->id."' data-isi='B'><label class=''><input type='radio' name='pilihan".$dt->index_question."' id='".$dt->index_question."#".$id_participant."' value='B'";if($dt->answer_actual=='B')$konten.= ' checked';$konten.= ">".$dt->option_b."</label></div></div>";
                        $konten.="<div class='form-group col-lg-6'><b>C</b><div class='radio pilihan' data-idquestion='".$dt->id."' data-isi='C'><label><input type='radio' name='pilihan".$dt->index_question."' id='".$dt->index_question."#".$id_participant."' value='C'";if($dt->answer_actual=='C')$konten.=' checked';$konten.=">".$dt->option_c."</label></div></div>";
                        $konten.="<div class='form-group col-lg-6'><b>D</b><div class='radio pilihan' data-idquestion='".$dt->id."' data-isi='D'><label><input type='radio' name='pilihan".$dt->index_question."' id='".$dt->index_question."#".$id_participant."' value='D'";if($dt->answer_actual=='D')$konten.=' checked';$konten.=">".$dt->option_d."</label></div></div>";
                    $konten.="</div>";
                $konten.="</div>";
            $konten.="</div>";
        }
        $konten.="<input type='hidden' value='".$percent."' id='percent'>";
        $konten.="<input type='hidden' value='".$status_grade."' id='statusgrade'>";

        $tb_participant=DB::connection('training')->table('tb_training_participant')->where('id',$data->idparticipant)->get();
        foreach($tb_participant as $dt){
            if($percent < $dt->free_test){
                $progress='-1';
            }
            elseif($dt->free_test==$percent){$progress='0';}
            else {$progress='1';}
        }
        $update_participant=DB::connection('training')->table('tb_training_participant')->where('id',$data->idparticipant)->update([
            'post_test'=>$percent,
            'passing_grade'=>$passing_grade,
            'progress'=>$progress,
            'grade_status'=>$status_grade
        ]);

        return $konten;
    }

}
#endregion
