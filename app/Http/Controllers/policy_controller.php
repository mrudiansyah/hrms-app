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

class policy_controller extends Controller
{
    private $site;
    public function __construct(){
        $this->middleware(['auth','verified']);
        $this->site = $_SERVER['SCRIPT_NAME'];
    }
    function index(){
        if (request()->user()->hasRole('hr_access')||request()->user()->hasRole('policy')){
            $email=Auth::user()->email;
            $tb_employee=DB::table('tb_emails')
            ->leftjoin('tb_employees','tb_employees.id','=','tb_emails.id_employee')
            ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
            ->where('email_address',$email)->get();
            foreach($tb_employee as $dt){
                $my_level=$dt->id_level;
            }
            if (request()->user()->hasRole('hr_access')){
                $data['tb_policy']=DB::table('tb_policy')->where('status','1')->orderby('policy_name','asc')->get();
            }else{
                $data['tb_policy']=DB::table('tb_policy')
                ->leftjoin('tb_policy_level','tb_policy_level.id_policy','=','tb_policy.id')
                ->where('tb_policy_level.id_level',$my_level)
                ->where('tb_policy_level.status','1')
                ->where('tb_policy.status','1')
                ->orderby('policy_name','asc')->get(['tb_policy.*']);
            }
            //return $data['tb_policy'];
            return view('page/policy/policy',['data'=>$data,'site'=>$this->site,'menu'=>'policy','juduls'=>'Kebijakan Direksi']);
        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }
    function policyArsif(){
        if (request()->user()->hasRole('hr_access')||request()->user()->hasRole('policy')){
            $email=Auth::user()->email;
            $tb_employee=DB::table('tb_emails')
            ->leftjoin('tb_employees','tb_employees.id','=','tb_emails.id_employee')
            ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
            ->where('email_address',$email)->get();
            foreach($tb_employee as $dt){
                $my_level=$dt->id_level;
            }
            if (request()->user()->hasRole('hr_access')){
                $data['tb_policy']=DB::table('tb_policy')->where('status','0')->orderby('policy_name','asc')->get();
            }else{
                $data['tb_policy']=DB::table('tb_policy')
                ->leftjoin('tb_policy_level','tb_policy_level.id_policy','=','tb_policy.id')
                ->where('tb_policy_level.id_level',$my_level)
                ->where('tb_policy_level.status','1')
                ->where('tb_policy.status','0')
                ->orderby('policy_name','asc')->get(['tb_policy.*']);
            }
            //return $data['tb_policy'];
            return view('page/policy/policy_arsif',['data'=>$data,'site'=>$this->site,'menu'=>'policy','juduls'=>'Kebijakan Direksi']);
        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }
    function document_upload(request $data){
        $admin=Auth::user()->name;
        if($data->policy_name=='')return "File Name tidak boleh kosong";
        if($data->id_file==''){
            if(!$data->hasFile('file_name') || !$data->file('file_name')->isValid()){
                return "Dokumen tidak terbaca";
            }

            $file = $data->file('file_name');
            $namaFile = $file->getClientOriginalName();
            $tb_policy=DB::table('tb_policy')->where('file_name',$namaFile)->count();
            if($tb_policy>0)return "Dokumen sudah ada yang sama";

            $file->storeAs('public', $namaFile);
            $add=DB::table('tb_policy')->insert([
                'policy_name'=>$data->policy_name,
                'description'=>$data->description,
                'publish_date'=>$data->publish_date,
                'file_name'=>$namaFile,
                'uploaded_by'=>$admin,
            ]);
            if($add)return "Sukses Add";
        }else{
            $update=DB::table('tb_policy')->where('id',$data->id_file)->update([
                'policy_name'=>$data->policy_name,
                'description'=>$data->description,
                'publish_date'=>$data->publish_date,
                'uploaded_by'=>$admin,
            ]);
            if($update)return "Sukses Update";
        }
    }
    function document_delete(request $request){
        try {
            // Cari data policy
            $policy = DB::table('tb_policy')->where('id', $request->id)->first();
            
            if (!$policy) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
            }
            
            // Hapus file fisik
            $filePath = 'public/' . $policy->file_name;
            if (Storage::exists($filePath)) {
                Storage::delete($filePath);
            }
            
            // Hapus dari database
            DB::table('tb_policy')->where('id', $request->id)->delete();
            
            return response()->json(['success' => true, 'message' => 'Data dan file berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    function document_download(request $data){
        $email=Auth::user()->email;
        $akses=DB::table('tb_emails')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_emails.id_employee')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->leftjoin('tb_level','tb_level.id','=','tb_positions.id_level')
        ->leftjoin('tb_policy_level','tb_policy_level.id_level','=','tb_level.id')
        ->where('tb_policy_level.id_policy',$data->id)
        ->where('email_address',$email)->whereNotNull('tb_policy_level.id_policy')->where('tb_policy_level.status','1')->count();

        if (request()->user()->hasRole('hr_access'))$akses=1;
        if($akses==0){
            return abort(403,'Anda tidak punya akses');
        }else{
            $tb_policy=DB::table('tb_policy')->where('id',$data->id)->get();
            foreach($tb_policy as $dt){
                if(Storage::exists('public/'.$dt->file_name)){ 
                    return Storage::download('public/'.$dt->file_name);
                }else{
                    return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
                }
            }
        }
    }
    function document_show(Request $request) {
        $email=Auth::user()->email;
        $akses=DB::table('tb_emails')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_emails.id_employee')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->leftjoin('tb_level','tb_level.id','=','tb_positions.id_level')
        ->leftjoin('tb_policy_level','tb_policy_level.id_level','=','tb_level.id')
        ->where('tb_policy_level.id_policy',$request->id)
        ->where('email_address',$email)->whereNotNull('tb_policy_level.id_policy')->where('tb_policy_level.status','1')->count();
        if (request()->user()->hasRole('hr_access'))$akses=1;
        if($akses==0){
            return abort(403,'Anda tidak punya akses');
        }else{
            // Cari data policy
            $policy = DB::table('tb_policy')->where('id', $request->id)->first();
            
            if (!$policy) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
            }
        
            $filePath = storage_path('app/public/' . $policy->file_name);
            
            if (!file_exists($filePath)) {
                return response()->json(['success' => false, 'message' => 'File tidak ditemukan']);
            }
        
            // Return response untuk menampilkan PDF di browser
            return response()->file($filePath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $policy->file_name . '"'
            ]);
        }
    }
    function policyLevel($id_policy){
        $admin=Auth::user()->name;
        $cek=DB::table('tb_policy_level')->where('id_policy',$id_policy)->count();
        //return $cek;
        if($cek==0){
            $tb_level=DB::table('tb_level')->get();
            foreach($tb_level as $dt){
                $tb_policy_level=DB::table('tb_policy_level')->insert([
                    'id_policy'=>$id_policy,
                    'id_level'=>$dt->id,
                    'updated_by'=>$admin,
                ]);
            }
        }
        $data['tb_policy_level']=DB::table('tb_policy_level')
        ->leftjoin('tb_policy','tb_policy.id','=','tb_policy_level.id_policy')
        ->leftjoin('tb_level','tb_level.id','=','tb_policy_level.id_level')
        ->where('tb_policy_level.id_policy',$id_policy)->get(['tb_policy_level.*','tb_policy.policy_name','tb_level.nama_level']);
        foreach($data['tb_policy_level'] as $dt){
            $data['policy_name']=$dt->policy_name;
        }
        $data['id_policy']=$id_policy;

        if (request()->user()->hasRole('hr_access')){
            return view('page/policy/policy_level',['data'=>$data,'site'=>$this->site,'menu'=>'policy','juduls'=>'Kebijakan Direksi']);
        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }
    function policyLevelSave(Request $data){
        $admin=Auth::user()->name;
        for($i=1;$i<=$data->qty;$i++){
            $teks1='id_level_'.$i;
            $id_level=$data->$teks1;
            $teks2='status_'.$i;
            $teks3=$data->$teks2;
            if($teks3=='')$status='0';
            else $status=$teks3;
            $tb=DB::table('tb_policy_level')->where('id_policy',$data->id_policy)->where('id_level',$id_level)->get();
            foreach($tb as $dt){
                if($dt->status!=$status){
                    $now=date('Y-m-d H:i:s');
                    $tb=DB::table('tb_policy_level')->where('id_policy',$data->id_policy)->where('id_level',$id_level)->update([
                        'status'=>$status,
                        'updated_by'=>$admin,
                        'updated_at'=>$now,
                    ]);
                }
            }
        }
        return redirect()->back();
    }
    function policyNonactive(Request $data){
        $admin=Auth::user()->name;
        $now=date('Y-m-d H:i:s');
        $update=DB::table('tb_policy')->where('id',$data->id)->update([
            'status'=>'0',
            'deleted_by'=>$admin,
            'deleted_at'=>$now,
        ]);
        //return
        if($update)return "Sukses Update";
    }
    function policyReactive(Request $data){
        $admin=Auth::user()->name;
        $now=date('Y-m-d H:i:s');
        $update=DB::table('tb_policy')->where('id',$data->id)->update([
            'status'=>'1',
        ]);
        //return
        if($update)return "Sukses Update";
    }
    function policy_control(){
        if (request()->user()->hasRole('legal')){
            $data['tb_category']=DB::table('tb_legal_category')->get();
            $data['tb_legal_permit']=DB::table('tb_legal_permits')
            ->leftjoin('tb_legal_category','tb_legal_category.id','=','tb_legal_permits.id_category')
            ->where('status','<>','Extended')
            ->orderby('expiry_date','desc')->get(['tb_legal_permits.*','tb_legal_category.category','tb_legal_category.warning_day','tb_legal_category.critical_day']);
            $today=date('Y-m-d');
            $tgl1 = new DateTime( $today);
            foreach($data['tb_legal_permit'] as $dt){
                $tgl2 = new DateTime($dt->expiry_date);
                $diffdays = $tgl2->diff($tgl1)->days;
                if($dt->expiry_date<=$today){
                    $status='Expired';
                }elseif($diffdays<=$dt->critical_day){
                    $status='Critical';
                }elseif($diffdays<=$dt->warning_day){
                    $status='Warning';
                }else{
                    $status='Active';
                }
                $update=DB::table('tb_legal_permits')->where('id',$dt->id)->update(['status'=>$status]);
            }
            $data['tb_legal_permit']=DB::table('tb_legal_permits')
            ->leftjoin('tb_legal_category','tb_legal_category.id','=','tb_legal_permits.id_category')
            ->where('status','<>','Extended')
            ->orderby('tb_legal_permits.id_category','asc')->get(['tb_legal_permits.*','tb_legal_category.category','tb_legal_category.warning_day','tb_legal_category.critical_day']);
            return view('page/policy/policy_control',['data'=>$data,'site'=>$this->site,'menu'=>'policy_control','juduls'=>'Document Control']);
        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }
    function policy_control_save(request $data){
        $id_category=DB::table('tb_legal_category')->where('category',$data->category)->value('id');
        $namaFile = null;

        if ($data->hasFile('file_upload')) {
            $file = $data->file('file_upload');
            $namaFile = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public', $namaFile);
            
            // Delete old file if updating
            if($data->id != '') {
                $oldFile = DB::table('tb_legal_permits')->where('id', $data->id)->value('file_name');
                if($oldFile && Storage::exists('public/' . $oldFile)) {
                    Storage::delete('public/' . $oldFile);
                }
            }
        }

        if($data->id==''){
            $add=DB::table('tb_legal_permits')->insert([
                'id_category'=>$id_category,
                'permit_name'=>$data->permit_name,
                'description'=>$data->description,
                'expiry_date'=>$data->expiry_date,
                'status'=>$data->status,
                'file_name'=>$namaFile
            ]);
        }else{
            $updateData = [
                'id_category'=>$id_category,
                'permit_name'=>$data->permit_name,
                'description'=>$data->description,
                'expiry_date'=>$data->expiry_date,
                'status'=>$data->status
            ];
            if ($namaFile) {
                $updateData['file_name'] = $namaFile;
            }
            $update=DB::table('tb_legal_permits')->where('id',$data->id)->update($updateData);
        }
    }
    function policy_control_delete(request $request){
        try {
            // Cari data untuk hapus filenya
            $permit = DB::table('tb_legal_permits')->where('id', $request->id)->first();
            if ($permit && $permit->file_name && Storage::exists('public/' . $permit->file_name)) {
                Storage::delete('public/' . $permit->file_name);
            }

            // Hapus dari database
            DB::table('tb_legal_permits')->where('id', $request->id)->delete();
            
            return response()->json(['success' => true, 'message' => 'Data dan file berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    function policyArsif_control(){
        if (request()->user()->hasRole('legal')){
            $data['tb_legal_permit']=DB::table('tb_legal_permits')
            ->leftjoin('tb_legal_category','tb_legal_category.id','=','tb_legal_permits.id_category')
            ->where('status','Extended')
            ->orderby('expiry_date','desc')->get(['tb_legal_permits.*','tb_legal_category.category','tb_legal_category.warning_day','tb_legal_category.critical_day']);
            return view('page/policy/policy_control_arsif',['data'=>$data,'site'=>$this->site,'menu'=>'policy_control','juduls'=>'Document Control']);
        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }

}
