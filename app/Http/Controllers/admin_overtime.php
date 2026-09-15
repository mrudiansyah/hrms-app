<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DateTime;
use Auth;
use PDF;
use App\Http\Controllers\mail_controller;

class admin_overtime extends Controller
{
    public function __construct(){
        $this->middleware(['auth','verified']);
    }
    function index(){
        $admin=Auth::user()->name;
        $tb_overtime=DB::table('tb_overtimes')->leftjoin('tb_employees','tb_employees.id','=','tb_overtimes.diperintah')->where([['admin',$admin],['tb_overtimes.status','0']])->get(['tb_overtimes.*','tb_employees.employee_name']);
        $tb_department=DB::table('tb_departments')->select('tb_departments.*');

        $email=Auth::user()->email;
        $tb_email=DB::table('tb_emails')->where('email_address',$email)->get();
        foreach($tb_email as $dt){
            $id_employee=$dt->id_employee;
            $tb_admin=DB::table('tb_admins')->where('id_employee',$id_employee)->get();
            foreach($tb_admin as $dt2){
                $dept_id=$dt2->dept_id;
                $tb_department=$tb_department->orwhere([['id',$dept_id],['isDelete',0]]);
            }
        }
        $tb_department=$tb_department->get();

        //Check Lock
        $data['limit']=999;
        $limit_create=DB::table('tb_utilities')->where('id','13')->where('status','1')->get();
        foreach($limit_create as $dt){
            $data['limit']=$dt->limit_transaksi;
        }
        $lock_create=0;
        $cek_lock_create=DB::table('tb_utilities')->where('id','13')->where('status','1')->count();
        if($cek_lock_create==1){
            $results = DB::table('tb_overtimes')
            ->where('admin', $admin)
            ->where('status','1')
            ->where(function($query) {
                $query->where('status_diperintah', 0)
                    ->orWhere(function($q) {
                        $q->where('disetujui', '>', 0)
                            ->where('status_disetujui', 0);
                    })
                    ->orWhere(function($q) {
                        $q->where('diketahui', '>', 0)
                            ->where('status_diketahui', 0);
                    });
            })
            ->count();
            if($results>=$data['limit']){
                $lock_create=1;
            }
        }
        $lock_backdate=DB::table('tb_utilities')->where('id','14')->where('status','1')->count();
        $nama=Auth::user()->name;
		$now=date('Y-m-d H:i:s');
		$cek=DB::table('tb_utilities_exception')->where('id_utility','14')->where('admin',$nama)->where('status','1')->where('start','<=',$now)->where('end','>=',$now)->count();
		if($cek==1)$lock_backdate=0;


        return view('page/admin/m_overtime/formspl',['tb_overtime'=>$tb_overtime,'data'=>$data,'tb_departments'=>$tb_department,'lock_create'=>$lock_create,'lock_backdate'=>$lock_backdate,'menu'=>'overtime']);
    }
    function viewDraft($id){
        $tb_ot=DB::table('tb_overtimes')->where('id',$id)->get();
        foreach($tb_ot as $dt){
            $periode=date('ymd',strtotime($dt->created_at));
            $check=$dt->id_overtime;
        }
        $lastid=$id;
        $lastid2=$lastid%100000;
        $noreg=$periode.$lastid2;
        $id_overtime='SAI/OT/'.$noreg;
        if($check==''){
            $update=DB::table('tb_overtimes')->where('id',$lastid)->update(['id_overtime'=>$id_overtime]);
        }

        $update=DB::table('tb_overtimes')->where('id',$id)->update(['status'=>'0']);
        $tb_overtime=DB::table('tb_overtimes')->where('id',$id)->get();
        foreach($tb_overtime as $dt){
            $dept_id=$dt->dept_id;
            $id_overtime=$dt->id_overtime;
            $id_memo=$dt->id_memo;
        }
        // return $tb_overtime;
        $category=DB::table('tb_reason_ots')->select('category')->where('is_active','1')->groupby('category')->get();
        $tb_reason_ot=DB::table('tb_reason_ots')->where('is_active','1')->orderby('id','desc')->orderby('group_reason','asc')->get();
        $tb_job_ot=DB::table('tb_job_ot')->where('dept_id',$dept_id)->get();
        $tb_customer=DB::table('tb_customer')->get();

        $tb_pesan=DB::table('tb_overtime_notes')->where('id_ot',$id)->get();

        $admin=Auth::user()->name;
        $tb_pesan=DB::table('tb_overtime_notes')->where('id_ot',$id)->get();
        $tb_employee=DB::table('tb_employees')->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')->where([['tb_employees.status','1'],['tb_positions.position_index','<','3']])->orderby('employee_name','asc')->get(['tb_employees.*','tb_departments.dept_code']);
       

        $data['table1']=DB::table('tb_memo')->where('tb_memo.id_category','1')->where('tb_memo.is_delete','0')->where('tb_memo.is_completed','2')->get();
        $today=date('Y-m-d');
        $tb_ref=DB::table('tb_memo')->where('tb_memo.id_category','1')->where('tb_memo.is_delete','0')->where('tb_memo.description','LIKE','Planned Overtime%')->where('date_information','>=',$today)->get();
        //$tb_ref='';
        //return $data['table1'];
        $data['table2']=DB::table('tb_group_reason_ot')->select('category')->groupby('category')->whereNotNull('category')->get();
        $data['table3']=DB::table('tb_group_reason_ot')->select('detail_category')->groupby('detail_category')->whereNotNull('detail_category')->get();
       
        //return view('page/admin/m_overtime/formspl_detail_memo',['tb_pesan'=>$tb_pesan,'tb_reason_ot'=>$tb_reason_ot,'tb_job_ot'=>$tb_job_ot,'tb_customer'=>$tb_customer,'tb_overtime'=>$tb_overtime,'tb_employee'=>$tb_employee,'dept_id'=>$dept_id,'tb_draft_overtime'=>$tb_draft_overtime,'menu'=>'overtime','data'=>$data]);
       return view('page/admin/m_overtime/formspl_detail',['tb_pesan'=>$tb_pesan,'tb_reason_ot'=>$tb_reason_ot,'category_list'=>$category,'tb_ref'=>$tb_ref,'tb_job_ot'=>$tb_job_ot,'tb_customer'=>$tb_customer,'tb_overtime'=>$tb_overtime,'tb_employee'=>$tb_employee,'dept_id'=>$dept_id,'menu'=>'overtime']);
    }
    public function GetListLine(Request $request){
         $searchTerm = $request->searchTerm ;     
          $dept_id = $request->dept_id ;
          if($dept_id == 7){
            $category = "NON STP";
          }else if($dept_id == 11) {
            $category = "STP";
          }
          $page = $request->page ; 
        //   $line = $request->line;
          $resultCount = 25 ; 
          $offset = ($page - 1) * $resultCount;
          $db = array() ;  
          $db = DB::table('tb_line as a')
                ->leftJoin('tb_machine as b','b.IdLine','=','a.id')
                ->where('b.isactive','1')
                ->where('a.category',$category)
              ->where('a.line', 'LIKE',  '%' . $searchTerm . '%')
                // ->where('a.line',$line)
              ->orderBy('a.id','asc')->offset($offset)->limit($resultCount)
              ->get(['b.id',DB::raw("concat(a.line, ' - ',b.DetailLine) as text ")]) ;  
          $count = Count(DB::table('tb_line as a')
                ->leftJoin('tb_machine as b','b.IdLine','=','a.id')
                ->where('b.isactive','1')
                ->where('a.category',$category)
                // ->where('a.line',$line)
              ->where('a.line', 'LIKE',  '%' . $searchTerm . '%')
              ->orderBy('b.id','asc')
              ->get(['b.id', DB::raw("concat(a.line, ' - ',b.DetailLine) as text")])) ;
              $endCount = $offset + $resultCount ; 
              $morePages = $count > $endCount ;  
          $results = array(
              "results" => $db,
              "pagination" => array(
              "more" => $morePages
          )
          );     
          echo json_encode($results);

    }
    function saveNote(Request $data){
        date_default_timezone_set("Asia/Jakarta");
        $admin=Auth::user()->name;
        $today=date('Y-m-d H:i:s');
        $periksa=tb_overtime_note::where('id_ot',$data->id_ot)->where('penulis',$admin)->where('pesan',$data->pesan)->count();
        if($periksa==0){
            $simpan=DB::table('tb_overtime_notes')->insert([
                'id_ot'=>$data->id_ot,
                'waktu'=>$today,
                'penulis'=>$admin,
                'pesan'=>$data->pesan
            ]);
        }
        return redirect()->back();
    }
    function showKonten(Request $data){
        $tb_overtime_detail=DB::table('tb_overtime_details')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_overtime_details.id_employee')
        ->leftjoin('tb_machine','tb_machine.id','=','tb_overtime_details.id_line_machine')
        ->leftjoin('tb_line','tb_line.id','=','tb_machine.IdLine')
        ->where('id_overtime',$data->id_overtime)
        ->get(['tb_overtime_details.*','tb_employees.NIK','tb_employees.employee_name',DB::raw("concat(tb_line.line, ' - ',tb_machine.DetailLine) as line")]);
        $konten="";
        $no=0;
        foreach($tb_overtime_detail as $dt){
            $no++;
            $konten.="<tr>";
            $konten.="<td>".$no."</td><td>".$dt->NIK."</td><td>".$dt->employee_name."</td><td>".date('H:i',strtotime($dt->start_plan))."~".date('H:i',strtotime($dt->finish_plan))."</td>";
            $konten.="<td>".($dt->line == "" ? "" : $dt->line)."</td>";
            $konten.="<td>".$dt->reason;
            $konten.="</td>";
            $konten.="<td>";
            $konten.="<button title='Delete' type='button' class='delete-modal btn btn-danger btn-xs' data-delid='$dt->id' data-delname='$dt->employee_name'><i class='fa fa-trash'></i></button>";

            $konten.="</td>";
            $konten.="</tr>";
        }
        if($konten==""){
            $konten="<tr><td colspan='6' style='text-align:center;'>No Data Recorded</td></tr>";
        }
        return $konten;
    }
    function selectReason(Request $data){
        $category=$data->category;
        if($category==''){
            $tb1=DB::table('tb_reason_ots')->where('is_active','1')->get();
        }else{
            $tb1=DB::table('tb_reason_ots')->where('category',$category)->where('is_active','1')->get();
        }
        $datas="<option value=''></pilih>";
        foreach ($tb1 as $dt1 ){
            $datas.= "<option value='".$dt1->reason_ot."'>".$dt1->reason_ot."</option>";
        }
        return $datas;
    }
    function saveDetail(Request $data){
        $start_plan = DateTime::createFromFormat('Y-m-d\TH:i', $data->start_plan);
        $finish_plan = DateTime::createFromFormat('Y-m-d\TH:i', $data->finish_plan);
        if($start_plan === false || $finish_plan === false){
            return response('Format tanggal overtime tidak valid', 422);
        }
        $start_plan = $start_plan->format('Y-m-d H:i:s');
        $finish_plan = $finish_plan->format('Y-m-d H:i:s');
        $limt_bawah=date('Y-m-d H:i:s',strtotime('-1 hours',strtotime($start_plan)));
        $limt_atas=date('Y-m-d H:i:s',strtotime('1 hours',strtotime($start_plan)));
        $check=DB::table('tb_overtime_details')->where('id_employee',$data->id_employee)->where('start_plan','>=',$limt_bawah)->where('start_plan','<=',$limt_atas)->where('isDelete','0')->count();
        //$check=tb_overtime_detail::where('id_employee',$data->id_employee)->where('start_plan',$data->start_plan)->count();
        $tb_salary=DB::table('tb_salaries')->leftjoin('tb_employees','tb_employees.id','=','tb_salaries.id_employee')->where('tb_salaries.id_employee',$data->id_employee)->get(['tb_salaries.*','tb_employees.position_id']);
        foreach ($tb_salary as $dt) {
            $slpj=round($dt->slpj);
            $ammount=$slpj*$data->hours_convertion;
            $konversi=$data->hours_convertion;
            if($dt->position_id=='19'||$dt->position_id=='23'){
                $konversi=$data->hours_plan;
                $ammount=$slpj*$data->hours_plan;
            }
        }
        $tb_driver=DB::table('tb_employees')->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')->leftjoin('tb_utilities','tb_utilities.atribut','=','tb_positions.position_name')->where('tb_employees.id',$data->id_employee)->where('tb_employees.status','1')->get(['tb_employees.*','tb_utilities.status']);
        foreach($tb_driver as $dt_driver){
            if($dt_driver->status>0&&$dt_driver->position_id=='18'&&$dt_driver->dept_id=='10')$ammount=$dt_driver->status;
            //if($dt_driver->status>0)$ammount=$dt_driver->status;
        }
        //$check=0;
        $tb_cd_qty=DB::table('tb_changedays')->where('id_employee',$data->id_employee)->where('date_on',$data->date_on)->count();
        if(isset($slpj)&&$check==0&&$tb_cd_qty==0){
            $simpan=DB::table('tb_overtime_details')->insert([
                'id_ot'=>$data->idot,
                'id_overtime'=>$data->id_overtime,
                'id_employee'=>$data->id_employee,
                'ot_category'=>$data->ot_category,
                'date_on'=>$data->date_on,
                'reason_ot'=>$data->reason_ot,
                'job_ot'=>$data->job_ot,
                'customer'=>$data->customer,
                'reason'=>$data->reason,
                'start_plan'=>$start_plan,
                'finish_plan'=>$finish_plan,
                'start_act'=>$start_plan,
                'finish_act'=>$finish_plan,
                'minutes_break'=>$data->otisoma,
                'hours_plan'=>$data->hours_plan,
                'hours_act'=>$data->hours_plan,
                'hours_convertion'=>$konversi,
                'SLPJ'=>$slpj,
                'ammount'=>$ammount,
                'sign_before'=>'0',
                'sign_after'=>'0',
                'status'=>'0',
                'id_draft_overtime'=>$data->iddraftovertime,
                'id_line_machine' =>($data->id_line_machine != 0 ? $data->id_line_machine : 0 ),
                'id_dept'=>$data->iddept,
                'nama_department'=>$data->nmdept,
                'pic_or_subtitusi'=>$data->pic,
                'reference'=>$data->reference,
            ]);
            
        }
        $tb_overtime_detail=DB::table('tb_overtime_details')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_overtime_details.id_employee')
        ->leftjoin('tb_machine','tb_machine.id','=','tb_overtime_details.id_line_machine')
        ->leftjoin('tb_line','tb_line.id','=','tb_machine.IdLine')
        ->where('id_overtime',$data->id_overtime)
        ->get(['tb_overtime_details.*','tb_employees.NIK','tb_employees.employee_name',DB::raw("concat(tb_line.line, ' - ',tb_machine.DetailLine) as line")]);
        $konten="";
        $no=0;
        foreach($tb_overtime_detail as $dt){
            if($data->deptid=='7'||$data->deptid=='11'){
                $cek=DB::table('tb_overtime_details_review')->where('id_head',$data->idot)->where('id_detail',$dt->id)->count();
                if($cek==0){
                    $add=DB::table('tb_overtime_details_review')->insert([
                        'id_head'=>$data->idot,
                        'id_detail'=>$dt->id,
                        'dept_id'=>$data->deptid
                    ]);
                }
            }
            $no++;
            $konten.="<tr>";
            $konten.="<td>".$no."</td><td>".$dt->NIK."</td><td>".$dt->employee_name."</td><td>".date('H:i',strtotime($dt->start_plan))."~".date('H:i',strtotime($dt->finish_plan))."</td>";
            $konten.="<td>".($dt->line == "" ? "" : $dt->line)."</td>";
            $konten.="<td>".$dt->reason."</td><td>";

            $konten.="<div class='pull-right'>";
            $konten.="<button title='Delete' type='button' class='delete-modal btn btn-danger btn-xs' data-delid='$dt->id' data-delname='$dt->employee_name'><i class='fa fa-trash'></i></button>";

            $konten.="</div></td>";
            $konten.="</tr>";
        }
        $pesan='';
        if($check>0){
            $tb_ot=DB::table('tb_overtime_details')
            ->leftjoin('tb_overtimes','tb_overtimes.id','=','tb_overtime_details.id_ot')
            ->where('id_employee',$data->id_employee)->where('start_plan','>=',$limt_bawah)->where('start_plan','<=',$limt_atas)
            ->get(['tb_overtimes.id_overtime','tb_overtimes.admin']);
            foreach($tb_ot as $dt){
                $pesan="Note: Sudah masuk di No.".$dt->id_overtime.", Silahkan konfirmasi ke ".$dt->admin." sebagai pembuat SPL";
            }
            return $pesan;
        }elseif(!isset($slpj)){
            $pesan='Note: SLPJ belum tersedia,';
            return $pesan;
        }elseif($tb_cd_qty>0){
            $pesan='Note: Ada jadwal ganti hari untuk Employee ini';
            return $pesan;
        }
        else return $konten;

    }
    function deleteDetail($id){
        $tb_ot=DB::table('tb_overtime_details')->where('id',$id)->get();
        $id_draft_overtime='';
        foreach($tb_ot as $dt){
            $id_draft_overtime=$dt->id_draft_overtime;
        }
        $delete=DB::table('tb_overtime_details')->where('id',$id)->delete();
        if($delete){
            return redirect()->back()->with(['success' => 'Delete SPL Berhasil']); 
        }
        else echo "gagal";
    }
    function signBeforeAll($id){
        $tb1=DB::table('tb_overtime_details')->where('id_ot',$id)->where('sign_before','0')->count();
        if($tb1>0){
            $update=DB::table('tb_overtime_details')->where('id_ot',$id)->update([
                'sign_before'=>'1'
            ]);
            //$send_whatsapp=$this->messageOvertime($id);
        }
        return redirect()->back();
    }
    function quotaOT(Request $data){
        $dateon=$data->dateon;
        $weekday=date('w',strtotime($dateon));
        $Tglawal=date('Y-m-d',strtotime('-'.$weekday.' days',strtotime($dateon)));
        $Tglakhir=date('Y-m-d',strtotime('+6 days',strtotime($Tglawal)));
        
        //return $Tglawal;
        // DB::table('tb_overtime_details')->where('id_employee',$data->id_employee)->get();
        $tb_sumot=DB::table('tb_overtime_details')
        ->select('id_employee', DB::raw('SUM(hours_act) as total_act,SUM(hours_convertion) as total_convertion'))
        ->groupBy('id_employee')
        ->where('id_employee',$data->id_employee)
        ->where('date_on','>=',$Tglawal)
        ->where('date_on','<=',$Tglakhir)
        ->get();
        $terpakai=0;
        foreach($tb_sumot as $dt){
            $terpakai=$dt->total_act;
        }
        $kuota=14;
        $sisa=$kuota-$terpakai;

        $tb_employee=DB::table('tb_employees')->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')->where('tb_employees.id',$data->id_employee)->get();
        foreach($tb_employee as $dt){
            $id_dept=$dt->dept_id;
            $nm_dept=$dt->dept_code;
            if($id_dept==$data->dept_id)$pic='PIC';
            else $pic='Subtitusi';
    
        }

        $slpj=0;
        $tb_salary=DB::table('tb_salaries')->leftjoin('tb_employees','tb_employees.id','=','tb_salaries.id_employee')->where('tb_salaries.id_employee',$data->id_employee)->get(['tb_salaries.*','tb_employees.position_id']);
        foreach ($tb_salary as $dt) {
            $slpj=round($dt->slpj);
        }

        return $id_dept.'#'.$nm_dept.'#'.$pic.'#'.$sisa.'#'.$slpj;
        //if($sisa<=7)return "Batas overtime mingguan: ".$sisa." jam";
        //else return false;
    }    
    function selectOrder(Request $data){
        $admin=Auth::user()->name;
        $deptid=$data->deptid;
        $dtmix=explode('#',$deptid);
        $dtid=$dtmix['0'];
        $tdname=$dtmix['1'];

        $diperintah="<option value=''>&nbsp;</pilih>";
        $dt_employee=DB::table('tb_approval_spl')->leftjoin('tb_employees','tb_employees.id','=','tb_approval_spl.diperintah')->where('tb_approval_spl.dept_id',$dtid)->get(['tb_employees.*']);
        foreach ($dt_employee as $dt ){
            $diperintah.= "<option value='".$dt->id."'>".$dt->employee_name."</option>";
        }
        return $diperintah;
    }
    function selectApproved(Request $data){
        $dipid=$data->dipid;

        $dt_employee=DB::table('tb_approval_spl')->leftjoin('tb_employees','tb_employees.id','=','tb_approval_spl.disetujui')->where('tb_approval_spl.diperintah',$dipid)->get(['tb_employees.*']);

        $disetujui="<option value=''></pilih>";
        foreach ($dt_employee as $dt ){
            $disetujui.= "<option value='".$dt->id."'>".$dt->employee_name."</option>";
        }
        return $disetujui;
    }
    function selectSeen(Request $data){
        $disid=$data->disid;

        $dt_employee=DB::table('tb_approval_spl')->leftjoin('tb_employees','tb_employees.id','=','tb_approval_spl.diketahui')->where('tb_approval_spl.disetujui',$disid)->orderby('tb_employees.id','asc')->get(['tb_employees.*']);

        $diketahui="<option value=''></pilih>";
        $item='';
        foreach ($dt_employee as $dt){
            if($item!=$dt->id)
            $diketahui.= "<option value='".$dt->id."'>".$dt->employee_name."</option>";
            $item=$dt->id;
        }
        return $diketahui;
    }
    function createspl(Request $data){
        $admin=Auth::user()->name;

        $this->validate($data,[
            'ot_date'=>'required',
            'dept_id'=>'required',
            'diperintah'=>'required',
            //'disetujui'=>'required'
        ]);
        $deptid=$data->dept_id;
        $dtmix=explode('#',$deptid);
        $dtid=$dtmix['0'];
        $dtname=$dtmix['1'];

        $ip_address=$_SERVER['REMOTE_ADDR'];
        date_default_timezone_set("Asia/Jakarta");
        $today=date('Y-m-d');
        $id_overtime=$data->id_overtime;

        $tb_approval=DB::table('tb_approvals')->where('id','1')->get();
        foreach($tb_approval as $dt){
            $diketahui_hr=$dt->diketahui;
            //$dicatat=$dt->dicatat;
            $approve=$dt->approve;
            $paid=$dt->paid;
        }

        if($id_overtime==''){
            $newid=DB::table('tb_overtimes')->insert([
                'doc_date'=>$today,
                'ot_date'=>$data->ot_date,
                'dept_id'=>$dtid,
                'dept_name'=>$dtname,
                'diperintah'=>$data->diperintah,
                'disetujui'=>$data->disetujui,
                'diketahui'=>$data->diketahui,
                'dicatat'=>'122',
                'approve'=>'122',
                'paid'=>$paid,
                'status_diperintah'=>'0',
                'status_disetujui'=>'0',
                'status_diketahui'=>'0',
                'status_dicatat'=>'0',
                'status_approve'=>'0',
                'status_paid'=>'0',
                'admin'=>$admin,
                'ip_address'=>$ip_address,
                'status'=>'0'
            ]);
        }else{
            $newid=DB::table('tb_overtimes')->where('id_overtime',$id_overtime)->update([
                'ot_date'=>$data->ot_date,
                'dept_id'=>$dtid,
                'dept_name'=>$dtname,
                'diperintah'=>$data->diperintah,
                'disetujui'=>$data->disetujui,
                'admin'=>$admin,
                'ip_address'=>$ip_address,
            ]);
            $update_detail=DB::table('tb_overtime_details')->where('id_overtime',$id_overtime)->update(['date_on'=>$data->ot_date]);
        }
        
        //return redirect('/Admin/Overtime/Detail');
        $jml=DB::table('tb_overtimes')->where([['ip_address',$ip_address],['admin',$admin],['status','0']])->count();
        if($jml>1){
            return redirect()->back()->with(['success' => 'Create SPL Berhasil, silahkan pilih di draft dikarenakan terdapat lebih dari 1 SPL']);
        }else{
            $tb=DB::table('tb_overtimes')->where([['ip_address',$ip_address],['admin',$admin],['status','0']])->get();
            foreach($tb as $dt){
                return redirect('/Admin/Overtime/Draft/'.$dt->id);
            }
        }
    }
    function confirmSPL($id){
        $n_detail=DB::table('tb_overtime_details')->where('id_ot',$id)->count();
        if($n_detail==0){
            return redirect()->back()->with(['success' => 'Data masih kosong, silahkan isi.']);
        }
        $update=DB::table('tb_overtimes')->where('id',$id)->update(['status'=>'1']);
        if($update){
            //app('App\Http\Controllers\mail_controller')->OTMail($id);
            return redirect('/Admin/Overtime/Dept/'.$id)->with(['success' => 'Confirm Employee Berhasil']);
        }
    }
    function bodyOT($id){
        $admin=Auth::user()->name;
        $tb_overtime=DB::table('tb_overtimes')->where('id',$id)->get();
        foreach($tb_overtime as $dt){
            $dept_id=$dt->dept_id;
        }
        //if($admin=='Ratih Anggita'||$admin=='UNAY TAKIYUDIN'||$admin=='Cahyudin'||$admin=='Siti Nurfazriah'||$admin=='Nova Triana Nofianti'||$admin=='Meiliya Rizki'||$admin=='Hani Putriyani'||$admin=='Robby Rubaidy'||$admin=='Angella Puspa Dewi'||$admin=='Aenal Kaisha'||$admin=='Ardilah'||$admin=='RAMLAN'||$admin=='Ramadianti')
        $tb_employee=DB::table('tb_employees')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->where('tb_employees.status','1')
        ->where('tb_positions.position_index','<','4')
        ->whereNotExists(function ($query) use($id){
            $query->select(DB::raw(1))
                    ->from('tb_overtime_details')
                    ->whereColumn('tb_overtime_details.id_employee', 'tb_employees.id')
                    ->where('tb_overtime_details.id_ot',$id);
        })
        ->get(['tb_employees.*','tb_departments.dept_code']);
        //else $tb_employee=DB::table('tb_employees')->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')->where([['tb_employees.dept_id',$dept_id],['tb_employees.status','1'],['tb_positions.position_index','<','4']])->get(['tb_employees.*','tb_departments.dept_code']);

        $tb_overtime_detail=DB::table('tb_overtime_details')->leftjoin('tb_overtimes','tb_overtimes.id','=','tb_overtime_details.id_ot')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_overtime_details.id_employee')
        ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_overtime_details.id_employee_plan')
        ->where('tb_overtime_details.id_ot',$id)->get(['tb_overtime_details.*','tb_employees.NIK','tb_employees.employee_name','tb_overtimes.status_diketahui','tb_overtimes.status_dicatat','tb_employees1.employee_name as employee_plan']);
        return view('page/admin/m_overtime/realisationspl',['idspl'=>$id,'tb_overtime_detail'=>$tb_overtime_detail,'tb_employee'=>$tb_employee,'menu'=>'overtime','submenu'=>'dept']);
    }
    function headOT($periode){
        date_default_timezone_set("Asia/Jakarta");
        $kalendar=CAL_GREGORIAN;
        if($periode==0)$periode=date('Y-m');
        $thn=date('Y',strtotime($periode.'-01'));
        $bln=date('m',strtotime($periode.'-01'));
        $hariakhir=cal_days_in_month($kalendar,$bln,$thn);
        $periode_awal=date('Y-m-d',strtotime($thn.'-'.$bln.'-01'));
        $periode_akhir=date('Y-m-d',strtotime($thn.'-'.$bln.'-'.$hariakhir));

        $admin=Auth::user()->name;
        $tb_overtime_plan=DB::table('tb_overtimes')
        ->leftjoin('tb_employees as tb1','tb1.id','=','tb_overtimes.diperintah')
        ->leftjoin('tb_employees as tb2','tb2.id','=','tb_overtimes.disetujui')
        ->leftjoin('tb_employees as tb3','tb3.id','=','tb_overtimes.diketahui')
        ->select('tb_overtimes.*','tb1.employee_name as nm1','tb2.employee_name as nm2','tb3.employee_name as nm3')
        ->where('tb_overtimes.status','1')
        ->where('tb_overtimes.isDelete','0')
        ->whereExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('tb_overtime_details')
                ->whereColumn('tb_overtime_details.id_ot', 'tb_overtimes.id')
                ->where([['tb_overtime_details.sign_before','0'],['tb_overtime_details.status','<','90']]);
        })
        ->where('tb_overtimes.admin',$admin);

        $tb_overtime_actual=DB::table('tb_overtimes')
        ->whereExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('tb_overtime_details')
                ->whereColumn('tb_overtime_details.id_ot', 'tb_overtimes.id')
                ->where('tb_overtime_details.sign_before','1')
                ->where('tb_overtime_details.sign_after','0');
        })
        ->where('tb_overtimes.status','1')
        ->where('tb_overtimes.isDelete','0')
        ->where('tb_overtimes.admin',$admin);

        $tb_overtime=DB::table('tb_overtimes')
        ->whereExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('tb_overtime_details')
                ->whereColumn('tb_overtime_details.id_ot', 'tb_overtimes.id')
                ->where('tb_overtime_details.sign_before','1')
                ->where('tb_overtime_details.sign_after','1');
        })
        ->where('tb_overtimes.status','1')
        ->where('ot_date','>=',$periode_awal)
        ->where('ot_date','<=',$periode_akhir)
        ->where('tb_overtimes.admin',$admin);

        $tb_overtime_plan = $tb_overtime_plan->get();
        //return $tb_overtime_plan;
        $tb_overtime_actual = $tb_overtime_actual->get();
        $tb_overtime = $tb_overtime->get();
        $jmlot_plan=$tb_overtime_plan->count();
        $jmlot_actual=$tb_overtime_actual->count();
        return view('page/admin/m_overtime/createdspl',['tb_overtime_plan'=>$tb_overtime_plan,'tb_overtime_actual'=>$tb_overtime_actual,'tb_overtime'=>$tb_overtime,'jmlot_plan'=>$jmlot_plan,'jmlot_actual'=>$jmlot_actual,'periode'=>$periode,'cabang'=>'Depts','menu'=>'overtime','submenu'=>'realisation']);


    }
    function updateSales(Request $data){
        $simpan=DB::table('tb_overtime_sales')->where('periode',$data->periodesales)->update(['amount_sales'=>$data->salesammount]);
        if($simpan)return redirect()->back()->with(['success'=>'Sales Amount Updated']);
    }
    function approveSPL($id){
        $tb_overtime=DB::table('tb_overtimes')->leftjoin('tb_departments','tb_departments.id','=','tb_overtimes.dept_id')->where([['tb_overtimes.id',$id],['tb_overtimes.status','1']])->get(['tb_overtimes.*','tb_departments.dept_name']);
        $tb_overtime_detail=DB::table('tb_overtime_details')->leftjoin('tb_employees','tb_employees.id','=','tb_overtime_details.id_employee')->where('tb_overtime_details.id_ot',$id)->get(['tb_overtime_details.*','tb_employees.NIK','tb_employees.employee_name']);

        $disetujui='';
        $disetujui_j='';
        $diketahui='';
        $diketahui_j='';
        $dicatat='';
        $dicatat_j='';

        foreach($tb_overtime as $dt){
            //$qry=DB::table('tb_employees')->leftjoin('tb_positions','tb_positions.id','tb_employees.position_id')->select('tb_employees.*','tb_positions.position_name')->where('status','1')->where('tb_employees.id',$dt->diperintah)->get();
            $qry=DB::table('tb_employees')->leftjoin('tb_positions','tb_positions.id','tb_employees.position_id')->select('tb_employees.*','tb_positions.position_name')->where('tb_employees.id',$dt->diperintah)->get();
            foreach($qry as $dt2){
                $diperintah=$dt2->employee_name;
                $diperintah_j=$dt2->position_name;
            }
            $qry=DB::table('tb_employees')->leftjoin('tb_positions','tb_positions.id','tb_employees.position_id')->select('tb_employees.*','tb_positions.position_name')->where('status','1')->where('tb_employees.id',$dt->disetujui)->get();
            foreach($qry as $dt2){
                $disetujui=$dt2->employee_name;
                $disetujui_j=$dt2->position_name;
            }
            $qry=DB::table('tb_employees')->leftjoin('tb_positions','tb_positions.id','tb_employees.position_id')->select('tb_employees.*','tb_positions.position_name')->where('status','1')->where('tb_employees.id',$dt->diketahui)->get();
            foreach($qry as $dt2){
                $diketahui=$dt2->employee_name;
                $diketahui_j=$dt2->position_name;
            }
            $qry=DB::table('tb_employees')->leftjoin('tb_positions','tb_positions.id','tb_employees.position_id')->select('tb_employees.*','tb_positions.position_name')->where('status','1')->where('tb_employees.id',$dt->dicatat)->get();
            foreach($qry as $dt2){
                $dicatat=$dt2->employee_name;
                $dicatat_j=$dt2->position_name;
            }
            $qry=DB::table('tb_employees')->leftjoin('tb_positions','tb_positions.id','tb_employees.position_id')->select('tb_employees.*','tb_positions.position_name')->where('status','1')->where('tb_employees.id',$dt->approve)->get();
            foreach($qry as $dt2){
                $approve=$dt2->employee_name;
                $approve_j=$dt2->position_name;
            }
            $qry=DB::table('tb_employees')->leftjoin('tb_positions','tb_positions.id','tb_employees.position_id')->select('tb_employees.*','tb_positions.position_name')->where('status','1')->where('tb_employees.id',$dt->paid)->get();
            foreach($qry as $dt2){
                $paid=$dt2->employee_name;
                $paid_j=$dt2->position_name;
            }
        }
        $email=Auth::user()->email;
        $tb_email=DB::table('tb_emails')->where('email_address',$email)->get();
        $id_employee='';
        foreach($tb_email as $dt){
            $id_employee=$dt->id_employee;
        }
        $status_before=DB::table('tb_overtime_details')->where([['sign_before','0'],['id_ot',$id]])->count();
        $status_after=DB::table('tb_overtime_details')->where([['sign_after','0'],['id_ot',$id],['status','<','10']])->count();

        $tb_pesan=DB::table('tb_overtime_notes')->where('id_ot',$id)->orderby('waktu','asc')->get();

        
        return view('page/admin/m_overtime/approvalspl',['idSPL'=>$id,'tb_pesan'=>$tb_pesan,'tb_overtime'=>$tb_overtime,'tb_overtime_detail'=>$tb_overtime_detail,'diperintah'=>$diperintah,'disetujui'=>$disetujui,'diketahui'=>$diketahui,'dicatat'=>$dicatat,'approve'=>$approve,'paid'=>$paid,'diperintah_j'=>$diperintah_j,'disetujui_j'=>$disetujui_j,'diketahui_j'=>$diketahui_j,'dicatat_j'=>$dicatat_j,'approve_j'=>$approve_j,'paid_j'=>$paid_j,'id_loger'=>$id_employee,'status_before'=>$status_before,'status_after'=>$status_after,'id_employee'=>$id_employee,'menu'=>'overtime']);
    }
    function entryStatus(Request $data){
        DB::table('tb_overtime_details')->where('id',$data->detailid)->update([
            'status'=>$data->detailstatus,
        ]);
    }    
    function signSPL($id,$posisi){
        date_default_timezone_set("Asia/Jakarta");
        $sekarang=date('Y-m-d H:i:s');
        if($posisi=='diperintah'){
            $sign=DB::table('tb_overtimes')->where('id',$id)->update(['status_diperintah'=>'1','date_diperintah'=>$sekarang]);
            $send_whatsapp=$this->messageOvertime($id);
            //app('App\Http\Controllers\mail_controller')->OTMail($id);
        }elseif($posisi=='disetujui'){
            $sign=DB::table('tb_overtimes')->where('id',$id)->update(['status_disetujui'=>'1','date_disetujui'=>$sekarang]);
            $send_whatsapp=$this->messageOvertime($id);
            //app('App\Http\Controllers\mail_controller')->OTMail($id);
        }elseif($posisi=='diketahui'){
            $sign=DB::table('tb_overtimes')->where('id',$id)->update(['status_diketahui'=>'1','date_diketahui'=>$sekarang]);
            //app('App\Http\Controllers\mail_controller')->OTMail($id);
        }elseif($posisi=='dicatat'){
            $sign=DB::table('tb_overtimes')->where('id',$id)->update(['status_dicatat'=>'1','date_dicatat'=>$sekarang]);
            //app('App\Http\Controllers\mail_controller')->OTMail($id);
        }elseif($posisi=='approve'){
            $sign=DB::table('tb_overtimes')->where('id',$id)->update(['status_approve'=>'1','date_approve'=>$sekarang]);
        }elseif($posisi=='paid'){
            $sign=DB::table('tb_overtimes')->where('id',$id)->update(['status_paid'=>'1','date_paid'=>$sekarang]);
            //$hasil=$this->update_summary($id,'add');
            //return $hasil;
            return redirect('/Admin/Overtime/Verifications2/0')->with(['success' => "SPL berhasil Anda Confirmasi"]);
        }

        if($sign)
        $teks="SPL ini berhasil Anda tanda tangani";
        return redirect()->back()->with(['success' => $teks]);    
    }
    function deniedSPL($id,$posisi){
        date_default_timezone_set("Asia/Jakarta");
        $sekarang=date('Y-m-d H:I:s');
        if($posisi=='diperintah'){
            $reject=DB::table('tb_overtimes')->where('id',$id)->update(['status_diperintah'=>'0','date_diperintah'=>$sekarang]);
        }elseif($posisi=='disetujui'){
            $reject=DB::table('tb_overtimes')->where('id',$id)->update(['status_disetujui'=>'0','date_disetujui'=>$sekarang]);
        }elseif($posisi=='diketahui'){
            $reject=DB::table('tb_overtimes')->where('id',$id)->update(['status_diketahui'=>'0','date_diketahui'=>$sekarang]);
        }elseif($posisi=='dicatat'){
            $reject=DB::table('tb_overtimes')->where('id',$id)->update(['status_dicatat'=>'0','date_dicatat'=>$sekarang]);
        }elseif($posisi=='approve'){
            $reject=DB::table('tb_overtimes')->where('id',$id)->update(['status_approve'=>'0','date_approve'=>$sekarang]);
        }elseif($posisi=='paid'){
            $reject=DB::table('tb_overtimes')->where('id',$id)->update(['status_paid'=>'0','date_paid'=>$sekarang]);
        }

        //if($reject)
        $teks="Anda memutuskan untuk menunda SPL ini, informasikan Staff Admin untuk memeriksa ulang atau membatalkan SPL tersebut.";
        return redirect()->back()->with(['success' => $teks]);    
        //echo "Masuk";
    }
    function reviewSPL($id,$posisi){
        if($posisi=='diperintah'){
            $review=DB::table('tb_overtimes')->where('id',$id)->update(['status_diperintah'=>'0']);
        }elseif($posisi=='disetujui'){
            $review=DB::table('tb_overtimes')->where('id',$id)->update(['status_disetujui'=>'0']);
        }elseif($posisi=='diketahui'){
            $review=DB::table('tb_overtimes')->where('id',$id)->update(['status_diketahui'=>'0']);
        }elseif($posisi=='dicatat'){
            $review=DB::table('tb_overtimes')->where('id',$id)->update(['status_dicatat'=>'0']);
        }elseif($posisi=='approve'){
            $review=DB::table('tb_overtimes')->where('id',$id)->update(['status_approve'=>'0']);
        }elseif($posisi=='paid'){
            $review=DB::table('tb_overtimes')->where('id',$id)->update(['status_paid'=>'0']);
            //$hasil=$this->update_summary($id,'review');
        }

        if($review)
        $teks="Silahkan Review Ulang SPL ini";
        return redirect()->back()->with(['success' => $teks]);    
    }
    public function messageOvertime($id){
        $tb_overtime=DB::table('tb_overtimes')->where('id',$id)->get();
        $data['kontak']='';
        $data['pesan']='';
        $id_employee='';
        foreach($tb_overtime as $dt){
            $id_overtime=$dt->id_overtime;
            $ot_date=$dt->ot_date;
            $dept=$dt->dept_name;
            if($dt->status_diperintah==0){
                $id_employee=$dt->diperintah;
                $pos='Approval Status Diperintah';
            }else if($dt->disetujui!=null&&$dt->status_disetujui==0){
                $id_employee=$dt->disetujui;
                $pos='Approval Status Disetujui';
            }else if($dt->diketahui!=null&&$dt->status_diketahui==0){
                $id_employee=$dt->diketahui;
                $pos='Approval Status Diketahui';
            }
            $qty=DB::table('tb_overtime_details')->where('id_ot',$id)->count();
            if($id_employee!=''){
                $data['kontak']=DB::table('tb_employee_detail')->where('id_employee',$id_employee)->value('nomor_telepon');
                $data['pesan']="*NOTIFIKASI SPL*\n\nID: *$id_overtime*\nTanggal: *$ot_date*\nDepartemen: *$dept*\nJumlah: *$qty orang*\n\nMenunggu *$pos* oleh Anda.\n\nSegera lakukan pengecekan via EMS via link berikut:\nhttps://ems.summitadyawinsa.co.id/EMS/Admin/Overtime/Approval/$id";
            }
        }
        $data['kontak']='083148870127';
        if($data['kontak']!=''){
            \App\Http\Controllers\WuzapiController::sendInternalMessage($data['kontak'], $data['pesan']);
            return 'Success';
        }else{
            return 'Failed';
        }
    }
    function signAfter(Request $data){
        //return $data->id_employee;
        $respon='Success';
        $start_act=$this->formatDateTimeForDatabase($data->start_act);
        $finish_act=$this->formatDateTimeForDatabase($data->finish_act);
        if($start_act===null||$finish_act===null){
            return response('Format tanggal overtime tidak valid',422);
        }
        $id_employee=$data->id_employee;
        $tb_ot_detail=DB::table('tb_overtime_details')->where('id',$data->id_after)->get();
        foreach($tb_ot_detail as $row){
            $mp_lama=$row->id_employee;
            $id_ot=$row->id_ot;
        }

        if($data->sign_after=='3'){
            if($data->id_employee=='')$respon='Employee pengganti belum dipilih';
            $change=DB::table('tb_overtime_details')->where('id',$data->id_after)->update([
                'id_employee_plan'=>$mp_lama,
                'id_employee'=>$data->id_employee_baru
            ]);
            $id_employee=$data->id_employee_baru;
        }
        if($data->is_over==1){
            $change2=DB::table('tb_overtimes')->where('id',$id_ot)->update([
                'is_over'=>'1'
            ]);
        }
        //$tb_salary=tb_salary::where('id_employee',$data->id_employee)->get();
        $tb_salary=DB::table('tb_salaries')->leftjoin('tb_employees','tb_employees.id','=','tb_salaries.id_employee')
        ->where('tb_salaries.id_employee',$id_employee)
        ->orderby('tb_salaries.status','desc')
        ->get(['tb_salaries.*','tb_employees.position_id']);
        foreach ($tb_salary as $dt) {
            $slpj=round($dt->slpj);
            $ammount=$slpj*$data->hours_convertion;
            $konversi=$data->hours_convertion;
            if($dt->position_id=='19'||$dt->position_id=='23'){
                $konversi=$data->hours_plan;
                $ammount=$slpj*$data->hours_plan;
            }
        }
        $tb_driver=DB::table('tb_employees')->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')->leftjoin('tb_utilities','tb_utilities.atribut','=','tb_positions.position_name')->where('tb_employees.id',$id_employee)->where('tb_employees.status','1')->get(['tb_employees.*','tb_utilities.status']);
        foreach($tb_driver as $dt_driver){
            if($dt_driver->status>0&&$dt_driver->position_id=='18'&&$dt_driver->dept_id=='10')$ammount=$dt_driver->status;
            //if($dt_driver->status>0)$ammount=$dt_driver->status;
        }
        if(isset($slpj)){
            $update=DB::table('tb_overtime_details')->where('id',$data->id_after)->update([
                'start_act'=>$start_act,
                'finish_act'=>$finish_act,
                'ot_category'=>$data->ot_category,
                'minutes_break'=>$data->minutes_break,
                'hours_act'=>$data->hours_plan,
                'hours_convertion'=>$konversi,
                'SLPJ'=>$slpj,
                'ammount'=>$ammount,
                'sign_after'=>$data->sign_after,
                'status'=>'4',
                'isOver'=>$data->is_over,
                'reason_over'=>$data->reason_over,
            ]);

        }
        if($data->sign_after=='2'){
            $update=DB::table('tb_overtime_details')->where('id',$data->id_after)->update([
                'status'=>'95'
             ]);
        }

        //Add Capture Finger
        $tb_overtime_detail=DB::table('tb_overtime_details')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_overtime_details.id_employee')
        ->where('tb_overtime_details.id',$data->id_after)
        ->get(['tb_overtime_details.*','tb_employees.badgenumber','tb_employees.NIK']);
        $no=0;
        foreach($tb_overtime_detail as $dt){

            $no++;
            $checkin_act='';
            $checkout_act='';

            $ncdatein=$dt->start_act;
            //Reduce 2 Hour
            $date = date_create($ncdatein);
            date_add($date, date_interval_create_from_date_string('-5 hours'));
            $ncdateindown= date_format($date, 'Y-m-d H:i:s');
            //Increas 2 Hour
            $date = date_create($ncdatein);
            date_add($date, date_interval_create_from_date_string('2 hours'));
            $ncdateinup= date_format($date, 'Y-m-d H:i:s');
            //echo $ncdatein.' ';

            $ncdateout=$dt->finish_act;
            //Reduce 3 Hour
            $date = date_create($ncdateout);
            date_add($date, date_interval_create_from_date_string('-3 hours'));
            $ncdateoutdown= date_format($date, 'Y-m-d H:i:s');
            //Increas 3 Hour
            $date = date_create($ncdateout);
            date_add($date, date_interval_create_from_date_string('3 hours'));
            $ncdateoutup= date_format($date, 'Y-m-d H:i:s');
            //echo $ncdateout.' ';

            $lenbadge=strlen($dt->badgenumber);
            $nullbadge=9-$lenbadge;
            $j='';
            for($i=1;$i<=$nullbadge;$i++){
                $j.='0';
            }
            $badge=$j.$dt->badgenumber;

            $qry5=DB::table('tb_iclock')
            ->where('badgenumber',$badge)
            ->where('checktime','>=',$ncdateindown)
            ->where('checktime','<=',$ncdateinup)
            ->orderby('checktime','asc')
            ->limit(1)
            ->get(['checktime']);
            foreach($qry5 as $dt5){
                $checkin_act = $dt5->checktime;
                $in_finger=1;
            }
            $qry6=DB::table('tb_iclock')
            ->where('badgenumber',$badge)
            ->where('checktime','>=',$ncdateoutdown)
            ->where('checktime','<=',$ncdateoutup)
            ->orderby('checktime','asc')
            ->limit(1)
            ->get(['checktime']);
            foreach($qry6 as $dt6){
                $checkout_act = $dt6->checktime;
                $out_finger=1;
            }

            //Absen Manual Start
            if($checkin_act==''){
                $qry7=DB::table('tb_checktimes')->where('NIK',$dt->NIK)->where('checktime','>=',$ncdateindown)->where('checktime','<=',$ncdateinup)->orderby('checktime','asc')->limit(1)->get();
                foreach($qry7 as $dt7){
                    $checkin_act=$dt7->checktime;
                    $in_finger=0;
                }
            }
            if($checkout_act==''){
                $qry7=DB::table('tb_checktimes')->where('NIK',$dt->NIK)->where('checktime','>=',$ncdateoutdown)->where('checktime','<=',$ncdateoutup)->orderby('checktime','asc')->limit(1)->get();
                foreach($qry7 as $dt7){
                    $checkout_act=$dt7->checktime;
                    $out_finger=0;
                }
            }
            //Absen Manual End
            if($checkin_act!=''){
                $update=DB::table('tb_overtime_details')->where('id',$dt->id)->update([
                    'checkin'=>$checkin_act,
                    'in_finger'=>$in_finger
                ]);
            }
            if($checkout_act!=''){
                $update=DB::table('tb_overtime_details')->where('id',$dt->id)->update([
                    'checkout'=>$checkout_act,
                    'out_finger'=>$out_finger
                ]);
            }

            \Log::info($no.' '.$checkin_act.' - '.$checkout_act);

        }

        
        return $respon;
    }
    function signAfterAll($id){
        $update=DB::table('tb_overtime_details')->where('id_ot',$id)->where('status','<=','4')->update([
            'sign_after'=>'1',
            'status'=>'4'
        ]);

        //Add capture finger
        $tb_overtime_detail=DB::table('tb_overtime_details')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_overtime_details.id_employee')
        ->where('tb_overtime_details.id_ot',$id)
        ->get(['tb_overtime_details.*','tb_employees.badgenumber','tb_employees.NIK']);
        $no=0;
        foreach($tb_overtime_detail as $dt){

            $no++;
            $checkin_act='';
            $checkout_act='';

            $ncdatein=$dt->start_act;
            //Reduce 2 Hour
            $date = date_create($ncdatein);
            date_add($date, date_interval_create_from_date_string('-3 hours'));
            $ncdateindown= date_format($date, 'Y-m-d H:i:s');
            //Increas 2 Hour
            $date = date_create($ncdatein);
            date_add($date, date_interval_create_from_date_string('1 hours'));
            $ncdateinup= date_format($date, 'Y-m-d H:i:s');
            //echo $ncdatein.' ';

            $ncdateout=$dt->finish_act;
            //Reduce 3 Hour
            $date = date_create($ncdateout);
            date_add($date, date_interval_create_from_date_string('-1 hours'));
            $ncdateoutdown= date_format($date, 'Y-m-d H:i:s');
            //Increas 3 Hour
            $date = date_create($ncdateout);
            date_add($date, date_interval_create_from_date_string('3 hours'));
            $ncdateoutup= date_format($date, 'Y-m-d H:i:s');
            //echo $ncdateout.' ';

            $lenbadge=strlen($dt->badgenumber);
            $nullbadge=9-$lenbadge;
            $j='';
            for($i=1;$i<=$nullbadge;$i++){
                $j.='0';
            }
            $badge=$j.$dt->badgenumber;

            $qry5=DB::table('tb_iclock')
            ->where('badgenumber',$badge)
            ->where('checktime','>=',$ncdateindown)
            ->where('checktime','<=',$ncdateinup)
            ->orderby('checktime','asc')
            ->limit(1)
            ->get(['checktime']);
            foreach($qry5 as $dt5){
                $checkin_act = $dt5->checktime;
                $in_finger=1;
            }
            $qry6=DB::table('tb_iclock')
            ->where('badgenumber',$badge)
            ->where('checktime','>=',$ncdateoutdown)
            ->where('checktime','<=',$ncdateoutup)
            ->orderby('checktime','asc')
            ->limit(1)
            ->get(['checktime']);
            foreach($qry6 as $dt6){
                $checkout_act = $dt6->checktime;
                $out_finger=1;
            }

            //Absen Manual Start
            if($checkin_act==''){
                $qry7=DB::table('tb_checktimes')->where('NIK',$dt->NIK)->where('checktime','>=',$ncdateindown)->where('checktime','<=',$ncdateinup)->orderby('checktime','asc')->limit(1)->get();
                foreach($qry7 as $dt7){
                    $checkin_act=$dt7->checktime;
                    $in_finger=0;
                }
            }
            if($checkout_act==''){
                $qry7=DB::table('tb_checktimes')->where('NIK',$dt->NIK)->where('checktime','>=',$ncdateoutdown)->where('checktime','<=',$ncdateoutup)->orderby('checktime','asc')->limit(1)->get();
                foreach($qry7 as $dt7){
                    $checkout_act=$dt7->checktime;
                    $out_finger=0;
                }
            }
            //Absen Manual End
            if($checkin_act!=''){
                $update=DB::table('tb_overtime_details')->where('id',$dt->id)->update([
                    'checkin'=>$checkin_act,
                    'in_finger'=>$in_finger
                ]);
            }
            if($checkout_act!=''){
                $update=DB::table('tb_overtime_details')->where('id',$dt->id)->update([
                    'checkout'=>$checkout_act,
                    'out_finger'=>$out_finger
                ]);
            }
            \Log::info($no.' ('.$ncdateindown.' - '.$ncdateinup.') ('.$ncdateoutdown.' - '.$ncdateoutup);
            \Log::info($no.' '.$checkin_act.' - '.$checkout_act);

        }

        return redirect()->back();
    }
    function verificationOT($periode){
        //$this->autoCancel();
        date_default_timezone_set("Asia/Jakarta");
        $kalendar=CAL_GREGORIAN;
        if($periode==0)$periode=date('Y-m');
        $thn=date('Y',strtotime($periode.'-01'));
        $bln=date('m',strtotime($periode.'-01'));
        $hariakhir=cal_days_in_month($kalendar,$bln,$thn);
        $periode_awal=date('Y-m-d',strtotime($thn.'-'.$bln.'-01'));
        $periode_akhir=date('Y-m-d',strtotime($thn.'-'.$bln.'-'.$hariakhir));

        $tb_overtime_plan=DB::table('tb_overtimes')
        ->leftjoin('tb_employees as tb1','tb1.id','=','tb_overtimes.diperintah')
        ->leftjoin('tb_employees as tb2','tb2.id','=','tb_overtimes.disetujui')
        ->leftjoin('tb_employees as tb3','tb3.id','=','tb_overtimes.diketahui')
        ->select('tb_overtimes.*','tb1.employee_name as nm1','tb2.employee_name as nm2','tb3.employee_name as nm3')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_overtimes.dept_id')
        ->where('status_paid','0')
        ->where('tb_overtimes.status','1')
        ->where('tb_overtimes.isDelete','0')
		->where('tb_departments.isTrial','1')
        ->get(['tb_overtimes.*','tb_departments.dept_name']);
        $tb_overtime_actual=DB::table('tb_overtimes')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_overtimes.dept_id')
        ->where([['status_approve','1'],['status_paid','0']])
        ->where('tb_overtimes.status','1')
		->where('tb_departments.isTrial','1')
        //->where('tb_overtimes.isDelete','0')
        ->where('tb_overtimes.isDelete','9')
        ->get(['tb_overtimes.*','tb_departments.dept_name']);
        $tb_overtime=DB::table('tb_overtimes')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_overtimes.dept_id')
        ->where('status_paid','1')
        ->where('tb_overtimes.status','1')
        ->where('tb_overtimes.isDelete','0')
		->where('tb_departments.isTrial','1')
        ->where('ot_date','>=',$periode_awal)
        ->where('ot_date','<=',$periode_akhir)
        ->get(['tb_overtimes.*','tb_departments.dept_name']);


        $jmlot_plan=$tb_overtime_plan->count();
        $ot_counts=DB::table('tb_overtimes')
        ->join('tb_departments','tb_departments.id','=','tb_overtimes.dept_id')
        ->where('tb_departments.isTrial','1')
        ->selectRaw("SUM(CASE WHEN tb_overtimes.status_approve='1' AND tb_overtimes.status_paid='0' AND tb_overtimes.isDelete='0' THEN 1 ELSE 0 END) as actual_count")
        ->value('actual_count');
        $jmlot_actual=(int) $ot_counts;

        $slatus_lock=DB::table('tb_utilities')
        ->where('atribut','limit_approval_ot')
        ->value('status');
        return view('page/admin/m_overtime/createdspl',['tb_overtime_plan'=>$tb_overtime_plan,'tb_overtime_actual'=>$tb_overtime_actual,'tb_overtime'=>$tb_overtime,'jmlot_plan'=>$jmlot_plan,'jmlot_actual'=>$jmlot_actual,'periode'=>$periode,'status_lock'=>$slatus_lock,'cabang'=>'Verifications','menu'=>'overtime','submenu'=>'verification']);
    }    
    function verificationOT2($periode){
        date_default_timezone_set("Asia/Jakarta");
        $kalendar=CAL_GREGORIAN;
        if($periode==0)$periode=date('Y-m');
        $thn=date('Y',strtotime($periode.'-01'));
        $bln=date('m',strtotime($periode.'-01'));
        $hariakhir=cal_days_in_month($kalendar,$bln,$thn);
        $periode_awal=date('Y-m-d',strtotime($thn.'-'.$bln.'-01'));
        $periode_akhir=date('Y-m-d',strtotime($thn.'-'.$bln.'-'.$hariakhir));

        $tb_overtime_plan=DB::table('tb_overtimes')
        ->leftjoin('tb_employees as tb1','tb1.id','=','tb_overtimes.diperintah')
        ->leftjoin('tb_employees as tb2','tb2.id','=','tb_overtimes.disetujui')
        ->leftjoin('tb_employees as tb3','tb3.id','=','tb_overtimes.diketahui')
        ->select('tb_overtimes.*','tb1.employee_name as nm1','tb2.employee_name as nm2','tb3.employee_name as nm3')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_overtimes.dept_id')
        ->where('status_paid','0')
        ->where('tb_overtimes.status','1')
        //->where('tb_overtimes.isDelete','0')
        ->where('tb_overtimes.isDelete','9')
		->where('tb_departments.isTrial','1')
        ->get(['tb_overtimes.*','tb_departments.dept_name']);
        $tb_overtime_actual=DB::table('tb_overtimes')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_overtimes.dept_id')
        ->where([['status_approve','1'],['status_paid','0']])
        ->where('tb_overtimes.status','1')
		->where('tb_departments.isTrial','1')
        ->where('tb_overtimes.isDelete','0')
        ->get(['tb_overtimes.*','tb_departments.dept_name']);
        $tb_overtime=DB::table('tb_overtimes')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_overtimes.dept_id')
        ->where('status_paid','1')
        ->where('tb_overtimes.status','1')
        ->where('tb_overtimes.isDelete','0')
		->where('tb_departments.isTrial','1')
        ->where('ot_date','>=',$periode_awal)
        ->where('ot_date','<=',$periode_akhir)
        ->get(['tb_overtimes.*','tb_departments.dept_name']);


        $jmlot_plan=DB::table('tb_overtimes')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_overtimes.dept_id')
        ->where('status_paid','0')
        ->where('tb_overtimes.status','1')
		->where('tb_departments.isTrial','1')
        ->where('tb_overtimes.isDelete','0')
        //->where('tb_overtimes.isDelete','9')
        ->count();
        $jmlot_actual=DB::table('tb_overtimes')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_overtimes.dept_id')
        ->where([['status_approve','1'],['status_paid','0']])
		->where('tb_departments.isTrial','1')
        ->where('tb_overtimes.isDelete','0')
        ->count();

        $status_lock_ot=DB::table('tb_utilities')->where('atribut','limit_approval_ot')->get();
        foreach($status_lock_ot as $dt){$slatus_lock=$dt->status;}
        return view('page/admin/m_overtime/createdspl',['tb_overtime_plan'=>$tb_overtime_plan,'tb_overtime_actual'=>$tb_overtime_actual,'tb_overtime'=>$tb_overtime,'jmlot_plan'=>$jmlot_plan,'jmlot_actual'=>$jmlot_actual,'periode'=>$periode,'status_lock'=>$slatus_lock,'cabang'=>'Verifications','menu'=>'overtime','submenu'=>'verification2']);
    }    
    function verificationdetailOT($id){
        //Update Status OT
        $akses=1;
        if($akses==1){
            $tb_overtime_detail=DB::table('tb_overtime_details')
            ->leftjoin('tb_overtimes','tb_overtimes.id','=','tb_overtime_details.id_ot')
            ->leftjoin('tb_employees','tb_employees.id','=','tb_overtime_details.id_employee')
            ->join('tb_employee_shifts', function ($join) {
                $join->on('tb_employee_shifts.id_employee', '=', 'tb_employees.id')
                    ->where('tb_employee_shifts.status',1);
            })
            ->leftjoin('tb_group_shifts','tb_group_shifts.id','=','tb_employee_shifts.id_shift')
            ->where('tb_overtime_details.id_ot',$id)
            ->get(['tb_overtime_details.*','tb_employees.PIN','tb_employees.NIK','tb_employees.employee_name','tb_overtimes.status_diketahui','tb_overtimes.status_dicatat','tb_group_shifts.hari_kerja','tb_employees.badgenumber']);

            foreach($tb_overtime_detail as $dt){
                if($dt->ot_category==2){
                    if($dt->checkin<=$dt->start_act&&$dt->checkout>=$dt->finish_act&&$dt->checkin!=null&&$dt->checkout!=null){
                        $update=DB::table('tb_overtime_details')->where('id',$dt->id)->where('status','<','6')->update(['status'=>'6']);
                    }
                }else{
                    if(($dt->checkin<=$dt->start_act&&$dt->checkout>=$dt->finish_act&&$dt->checkin!=null&&$dt->checkout!=null)||($dt->checkin==null&&$dt->checkout>=$dt->finish_act&&$dt->checkout!=null)||($dt->checkin<=$dt->start_act&&$dt->checkout==null&&$dt->checkin!=null)){
                        $update=DB::table('tb_overtime_details')->where('id',$dt->id)->where('status','<','6')->update(['status'=>'6']);
                    }
                }
            }
        }
        //End Update Status OT
        $tb_overtime_detail=DB::table('tb_overtime_details')
        ->leftjoin('tb_overtimes','tb_overtimes.id','=','tb_overtime_details.id_ot')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_overtime_details.id_employee')
        ->join('tb_employee_shifts', function ($join) {
            $join->on('tb_employee_shifts.id_employee', '=', 'tb_employees.id')
                 ->where('tb_employee_shifts.status',1);
        })
        ->leftjoin('tb_group_shifts','tb_group_shifts.id','=','tb_employee_shifts.id_shift')
        ->where('tb_overtime_details.id_ot',$id)
        ->get(['tb_overtime_details.*','tb_employees.PIN','tb_employees.NIK','tb_employees.employee_name','tb_overtimes.status_diketahui','tb_overtimes.status_dicatat','tb_group_shifts.hari_kerja','tb_employees.badgenumber']);
        return view('page/admin/m_overtime/verificationspl',['tb_overtime_detail'=>$tb_overtime_detail,'idot'=>$id,'menu'=>'overtime','submenu'=>'verification2']);
    }
    function verificationUpdateFinger(Request $data){
        $id=$data->id;
        $tb_overtime_detail=DB::table('tb_overtime_details')
        ->leftjoin('tb_overtimes','tb_overtimes.id','=','tb_overtime_details.id_ot')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_overtime_details.id_employee')
        ->join('tb_employee_shifts', function ($join) {
            $join->on('tb_employee_shifts.id_employee', '=', 'tb_employees.id')
                 ->where('tb_employee_shifts.status',1);
        })
        ->leftjoin('tb_group_shifts','tb_group_shifts.id','=','tb_employee_shifts.id_shift')
        ->where('tb_overtime_details.id_ot',$id)
        ->whereNull('tb_overtime_details.in_finger')
        ->whereNull('tb_overtime_details.out_finger')
        //->where('badgenumber','6083')
        ->limit(40)
        ->get(['tb_overtime_details.*','tb_employees.PIN','tb_employees.NIK','tb_employees.employee_name','tb_overtimes.status_diketahui','tb_overtimes.status_dicatat','tb_group_shifts.hari_kerja','tb_employees.badgenumber']);
        $no=0;
        foreach($tb_overtime_detail as $dt){

            $no++;
            $checkin_act='';
            $checkout_act='';

            $ncdatein=$dt->start_act;
            //Reduce 2 Hour
            $date = date_create($ncdatein);
            date_add($date, date_interval_create_from_date_string('-5 hours'));
            $ncdateindown= date_format($date, 'Y-m-d H:i:s');
            //Increas 2 Hour
            $date = date_create($ncdatein);
            date_add($date, date_interval_create_from_date_string('2 hours'));
            $ncdateinup= date_format($date, 'Y-m-d H:i:s');
            //echo $ncdatein.' ';

            $ncdateout=$dt->finish_act;
            //Reduce 3 Hour
            $date = date_create($ncdateout);
            date_add($date, date_interval_create_from_date_string('-3 hours'));
            $ncdateoutdown= date_format($date, 'Y-m-d H:i:s');
            //Increas 3 Hour
            $date = date_create($ncdateout);
            date_add($date, date_interval_create_from_date_string('3 hours'));
            $ncdateoutup= date_format($date, 'Y-m-d H:i:s');
            //echo $ncdateout.' ';

            $lenbadge=strlen($dt->badgenumber);
            $nullbadge=9-$lenbadge;
            $j='';
            for($i=1;$i<=$nullbadge;$i++){
                $j.='0';
            }
            $badge=$j.$dt->badgenumber;


            // $qry5=DB::connection('fingerPrint')->table('checkinout')
            $qry5=DB::table('tb_iclock')
            // ->leftjoin('userinfo','userinfo.userid','=','checkinout.userid')
            ->where('badgenumber',$badge)
            ->where('checktime','>=',$ncdateindown)
            ->where('checktime','<=',$ncdateinup)
            ->orderby('checktime','asc')
            ->limit(1)
            ->get(['checktime']);
            foreach($qry5 as $dt5){
                $checkin_act = $dt5->checktime;
                $in_finger=1;
            }
            // $qry5=DB::connection('fingerPrint')->table('checkinout')
            $qry5=DB::table('tb_iclock')
            // ->leftjoin('userinfo','userinfo.userid','=','checkinout.userid')
            ->where('badgenumber',$badge)
            ->where('checktime','>=',$ncdateoutdown)
            ->where('checktime','<=',$ncdateoutup)
            ->orderby('checktime','asc')
            ->limit(1)
            ->get(['checktime']);
            foreach($qry5 as $dt5){
                $checkout_act = $dt5->checktime;
                $out_finger=1;
            }

            //Absen Manual Start
            if($checkin_act==''){
                $qry7=DB::table('tb_checktimes')->where('NIK',$dt->NIK)->where('checktime','>=',$ncdateindown)->where('checktime','<=',$ncdateinup)->orderby('checktime','asc')->limit(1)->get();
                foreach($qry7 as $dt7){
                    $checkin_act=$dt7->checktime;
                    $in_finger=0;
                }
            }
            if($checkout_act==''){
                $qry7=DB::table('tb_checktimes')->where('NIK',$dt->NIK)->where('checktime','>=',$ncdateoutdown)->where('checktime','<=',$ncdateoutup)->orderby('checktime','asc')->limit(1)->get();
                foreach($qry7 as $dt7){
                    $checkout_act=$dt7->checktime;
                    $out_finger=0;
                }
            }
            //Absen Manual End
            if($checkin_act!=''){
                $update=DB::table('tb_overtime_details')->where('id',$dt->id)->update([
                    'checkin'=>$checkin_act,
                    'in_finger'=>$in_finger
                ]);
            }
            if($checkout_act!=''){
                $update=DB::table('tb_overtime_details')->where('id',$dt->id)->update([
                    'checkout'=>$checkout_act,
                    'out_finger'=>$out_finger
                ]);
            }

            \Log::info($no.' '.$checkin_act.' - '.$checkout_act);

        }
    }
    function verificationUpdate(Request $data){
        //$tb_salary=tb_salary::where('id_employee',$data->id_employee)->get();
        $start_act=$this->formatDateTimeForDatabase($data->start_act);
        $finish_act=$this->formatDateTimeForDatabase($data->finish_act);
        if($start_act===null||$finish_act===null){
            return response('Format tanggal overtime tidak valid',422);
        }
        $tb_salary=DB::table('tb_salaries')->leftjoin('tb_employees','tb_employees.id','=','tb_salaries.id_employee')->where('tb_salaries.id_employee',$data->id_employee)->get(['tb_salaries.*','tb_employees.position_id','tb_employees.dept_id']);
        foreach ($tb_salary as $dt) {
            $slpj=round($dt->slpj);
            $ammount=$slpj*$data->hours_convertion;
            $konversi=$data->hours_convertion;
            $id_posisi=$dt->position_id;
            $dept_id=$dt->dept_id;
            if($dt->position_id=='19'||$dt->position_id=='23'){
                $konversi=$data->hours_plan;
                $ammount=$slpj*$data->hours_plan;
            }
        }
        $tb_driver=DB::table('tb_employees')->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')->leftjoin('tb_utilities','tb_utilities.atribut','=','tb_positions.position_name')->where('tb_employees.id',$data->id_employee)->where('tb_employees.status','1')->get(['tb_utilities.status']);
        foreach($tb_driver as $dt_driver){
            if($dt_driver->status>0&&$id_posisi=='18'&&$dept_id=='10')$ammount=$dt_driver->status;
            //if($dt_driver->status>0)$ammount=$dt_driver->status;
        }
        if($data->sign_after==2)$status=96;
        else $status=6;
        if(isset($slpj)){
            $update=DB::table('tb_overtime_details')->where('id',$data->id_after)->update([
                'start_act'=>$start_act,
                'finish_act'=>$finish_act,
                'ot_category'=>$data->ot_category,
                'minutes_break'=>$data->minutes_break,
                'hours_act'=>$data->hours_plan,
                'hours_convertion'=>$konversi,
                'SLPJ'=>$slpj,
                'ammount'=>$ammount,
                'sign_after'=>$data->sign_after,
                'status'=>$status
            ]);
        }
        if($update){
            $tb_overtime=DB::table('tb_overtimes')
            ->where('tb_overtimes.id',$data->id_after)
            ->get();
            foreach($tb_overtime as $dt){
                $tb_salary=DB::table('tb_salaries')->where('id_employee',$dt->id_employee)->get();
                foreach ($tb_salary as $dt2) {
                    $meal=$dt2->meal;
                }
                if(($dt->ot_category=='1'&&$dt->hours_act>=4)||($dt->ot_category=='2'&&$dt->hours_act>=8)){
                    $set_meal=DB::table('tb_meals')->insert([
                        'id_employee'=>$dt->id_employee,
                        'date_on'=>$dt->date_on,
                        'category'=>'meal_ot',
                        'ref_id'=>$dt->id,
                        'meal'=>$meal
                    ]);
                }
        
            }
        }
    }
    function previewSPL($id){       

        $tb_overtime=DB::table('tb_overtimes')->leftjoin('tb_departments','tb_departments.id','=','tb_overtimes.dept_id')->where('tb_overtimes.id',$id)->get(['tb_overtimes.*','tb_departments.dept_name']);
        $n_detail=DB::table('tb_overtime_details')->where('id_ot',$id)->count();
        if($n_detail==0){
            echo "<script>alert('Data masih kosong');window.close();</script>";
        }
        #region SAI Overtime
        $tb_overtime_detail = '';
        $tb_overtime_detail_count=DB::table('tb_overtime_details')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_overtime_details.id_employee')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->select('tb_overtime_details.*','tb_employees.NIK','tb_employees.employee_name','tb_departments.dept_code')
        ->where('tb_employees.position_id','<>',29)
        ->where('tb_overtime_details.id_ot',$id)
        ->get()->count();

        if($tb_overtime_detail_count > 0 ){
        $tb_overtime_detail=DB::table('tb_overtime_details')
            ->leftjoin('tb_employees','tb_employees.id','=','tb_overtime_details.id_employee')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
            ->leftjoin('tb_machine','tb_machine.id','=','tb_overtime_details.id_line_machine')
        ->leftjoin('tb_line','tb_line.id','=','tb_machine.IdLine')
            ->select('tb_overtime_details.*','tb_employees.NIK','tb_employees.employee_name','tb_departments.dept_code',DB::raw("concat(tb_line.line, ' - ',tb_machine.DetailLine) as line"))
            ->where('tb_employees.position_id','<>',29)
            ->where('tb_overtime_details.id_ot',$id)
            ->get();
        }
        #endregion

        #region DPK Overtime
        $dpk_count = DB::table('tb_overtime_details')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_overtime_details.id_employee')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->select('tb_overtime_details.*','tb_employees.NIK','tb_employees.employee_name','tb_departments.dept_code')
        ->where('tb_employees.position_id','=',29)
        ->where('tb_overtime_details.id_ot',$id)
        ->get()->count();
        $tb_emp_dpk = '';
        if($dpk_count > 0){
            $tb_emp_dpk = DB::table('tb_overtime_details')
                ->leftjoin('tb_employees','tb_employees.id','=','tb_overtime_details.id_employee')
                ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
                ->leftjoin('tb_machine','tb_machine.id','=','tb_overtime_details.id_line_machine')
        ->leftjoin('tb_line','tb_line.id','=','tb_machine.IdLine')
                ->select('tb_overtime_details.*','tb_employees.NIK','tb_employees.employee_name','tb_departments.dept_code',DB::raw("concat(tb_line.line, ' - ',tb_machine.DetailLine) as line"))
                ->where('tb_employees.position_id','=',29)
                ->where('tb_overtime_details.id_ot',$id)
                ->get();
        }
        #endregion
        $disetujui='';
        $disetujui_j='';
        $diketahui='';
        $diketahui_j='';

        foreach($tb_overtime as $dt){
            $id_ot=$dt->id_overtime;

            //$qry=DB::table('tb_employees')->leftjoin('tb_positions','tb_positions.id','tb_employees.position_id')->select('tb_employees.*','tb_positions.position_name')->where('status','1')->where('tb_employees.id',$dt->diperintah)->get();
            $qry=DB::table('tb_employees')->leftjoin('tb_positions','tb_positions.id','tb_employees.position_id')->select('tb_employees.*','tb_positions.position_name')->where('tb_employees.id',$dt->diperintah)->get();
            foreach($qry as $dt2){
                $diperintah=$dt2->employee_name;
                $diperintah_j=$dt2->position_name;
            }
            $qry=DB::table('tb_employees')->leftjoin('tb_positions','tb_positions.id','tb_employees.position_id')->select('tb_employees.*','tb_positions.position_name')->where('status','1')->where('tb_employees.id',$dt->disetujui)->get();
            foreach($qry as $dt2){
                $disetujui=$dt2->employee_name;
                $disetujui_j=$dt2->position_name;
            }
            $qry=DB::table('tb_employees')->leftjoin('tb_positions','tb_positions.id','tb_employees.position_id')->select('tb_employees.*','tb_positions.position_name')->where('status','1')->where('tb_employees.id',$dt->diketahui)->get();
            foreach($qry as $dt2){
                $diketahui=$dt2->employee_name;
                $diketahui_j=$dt2->position_name;
            }
            $qry=DB::table('tb_employees')->leftjoin('tb_positions','tb_positions.id','tb_employees.position_id')->select('tb_employees.*','tb_positions.position_name')->where('status','1')->where('tb_employees.id',$dt->dicatat)->get();
            foreach($qry as $dt2){
                $dicatat=$dt2->employee_name;
                $dicatat_j=$dt2->position_name;
            }
            $qry=DB::table('tb_employees')->leftjoin('tb_positions','tb_positions.id','tb_employees.position_id')->select('tb_employees.*','tb_positions.position_name')->where('status','1')->where('tb_employees.id',$dt->approve)->get();
            foreach($qry as $dt2){
                $approve=$dt2->employee_name;
                $approve_j=$dt2->position_name;
            }
            $qry=DB::table('tb_employees')->leftjoin('tb_positions','tb_positions.id','tb_employees.position_id')->select('tb_employees.*','tb_positions.position_name')->where('status','1')->where('tb_employees.id',$dt->paid)->get();
            foreach($qry as $dt2){
                $paid=$dt2->employee_name;
                $paid_j=$dt2->position_name;
            }
        }
 
        $tb_pesan=DB::table('tb_overtime_notes')->where('id_ot',$id)->orderby('waktu','asc')->get();
 
        $FileName='FORMSPL '.$id_ot.'.PDF';
        $pdf = PDF::loadview('page/admin/m_overtime/printviewspl',[
            'tb_pesan'=>$tb_pesan,
            'tb_overtime'=>$tb_overtime,
            'tb_overtime_detail'=>$tb_overtime_detail,
            'diperintah'=>$diperintah,
            'disetujui'=>$disetujui,
            'diketahui'=>$diketahui,
            'dicatat'=>$dicatat,
            'approve'=>$approve,
            'paid'=>$paid,
            'diperintah_j'=>$diperintah_j,
            'disetujui_j'=>$disetujui_j,
            'diketahui_j'=>$diketahui_j,
            'dicatat_j'=>$dicatat_j,
            'approve_j'=>$approve_j,
            'paid_j'=>$paid_j,
            'tb_overtime_detail_count'=> $tb_overtime_detail_count,
            'dpk_count'=>$dpk_count,
            'tb_emp_dpk'=>$tb_emp_dpk
            ])->setPaper('a4');
        return $pdf->stream($FileName);
    }
    function removeOvertime($id){
        $delete=DB::table('tb_overtimes')->where('id',$id)->update(['isDelete'=>1]);
        if($delete){
            DB::table('tb_overtime_details')->where('id_ot',$id)->update(['isDelete'=>1]);
            return redirect()->back()->with(['success' => 'Delete SPL Berhasil']); 
        }
    }
    function update_lock(){
        $tb_utilities=DB::table('tb_utilities')->where('atribut','limit_approval_ot')->get();
        foreach($tb_utilities as $dt){
            $status_lama=$dt->status;
            if($status_lama==0)$status_baru=1;
            else $status_baru=0;
            $update=DB::table('tb_utilities')->where('atribut','limit_approval_ot')->update(['status'=>$status_baru]);
        }
        return redirect()->back()->with(['success'=>'Success Update Lock']);
    }
    function open_lock_all(){
        $tb_overtime=DB::table('tb_overtimes')->where('autoCancel','1')->get();
        foreach($tb_overtime as $dt){
            if($dt->status_diperintah==3)DB::table('tb_overtimes')->where('id',$dt->id)->update(['status_diperintah'=>'0','autoCancel'=>'0']);
            if($dt->status_disetujui==3)DB::table('tb_overtimes')->where('id',$dt->id)->update(['status_disetujui'=>'0','autoCancel'=>'0']);
            if($dt->status_diketahui==3)DB::table('tb_overtimes')->where('id',$dt->id)->update(['status_diketahui'=>'0','autoCancel'=>'0']);
        }
        $tb_overtime=DB::table('tb_overtimes')->where('autoCancel','0')->get();
        return redirect()->back()->with(['success'=>'Success Reopen SPL']);
    }    
    function open_lock($id){
        $tb_overtime=DB::table('tb_overtimes')->where('id',$id)->get();
        //return $tb_overtime;
        foreach($tb_overtime as $dt){
            if($dt->status_diperintah==3)DB::table('tb_overtimes')->where('id',$id)->update(['status_diperintah'=>'0','autoCancel'=>'0']);
            if($dt->status_disetujui==3)DB::table('tb_overtimes')->where('id',$id)->update(['status_disetujui'=>'0','autoCancel'=>'0']);
            if($dt->status_diketahui==3)DB::table('tb_overtimes')->where('id',$id)->update(['status_diketahui'=>'0','autoCancel'=>'0']);
            if($dt->autoCancel==1)DB::table('tb_overtimes')->where('id',$id)->update(['autoCancel'=>'0']);
        }
        return redirect()->back()->with(['success'=>'Success Update Lock']);
    }

    function addMember_(){
        date_default_timezone_set("Asia/Jakarta");
        $periode=date('md');
        $ip_address=$_SERVER['REMOTE_ADDR'];
        $admin=Auth::user()->name;
        $lastid=tb_overtime::where([['ip_address',$ip_address],['admin',$admin]])->max('id');
        $lastid2=$lastid%100000;
        $noreg=$periode.$lastid2;
        $id_overtime='SAI/OT/'.$noreg;
        $update=tb_overtime::where('id',$lastid)->update(['id_overtime'=>$id_overtime]);

        $tb_overtime=tb_overtime::where('id_overtime',$id_overtime)->get();
        foreach($tb_overtime as $dt){
            $dept_id=$dt->dept_id;
            $id_overtime=$dt->id_overtime;
            $id=$dt->id;
        }
        return redirect('/Admin/Overtime/Draft/'.$id);
        //$tb_reason_ot=tb_reason_ot::all();
        $tb_reason_ot=tb_reason_ot::where('is_active','1')->orderby('id','desc')->orderby('group_reason','asc')->get();
        $tb_job_ot=tb_job_ot::where('dept_id',$dept_id)->get();
        $tb_customer=tb_customer::all();

        $tb_pesan=tb_overtime_note::where('id_ot',$id)->get();
        //$tb_employee=DB::table('tb_employees')->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')->where([['tb_employees.status','1'],['tb_positions.position_index','<','4']])->orderby('employee_name','asc')->get(['tb_employees.*','tb_departments.dept_code']);
        $tb_employee=DB::table('tb_employees')->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')->where([['tb_employees.status','1'],['tb_positions.position_index','<','3']])->orderby('employee_name','asc')->get(['tb_employees.*','tb_departments.dept_code']);
        //return view('page/admin/m_overtime/formspl_detail',['tb_pesan'=>$tb_pesan,'tb_reason_ot'=>$tb_reason_ot,'tb_job_ot'=>$tb_job_ot,'tb_customer'=>$tb_customer,'tb_overtime'=>$tb_overtime,'tb_employee'=>$tb_employee,'dept_id'=>$dept_id,'tb_draft_overtime'=>'','menu'=>'overtime']);
    }
    function deleteOvertime($id){
        $delete=tb_overtime::where('id',$id)->delete();
        if($delete){
            tb_overtime_detail::where('id_ot',$id)->delete();
            return redirect('/Admin/Overtime')->with(['success' => 'Delete SPL Berhasil']); 
        }
    }
    function signBefore(Request $data){
        $periksa=DB::table('tb_overtime_details')->where('id',$data->id_before)->get();
        foreach($periksa as $dt){
            $sign_before=$dt->sign_before;
            if($sign_before==1)$usign_before=0;
            if($sign_before==0)$usign_before=1;
            $id_ot=$dt->id_ot;
        }
        DB::table('tb_overtime_details')->where('id',$data->id_before)->update(['sign_before'=>$usign_before]);
        $tb1=DB::table('tb_overtime_details')->where('id_ot',$id_ot)->where('sign_before','0')->count();
        if($tb1==0){
            $send_whatsapp=$this->messageOvertime($id_ot);
            if($send_whatsapp=='Success'){
                return redirect()->back();
            }else{
                return redirect()->back()->with('error', $send_whatsapp);
            }
        }
    }
    function actualOT(){
        $email=Auth::user()->email;
        $tb_email=tb_email::where('email_address',$email)->get();
        foreach($tb_email as $dt){
            $id_employee=$dt->id_employee;
        }
        if(isset($id_employee)){
            $tb_overtime_detail=DB::table('tb_overtime_details')->leftjoin('tb_overtimes','tb_overtimes.id','=','tb_overtime_details.id_ot')->leftjoin('tb_employees','tb_employees.id','=','tb_overtime_details.id_employee')->where([['tb_overtime_details.id_employee',$id_employee],['tb_overtimes.status','1']])->orderby('tb_overtimes.id','desc')->get(['tb_overtime_details.*','tb_employees.NIK','tb_employees.employee_name','tb_overtimes.status_diketahui','tb_overtimes.status_dicatat']);
            return view('page/admin/m_overtime/realisationspl',['tb_overtime_detail'=>$tb_overtime_detail,'menu'=>'overtime','submenu'=>'employee']);
        }else return redirect()->back();
    }
    function planApproval($periode){
        //return "A";
        $cek_lock=DB::connection('mysql')->table('tb_utilities')->where('atribut','limit_approval_ot')->where('status','1')->count();
        //if($cek_lock==1){
            $this->autoCancel();
        //}
        date_default_timezone_set("Asia/Jakarta");
        $kalendar=CAL_GREGORIAN;
        if($periode==0){$periode=date('Y-m');$periode_teks=date('F Y');}
        else $periode_teks=date('F Y',strtotime($periode.'-01'));
        $tahun=date('Y',strtotime($periode.'-01'));
        $thn=date('Y',strtotime($periode.'-01'));
        $bln=date('m',strtotime($periode.'-01'));
        $hariakhir=cal_days_in_month($kalendar,$bln,$thn);
        $periode_awal=date('Y-m-d',strtotime($thn.'-'.$bln.'-01'));
        $periode_akhir=date('Y-m-d',strtotime($thn.'-'.$bln.'-'.$hariakhir));

        $email=Auth::user()->email;
        $tb_email=tb_email::where('email_address',$email)->get();
        $id_employee='';
        foreach($tb_email as $dt){
            $id_employee=$dt->id_employee;
        }


        $tb_overtime_plan=DB::table('tb_overtimes')
        ->leftjoin('tb_employees as tb1','tb1.id','=','tb_overtimes.diperintah')
        ->leftjoin('tb_employees as tb2','tb2.id','=','tb_overtimes.disetujui')
        ->leftjoin('tb_employees as tb3','tb3.id','=','tb_overtimes.diketahui')
        ->select('tb_overtimes.*','tb1.employee_name as nm1','tb2.employee_name as nm2','tb3.employee_name as nm3')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_overtimes.dept_id')
        ->where([['tb_overtimes.diperintah',$id_employee],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<=','3'],['tb_overtimes.status_disetujui','<=','3'],['tb_overtimes.status_diketahui','<=','3'],['tb_overtimes.status_dicatat','0'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.disetujui',$id_employee],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<=','3'],['tb_overtimes.status_disetujui','<=','3'],['tb_overtimes.status_diketahui','<=','3'],['tb_overtimes.status_dicatat','0'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.diketahui',$id_employee],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<=','3'],['tb_overtimes.status_disetujui','<=','3'],['tb_overtimes.status_diketahui','<=','3'],['tb_overtimes.status_dicatat','0'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.dicatat',$id_employee],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<=','3'],['tb_overtimes.status_disetujui','<=','3'],['tb_overtimes.status_diketahui','<=','3'],['tb_overtimes.status_dicatat','0'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.approve',$id_employee],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<=','3'],['tb_overtimes.status_disetujui','<=','3'],['tb_overtimes.status_diketahui','<=','3'],['tb_overtimes.status_dicatat','0'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.paid',$id_employee],['tb_overtimes.status','1'],['tb_overtimes.status_approve','0'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->get(['tb_overtimes.*','tb_departments.dept_name']);
        //->count();
        //return $tb_overtime_plan;

        $tb_overtime_actual=DB::table('tb_overtimes')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_overtimes.dept_id')
        ->where([['tb_overtimes.diperintah',$id_employee],['status','1'],['tb_overtimes.status_dicatat','1'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.disetujui',$id_employee],['status','1'],['tb_overtimes.status_dicatat','1'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.diketahui',$id_employee],['status','1'],['tb_overtimes.status_dicatat','1'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.dicatat',$id_employee],['status','1'],['tb_overtimes.status_dicatat','1'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.approve',$id_employee],['status','1'],['tb_overtimes.status_dicatat','1'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.paid',$id_employee],['status','1'],['tb_overtimes.status_dicatat','1'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->get(['tb_overtimes.*','tb_departments.dept_name']);

        $tb_overtime=DB::table('tb_overtimes')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_overtimes.dept_id')
        ->where([['tb_overtimes.diperintah',$id_employee],['status','1'],['tb_overtimes.status_paid','1'],['tb_departments.isTrial','1'],['tb_departments.isTrial','1'],['ot_date','>=',$periode_awal],['ot_date','<=',$periode_akhir],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.disetujui',$id_employee],['status','1'],['tb_overtimes.status_paid','1'],['tb_departments.isTrial','1'],['tb_departments.isTrial','1'],['ot_date','>=',$periode_awal],['ot_date','<=',$periode_akhir],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.diketahui',$id_employee],['status','1'],['tb_overtimes.status_paid','1'],['tb_departments.isTrial','1'],['tb_departments.isTrial','1'],['ot_date','>=',$periode_awal],['ot_date','<=',$periode_akhir],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.dicatat',$id_employee],['status','1'],['tb_overtimes.status_paid','1'],['tb_departments.isTrial','1'],['tb_departments.isTrial','1'],['ot_date','>=',$periode_awal],['ot_date','<=',$periode_akhir],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.approve',$id_employee],['status','1'],['tb_overtimes.status_paid','1'],['tb_departments.isTrial','1'],['tb_departments.isTrial','1'],['ot_date','>=',$periode_awal],['ot_date','<=',$periode_akhir],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.paid',$id_employee],['status','1'],['tb_overtimes.status_paid','1'],['tb_departments.isTrial','1'],['tb_departments.isTrial','1'],['ot_date','>=',$periode_awal],['ot_date','<=',$periode_akhir],['tb_overtimes.isDelete',0]])
        ->orderby('tb_overtimes.ot_date','desc')
        ->get(['tb_overtimes.*','tb_departments.dept_name']);

        $jmlot_plan=DB::table('tb_overtimes')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_overtimes.dept_id')
        ->where([['tb_overtimes.diperintah',$id_employee],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<','3'],['tb_overtimes.status_disetujui','<','3'],['tb_overtimes.status_diketahui','<','3'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.disetujui',$id_employee],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<','3'],['tb_overtimes.status_disetujui','<','3'],['tb_overtimes.status_diketahui','<','3'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.diketahui',$id_employee],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<','3'],['tb_overtimes.status_disetujui','<','3'],['tb_overtimes.status_diketahui','<','3'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.dicatat',$id_employee],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<','3'],['tb_overtimes.status_disetujui','<','3'],['tb_overtimes.status_diketahui','<','3'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.approve',$id_employee],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<','3'],['tb_overtimes.status_disetujui','<','3'],['tb_overtimes.status_diketahui','<','3'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.paid',$id_employee],['tb_overtimes.status','1'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->count();
        $jmlot_actual=DB::table('tb_overtimes')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_overtimes.dept_id')
        ->where([['tb_overtimes.diperintah',$id_employee],['status','1'],['tb_overtimes.status_diperintah','1'],['tb_overtimes.status_dicatat','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.disetujui',$id_employee],['status','1'],['tb_overtimes.status_diperintah','1'],['tb_overtimes.status_dicatat','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.diketahui',$id_employee],['status','1'],['tb_overtimes.status_diperintah','1'],['tb_overtimes.status_dicatat','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.dicatat',$id_employee],['status','1'],['tb_overtimes.status_diperintah','1'],['tb_overtimes.status_dicatat','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.approve',$id_employee],['status','1'],['tb_overtimes.status_diperintah','1'],['tb_overtimes.status_dicatat','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.paid',$id_employee],['status','1'],['tb_overtimes.status_diperintah','1'],['tb_overtimes.status_dicatat','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->count();

        date_default_timezone_set("Asia/Jakarta");
        $kalendar=CAL_GREGORIAN;

        //$thn='2021';
        //$bln='04';

        $thn=date('Y',strtotime($periode.'-01'));
        $bln=date('m',strtotime($periode.'-01'));
        
        $hariakhir=cal_days_in_month($kalendar,$bln,$thn);

        $Tglawal=date('Y-m-d',strtotime($thn.'-'.$bln.'-01'));
        $Tglakhir=date('Y-m-d',strtotime($thn.'-'.$bln.'-'.$hariakhir));

        $tb_sumot_detail=$results = DB::select( DB::raw("SELECT id,dept_code,
        (select sum(ammount) from tb_overtime_details left join tb_overtimes on tb_overtime_details.id_ot=tb_overtimes.id where tb_overtimes.dept_id=tb_departments.id and tb_overtime_details.date_on>='$Tglawal' and tb_overtime_details.date_on<='$Tglakhir' and tb_overtime_details.status<'90')as total_act,
        (select sum(ammount) from tb_overtime_details left join tb_overtimes on tb_overtime_details.id_ot=tb_overtimes.id where tb_overtimes.dept_id=tb_departments.id and tb_overtimes.status_approve='1' and tb_overtime_details.date_on>='$Tglawal' and tb_overtime_details.date_on<='$Tglakhir' and tb_overtime_details.status<'90')as total_act2,
        (select sum(ammount) from tb_overtime_details left join tb_overtimes on tb_overtime_details.id_ot=tb_overtimes.id where tb_overtimes.dept_id=tb_departments.id and tb_overtimes.status_approve='0' and tb_overtime_details.date_on>='$Tglawal' and tb_overtime_details.date_on<='$Tglakhir' and tb_overtime_details.status<'90')as total_act3,
        (select tb_overtime_target.ammount from tb_overtime_target left join tb_overtimes on tb_overtime_target.dept_id=tb_overtimes.dept_id where tb_overtime_target.dept_id=tb_departments.id and tb_overtime_target.periode='$periode' limit 1)as ammount_target
        FROM tb_departments
        where EXISTS(select * from tb_admins where tb_admins.dept_id=tb_departments.id and tb_admins.id_employee='$id_employee') order by total_act desc") );

        //$total_sumot=DB::table('tb_overtime_details')->where('date_on','>=',$Tglawal)->where('date_on','<=',$Tglakhir)->where('status','<','90')->sum('hours_act');
        //$total_ammount=DB::table('tb_overtime_details')->where('date_on','>=',$Tglawal)->where('date_on','<=',$Tglakhir)->where('status','<','90')->sum('ammount');
        // $total_sumot=DB::table('tb_overtime_details')->where('date_on','>=',$Tglawal)->where('date_on','<=',$Tglakhir)->where('status','>=','1')->where('status','<','90')->sum('hours_act');
        // $total_ammount=DB::table('tb_overtime_details')->where('date_on','>=',$Tglawal)->where('date_on','<=',$Tglakhir)->where('status','>=','1')->where('status','<','90')->sum('ammount');
        $total_sumot=DB::table('tb_overtime_details')->where('date_on','>=',$Tglawal)->where('date_on','<=',$Tglakhir)->where('status','=','6')->sum('hours_act');
        $total_ammount=DB::table('tb_overtime_details')->where('date_on','>=',$Tglawal)->where('date_on','<=',$Tglakhir)->where('status','=','6')->sum('ammount');
        //return $tb_sumot_detail;

        $last_check=DB::table('tb_overtime_sales')->where('periode',$periode)->count();
        if($last_check==0){
            $prev=DB::table('tb_overtime_sales')->orderby('periode','desc')->take(1)->get();
            foreach($prev as $dt_prev){
                $add_new=DB::table('tb_overtime_sales')->insert([
                    'periode'=>$periode,
                    'amount_sales'=>$dt_prev->amount_sales,
                    'tahun'=>$tahun,
                ]);
            }
        }

        $qty_sumot=DB::table('tb_overtime_sales')->count();
        $take=12;
        $skip=$qty_sumot-$take;
        if($skip<0)$skip=0;
        
        $last=DB::table('tb_overtime_sales')->where('periode',$periode)->get();
        foreach($last as $dt_last){
            $last_sales=$dt_last->amount_sales;
            $last_target=$last_sales/100;
            $act_persentase=number_format($total_ammount/$last_sales*100,2);
            DB::table('tb_overtime_sales')->where('periode',$periode)->update([
                'amount_sales'=>$last_sales,
                'amount_overtime'=>$total_ammount,
                'target_persentase'=>'1',
                'aktual_persentase'=>$act_persentase,
                'target_overtime'=>$last_target
            ]);
            //return "Masuk";
        }

        $tb_sumot=DB::table('tb_overtime_sales')->orderby('periode','asc')->take($take)->skip($skip)->get();
        $Tglawal='2200-01';
        $Tglakhir='2000-01';
        foreach($tb_sumot as $dt){
            if($Tglawal>$dt->periode)$Tglawal=$dt->periode;
            if($Tglakhir<$dt->periode)$Tglakhir=$dt->periode;
        }
        //return $last_sales;
        $pareto=session('pareto','');
        if($pareto==''||$pareto=='0')
        return view('page/admin/m_overtime/createdspl',['total_sumot'=>$total_sumot,'tb_sumot'=>$tb_sumot,'tb_sumot_detail'=>$tb_sumot_detail,'tb_overtime_plan'=>$tb_overtime_plan,'tb_overtime_actual'=>$tb_overtime_actual,'tb_overtime'=>$tb_overtime,'jmlot_plan'=>$jmlot_plan,'jmlot_actual'=>$jmlot_actual,'Tglawal'=>$Tglawal,'Tglakhir'=>$Tglakhir,'periode'=>$periode,'periode_teks'=>$periode_teks,'salesammount'=>$last_sales,'id_employee'=>$id_employee,'cabang'=>'Plan','menu'=>'overtime','submenu'=>'approval']);
        else
        return view('page/admin/m_overtime/createdspl2',['total_sumot'=>$total_sumot,'tb_sumot'=>$tb_sumot,'tb_sumot_detail'=>$tb_sumot_detail,'tb_overtime_plan'=>$tb_overtime_plan,'tb_overtime_actual'=>$tb_overtime_actual,'tb_overtime'=>$tb_overtime,'jmlot_plan'=>$jmlot_plan,'jmlot_actual'=>$jmlot_actual,'Tglawal'=>$Tglawal,'Tglakhir'=>$Tglakhir,'periode'=>$periode,'periode_teks'=>$periode_teks,'salesammount'=>$last_sales,'id_employee'=>$id_employee,'cabang'=>'Plan','menu'=>'overtime','submenu'=>'approval']);
    }
    function planLegalize($periode){
        $cek_lock=DB::connection('mysql')->table('tb_utilities')->where('atribut','limit_approval_ot')->where('status','1')->count();
        //if($cek_lock==1){
            $this->autoCancel();
        //}
        date_default_timezone_set("Asia/Jakarta");
        $kalendar=CAL_GREGORIAN;
        if($periode==0){$periode=date('Y-m');$periode_teks=date('F Y');}
        else $periode_teks=date('F Y',strtotime($periode.'-01'));
        $thn=date('Y',strtotime($periode.'-01'));
        $bln=date('m',strtotime($periode.'-01'));
        $hariakhir=cal_days_in_month($kalendar,$bln,$thn);
        $periode_awal=date('Y-m-d',strtotime($thn.'-'.$bln.'-01'));
        $periode_akhir=date('Y-m-d',strtotime($thn.'-'.$bln.'-'.$hariakhir));

        $email=Auth::user()->email;
        $tb_email=tb_email::where('email_address',$email)->get();
        $id_employee='';
        foreach($tb_email as $dt){
            $id_employee=$dt->id_employee;
        }

        $tb_overtime_plan=DB::table('tb_overtimes')
        ->leftjoin('tb_employees as tb1','tb1.id','=','tb_overtimes.diperintah')
        ->leftjoin('tb_employees as tb2','tb2.id','=','tb_overtimes.disetujui')
        ->leftjoin('tb_employees as tb3','tb3.id','=','tb_overtimes.diketahui')
        ->select('tb_overtimes.*','tb1.employee_name as nm1','tb2.employee_name as nm2','tb3.employee_name as nm3')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_overtimes.dept_id')
        ->where([['tb_overtimes.diperintah',$id_employee],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<=','3'],['tb_overtimes.status_disetujui','<=','3'],['tb_overtimes.status_diketahui','<=','3'],['tb_overtimes.status_dicatat','1'],['tb_overtimes.status_approve','0'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.disetujui',$id_employee],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<=','3'],['tb_overtimes.status_disetujui','<=','3'],['tb_overtimes.status_diketahui','<=','3'],['tb_overtimes.status_dicatat','1'],['tb_overtimes.status_approve','0'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.diketahui',$id_employee],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<=','3'],['tb_overtimes.status_disetujui','<=','3'],['tb_overtimes.status_diketahui','<=','3'],['tb_overtimes.status_dicatat','1'],['tb_overtimes.status_approve','0'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.dicatat',$id_employee],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<=','3'],['tb_overtimes.status_disetujui','<=','3'],['tb_overtimes.status_diketahui','<=','3'],['tb_overtimes.status_dicatat','1'],['tb_overtimes.status_approve','0'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.approve',$id_employee],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<=','3'],['tb_overtimes.status_disetujui','<=','3'],['tb_overtimes.status_diketahui','<=','3'],['tb_overtimes.status_dicatat','1'],['tb_overtimes.status_approve','0'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.paid',$id_employee],['tb_overtimes.status','1'],['tb_overtimes.status_dicatat','1'],['tb_overtimes.status_approve','0'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->get(['tb_overtimes.*','tb_departments.dept_name']);
        //->count();
        //return $tb_overtime_plan;

        $tb_overtime_actual=DB::table('tb_overtimes')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_overtimes.dept_id')
        ->where([['tb_overtimes.diperintah',$id_employee],['status','1'],['tb_overtimes.status_dicatat','1'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.disetujui',$id_employee],['status','1'],['tb_overtimes.status_dicatat','1'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.diketahui',$id_employee],['status','1'],['tb_overtimes.status_dicatat','1'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.dicatat',$id_employee],['status','1'],['tb_overtimes.status_dicatat','1'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.approve',$id_employee],['status','1'],['tb_overtimes.status_dicatat','1'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.paid',$id_employee],['status','1'],['tb_overtimes.status_dicatat','1'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->get(['tb_overtimes.*','tb_departments.dept_name']);

        $tb_overtime=DB::table('tb_overtimes')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_overtimes.dept_id')
        ->where([['tb_overtimes.diperintah',$id_employee],['status','1'],['tb_overtimes.status_paid','1'],['tb_departments.isTrial','1'],['tb_departments.isTrial','1'],['ot_date','>=',$periode_awal],['ot_date','<=',$periode_akhir],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.disetujui',$id_employee],['status','1'],['tb_overtimes.status_paid','1'],['tb_departments.isTrial','1'],['tb_departments.isTrial','1'],['ot_date','>=',$periode_awal],['ot_date','<=',$periode_akhir],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.diketahui',$id_employee],['status','1'],['tb_overtimes.status_paid','1'],['tb_departments.isTrial','1'],['tb_departments.isTrial','1'],['ot_date','>=',$periode_awal],['ot_date','<=',$periode_akhir],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.dicatat',$id_employee],['status','1'],['tb_overtimes.status_paid','1'],['tb_departments.isTrial','1'],['tb_departments.isTrial','1'],['ot_date','>=',$periode_awal],['ot_date','<=',$periode_akhir],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.approve',$id_employee],['status','1'],['tb_overtimes.status_paid','1'],['tb_departments.isTrial','1'],['tb_departments.isTrial','1'],['ot_date','>=',$periode_awal],['ot_date','<=',$periode_akhir],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.paid',$id_employee],['status','1'],['tb_overtimes.status_paid','1'],['tb_departments.isTrial','1'],['tb_departments.isTrial','1'],['ot_date','>=',$periode_awal],['ot_date','<=',$periode_akhir],['tb_overtimes.isDelete',0]])
        ->orderby('tb_overtimes.ot_date','desc')
        ->get(['tb_overtimes.*','tb_departments.dept_name']);

        $jmlot_plan=DB::table('tb_overtimes')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_overtimes.dept_id')
        ->where([['tb_overtimes.diperintah',$id_employee],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<','3'],['tb_overtimes.status_disetujui','<','3'],['tb_overtimes.status_diketahui','<','3'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.disetujui',$id_employee],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<','3'],['tb_overtimes.status_disetujui','<','3'],['tb_overtimes.status_diketahui','<','3'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.diketahui',$id_employee],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<','3'],['tb_overtimes.status_disetujui','<','3'],['tb_overtimes.status_diketahui','<','3'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.dicatat',$id_employee],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<','3'],['tb_overtimes.status_disetujui','<','3'],['tb_overtimes.status_diketahui','<','3'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.approve',$id_employee],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<','3'],['tb_overtimes.status_disetujui','<','3'],['tb_overtimes.status_diketahui','<','3'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.paid',$id_employee],['tb_overtimes.status','1'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->count();
        $jmlot_actual=DB::table('tb_overtimes')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_overtimes.dept_id')
        ->where([['tb_overtimes.diperintah',$id_employee],['status','1'],['tb_overtimes.status_diperintah','1'],['tb_overtimes.status_dicatat','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.disetujui',$id_employee],['status','1'],['tb_overtimes.status_diperintah','1'],['tb_overtimes.status_dicatat','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.diketahui',$id_employee],['status','1'],['tb_overtimes.status_diperintah','1'],['tb_overtimes.status_dicatat','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.dicatat',$id_employee],['status','1'],['tb_overtimes.status_diperintah','1'],['tb_overtimes.status_dicatat','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.approve',$id_employee],['status','1'],['tb_overtimes.status_diperintah','1'],['tb_overtimes.status_dicatat','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.paid',$id_employee],['status','1'],['tb_overtimes.status_diperintah','1'],['tb_overtimes.status_dicatat','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->count();

        date_default_timezone_set("Asia/Jakarta");
        $kalendar=CAL_GREGORIAN;

        //$thn='2021';
        //$bln='04';

        $thn=date('Y',strtotime($periode.'-01'));
        $bln=date('m',strtotime($periode.'-01'));
        
        $hariakhir=cal_days_in_month($kalendar,$bln,$thn);

        $Tglawal=date('Y-m-d',strtotime($thn.'-'.$bln.'-01'));
        $Tglakhir=date('Y-m-d',strtotime($thn.'-'.$bln.'-'.$hariakhir));

        $tb_sumot_detail=$results = DB::select( DB::raw("SELECT id,dept_code,
        (select sum(ammount) from tb_overtime_details left join tb_overtimes on tb_overtime_details.id_ot=tb_overtimes.id where tb_overtimes.dept_id=tb_departments.id and tb_overtime_details.date_on>='$Tglawal' and tb_overtime_details.date_on<='$Tglakhir' and tb_overtime_details.status<'90')as total_act,
        (select sum(ammount) from tb_overtime_details left join tb_overtimes on tb_overtime_details.id_ot=tb_overtimes.id where tb_overtimes.dept_id=tb_departments.id and tb_overtimes.status_approve='1' and tb_overtime_details.date_on>='$Tglawal' and tb_overtime_details.date_on<='$Tglakhir' and tb_overtime_details.status<'90')as total_act2,
        (select sum(ammount) from tb_overtime_details left join tb_overtimes on tb_overtime_details.id_ot=tb_overtimes.id where tb_overtimes.dept_id=tb_departments.id and tb_overtimes.status_approve='0' and tb_overtime_details.date_on>='$Tglawal' and tb_overtime_details.date_on<='$Tglakhir' and tb_overtime_details.status<'90')as total_act3,
        (select tb_overtime_target.ammount from tb_overtime_target left join tb_overtimes on tb_overtime_target.dept_id=tb_overtimes.dept_id where tb_overtime_target.dept_id=tb_departments.id and tb_overtime_target.periode='$periode' limit 1)as ammount_target
        FROM tb_departments
        where EXISTS(select * from tb_admins where tb_admins.dept_id=tb_departments.id and tb_admins.id_employee='$id_employee') order by total_act desc") );

        //$total_sumot=DB::table('tb_overtime_details')->where('date_on','>=',$Tglawal)->where('date_on','<=',$Tglakhir)->where('status','<','90')->sum('hours_act');
        //$total_ammount=DB::table('tb_overtime_details')->where('date_on','>=',$Tglawal)->where('date_on','<=',$Tglakhir)->where('status','<','90')->sum('ammount');
        $total_sumot=DB::table('tb_overtime_details')->where('date_on','>=',$Tglawal)->where('date_on','<=',$Tglakhir)->where('status','>=','1')->where('status','<','90')->sum('hours_act');
        $total_ammount=DB::table('tb_overtime_details')->where('date_on','>=',$Tglawal)->where('date_on','<=',$Tglakhir)->where('status','>=','1')->where('status','<','90')->sum('ammount');
        //return $tb_sumot_detail;

        $last_check=DB::table('tb_overtime_sales')->where('periode',$periode)->count();
        if($last_check==0){
            $prev=DB::table('tb_overtime_sales')->orderby('periode','desc')->take(1)->get();
            foreach($prev as $dt_prev){
                $add_new=DB::table('tb_overtime_sales')->insert([
                    'periode'=>$periode,
                    'amount_sales'=>$dt_prev->amount_sales,
                ]);
            }
        }

        $qty_sumot=DB::table('tb_overtime_sales')->count();
        $take=12;
        $skip=$qty_sumot-$take;
        if($skip<0)$skip=0;
        
        $last=DB::table('tb_overtime_sales')->where('periode',$periode)->get();
        foreach($last as $dt_last){
            $last_sales=$dt_last->amount_sales;
            $last_target=$last_sales/100;
            $act_persentase=number_format($total_ammount/$last_sales*100,2);
            DB::table('tb_overtime_sales')->where('periode',$periode)->update([
                'amount_sales'=>$last_sales,
                'amount_overtime'=>$total_ammount,
                'target_persentase'=>'1',
                'aktual_persentase'=>$act_persentase,
                'target_overtime'=>$last_target
            ]);
        }

        $tb_sumot=DB::table('tb_overtime_sales')->orderby('periode','asc')->take($take)->skip($skip)->get();
        $Tglawal='2200-01';
        $Tglakhir='2000-01';
        foreach($tb_sumot as $dt){
            if($Tglawal>$dt->periode)$Tglawal=$dt->periode;
            if($Tglakhir<$dt->periode)$Tglakhir=$dt->periode;
        }
        //return $last_sales;
        $pareto=session('pareto','');
        if($pareto==''||$pareto=='0')
        return view('page/admin/m_overtime/createdspl_legalize',['total_sumot'=>$total_sumot,'tb_sumot'=>$tb_sumot,'tb_sumot_detail'=>$tb_sumot_detail,'tb_overtime_plan'=>$tb_overtime_plan,'tb_overtime_actual'=>$tb_overtime_actual,'tb_overtime'=>$tb_overtime,'jmlot_plan'=>$jmlot_plan,'jmlot_actual'=>$jmlot_actual,'Tglawal'=>$Tglawal,'Tglakhir'=>$Tglakhir,'periode'=>$periode,'periode_teks'=>$periode_teks,'salesammount'=>$last_sales,'id_employee'=>$id_employee,'cabang'=>'Plan','menu'=>'overtime','submenu'=>'approval']);
        else
        return view('page/admin/m_overtime/createdspl2',['total_sumot'=>$total_sumot,'tb_sumot'=>$tb_sumot,'tb_sumot_detail'=>$tb_sumot_detail,'tb_overtime_plan'=>$tb_overtime_plan,'tb_overtime_actual'=>$tb_overtime_actual,'tb_overtime'=>$tb_overtime,'jmlot_plan'=>$jmlot_plan,'jmlot_actual'=>$jmlot_actual,'Tglawal'=>$Tglawal,'Tglakhir'=>$Tglakhir,'periode'=>$periode,'periode_teks'=>$periode_teks,'salesammount'=>$last_sales,'id_employee'=>$id_employee,'cabang'=>'Plan','menu'=>'overtime','submenu'=>'approval']);
    }
    function grafik($periode,$pareto){
        session(['pareto'=>$pareto]);
        return redirect()->back();
    }
    public function autoCancel(){
        $cek_lock=DB::connection('mysql')->table('tb_utilities')->where('atribut','limit_approval_ot')->where('status','1')->count();
        if($cek_lock==1){
            date_default_timezone_set("Asia/Jakarta");
            $tgl_sekarang=date('Y-m-d H:i:s');
            //$tgl_sekarang='2022-04-05 15:00:00';
            $ot_diperintah=DB::table('tb_overtimes')
            ->where('autoCancel','0')
            ->where('status','1')
            ->where('status_diperintah','0')
            //->where('dept_id','99')
            ->where('created_at','>','2022-01-01 00:00:01')
            ->get();
            foreach($ot_diperintah as $row){
                $tgl_ot=date('Y-m-d H:i:s',strtotime($row->ot_date.' 20:00:00'));
                for($i=1;$i<30;$i++){
                    $batas=date('Y-m-d H:i:s',strtotime('+'.$i.' days',strtotime($tgl_ot)));
                    $batas_tgl=date('Y-m-d',strtotime($batas));
                    $wday=date('w',strtotime($batas));
                    $libur=DB::table('tb_freedays')->where('date_off',$batas_tgl)->count();
                    if($wday>0&&$wday<6&&$libur==0)break;
                }
                if($batas<$tgl_sekarang){
                    $cancel=DB::table('tb_overtimes')->where('id',$row->id)->update(['status_diperintah'=>'3','date_diperintah'=>$batas,'autoCancel'=>'1']);
                }
            }
            $ot_disetujui=DB::table('tb_overtimes')
            ->where('autoCancel','0')
            ->where('status','1')
            ->where('disetujui','>','0')
            ->where('status_disetujui','0')
            //->where('dept_id','99')
            ->where('created_at','>','2022-01-01 00:00:01')
            ->get();
            foreach($ot_disetujui as $row){
                $tgl_ot=date('Y-m-d H:i:s',strtotime($row->ot_date.' 20:00:00'));
                for($i=1;$i<30;$i++){
                    $batas=date('Y-m-d H:i:s',strtotime('+'.$i.' days',strtotime($tgl_ot)));
                    $batas_tgl=date('Y-m-d',strtotime($batas));
                    $wday=date('w',strtotime($batas));
                    $libur=DB::table('tb_freedays')->where('date_off',$batas_tgl)->count();
                    if($wday>0&&$wday<6&&$libur==0)break;
                }
                if($batas<$tgl_sekarang){
                    $cancel=DB::table('tb_overtimes')->where('id',$row->id)->update(['status_disetujui'=>'3','date_disetujui'=>$batas,'autoCancel'=>'1']);
                }
            }
            $ot_ketahui=DB::table('tb_overtimes')
            ->where('autoCancel','0')
            ->where('status','1')
            ->where('diketahui','>','0')
            ->where('status_diketahui','0')
            //->where('dept_id','99')
            ->where('created_at','>','2022-01-01 00:00:01')
            ->get();
            foreach($ot_ketahui as $row){
                $tgl_ot=date('Y-m-d H:i:s',strtotime($row->ot_date.' 20:00:00'));
                for($i=1;$i<30;$i++){
                    $batas=date('Y-m-d H:i:s',strtotime('+'.$i.' days',strtotime($tgl_ot)));
                    $batas_tgl=date('Y-m-d',strtotime($batas));
                    $wday=date('w',strtotime($batas));
                    $libur=DB::table('tb_freedays')->where('date_off',$batas_tgl)->count();
                    if($wday>0&&$wday<6&&$libur==0)break;
                }
                if($batas<$tgl_sekarang){
                    $cancel=DB::table('tb_overtimes')->where('id',$row->id)->update(['status_diketahui'=>'3','date_diketahui'=>$batas,'autoCancel'=>'1']);
                }
            }
        }
        //$nama=Auth::user()->name;
        $now=date('Y-m-d H:i:s');
        $cek=DB::table('tb_utilities_exception')->where('id_utility','9')->where('start','<=',$now)->where('end','>=',$now)->where('status','1')->where('start','<=',$now)->where('end','>=',$now)->get();
        return $cek;
        foreach($cek as $dt){
            $ot=DB::table('tb_overtimes')
            ->where('autoCancel','1')
            ->where('status','1')
            ->where('admin',$dt->admin)
            ->where('created_at','>','2025-06-01 00:00:01')
            ->get();
            foreach($ot as $dt){
                if($dt->status_diperintah==3)$update=DB::table('tb_overtimes')->where('id',$dt->id)->update(['status_diperintah'=>'0','date_diperintah'=>Null,'autoCancel'=>'0']);
                if($dt->status_disetujui==3)$update=DB::table('tb_overtimes')->where('id',$dt->id)->update(['status_disetujui'=>'0','date_disetujui'=>Null,'autoCancel'=>'0']);
                if($dt->status_diketahui==3)$update=DB::table('tb_overtimes')->where('id',$dt->id)->update(['status_diketahui'=>'0','date_diketahui'=>Null,'autoCancel'=>'0']);
            }
        }
 
    }
    public function autoCancel_malam(){
        date_default_timezone_set("Asia/Jakarta");
        $tgl_sekarang=date('Y-m-d H:i:s');
        $ot_diperintah=DB::table('tb_overtimes')
        ->where('autoCancel','0')
        ->where('status','1')
        ->where('status_diperintah','0')
        ->where('dept_id','99')
        ->where('created_at','>','2022-01-01 00:00:01')
        ->get();
        foreach($ot_diperintah as $row){
            $tgl_pembuatan=date('Y-m-d H:i:s',strtotime($row->ot_date.' 20:00:00'));
            $batas=date('Y-m-d H:i:s',strtotime('+1 days',strtotime($tgl_pembuatan)));
            if($batas<$tgl_sekarang){
                $cancel=DB::table('tb_overtimes')->where('id',$row->id)->update(['status_diperintah'=>'3','date_diperintah'=>$batas,'autoCancel'=>'1']);
            }
        }
        $ot_disetujui=DB::table('tb_overtimes')
        ->where('autoCancel','0')
        ->where('status','1')
        ->where('disetujui','>','0')
        ->where('status_disetujui','0')
        ->where('dept_id','99')
        ->where('created_at','>','2022-01-01 00:00:01')
        ->get();
        foreach($ot_disetujui as $row){
            $tgl_pembuatan=date('Y-m-d H:i:s',strtotime($row->ot_date.' 20:00:00'));
            $batas=date('Y-m-d H:i:s',strtotime('+1 days',strtotime($tgl_pembuatan)));
            if($batas<$tgl_sekarang){
                $cancel=DB::table('tb_overtimes')->where('id',$row->id)->update(['status_disetujui'=>'3','date_disetujui'=>$batas,'autoCancel'=>'1']);
            }
        }
        $ot_ketahui=DB::table('tb_overtimes')
        ->where('autoCancel','0')
        ->where('status','1')
        ->where('diketahui','>','0')
        ->where('status_diketahui','0')
        ->where('dept_id','99')
        ->where('created_at','>','2022-01-01 00:00:01')
        ->get();
        foreach($ot_ketahui as $row){
            $tgl_pembuatan=date('Y-m-d H:i:s',strtotime($row->ot_date.' 20:00:00'));
            $batas=date('Y-m-d H:i:s',strtotime('+1 days',strtotime($tgl_pembuatan)));
            if($batas<$tgl_sekarang){
                $cancel=DB::table('tb_overtimes')->where('id',$row->id)->update(['status_diketahui'=>'3','date_diketahui'=>$batas,'autoCancel'=>'1']);
            }
        }
    }
    public function autoCancel_Original(){
        date_default_timezone_set("Asia/Jakarta");
        $tgl_sekarang=date('Y-m-d H:i:s');
        $ot_diperintah=DB::table('tb_overtimes')
        ->where('autoCancel','0')
        ->where('status','1')
        ->where('status_diperintah','0')
        //->where('dept_id','99')
        ->where('created_at','>','2022-01-01 00:00:01')
        ->get();
        foreach($ot_diperintah as $row){
            $tgl_pembuatan=$row->created_at;
            $batas=date('Y-m-d H:i:s',strtotime('+1 days',strtotime($tgl_pembuatan)));
            if($batas<$tgl_sekarang){
                $cancel=DB::table('tb_overtimes')->where('id',$row->id)->update(['status_diperintah'=>'3','date_diperintah'=>$batas,'autoCancel'=>'1']);
            }
        }
        $ot_disetujui=DB::table('tb_overtimes')
        ->where('autoCancel','0')
        ->where('status','1')
        ->where('disetujui','>','0')
        ->where('status_disetujui','0')
        //->where('dept_id','99')
        ->where('created_at','>','2022-01-01 00:00:01')
        ->get();
        foreach($ot_disetujui as $row){
            $tgl_pembuatan=$row->created_at;
            $batas=date('Y-m-d H:i:s',strtotime('+1 days',strtotime($tgl_pembuatan)));
            if($batas<$tgl_sekarang){
                $cancel=DB::table('tb_overtimes')->where('id',$row->id)->update(['status_disetujui'=>'3','date_disetujui'=>$batas,'autoCancel'=>'1']);
            }
        }
        $ot_ketahui=DB::table('tb_overtimes')
        ->where('autoCancel','0')
        ->where('status','1')
        ->where('diketahui','>','0')
        ->where('status_diketahui','0')
        //->where('dept_id','99')
        ->where('created_at','>','2022-01-01 00:00:01')
        ->get();
        foreach($ot_ketahui as $row){
            $tgl_pembuatan=$row->created_at;
            $batas=date('Y-m-d H:i:s',strtotime('+1 days',strtotime($tgl_pembuatan)));
            if($batas<$tgl_sekarang){
                $cancel=DB::table('tb_overtimes')->where('id',$row->id)->update(['status_diketahui'=>'3','date_diketahui'=>$batas,'autoCancel'=>'1']);
            }
        }
    }
    function confirm($id,$id_confirm){
        date_default_timezone_set("Asia/Jakarta");
        $sekarang=date('Y-m-d H:i:s');
        $update=DB::table('tb_overtime_details')->where('id',$id)->update([
            'id_confirm'=>$id_confirm,
            'date_confirm'=>$sekarang,
            'updated_at'=>$sekarang
        ]);
        if($update)return redirect()->back()->with(['success'=>'Confirm Success']);
    }
    public function update_summary($id,$action){
        $hasil='start';
        date_default_timezone_set("Asia/Jakarta");
        $kalendar=CAL_GREGORIAN;
        $sekarang=date('Y-m-d H:i:s');
        //Get ot_detail
        $tb_ot_detail=DB::table('tb_overtime_details')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_overtime_details.id_employee')
        ->leftjoin('tb_overtimes','tb_overtimes.id','=','tb_overtime_details.id_ot')
        ->where('tb_overtime_details.status','6')
        ->where('tb_overtimes.id',$id)->get(['tb_overtime_details.*','tb_employees.dept_id']);
        foreach($tb_ot_detail as $dt){
            $periode=date('Y-m',strtotime($dt->date_on));
            $dept_id=$dt->dept_id;
            $Tgl=$dt->date_on;
            $id_employee=$dt->id_employee;
            $slpj=round($dt->SLPJ);
            $id_ot_detail=$dt->id;
            //Create if empty
            $check=DB::connection('emsOvertime')->table('tb_slip_overtimes_trial')->where('id_employee',$id_employee)->where('date_on',$Tgl)->count();
            if($check==0){
                $create=DB::connection('emsOvertime')->table('tb_slip_overtimes_trial')->insert([
                    'periode'=>$periode,
                    'dept_id'=>$dept_id,
                    'date_on'=>$Tgl,
                    'id_employee'=>$dt->id_employee,
                    'slpj'=>$slpj,
                ]);   
                $hasil.='#1';       
            }
            //Get last slip
            $tb_slip=DB::connection('emsOvertime')->table('tb_slip_overtimes_trial')->where('id_employee',$id_employee)->where('date_on',$Tgl)->get();
            foreach($tb_slip as $dt2){
                $t_hours=$dt2->act_hours;
                $t_convertion=$dt2->act_convertion;
                $t_ammount=$dt2->ammount;
                $meal_off=$dt2->meal_off;
                $meal_tl=$dt2->meal_tl;
                $meal_ot=$dt2->meal_ot;
                $total_bayar=$t_ammount+$meal_off+$meal_tl+$meal_ot;
                $hasil.='#2';       
            }
            //Update slip
            if($action=='add')$pengali=1;
            else $pengali=-1;
            $tb_overtime_detail=tb_overtime_detail::where('id',$id_ot_detail)->get();
            foreach ($tb_overtime_detail as $dt3) {
                $t_hours=$t_hours+($dt3->hours_act*$pengali);
                $t_convertion=$t_convertion+($dt3->hours_convertion*$pengali);
                $t_ammount=$t_ammount+($dt3->ammount*$pengali);
                $total_bayar=$t_ammount+$meal_off+$meal_tl+$meal_ot;

                $update_slip=DB::connection('emsOvertime')->table('tb_slip_overtimes_trial')->where('id_employee',$id_employee)->where('date_on',$Tgl)->update([
                    'periode'=>$periode,
                    'date_on'=>$Tgl,
                    'ot_category'=>$dt3->ot_category,
                    'id_employee'=>$id_employee,
                    'id_overtime'=>$dt3->id_ot,
                    'id_overtime_detail'=>$dt3->id,
                    'slpj'=>$slpj,
                    'ot_start'=>$dt3->start_act,
                    'ot_finish'=>$dt3->finish_act,
                    'act_hours'=>$t_hours,
                    'act_convertion'=>$t_convertion,
                    'ammount'=>$t_ammount,
                    'total_bayar'=>$total_bayar,
                    'status'=>'0',
                    'updated_at'=>$sekarang
                ]);
                $hasil.='#3';       
            }
        }
        //return $hasil;
    }
    function ReviewOT($id,$pass){
        $today = date('Y-m-d');
        $lastDate = date('Y-m-d', strtotime('-7 days'));

        if($id==0){
            $tb_overtime_plan=DB::table('tb_overtimes')
            ->leftjoin('tb_employees as tb1','tb1.id','=','tb_overtimes.diperintah')
            ->leftjoin('tb_employees as tb2','tb2.id','=','tb_overtimes.disetujui')
            ->leftjoin('tb_employees as tb3','tb3.id','=','tb_overtimes.diketahui')
            ->select('tb_overtimes.*','tb1.employee_name as nm1','tb2.employee_name as nm2','tb3.employee_name as nm3')
            ->where('tb_overtimes.status','1')
            ->where('tb_overtimes.isDelete','0')
            ->Where(function ($query) {
                $query->where('tb_overtimes.dept_id', '7')
                      ->orwhere('tb_overtimes.dept_id', '11');
            });  
            if($pass==0){
                $tb_overtime_plan=$tb_overtime_plan->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('tb_overtime_details')
                        ->whereColumn('tb_overtime_details.id_ot', 'tb_overtimes.id')
                        ->where([['tb_overtime_details.sign_before','0'],['tb_overtime_details.status','<','90']]);
                })
                ->where('tb_overtimes.ot_date','>=',$today)->get();
            }else{
                $tb_overtime_plan=$tb_overtime_plan->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('tb_overtime_details')
                        ->whereColumn('tb_overtime_details.id_ot', 'tb_overtimes.id');
                        //->where([['tb_overtime_details.sign_before','1'],['tb_overtime_details.status','<','90']]);
                })
                ->where('tb_overtimes.ot_date','<',$today)->where('tb_overtimes.ot_date','>',$lastDate)->get();
            }
            //return $lastDate;

            return view('page/admin/m_overtime/reviewspl',['tb_overtime_plan'=>$tb_overtime_plan,'pass'=>$pass,'menu'=>'overtime','submenu'=>'review']);
        }else{
            $tb_overtime_detail=DB::table('tb_overtime_details')->leftjoin('tb_overtimes','tb_overtimes.id','=','tb_overtime_details.id_ot')
            ->leftjoin('tb_employees','tb_employees.id','=','tb_overtime_details.id_employee')
            ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_overtime_details.id_employee_plan')
            ->leftjoin('tb_overtime_details_review','tb_overtime_details.id','=','tb_overtime_details_review.id_detail')
            ->where('tb_overtime_details.id_ot',$id)->get(['tb_overtime_details.id as id_detail','tb_overtime_details.id_ot','tb_overtime_details.id_overtime','tb_overtime_details.date_on','tb_overtime_details.start_plan','tb_overtime_details.finish_plan','tb_overtime_details.reason','tb_employees.NIK','tb_employees.employee_name','tb_overtimes.status_diketahui','tb_overtimes.status_dicatat','tb_employees1.employee_name as employee_plan','tb_overtime_details_review.*']);
            //return $tb_overtime_detail;
            
            return view('page/admin/m_overtime/reviewspl_detail',['tb_overtime_detail'=>$tb_overtime_detail,'pass'=>$pass,'menu'=>'overtime','submenu'=>'review']);
        }
    }
    function ReviewUpdate(Request $data){
        $field=$data->bagian.'_status';
        if($data->nilai==0)$nilai=1;
        else $nilai=0;
        $update=DB::table('tb_overtime_details_review')->where('id_head',$data->idhead)->where('id_detail',$data->iddetail)->update([
            $field=>$nilai
        ]);
        if($update)
        return 'OK';
        else 
        return 'Gagal Update';
    }
    function ReviewUpdateAll(Request $data){
        $field=$data->bagian.'_status';
        if($data->nilai==0)$nilai=1;
        else $nilai=0;

        $update=DB::table('tb_overtime_details_review')->where('id_head',$data->idhead)->update([
            $field=>$nilai
        ]);
        
        if($update)
        return $data->idhead;
        else 
        return 'Gagal Update';
    }
    function OTCategory(){
        $tb_reason_ots=DB::table('tb_reason_ots')->orderby('reason_ot','asc')->get();
        $data['table1']=DB::table('tb_group_reason_ot')->select('category')->groupby('category')->whereNotNull('category')->get();
        $data['table2']=DB::table('tb_group_reason_ot')->select('detail_category')->groupby('detail_category')->whereNotNull('detail_category')->get();
        //return $data['table1'];
        if (request()->user()->hasRole('hr_access'))$data['status']='';
        else $data['status']='disabled';
        return view('page/admin/m_overtime/ot_category',['tb_reason_ots'=>$tb_reason_ots,'data'=>$data,'menu'=>'category']);
    }
    function OTCategoryCreate(Request $data){
        if($data->idcategory==''){
            $this->validate($data,[
                'reason_ot' => 'required|unique:tb_reason_ots,reason_ot',
            ]);
            $add=DB::table('tb_reason_ots')->insert([
                'reason_ot'=>$data->reason_ot,
                'category'=>$data->category,
                'detail_category'=>$data->detail_category,
            ]);
    
        }else{
            $update=DB::table('tb_reason_ots')->where('id',$data->idcategory)->update([
                'category'=>$data->category,
                'detail_category'=>$data->detail_category,
            ]);
        }
        return redirect()->back();
    }
    function OTCategoryDelete($id){
        // $delete=DB::table('tb_reason_ots')->where('id',$id)->delete();
        $delete=DB::table('tb_reason_ots')->where('id',$id)->update(['is_active'=>'0']);
        return redirect()->back();
    }
    function OTCategoryActive($id){
        // $delete=DB::table('tb_reason_ots')->where('id',$id)->delete();
        $delete=DB::table('tb_reason_ots')->where('id',$id)->update(['is_active'=>'1']);
        return redirect()->back();
    }
    function OTCategorySelect(Request $data){
        $data['table1']=DB::table('tb_group_reason_ot')->select('detail_category')->where('category',$data->category)->get();
        $isi="<option value=''></option>";
        foreach($data['table1'] as $dt){
          $isi.= "<option value='".$dt->detail_category."'>".$dt->detail_category."</option>";
        }
        return $isi;
    }
    function selectCategory(Request $data){
        $isi="<option value=''></option>";
        $x='';
        if($data->id_memo!=''){
            $data['table1']=DB::connection('memo')->table('tb_memo_ot')
            ->leftjoin('tb_memo','tb_memo.id','=','tb_memo_ot.id_memo')
            ->select('reason_ot','memo_number',DB::raw('count(*) as item_qty'))
            ->where('tb_memo.memo_number',$data->id_memo)
            ->groupby(['reason_ot'],['memo_number'])
            ->get();
            foreach($data['table1'] as $dt1){
                $data['table2']=DB::table('tb_reason_ots')->select('category')->where('reason_ot',$dt1->reason_ot)->groupby('category')->get();
                foreach($data['table2'] as $dt2){
                    if($x!=$dt2->category)
                    $isi.= "<option value='".$dt2->category."'>".$dt2->category."</option>";
                    $x=$dt2->category;
                }
            }
        }else{
            $data['table2']=DB::table('tb_group_reason_ot')->select('category')->groupby('category')->whereNotNull('category')->get();
            foreach($data['table2'] as $dt2){
                if($x!=$dt2->category)
                $isi.= "<option value='".$dt2->category."'>".$dt2->category."</option>";
                $x=$dt2->category;
            }
        }
        return $isi;
    }
    function selectSubCategory(Request $data){
        $isi="<option value=''></option>";
        $x='';
        if($data->id_memo!=''){
            $data['table1']=DB::connection('memo')->table('tb_memo_ot')
            ->leftjoin('tb_memo','tb_memo.id','=','tb_memo_ot.id_memo')
            ->select('reason_ot','memo_number',DB::raw('count(*) as item_qty'))
            ->where('tb_memo.memo_number',$data->id_memo)
            ->groupby(['reason_ot'],['memo_number'])
            ->get();
            foreach($data['table1'] as $dt1){
                $data['table2']=DB::table('tb_reason_ots')->select('detail_category')->where('reason_ot',$dt1->reason_ot)->groupby('detail_category')->get();
                foreach($data['table2'] as $dt2){
                    if($x!=$dt2->detail_category)
                    $isi.= "<option value='".$dt2->detail_category."'>".$dt2->detail_category."</option>";
                    $x=$dt2->detail_category;
                }
            }
        }else{
            $data['table2']=DB::table('tb_group_reason_ot')->select('category','detail_category')->groupby('category')->groupby('detail_category')->where('category',$data->category)->get();
            foreach($data['table2'] as $dt2){
                if($x!=$dt2->detail_category)
                $isi.= "<option value='".$dt2->detail_category."'>".$dt2->detail_category."</option>";
                $x=$dt2->detail_category;
            }
        }
        return $isi;
    }
    function selectDetailCategory(Request $data){
        $isi="<option value=''></option>";
        $x='';
        if($data->id_memo!=''){
            $data['table1']=DB::connection('memo')->table('tb_memo_ot')
            ->leftjoin('tb_memo','tb_memo.id','=','tb_memo_ot.id_memo')
            ->select('reason_ot','memo_number',DB::raw('count(*) as item_qty'))
            ->where('tb_memo.memo_number',$data->id_memo)
            ->groupby(['reason_ot'],['memo_number'])
            ->get();
            foreach($data['table1'] as $dt1){
                $data['table2']=DB::table('tb_reason_ots')->select('reason_ot')->where('reason_ot',$dt1->reason_ot)->groupby('reason_ot')->get();
                foreach($data['table2'] as $dt2){
                    if($x!=$dt2->reason_ot)
                    $isi.= "<option value='".$dt2->reason_ot."'>".$dt2->reason_ot."</option>";
                    $x=$dt2->reason_ot;
                }
            }
        }else{
            $data['table2']=DB::table('tb_reason_ots')->select('reason_ot')->groupby('reason_ot')->where('category',$data->category)->where('detail_category',$data->subcategory)->get();
            foreach($data['table2'] as $dt2){
                if($x!=$dt2->reason_ot)
                $isi.= "<option value='".$dt2->reason_ot."'>".$dt2->reason_ot."</option>";
                $x=$dt2->reason_ot;
            }
        }

        return $isi;
    }
    function selectDetailCategory2(Request $data){
        $isi="";
        $x='';
        if($data->id_memo!=''){
            $data['table1']=DB::connection('memo')->table('tb_memo_ot')
            ->leftjoin('tb_memo','tb_memo.id','=','tb_memo_ot.id_memo')
            ->where('tb_memo.memo_number',$data->id_memo)
            ->where('tb_memo_ot.reason_ot',$data->reasonot)
            ->orderby('remark','asc')
            ->get();
            foreach($data['table1'] as $dt1){
                if($x!=$dt1->remark){
                    $isi.=$dt1->remark.', ';
                }
                $x=$dt1->remark;
            }
        }

        return $isi;
    }
    public function approveOvertime($id){
        $tb_overtime=tb_overtime::where('id',$id)->get();
        $data['kontak']='';
        $data['pesan']='';
        $id_employee='';
        foreach($tb_overtime as $dt){
            $id_overtime=$dt->id_overtime;
            $ot_date=$dt->ot_date;
            $dept=$dt->dept_name;
            if($dt->status_diperintah==0){
                $id_employee=$dt->diperintah;
                $pos='Approval Status Diperintah';
            }else if($dt->disetujui!=null&&$dt->status_disetujui==0){
                $id_employee=$dt->disetujui;
                $pos='Approval Status Disetujui';
            }else if($dt->diketahui!=null&&$dt->status_diketahui==0){
                $id_employee=$dt->diketahui;
                $pos='Approval Status Diketahui';
            }
            $qty=DB::table('tb_overtime_details')->where('id_ot',$id)->count();
            if($id_employee!=''){
                $data['kontak']=DB::table('tb_employee_detail')->where('id_employee',$id_employee)->value('nomor_telepon');
                $data['pesan']="*NOTIFIKASI SPL*\n\nID: *$id_overtime*\nTanggal: *$ot_date*\nDepartemen: *$dept*\nJumlah: *$qty orang*\n\nMenunggu *$pos* oleh Anda.\n\nSegera lakukan pengecekan via EMS, klik link berikut:\nhttps://ems.summitadyawinsa.co.id/EMS/Admin/Overtime/Approval/$id";
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

    private function formatDateTimeForDatabase($value){
        if(empty($value)){
            return null;
        }

        foreach(['Y-m-d\\TH:i','Y-m-d\\TH:i:s','Y-m-d H:i:s','Y-m-d H:i'] as $format){
            $date=DateTime::createFromFormat($format,$value);
            if($date!==false){
                return $date->format('Y-m-d H:i:s');
            }
        }

        return null;
    }

}
