<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Auth;
use PDF;

class PayrollController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['auth', 'verified']);
    }
    public function summary_overtime(Request $request, $start = null, $end = null)
    {
        $start = $start ?? $request->start;
        $end = $end ?? $request->end;

        if (!$start || !$end || $start == '0' || $end == '0') {
            $today = date('Y-m-d');
            $periode = date('Y-m', strtotime($today));
            $end = date('Y-m-d', strtotime($periode . '-24'));
            $awal_bulan = date('Y-m-d', strtotime($periode . '-01'));
            $akhir_bulan = date('Y-m', strtotime('-1 days', strtotime($awal_bulan)));
            $start = date('Y-m-d', strtotime($akhir_bulan . '-25'));
        }

        $tb1 = $this->get_summary_data($start, $end);
        $menu = 'overtime_summary';

        return view('payroll.summary_overtime', compact('tb1', 'start', 'end', 'menu'));
    }

    public function tax_overtime(Request $request, $start = null, $end = null)
    {
        $start = $start ?? $request->start;
        $end = $end ?? $request->end;

        if (!$start || !$end || $start == '0' || $end == '0') {
            $today = date('Y-m-d');
            $periode = date('Y-m', strtotime($today));
            $end = date('Y-m-d', strtotime($periode . '-24'));
            $awal_bulan = date('Y-m-d', strtotime($periode . '-01'));
            $akhir_bulan = date('Y-m', strtotime('-1 days', strtotime($awal_bulan)));
            $start = date('Y-m-d', strtotime($akhir_bulan . '-25'));
        }

        $tb1 = DB::table('tb_ot_summary')
            ->leftjoin('tb_employee_detail as a', 'a.id_employee', '=', 'tb_ot_summary.id_employee')
            ->leftjoin('tb_employees as b', 'b.id', '=', 'tb_ot_summary.id_employee')
            ->leftjoin('tb_positions as c', 'c.id', '=', 'b.position_id')
            ->where('start_date', $start)
            ->where('end_date', $end)
            ->where('kategori', 'Overtime')
            ->orderby('dept_code', 'asc')
            ->orderby('employee_name', 'asc')
            ->get(['tb_ot_summary.*', 'a.nomor_rekening', 'b.cc_code', 'c.position_name']);

        $menu = 'overtime_tax';

        return view('payroll.tax_overtime', compact('tb1', 'start', 'end', 'menu'));
    }
    public function tax_overtime_excel(Request $request, $start = null, $end = null)
    {
        $start = $start ?? $request->start;
        $end = $end ?? $request->end;

        if (!$start || !$end || $start == '0' || $end == '0') {
            $today = date('Y-m-d');
            $periode = date('Y-m', strtotime($today));
            $end = date('Y-m-d', strtotime($periode . '-24'));
            $awal_bulan = date('Y-m-d', strtotime($periode . '-01'));
            $akhir_bulan = date('Y-m', strtotime('-1 days', strtotime($awal_bulan)));
            $start = date('Y-m-d', strtotime($akhir_bulan . '-25'));
        }

        $tb1 = DB::table('tb_ot_summary')
            ->leftjoin('tb_employee_detail as a', 'a.id_employee', '=', 'tb_ot_summary.id_employee')
            ->leftjoin('tb_employees as b', 'b.id', '=', 'tb_ot_summary.id_employee')
            ->leftjoin('tb_positions as c', 'c.id', '=', 'b.position_id')
            ->where('start_date', $start)
            ->where('end_date', $end)
            ->where('kategori', 'Overtime')
            ->orderby('dept_code', 'asc')
            ->orderby('employee_name', 'asc')
            ->get(['tb_ot_summary.*', 'a.nomor_rekening', 'b.cc_code', 'c.position_name']);

        $menu = 'overtime_tax';

        return view('payroll.tax_overtime_excel', compact('tb1', 'start', 'end', 'menu'));
    }

    public function distribute_spl_slip($start = null, $end = null)
    {
        $update = DB::table('tb_ot_summary')
            ->where('start_date', $start)
            ->where('end_date', $end)
            ->where('kategori', 'Overtime')
            ->update([
                'distribute' => '1',
            ]);
        if ($update)
            return redirect()->back()->with('success', 'Data berhasil didistribusikan.');
        else
            return redirect()->back()->with('error', 'Data gagal didistribusikan.');
    }

    private function get_summary_data($start, $end)
    {
        $amt_driver = DB::table('tb_salary_component')->where('id', '39')->value('amount') ?? 0;

        return DB::table('tb_overtime_details as a')
            ->leftjoin('tb_employees as b', 'b.id', 'a.id_employee')
            ->leftjoin('tb_departments as c', 'c.id', 'b.dept_id')
            ->leftJoin('tb_salaries as d', function ($join) {
                $join->on('d.id_employee', '=', 'b.id')
                    ->where('d.status', 1);
            })
            ->select(
                'b.id',
                'b.NIK',
                'b.employee_name',
                'c.dept_code',
                'b.position_id',
                'd.slpj',
                DB::raw('COUNT(*) as frekuensi'),
                DB::raw('SUM(a.hours_act) as hours_act'),
                DB::raw('SUM(a.hours_convertion) as hours_convertion'),
                DB::raw("SUM(CASE WHEN b.position_id = '18' THEN $amt_driver ELSE (d.slpj * a.hours_convertion) END) as total_overtime")
            )
            ->selectSub(function ($query) use ($start, $end) {
                $query->from('tb_meals')
                    ->selectRaw('SUM(meal)')
                    ->where('date_on', '>=', $start)
                    ->where('date_on', '<=', $end)
                    ->whereColumn('id_employee', 'b.id')
                    ->where('is_deleted', '0');
            }, 'total_meal')
            ->where('a.date_on', '>=', $start)
            ->where('a.date_on', '<=', $end)
            ->where('a.status', '6')
            ->groupBy('b.id', 'b.NIK', 'b.employee_name', 'c.dept_code', 'b.position_id', 'd.slpj')
            ->orderby('c.dept_code', 'asc')
            ->orderby('b.employee_name', 'asc')
            ->get()
            ->map(function ($item) {
                $item->total_bayar = $item->total_overtime + $item->total_meal;
                return $item;
            });
    }

    public function save_summary(Request $request)
    {
        $start = $request->start;
        $end = $request->end;
        $periode = date('Y-m', strtotime($end));
        if (!$start || !$end) {
            return redirect()->back()->with('error', 'Start and End dates are required.');
        }

        $data = $this->get_summary_data($start, $end);

        foreach ($data as $row) {
            DB::table('tb_ot_summary')->updateOrInsert(
                [
                    'id_employee' => $row->id,
                    'periode' => $periode,
                    'start_date' => $start,
                    'end_date' => $end,
                    'kategori' => 'Overtime',
                ],
                [
                    'nik' => $row->NIK,
                    'employee_name' => $row->employee_name,
                    'dept_code' => $row->dept_code,
                    'slpj' => $row->slpj,
                    'hours_act' => $row->hours_act,
                    'hour_convertion' => $row->hours_convertion,
                    'ot_amount' => $row->total_overtime,
                    'meal_amount' => $row->total_meal,
                    'gross_amount' => $row->total_bayar,
                    'updated_at' => now(),
                ]
            );
        }
        $update_capture = DB::table('tb_overtime_details')->where('date_on', '>=', $start)->where('date_on', '<=', $end)->where('status', '6')->update([
            'captured_at' => now(),
        ]);

        return redirect('/payroll/tax_overtime/' . $start . '/' . $end)->with('success', 'Summary data has been saved to tb_ot_summary.');
    }


    public function import_rapel(Request $request)
    {
        if (!$request->hasFile('file')) {
            return redirect()->back()->with('error', 'Excel file is required.');
        }

        $array = Excel::toArray([], $request->file('file'))[0];
        // Skip header if it is likely a header row (check if column C is numeric)
        $startRow = (isset($array[0][2]) && is_numeric($array[0][2])) ? 0 : 1;

        $updatedCount = 0;
        $insertedCount = 0;
        for ($i = $startRow; $i < count($array); $i++) {
            $nik = $array[$i][0];
            $periode = $array[$i][1];
            $rapel = (float) ($array[$i][2] ?? 0);

            if ($nik && $periode) {
                $exists = DB::table('tb_ot_summary')
                    ->where('nik', $nik)
                    ->where('periode', $periode)
                    ->exists();

                if ($exists) {
                    $affected = DB::table('tb_ot_summary')
                        ->where('nik', $nik)
                        ->where('periode', $periode)
                        ->update([
                            'rapel_amount' => $rapel,
                            'gross_amount' => DB::raw("IFNULL(ot_amount, 0) + IFNULL(meal_amount, 0) + $rapel"),
                            'updated_at' => now()
                        ]);
                    if ($affected)
                        $updatedCount++;
                } else {
                    // Fetch employee details
                    $employee = DB::table('tb_employees as b')
                        ->leftJoin('tb_departments as c', 'c.id', 'b.dept_id')
                        ->leftJoin('tb_salaries as d', function ($join) {
                            $join->on('d.id_employee', '=', 'b.id')
                                ->where('d.status', 1);
                        })
                        ->where('b.NIK', $nik)
                        ->select('b.id', 'b.NIK', 'b.employee_name', 'c.dept_code', 'd.slpj')
                        ->first();

                    if ($employee) {
                        // Calculate start_date and end_date
                        $end_date = date('Y-m-d', strtotime($periode . '-24'));
                        $awal_bulan = date('Y-m-d', strtotime($periode . '-01'));
                        $akhir_bulan = date('Y-m', strtotime('-1 days', strtotime($awal_bulan)));
                        $start_date = date('Y-m-d', strtotime($akhir_bulan . '-25'));

                        DB::table('tb_ot_summary')->insert([
                            'id_employee' => $employee->id,
                            'periode' => $periode,
                            'start_date' => $start_date,
                            'end_date' => $end_date,
                            'nik' => $employee->NIK,
                            'employee_name' => $employee->employee_name,
                            'dept_code' => $employee->dept_code,
                            'slpj' => $employee->slpj,
                            'rapel_amount' => $rapel,
                            'gross_amount' => $rapel,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        $insertedCount++;
                    }
                }
            }
        }

        return redirect()->back()->with('success', $updatedCount . ' records updated, ' . $insertedCount . ' records inserted successfully.');
    }

    public function download_format_rapel()
    {
        return Excel::download(new \App\Exports\RapelFormatExport, 'format_import_rapel.xlsx');
    }
    public function collect_meals(Request $request)
    {
        $now = date('Y-m-d H:i:s');
        $admin = Auth::user()->name;
        $start = $request->start;
        $end = $request->end;
        $periode = date('Y-m', strtotime($end));
        $fiscal_year = date('Y', strtotime($end));
        if (!$start || !$end) {
            return redirect()->back()->with('error', 'Start and End dates are required.');
        }

        $data = DB::table('tb_meals')
            ->leftjoin('tb_employees', 'tb_employees.id', '=', 'tb_meals.id_employee')
            ->leftjoin('tb_departments', 'tb_departments.id', '=', 'tb_employees.dept_id')
            ->leftJoin('tb_salaries', function ($join) {
                $join->on('tb_salaries.id_employee', '=', 'tb_employees.id')
                    ->where('tb_salaries.status', 1);
            })
            ->selectRaw('tb_meals.id_employee,NIK,employee_name,dept_code,slpj, SUM(tb_meals.meal) as meals')
            ->where('date_on', '>=', $start)
            ->where('date_on', '<=', $end)
            ->where('tb_meals.is_deleted', '0')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('tb_ot_summary')
                    ->whereRaw('tb_ot_summary.id_employee = tb_meals.id_employee');
            })
            ->groupBy('tb_meals.id_employee', 'NIK', 'employee_name', 'dept_code', 'slpj')
            ->limit(2)
            ->get();

        foreach ($data as $row) {
            DB::table('tb_ot_summary')->insert([
                'id_employee' => $row->id_employee,
                'periode' => $periode,
                'start_date' => $start,
                'end_date' => $end,
                'nik' => $row->NIK,
                'employee_name' => $row->employee_name,
                'dept_code' => $row->dept_code,
                'slpj' => $row->slpj,
                'meal_amount' => $row->meals,
                'gross_amount' => $row->meals,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Summary data has been saved to tb_ot_summary.');
    }

    public function tax_overtime_calculation(Request $request)
    {
        $now = date('Y-m-d H:i:s');
        $admin = Auth::user()->name;
        $start = $request->start;
        $end = $request->end;
        $periode = date('Y-m', strtotime($end));
        $fiscal_year = date('Y', strtotime($end));
        if (!$start || !$end) {
            return redirect()->back()->with('error', 'Start and End dates are required.');
        }

        $data = DB::table('tb_ot_summary')
            ->where('start_date', $start)
            ->where('end_date', $end)
            ->where('kategori', 'Overtime')
            ->get();

        foreach ($data as $row) {
            //MyScript
            $tb_salary_contract_employee = $this->tb_salary_contract_employee($row->id_employee, '0');
            foreach ($tb_salary_contract_employee as $dt) {
                $category = $dt->kategori;
                $status_pajak = $dt->status_pajak;
            }
            $penghasilan_bruto_pajak = $row->gross_amount;
            $cum = $this->cum_salary_tax_ter($periode, $row->id_employee, 'tb_salary_earn_additional', 'overtime');
            $cum_tax = $cum + $penghasilan_bruto_pajak;
            $prev = $cum_tax - $penghasilan_bruto_pajak;
            $tb_pph21_ter = $this->tb_pph21_ter($category, $cum_tax);
            foreach ($tb_pph21_ter as $dt2) {
                $ter_id = $dt2->id;
                $ter = $dt2->prosentase;
                $pph21_ter = ($ter * $cum_tax / 100);
            }
            $periksa_tax = $this->qty_salary_tax_ter($periode, $row->id_employee, 'tb_salary_earn_additional', 'overtime');
            $prev_tax = $this->prev_salary_tax_ter($periode, $row->id_employee, 'tb_salary_earn_additional', 'overtime');
            $balance_amount = $pph21_ter - $prev_tax;
            //end tax calculation
            if ($periksa_tax == 0) {
                $lock = DB::table('tb_salary_ter_monthly')->where('periode', $periode)->where('id_employee', $row->id_employee)->update(['locked' => '1']);
                $update_tax = DB::table('tb_salary_ter_monthly')->insert([
                    'id_employee' => $row->id_employee,
                    'fiscal_year' => $fiscal_year,
                    'periode' => $periode,
                    'related_table' => 'tb_salary_earn_additional',
                    'category' => 'overtime',
                    'previous_payment' => $prev_tax,
                    'monthly_salary' => '0',
                    'prev_amount' => $prev,
                    'current_amount' => $penghasilan_bruto_pajak,
                    'cumulative_amount' => $cum_tax,
                    'status_pajak' => $status_pajak,
                    'ter_id' => $ter_id,
                    'prosentase' => $ter,
                    'tax_amount' => $pph21_ter,
                    'balance_amount' => $balance_amount,
                    'admin' => $admin,
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
            } else {
                $update_tax = DB::table('tb_salary_ter_monthly')
                    ->where('periode', $periode)
                    ->where('id_employee', $row->id_employee)
                    ->where('related_table', 'tb_salary_earn_additional')
                    ->where('category', 'overtime')
                    ->where('locked', '0')
                    ->update([
                        'id_employee' => $row->id_employee,
                        'fiscal_year' => $fiscal_year,
                        'periode' => $periode,
                        'related_table' => 'tb_salary_earn_additional',
                        'category' => 'overtime',
                        'previous_payment' => $prev_tax,
                        'prev_amount' => $prev,
                        'current_amount' => $penghasilan_bruto_pajak,
                        'cumulative_amount' => $cum_tax,
                        'status_pajak' => $status_pajak,
                        'ter_id' => $ter_id,
                        'prosentase' => $ter,
                        'tax_amount' => $pph21_ter,
                        'balance_amount' => $balance_amount,
                        'admin' => $admin,
                        'updated_at' => $now
                    ]);
            }
            //EndMyScrip

            if ($update_tax) {
                $paid = $row->gross_amount - $balance_amount;
                DB::table('tb_ot_summary')->where('id', $row->id)->update([
                    'pph21_amount' => $balance_amount,
                    'net_amount' => $paid,
                    'updated_at' => now(),
                ]);
            }
        }

        return redirect()->back()->with('success', 'Summary data has been saved to tb_ot_summary.');
    }
    public function tb_salary_contract_employee($id_employee)
    {
        $tabel = DB::table('tb_salary_contract')
            ->leftjoin('tb_pph21_status', 'tb_pph21_status.id', '=', 'tb_salary_contract.status_pajak')
            ->where('id_employee', $id_employee)
            ->get(['tb_salary_contract.*', 'tb_pph21_status.status_pajak as statuspajak', 'tb_pph21_status.ptkp', 'tb_pph21_status.kategori']);
        return $tabel;
    }
    public function cum_salary_tax_ter($periode, $id_employee, $related, $category)
    {
        $tb1 = DB::table('tb_salary_ter_monthly')
            ->where('periode', $periode)
            ->where('id_employee', $id_employee)
            ->where('related_table', $related)
            ->where('category', $category)
            ->value('created_at');

        if (!$tb1) {
            $tb1 = date('Y-m-d H:i:s');
        }

        $tabel = DB::table('tb_salary_ter_monthly')
            ->where('periode', $periode)
            ->where('id_employee', $id_employee)
            ->where('created_at', '<', $tb1)
            ->get();
        $total_tax_amount = $tabel->sum('current_amount');
        return $total_tax_amount;
    }
    public function tb_pph21_ter($kategori, $bruto)
    {
        $tabel = DB::table('tb_pph21_ter')->where('ter', $kategori)->where('batas_bawah', '<', $bruto)->where('batas_atas', '>=', $bruto)->get();
        return $tabel;
    }
    public function qty_salary_tax_ter($periode, $id_employee, $related, $category)
    {
        $tabel = DB::table('tb_salary_ter_monthly')
            ->where('periode', $periode)
            ->where('id_employee', $id_employee)
            ->where('related_table', $related)
            ->where('category', $category)
            ->count();
        return $tabel;
    }
    public function prev_salary_tax_ter($periode, $id_employee, $related, $category)
    {
        $tabel = DB::table('tb_salary_ter_monthly')
            ->where('periode', $periode)
            ->where('id_employee', $id_employee)
            ->where('related_table', '<>', $related)
            ->where('category', '<>', $category)
            ->get();
        $total_tax_amount = $tabel->sum('tax_amount');
        return $total_tax_amount;
    }
    public function slip_overtime($start, $end, $id_employee)
    {
        if (request()->user()->hasRole('payroll')) {
            $tabel = DB::table('tb_overtime_details')
                ->where('date_on', '>=', $start)
                ->where('date_on', '<=', $end)
                ->where('id_employee', $id_employee)
                ->where('status', '6')
                ->where('captured_at', '<>', null)
                ->orderBy('date_on', 'asc')
                ->get()
                ->keyBy('date_on');

            $employee = DB::table('tb_employees as b')
                ->leftJoin('tb_departments as c', 'c.id', 'b.dept_id')
                ->leftJoin('tb_salaries as d', function ($join) {
                    $join->on('d.id_employee', '=', 'b.id')
                        ->where('d.status', 1);
                })
                ->where('b.id', $id_employee)
                ->select('b.employee_name', 'b.NIK', 'c.dept_name', 'd.slpj', 'b.position_id', 'c.dept_code')
                ->first();

            $summary = DB::table('tb_ot_summary')
                ->where('id_employee', $id_employee)
                ->where('start_date', $start)
                ->where('end_date', $end)
                ->first();

            $meals = DB::table('tb_meals')
                ->where('id_employee', $id_employee)
                ->where('date_on', '>=', $start)
                ->where('date_on', '<=', $end)
                ->where('is_deleted', 0)
                ->get()
                ->keyBy('date_on');

            $amt_driver = DB::table('tb_salary_component')->where('id', '39')->value('amount') ?? 0;

            $dates = [];
            $current = strtotime($start);
            $last = strtotime($end);
            while ($current <= $last) {
                $dates[] = date('Y-m-d', $current);
                $current = strtotime('+1 day', $current);
            }

            $data = compact('start', 'end', 'id_employee', 'employee', 'summary', 'tabel', 'meals', 'dates', 'amt_driver');

            $FileName = 'SLIP_OT_' . ($employee->NIK ?? 'UNDEF') . '.PDF';
            $pdf = PDF::loadview('payroll.slip_overtime', $data)->setPaper('A5', 'landscape');
            return $pdf->stream($FileName);
        } else {
            return abort(403, 'Anda tidak punya akses');
        }
    }
    public function slip_overtime_personal($start, $end, $id_employee)
    {
        $email = Auth::user()->email;
        $id = DB::table('tb_emails')
            ->leftjoin('tb_employees', 'tb_employees.id', '=', 'tb_emails.id_employee')
            ->where('email_address', $email)->value('tb_employees.id');
        if ($id_employee != $id) {
            return abort(403, 'Anda tidak punya akses');
        }
        $tabel = DB::table('tb_overtime_details')
            ->where('date_on', '>=', $start)
            ->where('date_on', '<=', $end)
            ->where('id_employee', $id_employee)
            ->where('status', '6')
            ->where('captured_at', '<>', null)
            ->orderBy('date_on', 'asc')
            ->get()
            ->keyBy('date_on');

        $employee = DB::table('tb_employees as b')
            ->leftJoin('tb_departments as c', 'c.id', 'b.dept_id')
            ->leftJoin('tb_salaries as d', function ($join) {
                $join->on('d.id_employee', '=', 'b.id')
                    ->where('d.status', 1);
            })
            ->where('b.id', $id_employee)
            ->select('b.employee_name', 'b.NIK', 'c.dept_name', 'd.slpj', 'b.position_id', 'c.dept_code')
            ->first();

        $summary = DB::table('tb_ot_summary')
            ->where('id_employee', $id_employee)
            ->where('start_date', $start)
            ->where('end_date', $end)
            ->first();

        $meals = DB::table('tb_meals')
            ->where('id_employee', $id_employee)
            ->where('date_on', '>=', $start)
            ->where('date_on', '<=', $end)
            ->where('is_deleted', 0)
            ->get()
            ->keyBy('date_on');

        $amt_driver = DB::table('tb_salary_component')->where('id', '39')->value('amount') ?? 0;

        $dates = [];
        $current = strtotime($start);
        $last = strtotime($end);
        while ($current <= $last) {
            $dates[] = date('Y-m-d', $current);
            $current = strtotime('+1 day', $current);
        }

        $data = compact('start', 'end', 'id_employee', 'employee', 'summary', 'tabel', 'meals', 'dates', 'amt_driver');

        $FileName = 'SLIP_OT_' . ($employee->NIK ?? 'UNDEF') . '.PDF';
        $pdf = PDF::loadview('payroll.slip_overtime', $data)->setPaper('A5', 'landscape');
        return $pdf->stream($FileName);
    }
    public function capture_assignment(Request $request, $start = null, $end = null)
    {
        $start = $start ?? $request->start;
        $end = $end ?? $request->end;

        if (!$start || !$end || $start == '0' || $end == '0') {
            $today = date('Y-m-d');
            $periode = date('Y-m', strtotime($today));
            $end = date('Y-m-d', strtotime($periode . '-24'));
            $awal_bulan = date('Y-m-d', strtotime($periode . '-01'));
            $akhir_bulan = date('Y-m', strtotime('-1 days', strtotime($awal_bulan)));
            $start = date('Y-m-d', strtotime($akhir_bulan . '-25'));
        }

        $tb1 = DB::table('tb_overtime_spv')
            ->leftjoin('tb_employees', 'tb_employees.id', '=', 'tb_overtime_spv.id_employee')
            ->leftjoin('tb_departments', 'tb_departments.id', '=', 'tb_employees.dept_id')
            ->leftjoin('tb_positions', 'tb_positions.id', '=', 'tb_employees.position_id')
            ->select('tb_employees.NIK', 'tb_employees.employee_name', 'tb_departments.dept_code', 'tb_positions.position_name', DB::raw('SUM(amount) as total_amount'))
            ->Where([['tb_overtime_spv.isVerified', '1'], ['tb_overtime_spv.isDelete', '0'], ['tb_overtime_spv.ot_date', '>=', $start], ['tb_overtime_spv.ot_date', '<=', $end]])
            ->groupby('tb_employees.NIK', 'tb_employees.employee_name', 'tb_employees.PIN', 'tb_employees.dept_id', 'tb_departments.dept_code', 'tb_positions.position_index', 'tb_positions.position_name')
            ->get(['tb_employees.NIK', 'tb_employees.employee_name', 'tb_departments.dept_code', 'tb_positions.position_name']);

        $menu = 'capture_assignment';

        return view('payroll.capture_assignment', compact('tb1', 'start', 'end', 'menu'));
    }
    public function save_summary_assignment(Request $request)
    {
        $start = $request->start;
        $end = $request->end;
        $periode = date('Y-m', strtotime($end));
        if (!$start || !$end) {
            return redirect()->back()->with('error', 'Start and End dates are required.');
        }

        $tb1 = DB::table('tb_overtime_spv')
            ->leftjoin('tb_employees', 'tb_employees.id', '=', 'tb_overtime_spv.id_employee')
            ->leftjoin('tb_departments', 'tb_departments.id', '=', 'tb_employees.dept_id')
            ->leftjoin('tb_positions', 'tb_positions.id', '=', 'tb_employees.position_id')
            ->select('tb_employees.NIK', 'tb_employees.id', 'tb_employees.employee_name', 'tb_departments.dept_code', 'tb_positions.position_name', DB::raw('SUM(amount) as total_amount'))
            ->Where([['tb_overtime_spv.isVerified', '1'], ['tb_overtime_spv.isDelete', '0'], ['tb_overtime_spv.ot_date', '>=', $start], ['tb_overtime_spv.ot_date', '<=', $end]])
            ->groupby('tb_employees.NIK', 'tb_employees.id', 'tb_employees.employee_name', 'tb_employees.PIN', 'tb_employees.dept_id', 'tb_departments.dept_code', 'tb_positions.position_index', 'tb_positions.position_name')
            ->get(['tb_employees.NIK', 'tb_employees.id', 'tb_employees.employee_name', 'tb_departments.dept_code', 'tb_positions.position_name']);

        // return $tb1;
        foreach ($tb1 as $row) {
            DB::table('tb_ot_summary')->updateOrInsert(
                [
                    'id_employee' => $row->id,
                    'periode' => $periode,
                    'start_date' => $start,
                    'end_date' => $end,
                    'kategori' => 'Assignment'
                ],
                [
                    'nik' => $row->NIK,
                    'employee_name' => $row->employee_name,
                    'dept_code' => $row->dept_code,
                    'ot_amount' => $row->total_amount,
                    'gross_amount' => $row->total_amount,
                    'net_amount' => $row->total_amount,
                    'updated_at' => now(),
                ]
            );
        }
        $update_capture = DB::table('tb_overtime_spv')->where('ot_date', '>=', $start)->where('ot_date', '<=', $end)->where('isVerified', '1')->update([
            'captured_at' => now(),
        ]);

        return redirect('/payroll/summary_assignment/' . $start . '/' . $end)->with('success', 'Summary data has been saved to tb_ot_summary.');
    }
    public function summary_assignment(Request $request, $start = null, $end = null)
    {
        $start = $start ?? $request->start;
        $end = $end ?? $request->end;

        if (!$start || !$end || $start == '0' || $end == '0') {
            $today = date('Y-m-d');
            $periode = date('Y-m', strtotime($today));
            $end = date('Y-m-d', strtotime($periode . '-24'));
            $awal_bulan = date('Y-m-d', strtotime($periode . '-01'));
            $akhir_bulan = date('Y-m', strtotime('-1 days', strtotime($awal_bulan)));
            $start = date('Y-m-d', strtotime($akhir_bulan . '-25'));
        }

        $tb1 = DB::table('tb_ot_summary')
            ->leftjoin('tb_employee_detail as a', 'a.id_employee', '=', 'tb_ot_summary.id_employee')
            ->leftjoin('tb_employees as b', 'b.id', '=', 'tb_ot_summary.id_employee')
            ->where('start_date', $start)
            ->where('end_date', $end)
            ->where('kategori', 'Assignment')
            ->orderby('dept_code', 'asc')
            ->orderby('employee_name', 'asc')
            ->get(['tb_ot_summary.*', 'a.nomor_rekening', 'b.cc_code']);

        $menu = 'summary_assignment';

        return view('payroll.summary_assignment', compact('tb1', 'start', 'end', 'menu'));
    }
    public function distribute_assignment_slip($start = null, $end = null)
    {
        $update = DB::table('tb_ot_summary')
            ->where('start_date', $start)
            ->where('end_date', $end)
            ->where('kategori', 'Assignment')
            ->update([
                'distribute' => '1',
            ]);
        if ($update)
            return redirect()->back()->with('success', 'Data berhasil didistribusikan.');
        else
            return redirect()->back()->with('error', 'Data gagal didistribusikan.');
    }
    public function tax_assignment_excel(Request $request, $start = null, $end = null)
    {
        $start = $start ?? $request->start;
        $end = $end ?? $request->end;

        if (!$start || !$end || $start == '0' || $end == '0') {
            $today = date('Y-m-d');
            $periode = date('Y-m', strtotime($today));
            $end = date('Y-m-d', strtotime($periode . '-24'));
            $awal_bulan = date('Y-m-d', strtotime($periode . '-01'));
            $akhir_bulan = date('Y-m', strtotime('-1 days', strtotime($awal_bulan)));
            $start = date('Y-m-d', strtotime($akhir_bulan . '-25'));
        }

        $tb1 = DB::table('tb_ot_summary')
            ->leftjoin('tb_employee_detail as a', 'a.id_employee', '=', 'tb_ot_summary.id_employee')
            ->leftjoin('tb_employees as b', 'b.id', '=', 'tb_ot_summary.id_employee')
            ->where('start_date', $start)
            ->where('end_date', $end)
            ->where('kategori', 'Assignment')
            ->orderby('dept_code', 'asc')
            ->orderby('employee_name', 'asc')
            ->get(['tb_ot_summary.*', 'a.nomor_rekening', 'b.cc_code']);

        $menu = 'overtime_tax';

        return view('payroll.summary_assignment_excel', compact('tb1', 'start', 'end', 'menu'));
    }
    public function slip_assignment($start, $end, $id_employee)
    {
        $email = Auth::user()->email;
        $id = DB::table('tb_emails')
            ->leftjoin('tb_employees', 'tb_employees.id', '=', 'tb_emails.id_employee')
            ->where('email_address', $email)->value('tb_employees.id');
        if ($id_employee != $id) {
            return abort(403, 'Anda tidak punya akses');
        }
        $tabel = DB::table('tb_overtime_spv')
            ->where('ot_date', '>=', $start)
            ->where('ot_date', '<=', $end)
            ->where('id_employee', $id_employee)
            ->where('isVerified', '1')
            ->where('captured_at', '<>', null)
            ->orderBy('ot_date', 'asc')
            ->get()
            ->keyBy('ot_date');

        $employee = DB::table('tb_employees as b')
            ->leftJoin('tb_departments as c', 'c.id', 'b.dept_id')
            ->leftJoin('tb_salaries as d', function ($join) {
                $join->on('d.id_employee', '=', 'b.id')
                    ->where('d.status', 1);
            })
            ->where('b.id', $id_employee)
            ->select('b.employee_name', 'b.NIK', 'c.dept_name', 'd.slpj', 'b.position_id', 'c.dept_code')
            ->first();

        $summary = DB::table('tb_ot_summary')
            ->where('id_employee', $id_employee)
            ->where('start_date', $start)
            ->where('end_date', $end)
            ->first();

        $meals = DB::table('tb_meals')
            ->where('id_employee', $id_employee)
            ->where('date_on', '>=', $start)
            ->where('date_on', '<=', $end)
            ->where('is_deleted', 0)
            ->get()
            ->keyBy('date_on');

        $amt_driver = DB::table('tb_salary_component')->where('id', '39')->value('amount') ?? 0;

        $dates = [];
        $current = strtotime($start);
        $last = strtotime($end);
        while ($current <= $last) {
            $dates[] = date('Y-m-d', $current);
            $current = strtotime('+1 day', $current);
        }

        $data = compact('start', 'end', 'id_employee', 'employee', 'summary', 'tabel', 'meals', 'dates', 'amt_driver');

        $FileName = 'SLIP_ASSIGNMENT_' . ($employee->NIK ?? 'UNDEF') . '.PDF';
        $pdf = PDF::loadview('page_ess.slip_assignment', $data)->setPaper('A5', 'landscape');
        return $pdf->stream($FileName);
    }

}
