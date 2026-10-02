<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use App\Imports\WorkEntryImport;
use App\Imports\EarnImport;
use Maatwebsite\Excel\Facades\Excel;
use Session;
use DateTime;
use Auth;
use PDF;
use App\Mail\slip_Gaji;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Validator;

class tms_controller extends Controller
{
    public function __construct(){
        $this->middleware(['auth','verified']);
        $this->site = $_SERVER['SCRIPT_NAME'];
    }
    function index($department,$periode,$group){

        if($periode==0)$periode=date('Y-m');
        $subjudul=date('F Y',strtotime($periode.'-01'));
        //$this->setupTMS($department,$periode);
        $kalendar=CAL_GREGORIAN;
        $thn=date('Y',strtotime($periode.'-01'));
        $bln=date('m',strtotime($periode.'-01'));
        $hariakhir=cal_days_in_month($kalendar,$bln,$thn);
        $mulai=date('Y-m-d',strtotime($periode.'-01'));
        $akhir=date('Y-m-d',strtotime($periode.'-'.$hariakhir));

        $tb_department=$this->tb_department();
        //return $tb_department;
        $tb_work_entries=$this->tb_work_entries($department,$periode,$group);
        $work_code_lookup=DB::table('tb_work_code')->get()->keyBy('work_code');
        $entry_ids=$tb_work_entries->pluck('id')->unique();
        $checktime_lookup=collect();
        if($entry_ids->isNotEmpty()){
            $checktime_lookup=DB::table('tb_work_checktime')
                ->whereIn('id_work_entry',$entry_ids)
                ->where('status','0')
                ->get()
                ->keyBy(function($row){
                    return $row->id_work_entry.'|'.$row->id_column;
                });
        }
        //$tb_work_code=DB::table('tb_work_code')->orderby('id','asc')->get();
        $tb_work_shift=DB::table('tb_work_shift')->get();
        $shift_code='All Group';
        foreach($tb_work_shift as $dt){
            if($dt->id==$group)$shift_code=$dt->shift_code;
        }
        $dept_id=0;
        foreach($tb_department as $dt){
            if($dt->dept_code==$department)
            $dept_id=$dt->id;
        }
        $tb_work_code=DB::table('tb_work_code')->where('manual_adjust','1')->orderby('id','asc')->get();
        $cek_ar=DB::table('tb_absensi_rate_kumulatif')
        ->where('tb_absensi_rate_kumulatif.periode',$periode)->where('dept_id',$dept_id)->where('hari_kerja','5')->count();

        $limit_date=DB::table('tb_utilities')->where('id','24')->where('status','1')->value('limit_date');

        if (request()->user()->hasRole('root')||request()->user()->hasRole('tms')||request()->user()->hasRole('admin_department')){
            return view('page/tms/employee_shift',['tb_work_entries'=>$tb_work_entries,'tb_department'=>$tb_department,'tb_work_code'=>$tb_work_code,'tb_work_shift'=>$tb_work_shift,'work_code_lookup'=>$work_code_lookup,'checktime_lookup'=>$checktime_lookup,'department'=>$department,'dept_id'=>$dept_id,'periode'=>$periode,'group'=>$group,'shift_code'=>$shift_code,'mulai'=>$mulai,'akhir'=>$akhir,'cek_ar'=>$cek_ar,'site'=>$this->site,'menu'=>'tms','juduls'=>'Work Entry','subjudul'=>$subjudul,'limit_date'=>$limit_date]);
        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }
    function groupTMS(){
        
        $tb_work_time=$this->tb_work_times();
        $tb_group=DB::table('tb_work_group')->where('isactive',1)->orderby('id','asc')->get();
        $tb_group_shift=DB::table('tb_work_shift')->orderby('id','asc')->get();
        $max_groups=DB::table('tb_work_group')->orderby('id','asc')->max('id');
        $tb_cycle=DB::table('tb_work_cycle')
        ->leftjoin('tb_work_time','tb_work_time.id','=','tb_work_cycle.id_work_time')
        ->where('id_work_group',$max_groups)->get(['tb_work_time.*','tb_work_cycle.id as id_work_cycle','tb_work_cycle.days','tb_work_cycle.id_work_time','tb_work_cycle.id_work_group']);
        return view('page/tms/group_shift',[
            'tb_group'=>$tb_group,'tb_group_shift'=>$tb_group_shift,'menu'=>'shift','max_groups'=>$max_groups,'tb_cycle'=>$tb_cycle,'tb_work_time'=>$tb_work_time,'site'=>$this->site,'menu'=>'tms','juduls'=>'Group
            Shift','department'=>'0','periode'=>'0','subjudul'=>'','work_time'=> '','tb_work_time'=>$tb_work_time]);
    }
    public function tb_work_times(){
        $tabel=DB::table('tb_work_time')->orderby('id','asc')->get();
        return $tabel;
    }
    public function GetDataGroupId(Request $request){
        $str = explode('_',$request->id);    
        $idGroup = Crypt::decryptString(str_replace("-", "=", $str[0]));
        $db = DB::table('tb_work_group')->where('id',$idGroup)->get();
        if(!empty($db)){
            foreach($db as $d){
                $data['group_code'] = $d->group_code;
                $data['cycle_day'] = $d->cycle_day;
                $data['id'] = $d->id;
            }
        }else{
            $data['group_code'] = '';
            $data['cycle_day'] = '';
            $data['id'] = '0';
        }
        return json_encode($data);
    }
    public function GetDataGroup(Request $request){
        $columns = array( 
            0 =>'tb_work_group.group_code', 
            1 =>'tb_work_group.cycle_day',
            2 =>'id',
          ); 
        $totalData =  DB::table('tb_work_group')->count();
        $totalFiltered = $totalData;
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = ($request->input('order.0.column')==0 ? $columns[2] : $columns[$request->input('order.0.column')]);
        $dir = ($request->input('order.0.column')==0 ? 'asc' : $request->input('order.0.dir')) ; 
        if(empty($request->input('search.value')))
        {
            $posts = DB::table('tb_work_group') 
            ->where('isactive',1)
            // ->offset($start)
            ->limit($limit)
            ->orderBy($order,$dir)
            ->get();
        }else{
        $search = $request->input('search.value');  
            $posts = DB::table('tb_work_group') 
            ->where('isactive',1)
            ->where(function($query) use ($search) {
                $query->where('group_code','LIKE', "%$search%") ;
                $query->orWhere('cycle_day','LIKE', "%$search%") ;
              }) 
            ->offset($start)
            ->limit($limit)
            ->orderBy($order,$dir)
            ->get();
            $totalFiltered = DB::table('tb_work_group') 
            ->where('isactive',1)
            ->where(function($query) use ($search) {
                $query->where('group_code','LIKE', "%$search%") ;
                $query->orWhere('cycle_day','LIKE', "%$search%") ;
              }) 
            ->count();
        }
        $data = array();
        if(!empty($posts)){ 
            $no = $start ;
            foreach ($posts as $post)
            { 
                $id = "'".str_replace("=","-", Crypt::encryptString($post->id)).'_'.$no."'"  ;     
                $button = '<button class="btn btn-xs btn-info" id="btnEditGroup" onClick="AddGroup('.$id.')"><i class="fa fa-edit"></i></button>  <button class="btn btn-xs btn-danger" id="btnEditGroup" onClick="DeleteGroup('.$id.')"><i class="fa fa-trash"></i></button>
                ';
                $nested['group_code'] = $post->group_code;
                $nested['cycle_day'] = $post->cycle_day;
                $nested['action'] = $button;
                $data[] = $nested; 
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
    public function SaveGroup(Request $request){
        $validator = Validator::make($request->all(), [
            'group_code' => 'required',
            'cycle_change' => 'required'
          ]);  
          if ($validator->fails()) {  

            $cont = explode("^", $validator->messages()->first()) ; 
            $dt['process_status'] = 0 ;
            $dt['msg_process'] = $cont[1] ;
            $dt['field'] = $cont[0] ;
  
          }else{
            $idGroup = $request->id;
            $data['group_code'] = $request->group_code;
            $data['cycle_day'] = $request->cycle_change;
            $data['updated_at'] = Carbon::now();  

            if($idGroup == 0){
                $data['created_at'] = Carbon::now();
                $validasi = DB::table('tb_work_group')
                ->where('group_code', $request->group_code)
                ->where('cycle_day', $request->cycle_change)
                ->count();
                if($validasi == 0){
                    DB::table('tb_work_group')->insert($data);
                    $dt['process_status'] = 1 ;
                    $dt['msg_process'] = 'Data Berhasil di simpan' ; 
                }else{
                    $dt['process_status'] = 0 ;
                    $dt['msg_process'] = 'Data sudah ada !' ;
                    // $dt['field'] = 'input_judul_Data' ; 
                }
            }else{
                $str = explode('_',$idGroup);
                $idGroup = Crypt::decryptString(str_replace("-", "=", $str[0])) ; 
                $validasi = DB::table('tb_work_group')
                ->where('group_code', $request->group_code)
                ->where('cycle_day', $request->cycle_change)
                ->count();
                if($validasi == 0){
                    DB::table('tb_work_group')->where('id',$idGroup)->update($data);
                    $dt['process_status'] = 1 ;
                    $dt['msg_process'] = 'Data Berhasil di update' ;   
                }else{
                    $dt['process_status'] = 0 ;
                    $dt['msg_process'] = 'Data sudah ada !' ;
                    // $dt['field'] = 'input_judul_soal' ; 
                }
            }
          }
        echo json_encode($dt);
    }
    public function RemoveGroupId(Request $request){
        $str = explode('_',$request->id);    
        $idGroup = Crypt::decryptString(str_replace("-", "=", $str[0]));
        $db = DB::table('tb_work_group')->where('id',$idGroup)->update(['isactive'=>0]);
        if($db){
            $dt['process_status'] = 1 ;
            $dt['msg_process'] = 'Data Berhasil dihapus' ;   
        }else{
            $dt['process_status'] = 0 ;
            $dt['msg_process'] = 'Terjadi kesalahan' ;   
        }
        echo json_encode($dt);    
    }
    public function ShowWorkTime(Request $request){
        $id_time = $request->id_work_time;
        $str = explode('_',$id_time);
        if($str[0] != 0 ){
        $id_work_time = Crypt::decryptString(str_replace("-", "=", $str[0])) ;
        $db = DB::table('tb_work_time')
        ->where('id',$id_work_time)
        ->get();

        if($db->count() > 0){
        foreach($db as $d){
                $data['id'] = $id_time;
            $data['check_in'] = $d->check_in;
            $data['check_out'] = $d->check_out;
            $data['isoma_start'] = $d->isoma_start;
            $data['isoma_finish'] = $d->isoma_finish;
                $data['warna'] = $d->background;
                $data['isactive'] = $d->isactive;

        }
        }else{
            $data['id'] = 0;
            $data['check_in'] = '';
            $data['check_out'] = '';
            $data['isoma_start'] = '';
            $data['isoma_finish'] = '';
                $data['warna'] = '';
                $data['isactive'] = '1';

            }
        }else{
             $data['id'] = 0;
             $data['check_in'] = '';
             $data['check_out'] = '';
             $data['isoma_start'] = '';
             $data['isoma_finish'] = '';
             $data['warna'] = '';
             $data['isactive'] = '1';
        }
        return json_encode($data);
    }
    public function SaveTime(Request $request){
        $id = $request->id_time;
        $data['check_in']=$request->check_in;
        $data['check_out']=$request->check_out;
        $data['isoma_start']=$request->isoma_start;
        $data['isoma_finish']=$request->isoma_finish;
        //$data['background']=$request->background;
        $data['isactive']=$request->isActive;

        if($id == 0){
            $data['created_at'] = Carbon::now();
             $validasi = DB::table('tb_work_time')
             ->where('check_out', $request->check_out)
             ->where('check_in', $request->check_in)
             ->where('isoma_start', $request->isoma_start)
             ->where('isoma_finish', $request->isoma_finish)
             //->where('background', $request->background)
             ->where('isactive', $request->isActive)
             ->count();
             if($validasi == 0){
                DB::table('tb_work_time')->insert($data);
                $dt['process_status'] = 1 ;
                $dt['msg_process'] = 'Data Berhasil di simpan' ;
            }else{
                $dt['process_status'] = 0 ;
                $dt['msg_process'] = 'Data sudah ada !' ;
                // $dt['field'] = 'input_judul_Data' ;
            }
        }else{
             $str = explode('_',$id);
             $id_time = Crypt::decryptString(str_replace("-", "=", $str[0])) ;
             $validasi2 = DB::table('tb_work_time')
             ->where('check_out', $request->check_out)
             ->where('check_in', $request->check_in)
              ->where('isoma_start', $request->isoma_start)
              ->where('isoma_finish', $request->isoma_finish)
              //->where('background', $request->background)
             ->where('isactive', $request->isActive)
             ->count();
             if($validasi2 == 0){
            $data['updated_at'] = Carbon::now();
                DB::table('tb_work_time')->where('id',$id_time)->update($data);
                $dt['process_status'] = 1 ;
                $dt['msg_process'] = 'Data Berhasil di update' ;
             }else{
                $dt['process_status'] = 0 ;
                $dt['msg_process'] = 'Data sudah ada !' ;
                // $dt['field'] = 'input_judul_soal' ;
             }
        }
        echo json_encode($dt);
    }
    public function GetDataWorkTime(){
        $db = DB::table('tb_work_time')
        ->where('isactive',1)
        ->orderBy('check_in','asc')
        ->get();
        $button = "";
        $no = 1;
        foreach($db as $d){
            $id = "'".str_replace("=","-", Crypt::encryptString($d->id)).'_'.$no."'" ;
            $button .='<button class="btn btn-default btn-md" onclick="ShowWorkTime('.$id.')" 
                    style="background:'.$d->background.'; color:'.$d->color.'">
                    '.substr($d->check_in,0,5).' - '.substr($d->check_out,0,5).'
                    </button> ';
            $no++;
            }
        return $button;
    }
    function choseCycle(Request $data){
        $hasil=$data->groupid;
        $tb_cycle=DB::table('tb_work_cycle')
        ->leftjoin('tb_work_time','tb_work_time.id','=','tb_work_cycle.id_work_time')
        ->where('id_work_group',$hasil)->get(['tb_work_time.*','tb_work_cycle.id as id_work_cycle','tb_work_cycle.days','tb_work_cycle.id_work_time','tb_work_cycle.id_work_group']);
        $i=0;
        $konten="";
        foreach($tb_cycle as $dt){
            $konten.="<tr>";
            $konten.="<td style='text-align:center;'>".$i."</td>";
            $konten.="<td>".$dt->id_work_time;
            $konten.="<button type='button' class='pull-right editcycle btn btn-primary btn-xs' data-cycleid='".$dt->id_work_cycle."' data-cycledays='".$i."' data-idworkgroup='".$dt->id_work_group."' data-idworktime='".$dt->id_work_time."'><i class='fa fa-wrench'></i></button>";
            $konten.="</td>";
            $konten.="<td>".$dt->check_in."</td>";
            $konten.="<td>".$dt->check_out."</td>";
            $konten.="<td>";
            $konten.=$dt->isoma_start; 
            if($dt->id_work_time>0)$konten.="~";
            $konten.=$dt->isoma_finish;
            $konten.="</td>";
            $konten.="<td>".$dt->is_advance."</td>";
            $konten.="<td>".$dt->is_cross."</td>";
            $konten.="</tr>";
            $i++;
        }
        return $konten;
    }
    function saveCycle(Request $data){
        $konten="";
        $update=DB::table('tb_work_cycle')->where('id',$data->id)->update([
            'id_work_time'=>$data->idworktime
        ]);
        //Show
            $hasil=$data->idworkgroup;
            $tb_cycle=DB::table('tb_work_cycle')
            ->leftjoin('tb_work_time','tb_work_time.id','=','tb_work_cycle.id_work_time')
            ->where('id_work_group',$hasil)->get(['tb_work_time.*','tb_work_cycle.id as id_work_cycle','tb_work_cycle.days','tb_work_cycle.id_work_time','tb_work_cycle.id_work_group']);
            $i=0;
            foreach($tb_cycle as $dt){
                $konten.="<tr>";
                $konten.="<td style='text-align:center;'>".$i."</td>";
                $konten.="<td>".$dt->id_work_time;
                $konten.="<button type='button' class='pull-right editcycle btn btn-primary btn-xs' data-cycleid='".$dt->id_work_cycle."' data-cycledays='".$i."' data-idworkgroup='".$dt->id_work_group."' data-idworktime='".$dt->id_work_time."'><i class='fa fa-wrench'></i></button>";
                $konten.="</td>";
                $konten.="<td>".$dt->check_in."</td>";
                $konten.="<td>".$dt->check_out."</td>";
                $konten.="<td>";
                $konten.=$dt->isoma_start; 
                if($dt->id_work_time>0)$konten.="~";
                $konten.=$dt->isoma_finish;
                $konten.="</td>";
                $konten.="<td>".$dt->is_advance."</td>";
                $konten.="<td>".$dt->is_cross."</td>";
                $konten.="</tr>";
                $i++;
            }
        // End Show
        return $konten;
    }
    function monthlyCheck($periode){
        if($periode==0)$periode=date('Y-m');
        $subjudul=date('F Y',strtotime($periode.'-01'));
        $kalendar=CAL_GREGORIAN;
        $thn=date('Y',strtotime($periode.'-01'));
        $bln=date('m',strtotime($periode.'-01'));
        $hariakhir=cal_days_in_month($kalendar,$bln,$thn);
        $mulai=date('Y-m-d',strtotime($periode.'-01'));
        $akhir=date('Y-m-d',strtotime($periode.'-'.$hariakhir));

        $tb_department=$this->tb_department();
        $tb_work_entries=DB::table('tb_work_entries')
        ->leftjoin('tb_work_contract','tb_work_contract.id_employee','=','tb_work_entries.id_employee')
        ->leftjoin('tb_work_shift','tb_work_shift.id','tb_work_contract.id_work_shift')
        ->where([['periode',$periode],['tb_work_contract.isactive','1'],['tb_work_contract.is_draft','0'],['tb_work_entries.qty_absent','>','0']])
        ->orderby('tb_work_entries.id','asc')->orderby('plan_actual','desc')
        ->get(['tb_work_entries.*','tb_work_contract.NIK','tb_work_contract.PIN','tb_work_contract.nama_karyawan','tb_work_contract.department','tb_work_contract.jabatan','tb_work_contract.id as id_contract','tb_work_contract.id_work_shift','tb_work_shift.shift_code','tb_work_shift.working_perweek','tb_work_contract.suggest_ws','tb_work_contract.position_index']);
        $work_code_lookup=DB::table('tb_work_code')->get()->keyBy('work_code');
        $entry_ids=$tb_work_entries->pluck('id')->unique();
        $checktime_lookup=collect();
        if($entry_ids->isNotEmpty()){
            $checktime_lookup=DB::table('tb_work_checktime')->whereIn('id_work_entry',$entry_ids)->where('status','0')->get()->keyBy(function($row){
                return $row->id_work_entry.'|'.$row->id_column;
            });
        }
        $tb_work_shift=DB::table('tb_work_shift')->get();
        $shift_code='All Group';
        $dept_id=0;
        $tb_work_code=DB::table('tb_work_code')->where('manual_adjust','1')->orderby('id','asc')->get();
        $cek_ar=DB::table('tb_absensi_rate_kumulatif')
        ->where('tb_absensi_rate_kumulatif.periode',$periode)->where('dept_id',$dept_id)->where('hari_kerja','5')->count();

        $limit_date=DB::table('tb_utilities')->where('id','24')->where('status','1')->value('limit_date');

        if (request()->user()->hasRole('root')||request()->user()->hasRole('tms')||request()->user()->hasRole('admin_department')){
            return view('page/tms/monthly_check',['tb_work_entries'=>$tb_work_entries,'tb_department'=>$tb_department,'tb_work_code'=>$tb_work_code,'tb_work_shift'=>$tb_work_shift,'work_code_lookup'=>$work_code_lookup,'checktime_lookup'=>$checktime_lookup,'department'=>'0','dept_id'=>$dept_id,'periode'=>$periode,'group'=>'0','shift_code'=>$shift_code,'mulai'=>$mulai,'akhir'=>$akhir,'cek_ar'=>$cek_ar,'site'=>$this->site,'menu'=>'tms','juduls'=>'Work Entry','subjudul'=>$subjudul,'limit_date'=>$limit_date]);
        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }
    function checktimeTMS($id_employee,$periode){
        $kalendar=CAL_GREGORIAN;
        $thn=date('Y',strtotime($periode.'-01'));
        $bln=date('m',strtotime($periode.'-01'));
        $hariakhir=cal_days_in_month($kalendar,$bln,$thn);
        $awal=date('Y-m-d',strtotime($periode.'-01'));
        $akhir=date('Y-m-d',strtotime($periode.'-'.$hariakhir));

        $tb_work_entries=DB::table('tb_work_entries')
        ->leftjoin('tb_work_contract','tb_work_contract.id_employee','=','tb_work_entries.id_employee')
        ->leftjoin('tb_work_shift','tb_work_shift.id','=','tb_work_contract.id_work_shift')
            ->where('daily_show','1')
        ->where('tb_work_entries.id_employee',$id_employee)->where('periode',$periode)->where('plan_actual','plan')->get(['tb_work_entries.*','tb_work_contract.PIN','tb_work_contract.nama_karyawan','tb_work_shift.shift_code']);

        $employee=$tb_work_entries->first();
        $nama_karyawan=$employee->nama_karyawan ?? '';
        $badge=str_pad((string)($employee->PIN ?? ''),9,'0',STR_PAD_LEFT);
        $work_time_ids=[];
        $dates=[];
        for($i=1;$i<=31;$i++){
            $date=date('Y-m-d',strtotime($periode.'-'.str_pad($i,2,'0',STR_PAD_LEFT)));
            $dates[]=$date;
        }
        foreach($tb_work_entries as $entry){
            for($i=1;$i<=31;$i++){
                $column='D'.str_pad($i,2,'0',STR_PAD_LEFT);
                if($entry->$column){
                    $work_time_ids[]=$entry->$column;
                }
            }
        }
        $work_times=DB::table('tb_work_time')->whereIn('id',array_unique($work_time_ids))->get()->keyBy('id');
        $free_days=DB::table('tb_freedays')->whereIn('date_off',array_unique($dates))->get()->keyBy('date_off');
        $checktime_records=[];
        foreach($tb_work_entries as $entry){
            for($i=1;$i<=31;$i++){
                $column='D'.str_pad($i,2,'0',STR_PAD_LEFT);
                $date=$dates[$i-1];
                $work_time=$work_times->get($entry->$column);
                $check_in=$work_time->check_in ?? 0;
                $check_out=$work_time->check_out ?? 0;
                $advance=$work_time->is_advance ?? 0;
                $checkin_act=0;
                $checkout_act=0;
                $status='';

                if($check_in!=0){
                    $checkin_base=strtotime($date.' '.$check_in);
                    if($advance==1){
                        $checkin_base=strtotime('-1 day',$checkin_base);
                    }
                    $checkin_start=date('Y-m-d H:i:s',strtotime('-4 hours',$checkin_base));
                    $checkin_end=date('Y-m-d H:i:s',strtotime('+5 hours',$checkin_base));
                    $checkin_act=DB::table('tb_iclock')
                        ->where('badgenumber',$badge)
                        ->whereBetween('checktime',[$checkin_start,$checkin_end])
                        ->max('checktime') ?? 0;
                    if($checkin_act!=0){
                        $status='Present';
                    }
                }

                if($check_out!=0){
                    $checkout_base=strtotime($date.' '.$check_out);
                    $checkout_start=date('Y-m-d H:i:s',strtotime('-2 hours',$checkout_base));
                    $checkout_end=date('Y-m-d H:i:s',strtotime('+5 hours 30 minutes',$checkout_base));
                    $checkout_act=DB::table('tb_iclock')
                        ->where('badgenumber',$badge)
                        ->whereBetween('checktime',[$checkout_start,$checkout_end])
                        ->max('checktime') ?? 0;
                    if($checkout_act!=0){
                        $status='Present';
                    }
                }

                $free_day=$free_days->get($date);
                if($free_day){
                    $status=$free_day->category;
                }

                $checktime_records[]=[
                    'day'=>$i,
                    'date'=>$date,
                    'shift_code'=>$entry->shift_code,
                    'check_in'=>$check_in,
                    'check_out'=>$check_out,
                    'checkin_act'=>$checkin_act,
                    'checkout_act'=>$checkout_act,
                    'status'=>$status
                ];
            }
        }
        
        if (request()->user()->hasRole('root')||request()->user()->hasRole('tms')||request()->user()->hasRole('admin_department')){
            return view('page/tms/employee_checktime',['checktime_records'=>$checktime_records,'periode'=>$periode,'employee_name'=>$nama_karyawan,'badgenumber'=>$badge,'awal'=>$awal,'akhir'=>$akhir,'site'=>$this->site,'menu'=>'tms','juduls'=>'Work Entry']);
        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }
    public function tb_department(){
        $email=Auth::user()->email;
        $tb_email=DB::table('tb_emails')->where('email_address',$email)->get();
        foreach($tb_email as $dt){
            $id_employee=$dt->id_employee;
        }
        $tb_admins=DB::table('tb_admins')->where('id_employee',$id_employee)->get();

        $tabel=DB::table('tb_departments')->where('id','0');
        foreach($tb_admins as $dt2){
            $tabel=$tabel->orwhere('id',$dt2->dept_id);
        }
        $tabel=$tabel->orderby('id','asc')->get(['tb_departments.*','tb_departments.dept_code as department']);
     return $tabel;
    }
    function updateActualOneAll(Request $data){
        \Log::info("Update WE from Finger Start");
        $Today=date('Y-m-d');
        $now=date('Y-m-d H:i:s');
        $kalendar=CAL_GREGORIAN;
        $thn=date('Y',strtotime($data->periode.'-01'));
        $bln=date('m',strtotime($data->periode.'-01'));
        $hariakhir=cal_days_in_month($kalendar,$bln,$thn);
        for($i=1;$i<=$hariakhir;$i++){
            //$i=$day;
            if(strlen($i)==1)$d='0'.$i;
            else $d=$i;
            $column='D'.$d;
            
            $Tgl=$data->periode."-".$d;
            // Update WE-Actual
            
                \Log::info('Actual-Started');
                $cek_actual=DB::table('tb_work_entries')
                ->leftjoin('tb_work_contract','tb_work_contract.id_employee','=','tb_work_entries.id_employee')
                ->where('tb_work_contract.isactive','1')
                ->where('periode',$data->periode)
                ->where('plan_actual','actual')
                ->where('tb_work_entries.id_employee',$data->id_employee)
                ->where(function ($query) use ($column){
                    $query->where($column, '99')
                        ->orWhere($column, '98')
                        ->orWhere($column, '0')
                        ->orWhere($column, '71')
                        ->orwhereNull($column);
                })
                ->count();
                $qty_cek_act=$cek_actual;
                $cek_actual=1;
                if($cek_actual>0&&$Tgl<=$Today){
                    
                    $tb_focus=DB::table('tb_work_entries')
                    ->leftjoin('tb_work_contract','tb_work_contract.id_employee','=','tb_work_entries.id_employee')
                    ->where('tb_work_contract.isactive','1')
                    ->where('periode',$data->periode)
                    ->where('plan_actual','actual')
                    ->where('tb_work_entries.id_employee',$data->id_employee)
                    ->where(function ($query) use ($column){
                        $query->where($column, '99')
                            ->orWhere($column, '98')
                            ->orWhere($column, '0')
                            ->orWhere($column, '71')
                            ->orWhere($column, '72')
                            ->orWhere($column, '52')
                            ->orWhere($column, '710')
                            ->orWhere($column, '720')
                            ->orwhereNull($column);
                    })
                    ->orderby('department','asc')
                    ->orderby('nama_karyawan','asc')
                    ->get(['tb_work_entries.*','tb_work_contract.department','tb_work_contract.nama_karyawan']);
                    $no3=0;
                    foreach($tb_focus as $dtf){
                        \Log::info('Actual-Process: '.$dtf->nama_karyawan);
                        $id_employee_focus=$dtf->id_employee;

                        //$tb_work_entries=$this->tb_work_entries($department,$data->periode,0);
                        $tb_work_entries=DB::table('tb_work_entries')
                        ->leftjoin('tb_work_contract','tb_work_contract.id_employee','=','tb_work_entries.id_employee')
                        ->where('periode',$data->periode)
                        ->where('plan_actual','plan')
                        ->where('tb_work_entries.id_employee',$id_employee_focus)
                        ->orderby('tb_work_entries.id','asc')
                        ->get(['tb_work_entries.*','tb_work_contract.PIN','tb_work_contract.NIK','tb_work_contract.department','tb_work_contract.nama_karyawan']);
                        foreach($tb_work_entries as $dt){
                            $plan_work=$dt->$column;
                            \Log::info('Actual-CheckPlan: '.$dtf->nama_karyawan.' '.$plan_work);
                            //$tb_work_time=$this->tb_work_time($plan_work);
                            $tb_work_time=DB::table('tb_work_time')->where('id',$plan_work)->get();
                            $work_status=0;
                            foreach($tb_work_time as $dt2){
                                $advance=$dt2->is_advance;
                                if($dt2->is_advance==0)$tanggal=$Tgl;
                                else $tanggal=date('Y-m-d',strtotime('-1 days',strtotime($Tgl)));
                                if($plan_work>0){
                                    $check_in=$tanggal.' '.$dt2->check_in;
                                    $check_out=$Tgl.' '.$dt2->check_out;

                                    //Return Checkin Range
                                    if(isset($check_in)&&$check_in!=''&&$check_in!=$check_out){
                                        $ncdatein=$check_in;
                                        $ncdateindown= date('Y-m-d H:i:s',strtotime('-3 hours',strtotime($ncdatein)));
                                        $ncdateinup= date('Y-m-d H:i:s',strtotime('+5 hours',strtotime($ncdatein)));
                                    }else{
                                        $ncdateindown='';
                                        $ncdateinup='';
                                    }
                                    //Return Checkout Range
                                    if(isset($check_out)&&$check_out!=''&&$check_in!=$check_out){
                                        $ncdateout=$check_out;
                                        $ncdateoutdown= date('Y-m-d H:i:s',strtotime('-2 hours',strtotime($ncdateout)));
                                        $ncdateoutup= date('Y-m-d H:i:s',strtotime('+5 hours',strtotime($ncdateout)));
                                    }else{
                                        $ncdateoutdown='';
                                        $ncdateoutup='';
                                    }
                                    //Read Finger Print
                                    $PIN=$dt->PIN;
                                    $NIK=$dt->NIK;

                                    $lenbadge=strlen($PIN);
                                    $nullbadge=9-$lenbadge;
                                    $p='';
                                    for($q=1;$q<=$nullbadge;$q++){
                                        $p.='0';
                                    }
                                    $badge=$p.$PIN;
                                    
                                    if($check_in<=$now) $work_status='99';
                                    else $work_status='98';
                                    //Check Actual Finger
                                        $host = mysqli_connect("192.168.121.4:83306","cahyudin","123456","adms_db");
                                        $text1="select checktime as masuk from checkinout left join userinfo on userinfo.userid=checkinout.userid where userinfo.badgenumber='$badge' and checktime>='$ncdateindown' and checktime<='$ncdateinup' order by checktime desc";
                                        $qry=mysqli_query($host,$text1)or die(mysqli_error($host));
                                        $qty_finger=mysqli_num_rows($qry);
                                        if($qty_finger>0){
                                            while($row=mysqli_fetch_array($qry)){
                                                if($row['masuk']>=$ncdatein)$work_status='71';
                                                elseif($row['masuk']>0) $work_status='51';
                                            }
                                        }
                                        $qty_finger2=0;
                                        if($qty_finger==0){
                                            $text2="select checktime as masuk from checkinout left join userinfo on userinfo.userid=checkinout.userid where userinfo.badgenumber='$badge' and checktime>='$ncdateoutdown' and checktime<='$ncdateoutup' order by checktime desc";
                                            $qry2=mysqli_query($host,$text2)or die(mysqli_error($host));
                                            $qty_finger2=mysqli_num_rows($qry2);
                                            while($row2=mysqli_fetch_array($qry2)){
                                                if($row2['masuk']>=$ncdatein)$work_status='71';
                                            }
                                        }
                                    //End Check Actual Finger
                                    //Check Actual EMS (Manualcheck)
                                        $masuk=DB::table('tb_checktimes')
                                        ->where('NIK',$NIK)->where('checktime','>=',$ncdateindown)->where('checktime','<=',$ncdateinup)
                                        ->orderby('checktime','desc')
                                        ->get();
                                        $Manual=0;
                                        foreach($masuk as $row){
                                            if($row->checktime>=$ncdatein)$work_status='72';
                                            elseif($plan_work>0) $work_status='52';
                                            $Manual=$row->checktime;
                                        }
                                    //End Check Actual EMS (Manualcheck)
                                    \Log::info('Actual-CheckTime: '.$dtf->nama_karyawan.' '.$plan_work.' : '.$check_in.' ~ '.$check_out.' Finger: '.$qty_finger.' ~ '.$qty_finger2.'; Manual: '.$Manual.' Status : '.$work_status);

                                }

                            }
                            //Disini

                            $tb_work_entries=DB::table('tb_work_entries')->where('id_employee',$dt->id_employee)->where('periode',$data->periode)->where('plan_actual','actual')->update([
                                $column=>$work_status
                            ]);
                            if($tb_work_entries){
                                $no3++;
                                \Log::info("Actual-".$column.": ".$no3." ".$dt->department." ".$dt->nama_karyawan." ".$work_status);
                            }

                        }

                    }
                }
                \Log::info('Actual-'.$column.": Finish ".$qty_cek_act);
            
            // End Update WE-Actual
        }
        \Log::info("Update Actual WE from Finger Finish: ".$no3." Record");
    }
    function updateActualOne(Request $data){
        $now=date('Y-m-d H:i:s');
        $admin=Auth::user()->name;
        if (request()->user()->hasRole('root')||request()->user()->hasRole('hr_access')){$status=1;}else {$status=0;}
        $hasil=$data->idworkentry."#".$data->idcolumn."#".$data->entrycode."#".$data->remark;
        $admin=Auth::user()->name;
        $cek=DB::table('tb_work_checktime')->where('id_work_entry',$data->idworkentry)->where('id_column',$data->idcolumn)->count();
        if($cek==0){
            $simpan=DB::table('tb_work_checktime')->insert([
                'id_work_entry'=>$data->idworkentry,
                'id_column'=>$data->idcolumn,
                'entry_code'=>$data->entrycode,
                'remark'=>$data->remark,
                'status'=>$status,
                'admin'=>$admin
            ]);
        }else{
            $simpan=DB::table('tb_work_checktime')->where('id_work_entry',$data->idworkentry)->where('id_column',$data->idcolumn)->update([
                'entry_code'=>$data->entrycode,
                'remark'=>$data->remark,
                'status'=>$status,
                'admin'=>$admin
            ]);
        }
        if($status==1){
            $filed=$data->idcolumn;
            $update=DB::table('tb_work_entries')->where('id',$data->idworkentry)->update([
                $filed=>$data->entrycode,
                'admin'=>$admin,
                'updated_at'=>$now
            ]);
        }
        if($simpan)return 'Sukses';
        else return $hasil;
    }
    function draft($department,$periode){
        //if($department==0)$department="HRGA";
        $admin=Auth::user()->name;
        //Generate tb_work_contract from tb_employees EMS
        DB::table('tb_employees')->select('id','status','badgenumber')->orderBy('id')->chunkById(500,function($employees){
            foreach($employees as $employee){
                if($employee->status==0||$employee->badgenumber=='000000000'){
                    DB::table('tb_work_contract')->where('id_employee',$employee->id)->update(['isactive'=>'0']);
                }
            }
        });
        //Add to tb_work_entries
        if($periode==0)$periode=date('Y-m');
        $min=1;

        $tb_work_entries_draft=$this->tb_work_entries_draft($department,$periode);
        $work_time_ids=[];
        foreach($tb_work_entries_draft as $entry){
            for($day=1;$day<=31;$day++){
                $column='D'.str_pad($day,2,'0',STR_PAD_LEFT);
                if($entry->$column>0){
                    $work_time_ids[]=$entry->$column;
                }
            }
        }
        $work_time_lookup=DB::table('tb_work_time')->whereIn('id',array_unique($work_time_ids))->get()->keyBy('id');
        $tb_work_shift=DB::table('tb_work_shift')->get();
        $tb_department=$this->tb_department();

        if (request()->user()->hasRole('root')||request()->user()->hasRole('tms')||request()->user()->hasRole('admin_department')){
            return view('page/tms/employee_shift_draft',['tb_work_entries_draft'=>$tb_work_entries_draft,'work_time_lookup'=>$work_time_lookup,'tb_work_shift'=>$tb_work_shift,'tb_department'=>$tb_department,'department'=>$department,'periode'=>$periode,'site'=>$this->site,'menu'=>'tms','juduls'=>'Work Shedule','subjudul'=>'darft']);
        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }
    public function tb_work_entries_draft($department,$periode){
        $tb_department=$this->tb_department();

        $tabel=DB::table('tb_work_entries')
        ->leftjoin('tb_work_contract','tb_work_contract.id_employee','=','tb_work_entries.id_employee')
        ->where('tb_work_contract.is_draft','1')
        ->where('tb_work_entries.plan_actual','plan')
        ->where('tb_work_entries.periode',$periode)
        ->where('tb_work_contract.isactive','1');
        $tabel=$tabel->orderBy('tb_work_entries.id','asc')->paginate(100,['tb_work_entries.*','tb_work_contract.NIK','tb_work_contract.PIN','tb_work_contract.nama_karyawan','tb_work_contract.department','tb_work_contract.jabatan','tb_work_contract.id as id_contract','tb_work_contract.id_work_shift','tb_work_contract.suggest_ws']);
        return $tabel;
    }
    function draftGet($department,$periode){
        $admin=Auth::user()->name;
        //Generate tb_work_contract from tb_employees EMS
        $tb_employee=DB::table('tb_employees')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->where('status','1')->where('badgenumber','<>','000000000');
        if($department!=0){
            $tb_employee=$tb_employee->where('tb_departments.dept_code',$department);
        }
        $tb_employee=$tb_employee->get(['tb_employees.*','tb_departments.dept_code','tb_positions.position_name','tb_positions.position_index']);
        $proses=1;
        if($proses==1){
            foreach($tb_employee as $dt){
                if($dt->position_id=='19')$category="Magang";
                else $category="SAI";
                $cek=DB::table('tb_work_contract')->where('id_employee',$dt->id)->count();
                if($cek==0){
                    $add=DB::table('tb_work_contract')->insert([
                        'id_employee'=>$dt->id,
                        'NIK'=>$dt->NIK,
                        'nama_karyawan'=>$dt->employee_name,
                        'department'=>$dt->dept_code,
                        'jabatan'=>$dt->position_name,
                        'PIN'=>$dt->badgenumber,
                        'admin'=>$admin,
                        'position_index'=>$dt->position_index,
                        'category'=>$category,
                    ]);
                }else{
                    $add=DB::table('tb_work_contract')->where('id_employee',$dt->id)->update([
                        'NIK'=>$dt->NIK,
                        'nama_karyawan'=>$dt->employee_name,
                        //'department'=>$dt->dept_code,
                        'jabatan'=>$dt->position_name,
                        'PIN'=>$dt->badgenumber,
                        'position_index'=>$dt->position_index,
                        'category'=>$category,
                        'admin'=>$admin
                    ]);
                }
            }
        }
        //Add to tb_work_entries
        if($periode==0)$periode=date('Y-m');
        if($department!=0){
            $tb_work_contract=DB::table('tb_work_contract')->where('is_draft','1')->where('department',$department)->get();
        }
        else{
            $tb_work_contract=DB::table('tb_work_contract')->where('is_draft','1')->get();
        }
        $min=1;
        if($proses==1){
            foreach($tb_work_contract as $dt){
                $qty_work_entries=DB::table('tb_work_entries')
                ->where('periode',$periode)
                ->where('id_employee',$dt->id_employee)
                ->count();
                if($min>$qty_work_entries)$min=$qty_work_entries;
                if($qty_work_entries==0){
                    $tb_work_entries=DB::table('tb_work_entries')->insert([
                        'id_employee'=>$dt->id_employee,
                        'periode'=>$periode,
                        'plan_actual'=>'plan'
                    ]);
                    $tb_work_entries=DB::table('tb_work_entries')->insert([
                        'id_employee'=>$dt->id_employee,
                        'periode'=>$periode,
                        'plan_actual'=>'actual'
                    ]);
                }
            }
        }
        return redirect()->back();
    }
    function saveShift(Request $data){
        $konten="";
        $update=DB::table('tb_work_contract')->where('id',$data->id)->update([
            'id_work_shift'=>$data->idworkshift,
            'is_draft'=>'0'
        ]);
        if($update)$konten='Sukses';
        return $konten;
    }
    function updatesPlanTMSEmployee($department,$periode,$id_employee){
        $kalendar=CAL_GREGORIAN;
        if (request()->user()->hasRole('root')||request()->user()->hasRole('tms')||request()->user()->hasRole('admin_department')){
            $start=date('Y-m-d',strtotime($periode.'-01'));
            $thn=date('Y',strtotime($periode.'-01'));
            $bln=date('m',strtotime($periode.'-01'));
            $hariakhir=cal_days_in_month($kalendar,$bln,$thn);

            for($a=1;$a<=$hariakhir;$a++){
                if(strlen($a)==1)$b='0'.$a;
                else $b=$a;
                $Tgl=date('Y-m-d',strtotime($periode.'-'.$b));
                $i=date('d',strtotime($Tgl));
                if(strlen($i)==1)$j='D0'.$i;
                else $j='D'.$i;
                $cek=$this->qty_work_entry_employee($department,$Tgl,$id_employee);
                Log::info($Tgl.':'.$cek);      
                //return $cek;
                $cek_libur=DB::table('tb_freedays')->where('date_off',$Tgl)->where('category','Holiday')->count();
                if($cek>0&&$cek_libur==0){
                    $tb_work_contract=$this->tb_work_contract_employee($id_employee);
                    foreach($tb_work_contract as $dt){
                        $tgl1 = new DateTime($dt->start_implement);
                        $tgl2 = new DateTime($Tgl);
                        $diffdays = $tgl2->diff($tgl1)->days;
                        $cycle=$dt->cycle_day;
                        $diffcycle=Floor($diffdays/$cycle);
                        $modcycle=$diffdays%$cycle;
                        $modcycle++;

                        $tb_work_cycle=$this->tb_work_cycle($dt->id_work_group,$modcycle);
                        //return $tb_work_cycle;
                        foreach($tb_work_cycle as $dt2){
                            $plan_work=$dt2->id_work_time;
                            Log::info($plan_work);
                            $tb_work_entries=DB::table('tb_work_entries')->where('id_employee',$dt->id_employee)->where('periode',$periode)->where('plan_actual','plan')->update([
                                $j=>$plan_work
                            ]);
                        }
                    }
                }
                
            }
            return redirect()->back()->with(['info'=>'Success Updated']);
        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }
    public function qty_work_entry_employee($department,$Tgl,$id_employee){
        $i=date('d',strtotime($Tgl));
        $j='D'.$i;
        $periode=date('Y-m',strtotime($Tgl));
        $tabel=DB::table('tb_work_entries')
        ->leftjoin('tb_work_contract','tb_work_contract.id_employee','=','tb_work_entries.id_employee')
        ->where('periode',$periode)->where('tb_work_entries.id_employee',$id_employee)
        ->where('plan_actual','plan')
        ->count();
        return $tabel;
    }
    public function tb_work_contract_employee($id_employee){
        $tabel=DB::table('tb_work_contract')
        ->leftjoin('tb_work_shift','tb_work_shift.id','=','tb_work_contract.id_work_shift')
        ->leftjoin('tb_work_group','tb_work_group.id','=','tb_work_shift.id_work_group')
        ->where('tb_work_contract.isactive','1')->where('id_employee',$id_employee)
        ->get(['tb_work_contract.*','tb_work_shift.id_work_group','tb_work_shift.start_implement','tb_work_group.cycle_day']);
        return $tabel;
    }
    public function tb_work_cycle($id_work_group,$days){
        $tabel=DB::table('tb_work_cycle')
        ->leftjoin('tb_work_time','tb_work_time.id','=','tb_work_cycle.id_work_time')
        ->where('tb_work_cycle.id_work_group',$id_work_group)
        ->where('tb_work_cycle.days',$days)
        ->get();
        return $tabel;
    }
    function planTMS($department,$periode,$shift,$group){
        if($periode==0)$periode=date('Y-m');
        $subjudul=date('F Y',strtotime($periode.'-01'));
        $Tgl=date('Y-m-d',strtotime($periode.'-01'));
        $kalendar=CAL_GREGORIAN;
        $thn=date('Y',strtotime($periode.'-01'));
        $bln=date('m',strtotime($periode.'-01'));
        $hariakhir=cal_days_in_month($kalendar,$bln,$thn);
        $hari_akhir='D'.$hariakhir;

        $tb_work_time=$this->tb_work_times();
        $work_time_lookup=$tb_work_time->keyBy('id');
        $mulai=date('Y-m-d',strtotime($periode.'-01'));
        $tb_department=$this->tb_department();
        $tb_work_entries=$this->tb_work_entries($department,$periode,$group);
        $tb_work_shift=DB::table('tb_work_shift')->get();
        $shift_code='All Group';
        foreach($tb_work_shift as $dt){
            if($dt->id==$group)$shift_code=$dt->shift_code;
        }
        $no=0;
        foreach($tb_work_entries as $dt){
            $no++;
        }
        if (request()->user()->hasRole('root')||request()->user()->hasRole('tms')||request()->user()->hasRole('admin_department')){
            return view('page/tms/employee_shift_plan',['tb_work_entries'=>$tb_work_entries,'tb_department'=>$tb_department,'department'=>$department,'periode'=>$periode,'mulai'=>$mulai,'shift'=>$shift,'group'=>$group,'shift_code'=>$shift_code,'tb_work_time'=>$tb_work_time,'work_time_lookup'=>$work_time_lookup,'tb_work_shift'=>$tb_work_shift,'jumlah'=>$no,'site'=>$this->site,'menu'=>'tms','juduls'=>'Work Shedule','subjudul'=>$subjudul]);
        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }
    public function tb_work_entries($department,$periode,$group){
        $tb_department=$this->tb_department();

        $tabel=DB::table('tb_work_entries')
        ->leftjoin('tb_work_contract','tb_work_contract.id_employee','=','tb_work_entries.id_employee')
        ->leftjoin('tb_work_shift','tb_work_shift.id','=','tb_work_contract.id_work_shift')
        ->where('tb_work_entries.periode',$periode)
        ->where('tb_work_contract.is_draft','0')
        ->where('tb_work_contract.isactive','1');
        if($department!=0&&(request()->user()->hasRole('root')||request()->user()->hasRole('tms'))){
            $tabel=$tabel->where('tb_work_contract.department',$department);
            if($group!=0)$tabel=$tabel->where('tb_work_contract.id_work_shift',$group);
        }else{
            $tabel=$tabel->where(function($query) use ($tb_department,$group){
                $query->where(function($departmentQuery) use ($group){
                    $departmentQuery->where('tb_work_contract.department','0');
                    if($group!=0)$departmentQuery->where('tb_work_contract.id_work_shift','0');
                });
                foreach($tb_department as $dt){
                    $query->orWhere(function($departmentQuery) use ($dt,$group){
                        $departmentQuery->where('tb_work_contract.department',$dt->dept_code);
                        if($group!=0)$departmentQuery->where('tb_work_contract.id_work_shift',$group);
                    });
                }
            });
        }
        $tabel=$tabel->orderby('tb_work_entries.id','asc')->orderby('plan_actual','desc')
        ->get(['tb_work_entries.*','tb_work_contract.NIK','tb_work_contract.PIN','tb_work_contract.nama_karyawan','tb_work_contract.department','tb_work_contract.jabatan','tb_work_contract.id as id_contract','tb_work_contract.id_work_shift','tb_work_shift.shift_code','tb_work_shift.working_perweek','tb_work_contract.suggest_ws','tb_work_contract.position_index']);
        //return $periode;
        return $tabel;
    }
    function updatePlanOne(Request $data){
        if(strlen($data->kolom)==1)$j='D0'.$data->kolom;
        else $j='D'.$data->kolom;
        $update=DB::table('tb_work_entries')->where('id',$data->idworkentry)->update([
            $j=>$data->idshift
        ]);
        if($update)return 'Sukses';
    }
    function dailyTMS($department,$tanggal,$code){
        $tb_department=$this->tb_department();
        if($tanggal==0)$tanggal=date('Y-m-d');
        $proses=$this->reconsileAll($tanggal);
        $periode=date('Y-m',strtotime($tanggal));
        $day=date('d',strtotime($tanggal));
        $kolom="D".$day;
        $subjudul=date('d F Y',strtotime($tanggal));

        if($department==0){
            $tb_work_entry=DB::table('tb_work_entries')
            ->leftjoin('tb_work_contract','tb_work_contract.id_employee','=','tb_work_entries.id_employee')
            ->select($kolom,DB::raw('count(*) as employee_count, '.$kolom))
            ->where('tb_work_contract.isactive','1')
            ->where('is_draft','0')
            ->where('daily_show','1')
            ->groupby($kolom);
        }else{
            $tb_work_entry=DB::table('tb_work_entries')
            ->leftjoin('tb_work_contract','tb_work_contract.id_employee','=','tb_work_entries.id_employee')
            ->select($kolom,'department',DB::raw('count(*) as employee_count, '.$kolom))
            ->where('tb_work_contract.isactive','1')
            ->where('is_draft','0')
            ->where('daily_show','1')
            ->groupby([$kolom,'department']);
            $tb_work_entry=$tb_work_entry->where('department',$department);
        }
        //if($department>0)$tb_work_entry=$tb_work_entry->where('department',$department);
        $tb_work_entry=$tb_work_entry->where('periode',$periode)->where('plan_actual','actual')->where('isactive','1')->where('is_draft','0')->get();
        $tb_work_code=DB::table('tb_work_code')->where('source_check','<>','Other')->get();
        //return $tb_work_entry; 
        
        //$group=0;
        //$tb_work_entries=$this->tb_work_entries($department,$periode,$group);

        $tb_work_entries = DB::table('tb_work_entries')
        ->leftJoin('tb_work_contract', 'tb_work_contract.id_employee', '=', 'tb_work_entries.id_employee')
        ->where('tb_work_entries.periode', $periode)
        ->where('tb_work_entries.plan_actual', 'actual')
        ->where('is_draft','0')
        ->where('daily_show','1')
        ->where('tb_work_contract.isactive','1');
        if($department>0)$tb_work_entries=$tb_work_entries->where('department',$department);
        $tb_work_entries=$tb_work_entries->get(['tb_work_entries.*','tb_work_contract.NIK','tb_work_contract.PIN','tb_work_contract.nama_karyawan','tb_work_contract.department','tb_work_contract.jabatan','tb_work_contract.id as id_contract','tb_work_contract.id_work_shift','tb_work_contract.suggest_ws']);

        $work_code_lookup=DB::table('tb_work_code')->get()->keyBy('work_code');
        $entry_ids=$tb_work_entries->pluck('id')->unique();
        $daily_checktime_lookup=collect();
        if($entry_ids->isNotEmpty()){
            $daily_checktime_lookup=DB::table('tb_work_checktime')
                ->whereIn('id_work_entry',$entry_ids)
                ->where('id_column',$kolom)
                ->get()
                ->keyBy('id_work_entry');
        }

        $limit_date=DB::table('tb_utilities')->where('id','24')->where('status','1')->value('limit_date');
        if($limit_date!=''&&$tanggal<=$limit_date)$lock_status=1;
        else $lock_status=0;
        $data['capture']=DB::table('tb_log_capture')->where('capture_date',$tanggal)->count();
        if (request()->user()->hasRole('root')||request()->user()->hasRole('tms')||request()->user()->hasRole('admin_department')){
            return view('page/tms/daily_presence',['tb_department'=>$tb_department,'tb_work_code'=>$tb_work_code,'tb_work_entry'=>$tb_work_entry,'tb_work_entries'=>$tb_work_entries,'work_code_lookup'=>$work_code_lookup,'daily_checktime_lookup'=>$daily_checktime_lookup,'department'=>$department,'periode'=>$periode,'tanggal'=>$tanggal,'kolom'=>$kolom,'data'=>$data,'code'=>$code,'site'=>$this->site,'menu'=>'tms','juduls'=>'Work Entry','subjudul'=>$subjudul,'lock_status'=>$lock_status]);
        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }
    function reconsile(Request $data){
        $pesan="";
        $info="";
        if($data->source=='leave'){
            $pesan="Form belum dibuat";
            //$cek=DB::table('tb_leaves')->where('category','annual')->where('id_employee',$data->id_employee)->where('start_leave','<=',$data->tgl)->where('finish_leave','>=',$data->tgl)->get();
            $cek=DB::table('tb_leaves')->whereIn('category', ['annual', 'special'])->where('id_employee',$data->id_employee)->where('start_leave','<=',$data->tgl)->where('finish_leave','>=',$data->tgl)->get();
            foreach($cek as $dt){
                if($dt->status_approved!=1)$pesan="Waiting Approve 1";
                elseif($dt->status_approved2!=1&&$dt->approved2!=0)$pesan="Waiting Approve 2";
                elseif($dt->status_legalized!=1)$pesan="Waiting Legalize by HR";
                elseif($dt->status_legalized==1)$pesan="Approval Completed";
            }
            $code='53';
        }elseif($data->source=='skd'){
            $pesan="Form belum diupload";
            $cek=DB::table('tb_leaves')->where('category','docter')->where('id_employee',$data->id_employee)->where('start_leave','<=',$data->tgl)->where('finish_leave','>=',$data->tgl)->get();
            foreach($cek as$dt){
                if($dt->status_approved!=1)$pesan="Waiting Approve 1";
                elseif($dt->status_approved2!=1&&$dt->approved2!=0)$pesan="Waiting Approve 2";
                elseif($dt->status_legalized!=1)$pesan="Waiting Legalize by HR";
                elseif($dt->status_legalized==1)$pesan="Approval Completed";
            }
            $code='54';
        }elseif($data->source=='ijin'){
            $pesan="Form belum dibuat";
            $cek=DB::table('tb_izins')->where('category','A')->where('id_employee',$data->id_employee)->where('apply_date',$data->tgl)->get();
            foreach($cek as$dt){
                if($dt->status_disetujui!=1)$pesan="Waiting Approve";
                elseif($dt->status_personalia!=1)$pesan="Waiting Legalize by HR";
                elseif($dt->status_personalia==1)$pesan="Approval Completed";
            }
            $code='55';
        }elseif($data->source=='permit_b'){
            $pesan="Form belum dibuat";
            $cek=DB::table('tb_izins')->where('category','B')->where('id_employee',$data->id_employee)->where('apply_date',$data->tgl)->get();
            foreach($cek as$dt){
                if($dt->status_disetujui!=1)$pesan="Waiting Approve";
                elseif($dt->status_personalia!=1)$pesan="Waiting Legalize by HR";
                elseif($dt->status_personalia==1)$pesan="Approval Completed";
            }
            $code='731';
            $info="Form B";
        }elseif($data->source=='permit_c'){
            $pesan="Form belum dibuat";
            $cek=DB::table('tb_izins')->where('category','C')->where('id_employee',$data->id_employee)->where('apply_date',$data->tgl)->get();
            foreach($cek as$dt){
                if($dt->status_disetujui!=1)$pesan="Waiting Approve";
                elseif($dt->status_personalia!=1)$pesan="Waiting Legalize by HR";
                elseif($dt->status_personalia==1)$pesan="Approval Completed";
            }
            $code='732';
            $info="Form C";
        }elseif($data->source=='permit_d'){
            $pesan="Form belum dibuat";
            $cek=DB::table('tb_izins')->where('category','D')->where('id_employee',$data->id_employee)->where('apply_date',$data->tgl)->get();
            foreach($cek as$dt){
                if($dt->status_disetujui!=1)$pesan="Waiting Approve";
                elseif($dt->status_personalia!=1)$pesan="Waiting Legalize by HR";
                elseif($dt->status_personalia==1)$pesan="Approval Completed";
            }
            $code='733';
            $info="Form D";
        }
        if($pesan!=''){
            $admin=Auth::user()->name;
            $day=date('d',strtotime($data->tgl));
            $id_column='D'.$day;
            $cek=DB::table('tb_work_checktime')->where('id_work_entry',$data->idwe)->where('id_column',$id_column)->count();
            if($cek==0){
                $simpan=DB::table('tb_work_checktime')->insert([
                    'id_work_entry'=>$data->idwe,
                    'id_column'=>$id_column,
                    'entry_code'=>$code,
                    'remark'=>$pesan,
                    'status'=>0,
                    'admin'=>$admin,
                    'info'=>$info,
                ]);
            }else{
                $simpan=DB::table('tb_work_checktime')->where('id_work_entry',$data->idwe)->where('id_column',$id_column)->update([
                    'entry_code'=>$code,
                    'remark'=>$pesan,
                    'status'=>0,
                    'admin'=>$admin,
                    'info'=>$info,
                ]);
            }
            if($pesan=='Approval Completed'){
                $update_we=DB::table('tb_work_entries')->where('id',$data->idwe)->update([
                    $id_column=>$code
                ]);
            }
            // if($simpan)return $code;
            // else return $cek;
            return $code;
        }
    }
    function tmsUpdateOne($periode,$id_employee,$tgl){
        $admin=Auth::user()->name;
        $day=date('d',strtotime($tgl));
        $kolom="D".$day;
        $column=$kolom;
        $Tgl=$tgl;
        $now=date('Y-m-d H:i:s');

        \Log::info('Manual Update by '.$admin);

        $tb_focus=DB::table('tb_work_entries')
        ->leftjoin('tb_work_contract','tb_work_contract.id_employee','=','tb_work_entries.id_employee')
        ->where('tb_work_contract.isactive','1')
        ->where('periode',$periode)
        ->where('tb_work_entries.id_employee',$id_employee)
        ->where('plan_actual','actual')
        ->get(['tb_work_entries.*','tb_work_contract.PIN','tb_work_contract.NIK','tb_work_contract.department','tb_work_contract.nama_karyawan']);
        
        foreach($tb_focus as $dtf){
            \Log::info('Manual Processing: '.$dtf->nama_karyawan);
            $id_employee_focus=$dtf->id_employee;

            //$tb_work_entries=$this->tb_work_entries($department,$periode,0);
            $tb_work_entries=DB::table('tb_work_entries')
            ->leftjoin('tb_work_contract','tb_work_contract.id_employee','=','tb_work_entries.id_employee')
            ->where('periode',$periode)
            ->where('plan_actual','plan')
            ->where('tb_work_entries.id_employee',$id_employee_focus)
            ->orderby('tb_work_entries.id','asc')
            ->get(['tb_work_entries.*','tb_work_contract.PIN','tb_work_contract.NIK','tb_work_contract.department','tb_work_contract.nama_karyawan']);
            foreach($tb_work_entries as $dt){
                $plan_work=$dt->$column;
                \Log::info('Manual CheckPlan: '.$dtf->nama_karyawan.' '.$plan_work);
                //$tb_work_time=$this->tb_work_time($plan_work);
                $tb_work_time=DB::table('tb_work_time')->where('id',$plan_work)->get();
                $work_status=0;
                foreach($tb_work_time as $dt2){
                    $advance=$dt2->is_advance;
                    if($dt2->is_advance==0)$tanggal=$Tgl;
                    else $tanggal=date('Y-m-d',strtotime('-1 days',strtotime($Tgl)));
                    if($plan_work>0){
                        $check_in=$tanggal.' '.$dt2->check_in;
                        $check_out=$Tgl.' '.$dt2->check_out;

                        //Return Checkin Range
                        if(isset($check_in)&&$check_in!=''&&$check_in!=$check_out){
                            $ncdatein=$check_in;
                            $ncdateindown= date('Y-m-d H:i:s',strtotime('-3 hours',strtotime($ncdatein)));
                            $ncdateinup= date('Y-m-d H:i:s',strtotime('+5 hours',strtotime($ncdatein)));
                        }else{
                            $ncdateindown='';
                            $ncdateinup='';
                        }
                        //Return Checkout Range
                        if(isset($check_out)&&$check_out!=''&&$check_in!=$check_out){
                            $ncdateout=$check_out;
                            $ncdateoutdown= date('Y-m-d H:i:s',strtotime('-2 hours',strtotime($ncdateout)));
                            $ncdateoutup= date('Y-m-d H:i:s',strtotime('+5 hours',strtotime($ncdateout)));
                        }else{
                            $ncdateoutdown='';
                            $ncdateoutup='';
                        }
                        //Read Finger Print
                        $PIN=$dt->PIN;
                        $NIK=$dt->NIK;

                        $lenbadge=strlen($PIN);
                        $nullbadge=9-$lenbadge;
                        $p='';
                        for($q=1;$q<=$nullbadge;$q++){
                            $p.='0';
                        }
                        $badge=$p.$PIN;
                        
                        if($check_in<=$now) $work_status='99';
                        else $work_status='98';
                        //Check Actual Finger
                            $finger_checkins=DB::table('tb_iclock')
                                ->where('badgenumber',$badge)
                                ->where('checktime','>=',$ncdateindown)
                                ->where('checktime','<=',$ncdateinup)
                                ->orderBy('checktime','desc')
                                ->get(['checktime']);
                            $qty_finger=$finger_checkins->count();
                            if($qty_finger>0){
                                foreach($finger_checkins as $row){
                                    if($row->checktime>=$ncdatein)$work_status='71';
                                    elseif($row->checktime>0) $work_status='51';
                                }
                            }
                            $qty_finger2=0;
                            if($qty_finger==0){
                                $finger_checkouts=DB::table('tb_iclock')
                                    ->where('badgenumber',$badge)
                                    ->where('checktime','>=',$ncdateoutdown)
                                    ->where('checktime','<=',$ncdateoutup)
                                    ->orderBy('checktime','desc')
                                    ->get(['checktime']);
                                $qty_finger2=$finger_checkouts->count();
                                foreach($finger_checkouts as $row2){
                                    if($row2->checktime>=$ncdatein)$work_status='71';
                                }
                            }
                        //End Check Actual Finger
                        //Check Actual EMS (Manualcheck)
                            $masuk=DB::table('tb_checktimes')
                            ->where('NIK',$NIK)->where('checktime','>=',$ncdateindown)->where('checktime','<=',$ncdateinup)
                            ->orderby('checktime','desc')
                            ->get();
                            $Manual=0;
                            foreach($masuk as $row){
                                if($row->checktime>=$ncdatein)$work_status='72';
                                elseif($plan_work>0) $work_status='52';
                                $Manual=$row->checktime;
                            }
                        //End Check Actual EMS (Manualcheck)
                        \Log::info('Actual-CheckTime: '.$dtf->nama_karyawan.' '.$plan_work.' : '.$check_in.' ~ '.$check_out.' Finger: '.$qty_finger.' ~ '.$qty_finger2.'; Manual: '.$Manual.' Status : '.$work_status);

                    }

                }
                //Disini

                $tb_work_entries=DB::table('tb_work_entries')->where('id_employee',$dt->id_employee)->where('periode',$periode)->where('plan_actual','actual')->update([
                    $column=>$work_status
                ]);
                if($tb_work_entries){
                    \Log::info("Manual Updated ".$column.": ".$dt->department." ".$dt->nama_karyawan." ".$work_status);
                }

            }

        }
        \Log::info('Manual Update by '.$admin.' Finish');
        return redirect()->back();

    }
    function absensiRate($periode,$dept){
        if($periode==0)$periode=date('Y-m');
        $admin=Auth::user()->name;
        date_default_timezone_set("Asia/Jakarta");
        $kalendar=CAL_GREGORIAN;
        $sekarang=date('Y-m-d H:i:s');
        $Tgl=date('Y-m-d');

        $data_a=explode('-',$periode);
        $thn=$data_a[0];
        $bln=$data_a[1];

        $hariakhir=cal_days_in_month($kalendar,$bln,$thn);

        $Tglawal=date('Y-m-d',strtotime($thn.'-'.$bln.'-01'));
        $Tglakhir=date('Y-m-d',strtotime($thn.'-'.$bln.'-'.$hariakhir));
        $periode=date('Y-m',strtotime($Tglawal));
        
        $email=Auth::user()->email;
        $tb_email=DB::table('tb_emails')->where('email_address',$email)->get();
        foreach($tb_email as $dt){
            $id_user=$dt->id_employee;
            $tb_admin=DB::table('tb_admins')->leftjoin('tb_departments','tb_departments.id','=','tb_admins.dept_id')->where('id_employee',$id_user)->where('tb_departments.isDelete',0)->orderby('tb_departments.dept_code','asc')->get(['tb_admins.*','tb_departments.dept_code','tb_departments.dept_name']);
        }

        if($dept==0){
            $tb_absen=DB::table('tb_absensi_rate_kumulatif')
            ->where([['tb_absensi_rate_kumulatif.periode',$periode],['tb_absensi_rate_kumulatif.dept_id','0']]);
            foreach($tb_admin as $dt){
                $tb_absen=$tb_absen->orwhere([['tb_absensi_rate_kumulatif.periode',$periode],['tb_absensi_rate_kumulatif.dept_id',$dt->dept_id]]);
            }
            $tb_absen=$tb_absen->orderby('absensi_rate','asc')->orderby('hari_kerja','asc')->get();
            $tb_absen2=DB::table('tb_absensi_rate_kumulatif')
            ->where('tb_absensi_rate_kumulatif.periode',$periode);
            foreach($tb_admin as $dt){
                $tb_absen2=$tb_absen2->where('tb_absensi_rate_kumulatif.dept_id','<>',$dt->dept_id);
            }
            $tb_absen2=$tb_absen2->orderby('absensi_rate','asc')->orderby('hari_kerja','asc')->get();
            //return $tb_absen;
            return view('page/user/m_absency/absencysummaries',['periode'=>$periode,'thn'=>$thn,'bln'=>$bln,'tb_absen'=>$tb_absen,'tb_absen2'=>$tb_absen2,'cekstatus'=>'','menu'=>'tms']);
        }else{
            $validasi=DB::table('tb_admins')->where('id_employee',$id_user);
            if($dept!='99'){
                $validasi=$validasi->where('dept_id',$dept);
            }
            $validasi=$validasi->count();
            $dept_name='';
            if($validasi){
                $tb_dept=DB::table('tb_departments')->where('id',$dept)->get();
                foreach($tb_dept as $dt){
                    $dept_name=$dt->dept_name;
                }

                $tb_absen=DB::table('tb_absensi_rate');
                if($dept!=99){
                    $tb_absen=$tb_absen->where('dept_id',$dept);
                }
                $tb_absen=$tb_absen->where('tb_absensi_rate.periode',$periode)->where('hari_kerja','5')->orderby('employee_name','asc')->get();
                return view('page/user/m_absency/absencysummary',['Tgl_awal'=>$Tglawal,'Tgl_akhir'=>$Tglakhir,'dept_id'=>$dept,'dept_name'=>$dept_name,'periode'=>$periode,'thn'=>$thn,'bln'=>$bln,'tb_absen'=>$tb_absen,'hari_kerja'=>'5','menu'=>'tms']);
            }else{
                return redirect()->back();
            }
        }
    }
    function absensiRateDetail($periode){
        if (request()->user()->hasRole('root')||request()->user()->hasRole('hr_access')){

            if($periode==0)$periode=date('Y-m');
            $admin=Auth::user()->name;
            date_default_timezone_set("Asia/Jakarta");
            $kalendar=CAL_GREGORIAN;
            $sekarang=date('Y-m-d H:i:s');
            $Tgl=date('Y-m-d');

            $data_a=explode('-',$periode);
            $thn=$data_a[0];
            $bln=$data_a[1];

            $hariakhir=cal_days_in_month($kalendar,$bln,$thn);

            $Tglawal=date('Y-m-d',strtotime($thn.'-'.$bln.'-01'));
            $Tglakhir=date('Y-m-d',strtotime($thn.'-'.$bln.'-'.$hariakhir));
            $periode=date('Y-m',strtotime($Tglawal));

            $tb_absen=DB::table('tb_absensi_rate')
            ->select('id_employee','employee_name','NIK','present_plan','present_actual','present_rate','hour_plan','hour_actual','hour_rate','terlambat','terlambat_minutes','setengah_minutes','keluar_minutes','cuti','sakit','izin','alpa')
            ->where('tb_absensi_rate.periode',$periode)
            ->orderby('employee_name','asc')->get();
            $employee_ids=$tb_absen->pluck('id_employee')->unique();
            $leave_tooltips=collect();
            if($employee_ids->isNotEmpty()){
                $leave_tooltips=DB::table('tb_leaves')
                    ->whereIn('id_employee',$employee_ids)
                    ->where(function($query) use ($Tglawal,$Tglakhir){
                        $query->where(function($dateQuery) use ($Tglawal,$Tglakhir){
                            $dateQuery->where('start_leave','>=',$Tglawal)
                                ->where('start_leave','<=',$Tglakhir);
                        })->orWhere(function($dateQuery) use ($Tglawal,$Tglakhir){
                            $dateQuery->where('finish_leave','>=',$Tglawal)
                                ->where('finish_leave','<=',$Tglakhir);
                        });
                    })
                    ->whereIn('category',['annual','special'])
                    ->where('status_legalized',1)
                    ->get(['id_employee','leave_count','start_leave','finish_leave'])
                    ->groupBy('id_employee')
                    ->map(function($leaves,$id_employee){
                        return $leaves->reduce(function($tooltip,$leave){
                            return $tooltip.' ('.$leave->leave_count.' on '.$leave->start_leave.' ~ '.$leave->finish_leave.')';
                        },$id_employee.': ');
                    });
            }
            return view('page/user/m_absency/absencysummary_all',['Tgl_awal'=>$Tglawal,'Tgl_akhir'=>$Tglakhir,'periode'=>$periode,'thn'=>$thn,'bln'=>$bln,'tb_absen'=>$tb_absen,'leave_tooltips'=>$leave_tooltips,'hari_kerja'=>'5','menu'=>'tms']);
        }
    }
    function gagalFinger($start,$end){
        $today=date('Y-m-d');
        $periode=date('Y-m',strtotime($today));
        $next_periode=date('Y-m',strtotime('+31 days',strtotime($today)));
        if($start==0||$end==0){
            // $start=$periode.'-01';
            // $a=$next_periode.'-01';
            // $end=date('Y-m-d',strtotime('-1 days',strtotime($a)));
            $start=$today;
            $end=$today;
        }
        $data['start']=$start;
        $data['end']=$end;
        $data['tb1']=DB::table('tb_gagal_finger')
        ->leftjoin('tb_work_contract','tb_work_contract.NIK','=','tb_gagal_finger.NIK')
        ->where('tb_gagal_finger.tanggal','>=',$start)->where('tb_gagal_finger.tanggal','<=',$end)->get(['tb_gagal_finger.*','tb_work_contract.nama_karyawan','tb_work_contract.department','tb_work_contract.jabatan']);
        return view('page/tms/failed_finger',['data'=>$data,'site'=>$this->site,'menu'=>'tms','juduls'=>'Work Shedule','subjudul'=>'darft']);
    }
    function confirmFinger(Request $data){
        $now=date('Y-m-d H:i:s');
        $periode=date('Y-m',strtotime($now));
        $tgl=date('Y-m-d',strtotime($now));
        $admin=Auth::user()->name;
        $tb_checktime=DB::table('tb_checktimes')->where('id',$data->id_checktime)->get();
        foreach($tb_checktime as $dt){
            $draft=$dt->checktime_draft;
        }
        $jam=date('H:i',strtotime($draft));
        if($jam>='19:00')$code='520';
        else $code='52';
        $update_checktime=DB::table('tb_checktimes')->where('id',$data->id_checktime)->update([
            'checktime'=>$draft
        ]);
        $update_gagal_finger=DB::table('tb_gagal_finger')->where('id_checktime',$data->id_checktime)->update([
            'status'=>'1',
            'confirmed_at'=>$now,
            'confirmed_by'=>$admin
        ]);
        $tb2=DB::table('tb_gagal_finger')->leftjoin('tb_work_contract','tb_work_contract.NIK','=','tb_gagal_finger.NIK')->where('tb_gagal_finger.id_checktime',$data->id_checktime)->get(['tb_gagal_finger.*','tb_work_contract.id_employee']);
        foreach($tb2 as $dt2){
            $id_we=$dt2->id_we;
            $id_column=$dt2->id_column;
            $id_employee=$dt2->id_employee;
            
        }
        $update_we_checktime=DB::table('tb_work_checktime')->where('id_work_entry',$id_we)->where('id_column',$id_column)->update([
            'status'=>1,
        ]);
        if($update_we_checktime){
            $update_we=DB::table('tb_work_entries')->where('plan_actual','actual')->where('id',$id_we)->update([
                $id_column=>$code
            ]);
        }
        //return $id_we.' '.$id_column;
    }


    //View Group

    //Draft tb_work_contract
    function suggest($department,$periode){
        $admin=Auth::user()->name;
        //Generate tb_work_contract from tb_employees EMS
        $tb_employee=DB::table('tb_employees')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->where('status','1');
        if($department!=0){
            $tb_employee=$tb_employee->where('tb_departments.dept_code',$department);
        }
        $tb_employee=$tb_employee->get(['tb_employees.*','tb_departments.dept_code','tb_positions.position_name','tb_positions.position_index']);
        $now=date('Y-m-d');
        $tgl=date('Y-m-d',strtotime('-2 days',strtotime($now)));
        $start=date('Y-m-d H:i:s',strtotime($tgl.' 00:00:00'));
        $end=date('Y-m-d H:i:s',strtotime('+1 days',strtotime($start)));
        
        $tgl1=date('Y-m-d',strtotime('-7 days',strtotime($tgl)));
        $start1=date('Y-m-d H:i:s',strtotime('-7 days',strtotime($start)));
        $end1=date('Y-m-d H:i:s',strtotime('+1 days',strtotime($start1)));
        $weekday=date('w',strtotime($tgl));
        
        $co_day=DB::table('tb_work_cycle')
        ->leftjoin('tb_work_time','tb_work_time.id','=','tb_work_cycle.id_work_time')
        ->where('id_work_group','1')
        ->where('days',$weekday)
        ->get(['check_out']);
        foreach($co_day as $dt){
            $time1=$tgl.' '.$dt->check_out;
            $time1_bawah=date('Y-m-d H:i:s',strtotime('-2 hours',strtotime($time1)));
            $time1_atas=date('Y-m-d H:i:s',strtotime('+2 hours',strtotime($time1)));
            $time10=$tgl1.' '.$dt->check_out;
            $time10_bawah=date('Y-m-d H:i:s',strtotime('-2 hours',strtotime($time10)));
            $time10_atas=date('Y-m-d H:i:s',strtotime('+2 hours',strtotime($time10)));
        }

        $ci_night=DB::table('tb_work_cycle')
        ->leftjoin('tb_work_time','tb_work_time.id','=','tb_work_cycle.id_work_time')
        ->where('id_work_group','2')
        ->where('days',$weekday)
        ->get(['check_in']);
        foreach($ci_night as $dt){
            $time2=$tgl.' '.$dt->check_in;
            $time2_bawah=date('Y-m-d H:i:s',strtotime('-3 hours',strtotime($time2)));
            $time2_atas=date('Y-m-d H:i:s',strtotime('+1 hours',strtotime($time2)));
            $time20=$tgl1.' '.$dt->check_in;
            $time20_bawah=date('Y-m-d H:i:s',strtotime('-3 hours',strtotime($time20)));
            $time20_atas=date('Y-m-d H:i:s',strtotime('+1 hours',strtotime($time20)));
        }

        $proses=1;
        $no=0;
        $action=0;
        if($proses==1){
            foreach($tb_employee as $dt){
                $no++;
                $badgenumber=$dt->badgenumber;
                //if($badgenumber=='000009546'){
                    $tb_absen = DB::connection('fingerPrint')->table('checkinout')
                    ->leftJoin('userinfo', 'checkinout.userid', '=', 'userinfo.userid')
                    ->select(
                        'checkinout.userid', 'userinfo.name','userinfo.badgenumber','checkinout.checktime','checkinout.checktype','checkinout.WorkCode','checkinout.sensorid'
                    )
                    ->where('checkinout.checktime', '>=', $start)
                    ->where('checkinout.checktime', '<=', $end)
                    //->where('checkinout.userid',$PIN)
                    ->where('userinfo.badgenumber',$badgenumber)
                    ->orderby('checkinout.checktime','desc')
                    ->limit(1)
                    ->get();
    
                    $tb_absen1 = DB::connection('fingerPrint')->table('checkinout')
                    ->leftJoin('userinfo', 'checkinout.userid', '=', 'userinfo.userid')
                    ->select(
                        'checkinout.userid', 'userinfo.name','userinfo.badgenumber','checkinout.checktime','checkinout.checktype','checkinout.WorkCode','checkinout.sensorid'
                    )
                    ->where('checkinout.checktime', '>=', $start1)
                    ->where('checkinout.checktime', '<=', $end1)
                    //->where('checkinout.userid',$PIN)
                    ->where('userinfo.badgenumber',$badgenumber)
                    ->orderby('checkinout.checktime','desc')
                    ->limit(1)
                    ->get();
    
                    $week=0;
                    foreach($tb_absen as $dt2){
                        $checktime1=$dt2->checktime;
                        if($dt2->checktime>=$time1_bawah&&$dt2->checktime<=$time1_atas)$week="Pagi";
                        if($dt2->checktime>=$time2_bawah&&$dt2->checktime<=$time2_atas)$week="Malam";
                    }
    
                    $week1=0;
                    foreach($tb_absen1 as $dt2){
                        $checktime2=$dt2->checktime;
                        if($dt2->checktime>=$time10_bawah&&$dt2->checktime<=$time10_atas)$week1="Pagi";
                        if($dt2->checktime>=$time20_bawah&&$dt2->checktime<=$time20_atas)$week1="Malam";
                    }
                    
                    //Check Shift
                    $tb_work_shift=DB::table('tb_work_shift')
                    ->leftjoin('tb_work_group','tb_work_group.id','=','tb_work_shift.id_work_group')
                    ->where('tb_work_shift.id',2)->get(['tb_work_shift.start_implement','tb_work_group.cycle_day']);
                    foreach($tb_work_shift as $dt2)
                    $tgl1 = new DateTime($dt2->start_implement);
                    $tgl2 = new DateTime($tgl);
                    $diffdays = $tgl2->diff($tgl1)->days;
    
                    $cycle=$dt2->cycle_day;
                    $diffcycle=Floor($diffdays/$cycle);
                    $modcycle=$diffdays%$cycle;
                    $awal=$modcycle;
                    $modcycle++;
                    $tb_work_time=DB::table('tb_work_cycle')->where('id_work_group','2')->where('days',$modcycle)->get(['id_work_time']);
                    foreach($tb_work_time as $dt2){
                        if($dt2->id_work_time=='3')$shift="Malam";
                        else if($dt2->id_work_time=='1')$shift="Pagi";
                    }
                    //
                    $id_work_shift=0;
                    if($week=="Pagi"&&$week1=="Pagi")$id_work_shift=1;
                    else if($week==$shift)$id_work_shift=2;
                    else if($week1==$shift)$id_work_shift=3;
    
                    //$update=0;
                    if($id_work_shift>0){
                        $cek=DB::table('tb_work_contract')->where('id_employee',$dt->id)->count();
                        if($cek==0){
                            $add=DB::table('tb_work_contract')->insert([
                                'id_employee'=>$dt->id,
                                'NIK'=>$dt->NIK,
                                'nama_karyawan'=>$dt->employee_name,
                                'department'=>$dt->dept_code,
                                'jabatan'=>$dt->position_name,
                                'PIN'=>$dt->badgenumber,
                                'suggest_ws'=>$id_work_shift,
                                'position_index'=>$dt->position_index,
                                'admin'=>$admin
                            ]);
                        }else{
                            $add=DB::table('tb_work_contract')->where('id_employee',$dt->id)->update([
                                'NIK'=>$dt->NIK,
                                'nama_karyawan'=>$dt->employee_name,
                                //'department'=>$dt->dept_code,
                                'jabatan'=>$dt->position_name,
                                'PIN'=>$dt->badgenumber,
                                'suggest_ws'=>$id_work_shift,
                                'position_index'=>$dt->position_index,
                                'admin'=>$admin
                            ]);
                        }
                        $action++;
                    }
                //}
            }

        }

        $tb_employee=DB::table('tb_employees')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->where('status','1');
        if($department!=0){
            $tb_employee=$tb_employee->where('tb_departments.dept_code',$department);
        }
        $tb_employee_qty=$tb_employee->count();
        //return $time1_bawah.'#'.$checktime1.'#'.$time1_atas;
        //return $time2_bawah.'#'.$checktime1.'#'.$time2_atas;
        //return $time10_bawah.'#'.$checktime2.'#'.$time10_atas;
        //return $time20_bawah.'#'.$checktime2.'#'.$time20_atas;
        //return $week1.' & '.$week;
        //return $tb_employee_qty.' No '.$no.' Proses '.$action;
        return redirect()->back();
    }
    function suggestSave($department){
        $now=date('Y-m-d H:i:s');
        $tb_work_contract=DB::table('tb_work_contract')->where('department',$department)->where('is_draft','1')->get();
        foreach($tb_work_contract as $dt){
            $id_work_shift=$dt->suggest_ws;
            if($id_work_shift>0){
                $update=DB::table('tb_work_contract')->where('id',$dt->id)->update([
                    'id_work_shift'=>$id_work_shift,
                    'is_draft'=>'0',
                    'updated_at'=>$now,
                ]);
            }
        }
        return redirect()->back();
    }

    //View WE-Plan
    //Generate WE-Template
    public function setupTMS($department,$periode){
        $admin=Auth::user()->name;
        //Generate Work Entry by Shift Group
        if (request()->user()->hasRole('root')||request()->user()->hasRole('tms')){
            //Generate WE from tb_work_contract
            if($periode==0)$periode=date('Y-m');
            $tb_work_contract=$this->tb_work_contract($department);
            $min=1;
            foreach($tb_work_contract as $dt){
                $qty_work_entries=DB::table('tb_work_entries')
                ->where('periode',$periode)
                ->where('id_employee',$dt->id_employee)
                ->count();
                if($min>$qty_work_entries)$min=$qty_work_entries;
                if($qty_work_entries==0){
                    $tb_work_entries=DB::table('tb_work_entries')->insert([
                        'id_employee'=>$dt->id_employee,
                        'periode'=>$periode,
                        'plan_actual'=>'plan'
                    ]);
                    $tb_work_entries=DB::table('tb_work_entries')->insert([
                        'id_employee'=>$dt->id_employee,
                        'periode'=>$periode,
                        'plan_actual'=>'actual'
                    ]);
                }
            }
            return redirect()->back();
        }
    }
    //Generate WE-Plan Continuous (Auto from View WE-Plan)
    function updatesPlanTMS($department,$Tgl){
        $kalendar=CAL_GREGORIAN;
        if (request()->user()->hasRole('root')||request()->user()->hasRole('tms')||request()->user()->hasRole('admin_department')){
            $Today=date('Y-m-d');
            $thn=date('Y',strtotime($Today));
            $bln=date('m',strtotime($Today));
            $hariakhir=cal_days_in_month($kalendar,$bln,$thn);
            $hari_akhir=date('Y-m-d',strtotime($thn.'-'.$bln.'-'.$hariakhir));

            $periode=date('Y-m',strtotime($Tgl));
            $subjudul=date('F Y',strtotime($periode.'-01')).' Generate Schedule...';
            $i=date('d',strtotime($Tgl));
            if(strlen($i)==1)$j='D0'.$i;
            else $j='D'.$i;
            $cek=$this->qty_work_entry($department,$Tgl);

            $cek_libur=DB::table('tb_freedays')->where('date_off',$Tgl)->where('category','Holiday')->count();
            //return $cek;
            if($cek>0&&$cek_libur==0){
                $tb_work_contract=$this->tb_work_contract($department);
                foreach($tb_work_contract as $dt){
                    $tgl1 = new DateTime($dt->start_implement);
                    $tgl2 = new DateTime($Tgl);
                    $diffdays = $tgl2->diff($tgl1)->days;
                    $cycle=$dt->cycle_day;
                    $diffcycle=Floor($diffdays/$cycle);
                    $modcycle=$diffdays%$cycle;
                    $modcycle++;

                    $tb_work_cycle=$this->tb_work_cycle($dt->id_work_group,$modcycle);
                    //return $tb_work_cycle;
                    foreach($tb_work_cycle as $dt2){
                        $plan_work=$dt2->id_work_time;
                        $tb_work_entries=DB::table('tb_work_entries')->where('id_employee',$dt->id_employee)->where('periode',$periode)->where('plan_actual','plan')->update([
                            $j=>$plan_work
                        ]);
                    }
                }
            }

            $tb_department=DB::table('tb_departments')->get(['tb_departments.*','tb_departments.dept_code as department']);
            $Tomorrow=date('Y-m-d',strtotime('+1 days',strtotime($Tgl)));
            return view('page/tms/employee_shift_plan_update',['tb_department'=>$tb_department,'department'=>$department,'periode'=>$periode,'Tgl'=>$Tgl,'Tomorrow'=>$Tomorrow,'Akhir'=>$hari_akhir,'halaman'=>'updatesTMS/Plan','site'=>$this->site,'menu'=>'tms','juduls'=>'Work Schedule Update','subjudul'=>$subjudul]);
        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }
    function updatesPlanTMS_Single($department,$Tgl){
        $kalendar=CAL_GREGORIAN;
        if (request()->user()->hasRole('root')||request()->user()->hasRole('tms')||request()->user()->hasRole('admin_department')){
            $Today=date('Y-m-d');
            $thn=date('Y',strtotime($Today));
            $bln=date('m',strtotime($Today));
            $hariakhir=cal_days_in_month($kalendar,$bln,$thn);
            $hari_akhir=date('Y-m-d',strtotime($thn.'-'.$bln.'-'.$hariakhir));

            $periode=date('Y-m',strtotime($Tgl));
            $subjudul=date('F Y',strtotime($periode.'-01')).' Generate Schedule...';
            $i=date('d',strtotime($Tgl));
            if(strlen($i)==1)$j='D0'.$i;
            else $j='D'.$i;
            $cek=$this->qty_work_entry($department,$Tgl);

            $cek_libur=DB::table('tb_freedays')->where('date_off',$Tgl)->where('category','Holiday')->count();
            //return $cek;
            if($cek>0&&$cek_libur==0){
                $tb_work_contract=$this->tb_work_contract($department);
                foreach($tb_work_contract as $dt){
                    $tgl1 = new DateTime($dt->start_implement);
                    $tgl2 = new DateTime($Tgl);
                    $diffdays = $tgl2->diff($tgl1)->days;
                    $cycle=$dt->cycle_day;
                    $diffcycle=Floor($diffdays/$cycle);
                    $modcycle=$diffdays%$cycle;
                    $modcycle++;

                    $tb_work_cycle=$this->tb_work_cycle($dt->id_work_group,$modcycle);
                    //return $tb_work_cycle;
                    foreach($tb_work_cycle as $dt2){
                        $plan_work=$dt2->id_work_time;
                        $tb_work_entries=DB::table('tb_work_entries')->where('id_employee',$dt->id_employee)->where('periode',$periode)->where('plan_actual','plan')->update([
                            $j=>$plan_work
                        ]);
                    }
                }
            }

        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }
    //Update WE-Plan 1 Day for All Employee
    function updatePlanTMS($department,$Tgl,$shift,$group){
        //return "A";
        //update via tabel head
        if (request()->user()->hasRole('root')||request()->user()->hasRole('tms')){
            $periode=date('Y-m',strtotime($Tgl));
            $i=date('d',strtotime($Tgl));
            if(strlen($i)==1)$j='D0'.$i;
            else $j='D'.$i;

            $tb_work_entries=DB::table('tb_work_entries')
            ->leftjoin('tb_work_contract','tb_work_contract.id_employee','=','tb_work_entries.id_employee')
            ->where('tb_work_contract.id_work_shift',$group)->where('tb_work_contract.department',$department)
            ->where('tb_work_entries.periode',$periode)->where('tb_work_entries.plan_actual','plan')->update([
                $j=>$shift
            ]);

            return redirect()->back();
        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }

    //Update WE-Actual Continuous by Finger Data
    function updatesTMS($department,$Tgl){
        $now=date('Y-m-d H:i:s');
        $admin=Auth::user()->name;
        if (request()->user()->hasRole('root')||request()->user()->hasRole('tms')){
            $Today=date('Y-m-d');
            $periode=date('Y-m',strtotime($Tgl));
            if($department==0)$subjudul=date('F Y',strtotime($periode.'-01')).' Updating Finger Print...';
            else $subjudul=date('F Y',strtotime($periode.'-01')).' Updating Finger Print...';
            $i=date('d',strtotime($Tgl));
            if(strlen($i)==1)$j='D0'.$i;
            else $j='D'.$i;
            $cek=$this->qty_work_entry($department,$Tgl);
            \Log::info("Update ".$Tgl);
            if($cek>0){
                $tb_work_entries=$this->tb_work_entries($department,$periode,0);
                //return $tb_work_entries;
                $no=0;
                foreach($tb_work_entries as $dt){
                    $no++;
                    \Log::info("Update ".$Tgl.' '.$no);
                    $plan_work=$dt->$j;
                    $tb_work_time=$this->tb_work_time($plan_work);
                    //return $dt->plan_actual;
                    //return $dt->D02;
                    foreach($tb_work_time as $dt2){
                        $advance=$dt2->is_advance;
                        if($dt2->is_advance==0)$tanggal=$Tgl;
                        else $tanggal=date('Y-m-d',strtotime('-1 days',strtotime($Tgl)));
                        if($plan_work>0){
                            $check_in=$tanggal.' '.$dt2->check_in;
                            $check_out=$Tgl.' '.$dt2->check_out;
                        }else{
                            $check_in='';
                            $check_out='';
                        }

                        //Check Actual Finger
                            //Return Checkin Range
                            if(isset($check_in)&&$check_in!=''&&$check_in!=$check_out){
                                $ncdatein=$check_in;
                                $ncdateindown= date('Y-m-d H:i:s',strtotime('-3 hours',strtotime($ncdatein)));
                                $ncdateinup= date('Y-m-d H:i:s',strtotime('+5 hours',strtotime($ncdatein)));
                            }else{
                                $ncdateindown='';
                                $ncdateinup='';
                            }
                            //Return Checkout Range
                            if(isset($check_out)&&$check_out!=''&&$check_in!=$check_out){
                                $ncdateout=$check_out;
                                $ncdateoutdown= date('Y-m-d H:i:s',strtotime('-2 hours',strtotime($ncdateout)));
                                $ncdateoutup= date('Y-m-d H:i:s',strtotime('+5 hours',strtotime($ncdateout)));
                            }else{
                                $ncdateoutdown='';
                                $ncdateoutup='';
                            }
                            //Read Finger Print
                            $PIN=$dt->PIN;
                            $lenbadge=strlen($PIN);
                            $nullbadge=9-$lenbadge;
                            $p='';
                            for($q=1;$q<=$nullbadge;$q++){
                                $p.='0';
                            }
                            $badge=$p.$PIN;

                            $host = mysqli_connect("192.168.121.4:83306","cahyudin","123456","adms_db");
                            $text1="select checktime as masuk from checkinout left join userinfo on userinfo.userid=checkinout.userid where userinfo.badgenumber='$badge' and checktime>='$ncdateindown' and checktime<='$ncdateinup' order by checktime desc";
                            //$text2="select max(checktime) from checkinout left join userinfo on userinfo.userid=checkinout.userid where userinfo.badgenumber='$badge' and checktime>='$ncdateoutdown' and checktime<='$ncdateoutup'";
                            $qry=mysqli_query($host,$text1)or die(mysqli_error($host));
                            $qty_finger=mysqli_num_rows($qry);
                            if($qty_finger>0){
                                while($row=mysqli_fetch_array($qry)){
                                    if($row['masuk']>$ncdatein)$work_status='71';
                                    elseif($row['masuk']>0) $work_status='51';
                                }
                            }else $work_status='99';
                        //End Check Actual Finger

                        if($work_status>0){
                            $tb_work_entries=DB::table('tb_work_entries')->where('id_employee',$dt->id_employee)->where('periode',$periode)->where('plan_actual','actual')->update([
                                $j=>$work_status,
                                'admin'=>$admin,
                                'updated_at'=>$now
                            ]);
                        }

                    }
                }
            }

            $tb_department=$this->tb_department();
            $Tomorrow=date('Y-m-d',strtotime('+1 days',strtotime($Tgl)));
            return view('page/tms/employee_shift_update',['tb_department'=>$tb_department,'department'=>$department,'periode'=>$periode,'Tgl'=>$Tgl,'Tomorrow'=>$Tomorrow,'Today'=>$Today,'halaman'=>'updatesTMS','site'=>$this->site,'menu'=>'tms','juduls'=>'Work Entry Update','subjudul'=>$subjudul]);
        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }
    //Update WE-Actual Continuous by tb_checktimes
    function updatesManual($department,$Tgl){
        $now=date('Y-m-d H:i:s');
        //return $Tgl;
        $admin=Auth::user()->name;
        if (request()->user()->hasRole('root')||request()->user()->hasRole('tms')){
            $Today=date('Y-m-d');
            $periode=date('Y-m',strtotime($Tgl));
            $subjudul=date('F Y',strtotime($periode.'-01')).' Updating Manual Check...';
            $i=date('d',strtotime($Tgl));
            if(strlen($i)==1)$j='D0'.$i;
            else $j='D'.$i;
            $cek=$this->qty_work_entry_manual($department,$Tgl);
            //return $cek;
            if($cek>0){
                $tb_work_entries=$this->tb_work_entries($department,$periode,0);
                //return $tb_work_entries;
                $no=0;
                foreach($tb_work_entries as $dt){
                    $no++;
                    $plan_work=$dt->$j;
                    $tb_work_time=$this->tb_work_time($plan_work);
                    foreach($tb_work_time as $dt2){
                        $advance=$dt2->is_advance;
                        if($dt2->is_advance==0)$tanggal=$Tgl;
                        else $tanggal=date('Y-m-d',strtotime('-1 days',strtotime($Tgl)));
                        if($plan_work>0){
                            $check_in=$tanggal.' '.$dt2->check_in;
                            $check_out=$Tgl.' '.$dt2->check_out;
                        }else{
                            $check_in='';
                            $check_out='';
                        }
                    }
                    //Check Actual Finger
                        //Return Checkin Range
                        if(isset($check_in)&&$check_in!=''&&$check_in!=$check_out){
                            $ncdatein=$check_in;
                            $ncdateindown= date('Y-m-d H:i:s',strtotime('-3 hours',strtotime($ncdatein)));
                            $ncdateinup= date('Y-m-d H:i:s',strtotime('+5 hours',strtotime($ncdatein)));
                        }else{
                            $ncdateindown='';
                            $ncdateinup='';
                        }
                        //Return Checkout Range
                        if(isset($check_out)&&$check_out!=''&&$check_in!=$check_out){
                            $ncdateout=$check_out;
                            $ncdateoutdown= date('Y-m-d H:i:s',strtotime('-2 hours',strtotime($ncdateout)));
                            $ncdateoutup= date('Y-m-d H:i:s',strtotime('+5 hours',strtotime($ncdateout)));
                        }else{
                            $ncdateoutdown='';
                            $ncdateoutup='';
                        }
                        //Read Manual
                            $PIN=$dt->PIN;
                            $NIK=$dt->NIK;
                            $lenbadge=strlen($PIN);
                            $nullbadge=9-$lenbadge;
                            $p='';
                            for($q=1;$q<=$nullbadge;$q++){
                                $p.='0';
                            }
                            $badge=$p.$PIN;
                            if($plan_work>0){
                                $work_status='99';
                            }
                            //$qty_manual=DB::table('tb_checktimes')->where('PIN',$PIN)->where('checktime','>=',$ncdateindown)->where('checktime','<=',$ncdateoutup)->count();
                            $masuk=DB::table('tb_checktimes')
                            ->where('NIK',$NIK)->where('checktime','>=',$ncdateindown)->where('checktime','<=',$ncdateinup)
                            ->orderby('checktime','desc')
                            ->get(['checktime']);
                            foreach($masuk as $row){
                                if($row->checktime>=$ncdatein)$work_status='72';
                                elseif($plan_work>0) $work_status='52';
                            }
                            $tb_work_entries=DB::table('tb_work_entries')->where('id_employee',$dt->id_employee)->where('periode',$periode)->where('plan_actual','actual')->where($j,'99')->update([
                                $j=>$work_status,
                                'admin'=>$admin,
                                'updated_at'=>$now
                            ]);
                        //End Check Actual Finger
                    //End Check Actual Finger
                    \Log::info($no.": ".$dt->id_employee);
                }
            }

            $tb_department=DB::table('tb_departments')->get(['tb_departments.*','tb_departments.dept_code as department']);
            $Tomorrow=date('Y-m-d',strtotime('+1 days',strtotime($Tgl)));
            return view('page/tms/employee_shift_update',['tb_department'=>$tb_department,'department'=>$department,'periode'=>$periode,'Tgl'=>$Tgl,'Tomorrow'=>$Tomorrow,'Today'=>$Today,'halaman'=>'updatesManual','site'=>$this->site,'menu'=>'tms','juduls'=>'Work Entry','subjudul'=>$subjudul]);
        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }
    //Update WE-Actual Continuous by tb_absencies
    function updatesLeave($department,$Tgl){
        $now=date('Y-m-d H:i:s');
        $admin=Auth::user()->name;
        if (request()->user()->hasRole('root')||request()->user()->hasRole('tms')){
            //update Mass leave
            $qty_leaves=DB::table('tb_freedays')->where('date_off','>=',$Tgl)->where('category','LEAVE')->get();
            foreach($qty_leaves as $dt){
                $periode=date('Y-m',strtotime($Tgl));
                $i=date('d',strtotime($dt->date_off));
                if(strlen($i)==1)$j='D0'.$i;
                else $j='D'.$i;
                $tb_work_entries_p=DB::table('tb_work_entries')->where('periode',$periode)->where('plan_actual','plan')->where($j,">","0")->count();
                if($tb_work_entries_p>0){
                    $tb_work_entries=DB::table('tb_work_entries')->where('periode',$periode)->where('plan_actual','actual')->update([
                        $j=>'53',
                        'admin'=>$admin,
                        'updated_at'=>$now
                    ]);
                }
            }

            $qty_leave=DB::table('tb_absencies')->where('date_off','>=',$Tgl)->where('category','LEAVE')->get();
            foreach($qty_leave as $dt){
                $periode=date('Y-m',strtotime($Tgl));
                $i=date('d',strtotime($dt->date_off));
                if(strlen($i)==1)$j='D0'.$i;
                else $j='D'.$i;
                $tb_work_entries_p=DB::table('tb_work_entries')->where('id_employee',$dt->id_employee)->where('periode',$periode)->where('plan_actual','plan')->where($j,">","0")->count();
                if($tb_work_entries_p>0){
                    $tb_work_entries=DB::table('tb_work_entries')->where('id_employee',$dt->id_employee)->where('periode',$periode)->where('plan_actual','actual')->update([
                        $j=>'53',
                        'admin'=>$admin,
                        'updated_at'=>$now
                    ]);
                }
            }
            $qty_sakit=DB::table('tb_absencies')->where('date_off','>=',$Tgl)->where('category','SAKIT')->get();
            foreach($qty_sakit as $dt){
                $periode=date('Y-m',strtotime($Tgl));
                $i=date('d',strtotime($dt->date_off));
                if(strlen($i)==1)$j='D0'.$i;
                else $j='D'.$i;
                $tb_work_entries=DB::table('tb_work_entries')->where('id_employee',$dt->id_employee)->where('periode',$periode)->where('plan_actual','actual')->update([
                    $j=>'54',
                    'admin'=>$admin,
                    'updated_at'=>$now
                ]);
            }
            $qty_ijin=DB::table('tb_absencies')->where('date_off','>=',$Tgl)->where('category','IJIN')->get();
            foreach($qty_ijin as $dt){
                $periode=date('Y-m',strtotime($Tgl));
                $i=date('d',strtotime($dt->date_off));
                if(strlen($i)==1)$j='D0'.$i;
                else $j='D'.$i;
                $tb_work_entries=DB::table('tb_work_entries')->where('id_employee',$dt->id_employee)->where('periode',$periode)->where('plan_actual','actual')->update([
                    $j=>'55',
                    'admin'=>$admin,
                    'updated_at'=>$now
                ]);
            }
            $qty_permit=DB::table('tb_izins')->where('apply_date','>=',$Tgl)->where('status_personalia','1')->get();
            foreach($qty_permit as $dt){
                $periode=date('Y-m',strtotime($Tgl));
                $i=date('d',strtotime($dt->apply_date));
                if(strlen($i)==1)$j='D0'.$i;
                else $j='D'.$i;
                if($dt->category!='A'){
                    if($dt->category=='B')$nilai='731';
                    elseif($dt->category=='C')$nilai='732';
                    elseif($dt->category=='D')$nilai='733';
                    else $nilai='73';
                    $tb_work_entries=DB::table('tb_work_entries')->where('id_employee',$dt->id_employee)->where('periode',$periode)->where('plan_actual','actual')->update([
                        $j=>$nilai,
                        'admin'=>$admin,
                        'updated_at'=>$now
                    ]);
                }
            }
            return redirect()->back();
        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }
    //Update WE-Actual 1 Employee for 1 Day
    //Update WE-Actual 1 Day form all Employee
    function updateTMS($department,$Tgl){
        $now=date('Y-m-d H:i:s');
        $admin=Auth::user()->name;
        $Today=date('Y-m-d');
        $this->updatesPlanTMS_Single($department,$Tgl);
        if($Tgl<=$Today){
            //return "Masuk2";
            if (request()->user()->hasRole('root')||request()->user()->hasRole('tms')||request()->user()->hasRole('admin_department')){
                $periode=date('Y-m',strtotime($Tgl));
                $i=date('d',strtotime($Tgl));
                if(strlen($i)==1)$j='D0'.$i;
                else $j='D'.$i;
                $tb_work_entries=$this->tb_work_entries($department,$periode,0);
                foreach($tb_work_entries as $dt){
                    //if($dt->id_employee=='1224'){
                        $plan_work=$dt->$j;
                        $tb_work_time=$this->tb_work_time($plan_work);
                        foreach($tb_work_time as $dt2){
                            $advance=$dt2->is_advance;
                            if($dt2->is_advance==0)$tanggal=$Tgl;
                            else $tanggal=date('Y-m-d',strtotime('-1 days',strtotime($Tgl)));
                            if($plan_work>0){
                                $check_in=$tanggal.' '.$dt2->check_in;
                                $check_out=$Tgl.' '.$dt2->check_out;
                            }else{
                                $check_in='';
                                $check_out='';
                            }
                        }
                        //Check Actual Finger
                            //Return Checkin Range
                            if(isset($check_in)&&$check_in!=''&&$check_in!=$check_out){
                                $ncdatein=$check_in;
                                $ncdateindown= date('Y-m-d H:i:s',strtotime('-3 hours',strtotime($ncdatein)));
                                $ncdateinup= date('Y-m-d H:i:s',strtotime('+5 hours',strtotime($ncdatein)));
                            }else{
                                $ncdateindown='';
                                $ncdateinup='';
                            }
                            //Return Checkout Range
                            if(isset($check_out)&&$check_out!=''&&$check_in!=$check_out){
                                $ncdateout=$check_out;
                                $ncdateoutdown= date('Y-m-d H:i:s',strtotime('-2 hours',strtotime($ncdateout)));
                                $ncdateoutup= date('Y-m-d H:i:s',strtotime('+5 hours',strtotime($ncdateout)));
                            }else{
                                $ncdateoutdown='';
                                $ncdateoutup='';
                            }
                            //Read Finger Print
                            $PIN=$dt->PIN;
                            $lenbadge=strlen($PIN);
                            $nullbadge=9-$lenbadge;
                            $p='';
                            for($q=1;$q<=$nullbadge;$q++){
                                $p.='0';
                            }
                            $badge=$p.$PIN;
                            $host = mysqli_connect("192.168.121.4:83306","cahyudin","123456","adms_db");
                            $text1="select checktime as masuk from checkinout left join userinfo on userinfo.userid=checkinout.userid where userinfo.badgenumber='$badge' and checktime>='$ncdateindown' and checktime<='$ncdateinup' order by checktime desc";
                            //$text2="select max(checktime) from checkinout left join userinfo on userinfo.userid=checkinout.userid where userinfo.badgenumber='$badge' and checktime>='$ncdateoutdown' and checktime<='$ncdateoutup'";
                            $qry=mysqli_query($host,$text1)or die(mysqli_error($host));
                            $qty_finger=mysqli_num_rows($qry);
                            $work_status=0;
                            if($qty_finger>0){
                                while($row=mysqli_fetch_array($qry)){
                                    if($row['masuk']>$ncdatein)$work_status='71';
                                    else $work_status='51';
                                }
                            }
                            //if($dt->id_employee=='76')return $qty_finger;
                        //End Check Actual Finger
                        if($work_status=='71'||$work_status=='51'){
                            $tb_work_entries=DB::table('tb_work_entries')->where('id_employee',$dt->id_employee)->where('periode',$periode)->where('plan_actual','actual')->update([
                                $j=>$work_status,
                                'admin'=>$admin,
                                'updated_at'=>$now
                            ]);
                        }else{
                            $cek_libur=DB::table('tb_freedays')->where('date_off',$Tgl)->count();
                            if($cek_libur>0){
                                //return $j;
                                $tb_work_entries=DB::table('tb_work_entries')->where('id_employee',$dt->id_employee)->where('periode',$periode)->update([
                                    $j=>'0',
                                    'admin'=>$admin,
                                    'updated_at'=>$now
                                ]);
                            }
                        }
                        //return $cek_libur;
                    //}
                }
                return redirect()->back();
            }else{
                return abort(403,'Anda tidak punya akses');
            }
        }else{
            return redirect()->back()->with(['success'=>'Waiting for Finish Day']);
        }
    }
    //Inactive tb_work_contract
    function inactiveTMS($id){
        $update=DB::table('tb_work_contract')->where('id',$id)->update(['isactive'=>'0']);
        if($update)return redirect()->back()->with(['success'=>'Deleted']);
    }
    //Update summary kehadiran
    function summaryTMS($department,$periode,$group){
        $now=date('Y-m-d H:i:s');
        $admin=Auth::user()->name;
        if (request()->user()->hasRole('root')||request()->user()->hasRole('tms')){
            $tb_work_entries=$this->tb_work_entries($department,$periode,$group);
            foreach($tb_work_entries as $dt){
                if($dt->plan_actual=='plan'){
                    $working_day=0;
                    $qty_shift=0;$b='';
                    for($a=1;$a<=31;$a++){
                        if(strlen($a)==1)$j='D0'.$a;
                        else $j='D'.$a;
                        //$b.=$a." ";
                        //if($a==1)return $b;
                        if($dt->$j>0)$working_day++;
                        $tb_work_time=$this->tb_work_time($dt->$j);
                        foreach ($tb_work_time as $dt2) {
                            if($dt2->is_shift==1){
                                $tb_we=DB::table('tb_work_entries')->where('id_employee',$dt->id_employee)->where('periode',$periode)->where('plan_actual','actual')->get([$j]);
                                foreach($tb_we as $dt3){
                                    if($dt3->$j=='51'||$dt3->$j=='52'||$dt3->$j=='71'||$dt3->$j=='72'||$dt3->$j=='73'||$dt3->$j=='731'||$dt3->$j=='732'||$dt3->$j=='733'){
                                        $qty_shift++;
                                    }
                                }
                            }
                        }                      
                    }
                }
                if($dt->plan_actual=='actual'){
                    $qty_entry=0;
                    $qty_absent=0;
                    $shortage_times=0;
                    $qty_leave=0;
                    $permit_times=0;
                    for($i=1;$i<=31;$i++){
                        if(strlen($i)==1)$j='D0'.$i;
                        else $j='D'.$i;
                        // if($dt->$j=='51'||$dt->$j=='52'||$dt->$j=='71'||$dt->$j=='72'||$dt->$j=='73'||$dt3->$j=='731'||$dt3->$j=='732'||$dt3->$j=='733')$qty_entry++;
                        if($dt->$j=='51'||$dt->$j=='52'||$dt->$j=='71'||$dt->$j=='72'||$dt->$j=='73'||$dt->$j=='510'||$dt->$j=='520'||$dt->$j=='710'||$dt->$j=='720'||$dt->$j=='730'||$dt->$j=='731'||$dt->$j=='732'||$dt->$j=='733'||$dt->$j=='7310'||$dt->$j=='7320'||$dt->$j=='7330')$qty_entry++;
                        if($dt->$j=="99")$qty_absent++;
                        if($dt->$j=="71"||$dt->$j=="72")$shortage_times++;
                        if($dt->$j=="53")$qty_leave++;
                        if($dt->$j=="55")$permit_times++;
                        //return $j;
                    }
                    //return $qty_entry;
                    $update_we=DB::table('tb_work_entries')->where('id_employee',$dt->id_employee)->where('periode',$periode)->where('plan_actual','actual')
                    ->update([
                        'working_day'=>$working_day,
                        'qty_entry'=>$qty_entry,
                        'qty_shift'=>$qty_shift,
                        'qty_absent'=>$qty_absent,
                        'shortage_times'=>$shortage_times,
                        'permit_times'=>$permit_times,
                        'admin'=>$admin,
                        'updated_at'=>$now
                    ]);
                }
            }
            $link="/TMS/SummaryShortage/".$department."/".$periode."/".$group;
            //return $link;
            //return redirect($link);
            return redirect()->back();
        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }
    //Update keterlambatan
    function summaryTMSshortage($department,$periode,$group){
        $now=date('Y-m-d H:i:s');
        $admin=Auth::user()->name;
        if (request()->user()->hasRole('root')||request()->user()->hasRole('tms')){
            $tb_work_entries=$this->tb_work_entries($department,$periode,$group);
            foreach($tb_work_entries as $dt){
                if($dt->plan_actual=='actual'){
                    $minute_shortage=0;
                    for($i=1;$i<=31;$i++){
                        if(strlen($i)==1){
                            $j='D0'.$i;
                            $Tgl=$periode."-0".$i;
                        }
                        else {
                            $j='D'.$i;
                            $Tgl=$periode."-".$i;
                        }
                        $plan_work=$dt->$j;
                        if($dt->$j=="71"||$dt->$j=="72"){
                            $tb_we=DB::table('tb_work_entries')->where('id_employee',$dt->id_employee)->where('periode',$periode)->where('plan_actual','plan')->get([$j]);
                            foreach($tb_we as $dt2){
                                $tb_work_time=$this->tb_work_time($dt2->$j);
                                foreach($tb_work_time as $dt3){
                                    $advance=$dt3->is_advance;
                                    if($dt3->is_advance==0)$tanggal=$Tgl;
                                    else $tanggal=date('Y-m-d',strtotime('-1 days',strtotime($Tgl)));
                                    if($plan_work>0){
                                        $check_in=$tanggal.' '.$dt3->check_in;
                                        $check_out=$Tgl.' '.$dt3->check_out;
                                    }else{
                                        $check_in='';
                                        $check_out='';
                                    }
                                    
                                }
                                //Return Checkin Range
                                if(isset($check_in)&&$check_in!=''&&$check_in!=$check_out){
                                    $ncdatein=$check_in;
                                    $ncdateindown= date('Y-m-d H:i:s',strtotime('-3 hours',strtotime($ncdatein)));
                                    $ncdateinup= date('Y-m-d H:i:s',strtotime('+5 hours',strtotime($ncdatein)));
                                }else{
                                    $ncdateindown='';
                                    $ncdateinup='';
                                }
                                //Return Checkout Range
                                if(isset($check_out)&&$check_out!=''&&$check_in!=$check_out){
                                    $ncdateout=$check_out;
                                    $ncdateoutdown= date('Y-m-d H:i:s',strtotime('-2 hours',strtotime($ncdateout)));
                                    $ncdateoutup= date('Y-m-d H:i:s',strtotime('+5 hours',strtotime($ncdateout)));
                                }else{
                                    $ncdateoutdown='';
                                    $ncdateoutup='';
                                }
                                //Read Finger Print
                                $PIN=$dt->PIN;
                                $NIK=$dt->NIK;
                                $lenbadge=strlen($PIN);
                                $nullbadge=9-$lenbadge;
                                $p='';
                                for($q=1;$q<=$nullbadge;$q++){
                                    $p.='0';
                                }
                                $badge=$p.$PIN;
                                $actual='';
                                //Check Actual Finger
                                    if($dt->$j=="71"||$dt->$j=="710"){
                                        $host = mysqli_connect("192.168.121.4:83306","cahyudin","123456","adms_db");
                                        $text1="select checktime as masuk from checkinout left join userinfo on userinfo.userid=checkinout.userid where userinfo.badgenumber='$badge' and checktime>='$ncdateindown' and checktime<='$ncdateinup' order by checktime desc";
                                        $qry=mysqli_query($host,$text1)or die(mysqli_error($host));
                                        while($row=mysqli_fetch_array($qry)){
                                            $actual=$row['masuk'];
                                        }
                                    }
                                //End Check Actual Finger
                                //Check Actual Manual
                                    if($dt->$j=="72"||$dt->$j=="720"){
                                        $masuk=DB::table('tb_checktimes')
                                        ->where('NIK',$NIK)->where('checktime','>=',$ncdateindown)->where('checktime','<=',$ncdateinup)
                                        ->orderby('checktime','desc')
                                        ->get();
                                        foreach($masuk as $row){
                                            $actual=$row->checktime;
                                        }
                                    }
                                //End Check Actual Manual
                                if($actual!=''){
                                    $datetime1 = date_create($actual);
                                    $datetime2 = date_create($ncdatein);
                                    $selisih = date_diff($datetime1, $datetime2);
                                    $selisih_jam=$selisih->format('%h');
                                    $selisih_menit=$selisih->format('%i');
                                    $jumlah_selisih=$selisih_jam*60+$selisih_menit;
                                }else{
                                    $jumlah_selisih=0;
                                }
                            }
                            $minute_shortage=$minute_shortage+$jumlah_selisih;
                        }
                    }
                    $update_we=DB::table('tb_work_entries')->where('id_employee',$dt->id_employee)->where('periode',$periode)->where('plan_actual','actual')
                    ->update([
                        'minute_shortage'=>$minute_shortage,
                        'admin'=>$admin,
                        'updated_at'=>$now
                    ]);
                 }
            }
            $link="/TMS/SummaryPermit/".$department."/".$periode."/".$group;
            //return $link;
            //return redirect($link);
            return redirect()->back();
        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }
    //Update Qty Permit
    function summaryTMSpermit($department,$periode,$group){
        $now=date('Y-m-d H:i:s');
        $admin=Auth::user()->name;
        if (request()->user()->hasRole('root')||request()->user()->hasRole('tms')){
            $tb_work_entries=$this->tb_work_entries($department,$periode,$group);
            foreach($tb_work_entries as $dt){
                if($dt->plan_actual=='actual'){
                    $minute_permit=0;
                    $qty_permit=0;
                    $qty_leave=0;
                    $qty_sick=0;
                    $qty_a=0;
                    $qty_b=0;
                    $qty_c=0;
                    $qty_d=0;
                    for($i=1;$i<=31;$i++){
                        if(strlen($i)==1){
                            $j='D0'.$i;
                            $Tgl=$periode."-0".$i;
                        }
                        else {
                            $j='D'.$i;
                            $Tgl=$periode."-".$i;
                        }
                        if($dt->$j=="53"){
                            $qty_leave++;
                        }
                        if($dt->$j=="54"){
                            $qty_sick++;
                        }
                        if($dt->$j=="55"){
                            $jumlah_menit=480;
                            $qty_a++;
                        }
                        if($dt->$j=="73"||$dt->$j=="731"||$dt->$j=="732"||$dt->$j=="733"){
                            $qty_permit++;
                            $tb_izins=DB::table('tb_izins')->where('id_employee',$dt->id_employee)->where('apply_date',$Tgl)->where('status_personalia','1')->get();
                            foreach($tb_izins as $dt2){
                                if($dt2->category=='B'){
                                    $jumlah_menit=240;
                                    $qty_b++;
                                }
                                if($dt2->category=='C'){
                                    $jumlah_menit=$dt2->minutes;
                                    $qty_c++;
                                }
                                if($dt2->category=='D'){
                                    $jumlah_menit=$dt2->minutes;
                                    $qty_d++;
                                }
                            }
                            $minute_permit=$minute_permit+$jumlah_menit;
                        }
                    }
                    $update_we=DB::table('tb_work_entries')->where('id_employee',$dt->id_employee)->where('periode',$periode)->where('plan_actual','actual')
                    ->update([
                        'minute_permit'=>$minute_permit,
                        'permit_times'=>$qty_permit,
                        'qty_leave'=>$qty_leave,
                        'qty_sick'=>$qty_sick,
                        'qty_a'=>$qty_a,
                        'qty_b'=>$qty_b,
                        'qty_c'=>$qty_c,
                        'qty_d'=>$qty_d,
                        'admin'=>$admin,
                        'updated_at'=>$now
                    ]);
                 }
            }
            $link="/TMS/Sending/".$department."/".$periode."/".$group;
            //$link="/TMS/Sending/0/".$periode."/".$group;
            return redirect()->back();
            //return redirect($link);
        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }
    function sendingTMS($department,$periode,$group){
        //return $department;
        //return "Masuk";
        $kalendar=CAL_GREGORIAN;
        $thn=date('Y',strtotime($periode.'-01'));
        $bln=date('m',strtotime($periode.'-01'));
        $hariakhir=cal_days_in_month($kalendar,$bln,$thn);

        $now=date('Y-m-d H:i:s');
        $admin=Auth::user()->name;
        $start=date('Y-m-d',strtotime($periode.'-01'));
        $finish=date('Y-m-d',strtotime($periode.'-'.$hariakhir));

        if (request()->user()->hasRole('root')||request()->user()->hasRole('tms')){
            $tb_work_entries=$this->tb_work_entries($department,$periode,$group);
            //return $tb_work_entries;
            foreach($tb_work_entries as $dt){
                //return "Masuk";
                if($dt->plan_actual=='actual'){
                    $tb_department=DB::table('tb_departments')->where('dept_code',$dt->department)->get();
                    foreach($tb_department as $dt2){
                        $dept_id=$dt2->id;
                        $divisi=$dt2->divisi;
                        $dept_code=$dt2->dept_code;
                    }
                    $form_a_minutes=$dt->qty_a*480;
                    $setengah_minutes=$dt->qty_b*240;
                    $telat=$dt->minute_shortage;
                    $keluar_minutes=$dt->minute_permit-($form_a_minutes+$setengah_minutes);
                    $present_actual=$dt->qty_entry+$dt->qty_leave;
                    if($dt->working_day==''||$dt->working_day==0)$pesent_rate=0;
                    else $pesent_rate=$present_actual/$dt->working_day*100;
                    $hour_plan=$dt->working_day*8*60;
                    $hour_actual=($present_actual*8*60)-($setengah_minutes+$keluar_minutes+$telat);
                    if($hour_plan==''||$hour_plan==0)$hour_rate=0;
                    else $hour_rate=$hour_actual/$hour_plan*100;
                    // $jumlah_absen=$dt->qty_absent+$dt->qty_a+$dt->qty_sick+$dt->qty_leave;
                    $jumlah_absen=$dt->qty_absent+$dt->qty_a+$dt->qty_sick;

                    $cek=DB::table('tb_absensi_rate')->where('periode',$periode)->where('id_employee',$dt->id_employee)->count();
                    //return $department;
                    if($dt->working_day!=''&&$dt->working_day>0){
                        if($cek>0){
                            $update=DB::table('tb_absensi_rate')->where('periode',$periode)->where('id_employee',$dt->id_employee)->update([
                                'hari_kerja'=>$dt->working_perweek,
                                'dept_id'=>$dept_id,
                                'NIK'=>$dt->NIK,
                                'employee_name'=>$dt->nama_karyawan,
                                'jabatan'=>$dt->jabatan,
                                'dept_code'=>$dept_code,
                                'divisi'=>$divisi,
                                'start'=>$start,
                                'end'=>$finish,
                                'terlambat'=>$dt->shortage_times,
                                'terlambat_minutes'=>$dt->minute_shortage,
                                'setengah_minutes'=>$setengah_minutes,
                                'keluar_minutes'=>$keluar_minutes,
                                'cuti'=>$dt->qty_leave,
                                'sakit'=>$dt->qty_sick,
                                'izin'=>$dt->qty_a,
                                'alpa'=>$dt->qty_absent,
                                'present_plan'=>$dt->working_day,
                                'present_actual'=>$present_actual,
                                'present_rate'=>$pesent_rate,
                                'hour_plan'=>$hour_plan,
                                'hour_actual'=>$hour_actual,
                                'hour_rate'=>$hour_rate,
                                'absent'=>$jumlah_absen,
                                'admin'=>$admin,
                                'position_index'=>$dt->position_index,
                                'updated_at'=>$now
                            ]);
                        }else{
                            $add=DB::table('tb_absensi_rate')->insert([
                                'periode'=>$periode,
                                'hari_kerja'=>$dt->working_perweek,
                                'dept_id'=>$dept_id,
                                'id_employee'=>$dt->id_employee,
                                'NIK'=>$dt->NIK,
                                'employee_name'=>$dt->nama_karyawan,
                                'jabatan'=>$dt->jabatan,
                                'dept_code'=>$dept_code,
                                'divisi'=>$divisi,
                                'start'=>$start,
                                'end'=>$finish,
                                'terlambat'=>$dt->shortage_times,
                                'terlambat_minutes'=>$dt->minute_shortage,
                                'setengah_minutes'=>$setengah_minutes,
                                'keluar_minutes'=>$keluar_minutes,
                                'cuti'=>$dt->qty_leave,
                                'sakit'=>$dt->qty_sick,
                                'izin'=>$dt->qty_a,
                                'alpa'=>$dt->qty_absent,
                                'present_plan'=>$dt->working_day,
                                'present_actual'=>$present_actual,
                                'present_rate'=>$pesent_rate,
                                'hour_plan'=>$hour_plan,
                                'hour_actual'=>$hour_actual,
                                'hour_rate'=>$hour_rate,
                                'absent'=>$jumlah_absen,
                                'admin'=>$admin,
                                'position_index'=>$dt->position_index,
                                'updated_at'=>$now
                            ]);
                        }
                    }

                }
            }
            $link="/TMS/Kumulatif/".$department."/".$periode."/".$group;
            return redirect($link);
        }else{
            return abort(403,'Anda tidak punya akses');
        }

    }
    function kumulatif_AR($department,$periode,$group){
        $now=date('Y-m-d H:i:s');
        $admin=Auth::user()->name;
        if (request()->user()->hasRole('root')||request()->user()->hasRole('tms')){
            $tb_rate=DB::table('tb_absensi_rate')
            ->select('dept_id','hari_kerja','divisi','dept_code','start','end')
            ->where('periode',$periode)->where('present_plan','>','0')
            ->groupby('dept_id','dept_code','hari_kerja','divisi','start','end')
            ->get();
            foreach($tb_rate as $dt){
                $check=DB::table('tb_absensi_rate_kumulatif')->where('periode',$periode)->where('dept_id',$dt->dept_id)->count();                
                if($check==0){
                    DB::table('tb_absensi_rate_kumulatif')->insert([
                        'periode'=>$periode,
                        'hari_kerja'=>$dt->hari_kerja,
                        'dept_id'=>$dt->dept_id,
                        'dept_code'=>$dt->dept_code,
                        'divisi'=>$dt->divisi,
                        'start'=>$dt->start,
                        'end'=>$dt->end,
                        'total_absen'=>'0',
                        'present_plan'=>'0',
                        'total_employee'=>'0',
                        'absensi_rate'=>'0',
                        'parameter'=>'0',
                        'kriteria'=>'0',
                        'admin'=>$admin,
                        'status'=>'0',
                        'created_at'=>$now,
                        'updated_at'=>$now
                    ]);
                }
                $tb_kumulatif= DB::table('tb_absensi_rate_kumulatif')->where('periode',$periode)->where('dept_id',$dt->dept_id)->get();
                $std=DB::table('tb_utilities')->where('atribut','standar_absensi_rate')->get('status');
                foreach($std as $rw){
                    $std_ar=$rw->status;
                }
                foreach ($tb_kumulatif as $dt){
                    $jumlah_absen=0;
                    $plan=0;
                    $jumlah_employee=0;
                    $tb_rate=DB::table('tb_absensi_rate')->where('periode',$periode)->where('dept_id',$dt->dept_id)->get();
                    foreach($tb_rate as $dt_rate){
                        $jumlah_absen=$jumlah_absen+$dt_rate->absent;
                        if($dt_rate->present_plan>0)$plan=$dt_rate->present_plan;
                        if($plan>$dt_rate->present_plan&&$dt_rate->present_plan>0)$plan=$dt_rate->present_plan;
                        $jumlah_employee++;
                    }
                    if($plan* $jumlah_employee==0)$absensi_rate=0;
                    else $absensi_rate=number_format($jumlah_absen/($plan* $jumlah_employee)*100,2);
                    $paramater=$std_ar;
                    if($absensi_rate<=$paramater)$kriteria='Baik';
                    else $kriteria='Buruk';
                    $sekarang=date('Y-m-d H:i:s');
                    $satu=date('Ym');
                    $dua=date('Ym',strtotime($periode.'01'));
                    if($satu>$dua)$status=1;
                    else $status=0;
                    
                    $simpan=DB::table('tb_absensi_rate_kumulatif')->where('dept_id',$dt->dept_id)->where('periode',$periode)->update([
                        'total_absen'=>$jumlah_absen,
                        'present_plan'=>$plan,
                        'dept_code'=>$dt->dept_code,
                        'total_employee'=>$jumlah_employee,
                        'absensi_rate'=>$absensi_rate,
                        'parameter'=>$paramater,
                        'kriteria'=>$kriteria,
                        'admin'=>$admin,
                        'status'=>$status,
                        'updated_at'=>$sekarang
                    ]);

                } 

            }

            $link="/TMS/".$department."/".$periode."/".$group;
            //$link="/Absency/Rate/".$periode."/".$department."/".$group;
            //return $link;
            return redirect($link);
        }else{
            return abort(403,'Anda tidak punya akses');
        }
    
    }
    function dailyGraph($tanggal){
        if($tanggal==0)$tanggal=date('Y-m-d');
        $now=date('Y-m-d H:i:s');
        $periode=date('Y-m',strtotime($tanggal));
        $day=date('d',strtotime($tanggal));
        $kolom="D".$day;
        $subjudul=date('d F Y',strtotime($tanggal));

        $result=DB::table('tb_work_daily')
        ->select('department','jumlah_employee as jml','hadir as Hadir','terlambat as Terlambat','ijin as Ijin','cuti as Cuti','skd as SKD','other as Other','other_siang as Other1','other_malam as Other2','alfa as Alfa','sai as SAI','magang as Magang','pagi','siang','malam','pagi_act','siang_act','malam_act','free','other')
        ->where('periode',$periode)->where('kolom_tgl',$kolom)->get();
        //return $result;

        $sai=0;
        $magang=0;
        $pagi=0;
        $siang=0;
        $malam=0;
        $pagi_masuk=0;
        $pagi_absen=0;
        $siang_masuk=0;
        $siang_absen=0;
        $malam_masuk=0;
        $malam_absen=0;
        $free=0;
        $pagi_other=0;
        $siang_other=0;
        $malam_other=0;
        
        foreach($result as $raw){
            $sai=$sai+$raw->SAI;
            $magang=$magang+$raw->Magang;

            $pagi=$pagi+$raw->pagi;
            $siang=$siang+$raw->siang;
            $malam=$malam+$raw->malam;
            $pagi_masuk=$pagi_masuk+$raw->pagi_act;
            $siang_masuk=$siang_masuk+$raw->siang_act;
            $malam_masuk=$malam_masuk+$raw->malam_act;
            $pagi_other=$pagi_other+$raw->Other;
            $siang_other=$siang_other+$raw->Other1;
            $malam_other=$malam_other+$raw->Other2;
            $free=$free+$raw->free;
        }

        $pagi_absen=$pagi-$pagi_masuk-$pagi_other;
        $siang_absen=$siang-$siang_masuk-$siang_other;
        $malam_absen=$malam-$malam_masuk-$malam_other;

        //return $malam_absen;

        $qty_active=$sai+$magang;

        $tb_work_entries = DB::table('tb_work_entries')
        ->leftJoin('tb_work_contract', 'tb_work_contract.id_employee', '=', 'tb_work_entries.id_employee')
        ->leftjoin('view_email_leader','view_email_leader.id_employee','=','tb_work_contract.id_employee')
        ->where('is_draft','0')
        ->where($kolom,'<>','51')
        ->where($kolom,'<>','52')
        ->where($kolom,'<>','510')
        ->where($kolom,'<>','520')
        // ->where($kolom,'<>','98')
        ->where('daily_show','1')
        ->where('tb_work_entries.periode', $periode)
        ->where('tb_work_entries.plan_actual', 'actual')
        ->where('tb_work_contract.isactive','1');
        $tb_work_entries=$tb_work_entries->get(['tb_work_entries.*','email_address','tb_work_contract.NIK','tb_work_contract.PIN','tb_work_contract.nama_karyawan','tb_work_contract.department','tb_work_contract.jabatan','tb_work_contract.id as id_contract','tb_work_contract.id_work_shift','tb_work_contract.suggest_ws']);

        // foreach($tb_work_entries as $dt){
        //     $emails[]=$dt->email_address;
        // }

        //return  $tb_work_entries ;

        
        // $statusPerDepartment = [];

        // // Definisikan semua status yang mungkin
        // $allStatuses = ['71','72','710','720', '55', '53', '54', '99'];
        
        // foreach ($tb_work_entries as $record) {
        //     $department = $record->department;
        //     $status = $record->$kolom;
            
        //     // Inisialisasi array untuk department jika belum ada
        //     if (!isset($statusPerDepartment[$department])) {
        //         $statusPerDepartment[$department] = array_fill_keys($allStatuses, 0);
        //     }
            
        //     // Increment counter untuk status yang ditemukan
        //     if (in_array($status, $allStatuses)) {
        //         $statusPerDepartment[$department][$status]++;
        //     }
        // }
        
        if (request()->user()->hasRole('root')||request()->user()->hasRole('tms')||request()->user()->hasRole('admin_department')){
            return view('page/tms/daily_graph',['periode'=>$periode,'tb_work_entries'=>$tb_work_entries,'pagi'=>$pagi,'siang'=>$siang,'malam'=>$malam,'pagi_masuk'=>$pagi_masuk,'pagi_absen'=>$pagi_absen,'siang_masuk'=>$siang_masuk,'siang_absen'=>$siang_absen,'malam_masuk'=>$malam_masuk,'malam_absen'=>$malam_absen,'qty_magang'=>$magang,'qty_sai'=>$sai,'pagi_other'=>$pagi_other,'siang_other'=>$siang_other,'malam_other'=>$malam_other,'qty_all'=>$qty_active,'free'=>$free,'tanggal'=>$tanggal,'kolom'=>$kolom,'site'=>$this->site,'menu'=>'tms','juduls'=>'Daily Presence','subjudul'=>$subjudul,'tb_result'=>$result]);
        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }


    
    public function tb_work_time($id){
        $tabel=DB::table('tb_work_time')->where('id',$id)->get();
        return $tabel;
    }
    public function qty_work_entry($department,$Tgl){
        $i=date('d',strtotime($Tgl));
        $j='D'.$i;
        $periode=date('Y-m',strtotime($Tgl));
        $tabel=DB::table('tb_work_entries')
        ->leftjoin('tb_work_contract','tb_work_contract.id_employee','=','tb_work_entries.id_employee')
        ->where('periode',$periode)
        ->whereNull($j);
        if($department!=0)$tabel=$tabel->where('department',$department);
        $tabel=$tabel->count();
        return $tabel;
    }
    public function tb_work_contract($department){
        $tabel=DB::table('tb_work_contract')
        ->leftjoin('tb_work_shift','tb_work_shift.id','=','tb_work_contract.id_work_shift')
        ->leftjoin('tb_work_group','tb_work_group.id','=','tb_work_shift.id_work_group')
        ->where('tb_work_contract.isactive','1');
        if($department!=0)$tabel=$tabel->where('department',$department);
        $tabel=$tabel->get(['tb_work_contract.*','tb_work_shift.id_work_group','tb_work_shift.start_implement','tb_work_group.cycle_day']);
        return $tabel;
    }
    public function qty_work_entry_manual($department,$Tgl){
        $i=date('d',strtotime($Tgl));
        $j='D'.$i;
        $periode=date('Y-m',strtotime($Tgl));
        $tabel=DB::table('tb_work_entries')
        ->leftjoin('tb_work_contract','tb_work_contract.id_employee','=','tb_work_entries.id_employee')
        ->where('periode',$periode)
        ->where($j,'99');
        if($department!=0)$tabel=$tabel->where('department',$department);
        $tabel=$tabel->count();
        return $tabel;
    }




    function updateTMS_backup($department,$Tgl){
        if (request()->user()->hasRole('root')||request()->user()->hasRole('tms')){
            $periode=date('Y-m',strtotime($Tgl));
            $i=date('d',strtotime($Tgl));
            if(strlen($i)==1)$j='D0'.$i;
            else $j='D'.$i;
            $tb_work_contract=$this->tb_work_contract($department);
            foreach($tb_work_contract as $dt){
                $tgl1 = new DateTime($dt->start_implement);
                $tgl2 = new DateTime($Tgl);
                $diffdays = $tgl2->diff($tgl1)->days;
                $cycle=$dt->cycle_day;
                $diffcycle=Floor($diffdays/$cycle);
                $modcycle=$diffdays%$cycle;
                $modcycle++;

                $tb_work_cycle=$this->tb_work_cycle($dt->id_work_group,$modcycle);
                //return $tb_work_cycle;
                foreach($tb_work_cycle as $dt2){
                    $plan_work=$dt2->id_work_time;
                    $tb_work_entries=DB::table('tb_work_entries')->where('id_employee',$dt->id_employee)->where('periode',$periode)->where('plan_actual','plan')->update([
                        $j=>$plan_work
                    ]);
                    $advance=$dt2->is_advance;
                    if($dt2->is_advance==0)$tanggal=$Tgl;
                    else $tanggal=date('Y-m-d',strtotime('-1 days',strtotime($Tgl)));
                    if($plan_work>0){
                        $check_in=$tanggal.' '.$dt2->check_in;
                        $check_out=$Tgl.' '.$dt2->check_out;
                    }else{
                        $check_in='';
                        $check_out='';
                    }

                    //Check Actual Finger
                        //Return Checkin Range
                        if(isset($check_in)&&$check_in!=''&&$check_in!=$check_out){
                            $ncdatein=$check_in;
                            $ncdateindown= date('Y-m-d H:i:s',strtotime('-3 hours',strtotime($ncdatein)));
                            $ncdateinup= date('Y-m-d H:i:s',strtotime('+5 hours',strtotime($ncdatein)));
                        }else{
                            $ncdateindown='';
                            $ncdateinup='';
                        }
                        //Return Checkout Range
                        if(isset($check_out)&&$check_out!=''&&$check_in!=$check_out){
                            $ncdateout=$check_out;
                            $ncdateoutdown= date('Y-m-d H:i:s',strtotime('-2 hours',strtotime($ncdateout)));
                            $ncdateoutup= date('Y-m-d H:i:s',strtotime('+5 hours',strtotime($ncdateout)));
                        }else{
                            $ncdateoutdown='';
                            $ncdateoutup='';
                        }
                        //Read Finger Print
                        $PIN=$dt->PIN;
						$host = mysqli_connect("192.168.121.4:83306","cahyudin","123456","adms_db");
                        $qry=mysqli_query($host,"select checktime from checkinout where userid='$PIN' and checktime>='$ncdateindown' and checktime<='$ncdateoutup'")or die(mysqli_error($host));
                        $qty_finger=mysqli_num_rows($qry);
                        if($qty_finger>0){
                            if($plan_work=='3')$work_status='11';
                            else $work_status='10';
                        }else $work_status='0';
                        $tb_work_entries=DB::table('tb_work_entries')->where('id_employee',$dt->id_employee)->where('periode',$periode)->where('plan_actual','actual')->update([
                            $j=>$work_status
                        ]);
                    //End Check Actual Finger
                }
            }
            return redirect()->back();
        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }
    function updatesTMS_backup($department,$Tgl){
        if (request()->user()->hasRole('root')||request()->user()->hasRole('tms')){
            $Today=date('Y-m-d');
            $periode=date('Y-m',strtotime($Tgl));
            $i=date('d',strtotime($Tgl));
            if(strlen($i)==1)$j='D0'.$i;
            else $j='D'.$i;
            $cek=$this->qty_work_entry($department,$Tgl);
            //return $cek;
            if($cek>0){
                $tb_work_contract=$this->tb_work_contract($department);
                foreach($tb_work_contract as $dt){
                    $tgl1 = new DateTime($dt->start_implement);
                    $tgl2 = new DateTime($Tgl);
                    $diffdays = $tgl2->diff($tgl1)->days;
                    $cycle=$dt->cycle_day;
                    $diffcycle=Floor($diffdays/$cycle);
                    $modcycle=$diffdays%$cycle;
                    $modcycle++;

                    $tb_work_cycle=$this->tb_work_cycle($dt->id_work_group,$modcycle);
                    //return $tb_work_cycle;
                    foreach($tb_work_cycle as $dt2){
                        $plan_work=$dt2->id_work_time;
                        $tb_work_entries=DB::table('tb_work_entries')->where('id_employee',$dt->id_employee)->where('periode',$periode)->where('plan_actual','plan')->update([
                            $j=>$plan_work
                        ]);
                        $advance=$dt2->is_advance;
                        if($dt2->is_advance==0)$tanggal=$Tgl;
                        else $tanggal=date('Y-m-d',strtotime('-1 days',strtotime($Tgl)));
                        if($plan_work>0){
                            $check_in=$tanggal.' '.$dt2->check_in;
                            $check_out=$Tgl.' '.$dt2->check_out;
                        }else{
                            $check_in='';
                            $check_out='';
                        }

                        //Check Actual Finger
                            //Return Checkin Range
                            if(isset($check_in)&&$check_in!=''&&$check_in!=$check_out){
                                $ncdatein=$check_in;
                                $ncdateindown= date('Y-m-d H:i:s',strtotime('-3 hours',strtotime($ncdatein)));
                                $ncdateinup= date('Y-m-d H:i:s',strtotime('+5 hours',strtotime($ncdatein)));
                            }else{
                                $ncdateindown='';
                                $ncdateinup='';
                            }
                            //Return Checkout Range
                            if(isset($check_out)&&$check_out!=''&&$check_in!=$check_out){
                                $ncdateout=$check_out;
                                $ncdateoutdown= date('Y-m-d H:i:s',strtotime('-2 hours',strtotime($ncdateout)));
                                $ncdateoutup= date('Y-m-d H:i:s',strtotime('+5 hours',strtotime($ncdateout)));
                            }else{
                                $ncdateoutdown='';
                                $ncdateoutup='';
                            }
                            //Read Finger Print
                                $PIN=$dt->PIN;
                                $host = mysqli_connect("192.168.121.4:83306","cahyudin","123456","adms_db");
                                $qry=mysqli_query($host,"select checktime from checkinout where userid='$PIN' and checktime>='$ncdateindown' and checktime<='$ncdateoutup'")or die(mysqli_error($host));
                                $qty_finger=mysqli_num_rows($qry);
                                if($qty_finger>0){
                                    if($plan_work=='3')$work_status='11';
                                    else $work_status='10';
                                }else $work_status='0';
                                $tb_work_entries=DB::table('tb_work_entries')->where('id_employee',$dt->id_employee)->where('periode',$periode)->where('plan_actual','actual')->update([
                                    $j=>$work_status
                                ]);
                            //End Check Actual Finger
                    }
                }
            }

            $tb_department=DB::table('tb_departments')->get(['tb_departments.*','tb_departments.dept_code as department']);
            $Tomorrow=date('Y-m-d',strtotime('+1 days',strtotime($Tgl)));
            return view('page/tms/employee_shift_update',['tb_department'=>$tb_department,'department'=>$department,'periode'=>$periode,'Tgl'=>$Tgl,'Tomorrow'=>$Tomorrow,'Today'=>$Today,'halaman'=>'updatesTMS','site'=>$this->site,'menu'=>'tms','juduls'=>'Work Entry']);
        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }
    function updatesManual_backup($department,$Tgl){
        if (request()->user()->hasRole('root')||request()->user()->hasRole('tms')){
            $Today=date('Y-m-d');
            $periode=date('Y-m',strtotime($Tgl));
            $i=date('d',strtotime($Tgl));
            if(strlen($i)==1)$j='D0'.$i;
            else $j='D'.$i;
            $cek=$this->qty_work_entry_manual($department,$Tgl);
            //return $cek;
            if($cek>0){
                $tb_work_contract=$this->tb_work_contract($department);
                foreach($tb_work_contract as $dt){
                    $tgl1 = new DateTime($dt->start_implement);
                    $tgl2 = new DateTime($Tgl);
                    $diffdays = $tgl2->diff($tgl1)->days;
                    $cycle=$dt->cycle_day;
                    $diffcycle=Floor($diffdays/$cycle);
                    $modcycle=$diffdays%$cycle;
                    $modcycle++;

                    $tb_work_cycle=$this->tb_work_cycle($dt->id_work_group,$modcycle);
                    //return $tb_work_cycle;
                    foreach($tb_work_cycle as $dt2){
                        $plan_work=$dt2->id_work_time;
                        $tb_work_entries=DB::table('tb_work_entries')->where('id_employee',$dt->id_employee)->where('periode',$periode)->where('plan_actual','plan')->update([
                            $j=>$plan_work
                        ]);
                        $advance=$dt2->is_advance;
                        if($dt2->is_advance==0)$tanggal=$Tgl;
                        else $tanggal=date('Y-m-d',strtotime('-1 days',strtotime($Tgl)));
                        if($plan_work>0){
                            $check_in=$tanggal.' '.$dt2->check_in;
                            $check_out=$Tgl.' '.$dt2->check_out;
                        }else{
                            $check_in='';
                            $check_out='';
                        }
                        //Check Actual Finger
                            //Return Checkin Range
                            if(isset($check_in)&&$check_in!=''&&$check_in!=$check_out){
                                $ncdatein=$check_in;
                                $ncdateindown= date('Y-m-d H:i:s',strtotime('-3 hours',strtotime($ncdatein)));
                                $ncdateinup= date('Y-m-d H:i:s',strtotime('+5 hours',strtotime($ncdatein)));
                            }else{
                                $ncdateindown='';
                                $ncdateinup='';
                            }
                            //Return Checkout Range
                            if(isset($check_out)&&$check_out!=''&&$check_in!=$check_out){
                                $ncdateout=$check_out;
                                $ncdateoutdown= date('Y-m-d H:i:s',strtotime('-2 hours',strtotime($ncdateout)));
                                $ncdateoutup= date('Y-m-d H:i:s',strtotime('+5 hours',strtotime($ncdateout)));
                            }else{
                                $ncdateoutdown='';
                                $ncdateoutup='';
                            }
                            //Read Manual
                                $PIN=$dt->PIN;
                                $qty_manual=DB::table('tb_checktimes')->where('PIN',$PIN)->where('checktime','>=',$ncdateindown)->where('checktime','<=',$ncdateoutup)->count();
                                if($qty_manual>0){
                                    if($plan_work=='3')$work_status='21';
                                    else $work_status='20';
                                    $tb_work_entries=DB::table('tb_work_entries')->where('id_employee',$dt->id_employee)->where('periode',$periode)->where('plan_actual','actual')->where($j,'0')->update([
                                        $j=>$work_status
                                    ]);
                                }elseif($plan_work>0){
                                    $tb_work_entries=DB::table('tb_work_entries')->where('id_employee',$dt->id_employee)->where('periode',$periode)->where('plan_actual','actual')->where($j,'0')->update([
                                        $j=>'99'
                                    ]);
                                }
                            //End Check Actual Finger
                    }
                }
            }

            $tb_department=DB::table('tb_departments')->get(['tb_departments.*','tb_departments.dept_code as department']);
            $Tomorrow=date('Y-m-d',strtotime('+1 days',strtotime($Tgl)));
            return view('page/tms/employee_shift_update',['tb_department'=>$tb_department,'department'=>$department,'periode'=>$periode,'Tgl'=>$Tgl,'Tomorrow'=>$Tomorrow,'Today'=>$Today,'halaman'=>'updatesManual','site'=>$this->site,'menu'=>'tms','juduls'=>'Work Entry']);
        }else{
            return abort(403,'Anda tidak punya akses');
        }
    }

    public function qty_work_entries($id_employee,$periode){
        $tabel=DB::table('tb_work_entries')
        ->where('periode',$periode)
        ->where('id_employee',$id_employee)
        ->count();
        return $tabel;
    }


    
    function timeList(){
        $tb_work_time=$this->tb_work_times();
        return view('page/tms/time_list',['tb_work_time'=>$tb_work_time,'site'=>$this->site,'menu'=>'tms']);
    }
    function timeListSave(request $data){
        $update=DB::table('tb_work_time')->where('id',$data->id_time)->update([
            'check_in'=>$data->check_in,
            'check_out'=>$data->check_out,
            //'isactive'=>$data->isActiveTime,
            'isoma_start'=>$data->isoma_start,
            'isoma_finish'=>$data->isoma_finish
        ]);
        //if($update) 
        return redirect()->back();
        // return "Sukses";
        // else return "Gagal";
    }
    function saveManual(Request $data){
        $checker=Auth::user()->name;
        $simpan=tb_checktime::create([
            'PIN'=>$data->PIN,
            'checktime'=>$data->checktime,
            'checktime_draft'=>$data->checktime_draft,
            'checker'=>$checker,
            'NIK'=>$data->NIK,
            'employee_name'=>$data->employee_name,
            'status_kerja'=>$data->status_kerja,
            'status_checktime'=>$data->status_checktime
        ]);
        $cek='';
        if($data->checktime_draft!=''){
            $periode=date('Y-m',strtotime($data->checktime_draft));
            $tanggal=date('Y-m-d',strtotime($data->checktime_draft));
            $tb_checktime=DB::table('tb_checktimes')->where('NIK',$data->NIK)->where('checktime_draft',$data->checktime_draft)->get();
            foreach($tb_checktime as $dt){
                $id=$dt->id;
            }
            $add_gagal_finger=DB::table('tb_gagal_finger')->insert([
                'NIK'=>$data->NIK,
                'periode'=>$periode,
                'tanggal'=>$tanggal,
                'checktime_draft'=>$data->checktime_draft,
                'created_by'=>$checker,
                'id_checktime'=>$id,
                'id_we'=>$data->idworkentry2,
                'id_column'=>$data->idcolumn2
            ]);
            $pesan='Gagal Finger';
            $jam=date('H:i',strtotime($data->checktime_draft));
            if($jam>='19:00')$code='520';
            else $code='52';
            $cek=DB::table('tb_work_checktime')->where('id_work_entry',$data->idworkentry2)->where('id_column',$data->idcolumn2)->count();
            if($cek==0){
                $simpan=DB::table('tb_work_checktime')->insert([
                    'id_work_entry'=>$data->idworkentry2,
                    'id_column'=>$data->idcolumn2,
                    'entry_code'=>$code,
                    'remark'=>$pesan,
                    'status'=>0,
                    'admin'=>$checker,
                    'info'=>'',
                ]);
            }else{
                $simpan=DB::table('tb_work_checktime')->where('id_work_entry',$data->idworkentry2)->where('id_column',$data->idcolumn2)->update([
                    'entry_code'=>$code,
                    'remark'=>$pesan,
                    'status'=>0,
                    'admin'=>$checker,
                    'info'=>'',
                ]);
            }
        }
        return "Data : ".$data->checktime_draft.' Cek:'.$cek.' IDWE:'.$data->idworkentry2.' D:'.$data->idcolumn2;
    }
    function reconsileAll($tgl){
        $periode=date('Y-m',strtotime($tgl));
        $day=date('d',strtotime($tgl));
        $kolom="D".$day;
        $tb_work_entries = DB::table('tb_work_entries')
        ->leftJoin('tb_work_contract', 'tb_work_contract.id_employee', '=', 'tb_work_entries.id_employee')
        ->where('tb_work_entries.periode', $periode)
        ->where('tb_work_entries.plan_actual', 'actual')
        ->where('is_draft','0')
        ->where('daily_show','1')
        ->where('tb_work_contract.isactive','1')
        ->where($kolom,'99')
        ->get(['tb_work_entries.*']);
        $pesan="";
        foreach($tb_work_entries as $dt){
            $pesan="";
            $info="";
            $code="";
            $id_employee=$dt->id_employee;
            $idwe=$dt->id;
            // $cek=DB::table('tb_leaves')->where('category','annual')->where('id_employee',$id_employee)->where('start_leave','<=',$tgl)->where('finish_leave',$tgl)->get();
            $cek=DB::table('tb_leaves')->whereIn('category', ['annual', 'special'])->where('id_employee',$id_employee)->where('start_leave','<=',$tgl)->where('finish_leave',$tgl)->get();
            foreach($cek as $dt){
                if($dt->status_approved!=1)$pesan="Waiting Approve 1";
                elseif($dt->status_approved2!=1&&$dt->approved2!=0)$pesan="Waiting Approve 2";
                elseif($dt->status_legalized!=1)$pesan="Waiting Legalize by HR";
                $code='53';
            }
            $cek=DB::table('tb_leaves')->where('category','docter')->where('id_employee',$id_employee)->where('start_leave','<=',$tgl)->where('finish_leave',$tgl)->get();
            foreach($cek as$dt){
                if($dt->status_approved!=1)$pesan="Waiting Approve 1";
                elseif($dt->status_approved2!=1&&$dt->approved2!=0)$pesan="Waiting Approve 2";
                elseif($dt->status_legalized!=1)$pesan="Waiting Legalize by HR";
                $code='54';
            }
            $cek=DB::table('tb_izins')->where('category','A')->where('id_employee',$id_employee)->where('apply_date',$tgl)->get();
            foreach($cek as$dt){
                if($dt->status_disetujui!=1)$pesan="Waiting Approve ";
                elseif($dt->status_personalia!=1)$pesan="Waiting Legalize by HR";
                $code='55';
            }
            $cek=DB::table('tb_izins')->where('category','B')->where('id_employee',$id_employee)->where('apply_date',$tgl)->get();
            foreach($cek as$dt){
                if($dt->status_disetujui!=1)$pesan="Waiting Approve";
                elseif($dt->status_personalia!=1)$pesan="Waiting Legalize by HR";
                $code='731';
                $info="Form B";
            }
            $cek=DB::table('tb_izins')->where('category','C')->where('id_employee',$id_employee)->where('apply_date',$tgl)->get();
            foreach($cek as$dt){
                if($dt->status_disetujui!=1)$pesan="Waiting Approve";
                elseif($dt->status_personalia!=1)$pesan="Waiting Legalize by HR";
                $code='732';
                $info="Form C";
            }
            $cek=DB::table('tb_izins')->where('category','D')->where('id_employee',$id_employee)->where('apply_date',$tgl)->get();
            foreach($cek as$dt){
                if($dt->status_disetujui!=1)$pesan="Waiting Approve";
                elseif($dt->status_personalia!=1)$pesan="Waiting Legalize by HR";
            $code='733';
            $info="Form D";
            }
            if($pesan!=''){
                $admin=Auth::user()->name;
                $day=date('d',strtotime($tgl));
                $id_column='D'.$day;
                $cek=DB::table('tb_work_checktime')->where('id_work_entry',$idwe)->where('id_column',$id_column)->count();
                if($cek==0){
                    $simpan=DB::table('tb_work_checktime')->insert([
                        'id_work_entry'=>$idwe,
                        'id_column'=>$id_column,
                        'entry_code'=>$code,
                        'remark'=>$pesan,
                        'status'=>0,
                        'admin'=>$admin,
                        'info'=>$info,
                    ]);
                }else{
                    $simpan=DB::table('tb_work_checktime')->where('id_work_entry',$idwe)->where('id_column',$id_column)->update([
                        'entry_code'=>$code,
                        'remark'=>$pesan,
                        'status'=>0,
                        'admin'=>$admin,
                        'info'=>$info,
                    ]);
                }

            }
        }
        if($pesan!='')return 'Berhasil';
        else return $tb_work_entries;
    }
}