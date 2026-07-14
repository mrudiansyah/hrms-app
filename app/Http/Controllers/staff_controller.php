<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class staff_controller extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }
    function index(){
        $tb_user=DB::table('users')->leftjoin('tb_emails','tb_emails.email_address','=','users.email')->get(['users.*','tb_emails.id_employee']);
        $tb_dept=DB::table('tb_departments')->get();
        return view('page/admin/staffs',['tb_user'=>$tb_user,'tb_dept'=>$tb_dept,'menu'=>'user_management']);
    }
    function selectUser(Request $data){
        $user_id=$data->user_id;
        $tb_admin=DB::table('tb_admins')->leftjoin('tb_departments','tb_departments.id','=','tb_admins.dept_id')->where('id_employee',$user_id)->orderby('dept_id','asc')->get(['tb_admins.*','tb_departments.dept_code']);
        $konten="";
        foreach($tb_admin as $dt){
            $konten.="<tr class='showcycle'><td>".$dt->dept_id."</td>"."<td>".$dt->dept_code."<div class='pull-right'>";
            $konten.="<button title='Remove Role' type='button' class='removerole' data-roleid='".$dt->id."'><i class='fa fa-angle-double-right'></i></button>";
            $konten.="</div></td></tr>";
        }
        return $konten;
    }
    function addDept(Request $data){
        $user_id=$data->user_id;
        $dept_id=$data->role_id;
        $periksa=DB::table('tb_admins')->where([['id_employee',$user_id],['dept_id',$dept_id]])->count();
        if($periksa==0)
        $update=DB::table('tb_admins')->insert(['id_employee' => $user_id,'dept_id'=>$dept_id]);
        $tb_admin=DB::table('tb_admins')->leftjoin('tb_departments','tb_departments.id','=','tb_admins.dept_id')->where('id_employee',$user_id)->orderby('dept_id','asc')->get(['tb_admins.*','tb_departments.dept_code']);
        $konten="";
        foreach($tb_admin as $dt){
            $konten.="<tr class='showcycle'><td>".$dt->dept_id."</td>"."<td>".$dt->dept_code."<div class='pull-right'>";
            $konten.="<button title='Remove Role' type='button' class='removerole' data-roleid='".$dt->id."'><i class='fa fa-angle-double-right'></i></button>";
            $konten.="</div></td></tr>";
        }
        return $konten;
    }
    function removeDept(Request $data){
        $user_id=$data->user_id;
        $id=$data->role_id;
        $delete=DB::table('tb_admins')->where('id',$id)->delete();
        $tb_admin=DB::table('tb_admins')->leftjoin('tb_departments','tb_departments.id','=','tb_admins.dept_id')->where('id_employee',$user_id)->orderby('dept_id','asc')->get(['tb_admins.*','tb_departments.dept_code']);
        $konten="";
        foreach($tb_admin as $dt){
            $konten.="<tr class='showcycle'><td>".$dt->dept_id."</td>"."<td>".$dt->dept_code."<div class='pull-right'>";
            $konten.="<button title='Remove Role' type='button' class='removerole' data-roleid='".$dt->id."'><i class='fa fa-angle-double-right'></i></button>";
            $konten.="</div></td></tr>";
        }
        return $konten;

    }

}
