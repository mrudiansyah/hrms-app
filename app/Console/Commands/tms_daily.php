<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Http\Request;
use App\Models\tb_employee;
use App\Models\tb_presence;
use App\Models\tb_shift;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use DateTime;
use Auth;
use PDF;
use App\Http\Controllers\tms_controller;
use App\Mail\tms_share;
use App\Mail\tms_share_all;


class tms_daily extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sg:tms_daily';

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

        //Copy from iClock to EMS
            $tb1=DB::table('tb_iclock')->max('checktime');
            \Log::info("Last Update ".$tb1);
            $now=date('Y-m-d H:i:s');

            $tb_absen = DB::connection('fingerPrint')->table('checkinout')
            ->leftJoin('userinfo', 'checkinout.userid', '=', 'userinfo.userid')
            ->select(
                'checkinout.userid', 'userinfo.name','userinfo.badgenumber','checkinout.checktime','checkinout.checktype','checkinout.WorkCode','checkinout.sensorid'
            )
            ->where('checkinout.checktime', '>=', $tb1)
            ->where('checkinout.checktime', '<=', $now)
            ->orderby('checkinout.checktime','asc')
            ->get();
            if ($tb_absen->isNotEmpty()) {
                $dataToInsert = $tb_absen->map(function ($item) {
                    return (array) $item;
                })->toArray();
                DB::table('tb_iclock')->insert($dataToInsert);
            }
        //End Copy

        $periode=date('Y-m');
        // $periode='2026-09';
        $kalendar=CAL_GREGORIAN;
        $hari_awal=$periode.'-01';
        $thn=date('Y',strtotime($periode.'-01'));
        $bln=date('m',strtotime($periode.'-01'));
        $hariakhir=cal_days_in_month($kalendar,$bln,$thn);

        $dept='';
        $id_employee='';

        $spesific_day=(int)date('d');
        $all_day=0;

        $loop_start = ($all_day==0) ? $spesific_day : 1;
        $loop_end   = ($all_day==0) ? $spesific_day : $hariakhir;

        \Log::info("Start Update ".$periode);
        for($i=$loop_start;$i<=$loop_end;$i++){
            if(strlen($i)==1)$d='0'.$i;
            else $d=$i;
            $column='D'.$d;
            $Tgl=$periode."-".$d;
            \Log::info($Tgl);
            $tb_focus=DB::table('tb_work_entries')
            ->leftjoin('tb_work_contract','tb_work_contract.id_employee','=','tb_work_entries.id_employee')
            ->where('tb_work_contract.isactive','1')
            ->where('periode',$periode)
            ->where('plan_actual','actual')
            ->where(function ($query) use ($column){
                $query->where($column, '99')
                    ->orWhere($column, '98')
                    ->orWhere($column, '981')
                    ->orWhere($column, '982')
                    ->orWhere($column, '0')
                    ->orWhere($column, '71')
                    ->orWhere($column, '710')
                    ->orWhere($column, '51')
                    ->orWhere($column, '732')
                    ->orwhereNull($column);
            });
            if($dept!='') $tb_focus=$tb_focus->where('department',$dept);
            if($id_employee!='') $tb_focus=$tb_focus->where('tb_work_contract.id_employee',$id_employee);
            // ->where('id_employee','')
            $tb_focus=$tb_focus->orderby('department','asc')
            ->orderby('nama_karyawan','asc')
            ->get(['tb_work_entries.*','tb_work_contract.department','tb_work_contract.nama_karyawan']);
            $no=0;
            foreach($tb_focus as $dtf){
                $no++;
                \Log::info($Tgl." No.".$no);
                $tb_work_entries=DB::table('tb_work_entries')
                ->leftjoin('tb_work_contract','tb_work_contract.id_employee','=','tb_work_entries.id_employee')
                ->where('periode',$periode)
                ->where('plan_actual','plan')
                ->where('tb_work_entries.id_employee',$dtf->id_employee)
                ->orderby('tb_work_entries.id','asc')
                ->get(['tb_work_entries.*','tb_work_contract.PIN','tb_work_contract.NIK','tb_work_contract.department','tb_work_contract.nama_karyawan']);
                foreach($tb_work_entries as $dt){
                    $plan_work=$dt->$column;
                    $tb_work_time=DB::table('tb_work_time')->where('id',$plan_work)->get();
                    $work_status=99;
                    foreach($tb_work_time as $dt2){
                        $is_shift=$dt2->is_shift;
                        $advance=$dt2->is_advance;
                        if($dt2->is_advance==0)$tanggal=$Tgl;
                        else $tanggal=date('Y-m-d',strtotime('-1 days',strtotime($Tgl)));
                        if($plan_work>0){
                            $check_in=$tanggal.' '.$dt2->check_in;
                            $check_out=$Tgl.' '.$dt2->check_out;

                            //Return Checkin Range
                            if(isset($check_in)&&$check_in!=''&&$check_in!=$check_out){
                                $ncdatein=$check_in;
                                $ncdateindown= date('Y-m-d H:i:s',strtotime('-5 hours',strtotime($ncdatein)));
                                $ncdateinup= date('Y-m-d H:i:s',strtotime('+5 hours',strtotime($ncdatein)));
                            }else{
                                $ncdateindown='';
                                $ncdateinup='';
                            }
                            //Return Checkout Range
                            if(isset($check_out)&&$check_out!=''&&$check_in!=$check_out){
                                $ncdateout=$check_out;
                                $ncdateoutdown= date('Y-m-d H:i:s',strtotime('-2 hours',strtotime($ncdateout)));
                                $ncdateoutup= date('Y-m-d H:i:s',strtotime('+5 hours',strtotime($ncdateout)));
                            }else{
                                $ncdateoutdown='';
                                $ncdateoutup='';
                            }
                            //Read Finger Print
                            $PIN=$dt->PIN;
                            $NIK=$dt->NIK;

                            $lenbadge=strlen($PIN);
                            $nullbadge=9-$lenbadge;
                            $p='';
                            for($q=1;$q<=$nullbadge;$q++){
                                $p.='0';
                            }
                            $badge=$p.$PIN;
                            
                            //Jika Masuk Siang
                            if($ncdateindown>$now){
                                $work_status=98;
                            }
                            //Check Actual Finger
                                $tb1=DB::table('tb_iclock')->where('badgenumber',$badge)->where('checktime','>=',$ncdateindown)->where('checktime','<=',$ncdateinup)->orderby('checktime','asc')->limit(1)->get();
                                $qty_finger=0;
                                foreach($tb1 as $row){
                                    $actual=$row->checktime;
                                    if($dt2->is_shift==1){
                                        if($actual>=$ncdatein)$work_status='710';
                                        elseif($actual>0) $work_status='510';
                                    }else{
                                        if($actual>=$ncdatein)$work_status='71';
                                        elseif($actual>0) $work_status='51';
                                    }
                                    $qty_finger++;
                                }

                                $qty_finger2=0;
                                if($qty_finger==0){
                                    $tb1=DB::table('tb_iclock')->where('badgenumber',$badge)->where('checktime','>=',$ncdateoutdown)->where('checktime','<=',$ncdateoutup)->orderby('checktime','desc')->limit(1)->get();
                                    foreach($tb1 as $row){
                                        $actual=$row->checktime;
                                        if($dt2->is_shift==1){
                                            if($actual>=$ncdatein)$work_status='710';
                                            // elseif($actual>0) $work_status='510';
                                        }else{
                                            if($actual>=$ncdatein)$work_status='71';
                                            // elseif($actual>0) $work_status='51';
                                        }
                                    }
                                    $qty_finger2++;
                                }
                            //End Check Actual Finger
                            //Check Actual EMS (Manualcheck)
                                $masuk=DB::connection('mysql')->table('tb_checktimes')
                                ->where('NIK',$NIK)->where('checktime','>=',$ncdateindown)->where('checktime','<=',$ncdateinup)
                                ->orderby('checktime','desc')
                                ->get();
                                $Manual=0;
                                foreach($masuk as $row){
                                    if($dt2->is_shift==1){
                                        if($row->checktime>=$ncdatein)$work_status='720';
                                        elseif($plan_work>0) $work_status='520';
                                        $Manual=$row->checktime;
                                    }else{
                                        if($row->checktime>=$ncdatein)$work_status='72';
                                        elseif($plan_work>0) $work_status='52';
                                        $Manual=$row->checktime;
                                    }
                                }
                            //End Check Actual EMS (Manualcheck)
                        }

                    }
                    //Disini

                    $limit_date=DB::table('tb_utilities')->where('id','24')->where('status','1')->value('limit_date');
                    if($limit_date!=''&&$Tgl<=$limit_date)$lock_status=1;
                    else $lock_status=0;
                    if($lock_status==0){
                        $tb_work_entries=DB::table('tb_work_entries')->where('id_employee',$dt->id_employee)->where('periode',$periode)->where('plan_actual','actual')->update([
                            $column=>$work_status
                        ]);
                        if($tb_work_entries){
                            \Log::info("Start Update ".$Tgl." No.".$no." ".$dtf->department." ".$dtf->nama_karyawan." ".$column." ".$work_status);
                        }
                    }


                }

            }
        }
        \Log::info("Finish Update");

        //Record Capture
        $Today=date('Y-m-d');
        $add=DB::table('tb_log_capture')->insert([
            'capture_date'=>$Today
        ]);
    }
}
