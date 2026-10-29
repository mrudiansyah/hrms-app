<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class FunctionalityTestController extends Controller
{
    public function kskApprovalReminders()
    {
        abort_unless(request()->user()->hasRole('root'), 403, 'Anda tidak punya akses');

        $kskRecords = DB::table('tb_ksk')
            ->where('tb_ksk.is_delete', '0')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('tb_ksk_lock')
                    ->whereColumn('tb_ksk_lock.periode', 'tb_ksk.periode')
                    ->where('tb_ksk_lock.is_lock', '<>', 0);
            })
            ->orderBy('tb_ksk.dead_line')
            ->orderBy('tb_ksk.no_ksk')
            ->get([
                'tb_ksk.no_ksk',
                'tb_ksk.id',
                'tb_ksk.periode',
                'tb_ksk.dead_line',
                'tb_ksk.approval1',
                'tb_ksk.approval2',
                'tb_ksk.approval3',
                'tb_ksk.approval4',
                'tb_ksk.approval5',
                'tb_ksk.approval6',
                'tb_ksk.approval1_status',
                'tb_ksk.approval2_status',
                'tb_ksk.approval3_status',
                'tb_ksk.approval4_status',
                'tb_ksk.approval5_status',
                'tb_ksk.approval6_status',
            ]);

        $pendingRecords = collect();
        foreach ($kskRecords as $record) {
            for ($step = 1; $step <= 6; $step++) {
                $approverId = $record->{'approval' . $step};
                $approvalStatus = $record->{'approval' . $step . '_status'};

                if ($approverId !== null && (int) $approverId > 0
                    && $approvalStatus !== null && (int) $approvalStatus === 0) {
                    $record->approver_id = $approverId;
                    $pendingRecords->push($record);
                    break;
                }
            }
        }

        $employeeIds = $pendingRecords->pluck('approver_id')->unique()->values();
        $employees = DB::table('tb_employees')
            ->leftJoin('tb_employee_detail', 'tb_employee_detail.id_employee', '=', 'tb_employees.id')
            ->leftJoin('tb_positions', 'tb_positions.id', '=', 'tb_employees.position_id')
            ->whereIn('tb_employees.id', $employeeIds)
            ->get([
                'tb_employees.id',
                'tb_employees.employee_name',
                'tb_positions.position_name',
                'tb_employee_detail.nomor_telepon',
            ])
            ->keyBy('id');

        return view('functionality_test.ksk_approval_reminders', [
            'records' => $pendingRecords,
            'employees' => $employees,
            'menu' => 'functionality_test',
            'Judul' => 'Functionality Test',
        ]);
    }

    public function sendKskReminder(int $id): RedirectResponse
    {
        abort_unless(request()->user()->hasRole('root'), 403, 'Anda tidak punya akses');

        $record = DB::table('tb_ksk')
            ->where('tb_ksk.id', $id)
            ->where('tb_ksk.is_delete', '0')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('tb_ksk_lock')
                    ->whereColumn('tb_ksk_lock.periode', 'tb_ksk.periode')
                    ->where('tb_ksk_lock.is_lock', '<>', 0);
            })
            ->first([
                'tb_ksk.id',
                'tb_ksk.no_ksk',
                'tb_ksk.periode',
                'tb_ksk.dead_line',
                'tb_ksk.approval1',
                'tb_ksk.approval2',
                'tb_ksk.approval3',
                'tb_ksk.approval4',
                'tb_ksk.approval5',
                'tb_ksk.approval6',
                'tb_ksk.approval1_status',
                'tb_ksk.approval2_status',
                'tb_ksk.approval3_status',
                'tb_ksk.approval4_status',
                'tb_ksk.approval5_status',
                'tb_ksk.approval6_status',
            ]);

        if (!$record) {
            return redirect('/functionality-test')->with('error', 'Data KSK tidak ditemukan atau periodenya sudah terkunci.');
        }

        $approverId = null;
        for ($step = 1; $step <= 6; $step++) {
            $candidateId = $record->{'approval' . $step};
            $approvalStatus = $record->{'approval' . $step . '_status'};

            if ($candidateId !== null && (int) $candidateId > 0
                && $approvalStatus !== null && (int) $approvalStatus === 0) {
                $approverId = $candidateId;
                break;
            }
        }

        if ($approverId === null) {
            return redirect('/functionality-test')->with('error', 'Tidak ada approval yang sedang menunggu untuk KSK ini.');
        }

        $phone = DB::table('tb_employee_detail')
            ->where('id_employee', $approverId)
            ->value('nomor_telepon');

        if (!$phone) {
            return redirect('/functionality-test')->with('error', 'Nomor telepon approver tidak ditemukan.');
        }

        $message = "*NOTIFIKASI KSK*\n\n"
            . "No. KSK: {$record->no_ksk}\n"
            . "Periode: {$record->periode}\n"
            . "Deadline: {$record->dead_line}\n"
            . "Mohon segera melakukan approval melalui sistem HRMS. klik link berikut:\nhttps://hrms.summitadyawinsa.co.id/Employee/KSK/Detail/{$record->id}/{$record->periode}";

        $result = WuzapiController::sendInternalMessage($phone, $message);
        if (($result['success'] ?? true) !== true) {
            Log::warning('KSK approval reminder failed to send.', [
                'ksk_id' => $id,
                'approver_id' => $approverId,
                'result' => $result,
            ]);

            return redirect('/functionality-test')->with('error', 'Gagal mengirim reminder WhatsApp.');
        }

        return redirect('/functionality-test')->with('success', 'Reminder WhatsApp untuk KSK ' . $record->no_ksk . ' berhasil dikirim.');
    }
}