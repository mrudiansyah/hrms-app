<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class MobileSkdController extends Controller
{
    public function options(Request $request)
    {
        $employee = $this->employeeFor($request);
        if (!$employee) {
            return response()->json(['message' => 'Data karyawan untuk akun ini tidak ditemukan.'], 404);
        }

        $diagnoses = DB::table('tb_diagnosa_list')
            ->where('is_active', '1')
            ->orderBy('diagnosa')
            ->pluck('diagnosa');

        return response()->json([
            'employee' => [
                'name' => $employee->employee_name,
                'nik' => $employee->NIK,
                'department' => $employee->department,
            ],
            'diagnoses' => $diagnoses,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'start_leave' => ['required', 'date_format:Y-m-d'],
            'finish_leave' => ['required', 'date_format:Y-m-d', 'after_or_equal:start_leave'],
            'diagnosa' => ['required', 'string', 'max:255'],
            'foto' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:600'],
        ]);

        $start = Carbon::createFromFormat('Y-m-d', $data['start_leave'])->startOfDay();
        $finish = Carbon::createFromFormat('Y-m-d', $data['finish_leave'])->startOfDay();
        if ($start->isSunday() || $finish->isSunday()) {
            throw ValidationException::withMessages([
                'finish_leave' => 'Tanggal mulai dan akhir tidak boleh hari Minggu.',
            ]);
        }

        $employee = $this->employeeFor($request);
        if (!$employee) {
            return response()->json(['message' => 'Data karyawan untuk akun ini tidak ditemukan.'], 404);
        }

        $leave = DB::table('tb_employee_leaves')
            ->where('id_employee', $employee->id)
            ->where('status', '1')
            ->orderByDesc('id')
            ->first(['id']);
        if (!$leave) {
            return response()->json(['message' => 'Data hak cuti aktif karyawan tidak ditemukan.'], 422);
        }

        $leader = DB::table('tb_employees')
            ->leftJoin('tb_positions', 'tb_positions.id', '=', 'tb_employees.position_id')
            ->where('tb_employees.id', $employee->leader_id)
            ->first([
                'tb_employees.id',
                'tb_employees.leader_id',
                'tb_positions.position_index',
            ]);
        if (!$leader) {
            return response()->json(['message' => 'Atasan langsung karyawan tidak ditemukan. Hubungi HR.'], 422);
        }

        $approver = (int) $leader->position_index < 3
            ? $leader->leader_id
            : $leader->id;
        if (!$approver || !DB::table('tb_employees')->where('id', $approver)->exists()) {
            return response()->json(['message' => 'Atasan yang menyetujui SKD tidak ditemukan. Hubungi HR.'], 422);
        }

        $leaveCount = $this->leaveCount($employee->id, $start, $finish);
        $file = $data['foto'];
        $extension = strtolower((string) $file->guessExtension());
        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'pdf'], true)) {
            throw ValidationException::withMessages([
                'foto' => 'Format file tidak valid. Gunakan JPG, JPEG, PNG, atau PDF.',
            ]);
        }
        $filename = bin2hex(random_bytes(16)) . '.' . $extension;
        $storedPath = $file->storeAs('skd', $filename, 'public');
        $storage = Storage::disk('public');

        if (!$storedPath || !$storage->exists($storedPath) || $storage->size($storedPath) === 0) {
            if ($storedPath) {
                $storage->delete($storedPath);
            }
            return response()->json(['message' => 'File SKD gagal diunggah. Silakan coba kembali.'], 500);
        }

        try {
            $record = DB::transaction(function () use (
                $employee,
                $leave,
                $approver,
                $data,
                $start,
                $finish,
                $leaveCount,
                $filename
            ) {
                DB::table('tb_employees')
                    ->where('id', $employee->id)
                    ->lockForUpdate()
                    ->first(['id']);

                if (DB::table('tb_sick')
                    ->where('id_employee', $employee->id)
                    ->whereDate('tanggal', $start->toDateString())
                    ->exists()) {
                    throw ValidationException::withMessages([
                        'foto' => 'Anda sudah mengunggah SKD untuk tanggal mulai tersebut.',
                    ]);
                }

                $now = now();
                $documentId = DB::table('tb_sick')->insertGetId([
                    'id_employee' => $employee->id,
                    'NIK' => $employee->NIK,
                    'employee_name' => $employee->employee_name,
                    'upload_time' => $now,
                    'skd' => $filename,
                    'status' => '0',
                    'diagnosa' => $data['diagnosa'],
                    'tanggal' => $start->toDateString(),
                ]);

                DB::table('tb_leaves')->insert([
                    'id_leave' => $leave->id,
                    'id_doc' => $documentId,
                    'doc_date' => $now->toDateString(),
                    'id_employee' => $employee->id,
                    'category' => 'docter',
                    'start_leave' => $start->toDateString(),
                    'finish_leave' => $finish->toDateString(),
                    'start_working' => $finish->copy()->addDay()->toDateString(),
                    'leave_count' => $leaveCount,
                    'reason' => $data['diagnosa'],
                    'remark' => '',
                    'approved' => $approver,
                    'approved2' => '0',
                    'legalized' => '122',
                    'status_requested' => '1',
                    'status_approved' => '1',
                    'status_approved2' => '0',
                    'status_legalized' => '0',
                    'status_reported' => '0',
                    'admin' => 'Form SKD',
                    'status' => '1',
                    'overlap_doc' => '',
                ]);

                return [
                    'id' => $documentId,
                    'start_leave' => $start->toDateString(),
                    'finish_leave' => $finish->toDateString(),
                    'leave_count' => $leaveCount,
                ];
            });
        } catch (\Throwable $exception) {
            $storage->delete($storedPath);
            throw $exception;
        }

        return response()->json([
            'message' => 'Berhasil mengunggah SKD. Dokumen SKD asli tetap harus diserahkan ke HR.',
            'record' => $record,
        ], 201);
    }

    private function employeeFor(Request $request): ?object
    {
        $nik = $request->user()->nik;
        if (!$nik) {
            return null;
        }

        return DB::table('tb_employees')
            ->leftJoin('tb_departments', 'tb_departments.id', '=', 'tb_employees.dept_id')
            ->where('tb_employees.NIK', $nik)
            ->where('tb_employees.status', '1')
            ->first([
                'tb_employees.id',
                'tb_employees.NIK',
                'tb_employees.employee_name',
                'tb_employees.leader_id',
                'tb_departments.dept_name as department',
            ]);
    }

    private function leaveCount(int $employeeId, Carbon $start, Carbon $finish): int
    {
        $shifts = DB::table('tb_employee_shifts')
            ->leftJoin('tb_group_shifts', 'tb_group_shifts.id', '=', 'tb_employee_shifts.id_shift')
            ->leftJoin('tb_groups', 'tb_groups.group', '=', 'tb_group_shifts.group')
            ->where('tb_employee_shifts.id_employee', $employeeId)
            ->where('tb_employee_shifts.status', '1')
            ->get([
                'tb_groups.group as group_code',
                'tb_groups.cycle as cycle_length',
                'tb_employee_shifts.start_implement',
            ]);

        if ($shifts->isEmpty()) {
            return 0;
        }

        $groups = $shifts->pluck('group_code')->unique()->all();
        $workCycles = DB::table('tb_cycles')
            ->whereIn('group', $groups)
            ->where('shift', '>', '0')
            ->get(['group', 'days'])
            ->groupBy('group')
            ->map(fn ($cycles) => $cycles->pluck('days')->map(fn ($day) => (int) $day)->all());

        $daysOff = DB::table('tb_freedays')
            ->whereDate('date_off', '>=', $start->toDateString())
            ->whereDate('date_off', '<=', $finish->toDateString())
            ->get(['date_off', 'category'])
            ->groupBy(fn ($dayOff) => Carbon::parse($dayOff->date_off)->toDateString());

        $count = 0;
        for ($date = $start->copy(); $date->lte($finish); $date->addDay()) {
            $dateKey = $date->toDateString();
            $dateOff = $daysOff->get($dateKey, collect());
            $isWorkingOverride = $dateOff->contains('category', 'Working');
            $isDayOff = $dateOff->isNotEmpty();

            foreach ($shifts as $shift) {
                $cycleLength = (int) $shift->cycle_length;
                if ($cycleLength < 1) {
                    continue;
                }

                $cycleDay = (Carbon::parse($shift->start_implement)->diffInDays($date) % $cycleLength) + 1;
                $isScheduledWorkday = in_array(
                    $cycleDay,
                    $workCycles->get($shift->group_code, []),
                    true,
                );

                if (($isScheduledWorkday && !$isDayOff) || $isWorkingOverride) {
                    $count++;
                }
            }
        }

        return $count;
    }
}
