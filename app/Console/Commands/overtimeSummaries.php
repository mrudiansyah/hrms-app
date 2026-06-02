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
use App\Models\tb_salary;
use Auth;
use PDF;


class overtimeSummaries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sg:overtimeSummaries';

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

        $admin='System';
        date_default_timezone_set("Asia/Jakarta");
        $kalendar=CAL_GREGORIAN;
        $sekarang=date('Y-m-d H:i:s');
        $thn_sekarang=date('Y');
        $bln_sekarang=date('m');
        $tgl_sekarang=date('d');

        if($tgl_sekarang>1 && $tgl_sekarang<=14){
            \Log::info('Start Overtime Summaries');
            
            $awalBulan=date('Y-m',strtotime($thn_sekarang.'-'.$bln_sekarang.'-01'));
            $akhirBulan=date('Y-m-d',strtotime('-1 days',strtotime($awalBulan)));
            $thn=date('Y',strtotime($akhirBulan));
            $bln=date('m',strtotime($akhirBulan));
            
            $periode=date('Y-m',strtotime($thn.'-'.$bln.'-01'));
            $hariakhir=cal_days_in_month($kalendar,$bln,$thn);
    
            $tb_pph21_status=DB::table('tb_utilities')->where('atribut','PPH21')->get();
            foreach($tb_pph21_status as $dt_pphs1_status){$pph21_status=$dt_pphs1_status->status;}
    
            $tb_department=DB::table('tb_departments')->where('isTrial','1')->where('isDelete','0')->orderby('id','asc')->get();
            foreach($tb_department as $dt_department){
                \Log::info('Updating Department '.$dt_department->dept_code);
    
                $tb_employee=DB::table('tb_employees')
                ->leftjoin('tb_departments','tb_departments.id','tb_employees.dept_id','tb_employees.NIK','tb_employees.employee_name')
                ->where('dept_id',$dt_department->id)
                ->where('delete','0')
                ->get(['tb_employees.*','tb_departments.dept_code','tb_departments.dept_name']);
                $divisi='';
                foreach ($tb_employee as $dt) {
                    $divisi=$dt->dept_name;
                    $t_hours=0;$t_convertion=0;
                    $total_bayars=0;
                    $t_ammount=0;
                    $t_off=0;$t_ot=0;$t_tl=0;
                    $total_meal=0;
                    for ($i=1; $i <= $hariakhir; $i++) {
                        $Tgl=date('Y-m-d',strtotime($thn.'-'.$bln.'-'.$i));
                        $delete=DB::connection('emsOvertime')->table('tb_slip_overtimes')->where('id_employee',$dt->id)->where('date_on',$Tgl)->delete();
                        //Update Slip via Meal
                            $meal_off=0;
                            $meal_ot=0;
                            $meal_tl=0;
                            $total_bayar=0;
                            $tb_meal=tb_meal::where('date_on',$Tgl)->where('id_employee',$dt->id)->get();
                            foreach ($tb_meal as $dt2) {
                                if($dt2->category=='meal_off'){$meal_off=$dt2->meal;$t_off=$t_off+$meal_off;}
                                if($dt2->category=='meal_ot'){$meal_ot=$dt2->meal;$t_ot=$t_ot+$meal_ot;}
                                if($dt2->category=='meal_tl'){$meal_tl=$dt2->meal;$t_tl=$t_tl+$meal_tl;}
                                $total_bayar=$meal_off+$meal_ot+$meal_tl;
                                $total_meal=$t_off+$t_ot+$t_tl;
                            }
                            if($total_bayar>0){
                                $insert_slip=DB::connection('emsOvertime')->table('tb_slip_overtimes')->insert([
                                    'periode'=>$periode,
                                    'dept_id'=>$dt_department->id,
                                    'date_on'=>$Tgl,
                                    'id_employee'=>$dt->id,
                                    'meal_off'=>$meal_off,
                                    'meal_ot'=>$meal_ot,
                                    'meal_tl'=>$meal_tl,
                                    'total_bayar'=>$total_bayar,
                                    'status'=>'0',
                                    'created_at'=>$sekarang,
                                    'updated_at'=>$sekarang
                                ]);
                            }
                        //Update Slip via Mal End
                        //Update Slip via Overtime
                            $tb_overtime_detail=tb_overtime_detail::where('date_on',$Tgl)->where('id_employee',$dt->id)->where('status','6')->get();
                            //$SLPJ=0;
                            foreach ($tb_overtime_detail as $dt2) {
                                $slpj=round($dt2->SLPJ);
                                $total_bayar=$total_bayar+$dt2->ammount;
                                $t_hours=$t_hours+$dt2->hours_act;
                                $t_convertion=$t_convertion+$dt2->hours_convertion;
                                $t_ammount=$t_ammount+$dt2->ammount;
                                //$SLPJ=$dt2->SLPJ;
    
    
                                //$check=DB::connection('emsOvertime')->table('tb_slip_overtimes')->where('id_employee',$dt->id)->where('date_on',$Tgl)->where('ot_start',$dt2->start_act)->count();
                                $check=DB::connection('emsOvertime')->table('tb_slip_overtimes')->where('id_employee',$dt->id)->where('date_on',$Tgl)->count();
                                if($check>0){
                                    $update1=DB::connection('emsOvertime')->table('tb_slip_overtimes')->where('id_employee',$dt->id)->where('date_on',$Tgl)->update([
                                        'ot_category'=>$dt2->ot_category,
                                        'id_overtime'=>$dt2->id_ot,
                                        'id_overtime_detail'=>$dt2->id,
                                        'slpj'=>$slpj,
                                        'ot_start'=>$dt2->start_act,
                                        'ot_finish'=>$dt2->finish_act,
                                        'act_hours'=>$dt2->hours_act,
                                        'act_convertion'=>$dt2->hours_convertion,
                                        'ammount'=>$dt2->ammount,
                                        'total_bayar'=>$total_bayar
                                    ]);
                                }else{
                                    $insert_slip=DB::connection('emsOvertime')->table('tb_slip_overtimes')->insert([
                                        'periode'=>$periode,
                                        'dept_id'=>$dt_department->id,
                                        'date_on'=>$Tgl,
                                        'ot_category'=>$dt2->ot_category,
                                        'id_employee'=>$dt->id,
                                        'id_overtime'=>$dt2->id_ot,
                                        'id_overtime_detail'=>$dt2->id,
                                        'slpj'=>$slpj,
                                        'ot_start'=>$dt2->start_act,
                                        'ot_finish'=>$dt2->finish_act,
                                        'act_hours'=>$dt2->hours_act,
                                        'act_convertion'=>$dt2->hours_convertion,
                                        'ammount'=>$dt2->ammount,
                                        'total_bayar'=>$total_bayar,
                                        'status'=>'0',
                                        'created_at'=>$sekarang,
                                        'updated_at'=>$sekarang
                                    ]);
    
                                }
                            }
                        //Update Slip via Overtime End
                        $total_bayars=$total_bayars+$total_bayar;
        
                    }
    
                    $tb_rapel=DB::connection('emsOvertime')->table('tb_slip_overtimes')->where('id_employee',$dt->id)->where('periode',$periode)->where('rapel_ot','1')->get();
                    foreach($tb_rapel as $dt3){
                        $t_ammount=$t_ammount+$dt3->total_bayar;
                        $t_hours=$t_hours+$dt3->act_hours;
                        $t_convertion=$t_convertion+$dt3->act_convertion;
                        $total_bayars=$total_bayars+$dt3->total_bayar;
                    }
                    $pph21=0;
                    $pph21_insentive=0;
    
                    $tb_summary_qty=DB::connection('emsOvertime')->table('tb_summary_overtime')->where('id_employee',$dt->id)->where('periode',$periode)->count();
                    if($tb_summary_qty==0){
                        $SLPJ=0;
                        $tb_salary=tb_salary::where('id_employee',$dt->id)->where('status','1')->limit(1)->get();
                        //return $tb_salary;
                        foreach($tb_salary as $dtsal){$SLPJ=round($dtsal->slpj);}
                        DB::connection('emsOvertime')->table('tb_summary_overtime')->insert([
                            'periode'=>$periode,
                            'dept_id'=>$dt->dept_id,
                            'id_employee'=>$dt->id,
                            'NIK'=>$dt->NIK,
                            'employee_name'=>$dt->employee_name,
                            'divisi'=>$dt->dept_name,
                            'SLPJ'=>$SLPJ,
                            'rapel'=>'0',
                            'pph21'=>'0',
                            'pph21_insentive'=>'0',
                            't_hours'=>'0',
                            't_convertion'=>'0',
                            't_ammount'=>'0',
                            't_off'=>'0',
                            't_ot'=>'0',
                            't_tl'=>'0',
                            'total_meal'=>'0',
                            'total_bayars'=>'0',
                            'total_paid'=>'0',
                            'payroll'=>$admin,
                            'status'=>'0',
                            'created_at'=>$sekarang,
                            'updated_at'=>$sekarang
                        ]);

                    }

                    $tb_summary=DB::connection('emsOvertime')->table('tb_summary_overtime')->where('id_employee',$dt->id)->where('periode',$periode)->get();
                    foreach ($tb_summary as $dt2) {
                        $tb_pph21=DB::table('tb_salary')->where('Periode',$periode)->where('id_employee',$dt2->id_employee)->get(['PPH21_OT']);
                        foreach($tb_pph21 as $dt_tax){
                            $pph21=$dt_tax->PPH21_OT;
                            $pph21_insentive=$pph21*$pph21_status;
                        }
    
                        //if($pph21>1)return $pph21;
    
                        $rapel=$dt2->rapel;
                        $total_paid=$rapel-$pph21+$pph21_insentive+$total_bayars;
                        $update2=DB::connection('emsOvertime')->table('tb_summary_overtime')->where('id',$dt2->id)->update([
                            //'SLPJ'=>$slpj,
                            't_hours'=>$t_hours,
                            't_convertion'=>$t_convertion,
                            't_ammount'=>$t_ammount,
                            't_off'=>$t_off,
                            't_ot'=>$t_ot,
                            't_tl'=>$t_tl,
                            'pph21'=>$pph21,
                            'pph21_insentive'=>$pph21_insentive,
                            'total_meal'=>$total_meal,
                            'total_bayars'=>$total_bayars,
                            'total_paid'=>$total_paid,
                            'payroll'=>$admin
                        ]);
                    }
    
    
                }
    
            }
            \Log::info('Finish Overtime Summaries');
    
        }
        else \Log::info('Skip Update Summaries Overtime');

    }
}
