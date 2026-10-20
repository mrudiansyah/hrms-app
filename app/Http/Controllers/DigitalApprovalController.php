<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DateTime;
use Session;
use Auth;
use PDF;
use App\Http\Controllers\mail_controller;
use image;
use Illuminate\Support\Facades\Storage;

class DigitalApprovalController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }
    public function index($table, $id, $pos)
    {
        $tables = [
            '1' => 'tb_leaves',
            '2' => 'tb_izins',
            '3' => 'tb_overtimes',
            '4' => 'tb_overtime_spv',
        ];

        if (!array_key_exists($table, $tables)) {
            abort(403, 'Forbidden');
        }

        $tb_name = $tables[$table];
        if($tb_name=='tb_leaves'){
            $module_name='Employee Leave';
            $data_source=$this->tb_leaves($id,$pos);
        }elseif($tb_name=='tb_izins'){
            $module_name='Employee Permit';
            $data_source=$this->tb_izins($id,$pos);
        }elseif($tb_name=='tb_overtimes'){
            $module_name='Overtime';
            $data_source=$this->tb_overtimes($id,$pos);
        }elseif($tb_name=='tb_overtime_spv'){
            $module_name='Assignment';
            $data_source=$this->tb_overtime_spv($id,$pos);
        }else{
            $module_name=$tb_name;
            $data_source='';
        }
        $tb1=DB::table($tb_name)->where('id',$id)->get();
        return view('page.approval.index', [
            'tb_name' => $module_name,
            'id' => $id,
            'pos' => $pos,
            'data'=>$data_source,
            'menu' => 'approval',
            'submenu' => 'verification',
        ]);
    }
    function tb_leaves($id,$pos){
        $tb1=DB::table('tb_leaves')->where('id',$id)->get();
        foreach($tb1 as $dt1){
            $data['created_at']=$dt1->created_at;
            $data['created_by']=$dt1->admin;            
        }
        if($pos=='1'){
            $id_employee=DB::table('tb_leaves')->where('id',$id)->value('id_employee');
            $data['approve_date']=DB::table('tb_leaves')->where('id',$id)->value('created_at');
            $data['approval_status']=DB::table('tb_leaves')->where('id',$id)->value('status_requested');
            $data['pos_mame']='Requestor';
        }elseif($pos=='2'){
            $id_employee=DB::table('tb_leaves')->where('id',$id)->value('approved');
            $data['approve_date']=DB::table('tb_leaves')->where('id',$id)->value('date_approved');
            $data['approval_status']=DB::table('tb_leaves')->where('id',$id)->value('status_approved');
            $data['pos_mame']='Direct Leader';
        }elseif($pos=='3'){
            $id_employee=DB::table('tb_leaves')->where('id',$id)->value('approved2');
            $data['approve_date']=DB::table('tb_leaves')->where('id',$id)->value('date_approved2');
            $data['approval_status']=DB::table('tb_leaves')->where('id',$id)->value('status_approved2');
            $data['pos_mame']='Second Level Approver';
        }elseif($pos=='4'){
            $id_employee=DB::table('tb_leaves')->where('id',$id)->value('legalized');
            $data['approve_date']=DB::table('tb_leaves')->where('id',$id)->value('date_legalized');
            $data['approval_status']=DB::table('tb_leaves')->where('id',$id)->value('status_legalized');
            $data['pos_mame']='Legalization Officer';
        }
        $tb_employee=DB::table('tb_employees as tb1')->leftjoin('tb_positions as tb2','tb1.position_id','=','tb2.id')->where('tb1.id',$id_employee)->get(['tb1.NIK','tb1.employee_name','tb2.position_name']);
        foreach($tb_employee as $dt){
            $data['NIK']=$dt->NIK;
            $data['employee_name']=$dt->employee_name;
            $data['position_name']=$dt->position_name;
        }
        $data['link']=url('/Leave/Approves/0/0');
        return $data;
    }
    function tb_izins($id,$pos){
        $tb1=DB::table('tb_izins')->where('id',$id)->get();
        foreach($tb1 as $dt1){
            $data['created_at']=$dt1->created_at;
            $data['created_by']=$dt1->admin;            
        }
        if($pos=='1'){
            $id_employee=DB::table('tb_izins')->where('id',$id)->value('id_employee');
            $data['approve_date']=DB::table('tb_izins')->where('id',$id)->value('created_at');
            $data['approval_status']=DB::table('tb_izins')->where('id',$id)->value('status_requested');
            $data['pos_mame']='Requestor';
        }elseif($pos=='2'){
            $id_employee=DB::table('tb_izins')->where('id',$id)->value('disetujui');
            $data['approve_date']=DB::table('tb_izins')->where('id',$id)->value('date_disetujui');
            $data['approval_status']=DB::table('tb_izins')->where('id',$id)->value('status_disetujui');
            $data['pos_mame']='Direct Leader';
        }elseif($pos=='3'){
            $id_employee=DB::table('tb_izins')->where('id',$id)->value('personalia');
            $data['approve_date']=DB::table('tb_izins')->where('id',$id)->value('date_personalia');
            $data['approval_status']=DB::table('tb_izins')->where('id',$id)->value('status_personalia');
            $data['pos_mame']='Personalia';
        }elseif($pos=='4'){
            $id_employee=DB::table('tb_izins')->where('id',$id)->value('scurity');
            $data['approve_date']=DB::table('tb_izins')->where('id',$id)->value('date_scurity');
            $data['approval_status']=DB::table('tb_izins')->where('id',$id)->value('status_scurity');
            $data['pos_mame']='Security';
        }
        $tb_employee=DB::table('tb_employees as tb1')->leftjoin('tb_positions as tb2','tb1.position_id','=','tb2.id')->where('tb1.id',$id_employee)->get(['tb1.NIK','tb1.employee_name','tb2.position_name']);
        foreach($tb_employee as $dt){
            $data['NIK']=$dt->NIK;
            $data['employee_name']=$dt->employee_name;
            $data['position_name']=$dt->position_name;
        }
        $data['link']=url('/Permit/Approves/0/0');
        return $data;
    }
    function tb_overtimes($id,$pos){
        $tb1=DB::table('tb_overtimes')->where('id',$id)->get();
        foreach($tb1 as $dt1){
            $data['created_at']=$dt1->created_at;
            $data['created_by']=$dt1->admin;            
        }
        if($pos=='1'){
            $id_employee=DB::table('tb_overtimes')->where('id',$id)->value('diperintah');
            $data['approve_date']=DB::table('tb_overtimes')->where('id',$id)->value('date_diperintah');
            $data['approval_status']=DB::table('tb_overtimes')->where('id',$id)->value('status_diperintah');
            $data['pos_mame']='Requestor';
        }elseif($pos=='2'){
            $id_employee=DB::table('tb_overtimes')->where('id',$id)->value('disetujui');
            $data['approve_date']=DB::table('tb_overtimes')->where('id',$id)->value('date_disetujui');
            $data['approval_status']=DB::table('tb_overtimes')->where('id',$id)->value('status_disetujui');
            $data['pos_mame']='Approver';
        }elseif($pos=='3'){
            $id_employee=DB::table('tb_overtimes')->where('id',$id)->value('diketahui');
            $data['approve_date']=DB::table('tb_overtimes')->where('id',$id)->value('date_diketahui');
            $data['approval_status']=DB::table('tb_overtimes')->where('id',$id)->value('status_diketahui');
            $data['pos_mame']='Aknowledgement';
        }elseif($pos=='4'){
            $id_employee=DB::table('tb_overtimes')->where('id',$id)->value('dicatat');
            $data['approve_date']=DB::table('tb_overtimes')->where('id',$id)->value('date_dicatat');
            $data['approval_status']=DB::table('tb_overtimes')->where('id',$id)->value('status_dicatat');
            $data['pos_mame']='Personalia';
        }elseif($pos=='5'){
            $id_employee=DB::table('tb_overtimes')->where('id',$id)->value('approve');
            $data['approve_date']=DB::table('tb_overtimes')->where('id',$id)->value('date_approve');
            $data['approval_status']=DB::table('tb_overtimes')->where('id',$id)->value('status_approve');
            $data['pos_mame']='Personalia';
        }elseif($pos=='6'){
            $id_employee=DB::table('tb_overtimes')->where('id',$id)->value('paid');
            $data['approve_date']=DB::table('tb_overtimes')->where('id',$id)->value('date_paid');
            $data['approval_status']=DB::table('tb_overtimes')->where('id',$id)->value('status_paid');
            $data['pos_mame']='Verified By HR';
        }
        $tb_employee=DB::table('tb_employees as tb1')->leftjoin('tb_positions as tb2','tb1.position_id','=','tb2.id')->where('tb1.id',$id_employee)->get(['tb1.NIK','tb1.employee_name','tb2.position_name']);
        foreach($tb_employee as $dt){
            $data['NIK']=$dt->NIK;
            $data['employee_name']=$dt->employee_name;
            $data['position_name']=$dt->position_name;
        }
        $data['link']=url('/ApprovalSPL');
        return $data;
    }
    function tb_overtime_spv($id,$pos){
        $tb1=DB::table('tb_overtime_spv')->where('id',$id)->get();
        foreach($tb1 as $dt1){
            $data['created_at']=$dt1->created_at;
            $data['created_by']=$dt1->admin;            
        }
        if($pos=='1'){
            $id_employee=DB::table('tb_overtime_spv')->where('id',$id)->value('assigned');
            $data['approve_date']=DB::table('tb_overtime_spv')->where('id',$id)->value('assigned_status_date');
            $data['approval_status']=DB::table('tb_overtime_spv')->where('id',$id)->value('assigned_status');
            $data['pos_mame']='Requestor';
        }elseif($pos=='2'){
            $id_employee=DB::table('tb_overtime_spv')->where('id',$id)->value('approved1');
            $data['approve_date']=DB::table('tb_overtime_spv')->where('id',$id)->value('approved1_status_date');
            $data['approval_status']=DB::table('tb_overtime_spv')->where('id',$id)->value('approved1_status');
            $data['pos_mame']='Approver1';
        }elseif($pos=='3'){
            $id_employee=DB::table('tb_overtime_spv')->where('id',$id)->value('approved2');
            $data['approve_date']=DB::table('tb_overtime_spv')->where('id',$id)->value('approved2_status_date');
            $data['approval_status']=DB::table('tb_overtime_spv')->where('id',$id)->value('approved2_status');
            $data['pos_mame']='Approver2';
        }elseif($pos=='4'){
            $id_employee=DB::table('tb_overtime_spv')->where('id',$id)->value('conducted');
            $data['approve_date']=DB::table('tb_overtime_spv')->where('id',$id)->value('conducted_status_date');
            $data['approval_status']=DB::table('tb_overtime_spv')->where('id',$id)->value('conducted_status');
            $data['pos_mame']='Employee';
        }elseif($pos=='5'){
            $id_employee=DB::table('tb_overtime_spv')->where('id',$id)->value('legalized');
            $data['approve_date']=DB::table('tb_overtime_spv')->where('id',$id)->value('legalized_status_date');
            $data['approval_status']=DB::table('tb_overtime_spv')->where('id',$id)->value('legalized_status');
            $data['pos_mame']='Personalia';
        }
        $tb_employee=DB::table('tb_employees as tb1')->leftjoin('tb_positions as tb2','tb1.position_id','=','tb2.id')->where('tb1.id',$id_employee)->get(['tb1.NIK','tb1.employee_name','tb2.position_name']);
        foreach($tb_employee as $dt){
            $data['NIK']=$dt->NIK;
            $data['employee_name']=$dt->employee_name;
            $data['position_name']=$dt->position_name;
        }
        $data['link']=url('/Assigment/Approvals/0');
        return $data;
    }

}
