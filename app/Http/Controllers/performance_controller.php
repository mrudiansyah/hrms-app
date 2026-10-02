<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\KSKImport;

use DateTime;
use App\Http\Controllers\mail_controller;
use PDF;
use Auth;
use App\Mail\ksk_distribute;
use Illuminate\Support\Facades\Log;


class performance_controller extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
        $this->site = $_SERVER['SCRIPT_NAME'];
    }
    function index($periode){
        //return "Under Maintenance, tunggung beberapa menit...";
        $last_date=$periode.'-12-31';
        $tahun_ini=date('Y');
        if($periode==0){
            $periode=date('Y');
            return redirect('/Performance/'.$periode);
        }
        if($periode>$tahun_ini)return redirect('/Performance/0');
        $today=date('Y-m-d');
        $bulan_skr=date('Y-m');
        $nama=Auth::user()->name;
        $email=Auth::user()->email;
        $cek1=DB::table('tb_emails')->where('email_address',$email)->get();
        foreach($cek1 as $dt){$id_user=$dt->id_employee;}
        $tb_aku=DB::table('tb_employees')->where('leader_id',$id_user)->get();

        $tb_anak=DB::table('tb_employees')->where('leader_id',$id_user);
        foreach($tb_aku as $dt){
            $tb_anak=$tb_anak->orwhere('leader_id',$dt->id);
        }
        $tb_anak=$tb_anak->get();

        $tb_cucu=DB::table('tb_employees')->where('leader_id',$id_user);
        foreach($tb_anak as $dt){
            $tb_cucu=$tb_cucu->orwhere('leader_id',$dt->id);
        }
        $tb_cucu=$tb_cucu->get();

        $tb_buyut=DB::table('tb_employees')->where('leader_id',$id_user);
        foreach($tb_anak as $dt){
            $tb_buyut=$tb_buyut->orwhere('leader_id',$dt->id);
        }
        foreach($tb_cucu as $dt){
            $tb_buyut=$tb_buyut->orwhere('leader_id',$dt->id);
        }
        $tb_buyut=$tb_buyut->get();

        //return $tb_cucu;

        $tb_admins=DB::table('tb_admins')->where('id_employee',$id_user)->get();
        $tb_employee=DB::table('tb_employees')
        ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_employees.leader_id')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->leftjoin('tb_performance', function($join) use ($periode) {
            $join->on('tb_performance.id_employee', '=', 'tb_employees.id')
                 ->where('tb_performance.periode', '=', $periode);
        })        
        ->leftjoin('tb_performance_recap','tb_performance_recap.id_performance','=','tb_performance.id');
        $tb_employee=$tb_employee->where([['tb_employees.leader_id',$id_user],['tb_employees.status','1'],['tb_employees.join_date','<=',$last_date],['tb_employees.position_id','<>','37'],['tb_employees.position_id','<>','23'],['tb_employees.position_id','<>','12'],['tb_employees.position_id','<>','13'],['tb_employees.position_id','<>','14'],['tb_employees.position_id','<>','15'],['tb_employees.position_id','<>','16']]);
        foreach($tb_buyut as $dt2){
            $tb_employee=$tb_employee->orwhere([['tb_employees.leader_id',$dt2->id],['tb_employees.status','1'],['tb_employees.join_date','<=',$last_date],['tb_employees.position_id','<>','37'],['tb_employees.position_id','<>','23'],['tb_employees.position_id','<>','12'],['tb_employees.position_id','<>','13'],['tb_employees.position_id','<>','14'],['tb_employees.position_id','<>','15'],['tb_employees.position_id','<>','16']]);
        }
        $tb_employee=$tb_employee->orderby('tb_employees.employee_name','asc')->get(['tb_employees.*','tb_performance.atasan_langsung as atasan_langsung','tb_employees1.employee_name as leader_name','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_index','tb_positions.position_name','tb_performance.id as idperformance','tb_performance.masa_kerja_member','tb_performance_recap.triwulan1','tb_performance_recap.triwulan2','tb_performance_recap.triwulan3','tb_performance_recap.triwulan4','tb_performance_recap.average','tb_performance_recap.grade','tb_performance.target','tb_employees1.leader_id as id_leader2','tb_performance.status_penilai_1','tb_performance.status_penilai_2']);
        //return $tb_employee;
        foreach($tb_employee as $dt){
            $cek=DB::table('tb_performance')->where('id_employee',$dt->id)->where('periode',$periode)->count();
            //$cek=1;
            if($cek==0&&$dt->status=='1'){
                $durasi=$this->durasi($today,$dt->join_date);
                if($dt->position_index<2)$kategori='Pelaksana';
                else if($dt->position_index>4)$kategori='Manager';
                else $kategori='Pengawas';
                $add=DB::table('tb_performance')->insert([
                    'periode'=>$periode,
                    'id_employee'=>$dt->id,
                    'nama_karyawan'=>$dt->employee_name,
                    'tanggal_masuk'=>$dt->join_date,
                    'department'=>$dt->dept_code,
                    'jabatan'=>$dt->position_name,
                    'atasan_langsung'=>$dt->leader_name,
                    'masa_kerja_member'=>$durasi,
                    'kategori_jabatan'=>$kategori,
                    'created_by'=>$nama,
                ]);
                //return redirect()->back();
            }
        }
        return view('page/performance/performance',['menu'=>'performance','bulan_skr'=>$bulan_skr,'tb_employee'=>$tb_employee,'periode'=>$periode,'saya'=>$nama,'id_user'=>$id_user]);
    }
    function performanceDetail($idperformance,$periode,$triwulan,$employee_name,$created_by){
        $email=Auth::user()->email;
        //return $idperformance;
        $cek1=DB::table('tb_emails')->where('email_address',$email)->get();
        foreach($cek1 as $dt){$id_user=$dt->id_employee;}
        $cek=DB::table('tb_performance')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_performance.id_employee')
        ->where('tb_performance.id',$idperformance)->get(['tb_employees.leader_id','status_penilai_2']);
        foreach($cek as $dt){
            $leader_id=$dt->leader_id;
            $status_ttd=$dt->status_penilai_2;
        }
        $id_leader=!empty($leader_id) ? $leader_id : '0';
        $status_ttd=!empty($status_ttd) ? $status_ttd : '0';
        if($id_user==$id_leader){
            $akses=1;
            $status_leader=1;
        }else {
            if($created_by==2){
                $status_leader=1;
            }else{
                $status_leader=0;
            }
            $akses=0;
        }
        $id=$idperformance;

        $check=DB::table('tb_performance_recap')->where('id_performance',$id)->count();
        if($check==0){
            $add=DB::table('tb_performance_recap')->insert([
                'id_performance'=>$id,
            ]);
        }

        $check2=DB::table('tb_performance_detail')->where('id_performance',$id)->where('created_by','1')->count();
        if($check2==0){
            $tb_performance=DB::table('tb_performance')->where('id',$id)->get();
            foreach($tb_performance as $dt){
                $jabatan=$dt->kategori_jabatan;
            }
            for($i=1;$i<=4;$i++){
                $tb_preform_aspek=DB::table('tb_perform_aspek')->where('jabatan',$jabatan)->get();
                foreach($tb_preform_aspek as $dt){
                    if($i==2||$i==4){
                        $add=DB::table('tb_performance_detail')->insert([
                            'id_performance'=>$id,
                            'triwulan'=>$i,
                            'id_aspek'=>$dt->id,
                            'nama_aspek'=>$dt->nama_aspek,
                            'created_by'=>'1'
                        ]);
                    }
                }
            }
        }
        $check2=DB::table('tb_performance_detail')->where('id_performance',$id)->where('created_by','2')->count();
        if($check2==0){
            $tb_performance=DB::table('tb_performance')->where('id',$id)->get();
            foreach($tb_performance as $dt){
                $jabatan=$dt->kategori_jabatan;
            }
            for($i=1;$i<=4;$i++){
                $tb_preform_aspek=DB::table('tb_perform_aspek')->where('jabatan',$jabatan)->get();
                foreach($tb_preform_aspek as $dt){
                    if($i==2||$i==4){
                        $add=DB::table('tb_performance_detail')->insert([
                            'id_performance'=>$id,
                            'triwulan'=>$i,
                            'id_aspek'=>$dt->id,
                            'nama_aspek'=>$dt->nama_aspek,
                            'created_by'=>'2'
                        ]);
                    }
                }
            }
        }

        if($created_by==1){
            $x=$this->copyCreatedBy($id,$triwulan);
        }
        //return $x;

        $tb_performance_detail = DB::table('tb_performance_detail as t1')
        ->leftJoin('tb_perform_aspek', 'tb_perform_aspek.id', '=', 't1.id_aspek')
        ->leftJoin('tb_performance_detail as t2', function($join) use ($id, $triwulan) {
            $join->on('t2.id_performance', '=', 't1.id_performance')
                 ->on('t2.triwulan', '=', 't1.triwulan')
                 ->on('t2.id_aspek', '=', 't1.id_aspek')
                 ->where('t2.created_by', 2);
        })
        ->select('t1.*', 'tb_perform_aspek.nama_aspek', 't2.id as id2', 't2.value as value2', 't2.grade as grade2')
        ->where('t1.id_performance', $id)
        ->where('t1.triwulan', $triwulan)
        ->where('t1.created_by', 1)
        ->orderBy('t1.triwulan', 'asc')
        ->orderBy('t1.id_aspek', 'asc')
        ->orderBy('t1.created_by', 'asc')
        ->get();
        //return $tb_performance_detail;

        $tb_perform_value=DB::table('tb_perform_value')->where('id_aspek','0');
        foreach($tb_performance_detail as $dt){
            $tb_perform_value=$tb_perform_value->orwhere('id_aspek',$dt->id_aspek);
        }

        $tb_perform_value=$tb_perform_value->get();
        $tahun=$periode;
        if($triwulan==1)$triwulan_text="Jan-Mar ".$tahun;
        else if($triwulan==2)$triwulan_text="Jan-Jun ".$tahun;
        else if($triwulan==3)$triwulan_text="Jul-Sep ".$tahun;
        else if($triwulan==4)$triwulan_text="Jul-Dec ".$tahun;
        else $triwulan_text='';

        $tb_performance=DB::table('tb_performance')->where('id',$id)->get();
        foreach($tb_performance as $dt){
            $id_employee=$dt->id_employee;
        }
        $bawah='';
        $atas='';
        if($triwulan==2){
            $bawah=$periode.'-01';
            $atas=$periode.'-06';
        }
        if($triwulan==4){
            $bawah=$periode.'-07';
            $atas=$periode.'-12';
        }
        $data['tb_absensi_rate']=DB::table('tb_absensi_rate')->where('id_employee',$id_employee)->where('periode','>=',$bawah)->where('periode','<=',$atas)->get(['periode','present_rate','hour_rate']);
        $n=0;
        $pr=0;
        $hr=0;
        foreach($data['tb_absensi_rate'] as $dt){
            $n++;
            $pr=$pr+$dt->present_rate;
            $hr=$hr+$dt->hour_rate;
        }
        if($n==0){
            $data['pr_ave']=0;
            $data['hr_ave']=0;
        }else{
            $data['pr_ave']=number_format($pr/$n,2);
            $data['hr_ave']=number_format($hr/$n,2);
        }
        //return $pr_ave.' '.$hr_ave;

        //return $id;
        return view('page/performance/performance_detail',['menu'=>'performance','data'=>$data,'tb_performance_detail'=>$tb_performance_detail,'tb_perform_value'=>$tb_perform_value,'id_performance'=>$id,'periode'=>$tahun,'triwulan'=>$triwulan,'triwulan_text'=>$triwulan_text,'employee_name'=>$employee_name,'akses'=>$akses,'status_ttd'=>$status_ttd,'status_leader'=>$status_leader,'created_by'=>$created_by]);
    }




    public function copyCreatedBy($id_performance,$triwulan){
        $check1=DB::table('tb_performance_detail')
        ->leftjoin('tb_perform_aspek','tb_perform_aspek.id','=','tb_performance_detail.id_aspek')
        ->where('id_performance',$id_performance)
        ->where('triwulan',$triwulan)
        ->where('created_by','1')
        ->where('value','0')
        ->count();
        $check2=DB::table('tb_performance_detail')
        ->leftjoin('tb_perform_aspek','tb_perform_aspek.id','=','tb_performance_detail.id_aspek')
        ->where('id_performance',$id_performance)
        ->where('triwulan',$triwulan)
        ->where('created_by','2')
        ->where('value','0')
        ->count();
        // return $check1.' '.$check2;
        if($check1==0&&$check2>0){
            $createdby1=DB::table('tb_performance_detail')->where('id_performance',$id_performance)->where('triwulan',$triwulan)->where('created_by','1')->get();
            foreach($createdby1 as $dt1){
                $createdby2=DB::table('tb_performance_detail')->where('id_performance',$id_performance)->where('triwulan',$triwulan)->where('id_aspek',$dt1->id_aspek)->where('created_by','2')->update([
                    'id_value'=>$dt1->id_value,
                    'value'=>$dt1->value,
                    'grade'=>$dt1->grade,
                    'level'=>$dt1->level,
                    'kriteria'=>$dt1->kriteria
                ]);
            }
            return $createdby1;
        }
    }
    //Arsif
    function performance(request $data){
        $email=Auth::user()->email;
        //return $data->idperformance;
        $cek1=DB::table('tb_emails')->where('email_address',$email)->get();
        foreach($cek1 as $dt){$id_user=$dt->id_employee;}
        $cek=DB::table('tb_performance')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_performance.id_employee')
        ->where('tb_performance.id',$data->idperformance)->get(['tb_employees.leader_id','status_penilai_2']);
        foreach($cek as $dt){
            $leader_id=$dt->leader_id;
            $status_ttd=$dt->status_penilai_2;
        }
        $id_leader=!empty($leader_id) ? $leader_id : '0';
        $status_ttd=!empty($status_ttd) ? $status_ttd : '0';
        if($id_user==$id_leader){
            $akses=1;
            $status_leader=1;
        }else {
            $akses=0;
            $status_leader=0;
        }
        $id=$data->idperformance;

        $check=DB::table('tb_performance_recap')->where('id_performance',$id)->count();
        if($check==0){
            $add=DB::table('tb_performance_recap')->insert([
                'id_performance'=>$id
            ]);
        }

        $check2=DB::table('tb_performance_detail')->where('id_performance',$id)->count();
        if($check2==0){
            $tb_performance=DB::table('tb_performance')->where('id',$id)->get();
            foreach($tb_performance as $dt){
                $jabatan=$dt->kategori_jabatan;
            }
            for($i=1;$i<=4;$i++){
                $tb_preform_aspek=DB::table('tb_perform_aspek')->where('jabatan',$jabatan)->get();
                foreach($tb_preform_aspek as $dt){
                    $add=DB::table('tb_performance_detail')->insert([
                        'id_performance'=>$id,
                        'triwulan'=>$i,
                        'id_aspek'=>$dt->id,
                        'nama_aspek'=>$dt->nama_aspek,
                    ]);
                }
            }
        }

        $tb_performance_detail=DB::table('tb_performance_detail')
        ->leftjoin('tb_perform_aspek','tb_perform_aspek.id','=','tb_performance_detail.id_aspek')
        ->where('id_performance',$id)
        ->where('triwulan',$data->triwulan)
        ->get('tb_performance_detail.*','tb_perform_aspek.nama_aspek');

        $tb_perform_value=DB::table('tb_perform_value')->where('id_aspek','0');
        foreach($tb_performance_detail as $dt){
            $tb_perform_value=$tb_perform_value->orwhere('id_aspek',$dt->id_aspek);
        }
        $tb_perform_value=$tb_perform_value->get();
        $tahun=$data->periode;
        if($data->triwulan==1)$triwulan_text="Jan-Mar ".$tahun;
        else if($data->triwulan==2)$triwulan_text="Apr-Jun ".$tahun;
        else if($data->triwulan==3)$triwulan_text="Jul-Sep ".$tahun;
        else if($data->triwulan==4)$triwulan_text="Oct-Dec ".$tahun;
        else $triwulan_text='';
        //return $id;
        return view('page/performance/performance_detail',['menu'=>'performance','tb_performance_detail'=>$tb_performance_detail,'tb_perform_value'=>$tb_perform_value,'id_performance'=>$id,'periode'=>$tahun,'triwulan'=>$data->triwulan,'triwulan_text'=>$triwulan_text,'employee_name'=>$data->employee_name,'akses'=>$akses,'status_ttd'=>$status_ttd,'status_leader'=>$status_leader]);
    }
    //End Arsif performance
    function performanceSubmit(request $data){
        $hasil='No Action';
        $tb_value=DB::table('tb_perform_value')->where('id',$data->id_value)->get();
        foreach($tb_value as $dt){
            // $update=DB::table('tb_performance_detail')->where('id',$data->id_perform_dtl)->where('created_by',$data->created_by)->update([
            $tb1=DB::table('tb_performance_detail')->where('id',$data->id_perform_dtl)->get();
            foreach($tb1 as $dt1){
                if($dt1->created_by=1){
                    // $update=DB::table('tb_performance_detail')->where('id',$data->id_perform_dtl)->update([
                    $update=DB::table('tb_performance_detail')->where('id_performance',$dt1->id_performance)->where('triwulan',$dt1->triwulan)->where('id_aspek',$dt1->id_aspek)->update([
                        'id_value'=>$dt->id,
                        'value'=>$dt->nilai,
                        'grade'=>$dt->grade,
                        'level'=>$dt->level,
                        'kriteria'=>$dt->kriteria,
                    ]);
                }                
            }
            if($update)$hasil="Sukses";
        }
        //$hasil=$data->id_perform_dtl.' '.
        return $hasil;
    }
    function performanceCopy(request $data){
        // $last=$data->triwulan-2;
        $last=$data->triwulan-2;
        $update='';
        if($last<=0){
            $hasil='No Data';
        }else{
            $tb_performance_detail=DB::table('tb_performance_detail')->where('id_performance',$data->id_performance)->where('triwulan',$last)->where('created_by',$data->created_by)->get();
            // $tb_performance_detail=DB::table('tb_performance_detail')->where('id_performance',$data->id_performance)->where('triwulan',$last)->get();
            foreach($tb_performance_detail as $dt){
                $update=DB::table('tb_performance_detail')->where('id_performance',$data->id_performance)->where('triwulan',$data->triwulan)->where('id_aspek',$dt->id_aspek)->where('created_by',$data->created_by)->update([
                // $update=DB::table('tb_performance_detail')->where('id_performance',$data->id_performance)->where('triwulan',$data->triwulan)->where('id_aspek',$dt->id_aspek)->update([
                    'id_value'=>$dt->id_value,
                    'value'=>$dt->value,
                    'grade'=>$dt->grade,
                    'level'=>$dt->level,
                    'kriteria'=>$dt->kriteria,
                ]);
            }
        }
        if($update!='')$hasil='Sukses';
        else $hasil='No Data';
        return $hasil;
    }
    function performanceCopy1(request $data){
        $update='';
        $tb_performance_detail=DB::table('tb_performance_detail')->where('id_performance',$data->id_performance)->where('triwulan',$data->triwulan)->where('created_by','1')->get();
        foreach($tb_performance_detail as $dt){
            $update=DB::table('tb_performance_detail')->where('id_performance',$data->id_performance)->where('triwulan',$data->triwulan)->where('id_aspek',$dt->id_aspek)->where('created_by','2')->update([
                'id_value'=>$dt->id_value,
                'value'=>$dt->value,
                'grade'=>$dt->grade,
                'level'=>$dt->level,
                'kriteria'=>$dt->kriteria,
            ]);
        }
        //if($update!='')$hasil='Sukses';
        //else $hasil='No Data';
        $hasil=$data->id_performance.' #'.$data->triwulan;
        return $hasil;
    }
    function performanceSave(request $data){
        $id_performance=$data->id_performance;
        $hasil='No Action';
        $tb_performance_detail=DB::table('tb_performance_detail')->where('id_performance',$id_performance)->get();
        $data['T1']=0;
        $data['T2']=0;
        $data['T3']=0;
        $data['T4']=0;
        $data['G1']=0;
        $data['G2']=0;
        $data['G3']=0;
        $data['G4']=0;

        $data['1T1']=0;
        $data['1T2']=0;
        $data['1T3']=0;
        $data['1T4']=0;
        $data['1G1']=0;
        $data['1G2']=0;
        $data['1G3']=0;
        $data['1G4']=0;

        $data['2T1']=0;
        $data['2T2']=0;
        $data['2T3']=0;
        $data['2T4']=0;
        $data['2G1']=0;
        $data['2G2']=0;
        $data['2G3']=0;
        $data['2G4']=0;

        foreach($tb_performance_detail as $dt){
            $data[$dt->id_performance.$dt->triwulan.$dt->id_aspek]=$dt->value;
            if($dt->triwulan==1){
                if($dt->created_by==1){
                    $data['1T1']=$data['1T1']+$dt->value;
                }
                if($dt->created_by==2){
                    $data['2T1']=$data['2T1']+$dt->value;
                }
                // $data['T1']=$data['T1']+$dt->value;
            }
            if($dt->triwulan==2){
                if($dt->created_by==1){
                    $data['1T2']=$data['1T2']+$dt->value;
                }
                if($dt->created_by==2){
                    $data['2T2']=$data['2T2']+$dt->value;
                }
                // $data['T2']=$data['T2']+$dt->value;
            }
            if($dt->triwulan==3){
                if($dt->created_by==1){
                    $data['1T3']=$data['1T3']+$dt->value;
                }
                if($dt->created_by==2){
                    $data['2T3']=$data['2T3']+$dt->value;
                }
                // $data['T3']=$data['T3']+$dt->value;
            }
            if($dt->triwulan==4){
                if($dt->created_by==1){
                    $data['1T4']=$data['1T4']+$dt->value;
                }
                if($dt->created_by==2){
                    $data['2T4']=$data['2T4']+$dt->value;
                }
                // $data['T4']=$data['T4']+$dt->value;
            }
        }
        $qty=4;
        $sum=0;
        for($i=1;$i<=4;$i++){
            $data['T'.$i]=($data['1T'.$i]+$data['2T'.$i])/2;
            if($data['T'.$i]==0){
                $qty--;
            }
            $sum=$sum+$data['T'.$i];

            $tb_perform_grade=DB::table('tb_perform_grade')->where('range_bawah','<',$data['T'.$i])->where('range_atas','>=',$data['T'.$i])->get();
            foreach($tb_perform_grade as $dt){
                $data['G'.$i]=$dt->grade;
            }
        }
        if($qty==0){
            $data['TAve']=0;
            $data['GAve']=0;
        }else{
            $data['TAve']=$sum/$qty;
        }
        
        $tb_perform_grade=DB::table('tb_perform_grade')->where('range_bawah','<',$data['TAve'])->where('range_atas','>=',$data['TAve'])->get();
        foreach($tb_perform_grade as $dt){
            $data['GAve']=$dt->grade;
        }
        //return $data['TAve'];
        

        if($data['GAve']=='A+')$ranked='1';
        else if($data['GAve']=='A')$ranked='2';
        else if($data['GAve']=='B+')$ranked='3';
        else if($data['GAve']=='B')$ranked='4';
        else if($data['GAve']=='C')$ranked='5';
        else $ranked='0';

        $tb_performance_recap=DB::table('tb_performance_recap')->where('id_performance',$id_performance)->count();
        if($tb_performance_recap==0){
            $proses=DB::table('tb_performance_recap')->insert([
                'id_performance'=>$id_performance,
                'triwulan1'=>$data['T1'],
                'triwulan2'=>$data['T2'],
                'triwulan3'=>$data['T3'],
                'triwulan4'=>$data['T4'],
                'grade1'=>$data['G1'],
                'grade2'=>$data['G2'],
                'grade3'=>$data['G3'],
                'grade4'=>$data['G4'],
                'average'=>$data['TAve'],
                'grade'=>$data['GAve'],
                'ranked'=>$ranked,
                'ranked_bod'=>$ranked,
                'triwulan11'=>$data['1T1'],
                'triwulan21'=>$data['1T2'],
                'triwulan31'=>$data['1T3'],
                'triwulan41'=>$data['1T4'],
                'triwulan12'=>$data['2T1'],
                'triwulan22'=>$data['2T2'],
                'triwulan32'=>$data['2T3'],
                'triwulan42'=>$data['2T4'],
            ]);
        }else{
            $proses=DB::table('tb_performance_recap')->where('id_performance',$id_performance)->update([
                'triwulan1'=>$data['T1'],
                'triwulan2'=>$data['T2'],
                'triwulan3'=>$data['T3'],
                'triwulan4'=>$data['T4'],
                'grade1'=>$data['G1'],
                'grade2'=>$data['G2'],
                'grade3'=>$data['G3'],
                'grade4'=>$data['G4'],
                'average'=>$data['TAve'],
                'grade'=>$data['GAve'],
                'ranked'=>$ranked,
                'ranked_bod'=>$ranked,
                'triwulan11'=>$data['1T1'],
                'triwulan21'=>$data['1T2'],
                'triwulan31'=>$data['1T3'],
                'triwulan41'=>$data['1T4'],
                'triwulan12'=>$data['2T1'],
                'triwulan22'=>$data['2T2'],
                'triwulan32'=>$data['2T3'],
                'triwulan42'=>$data['2T4'],
            ]);
        }
        $now=date('Y-m-d H:i:s');
        $update_performance=DB::table('tb_performance')->where('id',$id_performance)->update(['status_dibuka'=>'1','tgl_dibuka'=>$now,'status_penilai_1'=>'1']);
        $ttd_leader='ttd_leader'.$data->triwulan;
        $tgl_ttd_leader='tgl_ttd_leader'.$data->triwulan;
        $ttd=DB::table('tb_performance_recap')->where('id_performance',$data->id_performance)->update([
            $ttd_leader=>'1',
            $tgl_ttd_leader=>$now,
        ]);
        $tb_performance=DB::table('tb_performance')->where('id',$id_performance)->get();
        $periode='0';
        foreach($tb_performance as $dt){
            $periode=$dt->periode;
        }
        if($proses)$hasil=$periode;
        return $hasil;
    }
    function preview($id_performance){
        $tb_performance=DB::table('tb_performance')->where('id',$id_performance)->get();
        foreach($tb_performance as $dt){
            $jabatan=$dt->kategori_jabatan;
            $periode=$dt->periode;
        }
        // $tb_performance_detail=DB::table('tb_performance_detail')->where('id_performance',$id_performance)->get();
        $tb_performance_detail=DB::table('tb_performance_detail')->where('id_performance',$id_performance)->where('created_by','1')->get();
        $data['T1']=0;
        $data['T2']=0;
        $data['T3']=0;
        $data['T4']=0;
        $data['G1']=0;
        $data['G2']=0;
        $data['G3']=0;
        $data['G4']=0;
        foreach($tb_performance_detail as $dt){
            $data[$dt->id_performance.$dt->triwulan.$dt->id_aspek]=$dt->value;
            if($dt->triwulan==1)$data['T1']=$data['T1']+$dt->value;
            if($dt->triwulan==2)$data['T2']=$data['T2']+$dt->value;
            if($dt->triwulan==3)$data['T3']=$data['T3']+$dt->value;
            if($dt->triwulan==4)$data['T4']=$data['T4']+$dt->value;
        }
        $qty=4;
        $sum=0;
        for($i=1;$i<=4;$i++){
            if($data['T'.$i]==0){
                $qty--;
            }
            $sum=$sum+$data['T'.$i];

            $tb_perform_grade=DB::table('tb_perform_grade')->where('range_bawah','<',$data['T'.$i])->where('range_atas','>=',$data['T'.$i])->get();
            foreach($tb_perform_grade as $dt){
                $data['G'.$i]=$dt->grade;
            }
        }
        if($qty==0){
            $data['TAve']=0;
            $data['GAve']=0;
        }else{
            $data['TAve']=$sum/$qty;
        }
        //return $data['T1'].'+'.$data['T2'].' '.$data['TAve'].'='.$sum.'/'.$qty;
        $tb_perform_grade=DB::table('tb_perform_grade')->where('range_bawah','<',$data['TAve'])->where('range_atas','>=',$data['TAve'])->get();

        foreach($tb_perform_grade as $dt){
            $data['GAve']=$dt->grade;
        }
        //return $data['T1'].' '.$data['T2'].' '.$data['T3'].' '.$data['T4'].' = '.$sum.' '.$qty;
        

        $tb_performance_recap=DB::table('tb_performance_recap')->where('id_performance',$id_performance)->count();
        if($tb_performance_recap==0){
            $add=DB::table('tb_performance_recap')->insert([
                'id_performance'=>$id_performance,
                'triwulan1'=>$data['T1'],
                'triwulan2'=>$data['T2'],
                'triwulan3'=>$data['T3'],
                'triwulan4'=>$data['T4'],
                'grade1'=>$data['G1'],
                'grade2'=>$data['G2'],
                'grade3'=>$data['G3'],
                'grade4'=>$data['G4'],
                'average'=>$data['TAve'],
                'grade'=>$data['GAve'],
            ]);
        }else{
            $edit=DB::table('tb_performance_recap')->where('id_performance',$id_performance)->update([
                'triwulan1'=>$data['T1'],
                'triwulan2'=>$data['T2'],
                'triwulan3'=>$data['T3'],
                'triwulan4'=>$data['T4'],
                'grade1'=>$data['G1'],
                'grade2'=>$data['G2'],
                'grade3'=>$data['G3'],
                'grade4'=>$data['G4'],
                'average'=>$data['TAve'],
                'grade'=>$data['GAve'],
            ]);
        }

        $tb_performance_recap=DB::table('tb_performance_recap')->where('id_performance',$id_performance)->get();
        foreach($tb_performance_recap as $dt){
            $data['TTD1']=$dt->ttd1;
            $data['TTD2']=$dt->ttd2;
            $data['TTD3']=$dt->ttd3;
            $data['TTD4']=$dt->ttd4;
            $data['TTDL1']=$dt->ttd_leader1;
            $data['TTDL2']=$dt->ttd_leader2;
            $data['TTDL3']=$dt->ttd_leader3;
            $data['TTDL4']=$dt->ttd_leader4;
            $data['TGLTTD1']=$dt->tgl_ttd1;
            $data['TGLTTD2']=$dt->tgl_ttd2;
            $data['TGLTTD3']=$dt->tgl_ttd3;
            $data['TGLTTD4']=$dt->tgl_ttd4;
            $data['TGLTTDL1']=$dt->tgl_ttd_leader1;
            $data['TGLTTDL2']=$dt->tgl_ttd_leader2;
            $data['TGLTTDL3']=$dt->tgl_ttd_leader3;
            $data['TGLTTDL4']=$dt->tgl_ttd_leader4;
            $data['NOTE']=$dt->catatan;
        }
        $year=$periode;
        for($i=1;$i<=12;$i++){
            if(strlen($i)==1)$j='0'.$i;
            else $j=$i;
            $data['P'.$i]=date('F',strtotime($year.'-'.$j.'-01'));
            $data['K'.$i]='';
            $data['U'.$i]='';
            $data['A'.$i]='';
            $tb_performance_kendala=DB::table('tb_performance_kendala')->where('id_performance',$id_performance)->where('bulan',$i)->get();
            foreach($tb_performance_kendala as $dt){
                $data['K'.$i]=$dt->kendala;
                $data['U'.$i]=$dt->upaya;
                $data['A'.$i]=$dt->ttd_training;
            }
        }

        $tb_perform_grade=DB::table('tb_perform_grade')->get();
        $tb_perform_aspek=DB::table('tb_perform_aspek')->where('jabatan',$jabatan)->orderby('id','asc')->get();
        $tb_perform_value=DB::table('tb_perform_value')
        ->leftjoin('tb_perform_aspek','tb_perform_aspek.id','=','tb_perform_value.id_aspek')
        ->where('jabatan',$jabatan)
        ->orderby('tb_perform_value.id','asc')->get();
        foreach($tb_perform_value as $dt){
            $data[$dt->id_aspek.$dt->grade.$dt->level]=$dt->nilai;
            $data[$dt->id_aspek.$dt->grade]=$dt->kriteria;
        }
        $page=strtolower($jabatan);
        if($periode>='2025')$page.='_2025';
        $pdf = PDF::loadview('page/performance/performance_'.$page,['tb_performance'=>$tb_performance,'tb_perform_grade'=>$tb_perform_grade,'tb_perform_aspek'=>$tb_perform_aspek,'id_performance'=>$id_performance,'data'=>$data,'year'=>$year])->setPaper('a3','potret');
        return $pdf->stream('Performance.pdf');
    }
    function durasi($awal,$akhir){
        $tanggal_awal = new DateTime($awal);
        $tanggal_akhir = new DateTime($akhir);
        
        // Menghitung perbedaan antara tanggal
        $interval = $tanggal_awal->diff($tanggal_akhir);
        
        $durasi_tahun=$interval->y;
        $durasi_bulan=$interval->m;
        $durasi=($durasi_tahun*12)+$durasi_bulan;
        return $durasi;
    }
    function durasi_text($awal,$akhir){
        $tanggal_awal = new DateTime($awal);
        $tanggal_akhir = new DateTime($akhir);
        
        // Menghitung perbedaan antara tanggal
        $interval = $tanggal_awal->diff($tanggal_akhir);
        
        $durasi_tahun=$interval->y;
        $durasi_bulan=$interval->m;
        if($durasi_tahun>0)$tahun=$durasi_tahun.' Tahun';
        else $tahun='';
        if($durasi_bulan>0)$bulan=$durasi_bulan.' Bulan';
        else $bulan='';
        $durasi=$tahun.' '.$bulan;
        return $durasi;
    }
    function performanceInfo(request $data){
        $tb_performance_kendala=DB::table('tb_performance_kendala')->where('id_performance',$data->idperformance)->get();
        $konten="<table id='table1' class='table'><thead><tr><th>Periode</th><th>Kendala</th><th>Upaya/Antisipasi</th></tr></thead>";
        $konten.="<tbody>";
        foreach($tb_performance_kendala as $dt){
            $konten.="<tr><td>".$dt->bulan."</td><td>".$dt->kendala."</td><td>".$dt->upaya;
            $konten.="<i class='fa fa-trash-o pull-right delete' style='color:#C00;cursor:pointer;' data-idkendala='".$dt->id."'></i>";
            $konten.="</td></tr>";
        }
        $konten.="</tbody></table>";
        return $konten;
    }
    function performanceInfoSave(request $data){
        $hasil="No Action";
        $nama=Auth::user()->name;
        $tb_performance=DB::table('tb_performance')->where('id',$data->idperformance)->update(['target'=>$data->target]);
        if($tb_performance)$hasil="Sukses";
        if($data->bulan>0&&$data->kendala!=''){
            $cek=DB::table('tb_performance_kendala')->where('id_performance',$data->idperformance)->where('bulan',$data->bulan)->count();
            if($cek==0){
                $proses=DB::table('tb_performance_kendala')->insert([
                    'id_performance'=>$data->idperformance,
                    'bulan'=>$data->bulan,
                    'kendala'=>$data->kendala,
                    'upaya'=>$data->upaya,
                    'admin'=>$nama,
                ]);
            }else{
                $proses=DB::table('tb_performance_kendala')->where('id_performance',$data->idperformance)->where('bulan',$data->bulan)->update([
                    'bulan'=>$data->bulan,
                    'kendala'=>$data->kendala,
                    'upaya'=>$data->upaya,
                    'admin'=>$nama,
                ]);
            }
            if($proses)$hasil="Sukses";
        }
        //if($tb_performance||$proses)$hasil='Sukses';
        //else $hasil=$data->idperformance.' & '.$data->target;
        return $hasil;
    }
    function performanceInfoDelete(request $data){
        $delete=DB::table('tb_performance_kendala')->where('id',$data->idkendala)->delete();
        if($delete)return "Sukses";
        else return $data->idkendala;
    }
    function performanceRecap(request $data){
        $email=Auth::user()->email;
        $cek1=DB::table('tb_emails')->where('email_address',$email)->get();
        foreach($cek1 as $dt){$id_user=$dt->id_employee;}
        $cek=DB::table('tb_performance')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_performance.id_employee')
        ->leftjoin('tb_employees as tb_employees1','tb_employees.leader_id','=','tb_employees1.id')
        ->leftjoin('tb_employees as tb_employees2','tb_employees1.leader_id','=','tb_employees2.id')
        ->where('tb_performance.id',$data->idperformance)
        ->get(['tb_employees.leader_id','tb_employees1.leader_id as leader2','tb_employees1.employee_name as nama1','tb_employees2.employee_name as nama2']);
        foreach($cek as $dt){
            $leader_id=$dt->leader_id;
            $leader2=$dt->leader2;
            $data['id1']=$leader_id;
            $data['id2']=$leader2;
            $data['nama1']=$dt->nama1;
            $data['nama2']=$dt->nama2;
        }
        $id_leader=!empty($leader_id) ? $leader_id : '0';
        $id_leader2=!empty($leader2) ? $leader2 : '0';
        if($id_user==$id_leader){
            $data['akses']=1;
            $data['pos']=1;
        }else if($id_user==$id_leader2){
            $data['akses']=1;
            $data['pos']=2;
        }else{
            $data['akses']=0;
        }
        $cek_direktur=DB::table('tb_employees')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->where('tb_employees.id',$id_user)
        ->get(['position_index','employee_name','tb_employees.id']);
        foreach ($cek_direktur as $dt) {
            if($dt->position_index>=9){
                $data['direktur']=1;
                $data['nama_direktur']=$dt->employee_name;
            }else {
                $data['direktur']=0;
                $data['nama_direktur']='';
            }
            $posisi=$dt->position_index;
            $data['id3']=$dt->id;
        }

        $tb_performance=DB::table('tb_performance')->where('id',$data->idperformance)->get();
        foreach($tb_performance as $dt){
            $jabatan=$dt->kategori_jabatan;
            $data['note1']=$dt->catatan_penilai_1;
            $data['note2']=$dt->catatan_penilai_2;
        }
        $tb_perform_aspek=DB::table('tb_perform_aspek')->where('jabatan',$jabatan)->orderby('id','asc')->get();

        $tb_performance_detail=DB::table('tb_performance_detail')->where('id_performance',$data->idperformance)->get();
        $data['T1']=0;
        $data['T2']=0;
        $data['T3']=0;
        $data['T4']=0;
        $data['G1']=0;
        $data['G2']=0;
        $data['G3']=0;
        $data['G4']=0;
        foreach($tb_performance_detail as $dt){
            $data[$dt->id_performance.$dt->triwulan.$dt->id_aspek]=$dt->value;
            if($dt->triwulan==1)$data['T1']=$data['T1']+$dt->value;
            if($dt->triwulan==2)$data['T2']=$data['T2']+$dt->value;
            if($dt->triwulan==3)$data['T3']=$data['T3']+$dt->value;
            if($dt->triwulan==4)$data['T4']=$data['T4']+$dt->value;
        }
        $qty=4;
        $sum=0;
        for($i=1;$i<=4;$i++){
            if($data['T'.$i]==0){
                $qty--;
            }
            $sum=$sum+$data['T'.$i];

            $tb_perform_grade=DB::table('tb_perform_grade')->where('range_bawah','<',$data['T'.$i])->where('range_atas','>=',$data['T'.$i])->get();
            foreach($tb_perform_grade as $dt){
                $data['G'.$i]=$dt->grade;
            }
        }
        if($qty==0){
            $data['TAve']=0;
            $data['GAve']=0;
        }else{
            $data['TAve']=$sum/$qty;
        }
        
        $tb_perform_grade=DB::table('tb_perform_grade')->where('range_bawah','<',$data['TAve'])->where('range_atas','>=',$data['TAve'])->get();
        foreach($tb_perform_grade as $dt){
            $data['GAve']=$dt->grade;
        }

        $tb_performance_detail=DB::table('tb_performance_detail')
        ->leftjoin('tb_perform_aspek','tb_perform_aspek.id','=','tb_performance_detail.id_aspek')
        ->where('id_performance',$data->idperformance)
        ->get('tb_performance_detail.*','tb_perform_aspek.nama_aspek');

        return view('page/performance/performance_recap',['menu'=>'performance','date'=>$data,'tb_performance_detail'=>$tb_performance_detail,'employee'=>$data->employee_name,'periode'=>$data->periode,'tb_perform_aspek'=>$tb_perform_aspek,'id_performance'=>$data->idperformance,'data'=>$data,'tb_performance'=>$tb_performance]);
    }
    function performanceRecap2($idperformance,$periode,$employee_name){
        // $data['id_performance']=$idperformance;
        $email=Auth::user()->email;
        $cek1=DB::table('tb_emails')->where('email_address',$email)->get();
        foreach($cek1 as $dt){$id_user=$dt->id_employee;}
        $cek=DB::table('tb_performance')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_performance.id_employee')
        ->leftjoin('tb_employees as tb_employees1','tb_employees.leader_id','=','tb_employees1.id')
        ->leftjoin('tb_employees as tb_employees2','tb_employees1.leader_id','=','tb_employees2.id')
        ->where('tb_performance.id',$idperformance)
        ->get(['tb_employees.leader_id','tb_employees1.leader_id as leader2','tb_employees1.employee_name as nama1','tb_employees2.employee_name as nama2']);
        foreach($cek as $dt){
            $leader_id=$dt->leader_id;
            $leader2=$dt->leader2;
            $data['id1']=$leader_id;
            $data['id2']=$leader2;
            $data['nama1']=$dt->nama1;
            $data['nama2']=$dt->nama2;
        }
        $id_leader=!empty($leader_id) ? $leader_id : '0';
        $id_leader2=!empty($leader2) ? $leader2 : '0';
        if($id_user==$id_leader){
            $data['akses']=1;
            $data['pos']=1;
        }else if($id_user==$id_leader2){
            $data['akses']=1;
            $data['pos']=2;
        }else{
            $data['akses']=0;
        }
        $cek_direktur=DB::table('tb_employees')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->where('tb_employees.id',$id_user)
        ->get(['position_index','employee_name','tb_employees.id']);
        foreach ($cek_direktur as $dt) {
            if($dt->position_index>=9){
                $data['direktur']=1;
                $data['nama_direktur']=$dt->employee_name;
            }else {
                $data['direktur']=0;
                $data['nama_direktur']='';
            }
            $posisi=$dt->position_index;
            $data['id3']=$dt->id;
        }

        $tb_performance=DB::table('tb_performance')->where('id',$idperformance)->get();
        foreach($tb_performance as $dt){
            $jabatan=$dt->kategori_jabatan;
            $data['note1']=$dt->catatan_penilai_1;
            $data['note2']=$dt->catatan_penilai_2;
        }
        $tb_perform_aspek=DB::table('tb_perform_aspek')->where('jabatan',$jabatan)->orderby('id','asc')->get();

        $tb_performance_detail=DB::table('tb_performance_detail')->where('id_performance',$idperformance)->get();
        $data['T1']=0;
        $data['T2']=0;
        $data['T3']=0;
        $data['T4']=0;
        $data['G1']=0;
        $data['G2']=0;
        $data['G3']=0;
        $data['G4']=0;
        foreach($tb_performance_detail as $dt){
            $data[$dt->id_performance.$dt->triwulan.$dt->id_aspek]=$dt->value;
            if($dt->triwulan==1)$data['T1']=$data['T1']+$dt->value;
            if($dt->triwulan==2)$data['T2']=$data['T2']+$dt->value;
            if($dt->triwulan==3)$data['T3']=$data['T3']+$dt->value;
            if($dt->triwulan==4)$data['T4']=$data['T4']+$dt->value;
        }
        $qty=4;
        $sum=0;
        for($i=1;$i<=4;$i++){
            if($data['T'.$i]==0){
                $qty--;
            }
            $sum=$sum+$data['T'.$i];

            $tb_perform_grade=DB::table('tb_perform_grade')->where('range_bawah','<',$data['T'.$i])->where('range_atas','>=',$data['T'.$i])->get();
            foreach($tb_perform_grade as $dt){
                $data['G'.$i]=$dt->grade;
            }
        }
        if($qty==0){
            $data['TAve']=0;
            $data['GAve']=0;
        }else{
            $data['TAve']=$sum/$qty;
        }
        
        $tb_perform_grade=DB::table('tb_perform_grade')->where('range_bawah','<',$data['TAve'])->where('range_atas','>=',$data['TAve'])->get();
        foreach($tb_perform_grade as $dt){
            $data['GAve']=$dt->grade;
        }

        $tb_performance_detail=DB::table('tb_performance_detail')
        ->leftjoin('tb_perform_aspek','tb_perform_aspek.id','=','tb_performance_detail.id_aspek')
        ->where('id_performance',$idperformance)
        ->get('tb_performance_detail.*','tb_perform_aspek.nama_aspek');

        return view('page/performance/performance_recap',['menu'=>'performance','date'=>$data,'tb_performance_detail'=>$tb_performance_detail,'employee'=>$employee_name,'periode'=>$periode,'tb_perform_aspek'=>$tb_perform_aspek,'id_performance'=>$idperformance,'data'=>$data,'tb_performance'=>$tb_performance]);
    }
    function performanceRecapSave(request $data){
        $now=date('Y-m-d H:i:s');
        if($data->pos==1){
            $sign=DB::table('tb_performance')->where('id',$data->idperformance)->update([
                'status_penilai_1'=>'1',
                'tgl_penilai_1'=>$now,
                'catatan_penilai_1'=>$data->note1,
                'id1'=>$data->idleader,
            ]);
        }else if($data->pos==2){
            $sign=DB::table('tb_performance')->where('id',$data->idperformance)->update([
                'status_penilai_2'=>'1',
                'tgl_penilai_2'=>$now,
                'nama_penilai_2'=>$data->nama,
                'catatan_penilai_2'=>$data->note2,
                'id2'=>$data->idleader,
            ]);
        }else{
            $sign=DB::table('tb_performance')->where('id',$data->idperformance)->update([
                'status_direktur'=>'1',
                'tgl_direktur'=>$now,
                'nama_direktur'=>$data->nama,
                'id3'=>$data->idleader,
            ]);
        }
        if($sign)return "Sukses";
        else return $data->idperformance.' '.$data->pos.' '.$data->nama.' '.$data->note1.' '.$data->note2;
    }
    function performanceAll($periode,$dept,$nm_level){
        if($periode==0)$periode=date('Y');
        if($dept==0)$dept="All Dept";
        if($nm_level==0)$nm_level="All Level";
        $nama=Auth::user()->name;
        $email=Auth::user()->email;
        $cek1=DB::table('tb_emails')->where('email_address',$email)->get();
        foreach($cek1 as $dt){$id_user=$dt->id_employee;}
        $cari_index=DB::table('tb_employees')->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')->where('tb_employees.id',$id_user)->get(['id_level']);
        foreach($cari_index as $dt_index){
            $level_saya=$dt_index->id_level;
        }
        $tb_admins=DB::table('tb_admins')->leftjoin('tb_departments','tb_departments.id','=','tb_admins.dept_id')->where('id_employee',$id_user)->get(['tb_admins.*','tb_departments.dept_code']);

        $tb_aku=DB::table('tb_employees')->where('leader_id',$id_user)->get();
        $tb_anak=DB::table('tb_employees')->where('leader_id',$id_user);
        foreach($tb_aku as $dt){
            $tb_anak=$tb_anak->orwhere('leader_id',$dt->id);
        }
        $tb_anak=$tb_anak->get();

        $tb_cucu=DB::table('tb_employees')->where('leader_id',$id_user);
        foreach($tb_anak as $dt){
            $tb_cucu=$tb_cucu->orwhere('leader_id',$dt->id);
        }
        $tb_cucu=$tb_cucu->get();

        $tb_buyut=DB::table('tb_employees')->where('leader_id',$id_user);
        foreach($tb_anak as $dt){
            $tb_buyut=$tb_buyut->orwhere('leader_id',$dt->id);
        }
        foreach($tb_cucu as $dt){
            $tb_buyut=$tb_buyut->orwhere('leader_id',$dt->id);
        }
        $tb_buyut=$tb_buyut->get();

        if($nm_level=="All Level"&&$dept=="All Dept"){
            $tb_employee=DB::table('tb_employees')
            ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_employees.leader_id')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
            ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
            ->leftjoin('tb_level', function($join) use ($nm_level) {
                $join->on('tb_level.id_level', '=', 'tb_positions.id_level');
                     //->where('tb_level.nama_level', '=', $nm_level);
            })        
            ->leftjoin('tb_performance', function($join) use ($periode) {
                $join->on('tb_performance.id_employee', '=', 'tb_employees.id')
                     ->where('tb_performance.periode', '=', $periode);
            })        
            ->leftjoin('tb_performance_recap','tb_performance_recap.id_performance','=','tb_performance.id');
            $tb_employee=$tb_employee->where([['tb_employees.leader_id',$id_user],['tb_employees.status','1'],['tb_performance.status_penilai_2', '=', '1']]);
            foreach($tb_buyut as $dt2){
                $tb_employee=$tb_employee->orwhere([['tb_employees.leader_id',$dt2->id],['tb_employees.status','1'],['tb_performance.status_penilai_2', '=', '1']]);
            }
            $tb_employee=$tb_employee->orderby('tb_performance_recap.average','desc')
            ->get(['tb_employees.*','tb_performance.atasan_langsung','tb_employees1.employee_name as leader_name','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_index','tb_positions.position_name','tb_positions.id_level','tb_performance.id as idperformance','tb_performance.masa_kerja_member','tb_performance_recap.triwulan1','tb_performance_recap.triwulan2','tb_performance_recap.triwulan3','tb_performance_recap.triwulan4','tb_performance_recap.average','tb_performance_recap.grade','tb_performance.target','tb_level.nama_level','tb_performance_recap.ranked']);
            
            $jumlah=DB::table('tb_employees')
            ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_employees.leader_id')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
            ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
            ->leftjoin('tb_level', function($join) use ($nm_level) {
                $join->on('tb_level.id_level', '=', 'tb_positions.id_level');
                     //->where('tb_level.nama_level', '=', $nm_level);
            })        
            ->leftjoin('tb_performance', function($join) use ($periode) {
                $join->on('tb_performance.id_employee', '=', 'tb_employees.id')
                     ->where('tb_performance.periode', '=', $periode);
            })        
            ->leftjoin('tb_performance_recap','tb_performance_recap.id_performance','=','tb_performance.id');
            $jumlah=$jumlah->where([['tb_employees.leader_id',$id_user],['tb_employees.status','1'],['tb_performance.status_penilai_2', '=', '1']]);
            foreach($tb_buyut as $dt2){
                $jumlah=$jumlah->orwhere([['tb_employees.leader_id',$dt2->id],['tb_employees.status','1'],['tb_performance.status_penilai_2', '=', '1']]);
            }
            $jumlah=$jumlah->orderby('tb_performance_recap.average','desc')
            ->count();
        }else if($nm_level!="All Level"&&$dept=="All Dept"){

            $tb_employee=DB::table('tb_employees')
            ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_employees.leader_id')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
            ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
            ->leftjoin('tb_level', function($join) use ($nm_level) {
                $join->on('tb_level.id_level', '=', 'tb_positions.id_level')
                     ->where('tb_level.nama_level', '=', $nm_level);
            })        
            ->leftjoin('tb_performance', function($join) use ($periode) {
                $join->on('tb_performance.id_employee', '=', 'tb_employees.id')
                     ->where('tb_performance.periode', '=', $periode);
            })        
            ->leftjoin('tb_performance_recap','tb_performance_recap.id_performance','=','tb_performance.id');
            $tb_employee=$tb_employee->where([['tb_employees.leader_id',$id_user],['tb_employees.status','1'],['tb_level.nama_level',$nm_level],['tb_performance.status_penilai_2', '=', '1']]);
            foreach($tb_buyut as $dt2){
                $tb_employee=$tb_employee->orwhere([['tb_employees.leader_id',$dt2->id],['tb_employees.status','1'],['tb_level.nama_level',$nm_level],['tb_performance.status_penilai_2', '=', '1']]);
            }
            $tb_employee=$tb_employee->orderby('tb_performance_recap.average','desc')
            ->get(['tb_employees.*','tb_performance.atasan_langsung','tb_employees1.employee_name as leader_name','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_index','tb_positions.position_name','tb_positions.id_level','tb_performance.id as idperformance','tb_performance.masa_kerja_member','tb_performance_recap.triwulan1','tb_performance_recap.triwulan2','tb_performance_recap.triwulan3','tb_performance_recap.triwulan4','tb_performance_recap.average','tb_performance_recap.grade','tb_performance.target','tb_level.nama_level','tb_performance_recap.ranked']);
   
            $jumlah=DB::table('tb_employees')
            ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_employees.leader_id')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
            ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
            ->leftjoin('tb_level', function($join) use ($nm_level) {
                $join->on('tb_level.id_level', '=', 'tb_positions.id_level')
                     ->where('tb_level.nama_level', '=', $nm_level);
            })        
            ->leftjoin('tb_performance', function($join) use ($periode) {
                $join->on('tb_performance.id_employee', '=', 'tb_employees.id')
                     ->where('tb_performance.periode', '=', $periode);
            })        
            ->leftjoin('tb_performance_recap','tb_performance_recap.id_performance','=','tb_performance.id');
            $jumlah=$jumlah->where([['tb_employees.leader_id',$id_user],['tb_employees.status','1'],['tb_level.nama_level',$nm_level],['tb_performance.status_penilai_2', '=', '1']]);
            foreach($tb_buyut as $dt2){
                $jumlah=$jumlah->orwhere([['tb_employees.leader_id',$dt2->id],['tb_employees.status','1'],['tb_level.nama_level',$nm_level],['tb_performance.status_penilai_2', '=', '1']]);
            }
            $jumlah=$jumlah->orderby('tb_performance_recap.average','desc')
            ->count();
        }else if($nm_level=="All Level"&&$dept!="All Dept"){

            $tb_employee=DB::table('tb_employees')
            ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_employees.leader_id')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
            ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
            ->leftjoin('tb_level', function($join) use ($nm_level) {
                $join->on('tb_level.id_level', '=', 'tb_positions.id_level');
                     //->where('tb_level.nama_level', '=', $nm_level);
            })        
            ->leftjoin('tb_performance', function($join) use ($periode) {
                $join->on('tb_performance.id_employee', '=', 'tb_employees.id')
                     ->where('tb_performance.periode', '=', $periode);
            })        
            ->leftjoin('tb_performance_recap','tb_performance_recap.id_performance','=','tb_performance.id');
            $tb_employee=$tb_employee->where([['tb_employees.leader_id',$id_user],['tb_employees.status','1'],['tb_performance.Department',$dept],['tb_performance.status_penilai_2', '=', '1']]);
            foreach($tb_buyut as $dt2){
                $tb_employee=$tb_employee->orwhere([['tb_employees.leader_id',$dt2->id],['tb_employees.status','1'],['tb_performance.Department',$dept],['tb_performance.status_penilai_2', '=', '1']]);
            }
            $tb_employee=$tb_employee->orderby('tb_performance_recap.average','desc')
            ->get(['tb_employees.*','tb_performance.atasan_langsung','tb_employees1.employee_name as leader_name','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_index','tb_positions.position_name','tb_positions.id_level','tb_performance.id as idperformance','tb_performance.masa_kerja_member','tb_performance_recap.triwulan1','tb_performance_recap.triwulan2','tb_performance_recap.triwulan3','tb_performance_recap.triwulan4','tb_performance_recap.average','tb_performance_recap.grade','tb_performance.target','tb_level.nama_level','tb_performance_recap.ranked']);
   
            $jumlah=DB::table('tb_employees')
            ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_employees.leader_id')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
            ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
            ->leftjoin('tb_level', function($join) use ($nm_level) {
                $join->on('tb_level.id_level', '=', 'tb_positions.id_level');
                     //->where('tb_level.nama_level', '=', $nm_level);
            })        
            ->leftjoin('tb_performance', function($join) use ($periode) {
                $join->on('tb_performance.id_employee', '=', 'tb_employees.id')
                     ->where('tb_performance.periode', '=', $periode);
            })        
            ->leftjoin('tb_performance_recap','tb_performance_recap.id_performance','=','tb_performance.id');
            $jumlah=$jumlah->where([['tb_employees.leader_id',$id_user],['tb_employees.status','1'],['tb_performance.Department',$dept],['tb_performance.status_penilai_2', '=', '1']]);
            foreach($tb_buyut as $dt2){
                $jumlah=$jumlah->orwhere([['tb_employees.leader_id',$dt2->id],['tb_employees.status','1'],['tb_performance.Department',$dept],['tb_performance.status_penilai_2', '=', '1']]);
            }
            $jumlah=$jumlah->orderby('tb_performance_recap.average','desc')
            ->count();
        }else{

            $tb_employee=DB::table('tb_employees')
            ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_employees.leader_id')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
            ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
            ->leftjoin('tb_level', function($join) use ($nm_level) {
                $join->on('tb_level.id_level', '=', 'tb_positions.id_level')
                     ->where('tb_level.nama_level', '=', $nm_level);
            })        
            ->leftjoin('tb_performance', function($join) use ($periode) {
                $join->on('tb_performance.id_employee', '=', 'tb_employees.id')
                     ->where('tb_performance.periode', '=', $periode);
            })        
            ->leftjoin('tb_performance_recap','tb_performance_recap.id_performance','=','tb_performance.id');
            $tb_employee=$tb_employee->where([['tb_employees.leader_id',$id_user],['tb_employees.status','1'],['tb_level.nama_level',$nm_level],['tb_performance.Department',$dept],['tb_performance.status_penilai_2', '=', '1']]);
            foreach($tb_buyut as $dt2){
                $tb_employee=$tb_employee->orwhere([['tb_employees.leader_id',$dt2->id],['tb_employees.status','1'],['tb_level.nama_level',$nm_level],['tb_performance.Department',$dept],['tb_performance.status_penilai_2', '=', '1']]);
            }
            $tb_employee=$tb_employee->orderby('tb_performance_recap.average','desc')
            ->get(['tb_employees.*','tb_performance.atasan_langsung','tb_employees1.employee_name as leader_name','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_index','tb_positions.position_name','tb_positions.id_level','tb_performance.id as idperformance','tb_performance.masa_kerja_member','tb_performance_recap.triwulan1','tb_performance_recap.triwulan2','tb_performance_recap.triwulan3','tb_performance_recap.triwulan4','tb_performance_recap.average','tb_performance_recap.grade','tb_performance.target','tb_level.nama_level','tb_performance_recap.ranked']);

            $jumlah=DB::table('tb_employees')
            ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_employees.leader_id')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
            ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
            ->leftjoin('tb_level', function($join) use ($nm_level) {
                $join->on('tb_level.id_level', '=', 'tb_positions.id_level')
                     ->where('tb_level.nama_level', '=', $nm_level);
            })        
            ->leftjoin('tb_performance', function($join) use ($periode) {
                $join->on('tb_performance.id_employee', '=', 'tb_employees.id')
                     ->where('tb_performance.periode', '=', $periode);
            })        
            ->leftjoin('tb_performance_recap','tb_performance_recap.id_performance','=','tb_performance.id');
            $jumlah=$jumlah->where([['tb_employees.leader_id',$id_user],['tb_employees.status','1'],['tb_level.nama_level',$nm_level],['tb_performance.Department',$dept],['tb_performance.status_penilai_2', '=', '1']]);
            foreach($tb_buyut as $dt2){
                $jumlah=$jumlah->orwhere([['tb_employees.leader_id',$dt2->id],['tb_employees.status','1'],['tb_level.nama_level',$nm_level],['tb_performance.Department',$dept],['tb_performance.status_penilai_2', '=', '1']]);
            }
            $jumlah=$jumlah->orderby('tb_performance_recap.average','desc')
            ->count();
        }
        
        // if($nm_level!="All Level"){
        //     $jumlah=DB::table('tb_employees')
        //     ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_employees.leader_id')
        //     ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        //     ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        //     ->leftjoin('tb_level', function($join) use ($nm_level) {
        //         $join->on('tb_level.id_level', '=', 'tb_positions.id_level')
        //              ->where('tb_level.nama_level', '=', $nm_level);
        //     })        
        //     ->leftjoin('tb_performance', function($join) use ($periode) {
        //         $join->on('tb_performance.id_employee', '=', 'tb_employees.id')
        //              ->where('tb_performance.periode', '=', $periode);
        //     })        
        //     ->leftjoin('tb_performance_recap','tb_performance_recap.id_performance','=','tb_performance.id')
        //     //->where('tb_performance.status_penilai_2', '=', '1')
        //     ->where('tb_employees.status','1');
        //     if($dept!="All Dept"){
        //         $jumlah=$jumlah->where('tb_performance.Department',$dept);
        //     }
        //     if($nm_level!="All Level"){
        //         $jumlah=$jumlah->where('tb_level.nama_level',$nm_level);
        //     }
        //     $jumlah=$jumlah->count();
        // }else{
        //     $jumlah=DB::table('tb_employees')
        //     ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_employees.leader_id')
        //     ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        //     ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        //     ->leftjoin('tb_level', function($join) use ($nm_level) {
        //         $join->on('tb_level.id_level', '=', 'tb_positions.id_level')
        //              ->where('tb_level.nama_level', '=', $nm_level);
        //     })        
        //     ->leftjoin('tb_performance', function($join) use ($periode) {
        //         $join->on('tb_performance.id_employee', '=', 'tb_employees.id')
        //              ->where('tb_performance.periode', '=', $periode);
        //     })        
        //     ->leftjoin('tb_performance_recap','tb_performance_recap.id_performance','=','tb_performance.id')
        //     //->where('tb_performance.status_penilai_2', '=', '1')
        //     ->where('tb_employees.status','1');
        //     if($dept!="All Dept"){
        //         $jumlah=$jumlah->where('tb_performance.Department',$dept);
        //     }
        //     if($nm_level!="All Level"){
        //         $jumlah=$jumlah->where('tb_level.nama_level',$nm_level);
        //     }
        //     $jumlah=$jumlah->count();
        // }
        //return $tb_employee;
        
        $tb_rank=DB::table('tb_perform_rank')->orderby('rank','asc')->get();
        foreach($tb_rank as $dt){
            $rank[$dt->rank]=round($jumlah*$dt->kuota/100,0);
        }
        $j=0;
        for($i=1;$i<=5;$i++){
            $j=$j+$rank[$i];
            $level[$i]=$j;
        }
        $tb_department=DB::table('tb_departments')
        ->where([['isDelete','0'],['id','0']]);
        foreach($tb_admins as $dt){
            $tb_department=$tb_department->orWhere([['isDelete','0'],['id',$dt->dept_id]]);
            // if (request()->user()->hasRole('hr_access')){
            //     continue;
            // }else{
            //     if($dept=="All Dept"){
            //         $dept=$dt->dept_code;
            //     }
            // }
        }
        $tb_department=$tb_department->orderby('id','desc')->get();
        $tb_position=DB::table('tb_level')->where('id_level','>',$level_saya)->orderby('id_level','asc')->get(['tb_level.nama_level']);
        $no=0;
        foreach($tb_employee as $dt){
            $no++;
            if($no<=$level[1]){$ranked=1;}
            else if($no<=$level[2]){$ranked=2;}
            else if($no<=$level[3]){$ranked=3;}
            else if($no<=$level[4]){$ranked=4;}
            else {$ranked=5;}
            $see=DB::table('tb_performance_recap')->where('id_performance',$dt->idperformance)->whereNull('ranked')->count();
            if($see==1){
                $update=DB::table('tb_performance_recap')->where('id_performance',$dt->idperformance)->update(['ranked'=>$ranked,'ranked_bod'=>$ranked]);
            }
        }

        // $tb_employee=DB::table('tb_employees')
        // ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_employees.leader_id')
        // ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        // ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        // ->leftjoin('tb_level', function($join) use ($nm_level) {
        //     $join->on('tb_level.id_level', '=', 'tb_positions.id_level')
        //          ->where('tb_level.nama_level', '=', $nm_level);
        // })        
        // ->leftjoin('tb_performance', function($join) use ($periode) {
        //     $join->on('tb_performance.id_employee', '=', 'tb_employees.id')
        //          ->where('tb_performance.periode', '=', $periode);
        // })        
        // ->leftjoin('tb_performance_recap','tb_performance_recap.id_performance','=','tb_performance.id')
        // ->where('tb_performance.status_penilai_2', '=', '1')
        // ->where('tb_employees.status','1');
        // if($dept!="All Dept"){
        //     $tb_employee=$tb_employee->where('tb_performance.Department',$dept);
        // }
        // if($nm_level!="All Level"){
        //     $tb_employee=$tb_employee->where('tb_level.nama_level',$nm_level);
        // }
        // $tb_employee=$tb_employee->orderby('tb_performance_recap.average','desc')
        // ->get(['tb_employees.*','tb_employees1.employee_name as leader_name','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_index','tb_positions.position_name','tb_positions.id_level','tb_performance.id as idperformance','tb_performance.masa_kerja_member','tb_performance_recap.triwulan1','tb_performance_recap.triwulan2','tb_performance_recap.triwulan3','tb_performance_recap.triwulan4','tb_performance_recap.average','tb_performance_recap.grade','tb_performance.target','tb_level.nama_level','tb_performance_recap.ranked']);
        
        //return $jumlah;
        if (request()->user()->hasRole('root')||request()->user()->hasRole('hr_access')||request()->user()->hasRole('performance')||request()->user()->hasRole('leader')){
            return view('page/performance/performance_all',['menu'=>'performanceAll','tb_employee'=>$tb_employee,'periode'=>$periode,'jumlah'=>$jumlah,'level'=>$level,'tb_department'=>$tb_department,'tb_position'=>$tb_position,'nm_level'=>$nm_level,'dept'=>$dept]);
        }
    }
    function performanceAll_backup($periode,$dept,$nm_level){
        if($periode==0)$periode=date('Y');
        if($dept==0)$dept="All Dept";
        if($nm_level==0)$nm_level="All Level";
        $nama=Auth::user()->name;
        $email=Auth::user()->email;
        $cek1=DB::table('tb_emails')->where('email_address',$email)->get();
        foreach($cek1 as $dt){$id_user=$dt->id_employee;}
        $tb_admins=DB::table('tb_admins')->leftjoin('tb_departments','tb_departments.id','=','tb_admins.dept_id')->where('id_employee',$id_user)->get(['tb_admins.*','tb_departments.dept_code']);

        $tb_aku=DB::table('tb_employees')->where('leader_id',$id_user)->get();
        $tb_anak=DB::table('tb_employees')->where('leader_id',$id_user);
        foreach($tb_aku as $dt){
            $tb_anak=$tb_anak->orwhere('leader_id',$dt->id);
        }
        $tb_anak=$tb_anak->get();

        $tb_cucu=DB::table('tb_employees')->where('leader_id',$id_user);
        foreach($tb_anak as $dt){
            $tb_cucu=$tb_cucu->orwhere('leader_id',$dt->id);
        }
        $tb_cucu=$tb_cucu->get();

        $tb_buyut=DB::table('tb_employees')->where('leader_id',$id_user);
        foreach($tb_anak as $dt){
            $tb_buyut=$tb_buyut->orwhere('leader_id',$dt->id);
        }
        foreach($tb_cucu as $dt){
            $tb_buyut=$tb_buyut->orwhere('leader_id',$dt->id);
        }
        $tb_buyut=$tb_buyut->get();

        if($nm_level=="All Level"&&$dept=="All Dept"){
            $tb_employee=DB::table('tb_employees')
            ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_employees.leader_id')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
            ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
            ->leftjoin('tb_level', function($join) use ($nm_level) {
                $join->on('tb_level.id_level', '=', 'tb_positions.id_level');
                     //->where('tb_level.nama_level', '=', $nm_level);
            })        
            ->leftjoin('tb_performance', function($join) use ($periode) {
                $join->on('tb_performance.id_employee', '=', 'tb_employees.id')
                     ->where('tb_performance.periode', '=', $periode);
            })        
            ->leftjoin('tb_performance_recap','tb_performance_recap.id_performance','=','tb_performance.id');
            $tb_employee=$tb_employee->where([['tb_employees.leader_id',$id_user],['tb_employees.status','1'],['tb_performance.status_penilai_2', '=', '0']]);
            foreach($tb_buyut as $dt2){
                $tb_employee=$tb_employee->orwhere([['tb_employees.leader_id',$dt2->id],['tb_employees.status','1'],['tb_performance.status_penilai_2', '=', '0']]);
            }
            $tb_employee=$tb_employee->orderby('tb_performance_recap.average','desc')
            ->get(['tb_employees.*','tb_performance.atasan_langsung','tb_employees1.employee_name as leader_name','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_index','tb_positions.position_name','tb_positions.id_level','tb_performance.id as idperformance','tb_performance.masa_kerja_member','tb_performance_recap.triwulan1','tb_performance_recap.triwulan2','tb_performance_recap.triwulan3','tb_performance_recap.triwulan4','tb_performance_recap.average','tb_performance_recap.grade','tb_performance.target','tb_level.nama_level','tb_performance_recap.ranked']);
            
            $jumlah=DB::table('tb_employees')
            ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_employees.leader_id')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
            ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
            ->leftjoin('tb_level', function($join) use ($nm_level) {
                $join->on('tb_level.id_level', '=', 'tb_positions.id_level');
                     //->where('tb_level.nama_level', '=', $nm_level);
            })        
            ->leftjoin('tb_performance', function($join) use ($periode) {
                $join->on('tb_performance.id_employee', '=', 'tb_employees.id')
                     ->where('tb_performance.periode', '=', $periode);
            })        
            ->leftjoin('tb_performance_recap','tb_performance_recap.id_performance','=','tb_performance.id');
            $jumlah=$jumlah->where([['tb_employees.leader_id',$id_user],['tb_employees.status','1'],['tb_performance.status_penilai_2', '=', '0']]);
            foreach($tb_buyut as $dt2){
                $jumlah=$jumlah->orwhere([['tb_employees.leader_id',$dt2->id],['tb_employees.status','1'],['tb_performance.status_penilai_2', '=', '0']]);
            }
            $jumlah=$jumlah->orderby('tb_performance_recap.average','desc')
            ->count();
        }else if($nm_level!="All Level"&&$dept=="All Dept"){

            $tb_employee=DB::table('tb_employees')
            ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_employees.leader_id')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
            ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
            ->leftjoin('tb_level', function($join) use ($nm_level) {
                $join->on('tb_level.id_level', '=', 'tb_positions.id_level')
                     ->where('tb_level.nama_level', '=', $nm_level);
            })        
            ->leftjoin('tb_performance', function($join) use ($periode) {
                $join->on('tb_performance.id_employee', '=', 'tb_employees.id')
                     ->where('tb_performance.periode', '=', $periode);
            })        
            ->leftjoin('tb_performance_recap','tb_performance_recap.id_performance','=','tb_performance.id');
            $tb_employee=$tb_employee->where([['tb_employees.leader_id',$id_user],['tb_employees.status','1'],['tb_level.nama_level',$nm_level],['tb_performance.status_penilai_2', '=', '0']]);
            foreach($tb_buyut as $dt2){
                $tb_employee=$tb_employee->orwhere([['tb_employees.leader_id',$dt2->id],['tb_employees.status','1'],['tb_level.nama_level',$nm_level],['tb_performance.status_penilai_2', '=', '0']]);
            }
            $tb_employee=$tb_employee->orderby('tb_performance_recap.average','desc')
            ->get(['tb_employees.*','tb_performance.atasan_langsung','tb_employees1.employee_name as leader_name','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_index','tb_positions.position_name','tb_positions.id_level','tb_performance.id as idperformance','tb_performance.masa_kerja_member','tb_performance_recap.triwulan1','tb_performance_recap.triwulan2','tb_performance_recap.triwulan3','tb_performance_recap.triwulan4','tb_performance_recap.average','tb_performance_recap.grade','tb_performance.target','tb_level.nama_level','tb_performance_recap.ranked']);
   
            $jumlah=DB::table('tb_employees')
            ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_employees.leader_id')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
            ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
            ->leftjoin('tb_level', function($join) use ($nm_level) {
                $join->on('tb_level.id_level', '=', 'tb_positions.id_level')
                     ->where('tb_level.nama_level', '=', $nm_level);
            })        
            ->leftjoin('tb_performance', function($join) use ($periode) {
                $join->on('tb_performance.id_employee', '=', 'tb_employees.id')
                     ->where('tb_performance.periode', '=', $periode);
            })        
            ->leftjoin('tb_performance_recap','tb_performance_recap.id_performance','=','tb_performance.id');
            $jumlah=$jumlah->where([['tb_employees.leader_id',$id_user],['tb_employees.status','1'],['tb_level.nama_level',$nm_level],['tb_performance.status_penilai_2', '=', '0']]);
            foreach($tb_buyut as $dt2){
                $jumlah=$jumlah->orwhere([['tb_employees.leader_id',$dt2->id],['tb_employees.status','1'],['tb_level.nama_level',$nm_level],['tb_performance.status_penilai_2', '=', '0']]);
            }
            $jumlah=$jumlah->orderby('tb_performance_recap.average','desc')
            ->count();
        }else if($nm_level=="All Level"&&$dept!="All Dept"){

            $tb_employee=DB::table('tb_employees')
            ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_employees.leader_id')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
            ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
            ->leftjoin('tb_level', function($join) use ($nm_level) {
                $join->on('tb_level.id_level', '=', 'tb_positions.id_level');
                     //->where('tb_level.nama_level', '=', $nm_level);
            })        
            ->leftjoin('tb_performance', function($join) use ($periode) {
                $join->on('tb_performance.id_employee', '=', 'tb_employees.id')
                     ->where('tb_performance.periode', '=', $periode);
            })        
            ->leftjoin('tb_performance_recap','tb_performance_recap.id_performance','=','tb_performance.id');
            $tb_employee=$tb_employee->where([['tb_employees.leader_id',$id_user],['tb_employees.status','1'],['tb_performance.Department',$dept],['tb_performance.status_penilai_2', '=', '0']]);
            foreach($tb_buyut as $dt2){
                $tb_employee=$tb_employee->orwhere([['tb_employees.leader_id',$dt2->id],['tb_employees.status','1'],['tb_performance.Department',$dept],['tb_performance.status_penilai_2', '=', '0']]);
            }
            $tb_employee=$tb_employee->orderby('tb_performance_recap.average','desc')
            ->get(['tb_employees.*','tb_performance.atasan_langsung','tb_employees1.employee_name as leader_name','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_index','tb_positions.position_name','tb_positions.id_level','tb_performance.id as idperformance','tb_performance.masa_kerja_member','tb_performance_recap.triwulan1','tb_performance_recap.triwulan2','tb_performance_recap.triwulan3','tb_performance_recap.triwulan4','tb_performance_recap.average','tb_performance_recap.grade','tb_performance.target','tb_level.nama_level','tb_performance_recap.ranked']);
   
            $jumlah=DB::table('tb_employees')
            ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_employees.leader_id')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
            ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
            ->leftjoin('tb_level', function($join) use ($nm_level) {
                $join->on('tb_level.id_level', '=', 'tb_positions.id_level');
                     //->where('tb_level.nama_level', '=', $nm_level);
            })        
            ->leftjoin('tb_performance', function($join) use ($periode) {
                $join->on('tb_performance.id_employee', '=', 'tb_employees.id')
                     ->where('tb_performance.periode', '=', $periode);
            })        
            ->leftjoin('tb_performance_recap','tb_performance_recap.id_performance','=','tb_performance.id');
            $jumlah=$jumlah->where([['tb_employees.leader_id',$id_user],['tb_employees.status','1'],['tb_performance.Department',$dept],['tb_performance.status_penilai_2', '=', '0']]);
            foreach($tb_buyut as $dt2){
                $jumlah=$jumlah->orwhere([['tb_employees.leader_id',$dt2->id],['tb_employees.status','1'],['tb_performance.Department',$dept],['tb_performance.status_penilai_2', '=', '0']]);
            }
            $jumlah=$jumlah->orderby('tb_performance_recap.average','desc')
            ->count();
        }else{

            $tb_employee=DB::table('tb_employees')
            ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_employees.leader_id')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
            ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
            ->leftjoin('tb_level', function($join) use ($nm_level) {
                $join->on('tb_level.id_level', '=', 'tb_positions.id_level')
                     ->where('tb_level.nama_level', '=', $nm_level);
            })        
            ->leftjoin('tb_performance', function($join) use ($periode) {
                $join->on('tb_performance.id_employee', '=', 'tb_employees.id')
                     ->where('tb_performance.periode', '=', $periode);
            })        
            ->leftjoin('tb_performance_recap','tb_performance_recap.id_performance','=','tb_performance.id');
            $tb_employee=$tb_employee->where([['tb_employees.leader_id',$id_user],['tb_employees.status','1'],['tb_level.nama_level',$nm_level],['tb_performance.Department',$dept],['tb_performance.status_penilai_2', '=', '0']]);
            foreach($tb_buyut as $dt2){
                $tb_employee=$tb_employee->orwhere([['tb_employees.leader_id',$dt2->id],['tb_employees.status','1'],['tb_level.nama_level',$nm_level],['tb_performance.Department',$dept],['tb_performance.status_penilai_2', '=', '0']]);
            }
            $tb_employee=$tb_employee->orderby('tb_performance_recap.average','desc')
            ->get(['tb_employees.*','tb_performance.atasan_langsung','tb_employees1.employee_name as leader_name','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_index','tb_positions.position_name','tb_positions.id_level','tb_performance.id as idperformance','tb_performance.masa_kerja_member','tb_performance_recap.triwulan1','tb_performance_recap.triwulan2','tb_performance_recap.triwulan3','tb_performance_recap.triwulan4','tb_performance_recap.average','tb_performance_recap.grade','tb_performance.target','tb_level.nama_level','tb_performance_recap.ranked']);

            $jumlah=DB::table('tb_employees')
            ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_employees.leader_id')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
            ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
            ->leftjoin('tb_level', function($join) use ($nm_level) {
                $join->on('tb_level.id_level', '=', 'tb_positions.id_level')
                     ->where('tb_level.nama_level', '=', $nm_level);
            })        
            ->leftjoin('tb_performance', function($join) use ($periode) {
                $join->on('tb_performance.id_employee', '=', 'tb_employees.id')
                     ->where('tb_performance.periode', '=', $periode);
            })        
            ->leftjoin('tb_performance_recap','tb_performance_recap.id_performance','=','tb_performance.id');
            $jumlah=$jumlah->where([['tb_employees.leader_id',$id_user],['tb_employees.status','1'],['tb_level.nama_level',$nm_level],['tb_performance.Department',$dept],['tb_performance.status_penilai_2', '=', '0']]);
            foreach($tb_buyut as $dt2){
                $jumlah=$jumlah->orwhere([['tb_employees.leader_id',$dt2->id],['tb_employees.status','1'],['tb_level.nama_level',$nm_level],['tb_performance.Department',$dept],['tb_performance.status_penilai_2', '=', '0']]);
            }
            $jumlah=$jumlah->orderby('tb_performance_recap.average','desc')
            ->count();
        }
        
        // if($nm_level!="All Level"){
        //     $jumlah=DB::table('tb_employees')
        //     ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_employees.leader_id')
        //     ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        //     ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        //     ->leftjoin('tb_level', function($join) use ($nm_level) {
        //         $join->on('tb_level.id_level', '=', 'tb_positions.id_level')
        //              ->where('tb_level.nama_level', '=', $nm_level);
        //     })        
        //     ->leftjoin('tb_performance', function($join) use ($periode) {
        //         $join->on('tb_performance.id_employee', '=', 'tb_employees.id')
        //              ->where('tb_performance.periode', '=', $periode);
        //     })        
        //     ->leftjoin('tb_performance_recap','tb_performance_recap.id_performance','=','tb_performance.id')
        //     //->where('tb_performance.status_penilai_2', '=', '1')
        //     ->where('tb_employees.status','1');
        //     if($dept!="All Dept"){
        //         $jumlah=$jumlah->where('tb_performance.Department',$dept);
        //     }
        //     if($nm_level!="All Level"){
        //         $jumlah=$jumlah->where('tb_level.nama_level',$nm_level);
        //     }
        //     $jumlah=$jumlah->count();
        // }else{
        //     $jumlah=DB::table('tb_employees')
        //     ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_employees.leader_id')
        //     ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        //     ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        //     ->leftjoin('tb_level', function($join) use ($nm_level) {
        //         $join->on('tb_level.id_level', '=', 'tb_positions.id_level')
        //              ->where('tb_level.nama_level', '=', $nm_level);
        //     })        
        //     ->leftjoin('tb_performance', function($join) use ($periode) {
        //         $join->on('tb_performance.id_employee', '=', 'tb_employees.id')
        //              ->where('tb_performance.periode', '=', $periode);
        //     })        
        //     ->leftjoin('tb_performance_recap','tb_performance_recap.id_performance','=','tb_performance.id')
        //     //->where('tb_performance.status_penilai_2', '=', '1')
        //     ->where('tb_employees.status','1');
        //     if($dept!="All Dept"){
        //         $jumlah=$jumlah->where('tb_performance.Department',$dept);
        //     }
        //     if($nm_level!="All Level"){
        //         $jumlah=$jumlah->where('tb_level.nama_level',$nm_level);
        //     }
        //     $jumlah=$jumlah->count();
        // }
        //return $tb_employee;
        
        $tb_rank=DB::table('tb_perform_rank')->orderby('rank','asc')->get();
        foreach($tb_rank as $dt){
            $rank[$dt->rank]=round($jumlah*$dt->kuota/100,0);
        }
        $j=0;
        for($i=1;$i<=5;$i++){
            $j=$j+$rank[$i];
            $level[$i]=$j;
        }
        $tb_department=DB::table('tb_departments')
        ->where([['isDelete','0'],['id','0']]);
        foreach($tb_admins as $dt){
            $tb_department=$tb_department->orWhere([['isDelete','0'],['id',$dt->dept_id]]);
            // if (request()->user()->hasRole('hr_access')){
            //     continue;
            // }else{
            //     if($dept=="All Dept"){
            //         $dept=$dt->dept_code;
            //     }
            // }
        }
        $tb_department=$tb_department->orderby('id','desc')->get();
        $tb_position=DB::table('tb_level')->orderby('id_level','asc')->get(['tb_level.nama_level']);
        $no=0;
        foreach($tb_employee as $dt){
            $no++;
            if($no<=$level[1]){$ranked=1;}
            else if($no<=$level[2]){$ranked=2;}
            else if($no<=$level[3]){$ranked=3;}
            else if($no<=$level[4]){$ranked=4;}
            else {$ranked=5;}
            $see=DB::table('tb_performance_recap')->where('id_performance',$dt->idperformance)->whereNull('ranked')->count();
            if($see==1){
                $update=DB::table('tb_performance_recap')->where('id_performance',$dt->idperformance)->update(['ranked'=>$ranked,'ranked_bod'=>$ranked]);
            }
        }

        // $tb_employee=DB::table('tb_employees')
        // ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_employees.leader_id')
        // ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        // ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        // ->leftjoin('tb_level', function($join) use ($nm_level) {
        //     $join->on('tb_level.id_level', '=', 'tb_positions.id_level')
        //          ->where('tb_level.nama_level', '=', $nm_level);
        // })        
        // ->leftjoin('tb_performance', function($join) use ($periode) {
        //     $join->on('tb_performance.id_employee', '=', 'tb_employees.id')
        //          ->where('tb_performance.periode', '=', $periode);
        // })        
        // ->leftjoin('tb_performance_recap','tb_performance_recap.id_performance','=','tb_performance.id')
        // ->where('tb_performance.status_penilai_2', '=', '1')
        // ->where('tb_employees.status','1');
        // if($dept!="All Dept"){
        //     $tb_employee=$tb_employee->where('tb_performance.Department',$dept);
        // }
        // if($nm_level!="All Level"){
        //     $tb_employee=$tb_employee->where('tb_level.nama_level',$nm_level);
        // }
        // $tb_employee=$tb_employee->orderby('tb_performance_recap.average','desc')
        // ->get(['tb_employees.*','tb_employees1.employee_name as leader_name','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_index','tb_positions.position_name','tb_positions.id_level','tb_performance.id as idperformance','tb_performance.masa_kerja_member','tb_performance_recap.triwulan1','tb_performance_recap.triwulan2','tb_performance_recap.triwulan3','tb_performance_recap.triwulan4','tb_performance_recap.average','tb_performance_recap.grade','tb_performance.target','tb_level.nama_level','tb_performance_recap.ranked']);
        
        //return $jumlah;
        if (request()->user()->hasRole('root')||request()->user()->hasRole('hr_access')||request()->user()->hasRole('performance')){
            return view('page/performance/performance_all',['menu'=>'performanceAll','tb_employee'=>$tb_employee,'periode'=>$periode,'jumlah'=>$jumlah,'level'=>$level,'tb_department'=>$tb_department,'tb_position'=>$tb_position,'nm_level'=>$nm_level,'dept'=>$dept]);
        }
    }
    function performanceHR($periode,$dept,$nm_level){
        $last_date=$periode.'-12-31';
        if((request()->user()->hasRole('hr_access'))){
        
        $today=date('Y-m-d');
        if($periode==0)$periode=date('Y');
        if($dept==0)$dept="All Dept";
        if($nm_level==0)$nm_level="All Level";
        $nama=Auth::user()->name;
        $email=Auth::user()->email;
        $cek1=DB::table('tb_emails')->where('email_address',$email)->get();
        foreach($cek1 as $dt){$id_user=$dt->id_employee;}
        $tb_admins=DB::table('tb_admins')->leftjoin('tb_departments','tb_departments.id','=','tb_admins.dept_id')->where('id_employee',$id_user)->get(['tb_admins.*','tb_departments.dept_code']);

        if($nm_level=="All Level"&&$dept=="All Dept"){
            $tb_employee=DB::table('tb_employees')
            ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_employees.leader_id')
            ->leftjoin('tb_employees as tb_employees2','tb_employees2.id','=','tb_employees1.leader_id')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
            ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
            ->leftjoin('tb_level', function($join) use ($nm_level) {
                $join->on('tb_level.id_level', '=', 'tb_positions.id_level');
                     //->where('tb_level.nama_level', '=', $nm_level);
            })        
            ->leftjoin('tb_performance', function($join) use ($periode) {
                $join->on('tb_performance.id_employee', '=', 'tb_employees.id')
                     ->where('tb_performance.periode', '=', $periode);
            })        
            ->leftjoin('tb_performance_recap','tb_performance_recap.id_performance','=','tb_performance.id');
            $tb_employee=$tb_employee->where([['tb_employees.status','1'],['tb_performance.status_penilai_2', '=', '1'],['tb_employees.join_date','<=',$last_date],['tb_employees.position_id','<>','37'],['tb_employees.position_id','<>','23']]);
            $tb_employee=$tb_employee->orderby('tb_performance_recap.average','desc')
            ->get(['tb_employees.*','tb_performance.atasan_langsung','tb_employees1.employee_name as leader_name','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_index','tb_positions.position_name','tb_positions.id_level','tb_performance.id as idperformance','tb_performance.masa_kerja_member','tb_performance_recap.triwulan1','tb_performance_recap.triwulan2','tb_performance_recap.triwulan3','tb_performance_recap.triwulan4','tb_performance_recap.average','tb_performance_recap.grade','tb_performance.target','tb_level.nama_level','tb_performance_recap.ranked','tb_performance.tanggal_masuk','tb_performance.tgl_distribusi','tb_performance.status_penilai_1','tb_performance.status_penilai_2','tb_employees2.employee_name as atasan2','tb_performance_recap.ranked_bod']);
            
            $jumlah=DB::table('tb_employees')
            ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_employees.leader_id')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
            ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
            ->leftjoin('tb_level', function($join) use ($nm_level) {
                $join->on('tb_level.id_level', '=', 'tb_positions.id_level');
                     //->where('tb_level.nama_level', '=', $nm_level);
            })        
            ->leftjoin('tb_performance', function($join) use ($periode) {
                $join->on('tb_performance.id_employee', '=', 'tb_employees.id')
                     ->where('tb_performance.periode', '=', $periode);
            })        
            ->leftjoin('tb_performance_recap','tb_performance_recap.id_performance','=','tb_performance.id');
            $jumlah=$jumlah->where([['tb_employees.status','1'],['tb_performance.status_penilai_2', '=', '1'],['tb_employees.join_date','<=',$last_date],['tb_employees.position_id','<>','37'],['tb_employees.position_id','<>','23']]);
            $jumlah=$jumlah->orderby('tb_performance_recap.average','desc')
            ->count();
        }else if($nm_level!="All Level"&&$dept=="All Dept"){

            $tb_employee=DB::table('tb_employees')
            ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_employees.leader_id')
            ->leftjoin('tb_employees as tb_employees2','tb_employees2.id','=','tb_employees1.leader_id')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
            ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
            ->leftjoin('tb_level', function($join) use ($nm_level) {
                $join->on('tb_level.id_level', '=', 'tb_positions.id_level')
                     ->where('tb_level.nama_level', '=', $nm_level);
            })        
            ->leftjoin('tb_performance', function($join) use ($periode) {
                $join->on('tb_performance.id_employee', '=', 'tb_employees.id')
                     ->where('tb_performance.periode', '=', $periode);
            })        
            ->leftjoin('tb_performance_recap','tb_performance_recap.id_performance','=','tb_performance.id');
            $tb_employee=$tb_employee->where([['tb_employees.status','1'],['tb_level.nama_level',$nm_level],['tb_performance.status_penilai_2', '=', '1'],['tb_employees.join_date','<=',$last_date],['tb_employees.position_id','<>','37'],['tb_employees.position_id','<>','23']]);
            $tb_employee=$tb_employee->orderby('tb_performance_recap.average','desc')
            ->get(['tb_employees.*','tb_performance.atasan_langsung','tb_employees1.employee_name as leader_name','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_index','tb_positions.position_name','tb_positions.id_level','tb_performance.id as idperformance','tb_performance.masa_kerja_member','tb_performance_recap.triwulan1','tb_performance_recap.triwulan2','tb_performance_recap.triwulan3','tb_performance_recap.triwulan4','tb_performance_recap.average','tb_performance_recap.grade','tb_performance.target','tb_level.nama_level','tb_performance_recap.ranked','tb_performance.tanggal_masuk','tb_performance.tgl_distribusi','tb_performance.status_penilai_1','tb_performance.status_penilai_2','tb_employees2.employee_name as atasan2','tb_performance_recap.ranked_bod']);
   
            $jumlah=DB::table('tb_employees')
            ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_employees.leader_id')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
            ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
            ->leftjoin('tb_level', function($join) use ($nm_level) {
                $join->on('tb_level.id_level', '=', 'tb_positions.id_level')
                     ->where('tb_level.nama_level', '=', $nm_level);
            })        
            ->leftjoin('tb_performance', function($join) use ($periode) {
                $join->on('tb_performance.id_employee', '=', 'tb_employees.id')
                     ->where('tb_performance.periode', '=', $periode);
            })        
            ->leftjoin('tb_performance_recap','tb_performance_recap.id_performance','=','tb_performance.id');
            $jumlah=$jumlah->where([['tb_employees.status','1'],['tb_level.nama_level',$nm_level],['tb_performance.status_penilai_2', '=', '1'],['tb_employees.join_date','<=',$last_date],['tb_employees.position_id','<>','37'],['tb_employees.position_id','<>','23']]);
            $jumlah=$jumlah->orderby('tb_performance_recap.average','desc')
            ->count();
        }else if($nm_level=="All Level"&&$dept!="All Dept"){

            $tb_employee=DB::table('tb_employees')
            ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_employees.leader_id')
            ->leftjoin('tb_employees as tb_employees2','tb_employees2.id','=','tb_employees1.leader_id')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
            ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
            ->leftjoin('tb_level', function($join) use ($nm_level) {
                $join->on('tb_level.id_level', '=', 'tb_positions.id_level');
                     //->where('tb_level.nama_level', '=', $nm_level);
            })        
            ->leftjoin('tb_performance', function($join) use ($periode) {
                $join->on('tb_performance.id_employee', '=', 'tb_employees.id')
                     ->where('tb_performance.periode', '=', $periode);
            })        
            ->leftjoin('tb_performance_recap','tb_performance_recap.id_performance','=','tb_performance.id');
            $tb_employee=$tb_employee->where([['tb_employees.status','1'],['tb_performance.Department',$dept],['tb_performance.status_penilai_2', '=', '1'],['tb_employees.join_date','<=',$last_date],['tb_employees.position_id','<>','37'],['tb_employees.position_id','<>','23']]);
            $tb_employee=$tb_employee->orderby('tb_performance_recap.average','desc')
            ->get(['tb_employees.*','tb_performance.atasan_langsung','tb_employees1.employee_name as leader_name','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_index','tb_positions.position_name','tb_positions.id_level','tb_performance.id as idperformance','tb_performance.masa_kerja_member','tb_performance_recap.triwulan1','tb_performance_recap.triwulan2','tb_performance_recap.triwulan3','tb_performance_recap.triwulan4','tb_performance_recap.average','tb_performance_recap.grade','tb_performance.target','tb_level.nama_level','tb_performance_recap.ranked','tb_performance.tanggal_masuk','tb_performance.tgl_distribusi','tb_performance.status_penilai_1','tb_performance.status_penilai_2','tb_employees2.employee_name as atasan2','tb_performance_recap.ranked_bod']);
   
            $jumlah=DB::table('tb_employees')
            ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_employees.leader_id')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
            ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
            ->leftjoin('tb_level', function($join) use ($nm_level) {
                $join->on('tb_level.id_level', '=', 'tb_positions.id_level');
                     //->where('tb_level.nama_level', '=', $nm_level);
            })        
            ->leftjoin('tb_performance', function($join) use ($periode) {
                $join->on('tb_performance.id_employee', '=', 'tb_employees.id')
                     ->where('tb_performance.periode', '=', $periode);
            })        
            ->leftjoin('tb_performance_recap','tb_performance_recap.id_performance','=','tb_performance.id');
            $jumlah=$jumlah->where([['tb_employees.status','1'],['tb_performance.Department',$dept],['tb_performance.status_penilai_2', '=', '1'],['tb_employees.join_date','<=',$last_date],['tb_employees.position_id','<>','37'],['tb_employees.position_id','<>','23']]);
            $jumlah=$jumlah->orderby('tb_performance_recap.average','desc')
            ->count();
        }else{

            $tb_employee=DB::table('tb_employees')
            ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_employees.leader_id')
            ->leftjoin('tb_employees as tb_employees2','tb_employees2.id','=','tb_employees1.leader_id')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
            ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
            ->leftjoin('tb_level', function($join) use ($nm_level) {
                $join->on('tb_level.id_level', '=', 'tb_positions.id_level')
                     ->where('tb_level.nama_level', '=', $nm_level);
            })        
            ->leftjoin('tb_performance', function($join) use ($periode) {
                $join->on('tb_performance.id_employee', '=', 'tb_employees.id')
                     ->where('tb_performance.periode', '=', $periode);
            })        
            ->leftjoin('tb_performance_recap','tb_performance_recap.id_performance','=','tb_performance.id');
            $tb_employee=$tb_employee->where([['tb_employees.status','1'],['tb_level.nama_level',$nm_level],['tb_performance.Department',$dept],['tb_performance.status_penilai_2', '=', '1'],['tb_employees.join_date','<=',$last_date],['tb_employees.position_id','<>','37'],['tb_employees.position_id','<>','23']]);
            $tb_employee=$tb_employee->orderby('tb_performance_recap.average','desc')
            ->get(['tb_employees.*','tb_performance.atasan_langsung','tb_employees1.employee_name as leader_name','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_index','tb_positions.position_name','tb_positions.id_level','tb_performance.id as idperformance','tb_performance.masa_kerja_member','tb_performance_recap.triwulan1','tb_performance_recap.triwulan2','tb_performance_recap.triwulan3','tb_performance_recap.triwulan4','tb_performance_recap.average','tb_performance_recap.grade','tb_performance.target','tb_level.nama_level','tb_performance_recap.ranked','tb_performance.tanggal_masuk','tb_performance.tgl_distribusi','tb_performance.status_penilai_1','tb_performance.status_penilai_2','tb_employees2.employee_name as atasan2','tb_performance_recap.ranked_bod']);

            $jumlah=DB::table('tb_employees')
            ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_employees.leader_id')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
            ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
            ->leftjoin('tb_level', function($join) use ($nm_level) {
                $join->on('tb_level.id_level', '=', 'tb_positions.id_level')
                     ->where('tb_level.nama_level', '=', $nm_level);
            })        
            ->leftjoin('tb_performance', function($join) use ($periode) {
                $join->on('tb_performance.id_employee', '=', 'tb_employees.id')
                     ->where('tb_performance.periode', '=', $periode);
            })        
            ->leftjoin('tb_performance_recap','tb_performance_recap.id_performance','=','tb_performance.id');
            $jumlah=$jumlah->where([['tb_employees.status','1'],['tb_level.nama_level',$nm_level],['tb_performance.Department',$dept],['tb_performance.status_penilai_2', '=', '1'],['tb_employees.join_date','<=',$last_date],['tb_employees.position_id','<>','37'],['tb_employees.position_id','<>','23']]);
            $jumlah=$jumlah->orderby('tb_performance_recap.average','desc')
            ->count();
        }

        $tb_rank=DB::table('tb_perform_rank')->orderby('rank','asc')->get();
        foreach($tb_rank as $dt){
            $rank[$dt->rank]=round($jumlah*$dt->kuota/100,0);
        }
        $j=0;
        for($i=1;$i<=5;$i++){
            $j=$j+$rank[$i];
            $level[$i]=$j;
        }
        $tb_department=DB::table('tb_departments')->where([['isDelete','0']])->orderby('id','desc')->get();
        $tb_position=DB::table('tb_level')->orderby('id_level','asc')->get(['tb_level.nama_level']);
        $no=0;
        foreach($tb_employee as $dt){
            $no++;
            if($no<=$level[1]){$ranked=1;}
            else if($no<=$level[2]){$ranked=2;}
            else if($no<=$level[3]){$ranked=3;}
            else if($no<=$level[4]){$ranked=4;}
            else {$ranked=5;}
            $see=DB::table('tb_performance_recap')->where('id_performance',$dt->idperformance)->whereNull('ranked')->count();
            if($see==1){
                $update=DB::table('tb_performance_recap')->where('id_performance',$dt->idperformance)->update(['ranked'=>$ranked,'ranked_bod'=>$ranked]);
            }
        }
        if (request()->user()->hasRole('root')||request()->user()->hasRole('hr_access')||request()->user()->hasRole('performance')){
            return view('page/performance/performance_hr',['menu'=>'performanceAll','tb_employee'=>$tb_employee,'periode'=>$periode,'jumlah'=>$jumlah,'level'=>$level,'tb_department'=>$tb_department,'tb_position'=>$tb_position,'nm_level'=>$nm_level,'dept'=>$dept,'today'=>$today,'status'=>'1']);
        }

        }

    }
    function performanceHR_Remining($periode,$dept,$nm_level){
        $last_date=$periode.'-12-31';
        if((request()->user()->hasRole('hr_access'))){
        
        $today=date('Y-m-d');
        if($periode==0)$periode=date('Y');
        if($dept==0)$dept="All Dept";
        if($nm_level==0)$nm_level="All Level";
        $nama=Auth::user()->name;
        $email=Auth::user()->email;
        $cek1=DB::table('tb_emails')->where('email_address',$email)->get();
        foreach($cek1 as $dt){$id_user=$dt->id_employee;}
        $tb_admins=DB::table('tb_admins')->leftjoin('tb_departments','tb_departments.id','=','tb_admins.dept_id')->where('id_employee',$id_user)->get(['tb_admins.*','tb_departments.dept_code']);

        if($nm_level=="All Level"&&$dept=="All Dept"){
            $tb_employee=DB::table('tb_employees')
            ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_employees.leader_id')
            ->leftjoin('tb_employees as tb_employees2','tb_employees2.id','=','tb_employees1.leader_id')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
            ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
            ->leftjoin('tb_level', function($join) use ($nm_level) {
                $join->on('tb_level.id_level', '=', 'tb_positions.id_level');
                     //->where('tb_level.nama_level', '=', $nm_level);
            })        
            ->leftjoin('tb_performance', function($join) use ($periode) {
                $join->on('tb_performance.id_employee', '=', 'tb_employees.id')
                     ->where('tb_performance.periode', '=', $periode);
            })        
            ->leftjoin('tb_performance_recap','tb_performance_recap.id_performance','=','tb_performance.id');
            $tb_employee=$tb_employee->where([['tb_employees.status','1'],['tb_performance.status_penilai_2', '=', '0'],['tb_employees.join_date','<=',$last_date],['tb_employees.position_id','<>','37'],['tb_employees.position_id','<>','23']]);
            $tb_employee=$tb_employee->orderby('tb_performance_recap.average','desc')
            ->get(['tb_employees.*','tb_performance.atasan_langsung','tb_employees1.employee_name as leader_name','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_index','tb_positions.position_name','tb_positions.id_level','tb_performance.id as idperformance','tb_performance.masa_kerja_member','tb_performance_recap.triwulan1','tb_performance_recap.triwulan2','tb_performance_recap.triwulan3','tb_performance_recap.triwulan4','tb_performance_recap.average','tb_performance_recap.grade','tb_performance.target','tb_level.nama_level','tb_performance_recap.ranked','tb_performance.tanggal_masuk','tb_performance.tgl_distribusi','tb_performance.status_penilai_1','tb_performance.status_penilai_2','tb_employees2.employee_name as atasan2','tb_performance_recap.ranked_bod']);
            
            $jumlah=DB::table('tb_employees')
            ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_employees.leader_id')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
            ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
            ->leftjoin('tb_level', function($join) use ($nm_level) {
                $join->on('tb_level.id_level', '=', 'tb_positions.id_level');
                     //->where('tb_level.nama_level', '=', $nm_level);
            })        
            ->leftjoin('tb_performance', function($join) use ($periode) {
                $join->on('tb_performance.id_employee', '=', 'tb_employees.id')
                     ->where('tb_performance.periode', '=', $periode);
            })        
            ->leftjoin('tb_performance_recap','tb_performance_recap.id_performance','=','tb_performance.id');
            $jumlah=$jumlah->where([['tb_employees.status','1'],['tb_performance.status_penilai_2', '=', '0'],['tb_employees.join_date','<=',$last_date],['tb_employees.position_id','<>','37'],['tb_employees.position_id','<>','23']]);
            $jumlah=$jumlah->orderby('tb_performance_recap.average','desc')
            ->count();
        }else if($nm_level!="All Level"&&$dept=="All Dept"){

            $tb_employee=DB::table('tb_employees')
            ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_employees.leader_id')
            ->leftjoin('tb_employees as tb_employees2','tb_employees2.id','=','tb_employees1.leader_id')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
            ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
            ->leftjoin('tb_level', function($join) use ($nm_level) {
                $join->on('tb_level.id_level', '=', 'tb_positions.id_level')
                     ->where('tb_level.nama_level', '=', $nm_level);
            })        
            ->leftjoin('tb_performance', function($join) use ($periode) {
                $join->on('tb_performance.id_employee', '=', 'tb_employees.id')
                     ->where('tb_performance.periode', '=', $periode);
            })        
            ->leftjoin('tb_performance_recap','tb_performance_recap.id_performance','=','tb_performance.id');
            $tb_employee=$tb_employee->where([['tb_employees.status','1'],['tb_level.nama_level',$nm_level],['tb_performance.status_penilai_2', '=', '0'],['tb_employees.join_date','<=',$last_date],['tb_employees.position_id','<>','37'],['tb_employees.position_id','<>','23']]);
            $tb_employee=$tb_employee->orderby('tb_performance_recap.average','desc')
            ->get(['tb_employees.*','tb_performance.atasan_langsung','tb_employees1.employee_name as leader_name','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_index','tb_positions.position_name','tb_positions.id_level','tb_performance.id as idperformance','tb_performance.masa_kerja_member','tb_performance_recap.triwulan1','tb_performance_recap.triwulan2','tb_performance_recap.triwulan3','tb_performance_recap.triwulan4','tb_performance_recap.average','tb_performance_recap.grade','tb_performance.target','tb_level.nama_level','tb_performance_recap.ranked','tb_performance.tanggal_masuk','tb_performance.tgl_distribusi','tb_performance.status_penilai_1','tb_performance.status_penilai_2','tb_employees2.employee_name as atasan2','tb_performance_recap.ranked_bod']);
   
            $jumlah=DB::table('tb_employees')
            ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_employees.leader_id')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
            ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
            ->leftjoin('tb_level', function($join) use ($nm_level) {
                $join->on('tb_level.id_level', '=', 'tb_positions.id_level')
                     ->where('tb_level.nama_level', '=', $nm_level);
            })        
            ->leftjoin('tb_performance', function($join) use ($periode) {
                $join->on('tb_performance.id_employee', '=', 'tb_employees.id')
                     ->where('tb_performance.periode', '=', $periode);
            })        
            ->leftjoin('tb_performance_recap','tb_performance_recap.id_performance','=','tb_performance.id');
            $jumlah=$jumlah->where([['tb_employees.status','1'],['tb_level.nama_level',$nm_level],['tb_performance.status_penilai_2', '=', '0'],['tb_employees.join_date','<=',$last_date],['tb_employees.position_id','<>','37'],['tb_employees.position_id','<>','23']]);
            $jumlah=$jumlah->orderby('tb_performance_recap.average','desc')
            ->count();
        }else if($nm_level=="All Level"&&$dept!="All Dept"){

            $tb_employee=DB::table('tb_employees')
            ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_employees.leader_id')
            ->leftjoin('tb_employees as tb_employees2','tb_employees2.id','=','tb_employees1.leader_id')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
            ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
            ->leftjoin('tb_level', function($join) use ($nm_level) {
                $join->on('tb_level.id_level', '=', 'tb_positions.id_level');
                     //->where('tb_level.nama_level', '=', $nm_level);
            })        
            ->leftjoin('tb_performance', function($join) use ($periode) {
                $join->on('tb_performance.id_employee', '=', 'tb_employees.id')
                     ->where('tb_performance.periode', '=', $periode);
            })        
            ->leftjoin('tb_performance_recap','tb_performance_recap.id_performance','=','tb_performance.id');
            $tb_employee=$tb_employee->where([['tb_employees.status','1'],['tb_performance.Department',$dept],['tb_performance.status_penilai_2', '=', '0'],['tb_employees.join_date','<=',$last_date],['tb_employees.position_id','<>','37'],['tb_employees.position_id','<>','23']]);
            $tb_employee=$tb_employee->orderby('tb_performance_recap.average','desc')
            ->get(['tb_employees.*','tb_performance.atasan_langsung','tb_employees1.employee_name as leader_name','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_index','tb_positions.position_name','tb_positions.id_level','tb_performance.id as idperformance','tb_performance.masa_kerja_member','tb_performance_recap.triwulan1','tb_performance_recap.triwulan2','tb_performance_recap.triwulan3','tb_performance_recap.triwulan4','tb_performance_recap.average','tb_performance_recap.grade','tb_performance.target','tb_level.nama_level','tb_performance_recap.ranked','tb_performance.tanggal_masuk','tb_performance.tgl_distribusi','tb_performance.status_penilai_1','tb_performance.status_penilai_2','tb_employees2.employee_name as atasan2','tb_performance_recap.ranked_bod']);
   
            $jumlah=DB::table('tb_employees')
            ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_employees.leader_id')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
            ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
            ->leftjoin('tb_level', function($join) use ($nm_level) {
                $join->on('tb_level.id_level', '=', 'tb_positions.id_level');
                     //->where('tb_level.nama_level', '=', $nm_level);
            })        
            ->leftjoin('tb_performance', function($join) use ($periode) {
                $join->on('tb_performance.id_employee', '=', 'tb_employees.id')
                     ->where('tb_performance.periode', '=', $periode);
            })        
            ->leftjoin('tb_performance_recap','tb_performance_recap.id_performance','=','tb_performance.id');
            $jumlah=$jumlah->where([['tb_employees.status','1'],['tb_performance.Department',$dept],['tb_performance.status_penilai_2', '=', '0'],['tb_employees.join_date','<=',$last_date],['tb_employees.position_id','<>','37'],['tb_employees.position_id','<>','23']]);
            $jumlah=$jumlah->orderby('tb_performance_recap.average','desc')
            ->count();
        }else{

            $tb_employee=DB::table('tb_employees')
            ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_employees.leader_id')
            ->leftjoin('tb_employees as tb_employees2','tb_employees2.id','=','tb_employees1.leader_id')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
            ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
            ->leftjoin('tb_level', function($join) use ($nm_level) {
                $join->on('tb_level.id_level', '=', 'tb_positions.id_level')
                     ->where('tb_level.nama_level', '=', $nm_level);
            })        
            ->leftjoin('tb_performance', function($join) use ($periode) {
                $join->on('tb_performance.id_employee', '=', 'tb_employees.id')
                     ->where('tb_performance.periode', '=', $periode);
            })        
            ->leftjoin('tb_performance_recap','tb_performance_recap.id_performance','=','tb_performance.id');
            $tb_employee=$tb_employee->where([['tb_employees.status','1'],['tb_level.nama_level',$nm_level],['tb_performance.Department',$dept],['tb_performance.status_penilai_2', '=', '0'],['tb_employees.join_date','<=',$last_date],['tb_employees.position_id','<>','37'],['tb_employees.position_id','<>','23']]);
            $tb_employee=$tb_employee->orderby('tb_performance_recap.average','desc')
            ->get(['tb_employees.*','tb_performance.atasan_langsung','tb_employees1.employee_name as leader_name','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_index','tb_positions.position_name','tb_positions.id_level','tb_performance.id as idperformance','tb_performance.masa_kerja_member','tb_performance_recap.triwulan1','tb_performance_recap.triwulan2','tb_performance_recap.triwulan3','tb_performance_recap.triwulan4','tb_performance_recap.average','tb_performance_recap.grade','tb_performance.target','tb_level.nama_level','tb_performance_recap.ranked','tb_performance.tanggal_masuk','tb_performance.tgl_distribusi','tb_performance.status_penilai_1','tb_performance.status_penilai_2','tb_employees2.employee_name as atasan2','tb_performance_recap.ranked_bod']);

            $jumlah=DB::table('tb_employees')
            ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_employees.leader_id')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
            ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
            ->leftjoin('tb_level', function($join) use ($nm_level) {
                $join->on('tb_level.id_level', '=', 'tb_positions.id_level')
                     ->where('tb_level.nama_level', '=', $nm_level);
            })        
            ->leftjoin('tb_performance', function($join) use ($periode) {
                $join->on('tb_performance.id_employee', '=', 'tb_employees.id')
                     ->where('tb_performance.periode', '=', $periode);
            })        
            ->leftjoin('tb_performance_recap','tb_performance_recap.id_performance','=','tb_performance.id');
            $jumlah=$jumlah->where([['tb_employees.status','1'],['tb_level.nama_level',$nm_level],['tb_performance.Department',$dept],['tb_performance.status_penilai_2', '=', '0'],['tb_employees.join_date','<=',$last_date],['tb_employees.position_id','<>','37'],['tb_employees.position_id','<>','23']]);
            $jumlah=$jumlah->orderby('tb_performance_recap.average','desc')
            ->count();
        }

        $tb_rank=DB::table('tb_perform_rank')->orderby('rank','asc')->get();
        foreach($tb_rank as $dt){
            $rank[$dt->rank]=round($jumlah*$dt->kuota/100,0);
        }
        $j=0;
        for($i=1;$i<=5;$i++){
            $j=$j+$rank[$i];
            $level[$i]=$j;
        }
        $tb_department=DB::table('tb_departments')->where([['isDelete','0']])->orderby('id','desc')->get();
        $tb_position=DB::table('tb_level')->orderby('id_level','asc')->get(['tb_level.nama_level']);
        $no=0;
        foreach($tb_employee as $dt){
            $no++;
            if($no<=$level[1]){$ranked=1;}
            else if($no<=$level[2]){$ranked=2;}
            else if($no<=$level[3]){$ranked=3;}
            else if($no<=$level[4]){$ranked=4;}
            else {$ranked=5;}
            $see=DB::table('tb_performance_recap')->where('id_performance',$dt->idperformance)->whereNull('ranked')->count();
            if($see==1){
                $update=DB::table('tb_performance_recap')->where('id_performance',$dt->idperformance)->update(['ranked'=>$ranked,'ranked_bod'=>$ranked]);
            }
        }
        if (request()->user()->hasRole('root')||request()->user()->hasRole('hr_access')||request()->user()->hasRole('performance')){
            return view('page/performance/performance_hr',['menu'=>'performanceAll','tb_employee'=>$tb_employee,'periode'=>$periode,'jumlah'=>$jumlah,'level'=>$level,'tb_department'=>$tb_department,'tb_position'=>$tb_position,'nm_level'=>$nm_level,'dept'=>$dept,'today'=>$today,'status'=>'0']);
        }

        }

    }
    function performanceRollback(request $data){
        $reset=DB::table('tb_performance')->where('id',$data->id_performance)->update([
            'status_penilai_2'=>'0',
            'status_direktur'=>'0',
        ]);
        if($reset)return "Sukses";
        else return $data->id_performance;
    }
    function rankUpdate(request $data){
        $update=DB::table('tb_performance_recap')->where('id_performance',$data->idperformance)->update([
            'ranked'=>$data->ranked,
            'ranked_bod'=>$data->ranked
        ]);
        if($update)return "Sukses";
        else return $data->idperformance.' '.$data->ranked;
    }
    function rankReset(request $data){
        $periode=$data->periode;
        $dept=$data->dept;
        $nm_level=$data->level;
        $email=Auth::user()->email;
        $cek1=DB::table('tb_emails')->where('email_address',$email)->get();
        foreach($cek1 as $dt){$id_user=$dt->id_employee;}

        $tb_aku=DB::table('tb_employees')->where('leader_id',$id_user)->get();
        $tb_anak=DB::table('tb_employees')->where('leader_id',$id_user);
        foreach($tb_aku as $dt){
            $tb_anak=$tb_anak->orwhere('leader_id',$dt->id);
        }
        $tb_anak=$tb_anak->get();

        $tb_cucu=DB::table('tb_employees')->where('leader_id',$id_user);
        foreach($tb_anak as $dt){
            $tb_cucu=$tb_cucu->orwhere('leader_id',$dt->id);
        }
        $tb_cucu=$tb_cucu->get();

        $tb_buyut=DB::table('tb_employees')->where('leader_id',$id_user);
        foreach($tb_anak as $dt){
            $tb_buyut=$tb_buyut->orwhere('leader_id',$dt->id);
        }
        foreach($tb_cucu as $dt){
            $tb_buyut=$tb_buyut->orwhere('leader_id',$dt->id);
        }
        $tb_buyut=$tb_buyut->get();

        if($data->dept=="All Dept"){

            $tb_employee=DB::table('tb_employees')
            ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_employees.leader_id')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
            ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
            ->leftjoin('tb_level', function($join) use ($nm_level) {
                $join->on('tb_level.id_level', '=', 'tb_positions.id_level')
                     ->where('tb_level.nama_level', '=', $nm_level);
            })        
            ->leftjoin('tb_performance', function($join) use ($periode) {
                $join->on('tb_performance.id_employee', '=', 'tb_employees.id')
                     ->where('tb_performance.periode', '=', $periode);
            })        
            ->leftjoin('tb_performance_recap','tb_performance_recap.id_performance','=','tb_performance.id');
            $tb_employee=$tb_employee->where([['tb_employees.leader_id',$id_user],['tb_employees.status','1'],['tb_level.nama_level',$nm_level],['tb_performance.status_penilai_2', '=', '0']]);
            foreach($tb_buyut as $dt2){
                $tb_employee=$tb_employee->orwhere([['tb_employees.leader_id',$dt2->id],['tb_employees.status','1'],['tb_level.nama_level',$nm_level],['tb_performance.status_penilai_2', '=', '0']]);
            }
            $tb_employee=$tb_employee->orderby('tb_performance_recap.average','desc')
            ->get(['tb_employees.*','tb_performance.atasan_langsung','tb_employees1.employee_name as leader_name','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_index','tb_positions.position_name','tb_positions.id_level','tb_performance.id as idperformance','tb_performance.masa_kerja_member','tb_performance_recap.triwulan1','tb_performance_recap.triwulan2','tb_performance_recap.triwulan3','tb_performance_recap.triwulan4','tb_performance_recap.average','tb_performance_recap.grade','tb_performance.target','tb_level.nama_level','tb_performance_recap.ranked']);
            
            $jumlah=DB::table('tb_employees')
            ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_employees.leader_id')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
            ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
            ->leftjoin('tb_level', function($join) use ($nm_level) {
                $join->on('tb_level.id_level', '=', 'tb_positions.id_level')
                     ->where('tb_level.nama_level', '=', $nm_level);
            })        
            ->leftjoin('tb_performance', function($join) use ($periode) {
                $join->on('tb_performance.id_employee', '=', 'tb_employees.id')
                     ->where('tb_performance.periode', '=', $periode);
            })        
            ->leftjoin('tb_performance_recap','tb_performance_recap.id_performance','=','tb_performance.id');
            $jumlah=$jumlah->where([['tb_employees.leader_id',$id_user],['tb_employees.status','1'],['tb_level.nama_level',$nm_level],['tb_performance.status_penilai_2', '=', '0']]);
            foreach($tb_buyut as $dt2){
                $jumlah=$jumlah->orwhere([['tb_employees.leader_id',$dt2->id],['tb_employees.status','1'],['tb_level.nama_level',$nm_level],['tb_performance.status_penilai_2', '=', '0']]);
            }
            $jumlah=$jumlah->orderby('tb_performance_recap.average','desc')
            ->count();
        }else{
            $tb_employee=DB::table('tb_employees')
            ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_employees.leader_id')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
            ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
            ->leftjoin('tb_level', function($join) use ($nm_level) {
                $join->on('tb_level.id_level', '=', 'tb_positions.id_level')
                     ->where('tb_level.nama_level', '=', $nm_level);
            })        
            ->leftjoin('tb_performance', function($join) use ($periode) {
                $join->on('tb_performance.id_employee', '=', 'tb_employees.id')
                     ->where('tb_performance.periode', '=', $periode);
            })        
            ->leftjoin('tb_performance_recap','tb_performance_recap.id_performance','=','tb_performance.id');
            $tb_employee=$tb_employee->where([['tb_employees.leader_id',$id_user],['tb_employees.status','1'],['tb_level.nama_level',$nm_level],['tb_performance.Department',$dept],['tb_performance.status_penilai_2', '=', '1']]);
            foreach($tb_buyut as $dt2){
                $tb_employee=$tb_employee->orwhere([['tb_employees.leader_id',$dt2->id],['tb_employees.status','1'],['tb_level.nama_level',$nm_level],['tb_performance.Department',$dept],['tb_performance.status_penilai_2', '=', '1']]);
            }
            $tb_employee=$tb_employee->orderby('tb_performance_recap.average','desc')
            ->get(['tb_employees.*','tb_performance.atasan_langsung','tb_employees1.employee_name as leader_name','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_index','tb_positions.position_name','tb_positions.id_level','tb_performance.id as idperformance','tb_performance.masa_kerja_member','tb_performance_recap.triwulan1','tb_performance_recap.triwulan2','tb_performance_recap.triwulan3','tb_performance_recap.triwulan4','tb_performance_recap.average','tb_performance_recap.grade','tb_performance.target','tb_level.nama_level','tb_performance_recap.ranked']);

            $jumlah=DB::table('tb_employees')
            ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_employees.leader_id')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
            ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
            ->leftjoin('tb_level', function($join) use ($nm_level) {
                $join->on('tb_level.id_level', '=', 'tb_positions.id_level')
                     ->where('tb_level.nama_level', '=', $nm_level);
            })        
            ->leftjoin('tb_performance', function($join) use ($periode) {
                $join->on('tb_performance.id_employee', '=', 'tb_employees.id')
                     ->where('tb_performance.periode', '=', $periode);
            })        
            ->leftjoin('tb_performance_recap','tb_performance_recap.id_performance','=','tb_performance.id');
            $jumlah=$jumlah->where([['tb_employees.leader_id',$id_user],['tb_employees.status','1'],['tb_level.nama_level',$nm_level],['tb_performance.Department',$dept],['tb_performance.status_penilai_2', '=', '1']]);
            foreach($tb_buyut as $dt2){
                $jumlah=$jumlah->orwhere([['tb_employees.leader_id',$dt2->id],['tb_employees.status','1'],['tb_level.nama_level',$nm_level],['tb_performance.Department',$dept],['tb_performance.status_penilai_2', '=', '1']]);
            }
            $jumlah=$jumlah->orderby('tb_performance_recap.average','desc')
            ->count();

        }
        
        $tb_rank=DB::table('tb_perform_rank')->orderby('rank','asc')->get();
        foreach($tb_rank as $dt){
            $rank[$dt->rank]=round($jumlah*$dt->kuota/100,0);
        }
        $j=0;
        for($i=1;$i<=5;$i++){
            $j=$j+$rank[$i];
            $level[$i]=$j;
        }
        $no=0;
        $hasil=0;
        foreach($tb_employee as $dt){
            $no++;
            if($no<=$level[1]){$ranked=1;}
            else if($no<=$level[2]){$ranked=2;}
            else if($no<=$level[3]){$ranked=3;}
            else if($no<=$level[4]){$ranked=4;}
            else {$ranked=5;}
            $update=DB::table('tb_performance_recap')->where('id_performance',$dt->idperformance)->update(['ranked'=>$ranked,'ranked_bod'=>$ranked]);
            if($update)$hasil++;
        }
        if($hasil>0)return "Sukses";
        else return "Gagal Update";
    }
    function cutoffUpdate(request $data){
        $update=DB::table('tb_performance')->where('periode',$data->periode)->update(['tgl_cutoff'=>$data->cutoff]);
        $calculate=DB::table('tb_performance')->where('periode',$data->periode)->get();
        foreach($calculate as $dt){
            $durasi=$this->durasi($dt->tgl_cutoff,$dt->tanggal_masuk);
            $update2=DB::table('tb_performance')->where('id',$dt->id)->update([
                'masa_kerja_member'=>$durasi,
            ]);
        }
        if($update)return "Sukses";
        else
        return $data->periode.' '.$data->cutoff;
    }
    function triwulanReset(request $data){
        $update=DB::table('tb_performance_detail')->where('id_performance',$data->id_performance)->where('triwulan',$data->triwulan)->where('created_by',$data->created_by)->update([
            'id_value'=>Null,
            'value'=>'0',
            'grade'=>Null,
            'level'=>Null,
            'kriteria'=>Null,
        ]);
        if($data->created_by==1){
            $update2=DB::table('tb_performance_detail')->where('id_performance',$data->id_performance)->where('triwulan',$data->triwulan)->update([
                'id_value'=>Null,
                'value'=>'0',
                'grade'=>Null,
                'level'=>Null,
                'kriteria'=>Null,
            ]);
        }
        if($update){

            // //Update Recap
            //     $id_performance=$data->id_performance;
            //     $hasil='No Action';
            //     $tb_performance_detail=DB::table('tb_performance_detail')->where('id_performance',$id_performance)->get();
            //     $data['T1']=0;
            //     $data['T2']=0;
            //     $data['T3']=0;
            //     $data['T4']=0;
            //     $data['G1']=0;
            //     $data['G2']=0;
            //     $data['G3']=0;
            //     $data['G4']=0;
        
            //     $data['1T1']=0;
            //     $data['1T2']=0;
            //     $data['1T3']=0;
            //     $data['1T4']=0;
            //     $data['1G1']=0;
            //     $data['1G2']=0;
            //     $data['1G3']=0;
            //     $data['1G4']=0;
        
            //     $data['2T1']=0;
            //     $data['2T2']=0;
            //     $data['2T3']=0;
            //     $data['2T4']=0;
            //     $data['2G1']=0;
            //     $data['2G2']=0;
            //     $data['2G3']=0;
            //     $data['2G4']=0;
        
            //     foreach($tb_performance_detail as $dt){
            //         $data[$dt->id_performance.$dt->triwulan.$dt->id_aspek]=$dt->value;
            //         if($dt->triwulan==1){
            //             if($dt->created_by==1){
            //                 $data['1T1']=$data['1T1']+$dt->value;
            //             }
            //             if($dt->created_by==2){
            //                 $data['2T1']=$data['2T1']+$dt->value;
            //             }
            //             // $data['T1']=$data['T1']+$dt->value;
            //         }
            //         if($dt->triwulan==2){
            //             if($dt->created_by==1){
            //                 $data['1T2']=$data['1T2']+$dt->value;
            //             }
            //             if($dt->created_by==2){
            //                 $data['2T2']=$data['2T2']+$dt->value;
            //             }
            //             // $data['T2']=$data['T2']+$dt->value;
            //         }
            //         if($dt->triwulan==3){
            //             if($dt->created_by==1){
            //                 $data['1T3']=$data['1T3']+$dt->value;
            //             }
            //             if($dt->created_by==2){
            //                 $data['2T3']=$data['2T3']+$dt->value;
            //             }
            //             // $data['T3']=$data['T3']+$dt->value;
            //         }
            //         if($dt->triwulan==4){
            //             if($dt->created_by==1){
            //                 $data['1T4']=$data['1T4']+$dt->value;
            //             }
            //             if($dt->created_by==2){
            //                 $data['2T4']=$data['2T4']+$dt->value;
            //             }
            //             // $data['T4']=$data['T4']+$dt->value;
            //         }
            //     }
            //     $qty=4;
            //     $sum=0;
            //     for($i=1;$i<=4;$i++){
            //         $data['T'.$i]=($data['1T'.$i]+$data['2T'.$i])/2;
            //         if($data['T'.$i]==0){
            //             $qty--;
            //         }
            //         $sum=$sum+$data['T'.$i];
        
            //         $tb_perform_grade=DB::table('tb_perform_grade')->where('range_bawah','<',$data['T'.$i])->where('range_atas','>=',$data['T'.$i])->get();
            //         foreach($tb_perform_grade as $dt){
            //             $data['G'.$i]=$dt->grade;
            //         }
            //     }
            //     if($qty==0){
            //         $data['TAve']=0;
            //         $data['GAve']=0;
            //     }else{
            //         $data['TAve']=$sum/$qty;
            //     }
                
            //     $tb_perform_grade=DB::table('tb_perform_grade')->where('range_bawah','<',$data['TAve'])->where('range_atas','>=',$data['TAve'])->get();
            //     foreach($tb_perform_grade as $dt){
            //         $data['GAve']=$dt->grade;
            //     }
            //     //return $data['TAve'];
                
        
            //     if($data['GAve']=='A+')$ranked='1';
            //     else if($data['GAve']=='A')$ranked='2';
            //     else if($data['GAve']=='B+')$ranked='3';
            //     else if($data['GAve']=='B')$ranked='4';
            //     else if($data['GAve']=='C')$ranked='5';
            //     else $ranked='';
        
            //     $proses=DB::table('tb_performance_recap')->where('id_performance',$id_performance)->update([
            //         'triwulan1'=>$data['T1'],
            //         'triwulan2'=>$data['T2'],
            //         'triwulan3'=>$data['T3'],
            //         'triwulan4'=>$data['T4'],
            //         'grade1'=>$data['G1'],
            //         'grade2'=>$data['G2'],
            //         'grade3'=>$data['G3'],
            //         'grade4'=>$data['G4'],
            //         'average'=>$data['TAve'],
            //         'grade'=>$data['GAve'],
            //         'ranked'=>$ranked,
            //         'ranked_bod'=>$ranked,
            //         'triwulan11'=>$data['1T1'],
            //         'triwulan21'=>$data['1T2'],
            //         'triwulan31'=>$data['1T3'],
            //         'triwulan41'=>$data['1T4'],
            //         'triwulan12'=>$data['2T1'],
            //         'triwulan22'=>$data['2T2'],
            //         'triwulan32'=>$data['2T3'],
            //         'triwulan42'=>$data['2T4'],
            //     ]);
            // // End Update recap

            return "Sukses";
        }
        else return $data->created_by;
    }
    function LeaderUpdate(request $data){
        $table=DB::table('tb_performance')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_performance.id_employee')
        ->leftjoin('tb_employees as tb_employees2','tb_employees2.id','=','tb_employees.leader_id')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->where('periode',$data->periode)->get(['tb_performance.*','tb_employees2.employee_name as leader_name','tb_departments.dept_code']);
        $beda=0;
        foreach($table as $dt){
            if($dt->atasan_langsung!=$dt->leader_name||$dt->department!=$dt->dept_code){
                $update=DB::table('tb_performance')->where('id',$dt->id)->update(['atasan_langsung'=>$dt->leader_name,'department'=>$dt->dept_code]);
                if($update)$beda++;
            }
        }
        if($beda>0)return "Sukses";
        else return "Gagal Update";
    }
    function performanceProgress($periode,$dept,$nm_level){

        $result=$this->updateProgress($periode);
        //return $result;

        $data['progress_create']='0';
        $data['jumlah_create']='0';
        $data['sisa_create']='0';
        $data['progress_confirm']='0';
        $data['jumlah_confirm']='0';
        $data['sisa_confirm']='0';
        $data['NotFirm']='0';
        $data['Firm']='0';

        $data['total']='0';
        foreach($result as $dt){
            $data['total']=$data['total']+$dt->jumlah_karyawan;
            $data['jumlah_create']=$data['jumlah_create']+$dt->jumlah_create;
            $data['jumlah_confirm']=$data['jumlah_confirm']+$dt->jumlah_confirm;
        }
        $data['progress_create']=number_format($data['jumlah_create']/($data['total'])*100,2);
        $data['progress_confirm']=number_format($data['jumlah_confirm']/($data['total'])*100,2);
        $data['sisa_create']=($data['total'])-$data['jumlah_create'];
        $data['sisa_confirm']=($data['total'])-$data['jumlah_confirm'];
        $data['NotFirm']=$data['sisa_confirm'];
        $data['Firm']=$data['jumlah_confirm'];
        return view('page/performance/performance_progress',['periode'=>$periode,'tb_result'=>$result,'data'=>$data,'dept'=>$dept,'nm_level'=>$nm_level,'site'=>$this->site,'menu'=>'performance','juduls'=>'Summary Progress']);
        
    }
    public function updateProgress($periode){
        $last_date=$periode.'-12-31';
        $check=DB::table('tb_performance_progress')->where('periode',$periode)->count();
        if($check==0){
            $tb_employee=DB::table('tb_employees')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
            ->select('dept_code',DB::raw('count(*) as employee_count'))
            ->where('tb_employees.status','1')
            ->where('tb_employees.join_date','<=',$last_date)
            ->where('position_id','<>','37')
            ->where('position_id','<>','23')
            ->groupby('dept_code')
            ->get();
            foreach($tb_employee as $dt){
                $jml=$dt->employee_count;
                if($dt->dept_code!='BOD'){
                    $add=DB::table('tb_performance_progress')->insert([
                        'periode'=>$periode,
                        'department'=>$dt->dept_code,
                        'jumlah_karyawan'=>$jml
                    ]);
                }
            }
        }else{
            $tb_employee=DB::table('tb_employees')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
            ->select('dept_code',DB::raw('count(*) as employee_count'))
            ->where('tb_employees.status','1')
            ->where('tb_employees.join_date','<=',$last_date)
            ->where('position_id','<>','37')
            ->where('position_id','<>','23')
            ->groupby('dept_code')
            ->get();
            foreach($tb_employee as $dt){
                $jml=$dt->employee_count;
                $add=DB::table('tb_performance_progress')->where('periode',$periode)->where('department',$dt->dept_code)->update([
                    'jumlah_karyawan'=>$jml
                ]);
            }
        }
        $now=date('Y-m-d H:i:s');
        $tb_progress=DB::table('tb_performance_progress')->where('periode',$periode)->get();
        foreach($tb_progress as $dt){
            $jumlah_create=DB::table('tb_performance')->leftjoin('tb_employees','tb_employees.id','=','tb_performance.id_employee')->where('tb_employees.status','1')->where('periode',$periode)->where('department',$dt->department)->where('status_penilai_1','1')->where('tb_employees.join_date','<=',$last_date)->count();
            $jumlah_confirm=DB::table('tb_performance')->leftjoin('tb_employees','tb_employees.id','=','tb_performance.id_employee')->where('tb_employees.status','1')->where('periode',$periode)->where('department',$dt->department)->where('status_penilai_2','1')->where('tb_employees.join_date','<=',$last_date)->count();
            $update=DB::table('tb_performance_progress')->where('periode',$periode)->where('department',$dt->department)->update([
                'jumlah_create'=>$jumlah_create,
                'jumlah_confirm'=>$jumlah_confirm,
                'updated_at'=>$now,
            ]);
        }
        $tb_progress=DB::table('tb_performance_progress')
        ->select(
            'tb_performance_progress.*',
            DB::raw('jumlah_karyawan - jumlah_create AS sisa_create'),
            DB::raw('jumlah_create - jumlah_confirm AS sisa_confirm'),
            DB::raw('jumlah_karyawan - jumlah_confirm AS sisa_confirms'),
        )
        ->where('periode',$periode)->get();
        return $tb_progress;
    }
    function performanceDistribution($periode,$nm_level,$rank){
        $last_date=$periode.'-12-31';

        $table1 = DB::table('tb_performance')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_performance.id_employee')
        ->select('jabatan', DB::raw('COUNT(jabatan) as total'))
        ->where([['tb_employees.status','1'],['tb_performance.periode',$periode],['tb_performance.status_penilai_2', '=', '1'],['tb_employees.join_date','<=',$last_date],['tb_employees.position_id','<>','37'],['tb_employees.position_id','<>','23']])
        ->groupBy('jabatan')
        ->get();
            
        // if($nm_level<0){
        //     $table2='';
        // }else{
            $table2 = DB::table('tb_performance_recap')
            ->leftJoin('tb_performance', 'tb_performance.id', '=', 'tb_performance_recap.id_performance')
            ->leftjoin('tb_employees','tb_employees.id','=','tb_performance.id_employee');
            if($nm_level!=0)
            $table2=$table2->where([['tb_employees.status','1'],['tb_performance.periode',$periode],['tb_performance.jabatan',$nm_level],['tb_performance.status_penilai_2', '=', '1'],['tb_employees.join_date','<=',$last_date],['tb_employees.position_id','<>','37'],['tb_employees.position_id','<>','23']]);
            else
            $table2=$table2->where([['tb_employees.status','1'],['tb_performance.periode',$periode],['tb_performance.status_penilai_2', '=', '1'],['tb_employees.join_date','<=',$last_date],['tb_employees.position_id','<>','37'],['tb_employees.position_id','<>','23']]);
            $table2=$table2->selectRaw('COUNT(*) AS TOTAL')
            ->selectRaw('SUM(CASE WHEN ranked_bod = 1 THEN 1 ELSE 0 END) AS R1R')
            ->selectRaw('(SUM(CASE WHEN ranked_bod = 1 THEN 1 ELSE 0 END) * 100.0 / COUNT(*)) AS R1P')
            ->selectRaw('SUM(CASE WHEN ranked_bod = 2 THEN 1 ELSE 0 END) AS R2R')
            ->selectRaw('(SUM(CASE WHEN ranked_bod = 2 THEN 1 ELSE 0 END) * 100.0 / COUNT(*)) AS R2P')
            ->selectRaw('SUM(CASE WHEN ranked_bod = 3 THEN 1 ELSE 0 END) AS R3R')
            ->selectRaw('(SUM(CASE WHEN ranked_bod = 3 THEN 1 ELSE 0 END) * 100.0 / COUNT(*)) AS R3P')
            ->selectRaw('SUM(CASE WHEN ranked_bod = 4 THEN 1 ELSE 0 END) AS R4R')
            ->selectRaw('(SUM(CASE WHEN ranked_bod = 4 THEN 1 ELSE 0 END) * 100.0 / COUNT(*)) AS R4P')
            ->selectRaw('SUM(CASE WHEN ranked_bod = 5 THEN 1 ELSE 0 END) AS R5R')
            ->selectRaw('(SUM(CASE WHEN ranked_bod = 5 THEN 1 ELSE 0 END) * 100.0 / COUNT(*)) AS R5P')
            ->get();
        // }
        //return $table2;

        $table3=DB::table('tb_employees')
        ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_employees.leader_id')
        ->leftjoin('tb_employees as tb_employees2','tb_employees2.id','=','tb_employees1.leader_id')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id');
           $table3=$table3->leftjoin('tb_level', function($join) use ($nm_level) {
                $join->on('tb_level.id_level', '=', 'tb_positions.id_level');
                if($nm_level!=0){
                    $join->where('tb_level.nama_level', '=', $nm_level);
                }
            });        
        $table3=$table3->leftjoin('tb_performance', function($join) use ($periode) {
            $join->on('tb_performance.id_employee', '=', 'tb_employees.id')
                    ->where('tb_performance.periode', '=', $periode);
        })        
        ->leftjoin('tb_performance_recap','tb_performance_recap.id_performance','=','tb_performance.id');
        $table3=$table3->where([['tb_employees.status','1'],['tb_performance.status_penilai_2', '=', '1'],['tb_employees.join_date','<=',$last_date],['tb_employees.position_id','<>','37'],['tb_employees.position_id','<>','23']]);
        if($rank!=0){
            $table3=$table3->where('ranked_bod',$rank);
        }
        if($nm_level!=0){
            $table3=$table3->where('tb_positions.position_name',$nm_level);
        }
        $table3=$table3->orderby('tb_performance_recap.ranked','asc')
        ->get(['tb_employees.*','tb_performance.atasan_langsung','tb_employees1.employee_name as leader_name','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_index','tb_positions.position_name','tb_positions.id_level','tb_performance.id as idperformance','tb_performance.masa_kerja_member','tb_performance_recap.triwulan1','tb_performance_recap.triwulan2','tb_performance_recap.triwulan3','tb_performance_recap.triwulan4','tb_performance_recap.average','tb_performance_recap.grade','tb_performance.target','tb_level.nama_level','tb_performance_recap.ranked','tb_performance.tanggal_masuk','tb_performance.tgl_distribusi','tb_performance.status_penilai_1','tb_performance.status_penilai_2','tb_employees2.employee_name as atasan2','tb_performance_recap.ranked_bod']);
        
    
        //return $table3;        
        return view('page/user/m_dashboard/performance',['table1'=>$table1,'table2'=>$table2,'table3'=>$table3,'periode'=>$periode,'rank'=>$rank,'nm_level'=>$nm_level,'site'=>$this->site,'juduls'=>'Dashboard Performance','menu'=>'dashboard']);

    }
    function rankResetBOD(request $data){
        $update=DB::table('tb_performance_recap')
        ->leftjoin('tb_performance','tb_performance.id','=','tb_performance_recap.id_performance')
        ->where('periode',$data->periode)
        ->update([
            'ranked_bod'=>DB::raw('ranked')
        ]);

        if($update)return "Sukses";
        else return $data->periode;
    }
    function rankUpdateBOD(request $data){
        $update=DB::table('tb_performance_recap')->where('id_performance',$data->idperformance)->update([
            'ranked_bod'=>$data->ranked
        ]);
        if($update)return "Sukses";
        else return $data->idperformance.' '.$data->ranked;
    }
    function performanceConfirm($periode){
        $last_date=$periode.'-12-31';
        $today=date('Y-m-d');
        $tb_employee=DB::table('tb_employees')
        ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_employees.leader_id')
        ->leftjoin('tb_employees as tb_employees2','tb_employees2.id','=','tb_employees1.leader_id')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->leftjoin('tb_level', function($join) {
            $join->on('tb_level.id_level', '=', 'tb_positions.id_level');
        })        
        ->leftjoin('tb_performance', function($join) use ($periode) {
            $join->on('tb_performance.id_employee', '=', 'tb_employees.id')
                    ->where('tb_performance.periode', '=', $periode);
        })        
        ->leftjoin('tb_performance_recap','tb_performance_recap.id_performance','=','tb_performance.id');
        $tb_employee=$tb_employee->where([['tb_employees.status','1'],['tb_performance.status_penilai_1', '=', '1'],['tb_employees.join_date','<=',$last_date]])
        ->get(['tb_employees.*','tb_performance.atasan_langsung','tb_employees1.employee_name as leader_name','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_index','tb_positions.position_name','tb_positions.id_level','tb_performance.id as idperformance','tb_performance.masa_kerja_member','tb_performance_recap.triwulan1','tb_performance_recap.triwulan2','tb_performance_recap.triwulan3','tb_performance_recap.triwulan4','tb_performance_recap.average','tb_performance_recap.grade','tb_performance.target','tb_level.nama_level','tb_performance_recap.ranked','tb_performance.tanggal_masuk','tb_performance.tgl_distribusi','tb_performance.status_penilai_1','tb_performance.status_penilai_2','tb_employees2.employee_name as atasan2']);
        foreach($tb_employee as $dt){
            if($dt->status_penilai_2==1||$dt->atasan2==''){
                if($dt->status_penilai_1==1){
                    $update=DB::table('tb_performance')->where('id',$dt->idperformance)->update([
                        'status_penilai_2'=>'1',
                        'status_konfirmasi'=>'1',
                        'tgl_konfirmasi'=>$today,
                    ]);
                }
            }
        }
        //return $tb_employee;
        return redirect()->back();
    }

}