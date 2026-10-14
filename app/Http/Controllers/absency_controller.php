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
    function absensiRate($periode,$dept_id,$hari_kerja){
        $tb_dept=DB::table('tb_departments')->where('id',$dept_id)->orwhere('dept_code',$dept_id)->get();
        foreach($tb_dept as $dt){
            $dept_name=$dt->dept_name;
            $dept_code=$dt->dept_code;
        }
        //return $tb_dept;

        $admin=Auth::user()->name;
        date_default_timezone_set("Asia/Jakarta");
        $kalendar=CAL_GREGORIAN;
        $sekarang=date('Y-m-d H:i:s');

        $data_a=explode('-',$periode);
        $thn=$data_a[0];
        $bln=$data_a[1];

        $hariakhir=cal_days_in_month($kalendar,$bln,$thn);

        $Tglawal=date('Y-m-d',strtotime($thn.'-'.$bln.'-01'));
        $Tglakhir=date('Y-m-d',strtotime($thn.'-'.$bln.'-'.$hariakhir));
        $periode=date('Y-m',strtotime($Tglawal));

        $tb_absens=DB::table('tb_absensi_rate_kumulatif')
        ->where('tb_absensi_rate_kumulatif.periode',$periode)->where('dept_id',$dept_id)->where('hari_kerja',$hari_kerja)->get();
        $tb_absens_qty=DB::table('tb_absensi_rate_kumulatif')
        ->where('tb_absensi_rate_kumulatif.periode',$periode)->where('dept_id',$dept_id)->where('hari_kerja',$hari_kerja)->count();
        //return $tb_absens;
        $tb_absen=DB::table('tb_absensi_rate')
        ->where('tb_absensi_rate.periode',$periode)->where('dept_id',$dept_id)->where('present_plan','>','0')->where('hari_kerja',$hari_kerja)
        ->orderby('position_index','desc')
        ->orderby('jabatan','desc')
        ->orderby('employee_name','asc')
        ->get();
        $tb_absen_qty=DB::table('tb_absensi_rate')
        ->where('tb_absensi_rate.periode',$periode)->where('dept_id',$dept_id)->where('present_plan','>','0')->where('hari_kerja',$hari_kerja)
        ->orderby('employee_name','asc')->count();
        //return  $tb_absen_qty;
        //return "Data belum tersedia";
        if($tb_absen_qty==0||$tb_absens_qty==0)return redirect("/TMS/Sending/".$dept_code."/".$periode."/0");
        //return redirect("/NewAbsencyRate/".$periode."/".$dept_id."/".$hari_kerja);
        //return view('page/user/m_absency/absencysummary',['Tgl_akhir'=>$Tglakhir,'dept_name'=>$dept_name,'periode'=>$periode,'thn'=>$thn,'bln'=>$bln,'tb_absen'=>$tb_absen,'menu'=>'absency']);
        
        //return $tb_absen;
        $FileName='ABSENCY_RATE.PDF';
        $pdf = PDF::loadview('page/user/m_absency/absencyrate',['tb_absens'=>$tb_absens,'tb_absen'=>$tb_absen])->setPaper('a4','landscape');
        return $pdf->stream($FileName);
    }

 
}
