<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MobileInsideChecktimeController extends Controller
{
    public function index(Request $request)
    {
        $dates = $request->validate([
            'start_date' => ['nullable', 'required_with:end_date', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'required_with:start_date', 'date_format:Y-m-d'],
        ]);

        if (empty($dates['start_date']) !== empty($dates['end_date'])) {
            throw ValidationException::withMessages([
                'start_date' => 'Tanggal mulai dan tanggal akhir harus diisi bersama.',
            ]);
        }

        if (empty($dates['start_date'])) {
            $start = now()->subDays(7);
            $end = now();
        } else {
            $start = Carbon::createFromFormat('Y-m-d', $dates['start_date'])->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $dates['end_date']);

            if ($endDate->lt($start)) {
                throw ValidationException::withMessages([
                    'end_date' => 'Tanggal akhir tidak boleh sebelum tanggal mulai.',
                ]);
            }

            if ($endDate->gt($start->copy()->addDays(30))) {
                throw ValidationException::withMessages([
                    'end_date' => 'Rentang tanggal maksimal 31 hari.',
                ]);
            }

            $end = $endDate->endOfDay();
        }

        $user = $request->user();
        $nik = $user->nik;
        if (!$nik) {
            return response()->json(['message' => 'NIK akun ini tidak ditemukan.'], 404);
        }

        $employee = DB::table('tb_employees')
            ->where('NIK', $nik)
            ->first(['employee_name', 'badgenumber']);

        if (!$employee || trim((string) $employee->badgenumber) === '') {
            return response()->json(['message' => 'Data badge karyawan tidak ditemukan.'], 404);
        }

        $badgeNumber = str_pad(trim((string) $employee->badgenumber), 9, '0', STR_PAD_LEFT);
        $records = DB::table('tb_iclock')
            ->where('badgenumber', $badgeNumber)
            ->whereBetween('checktime', [$start, $end])
            ->orderByDesc('checktime')
            ->orderByDesc('id')
            ->get([
                'id',
                'userid',
                'name',
                'badgenumber',
                'checktime',
                'checktype',
                'WorkCode',
                'sensorid',
                'captured_date',
            ]);

        return response()->json([
            'employee_name' => $employee->employee_name,
            'badgenumber' => $badgeNumber,
            'start_date' => $start->toDateTimeString(),
            'end_date' => $end->toDateTimeString(),
            'records' => $records,
        ]);
    }
}
