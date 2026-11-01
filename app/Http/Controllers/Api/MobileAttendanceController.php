<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MobileAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $employee = $this->employeeFor($request->user()->email);
        if (!$employee) {
            return response()->json(['message' => 'Data karyawan untuk akun ini tidak ditemukan.'], 404);
        }

        $records = DB::table('tb_checktimes')
            ->where('NIK', $employee->NIK)
            ->whereNotNull('checktime')
            ->orderByDesc('checktime')
            ->orderByDesc('id')
            ->limit(50)
            ->get([
                'id',
                'checktime',
                'status_checktime',
                'status_kerja',
                'latitut',
                'longitut',
            ]);

        return response()->json([
            'next_action' => $this->nextAction($records->first()),
            'records' => $records,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'status_kerja' => ['required', 'in:TL,DRIVER,WFO,WFH'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'photo' => ['required', 'string', 'max:1250000', 'regex:~^data:image/jpeg;base64,[A-Za-z0-9+/]+={0,2}$~'],
        ]);

        $photoData = substr($data['photo'], strlen('data:image/jpeg;base64,'));
        $photoBytes = base64_decode($photoData, true);
        $imageInfo = $photoBytes === false ? false : @getimagesizefromstring($photoBytes);

        if (
            $photoBytes === false
            || strlen($photoBytes) > 900000
            || $imageInfo === false
            || $imageInfo['mime'] !== 'image/jpeg'
        ) {
            throw ValidationException::withMessages([
                'photo' => 'Foto selfie harus berupa gambar JPEG yang valid dan berukuran maksimal 900 KB.',
            ]);
        }

        $employee = $this->employeeFor($request->user()->email);
        if (!$employee) {
            return response()->json(['message' => 'Data karyawan untuk akun ini tidak ditemukan.'], 404);
        }

        $record = DB::transaction(function () use ($data, $employee) {
            DB::table('tb_employees')
                ->where('id', $employee->id)
                ->lockForUpdate()
                ->first(['id']);

            $lastRecord = DB::table('tb_checktimes')
                ->where('NIK', $employee->NIK)
                ->whereNotNull('checktime')
                ->orderByDesc('checktime')
                ->orderByDesc('id')
                ->first(['id', 'checktime', 'status_checktime']);

            $action = $this->nextAction($lastRecord);
            $now = now();

            $id = DB::table('tb_checktimes')->insertGetId([
                'PIN' => $employee->PIN,
                'NIK' => $employee->NIK,
                'employee_name' => $employee->employee_name,
                'status_kerja' => $data['status_kerja'],
                'status_checktime' => $action,
                'checktime' => $now,
                'checker' => $employee->employee_name,
                'latitut' => $data['latitude'],
                'longitut' => $data['longitude'],
                'photo' => $data['photo'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            return [
                'id' => $id,
                'checktime' => $now->toDateTimeString(),
                'status_checktime' => $action,
                'status_kerja' => $data['status_kerja'],
                'latitut' => $data['latitude'],
                'longitut' => $data['longitude'],
            ];
        });

        return response()->json([
            'message' => $record['status_checktime'] === 'checkin'
                ? 'Berhasil check-in.'
                : 'Berhasil check-out.',
            'record' => $record,
            'next_action' => $record['status_checktime'] === 'checkin' ? 'checkout' : 'checkin',
        ], 201);
    }

    private function employeeFor(string $email): ?object
    {
        return DB::table('tb_emails')
            ->join('tb_employees', 'tb_employees.id', '=', 'tb_emails.id_employee')
            ->where('tb_emails.email_address', $email)
            ->first([
                'tb_employees.id',
                'tb_employees.PIN',
                'tb_employees.NIK',
                'tb_employees.employee_name',
            ]);
    }

    private function nextAction(?object $lastRecord): string
    {
        if (!$lastRecord || Carbon::parse($lastRecord->checktime)->lte(now()->subHours(20))) {
            return 'checkin';
        }

        return match (strtolower((string) $lastRecord->status_checktime)) {
            'checkin', 'in' => 'checkout',
            'checkout', 'out' => 'checkin',
            default => throw new \Symfony\Component\HttpKernel\Exception\HttpException(
                409,
                'Status absensi terakhir tidak dikenali. Silakan hubungi administrator.',
            ),
        };
    }
}
