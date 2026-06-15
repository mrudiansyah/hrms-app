<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use DateTime;
use Auth;

class RenewalController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    // Menampilkan halaman utama Renewal dengan data
    public function index(Request $request)
    {
        $nik = Auth::user()->nik;
        $user_id=DB::table('tb_employees')->where('NIK',$nik)->value('id');
        // Query builder
        $query = DB::table('tb_renewal');

        // Filter berdasarkan start_date
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        // Filter berdasarkan end_date
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Filter berdasarkan NIK
        if ($request->filled('nik')) {
            $query->where('nik', 'like', '%' . $request->nik . '%');
        }

        // Filter berdasarkan grade
        if ($request->filled('grade')) {
            $query->where('new_grade', $request->grade);
        }

        // Filter berdasarkan status approval
        if ($request->filled('status_approval')) {
            $status = $request->status_approval;
            if ($status == 'Approved') {
                $query->whereNotNull('approved_by_name')->whereNotNull('approved_by_date');
            } elseif ($status == 'Proposed') {
                $query->whereNotNull('proposed_by_name')->whereNotNull('proposed_by_date')
                    ->whereNull('approved_by_name');
            } elseif ($status == 'Supported') {
                $query->whereNotNull('supported_by_name')->whereNotNull('supported_by_date')
                    ->whereNull('proposed_by_name');
            } elseif ($status == 'Submitted') {
                $query->whereNotNull('suggested_by_name')->whereNotNull('suggested_by_date')
                    ->whereNull('supported_by_name');
            } elseif ($status == 'Draft') {
                $query->whereNull('suggested_by_name');
            }
        }

        // Get data with pagination
        $renewals = $query->where('is_deleted', '0')->orderBy('created_at', 'desc')->get();

        // Get distinct grades for filter dropdown
        $grades = DB::table('tb_renewal')
            ->select('new_grade')
            ->distinct()
            ->whereNotNull('new_grade')
            ->pluck('new_grade')
            ->toArray();

        // Pass variables to view
        return view('renewal.index', [
            'renewals' => $renewals,
            'grades' => $grades,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'user_id'=>$user_id
        ]);
    }

    // Search employee by NIK or employee_name from tb_employees
    public function searchEmployee(Request $request)
    {
        $keyword = $request->get('keyword');

        $employees = DB::table('tb_employees')
            ->leftJoin('tb_positions', 'tb_employees.position_id', '=', 'tb_positions.id')
            ->where('tb_employees.NIK', 'like', '%' . $keyword . '%')
            ->orWhere('tb_employees.employee_name', 'like', '%' . $keyword . '%')
            ->where('tb_employees.status', '1')
            ->select(
                'tb_employees.NIK as nik',
                'tb_employees.employee_name as name',
                'tb_employees.PIN',
                'tb_employees.position_id',
                'tb_employees.dept_id',
                'tb_positions.position_name'
            )
            ->limit(10)
            ->get();

        // Format response
        foreach ($employees as $employee) {
            $employee->position = $employee->position_name ?? '-';
        }

        return response()->json($employees);
    }

    // Menampilkan form create renewal berdasarkan NIK
    public function create(Request $request)
    {
        $nik = $request->get('nik');

        // Ambil data karyawan dari tabel tb_employees dengan join ke tb_positions
        $employee = DB::table('tb_employees')
            ->leftJoin('tb_positions', 'tb_employees.position_id', '=', 'tb_positions.id')
            ->where('tb_employees.NIK', $nik)
            ->select(
                'tb_employees.*',
                'tb_positions.position_name'
            )
            ->first();

        if (!$employee) {
            return redirect()->route('renewal.index')
                ->with('error', 'Karyawan tidak ditemukan!');
        }

        // Hitung masa kerja dari join_date
        $joinDate = new DateTime($employee->join_date);
        $now = new DateTime();
        $diff = $joinDate->diff($now);
        $workPeriod = $diff->y . ' Tahun ' . $diff->m . ' Bulan';

        // Mapping field untuk form
        $employeeData = (object) [
            'nik' => $employee->NIK,
            'name' => $employee->employee_name,
            'date_of_join' => $employee->join_date,
            'work_period' => $workPeriod,
            'position' => $employee->position_name ?? '-',
            'department_id' => $employee->dept_id ?? '',
            'gender' => $employee->gender ?? '',
            'leader_id' => $employee->leader_id ?? '',
            'status' => $employee->status ?? ''
        ];

        $positions = DB::table('tb_positions')
            ->where('status_active', 1)
            ->orderBy('position_name', 'asc')
            ->get();

        // Ambil data employees untuk select2 (menggunakan id)
        $employees = DB::table('tb_employees')
            ->where('status', 1) // Hanya karyawan aktif
            ->orderBy('employee_name', 'asc')
            ->get(['id', 'NIK', 'employee_name']);

        return view('renewal.form', compact('employeeData', 'positions', 'employees'));
    }

    // Menyimpan data renewal
    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required',
            'name' => 'required',
            'date_of_join' => 'required|date',
            'work_period' => 'required',
            'category' => 'required|in:Mutation,Promotion',
            'new_position' => 'required',
            'new_grade' => 'required',
            'new_division' => 'required',
            'new_salary' => 'required_if:category,Promotion|nullable',
            'new_others' => 'nullable',
            'reasons' => 'required',
            'effective_from' => 'required|date',
            'suggested_by_id' => 'nullable|exists:tb_employees,id', // Validasi id exists
            'supported_by_id' => 'nullable|exists:tb_employees,id',
        ]);

        // Ambil nama karyawan dari id yang dipilih
        $suggestedByName = null;
        if ($request->suggested_by_id) {
            $suggested = DB::table('tb_employees')->where('id', $request->suggested_by_id)->first();
            $suggestedByName = $suggested->employee_name ?? null;
        }

        $supportedByName = null;
        if ($request->supported_by_id) {
            $supported = DB::table('tb_employees')->where('id', $request->supported_by_id)->first();
            $supportedByName = $supported->employee_name ?? null;
        }

        $newSalary = $request->new_salary ? str_replace(['.', ','], '', $request->new_salary) : null;
        if ($newSalary == '')
            $newSalary = 0;
        $lastSalary = $request->last_salary ? str_replace(['.', ','], '', $request->last_salary) : null;

        $personnel_id = DB::table('tb_employees')->where('status', '1')->where('position_id', '17')->value('id');
        $proposed_by_id = DB::table('tb_employees')->where('status', '1')->where('position_id', '15')->value('id');
        $approved_by_id = DB::table('tb_employees')->where('status', '1')->where('position_id', '16')->value('id');
        $personnel_name = DB::table('tb_employees')->where('status', '1')->where('position_id', '17')->value('employee_name');
        $proposed_by_name = DB::table('tb_employees')->where('status', '1')->where('position_id', '15')->value('employee_name');
        $approved_by_name = DB::table('tb_employees')->where('status', '1')->where('position_id', '16')->value('employee_name');

        if ($request->category == 'Mutation') {
            $approved_by_id = '';
            $approved_by_name = '';
        }

        $data = [
            'nik' => $request->nik,
            'name' => $request->name,
            'date_of_join' => $request->date_of_join,
            'work_period' => $request->work_period,
            'category' => $request->category,
            'last_renewal' => $request->last_renewal,
            'last_position' => $request->last_position,
            'last_grade' => $request->last_grade,
            'last_division' => $request->last_division,
            'last_salary' => $lastSalary,
            'last_others' => $request->last_others,
            'new_position' => $request->new_position,
            'new_grade' => $request->new_grade,
            'new_division' => $request->new_division,
            'new_salary' => $newSalary,
            'new_others' => $request->new_others,
            'reasons' => $request->reasons,
            'effective_from' => $request->effective_from,
            'suggested_by_id' => $request->suggested_by_id, // Simpan id
            'suggested_by_name' => $suggestedByName, // Simpan nama
            'suggested_by_date' => null,
            'supported_by_id' => $request->supported_by_id, // Simpan id
            'supported_by_name' => $supportedByName, // Simpan nama
            'supported_by_date' => null,
            'personnel_id' => $personnel_id,
            'personnel_name' => $personnel_name,
            'proposed_by_id' => $proposed_by_id,
            'proposed_by_name' => $proposed_by_name,
            'approved_by_id' => $approved_by_id,
            'approved_by_name' => $approved_by_name,
            'created_by' => Auth::id(),
            'created_at' => now(),
        ];

        $id = DB::table('tb_renewal')->insertGetId($data);

        return redirect()->route('renewal.show', $id)
            ->with('success', 'Data renewal berhasil disimpan!');
    }
    // Menampilkan detail renewal
    public function show($id)
    {
        $renewal = DB::table('tb_renewal')->where('id', $id)->first();

        if (!$renewal) {
            return redirect()->route('renewal.index')
                ->with('error', 'Data tidak ditemukan!');
        }

        return view('renewal.show', compact('renewal'));
    }

    // Menampilkan form edit renewal
    public function edit($id)
    {
        $renewal = DB::table('tb_renewal')->where('id', $id)->first();

        if (!$renewal) {
            return redirect()->route('renewal.index')
                ->with('error', 'Data tidak ditemukan!');
        }

        return view('renewal.edit', compact('renewal'));
    }

    // Update data renewal
    public function update(Request $request, $id)
    {
        $request->validate([
            'new_position' => 'required',
            'new_grade' => 'required',
            'new_division' => 'required',
            'new_salary' => 'required|numeric',
            'reasons' => 'required',
            'effective_from' => 'required|date',
        ]);

        $data = [
            'new_position' => $request->new_position,
            'new_grade' => $request->new_grade,
            'new_division' => $request->new_division,
            'new_salary' => str_replace(['Rp', '.', ','], '', $request->new_salary),
            'new_others' => $request->new_others,
            'reasons' => $request->reasons,
            'effective_from' => $request->effective_from,
            'updated_at' => now(),
        ];

        DB::table('tb_renewal')->where('id', $id)->update($data);

        return redirect()->route('renewal.show', $id)
            ->with('success', 'Data renewal berhasil diupdate!');
    }

    // Process approval
    public function approve(Request $request, $id)
    {
        $request->validate([
            'approval_stage' => 'required|in:submitted,supported,personnel,proposed,approved',
            'approver_name' => 'required|string|max:255',
            'approval_date' => 'required|date',
        ]);

        $data = [];
        $stage = $request->approval_stage;
        $approverName = $request->approver_name;
        $approvalDate = $request->approval_date;
        $approvalAction = $request->approval_action;

        switch ($stage) {
            case 'submitted':
                $data = [
                    'suggested_by_name' => $approverName,
                    'suggested_by_date' => $approvalDate,
                    'suggested_status' => $approvalAction,
                    'updated_at' => now(),
                ];
                $message = 'Status berhasil diupdate menjadi Submitted!';
                break;
            case 'supported':
                $data = [
                    'supported_by_name' => $approverName,
                    'supported_by_date' => $approvalDate,
                    'supported_status' => $approvalAction,
                    'updated_at' => now(),
                ];
                $message = 'Status berhasil diupdate menjadi Supported!';
                break;
            case 'personnel':
                $data = [
                    'personnel_name' => $approverName,
                    'personnel_date' => $approvalDate,
                    'personnel_status' => $approvalAction,
                    'updated_at' => now(),
                ];
                $message = 'Status berhasil diupdate menjadi Personnel!';
                break;
            case 'proposed':
                $data = [
                    'proposed_by_name' => $approverName,
                    'proposed_by_date' => $approvalDate,
                    'proposed_status' => $approvalAction,
                    'updated_at' => now(),
                ];
                $message = 'Status berhasil diupdate menjadi Proposed!';
                break;
            case 'approved':
                $data = [
                    'approved_by_name' => $approverName,
                    'approved_by_date' => $approvalDate,
                    'approved_status' => $approvalAction,
                    'updated_at' => now(),
                ];
                $message = 'Status berhasil diupdate menjadi Approved!';
                break;
            default:
                return response()->json(['success' => false, 'message' => 'Invalid approval stage'], 400);
        }

        DB::table('tb_renewal')->where('id', $id)->update($data);

        return response()->json(['success' => true, 'message' => $message]);
    }

    // Delete data renewal
    public function destroy($id)
    {
        $renewal = DB::table('tb_renewal')->where('id', $id)->first();

        if (!$renewal) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan!'], 404);
        }

        // DB::table('tb_renewal')->where('id', $id)->delete();
        DB::table('tb_renewal')->where('id', $id)->update(['is_deleted' => '1', 'updated_at' => now()]);

        return response()->json(['success' => true, 'message' => 'Data berhasil dihapus!']);
    }

    // Print/Generate PDF
    public function print($id)
    {
        $nik = Auth::user()->nik;
        $user_id=DB::table('tb_employees')->where('NIK',$nik)->value('id');
        $renewal = DB::table('tb_renewal')->where('id', $id)->first();

        if (!$renewal) {
            return redirect()->route('renewal.index')
                ->with('error', 'Data tidak ditemukan!');
        }

        // Load view untuk PDF
        $pdf = \PDF::loadView('renewal.pdf', compact('renewal','user_id'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('Employee_Status_Renewal_' . $renewal->nik . '.pdf');
    }
}