<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Image;
use Maatwebsite\Excel\Facades\Excel;
use Session;
use DateTime;
use Auth;
use PDF;
use App\Mail\slip_Gaji_Payroll;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Validator;

class training_controller extends Controller
{
    private $site;
    public function __construct(){
        $this->middleware(['auth','verified']);
        $this->site = $_SERVER['SCRIPT_NAME'];
    }
    function index($type,$category){
        if (request()->user()->hasRole('root')||request()->user()->hasRole('training')){
            if($category==0)$category='All Category';
            $tb_skill_type=DB::table('tb_skill_type')->orderby('id','asc')->get();
            $tb_category=DB::table('tb_training_category')->orderby('id','asc')->get();
            $skill_type='';
            foreach($tb_skill_type as $dt){
                if($type==$dt->id)$skill_type=$dt->skill_type;
            }
            $tb_training_list=DB::table('tb_training_list')
            ->leftjoin('tb_skill_type','tb_skill_type.id','=','tb_training_list.id_type')
            ->where('tb_training_list.is_delete','0');
            if($type>0)$tb_training_list=$tb_training_list->where('id_type',$type);
            if($category!='All Category')$tb_training_list=$tb_training_list->where('category',$category);
            $tb_training_list=$tb_training_list->orderby('id','asc')->get(['tb_training_list.*','tb_skill_type.skill_type']);
            return view('page/training/training_list',['tb_training_list'=>$tb_training_list,'tb_category'=>$tb_category,'tb_skill_type'=>$tb_skill_type,'type'=>$type,'category'=>$category,'skill_type'=>$skill_type,'site'=>$this->site,'menu'=>'training','juduls'=>'Training List']);
        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }
    function training_plan(){
        if (request()->user()->hasRole('root')||request()->user()->hasRole('training')){
            $tb_training_schedule=DB::connection('training')->table('tb_training_schedule')
            ->leftjoin('tb_training_list','tb_training_list.id','=','tb_training_schedule.id_training')
            ->leftjoin('tb_skill_type','tb_skill_type.id','=','tb_training_list.id_type')
            ->where('tb_training_schedule.is_delete','0')
            ->orderby('tb_training_schedule.id','desc')->get(['tb_training_schedule.*','tb_training_list.training_name','tb_skill_type.skill_type']);
            $tb_training_list=DB::connection('training')->table('tb_training_list')->orderby('training_name','asc')->get();

            $tb_department=DB::connection('mysql')->table('tb_departments')->orderby('dept_name','asc')->get();

            return view('page/training/training_plan',['tb_training_list'=>$tb_training_list,'tb_training_schedule'=>$tb_training_schedule,'tb_department'=>$tb_department,'site'=>$this->site,'menu'=>'training_activity','juduls'=>'Training Schedule']);
        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }
    function training_periode($periode){
        if($periode==0)$periode=date('Y-m');
        $tahun=date('Y',strtotime($periode.'-01'));
        if (request()->user()->hasRole('root')||request()->user()->hasRole('training')){
            $tb_training_schedule=DB::table('tb_training_schedule')
            ->leftjoin('tb_training_list','tb_training_list.id','=','tb_training_schedule.id_training')
            ->leftjoin('tb_skill_type','tb_skill_type.id','=','tb_training_list.id_type')
            ->where('tb_training_schedule.is_delete','0')
            ->where('tb_training_schedule.periode',$periode)
            ->orderby('tb_training_schedule.id','desc')->get(['tb_training_schedule.*','tb_training_list.training_name','tb_skill_type.skill_type']);
            $tb_training_list=DB::table('tb_training_list')->orderby('training_name','asc')->get();

            $tb_department=DB::table('tb_departments')->orderby('dept_name','asc')->get();

            return view('page/training/training_periode',['tb_training_list'=>$tb_training_list,'tb_training_schedule'=>$tb_training_schedule,'tb_department'=>$tb_department,'tahun'=>$tahun,'periode'=>$periode,'site'=>$this->site,'menu'=>'training_activity','juduls'=>'Training Schedule']);
        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }
    function training_plan_participant($id){
        if (request()->user()->hasRole('root')||request()->user()->hasRole('training')){
            $tb_employee=DB::table('tb_employees')->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')->where([['tb_employees.status','1']])->orderby('employee_name','asc')->get(['tb_employees.*','tb_departments.dept_code']);
            $tb_training_schedule=DB::table('tb_training_schedule')
            ->leftjoin('tb_training_list','tb_training_list.id','=','tb_training_schedule.id_training')
            ->leftjoin('tb_skill_type','tb_skill_type.id','=','tb_training_list.id_type')
            ->where('tb_training_schedule.id',$id)
            ->orderby('tb_training_schedule.id','desc')->get(['tb_training_schedule.*','tb_training_list.training_name','tb_skill_type.skill_type']);
            $tb_training_invitation=DB::table('tb_training_invitation')->where('id_training_schedule',$id)->orderby('id','desc')->get();

            $tb_training_document=DB::table('tb_training_document')->orderby('document_name','asc')->get();
            $tb_related_document=DB::table('tb_related_document')
            ->leftjoin('tb_training_document','tb_training_document.id','=','tb_related_document.id_document')
            ->where('id_training_schedule',$id)->orderby('id','desc')->get(['tb_related_document.*','tb_training_document.document_name','tb_training_document.file_name']);

            $tb_training_test=DB::table('tb_training_test')->orderby('test_name','asc')->get();
            $tb_related_test=DB::table('tb_related_test')
            ->leftjoin('tb_training_test','tb_training_test.id','=','tb_related_test.id_test')
            ->where('id_training_schedule',$id)->get(['tb_related_test.*','tb_training_test.test_name','tb_training_test.minutes','tb_training_test.passing_grade']);

            return view('page/training/training_plan_participant',['tb_training_invitation'=>$tb_training_invitation,'tb_training_schedule'=>$tb_training_schedule,'tb_employee'=>$tb_employee,'id_training'=>$id,'tb_training_document'=>$tb_training_document,'tb_related_document'=>$tb_related_document,'tb_training_test'=>$tb_training_test,'tb_related_test'=>$tb_related_test,'site'=>$this->site,'menu'=>'training_activity','juduls'=>'Training Schedule']);
        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }
    function training_actual_group($type,$category){
        if (request()->user()->hasRole('root')||request()->user()->hasRole('training')){
            if($category==0)$category='All Category';
            $tb_skill_type=DB::connection('training')->table('tb_skill_type')->orderby('id','asc')->get();
            $skill_type='';
            foreach($tb_skill_type as $dt){
                if($type==$dt->id)$skill_type=$dt->skill_type;
            }

            $tb_training_actual=DB::connection('training')->table('tb_training_actual')
            ->leftjoin('tb_training_schedule','tb_training_schedule.id','=','tb_training_actual.id_training_schedule')
            ->leftjoin('tb_training_list','tb_training_list.id','=','tb_training_schedule.id_training')
            ->leftjoin('tb_skill_type','tb_skill_type.id','=','tb_training_list.id_type')
            ->where('tb_training_actual.is_delete','0');
            if($type>0)$tb_training_actual=$tb_training_actual->where('tb_training_list.id_type',$type);
            if($category!='All Category')$tb_training_actual=$tb_training_actual->where('tb_training_list.category',$category);
            $tb_training_actual=$tb_training_actual->orderby('tb_training_actual.id','desc')
            ->get(['tb_training_actual.*','tb_training_list.training_name','tb_training_list.category','tb_skill_type.skill_type']);
            return view('page/training/training_actual_group',['tb_training_actual'=>$tb_training_actual,'tb_skill_type'=>$tb_skill_type,'skill_type'=>$skill_type,'type'=>$type,'category'=>$category,'site'=>$this->site,'menu'=>'training_activity','juduls'=>'Actual Training']);
        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }
    function training_actual(){
        if (request()->user()->hasRole('root')||request()->user()->hasRole('training')){
            $tb_training_actual=DB::connection('training')->table('tb_training_actual')
            ->leftjoin('tb_training_schedule','tb_training_schedule.id','=','tb_training_actual.id_training_schedule')
            ->leftjoin('tb_training_list','tb_training_list.id','=','tb_training_schedule.id_training')
            ->leftjoin('tb_skill_type','tb_skill_type.id','=','tb_training_list.id_type')
            ->where('tb_training_actual.is_delete','0')
            //->where('tb_training_actual.in_class','1')
            ->orderby('tb_training_actual.id','desc')->get(['tb_training_actual.*','tb_training_list.training_name','tb_skill_type.skill_type']);
            $tb_training_personal=DB::connection('training')->table('tb_training_actual')
            ->leftjoin('tb_training_schedule','tb_training_schedule.id','=','tb_training_actual.id_training_schedule')
            ->leftjoin('tb_training_list','tb_training_list.id','=','tb_training_schedule.id_training')
            ->leftjoin('tb_skill_type','tb_skill_type.id','=','tb_training_list.id_type')
            ->leftjoin('tb_training_participant','tb_training_participant.id_training_actual','=','tb_training_actual.id')
            ->where('tb_training_actual.is_delete','0')
            //->where('tb_training_actual.in_class','0')
            ->orderby('tb_training_actual.id','desc')->get(['tb_training_actual.*','tb_training_list.training_name','tb_skill_type.skill_type','tb_training_participant.nama_karyawan','tb_training_participant.NIK','tb_training_participant.department','tb_training_participant.jabatan']);
            $tb_training_schedule=DB::connection('training')->table('tb_training_schedule')
            ->leftjoin('tb_training_list','tb_training_list.id','=','tb_training_schedule.id_training')
            ->leftjoin('tb_skill_type','tb_skill_type.id','=','tb_training_list.id_type')
            ->where('tb_training_schedule.is_delete','0')
            ->orderby('tb_training_schedule.id','desc')->get(['tb_training_schedule.*','tb_training_list.training_name','tb_skill_type.skill_type']);
            return view('page/training/training_actual',['tb_training_actual'=>$tb_training_actual,'tb_training_personal'=>$tb_training_personal,'tb_training_schedule'=>$tb_training_schedule,'site'=>$this->site,'menu'=>'training_activity','juduls'=>'Actual Training']);
        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }
    function training_inclass(Request $request){
        if (request()->user()->hasRole('root')||request()->user()->hasRole('training')){
            $columns = array( 
                0 =>'',
                1 =>'id', 
                2 =>'id_overtime',
                3 =>'ot_date',
                4 =>'dept_name' ,
                5 =>'status_diperintah' ,   
                6 =>'disetujui' ,   
                7 =>'diketahui' ,   
                8 =>'status_dicatat' , 
                9 =>'status_approve' ,   
                10 =>'status_dicatat' ,   
                11 =>'status_paid'
              );   
              $tb_training_actual=DB::connection('training')->table('tb_training_actual')
              ->leftjoin('tb_training_schedule','tb_training_schedule.id','=','tb_training_actual.id_training_schedule')
              ->leftjoin('tb_training_list','tb_training_list.id','=','tb_training_schedule.id_training')
              ->leftjoin('tb_skill_type','tb_skill_type.id','=','tb_training_list.id_type')
              ->where('tb_training_actual.is_delete','0')
              //->where('tb_training_actual.in_class','1')
              ->orderby('tb_training_actual.id','desc')->get(['tb_training_actual.*','tb_training_list.training_name','tb_skill_type.skill_type']);
            $tb_training_schedule=DB::connection('training')->table('tb_training_schedule')
            ->leftjoin('tb_training_list','tb_training_list.id','=','tb_training_schedule.id_training')
            ->leftjoin('tb_skill_type','tb_skill_type.id','=','tb_training_list.id_type')
            ->where('tb_training_schedule.is_delete','0')
            ->whereNotNull('tb_training_schedule.nara_sumber')
            ->orderby('tb_training_schedule.id','desc')
            ->get(['tb_training_schedule.*','tb_training_list.training_name','tb_skill_type.skill_type']);
      
            return view('page/training/training_inclass',['tb_training_actual'=>$tb_training_actual,'tb_training_schedule'=>$tb_training_schedule,'site'=>$this->site,'menu'=>'training_actual','juduls'=>'Actual Training']);

        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }
    function training_personal(){
        if (request()->user()->hasRole('root')||request()->user()->hasRole('training')){
            $tb_training_personal=DB::connection('training')->table('tb_training_actual')
            ->leftjoin('tb_training_schedule','tb_training_schedule.id','=','tb_training_actual.id_training_schedule')
            ->leftjoin('tb_training_list','tb_training_list.id','=','tb_training_schedule.id_training')
            ->leftjoin('tb_skill_type','tb_skill_type.id','=','tb_training_list.id_type')
            ->leftjoin('tb_training_participant','tb_training_participant.id_training_actual','=','tb_training_actual.id')
            ->where('tb_training_actual.is_delete','0')
            ->where('tb_training_actual.in_class','0')
            ->orderby('tb_training_actual.id','desc')->get(['tb_training_actual.*','tb_training_list.training_name','tb_skill_type.skill_type','tb_training_participant.nama_karyawan','tb_training_participant.NIK','tb_training_participant.department','tb_training_participant.jabatan']);
            
            $tb_training_schedule=DB::connection('training')->table('tb_training_schedule')
            ->leftjoin('tb_training_list','tb_training_list.id','=','tb_training_schedule.id_training')
            ->leftjoin('tb_skill_type','tb_skill_type.id','=','tb_training_list.id_type')
            ->where('tb_training_schedule.is_delete','0')
            ->orderby('tb_training_schedule.id','desc')->get(['tb_training_schedule.*','tb_training_list.training_name','tb_skill_type.skill_type']);
            return view('page/training/training_personal',['tb_training_personal'=>$tb_training_personal,'tb_training_schedule'=>$tb_training_schedule,'site'=>$this->site,'menu'=>'training_actual','juduls'=>'Actual Training']);
        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }
    function training_actual_participant($id){
        $tb_training_actual=DB::table('tb_training_actual')->where('id',$id)->get();
        foreach($tb_training_actual as $dt){
            $id_training_schedule=$dt->id_training_schedule;
            $in_class=$dt->in_class;
        }
        if (request()->user()->hasRole('root')||request()->user()->hasRole('training')){
            $tb_employee=DB::table('tb_training_invitation')->orderby('nama_karyawan','asc')->get();
            $tb_training_actual=DB::table('tb_training_actual')
            ->leftjoin('tb_training_schedule','tb_training_schedule.id','=','tb_training_actual.id_training_schedule')
            ->leftjoin('tb_training_list','tb_training_list.id','=','tb_training_schedule.id_training')
            ->leftjoin('tb_skill_type','tb_skill_type.id','=','tb_training_list.id_type')
            ->where('tb_training_actual.id',$id)
            ->orderby('tb_training_actual.id','desc')->get(['tb_training_actual.*','tb_training_list.training_name','tb_skill_type.skill_type']);
            $tb_training_participant=DB::table('tb_training_participant')->where('id_training_actual',$id)->orderby('id','desc')->get();

            $tb_related_document=DB::table('tb_related_document')
            ->leftjoin('tb_training_document','tb_training_document.id','=','tb_related_document.id_document')
            ->leftjoin('tb_training_schedule','tb_training_schedule.id','=','tb_related_document.id_training_schedule')
            ->leftjoin('tb_training_actual','tb_training_actual.id_training_schedule','=','tb_training_schedule.id')
            ->where('tb_training_actual.id',$id)->orderby('id','desc')->get(['tb_related_document.*','tb_training_document.id as id_doc','tb_training_document.document_name','tb_training_document.file_name']);

            $tb_training_test=DB::table('tb_training_test')->orderby('test_name','asc')->get();
            $tb_related_test=DB::table('tb_related_test')
            ->leftjoin('tb_training_test','tb_training_test.id','=','tb_related_test.id_test')
            ->where('id_training_schedule',$id_training_schedule)->get(['tb_related_test.*','tb_training_test.test_name','tb_training_test.minutes','tb_training_test.passing_grade']);

            return view('page/training/training_actual_participant',['tb_training_participant'=>$tb_training_participant,'tb_training_actual'=>$tb_training_actual,'tb_employee'=>$tb_employee,'tb_training_test'=>$tb_training_test,'tb_related_document'=>$tb_related_document,'tb_related_test'=>$tb_related_test,'id_training'=>$id,'in_class'=>$in_class,'site'=>$this->site,'menu'=>'training_activity','juduls'=>'Training Schedule']);
        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }
    function training_document($id_doc){
        $tb_training_document=DB::table('tb_training_document')->where('doc_level','0')->orderby('id','desc')->get();
        $related_file=DB::table('tb_training_document')->where('id',$id_doc)->get();
        $file_name='';
        $document_name='';
        foreach($related_file as $dt){
            $file_name=$dt->file_name;
            $document_name=$dt->document_name;
        }
        if (request()->user()->hasRole('root')||request()->user()->hasRole('training')){
            return view('page/training/training_document',['tb_training_document'=>$tb_training_document,'id_doc'=>$id_doc,'file_name'=>$file_name,'document_name'=>$document_name,'menu'=>'training_tools','juduls'=>'Training Documents']);
        //}elseif (request()->user()->hasRole('ess')){
            //return view('page/training/document',['tb_training_document'=>$tb_training_document,'site'=>$this->site,'menu'=>'training_tools','juduls'=>'Training Documents']);
        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }
    function training_examination(){
        if (request()->user()->hasRole('root')||request()->user()->hasRole('training')){
            $tb_training_test=DB::table('tb_training_test')->orderby('id','desc')->get();
            return view('page/training/training_test',['tb_training_test'=>$tb_training_test,'site'=>$this->site,'menu'=>'training_tools','juduls'=>'Training Documents']);
        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }
    function training_question($id){
        if (request()->user()->hasRole('root')||request()->user()->hasRole('training')){
            $tb_training_test=DB::table('tb_training_test')->where('id',$id)->orderby('id','desc')->get();
            $tb_question=DB::table('tb_question')->where('id_training_test',$id)->orderby('index_question','desc')->get();
            foreach($tb_training_test as $dt){
                $test_name=$dt->test_name;
            }
            return view('page/training/training_question',['tb_training_test'=>$tb_training_test,'test_name'=>$test_name,'tb_question'=>$tb_question,'id_test'=>$id,'site'=>$this->site,'menu'=>'training_tools','juduls'=>'Training Documents']);
        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }
    function training_question_show($id){
        if (request()->user()->hasRole('root')||request()->user()->hasRole('training')){
            return "Masuk";
            $tb_training_test=DB::connection('training')->table('tb_training_test')->where('id',$id)->orderby('id','desc')->get();
            $tb_question=DB::connection('training')->table('tb_question')->where('id_training_test',$id)->orderby('index_question','desc')->get();
            foreach($tb_training_test as $dt){
                $test_name=$dt->test_name;
            }
            return view('page/training/training_question',['tb_training_test'=>$tb_training_test,'test_name'=>$test_name,'tb_question'=>$tb_question,'id_test'=>$id,'site'=>$this->site,'menu'=>'training_tools','juduls'=>'Training Documents']);
        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }
    function training_monitor_free($id_training_actual){
        $tb_training_actual=DB::connection('training')->table('tb_training_actual')->where('id',$id_training_actual)->get();
        foreach($tb_training_actual as $dt){
            $id_training_schedule=$dt->id_training_schedule;
        }
        if (request()->user()->hasRole('root')||request()->user()->hasRole('training')){
            $tb_question=DB::connection('training')->table('tb_related_test')
            ->leftjoin('tb_question','tb_question.id_training_test','=','tb_related_test.id_test')
            ->where('tb_related_test.id_training_schedule',$id_training_schedule)->orderby('tb_question.index_question','asc')
            ->get(['tb_question.*']);

            return view('page/training/training_monitor',['tb_question'=>$tb_question,'id_training'=>$id_training_actual,'type'=>'Free','site'=>$this->site,'menu'=>'training_activity','juduls'=>'Training Schedule']);
        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }
    function training_monitor_post($id_training_actual){
        $tb_training_actual=DB::connection('training')->table('tb_training_actual')->where('id',$id_training_actual)->get();
        foreach($tb_training_actual as $dt){
            $id_training_schedule=$dt->id_training_schedule;
        }
        if (request()->user()->hasRole('root')||request()->user()->hasRole('training')){
            $tb_question=DB::connection('training')->table('tb_related_test')
            ->leftjoin('tb_question','tb_question.id_training_test','=','tb_related_test.id_test')
            ->where('tb_related_test.id_training_schedule',$id_training_schedule)->orderby('tb_question.index_question','asc')
            ->get(['tb_question.*']);

            return view('page/training/training_monitor',['tb_question'=>$tb_question,'id_training'=>$id_training_actual,'type'=>'Post','site'=>$this->site,'menu'=>'training_activity','juduls'=>'Training Schedule']);
        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }
    function training_monitor_update(request $data){
        $konten='';
        $tabel=DB::connection('training')->table('tb_training_participant')->where('id_training_actual',$data->idtraining)->get();
        $no=0;
        foreach($tabel as $dt){
            $no++;
            $konten.="<tr>";
            $konten.="<td>".$no."</td>";
            $konten.="<td>".$dt->NIK."</td>";
            $konten.="<td>".$dt->nama_karyawan."</td>";
            if($data->type=='Free'){
                $tabel2=DB::connection('training')->table('tb_free_test')->where('id_participant',$dt->id)->get();
                $konten.="<td style='text-align:center;'>";
                foreach($tabel2 as $dt2){
                    if($dt2->answer_code==$dt2->answer_actual && $dt2->answer_late == 0)
                    { 
                        $konten.="<b class='badge bg-green'>".$dt2->answer_actual."</b>";
                    }else if ($dt2->answer_late == 1){ 
                        $konten.="<b class='badge bg-yellow'>".$dt2->answer_actual."</b>";
                    }else{
                        $konten.="<b class='badge bg-red'>".$dt2->answer_actual."</b>";
                    }
                    if($dt2->answer_actual == ""){
                        $konten.="<b class='badge bg-red'>Not Answered</b>";
                    }
                }
                $konten.="</td>";

                $konten.="<td>".$dt->free_test."</td>";
            }
            else{
                $tabel2=DB::connection('training')->table('tb_post_test')->where('id_participant',$dt->id)->get();
                $konten.="<td style='text-align:center;'>";

                foreach($tabel2 as $dt2){
                    if($dt2->answer_code==$dt2->answer_actual && $dt2->answer_late == 0)
                    { 
                        $konten.="<b class='badge bg-green'>".$dt2->answer_actual."</b>";
                    }else if ($dt2->answer_late == 1 && $dt2->answer_code==$dt2->answer_actual ){ 
                        $konten.="<b class='badge bg-yellow'>".$dt2->answer_actual."</b>";
                    }else{
                        $konten.="<b class='badge bg-red'>".$dt2->answer_actual."</b>";
                    }
                    if($dt2->answer_actual == ""){
                        $konten.="<b class='badge bg-red'>Not Answered</b>";
                    }
                }
                $konten.="</td>";

                $konten.="<td>".$dt->post_test."</td>";
            }
            $konten.="</tr>";
        }

        return $konten;
    }

    function simpan_list(request $data){
        $admin=Auth::user()->name;
        $hasil="There is no change";
        if($data->idcomponent==''){
            $tb_training_list=DB::table('tb_training_list')->where('training_name',$data->trainingname)->count();
            if($tb_training_list>0)$hasil="Failed, Training Name already Exixts";
            else{
                $add=DB::table('tb_training_list')->insert([
                    'training_name'=>$data->trainingname,
                    'id_type'=>$data->skilltype,
                    'category'=>$data->categorytraining,
                    'level_participant'=>$data->levelparticipant,
                    'admin'=>$admin
                ]);
                if($add)$hasil="Sukses";
            }
        }else{
            $edit=DB::table('tb_training_list')->where('id',$data->idcomponent)->update([
                'training_name'=>$data->trainingname,
                'id_type'=>$data->skilltype,
                'category'=>$data->categorytraining,
                'level_participant'=>$data->levelparticipant,
            'admin'=>$admin
            ]);
            if($edit)$hasil="Sukses";
        }
        return $hasil;
        
    }
    function simpan_plan(request $data){
        $now=date('Y-m-d H:i:s');
        $admin=Auth::user()->name;
        $hasil="There is no change";
        $periode=date('Y-m',strtotime($data->tanggal));
        if($data->idcomponent==''){
            $tb_training_schedule=DB::table('tb_training_schedule')->where('id_training',$data->idtraining)->where('tanggal',$data->tanggal)->count();
            if($tb_training_schedule>0)$hasil="Failed, Training Schedule already Exixts";
            else{
                $add=DB::table('tb_training_schedule')->insert([
                    'id_training'=>$data->idtraining,
                    'nara_sumber'=>$data->narasumber,
                    'tanggal'=>$data->tanggal,
                    'start'=>$data->start,
                    'finish'=>$data->finish,
                    'home_line'=>$data->homeline,
                    'department'=>$data->department,
                    'draft_qty'=>$data->draftqty,
                    'periode'=>$periode,
                    'week_number'=>$data->weeknumber,
                    'admin'=>$admin,
                    'is_delete'=>'0',
                    'created_at'=>$now,
                ]);
                if($add)$hasil="Sukses";
            }
        }else{
            $edit=DB::table('tb_training_schedule')->where('id',$data->idcomponent)->update([
                'id_training'=>$data->idtraining,
                'nara_sumber'=>$data->narasumber,
                'tanggal'=>$data->tanggal,
                'start'=>$data->start,
                'finish'=>$data->finish,
                'home_line'=>$data->homeline,
                'department'=>$data->department,
                'draft_qty'=>$data->draftqty,
                'periode'=>$periode,
                'admin'=>$admin
            ]);
            if($edit)$hasil="Sukses";
        }
        return $hasil;
        
    }
    function simpan_plan_participant(request $data){
        $admin=Auth::user()->name;
        $tb_employee=DB::table('tb_employees')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->where('tb_employees.id',$data->idemployee)
        ->get();
        foreach($tb_employee as $dt){
            $tb_training_invitation=DB::table('tb_training_invitation')->where('id_employee',$data->idemployee)->where('id_training_schedule',$data->idtraining)->count();
            if($tb_training_invitation>0)$hasil="Failed, Employee already Exixts";
            else{
                $add=DB::table('tb_training_invitation')->insert([
                    'id_training_schedule'=>$data->idtraining,
                    'id_employee'=>$data->idemployee,
                    'NIK'=>$dt->NIK,
                    'nama_karyawan'=>$dt->employee_name,
                    'department'=>$dt->dept_code,
                    'jabatan'=>$dt->position_name,
                    'admin'=>$admin
                ]);
                if($add)$hasil="Sukses";
            }
        }
        $tb_training_invitation=DB::table('tb_training_invitation')->where('id_training_schedule',$data->idtraining)->orderby('id','desc')->get();        
        $no=0;
        $hasil="";
        foreach($tb_training_invitation as $dt){
            $no++;
            $hasil.="<tr>";
            $hasil.="<td>".$no."</td>";
            $hasil.="<td>".$dt->NIK."</td>";
            $hasil.="<td>".$dt->nama_karyawan."</td>";
            $hasil.="<td>".$dt->department."</td>";
            $hasil.="<td>".$dt->jabatan;
            $hasil.="<div class='pull-right'>";
            $hasil.="<button title='Delete' type='button' class='delete-modal btn btn-danger btn-xs' data-delid='".$dt->id."' data-delname='".$dt->nama_karyawan."'><i class='fa fa-trash'></i></button>";
            $hasil.="</div>";
            $hasil.="</td>";
            $hasil.="</tr>";
        }
        return $hasil;
        
    }
    function simpan_actual(request $data){
        $admin=Auth::user()->name;
        $hasil="There is no change";
        if($data->idcomponent==''){
            $tb_training_schedule=DB::connection('training')->table('tb_training_actual')->where('id_training_schedule',$data->idtraining)->where('tanggal_aktual',$data->tanggal)->where('in_class','1')->where('is_delete','0')->count();
            if($tb_training_schedule>0)$hasil="Failed, Training Schedule already Exixts";
            else{
                $add=DB::connection('training')->table('tb_training_actual')->insert([
                    'id_training_schedule'=>$data->idtraining,
                    'nara_sumber'=>$data->narasumber,
                    'tanggal_aktual'=>$data->tanggal,
                    'start_aktual'=>$data->start,
                    'finish_aktual'=>$data->finish,
                    'admin'=>$admin
                ]);
                if($add)$hasil="Sukses";
            }
        }else{
            $edit=DB::connection('training')->table('tb_training_actual')->where('id',$data->idcomponent)->update([
                'id_training_schedule'=>$data->idtraining,
                'nara_sumber'=>$data->narasumber,
                'tanggal_aktual'=>$data->tanggal,
                'start_aktual'=>$data->start,
                'finish_aktual'=>$data->finish,
                'admin'=>$admin
            ]);
            if($edit)$hasil="Sukses";
        }
        return $hasil;
        
    }
    function simpan_actual_participant(request $data){
        $tb_training_actual=DB::connection('training')->table('tb_training_actual')->where('id',$data->idtraining)->get();
        foreach($tb_training_actual as $dt){
            $id_training_schedule=$dt->id_training_schedule;
        }
        $hasil="No Action";
        $admin=Auth::user()->name;
        $jam=date('Y-m-d H:i:s');
        $tb_employee=DB::connection('training')->table('tb_training_invitation')
        ->where('tb_training_invitation.id_employee',$data->idemployee)
        ->where('id_training_schedule',$id_training_schedule)
        ->get();
        foreach($tb_employee as $dt){
            $id_training_invitation=$dt->id;
            //$hasil="Masuk";
            $tb_training_participant=DB::connection('training')->table('tb_training_participant')->where('id_employee',$data->idemployee)->where('id_training_actual',$data->idtraining)->count();
            if($tb_training_participant>0)$hasil="Failed, Employee already Exixts";
            else{
                $add=DB::connection('training')->table('tb_training_participant')->insert([
                    'id_training_actual'=>$data->idtraining,
                    'id_training_invitation'=>$id_training_invitation,
                    'id_employee'=>$data->idemployee,
                    'NIK'=>$dt->NIK,
                    'nama_karyawan'=>$dt->nama_karyawan,
                    'department'=>$dt->department,
                    'jabatan'=>$dt->jabatan,
                    'admin'=>$admin,
                    'created_at'=>$jam,
                ]);
                if($add)$hasil="Sukses";
            }
        }

        $tb_related_test=DB::connection('training')->table('tb_training_participant')
        ->leftjoin('tb_training_actual','tb_training_actual.id','=','tb_training_participant.id_training_actual')
        ->leftjoin('tb_related_test','tb_related_test.id_training_schedule','=','tb_training_actual.id_training_schedule')
        ->leftjoin('tb_question','tb_question.id_training_test','=','tb_related_test.id_test')
        ->where('tb_training_participant.id_training_actual',$data->idtraining)
        ->where('tb_training_participant.admin',$admin)
        ->where('tb_training_participant.created_at',$jam)
        ->get(['tb_question.*','tb_training_participant.id as id_participant']);
        foreach($tb_related_test as $dt2){
            $add3=DB::connection('training')->table('tb_free_test')->insert([
                'id_participant'=>$dt2->id_participant,
                'id_question'=>$dt2->id,
                'answer_code'=>$dt2->answer_code,
                'admin'=>$admin
            ]);
            $add4=DB::connection('training')->table('tb_post_test')->insert([
                'id_participant'=>$dt2->id_participant,
                'id_question'=>$dt2->id,
                'answer_code'=>$dt2->answer_code,
                'admin'=>$admin
            ]);
        }

        $tb_training_participant=DB::connection('training')->table('tb_training_participant')->where('id_training_actual',$data->idtraining)->orderby('id','desc')->get();        
        $no=0;
        foreach($tb_training_participant as $dt){
            $no++;
            $hasil.="<tr>";
            $hasil.="<td>".$no."</td>";
            $hasil.="<td>".$dt->NIK."</td>";
            $hasil.="<td>".$dt->nama_karyawan."</td>";
            $hasil.="<td>".$dt->department."</td>";
            $hasil.="<td>".$dt->jabatan;
            $hasil.="<div class='pull-right'>";
            $hasil.="<button title='Delete' type='button' class='delete-modal btn btn-danger btn-xs' data-delid='".$dt->id."' data-delname='".$dt->nama_karyawan."'><i class='fa fa-trash'></i></button>";
            $hasil.="</div>";
            $hasil.="</td>";
            $hasil.="</tr>";
        }
        return $hasil;
        
    }
    function document_upload(request $data){
        $admin=Auth::user()->name;
        $namaFile='';
        if($data->document_name=='')return redirect()->back()->with(['success'=>'Document Name can not be null']);
        if($data->id_document==''){
            $file = $data->file('training_doc');
            if($file=='')return redirect()->back()->with(['success'=>'File can not be null']);
            $tb_training_document=DB::connection('training')->table('tb_training_document')->where('file_name',$file)->where('doc_level','1')->count();
            if($tb_training_document>0)return redirect()->back()->with(['success'=>'File already exists']);

            $namaFile = $_FILES['training_doc']['name'];
            $namaSementara = $_FILES['training_doc']['tmp_name'];
            $dirUpload = "laravel/storage/app/public/";

            $terupload = move_uploaded_file($namaSementara, $dirUpload.$namaFile);
            if($terupload){
                $add=DB::connection('training')->table('tb_training_document')->insert([
                    'document_name'=>$data->document_name,
                    'file_name'=>$namaFile,
                    'doc_level'=>'1',
                    'admin'=>$admin,
                ]);
                if($add)return redirect()->back()->with(['success'=>'Upload Sukses']);
            }
        }else{
            $update=DB::connection('training')->table('tb_training_document')->where('id',$data->id_document)->update([
                'document_name'=>$data->document_name,
                'admin'=>$admin,
            ]);
            if($update)return redirect()->back()->with(['success'=>'Update Sukses']);
        }
        return redirect()->back()->with(['success'=>'There is no change']);
    }
    function document_download($id){
        $tb_training_document=DB::connection('training')->table('tb_training_document')->where('id',$id)->get();
        foreach($tb_training_document as $dt){
            $dirUpload = "laravel/storage/app/public/";
            $file_path = $dirUpload.$dt->file_name; 
            if(file_exists($file_path)){ 
                return Storage::download('public/'.$dt->file_name);
            }else{
                return redirect()->back()->with(['success'=>'File is not available']);
            }
        }
    }
    function simpan_supporting(request $data){
        $hasil="Kosong";
        $admin=Auth::user()->name;
        $tb_related_document=DB::table('tb_related_document')->where('id_training_schedule',$data->idtraining)->where('id_document',$data->iddocument)->count();
        if($tb_related_document>0)$hasil="Failed, DDocument already Exixts";
        else{
            $add=DB::table('tb_related_document')->insert([
                'id_training_schedule'=>$data->idtraining,
                'id_document'=>$data->iddocument,
                'admin'=>$admin
            ]);
        }

        $tb_related_document=DB::table('tb_related_document')
        ->leftjoin('tb_training_document','tb_training_document.id','=','tb_related_document.id_document')
        ->where('id_training_schedule',$data->idtraining,)->orderby('id','desc')->get(['tb_related_document.*','tb_training_document.document_name','tb_training_document.file_name']);

        $no=0;
        $hasil="";
        foreach($tb_related_document as $dt){
            $no++;
            $hasil.="<tr>";
            $hasil.="<td>".$no."</td>";
            $hasil.="<td>".$dt->file_name;
            $hasil.="<div class='pull-right'>";
            $hasil.="<button title='Delete' type='button' class='deletesupport-modal btn btn-danger btn-xs' data-idsupport='".$dt->id."' data-supportnamefilename='".$dt->file_name."'><i class='fa fa-trash'></i></button>";
            $hasil.="</div>";
            $hasil.="</td>";
            $hasil.="</tr>";
        }
        return $hasil;
        
    }
    function simpan_examination(request $data){
        $hasil="No Action";
        $admin=Auth::user()->name;
        if($data->testname=='')$hasil="Test Name can not be null";
        elseif($data->minutes=='')$hasil="Minutes can not be null";
        elseif($data->passinggrade=='')$hasil="Passing Grade can not be null";
        else{
            if($data->id==''){
                $cek=DB::table('tb_training_test')->where('test_name',$data->test_name)->count();
                if($cek>0)$hasil="Test Name already exists";
                else{
                    $simpan=DB::table('tb_training_test')->insert([
                        'test_name'=>$data->testname,
                        'minutes'=>$data->minutes,
                        'passing_grade'=>$data->passinggrade,
                        'admin'=>$admin
                    ]);
                    if($simpan)$hasil="Sukses";
                }
            }else{
                $simpan=DB::table('tb_training_test')->where('id',$data->id)->update([
                    'test_name'=>$data->testname,
                    'minutes'=>$data->minutes,
                    'passing_grade'=>$data->passinggrade,
                    'admin'=>$admin
                ]);
                if($simpan)$hasil="Sukses";
            }

        }
        return $hasil;
    }
    function simpan_question(request $data){
        $hasil="No Action";
        $admin=Auth::user()->name;
        if($data->question=='')$hasil="Question can not be null";
        elseif($data->optiona=='')$hasil="Option A can not be null";
        elseif($data->answercode=='')$hasil="Answer Code can not be null";
        else{
            if($data->id==''){
                $cek=DB::table('tb_question')->where('question',$data->question)->where('id_training_test',$data->idtest)->count();
                $cekindex=DB::table('tb_question')->where('index_question',$data->indexquestion)->count();
                
                if($cek>0 ) $hasil="Question  already exists";
                else{
                    $simpan=DB::table('tb_question')->insert([
                        'index_question'=>$data->indexquestion,
                        'id_training_test'=>$data->idtest,
                        'question'=>$data->question,
                        'option_a'=>$data->optiona,
                        'option_b'=>$data->optionb,
                        'option_c'=>$data->optionc,
                        'option_d'=>$data->optiond,
                        'answer_code'=>$data->answercode,
                        'admin'=>$admin
                    ]);
                    if($simpan)$hasil="Sukses";
                }
            }else{
                $tb_question=DB::table('tb_question')->where('id',$data->id)->get();
                foreach($tb_question as $dt){
                    $index_lama=$dt->index_question;
                    $update=DB::table('tb_question')->where('index_question',$data->indexquestion)->update([
                        'index_question'=>$index_lama,
                        'admin'=>$admin
                    ]);
                }
                $simpan=DB::table('tb_question')->where('id',$data->id)->update([
                    'index_question'=>$data->indexquestion,
                    'id_training_test'=>$data->idtest,
                    'question'=>$data->question,
                    'option_a'=>$data->optiona,
                    'option_b'=>$data->optionb,
                    'option_c'=>$data->optionc,
                    'option_d'=>$data->optiond,
                    'answer_code'=>$data->answercode,
                    'admin'=>$admin
            ]);
                if($simpan)$hasil="Sukses";
            }

        }
        return $hasil;
    }
    function simpan_supporting_test(request $data){
        $hasil="Kosong";
        $admin=Auth::user()->name;
        $tb_related_test=DB::table('tb_related_test')->where('id_training_schedule',$data->idtraining)->where('id_test',$data->idtest)->count();
        if($tb_related_test>0)$hasil="Failed, Test already Exixts";
        else{
            $add=DB::table('tb_related_test')->insert([
                'id_training_schedule'=>$data->idtraining,
                'id_test'=>$data->idtest,
                'admin'=>$admin
            ]);
            if($add)$hasil="Sukses";
        }
        return $hasil;
        
    }
    function share_free_test(request $data){
        $hasil="Already Start";
        $admin=Auth::user()->name;
        $tb_related_test=DB::table('tb_training_participant')
        ->leftjoin('tb_training_actual','tb_training_actual.id','=','tb_training_participant.id_training_actual')
        ->leftjoin('tb_related_test','tb_related_test.id_training_schedule','=','tb_training_actual.id_training_schedule')
        ->leftjoin('tb_question','tb_question.id_training_test','=','tb_related_test.id_test')
        ->where('tb_training_participant.id_training_actual',$data->idtraining)->get(['tb_question.*','tb_training_participant.id as id_participant']);
        //$hasil=$tb_related_test;
        foreach($tb_related_test as $dt){
            $cek=DB::table('tb_free_test')->where('id_participant',$dt->id_participant)->where('id_question',$dt->id)->count();
            $no=0;
            if($cek==0){
                $add=DB::table('tb_free_test')->insert([
                    'id_participant'=>$dt->id_participant,
                    'id_question'=>$dt->id,
                    'answer_code'=>$dt->answer_code,
                    'admin'=>$admin
                ]);
                if($add)$hasil="Sukses";
            }else{
                $reset=DB::table('tb_free_test')->where('id_participant',$dt->id_participant)->where('id_question',$dt->id)->update([
                    'answer_actual'=>NULL,
                    'answer_status'=>0,
                    'admin'=>$admin
                ]);
                if($reset)$hasil="Sukses";
            }
            $update=DB::table('tb_training_participant')->where('id',$dt->id_participant)->update([
                'free_test'=>NULL,
                'progress'=>NULL,
                'grade_status'=>NULL
            ]);
        }
        return $hasil;
    }
    function share_post_test(request $data){
        $hasil="There is no change";
        $admin=Auth::user()->name;
        $tb_related_test=DB::connection('training')->table('tb_training_participant')
        ->leftjoin('tb_training_actual','tb_training_actual.id','=','tb_training_participant.id_training_actual')
        ->leftjoin('tb_related_test','tb_related_test.id_training_schedule','=','tb_training_actual.id_training_schedule')
        ->leftjoin('tb_question','tb_question.id_training_test','=','tb_related_test.id_test')
        ->where('tb_training_participant.id_training_actual',$data->idtraining)->get(['tb_question.*','tb_training_participant.id as id_participant']);
        foreach($tb_related_test as $dt){
            $cek=DB::connection('training')->table('tb_post_test')->where('id_participant',$dt->id_participant)->where('id_question',$dt->id)->count();
            $no=0;
            if($cek==0){
                $add=DB::connection('training')->table('tb_post_test')->insert([
                    'id_participant'=>$dt->id_participant,
                    'id_question'=>$dt->id,
                    'answer_code'=>$dt->answer_code,
                    'admin'=>$admin
                ]);
                if($add)$hasil="Sukses";
            }else{
                $reset=DB::connection('training')->table('tb_post_test')->where('id_participant',$dt->id_participant)->where('id_question',$dt->id)->update([
                    'answer_actual'=>NULL,
                    'answer_status'=>0,
                    'admin'=>$admin
                ]);
                if($reset)$hasil="Sukses";
            }
            $update=DB::connection('training')->table('tb_training_participant')->where('id',$dt->id_participant)->update([
                'post_test'=>NULL,
                'progress'=>NULL,
                'grade_status'=>NULL
            ]);
        }
        return $hasil;
    }
    function refresh_periode($periode){
        if($periode==0)$periode=date('Y-m');
        $tahun=date('Y',strtotime($periode.'-01'));
        if (request()->user()->hasRole('root')||request()->user()->hasRole('training')){
            $tb_training_schedule=DB::connection('training')->table('tb_training_schedule')
            ->where('tb_training_schedule.is_delete','0')
            ->where('tb_training_schedule.periode',$periode)
            //->where('id','904')
            ->get();
            foreach($tb_training_schedule as $dt){
                $tb_inv=DB::connection('training')->table('tb_training_invitation')
                ->select('id_employee','id_training_schedule')
                ->groupby('id_employee','id_training_schedule')
                ->where('id_training_schedule',$dt->id)
                ->get();
                $plan=0;
                foreach($tb_inv as $dt2){
                    $plan++;
                    $tb_act=DB::connection('training')->table('tb_training_participant')
                    ->leftjoin('tb_training_invitation','tb_training_invitation.id','=','tb_training_participant.id_training_invitation')
                    ->select('tb_training_participant.id_employee','id_training_invitation')
                    ->groupby('tb_training_participant.id_employee','id_training_invitation')
                    ->where('id_training_schedule',$dt2->id_training_schedule)
                    ->get();
                    $actual=0;
                    foreach($tb_act as $dt3){
                        $actual++;
                    }
                    $update=DB::connection('training')->table('tb_training_schedule')->where('id',$dt->id)->update(['plan_qty'=>$plan,'actual_qty'=>$actual]);
                }
            }
        }
        return redirect()->back()->with(['success'=>'Success Refresh']);
    }

    function delete_list($id){
        $delete=DB::table('tb_training_list')->where('id',$id)->update(['is_delete'=>'1']);;
        if($delete)return redirect()->back()->with(['success'=>'Deleted']);
    }
    function delete_plan($id){
        $delete=DB::connection('training')->table('tb_training_schedule')->where('id',$id)->update(['is_delete'=>'1']);
        if($delete)return redirect()->back()->with(['success'=>'Deleted']);
    }
    function delete_plan_participant(request $data){
        $delete=DB::table('tb_training_invitation')->where('id',$data->id)->delete();
        $tb_training_invitation=DB::table('tb_training_invitation')->where('id_training_schedule',$data->idtraining)->orderby('id','desc')->get();        
        $no=0;
        $hasil="";
        foreach($tb_training_invitation as $dt){
            $no++;
            $hasil.="<tr>";
            $hasil.="<td>".$no."</td>";
            $hasil.="<td>".$dt->NIK."</td>";
            $hasil.="<td>".$dt->nama_karyawan."</td>";
            $hasil.="<td>".$dt->department."</td>";
            $hasil.="<td>".$dt->jabatan;
            $hasil.="<div class='pull-right'>";
            $hasil.="<button title='Delete' type='button' class='delete-modal btn btn-danger btn-xs' data-delid='".$dt->id."' data-delname='".$dt->nama_karyawan."'><i class='fa fa-trash'></i></button>";
            $hasil.="</div>";
            $hasil.="</td>";
            $hasil.="</tr>";
        }
        return $hasil;
    }
    function delete_actual($id){
        $delete=DB::connection('training')->table('tb_training_actual')->where('id',$id)->update(['is_delete'=>'1']);
        if($delete)return redirect()->back()->with(['success'=>'Deleted']);
    }
    function delete_actual_participant(request $data){
        $delete=DB::connection('training')->table('tb_training_participant')->where('id',$data->id)->delete();
        $tb_training_participant=DB::connection('training')->table('tb_training_participant')->where('id_training_actual',$data->idtraining)->orderby('id','desc')->get();        
        $no=0;
        $hasil="";
        foreach($tb_training_participant as $dt){
            $no++;
            $hasil.="<tr>";
            $hasil.="<td>".$no."</td>";
            $hasil.="<td>".$dt->NIK."</td>";
            $hasil.="<td>".$dt->nama_karyawan."</td>";
            $hasil.="<td>".$dt->department."</td>";
            $hasil.="<td>".$dt->jabatan;
            $hasil.="<div class='pull-right'>";
            $hasil.="<button title='Delete' type='button' class='delete-modal btn btn-danger btn-xs' data-delid='".$dt->id."' data-delname='".$dt->nama_karyawan."'><i class='fa fa-trash'></i></button>";
            $hasil.="</div>";
            $hasil.="</td>";
            $hasil.="</tr>";
        }
        return $hasil;
    }
    function delete_document(request $data){
        $tb_training_document=DB::table('tb_training_document')->where('id',$data->id)->get();
        foreach($tb_training_document as $dt){
            $dirUpload = "laravel/storage/app/public/";
            $file_path = $dirUpload.$dt->file_name; 
            if(file_exists($file_path)){ 
                unlink($file_path); 
            }
        }
        $delete=DB::table('tb_training_document')->where('id',$data->id)->delete();
        if($delete){
            $hasil='Sukses';
        }
        else $hasil='Gagal';
        return $hasil;
    }
    function delete_supporting(request $data){
        $delete=DB::table('tb_related_document')->where('id',$data->id)->delete();

        $tb_related_document=DB::table('tb_related_document')
        ->leftjoin('tb_training_document','tb_training_document.id','=','tb_related_document.id_document')
        ->where('id_training_schedule',$data->idtraining,)->orderby('id','desc')->get(['tb_related_document.*','tb_training_document.document_name','tb_training_document.file_name']);

        $no=0;
        $hasil="";
        foreach($tb_related_document as $dt){
            $no++;
            $hasil.="<tr>";
            $hasil.="<td>".$no."</td>";
            $hasil.="<td>".$dt->file_name;
            $hasil.="<div class='pull-right'>";
            $hasil.="<button title='Delete' type='button' class='deletesupport-modal btn btn-danger btn-xs' data-idsupport='".$dt->id."' data-supportnamefilename='".$dt->file_name."'><i class='fa fa-trash'></i></button>";
            $hasil.="</div>";
            $hasil.="</td>";
            $hasil.="</tr>";
        }
        return $hasil;
    }
    function delete_examination(request $data){
        $delete=DB::table('tb_training_test')->where('id',$data->id)->delete();
        if($delete){
            $hasil='Sukses';
        }
        else $hasil='Gagal';
        return $hasil;
    }
    function delete_question(request $data){
        $delete=DB::table('tb_question')->where('id',$data->id)->delete();
        if($delete){
            $hasil='Sukses';
        }
        else $hasil='Gagal';
        return $hasil;
    }
    function delete_supporting_test($id){
        $delete=DB::table('tb_related_test')->where('id',$id)->delete();
        if($delete)return redirect()->back()->with(['success'=>'Deleted']);
    }

    function update_participant(request $data){
        $tb_training_participant=DB::connection('training')->table('tb_training_participant')->where('id_training_actual',$data->idtraining)->get();
        $tb_training_actual=DB::connection('training')->table('tb_training_actual')->where('id',$data->idtraining)->get();
        foreach($tb_training_actual as $dt){
            $id_training_schedule=$dt->id_training_schedule;
        }
        $tb_training_invitation=DB::connection('training')->table('tb_training_invitation')
        ->where('id_training_schedule',$id_training_schedule);
        foreach($tb_training_participant as $dt){
            $tb_training_invitation=$tb_training_invitation->where('id_employee','<>',$dt->id_employee);
        }
        $tb_training_invitation=$tb_training_invitation->orderby('id','desc')->get();        
        $hasil="<option value=''></option>";
        foreach($tb_training_invitation as $dt2){
            $hasil.="<option value='".$dt2->id_employee."'>".$dt2->nama_karyawan." (".$dt2->department.")</option>";
        }
        return $hasil;
    }
    function setup_participant($id_training_invitation){
        $tb_training_schedule=DB::connection('training')->table('tb_training_invitation')->where('id',$id_training_invitation)->get();
        foreach($tb_training_schedule as $dt){
            $id_training_schedule=$dt->id_training_schedule;
        }
        $email=Auth::user()->email;
        $tb_employee=DB::connection('mysql')->table('tb_emails')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_emails.id_employee')
        ->where('email_address',$email)->get();
        foreach($tb_employee as $dt){
            $id_employee=$dt->id_employee;
        }
        $admin=Auth::user()->name;
        date_default_timezone_set("Asia/Jakarta");
        $tanggal=date('Y-m-d');
        $start=date('H:i:s');
        $finish=date('H:i:s',strtotime('+1 hours',strtotime($tanggal.' '.$start)));
        $time=date('Y-m-d H:i:s');

        $cek=DB::connection('training')->table('tb_training_actual')->where('id_training_schedule',$id_training_schedule)->where('in_class',0)->where('admin',$admin)->where('is_delete','0')->count();
        //return $cek;
        if($cek==0){
            $tb_training_schedule=DB::connection('training')->table('tb_training_schedule')->where('id',$id_training_schedule)->where('is_delete','0')->get();
            foreach($tb_training_schedule as $dt){
                $add=DB::connection('training')->table('tb_training_actual')->insert([
                    'id_training_schedule'=>$dt->id,
                    'nara_sumber'=>$dt->nara_sumber,
                    'tanggal_aktual'=>$tanggal,
                    'start_aktual'=>$start,
                    'finish_aktual'=>$finish,
                    'in_class'=>0,
                    'admin'=>$admin,
                    'created_at'=>$time,
                ]);
                if($add){
                    $tb_training_invitation=DB::connection('training')->table('tb_training_invitation')
                    ->where('tb_training_invitation.id',$id_training_invitation)
                    ->get();
                    $tb_training_actual=DB::connection('training')->table('tb_training_actual')->where('admin',$admin)->where('created_at',$time)->get();                    
                    foreach($tb_training_actual as $dt){
                        $id_training_actual=$dt->id;
                    }
                    foreach($tb_training_invitation as $dt){
                        $add2=DB::connection('training')->table('tb_training_participant')->insert([
                            'id_training_actual'=>$id_training_actual,
                            'id_employee'=>$dt->id_employee,
                            'NIK'=>$dt->NIK,
                            'nama_karyawan'=>$dt->nama_karyawan,
                            'department'=>$dt->department,
                            'jabatan'=>$dt->jabatan,
                            'admin'=>$admin,
                            'created_at'=>$time
                        ]);
                        if($add2){

                            $tb_related_test=DB::connection('training')->table('tb_training_participant')
                            ->leftjoin('tb_training_actual','tb_training_actual.id','=','tb_training_participant.id_training_actual')
                            ->leftjoin('tb_related_test','tb_related_test.id_training_schedule','=','tb_training_actual.id_training_schedule')
                            ->leftjoin('tb_question','tb_question.id_training_test','=','tb_related_test.id_test')
                            ->where('tb_training_participant.id_training_actual',$id_training_actual)->get(['tb_question.*','tb_training_participant.id as id_participant']);
                            $no=0;
                            foreach($tb_related_test as $dt2){
                                $add3=DB::connection('training')->table('tb_free_test')->insert([
                                    'id_participant'=>$dt2->id_participant,
                                    'id_question'=>$dt2->id,
                                    'answer_code'=>$dt2->answer_code,
                                    'admin'=>$admin
                                ]);
                                $add4=DB::connection('training')->table('tb_post_test')->insert([
                                    'id_participant'=>$dt2->id_participant,
                                    'id_question'=>$dt2->id,
                                    'answer_code'=>$dt2->answer_code,
                                    'admin'=>$admin
                                ]);
                                if($add3)$no++;
                                $id_participant=$dt2->id_participant;
                            }
                            if($no>0){
                                $update=DB::connection('training')->table('tb_training_participant')->where('id',$id_participant)->update([
                                    'free_test'=>0
                                ]);
                                return redirect('/FreeTest/'.$id_participant);
                            }
                                                
                        }else return "Failed Add Participant";
                    }
                }else return "Failed Add Actual & Participant";
            }
        }else{
            $tb_training_actual=DB::connection('training')->table('tb_training_actual')->where('id_training_schedule',$id_training_schedule)->where('admin',$admin)->where('is_delete','0')->get();
            foreach($tb_training_actual as $dt){
                return redirect('/Training/Actual/'.$dt->id.'/0');
                $tb_training_participant=DB::connection('training')->table('tb_training_participant')->where('id_training_actual',$dt->id)->where('id_employee',$id_employee)->get();
                foreach($tb_training_participant as $dt2){
                    $id_participant=$dt2->id;
                    return redirect('/FreeTest/'.$id_participant);
                }
            }
        }

    }
    function training_print($periode){
        $tb_training_list=DB::connection('training')->table('tb_training_schedule')
        ->leftjoin('tb_training_list','tb_training_list.id','=','tb_training_schedule.id_training')
        ->select('id_training','training_name','category','nara_sumber','department','level_participant','home_line',DB::raw('SUM(draft_qty) as draft_qty'))
        ->groupby('id_training','training_name','category','nara_sumber','department','level_participant','home_line')
        ->where('periode','like','%'.$periode.'%')
        ->orderby('category','desc')
        ->orderby('department','asc')
        ->get();
        //return $tb_training_list;
        if (request()->user()->hasRole('root')||request()->user()->hasRole('competence')){
            return view('page/training/training_plan_print',['periode'=>$periode,'tb_training_list'=>$tb_training_list]);
        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }

#region added by Al Kin Training Question
    public function GetTrainingQuestion (Request $request){
        $id = $request->id_training_header;
        $columns = array( 
            0 =>'index_question', 
            1 =>'question',
            2 =>'answer_code',
            
          );   
        if (request()->user()->hasRole('root')||request()->user()->hasRole('training')){
            $tb_training_test=DB::connection('training')->table('tb_training_test')->where('id',$id)->orderby('id','desc')->get();
        $tb_question=DB::connection('training')->table('tb_question')->where('id_training_test',$id)->orderby('index_question','asc');
        $totalData = $tb_question->count();
        $totalFiltered = $totalData; 
        $totalData = $tb_question->count();
        $totalFiltered = $totalData;  
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = ($request->input('order.0.column')==0 ? $columns[0] : $columns[$request->input('order.0.column')]);
        $dir = ($request->input('order.0.column')==0 ? 'desc' : $request->input('order.0.dir')) ; 

        if(empty($request->input('search.value')))
        {  
            $posts = $tb_question
            ->offset($start)
        ->limit($limit)
        ->orderBy($order,$dir)
        ->get();
        }else{
            $search = $request->input('search.value');  
            $posts =  $tb_question->offset($start)
            ->orWhere('tb_question.question','LIKE',"%$search%")
            ->limit($limit)
            ->orderBy($order,$dir)->get();
            $totalFiltered = $posts->count();  
        }
        $data = array();
        if(!empty($posts)){
            $no = $start ;
                foreach($posts as $post){
                  // laravel/Admin/Overtime/Draft/  -Link button ke draft ketika submenu realisation
                  // href='{{$tujuan}}{{$dt->id}}
                  // <?php if(isset($id_employee)&&($id_employee=='122'||$id_employee=='101'))echo " 
                  /* onclick="<?php echo "myFunction(".$no.");"?>" */
                  $no++; 
                  $id = "'".str_replace("=","-", Crypt::encryptString($post->id)).'_'.$no."'"  ;   
                  $button = '<button title="Edit" type="button" onclick="GetForm('.$post->id.','.$post->id_training_test.')" class="form btn btn-default btn-xs" data-idquestion="'.$post->id.'" ><i class="fa fa-edit"></i></button><button title="Delete" type="button" class="delete-modal btn btn-danger btn-xs" data-delid="'.$post->id.'"><i class="fa fa-trash"></i></button>';
                    $nestedData['action'] = $button ;  
                    $nestedData['no'] = $no ; 
                    $nestedData['id'] = $post->id ;   
                    $nestedData['index_question'] = $post->index_question ;   
                    $nestedData['question'] = $post->question ;   
                    $nestedData['answer_code'] = $post->answer_code ;   
                   
                    $data[] = $nestedData; 
        }}
        $json_data = array(
            "draw"            => intval($request->input('draw')),  
            "recordsTotal"    => intval($totalData),  
            "recordsFiltered" => intval($totalFiltered), 
            "data"            => $data   
            ); 
            echo json_encode($json_data);
            
        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }
    public function GetForm(Request $request){
        $id = $request->id;
        $id_training_test = $request->id_training_test;
        $tb_question=DB::connection('training')->table('tb_question')->where('id_training_test',$id_training_test)->orderby('index_question','desc');
        $no = 1;
        $count = $tb_question->count();
    if($id > 0){
        $tb_question=DB::connection('training')->table('tb_question')
        ->where('id',$id)
        ->where('id_training_test',$id_training_test)
        ->orderby('index_question','desc');
            foreach($tb_question->get() as $r){
                
                $data['no'] = $no;
                $data['id'] = $r->id;
                $data['id_training_test'] = $r->id_training_test;
                $data['index_question'] = $r->index_question;
                $data['question'] = $r->question;
                $data['option_a'] = $r->option_a;
                $data['option_b'] = $r->option_b;
                $data['option_c'] = $r->option_c;
                $data['option_d'] = $r->option_d;
                $data['answer_code'] = $r->answer_code;
                $index_question = $r->index_question;
                $no++;
            }
        }else{
            foreach($tb_question->get() as $r){
                $data['id_training_test'] = $r->id_training_test;
                $index_question = $r->index_question;
                $no++;
            }
                $data['no'] = $no ;
                $data['id_training_test'] = $id_training_test;
                $data['id'] = '';
                $data['index_question'] = $no;
                $data['question'] = '';
                $data['option_a'] = '';
                $data['option_b'] = '';
                $data['option_c'] = '';
                $data['option_d'] ='';
                $data['answer_code'] = '';
        }
    return view('/page/training/training_question_form',$data);
    }

    public function SaveQuestion(Request $request){
        $admin=Auth::user()->name;

        $validator = Validator::make($request->all(), [ 
            'index_question' => 'required',
            'question' => 'required',
            'answer' => 'required',
            'option_a' => 'required', 
            'option_b' => 'required',
            'option_c' => 'required',
            'option_d' => 'required'
          ]);  
          if ($validator->fails()) {  
            $cont = explode("^", $validator->messages()->first()) ; 
            dd($cont);
            $dt['process_status'] = 0 ;
            $dt['msg_process'] = $cont[1] ;
            $dt['field'] = $cont[0] ; 
          }else{
            $id = $request->id_question;
            
            $id_training_test = $request->id_training_test;
            $index_question = $request->index_question;
            $answer = $request->answer;
            $question = $request->question;

            $dom = new \DomDocument(); 
            $dom->loadHtml($question, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);    
            $images = $dom->getElementsByTagName('img');  
            foreach($images as $k => $img){ 
                $data_img = str_replace(" ", "+", $img->getAttribute('src')); 
                $extension = explode('/', mime_content_type($data_img))[1]; 
                $image_name = time().$k.'.'.$extension ;   
                $path = 'laravel/public/images/lampiran_soal/' . $image_name;
                Image::make(file_get_contents($data_img))->save($path); 
                $img->removeAttribute('src'); 
                $pathX ='/EMS/laravel/public/images/lampiran_soal/' . $image_name;   
                $img->setAttribute('src', $pathX); 
              }  
            $question = $dom->saveHTML(); 

            $option_a = $request->option_a ; 
          $dom_a = new \DomDocument(); 
          $dom_a->loadHtml($option_a, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);    
          $images_a = $dom_a->getElementsByTagName('img');   
          foreach($images_a as $k => $img){ 
              $data_img = str_replace(" ", "+", $img->getAttribute('src')); 
              $contains = Str::contains($data_img, 'base64');
              if($contains === true){
              $extension = explode('/', mime_content_type($data_img))[1]; 
              $image_name = time().$k.'.'.$extension ;   
              $path = 'laravel/public/images/lampiran_soal/jawaban/a/' . $image_name;
              Image::make(file_get_contents($data_img))->save($path); 
              $img->removeAttribute('src'); 
              $pathX ='/EMS/laravel/public/images/lampiran_soal/jawaban/a/' . $image_name;   
              $img->setAttribute('src', $pathX); 
              }
            }  
          $option_a = $dom_a->saveHTML(); 
            
          $option_b = $request->option_b ; 
          $dom_b = new \DomDocument(); 
          $dom_b->loadHtml($option_b, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);    
          $images_b = $dom_b->getElementsByTagName('img');   
          foreach($images_b as $k => $img){ 
              $data_img = str_replace(" ", "+", $img->getAttribute('src')); 
              $contains = Str::contains($data_img, 'base64');
              if($contains === true){
              $extension = explode('/', mime_content_type($data_img))[1]; 
              $image_name = time().$k.'.'.$extension ;   
              $path = 'laravel/public/images/lampiran_soal/jawaban/b/' . $image_name;
              Image::make(file_get_contents($data_img))->save($path); 
              $img->removeAttribute('src'); 
              $pathX ='/EMS/laravel/public/images/lampiran_soal/jawaban/b/' . $image_name;   
              $img->setAttribute('src', $pathX); 
              }
            }  
          $option_b = $dom_b->saveHTML();

          $option_c = $request->option_c ; 
          $dom_c = new \DomDocument(); 
          $dom_c->loadHtml($option_c, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);    
          $images_c = $dom_c->getElementsByTagName('img');   
          foreach($images_c as $k => $img){ 
              $data_img = str_replace(" ", "+", $img->getAttribute('src')); 
              $contains = Str::contains($data_img, 'base64');
              if($contains === true){
              $extension = explode('/', mime_content_type($data_img))[1]; 
              $image_name = time().$k.'.'.$extension ;   
              $path = 'laravel/public/images/lampiran_soal/jawaban/c/' . $image_name;
              Image::make(file_get_contents($data_img))->save($path); 
              $img->removeAttribute('src'); 
              $pathX ='/EMS/laravel/public/images/lampiran_soal/jawaban/c/' . $image_name;   
              $img->setAttribute('src', $pathX); 
              }
            }  
          $option_c = $dom_c->saveHTML();

          $option_d = $request->option_d ; 
          $dom_d = new \DomDocument(); 
          $dom_d->loadHtml($option_d, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);    
          $images_d = $dom_d->getElementsByTagName('img');   
          foreach($images_d as $k => $img){ 
              $data_img = str_replace(" ", "+", $img->getAttribute('src')); 
              $contains = Str::contains($data_img, 'base64');
              if($contains === true){
              $extension = explode('/', mime_content_type($data_img))[1]; 
              $image_name = time().$k.'.'.$extension ;   
              $path = 'laravel/public/images/lampiran_soal/jawaban/d/' . $image_name;
              Image::make(file_get_contents($data_img))->save($path); 
              $img->removeAttribute('src'); 
              $pathX ='/EMS/laravel/public/images/lampiran_soal/jawaban/d/' . $image_name;   
              $img->setAttribute('src', $pathX); 
              }
            }  
          $option_d = $dom_d->saveHTML();
            
            // $datas['id'] = $id;
        if($id == 0){
            $datas['id_training_test'] = $id_training_test;
            $datas['question'] = str_replace("tanda_kutip", "'", str_replace("tanda_kutip_dua", '"', str_replace("lebih_kecil", "<", str_replace("lebih_besar", ">", str_replace("jeung", "&", str_replace("pleus", "+", $question))))));
            $datas['index_question'] = $index_question;
            $datas['answer_code'] = $answer;
            $datas['option_a'] = $option_a;
            $datas['option_b'] = $option_b;
            $datas['option_c'] = $option_c;
            $datas['option_d'] = $option_d;
            $datas['admin'] = $admin;
            $datas['created_at'] = Carbon::now();  

            DB::connection('training')->table('tb_question')->insert($datas) ; 
            $dt['process_status'] = 1 ;
            $dt['msg_process'] = 'Success' ; 
        } else {
            $datas['id_training_test'] = $id_training_test;
            $datas['question'] = str_replace("tanda_kutip", "'", str_replace("tanda_kutip_dua", '"', str_replace("lebih_kecil", "<", str_replace("lebih_besar", ">", str_replace("jeung", "&", str_replace("pleus", "+", $question))))));
            $datas['index_question'] = $index_question;
            $datas['answer_code'] = $answer;
            $datas['option_a'] = $option_a;
            $datas['option_b'] = $option_b;
            $datas['option_c'] = $option_c;
            $datas['option_d'] = $option_d;
            $datas['admin'] = $admin;
            DB::connection('training')->table('tb_question')->where('id', $id)->update($datas) ;  
            $dt['process_status'] = 1 ;
            $dt['msg_process'] = 'Updated' ; 
        }
          }
      return json_encode($dt);

}

#endregion

}
