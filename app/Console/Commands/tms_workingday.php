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


class tms_workingday extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sg:tms_workingday';

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
        //$periode='2026-09';
        $periode=date('Y-m');
        $kalendar=CAL_GREGORIAN;
        $hari_awal=$periode.'-01';
        $thn=date('Y',strtotime($periode.'-01'));
        $bln=date('m',strtotime($periode.'-01'));
        $hariakhir=cal_days_in_month($kalendar,$bln,$thn);
        $hari_akhir=$periode.'-'.$hariakhir;

        $dept='';
        $id_employee='';

        \Log::info("Start Update ".$periode);
        $tb1=DB::connection('tms')->table('tb_work_entries as we')->leftjoin('tb_work_contract as wc','wc.id_employee','=','we.id_employee')
        ->where('we.periode',$periode)->where('we.plan_actual','plan');
        if($dept!='')$tb1=$tb1->where('wc.department',$dept);
        if($id_employee!='')$tb1=$tb1->where('wc.id_employee',$id_employee);
        $tb1=$tb1->orderby('wc.department','asc')->orderby('wc.nama_karyawan','asc')->get(['we.*','wc.nama_karyawan']);
        $wd=0;
        $no=0;
        foreach($tb1 as $dt1){
            $tb2=DB::table('tb_statuses')->where('id_employee',$dt1->id_employee)->where('contract_name','End Contract')->where('active','1')->get();
            $tb2 = DB::table('tb_statuses')
                ->where('id_employee', $dt1->id_employee)
                ->where('start_contract','>=',$hari_awal)
                ->where(function ($query) {
                    $query->where('contract_name', 'End Contract')
                        ->orWhere('contract_name', 'Resign')
                        ->orWhere('contract_name', 'Kabur')
                        ->orWhere('contract_name', 'Pensiun Dini')
                        ->orWhere('contract_name', 'Pensiun');
                })
                ->where('active', '1')
                ->get();
                            
            foreach($tb2 as $dt2){
                $start=$dt2->join_date;
                $end=$dt2->start_contract;
                if($start>$hari_awal||$dt2->contract_name=='Resign'||$dt2->contract_name=='End Contract'||$dt2->contract_name=='Kabur'||$dt2->contract_name=='Resign'||$dt2->contract_name=='Pensiun'||$dt2->contract_name=='Pensiun Dini'){

                    // \Log::info($no.' Masuk 00');
                    if($start>$hari_awal){
                        // \Log::info($no.' Masuk 01');
                        $day=date('d',strtotime($start))*1;
                        for($i=1;$i<=31;$i++){
                            if(strlen($i)==1)$j='0'.$i;
                            else $j=$i;
                            $column='D'.$j;
                            if($i<$day){
                                $update=DB::connection('tms')->table('tb_work_entries')->where('id_employee',$dt1->id_employee)->where('periode',$dt1->periode)->update([$column=>97]);
                                // \Log::info($no.' A');
                            }
                        }
                    }else{
                        // \Log::info($no.' Masuk 02');
                        $day=date('d',strtotime($end))*1;
                        for($i=1;$i<=31;$i++){
                            if(strlen($i)==1)$j='0'.$i;
                            else $j=$i;
                            $column='D'.$j;
                            if($i>$day){
                                $update=DB::connection('tms')->table('tb_work_entries')->where('id_employee',$dt1->id_employee)->where('periode',$dt1->periode)->update([$column=>97]);
                                // \Log::info($no.' B');
                            }
                        }

                    }
                }else{
                    for($i=1;$i<=31;$i++){
                        if(strlen($i)==1)$j='0'.$i;
                        else $j=$i;
                        $column='D'.$j;
                        if($dt1->$column>0)$wd++;
                    }
                }
                $no++;
                \Log::info($no.' '.$dt1->nama_karyawan);
            }
        }
        \Log::info("Finish Update");
    }
}
