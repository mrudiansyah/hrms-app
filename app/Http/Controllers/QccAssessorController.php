<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class QccAssessorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $this->authorizeCommittee();

        $periods = DB::table('tb_qcc_periode')
            ->orderByDesc('periode_start')
            ->orderByDesc('id')
            ->get();
        $categories = DB::table('tb_asses_category')
            ->orderBy('asses_category')
            ->get();
        $selectedPeriodId = $request->query('periode_id');
        $selectedCategoryId = $request->query('category_id');

        if ($selectedPeriodId !== null) {
            abort_unless($periods->contains(fn ($period) => (string) $period->id === (string) $selectedPeriodId), 404);
            $selectedPeriodId = (int) $selectedPeriodId;
        } else {
            $selectedPeriodId = $periods->first()?->id;
        }

        if ($selectedCategoryId !== null) {
            abort_unless($categories->contains(fn ($category) => (string) $category->id === (string) $selectedCategoryId), 404);
            $selectedCategoryId = (int) $selectedCategoryId;
        } else {
            $selectedCategoryId = $categories->first()?->id;
        }

        $assessors = collect();
        $membersByAssessor = collect();
        $availableEmployeesByAssessor = collect();

        if ($selectedPeriodId && $selectedCategoryId) {
            $assessors = DB::table('tb_qcc_assesor')
                ->where('id_qcc_periode', $selectedPeriodId)
                ->where('id_asses_category', $selectedCategoryId)
                ->orderBy('assesor_name')
                ->orderBy('id')
                ->get();

            if ($assessors->isNotEmpty()) {
                $membersByAssessor = DB::table('tb_qcc_assesor_member')
                    ->leftJoin('tb_employees', 'tb_employees.id', '=', 'tb_qcc_assesor_member.id_employee')
                    ->whereIn('tb_qcc_assesor_member.id_assesor', $assessors->pluck('id'))
                    ->orderBy('tb_employees.employee_name')
                    ->get([
                        'tb_qcc_assesor_member.*',
                        'tb_employees.NIK',
                        'tb_employees.employee_name',
                    ])
                    ->groupBy('id_assesor');

                $employees = DB::table('tb_employees')
                    ->where('delete', '0')
                    ->orderBy('employee_name')
                    ->get(['id', 'NIK', 'employee_name']);

                foreach ($assessors as $assessor) {
                    $assignedIds = $membersByAssessor->get($assessor->id, collect())->pluck('id_employee');
                    $availableEmployeesByAssessor[$assessor->id] = $employees
                        ->reject(fn ($employee) => $assignedIds->contains($employee->id))
                        ->values();
                }
            }
        }

        $menu = 'qcc';

        return view('qcc.assessors', compact(
            'menu',
            'periods',
            'categories',
            'selectedPeriodId',
            'selectedCategoryId',
            'assessors',
            'membersByAssessor',
            'availableEmployeesByAssessor',
        ));
    }

    public function store(Request $request)
    {
        $this->authorizeCommittee();
        $data = $request->validate([
            'id_qcc_periode' => ['required', 'integer', Rule::exists('tb_qcc_periode', 'id')],
            'id_asses_category' => ['required', 'integer', Rule::exists('tb_asses_category', 'id')],
            'assesor_name' => ['required', 'string', 'max:50'],
        ]);

        if ($this->assessorExists($data['id_qcc_periode'], $data['id_asses_category'], $data['assesor_name'])) {
            return redirect($this->assessorUrl($data['id_qcc_periode'], $data['id_asses_category']))
                ->withInput()
                ->with('error', 'Nama assessor sudah terdaftar untuk periode dan kategori ini.');
        }

        $now = now();
        DB::table('tb_qcc_assesor')->insert([
            ...$data,
            'created_at' => $now,
            'updated_at' => $now,
            'updated_by' => Auth::user()->name,
        ]);

        return redirect($this->assessorUrl($data['id_qcc_periode'], $data['id_asses_category']))
            ->with('success', 'Assessor berhasil ditambahkan.');
    }

    public function update(Request $request, int $id)
    {
        $this->authorizeCommittee();
        $assessor = DB::table('tb_qcc_assesor')->where('id', $id)->first();
        abort_unless($assessor, 404);
        $data = $request->validate([
            'id_qcc_periode' => ['required', 'integer', Rule::exists('tb_qcc_periode', 'id')],
            'id_asses_category' => ['required', 'integer', Rule::exists('tb_asses_category', 'id')],
            'assesor_name' => ['required', 'string', 'max:50'],
        ]);

        if (
            ((int) $assessor->id_qcc_periode !== (int) $data['id_qcc_periode']
                || (int) $assessor->id_asses_category !== (int) $data['id_asses_category'])
            && DB::table('tb_qcc_assesment_team')->where('id_assesor', $id)->exists()
        ) {
            return redirect($this->assessorUrl($assessor->id_qcc_periode, $assessor->id_asses_category))
                ->with('error', 'Periode atau kategori assessor yang sudah memiliki penilaian tidak dapat diubah.');
        }

        if ($this->assessorExists($data['id_qcc_periode'], $data['id_asses_category'], $data['assesor_name'], $id)) {
            return redirect($this->assessorUrl($assessor->id_qcc_periode, $assessor->id_asses_category))
                ->withInput()
                ->with('error', 'Nama assessor sudah terdaftar untuk periode dan kategori ini.');
        }

        DB::table('tb_qcc_assesor')->where('id', $id)->update([
            ...$data,
            'updated_at' => now(),
            'updated_by' => Auth::user()->name,
        ]);

        return redirect($this->assessorUrl($data['id_qcc_periode'], $data['id_asses_category']))
            ->with('success', 'Assessor berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $this->authorizeCommittee();
        $assessor = DB::table('tb_qcc_assesor')->where('id', $id)->first();
        abort_unless($assessor, 404);

        if (
            DB::table('tb_qcc_assesor_member')->where('id_assesor', $id)->exists()
            || DB::table('tb_qcc_assesment_team')->where('id_assesor', $id)->exists()
        ) {
            return redirect($this->assessorUrl($assessor->id_qcc_periode, $assessor->id_asses_category))
                ->with('error', 'Assessor masih memiliki anggota atau riwayat penilaian dan tidak dapat dihapus.');
        }

        DB::table('tb_qcc_assesor')->where('id', $id)->delete();

        return redirect($this->assessorUrl($assessor->id_qcc_periode, $assessor->id_asses_category))
            ->with('success', 'Assessor berhasil dihapus.');
    }

    public function storeMember(Request $request, int $assessorId)
    {
        $this->authorizeCommittee();
        $assessor = DB::table('tb_qcc_assesor')->where('id', $assessorId)->first();
        abort_unless($assessor, 404);
        $data = $request->validate([
            'id_employee' => ['required', 'integer', Rule::exists('tb_employees', 'id')],
        ]);

        $employeeIsActive = DB::table('tb_employees')
            ->where('id', $data['id_employee'])
            ->where('delete', '0')
            ->exists();
        if (!$employeeIsActive) {
            return redirect($this->assessorUrl($assessor->id_qcc_periode, $assessor->id_asses_category))
                ->with('error', 'Karyawan tidak aktif atau tidak ditemukan.');
        }

        $exists = DB::table('tb_qcc_assesor_member')
            ->where('id_assesor', $assessorId)
            ->where('id_employee', $data['id_employee'])
            ->exists();
        if ($exists) {
            return redirect($this->assessorUrl($assessor->id_qcc_periode, $assessor->id_asses_category))
                ->with('error', 'Karyawan sudah menjadi anggota assessor ini.');
        }

        $now = now();
        DB::table('tb_qcc_assesor_member')->insert([
            'id_assesor' => $assessorId,
            'id_employee' => $data['id_employee'],
            'created_at' => $now,
            'updated_at' => $now,
            'updated_by' => Auth::user()->name,
        ]);

        return redirect($this->assessorUrl($assessor->id_qcc_periode, $assessor->id_asses_category))
            ->with('success', 'Anggota assessor berhasil ditambahkan.');
    }

    public function destroyMember(int $id)
    {
        $this->authorizeCommittee();
        $member = DB::table('tb_qcc_assesor_member')
            ->join('tb_qcc_assesor', 'tb_qcc_assesor.id', '=', 'tb_qcc_assesor_member.id_assesor')
            ->where('tb_qcc_assesor_member.id', $id)
            ->first([
                'tb_qcc_assesor_member.id',
                'tb_qcc_assesor.id_qcc_periode',
                'tb_qcc_assesor.id_asses_category',
            ]);
        abort_unless($member, 404);

        DB::table('tb_qcc_assesor_member')->where('id', $id)->delete();

        return redirect($this->assessorUrl($member->id_qcc_periode, $member->id_asses_category))
            ->with('success', 'Anggota assessor berhasil dihapus.');
    }

    private function assessorExists(int $periodId, int $categoryId, string $name, ?int $exceptId = null): bool
    {
        $query = DB::table('tb_qcc_assesor')
            ->where('id_qcc_periode', $periodId)
            ->where('id_asses_category', $categoryId)
            ->where('assesor_name', $name);

        if ($exceptId !== null) {
            $query->where('id', '<>', $exceptId);
        }

        return $query->exists();
    }

    private function authorizeCommittee(): void
    {
        abort_unless(Auth::user()?->hasRole('committee'), 403, 'Anda tidak punya akses.');
    }

    private function assessorUrl(int $periodId, int $categoryId): string
    {
        return '/qcc_assesor?periode_id=' . $periodId . '&category_id=' . $categoryId;
    }
}
