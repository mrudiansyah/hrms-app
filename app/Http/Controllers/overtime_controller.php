<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DateTime;
use Auth;
use PDF;

class overtime_controller extends Controller
{
    public function __construct(){
        $this->middleware(['auth','verified']);
    }
    function assigment($periode){
        date_default_timezone_set("Asia/Jakarta");
        $kalendar=CAL_GREGORIAN;
        $sekarang=date('Y-m-d H:I:s');
        if($periode==0)$periode=date('Y-m');
        $thn=date('Y',strtotime($periode.'-01'));
        $bln=date('m',strtotime($periode.'-01'));
        $hariakhir=cal_days_in_month($kalendar,$bln,$thn);


        $awal=date('Y-m-d',strtotime($periode.'-01'));
        $akhir=date('Y-m-d',strtotime($periode.'-'.$hariakhir));
        $admin=Auth::user()->name;

        $min=DB::table('tb_overtime_spv')->orderby('ot_date','asc')->limit(1)->get();
        $thn_awal=date('Y');
        foreach($min as $row){
            $thn_awal=date('Y',strtotime($row->ot_date));
        }

        
        $email=Auth::user()->email;
        $tb_email=DB::table('tb_emails')->where('email_address',$email)->get();
        //return $tb_assigment;
        $department_ids=[];
        foreach($tb_email as $dt){
            $id_employee=$dt->id_employee;
            $tb_admin=DB::table('tb_admins')->leftjoin('tb_departments','tb_departments.id','=','tb_admins.dept_id')->where('id_employee',$id_employee)->where('tb_departments.isDelete',0)->orderby('tb_departments.dept_code','asc')->get(['tb_admins.*','tb_departments.dept_code','tb_departments.dept_name']);
            foreach($tb_admin as $dt2){
                $department_ids[]=$dt2->dept_id;
            }
        }
        $tb_assigment=DB::table('tb_overtime_spv')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_overtime_spv.id_employee')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->where('tb_overtime_spv.isVerified','1')
        ->where('tb_overtime_spv.isDelete','0')
        ->where('tb_overtime_spv.ot_date','>=',$awal)
        ->where('tb_overtime_spv.ot_date','<=',$akhir)
        ->where(function($query) use($department_ids){
            $query->where('tb_employees.dept_id','0');
            if(!empty($department_ids))$query->orWhereIn('tb_employees.dept_id',array_unique($department_ids));
        });

        $tb_assigment=$tb_assigment->get(['tb_overtime_spv.*','tb_employees.NIK','tb_employees.employee_name','tb_employees.PIN','tb_employees.dept_id','tb_departments.dept_code','tb_positions.position_index','tb_positions.position_name']);
        $finish_dates=$tb_assigment->map(function($assignment){
            return date('Y-m-d',strtotime($assignment->finish_act));
        })->unique()->values();
        $free_days=DB::table('tb_freedays')
            ->whereIn('date_off',$finish_dates)
            ->pluck('date_off')
            ->countBy();
        $incentive_attributes=[];
        foreach($tb_assigment as $assignment){
            $finish_timestamp=strtotime($assignment->finish_act);
            $finish_date=date('Y-m-d',$finish_timestamp);
            $day=date('w',$finish_timestamp);
            $is_free_day=isset($free_days[$finish_date]);
            if($day==6||$day==0||$is_free_day){
                $attribute=$assignment->position_index<=4?'insentif_sh':'insentif_dh';
            }elseif($assignment->position_index<=4){
                $attribute='insentif_wd';
            }else{
                $attribute=null;
            }
            $assignment->incentive_attribute=$attribute;
            if($attribute!==null)$incentive_attributes[]=$attribute;
        }
        $incentive_rates=DB::table('tb_utilities')
            ->whereIn('atribut',array_unique($incentive_attributes))
            ->pluck('status','atribut');
        foreach($tb_assigment as $assignment){
            $attribute=$assignment->incentive_attribute;
            $assignment->assignment_amount='';
            if($attribute===null||!isset($incentive_rates[$attribute]))continue;
            $rate=$incentive_rates[$attribute];
            $finish_timestamp=strtotime($assignment->finish_act);
            $day=date('w',$finish_timestamp);
            $is_free_day=isset($free_days[date('Y-m-d',$finish_timestamp)]);
            if($day==6||$day==0||$is_free_day){
                $assignment->assignment_amount=$assignment->hours_act>=4?number_format($rate,0):'Check';
            }else{
                $hours=floor((strtotime($assignment->finish_act)-strtotime($assignment->start_act))/3600);
                if($hours>=4)$assignment->assignment_amount=number_format($rate*4,0);
                elseif($hours>=3)$assignment->assignment_amount=number_format($rate*3,0);
                elseif($hours>=2)$assignment->assignment_amount=number_format($rate*2,0);
                else $assignment->assignment_amount=0;
            }
        }

        return view('page/user/m_overtime/assignment',['tb_assigment'=>$tb_assigment,'periode'=>$periode,'thn_awal'=>$thn_awal,'menu'=>'report_assigment']);
    }


    function index(){
        date_default_timezone_set("Asia/Jakarta");
        $kalendar=CAL_GREGORIAN;
        $thn=date('Y');
        $bln=date('m');
        $hariakhir=cal_days_in_month($kalendar,$bln,$thn);

        $Tglawal=date('Y-m-d',strtotime($thn.'-'.$bln.'-01'));
        $Tglakhir=date('Y-m-d',strtotime($thn.'-'.$bln.'-'.$hariakhir));

        $email=Auth::user()->email;
        $tb_email=tb_email::where('email_address',$email)->get();
        $dept_id='';
        $tb_overtime_detail=DB::table('tb_overtime_details')
        ->leftjoin('tb_overtimes','tb_overtimes.id','=','tb_overtime_details.id_ot')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_overtime_details.id_employee')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_reason_ots','tb_reason_ots.reason_ot','=','tb_overtime_details.reason_ot')
        ->where([['date_on','>=',$Tglawal],['date_on','<=',$Tglakhir],['tb_overtimes.dept_id','0'],['tb_overtime_details.status','<','90']]);
  
        foreach($tb_email as $dt){
            $id_employee=$dt->id_employee;
            $tb_admin=DB::table('tb_admins')->leftjoin('tb_departments','tb_departments.id','=','tb_admins.dept_id')->where('id_employee',$id_employee)->where('tb_departments.isDelete',0)->orderby('tb_departments.dept_code','asc')->get(['tb_admins.*','tb_departments.dept_code','tb_departments.dept_name']);
            foreach($tb_admin as $dt2){
                $dept_id=$dt2->dept_id;
                $tb_overtime_detail=$tb_overtime_detail->orWhere([['date_on','>=',$Tglawal],['date_on','<=',$Tglakhir],['tb_overtimes.dept_id',$dept_id],['tb_overtime_details.status','<','90']]);
            }
        }

        $tb_overtime_detail=$tb_overtime_detail->get(['tb_overtime_details.*','tb_overtimes.dept_name','tb_overtimes.status_paid','tb_employees.NIK','tb_employees.employee_name','tb_reason_ots.category','tb_reason_ots.detail_category','tb_departments.dept_code']);
        
        if($dept_id!=''){
            $tb_employee=DB::table('tb_employees')->leftjoin('tb_admins','tb_admins.dept_id','=','tb_employees.dept_id')->where('tb_admins.id_employee',$id_employee)->orderby('tb_employees.employee_name','asc')->get(['tb_employees.*']);
        }else{
            $tb_employee=DB::table('tb_employees')->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')->where('tb_employees.id',$id_employee)->get(['tb_employees.*','tb_departments.dept_code','tb_departments.dept_name']);
        }
        //return $tb_overtime_detail;

        return view('page/user/m_overtime/overtime',['tb_overtime_detail'=>$tb_overtime_detail,'Tglawal'=>$Tglawal,'Tglakhir'=>$Tglakhir,'tb_admin'=>$tb_admin,'tb_employee'=>$tb_employee,'menu'=>'overtimes']);
    }
    function OTCancel(){
        date_default_timezone_set("Asia/Jakarta");
        $kalendar=CAL_GREGORIAN;
        $thn=date('Y');
        $bln=date('m');
        $hariakhir=cal_days_in_month($kalendar,$bln,$thn);

        $Tglawal=date('Y-m-d',strtotime($thn.'-'.$bln.'-01'));
        $Tglakhir=date('Y-m-d',strtotime($thn.'-'.$bln.'-'.$hariakhir));

        $email=Auth::user()->email;
        $tb_email=tb_email::where('email_address',$email)->get();
        $dept_id='';
        $tb_overtime_detail=DB::table('tb_overtime_details')
        ->leftjoin('tb_overtimes','tb_overtimes.id','=','tb_overtime_details.id_ot')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_overtime_details.id_employee')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_reason_ots','tb_reason_ots.reason_ot','=','tb_overtime_details.reason_ot')
        ->where([['date_on','>=',$Tglawal],['date_on','<=',$Tglakhir],['tb_overtimes.dept_id','0'],['tb_overtime_details.status','96']]);
  
        foreach($tb_email as $dt){
            $id_employee=$dt->id_employee;
            $tb_admin=DB::table('tb_admins')->leftjoin('tb_departments','tb_departments.id','=','tb_admins.dept_id')->where('id_employee',$id_employee)->where('tb_departments.isDelete',0)->orderby('tb_departments.dept_code','asc')->get(['tb_admins.*','tb_departments.dept_code','tb_departments.dept_name']);
            foreach($tb_admin as $dt2){
                $dept_id=$dt2->dept_id;
                $tb_overtime_detail=$tb_overtime_detail->orWhere([['date_on','>=',$Tglawal],['date_on','<=',$Tglakhir],['tb_overtimes.dept_id',$dept_id],['tb_overtime_details.status','96']]);
            }
        }

        $tb_overtime_detail=$tb_overtime_detail->get(['tb_overtime_details.*','tb_overtimes.dept_name','tb_overtimes.status_paid','tb_employees.NIK','tb_employees.employee_name','tb_reason_ots.category','tb_reason_ots.detail_category','tb_departments.dept_code']);
        
        if($dept_id!=''){
            $tb_employee=DB::table('tb_employees')->leftjoin('tb_admins','tb_admins.dept_id','=','tb_employees.dept_id')->where('tb_admins.id_employee',$id_employee)->orderby('tb_employees.employee_name','asc')->get(['tb_employees.*']);
        }else{
            $tb_employee=DB::table('tb_employees')->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')->where('tb_employees.id',$id_employee)->get(['tb_employees.*','tb_departments.dept_code','tb_departments.dept_name']);
        }
        //return $tb_overtime_detail;

        return view('page/user/m_overtime/overtime_cancel',['tb_overtime_detail'=>$tb_overtime_detail,'Tglawal'=>$Tglawal,'Tglakhir'=>$Tglakhir,'tb_admin'=>$tb_admin,'tb_employee'=>$tb_employee,'menu'=>'overtimes']);
    }
    function reportOT(Request $data){
        $dept_id=$data->dept_id;
        $konten='<option></option>';
        $tb_employee=DB::table('tb_employees')->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')->where('dept_id',$dept_id)->where('tb_positions.position_index','<','4')->orderby('employee_name','asc')->get('tb_employees.*');
        foreach($tb_employee as $dt){
            $konten.="<option value='".$dt->id."'>".$dt->employee_name."</option>";
        }
        return $konten;
    }
    function submitOT(Request $data){
        $id_employee=$data->id_employee;
        $dept_id=$data->dept_id;

        $Tglawal=date('Y-m-d',strtotime($data->ot_start));
        $Tglakhir=date('Y-m-d',strtotime($data->ot_finish));

        $email=Auth::user()->email;
        $tb_email=tb_email::where('email_address',$email)->get();
        foreach($tb_email as $dt){
            $id_user=$dt->id_employee;
            $tb_admin=DB::table('tb_admins')->leftjoin('tb_departments','tb_departments.id','=','tb_admins.dept_id')->where('id_employee',$id_user)->where('tb_departments.isDelete',0)->orderby('tb_departments.dept_code','asc')->get(['tb_admins.*','tb_departments.dept_code','tb_departments.dept_name']);
        }

        $tb_overtime_detail=DB::table('tb_overtime_details')
        ->leftjoin('tb_overtimes','tb_overtimes.id','=','tb_overtime_details.id_ot')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_overtime_details.id_employee')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_reason_ots','tb_reason_ots.reason_ot','=','tb_overtime_details.reason_ot');

        if($id_employee!=''){
            $tb_overtime_detail=$tb_overtime_detail->where([['date_on','>=',$Tglawal],['date_on','<=',$Tglakhir],['tb_overtime_details.id_employee',$id_employee],['tb_overtime_details.status','<','90']]);
        }
        else if($dept_id!=''){
            $tb_overtime_detail=$tb_overtime_detail->where([['date_on','>=',$Tglawal],['date_on','<=',$Tglakhir],['tb_overtimes.dept_id',$dept_id],['tb_overtime_details.status','<','90']]);
        }
        else{
            $tb_overtime_detail=$tb_overtime_detail->where([['date_on','>=',$Tglawal],['date_on','<=',$Tglakhir],['tb_overtimes.dept_id','0'],['tb_overtime_details.id_employee','0']]);
            foreach($tb_admin as $dt2){
                $dept_id=$dt2->dept_id;
                $tb_overtime_detail=$tb_overtime_detail->orWhere([['date_on','>=',$Tglawal],['date_on','<=',$Tglakhir],['tb_overtimes.dept_id',$dept_id],['tb_overtime_details.status','<','90']]);
            }
        }
  
        $tb_overtime_detail=$tb_overtime_detail->get(['tb_overtime_details.*','tb_overtimes.dept_name','tb_overtimes.status_paid','tb_employees.NIK','tb_employees.employee_name','tb_reason_ots.category','tb_reason_ots.detail_category','tb_departments.dept_code']);
        
        if($dept_id!=''){
            $tb_employee=DB::table('tb_employees')->leftjoin('tb_admins','tb_admins.dept_id','=','tb_employees.dept_id')->where('tb_admins.id_employee',$id_employee)->orderby('tb_employees.employee_name','asc')->get(['tb_employees.*']);
        }else{
            $tb_employee=DB::table('tb_employees')->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')->where('tb_employees.id',$id_employee)->get(['tb_employees.*','tb_departments.dept_code','tb_departments.dept_name']);
        }
        //return $tb_overtime_detail;
        return view('page/user/m_overtime/overtime',['tb_overtime_detail'=>$tb_overtime_detail,'Tglawal'=>$Tglawal,'Tglakhir'=>$Tglakhir,'tb_admin'=>$tb_admin,'tb_employee'=>$tb_employee,'menu'=>'overtimes']);

    }
    function submitOTCancel(Request $data){
        $id_employee=$data->id_employee;
        $dept_id=$data->dept_id;

        $Tglawal=date('Y-m-d',strtotime($data->ot_start));
        $Tglakhir=date('Y-m-d',strtotime($data->ot_finish));

        $email=Auth::user()->email;
        $tb_email=tb_email::where('email_address',$email)->get();
        foreach($tb_email as $dt){
            $id_user=$dt->id_employee;
            $tb_admin=DB::table('tb_admins')->leftjoin('tb_departments','tb_departments.id','=','tb_admins.dept_id')->where('id_employee',$id_user)->where('tb_departments.isDelete',0)->orderby('tb_departments.dept_code','asc')->get(['tb_admins.*','tb_departments.dept_code','tb_departments.dept_name']);
        }

        $tb_overtime_detail=DB::table('tb_overtime_details')
        ->leftjoin('tb_overtimes','tb_overtimes.id','=','tb_overtime_details.id_ot')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_overtime_details.id_employee')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_reason_ots','tb_reason_ots.reason_ot','=','tb_overtime_details.reason_ot');

        if($id_employee!=''){
            $tb_overtime_detail=$tb_overtime_detail->where([['date_on','>=',$Tglawal],['date_on','<=',$Tglakhir],['tb_overtime_details.id_employee',$id_employee],['tb_overtime_details.status','96']]);
        }
        else if($dept_id!=''){
            $tb_overtime_detail=$tb_overtime_detail->where([['date_on','>=',$Tglawal],['date_on','<=',$Tglakhir],['tb_overtimes.dept_id',$dept_id],['tb_overtime_details.status','96']]);
        }
        else{
            $tb_overtime_detail=$tb_overtime_detail->where([['date_on','>=',$Tglawal],['date_on','<=',$Tglakhir],['tb_overtimes.dept_id','0'],['tb_overtime_details.id_employee','0']]);
            foreach($tb_admin as $dt2){
                $dept_id=$dt2->dept_id;
                $tb_overtime_detail=$tb_overtime_detail->orWhere([['date_on','>=',$Tglawal],['date_on','<=',$Tglakhir],['tb_overtimes.dept_id',$dept_id],['tb_overtime_details.status','96']]);
            }
        }
  
        $tb_overtime_detail=$tb_overtime_detail->get(['tb_overtime_details.*','tb_overtimes.dept_name','tb_overtimes.status_paid','tb_employees.NIK','tb_employees.employee_name','tb_reason_ots.category','tb_reason_ots.detail_category','tb_departments.dept_code']);
        
        if($dept_id!=''){
            $tb_employee=DB::table('tb_employees')->leftjoin('tb_admins','tb_admins.dept_id','=','tb_employees.dept_id')->where('tb_admins.id_employee',$id_employee)->orderby('tb_employees.employee_name','asc')->get(['tb_employees.*']);
        }else{
            $tb_employee=DB::table('tb_employees')->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')->where('tb_employees.id',$id_employee)->get(['tb_employees.*','tb_departments.dept_code','tb_departments.dept_name']);
        }
        //return $tb_overtime_detail;
        return view('page/user/m_overtime/overtime_cancel',['tb_overtime_detail'=>$tb_overtime_detail,'Tglawal'=>$Tglawal,'Tglakhir'=>$Tglakhir,'tb_admin'=>$tb_admin,'tb_employee'=>$tb_employee,'menu'=>'overtimes']);

    }
    function employeeOT($periode,$dept_id){
        date_default_timezone_set("Asia/Jakarta");
        $kalendar=CAL_GREGORIAN;

        $data_a=explode('-',$periode);
        $thn=$data_a[0];
        $bln=$data_a[1];

        //$thn=date('Y');
        //$bln=date('m');
        $hariakhir=cal_days_in_month($kalendar,$bln,$thn);

        $Tglawal=date('Y-m-d',strtotime($thn.'-'.$bln.'-01'));
        $Tglakhir=date('Y-m-d',strtotime($thn.'-'.$bln.'-'.$hariakhir));
        $periode=date('Y-m',strtotime($Tglawal));
     
        $email=Auth::user()->email;
        $tb_email=tb_email::where('email_address',$email)->get();
        foreach($tb_email as $dt){
            $id_employee=$dt->id_employee;
        }

        $tb_sumot=DB::table('tb_overtime_details')
        ->leftjoin('tb_overtimes','tb_overtimes.id','=','tb_overtime_details.id_ot')
        ->select('tb_overtimes.dept_id','tb_overtimes.dept_name', DB::raw('SUM(hours_act) as total_act,SUM(hours_convertion) as total_convertion,sum(ammount) as total_ammount'))
        ->groupBy('dept_id','dept_name')
        //->where('tb_overtimes.status_approve','1')
        //->where('tb_overtime_details.status','6')
        ->where('tb_overtime_details.status','<','90')
        ->where('date_on','>=',$Tglawal)
        ->where('date_on','<=',$Tglakhir)
        ->whereExists(function ($query) use($id_employee){
            $query->select(DB::raw(1))
                  ->from('tb_admins')
                  ->whereColumn('tb_admins.dept_id', 'tb_overtimes.dept_id')
                  ->where('tb_admins.id_employee',$id_employee);
        })
        ->orderby('total_convertion','desc')
        ->get();
        
        $tb_sumot_b=DB::table('tb_overtime_details')
        ->leftjoin('tb_overtimes','tb_overtimes.id','=','tb_overtime_details.id_ot')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_overtime_details.id_employee')
        ->select('tb_employees.NIK','tb_employees.employee_name', DB::raw('SUM(hours_act) as total_act,SUM(hours_convertion) as total_convertion,sum(ammount) as total_ammount'))
        ->groupBy('NIK','employee_name')
        //->where('tb_overtimes.status_approve','1')
        //->where('tb_overtime_details.status','6')
        ->where('tb_overtime_details.status','<','90')
        ->where('date_on','>=',$Tglawal)
        ->where('date_on','<=',$Tglakhir);

        if($dept_id>0){
            $tb_sumot_b=$tb_sumot_b->where('tb_overtimes.dept_id',$dept_id);
        }
        else{
            $tb_sumot_b=$tb_sumot_b->whereExists(function ($query) use($id_employee){
                $query->select(DB::raw(1))
                      ->from('tb_admins')
                      ->whereColumn('tb_admins.dept_id', 'tb_overtimes.dept_id')
                      ->where('tb_admins.id_employee',$id_employee);
            });
        }

        $tb_sumot_b=$tb_sumot_b->orderby('total_convertion','desc')
        ->get();

        return view('page/user/m_overtime/overtimeemployee',['Periode'=>$periode,'dept_id'=>$dept_id,'tb_sumot'=>$tb_sumot,'tb_sumot_b'=>$tb_sumot_b,'periode'=>$periode,'Tglawal'=>$Tglawal,'Tglakhir'=>$Tglakhir,'menu'=>'overtimes']);
    }
    function reasonOT($periode,$dept_id,$reason){
        date_default_timezone_set("Asia/Jakarta");
        $kalendar=CAL_GREGORIAN;

        $data_a=explode('-',$periode);
        $thn=$data_a[0];
        $bln=$data_a[1];

        //$thn=date('Y');
        //$bln=date('m');
        $hariakhir=cal_days_in_month($kalendar,$bln,$thn);

        $Tglawal=date('Y-m-d',strtotime($thn.'-'.$bln.'-01'));
        $Tglakhir=date('Y-m-d',strtotime($thn.'-'.$bln.'-'.$hariakhir));
        $periode=date('Y-m',strtotime($Tglawal));
     
        $email=Auth::user()->email;
        $tb_email=tb_email::where('email_address',$email)->get();
        foreach($tb_email as $dt){
            $id_employee=$dt->id_employee;
            $tb_admin=tb_admin::where('id_employee',$id_employee)->get();
            foreach($tb_admin as $dt2){
                $dept_admin=$dt2->dept_id;
            }
        }

        $tb_sumot=DB::table('tb_overtime_details')
        ->leftjoin('tb_overtimes','tb_overtimes.id','=','tb_overtime_details.id_ot')
        ->select('tb_overtimes.dept_id','tb_overtimes.dept_name', DB::raw('SUM(hours_act) as total_act,SUM(hours_convertion) as total_convertion,sum(ammount) as total_ammount'))
        ->groupBy('dept_id','dept_name')
        //->where('tb_overtimes.status_approve','1')
        //->where('tb_overtime_details.status','6')
        ->where('tb_overtime_details.status','<','90')
        ->where('date_on','>=',$Tglawal)
        ->where('date_on','<=',$Tglakhir)
        ->whereExists(function ($query) use($id_employee){
            $query->select(DB::raw(1))
                  ->from('tb_admins')
                  ->whereColumn('tb_admins.dept_id', 'tb_overtimes.dept_id')
                  ->where('tb_admins.id_employee',$id_employee);
        })
        ->orderby('total_convertion','desc')
        ->get();
        $tb_sumot_b=DB::table('tb_overtime_details')
        ->leftjoin('tb_overtimes','tb_overtimes.id','=','tb_overtime_details.id_ot');
        
        if($reason==1){$opsi='Reason OT';$tb_sumot_b=$tb_sumot_b->select('tb_overtime_details.reason_ot as reason', DB::raw('SUM(hours_act) as total_act,SUM(hours_convertion) as total_convertion,sum(ammount) as total_ammount'))->groupBy('reason_ot');}
        elseif($reason==2){$opsi='Job OT';$tb_sumot_b=$tb_sumot_b->select('tb_overtime_details.job_ot as reason', DB::raw('SUM(hours_act) as total_act,SUM(hours_convertion) as total_convertion,sum(ammount) as total_ammount'))->groupBy('job_ot');}
        elseif($reason==3){$opsi='Customer';$tb_sumot_b=$tb_sumot_b->select('tb_overtime_details.customer as reason', DB::raw('SUM(hours_act) as total_act,SUM(hours_convertion) as total_convertion,sum(ammount) as total_ammount'))->groupBy('customer');}
        else {$opsi='General';$tb_sumot_b=$tb_sumot_b->select('tb_overtime_details.reason', DB::raw('SUM(hours_act) as total_act,SUM(hours_convertion) as total_convertion,sum(ammount) as total_ammount'))->groupBy('reason');}

        if($dept_id>0)$tb_sumot_b=$tb_sumot_b->where('tb_overtimes.dept_id',$dept_id);

        $tb_sumot_b=$tb_sumot_b->where('date_on','>=',$Tglawal)
        ->whereExists(function ($query) use($id_employee){
            $query->select(DB::raw(1))
                  ->from('tb_admins')
                  ->whereColumn('tb_admins.dept_id', 'tb_overtimes.dept_id')
                  ->where('tb_admins.id_employee',$id_employee);
        })
        ->where('date_on','<=',$Tglakhir)
        //->where('tb_overtimes.status_approve','1')
        //->where('tb_overtime_details.status','6')
        ->where('tb_overtime_details.status','<','90')
        ->orderby('total_convertion','desc')
        ->orderby('total_convertion','desc')
        ->get();
        //return $tb_sumot_b;
        return view('page/user/m_overtime/overtimereason',['dept_admin'=>$dept_admin,'Reason'=>$opsi,'Periode'=>$periode,'dept_id'=>$dept_id,'tb_sumot'=>$tb_sumot,'tb_sumot_b'=>$tb_sumot_b,'periode'=>$periode,'Tglawal'=>$Tglawal,'Tglakhir'=>$Tglakhir,'reason'=>$reason,'menu'=>'overtimes']);
    }
    function reasonOT_cutoff($Tglawal,$Tglakhir,$dept_id,$reason){
        date_default_timezone_set("Asia/Jakarta");
        $kalendar=CAL_GREGORIAN;
        $thn=date('Y');
        $bln=date('m');
        $d=date('d');
        $hariakhir=cal_days_in_month($kalendar,$bln,$thn);
        $Tgl1=date('Y-m-d',strtotime($thn.'-'.$bln.'-01'));
        $Tgl2=date('Y-m-d',strtotime($thn.'-'.$bln.'-'.$hariakhir));
        $periode_sekarang=date('Y-m');
        $periode_belakang=date('Y-m',strtotime('-1 days',strtotime($Tgl1)));
        $periode_depan=date('Y-m',strtotime('+1 days',strtotime($Tgl2)));

        if($Tglawal==0||$Tglakhir==0){
            if($d>24){
                $Tglawal=date('Y-m-d',strtotime($periode_sekarang.'-25'));
                $Tglakhir=date('Y-m-d',strtotime($periode_depan.'-24'));
            }else{
                $Tglawal=date('Y-m-d',strtotime($periode_belakang.'-25'));
                $Tglakhir=date('Y-m-d',strtotime($periode_sekarang.'-24'));
            }
        }
        $periode=date('Y-m',strtotime($Tglakhir));
     
        $email=Auth::user()->email;
        $tb_email=DB::table('tb_email')->where('email_address',$email)->get();
        foreach($tb_email as $dt){
            $id_employee=$dt->id_employee;
            $tb_admin=DB::table('tb_admin')->where('id_employee',$id_employee)->get();
            foreach($tb_admin as $dt2){
                $dept_admin=$dt2->dept_id;
            }
        }
        
        $tb_sumot=DB::table('tb_overtime_details')
        ->leftjoin('tb_overtimes','tb_overtimes.id','=','tb_overtime_details.id_ot')
        ->select('tb_overtimes.dept_id','tb_overtimes.dept_name', DB::raw('SUM(hours_act) as total_act,SUM(hours_convertion) as total_convertion,sum(ammount) as total_ammount'))
        ->groupBy('dept_id','dept_name')
        //->where('tb_overtimes.status_approve','1')
        //->where('tb_overtime_details.status','6')
        ->where('tb_overtime_details.status','<','90')
        ->where('date_on','>=',$Tglawal)
        ->where('date_on','<=',$Tglakhir)
        ->whereExists(function ($query) use($id_employee){
            $query->select(DB::raw(1))
                  ->from('tb_admins')
                  ->whereColumn('tb_admins.dept_id', 'tb_overtimes.dept_id')
                  ->where('tb_admins.id_employee',$id_employee);
        })
        ->orderby('total_convertion','desc')
        ->get();
        $tb_sumot_b=DB::table('tb_overtime_details')
        ->leftjoin('tb_overtimes','tb_overtimes.id','=','tb_overtime_details.id_ot');
        
        if($reason==1){$opsi='Reason OT';$tb_sumot_b=$tb_sumot_b->select('tb_overtime_details.reason_ot as reason', DB::raw('SUM(hours_act) as total_act,SUM(hours_convertion) as total_convertion,sum(ammount) as total_ammount'))->groupBy('reason_ot');}
        elseif($reason==2){$opsi='Job OT';$tb_sumot_b=$tb_sumot_b->select('tb_overtime_details.job_ot as reason', DB::raw('SUM(hours_act) as total_act,SUM(hours_convertion) as total_convertion,sum(ammount) as total_ammount'))->groupBy('job_ot');}
        elseif($reason==3){$opsi='Customer';$tb_sumot_b=$tb_sumot_b->select('tb_overtime_details.customer as reason', DB::raw('SUM(hours_act) as total_act,SUM(hours_convertion) as total_convertion,sum(ammount) as total_ammount'))->groupBy('customer');}
        else {$opsi='General';$tb_sumot_b=$tb_sumot_b->select('tb_overtime_details.reason', DB::raw('SUM(hours_act) as total_act,SUM(hours_convertion) as total_convertion,sum(ammount) as total_ammount'))->groupBy('reason');}

        if($dept_id>0)$tb_sumot_b=$tb_sumot_b->where('tb_overtimes.dept_id',$dept_id);

        $tb_sumot_b=$tb_sumot_b->where('date_on','>=',$Tglawal)
        ->whereExists(function ($query) use($id_employee){
            $query->select(DB::raw(1))
                  ->from('tb_admins')
                  ->whereColumn('tb_admins.dept_id', 'tb_overtimes.dept_id')
                  ->where('tb_admins.id_employee',$id_employee);
        })
        ->where('date_on','<=',$Tglakhir)
        //->where('tb_overtimes.status_approve','1')
        //->where('tb_overtime_details.status','6')
        ->where('tb_overtime_details.status','<','90')
        ->orderby('total_convertion','desc')
        ->orderby('total_convertion','desc')
        ->get();
        //return $tb_sumot_b;
        return view('page/user/m_overtime/overtimereason_cutoff',['dept_admin'=>$dept_admin,'Reason'=>$opsi,'Periode'=>$periode,'dept_id'=>$dept_id,'tb_sumot'=>$tb_sumot,'tb_sumot_b'=>$tb_sumot_b,'periode'=>$periode,'Tglawal'=>$Tglawal,'Tglakhir'=>$Tglakhir,'reason'=>$reason,'menu'=>'overtimes']);
    }
    function summaries_baru($periode,$dept_id){
        $divisi='';
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


            //$this->summaryUpdate($thn,$bln,$dept_id);
            $tb_slip=DB::table('tb_summary_overtime_trial')
            ->select(
                'tb_summary_overtime_trial.dept_id',
                'tb_summary_overtime_trial.divisi',
                'tb_summary_overtime_trial.id_employee',
                'tb_summary_overtime_trial.NIK',
                'tb_summary_overtime_trial.employee_name',
                'tb_summary_overtime_trial.SLPJ',
                'tb_summary_overtime_trial.rapel',
                DB::raw('SUM(tb_summary_overtime_trial.t_hours) as st_hours,SUM(tb_summary_overtime_trial.t_convertion) as stotal_convertion,SUM(tb_summary_overtime_trial.t_ammount) as st_ammount,SUM(tb_summary_overtime_trial.total_meal) as stotal_meal,SUM(tb_summary_overtime_trial.rapel) as srapel,SUM(tb_summary_overtime_trial.total_bayars) as stotal_bayars,SUM(tb_summary_overtime_trial.pph21) as spph21,SUM(tb_summary_overtime_trial.pph21_insentive) as spph21_insentive,SUM(tb_summary_overtime_trial.total_paid) as stotal_paid')
            )
            ->groupBy(
                'tb_summary_overtime_trial.dept_id',
                'tb_summary_overtime_trial.divisi',
                'tb_summary_overtime_trial.id_employee',
                'tb_summary_overtime_trial.NIK',
                'tb_summary_overtime_trial.employee_name',
                'tb_summary_overtime_trial.SLPJ',
                'tb_summary_overtime_trial.rapel',
            )
            ->where('tb_summary_overtime_trial.periode',$periode)->orderby('stotal_paid','desc')->get();
            foreach($tb_slip as $dt){$divisi=$dt->divisi;}
            $ntb_slip=DB::table('tb_summary_overtime_trial')->where('total_paid','>','0')->count();  

            //return $ntb_slip;          
            return view('page/user/m_overtime/overtimesummary_all',['divisi'=>$divisi,'periode'=>$periode,'thn'=>$thn,'bln'=>$bln,'dept_id'=>$dept_id,'tb_slip'=>$tb_slip,'ntb_slip'=>$ntb_slip,'sequen'=>'0','lanjut'=>'0','menu'=>'overtimes']);
    }
    function summaries($periode,$dept_id){
        $divisi='';
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


            //$this->summaryUpdate($thn,$bln,$dept_id);
            $tb_slip=DB::table('tb_summary_overtime')
            ->select(
                'tb_summary_overtime.dept_id',
                'tb_summary_overtime.divisi',
                'tb_summary_overtime.id_employee',
                'tb_summary_overtime.NIK',
                'tb_summary_overtime.employee_name',
                'tb_summary_overtime.SLPJ',
                'tb_summary_overtime.rapel',
                'tb_summary_overtime.cc_code',
                DB::raw('SUM(tb_summary_overtime.t_hours) as st_hours,SUM(tb_summary_overtime.t_convertion) as stotal_convertion,SUM(tb_summary_overtime.t_ammount) as st_ammount,SUM(tb_summary_overtime.total_meal) as stotal_meal,SUM(tb_summary_overtime.rapel) as srapel,SUM(tb_summary_overtime.total_bayars) as stotal_bayars,SUM(tb_summary_overtime.pph21) as spph21,SUM(tb_summary_overtime.pph21_insentive) as spph21_insentive,SUM(tb_summary_overtime.total_paid) as stotal_paid')
            )
            ->groupBy(
                'tb_summary_overtime.dept_id',
                'tb_summary_overtime.divisi',
                'tb_summary_overtime.id_employee',
                'tb_summary_overtime.NIK',
                'tb_summary_overtime.employee_name',
                'tb_summary_overtime.SLPJ',
                'tb_summary_overtime.rapel',
                'tb_summary_overtime.cc_code',
            )
            ->where('tb_summary_overtime.periode',$periode)->orderby('stotal_paid','desc')->get();
            foreach($tb_slip as $dt){$divisi=$dt->divisi;}
            $ntb_slip=DB::table('tb_summary_overtime')->where('total_paid','>','0')->count();  

            //return $ntb_slip;          
            return view('page/user/m_overtime/overtimesummary_all',['divisi'=>$divisi,'periode'=>$periode,'thn'=>$thn,'bln'=>$bln,'dept_id'=>$dept_id,'tb_slip'=>$tb_slip,'ntb_slip'=>$ntb_slip,'sequen'=>'0','lanjut'=>'0','menu'=>'overtimes','is_dpk'=>'1']);
    }
    function summariesDetail($periode,$dept_id){
        $divisi='';
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


            //$this->summaryUpdate($thn,$bln,$dept_id);
            $tb_slip=DB::table('tb_slip_overtimes')
            ->join('tb_summary_overtime', function ($join) {
                $join->on('tb_summary_overtime.id_employee', '=', 'tb_slip_overtimes.id_employee')
                ->on('tb_summary_overtime.periode', '=', 'tb_slip_overtimes.periode');
            })
            ->select(
                'tb_summary_overtime.divisi',
                'tb_summary_overtime.employee_name',
                'tb_summary_overtime.NIK',
                'tb_slip_overtimes.id_employee',
                'tb_slip_overtimes.slpj',
                'tb_slip_overtimes.ot_category',
                'tb_slip_overtimes.act_hours',
                'tb_slip_overtimes.act_convertion',
                'tb_slip_overtimes.meal_off',
                'tb_slip_overtimes.meal_ot',
                'tb_slip_overtimes.meal_tl',
                'tb_slip_overtimes.ammount',
                'tb_slip_overtimes.total_bayar',
            )
            ->where('tb_summary_overtime.periode',$periode)->get();
            foreach($tb_slip as $dt){$divisi=$dt->divisi;}
            $ntb_slip=DB::table('tb_summary_overtime')->where('total_paid','>','0')->count();  

            //return $tb_slip;          
            return view('page/user/m_overtime/overtimesummary_all_detail',['divisi'=>$divisi,'periode'=>$periode,'thn'=>$thn,'bln'=>$bln,'dept_id'=>$dept_id,'tb_slip'=>$tb_slip,'ntb_slip'=>$ntb_slip,'sequen'=>'0','lanjut'=>'0','menu'=>'overtimes']);
    }
    function summaryUpdate($thn,$bln,$deptid,$sequen){
        $take=30;
        $skip=$sequen*$take;
        $admin=Auth::user()->name;
        date_default_timezone_set("Asia/Jakarta");
        $kalendar=CAL_GREGORIAN;
        $thn=$thn;
        $bln=$bln;
        $periode=date('Y-m',strtotime($thn.'-'.$bln.'-01'));
        $hariakhir=cal_days_in_month($kalendar,$bln,$thn);
        $sekarang=date('Y-m-d H:i:s');
        $qty_employee=DB::table('tb_employees')->where('dept_id',$deptid)->where('delete','0')->count();
        if($skip>$qty_employee)return redirect("/Overtimes/Summary/".$thn."-".$bln."/".$deptid);
        $tb_pph21_status=DB::table('tb_utilities')->where('atribut','PPH21')->get();
        foreach($tb_pph21_status as $dt_pphs1_status){$pph21_status=$dt_pphs1_status->status;}
        $tb_employee=DB::table('tb_employees')->leftjoin('tb_departments','tb_departments.id','tb_employees.dept_id')->where('dept_id',$deptid)->where('delete','0')->skip($skip)->take($take)->get(['tb_employees.*','tb_departments.dept_code','tb_departments.dept_name']);
        $divisi='';
        foreach ($tb_employee as $dt) {
            $divisi=$dt->dept_name;
            $t_hours=0;
            $t_convertion=0;
            $total_bayars=0;
            $t_ammount=0;
            $t_off=0;$t_ot=0;$t_tl=0;
            $total_meal=0;
            for ($i=1; $i <= $hariakhir; $i++) {
                $Tgl=date('Y-m-d',strtotime($thn.'-'.$bln.'-'.$i));
                $delete=DB::table('tb_slip_overtimes')->where('id_employee',$dt->id)->where('date_on',$Tgl)->delete();
                //Update Slip via Meal
                    $meal_off=0;
                    $meal_ot=0;
                    $meal_tl=0;
                    $total_bayar=0;
                    $tb_meal=tb_meal::where('date_on',$Tgl)->where('id_employee',$dt->id)->get();
                    foreach ($tb_meal as $dt2) {
                        if($dt2->category=='meal_off'){$meal_off=$dt2->meal;$t_off=$t_off+$meal_off;}
                        if($dt2->category=='meal_ot'){$meal_ot=$dt2->meal;$t_ot=$t_ot+$meal_ot;}
                        if($dt2->category=='meal_tl'){$meal_tl=$dt2->meal;$t_tl=$t_tl+$meal_tl;}
                        $total_bayar=$meal_off+$meal_ot+$meal_tl;
                        $total_meal=$t_off+$t_ot+$t_tl;
                    }
                    if($total_bayar>0){
                        $insert_slip=DB::table('tb_slip_overtimes')->insert([
                            'periode'=>$periode,
                            'dept_id'=>$deptid,
                            'date_on'=>$Tgl,
                            'id_employee'=>$dt->id,
                            'meal_off'=>$meal_off,
                            'meal_ot'=>$meal_ot,
                            'meal_tl'=>$meal_tl,
                            'total_bayar'=>$total_bayar,
                            'status'=>'0',
                            'created_at'=>$sekarang,
                            'updated_at'=>$sekarang
                        ]);
                    }
                //Update Slip via Mal End
                //Update Slip via Overtime
                    $tb_overtime_detail=tb_overtime_detail::where('date_on',$Tgl)->where('id_employee',$dt->id)->where('status','6')->get();
                    //$SLPJ=0;
                    foreach ($tb_overtime_detail as $dt2) {
                        $slpj=round($dt2->SLPJ);
                        $total_bayar=$total_bayar+$dt2->ammount;
                        $t_hours=$t_hours+$dt2->hours_act;
                        $t_convertion=$t_convertion+$dt2->hours_convertion;
                        $t_ammount=$t_ammount+$dt2->ammount;
                        //$SLPJ=$dt2->SLPJ;


                        //$check=DB::table('tb_slip_overtimes')->where('id_employee',$dt->id)->where('date_on',$Tgl)->where('ot_start',$dt2->start_act)->count();
                        $check=DB::table('tb_slip_overtimes')->where('id_employee',$dt->id)->where('date_on',$Tgl)->count();
                        if($check>0){
                            $update1=DB::table('tb_slip_overtimes')->where('id_employee',$dt->id)->where('date_on',$Tgl)->update([
                                'ot_category'=>$dt2->ot_category,
                                'id_overtime'=>$dt2->id_ot,
                                'id_overtime_detail'=>$dt2->id,
                                'slpj'=>$slpj,
                                'ot_start'=>$dt2->start_act,
                                'ot_finish'=>$dt2->finish_act,
                                'act_hours'=>$dt2->hours_act,
                                'act_convertion'=>$dt2->hours_convertion,
                                'ammount'=>$dt2->ammount,
                                'total_bayar'=>$total_bayar
                            ]);
                        }else{
                            $insert_slip=DB::table('tb_slip_overtimes')->insert([
                                'periode'=>$periode,
                                'dept_id'=>$deptid,
                                'date_on'=>$Tgl,
                                'ot_category'=>$dt2->ot_category,
                                'id_employee'=>$dt->id,
                                'id_overtime'=>$dt2->id_ot,
                                'id_overtime_detail'=>$dt2->id,
                                'slpj'=>$slpj,
                                'ot_start'=>$dt2->start_act,
                                'ot_finish'=>$dt2->finish_act,
                                'act_hours'=>$dt2->hours_act,
                                'act_convertion'=>$dt2->hours_convertion,
                                'ammount'=>$dt2->ammount,
                                'total_bayar'=>$total_bayar,
                                'status'=>'0',
                                'created_at'=>$sekarang,
                                'updated_at'=>$sekarang
                            ]);

                        }
                    }
                //Update Slip via Overtime End
                $total_bayars=$total_bayars+$total_bayar;
                //if($dt->id=='1138' && $total_bayars>0)return $Tgl.': '.$total_bayars;
            }
            $tb_rapel=DB::table('tb_slip_overtimes')->where('id_employee',$dt->id)->where('periode',$periode)->where('rapel_ot','1')->get();
            foreach($tb_rapel as $dt3){
                $t_ammount=$t_ammount+$dt3->total_bayar;
                $t_hours=$t_hours+$dt3->act_hours;
                $t_convertion=$t_convertion+$dt3->act_convertion;
                $total_bayars=$total_bayars+$dt3->total_bayar;
            }
            $pph21=0;
            $pph21_insentive=0;
            //return $total_bayars;
            $tb_summary=DB::table('tb_summary_overtime')->where('id_employee',$dt->id)->where('periode',$periode)->get();
            //return $tb_summary;
            foreach ($tb_summary as $dt2) {
                $tb_pph21=DB::table('tb_salary')->where('Periode',$periode)->where('id_employee',$dt2->id_employee)->get(['PPH21_OT']);
                foreach($tb_pph21 as $dt_tax){
                    $pph21=$dt_tax->PPH21_OT;
                    $pph21_insentive=$pph21*$pph21_status;
                }

                //if($pph21>1)return $pph21;

                $rapel=$dt2->rapel;
                $total_paid=$rapel-$pph21+$pph21_insentive+$total_bayars;
                $update2=DB::table('tb_summary_overtime')->where('id',$dt2->id)->update([
                    //'SLPJ'=>$slpj,
                    't_hours'=>$t_hours,
                    't_convertion'=>$t_convertion,
                    't_ammount'=>$t_ammount,
                    't_off'=>$t_off,
                    't_ot'=>$t_ot,
                    't_tl'=>$t_tl,
                    'pph21'=>$pph21,
                    'pph21_insentive'=>$pph21_insentive,
                    'total_meal'=>$total_meal,
                    'total_bayars'=>$total_bayars,
                    'total_paid'=>$total_paid,
                    'payroll'=>$admin
                ]);
            }


        }
        $tb_slip=DB::table('tb_summary_overtime')
        ->select(
            'tb_summary_overtime.dept_id',
            'tb_summary_overtime.divisi',
            'tb_summary_overtime.id_employee',
            'tb_summary_overtime.NIK',
            'tb_summary_overtime.employee_name',
            'tb_summary_overtime.SLPJ',
            DB::raw('SUM(tb_summary_overtime.t_hours) as st_hours,SUM(tb_summary_overtime.t_convertion) as stotal_convertion,SUM(tb_summary_overtime.t_ammount) as st_ammount,SUM(tb_summary_overtime.total_meal) as stotal_meal,SUM(tb_summary_overtime.rapel) as srapel,SUM(tb_summary_overtime.total_bayars) as stotal_bayars,SUM(tb_summary_overtime.pph21) as spph21,SUM(tb_summary_overtime.pph21_insentive) as spph21_insentive,SUM(tb_summary_overtime.total_paid) as stotal_paid')
        )
        ->groupBy(
            'tb_summary_overtime.dept_id',
            'tb_summary_overtime.divisi',
            'tb_summary_overtime.id_employee',
            'tb_summary_overtime.NIK',
            'tb_summary_overtime.employee_name',
            'tb_summary_overtime.SLPJ',
        )
        ->where('tb_summary_overtime.periode',$periode)->where('dept_id',$deptid)->orderby('stotal_paid','desc')->get();
        $ntb_slip=DB::table('tb_summary_overtime')->where('dept_id',$deptid)->where('total_paid','>','0')->count();  
        $sequen++;
        if($take*$sequen>=$qty_employee)$progress=100;
        else $progress=number_format($take*$sequen/$qty_employee*100,0);
        //return "Sukses";
        //return redirect("/Slip/Overtime/".$thn."/".$bln."/".$deptid);
        return view('page/user/m_overtime/overtimesummary',['divisi'=>$divisi,'periode'=>$periode,'thn'=>$thn,'bln'=>$bln,'dept_id'=>$deptid,'tb_slip'=>$tb_slip,'ntb_slip'=>$ntb_slip,'sequen'=>$sequen,'lanjut'=>'1','progress'=>$progress,'menu'=>'overtimes']);
    }
    function summaryUpdate_lama($thn,$bln,$deptid,$sequen){
        $take=30;
        $skip=$sequen*$take;
        $admin=Auth::user()->name;
        date_default_timezone_set("Asia/Jakarta");
        $kalendar=CAL_GREGORIAN;
        $thn=$thn;
        $bln=$bln;
        $periode=date('Y-m',strtotime($thn.'-'.$bln.'-01'));
        $hariakhir=cal_days_in_month($kalendar,$bln,$thn);
        $sekarang=date('Y-m-d H:i:s');
        //$qty_employee=DB::table('tb_employees')->where('dept_id',$deptid)->where('status','1')->where('id','1138')->count();
        $qty_employee=DB::table('tb_employees')->where('dept_id',$deptid)->where('delete','0')->count();
        //return $qty_employee;
        if($skip>$qty_employee)return redirect("/Overtimes/Summary/".$thn."-".$bln."/".$deptid);

        $tb_pph21_status=DB::table('tb_utilities')->where('atribut','PPH21')->get();
        foreach($tb_pph21_status as $dt_pphs1_status){$pph21_status=$dt_pphs1_status->status;}

        //$tb_employee=DB::table('tb_employees')->leftjoin('tb_departments','tb_departments.id','tb_employees.dept_id')->where('dept_id',$deptid)->where('tb_employees.id','1138')->where('status','1')->skip($skip)->take($take)->get(['tb_employees.*','tb_departments.dept_code','tb_departments.dept_name']);
        $tb_employee=DB::table('tb_employees')->leftjoin('tb_departments','tb_departments.id','tb_employees.dept_id')->where('dept_id',$deptid)->where('delete','0')->skip($skip)->take($take)->get(['tb_employees.*','tb_departments.dept_code','tb_departments.dept_name']);
        //return $tb_employee;
        $divisi='';
        foreach ($tb_employee as $dt) {
            $divisi=$dt->dept_name;
            $t_hours=0;
            $t_convertion=0;
            $total_bayars=0;
            $t_ammount=0;
            $t_off=0;$t_ot=0;$t_tl=0;
            $total_meal=0;
            for ($i=1; $i <= $hariakhir; $i++) {
                $Tgl=date('Y-m-d',strtotime($thn.'-'.$bln.'-'.$i));
                $delete=DB::table('tb_slip_overtimes')->where('id_employee',$dt->id)->where('date_on',$Tgl)->delete();
                //Update Slip via Meal
                    $meal_off=0;
                    $meal_ot=0;
                    $meal_tl=0;
                    $total_bayar=0;
                    $tb_meal=tb_meal::where('date_on',$Tgl)->where('id_employee',$dt->id)->get();
                    foreach ($tb_meal as $dt2) {
                        if($dt2->category=='meal_off'){$meal_off=$dt2->meal;$t_off=$t_off+$meal_off;}
                        if($dt2->category=='meal_ot'){$meal_ot=$dt2->meal;$t_ot=$t_ot+$meal_ot;}
                        if($dt2->category=='meal_tl'){$meal_tl=$dt2->meal;$t_tl=$t_tl+$meal_tl;}
                        $total_bayar=$meal_off+$meal_ot+$meal_tl;
                        $total_meal=$t_off+$t_ot+$t_tl;
                    }
                    if($total_bayar>0){
                        $insert_slip=DB::table('tb_slip_overtimes')->insert([
                            'periode'=>$periode,
                            'dept_id'=>$deptid,
                            'date_on'=>$Tgl,
                            'id_employee'=>$dt->id,
                            'meal_off'=>$meal_off,
                            'meal_ot'=>$meal_ot,
                            'meal_tl'=>$meal_tl,
                            'total_bayar'=>$total_bayar,
                            'status'=>'0',
                            'created_at'=>$sekarang,
                            'updated_at'=>$sekarang
                        ]);
                    }
                //Update Slip via Mal End
                //Update Slip via Overtime
                    $tb_overtime_detail=tb_overtime_detail::where('date_on',$Tgl)->where('id_employee',$dt->id)->where('status','6')->get();
                    //$SLPJ=0;
                    foreach ($tb_overtime_detail as $dt2) {
                        $slpj=round($dt2->SLPJ);
                        $total_bayar=$total_bayar+$dt2->ammount;
                        $t_hours=$t_hours+$dt2->hours_act;
                        $t_convertion=$t_convertion+$dt2->hours_convertion;
                        $t_ammount=$t_ammount+$dt2->ammount;
                        //$SLPJ=$dt2->SLPJ;


                        //$check=DB::table('tb_slip_overtimes')->where('id_employee',$dt->id)->where('date_on',$Tgl)->where('ot_start',$dt2->start_act)->count();
                        $check=DB::table('tb_slip_overtimes')->where('id_employee',$dt->id)->where('date_on',$Tgl)->count();
                        if($check>0){
                            $update1=DB::table('tb_slip_overtimes')->where('id_employee',$dt->id)->where('date_on',$Tgl)->update([
                                'ot_category'=>$dt2->ot_category,
                                'id_overtime'=>$dt2->id_ot,
                                'id_overtime_detail'=>$dt2->id,
                                'slpj'=>$slpj,
                                'ot_start'=>$dt2->start_act,
                                'ot_finish'=>$dt2->finish_act,
                                'act_hours'=>$dt2->hours_act,
                                'act_convertion'=>$dt2->hours_convertion,
                                'ammount'=>$dt2->ammount,
                                'total_bayar'=>$total_bayar
                            ]);
                        }else{
                            $insert_slip=DB::table('tb_slip_overtimes')->insert([
                                'periode'=>$periode,
                                'dept_id'=>$deptid,
                                'date_on'=>$Tgl,
                                'ot_category'=>$dt2->ot_category,
                                'id_employee'=>$dt->id,
                                'id_overtime'=>$dt2->id_ot,
                                'id_overtime_detail'=>$dt2->id,
                                'slpj'=>$slpj,
                                'ot_start'=>$dt2->start_act,
                                'ot_finish'=>$dt2->finish_act,
                                'act_hours'=>$dt2->hours_act,
                                'act_convertion'=>$dt2->hours_convertion,
                                'ammount'=>$dt2->ammount,
                                'total_bayar'=>$total_bayar,
                                'status'=>'0',
                                'created_at'=>$sekarang,
                                'updated_at'=>$sekarang
                            ]);

                        }
                    }
                //Update Slip via Overtime End
                $total_bayars=$total_bayars+$total_bayar;
                //if($dt->id=='1138' && $total_bayars>0)return $Tgl.': '.$total_bayars;
            }

            $tb_rapel=DB::table('tb_slip_overtimes')->where('id_employee',$dt->id)->where('periode',$periode)->where('rapel_ot','1')->get();
            foreach($tb_rapel as $dt3){
                $t_ammount=$t_ammount+$dt3->total_bayar;
                $t_hours=$t_hours+$dt3->act_hours;
                $t_convertion=$t_convertion+$dt3->act_convertion;
                $total_bayars=$total_bayars+$dt3->total_bayar;
            }
            $pph21=0;
            $pph21_insentive=0;
            //return $total_bayars;
            $tb_summary=DB::table('tb_summary_overtime')->where('id_employee',$dt->id)->where('periode',$periode)->get();
            //return $tb_summary;
            foreach ($tb_summary as $dt2) {
                $tb_pph21=DB::table('tb_salary')->where('Periode',$periode)->where('id_employee',$dt2->id_employee)->get(['PPH21_OT']);
                foreach($tb_pph21 as $dt_tax){
                    $pph21=$dt_tax->PPH21_OT;
                    $pph21_insentive=$pph21*$pph21_status;
                }

                //if($pph21>1)return $pph21;

                $rapel=$dt2->rapel;
                $total_paid=$rapel-$pph21+$pph21_insentive+$total_bayars;
                $update2=DB::table('tb_summary_overtime')->where('id',$dt2->id)->update([
                    //'SLPJ'=>$slpj,
                    't_hours'=>$t_hours,
                    't_convertion'=>$t_convertion,
                    't_ammount'=>$t_ammount,
                    't_off'=>$t_off,
                    't_ot'=>$t_ot,
                    't_tl'=>$t_tl,
                    'pph21'=>$pph21,
                    'pph21_insentive'=>$pph21_insentive,
                    'total_meal'=>$total_meal,
                    'total_bayars'=>$total_bayars,
                    'total_paid'=>$total_paid,
                    'payroll'=>$admin
                ]);
            }


        }
        $tb_slip=DB::table('tb_summary_overtime')
        ->select(
            'tb_summary_overtime.dept_id',
            'tb_summary_overtime.divisi',
            'tb_summary_overtime.id_employee',
            'tb_summary_overtime.NIK',
            'tb_summary_overtime.employee_name',
            'tb_summary_overtime.SLPJ',
            DB::raw('SUM(tb_summary_overtime.t_hours) as st_hours,SUM(tb_summary_overtime.t_convertion) as stotal_convertion,SUM(tb_summary_overtime.t_ammount) as st_ammount,SUM(tb_summary_overtime.total_meal) as stotal_meal,SUM(tb_summary_overtime.rapel) as srapel,SUM(tb_summary_overtime.total_bayars) as stotal_bayars,SUM(tb_summary_overtime.pph21) as spph21,SUM(tb_summary_overtime.pph21_insentive) as spph21_insentive,SUM(tb_summary_overtime.total_paid) as stotal_paid')
        )
        ->groupBy(
            'tb_summary_overtime.dept_id',
            'tb_summary_overtime.divisi',
            'tb_summary_overtime.id_employee',
            'tb_summary_overtime.NIK',
            'tb_summary_overtime.employee_name',
            'tb_summary_overtime.SLPJ',
        )
        ->where('tb_summary_overtime.periode',$periode)->where('dept_id',$deptid)->orderby('stotal_paid','desc')->get();
        $ntb_slip=DB::table('tb_summary_overtime')->where('dept_id',$deptid)->where('total_paid','>','0')->count();  
        $sequen++;
        if($take*$sequen>=$qty_employee)$progress=100;
        else $progress=number_format($take*$sequen/$qty_employee*100,0);
        //return "Sukses";
        //return redirect("/Slip/Overtime/".$thn."/".$bln."/".$deptid);
        return view('page/user/m_overtime/overtimesummary',['divisi'=>$divisi,'periode'=>$periode,'thn'=>$thn,'bln'=>$bln,'dept_id'=>$deptid,'tb_slip'=>$tb_slip,'ntb_slip'=>$ntb_slip,'sequen'=>$sequen,'lanjut'=>'1','progress'=>$progress,'menu'=>'overtimes']);
    }

    function summaryUpdateTrial($thn,$bln,$deptid){
        $admin=Auth::user()->name;
        date_default_timezone_set("Asia/Jakarta");
        $kalendar=CAL_GREGORIAN;
        $periode=date('Y-m',strtotime($thn.'-'.$bln.'-01'));
        $sekarang=date('Y-m-d H:i:s');

        $tb_pph21_status=DB::table('tb_utilities')->where('atribut','PPH21')->get();
        foreach($tb_pph21_status as $dt_pphs1_status){$pph21_status=$dt_pphs1_status->status;}
        //$tb_employee=DB::table('tb_employees')->leftjoin('tb_departments','tb_departments.id','tb_employees.dept_id')->where('delete','0')->get(['tb_employees.*','tb_departments.dept_code','tb_departments.dept_name']);
        $tb_employee=DB::table('tb_employees')->leftjoin('tb_departments','tb_departments.id','tb_employees.dept_id')->where('delete','0')->where('tb_employees.dept_id',$deptid)->get(['tb_employees.*','tb_departments.dept_code','tb_departments.dept_name']);        $divisi='';
        foreach ($tb_employee as $dt) {
            $divisi=$dt->dept_name;
            $t_hours=0;$t_convertion=0;
            $total_bayars=0;
            $t_ammount=0;
            $t_off=0;$t_ot=0;$t_tl=0;
            $total_meal=0;

            $filter='%'.$periode.'%';
            $tb_meal=tb_meal::where('date_on','like',$filter)->where('id_employee',$dt->id)->get();
            foreach ($tb_meal as $dt2) {
                $total_meal=$total_meal+$dt2->meal;
            }
            $total_bayars=$total_bayars+$total_meal;
            //return $total_meal;
            $tb_rapel=DB::table('tb_slip_overtimes_trial')
            ->where('id_employee',$dt->id)
            ->where('periode',$periode)->get();
            foreach($tb_rapel as $dt3){
                $t_ammount=$t_ammount+$dt3->total_bayar;
                $t_hours=$t_hours+$dt3->act_hours;
                $t_convertion=$t_convertion+$dt3->act_convertion;
                $total_bayars=$total_bayars+$dt3->total_bayar;
            }
            $pph21=0;
            $pph21_insentive=0;
            $tb_summary=DB::table('tb_summary_overtime_trial')->where('id_employee',$dt->id)->where('periode',$periode)->get();
            foreach ($tb_summary as $dt2) {
                //$tb_pph21=DB::table('tb_salary')->where('Periode',$periode)->where('id_employee',$dt2->id_employee)->get(['PPH21_OT']);
                //foreach($tb_pph21 as $dt_tax){
                    //$pph21=$dt_tax->PPH21_OT;
                    //$pph21_insentive=$pph21*$pph21_status;
                //}
                $rapel=$dt2->rapel;
                $total_paid=$rapel-$pph21+$pph21_insentive+$total_bayars;
                $update2=DB::table('tb_summary_overtime_trial')->where('id',$dt2->id)->update([
                    't_hours'=>$t_hours,
                    't_convertion'=>$t_convertion,
                    't_ammount'=>$t_ammount,
                    't_off'=>$t_off,
                    't_ot'=>$t_ot,
                    't_tl'=>$t_tl,
                    'pph21'=>$pph21,
                    'pph21_insentive'=>$pph21_insentive,
                    'total_meal'=>$total_meal,
                    'total_bayars'=>$total_bayars,
                    'total_paid'=>$total_paid,
                    'payroll'=>$admin
                ]);
                //return $dt2->id;
            }
        }
        return redirect()->back()->with(['info'=>'Update Success']);
    }
    function summaryUpdateSLPJ($thn,$bln,$deptid){
        $periode=$thn.'-'.$bln;
        $tb1=DB::table('tb_summary_overtime as a')->where('a.periode',$periode)->where('a.dept_id',$deptid)->get();
        foreach($tb1 as $dt1){
            $slpj=$dt1->SLPJ;
            $tb2=DB::table('tb_slip_overtimes as b')->where('b.periode',$periode)->where('b.id_employee',$dt1->id_employee)->get();
            foreach($tb2 as $dt2){
                $slpj=DB::table('tb_salaries as c')->where('c.id_employee',$dt1->id_employee)->where('c.status','1')->limit(1)->value('c.slpj');
                if(round($dt2->slpj,0)!=round($slpj,0)){
                    $amount=$slpj*$dt2->act_convertion;
                    $total=$amount+$dt2->meal_off+$dt2->meal_tl+$dt2->meal_ot;
                    $update_slip=DB::table('tb_slip_overtimes')->where('id',$dt2->id)->update([
                        'slpj'=>$slpj,
                        'ammount'=>$amount,
                        'total_bayar'=>$total,
                    ]);
                }
            }
            $total_bayar=DB::table('tb_slip_overtimes as b')->where('b.periode',$periode)->where('b.id_employee',$dt1->id_employee)->sum('total_bayar');
            $total_bayars=$total_bayar+$dt1->rapel;
            $total_paid=$total_bayars-$dt1->pph21;
            $update_summary=DB::table('tb_summary_overtime')->where('id',$dt1->id)->update([
                'SLPJ'=>$slpj,
                't_ammount'=>$total_bayar,
                'total_bayars'=>$total_bayars,
                'total_paid'=>$total_paid
            ]);
        }
        return redirect()->back()->with(['info'=>'Update Success']);
    }
    function slipOT($thn,$bln,$deptid,$id_employee){
        $kalendar=CAL_GREGORIAN;
        $hariakhir=cal_days_in_month($kalendar,$bln,$thn);
        $bulan=date('F',strtotime($thn.'-'.$bln.'-01'));
        $akhirbulan=date('Y-m-d',strtotime($thn.'-'.$bln.'-'.$hariakhir));
        $judul="PERIODE 01 ".$bulan." S/D ".$hariakhir." ".$bulan." ".$thn;

        $periode=date('Y-m',strtotime($thn.'-'.$bln.'-01'));

        $tb_slip=DB::table('tb_summary_overtime')
        ->leftjoin('tb_rapel_khusus','tb_rapel_khusus.id_summary','=','tb_summary_overtime.id')
        ->where('tb_summary_overtime.id_employee',$id_employee)
        ->where('tb_summary_overtime.periode',$periode)
        ->get(['tb_summary_overtime.*','tb_rapel_khusus.rapel_gaji','tb_rapel_khusus.rapel_ot','tb_rapel_khusus.pph21_rapel','tb_rapel_khusus.rapel_total']);
        //return $tb_slip;
        $FileName='SLIP_OT.PDF';
        $pdf = PDF::loadview('page/user/m_overtime/slip_overtime',['tb_slip'=>$tb_slip,'judul'=>$judul,'thn'=>$thn,'bln'=>$bln,'akhirbulan'=>$akhirbulan,'hariakhir'=>$hariakhir,'periode'=>$periode])->setPaper(array(0,0,560,480 ));
        return $pdf->stream($FileName);
    }
    function slipOT_($thn,$bln,$deptid,$id_employee,$no){
        $kalendar=CAL_GREGORIAN;
        $hariakhir=cal_days_in_month($kalendar,$bln,$thn);
        $bulan=date('F',strtotime($thn.'-'.$bln.'-01'));
        $akhirbulan=date('Y-m-d',strtotime($thn.'-'.$bln.'-'.$hariakhir));
        $judul="PERIODE 01 ".$bulan." S/D ".$hariakhir." ".$bulan." ".$thn;

        $periode=date('Y-m',strtotime($thn.'-'.$bln.'-01'));

        $take=20*31;
        $skip=($no-1)*$take;

        $tb_slip=DB::table('tb_slip_overtimes')
        ->leftjoin('tb_summary_overtime',function($join){
            $join->on('tb_summary_overtime.periode','=','tb_slip_overtime.periode')
            ->on('tb_summary_overtime.dept_id','=','tb_slip_overtime.dept_id')
            ->on('tb_summary_overtime.id_employee','=','tb_slip_overtime.id_employee');
        })
        ->where('tb_summary_overtime.dept_id',$deptid);
        if($id_employee>0)$tb_slip=$tb_slip->where('tb_summary_overtime.id_employee',$id_employee);
        $tb_slip=$tb_slip->where('tb_summary_overtime.total_paid','>','0')
        ->where('tb_slip_overtime.periode',$periode)
        ->orderby('tb_summary_overtime.total_paid','desc')
        ->orderby('tb_summary_overtime.id_employee','asc')
        ->orderby('date_on','asc')
        ->skip($skip)->take($take)
        ->get();
        //return $tb_slip;

        $FileName='SLIP_OT.PDF';
        $pdf = PDF::loadview('page/user/m_overtime/slip_overtime',['tb_slip'=>$tb_slip,'judul'=>$judul,'akhirbulan'=>$akhirbulan])->setPaper('a5','landscape');
        return $pdf->stream($FileName);
    }
    function slipOTExcel($thn,$bln,$deptid,$id_employee){
        $kalendar=CAL_GREGORIAN;
        $hariakhir=cal_days_in_month($kalendar,$bln,$thn);
        $bulan=date('F',strtotime($thn.'-'.$bln.'-01'));
        $akhirbulan=date('Y-m-d',strtotime($thn.'-'.$bln.'-'.$hariakhir));
        $judul="PERIODE 01 ".$bulan." S/D ".$hariakhir." ".$bulan." ".$thn;

        $periode=date('Y-m',strtotime($thn.'-'.$bln.'-01'));

        $tb_slip=DB::table('tb_summary_overtime')
        ->where('tb_summary_overtime.id_employee',$id_employee)
        ->where('tb_summary_overtime.periode',$periode)
        ->get();

        return view('page/user/m_overtime/slip_overtime_excel',['tb_slip'=>$tb_slip,'judul'=>$judul,'thn'=>$thn,'bln'=>$bln,'akhirbulan'=>$akhirbulan,'hariakhir'=>$hariakhir]);

    }
    function summaryDetail($thn,$bln,$deptid,$id_employee){
        $tb_salary=tb_salary::where('id_employee',$id_employee)->get();
        foreach($tb_salary as $dt){
            $meal=$dt->meal;
        }

        $kalendar=CAL_GREGORIAN;
        $hariawal=date('Y-m-d',strtotime($thn.'-'.$bln.'-01'));
        $hariakhir=cal_days_in_month($kalendar,$bln,$thn);
        $bulan=date('F',strtotime($thn.'-'.$bln.'-01'));
        $akhirbulan=date('Y-m-d',strtotime($thn.'-'.$bln.'-'.$hariakhir));
        $judul="PERIODE 01 ".$bulan." S/D ".$hariakhir." ".$bulan." ".$thn;

        $periode=date('Y-m',strtotime($thn.'-'.$bln.'-01'));

        $tb_sum=DB::table('tb_summary_overtime')
        ->where('id_employee',$id_employee)
        ->where('periode',$periode)
        ->get();

        $tb_slip=DB::table('tb_slip_overtimes')
        ->where('id_employee',$id_employee)
        ->where('periode',$periode)
        ->orderby('rapel_ot','asc')
        ->orderby('date_on','asc')
        ->get();
        //return $tb_sum;

        $temp1=date('-1 days',strtotime($hariawal));

        $tb_ot=tb_overtime_detail::where('id_employee',$id_employee)->where('date_on','<',$hariawal)->where('status','6')->where('isRapel','0')->get();
        return view('page/user/m_overtime/overtimesummary_detail',['tb_sum'=>$tb_sum,'meal'=>$meal,'tb_slip'=>$tb_slip,'dept_id'=>$deptid,'thn'=>$thn,'bln'=>$bln,'judul'=>$judul,'akhirbulan'=>$akhirbulan,'tb_ot'=>$tb_ot,'periode'=>$periode,'hariawal'=>$hariawal,'id_employee'=>$id_employee,'menu'=>'overtimes']);
    }
    function summaryDetail_new($thn,$bln,$deptid,$id_employee){
        $tb_salary=tb_salary::where('id_employee',$id_employee)->get();
        foreach($tb_salary as $dt){
            $meal=$dt->meal;
        }

        $kalendar=CAL_GREGORIAN;
        $hariawal=date('Y-m-d',strtotime($thn.'-'.$bln.'-01'));
        $hariakhir=cal_days_in_month($kalendar,$bln,$thn);
        $bulan=date('F',strtotime($thn.'-'.$bln.'-01'));
        $akhirbulan=date('Y-m-d',strtotime($thn.'-'.$bln.'-'.$hariakhir));
        $judul="PERIODE 01 ".$bulan." S/D ".$hariakhir." ".$bulan." ".$thn;

        $periode=date('Y-m',strtotime($thn.'-'.$bln.'-01'));

        $tb_sum=DB::table('tb_summary_overtime')
        ->where('id_employee',$id_employee)
        ->where('periode',$periode)
        ->get();

        $tb_slip=DB::table('tb_slip_overtimes')
        ->where('id_employee',$id_employee)
        ->where('periode',$periode)
        ->orderby('rapel_ot','asc')
        ->orderby('date_on','asc')
        ->get();
        //return $tb_sum;

        $temp1=date('-1 days',strtotime($hariawal));

        $tb_ot=tb_overtime_detail::where('id_employee',$id_employee)->where('date_on','<',$hariawal)->where('status','6')->where('isRapel','0')->get();
        return view('page/user/m_overtime/overtimesummary_detail',['tb_sum'=>$tb_sum,'meal'=>$meal,'tb_slip'=>$tb_slip,'dept_id'=>$deptid,'thn'=>$thn,'bln'=>$bln,'judul'=>$judul,'akhirbulan'=>$akhirbulan,'tb_ot'=>$tb_ot,'periode'=>$periode,'hariawal'=>$hariawal,'id_employee'=>$id_employee,'menu'=>'overtimes']);
    }
    function addMeal($id_employee,$date_on,$category,$meal,$dept_id){
        $thn=date('Y',strtotime($date_on));
        $bln=date('m',strtotime($date_on));
        $simpan=tb_meal::create([
            'id_employee'=>$id_employee,
            'date_on'=>$date_on,
            'category'=>$category,
            'ref_id'=>0,
            'meal'=>$meal
        ]);
        //$coba=$this->summaryUpdate($thn,$bln,$dept_id);
        //return $coba;
        if($simpan)return redirect()->back();
        //else return "Gagal Add";
    }
    function addRafel(Request $data){
        $result='';
        $slpj=round($data->slpj);
        $periksa=DB::table('tb_slip_overtimes')
        ->where('periode',$data->periode)
        ->where('id_employee',$data->id_employee)
        ->where('ot_start',$data->ot_start)
        ->count();
        if($periksa==0){
            $total=$data->ammount+$data->meal_ot;
            $simpan=DB::table('tb_slip_overtimes')->insert([
                'periode'=>$data->periode,
                'dept_id'=>$data->dept_id,
                'date_on'=>$data->date_on,
                'id_overtime'=>$data->id_overtime,
                'id_overtime_detail'=>$data->id_overtime_detail,
                'id_employee'=>$data->id_employee,
                'slpj'=>$slpj,
                'ot_start'=>$data->ot_start,
                'ot_finish'=>$data->ot_finish,
                'act_hours'=>$data->act_hours,
                'act_convertion'=>$data->act_convertion,
                'ammount'=>$data->ammount,
                'total_bayar'=>$total,
                'rapel_ot'=>'1',
                'status'=>'0'
            ]);
            $thn=date('Y',strtotime($data->periode.'-01'));
            $bln=date('m',strtotime($data->periode.'-01'));
            if($simpan)$this->updateAlt($thn,$bln,$data->dept_id,$data->id_employee);
            $result.='new_slip';
            //if($simpan)return redirect('/Overtimes/Update/Alt/'.$periode.'/'.$data->dept_id.'/'.$data->id_employee);
        }else{
            $total=$data->ammount+$data->meal_ot;
            $simpan=DB::table('tb_slip_overtimes')
            ->where('periode',$data->periode)
            ->where('id_employee',$data->id_employee)
            ->where('ot_start',$data->ot_start)
            ->update([
                'periode'=>$data->periode,
                'dept_id'=>$data->dept_id,
                'date_on'=>$data->date_on,
                'id_overtime'=>$data->id_overtime,
                'id_overtime_detail'=>$data->id_overtime_detail,
                'id_employee'=>$data->id_employee,
                'slpj'=>$slpj,
                'ot_start'=>$data->ot_start,
                'ot_finish'=>$data->ot_finish,
                'act_hours'=>$data->act_hours,
                'act_convertion'=>$data->act_convertion,
                'ammount'=>$data->ammount,
                'total_bayar'=>$total,
                'rapel_ot'=>'1',
                'status'=>'0'
            ]);
            $thn=date('Y',strtotime($data->periode.'-01'));
            $bln=date('m',strtotime($data->periode.'-01'));
            if($simpan){
                $this->updateAlt($thn,$bln,$data->dept_id,$data->id_employee);
            }
            $result.='update_slip';

        }
        $update_detail=DB::table('tb_overtime_details')->where('id',$data->id_overtime_detail)->update(['isRapel'=>'1']);
        return $result;
    }
    function addRafel_new(Request $data){
        $result='';
        $slpj=round($data->slpj);
        //tb_slip_trial
            $periksa=DB::table('tb_slip_overtimes_trial')
            ->where('periode',$data->periode)
            ->where('id_employee',$data->id_employee)
            ->where('ot_start',$data->ot_start)
            ->count();
            if($periksa==0){
                $total=$data->ammount+$data->meal_ot;
                $simpan1=DB::table('tb_slip_overtimes_trial')->insert([
                    'periode'=>$data->periode,
                    'dept_id'=>$data->dept_id,
                    'date_on'=>$data->date_on,
                    'id_overtime'=>$data->id_overtime,
                    'id_overtime_detail'=>$data->id_overtime_detail,
                    'id_employee'=>$data->id_employee,
                    'slpj'=>$slpj,
                    'ot_start'=>$data->ot_start,
                    'ot_finish'=>$data->ot_finish,
                    'act_hours'=>$data->act_hours,
                    'act_convertion'=>$data->act_convertion,
                    'ammount'=>$data->ammount,
                    'total_bayar'=>$total,
                    'rapel_ot'=>'1',
                    'status'=>'0'
                ]);
                $result.='new_slip_trial';
            }else{
                $total=$data->ammount+$data->meal_ot;
                $simpan1=DB::table('tb_slip_overtimes_trial')
                ->where('periode',$data->periode)
                ->where('id_employee',$data->id_employee)
                ->where('ot_start',$data->ot_start)
                ->update([
                    'periode'=>$data->periode,
                    'dept_id'=>$data->dept_id,
                    'date_on'=>$data->date_on,
                    'id_overtime'=>$data->id_overtime,
                    'id_overtime_detail'=>$data->id_overtime_detail,
                    'id_employee'=>$data->id_employee,
                    'slpj'=>$slpj,
                    'ot_start'=>$data->ot_start,
                    'ot_finish'=>$data->ot_finish,
                    'act_hours'=>$data->act_hours,
                    'act_convertion'=>$data->act_convertion,
                    'ammount'=>$data->ammount,
                    'total_bayar'=>$total,
                    'rapel_ot'=>'1',
                    'status'=>'0'
                ]);
                $result.='new_slip_trial';

            }
        //Ens tb_slip_trial
        $update_detail=DB::table('tb_overtime_details')->where('id',$data->id_overtime_detail)->update(['isRapel'=>'1']);
        return $result;
    }
    function addRafel_combine(Request $data){
        $result='';
        $slpj=round($data->slpj);
        $periksa=DB::table('tb_slip_overtimes')
        ->where('periode',$data->periode)
        ->where('id_employee',$data->id_employee)
        ->where('ot_start',$data->ot_start)
        ->count();
        if($periksa==0){
            $total=$data->ammount+$data->meal_ot;
            $simpan=DB::table('tb_slip_overtimes')->insert([
                'periode'=>$data->periode,
                'dept_id'=>$data->dept_id,
                'date_on'=>$data->date_on,
                'id_overtime'=>$data->id_overtime,
                'id_overtime_detail'=>$data->id_overtime_detail,
                'id_employee'=>$data->id_employee,
                'slpj'=>$slpj,
                'ot_start'=>$data->ot_start,
                'ot_finish'=>$data->ot_finish,
                'act_hours'=>$data->act_hours,
                'act_convertion'=>$data->act_convertion,
                'ammount'=>$data->ammount,
                'total_bayar'=>$total,
                'rapel_ot'=>'1',
                'status'=>'0'
            ]);
            $thn=date('Y',strtotime($data->periode.'-01'));
            $bln=date('m',strtotime($data->periode.'-01'));
            if($simpan)$this->updateAlt($thn,$bln,$data->dept_id,$data->id_employee);
            $result.='new_slip';
            //if($simpan)return redirect('/Overtimes/Update/Alt/'.$periode.'/'.$data->dept_id.'/'.$data->id_employee);
        }else{
            $total=$data->ammount+$data->meal_ot;
            $simpan=DB::table('tb_slip_overtimes')
            ->where('periode',$data->periode)
            ->where('id_employee',$data->id_employee)
            ->where('ot_start',$data->ot_start)
            ->update([
                'periode'=>$data->periode,
                'dept_id'=>$data->dept_id,
                'date_on'=>$data->date_on,
                'id_overtime'=>$data->id_overtime,
                'id_overtime_detail'=>$data->id_overtime_detail,
                'id_employee'=>$data->id_employee,
                'slpj'=>$slpj,
                'ot_start'=>$data->ot_start,
                'ot_finish'=>$data->ot_finish,
                'act_hours'=>$data->act_hours,
                'act_convertion'=>$data->act_convertion,
                'ammount'=>$data->ammount,
                'total_bayar'=>$total,
                'rapel_ot'=>'1',
                'status'=>'0'
            ]);
            $thn=date('Y',strtotime($data->periode.'-01'));
            $bln=date('m',strtotime($data->periode.'-01'));
            if($simpan){
                $this->updateAlt($thn,$bln,$data->dept_id,$data->id_employee);
            }
            $result.='update_slip';

        }
        //tb_slip_trial
            $periksa=DB::table('tb_slip_overtimes_trial')
            ->where('periode',$data->periode)
            ->where('id_employee',$data->id_employee)
            ->where('ot_start',$data->ot_start)
            ->count();
            if($periksa==0){
                $total=$data->ammount+$data->meal_ot;
                $simpan1=DB::table('tb_slip_overtimes_trial')->insert([
                    'periode'=>$data->periode,
                    'dept_id'=>$data->dept_id,
                    'date_on'=>$data->date_on,
                    'id_overtime'=>$data->id_overtime,
                    'id_overtime_detail'=>$data->id_overtime_detail,
                    'id_employee'=>$data->id_employee,
                    'slpj'=>$slpj,
                    'ot_start'=>$data->ot_start,
                    'ot_finish'=>$data->ot_finish,
                    'act_hours'=>$data->act_hours,
                    'act_convertion'=>$data->act_convertion,
                    'ammount'=>$data->ammount,
                    'total_bayar'=>$total,
                    'rapel_ot'=>'1',
                    'status'=>'0'
                ]);
                $result.='new_slip_trial';
            }else{
                $total=$data->ammount+$data->meal_ot;
                $simpan1=DB::table('tb_slip_overtimes_trial')
                ->where('periode',$data->periode)
                ->where('id_employee',$data->id_employee)
                ->where('ot_start',$data->ot_start)
                ->update([
                    'periode'=>$data->periode,
                    'dept_id'=>$data->dept_id,
                    'date_on'=>$data->date_on,
                    'id_overtime'=>$data->id_overtime,
                    'id_overtime_detail'=>$data->id_overtime_detail,
                    'id_employee'=>$data->id_employee,
                    'slpj'=>$slpj,
                    'ot_start'=>$data->ot_start,
                    'ot_finish'=>$data->ot_finish,
                    'act_hours'=>$data->act_hours,
                    'act_convertion'=>$data->act_convertion,
                    'ammount'=>$data->ammount,
                    'total_bayar'=>$total,
                    'rapel_ot'=>'1',
                    'status'=>'0'
                ]);
                $result.='new_slip_trial';

            }

        //Ens tb_slip_trial
        $update_detail=DB::table('tb_overtime_details')->where('id',$data->id_overtime_detail)->update(['isRapel'=>'1']);
        return $result;
    }
    function deleteRafel($id){
        $tb_slip=DB::table('tb_slip_overtimes')->where('id',$id)->get();
        foreach($tb_slip as $dt){
            $thn=date('Y',strtotime($dt->periode.'-01'));
            $bln=date('m',strtotime($dt->periode.'-01'));
            $deptid=$dt->dept_id;
            $id_employee=$dt->id_employee;
        }
        $delete=DB::table('tb_slip_overtimes')->where('id',$id)->delete();
        if($delete){
            $this->updateAlt($thn,$bln,$deptid,$id_employee);
            $update_detail=DB::table('tb_overtime_details')->where('id',$data->id_overtime_detail)->update(['isRapel'=>'0']);
            return redirect('/Overtimes/Update/Alt/'.$thn.'/'.$bln.'/'.$deptid.'/'.$id_employee);
        }
    }
    function deleteRafel_new($id){
        $tb_slip=DB::table('tb_slip_overtimes_trial')->where('id',$id)->get();
        foreach($tb_slip as $dt){
            $thn=date('Y',strtotime($dt->periode.'-01'));
            $bln=date('m',strtotime($dt->periode.'-01'));
            $deptid=$dt->dept_id;
            $id_employee=$dt->id_employee;
        }
        $delete=DB::table('tb_slip_overtimes_trial')->where('id',$id)->delete();
        //End tb_slip_trial
        if($delete){
            $this->updateAlt($thn,$bln,$deptid,$id_employee);
            $update_detail=DB::table('tb_overtime_details')->where('id',$data->id_overtime_detail)->update(['isRapel'=>'0']);
            return redirect('/Overtimes/Update/Alt/'.$thn.'/'.$bln.'/'.$deptid.'/'.$id_employee);
        }
    }

    function updateSum(Request $data){
        $total=$data->rapel-$data->pph21+$data->insentifpph21;
        $paid=$data->total_bayars+$total;
        $update=DB::table('tb_summary_overtime')->where('id',$data->id_sum)->update([
            'rapel'=>$data->rapel,
            'pph21'=>$data->pph21,
            'pph21_insentive'=>$data->insentifpph21,
            'total_paid'=>$paid,
        ]);
        if($update)return redirect()->back();
    }
    function updateAlt($thn,$bln,$deptid,$id_employee){
        $admin=Auth::user()->name;
        date_default_timezone_set("Asia/Jakarta");
        $kalendar=CAL_GREGORIAN;
        $thn=$thn;
        $bln=$bln;
        $periode=date('Y-m',strtotime($thn.'-'.$bln.'-01'));
        $hariakhir=cal_days_in_month($kalendar,$bln,$thn);
        $sekarang=date('Y-m-d H:i:s');

        $tb_employee=DB::table('tb_employees')
        ->leftjoin('tb_departments','tb_departments.id','tb_employees.dept_id')
        ->where('tb_employees.id',$id_employee)->where('dept_id',$deptid)
        ->where('delete','0')
        ->get(['tb_employees.*','tb_departments.dept_code','tb_departments.dept_name']);
        foreach ($tb_employee as $dt) {
            $total_bayars=0;$t_hours=0;$t_convertion=0;$t_ammount=0;$t_off=0;$t_ot=0;$t_tl=0;$total_meal=0;
            for ($i=1; $i <= $hariakhir; $i++) {
                $Tgl=date('Y-m-d',strtotime($thn.'-'.$bln.'-'.$i));
                $meal_off=0;
                $meal_ot=0;
                $meal_tl=0;
                $total_bayar=0;
                $tb_meal=tb_meal::where('date_on',$Tgl)->where('id_employee',$dt->id)->get();
                foreach ($tb_meal as $dt2) {
                    if($dt2->category=='meal_off'){$meal_off=$dt2->meal;$t_off=$t_off+$meal_off;}
                    if($dt2->category=='meal_ot'){$meal_ot=$dt2->meal;$t_ot=$t_ot+$meal_ot;}
                    if($dt2->category=='meal_tl'){$meal_tl=$dt2->meal;$t_tl=$t_tl+$meal_tl;}
                    $total_bayar=$meal_off+$meal_ot+$meal_tl;
                    $total_meal=$t_off+$t_ot+$t_tl;
                }
                $update_slip=DB::table('tb_slip_overtimes')->where('id_employee',$dt->id)->where('date_on',$Tgl)->update([
                    'meal_off'=>$meal_off,
                    'meal_ot'=>$meal_ot,
                    'meal_tl'=>$meal_tl,
                    'total_bayar'=>$total_bayar
                ]);
                $tb_overtime_detail=tb_overtime_detail::where('date_on',$Tgl)->where('id_employee',$dt->id)->where('status','6')->get();
                
                foreach ($tb_overtime_detail as $dt2) {
                    $slpj=round($dt2->SLPJ);
                    $t_hours=$t_hours+$dt2->hours_act;
                    $t_convertion=$t_convertion+$dt2->hours_convertion;
                    $t_ammount=$t_ammount+$dt2->ammount;
                    $total_bayar=$total_bayar+$dt2->ammount;
                    $update1=DB::table('tb_slip_overtimes')->where('id_employee',$dt->id)->where('date_on',$Tgl)->update([
                        'id_overtime'=>$dt2->id_ot,
                        'id_overtime_detail'=>$dt2->id,
                        'slpj'=>$slpj,
                        'ot_start'=>$dt2->start_act,
                        'ot_finish'=>$dt2->finish_act,
                        'act_hours'=>$dt2->hours_act,
                        'act_convertion'=>$dt2->hours_convertion,
                        'ammount'=>$dt2->ammount,
                        'total_bayar'=>$total_bayar

                    ]);
                }
                $total_bayars=$total_bayars+$total_bayar;
 
            }

            $tb_rapel=DB::table('tb_slip_overtimes')->where('id_employee',$dt->id)->where('periode',$periode)->where('rapel_ot','1')->get();
            foreach($tb_rapel as $dt3){
                $t_ammount=$t_ammount+$dt3->total_bayar;
                $t_hours=$t_hours+$dt3->act_hours;
                $t_convertion=$t_convertion+$dt3->act_convertion;
                $t_ot=$t_ot+$dt3->meal_ot;
                $total_meal=$total_meal+$t_ot;
                $total_bayars=$t_ammount+$total_meal;
            }

            $tb_summary=DB::table('tb_summary_overtime')->where('id_employee',$dt->id)->where('periode',$periode)->get();
            foreach ($tb_summary as $dt2) {
                $rapel=$dt2->rapel;
                $pph21=$dt2->pph21;
                $pph21_insentive=$dt2->pph21_insentive;
            }
            $total_paid=$rapel-$pph21+$pph21_insentive+$total_bayars;
            $update2=DB::table('tb_summary_overtime')->where('id_employee',$dt->id)->where('periode',$periode)->update([
                't_hours'=>$t_hours,
                't_convertion'=>$t_convertion,
                't_ammount'=>$t_ammount,
                't_off'=>$t_off,
                't_ot'=>$t_ot,
                't_tl'=>$t_tl,
                'total_meal'=>$total_meal,
                'total_bayars'=>$total_bayars,
                'total_paid'=>$total_paid,
                'payroll'=>$admin
            ]);


        }
        //return "Sukses";
        return redirect('/Overtimes/Summary/'.$thn.'/'.$bln.'/'.$deptid.'/'.$id_employee);
    }
    function checkDouble($periode){
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
        
        //return $Tglakhir;
        return view('page/user/m_overtime/overtimecheck',['periode'=>$periode,'Tglawal'=>$Tglawal,'Tglakhir'=>$Tglakhir,'thn'=>$thn,'bln'=>$bln,'menu'=>'overtimes']);
    }
    function showOver($periode){
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
        
        //return $Tglakhir;
        return view('page/user/m_overtime/check_over',['periode'=>$periode,'Tglawal'=>$Tglawal,'Tglakhir'=>$Tglakhir,'thn'=>$thn,'bln'=>$bln,'menu'=>'overtimes']);
    }
    function checkOver(Request $data){
        $periode=$data->periode;

        date_default_timezone_set("Asia/Jakarta");
        $kalendar=CAL_GREGORIAN;
        $sekarang=date('Y-m-d H:I:s');
        $thn=date('Y',strtotime($periode.'-01'));
        $bln=date('m',strtotime($periode.'-01'));
        $hariakhir=cal_days_in_month($kalendar,$bln,$thn);
        $periode_awal=date('Y-m-d',strtotime($periode.'-01'));
        $periode_akhir=date('Y-m-d',strtotime($periode.'-'.$hariakhir));

            
        //Konfigurasi awal
        $year=date('Y');
        $Tgl=date('Y-m-d',strtotime($year.'-01-01'));
        $cek=DB::table('ut_weekyear')->where('start_date','>=',$Tgl)->count();
        if($cek==0){
            $cek2=date('W',strtotime($Tgl));
            if($cek2>1){
                $Tgl=date('Y-m-d',strtotime('+7 days',strtotime($Tgl)));
            }

            $weekday=date('w',strtotime($Tgl));
            $minus='-'.$weekday.' days';
            $Tgl_fix=date('Y-m-d',strtotime($minus,strtotime($Tgl)));

            for($i=1;$i<=52;$i++){
                $awal=date('Y-m-d',strtotime('+1 days',strtotime($Tgl_fix)));
                $akhir=date('Y-m-d',strtotime('+6 days',strtotime($awal)));

                $setup=DB::table('ut_weekyear')->insert([
                    'weekyears'=>$i,
                    'start_date'=>$awal,
                    'finish_date'=>$akhir,
                    'created_at'=>$sekarang
                ]);

                $Tgl_fix=$akhir;
            }
        }
        // Akhir Konfigurasi

        $ut_weekyear=DB::table('ut_weekyear')
        ->where('finish_date','>=',$periode_awal)
        ->where('start_date','<=',$periode_akhir)
        ->get();

        $konten="";
        $no=0;

        foreach($ut_weekyear as $dt){
            $bawah=$dt->start_date;
            $atas=$dt->finish_date;

            $tb_overtime_detail=DB::table('tb_overtime_details')
            ->leftjoin('tb_employees','tb_employees.id','=','tb_overtime_details.id_employee')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
            ->select('id_employee','dept_code','nik','employee_name', DB::raw('SUM(hours_act) as aweek_ot'))
            ->groupBy('id_employee','dept_code','nik','employee_name')
            ->where('start_plan','>=',$bawah)
            ->where('start_plan','<=',$atas)
            ->where('tb_overtime_details.status','<','90')
            ->where('ot_category','1')
            ->havingRaw('SUM(hours_act) > ?', [18])
            ->orderby('aweek_ot','desc')
            ->get();
            //echo "Week ".$dt->weekyears." : ".$dt->start_date.' ~ '.$dt->finish_date.'<br>';
            foreach($tb_overtime_detail as $dt2){
                $no++;
                $konten.="<tr>";
                $konten.="<td>".$no."</td>";
                $konten.="<td>".$dt->weekyears."</td>";
                $konten.="<td>".$dt->start_date."</td>";
                $konten.="<td>".$dt->finish_date."</td>";
                $konten.="<td>".$dt2->nik."</td>";
                $konten.="<td>".$dt2->employee_name."</td>";
                $konten.="<td>".$dt2->dept_code."</td>";
                $konten.="<td>".$dt2->aweek_ot."</td>";
                $konten.="</tr>";
            }
        }
        if($konten=='')$konten.="<tr><td colspan='8' style='text-align:center;'>No Data Available</td></tr>";
        return $konten;

    }

    public function add_summary($periode){
        $admin=Auth::user()->name;
        date_default_timezone_set("Asia/Jakarta");
        $kalendar=CAL_GREGORIAN;
        $sekarang=date('Y-m-d H:i:s');
        //Entry New Data tb_summary_overtime

            $tb_employee=DB::table('tb_employees')
            ->leftjoin('tb_departments','tb_departments.id','tb_employees.dept_id')
            ->where('status','1')
            ->where('tb_departments.isDelete','0')
            ->where('tb_departments.isTrial','1')
            ->get(['tb_employees.*','tb_departments.dept_code','tb_departments.dept_name']);  
            foreach ($tb_employee as $dt) {
                $SLPJ=0;
                $tb_salary=tb_salary::where('id_employee',$dt->id)->where('status','1')->limit(1)->get();
                //return $tb_salary;
                foreach($tb_salary as $dtsal){$SLPJ=round($dtsal->slpj);}
                $check2=DB::table('tb_summary_overtime')->where('periode',$periode)->where('id_employee',$dt->id)->count();
                if($check2==0){
                    DB::table('tb_summary_overtime')->insert([
                        'periode'=>$periode,
                        'dept_id'=>$dt->dept_id,
                        'id_employee'=>$dt->id,
                        'NIK'=>$dt->NIK,
                        'employee_name'=>$dt->employee_name,
                        'divisi'=>$dt->dept_name,
                        'SLPJ'=>$SLPJ,
                        'rapel'=>'0',
                        'pph21'=>'0',
                        'pph21_insentive'=>'0',
                        't_hours'=>'0',
                        't_convertion'=>'0',
                        't_ammount'=>'0',
                        't_off'=>'0',
                        't_ot'=>'0',
                        't_tl'=>'0',
                        'total_meal'=>'0',
                        'total_bayars'=>'0',
                        'total_paid'=>'0',
                        'payroll'=>$admin,
                        'status'=>'0',
                        'created_at'=>$sekarang,
                        'updated_at'=>$sekarang
                    ]);
                }else{
                    DB::table('tb_summary_overtime')->where('periode',$periode)->where('id_employee',$dt->id)->update([
                        'SLPJ'=>$SLPJ,
                        'updated_at'=>$sekarang
                    ]);
                }
            }
        //End New Entry
        return redirect()->back()->with(['success'=>'Success Add Summary']);
    }
    public function add_summary_trial($periode){
        $admin=Auth::user()->name;
        date_default_timezone_set("Asia/Jakarta");
        $kalendar=CAL_GREGORIAN;
        $sekarang=date('Y-m-d H:i:s');
        //Entry New Data tb_summary_overtime

            $tb_employee=DB::table('tb_employees')
            ->leftjoin('tb_departments','tb_departments.id','tb_employees.dept_id')
            ->where('status','1')
            ->where('tb_departments.isDelete','0')
            ->where('tb_departments.isTrial','1')
            ->get(['tb_employees.*','tb_departments.dept_code','tb_departments.dept_name']);  
            foreach ($tb_employee as $dt) {
                $SLPJ=0;
                $tb_salary=tb_salary::where('id_employee',$dt->id)->where('status','1')->limit(1)->get();
                //return $tb_salary;
                foreach($tb_salary as $dtsal){$SLPJ=round($dtsal->slpj);}
                $check2=DB::table('tb_summary_overtime_trial')->where('periode',$periode)->where('id_employee',$dt->id)->count();
                if($check2==0){
                    DB::table('tb_summary_overtime_trial')->insert([
                        'periode'=>$periode,
                        'dept_id'=>$dt->dept_id,
                        'id_employee'=>$dt->id,
                        'NIK'=>$dt->NIK,
                        'employee_name'=>$dt->employee_name,
                        'divisi'=>$dt->dept_name,
                        'SLPJ'=>$SLPJ,
                        'rapel'=>'0',
                        'pph21'=>'0',
                        'pph21_insentive'=>'0',
                        't_hours'=>'0',
                        't_convertion'=>'0',
                        't_ammount'=>'0',
                        't_off'=>'0',
                        't_ot'=>'0',
                        't_tl'=>'0',
                        'total_meal'=>'0',
                        'total_bayars'=>'0',
                        'total_paid'=>'0',
                        'payroll'=>$admin,
                        'status'=>'0',
                        'created_at'=>$sekarang,
                        'updated_at'=>$sekarang
                    ]);
                }else{
                    DB::table('tb_summary_overtime_trial')->where('periode',$periode)->where('id_employee',$dt->id)->update([
                        'SLPJ'=>$SLPJ,
                        'updated_at'=>$sekarang
                    ]);
                }
            }
        //End New Entry
        return redirect()->back()->with(['success'=>'Success Add Summary']);
    }
    function shareSlip($periode){
        date_default_timezone_set("Asia/Jakarta");
        $now=date('Y-m-d H:i:s');
        $update=DB::table('tb_summary_overtime')->where('periode',$periode)->update([
            'send_mail'=>'1',
            'updated_at'=>$now,
        ]);
        if($update)return redirect()->back()->with(['success'=>'Success Share Slip to ESS']);
    }
    function checkVerify($periode){
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

        
        return view('page/user/m_overtime/overtimecheckverify',['periode'=>$periode,'Tglawal'=>$Tglawal,'Tglakhir'=>$Tglakhir,'thn'=>$thn,'bln'=>$bln,'menu'=>'overtimes']);
    }
    function checkSLPJ($periode){
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

        $temp=date('Y-m-d', strtotime('-1 days',strtotime($Tglawal)));
        $periode_lalu= date('Y-m',strtotime($temp));
        
        return view('page/user/m_overtime/overtimecheckspl',['periode'=>$periode,'Tglawal'=>$Tglawal,'Tglakhir'=>$Tglakhir,'thn'=>$thn,'bln'=>$bln,'periode_lalu'=>$periode_lalu,'menu'=>'overtimes']);
    }
    function summaries_dpk($periode,$dept_id){
        $divisi='';
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


            //$this->summaryUpdate($thn,$bln,$dept_id);
            $tb_slip=DB::table('tb_summary_overtime')
            ->select(
                'tb_summary_overtime.dept_id',
                'tb_summary_overtime.divisi',
                'tb_summary_overtime.id_employee',
                'tb_summary_overtime.NIK',
                'tb_summary_overtime.employee_name',
                'tb_summary_overtime.SLPJ',
                'tb_summary_overtime.rapel',
                'tb_summary_overtime.is_dpk',
                DB::raw('SUM(tb_summary_overtime.t_hours) as st_hours,SUM(tb_summary_overtime.t_convertion) as stotal_convertion,SUM(tb_summary_overtime.t_ammount) as st_ammount,SUM(tb_summary_overtime.total_meal) as stotal_meal,SUM(tb_summary_overtime.rapel) as srapel,SUM(tb_summary_overtime.total_bayars) as stotal_bayars,SUM(tb_summary_overtime.pph21) as spph21,SUM(tb_summary_overtime.pph21_insentive) as spph21_insentive,SUM(tb_summary_overtime.total_paid) as stotal_paid')
            )
            ->groupBy(
                'tb_summary_overtime.dept_id',
                'tb_summary_overtime.divisi',
                'tb_summary_overtime.id_employee',
                'tb_summary_overtime.NIK',
                'tb_summary_overtime.employee_name',
                'tb_summary_overtime.SLPJ',
                'tb_summary_overtime.rapel',
                'tb_summary_overtime.is_dpk',
            )
            ->where('tb_summary_overtime.periode',$periode)
            ->where('tb_summary_overtime.is_dpk','1')
            ->orderby('stotal_paid','desc')->get();
            foreach($tb_slip as $dt){$divisi=$dt->divisi;}
            $ntb_slip=DB::table('tb_summary_overtime')->where('total_paid','>','0')->count();  

            //return $ntb_slip;          
            return view('page/user/m_overtime/overtimesummary_all',['divisi'=>$divisi,'periode'=>$periode,'thn'=>$thn,'bln'=>$bln,'dept_id'=>$dept_id,'tb_slip'=>$tb_slip,'ntb_slip'=>$ntb_slip,'sequen'=>'0','lanjut'=>'0','menu'=>'overtimes','is_dpk'=>'1']);
    }

    function GetDataDPK(Request $request){
        $columns = array( 
            0 =>'',
            1 =>'id_overtime', 
            2 =>'ot_category',
            3 =>'date_on',
            4 =>'dept_name' ,
            5 =>'NIK' ,   
            6 =>'employee_name' ,   
            7 =>'reason_ot' ,   
            8 =>'job_ot' , 
            9 =>'customer' ,   
            10 =>'reason' ,   
            11 =>'actual_time',
            12 =>'hours_act',
            13 =>'hours_convertion',
            14 =>'ammount',
            15 =>'bgc',
          );   
    $tb_overtime_detail =  DB::table('tb_overtime_details')
    ->leftjoin('tb_employees','tb_employees.id','=','tb_overtime_details.id_employee')
    ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
    ->select('tb_overtime_details.*','tb_employees.NIK','tb_employees.employee_name','tb_departments.dept_code')
    ->where('tb_employees.position_id','=',29)
    ->get();

    $totalData = $tb_overtime_detail->count();
    $totalFiltered = $totalData;
    $limit = $request->input('length');
    $start = $request->input('start');
    $order = ($request->input('order.0.column')==0 ? $columns[3] : $columns[$request->input('order.0.column')]);
    $dir = ($request->input('order.0.column')==0 ? 'desc' : $request->input('order.0.dir')) ; 

    if(empty($request->input('search.value'))){
        $posts = DB::table('tb_overtime_details')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_overtime_details.id_employee')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->where('tb_employees.position_id','=',29)
        // ->where('tb_overtime_details.id_ot',$id)
        ->offset($start)
        ->limit($limit)
        ->orderBy($order,$dir)
        ->get();
    }else{
        $search = $request->input('search.value');  
        $posts = DB::table('tb_overtime_details')
        ->leftjoin('tb_overtimes','tb_overtimes.id','=','tb_overtime_details.id_ot')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_overtime_details.id_employee')
      
        ->where('tb_employees.position_id','=',29)
        ->where('tb_overtime_details.id_overtime','=',"%$search%")
        ->orwhere('tb_employees.employee_name','=',"%$search%")
        ->orwhere('tb_employees.NIK','=',"%$search%")
        ->orwhere('tb_overtime_details.reason_ot','=',"%$search%")
        ->orwhere('tb_overtime_details.customer','=',"%$search%")
        ->orwhere('tb_overtime_details.date_on','=',"%$search%")
        // ->where('tb_overtime_details.id_ot',$id)
        ->offset($start)
        ->limit($limit)
        ->orderBy($order,$dir)
        ->get();
        $totalFiltered = $posts->count();
    }
    $data = array();
    if(!empty($posts)){
        $no = $start ;
        foreach($posts as $post){
            if($post->status==6)$bgc="<div class='pull-right'><span class='badge bg-green'>Verified</span></div>";
            else if($post->status<90)$bgc="<div class='pull-right'><span class='badge bg-yellow'>Check</span></div>";
            else if($post->status>90)$bgc="<div class='pull-right'><span class='badge bg-red'>Cancel</span></div>";
        $no++; 
        $status = "";
        $nestedData['no'] = $no ; 
        $nestedData['id'] = $post->id ;   
        $nestedData['id_overtime'] = $post->id_overtime ;  
        $nestedData['ot_category'] = $post->ot_category ;   
        $nestedData['date_on'] = $post->date_on ;   
        $nestedData['dept_name'] = $post->dept_name ;   
        $nestedData['NIK'] = $post->NIK ;   
        $nestedData['employee_name'] = $post->employee_name ;   
        $nestedData['reason_ot'] = $post->reason_ot ;   
        $nestedData['job_ot'] = $post->job_ot ;   
        $nestedData['customer'] = $post->customer ;   
        $nestedData['reason'] = $post->reason ;   
        $nestedData['plan_time'] =date('H:i',strtotime($post->start_plan)).' ~ '.date('H:i',strtotime($post->finish_plan));   
        $nestedData['actual_time'] = ($post->sign_after=='1') ? date('H:i',strtotime($post->start_act)).' ~ '.date('H:i',strtotime($post->finish_act)) : '';  
        $nestedData['hours_act'] = $post->hours_act ;   
        $nestedData['hours_convertion'] = $post->hours_convertion ;   
        $nestedData['ammount'] = (request()->user()->hasRole('allowance')||request()->user()->hasRole('info_overtime_dept')) ?  $post->ammount : '';
        $nestedData['bgc'] = $bgc ;   
        }
    }
    $json_data = array(
        "draw"            => intval($request->input('draw')),  
        "recordsTotal"    => intval($totalData),  
        "recordsFiltered" => intval($totalFiltered), 
        "data"            => $data   
        ); 
        echo json_encode($json_data);
    }
    function summary($periode,$dept_id){
        $divisi='';

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



        if($dept_id==0){
            $divisi='';
            $tb_slip=DB::table('tb_summary_overtime')
            ->select(
                'tb_summary_overtime.dept_id',
                'tb_summary_overtime.divisi',
                DB::raw('SUM(tb_summary_overtime.t_hours) as st_hours,SUM(tb_summary_overtime.t_convertion) as stotal_convertion,SUM(tb_summary_overtime.t_ammount) as st_ammount,SUM(tb_summary_overtime.total_meal) as stotal_meal,SUM(tb_summary_overtime.rapel) as srapel,SUM(tb_summary_overtime.total_bayars) as stotal_bayars,SUM(tb_summary_overtime.pph21) as spph21,SUM(tb_summary_overtime.pph21_insentive) as spph21_insentive,SUM(tb_summary_overtime.total_paid) as stotal_paid')
            )
            ->groupBy(
                'tb_summary_overtime.dept_id',
                'tb_summary_overtime.divisi',
            )
            ->where('tb_summary_overtime.periode',$periode)->orderby('stotal_paid','desc')->get();
            return view('page/user/m_overtime/overtimesummaries',['periode'=>$periode,'thn'=>$thn,'bln'=>$bln,'dept_id'=>$dept_id,'tb_slip'=>$tb_slip,'menu'=>'overtimes']);
        }else{
            //$this->summaryUpdate($thn,$bln,$dept_id);
            $tb_slip=DB::table('tb_summary_overtime')
            ->select(
                'tb_summary_overtime.dept_id',
                'tb_summary_overtime.divisi',
                'tb_summary_overtime.id_employee',
                'tb_summary_overtime.NIK',
                'tb_summary_overtime.employee_name',
                'tb_summary_overtime.SLPJ',
                DB::raw('SUM(tb_summary_overtime.t_hours) as st_hours,SUM(tb_summary_overtime.t_convertion) as stotal_convertion,SUM(tb_summary_overtime.t_ammount) as st_ammount,SUM(tb_summary_overtime.total_meal) as stotal_meal,SUM(tb_summary_overtime.rapel) as srapel,SUM(tb_summary_overtime.total_bayars) as stotal_bayars,SUM(tb_summary_overtime.pph21) as spph21,SUM(tb_summary_overtime.pph21_insentive) as spph21_insentive,SUM(tb_summary_overtime.total_paid) as stotal_paid')
            )
            ->groupBy(
                'tb_summary_overtime.dept_id',
                'tb_summary_overtime.divisi',
                'tb_summary_overtime.id_employee',
                'tb_summary_overtime.NIK',
                'tb_summary_overtime.employee_name',
                'tb_summary_overtime.SLPJ',
            )
            ->where('tb_summary_overtime.periode',$periode)->where('dept_id',$dept_id)->orderby('stotal_paid','desc')->get();
            foreach($tb_slip as $dt){$divisi=$dt->divisi;}
            $ntb_slip=DB::table('tb_summary_overtime')->where('dept_id',$dept_id)->where('total_paid','>','0')->count();  

            //return $ntb_slip;          
            return view('page/user/m_overtime/overtimesummary',['divisi'=>$divisi,'periode'=>$periode,'thn'=>$thn,'bln'=>$bln,'dept_id'=>$dept_id,'tb_slip'=>$tb_slip,'ntb_slip'=>$ntb_slip,'sequen'=>'0','lanjut'=>'0','menu'=>'overtimes']);
        }

        //return $tb_slip;
    }

}
