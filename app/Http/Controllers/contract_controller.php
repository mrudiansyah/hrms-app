<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\KSKImport;

use DateTime;
use App\Http\Controllers\mail_controller;
use PDF;
use Auth;
use App\Mail\ksk_distribute;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\HcmisController;
use Illuminate\Http\Request as HttpRequest;


class contract_controller extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }
    function index(){
        $tb_employee=DB::table('tb_employees')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->leftjoin('tb_statuses', function ($join) {
            $join->on('tb_statuses.id_employee', '=', 'tb_employees.id');
                 //->where('tb_statuses.active', '1');
        })
        ->where('delete','0')
        ->where('tb_employees.status','1')
        ->orderby('tb_employees.employee_name','asc')
        ->orderby('active','desc')
        ->get(['tb_employees.PIN','tb_employees.id as idemployee','tb_employees.join_date as joindate','tb_employees.NIK','tb_employees.employee_name','tb_employees.gender','tb_employees.status','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_name','tb_statuses.*']);

        return view('page/admin/m_employee/contract',['tb_employee'=>$tb_employee,'menu'=>'employee','submenu'=>'contract','Judul'=>'Employee List (Over All)']);
    }
    function nonactive($periode){
      if($periode!=0){
        $kalendar=CAL_GREGORIAN;
        $hari_awal=$periode.'-01';
        $thn=date('Y',strtotime($periode.'-01'));
        $bln=date('m',strtotime($periode.'-01'));
        $hariakhir=cal_days_in_month($kalendar,$bln,$thn);
        $hari_akhir=$periode.'-'.$hariakhir;
      }


      $now=date('Y-m-d H:i:s');
        $tb_employee=DB::table('tb_employees')
        ->leftjoin('tb_employee_detail','tb_employee_detail.id_employee','=','tb_employees.id')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->leftjoin('tb_statuses', function ($join) {
            $join->on('tb_statuses.id_employee', '=', 'tb_employees.id')
                 ->where('tb_statuses.active', '1');
        })
        ->where('delete','0')
        ->where('tb_employees.status','0');
        if($periode!=0){
          $tb_employee=$tb_employee->where('start_contract','>=',$hari_awal)->where('start_contract','<=',$hari_akhir);
        }
        $tb_employee=$tb_employee->orderby('employee_name','asc')
        ->get(['tb_employees.PIN','tb_employee_detail.nomor_ktp','tb_employees.id as idemployee','tb_employees.join_date as joindate','tb_employees.NIK','tb_employees.employee_name','tb_employees.gender','tb_employees.status','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_name','tb_statuses.*']);
        return view('page/admin/m_employee/contract_inactive',['tb_employee'=>$tb_employee,'periode'=>$periode,'menu'=>'employee','submenu'=>'contract','Judul'=>'Employee (Non Active)']);
    }
    function active(){
      $tb_employee=DB::table('tb_employees')
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
      ->where('tb_statuses.active','1')

      ->orderby('tb_employees.employee_name','asc')
      ->orderby('active','desc')
      ->get(['tb_employees.PIN','tb_employees.id as idemployee','tb_employees.join_date as joindate','tb_employees.NIK','tb_employees.employee_name','tb_employees.gender','tb_employees.status','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_name','tb_statuses.*']);

      $tb=DB::table('view_migration_hcmis')->where('hcmis_id','!=','0')->where('hcmis_status','=',0)->orderby('id_employee','asc')->skip(3)->take(50)->get();
      foreach($tb as $data){

          //HCMIS
          $sync_hcmis=1;
          if ($sync_hcmis==1) {
            if($data->contract_name=='Permanen')$contract_type='PKWTT';
            else $contract_type='PKWT';
            if($data->contract_name=='Permanen')$employement='1';
            elseif($data->contract_name=='Kontrak')$employement='2';
            elseif($data->contract_name=='Other')$employement='3';
            elseif($data->contract_name=='Magang')$employement='4';
            elseif($data->contract_name=='Pensiun Dini')$employement='5';
            else $employement='7';
            $tb1=DB::table('tb_employees')->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')->where('tb_employees.id',$data->id_employee)->get(['tb_employees.*','tb_departments.hcmis_id','tb_departments.division_id']);
            try {
                foreach($tb1 as $dt){
                // normalize gender to 'male'|'female'
                $g = strtolower(trim($dt->gender ?? ''));
                if ($g === 'laki-laki' || $g === 'male') $gender = 'male';
                else $gender = 'female';

                $employee_number = $dt->NIK ?? ($data->NIK ?? null);
                $full_name = $dt->employee_name ?? ($data->employee_name ?? null);

                // normalize and validate dates for HCMIS
                $start_date = $data->start_contract ?? null;
                $end_date = $data->finish_contract ?? null;
                if ($data->contract_name == 'Permanen') {
                  // permanen may not require end_date
                  $end_date = null;
                } else {
                  // if end_date missing or earlier than start_date, set end_date = start_date
                  if (empty($end_date) && !empty($start_date)) {
                    $end_date = $start_date;
                    Log::warning('HCMIS date normalization: end_date missing, set to start_date', ['start_date' => $start_date, 'end_date' => $end_date, 'employee_number' => $employee_number]);
                  } elseif (!empty($start_date) && !empty($end_date) && strtotime($end_date) < strtotime($start_date)) {
                    Log::warning('HCMIS date normalization: end_date earlier than start_date, adjusting end_date to start_date', ['start_date' => $start_date, 'original_end_date' => $end_date, 'employee_number' => $employee_number]);
                    $end_date = $start_date;
                  }
                }

                $payload = [
                  'company_code'      => config('hcmis.company_code'),
                  'org_code'          => config('hcmis.org_code'),
                  'username'          => config('hcmis.username'),
                  'password'          => config('hcmis.password'),
                  'employee_number'   => $employee_number,
                  'full_name'         => $full_name,
                  'gender'            => $gender,
                  'contract_type'     => $contract_type,
                  'employment_status_id' => $employement,
                  'start_date'        => $start_date,
                  'end_date'          => $end_date,
                  'division_id'       => $dt->division_id,
                  'department_id'     => $dt->hcmis_id,
                ];

                // if required fields missing, log and skip HCMIS call
                if (empty($payload['employee_number']) || empty($payload['full_name'])) {
                  Log::warning('HCMIS sync skipped: missing required fields', $payload);
                  continue;
                }

                Log::info('HCMIS sync payload', $payload);

                $hcmisController = app(HcmisController::class);
                // Insert-only: always try to create the employee in HCMIS.
                $response = $hcmisController->employeesStore(new HttpRequest($payload));
                $hcmisResult = json_decode($response->getContent(), true);
                Log::info('HCMIS insertContract response', (array) $hcmisResult);

                // If HCMIS reports the record already exists, just log and continue.
                if (!empty($hcmisResult['status_code']) && $hcmisResult['status_code'] === 422 && strpos(strtolower($hcmisResult['message'] ?? ''), 'already been taken') !== false) {
                  Log::info('HCMIS insert conflict: record already exists, skipping update', ['employees_number' => $employee_number, 'hcmis_result' => $hcmisResult]);
                }
              }
            } catch (\Exception $e) {
              Log::error('HCMIS sync exception: '.$e->getMessage());
            }

          }
      }

      return view('page/admin/m_employee/contract',['tb_employee'=>$tb_employee,'menu'=>'employee','submenu'=>'contract','submenu'=>'contract','Judul'=>'Employee (Active)']);
    }
    function permanen(){
      $tb_employee=DB::table('tb_employees')
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
      ->where('tb_statuses.contract_name', 'Permanen')
      ->orderby('tb_employees.employee_name','asc')
      ->orderby('active','desc')
      ->get(['tb_employees.PIN','tb_employees.id as idemployee','tb_employees.join_date as joindate','tb_employees.NIK','tb_employees.employee_name','tb_employees.gender','tb_employees.status','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_name','tb_statuses.*']);

      return view('page/admin/m_employee/contract',['tb_employee'=>$tb_employee,'menu'=>'employee','submenu'=>'contract','submenu'=>'contract','Judul'=>'Employee (Permanen)']);
    }
    function kontrak(){
      $tb_employee=DB::table('tb_employees')
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
      ->where('tb_statuses.contract_name','Kontrak')
      ->orderby('finish_contract','asc')
      ->get(['tb_employees.PIN','tb_employees.id as idemployee','tb_employees.join_date as joindate','tb_employees.NIK','tb_employees.employee_name','tb_employees.gender','tb_employees.status','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_name','tb_statuses.*']);

      return view('page/admin/m_employee/contract(custome)',['tb_employee'=>$tb_employee,'menu'=>'employee','submenu'=>'contract','submenu'=>'contract','Judul'=>'Employee (Kontrak)']);
    }
    function satu(){
      $tb_employee=DB::table('tb_employees')
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
      ->where('tb_statuses.contract_name','Kontrak 1')
      ->orderby('tb_employees.employee_name','asc')
      ->orderby('active','desc')
      ->get(['tb_employees.PIN','tb_employees.id as idemployee','tb_employees.join_date as joindate','tb_employees.NIK','tb_employees.employee_name','tb_employees.gender','tb_employees.status','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_name','tb_statuses.*']);

      return view('page/admin/m_employee/contract',['tb_employee'=>$tb_employee,'menu'=>'employee','submenu'=>'contract','submenu'=>'contract','Judul'=>'Employee (Kontrak 1)']);
    }
    function dua(){
      $tb_employee=DB::table('tb_employees')
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
      ->where('tb_statuses.contract_name','Kontrak 2')
      ->orderby('tb_employees.employee_name','asc')
      ->orderby('active','desc')
      ->get(['tb_employees.PIN','tb_employees.id as idemployee','tb_employees.join_date as joindate','tb_employees.NIK','tb_employees.employee_name','tb_employees.gender','tb_employees.status','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_name','tb_statuses.*']);

      return view('page/admin/m_employee/contract',['tb_employee'=>$tb_employee,'menu'=>'employee','submenu'=>'contract','submenu'=>'contract','Judul'=>'Employee (Kontrak 2)']);
    }
    function pembaharuan(){
      $tb_employee=DB::table('tb_employees')
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
      ->where('tb_statuses.contract_name','Pembaharuan')
      ->orderby('tb_employees.employee_name','asc')
      ->orderby('active','desc')
      ->get(['tb_employees.PIN','tb_employees.id as idemployee','tb_employees.join_date as joindate','tb_employees.NIK','tb_employees.employee_name','tb_employees.gender','tb_employees.status','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_name','tb_statuses.*']);

      return view('page/admin/m_employee/contract',['tb_employee'=>$tb_employee,'menu'=>'employee','submenu'=>'contract','submenu'=>'contract','Judul'=>'Employee (Pembaharuan)']);
    }
    function magang(){
      $tb_employee=DB::table('tb_employees')
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
      //->where('delete','0')
      //->where('tb_employees.status','1')
      ->where('tb_statuses.active', '1')
      ->where('tb_statuses.contract_name','Magang')
      ->orderby('tb_employees.employee_name','asc')
      ->orderby('active','desc')
      ->get(['tb_employees.PIN','tb_employees.id as idemployee','tb_employees.join_date as joindate','tb_employees.NIK','tb_employees.employee_name','tb_employees.gender','tb_employees.status','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_name','tb_statuses.*']);

      return view('page/admin/m_employee/contract(custome)',['tb_employee'=>$tb_employee,'menu'=>'employee','submenu'=>'contract','submenu'=>'contract','Judul'=>'Employee (Magang)']);
    }
    function other(){
      $tb_employee=DB::table('tb_employees')
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
      ->where(function ($query) {
            $query->where('tb_statuses.contract_name','Other');
        })
      ->orderby('tb_employees.employee_name','asc')
      ->orderby('active','desc')
      ->get(['tb_employees.PIN','tb_employees.id as idemployee','tb_employees.join_date as joindate','tb_employees.NIK','tb_employees.employee_name','tb_employees.gender','tb_employees.status','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_name','tb_statuses.*']);

      return view('page/admin/m_employee/contract',['tb_employee'=>$tb_employee,'menu'=>'employee','submenu'=>'contract','Judul'=>'Employee (Others)']);
    }
    function draft(){
      $tb_employee=DB::table('tb_employees')
      ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
      ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
      ->leftjoin('tb_statuses', function ($join) {
          $join->on('tb_statuses.id_employee', '=', 'tb_employees.id')
          ->where('tb_statuses.active', '1');
      })
      ->where(function($query) {
          $query->where('tb_employees.delete','0')
          ->where('tb_employees.status','1')
          ->whereNotExists(function ($query) {
              $query->select(DB::raw(1))
                    ->from('tb_statuses')
                    ->whereColumn('tb_statuses.id_employee', 'tb_employees.id');
          });
      })
      ->orWhere(function($query) {
          $query->where('tb_employees.delete','0')
          ->where('tb_employees.status','1')
          ->whereExists(function ($query) {
              $query->select(DB::raw(1))
                    ->from('tb_statuses')
                    ->whereColumn('tb_statuses.id_employee', 'tb_employees.id')
                    ->where('tb_statuses.contract_name','End Contract');
          });
      })
      ->orderby('tb_employees.employee_name','asc')
      ->orderby('active','desc')
      ->get(['tb_employees.PIN','tb_employees.id as idemployee','tb_employees.join_date as joindate','tb_employees.NIK','tb_employees.employee_name','tb_employees.gender','tb_employees.status','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_name','tb_statuses.*']);

      //return $tb_employee;
      //foreach($tb_employee as $dt){DB::table('tb_employees')->where('id',$dt->idemployee)->update(['status'=>'0']);}
      return view('page/admin/m_employee/contract(custome)',['tb_employee'=>$tb_employee,'menu'=>'employee','submenu'=>'contract','submenu'=>'contract','Judul'=>'Employee (Draft)']);
    }
    function newContract(Request $data){
      //return $data->id_contract;
      $now=date('Y-m-d H:i:s');
        $tgl1 = new DateTime($data->start_contract);
        $tgl2 = new DateTime($data->finish_contract);
        $diffdays = $tgl2->diff($tgl1)->days;

        $add_contract=DB::table('tb_statuses')->insert([
            'id_employee'=>$data->id_employee,
            'join_date'=>$data->join_date,
            'contract_name'=>$data->contract_name,
            'revise'=>'0',
            'start_contract'=>$data->start_contract,
            'finish_contract'=>$data->finish_contract,
            'balance'=>$diffdays,
            'active'=>'1',
            'contract_ref'=>$data->id_agreement,
        ]);
        if($add_contract){
          $id_status=0;
          $tb_status=DB::table('tb_statuses')->where('contract_ref',$data->id_agreement)->get();
          foreach($tb_status as $dt){
            $id_status=$dt->id;
          }
          $tb_agreement=DB::table('tb_contract')->where('id',$data->id_agreement)->update([
            'id_status'=>$id_status,
          ]);

          if($data->id_contract!=''){
            DB::table('tb_statuses')->where('id',$data->id_contract)->update(['active'=>'0']);
            if($data->contract_name!='Resign'&&$data->contract_name!='Kabur'&&$data->contract_name!='Magang'&&$data->contract_name!='SAB'){
              //Kompensasi

              date_default_timezone_set("Asia/Jakarta");
              $today=date('Y-m-d');
              $tb_status=DB::table('tb_statuses')
              ->leftjoin('tb_salaries','tb_salaries.id_employee','=','tb_statuses.id_employee')
              ->leftjoin('tb_employees','tb_employees.id','=','tb_statuses.id_employee')
              ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
              ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
              ->select('tb_employees.NIK','tb_employees.employee_name','tb_salaries.*','tb_statuses.id as id_status','tb_statuses.join_date','tb_statuses.contract_name','tb_statuses.start_contract','tb_statuses.finish_contract','tb_departments.dept_name','tb_positions.position_name')
              ->where('tb_statuses.id',$data->id_contract)->get();
              foreach($tb_status as $dt){
                if($dt->contract_name=='Kontrak'){
                  $start_contract=date('Y-m-d',strtotime('-1 days',strtotime($dt->start_contract)));
                  if($start_contract<'2020-11-01')$start_contract='2020-10-31';
                  //$finish_contract=$dt->finish_contract;
                  $finish_contract=date('Y-m-d',strtotime('-1 days',strtotime($data->start_contract)));
                  $datetime1 = date_create($start_contract);
                  $datetime2 = date_create($finish_contract);
                  $bulan = date_diff($datetime1, $datetime2);
                  $lama_tahun=$bulan->format('%y');
                  $lama_bulan=$bulan->format('%m');
                  $lama_contract=$lama_tahun*12+$lama_bulan;
                  $PPH21_Insentif=0;
                  $PPH21=0;
                  $total_pendapatan=$dt->salary+$PPH21_Insentif;
                  $kompensasi=$lama_contract*$dt->salary/12;
                  $total_kompensasi=$kompensasi+$PPH21_Insentif-$PPH21;
                  $add_kompensasi=DB::table('tb_kompensasi')->insert([
                    'id_employee'=>$dt->id_employee,
                    'nama'=>$dt->employee_name,
                    'NIK'=>$dt->NIK,
                    'Bagian'=>$dt->dept_name,
                    'Jabatan'=>$dt->position_name,
                    'Upah_Pokok'=>$dt->upah_pokok,
                    'Tunjangan_Jabatan'=>$dt->tunjangan_jabatan,
                    'Tunjangan_Prestasi'=>$dt->tunjangan_prestasi,
                    'Tunjangan_Skill'=>$dt->tunjangan_skill,
                    'PPH21_Insentif'=>$PPH21_Insentif,
                    'Total_Pendapatan'=>$total_pendapatan,
                    'Start_Kontrak'=>$dt->start_contract,
                    'Finish_Kontrak'=>$finish_contract,
                    'Jumlah_Bulan'=>$lama_contract,
                    'Kompensasi'=>$kompensasi,
                    'PPH21'=>$PPH21,
                    'Total_Kompensasi'=>$total_kompensasi,
                    'send_mail'=>0,
                    'pay_date'=>$today,
                    'contract_name'=>$data->contract_name,
                  ]);
                }
              }
      
              //Kompensasi End
            }
          }
          if($data->contract_name=='Resign'||$data->contract_name=='Kabur'||$data->contract_name=='End Contract'||$data->contract_name=='Pensiun'||$data->contract_name=='Pensiun Dini'||$data->contract_name=='PHK'){
              $deaktif=DB::table('tb_employees')->where('id',$data->id_employee)->update(['status'=>'0']);
              $deaktif_slpj=DB::table('tb_salaries')->where('id',$data->id_employee)->update(['status'=>'0']);
              //NonActive Cuti
              $deaktif=DB::table('tb_employee_leaves')->where('id_employee',$data->id_employee)->update(['status'=>'0']);
              //Nonactive TMS
              $periode=date('Y-m',strtotime($now));
              $tb_we=DB::table('tb_work_entries')->where('id_employee',$data->id_employee)->where('periode',$periode)->where('daily_show','1')->update(['daily_show'=>'0']);
              //Ganti atasan
                $table1=DB::table('tb_employees')->where('id',$data->id_employee)->get();
                foreach($table1 as $tb1){
                  $atasan_baru=$tb1->leader_id;
                }
                $update_leader=DB::table('tb_employees')->where('leader_id',$data->id_employee)->update([
                  'leader_id'=>$atasan_baru,
                  'updated_at'=>$now
                ]);

                //Update TMS
                $update_tms=DB::table('tb_work_contract')->where('id_employee',$data->id_employee)->update([
                  'isactive'=>'0'
                ]);

              //End ganti Atasan
          }else{
            $reaktif=DB::table('tb_employees')->where('id',$data->id_employee)->update(['status'=>'1']);
          }

          //HCMIS
          $sync_hcmis=1;
          if ($sync_hcmis==1) {
            if($data->contract_name=='Permanen')$contract_type='PKWTT';
            else $contract_type='PKWT';
            if($data->contract_name=='Permanen')$employement='1';
            elseif($data->contract_name=='Kontrak')$employement='2';
            elseif($data->contract_name=='Other')$employement='3';
            elseif($data->contract_name=='Magang')$employement='4';
            elseif($data->contract_name=='Pensiun Dini')$employement='5';
            else $employement='7';
            $tb1=DB::table('tb_employees')->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')->where('tb_employees.id',$data->id_employee)->get(['tb_employees.*','tb_departments.hcmis_id','tb_departments.division_id']);
            try {
                foreach($tb1 as $dt){
                // normalize gender to 'male'|'female'
                $g = strtolower(trim($dt->gender ?? ''));
                if ($g === 'laki-laki' || $g === 'male') $gender = 'male';
                else $gender = 'female';

                $employee_number = $dt->NIK ?? ($data->NIK ?? null);
                $full_name = $dt->employee_name ?? ($data->employee_name ?? null);

                // normalize and validate dates for HCMIS
                $start_date = $data->start_contract ?? null;
                $end_date = $data->finish_contract ?? null;
                if ($data->contract_name == 'Permanen') {
                  // permanen may not require end_date
                  $end_date = null;
                } else {
                  // if end_date missing or earlier than start_date, set end_date = start_date
                  if (empty($end_date) && !empty($start_date)) {
                    $end_date = $start_date;
                    Log::warning('HCMIS date normalization: end_date missing, set to start_date', ['start_date' => $start_date, 'end_date' => $end_date, 'employee_number' => $employee_number]);
                  } elseif (!empty($start_date) && !empty($end_date) && strtotime($end_date) < strtotime($start_date)) {
                    Log::warning('HCMIS date normalization: end_date earlier than start_date, adjusting end_date to start_date', ['start_date' => $start_date, 'original_end_date' => $end_date, 'employee_number' => $employee_number]);
                    $end_date = $start_date;
                  }
                }

                $payload = [
                  'company_code'      => config('hcmis.company_code'),
                  'org_code'          => config('hcmis.org_code'),
                  'username'          => config('hcmis.username'),
                  'password'          => config('hcmis.password'),
                  'employee_number'   => $employee_number,
                  'full_name'         => $full_name,
                  'gender'            => $gender,
                  'contract_type'     => $contract_type,
                  'employment_status_id' => $employement,
                  'start_date'        => $start_date,
                  'end_date'          => $end_date,
                  'division_id'       => $dt->division_id,
                  'department_id'     => $dt->hcmis_id,
                ];

                // if required fields missing, log and skip HCMIS call
                if (empty($payload['employee_number']) || empty($payload['full_name'])) {
                  Log::warning('HCMIS sync skipped: missing required fields', $payload);
                  continue;
                }

                Log::info('HCMIS sync payload', $payload);

                $hcmisController = app(HcmisController::class);
                // Insert-only: always try to create the employee in HCMIS.
                $response = $hcmisController->employeesStore(new HttpRequest($payload));
                $hcmisResult = json_decode($response->getContent(), true);
                Log::info('HCMIS insertContract response', (array) $hcmisResult);

                // If HCMIS reports the record already exists, just log and continue.
                if (!empty($hcmisResult['status_code']) && $hcmisResult['status_code'] === 422 && strpos(strtolower($hcmisResult['message'] ?? ''), 'already been taken') !== false) {
                  Log::info('HCMIS insert conflict: record already exists, skipping update', ['employees_number' => $employee_number, 'hcmis_result' => $hcmisResult]);
                }
              }
            } catch (\Exception $e) {
              Log::error('HCMIS sync exception: '.$e->getMessage());
            }

          }

          // send to HCMIS for sync and log payload/response for debugging

          //End HCMIS

          $fields='contract_name';
          $before='';
          $after=$data->contract_name;
          if($data->id_contract=='')$id_table=$data->id_employee;
          else $id_table=$data->id_contract;
          session([
              'table_name'=>'tb_statuses',
              'id_table'=>$id_table,
              'activity'=>'insert',
              'fields'=>$fields,
              'before'=>$before,
              'after'=>$after
          ]);
          app('App\Http\Controllers\log_controller')->index();
          return redirect()->back();
        }

    }
    function ksk($periode){
      $tb_leader=DB::table('tb_employees')
      ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
      ->where('tb_employees.status','1')
      ->where('position_index','>','1')
      ->orderby('employee_name','asc')->get('tb_employees.*');
    
      date_default_timezone_set("Asia/Jakarta");
      $kalendar=CAL_GREGORIAN;
      $today=date('Y-m-d');
      $thn1=date('Y',strtotime($today));
      $bln1=date('m',strtotime($today));
      $awal_bulan=date('Y-m-d',strtotime($thn1.'-'.$bln1.'-01'));
      $hariakhir1=cal_days_in_month($kalendar,$bln1,$thn1);
      $akhir_bulan=date('Y-m-d',strtotime($thn1.'-'.$bln1.'-'.$hariakhir1));
      
      if($periode==0)$start=date('Y-m-d',strtotime('+1 days',strtotime($akhir_bulan)));
      else $start=date('Y-m-d',strtotime($periode.'-01'));
      $thn2=date('Y',strtotime($start));
      $bln2=date('m',strtotime($start));
      $hariakhir2=cal_days_in_month($kalendar,$bln2,$thn2);
      $finish=date('Y-m-d',strtotime($thn2.'-'.$bln2.'-'.$hariakhir2));
      if($periode==0)$periode=$thn2.'-'.$bln2;

      $tb_employee=DB::table('tb_statuses')
      ->leftjoin('tb_employees','tb_employees.id','=','tb_statuses.id_employee')
      ->leftjoin('tb_employees as tb_employees2','tb_employees.leader_id','=','tb_employees2.id')
      ->leftjoin('tb_employees as tb_employees3','tb_employees2.leader_id','=','tb_employees3.id')
      ->leftjoin('tb_employees as tb_employees4','tb_employees3.leader_id','=','tb_employees4.id')
      ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
      ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
      ->where('tb_statuses.active',1)->where('tb_employees.status','1')
      //->where('tb_statuses.finish_contract','<',$today)
      //->where('tb_statuses.finish_contract','>=',$awal_bulan)
      //->where('tb_statuses.finish_contract','<=',$akhir_bulan)
      ->where('tb_statuses.finish_contract','>=',$start)
      ->where('tb_statuses.finish_contract','<=',$finish)
      ->where('tb_statuses.contract_name','!=','Permanen')
      ->where('tb_statuses.contract_name','!=','PSAB')
      ->where('tb_statuses.contract_name','!=','SAB')
      ->where('tb_statuses.contract_name','!=','NASKA')
      ->where('tb_statuses.contract_name','!=','PKL')
      ->where('tb_statuses.contract_name','!=','Other')
      ->where('tb_statuses.contract_name','!=','End Contract')
      ->orderby('tb_statuses.finish_contract','asc')
      ->get(['tb_employees.PIN','tb_employees.id as idemployee','tb_employees.join_date as joindate','tb_employees.NIK','tb_employees.gender','tb_employees.status','tb_employees.employee_name','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_name','tb_statuses.*','tb_employees2.employee_name as leader1','tb_employees3.employee_name as leader2','tb_employees4.employee_name as leader3','tb_employees2.id as id_leader1','tb_employees3.id as id_leader2','tb_employees4.id as id_leader3']);
      //return $tb_employee;
      $leader_status='1';
      foreach($tb_employee as $dt){
        //if($dt->id_leader1==''||$dt->id_leader2==''||$dt->id_leader2=='')$leader_status='0';
        if($dt->id_leader1=='')$leader_status='0';
      }
      $judul="Contract List (".$periode.")";
      return view('page/admin/m_employee/ksk_draft',['tb_employee'=>$tb_employee,'tb_leader'=>$tb_leader,'leader_status'=>$leader_status,'periode'=>$periode,'menu'=>'employee','submenu'=>'contract','submenu'=>'contract','Judul'=>$judul]);

    }
    function kskCreate($periode){
      $admin=Auth::user()->name;
      
      date_default_timezone_set("Asia/Jakarta");
      $kalendar=CAL_GREGORIAN;
      $sekarang=date('Y-m-d H:i:s');
      $today=date('Y-m-d');
      $thn1=date('Y',strtotime($today));
      $bln1=date('m',strtotime($today));
      $awal_bulan=date('Y-m-d',strtotime($thn1.'-'.$bln1.'-01'));
      $hariakhir1=cal_days_in_month($kalendar,$bln1,$thn1);
      $akhir_bulan=date('Y-m-d',strtotime($thn1.'-'.$bln1.'-'.$hariakhir1));
      
      if($periode==0)$start=date('Y-m-d',strtotime('+1 days',strtotime($akhir_bulan)));
      else $start=date('Y-m-d',strtotime($periode.'-01'));
      $thn2=date('Y',strtotime($start));
      $bln2=date('m',strtotime($start));
      $hariakhir2=cal_days_in_month($kalendar,$bln2,$thn2);
      $finish=date('Y-m-d',strtotime($thn2.'-'.$bln2.'-'.$hariakhir2));

      if($periode==0)$periode=$thn2.'-'.$bln2;

      $bulan = date('n',strtotime($start));
      $romawi = $this->getRomawi($bulan);
      $kunci='%'.$romawi.'/'.$thn2;
      
      $qty_ksk=0;
      $qty_ksk=DB::table('tb_ksk')->where('no_ksk','like',$kunci)->count();
 
      if($qty_ksk==0){
        //$this->kskRefresh($periode);
      }

      // Query utama
      $tb_ksk = DB::table('tb_ksk')
          ->leftJoin('tb_departments', 'tb_departments.id', '=', 'tb_ksk.dept_id')
          ->leftJoin('tb_employees as tb_employees1', 'tb_employees1.id', '=', 'tb_ksk.approval1')
          ->leftJoin('tb_employees as tb_employees2', 'tb_employees2.id', '=', 'tb_ksk.approval2')
          ->leftJoin('tb_employees as tb_employees3', 'tb_employees3.id', '=', 'tb_ksk.approval3')
          ->leftJoin('tb_employees as tb_employees4', 'tb_employees4.id', '=', 'tb_ksk.approval4')
          ->leftJoin('tb_employees as tb_employees5', 'tb_employees5.id', '=', 'tb_ksk.approval5')
          ->leftJoin('tb_employees as tb_employees6', 'tb_employees6.id', '=', 'tb_ksk.approval6')
          ->leftJoin('tb_ksk_target', function($join) {
              $join->on('tb_ksk_target.periode', '=', 'tb_ksk.periode')
                  ->on('tb_ksk_target.dept_id', '=', 'tb_ksk.dept_id');
          })
          ->select([
              'tb_ksk.*',
              'tb_departments.dept_code',
              'tb_employees1.id as approval1',
              'tb_employees2.id as approval2',
              'tb_employees3.id as approval3',
              'tb_employees4.id as approval4',
              'tb_employees5.id as approval5',
              'tb_employees6.id as approval6',
              'tb_employees1.employee_name as approvalname1',
              'tb_employees2.employee_name as approvalname2',
              'tb_employees3.employee_name as approvalname3',
              'tb_employees4.employee_name as approvalname4',
              'tb_employees5.employee_name as approvalname5',
              'tb_employees6.employee_name as approvalname6',
              'tb_ksk_target.permanent_target',
              'tb_ksk_target.contract_target',
              'tb_ksk_target.magang_target',
              'tb_ksk_target.permanent_actual',
              'tb_ksk_target.contract_actual',
              'tb_ksk_target.magang_actual',
              'tb_ksk_target.permanent_remain',
              'tb_ksk_target.contract_remain',
              'tb_ksk_target.magang_remain',
          ])
          ->where('no_ksk', 'like', $kunci)
          ->where('tb_ksk.periode', $periode)
          ->orderBy('no_ksk', 'asc')
          ->get();

      // Proses data untuk view
      $data = [];
      $no = 0;
      foreach ($tb_ksk as $dt) {
          $no++;
          
          // Hitung approval status
          $qty_approval = 0;
          if ($dt->approval1 > 0 && $dt->approval1_status == 1) $qty_approval++;
          if ($dt->approval2 > 0 && $dt->approval2_status == 1) $qty_approval++;
          if ($dt->approval3 > 0 && $dt->approval3_status == 1) $qty_approval++;
          if ($dt->approval4 > 0 && $dt->approval4_status == 1) $qty_approval++;
          if ($dt->approval5 > 0 && $dt->approval5_status == 1) $qty_approval++;
          if ($dt->approval6 > 0 && $dt->approval6_status == 1) $qty_approval++;
          
          // Query tb_ksk_detail (pindahkan dari view)
          $qty_total = DB::table('tb_ksk_detail')
              ->where('id_ksk', $dt->id)
              ->count();
              
          $qty_performance = DB::table('tb_ksk_detail')
              ->where('id_ksk', $dt->id)
              ->whereNull('performance')
              ->count();
          
          // Tentukan status dan warna
          $performance_status = 0;
          $warna = 'btn-success';
          if ($qty_performance > 0) {
              $warna = 'btn-warning';
              $performance_status = 0;
          }
          
          // Cek quota
          $quota_status = 1;
          if ($dt->permanent_target == '') {
              $quota_status = 0;
          }
          
          // Status lock
          $lock_status = 0;
          if ($dt->approval1_status == 1) {
              $lock_status = 1;
          }
          
          // Simpan ke array
          $data[] = [
              'no' => $no,
              'id' => $dt->id,
              'no_ksk' => $dt->no_ksk,
              'dept_code' => $dt->dept_code,
              'dept_id' => $dt->dept_id,
              'approval1' => $dt->approval1,
              'approval2' => $dt->approval2,
              'approval3' => $dt->approval3,
              'approval4' => $dt->approval4,
              'approval5' => $dt->approval5,
              'approval6' => $dt->approval6,
              'approval1_status' => $dt->approval1_status,
              'approval2_status' => $dt->approval2_status,
              'approval3_status' => $dt->approval3_status,
              'approval4_status' => $dt->approval4_status,
              'approval5_status' => $dt->approval5_status,
              'approval6_status' => $dt->approval6_status,
              'approvalname1' => $dt->approvalname1,
              'approvalname2' => $dt->approvalname2,
              'approvalname3' => $dt->approvalname3,
              'approvalname4' => $dt->approvalname4,
              'approvalname5' => $dt->approvalname5,
              'approvalname6' => $dt->approvalname6,
              'qty_approval' => $qty_approval,
              'qty_total' => $qty_total,
              'qty_performance' => $qty_performance,
              'warna' => $warna,
              'performance_status' => $performance_status,
              'quota_status' => $quota_status,
              'lock_status' => $lock_status,
              'permanent_target' => $dt->permanent_target,
              'contract_target' => $dt->contract_target,
              'magang_target' => $dt->magang_target,
              'permanent_actual' => $dt->permanent_actual,
              'contract_actual' => $dt->contract_actual,
              'magang_actual' => $dt->magang_actual,
              'periode' => $periode,
          ];
      }
      //return $tb_ksk;
      $judul="KSK List (".$periode.")";
      $tb_ksk_lock=DB::table('tb_ksk_lock')->where('periode',$periode)->get();
      $status_lock=0;
      foreach ($tb_ksk_lock as $dt) {
        $status_lock=$dt->is_lock;
      }
      return view('page/admin/m_employee/ksk',['tb_ksk'=>$tb_ksk,'data'=>$data,'periode'=>$periode,'status_lock'=>$status_lock,'menu'=>'employee','submenu'=>'contract','submenu'=>'contract','Judul'=>$judul]);
      //return redirect('/Status/KSK/Detail/'.$periode)->with(['success' => "KSK Created"]);
    }
    function kskRefresh($periode){
      $admin=Auth::user()->name;
      $email=Auth::user()->email;
      $cek1=DB::table('tb_emails')->where('email_address',$email)->get();
      foreach($cek1 as $dt){$id_user=$dt->id_employee;}
    
      date_default_timezone_set("Asia/Jakarta");
      $kalendar=CAL_GREGORIAN;
      $sekarang=date('Y-m-d H:i:s');
      $today=date('Y-m-d');
      $thn1=date('Y',strtotime($today));
      $bln1=date('m',strtotime($today));
      $awal_bulan=date('Y-m-d',strtotime($thn1.'-'.$bln1.'-01'));
      $hariakhir1=cal_days_in_month($kalendar,$bln1,$thn1);
      $akhir_bulan=date('Y-m-d',strtotime($thn1.'-'.$bln1.'-'.$hariakhir1));
      
      if($periode==0)$start=date('Y-m-d',strtotime('+1 days',strtotime($akhir_bulan)));
      else $start=date('Y-m-d',strtotime($periode.'-01'));
      $thn2=date('Y',strtotime($start));
      $bln2=date('m',strtotime($start));
      $hariakhir2=cal_days_in_month($kalendar,$bln2,$thn2);
      $finish=date('Y-m-d',strtotime($thn2.'-'.$bln2.'-'.$hariakhir2));

      if($periode==0)$periode=$thn2.'-'.$bln2;

      $bulan = date('n',strtotime($start));
      $romawi = $this->getRomawi($bulan);
      $kunci='%/'.$romawi.'/'.$thn2;
      
      $qty_ksk=0;
      //$qty_ksk=DB::table('tb_ksk')->where('no_ksk','like',$kunci)->count();
 
      if($qty_ksk==0){

        $tb_status=DB::table('tb_statuses')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_statuses.id_employee')
        ->leftjoin('tb_employees as tb_employees2','tb_employees.leader_id','=','tb_employees2.id')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->where('tb_statuses.active',1)->where('tb_employees.status','1')
        ->where('tb_statuses.finish_contract','>=',$start)
        ->where('tb_statuses.finish_contract','<=',$finish)
        ->where('tb_statuses.contract_name','!=','Permanen')
        ->where('tb_statuses.contract_name','!=','PSAB')
        ->where('tb_statuses.contract_name','!=','SAB')
        ->where('tb_statuses.contract_name','!=','NASKA')
        ->where('tb_statuses.contract_name','!=','PKL')
        ->where('tb_statuses.contract_name','!=','Other')
        ->where('tb_statuses.contract_name','!=','Kabur')
        ->where('tb_statuses.contract_name','!=','End Contract')
        ->orderby('tb_employees.dept_id','asc')
        ->orderby('tb_employees.leader_id','asc')
        ->get(['tb_employees.PIN','tb_employees.id as idemployee','tb_employees.join_date as joindate','tb_employees.NIK','tb_employees.leader_id','tb_employees2.employee_name as leader_name','tb_employees.gender','tb_employees.status','tb_employees.employee_name','tb_employees.dept_id','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_name','tb_positions.position_index','tb_statuses.*']);
        //return $tb_status;
        $dept_id=0;
        $leader_id=0;
        $dept_leader=$dept_id.'#'.$leader_id;
        $i=0;
  
        $delete1=DB::table('tb_ksk_detail')->where('no_ksk','like',$kunci)->delete();
        $delete2=DB::table('tb_ksk')->where('no_ksk','like',$kunci)->delete();
  
        foreach($tb_status as $dt){
          $start_contract=date('Y-m-d',strtotime('-1 days',strtotime($dt->join_date)));
          $datetime2 = date_create($dt->finish_contract);
          $datetime3 = date_create($start_contract);
          $bulan = date_diff($datetime2, $datetime3);
          $lama_tahun=$bulan->format('%y');
          $lama_bulan=$bulan->format('%m');
          $lama_hari=round($bulan->format('%d')/30,0);
          $lama_contract=$lama_tahun*12+$lama_bulan+$lama_hari;
          //$lama_contract=$lama_tahun*12+$lama_bulan;
          //return $lama_contract;
  
          $dept_leader_baru=$dt->dept_id.'#'.$dt->leader_id;
          if($dept_leader!=$dept_leader_baru){
            $i++;
            if(strlen($i)==1)$j='00'.$i;
            elseif(strlen($i)==2)$j='0'.$i;
            else $j=$i;
  
            $no_ksk=$j.'/HRGA/'.$romawi.'/'.$thn2;

            //Get Kolom Atasan

              $tb_leader=DB::table('tb_employees')
              ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','tb_employees.leader_id')
              ->leftjoin('tb_employees as tb_employees2','tb_employees2.id','tb_employees1.leader_id')
              ->leftjoin('tb_employees as tb_employees3','tb_employees3.id','tb_employees2.leader_id')
              ->leftjoin('tb_employees as tb_employees4','tb_employees4.id','tb_employees3.leader_id')
              ->leftjoin('tb_employees as tb_employees5','tb_employees5.id','tb_employees4.leader_id')
              ->leftjoin('tb_employees as tb_employees6','tb_employees6.id','tb_employees5.leader_id')
              ->select([
                'tb_employees.id',
                'tb_employees1.id as approval1',
                'tb_employees2.id as approval2',
                'tb_employees3.id as approval3',
                'tb_employees4.id as approval4',
                'tb_employees5.id as approval5',
                'tb_employees6.id as approval6',
                'tb_employees1.position_id as position_id1',
                'tb_employees2.position_id as position_id2',
                'tb_employees3.position_id as position_id3',
                'tb_employees4.position_id as position_id4',
                'tb_employees5.position_id as position_id5',
                'tb_employees6.position_id as position_id6',
              ])
              ->where('tb_employees.id',$dt->id_employee)
              ->get();
              $pic='';
              $approval6='0';
              $approval5='0';
              $approval4='0';
              $approval3='0';
              foreach($tb_leader as $dt2){
                $tb_position=DB::table('tb_positions')->where('id',$dt2->position_id6)->get();
                foreach($tb_position as $dt3){
                  if($dt3->position_index<=7)$approval6=$dt2->approval6;
                  else $approval6='0';
                  if($dt3->position_index>=6)$pic=$dt2->approval6;
                }
                $tb_position=DB::table('tb_positions')->where('id',$dt2->position_id5)->get();
                foreach($tb_position as $dt3){
                  if($dt3->position_index<=7)$approval5=$dt2->approval5;
                  else $approval5='0';
                  if($dt3->position_index>=6)$pic=$dt2->approval5;
                }
                $tb_position=DB::table('tb_positions')->where('id',$dt2->position_id4)->get();
                foreach($tb_position as $dt3){
                  if($dt3->position_index<=10)$approval4=$dt2->approval4;
                  else $approval4='0';
                  if($dt3->position_index>=6)$pic=$dt2->approval4;
                }
                $tb_position=DB::table('tb_positions')->where('id',$dt2->position_id3)->get();
                foreach($tb_position as $dt3){
                  if($dt3->position_index<=10)$approval3=$dt2->approval3;
                  else $approval3='0';
                  if($dt3->position_index>=6)$pic=$dt2->approval3;
                }
                $tb_position=DB::table('tb_positions')->where('id',$dt2->position_id2)->get();
                foreach($tb_position as $dt3){
                  if($dt3->position_index<=10)$approval2=$dt2->approval2;
                  else $approval2='0';
                  if($dt3->position_index>=6)$pic=$dt2->approval2;
                }
                $tb_position=DB::table('tb_positions')->where('id',$dt2->position_id1)->get();
                foreach($tb_position as $dt3){
                  if($dt3->position_index<=10)$approval1=$dt2->approval1;
                  else $approval1='0';
                  if($dt3->position_index>=6)$pic=$dt2->approval1;
                }
              }
              $tb_employees=DB::table('tb_employees')
              ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
              ->where('tb_employees.id',$pic)->get(['tb_employees.*','tb_positions.position_name']);
              foreach($tb_employees as $dtemployee){
                $pic_name=$dtemployee->employee_name;
                $pic_position=$dtemployee->position_name;
              }
              
            //End Gete Kolom Atasan

            $hr_id=122;
            $dh_hr=DB::table('tb_employees')->where('position_id','17')->get();
            foreach($dh_hr as $dt_hr){$hr_id=$dt_hr->id;}  
  
            $dead_line=date('Y-m-d',strtotime('-15 days',strtotime($start)));
            $add_ksk=DB::table('tb_ksk')->insert([
              'no_ksk'=>$no_ksk,
              'periode'=>$periode,
              'dead_line'=>$dead_line,
              'dept_id'=>$dt->dept_id,
              'approval1'=>$approval1,
              'approval2'=>$approval2,
              'approval3'=>$approval3,
              'approval4'=>$approval4,
              'approval5'=>$approval5,
              'approval6'=>$approval6,
              'legalize1'=>$hr_id,
              'legalize2'=>'879',
              'legalize3'=>'1406',
              'legalize4'=>'0',
              'distribute_status'=>'0',
              'direct_id'=>$pic,
              'direct_spv'=>$pic_name,
              'direct_pos'=>$pic_position,
              'admin_id'=>$id_user,
              'admin'=>$admin,
              'created_at'=>$sekarang,
              'updated_at'=>$sekarang,
              'manager_name'=>$pic_name
           ]);
            
          }
          $dept_leader=$dept_leader_baru;
  
  
          $periode_awal=date('Y-m',strtotime($dt->start_contract));
          $periode_akhir=date('Y-m',strtotime($dt->finish_contract));
          $tb_absen=DB::table('tb_absensi_rate')->where('id_employee',$dt->idemployee)->where('periode','>=',$periode_awal)->where('periode','<=',$periode_akhir)->get();
          $sick=0;$permit=0;$alpa=0;$late=0;$minutes=0;
          foreach($tb_absen as $dt_absen){
            $sick=$sick+$dt_absen->sakit;
            $permit=$permit+$dt_absen->izin;
            $alpa=$alpa+$dt_absen->alpa;
            $late=$late+$dt_absen->terlambat;
            $minutes=$minutes+$dt_absen->terlambat_minutes;
          }
  
          $id_ksks=DB::table('tb_ksk')->where('no_ksk',$no_ksk)->get();
          foreach($id_ksks as $dt_ksk){$id_ksk=$dt_ksk->id;}
  
          $add_detail=DB::table('tb_ksk_detail')->insert([
            'id_ksk'=>$id_ksk,
            'no_ksk'=>$no_ksk,
            'id_kontrak'=>$dt->id,
            'id_employee'=>$dt->idemployee,
            'join_date'=>$dt->join_date,
            'warning_letter'=>'0',
            'sick'=>$sick,
            'permit'=>$permit,
            'alpa'=>$alpa,
            'late'=>$late,
            'minutes'=>$minutes,
            'first_contract'=>$dt->join_date,
            'start_contract'=>$dt->start_contract,
            'finish_contract'=>$dt->finish_contract,
            'months'=>$lama_contract,
            'judge'=>'',
            'created_at'=>$sekarang,
            'updated_at'=>$sekarang,
          ]);
        }
          
      }
      //return "Masuk";
      return redirect()->back();
    }
    function kskRefresh_221124_2($periode){
      $admin=Auth::user()->name;
      
      date_default_timezone_set("Asia/Jakarta");
      $kalendar=CAL_GREGORIAN;
      $sekarang=date('Y-m-d H:i:s');
      $today=date('Y-m-d');
      $thn1=date('Y',strtotime($today));
      $bln1=date('m',strtotime($today));
      $awal_bulan=date('Y-m-d',strtotime($thn1.'-'.$bln1.'-01'));
      $hariakhir1=cal_days_in_month($kalendar,$bln1,$thn1);
      $akhir_bulan=date('Y-m-d',strtotime($thn1.'-'.$bln1.'-'.$hariakhir1));
      
      if($periode==0)$start=date('Y-m-d',strtotime('+1 days',strtotime($akhir_bulan)));
      else $start=date('Y-m-d',strtotime($periode.'-01'));
      $thn2=date('Y',strtotime($start));
      $bln2=date('m',strtotime($start));
      $hariakhir2=cal_days_in_month($kalendar,$bln2,$thn2);
      $finish=date('Y-m-d',strtotime($thn2.'-'.$bln2.'-'.$hariakhir2));

      if($periode==0)$periode=$thn2.'-'.$bln2;

      $bulan = date('n',strtotime($start));
      $romawi = $this->getRomawi($bulan);
      $kunci='%'.$romawi.'/'.$thn2;
      
      $qty_ksk=0;
      //$qty_ksk=DB::table('tb_ksk')->where('no_ksk','like',$kunci)->count();
 
      if($qty_ksk==0){

        $tb_status=DB::table('tb_statuses')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_statuses.id_employee')
        ->leftjoin('tb_employees as tb_employees2','tb_employees.leader_id','=','tb_employees2.id')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->where('tb_statuses.active',1)->where('tb_employees.status','1')
        ->where('tb_statuses.finish_contract','>=',$start)
        ->where('tb_statuses.finish_contract','<=',$finish)
        ->where('tb_statuses.contract_name','!=','Permanen')
        ->where('tb_statuses.contract_name','!=','PSAB')
        ->where('tb_statuses.contract_name','!=','SAB')
        ->where('tb_statuses.contract_name','!=','NASKA')
        ->where('tb_statuses.contract_name','!=','PKL')
        ->where('tb_statuses.contract_name','!=','Other')
        ->where('tb_statuses.contract_name','!=','End Contract')
        ->orderby('tb_employees.dept_id','asc')
        ->orderby('tb_employees.leader_id','asc')
        ->get(['tb_employees.PIN','tb_employees.id as idemployee','tb_employees.join_date as joindate','tb_employees.NIK','tb_employees.leader_id','tb_employees2.employee_name as leader_name','tb_employees.gender','tb_employees.status','tb_employees.employee_name','tb_employees.dept_id','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_name','tb_positions.position_index','tb_statuses.*']);
        //return $tb_status;
        $dept_id=0;
        $leader_id=0;
        $dept_leader=$dept_id.'#'.$leader_id;
        $i=0;
  
        $delete1=DB::table('tb_ksk_detail')->where('no_ksk','like',$kunci)->delete();
        $delete2=DB::table('tb_ksk')->where('no_ksk','like',$kunci)->delete();
  
        foreach($tb_status as $dt){
          $start_contract=date('Y-m-d',strtotime('-1 days',strtotime($dt->join_date)));
          $datetime2 = date_create($dt->finish_contract);
          $datetime3 = date_create($start_contract);
          $bulan = date_diff($datetime2, $datetime3);
          $lama_tahun=$bulan->format('%y');
          $lama_bulan=$bulan->format('%m');
          $lama_contract=$lama_tahun*12+$lama_bulan;
          //return $lama_contract;
  
          $dept_leader_baru=$dt->dept_id.'#'.$dt->leader_id;
          if($dept_leader!=$dept_leader_baru){
            $i++;
            if(strlen($i)==1)$j='00'.$i;
            elseif(strlen($i)==2)$j='0'.$i;
            else $j=$i;
  
            $no_ksk=$j.'/HRGA/'.$romawi.'/'.$thn2;
            //Get Kolom Atasan
              $gm_id='0';
              $agm_id='0';
              $dh_id='0';
              $sh_id='0';
              $leader_id='0';

              $tb_leader=DB::table('tb_employees')
              ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
              ->select(['tb_employees.id','tb_employees.leader_id','tb_positions.position_index as position'])
              ->where('tb_employees.id',$dt->leader_id)->get();
              foreach($tb_leader as $dt1){
                if($dt1->position==8){
                  $gm_id=$dt1->id;
                }elseif($dt1->position==7){
                  $agm_id=$dt1->id;
                  $tb_leader2=DB::table('tb_employees')
                  ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
                  ->select(['tb_employees.id','tb_employees.leader_id','tb_positions.position_index as position'])
                  ->where('tb_employees.id',$dt1->leader_id)->get();
                  foreach($tb_leader2 as $dt2){
                    if($dt2->position==8){
                      $gm_id=$dt2->id;
                    }                        
                  }
                }elseif($dt1->position==6){
                  $dh_id=$dt1->id;
                  $tb_leader2=DB::table('tb_employees')
                  ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
                  ->select(['tb_employees.id','tb_employees.leader_id','tb_positions.position_index as position'])
                  ->where('tb_employees.id',$dt1->leader_id)->get();
                  foreach($tb_leader2 as $dt2){
                    if($dt2->position==7){
                      $agm_id=$dt2->id;
                      $tb_leader3=DB::table('tb_employees')
                      ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
                      ->select(['tb_employees.id','tb_employees.leader_id','tb_positions.position_index as position'])
                      ->where('tb_employees.id',$dt2->leader_id)->get();
                      foreach($tb_leader3 as $dt3){
                        if($dt3->position==8){
                          $gm_id=$dt3->id;
                        }                        
                      }
                    }elseif($dt2->position==8){
                      $gm_id=$dt2->id;
                    }                     
                  }
                }elseif($dt1->position==5){
                  $adh_id=$dt1->id;
                  $tb_leader2=DB::table('tb_employees')
                  ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
                  ->select(['tb_employees.id','tb_employees.leader_id','tb_positions.position_index as position'])
                  ->where('tb_employees.id',$dt1->leader_id)->get();
                  foreach($tb_leader2 as $dt2){
                    if($dt2->position==6){
                      $dh_id=$dt2->id;
                      $tb_leader3=DB::table('tb_employees')
                      ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
                      ->select(['tb_employees.id','tb_employees.leader_id','tb_positions.position_index as position'])
                      ->where('tb_employees.id',$dt2->leader_id)->get();
                      foreach($tb_leader3 as $dt3){
                        if($dt3->position==7){
                          $agm_id=$dt3->id;
                          $tb_leader4=DB::table('tb_employees')
                          ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
                          ->select(['tb_employees.id','tb_employees.leader_id','tb_positions.position_index as position'])
                          ->where('tb_employees.id',$dt3->leader_id)->get();
                          foreach($tb_leader4 as $dt4){
                            if($dt4->position==7){
                              $gm_id=$dt4->id;
                            }                        
                          }
                        }                        
                      }
                    }elseif($dt2->position==7){
                      $agm_id=$dt2->id;
                    }                     
                  }

                }elseif($dt1->position==3||$dt1->position==4){
                  $sh_id=$dt1->id;
                  $tb_leader2=DB::table('tb_employees')
                  ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
                  ->select(['tb_employees.id','tb_employees.leader_id','tb_positions.position_index as position'])
                  ->where('tb_employees.id',$dt1->leader_id)->get();
                  foreach($tb_leader2 as $dt2){
                    if($dt2->position==5){
                      $adh_id=$dt2->id;
                      $tb_leader3=DB::table('tb_employees')
                      ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
                      ->select(['tb_employees.id','tb_employees.leader_id','tb_positions.position_index as position'])
                      ->where('tb_employees.id',$dt2->leader_id)->get();
                      foreach($tb_leader3 as $dt3){
                        if($dt3->position==7){
                          $agm_id=$dt3->id;
                          $tb_leader4=DB::table('tb_employees')
                          ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
                          ->select(['tb_employees.id','tb_employees.leader_id','tb_positions.position_index as position'])
                          ->where('tb_employees.id',$dt3->leader_id)->get();
                          foreach($tb_leader4 as $dt4){
                            if($dt4->position==7){
                              $gm_id=$dt4->id;
                            }                        
                          }
                        }                        
                      }
                    }elseif($dt2->position==7){
                      $agm_id=$dt2->id;
                    }                     
                  }
                }elseif($dt1->position<3){
                  $leader_id=$dt1->id;
                }
              }

            //End Gete Kolom Atasan

            $dh_hr=DB::table('tb_employees')->where('position_id','17')->get();
            foreach($dh_hr as $dt_hr){$hr_id=$dt_hr->id;}
  
  
            $dead_line=date('Y-m-d',strtotime('-15 days',strtotime($start)));
            $add_ksk=DB::table('tb_ksk')->insert([
              'no_ksk'=>$no_ksk,
              'dead_line'=>$dead_line,
              'dept_id'=>$dt->dept_id,
              'leader_id'=>$dt->leader_id,
              'sh_id'=>$sh_id,
              'dh_id'=>$dh_id,
              'agm_id'=>$agm_id,
              'hr_id'=>$hr_id,
              'hr_agm'=>'115',
              'vice_id'=>'879',
              'presdir_id'=>'719',
              'admin'=>$admin,
              'created_at'=>$sekarang,
              'updated_at'=>$sekarang
            ]);
            
          }
          $dept_leader=$dept_leader_baru;
  
  
          $periode_awal=date('Y-m',strtotime($dt->start_contract));
          $periode_akhir=date('Y-m',strtotime($dt->finish_contract));
          $tb_absen=DB::connection('emsAbsensi')->table('tb_absensi_rate')->where('id_employee',$dt->idemployee)->where('periode','>=',$periode_awal)->where('periode','<=',$periode_akhir)->get();
          $sick=0;$permit=0;$alpa=0;$late=0;$minutes=0;
          foreach($tb_absen as $dt_absen){
            $sick=$sick+$dt_absen->sakit;
            $permit=$permit+$dt_absen->izin;
            $alpa=$alpa+$dt_absen->alpa;
            $late=$late+$dt_absen->terlambat;
            $minutes=$minutes+$dt_absen->terlambat_minutes;
          }
  
          $id_ksks=DB::table('tb_ksk')->where('no_ksk',$no_ksk)->get();
          foreach($id_ksks as $dt_ksk){$id_ksk=$dt_ksk->id;}
  
          $add_detail=DB::table('tb_ksk_detail')->insert([
            'id_ksk'=>$id_ksk,
            'no_ksk'=>$no_ksk,
            'id_kontrak'=>$dt->id,
            'id_employee'=>$dt->idemployee,
            'join_date'=>$dt->join_date,
            'warning_letter'=>'0',
            'sick'=>$sick,
            'permit'=>$permit,
            'alpa'=>$alpa,
            'late'=>$late,
            'minutes'=>$minutes,
            'first_contract'=>$dt->join_date,
            'start_contract'=>$dt->start_contract,
            'finish_contract'=>$dt->finish_contract,
            'months'=>$lama_contract,
            'judge'=>'',
            'created_at'=>$sekarang,
            'updated_at'=>$sekarang,
          ]);
        }
          
      }
      return "Masuk";
      //return redirect()->back();
    }
    function kskRefresh_221124($periode){
      $admin=Auth::user()->name;
      
      date_default_timezone_set("Asia/Jakarta");
      $kalendar=CAL_GREGORIAN;
      $sekarang=date('Y-m-d H:i:s');
      $today=date('Y-m-d');
      $thn1=date('Y',strtotime($today));
      $bln1=date('m',strtotime($today));
      $awal_bulan=date('Y-m-d',strtotime($thn1.'-'.$bln1.'-01'));
      $hariakhir1=cal_days_in_month($kalendar,$bln1,$thn1);
      $akhir_bulan=date('Y-m-d',strtotime($thn1.'-'.$bln1.'-'.$hariakhir1));
      
      if($periode==0)$start=date('Y-m-d',strtotime('+1 days',strtotime($akhir_bulan)));
      else $start=date('Y-m-d',strtotime($periode.'-01'));
      $thn2=date('Y',strtotime($start));
      $bln2=date('m',strtotime($start));
      $hariakhir2=cal_days_in_month($kalendar,$bln2,$thn2);
      $finish=date('Y-m-d',strtotime($thn2.'-'.$bln2.'-'.$hariakhir2));

      if($periode==0)$periode=$thn2.'-'.$bln2;

      $bulan = date('n',strtotime($start));
      $romawi = $this->getRomawi($bulan);
      $kunci='%'.$romawi.'/'.$thn2;
      
      $qty_ksk=0;
      //$qty_ksk=DB::table('tb_ksk')->where('no_ksk','like',$kunci)->count();
 
      if($qty_ksk==0){

        $tb_status=DB::table('tb_statuses')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_statuses.id_employee')
        ->leftjoin('tb_employees as tb_employees2','tb_employees.leader_id','=','tb_employees2.id')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->where('tb_statuses.active',1)->where('tb_employees.status','1')
        //->where('tb_statuses.finish_contract','<',$today)
        //->where('tb_statuses.finish_contract','>=',$awal_bulan)
        //->where('tb_statuses.finish_contract','<=',$akhir_bulan)
        //->where('tb_employees.id','307')
        ->where('tb_statuses.finish_contract','>=',$start)
        ->where('tb_statuses.finish_contract','<=',$finish)
        ->where('tb_statuses.contract_name','!=','Permanen')
        ->where('tb_statuses.contract_name','!=','PSAB')
        ->where('tb_statuses.contract_name','!=','SAB')
        ->where('tb_statuses.contract_name','!=','NASKA')
        ->where('tb_statuses.contract_name','!=','PKL')
        ->where('tb_statuses.contract_name','!=','Other')
        ->where('tb_statuses.contract_name','!=','End Contract')
        ->orderby('tb_employees.dept_id','asc')
        ->orderby('tb_employees.leader_id','asc')
        ->get(['tb_employees.PIN','tb_employees.id as idemployee','tb_employees.join_date as joindate','tb_employees.NIK','tb_employees.leader_id','tb_employees2.employee_name as leader_name','tb_employees.gender','tb_employees.status','tb_employees.employee_name','tb_employees.dept_id','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_name','tb_positions.position_index','tb_statuses.*']);
        //return $tb_status;
        $dept_id=0;
        $leader_id=0;
        $dept_leader=$dept_id.'#'.$leader_id;
        $i=0;
  
        $delete1=DB::table('tb_ksk_detail')->where('no_ksk','like',$kunci)->delete();
        $delete2=DB::table('tb_ksk')->where('no_ksk','like',$kunci)->delete();
  
        foreach($tb_status as $dt){
          $start_contract=date('Y-m-d',strtotime('-1 days',strtotime($dt->join_date)));
          $datetime2 = date_create($dt->finish_contract);
          $datetime3 = date_create($start_contract);
          $bulan = date_diff($datetime2, $datetime3);
          $lama_tahun=$bulan->format('%y');
          $lama_bulan=$bulan->format('%m');
          $lama_contract=$lama_tahun*12+$lama_bulan;
          //return $lama_contract;
  
          $dept_leader_baru=$dt->dept_id.'#'.$dt->leader_id;
          if($dept_leader!=$dept_leader_baru){
            $i++;
            if(strlen($i)==1)$j='00'.$i;
            elseif(strlen($i)==2)$j='0'.$i;
            else $j=$i;
  
            $no_ksk=$j.'/HRGA/'.$romawi.'/'.$thn2;
            if($dt->position_index>=5){
              $pos=1;
              $agm_id=$dt->leader_id;
              $dh_id='0';
              $sh_id='0';
              $leader_id='0';
            }else if($dt->position_index>=3){
              $pos=2;
              $tb_leader=DB::table('tb_employees')
              ->leftjoin('tb_employees as leader2','leader2.id','tb_employees.leader_id')
              ->leftjoin('tb_positions','tb_positions.id','=','leader2.position_id')
              ->select(['leader2.id as agm_id','leader2.leader_id as gm_id','tb_positions.position_index as agm_position'])->where('tb_employees.id',$dt->leader_id)->get();
              foreach($tb_leader as $dt2){
                if($dt2->agm_position==10)$agm_id=$dt2->agm_id;
                if($dt2->agm_position==9)$agm_id=$dt2->gm_id;
                $dh_id=$dt->leader_id;
                $sh_id='0';
                $leader_id='0';
              }
            }else if($dt->position_index>=2){
              $pos=3;
              $tb_leader=DB::table('tb_employees')
              ->leftjoin('tb_employees as leader2','leader2.id','tb_employees.leader_id')
              ->leftjoin('tb_employees as leader3','leader3.id','leader2.leader_id')
              ->leftjoin('tb_positions','tb_positions.id','=','leader3.position_id')
              ->select(['leader2.id as dh_id','leader3.id as agm_id','leader3.leader_id as gm_id','tb_positions.position_index as agm_position'])->where('tb_employees.id',$dt->leader_id)->get();
              foreach($tb_leader as $dt2){
                if($dt2->agm_position==10)$agm_id=$dt2->agm_id;
                if($dt2->agm_position==9)$agm_id=$dt2->gm_id;
                $dh_id=$dt2->dh_id;
                $sh_id=$dt->leader_id;
                $leader_id='0';
              }
            }else{
              $pos=4;
              $tb_leader=DB::table('tb_employees')
              ->leftjoin('tb_employees as leader2','leader2.id','tb_employees.leader_id')
              ->leftjoin('tb_employees as leader3','leader3.id','leader2.leader_id')
              ->leftjoin('tb_employees as leader4','leader4.id','leader3.leader_id')
              ->select([
                'leader2.id as sh_id','leader3.id as dh_id','leader4.id as agm_id','leader4.leader_id as gm_id',
                'tb_employees.position_id as leader_position','leader2.position_id as sh_position','leader3.position_id as dh_position','leader4.position_id as agm_position'
              ])
              ->where('tb_employees.id',$dt->leader_id)->get();
              foreach($tb_leader as $dt2){
                if($dt2->agm_position==15)$agm_id=$dt2->agm_id;
                else $agm_id='0';
                if($dt2->dh_position>=12&&$dt2->dh_position<=14)$dh_id=$dt2->dh_id;
                else $dh_id='0';
                if($dt2->sh_position==10)$sh_id=$dt2->sh_id;
                else $sh_id='0';
                if($dt2->leader_position==8)$leader_id=$dt->leader_id;
                else $leader_id='0';
              }
            }
            //return $tb_leader;
            $dh_hr=DB::table('tb_employees')->where('position_id','17')->get();
            foreach($dh_hr as $dt_hr){$hr_id=$dt_hr->id;}
  
  
            //$delete=DB::table('tb_ksk')->where('no_ksk',$no_ksk)->delete();
            //$cek_ksk=DB::table('tb_ksk')->where('no_ksk',$no_ksk)->count();
            $dead_line=date('Y-m-d',strtotime('-15 days',strtotime($start)));
            $add_ksk=DB::table('tb_ksk')->insert([
              'no_ksk'=>$no_ksk,
              'dead_line'=>$dead_line,
              'dept_id'=>$dt->dept_id,
              'leader_id'=>$dt->leader_id,
              'sh_id'=>$sh_id,
              'dh_id'=>$dh_id,
              'agm_id'=>$agm_id,
              'hr_id'=>$hr_id,
              'hr_agm'=>'115',
              'vice_id'=>'879',
              'presdir_id'=>'719',
              'admin'=>$admin,
              'created_at'=>$sekarang,
              'updated_at'=>$sekarang
            ]);
            
          }
          $dept_leader=$dept_leader_baru;
  
  
          $periode_awal=date('Y-m',strtotime($dt->start_contract));
          $periode_akhir=date('Y-m',strtotime($dt->finish_contract));
          $tb_absen=DB::connection('emsAbsensi')->table('tb_absensi_rate')->where('id_employee',$dt->idemployee)->where('periode','>=',$periode_awal)->where('periode','<=',$periode_akhir)->get();
          $sick=0;$permit=0;$alpa=0;$late=0;$minutes=0;
          foreach($tb_absen as $dt_absen){
            $sick=$sick+$dt_absen->sakit;
            $permit=$permit+$dt_absen->izin;
            $alpa=$alpa+$dt_absen->alpa;
            $late=$late+$dt_absen->terlambat;
            $minutes=$minutes+$dt_absen->terlambat_minutes;
          }
  
          $id_ksks=DB::table('tb_ksk')->where('no_ksk',$no_ksk)->get();
          foreach($id_ksks as $dt_ksk){$id_ksk=$dt_ksk->id;}
  
          $add_detail=DB::table('tb_ksk_detail')->insert([
            'id_ksk'=>$id_ksk,
            'no_ksk'=>$no_ksk,
            'id_kontrak'=>$dt->id,
            'id_employee'=>$dt->idemployee,
            'join_date'=>$dt->join_date,
            'warning_letter'=>'0',
            'sick'=>$sick,
            'permit'=>$permit,
            'alpa'=>$alpa,
            'late'=>$late,
            'minutes'=>$minutes,
            'first_contract'=>$dt->join_date,
            'start_contract'=>$dt->start_contract,
            'finish_contract'=>$dt->finish_contract,
            'months'=>$lama_contract,
            'judge'=>'',
            'created_at'=>$sekarang,
            'updated_at'=>$sekarang,
          ]);
        }
          
      }
      return "Masuk";
      //return redirect()->back();
    }

    function kskDetail($id_ksk,$periode){
      $email=Auth::user()->email;
      $cek1=DB::table('tb_emails')->where('email_address',$email)->get();
      foreach($cek1 as $dt){$id_user=$dt->id_employee;}

      $tb_spv=DB::table('tb_employees')
      ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
      ->where('tb_employees.status','1')
      ->where('position_index','>=','3')
      ->orderby('employee_name','asc')->get('tb_employees.*');
  
        $tb_ksk=DB::table('tb_ksk_detail')
        ->leftjoin('tb_ksk','tb_ksk.no_ksk','=','tb_ksk_detail.no_ksk')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_ksk.dept_id')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_ksk_detail.id_employee')
        ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','tb_ksk.approval1')
        ->leftjoin('tb_employees as tb_employees2','tb_employees2.id','tb_ksk.approval2')
        ->leftjoin('tb_employees as tb_employees3','tb_employees3.id','tb_ksk.approval3')
        ->leftjoin('tb_employees as tb_employees4','tb_employees4.id','tb_ksk.approval4')
        ->leftjoin('tb_employees as tb_employees5','tb_employees5.id','tb_ksk.approval5')
        ->leftjoin('tb_employees as tb_employees6','tb_employees6.id','tb_ksk.approval6')
        ->leftjoin('tb_employees as tb_employees7','tb_employees7.id','tb_ksk.legalize1')
        ->leftjoin('tb_employees as tb_employees8','tb_employees8.id','tb_ksk.legalize2')
        ->leftjoin('tb_employees as tb_employees9','tb_employees9.id','tb_ksk.legalize3')
        ->leftjoin('tb_employees as tb_employees10','tb_employees10.id','tb_ksk.legalize4')
        ->where('periode',$periode);
        if($id_ksk>0)$tb_ksk=$tb_ksk->where('tb_ksk_detail.id_ksk',$id_ksk);
        $tb_ksk=$tb_ksk->select([
          'tb_ksk_detail.*',
          'tb_employees.NIK',
          'tb_employees.employee_name',
          'tb_departments.dept_code',
          'tb_employees1.id as approval1',
          'tb_employees2.id as approval2',
          'tb_employees3.id as approval3',
          'tb_employees4.id as approval4',
          'tb_employees5.id as approval5',
            'tb_employees6.id as approval6',
            'tb_employees7.id as legalize1',
            'tb_employees8.id as legalize2',
            'tb_employees9.id as legalize3',
            'tb_employees10.id as legalize4',
            'tb_employees1.employee_name as approvalname1',
            'tb_employees2.employee_name as approvalname2',
            'tb_employees3.employee_name as approvalname3',
            'tb_employees4.employee_name as approvalname4',
            'tb_employees5.employee_name as approvalname5',
            'tb_employees6.employee_name as approvalname6',
            'tb_ksk.approval_status',
            'tb_employees7.employee_name as legalizename1',
            'tb_employees8.employee_name as legalizename2',
            'tb_employees9.employee_name as legalizename3',
            'tb_employees10.employee_name as legalizename4',
        ])
        ->orderby('no_ksk','asc')->get();
      //return $tb_ksk;
      $judul="KSK ID ".$id_ksk." (".$periode.")";
      return view('page/admin/m_employee/ksk_detail',['tb_ksk'=>$tb_ksk,'id_ksk'=>$id_ksk,'tb_spv'=>$tb_spv,'periode'=>$periode,'id_employee'=>$id_user,'menu'=>'employee','submenu'=>'contract','submenu'=>'contract','Judul'=>$judul]);
    
    }
    function kskPrint_221206($id_ksk){
  
      $tb_ksk=DB::table('tb_ksk_detail')
      ->leftjoin('tb_ksk','tb_ksk.no_ksk','=','tb_ksk_detail.no_ksk')
      ->leftjoin('tb_departments','tb_departments.id','=','tb_ksk.dept_id')
      ->leftjoin('tb_employees','tb_employees.id','=','tb_ksk_detail.id_employee')
      ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
      ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','tb_ksk.approval1')
      ->leftjoin('tb_employees as tb_employees2','tb_employees2.id','tb_ksk.approval2')
      ->leftjoin('tb_employees as tb_employees3','tb_employees3.id','tb_ksk.approval3')
      ->leftjoin('tb_employees as tb_employees4','tb_employees4.id','tb_ksk.approval4')
      ->leftjoin('tb_employees as tb_employees5','tb_employees5.id','tb_ksk.approval5')
      ->leftjoin('tb_employees as tb_employees6','tb_employees6.id','tb_ksk.approval6')
      ->leftjoin('tb_employees as tb_employees7','tb_employees7.id','tb_ksk.legalize1')
      ->leftjoin('tb_employees as tb_employees8','tb_employees8.id','tb_ksk.legalize2')
      ->leftjoin('tb_employees as tb_employees9','tb_employees9.id','tb_ksk.legalize3')
      ->leftjoin('tb_employees as tb_employees10','tb_employees10.id','tb_ksk.legalize4')
      ->leftjoin('tb_ksk_target',function($join){
        $join->on('tb_ksk_target.dept_id','=','tb_ksk.dept_id')->on('tb_ksk_target.periode','=','tb_ksk.periode');
      })
      ->where('tb_ksk_detail.id_ksk',$id_ksk)
      ->select([
        'tb_ksk_detail.*',
        'tb_employees.NIK',
        'tb_employees.employee_name',
        'tb_positions.position_name',
        'tb_departments.dept_name',
        'tb_departments.dept_code',
        'tb_employees1.id as approval1',
        'tb_employees2.id as approval2',
        'tb_employees3.id as approval3',
        'tb_employees4.id as approval4',
        'tb_employees5.id as approval5',
        'tb_employees5.leader_id as approval6',
        'tb_employees7.id as legalize1',
        'tb_employees8.id as legalize2',
        'tb_employees9.id as legalize3',
        'tb_employees10.id as legalize4',
        'tb_employees1.employee_name as approvalname1',
        'tb_employees2.employee_name as approvalname2',
        'tb_employees3.employee_name as approvalname3',
        'tb_employees4.employee_name as approvalname4',
        'tb_employees5.employee_name as approvalname5',
        'tb_employees6.employee_name as approvalname6',
        'tb_employees7.employee_name as legalizename1',
        'tb_employees8.employee_name as legalizename2',
        'tb_employees9.employee_name as legalizename3',
        'tb_employees10.employee_name as legalizename4',
        'tb_ksk_target.permanent_target',
        'tb_ksk_target.contract_target',
        'tb_ksk_target.magang_target',
        'tb_ksk_target.permanent_actual',
        'tb_ksk_target.contract_actual',
        'tb_ksk_target.magang_actual',
        'tb_ksk_target.permanent_remain',
        'tb_ksk_target.contract_remain',
        'tb_ksk_target.magang_remain'
      ])
      ->orderby('tb_employees.NIK','asc')->get();

      foreach($tb_ksk as $dt){
        if($dt->approvalname6!='')$jml_approval=6;
        elseif($dt->approvalname5!='')$jml_approval=5;
        elseif($dt->approvalname4!='')$jml_approval=4;
        elseif($dt->approvalname3!='')$jml_approval=3;
        elseif($dt->approvalname2!='')$jml_approval=2;
        elseif($dt->approvalname1!='')$jml_approval=1;
      }
      //return $tb_ksk;
      $FileName='KSK #'.$id_ksk.'.PDF';
      $pdf = PDF::loadview('page/admin/m_employee/ksk_preview',['tb_ksk'=>$tb_ksk,'jml_approval'=>$jml_approval])->setPaper('a4','landscape');
      return $pdf->stream($FileName);
    
    }
    function kskUpdate(Request $data){
      date_default_timezone_set("Asia/Jakarta");
      $sekarang=date('Y-m-d H:i:s');
      $update=DB::table('tb_ksk_detail')->where('id',$data->idksk)->update([
        'warning_letter'=>$data->warning_letter,
        'sick'=>$data->sick,
        'permit'=>$data->permit,
        'alpa'=>$data->alpa,
        'late'=>$data->late,
        'minutes'=>$data->minutes,
        'performance'=>$data->performance,
        'updated_at'=>$sekarang
      ]);
      if($update)return redirect()->back()->with(['success'=>'Updated']);
      else return $data->judge;
      
    }
    function kskTarget(Request $data){
      $admin=Auth::user()->name;
      date_default_timezone_set("Asia/Jakarta");
      $sekarang=date('Y-m-d H:i:s');
      $permanent_remain=$data->permanent_plan-$data->permanent_actual;
      $contract_remain=$data->contract_plan-$data->contract_actual;
      $magang_remain=$data->magang_plan-$data->magang_actual;
      $cek=DB::table('tb_ksk_target')->where('dept_id',$data->deptid)->where('periode',$data->periode_target)->count();
      if($cek==0){
        $insert=DB::table('tb_ksk_target')->insert([
          'dept_id'=>$data->deptid,
          'periode'=>$data->periode_target,
          'permanent_target'=>$data->permanent_plan,
          'contract_target'=>$data->contract_plan,
          'magang_target'=>$data->magang_plan,
          'permanent_actual'=>$data->permanent_actual,
          'contract_actual'=>$data->contract_actual,
          'magang_actual'=>$data->magang_actual,
          'permanent_remain'=>$permanent_remain,
          'contract_remain'=>$contract_remain,
          'magang_remain'=>$magang_remain,
          'admin'=>$admin,
          'updated_at'=>$sekarang
        ]);
        if($insert)return redirect()->back()->with(['success'=>'Success Entry']);
      }else{
        $update=DB::table('tb_ksk_target')->where('dept_id',$data->deptid)->where('periode',$data->periode_target)->update([
          'permanent_target'=>$data->permanent_plan,
          'contract_target'=>$data->contract_plan,
          'magang_target'=>$data->magang_plan,
          'permanent_actual'=>$data->permanent_actual,
          'contract_actual'=>$data->contract_actual,
          'magang_actual'=>$data->magang_actual,
          'permanent_remain'=>$permanent_remain,
          'contract_remain'=>$contract_remain,
          'magang_remain'=>$magang_remain,
          'admin'=>$admin,
          'updated_at'=>$sekarang
        ]);
        if($update)return redirect()->back()->with(['success'=>'Success Updated']);

      }
      return redirect()->back();
      
    }
    function kskDistribute($periode){
      $email=Auth::user()->email;
      $update=DB::connection('mysql')->table('tb_ksk')->where('periode',$periode)->update(['distribute_status'=>'1']);
      if($update){
        $tb_ksk=DB::connection('mysql')->table('tb_ksk')
        ->select(['tb_ksk.direct_id'])
        ->groupby('tb_ksk.direct_id')
        ->where('periode',$periode)
        ->get();

        foreach($tb_ksk as $dt){
          $tb_ksk_detail=DB::table('tb_ksk_detail')
          ->leftjoin('tb_ksk','tb_ksk.no_ksk','=','tb_ksk_detail.no_ksk')
          ->leftjoin('tb_departments','tb_departments.id','=','tb_ksk.dept_id')
          ->leftjoin('tb_employees','tb_employees.id','=','tb_ksk_detail.id_employee')
          ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','tb_ksk.approval1')
          ->leftjoin('tb_employees as tb_employees2','tb_employees2.id','tb_ksk.approval2')
          ->leftjoin('tb_employees as tb_employees3','tb_employees3.id','tb_ksk.approval3')
          ->leftjoin('tb_employees as tb_employees4','tb_employees4.id','tb_ksk.approval4')
          ->leftjoin('tb_employees as tb_employees5','tb_employees5.id','tb_ksk.approval5')
          ->leftjoin('tb_employees as tb_employees6','tb_employees6.id','tb_ksk.approval6')
          ->leftjoin('tb_employees as tb_employees7','tb_employees7.id','tb_ksk.legalize1')
          ->leftjoin('tb_employees as tb_employees8','tb_employees8.id','tb_ksk.legalize2')
          ->leftjoin('tb_employees as tb_employees9','tb_employees9.id','tb_ksk.legalize3')
          ->leftjoin('tb_employees as tb_employees10','tb_employees10.id','tb_ksk.legalize4')
          ->where('tb_ksk.direct_id',$dt->direct_id)
          ->where('tb_ksk.periode',$periode)
          ->select([
            'tb_ksk_detail.*',
            'tb_employees.NIK',
            'tb_employees.employee_name',
            'tb_departments.dept_code',
            'tb_employees1.id as approval1',
            'tb_employees2.id as approval2',
            'tb_employees3.id as approval3',
            'tb_employees4.id as approval4',
            'tb_employees5.id as approval5',
            'tb_employees6.id as approval6',
            'tb_employees7.id as legalize1',
            'tb_employees8.id as legalize2',
            'tb_employees9.id as legalize3',
            'tb_employees10.id as legalize4',
            'tb_employees1.employee_name as approvalname1',
            'tb_employees2.employee_name as approvalname2',
            'tb_employees3.employee_name as approvalname3',
            'tb_employees4.employee_name as approvalname4',
            'tb_employees5.employee_name as approvalname5',
            'tb_employees6.employee_name as approvalname6',
            'tb_ksk.approval_status',
            'tb_employees7.employee_name as legalizename1',
            'tb_employees8.employee_name as legalizename2',
            'tb_employees9.employee_name as legalizename3',
            'tb_employees10.employee_name as legalizename4',
          ])
          ->orderby('no_ksk','asc')->get();

          $judul="KSK ".$periode;
          $tb_email=DB::connection('mysql')->table('tb_emails')
          ->leftjoin('tb_employees','tb_employees.id','=','tb_emails.id_employee')
          ->where('id_employee',$dt->direct_id)->get(['tb_employees.*','tb_emails.email_address']);
          foreach($tb_email as $dt2){
            $tujuan=$dt2->email_address;
            //$tujuan=$email;
            $nama=$dt2->employee_name;
            $kirim=Mail::to($tujuan)
            ->cc($email)
            ->bcc('cahyudin@summitadyawinsa.co.id')
            ->queue(new ksk_distribute($tb_ksk_detail,$periode,$judul,$nama));
            Log::info('Send Notification KSK to '.$tujuan);      
          }
        }
        $tb1=DB::connection('mysql')->table('tb_ksk')->where('periode',$periode)->get();
        foreach($tb1 as $dt){
          $this->notificationKSK($dt->id);
        }
        return redirect()->back()->with(['success'=>'Sucess Distribute KSK']);
      }
      return redirect()->back()->with(['success'=>'Already Distribute KSK']);
    }
    function leaderUpdate(Request $data){
      date_default_timezone_set("Asia/Jakarta");
      $sekarang=date('Y-m-d H:i:s');
      $simpan=tb_employee::where('id',$data->id_employee)->update([
          'leader_id'=>$data->id_leader,
          'updated_at'=>$sekarang,
      ]);
      if($simpan){
          $teks=$data->employee_name.' berhasil diupdate';
          return redirect()->back()->with(['success' => $teks]);
      }
      else    
      return redirect()->back()->with(['success' => 'Gagal']);;
    }
    function getRomawi($bln){
      switch ($bln){
          case 1: 
              return "I";
              break;
          case 2:
              return "II";
              break;
          case 3:
              return "III";
              break;
          case 4:
              return "IV";
              break;
          case 5:
              return "V";
              break;
          case 6:
              return "VI";
              break;
          case 7:
              return "VII";
              break;
          case 8:
              return "VIII";
              break;
          case 9:
              return "IX";
              break;
          case 10:
              return "X";
              break;
          case 11:
              return "XI";
              break;
          case 12:
              return "XII";
              break;
      }
    }    
    function psab(){
      $tb_employee=DB::table('tb_employees')
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
      ->where(function ($query) {
            $query->where('tb_statuses.contract_name','PSAB');
        })
      ->orderby('tb_employees.employee_name','asc')
      ->orderby('active','desc')
      ->get(['tb_employees.PIN','tb_employees.id as idemployee','tb_employees.join_date as joindate','tb_employees.NIK','tb_employees.employee_name','tb_employees.gender','tb_employees.status','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_name','tb_statuses.*']);

      return view('page/admin/m_employee/contract',['tb_employee'=>$tb_employee,'menu'=>'employee','submenu'=>'contract','Judul'=>'Employee (PSAB)']);
    }
    function sab(){
      $tb_employee=DB::table('tb_employees')
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
      ->where(function ($query) {
            $query->where('tb_statuses.contract_name','SAB');
        })
      ->orderby('tb_employees.employee_name','asc')
      ->orderby('active','desc')
      ->get(['tb_employees.PIN','tb_employees.id as idemployee','tb_employees.join_date as joindate','tb_employees.NIK','tb_employees.employee_name','tb_employees.gender','tb_employees.status','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_name','tb_statuses.*']);

      return view('page/admin/m_employee/contract',['tb_employee'=>$tb_employee,'menu'=>'employee','submenu'=>'contract','Judul'=>'Employee (SAB)']);
    }
    function naska(){
      $tb_employee=DB::table('tb_employees')
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
      ->where(function ($query) {
            $query->where('tb_statuses.contract_name','NASKA');
        })
      ->orderby('tb_employees.employee_name','asc')
      ->orderby('active','desc')
      ->get(['tb_employees.PIN','tb_employees.id as idemployee','tb_employees.join_date as joindate','tb_employees.NIK','tb_employees.employee_name','tb_employees.gender','tb_employees.status','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_name','tb_statuses.*']);

      return view('page/admin/m_employee/contract',['tb_employee'=>$tb_employee,'menu'=>'employee','submenu'=>'contract','Judul'=>'Employee (NASKA)']);
    }
    function pkl(){
      $tb_employee=DB::table('tb_employees')
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
      ->where(function ($query) {
            $query->where('tb_statuses.contract_name','PKL');
        })
      ->orderby('tb_employees.employee_name','asc')
      ->orderby('active','desc')
      ->get(['tb_employees.PIN','tb_employees.id as idemployee','tb_employees.join_date as joindate','tb_employees.NIK','tb_employees.employee_name','tb_employees.gender','tb_employees.status','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_name','tb_statuses.*']);

      return view('page/admin/m_employee/contract',['tb_employee'=>$tb_employee,'menu'=>'employee','submenu'=>'contract','Judul'=>'Employee (PKL)']);
    }
    function arsif($periode){
      if($periode!=0){
        $kalendar=CAL_GREGORIAN;
        $hari_awal=$periode.'-01';
        $thn=date('Y',strtotime($periode.'-01'));
        $bln=date('m',strtotime($periode.'-01'));
        $hariakhir=cal_days_in_month($kalendar,$bln,$thn);
        $hari_akhir=$periode.'-'.$hariakhir;
      }

      $tb_employee=DB::table('tb_employees')
      ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
      ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
      ->leftjoin('tb_statuses', function ($join) {
          $join->on('tb_statuses.id_employee', '=', 'tb_employees.id')
               ->where('tb_statuses.active', '1');
      })
      ->where('delete','1')
      ->where('tb_employees.status','0');
      if($periode!=0){
        $tb_employee=$tb_employee->where('start_contract','>=',$hari_awal)->where('start_contract','<=',$hari_akhir);
      }
      $tb_employee=$tb_employee->orderby('employee_name','asc')
      ->get(['tb_employees.PIN','tb_employees.id as idemployee','tb_employees.join_date as joindate','tb_employees.NIK','tb_employees.employee_name','tb_employees.gender','tb_employees.status','tb_departments.dept_code','tb_departments.dept_name','tb_positions.position_name','tb_statuses.*']);

      return view('page/admin/m_employee/contract_arsif',['tb_employee'=>$tb_employee,'periode'=>$periode,'menu'=>'employee','submenu'=>'contract','Judul'=>'Employee (Arsif)']);
    }
    function reactive($id_employee){
      $reactive=DB::table('tb_employees')->where('id',$id_employee)->update(['status'=>'1']);
      if($reactive)return redirect()->back()->with(['success','Activated Success']);
    }
    function deactive($id_employee){
      $reactive=DB::table('tb_employees')->where('id',$id_employee)->update(['status'=>'0']);
      if($reactive)return redirect()->back()->with(['success','Deactivated Success']);
    }
    function delete($id_employee){
      $reactive=DB::table('tb_employees')->where('id',$id_employee)->update(['delete'=>'1']);
      if($reactive)return redirect()->back()->with(['success','Success Save to Archive']);
    }
    function kskPerformance($id_ksk,$periode){
      $tb_spv=DB::table('tb_employees')
      ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
      ->where('tb_employees.status','1')
      ->where('position_index','>=','3')
      ->orderby('employee_name','asc')->get('tb_employees.*');
  
      $tb_ksk=DB::table('tb_ksk_detail')
      ->leftjoin('tb_ksk','tb_ksk.no_ksk','=','tb_ksk_detail.no_ksk')
      ->leftjoin('tb_departments','tb_departments.id','=','tb_ksk.dept_id')
      ->leftjoin('tb_employees','tb_employees.id','=','tb_ksk_detail.id_employee')
      ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','tb_ksk.approval1')
      ->leftjoin('tb_employees as tb_employees2','tb_employees2.id','tb_ksk.approval2')
      ->leftjoin('tb_employees as tb_employees3','tb_employees3.id','tb_ksk.approval3')
      ->leftjoin('tb_employees as tb_employees4','tb_employees4.id','tb_ksk.approval4')
      ->leftjoin('tb_employees as tb_employees5','tb_employees5.id','tb_ksk.approval5')
      ->leftjoin('tb_employees as tb_employees6','tb_employees6.id','tb_ksk.approval6')
      ->where('periode',$periode);
      if($id_ksk>0)$tb_ksk=$tb_ksk->where('tb_ksk_detail.id_ksk',$id_ksk);
      $tb_ksk=$tb_ksk->select([
        'tb_ksk_detail.*',
        'tb_employees.NIK',
        'tb_employees.employee_name',
        'tb_departments.dept_code',
        'tb_employees1.id as approval1',
        'tb_employees2.id as approval2',
        'tb_employees3.id as approval3',
        'tb_employees4.id as approval4',
        'tb_employees5.id as approval5',
        'tb_employees5.leader_id as approval6',
        'tb_employees1.employee_name as approvalname1',
        'tb_employees2.employee_name as approvalname2',
        'tb_employees3.employee_name as approvalname3',
        'tb_employees4.employee_name as approvalname4',
        'tb_employees5.employee_name as approvalname5',
        'tb_employees6.employee_name as approvalname6'
      ])
      ->orderby('no_ksk','asc')->get();
      $judul="KSK ID ".$id_ksk." (".$periode.")";
      return view('page/admin/m_employee/ksk_detail_performance',['tb_ksk'=>$tb_ksk,'id_ksk'=>$id_ksk,'tb_spv'=>$tb_spv,'periode'=>$periode,'menu'=>'employee','submenu'=>'contract','submenu'=>'contract','Judul'=>$judul]);
    
    }
    function kskTemplate($periode){
      $reset=DB::connection('mysql')->table('tb_ksk_detail_performance')->where('periode',$periode)->delete();
      $tb_ksk=DB::table('tb_ksk_detail')
      ->leftjoin('tb_ksk','tb_ksk.no_ksk','=','tb_ksk_detail.no_ksk')
      ->leftjoin('tb_departments','tb_departments.id','=','tb_ksk.dept_id')
      ->leftjoin('tb_employees','tb_employees.id','=','tb_ksk_detail.id_employee')
      ->where('periode',$periode)
      ->select([
        'tb_ksk_detail.*',
        'tb_employees.NIK',
        'tb_employees.employee_name',
        'tb_departments.dept_code',
      ])
      ->orderby('no_ksk','asc')->get();
      $judul="KSK PERIODE ".$periode;
      return view('page/admin/m_employee/ksk_detail_template',['tb_ksk'=>$tb_ksk,'periode'=>$periode]);
    
    }
    function ImportKSK(Request $request){
      //return "Masuk";
      $this->validate($request, [
        'file' => 'required|mimes:csv,xls,xlsx'
      ]);
      $file = $request->file('file');
      $nama_file = rand().$file->getClientOriginalName();
      $file->move('laravel/public/excel',$nama_file);
      $import=Excel::import(new KSKImport, public_path('/excel/'.$nama_file));
      if($import){
        $this->updatePerformance($request->periode);
        return redirect()->back()->with(['info'=>'Import Berhasil']);
      }
    }
    function updatePerformance($periode){
      date_default_timezone_set("Asia/Jakarta");
      $sekarang=date('Y-m-d H:i:s');

      $tb_ksk_detail_performance=DB::connection('mysql')->table('tb_ksk_detail_performance')->where('periode',$periode)->get();
      
      foreach($tb_ksk_detail_performance as $dt){
        $update=DB::connection('mysql')->table('tb_ksk_detail')->where('id',$dt->id_ksk_detail)->update([
          'warning_letter'=>'0',
          'sick'=>$dt->sick,
          'permit'=>$dt->permit,
          'alpa'=>$dt->alpa,
          'late'=>$dt->late,
          'performance'=>$dt->performance,
          'minutes'=>$dt->minutes,
          'updated_at'=>$sekarang,
        ]);
      }
      //return $update;
      return redirect()->back();
    }
    function updateLock($periode,$status){
      if($status==0)$next=1;else $next=0;
      if (request()->user()->hasRole('root')||request()->user()->hasRole('hr_access')){
        $check=DB::connection('mysql')->table('tb_ksk_lock')->where('periode',$periode)->count();
        if($check==0){
          $add=DB::connection('mysql')->table('tb_ksk_lock')->insert([
            'periode'=>$periode
          ]);
        }
        $update=DB::connection('mysql')->table('tb_ksk_lock')->where('periode',$periode)->update(['is_lock'=>$next]);
        return redirect()->back();
      }
    }
    function kskDetailRefresh(Request $data){
      $id_ksk_detail=$data->id;
      $tb_ksk_detail=DB::table('tb_ksk_detail')->where('id',$id_ksk_detail)->get();
      foreach($tb_ksk_detail as $dt){
        $tb_employees=DB::table('tb_employees')->where('id',$dt->id_employee)->get();
        foreach ($tb_employees as $dt1) {
          $join_date_ems=$dt1->join_date;
        }

        $update_tb_statuses=DB::table('tb_statuses')->where('id',$dt->id_kontrak)->update([
          'join_date'=>$join_date_ems
        ]);

        $tb_statuses=DB::table('tb_statuses')->where('id',$dt->id_kontrak)->get();
        foreach($tb_statuses as $dt2){
          $start_contract=date('Y-m-d',strtotime('-1 days',strtotime($dt2->join_date)));
          $datetime2 = date_create($dt2->finish_contract);
          $datetime3 = date_create($start_contract);
          $bulan = date_diff($datetime2, $datetime3);
          $lama_tahun=$bulan->format('%y');
          $lama_bulan=$bulan->format('%m');
          $lama_hari=round($bulan->format('%d')/30,0);
          $lama_contract=$lama_tahun*12+$lama_bulan+$lama_hari;
        }


        $update=DB::table('tb_ksk_detail')->where('id',$id_ksk_detail)->update([
          'join_date'=>$join_date_ems,
          'first_contract'=>$join_date_ems,
          'months'=>$lama_contract
        ]);
      }
      if($update){
        return 'Sukses';
      }else{
        return "Gagal";
      }
    }

    function letter($periode){
      if($periode==0)$periode=date('Y-m');
      $email=Auth::user()->email;
      $cek1=DB::table('tb_emails')->where('email_address',$email)->get();
      foreach($cek1 as $dt){$id_user=$dt->id_employee;}

      $tb_spv=DB::table('tb_employees')
      ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
      ->where('tb_employees.status','1')
      ->where('position_index','>=','3')
      ->orderby('employee_name','asc')->get('tb_employees.*');
  
        $tb_ksk=DB::table('tb_ksk_detail')
        ->leftjoin('tb_ksk','tb_ksk.no_ksk','=','tb_ksk_detail.no_ksk')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_ksk.dept_id')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_ksk_detail.id_employee')
        ->leftjoin('tb_employees as tb_employees1','tb_employees1.id','tb_ksk.approval1')
        ->leftjoin('tb_employees as tb_employees2','tb_employees2.id','tb_ksk.approval2')
        ->leftjoin('tb_employees as tb_employees3','tb_employees3.id','tb_ksk.approval3')
        ->leftjoin('tb_employees as tb_employees4','tb_employees4.id','tb_ksk.approval4')
        ->leftjoin('tb_employees as tb_employees5','tb_employees5.id','tb_ksk.approval5')
        ->leftjoin('tb_employees as tb_employees6','tb_employees6.id','tb_ksk.approval6')
        ->leftjoin('tb_employees as tb_employees7','tb_employees7.id','tb_ksk.legalize1')
        ->leftjoin('tb_employees as tb_employees8','tb_employees8.id','tb_ksk.legalize2')
        ->leftjoin('tb_employees as tb_employees9','tb_employees9.id','tb_ksk.legalize3')
        ->leftjoin('tb_employees as tb_employees10','tb_employees10.id','tb_ksk.legalize4')
        ->leftjoin('tb_statuses','tb_statuses.id','=','tb_ksk_detail.id_kontrak')
        ->leftjoin('tb_contract','tb_contract.id','=','tb_statuses.contract_ref')
        ->where('periode',$periode);
        $tb_ksk=$tb_ksk->select([
          'tb_ksk_detail.*',
          'tb_employees.NIK',
          'tb_employees.employee_name',
          'tb_departments.dept_code',
          'tb_employees1.id as approval1',
          'tb_employees2.id as approval2',
          'tb_employees3.id as approval3',
          'tb_employees4.id as approval4',
          'tb_employees5.id as approval5',
            'tb_employees6.id as approval6',
            'tb_employees7.id as legalize1',
            'tb_employees8.id as legalize2',
            'tb_employees9.id as legalize3',
            'tb_employees10.id as legalize4',
            'tb_employees1.employee_name as approvalname1',
            'tb_employees2.employee_name as approvalname2',
            'tb_employees3.employee_name as approvalname3',
            'tb_employees4.employee_name as approvalname4',
            'tb_employees5.employee_name as approvalname5',
            'tb_employees6.employee_name as approvalname6',
            'tb_ksk.approval_status',
            'tb_employees7.employee_name as legalizename1',
            'tb_employees8.employee_name as legalizename2',
            'tb_employees9.employee_name as legalizename3',
            'tb_employees10.employee_name as legalizename4',
            'tb_statuses.contract_ref',
            'nomor_perjanjian',
        ])
        ->orderby('no_ksk','asc')->get();
      //return $periode;
      $judul="KSK LETTER PERIODE ".$periode;
      return view('page/admin/m_employee/ksk_letter',['tb_ksk'=>$tb_ksk,'id_ksk'=>'','tb_spv'=>$tb_spv,'periode'=>$periode,'id_employee'=>$id_user,'menu'=>'employee','submenu'=>'contract','submenu'=>'contract','Judul'=>$judul]);
    }
    function SendLetter(Request $data){
      $admin=Auth::user()->email;
      $tgl=date('Y-m-d');
      $periode=date('Y-m');
      $check_sequence=DB::table('tb_letters')->where('periode',$periode)->max('sequence_periode');
      $next_sequence=$check_sequence+1;
      $tahun=date('Y');
      $bulan=date('m');
      if($bulan==1)$romawi='I';
      if($bulan==2)$romawi='II';
      if($bulan==3)$romawi='III';
      if($bulan==4)$romawi='IV';
      if($bulan==5)$romawi='V';
      if($bulan==6)$romawi='VI';
      if($bulan==7)$romawi='VII';
      if($bulan==8)$romawi='VIII';
      if($bulan==9)$romawi='IX';
      if($bulan==10)$romawi='X';
      if($bulan==11)$romawi='XI';
      if($bulan==12)$romawi='XII';
      $nomor_surat=$next_sequence.'/SAI-HRGA/'.$romawi.'/'.$tahun;

      $tb_ksk_detail=DB::table('tb_ksk_detail')->where('tb_ksk_detail.id',$data->idkskdetail)
      ->leftjoin('tb_employees','tb_employees.id','=','tb_ksk_detail.id_employee')
      ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
      ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
      ->leftjoin('tb_statuses','tb_statuses.id','=','tb_ksk_detail.id_kontrak')
      ->get();
      foreach($tb_ksk_detail as $dt){
        $employee_name=$dt->employee_name;
        $bagian=$dt->dept_name;
        $jabatan=$dt->position_name;
        $nomor_perjanjian=$dt->contract_ref;
        $tgl_mulai=$dt->start_contract;
        $tgl_akhir=$dt->finish_contract;
        $judge=$dt->judge;
      }
      $tgl_undangan=date('Y-m-d',strtotime('-21 Days',strtotime($tgl_akhir)));
      $check_tb_letters=DB::table('tb_letters')->where('id_ksk_detail',$data->idkskdetail)->count();
      if($check_tb_letters==0){
        $check_tb_letters=DB::table('tb_letters')->insert([
          'id_ksk_detail'=>$data->idkskdetail,
          'periode'=>$periode,
          'sequence_periode'=>$next_sequence,
          'nomor_surat'=>$nomor_surat,
          'lampiran'=>'',
          'hal'=>'Konfirmasi Kontrak Kerja',
          'kepada'=>$employee_name,
          'bagian'=>$bagian,
          'jabatan'=>$jabatan,
          'nomor_perjanjian'=>$nomor_perjanjian,
          'tgl_mulai'=>$tgl_mulai,
          'tgl_akhir'=>$tgl_akhir,
          'tgl_undangan'=>$tgl_undangan,
          'tujuan_undangan'=>'Konfirmasi Kontrak',
          'tgl_surat'=>$tgl,
          'dept_head'=>'Ristiyono',
          'admin'=>$admin,
          'judge'=>$judge,
        ]);
      }else{
        $check_tb_letters=DB::table('tb_letters')->where('id_ksk_detail',$data->idkskdetail)->update([
          //'nomor_surat'=>$nomor_surat,
          //'periode'=>$periode,
          //'sequence_periode'=>$next_sequence,
          'lampiran'=>'',
          'hal'=>'Konfirmasi Kontrak Kerja',
          'kepada'=>$employee_name,
          'bagian'=>$bagian,
          'jabatan'=>$jabatan,
          'nomor_perjanjian'=>$nomor_perjanjian,
          'tgl_mulai'=>$tgl_mulai,
          'tgl_akhir'=>$tgl_akhir,
          'tgl_undangan'=>$tgl_undangan,
          'tujuan_undangan'=>'Konfirmasi Kontrak',
          'tgl_surat'=>$tgl,
          'dept_head'=>'Ristiyono',
          'admin'=>$admin,
          'judge'=>$judge,
        ]);
      }
      $tb_letters=DB::table('tb_letters')->where('tb_letters.id_ksk_detail',$data->idkskdetail)->get();
      //return $tb_letters;
      $FileName=$data->idkskdetail;
      if($judge=='EXTEND')$page='ksk_letter_preview';
      if($judge=='NOT EXTEND')$page='ksk_letter_preview2';
      $pdf = PDF::loadview('page/admin/m_employee/'.$page,['tb_letters'=>$tb_letters])->setPaper('a4','potret');
      return $pdf->stream($FileName);

    }
    function Agreement($type){
      $tb_contract=DB::table('tb_contract')->where('type',$type)->get();
      $tb_department=tb_department::where('isDelete',0)->get();
      $tb_position=tb_position::all();
      $tb_ksk_detail=DB::table('tb_ksk_detail')
      ->leftjoin('tb_statuses','tb_statuses.id','=','tb_ksk_detail.id_kontrak')
      ->leftjoin('tb_contract','tb_contract.id_status','=','tb_statuses.id')
      ->where('tb_ksk_detail.judge','EXTEND')
      ->where('tb_ksk_detail.days','>','0')
      ->where('type',$type)
      ->get(['tb_ksk_detail.id as id_ref','tb_contract.*']);

      //return $tb_ksk_detail;
      return view('page/admin/m_employee/agreement',['tb_contract'=>$tb_contract,'tb_department'=>$tb_department,'tb_position'=>$tb_position,'tb_ksk_detail'=>$tb_ksk_detail,'type'=>$type,'menu'=>'employee','submenu'=>'contract','submenu'=>'contract','Judul'=>'Agreement']);
  }
   function AgreementShow(Request $data){
      $tb_contract=DB::table('tb_contract')->where('id',$data->id)->get();
      foreach($tb_contract as $dt){
        //text tgl ttd
          $hr=date('D',strtotime($dt->tanggal_ttd));
          $hari_ttd=$this->konversi_hari($hr);
          $bl=date('m',strtotime($dt->tanggal_ttd));
          $bulan_ttd=$this->konversi_bulan($bl);
          $tgl_ttd=$hari_ttd.' tanggal '.date('d',strtotime($dt->tanggal_ttd)).' '.$bulan_ttd.' '.date('Y',strtotime($dt->tanggal_ttd));
          $tgl_ttd2=$hari_ttd.', '.date('d',strtotime($dt->tanggal_ttd)).' '.$bulan_ttd.' '.date('Y',strtotime($dt->tanggal_ttd));
        //text tgl ttd end
        //text tgl lahir
          $bl_lahir=date('m',strtotime($dt->tanggal_lahir));
          $bulan_lahir=$this->konversi_bulan($bl_lahir);
          $tgl_lahir=date('d',strtotime($dt->tanggal_lahir)).' '.$bulan_lahir.' '.date('Y',strtotime($dt->tanggal_lahir));
        //text tgl lahir end
        //text durasi
          if($dt->durasi_tahun>0)
          $angka_tahun=$dt->durasi_tahun." (".$this->terbilang($dt->durasi_tahun).") Tahun";
          else
          $angka_tahun='';
          
          $bln=$this->terbilang($dt->durasi_bulan);
          if($dt->durasi_bulan>0)
          $angka_bulan=$dt->durasi_bulan." (".$bln.") Bulan";
          else
          $angka_bulan='';
          
          if($dt->durasi_hari>0)
          $angka_hari=$dt->durasi_hari." (".$this->terbilang($dt->durasi_hari).") Hari";
          else
          $angka_hari='';
          
          $bl_mulai=date('m',strtotime($dt->tanggal_mulai));
          $bulan_mulai=$this->konversi_bulan($bl_mulai);
          $text_mulai=$this->konversi_text_tgl($dt->tanggal_mulai);

          $bl_akhir=date('m',strtotime($dt->tanggal_akhir));
          $bulan_akhir=$this->konversi_bulan($bl_akhir);
          $text_akhir=$this->konversi_text_tgl($dt->tanggal_akhir);

          $durasi=$angka_tahun.' '.$angka_bulan.' '.$angka_hari.', terhitung sejak '.date('d',strtotime($dt->tanggal_mulai)).' '.$bulan_mulai.' '.date('Y',strtotime($dt->tanggal_mulai)).' '.$text_mulai.' s/d '.date('d',strtotime($dt->tanggal_akhir)).' '.$bulan_akhir.' '.date('Y',strtotime($dt->tanggal_akhir)).' '.$text_akhir;
        //text durasi end
        //text upah
          $text_upah=$this->terbilang($dt->upah);
          $upah='Rp. '.number_format($dt->upah).' ('.$text_upah.' rupiah)';
        //text upah end

        //return $upah;

        $type=$dt->type;

      }
      if($type=='Kontrak'){
        $pdf = PDF::loadview('page/admin/m_employee/agreement_preview',['tb_contract'=>$tb_contract,'tgl_ttd'=>$tgl_ttd,'tgl_ttd2'=>$tgl_ttd2,'tgl_lahir'=>$tgl_lahir,'durasi'=>$durasi,'upah'=>$upah])->setPaper('a4','potret');
      }else{
        $pdf = PDF::loadview('page/admin/m_employee/agreement_preview_magang',['tb_contract'=>$tb_contract,'tgl_ttd'=>$tgl_ttd,'tgl_ttd2'=>$tgl_ttd2,'tgl_lahir'=>$tgl_lahir,'durasi'=>$durasi,'upah'=>$upah])->setPaper('a4','potret');
      }
      return $pdf->stream('Agreement');

    }
    function AgreementShow2(Request $data){
      $pos=0;
      $tb_contract=DB::table('tb_contract')->where('id_status',$data->id)->get();
      foreach($tb_contract as $dt){
        //text tgl ttd
          $hr=date('D',strtotime($dt->tanggal_ttd));
          $hari_ttd=$this->konversi_hari($hr);
          $bl=date('m',strtotime($dt->tanggal_ttd));
          $bulan_ttd=$this->konversi_bulan($bl);
          $tgl_ttd=$hari_ttd.' tanggal '.date('d',strtotime($dt->tanggal_ttd)).' '.$bulan_ttd.' '.date('Y',strtotime($dt->tanggal_ttd));
          $tgl_ttd2=$hari_ttd.', '.date('d',strtotime($dt->tanggal_ttd)).' '.$bulan_ttd.' '.date('Y',strtotime($dt->tanggal_ttd));
        //text tgl ttd end
        //text tgl lahir
          $bl_lahir=date('m',strtotime($dt->tanggal_lahir));
          $bulan_lahir=$this->konversi_bulan($bl_lahir);
          $tgl_lahir=date('d',strtotime($dt->tanggal_lahir)).' '.$bulan_lahir.' '.date('Y',strtotime($dt->tanggal_lahir));
        //text tgl lahir end
        //text durasi
          if($dt->durasi_tahun>0)
          $angka_tahun=$dt->durasi_tahun." (".$this->terbilang($dt->durasi_tahun).") Tahun";
          else
          $angka_tahun='';
          
          $bln=$this->terbilang($dt->durasi_bulan);
          if($dt->durasi_bulan>0)
          $angka_bulan=$dt->durasi_bulan." (".$bln.") Bulan";
          else
          $angka_bulan='';
          
          if($dt->durasi_hari>0)
          $angka_hari=$dt->durasi_hari." (".$this->terbilang($dt->durasi_hari).") Hari";
          else
          $angka_hari='';
          
          $bl_mulai=date('m',strtotime($dt->tanggal_mulai));
          $bulan_mulai=$this->konversi_bulan($bl_mulai);
          $text_mulai=$this->konversi_text_tgl($dt->tanggal_mulai);

          $bl_akhir=date('m',strtotime($dt->tanggal_akhir));
          $bulan_akhir=$this->konversi_bulan($bl_akhir);
          $text_akhir=$this->konversi_text_tgl($dt->tanggal_akhir);

          $durasi=$angka_tahun.' '.$angka_bulan.' '.$angka_hari.', terhitung sejak '.date('d',strtotime($dt->tanggal_mulai)).' '.$bulan_mulai.' '.date('Y',strtotime($dt->tanggal_mulai)).' '.$text_mulai.' s/d '.date('d',strtotime($dt->tanggal_akhir)).' '.$bulan_akhir.' '.date('Y',strtotime($dt->tanggal_akhir)).' '.$text_akhir;
        //text durasi end
        //text upah
          $text_upah=$this->terbilang($dt->upah);
          $upah='Rp. '.number_format($dt->upah).' ('.$text_upah.' rupiah)';
        //text upah end

        //return $upah;
        $pos=1;

      }
      if($pos==0)return redirect()->back();
      $pdf = PDF::loadview('page/admin/m_employee/agreement_preview',['tb_contract'=>$tb_contract,'tgl_ttd'=>$tgl_ttd,'tgl_ttd2'=>$tgl_ttd2,'tgl_lahir'=>$tgl_lahir,'durasi'=>$durasi,'upah'=>$upah])->setPaper('a4','potret');
      return $pdf->stream('Agreement');

    }

    function AgreementSave(Request $data){
      $admin=Auth::user()->name;

      $host1 = mysqli_connect("192.168.1.4","ems","123456","db_wilayah");                
      $qry=mysqli_query($host1,"SELECT * FROM wilayah_2020 WHERE kode='$data->provinsi'")or die(mysqli_error($host1));
      while($dt=mysqli_fetch_array($qry)){
          $prov=$dt['nama'];
      }
      $kode_kab=$data->kabupaten;
      $kode_kec=$data->kecamatan;
      $kode_des=$data->kelurahan;
      $kab='';
      $kel='';
      $des='';
      if($data->kabupaten!=''){
        $qry=mysqli_query($host1,"SELECT * FROM wilayah_2020 WHERE kode='$data->kabupaten'")or die(mysqli_error($host1));
        while($dt=mysqli_fetch_array($qry)){
            $kab=$dt['nama'];
        }
      }
      if($data->kecamatan!=''){
        $qry=mysqli_query($host1,"SELECT * FROM wilayah_2020 WHERE kode='$data->kecamatan'")or die(mysqli_error($host1));
        while($dt=mysqli_fetch_array($qry)){
            $kec=$dt['nama'];
        }
      }
      if($data->kelurahan!=''){
        $qry=mysqli_query($host1,"SELECT * FROM wilayah_2020 WHERE kode='$data->kelurahan'")or die(mysqli_error($host1));
        while($dt=mysqli_fetch_array($qry)){
            $des=$dt['nama'];
        }
      }

      $tanggal_awal = new DateTime($data->tanggalmulai);
      $tanggal_akhir = new DateTime($data->tanggalakhir);
      
      // Menghitung perbedaan antara tanggal
      $interval = $tanggal_awal->diff($tanggal_akhir);
      
      $durasi_tahun=$interval->y;
      $durasi_bulan=$interval->m;
      $durasi_hari=$interval->d;
      $durasi_kontrak=$interval->days * ($tanggal_akhir> $tanggal_awal ? 1 : -1);

      $status='';
      if($data->nomorperjanjian==''){
        $tgl=date('ymd');
        $tb_contract=DB::table('tb_contract')->where('nomor_perjanjian','like','%'.$tgl.'%')->where('type',$data->type)->max('nomor_perjanjian');
        if($tb_contract>0)$x=(substr($tb_contract, -3)*1)+1;
        else $x=1;
        if(strlen($x)==1)$nol='00';
        elseif(strlen($x)==2)$nol='0';
        else $nol='';
        $nomor=$nol.$x;
        if($data->type=='Kontrak'){
          $tglprint=date('ymd');
          $generate_id='SAI/HRGA-'.$tglprint.'-'.$nomor;
        }else{
          $tglprint=date('dmY');
          $generate_id='SAI-MG/'.$tglprint.'-'.$nomor;
        }
        $cek=DB::table('tb_contract')->where('tanggal_ttd',$data->tanggalttd)->where('type',$data->type)->where('id_ktp',$data->idktp)->count();
        if($cek==0){
          if($data->idstatusref!=''&&$data->kode_kabupaten==''){
            $cek=DB::table('tb_contract')->where('id_ktp',$data->idktp)->where('type',$data->type)->orderby('tanggal_mulai','desc')->limit(1)->get();
            foreach($cek as $dt){
              $kode_kab=$dt->kode_kabupaten;
              $kode_kec=$dt->kode_kecamatan;
              $kode_des=$dt->kode_kelurahan;
              $kab=$dt->kabupaten;
              $kec=$dt->kecamatan;
              $des=$dt->kelurahan;
            }

          }
  
          $insert=DB::table('tb_contract')->insert([
            'nomor_perjanjian'=>$generate_id,
            'type'=>$data->type,
            'tanggal_ttd'=>$data->tanggalttd,
            'pihak_pertama'=>$data->pihakpertama,
            'jabatan_pihak_pertama'=>$data->jabatanpihakpertama,
            'pihak_kedua'=>$data->pihakkedua,
            'tempat_lahir'=>$data->tempatlahir,
            'tanggal_lahir'=>$data->tanggallahir,
            'jenis_kelamin'=>$data->jeniskelamin,
            'kode_provinsi'=>$data->provinsi,
            'kode_kabupaten'=>$kode_kab,
            'kode_kecamatan'=>$kode_kec,
            'kode_kelurahan'=>$kode_des,
            'provinsi'=>$prov,
            'kabupaten'=>$kab,
            'kecamatan'=>$kec,
            'kelurahan'=>$des,
            'detail'=>$data->detail,
            'id_ktp'=>$data->idktp,
            'tanggal_mulai'=>$data->tanggalmulai,
            'tanggal_akhir'=>$data->tanggalakhir,
            'durasi_tahun'=>$durasi_tahun,
            'durasi_bulan'=>$durasi_bulan,
            'durasi_hari'=>$durasi_hari,
            'durasi_kontrak'=>$durasi_kontrak,
            'bagian'=>$data->bagian,
            'jabatan'=>$data->jabatan,
            'upah'=>$data->upah,
            'id_ksk_detail'=>$data->idstatusref,
            'admin'=>$admin,
          ]);
          //return "Add";
        }
        $status='Add';
      }else{
        $update=DB::table('tb_contract')->where('nomor_perjanjian',$data->nomorperjanjian)->update([
          'tanggal_ttd'=>$data->tanggalttd,
          'pihak_pertama'=>$data->pihakpertama,
          'jabatan_pihak_pertama'=>$data->jabatanpihakpertama,
          'pihak_kedua'=>$data->pihakkedua,
          'tempat_lahir'=>$data->tempatlahir,
          'tanggal_lahir'=>$data->tanggallahir,
          'jenis_kelamin'=>$data->jeniskelamin,
          'kode_provinsi'=>$data->provinsi,
          'provinsi'=>$prov,
          'detail'=>$data->detail,
          'id_ktp'=>$data->idktp,
          'tanggal_mulai'=>$data->tanggalmulai,
          'tanggal_akhir'=>$data->tanggalakhir,
          'durasi_tahun'=>$durasi_tahun,
          'durasi_bulan'=>$durasi_bulan,
          'durasi_hari'=>$durasi_hari,
          'durasi_kontrak'=>$durasi_kontrak,
          'bagian'=>$data->bagian,
          'jabatan'=>$data->jabatan,
          'upah'=>$data->upah,
          'id_ksk_detail'=>$data->idstatusref,
          'admin'=>$admin,
        ]);
        if($data->kode_kabupaten!=''){
          $update=DB::table('tb_contract')->where('nomor_perjanjian',$data->nomorperjanjian)->update([
            'kode_kabupaten'=>$data->kabupaten,
            'kabupaten'=>$kab,
          ]);
        }
        if($data->kode_kecamatan!=''){
          $update=DB::table('tb_contract')->where('nomor_perjanjian',$data->nomorperjanjian)->update([
            'kode_kecamatan'=>$data->kecamatan,
            'kecamatan'=>$kec,
          ]);
        }
        if($data->kode_kelurahan!=''){
          $update=DB::table('tb_contract')->where('nomor_perjanjian',$data->nomorperjanjian)->update([
            'kode_kelurahan'=>$data->kelurahan,
            'kelurahan'=>$des,
          ]);
        }
        //return "Edit";
        $status='Update';
      }
      if($status=='') return "Gagal";
      else return $status;
    }
    function AgreementDelete(Request $data){
      $a=0;
      $h=0;
      $tb_contract=DB::table('tb_contract')->where('id',$data->id)->get();
      foreach($tb_contract as $dt){
        $a=$dt->id_status;
      }
      if($a==''){
        $delete=DB::table('tb_contract')->where('id',$data->id)->delete();
        $h=1;
      }
      return $h;
    }
    public function konversi_hari($hr){
      if($hr=='Mon')$hari='Senin';
      if($hr=='Tue')$hari='Selasa';
      if($hr=='Wed')$hari='Rabu';
      if($hr=='Thu')$hari='Kamis';
      if($hr=='Fri')$hari='Jumat';
      if($hr=='Sat')$hari='Sabtu';
      if($hr=='Sun')$hari='Minggu';
      return $hari;
    }
    public function konversi_bulan($bl){
      if($bl==1)$bulan='Januari';
      if($bl==2)$bulan='Februari';
      if($bl==3)$bulan='Maret';
      if($bl==4)$bulan='April';
      if($bl==5)$bulan='Mei';
      if($bl==6)$bulan='Juni';
      if($bl==7)$bulan='Juli';
      if($bl==8)$bulan='Agustus';
      if($bl==9)$bulan='September';
      if($bl==10)$bulan='Oktober';
      if($bl==11)$bulan='November';
      if($bl==12)$bulan='Desember';
      return $bulan;
    }
    public function konversi_text_tgl($tanggal){
      $tgl=date('d',strtotime($tanggal));
      $text_tgl=$this->terbilang($tgl);

      $bln=date('m',strtotime($tanggal));
      $text_bln=$this->konversi_bulan($bln);

      $thn=date('Y',strtotime($tanggal));
      $text_thn=$this->terbilang($thn);

      $gabungan=strtolower('('.$text_tgl.' '.$text_bln.' '.$text_thn.')');
      return $gabungan;
    }
    public function terbilang($bilangan) {

      $angka = array('0','0','0','0','0','0','0','0','0','0',
                     '0','0','0','0','0','0');
      $kata = array('','satu','dua','tiga','empat','lima',
                    'enam','tujuh','delapan','sembilan');
      $tingkat = array('','ribu','juta','milyar','triliun');
    
      $panjang_bilangan = strlen($bilangan);
    
      /* pengujian panjang bilangan */
      if ($panjang_bilangan > 15) {
        $kalimat = "Diluar Batas";
        return $kalimat;
      }
    
      /* mengambil angka-angka yang ada dalam bilangan,
         dimasukkan ke dalam array */
      for ($i = 1; $i <= $panjang_bilangan; $i++) {
        $angka[$i] = substr($bilangan,-($i),1);
      }
    
      $i = 1;
      $j = 0;
      $kalimat = "";
    
    
      /* mulai proses iterasi terhadap array angka */
      while ($i <= $panjang_bilangan) {
    
        $subkalimat = "";
        $kata1 = "";
        $kata2 = "";
        $kata3 = "";
    
        /* untuk ratusan */
        if ($angka[$i+2] != "0") {
          if ($angka[$i+2] == "1") {
            $kata1 = "seratus";
          } else {
            $kata1 = $kata[$angka[$i+2]] . " ratus";
          }
        }
    
        /* untuk puluhan atau belasan */
        if ($angka[$i+1] != "0") {
          if ($angka[$i+1] == "1") {
            if ($angka[$i] == "0") {
              $kata2 = "sepuluh";
            } elseif ($angka[$i] == "1") {
              $kata2 = "sebelas";
            } else {
              $kata2 = $kata[$angka[$i]] . " belas";
            }
          } else {
            $kata2 = $kata[$angka[$i+1]] . " puluh";
          }
        }
    
        /* untuk satuan */
        if ($angka[$i] != "0") {
          if ($angka[$i+1] != "1") {
            $kata3 = $kata[$angka[$i]];
          }
        }
    
        /* pengujian angka apakah tidak nol semua,
           lalu ditambahkan tingkat */
        if (($angka[$i] != "0") OR ($angka[$i+1] != "0") OR
            ($angka[$i+2] != "0")) {
          $subkalimat = "$kata1 $kata2 $kata3 " . $tingkat[$j] . " ";
        }
    
        /* gabungkan variabe sub kalimat (untuk satu blok 3 angka)
           ke variabel kalimat */
        $kalimat = $subkalimat . $kalimat;
        $i = $i + 3;
        $j = $j + 1;
    
      }
    
      /* mengganti satu ribu jadi seribu jika diperlukan */
      if (($angka[5] == "0") AND ($angka[6] == "0")) {
        $kalimat = str_replace("satu ribu","seribu",$kalimat);
      }
    
      return trim($kalimat);
    
    }     
    function AgreementCheck(Request $data){
      $tb_ksk_detail=DB::table('tb_ksk_detail')
      ->leftjoin('tb_statuses','tb_statuses.id','=','tb_ksk_detail.id_kontrak')
      ->leftjoin('tb_contract','tb_contract.id_status','=','tb_statuses.id')
      ->where('tb_ksk_detail.id',$data->id)->get(['tb_ksk_detail.id as id_ksk_detail','tb_contract.*','tb_ksk_detail.finish_contract as start_new','tb_ksk_detail.date_contract as finish_new']);
      $group_data='';
      foreach($tb_ksk_detail as $dt){
        $group_data=$group_data.'#'.$dt->pihak_kedua;
        $group_data=$group_data.'#'.$dt->id_ktp;
        $group_data=$group_data.'#'.$dt->jenis_kelamin;
        $group_data=$group_data.'#'.$dt->tempat_lahir;
        $group_data=$group_data.'#'.$dt->tanggal_lahir;
        $group_data=$group_data.'#'.$dt->kode_provinsi;
        $group_data=$group_data.'#'.$dt->detail;
        $group_data=$group_data.'#'.$dt->start_new;
        $group_data=$group_data.'#'.$dt->finish_new;
        $group_data=$group_data.'#'.$dt->bagian;
        $group_data=$group_data.'#'.$dt->jabatan;
        $group_data=$group_data.'#'.$dt->upah;
      }
      return $group_data;
    } 
    function AgreementCheck2(Request $data){
      $tb_ksk_detail=DB::table('tb_contract')
      ->where('id',$data->id)->get();
      $group_data='';
      foreach($tb_ksk_detail as $dt){
        $a=date('dmy',strtotime($dt->tanggal_mulai));
        $b=substr($dt->nomor_perjanjian, -3);
        $c=date('dmY',strtotime($dt->tanggal_mulai));
        if($dt->type=='Kontrak'){
          $nik=$a."-".$b;
        }else{
          $nik="MG".$c."-".$b;
        }
        $group_data=$group_data.'#'.$dt->pihak_kedua;
        $group_data=$group_data.'#'.$dt->id_ktp;
        $group_data=$group_data.'#'.$dt->jenis_kelamin;
        $group_data=$group_data.'#'.$dt->tempat_lahir;
        $group_data=$group_data.'#'.$dt->tanggal_lahir;
        $group_data=$group_data.'#'.$dt->kode_provinsi;
        $group_data=$group_data.'#'.$dt->detail;
        $group_data=$group_data.'#'.$dt->tanggal_mulai;
        $group_data=$group_data.'#'.$dt->tanggal_akhir;
        $group_data=$group_data.'#'.$dt->bagian;
        $group_data=$group_data.'#'.$dt->jabatan;
        $group_data=$group_data.'#'.$nik;
      }
      return $group_data;
    } 
    function ContractCheck(Request $data){
      $id_agreement='';
      $nomor_perjanjian='';
      $contract_name='';
      $check=DB::table('tb_ksk_detail')->where('id_kontrak',$data->idstatus)->count();
      if($check>0){
        $tb_status=DB::table('tb_statuses')->where('id',$data->idstatus)->get();
        foreach($tb_status as $dt3){
          $contract_name=$dt3->contract_name;
        }
        $tb_ksk_detail=DB::table('tb_ksk_detail')->where('id_kontrak',$data->idstatus)->get();
        foreach($tb_ksk_detail as $dt){
          $tb_agreement=DB::table('tb_contract')->where('id_ksk_detail',$dt->id)->get();
          foreach($tb_agreement as $dt2){
            $id_agreement=$dt2->id;
            $nomor_perjanjian=$dt2->nomor_perjanjian;
            $tanggal_mulai=$dt2->tanggal_mulai;
            $tanggal_akhir=$dt2->tanggal_akhir;
          }
        }
      }else{
        $tb_employee=DB::table('tb_employees')->where('id',$data->idemployee)->get();
        foreach($tb_employee as $dt){
          $tb_agreement=DB::table('tb_contract')->where('id',$dt->id_agreement)->get();
          foreach($tb_agreement as $dt2){
            $id_agreement=$dt2->id;
            $nomor_perjanjian=$dt2->nomor_perjanjian;
            $tanggal_mulai=$dt2->tanggal_mulai;
            $tanggal_akhir=$dt2->tanggal_akhir;
            $contract_name=$dt2->type;
          }
        }
      }
      return $id_agreement."#".$nomor_perjanjian."#".$contract_name."#".$tanggal_mulai."#".$tanggal_akhir;
    }
    public function notificationKSK($id){
        $tb_ksk=DB::connection('mysql')->table('tb_ksk')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_ksk.dept_id')
        ->where('tb_ksk.id',$id)
        ->get(['tb_ksk.*','tb_departments.dept_name']);
        $data['kontak']='';
        $data['pesan']='';
        $id_employee='';
        foreach($tb_ksk as $dt){
            $id_ksk=$dt->no_ksk;
            $dead_line=$dt->dead_line;
            $dept=$dt->dept_name;
            $periode=$dt->periode;
            if($dt->approval1_status==0){
                $id_employee=$dt->approval1;
                $pos='Atasan Pertama';
            }else if($dt->approval2>0&&$dt->approval2_status==0){
                $id_employee=$dt->approval2;
                $pos='Atasan Kedua';
            }else if($dt->approval3>0&&$dt->approval3_status==0){
                $id_employee=$dt->approval3;
                $pos='Atasan Ketiga';
            }else if($dt->approval4>0&&$dt->approval4_status==0){
                $id_employee=$dt->approval4;
                $pos='Atasan Keempat';
            }
            $qty=DB::table('tb_ksk_detail')->where('id_ksk',$id)->count();
            if($id_employee!=''){
                $data['kontak']=DB::table('tb_employee_detail')->where('id_employee',$id_employee)->value('nomor_telepon');
                $data['pesan']="*NOTIFIKASI KSK*\n\nID: *$id_ksk*\nDead Line: *$dead_line*\nDepartemen: *$dept*\nJumlah: *$qty orang*\n\nMenunggu Approval Anda sebagai *$pos*.\n\nSegera lakukan pengecekan via EMS, klik link berikut:\nhttps://ems.summitadyawinsa.co.id/EMS/Employee/KSK/Detail/$id/$periode";
            }
        }
        // $data['kontak']='08211212418';
        if($data['kontak']!=''){
            // \App\Http\Controllers\WhatsAppController::sendInternalMessage($data['kontak'], $data['pesan']);
            \App\Http\Controllers\WuzapiController::sendInternalMessage($data['kontak'], $data['pesan']);
            return 'Success';
        }else{
            return 'Failed';
        }
    }

}
