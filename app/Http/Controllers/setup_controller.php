<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Image;
use Maatwebsite\Excel\Facades\Excel;
use Session;
use DateTime;
use Auth;
use PDF;
use App\Mail\slip_Gaji_Payroll;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Validator;

class setup_controller extends Controller
{
    private $site;
    public function __construct(){
        $this->middleware(['auth','verified']);
        $this->site = $_SERVER['SCRIPT_NAME'];
    }
    function index(){
        if (request()->user()->hasRole('hr_access')){
            $data['tb_utilities']=DB::table('tb_utilities')->where('is_setup','1')->get();
            //return $data['tb_utilities'];
            return view('page/setup/setup_hr',['data'=>$data,'site'=>$this->site,'menu'=>'setup','juduls'=>'Setup HR']);
        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }
    function setup_update(Request $data){
        if (request()->user()->hasRole('hr_access')){
            $update=DB::table('tb_utilities')->where('id',$data->id)->update(['status'=>$data->status]);
            if(($data->id=='24')){
                $today=date('Y-m-d');
                $periode=date('Y-m');
                $day=date('d');
                if($day>=25){
                    $limit=$periode.'-24';
                }else{
                    $first_date=date('Y-m-01');
                    $last_periode=date('Y-m',strtotime('-1 days',strtotime($first_date)));
                    $limit=$last_periode.'-24';
                }
                $update=DB::table('tb_utilities')->where('id',$data->id)->update(['limit_date'=>$limit]);
            }
            if(($data->id=='26')){
                $update=DB::connection('tms')->table('tb_work_code')->where('id','11')->update(['manual_adjust'=>$data->status]);
            }
        }else{
            return abort(403,'Anda tidak punya akses');
        }
        //return $data->id.' '.$data->status;
    }
    function setup_limit(Request $data){
        if (request()->user()->hasRole('hr_access')){
            $update=DB::table('tb_utilities')->where('id',$data->id)->update(['limit_transaksi'=>$data->limit_transaksi]);
        }else{
            return abort(403,'Anda tidak punya akses');
        }
        //return $data->id.' '.$data->status;
    }

}
