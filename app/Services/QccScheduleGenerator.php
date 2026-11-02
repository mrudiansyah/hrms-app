<?php

namespace App\Services;

use Carbon\Carbon;

class QccScheduleGenerator
{
    private const DURATIONS_IN_WEEKS = [1 => 2, 2 => 1, 3 => 3, 4 => 3, 5 => 6, 6 => 3, 7 => 2, 8 => 1];

    public function generate(int $periodId, iterable $steps, string $periodStart, string $periodFinish, string $updatedBy): array
    {
        $start = Carbon::parse($periodStart)->startOfDay();
        $finish = Carbon::parse($periodFinish)->startOfDay();
        $nextStart = $start->copy();
        $now = now();
        $schedules = [];

        foreach ($steps as $step) {
            $sequence = (int) $step->step_sequance;
            if (!isset(self::DURATIONS_IN_WEEKS[$sequence])) {
                continue;
            }

            if ($nextStart->gt($finish)) {
                $planStart = $finish->copy();
                $planFinish = $finish->copy();
            } else {
                $planStart = $nextStart->copy();
                $planFinish = $planStart->copy()
                    ->addWeeks(self::DURATIONS_IN_WEEKS[$sequence])
                    ->subDay();

                if ($planFinish->gt($finish)) {
                    $planFinish = $finish->copy();
                }
            }

            $schedules[] = [
                'id_qcc_periode' => $periodId,
                'id_qcc_step' => $step->id,
                'plan_start' => $planStart->toDateString(),
                'plan_finish' => $planFinish->toDateString(),
                'created_at' => $now,
                'updated_at' => $now,
                'updated_by' => $updatedBy,
            ];
            $nextStart = $planFinish->copy()->addDay();
        }

        return $schedules;
    }
}
