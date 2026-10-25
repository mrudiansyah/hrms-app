<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $periode=date('Y-m');
        $kalendar=CAL_GREGORIAN;
        $hari_awal=$periode.'-01';
        $thn=date('Y',strtotime($periode.'-01'));
        $bln=date('m',strtotime($periode.'-01'));
        $hariakhir=cal_days_in_month($kalendar,$bln,$thn);
        $day=date('d');


        $action=1;
        if($action==1){

            // Harian
                $schedule->command('sg:tms_daily');// Update db_ems_tms.tb_work_entries actual
                // $schedule->command('sg:tms_workingday');// Idem but for new employee/end contract
                // $schedule->command('sg:tms_manifest');

                // $schedule->command('sg:update_finger_asignment');
                // $schedule->command('sg:send_mail_pareto_ot');
                // $schedule->command('sg:send_mail_legal');
                // $schedule->command('sg:employee_delete');

            // Periode Payroll
                if($day>=24&&$day<=25){
                    // $schedule->command('sg:tms_daily_payroll');
                    // $schedule->command('sg:payroll_generatemeals');
                    // $schedule->command('sg:tms_payroll');
                    // $schedule->command('sg:tms_payroll_psab');
                }

            // Periode End Month
                if($day==$hariakhir){
                    // $schedule->command('sg:capture_employee');
                    // $schedule->command('sg:tms_GeneratePlan');
                }

            // Periode Awal Bulan
                // $schedule->command('sg:tms_update_summary_workentries');// Update Tgl 1 s/d 3
                // $schedule->command('sg:tms_AbsencyRate_optimize');// Update Tgl 1 s/d 3

        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
