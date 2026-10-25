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


class tms_manifest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sg:tms_manifest';

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
        \Log::info("Start Excecute");

        $tgl=date('Y-m-d');
        $manifest_calculation=1;
        if($manifest_calculation==1){

            \Log::info("Start Calculation Manifest");        
            //Manifest Calculation
            $nama='System';
            if($tgl==0)$tgl=date('Y-m-d');
            $periode=date('Y-m',strtotime($tgl));
            $now=date('Y-m-d H:i:s');
            $day = date('d',strtotime($tgl));
            $kolom = "D".$day;

            $tb1 = DB::connection('tms')->table('view_manifest')
            ->leftjoin('tb_work_code', 'view_manifest.' . $kolom, '=', 'tb_work_code.work_code')
            ->select(
                'view_manifest.id_employee',
                'view_manifest.NIK',
                'view_manifest.PIN',
                'view_manifest.employee_name',
                'view_manifest.dept_code',
                'view_manifest.periode', 
                'view_manifest.plant', 
                'view_manifest.area', 
                'view_manifest.ap', 
                'view_manifest.plan_actual',
                'view_manifest.' . $kolom . ' as kode_kerja_hari_ini',
                'tb_work_code.category',
                'tb_work_code.category_detail',
                'tb_work_code.source_check',
            )
            ->where('view_manifest.periode', $periode)
            ->get();
            
            foreach ($tb1 as $dt1) {
                $qty=DB::connection('tms')->table('tb_manifest')
                ->where('id_employee', $dt1->id_employee)
                ->where('tanggal', $tgl)
                ->count();
                if($dt1->category=='Abcent')$ijin=$dt1->source_check;
                else $ijin=null;
                if(str_contains($dt1->source_check, 'Upload')&&$dt1->dept_code!='HRGA')$tl=1;
                else $tl=null;
                if($qty==0){
                    $add=DB::connection('tms')->table('tb_manifest')->insert([
                        'periode' => $dt1->periode,
                        'tanggal' => $tgl,
                        'id_employee' => $dt1->id_employee,
                        'NIK'=>$dt1->NIK,
                        'PIN'=>$dt1->PIN,
                        'employee_name'=>$dt1->employee_name,
                        'dept_code'=>$dt1->dept_code,
                        'plant'=>$dt1->plant,
                        'area'=>$dt1->area,
                        'ap'=>$dt1->ap,
                        'work_code' => $dt1->kode_kerja_hari_ini,
                        'work_time' => null,
                        'category'=>$dt1->category,
                        'category_detail'=>$dt1->category_detail,
                        'source_check'=>$dt1->source_check,
                        'ijin'=>$ijin,
                        'tugas_luar'=>$tl,
                        'created_at' => $now,
                        'created_by' => $nama,
                    ]);
                }else{
                    $update=DB::connection('tms')->table('tb_manifest')->where('id_employee', $dt1->id_employee)->where('tanggal', $tgl)->update([
                        'periode' => $dt1->periode,
                        'tanggal' => $tgl,
                        'id_employee' => $dt1->id_employee,
                        'NIK'=>$dt1->NIK,
                        'PIN'=>$dt1->PIN,
                        'employee_name'=>$dt1->employee_name,
                        'dept_code'=>$dt1->dept_code,
                        'plant'=>$dt1->plant,
                        'area'=>$dt1->area,
                        'ap'=>$dt1->ap,
                        'work_code' => $dt1->kode_kerja_hari_ini,
                        'work_time' => null,
                        'category'=>$dt1->category,
                        'category_detail'=>$dt1->category_detail,
                        'source_check'=>$dt1->source_check,
                        'ijin'=>$ijin,
                        'tugas_luar'=>$tl,
                        'created_at' => $now,
                        'created_by' => $nama,
                    ]);
                } 

            }
            $tb2 = DB::connection('tms')->table('view_manifest_plan')
            ->join('tb_work_time', 'view_manifest_plan.' . $kolom, '=', 'tb_work_time.id')
            ->select(
                'view_manifest_plan.id_employee',
                'view_manifest_plan.periode', 
                'view_manifest_plan.' . $kolom . ' as shift_hari_ini',
                'tb_work_time.category',
                'tb_work_time.check_in',
                'tb_work_time.check_out',
                'tb_work_time.is_advance'
            )
            ->where('view_manifest_plan.periode', $periode)
            ->get();
            // return $tb2;
            foreach ($tb2 as $dt2) {
                $update=DB::connection('tms')->table('tb_manifest')->where('id_employee', $dt2->id_employee)->where('tanggal', $tgl)->update([
                    'work_time' => $dt2->shift_hari_ini,
                    'shift' => $dt2->category,
                    'check_in' => $dt2->check_in,
                    'check_out' => $dt2->check_out,
                    'is_advance' => $dt2->is_advance,
                ]);
            }

            //manifest Update Finger
            \Log::info("Start Update Finger Manifest");        
            $tb1=DB::connection('tms')->table('tb_manifest')->where('tanggal', $tgl)->get();
            foreach ($tb1 as $dt1) {
                if($dt1->work_time>0){
                    if($dt1->is_advance==0)$tanggal=$tgl;
                    else $tanggal=date('Y-m-d',strtotime('-1 days',strtotime($tgl)));
                    $check_in=$tanggal.' '.$dt1->check_in;
                    $check_out=$tgl.' '.$dt1->check_out;

                    //Return Checkin Range
                    $ncdatein=$check_in;
                    $ncdateindown= date('Y-m-d H:i:s',strtotime('-5 hours',strtotime($ncdatein)));
                    $ncdateinup= date('Y-m-d H:i:s',strtotime('+5 hours',strtotime($ncdatein)));

                    //Return Checkout Range
                    $ncdateout=$check_out;
                    $ncdateoutdown= date('Y-m-d H:i:s',strtotime('-2 hours',strtotime($ncdateout)));
                    $ncdateoutup= date('Y-m-d H:i:s',strtotime('+5 hours',strtotime($ncdateout)));

                    //Read Finger Print
                    $actual_in=null;
                    $actual_out=null;
                    $PIN=$dt1->PIN;
                    $lenbadge=strlen($PIN);
                    $nullbadge=9-$lenbadge;
                    $p='';
                    for($q=1;$q<=$nullbadge;$q++){
                        $p.='0';
                    }
                    $badge=$p.$PIN;

                    $tb1=DB::connection('tms')->table('tb_iclock')->where('badgenumber',$badge)->where('checktime','>=',$ncdateindown)->where('checktime','<=',$ncdateinup)->orderby('checktime','asc')->limit(1)->get();
                    foreach($tb1 as $row){
                        $actual_in=$row->checktime;
                    }

                    $tb2=DB::connection('tms')->table('tb_iclock')->where('badgenumber',$badge)->where('checktime','>=',$ncdateoutdown)->where('checktime','<=',$ncdateoutup)->orderby('checktime','desc')->limit(1)->get();
                    foreach($tb2 as $row){
                        $actual_out=$row->checktime;
                    }

                    //Update Manifest
                    $update=DB::connection('tms')->table('tb_manifest')->where('id_employee', $dt1->id_employee)->where('tanggal', $tgl)->update([
                        'masuk' => $actual_in,
                        'keluar' => $actual_out,
                    ]);

                }
                
            }

        }

    }
}
