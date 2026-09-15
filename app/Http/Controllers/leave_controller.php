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

class leave_controller extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }
    function index(){
        date_default_timezone_set("Asia/Bangkok");
        $kalendar=CAL_GREGORIAN;
        $Tgl=date('Y-m-d');
        $jam=date('Y-m-d H:i:s');
        //Daily Refresh
            // $qty_record=DB::connection('mysql')->table('tb_update_record')->where('record_date',$Tgl)->count();
            // if($qty_record==0){
            //     $tb_record=DB::connection('mysql')->table('tb_update_record')->insert([
            //         'tabel_name'=>'tb_employee_leaves',
            //         'record_date'=>$Tgl,
            //     ]);
            //     return redirect('/Leave/Update');
            // }
        //Daily Refresh End
        $next_month=date('Y-m-d',strtotime('+1 months',strtotime($Tgl)));

        $nama=Auth::user()->name;
        $email=Auth::user()->email;
        $id_user = null;
        $cek1 = DB::table('tb_emails')->where('email_address',$email)->first();
        if ($cek1) {
            $id_user = $cek1->id_employee;
        }

        $tb_admins = [];
        if ($id_user) {
            $tb_admins = DB::table('tb_admins')
                ->where('id_employee', $id_user)
                ->pluck('dept_id')
                ->toArray();
        }

        $tb_leave = DB::table('tb_employees')
            ->leftJoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
            ->leftJoin('tb_employee_leaves','tb_employee_leaves.id_employee','=','tb_employees.id')
            ->select([
                'tb_employees.id as idemployee',
                'tb_employees.NIK',
                'tb_employees.employee_name',
                'tb_employees.join_date',
                'tb_departments.dept_code',
                'tb_employee_leaves.*'
            ])
            ->where('tb_employees.status','1')
            ->where('tb_employees.position_id','<>','19')
            ->where('tb_employee_leaves.status','1');

        if (!empty($tb_admins)) {
            $tb_leave->where(function($query) use ($tb_admins) {
                $query->where('tb_employees.dept_id','0')
                    ->orWhereIn('tb_employees.dept_id', $tb_admins);
            });
        } else {
            $tb_leave->where('tb_employees.dept_id','0');
        }

        $tb_leave = $tb_leave
            ->orderBy('tb_employees.employee_name','asc')
            ->orderBy('tb_employee_leaves.year','desc')
            ->get();


            
        $tb_utility=DB::table('tb_utilities')->where('atribut','limit_leave_status')->get(['status']);
        foreach($tb_utility as $dt){
            $limit_leave_status=$dt->status;
        }
        return view('page/admin/m_leave/leave',['tb_leave'=>$tb_leave,'next_month'=>$next_month,'sekarang'=>$Tgl,'limit_leave_status'=>$limit_leave_status,'menu'=>'leave','submenu'=>'apply']);
    }
    #region SelectEmplouee Detail Leave 3month Count
    function selectEmployee($id_leave){
        $str = explode("_", $id_leave) ;   
        if ($str[0] != "0") {  
            // $id_leave = Crypt::decryptString(str_replace("-", "=", $str[0])) ;    
            $tb_leave=DB::table('tb_leaves')->select(DB::raw('SUM(leave_count) as days'))->where('id_leave',$id_leave)->get();
            
            foreach($tb_leave as $dt){$days=$dt->days;}
            if($days=='')$days=0;
            $tb_employee_leave=DB::table('tb_employee_leaves')->where('id',$id_leave)->get();
            foreach($tb_employee_leave as $dt){
                $id=$dt->id_employee;
                $outstanding=$dt->allowance-$days;
                $update=DB::table('tb_employee_leaves')->where('id',$dt->id)->update([
                    'used'=>$days,
                    'outstanding'=>$outstanding,
                ]);
            }
            //return $tb_employee_leave;
            date_default_timezone_set("Asia/Bangkok");
            $kalendar=CAL_GREGORIAN;
            $Tgl=date('Y-m-d');
            $sekarang=date('Y-m-d H:i:s');
            $MinTgl=date('Y-m-d',strtotime('1 weeks',strtotime($Tgl)));

            $tb_employee=DB::table('tb_employees')->where('id',$id)->get();
            foreach($tb_employee as $dt){
                $tgl1 = new DateTime($dt->join_date);
                $tgl2 = new DateTime($Tgl);
                $diffdays = $tgl2->diff($tgl1)->days;
                $diffyears=Floor($diffdays/365);
                $Thnawal=date('Y',strtotime($dt->join_date));
                $Thnstart=$Thnawal+$diffyears;
                $Thnend=$Thnstart+1;
                $Bln=date('m-d',strtotime($dt->join_date));
                if($Bln=='02-29')$Bln='02-28';
                $Periode_awal=$Thnstart.'-'.$Bln;
                $Periode_akhir_temp=$Thnend.'-'.$Bln;
                $Periode_akhir = date('Y-m-d', strtotime("-1 day", strtotime($Periode_akhir_temp)));
                $Periode_extend=date('Y-m-d', strtotime("+6 month", strtotime($Periode_akhir)));
            }

            //$tb_employee_freeday=tb_employee_freeday::where([['id_employee',$id],['date_off','>=',$Periode_awal],['date_off','<=',$Periode_extend],['description','Mass Leave']])->count();        
            $tb_employee_leave=DB::table('tb_employee_leaves')->where([['id_employee',$id],['end','>=',$Tgl]])->count();
            //return $tb_employee_leave;
            if($diffyears>=1)$jatah='12';
            else $jatah='0';
            //$kurang=$tb_employee_freeday;
            $tb_employee_freeday=0;
            $kurang=0;
            $outstanding=$jatah-$kurang;
            //$outstanding=1;
            if(request()->user()->hasRole('allowance'))$status='1';
            else $status='0';

            if($tb_employee_leave==0){
                DB::table('tb_employee_leaves')->insert([
                    'id_employee'=>$id,
                    'year'=>$Thnstart,
                    'start'=>$Periode_awal,
                    'end'=>$Periode_akhir,
                    'extend'=>$Periode_extend,
                    'sisa'=>'0',
                    'kurang'=>$kurang,
                    'allowance'=>$jatah,
                    'used'=>$tb_employee_freeday,
                    'outstanding'=>$outstanding,
                    'remark'=>'Open Form',
                    'status'=>$status,
                    'admin'=>'Auto'
                ]);
            }
            //else{if(request()->user()->hasRole('allowance'))tb_employee_leave::where('id_employee',$id)->update(['status'=>$status]);}

            //$tb_employee=tb_employee::all();
            $tb_employee_leave=DB::table('tb_employee_leaves')
            ->leftjoin('tb_employees','tb_employees.id','=','tb_employee_leaves.id_employee')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
            ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
            ->where([['tb_employee_leaves.id',$id_leave]])
            ->orderby('created_at','asc')
            ->limit(1)
            ->get(['tb_employee_leaves.*','tb_employees.NIK','tb_employees.employee_name','tb_departments.dept_name','tb_positions.position_name','tb_employees.leader_id']);
            //return $tb_employee_leave;
            $leader_name='';
            $id_posisi='';
            
            foreach($tb_employee_leave as $dt){
                $status=$dt->status;
                $id_cuti=$dt->id;
                $leader_id=$dt->leader_id;
                $tb_leader=DB::table('tb_employees')->where('id',$leader_id)->get();
                foreach($tb_leader as $dt2){
                    $leader_name=$dt2->employee_name;
                    $id_posisi=$dt2->position_id;
                    $leader_id2=$dt2->leader_id;
                }
                
                // $tb_posisi=DB::table('tb_positions')->where('id',$id_posisi)->get('position_index');
                // foreach($tb_posisi as $dts){
                //     if($dts->position_index<3){
                //         $tb_leader2=tb_employee::where('id',$leader_id2)->get();
                //         foreach($tb_leader2 as $dt3){
                //             $leader_id=$dt3->leader_id;
                //             $leader_name=$dt3->employee_name;
                //         }
                //     }
                // }

                $id_leave=$dt->id;
                $tb_anual=DB::table('tb_leaves')
                ->leftjoin('tb_employees','tb_employees.id','=','tb_leaves.approved')
                ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_leaves.approved2')
                ->where('category','annual')
                //->where('start_leave','>=',$Periode_awal)
                ->where('id_leave',$id_leave)
                ->get(['tb_leaves.*','tb_employees.employee_name as approved_name','tb_employees1.employee_name as approved1_name']);
                
                $tb_docter=DB::table('tb_leaves')->where('id_employee',$dt->id_employee)->where('category','docter')->orderby('doc_date','desc')->take(6)->get();
                //return $tb_docter;
                $tb_special=DB::table('tb_leaves')->where('id_employee',$dt->id_employee)->where('category','special')->orderby('doc_date','desc')->take(6)->get();
                //$anual_count=tb_leave::where('id_leave',$id_leave)->where('category','annual')->count();
                $anual_count=DB::table('tb_leaves')->where('id_leave',$id_leave)
                ->where('category','annual')
                ->where('status_approved','<>','2')
                ->where('status_approved2','<>','2')
                ->where('status_legalized','<>','2')
                ->count();
                $tb_anual_count=DB::table('tb_leaves')->select('id_leave', DB::raw('SUM(leave_count) as anual_count'))
                ->groupby('id_leave')
                ->where('id_leave',$id_leave)
                ->where('category','annual')
                ->where('status_approved','<>','2')
                ->where('status_approved2','<>','2')
                ->where('status_legalized','<>','2')
                ->get();
                foreach($tb_anual_count as $dt9){
                    $anual_count=$dt9->anual_count;
                }
                $tb_employee_leave_update=DB::table('tb_employee_leaves')->where('id',$id_leave)->get();
                foreach($tb_employee_leave_update as $dt){
                    $allowance=$dt->allowance;
                    $osbaru=$allowance-$anual_count;
                    $update=DB::table('tb_employee_leaves')->where('id',$id_leave)->update(['used'=>$anual_count,'outstanding'=>$osbaru]);
                }
                $sisa_count = 0;
                $sumSisa=DB::table('tb_leaves as a')
                ->leftjoin('tb_employee_leaves as b','a.id_leave','=','b.id')
                ->where('category','annual')
                ->whereRaw('a.start_leave < DATEADD(MONTH, 3, b.start)')
                //->where('start_leave','>=',$Periode_awal)
                ->where('id_leave',$id_leave)
                ->selectRaw('b.id,sum(a.leave_count) as leave_count')
                ->groupBy('b.id')
                ->get();
                foreach($sumSisa as $d){
                    $sisa_count = $d->leave_count;
                }
            }
            //return $tb_leave;
            //Leader Code
            $code=0;
            $qty_leave_code=DB::table('tb_leave_code')->where('id_employee',$id)->count();
            if($qty_leave_code>0){
                $qty_open_code=DB::table('tb_leave_code')->where('id_employee',$id)->where('status','0')->count();
                if($qty_open_code==0){
                    $code=rand(1000,9999);
                    $create_code=DB::table('tb_leave_code')->insert([
                        'id_employee'=>$id,
                        'id_leader'=>$leader_id,
                        'leader_code'=>$code,
                    ]);
                    //if($create_code)app('App\Http\Controllers\mail_controller')->codeMail($id,$leader_id,$code);
                }else{
                    $expired=date('Y-m-d H:i:ss',strtotime('-1 days',strtotime($Tgl)));
                    $tb_leave_code=DB::table('tb_leave_code')->where('id_employee',$id)->where('status','0')->get();
                    foreach($tb_leave_code as $dtcode){
                        $id_leave_code=$dtcode->id;
                        $code=$dtcode->leader_code;
                        $leader_id=$dtcode->id_leader;
                        $updated_at=$dtcode->updated_at;
                    }
                    if($updated_at<$expired){
                        ///app('App\Http\Controllers\mail_controller')->codeMail($id,$leader_id,$code);
                        $update=DB::table('tb_leave_code')->where('id',$id_leave_code)->update(['updated_at'=>$sekarang]);
                    }
                }
            }
            //End Leader Code
        
            $lock_backdate=DB::table('tb_utilities')->where('id','15')->where('status','1')->count();
            $nama=Auth::user()->name;
            $now=date('Y-m-d H:i:s');
            $cek=DB::table('tb_utilities_exception')->where('id_utility','15')->where('admin',$nama)->where('status','1')->where('start','<=',$now)->where('end','>=',$now)->count();
            if($cek==1)$lock_backdate=0;

            return view('page/admin/m_leave/leaves',['anual_count'=>$anual_count,'diffyears'=>$diffyears,'tb_anual'=>$tb_anual,'tb_docter'=>$tb_docter,'tb_special'=>$tb_special,'leader_id'=>$leader_id,'leader_name'=>$leader_name,'tb_employee_leave'=>$tb_employee_leave,'tb_employee'=>$tb_employee,'status'=>$status,'id_cuti'=>$id_cuti,'code'=>$code,'Tgl'=>$Tgl,'MinTgl'=>$MinTgl,'menu'=>'leave','sisa_count' => $sisa_count,'lock_backdate'=>$lock_backdate]);

        }

    }
    function addLeave(Request $data){
        //Jika Direct leader kosong akan dikembalikan
        //Penyesuaian Skep Cuti
            $pos_0=DB::table('tb_employees')->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')->where('tb_employees.id',$data->id_employee)->value('position_index');
            $approve1=$data->leader_id;
            $qty_approval=DB::table('tb_employee_leaves')->where('id',$data->id_leave)->value('qty_approval');
            if($qty_approval==1)$approve2=0;
            else $approve2=DB::table('tb_employees')->where('tb_employees.id',$approve1)->value('leader_id');
            if($approve2==Null)$approve2=0;
            if($pos_0<=4){
                $legalized='122';
            }elseif($pos_0>4){
                $legalized='879';
            }
        //End Penyesuaian
 
        //return $data->id_doc;
        $admin=Auth::user()->name;
        date_default_timezone_set("Asia/Jakarta");
        $doc_date=date('Y-m-d');
        $sekarang=date('Y-m-d H:i:s');
        $this->validate($data,[
            'category'=>'required',
            'leave_count'=>'required',
            'start_leave'=>'required',
            'finish_leave'=>'required',
            'start_working'=>'required',
            // 'leader_id'=>'required',
            'reason'=>'required',
        ]);
        if($data->category=='SKD'){
            $status='1';
            if($approve2=='0')$status2='0';
            else $status2='1';
        }
        else {$status='0';$status2='0';}
        
        $namaFile='';
        $file = $data->file('overlap_doc');
        if($file!=''){ 
            $namaFile = $_FILES['overlap_doc']['name'];
            $namaSementara = $_FILES['overlap_doc']['tmp_name'];
            $dirUpload = "laravel/storage/app/public/";

            $terupload = move_uploaded_file($namaSementara, $dirUpload.$namaFile);
            if(!$terupload)return redirect()->back()->with(['success'=>'Upload Gagal']);
        }
        $Leaves =  DB::table('tb_employee_leaves as a')
                ->where('a.id',$data->id_leave)
                ->select('a.outstanding')
                ->get();
            foreach($Leaves as $d){
                $outstanding = $d->outstanding;
            }
            //Hapus ini jika tolak < 1 tahun
            //$outstanding=4;
        //return $outstanding." ".$data->leave_count." ".$data->category;
        if($approve1=='')$approve1=0;
        if($outstanding >= $data->leave_count || $data->category == 'special' || $data->category == 'docter'){
            $check=DB::table('tb_leaves')->where('id_leave',$data->id_leave)->where('start_leave',$data->start_leave)->where('finish_leave',$data->finish_leave)->where('status_legalized','0')->count();
            if($check==0){
                $simpan=DB::table('tb_leaves')->insert([
                'id_leave'=>$data->id_leave,
                'id_doc'=>$data->id_doc,
                'doc_date'=>$doc_date,
                'id_employee'=>$data->id_employee,
                'category'=>$data->category,
                'start_leave'=>$data->start_leave,
                'finish_leave'=>$data->finish_leave,
                'start_working'=>$data->start_working,
                'leave_count'=>$data->leave_count,
                'reason'=>$data->reason,
                'remark'=>$data->remark,
                'approved'=>$approve1,
                'approved2'=>$approve2,
                'legalized'=>$legalized,
                'status_requested'=>'1',
                'status_approved'=>$status,
                'status_approved2'=>$status2,
                'status_legalized'=>$status,
                'status_reported'=>$status,
                'admin'=>$admin,
                'status'=>$status,
                'overlap_doc'=>$namaFile,
                ]);
                if($simpan){
                    $id=DB::table('tb_leaves')->where('id_leave',$data->id_leave)->where('start_leave',$data->start_leave)->where('finish_leave',$data->finish_leave)->value('id');
                    //$notification=$this->notificationLeave($id);
                }
            }

        } else{
            return redirect()->back()->with(['error'=>' Sudah mencapai batas cuti']);
        }
        if($simpan){
            if($data->category=='annual'){
                $tb_employee_leave=DB::table('tb_employee_leaves')->where('id',$data->id_leave)->get();
                foreach($tb_employee_leave as $dt){
                    $outstanding=$dt->outstanding;
                    $osbaru=$outstanding-$data->leave_count;
                    $terpakai=$dt->used+$data->leave_count;
                    //$used=$terpakai+$data->leave_count;
                    $update=DB::table('tb_employee_leaves')->where('id',$data->id_leave)->update(['used'=>$terpakai,'outstanding'=>$osbaru]);
                }
            }
            if($data->category=='SKD'){
                $update=DB::table('tb_sick')->where('id',$data->id_doc)->update(['status'=>'1']);
            }
            $tb_leave_code=DB::table('tb_leave_code')->where('id_employee',$data->id_employee)->where('status','0')->update([
                'status'=>'1',
                'updated_at'=>$sekarang
            ]);
            if($tb_leave_code){
                $code=rand(1000,9999);
                $create=DB::table('tb_leave_code')->insert([
                    'id_employee'=>$data->id_employee,
                    'id_leader'=>$data->leader_id,
                    'leader_code'=>$code,
                    'status'=>'0'
                ]);
            }
            //return "A";
            $tb_leave=DB::table('tb_leaves')->where('id_leave',$data->id_leave)->orderby('id','desc')->limit(1)->get();
            foreach($tb_leave as $dt){
                //app('App\Http\Controllers\mail_controller')->LeaveMail($dt->id);
            }
            return redirect()->back()->with(['success'=>'Success, document was created']);
        }else{
            return redirect()->back()->with(['success'=>'Failed']);

        }
    }
    function deleteLeave($id){
        $tb_employee_leave=DB::table('tb_employee_leaves')
        ->leftjoin('tb_leaves','tb_leaves.id_leave','=','tb_employee_leaves.id')
        ->where('tb_leaves.id',$id)->get(['tb_employee_leaves.*','tb_leaves.leave_count']);
        foreach($tb_employee_leave as $dt){
            $outstanding=$dt->outstanding;
            $osbaru=$outstanding+$dt->leave_count;
            $terpakai=$dt->used-$dt->leave_count;
            //$used=$terpakai+$data->leave_count;
            $update=tb_employee_leave::where('id',$dt->id)->update(['used'=>$terpakai,'outstanding'=>$osbaru]);
        }
        $delete=tb_leave::where('id',$id)->update(['status_legalized'=>'2']);
        if($delete){
            session([
                'table_name'=>'tb_leave',
                'id_table'=>$id,
                'activity'=>'delete',
                'fields'=>'',
                'before'=>'',
                'after'=>''
            ]);
            app('App\Http\Controllers\log_controller')->index();
            return redirect()->back();
        }
    }
    function sisaCuti($id,$qty){
        if($qty=='null')return redirect()->back();
        $tb_employee_leave=DB::table('tb_employee_leaves')->where('id',$id)->get();
        foreach($tb_employee_leave as $dt){
            //$outstanding=$dt->outstanding;
            $sisa_awal=$dt->sisa;
            $kurang_awal=$dt->kurang;
            $osbaru=12-$kurang_awal+$qty;
            $bal=$osbaru-$dt->used;
            $update=DB::table('tb_employee_leaves')->where('id',$id)->update(['sisa'=>$qty,'allowance'=>$osbaru,'outstanding'=>$bal]);
        }
        if($update){
            session([
                'table_name'=>'tb_employee_leave',
                'id_table'=>$id,
                'activity'=>'update',
                'fields'=>'sisa',
                'before'=>$sisa_awal,
                'after'=>$qty
            ]);
            app('App\Http\Controllers\log_controller')->index();
            return redirect()->back();
        }
    }
    function kurangCuti($id,$qty){
        if($qty=='null')return redirect()->back();
        $tb_employee_leave=DB::table('tb_employee_leaves')->where('id',$id)->get();
        foreach($tb_employee_leave as $dt){
            //$outstanding=$dt->outstanding;
            $kurang_awal=$dt->kurang;
            $sisa_awal=$dt->sisa;
            $osbaru=12+$sisa_awal-$qty;
            $bal=$osbaru-$dt->used;
            $update=DB::table('tb_employee_leaves')->where('id',$id)->update(['kurang'=>$qty,'allowance'=>$osbaru,'outstanding'=>$bal]);
        }
        if($update){
            session([
                'table_name'=>'tb_employee_leave',
                'id_table'=>$id,
                'activity'=>'update',
                'fields'=>'kurang',
                'before'=>$kurang_awal,
                'after'=>$qty
            ]);
            app('App\Http\Controllers\log_controller')->index();
            return redirect()->back();
        }
    }
    function approveLeave($awal,$akhir){
        date_default_timezone_set("Asia/Jakarta");
        $kalendar=CAL_GREGORIAN;
        if($awal==0){
            $periode=date('Y-m');
            $periodeBefore=date('Y-m-d',strtotime('-1 days',strtotime($periode.'-01')));
            $bln=date('Y',strtotime($periodeBefore.'-01'));
            $awal=date('Y-m-d',strtotime($bln.'-01-01'));
            $akhir=date('Y-m-d',strtotime($bln.'-12-31'));
        }else{
            $periode=date('Y-m',strtotime($awal));
        }

        $email=Auth::user()->email;
        $nama=Auth::user()->name;
        $id_employee = DB::table('tb_emails')->where('email_address',$email)->value('id_employee');
        if(!$id_employee){
            $id_employee = 0;
        }
        $limit_approval=DB::table('tb_utilities')->where('id','22')->where('status','1')->count();
        $limit_day=DB::table('tb_utilities')->where('id','22')->where('status','1')->value('limit_transaksi');
        $now=date('Y-m-d h:i:s');
        $tb_exception=DB::table('tb_utilities_exception')->where('id_utility','15')->where('status','1')->where('start','<=',$now)->where('end','>=',$now)->get();
        foreach($tb_exception as $dt){
             DB::table('tb_leaves')->where('status_legalized','0')->where('admin',$dt->admin)->update(['exception'=>'1']);
        }

        $baseLeave = DB::table('tb_leaves as l')
            ->leftJoin('tb_employees as e','e.id','=','l.id_employee')
            ->leftJoin('tb_employees as e2','e2.id','=','l.approved')
            ->leftJoin('tb_employees as e3','e3.id','=','l.approved2')
            ->select([
                'l.*',
                'e.NIK',
                'e.employee_name',
                'e2.employee_name as leader_name',
                'e3.employee_name as leader_name2'
            ]);

        $tb_leave_new = (clone $baseLeave)
            ->where(function($query) use ($id_employee) {
                $query->where(function($sub) use ($id_employee) {
                    $sub->where('l.status_approved', '0')
                        ->where('l.approved', $id_employee)
                        ->where('l.status_legalized', '<>', '2');
                })->orWhere(function($sub) use ($id_employee) {
                    $sub->where('l.status_approved', '1')
                        ->where('l.status_approved2', '0')
                        ->where('l.approved2', $id_employee)
                        ->where('l.status_legalized', '<>', '2');
                });
            })
            ->get();

        $tb_leave_approve = (clone $baseLeave)
            ->where(function($query) use ($awal, $akhir, $id_employee) {
                $query->where(function($sub) use ($awal, $akhir, $id_employee) {
                    $sub->where('l.status_approved', '1')
                        ->where('l.approved', $id_employee)
                        ->where('l.start_leave', '>=', $awal)
                        ->where('l.start_leave', '<=', $akhir)
                        ->where('l.status_legalized', '<>', '2');
                })->orWhere(function($sub) use ($awal, $akhir, $id_employee) {
                    $sub->where('l.status_approved2', '1')
                        ->where('l.approved2', $id_employee)
                        ->where('l.start_leave', '>=', $awal)
                        ->where('l.start_leave', '<=', $akhir)
                        ->where('l.status_legalized', '<>', '2');
                });
            })
            ->get();

        $tb_leave_refuse = (clone $baseLeave)
            ->where(function($query) use ($id_employee) {
                $query->where(function($sub) use ($id_employee) {
                    $sub->where('l.status_approved', '2')
                        ->where('l.approved', $id_employee);
                })->orWhere(function($sub) use ($id_employee) {
                    $sub->where('l.status_approved', '2')
                        ->where('l.approved2', $id_employee);
                })->orWhere(function($sub) use ($id_employee) {
                    $sub->where('l.status_approved2', '2')
                        ->where('l.approved', $id_employee);
                })->orWhere(function($sub) use ($id_employee) {
                    $sub->where('l.status_approved2', '2')
                        ->where('l.approved2', $id_employee);
                })->orWhere(function($sub) use ($id_employee) {
                    $sub->where('l.status_legalized', '2')
                        ->where('l.approved', $id_employee);
                })->orWhere(function($sub) use ($id_employee) {
                    $sub->where('l.status_legalized', '2')
                        ->where('l.approved2', $id_employee);
                });
            })
            ->get();

        $qty_leave_new = (clone $baseLeave)
            ->where(function($query) use ($id_employee) {
                $query->where(function($sub) use ($id_employee) {
                    $sub->where('l.status_approved', '0')
                        ->where('l.approved', $id_employee)
                        ->where('l.status_legalized', '<>', '2');
                })->orWhere(function($sub) use ($id_employee) {
                    $sub->where('l.status_approved', '1')
                        ->where('l.status_approved2', '0')
                        ->where('l.approved2', $id_employee)
                        ->where('l.status_legalized', '<>', '2');
                });
            })
            ->count();

        $list_code=DB::table('tb_leave_code')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_leave_code.id_employee')
        ->where('tb_leave_code.id_leader',$id_employee)
        ->where('tb_leave_code.status','0')->get();

        $qty_leave_proccess=DB::table('tb_leaves')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_leaves.id_employee')
        ->leftjoin('tb_employees as tb_employees2','tb_employees2.id','=','tb_employees.leader_id')
        ->where([['status_legalized','0'],['status_approved','<>','2'],['status_approved2','<>','2'],['legalized','879']])
        ->count();

        $code_qty=DB::table('tb_leave_code')->where('tb_leave_code.id_leader',$id_employee)->where('tb_leave_code.status','0')->count();

        return view('page/admin/m_leave/approve_leave',['nama'=>$nama,'start'=>$awal,'list_code'=>$list_code,'code_qty'=>$code_qty,'finish'=>$akhir,'qty_leave_new'=>$qty_leave_new,'tb_leave_new'=>$tb_leave_new,'tb_leave_approve'=>$tb_leave_approve,'tb_leave_refuse'=>$tb_leave_refuse,'id_employee'=>$id_employee,'periode'=>$periode,'qty_leave_proccess'=>$qty_leave_proccess,'limit_approval'=>$limit_approval,'limit_day'=>$limit_day,'menu'=>'leave']);
    }
    function approveSign($id,$status,$approved){
        date_default_timezone_set("Asia/Jakarta");
        $sekarang=date('Y-m-d');
        if($approved==1){
            $approve=DB::table('tb_leaves')->where('id',$id)->update(['status_approved'=>$status,'date_approved'=>$sekarang]);
            //app('App\Http\Controllers\mail_controller')->LeaveMail($id);
            $tb=DB::table('tb_leaves')->where('id',$id)->get();
            foreach($tb as $dt){
                if($dt->approved==$dt->legalized){
                    $update=DB::table('tb_leaves')->where('id',$id)->update(['status_legalized'=>$status,'date_approved'=>$sekarang]);
                }
            }
        }
        if($approved==2){
            $approve=DB::table('tb_leaves')->where('id',$id)->update(['status_approved2'=>$status,'date_approved2'=>$sekarang]);
            $tb=DB::table('tb_leaves')->where('id',$id)->get();
            foreach($tb as $dt){
                if($dt->approved2==$dt->legalized){
                    $update=DB::table('tb_leaves')->where('id',$id)->update(['status_legalized'=>$status,'date_approved'=>$sekarang]);
                }
            }
        }
        if($approve){
            //$notification=$this->notificationLeave($id);
            return redirect()->back()->with(['success'=>'Update Success']);
        }
    }
    function legalizeLeave($awal,$akhir,$category){
        if($category=='SKD'){
            $judul='Employee SKD';
            $submenu='legalized';
        }
        else {
            $judul='Employee '.$category.' Leave';
            $submenu='legalized';
        }
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
        $id_employee = DB::table('tb_emails')->where('email_address',$email)->value('id_employee');
        if(!$id_employee){
            $id_employee = 0;
        }

        $baseLeave = DB::table('tb_leaves as l')
            ->leftJoin('tb_employees as e','e.id','=','l.id_employee')
            ->leftJoin('tb_employees as e2','e2.id','=','l.approved')
            ->leftJoin('tb_employees as e3','e3.id','=','l.approved2')
            ->leftJoin('tb_departments as d','d.id','=','e.dept_id')
            ->where('l.category',$category)
            ->select([
                'l.*',
                'e.NIK',
                'e.employee_name',
                'e2.employee_name as leader_name',
                'e3.employee_name as leader_name2',
                'd.dept_code'
            ]);

        $tb_leave_new = (clone $baseLeave)
            ->where('l.status_approved','0')
            ->get();

        $tb_leave_proccess = (clone $baseLeave)
            ->where('l.status_legalized','0')
            ->where('l.status_approved','<>','2')
            ->where('l.status_approved2','<>','2')
            ->get();

        $tb_leave_approve = (clone $baseLeave)
            ->where('l.status_legalized','1')
            ->where('l.start_leave','>=',$awal)
            ->where('l.start_leave','<=',$akhir)
            ->where('l.legalized','122')
            ->get();

        $tb_leave_refuse = (clone $baseLeave)
            ->where(function($query) {
                $query->where('l.status_approved','2')
                    ->orWhere('l.status_legalized','2');
            })
            ->get();

        $qty_leave_new = (clone $baseLeave)
            ->where('l.status_approved','0')
            ->count();

        $qty_leave_proccess = (clone $baseLeave)
            ->where('l.status_legalized','0')
            ->where('l.status_approved','<>','2')
            ->where('l.status_approved2','<>','2')
            ->count();

        return view('page/admin/m_leave/legalize_leave',['id_employee'=>$id_employee,'start'=>$awal,'finish'=>$akhir,'qty_leave_new'=>$qty_leave_new,'qty_leave_proccess'=>$qty_leave_proccess,'tb_leave_new'=>$tb_leave_new,'tb_leave_proccess'=>$tb_leave_proccess,'tb_leave_approve'=>$tb_leave_approve,'tb_leave_refuse'=>$tb_leave_refuse,'periode'=>$periode,'category'=>$category,'judul'=>$judul,'menu'=>'leave','submenu'=>$submenu]);
    }
    function legalizeLeave_DirHR($awal,$akhir,$category){
        if($category=='SKD'){
            $judul='Employee SKD';
            $submenu='legalized';
        }
        else {
            $judul='Employee '.$category.' Leave';
            $submenu='legalized';
        }
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
        $tb_email=tb_email::where('email_address',$email)->get();
        foreach($tb_email as $dt){
            $id_employee=$dt->id_employee;
        }
        $tb_leave_new=DB::table('tb_leaves')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_leaves.id_employee')
        ->leftjoin('tb_employees as tb_employees2','tb_employees2.id','=','tb_leaves.approved')
        ->leftjoin('tb_employees as tb_employees3','tb_employees3.id','=','tb_leaves.approved2')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->where([['status_approved','0'],['category',$category],['legalized','879']])
        //->orwhere([['status_approved2','0'],['approved2','>','0'],['category',$category]])
        //->where('legalized',$id_employee)
        ->get(['tb_leaves.*','tb_employees.NIK','tb_employees.employee_name','tb_employees2.employee_name as leader_name','tb_employees3.employee_name as leader_name2','tb_departments.dept_code']);
        $tb_leave_proccess=DB::table('tb_leaves')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_leaves.id_employee')
        ->leftjoin('tb_employees as tb_employees2','tb_employees2.id','=','tb_leaves.approved')
        ->leftjoin('tb_employees as tb_employees3','tb_employees3.id','=','tb_leaves.approved2')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->where([['status_legalized','0'],['status_approved','<>','2'],['status_approved2','<>','2'],['category',$category],['legalized','879']])
        //->where([['status_approved','1'],['approved2','0'],['status_legalized','0']])
        //->orwhere([['status_approved2','1'],['status_legalized','0']])
        //->where('legalized',$id_employee)
        ->get(['tb_leaves.*','tb_employees.NIK','tb_employees.employee_name','tb_employees2.employee_name as leader_name','tb_employees3.employee_name as leader_name2','tb_departments.dept_code']);
        $tb_leave_approve=DB::table('tb_leaves')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_leaves.id_employee')
        ->leftjoin('tb_employees as tb_employees2','tb_employees2.id','=','tb_leaves.approved')
        ->leftjoin('tb_employees as tb_employees3','tb_employees3.id','=','tb_leaves.approved2')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->where('status_legalized','1')
        ->where('start_leave','>=',$awal)
        ->where('start_leave','<=',$akhir)
        ->where('category',$category)
        ->where('legalized','879')
        //->where('legalized',$id_employee)
        ->get(['tb_leaves.*','tb_employees.NIK','tb_employees.employee_name','tb_employees2.employee_name as leader_name','tb_employees3.employee_name as leader_name2','tb_departments.dept_code']);
        $tb_leave_refuse=DB::table('tb_leaves')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_leaves.id_employee')
        ->leftjoin('tb_employees as tb_employees2','tb_employees2.id','=','tb_leaves.approved')
        ->leftjoin('tb_employees as tb_employees3','tb_employees3.id','=','tb_leaves.approved2')
        ->where([['status_approved','2'],['category',$category]])->orWhere([['status_legalized','2'],['category',$category],['legalized','879']])
        //->where('legalized',$id_employee)
        ->get(['tb_leaves.*','tb_employees.NIK','tb_employees.employee_name','tb_employees2.employee_name as leader_name','tb_employees3.employee_name as leader_name2']);

        // $qty_leave_new=DB::table('tb_leaves')
        // ->leftjoin('tb_employees','tb_employees.id','=','tb_leaves.id_employee')
        // ->leftjoin('tb_employees as tb_employees2','tb_employees2.id','=','tb_employees.leader_id')
        // ->where([['status_approved','0'],['category',$category],['legalized','879']])
        // ->orwhere([['status_approved2','0'],['approved2','>','0'],['category',$category],['legalized','879']])
        // ->count();
        $qty_leave_new=DB::table('tb_leaves')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_leaves.id_employee')
        ->leftjoin('tb_employees as tb_employees2','tb_employees2.id','=','tb_leaves.approved')
        ->leftjoin('tb_employees as tb_employees3','tb_employees3.id','=','tb_leaves.approved2')
        ->where([['status_approved','0'],['approved',$id_employee],['status_legalized','<>','2']])
        ->orWhere([['status_approved','1'],['status_approved2','0'],['approved2',$id_employee],['status_legalized','<>','2']])
        ->count();

        $qty_leave_proccess=DB::table('tb_leaves')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_leaves.id_employee')
        ->leftjoin('tb_employees as tb_employees2','tb_employees2.id','=','tb_employees.leader_id')
        ->where([['status_legalized','0'],['status_approved','<>','2'],['status_approved2','<>','2'],['legalized','879']])
        ->where('category',$category)
        //->where([['status_approved','1'],['approved2','0'],['status_legalized','0']])
        //->orwhere([['status_approved2','1'],['status_legalized','0']])
        //->where('legalized',$id_employee)
        ->count();
        return view('page/admin/m_leave/legalize_leave',['id_employee'=>$id_employee,'start'=>$awal,'finish'=>$akhir,'qty_leave_new'=>$qty_leave_new,'qty_leave_proccess'=>$qty_leave_proccess,'tb_leave_new'=>$tb_leave_new,'tb_leave_proccess'=>$tb_leave_proccess,'tb_leave_approve'=>$tb_leave_approve,'tb_leave_refuse'=>$tb_leave_refuse,'periode'=>$periode,'category'=>$category,'judul'=>$judul,'menu'=>'leave','submenu'=>$submenu]);
    }
    function legalizeSign($id,$status){
        $email=Auth::user()->email;
        $tb_email=tb_email::where('email_address',$email)->get();
        foreach($tb_email as $dt){
            $legalizer=$dt->id_employee;
        }
        date_default_timezone_set("Asia/Jakarta");
        $sekarang=date('Y-m-d');
        $tb_cutoff=tb_cutoff::where('usage','payroll')->get();
        foreach($tb_cutoff as $dt){
            $Batas_awal=$dt->start_implement;
        }
        $approve=tb_leave::where('id',$id)->update(['status_legalized'=>$status,'legalized'=>$legalizer,'date_legalized'=>$sekarang]);
        if($status=='1'){
            $tb_leave=DB::table('tb_leaves')->where('id',$id)->get();
            foreach($tb_leave as $dtsick){
                if($dtsick->id_doc>0){
                    $update=DB::table('tb_sick')->where('id',$dtsick->id_doc)->update(['status'=>'1']);
                }
            }
            $tb_leave=tb_leave::where('id',$id)->limit(1)->get();
            foreach($tb_leave as $dt2){
                $tgl1 = new DateTime($dt2->start_leave);
                $tgl2 = new DateTime($dt2->finish_leave);
                $diffdays = $tgl2->diff($tgl1)->days;
                $id_employee=$dt2->id_employee;
                $categori=$dt2->category;
                //echo $dt2->finish_leave;
            }
            //echo $diffdays;
            $mulai=$dt2->start_leave;
            for($i=0;$i<=$diffdays;$i++){
                $Tgl=date('Y-m-d',strtotime($i.' days',strtotime($mulai)));
                $tb_shift=DB::table('tb_employee_shifts')->leftJoin('tb_group_shifts','tb_group_shifts.id','=','tb_employee_shifts.id_shift')->leftJoin('tb_groups','tb_groups.group','=','tb_group_shifts.group')->where('id_employee',$id_employee)->get();
                foreach($tb_shift as $row){
                    $group=$row->group;
                    $tgl1 = new DateTime($row->start_implement);
                    $tgl2 = new DateTime($Tgl);
                    $diffdays2 = $tgl2->diff($tgl1)->days;
                    $diffcycle=Floor($diffdays2/$row->cycle);
                    $modcycle=$diffdays2%$row->cycle;
                    $modcycle++;
                }
                $tb_cycle=tb_cycle::where('days',$modcycle)->where('group',$group)->where('shift','>','0')->count();
                $tb_freeday=tb_freeday::where('date_off',$Tgl)->count();
                if($tb_cycle>0&&$tb_freeday==0){
                    $periode_normal=date('Y-m',strtotime($Tgl));
                    $tgl_berjalan=date('d',strtotime($Tgl));
                    if($tgl_berjalan>=$Batas_awal){
                        $periode_cutoff=date('Y-m',strtotime('+10 days',strtotime($Tgl)));
                    }else{
                        $periode_cutoff=date('Y-m',strtotime($Tgl));
                    }
                    if($categori=='docter')$category="SAKIT";
                    else $category="LEAVE";
                    $simpan=tb_absency::create([
                        'id_employee'=>$id_employee,
                        'date_off'=>$Tgl,
                        'periode_normal'=>$periode_normal,
                        'periode_cutoff'=>$periode_cutoff,
                        'form_reference'=>'Leave',
                        'ref_id'=>$id,
                        'category'=>$category
                    ]);  
                    //Update 
                    $day=date('d',strtotime($Tgl));
                    $kolom="D".$day;
                    if($category=='LEAVE')$code='53';
                    if($category=='SAKIT')$code='54'; 
                    $update=DB::connection('tms')->table('tb_work_entries')->where('plan_actual','actual')->where('id_employee',$id_employee)->where('periode',$periode_normal)->update([
                        $kolom=>$code,
                    ]);
                    //End Upate TMS
                }
            }
            return redirect()->back()->with(['success'=>'Update Success']);
        }
        if($status=='2'){
            $tb_leave=tb_leave::where('id',$id)->limit(1)->get();
            foreach($tb_leave as $dt2){
                $tgl1 = new DateTime($dt2->start_leave);
                $tgl2 = new DateTime($dt2->finish_leave);
                $diffdays = $tgl2->diff($tgl1)->days;
                $id_employee=$dt2->id_employee;
                $categori=$dt2->category;
                //echo $dt2->finish_leave;
            }
            $tb1=DB::table('tb_absencies')->where('id_employee',$id_employee)->where('ref_id',$id)->get();
            foreach($tb1 as $dt1){
                    $day=date('d',strtotime($dt1->date_off));
                    $kolom="D".$day;
                $update=DB::connection('tms')->table('tb_work_entries')->where('plan_actual','actual')->where('id_employee',$id_employee)->where('periode',$dt1->periode_normal)->update([
                    $kolom=>'99',
                ]);
    
            }
            $delete=DB::table('tb_absencies')->where('id_employee',$id_employee)->where('ref_id',$id)->delete();
        }
        return redirect()->back();
    }
    function reportLeave($awal,$akhir,$category){
        date_default_timezone_set("Asia/Jakarta");
        
        // Hitung periode
        if($awal==0){
            $periode=date('Y-m');
            $periodeBefore=date('Y-m-d',strtotime('-1 days',strtotime($periode.'-01')));
            $bln=date('Y-m',strtotime($periodeBefore.'-01'));
            $awal=date('Y-m-d',strtotime($bln.'-25'));
            $akhir=date('Y-m-d',strtotime($periode.'-24'));
        }else{
            $periode=date('Y-m',strtotime($awal));
        }

        $nama=Auth::user()->name;
        $email=Auth::user()->email;
        
        // Ambil data employee dan admin
        $employeeData = DB::table('tb_emails')
            ->join('tb_admins', 'tb_emails.id_employee', '=', 'tb_admins.id_employee')
            ->where('tb_emails.email_address', $email)
            ->select('tb_admins.id_employee', 'tb_admins.dept_id')
            ->get();
        
        $deptIds = [0];
        foreach($employeeData as $dt){
            $deptIds[] = $dt->dept_id;
        }

        // Base query yang konsisten untuk semua
        $baseQuery = DB::table('tb_leaves')
            ->leftJoin('tb_employees', 'tb_employees.id', '=', 'tb_leaves.id_employee')
            ->leftJoin('tb_employees as tb_employees2', 'tb_employees2.id', '=', 'tb_leaves.approved')
            ->leftJoin('tb_employees as tb_employees3', 'tb_employees3.id', '=', 'tb_leaves.approved2')
            ->leftJoin('tb_departments', 'tb_departments.id', '=', 'tb_employees.dept_id')
            ->where('category', $category)
            ->where('status_legalized', '<>', '2')
            ->whereIn('tb_employees.dept_id', $deptIds);

        // 1. Leave Baru (status_approved = 0)
        $tb_leave_new = (clone $baseQuery)
            ->where('status_approved', '0')
            ->get([
                'tb_leaves.*',
                'tb_employees.NIK',
                'tb_employees.employee_name',
                'tb_employees2.employee_name as leader_name',
                'tb_employees3.employee_name as leader_name2',
                'tb_departments.dept_code'
            ]);

        // 2. Leave Proses (status_approved = 1, status_legalized = 0)
        $tb_leave_proccess = (clone $baseQuery)
            ->where('status_approved', '1')
            ->where('status_legalized', '0')
            ->get([
                'tb_leaves.*',
                'tb_employees.NIK',
                'tb_employees.employee_name',
                'tb_employees2.employee_name as leader_name',
                'tb_employees3.employee_name as leader_name2',
                'tb_departments.dept_code'
            ]);

        // 3. Leave Approve (status_legalized = 1)
        $tb_leave_approve = (clone $baseQuery)
            ->where('status_legalized', '1')
            ->whereBetween('start_leave', [$awal, $akhir])
            ->get([
                'tb_leaves.*',
                'tb_employees.NIK',
                'tb_employees.employee_name',
                'tb_employees2.employee_name as leader_name',
                'tb_employees3.employee_name as leader_name2',
                'tb_departments.dept_code'
            ]);

        // 4. Leave Refuse - PERBAIKAN: tambahkan join departments dan select yang konsisten
        $tb_leave_refuse = DB::table('tb_leaves')
            ->leftJoin('tb_employees', 'tb_employees.id', '=', 'tb_leaves.id_employee')
            ->leftJoin('tb_employees as tb_employees2', 'tb_employees2.id', '=', 'tb_leaves.approved')
            ->leftJoin('tb_employees as tb_employees3', 'tb_employees3.id', '=', 'tb_leaves.approved2')
            ->leftJoin('tb_departments', 'tb_departments.id', '=', 'tb_employees.dept_id') // ← TAMBAHKAN INI
            ->where('category', $category)
            ->whereIn('tb_employees.dept_id', $deptIds)
            ->where(function($q) {
                $q->where('status_approved', '2')
                ->orWhere('status_approved2', '2')
                ->orWhere('status_legalized', '2');
            })
            ->get([
                'tb_leaves.*',
                'tb_employees.NIK',
                'tb_employees.employee_name',
                'tb_employees2.employee_name as leader_name',
                'tb_employees3.employee_name as leader_name2',
                'tb_departments.dept_code' // ← TAMBAHKAN INI
            ]);

        // Count
        $qty_leave_new = $tb_leave_new->count();
        $qty_leave_proccess = $tb_leave_proccess->count();

        return view('page/admin/m_leave/report_leave',[
            'start'=>$awal,
            'finish'=>$akhir,
            'qty_leave_new'=>$qty_leave_new,
            'qty_leave_proccess'=>$qty_leave_proccess,
            'tb_leave_new'=>$tb_leave_new,
            'tb_leave_approve'=>$tb_leave_approve,
            'tb_leave_proccess'=>$tb_leave_proccess,
            'tb_leave_refuse'=>$tb_leave_refuse,
            'periode'=>$periode,
            'nama'=>$nama,
            'category'=>$category,
            'menu'=>'leave',
            'submenu'=>'report'
        ]);
    }
    function reportSign($id,$status){
        $approve=tb_leave::where('id',$id)->update(['status_reported'=>$status]);
        if($approve)return redirect()->back()->with(['success'=>'Update Success']);
    }
    function skd($awal,$akhir){
        date_default_timezone_set("Asia/Jakarta");
        $kalendar=CAL_GREGORIAN;
        
        if($awal==0){
            $periode=date('Y-m');
            $periodeBefore=date('Y-m-d',strtotime('-1 days',strtotime($periode.'-01')));
            $bln=date('Y-m',strtotime($periodeBefore.'-01'));
            $awal=date('Y-m-d',strtotime($bln.'-25'));
            $akhir=date('Y-m-d',strtotime($periode.'-24'));
        }

        $email=Auth::user()->email;
        $user = DB::table('tb_emails')->where('email_address',$email)->first();
        $id_user = $user->id_employee ?? 0;
        
        // Ambil semua dept_id yang diizinkan
        $deptIds = DB::table('tb_admins')
            ->where('id_employee', $id_user)
            ->pluck('dept_id')
            ->toArray();
        
        // Tambahkan dept_id = 0
        $deptIds[] = 0;
        
        // 🔥 SATU QUERY dengan WHERE IN
        $tb_sick = DB::table('tb_sick')
            ->leftJoin('tb_employees','tb_employees.id','=','tb_sick.id_employee')
            ->leftJoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
            ->leftJoin('tb_leaves','tb_leaves.id_doc','=','tb_sick.id')
            ->whereIn('tb_employees.dept_id', $deptIds)
            ->whereBetween('upload_time', [$awal, $akhir])
            ->orderBy('upload_time','desc')
            ->orderBy('tb_sick.status','asc')
            ->orderBy('tb_sick.id','asc')
            ->get([
                'tb_sick.*',
                'tb_employees.leader_id',
                'tb_departments.dept_code',
                'tb_leaves.leave_count',
                'tb_leaves.start_leave',
                'tb_leaves.finish_leave',
                'tb_leaves.reason'
            ]);
        
        // Pisahkan berdasarkan status
        $tb_sick_new = $tb_sick->where('status', 0);
        $tb_sick_proccess = $tb_sick->where('status', '>', 0);
        $qty_sick_new = $tb_sick_new->count();
        
        return view('page/admin/m_leave/skd',[
            'start'=>$awal,
            'finish'=>$akhir,
            'tb_sick_new'=>$tb_sick_new,
            'tb_sick_proccess'=>$tb_sick_proccess,
            'qty_sick_new'=>$qty_sick_new,
            'menu'=>'leave',
            'submenu'=>'apply'
        ]);
    }    function skdApprove($id){
        $update=DB::table('tb_sick')->where('id',$id)->update(['status'=>'1']);
        if($update) return redirect()->back()->with(['success'=>'Approve Success']);
    }
    function skdRefuse($id){
        $update=DB::table('tb_sick')->where('id',$id)->update(['status'=>'2']);
        //return "Masuk";
        if($update) return redirect()->back()->with(['success'=>'Refuse Success']);
    }
    function skdDelete($id){
        $delete=DB::table('tb_sick')->where('id',$id)->delete();
        if($delete) return redirect()->back()->with(['success'=>'Delete Success']);
    }
    function deleteEmployeeLeave($id){
        //return $id;
        $delete=DB::table('tb_employee_leaves')->where('id',$id)->update(['status'=>'0']);
        if($delete)return redirect()->back()->with(['success'=>'Cuti telah dinon aktifkan']);
    }
    function deleteEmployeeLeave1($id){
        $delete=DB::table('tb_employee_leaves')->where('id',$id)->update(['is_delete'=>'1']);
        if($delete)return redirect()->back()->with(['success'=>'Cuti telah dihapus']);
    }
    function special(){
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

        $tb_leave=DB::table('tb_employees')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_employee_leaves','tb_employee_leaves.id_employee','=','tb_employees.id')
        ->where([['tb_employees.status','1'],['dept_id','0']]);
        foreach($tb_admins as $dt2){
            $tb_leave=$tb_leave->orwhere([['tb_employees.status','1'],['dept_id',$dt2->dept_id]]);
        }
        $tb_leave=$tb_leave->orderby('employee_name','asc')->orderby('year','desc')
        ->get(['tb_employees.id as idemployee','tb_employees.NIK','tb_employees.employee_name','tb_employees.join_date','tb_departments.dept_code','tb_employee_leaves.*']);
        //return view('page/admin/m_leave/leaves',['tb_employee'=>$tb_employee,'menu'=>'leave']);
        return view('page/admin/m_leave/special',['tb_leave'=>$tb_leave,'menu'=>'leave','submenu'=>'apply']);
    }
    function massLeave($periode){
        date_default_timezone_set("Asia/Jakarta");
        if($periode==0)$tanggal=date('Y-m-d');
    }
    function createNew($id){
        //return("Under Maintenance");
        $admin=Auth::user()->name;
        date_default_timezone_set("Asia/Bangkok");
        $kalendar=CAL_GREGORIAN;
        $Tgl=date('Y-m-d');
        $now=date('Y-m-d H:i:s');

        $tb_leave=DB::table('tb_employee_leaves')->where('id',$id)->where('status','1')->get();
        //return $tb_leave;
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
                
            $create=DB::table('tb_employee_leaves')->insert([
                'id_employee'=>$dt->id_employee,
                'year'=>$Thnstart,
                'start'=>$Periode_awal,
                'end'=>$Periode_akhir,
                'extend'=>$Periode_extend,
                'sisa'=>'0',
                'kurang'=>'0',
                'allowance'=>'12',
                'used'=>'0',
                'outstanding'=>'12',
                'remark'=>'Trial Double',
                'status'=>'1',
                'admin'=>$admin,
                'qty_approval'=>$dt->qty_approval,
            ]);
            if($create){
                $tb_leave_update=DB::table('tb_employee_leaves')->where('id',$id)->where('status','1')->update([
                    'status'=>'0',
                    'updated_at'=>$now,
                    'admin'=>$admin
                ]);
                return redirect()->back()->with(['success'=>'New Leave Form Success Created']);

            }
        }
    }
    function activated($id){
        $update=DB::table('tb_employee_leaves')->where('id',$id)->update(['status'=>'1']);
        if($update)return redirect()->back()->with(['success'=>'Success Activated']);
    }
    function lock(Request $data){
        $nama=Auth::user()->name;
        $email=Auth::user()->email;
        $cek1=DB::table('tb_emails')->where('email_address',$email)->get();
        foreach($cek1 as $dt){$id_user=$dt->id_employee;}

        $respon=0;
        $tb_employee=DB::table('tb_employees')->where('id',$data->id_employee)->get();
        foreach($tb_employee as $dt){
            $leader_id=$dt->leader_id;
        }
        $qty_leave_code=DB::table('tb_leave_code')->where('id_employee',$data->id_employee)->count();
        if($qty_leave_code==0){
            $code=rand(1000,9999);
            $create=DB::table('tb_leave_code')->insert([
                'id_employee'=>$data->id_employee,
                'id_leader'=>$leader_id,
                'leader_code'=>$code,
                'status'=>'0'
            ]);
            if($create)$respon=1;
        }
        return $respon;
    }
    function unlock(Request $data){
        $respon=0;
        $tb_leave_code=DB::table('tb_leave_code')->where('id_employee',$data->id_employee)->delete();
        if($tb_leave_code)$respon=1;
        return $respon;
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
    function leaveCount(Request $data){

        $tgl1 = new DateTime($data->start);
        $tgl2 = new DateTime($data->finish);
        $diffdays = $tgl2->diff($tgl1)->days;
        $id_employee=$data->idemployee;
        $mulai=$data->start;

        $days=0;
        for($i=0;$i<=$diffdays;$i++){
            $Tgl=date('Y-m-d',strtotime($i.' days',strtotime($mulai)));
            // $tb_shift=DB::table('tb_employee_shifts')->leftJoin('tb_group_shifts','tb_group_shifts.id','=','tb_employee_shifts.id_shift')->leftJoin('tb_groups','tb_groups.group','=','tb_group_shifts.group')->where('id_employee',$id_employee)
            // ->where('tb_employee_shifts.status','1')
            // ->get();
            $tb_shift=DB::table('tb_work_contract')->leftjoin('tb_work_shift','tb_work_shift.id','=','tb_work_contract.id_work_shift')->leftjoin('tb_work_group','tb_work_group.id','=','tb_work_shift.id_work_group')->where('tb_work_contract.id_employee',$id_employee)
            ->where('tb_work_contract.isactive','1')
            ->get(['tb_work_group.id as group','tb_work_shift.start_implement','tb_work_group.cycle_day as cycle']);
            foreach($tb_shift as $row){
                $group=$row->group;
                $tgl1 = new DateTime($row->start_implement);
                $tgl2 = new DateTime($Tgl);
                $diffdays2 = $tgl2->diff($tgl1)->days;
                $diffcycle=Floor($diffdays2/$row->cycle);
                $modcycle=$diffdays2%$row->cycle;
                $modcycle++;

                // $tb_cycle=tb_cycle::where('days',$modcycle)->where('group',$group)->where('shift','>','0')->count();
                $tb_cycle=DB::table('tb_work_cycle')->where('id_work_group',$group)->where('days',$modcycle)->where('id_work_time','>','0')->count();
                $tb_freeday=DB::table('tb_freedays')->where('date_off',$Tgl)->count();
                $change_day=DB::table('tb_freedays')->where('date_off',$Tgl)->where('category','Working')->count();
                if(($tb_cycle>0&&$tb_freeday==0)||($change_day>0)){
                    $days++;
                }
            }
        }
        return $days;
    }
    function createNow($id){
        $nama=Auth::user()->name;
        date_default_timezone_set("Asia/Bangkok");
        $kalendar=CAL_GREGORIAN;
        $Tgl=date('Y-m-d');
        $MinTgl=date('Y-m-d',strtotime('1 weeks',strtotime($Tgl)));

        $tb_employee=tb_employee::where('id',$id)->get();
        foreach($tb_employee as $dt){
            $tgl1 = new DateTime($dt->join_date);
            $tgl2 = new DateTime($Tgl);
            $diffdays = $tgl2->diff($tgl1)->days;
            $diffyears=Floor($diffdays/365);
            $Thnawal=date('Y',strtotime($dt->join_date));
            $Thnstart=$Thnawal+$diffyears;
            $Thnend=$Thnstart+1;
            $Bln=date('m-d',strtotime($dt->join_date));
            if($Bln=='02-29')$Bln='02-28';
            $Periode_awal=$Thnstart.'-'.$Bln;
            $Periode_akhir_temp=$Thnend.'-'.$Bln;
            $Periode_akhir = date('Y-m-d', strtotime("-1 day", strtotime($Periode_akhir_temp)));
            $Periode_extend=date('Y-m-d', strtotime("+6 month", strtotime($Periode_akhir)));
        }
        $tb_employee_leave=tb_employee_leave::where([['id_employee',$id],['end','>=',$Tgl]])->count();
        if($diffyears>=1)$jatah='12';
        else $jatah='0';
        $tb_employee_freeday=0;
        $kurang=0;
        $outstanding=$jatah-$kurang;
        if(request()->user()->hasRole('allowance'))$status='1';
        else $status='0';

        if($tb_employee_leave==0){
            tb_employee_leave::create([
                'id_employee'=>$id,
                'year'=>$Thnstart,
                'start'=>$Periode_awal,
                'end'=>$Periode_akhir,
                'extend'=>$Periode_extend,
                'sisa'=>'0',
                'kurang'=>$kurang,
                'allowance'=>$jatah,
                'used'=>$tb_employee_freeday,
                'outstanding'=>$outstanding,
                'remark'=>'Create New',
                'status'=>$status,
                'admin'=>$nama
            ]);
        }
        $tb_employee_leave=tb_employee_leave::where([['id_employee',$id],['end','>=',$Tgl]])->get();
        foreach($tb_employee_leave as $dt){
            $id_leave=$dt->id;
        }
        //return $tb_employee_leave;
        return redirect('/Leave/Employee/'.$id_leave);

    }
    function extend($id){
        $tb_employee_leave=DB::table('tb_employee_leaves')->where('id',$id)->get();
        foreach($tb_employee_leave as $dt){
            $ext_lama=$dt->extend;
            $ext_baru=date('Y-m-d',strtotime('+3 months',strtotime($ext_lama)));
            $update=DB::table('tb_employee_leaves')->where('id',$id)->update(['extend'=>$ext_baru,'isExtend'=>'1']);
            if($update)return redirect()->back()->with(['success'=>'Success Extend']);
        }
    }
    function updateBalance(){
        date_default_timezone_set("Asia/Jakarta");
        $now=date('Y-m-d H:i:s');
        $today=date('Y-m-d');
        $tb_employee_leaves=DB::table('tb_employee_leaves')->where('status','1')->get();
        foreach($tb_employee_leaves as $dt){
            $tb_leaves=DB::table('tb_leaves')->where('id_leave',$dt->id)->where('category','annual')->where('status_legalized','<','2')->get();
            $qty=0;
            foreach($tb_leaves as $dt2){
                $qty=$qty+$dt2->leave_count;
            }
            $allowance=12+$dt->sisa-$dt->kurang;
            $os=$allowance-$qty;

            if($dt->extend<$today)$status='0';
            else $status='1';

            $update=DB::table('tb_employee_leaves')->where('id',$dt->id)->update([
                'allowance'=>$allowance,
                'used'=>$qty,
                'outstanding'=>$os,
                'status'=>$status,
                'updated_at'=>$now,
            ]);
        }
        return redirect()->back();
    }
    function inactiveLeave(){
        $sekarang=date('Y-m-d');
        $batas=date('Y-m-d',strtotime('-120 months',strtotime($sekarang)));
        //return $batas;
        $nama=Auth::user()->name;
        $email=Auth::user()->email;
        $cek1=DB::table('tb_emails')->where('email_address',$email)->get();
        foreach($cek1 as $dt){$id_user=$dt->id_employee;}
        $tb_admins=DB::table('tb_admins')->where('id_employee',$id_user)->get();

        $tb_leave=DB::table('tb_employees')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_employee_leaves','tb_employee_leaves.id_employee','=','tb_employees.id')
        ->where([['dept_id','0'],['position_id','<>','19'],['tb_employee_leaves.status','0'],['is_delete','0']]);
        foreach($tb_admins as $dt2){
            $tb_leave=$tb_leave->orwhere([['dept_id',$dt2->dept_id],['position_id','<>','19'],['tb_employee_leaves.status','0'],['is_delete','0']]);
        }
        $tb_leave=$tb_leave->orderby('employee_name','asc')->orderby('year','desc')
        ->get(['tb_employees.id as idemployee','tb_employees.NIK','tb_employees.employee_name','tb_employees.join_date','tb_departments.dept_code','tb_employee_leaves.*']);
        return view('page/admin/m_leave/leave_inactive',['tb_leave'=>$tb_leave,'menu'=>'leave','submenu'=>'apply']);
    }
    function updateOpsi(request $data){
        $tb_employee_leave=DB::table('tb_employee_leaves')->where('id',$data->id_leave)->get();
        foreach($tb_employee_leave as $dt){
            if($dt->qty_approval==2)$new=1;
            else $new=2;
            $update=DB::table('tb_employee_leaves')->where('id',$data->id_leave)->update(['qty_approval'=>$new]);
        }
        if($update)
        return "Sukses";
    }
    public function AutoCreateCuti(Request $request){
        date_default_timezone_set("Asia/Bangkok");
        $kalendar=CAL_GREGORIAN;
        $Tgl=date('Y-m-d');
        $sekarang=date('Y-m-d H:i:s');
        $MinTgl=date('Y-m-d',strtotime('1 weeks',strtotime($Tgl)));
        $tb_employee=tb_employee::
        leftJoin('tb_employee_leaves','tb_employee_leaves.id_employee','tb_employees.id')
        ->where('tb_employees.status',1)->get();
        foreach($tb_employee as $dt){
            $tgl1 = new DateTime($dt->join_date);
            $tgl2 = new DateTime($Tgl);
            $diffdays = $tgl2->diff($tgl1)->days;
            $diffyears=Floor($diffdays/365);
            $Thnawal=date('Y',strtotime($dt->join_date));
            $Thnstart=$Thnawal+$diffyears;
            $Thnend=$Thnstart+1;
            $Bln=date('m-d',strtotime($dt->join_date));
            if($Bln=='02-29')$Bln='02-28';
            $Periode_awal=$Thnstart.'-'.$Bln;
            $Periode_akhir_temp=$Thnend.'-'.$Bln;
            $Periode_akhir = date('Y-m-d', strtotime("-1 day", strtotime($Periode_akhir_temp)));
            $Periode_extend=date('Y-m-d', strtotime("+6 month", strtotime($Periode_akhir)));
            $tb_employee_leave=tb_employee_leave::where([['id_employee',$dt->id_employee],['end','>=',$Tgl]])->count();
        //return $tb_employee_leave;
        if($diffyears>=1)$jatah='12';
        else $jatah='0';
        //$kurang=$tb_employee_freeday;
        $tb_employee_freeday=0;
        $kurang=0;
        $outstanding=$jatah-$kurang;
       
        if($tb_employee_leave==0){
            tb_employee_leave::create([
                'id_employee'=>$dt->id_employee,
                'year'=>$Thnstart,
                'start'=>$Periode_awal,
                'end'=>$Periode_akhir,
                'extend'=>$Periode_extend,
                'sisa'=>'0',
                'kurang'=>$kurang,
                'allowance'=>$jatah,
                'used'=>$tb_employee_freeday,
                'outstanding'=>$outstanding,
                'remark'=>'AutoCreateCuti',
                'status'=>1,
                'admin'=>'System'
            ]);
        }
    }
        }
    function limitUpdate(request $data){
        if($data->limitstatus==1)$new=0;
        else $new=1;
        $update=DB::table('tb_utilities')->where('atribut','limit_leave_status')->update(['status'=>$new]);
        if($update)return "Sukses";
        else return $data->limitstatus;
    }
    function CClimitUpdate(request $data){
        $update=DB::table('tb_cost_center')->where('id',$data->idcc)->update(['leave_limit'=>$data->leavelimit]);
        if($update)return "Sukses";
        else return $data->idcc.' '.$data->leavelimit;
    }
    function leaveLimitCheck(Request $data){
        $teks='';
        $bagian='';
        $problem=0;
        $cek_status=DB::table('tb_utilities')->where('atribut','limit_leave_status')->where('status','1')->count();
        if($cek_status==1){
            $tgl1 = new DateTime($data->start);
            $tgl2 = new DateTime($data->finish);
            $diffdays = $tgl2->diff($tgl1)->days;
            $id_employee=$data->idemployee;
            $mulai=$data->start;

            for($i=0;$i<=$diffdays;$i++){
                $jml_leave=0;
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

                    $tb_cycle=tb_cycle::where('days',$modcycle)->where('group',$group)->where('shift','>','0')->count();
                    $tb_freeday=tb_freeday::where('date_off',$Tgl)->count();
                    $change_day=tb_freeday::where('date_off',$Tgl)->where('category','Working')->count();
                    if(($tb_cycle>0&&$tb_freeday==0)||($change_day>0)){
                        //Check Data Cuti
                        $tb_employees=DB::table('tb_employees')
                        ->leftjoin('tb_cost_center','tb_cost_center.cc_code','=','tb_employees.cc_code')
                        ->where('tb_employees.id',$id_employee)->get(['tb_employees.*','tb_cost_center.segment_name','tb_cost_center.leave_limit']);
                        foreach($tb_employees as $dt){
                            $cc_code=$dt->cc_code;
                            $bagian=$dt->segment_name;
                            $batas=$dt->leave_limit;
                            $tb_leaves=DB::table('tb_leaves')
                            ->leftjoin('tb_employees','tb_employees.id','=','tb_leaves.id_employee')
                            ->where('cc_code',$cc_code)
                            ->where('start_leave','<=',$Tgl)
                            ->where('finish_leave','>=',$Tgl)
                            ->get(['tb_employees.employee_name','tb_leaves.start_leave','tb_leaves.finish_leave','tb_leaves.id_leave']);
                            $teks_detail='';
                            foreach($tb_leaves as $dt2){
                                $teks_detail.=$dt2->employee_name.'; ';
                                $jml_leave++;
                            }
                        }

                        //End Check
                    }
                }
                if($batas<=$jml_leave){
                    $problem++;
                    $teks.=$Tgl." kuota sudah habis, diajukan oleh: ".$teks_detail."\n"; 
                }
            }
        }
        
        if($problem>0)$teks="Batas pengajuan cuti bagian ".$bagian." ".$batas." orang.\n".$teks;
        return $teks;
    }
    public function notificationLeave($id){
        $tb1=DB::table('tb_leaves')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_leaves.id_employee')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->where('tb_leaves.id',$id)
        ->get(['tb_leaves.*','tb_employees.employee_name','tb_departments.dept_name']);
        $data['kontak']='';
        $data['pesan']='';
        $id_employee='';
        foreach($tb1 as $dt){
            $id=$dt->id;
            $date1=$dt->start_leave;
            $date2=$dt->finish_leave;
            $dept=$dt->dept_name;
            if($dt->status_approved==0){
                $id_employee=$dt->approved;
                $pos='Atasan Pertama';
            }else if($dt->approved2>0&&$dt->status_approved2==0){
                $id_employee=$dt->approved2;
                $pos='Atasan Kedua';
            }else if($dt->legalized>0&&$dt->status_legalized==0){
                $id_employee=$dt->legalized;
                $pos='Personalia';
            }
            $name=$dt->employee_name;
            if($id_employee!=''){
                $data['kontak']=DB::table('tb_employee_detail')->where('id_employee',$id_employee)->value('nomor_telepon');
                if($pos!='Personalia'){
                    $data['pesan']="*NOTIFIKASI CUTI*\n\nID: *$id*\nTanggal: *$date1 - $date2*\nNama: *$name*\nDepartemen: *$dept*\n\nMenunggu Approval Anda sebagai *$pos*.\n\nSegera lakukan pengecekan via EMS, klik link berikut:\nhttps://ems.summitadyawinsa.co.id/EMS/Leave/Approves/0/0";
                }
                else{
                    $data['pesan']="*NOTIFIKASI CUTI*\n\nID: *$id*\nTanggal: *$date1 - $date2*\nNama: *$name*\nDepartemen: *$dept*\n\nMenunggu Approval Anda sebagai *$pos*.\n\nSegera lakukan pengecekan via EMS, klik link berikut:\nhttps://ems.summitadyawinsa.co.id/EMS/Leave/Legalizes_dirhr/0/0/Annual";
                }
            }
        }
        //$data['kontak']='08211212418';
        if($data['kontak']!=''){
            // \App\Http\Controllers\WhatsAppController::sendInternalMessage($data['kontak'], $data['pesan']);
            \App\Http\Controllers\WuzapiController::sendInternalMessage($data['kontak'], $data['pesan']);
            return 'Success';
        }else{
            return 'Failed';
        }
    }

}
