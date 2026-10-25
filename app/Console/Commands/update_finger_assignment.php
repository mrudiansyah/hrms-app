<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use DateTime;
use Auth;
use PDF;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\ApiEmailController;


class update_finger_assignment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sg:update_finger_assignment';

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
    public function handle(){
        \Log::info("Update Finger Assignment Start");
        $calculation_assignment=1;
        $now=date('Y-m-d H:i:s');
        if($calculation_assignment==1){
            \Log::info("Update Assignment");

            $tb_assigment_new=DB::table('tb_overtime_spv')
            ->leftjoin('tb_employees','tb_employees.id','=','tb_overtime_spv.id_employee')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
            ->where('tb_overtime_spv.isDelete','0')
            // ->where('conducted_status','0')
            ->where('hours_act','0')
            //->whereNull('out_finger')
            // ->where('id_employee','111')
            ->where(function($query) {
                $query->where('approved2_status', '1')
                ->orWhere(function($q) {
                    $q->whereNull('approved2')
                    ->where('approved1_status', '1');
                })
                ->orWhere(function($q) {
                    $q->whereNull('approved2')
                    ->whereNull('approved1')
                    ->where('assigned_status', '1');
                });        
            })
            ->get(['tb_overtime_spv.*','tb_employees.NIK','tb_employees.employee_name','tb_employees.PIN','tb_employees.badgenumber','tb_departments.dept_code']);
            $no=0;
            //\Log::info($tb_assigment_new);
            foreach($tb_assigment_new as $dt){
                
                $no++;
                \Log::info("Update Assignment ".$no);
                $checkin_act = '';
                $checkout_act = '';
                $lenbadge = strlen($dt->badgenumber);
                $nullbadge = 9 - $lenbadge;
                $j = '';
                for ($i = 1; $i <= $nullbadge; $i++) {
                    $j .= '0';
                }
                $badge = $j . $dt->badgenumber;
                
                $ncdatein = $dt->start_act;
                //Reduce 2 Hour
                $date = date_create($ncdatein);
                date_add($date, date_interval_create_from_date_string('-10 hours'));
                $ncdateindown = date_format($date, 'Y-m-d H:i:s');
                //Increas 5 Hour
                $date = date_create($ncdatein);
                date_add($date, date_interval_create_from_date_string('5 hours'));
                $ncdateinup = date_format($date, 'Y-m-d H:i:s');
                //echo $ncdatein.' ';
                
                $ncdateout = $dt->finish_act;
                //Reduce 2 Hour
                $date = date_create($ncdateout);
                date_add($date, date_interval_create_from_date_string('-2 hours'));
                $ncdateoutdown = date_format($date, 'Y-m-d H:i:s');
                //Increas 5 Hour
                $date = date_create($ncdateout);
                date_add($date, date_interval_create_from_date_string('10 hours 30 minutes'));
                $ncdateoutup = date_format($date, 'Y-m-d H:i:s');
                $qry5=DB::connection('tms')->table('tb_iclock')
                ->where('badgenumber',$badge)
                ->where('checktime','>=',$ncdateindown)
                ->where('checktime','<=',$ncdateinup)
                ->orderby('checktime','asc')
                ->limit(1)
                ->get(['checktime']);
                foreach($qry5 as $dt5){
                    $checkin_act = $dt5->checktime;
                    $in_finger=1;
                }
                $qry6=DB::connection('tms')->table('tb_iclock')
                ->where('badgenumber',$badge)
                ->where('checktime','>=',$ncdateoutdown)
                ->where('checktime','<=',$ncdateoutup)
                ->orderby('checktime','asc')
                ->limit(1)
                ->get(['checktime']);
                foreach($qry6 as $dt6){
                    $checkout_act = $dt6->checktime;
                    $out_finger=1;
                }
                    
                //Durasi
                    // if($dt->start_plan<$checkin_act)$tgl1=$checkin_act;
                    // else $tgl1=$dt->start_plan;
                    // if($dt->finish_plan>$checkout_act)$tgl2=$checkout_act;
                    // else $tgl2=$dt->finish_plan;
                    $tgl1=$dt->start_plan;
                    $tgl2=$dt->finish_plan;
                    $date1 = new DateTime($tgl1);
                    $date2 = new DateTime($tgl2);
                    $diff = $date1->diff($date2);
                    
                    $totalJam = ($diff->days * 24) + $diff->h + ($diff->i / 60) + ($diff->s / 3600);
                // End Durasi

                if($checkin_act!=''){
                    $update=DB::table('tb_overtime_spv')->where('id',$dt->id)->update([
                        'checkin'=>$checkin_act,
                        'in_finger'=>$in_finger,
                        'conducted_status'=>'1',
                        'start_act'=>$tgl1,
                        'finish_act'=>$tgl2,
                        'hours_act'=>$totalJam,
                        'isCompleted'=>'1',
                        'conducted_status_date'=>$now
                    ]);
                }
                if($checkout_act!=''){
                    $update=DB::table('tb_overtime_spv')->where('id',$dt->id)->update([
                        'checkout'=>$checkout_act,
                        'out_finger'=>$out_finger,
                        'conducted_status'=>'1',
                        'start_act'=>$tgl1,
                        'finish_act'=>$tgl2,
                        'hours_act'=>$totalJam,
                        'isCompleted'=>'1',
                        'conducted_status_date'=>$now
                    ]);
                }

            }

        }
        \Log::info("Update Finger Assignment End");
    }
}
