<?php

namespace App\Http\Controllers;

use App\Services\QccScheduleGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class QccManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function steps()
    {
        $isCommittee = Auth::user()->hasRole('committee');
        $menu = 'qcc';
        $steps = DB::table('tb_qcc_step')
            ->orderBy('step_sequance')
            ->get();

        return view('qcc.steps', compact('menu', 'steps', 'isCommittee'));
    }

    public function storeStep(Request $request)
    {
        $this->authorizeCommittee();
        $data = $request->validate([
            'step_sequance' => ['required', 'integer', 'between:1,8', Rule::unique('tb_qcc_step', 'step_sequance')],
            'step_description' => ['required', 'string', 'max:255'],
        ]);
        $now = now();

        DB::table('tb_qcc_step')->insert([
            ...$data,
            'created_at' => $now,
            'updated_at' => $now,
            'updated_by' => Auth::user()->name,
        ]);

        return redirect('/qcc_step')->with('success', 'Master step berhasil ditambahkan.');
    }

    public function updateStep(Request $request, int $id)
    {
        $this->authorizeCommittee();
        $data = $request->validate([
            'step_sequance' => ['required', 'integer', 'between:1,8', Rule::unique('tb_qcc_step', 'step_sequance')->ignore($id)],
            'step_description' => ['required', 'string', 'max:255'],
        ]);

        $step = DB::table('tb_qcc_step')->where('id', $id)->first();
        abort_unless($step, 404);

        if (
            (int) $step->step_sequance !== (int) $data['step_sequance']
            && DB::table('tb_qcc_periode_schedule')->where('id_qcc_step', $id)->exists()
        ) {
            return redirect('/qcc_step')->with('error', 'Urutan step yang sudah digunakan pada schedule tidak dapat diubah.');
        }

        DB::table('tb_qcc_step')->where('id', $id)->update([
            ...$data,
            'updated_at' => now(),
            'updated_by' => Auth::user()->name,
        ]);

        return redirect('/qcc_step')->with('success', 'Master step berhasil diperbarui.');
    }

    public function destroyStep(int $id)
    {
        $this->authorizeCommittee();

        if (DB::table('tb_qcc_periode_schedule')->where('id_qcc_step', $id)->exists()) {
            return redirect('/qcc_step')->with('error', 'Step sudah digunakan pada schedule dan tidak dapat dihapus.');
        }

        DB::table('tb_qcc_step')->where('id', $id)->delete();

        return redirect('/qcc_step')->with('success', 'Master step berhasil dihapus.');
    }

    public function periods()
    {
        $isCommittee = Auth::user()->hasRole('committee');
        $menu = 'qcc';
        $periods = DB::table('tb_qcc_periode')
            ->orderByDesc('periode_start')
            ->get();
        $scheduleCounts = DB::table('tb_qcc_periode_schedule')
            ->select('id_qcc_periode', DB::raw('COUNT(*) as schedule_count'))
            ->groupBy('id_qcc_periode')
            ->pluck('schedule_count', 'id_qcc_periode');

        return view('qcc.periods', compact('menu', 'periods', 'scheduleCounts', 'isCommittee'));
    }

    public function storePeriod(Request $request, QccScheduleGenerator $generator)
    {
        $this->authorizeCommittee();
        $data = $request->validate([
            'sugested_thema' => ['required', 'string', 'max:255'],
            'periode_start' => ['required', 'date'],
            'periode_finish' => ['required', 'date', 'after_or_equal:periode_start'],
        ]);

        $steps = DB::table('tb_qcc_step')->orderBy('step_sequance')->get();
        if ($steps->count() !== 8 || $steps->pluck('step_sequance')->map(fn ($value) => (int) $value)->all() !== range(1, 8)) {
            return redirect('/qcc_perriode')
                ->withInput()
                ->with('error', 'Lengkapi Master Steps dengan urutan 1 sampai 8 sebelum membuat periode.');
        }

        DB::transaction(function () use ($data, $steps, $generator) {
            $now = now();
            $periodId = DB::table('tb_qcc_periode')->insertGetId([
                ...$data,
                'isLocked' => 0,
                'created_at' => $now,
                'updated_at' => $now,
                'updated_by' => Auth::user()->name,
            ]);

            DB::table('tb_qcc_periode_schedule')->insert(
                $generator->generate(
                    $periodId,
                    $steps,
                    $data['periode_start'],
                    $data['periode_finish'],
                    Auth::user()->name,
                ),
            );
        });

        return redirect('/qcc_perriode')->with('success', 'Periode dan 8 schedule QCC berhasil dibuat.');
    }

    public function updatePeriod(Request $request, int $id)
    {
        $this->authorizeCommittee();
        $data = $request->validate([
            'sugested_thema' => ['required', 'string', 'max:255'],
            'periode_start' => ['required', 'date'],
            'periode_finish' => ['required', 'date', 'after_or_equal:periode_start'],
        ]);

        $period = DB::table('tb_qcc_periode')->where('id', $id)->first();
        abort_unless($period, 404);

        $hasScheduleOutsideRange = DB::table('tb_qcc_periode_schedule')
            ->where('id_qcc_periode', $id)
            ->where(function ($query) use ($data) {
                $query->whereDate('plan_start', '<', $data['periode_start'])
                    ->orWhereDate('plan_finish', '>', $data['periode_finish']);
            })
            ->exists();

        if ($hasScheduleOutsideRange) {
            return redirect('/qcc_perriode')
                ->withInput()
                ->with('error', 'Rentang periode tidak boleh mengecualikan tanggal schedule. Sesuaikan schedule terlebih dahulu.');
        }

        DB::table('tb_qcc_periode')->where('id', $id)->update([
            ...$data,
            'updated_at' => now(),
            'updated_by' => Auth::user()->name,
        ]);

        return redirect('/qcc_perriode')->with('success', 'Periode QCC berhasil diperbarui.');
    }

    public function destroyPeriod(int $id)
    {
        $this->authorizeCommittee();
        DB::transaction(function () use ($id) {
            DB::table('tb_qcc_periode_schedule')->where('id_qcc_periode', $id)->delete();
            DB::table('tb_qcc_periode')->where('id', $id)->delete();
        });

        return redirect('/qcc_perriode')->with('success', 'Periode dan schedule terkait berhasil dihapus.');
    }

    public function togglePeriodLock(int $id)
    {
        $this->authorizeCommittee();

        DB::transaction(function () use ($id) {
            $period = DB::table('tb_qcc_periode')
                ->where('id', $id)
                ->lockForUpdate()
                ->first(['id', 'isLocked']);
            abort_unless($period, 404, 'Periode QCC tidak ditemukan.');
            $isLocked = (int) $period->isLocked === 1;

            DB::table('tb_qcc_periode')
                ->where('id', $id)
                ->update([
                    'isLocked' => $isLocked ? 0 : 1,
                    'updated_at' => now(),
                    'updated_by' => Auth::user()->name,
                ]);
        });

        return redirect('/qcc_perriode')->with('success', 'Status lock periode berhasil diperbarui.');
    }

    public function schedule(Request $request)
    {
        $isCommittee = Auth::user()->hasRole('committee');
        $menu = 'qcc';
        $periods = DB::table('tb_qcc_periode')
            ->orderByDesc('periode_start')
            ->orderByDesc('id')
            ->get();
        $selectedPeriod = null;
        $schedules = collect();
        $weeks = [];
        $monthGroups = [];

        if ($request->filled('periode_id')) {
            $selectedPeriod = DB::table('tb_qcc_periode')
                ->where('id', $request->input('periode_id'))
                ->first();

            if (!$selectedPeriod) {
                abort(404, 'Periode QCC tidak ditemukan.');
            }
        } else {
            $selectedPeriod = $periods->first();
        }

        if ($selectedPeriod) {
            $schedules = DB::table('tb_qcc_periode_schedule')
                ->join('tb_qcc_step', 'tb_qcc_step.id', '=', 'tb_qcc_periode_schedule.id_qcc_step')
                ->where('tb_qcc_periode_schedule.id_qcc_periode', $selectedPeriod->id)
                ->orderBy('tb_qcc_step.step_sequance')
                ->get([
                    'tb_qcc_periode_schedule.*',
                    'tb_qcc_step.step_sequance',
                    'tb_qcc_step.step_description',
                ]);

            $weekStart = Carbon::parse($selectedPeriod->periode_start)->startOfDay();
            $periodFinish = Carbon::parse($selectedPeriod->periode_finish)->startOfDay();
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
                    'month_key' => $monthKey,
                    'month_label' => $monthLabel,
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
        }

        return view('qcc.schedule', compact(
            'menu',
            'periods',
            'selectedPeriod',
            'schedules',
            'weeks',
            'monthGroups',
            'isCommittee',
        ));
    }

    public function updateSchedule(Request $request, int $id)
    {
        $this->authorizeCommittee();
        $schedule = DB::table('tb_qcc_periode_schedule')
            ->where('id', $id)
            ->first();
        abort_unless($schedule, 404);

        $period = DB::table('tb_qcc_periode')
            ->where('id', $schedule->id_qcc_periode)
            ->first();
        abort_unless($period, 404);

        $data = $request->validate([
            'plan_start' => ['required', 'date', 'after_or_equal:' . Carbon::parse($period->periode_start)->toDateString(), 'before_or_equal:' . Carbon::parse($period->periode_finish)->toDateString()],
            'plan_finish' => ['required', 'date', 'after_or_equal:plan_start', 'before_or_equal:' . Carbon::parse($period->periode_finish)->toDateString()],
        ]);

        DB::table('tb_qcc_periode_schedule')->where('id', $id)->update([
            ...$data,
            'updated_at' => now(),
            'updated_by' => Auth::user()->name,
        ]);

        return redirect('/qcc_schedule?periode_id=' . $schedule->id_qcc_periode)
            ->with('success', 'Tanggal schedule berhasil diperbarui.');
    }

    private function authorizeCommittee(): void
    {
        abort_unless(Auth::user()?->hasRole('committee'), 403, 'Anda tidak punya akses.');
    }
}
