<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfileRefreshDataController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index(Request $request)
    {
        abort_unless($request->user()->hasRole('root'), 403, 'Anda tidak punya akses');

        $filters = $request->validate([
            'periode' => ['nullable', 'digits:4'],
            'dept_code' => ['nullable', 'string', 'max:50'],
            'position_name' => ['nullable', 'string', 'max:100'],
        ]);
        $period = $filters['periode'] ?? now()->format('Y');

        $pendingEmployees = DB::table('tb_employees')
            ->leftJoin('tb_departments', 'tb_departments.id', '=', 'tb_employees.dept_id')
            ->leftJoin('tb_positions', 'tb_positions.id', '=', 'tb_employees.position_id')
            ->select(
                'tb_employees.id',
                'tb_employees.NIK',
                'tb_employees.employee_name',
                'tb_departments.dept_code',
                'tb_departments.dept_name',
                'tb_positions.position_name'
            )
            ->where('tb_employees.status', '1')
            ->whereNotExists(function ($query) use ($period) {
                $query->selectRaw('1')
                    ->from('tb_refresh_data')
                    ->whereColumn('tb_refresh_data.id_employee', 'tb_employees.id')
                    ->where('tb_refresh_data.periode', $period);
            });

        if (!empty($filters['dept_code'])) {
            $pendingEmployees->where('tb_departments.dept_code', $filters['dept_code']);
        }
        if (!empty($filters['position_name'])) {
            $pendingEmployees->where('tb_positions.position_name', $filters['position_name']);
        }

        $pendingEmployees = $pendingEmployees
            ->orderBy('tb_employees.employee_name')
            ->paginate(50)
            ->withQueryString();

        $periods = DB::table('tb_refresh_data')
            ->select('periode')
            ->whereNotNull('periode')
            ->distinct()
            ->orderByDesc('periode')
            ->pluck('periode');
        if (!$periods->contains($period)) {
            $periods->push($period);
            $periods = $periods->sortDesc()->values();
        }
        $departments = DB::table('tb_departments')
            ->select('dept_code', 'dept_name')
            ->whereNotNull('dept_code')
            ->distinct()
            ->orderBy('dept_code')
            ->get();
        $positions = DB::table('tb_positions')
            ->select('position_name')
            ->whereNotNull('position_name')
            ->distinct()
            ->orderBy('position_name')
            ->pluck('position_name');

        return view('page.admin.profile_refresh_data', [
            'pendingEmployees' => $pendingEmployees,
            'period' => $period,
            'periods' => $periods,
            'filters' => $filters,
            'departments' => $departments,
            'positions' => $positions,
            'menu' => 'profile_refresh_data',
            'juduls' => 'Konfirmasi Data Profil',
        ]);
    }
}
