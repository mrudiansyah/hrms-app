<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class QccActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $isCommittee = $user->hasRole('committee');
        $periods = DB::table('tb_qcc_periode')
            ->orderByDesc('periode_start')
            ->orderByDesc('id')
            ->get();
        $selectedPeriod = null;

        if ($request->filled('periode_id')) {
            $selectedPeriod = $periods->firstWhere('id', $request->input('periode_id'));
            abort_unless($selectedPeriod, 404, 'Periode QCC tidak ditemukan.');
        } else {
            $selectedPeriod = $periods->first();
        }

        $teamPeriodsQuery = DB::table('tb_qcc_periode_team')
            ->join('tb_qcc_team', 'tb_qcc_team.id', '=', 'tb_qcc_periode_team.id_qcc_team')
            ->join('tb_qcc_periode', 'tb_qcc_periode.id', '=', 'tb_qcc_periode_team.id_qcc_periode')
            ->leftJoinSub(
                DB::table('tb_qcc_periode_team_member')
                    ->select('id_qcc_team_periode')
                    ->selectRaw('COUNT(*) as member_count')
                    ->groupBy('id_qcc_team_periode'),
                'team_member_counts',
                'team_member_counts.id_qcc_team_periode',
                '=',
                'tb_qcc_periode_team.id',
            )
            ->orderByDesc('tb_qcc_periode.periode_start')
            ->orderBy('tb_qcc_team.group_name');

        if ($selectedPeriod) {
            $teamPeriodsQuery->where('tb_qcc_periode_team.id_qcc_periode', $selectedPeriod->id);
        } else {
            $teamPeriodsQuery->whereRaw('1 = 0');
        }

        if (!$isCommittee) {
            $employeeIds = $this->employeeIdsForUser($user->email);
            $teamPeriodsQuery->whereIn(
                'tb_qcc_periode_team.id',
                DB::table('tb_qcc_periode_team_member')
                    ->select('id_qcc_team_periode')
                    ->whereIn('id_employee', $employeeIds),
            );
        }

        $teamPeriods = $teamPeriodsQuery->get([
            'tb_qcc_periode_team.id',
            'tb_qcc_periode_team.theme_team',
            'tb_qcc_team.group_name',
            'tb_qcc_periode.sugested_thema',
            'tb_qcc_periode.periode_start',
            'tb_qcc_periode.periode_finish',
            DB::raw('COALESCE(team_member_counts.member_count, 0) as member_count'),
        ]);
        $progressByTeam = [];
        foreach ($teamPeriods as $teamPeriod) {
            $progressByTeam[(int) $teamPeriod->id] = 0;
        }

        if ($teamPeriods->isNotEmpty()) {
            $actualProgress = DB::table('tb_qcc_periode_team_actual')
                ->join('tb_qcc_periode_schedule', 'tb_qcc_periode_schedule.id', '=', 'tb_qcc_periode_team_actual.id_periode_schedule')
                ->join('tb_qcc_step', 'tb_qcc_step.id', '=', 'tb_qcc_periode_schedule.id_qcc_step')
                ->whereIn('tb_qcc_periode_team_actual.id_periode_team', $teamPeriods->pluck('id'))
                ->get([
                    'tb_qcc_periode_team_actual.id_periode_team',
                    'tb_qcc_periode_team_actual.actual_start',
                    'tb_qcc_periode_team_actual.actual_finish',
                    'tb_qcc_periode_team_actual.resume_meeting',
                    'tb_qcc_periode_team_actual.report_improvement',
                    'tb_qcc_step.step_sequance',
                ]);

            foreach ($actualProgress as $actual) {
                $stepUpdated = $actual->actual_start
                    || $actual->actual_finish
                    || $actual->resume_meeting
                    || $actual->report_improvement;

                if ($stepUpdated) {
                    $teamId = (int) $actual->id_periode_team;
                    $stepSequence = (int) $actual->step_sequance;
                    $progressByTeam[$teamId] = max($progressByTeam[$teamId] ?? 0, $stepSequence);
                }
            }

            foreach ($teamPeriods as $teamPeriod) {
                $teamPeriod->progress_step = $progressByTeam[(int) $teamPeriod->id];
            }
        }
        $menu = 'qcc';

        return view('qcc.activities', compact(
            'menu',
            'teamPeriods',
            'isCommittee',
            'periods',
            'selectedPeriod',
        ));
    }

    public function updateTeamTheme(Request $request, int $teamPeriodId)
    {
        abort_unless(Auth::user()->hasRole('committee'), 403, 'Anda tidak punya akses.');
        $teamPeriod = DB::table('tb_qcc_periode_team')
            ->where('id', $teamPeriodId)
            ->first(['id_qcc_periode']);
        abort_unless($teamPeriod, 404, 'Team pada periode QCC tidak ditemukan.');

        $data = $request->validate([
            'theme_team' => ['required', 'string', 'max:255'],
        ]);

        DB::table('tb_qcc_periode_team')
            ->where('id', $teamPeriodId)
            ->update([
                'theme_team' => $data['theme_team'],
                'updated_at' => now(),
                'updated_by' => Auth::user()->name,
            ]);

        return redirect('/qcc_activity?periode_id=' . $teamPeriod->id_qcc_periode)
            ->with('success', 'Tema team berhasil diperbarui.');
    }

    public function open(int $teamPeriodId)
    {
        $teamPeriod = $this->authorizedTeamPeriod($teamPeriodId);
        $this->ensureActualRows($teamPeriod);

        return redirect('/qcc_activity/' . $teamPeriodId);
    }

    public function show(int $teamPeriodId)
    {
        $teamPeriod = $this->authorizedTeamPeriod($teamPeriodId);
        $period = DB::table('tb_qcc_periode')
            ->where('id', $teamPeriod->id_qcc_periode)
            ->first();
        abort_unless($period, 404, 'Periode QCC tidak ditemukan.');

        $schedules = DB::table('tb_qcc_periode_schedule')
            ->join('tb_qcc_step', 'tb_qcc_step.id', '=', 'tb_qcc_periode_schedule.id_qcc_step')
            ->where('tb_qcc_periode_schedule.id_qcc_periode', $period->id)
            ->orderBy('tb_qcc_step.step_sequance')
            ->get([
                'tb_qcc_periode_schedule.*',
                'tb_qcc_step.step_sequance',
                'tb_qcc_step.step_description',
            ]);

        $actuals = DB::table('tb_qcc_periode_team_actual')
            ->where('id_periode_team', $teamPeriodId)
            ->get()
            ->keyBy('id_periode_schedule');

        $weekStart = Carbon::parse($period->periode_start)->startOfDay();
        $periodFinish = Carbon::parse($period->periode_finish)->startOfDay();
        $weeks = [];
        $monthGroups = [];
        $weekNumber = 1;

        while ($weekStart->lte($periodFinish)) {
            $weekFinish = $weekStart->copy()->addDays(6);
            if ($weekFinish->gt($periodFinish)) {
                $weekFinish = $periodFinish->copy();
            }

            $monthKey = $weekStart->format('Y-m');
            $monthLabel = $weekStart->copy()->locale('id')->translatedFormat('F Y');
            $weeks[] = [
                'number' => $weekNumber,
                'start' => $weekStart->toDateString(),
                'finish' => $weekFinish->toDateString(),
            ];
            if (!isset($monthGroups[$monthKey])) {
                $monthGroups[$monthKey] = [
                    'label' => $monthLabel,
                    'count' => 0,
                ];
            }
            $monthGroups[$monthKey]['count']++;
            $weekStart->addDays(7);
            $weekNumber++;
        }

        $monthGroups = array_values($monthGroups);
        $menu = 'qcc';

        return view('qcc.activity', compact(
            'menu',
            'teamPeriod',
            'period',
            'schedules',
            'actuals',
            'weeks',
            'monthGroups',
        ));
    }

    public function updateActual(Request $request, int $id)
    {
        $actual = DB::table('tb_qcc_periode_team_actual')->where('id', $id)->first();
        abort_unless($actual, 404, 'Data actual tidak ditemukan.');
        $teamPeriod = $this->authorizedTeamPeriod((int) $actual->id_periode_team);
        $schedule = DB::table('tb_qcc_periode_schedule')
            ->join('tb_qcc_step', 'tb_qcc_step.id', '=', 'tb_qcc_periode_schedule.id_qcc_step')
            ->where('tb_qcc_periode_schedule.id', $actual->id_periode_schedule)
            ->first([
                'tb_qcc_periode_schedule.id_qcc_periode',
                'tb_qcc_step.step_sequance',
            ]);
        abort_unless($schedule, 404, 'Schedule step tidak ditemukan.');

        $previousScheduleIds = DB::table('tb_qcc_periode_schedule')
            ->join('tb_qcc_step', 'tb_qcc_step.id', '=', 'tb_qcc_periode_schedule.id_qcc_step')
            ->where('tb_qcc_periode_schedule.id_qcc_periode', $schedule->id_qcc_periode)
            ->where('tb_qcc_step.step_sequance', '<', $schedule->step_sequance)
            ->pluck('tb_qcc_periode_schedule.id');

        if ($previousScheduleIds->isNotEmpty()) {
            $completedPreviousCount = DB::table('tb_qcc_periode_team_actual')
                ->where('id_periode_team', $teamPeriod->id)
                ->whereIn('id_periode_schedule', $previousScheduleIds)
                ->where(function ($query) {
                    $query->whereNotNull('actual_start')
                        ->orWhereNotNull('actual_finish')
                        ->orWhereNotNull('resume_meeting')
                        ->orWhereNotNull('report_improvement');
                })
                ->count();

            abort_unless(
                $completedPreviousCount === $previousScheduleIds->count(),
                422,
                'Isi data Actual pada step sebelumnya terlebih dahulu.',
            );
        }

        $data = $request->validate([
            'actual_start' => ['nullable', 'date', 'required_with:actual_finish'],
            'actual_finish' => ['nullable', 'date', 'required_with:actual_start', 'after_or_equal:actual_start'],
            'resume_meeting' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:10240'],
            'report_improvement' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:10240'],
        ]);

        $newFiles = [];
        $oldFiles = [];

        try {
            DB::transaction(function () use ($data, $actual, $id, &$newFiles, &$oldFiles) {
                $updateData = [
                    'actual_start' => $data['actual_start'] ?? null,
                    'actual_finish' => $data['actual_finish'] ?? null,
                    'updated_at' => now(),
                    'updated_by' => Auth::user()->name,
                ];

                foreach (['resume_meeting', 'report_improvement'] as $field) {
                    if (!isset($data[$field])) {
                        continue;
                    }

                    $extension = $data[$field]->extension();
                    $storage = Storage::disk('local');
                    do {
                        $fileReference = Str::random(16) . '.' . $extension;
                        $newPath = 'qcc/activity/' . $id . '/' . $fileReference;
                    } while ($storage->exists($newPath));

                    $storedPath = $data[$field]->storeAs('qcc/activity/' . $id, $fileReference, 'local');
                    if ($storedPath === false) {
                        throw new \RuntimeException('File ' . str_replace('_', ' ', $field) . ' gagal disimpan.');
                    }

                    $newFiles[] = $newPath;
                    $updateData[$field] = $fileReference;

                    $oldPath = $this->activityFilePath($actual, $field);
                    if ($oldPath !== null) {
                        $oldFiles[] = $oldPath;
                    }
                }

                DB::table('tb_qcc_periode_team_actual')
                    ->where('id', $id)
                    ->update($updateData);
            });
        } catch (\Throwable $exception) {
            foreach ($newFiles as $newPath) {
                if (!Storage::disk('local')->delete($newPath)) {
                    Log::warning('Failed to remove a QCC activity upload after a database error.', [
                        'file' => $newPath,
                    ]);
                }
            }

            throw $exception;
        }

        foreach ($oldFiles as $oldPath) {
            if (!Storage::disk('local')->delete($oldPath)) {
                Log::warning('Failed to remove a replaced QCC activity file.', [
                    'file' => $oldPath,
                ]);
            }
        }

        return redirect('/qcc_activity/' . $actual->id_periode_team)
            ->with('success', 'Actual activity berhasil diperbarui.');
    }

    public function showActualFile(int $id, string $field)
    {
        abort_unless(in_array($field, ['resume_meeting', 'report_improvement'], true), 404);

        $actual = DB::table('tb_qcc_periode_team_actual')->where('id', $id)->first();
        abort_unless($actual, 404, 'Data actual tidak ditemukan.');
        $this->authorizedTeamPeriod((int) $actual->id_periode_team);

        $path = $this->activityFilePath($actual, $field);
        abort_unless($path !== null, 404);

        $storage = Storage::disk('local');
        abort_unless($storage->exists($path), 404, 'File tidak ditemukan.');

        return $storage->response($path, basename($path), [
            'X-Content-Type-Options' => 'nosniff',
        ], 'inline');
    }

    private function activityFilePath(object $actual, string $field): ?string
    {
        $reference = $actual->{$field};
        if (!is_string($reference)) {
            return null;
        }

        if (preg_match('/^[A-Za-z0-9]{16}\.(?:jpg|jpeg|png|webp|pdf)$/i', $reference)) {
            return 'qcc/activity/' . $actual->id . '/' . $reference;
        }

        if (preg_match(
            '/^qcc\/activity\/' . preg_quote((string) $actual->id, '/') . '\/[A-Za-z0-9]{40}\.(?:jpg|jpeg|png|webp|pdf)$/i',
            $reference,
        )) {
            return $reference;
        }

        return null;
    }

    private function authorizedTeamPeriod(int $teamPeriodId): object
    {
        $teamPeriod = DB::table('tb_qcc_periode_team')
            ->join('tb_qcc_team', 'tb_qcc_team.id', '=', 'tb_qcc_periode_team.id_qcc_team')
            ->where('tb_qcc_periode_team.id', $teamPeriodId)
            ->first(['tb_qcc_periode_team.*', 'tb_qcc_team.group_name']);
        abort_unless($teamPeriod, 404, 'Team pada periode QCC tidak ditemukan.');

        $user = Auth::user();
        if (!$user->hasRole('committee')) {
            $employeeIds = $this->employeeIdsForUser($user->email);
            abort_unless(
                DB::table('tb_qcc_periode_team_member')
                    ->where('id_qcc_team_periode', $teamPeriodId)
                    ->whereIn('id_employee', $employeeIds)
                    ->exists(),
                403,
                'Anda bukan anggota team QCC pada periode ini.',
            );
        }

        return $teamPeriod;
    }

    private function employeeIdsForUser(?string $email)
    {
        if (!$email) {
            return collect();
        }

        return DB::table('tb_emails')
            ->where('email_address', $email)
            ->pluck('id_employee')
            ->filter()
            ->unique()
            ->values();
    }

    private function ensureActualRows(object $teamPeriod): void
    {
        $scheduleIds = DB::table('tb_qcc_periode_schedule')
            ->where('id_qcc_periode', $teamPeriod->id_qcc_periode)
            ->orderBy('id')
            ->pluck('id');

        abort_unless(
            $scheduleIds->count() === 8,
            422,
            'Periode QCC harus memiliki 8 schedule sebelum actual activity dapat dibuat.',
        );

        DB::transaction(function () use ($teamPeriod, $scheduleIds) {
            foreach ($scheduleIds as $scheduleId) {
                $exists = DB::table('tb_qcc_periode_team_actual')
                    ->where('id_periode_team', $teamPeriod->id)
                    ->where('id_periode_schedule', $scheduleId)
                    ->exists();

                if (!$exists) {
                    $now = now();
                    DB::table('tb_qcc_periode_team_actual')->insert([
                        'id_periode_team' => $teamPeriod->id,
                        'id_periode_schedule' => $scheduleId,
                        'created_at' => $now,
                        'updated_at' => $now,
                        'updated_by' => Auth::user()->name,
                    ]);
                }
            }
        });
    }
}
