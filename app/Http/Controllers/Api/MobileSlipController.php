<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MobileSlipController extends Controller
{
    public function salary(Request $request)
    {
        $employee = $this->employeeFor($request);
        if (!$employee) {
            return response()->json(['message' => 'Data karyawan untuk akun ini tidak ditemukan.'], 404);
        }

        $current = DB::table('tb_salary_summary')
            ->where('id_employee', $employee->id)
            ->where('mail_status', '1')
            ->orderByDesc('periode')
            ->get();

        $previous = DB::table('tb_salary_excel')
            ->where('NIK', $employee->NIK)
            ->where('send_mail', '1')
            ->orderByDesc('periode')
            ->limit(6)
            ->get();

        return response()->json([
            'current' => $current->map(fn ($slip) => [
                'id' => (string) $slip->id,
                'period' => (string) $slip->periode,
                'earnings' => $this->sum($slip, [
                    'upah_pokok',
                    'tunjangan_jabatan',
                    'tunjangan_prestasi_tahunan',
                    'tunjangan_skill',
                    'rapel',
                    'insentif_pph21',
                    'pph21_lebih_bayar',
                    'tunjangan_shift',
                    'tunjangan_transport',
                ]),
                'deductions' => $this->sum($slip, [
                    'ketidakhadiran',
                    'standby_oncall',
                    'pph21_ter',
                    'jaminan_hari_tua',
                    'jaminan_pensiun',
                    'bpjs_kesehatan',
                    'serikat',
                    'koperasi',
                    'lain_lain',
                    'pph21_kurang_bayar',
                ]),
            ])->values(),
            'previous' => $previous->map(fn ($slip) => [
                'id' => (string) $slip->id,
                'period' => (string) $slip->Periode,
                'earnings' => $this->sum($slip, [
                    'Upah_Pokok',
                    'Tunjangan_Jabatan',
                    'Tunjangan_Prestasi',
                    'Tunjangan_Skill',
                    'Rapel',
                    'PPH21_Insentif',
                    'PPH21_Lebih_Bayar',
                    'tunjangan_shift',
                    'tunjangan_transport',
                    'tunjangan_masa_kerja',
                ]),
                'deductions' => $this->sum($slip, [
                    'Ketidakhadiran_Ammount',
                    'Standby_OnCall',
                    'PPH21',
                    'BPJS_JHT',
                    'BPJS_Pensiun',
                    'BPJS_Kesehatan',
                    'Serikat',
                    'Koperasi',
                    'Lain_Lain',
                    'PPH21_Kurang_Bayar',
                ]),
            ])->values(),
        ]);
    }

    public function overtime(Request $request)
    {
        $employee = $this->employeeFor($request);
        if (!$employee) {
            return response()->json(['message' => 'Data karyawan untuk akun ini tidak ditemukan.'], 404);
        }

        $current = DB::table('tb_ot_summary')
            ->where('id_employee', $employee->id)
            ->where('distribute', '1')
            ->orderByDesc('periode')
            ->get([
                'id',
                'periode',
                'kategori',
                'start_date',
                'end_date',
                'ot_amount',
                'meal_amount',
                'rapel_amount',
                'pph21_amount',
                'net_amount',
            ]);

        $previous = DB::table('tb_summary_overtime')
            ->leftJoin('tb_rapel_khusus', 'tb_rapel_khusus.id_summary', '=', 'tb_summary_overtime.id')
            ->where('tb_summary_overtime.send_mail', '1')
            ->where('tb_summary_overtime.id_employee', $employee->id)
            ->where('tb_summary_overtime.show_status', '1')
            ->where(function ($query) {
                $query->where('tb_summary_overtime.total_paid', '>', 0)
                    ->orWhere('tb_rapel_khusus.rapel_total', '>', 0);
            })
            ->orderByDesc('tb_summary_overtime.periode')
            ->limit(6)
            ->get([
                'tb_summary_overtime.id',
                'tb_summary_overtime.periode',
                'tb_summary_overtime.total_bayars',
                'tb_summary_overtime.pph21',
                'tb_summary_overtime.rapel',
                'tb_rapel_khusus.pph21_rapel as pph21_rapel',
                'tb_rapel_khusus.rapel_total',
            ]);

        return response()->json([
            'current' => $current->map(fn ($slip) => [
                'id' => (string) $slip->id,
                'period' => (string) $slip->periode,
                'category' => (string) $slip->kategori,
                'start_date' => (string) $slip->start_date,
                'end_date' => (string) $slip->end_date,
                'earnings' => $this->sum($slip, ['ot_amount', 'meal_amount']),
                'deductions' => (float) ($slip->pph21_amount ?? 0),
                'received' => (float) ($slip->net_amount ?? 0),
                'rapel' => (float) ($slip->rapel_amount ?? 0),
            ])->values(),
            'previous' => $previous->map(fn ($slip) => [
                'id' => (string) $slip->id,
                'period' => (string) $slip->periode,
                'category' => 'Previous Slip',
                'earnings' => (float) ($slip->total_bayars ?? 0),
                'deductions' => $this->sum($slip, ['pph21', 'pph21_rapel']),
                'received' => (float) ($slip->total_bayars ?? 0)
                    + (float) ($slip->rapel ?? 0)
                    - $this->sum($slip, ['pph21', 'pph21_rapel']),
                'rapel' => (float) ($slip->rapel ?? 0),
            ])->values(),
        ]);
    }

    public function salaryPdf(Request $request, string $source, string $id)
    {
        $employee = $this->employeeFor($request);
        if (!$employee) {
            return response()->json(['message' => 'Data karyawan untuk akun ini tidak ditemukan.'], 404);
        }

        if ($source === 'current') {
            $slip = DB::table('tb_salary_summary')
                ->where('id', $id)
                ->where('id_employee', $employee->id)
                ->where('mail_status', '1')
                ->first(['id', 'periode']);
        } else {
            $slip = DB::table('tb_salary_excel')
                ->where('id', $id)
                ->where('NIK', $employee->NIK)
                ->where('send_mail', '1')
                ->first(['id', 'Periode']);
        }

        if (!$slip) {
            return response()->json(['message' => 'Slip tidak ditemukan atau belum dipublikasikan.'], 404);
        }

        $period = $source === 'current' ? $slip->periode : $slip->Periode;
        $date = Carbon::parse($period . '-01');
        $periodText = $date->format('Y F');
        $year = $date->format('Y');

        if ($source === 'current') {
            $summary = DB::table('tb_salary_summary')
                ->leftJoin('tb_salary_contract', 'tb_salary_contract.id_employee', '=', 'tb_salary_summary.id_employee')
                ->where('tb_salary_summary.id', $id)
                ->where('tb_salary_summary.id_employee', $employee->id)
                ->where('tb_salary_summary.mail_status', '1')
                ->get([
                    'tb_salary_summary.*',
                    'tb_salary_contract.NIK',
                    'tb_salary_contract.nama_karyawan',
                    'tb_salary_contract.department as dept_code',
                    'tb_salary_contract.jabatan as nama_jabatan',
                    'tb_salary_contract.status_pajak',
                    'tb_salary_contract.total_salary',
                    'tb_salary_contract.tipe_kontrak',
                    'tb_salary_contract.upah_harian as upah_psab',
                    'tb_salary_contract.tunjangan_harian as tunjangan_pkl',
                ]);
            $contractType = $summary->first()?->tipe_kontrak;
            $view = match ($contractType) {
                'SAI' => 'page_ess.salary_slip_sai',
                'MAGANG' => 'page_ess.salary_slip_magang',
                default => null,
            };

            if ($view === null) {
                return response()->json(['message' => 'Format PDF untuk jenis kontrak ini belum tersedia.'], 422);
            }

            return Pdf::loadView($view, [
                'tb_salary_summary_employee' => $summary,
                'periode_text' => $periodText,
                'thn' => $year,
                'menu' => 'salary_slip',
            ])->setPaper([0, 0, 280, 630])
                ->download("salary_slip_{$period}.pdf");
        }

        $isIntern = DB::table('tb_employees')
            ->where('id', $employee->id)
            ->where('position_id', 19)
            ->where('status', '1')
            ->exists();
        $periodNumber = $date->format('m');
        $startOfMonth = $date->copy()->startOfMonth()->toDateString();
        $endOfMonth = $date->copy()->endOfMonth()->toDateString();
        $view = $isIntern ? 'page_ess.slip_gaji_magang' : 'page_ess.slip_gaji_mail';

        return Pdf::loadView($view, [
            'tb_slip' => DB::table('tb_salary_excel')
                ->where('id', $id)
                ->where('NIK', $employee->NIK)
                ->where('send_mail', '1')
                ->get(),
            'thn' => $year,
            'bln' => $periodNumber,
            'periode_text' => $periodText,
            'akhirbulan' => $endOfMonth,
            'periode' => $period,
            'hariawal' => $startOfMonth,
            'NIK' => $employee->NIK,
            'menu' => 'salary_slip',
        ])->setPaper([0, 0, 280, 620])
            ->download("salary_slip_{$period}.pdf");
    }

    public function overtimePdf(Request $request, string $source, string $id)
    {
        $employee = $this->employeeFor($request);
        if (!$employee) {
            return response()->json(['message' => 'Data karyawan untuk akun ini tidak ditemukan.'], 404);
        }

        if ($source === 'current') {
            $slip = DB::table('tb_ot_summary')
                ->where('id', $id)
                ->where('id_employee', $employee->id)
                ->where('distribute', '1')
                ->first();

            if (!$slip) {
                return response()->json(['message' => 'Slip tidak ditemukan atau belum dipublikasikan.'], 404);
            }

            $earnings = (float) ($slip->ot_amount ?? 0) + (float) ($slip->meal_amount ?? 0);
            $deductions = (float) ($slip->pph21_amount ?? 0);
            $rapel = (float) ($slip->rapel_amount ?? 0);
            $received = (float) ($slip->net_amount ?? 0);
            $category = (string) $slip->kategori;
            $startDate = (string) $slip->start_date;
            $endDate = (string) $slip->end_date;
            $period = (string) $slip->periode;
        } else {
            $slip = DB::table('tb_summary_overtime')
                ->leftJoin('tb_rapel_khusus', 'tb_rapel_khusus.id_summary', '=', 'tb_summary_overtime.id')
                ->where('tb_summary_overtime.id', $id)
                ->where('tb_summary_overtime.id_employee', $employee->id)
                ->where('tb_summary_overtime.send_mail', '1')
                ->where('tb_summary_overtime.show_status', '1')
                ->where(function ($query) {
                    $query->where('tb_summary_overtime.total_paid', '>', 0)
                        ->orWhere('tb_rapel_khusus.rapel_total', '>', 0);
                })
                ->first([
                    'tb_summary_overtime.periode',
                    'tb_summary_overtime.total_bayars',
                    'tb_summary_overtime.pph21',
                    'tb_summary_overtime.rapel',
                    'tb_rapel_khusus.pph21_rapel as pph21_rapel',
                ]);

            if (!$slip) {
                return response()->json(['message' => 'Slip tidak ditemukan atau belum dipublikasikan.'], 404);
            }

            $earnings = (float) ($slip->total_bayars ?? 0);
            $deductions = (float) ($slip->pph21 ?? 0) + (float) ($slip->pph21_rapel ?? 0);
            $rapel = (float) ($slip->rapel ?? 0);
            $received = $earnings + $rapel - $deductions;
            $category = 'Previous Slip';
            $period = (string) $slip->periode;
            $startDate = null;
            $endDate = null;
        }

        return Pdf::loadView('page_ess.mobile_overtime_slip', [
            'employee' => $employee,
            'period' => $period,
            'category' => $category,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'earnings' => $earnings,
            'deductions' => $deductions,
            'rapel' => $rapel,
            'received' => $received,
        ])->setPaper('A5', 'portrait')
            ->download("overtime_slip_{$period}.pdf");
    }

    private function employeeFor(Request $request): ?object
    {
        abort_unless(
            $request->user()->hasRole('root') || $request->user()->hasRole('ess'),
            403,
            'Anda tidak punya akses',
        );

        return DB::table('tb_emails')
            ->join('tb_employees', 'tb_employees.id', '=', 'tb_emails.id_employee')
            ->where('tb_emails.email_address', $request->user()->email)
            ->first([
                'tb_employees.id',
                'tb_employees.NIK',
                'tb_employees.employee_name',
            ]);
    }

    private function sum(object $record, array $columns): float
    {
        return array_reduce(
            $columns,
            fn (float $total, string $column) => $total + (float) ($record->{$column} ?? 0),
            0.0,
        );
    }
}
