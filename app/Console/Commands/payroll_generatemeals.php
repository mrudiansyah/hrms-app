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


class payroll_generatemeals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sg:payroll_generatemeals';

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
        // $periode='2026-09';
        $periode=date('Y-m');
        $kalendar=CAL_GREGORIAN;
        $hari_awal=$periode.'-01';
        $periode_awal=date('Y-m',strtotime('-1 days',strtotime($hari_awal)));

        $dept='';
        $id_employee='';

        \Log::info($periode);

        $tb_focus=DB::table('tb_employee_vendorstay as a')
        ->leftjoin('tb_employees as b','b.id','=','a.id_employee')
        ->leftjoin('tb_departments as c','c.id','=','b.dept_id')
        ->where('b.status','1')
        ->where('a.status','1')
        ->get(['a.id_employee','b.employee_name as nama_karyawan','c.dept_code as department']);

        $no=0;
        foreach($tb_focus as $dtf){
            
            $tb_work_entries=DB::connection('tms')->table('tb_work_entries')
            ->where('periode',$periode_awal)
            ->where('plan_actual','actual')
            ->where('tb_work_entries.id_employee',$dtf->id_employee)
            ->orderby('tb_work_entries.id','asc')
            ->get();
            foreach($tb_work_entries as $dt){
                \Log::info($periode_awal.' Tua');
                for($i=25;$i<=31;$i++){
                    $j = (strlen($i)==1) ? '0'.$i : $i;
                    $kolom='D'.$j;
                    $Tgl=$periode_awal.'-'.$j;
                    $val = (string)$dt->$kolom;
                    if(in_array($val, ['51','52','71','72','73','731','732','733','510','520','710','720','730','7310','7320','7330'])) {
                        $check=DB::table('tb_meals')->where('id_employee',$dtf->id_employee)->where('date_on',$Tgl)->where('category','meal_tl')->count();
                        if($check==0){
                            $add=DB::table('tb_meals')->insert([
                                'id_employee'=>$dtf->id_employee,
                                'date_on'=>$Tgl,
                                'category'=>'meal_tl',
                                'ref_id'=>'0',
                                'meal'=>'17000',
                            ]);
                            \Log::info("Add");
                        }
                        \Log::info($val.' '.$check);
                    }else{
                        $check=DB::table('tb_meals')->where('id_employee',$dtf->id_employee)->where('date_on',$Tgl)->where('category','meal_tl')->update([
                            'is_deleted'=>'1',
                            'deleted_by'=>'system'
                        ]);
                        \Log::info($val);
                    }
                    \Log::info($Tgl);
                }
            }

            $tb_work_entries=DB::connection('tms')->table('tb_work_entries')
            ->where('periode',$periode)
            ->where('plan_actual','actual')
            ->where('tb_work_entries.id_employee',$dtf->id_employee)
            ->orderby('tb_work_entries.id','asc')
            ->get();
            foreach($tb_work_entries as $dt){
                \Log::info($periode.' Muda');
                for($i=1;$i<=24;$i++){
                    $j = (strlen($i)==1) ? '0'.$i : $i;
                    $kolom='D'.$j;
                    $Tgl=$periode.'-'.$j;
                    $val = (string)$dt->$kolom;
                    if(in_array($val, ['51','52','71','72','73','731','732','733','510','520','710','720','730','7310','7320','7330'])) {
                        $check=DB::table('tb_meals')->where('id_employee',$dtf->id_employee)->where('date_on',$Tgl)->where('category','meal_tl')->count();
                        if($check==0){
                            $add=DB::table('tb_meals')->insert([
                                'id_employee'=>$dtf->id_employee,
                                'date_on'=>$Tgl,
                                'category'=>'meal_tl',
                                'ref_id'=>'0',
                                'meal'=>'17000',
                            ]);
                            $no++;
                        }
                    }else{
                        $check=DB::table('tb_meals')->where('id_employee',$dtf->id_employee)->where('date_on',$Tgl)->where('category','meal_tl')->update([
                            'is_deleted'=>'1',
                            'deleted_by'=>'system'
                        ]);
                    }
                }
            }
            \Log::info($dtf->id_employee.' '.$no);

        }

        \Log::info("Finish Update");
    }
}
