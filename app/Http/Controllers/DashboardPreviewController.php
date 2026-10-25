<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DashboardPreviewController extends Controller
{
    public function __construct()
    {
        //$this->middleware(['auth', 'verified']);
    }

    public function index()
    {
        $tb1=DB::table('users')->first();
        return $tb1;
        $today = Carbon::today();
        $periodEnd = $today->copy()->addDays(30);
        $retirementPeriodEnd = $today->copy()->addYearsNoOverflow(5);
        $retirementProjectionEnd = $today->copy()->addYearsNoOverflow(10);

        $passwordExpiryEnabled = DB::table('tb_utilities')
            ->where('atribut', 'expired_password')
            ->where('status', '1')
            ->exists();

        if ($passwordExpiryEnabled && Auth::user()->expired_date < $today->toDateString()) {
            return redirect('/ChangePassword');
        }

        $retirementAgeValue = DB::table('tb_utilities')
            ->where('atribut', 'Pensiun')
            ->value('status');
        
        if (!is_numeric($retirementAgeValue) || (int) $retirementAgeValue < 1) {
            throw new RuntimeException('Usia pensiun pada tb_utilities (atribut Pensiun) harus berupa angka tahun.');
        }

        $retirementAge = (int) $retirementAgeValue;
        $activeEmployees = DB::table('tb_employees')->where('status', '1');
        $activeEmployeeCount = (clone $activeEmployees)->count();

        $countActiveContract = function ($contractNames, $excludeMagangHub = false) {
            $query = DB::table('tb_statuses')
                ->join('tb_employees', 'tb_employees.id', '=', 'tb_statuses.id_employee')
                ->where('tb_employees.status', '1')
                ->where('tb_statuses.active', '1')
                ->whereIn('tb_statuses.contract_name', (array) $contractNames);

            if ($excludeMagangHub) {
                $query->where('tb_employees.position_id', '<>', '37');
            }

            return $query->distinct()->count('tb_employees.id');
        };

        $permanentCount = $countActiveContract('Permanen');
        $contractCount = $countActiveContract('Kontrak');
        $internCount = $countActiveContract('Magang', true);
        $saiCount = $permanentCount + $contractCount;
        $coreEmployeeCount = $saiCount + $internCount;
        $internIdealCount = (int) round($saiCount * 0.2);
        $otherCount = $countActiveContract(['SAB', 'Other']);
        $psabCount = (clone $activeEmployees)->where('position_id', '23')->count();

        $permanentPercentage = $coreEmployeeCount > 0
            ? round($permanentCount / $coreEmployeeCount * 100, 2)
            : 0;
        $contractPercentage = $saiCount > 0
            ? round($contractCount / $saiCount * 100, 2)
            : 0;
        $internPercentage = $saiCount > 0
            ? round($internCount / $saiCount * 100, 2)
            : 0;

        $newHireCount = (clone $activeEmployees)
            ->whereBetween('join_date', [$today->copy()->subDays(29)->toDateString(), $today->toDateString()])
            ->count();

        $contractQuery = DB::table('tb_statuses')
            ->join('tb_employees', 'tb_employees.id', '=', 'tb_statuses.id_employee')
            ->leftJoin('tb_departments', 'tb_departments.id', '=', 'tb_employees.dept_id')
            ->where('tb_employees.status', '1')
            ->where('tb_statuses.active', '1')
            ->whereIn('tb_statuses.contract_name', ['Kontrak', 'Magang'])
            ->whereBetween('tb_statuses.finish_contract', [$today->toDateString(), $periodEnd->toDateString()]);

        $contractEndingCount = (clone $contractQuery)
            ->distinct()
            ->count('tb_employees.id');

        $contractProjectionStart = $today->copy()->startOfMonth();
        $contractProjectionEnd = $contractProjectionStart->copy()->addMonthsNoOverflow(6)->subDay();
        $contractProjectionMonths = collect(range(0, 5))
            ->map(function ($offset) use ($contractProjectionStart) {
                return $contractProjectionStart->copy()->addMonthsNoOverflow($offset);
            });
        $contractProjectionCounts = [
            'Kontrak' => $contractProjectionMonths->mapWithKeys(function ($month) {
                return [$month->format('Y-m') => []];
            })->all(),
            'Magang' => $contractProjectionMonths->mapWithKeys(function ($month) {
                return [$month->format('Y-m') => []];
            })->all(),
        ];

        $contractProjectionEmployees = DB::table('tb_statuses')
            ->join('tb_employees', 'tb_employees.id', '=', 'tb_statuses.id_employee')
            ->where('tb_employees.status', '1')
            ->where('tb_statuses.active', '1')
            ->whereIn('tb_statuses.contract_name', ['Kontrak', 'Magang'])
            ->whereBetween('tb_statuses.finish_contract', [
                $today->toDateString(),
                $contractProjectionEnd->toDateString(),
            ])
            ->get([
                'tb_employees.id as employee_id',
                'tb_statuses.contract_name',
                'tb_statuses.finish_contract',
            ]);

        foreach ($contractProjectionEmployees as $contract) {
            $month = Carbon::parse($contract->finish_contract)->format('Y-m');
            if (isset($contractProjectionCounts[$contract->contract_name][$month])) {
                $contractProjectionCounts[$contract->contract_name][$month][$contract->employee_id] = true;
            }
        }

        foreach ($contractProjectionCounts as $contractName => $monthCounts) {
            foreach ($monthCounts as $month => $employeeIds) {
                $contractProjectionCounts[$contractName][$month] = count((array) $employeeIds);
            }
        }

        $contractProjection = [
            'labels' => $contractProjectionMonths->map(function ($month) {
                return $month->format('Y-m');
            })->values(),
            'displayLabels' => $contractProjectionMonths->map(function ($month) {
                return $month->format('M Y');
            })->values(),
            'datasets' => [
                [
                    'label' => 'Kontrak',
                    'contract_name' => 'Kontrak',
                    'data' => array_values($contractProjectionCounts['Kontrak']),
                    'borderColor' => '#2878b5',
                    'backgroundColor' => 'rgba(40, 120, 181, 0.08)',
                ],
                [
                    'label' => 'Magang',
                    'contract_name' => 'Magang',
                    'data' => array_values($contractProjectionCounts['Magang']),
                    'borderColor' => '#e89b36',
                    'backgroundColor' => 'rgba(232, 155, 54, 0.08)',
                ],
            ],
        ];

        $projectionYears = range($today->year, $retirementProjectionEnd->year);
        $retirementProjectionCounts = array_fill_keys($projectionYears, 0);
        $retirementCandidates = collect();

        $employeesWithBirthDate = DB::table('tb_employees')
            ->join('tb_employee_detail', 'tb_employee_detail.id_employee', '=', 'tb_employees.id')
            ->where('tb_employees.status', '1')
            ->whereNotNull('tb_employee_detail.tanggal_lahir')
            ->distinct()
            ->get([
                'tb_employees.id as employee_id',
                'tb_employees.NIK',
                'tb_employees.employee_name',
                'tb_employee_detail.tanggal_lahir',
            ])
            ->unique('employee_id')
            ->values();

        foreach ($employeesWithBirthDate as $employee) {
            $retirementDate = Carbon::parse($employee->tanggal_lahir)->addYearsNoOverflow($retirementAge);

            if ($retirementDate->betweenIncluded($today, $retirementPeriodEnd)) {
                $employee->retirement_date = $retirementDate->toDateString();
                $retirementCandidates->push($employee);
            }

            if (
                $retirementDate->betweenIncluded($today, $retirementProjectionEnd)
                && array_key_exists($retirementDate->year, $retirementProjectionCounts)
            ) {
                $retirementProjectionCounts[$retirementDate->year]++;
            }
        }

        $retirementCandidates = $retirementCandidates
            ->sortBy('retirement_date')
            ->values();
        $retirementProjection = collect($projectionYears)
            ->map(function ($year) use ($retirementProjectionCounts) {
                return [
                    'label' => (string) $year,
                    'total' => $retirementProjectionCounts[$year],
                ];
            });

        $generationRanges = $this->generationRanges($today->year);
        $generationCounts = array_fill_keys(array_keys($generationRanges), 0);
        foreach ($employeesWithBirthDate as $employee) {
            $birthDate = Carbon::parse($employee->tanggal_lahir);
            if ($birthDate->greaterThan($today)) {
                continue;
            }

            foreach ($generationRanges as $generation => [$startYear, $endYear]) {
                if ($birthDate->year >= $startYear && $birthDate->year <= $endYear) {
                    $generationCounts[$generation]++;
                    break;
                }
            }
        }
        $generationDistribution = collect($generationCounts)
            ->map(function ($total, $label) {
                return ['label' => $label, 'total' => $total];
            })
            ->values();

        $serviceTime = [
            'Kurang dari 1 tahun' => 0,
            '1–5 tahun' => 0,
            '6–10 tahun' => 0,
            'Lebih dari 10 tahun' => 0,
        ];
        $totalServiceDays = 0;
        $employeesWithJoinDate = 0;

        (clone $activeEmployees)
            ->whereNotNull('join_date')
            ->pluck('join_date')
            ->each(function ($joinDate) use (&$serviceTime, &$totalServiceDays, &$employeesWithJoinDate, $today) {
                $date = Carbon::parse($joinDate);

                if ($date->greaterThan($today)) {
                    return;
                }

                $days = $date->diffInDays($today);
                $totalServiceDays += $days;
                $employeesWithJoinDate++;

                if ($days < 365) {
                    $serviceTime['Kurang dari 1 tahun']++;
                } elseif ($days < 2190) {
                    $serviceTime['1–5 tahun']++;
                } elseif ($days < 4015) {
                    $serviceTime['6–10 tahun']++;
                } else {
                    $serviceTime['Lebih dari 10 tahun']++;
                }
            });

        $distribution = function ($column, $joinDetail = false) {
            $query = DB::table('tb_employees')
                ->when($joinDetail, function ($query) {
                    $query->leftJoin('tb_employee_detail', 'tb_employee_detail.id_employee', '=', 'tb_employees.id');
                })
                ->where('tb_employees.status', '1');

            $expression = "COALESCE(NULLIF(LTRIM(RTRIM({$column})), ''), 'Belum diisi')";

            return $query
                ->selectRaw("{$expression} as label, COUNT(DISTINCT tb_employees.id) as total")
                ->groupByRaw($expression)
                ->orderByDesc('total')
                ->get();
        };

        $genderDistribution = $distribution('tb_employees.gender');
        $religionDistribution = $distribution('tb_employee_detail.agama', true);
        $educationDistribution = DB::table('tb_employees')
            ->leftJoin('tb_educations', function ($join) {
                $join->on('tb_educations.id_employee', '=', 'tb_employees.id')
                    ->where('tb_educations.is_top', '1');
            })
            ->where('tb_employees.status', '1')
            ->selectRaw("COALESCE(NULLIF(LTRIM(RTRIM(tb_educations.level_education)), ''), 'Belum diisi') as label, COUNT(DISTINCT tb_employees.id) as total")
            ->groupByRaw("COALESCE(NULLIF(LTRIM(RTRIM(tb_educations.level_education)), ''), 'Belum diisi')")
            ->orderByDesc('total')
            ->get();
        $maritalStatusDistribution = $distribution('tb_employee_detail.kode_status', true);

        $positionDistribution = DB::table('tb_employees')
            ->leftJoin('tb_positions', 'tb_positions.id', '=', 'tb_employees.position_id')
            ->where('tb_employees.status', '1')
            ->selectRaw("COALESCE(NULLIF(LTRIM(RTRIM(tb_positions.position_name)), ''), 'Belum diisi') as label, COUNT(DISTINCT tb_employees.id) as total")
            ->groupByRaw("COALESCE(NULLIF(LTRIM(RTRIM(tb_positions.position_name)), ''), 'Belum diisi')")
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $addressBase = DB::table('tb_addresses')
            ->join('tb_employees', 'tb_employees.id', '=', 'tb_addresses.id_employee')
            ->where('tb_employees.status', '1')
            ->where('tb_addresses.status', '1');

        $addressDistribution = (clone $addressBase)
            ->selectRaw("COALESCE(NULLIF(LTRIM(RTRIM(tb_addresses.kabupaten)), ''), 'Belum diisi') as label, COUNT(DISTINCT tb_employees.id) as total")
            ->groupByRaw("COALESCE(NULLIF(LTRIM(RTRIM(tb_addresses.kabupaten)), ''), 'Belum diisi')")
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $addressSummary = (clone $addressBase)
            ->whereNotNull('tb_addresses.kabupaten')
            ->whereRaw("LTRIM(RTRIM(tb_addresses.kabupaten)) <> ''")
            ->selectRaw(
                "COUNT(DISTINCT CASE WHEN UPPER(LTRIM(RTRIM(tb_addresses.kabupaten))) = ? THEN tb_employees.id END) as karawang, COUNT(DISTINCT tb_employees.id) as total",
                ['KAB. KARAWANG']
            )
            ->first();

        $karawangCount = (int) $addressSummary->karawang;
        $nonKarawangCount = max(0, (int) $addressSummary->total - $karawangCount);
        $addressTotal = $karawangCount + $nonKarawangCount;

        return view('dashboard.preview', [
            'today' => $today,
            'periodEnd' => $periodEnd,
            'retirementPeriodEnd' => $retirementPeriodEnd,
            'activeEmployeeCount' => $activeEmployeeCount,
            'permanentCount' => $permanentCount,
            'contractCount' => $contractCount,
            'internCount' => $internCount,
            'saiCount' => $saiCount,
            'coreEmployeeCount' => $coreEmployeeCount,
            'internIdealCount' => $internIdealCount,
            'internOverCount' => max(0, $internCount - $internIdealCount),
            'otherCount' => $otherCount,
            'psabCount' => $psabCount,
            'permanentPercentage' => $permanentPercentage,
            'contractPercentage' => $contractPercentage,
            'internPercentage' => $internPercentage,
            'newHireCount' => $newHireCount,
            'contractEndingCount' => $contractEndingCount,
            'contractProjection' => $contractProjection,
            'contractProjectionEnd' => $contractProjectionEnd,
            'retirementAge' => $retirementAge,
            'retirementCandidates' => $retirementCandidates,
            'retirementProjection' => $retirementProjection,
            'retirementProjectionEnd' => $retirementProjectionEnd,
            'generationDistribution' => $generationDistribution,
            'serviceTime' => $serviceTime,
            'averageServiceYears' => $employeesWithJoinDate > 0
                ? round($totalServiceDays / $employeesWithJoinDate / 365.25, 1)
                : 0,
            'genderDistribution' => $genderDistribution,
            'religionDistribution' => $religionDistribution,
            'educationDistribution' => $educationDistribution,
            'maritalStatusDistribution' => $maritalStatusDistribution,
            'positionDistribution' => $positionDistribution,
            'addressDistribution' => $addressDistribution,
            'karawangCount' => $karawangCount,
            'nonKarawangCount' => $nonKarawangCount,
            'addressTotal' => $addressTotal,
            'karawangPercentage' => $addressTotal > 0 ? round($karawangCount / $addressTotal * 100, 1) : 0,
            'nonKarawangPercentage' => $addressTotal > 0 ? round($nonKarawangCount / $addressTotal * 100, 1) : 0,
            'menu' => 'dashboard',
        ]);
    }

    public function chartDetails(Request $request)
    {
        abort_unless(Auth::user()->hasRole('hr_access'), 403);

        $filters = $request->validate([
            'chart' => 'required|in:gender,religion,education,marital,service,position,address,location,retirement_projection,contract_projection,generation',
            'label' => 'required|string|max:255',
            'year' => 'required_if:chart,retirement_projection|integer|min:2000|max:2200',
            'month' => 'required_if:chart,contract_projection|date_format:Y-m',
            'contract_name' => 'required_if:chart,contract_projection|in:Kontrak,Magang',
            'page' => 'nullable|integer|min:1',
        ]);

        $page = (int) ($filters['page'] ?? 1);
        $query = DB::table('tb_employees')
            ->leftJoin('tb_departments', 'tb_departments.id', '=', 'tb_employees.dept_id')
            ->leftJoin('tb_positions', 'tb_positions.id', '=', 'tb_employees.position_id')
            ->where('tb_employees.status', '1');

        $query->select([
            'tb_employees.id as employee_id',
            'tb_employees.NIK',
            'tb_employees.employee_name',
            'tb_employees.join_date',
            'tb_departments.dept_code',
            'tb_positions.position_name',
        ])->distinct();

        switch ($filters['chart']) {
            case 'gender':
                $this->whereNormalizedValue($query, 'tb_employees.gender', $filters['label']);
                break;

            case 'religion':
            case 'marital':
                $query->leftJoin('tb_employee_detail', 'tb_employee_detail.id_employee', '=', 'tb_employees.id');
                $column = [
                    'religion' => 'tb_employee_detail.agama',
                    'marital' => 'tb_employee_detail.kode_status',
                ][$filters['chart']];
                $this->whereNormalizedValue($query, $column, $filters['label']);
                break;

            case 'education':
                $query->leftJoin('tb_educations', function ($join) {
                    $join->on('tb_educations.id_employee', '=', 'tb_employees.id')
                        ->where('tb_educations.is_top', '1');
                });
                $this->whereNormalizedValue($query, 'tb_educations.level_education', $filters['label']);
                break;

            case 'service':
                $query->whereNotNull('tb_employees.join_date');
                $employees = $query->get();
                $matchingEmployees = $employees->filter(function ($employee) use ($filters) {
                    $joinDate = Carbon::parse($employee->join_date);

                    if ($joinDate->isFuture()) {
                        return false;
                    }

                    $days = $joinDate->diffInDays(Carbon::today());
                    $range = $days < 365
                        ? 'Kurang dari 1 tahun'
                        : ($days < 2190 ? '1–5 tahun' : ($days < 4015 ? '6–10 tahun' : 'Lebih dari 10 tahun'));

                    return $range === $filters['label'];
                })->unique('employee_id')->values();

                return response()->json($this->paginateEmployeeCollection($matchingEmployees, $page));

            case 'position':
                $this->whereNormalizedValue($query, 'tb_positions.position_name', $filters['label']);
                break;

            case 'generation':
                $generationRanges = $this->generationRanges(Carbon::today()->year);
                abort_unless(array_key_exists($filters['label'], $generationRanges), 422);
                [$startYear, $endYear] = $generationRanges[$filters['label']];
                $query
                    ->join('tb_employee_detail', 'tb_employee_detail.id_employee', '=', 'tb_employees.id')
                    ->whereBetween('tb_employee_detail.tanggal_lahir', [
                        Carbon::create($startYear, 1, 1)->toDateString(),
                        Carbon::create($endYear, 12, 31)->min(Carbon::today())->toDateString(),
                    ]);
                break;

            case 'address':
                $query
                    ->join('tb_addresses', 'tb_addresses.id_employee', '=', 'tb_employees.id')
                    ->where('tb_addresses.status', '1');
                $this->whereNormalizedValue($query, 'tb_addresses.kabupaten', $filters['label']);
                break;

            case 'location':
                $query
                    ->join('tb_addresses', 'tb_addresses.id_employee', '=', 'tb_employees.id')
                    ->where('tb_addresses.status', '1')
                    ->whereNotNull('tb_addresses.kabupaten')
                    ->whereRaw("LTRIM(RTRIM(tb_addresses.kabupaten)) <> ''");

                if ($filters['label'] === 'Karawang') {
                    $query->whereRaw("UPPER(LTRIM(RTRIM(tb_addresses.kabupaten))) = 'KAB. KARAWANG'");
                } else {
                    $query->whereRaw("UPPER(LTRIM(RTRIM(tb_addresses.kabupaten))) <> 'KAB. KARAWANG'");
                }
                break;

            case 'contract_projection':
                $month = Carbon::createFromFormat('Y-m', $filters['month']);
                $query
                    ->join('tb_statuses', 'tb_statuses.id_employee', '=', 'tb_employees.id')
                    ->where('tb_statuses.active', '1')
                    ->where('tb_statuses.contract_name', $filters['contract_name'])
                    ->whereBetween('tb_statuses.finish_contract', [
                        $month->copy()->startOfMonth()->max(Carbon::today())->toDateString(),
                        $month->copy()->endOfMonth()->toDateString(),
                    ]);
                break;

            case 'retirement_projection':
                $retirementAge = (int) DB::table('tb_utilities')
                    ->where('atribut', 'Pensiun')
                    ->value('status');
                if ($retirementAge < 1) {
                    throw new RuntimeException('Usia pensiun pada tb_utilities (atribut Pensiun) harus berupa angka tahun.');
                }

                $matchingEmployees = $query
                    ->join('tb_employee_detail', 'tb_employee_detail.id_employee', '=', 'tb_employees.id')
                    ->whereNotNull('tb_employee_detail.tanggal_lahir')
                    ->addSelect('tb_employee_detail.tanggal_lahir')
                    ->get()
                    ->filter(function ($employee) use ($retirementAge, $filters) {
                        $retirementDate = Carbon::parse($employee->tanggal_lahir)
                            ->addYearsNoOverflow($retirementAge);

                        return $retirementDate->betweenIncluded(
                            Carbon::today(),
                            Carbon::today()->addYearsNoOverflow(10)
                        ) && $retirementDate->year === (int) $filters['year'];
                    })
                    ->unique('employee_id')
                    ->values();

                return response()->json($this->paginateEmployeeCollection($matchingEmployees, $page));
        }

        return response()->json($query
            ->orderBy('tb_employees.employee_name')
            ->paginate(50, ['*'], 'page', $page));
    }

    private function whereNormalizedValue($query, $column, $value)
    {
        if ($value === 'Belum diisi') {
            $query->where(function ($query) use ($column) {
                $query->whereNull($column)
                    ->orWhereRaw("LTRIM(RTRIM({$column})) = ''");
            });

            return;
        }

        $query->whereRaw("LTRIM(RTRIM({$column})) = ?", [$value]);
    }

    private function generationRanges($currentYear)
    {
        return [
            'Boomers' => [1946, 1964],
            'Gen X' => [1965, 1980],
            'Gen Y' => [1981, 1996],
            'Gen Z' => [1997, 2012],
            'Alpha' => [2013, $currentYear],
        ];
    }

    private function paginateEmployeeCollection($employees, $page)
    {
        $perPage = 50;
        $total = $employees->count();

        return [
            'data' => $employees->slice(($page - 1) * $perPage, $perPage)->values(),
            'current_page' => $page,
            'last_page' => max(1, (int) ceil($total / $perPage)),
            'per_page' => $perPage,
            'total' => $total,
        ];
    }
}
