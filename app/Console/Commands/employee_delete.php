<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Http\Request;
use App\Models\tb_employee;
use App\Models\tb_presence;
use App\Models\tb_shift;
use Illuminate\Support\Facades\DB;
use DateTime;
use Auth;
use PDF;
use App\Http\Controllers\tms_controller;


class employee_delete extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sg:employee_delete';

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

        \Log::info("Start Reading Employee");
        $weekday=date('w');
        $hour=date('H');
        if($weekday<6&&$hour<7){
            $periode=date('Y-m');
            $tb_employee=DB::table('tb_employees')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
            ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
            ->leftjoin('tb_statuses', function ($join) {
                $join->on('tb_statuses.id_employee', '=', 'tb_employees.id')
                    ->where('tb_statuses.active', '1');
            })
            //->where('delete','0')
            ->where('tb_employees.status','0')
            ->orderby('employee_name','asc')
            ->get(['tb_statuses.*','tb_employees.employee_name','tb_employees.delete']);
            $today=date('Y-m-d');
            $no=0;
            foreach($tb_employee as $dt){
                if($dt->delete==0){
                    $markup=date('Y-m-d',strtotime('+2 months',strtotime($dt->start_contract)));
                    if($markup<$today){
                        $no++;
                        $update=DB::table('tb_employees')->where('id',$dt->id_employee)->update([
                            'delete'=>'1',
                            'updated_at'=>$now,
                        ]);
                        $deaktif_payroll=DB::connection('payroll')->table('tb_salary_contract')->where('id',$dt->id_employee)->update(['status'=>'0']);
                        $deaktif_tms=DB::connection('tms')->table('tb_work_contract')->where('id',$dt->id_employee)->update(['is_delete'=>'1']);
                        \Log::info("Update: ".$no.". ".$dt->employee_name);
                    }
                }
                $tb_we=DB::connection('tms')->table('tb_work_entries')->where('id_employee',$dt->id_employee)->where('periode',$periode)->where('daily_show','1')->update(['daily_show'=>'0']);
                if($tb_we)\Log::info("Update Status Work Entry :".$dt->employee_name);
            }
            \Log::info("Update Status Employee Finish ".$no." Records");
        }

    }
}
