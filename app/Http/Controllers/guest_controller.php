<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use DateTime;
use Session;
use PDF;
use Auth;
use image;

class guest_controller extends Controller
{
    function index(){
        $email=Auth::user()->email;
        $id_employee=DB::table('tb_emails')->where('email_address',$email)->value('id_employee');
        $NIK=DB::table('tb_employees')->where('id',$id_employee)->value('NIK');
        $employee_name=DB::table('tb_employees')->where('id',$id_employee)->value('employee_name');
        $department=DB::table('tb_employees')->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')->where('tb_employees.id',$id_employee)->value('tb_departments.dept_name');
        $photo=DB::table('tb_photos')->where('id_employee',$id_employee)->value('nama_photo');
        $status_checktime=$this->check($NIK);
        $tb_employee=DB::table('tb_employees')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->where('tb_employees.status','1')->where('tb_employees.id',$id_employee)
        ->get(['tb_employees.*','tb_departments.dept_name as department','tb_positions.position_name']);
        return view('page/admin/m_checktime/realchecktime',['tb_employee'=>$tb_employee,'id_employee'=>$id_employee,'NIK'=>$NIK,'employee_name'=>$employee_name,'department'=>$department,'photo'=>$photo,'status_checktime'=>$status_checktime,'menu'=>'profile','juduls'=>'Manual Checktime']);
    }
    public function compress(Request $data)
    {
      date_default_timezone_set("Asia/Jakarta");
      $sekarang=date('Y-m-d H:i:s');
      $limit=date('Y-m-d H:i:s',strtotime('-1 hours',strtotime($sekarang)));
      
      $tb_checktime1=DB::table('tb_checktimes')->where('NIK',session('NIK'))->orderby('checktime','desc')->take(1)->get();
      //return $tb_checktime1;
      foreach($tb_checktime1 as $dt_checktime1){
        $limit=date('Y-m-d H:i:s',strtotime('+1 hours',strtotime($dt_checktime1->checktime)));
      }

      //$awal=date('Y-m-d H:i:s');
      $awal=$sekarang;      
      $tb_checktime=DB::table('tb_checktimes')->where('NIK',session('NIK'))->where('status_checktime','checkin')->orderby('checktime','desc')->take(1)->get();
      foreach($tb_checktime as $dt_checktime){
        $awal=$dt_checktime->checktime;
      }

      //return $limit;

      $category=$data->status_kerja;
      $tujuan_upload = 'public/absensi';

      $namafile=date('ymdhis').'-'.session('PIN');

      $file = $data->file('foto');
      if($file!=''){ 
        if($sekarang>$limit){
            $email=Auth::user()->email;
            $id_employee=DB::table('tb_emails')->where('email_address',$email)->value('id_employee');
            $NIK=DB::table('tb_employees')->where('id',$id_employee)->value('NIK');
            $PIN=DB::table('tb_employees')->where('id',$id_employee)->value('PIN');
            $employee_name=DB::table('tb_employees')->where('id',$id_employee)->value('employee_name');
            $simpan=DB::table('tb_checktimes')->insert([
                'PIN'=>$PIN,
                'checktime'=>$sekarang,
                'checker'=>$employee_name,
                'NIK'=>$NIK,
                'employee_name'=>$employee_name,
                'status_kerja'=>$data->status_kerja,
                'status_checktime'=>session('status_checktime'),
                'photo'=>'Skip'
            ]);
        }
        return redirect('ManualCheck')->with(['success' => 'Berhasil']);

      }else{
        return redirect()->back()->with(['success' => 'Photo belum dipilih']);;
      }
    }
    function compressImage($source, $destination, $quality) {
      $info = getimagesize($source);
      if ($info['mime'] == 'image/jpeg')$image = imagecreatefromjpeg($source);
      elseif ($info['mime'] == 'image/gif')$image = imagecreatefromgif($source);
      elseif ($info['mime'] == 'image/png')$image = imagecreatefrompng($source);
      imagejpeg($image, $destination, $quality);
    }
    function check($NIK){
        $tb_checktime=DB::table('tb_checktimes')->where('NIK',$NIK)->orderby('checktime','desc')->limit(1)->get();
        foreach($tb_checktime as $dt_checktime){
          date_default_timezone_set("Asia/Jakarta");
          $sekarang=date('Y-m-d H:i:s');
          $awal  = date_create($dt_checktime->checktime);
          $akhir = date_create($sekarang);
          $diff  = date_diff( $awal, $akhir );
          $hari=$diff->d;
          $jam=$diff->h;
          $rentang=$hari*24+$jam;
          if($rentang>20)session(['status_checktime'=>'checkin','rentang'=>'0']);
          else session(['status_checktime'=>'checkout','rentang'=>$rentang]);
        }

    }

}
