<?php

namespace Tests\Unit;

use App\Services\QccScheduleGenerator;
use Tests\TestCase;

class QccScheduleGeneratorTest extends TestCase
{
    public function test_it_generates_eight_sequential_steps_using_the_configured_durations(): void
    {
        $steps = collect(range(1, 8))->map(fn (int $sequence) => (object) [
            'id' => $sequence + 100,
            'step_sequance' => $sequence,
        ]);

        $schedules = app(QccScheduleGenerator::class)->generate(
            12,
            $steps,
            '2026-01-01',
            '2026-12-31',
            'QCC Admin',
        );

        $this->assertCount(8, $schedules);
        $this->assertSame('2026-01-01', $schedules[0]['plan_start']);
        $this->assertSame('2026-01-14', $schedules[0]['plan_finish']);
        $this->assertSame('2026-01-15', $schedules[1]['plan_start']);
        $this->assertSame('2026-01-21', $schedules[1]['plan_finish']);
        $this->assertSame('2026-02-11', $schedules[2]['plan_finish']);
        $this->assertSame(12, $schedules[0]['id_qcc_periode']);
        $this->assertSame(101, $schedules[0]['id_qcc_step']);
    }

    public function test_it_clamps_short_periods_and_places_remaining_steps_on_the_period_finish(): void
    {
        $steps = collect(range(1, 8))->map(fn (int $sequence) => (object) [
            'id' => $sequence,
            'step_sequance' => $sequence,
        ]);

        $schedules = app(QccScheduleGenerator::class)->generate(
            4,
            $steps,
            '2026-01-01',
            '2026-01-03',
            'QCC Admin',
        );

        $this->assertCount(8, $schedules);
        $this->assertSame('2026-01-01', $schedules[0]['plan_start']);
        $this->assertSame('2026-01-03', $schedules[0]['plan_finish']);
        foreach (array_slice($schedules, 1) as $schedule) {
            $this->assertSame('2026-01-03', $schedule['plan_start']);
            $this->assertSame('2026-01-03', $schedule['plan_finish']);
        }
    }
}
