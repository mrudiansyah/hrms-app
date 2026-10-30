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
        $this->middleware(['auth', 'verified']);
    }

    public function index()
    {
        // $tb1=DB::table('users')->first();
        // return $tb1;
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
        $coreEmployees = $this->coreEmployeeQuery();
        $coreEmployeeCount = (clone $coreEmployees)->count();
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
        $generationCounts = array_fill_keys(array_merge(array_keys($generationRanges), ['Tidak tersedia']), 0);
        $birthDates = DB::table('tb_employee_detail')
            ->select('id_employee')
            ->selectRaw('MAX(tanggal_lahir) as tanggal_lahir')
            ->groupBy('id_employee');
        (clone $coreEmployees)
            ->leftJoinSub($birthDates, 'employee_birth_dates', function ($join) {
                $join->on('employee_birth_dates.id_employee', '=', 'tb_employees.id');
            })
            ->select('tb_employees.id as employee_id', 'employee_birth_dates.tanggal_lahir')
            ->get()
            ->unique('employee_id')
            ->each(function ($employee) use (&$generationCounts, $generationRanges, $today) {
                $generation = $this->employeeGeneration($employee->tanggal_lahir, $generationRanges, $today);
                $generationCounts[$generation]++;
            });
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
            'Tidak tersedia' => 0,
        ];
        $totalServiceDays = 0;
        $employeesWithJoinDate = 0;

        (clone $coreEmployees)
            ->select('tb_employees.id', 'tb_employees.join_date')
            ->get()
            ->each(function ($employee) use (&$serviceTime, &$totalServiceDays, &$employeesWithJoinDate, $today) {
                $joinDate = $employee->join_date;
                if (!$joinDate || strtoupper(trim((string) $joinDate)) === 'NULL') {
                    $serviceTime['Tidak tersedia']++;
                    return;
                }

                $date = Carbon::parse($joinDate);

                if ($date->greaterThan($today)) {
                    $serviceTime['Tidak tersedia']++;
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

        $employeeDetails = DB::table('tb_employee_detail')
            ->select('id_employee')
            ->selectRaw('MAX(agama) as agama, MAX(kode_status) as kode_status')
            ->groupBy('id_employee');
        $distribution = function ($column, $joinDetail = false) use ($coreEmployees, $employeeDetails) {
            $query = (clone $coreEmployees)
                ->when($joinDetail, function ($query) use ($employeeDetails) {
                    $query->leftJoinSub($employeeDetails, 'employee_details', function ($join) {
                        $join->on('employee_details.id_employee', '=', 'tb_employees.id');
                    });
                });

            if ($joinDetail) {
                $column = str_replace('tb_employee_detail.', 'employee_details.', $column);
            }

            $expression = "COALESCE(NULLIF(LTRIM(RTRIM({$column})), ''), 'Belum diisi')";

            return $query
                ->selectRaw("{$expression} as label, COUNT(DISTINCT tb_employees.id) as total")
                ->groupByRaw($expression)
                ->orderByDesc('total')
                ->get();
        };

        $genderDistribution = $distribution('tb_employees.gender');
        $religionDistribution = $distribution('tb_employee_detail.agama', true);
        $topEducation = DB::table('tb_educations')
            ->select('id_employee')
            ->selectRaw('MAX(level_education) as level_education')
            ->where('is_top', '1')
            ->groupBy('id_employee');
        $educationDistribution = (clone $coreEmployees)
            ->leftJoinSub($topEducation, 'top_education', function ($join) {
                $join->on('top_education.id_employee', '=', 'tb_employees.id');
            })
            ->selectRaw("COALESCE(NULLIF(LTRIM(RTRIM(top_education.level_education)), ''), 'Belum diisi') as label, COUNT(DISTINCT tb_employees.id) as total")
            ->groupByRaw("COALESCE(NULLIF(LTRIM(RTRIM(top_education.level_education)), ''), 'Belum diisi')")
            ->orderByDesc('total')
            ->get();
        $maritalStatusDistribution = $distribution('tb_employee_detail.kode_status', true);

        $positionDistribution = (clone $coreEmployees)
            ->leftJoin('tb_positions', 'tb_positions.id', '=', 'tb_employees.position_id')
            ->selectRaw("COALESCE(NULLIF(LTRIM(RTRIM(tb_positions.position_name)), ''), 'Belum diisi') as label, COUNT(DISTINCT tb_employees.id) as total")
            ->groupByRaw("COALESCE(NULLIF(LTRIM(RTRIM(tb_positions.position_name)), ''), 'Belum diisi')")
            ->orderByDesc('total')
            ->orderBy('label');
        $positionDistribution = $this->topDistributionWithOthers($positionDistribution);

        $activeAddresses = DB::table('tb_addresses')
            ->select('id_employee')
            ->selectRaw('MAX(kabupaten) as kabupaten')
            ->where('status', '1')
            ->groupBy('id_employee');
        $addressBase = (clone $coreEmployees)
            ->leftJoinSub($activeAddresses, 'active_addresses', function ($join) {
                $join->on('active_addresses.id_employee', '=', 'tb_employees.id');
            });

        $addressDistribution = (clone $addressBase)
            ->selectRaw("COALESCE(NULLIF(LTRIM(RTRIM(active_addresses.kabupaten)), ''), 'Belum diisi') as label, COUNT(DISTINCT tb_employees.id) as total")
            ->groupByRaw("COALESCE(NULLIF(LTRIM(RTRIM(active_addresses.kabupaten)), ''), 'Belum diisi')")
            ->orderByDesc('total')
            ->orderBy('label');
        $addressDistribution = $this->topDistributionWithOthers($addressDistribution);

        $addressSummary = (clone $addressBase)
            ->selectRaw(
                "COUNT(DISTINCT CASE WHEN UPPER(LTRIM(RTRIM(active_addresses.kabupaten))) = ? THEN tb_employees.id END) as karawang, COUNT(DISTINCT CASE WHEN active_addresses.kabupaten IS NULL OR LTRIM(RTRIM(active_addresses.kabupaten)) = '' THEN tb_employees.id END) as unrecorded",
                ['KAB. KARAWANG']
            )
            ->first();

        $karawangCount = (int) $addressSummary->karawang;
        $unrecordedAddressCount = (int) $addressSummary->unrecorded;
        $nonKarawangCount = max(0, $coreEmployeeCount - $karawangCount - $unrecordedAddressCount);
        $addressTotal = $coreEmployeeCount;

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
            'unrecordedAddressCount' => $unrecordedAddressCount,
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
        if (!in_array($filters['chart'], ['retirement_projection', 'contract_projection'], true)) {
            $this->applyCoreEmployeeFilter($query);
        }

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
                $employeeDetails = DB::table('tb_employee_detail')
                    ->select('id_employee')
                    ->selectRaw('MAX(agama) as agama, MAX(kode_status) as kode_status')
                    ->groupBy('id_employee');
                $query->leftJoinSub($employeeDetails, 'employee_details', function ($join) {
                    $join->on('employee_details.id_employee', '=', 'tb_employees.id');
                });
                $column = [
                    'religion' => 'employee_details.agama',
                    'marital' => 'employee_details.kode_status',
                ][$filters['chart']];
                $this->whereNormalizedValue($query, $column, $filters['label']);
                break;

            case 'education':
                $topEducation = DB::table('tb_educations')
                    ->select('id_employee')
                    ->selectRaw('MAX(level_education) as level_education')
                    ->where('is_top', '1')
                    ->groupBy('id_employee');
                $query->leftJoinSub($topEducation, 'top_education', function ($join) {
                    $join->on('top_education.id_employee', '=', 'tb_employees.id');
                });
                $this->whereNormalizedValue($query, 'top_education.level_education', $filters['label']);
                break;

            case 'service':
                $employees = $query->get();
                $matchingEmployees = $employees->filter(function ($employee) use ($filters) {
                    if (!$employee->join_date || strtoupper(trim((string) $employee->join_date)) === 'NULL') {
                        return $filters['label'] === 'Tidak tersedia';
                    }

                    $joinDate = Carbon::parse($employee->join_date);

                    if ($joinDate->isFuture()) {
                        return $filters['label'] === 'Tidak tersedia';
                    }

                    $days = $joinDate->diffInDays(Carbon::today());
                    $range = $days < 365
                        ? 'Kurang dari 1 tahun'
                        : ($days < 2190 ? '1–5 tahun' : ($days < 4015 ? '6–10 tahun' : 'Lebih dari 10 tahun'));

                    return $range === $filters['label'];
                })->unique('employee_id')->values();

                return response()->json($this->paginateEmployeeCollection($matchingEmployees, $page));

            case 'position':
                if ($filters['label'] === '__other__') {
                    $expression = "COALESCE(NULLIF(LTRIM(RTRIM(tb_positions.position_name)), ''), 'Belum diisi')";
                    $query->whereNotIn(DB::raw($expression), $this->topPositionLabels());
                } else {
                    $this->whereNormalizedValue($query, 'tb_positions.position_name', $filters['label']);
                }
                break;

            case 'generation':
                $generationRanges = $this->generationRanges(Carbon::today()->year);
                abort_unless(
                    array_key_exists($filters['label'], $generationRanges) || $filters['label'] === 'Tidak tersedia',
                    422
                );
                $birthDates = DB::table('tb_employee_detail')
                    ->select('id_employee')
                    ->selectRaw('MAX(tanggal_lahir) as tanggal_lahir')
                    ->groupBy('id_employee');
                $query->leftJoinSub($birthDates, 'employee_birth_dates', function ($join) {
                    $join->on('employee_birth_dates.id_employee', '=', 'tb_employees.id');
                });
                if ($filters['label'] === 'Tidak tersedia') {
                    $query->where(function ($query) use ($generationRanges) {
                        $query->whereNull('employee_birth_dates.tanggal_lahir')
                            ->orWhereRaw("LTRIM(RTRIM(employee_birth_dates.tanggal_lahir)) = ''")
                            ->orWhere('employee_birth_dates.tanggal_lahir', '>', Carbon::today()->toDateString())
                            ->orWhere('employee_birth_dates.tanggal_lahir', '<', Carbon::create($generationRanges['Boomers'][0], 1, 1)->toDateString());
                    });
                } else {
                    [$startYear, $endYear] = $generationRanges[$filters['label']];
                    $query->whereBetween('employee_birth_dates.tanggal_lahir', [
                        Carbon::create($startYear, 1, 1)->toDateString(),
                        Carbon::create($endYear, 12, 31)->min(Carbon::today())->toDateString(),
                    ]);
                }
                break;

            case 'address':
                $activeAddresses = DB::table('tb_addresses')
                    ->select('id_employee')
                    ->selectRaw('MAX(kabupaten) as kabupaten')
                    ->where('status', '1')
                    ->groupBy('id_employee');
                $query->leftJoinSub($activeAddresses, 'active_addresses', function ($join) {
                    $join->on('active_addresses.id_employee', '=', 'tb_employees.id');
                });
                if ($filters['label'] === '__other__') {
                    $expression = "COALESCE(NULLIF(LTRIM(RTRIM(active_addresses.kabupaten)), ''), 'Belum diisi')";
                    $query->whereNotIn(DB::raw($expression), $this->topAddressLabels());
                } else {
                    $this->whereNormalizedValue($query, 'active_addresses.kabupaten', $filters['label']);
                }
                break;

            case 'location':
                $activeAddresses = DB::table('tb_addresses')
                    ->select('id_employee')
                    ->selectRaw('MAX(kabupaten) as kabupaten')
                    ->where('status', '1')
                    ->groupBy('id_employee');
                $query->leftJoinSub($activeAddresses, 'active_addresses', function ($join) {
                    $join->on('active_addresses.id_employee', '=', 'tb_employees.id');
                });

                if ($filters['label'] === 'Karawang') {
                    $query->whereRaw("UPPER(LTRIM(RTRIM(active_addresses.kabupaten))) = 'KAB. KARAWANG'");
                } elseif ($filters['label'] === 'Outside') {
                    $query->whereNotNull('active_addresses.kabupaten')
                        ->whereRaw("LTRIM(RTRIM(active_addresses.kabupaten)) <> ''")
                        ->whereRaw("UPPER(LTRIM(RTRIM(active_addresses.kabupaten))) <> 'KAB. KARAWANG'");
                } else {
                    abort_unless($filters['label'] === 'Belum diisi', 422);
                    $query->where(function ($query) {
                        $query->whereNull('active_addresses.kabupaten')
                            ->orWhereRaw("LTRIM(RTRIM(active_addresses.kabupaten)) = ''");
                    });
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

    private function coreEmployeeQuery()
    {
        return $this->applyCoreEmployeeFilter(
            DB::table('tb_employees')->where('tb_employees.status', '1')
        );
    }

    private function applyCoreEmployeeFilter($query)
    {
        return $query->where(function ($query) {
            $query->whereExists(function ($query) {
                $query->selectRaw('1')
                    ->from('tb_statuses')
                    ->whereColumn('tb_statuses.id_employee', 'tb_employees.id')
                    ->where('tb_statuses.active', '1')
                    ->whereIn('tb_statuses.contract_name', ['Permanen', 'Kontrak']);
            })->orWhere(function ($query) {
                $query->where('tb_employees.position_id', '<>', '37')
                    ->whereExists(function ($query) {
                        $query->selectRaw('1')
                            ->from('tb_statuses')
                            ->whereColumn('tb_statuses.id_employee', 'tb_employees.id')
                            ->where('tb_statuses.active', '1')
                            ->where('tb_statuses.contract_name', 'Magang');
                    });
            });
        });
    }

    private function topDistributionWithOthers($query)
    {
        $rows = $query->get();
        if ($rows->count() <= 10) {
            return $rows;
        }

        $topRows = $rows->take(10)->values();
        $topRows->push((object) [
            'label' => 'Lainnya',
            'total' => $rows->slice(10)->sum('total'),
            'detailLabel' => '__other__',
        ]);

        return $topRows;
    }

    private function topPositionLabels()
    {
        $expression = "COALESCE(NULLIF(LTRIM(RTRIM(tb_positions.position_name)), ''), 'Belum diisi')";

        return (clone $this->coreEmployeeQuery())
            ->leftJoin('tb_positions', 'tb_positions.id', '=', 'tb_employees.position_id')
            ->selectRaw("{$expression} as label, COUNT(DISTINCT tb_employees.id) as total")
            ->groupByRaw($expression)
            ->orderByDesc('total')
            ->orderBy('label')
            ->limit(10)
            ->pluck('label')
            ->all();
    }

    private function topAddressLabels()
    {
        $expression = "COALESCE(NULLIF(LTRIM(RTRIM(active_addresses.kabupaten)), ''), 'Belum diisi')";
        $activeAddresses = DB::table('tb_addresses')
            ->select('id_employee')
            ->selectRaw('MAX(kabupaten) as kabupaten')
            ->where('status', '1')
            ->groupBy('id_employee');

        return (clone $this->coreEmployeeQuery())
            ->leftJoinSub($activeAddresses, 'active_addresses', function ($join) {
                $join->on('active_addresses.id_employee', '=', 'tb_employees.id');
            })
            ->selectRaw("{$expression} as label, COUNT(DISTINCT tb_employees.id) as total")
            ->groupByRaw($expression)
            ->orderByDesc('total')
            ->orderBy('label')
            ->limit(10)
            ->pluck('label')
            ->all();
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

    private function employeeGeneration($birthDate, $generationRanges, Carbon $today)
    {
        if (!$birthDate || strtoupper(trim((string) $birthDate)) === 'NULL') {
            return 'Tidak tersedia';
        }

        $birthDate = Carbon::parse($birthDate);
        if ($birthDate->greaterThan($today)) {
            return 'Tidak tersedia';
        }

        foreach ($generationRanges as $generation => [$startYear, $endYear]) {
            if ($birthDate->year >= $startYear && $birthDate->year <= $endYear) {
                return $generation;
            }
        }

        return 'Tidak tersedia';
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
