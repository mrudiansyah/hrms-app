<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DateTime;
use Auth;
use PDF;

class absency_controller extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }
    function iclock($PIN,$start,$end){
        $tb_absen = DB::table('tb_iclock')
        ->select(
            'tb_iclock.userid', 'tb_iclock.name','tb_iclock.badgenumber','tb_iclock.checktime','tb_iclock.checktype','tb_iclock.WorkCode','tb_iclock.sensorid'
        )
        ->where('tb_iclock.checktime', '>=', $start)
        ->where('tb_iclock.checktime', '<=', $end)
        ->where('tb_iclock.badgenumber',$PIN)
        ->orderby('tb_iclock.checktime','asc')
        ->get();
        return view('page/tms/absencyfingers',['PIN'=>$PIN,'start'=>$start,'end'=>$end,'tb_absen'=>$tb_absen,'badgenumber'=>$PIN,'menu'=>'absency']);
    }

 
}
