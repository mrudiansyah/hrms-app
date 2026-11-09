<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use DateTime;

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

        $records = DB::table('tb_sick')
            ->leftJoin('tb_leaves', function ($join) {
                $join->on(DB::raw('TRY_CONVERT(int, [tb_leaves].[id_doc])'), '=', 'tb_sick.id')
                    ->where('tb_leaves.category', 'docter');
            })
            ->where('tb_sick.id_employee', $employee->id)
            ->orderByDesc('tb_sick.upload_time')
            ->orderByDesc('tb_sick.id')
            ->get([
                'tb_sick.id',
                'tb_sick.tanggal as start_leave',
                'tb_leaves.finish_leave',
                'tb_sick.diagnosa',
                'tb_sick.status',
                'tb_sick.upload_time',
                'tb_sick.skd',
                'tb_leaves.status_legalized',
            ]);
        $records->each(function ($record) {
            $record->has_photo = is_string($record->skd) && $record->skd !== '';
            unset($record->skd);
        });

        return response()->json([
            'employee' => [
                'name' => $employee->employee_name,
                'nik' => $employee->NIK,
                'department' => $employee->department,
            ],
            'diagnoses' => $diagnoses,
            'records' => $records,
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

    public function photo(Request $request, int $id)
    {
        $employee = $this->employeeFor($request);
        if (!$employee) {
            return response()->json(['message' => 'Data karyawan untuk akun ini tidak ditemukan.'], 404);
        }

        $record = DB::table('tb_sick')
            ->where('id', $id)
            ->where('id_employee', $employee->id)
            ->first(['skd']);
        if (!$record || !is_string($record->skd) || $record->skd === '') {
            return response()->json(['message' => 'Foto SKD tidak ditemukan.'], 404);
        }

        if (preg_match('/\Adata:(image\/(?:jpeg|png));base64,([A-Za-z0-9+\/=\r\n]+)\z/', $record->skd, $matches)) {
            $contents = base64_decode($matches[2], true);
            if ($contents === false) {
                return response()->json(['message' => 'Foto SKD tidak dapat dibaca.'], 404);
            }

            return response($contents, 200, [
                'Content-Type' => $matches[1],
                'Cache-Control' => 'private, no-store',
            ]);
        }

        $filename = basename($record->skd);
        if ($filename !== $record->skd) {
            return response()->json(['message' => 'Foto SKD tidak ditemukan.'], 404);
        }

        $storage = Storage::disk('public');
        $path = 'skd/' . $filename;
        if (!$storage->exists($path)) {
            return response()->json(['message' => 'Foto SKD tidak ditemukan.'], 404);
        }

        $mimeType = $storage->mimeType($path);
        if (!is_string($mimeType) || !in_array($mimeType, ['image/jpeg', 'image/png'], true)) {
            return response()->json(['message' => 'Dokumen SKD ini bukan foto yang dapat ditampilkan.'], 415);
        }

        return $storage->response($path, null, [
            'Cache-Control' => 'private, no-store',
        ], 'inline');
    }

    public function destroy(Request $request, int $id)
    {
        $employee = $this->employeeFor($request);
        if (!$employee) {
            return response()->json(['message' => 'Data karyawan untuk akun ini tidak ditemukan.'], 404);
        }

        $result = DB::transaction(function () use ($employee, $id) {
            $sick = DB::table('tb_sick')
                ->where('id', $id)
                ->where('id_employee', $employee->id)
                ->lockForUpdate()
                ->first(['id', 'skd']);
            if (!$sick) {
                return ['status' => 'missing'];
            }

            $leaves = DB::table('tb_leaves')
                ->whereRaw('TRY_CONVERT(int, [id_doc]) = ?', [$id])
                ->where('id_employee', $employee->id)
                ->where('category', 'docter')
                ->lockForUpdate()
                ->get(['id', 'status_legalized']);
            if ($leaves->isEmpty() || $leaves->contains(
                fn ($leave) => (string) $leave->status_legalized !== '0'
            )) {
                return ['status' => 'not_deletable'];
            }

            DB::table('tb_leaves')
                ->whereIn('id', $leaves->pluck('id'))
                ->delete();
            DB::table('tb_sick')
                ->where('id', $sick->id)
                ->where('id_employee', $employee->id)
                ->delete();

            return ['status' => 'deleted', 'filename' => $sick->skd];
        });

        if ($result['status'] === 'missing') {
            return response()->json(['message' => 'Data SKD tidak ditemukan.'], 404);
        }
        if ($result['status'] === 'not_deletable') {
            return response()->json([
                'message' => 'SKD hanya dapat dihapus sebelum dilegalisasi.',
            ], 409);
        }

        $filename = $result['filename'];
        if (is_string($filename) &&
            $filename !== '' &&
            !str_starts_with($filename, 'data:') &&
            basename($filename) === $filename) {
            $storage = Storage::disk('public');
            $path = 'skd/' . $filename;
            if ($storage->exists($path) && !$storage->delete($path)) {
                Log::warning('Mobile SKD file could not be deleted after record removal.', [
                    'employee_id' => $employee->id,
                    'skd_id' => $id,
                ]);
            }
        }

        return response()->json(['message' => 'Data SKD berhasil dihapus.']);
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
        $tgl1 = new DateTime($start);
        $tgl2 = new DateTime($finish);
        $diffdays = $tgl2->diff($tgl1)->days;
        $id_employee=$employeeId;
        $mulai=$start;
        $qty=0;
        for($i=0;$i<=$diffdays;$i++){
            $jml_leave=0;
            $Tgl=date('Y-m-d',strtotime($i.' days',strtotime($mulai)));
            $periode=date('Y-m',strtotime($Tgl));
            $d=date('d',strtotime($Tgl));
            if(strlen($d)==1)$d='0'.$d;
            $column='D'.$d;
            $id_work_time=DB::table('tb_work_entries')->where('id_employee',$id_employee)->where('periode',$periode)->value($column);
            $tb_freeday=DB::table('tb_freedays')->where('date_off',$Tgl)->count();
            $change_day=DB::table('tb_freedays')->where('date_off',$Tgl)->where('category','Working')->count();
            if(($id_work_time>0&&$tb_freeday==0)||($change_day>0))$qty++;
        }
        return $qty;
    }
}
