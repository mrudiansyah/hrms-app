<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DateTime;
use Auth;
use PDF;

class assigment_controller extends Controller
{
    public function __construct(){
        $this->middleware(['auth','verified']);
    }
    function index(){
        $admin=Auth::user()->name;
        $tb_assigment=DB::table('tb_overtime_spv')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_overtime_spv.id_employee')
        ->where('assigned_status','0')->where('admin',$admin)->get(['tb_overtime_spv.*','tb_employees.NIK','tb_employees.employee_name']);

        $lock_create=DB::table('tb_utilities')->where('id','19')->where('status','1')->count();
        if($lock_create>0){
            $email=Auth::user()->email;
            $id_employee=DB::table('tb_emails')->where('email_address',$email)->value('id_employee');
            $tb_employee=DB::table('tb_employees')->where('leader_id',$id_employee)->where('status','1')->orderby('employee_name','asc')->get();
        }else{
            $tb_employee=DB::table('tb_employees')
            ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
            ->where([['position_index','>=','3'],['position_index','<=','4'],['tb_positions.status_active','1'],['tb_employees.status','1']])
            ->whereNotIn('tb_employees.id', [131,132,133,911,1260,1261,1262]) //[BANJONG PRAMOONCHART, SUPOTE PATSRI, SAVEEYAH CHEDO, MUHAMMAD NERNTHONG, Somneuk Waenluang, Suriyan Promjak, Weerapong Ampawan]
            ->get(['tb_employees.*']);
        }

        $lock_backdate=DB::table('tb_utilities')->where('id','14')->where('status','1')->count();
        $now=date('Y-m-d H:i:s');
		$cek=DB::table('tb_utilities_exception')->where('id_utility','14')->where('admin',$admin)->where('status','1')->where('start','<=',$now)->where('end','>=',$now)->count();
		if($cek==1)$lock_backdate=0;
        $Tgl=date('Y-m-d');
		$Jam=date('Y-m-d').'T00:00';

        $today=date('Y-m-d');
        $tb_ref=DB::table('tb_memo')->where('tb_memo.id_category','1')->where('tb_memo.is_delete','0')->where('tb_memo.description','LIKE','Planned Overtime%')->where('date_information','>=',$today)->get();
        $category=DB::table('tb_reason_ots')->select('category')->where('is_active','1')->groupby('category')->get();
        $tb_reason_ot=DB::table('tb_reason_ots')->where('is_active','1')->orderby('id','desc')->orderby('group_reason','asc')->get();

        return view('page/admin/m_assigment/form_assigment',['tb_assigment'=>$tb_assigment,'tb_ref'=>$tb_ref,'category_list'=>$category,'tb_reason_ot'=>$tb_reason_ot,'tb_employee'=>$tb_employee,'menu'=>'assigment','lock_backdate'=>$lock_backdate,'Tgl'=>$Tgl,'Jam'=>$Jam]);
    }
    function select(Request $data){
        $id_employee=$data->id_employee;

        $qry=DB::table('tb_employees')->where('id',$id_employee)->get();
        foreach($qry as $row){
            $leader_id=$row->leader_id;
            $position_id=$row->position_id;
        }
        $dt_employee=DB::table('tb_employees')->where('id',$leader_id)->orderby('position_id','asc')->get();

        $assigned="<option value=''></pilih>";
        foreach ($dt_employee as $dt ){
            $assigned.= "<option value='".$dt->id."'>".$dt->employee_name."</option>";
        }
        return $assigned;
    }
    function adds(Request $data){

        $cek_dept=DB::table('tb_employees')
        ->where('id',$data->id_employee)
        ->where(function($query){
            $query->where('dept_id','1')
            ->orWhere('dept_id','2')
            ->orWhere('dept_id','3')
            ->orWhere('dept_id','6')
            ->orWhere('dept_id','10')
            ->orWhere('dept_id','16');
        })
        ->count();
        $status='OK';
        if($cek_dept==1){
            $status='Lock';
            if($data->assigned=='879'){
                $status='OK';
            }else{
                if($data->approved1=='879'){
                    $status='OK';
                }else{
                    if($data->approved2=='879'||$data->approved2!='')$status='OK';
                }
            }
        }
        if($status=='Lock'){
            return 'Aapproveal Assigment dept ini sampai BOD';
        }
        //return $status;
        
        $admin=Auth::user()->name;

        //Find dept Admin & CC_Code
        $email=Auth::user()->email;
        $tb_mail=DB::table('tb_emails')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_emails.id_employee')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->where('email_address',$email)
        ->get(['tb_departments.dept_code','tb_employees.cc_code','tb_employees.id as id_employee']);
        $dept_creater='';
        $cc_code='';
        foreach($tb_mail as $dt){
            $dept_creater=$dt->dept_code;
            $cc_code=$dt->cc_code;
            $login_id=$dt->id_employee;
        }
        if($login_id==$data->assigned)$status_assigned='1';
        else $status_assigned='0';
        //

        date_default_timezone_set("Asia/Jakarta");
        $Tgl=date('Y-m-d');
        $startPlan=Carbon::parse($data->start_plan)->format('Y-m-d H:i:s');
        $finishPlan=Carbon::parse($data->finish_plan)->format('Y-m-d H:i:s');
        $date_on=Carbon::parse($startPlan)->format('Y-m-d');
        $check=DB::table('tb_overtime_spv')->where('id_employee',$data->id_employee)->where('ot_date',$date_on)->count();
        // $check=DB::table('tb_overtime_spv')->where('id_employee',$data->id_employee)->where('start_plan',$data->start_plan)->count();
        if($check>0){
            return redirect()->back()->with(['success' => 'Sudah pernah dibuat, Silahkan check ulang']); 
        }else{
            $simpan=DB::table('tb_overtime_spv')->insert([
                'id_employee'=>$data->id_employee,
                'doc_date'=>$Tgl,
                'ot_date'=>$date_on,
                'start_plan'=>$startPlan,
                'finish_plan'=>$finishPlan,
                'start_act'=>$startPlan,
                'finish_act'=>$finishPlan,
                'hours_plan'=>$data->hours_plan,
                'hours_act'=>'0',
                'assigned'=>$data->assigned,
                'conducted'=>$data->id_employee,
                'approved1'=>$data->approved1,
                'approved2'=>$data->approved2,
                'legalized'=>'122',
                'assigned_status'=>$status_assigned,
                'conducted_status'=>'0',
                'approved1_status'=>'0',
                'approved2_status'=>'0',
                'legalized_status'=>'0',
                'jobs'=>$data->jobs,
                'admin'=>$admin,
                'dept_creater'=>$dept_creater,
                'cc_code'=>$cc_code,
                'reason_ot'=>$data->reason_ot,
            ]);
            if($simpan){
                $id=DB::table('tb_overtime_spv')->where('id_employee',$data->id_employee)->where('ot_date',$date_on)->value('id');
                //$this->notificationAssigment($id);
                return redirect()->back()->with(['success' => 'Save Data Berhasil']); 
            }
            else echo "Error";
        }
    }
    function show($id){
        $tb_assigment=DB::table('tb_overtime_spv')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_overtime_spv.id_employee')
        ->leftjoin('tb_employees as emp1','emp1.id','=','tb_overtime_spv.assigned')
        ->leftjoin('tb_employees as emp2','emp2.id','=','tb_overtime_spv.approved1')
        ->leftjoin('tb_employees as emp3','emp3.id','=','tb_overtime_spv.approved2')
        ->leftjoin('tb_employees as emp4','emp4.id','=','tb_overtime_spv.legalized')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->where('tb_overtime_spv.id',$id)->get(['tb_overtime_spv.*','tb_employees.NIK','tb_employees.employee_name','tb_departments.dept_name','tb_positions.position_name','emp1.employee_name as assigned_name','emp2.employee_name as approved1_name','emp3.employee_name as approved2_name','emp4.employee_name as legalized_name']);

        foreach($tb_assigment as $dt){
            $assigned=$dt->assigned;
        }

        $tb_atasan=DB::table('tb_employees')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->where('tb_employees.id',$assigned)->get(['tb_employees.*','tb_departments.dept_name','tb_positions.position_name']);

        $FileName='FORMSPL '.$id.'.PDF';
        $pdf = PDF::loadview('page/admin/m_assigment/printview',['tb_assigment'=>$tb_assigment,'tb_atasan'=>$tb_atasan]);
        return $pdf->stream($FileName);
    }
    function delete($id){
        $delete=DB::table('tb_overtime_spv')->where('id',$id)->delete();
        if($delete)
        return redirect()->back()->with(['success' => 'Delete Data Berhasil']); 
        else echo "Error";
    }
    function approvals($periode){
        date_default_timezone_set("Asia/Jakarta");
        $kalendar=CAL_GREGORIAN;
        if($periode==0)$periode=date('Y-m');
        $thn=date('Y',strtotime($periode.'-01'));
        $bln=date('m',strtotime($periode.'-01'));
        $hariakhir=cal_days_in_month($kalendar,$bln,$thn);

        $awal=date('Y-m-d',strtotime($periode.'-01'));
        $akhir=date('Y-m-d',strtotime($periode.'-'.$hariakhir));

        $admin=Auth::user()->name;
        $email=Auth::user()->email;
        $tb_email=DB::table('tb_emails')->where('email_address',$email)->get();
        foreach($tb_email as $dt){
            $id_employee=$dt->id_employee;
        }

        $min=DB::table('tb_overtime_spv')->orderby('ot_date','asc')->limit(1)->get();
        $thn_awal=date('Y');
        foreach($min as $row){
            $thn_awal=date('Y',strtotime($row->ot_date));
        }

        $limit_approval=DB::table('tb_utilities')->where('id','20')->where('status','1')->count();
        $limit_day=DB::table('tb_utilities')->where('id','20')->where('status','1')->value('limit_transaksi');
        $now=date('Y-m-d h:i:s');
        $tb_exception=DB::table('tb_utilities_exception')->where('id_utility','14')->where('status','1')->where('start','<=',$now)->where('end','>=',$now)->get();
        foreach($tb_exception as $dt){
             $update=DB::table('tb_overtime_spv')->where('legalized_status','0')->where('admin',$dt->admin)->update(['exception'=>'1']);
        }

        $tb_assigment_new=DB::table('tb_overtime_spv')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_overtime_spv.id_employee')
        ->where('legalized_status','0')
        // ->where('hours_act','0')
        ->Where(function($query) use($id_employee){
            $query->where('assigned',$id_employee)
            ->orWhere('conducted',$id_employee)
            ->orWhere('approved1',$id_employee)
            ->orWhere('approved2',$id_employee)
            ->orWhere('legalized',$id_employee);
        })
        ->get(['tb_overtime_spv.*','tb_employees.NIK','tb_employees.employee_name']);
        foreach($tb_assigment_new as $dt){
            if($id_employee==$dt->assigned)$tugas='assigned';
            elseif($id_employee==$dt->conducted)$tugas='conducted';
            elseif($id_employee==$dt->approved1)$tugas='approved1';
            elseif($id_employee==$dt->approved2)$tugas='approved2';
            elseif($id_employee==$dt->legalized)$tugas='legalized';
        }

       $qty_new=DB::table('tb_overtime_spv')->where('legalized_status','0')
        ->Where(function($query) use($id_employee){
            $query->where('assigned',$id_employee)
            ->orWhere('conducted',$id_employee)
            ->orWhere('approved1',$id_employee)
            ->orWhere('approved2',$id_employee)
            ->orWhere('legalized',$id_employee);
        })
       ->count();

       $tb_assigment=DB::table('tb_overtime_spv')
       ->leftjoin('tb_employees','tb_employees.id','=','tb_overtime_spv.id_employee')
       ->where('legalized_status','1')
       ->where('ot_date','>=',$awal)
       ->where('ot_date','<=',$akhir)
       ->Where(function($query) use($id_employee){
           $query->where('assigned',$id_employee)
           ->orWhere('conducted',$id_employee)
           ->orWhere('approved1',$id_employee)
           ->orWhere('approved2',$id_employee)
           ->orWhere('legalized',$id_employee);
       })
       ->get(['tb_overtime_spv.*','tb_employees.NIK','tb_employees.employee_name']);
       $tugas='';
       foreach($tb_assigment_new as $dt){
           if($id_employee==$dt->assigned)$tugas.='assigned';
           elseif($id_employee==$dt->conducted)$tugas.='conducted';
           elseif($id_employee==$dt->approved1)$tugas.='approved1';
           elseif($id_employee==$dt->approved2)$tugas.='approved2';
           elseif($id_employee==$dt->legalized)$tugas.='legalized';
       }
       //return $cek;

       return view('page/admin/m_assigment/approvals',['tb_assigment_new'=>$tb_assigment_new,'tb_assigment'=>$tb_assigment,'tugas'=>$tugas,'qty_new'=>$qty_new,'periode'=>$periode,'thn_awal'=>$thn_awal,'id_employee'=>$id_employee,'limit_approval'=>$limit_approval,'limit_day'=>$limit_day,'menu'=>'assigment']);
    }
    function sign($id,$status){
        $now=date('Y-m-d H:i:s');
        $tgl=$status.'_date';
        $update=DB::table('tb_overtime_spv')->where('id',$id)->update([$status=>'1',$tgl=>$now]);
        // $update=DB::table('tb_overtime_spv')->where('id',$id)->update([$status=>'1']);
        if($update){
            //$send_whatsapp=$this->notificationAssigment($id);
            return redirect()->back()->with(['success' => 'Sign Form Berhasil']);
        }
    }
    function realisations($periode){
        date_default_timezone_set("Asia/Jakarta");
        $kalendar=CAL_GREGORIAN;
        if($periode==0)$periode=date('Y-m');
        $thn=date('Y',strtotime($periode.'-01'));
        $bln=date('m',strtotime($periode.'-01'));
        $hariakhir=cal_days_in_month($kalendar,$bln,$thn);

        $awal=date('Y-m-d',strtotime($periode.'-01'));
        $akhir=date('Y-m-d',strtotime($periode.'-'.$hariakhir));

        $admin=Auth::user()->name;
        //return $admin;

        $tb_assigment_new=DB::table('tb_overtime_spv')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_overtime_spv.id_employee')
        ->where('hours_act','0')
        ->Where('admin',$admin)
        //->where('admin', 'like', '%'.$admin.'%')
        ->where('isDelete','0')
        ->get(['tb_overtime_spv.*','tb_employees.NIK','tb_employees.employee_name','tb_employees.PIN','tb_employees.badgenumber']);
        //return $admin;

        $qty_new=DB::table('tb_overtime_spv')->where('legalized_status','0')->Where('admin',$admin)->count();

        $tb_assigment=DB::table('tb_overtime_spv')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_overtime_spv.id_employee')
        //->where([['hours_act','>','0'],['admin',$admin],['ot_date','>=',$awal],['ot_date','<=',$akhir]])
        ->where([['admin',$admin],['ot_date','>=',$awal],['ot_date','<=',$akhir]])
        ->get(['tb_overtime_spv.*','tb_employees.NIK','tb_employees.employee_name','tb_employees.PIN']);

       return view('page/admin/m_assigment/realisations',['tb_assigment_new'=>$tb_assigment_new,'tb_assigment'=>$tb_assigment,'qty_new'=>$qty_new,'periode'=>$periode,'menu'=>'assigment']);
    }
    function update(Request $data){
        $admin=Auth::user()->name;
        $start_act=Carbon::parse($data->start_act)->format('Y-m-d H:i:s');
        $finish_act=Carbon::parse($data->finish_act)->format('Y-m-d H:i:s');
        if($data->kondisi=='1'){
            $update=DB::table('tb_overtime_spv')->where('id',$data->idform)->update([
                'start_act'=>$start_act,
                'finish_act'=>$finish_act,
                'hours_act'=>$data->hours_act,
                'isCompleted'=>'1'
            ]);
            if($update)
            return redirect()->back()->with(['success' => 'Confirm Realisation Berhasil']); 
            else echo "Error";
        }
        elseif($data->kondisi=='2'){
            $update=DB::table('tb_overtime_spv')->where('id',$data->idform)->update([
                'isDelete'=>'1',
                'admin'=>$admin
            ]);
            if($update)
            return redirect()->back()->with(['success' => 'Cancel Form Berhasil']); 
            else echo "Error";
        }
    }
    function confirm($id){
        $tb_spv=DB::table('tb_overtime_spv')->where('id',$id)->get();
        foreach($tb_spv as $dt){
            $update=DB::table('tb_overtime_spv')->where('id',$id)->update([
                'start_act'=>$dt->start_plan,
                'finish_act'=>$dt->finish_plan,
                'hours_act'=>$dt->hours_plan,
                'isCompleted'=>'1'
            ]);
        }
        return redirect()->back()->with(['success' => 'Confirm Realisation Berhasil']); 
    }
    function verification($periode){
        date_default_timezone_set("Asia/Jakarta");
        $kalendar=CAL_GREGORIAN;
        if($periode==0)$periode=date('Y-m');
        $thn=date('Y',strtotime($periode.'-01'));
        $bln=date('m',strtotime($periode.'-01'));
        $hariakhir=cal_days_in_month($kalendar,$bln,$thn);

        $awal=date('Y-m-d',strtotime($periode.'-01'));
        $akhir=date('Y-m-d',strtotime($periode.'-'.$hariakhir));
        $admin=Auth::user()->name;

        $tb_assigment_new=DB::table('tb_overtime_spv')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_overtime_spv.id_employee')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->Where('tb_overtime_spv.isVerified','0')
        ->where('tb_overtime_spv.isDelete','0')
        ->get(['tb_overtime_spv.*','tb_employees.NIK','tb_employees.employee_name','tb_employees.PIN','tb_employees.badgenumber','tb_departments.dept_code']);

        $qty_new=DB::table('tb_overtime_spv')->where('isVerified','0')->where('isDelete','0')->count();

        $tb_assigment=DB::table('tb_overtime_spv')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_overtime_spv.id_employee')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->Where('tb_overtime_spv.isVerified','1')
        ->where('tb_overtime_spv.isDelete','0')
        ->where('ot_date','>=',$awal)
        ->where('ot_date','<=',$akhir)
        ->get(['tb_overtime_spv.*','tb_employees.NIK','tb_employees.employee_name','tb_employees.PIN','tb_employees.badgenumber','tb_departments.dept_code']);

       return view('page/admin/m_assigment/verification',['tb_assigment_new'=>$tb_assigment_new,'tb_assigment'=>$tb_assigment,'qty_new'=>$qty_new,'periode'=>$periode,'menu'=>'assigment']);
    }
    function capture_finger(){
        $tb_assigment_new=DB::table('tb_overtime_spv')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_overtime_spv.id_employee')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->Where('tb_overtime_spv.isVerified','0')
        ->where('tb_overtime_spv.isDelete','0')
        ->whereNULL('tb_overtime_spv.checkin')
        ->get(['tb_overtime_spv.*','tb_employees.NIK','tb_employees.employee_name','tb_employees.PIN','tb_employees.badgenumber','tb_departments.dept_code']);
        foreach($tb_assigment_new as $dt){
            //if($dt->checkin==''||$dt->checkout==''){
                $checkin_act = '';
                $checkout_act = '';
                $lenbadge = strlen($dt->badgenumber);
                $nullbadge = 9 - $lenbadge;
                $j = '';
                for ($i = 1; $i <= $nullbadge; $i++) {
                    $j .= '0';
                }
                $badge = $j . $dt->badgenumber;
                
                $ncdatein = $dt->start_act;
                //Reduce 2 Hour
                $date = date_create($ncdatein);
                date_add($date, date_interval_create_from_date_string('-10 hours'));
                $ncdateindown = date_format($date, 'Y-m-d H:i:s');
                //Increas 5 Hour
                $date = date_create($ncdatein);
                date_add($date, date_interval_create_from_date_string('5 hours'));
                $ncdateinup = date_format($date, 'Y-m-d H:i:s');
                //echo $ncdatein.' ';
                
                $ncdateout = $dt->finish_act;
                //Reduce 2 Hour
                $date = date_create($ncdateout);
                date_add($date, date_interval_create_from_date_string('-2 hours'));
                $ncdateoutdown = date_format($date, 'Y-m-d H:i:s');
                //Increas 5 Hour
                $date = date_create($ncdateout);
                date_add($date, date_interval_create_from_date_string('10 hours 30 minutes'));
                $ncdateoutup = date_format($date, 'Y-m-d H:i:s');
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
                if ($checkin_act == '') {
                    $qry7=DB::table('tb_checktimes')->where('NIK',$dt->NIK)->where('checktime','>=',$ncdateindown)->where('checktime','<=',$ncdateinup)->orderby('checktime','asc')->limit(1)->get();
                    foreach($qry7 as $dt7){
                        $checkin_act=$dt7->checktime;
                        $in_finger=0;
                    }
                }
                if ($checkout_act == '') {
                    $qry7=DB::table('tb_checktimes')->where('NIK',$dt->NIK)->where('checktime','>=',$ncdateoutdown)->where('checktime','<=',$ncdateoutup)->orderby('checktime','desc')->limit(1)->get();
                    foreach($qry7 as $dt7){
                        $checkout_act=$dt7->checktime;
                        $out_finger=0;
                    }
                }
                //Absen Manual End
                if($checkin_act!=''){
                    $update=DB::table('tb_overtime_spv')->where('id',$dt->id)->update([
                        'checkin'=>$checkin_act,
                        'in_finger'=>$in_finger
                    ]);
                }
                if($checkout_act!=''){
                    $update=DB::table('tb_overtime_spv')->where('id',$dt->id)->update([
                        'checkout'=>$checkout_act,
                        'out_finger'=>$out_finger
                    ]);
                }

            //}
        }
        return redirect()->back();
    }
    function verify($id){
        //Add Calculation
            $pas=0;
            $tb1=DB::table('tb_overtime_spv')->where('id',$id)->get();
            foreach($tb1 as $dt){

                $Hari = date('w', strtotime($dt->finish_act));
                $finish_act=date('Y-m-d', strtotime($dt->finish_act));
                $tb_freedays = DB::table('tb_freedays')
                    ->where('date_off', $finish_act)
                    ->count();
                $from = $dt->start_act;
                $to = $dt->finish_act;
                
                $total = strtotime($to) - strtotime($from);
                $hours = floor($total / 60 / 60);
                $minutes = round(($total - $hours * 60 * 60) / 60);
                if ($Hari == 6 || $Hari == 0 || $tb_freedays > 0) {
                    $insentif = 'insentif_sh';
                } else {
                    //if ($dt->position_index <= 4) {
                        $insentif = 'insentif_wd';
                    //}
                }
                $total_bayar = '';
                // $host = mysqli_connect('192.168.1.4', 'ems', '123456', 'db_ems');
                // ($qry = mysqli_query($host, "select * from tb_utilities where atribut='$insentif'")) or die(mysqli_error($host));
                // while ($dt_insentif = mysqli_fetch_array($qry)) {
                $qry=DB::table('tb_utilities')->where('atribut',$insentif)->get(['status']);
                foreach($qry as $dt_insentif){
                    $total_bayar=0;
                    if( $Hari == 6 || $Hari == 0 || $tb_freedays > 0){
                        if($dt->hours_act>=4){
                            $total_bayar = $dt_insentif->status;
                            $pas=$total_bayar;
                        }else{
                            $pas=0;
                        }
                        $x=0;
                    } else {
                        //if ($dt->position_index <= 4) {
                            //if (round($dt->hours_act, 1) >=4) {
                            if ($hours >=4) {
                                $total_bayar = $dt_insentif->status * 4;
                            //}else if (round($dt->hours_act, 1) >=3) {
                            }else if ($hours >=3) {
                                //} else {
                                $x=1;
                                $total_bayar = $dt_insentif->status*3;
                            //}else if (round($dt->hours_act, 1) >=2) {
                            }else if ($hours >=2) {
                                //} else {
                                $x=1;
                                $total_bayar = $dt_insentif->status*2;
                            }else{
                            //if (round($hours, 1) >= 4) {
                                $x=2;
                                $total_bayar = 0;
                            }
                            $pas=$total_bayar;
                        //}
                        
                    }
                }


            }
            //return $hours;
        //End calculation
        if($pas>0){
            $update=DB::table('tb_overtime_spv')->where('id',$id)->update([
                'isVerified'=>'1',
                'amount'=>$pas,
            ]);
        }
        return redirect()->back()->with(['success' => 'Success Verified']); 
    }
    function verified(Request $data){
        $admin=Auth::user()->name;
        if($data->kondisi=='1'){
            //Add Calculation
                $pas=0;
                $tb1=DB::table('tb_overtime_spv')->where('id',$data->idform)->get();
                foreach($tb1 as $dt){

                    $Hari = date('w', strtotime($dt->finish_act));
                    $finish_act=date('Y-m-d', strtotime($dt->finish_act));
                    $tb_freedays = DB::table('tb_freedays')
                        ->where('date_off', $finish_act)
                        ->count();
                    $from = $dt->start_act;
                    $to = $dt->finish_act;
                    
                    $total = strtotime($to) - strtotime($from);
                    $hours = floor($total / 60 / 60);
                    $minutes = round(($total - $hours * 60 * 60) / 60);
                    if ($Hari == 6 || $Hari == 0 || $tb_freedays > 0) {
                        $insentif = 'insentif_sh';
                    } else {
                        // if ($dt->position_index <= 4) {
                            $insentif = 'insentif_wd';
                        // }
                    }
                    $total_bayar = '';
                    // $host = mysqli_connect('192.168.1.4', 'ems', '123456', 'db_ems');
                    // ($qry = mysqli_query($host, "select * from tb_utilities where atribut='$insentif'")) or die(mysqli_error($host));
                    // while ($dt_insentif = mysqli_fetch_array($qry)) {
                    $qry=DB::table('tb_utilities')->where('atribut',$insentif)->get(['status']);
                    foreach($qry as $dt_insentif){
                        //if ($Hari < 6 || $Hari != 0 || $tb_freedays == 0) {
                        $total_bayar=0;
                        if( $Hari == 6 || $Hari == 0 || $tb_freedays > 0){
                            if($dt->hours_act>=4){
                                $total_bayar = $dt_insentif->status;
                                $pas=$total_bayar;
                            }else{
                                $pas=0;
                            }
                            $x=0;
                        } else {
                            // if ($dt->position_index <= 4) {
                                //if (round($dt->hours_act, 1) >=4) {
                                if ($hours >=4) {
                                    $total_bayar = $dt_insentif->status * 4;
                                //}else if (round($dt->hours_act, 1) >=3) {
                                }else if ($hours >=3) {
                                    //} else {
                                    $x=1;
                                    $total_bayar = $dt_insentif->status*3;
                                //}else if (round($dt->hours_act, 1) >=2) {
                                }else if ($hours >=2) {
                                    //} else {
                                    $x=1;
                                    $total_bayar = $dt_insentif->status*2;
                                }else{
                                //if (round($hours, 1) >= 4) {
                                    $x=2;
                                    $total_bayar = 0;
                                }
                                $pas=$total_bayar;
                            // }
                            
                        }
                    }


                }
            //End calculation
            $start_act=Carbon::parse($data->start_act)->format('Y-m-d H:i:s');
            $finish_act=Carbon::parse($data->finish_act)->format('Y-m-d H:i:s');
            $update=DB::table('tb_overtime_spv')->where('id',$data->idform)->update([
                'start_act'=>$start_act,
                'finish_act'=>$finish_act,
                'hours_act'=>$data->hours_act,
                'isVerified'=>'1',
                'amount'=>$pas,
            ]);
        }
        elseif($data->kondisi=='2'){
            $update=DB::table('tb_overtime_spv')->where('id',$data->idform)->update([
                'isDelete'=>'1',
                'admin'=>$admin
            ]);
        }
        return redirect()->back()->with(['success' => 'Confirm Realisation Berhasil']);
    }
    
    public function notificationAssigment($id){
        $tb_assignment=DB::table('tb_overtime_spv')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_overtime_spv.id_employee')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->where('tb_overtime_spv.id',$id)
        ->get(['tb_overtime_spv.*','tb_employees.employee_name','tb_departments.dept_name']);
        $data['kontak']='';
        $data['pesan']='';
        $id_employee='';
        foreach($tb_assignment as $dt){
            $id_assignment=$dt->id;
            $ot_date=$dt->ot_date;
            $dept=$dt->dept_name;
            if($dt->assigned_status==0){
                $id_employee=$dt->assigned;
                $pos='Atasan Pertama';
            }else if($dt->approved1!=null&&$dt->approved1_status==0){
                $id_employee=$dt->approved1;
                $pos='Atasan Kedua';
            }else if($dt->approved2!=null&&$dt->approved2_status==0){
                $id_employee=$dt->approved2;
                $pos='Atasan Ketiga';
            }else if($dt->conducted_status==0){
                $id_employee=$dt->conducted;
                $pos='Employee';
            }
            $name=$dt->employee_name;
            if($id_employee!=''){
                $data['kontak']=DB::table('tb_employee_detail')->where('id_employee',$id_employee)->value('nomor_telepon');
                $data['pesan']="*NOTIFIKASI ASSIGNMENT*\n\nID: *$id_assignment*\nTanggal: *$ot_date*\nNama: *$name*\nDepartemen: *$dept*\n\nMenunggu Approval Anda sebagai *$pos*.\n\nSegera lakukan pengecekan via EMS, klik link berikut:\nhttps://ems.summitadyawinsa.co.id/EMS/Assigment/Approvals/0";
            }
        }
        //$data['kontak']='081290431457';
        if($data['kontak']!=''){
            // \App\Http\Controllers\WhatsAppController::sendInternalMessage($data['kontak'], $data['pesan']);
            // \App\Http\Controllers\WuzapiController::sendInternalMessage('081324982872', $data['pesan']);
            \App\Http\Controllers\WuzapiController::sendInternalMessage($data['kontak'], $data['pesan']);
            return 'Success';
        }else{
            return 'Failed';
        }
    }



    function checkFreedays(Request $data){
        $tb_freedays=DB::connection('mysql')->table('tb_freedays')->where('date_off',$data->date_off)->count();
        $hari=date('w',strtotime($data->date_off));
        if($hari==0||$hari==6||$tb_freedays==1)$assigment_status=1;
        else $assigment_status=0;
        return $assigment_status;
    }

    public function CheckDaysnPosition(Request $request){
        $id_employee = $request->idemployee;
        $Position = DB::table('tb_employees as a')
        ->leftjoin('tb_positions as b','b.id','=','a.position_id')
        ->where('a.id',$id_employee)
        ->get();
        foreach($Position as $d){
            $id_position = $d->position_index;

        }
        $tb_freedays= DB::connection('mysql')->table('tb_freedays')
        ->where('date_off',$request->date_off)
        ->count();
        $hari= date('w',strtotime($request->date_off));
        
            $data["assigment_status"] = 1;
        return $data;
    }

}
