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


class presenceUpdate_Daily extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sg:presenceUpdate_Daily';

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
        $host = mysqli_connect("192.168.121.4:83306","cahyudin","123456","adms_db");

        $admin='System';
        date_default_timezone_set("Asia/Jakarta");
        $kalendar=CAL_GREGORIAN;
        $sekarang=date('Y-m-d H:i:s');
        $thn_sekarang=date('Y');
        $bln_sekarang=date('m');
        $tgl_akhirbulan=date('Y-m-d',strtotime('-1 days',strtotime($thn_sekarang.'-'.$bln_sekarang.'-01')));
        $periode=date('Y-m',strtotime($tgl_akhirbulan));
        $thn=date('Y',strtotime($periode.'-01'));
        $bln=date('m',strtotime($periode.'-01'));
        $hariakhir=cal_days_in_month($kalendar,$bln,$thn);
        $Tglawal=date('Y-m-d',strtotime($thn.'-'.$bln.'-01'));
        $Tglakhir=date('Y-m-d',strtotime($thn.'-'.$bln.'-'.$hariakhir));
        
        $tgl_sekarang=date('d');
        if($tgl_sekarang==1 || ($tgl_sekarang>=6 && $tgl_sekarang<=22)){

            \Log::info('Start Summaries Presence');

            //Entry New Data tb_absensi_rate
                $tb_employee=DB::table('tb_employees')
                ->leftjoin('tb_departments','tb_departments.id','tb_employees.dept_id')
                ->leftjoin('tb_employee_shifts','tb_employee_shifts.id_employee','=','tb_employees.id')
                ->leftjoin('tb_group_shifts','tb_group_shifts.id','=','tb_employee_shifts.id_shift')
                ->where('tb_employees.status','1')
                ->get(['tb_employees.*','tb_departments.dept_code','tb_departments.dept_name','tb_group_shifts.hari_kerja']);        
                foreach ($tb_employee as $dt) {
                    $check=DB::connection('emsAbsensi')->table('tb_absensi_rate')->where('periode',$periode)->where('id_employee',$dt->id)->count();
                    if($check==0){
                        DB::connection('emsAbsensi')->table('tb_absensi_rate')->insert([
                            'periode'=>$periode,
                            'hari_kerja'=>$dt->hari_kerja,
                            'dept_id'=>$dt->dept_id,
                            'id_employee'=>$dt->id,
                            'NIK'=>$dt->NIK,
                            'employee_name'=>$dt->employee_name,
                            'divisi'=>$dt->dept_name,
                            'start'=>$Tglawal,
                            'end'=>$Tglakhir,
                            'terlambat'=>'0',
                            'terlambat_minutes'=>'0',
                            'setengah_minutes'=>'0',
                            'keluar_minutes'=>'0',
                            'cuti'=>'0',
                            'sakit'=>'0',
                            'izin'=>'0',
                            'alpa'=>'0',
                            'present_plan'=>'0',
                            'present_actual'=>'0',
                            'present_rate'=>'0',
                            'hour_plan'=>'0',
                            'hour_actual'=>'0',
                            'hour_rate'=>'0',
                            'absent'=>'0',
                            'admin'=>$admin,
                            'status'=>'0',
                            'created_at'=>$sekarang,
                            'updated_at'=>$sekarang
                        ]);
                    }
                }
                \Log::info('Created tb_absensi_rate ');
            // End New Detail
            // Start New Summary 
                $tb_department=DB::table('tb_departments')
                ->leftjoin('tb_departments_shift','tb_departments_shift.dept_id','=','tb_departments.id')
                ->where('isTrial','1')->get(['tb_departments.*','tb_departments_shift.hari_kerja']);        
                foreach ($tb_department as $dt) {
                    $check=DB::connection('emsAbsensi')->table('tb_absensi_rate_kumulatif')->where('periode',$periode)->where('dept_id',$dt->id)->count();
                    if($check==0){
                        DB::connection('emsAbsensi')->table('tb_absensi_rate_kumulatif')->insert([
                            'periode'=>$periode,
                            'hari_kerja'=>$dt->hari_kerja,
                            'dept_id'=>$dt->id,
                            'divisi'=>$dt->dept_name,
                            'start'=>$Tglawal,
                            'end'=>$Tglakhir,
                            'total_absen'=>'0',
                            'present_plan'=>'0',
                            'total_employee'=>'0',
                            'absensi_rate'=>'0',
                            'parameter'=>'0',
                            'kriteria'=>'0',
                            'admin'=>$admin,
                            'status'=>'0',
                            'created_at'=>$sekarang,
                            'updated_at'=>$sekarang
                        ]);
                    }
                }            
                \Log::info('Created tb_absensi_rate_kumulatif ');
            // End New Summary 
            $tb_absen=DB::connection('emsAbsensi')->table('tb_absensi_rate')->where('periode',$periode)->orderby('dept_id','asc')->get();
            $dept_id='';
            foreach($tb_absen as $dt_absen){
                if($dept_id!=$dt_absen->dept_id)\Log::info('Start Update Dept: '.$dt_absen->dept_id);

                $id_employee=$dt_absen->id_employee;
                $tb_employee_shift=DB::table('tb_employee_shifts')->leftjoin('tb_employees','tb_employees.id','=','tb_employee_shifts.id_employee')->where('tb_employees.id',$id_employee)->get(['tb_employee_shifts.*','tb_employees.PIN']);
                foreach($tb_employee_shift as $row){

                    $userid=$row->id_employee;
                    $id_shift=$row->id_shift;
                    $PIN=$row->PIN;

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
                    $alpa=0;
                    $hoursplan=0;
                    $hoursact=0;
                    $hoursrate=0;
                    $totalabsen=0;

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

                                        $tb_checktime_record=DB::table('tb_checktimes')->where('PIN',$PIN)->where('checktime','>=',$ncdateindown)->where('checktime','<=',$ncdateoutup)->count();

                                        //$tb_freeday=tb_employee_freeday::where([['id_employee',$userid],['date_off',$Tgl]])->count();

                                        $tb_freeday=DB::table('tb_freedays')->where('date_off',$Tgl)->count();
                                        $tb_changeday=tb_changeday::where([['id_employee',$userid],['date_off',$Tgl]])->count();
                                        if($tb_freeday>0||$tb_changeday>0){
                                            $off_status='1';
                                        }else{
                                            $i++;
    
                                            if($in_status=='1'||$out_status=='1'||$tb_checktime_record>0)$present++;
                                            else{
                                                if(strlen($day)==1)$dj='0'.$day;
                                                else $dj=$day;
                                                if($off_status=='0'){
                                                    $offDays=$offDays.$dj.' ';
                                                }
                                            }
        
                                        }
    
                                    }
                                }
                            
                                $cek_cuti=DB::table('tb_absencies')->where('category','LEAVE')->where('date_off',$Tgl)->where('id_employee',$userid)->count();
                                $cuti=$cuti+$cek_cuti;
                                $present=$present+$cek_cuti;
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
                                $cek_sakit=DB::table('tb_absencies')->where('category','SAKIT')->where('date_off',$Tgl)->where('id_employee',$userid)->count();
                                $sakit=$sakit+$cek_sakit;
                                $alpa=$i-$present-$izin-$sakit;
                                $hoursplan=$i*8;
                                $hoursact=$hoursplan-($menit/60)-($setengah*4)-($izin*8)-($sakit*8);
                                if($i==0)$hoursrate=0;
                                else $hoursrate=number_format($hoursact/$hoursplan*100,'2');
                                $totalabsen=$izin+$sakit+$alpa;
                            
                            //End Insert Old Code

                            }
                            $sekarang=date('Y-m-d H:i:s');
                            $satu=date('Ym');
                            $dua=date('Ym',strtotime($Tgl));
                            if($satu>$dua)$status=1;
                            else $status=0;
                            DB::connection('emsAbsensi')->table('tb_absensi_rate')->where('id_employee',$userid)->where('periode',$periode)->update([
                                'terlambat'=>$delay_fr,
                                'terlambat_minutes'=>$delay_minutes,
                                'setengah_minutes'=>$setengah,
                                'keluar_minutes'=>$menit,
                                'cuti'=>$cuti,
                                'sakit'=>$sakit,
                                'izin'=>$izin,
                                'alpa'=>$alpa,
                                'present_plan'=>$i,
                                'present_actual'=>$present,
                                'present_rate'=>$presence,
                                'hour_plan'=>$hoursplan,
                                'hour_actual'=>$hoursact,
                                'hour_rate'=>$hoursrate,
                                'absent'=>$totalabsen,
                                'status'=>$status,
                                'updated_at'=>$sekarang
                            ]);
                        }

                }

            }
            
            \Log::info('Start Update Absensi Rate Kumulatif');

            $tb_kumulatif= DB::connection('emsAbsensi')->table('tb_absensi_rate_kumulatif')->where('periode',$periode)->get();        
            foreach ($tb_kumulatif as $dt) {
                $jumlah_absen=0;
                $plan=0;
                $jumlah_employee=0;
                $tb_rate=DB::connection('emsAbsensi')->table('tb_absensi_rate')->where('periode',$periode)->where('dept_id',$dt->dept_id)->where('hari_kerja',$dt->hari_kerja)->where('present_plan','>','0')->get();
                foreach($tb_rate as $dt_rate){
                    $jumlah_absen=$jumlah_absen+$dt_rate->absent;
                    if($dt_rate->present_plan>0)$plan=$dt_rate->present_plan;
                    if($plan>$dt_rate->present_plan&&$dt_rate->present_plan>0)$plan=$dt_rate->present_plan;
                    $jumlah_employee++;
                }
                if($plan* $jumlah_employee==0)$absensi_rate=0;
                else $absensi_rate=number_format($jumlah_absen/($plan* $jumlah_employee)*100,2);
                $paramater=0.76;
                if($absensi_rate<=$paramater)$kriteria='Baik';
                else $kriteria='Buruk';
                $sekarang=date('Y-m-d H:i:s');
                $satu=date('Ym');
                $dua=date('Ym',strtotime($periode.'-01'));
                if($satu>$dua)$status=1;
                else $status=0;
                DB::connection('emsAbsensi')->table('tb_absensi_rate_kumulatif')->where('dept_id',$dt->dept_id)->where('periode',$periode)->where('hari_kerja',$dt->hari_kerja)->update([
                    'total_absen'=>$jumlah_absen,
                    'present_plan'=>$plan,
                    'total_employee'=>$jumlah_employee,
                    'absensi_rate'=>$absensi_rate,
                    'parameter'=>$paramater,
                    'kriteria'=>$kriteria,
                    'admin'=>$admin,
                    'status'=>$status,
                    'updated_at'=>$sekarang
                ]);
                \Log::info('Updated Dept '.$dt->dept_id);
            }            

            \Log::info('Finish Summaries Presence');
        }
        else \Log::info('Skip Update Summaries Presence');

    }
}
