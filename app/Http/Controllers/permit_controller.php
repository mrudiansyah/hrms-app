<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DateTime;
use Auth;
use PDF;

class permit_controller extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }
    function index(){
        $nama=Auth::user()->name;
        $email=Auth::user()->email;
        $cek1=DB::table('tb_emails')->where('email_address',$email)->get();
        foreach($cek1 as $dt){$id_user=$dt->id_employee;}
        $tb_admins=DB::table('tb_admins')->where('id_employee',$id_user)->get();

        $tb_employee=DB::table('tb_employees')->where([['status','1'],['dept_id','0']]);
        foreach($tb_admins as $dt2){
            $tb_employee=$tb_employee->orwhere([['status','1'],['dept_id',$dt2->dept_id]]);
        }
        $tb_employee=$tb_employee->orderby('employee_name','asc')->get();

        // Kumpulkan semua dept_id yang diizinkan
        $deptIds = [0]; // selalu sertakan 0
        foreach($tb_admins as $dt2){
            $deptIds[] = $dt2->dept_id;
        }

        $tb_izin = DB::table('tb_izins')
            ->leftJoin('tb_employees', 'tb_employees.id', '=', 'tb_izins.id_employee')
            ->where('tb_employees.status', '1')
            ->where('status_disetujui', '0')
            ->where('tb_izins.is_deleted', '0')
            ->whereIn('dept_id', $deptIds) // ← Gunakan whereIn
            ->orderBy('tb_izins.id', 'desc')
            ->get(['tb_izins.*', 'tb_employees.NIK', 'tb_employees.employee_name']);
    
        $jam_masuk=DB::table('tb_cycles')->distinct()->get('check_in');
		$jam_isoma=DB::table('tb_cycles')->distinct()->get('isoma');

		$lock_backdate=DB::table('tb_utilities')->where('id','17')->where('status','1')->count();
		$now=date('Y-m-d H:i:s');
		$cek=DB::table('tb_utilities_exception')->where('id_utility','17')->where('admin',$nama)->where('status','1')->where('start','<=',$now)->where('end','>=',$now)->count();
		if($cek==1)$lock_backdate=0;
		$Tgl=date('Y-m-d');
		$Jam=date('Y-m-d').'T00:00';

        return view('page/admin/m_permit/permit',['jam_masuk'=>$jam_masuk,'jam_isoma'=>$jam_isoma,'tb_employee'=>$tb_employee,'tb_izin'=>$tb_izin,'nama'=>$nama,'menu'=>'permit','lock_backdate'=>$lock_backdate,'Tgl'=>$Tgl,'Jam'=>$Jam]);
    }
    function selectApprove(Request $data){
    	$konten="";
    	$tb_employee=DB::table('tb_employees')
    	->leftjoin('tb_employees as tb_employees1','tb_employees.leader_id','=','tb_employees1.id')
    	->where('tb_employees.id',$data->idemployee)
		->where('tb_employees.status','1')
		->get(['tb_employees1.*','tb_employees.dept_id as department','tb_employees.id as idemployee']);
		$tb_izin_advance=DB::table('tb_izin_advance')->where('id_employee',$data->idemployee)->count();
    	$i=0;
		foreach ($tb_employee as $dt) {
			$dept=$dt->department;
			$idemployee=$dt->idemployee;
			$tb_posisi=DB::table('tb_positions')->where('id',$dt->position_id)->get('position_index');
			foreach($tb_posisi as $dts){
				$position_index=$dts->position_index;
				if($position_index>=2&&$tb_izin_advance==0)$konten.="<option value='".$dt->id."'>".$dt->employee_name."</option>";
				//if($position_index>=2)$konten.="<option value='".$dt->id."'>".$dt->employee_name."</option>";
				$i++;
			}
	    	$tb_employee2=DB::table('tb_employees')->where('id',$dt->leader_id)->get();
	    	foreach ($tb_employee2 as $dt2) {
				$tb_posisi=DB::table('tb_positions')->where('id',$dt2->position_id)->get('position_index');
				foreach($tb_posisi as $dts){
					$position_index=$dts->position_index;
					if($position_index>=2)$konten.="<option value='".$dt2->id."'>".$dt2->employee_name."</option>";
					$i++;
				}
	    		//$konten.="<option value='".$dt2->id."'>".$dt2->employee_name."</option>";
		    	$tb_employee3=DB::table('tb_employees')->where('id',$dt2->leader_id)->get();
		    	foreach ($tb_employee3 as $dt3) {
					$tb_posisi=DB::table('tb_positions')->where('id',$dt3->position_id)->get('position_index');
					foreach($tb_posisi as $dts){
						$position_index=$dts->position_index;
						if($position_index>=2)$konten.="<option value='".$dt3->id."'>".$dt3->employee_name."</option>";
						$i++;
					}
		    		//$konten.="<option value='".$dt3->id."'>".$dt3->employee_name."</option>";
		    	}
	    	}
    	}
		if($i==0){
			$tb_sect=DB::table('tb_employees')
			->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
			->where('dept_id',$dept)
			->where('position_index','>=','3')
			->where('tb_employees.id','<>',$idemployee)
			->where('tb_employees.status','1')
			->orderby('position_id','asc')
			->get('tb_employees.*');
			foreach($tb_sect as $dt4){
				$konten.="<option value='".$dt4->id."'>".$dt4->employee_name."</option>";
			}
		}
    	return $konten;
    }
    function addPermit(Request $data){
        $nama=Auth::user()->name;
    	$this->validate($data,[
    		'category'=>'required',
    		'id_employee'=>'required',
    		'disetujui'=>'required',
    		'apply_date'=>'required',
    		'keperluan'=>'required',
    	]);
    	$Tgl=date('Y-m-d');
		if($data->category=='C'||$data->category=='D')$minutes=$data->minutes;
		else $minutes='0';
		//$minutes='0';
		$tb1=DB::table('tb_izins')->where('id_employee',$data->id_employee)->where('apply_date',$data->apply_date)->where('is_deleted','0')->count();
		if($tb1==0){
			$simpan=DB::table('tb_izins')->insert([
				'category'=>$data->category,
				'id_employee'=>$data->id_employee,
				'doc_date'=>$Tgl,
				'id_employee'=>$data->id_employee,
				'apply_date'=>$data->apply_date,
				'start_izin'=>$data->start_izin,
				'finish_izin'=>$data->finish_izin,
				'minutes'=>$minutes,
				'keperluan'=>$data->keperluan,
				'keluhan'=>$data->keluhan,
				'berobat_ke'=>$data->berobat_ke,
				'disetujui'=>$data->disetujui,
				'personalia'=>'0',
				'scurity'=>'0',
				'status_disetujui'=>'0',
				'status_personalia'=>'0',
				'status_scurity'=>'0',
				'verified'=>'0',
				'admin'=>$nama
			]);
			if($simpan){
				//$tb1=DB::table('tb_izins')->where('id_employee',$data->id_employee)->where('apply_date',$data->apply_date)->value('id');
				//$send_notif=$this->notificationPermit($tb1);
			}
			return redirect()->back();
		}else{
			return redirect()->back()->with(['error'=>'Anda masih memiliki izin yang belum disetujui']);
		}
    }
    function deletePermit($id){
		$nama=Auth::user()->name;
    	//$delete=tb_izin::where('id',$id)->delete();
		$delete=DB::table('tb_izins')->where('id',$id)->update([
			'is_deleted'=>'1',
			'deleted_by'=>$nama
		]);
        // return $id;
    	if($delete)return redirect()->back();
		else return $nama;

    }
    function previewPermit($id){
 		$tb_izin=DB::table('tb_izins')
 		->leftjoin('tb_employees','tb_employees.id','=','tb_izins.id_employee')
 		->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
 		->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_izins.disetujui')
 		->leftjoin('tb_employees as tb_employees2','tb_employees2.id','=','tb_izins.personalia')
 		->leftjoin('tb_employees as tb_employees3','tb_employees3.id','=','tb_izins.scurity')
 		->where('tb_izins.id',$id)
 		->get(['tb_izins.*','tb_employees.NIK','tb_employees.employee_name','tb_departments.dept_name','tb_employees1.employee_name as nama_atasan','tb_employees2.employee_name as nama_personalia','tb_employees3.employee_name as nama_scurity']);

        $FileName='FORMIZIN '.$id.'.PDF';
        $pdf = PDF::loadview('page/admin/m_permit/previewizin',['tb_izin'=>$tb_izin])->setPaper('a6');
		
        return $pdf->stream($FileName);

    }
    function approvePermit($awal,$akhir){
        date_default_timezone_set("Asia/Jakarta");
        $kalendar=CAL_GREGORIAN;
        if($awal==0){
            $periode=date('Y-m');
            $periodeBefore=date('Y-m-d',strtotime('-1 days',strtotime($periode.'-01')));
            $bln=date('Y-m',strtotime($periodeBefore.'-01'));
            $awal=date('Y-m-d',strtotime($bln.'-25'));
            $akhir=date('Y-m-d',strtotime($periode.'-24'));
        }else{
            $periode=date('Y-m',strtotime($awal));
        }

		$email=Auth::user()->email;
        $tb_email=DB::table('tb_emails')->where('email_address',$email)->get();
        foreach($tb_email as $dt){
            $id_employee=$dt->id_employee;
        }

        $limit_approval=DB::table('tb_utilities')->where('id','23')->where('status','1')->count();
        $limit_day=DB::table('tb_utilities')->where('id','23')->where('status','1')->value('limit_transaksi');
        $now=date('Y-m-d h:i:s');
        $tb_exception=DB::table('tb_utilities_exception')->where('id_utility','17')->where('status','1')->where('start','<=',$now)->where('end','>=',$now)->get();
        //return $tb_exception;
        foreach($tb_exception as $dt){
             $update=DB::table('tb_izins')->where('status_disetujui','0')->where('admin',$dt->admin)->update(['exception'=>'1']);
        }
		
 		$tb_permit_new=DB::table('tb_izins')
 		->leftjoin('tb_employees','tb_employees.id','=','tb_izins.id_employee')
 		->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
 		->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_izins.disetujui')
 		->leftjoin('tb_employees as tb_employees2','tb_employees2.id','=','tb_izins.personalia')
 		->leftjoin('tb_employees as tb_employees3','tb_employees3.id','=','tb_izins.scurity')
 		->where('tb_izins.is_deleted','0')
 		->where('tb_izins.status_disetujui','0')
 		->where('tb_izins.disetujui',$id_employee)
 		->get(['tb_izins.*','tb_employees.NIK','tb_employees.employee_name','tb_departments.dept_name','tb_employees1.employee_name as nama_atasan','tb_employees2.employee_name as nama_personalia','tb_employees3.employee_name as nama_scurity']);
 		$qty_permit_new=DB::table('tb_izins')->where('status_disetujui',0)
 		->where('tb_izins.is_deleted','0')
		->where('disetujui',$id_employee)->count();

 		$tb_permit_approve=DB::table('tb_izins')
 		->leftjoin('tb_employees','tb_employees.id','=','tb_izins.id_employee')
 		->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
 		->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_izins.disetujui')
 		->leftjoin('tb_employees as tb_employees2','tb_employees2.id','=','tb_izins.personalia')
 		->leftjoin('tb_employees as tb_employees3','tb_employees3.id','=','tb_izins.scurity')
 		->where('tb_izins.status_disetujui','1')
 		->where('tb_izins.disetujui',$id_employee)
		->where('apply_date','>=',$awal)
		->where('apply_date','<=',$akhir)
 		->get(['tb_izins.*','tb_employees.NIK','tb_employees.employee_name','tb_departments.dept_name','tb_employees1.employee_name as nama_atasan','tb_employees2.employee_name as nama_personalia','tb_employees3.employee_name as nama_scurity']);

 		$tb_permit_refuse=DB::table('tb_izins')
 		->leftjoin('tb_employees','tb_employees.id','=','tb_izins.id_employee')
 		->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
 		->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_izins.disetujui')
 		->leftjoin('tb_employees as tb_employees2','tb_employees2.id','=','tb_izins.personalia')
 		->leftjoin('tb_employees as tb_employees3','tb_employees3.id','=','tb_izins.scurity')
 		->where('tb_izins.status_disetujui','2')
 		->where('tb_izins.disetujui',$id_employee)
 		->get(['tb_izins.*','tb_employees.NIK','tb_employees.employee_name','tb_departments.dept_name','tb_employees1.employee_name as nama_atasan','tb_employees2.employee_name as nama_personalia','tb_employees3.employee_name as nama_scurity']);

        return view('page/admin/m_permit/permit_approve',[
        	'start'=>$awal,'finish'=>$akhir,
			'tb_permit_new'=>$tb_permit_new,
        	'qty_permit_new'=>$qty_permit_new,
        	'tb_permit_approve'=>$tb_permit_approve,
        	'tb_permit_refuse'=>$tb_permit_refuse,'limit_approval'=>$limit_approval,'limit_day'=>$limit_day,
        	'menu'=>'permit'
        ]);
    }
    function approveSign($id,$status){
        $approve=DB::table('tb_izins')->where('id',$id)->update(['status_disetujui'=>$status]);
        if($approve){
			//$this->notificationPermit($id);
			return redirect()->back()->with(['success'=>'Update Success']);
		}
    }
    function personaliaPermit($start,$finish){
		date_default_timezone_set("Asia/Jakarta");
		if($start==0){
			$periode_ini=date('Y-m');
			$bulan_lalu=date('Y-m-d',strtotime('-1 days',strtotime($periode_ini.'-01')));
			$periode_lalu=date('Y-m',strtotime($bulan_lalu));
			$start=date('Y-m-d',strtotime($periode_lalu.'-25'));
			$finish=date('Y-m-d',strtotime($periode_ini.'-24'));
		}
 		$tb_permit_new=DB::table('tb_izins')
 		->leftjoin('tb_employees','tb_employees.id','=','tb_izins.id_employee')
 		->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
 		->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_izins.disetujui')
 		->leftjoin('tb_employees as tb_employees2','tb_employees2.id','=','tb_izins.personalia')
 		->leftjoin('tb_employees as tb_employees3','tb_employees3.id','=','tb_izins.scurity')
 		->where('tb_izins.status_disetujui','0')
		->where('tb_izins.is_deleted','0')
 		->get(['tb_izins.*','tb_employees.NIK','tb_employees.PIN','tb_employees.employee_name','tb_departments.dept_name','tb_employees1.employee_name as nama_atasan','tb_employees2.employee_name as nama_personalia','tb_employees3.employee_name as nama_scurity']);
 		
		$qty_permit_new=DB::table('tb_izins')
 		->leftjoin('tb_employees','tb_employees.id','=','tb_izins.id_employee')
 		->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
 		->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_izins.disetujui')
 		->leftjoin('tb_employees as tb_employees2','tb_employees2.id','=','tb_izins.personalia')
 		->leftjoin('tb_employees as tb_employees3','tb_employees3.id','=','tb_izins.scurity')
 		->where('tb_izins.status_disetujui','0')
		->where('tb_izins.is_deleted','0')
		->count();
		//return $qty_permit_new;

 		$tb_permit_proccess=DB::table('tb_izins')
 		->leftjoin('tb_employees','tb_employees.id','=','tb_izins.id_employee')
 		->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
 		->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_izins.disetujui')
 		->leftjoin('tb_employees as tb_employees2','tb_employees2.id','=','tb_izins.personalia')
 		->leftjoin('tb_employees as tb_employees3','tb_employees3.id','=','tb_izins.scurity')
 		->where('tb_izins.status_disetujui','<=','1')
 		->where('tb_izins.status_personalia','0')
		 ->where('tb_izins.is_deleted','0')
 		->get(['tb_izins.*','tb_employees.NIK','tb_employees.PIN','tb_employees.employee_name','tb_departments.dept_code','tb_departments.dept_name','tb_employees1.employee_name as nama_atasan','tb_employees2.employee_name as nama_personalia','tb_employees3.employee_name as nama_scurity']);
 		//$qty_permit_proccess=tb_izin::where('status_disetujui',1)->where('status_personalia',0)->count();
		$qty_permit_proccess=DB::table('tb_izins')
		->leftjoin('tb_employees','tb_employees.id','=','tb_izins.id_employee')
		->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
		->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_izins.disetujui')
		->leftjoin('tb_employees as tb_employees2','tb_employees2.id','=','tb_izins.personalia')
		->leftjoin('tb_employees as tb_employees3','tb_employees3.id','=','tb_izins.scurity')
		->where('tb_izins.status_disetujui','<=','1')
		->where('tb_izins.status_personalia','0')
		->where('tb_izins.is_deleted','0')->count();
	   //return $qty_permit_proccess;


        return view('page/admin/m_permit/permit_personalia',[
        	'tb_permit_new'=>$tb_permit_new,
        	'tb_permit_proccess'=>$tb_permit_proccess,
        	'qty_permit_new'=>$qty_permit_new,
        	'qty_permit_proccess'=>$qty_permit_proccess,
        	//'tb_permit_approve'=>$tb_permit_approve,
        	//'tb_permit_refuse'=>$tb_permit_refuse,
			'start'=>$start,
			'finish'=>$finish,
        	'menu'=>'permit'
        ]);
    }
    function personaliaSign($id,$status){
		date_default_timezone_set("Asia/Jakarta");
        $tb_cutoff=DB::table('tb_cutoffs')->where('usage','payroll')->get();
        foreach($tb_cutoff as $dt){
            $Batas_awal=$dt->start_implement;
        }

        $email=Auth::user()->email;
        $tb_email=DB::table('tb_emails')->where('email_address',$email)->get();
        foreach($tb_email as $dt){
            $id_employee=$dt->id_employee;
        }
        $approve=DB::table('tb_izins')->where('id',$id)->update(['personalia'=>$id_employee,'status_personalia'=>$status]);
        if($approve&&$status=='1'){
            $tb_izin=DB::table('tb_izins')->where('id',$id)->limit(1)->get();
            foreach($tb_izin as $dt2){

            	$periode_normal=date('Y-m',strtotime($dt2->apply_date));
		        $tgl_berjalan=date('d',strtotime($dt2->apply_date));
		        if($tgl_berjalan>=$Batas_awal){
		        	$periode_cutoff=date('Y-m',strtotime('+10 days',strtotime($dt2->apply_date)));
		        }else{
		        	$periode_cutoff=date('Y-m',strtotime($dt2->apply_date));
		        }
		        if($dt2->category=='A')$category="IJIN";
		        elseif($dt2->category=='B')$category="1/2 HARI";
		        else {
					$category="KELUAR PABRIK";
				}

                $simpan=DB::table('tb_absencies')->insert([
                    'id_employee'=>$dt2->id_employee,
                    'date_off'=>$dt2->apply_date,
					'minutes'=>$dt2->minutes,
                    'periode_normal'=>$periode_normal,
                    'periode_cutoff'=>$periode_cutoff,
                    'form_reference'=>'Permit',
                    'ref_id'=>$id,
                    'category'=>$category
                ]);            

				//Update 
				$day=date('d',strtotime($dt2->apply_date));
				$kolom="D".$day;
				if($category=='IJIN')$code='55';
				else $code='73'; 
				$update=DB::table('tb_work_entries')->where('plan_actual','actual')->where('id_employee',$dt2->id_employee)->where('periode',$periode_normal)->update([
					$kolom=>$code,
				]);
				//End Upate TMS

            }
        }
        return redirect()->back()->with(['success'=>'Update Success']);
    }

    function scurityPermit($awal,$akhir){
        date_default_timezone_set("Asia/Jakarta");
        $kalendar=CAL_GREGORIAN;
        if($awal==0){
            $periode=date('Y-m');
            $periodeBefore=date('Y-m-d',strtotime('-1 days',strtotime($periode.'-01')));
            $bln=date('Y-m',strtotime($periodeBefore.'-01'));
            $awal=date('Y-m-d',strtotime($bln.'-25'));
            $akhir=date('Y-m-d',strtotime($periode.'-24'));
        }else{
            $periode=date('Y-m',strtotime($awal));
        }
		$today=date('Y-m-d');
		$kmaren=date('Y-m-d',strtotime('-1 days',strtotime($today)));
		//return $kmaren;

 		$tb_permit_new=DB::table('tb_izins')
 		->leftjoin('tb_employees','tb_employees.id','=','tb_izins.id_employee')
 		->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
 		->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_izins.disetujui')
 		->leftjoin('tb_employees as tb_employees2','tb_employees2.id','=','tb_izins.personalia')
 		->leftjoin('tb_employees as tb_employees3','tb_employees3.id','=','tb_izins.scurity')
 		->where('tb_izins.status_disetujui','1')
 		->where('tb_izins.status_personalia','!=','2')
		->where('category','!=','A')
 		->where('tb_izins.status_scurity','0')
		->where('tb_izins.is_deleted','0')
		->where('apply_date','>=',$kmaren)
 		->get(['tb_izins.*','tb_employees.NIK','tb_employees.employee_name','tb_departments.dept_name','tb_employees1.employee_name as nama_atasan','tb_employees2.employee_name as nama_personalia','tb_employees3.employee_name as nama_scurity']);
 		$qty_permit_new=DB::table('tb_izins')->where('status_scurity',0)
 		->where('tb_izins.status_disetujui','1')
 		->where('tb_izins.status_personalia','!=','2')
		->where('category','!=','A')
 		->where('tb_izins.status_scurity','0')
		->where('tb_izins.is_deleted','0')
		->where('apply_date','>=',$kmaren)
		->count();

        return view('page/admin/m_permit/permit_scurity',[
        	'awal'=>$awal,'akhir'=>$akhir,
        	'tb_permit_new'=>$tb_permit_new,
        	'qty_permit_new'=>$qty_permit_new,
        	'menu'=>'permit'
        ]);
    }
    function scuritySign(Request $data){
        $email=Auth::user()->email;
        $tb_email=DB::table('tb_emails')->where('email_address',$email)->get();
        foreach($tb_email as $dt){
            $id_employee=$dt->id_employee;
        }
		$category=$data->category;
		$minutes=$data->minutes;
		if($data->minutes>180){
			$category='B';
			$minutes=0;
		}
		if($data->minutes>300){
			$category='A';
			$minutes=0;
		}
		//return $category;
		if($data->akses=='security'){
			$approve=DB::table('tb_izins')->where('id',$data->sysid)->update([
				'category'=>$category,
				'start_izin'=>$data->start_izin,
				'finish_izin'=>$data->finish_izin,
				'minutes'=>$minutes,
				'scurity'=>$id_employee,
				'status_scurity'=>'1'
			]);
		}
		if($data->akses=='personalia'){
			$approve=DB::table('tb_izins')->where('id',$data->sysid)->update([
				'category'=>$category,
				'start_izin'=>$data->start_izin,
				'finish_izin'=>$data->finish_izin,
				'minutes'=>$minutes,
				'personalia'=>$id_employee,
				'status_personalia'=>'1'
			]);
		}
        if($approve)return redirect()->back()->with(['success'=>'Update Success']);
    }
    function reportPermit($start, $finish) {
        date_default_timezone_set("Asia/Jakarta");
        
        if ($start == 0) {
            $periode_ini = date('Y-m');
            $bulan_lalu = date('Y-m-d', strtotime('-1 days', strtotime($periode_ini . '-01')));
            $periode_lalu = date('Y-m', strtotime($bulan_lalu));
            $start = date('Y-m-d', strtotime($periode_lalu . '-25'));
            $finish = date('Y-m-d', strtotime($periode_ini . '-24'));
        }
        
        $email = Auth::user()->email;
        $id_user = DB::table('tb_emails')->where('email_address', $email)->value('id_employee');
        // Get department IDs in one query instead of looping
        $dept_ids = DB::table('tb_admins')
            ->where('id_employee', $id_user)
            ->pluck('dept_id')
            ->toArray();
        
        // Build query with better structure
        $query = DB::table('tb_izins')
            ->leftJoin('tb_employees', 'tb_employees.id', '=', 'tb_izins.id_employee')
            ->leftJoin('tb_departments', 'tb_departments.id', '=', 'tb_employees.dept_id')
            ->leftJoin('tb_employees as tb_employees1', 'tb_employees1.id', '=', 'tb_izins.disetujui')
            ->leftJoin('tb_employees as tb_employees2', 'tb_employees2.id', '=', 'tb_izins.personalia')
            ->leftJoin('tb_employees as tb_employees3', 'tb_employees3.id', '=', 'tb_izins.scurity')
            ->where('tb_employees.status', '>', 0)
            ->where('is_deleted', 0)
            ->whereBetween('apply_date', [$start, $finish])
            ->where(function($q) use ($dept_ids) {
                // Include dept_id = 0 OR in admin's departments
                $q->where('tb_employees.dept_id', 0)
                ->orWhereIn('tb_employees.dept_id', $dept_ids);
            });
        
        $tb_permit = $query->get([
            'tb_izins.*',
            'tb_employees.NIK',
            'tb_employees.employee_name',
            'tb_departments.dept_code',
            'tb_departments.dept_name',
            'tb_employees1.employee_name as nama_atasan',
            'tb_employees2.employee_name as nama_personalia',
            'tb_employees3.employee_name as nama_security' // Fixed typo
        ]);
        
        return view('page/admin/m_permit/permit_report', [
            'tb_permit' => $tb_permit,
            'start' => $start,
            'finish' => $finish,
            'menu' => 'permit'
        ]);
    }
	function photoPermit($id){
		$path = storage_path('app/public/'.$id.'.jpg');
    
		if (!file_exists($path)) {
			abort(404);
		}
		
		return response()->file($path);
	}
    public function notificationPermit($id){
        $tb_izins	=DB::table('tb_izins')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_izins.id_employee')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->where('tb_izins.id',$id)
        ->get(['tb_izins.*','tb_employees.employee_name','tb_departments.dept_name']);
        $data['kontak']='';
        $data['pesan']='';
        $id_employee='';
        foreach($tb_izins as $dt){
            $id=$dt->id;
            $date=$dt->apply_date;
            $dept=$dt->dept_name;
            if($dt->status_disetujui==0){
                $id_employee=$dt->disetujui;
                $pos='Atasan';
            }else if($dt->status_personalia==0){
                $id_employee=$dt->personalia;
                $pos='Personalia';
            }
            $name=$dt->employee_name;
            if($id_employee!=''){
                $data['kontak']=DB::table('tb_employee_detail')->where('id_employee',$id_employee)->value('nomor_telepon');
                $data['pesan']="*NOTIFIKASI PERMIT*\n\nID: *$id*\nTanggal: *$date*\nNama: *$name*\nDepartemen: *$dept*\n\nMenunggu Approval Anda sebagai *$pos*.\n\nSegera lakukan pengecekan via EMS, klik link berikut:\nhttps://ems.summitadyawinsa.co.id/EMS/Permit/Approves/0/0";
            }
        }
        // $data['kontak']='08211212418';
        if($data['kontak']!=''){
            // \App\Http\Controllers\WhatsAppController::sendInternalMessage($data['kontak'], $data['pesan']);
            \App\Http\Controllers\WuzapiController::sendInternalMessage($data['kontak'], $data['pesan']);
            return 'Success';
        }else{
            return 'Failed';
        }
    }

}
