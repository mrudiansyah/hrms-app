<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DateTime;
use Auth;


class admin_freeday extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }
    function index(){
        $tb_freeday=DB::table('tb_freedays')->orderby('date_off','desc')->get();
        return view('page/admin/m_calendar/freeday',['tb_freeday'=>$tb_freeday,'menu'=>'calendar']);
    }
    function createData(Request $data){
        $this->validate($data,[
            'date_off'=>'required',
            'category'=>'required'
        ]);
        if($data->category=='Leave'){
            $tb_leave=DB::table('tb_employee_leaves')->where('end','<',$data->date_off)->where('status','1')->get();
            foreach($tb_leave as $dt){

                $Thnawal=date('Y',strtotime($dt->start));
                $Thnstart=$Thnawal+1;
                $Thnend=$Thnstart+1;

                $Bln=date('m-d',strtotime($dt->start));
                if($Bln=='02-29')$Bln='02-28';
                $Periode_awal=$Thnstart.'-'.$Bln;
                $Periode_akhir_temp=$Thnend.'-'.$Bln;
                $Periode_akhir = date('Y-m-d', strtotime("-1 day", strtotime($Periode_akhir_temp)));
                $Periode_extend=date('Y-m-d', strtotime("+6 month", strtotime($Periode_akhir)));
                    
                if($dt->outstanding<0)$kurang=$dt->outstanding*-1;
                else $kurang=0;
                $outstanding=12-$kurang;
                //Revisi due to double employee_leave
                $check=DB::table('tb_employee_leaves')->where('id_employee',$dt->id_employee)->where('start','<=',$data->date_off)->where('end','>=',$data->date_off)->where('status','1')->count();
                if($check==0){
                    $create=DB::table('tb_employee_leaves')->insert([
                        'id_employee'=>$dt->id_employee,
                        'year'=>$Thnstart,
                        'start'=>$Periode_awal,
                        'end'=>$Periode_akhir,
                        'extend'=>$Periode_extend,
                        'sisa'=>'0',
                        'kurang'=>$kurang,
                        'allowance'=>'12',
                        'used'=>'0',
                        'outstanding'=>$outstanding,
                        'remark'=>'By Calender Setup',
                        'status'=>'1'
                    ]);
                }
            }
        }
        if($data->sysid>0){
            $simpan=DB::table('tb_freedays')->where('id',$data->sysid)->update([
                'date_off'=>$data->date_off,
                'category'=>$data->category,
                'description'=>$data->description
            ]);
        }else{
            $simpan=DB::table('tb_freedays')->insert([
                'date_off'=>$data->date_off,
                'category'=>$data->category,
                'description'=>$data->description
            ]);
        }
        if($simpan){
            if($data->category=='Leave'){
                date_default_timezone_set("Asia/Bangkok");
                $kalendar=CAL_GREGORIAN;
                $Tgl=date('Y-m-d');
                $Now=date('Y-m-d H:i:s');
                $admin=Auth::user()->name;
                $start_working=date('Y-m-d',strtotime('+1 days',strtotime($data->date_off)));
        
                $tb_employee_leave=DB::table('tb_employee_leaves')->where('start','<=',$data->date_off)->where('end','>=',$data->date_off)->where('status','1')->get();
                foreach($tb_employee_leave as $dt){
                    $tb_leave=DB::table('tb_leaves')->where('id_employee',$dt->id_employee)->where('start_leave',$data->date_off)->count();
                    if($tb_leave==0){
                        $create=DB::table('tb_leaves')->insert([
                            'id_leave'=>$dt->id,
                            'doc_date'=>$Tgl,
                            'id_employee'=>$dt->id_employee,
                            'category'=>'annual',
                            'start_leave'=>$data->date_off,
                            'finish_leave'=>$data->date_off,
                            'leave_count'=>'1',
                            'reason'=>'Mass Leave',
                            'remark'=>$data->description,
                            'approved'=>'122',
                            'approved2'=>'0',
                            'legalized'=>'122',
                            'status_requested'=>'1',
                            'status_approved'=>'1',
                            'status_legalized'=>'1',
                            'status_reported'=>'1',
                            'admin'=>$admin,
                            'status'=>'0',
                            'created_at'=>$Now,
                            'updated_at'=>$Now
                        ]);
                        if($create){
                            $used=$dt->used+1;
                            $outstanding=$dt->outstanding-1;
                            $update=DB::table('tb_employee_leaves')->where('id',$dt->id)->update([
                                'used'=>$used,
                                'outstanding'=>$outstanding,
                                'updated_at'=>$Now
                            ]);
                        }
                    }
                }
            }
            $tgl=date('d',strtotime($data->date_off));
            $kolom='D'.$tgl;
            $periode=date('Y-m',strtotime($data->date_off));
            if($data->category!='Working'){
                if($data->category=='Leave'){
                    $update=DB::table('tb_work_entries')->where('periode',$periode)->where('plan_actual','actual')->update([
                        $kolom=>'53'
                    ]);
                }else{
                    $update=DB::table('tb_work_entries')->where('periode',$periode)->update([
                        $kolom=>'0'
                    ]);
                }
            }
            $teks=$data->employee_name.' berhasil disimpan';
            return redirect('/Admin/Freeday')->with(['success' => $teks]);
        }
    }
    function deleteData($id){
        $tb_freeday=DB::table('tb_freedays')->where('id',$id)->get();
        foreach($tb_freeday as $dt2){
            if($dt2->category=='Leave'){
                $tb_employee_leave=DB::table('tb_employee_leaves')->get();
                foreach($tb_employee_leave as $dt){
                    $delete=$tb_leave=DB::table('tb_leaves')->where('id_employee',$dt->id_employee)->where('start_leave',$dt2->date_off)->where('remark',$dt2->description)->delete();
                    if($delete){
                        date_default_timezone_set("Asia/Bangkok");
                        $Now=date('Y-m-d H:i:s');
                        $used=$dt->used-1;
                        $outstanding=$dt->outstanding+1;
                        $update=DB::table('tb_employee_leaves')->where('id',$dt->id)->update([
                            'used'=>$used,
                            'outstanding'=>$outstanding,
                            'updated_at'=>$Now
                        ]);
                    }
                }
            }

        }
        $delete=DB::table('tb_freedays')->where('id',$id)->delete();
        if($delete)
        return redirect('/Admin/Freeday')->with(['success' => 'Delete data berhasil']);
    }

}
