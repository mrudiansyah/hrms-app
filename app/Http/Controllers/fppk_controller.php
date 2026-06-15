<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use DateTime;
use Auth;
use PDF;

class fppk_controller extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    #region Index / List FPPK
    function index(Request $request)
    {
        // Ambil parameter filter
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $department = $request->input('department');
        $status = $request->input('status');

        // Query dasar
        $query = DB::table('tb_fppk')
            ->where('isDelete', 0);

        // Filter tanggal berdasarkan submitted_date (atau bisa pakai created_at)
        if ($start_date && $end_date) {
            $query->whereBetween('submitted_date', [$start_date, $end_date]);
        } elseif ($start_date) {
            $query->where('submitted_date', '>=', $start_date);
        } elseif ($end_date) {
            $query->where('submitted_date', '<=', $end_date);
        }

        // Filter department
        if ($department) {
            $query->where('department', $department);
        }

        // Filter status (Baru / Penggantian)
        if ($status) {
            $query->where('application_status', $status);
        }

        // Ambil data
        $fppkList = $query->orderBy('id', 'desc')->get();

        // Ambil daftar department untuk filter dropdown
        $departments = DB::table('tb_fppk')
            ->where('isDelete', 0)
            ->whereNotNull('department')
            ->distinct()
            ->pluck('department');

        $menu = 'fppk';

        return view('recruitment.fppk', compact('fppkList', 'departments', 'start_date', 'end_date', 'menu'));
    }
    #endregion

    #region Create Form
    function create()
    {
        $menu = 'fppk';
        return view('recruitment.fppk_create', compact('menu'));
    }
    #endregion

    #region Store Data
    function store(Request $request)
    {
        try {
            $data = [
                'registration_number' => $request->registration_number,
                'application_status' => $request->application_status,
                'division' => $request->division,
                'department' => $request->department,
                'section' => $request->section,
                'total_employee_section' => $request->total_employee_section,
                'total_employee_bod_approved' => $request->total_employee_bod_approved,
                'position_job' => $request->position_job,
                'job_function_level' => $request->job_function_level,
                'employment_status' => $request->employment_status,
                'employment_type' => $request->employment_type,
                'working_period_years' => $request->working_period_years,
                'working_period_months' => $request->working_period_months,
                'starting_date' => $request->starting_date,
                'employee_number_needed' => $request->employee_number_needed,
                'date_received' => $request->date_received,
                'minimal_wage' => $request->minimal_wage,
                'follow_sae_regulation' => $request->follow_sae_regulation ? 1 : 0,
                'salary_notes' => $request->salary_notes,
                'payment_term' => $request->payment_term,
                'task_description' => $request->task_description,
                'min_education' => $request->min_education,
                'special_ability' => $request->special_ability,
                'work_experience_type' => $request->work_experience_type,
                'gender' => $request->gender,
                'min_work_experience_years' => $request->min_work_experience_years,
                'min_age' => $request->min_age,
                'max_age' => $request->max_age,
                'computer_mastery' => $request->computer_mastery,
                'min_height_cm' => $request->min_height_cm,
                'min_weight_kg' => $request->min_weight_kg,
                'max_weight_kg' => $request->max_weight_kg,
                'foreign_language_ability' => $request->foreign_language_ability,
                'character_personalities' => $request->character_personalities,
                'reason_for_recruiting' => $request->reason_for_recruiting,
                'budget_status' => $request->budget_status,
                'remarks' => $request->remarks,
                'submitted_by' => Auth::user()->name,
                'submitted_date' => date('Y-m-d'),
                'created_by' => Auth::user()->name,
                'created_at' => now(),
            ];

            DB::table('tb_fppk')->insert($data);

            return redirect('/FPPK')->with('success', 'Data FPPK berhasil disimpan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }
    #endregion

    #region Show Detail
    function show($id)
    {
        $fppk = DB::table('tb_fppk')->where('id', $id)->where('isDelete', 0)->first();
        $candidates = DB::table('tb_fppk_candidates')->where('fppk_id', $id)->where('isDelete', 0)->get();
        $menu = 'fppk';

        if (!$fppk) {
            return redirect('/FPPK')->with('error', 'Data tidak ditemukan!');
        }

        return view('recruitment.fppk_show', compact('fppk', 'candidates', 'menu'));
    }
    #endregion

    #region Edit Form
    function edit($id)
    {
        $fppk = DB::table('tb_fppk')->where('id', $id)->where('isDelete', 0)->first();
        $menu = 'fppk';

        if (!$fppk) {
            return redirect('/FPPK')->with('error', 'Data tidak ditemukan!');
        }

        return view('recruitment.fppk_edit', compact('fppk', 'menu'));
    }
    #endregion

    #region Update Data
    function update(Request $request, $id)
    {
        try {
            $data = [
                'registration_number' => $request->registration_number,
                'application_status' => $request->application_status,
                'division' => $request->division,
                'department' => $request->department,
                'section' => $request->section,
                'total_employee_section' => $request->total_employee_section,
                'total_employee_bod_approved' => $request->total_employee_bod_approved,
                'position_job' => $request->position_job,
                'job_function_level' => $request->job_function_level,
                'employment_status' => $request->employment_status,
                'employment_type' => $request->employment_type,
                'working_period_years' => $request->working_period_years,
                'working_period_months' => $request->working_period_months,
                'starting_date' => $request->starting_date,
                'employee_number_needed' => $request->employee_number_needed,
                'date_received' => $request->date_received,
                'minimal_wage' => $request->minimal_wage,
                'follow_sae_regulation' => $request->follow_sae_regulation ? 1 : 0,
                'salary_notes' => $request->salary_notes,
                'payment_term' => $request->payment_term,
                'task_description' => $request->task_description,
                'min_education' => $request->min_education,
                'special_ability' => $request->special_ability,
                'work_experience_type' => $request->work_experience_type,
                'gender' => $request->gender,
                'min_work_experience_years' => $request->min_work_experience_years,
                'min_age' => $request->min_age,
                'max_age' => $request->max_age,
                'computer_mastery' => $request->computer_mastery,
                'min_height_cm' => $request->min_height_cm,
                'min_weight_kg' => $request->min_weight_kg,
                'max_weight_kg' => $request->max_weight_kg,
                'foreign_language_ability' => $request->foreign_language_ability,
                'character_personalities' => $request->character_personalities,
                'reason_for_recruiting' => $request->reason_for_recruiting,
                'budget_status' => $request->budget_status,
                'remarks' => $request->remarks,
                'updated_at' => now(),
            ];

            DB::table('tb_fppk')->where('id', $id)->update($data);

            return redirect('/FPPK')->with('success', 'Data FPPK berhasil diupdate!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengupdate: ' . $e->getMessage());
        }
    }
    #endregion

    #region Approval Process
    function approve(Request $request, $id)
    {
        try {
            $stage = $request->approval_stage;
            $approver_name = $request->approver_name;
            $approval_date = $request->approval_date;

            $data = [];

            switch ($stage) {
                case 'submitted':
                    $data['submitted_by'] = $approver_name;
                    $data['submitted_date'] = $approval_date;
                    break;
                case 'supported':
                    $data['supported_by'] = $approver_name;
                    $data['supported_date'] = $approval_date;
                    break;
                case 'checked':
                    $data['checked_by'] = $approver_name;
                    $data['checked_date'] = $approval_date;
                    break;
                case 'validated':
                    $data['validated_by'] = $approver_name;
                    $data['validated_date'] = $approval_date;
                    break;
                case 'approved':
                    $data['approved_by'] = $approver_name;
                    $data['approved_date'] = $approval_date;
                    break;
                case 'legalized':
                    $data['legalized_by'] = $approver_name;
                    $data['legalized_date'] = $approval_date;
                    break;
            }

            $data['updated_at'] = now();

            DB::table('tb_fppk')->where('id', $id)->update($data);

            return response()->json(['success' => true, 'message' => 'Approval berhasil!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    #endregion

    #region Delete (Soft Delete)
    function destroy($id)
    {
        try {
            DB::table('tb_fppk')->where('id', $id)->update([
                'isDelete' => 1,
                'updated_at' => now()
            ]);

            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    #endregion

    #region Print PDF
    function print($id)
    {
        $fppk = DB::table('tb_fppk')->where('id', $id)->where('isDelete', 0)->first();
        $candidates = DB::table('tb_fppk_candidates')->where('fppk_id', $id)->where('isDelete', 0)->get();

        if (!$fppk) {
            return redirect('/FPPK')->with('error', 'Data tidak ditemukan!');
        }

        $pdf = PDF::loadView('recruitment.fppk_pdf', compact('fppk', 'candidates'));
        return $pdf->download('FPPK_' . $fppk->registration_number . '.pdf');
    }
    #endregion

    #region Export Excel
    function export(Request $request)
    {
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $department = $request->input('department');
        $status = $request->input('status');

        $query = DB::table('tb_fppk')->where('isDelete', 0);

        if ($start_date && $end_date) {
            $query->whereBetween('submitted_date', [$start_date, $end_date]);
        }
        if ($department) {
            $query->where('department', $department);
        }
        if ($status) {
            $query->where('application_status', $status);
        }

        $data = $query->orderBy('id', 'desc')->get();

        // Export ke Excel menggunakan maatwebsite/excel atau output sederhana
        // Jika tidak ada package Excel, bisa pakai output CSV
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="FPPK_Export_' . date('Ymd') . '.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, [
            'ID',
            'Reg Number',
            'Status',
            'Division',
            'Department',
            'Section',
            'Position',
            'Jumlah Dibutuhkan',
            'Min Pendidikan',
            'Gender',
            'Budget Status',
            'Submitted By',
            'Submitted Date',
            'Approved By',
            'Approved Date'
        ]);

        foreach ($data as $row) {
            fputcsv($output, [
                $row->id,
                $row->registration_number,
                $row->application_status,
                $row->division,
                $row->department,
                $row->section,
                $row->position_job,
                $row->employee_number_needed,
                $row->min_education,
                $row->gender,
                $row->budget_status,
                $row->submitted_by,
                $row->submitted_date,
                $row->approved_by,
                $row->approved_date
            ]);
        }

        fclose($output);
        exit;
    }
    #endregion
}