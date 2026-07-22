<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DateTime;
use Auth;
use PDF;

use Illuminate\Support\Facades\Mail;
use App\Mail\ksk_approved;
use Illuminate\Support\Facades\Log;


class employee_controller extends Controller
{
    function __construct()
    {
        $this->middleware(['auth','verified']);
    }
    function index($status){
        $nama=Auth::user()->name;
        $email=Auth::user()->email;
        $cek1=DB::table('tb_emails')->where('email_address',$email)->get();
        foreach($cek1 as $dt){$id_user=$dt->id_employee;}
        $tb_admins=DB::table('tb_admins')->where('id_employee',$id_user)->get();
    
        //if($status==0)$status='Magang';
        //$tb_employee=DB::table('tb_employees')->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')->leftjoin('tb_employee_shifts','tb_employee_shifts.id_employee','=','tb_employees.id')->where([['tb_employee_shifts.status','1'],['tb_employees.status','1']])->get(['tb_employees.*','tb_departments.dept_code','tb_positions.position_name','tb_employee_shifts.id_shift']);
        $tb_employee=DB::table('tb_employees')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->leftjoin('tb_employee_shifts','tb_employee_shifts.id_employee','=','tb_employees.id')
        ->leftjoin('tb_group_shifts','tb_group_shifts.id','=','tb_employee_shifts.id_shift')
        ->leftjoin('tb_statuses', function ($join) {
            $join->on('tb_statuses.id_employee', '=', 'tb_employees.id')
                  ->where('tb_statuses.active', '1');
        });
        if($status!=0){
            $tb_employee=$tb_employee->where([['tb_employees.status','1'],['dept_id','0'],['tb_employee_shifts.status','1'],['tb_statuses.contract_name', $status]]);
            foreach($tb_admins as $dt2){
                $tb_employee=$tb_employee->orwhere([['tb_employees.status','1'],['dept_id',$dt2->dept_id],['tb_employee_shifts.status','1'],['tb_statuses.contract_name', $status]]);
            }
        }
        else{
            $tb_employee=$tb_employee->where([['tb_employees.status','1'],['dept_id','0'],['tb_employee_shifts.status','1']]);
            foreach($tb_admins as $dt2){
                $tb_employee=$tb_employee->orwhere([['tb_employees.status','1'],['dept_id',$dt2->dept_id],['tb_employee_shifts.status','1']]);
            }
        }
        $tb_employee=$tb_employee->orderby('tb_statuses.finish_contract','asc')->get(['tb_employees.*','tb_departments.dept_code','tb_positions.position_name','tb_employee_shifts.id_shift','tb_group_shifts.shift_code','tb_statuses.start_contract','tb_statuses.finish_contract']);
        //return $tb_employee;
        $last_update=tb_employee::max('updated_at');
        return view('page/user/m_employee/employees',['menu'=>'employees','tb_employee'=>$tb_employee,'status'=>$status,'menu'=>'employees']);
    }
    function employee($id,$pin)
    {
        $badgenumber = $pin;
        date_default_timezone_set("Asia/Jakarta");
        $Tgl = date('Y-m-d');
        $today=$Tgl;

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

    return view('page/user/m_employee/profile', ['photo' => $photo, 'tb_employee_family' => $tb_employee_family, 'tb_domicile' => $tb_domicile, 'tb_address_darurat' => $tb_address_darurat, 'tb_bagian' => $tb_bagian, 'tb_status' => $tb_status, 'id_employee' => $id, 'tb_employee' => $tb_employee, 'tb_employee_leave' => $tb_employee_leave, 'checkin_act' => $checkin_act, 'checkout_act' => $checkout_act, 'cshift' => $cshift, 'leader_id' => $leader_id, 'leader_name' => $leader_name, 'shift_code' => $shift_code, 'id_employee' => $id, 'PIN' => $pin, 'tb_address' => $tb_address, 'tb_education' => $tb_education, 'tb_experience' => $tb_experience, 'tb_skill' => $tb_skill, 'tb_leaves' => $tb_leaves, 'email' => '', 'juduls' => 'Profile', 'menu' => 'employees', 'ess_leave' => $ess_leave]);
    }
    function checktime($id,$badgenumber,$start,$end){
        date_default_timezone_set("Asia/Jakarta");
        $kalendar=CAL_GREGORIAN;
        if($start==0){
            $periode=date('Y-m');
        }else{
            $periode=date('Y-m',strtotime($start));
        }
        $thn=date('Y');
        $bln=date('m');
        $hariakhir=cal_days_in_month($kalendar,$bln,$thn);
        $start=date('Y-m-d',strtotime($periode.'-01'));
        $end=date('Y-m-d',strtotime($periode.'-'.$hariakhir));
        
        $tb_group_shift=DB::table('tb_group_shifts')
        ->leftjoin('tb_groups','tb_groups.group','=','tb_group_shifts.group')
        ->get(['tb_group_shifts.*','tb_groups.cycle','tb_groups.nshift','tb_groups.ngroup']);
        $tb_employee=tb_employee::where('id',$id)->get();
        foreach($tb_employee as $dt){
            $employee_name=$dt->employee_name;
        }
        return view('page/user/m_employee/employee_checktime_all',['tb_group_shift'=>$tb_group_shift,'id_employee'=>$id,'badgenumber'=>$badgenumber,'periode'=>$periode,'start'=>$start,'end'=>$end,'employee_name'=>$employee_name,'menu'=>'employees']);
    }
    function overtime($id,$pin,$start,$end){
        $tb_employee=tb_employee::where('id',$id)->get();
        foreach($tb_employee as $dt){
            $employee_name=$dt->employee_name;
        }
        $tb_overtime=tb_overtime::where([['id_employee',$id],['approval','1']])->get();
        foreach($tb_overtime as $dt){
            $id_overtime=$dt->id;
            $host = mysqli_connect("192.168.121.4:83306","cahyudin","123456","adms_db");

            $checkin_act='';
            $checkin_status='0';
            $checkout_act='';
            $checkout_status='0';

            //Return Checkin Range
            if(isset($dt->checkin_plan)){
                $ncdatein=$dt->checkin_plan;
                //Reduce 2 Hour
                $date = date_create($ncdatein);
                date_add($date, date_interval_create_from_date_string('-2 hours'));
                $ncdateindown= date_format($date, 'Y-m-d H:i:s');
                //Increas 5 Hour
                $date = date_create($ncdatein);
                date_add($date, date_interval_create_from_date_string('30 minutes'));
                $ncdateinup= date_format($date, 'Y-m-d H:i:s');
                //echo $ncdatein.' ';

                $qry2=mysqli_query($host,"select checktime from checkinout where userid='$pin'and checktime>='$ncdateindown' and checktime<='$ncdateinup' order by checktime asc")or die(mysqli_error($host));
                while($dt2=mysqli_fetch_array($qry2)){
                    $checkin_act=$dt2['checktime'];
                    $checkin_status='1';
                }
            }
            //Return Checkout Range
            if(isset($dt->checkout_plan)){
                $ncdateout=$dt->checkout_plan;
                //Reduce 2 Hour
                $date = date_create($ncdateout);
                date_add($date, date_interval_create_from_date_string('-30 minutes'));
                $ncdateoutdown= date_format($date, 'Y-m-d H:i:s');
                //Increas 5 Hour
                $date = date_create($ncdateout);
                date_add($date, date_interval_create_from_date_string('4 hours 30 minutes'));
                $ncdateoutup= date_format($date, 'Y-m-d H:i:s');
                //echo $ncdateout.' ';

                $qry2=mysqli_query($host,"select checktime from checkinout where userid='$pin' and checktime>='$ncdateoutdown' and checktime<='$ncdateoutup' order by checktime desc")or die(mysqli_error($host));
                while($dt2=mysqli_fetch_array($qry2)){
                    $checkout_act=$dt2['checktime'];
                    $checkout_status='1';
                }
            }

            if($checkin_status='1'&&$checkout_status='1'){
                $status='1';
                $hours_act=$dt->hours_plan;
            }else {
                $status='0';
                $hours_act='0';
            }
            
            $update_ot=tb_overtime::where('id',$id_overtime)->update([
                'checkin_act'=>$checkin_act,
                'checkout_act'=>$checkout_act,
                'hours_act'=>$hours_act,
                'status'=>$status
            ]);
            //echo $ncdateoutup.'<br>';
        }
        if($start!='0'){
            $tb_overtime=tb_overtime::where([['id_employee',$id],['date_on','>=',$start],['date_on','<=',$end]])->get();
        }
        else{
            $tb_overtime=tb_overtime::where([['id_employee',$id]])->get();
            $start=tb_overtime::where([['id_employee',$id]])->min('date_on');
            $end=tb_overtime::where([['id_employee',$id]])->max('date_on');
            if($start==''){
                $start=0;
                $end=0;
            }
        }
        return view('page/user/m_employee/employee_overtime',['id_employee'=>$id,'PIN'=>$pin,'start'=>$start,'end'=>$end,'employee_name'=>$employee_name,'tb_overtime'=>$tb_overtime,'menu'=>'employees']);
    }
    function changeday($id,$pin,$start,$end){
        $tb_employee=tb_employee::where('id',$id)->get();
        foreach($tb_employee as $dt){
            $employee_name=$dt->employee_name;
        }
        if($start!='0'){
            $tb_changeday=tb_changeday::where([['id_employee',$id],['date_on','>=',$start],['date_on','<=',$end]])->get();
        }
        else{
            $tb_changeday=tb_changeday::where('id_employee',$id)->get();
            $start=tb_changeday::where([['id_employee',$id]])->min('date_on');
            $end=tb_changeday::where([['id_employee',$id]])->max('date_on');
            if($start==''){
                $start=0;
                $end=0;
            }
        }
        return view('page/user/m_employee/employee_changeday',['tb_changeday'=>$tb_changeday,'id_employee'=>$id,'PIN'=>$pin,'start'=>$start,'end'=>$end,'employee_name'=>$employee_name,'menu'=>'employees']);
        
    }
    function presenceUpdate($id,$pin){
        date_default_timezone_set("Asia/Bangkok");
        $kalendar=CAL_GREGORIAN;
        $host = mysqli_connect("192.168.121.4:83306","cahyudin","123456","adms_db");

        $tb_employee_shift=DB::table('tb_employee_shifts')->leftjoin('tb_employees','tb_employees.id','=','tb_employee_shifts.id_employee')->where('tb_employees.id',$id)->get(['tb_employee_shifts.*','tb_employees.PIN']);
        //$tb_employee_shift=tb_employee_shift::all();
        foreach($tb_employee_shift as $row){

            $userid=$row->id_employee;
            $id_shift=$row->id_shift;
            $PIN=$row->PIN;
            //echo '<br>#1 '.$userid.': ';

            //New Code
            $Today=date('Y-m-d');
            $BYesterday=date('Y-m-d',strtotime('-2 days',strtotime($Today)));
            $thn_max=date('Y',strtotime($Today));
            $thn_min=date('Y',strtotime($BYesterday));
            $bln_max=date('m',strtotime($Today));
            $bln_min=date('m',strtotime($BYesterday));

            //#1
            $thn=$thn_max;
            $bln=$bln_max;

                $hariakhir=cal_days_in_month($kalendar,$bln,$thn);
                $Tglawal=date('Y-m-d',strtotime($thn.'-'.$bln.'-01'));
                $Tglakhir=date('Y-m-d',strtotime($thn.'-'.$bln.'-'.$hariakhir));

                $i=0;
                $present=0;
                $presence=0;
                $offDays='';
                $delay_fr='0';
                $delay_minutes='0';

                for($day=$hariakhir;$day>=1;$day--){
                    $Tgl=date('Y-m-d',strtotime($thn.'-'.$bln.'-'.$day));
                    $Today=date('Y-m-d');
                    $Months=date('Y-m',strtotime($thn.'-'.$bln.'-'.$day));
                    if($Tgl<=$Today){

                    //insert Old Code

                        //echo '<br>&nbsp;&nbsp;#2 '.$Tgl.' ';

                        $tb_group_shift=tb_group_shift::where('id',$id_shift)->get();
                        foreach($tb_group_shift as $dt){
                            $tgl1 = new DateTime($dt->start_implement);
                            $tgl2 = new DateTime($Tgl);
                            $diffdays = $tgl2->diff($tgl1)->days;
                            $tb_group=tb_group::where('group',$dt->group)->get();
                            foreach($tb_group as $dt2){
                                $cycle=$dt2->cycle;
                            }
                            $diffcycle=Floor($diffdays/$cycle);
                            $modcycle=$diffdays%$cycle;
                            $modcycle++;
                            //echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;#3 '.$diffcycle.'-'.$modcycle.' ';
                            $tb_cycle=tb_cycle::where([['group',$dt->group],['days',$modcycle],['check_in','<>','check_out']])->get();
                            foreach($tb_cycle as $dt3){

                                $shift=$dt3->shift;
                                $check_in=$dt3->check_in;
                                $check_out=$dt3->check_out;
                                $advance=$dt3->advance;
                                $cross=$dt3->cross;
                
                                //Return Checkin Range
                                if(isset($check_in)&&$check_in!=''){
                                    $cdate=date('Y-m-d',strtotime($Tgl));
                                    if($advance==1){
                                        $date = date_create($cdate);
                                        date_add($date, date_interval_create_from_date_string('-1 days'));
                                        $cdate= date_format($date, 'Y-m-d');
                                    }
                                    $ncdatein=$cdate." ".$check_in;
                                    //Reduce 2 Hour
                                    $date = date_create($ncdatein);
                                    date_add($date, date_interval_create_from_date_string('-2 hours'));
                                    $ncdateindown= date_format($date, 'Y-m-d H:i:s');
                                    //Increas 5 Hour
                                    $date = date_create($ncdatein);
                                    date_add($date, date_interval_create_from_date_string('5 hours'));
                                    $ncdateinup= date_format($date, 'Y-m-d H:i:s');
                                    //echo $ncdatein.' ';
                                }
                                //Return Checkout Range
                                if(isset($check_out)&&$check_out!=''){
                                    $cdate=date('Y-m-d',strtotime($Tgl));
                                    if($cross==1){
                                        $date = date_create($cdate);
                                        date_add($date, date_interval_create_from_date_string('+1 days'));
                                        $cdate= date_format($date, 'Y-m-d');
                                    }
                                    $ncdateout=$cdate." ".$check_out;
                                    //Reduce 2 Hour
                                    $date = date_create($ncdateout);
                                    date_add($date, date_interval_create_from_date_string('-2 hours'));
                                    $ncdateoutdown= date_format($date, 'Y-m-d H:i:s');
                                    //Increas 5 Hour
                                    $date = date_create($ncdateout);
                                    date_add($date, date_interval_create_from_date_string('4 hours 30 minutes'));
                                    $ncdateoutup= date_format($date, 'Y-m-d H:i:s');
                                    //echo $ncdateout.' ';
                                }

                                $in_status='0';
                                $out_status='0';
                                $off_status='0';
                                $qry2=mysqli_query($host,"select checkinout.checktime,checkinout.userid,userinfo.name from checkinout left join userinfo on checkinout.userid=userinfo.userid where userinfo.userid='$PIN' and checkinout.checktime>='$ncdateindown' and checkinout.checktime<='$ncdateoutup' order by checkinout.checktime asc")or die(mysqli_error($host));
                                while($dt2=mysqli_fetch_array($qry2)){
                                    $ctime=$dt2['checktime'];
                                    //Conditionl IN-OUT
                                    if($ctime>=$ncdateindown and $ctime<=$ncdateinup){
                                        $in_status="1";
                                        $tgl101 = strtotime($check_in);
                                        $tgl201 = strtotime($ctime);
                                        if($tgl201>$tgl101){
                                            $delay_minutes=$delay_minutes+floor(($tgl201-$tgl101)/60);
                                            $delay_fr++;
                                        }
                                        //$sample = $tgl201-$tgl101;
                                        //echo $delay_fr.' '.$delay_minutes.'<br>';
                                    }
                                    if($ctime>=$ncdateoutdown and $ctime<=$ncdateoutup)$out_status="1";
                                }

                                $tb_freeday=tb_employee_freeday::where([['id_employee',$userid],['date_off',$Tgl]])->count();
                                $tb_changeday=tb_changeday::where([['id_employee',$userid],['date_off',$Tgl]])->count();
                                $i++;
                                if($tb_freeday>0||$tb_changeday>0){
                                    $i--;
                                    $off_status='1';
                                }

                                if($in_status=='1'||$out_status=='1')$present++;
                                else{
                                    if(strlen($day)==1)$dj='0'.$day;
                                    else $dj=$day;
                                    if($off_status=='0'){
                                        $offDays=$offDays.$dj.' ';
                                    }
                                }

                            }
                        }
                    

                        if($i==0)$presence=0;
                        else
                        $presence=number_format($present/$i*100,'2');
                        $absent=100-$presence;

                        $remark=0;

                        $check_presence=tb_presence::where([['month',$Months],['employee_id',$userid]])->count();
                        if($userid<>''){
                            if($check_presence==0){
                                tb_presence::create([
                                    'month'=>$Months,
                                    'start'=>$Tglawal,
                                    'end'=>$Tglakhir,
                                    'employee_id'=>$userid,
                                    'plan'=>$i,
                                    'actual'=>$present,
                                    'presence'=>$presence,
                                    'delay_fr'=>$delay_fr,
                                    'delay_minutes'=>$delay_minutes,
                                    'status'=>$remark,
                                    'absent' => $offDays
                                ]);
                        
                            }else{
                                tb_presence::where([['month',$Months],['employee_id',$userid]])->update([
                                    'plan'=>$i,
                                    'actual'=>$present,
                                    'presence'=>$presence,
                                    'delay_fr'=>$delay_fr,
                                    'delay_minutes'=>$delay_minutes,
                                    'status'=>$remark,
                                    'absent' => $offDays
                                ]);
                            }
                        }


                    //End Insert Old Code

                    }
                }
            //End #1
            //\Log::info('Presence #1'.$userid.' '.$thn.'-'.$bln.' Updated');

            if($bln_max!=$bln_min){

                //#2
                $thn=$thn_min;
                $bln=$bln_min;

                $hariakhir=cal_days_in_month($kalendar,$bln,$thn);
                $Tglawal=date('Y-m-d',strtotime($thn.'-'.$bln.'-01'));
                $Tglakhir=date('Y-m-d',strtotime($thn.'-'.$bln.'-'.$hariakhir));

                $i=0;
                $present=0;
                $presence=0;
                $offDays='';
                $delay_fr='0';
                $delay_minutes='0';

                for($day=$hariakhir;$day>=1;$day--){
                    $Tgl=date('Y-m-d',strtotime($thn.'-'.$bln.'-'.$day));
                    $Today=date('Y-m-d');
                    $Months=date('Y-m',strtotime($thn.'-'.$bln.'-'.$day));
                    if($Tgl<=$Today){

                    //insert Old Code

                        //echo '<br>&nbsp;&nbsp;#2 '.$Tgl.' ';

                        $tb_group_shift=tb_group_shift::where('id',$id_shift)->get();
                        foreach($tb_group_shift as $dt){
                            $tgl1 = new DateTime($dt->start_implement);
                            $tgl2 = new DateTime($Tgl);
                            $diffdays = $tgl2->diff($tgl1)->days;
                            $tb_group=tb_group::where('group',$dt->group)->get();
                            foreach($tb_group as $dt2){
                                $cycle=$dt2->cycle;
                            }
                            $diffcycle=Floor($diffdays/$cycle);
                            $modcycle=$diffdays%$cycle;
                            $modcycle++;
                            //echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;#3 '.$diffcycle.'-'.$modcycle.' ';
                            $tb_cycle=tb_cycle::where([['group',$dt->group],['days',$modcycle],['check_in','<>','check_out']])->get();
                            foreach($tb_cycle as $dt3){

                                $shift=$dt3->shift;
                                $check_in=$dt3->check_in;
                                $check_out=$dt3->check_out;
                                $advance=$dt3->advance;
                                $cross=$dt3->cross;
                
                                //Return Checkin Range
                                if(isset($check_in)&&$check_in!=''){
                                    $cdate=date('Y-m-d',strtotime($Tgl));
                                    if($advance==1){
                                        $date = date_create($cdate);
                                        date_add($date, date_interval_create_from_date_string('-1 days'));
                                        $cdate= date_format($date, 'Y-m-d');
                                    }
                                    $ncdatein=$cdate." ".$check_in;
                                    //Reduce 2 Hour
                                    $date = date_create($ncdatein);
                                    date_add($date, date_interval_create_from_date_string('-2 hours'));
                                    $ncdateindown= date_format($date, 'Y-m-d H:i:s');
                                    //Increas 5 Hour
                                    $date = date_create($ncdatein);
                                    date_add($date, date_interval_create_from_date_string('5 hours'));
                                    $ncdateinup= date_format($date, 'Y-m-d H:i:s');
                                    //echo $ncdatein.' ';
                                }
                                //Return Checkout Range
                                if(isset($check_out)&&$check_out!=''){
                                    $cdate=date('Y-m-d',strtotime($Tgl));
                                    if($cross==1){
                                        $date = date_create($cdate);
                                        date_add($date, date_interval_create_from_date_string('+1 days'));
                                        $cdate= date_format($date, 'Y-m-d');
                                    }
                                    $ncdateout=$cdate." ".$check_out;
                                    //Reduce 2 Hour
                                    $date = date_create($ncdateout);
                                    date_add($date, date_interval_create_from_date_string('-2 hours'));
                                    $ncdateoutdown= date_format($date, 'Y-m-d H:i:s');
                                    //Increas 5 Hour
                                    $date = date_create($ncdateout);
                                    date_add($date, date_interval_create_from_date_string('4 hours 30 minutes'));
                                    $ncdateoutup= date_format($date, 'Y-m-d H:i:s');
                                    //echo $ncdateout.' ';
                                }
                                $in_status='0';
                                $out_status='0';
                                $off_status='0';

                                $qry2=mysqli_query($host,"select checkinout.checktime,checkinout.userid,userinfo.name from checkinout left join userinfo on checkinout.userid=userinfo.userid where userinfo.userid='$PIN' and checkinout.checktime>='$ncdateindown' and checkinout.checktime<='$ncdateoutup' order by checkinout.checktime asc")or die(mysqli_error($host));
                                while($dt2=mysqli_fetch_array($qry2)){
                                    $ctime=$dt2['checktime'];
                                    //Conditionl IN-OUT
                                    if($ctime>=$ncdateindown and $ctime<=$ncdateinup){
                                        $in_status="1";
                                        $tgl101 = strtotime($check_in);
                                        $tgl201 = strtotime($ctime);
                                        if($tgl201>$tgl101){
                                            $delay_minutes=$delay_minutes+floor(($tgl201-$tgl101)/60);
                                            $delay_fr++;
                                        }
                                        //$sample = $tgl201-$tgl101;
                                        //echo $delay_fr.' '.$delay_minutes.'<br>';
                                    }
                                   if($ctime>=$ncdateoutdown and $ctime<=$ncdateoutup)$status="2";
                                }

                                $tb_freeday=tb_employee_freeday::where([['id_employee',$userid],['date_off',$Tgl]])->count();
                                $tb_changeday=tb_changeday::where([['id_employee',$userid],['date_off',$Tgl]])->count();
                                $i++;
                                if($tb_freeday>0||$tb_changeday>0){
                                    $i--;
                                    $off_status='1';
                                }

                                if($in_status=='1'||$out_status=='1')$present++;
                                else{
                                    if(strlen($day)==1)$dj='0'.$day;
                                    else $dj=$day;
                                    if($off_status=='0'){
                                        $offDays=$offDays.$dj.' ';
                                    }
                                }

                            }
                        }
                    

                        if($i==0)$presence=0;
                        else
                        $presence=number_format($present/$i*100,'2');
                        $absent=100-$presence;

                        $remark=1;

                        $check_presence=tb_presence::where([['month',$Months],['employee_id',$userid]])->count();
                        if($userid<>''){
                            if($check_presence==0){
                                tb_presence::create([
                                    'month'=>$Months,
                                    'start'=>$Tglawal,
                                    'end'=>$Tglakhir,
                                    'employee_id'=>$userid,
                                    'plan'=>$i,
                                    'actual'=>$present,
                                    'presence'=>$presence,
                                    'delay_fr'=>$delay_fr,
                                    'delay_minutes'=>$delay_minutes,
                                    'status'=>$remark,
                                    'absent' => $offDays
                                ]);
                        
                            }else{
                                tb_presence::where([['month',$Months],['employee_id',$userid]])->update([
                                    'plan'=>$i,
                                    'actual'=>$present,
                                    'presence'=>$presence,
                                    'delay_fr'=>$delay_fr,
                                    'delay_minutes'=>$delay_minutes,
                                    'status'=>$remark,
                                    'absent' => $offDays
                                ]);
                            }
                        }


                    //End Insert Old Code

                    }
                }

                //\Log::info('Presence #2'.$userid.' '.$thn.'-'.$bln.' Updated');
            }        

            //End New Code

        }

        //\Log::info('Finish');
        return redirect('/Employee/'.$id.'/'.$PIN);
    }
    function payrollUpdate($id,$pin){
        $tb_cutoff=tb_cutoff::where('usage','payroll')->get();
        foreach($tb_cutoff as $dt){
            $Batas_awal=$dt->start_implement;
            $Batas_akhir=$Batas_awal-1;
        }
        date_default_timezone_set("Asia/Bangkok");
        $kalendar=CAL_GREGORIAN;
        $host = mysqli_connect("192.168.121.4:83306","cahyudin","123456","adms_db");

        $tb_employee_shift=DB::table('tb_employee_shifts')->leftjoin('tb_employees','tb_employees.id','=','tb_employee_shifts.id_employee')->where('tb_employees.id',$id)->get(['tb_employee_shifts.*','tb_employees.PIN']);
        //$tb_employee_shift=tb_employee_shift::all();
        foreach($tb_employee_shift as $row){

            $userid=$row->id_employee;
            $id_shift=$row->id_shift;
            $PIN=$row->PIN;
            //echo '<br>#1 '.$userid.': ';

            //New Code
            $Months=date('Y-m');

            $blnawal=date('m')-1;
            $thnawal=date('Y');
            if($blnawal==0){
                $blnawal=12;
                $thnawal=$thnawal-1;
            }
            $tglawal=$thnawal.'-'.$blnawal.'-'.$Batas_awal;
            $tglakhir=$Months.'-'.$Batas_akhir;
            
            $hariawal=cal_days_in_month($kalendar,$blnawal,$thnawal);

            $i=0;
            $present=0;
            $presence=0;
            $offDays='';

            for($j=$Batas_awal;$j<=$hariawal;$j++){
                $Tgl=$thnawal.'-'.$blnawal.'-'.$j;

                //insert Old Code

                    $tb_group_shift=tb_group_shift::where('id',$id_shift)->get();
                    foreach($tb_group_shift as $dt){
                        $tgl1 = new DateTime($dt->start_implement);
                        $tgl2 = new DateTime($Tgl);
                        $diffdays = $tgl2->diff($tgl1)->days;
                        $tb_group=tb_group::where('group',$dt->group)->get();
                        foreach($tb_group as $dt2){
                            $cycle=$dt2->cycle;
                        }
                        $diffcycle=Floor($diffdays/$cycle);
                        $modcycle=$diffdays%$cycle;
                        $modcycle++;
                        $tb_cycle=tb_cycle::where([['group',$dt->group],['days',$modcycle],['check_in','<>','check_out']])->get();
                        foreach($tb_cycle as $dt3){

                            $shift=$dt3->shift;
                            $check_in=$dt3->check_in;
                            $check_out=$dt3->check_out;
                            $advance=$dt3->advance;
                            $cross=$dt3->cross;
            
                            //Return Checkin Range
                            if(isset($check_in)&&$check_in!=''){
                                $cdate=date('Y-m-d',strtotime($Tgl));
                                if($advance==1){
                                    $date = date_create($cdate);
                                    date_add($date, date_interval_create_from_date_string('-1 days'));
                                    $cdate= date_format($date, 'Y-m-d');
                                }
                                $ncdatein=$cdate." ".$check_in;
                                //Reduce 2 Hour
                                $date = date_create($ncdatein);
                                date_add($date, date_interval_create_from_date_string('-2 hours'));
                                $ncdateindown= date_format($date, 'Y-m-d H:i:s');
                                //Increas 5 Hour
                                $date = date_create($ncdatein);
                                date_add($date, date_interval_create_from_date_string('5 hours'));
                                $ncdateinup= date_format($date, 'Y-m-d H:i:s');
                                //echo $ncdatein.' ';
                            }
                            //Return Checkout Range
                            if(isset($check_out)&&$check_out!=''){
                                $cdate=date('Y-m-d',strtotime($Tgl));
                                if($cross==1){
                                    $date = date_create($cdate);
                                    date_add($date, date_interval_create_from_date_string('+1 days'));
                                    $cdate= date_format($date, 'Y-m-d');
                                }
                                $ncdateout=$cdate." ".$check_out;
                                //Reduce 2 Hour
                                $date = date_create($ncdateout);
                                date_add($date, date_interval_create_from_date_string('-2 hours'));
                                $ncdateoutdown= date_format($date, 'Y-m-d H:i:s');
                                //Increas 5 Hour
                                $date = date_create($ncdateout);
                                date_add($date, date_interval_create_from_date_string('4 hours 30 minutes'));
                                $ncdateoutup= date_format($date, 'Y-m-d H:i:s');
                                //echo $ncdateout.' ';
                            }

                            $status='0';

                            $qry2=mysqli_query($host,"select checkinout.checktime,checkinout.userid,userinfo.name from checkinout left join userinfo on checkinout.userid=userinfo.userid where userinfo.userid='$PIN' and checkinout.checktime>='$ncdateindown' and checkinout.checktime<='$ncdateoutup' order by checkinout.checktime asc")or die(mysqli_error($host));
                            while($dt2=mysqli_fetch_array($qry2)){
                                $ctime=$dt2['checktime'];
                                //Conditionl IN-OUT
                                if($ctime>=$ncdateindown and $ctime<=$ncdateinup)$status="1";
                                if($ctime>=$ncdateoutdown and $ctime<=$ncdateoutup)$status="2";
                            }

                            $tb_freeday=tb_employee_freeday::where([['id_employee',$userid],['date_off',$Tgl]])->count();
                            $tb_changeday=tb_changeday::where([['id_employee',$userid],['date_off',$Tgl]])->count();
                            if($tb_freeday==0||$tb_changeday>0){
                                $i++;
                            }else $status='3';
                            //echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;#4 '.$i.' ';

                            if($status=='1'||$status=='2'||$tb_changeday>0)$present++;
                            elseif($status=='0') {
                                if(strlen($j)==1)$dj='0'.$j;
                                else $dj=$j;
                                $offDays=$offDays.$dj.' ';
                            }
                            
                        }
                    }

                \Log::info('Payroll '.$userid.' '.$Tgl.' Off: '.$offDays);
                //End Insert Old Code

            }
            for($j=1;$j<=$Batas_akhir;$j++){
                if(strlen($j)==1)$tj='0'.$j;
                else $tj=$j;
                $Tgl=$Months.'-'.$tj;

                if($Tgl<=date('Y-m-d')){

                //insert Old Code

                    $tb_group_shift=tb_group_shift::where('id',$id_shift)->get();
                    foreach($tb_group_shift as $dt){
                        $tgl1 = new DateTime($dt->start_implement);
                        $tgl2 = new DateTime($Tgl);
                        $diffdays = $tgl2->diff($tgl1)->days;
                        $tb_group=tb_group::where('group',$dt->group)->get();
                        foreach($tb_group as $dt2){
                            $cycle=$dt2->cycle;
                        }
                        $diffcycle=Floor($diffdays/$cycle);
                        $modcycle=$diffdays%$cycle;
                        $modcycle++;
                        $tb_cycle=tb_cycle::where([['group',$dt->group],['days',$modcycle],['check_in','<>','check_out']])->get();
                        foreach($tb_cycle as $dt3){

                            $shift=$dt3->shift;
                            $check_in=$dt3->check_in;
                            $check_out=$dt3->check_out;
                            $advance=$dt3->advance;
                            $cross=$dt3->cross;
            
                            //Return Checkin Range
                            if(isset($check_in)&&$check_in!=''){
                                $cdate=date('Y-m-d',strtotime($Tgl));
                                if($advance==1){
                                    $date = date_create($cdate);
                                    date_add($date, date_interval_create_from_date_string('-1 days'));
                                    $cdate= date_format($date, 'Y-m-d');
                                }
                                $ncdatein=$cdate." ".$check_in;
                                //Reduce 2 Hour
                                $date = date_create($ncdatein);
                                date_add($date, date_interval_create_from_date_string('-2 hours'));
                                $ncdateindown= date_format($date, 'Y-m-d H:i:s');
                                //Increas 5 Hour
                                $date = date_create($ncdatein);
                                date_add($date, date_interval_create_from_date_string('5 hours'));
                                $ncdateinup= date_format($date, 'Y-m-d H:i:s');
                                //echo $ncdatein.' ';
                            }
                            //Return Checkout Range
                            if(isset($check_out)&&$check_out!=''){
                                $cdate=date('Y-m-d',strtotime($Tgl));
                                if($cross==1){
                                    $date = date_create($cdate);
                                    date_add($date, date_interval_create_from_date_string('+1 days'));
                                    $cdate= date_format($date, 'Y-m-d');
                                }
                                $ncdateout=$cdate." ".$check_out;
                                //Reduce 2 Hour
                                $date = date_create($ncdateout);
                                date_add($date, date_interval_create_from_date_string('-2 hours'));
                                $ncdateoutdown= date_format($date, 'Y-m-d H:i:s');
                                //Increas 5 Hour
                                $date = date_create($ncdateout);
                                date_add($date, date_interval_create_from_date_string('4 hours 30 minutes'));
                                $ncdateoutup= date_format($date, 'Y-m-d H:i:s');
                                //echo $ncdateout.' ';
                            }

                            $status='0';

                            $qry2=mysqli_query($host,"select checkinout.checktime,checkinout.userid,userinfo.name from checkinout left join userinfo on checkinout.userid=userinfo.userid where userinfo.userid='$PIN' and checkinout.checktime>='$ncdateindown' and checkinout.checktime<='$ncdateoutup' order by checkinout.checktime asc")or die(mysqli_error($host));
                            while($dt2=mysqli_fetch_array($qry2)){
                                $ctime=$dt2['checktime'];
                                //Conditionl IN-OUT
                                if($ctime>=$ncdateindown and $ctime<=$ncdateinup)$status="1";
                                if($ctime>=$ncdateoutdown and $ctime<=$ncdateoutup)$status="2";
                            }

                            $tb_freeday=tb_employee_freeday::where([['id_employee',$userid],['date_off',$Tgl]])->count();
                            $tb_changeday=tb_changeday::where([['id_employee',$userid],['date_off',$Tgl]])->count();
                            if($tb_freeday==0||$tb_changeday>0){
                                $i++;
                            }else $status='3';
                            //echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;#4 '.$i.' ';

                            if($status=='1'||$status=='2'||$tb_changeday>0)$present++;
                            elseif($status=='0') {
                                if(strlen($j)==1)$dj='0'.$j;
                                else $dj=$j;
                                $offDays=$offDays.$dj.' ';
                            }
                            \Log::info('Payroll '.$userid.' '.$Tgl.' Off: '.$offDays);
                        }
                    }
                

                //End Insert Old Code

                }

            }

            if($i==0)$presence=0;
            else
            $presence=number_format($present/$i*100,'2');
            $absent=100-$presence;

            if(date('Y-m-d')>$tglakhir)$remark=1;
            else $remark=0;

            $check_payroll=tb_payroll::where([['month',$Months],['employee_id',$userid]])->count();
            if($userid<>''){
                if($check_payroll==0){
                    tb_payroll::create([
                        'month'=>$Months,
                        'start'=>$tglawal,
                        'end'=>$tglakhir,
                        'employee_id'=>$userid,
                        'plan'=>$i,
                        'actual'=>$present,
                        'presence'=>$presence,
                        'status'=>$remark,
                        'absent' => $offDays
                    ]);
            
                }else{
                    tb_payroll::where([['month',$Months],['employee_id',$userid]])->update([
                        'plan'=>$i,
                        'actual'=>$present,
                        'presence'=>$presence,
                        'status'=>$remark,
                        'absent' => $offDays
                    ]);
                }
            }

            \Log::info('Payroll '.$userid.' '.$Months.' Updated');

            //End New Code

        }

        \Log::info('Finish');
        return redirect('/Employee/'.$id.'/'.$PIN);
    }
    function leaveUpdate($id,$pin){
        date_default_timezone_set("Asia/Bangkok");
        $kalendar=CAL_GREGORIAN;
        $Tgl=date('Y-m-d');

        $tb_employee=tb_employee::where('id',$id)->get();
        foreach($tb_employee as $dt){
            $tgl1 = new DateTime($dt->join_date);
            $tgl2 = new DateTime($Tgl);
            $diffdays = $tgl2->diff($tgl1)->days;
            $diffyears=Floor($diffdays/365);
            $Thnawal=date('Y',strtotime($dt->join_date));
            $Thnstart=$Thnawal+$diffyears;
            $Thnend=$Thnstart+1;
            $Bln=date('m-d',strtotime($dt->join_date));
            $Periode_awal=$Thnstart.'-'.$Bln;
            $Periode_akhir_temp=$Thnend.'-'.$Bln;
            $Periode_akhir = date('Y-m-d', strtotime("-1 day", strtotime($Periode_akhir_temp)));
            $Periode_extend=date('Y-m-d', strtotime("+6 month", strtotime($Periode_akhir)));
        }

        $tb_employee_freeday=tb_employee_freeday::where([['id_employee',$id],['date_off','>=',$Periode_awal],['date_off','<=',$Periode_extend],['category','Leave']])->count();        $tb_employee_leave=tb_employee_leave::where([['id_employee',$id],['status','1'],['end','>=',$Tgl]])->count();
        if($diffyears>0)$allowance='12';
        else $allowance='0';
        $outstanding=$allowance-$tb_employee_freeday;
        if($tb_employee_leave==0){
            tb_employee_leave::create([
                'id_employee'=>$id,
                'year'=>$Thnstart,
                'start'=>$Periode_awal,
                'end'=>$Periode_akhir,
                'extend'=>$Periode_extend,
                'allowance'=>$allowance,
                'used'=>$tb_employee_freeday,
                'outstanding'=>$outstanding,
                'remark'=>'',
                'status'=>'1'
            ]);
        }else{
            tb_employee_leave::where([['id_employee',$id],['status','1']])->update([
                'allowance'=>$allowance,
                'used'=>$tb_employee_freeday,
                'outstanding'=>$outstanding,
            ]);

        }

        return redirect('/Employee/'.$id.'/'.$pin);

    }
    function employeeShift($Tgl,$show){
        date_default_timezone_set("Asia/Jakarta");
        if($Tgl==0)$Tgl=date('Y-m-d');
        $Tgl_awal=date('Y-m-d',strtotime('-2 weeks',strtotime($Tgl)));
        $Tgl_akhir=date('Y-m-d',strtotime('2 weeks',strtotime($Tgl)));
    
        $nama=Auth::user()->name;
        $email=Auth::user()->email;
        $tb_email=DB::table('tb_emails')->where('email_address',$email)->get();
        foreach($tb_email as $dt){
            $id_employee=$dt->id_employee;
        }
        $tb_admin=DB::table('tb_admins')->where('id_employee',$id_employee)->get();
        $tb_employee=DB::table('tb_employees')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->leftjoin('tb_employee_shifts','tb_employee_shifts.id_employee','=','tb_employees.id')
        ->leftjoin('tb_group_shifts','tb_group_shifts.id','=','tb_employee_shifts.id_shift')
        ->leftjoin('tb_groups','tb_groups.group','=','tb_group_shifts.group')
        ->where([['tb_employees.status','1'],['tb_employee_shifts.status','1'],['tb_employees.dept_id','0']]);
        foreach($tb_admin as $dt_admin){
            $tb_employee=$tb_employee->orWhere([['tb_employees.status','1'],['tb_employee_shifts.status','1'],['tb_employees.dept_id',$dt_admin->dept_id]]); 
        }
        $tb_employee=$tb_employee->get(['tb_employees.*','tb_departments.dept_code','tb_positions.position_name','tb_employee_shifts.id_shift','tb_group_shifts.group','tb_group_shifts.start_implement','tb_groups.cycle','tb_group_shifts.shift_code','tb_groups.nshift']);
        //return $tb_employee;
        return view('page/user/m_employee/employee_shift',['Tgl'=>$Tgl,'Tgl_awal'=>$Tgl_awal,'Tgl_akhir'=>$Tgl_akhir,'tb_employee'=>$tb_employee,'show'=>$show,'menu'=>'meal']);
    }
    function ksk($periode){
        if($periode==0){
            $today=date('Y-m-d');
            $bulan_depan=date('Y-m-d',strtotime('+1 months',strtotime($today)));
            $periode=date('Y-m',strtotime($bulan_depan));
        }
        $email=Auth::user()->email;
        $cek1=DB::table('tb_emails')->where('email_address',$email)->get();
        foreach($cek1 as $dt){$id_user=$dt->id_employee;}
        $tb_department=DB::connection('mysql')->table('tb_departments')->get(['tb_departments.*','tb_departments.dept_code as department']);
        if (request()->user()->hasRole('root')||request()->user()->hasRole('ksk')){

            $admin=Auth::user()->name;
     
            $tb_ksk=DB::table('tb_ksk')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_ksk.dept_id')
            ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','tb_ksk.approval1')
            ->leftjoin('tb_employees as tb_employees2','tb_employees2.id','tb_ksk.approval2')
            ->leftjoin('tb_employees as tb_employees3','tb_employees3.id','tb_ksk.approval3')
            ->leftjoin('tb_employees as tb_employees4','tb_employees4.id','tb_ksk.approval4')
            ->leftjoin('tb_employees as tb_employees5','tb_employees5.id','tb_ksk.approval5')
            ->leftjoin('tb_employees as tb_employees6','tb_employees6.id','tb_ksk.approval6')
            ->leftjoin('tb_employees as tb_employees7','tb_employees7.id','tb_ksk.legalize1')
            ->leftjoin('tb_employees as tb_employees8','tb_employees8.id','tb_ksk.legalize2')
            ->leftjoin('tb_employees as tb_employees9','tb_employees9.id','tb_ksk.legalize3')
            ->leftjoin('tb_employees as tb_employees10','tb_employees10.id','tb_ksk.legalize4')
            ->select([
              'tb_ksk.*',
              'tb_departments.dept_code',
              'tb_employees1.id as approval1',
              'tb_employees2.id as approval2',
              'tb_employees3.id as approval3',
              'tb_employees4.id as approval4',
              'tb_employees5.id as approval5',
              'tb_employees6.id as approval6',
              'tb_employees7.id as legalize1',
              'tb_employees8.id as legalize2',
              'tb_employees9.id as legalize3',
              'tb_employees10.id as legalize4',
              'tb_employees1.employee_name as approvalname1',
              'tb_employees2.employee_name as approvalname2',
              'tb_employees3.employee_name as approvalname3',
              'tb_employees4.employee_name as approvalname4',
              'tb_employees5.employee_name as approvalname5',
              'tb_employees6.employee_name as approvalname6',
              'tb_employees7.employee_name as legalizename1',
              'tb_employees8.employee_name as legalizename2',
              'tb_employees9.employee_name as legalizename3',
              'tb_employees10.employee_name as legalizename4',
            ])
            ->where('tb_ksk.periode',$periode)
            ->where('distribute_status','1')
            ->where('tb_ksk.is_delete','0')
            ->where(function ($query) use($id_user) {
                $query->where('approval1', $id_user)
                      ->orWhere('approval2', $id_user)
                      ->orWhere('approval3', $id_user)
                      ->orWhere('approval4', $id_user)
                      ->orWhere('approval5', $id_user)
                      ->orWhere('approval6', $id_user)
                      ->orWhere('legalize1', $id_user)
                      ->orWhere('legalize2', $id_user)
                      ->orWhere('legalize3', $id_user)
                      ->orWhere('legalize4', $id_user);
            })
            ->orderby('no_ksk','asc')->get();
            //return $tb_ksk;
            return view('page/admin/m_employee/ksk_approval',['tb_ksk'=>$tb_ksk,'periode'=>$periode,'menu'=>'ksk','submenu'=>'ksk','Judul'=>'KSK List']);
        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }
    function kskDetail($id_ksk,$periode){
        $update_performance=$this->update_performance($id_ksk);
        $tb_ksk_lock=DB::table('tb_ksk_lock')->where('periode',$periode)->get();
        $status_lock=0;
        foreach($tb_ksk_lock as $dt){
            $status_lock=$dt->is_lock;
        }
        $email=Auth::user()->email;
        $cek1=DB::table('tb_emails')->where('email_address',$email)
        ->leftjoin('tb_employees','tb_employees.id','=','tb_emails.id_employee')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->get(['tb_emails.*','tb_positions.position_index']);
        foreach($cek1 as $dt){$id_user=$dt->id_employee;$my_index=$dt->position_index;}

        $tb_spv=DB::table('tb_employees')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->where('tb_employees.status','1')
        ->where('position_index','>=','3')
        ->orderby('employee_name','asc')->get('tb_employees.*');
    
        $tb_ksk=DB::table('tb_ksk_detail')
        ->leftjoin('tb_ksk','tb_ksk.no_ksk','=','tb_ksk_detail.no_ksk')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_ksk.dept_id')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_ksk_detail.id_employee')
        ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','tb_ksk.approval1')
        ->leftjoin('tb_employees as tb_employees2','tb_employees2.id','tb_ksk.approval2')
        ->leftjoin('tb_employees as tb_employees3','tb_employees3.id','tb_ksk.approval3')
        ->leftjoin('tb_employees as tb_employees4','tb_employees4.id','tb_ksk.approval4')
        ->leftjoin('tb_employees as tb_employees5','tb_employees5.id','tb_ksk.approval5')
        ->leftjoin('tb_employees as tb_employees6','tb_employees6.id','tb_ksk.approval6')
        ->leftjoin('tb_employees as tb_employees7','tb_employees7.id','tb_ksk.legalize1')
        ->leftjoin('tb_employees as tb_employees8','tb_employees8.id','tb_ksk.legalize2')
        ->leftjoin('tb_employees as tb_employees9','tb_employees9.id','tb_ksk.legalize3')
        ->leftjoin('tb_employees as tb_employees10','tb_employees10.id','tb_ksk.legalize4')
        ->where('tb_ksk.distribute_status','1')
        ->where(function ($query) use($id_user) {
            $query->where('approval1', $id_user)
                ->orWhere('approval2', $id_user)
                ->orWhere('approval3', $id_user)
                ->orWhere('approval4', $id_user)
                ->orWhere('approval5', $id_user)
                ->orWhere('approval6', $id_user)
                ->orWhere('legalize1', $id_user)
                ->orWhere('legalize2', $id_user)
                ->orWhere('legalize3', $id_user)
                ->orWhere('legalize4', $id_user);
        });
        if($id_ksk>0)$tb_ksk=$tb_ksk->where('tb_ksk_detail.id_ksk',$id_ksk);
        //->select(['tb_ksk_detail.*','tb_departments.dept_code','tb_departments.dept_name','tb_employees.NIK','tb_employees.employee_name','tb_positions.position_name','tb_leader.employee_name as leader_name','tb_sh.employee_name as sh_name','tb_dh.employee_name as dh_name','tb_agm.employee_name as agm_name','tb_employees2.employee_name as direct_spv_name'])
        $tb_ksk=$tb_ksk->select([
            'tb_ksk_detail.*',
            'tb_employees.NIK',
            'tb_employees.employee_name',
            'tb_departments.dept_code',
            'tb_employees1.id as approval1',
            'tb_employees2.id as approval2',
            'tb_employees3.id as approval3',
            'tb_employees4.id as approval4',
            'tb_employees5.id as approval5',
            'tb_employees6.id as approval6',
            'tb_employees7.id as legalize1',
            'tb_employees8.id as legalize2',
            'tb_employees9.id as legalize3',
            'tb_employees10.id as legalize4',
            'tb_employees1.employee_name as approvalname1',
            'tb_employees2.employee_name as approvalname2',
            'tb_employees3.employee_name as approvalname3',
            'tb_employees4.employee_name as approvalname4',
            'tb_employees5.employee_name as approvalname5',
            'tb_employees6.employee_name as approvalname6',
            'tb_ksk.approval_status',
            'tb_employees7.employee_name as legalizename1',
            'tb_employees8.employee_name as legalizename2',
            'tb_employees9.employee_name as legalizename3',
            'tb_employees10.employee_name as legalizename4',
            'tb_ksk.approval1_status',
            'tb_ksk.approval2_status',
            'tb_ksk.approval3_status',
            'tb_ksk.approval4_status',
            'tb_ksk.approval5_status',
            'tb_ksk.approval6_status',
        ])
        ->orderby('no_ksk','asc')->get();
        //return $tb_ksk;
        foreach($tb_ksk as $dt){
            $judul="KSK NO. ".$dt->no_ksk;
        }
        return view('page/admin/m_employee/ksk_approval_detail',['tb_ksk'=>$tb_ksk,'id_ksk'=>$id_ksk,'tb_spv'=>$tb_spv,'id_employee'=>$id_user,'periode'=>$periode,'my_index'=>$my_index,'status_lock'=>$status_lock,'menu'=>'ksk','submenu'=>'contract','submenu'=>'ksk','Judul'=>$judul]);
      
    }
    function kskStatus(Request $data){
        date_default_timezone_set("Asia/jakarta");
        $sekarang=date('Y-m-d H;i:s');
        $nama=Auth::user()->name;
        $email=Auth::user()->email;
        $cek1=DB::table('tb_emails')->where('email_address',$email)->get();
        foreach($cek1 as $dt){$id_user=$dt->id_employee;}

        $qty_ksk_detail_status=DB::connection('mysql')->table('tb_ksk_detail_status')->where('id_ksk_detail',$data->id_ksk_detail)->where('id_employee',$id_user)->count();
        //Entry Detail Status
            $simpan_status=DB::connection('mysql')->table('tb_ksk_detail_status')->insert([
                'id_ksk_detail'=>$data->id_ksk_detail,
                'id_employee'=>$id_user,
                'judge'=>$data->judge,
                'next_contract'=>$data->next_contract,
                'reason'=>$data->reason,
                'admin'=>$nama
            ]);
            if($simpan_status){
                if($data->statusupdate==1){
                    $update_detail=DB::connection('mysql')->table('tb_ksk_detail')->where('id',$data->id_ksk_detail)->update([
                        'judge'=>$data->judge,
                        'next_contract'=>$data->next_contract,
                        'reason'=>$data->reason,
                        'performance'=>$data->performance,
                        'direct_spv'=>$nama,
                    ]);
                }
                return redirect()->back()->with(['success'=>'Success Saving']);
            }
        //End Entry Detail
        return redirect()->back();
    }
    function kskStatus_custome24(Request $data){
        date_default_timezone_set("Asia/jakarta");
        $sekarang=date('Y-m-d H;i:s');
        $nama=Auth::user()->name;
        $email=Auth::user()->email;
        $cek1=DB::table('tb_emails')->where('email_address',$email)->get();
        foreach($cek1 as $dt){$id_user=$dt->id_employee;}
        $date_contract=$data->date_contract.'-24';
        $qty_ksk_detail_status=DB::connection('mysql')->table('tb_ksk_detail_status')->where('id_ksk_detail',$data->id_ksk_detail)->where('id_employee',$id_user)->count();
        //Entry Detail Status
            $simpan_status=DB::connection('mysql')->table('tb_ksk_detail_status')->insert([
                'id_ksk_detail'=>$data->id_ksk_detail,
                'id_employee'=>$id_user,
                'judge'=>$data->judge,
                'next_contract'=>$data->next_contract,
                'days'=>$data->days,
                'date_contract'=>$date_contract,
                'reason'=>$data->reason,
                'admin'=>$nama
            ]);
            if($simpan_status){
                if($data->statusupdate==1){
                    $update_detail=DB::connection('mysql')->table('tb_ksk_detail')->where('id',$data->id_ksk_detail)->update([
                        'judge'=>$data->judge,
                        'next_contract'=>$data->next_contract,
                        'days'=>$data->days,
                        'date_contract'=>$date_contract,
                        'reason'=>$data->reason,
                        'performance'=>$data->performance,
                        'direct_spv'=>$nama,
                    ]);
                }
                return redirect()->back()->with(['success'=>'Success Saving']);
            }
        //End Entry Detail
        return redirect()->back();
    }
    function kskPrint($id_ksk){
  
        $tb_ksk=DB::table('tb_ksk_detail')
        ->leftjoin('tb_ksk','tb_ksk.no_ksk','=','tb_ksk_detail.no_ksk')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_ksk.dept_id')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_ksk_detail.id_employee')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','tb_ksk.approval1')
        ->leftjoin('tb_employees as tb_employees2','tb_employees2.id','tb_ksk.approval2')
        ->leftjoin('tb_employees as tb_employees3','tb_employees3.id','tb_ksk.approval3')
        ->leftjoin('tb_employees as tb_employees4','tb_employees4.id','tb_ksk.approval4')
        ->leftjoin('tb_employees as tb_employees5','tb_employees5.id','tb_ksk.approval5')
        ->leftjoin('tb_employees as tb_employees6','tb_employees6.id','tb_ksk.approval6')
        ->leftjoin('tb_employees as tb_employees7','tb_employees7.id','tb_ksk.legalize1')
        ->leftjoin('tb_employees as tb_employees8','tb_employees8.id','tb_ksk.legalize2')
        ->leftjoin('tb_employees as tb_employees9','tb_employees9.id','tb_ksk.legalize3')
        ->leftjoin('tb_employees as tb_employees10','tb_employees10.id','tb_ksk.legalize4')
        ->leftjoin('tb_ksk_target',function($join){
            $join->on('tb_ksk_target.dept_id','=','tb_ksk.dept_id')->on('tb_ksk_target.periode','=','tb_ksk.periode');
        })
        ->where('tb_ksk_detail.id_ksk',$id_ksk)
        ->select([
          'tb_ksk_detail.*',
          'tb_ksk.direct_pos as pic_pos',
          'tb_ksk.direct_spv as pic',
          'tb_employees.NIK',
          'tb_employees.employee_name',
          'tb_positions.position_name',
          'tb_departments.dept_name',
          'tb_departments.dept_code',
          'tb_employees1.id as approval1',
          'tb_employees2.id as approval2',
          'tb_employees3.id as approval3',
          'tb_employees4.id as approval4',
          'tb_employees5.id as approval5',
          'tb_employees5.leader_id as approval6',
          'tb_employees7.id as legalize1',
          'tb_employees8.id as legalize2',
          'tb_employees9.id as legalize3',
          'tb_employees10.id as legalize4',
          'tb_employees1.employee_name as approvalname1',
          'tb_employees2.employee_name as approvalname2',
          'tb_employees3.employee_name as approvalname3',
          'tb_employees4.employee_name as approvalname4',
          'tb_employees5.employee_name as approvalname5',
          'tb_employees6.employee_name as approvalname6',
          'tb_employees7.employee_name as legalizename1',
          'tb_employees8.employee_name as legalizename2',
          'tb_employees9.employee_name as legalizename3',
          'tb_employees10.employee_name as legalizename4',
          'tb_ksk_target.permanent_target',
          'tb_ksk_target.contract_target',
          'tb_ksk_target.magang_target',
          'tb_ksk_target.permanent_actual',
          'tb_ksk_target.contract_actual',
          'tb_ksk_target.magang_actual',
          'tb_ksk_target.permanent_remain',
          'tb_ksk_target.contract_remain',
          'tb_ksk_target.magang_remain',
          'tb_ksk.approval1_status',
          'tb_ksk.approval2_status',
          'tb_ksk.approval3_status',
          'tb_ksk.approval4_status',
          'tb_ksk.approval5_status',
          'tb_ksk.approval6_status',
          'tb_ksk.legalize1_status',
          'tb_ksk.legalize2_status',
          'tb_ksk.legalize3_status',
          'tb_ksk.legalize4_status',
          'tb_ksk.approval1_date',
          'tb_ksk.approval2_date',
          'tb_ksk.approval3_date',
          'tb_ksk.approval4_date',
          'tb_ksk.approval5_date',
          'tb_ksk.approval6_date',
          'tb_ksk.legalize1_date',
          'tb_ksk.legalize2_date',
          'tb_ksk.legalize3_date',
          'tb_ksk.legalize4_date',
          'tb_employees1.position_id as approvalpos1',
          'tb_employees2.position_id as approvalpos2',
          'tb_employees3.position_id as approvalpos3',
          'tb_employees4.position_id as approvalpos4',
          'tb_employees5.position_id as approvalpos5',
          'tb_employees6.position_id as approvalpos6',
          'tb_employees7.position_id as legalizepos1',
          'tb_employees8.position_id as legalizepos2',
          'tb_employees9.position_id as legalizepos3',
          'tb_employees10.position_id as legalizepos4',
          'tb_ksk.manager_name',
        ])
        ->orderby('tb_employees.NIK','asc')->get();
        //return $tb_ksk;
        foreach($tb_ksk as $dt){
          if($dt->approvalname6!='')$jml_approval=6;
          elseif($dt->approvalname5!='')$jml_approval=5;
          elseif($dt->approvalname4!='')$jml_approval=4;
          elseif($dt->approvalname3!='')$jml_approval=3;
          elseif($dt->approvalname2!='')$jml_approval=2;
          elseif($dt->approvalname1!='')$jml_approval=1;
          $pos[1]=DB::table('tb_positions')->where('id',$dt->approvalpos1)->value('position_name');
          $pos[2]=DB::table('tb_positions')->where('id',$dt->approvalpos2)->value('position_name');
          $pos[3]=DB::table('tb_positions')->where('id',$dt->approvalpos3)->value('position_name');
          $pos[4]=DB::table('tb_positions')->where('id',$dt->approvalpos4)->value('position_name');
          $pos[5]=DB::table('tb_positions')->where('id',$dt->approvalpos5)->value('position_name');
          $pos[6]=DB::table('tb_positions')->where('id',$dt->approvalpos6)->value('position_name');
          $pos[7]=DB::table('tb_positions')->where('id',$dt->legalizepos1)->value('position_name');
          $pos[8]=DB::table('tb_positions')->where('id',$dt->legalizepos2)->value('position_name');
          $pos[9]=DB::table('tb_positions')->where('id',$dt->legalizepos3)->value('position_name');
        }
        $FileName='KSK #'.$id_ksk.'.PDF';
        $pdf = PDF::loadview('page/admin/m_employee/ksk_preview',['tb_ksk'=>$tb_ksk,'pos'=>$pos,'jml_approval'=>$jml_approval])->setPaper('a4','landscape');
        return $pdf->stream($FileName);
      
    }
    function kskConfirm($id_ksk){
        $sekarang=date('Y-m-d H;i:s');
        $nama=Auth::user()->name;
        $email=Auth::user()->email;
        $cek1=DB::table('tb_emails')->where('email_address',$email)->get();
        foreach($cek1 as $dt){$id_user=$dt->id_employee;}

        $tb_ksks=DB::connection('mysql')->table('tb_ksk')->where('tb_ksk.id',$id_ksk)->get();
        foreach($tb_ksks as $dt){

            $cek2=DB::table('tb_emails')->where('id_employee',$dt->admin_id)->where('verified','1')->get();
            foreach($cek2 as $dt2){$admin_mail=$dt2->email_address;}
            $no_ksk=$dt->no_ksk;

            if($id_user==$dt->approval1){
                $tb_ksk=DB::connection('mysql')->table('tb_ksk')->where('id',$dt->id)->update(['approval1_status'=>'1','approval1_date'=>$sekarang,'direct_spv'=>$nama]);
                if($dt->approval2==0){
                    $tb_ksk=DB::connection('mysql')->table('tb_ksk')->where('id',$dt->id)->update(['approval_status'=>'1']);
                    $this->kirim_mail($admin_mail,$no_ksk);
                    $cek2=DB::table('tb_emails')->where('id_employee',$dt->legalize1)->get();
                    foreach($cek2 as $dt2){
                        $this->kirim_mail($dt2->email_address,$no_ksk);
                    }
                }else{
                    $cek2=DB::table('tb_emails')->where('id_employee',$dt->approval2)->get();
                    foreach($cek2 as $dt2){
                        $this->kirim_mail($dt2->email_address,$no_ksk);
                    }
                }
            }
            if($id_user==$dt->approval2){
                $tb_ksk=DB::connection('mysql')->table('tb_ksk')->where('id',$dt->id)->update(['approval2_status'=>'1','approval2_date'=>$sekarang]);
                if($dt->approval3==0){
                    $tb_ksk=DB::connection('mysql')->table('tb_ksk')->where('id',$dt->id)->update(['approval_status'=>'1']);
                    $this->kirim_mail($admin_mail,$no_ksk);
                    $cek2=DB::table('tb_emails')->where('id_employee',$dt->legalize1)->get();
                    foreach($cek2 as $dt2){
                        $this->kirim_mail($dt2->email_address,$no_ksk);
                    }
                }else{
                    $cek2=DB::table('tb_emails')->where('id_employee',$dt->approval3)->get();
                    foreach($cek2 as $dt2){
                        $this->kirim_mail($dt2->email_address,$no_ksk);
                    }
                }
            }
            if($id_user==$dt->approval3){
                $tb_ksk=DB::connection('mysql')->table('tb_ksk')->where('id',$dt->id)->update(['approval3_status'=>'1','approval3_date'=>$sekarang]);
                if($dt->approval4==0){
                    $tb_ksk=DB::connection('mysql')->table('tb_ksk')->where('id',$dt->id)->update(['approval_status'=>'1']);
                    $this->kirim_mail($admin_mail,$no_ksk);
                    $cek2=DB::table('tb_emails')->where('id_employee',$dt->legalize1)->get();
                    foreach($cek2 as $dt2){
                        $this->kirim_mail($dt2->email_address,$no_ksk);
                    }
                }else{
                    $cek2=DB::table('tb_emails')->where('id_employee',$dt->approval4)->get();
                    foreach($cek2 as $dt2){
                        $this->kirim_mail($dt2->email_address,$no_ksk);
                    }
                }
            }
            if($id_user==$dt->approval4){
                $tb_ksk=DB::connection('mysql')->table('tb_ksk')->where('id',$dt->id)->update(['approval4_status'=>'1','approval4_date'=>$sekarang]);
                if($dt->approval5==0){
                    $tb_ksk=DB::connection('mysql')->table('tb_ksk')->where('id',$dt->id)->update(['approval_status'=>'1']);
                    $this->kirim_mail($admin_mail,$no_ksk);
                    $cek2=DB::table('tb_emails')->where('id_employee',$dt->legalize1)->get();
                    foreach($cek2 as $dt2){
                        $this->kirim_mail($dt2->email_address,$no_ksk);
                    }
                }else{
                    $cek2=DB::table('tb_emails')->where('id_employee',$dt->approval5)->get();
                    foreach($cek2 as $dt2){
                        $this->kirim_mail($dt2->email_address,$no_ksk);
                    }
                }
            }
            if($id_user==$dt->approval5){
                $tb_ksk=DB::connection('mysql')->table('tb_ksk')->where('id',$dt->id)->update(['approval5_status'=>'1','approval5_date'=>$sekarang]);
                if($dt->approval6==0){
                    $tb_ksk=DB::connection('mysql')->table('tb_ksk')->where('id',$dt->id)->update(['approval_status'=>'1']);
                    $this->kirim_mail($admin_mail,$no_ksk);
                    $cek2=DB::table('tb_emails')->where('id_employee',$dt->legalize1)->get();
                    foreach($cek2 as $dt2){
                        $this->kirim_mail($dt2->email_address,$no_ksk);
                    }
                }else{
                    $cek2=DB::table('tb_emails')->where('id_employee',$dt->approval6)->get();
                    foreach($cek2 as $dt2){
                        $this->kirim_mail($dt2->email_address,$no_ksk);
                    }
                }
            }
            if($id_user==$dt->approval6){
                $tb_ksk=DB::connection('mysql')->table('tb_ksk')->where('id',$dt->id)->update(['approval6_status'=>'1','approval_status'=>'1','approval6_date'=>$sekarang]);
                $this->kirim_mail($admin_mail,$no_ksk);
                $cek2=DB::table('tb_emails')->where('id_employee',$dt->legalize1)->get();
                foreach($cek2 as $dt2){
                    $this->kirim_mail($dt2->email_address,$no_ksk);
                }
            }
            
            if($id_user==$dt->legalize1){
                $tb_ksk=DB::connection('mysql')->table('tb_ksk')->where('id',$dt->id)->update(['legalize1_status'=>'1','legalize1_date'=>$sekarang]);
                $cek2=DB::table('tb_emails')->where('id_employee',$dt->legalize2)->get();
                foreach($cek2 as $dt2){
                    $this->kirim_mail($dt2->email_address,$no_ksk);
                }
            }
            if($id_user==$dt->legalize2){
                $tb_ksk=DB::connection('mysql')->table('tb_ksk')->where('id',$dt->id)->update(['legalize2_status'=>'1','legalize2_date'=>$sekarang]);
                $cek2=DB::table('tb_emails')->where('id_employee',$dt->legalize3)->get();
                foreach($cek2 as $dt2){
                    $this->kirim_mail($dt2->email_address,$no_ksk);
                }
            }
            if($id_user==$dt->legalize3){
                $tb_ksk=DB::connection('mysql')->table('tb_ksk')->where('id',$dt->id)->update(['legalize3_status'=>'1','legalize3_date'=>$sekarang]);
                $cek2=DB::table('tb_emails')->where('id_employee',$dt->legalize4)->get();
                foreach($cek2 as $dt2){
                    $this->kirim_mail($dt2->email_address,$no_ksk);
                }
            }
            if($id_user==$dt->legalize4){
                $tb_ksk=DB::connection('mysql')->table('tb_ksk')->where('id',$dt->id)->update(['legalize4_status'=>'1','legalize_status'=>'1','legalize4_date'=>$sekarang]);
                $this->kirim_mail($admin_mail,$no_ksk);
            }
        }
        $send_whatsapp=$this->notificationKSK($id_ksk);
        return redirect()->back()->with(['success'=>'Success Approve KSK']);
    }
    function kskApproved($id){
        $email=Auth::user()->email;
        $tb_ksk_detail=DB::table('tb_ksk_detail')
        ->leftjoin('tb_ksk','tb_ksk.no_ksk','=','tb_ksk_detail.no_ksk')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_ksk.dept_id')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_ksk_detail.id_employee')
        ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','tb_ksk.approval1')
        ->leftjoin('tb_employees as tb_employees2','tb_employees2.id','tb_ksk.approval2')
        ->leftjoin('tb_employees as tb_employees3','tb_employees3.id','tb_ksk.approval3')
        ->leftjoin('tb_employees as tb_employees4','tb_employees4.id','tb_ksk.approval4')
        ->leftjoin('tb_employees as tb_employees5','tb_employees5.id','tb_ksk.approval5')
        ->leftjoin('tb_employees as tb_employees6','tb_employees6.id','tb_ksk.approval6')
        ->leftjoin('tb_employees as tb_employees7','tb_employees7.id','tb_ksk.legalize1')
        ->leftjoin('tb_employees as tb_employees8','tb_employees8.id','tb_ksk.legalize2')
        ->leftjoin('tb_employees as tb_employees9','tb_employees9.id','tb_ksk.legalize3')
        ->leftjoin('tb_employees as tb_employees10','tb_employees10.id','tb_ksk.legalize4')
        ->where('tb_ksk.direct_id',$id)
        ->select([
            'tb_ksk_detail.*',
            'tb_employees.NIK',
            'tb_employees.employee_name',
            'tb_departments.dept_code',
            'tb_employees1.id as approval1',
            'tb_employees2.id as approval2',
            'tb_employees3.id as approval3',
            'tb_employees4.id as approval4',
            'tb_employees5.id as approval5',
            'tb_employees6.id as approval6',
            'tb_employees7.id as legalize1',
            'tb_employees8.id as legalize2',
            'tb_employees9.id as legalize3',
            'tb_employees10.id as legalize4',
            'tb_employees1.employee_name as approvalname1',
            'tb_employees2.employee_name as approvalname2',
            'tb_employees3.employee_name as approvalname3',
            'tb_employees4.employee_name as approvalname4',
            'tb_employees5.employee_name as approvalname5',
            'tb_employees6.employee_name as approvalname6',
            'tb_ksk.approval_status',
            'tb_employees7.employee_name as legalizename1',
            'tb_employees8.employee_name as legalizename2',
            'tb_employees9.employee_name as legalizename3',
            'tb_employees10.employee_name as legalizename4',
        ])
        ->orderby('no_ksk','asc')->get();

        $judul="KSK ".$periode;
        $tb_email=DB::connection('mysql')->table('tb_emails')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_emails.id_employee')
        ->where('id_employee',$dt->direct_id)->get(['tb_employees.*','tb_emails.email_address']);
        foreach($tb_email as $dt2){
            //$tujuan=$dt2->email_address;
            $tujuan='cahyudin@summitadyawinsa.co.id';
            $nama=$dt2->employee_name;
            if($tujuan=='cahyudin@summitadyawinsa.co.id'){
                //$kirim=Mail::to($tujuan)->queue(new ksk_distribute($tb_ksk_detail,$periode,$judul,$nama));
                Log::info('Send Notification KSK to '.$tujuan);      
            }
        }
    }
    function kirim_mail($admin_mail,$no_ksk){
        $judul='KSK '.$no_ksk;
        //$admin_mail='cahyudin@summitadyawinsa.co.id';
        //$kirim=Mail::to($admin_mail)->queue(new ksk_approved($judul));
        //Log::info('KSK return back to HR '.$admin_mail);      
        Log::info('Skip Email '.$admin_mail); 
    }
    function kskInfo(Request $data){
        $sekarang=date('Y-m-d H;i:s');
        $nama=Auth::user()->name;
        $email=Auth::user()->email;
        $cek1=DB::table('tb_emails')->where('email_address',$email)
        ->leftjoin('tb_employees','tb_employees.id','=','tb_emails.id_employee')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->get(['tb_emails.*','tb_positions.position_index']);
        foreach($cek1 as $dt){$id_user=$dt->id_employee;$my_index=$dt->position_index;}

        $id_ksk_detail=$data->idkskdetail;
        $tb_ksk=DB::connection('mysql')->table('tb_ksk_detail_status')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_ksk_detail_status.id_employee')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->leftjoin('tb_ksk_detail','tb_ksk_detail.id','=','tb_ksk_detail_status.id_ksk_detail')
        ->where('id_ksk_detail',$id_ksk_detail)
        ->get(['tb_ksk_detail_status.*','tb_employees.employee_name','tb_positions.position_name','tb_positions.position_index','tb_ksk_detail.visible_status']);
        $konten="";
        $konten.="<tr style='background:#DDD;height:30px;'><th>ASSIGN_BY</th><th>POSITION</th><th>JUDGEMENT</th><th>REASON</th><th>TIME</th></tr>";
        foreach($tb_ksk as $dt){
            if($dt->visible_status==1||($dt->visible_status==0&&$my_index>=$dt->position_index)){
                $konten.="<tr>";
                $konten.="<td style='height:30px;'>".$dt->position_name."</td>";
                $konten.="<td>".$dt->employee_name."</td>";
                $konten.="<td>".$dt->judge;
                if($dt->judge=='EXTEND')$konten.=" (".$dt->next_contract." BULAN)";
                $konten."</td>";
                $konten.="<td>".$dt->reason."</td>";
                $konten.="<td>".$dt->updated_at."</td>";
                $konten.="</tr>";
            }
        }
        return $konten;
    }
    function kskDisplay($id,$my_index){
        $tb_ksk_detail=DB::connection('mysql')->table('tb_ksk_detail')->where('id',$id)->get();
        foreach($tb_ksk_detail as $dt){
            $status_lama=$dt->visible_status;
            if($status_lama==0)$status_baru=1;
            else $status_baru=0;
            $update=DB::connection('mysql')->table('tb_ksk_detail')->where('id',$id)->update([
                'visible_status'=>$status_baru,
                'hide_by'=>$my_index,
            ]);
        }
        return redirect()->back()->with(['success'=>'Success Change']);
    }
    function leader(){
        $nama=Auth::user()->name;
        $email=Auth::user()->email;
        $cek1=DB::table('tb_emails')->where('email_address',$email)->get();
        foreach($cek1 as $dt){$id_user=$dt->id_employee;}
        $tb_admins=DB::table('tb_admins')->where('id_employee',$id_user)->get();
    
        $tb_employee=DB::table('tb_employees')
        ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_employees.leader_id')
        ->leftjoin('tb_cost_center','tb_cost_center.cc_code','=','tb_employees.cc_code')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->leftjoin('tb_employee_shifts','tb_employee_shifts.id_employee','=','tb_employees.id')
        ->leftjoin('tb_group_shifts','tb_group_shifts.id','=','tb_employee_shifts.id_shift')
        ->leftjoin('tb_statuses', function ($join) {
            $join->on('tb_statuses.id_employee', '=', 'tb_employees.id')
                  ->where('tb_statuses.active', '1');
        });
        $tb_employee=$tb_employee->where([['tb_employees.status','1'],['tb_employees.dept_id','0'],['tb_employee_shifts.status','1']]);
        foreach($tb_admins as $dt2){
            $tb_employee=$tb_employee->orwhere([['tb_employees.status','1'],['tb_employees.dept_id',$dt2->dept_id],['tb_employee_shifts.status','1']]);
        }
        $tb_employee=$tb_employee->orderby('tb_statuses.finish_contract','asc')->get(['tb_employees.*','tb_employees1.employee_name as leader_name','tb_departments.dept_code','tb_positions.position_name','tb_employee_shifts.id_shift','tb_group_shifts.shift_code','tb_statuses.start_contract','tb_statuses.finish_contract','tb_cost_center.segment_name']);
        //return $tb_employee;
        $last_update=DB::table('tb_employees')->max('updated_at');
        return view('page/user/m_employee/leader',['tb_employee'=>$tb_employee,'menu'=>'leader']);
    }
    function updateLeader($id){
        $tb_department=DB::table('tb_departments')->where('isDelete',0)->get();
        $tb_position=DB::table('tb_positions')->get();
        $leader_name='';
        $tb_employee=DB::table('tb_employees')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->leftjoin('tb_cost_center','tb_cost_center.cc_code','=','tb_employees.cc_code')
        ->where('tb_employees.id',$id)->orderby('id','desc')->get(['tb_employees.*','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_name','tb_cost_center.segment_name']);
        foreach($tb_employee as $dt){
            $PIN=$dt->PIN;
            $leader=DB::table('tb_employees')->where('id',$dt->leader_id)->get();
            foreach($leader as $dt2){
                $leader_name=$dt2->employee_name;
            }
            $nama_karyawan=$dt->employee_name;
            $NIK=$dt->NIK;
            $dept_id=$dt->dept_id;
        }
        $tb_cost_center=DB::table('tb_cost_center')->where('dept_id',$dept_id)->where('is_active','1')->orderby('cc_code','asc')->get();
        
        $koneksi='OK';

        return view('page/user/m_employee/leader_update',['tb_department'=>$tb_department,'tb_position'=>$tb_position,'tb_employee'=>$tb_employee,'tb_cost_center'=>$tb_cost_center,'id_employee'=>$id,'nama_karyawan'=>$nama_karyawan,'NIK'=>$NIK,'leader_name'=>$leader_name,'menu'=>'leader']);
    }
    function updateData($id){
        $tb_department=tb_department::where('isDelete',0)->get();
        $tb_position=tb_position::all();
        $leader_name='';
        $tb_employee=DB::table('tb_employees')->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')->where('tb_employees.id',$id)->orderby('id','desc')->get(['tb_employees.*','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_name']);
        foreach($tb_employee as $dt){
            $PIN=$dt->PIN;
            $leader=tb_employee::where('id',$dt->leader_id)->get();
            foreach($leader as $dt2){
                $leader_name=$dt2->employee_name;
            }
            $nama_karyawan=$dt->employee_name;
            $NIK=$dt->NIK;
        }
        $tb_address=tb_address::where([['id_employee',$id]])->get();
        $tb_group_shift=DB::table('tb_group_shifts')->leftjoin('tb_groups','tb_groups.group','=','tb_group_shifts.group')->get(['tb_group_shifts.*','tb_groups.cycle','tb_groups.nshift','tb_groups.ngroup']);
        $tb_employee_shift=tb_employee_shift::where('id_employee',$id)->get();
        $id_shift='';
        foreach($tb_employee_shift as $dt3){
            $id_shift=$dt3->id_shift;
        }
        $tb_group_employee=DB::table('tb_group_shifts')->leftjoin('tb_groups','tb_groups.group','=','tb_group_shifts.group')->where('tb_group_shifts.id',$id_shift)->get(['tb_group_shifts.*','tb_groups.cycle','tb_groups.nshift','tb_groups.ngroup']);
        $tb_education=tb_education::where('id_employee',$id)->orderby('graduate_year','desc')->get();
        $tb_experience=tb_experience::where('id_employee',$id)->orderby('finish_year','desc')->get();
        $tb_skill=tb_skill::where('id_employee',$id)->get();
        $tb_email=tb_email::where([['id_employee',$id]])->get();
        $tb_salary=tb_salary::where([['id_employee',$id],['status','1']])->get();
        $tb_admin=DB::table('tb_admins')->leftjoin('tb_departments','tb_departments.id','=','tb_admins.dept_id')->where('tb_admins.id_employee',$id)->get(['tb_admins.*','tb_departments.dept_code','tb_departments.dept_name']);
        $tb_bagian=DB::table('tb_bagians')->where([['id_employee',$id]])->orderby('implement','desc')->limit(1)->get();
        $tb_detail=DB::table('tb_employee_detail')->where([['id_employee',$id]])->get();
        $tb_address_darurat=DB::table('tb_address_darurat')->where([['id_employee',$id]])->where('status','1')->get();
        $tb_employee_family=DB::table('tb_employee_family')->where([['id_employee',$id]])->where('status','1')->get();
        //return $tb_employee_family;
        
        $koneksi='OK';
        try {DB::connection('fingerPrint')->getPdo();} catch (\Exception $e) {$koneksi='NG';}
        //$koneksi='NG';

        return view('page/admin/m_employee/employee_update',['tb_employee_family'=>$tb_employee_family,'tb_address_darurat'=>$tb_address_darurat,'tb_detail'=>$tb_detail,'tb_bagian'=>$tb_bagian,'PIN'=>$PIN,'koneksi'=>$koneksi,'tb_admin'=>$tb_admin,'tb_salary'=>$tb_salary,'tb_department'=>$tb_department,'tb_position'=>$tb_position,'tb_employee'=>$tb_employee,'tb_address'=>$tb_address,'leader_name'=>$leader_name,'tb_group_shift'=>$tb_group_shift,'tb_group_employee'=>$tb_group_employee,'tb_education'=>$tb_education,'tb_experience'=>$tb_experience,'tb_skill'=>$tb_skill,'tb_email'=>$tb_email,'id_employee'=>$id,'nama_karyawan'=>$nama_karyawan,'NIK'=>$NIK,'menu'=>'employee']);
    }
    function saveData(Request $data){

        $simpan=DB::table('tb_employees')->where('id',$data->id)->update([
            'leader_id'=>$data->leader_id,
            'cc_code'=>$data->cc_code,
        ]);
        if($simpan){
            $cc_name=DB::table('tb_cost_center')->where('cc_code',$data->cc_code)->value('description');
            $update_payroll=DB::table('tb_salary_contract')->where('id_employee',$data->id)->update([
                'cc_code'=>$data->cc_code,
                'cc_name'=>$cc_name
            ]);
            $teks=$data->employee_name.' berhasil diupdate';
            return redirect('/Leader/')->with(['success' => $teks]);
        }
        else    
        return redirect()->back()->with(['success' => 'Gagal']);;
    }
    function access_point(){
        $tb_access_point=DB::table('tb_area_master')->where('is_active','1')->orderby('ap','asc')->orderby('plant','asc')->get();
        return view('page/user/m_employee/access_point',['menu'=>'employees','tb_access_point'=>$tb_access_point,'menu'=>'access_point']);
    }
    function update_access_point($id){
        $data['area']='';
        $data['plant']='';
        $data['ap']='';
        $data['is_active']='1';
        $tb=DB::table('tb_area_master')->where('id',$id)->get();
        foreach($tb as $dt){
            $data['area']=$dt->area;
            $data['plant']=$dt->plant;
            $data['ap']=$dt->ap;
            $data['is_active']=$dt->is_active;
        }
        $data['id']=$id;
        return view('page/user/m_employee/ap_update',['data'=>$data,'tb'=>$tb,'menu'=>'access_point']);
    }
    function saveAccessPoint(Request $data){
        if($data->id!=0){
            $simpan=DB::table('tb_area_master')->where('id',$data->id)->update([
                'area'=>$data->area,
                'plant'=>$data->plant,
                'ap'=>$data->ap,
                'is_active'=>$data->is_active,
            ]);
        }else{
            $simpan=DB::table('tb_area_master')->insert([
                'area'=>$data->area,
                'plant'=>$data->plant,
                'ap'=>$data->ap,
                'is_active'=>$data->is_active,
            ]);
        }
        if($simpan){
            $teks=$data->area.' berhasil diupdate';
            return redirect('/AccessPoint/')->with(['success' => $teks]);
        }
        else    
        return redirect()->back()->with(['success' => 'Gagal']);;
    }
    function working_area(){
        $tb=DB::table('tb_area as a')
        ->leftjoin('tb_area_master as b','b.id','=','a.id_area')
        ->orderby('a.dept','asc')
        ->get(['a.id','a.nik','a.nama','a.dept','a.id_area','b.area','b.plant','b.ap']);
        return view('page/user/m_employee/working_area',['tb_area'=>$tb,'menu'=>'working_area']);
    }
    function update_working_area($id){
        $data['id_area']='';
        $data['area']='';
        $data['plant']='';
        $data['ap']='';
        $data['nik']='';
        $data['nama']='';
        $data['dept']='';
        $tb=DB::table('tb_area as a')
        ->leftjoin('tb_area_master as b','b.id','=','a.id_area')
        ->where('a.id',$id)->get(['a.id','a.nik','a.nama','a.dept','a.id_area','b.area','b.plant','b.ap']);
        foreach($tb as $dt){
            $data['id_area']=$dt->id_area;
            $data['area']=$dt->area;
            $data['nik']=$dt->nik;
            $data['nama']=$dt->nama;
            $data['dept']=$dt->dept;
            $data['plant']=$dt->plant;
            $data['ap']=$dt->ap;
            }
        $data['id']=$id;
        $tb2=DB::table('tb_area_master')->where('is_active','1')->orderby('plant','asc')->get();
        return view('page/user/m_employee/area_update',['data'=>$data,'tb'=>$tb,'tb2'=>$tb2,'menu'=>'working_area']);
    }
    function saveWorkingArea(Request $data){
        if($data->id!=0){
            $simpan=DB::table('tb_area')->where('id',$data->id)->update([
                'id_area'=>$data->id_area,
            ]);
        }
        if($simpan){
            $teks=$data->area.' berhasil diupdate';
            return redirect('/WorkingArea/')->with(['success' => $teks]);
        }
        else    
        return redirect()->back()->with(['success' => 'Gagal']);;
    }
    function notificationKSK($id){
        $tb_ksk=DB::connection('mysql')->table('tb_ksk')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_ksk.dept_id')
        ->where('tb_ksk.id',$id)
        ->get(['tb_ksk.*','tb_departments.dept_name']);
        $data['kontak']='';
        $data['pesan']='';
        $id_employee='';
        foreach($tb_ksk as $dt){
            $id_ksk=$dt->no_ksk;
            $dead_line=$dt->dead_line;
            $dept=$dt->dept_name;
            $periode=$dt->periode;
            if($dt->approval1_status==0){
                $id_employee=$dt->approval1;
                $pos='Atasan Pertama';
            }else if($dt->approval2>0&&$dt->approval2_status==0){
                $id_employee=$dt->approval2;
                $pos='Atasan Kedua';
            }else if($dt->approval3>0&&$dt->approval3_status==0){
                $id_employee=$dt->approval3;
                $pos='Atasan Ketiga';
            }else if($dt->approval4>0&&$dt->approval4_status==0){
                $id_employee=$dt->approval4;
                $pos='Atasan Keempat';
            }
            $qty=DB::table('tb_ksk_detail')->where('id_ksk',$id)->count();
            if($id_employee!=''){
                $data['kontak']=DB::table('tb_employee_detail')->where('id_employee',$id_employee)->value('nomor_telepon');
                $data['pesan']="*NOTIFIKASI KSK*\n\nID: *$id_ksk*\nDead Line: *$dead_line*\nDepartemen: *$dept*\nJumlah: *$qty orang*\n\nMenunggu Approval Anda sebagai *$pos*.\n\nSegera lakukan pengecekan via EMS, klik link berikut:\nhttps://ems.summitadyawinsa.co.id/EMS/Employee/KSK/Detail/$id/$periode";
            }
        }
        // $data['kontak']='08211212418';
        if($data['kontak']!=''){
            // \App\Http\Controllers\WhatsAppController::sendInternalMessage($data['kontak'], $data['pesan']);
            \App\Http\Controllers\WuzapiController::sendInternalMessage($data['kontak'], $data['pesan']);
            return 'Success';
        }else{
            return 'Failed';
        }
    }
    function update_performance($id_ksk){
        $tb1=DB::table('tb_ksk_detail')->where('id_ksk',$id_ksk)->get(['id','id_employee']);
        foreach($tb1 as $dt1){
            $tb2=DB::table('tb_performance as p')->leftjoin('tb_performance_recap as r','r.id_performance','=','p.id')->where('p.id_employee',$dt1->id_employee)->where('p.status_penilai_1','1')->orderby('p.periode','desc')->limit(1)->get(['r.ranked_bod']);
            $rank='';
            foreach($tb2 as $dt2){
                $rank=$dt2->ranked_bod;
            }
            if($rank==1)$grade='A+';
            elseif($rank==2)$grade='A';
            elseif($rank==3)$grade='B+';
            elseif($rank==4)$grade='B';
            elseif($rank==5)$grade='C+';
            elseif($rank==6)$grade='C';
            else $grade='';
            if($grade!=''){
                $update=DB::table('tb_ksk_detail')->where('id',$dt1->id)->update([
                    'performance'=>$grade,
                ]);
            }
        }
    }
}
