<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Imports\WorkEntryImport;
use App\Imports\EarnImport;
use App\Imports\DeductionImport;
use App\Imports\TaxImport;
use App\Imports\RapelImport;
use Maatwebsite\Excel\Facades\Excel;
use Session;
use DateTime;
use Auth;
use PDF;
use App\Mail\slip_Gaji_Payroll;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class payroll_controller extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->site = $_SERVER['SCRIPT_NAME'];
    }
    function index($department, $periode, $tipe)
    {
        if ($periode == 0) {
            $periode = date('Y-m');
        }
        $Thn = date('Y', strtotime($periode . '-01'));
        $default = date('Y-m-d', strtotime($Thn . '-01-01'));

        $tb_employee = $this->tb_employee(1, $tipe);
        $tb_pph21_status = $this->tb_pph21_status();
        $tb_salary_component = $this->tb_salary_component();
        $tb_department = $this->tb_department_salary_contract('1');

        $v_salary_contract = $this->reconcile_ems();
        $tb_salary_contract = $this->tb_salary_contract('1', $department, $tipe);
        $data['koperasi'] = DB::table('tb_salary_component')->where('category', 'Koperasi')->value('amount');
        $data['serikat'] = DB::table('tb_salary_component')->where('category', 'Serikat')->value('amount');
        //return $tb_salary_component;

        if (request()->user()->hasRole('root') || request()->user()->hasRole('payroll') || request()->user()->hasRole('payroll_management')) {
            return view('page/payroll/salary_contract', ['tb_salary_component' => $tb_salary_component, 'data' => $data, 'tb_department' => $tb_department, 'department' => $department, 'tb_pph21_status' => $tb_pph21_status, 'tb_salary_contract' => $tb_salary_contract, 'tb_employee' => $tb_employee, 'default' => $default, 'periode' => $periode, 'tipe' => $tipe, 'site' => $this->site, 'status' => '1', 'menu' => 'payroll_calculation', 'juduls' => 'Salary Contract']);
        } else {
            return abort(403, 'Anda tidak punya akses');
        }
    }
    function arsifKontrak($department, $periode, $tipe)
    {
        if ($periode == 0) {
            $periode = date('Y-m');
        }
        $Thn = date('Y', strtotime($periode . '-01'));
        $default = date('Y-m-d', strtotime($Thn . '-01-01'));

        $tb_employee = $this->tb_employee(1, $tipe);
        $tb_pph21_status = $this->tb_pph21_status();
        $tb_salary_component = $this->tb_salary_component();
        $tb_department = $this->tb_department_salary_contract('0');
        $data['koperasi'] = DB::table('tb_salary_component')->where('category', 'Koperasi')->value('amount');
        $data['serikat'] = DB::table('tb_salary_component')->where('category', 'Serikat')->value('amount');

        $tb_salary_contract = $this->tb_salary_contract('0', $department, $tipe);
        if (request()->user()->hasRole('hr_access')) {
            return view('page/payroll/salary_contract', ['tb_salary_component' => $tb_salary_component, 'data' => $data, 'tb_department' => $tb_department, 'department' => $department, 'tb_pph21_status' => $tb_pph21_status, 'tb_salary_contract' => $tb_salary_contract, 'tb_employee' => $tb_employee, 'default' => $default, 'periode' => $periode, 'tipe' => $tipe, 'site' => $this->site, 'status' => '0', 'menu' => 'payroll_calculation', 'juduls' => 'Arsif Contract']);
        } else {
            return abort(403, 'Anda tidak punya akses');
        }
    }
    function salary_detail($department, $periode, $tipe)
    {
        if ($periode == 0) {
            $periode = date('Y-m');
        }
        $Thn = date('Y', strtotime($periode . '-01'));
        $default = date('Y-m-d', strtotime($Thn . '-01-01'));

        $tb_employee = $this->tb_employee(1, $tipe);
        $tb_pph21_status = $this->tb_pph21_status();
        $tb_salary_component = $this->tb_salary_component();
        $tb_department = $this->tb_department_salary_contract('1');

        $v_salary_contract = $this->reconcile_ems();
        $tb_salary_contract = $this->tb_salary_contract('1', $department, $tipe);
        $data['koperasi'] = DB::table('tb_salary_component')->where('category', 'Koperasi')->value('amount');
        $data['serikat'] = DB::table('tb_salary_component')->where('category', 'Serikat')->value('amount');

        if (request()->user()->hasRole('root') || request()->user()->hasRole('payroll') || request()->user()->hasRole('payroll_management')) {
            return view('page/payroll/salary_contract_detail', ['tb_salary_component' => $tb_salary_component, 'data' => $data, 'tb_department' => $tb_department, 'department' => $department, 'tb_pph21_status' => $tb_pph21_status, 'tb_salary_contract' => $tb_salary_contract, 'tb_employee' => $tb_employee, 'default' => $default, 'periode' => $periode, 'tipe' => $tipe, 'site' => $this->site, 'status' => '1', 'menu' => 'payroll_calculation', 'juduls' => 'Salary Contract']);
        } else {
            return abort(403, 'Anda tidak punya akses');
        }
    }
    function simpanKontrak(Request $data)
    {
        $konten = 'Gag';
        //$status_management=0;
        $admin = Auth::user()->name;
        $today = date('Y-m-d');
        $now = date('Y-m-d H:i:s');
        $periode = date('Y-m');
        //Replace titik
        $upah_pokok = str_replace(".", "", $data->upah_pokok);
        $prosentase_upah = str_replace(".", "", $data->prosentase_upah);
        $upah_harian = str_replace(".", "", $data->upah_harian);
        $tunjangan_harian = str_replace(".", "", $data->tunjangan_harian);
        $tunjangan_jabatan = str_replace(".", "", $data->tunjangan_jabatan);
        $tunjangan_prestasi_tahunan = str_replace(".", "", $data->tunjangan_prestasi_tahunan);
        $tunjangan_skill = str_replace(".", "", $data->tunjangan_skill);
        $tunjangan_makan = str_replace(".", "", $data->tunjangan_makan);
        $tunjangan_transport = str_replace(".", "", $data->tunjangan_transport);
        $tunjangan_shift = str_replace(".", "", $data->tunjangan_shift);
        $total_salary = str_replace(".", "", $data->total_salary);
        $bpjs_salary_awal = str_replace(".", "", $data->bpjs_salary);
        //$bpjs_salary=$bpjs_salary_awal*100/$prosentase_upah;
        $bpjs_salary = floor($bpjs_salary_awal);
        $slpj = str_replace(".", "", $data->slpj);
        if ($data->tipe_kontrak == 'SAI' || $data->tipe_kontrak == 'MANAGEMENT') {
            $jaminan_hari_tua = str_replace(".", "", $data->jaminan_hari_tua);
            $jaminan_hari_tua_perusahaan = 0;
        } else if ($data->tipe_kontrak == 'TKA') {
            $jaminan_hari_tua = str_replace(".", "", $data->jaminan_hari_tua);
            $jaminan_hari_tua_perusahaan = 0;
        } else if ($data->tipe_kontrak == 'MAGANG') {
            $jaminan_hari_tua_perusahaan = str_replace(".", "", $data->jaminan_hari_tua);
            $jaminan_hari_tua = 0;
        } else {
            $jaminan_hari_tua_perusahaan = 0;
            $jaminan_hari_tua = 0;
        }
        $jaminan_pensiun = str_replace(".", "", $data->jaminan_pensiun);
        $bpjs_tambahan = str_replace(".", "", $data->bpjs_tambahan);
        $bpjs_kesehatan = str_replace(".", "", $data->bpjs_kesehatan);
        $prosentase_bpjs = str_replace(".", "", $data->prosentase_bpjs);
        $jaminan_kecelakaan_kerja = str_replace(".", "", $data->jaminan_kecelakaan_kerja);
        $jaminan_kematian = str_replace(".", "", $data->jaminan_kematian);
        $kesehatan = str_replace(".", "", $data->kesehatan);
        //End replcae    
        //Get Detail Employee
        if ($data->id_salary_kontrak > 0) {
            $tb_salary_contract = DB::table('tb_salary_contract')->where('id', $data->id_salary_kontrak)->get();
            foreach ($tb_salary_contract as $dt) {
                $id_employee = $dt->id_employee;
            }
        } else {
            $id_employee = $data->id_employee;
        }
        $cek_detail = DB::table('tb_employee_detail')->where('id_employee', $id_employee)->count();
        if ($cek_detail == 0) {
            $add_detail = DB::table('tb_employee_detail')->insert([
                'id_employee' => $id_employee,
                'nomor_npwp' => $data->npwp,
                'nomor_rekening' => $data->rekening,
            ]);
        } else {
            $update_detail = DB::table('tb_employee_detail')->where('id_employee', $id_employee)->update([
                'nomor_npwp' => $data->npwp,
                'nomor_rekening' => $data->rekening,
            ]);
        }
        $tb_employee = DB::table('tb_employees')
            ->leftjoin('tb_departments', 'tb_departments.id', '=', 'tb_employees.dept_id')
            ->leftjoin('tb_positions', 'tb_positions.id', '=', 'tb_employees.position_id')
            ->leftjoin('tb_employee_detail', 'tb_employee_detail.id_employee', '=', 'tb_employees.id')
            ->leftjoin('tb_cost_center', 'tb_cost_center.cc_code', '=', 'tb_employees.cc_code')
            ->where('tb_employees.id', $id_employee)
            ->get(['tb_employees.*', 'tb_departments.dept_code', 'tb_positions.position_name', 'tb_employees.cc_code', 'tb_cost_center.description', 'tb_employee_detail.nomor_npwp', 'tb_employee_detail.nomor_rekening', 'tb_positions.position_index']);
        //$status_npwp='1';
        //$status_rekening='0';
        foreach ($tb_employee as $dt) {
            $NIK = $dt->NIK;
            $employee_name = $dt->employee_name;
            $dept_code = $dt->dept_code;
            $cc_code = $dt->cc_code;
            $cc_name = $dt->description;
            $position_name = $dt->position_name;
            if ($dt->nomor_npwp != '' && $dt->nomor_npwp != '0')
                $status_npwp = '1';
            else
                $status_npwp = '0';
            //Manual Adjust
            $status_npwp = 1;
            if ($dt->nomor_rekening != '' && $dt->nomor_rekening != '0')
                $status_rekening = '1';
            else
                $status_rekening = '0';
            if ($dt->position_index > 4) {
                $status_management = '1';
            } else {
                $status_management = '0';
            }
        }
        //return $tb_employee;
        //End Get Detail Employee
        if ($data->id_salary_kontrak == '') {
            $simpan = DB::table('tb_salary_contract')->insert([
                'id_employee' => $data->id_employee,
                'NIK' => $NIK,
                'nama_karyawan' => $employee_name,
                'department' => $dept_code,
                'cc_code' => $cc_code,
                'cc_name' => $cc_name,
                'jabatan' => $position_name,
                'tanggal_kontrak' => $data->tanggal_kontrak,
                'tanggal_expired' => $data->tanggal_expired,
                'tipe_kontrak' => $data->tipe_kontrak,
                'upah_pokok' => $upah_pokok,
                'prosentase_upah' => $prosentase_upah,
                'upah_harian' => $upah_harian,
                'tunjangan_harian' => $tunjangan_harian,
                'tunjangan_jabatan' => $tunjangan_jabatan,
                'tunjangan_prestasi_tahunan' => $tunjangan_prestasi_tahunan,
                'tunjangan_skill' => $tunjangan_skill,
                'tunjangan_makan' => $tunjangan_makan,
                'tunjangan_transport' => $tunjangan_transport,
                'tunjangan_shift' => $tunjangan_shift,
                'total_salary' => $total_salary,
                'bpjs_salary' => $bpjs_salary,
                'slpj' => $slpj,
                'jaminan_hari_tua' => $jaminan_hari_tua,
                'jaminan_hari_tua_perusahaan' => $jaminan_hari_tua_perusahaan,
                'jaminan_pensiun' => $jaminan_pensiun,
                'bpjs_tambahan' => $bpjs_tambahan,
                'bpjs_kesehatan' => $bpjs_kesehatan,
                'prosentase_bpjs' => $prosentase_bpjs,
                'serikat' => $data->serikat,
                'koperasi' => $data->koperasi,
                'status_pajak' => $data->status_pajak,
                'jaminan_kecelakaan_kerja' => $jaminan_kecelakaan_kerja,
                'jaminan_kematian' => $jaminan_kematian,
                'kesehatan' => $kesehatan,
                'status_npwp' => $status_npwp,
                'status_rekening' => $status_rekening,
                'status_bpjs' => $data->status_bpjs,
                'status_management' => $status_management,
                'working_day' => $data->working_day,
                'status' => 1,
                'admin' => $admin,
                'created_at' => $today,
                'updated_at' => $now
            ]);
            // if($simpan)$tb_workentry=DB::table('tb_work_entry')->insert([
            //     'id_employee'=>$data->id_employee,
            //     'periode'=>$periode,
            // ]);
        } else {
            $simpan = DB::table('tb_salary_contract')->where('id', $data->id_salary_kontrak)->update([
                'tanggal_kontrak' => $data->tanggal_kontrak,
                'tanggal_expired' => $data->tanggal_expired,
                'tipe_kontrak' => $data->tipe_kontrak,
                'upah_pokok' => $upah_pokok,
                'prosentase_upah' => $prosentase_upah,
                'upah_harian' => $upah_harian,
                'tunjangan_harian' => $tunjangan_harian,
                'tunjangan_jabatan' => $tunjangan_jabatan,
                'tunjangan_prestasi_tahunan' => $tunjangan_prestasi_tahunan,
                'tunjangan_skill' => $tunjangan_skill,
                'tunjangan_makan' => $tunjangan_makan,
                'tunjangan_transport' => $tunjangan_transport,
                'tunjangan_shift' => $tunjangan_shift,
                'total_salary' => $total_salary,
                'bpjs_salary' => $bpjs_salary,
                'slpj' => $slpj,
                'jaminan_hari_tua' => $jaminan_hari_tua,
                'jaminan_hari_tua_perusahaan' => $jaminan_hari_tua_perusahaan,
                'jaminan_pensiun' => $jaminan_pensiun,
                'bpjs_tambahan' => $bpjs_tambahan,
                'bpjs_kesehatan' => $bpjs_kesehatan,
                'prosentase_bpjs' => $prosentase_bpjs,
                'serikat' => $data->serikat,
                'koperasi' => $data->koperasi,
                'status_pajak' => $data->status_pajak,
                'jaminan_kecelakaan_kerja' => $jaminan_kecelakaan_kerja,
                'jaminan_kematian' => $jaminan_kematian,
                'kesehatan' => $kesehatan,
                'status_npwp' => $status_npwp,
                'status_rekening' => $status_rekening,
                //'status_management'=>$status_management,
                'status_bpjs' => $data->status_bpjs,
                'working_day' => $data->working_day,
                'status' => 1,
                'admin' => $admin,
                'created_at' => $today,
                'updated_at' => $now
            ]);
        }
        if ($simpan) {
            $konten = 'Sukses';
            $tb_salaries = DB::table('tb_salaries')->where('id_employee', $id_employee)->where('status', '1')->count();
            if ($tb_salaries == 0) {
                $insert_slpj_ems = DB::table('tb_salaries')->insert([
                    'id_employee' => $id_employee,
                    'salary' => '0',
                    'slpj' => $slpj,
                    'meal' => '0',
                    'upah_pokok' => '0',
                    'tunjangan_jabatan' => '0',
                    'tunjangan_skill' => '0',
                    'tunjangan_prestasi' => '0',
                    'BPJS_JHT' => '0',
                    'BPJS_JP' => '0',
                    'BPJS_Kesehatan' => '0',
                    'Koperasi' => '0',
                    'Serikat' => '0',
                    'implement' => $data->tanggal_kontrak,
                    'status' => '1',
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
            } else {
                $update_slpj_ems = DB::table('tb_salaries')->where('id_employee', $id_employee)->where('status', '1')->update([
                    'slpj' => $slpj,
                    'implement' => $data->tanggal_kontrak,
                    'updated_at' => $now
                ]);
            }
            $this->updateSummaryOne($periode, $data->id_employee, $data->tipe_kontrak);
        } else
            $konten = 'Gagal';
        return $konten;
    }


    function deleteKontrak($id)
    {
        if (request()->user()->hasRole('root') || request()->user()->hasRole('payroll') || request()->user()->hasRole('payroll_management')) {
            $tb_salary_contract = DB::table('tb_salary_contract')->where('id', $id)->get();
            foreach ($tb_salary_contract as $dt) {
                $status = $dt->status;
            }
            if ($status == 1)
                $change = 0;
            else
                $change = 1;
            $update = DB::table('tb_salary_contract')->where('id', $id)->update(['status' => $change]);
            if ($update)
                return redirect()->back()->with(['info' => 'Success']);
        } else {
            return abort(403, 'Anda tidak punya akses');
        }
    }

    function earn($periode, $department, $component, $tipe)
    {
        $tb_employee = $this->tb_employee_contract();
        $tb_department = $this->tb_department_periode($periode, 'tb_salary_earn', '0');
        $tb_salary_earn = $this->tb_earn($periode, $department, $component, $tipe);
        if (request()->user()->hasRole('root') || request()->user()->hasRole('payroll') || request()->user()->hasRole('payroll_management')) {
            return view('page/payroll/salary_earn', ['tb_salary_earn' => $tb_salary_earn, 'tb_employee' => $tb_employee, 'tb_department' => $tb_department, 'periode' => $periode, 'department' => $department, 'tipe' => $tipe, 'site' => $this->site, 'status' => '1', 'menu' => 'payroll_calculation', 'juduls' => 'Salary Earn']);
        } else {
            return abort(403, 'Anda tidak punya akses');
        }
    }
    function earnSimpan(Request $data)
    {
        $admin = Auth::user()->name;
        $today = date('Y-m-d');
        $now = date('Y-m-d H:i:s');
        if ($data->idcomponent > 0) {
            $simpan = DB::table('tb_salary_earn')->where('id', $data->idcomponent)->update([
                'periode' => $data->periodeearn,
                'earn_amount' => $data->amount,
                'component_name' => $data->componentname,
                'description' => $data->description,
                'admin' => $admin,
                'updated_at' => $now,
            ]);
            if ($simpan)
                $pesan = "Sukses";
            else
                $pesan = "Gagal Simpan";
        } else {
            $tb_salary_earn = DB::table('tb_salary_earn')->where('periode', $data->periode)->where('component_name', $data->componentname)->where('id_employee', $data->idemployee)->count();
            if ($tb_salary_earn > 0)
                $pesan = 'Komponen untuk perode ini sudah ada';
            else {
                $update = DB::table('tb_salary_earn')->insert([
                    'id_employee' => $data->idemployee,
                    'tipe_kontrak' => $data->tipe,
                    'periode' => $data->periodeearn,
                    'earn_amount' => $data->amount,
                    'component_name' => $data->componentname,
                    'description' => $data->description,
                    'created_at' => $now,
                    'admin' => $admin,
                    'updated_at' => $now,
                ]);
                if ($update)
                    $pesan = "Sukses";
                else
                    $pesan = "Gagal Update";
            }
        }
        //$x = $this->updateSummaryOne($data->periodeearn, $data->idemployee, $data->tipe);
        return $pesan;
    }
    function earnDelete($id)
    {
        if (request()->user()->hasRole('root') || request()->user()->hasRole('payroll') || request()->user()->hasRole('payroll_management')) {
            $delete = DB::table('tb_salary_earn')->where('id', $id)->delete();
            if ($delete)
                return redirect()->back()->with(['info' => 'Deleted']);
        } else {
            return abort(403, 'Anda tidak punya akses');
        }
    }

    function deduction($periode, $department, $component, $tipe)
    {
        $tb_employee = $this->tb_employee_contract();
        $tb_department = $this->tb_department_periode($periode, 'tb_salary_deduction', '0');
        $tb_salary_deduction = $this->tb_deduction($periode, $department, $component, $tipe);
        if (request()->user()->hasRole('root') || request()->user()->hasRole('payroll') || request()->user()->hasRole('payroll_management')) {
            return view('page/payroll/salary_deduction', ['tb_salary_deduction' => $tb_salary_deduction, 'tb_employee' => $tb_employee, 'tb_department' => $tb_department, 'periode' => $periode, 'department' => $department, 'tipe' => $tipe, 'site' => $this->site, 'status' => '1', 'menu' => 'payroll_calculation', 'juduls' => 'Salary Deduction']);
        } else {
            return abort(403, 'Anda tidak punya akses');
        }
    }
    function deductionSimpan(Request $data)
    {
        $admin = Auth::user()->name;
        $today = date('Y-m-d');
        $now = date('Y-m-d H:i:s');
        if ($data->idcomponent > 0) {
            $simpan = DB::table('tb_salary_deduction')->where('id', $data->idcomponent)->update([
                'periode' => $data->periodeearn,
                'tipe_kontrak' => $data->tipe,
                'deduction_amount' => $data->amount,
                'component_name' => $data->componentname,
                'description' => $data->description,
                'admin' => $admin,
                'updated_at' => $now,
            ]);
            if ($simpan)
                $pesan = "Sukses";
            else
                $pesan = "Gagal Simpan";
        } else {
            $tb_salary_deduction = DB::table('tb_salary_deduction')->where('periode', $data->periode)->where('component_name', $data->componentname)->where('id_employee', $data->idemployee)->count();
            if ($tb_salary_deduction > 0)
                $pesan = 'Komponen untuk perode ini sudah ada';
            else {
                $update = DB::table('tb_salary_deduction')->insert([
                    'id_employee' => $data->idemployee,
                    'tipe_kontrak' => $data->tipe,
                    'periode' => $data->periodeearn,
                    'deduction_amount' => $data->amount,
                    'component_name' => $data->componentname,
                    'description' => $data->description,
                    'created_at' => $now,
                    'admin' => $admin,
                    'updated_at' => $now,
                ]);
                if ($update)
                    $pesan = "Sukses";
                else
                    $pesan = "Gagal Update";
            }
        }
        //$x = $this->updateSummaryOne($data->periodeearn, $data->idemployee, $data->tipe);
        return $pesan;
    }
    function deductionDelete($id)
    {
        if (request()->user()->hasRole('root') || request()->user()->hasRole('payroll') || request()->user()->hasRole('payroll_management')) {
            $delete = DB::table('tb_salary_deduction')->where('id', $id)->delete();
            if ($delete)
                return redirect()->back()->with(['info' => 'Deleted']);
        } else {
            return abort(403, 'Anda tidak punya akses');
        }
    }

    function workEntry($periode, $department, $tipe)
    {
        $tb_employee = $this->tb_employee_workentry($periode, $department, $tipe);
        $tb_department = $this->tb_department_periode($periode, 'tb_work_entry', $tipe);
        $tb_work_entry = $this->tb_work_entry($periode, $department, $tipe);
        if (request()->user()->hasRole('root') || request()->user()->hasRole('payroll') || request()->user()->hasRole('payroll_management')) {
            return view('page/payroll/work_entry', ['tb_work_entry' => $tb_work_entry, 'tb_employee' => $tb_employee, 'tb_department' => $tb_department, 'periode' => $periode, 'department' => $department, 'tipe' => $tipe, 'site' => $this->site, 'menu' => 'payroll_calculation', 'juduls' => 'Work Entry']);
        } else {
            return abort(403, 'Anda tidak punya akses');
        }
    }
    function ImportWorkEntry(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'file' => 'required|mimes:csv,xls,xlsx'
        ]);

        if ($validator->fails()) {
            $uploadedFile = $request->file('file');
            return response()->json([
                'status' => 'validation_failed',
                'errors' => $validator->errors()->all(),
                'has_file' => $request->hasFile('file'),
                'is_valid_upload' => $uploadedFile ? $uploadedFile->isValid() : null,
                'upload_error' => $uploadedFile ? $uploadedFile->getErrorMessage() : 'No file',
                'client_mime' => $uploadedFile ? $uploadedFile->getClientMimeType() : null,
                'detected_mime' => $uploadedFile ? $uploadedFile->getMimeType() : null,
                'client_extension' => $uploadedFile ? $uploadedFile->getClientOriginalExtension() : null,
                'original_name' => $uploadedFile ? $uploadedFile->getClientOriginalName() : null,
            ]);
        }
        $file = $request->file('file');
        $nama_file = rand() . '_' . $file->getClientOriginalName();
        $file->move(public_path('excel'), $nama_file);
        $import = Excel::import(new WorkEntryImport, public_path('excel/' . $nama_file));
        if ($import) {
            return redirect()->back()->with(['info' => 'Import Berhasil']);
        }
    }
    function templateWorkEntry($periode, $tipe)
    {
        $tb_employee = $this->tb_employee_template($tipe);
        if (request()->user()->hasRole('root') || request()->user()->hasRole('payroll')) {
            return view('page/payroll/work_entry_template', ['tb_employee' => $tb_employee, 'periode' => $periode, 'tipe' => $tipe]);
        } else {
            return abort(403, 'Anda tidak punya akses');
        }
    }
    function resetWorkEntry($periode, $tipe)
    {
        if (request()->user()->hasRole('root') || request()->user()->hasRole('payroll')) {
            $reset = DB::table('tb_work_entry')
                ->leftjoin('tb_salary_contract', 'tb_salary_contract.id_employee', '=', 'tb_work_entry.id_employee')
                ->where('periode', $periode);
            // $reset=$reset->where('tb_salary_contract.tipe_kontrak',$tipe);
            if ($tipe != 0 && ($tipe == 'SAI' || $tipe == 'TKA')) {
                $reset = $reset->where(function ($query) use ($tipe) {
                    $query->where('tb_salary_contract.tipe_kontrak', 'SAI')
                        ->orWhere('tb_salary_contract.tipe_kontrak', 'TKA');
                });
            }
            if ($tipe != 0 && ($tipe == 'MAGANG' || $tipe == 'MANAGEMENT')) {
                $reset = $reset->where('tb_salary_contract.tipe_kontrak', $tipe);
            }
            $reset = $reset->delete();
            if ($reset)
                return redirect()->back()->with(['info' => 'Reset Berhasil']);
        } else {
            return abort(403, 'Anda tidak punya akses');
        }
    }
    function simpanWorkEntry(request $data)
    {
        $status = '';
        $now = date('Y-m-d H:i:s');
        if ($data->idworkentry == 0) {
            $cek = DB::table('tb_work_entry')->where('id_employee', $data->idemployee)->where('periode', $data->periodework)->count();
            if ($cek > 0)
                $status = "Failed, Duplicat Entry";
            else {
                $simpan = DB::table('tb_work_entry')->insert([
                    'id_employee' => $data->idemployee,
                    'tipe_kontrak' => $data->tipe,
                    'periode' => $data->periodework,
                    'hari_kerja' => '0',
                    'aktual_kerja' => $data->workentry,
                    'ot_weekend' => '0',
                    'cuti' => '0',
                    'aktual_shift' => $data->workshift,
                    'ijin' => '0',
                    'sakit' => '0',
                    'mangkir' => '0',
                    'absen' => $data->absent,
                    'import_status' => '0'
                ]);
                if ($simpan)
                    $status = 'Success';
            }
        } else {
            $update = DB::table('tb_work_entry')->where('id', $data->idworkentry)->update([
                'aktual_kerja' => $data->workentry,
                'aktual_shift' => $data->workshift,
                'absen' => $data->absent,
            ]);
            if ($update)
                $status = 'Success';
        }
        return $status;
    }
    function workentryDelete($id)
    {
        if (request()->user()->hasRole('root') || request()->user()->hasRole('payroll') || request()->user()->hasRole('payroll_management')) {
            $delete = DB::table('tb_work_entry')->where('id', $id)->delete();
            if ($delete)
                return redirect()->back()->with(['info' => 'Deleted']);
        } else {
            return abort(403, 'Anda tidak punya akses');
        }
    }

    function summary($periode, $department, $tipe)
    {
        //$this->updateSummary($periode,$department,$tipe);
        $tb_department = $this->tb_department_periode($periode, 'tb_salary_summary', $tipe);
        $tb_salary_summary = $this->tb_salary_summary($periode, $department, $tipe);
        $tb_salary_component = $this->tb_salary_component();
        foreach ($tb_salary_component as $dt) {
            if ($dt->component == 'hari_kerja_psab')
                $hari_kerja_psab = $dt->amount;
        }
        if (request()->user()->hasRole('root') || request()->user()->hasRole('payroll') || request()->user()->hasRole('payroll_management')) {
            return view('page/payroll/salary_summary', ['tb_salary_summary' => $tb_salary_summary, 'tb_department' => $tb_department, 'periode' => $periode, 'department' => $department, 'tipe' => $tipe, 'hari_kerja_psab' => $hari_kerja_psab, 'site' => $this->site, 'status' => '1', 'menu' => 'payroll_calculation', 'juduls' => 'Summary Salary']);
        } else {
            return abort(403, 'Anda tidak punya akses');
        }
    }
    function summaryPDF($periode, $department, $tipe)
    {
        $tb_department = $this->tb_department_periode($periode, 'tb_salary_summary', $tipe);
        $tb_salary_summary = $this->tb_salary_summary($periode, $department, $tipe);
        $tb_salary_component = $this->tb_salary_component();
        foreach ($tb_salary_component as $dt) {
            if ($dt->component == 'hari_kerja_psab')
                $hari_kerja_psab = $dt->amount;
        }
        if (request()->user()->hasRole('root') || request()->user()->hasRole('payroll') || request()->user()->hasRole('payroll_management')) {
            return view('page/payroll/salary_summary_pdf', ['tb_salary_summary' => $tb_salary_summary, 'tb_department' => $tb_department, 'periode' => $periode, 'department' => $department, 'tipe' => $tipe, 'hari_kerja_psab' => $hari_kerja_psab, 'site' => $this->site, 'status' => '1', 'menu' => 'payroll_calculation', 'juduls' => 'Summary Salary']);
        } else {
            return abort(403, 'Anda tidak punya akses');
        }
    }
    function summaryDownloadPDF($periode, $department, $tipe)
    {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '-1');
        $tb_department = $this->tb_department_periode($periode, 'tb_salary_summary', $tipe);
        $tb_salary_summary = $this->tb_salary_summary($periode, $department, $tipe);
        $tb_salary_component = $this->tb_salary_component();
        foreach ($tb_salary_component as $dt) {
            if ($dt->component == 'hari_kerja_psab')
                $hari_kerja_psab = $dt->amount;
        }
        if (request()->user()->hasRole('root') || request()->user()->hasRole('payroll') || request()->user()->hasRole('payroll_management')) {
            $FileName = 'Salary_Summary_' . $tipe . '_' . $periode . '.pdf';
            $pdf = PDF::loadview('page/payroll/salary_summary_pdf', ['tb_salary_summary' => $tb_salary_summary, 'tb_department' => $tb_department, 'periode' => $periode, 'department' => $department, 'tipe' => $tipe, 'hari_kerja_psab' => $hari_kerja_psab, 'site' => $this->site, 'status' => '1', 'menu' => 'payroll', 'juduls' => 'Summary Salary', 'is_pdf' => true])->setPaper('a3', 'landscape');
            return $pdf->stream($FileName);
        } else {
            return abort(403, 'Anda tidak punya akses');
        }
    }
    function compensationDownloadPDF($periode, $department, $tipe)
    {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '-1');
        $tb_department = $this->tb_department_periode($periode, 'tb_salary_summary', $tipe);
        if ($periode == 0) {
            $periode = date('Y-m');
        }
        //$this->compensationupdate($periode);
        $admin = Auth::user()->name;

        $end = date('Y-m-d', strtotime($periode . '-24'));
        $awal_bulan = date('Y-m-d', strtotime($periode . '-01'));
        $akhir_bulan = date('Y-m', strtotime('-1 days', strtotime($awal_bulan)));
        $start = date('Y-m-d', strtotime($akhir_bulan . '-25'));

        // $data=$this->ter_calculation('tb_kompensasi','kompensasi',$periode);
        $data = $this->ppha21_pesangon($periode);

        $tb_kompensasi = DB::table('tb_kompensasi')
            ->leftjoin('tb_employees', 'tb_employees.id', '=', 'tb_kompensasi.id_employee')
            ->leftjoin('tb_employee_detail', 'tb_employee_detail.id_employee', '=', 'tb_employees.id')
            ->where('Finish_Kontrak', '>=', $start)->where('Finish_Kontrak', '<=', $end)->where('isDelete', '0')
            ->orderby('tb_kompensasi.Bagian', 'asc')->orderby('tb_employees.cc_code', 'asc')->orderby('tb_kompensasi.Nama', 'asc')
            ->get(['tb_kompensasi.*', 'tb_employees.cc_code', 'tb_employee_detail.nomor_rekening']);
        if (request()->user()->hasRole('root') || request()->user()->hasRole('payroll') || request()->user()->hasRole('payroll_management')) {
            $FileName = 'Compensation_' . $periode . '.pdf';
            $pdf = PDF::loadview('page/payroll/salary_compensation_excel', ['tb_kompensasi' => $tb_kompensasi, 'tb_department' => $tb_department, 'periode' => $periode, 'department' => '0', 'tipe' => 'SAI', 'site' => $this->site, 'status' => '1', 'menu' => 'compensation', 'juduls' => 'Compensation', 'is_pdf' => true])->setPaper('a4', 'portrait');
            return $pdf->stream($FileName);
        } else {
            return abort(403, 'Anda tidak punya akses');
        }
    }
    function slip($periode, $kategori, $id_employee)
    {
        $thn = date('Y', strtotime($periode . '-01'));
        $bln = date('F', strtotime($periode . '-01'));
        $periode_text = $thn . ' ' . $bln;

        $tb_salary_summary_employee = $this->tb_salary_summary_employee($periode, $id_employee);
        $view = '';
        if ($kategori == 'SAI' || $kategori == 'MANAGEMENT')
            $view = "page/payroll/salary_slip_sai";
        if ($kategori == 'MAGANG')
            $view = "page/payroll/salary_slip_magang";
        if ($kategori == 'PSAB')
            $view = "page/payroll/salary_slip_psab";
        if ($kategori == 'PKL')
            $view = "page/payroll/salary_slip_pkl";
        if ($view == '')
            return abort(403, 'Under Maintenance');
        $FileName = 'SLIP.PDF';
        if ($kategori == 'PSAB' || $kategori == 'PKL')
            $pdf = PDF::loadview($view, ['tb_salary_summary_employee' => $tb_salary_summary_employee, 'periode_text' => $periode_text, 'thn' => $thn, 'site' => $this->site, 'menu' => 'salary_slip'])->setPaper(array(0, 0, 280, 355));
        else
            $pdf = PDF::loadview($view, ['tb_salary_summary_employee' => $tb_salary_summary_employee, 'periode_text' => $periode_text, 'thn' => $thn, 'site' => $this->site, 'menu' => 'salary_slip'])->setPaper(array(0, 0, 280, 630));
        return $pdf->stream($FileName);

    }
    function updateRapel(Request $data)
    {
        $admin = Auth::user()->name;
        $today = date('Y-m-d');
        $now = date('Y-m-d H:i:s');
        $tb_salary_summary = DB::table('tb_salary_summary')->where('id', $data->id_summary)->get();
        foreach ($tb_salary_summary as $dt) {
            $periode = $dt->periode;
            $id_employee = $dt->id_employee;
            $pendapatan_bruto = $dt->pendapatan_bruto - $dt->rapel + $data->rapel;
            $pendapatan_netto = $dt->pendapatan_netto - $dt->rapel + $data->rapel;
        }
        $tb_rapel = DB::table('tb_salary_earn')->where('component_name', 'rapel')->where('periode', $periode)->where('id_employee', $id_employee)->count();
        if ($tb_rapel == 0) {
            $create = DB::table('tb_salary_earn')->insert([
                'periode' => $periode,
                'id_employee' => $id_employee,
                'earn_amount' => $data->rapel,
                'component_name' => 'rapel',
                'description' => $data->description,
                'admin' => $admin,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        } else {
            $update = DB::table('tb_salary_earn')->where('component_name', 'rapel')->where('periode', $periode)->where('id_employee', $id_employee)->update([
                'earn_amount' => $data->rapel,
                'description' => $data->description,
                'updated_at' => $now,
            ]);
        }
        $hasil = "";
        $update = DB::table('tb_salary_summary')->where('id', $data->id_summary)->update([
            'rapel' => $data->rapel,
            'pendapatan_bruto' => $pendapatan_bruto,
            'pendapatan_netto' => $pendapatan_netto,
        ]);
        if ($update)
            $hasil = "Sukses";
        return $hasil;
    }
    function tax($periode, $department, $tipe)
    {
        $sisa_bulan = $this->sisa_bulan($periode);
        $tb_salary_component = $this->tb_salary_component();
        foreach ($tb_salary_component as $dt) {
            if ($dt->component == 'biaya_jabatan') {
                $biaya_jabatan_p = $dt->presentase;
                $biaya_jabatan_l = $dt->amount;
            }
        }
        $tb_pph21_status = $this->tb_pph21_status();
        $tb_pph21 = $this->tb_pph21();
        $tb_department = $this->tb_department_periode($periode, 'tb_salary_tax', $tipe);
        $tb_salary_tax = $this->tb_salary_tax($periode, $department, $tipe);

        //$tb_pph21_ter=$this->tb_pph21_ter('A','7000000');
        //return $tb_pph21_ter;

        if (request()->user()->hasRole('root') || request()->user()->hasRole('payroll') || request()->user()->hasRole('payroll_management')) {
            return view('page/payroll/salary_tax', ['tb_salary_tax' => $tb_salary_tax, 'tb_department' => $tb_department, 'periode' => $periode, 'department' => $department, 'biaya_jabatan_p' => $biaya_jabatan_p, 'biaya_jabatan_l' => $biaya_jabatan_l, 'tb_pph21' => $tb_pph21, 'tb_pph21_status' => $tb_pph21_status, 'tipe' => $tipe, 'sisa_bulan' => $sisa_bulan, 'site' => $this->site, 'status' => '1', 'menu' => 'payroll', 'juduls' => 'Tax Calculation']);
        } else {
            return abort(403, 'Anda tidak punya akses');
        }
    }
    function taxter_monthly($periode, $department, $tipe, $category)
    {
        $tb_department = $this->tb_department_periode($periode, 'tb_salary_ter_monthly', $tipe);
        $tb1 = DB::table('tb_salary_ter_monthly as a')
            ->leftjoin('tb_salary_contract as b', 'b.id_employee', '=', 'a.id_employee')
            ->leftjoin('tb_pph21_status as c', 'c.id', '=', 'a.status_pajak')
            ->where('a.category', $category)->where('a.periode', $periode)->where('a.category', $category);
        if ($department != 0) {
            $tb1 = $tb1->where('b.department', $department);
        }
        $tb1 = $tb1->get(['a.*', 'b.NIK', 'b.nama_karyawan', 'b.department', 'b.jabatan', 'b.tipe_kontrak', 'c.status_pajak', 'c.kategori']);
        if (request()->user()->hasRole('root') || request()->user()->hasRole('payroll') || request()->user()->hasRole('payroll_management')) {
            return view('page/payroll/salary_tax_termonthly', ['tb1' => $tb1,'tb_department' => $tb_department, 'periode' => $periode, 'department' => $department, 'category' => $category, 'tipe' => $tipe, 'site' => $this->site, 'status' => '1', 'menu' => 'payroll_calculation', 'juduls' => 'Tax Calculation']);
        } else {
            return abort(403, 'Anda tidak punya akses');
        }
    }
    function taxConditional($periode, $department, $tipe)
    {
        $tb_salary_component = $this->tb_salary_component();
        foreach ($tb_salary_component as $dt) {
            if ($dt->component == 'biaya_jabatan') {
                $biaya_jabatan_p = $dt->presentase;
                $biaya_jabatan_l = $dt->amount;
            }
        }
        $tb_pph21_status = $this->tb_pph21_status();
        $tb_pph21 = $this->tb_pph21();
        $tb_department = $this->tb_department_periode($periode, 'tb_salary_tax', $tipe);
        $tb_salary_tax = $this->tb_salary_tax($periode, $department, $tipe);
        if (request()->user()->hasRole('root') || request()->user()->hasRole('payroll') || request()->user()->hasRole('payroll_management')) {
            return view('page/payroll/salary_tax_conditional', ['tb_salary_tax' => $tb_salary_tax, 'tb_department' => $tb_department, 'periode' => $periode, 'department' => $department, 'biaya_jabatan_p' => $biaya_jabatan_p, 'biaya_jabatan_l' => $biaya_jabatan_l, 'tb_pph21' => $tb_pph21, 'tb_pph21_status' => $tb_pph21_status, 'tipe' => $tipe, 'site' => $this->site, 'status' => '1', 'menu' => 'payroll', 'juduls' => 'Tax Calculation']);
        } else {
            return abort(403, 'Anda tidak punya akses');
        }
    }
    function taxConditional_template($periode, $department, $tipe)
    {
        $reset = DB::table('tb_salary_tax_conditional')->where('periode', $periode)->delete();
        $tb_salary_component = $this->tb_salary_component();
        foreach ($tb_salary_component as $dt) {
            if ($dt->component == 'biaya_jabatan') {
                $biaya_jabatan_p = $dt->presentase;
                $biaya_jabatan_l = $dt->amount;
            }
        }
        $tb_pph21_status = $this->tb_pph21_status();
        $tb_pph21 = $this->tb_pph21();
        $tb_department = $this->tb_department_periode($periode, 'tb_salary_tax', $tipe);
        $tb_salary_tax = $this->tb_salary_tax($periode, $department, $tipe);
        if (request()->user()->hasRole('root') || request()->user()->hasRole('payroll') || request()->user()->hasRole('payroll_management')) {
            return view('page/payroll/tax_conditional_template', ['tb_salary_tax' => $tb_salary_tax, 'tb_department' => $tb_department, 'periode' => $periode, 'department' => $department, 'biaya_jabatan_p' => $biaya_jabatan_p, 'biaya_jabatan_l' => $biaya_jabatan_l, 'tb_pph21' => $tb_pph21, 'tb_pph21_status' => $tb_pph21_status, 'tipe' => $tipe, 'site' => $this->site, 'status' => '1', 'menu' => 'payroll', 'juduls' => 'Tax Calculation']);
        } else {
            return abort(403, 'Anda tidak punya akses');
        }
    }
    function ImportTax(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:csv,xls,xlsx'
        ]);
        $file = $request->file('file');
        $nama_file = rand() . $file->getClientOriginalName();
        $file->move('laravel/public/excel', $nama_file);
        $import = Excel::import(new TaxImport, public_path('/excel/' . $nama_file));
        if ($import)
            return redirect()->back()->with(['info' => 'Import Berhasil']);
    }
    function UpdateTax(Request $data)
    {
        $update = DB::table('tb_salary_tax_conditional')->where('id', $data->idtaxconditional)->update([
            'tax_amount' => $data->taxamount,
            'status_implement' => $data->status_implement,
        ]);
        $tb_salary_tax = DB::table('tb_salary_tax')
            ->leftjoin('tb_salary_tax_conditional', 'tb_salary_tax_conditional.id_salary_tax', '=', 'tb_salary_tax.id')
            ->where('tb_salary_tax_conditional.id', $data->idtaxconditional)->get(['tb_salary_tax.*']);
        foreach ($tb_salary_tax as $dt) {
            $x = $this->updateSummaryOne($data->periode, $dt->id_employee, $dt->tipe_kontrak);
            //return "Masuk";
        }
        //return $x;
        return redirect()->back()->with(['info' => 'Success Update']);
    }

    public function sendMail($periode, $department, $tipe, $tabel)
    {
        $tb_salary_summary = DB::table($tabel)
            ->leftjoin('tb_salary_contract', 'tb_salary_contract.id_employee', '=', $tabel . '.id_employee')
            ->leftjoin('tb_pph21_status', 'tb_pph21_status.id', '=', 'tb_salary_contract.status_pajak')
            ->where('periode', $periode);
        if ($tipe != 0 && ($tipe == 'SAI' || $tipe == 'TKA')) {
            $tb_salary_summary = $tb_salary_summary->where(function ($query) use ($tipe) {
                $query->where('tb_salary_contract.tipe_kontrak', 'SAI')
                    ->orWhere('tb_salary_contract.tipe_kontrak', 'TKA');
            });
        } else {
            $tb_salary_summary = $tb_salary_summary->where('tb_salary_contract.tipe_kontrak', $tipe);
        }

        //->where('tb_salary_contract.tipe_kontrak',$tipe);
        // if(request()->user()->hasRole('root')){
        //     $tb_salary_summary=$tb_salary_summary->where('tb_salary_contract.status','1');
        // }elseif(request()->user()->hasRole('payroll')){
        //     $tb_salary_summary=$tb_salary_summary->where('tb_salary_contract.status','1')->where('tb_salary_contract.status_management','0');
        // }elseif(request()->user()->hasRole('payroll_management')){
        //     $tb_salary_summary=$tb_salary_summary->where('tb_salary_contract.status','1')->where('tb_salary_contract.status_management','1');
        // }
        if ($department != 0) {
            $tb_salary_summary = $tb_salary_summary->where('tb_salary_contract.department', $department);
        }
        $tb_salary_summary = $tb_salary_summary->get([$tabel . '.*', 'tb_pph21_status.status_pajak as statuspajak', 'tb_salary_contract.NIK', 'tb_salary_contract.nama_karyawan', 'tb_salary_contract.department as kode_bagian', 'tb_salary_contract.jabatan', 'tb_salary_contract.status_pajak', 'tb_salary_contract.total_salary', 'tb_salary_contract.tipe_kontrak', 'tb_salary_contract.upah_harian as upah_psab', 'tb_salary_contract.tunjangan_harian as tunjangan_pkl', 'tb_salary_contract.tanggal_kontrak']);

        Log::info('Distribute Mail Start');
        $i = 0;
        foreach ($tb_salary_summary as $dt) {
            $i++;
            $id_employee = $dt->id_employee;
            $employee_name = $dt->nama_karyawan;
            $periode_text = date('F Y', strtotime($dt->periode . '-01'));
            $tb_email = DB::table('tb_emails')->where('id_employee', $id_employee)->get();
            if ($tabel == 'tb_salary_summary')
                $component = 'Gaji';
            else {
                $component = strtoupper($dt->component_name);
            }
            foreach ($tb_email as $dt2) {
                $update = DB::table($tabel)->where('id', $dt->id)->update(['mail_status' => '1']);
                //if($update)Log::info($i); 
                Log::info('Distribute to ' . $dt2->email_address);
                //if($id_employee=='111'){
                //$tujuan='cahyudin@summitadyawinsa.co.id';
                //if($dt->mail_status==0){
                //$tujuan=$dt2->email_address;
                //$kirim=Mail::to($tujuan)->queue(new slip_Gaji_Payroll($id_employee,$tujuan,$employee_name,$periode_text,$component));
                //Log::info($i); 
                //DB::table($tabel)->where('id',$dt->id)->update(['mail_status'=>'1']);
                //}
                //}
            }
        }

        Log::info('Distribute Mail End');
        //return $tb_email;
        return redirect()->back();
    }
    public function sendMailOne($id, $tabel)
    {
        $tb_salary_summary = DB::table($tabel)
            ->leftjoin('tb_salary_contract', 'tb_salary_contract.id_employee', '=', $tabel . '.id_employee')
            ->where($tabel . '.id', $id)->get([$tabel . '.*', 'tb_salary_contract.nama_karyawan']);
        //return $tb_salary_summary;
        Log::info('sendMail_Start');
        $i = 0;
        foreach ($tb_salary_summary as $dt) {
            $i++;
            $id_employee = $dt->id_employee;
            $employee_name = $dt->nama_karyawan;
            $periode_text = date('F Y', strtotime($dt->periode . '-01'));
            if ($tabel == 'tb_salary_summary')
                $component = 'Gaji';
            else {
                $component = strtoupper($dt->component_name);
            }
            $tb_email = DB::table('tb_emails')->where('id_employee', $id_employee)->get();
            foreach ($tb_email as $dt2) {
                if ($dt->mail_status == 0) {
                    //$tujuan=$dt2->email_address;
                    $tujuan = 'cahyudin@summitadyawinsa.co.id';
                    $kirim = Mail::to($tujuan)->queue(new slip_Gaji_Payroll($id_employee, $tujuan, $employee_name, $periode_text, $component));
                    Log::info($i);
                    DB::table($tabel)->where('id', $dt->id)->update(['mail_status' => '1']);
                }
            }
        }

        Log::info('slipGaji_End');
        //return $tb_email;
        return redirect()->back();
    }
    public function sendMailCompensation($id)
    {
        $tb_kompensasi = DB::table('tb_kompensasi')
            ->where('tb_kompensasi.id', $id)->get();
        //return $tb_salary_summary;
        Log::info('sendMail_Start');
        $i = 0;
        foreach ($tb_kompensasi as $dt) {
            $i++;
            $id_employee = $dt->id_employee;
            $employee_name = $dt->Nama;
            $periode_text = date('d-M-Y', strtotime($dt->pay_date . '-01'));
            $tb_email = DB::table('tb_emails')->where('id_employee', $id_employee)->get();
            foreach ($tb_email as $dt2) {
                if ($dt->send_mail == 0) {
                    $tujuan = $dt2->email_address;
                    //$employee_name=$dt2->email_address;
                    //$tujuan='cahyudin@summitadyawinsa.co.id';
                    $kirim = Mail::to($tujuan)->queue(new slip_Gaji_Payroll($id_employee, $tujuan, $employee_name, $periode_text));
                    Log::info($i);
                    DB::table('tb_kompensasi')->where('id', $dt->id)->update(['send_mail' => '1']);
                }
            }
        }

        Log::info('slipGaji_End');
        //return $tb_email;
        return redirect()->back();
    }

    function recap($periode, $tipe)
    {
        $admin = Auth::user()->name;
        $nik = Auth::user()->nik;
        $id_employee = DB::table('tb_employees')->where('NIK', $nik)->value('id');
        if ($periode == 0)
            $periode = date('Y-m');
        $check_approval = DB::table('tb_payroll_approval')->where('periode', $periode)->where('category', 'payroll');
        // $check_approval=$check_approval->where('tipe',$tipe);
        if ($tipe != 0 && ($tipe == 'SAI' || $tipe == 'TKA')) {
            $check_approval = $check_approval->where(function ($query) use ($tipe) {
                $query->where('tb_payroll_approval.tipe', 'SAI')
                    ->orWhere('tb_payroll_approval.tipe', 'TKA');
            });
        } else {
            $check_approval = $check_approval->where('tb_payroll_approval.tipe', $tipe);
        }
        $check_approval = $check_approval->count();
        if ($check_approval == 0) {
            if ($tipe == 'PSAB') {
                $tb1 = DB::table('v_approval')->where('group', '2')->orderby('sequance', 'asc')->get();
                $no = 0;
                foreach ($tb1 as $dt1) {
                    $no++;
                    $dt_id[$no] = $dt1->id;
                    $dt_name[$no] = $dt1->employee_name;
                    $dt_pos[$no] = $dt1->position_name;
                }
                $add_approval = DB::table('tb_payroll_approval')->insert([
                    'periode' => $periode,
                    'category' => 'payroll',
                    'tipe' => $tipe,
                    'id_approver_1' => $dt_id[1],
                    'id_approver_2' => $dt_id[2],
                    'id_approver_3' => $dt_id[3],
                    'id_approver_4' => $dt_id[4],
                    'id_approver_5' => $dt_id[5],
                    'id_approver_6' => $dt_id[6],
                    'id_approver_7' => $dt_id[7],
                    'name_1' => $dt_name[1],
                    'name_2' => $dt_name[2],
                    'name_3' => $dt_name[3],
                    'name_4' => $dt_name[4],
                    'name_5' => $dt_name[5],
                    'name_6' => $dt_name[6],
                    'name_7' => $dt_name[7],
                    'pos_1' => $dt_pos[1],
                    'pos_2' => $dt_pos[2],
                    'pos_3' => $dt_pos[3],
                    'pos_4' => $dt_pos[4],
                    'pos_5' => $dt_pos[5],
                    'pos_6' => $dt_pos[6],
                    'pos_7' => $dt_pos[7],
                    'created_by' => $admin
                ]);
            } else {
                $tb1 = DB::table('v_approval')->where('group', '1')->orderby('sequance', 'asc')->get();
                $no = 0;
                foreach ($tb1 as $dt1) {
                    $no++;
                    $dt_id[$no] = $dt1->id;
                    $dt_name[$no] = $dt1->employee_name;
                    $dt_pos[$no] = $dt1->position_name;
                }
                $add_approval = DB::table('tb_payroll_approval')->insert([
                    'periode' => $periode,
                    'category' => 'payroll',
                    'tipe' => $tipe,
                    'id_approver_1' => $dt_id[1],
                    'id_approver_2' => $dt_id[2],
                    'id_approver_3' => $dt_id[3],
                    'id_approver_4' => $dt_id[4],
                    'id_approver_5' => $dt_id[5],
                    'name_1' => $dt_name[1],
                    'name_2' => $dt_name[2],
                    'name_3' => $dt_name[3],
                    'name_4' => $dt_name[4],
                    'name_5' => $dt_name[5],
                    'pos_1' => $dt_pos[1],
                    'pos_2' => $dt_pos[2],
                    'pos_3' => $dt_pos[3],
                    'pos_4' => $dt_pos[4],
                    'pos_5' => $dt_pos[5],
                    'created_by' => $admin
                ]);
            }
        }
        $tb_approval = DB::table('tb_payroll_approval')->where('periode', $periode)->where('category', 'payroll');
        if ($tipe != 0 && ($tipe == 'SAI' || $tipe == 'TKA')) {
            $tb_approval = $tb_approval->where(function ($query) use ($tipe) {
                $query->where('tb_payroll_approval.tipe', 'SAI')
                    ->orWhere('tb_payroll_approval.tipe', 'TKA');
            });
        } else {
            $tb_approval = $tb_approval->where('tb_payroll_approval.tipe', $tipe);
        }
        $tb_approval = $tb_approval->get();

        $tabel = DB::table('tb_salary_summary')
            ->join('tb_salary_contract', function ($join) {
                $join->on('tb_salary_contract.id_employee', '=', 'tb_salary_summary.id_employee')
                    ->on('tb_salary_contract.tipe_kontrak', '=', 'tb_salary_summary.tipe_kontrak');
            })
            ->leftjoin('tb_dept_category', 'tb_dept_category.dept_code', '=', 'tb_salary_contract.department')
            ->select('tb_salary_contract.department', 'tb_dept_category.category', 'tb_dept_category.segment', 'tb_salary_contract.cc_code', 'tb_salary_contract.cc_name', DB::raw('COUNT(tb_salary_contract.id_employee) as jml_employee,SUM(tb_salary_contract.total_salary) as total_salary,SUM(tb_salary_summary.tunjangan_shift) as total_shift,SUM(tb_salary_summary.tunjangan_transport) as total_transport,SUM(tb_salary_summary.pendapatan_netto) as total_paid,SUM(tb_salary_summary.rapel) as total_rapel'))
            ->groupby('tb_salary_contract.department', 'tb_dept_category.dept_code', 'tb_dept_category.category', 'tb_dept_category.segment', 'tb_salary_contract.cc_code', 'tb_salary_contract.cc_name');
        $tabel = $tabel->where('periode', $periode);
        if ($tipe != 0 && ($tipe == 'SAI' || $tipe == 'TKA')) {
            $tabel = $tabel->where(function ($query) use ($tipe) {
                $query->where('tb_salary_contract.tipe_kontrak', 'SAI')
                    ->orWhere('tb_salary_contract.tipe_kontrak', 'TKA');
            });
        } else {
            $tabel = $tabel->where('tb_salary_contract.tipe_kontrak', $tipe);
        }
        $tabel = $tabel->orderby('tb_dept_category.category', 'asc')
            ->orderby('tb_dept_category.dept_code', 'asc')
            ->orderby('tb_salary_contract.cc_code', 'asc')
            ->get();
        $tb_salary_component = $this->tb_salary_component();
        foreach ($tb_salary_component as $dt) {
            if ($dt->component == 'hari_kerja_psab')
                $hari_kerja_psab = $dt->amount;
        }
        $tb_working_day = DB::table('tb_working_day')->where('periode', $periode)->where('kelompok', '5HK')->get();
        foreach ($tb_working_day as $dt2) {
            $hari_kerja = $dt2->hari_kerja;
        }
        $view = '';
        if ($tipe == 'SAI' || $tipe == 'MAGANG' || $tipe == 'MANAGEMENT') {
            $view = "page/payroll/salary_rekap";
            $tb_salary_summary = $this->tb_salary_summary($periode, '0', $tipe);
            $jumlah_diterima_by_department = DB::table('tb_salary_summary')
                ->join('tb_salary_contract', function ($join) {
                    $join->on('tb_salary_contract.id_employee', '=', 'tb_salary_summary.id_employee')
                        ->on('tb_salary_contract.tipe_kontrak', '=', 'tb_salary_summary.tipe_kontrak');
                })
                ->where('tb_salary_summary.periode', $periode)
                ->where('tb_salary_summary.tipe_kontrak', $tipe)
                ->where('tb_salary_contract.status_rekening', '1')
                ->select('tb_salary_contract.department', DB::raw('SUM(tb_salary_summary.pendapatan_netto) as jumlah_diterima'))
                ->groupBy('tb_salary_contract.department')
                ->pluck('jumlah_diterima', 'department');
            foreach ($tabel as $dt) {
                $dt->jumlah_diterima = $jumlah_diterima_by_department->get($dt->department, 0);
                $dt->manual = $dt->total_paid != $dt->jumlah_diterima
                    ? $dt->total_paid - $dt->jumlah_diterima
                    : 0;
            }
        } elseif ($tipe == 'PSAB') {
            $view = "page/payroll/salary_rekap_psab";
            $tb_salary_summary = DB::table('tb_salary_summary_other')
                ->leftJoin('v_salary_contract_other', 'v_salary_contract_other.id_employee', '=', 'tb_salary_summary_other.id_employee')
                ->where('tb_salary_summary_other.periode', $periode)
                ->select('tb_salary_summary_other.*', 'v_salary_contract_other.employee_name', 'v_salary_contract_other.NIK', 'v_salary_contract_other.department', 'v_salary_contract_other.kategori')
                ->get();
        } elseif ($tipe == 'PKL') {
            $view = "page/payroll/salary_rekap_pkl";
            $tb_salary_summary = DB::table('tb_salary_summary_pkl')
                ->leftJoin('v_salary_contract_pkl', 'v_salary_contract_pkl.id_employee_other', '=', 'tb_salary_summary_pkl.id_employee_other')
                ->where('tb_salary_summary_pkl.periode', $periode)
                ->select('tb_salary_summary_pkl.*', 'v_salary_contract_pkl.nama', 'v_salary_contract_pkl.department', 'v_salary_contract_pkl.kategori')
                ->get();
        } elseif ($tipe == 'Compensation') {
            $end = date('Y-m-d', strtotime($periode . '-24'));
            $awal_bulan = date('Y-m-d', strtotime($periode . '-01'));
            $akhir_bulan = date('Y-m', strtotime('-1 days', strtotime($awal_bulan)));
            $start = date('Y-m-d', strtotime($akhir_bulan . '-25'));

            $view = "page/payroll/salary_rekap_compensation";
            $tabel = DB::table('v_kompensasi')
                ->select(
                    'bagian',
                    'cc_code',
                    DB::raw('count(*) as jml_employee'),
                    DB::raw('sum(total_pendapatan) as salary'),
                    DB::raw('sum(Kompensasi) as compensation_gross'),
                    DB::raw('sum(PPH21) as tax'),
                    DB::raw('sum(Total_Kompensasi) as compensation_nett')
                )
                ->where('Finish_Kontrak', '>=', $start)
                ->where('Finish_Kontrak', '<=', $end)
                ->groupBy('bagian', 'cc_code')
                ->get();
            $tb_salary_summary = $tabel;
        } else {
            return abort(403, 'Under Maintenance');
        }
        //return $tabel;
        $tb_department = $this->tb_department_salary_contract('1');

        if (request()->user()->hasRole('root') || request()->user()->hasRole('payroll') || request()->user()->hasRole('payroll_management')) {
            return view($view, ['tb_rekap' => $tabel, 'tb_department' => $tb_department, 'id_employee' => $id_employee, 'department' => '0', 'tb_salary_summary' => $tb_salary_summary, 'periode' => $periode, 'tipe' => $tipe, 'hari_kerja_psab' => $hari_kerja_psab, 'tb_approval' => $tb_approval, 'site' => $this->site, 'menu' => 'payroll_recap', 'juduls' => 'Salary Recap']);
        } else {
            return abort(403, 'Anda tidak punya akses');
        }
    }
    function recapSave(Request $data)
    {
        $now = date('Y-m-d H:i:s');
        $status = 'status_' . $data->kolom;
        $date = 'date_' . $data->kolom;
        $update = DB::table('tb_payroll_approval')->where('periode', $data->periode)->where('tipe', $data->tipe)->where('category', $data->category)->update([
            $status => $data->status,
            $date => $now
        ]);
    }
    function recapPDF($periode, $tipe)
    {
        $tb_approval = DB::table('tb_payroll_approval')->where('periode', $periode)->where('tipe', $tipe)->where('category', 'payroll')->get();
        $tabel = DB::table('tb_salary_summary')
            ->join('tb_salary_contract', function ($join) {
                $join->on('tb_salary_contract.id_employee', '=', 'tb_salary_summary.id_employee')
                    ->on('tb_salary_contract.tipe_kontrak', '=', 'tb_salary_summary.tipe_kontrak');
            })
            ->leftjoin('tb_dept_category', 'tb_dept_category.dept_code', '=', 'tb_salary_contract.department')
            ->select('tb_salary_contract.department', 'tb_dept_category.category', 'tb_dept_category.segment', 'tb_salary_contract.cc_code', 'tb_salary_contract.cc_name', DB::raw('COUNT(tb_salary_contract.id_employee) as jml_employee,SUM(tb_salary_contract.total_salary) as total_salary,SUM(tb_salary_summary.rapel) as total_rapel,SUM(tb_salary_summary.tunjangan_shift) as total_shift,SUM(tb_salary_summary.tunjangan_transport) as total_transport,SUM(tb_salary_summary.pendapatan_netto) as total_paid,SUM(tb_salary_summary.rapel) as total_rapel'))
            ->groupby('tb_salary_contract.department', 'tb_dept_category.dept_code', 'tb_dept_category.category', 'tb_dept_category.segment', 'tb_salary_contract.cc_code', 'tb_salary_contract.cc_name');
        $tabel = $tabel->where('periode', $periode);
        if ($tipe != 0 && ($tipe == 'SAI' || $tipe == 'TKA')) {
            $tabel = $tabel->where(function ($query) use ($tipe) {
                $query->where('tb_salary_contract.tipe_kontrak', 'SAI')
                    ->orWhere('tb_salary_contract.tipe_kontrak', 'TKA');
            });
        } else {
            $tabel = $tabel->where('tb_salary_contract.tipe_kontrak', $tipe);
        }
        $tabel = $tabel->orderby('tb_dept_category.category', 'asc')
            ->orderby('tb_dept_category.dept_code', 'asc')
            ->orderby('tb_salary_contract.cc_code', 'asc')
            ->get();
        //return $tabel;
        // $tb_salary_summary=$this->tb_salary_summary($periode,'0',$tipe);
        $tb_salary_component = $this->tb_salary_component();
        foreach ($tb_salary_component as $dt) {
            if ($dt->component == 'hari_kerja_psab')
                $hari_kerja_psab = $dt->amount;
        }
        $tb_working_day = DB::table('tb_working_day')->where('periode', $periode)->where('kelompok', '5HK')->get();
        $hari_kerja = 0;
        foreach ($tb_working_day as $dt2) {
            $hari_kerja = $dt2->hari_kerja;
        }
        $view = '';
        if ($tipe == 'SAI' || $tipe == 'MAGANG' || $tipe == 'MANAGEMENT') {
            $view = "page/payroll/salary_rekap_pdf";
            $tb_salary_summary = $this->tb_salary_summary($periode, '0', $tipe);
        } elseif ($tipe == 'PSAB') {
            $view = "page/payroll/salary_rekap_psab_pdf";
            $tb_salary_summary = DB::table('tb_salary_summary_other')
                ->leftJoin('v_salary_contract_other', 'v_salary_contract_other.id_employee', '=', 'tb_salary_summary_other.id_employee')
                ->where('tb_salary_summary_other.periode', $periode)
                ->select('tb_salary_summary_other.*', 'v_salary_contract_other.employee_name', 'v_salary_contract_other.NIK', 'v_salary_contract_other.department', 'v_salary_contract_other.kategori')
                ->get();
        } elseif ($tipe == 'PKL') {
            $view = "page/payroll/salary_rekap_pkl_pdf";
            $tb_salary_summary = DB::table('tb_salary_summary_pkl')
                ->leftJoin('v_salary_contract_pkl', 'v_salary_contract_pkl.id_employee_other', '=', 'tb_salary_summary_pkl.id_employee_other')
                ->where('tb_salary_summary_pkl.periode', $periode)
                ->select('tb_salary_summary_pkl.*', 'v_salary_contract_pkl.nama', 'v_salary_contract_pkl.department', 'v_salary_contract_pkl.kategori')
                ->get();
        } elseif ($tipe == 'Compensation') {
            $end = date('Y-m-d', strtotime($periode . '-24'));
            $awal_bulan = date('Y-m-d', strtotime($periode . '-01'));
            $akhir_bulan = date('Y-m', strtotime('-1 days', strtotime($awal_bulan)));
            $start = date('Y-m-d', strtotime($akhir_bulan . '-25'));

            $view = "page/payroll/salary_rekap_compensation_pdf";
            $tabel = DB::table('v_kompensasi')
                ->select(
                    'bagian',
                    'cc_code',
                    DB::raw('count(*) as jml_employee'),
                    DB::raw('sum(total_pendapatan) as salary'),
                    DB::raw('sum(Kompensasi) as compensation_gross'),
                    DB::raw('sum(PPH21) as tax'),
                    DB::raw('sum(Total_Kompensasi) as compensation_nett')
                )
                ->where('Finish_Kontrak', '>=', $start)
                ->where('Finish_Kontrak', '<=', $end)
                ->groupBy('bagian', 'cc_code')
                ->get();
            $tb_salary_summary = $tabel;
        } else {
            return abort(403, 'Under Maintenance');
        }
        //return $tabel;
        if (request()->user()->hasRole('root') || request()->user()->hasRole('payroll') || request()->user()->hasRole('payroll_management')) {
            $FileName = 'RECAP.PDF';
            if ($tipe == 'SAI' || $tipe == 'MAGANG' || $tipe == 'MANAGEMENT')
                $pdf = PDF::loadview($view, ['tb_rekap' => $tabel, 'tb_salary_summary' => $tb_salary_summary, 'tb_approval' => $tb_approval, 'periode' => $periode, 'tipe' => $tipe, 'hari_kerja_psab' => $hari_kerja_psab, 'site' => $this->site, 'menu' => 'payroll', 'juduls' => 'Salary Recap'])->setPaper(array(0, 0, 2480, 1950));
            elseif ($tipe == 'Compensation')
                $pdf = PDF::loadview($view, ['tb_rekap' => $tabel, 'tb_salary_summary' => $tb_salary_summary, 'tb_approval' => $tb_approval, 'periode' => $periode, 'tipe' => $tipe, 'hari_kerja_psab' => $hari_kerja_psab, 'site' => $this->site, 'menu' => 'payroll', 'juduls' => 'Salary Recap'])->setPaper(array(0, 0, 650, 800));
            else
                $pdf = PDF::loadview($view, ['tb_rekap' => $tabel, 'tb_salary_summary' => $tb_salary_summary, 'tb_approval' => $tb_approval, 'periode' => $periode, 'tipe' => $tipe, 'hari_kerja_psab' => $hari_kerja_psab, 'hari_kerja' => $hari_kerja, 'site' => $this->site, 'menu' => 'payroll', 'juduls' => 'Salary Recap'])->setPaper(array(0, 0, 842, 1191));
            return $pdf->stream($FileName);
        } else {
            return abort(403, 'Anda tidak punya akses');
        }
    }

    function compensation_deleted($periode)
    {
        //$this->compensationupdate($periode);
        $admin = Auth::user()->name;
        $now = date('Y-m-d H:i:s');
        //update pph21
        $filter = '%' . $periode . '%';
        $tb_kompensasi = DB::table('tb_kompensasi')->where('pay_date', 'like', $filter)->where('isDelete', '0')->orderby('pay_date')->get();
        foreach ($tb_kompensasi as $dt) {
            $tb_salary_contract_employee = $this->tb_salary_contract_employee($dt->id_employee, 'SAI');
            foreach ($tb_salary_contract_employee as $dt4) {
                $status_npwp = $dt4->status_npwp;
                $status_pajak = $dt4->status_pajak;
            }
            if ($dt->contract_name == 'End Contract') {
                $tb_salary_tax = DB::table('tb_salary_tax')->where('id_employee', $dt->id_employee)->orderby('periode', 'desc')->limit(1)->get();
                foreach ($tb_salary_tax as $dt2) {

                    $tb_pph21_pesangon = DB::table('tb_pph21_pesangon')->get();
                    foreach ($tb_pph21_pesangon as $dt3) {
                        if ($dt3->id == 1) {
                            $batas1 = $dt3->batas_atas;
                            $pajak1 = $dt3->prosentase;
                        }
                        if ($dt3->id == 2) {
                            $batas2 = $dt3->batas_atas;
                            $pajak2 = $dt3->prosentase;
                        }
                        if ($dt3->id == 3) {
                            $batas3 = $dt3->batas_atas;
                            $pajak3 = $dt3->prosentase;
                        }
                        if ($dt3->id == 4) {
                            $pajak4 = $dt3->prosentase;
                        }
                    }
                    $pkp = $dt->Kompensasi;
                    if ($pkp <= $batas1) {
                        $pph21 = $pkp * $pajak1 / 100;
                    } else if ($pkp > $batas1 && $pkp <= $batas2) {
                        $angka1 = $batas1 * $pajak1 / 100;
                        $angka2 = ($pkp - $batas1) * $pajak2 / 100;
                        $pph21 = $angka1 + $angka2;
                    } else if ($pkp > $batas2 && $pkp <= $batas3) {
                        $angka1 = $batas1 * $pajak1 / 100;
                        $angka2 = ($batas2 - $batas1) * $pajak2 / 100;
                        $angka3 = ($pkp - $batas2) * $pajak3 / 100;
                        $pph21 = $angka1 + $angka2 + $angka3;
                    } else if ($pkp > $batas3) {
                        $angka1 = $batas1 * $pajak1 / 100;
                        $angka2 = ($batas2 - $batas1) * $pajak2 / 100;
                        $angka3 = ($batas3 - $batas2) * $pajak3 / 100;
                        $angka4 = ($pkp - $batas3) * $pajak4 / 100;
                        $pph21 = $angka1 + $angka2 + $angka3 + $angka4;
                    }
                    if ($pph21 > 0)
                        $pph21_fix = $pph21;
                    else
                        $pph21_fix = 0;

                    if ($status_npwp == 0)
                        $pph21_tambahan = $pph21 * 20 / 100;
                    else
                        $pph21_tambahan = 0;
                    $pph21 = $pph21 + $pph21_tambahan;
                    $pph21_salary = $dt2->pph21_salary;
                    if ($pph21 <= 0) {
                        $pph21_balance = 0;
                    } else {
                        if ($pph21_salary <= 0)
                            $pph21_balance = $pph21;
                        else
                            $pph21_balance = $pph21 - $pph21_salary;
                    }

                    $status = '';
                    $cek = DB::table('tb_salary_tax_more')->where('id_salary_tax', $dt2->id)->where('id_salary_component', $dt->id)->count();
                    if ($cek == 0) {
                        $tambah_tax = DB::table('tb_salary_tax_more')->insert([
                            'periode' => $periode,
                            'component_name' => 'kompensasi',
                            'amount' => $dt->Kompensasi,
                            'id_salary_tax' => $dt2->id,
                            'id_salary_component' => $dt->id,
                            'penghasilan_bruto' => $dt->Kompensasi,
                            'amount_bruto' => $dt->Kompensasi,
                            'biaya_jabatan' => 0,
                            'penghasilan_netto' => $dt->Kompensasi,
                            'status_pajak' => $status_pajak,
                            'ptkp' => 0,
                            'pkp' => $dt->Kompensasi,
                            'pph21_amount' => $pph21_fix,
                            'pph21_balance' => $pph21_balance,
                            'admin' => $admin,
                            'created_at' => $now,
                            'updated_at' => $now
                        ]);
                        if ($tambah_tax)
                            $status = 'Success';
                    } else {
                        $edit_tax = DB::table('tb_salary_tax_more')->where('id_salary_tax', $dt2->id)->where('id_salary_component', $dt->id)->update([
                            'periode' => $periode,
                            'component_name' => 'kompensasi',
                            'amount' => $dt->Kompensasi,
                            'id_salary_tax' => 0,
                            'id_salary_component' => 0,
                            'penghasilan_bruto' => $dt->Kompensasi,
                            'amount_bruto' => $dt->Kompensasi,
                            'biaya_jabatan' => 0,
                            'penghasilan_netto' => $dt->Kompensasi,
                            'status_pajak' => $status_pajak,
                            'ptkp' => 0,
                            'pkp' => $dt->Kompensasi,
                            'pph21_amount' => $pph21_fix,
                            'pph21_balance' => $pph21_balance,
                            'admin' => $admin,
                            'updated_at' => $now
                        ]);
                        if ($edit_tax)
                            $status = 'Success';
                    }

                    $kompensasi_diterima = $dt->Kompensasi - $pph21_fix;
                    if ($status == 'Success') {
                        $update_kompensasi = DB::table('tb_kompensasi')->where('id', $dt->id)->update([
                            'PPh21' => $pph21_fix,
                            'Total_Kompensasi' => $kompensasi_diterima,
                            'pph21_calculate' => '1',
                        ]);
                    }
                }
            } else {
                $tb_salary_tax = DB::table('tb_salary_tax')->where('id_employee', $dt->id_employee)->orderby('periode', 'desc')->limit(1)->get();
                foreach ($tb_salary_tax as $dt2) {
                    //Get componen jabatan
                    $tb_salary_component = $this->tb_salary_component();
                    foreach ($tb_salary_component as $dt3) {
                        if ($dt3->component == 'biaya_jabatan') {
                            $biaya_jabatan_p = $dt3->presentase;
                            $biaya_jabatan_l = $dt3->amount;
                        }
                    }
                    //end Get

                    $amount_bruto = $dt2->penghasilan_bruto + $dt->Kompensasi;
                    $biaya_jabatan = $biaya_jabatan_p * $amount_bruto / 100;
                    if ($biaya_jabatan > $biaya_jabatan_l)
                        $biaya_jabatan = $biaya_jabatan_l;
                    $penghasilan_netto = $amount_bruto - $biaya_jabatan;
                    $pkp = $penghasilan_netto - $dt2->ptkp;
                    //tax calculation
                    $tb_pph21 = $this->tb_pph21();
                    foreach ($tb_pph21 as $dt3) {
                        if ($dt3->id == 1) {
                            $batas1 = $dt3->batas_atas;
                            $pajak1 = $dt3->prosentase;
                        }
                        if ($dt3->id == 2) {
                            $batas2 = $dt3->batas_atas;
                            $pajak2 = $dt3->prosentase;
                        }
                        if ($dt3->id == 3) {
                            $batas3 = $dt3->batas_atas;
                            $pajak3 = $dt3->prosentase;
                        }
                        if ($dt3->id == 4) {
                            $pajak4 = $dt3->prosentase;
                        }
                    }
                    if ($pkp <= $batas1) {
                        $pph21 = $pkp * $pajak1 / 100;
                    } else if ($pkp > $batas1 && $pkp <= $batas2) {
                        $angka1 = $batas1 * $pajak1 / 100;
                        $angka2 = ($pkp - $batas1) * $pajak2 / 100;
                        $pph21 = $angka1 + $angka2;
                    } else if ($pkp > $batas2 && $pkp <= $batas3) {
                        $angka1 = $batas1 * $pajak1 / 100;
                        $angka2 = ($batas2 - $batas1) * $pajak2 / 100;
                        $angka3 = ($pkp - $batas2) * $pajak3 / 100;
                        $pph21 = $angka1 + $angka2 + $angka3;
                    } else if ($pkp > $batas3) {
                        $angka1 = $batas1 * $pajak1 / 100;
                        $angka2 = ($batas2 - $batas1) * $pajak2 / 100;
                        $angka3 = ($batas3 - $batas2) * $pajak3 / 100;
                        $angka4 = ($pkp - $batas3) * $pajak4 / 100;
                        $pph21 = $angka1 + $angka2 + $angka3 + $angka4;
                    }
                    $sisa_bulan = $this->sisa_bulan($periode);
                    $pph21 = $pph21 / 12;


                    if ($status_npwp == 0)
                        $pph21_tambahan = $pph21 * 20 / 100;
                    else
                        $pph21_tambahan = 0;
                    $pph21 = $pph21 + $pph21_tambahan;
                    if ($pph21 > 0)
                        $pph21_fix = $pph21;
                    else
                        $pph21_fix = 0;

                    if ($status_npwp == 0)
                        $pph21_tambahan = $pph21 * 20 / 100;
                    else
                        $pph21_tambahan = 0;
                    $pph21 = $pph21 + $pph21_tambahan;
                    $pph21_salary = $dt2->pph21_salary;
                    if ($pph21 <= 0) {
                        $pph21_balance = 0;
                    } else {
                        if ($pph21_salary <= 0)
                            $pph21_balance = $pph21;
                        else
                            $pph21_balance = $pph21 - $pph21_salary;
                    }
                    //End calculation

                    $status = '';
                    $cek = DB::table('tb_salary_tax_more')->where('id_salary_tax', $dt2->id)->where('id_salary_component', $dt->id)->count();
                    if ($cek == 0) {
                        $tambah_tax = DB::table('tb_salary_tax_more')->insert([
                            'periode' => $periode,
                            'component_name' => 'kompensasi',
                            'amount' => $dt->Kompensasi,
                            'id_salary_tax' => $dt2->id,
                            'id_salary_component' => $dt->id,
                            'penghasilan_bruto' => $dt2->penghasilan_bruto,
                            'amount_bruto' => $amount_bruto,
                            'biaya_jabatan' => $biaya_jabatan,
                            'penghasilan_netto' => $penghasilan_netto,
                            'status_pajak' => $status_pajak,
                            'ptkp' => $dt2->ptkp,
                            'pkp' => $pkp,
                            'pph21_amount' => $pph21_fix,
                            'pph21_balance' => $pph21_balance,
                            'admin' => $admin,
                            'created_at' => $now,
                            'updated_at' => $now
                        ]);
                        if ($tambah_tax)
                            $status = 'Success';
                    } else {
                        $edit_tax = DB::table('tb_salary_tax_more')->where('id_salary_tax', $dt2->id)->where('id_salary_component', $dt->id)->update([
                            'periode' => $periode,
                            'component_name' => 'kompensasi',
                            'amount' => $dt->Kompensasi,
                            'penghasilan_bruto' => $dt2->penghasilan_bruto,
                            'amount_bruto' => $amount_bruto,
                            'biaya_jabatan' => $biaya_jabatan,
                            'penghasilan_netto' => $penghasilan_netto,
                            'status_pajak' => $status_pajak,
                            'ptkp' => $dt2->ptkp,
                            'pkp' => $pkp,
                            'pph21_amount' => $pph21_fix,
                            'pph21_balance' => $pph21_balance,
                            'admin' => $admin,
                            'updated_at' => $now
                        ]);
                        if ($edit_tax)
                            $status = 'Success';
                    }

                    $kompensasi_diterima = $dt->Kompensasi - $pph21_fix;
                    if ($status == 'Success') {
                        $update_kompensasi = DB::table('tb_kompensasi')->where('id', $dt->id)->update([
                            'PPh21' => $pph21_fix,
                            'Total_Kompensasi' => $kompensasi_diterima,
                            'pph21_calculate' => '1',
                        ]);
                    }

                }
            }

        }
        //end update

        $tb_kompensasi = DB::table('tb_kompensasi')
            ->leftjoin('tb_employees', 'tb_employees.id', '=', 'tb_kompensasi.id_employee')
            ->leftjoin('tb_positions', 'tb_positions.id', '=', 'tb_employees.position_id');

        if (request()->user()->hasRole('root')) {
            $tb_kompensasi = $tb_kompensasi->where('tb_employees.position_id', '<>', '19');
        } elseif (request()->user()->hasRole('payroll')) {
            $tb_kompensasi = $tb_kompensasi->where('tb_employees.position_id', '<>', '19')->where('tb_positions.position_index', '<=', '4');
        } elseif (request()->user()->hasRole('payroll_management')) {
            $tb_kompensasi = $tb_kompensasi->where('tb_employees.position_id', '<>', '19')->where('tb_positions.position_index', '>', '4');
        }

        $tb_kompensasi = $tb_kompensasi->where('pay_date', 'like', $filter)->where('pph21_calculate', '1')->where('isDelete', '0')->orderby('pay_date')
            ->get('tb_kompensasi.*');
        if (request()->user()->hasRole('root') || request()->user()->hasRole('payroll') || request()->user()->hasRole('payroll_management')) {
            return view('page/payroll/salary_compensation', ['tb_kompensasi' => $tb_kompensasi, 'tb_department' => '0', 'periode' => $periode, 'department' => '0', 'tipe' => 'SAI', 'site' => $this->site, 'status' => '1', 'menu' => 'payroll', 'juduls' => 'Compensation']);
        } else {
            return abort(403, 'Anda tidak punya akses');
        }
    }
    function compensation($periode)
    {
        if ($periode == 0) {
            $periode = date('Y-m');
        }
        //$this->compensationupdate($periode);
        $admin = Auth::user()->name;

        $end = date('Y-m-d', strtotime($periode . '-24'));
        $awal_bulan = date('Y-m-d', strtotime($periode . '-01'));
        $akhir_bulan = date('Y-m', strtotime('-1 days', strtotime($awal_bulan)));
        $start = date('Y-m-d', strtotime($akhir_bulan . '-25'));

        // $data=$this->ter_calculation('tb_kompensasi','kompensasi',$periode);
        $data = $this->ppha21_pesangon($periode);

        $tb_kompensasi = DB::table('tb_kompensasi')->where('Finish_Kontrak', '>=', $start)->where('Finish_Kontrak', '<=', $end)->where('isDelete', '0')->get('tb_kompensasi.*');
        if (request()->user()->hasRole('root') || request()->user()->hasRole('payroll') || request()->user()->hasRole('payroll_management')) {
            return view('page/payroll/salary_compensation', ['tb_kompensasi' => $tb_kompensasi, 'tb_department' => '0', 'periode' => $periode, 'department' => '0', 'tipe' => 'SAI', 'site' => $this->site, 'status' => '1', 'menu' => 'payroll_other', 'juduls' => 'Compensation']);
        } else {
            return abort(403, 'Anda tidak punya akses');
        }
    }
    function compensationExcel($periode)
    {
        if ($periode == 0) {
            $periode = date('Y-m');
        }
        //$this->compensationupdate($periode);
        $admin = Auth::user()->name;

        $end = date('Y-m-d', strtotime($periode . '-24'));
        $awal_bulan = date('Y-m-d', strtotime($periode . '-01'));
        $akhir_bulan = date('Y-m', strtotime('-1 days', strtotime($awal_bulan)));
        $start = date('Y-m-d', strtotime($akhir_bulan . '-25'));

        // $data=$this->ter_calculation('tb_kompensasi','kompensasi',$periode);
        $data = $this->ppha21_pesangon($periode);

        $tb_kompensasi = DB::table('tb_kompensasi')
            ->leftjoin('tb_employees', 'tb_employees.id', '=', 'tb_kompensasi.id_employee')
            ->leftjoin('tb_employee_detail', 'tb_employee_detail.id_employee', '=', 'tb_employees.id')
            ->where('Finish_Kontrak', '>=', $start)->where('Finish_Kontrak', '<=', $end)->where('isDelete', '0')
            ->orderby('tb_kompensasi.Bagian', 'asc')->orderby('tb_employees.cc_code', 'asc')->orderby('tb_kompensasi.Nama', 'asc')
            ->get(['tb_kompensasi.*', 'tb_employees.cc_code', 'tb_employee_detail.nomor_rekening']);
        if (request()->user()->hasRole('root') || request()->user()->hasRole('payroll') || request()->user()->hasRole('payroll_management')) {
            return view('page/payroll/salary_compensation_excel', ['tb_kompensasi' => $tb_kompensasi, 'tb_department' => '0', 'periode' => $periode, 'department' => '0', 'tipe' => 'SAI', 'site' => $this->site, 'status' => '1', 'menu' => 'payroll_other', 'juduls' => 'Compensation']);
        } else {
            return abort(403, 'Anda tidak punya akses');
        }
    }
    function compensationDelete($id)
    {
        $admin = Auth::user()->name;
        $now = date('Y-m-d H:i:s');
        $delete = DB::table('tb_kompensasi')
            ->where('id', $id)
            ->update([
                'isDelete' => '1',
                //'admin' => $admin, // update the admin if it exists
                //'updated_at' => $now
            ]);
        return redirect()->back();
    }
    function compensationSlip($id)
    {

        $tb_slip = DB::table('tb_kompensasi')->where('id', $id)->get();

        $FileName = 'SLIP_KOMPENSASI.PDF';
        $pdf = PDF::loadview('page/payroll/salary_slip_kompensasi', ['tb_slip' => $tb_slip])->setPaper(array(0, 0, 280, 595));
        return $pdf->stream($FileName);
    }
    function additionalTax($periode, $department, $component, $tipe)
    {
        if ($component == 'Compensation')
            $componen = 'kompensasi';
        else
            $componen = strtolower($component);
        $tb_pph21_status = $this->tb_pph21_status();
        $tb_pph21 = $this->tb_pph21();
        $tb_salary_component = $this->tb_salary_component();
        foreach ($tb_salary_component as $dt) {
            if ($dt->component == 'biaya_jabatan') {
                $biaya_jabatan_p = $dt->presentase;
                $biaya_jabatan_l = $dt->amount;
            }
        }
        $tb_salary_tax_more = DB::table('tb_salary_tax_more')
            ->leftjoin('tb_salary_tax', 'tb_salary_tax.id', '=', 'tb_salary_tax_more.id_salary_tax')
            ->leftjoin('tb_salary_contract', 'tb_salary_contract.id_employee', '=', 'tb_salary_tax.id_employee')
            ->leftjoin('tb_pph21_status', 'tb_pph21_status.id', '=', 'tb_salary_contract.status_pajak')
            ->where('tb_salary_tax_more.periode', $periode)
            ->where('component_name', $componen);
        if (request()->user()->hasRole('root')) {
            $tb_salary_tax_more = $tb_salary_tax_more->where('tb_salary_contract.status', '1');
        } elseif (request()->user()->hasRole('payroll')) {
            $tb_salary_tax_more = $tb_salary_tax_more->where('tb_salary_contract.status', '1')->where('tb_salary_contract.status_management', '0');
        } elseif (request()->user()->hasRole('payroll_management')) {
            $tb_salary_tax_more = $tb_salary_tax_more->where('tb_salary_contract.status', '1')->where('tb_salary_contract.status_management', '1');
        }
        if ($department != '0')
            $tb_salary_tax_more = $tb_salary_tax_more->where('tb_salary_contract.department', $department);
        //if($tipe!='0')$tb_salary_tax_more=$tb_salary_tax_more->where('tb_salary_contract.tipe_kontrak',$tipe);
        if ($tipe != 0 && ($tipe == 'SAI' || $tipe == 'TKA')) {
            $tb_salary_tax_more = $tb_salary_tax_more->where(function ($query) use ($tipe) {
                $query->where('tb_salary_contract.tipe_kontrak', 'SAI')
                    ->orWhere('tb_salary_contract.tipe_kontrak', 'TKA');
            });
        } else {
            $tb_salary_tax_more = $tb_salary_tax_more->where('tb_salary_contract.tipe_kontrak', $tipe);
        }
        $tb_salary_tax_more = $tb_salary_tax_more->get(['tb_salary_tax_more.*', 'tb_salary_contract.NIK', 'tb_salary_contract.nama_karyawan', 'tb_salary_contract.department as dept_code', 'tb_pph21_status.status_pajak as statuspajak']);
        //return $tb_salary_tax_more;
        if ($componen == 'kompensasi') {
            $department = '0';
            $tb_department = '';
        } else {
            $tb_department = $this->tb_department_periode($periode, 'tb_salary_earn_additional', $tipe);
        }
        $juduls = 'Tax Calculation (' . $component . ')';
        if (request()->user()->hasRole('root') || request()->user()->hasRole('payroll') || request()->user()->hasRole('payroll_management')) {
            return view('page/payroll/salary_tax_more', ['tb_salary_tax_more' => $tb_salary_tax_more, 'tb_department' => $tb_department, 'periode' => $periode, 'department' => $department, 'biaya_jabatan_p' => $biaya_jabatan_p, 'biaya_jabatan_l' => $biaya_jabatan_l, 'tb_pph21_status' => $tb_pph21_status, 'tb_pph21' => $tb_pph21, 'component' => $componen, 'tipe' => $tipe, 'site' => $this->site, 'status' => '1', 'menu' => 'payroll', 'juduls' => $juduls]);
        } else {
            return abort(403, 'Anda tidak punya akses');
        }
    }
    function additionalTaxTer($periode, $department, $component, $tipe)
    {
        if ($component == 'Compensation')
            $componen = 'kompensasi';
        else
            $componen = strtolower($component);
        $tb_pph21_status = $this->tb_pph21_status();
        $tb_pph21 = $this->tb_pph21();
        $tb_salary_component = $this->tb_salary_component();
        foreach ($tb_salary_component as $dt) {
            if ($dt->component == 'biaya_jabatan') {
                $biaya_jabatan_p = $dt->presentase;
                $biaya_jabatan_l = $dt->amount;
            }
        }
        $tb_salary_tax_more = DB::table('tb_salary_tax_more')
            ->leftjoin('tb_salary_tax', 'tb_salary_tax.id', '=', 'tb_salary_tax_more.id_salary_tax')
            ->leftjoin('tb_salary_contract', 'tb_salary_contract.id_employee', '=', 'tb_salary_tax.id_employee')
            ->leftjoin('tb_pph21_status', 'tb_pph21_status.id', '=', 'tb_salary_contract.status_pajak')
            ->where('tb_salary_tax_more.periode', $periode)
            ->where('component_name', $componen);
        if (request()->user()->hasRole('root')) {
            $tb_salary_tax_more = $tb_salary_tax_more->where('tb_salary_contract.status', '1');
        } elseif (request()->user()->hasRole('payroll')) {
            $tb_salary_tax_more = $tb_salary_tax_more->where('tb_salary_contract.status', '1')->where('tb_salary_contract.status_management', '0');
        } elseif (request()->user()->hasRole('payroll_management')) {
            $tb_salary_tax_more = $tb_salary_tax_more->where('tb_salary_contract.status', '1')->where('tb_salary_contract.status_management', '1');
        }
        if ($department != '0')
            $tb_salary_tax_more = $tb_salary_tax_more->where('tb_salary_contract.department', $department);
        // if($tipe!='0')$tb_salary_tax_more=$tb_salary_tax_more->where('tb_salary_contract.tipe_kontrak',$tipe);
        if ($tipe != 0 && ($tipe == 'SAI' || $tipe == 'TKA')) {
            $tb_salary_tax_more = $tb_salary_tax_more->where(function ($query) use ($tipe) {
                $query->where('tb_salary_contract.tipe_kontrak', 'SAI')
                    ->orWhere('tb_salary_contract.tipe_kontrak', 'TKA');
            });
        } else {
            $tb_salary_tax_more = $tb_salary_tax_more->where('tb_salary_contract.tipe_kontrak', $tipe);
        }
        $tb_salary_tax_more = $tb_salary_tax_more->get(['tb_salary_tax_more.*', 'tb_salary_contract.NIK', 'tb_salary_contract.nama_karyawan', 'tb_salary_contract.department as dept_code', 'tb_pph21_status.status_pajak as statuspajak']);
        //return $tb_salary_tax_more;
        if ($componen == 'kompensasi') {
            $department = '0';
            $tb_department = '';
        } else {
            $tb_department = $this->tb_department_periode($periode, 'tb_salary_earn_additional', $tipe);
        }
        $juduls = 'Tax Calculation (' . $component . ')';
        if (request()->user()->hasRole('root') || request()->user()->hasRole('payroll') || request()->user()->hasRole('payroll_management')) {
            return view('page/payroll/salary_tax_more', ['tb_salary_tax_more' => $tb_salary_tax_more, 'tb_department' => $tb_department, 'periode' => $periode, 'department' => $department, 'biaya_jabatan_p' => $biaya_jabatan_p, 'biaya_jabatan_l' => $biaya_jabatan_l, 'tb_pph21_status' => $tb_pph21_status, 'tb_pph21' => $tb_pph21, 'component' => $componen, 'tipe' => $tipe, 'site' => $this->site, 'status' => '1', 'menu' => 'payroll', 'juduls' => $juduls]);
        } else {
            return abort(403, 'Anda tidak punya akses');
        }
    }

    function additionalEarn($periode, $department, $component, $tipe)
    {
        $admin = Auth::user()->name;
        $today = date('Y-m-d');
        $now = date('Y-m-d H:i:s');
        $componen = strtolower($component);

        //update pph21
        //$filter='%'.$periode.'%';
        //$tb_kompensasi=DB::table('tb_kompensasi')->where('pay_date','like',$filter)->where('isDelete','0')->orderby('pay_date')->get();
        $tb_salary_earn_additional = $this->tb_earn_additional($periode, $department, $componen, $tipe);
        foreach ($tb_salary_earn_additional as $dt) {
            $tb_salary_tax = DB::table('tb_salary_tax')->where('id_employee', $dt->id_employee)->orderby('periode', 'desc')->limit(1)->get();
            foreach ($tb_salary_tax as $dt2) {
                //Get componen jabatan
                $tb_salary_component = $this->tb_salary_component();
                foreach ($tb_salary_component as $dt3) {
                    if ($dt3->component == 'biaya_jabatan') {
                        $biaya_jabatan_p = $dt3->presentase;
                        $biaya_jabatan_l = $dt3->amount;
                    }
                }
                //end Get
                $sisa_bulan = $this->sisa_bulan($periode);
                $amount_bruto = $dt2->penghasilan_bruto + ($dt->earn_amount * $sisa_bulan);
                $biaya_jabatan = $biaya_jabatan_p * $amount_bruto / 100;
                if ($biaya_jabatan > $biaya_jabatan_l)
                    $biaya_jabatan = $biaya_jabatan_l;
                $penghasilan_netto = $amount_bruto - $biaya_jabatan;
                $pkp = $penghasilan_netto - $dt2->ptkp;
                //tax calculation
                $tb_pph21 = $this->tb_pph21();
                foreach ($tb_pph21 as $dt3) {
                    if ($dt3->id == 1) {
                        $batas1 = $dt3->batas_atas;
                        $pajak1 = $dt3->prosentase;
                    }
                    if ($dt3->id == 2) {
                        $batas2 = $dt3->batas_atas;
                        $pajak2 = $dt3->prosentase;
                    }
                    if ($dt3->id == 3) {
                        $batas3 = $dt3->batas_atas;
                        $pajak3 = $dt3->prosentase;
                    }
                    if ($dt3->id == 4) {
                        $pajak4 = $dt3->prosentase;
                    }
                }
                if ($pkp <= $batas1) {
                    $pph21 = $pkp * $pajak1 / 100;
                } else if ($pkp > $batas1 && $pkp <= $batas2) {
                    $angka1 = $batas1 * $pajak1 / 100;
                    $angka2 = ($pkp - $batas1) * $pajak2 / 100;
                    $pph21 = $angka1 + $angka2;

                } else if ($pkp > $batas2 && $pkp <= $batas3) {
                    $angka1 = $batas1 * $pajak1 / 100;
                    $angka2 = ($batas2 - $batas1) * $pajak2 / 100;
                    $angka3 = ($pkp - $batas2) * $pajak3 / 100;
                    $pph21 = $angka1 + $angka2 + $angka3;
                } else if ($pkp > $batas3) {
                    $angka1 = $batas1 * $pajak1 / 100;
                    $angka2 = ($batas2 - $batas1) * $pajak2 / 100;
                    $angka3 = ($batas3 - $batas2) * $pajak3 / 100;
                    $angka4 = ($pkp - $batas3) * $pajak4 / 100;
                    $pph21 = $angka1 + $angka2 + $angka3 + $angka4;
                }
                $sisa_bulan = $this->sisa_bulan($periode);
                $pph21 = $pph21 / 12;

                $tb_salary_contract_employee = $this->tb_salary_contract_employee($dt->id_employee, $tipe);
                foreach ($tb_salary_contract_employee as $dt4) {
                    $status_npwp = $dt4->status_npwp;
                    $status_pajak = $dt4->status_pajak;
                }

                if ($status_npwp == 0)
                    $pph21_tambahan = $pph21 * 20 / 100;
                else
                    $pph21_tambahan = 0;
                $pph21 = $pph21 + $pph21_tambahan;
                $pph21_salary = $dt2->pph21_salary;
                if ($pph21 <= 0) {
                    $pph21_balance = 0;
                } else {
                    if ($pph21_salary <= 0)
                        $pph21_balance = $pph21;
                    else
                        $pph21_balance = $pph21 - $pph21_salary;
                }
                //$pph21_balance=$pph21-$pph21_salary;
                //End calculation

                $status = '';
                $cek = DB::table('tb_salary_tax_more')->where('id_salary_tax', $dt2->id)->where('id_salary_component', $dt->id)->count();
                if ($cek == 0) {
                    $tambah_tax = DB::table('tb_salary_tax_more')->insert([
                        'periode' => $periode,
                        'component_name' => $componen,
                        'amount' => $dt->earn_amount,
                        'id_salary_tax' => $dt2->id,
                        'id_salary_component' => $dt->id,
                        'pph21_salary' => $pph21_salary,
                        'penghasilan_bruto' => $dt2->penghasilan_bruto,
                        'amount_bruto' => $amount_bruto,
                        'biaya_jabatan' => $biaya_jabatan,
                        'penghasilan_netto' => $penghasilan_netto,
                        'status_pajak' => $status_pajak,
                        'ptkp' => $dt2->ptkp,
                        'pkp' => $pkp,
                        'pph21_amount' => $pph21,
                        'pph21_balance' => $pph21_balance,
                        'admin' => $admin,
                        'created_at' => $now,
                        'updated_at' => $now
                    ]);
                    if ($tambah_tax)
                        $status = 'Success';
                } else {
                    $edit_tax = DB::table('tb_salary_tax_more')->where('id_salary_tax', $dt2->id)->where('id_salary_component', $dt->id)->update([
                        'periode' => $periode,
                        'component_name' => $componen,
                        'amount' => $dt->earn_amount,
                        'pph21_salary' => $pph21_salary,
                        'penghasilan_bruto' => $dt2->penghasilan_bruto,
                        'amount_bruto' => $amount_bruto,
                        'biaya_jabatan' => $biaya_jabatan,
                        'penghasilan_netto' => $penghasilan_netto,
                        'status_pajak' => $status_pajak,
                        'ptkp' => $dt2->ptkp,
                        'pkp' => $pkp,
                        'pph21_amount' => $pph21,
                        'pph21_balance' => $pph21_balance,
                        'admin' => $admin,
                        'updated_at' => $now
                    ]);
                    if ($edit_tax)
                        $status = 'Success';
                }
                if ($pph21_balance < 0)
                    $pph21_fix = 0;
                else
                    $pph21_fix = $pph21_balance;
                $kompensasi_diterima = $dt->earn_amount - $pph21_fix;
                if ($status == 'Success') {
                    $update_kompensasi = DB::table('tb_salary_earn_additional')->where('id', $dt->id)->update([
                        'pph21' => $pph21_fix,
                        'pph21_calculate' => '1',
                        'amount_received' => $kompensasi_diterima,
                    ]);
                }

            }

        }
        //end update
        //return $tb_salary_earn_additional;

        $tb_employee = $this->tb_employee_contract();
        $tb_department = $this->tb_department_periode($periode, 'tb_salary_earn_additional', $tipe);
        $tb_salary_earn_additional = $this->tb_earn_additional($periode, $department, $componen, $tipe);
        $juduls = 'Salary Earn (' . strtoupper($component) . ')';
        if (request()->user()->hasRole('root') || request()->user()->hasRole('payroll') || request()->user()->hasRole('payroll_management')) {
            return view('page/payroll/salary_earn_additional', ['tb_salary_earn_additional' => $tb_salary_earn_additional, 'tb_employee' => $tb_employee, 'tb_department' => $tb_department, 'periode' => $periode, 'department' => $department, 'component' => $componen, 'tipe' => $tipe, 'today' => $today, 'site' => $this->site, 'status' => '1', 'menu' => 'payroll', 'juduls' => $juduls]);
        } else {
            return abort(403, 'Anda tidak punya akses');
        }
    }
    function additionalEarnTer($periode, $department, $component, $tipe)
    {
        $admin = Auth::user()->name;
        $today = date('Y-m-d');
        $now = date('Y-m-d H:i:s');
        $componen = strtolower($component);

        //update pph21
        $tb_salary_earn_additional = $this->tb_earn_additional($periode, $department, $componen, $tipe);
        foreach ($tb_salary_earn_additional as $dt) {
            $tb_salary_contract_employee = $this->tb_salary_contract_employee($dt->id_employee, $tipe);
            foreach ($tb_salary_contract_employee as $dt2) {
                $category = $dt2->kategori;
                $status_pajak = $dt2->status_pajak;
            }
            //calculation pph21 with ter
            $tax_payroll = $this->qty_salary_tax_ter($dt->periode, $dt->id_employee, 'tb_salary_summary', 'payroll');
            if ($tax_payroll == 0) {
                $tb_salary_contract_employee = $this->tb_salary_contract_employee($dt->id_employee, $tipe);
                foreach ($tb_salary_contract_employee as $dt_salary) {
                    $monthly_salary = $dt_salary->total_salary;
                }
            } else
                $monthly_salary = '0';
            $penghasilan_bruto_pajak = $dt->earn_amount;
            $cum = $this->cum_salary_tax_ter($periode, $dt->id_employee, 'tb_salary_earn_additional', $componen);
            $prev = $cum - $penghasilan_bruto_pajak;
            $cum_tax = $cum + $penghasilan_bruto_pajak + $monthly_salary;
            $tb_pph21_ter = $this->tb_pph21_ter($category, $cum_tax);
            foreach ($tb_pph21_ter as $dt3) {
                $ter_id = $dt3->id;
                $ter = $dt3->prosentase;
                $pph21_ter = ($ter * $cum_tax / 100);
            }

            $fiscal_year = date('Y', strtotime($periode . '-01'));
            $periksa_tax = $this->qty_salary_tax_ter($periode, $dt->id_employee, 'tb_salary_earn_additional', $componen);
            $prev_tax = $this->prev_salary_tax_ter($periode, $dt->id_employee, 'tb_salary_earn_additional', $componen);
            $balance_amount = $pph21_ter - $prev_tax;
            //end tax calculation
            if ($periksa_tax == 0) {
                $lock = DB::table('tb_salary_ter_monthly')->where('periode', $dt->periode)->where('id_employee', $dt->id_employee)->update(['locked' => '1']);
                $update_tax = DB::table('tb_salary_ter_monthly')->insert([
                    'id_employee' => $dt->id_employee,
                    'fiscal_year' => $fiscal_year,
                    'periode' => $periode,
                    'related_table' => 'tb_salary_earn_additional',
                    'category' => $componen,
                    'previous_payment' => $prev_tax,
                    'monthly_salary' => $monthly_salary,
                    'prev_amount' => $prev,
                    'current_amount' => $penghasilan_bruto_pajak,
                    'cumulative_amount' => $cum_tax,
                    'status_pajak' => $status_pajak,
                    'ter_id' => $ter_id,
                    'prosentase' => $ter,
                    'tax_amount' => $pph21_ter,
                    'balance_amount' => $balance_amount,
                    'admin' => $admin,
                    'created_at' => $now,
                    'updated_at' => $now,
                    'tipe_kontrak' => $tipe
                ]);
            } else {
                $update_tax = DB::table('tb_salary_ter_monthly')
                    ->where('periode', $dt->periode)
                    ->where('id_employee', $dt->id_employee)
                    ->where('related_table', 'tb_salary_earn_additional')
                    ->where('category', $componen)
                    ->where('locked', '0')
                    ->update([
                        'id_employee' => $dt->id_employee,
                        'fiscal_year' => $fiscal_year,
                        'periode' => $periode,
                        'related_table' => 'tb_salary_earn_additional',
                        'category' => $componen,
                        'previous_payment' => $prev_tax,
                        'monthly_salary' => $monthly_salary,
                        'prev_amount' => $prev,
                        'current_amount' => $penghasilan_bruto_pajak,
                        'cumulative_amount' => $cum_tax,
                        'status_pajak' => $status_pajak,
                        'ter_id' => $ter_id,
                        'prosentase' => $ter,
                        'tax_amount' => $pph21_ter,
                        'balance_amount' => $balance_amount,
                        'admin' => $admin,
                        'updated_at' => $now
                    ]);
            }
            if ($update_tax)
                $update = DB::table('tb_salary_earn_additional')->where('id', $dt->id)->update(['pph21' => $balance_amount, 'pph21_calculate' => $prev_tax]);

        }
        //end update

        $tb_employee = $this->tb_employee_contract();
        $tb_department = $this->tb_department_periode($periode, 'tb_salary_earn_additional', $tipe);
        $tb_salary_earn_additional = $this->tb_earn_additional($periode, $department, $componen, $tipe);
        $juduls = 'Salary Earn (' . strtoupper($component) . ')';
        if (request()->user()->hasRole('root') || request()->user()->hasRole('payroll') || request()->user()->hasRole('payroll_management')) {
            return view('page/payroll/salary_earn_additional', ['tb_salary_earn_additional' => $tb_salary_earn_additional, 'tb_employee' => $tb_employee, 'tb_department' => $tb_department, 'periode' => $periode, 'department' => $department, 'component' => $componen, 'tipe' => $tipe, 'today' => $today, 'site' => $this->site, 'status' => '1', 'menu' => 'payroll_calculation', 'juduls' => $juduls]);
        } else {
            return abort(403, 'Anda tidak punya akses');
        }
    }
    function additionalEarnSimpan(Request $data)
    {
        $admin = Auth::user()->name;
        $today = date('Y-m-d');
        $now = date('Y-m-d H:i:s');

        $componen = strtolower($data->componentname);
        $periode = $data->periodeearn;

        //Tax Calculation
        $tb_salary_contract_employee = $this->tb_salary_contract_employee($data->idemployee, $data->tipe_kontrak);
        foreach ($tb_salary_contract_employee as $dt2) {
            $category = $dt2->kategori;
            $status_pajak = $dt2->status_pajak;
        }
        $tax_payroll = $this->qty_salary_tax_ter($data->periodeearn, $data->idemployee, 'tb_salary_summary', 'payroll');
        if ($tax_payroll == 0) {
            $tb_salary_contract_employee = $this->tb_salary_contract_employee($data->idemployee, $data->tipe_kontrak);
            foreach ($tb_salary_contract_employee as $dt_salary) {
                $monthly_salary = $dt_salary->total_salary;
            }
        } else
            $monthly_salary = '0';
        $penghasilan_bruto_pajak = $data->amount;
        $cum = $this->cum_salary_tax_ter($periode, $data->idemployee, 'tb_salary_earn_additional', $componen);
        $cum_tax = $cum + $penghasilan_bruto_pajak + $monthly_salary;
        $tb_pph21_ter = $this->tb_pph21_ter($category, $cum_tax);
        foreach ($tb_pph21_ter as $dt3) {
            $ter_id = $dt3->id;
            $ter = $dt3->prosentase;
            $pph21_ter = ($ter * $cum_tax / 100);
        }
        $periksa_tax = $this->qty_salary_tax_ter($data->periodeearn, $data->idemployee, 'tb_salary_earn_additional', $componen);
        $prev_tax = $this->prev_salary_tax_ter($data->periodeearn, $data->idemployee, 'tb_salary_earn_additional', $componen);
        $balance_amount = $pph21_ter - $prev_tax;
        //end tax calculation


        if ($data->idcomponent > 0) {
            $simpan = DB::table('tb_salary_earn_additional')->where('id', $data->idcomponent)->update([
                'periode' => $data->periodeearn,
                'tanggal_pembayaran' => $data->tanggal_pembayaran,
                'component_name' => $data->componentname,
                'earn_amount' => $data->amount,
                'pph21' => $balance_amount,
                'amount_received' => '0',
                'description' => $data->description,
                'mail_status' => '0',
                'created_at' => $now,
                'admin' => $admin,
                'updated_at' => $now,
            ]);
            if ($simpan)
                $pesan = "Sukses";
            else
                $pesan = "Gagal Simpan";
        } else {
            $tb_salary_earn_additional = DB::table('tb_salary_earn_additional')->where('periode', $data->periode)->where('component_name', $data->componentname)->where('id_employee', $data->idemployee)->count();
            if ($tb_salary_earn_additional > 0)
                $pesan = 'Komponen untuk perode ini sudah ada';
            else {
                $update = DB::table('tb_salary_earn_additional')->insert([
                    'periode' => $data->periodeearn,
                    'tanggal_cutoff' => $data->tanggal_pembayaran,
                    'tanggal_pembayaran' => $data->tanggal_pembayaran,
                    'id_employee' => $data->idemployee,
                    'tipe_kontrak' => $data->tipe_kontrak,
                    'masa_kerja' => '0',
                    'component_name' => $data->componentname,
                    'earn_amount' => $data->amount,
                    'pph21' => $balance_amount,
                    'amount_received' => '0',
                    'description' => $data->description,
                    'mail_status' => '0',
                    'created_at' => $now,
                    'admin' => $admin,
                    'updated_at' => $now,
                ]);
                if ($update)
                    $pesan = "Sukses";
                else
                    $pesan = "Gagal Update";
            }
        }
        return $pesan;
    }
    function additionalEarnDelete($id)
    {
        $delete = DB::table('tb_salary_earn_additional')->where('id', $id)->delete();
        if ($delete) {
            $delete2 = DB::table('tb_salary_tax_more')->where('id_salary_component', $id)->delete();
            return redirect()->back()->with(['info' => 'Deleted']);
        }
    }
    function additionalEarnReset($id, $component)
    {
        $delete = DB::table('tb_salary_earn_additional')->where('periode', $id)->where('component_name', $component)->delete();
        if ($delete) {
            $delete2 = DB::table('tb_salary_tax_more')->where('periode', $id)->where('component_name', '<>', 'kompensasi')->delete();
            return redirect()->back()->with(['info' => 'Deleted']);
        }
    }
    function additionalEarnTemplate($periode, $component, $tipe)
    {
        $tb_employee = $this->tb_employee_template($tipe);
        if (request()->user()->hasRole('root') || request()->user()->hasRole('payroll')) {
            return view('page/payroll/salary_earn_template', ['tb_employee' => $tb_employee, 'periode' => $periode, 'component' => $component, 'tipe' => $tipe]);
        } else {
            return abort(403, 'Anda tidak punya akses');
        }
    }
    function ImportEarn(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:csv,xls,xlsx'
        ]);
        $file = $request->file('file');
        $nama_file = rand() . $file->getClientOriginalName();
        $file->move('laravel/public/excel', $nama_file);
        $import = Excel::import(new EarnImport, public_path('/excel/' . $nama_file));
        if ($import)
            return redirect()->back()->with(['info' => 'Import Berhasil']);
    }
    function ImportDeduction(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:csv,xls,xlsx'
        ]);
        $file = $request->file('file');
        $nama_file = rand() . $file->getClientOriginalName();
        $file->move('laravel/public/excel', $nama_file);
        $import = Excel::import(new DeductionImport, public_path('/excel/' . $nama_file));
        if ($import)
            return redirect()->back()->with(['info' => 'Import Berhasil']);
    }
    function additionalSlip($id)
    {

        $tb_slip = DB::table('tb_salary_earn_additional')
            ->leftjoin('tb_salary_contract', 'tb_salary_contract.id_employee', '=', 'tb_salary_earn_additional.id_employee')
            ->where('tb_salary_earn_additional.id', $id)->get();
        foreach ($tb_slip as $dt) {
            $component = strtoupper($dt->component_name);
            $judul = 'SLIP_' . $component . '.PDF';
            $periode_text = date('F Y', strtotime($dt->periode . '-01'));
        }
        $FileName = $judul;
        $pdf = PDF::loadview('page/payroll/salary_slip_additional', ['tb_slip' => $tb_slip, 'component' => $component, 'periode_text' => $periode_text])->setPaper(array(0, 0, 280, 595));
        return $pdf->stream($FileName);
    }
    function GenerateEarn(Request $data)
    {
        $admin = Auth::user()->name;
        $today = date('Y-m-d');
        $now = date('Y-m-d H:i:s');

        $tanggal_cutoff = $data->tgl_cutoff;
        $tanggal_pembayaran = $data->tgl_dibayar;
        $periode = date('Y-m', strtotime($tanggal_pembayaran));
        $description = 'THR ' . date('Y', strtotime($tanggal_pembayaran));
        $tb_salary_contract = $this->tb_salary_contract('1', '0', '0');
        $i = 0;
        $j = 0;
        foreach ($tb_salary_contract as $dt) {
            $i++;
            $start_contract = date('Y-m-d', strtotime('-1 days', strtotime($dt->tanggal_kontrak)));
            $finish_contract = $tanggal_cutoff;
            $datetime1 = date_create($start_contract);
            $datetime2 = date_create($finish_contract);
            $bulan = date_diff($datetime1, $datetime2);
            $lama_tahun = $bulan->format('%y');
            $lama_bulan = $bulan->format('%m');
            $lama_contract = $lama_tahun * 12 + $lama_bulan;
            if ($lama_contract > 12)
                $pengali = 1;
            else
                $pengali = $lama_contract / 12;
            $earn_amount = $dt->total_salary * $pengali;

            //Add THR
            $simpan = DB::table('tb_salary_earn_additional')->insert([
                'periode' => $periode,
                'tanggal_cutoff' => $tanggal_cutoff,
                'tanggal_pembayaran' => $tanggal_pembayaran,
                'id_employee' => $dt->id_employee,
                'tipe_kontrak' => $dt->tipe_kontrak,
                'masa_kerja' => $lama_contract,
                'component_name' => 'thr',
                'earn_amount' => $earn_amount,
                'pph21' => '0',
                'amount_received' => '0',
                'description' => $description,
                'mail_status' => '0',
                'created_at' => $now,
                'admin' => $admin,
                'updated_at' => $now,
            ]);
            if ($simpan)
                $j++;
            //End Add THR

        }
        if ($i == $j)
            return redirect()->back();
    }

    function additionalRapel($periode, $department, $tipe)
    {
        $admin = Auth::user()->name;
        $today = date('Y-m-d');
        $now = date('Y-m-d H:i:s');

        $tb_employee = $this->tb_employee_contract();
        $tb_department = $this->tb_department_periode($periode, 'tb_salary_earn_additional', $tipe);
        $tb_salary_earn_special = $this->tb_earn_special($periode, $department, $tipe);
        $juduls = 'Rapel Khusus';
        if (request()->user()->hasRole('root') || request()->user()->hasRole('payroll') || request()->user()->hasRole('payroll_management')) {
            return view('page/payroll/salary_earn_special', ['tb_salary_earn_special' => $tb_salary_earn_special, 'tb_employee' => $tb_employee, 'tb_department' => $tb_department, 'periode' => $periode, 'department' => $department, 'tipe' => $tipe, 'today' => $today, 'site' => $this->site, 'status' => '1', 'menu' => 'payroll', 'juduls' => $juduls]);
        } else {
            return abort(403, 'Anda tidak punya akses');
        }
    }
    function specialRapelTemplate($periode, $tipe)
    {
        $reset = DB::table('tb_salary_earn_special')->where('periode', $periode)->where('tipe_kontrak', $tipe)->delete();
        $tb_employee = $this->tb_employee_template($tipe);
        if (request()->user()->hasRole('root') || request()->user()->hasRole('payroll')) {
            return view('page/payroll/salary_deduction_template', ['tb_employee' => $tb_employee, 'periode' => $periode, 'tipe' => $tipe]);
        } else {
            return abort(403, 'Anda tidak punya akses');
        }
    }
    function deductionTemplate($periode, $tipe)
    {
        $reset = DB::table('tb_salary_deduction')->where('periode', $periode)->where('tipe_kontrak', $tipe)->delete();
        $tb_employee = $this->tb_employee_template($tipe);
        if (request()->user()->hasRole('root') || request()->user()->hasRole('payroll')) {
            return view('page/payroll/salary_deduction_template', ['tb_employee' => $tb_employee, 'periode' => $periode, 'tipe' => $tipe]);
        } else {
            return abort(403, 'Anda tidak punya akses');
        }
    }
    function ImportRapel(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:csv,xls,xlsx'
        ]);
        $file = $request->file('file');
        $nama_file = rand() . $file->getClientOriginalName();
        $file->move('laravel/public/excel', $nama_file);
        $import = Excel::import(new RapelImport, public_path('/excel/' . $nama_file));
        if ($import)
            return redirect()->back()->with(['info' => 'Import Berhasil']);
    }
    function distributeRapel($periode, $tipe)
    {
        $update = DB::table('tb_salary_earn_special')->where('periode', $periode)->where('tipe_kontrak', $tipe)->update([
            'mail_status' => '1',
        ]);
        //return $periode.'#'.$tipe;
        if ($update)
            return redirect()->back()->with(['info' => 'Success Distribute']);
    }
    function rapelSlip($id)
    {

        $tb_slip = DB::table('tb_salary_earn_special')
            ->leftjoin('tb_salary_contract', 'tb_salary_contract.id_employee', '=', 'tb_salary_earn_special.id_employee')
            ->where('tb_salary_earn_special.id', $id)->get();
        foreach ($tb_slip as $dt) {
            $judul = 'SLIP RAPEL 2022.PDF';
            $periode_text = date('F Y', strtotime($dt->periode . '-01'));
        }
        $FileName = $judul;
        $pdf = PDF::loadview('page/payroll/salary_slip_special', ['tb_slip' => $tb_slip, 'periode_text' => $periode_text])->setPaper(array(0, 0, 280, 345));
        return $pdf->stream($FileName);
    }

    //additionalRapel;
    public function tb_earn_special($periode, $department, $tipe)
    {
        $tabel = DB::table('tb_salary_earn_special')
            ->leftjoin('tb_salary_contract', 'tb_salary_contract.id_employee', '=', 'tb_salary_earn_special.id_employee')
            ->leftjoin('tb_pph21_status', 'tb_pph21_status.id', '=', 'tb_salary_contract.status_pajak');
        if (request()->user()->hasRole('root')) {
            $tabel = $tabel->where('tb_salary_contract.status', '1');
        } elseif (request()->user()->hasRole('payroll')) {
            $tabel = $tabel->where('tb_salary_contract.status', '1')->where('tb_salary_contract.status_management', '0');
        } elseif (request()->user()->hasRole('payroll_management')) {
            $tabel = $tabel->where('tb_salary_contract.status', '1')->where('tb_salary_contract.status_management', '1');
        }
        if ($periode != 0)
            $tabel = $tabel->where('periode', $periode);
        if ($tipe != 0)
            $tabel = $tabel->where('tb_salary_contract.tipe_kontrak', $tipe);
        if ($department != 0) {
            $tabel = $tabel->where('tb_salary_contract.department', $department);
        }
        $tabel = $tabel->get(['tb_salary_earn_special.*', 'tb_pph21_status.status_pajak as statuspajak', 'tb_salary_contract.NIK', 'tb_salary_contract.nama_karyawan', 'tb_salary_contract.department', 'tb_salary_contract.jabatan', 'tb_salary_contract.status_pajak', 'tb_salary_contract.total_salary', 'tb_salary_contract.tipe_kontrak']);
        return $tabel;
    }

    //index; arsifKontrak; management;
    public function tb_employee($status, $tipe)
    {
        $tb_salary_contract = $this->tb_salary_contract(1, 0, $tipe);
        $tabel = DB::table('tb_employees')
            ->leftjoin('tb_departments', 'tb_departments.id', '=', 'tb_employees.dept_id')
            ->leftjoin('tb_positions', 'tb_positions.id', '=', 'tb_employees.position_id')
            ->where('tb_employees.status', '1');
        if ($tipe == 'MAGANG')
            $tabel = $tabel->where('tb_employees.position_id', '19');
        elseif ($tipe == 'PSAB')
            $tabel = $tabel->where('tb_employees.position_id', '23');
        elseif ($tipe == 'SAI' || $tipe == 'TKA') {
            $tabel = $tabel->where('tb_employees.position_id', '<>', '19')->where('tb_employees.position_id', '<>', '23')->where('tb_employees.position_id', '<>', '37')->where('tb_positions.position_index', '<=', '4');
        } elseif ($tipe == 'MANAGEMENT') {
            $tabel = $tabel->where('tb_employees.position_id', '<>', '19')->where('tb_employees.position_id', '<>', '23')->where('tb_employees.position_id', '<>', '37')->where('tb_positions.position_index', '>=', '5');
        }
        foreach ($tb_salary_contract as $dt) {
            $tabel = $tabel->where('tb_employees.id', '<>', $dt->id_employee);
        }
        if (request()->user()->hasRole('root')) {
            $tabel = $tabel->where('tb_positions.position_index', '>=', '0');
        } elseif (request()->user()->hasRole('payroll')) {
            $tabel = $tabel->where('tb_positions.position_index', '<=', '4');
        } elseif (request()->user()->hasRole('payroll_management')) {
            $tabel = $tabel->where('tb_positions.position_index', '>=', '5');
        }
        $tabel = $tabel->orderby('tb_employees.dept_id', 'asc')
            ->orderby('tb_employees.employee_name', 'asc')
            ->get(['tb_employees.id', 'tb_employees.NIK', 'tb_employees.employee_name as nama_karyawan', 'tb_departments.dept_code', 'tb_positions.position_name']);
        return $tabel;
    }
    //index; arsifKontrak; tax;
    public function tb_pph21_status()
    {
        $tabel = DB::table('tb_pph21_status')->orderby('sequance', 'asc')->get();
        return $tabel;
    }
    //index; arsifKontrak; updateSummary; tax;
    public function tb_salary_component()
    {
        $tabel = DB::table('tb_salary_component')->where('presentase', '>', '0')->orWhere('amount', '>', '0')->get();
        return $tabel;
    }
    //index; arsifKontrak; management;
    public function tb_department_salary_contract($status)
    {
        $tabel = DB::table('tb_salary_contract')
            ->leftjoin('tb_pph21_status', 'tb_pph21_status.id', '=', 'tb_salary_contract.status_pajak')
            ->select('tb_salary_contract.department')
            ->groupby('tb_salary_contract.department');
        if (request()->user()->hasRole('root')) {
            $tabel = $tabel->where('tb_salary_contract.status', $status);
        } elseif (request()->user()->hasRole('payroll')) {
            $tabel = $tabel->where('tb_salary_contract.status', $status)->where('tb_salary_contract.status_management', '0');
        } elseif (request()->user()->hasRole('payroll_management')) {
            $tabel = $tabel->where('tb_salary_contract.status', $status)->where('tb_salary_contract.status_management', '1');
        }
        $tabel = $tabel->get();
        return $tabel;
    }
    //index; arsifKontrak; management;
    public function tb_salary_contract($status, $department, $tipe)
    {
        $tabel = DB::table('v_salary_contract')
            ->leftjoin('tb_pph21_status', 'tb_pph21_status.id', '=', 'v_salary_contract.status_pajak');
        $tabel = $tabel->where('v_salary_contract.status', $status);
        if ($tipe != 0 && ($tipe == 'SAI' || $tipe == 'TKA')) {
            // $tabel=$tabel->where('v_salary_contract.tipe_kontrak',$tipe);
            $tabel = $tabel->where(function ($query) use ($tipe) {
                $query->where('v_salary_contract.tipe_kontrak', 'SAI')
                    ->orWhere('v_salary_contract.tipe_kontrak', 'TKA');
            });
        } else {
            $tabel = $tabel->where('v_salary_contract.tipe_kontrak', $tipe);
        }
        if ($department != 0) {
            $tabel = $tabel->where('v_salary_contract.department', $department);
        }
        $tabel = $tabel->orderby('v_salary_contract.department', 'asc')->get(['v_salary_contract.*', 'tb_pph21_status.status_pajak as statusPajak']);
        return $tabel;
    }

    //earn; deduction; management;
    public function tb_employee_contract()
    {
        $tabel = DB::table('tb_salary_contract');
        $tabel = $tabel->get();
        return $tabel;
    }
    //earn; deduction; workEntry; workEntryDetail; summary; tax; management;
    public function tb_department_periode($periode, $data, $tipe)
    {
        $tabel = DB::table($data)
            ->leftjoin('tb_salary_contract', 'tb_salary_contract.id_employee', '=', $data . '.id_employee')
            ->select('tb_salary_contract.department')
            ->groupby('tb_salary_contract.department');
        $tabel = $tabel->where($data . '.periode', $periode);
        // if($tipe!='0')$tabel=$tabel->where('tb_salary_contract.tipe_kontrak',$tipe);
        if ($tipe != 0 && ($tipe == 'SAI' || $tipe == 'TKA')) {
            $tabel = $tabel->where(function ($query) use ($tipe) {
                $query->where('tb_salary_contract.tipe_kontrak', 'SAI')
                    ->orWhere('tb_salary_contract.tipe_kontrak', 'TKA');
            });
        } else {
            $tabel = $tabel->where('tb_salary_contract.tipe_kontrak', $tipe);
        }
        $tabel = $tabel->get();
        return $tabel;
    }
    //earn; management;
    public function tb_earn($periode, $department, $component, $tipe)
    {
        $tabel = DB::table('tb_salary_earn')
            ->join('tb_salary_contract', function ($join) {
                $join->on('tb_salary_contract.id_employee', '=', 'tb_salary_earn.id_employee')
                    ->on('tb_salary_contract.tipe_kontrak', '=', 'tb_salary_earn.tipe_kontrak');
            })
            ->leftjoin('tb_pph21_status', 'tb_pph21_status.id', '=', 'tb_salary_contract.status_pajak')
            ->where('tb_salary_earn.tipe_kontrak', $tipe);
        // if(request()->user()->hasRole('root')){
        //     $tabel=$tabel->where('tb_salary_contract.status','1');
        // }elseif(request()->user()->hasRole('payroll')){
        //     $tabel=$tabel->where('tb_salary_contract.status','1')->where('tb_salary_contract.status_management','0');
        // }elseif(request()->user()->hasRole('payroll_management')){
        //     $tabel=$tabel->where('tb_salary_contract.status','1')->where('tb_salary_contract.status_management','1');
        // }
        if ($component != 0)
            $tabel = $tabel->where('component_name', $component);
        if ($periode != 0)
            $tabel = $tabel->where('periode', $periode);
        if ($department != 0) {
            $tabel = $tabel->where('tb_salary_contract.department', $department);
        }
        $tabel = $tabel->get(['tb_salary_earn.*', 'tb_pph21_status.status_pajak as statuspajak', 'tb_salary_contract.NIK', 'tb_salary_contract.nama_karyawan', 'tb_salary_contract.department', 'tb_salary_contract.jabatan', 'tb_salary_contract.status_pajak', 'tb_salary_contract.total_salary', 'tb_salary_contract.tipe_kontrak']);
        return $tabel;
    }
    //earn_additional; management;
    public function tb_earn_additional($periode, $department, $component, $tipe)
    {
        $tabel = DB::table('tb_salary_earn_additional')
            ->leftjoin('tb_salary_contract', 'tb_salary_contract.id_employee', '=', 'tb_salary_earn_additional.id_employee')
            ->leftjoin('tb_pph21_status', 'tb_pph21_status.id', '=', 'tb_salary_contract.status_pajak');
        if (request()->user()->hasRole('root')) {
            $tabel = $tabel->where('tb_salary_contract.status', '1');
        } elseif (request()->user()->hasRole('payroll')) {
            $tabel = $tabel->where('tb_salary_contract.status', '1')->where('tb_salary_contract.status_management', '0');
        } elseif (request()->user()->hasRole('payroll_management')) {
            $tabel = $tabel->where('tb_salary_contract.status', '1')->where('tb_salary_contract.status_management', '1');
        }
        if ($component != 0)
            $tabel = $tabel->where('component_name', $component);
        if ($periode != 0)
            $tabel = $tabel->where('periode', $periode);
        if ($tipe != 0)
            $tabel = $tabel->where('tb_salary_contract.tipe_kontrak', $tipe);
        if ($department != 0) {
            $tabel = $tabel->where('tb_salary_contract.department', $department);
        }
        $tabel = $tabel->get(['tb_salary_earn_additional.*', 'tb_pph21_status.status_pajak as statuspajak', 'tb_salary_contract.NIK', 'tb_salary_contract.nama_karyawan', 'tb_salary_contract.department', 'tb_salary_contract.jabatan', 'tb_salary_contract.status_pajak', 'tb_salary_contract.total_salary', 'tb_salary_contract.tipe_kontrak']);
        return $tabel;
    }
    //deduction; management;
    public function tb_deduction($periode, $department, $component, $tipe)
    {
        $tabel = DB::table('tb_salary_deduction')
            ->join('tb_salary_contract', function ($join) {
                $join->on('tb_salary_contract.id_employee', '=', 'tb_salary_deduction.id_employee')
                    ->on('tb_salary_contract.tipe_kontrak', '=', 'tb_salary_deduction.tipe_kontrak');
            })
            ->leftjoin('tb_pph21_status', 'tb_pph21_status.id', '=', 'tb_salary_contract.status_pajak')
            ->where('tb_salary_deduction.tipe_kontrak', $tipe);
        if ($component != 0)
            $tabel = $tabel->where('component_name', $component);
        if ($periode != 0)
            $tabel = $tabel->where('periode', $periode);
        if ($department != 0) {
            $tabel = $tabel->where('tb_salary_contract.department', $department);
        }
        $tabel = $tabel->orderby('tb_salary_deduction.updated_at', 'desc')->get(['tb_salary_deduction.*', 'tb_pph21_status.status_pajak as statuspajak', 'tb_salary_contract.NIK', 'tb_salary_contract.nama_karyawan', 'tb_salary_contract.department', 'tb_salary_contract.jabatan', 'tb_salary_contract.status_pajak', 'tb_salary_contract.total_salary', 'tb_salary_contract.tipe_kontrak']);
        return $tabel;
    }

    //workEntry; workEntryDetail; updateSummary; management;
    public function tb_work_entry($periode, $department, $tipe)
    {
        $tabel = DB::table('tb_work_entry')
            ->leftjoin('tb_work_entry_tua', function ($join1) {
                $join1->on('tb_work_entry_tua.id_employee', '=', 'tb_work_entry.id_employee')
                    ->on('tb_work_entry_tua.periode', '=', 'tb_work_entry.periode');
            })
            ->leftjoin('tb_work_entry_muda', function ($join2) {
                $join2->on('tb_work_entry_muda.id_employee', '=', 'tb_work_entry.id_employee')
                    ->on('tb_work_entry_muda.periode', '=', 'tb_work_entry.periode');
            })
            ->leftjoin('tb_salary_contract', function ($join) {
                $join->on('tb_salary_contract.id_employee', '=', 'tb_work_entry.id_employee');
                // ->on('tb_salary_contract.tipe_kontrak','=','tb_work_entry.tipe_kontrak');
            })
            ->where('tb_work_entry.periode', $periode);
        //->where('tb_work_entry.tipe_kontrak',$tipe);
        if ($tipe != 0 && ($tipe == 'SAI' || $tipe == 'TKA')) {
            $tabel = $tabel->where(function ($query) use ($tipe) {
                $query->where('tb_work_entry.tipe_kontrak', 'SAI')
                    ->orWhere('tb_work_entry.tipe_kontrak', 'TKA');
            });
        } else {
            $tabel = $tabel->where('tb_work_entry.tipe_kontrak', $tipe);
        }
        // if(request()->user()->hasRole('root')||request()->user()->hasRole('payroll')){
        //     $tabel=$tabel->where('tb_salary_contract.status','1');
        // }elseif(request()->user()->hasRole('payroll_management')){
        //     $tabel=$tabel->where('tb_salary_contract.status','1')->where('tb_salary_contract.status_management','1');
        // }
        if ($department != 0) {
            $tabel = $tabel->where('tb_salary_contract.department', $department);
        }
        $tabel = $tabel->get(['tb_work_entry.*', 'tb_salary_contract.NIK', 'tb_salary_contract.nama_karyawan', 'tb_salary_contract.department as nama_bagian', 'tb_salary_contract.jabatan as kode_jabatan', 'tb_salary_contract.tipe_kontrak', 'tb_work_entry_tua.hari_kerja as hk_tua', 'tb_work_entry_tua.aktual_kerja as ak_tua', 'tb_work_entry_tua.aktual_shift as as_tua', 'tb_work_entry_tua.absen as ab_tua', 'tb_work_entry_muda.hari_kerja as hk_muda', 'tb_work_entry_muda.aktual_kerja as ak_muda', 'tb_work_entry_muda.aktual_shift as as_muda', 'tb_work_entry_muda.absen as ab_muda']);
        return $tabel;
    }
    //updateSummaryOne;
    public function tb_work_entry_one($periode, $id_employee, $tipe)
    {
        $tabel = DB::table('tb_work_entry')
            ->leftjoin('tb_salary_contract', 'tb_salary_contract.id_employee', '=', 'tb_work_entry.id_employee')
            ->where('periode', $periode)
            ->where('tb_work_entry.id_employee', $id_employee)
            ->where('tb_work_entry.tipe_kontrak', $tipe)
            ->get(['tb_work_entry.*', 'tb_salary_contract.NIK', 'tb_salary_contract.nama_karyawan', 'tb_salary_contract.department as nama_bagian', 'tb_salary_contract.jabatan as kode_jabatan']);
        return $tabel;
    }
    //templateWorkEntry;
    public function tb_employee_template($tipe)
    {
        $tabel = DB::table('v_salary_contract')
            ->leftjoin('tb_pph21_status', 'tb_pph21_status.id', '=', 'v_salary_contract.status_pajak');
        $tabel = $tabel->where('v_salary_contract.status', '1');
        if ($tipe != 0 && ($tipe == 'SAI' || $tipe == 'TKA')) {
            // $tabel=$tabel->where('v_salary_contract.tipe_kontrak',$tipe);
            $tabel = $tabel->where(function ($query) use ($tipe) {
                $query->where('v_salary_contract.tipe_kontrak', 'SAI')
                    ->orWhere('v_salary_contract.tipe_kontrak', 'TKA');
            });
        } else {
            $tabel = $tabel->where('v_salary_contract.tipe_kontrak', $tipe);
        }
        $tabel = $tabel->orderby('v_salary_contract.department', 'asc')->get(['v_salary_contract.id_employee as id', 'v_salary_contract.NIK as NIK', 'v_salary_contract.nama_karyawan', 'v_salary_contract.department as dept_code', 'v_salary_contract.jabatan as position_name', 'v_salary_contract.tanggal_kontrak', 'v_salary_contract.tipe_kontrak']);

        // $tabel=DB::table('tb_salary_contract')
        // ->where('tb_salary_contract.status','1');
        // // if($tipe!=0)$tabel=$tabel->where('tb_salary_contract.tipe_kontrak',$tipe);
        // if($tipe!=0&&($tipe=='SAI'||$tipe=='TKA')){
        //     $tabel=$tabel->where(function ($query) use ($tipe) {
        //         $query->where('tb_salary_contract.tipe_kontrak', 'SAI')
        //               ->orWhere('tb_salary_contract.tipe_kontrak', 'TKA');
        //     });        
        // }
        // if($tipe!=0&&$tipe=='MAGANG'){
        //     $tabel=$tabel->where('tb_salary_contract.tipe_kontrak',$tipe);
        // }
        // $tabel=$tabel->orderby('tb_salary_contract.tipe_kontrak','desc')->get(['tb_salary_contract.id_employee as id','tb_salary_contract.NIK','tb_salary_contract.nama_karyawan','tb_salary_contract.department as dept_code','tb_salary_contract.jabatan as position_name','tb_salary_contract.tanggal_kontrak','tb_salary_contract.tipe_kontrak']);
        return $tabel;
    }

    //summary; sendMail;
    public function tb_salary_summary($periode, $department, $tipe)
    {
        $tabel = DB::table('tb_salary_summary')
            // ->join('tb_salary_contract', function ($join) {
            //     $join->on('tb_salary_contract.id_employee', '=', 'tb_salary_summary.id_employee');
            // })
            ->join('v_salary_contract as tb_salary_contract', function ($join) {
                $join->on('tb_salary_contract.id_employee', '=', 'tb_salary_summary.id_employee');
            })
            ->leftjoin('tb_pph21_status', 'tb_pph21_status.id', '=', 'tb_salary_contract.status_pajak')
            ->where('periode', $periode);
        // ->where('tb_salary_summary.tipe_kontrak',$tipe);
        if ($tipe != 0 && ($tipe == 'SAI' || $tipe == 'TKA')) {
            $tabel = $tabel->where(function ($query) use ($tipe) {
                $query->where('tb_salary_summary.tipe_kontrak', 'SAI')
                    ->orWhere('tb_salary_summary.tipe_kontrak', 'TKA');
            });
        } else {
            $tabel = $tabel->where('tb_salary_summary.tipe_kontrak', $tipe);
        }
        if ($department != 0) {
            $tabel = $tabel->where('tb_salary_contract.department', $department);
        }
        $tabel = $tabel->orderby('tb_salary_contract.department', 'asc')->orderby('tb_salary_contract.cc_code', 'asc')->get(['tb_salary_summary.*', 'tb_pph21_status.status_pajak as statuspajak', 'tb_salary_contract.NIK', 'tb_salary_contract.nama_karyawan', 'tb_salary_contract.department as kode_bagian', 'tb_salary_contract.jabatan', 'tb_salary_contract.status_pajak', 'tb_salary_contract.total_salary', 'tb_salary_contract.tipe_kontrak', 'tb_salary_contract.upah_harian as upah_psab', 'tb_salary_contract.tunjangan_harian as tunjangan_pkl', 'tb_salary_contract.tanggal_kontrak', 'tb_salary_contract.tipe_kontrak as tipe', 'tb_salary_contract.cc_code', 'tb_salary_contract.cc_name', 'tb_salary_contract.jaminan_kecelakaan_kerja', 'tb_salary_contract.jaminan_kematian', 'tb_salary_contract.kesehatan', 'tb_salary_contract.nomor_rekening']);
        return $tabel;
    }
    //summary;
    public function updateSummary_deleted($periode, $department, $tipe)
    {
        if ($periode == 0)
            $periode = date('Y-m');
        $nextmonth = date('Y-m', strtotime('+1 months', strtotime($periode . '-01'))) . '-01';
        $admin = Auth::user()->name;
        $today = date('Y-m-d');
        $now = date('Y-m-d H:i:s');
        $tb_salary_component = $this->tb_salary_component();
        foreach ($tb_salary_component as $dt) {
            if ($dt->component == 'tunjangan_shift')
                $tunjangan_shift_a = $dt->amount;
            if ($dt->component == 'tunjangan_transport')
                $tunjangan_transport_a = $dt->amount;
            if ($dt->component == 'serikat')
                $serikat_a = $dt->amount;
            if ($dt->component == 'koperasi')
                $koperasi_a = $dt->amount;
            if ($dt->component == 'biaya_jabatan') {
                $biaya_jabatan_p = $dt->presentase;
                $biaya_jabatan_l = $dt->amount;
            }
        }

        $tb_work_entry = $this->tb_work_entry($periode, $department, $tipe);
        foreach ($tb_work_entry as $dt) {
            $tb_salary_contract_employee = $this->tb_salary_contract_employee($dt->id_employee, $tipe);
            foreach ($tb_salary_contract_employee as $dt2) {
                //Recondition Trial 2023-05-12
                $awal = date('Y', strtotime($periode . '-01')) . '-01';
                $penghasilan_sebelumnya = DB::table('tb_salary_record')
                    ->where('periode', '>=', $awal)
                    ->where('periode', '<', $dt->periode)
                    ->where('id_employee', $dt->id_employee)
                    ->where('tipe_kontrak', $tipe)
                    ->sum('gaji_bruto');


                //PSAB
                $aktual_kerja = (int) (float) $dt->aktual_kerja;
                $aktual_shift = (int) (float) $dt->aktual_shift;
                $upah_harian = $aktual_kerja * $dt2->upah_harian;
                //PKL
                $tunjangan_harian = $aktual_kerja * $dt2->tunjangan_harian;
                //penambah
                $salary = $dt2->upah_pokok + $dt2->tunjangan_jabatan + $dt2->tunjangan_prestasi_tahunan + $dt2->tunjangan_skill + $upah_harian + $tunjangan_harian;
                $tunjangan_transport = 0;
                $tunjangan_shift = 0;
                $kesehatan = $dt2->kesehatan * $dt2->status_bpjs;
                if ($dt2->tunjangan_transport == 1)
                    $tunjangan_transport = $aktual_kerja * $tunjangan_transport_a;
                if ($dt2->tunjangan_shift == 1)
                    $tunjangan_shift = $aktual_shift * $tunjangan_shift_a;
                $bpjs_perusahaan = $dt2->jaminan_kecelakaan_kerja + $dt2->jaminan_kematian + $kesehatan + $dt2->jaminan_hari_tua_perusahaan;
                $rapel = 0;
                $insentif_pph21 = 0;
                $pph21_lebih_bayar = 0;

                $tb_earn = $this->tb_earn_employee($periode, $dt->id_employee, $tipe);
                foreach ($tb_earn as $dt3) {
                    if ($dt3->component_name == 'rapel')
                        $rapel = $dt3->earn_amount;
                    if ($dt3->component_name == 'insentif_pph21')
                        $insentif_pph21 = $dt3->earn_amount;
                    if ($dt3->component_name == 'pph21_lebih_bayar')
                        $pph21_lebih_bayar = $dt3->earn_amount;
                }

                $penambah_pajak = $salary + $tunjangan_transport + $tunjangan_shift + $bpjs_perusahaan + $rapel;

                //pengurang
                //$absen=$dt->hari_kerja-($dt->aktual_kerja+$dt->cuti);
                if ($dt2->working_day == 6)
                    $pembagi = 25;
                else
                    $pembagi = 21;
                $absen = (int) (float) $dt->absen;
                $absen_amount = round($salary * ($absen / $pembagi));
                $pengurang_pajak = $dt2->jaminan_hari_tua + $dt2->jaminan_pensiun + $absen_amount;

                //balance
                $sisa_bulan = $this->sisa_bulan($periode);
                $gaji = $penambah_pajak - $pengurang_pajak;
                $penghasilan_bruto = $gaji * $sisa_bulan;

                //Recondition Trial 2023-05-12
                //$now_bruto=$penghasilan_bruto;

                //Recondition Ter 2024-01-20
                $now_bruto = $gaji;

                $penghasilan_bruto = $penghasilan_bruto + $penghasilan_sebelumnya;

                $biaya_jabatan = $biaya_jabatan_p * $penghasilan_bruto / 100;
                if ($biaya_jabatan > $biaya_jabatan_l)
                    $biaya_jabatan = $biaya_jabatan_l;
                $penghasilan_netto = $penghasilan_bruto - $biaya_jabatan;
                $pkp = $penghasilan_netto - $dt2->ptkp;

                //tax calculation
                $tb_pph21 = $this->tb_pph21();
                foreach ($tb_pph21 as $dt3) {
                    if ($dt3->id == 1) {
                        $batas1 = $dt3->batas_atas;
                        $pajak1 = $dt3->prosentase;
                    }
                    if ($dt3->id == 2) {
                        $batas2 = $dt3->batas_atas;
                        $pajak2 = $dt3->prosentase;
                    }
                    if ($dt3->id == 3) {
                        $batas3 = $dt3->batas_atas;
                        $pajak3 = $dt3->prosentase;
                    }
                    if ($dt3->id == 4) {
                        $pajak4 = $dt3->prosentase;
                    }
                }
                if ($pkp <= $batas1) {
                    $pph21 = $pkp * $pajak1 / 100;
                } else if ($pkp > $batas1 && $pkp <= $batas2) {
                    $angka1 = $batas1 * $pajak1 / 100;
                    $angka2 = ($pkp - $batas1) * $pajak2 / 100;
                    $pph21 = $angka1 + $angka2;
                } else if ($pkp > $batas2 && $pkp <= $batas3) {
                    $angka1 = $batas1 * $pajak1 / 100;
                    $angka2 = ($batas2 - $batas1) * $pajak2 / 100;
                    $angka3 = ($pkp - $batas2) * $pajak3 / 100;
                    $pph21 = $angka1 + $angka2 + $angka3;
                } else if ($pkp > $batas3) {
                    $angka1 = $batas1 * $pajak1 / 100;
                    $angka2 = ($batas2 - $batas1) * $pajak2 / 100;
                    $angka3 = ($batas3 - $batas2) * $pajak3 / 100;
                    $angka4 = ($pkp - $batas3) * $pajak4 / 100;
                    $pph21 = $angka1 + $angka2 + $angka3 + $angka4;
                }
                $sisa_bulan = $this->sisa_bulan($periode);
                $pph21 = $pph21 / 12;
                if ($dt2->status_npwp == 0)
                    $pph21_tambahan = $pph21 * 20 / 100;
                else
                    $pph21_tambahan = 0;
                $pph21 = $pph21 + $pph21_tambahan;
                if ($pph21 > 0)
                    $pph21_show = $pph21;
                else
                    $pph21_show = 0;

                //tax_conditional
                $tb_salary_tax_conditional = DB::table('tb_salary_tax_conditional')
                    ->leftjoin('tb_salary_tax', 'tb_salary_tax.id', '=', 'tb_salary_tax_conditional.id_salary_tax')
                    ->where('tb_salary_tax.periode', $periode)->where('tb_salary_tax.id_employee', $dt->id_employee)
                    ->get();
                foreach ($tb_salary_tax_conditional as $dt_tax) {
                    if ($dt_tax->status_implement == 1) {
                        $pph21_show = $dt_tax->tax_amount;
                    }
                }
                //end_tax_conditional

                //Summary Calculation
                $serikat = 0;
                $koperasi = 0;
                $standby_oncall = 0;
                $seragam = 0;
                $pants = 0;
                $pph21_kurang_bayar = 0;
                $lain_lain = 0;
                if ($dt2->serikat == 1)
                    $serikat = $serikat_a;
                if ($dt2->koperasi == 1)
                    $koperasi = $koperasi_a;
                $tb_deduction_employee = $this->tb_deduction_employee($periode, $dt->id_employee, $tipe);
                foreach ($tb_deduction_employee as $dt3) {
                    if ($dt3->component_name == 'koperasi')
                        $koperasi = $koperasi + $dt3->deduction_amount;
                    if ($dt3->component_name == 'standby_oncall')
                        $standby_oncall = $dt3->deduction_amount;
                    if ($dt3->component_name == 'seragam')
                        $seragam = $dt3->deduction_amount;
                    if ($dt3->component_name == 'pants')
                        $pants = $dt3->deduction_amount;
                    if ($dt3->component_name == 'pph21_kurang_bayar')
                        $pph21_kurang_bayar = $dt3->deduction_amount;
                    if ($dt3->component_name == 'lain_lain')
                        $lain_lain = $dt3->deduction_amount;
                }
                $bpjs_kesehatan = $dt2->bpjs_kesehatan * $dt2->status_bpjs;
                $pendapatan_bruto = $salary + $tunjangan_transport + $tunjangan_shift + $rapel + $insentif_pph21 + $pph21_lebih_bayar;
                $potongan = $dt2->jaminan_hari_tua + $dt2->jaminan_pensiun + $bpjs_kesehatan + $absen_amount + $serikat + $koperasi + $standby_oncall + $seragam + $pants + $pph21_kurang_bayar + $lain_lain + $pph21_show;
                $pendapatan_netto = $pendapatan_bruto - $potongan;

                //calculation pendapatan sebelumnya diperiode ini
                $tb_salary_record = DB::table('tb_salary_record')
                    ->select('periode', 'id_employee', 'tipe_kontrak', DB::raw('SUM(gaji_bruto) as pendapatan_sebelumnya, SUM(pph21_ter) as ter_sebelumnya'))
                    ->groupBy('periode', 'id_employee', 'tipe_kontrak')
                    ->where('periode', $dt->periode)
                    ->where('id_employee', $dt->id_employee)
                    ->where('tipe_kontrak', $tipe)
                    ->where('tanggal_pembayaran', '<', $nextmonth)
                    ->get();
                $pendapatan_sebelumnya = 0;
                $ter_sebelumnya = 0;
                foreach ($tb_salary_record as $dt4) {
                    $pendapatan_sebelumnya = $dt4->pendapatan_sebelumnya;
                    $ter_sebelumnya = $dt4->ter_sebelumnya;
                }
                $total_gaji = $gaji + $pendapatan_sebelumnya;
                //End calculation pendapatan sebelumnya diperiode ini

                //calculation pph21 with ter
                $tb_pph21_ter = $this->tb_pph21_ter($dt2->kategori, $total_gaji);
                foreach ($tb_pph21_ter as $dt3) {
                    $ter = $dt3->prosentase;
                    $pph21_ter = ($ter * $total_gaji / 100) - $ter_sebelumnya;
                }
                //End calculation PPh21 with ter


                //return $penghasilan_sebelumnya;
                $periksa_tax = $this->qty_salary_tax($dt->periode, $dt->id_employee, $tipe);
                //return $penghasilan_sebelumnya;
                if ($periksa_tax == 0) {
                    $tambah_tax = DB::table('tb_salary_tax')->insert([
                        'periode' => $periode,
                        'id_employee' => $dt->id_employee,
                        'tipe_kontrak' => $dt->tipe_kontrak,
                        'salary' => $salary,
                        'tunjangan_transport' => $tunjangan_transport,
                        'tunjangan_shift' => $tunjangan_shift,
                        'rapel' => $rapel,
                        'jaminan_kecelakaan_kerja' => $dt2->jaminan_kecelakaan_kerja,
                        'jaminan_kematian' => $dt2->jaminan_kematian,
                        'kesehatan' => $kesehatan,
                        'jaminan_hari_tua' => $dt2->jaminan_hari_tua,
                        'jaminan_pensiun' => $dt2->jaminan_pensiun,
                        'tunjangan_hari_raya' => '0',
                        'bonus' => '0',
                        'ketidakhadiran' => $absen_amount,
                        'standby_oncall' => '0',
                        'gaji' => $gaji,
                        'pendapatan_terbayar' => $pendapatan_sebelumnya,
                        'pph21_ter_terbayar' => $ter_sebelumnya,
                        'penghasilan_bruto' => $penghasilan_bruto,
                        'penghasilan_sebelumnya' => $penghasilan_sebelumnya,
                        'biaya_jabatan' => $biaya_jabatan,
                        'penghasilan_netto' => $penghasilan_netto,
                        'status_pajak' => $dt2->statuspajak,
                        'ptkp' => $dt2->ptkp,
                        'pkp' => $pkp,
                        'pph21_salary' => $pph21,
                        'kategori' => $dt2->kategori,
                        'ter' => $ter,
                        'pph21_ter' => $pph21_ter,
                        'admin' => $admin,
                        'created_at' => $today,
                        'updated_at' => $now
                    ]);
                } else {
                    $update_tax = DB::table('tb_salary_tax')
                        ->where('periode', $dt->periode)
                        ->where('id_employee', $dt->id_employee)
                        ->update([
                            'salary' => $salary,
                            'tunjangan_transport' => $tunjangan_transport,
                            'tunjangan_shift' => $tunjangan_shift,
                            'rapel' => $rapel,
                            'jaminan_kecelakaan_kerja' => $dt2->jaminan_kecelakaan_kerja,
                            'jaminan_kematian' => $dt2->jaminan_kematian,
                            'kesehatan' => $kesehatan,
                            'jaminan_hari_tua' => $dt2->jaminan_hari_tua,
                            'jaminan_pensiun' => $dt2->jaminan_pensiun,
                            'tunjangan_hari_raya' => '0',
                            'bonus' => '0',
                            'ketidakhadiran' => $absen_amount,
                            'standby_oncall' => '0',
                            'gaji' => $gaji,
                            'pendapatan_terbayar' => $pendapatan_sebelumnya,
                            'pph21_ter_terbayar' => $ter_sebelumnya,
                            'penghasilan_bruto' => $penghasilan_bruto,
                            'penghasilan_sebelumnya' => $penghasilan_sebelumnya,
                            'biaya_jabatan' => $biaya_jabatan,
                            'penghasilan_netto' => $penghasilan_netto,
                            'status_pajak' => $dt2->statuspajak,
                            'ptkp' => $dt2->ptkp,
                            'pkp' => $pkp,
                            'pph21_salary' => $pph21,
                            'kategori' => $dt2->kategori,
                            'ter' => $ter,
                            'pph21_ter' => $pph21_ter,
                            'admin' => $admin,
                            'created_at' => $today,
                            'updated_at' => $now
                        ]);
                }
                $cek_tb_salary_record = DB::table('tb_salary_record')
                    ->where('periode', $dt->periode)
                    ->where('id_employee', $dt->id_employee)
                    ->where('tipe_kontrak', $tipe)
                    ->where('tanggal_pembayaran', $nextmonth)
                    ->count();
                if ($cek_tb_salary_record > 0) {
                    $tb_salary_record = DB::table('tb_salary_record')
                        ->where('periode', $dt->periode)
                        ->where('id_employee', $dt->id_employee)
                        ->where('tipe_kontrak', $tipe)
                        ->where('tanggal_pembayaran', $nextmonth)
                        ->update([
                            'gaji_bruto' => $now_bruto,
                            'pph21_ter' => $pph21_ter,
                            'tanggal_pembayaran' => $nextmonth,
                        ]);
                } else {
                    $tb_salary_record = DB::table('tb_salary_record')->insert([
                        'id_employee' => $dt->id_employee,
                        'tipe_kontrak' => $tipe,
                        'periode' => $dt->periode,
                        'gaji_bruto' => $now_bruto,
                        'pph21_ter' => $pph21_ter,
                        'tanggal_pembayaran' => $nextmonth,
                    ]);
                }
                $periksa_summary = $this->qty_salary_summary($dt->periode, $dt->id_employee, $tipe);
                if ($periksa_summary == 0) {
                    $tambah_summary = DB::table('tb_salary_summary')->insert([
                        'periode' => $periode,
                        'tanggal_pembayaran' => $nextmonth,
                        'id_employee' => $dt->id_employee,
                        'tipe_kontrak' => $dt->tipe_kontrak,
                        'upah_pokok' => $dt2->upah_pokok,
                        'upah_harian' => $upah_harian,
                        'tunjangan_harian' => $tunjangan_harian,
                        'tunjangan_jabatan' => $dt2->tunjangan_jabatan,
                        'tunjangan_prestasi_tahunan' => $dt2->tunjangan_prestasi_tahunan,
                        'tunjangan_skill' => $dt2->tunjangan_skill,
                        'aktual_kerja' => $aktual_kerja,
                        'tunjangan_transport' => $tunjangan_transport,
                        'aktual_shift' => $aktual_shift,
                        'tunjangan_shift' => $tunjangan_shift,
                        'rapel' => $rapel,
                        'insentif_pph21' => '0',
                        'pph21_lebih_bayar' => '0',
                        'pendapatan_bruto' => $pendapatan_bruto,
                        'absen' => $absen,
                        'ketidakhadiran' => $absen_amount,
                        'standby_oncall' => '0',
                        'jaminan_hari_tua' => $dt2->jaminan_hari_tua,
                        'jaminan_pensiun' => $dt2->jaminan_pensiun,
                        'bpjs_kesehatan' => $bpjs_kesehatan,
                        'serikat' => $serikat,
                        'koperasi' => $koperasi,
                        'seragam' => $seragam,
                        'pants' => $pants,
                        'pph21' => $pph21_show,
                        'pph21_ter' => $pph21_ter,
                        'pph21_kurang_bayar' => $pph21_kurang_bayar,
                        'lain_lain' => $lain_lain,
                        'potongan' => $potongan,
                        'pendapatan_netto' => $pendapatan_netto,
                        'admin' => $admin,
                        'created_at' => $today,
                        'updated_at' => $now,
                        'note' => 'A'
                    ]);
                } else {
                    $tambah_summary = DB::table('tb_salary_summary')
                        ->where('periode', $dt->periode)
                        ->where('id_employee', $dt->id_employee)
                        ->where('tipe_kontrak', $dt->tipe_kontrak)
                        ->update([
                            'tanggal_pembayaran' => $nextmonth,
                            'upah_pokok' => $dt2->upah_pokok,
                            'upah_harian' => $upah_harian,
                            'tunjangan_harian' => $tunjangan_harian,
                            'tunjangan_jabatan' => $dt2->tunjangan_jabatan,
                            'tunjangan_prestasi_tahunan' => $dt2->tunjangan_prestasi_tahunan,
                            'tunjangan_skill' => $dt2->tunjangan_skill,
                            'aktual_kerja' => $aktual_kerja,
                            'tunjangan_transport' => $tunjangan_transport,
                            'aktual_shift' => $aktual_shift,
                            'tunjangan_shift' => $tunjangan_shift,
                            'rapel' => $rapel,
                            'insentif_pph21' => $insentif_pph21,
                            'pph21_lebih_bayar' => $pph21_lebih_bayar,
                            'pendapatan_bruto' => $pendapatan_bruto,
                            'absen' => $absen,
                            'ketidakhadiran' => $absen_amount,
                            'standby_oncall' => $standby_oncall,
                            'jaminan_hari_tua' => $dt2->jaminan_hari_tua,
                            'jaminan_pensiun' => $dt2->jaminan_pensiun,
                            'bpjs_kesehatan' => $bpjs_kesehatan,
                            'serikat' => $serikat,
                            'koperasi' => $koperasi,
                            'seragam' => $seragam,
                            'pants' => $pants,
                            'pph21' => $pph21_show,
                            'pph21_ter' => $pph21_ter,
                            'pph21_kurang_bayar' => $pph21_kurang_bayar,
                            'lain_lain' => $lain_lain,
                            'potongan' => $potongan,
                            'pendapatan_netto' => $pendapatan_netto,
                            'admin' => $admin,
                            'created_at' => $today,
                            'updated_at' => $now
                        ]);
                }

            }
        }
        return redirect()->back()->with(['info' => 'Success Update']);
    }
    public function updateSummaryTer($periode, $department, $tipe)
    {
        if ($periode == 0)
            $periode = date('Y-m');
        $nextmonth = date('Y-m', strtotime('+1 months', strtotime($periode . '-01'))) . '-01';
        $admin = Auth::user()->name;
        $today = date('Y-m-d');
        $now = date('Y-m-d H:i:s');
        $tb_salary_component = $this->tb_salary_component();
        foreach ($tb_salary_component as $dt) {
            if ($dt->component == 'tunjangan_shift')
                $tunjangan_shift_a = $dt->amount;
            if ($dt->component == 'tunjangan_shift_management')
                $tunjangan_shift_management = $dt->amount;
            if ($dt->component == 'tunjangan_transport')
                $tunjangan_transport_a = $dt->amount;
            if ($dt->component == 'serikat')
                $serikat_a = $dt->amount;
            if ($dt->component == 'koperasi')
                $koperasi_a = $dt->amount;
            if ($dt->component == 'biaya_jabatan') {
                $biaya_jabatan_p = $dt->presentase;
                $biaya_jabatan_l = $dt->amount;
            }
        }
        $tb_work_entry = $this->tb_work_entry($periode, $department, $tipe);
        $employeeIds = $tb_work_entry->pluck('id_employee')->unique()->values();

        $salaryContracts = DB::table('v_salary_contract as tb_salary_contract')
            ->leftjoin('tb_pph21_status', 'tb_pph21_status.id', '=', 'tb_salary_contract.status_pajak')
            ->whereIn('tb_salary_contract.id_employee', $employeeIds);
        if ($tipe != 0 && ($tipe == 'SAI' || $tipe == 'TKA')) {
            $salaryContracts = $salaryContracts->where(function ($query) {
                $query->where('tb_salary_contract.tipe_kontrak', 'SAI')
                    ->orWhere('tb_salary_contract.tipe_kontrak', 'TKA');
            });
        } else {
            $salaryContracts = $salaryContracts->where('tb_salary_contract.tipe_kontrak', $tipe);
        }
        $salaryContractsByEmployee = $salaryContracts
            ->get(['tb_salary_contract.*', 'tb_pph21_status.status_pajak as statuspajak', 'tb_pph21_status.ptkp', 'tb_pph21_status.kategori'])
            ->groupBy('id_employee');

        $earnsByEmployee = DB::table('tb_salary_earn')
            ->where('periode', $periode)
            ->whereIn('id_employee', $employeeIds)
            ->where('tipe_kontrak', $tipe)
            ->get()
            ->groupBy('id_employee');
        $deductionsByEmployee = DB::table('tb_salary_deduction')
            ->where('periode', $periode)
            ->whereIn('id_employee', $employeeIds)
            ->where('tipe_kontrak', $tipe)
            ->get()
            ->groupBy('id_employee');
        $terMonthlyByEmployee = DB::table('tb_salary_ter_monthly')
            ->where('periode', $periode)
            ->whereIn('id_employee', $employeeIds)
            ->get()
            ->groupBy('id_employee');
        $summariesByEmployee = DB::table('tb_salary_summary')
            ->where('periode', $periode)
            ->whereIn('id_employee', $employeeIds)
            ->get()
            ->groupBy('id_employee');
        $terRates = DB::table('tb_pph21_ter')->get();
        $processedEmployees = [];

        foreach ($tb_work_entry as $dt) {
            $tb_salary_contract_employee = $salaryContractsByEmployee->get($dt->id_employee, collect());
            foreach ($tb_salary_contract_employee as $dt2) {
                $employeeAlreadyProcessed = isset($processedEmployees[$dt->id_employee]);
                //PSAB
                $aktual_kerja = (int) (float) $dt->aktual_kerja;
                $aktual_shift = (int) (float) $dt->aktual_shift;
                $upah_harian = $aktual_kerja * $dt2->upah_harian;
                //PKL
                $tunjangan_harian = $aktual_kerja * $dt2->tunjangan_harian;
                //penambah
                $salary = $dt2->upah_pokok + $dt2->tunjangan_jabatan + $dt2->tunjangan_prestasi_tahunan + $dt2->tunjangan_skill + $upah_harian + $tunjangan_harian;
                $tunjangan_transport = 0;
                $tunjangan_shift = 0;
                $kesehatan = $dt2->kesehatan * $dt2->status_bpjs;
                if ($dt2->tunjangan_transport == 1)
                    $tunjangan_transport = $aktual_kerja * $tunjangan_transport_a;
                if ($dt2->tunjangan_shift == 1) {
                    if ($dt2->tipe_kontrak == 'MANAGEMENT') {
                        $tunjangan_shift = $aktual_shift * $tunjangan_shift_management;
                    } else {
                        $tunjangan_shift = $aktual_shift * $tunjangan_shift_a;
                    }
                }
                $bpjs_perusahaan = $dt2->jaminan_kecelakaan_kerja + $dt2->jaminan_kematian + $kesehatan + $dt2->jaminan_hari_tua_perusahaan;
                $rapel = 0;
                $insentif_pph21 = 0;
                $pph21_lebih_bayar = 0;

                $tb_earn = $earnsByEmployee->get($dt->id_employee, collect());
                foreach ($tb_earn as $dt3) {
                    if ($dt3->component_name == 'rapel')
                        $rapel = $dt3->earn_amount;
                    if ($dt3->component_name == 'insentif_pph21')
                        $insentif_pph21 = $dt3->earn_amount;
                    if ($dt3->component_name == 'pph21_lebih_bayar')
                        $pph21_lebih_bayar = $dt3->earn_amount;
                }

                $penambah_pajak = $salary + $tunjangan_transport + $tunjangan_shift + $bpjs_perusahaan + $rapel;

                //pengurang
                //$absen=$dt->hari_kerja-($dt->aktual_kerja+$dt->cuti);
                if ($dt2->working_day == 6)
                    $pembagi = 25;
                else
                    $pembagi = 21;
                $absen = (int) (float) $dt->absen;
                $absen_amount = round($salary * ($absen / $pembagi));
                // $pengurang_pajak=$dt2->jaminan_hari_tua+$dt2->jaminan_pensiun+$absen_amount;
                $pengurang_pajak = $absen_amount;

                //balance
                $penghasilan_bruto_pajak = $penambah_pajak - $pengurang_pajak;
                $employeeTerRows = $terMonthlyByEmployee->get($dt->id_employee, collect());
                if ($employeeAlreadyProcessed) {
                    $cum = $this->cum_salary_tax_ter($dt->periode, $dt->id_employee, 'tb_salary_summary', 'payroll');
                } else {
                    $existingTer = $employeeTerRows->first(function ($row) {
                        return $row->related_table == 'tb_salary_summary' && $row->category == 'payroll';
                    });
                    $terAnchor = $existingTer ? $existingTer->created_at : date('Y-m-d H:i:s');
                    $previousTerRows = $employeeTerRows->filter(function ($row) use ($terAnchor) {
                        return $row->created_at < $terAnchor;
                    });
                    $cum = $previousTerRows->sum('current_amount');
                }
                $cum_tax = $cum + $penghasilan_bruto_pajak;
                $prev = $cum_tax - $penghasilan_bruto_pajak;
                //calculation pph21 with ter
                $tb_pph21_ter = $terRates;
                foreach ($tb_pph21_ter as $dt3) {
                    if ($dt3->ter != $dt2->kategori || $dt3->batas_bawah >= $cum_tax || $dt3->batas_atas < $cum_tax)
                        continue;
                    $ter_id = $dt3->id;
                    $ter = $dt3->prosentase;
                    $pph21_ter = ($ter * $cum_tax / 100);
                }
                //End calculation PPh21 with ter

                //Summary Calculation
                $serikat = 0;
                $koperasi = 0;
                $standby_oncall = 0;
                $seragam = 0;
                $pants = 0;
                $pph21_kurang_bayar = 0;
                $lain_lain = 0;
                if ($dt2->serikat == 1)
                    $serikat = $serikat_a;
                if ($dt2->koperasi == 1)
                    $koperasi = $koperasi_a;
                $tb_deduction_employee = $deductionsByEmployee->get($dt->id_employee, collect());
                foreach ($tb_deduction_employee as $dt3) {
                    if ($dt3->component_name == 'koperasi')
                        $koperasi = $koperasi + $dt3->deduction_amount;
                    if ($dt3->component_name == 'standby_oncall')
                        $standby_oncall = $dt3->deduction_amount;
                    if ($dt3->component_name == 'seragam')
                        $seragam = $dt3->deduction_amount;
                    if ($dt3->component_name == 'pants')
                        $pants = $dt3->deduction_amount;
                    if ($dt3->component_name == 'pph21_kurang_bayar')
                        $pph21_kurang_bayar = $dt3->deduction_amount;
                    if ($dt3->component_name == 'lain_lain')
                        $lain_lain = $lain_lain + $dt3->deduction_amount;
                }
                $bpjs_kesehatan = $dt2->bpjs_kesehatan * $dt2->status_bpjs;
                $pendapatan_bruto = $salary + $tunjangan_transport + $tunjangan_shift + $rapel + $insentif_pph21 + $pph21_lebih_bayar;
                $potongan = $dt2->jaminan_hari_tua + $dt2->jaminan_pensiun + $bpjs_kesehatan + $absen_amount + $serikat + $koperasi + $standby_oncall + $seragam + $pants + $pph21_kurang_bayar + $lain_lain + $pph21_ter;
                $pendapatan_netto = $pendapatan_bruto - $potongan;
                // $teks=$dt2->jaminan_hari_tua." ".$dt2->jaminan_pensiun." ".$bpjs_kesehatan." ".$absen_amount." ".$serikat." ".$koperasi." ".$standby_oncall." ".$seragam." ".$pants." ".$pph21_kurang_bayar." ".$lain_lain." ".$pph21_ter;
                // if($dt->id_employee=='1737')return $teks;



                $fiscal_year = date('Y', strtotime($periode . '-01'));
                if ($employeeAlreadyProcessed) {
                    $periksa_tax = $this->qty_salary_tax_ter($dt->periode, $dt->id_employee, 'tb_salary_summary', 'payroll');
                    $prev_tax = $this->prev_salary_tax_ter($dt->periode, $dt->id_employee, 'tb_salary_summary', 'payroll');
                } else {
                    $periksa_tax = $employeeTerRows->filter(function ($row) {
                        return $row->related_table == 'tb_salary_summary' && $row->category == 'payroll';
                    })->count();
                    $prev_tax = $previousTerRows->sum('tax_amount');
                }
                $balance_amount = $pph21_ter - $prev_tax;
                if ($periksa_tax == 0) {
                    $lock = DB::table('tb_salary_ter_monthly')->where('periode', $dt->periode)->where('id_employee', $dt->id_employee)->update(['locked' => '1']);
                    $tambah_tax = DB::table('tb_salary_ter_monthly')->insert([
                        'id_employee' => $dt->id_employee,
                        'fiscal_year' => $fiscal_year,
                        'periode' => $periode,
                        'related_table' => 'tb_salary_summary',
                        'category' => 'payroll',
                        'previous_payment' => $prev_tax,
                        'monthly_salary' => '0',
                        'prev_amount' => $prev,
                        'current_amount' => $penghasilan_bruto_pajak,
                        'cumulative_amount' => $cum_tax,
                        'status_pajak' => $dt2->status_pajak,
                        'ter_id' => $ter_id,
                        'prosentase' => $ter,
                        'tax_amount' => $pph21_ter,
                        'balance_amount' => $balance_amount,
                        'admin' => $admin,
                        'created_at' => $now,
                        'updated_at' => $now,
                        'tipe_kontrak' => $tipe
                    ]);
                } else {
                    $update_tax = DB::table('tb_salary_ter_monthly')
                        ->where('periode', $dt->periode)
                        ->where('id_employee', $dt->id_employee)
                        ->where('related_table', 'tb_salary_summary')
                        ->where('category', 'payroll')
                        ->where('locked', '0')
                        ->update([
                            'id_employee' => $dt->id_employee,
                            'fiscal_year' => $fiscal_year,
                            'periode' => $periode,
                            'related_table' => 'tb_salary_summary',
                            'category' => 'payroll',
                            'previous_payment' => $prev_tax,
                            'monthly_salary' => '0',
                            'prev_amount' => $prev,
                            'current_amount' => $penghasilan_bruto_pajak,
                            'cumulative_amount' => $cum_tax,
                            'status_pajak' => $dt2->status_pajak,
                            'ter_id' => $ter_id,
                            'prosentase' => $ter,
                            'tax_amount' => $pph21_ter,
                            'balance_amount' => $balance_amount,
                            'admin' => $admin,
                            'updated_at' => $now
                        ]);
                }
                $employeeSummaries = $summariesByEmployee->get($dt->id_employee, collect());
                if ($employeeAlreadyProcessed) {
                    $periksa_summary = $this->qty_salary_summary($dt->periode, $dt->id_employee, $tipe) > 0;
                } else {
                    $periksa_summary = $employeeSummaries->contains(function ($summary) use ($tipe) {
                        if ($tipe != 0 && ($tipe == 'SAI' || $tipe == 'TKA')) {
                            return $summary->tipe_kontrak == 'SAI' || $summary->tipe_kontrak == 'TKA';
                        }

                        return $summary->tipe_kontrak == $tipe;
                    });
                }
                if (!$periksa_summary) {
                    $tambah_summary = DB::table('tb_salary_summary')->insert([
                        'periode' => $periode,
                        'tanggal_pembayaran' => $nextmonth,
                        'id_employee' => $dt->id_employee,
                        'tipe_kontrak' => $dt->tipe_kontrak,
                        'upah_pokok' => $dt2->upah_pokok,
                        'upah_harian' => $upah_harian,
                        'tunjangan_harian' => $tunjangan_harian,
                        'tunjangan_jabatan' => $dt2->tunjangan_jabatan,
                        'tunjangan_prestasi_tahunan' => $dt2->tunjangan_prestasi_tahunan,
                        'tunjangan_skill' => $dt2->tunjangan_skill,
                        'aktual_kerja' => $aktual_kerja,
                        'tunjangan_transport' => $tunjangan_transport,
                        'aktual_shift' => $aktual_shift,
                        'tunjangan_shift' => $tunjangan_shift,
                        'rapel' => $rapel,
                        'insentif_pph21' => '0',
                        'pph21_lebih_bayar' => '0',
                        'pendapatan_bruto' => $pendapatan_bruto,
                        'absen' => $absen,
                        'ketidakhadiran' => $absen_amount,
                        'standby_oncall' => '0',
                        'jaminan_hari_tua' => $dt2->jaminan_hari_tua,
                        'jaminan_pensiun' => $dt2->jaminan_pensiun,
                        'bpjs_kesehatan' => $bpjs_kesehatan,
                        'serikat' => $serikat,
                        'koperasi' => $koperasi,
                        'seragam' => '0',
                        'pants' => '0',
                        'pph21' => '0',
                        'pph21_ter' => $balance_amount,
                        'pph21_kurang_bayar' => '0',
                        'lain_lain' => '0',
                        'potongan' => $potongan,
                        'pendapatan_netto' => $pendapatan_netto,
                        'admin' => $admin,
                        'created_at' => $today,
                        'updated_at' => $now,
                        'note' => 'B'
                    ]);
                } else {
                    $tambah_summary = DB::table('tb_salary_summary')
                        ->where('periode', $dt->periode)
                        ->where('id_employee', $dt->id_employee)
                        ->where('tipe_kontrak', $dt->tipe_kontrak)
                        ->update([
                            'tanggal_pembayaran' => $nextmonth,
                            'upah_pokok' => $dt2->upah_pokok,
                            'upah_harian' => $upah_harian,
                            'tunjangan_harian' => $tunjangan_harian,
                            'tunjangan_jabatan' => $dt2->tunjangan_jabatan,
                            'tunjangan_prestasi_tahunan' => $dt2->tunjangan_prestasi_tahunan,
                            'tunjangan_skill' => $dt2->tunjangan_skill,
                            'aktual_kerja' => $aktual_kerja,
                            'tunjangan_transport' => $tunjangan_transport,
                            'aktual_shift' => $aktual_shift,
                            'tunjangan_shift' => $tunjangan_shift,
                            'rapel' => $rapel,
                            'insentif_pph21' => $insentif_pph21,
                            'pph21_lebih_bayar' => $pph21_lebih_bayar,
                            'pendapatan_bruto' => $pendapatan_bruto,
                            'absen' => $absen,
                            'ketidakhadiran' => $absen_amount,
                            'standby_oncall' => $standby_oncall,
                            'jaminan_hari_tua' => $dt2->jaminan_hari_tua,
                            'jaminan_pensiun' => $dt2->jaminan_pensiun,
                            'bpjs_kesehatan' => $bpjs_kesehatan,
                            'serikat' => $serikat,
                            'koperasi' => $koperasi,
                            'seragam' => $seragam,
                            'pants' => $pants,
                            'pph21' => '0',
                            'pph21_ter' => $balance_amount,
                            'pph21_kurang_bayar' => $pph21_kurang_bayar,
                            'lain_lain' => $lain_lain,
                            'potongan' => $potongan,
                            'pendapatan_netto' => $pendapatan_netto,
                            'admin' => $admin,
                            'created_at' => $today,
                            'updated_at' => $now
                        ]);
                }
                $processedEmployees[$dt->id_employee] = true;
            }

        }
        return redirect()->back()->with(['info' => 'Success Update']);
    }
    //simpanKontrak;
    public function updateSummaryOne($periode, $id_employee, $tipe)
    {
        //Recondition Trial 2023-05-12
        $awal = date('Y', strtotime($periode . '-01')) . '-01';
        $penghasilan_sebelumnya = DB::table('tb_salary_record')
            ->where('periode', '>=', $awal)
            ->where('periode', $periode)
            ->where('id_employee', $id_employee)
            ->where('tipe_kontrak', $tipe)
            ->sum('gaji_bruto');

        //return $periode.' '.$id_employee.' '.$tipe;
        if ($periode == 0)
            $periode = date('Y-m');
        $nextmonth = date('Y-m', strtotime('+1 months', strtotime($periode . '-01'))) . '-01';
        $admin = Auth::user()->name;
        $today = date('Y-m-d');
        $now = date('Y-m-d H:i:s');
        $tb_salary_component = $this->tb_salary_component();
        foreach ($tb_salary_component as $dt) {
            if ($dt->component == 'tunjangan_shift')
                $tunjangan_shift_a = $dt->amount;
            if ($dt->component == 'tunjangan_transport')
                $tunjangan_transport_a = $dt->amount;
            if ($dt->component == 'serikat')
                $serikat_a = $dt->amount;
            if ($dt->component == 'koperasi')
                $koperasi_a = $dt->amount;
            if ($dt->component == 'biaya_jabatan') {
                $biaya_jabatan_p = $dt->presentase;
                $biaya_jabatan_l = $dt->amount;
            }
        }

        $tb_work_entry = $this->tb_work_entry_one($periode, $id_employee, $tipe);
        foreach ($tb_work_entry as $dt) {
            $tb_salary_contract_employee = $this->tb_salary_contract_employee($dt->id_employee, $tipe);
            foreach ($tb_salary_contract_employee as $dt2) {
                //PSAB
                $upah_harian = $dt->aktual_kerja * $dt2->upah_harian;
                //PKL
                $tunjangan_harian = $dt->aktual_kerja * $dt2->tunjangan_harian;
                //penambah
                $salary = $dt2->upah_pokok + $dt2->tunjangan_jabatan + $dt2->tunjangan_prestasi_tahunan + $dt2->tunjangan_skill + $upah_harian + $tunjangan_harian;
                $tunjangan_transport = 0;
                $tunjangan_shift = 0;
                $kesehatan = $dt2->kesehatan * $dt2->status_bpjs;
                if ($dt2->tunjangan_transport == 1)
                    $tunjangan_transport = $dt->aktual_kerja * $tunjangan_transport_a;
                if ($dt2->tunjangan_shift == 1)
                    $tunjangan_shift = $dt->aktual_shift * $tunjangan_shift_a;
                $bpjs_perusahaan = $dt2->jaminan_kecelakaan_kerja + $dt2->jaminan_kematian + $kesehatan + $dt2->jaminan_hari_tua_perusahaan;
                $rapel = 0;
                $insentif_pph21 = 0;
                $pph21_lebih_bayar = 0;

                $tb_earn = $this->tb_earn_employee($periode, $dt->id_employee, $tipe);
                foreach ($tb_earn as $dt3) {
                    if ($dt3->component_name == 'rapel')
                        $rapel = $dt3->earn_amount;
                    if ($dt3->component_name == 'insentif_pph21')
                        $insentif_pph21 = $dt3->earn_amount;
                    if ($dt3->component_name == 'pph21_lebih_bayar')
                        $pph21_lebih_bayar = $dt3->earn_amount;
                }

                $penambah_pajak = $salary + $tunjangan_transport + $tunjangan_shift + $bpjs_perusahaan + $rapel;

                //pengurang
                //$absen=$dt->hari_kerja-($dt->aktual_kerja+$dt->cuti);
                if ($dt2->working_day == 6)
                    $pembagi = 25;
                else
                    $pembagi = 21;
                $absen = $dt->absen;
                $absen_amount = round($salary * ($absen / $pembagi));
                $pengurang_pajak = $dt2->jaminan_hari_tua + $dt2->jaminan_pensiun + $absen_amount;

                //balance
                $sisa_bulan = $this->sisa_bulan($periode);
                $gaji = $penambah_pajak - $pengurang_pajak;
                $penghasilan_bruto = $gaji * $sisa_bulan;

                //Recondition Trial 2023-05-12
                //$now_bruto=$penghasilan_bruto;

                //Recondition Ter 2023-05-12
                $now_bruto = $gaji;

                $penghasilan_bruto = $penghasilan_bruto + $penghasilan_sebelumnya;

                $biaya_jabatan = $biaya_jabatan_p * $penghasilan_bruto / 100;
                if ($biaya_jabatan > $biaya_jabatan_l)
                    $biaya_jabatan = $biaya_jabatan_l;
                $penghasilan_netto = $penghasilan_bruto - $biaya_jabatan;
                $pkp = $penghasilan_netto - $dt2->ptkp;

                //tax calculation
                $tb_pph21 = $this->tb_pph21();
                foreach ($tb_pph21 as $dt3) {
                    if ($dt3->id == 1) {
                        $batas1 = $dt3->batas_atas;
                        $pajak1 = $dt3->prosentase;
                    }
                    if ($dt3->id == 2) {
                        $batas2 = $dt3->batas_atas;
                        $pajak2 = $dt3->prosentase;
                    }
                    if ($dt3->id == 3) {
                        $batas3 = $dt3->batas_atas;
                        $pajak3 = $dt3->prosentase;
                    }
                    if ($dt3->id == 4) {
                        $pajak4 = $dt3->prosentase;
                    }
                }
                if ($pkp <= $batas1) {
                    $pph21 = $pkp * $pajak1 / 100;
                } else if ($pkp > $batas1 && $pkp <= $batas2) {
                    $angka1 = $batas1 * $pajak1 / 100;
                    $angka2 = ($pkp - $batas1) * $pajak2 / 100;
                    $pph21 = $angka1 + $angka2;
                } else if ($pkp > $batas2 && $pkp <= $batas3) {
                    $angka1 = $batas1 * $pajak1 / 100;
                    $angka2 = ($batas2 - $batas1) * $pajak2 / 100;
                    $angka3 = ($pkp - $batas2) * $pajak3 / 100;
                    $pph21 = $angka1 + $angka2 + $angka3;
                } else if ($pkp > $batas3) {
                    $angka1 = $batas1 * $pajak1 / 100;
                    $angka2 = ($batas2 - $batas1) * $pajak2 / 100;
                    $angka3 = ($batas3 - $batas2) * $pajak3 / 100;
                    $angka4 = ($pkp - $batas3) * $pajak4 / 100;
                    $pph21 = $angka1 + $angka2 + $angka3 + $angka4;
                }
                $sisa_bulan = $this->sisa_bulan($periode);
                $pph21 = $pph21 / 12;
                if ($dt2->status_npwp == 0)
                    $pph21_tambahan = $pph21 * 20 / 100;
                else
                    $pph21_tambahan = 0;
                $pph21 = $pph21 + $pph21_tambahan;
                if ($pph21 > 0)
                    $pph21_show = $pph21;
                else
                    $pph21_show = 0;

                //tax_conditional
                $tb_salary_tax_conditional = DB::table('tb_salary_tax_conditional')
                    ->leftjoin('tb_salary_tax', 'tb_salary_tax.id', '=', 'tb_salary_tax_conditional.id_salary_tax')
                    ->where('tb_salary_tax.periode', $periode)->where('tb_salary_tax.id_employee', $dt->id_employee)
                    ->get();
                foreach ($tb_salary_tax_conditional as $dt_tax) {
                    if ($dt_tax->status_implement == 1) {
                        $pph21_show = $dt_tax->tax_amount;
                    }
                }
                //end_tax_conditional

                //Summary Calculation
                $serikat = 0;
                $koperasi = 0;
                $standby_oncall = 0;
                $seragam = 0;
                $pants = 0;
                $pph21_kurang_bayar = 0;
                $lain_lain = 0;
                if ($dt2->serikat == 1)
                    $serikat = $serikat_a;
                if ($dt2->koperasi == 1)
                    $koperasi = $koperasi_a;
                $tb_deduction_employee = $this->tb_deduction_employee($periode, $dt->id_employee, $tipe);
                foreach ($tb_deduction_employee as $dt3) {
                    if ($dt3->component_name == 'koperasi')
                        $koperasi = $koperasi + $dt3->deduction_amount;
                    if ($dt3->component_name == 'standby_oncall')
                        $standby_oncall = $dt3->deduction_amount;
                    if ($dt3->component_name == 'seragam')
                        $seragam = $dt3->deduction_amount;
                    if ($dt3->component_name == 'pants')
                        $pants = $dt3->deduction_amount;
                    if ($dt3->component_name == 'pph21_kurang_bayar')
                        $pph21_kurang_bayar = $dt3->deduction_amount;
                    if ($dt3->component_name == 'lain_lain')
                        $lain_lain = $dt3->deduction_amount;
                }
                $bpjs_kesehatan = $dt2->bpjs_kesehatan * $dt2->status_bpjs;

                $pendapatan_bruto = $salary + $tunjangan_transport + $tunjangan_shift + $rapel + $insentif_pph21 + $pph21_lebih_bayar;
                $potongan = $dt2->jaminan_hari_tua + $dt2->jaminan_pensiun + $bpjs_kesehatan + $absen_amount + $serikat + $koperasi + $standby_oncall + $seragam + $pants + $pph21_kurang_bayar + $lain_lain + $pph21_show;
                $pendapatan_netto = $pendapatan_bruto - $potongan;

                //calculation pendapatan sebelumnya diperiode ini
                $tb_salary_record = DB::table('tb_salary_record')
                    ->select('periode', 'id_employee', 'tipe_kontrak', DB::raw('SUM(gaji_bruto) as pendapatan_sebelumnya, SUM(pph21_ter) as ter_sebelumnya'))
                    ->groupBy('periode', 'id_employee', 'tipe_kontrak')
                    ->where('periode', $dt->periode)
                    ->where('id_employee', $dt->id_employee)
                    ->where('tipe_kontrak', $tipe)
                    ->where('tanggal_pembayaran', '<', $nextmonth)
                    ->get();
                $pendapatan_sebelumnya = 0;
                $ter_sebelumnya = 0;
                foreach ($tb_salary_record as $dt4) {
                    $pendapatan_sebelumnya = $dt4->pendapatan_sebelumnya;
                    $ter_sebelumnya = $dt4->ter_sebelumnya;
                }
                $total_gaji = $gaji + $pendapatan_sebelumnya;
                //End calculation pendapatan sebelumnya diperiode ini

                //calculation pph21 with ter
                $tb_pph21_ter = $this->tb_pph21_ter($dt2->kategori, $total_gaji);
                foreach ($tb_pph21_ter as $dt3) {
                    $ter = $dt3->prosentase;
                    $pph21_ter = ($ter * $total_gaji / 100) - $ter_sebelumnya;
                }
                //End calculation PPh21 with ter

                $periksa_tax = $this->qty_salary_tax($dt->periode, $dt->id_employee, $tipe);
                if ($periksa_tax == 0) {
                    $tambah_tax = DB::table('tb_salary_tax')->insert([
                        'periode' => $periode,
                        'id_employee' => $dt->id_employee,
                        'salary' => $salary,
                        'tunjangan_transport' => $tunjangan_transport,
                        'tunjangan_shift' => $tunjangan_shift,
                        'rapel' => $rapel,
                        'jaminan_kecelakaan_kerja' => $dt2->jaminan_kecelakaan_kerja,
                        'jaminan_kematian' => $dt2->jaminan_kematian,
                        'kesehatan' => $kesehatan,
                        'jaminan_hari_tua' => $dt2->jaminan_hari_tua,
                        'jaminan_pensiun' => $dt2->jaminan_pensiun,
                        'tunjangan_hari_raya' => '0',
                        'bonus' => '0',
                        'ketidakhadiran' => $absen_amount,
                        'standby_oncall' => '0',
                        'gaji' => $gaji,
                        'pendapatan_terbayar' => $pendapatan_sebelumnya,
                        'pph21_ter_terbayar' => $ter_sebelumnya,
                        'penghasilan_bruto' => $penghasilan_bruto,
                        'penghasilan_sebelumnya' => $penghasilan_sebelumnya,
                        'biaya_jabatan' => $biaya_jabatan,
                        'penghasilan_netto' => $penghasilan_netto,
                        'status_pajak' => $dt2->statuspajak,
                        'ptkp' => $dt2->ptkp,
                        'pkp' => $pkp,
                        'pph21_salary' => $pph21,
                        'kategori' => $dt2->kategori,
                        'ter' => $ter,
                        'pph21_ter' => $pph21_ter,
                        'admin' => $admin,
                        'created_at' => $today,
                        'updated_at' => $now
                    ]);
                    $tb_salary_record = DB::table('tb_salary_record')->insert([
                        'id_employee' => $id_employee,
                        'tipe_kontrak' => $tipe,
                        'periode' => $periode,
                        'gaji_bruto' => $now_bruto,
                        'pph21_ter' => $pph21_ter,
                        'tanggal_pembayaran' => $nextmonth,
                    ]);
                } else {
                    $update_tax = DB::table('tb_salary_tax')
                        ->where('periode', $dt->periode)
                        ->where('id_employee', $dt->id_employee)
                        ->where('tipe_kontrak', $tipe)
                        ->update([
                            'salary' => $salary,
                            'tunjangan_transport' => $tunjangan_transport,
                            'tunjangan_shift' => $tunjangan_shift,
                            'rapel' => $rapel,
                            'jaminan_kecelakaan_kerja' => $dt2->jaminan_kecelakaan_kerja,
                            'jaminan_kematian' => $dt2->jaminan_kematian,
                            'kesehatan' => $kesehatan,
                            'jaminan_hari_tua' => $dt2->jaminan_hari_tua,
                            'jaminan_pensiun' => $dt2->jaminan_pensiun,
                            'tunjangan_hari_raya' => '0',
                            'bonus' => '0',
                            'ketidakhadiran' => $absen_amount,
                            'standby_oncall' => '0',
                            'gaji' => $gaji,
                            'pendapatan_terbayar' => $pendapatan_sebelumnya,
                            'pph21_ter_terbayar' => $ter_sebelumnya,
                            'penghasilan_bruto' => $penghasilan_bruto,
                            'penghasilan_sebelumnya' => $penghasilan_sebelumnya,
                            'biaya_jabatan' => $biaya_jabatan,
                            'penghasilan_netto' => $penghasilan_netto,
                            'status_pajak' => $dt2->statuspajak,
                            'ptkp' => $dt2->ptkp,
                            'pkp' => $pkp,
                            'pph21_salary' => $pph21,
                            'kategori' => $dt2->kategori,
                            'ter' => $ter,
                            'pph21_ter' => $pph21_ter,
                            'admin' => $admin,
                            'created_at' => $today,
                            'updated_at' => $now
                        ]);
                    $tb_salary_record = DB::table('tb_salary_record')
                        ->where('periode', $periode)
                        ->where('id_employee', $id_employee)
                        ->where('tipe_kontrak', $tipe)
                        ->where('tanggal_pembayaran', $nextmonth)
                        ->update([
                            'gaji_bruto' => $now_bruto,
                            'pph21_ter' => $pph21_ter,
                            'tanggal_pembayaran' => $nextmonth,
                        ]);
                }
                $periksa_summary = $this->qty_salary_summary($dt->periode, $dt->id_employee, $tipe);
                if ($periksa_summary == 0) {
                    $tambah_summary = DB::table('tb_salary_summary')->insert([
                        'periode' => $periode,
                        'tanggal_pembayaran' => $nextmonth,
                        'id_employee' => $dt->id_employee,
                        'upah_pokok' => $dt2->upah_pokok,
                        'upah_harian' => $upah_harian,
                        'tunjangan_harian' => $tunjangan_harian,
                        'tunjangan_jabatan' => $dt2->tunjangan_jabatan,
                        'tunjangan_prestasi_tahunan' => $dt2->tunjangan_prestasi_tahunan,
                        'tunjangan_skill' => $dt2->tunjangan_skill,
                        'aktual_kerja' => $dt->aktual_kerja,
                        'tunjangan_transport' => $tunjangan_transport,
                        'aktual_shift' => $dt->aktual_shift,
                        'tunjangan_shift' => $tunjangan_shift,
                        'rapel' => $rapel,
                        'insentif_pph21' => '0',
                        'pph21_lebih_bayar' => '0',
                        'pendapatan_bruto' => $pendapatan_bruto,
                        'absen' => $absen,
                        'ketidakhadiran' => $absen_amount,
                        'standby_oncall' => '0',
                        'jaminan_hari_tua' => $dt2->jaminan_hari_tua,
                        'jaminan_pensiun' => $dt2->jaminan_pensiun,
                        'bpjs_kesehatan' => $bpjs_kesehatan,
                        'serikat' => $serikat,
                        'koperasi' => $koperasi,
                        'seragam' => '0',
                        'pants' => '0',
                        'pph21' => $pph21_show,
                        'pph21_ter' => $pph21_ter,
                        'pph21_kurang_bayar' => '0',
                        'lain_lain' => '0',
                        'potongan' => $potongan,
                        'pendapatan_netto' => $pendapatan_netto,
                        'admin' => $admin,
                        'created_at' => $today,
                        'updated_at' => $now,
                        'note' => 'C'
                    ]);
                } else {
                    $tambah_summary = DB::table('tb_salary_summary')
                        ->where('periode', $dt->periode)
                        ->where('id_employee', $dt->id_employee)
                        ->where('tipe_kontrak', $tipe)
                        ->update([
                            'tanggal_pembayaran' => $nextmonth,
                            'upah_pokok' => $dt2->upah_pokok,
                            'upah_harian' => $upah_harian,
                            'tunjangan_harian' => $tunjangan_harian,
                            'tunjangan_jabatan' => $dt2->tunjangan_jabatan,
                            'tunjangan_prestasi_tahunan' => $dt2->tunjangan_prestasi_tahunan,
                            'tunjangan_skill' => $dt2->tunjangan_skill,
                            'aktual_kerja' => $dt->aktual_kerja,
                            'tunjangan_transport' => $tunjangan_transport,
                            'aktual_shift' => $dt->aktual_shift,
                            'tunjangan_shift' => $tunjangan_shift,
                            'rapel' => $rapel,
                            'insentif_pph21' => $insentif_pph21,
                            'pph21_lebih_bayar' => $pph21_lebih_bayar,
                            'pendapatan_bruto' => $pendapatan_bruto,
                            'absen' => $absen,
                            'ketidakhadiran' => $absen_amount,
                            'standby_oncall' => $standby_oncall,
                            'jaminan_hari_tua' => $dt2->jaminan_hari_tua,
                            'jaminan_pensiun' => $dt2->jaminan_pensiun,
                            'bpjs_kesehatan' => $bpjs_kesehatan,
                            'serikat' => $serikat,
                            'koperasi' => $koperasi,
                            'seragam' => $seragam,
                            'pants' => $pants,
                            'pph21' => $pph21_show,
                            'pph21_ter' => $pph21_ter,
                            'pph21_kurang_bayar' => $pph21_kurang_bayar,
                            'lain_lain' => $lain_lain,
                            'potongan' => $potongan,
                            'pendapatan_netto' => $pendapatan_netto,
                            'admin' => $admin,
                            'created_at' => $today,
                            'updated_at' => $now
                        ]);
                }

            }
        }
        //return redirect()->back()->with(['info'=>'Success Update']);
    }
    //updateSummary;
    public function tb_salary_contract_employee($id_employee, $tipe)
    {
        $tabel = DB::table('v_salary_contract as tb_salary_contract')
            ->leftjoin('tb_pph21_status', 'tb_pph21_status.id', '=', 'tb_salary_contract.status_pajak')
            ->where('id_employee', $id_employee);
        // ->where('tipe_kontrak',$tipe)
        if ($tipe != 0 && ($tipe == 'SAI' || $tipe == 'TKA')) {
            $tabel = $tabel->where(function ($query) use ($tipe) {
                $query->where('tb_salary_contract.tipe_kontrak', 'SAI')
                    ->orWhere('tb_salary_contract.tipe_kontrak', 'TKA');
            });
        } else {
            $tabel = $tabel->where('tb_salary_contract.tipe_kontrak', $tipe);
        }
        $tabel = $tabel->get(['tb_salary_contract.*', 'tb_pph21_status.status_pajak as statuspajak', 'tb_pph21_status.ptkp', 'tb_pph21_status.kategori']);
        return $tabel;
    }
    //updateSummary;
    public function tb_earn_employee($periode, $id_employee, $tipe)
    {
        $tabel = DB::table('tb_salary_earn')->where('periode', $periode)->where('id_employee', $id_employee)->where('tipe_kontrak', $tipe)->get();
        return $tabel;
    }
    //updateSummary;
    public function tb_deduction_employee($periode, $id_employee, $tipe)
    {
        $tabel = DB::table('tb_salary_deduction')->where('periode', $periode)->where('id_employee', $id_employee)->where('tipe_kontrak', $tipe)->get();
        return $tabel;
    }
    //updateSummary; tax;
    public function tb_pph21()
    {
        $tabel = DB::table('tb_pph21')->orderby('id', 'asc')->get();
        return $tabel;
    }
    //info: update ter
    public function tb_pph21_ter($kategori, $bruto)
    {
        $tabel = DB::table('tb_pph21_ter')->where('ter', $kategori)->where('batas_bawah', '<', $bruto)->where('batas_atas', '>=', $bruto)->get();
        return $tabel;
    }
    //updateSummary
    public function qty_salary_tax($periode, $id_employee, $tipe)
    {
        $tabel = DB::table('tb_salary_tax')
            ->where('periode', $periode)
            ->where('id_employee', $id_employee)
            ->where('tipe_kontrak', $tipe)
            ->count();
        return $tabel;
    }
    public function qty_salary_tax_ter($periode, $id_employee, $related, $category)
    {
        $tabel = DB::table('tb_salary_ter_monthly')
            ->where('periode', $periode)
            ->where('id_employee', $id_employee)
            ->where('related_table', $related)
            ->where('category', $category)
            ->count();
        return $tabel;
    }
    public function prev_salary_tax_ter($periode, $id_employee, $related, $category)
    {
        $tb1 = DB::table('tb_salary_ter_monthly')
            ->where('periode', $periode)
            ->where('id_employee', $id_employee)
            ->where('related_table', $related)
            ->where('category', $category)
            ->value('created_at');

        if (!$tb1) {
            $tb1 = date('Y-m-d H:i:s');
        }

        $tabel = DB::table('tb_salary_ter_monthly')
            ->where('periode', $periode)
            ->where('id_employee', $id_employee)
            ->where('created_at', '<', $tb1)
            ->get();
        $total_tax_amount = $tabel->sum('tax_amount');
        return $total_tax_amount;
    }
    public function cum_salary_tax_ter($periode, $id_employee, $related, $category)
    {
        $tb1 = DB::table('tb_salary_ter_monthly')
            ->where('periode', $periode)
            ->where('id_employee', $id_employee)
            ->where('related_table', $related)
            ->where('category', $category)
            ->value('created_at');

        if (!$tb1) {
            $tb1 = date('Y-m-d H:i:s');
        }

        $tabel = DB::table('tb_salary_ter_monthly')
            ->where('periode', $periode)
            ->where('id_employee', $id_employee)
            ->where('created_at', '<', $tb1)
            ->get();
        $total_tax_amount = $tabel->sum('current_amount');
        return $total_tax_amount;
    }
    //updateSummary
    public function qty_salary_summary($periode, $id_employee, $tipe)
    {
        $tabel = DB::table('tb_salary_summary')
            ->where('periode', $periode)
            ->where('id_employee', $id_employee);
        // ->where('tipe_kontrak',$tipe)
        if ($tipe != 0 && ($tipe == 'SAI' || $tipe == 'TKA')) {
            $tabel = $tabel->where(function ($query) use ($tipe) {
                $query->where('tb_salary_summary.tipe_kontrak', 'SAI')
                    ->orWhere('tb_salary_summary.tipe_kontrak', 'TKA');
            });
        } else {
            $tabel = $tabel->where('tb_salary_summary.tipe_kontrak', $tipe);
        }
        $tabel = $tabel->count();
        return $tabel;
    }

    //slip;
    public function tb_salary_summary_employee($periode, $id_employee)
    {
        $tabel = DB::table('tb_salary_summary')
            ->leftjoin('tb_salary_contract', 'tb_salary_contract.id_employee', '=', 'tb_salary_summary.id_employee');
        if ($periode != '0')
            $tabel = $tabel->where('periode', $periode)->whereNotNull('tb_salary_summary.tipe_kontrak');
        $tabel = $tabel->where('tb_salary_summary.id_employee', $id_employee)
            ->get(['tb_salary_summary.*', 'tb_salary_contract.NIK', 'tb_salary_contract.nama_karyawan', 'tb_salary_contract.department as dept_code', 'tb_salary_contract.jabatan as nama_jabatan', 'tb_salary_contract.status_pajak', 'tb_salary_contract.total_salary', 'tb_salary_contract.tipe_kontrak', 'tb_salary_contract.upah_harian as upah_psab', 'tb_salary_contract.tunjangan_harian as tunjangan_pkl']);
        return $tabel;
    }

    //tax; management;
    public function tb_salary_tax($periode, $department, $tipe)
    {
        $tabel = DB::table('tb_salary_tax')
            ->join('tb_salary_contract', function ($join) {
                $join->on('tb_salary_contract.id_employee', '=', 'tb_salary_tax.id_employee')
                    ->on('tb_salary_contract.tipe_kontrak', '=', 'tb_salary_tax.tipe_kontrak');
            })
            ->leftjoin('tb_pph21_status', 'tb_pph21_status.id', '=', 'tb_salary_contract.status_pajak')
            ->leftjoin('tb_salary_tax_conditional', 'tb_salary_tax_conditional.id_salary_tax', '=', 'tb_salary_tax.id')
            ->where('tb_salary_tax.periode', $periode)
            ->where('tb_salary_tax.tipe_kontrak', $tipe);
        if (request()->user()->hasRole('root')) {
            $tabel = $tabel->where('tb_salary_contract.status', '1');
        } elseif (request()->user()->hasRole('payroll')) {
            $tabel = $tabel->where('tb_salary_contract.status', '1')->where('tb_salary_contract.status_management', '0');
        } elseif (request()->user()->hasRole('payroll_management')) {
            $tabel = $tabel->where('tb_salary_contract.status', '1')->where('tb_salary_contract.status_management', '1');
        }
        if ($department != 0) {
            $tabel = $tabel->where('tb_salary_contract.department', $department);
        }
        $tabel = $tabel->get(['tb_salary_tax.*', 'tb_pph21_status.status_pajak as statuspajak', 'tb_salary_contract.NIK', 'tb_salary_contract.nama_karyawan', 'tb_salary_contract.department as dept_code', 'tb_salary_contract.jabatan', 'tb_salary_contract.status_pajak', 'tb_salary_contract.total_salary', 'tb_salary_contract.tipe_kontrak', 'tb_salary_tax_conditional.id as idconditional', 'tb_salary_tax_conditional.tax_amount', 'tb_salary_tax_conditional.status_implement']);
        return $tabel;
    }

    //workentry; management;
    public function tb_employee_workentry($periode, $department, $tipe)
    {
        $tb_work_entry = DB::table('tb_work_entry')->where('periode', $periode)->get();
        $tabel = DB::table('tb_salary_contract');
        // foreach($tb_work_entry as $dt){
        //     $tabel=$tabel->where('tb_salary_contract.id_employee','<>',$dt->id_employee);
        // }
        $tabel = $tabel->get();
        return $tabel;
    }
    //index
    public function reconcile_ems()
    {
        $sekarang = date('Y-m-d H:i:s');
        $v_salary_contract = DB::table('v_salary_contract')->get();
        foreach ($v_salary_contract as $dt) {
            $tb_salary_contract = DB::table('tb_salary_contract')->where('NIK', $dt->NIK)->get();
            foreach ($tb_salary_contract as $dt2) {
                if ($dt2->nama_karyawan != $dt->employee_name || $dt2->department != $dt->dept_code || $dt2->jabatan != $dt->position_name || $dt2->tanggal_kontrak != $dt->join_date) {
                    $update = DB::table('tb_salary_contract')->where('id', $dt->id)->update([
                        'nama_karyawan' => $dt->employee_name,
                        'department' => $dt->dept_code,
                        'jabatan' => $dt->position_name,
                        'tanggal_kontrak' => $dt->join_date,
                        'updated_at' => $sekarang
                    ]);
                }
            }
        }
        return $v_salary_contract;
    }
    //summary
    public function resetSummary($periode, $tipe)
    {
        $reset_summary = DB::table('tb_salary_summary')->where('periode', $periode)->where('tipe_kontrak', $tipe)->delete();
        $reset_tax = DB::table('tb_salary_ter_monthly')->where('periode', $periode)->where('tipe_kontrak', $tipe)->delete();
        $reset_record = DB::table('tb_salary_record')->where('periode', $periode)->where('tipe_kontrak', $tipe)->delete();
        if ($tipe == 'SAI') {
            $reset_summary2 = DB::table('tb_salary_summary')->where('periode', $periode)->where('tipe_kontrak', 'TKA')->delete();
            $reset_tax2 = DB::table('tb_salary_ter_monthly')->where('periode', $periode)->where('tipe_kontrak', 'TKA')->delete();
            $reset_record2 = DB::table('tb_salary_record')->where('periode', $periode)->where('tipe_kontrak', 'TKA')->delete();
        }
        if ($reset_summary && $reset_tax)
            return redirect()->back()->with(['info' => 'Summary has been reset']);
    }
    //Collect Overtime
    public function summaryOvertime($periode)
    {
        $kalendar = CAL_GREGORIAN;
        $admin = Auth::user()->name;
        $sekarang = date('Y-m-d H:i:s');
        $thn = date('Y', strtotime($periode . '-01'));
        $bln = date('m', strtotime($periode . '-01'));
        $hariakhir = cal_days_in_month($kalendar, $bln, $thn);
        $pembayaran = date('Y-m-d', strtotime('+15 days', strtotime($periode . '-' . $hariakhir)));
        $cutoff = $periode . '-01';
        $tb_summary_ot = DB::table('tb_summary_overtime')->where('periode', $periode)->get();
        $tb_salary_earn_additional = DB::table('tb_salary_earn_additional')->where('periode', $periode)->where('component_name', 'overtime')->delete();
        foreach ($tb_summary_ot as $dt) {
            if (substr($dt->NIK, 0, 2) == 'MG')
                $tipe = 'MAGANG';
            else
                $tipe = 'SAI';
            $earn_amount = $dt->rapel + $dt->pph21_insentive + $dt->total_bayars;
            $add = DB::table('tb_salary_earn_additional')->insert([
                'periode' => $periode,
                'tanggal_cutoff' => $cutoff,
                'tanggal_pembayaran' => $pembayaran,
                'id_employee' => $dt->id_employee,
                'tipe_kontrak' => $tipe,
                'component_name' => 'overtime',
                'earn_amount' => $earn_amount,
                'pph21' => '0',
                'amount_received' => $earn_amount,
                'mail_status' => '0',
                'admin' => $admin,
                'created_at' => $sekarang,
                'updated_at' => $sekarang
            ]);
        }
        if ($tb_summary_ot)
            return redirect()->back()->with(['info' => 'Success']);
    }
    public function sisa_bulan($periode)
    {
        $bulan = substr($periode, 5, 2);
        $sisa_bulan = 12 + 1 - $bulan;
        return $sisa_bulan;
    }
    public function compensationupdate($periode)
    {
        if ($periode == 0) {
            $periode = date('Y-m');
        }
        $end = date('Y-m-d', strtotime($periode . '-24'));
        $awal_bulan = date('Y-m-d', strtotime($periode . '-01'));
        $akhir_bulan = date('Y-m', strtotime('-1 days', strtotime($awal_bulan)));
        $start = date('Y-m-d', strtotime($akhir_bulan . '-25'));

        $tb_kompensasi = DB::table('tb_kompensasi')->where('Finish_Kontrak', '>=', $start)->where('Finish_Kontrak', '<=', $end)->where('isDelete', '0')->where('send_mail', '0')->get();
        foreach ($tb_kompensasi as $dt) {
            $tb_salary_contract = DB::table('tb_salary_contract')->where('id_employee', $dt->id_employee)->where('tipe_kontrak', 'SAI')->get();
            foreach ($tb_salary_contract as $dt2) {
                $kompensasi = $dt2->total_salary * $dt->Jumlah_Bulan / 12;
                $update = DB::table('tb_kompensasi')->where('id', $dt->id)->update([
                    'Upah_Pokok' => $dt2->upah_pokok,
                    'Tunjangan_Jabatan' => $dt2->tunjangan_jabatan,
                    'Tunjangan_Prestasi' => $dt2->tunjangan_prestasi_tahunan,
                    'Tunjangan_Skill' => $dt2->tunjangan_skill,
                    'Total_Pendapatan' => $dt2->total_salary,
                    'Kompensasi' => $kompensasi,
                ]);
            }
        }
        return redirect()->back()->with(['info' => 'Success']);
    }
    function dpkslpj()
    {
        $periode = date('Y-m');
        $tb_employee = DB::table('tb_employees')
            ->leftjoin('tb_departments', 'tb_departments.id', '=', 'tb_employees.dept_id')
            ->where('position_id', '29')->where('status', '1')->where('delete', '0')
            ->get(['tb_employees.*', 'tb_departments.dept_code']);
        $tb_salaries = DB::table('tb_salaries')
            ->leftjoin('tb_employees', 'tb_employees.id', '=', 'tb_salaries.id_employee')
            ->leftjoin('tb_departments', 'tb_departments.id', '=', 'tb_employees.dept_id')
            ->where('tb_employees.position_id', '29')
            ->get(['tb_salaries.*', 'tb_employees.NIK', 'tb_employees.employee_name', 'tb_departments.dept_code']);
        if (request()->user()->hasRole('root') || request()->user()->hasRole('payroll') || request()->user()->hasRole('payroll_management')) {
            //return "Masuk";
            return view('page/payroll/dpk_slpj', ['tb_employee' => $tb_employee, 'tb_salaries' => $tb_salaries, 'periode' => '', 'site' => $this->site, 'status' => '1', 'menu' => 'payroll', 'juduls' => 'DPK SLPJ']);
        } else {
            return abort(403, 'Anda tidak punya akses');
        }
    }
    function dpkslpj_simpan(Request $data)
    {
        $cek = DB::table('tb_salaries')->where('id_employee', $data->idemployee)->count();
        if ($cek == 0) {
            $simpan = DB::table('tb_salaries')->insert([
                'id_employee' => $data->idemployee,
                'slpj' => $data->slpj
            ]);
        } else {
            $simpan = DB::table('tb_salaries')->where('id_employee', $data->idemployee)->update([
                'slpj' => $data->slpj
            ]);
        }
        if ($simpan)
            return 'Sukses';
        else
            return 'Gagal';
    }
    function newKontrak($id)
    {
        $admin = Auth::user()->name;
        $today = date('Y-m-d');
        $now = date('Y-m-d H:i:s');
        $tb = DB::table('tb_salary_contract')->where('id', $id)->get();
        foreach ($tb as $data) {
            $simpan = DB::table('tb_salary_contract')->insert([
                'id_employee' => $data->id_employee,
                'NIK' => $data->NIK,
                'nama_karyawan' => $data->nama_karyawan,
                'department' => $data->department,
                'cc_code' => $data->cc_code,
                'cc_name' => $data->cc_name,
                'jabatan' => $data->jabatan,
                'tanggal_kontrak' => $data->tanggal_expired,
                'tanggal_expired' => null,
                'tipe_kontrak' => $data->tipe_kontrak,
                'upah_pokok' => $data->upah_pokok,
                'prosentase_upah' => $data->prosentase_upah,
                'upah_harian' => $data->upah_harian,
                'tunjangan_harian' => $data->tunjangan_harian,
                'tunjangan_jabatan' => $data->tunjangan_jabatan,
                'tunjangan_prestasi_tahunan' => $data->tunjangan_prestasi_tahunan,
                'tunjangan_skill' => $data->tunjangan_skill,
                'tunjangan_makan' => $data->tunjangan_makan,
                'tunjangan_transport' => $data->tunjangan_transport,
                'tunjangan_shift' => $data->tunjangan_shift,
                'total_salary' => $data->total_salary,
                'bpjs_salary' => $data->bpjs_salary,
                'slpj' => $data->slpj,
                'jaminan_hari_tua' => $data->jaminan_hari_tua,
                'jaminan_hari_tua_perusahaan' => $data->jaminan_hari_tua_perusahaan,
                'jaminan_pensiun' => $data->jaminan_pensiun,
                'bpjs_tambahan' => $data->bpjs_tambahan,
                'bpjs_kesehatan' => $data->bpjs_kesehatan,
                'prosentase_bpjs' => $data->prosentase_bpjs,
                'serikat' => $data->serikat,
                'koperasi' => $data->koperasi,
                'status_pajak' => $data->status_pajak,
                'jaminan_kecelakaan_kerja' => $data->jaminan_kecelakaan_kerja,
                'jaminan_kematian' => $data->jaminan_kematian,
                'kesehatan' => $data->kesehatan,
                'status_npwp' => $data->status_npwp,
                'status_rekening' => $data->status_rekening,
                'status_bpjs' => $data->status_bpjs,
                'status_management' => '0',
                'working_day' => $data->working_day,
                'status' => 1,
                'admin' => $admin,
                'created_at' => $today,
                'updated_at' => $now
            ]);
            if ($simpan) {
                $update = DB::table('tb_salary_contract')->where('id', $id)->update(['status' => '0']);
            }
        }
        return redirect()->back();
    }
    public function ter_calculation($tabel, $kategori, $periode)
    {
        $end = date('Y-m-d', strtotime($periode . '-24'));
        $awal_bulan = date('Y-m-d', strtotime($periode . '-01'));
        $akhir_bulan = date('Y-m', strtotime('-1 days', strtotime($awal_bulan)));
        $start = date('Y-m-d', strtotime($akhir_bulan . '-25'));

        $fiscal_year = date('Y', strtotime($periode . '-01'));
        $admin = Auth::check() ? Auth::user()->name : 'System';
        $now = date('Y-m-d H:i:s');

        $data = DB::table($tabel)->where('Finish_Kontrak', '>=', $start)->where('Finish_Kontrak', '<=', $end)->where('isDelete', '0')->get();
        foreach ($data as $row) {
            //MyScript
            $tb_salary_contract_employee = $this->tb_salary_contract_employee($row->id_employee, 'SAI');
            // $category = '';
            // $status_pajak = '';
            foreach ($tb_salary_contract_employee as $dt) {
                $category = $dt->kategori;
                $status_pajak = $dt->status_pajak;
            }
            $penghasilan_bruto_pajak = $row->Kompensasi;
            $cum = $this->cum_salary_tax_ter($periode, $row->id_employee, $tabel, $kategori);
            $cum_tax = $cum + $penghasilan_bruto_pajak;
            $prev = $cum_tax - $penghasilan_bruto_pajak;
            $tb_pph21_ter = $this->tb_pph21_ter($category, $cum_tax);
            // $ter_id = 0;
            // $ter = 0;
            // $pph21_ter = 0;
            foreach ($tb_pph21_ter as $dt2) {
                $ter_id = $dt2->id;
                $ter = $dt2->prosentase;
                $pph21_ter = ($ter * $cum_tax / 100);
            }
            $periksa_tax = $this->qty_salary_tax_ter($periode, $row->id_employee, $tabel, $kategori);
            $prev_tax = $this->prev_salary_tax_ter($periode, $row->id_employee, $tabel, $kategori);
            $balance_amount = $pph21_ter - $prev_tax;
            //end tax calculation
            if ($periksa_tax == 0) {
                $lock = DB::table('tb_salary_ter_monthly')->where('periode', $periode)->where('id_employee', $row->id_employee)->update(['locked' => '1']);
                $update_tax = DB::table('tb_salary_ter_monthly')->insert([
                    'id_employee' => $row->id_employee,
                    'fiscal_year' => $fiscal_year,
                    'periode' => $periode,
                    'related_table' => $tabel,
                    'category' => $kategori,
                    'previous_payment' => $prev_tax,
                    'monthly_salary' => '0',
                    'prev_amount' => $prev,
                    'current_amount' => $penghasilan_bruto_pajak,
                    'cumulative_amount' => $cum_tax,
                    'status_pajak' => $status_pajak,
                    'ter_id' => $ter_id,
                    'prosentase' => $ter,
                    'tax_amount' => $pph21_ter,
                    'balance_amount' => $balance_amount,
                    'admin' => $admin,
                    'created_at' => $now,
                    'updated_at' => $now,
                    'tipe_kontrak' => $row->tipe_kontrak
                ]);
            } else {
                $update_tax = DB::table('tb_salary_ter_monthly')
                    ->where('periode', $periode)
                    ->where('id_employee', $row->id_employee)
                    ->where('related_table', $tabel)
                    ->where('category', $kategori)
                    ->where('locked', '0')
                    ->update([
                        'id_employee' => $row->id_employee,
                        'fiscal_year' => $fiscal_year,
                        'periode' => $periode,
                        'related_table' => 'tb_salary_earn_additional',
                        'category' => 'overtime',
                        'previous_payment' => $prev_tax,
                        'prev_amount' => $prev,
                        'current_amount' => $penghasilan_bruto_pajak,
                        'cumulative_amount' => $cum_tax,
                        'status_pajak' => $status_pajak,
                        'ter_id' => $ter_id,
                        'prosentase' => $ter,
                        'tax_amount' => $pph21_ter,
                        'balance_amount' => $balance_amount,
                        'admin' => $admin,
                        'updated_at' => $now
                    ]);
            }
            //EndMyScrip

            if ($update_tax) {
                $paid = $row->Kompensasi - $balance_amount;
                DB::table('tb_kompensasi')->where('id', $row->id)->update([
                    'PPH21' => $balance_amount,
                    'Total_Kompensasi' => $paid,
                    'updated_at' => now(),
                ]);
            }
        }

    }
    public function ppha21_pesangon($periode)
    {
        $end = date('Y-m-d', strtotime($periode . '-24'));
        $awal_bulan = date('Y-m-d', strtotime($periode . '-01'));
        $akhir_bulan = date('Y-m', strtotime('-1 days', strtotime($awal_bulan)));
        $start = date('Y-m-d', strtotime($akhir_bulan . '-25'));

        $fiscal_year = date('Y', strtotime($periode . '-01'));
        $admin = Auth::check() ? Auth::user()->name : 'System';
        $now = date('Y-m-d H:i:s');

        $data = DB::table('tb_kompensasi')->where('Finish_Kontrak', '>=', $start)->where('Finish_Kontrak', '<=', $end)->where('isDelete', '0')->get();
        foreach ($data as $row) {
            $tb1 = DB::table('tb_pph21_pesangon')->orderby('id', 'asc')->get();
            $no = 0;
            foreach ($tb1 as $row1) {
                $no++;
                $batas[$no] = $row1->batas_atas;
                $prosentase[$no] = $row1->prosentase;
            }
            $nilai_total = $row->Kompensasi;
            if ($nilai_total > $batas[3]) {
                $val4 = $nilai_total - $batas[3];
                $pph_4 = $val4 * $prosentase[4] / 100;
                $pph_3 = ($batas[3] - $batas[2]) * $prosentase[3] / 100;
                $pph_2 = ($batas[2] - $batas[1]) * $prosentase[2] / 100;
                $pph_1 = $batas[1] * $prosentase[1] / 100;
                $pph21 = $pph_1 + $pph_2 + $pph_3 + $pph_4;
            } else if ($nilai_total > $batas[2]) {
                $val3 = $nilai_total - $batas[2];
                $pph_3 = $val3 * $prosentase[3] / 100;
                $pph_2 = ($batas[2] - $batas[1]) * $prosentase[2] / 100;
                $pph_1 = $batas[1] * $prosentase[1] / 100;
                $pph21 = $pph_1 + $pph_2 + $pph_3;
            } else if ($nilai_total > $batas[1]) {
                $val2 = $nilai_total - $batas[1];
                $pph_2 = $val2 * $prosentase[2] / 100;
                $pph_1 = $batas[1] * $prosentase[1] / 100;
                $pph21 = $pph_1 + $pph_2;
            } else {
                $pph_1 = $nilai_total * $prosentase[1] / 100;
                $pph21 = $pph_1;
            }
            $paid = $row->Kompensasi - $pph21;

            DB::table('tb_kompensasi')->where('id', $row->id)->update([
                'PPH21' => $pph21,
                'Total_Kompensasi' => $paid,
                'updated_at' => now(),
            ]);
        }

    }

    public function permanentList($cutoff)
    {
        if ($cutoff == 0) {
            $cutoff = date('Y-m-d');
        }
        $periode = date('Y-m', strtotime($cutoff));
        $tb_pesangon = DB::table('v_permanent_employee as v')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('tb_salary_pesangon as s')
                    ->whereColumn('s.id_employee', 'v.id');
            })
            ->get();

        return view('page/payroll/permanent_list', [
            'tb_pesangon' => $tb_pesangon,
            'cutoff' => $cutoff,
            'periode' => $periode,
            'site' => $this->site,
            'menu' => 'payroll_other',
            'juduls' => 'Pesangon'
        ]);
    }

    public function capture_pesangon(Request $data)
    {
        $admin = Auth::check() ? Auth::user()->name : 'System';
        $now = date('Y-m-d H:i:s');
        $items = $data->items;

        $tb_pajak = DB::table('tb_pph21_pesangon')->orderby('id', 'asc')->get();
        $brackets = [];
        foreach ($tb_pajak as $row) {
            $brackets[] = [
                'limit' => (float) $row->batas_atas,
                'rate' => (float) $row->prosentase
            ];
        }

        foreach ($items as $item) {
            $amount = (float) $item['amount'];
            $pph21 = 0;
            $nilai_total = $amount;

            // Tax calculation logic from ppha21_pesangon
            $batas1 = $brackets[0]['limit'];
            $pro1 = $brackets[0]['rate'];
            $batas2 = $brackets[1]['limit'];
            $pro2 = $brackets[1]['rate'];
            $batas3 = $brackets[2]['limit'];
            $pro3 = $brackets[2]['rate'];
            $pro4 = $brackets[3]['rate'];

            if ($nilai_total > $batas3) {
                $val4 = $nilai_total - $batas3;
                $pph_4 = $val4 * $pro4 / 100;
                $pph_3 = ($batas3 - $batas2) * $pro3 / 100;
                $pph_2 = ($batas2 - $batas1) * $pro2 / 100;
                $pph_1 = $batas1 * $pro1 / 100;
                $pph21 = $pph_1 + $pph_2 + $pph_3 + $pph_4;
            } else if ($nilai_total > $batas2) {
                $val3 = $nilai_total - $batas2;
                $pph_3 = $val3 * $pro3 / 100;
                $pph_2 = ($batas2 - $batas1) * $pro2 / 100;
                $pph_1 = $batas1 * $pro1 / 100;
                $pph21 = $pph_1 + $pph_2 + $pph_3;
            } else if ($nilai_total > $batas1) {
                $val2 = $nilai_total - $batas1;
                $pph_2 = $val2 * $pro2 / 100;
                $pph_1 = $batas1 * $pro1 / 100;
                $pph21 = $pph_1 + $pph_2;
            } else {
                $pph_1 = $nilai_total * $pro1 / 100;
                $pph21 = $pph_1;
            }

            $amount_paid = $amount - $pph21;
            $cutoff = $item['cutoff'];

            $d1 = new \DateTime($item['date_permanent']);
            $d2 = new \DateTime($cutoff);
            $diff = $d1->diff($d2);
            $years = $diff->y;
            $months = $diff->m;

            if ($d1 > $d2) {
                $years = 0;
                $months = 0;
            }

            DB::table('tb_salary_pesangon')->insert([
                'id_employee' => $item['id_employee'],
                'date_permanent' => $item['date_permanent'],
                'date_cutoff' => $cutoff,
                'tahun' => $years,
                'bulan' => $months,
                'total_salary' => $item['total_salary'],
                'amount' => $amount,
                'pph21' => $pph21,
                'amount_paid' => $amount_paid,
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }

        return response()->json(['status' => 'success']);
    }

    public function delete_pesangon($id)
    {
        DB::table('tb_salary_pesangon')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Data deleted successfully');
    }

    public function distribute_pesangon($id)
    {
        DB::table('tb_salary_pesangon')->where('id', $id)->update(['distributed' => '1', 'updated_at' => date('Y-m-d H:i:s')]);
        return redirect()->back()->with('success', 'Data distributed successfully');
    }

    public function pesangon($status)
    {
        $tb_pesangon = DB::table('tb_salary_pesangon as s')
            ->leftJoin('tb_salary_contract as c', 's.id_employee', '=', 'c.id_employee')
            ->where('s.distributed', $status)
            ->select('s.*', 'c.nama_karyawan as employee_name', 'c.nik', 'c.department')
            ->get();

        return view('page/payroll/pesangon_list', [
            'tb_pesangon' => $tb_pesangon,
            'periode' => '0',
            'status' => $status,
            'site' => $this->site,
            'menu' => 'payroll_other',
            'juduls' => 'Severance payment'
        ]);
    }

    public function resignation($status)
    {
        $tb_resignation = DB::table('tb_salary_resignation as s')
            ->leftJoin('tb_salary_contract as c', 's.id_employee', '=', 'c.id_employee')
            ->where('s.distributed', $status)
            ->select('s.*', 'c.nama_karyawan as employee_name', 'c.nik', 'c.department')
            ->get();

        return view('page/payroll/resignation_list', [
            'tb_resignation' => $tb_resignation,
            'periode' => '0',
            'status' => $status,
            'site' => $this->site,
            'menu' => 'payroll_other',
            'juduls' => 'Resignation'
        ]);
    }

    public function delete_resignation($id)
    {
        DB::table('tb_salary_resignation')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Data deleted successfully');
    }

    public function distribute_resignation($id)
    {
        DB::table('tb_salary_resignation')->where('id', $id)->update(['distributed' => '1', 'updated_at' => date('Y-m-d H:i:s')]);
        return redirect()->back()->with('success', 'Data distributed successfully');
    }

    public function resignation_permanent_list($cutoff)
    {
        if ($cutoff == '0') {
            $cutoff = date('Y-m-d');
        }
        $tb_resignation = DB::table('v_permanent_employee as v')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('tb_salary_resignation as s')
                    ->whereColumn('s.id_employee', 'v.id');
            })
            ->get(['v.id', 'v.NIK', 'v.employee_name', 'v.dept_code', 'v.date_permanent', 'v.total_salary']);

        $tb_severance = DB::table('tb_severance')->get();

        return response()->json([
            'tb_resignation' => $tb_resignation,
            'tb_severance' => $tb_severance,
            'cutoff' => $cutoff
        ]);
    }

    public function capture_resignation(Request $data)
    {
        $admin = Auth::check() ? Auth::user()->name : 'System';
        $now = date('Y-m-d H:i:s');
        $items = $data->items;

        $tb_pajak = DB::table('tb_pph21_pesangon')->orderby('id', 'asc')->get();
        $brackets = [];
        foreach ($tb_pajak as $row) {
            $brackets[] = [
                'limit' => (float) $row->batas_atas,
                'rate' => (float) $row->prosentase
            ];
        }

        foreach ($items as $item) {
            $amount = (float) $item['amount'];
            $pph21 = 0;
            $nilai_total = $amount;

            // Tax calculation logic
            $batas1 = $brackets[0]['limit'];
            $pro1 = $brackets[0]['rate'];
            $batas2 = $brackets[1]['limit'];
            $pro2 = $brackets[1]['rate'];
            $batas3 = $brackets[2]['limit'];
            $pro3 = $brackets[2]['rate'];
            $pro4 = $brackets[3]['rate'];

            if ($nilai_total > $batas3) {
                $val4 = $nilai_total - $batas3;
                $pph_4 = $val4 * $pro4 / 100;
                $pph_3 = ($batas3 - $batas2) * $pro3 / 100;
                $pph_2 = ($batas2 - $batas1) * $pro2 / 100;
                $pph_1 = $batas1 * $pro1 / 100;
                $pph21 = $pph_1 + $pph_2 + $pph_3 + $pph_4;
            } else if ($nilai_total > $batas2) {
                $val3 = $nilai_total - $batas2;
                $pph_3 = $val3 * $pro3 / 100;
                $pph_2 = ($batas2 - $batas1) * $pro2 / 100;
                $pph_1 = $batas1 * $pro1 / 100;
                $pph21 = $pph_1 + $pph_2 + $pph_3;
            } else if ($nilai_total > $batas1) {
                $val2 = $nilai_total - $batas1;
                $pph_2 = $val2 * $pro2 / 100;
                $pph_1 = $batas1 * $pro1 / 100;
                $pph21 = $pph_1 + $pph_2;
            } else {
                $pph_1 = $nilai_total * $pro1 / 100;
                $pph21 = $pph_1;
            }

            $amount_paid = $amount - $pph21;
            $cutoff = $item['cutoff'];

            $d1 = new \DateTime($item['date_permanent']);
            $d2 = new \DateTime($cutoff);
            $diff = $d1->diff($d2);
            $years = $diff->y;
            $months = $diff->m;

            if ($d1 > $d2) {
                $years = 0;
                $months = 0;
            }

            DB::table('tb_salary_resignation')->insert([
                'id_employee' => $item['id_employee'],
                'date_permanent' => $item['date_permanent'],
                'date_cutoff' => $cutoff,
                'tahun' => $years,
                'bulan' => $months,
                'total_salary' => $item['total_salary'],
                'amount' => $amount,
                'pph21' => $pph21,
                'amount_paid' => $amount_paid,
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }

        return response()->json(['status' => 'success']);
    }

    public function summary_assignment($start = null, $end = null)
    {
        $request = request();
        $start = $start ?? $request->start;
        $end = $end ?? $request->end;

        if (!$start || !$end || $start == '0' || $end == '0') {
            $today = date('Y-m-d');
            $periode = date('Y-m', strtotime($today));
            $end = date('Y-m-d', strtotime($periode . '-24'));
            $awal_bulan = date('Y-m-d', strtotime($periode . '-01'));
            $akhir_bulan = date('Y-m', strtotime('-1 days', strtotime($awal_bulan)));
            $start = date('Y-m-d', strtotime($akhir_bulan . '-25'));
        }

        $year = date('Y', strtotime($start));
        if ($request->has('year') && $request->year != "") {
            $year = $request->year;
        }
        $periode_name = date('Y, F', strtotime($start));

        // Data table by department
        $tb_rekap = DB::table('db_ems.tb_ot_summary as s')
            ->leftJoin('db_ems.tb_departments as d', 's.dept_code', '=', 'd.dept_code')
            ->where('s.kategori', 'Assignment')
            ->where('s.start_date', '>=', $start)
            ->where('s.end_date', '<=', $end)
            ->select('d.dept_name as department', 's.dept_code', DB::raw('SUM(s.net_amount) as nominal_incentive'))
            ->groupBy('d.dept_name', 's.dept_code')
            ->orderBy('d.dept_name')
            ->get();

        // Line chart data (month by month for the current year)
        $months = [
            '01' => 'JAN',
            '02' => 'FEB',
            '03' => 'MAR',
            '04' => 'APR',
            '05' => 'MEI',
            '06' => 'JUN',
            '07' => 'JUL',
            '08' => 'AGUS',
            '09' => 'SEPT',
            '10' => 'OKT',
            '11' => 'NOV',
            '12' => 'DES'
        ];
        $monthly_data = [];
        foreach ($months as $m => $mName) {
            $periode_m = $year . '-' . $m;
            $sum = DB::table('db_ems.tb_ot_summary')
                ->where('kategori', 'Assignment')
                ->where('periode', $periode_m)
                ->sum('net_amount');
            $monthly_data[] = [
                'month' => $mName,
                'amount' => (int) $sum
            ];
        }

        return view('page.payroll.salary_summary_assignment', [
            'start' => $start,
            'end' => $end,
            'year' => $year,
            'periode_name' => $periode_name,
            'tb_rekap' => $tb_rekap,
            'monthly_data' => $monthly_data,
            'site' => $this->site,
            'menu' => 'payroll'
        ]);
    }

}
