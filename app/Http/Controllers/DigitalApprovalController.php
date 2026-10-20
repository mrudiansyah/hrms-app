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
    public function index($tb_name, $id, $pos)
    {
        if($tb_name=='tb_leaves'){
            $module_name='Employee Leave';
            $data_source=$this->tb_leaves($id,$pos);
        }else{
            $module_name=$tb_name;
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
        }elseif($pos=='2'){
            $id_employee=DB::table('tb_leaves')->where('id',$id)->value('approved');
            $data['approve_date']=DB::table('tb_leaves')->where('id',$id)->value('date_approved');
            $data['approval_status']=DB::table('tb_leaves')->where('id',$id)->value('status_approved');
        }elseif($pos=='3'){
            $id_employee=DB::table('tb_leaves')->where('id',$id)->value('approved2');
            $data['approve_date']=DB::table('tb_leaves')->where('id',$id)->value('date_approved2');
            $data['approval_status']=DB::table('tb_leaves')->where('id',$id)->value('status_approved2');
        }elseif($pos=='4'){
            $id_employee=DB::table('tb_leaves')->where('id',$id)->value('legalized');
            $data['approve_date']=DB::table('tb_leaves')->where('id',$id)->value('date_legalized');
            $data['approval_status']=DB::table('tb_leaves')->where('id',$id)->value('status_legalized');
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

}
