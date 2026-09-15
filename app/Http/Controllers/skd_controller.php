<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use DateTime;
use Session;
use PDF;
use Auth;
use image;
use App\Models\tb_cycle;
use App\Models\tb_freeday;

class skd_controller extends Controller
{
    function index(){
        $id_employee=session('id_employee','');
        $NIK=session('NIK','');
        $employee_name=session('employee_name','');
        $department=session('department','');
        $photo=session('photo','');
        $tb_employee=DB::table('tb_employees')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->where('tb_employees.status','1')->where('tb_employees.id',$id_employee)
        ->get(['tb_employees.*','tb_departments.dept_name as department','tb_positions.position_name']);
        $tb_diagnosa=DB::table('tb_diagnosa_list')->where('is_active','1')->orderby('diagnosa','asc')->get();
        return view('page/admin/m_permit/skd',['tb_employee'=>$tb_employee,'tb_diagnosa'=>$tb_diagnosa,'id_employee'=>$id_employee,'NIK'=>$NIK,'employee_name'=>$employee_name,'department'=>$department,'photo'=>$photo,'menu'=>'profile','juduls'=>'SKD']);
    }
    function check(Request $data){
      $id = $data->NIK;
      $hasil=0;
      $tb_employee=DB::table('tb_employees')->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')->where('NIK',$id)->get(['tb_employees.*','tb_departments.dept_name']);
      foreach($tb_employee as $dt){
        session([
          'id_employee'=>$dt->id,
          'NIK'=>$dt->NIK,
          'employee_name'=>$dt->employee_name,
          'department'=>$dt->dept_name,
          'PIN'=>$dt->PIN
        ]);
        $hasil++;
      }
      if($hasil==0)return redirect()->back()->with(['success' => 'Gagal']);
      //if($hasil>0)return redirect()->back()->with(['success' => 'Berhasil']);
      return redirect()->back();
      
    }
    function keluar(){
      session()->forget(['id_employee','NIK','employee_name','department','PIN']);
      return redirect('/SKD');
    }
    public function compress(Request $data)
    {
      if($data->diagnosa=='')return redirect()->back()->with(['success' => 'Diagnosa harus diisi']);
      date_default_timezone_set("Asia/Jakarta");
      $sekarang=date('Y-m-d H:i:s');
      $tanggal=date('Y-m-d');
      $tujuan_upload = 'public/absensi';

      $namafile=date('ymdhis').'-'.session('PIN');

      $file = $data->file('foto');
      if($file!=''){ 
        $filename = $file->getClientOriginalName();
        $valid_ext = array('png','jpeg','jpg','pdf');
        $file_extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if(in_array($file_extension,$valid_ext)){
          $imagePath = $file->getPathName();
          $savedPath = public_path('absensi/' . $filename);

          if (in_array($file_extension, ['png','jpeg','jpg'], true)) {
            $this->compressImage($imagePath, $savedPath, 20);
          } else {
            $file->move(public_path('absensi'), $filename);
          }

          if (!file_exists($savedPath)) {
            return redirect()->back()->with(['success' => 'File upload gagal, silakan coba lagi']);
          }

          $type = strtolower(pathinfo($savedPath, PATHINFO_EXTENSION));
          $datas = file_get_contents($savedPath);
          if ($type === 'pdf') {
            $base64 = 'data:application/pdf;base64,' . base64_encode($datas);
          } else {
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($datas);
          }

          @unlink($savedPath);

        }else{
          echo "Invalid file type.";
        }
        
        $cek=DB::table('tb_sick')->where('id_employee',session('id_employee'))->where('tanggal',$data->start_leave)->count();
        if($cek==0){
          $simpan=DB::table('tb_sick')->insert([
            'id_employee'=>session('id_employee'),
            'NIK'=>session('NIK'),
            'employee_name'=>session('employee_name'),
            'upload_time'=>$sekarang,
            'skd'=>$base64,
            'status'=>'0',
            'diagnosa'=>$data->diagnosa,
            'tanggal'=>$data->start_leave,
          ]);
          //session(['size'=>'']);
          //session()->flush();
          if($simpan){
            $tb_employee_leave=DB::table('tb_employee_leaves')->where('id_employee',$data->id_employee)->where('status','1')->get();
            foreach($tb_employee_leave as $dt){
              $id_leave=$dt->id;            
            }
            $doc_date=date('Y-m-d');
            
            $tb_employees=DB::table('tb_employees')->where('id',$data->id_employee)->get();
            foreach($tb_employees as $dt2){
              $id_leader1=$dt2->leader_id;
            }
            $tb_leader1=DB::table('tb_employees')->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')->where('tb_employees.id',$id_leader1)->get(['tb_employees.id','tb_employees.leader_id','tb_positions.position_index']);
            foreach($tb_leader1 as $dt1){
                $position1=$dt1->position_index;
                $id_leader2=$dt1->leader_id;
            }
            $tb_leader2=DB::table('tb_employees')->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')->where('tb_employees.id',$id_leader2)->get(['tb_employees.id','tb_employees.leader_id','tb_positions.position_index']);
            foreach($tb_leader2 as $dt2){
                $position2=$dt2->position_index;
                $id_leader3=$dt2->leader_id;
            }
            if(isset($id_leader3)){
                $tb_leader3=DB::table('tb_employees')->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')->where('tb_employees.id',$id_leader3)->get(['tb_employees.id','tb_employees.leader_id','tb_positions.position_index']);
                foreach($tb_leader3 as $dt3){
                    $position3=$dt3->position_index;
                    $id_leader4=$dt3->leader_id;
                }
            }
            $qty_approval=1;
            if($position1<3){
                //Jika Direct leader dibawah specialist or Spv -> disetup ke atasan berikutnya
                $approve1=$id_leader2;
                //if($position3>8)$approve2='0';
                if($qty_approval==1)$approve2='0';
                else $approve2=$id_leader3;
            }else{
                $approve1=$id_leader1;
                //if($position2>8)$approve2='0';
                if($qty_approval==1)$approve2='0';
                else $approve2=$id_leader2;
            }
            if($approve2=='0')$status2=0;
            $admin='Form SKD';
            $tgl_masuk=date('Y-m-d',strtotime($data->finish.' +1 day'));
            $tb_sick=DB::table('tb_sick')->where('upload_time',$sekarang)->get();
            foreach($tb_sick as $dt){
              $id_doc=$dt->id;
            }
    
            $add_leave_docter=DB::table('tb_leaves')->insert([
              'id_leave'=>$id_leave,
              'id_doc'=>$id_doc,
              'doc_date'=>$doc_date,
              'id_employee'=>$data->id_employee,
              'category'=>'docter',
              'start_leave'=>$data->start_leave,
              'finish_leave'=>$data->finish_leave,
              'start_working'=>$tgl_masuk,
              'leave_count'=>$data->leave_count,
              'reason'=>$data->diagnosa,
              'remark'=>'',
              'approved'=>$approve1,
              'approved2'=>$approve2,
              'legalized'=>'122',
              'status_requested'=>'1',
              'status_approved'=>'1',
              'status_approved2'=>$status2,
              'status_legalized'=>'0',
              'status_reported'=>'0',
              'admin'=>$admin,
              'status'=>'1',
              'overlap_doc'=>'',
            ]);

            session()->forget(['id_employee','NIK','employee_name','department','PIN']);
            return redirect('SKD')->with(['success' => 'Berhasil, SKD Asli tetap harus diserahkan ke HR...!']);

          }
        }else{
          return redirect()->back()->with(['success' => 'Double Upload, Anda sudah upload SKD']);
        }

      }else{
        return redirect()->back()->with(['success' => 'Photo belum dipilih']);;
      }
    }
    function compressImage($source, $destination, $quality) {
      $info = @getimagesize($source);
      if ($info === false || !isset($info['mime'])) {
          return false;
      }

      switch ($info['mime']) {
          case 'image/jpeg':
              $image = imagecreatefromjpeg($source);
              break;
          case 'image/gif':
              $image = imagecreatefromgif($source);
              break;
          case 'image/png':
              $image = imagecreatefrompng($source);
              break;
          default:
              return false;
      }

      if ($image === false) {
          return false;
      }

      imagejpeg($image, $destination, $quality);
      imagedestroy($image);
      return true;
    }
    function image($id){
      $tb_sick=DB::table('tb_sick')->where('id',$id)->get();
      foreach($tb_sick as $dt){
          $photo=$dt->skd;
      }
      echo "<div style='width:100%;text-align:center;background:#333;padding:0px;'><img style='height:100%;border:1px solid #333;  background:#fff;' src='".$photo."' ></div>";
  }
  function leaveCount(Request $data){

    $tgl1 = new DateTime($data->start);
    $tgl2 = new DateTime($data->finish);
    $diffdays = $tgl2->diff($tgl1)->days;
    $id_employee=$data->idemployee;
    $mulai=$data->start;

    $days=0;
    for($i=0;$i<=$diffdays;$i++){
        $Tgl=date('Y-m-d',strtotime($i.' days',strtotime($mulai)));
        $tb_shift=DB::table('tb_employee_shifts')->leftJoin('tb_group_shifts','tb_group_shifts.id','=','tb_employee_shifts.id_shift')->leftJoin('tb_groups','tb_groups.group','=','tb_group_shifts.group')->where('id_employee',$id_employee)
        ->where('tb_employee_shifts.status','1')
        ->get();
        foreach($tb_shift as $row){
            $group=$row->group;
            $tgl1 = new DateTime($row->start_implement);
            $tgl2 = new DateTime($Tgl);
            $diffdays2 = $tgl2->diff($tgl1)->days;
            $diffcycle=Floor($diffdays2/$row->cycle);
            $modcycle=$diffdays2%$row->cycle;
            $modcycle++;

            $tb_cycle=DB::table('tb_cycles')->where('days',$modcycle)->where('group',$group)->where('shift','>','0')->count();
            $tb_freeday=DB::table('tb_freedays')->where('date_off',$Tgl)->count();
            $change_day=DB::table('tb_freedays')->where('date_off',$Tgl)->where('category','Working')->count();
            if(($tb_cycle>0&&$tb_freeday==0)||($change_day>0)){
                $days++;
            }
        }
    }
    return $days;
}

}
