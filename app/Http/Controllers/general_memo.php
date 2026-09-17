<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Imports\MemoOTImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\Import_tb_memo_ot;

use DateTime;
use Auth;
use PDF;

use Illuminate\Support\Facades\Mail;
use App\Mail\general_memo_mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class general_memo extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
        $this->site = $_SERVER['SCRIPT_NAME'];
    }
    function index($category){
        $data['today']=date('Y-m-d');
        $data['table1']=DB::table('tb_memo_category')->get();
        $data['table2']=DB::table('tb_memo')->where('id_category',$category)->where('is_delete','0')->where('date_information','>=',$data['today'])->orderby('created_at','desc')->get();
        $data['table3']=DB::table('tb_template_memo')->where('id_category',$category)->get();
        //return $data['table2'];
        $data['category']=$category;
        if($category==2){
            $email=Auth::user()->email;
            $table1=DB::table('tb_emails')->where('email_address',$email)->get();
            foreach($table1 as $dt1){
                $id_employee=$dt1->id_employee;
                $table2=DB::table('tb_employees')->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
                ->where('tb_employees.id',$id_employee)->get(['tb_employees.NIK','tb_employees.employee_name','tb_positions.position_name','tb_departments.dept_code']);
                foreach($table2 as $dt2){
                    $data['table3']=DB::table('tb_template_memo')->where('id_category',$category)->where('from_dept',$dt2->dept_code)->get();
                }
            }
        }
        return view('page/general_memo/memo',['data'=>$data,'site'=>$this->site,'menu'=>'memo']);
    }
    function archieveMemo($category,$periode){
        $data['today']=date('Y-m-d');
        if($periode==0)$periode=date('Y-m');
        $data['table1']=DB::table('tb_memo_category')->get();
        $data['table2']=DB::table('tb_memo')->where('id_category',$category)->where('is_delete','0')->where('date_information','like',$periode.'%')->where('date_information','<',$data['today'])->get();
        $data['table3']=DB::table('tb_template_memo')->where('id_category',$category)->get();
        //return $data['table2'];
        $data['category']=$category;
        $data['periode']=$periode;
        return view('page/general_memo/memo_archieve',['data'=>$data,'site'=>$this->site,'menu'=>'memo']);
    }
    public function createMemo(Request $data){
        $now=date('Y-m-d H:i:s');
        $id_memo=$data->id_memo;
        $date_information=$data->date_information;
        $additional_note=$data->additional_note;
        $id_template_memo=$data->id_template_memo;

        $admin=Auth::user()->name;
        $periode=date('Y-m');

        $id_memo=$data->id_memo;
        $table1=DB::table('tb_template_memo')->where('id',$id_template_memo)->get();
        foreach($table1 as $dt1){
            $legal_num=$this->generateNumber($periode,$dt1->code_memo);
            if($id_memo>0){
                $now=date('Y-m-d H:i:s');
                $update_memo=DB::table('tb_memo')->where('id',$data->id_memo)->update([
                    'memo_number'=>$legal_num,
                    'date_information'=>$date_information,
                    'id_category'=>$dt1->id_category,
                    'id_template_memo'=>$dt1->id,
                    'code_memo'=>$dt1->code_memo,
                    'description'=>$dt1->description,
                    'from_dept'=>$dt1->from_dept,
                    'to_dept'=>$dt1->to_dept,
                    'up_name'=>$dt1->up_name,
                    'cc_name'=>$dt1->cc_name,
                    'opening_text'=>$dt1->opening_text,
                    'table_name'=>$dt1->table_name,
                    'closing_text'=>$dt1->closing_text,
                    'created_by'=>$admin,
                    'additional_note'=>$additional_note,
                    'periode'=>$periode,
                    'updated_at'=>$now,
                ]);
            }else{
                $add_memo=DB::table('tb_memo')->insert([
                    'memo_number'=>$legal_num,
                    'date_information'=>$date_information,
                    'id_category'=>$dt1->id_category,
                    'id_template_memo'=>$dt1->id,
                    'code_memo'=>$dt1->code_memo,
                    'description'=>$dt1->description,
                    'from_dept'=>$dt1->from_dept,
                    'to_dept'=>$dt1->to_dept,
                    'up_name'=>$dt1->up_name,
                    'cc_name'=>$dt1->cc_name,
                    'opening_text'=>$dt1->opening_text,
                    'table_name'=>$dt1->table_name,
                    'closing_text'=>$dt1->closing_text,
                    'created_by'=>$admin,
                    'additional_note'=>$additional_note,
                    'periode'=>$periode,
                    'created_at'=>$now,
                ]);
                $table=DB::table('tb_memo')->where('created_by',$admin)->where('created_at',$now)->get();
                foreach($table as $dt){
                    $id_memo=$dt->id;
                }
                if($dt1->id_category==2){
                    $today=date('Y-m-d');
                    $tomorow=date('Y-m-d H:i:s',strtotime('+1 days',strtotime($now)));
                    $email=Auth::user()->email;
                    $table1=DB::table('tb_emails')->where('email_address',$email)->get();
                    foreach($table1 as $dt1){
                        $id_employee=$dt1->id_employee;
                        $table2=DB::table('tb_employees')->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
                        ->where('tb_employees.id',$id_employee)->get(['tb_employees.NIK','tb_employees.employee_name','tb_positions.position_name','tb_departments.dept_code']);
                        foreach($table2 as $dt2){
                            $add_detail=DB::table('tb_memo_access')->insert([
                                'id_memo'=>$id_memo,
                                'nik'=>$dt2->NIK,
                                'nama'=>$dt2->employee_name,
                                'position'=>$dt2->position_name,
                                'department'=>$dt2->dept_code,
                                'start'=>$now,
                                'end'=>$tomorow,
                                'created_by'=>$admin
                            ]);
                        }
                    }
                }
            }
        }
        return $id_memo;

    }


    public function deleteMemo(Request $data){
        $now=date('Y-m-d H:i:s');
        $update_memo=DB::table($data->tb_name)->where('id',$data->id)->update([
            'is_delete'=>'1',
            'updated_at'=>$now,
        ]);
        // return $data->tb_name.' '.$data->id
        if($update_memo)return response('Sukses', 200);
        else return response('Gagal', 400);
    }
    public function generateNumber($periode,$code_memo){
        $thn=date('Y',strtotime($periode.'-01'));
        $bln=date('m',strtotime($periode.'-01'));
        $table1=DB::table('tb_memo')->where('periode',$periode)->where('code_memo',$code_memo)->count();
        $next=$table1+1;
        if(strlen($next)==1)$no='00'.$next;
        elseif(strlen($next)==2)$no='0'.$next;
        else $no=$next;

        if($bln=='01')$romawi='I';
        else if($bln=='02')$romawi='II';
        else if($bln=='03')$romawi='III';
        else if($bln=='04')$romawi='IV';
        else if($bln=='05')$romawi='V';
        else if($bln=='06')$romawi='VI';
        else if($bln=='07')$romawi='VII';
        else if($bln=='08')$romawi='VIII';
        else if($bln=='09')$romawi='IX';
        else if($bln=='10')$romawi='X';
        else if($bln=='11')$romawi='XI';
        else if($bln=='12')$romawi='XII';
        else $romawi='';

        $legal_num=$no.'/'.$code_memo.'/'.$romawi.'/'.$thn;
        return $legal_num;
    }
    function detailMemo($id){
        //return "Masuk";
        $email=Auth::user()->email;
        $admin=Auth::user()->name;
        $data['admin']=$admin;
        $now=date('Y-m-d H:i:s');
        $table1=DB::table('tb_emails')->where('email_address',$email)->get();
        foreach($table1 as $dt1){
            $id_employee=$dt1->id_employee;
        }

        $this->addApproval($id);
        $data['table1']=DB::table('tb_memo')->where('id',$id)->get();
        foreach($data['table1'] as $dt){
            $data['table_name']=$dt->table_name;
            $data['status_draft']=$dt->is_draft;
            $data['from_dept']=$dt->from_dept;
            $data['to_dept']=$dt->to_dept;
            $data['created_by']=$dt->created_by;
        }
        // $data['table2']=DB::table($data['table_name'])->where('id_memo',$id)->where('is_delete','0')->get();
        if($data['table_name']=='tb_memo_ot'){
            $data['table2']=DB::table($data['table_name'])
            ->leftjoin('tb_memo','tb_memo.id','=',$data['table_name'].'.id_memo')
            ->leftjoin('tb_reason_ots','tb_reason_ots.reason_ot','=',$data['table_name'].'.reason_ot')
            ->where($data['table_name'].'.id_memo',$id)->where($data['table_name'].'.is_delete','0')->get([$data['table_name'].'.*','tb_reason_ots.reason_ot as check_reason','tb_memo.memo_number']);
        }else{
            $data['table2']=DB::table($data['table_name'])
            ->where($data['table_name'].'.id_memo',$id)->where($data['table_name'].'.is_delete','0')->get();
        }
        if($data['table_name']=='tb_memo_access'){
            // $data['table3']=DB::table('tb_memo_approval')
            // ->leftjoin('tb_template_approval_person','tb_template_approval_person.id_template_approval','=','tb_memo_approval.id_template_approval')
            // ->where('tb_memo_approval.id_memo',$id)->orderby('tb_memo_approval.seq_approval','asc')->get(['tb_memo_approval.*','tb_template_approval_person.id_employee as idemployee','tb_template_approval_person.employee_name as employeename','tb_template_approval_person.email_address as emailaddress']);
            $data['table3']=DB::table('tb_memo_approval')->where('id_memo',$id)->orderby('seq_approval','asc')->get();
        }else{
            $data['table3']=DB::table('tb_memo_approval')->where('id_memo',$id)->orderby('seq_approval','asc')->get();
        }
        $data['my_pos']='';
        $data['is_completed']=0;
        $data['progress']=0;
        foreach($data['table3'] as $dt3){
            $data['table4']=DB::table('tb_template_approval_person')->where('id_template_approval',$dt3->id_template_approval)->get();
            foreach($data['table4'] as $dt4){
                if($dt4->id_employee==$id_employee)$data['my_pos'].='#'.$dt4->id_template_approval;
            }
            if($dt3->approval_status==1)$data['progress']=$dt3->seq_approval;
        }
        $data['progress']++;

        $data['table5']=DB::table('tb_memo_approval')->where('id_memo',$id)->where('approval_group','2')->where('approval_status','0')->count();
        if($data['table5']==0)$data['status_completed']=1;
        else $data['status_completed']=0;

        $data['id_memo']=$id;
        //return $data['table2'];
        return view('page/general_memo/'.$data['table_name'],['data'=>$data,'site'=>$this->site,'menu'=>'memo']);

    }
    function detailArchieve($kategori,$periode){
        $data['table1']=DB::table('tb_memo_category')->where('id',$kategori)->get();
        foreach($data['table1'] as $dt){
            $data['table_name']=$dt->table_name;
        }
        $data['table2']=DB::table($data['table_name'])
        ->leftjoin('tb_memo','tb_memo.id','=',$data['table_name'].'.id_memo')
        ->where('date_information','like',$periode.'%')
        ->where($data['table_name'].'.is_delete','0')
        ->get([$data['table_name'].'.*','tb_memo.code_memo','tb_memo.from_dept','tb_memo.to_dept','tb_memo.memo_number']);
        //return $data['table2'];
        $data['periode']=$periode;
        $data['kategori']=$kategori;
        return view('page/general_memo/'.$data['table_name'].'_archieve',['data'=>$data,'site'=>$this->site,'menu'=>'memo']);

    }
    function templateTable($id){
        $data['table1']=DB::table('tb_memo')->where('id',$id)->get();
        foreach($data['table1'] as $dt){
            $data['table_name']=$dt->table_name;
        }
        $data['id_memo']=$id;
        $data['today']=date('Y-m-d');
        $data['now']=date('Y-m-d H:i:s');
        //return $link;
        return view('page/general_memo/template_'.$data['table_name'],['data'=>$data,'site'=>$this->site,'menu'=>'memo']);
    }
    public function addApproval($id_memo){
        $table1=DB::table('tb_memo_approval')->where('id_memo',$id_memo)->count();
        if($table1==0){
            $table2=DB::table('tb_memo')->where('id',$id_memo)->get();
            foreach($table2 as $dt2){
                $table3=DB::table('tb_template_approval')->where('id_template_memo',$dt2->id_template_memo)->get();
                foreach($table3 as $dt3){
                    $add=DB::table('tb_memo_approval')->insert([
                        'id_memo'=>$id_memo,
                        'id_template_approval'=>$dt3->id,
                        'approval_group'=>$dt3->approval_group,
                        'seq_approval'=>$dt3->seq_approval,
                        'position'=>$dt3->position,
                        'job_approval'=>$dt3->job_approval,
                        'is_mandatory'=>$dt3->is_mandatory
                    ]);
                }
            }
        }
    }
    function ImportTable(Request $request){
        $request->validate([
            'file' => 'required|mimes:xls,xlsx,csv|max:2048'
        ]);
        $file = $request->file('file');
        $nama_file = rand().$file->getClientOriginalName();
        $file->move('laravel/public/excel',$nama_file);
        $import=Excel::import(new Import_tb_memo_ot, public_path('/excel/'.$nama_file));
        if($import){
          return redirect()->back()->with(['info'=>'Import Berhasil']);
        }else{
            return redirect()->back()->with(['errors'=>'Gagal Import']);
        }
    }
    function confirmMemo(Request $data){
        $now=date('Y-m-d H:i:s');
        $update_memo=DB::table('tb_memo')->where('id',$data->id_memo)->update([
            'is_draft'=>$data->status,
            'updated_at'=>$now,
        ]);
        if($data->status=='1'){
            //Reset Approval
            $table1=DB::table('tb_memo_approval')->where('id_memo',$data->id_memo)->update([
                'id_employee'=>null,
                'employee_name'=>null,
                'email_address'=>null,
                'approval_status'=>'0',
                'approved_date'=>null
            ]);
            $delete=DB::table('tb_utilities_exception')->where('id_memo',$data->id_memo)->delete();
        }else{
            $table2=DB::table('tb_memo')->where('id',$data->id_memo)->get();
            foreach($table2 as $dt2){
                if($dt2->id_category==2){
                    $update=DB::table('tb_memo_access')->where('id_memo',$data->id_memo)->update([
                        'e_permit'=>$data->permit,
                        'e_spl'=>$data->spl,
                        'e_leave'=>$data->leave,
                        'alasan'=>$data->alasan,
                        'end'=>$data->end_date
                    ]);
                    $table3=DB::table('tb_memo_access')->where('id_memo',$data->id_memo)->get();
                    foreach($table3 as $dt3){
                        $start=$dt3->start;
                        $end=$dt3->end;
                        $created_by=$dt3->created_by;
                    }

                    if($data->permit==1){
                        $cek=DB::table('tb_utilities_exception')->where('id_utility','17')->where('id_memo',$data->id_memo)->count();
                        if($cek==0){
                            $add=DB::table('tb_utilities_exception')->insert([
                                'id_utility'=>'17',
                                'admin'=>$created_by,
                                'start'=>$start,
                                'end'=>$end,
                                'id_memo'=>$data->id_memo
                            ]);
                        }
                    }else{$delete=DB::table('tb_utilities_exception')->where('id_memo',$data->id_memo)->where('id_utility','17')->delete();}
                    
                    if($data->leave==1){
                        $cek=DB::table('tb_utilities_exception')->where('id_utility','15')->where('id_memo',$data->id_memo)->count();
                        if($cek==0){
                            $add=DB::table('tb_utilities_exception')->insert([
                                'id_utility'=>'15',
                                'admin'=>$created_by,
                                'start'=>$start,
                                'end'=>$end,
                                'id_memo'=>$data->id_memo
                            ]);
                        }
                    }else{$delete=DB::table('tb_utilities_exception')->where('id_memo',$data->id_memo)->where('id_utility','15')->delete();}
                    
                    if($data->spl==1){
                        $cek=DB::table('tb_utilities_exception')->where('id_utility','9')->where('id_memo',$data->id_memo)->count();
                        if($cek==0){
                            $add=DB::table('tb_utilities_exception')->insert([
                                'id_utility'=>'9',
                                'admin'=>$created_by,
                                'start'=>$start,
                                'end'=>$end,
                                'id_memo'=>$data->id_memo
                            ]);
                        }
                        $cek=DB::table('tb_utilities_exception')->where('id_utility','14')->where('id_memo',$data->id_memo)->count();
                        if($cek==0){
                            $add=DB::table('tb_utilities_exception')->insert([
                                'id_utility'=>'14',
                                'admin'=>$created_by,
                                'start'=>$start,
                                'end'=>$end,
                                'id_memo'=>$data->id_memo
                            ]);
                        }
                    }else{
                        $delete=DB::table('tb_utilities_exception')->where('id_memo',$data->id_memo)->where('id_utility','9')->delete();
                        $delete=DB::table('tb_utilities_exception')->where('id_memo',$data->id_memo)->where('id_utility','14')->delete();
                    }
                }
            }
            //$this->sendMail($data->id_memo);
            $this->notificationMemo($data->id_memo);            
        }
        return $data->id_memo.' '.$data->status;
    }
    function updateStatus(Request $data){
        $admin=Auth::user()->name;
        $now=date('Y-m-d H:i:s');

        if($data->status=='0'){
            $update_memo=DB::table('tb_memo_ot')->where('id',$data->id)->update([
                'canceled_by'=>$admin,
                'canceled_at'=>$now,
                'status'=>$data->status,
                'updated_at'=>$now,
            ]);
        }else{
            $update_memo=DB::table('tb_memo_ot')->where('id',$data->id)->update([
                'canceled_by'=>null,
                'canceled_at'=>null,
                'status'=>$data->status,
                'updated_at'=>$now,
            ]);
        }
        return $data->id.' '.$data->status;
    }
    function updateApproval(Request $data){
        $email=Auth::user()->email;
        $admin=Auth::user()->name;
        $now=date('Y-m-d H:i:s');
        $table1=DB::table('tb_emails')->where('email_address',$email)->get();
        foreach($table1 as $dt1){
            $id_employee=$dt1->id_employee;
        }
        $table2=DB::table('tb_memo_approval')
        ->leftjoin('tb_template_approval_person','tb_template_approval_person.id_template_approval','=','tb_memo_approval.id_template_approval')
        ->where('tb_memo_approval.id',$data->id)->get(['tb_template_approval_person.*','tb_memo_approval.id_memo']);
        $hasil=0;
        foreach($table2 as $dt2){
            if($id_employee==$dt2->id_employee)$hasil++;
            $id_memo=$dt2->id_memo;
        }
        if($hasil>0){
            if($data->status==0){
                $update_approval=DB::table('tb_memo_approval')->where('id',$data->id)->update([
                    'id_employee'=>null,
                    'employee_name'=>null,
                    'email_address'=>null,
                    'approval_status'=>$data->status,
                    'approved_date'=>null,
                ]);
            }else{
                $table6=DB::table('tb_memo')->where('id',$id_memo)->where('is_draft','1')->count();
                if($table6==0){
                    $update_approval=DB::table('tb_memo_approval')->where('id',$data->id)->update([
                        'id_employee'=>$id_employee,
                        'employee_name'=>$admin,
                        'email_address'=>$email,
                        'approval_status'=>$data->status,
                        'approved_date'=>$now,
                    ]);
            
                    //$this->sendMail($id_memo);

                }
            }
            $table3=DB::table('tb_memo_approval')->where('id_memo',$id_memo)->where('approval_group','3')->where('approval_status','0')->count();
            $table4=DB::table('tb_memo_approval')->where('id_memo',$id_memo)->where('approval_group','2')->where('approval_status','0')->count();
            $table5=DB::table('tb_memo_approval')->where('id_memo',$id_memo)->where('approval_group','1')->where('approval_status','0')->count();
            if($table3==0){
                $status_completed=3;
                //Update End
                    $yesterday=date('Y-m-d H:i:s',strtotime('+2 days',strtotime($now)));
                    $cek_tb=DB::table('tb_utilities_exception')->where('id_memo',$id_memo)->get();
                    foreach($cek_tb as $cek_dt){
                        $cek_end=$cek_dt->end;
                        if($yesterday >$cek_end){
                            $update_end=DB::table('tb_utilities_exception')->where('id_memo',$id_memo)->update(['end'=>$yesterday,'remark'=>'Update End']);
                        }
                    }
                //
                $update_utility=DB::table('tb_utilities_exception')->where('id_memo',$id_memo)->update(['status'=>'1']);
            }
            elseif($table4==0)$status_completed=2;
            elseif($table5==0)$status_completed=1;
            else $status_completed=0;
            $update_complete=DB::table('tb_memo')->where('id',$id_memo)->update(['is_completed'=>$status_completed]);
            $this->notificationMemo($id_memo);
        }

        return $data->id.' '.$data->status.' '.$hasil;

    }
    public function sendMail($id_memo){

        $table1 = DB::table('tb_memo_approval')
            ->leftjoin('tb_memo', 'tb_memo.id', '=', 'tb_memo_approval.id_memo')
            ->leftjoin('tb_template_memo', 'tb_template_memo.id', '=', 'tb_memo.id_template_memo')
            ->leftjoin('tb_template_approval_person', 'tb_template_approval_person.id_template_approval', '=', 'tb_memo_approval.id_template_approval')
            ->where('tb_memo_approval.id_memo', $id_memo)
            ->orderby('seq_approval', 'asc')
            ->get(['tb_template_approval_person.email_address', 'tb_template_approval_person.employee_name', 'tb_memo_approval.*', 'tb_memo.memo_number', 'tb_template_memo.table_name']);

        $post_prev = 0;
        $pos_next = 0;
        $group_prev = 0;
        $group_next = 0;
        $lock = 0;

        foreach ($table1 as $dt1) {
            $no_doc = $dt1->memo_number;
            $memo_category = $dt1->table_name;
            $data_table = DB::table($memo_category)->where('id_memo', $id_memo)->get();

            if ($dt1->approval_status == 1) {
                $pos_prev = $dt1->seq_approval;
                $pos_next = $pos_prev + 1;
                $group_prev = $dt1->approval_group;
            }
        }
        if($pos_next==0)$pos_next++;

        $table2 = DB::table('tb_memo_approval')
            ->where('tb_memo_approval.id_memo', $id_memo)
            ->where('seq_approval', $pos_next)
            ->get();

        foreach ($table2 as $dt2) {
            $group_next = $dt2->approval_group;
        }

        $i=0;
        if ($group_prev == 2 && $group_next == 3) {
            $table3 = DB::table('tb_memo_approval')
                ->leftjoin('tb_template_approval_person', 'tb_template_approval_person.id_template_approval', '=', 'tb_memo_approval.id_template_approval')
                ->where('tb_memo_approval.id_memo', $id_memo)
                ->where('approval_group', '3')
                ->get(['tb_template_approval_person.email_address']);
                $i++;
        } elseif ($group_prev <= 2 && $group_next <= 2) {
            $table3 = DB::table('tb_memo_approval')
                ->leftjoin('tb_template_approval_person', 'tb_template_approval_person.id_template_approval', '=', 'tb_memo_approval.id_template_approval')
                ->where('tb_memo_approval.id_memo', $id_memo)
                ->where('seq_approval', $pos_next)
                ->get(['tb_template_approval_person.email_address']);
                $i++;
        }

        $recipients = [];
        //$no = 0;

        if($i>0){
            foreach ($table3 as $dt3) {
                //$no++;
                //if ($no == 1) {
                    $recipients[] = $dt3->email_address;
                //}
            }
            // Generate token unik untuk setiap penerima
            foreach ($recipients as $recipientEmail) {
                $approvalToken = Str::random(32);
                
                // Simpan token ke database SEBELUM mengirim email
                DB::connection('memo')
                    ->table('tb_memo_approval')
                    ->where('id_memo', $id_memo)
                    ->where(function($query) use ($pos_next, $group_next) {
                        if ($group_next == 3) {
                            $query->where('approval_group', 3);
                        } else {
                            $query->where('seq_approval', $pos_next);
                        }
                    })
                    ->update([
                        'approval_token' => $approvalToken,
                        'token_expires_at' => now()->addHours(24)
                    ]);

                // Kirim email ke masing-masing penerima
                $kirim = Mail::to($recipientEmail)
                    //->bcc('cahyudin@summitadyawinsa.co.id')            
                    ->queue(new general_memo_mail(
                        $recipientEmail,
                        $no_doc,
                        $memo_category,
                        $data_table,
                        $id_memo,
                        $approvalToken,
                        $group_next
                    ));
            }

            $recipientsString = implode(', ', $recipients);
            Log::info('Module Memo, Send Mail to ' . $recipientsString . ' for memo ID: ' . $id_memo);
        }

        // Response atau redirect sesuai kebutuhan
        return redirect()->back()->with('success', 'Email approval telah dikirim');        
        
    }
    function previewMemo($id){
        $data['table1']=DB::table('tb_memo')->where('id',$id)->get();
        foreach($data['table1'] as $dt){
            $data['table_name']=$dt->table_name;
            $data['nomor_memo']=$dt->memo_number;
        }
        $data['table2']=DB::table($data['table_name'])
        ->leftjoin('tb_reason_ots','tb_reason_ots.reason_ot','=',$data['table_name'].'.reason_ot')
        ->where($data['table_name'].'.id_memo',$id)->where($data['table_name'].'.is_delete','0')->get([$data['table_name'].'.*','tb_reason_ots.reason_ot as check_reason']);

        $data['table3']=DB::table('tb_memo_approval')->where('id_memo',$id)->orderby('seq_approval','asc')->get();

        $FileName='MEMO OT '.$data['nomor_memo'].'.PDF';
        $pdf = PDF::loadview('page/general_memo/'.$data['table_name'].'_pdf',['data'=>$data,'id_memo'=>$id,'site'=>$this->site,'menu'=>'memo'])->setPaper('a4','potret');
        return $pdf->stream($FileName);
    }
    public function notificationMemo($id){
        $tb_memo=DB::table('tb_memo')->where('id',$id)->get();
        foreach($tb_memo as $dt){
            $memo_number=$dt->memo_number;
            $date=$dt->date_information;
            $desc=$dt->description;
            $dept=$dt->from_dept;
        }
        $pos=DB::table('tb_memo_approval')->where('id_memo',$id)->where('approval_status','0')->orderby('approval_group','asc')->orderby('seq_approval')->limit(1)->value('position');
        $tb=DB::table('tb_memo_approval')->where('id_memo',$id)->where('approval_status','0')->orderby('approval_group','asc')->orderby('seq_approval')->limit(1)->value('id_template_approval');
        $tb1=DB::table('tb_template_approval_person')->where('id_template_approval',$tb)->get();

        $data['kontak']='';
        $data['pesan']='';
        $id_employee='';
        foreach($tb1 as $dt){
            $id_employee=$dt->id_employee;
            $name=$dt->employee_name;
            if($id_employee!=''){
                $data['kontak']=DB::table('tb_employee_detail')->where('id_employee',$id_employee)->value('nomor_telepon');
                $data['pesan']="*NOTIFIKASI MEMO*\n\nNO: *$memo_number*\nTanggal: *$date*\nTentang: *$desc*\nNama: *$name*\nDepartemen: *$dept*\n\nMenunggu Approval Anda sebagai *$pos*.\n\nSegera lakukan pengecekan via EMS, klik link berikut:\nhttps://ems.summitadyawinsa.co.id/EMS/GeneralMemo/Detail/$id";
            }
        }
        //$data['kontak']='083148870127';
        if($data['kontak']!=''){
            // \App\Http\Controllers\WhatsAppController::sendInternalMessage($data['kontak'], $data['pesan']);
            \App\Http\Controllers\WuzapiController::sendInternalMessage($data['kontak'], $data['pesan']);
            return 'Success';
        }else{
            return 'Failed';
        }
    }

}
