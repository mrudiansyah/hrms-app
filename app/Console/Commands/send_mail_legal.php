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
use App\Http\Controllers\ApiEmailController_Legal;


class send_mail_legal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sg:send_mail_legal';

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
        $weekday=date('w');
        $hour=date('H');
        if($weekday<6&&$hour<7){
            \Log::info("Send Mail Legal Start");
            $data['tb_category']=DB::table('tb_legal_category')->get();
            $data['tb_legal_permit']=DB::table('tb_legal_permits')
            ->leftjoin('tb_legal_category','tb_legal_category.id','=','tb_legal_permits.id_category')
            ->where('status','<>','Extended')
            ->orderby('expiry_date','desc')->get(['tb_legal_permits.*','tb_legal_category.category','tb_legal_category.warning_day','tb_legal_category.critical_day']);
            $today=date('Y-m-d');
            $tgl1 = new DateTime( $today);
            foreach($data['tb_legal_permit'] as $dt){
                $tgl2 = new DateTime($dt->expiry_date);
                $diffdays = $tgl2->diff($tgl1)->days;
                if($dt->expiry_date<=$today){
                    $status='Expired';
                }elseif($diffdays<=$dt->critical_day){
                    $status='Critical';
                }elseif($diffdays<=$dt->warning_day){
                    $status='Warning';
                }else{
                    $status='Active';
                }
                $update=DB::table('tb_legal_permits')->where('id',$dt->id)->update(['status'=>$status]);
            }

            $query=DB::table('tb_legal_permits')
            ->leftjoin('tb_legal_category','tb_legal_category.id','=','tb_legal_permits.id_category')
            ->where('status','Warning')
            ->orWhere('status','Critical')
            ->orderby('expiry_date','desc')->get(['tb_legal_permits.*','tb_legal_category.category','tb_legal_category.warning_day','tb_legal_category.critical_day']);
            $jumlah_row=$query->count();

            $receiver='asep.setiana@summitadyawinsa.co.id';
            $cc_list='nova.triana@summitadyawinsa.co.id,springer.nenoharan@summitadyawinsa.co.id,ristiyono@summitadyawinsa.co.id,enovianto@summitadyawinsa.co.id,cahyudin@summitadyawinsa.co.id';
            \Log::info($receiver);
            if($jumlah_row>0){
                $request = new Request([
                    'to' => $receiver,
                    'subject' => 'Informasi Perpanjangan Dokumen',
                    'content' => $query,
                    'cc' => $cc_list
                ]);
                $ApiEmailController = new ApiEmailController_Legal();
                $result = $ApiEmailController->sendEmail($request);
                \Log::info("Send Mail Finish");
            }else{
                \Log::info("Send Mail Skip");
            }
        }
    }
}
