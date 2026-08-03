<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DateTime;
use Image;
use Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class admin_employee extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
       
    }
    function index(){
        $email=Auth::user()->email;
        $cek1=DB::table('users')->where('email',$email)->get();
        foreach($cek1 as $dt){
            $id_user=$dt->id;
            $cek2=DB::table('role_users')->where('user_id',$id_user)->where('role_id','3')->count();
            if($cek2==0)return view('auth/login');
        }
        $tb_employee=DB::table('tb_employees')
        ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_employees.leader_id')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->leftjoin('tb_salaries', function ($join) {
            $join->on('tb_salaries.id_employee', '=', 'tb_employees.id')
                 ->where('tb_salaries.status','1');
        })
        ->where([['tb_employees.delete','0'],['tb_employees.status','1']])
        ->orderby('tb_employees.join_date','asc')
        //->count();
        ->get(['tb_employees.*','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_name','tb_salaries.salary','tb_employees1.employee_name as leader']);
        //$this->generate_leave();
        $tb_employee_data=DB::table('view_employee_data')
        ->where('position_name','<>','PSAB')
        ->get();
        return view('page/admin/m_employee/employee_read',['tb_employee'=>$tb_employee_data,'menu'=>'employee']);
    }
    function createData(){
        $tb_department=DB::table('tb_departments')->where('isDelete',0)->get();
        $tb_position=DB::table('tb_positions')->get();
        $tb_contract=DB::table('tb_contract')->whereNull('id_status')->get();
        $tb_cost_center=DB::table('tb_cost_center')->orderby('cc_code','asc')->get();
        $tb_iclock=DB::table('tb_iclock')->select('userid','name','badgenumber')->groupby('userid','name','badgenumber')->get();
        return view('page/admin/m_employee/employee_create',['tb_department'=>$tb_department,'tb_cost_center'=>$tb_cost_center,'tb_position'=>$tb_position,'tb_contract'=>$tb_contract,'tb_iclock'=>$tb_iclock,'menu'=>'employee']);
    }
    function insertData(Request $data){
        $this->validate($data,[
            'NIK' => 'required|unique:users,NIK',
            'employee_name' => 'required',
            'gender'=>'required|in:Laki-laki,Perempuan',
            'dept_id' => 'numeric|required',
            'position_id' => 'numeric|required',
            'join_date' => 'required',
            'cc_code' => 'required',
            'email_address'=>'required|email|unique:users,email'
        ]);
        $teks=$data->employee_name.' berhasil disimpan';
        $input=$data->all();
        unset($input['_token']);
        $cek=DB::table('tb_employees')->where('NIK',$data->NIK)->count();
        if($cek>0){
            $teks=$data->NIK.' sudah terdaftar';
            return redirect('/Admin/Employee/Create')->withErrors(['Error' => $teks]);    
        }else{
            $file = $data->file('foto');
            if($file!=''){ 
                $filename = $file->getClientOriginalName();
                $valid_ext = array('png','jpeg','jpg');
                $location = 'public/fokar/'.$filename;
                $file_extension = pathinfo($location, PATHINFO_EXTENSION);
                $file_extension = strtolower($file_extension);
                if(in_array($file_extension,$valid_ext)){
                $imagePath = $file->getPathName();

                $this->compressImage($imagePath,$location,20);
                $tujuan_upload = 'public/fokar';
                $path = $tujuan_upload.'/'.$file->getClientOriginalName();
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $datas = file_get_contents($path);
                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($datas);

                }else{
                echo "Invalid file type.";
                }
                $save_photo=DB::table('tb_photos')->insert(['id_employee'=>$data->id,'nama_photo'=>$base64]);

            }
            $simpan=DB::table('tb_employees')->insert([
                'PIN'=>$data->PIN,
                'NIK'=>$data->NIK,
                'employee_name'=>$data->employee_name,
                'gender'=>$data->gender,
                'dept_id'=>$data->dept_id,
                'dept_id2'=>$data->dept_id,
                'position_id'=>$data->position_id,
                'leader_id'=>$data->leader_id,
                'status'=>'1',
                'delete'=>'0',
                'join_date'=>$data->join_date,
                'badgenumber'=>$data->PIN,
                'id_agreement'=>$data->id_agreement,
                'cc_code'=>$data->cc_code,
            ]);
            if($simpan){
                $tb_employee=DB::table('tb_employees')->where('NIK',$data->NIK)->orderby('id','desc')->limit(1)->get();
                foreach($tb_employee as $row){
                    $add_mail=$this->simpanEmail($data->NIK,$data->email_address,$row->id,$row->employee_name);
                }
            }
            if($add_mail=='Sukses'){
                return redirect('/Admin/Employee/Create')->with(['success' => $teks]);    
            }else{
                return redirect('/Admin/Employee/Create')->with(['success' => 'Failed save email']);    
            }
    
        }
    }
    function selectLeader(Request $data){
        $deptid=$data->deptid;
        $posid=$data->posid;
        
        $posindex=DB::table('tb_positions')->where('id',$posid)->get();
        foreach($posindex as $dt){
            $position_index=$dt->position_index;
        }

        $leader=$data->leader;
        $leader_name='';
        $dt_leader=DB::table('tb_employees')->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')->where('tb_employees.id',$leader)->get(["tb_employees.*"]);
        foreach($dt_leader as $dt2){
            $leader_name=$dt2->employee_name;
        }
        $leaders="<option value='".$leader."'>".$leader_name."</pilih>";
        
        if($position_index<='4')
        // $dt_employee=DB::table('tb_employees')->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')->where([['dept_id',$deptid],['position_index','>',$position_index],['tb_employees.status','1']])->get(["tb_employees.*"]);
        $dt_employee=DB::table('tb_employees')->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')->where([['position_index','>',$position_index],['tb_employees.status','1']])->orderby('tb_employees.employee_name','asc')->get(["tb_employees.*"]);        
        else
        $dt_employee=DB::table('tb_employees')->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')->where([['position_index','>',$position_index],['tb_employees.status','1']])->orderby('tb_employees.employee_name','asc')->get(["tb_employees.*"]);        
        
        foreach ($dt_employee as $dt ){
          $leaders.= "<option value='".$dt->id."'>".$dt->employee_name."</option>";
        }
        return $leaders;
    }
    function selectCC(Request $data){
        $deptid=$data->deptid;
        $tb_cost_center=DB::table('tb_cost_center')->where('dept_id',$deptid)->orderby('cc_code','asc')->get();

        $data="";
        
        foreach ($tb_cost_center as $dt ){
          $data.= "<option value='".$dt->cc_code."'>(".$dt->cc_code.") ".$dt->segment_name."</option>";
        }
        return $data;
    }
    function selectDept(Request $data){
        $tb_department=DB::table('tb_departments')->where('isDelete',0)->get();
        $dept="<option value=''></pilih>";
       
        foreach ($tb_department as $dt ){
            $dept.= "<option value='".$dt->id."'>".$dt->dept_name."</option>";
        }
        return $dept;
    }
    public function selectKab(Request $request)
    {
        $id = $request->id;
        $n = strlen($id);
        $m = ($n == 2) ? 5 : (($n == 5) ? 8 : 13);
        
        // Koneksi ke database db_wilayah (konfigurasi di config/database.php)
        $data = DB::table('wilayah_2020')
            ->select('kode', 'nama')
            ->whereRaw('LEFT(kode, ?) = ?', [$n, $id])
            ->whereRaw('LEN(kode) = ?', [$m])  // SQL Server pakai LEN()
            ->orderBy('nama')
            ->get();
        
        $kabupaten = "<option value='0'>&nbsp;</option>";
        
        foreach ($data as $dt) {
            $kabupaten .= "<option value='" . $dt->kode . "'>" . $dt->nama . "</option>";
        }
        
        return $kabupaten;
    }    
    public function selectKec(Request $request)
    {
        $id = $request->id;
        $n = strlen($id);
        $m = ($n == 2) ? 5 : (($n == 5) ? 8 : 13);
        
        // Koneksi ke database db_wilayah
        $data = DB::table('wilayah_2020')
            ->select('kode', 'nama')
            ->whereRaw('LEFT(kode, ?) = ?', [$n, $id])
            ->whereRaw('LEN(kode) = ?', [$m])  // SQL Server pakai LEN()
            ->orderBy('nama')
            ->get();
        
        $kecamatan = "<option value='0'>&nbsp;</option>";
        
        foreach ($data as $dt) {
            $kecamatan .= "<option value='" . $dt->kode . "'>" . $dt->nama . "</option>";
        }
        
        return $kecamatan;
    }
    public function selectDes(Request $request)
    {
        $id = $request->id;
        $n = strlen($id);
        $m = ($n == 2) ? 5 : (($n == 5) ? 8 : 13);
        
        // Koneksi ke database db_wilayah
        $data = DB::table('wilayah_2020')
            ->select('kode', 'nama')
            ->whereRaw('LEFT(kode, ?) = ?', [$n, $id])
            ->whereRaw('LEN(kode) = ?', [$m])
            ->orderBy('nama')
            ->get();
        
        $desa = "<option value='0'>&nbsp;</option>";
        
        foreach ($data as $dt) {
            $desa .= "<option value='" . $dt->kode . "'>" . $dt->nama . "</option>";
        }
        
        return $desa;
    }
    public function selectLink(Request $request)
    {
        $desa = $request->desa;
        $kecamatan = $request->kecamatan;
        
        // Koneksi ke database db_wilayah
        $linkmap = "https://www.google.co.id/maps/place/";
        
        // Query untuk mendapatkan nama desa
        $dataDesa = DB::table('wilayah_2020')
            ->select('nama')
            ->where('kode', $desa)
            ->first();
        
        if ($dataDesa) {
            $linkmap .= $dataDesa->nama . "+";
        }
        
        // Query untuk mendapatkan nama kecamatan
        $dataKecamatan = DB::table('wilayah_2020')
            ->select('nama')
            ->where('kode', $kecamatan)
            ->first();
        
        if ($dataKecamatan) {
            $linkmap .= $dataKecamatan->nama;
        }
        
        return $linkmap;
    }
    function updateData($id){
        $tb_department=DB::table('tb_departments')->where('isDelete',0)->get();
        $tb_position=DB::table('tb_positions')->get();
        $leader_name='';
        $tb_employee=DB::table('tb_employees')->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')->where('tb_employees.id',$id)->orderby('id','desc')->get(['tb_employees.*','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_name']);
        foreach($tb_employee as $dt){
            $PIN=$dt->PIN;
            $leader=DB::table('tb_employees')->where('id',$dt->leader_id)->get();
            foreach($leader as $dt2){
                $leader_name=$dt2->employee_name;
            }
            $nama_karyawan=$dt->employee_name;
            $NIK=$dt->NIK;
        }
        $tb_address=DB::table('tb_addresses')->where([['id_employee',$id]])->get();
        $tb_domiciles=DB::table('tb_domiciles')->where([['id_employee',$id]])->get();
        $tb_education=DB::table('tb_educations')->where('id_employee',$id)->orderby('graduate_year','desc')->get();
        $tb_experience=DB::table('tb_experiences')->where('id_employee',$id)->orderby('finish_year','desc')->get();
        $tb_skill=DB::table('tb_skills')->where('id_employee',$id)->get();
        $tb_email=DB::table('tb_emails')->where([['id_employee',$id]])->get();
        $tb_salary=DB::table('tb_salaries')->where([['id_employee',$id],['status','1']])->get();
        $tb_admin=DB::table('tb_admins')->leftjoin('tb_departments','tb_departments.id','=','tb_admins.dept_id')->where('tb_admins.id_employee',$id)->get(['tb_admins.*','tb_departments.dept_code','tb_departments.dept_name']);
        $tb_bagian=DB::table('tb_bagians')->where([['id_employee',$id]])->orderby('implement','desc')->limit(1)->get();
        $tb_detail=DB::table('tb_employee_detail')->where([['id_employee',$id]])->get();
        $tb_address_darurat=DB::table('tb_address_darurat')->where([['id_employee',$id]])->where('status','1')->get();
        $tb_employee_family=DB::table('tb_employee_family')->where([['id_employee',$id]])->where('status','1')->get();
        //return $tb_employee_family;
        
        $koneksi='OK';
        $data['tb_prov'] = DB::table('wilayah_2020')
        ->select('kode', 'nama')
        ->whereRaw('LEN(kode) = 2')
        ->orderBy('nama')
        ->get();

        return view('page/admin/m_employee/employee_update',['data'=>$data,'tb_employee_family'=>$tb_employee_family,'tb_address_darurat'=>$tb_address_darurat,'tb_detail'=>$tb_detail,'tb_bagian'=>$tb_bagian,'PIN'=>$PIN,'tb_admin'=>$tb_admin,'tb_salary'=>$tb_salary,'tb_department'=>$tb_department,'tb_position'=>$tb_position,'tb_employee'=>$tb_employee,'tb_address'=>$tb_address,'leader_name'=>$leader_name,'tb_education'=>$tb_education,'tb_experience'=>$tb_experience,'tb_skill'=>$tb_skill,'tb_email'=>$tb_email,'id_employee'=>$id,'nama_karyawan'=>$nama_karyawan,'NIK'=>$NIK,'tb_domiciles'=>$tb_domiciles,'menu'=>'employee']);
    }
    public function saveData(Request $data){
        $this->validate($data,[
            'PIN' => 'numeric|required',
            'NIK' => 'required',
            'employee_name' => 'required',
            'gender'=>'required|in:Laki-laki,Perempuan',
            'dept_id' => 'numeric|required',
            'position_id' => 'numeric|required',
            'leader_id' => 'numeric|required',
            'join_date' => 'required',
            // 'cc_code' => 'required',
            'status' => 'required'
        ]);

        $tujuan_upload = 'public/fokar';

        $namafile=$data->PIN;

        $file = $data->file('foto');
        if($file!=''){ 
            $filename = $file->getClientOriginalName();
            $valid_ext = array('png','jpeg','jpg');
            $location = 'public/fokar/'.$filename;
            $file_extension = pathinfo($location, PATHINFO_EXTENSION);
            $file_extension = strtolower($file_extension);
            if(in_array($file_extension,$valid_ext)){
            $imagePath = $file->getPathName();

            $this->compressImage($imagePath,$location,20);

            $path = $tujuan_upload.'/'.$file->getClientOriginalName();
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $datas = file_get_contents($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($datas);
            
            //$hapus=unlink($path);
            //return $hapus;

            }else{
            echo "Invalid file type.";
            }
            $save_photo=DB::table('tb_photos')->insert(['id_employee'=>$data->id,'nama_photo'=>$base64]);

        }

        $simpan=DB::table('tb_employees')->where('id',$data->id)->update([
            'PIN'=>$data->PIN,
            'NIK'=>$data->NIK,
            'employee_name'=>$data->employee_name,
            'gender'=>$data->gender,
            'dept_id'=>$data->dept_id,
            'position_id'=>$data->position_id,
            'leader_id'=>$data->leader_id,
            'status'=>$data->status,
            'join_date'=>$data->join_date,
            'badgenumber'=>$data->PIN,
        ]);
        if($simpan){
            $tb_employee=DB::table('tb_employees')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
            ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
            ->where('tb_employees.id',$data->id)
            ->get(['tb_employees.NIK','tb_employees.employee_name','tb_departments.dept_code as department','tb_positions.position_name as jabatan']);
            foreach($tb_employee as $dt){
                $update_payroll=DB::table('tb_salary_contract')->where('id_employee',$data->id)->update([
                    'NIK'=>$dt->NIK,
                    'nama_karyawan'=>$dt->employee_name,
                    'department'=>$dt->department,
                    'jabatan'=>$dt->jabatan
                ]);
                $update_tms=DB::table('tb_work_contract')->where('id_employee',$data->id)->update([
                    'NIK'=>$dt->NIK,
                    'nama_karyawan'=>$dt->employee_name,
                    'department'=>$dt->department,
                    'jabatan'=>$dt->jabatan
                ]);
            }
            $teks=$data->employee_name.' berhasil diupdate';
            return redirect('/Admin/Employee/Update/'.$data->id)->with(['success' => $teks]);
        }
        else    
        return redirect()->back()->with(['success' => 'Gagal']);;
    }
    function compressImage($source, $destination, $quality) {
      $info = getimagesize($source);
      if ($info['mime'] == 'image/jpeg')$image = imagecreatefromjpeg($source);
      elseif ($info['mime'] == 'image/gif')$image = imagecreatefromgif($source);
      elseif ($info['mime'] == 'image/png')$image = imagecreatefrompng($source);
      imagejpeg($image, $destination, $quality);
    }

    function deleteData($id){
        $delete=tb_employee::where('id',$id)->update(['delete'=>'1']);
        if($delete)
        $teks='Data berhasil dihapus';
        return redirect('/Admin/Employee')->with(['success' => $teks]);    
    }
    function saveAddress(Request $data){
        $this->validate($data,[
            'provinsi'=>'required',
            'kabupaten'=>'required',
            'kecamatan'=>'required',
            'kelurahan'=>'required',
            'detail'=>'required',
        ]);
        $update=DB::table('tb_addresses')->where('id_employee',$data->id_employee)->update(['status'=>'0']);

        $prov=DB::table('wilayah_2020')->where('kode',$data->provinsi)->value('nama');
        $kab=DB::table('wilayah_2020')->where('kode',$data->kabupaten)->value('nama');
        $kec=DB::table('wilayah_2020')->where('kode',$data->kecamatan)->value('nama');
        $des=DB::table('wilayah_2020')->where('kode',$data->kelurahan)->value('nama');

        $simpan=DB::table('tb_addresses')->insert([
            'id_employee'=>$data->id_employee,
            'provinsi'=>$prov,
            'kabupaten'=>$kab,
            'kecamatan'=> $kec,
            'kelurahan'=>$des,
            'detail'=>$data->detail,
            'map_address'=>$data->map_address,
            'status'=>'1'
        ]);
        if($simpan)
        $teks='Data Address berhasil disimpan';
        //return $simpan;
        return redirect('/Admin/Employee/Update/'.$data->id_employee)->with(['success' => $teks]);    
    }
    function saveDomicile(Request $data){
        $this->validate($data,[
            'provinsi_domicile'=>'required',
            'kabupaten_domicile'=>'required',
            'kecamatan_domicile'=>'required',
            'kelurahan_domicile'=>'required',
            'detail_domicile'=>'required',
        ]);
        $update=DB::table('tb_domiciles')->where('id_employee',$data->id_employee)->update(['status'=>'0']);

        $prov=DB::table('wilayah_2020')->where('kode',$data->provinsi_domicile)->value('nama');
        $kab=DB::table('wilayah_2020')->where('kode',$data->kabupaten_domicile)->value('nama');
        $kec=DB::table('wilayah_2020')->where('kode',$data->kecamatan_domicile)->value('nama');
        $des=DB::table('wilayah_2020')->where('kode',$data->kelurahan_domicile)->value('nama');
        
        $map=$this->generateMapLink($prov, $kab, $kec,$des,$data->detail_domicile);

        $simpan=DB::table('tb_domiciles')->insert([
            'id_employee'=>$data->id_employee,
            'provinsi'=>$prov,
            'kabupaten'=>$kab,
            'kecamatan'=> $kec,
            'kelurahan'=>$des,
            'detail'=>$data->detail_domicile,
            'map_address'=>'',
            'status'=>'1'
        ]);
        if($simpan)
        $teks='Data Address berhasil disimpan';
        //return $simpan;
        return redirect('/Admin/Employee/Update/'.$data->id_employee)->with(['success' => $teks]);    
    }
    function saveShift(Request $data){
        $this->validate($data,['groupid'=>'required']);
        $update=tb_employee_shift::where([['id_employee',$data->id_employee]])->update(['status'=>'0']);
        $simpan=tb_employee_shift::create([
            'id_employee'=>$data->id_employee,
            'id_shift'=>$data->groupid,
            'supdate'=>$data->start_implement,
            'status'=>'1'
        ]);
        if($simpan){
            $teks='Data Shift berhasil disimpan';
        }
        //return $simpan;
        return redirect()->back()->with(['success' => $teks]);    
    }
    function saveEducation(Request $data){
        $this->validate($data,['level_education'=>'required']);
        $simpan=DB::table('tb_educations')->insert([
            'id_employee'=>$data->id_employee,
            'level_education'=>$data->level_education,
            'institute'=>$data->institute,
            'prodi'=>$data->prodi,
            'year'=>$data->year,
            'graduate_year'=>$data->graduate_year,
            'remark'=>$data->remark
        ]);
        if($simpan)
        $teks='Data Education berhasil disimpan';
        //return $simpan;
        return redirect('/Admin/Employee/Update/'.$data->id_employee)->with(['success' => $teks]);    
    }
    function saveExperience(Request $data){
        $this->validate($data,['factory'=>'required','section'=>'required','year'=>'required','finish_year'=>'required']);
        $simpan=DB::table('tb_experiences')->insert([
            'id_employee'=>$data->id_employee,
            'factory'=>$data->factory,
            'section'=>$data->section,
            'year'=>$data->year,
            'finish_year'=>$data->finish_year,
            'remark'=>$data->remark
        ]);
        if($simpan)
        $teks='Data Experience berhasil disimpan';
        //return $simpan;
        return redirect('/Admin/Employee/Update/'.$data->id_employee)->with(['success' => $teks]);    
    }
    function saveSkill(Request $data){
        $this->validate($data,['skill_name'=>'required']);
        $simpan=DB::table('tb_skills')->insert([
            'id_employee'=>$data->id_employee,
            'skill_type'=>$data->skill_type,
            'skill_name'=>$data->skill_name
        ]);
        if($simpan)
        $teks='Data Skill berhasil disimpan';
        //return $simpan;
        return redirect('/Admin/Employee/Update/'.$data->id_employee)->with(['success' => $teks]);    
    }
    function saveBagian(Request $data){
        $this->validate($data,['posisi2'=>'required']);
        $simpan=DB::table('tb_bagians')->insert([
            'id_employee'=>$data->id_employee,
            'line'=>$data->line,
            'posisi'=>$data->posisi2,
            'implement'=>$data->implement
        ]);
        if($simpan)
        $teks='Data Posisi berhasil disimpan';
        //return $simpan;
        return redirect('/Admin/Employee/Update/'.$data->id_employee)->with(['success' => $teks]);    
    }
    function saveDetail(Request $data){
        $this->validate($data,[
            'nomor_ktp' => 'required|numeric|digits:16'
        ]);
        //return "Masuk";
        //$this->validate($data,['tanggal_lahir'=>'required']);
        $periksa=DB::table('tb_employee_detail')->where('id_employee',$data->id_employee)->count();
        if($periksa==0){
            $simpan=DB::table('tb_employee_detail')->insert([
                'id_employee'=>$data->id_employee,
                'tempat_lahir'=>$data->tempat_lahir,
                'tanggal_lahir'=>$data->tanggal_lahir,
                'agama'=>$data->agama,
                'golongan_darah'=>$data->golongan_darah,
                'ibu_kandung'=>$data->ibu_kandung,
                'nomor_ktp'=>$data->nomor_ktp,
                'nomor_kk'=>$data->nomor_kk,
                'nomor_npwp'=>$data->nomor_npwp,
                'nomor_bpjs_kes'=>$data->nomor_bpjs_kes,
                'nomor_bpjs_ket'=>$data->nomor_bpjs_ket,
                'nomor_rekening'=>$data->nomor_rekening,
                'nama_bank'=>$data->nama_bank,
                'nomor_telepon'=>$data->nomor_telepon,
            ]);
            //return "Satu";
        }else{
            $simpan=DB::table('tb_employee_detail')->where('id_employee',$data->id_employee)->update([
                'tempat_lahir'=>$data->tempat_lahir,
                'tanggal_lahir'=>$data->tanggal_lahir,
                'agama'=>$data->agama,
                'golongan_darah'=>$data->golongan_darah,
                'ibu_kandung'=>$data->ibu_kandung,
                'nomor_ktp'=>$data->nomor_ktp,
                'nomor_kk'=>$data->nomor_kk,
                'nomor_npwp'=>$data->nomor_npwp,
                'nomor_bpjs_kes'=>$data->nomor_bpjs_kes,
                'nomor_bpjs_ket'=>$data->nomor_bpjs_ket,
                'nomor_rekening'=>$data->nomor_rekening,
                'nama_bank'=>$data->nama_bank,
                'nomor_telepon'=>$data->nomor_telepon,
            ]);
            //return "Dua";
        }
        //return "Tiga";
        if($simpan)
        $teks='General Data berhasil disimpan';
        else $teks='Anda tidak merubah data apapun';
        //return $simpan;
        return redirect('/Admin/Employee/Update/'.$data->id_employee)->with(['success' => $teks]);    
    }
    function saveKontak(Request $data){
        $this->validate($data,['nama_keluarga'=>'required','nomor_kontak'=>'required']);
        $update=DB::table('tb_addresses')->where('id_employee',$data->id_employee)->update(['status'=>'0']);

        $prov=DB::table('wilayah_2020')->where('kode',$data->provinsi_kontak)->value('nama');
        $kab=DB::table('wilayah_2020')->where('kode',$data->kabupaten_kontak)->value('nama');
        $kec=DB::table('wilayah_2020')->where('kode',$data->kecamatan_kontak)->value('nama');
        $des=DB::table('wilayah_2020')->where('kode',$data->kelurahan_kontak)->value('nama');

        $simpan=DB::table('tb_address_darurat')->insert([
            'id_employee'=>$data->id_employee,
            'nama_keluarga'=>$data->nama_keluarga,
            'hubungan'=>$data->hubungan,
            'nomor_kontak'=>$data->nomor_kontak,
            'provinsi_kontak'=>$prov,
            'kabupaten_kontak'=>$kab,
            'kecamatan_kontak'=>$kec,
            'kelurahan_kontak'=>$des,
            'detail_kontak'=>$data->detail_kontak
        ]);
        if($simpan)
        $teks='Data Kontak berhasil disimpan';
        //return $simpan;
        return redirect('/Admin/Employee/Update/'.$data->id_employee)->with(['success' => $teks]);    
    }
    function saveFamily(Request $data){
        //return "Masuk";
        $this->validate($data,['nama_keluargas'=>'required','hubungans'=>'required','tanggal_lahir_keluarga'=>'required']);
        $simpan=DB::table('tb_employee_family')->insert([
            'id_employee'=>$data->id_employee,
            'nama_keluarga'=>$data->nama_keluargas,
            'hubungan'=>$data->hubungans,
            'tanggal_lahir'=>$data->tanggal_lahir_keluarga,
            'status'=>'1'
        ]);
        if($simpan)
        $teks='Data Family berhasil disimpan';
        //return $simpan;
        return redirect('/Admin/Employee/Update/'.$data->id_employee)->with(['success' => $teks]);    
    }
    function selectDelete($id,$tabel){
        $teks="Gagal";
        if (request()->user()->hasRole('hr_access')){
            $now=date('Y-m-d');
            $nama=Auth::user()->name;

            if($tabel=='Address'){
                $delete=DB::table('tb_addresses')->where('id',$id)->delete();
            }elseif($tabel=='Domicile'){
                $delete=DB::table('tb_domiciles')->where('id',$id)->delete();
            }elseif($tabel=='Education'){
                $delete=DB::table('tb_educations')->where('id',$id)->delete();
            }elseif($tabel=='Experience'){
                $delete=DB::table('tb_experiences')->where('id',$id)->delete();
            }elseif($tabel=='Skill'){
                $delete=DB::table('tb_skills')->where('id',$id)->delete();
            }elseif($tabel=='Email'){
                $delete=DB::table('tb_emails')->where('id',$id)->delete();
            }elseif($tabel=='Salary'){
                $delete=DB::table('tb_salaries')->where('id',$id)->delete();
            }elseif($tabel=='Admin'){
                $delete=DB::table('tb_admins')->where('id',$id)->delete();
            }elseif($tabel=='Posisi'){
                $delete=DB::table('tb_bagians')->where('id',$id)->delete();
            }elseif($tabel=='Kontak'){
                $delete=DB::table('tb_address_darurat')->where('id',$id)->delete();
            }elseif($tabel=='Family'){
                $delete=DB::table('tb_employee_family')->where('id',$id)->delete();
            }elseif($tabel=='Other'){
                $delete=DB::table('tb_employee_others')->where('id',$id)->update(['isDelete'=>'1','status'=>'0','updated_at'=>$now,'admin'=>$nama]);
            }elseif($tabel=='OtherActive'){
                $delete=DB::table('tb_employee_others')->where('id',$id)->update(['isDelete'=>'0','status'=>'1','updated_at'=>$now,'admin'=>$nama]);
            }

            if($delete)
            $teks="Data Tabel ".$tabel." berhasil dihapus";
        }
        return redirect()->back()->with(['success' => $teks]);    
    }
    function saveEmail(Request $data){
        $NIK=$data->NIK;
        $tgl_sekarang=date('Y-m-d H:i:s');
        $this->validate($data,['email_address'=>'required|email|unique:tb_emails,email_address']);
        $simpan=tb_email::create([
            'id_employee'=>$data->id_employee,
            'email_address'=>$data->email_address,
            'receive_slip_gaji'=>$data->receive_slipgaji,
            'receive_slip_ot'=>$data->receive_slipot
        ]);
        if($simpan){
            $cek_user=DB::connection('mysql')->table('users')->where('nik',$NIK)->count();
            if($cek_user==0){
                $add_user=DB::connection('mysql')->table('users')->insert([
                    'name' => $data->nama_karyawan,
                    'email' => $data->email_address,
                    'email_verified_at'=>$tgl_sekarang,
                    'nik'=>$NIK,
                    'password' =>'$2y$10$rHOAhWixGqy58KbLaqappOs8atW8JfGfScoSzbEcfugSQCTHyqMJO',
                    'hint'=>$NIK,
                ]);
            }
            $tb_user=DB::connection('mysql')->table('users')->where('nik',$NIK)->get();
            foreach($tb_user as $dt){
                $cek_role=DB::connection('mysql')->table('role_users')->where('user_id',$dt->id)->where('role_id','36')->count();
                if($cek_role==0){
                    $add_role=DB::connection('mysql')->table('role_users')->insert([
                        'user_id'=>$dt->id,
                        'role_id'=>'36',
                    ]);
                }
            }
            $teks='Email berhasil disimpan';
        }
        return redirect('/Admin/Employee/Update/'.$data->id_employee)->with(['success' => $teks]);    
    }
    function saveSalary(Request $data){
        $this->validate($data,['main_salary'=>'required','meal_salary'=>'required','start_implement'=>'required']);
        $update=tb_salary::where('id_employee',$data->id_employee)->update(['status'=>'0']);
        $simpan=tb_salary::create(['id_employee'=>$data->id_employee,'salary'=>$data->main_salary,'meal'=>$data->meal_salary,'slpj'=>$data->main_salary/173,'implement'=>$data->start_implement]);
        if($simpan){
            $teks='Salary berhasil disimpan';
        }
        return redirect('/Admin/Employee')->with(['success' => $teks]);    
        //return redirect('/Admin/Employee/Update/'.$data->id_employee)->with(['success' => $teks]);    
    }
    function saveAdmin(Request $data){
        $this->validate($data,['dept_id'=>'required']);
        $simpan=tb_admin::create(['id_employee'=>$data->id_employee,'dept_id'=>$data->dept_id]);
        if($simpan){
            $teks='Admin berhasil disimpan';
        }
        return redirect('/Admin/Employee/Update/'.$data->id_employee)->with(['success' => $teks]);    
    }
    function simpanEmail($NIK,$email,$id_employee,$employee_name){
        $awal=date('Y-m').'-01';
        $hasil="Failed";
        $tgl_sekarang=date('Y-m-d H:i:s');
        $cek_email=DB::table('tb_emails')->where('email_address',$email)->count();
        if($cek_email==0){
            $simpan=DB::table('tb_emails')->insert([
                'id_employee'=>$id_employee,
                'email_address'=>$email,
            ]);
            if($simpan){
                $hasil="Sukses";
                $cek_user=DB::table('users')->where('nik',$NIK)->count();
                if($cek_user==0){
                    $add_user=DB::table('users')->insert([
                        'name' => $employee_name,
                        'email' => $email,
                        'email_verified_at'=>$tgl_sekarang,
                        'nik'=>$NIK,
                        'password' => '$2y$10$rHOAhWixGqy58KbLaqappOs8atW8JfGfScoSzbEcfugSQCTHyqMJO',
                        'hint'=>'default',
                    ]);
                }else{
                    $hasil="Failed User";
                }
                $tb_user=DB::table('users')->where('nik',$NIK)->get();
                foreach($tb_user as $dt){
                    $cek_role=DB::table('role_users')->where('user_id',$dt->id)->where('role_id','36')->count();
                    if($cek_role==0){
                        $add_role=DB::table('role_users')->insert([
                            'user_id'=>$dt->id,
                            'role_id'=>'36',
                        ]);
                    }else{
                        $hasil="Failed Role";
                    }
                }
            }else{
                $hasil="Failed Email";
            }
        }else{
            $hasil = "Failed Email";
        }

        return $hasil;
    }
    public function generate_leave(){
        date_default_timezone_set("Asia/Bangkok");
        $kalendar=CAL_GREGORIAN;
        $Tgl=date('Y-m-d');
        $nama=Auth::user()->name;

        $tb_leave=DB::table('tb_employees')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_employee_leaves','tb_employee_leaves.id_employee','=','tb_employees.id')
        ->where('tb_employees.status','1')
        // ->where('tb_employee_leaves','1')
        ->get(['tb_employees.id as idemployee','tb_employees.NIK','tb_employees.employee_name','tb_employees.join_date','tb_departments.dept_code','tb_employee_leaves.*']);
        foreach($tb_leave as $dt){
            if($dt->qty_approval=='')$qty_approval=2;
            else $qty_approval=$dt->qty_approval;
            $tgl1 = new DateTime($dt->join_date);
            $tgl2 = new DateTime($Tgl);
            $diffdays = $tgl2->diff($tgl1)->days;
            $diffyears=Floor($diffdays/365);

            $Thnawal=date('Y',strtotime($dt->join_date));
            $Thnstart=$Thnawal+$diffyears;
            $Thnend=$Thnstart+1;

            $jatah='0';
            if($diffyears<1){
                $Thnstart++;
                $Thnend++;
            }

            $Bln=date('m-d',strtotime($dt->join_date));
            if($Bln=='02-29')$Bln='02-28';
            $Periode_awal=$Thnstart.'-'.$Bln;
            $Periode_akhir_temp=$Thnend.'-'.$Bln;
            $Periode_akhir = date('Y-m-d', strtotime("-1 day", strtotime($Periode_akhir_temp)));
            $Periode_extend=date('Y-m-d', strtotime("+6 month", strtotime($Periode_akhir)));
            
            
            if($dt->id==''){
                DB::table('tb_employee_leaves')->insert([
                    'id_employee'=>$dt->idemployee,
                    'year'=>$Thnstart,
                    'start'=>$Periode_awal,
                    'end'=>$Periode_akhir,
                    'extend'=>$Periode_extend,
                    'sisa'=>'0',
                    'kurang'=>'0',
                    'allowance'=>$jatah,
                    'used'=>'0',
                    'outstanding'=>$jatah,
                    'remark'=>'FROM admin_employee',
                    'status'=>'1',
                    'qty_approval'=>$qty_approval,
                ]);
            }else{
                if($dt->extend<$Tgl){
                    $update=DB::connection('mysql')->table('tb_employee_leaves')->where('id',$dt->id)->update(['status'=>'0']);
                }
            }
        }
    }
    function department($kategori,$periode){
        if($periode==0)$periode=date('Y-m');
        if($kategori=='0')$kategori='Position';
        if($kategori=='Department'){
            $data_table=DB::table('tb_departments')->where('isDelete','0')->get();
            $data_table1=DB::table('view_sum_address')->where('periode',$periode)->get();
        }elseif($kategori=='Position'){
            $data_table=DB::table('tb_record_position')->leftjoin('tb_departments','tb_departments.dept_code','=','tb_record_position.dept_code')->where('periode',$periode)->get(['tb_record_position.*','tb_departments.dept_category']);
            $data_table1=DB::table('view_sum_position')->where('periode',$periode)->get();
        }elseif($kategori=='Contract'||$kategori=='Gender'){
            $data_table=DB::table('tb_record_kontrak')->leftjoin('tb_departments','tb_departments.dept_code','=','tb_record_kontrak.dept_code')->where('periode',$periode)->get(['tb_record_kontrak.*','tb_departments.dept_category']);
            $data_table1=DB::table('view_sum_kontrak')->where('periode',$periode)->get();
        }elseif($kategori=='Age'){
            $data_table=DB::table('tb_record_age')->leftjoin('tb_departments','tb_departments.dept_code','=','tb_record_age.dept_code')->where('periode',$periode)->get(['tb_record_age.*','tb_departments.dept_category']);
            $data_table1=DB::table('view_sum_age')->where('periode',$periode)->get();
        }elseif($kategori=='Service Time'){
            $data_table=DB::table('tb_record_workingtime')->leftjoin('tb_departments','tb_departments.dept_code','=','tb_record_workingtime.dept_code')->where('periode',$periode)->get(['tb_record_workingtime.*','tb_departments.dept_category']);
            $data_table1=DB::table('view_sum_workingtime')->where('periode',$periode)->get();
        }elseif($kategori=='Religion'){
            $data_table=DB::table('tb_record_religion')->leftjoin('tb_departments','tb_departments.dept_code','=','tb_record_religion.dept_code')->where('periode',$periode)->get(['tb_record_religion.*','tb_departments.dept_category']);
            $data_table1=DB::table('view_sum_religion')->where('periode',$periode)->get();
        }elseif($kategori=='Education'){
            $data_table=DB::table('tb_record_education')->leftjoin('tb_departments','tb_departments.dept_code','=','tb_record_education.dept_code')->where('periode',$periode)->get(['tb_record_education.*','tb_departments.dept_category']);
            $data_table1=DB::table('view_sum_education')->where('periode',$periode)->get();
        }elseif($kategori=='Marital Status'){
            $data_table=DB::table('tb_record_tax')->leftjoin('tb_departments','tb_departments.dept_code','=','tb_record_tax.dept_code')->where('periode',$periode)->get(['tb_record_tax.*','tb_departments.dept_category']);
            $data_table1=DB::table('view_sum_tax')->where('periode',$periode)->get();
        }elseif($kategori=='Address'){
            $data_table=DB::table('tb_record_address')->leftjoin('tb_departments','tb_departments.dept_code','=','tb_record_address.dept_code')->where('periode',$periode)->get(['tb_record_address.*','tb_departments.dept_category']);
            $data_table1=DB::table('view_sum_address')->where('periode',$periode)->get();
        }
        return view('page/admin/m_employee/department',['data_table'=>$data_table,'data_table1'=>$data_table1,'kategori'=>$kategori,'periode'=>$periode,'menu'=>'employee']);
    }
    function recordShow($periode){
        if($periode==0)$periode=date('Y-m');
        //return $periode;
        if (request()->user()->hasRole('hr_access')) {
            $tb_employee_data=DB::table('tb_employee_records')->where('periode',$periode)->get();
        }
        return view('page/admin/m_employee/employee_record',['tb_employee'=>$tb_employee_data,'periode'=>$periode,'menu'=>'employee']);
    }

    function recordSave(request $data){
        $result="No Action";
        $admin=Auth::user()->name;
        $now=date('Y-m-d H:i:s');
        $cek=DB::table('tb_employee_records')->where('periode',$data->periode)->count();
        if($cek==0){
            $view_employee=DB::table('view_employee_data')->orderby('id','asc')->get();
            foreach($view_employee as $dt){
                $add=DB::table('tb_employee_records')->insert([
                    'id_employee'=>$dt->id,
                    'PIN'=>$dt->badgenumber,
                    'NIK'=>$dt->NIK,
                    'employee_name'=>$dt->employee_name,
                    'department'=>$dt->dept_code,
                    'position'=>$dt->position_name,
                    'status'=>$dt->contract_name,
                    'start_contract'=>$dt->start_contract,
                    'finish_contract'=>$dt->finish_contract,
                    'gender'=>$dt->gender,
                    'line'=>$dt->line,
                    'tax'=>$dt->kode_status,
                    'birth_city'=>$dt->tempat_lahir,
                    'date_birth'=> $dt->tanggal_lahir,
                    'kabupaten'=>$dt->kabupaten,
                    'telepon'=>$dt->nomor_telepon,
                    'blood'=>$dt->golongan_darah,
                    'religion'=>$dt->agama,
                    'education'=>$dt->top_education,
                    'program'=>$dt->prodi,
                    'mother'=>$dt->ibu_kandung,
                    'KTP'=>$dt->nomor_ktp,
                    'NPWP'=>$dt->nomor_npwp,
                    'bank_account'=>$dt->nomor_rekening,
                    'KK'=>$dt->nomor_kk,
                    'bpjs_kes'=>$dt->nomor_bpjs_kes,
                    'bpjs_ket'=>$dt->nomor_bpjs_ket,
                    'emergency'=>$dt->nama_keluarga,
                    'relation'=>$dt->hubungan,
                    'contact'=>$dt->nomor_kontak,
                    'periode'=>$data->periode,
                    'created_at'=>$now,
                    'admin'=>$admin,
                    'dept_id'=>$dt->dept_id,
                    'id_level'=>$dt->id_level,
                ]);
            }
            $table=DB::table('view_employee_address')->get();
            foreach($table as $dt){
                $add=DB::table('tb_record_address')->insert([
                    'dept_code'=>$dt->dept_code,
                    'karawang'=>$dt->karawang,
                    'luar_karawang'=>$dt->luar_karawang,
                    'periode'=>$data->periode,
                    'Admin'=>$admin,
                ]);
            }
            $table2=DB::table('view_employee_age')->get();
            foreach($table2 as $dt){
                $add=DB::table('tb_record_age')->insert([
                    'dept_code'=>$dt->dept_code,
                    'periode'=>$data->periode,
                    'Admin'=>$admin,
                    'b18'=>$dt->b18,
                    'b25'=>$dt->b25,
                    'b35'=>$dt->b35,
                    'b45'=>$dt->b45,
                    'b55'=>$dt->b55,
                    'm55'=>$dt->m55,
                    'other'=>$dt->other,
                    'pria'=>$dt->pria,
                    'wanita'=>$dt->wanita,
                    'jml_karyawan'=>$dt->jml_karyawan,
                ]);
            }
            $table3=DB::table('view_employee_education')->get();
            foreach($table3 as $dt){
                $add=DB::table('tb_record_education')->insert([
                    'dept_code'=>$dt->dept_code,
                    'periode'=>$data->periode,
                    'Admin'=>$admin,
                    'SD'=>$dt->SD,
                    'SLTP'=>$dt->SLTP,
                    'SLTA'=>$dt->SLTA,
                    'D1'=>$dt->D1,
                    'D2'=>$dt->D2,
                    'D3'=>$dt->D3,
                    'S1'=>$dt->S1,
                    'S2'=>$dt->S2,
                    'blank'=>$dt->blank,
                ]);
            }
            $table4=DB::table('view_employee_kontrak')->get();
            foreach($table4 as $dt){
                $add=DB::table('tb_record_kontrak')->insert([
                    'dept_code'=>$dt->dept_code,
                    'periode'=>$data->periode,
                    'Admin'=>$admin,
                    'jml_management'=>$dt->jml_management,
                    'jml_leader'=>$dt->jml_leader,
                    'jml_pelaksana'=>$dt->jml_pelaksana,
                    'jml_permanen'=>$dt->jml_permanen,
                    'jml_permanen_pria'=>$dt->jml_permanen_pria,
                    'jml_permanen_perempuan'=>$dt->jml_permanen_perempuan,
                    'jml_kontrak'=>$dt->jml_kontrak,
                    'jml_kontrak_pria'=>$dt->jml_kontrak_pria,
                    'jml_kontrak_perempuan'=>$dt->jml_kontrak_perempuan,
                    'jml_magang'=>$dt->jml_magang,
                    'jml_magang_hub'=>$dt->jml_magang_hub,
                ]);
            }
            $table5=DB::table('view_employee_position')->get();
            foreach($table5 as $dt){
                $add=DB::table('tb_record_position')->insert([
                    'dept_code'=>$dt->dept_code,
                    'periode'=>$data->periode,
                    'Admin'=>$admin,
                    'jml_karyawan'=>$dt->jml_karyawan,
                    'jml_karyawan_all'=>$dt->jml_karyawan_all,
                    'pd'=>$dt->pd,
                    'vp'=>$dt->vp,
                    'director'=>$dt->director,
                    'gm'=>$dt->gm,
                    'agm'=>$dt->agm,
                    'dh'=>$dt->dh,
                    'astman'=>$dt->astman,
                    'sh'=>$dt->sh,
                    'specialist'=>$dt->specialist,
                    'leader'=>$dt->leader,
                    'officer'=>$dt->officer,
                    'staff'=>$dt->staff,
                    'pelaksana'=>$dt->pelaksana,
                    'driver'=>$dt->driver,
                    'magang'=>$dt->magang,
                    'magang_hub'=>$dt->magang_hub,
                    'DPK'=>$dt->DPK,
                ]);
            }
            $table6=DB::table('view_employee_religion')->get();
            foreach($table6 as $dt){
                $add=DB::table('tb_record_religion')->insert([
                    'dept_code'=>$dt->dept_code,
                    'periode'=>$data->periode,
                    'Admin'=>$admin,
                    'islam'=>$dt->islam,
                    'protestan'=>$dt->protestan,
                    'katolik'=>$dt->katolik,
                    'hindu'=>$dt->hindu,
                    'budha'=>$dt->budha,
                    'konghucu'=>$dt->konghucu,
                    'undefined'=>$dt->undefined,
                ]);
            }
            $table7=DB::table('view_employee_tax')->get();
            foreach($table7 as $dt){
                $add=DB::table('tb_record_tax')->insert([
                    'dept_code'=>$dt->dept_code,
                    'periode'=>$data->periode,
                    'Admin'=>$admin,
                    'TK'=>$dt->TK,
                    'TK1'=>$dt->TK1,
                    'TK2'=>$dt->TK2,
                    'K0'=>$dt->K0,
                    'K1'=>$dt->K1,
                    'K2'=>$dt->K2,
                    'K3'=>$dt->K3,
                    'blank'=>$dt->blank,
                ]);
            }
            $table8=DB::table('view_employee_workingtime')->get();
            foreach($table8 as $dt){
                $add=DB::table('tb_record_workingtime')->insert([
                    'dept_code'=>$dt->dept_code,
                    'periode'=>$data->periode,
                    'Admin'=>$admin,
                    'b1'=>$dt->b1,
                    'b2'=>$dt->b2,
                    'b3'=>$dt->b3,
                    'b4'=>$dt->b4,
                    'b5'=>$dt->b5,
                    'b6'=>$dt->b6,
                    'b7'=>$dt->b7,
                    'b8'=>$dt->b8,
                    'b9'=>$dt->b9,
                    'b10'=>$dt->b10,
                    'b11'=>$dt->b11,
                    'b12'=>$dt->b12,
                    'b13'=>$dt->b13,
                    'b14'=>$dt->b14,
                    'b15'=>$dt->b15,
                    'b16'=>$dt->b16,
                    'b17'=>$dt->b17,
                    'b18'=>$dt->b18,
                    'b19'=>$dt->b19,
                    'b20'=>$dt->b20,
                    'm20'=>$dt->m20,
                ]);
            }
            if($add)$result='Sukses';
        }else{
            $result='Data sudah ada';
        }
        return $result;
    }
    function recordUpdate(request $data){
        $result="No Action";
        $cek=DB::table('tb_employee_records')->where('periode',$data->periode)->count();
        if($cek>0){
            $delete=DB::table('tb_employee_records')->where('periode',$data->periode)->delete();
            if($delete){
                $tb1=DB::table('tb_record_address')->where('periode',$data->periode)->delete();
                $tb2=DB::table('tb_record_age')->where('periode',$data->periode)->delete();
                $tb3=DB::table('tb_record_education')->where('periode',$data->periode)->delete();
                $tb4=DB::table('tb_record_kontrak')->where('periode',$data->periode)->delete();
                $tb5=DB::table('tb_record_position')->where('periode',$data->periode)->delete();
                $tb6=DB::table('tb_record_religion')->where('periode',$data->periode)->delete();
                $tb7=DB::table('tb_record_tax')->where('periode',$data->periode)->delete();
                $tb8=DB::table('tb_record_workingtime')->where('periode',$data->periode)->delete();
                $result='Sukses';
            }
        }else{
            $result='Data tidak ada';
        }
        return $result;
    }
    function costCenter(){
        $data_table=DB::table('tb_cost_center')->get();
        return view('page/admin/m_employee/cost_center',['data_table'=>$data_table,'menu'=>'employee']);
    }
    function bpjs(){
        $tb_employee=DB::table('tb_employees')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->leftjoin('tb_employee_detail','tb_employee_detail.id_employee','=','tb_employees.id')
        ->select('tb_employees.*','tb_employee_detail.id as id_detail','tb_departments.dept_code','tb_positions.position_name','tb_employee_detail.nomor_kk','tb_employee_detail.nomor_ktp','tb_employee_detail.nomor_bpjs_kes','tb_employee_detail.nomor_bpjs_ket','tb_employee_detail.status_bpjs_kes','tb_employee_detail.status_bpjs_ket')
        ->where('tb_employees.status','1')
        ->Where(function ($query) {
            $query->where('tb_employee_detail.status_bpjs_kes','0')
                  ->orWhere('tb_employee_detail.status_bpjs_ket','0');
        })
        ->get();
        //return $tb_employee;
        return view('page/admin/m_employee/employee_bpjs',['tb_employee'=>$tb_employee,'menu'=>'employee_bpjs']);
    }
    function bpjsUpdate(request $data){

        $now=date('Y-m-d H:i:s');
        $hasil=$data->id_detail.'#'.$data->nomor_kk.'#'.$data->nomor_ktp.'#'.$data->nomor_bpjs_kes.'#'.$data->nomor_bpjs_ket.'#'.$data->status_bpjs_kes.'#'.$data->status_bpjs_ket;
        if($data->status_bpjs_kes=='true')$status1='1';
        else $status1='0';
        if($data->status_bpjs_ket=='true')$status2='1';
        else $status2='0';

        $update=DB::table('tb_employee_detail')->where('id',$data->id_detail)->update([
            'nomor_kk'=>$data->nomor_kk,
            'nomor_ktp'=>$data->nomor_ktp,
            'nomor_bpjs_kes'=>$data->nomor_bpjs_kes,
            'nomor_bpjs_ket'=>$data->nomor_bpjs_ket,
            'status_bpjs_kes'=>$status1,
            'status_bpjs_ket'=>$status2,
            'updated_at'=>$now,
        ]);
        if($update)$hasil="Success";
        return $hasil;
    }
    function bpjsUpdateTK(request $data){
        $now=date('Y-m-d H:i:s');
        $hasil=$data->id_detail.'#'.$data->status_bpjs_ket;
        if($data->status_bpjs_ket==0)$status=1;
        else $status=0;

        $update=DB::table('tb_employee_detail')->where('id',$data->id_detail)->update([
            'status_bpjs_ket'=>$status,
            'updated_at'=>$now,
        ]);
        if($update)$hasil="Success";
        return $hasil;
    }
    function bpjsUpdateKes(request $data){
        $now=date('Y-m-d H:i:s');
        $hasil=$data->id_detail.'#'.$data->status_bpjs_kes;
        if($data->status_bpjs_kes==0)$status=1;
        else $status=0;

        $update=DB::table('tb_employee_detail')->where('id',$data->id_detail)->update([
            'status_bpjs_kes'=>$status,
            'updated_at'=>$now,
        ]);
        if($update)$hasil="Success";
        return $hasil;
    }
    function otherEmployee($type){
        $table1=DB::table('tb_employee_others');
        if($type=='Arsif'){
            $table1=$table1->where('isDelete','1');
        }else{
            if($type!=0){
                $table1=$table1->where('sub_kategori',$type);
            }
            $table1=$table1->where('isDelete','0');
        }
        $table1=$table1->orderby('nama','asc')->get();
        $table2=DB::table('tb_departments')->where('isDelete','0')->get();
        // return $table1;
        return view('page/admin/m_employee/employee_other',['table1'=>$table1,'table2'=>$table2,'type'=>$type,'menu'=>'employee']);
    }
    function otherEmployeeSave(request $data){
        $email=Auth::user()->email;
        $table1=DB::table('tb_employee_others')->where('nomor_ktp',$data->nomorktp)->count();
        if($table1==0){
            $update=DB::table('tb_employee_others')->insert([
                'kategori'=>$data->kategori,
                'sub_kategori'=>$data->subkategori,
                'nama'=>$data->nama,
                'gender'=>$data->gender,
                'nomor_ktp'=>$data->nomorktp,
                'ibu_kandung'=>$data->ibukandung,
                'join_date'=>$data->joindate,
                'dept_code'=>$data->deptcode,
                'PIN'=>$data->pin,
                'tempat_lahir'=>$data->tempatlahir,
                'tanggal_lahir'=>$data->tanggallahir,
                'nomor_hp'=>$data->nomorhp,
                'admin'=>$email
            ]);
        }else{
            $update=DB::table('tb_employee_others')->where('nomor_ktp',$data->nomorktp)->update([
                'kategori'=>$data->kategori,
                'sub_kategori'=>$data->subkategori,
                'nama'=>$data->nama,
                'gender'=>$data->gender,
                'ibu_kandung'=>$data->ibukandung,
                'join_date'=>$data->joindate,
                'dept_code'=>$data->deptcode,
                'PIN'=>$data->pin,
                'tempat_lahir'=>$data->tempatlahir,
                'tanggal_lahir'=>$data->tanggallahir,
                'nomor_hp'=>$data->nomorhp,
                'admin'=>$email
            ]);
        }
        if($update)return "Success";
        else return $data->nama;
        //return view('page/admin/m_employee/employee_other',['table1'=>$table1,'menu'=>'employee']);
    }
    function employeeDomiciles(){
        $tb_employee=DB::table('tb_employees')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_domiciles','tb_domiciles.id_employee','=','tb_employees.id')
        ->where('tb_employees.status','1')
        ->get(['tb_employees.NIK','tb_employees.employee_name','tb_departments.dept_code','tb_domiciles.*','tb_positions.position_name']);
        //return $tb_domiciles;
        return view('page/admin/m_employee/employee_domiciles',['tb_employee'=>$tb_employee,'menu'=>'employee']);
    }
    function employeeAddress(){
        $tb_employee=DB::table('tb_employees')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_addresses','tb_addresses.id_employee','=','tb_employees.id')
        ->where('tb_employees.status','1')
        ->get(['tb_employees.NIK','tb_employees.employee_name','tb_departments.dept_code','tb_addresses.*','tb_positions.position_name']);
        //return $tb_domiciles;
        return view('page/admin/m_employee/employee_address',['tb_employee'=>$tb_employee,'menu'=>'employee']);
    }
    function newEmployee(){
        $table1 = DB::table('tb_contract')
        ->where('type','Kontrak')
        ->whereNotIn('id', function ($query) {
            $query->select('id_agreement')->from('tb_employees')->whereNotNull('id_agreement');
        })
        ->get();
        $qty['table1']=DB::table('tb_contract')
        ->where('type','Kontrak')
        ->whereNotIn('id', function ($query) {
            $query->select('id_agreement')->from('tb_employees')->whereNotNull('id_agreement');
        })
        ->count();
        $table1a = DB::table('tb_contract')
        ->where('type','Magang')
        ->whereNotIn('id', function ($query) {
            $query->select('id_agreement')->from('tb_employees')->whereNotNull('id_agreement');
        })
        ->get();
        $qty['table1a']=DB::table('tb_contract')
        ->where('type','Magang')
        ->whereNotIn('id', function ($query) {
            $query->select('id_agreement')->from('tb_employees')->whereNotNull('id_agreement');
        })
        ->count();

        $table2 = DB::table('tb_employees')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->where('tb_employees.status','0')
        ->where('tb_employees.delete','0')
        ->where('tb_employees.position_id','<>','19')
        ->whereNotIn('tb_employees.id', function ($query) {
            $query->select('id_employee')->from('tb_statuses')->whereNotNull('id_employee');
        })
        ->get(['tb_employees.*','tb_departments.dept_name','tb_positions.position_name']);
        $qty['table2']= DB::table('tb_employees')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->where('tb_employees.status','0')
        ->where('tb_employees.delete','0')
        ->where('tb_employees.position_id','<>','19')
        ->whereNotIn('tb_employees.id', function ($query) {
            $query->select('id_employee')->from('tb_statuses')->whereNotNull('id_employee');
        })
        ->count();
        $table2a = DB::table('tb_employees')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->where('tb_employees.status','0')
        ->where('tb_employees.delete','0')
        ->where('tb_employees.position_id','19')
        ->whereNotIn('tb_employees.id', function ($query) {
            $query->select('id_employee')->from('tb_statuses')->whereNotNull('id_employee');
        })
        ->get(['tb_employees.*','tb_departments.dept_name','tb_positions.position_name']);
        $qty['table2a']= DB::table('tb_employees')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->where('tb_employees.status','0')
        ->where('tb_employees.delete','0')
        ->where('tb_employees.position_id','19')
        ->whereNotIn('tb_employees.id', function ($query) {
            $query->select('id_employee')->from('tb_statuses')->whereNotNull('id_employee');
        })
        ->count();
        //return $table2;

        $table3 = DB::table('tb_employees')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->where('tb_employees.delete','0')
        ->where('position_index','<','5')
        ->where('tb_employees.position_id','<>','19')
        ->where('tb_employees.position_id','<>','37')
        ->whereNotIn('tb_employees.id', function ($query) {
            $query->select('id_employee')->from('tb_salaries')->whereNotNull('id_employee');
        })
        ->get(['tb_employees.*','tb_departments.dept_name','tb_positions.position_name']);
        $qty['table3'] = DB::table('tb_employees')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->where('tb_employees.delete','0')
        ->where('position_index','<','5')
        ->where('tb_employees.position_id','<>','19')
        ->where('tb_employees.position_id','<>','37')
        ->whereNotIn('tb_employees.id', function ($query) {
            $query->select('id_employee')->from('tb_salaries')->whereNotNull('id_employee');
        })
        ->count();
        $table3a = DB::table('tb_employees')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->where('tb_employees.delete','0')
        ->where('position_index','<','5')
        ->where('tb_employees.position_id','19')
        ->whereNotIn('tb_employees.id', function ($query) {
            $query->select('id_employee')->from('tb_salaries')->whereNotNull('id_employee');
        })
        ->get(['tb_employees.*','tb_departments.dept_name','tb_positions.position_name']);
        $qty['table3a'] = DB::table('tb_employees')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->where('tb_employees.delete','0')
        ->where('position_index','<','5')
        ->where('tb_employees.position_id','19')
        ->whereNotIn('tb_employees.id', function ($query) {
            $query->select('id_employee')->from('tb_salaries')->whereNotNull('id_employee');
        })
        ->count();

        $today=date('Y-m-d');
        $table4=DB::table('tb_employees')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->leftjoin('tb_statuses', function ($join) {
            $join->on('tb_statuses.id_employee', '=', 'tb_employees.id');
                  //->where('tb_statuses.active', '1');
        })
        ->whereExists(function ($query) {
            $query->select(DB::raw(1))
                  ->from('tb_statuses')
                  ->whereColumn('tb_statuses.id_employee', 'tb_employees.id');
        })
        ->where('delete','0')
        ->where('tb_employees.status','1')
        ->where('tb_statuses.active', '1')
        ->where('contract_name','<>','Permanen')
        ->whereNotNull('finish_contract')
        ->where('finish_contract','<=',$today)
        ->where(function ($query){
            $query ->where('tb_statuses.contract_name','Kontrak');
        })
        ->orderby('finish_contract','asc')
        ->get(['tb_employees.PIN','tb_employees.id as idemployee','tb_employees.join_date as joindate','tb_employees.NIK','tb_employees.employee_name','tb_employees.gender','tb_employees.status','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_name','tb_statuses.*']);
        //return $table4;
        $qty['table4']=DB::table('tb_employees')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->leftjoin('tb_statuses', function ($join) {
            $join->on('tb_statuses.id_employee', '=', 'tb_employees.id');
                  //->where('tb_statuses.active', '1');
        })
        ->whereExists(function ($query) {
            $query->select(DB::raw(1))
                  ->from('tb_statuses')
                  ->whereColumn('tb_statuses.id_employee', 'tb_employees.id');
        })
        ->where('delete','0')
        ->where('tb_employees.status','1')
        ->where('tb_statuses.active', '1')
        ->where('contract_name','<>','Permanen')
        ->whereNotNull('finish_contract')
        ->where('finish_contract','<=',$today)
        ->where(function ($query){
            $query ->where('tb_statuses.contract_name','Kontrak');
        })
        ->orderby('finish_contract','asc')
        ->count();
        $table4a=DB::table('tb_employees')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->leftjoin('tb_statuses', function ($join) {
            $join->on('tb_statuses.id_employee', '=', 'tb_employees.id');
                  //->where('tb_statuses.active', '1');
        })
        ->whereExists(function ($query) {
            $query->select(DB::raw(1))
                  ->from('tb_statuses')
                  ->whereColumn('tb_statuses.id_employee', 'tb_employees.id');
        })
        ->where('delete','0')
        ->where('tb_employees.status','1')
        ->where('tb_statuses.active', '1')
        ->where('contract_name','<>','Permanen')
        ->whereNotNull('finish_contract')
        ->where('finish_contract','<=',$today)
        ->where(function ($query){
            $query ->Where('tb_statuses.contract_name','Magang');
        })
        ->orderby('finish_contract','asc')
        ->get(['tb_employees.PIN','tb_employees.id as idemployee','tb_employees.join_date as joindate','tb_employees.NIK','tb_employees.employee_name','tb_employees.gender','tb_employees.status','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_name','tb_statuses.*']);
        //return $table4;
        $qty['table4a']=DB::table('tb_employees')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->leftjoin('tb_statuses', function ($join) {
            $join->on('tb_statuses.id_employee', '=', 'tb_employees.id');
                  //->where('tb_statuses.active', '1');
        })
        ->whereExists(function ($query) {
            $query->select(DB::raw(1))
                  ->from('tb_statuses')
                  ->whereColumn('tb_statuses.id_employee', 'tb_employees.id');
        })
        ->where('delete','0')
        ->where('tb_employees.status','1')
        ->where('tb_statuses.active', '1')
        ->where('contract_name','<>','Permanen')
        ->whereNotNull('finish_contract')
        ->where('finish_contract','<=',$today)
        ->where(function ($query){
            $query ->Where('tb_statuses.contract_name','Magang');
        })
        ->orderby('finish_contract','asc')
        ->count();

        //return $table3;
        return view('page/admin/m_employee/employee_new',['table1'=>$table1,'table1a'=>$table1a,'table2'=>$table2,'table2a'=>$table2a,'table3'=>$table3,'table3a'=>$table3a,'table4'=>$table4,'table4a'=>$table4a,'qty'=>$qty,'menu'=>'employee']);
    }
    function generateMapLink($provinsi, $kabupaten, $kecamatan,$desa,$detailAlamat) {
        $fullAddress = "{$detailAlamat}, {$desa}, {$kecamatan}, {$kabupaten}, {$provinsi}";
        $encodedAddress = urlencode($fullAddress);
        $iframeSrc = "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.0405391163818!2d107.27705657459006!3d-6.258390461272131!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69832243580c7f%3A0xbf61a43aa7646d4!2s{$encodedAddress}!5e0!3m2!1sen!2sid!4v1741062632282!5m2!1sen!2sid";
        $iframeCode = "<iframe src=\"{$iframeSrc}\" width=\"100%\" height=\"300\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>";
        return $iframeCode;
    }
    function education(){
        $email=Auth::user()->email;
        $cek1=DB::table('users')->where('email',$email)->get();
        //return $cek1;
        foreach($cek1 as $dt){
            $id_user=$dt->id;
            $cek2=DB::table('role_users')->where('user_id',$id_user)->where('role_id','3')->count();
            if($cek2==0)return view('auth/login');
        }
        $tb_employee_education=DB::table('tb_educations')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_educations.id_employee')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->where('tb_employees.status','1')
        ->where('tb_educations.is_top','1')
        ->get(['tb_employees.NIK','tb_employees.employee_name','tb_departments.dept_code','tb_positions.position_name','tb_educations.*']);
        return view('page/admin/m_employee/employee_education',['tb_employee'=>$tb_employee_education,'menu'=>'employee']);
    }
    function psab_list(){
        $email=Auth::user()->email;
        $cek1=DB::table('users')->where('email',$email)->get();
        //return $cek1;
        foreach($cek1 as $dt){
            $id_user=$dt->id;
            $cek2=DB::table('role_users')->where('user_id',$id_user)->where('role_id','3')->count();
            if($cek2==0)return view('auth/login');
        }
        $tb_employee=DB::table('tb_employees')
        ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','=','tb_employees.leader_id')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->leftjoin('tb_salaries', function ($join) {
            $join->on('tb_salaries.id_employee', '=', 'tb_employees.id')
                 ->where('tb_salaries.status','1');
        })
        ->where([['tb_employees.delete','0'],['tb_employees.status','1']])
        ->orderby('tb_employees.join_date','asc')
        //->count();
        ->get(['tb_employees.*','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_name','tb_salaries.salary','tb_employees1.employee_name as leader']);
        //$this->generate_leave();
        $tb_employee_data=DB::table('view_employee_data')
        ->where('position_name','PSAB')
        ->get();
        return view('page/admin/m_employee/employee_psab',['tb_employee'=>$tb_employee_data,'menu'=>'employee']);
    }

}
