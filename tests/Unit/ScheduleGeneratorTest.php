<?php

namespace Tests\Unit;

use App\Services\ScheduleGenerator;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ScheduleGeneratorTest extends TestCase
{
    public function test_it_builds_four_weeks_with_seven_days_and_a_deload(): void
    {
        $schedule = (new ScheduleGenerator)->generate(
            'Beginner',
            'cutting',
            3,
            'Full Body',
            ['Senin', 'Rabu', 'Jumat'],
            Carbon::parse('2026-01-05'),
        );

        $this->assertSame('full_body', $schedule['family']);
        $this->assertSame(3, $schedule['effective_sessions']);
        $this->assertSame(['Senin', 'Rabu', 'Jumat'], $schedule['chosen_days']);
        $this->assertCount(4, $schedule['weeks']);

        foreach ($schedule['weeks'] as $week) {
            $this->assertCount(7, $week);
        }

        $this->assertSame(3, collect($schedule['weeks'][1])->where('tipe', 'latihan')->count());
        $this->assertSame('kardio', $schedule['weeks'][1][1]['tipe']);
        $this->assertStringContainsString('deload', implode(' ', $schedule['notes']));

        $weekOne = collect($schedule['weeks'][1])->firstWhere('tipe', 'latihan');
        $weekFour = collect($schedule['weeks'][4])->firstWhere('template_key', $weekOne['template_key']);

        $this->assertSame(
            max(1, (int) round($weekOne['detail']['exercises'][0]['set'] * 0.6)),
            $weekFour['detail']['exercises'][0]['set'],
        );
        $this->assertTrue($weekFour['detail']['is_deload']);
    }

    public function test_all_selected_days_become_training_days_even_above_the_recommended_frequency(): void
    {
        $schedule = (new ScheduleGenerator)->generate(
            'Beginner',
            'bulking',
            3,
            'Full body A/B',
            ['Senin', 'Selasa', 'Kamis', 'Jumat', 'Sabtu'],
            Carbon::parse('2026-01-05'),
        );

        $this->assertSame(5, $schedule['effective_sessions']);
        $this->assertSame(['Senin', 'Selasa', 'Kamis', 'Jumat', 'Sabtu'], $schedule['chosen_days']);
        $this->assertSame(5, collect($schedule['weeks'][1])->where('tipe', 'latihan')->count());
        $this->assertSame('Rabu', collect($schedule['weeks'][1])->firstWhere('tipe', 'kardio')['hari']);
    }

    public function test_it_reduces_sessions_when_available_days_are_limited(): void
    {
        $schedule = (new ScheduleGenerator)->generate(
            'Intermediate',
            'strength',
            5,
            'Push Pull Legs',
            ['Senin', 'Rabu'],
            Carbon::parse('2026-01-05'),
        );

        $this->assertSame(2, $schedule['effective_sessions']);
        $this->assertSame(['Senin', 'Rabu'], $schedule['chosen_days']);
        $this->assertStringContainsString('Sesi dikurangi menjadi 2', implode(' ', $schedule['notes']));
    }

    public function test_it_uses_default_days_when_none_are_selected(): void
    {
        $schedule = (new ScheduleGenerator)->generate(
            'Beginner',
            'cutting',
            3,
            null,
            [],
            Carbon::parse('2026-01-05'),
        );

        $this->assertSame(['Senin', 'Rabu', 'Jumat'], $schedule['chosen_days']);
        $this->assertSame(3, $schedule['effective_sessions']);
    }

    public function test_it_resolves_the_training_split_from_frequency_and_label(): void
    {
        $generator = new ScheduleGenerator;
        $monday = Carbon::parse('2026-01-05');

        $this->assertSame('push_pull_legs', $generator->generate('Intermediate', 'strength', 3, 'Push Pull Legs', [], $monday)['family']);
        $this->assertSame('upper_lower', $generator->generate('Intermediate', 'strength', 4, 'Upper Lower', [], $monday)['family']);
        $this->assertSame('ppl_upper_lower', $generator->generate('Advanced', 'strength', 5, 'Push Pull Legs + Upper Lower', [], $monday)['family']);
        $this->assertSame('ppl_twice', $generator->generate('Advanced', 'strength', 6, 'Push Pull Legs', [], $monday)['family']);
    }

    public function test_it_warns_about_training_every_day(): void
    {
        $schedule = (new ScheduleGenerator)->generate(
            'Advanced',
            'strength',
            7,
            'Push Pull Legs',
            ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],
            Carbon::parse('2026-01-05'),
        );

        $this->assertStringContainsString('lebih dari tiga hari latihan berurutan', implode(' ', $schedule['notes']));
        $this->assertStringContainsString('Enam sesi atau lebih', implode(' ', $schedule['notes']));
    }
}
