<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DateTime;
use Auth;
use PDF;
use App\Http\Controllers\mail_controller;


class approvalReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sg:approvalReminder';

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

        \Log::info('Start Leave Approval Reminder');
        $tb_leave=DB::table('tb_leaves')->where('status_approved','0')->orWhere([['status_approved2','0'],['approved2','>','0'],['status_approved','1']])->get();
        $i=0;
        foreach($tb_leave as $dt){app('App\Http\Controllers\mail_controller')->LeaveMail($dt->id);$i++;}
        \Log::info('Leave Reminder: '.$i.' person');

        \Log::info('Start Overtime Approval Reminder');
            $tb_overtime=DB::table('tb_overtimes')
            ->where([['status','1'],['isDelete','0'],['status_diperintah','0']])
            ->orwhere(function ($query) {
                $query->where('status','1')->where('isDelete','0')->where('status_diperintah','1')->wherenotnull('disetujui')->where('status_disetujui','0');
            })
            ->orwhere(function ($query1) {
                $query1->where('status','1')->where('isDelete','0')->where('status_disetujui','1')->wherenotnull('diketahui')->where('status_diketahui','0');
            })
            ->get();
            $j=0;
            foreach($tb_overtime as $dt1){app('App\Http\Controllers\mail_controller')->OTMail($dt1->id);$j++;}
        \Log::info('Overtime Reminder: '.$j.' person');
    }
}
