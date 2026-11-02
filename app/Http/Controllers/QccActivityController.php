<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QccActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $isCommittee = $user->hasRole('committee');

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
            'tb_qcc_team.group_name',
            'tb_qcc_periode.sugested_thema',
            'tb_qcc_periode.periode_start',
            'tb_qcc_periode.periode_finish',
            DB::raw('COALESCE(team_member_counts.member_count, 0) as member_count'),
        ]);
        $menu = 'qcc';

        return view('qcc.activities', compact('menu', 'teamPeriods', 'isCommittee'));
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
        $this->authorizedTeamPeriod((int) $actual->id_periode_team);

        $data = $request->validate([
            'actual_start' => ['nullable', 'date', 'required_with:actual_finish'],
            'actual_finish' => ['nullable', 'date', 'required_with:actual_start', 'after_or_equal:actual_start'],
            'resume_meeting' => ['nullable', 'string', 'max:5000'],
            'report_improvement' => ['nullable', 'string', 'max:5000'],
        ]);

        DB::table('tb_qcc_periode_team_actual')->where('id', $id)->update([
            ...$data,
            'updated_at' => now(),
            'updated_by' => Auth::user()->name,
        ]);

        return redirect('/qcc_activity/' . $actual->id_periode_team)
            ->with('success', 'Actual activity berhasil diperbarui.');
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
