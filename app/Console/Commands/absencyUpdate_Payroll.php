<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Http\Request;
use App\Models\tb_employee;
use App\Models\tb_presence;
use App\Models\tb_shift;
use Illuminate\Support\Facades\DB;
use DateTime;
use App\Models\tb_group;
use App\Models\tb_group_shift;
use App\Models\tb_cycle;
use App\Models\tb_freeday;
use App\Models\tb_employee_freeday;
use App\Models\tb_employee_leave;
use App\Models\tb_changeday;
use App\Models\tb_overtime;
use App\Models\tb_payroll;
use App\Models\tb_cutoff;
use App\Models\tb_sumpresence;
use App\Models\tb_department;
use App\Models\tb_position;
use App\Models\tb_address;
use App\Models\tb_employee_shift;
use App\Models\tb_education;
use App\Models\tb_experience;
use App\Models\tb_skill;
use App\Models\tb_overtime_detail;
use App\Models\tb_approval;
use App\Models\tb_email;
use App\Models\tb_subdept;
use App\Models\tb_admin;
use App\Models\tb_meal;
use Auth;
use PDF;


class absencyUpdate_Payroll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sg:absencyUpdate_Payroll';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        \Log::info('Start');

        $admin='System';

        date_default_timezone_set("Asia/Jakarta");
        $kalendar=CAL_GREGORIAN;
        $sekarang=date('Y-m-d H:i:s');
        //$Tgl=date('Y-m-d',strtotime('-1 months',strtotime($sekarang)));
        $Tgl=date('Y-m-d');
        $Tglsaiki=date('d');
        $periode=date('Y-m',strtotime($Tgl));

        $data_a=explode('-',$periode);
        $thn2=$data_a[0];
        $bln2=$data_a[1];

        $Tglakhir2=date('Y-m-d',strtotime($thn2.'-'.$bln2.'-24'));
        $Tglawal2=date('Y-m-d',strtotime($thn2.'-'.$bln2.'-01'));
        $Tglakhir1=date('Y-m-d',strtotime('-1 days',strtotime($Tglawal2)));
        $thn1=date('Y',strtotime($Tglakhir1));
        $bln1=date('m',strtotime($Tglakhir1));
        $Tglawal1=date('Y-m-d',strtotime($thn1.'-'.$bln1.'-25'));
        $hariakhir=date('d',strtotime($Tglakhir1));
        //return $Tglakhir1;

        //Entry New Data tb_summary_absency
        $check=DB::connection('emsAbsensi')->table('tb_absensi_payroll')->where('periode',$periode)->count();
        //return  $check;       
        if($check==0){
            \Log::info('Start to create tb_absensi_payroll');
            $tb_employee=DB::table('tb_employees')
            ->leftjoin('tb_departments','tb_departments.id','tb_employees.dept_id')
            ->where('status','1')
            ->get(['tb_employees.*','tb_departments.dept_code','tb_departments.dept_name']); 
            foreach ($tb_employee as $dt) {
                DB::connection('emsAbsensi')->table('tb_absensi_payroll')->insert([
                    'periode'=>$periode,
                    'dept_id'=>$dt->dept_id,
                    'id_employee'=>$dt->id,
                    'NIK'=>$dt->NIK,
                    'employee_name'=>$dt->employee_name,
                    'divisi'=>$dt->dept_name,
                    'start'=>$Tglawal1,
                    'end'=>$Tglakhir1,
                    'izin'=>'0',
                    'alpa'=>'0',
                    'present_plan'=>'0',
                    'present_actual'=>'0',
                    'present_rate'=>'0',
                    'absent'=>'0',
                    'salary'=>'0',
                    'upah_pokok'=>'0',
                    'tunjangan_jabatan'=>'0',
                    'tunjangan_skill'=>'0',
                    'tunjangan_prestasi'=>'0',
                    'admin'=>$admin,
                    'status'=>'0',
                    'created_at'=>$sekarang,
                    'updated_at'=>$sekarang
                ]);
                DB::connection('emsAbsensi')->table('tb_absensi_payroll')->insert([
                    'periode'=>$periode,
                    'dept_id'=>$dt->dept_id,
                    'id_employee'=>$dt->id,
                    'NIK'=>$dt->NIK,
                    'employee_name'=>$dt->employee_name,
                    'divisi'=>$dt->dept_name,
                    'start'=>$Tglawal2,
                    'end'=>$Tglakhir2,
                    'izin'=>'0',
                    'alpa'=>'0',
                    'present_plan'=>'0',
                    'present_actual'=>'0',
                    'present_rate'=>'0',
                    'absent'=>'0',
                    'salary'=>'0',
                    'upah_pokok'=>'0',
                    'tunjangan_jabatan'=>'0',
                    'tunjangan_skill'=>'0',
                    'tunjangan_prestasi'=>'0',
                    'admin'=>$admin,
                    'status'=>'0',
                    'created_at'=>$sekarang,
                    'updated_at'=>$sekarang
                ]);
            }
            \Log::info('Finish Created tb_absensi_payroll');
        }

        $host = mysqli_connect("192.168.121.4:83306","cahyudin","123456","adms_db");

        if($Tglsaiki>=1&&$Tglsaiki<25){
            $tb_absen=DB::connection('emsAbsensi')->table('tb_absensi_payroll')->where('start',$Tglawal1)->where('end',$Tglakhir1)->orderby('id_employee','asc')->get();
            \Log::info('Start to update tb_absensi_payroll 25 ~ 31');
            $counter=0;
            foreach($tb_absen as $dt_absen){
                $id_employee=$dt_absen->id_employee;
                \Log::info('Start to update tb_absensi_payroll #1 '.$id_employee);
                //$tb_employee_shift=DB::table('tb_employee_shifts')->leftjoin('tb_employees','tb_employees.id','=','tb_employee_shifts.id_employee')->where('tb_employees.id',$id)->get(['tb_employee_shifts.*','tb_employees.PIN']);
                $tb_employee_shift=DB::table('tb_employee_shifts')->leftjoin('tb_employees','tb_employees.id','=','tb_employee_shifts.id_employee')->where('tb_employees.id',$id_employee)->get(['tb_employee_shifts.*','tb_employees.PIN','tb_employees.NIK']);
                foreach($tb_employee_shift as $row){
    
                    $userid=$row->id_employee;
                    $id_shift=$row->id_shift;
                    $PIN=$row->PIN;
                    $NIK=$row->NIK;
                    //echo '<br>#1 '.$userid.': ';
    
                    //Update Periode 25 - akhir Bulan
                    $i=0;
                    $present=0;
                    $presence=0;
                    $offDays='';
                    $delay_fr='0';
                    $delay_minutes='0';
    
                    $izin=0;
                    $setengah=0;
                    $menit=0;
                    $sakit=0;
                    $cuti=0;
    
                    $wfhDays=0;
    
                    for($day=$hariakhir;$day>=25;$day--){
                        $Tgl=date('Y-m-d',strtotime($thn1.'-'.$bln1.'-'.$day));
    
                        $Today=date('Y-m-d');
                        $Months=date('Y-m',strtotime($thn1.'-'.$bln1.'-'.$day));
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
    
                                    //$tb_freeday=tb_employee_freeday::where([['id_employee',$userid],['date_off',$Tgl]])->count();
                                    $tb_freeday=DB::table('tb_freedays')->where('date_off',$Tgl)->count();
                                    $tb_changeday=tb_changeday::where([['id_employee',$userid],['date_off',$Tgl]])->count();
                                    
                                    $tb_manual=DB::table('tb_checktimes')->where('NIK',$NIK)->where('checktime','>=',$ncdateindown)->where('checktime','<=',$ncdateoutup)->get();foreach($tb_manual as $dt_manual){if($dt_manual->status_kerja=='TL'||$dt_manual->status_kerja=='WFO'||$dt_manual->status_kerja=='DRIVER')$in_status='1';if($dt_manual->status_kerja=='WFH')$wfhDays++;}
    
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
    
                            $ayeuna=date('Y-m');
                            if($periode<$ayeuna)$remark=1;
                            else $remark=0;
    
                            $tb_izin=DB::table('tb_izins')->where('id_employee',$userid)->where('apply_date',$Tgl)->get();
                            foreach($tb_izin as $dt_izin){
                                if($dt_izin->category=='A')$izin=$izin+1;
                                if($dt_izin->category=='B')$setengah=$setengah+1;
                                if($dt_izin->minutes>0)$menit=$menit+$dt_izin->minutes;
                            }
                            $cek_cuti=DB::table('tb_absencies')->where('category','LEAVE')->where('date_off',$Tgl)->where('id_employee',$userid)->count();
                            $cuti=$cuti+$cek_cuti;
                            $cek_sakit=DB::table('tb_absencies')->where('category','SAKIT')->where('date_off',$Tgl)->where('id_employee',$userid)->count();
                            $sakit=$sakit+$cek_sakit;
                            $alpa=$i-$present-$izin-$sakit-$cuti;
                            $hoursplan=$i*8;
                            $hoursact=$hoursplan-($menit/60)-($setengah*4)-($izin*8)-($sakit*8);
                            if($i==0)$hoursrate=0;
                            else $hoursrate=number_format($hoursact/$hoursplan*100,'2');
                            $totalabsen=$izin+$alpa;
                        
                        //End Insert Old Code
    
                        }
                        if($i>0){
                            $tb_salaries=DB::table('tb_salaries')->where('id_employee',$userid)->where('implement','<=',$Tglawal1)->orderby('implement','desc')->limit(1)->get();
                            foreach($tb_salaries as $row_salaries){
                                $salary=$row_salaries->salary;
                                $wfh_ammount=$wfhDays*$salary;
                                //$salary_present=$present*$salary;
                                $absent_ammount=$totalabsen/21*$salary;
                                $update=DB::connection('emsAbsensi')->table('tb_absensi_payroll')->where('id_employee',$userid)->where('start',$Tglawal1)->where('end',$Tglakhir1)->update([
                                    'izin'=>$izin,
                                    'alpa'=>$alpa,
                                    'present_plan'=>$i,
                                    'present_actual'=>$present,
                                    'present_rate'=>$presence,
                                    'absent'=>$totalabsen,
                                    'salary'=>$salary,
                                    'upah_pokok'=>$row_salaries->upah_pokok,
                                    'tunjangan_jabatan'=>$row_salaries->tunjangan_jabatan,
                                    'tunjangan_skill'=>$row_salaries->tunjangan_skill,
                                    'tunjangan_prestasi'=>$row_salaries->tunjangan_prestasi,
                                    'total_plan'=>'0',
                                    'absent_ammount'=>$absent_ammount,
                                    'wfh'=>$wfhDays,
                                    'wfh_ammount'=>$wfh_ammount,
                                    'status'=>$remark,
                                    'updated_at'=>$sekarang
                                ]);
                            }
                            if($update)$counter++;
                        }
                    }
    
                    //Update Periode Awal Bulan - 24
                    
                }
    
            }
            if($counter>0)\Log::info('Finish Update tb_absensi_payroll 25 ~ 31');
        }
        if($Tglsaiki>=25){
            $tb_absen2=DB::connection('emsAbsensi')->table('tb_absensi_payroll')->where('start',$Tglawal2)->where('end',$Tglakhir2)->orderby('id_employee','asc')->get();
            \Log::info('Start to update tb_absensi_payroll 1 ~ 24');
            $counter=0;
            foreach($tb_absen2 as $dt_absen){
                $id_employee=$dt_absen->id_employee;
                \Log::info('Start to update tb_absensi_payroll #2 '.$id_employee);
                //$tb_employee_shift=DB::table('tb_employee_shifts')->leftjoin('tb_employees','tb_employees.id','=','tb_employee_shifts.id_employee')->where('tb_employees.id',$id)->get(['tb_employee_shifts.*','tb_employees.PIN']);
                $tb_employee_shift=DB::table('tb_employee_shifts')->leftjoin('tb_employees','tb_employees.id','=','tb_employee_shifts.id_employee')->where('tb_employees.id',$id_employee)->get(['tb_employee_shifts.*','tb_employees.PIN','tb_employees.position_id']);
                foreach($tb_employee_shift as $row){

                    $userid=$row->id_employee;
                    $id_shift=$row->id_shift;
                    $PIN=$row->PIN;
                    //echo '<br>#1 '.$userid.': ';

                    //Update Periode 25 - akhir Bulan
                    $i=0;
                    $present=0;
                    $presence=0;
                    $offDays='';
                    $delay_fr='0';
                    $delay_minutes='0';

                    $izin=0;
                    $setengah=0;
                    $menit=0;
                    $sakit=0;
                    $cuti=0;

                    $wfhDays=0;

                    for($day=24;$day>=1;$day--){
                        $Tgl=date('Y-m-d',strtotime($thn2.'-'.$bln2.'-'.$day));
                        //\Log::info('Start to update tb_absensi_payroll #2 '.$Tgl);

                        $Today=date('Y-m-d');
                        $Months=date('Y-m',strtotime($thn2.'-'.$bln2.'-'.$day));
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
                                    
                                    $tb_manual=DB::table('tb_checktimes')->where('NIK',$NIK)->where('checktime','>=',$ncdateindown)->where('checktime','<=',$ncdateoutup)->get();foreach($tb_manual as $dt_manual){if($dt_manual->status_kerja=='TL'||$dt_manual->status_kerja=='WFO'||$dt_manual->status_kerja=='DRIVER')$in_status='1';if($dt_manual->status_kerja=='WFH')$wfhDays++;}
                                    
                                    $i++;
                                    if($tb_freeday>0||$tb_changeday>0){
                                        $i--;
                                        $off_status='1';
                                    }

                                    if($in_status=='1'||$out_status=='1'){
                                        $present++;
                                    }
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

                            $ayeuna=date('Y-m');
                            if($periode<$ayeuna)$remark=1;
                            else $remark=0;

                            $tb_izin=DB::table('tb_izins')->where('id_employee',$userid)->where('apply_date',$Tgl)->get();
                            foreach($tb_izin as $dt_izin){
                                if($dt_izin->category=='A')$izin=$izin+1;
                                if($dt_izin->category=='B')$setengah=$setengah+1;
                                if($dt_izin->minutes>0)$menit=$menit+$dt_izin->minutes;
                            }
                            $cek_cuti=DB::table('tb_absencies')->where('category','LEAVE')->where('date_off',$Tgl)->where('id_employee',$userid)->count();
                            $cuti=$cuti+$cek_cuti;
                            $cek_sakit=DB::table('tb_absencies')->where('category','SAKIT')->where('date_off',$Tgl)->where('id_employee',$userid)->count();
                            $sakit=$sakit+$cek_sakit;
                            $alpa=$i-$present-$izin-$sakit-$cuti;
                            $hoursplan=$i*8;
                            $hoursact=$hoursplan-($menit/60)-($setengah*4)-($izin*8)-($sakit*8);
                            if($i==0)$hoursrate=0;
                            else $hoursrate=number_format($hoursact/$hoursplan*100,'2');
                            $totalabsen=$izin+$alpa;
                        
                        //End Insert Old Code

                        }
                        if($i>0){
                            $tb_salaries=DB::table('tb_salaries')->where('id_employee',$userid)->where('implement','<=',$Tglawal2)->orderby('implement','desc')->limit(1)->get();
                            foreach($tb_salaries as $row_salaries){
                                $salary=$row_salaries->salary;
                                $wfh_ammount=$wfhDays*$salary;
                                //$salary_present=$present/$i*$salary;
                                $absent_ammount=$totalabsen/21*$salary;
                                $update=DB::connection('emsAbsensi')->table('tb_absensi_payroll')->where('id_employee',$userid)->where('start',$Tglawal2)->where('end',$Tglakhir2)->update([
                                    'izin'=>$izin,
                                    'alpa'=>$alpa,
                                    'present_plan'=>$i,
                                    'present_actual'=>$present,
                                    'present_rate'=>$presence,
                                    'absent'=>$totalabsen,
                                    'salary'=>$salary,
                                    'upah_pokok'=>$row_salaries->upah_pokok,
                                    'tunjangan_jabatan'=>$row_salaries->tunjangan_jabatan,
                                    'tunjangan_skill'=>$row_salaries->tunjangan_skill,
                                    'tunjangan_prestasi'=>$row_salaries->tunjangan_prestasi,
                                    'total_plan'=>'0',
                                    'absent_ammount'=>$absent_ammount,
                                    'wfh'=>$wfhDays,
                                    'wfh_ammount'=>$wfh_ammount,
                                    'status'=>$remark,
                                    'updated_at'=>$sekarang
                                ]);
                            }
                            if($update)$counter++;
                        }
                    }

                    
                }

            }
        if($counter>0)\Log::info('Update tb_absensi_payroll 1 s/d 24');
        }

        \Log::info('Finish');


    }
}
