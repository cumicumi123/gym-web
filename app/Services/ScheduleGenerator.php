<?php

namespace App\Services;

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ScheduleGenerator
{
    /**
     * Susun jadwal 7 hari dan 4 minggu dari level, tujuan, dan hari yang tersedia.
     *
     * @param  array<int, string>  $availableDays
     * @return array{
     *     family: string,
     *     split_label: string,
     *     effective_sessions: int,
     *     chosen_days: array<int, string>,
     *     days: array<int, array<string, mixed>>,
     *     weeks: array<int, array<int, array<string, mixed>>>,
     *     notes: array<int, string>,
     *     deload_week: int
     * }
     */
    public function generate(
        string $level,
        string $goal,
        int $sessionsPerWeek,
        ?string $split,
        array $availableDays = [],
        ?CarbonInterface $startDate = null,
    ): array {
        $allDays = config('workout_templates.days', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu']);
        $notes = [];

        $available = array_values(array_filter(
            $allDays,
            fn (string $day): bool => in_array($day, $availableDays, true),
        ));

        if ($available === []) {
            $defaultKey = max(1, min(7, $sessionsPerWeek));
            $available = config("workout_templates.default_days.{$defaultKey}", array_slice($allDays, 0, $defaultKey));
        }

        $effectiveSessions = count($available);

        if (count($available) < $sessionsPerWeek) {
            $notes[] = "Hari yang tersedia lebih sedikit dari target sesi. Sesi dikurangi menjadi {$effectiveSessions} per minggu.";
        }

        $chosen = $this->spreadDays($available, $effectiveSessions);
        $family = $this->resolveFamily((string) $split, $effectiveSessions);
        $templates = config("workout_templates.splits.{$family}.days", []);
        $templateKeys = array_keys($templates);

        if ($templateKeys === []) {
            $family = 'full_body';
            $templates = config('workout_templates.splits.full_body.days', []);
            $templateKeys = array_keys($templates);
        }

        $assignments = $this->assignTemplates($chosen, $templateKeys, $templates, $allDays, $notes);

        if ($this->hasLongTrainingStreak($chosen, $allDays)) {
            $notes[] = 'Ada lebih dari tiga hari latihan berurutan. Sisipkan hari istirahat atau pindahkan salah satu sesi.';
        }

        if ($effectiveSessions >= 6) {
            $notes[] = 'Enam sesi atau lebih menyisakan sedikit hari istirahat. Kurangi sesi bila kualitas tidur atau performa menurun.';
        }

        $restDays = array_values(array_diff($allDays, $chosen));
        $cardioDay = count($restDays) >= 2 ? $restDays[0] : null;

        if ($cardioDay === null) {
            $notes[] = 'Tidak ada hari istirahat yang cukup untuk kardio terpisah. Tambahkan kardio singkat setelah latihan beban bila pemulihanmu baik.';
        }

        $weekStart = $this->weekStart($startDate);
        $weekPlan = $this->buildWeekPlan($weekStart, $allDays, $assignments, $cardioDay, $templates, false);

        $weeks = [];
        for ($week = 1; $week <= 4; $week++) {
            $isDeload = $week === 4;
            $weekPlanForWeek = $this->buildWeekPlan(
                $weekStart->copy()->addWeeks($week - 1),
                $allDays,
                $assignments,
                $cardioDay,
                $templates,
                $isDeload,
            );

            foreach ($weekPlanForWeek as $session) {
                $session['minggu_ke'] = $week;
                $weeks[$week][] = $session;
            }
        }

        if (! empty($weeks[4])) {
            $notes[] = 'Minggu ke-4 adalah deload: volume latihan sekitar 60% dari minggu sebelumnya.';
        }

        return [
            'family' => $family,
            'split_label' => config("workout_templates.splits.{$family}.label", 'Program latihan'),
            'effective_sessions' => $effectiveSessions,
            'chosen_days' => $chosen,
            'days' => $weekPlan,
            'weeks' => $weeks,
            'notes' => array_values(array_unique($notes)),
            'deload_week' => 4,
        ];
    }

    /**
     * @param  array<int, string>  $chosen
     * @param  array<int, string>  $templateKeys
     * @param  array<string, array<string, mixed>>  $templates
     * @param  array<int, string>  $allDays
     * @param  array<int, string>  $notes
     * @return array<string, string>
     */
    private function assignTemplates(
        array $chosen,
        array $templateKeys,
        array $templates,
        array $allDays,
        array &$notes,
    ): array {
        $assignments = [];
        $cursor = 0;
        $previousDayIndex = null;
        $previousKey = null;
        $overlapNoted = false;

        foreach ($chosen as $day) {
            $dayIndex = array_search($day, $allDays, true);
            $candidate = null;

            for ($attempt = 0; $attempt < count($templateKeys); $attempt++) {
                $key = $templateKeys[($cursor + $attempt) % count($templateKeys)];
                $overlap = $previousDayIndex !== null
                    && $previousKey !== null
                    && $dayIndex === $previousDayIndex + 1
                    && array_intersect($templates[$key]['otot'] ?? [], $templates[$previousKey]['otot'] ?? []) !== [];

                if (! $overlap) {
                    $candidate = $key;
                    $cursor = ($cursor + $attempt + 1) % count($templateKeys);
                    break;
                }
            }

            if ($candidate === null) {
                $candidate = $templateKeys[$cursor % count($templateKeys)];
                $cursor = ($cursor + 1) % count($templateKeys);

                if (! $overlapNoted) {
                    $notes[] = 'Beberapa hari berurutan memakai otot yang sama. Beri jeda atau turunkan volume pada salah satu sesi.';
                    $overlapNoted = true;
                }
            }

            $assignments[$day] = $candidate;
            $previousDayIndex = $dayIndex;
            $previousKey = $candidate;
        }

        return $assignments;
    }

    /**
     * @param  array<int, string>  $allDays
     * @param  array<string, string>  $assignments
     * @param  array<string, array<string, mixed>>  $templates
     * @return array<int, array<string, mixed>>
     */
    private function buildWeekPlan(
        CarbonInterface $weekStart,
        array $allDays,
        array $assignments,
        ?string $cardioDay,
        array $templates,
        bool $isDeload,
    ): array {
        $plan = [];

        foreach ($allDays as $index => $day) {
            $date = $weekStart->copy()->addDays($index);
            $base = [
                'hari' => $day,
                'tanggal' => $date->toDateString(),
                'minggu_ke' => 1,
                'template_key' => null,
                'detail' => null,
            ];

            if (isset($assignments[$day])) {
                $key = $assignments[$day];
                $template = $templates[$key];

                $plan[] = array_merge($base, [
                    'tipe' => 'latihan',
                    'fokus' => $template['fokus'],
                    'template_key' => $key,
                    'detail' => [
                        'is_deload' => $isDeload,
                        'catatan' => $isDeload ? 'Minggu deload: volume sekitar 60% dari minggu sebelumnya.' : null,
                        'exercises' => $this->adjustExercises($template['exercises'], $isDeload),
                    ],
                ]);

                continue;
            }

            if ($day === $cardioDay) {
                $cardio = config('workout_templates.cardio');

                $plan[] = array_merge($base, [
                    'tipe' => 'kardio',
                    'fokus' => $cardio['fokus'],
                    'detail' => [
                        'is_deload' => $isDeload,
                        'catatan' => 'Kardio zona 2: bisa bicara dengan nyaman selama sesi.',
                        'exercises' => $cardio['exercises'],
                    ],
                ]);

                continue;
            }

            $plan[] = array_merge($base, [
                'tipe' => 'istirahat',
                'fokus' => 'Istirahat',
                'detail' => null,
            ]);
        }

        return $plan;
    }

    /**
     * @param  array<int, array<string, mixed>>  $exercises
     * @return array<int, array<string, mixed>>
     */
    private function adjustExercises(array $exercises, bool $isDeload): array
    {
        if (! $isDeload) {
            return $exercises;
        }

        return array_map(function (array $exercise): array {
            $exercise['set'] = max(1, (int) round(((int) $exercise['set']) * 0.6));

            return $exercise;
        }, $exercises);
    }

    /**
     * @param  array<int, string>  $available
     * @return array<int, string>
     */
    private function spreadDays(array $available, int $count): array
    {
        if ($count >= count($available)) {
            return $available;
        }

        if ($count === 1) {
            return [$available[0]];
        }

        $picked = [];
        $lastIndex = -1;

        for ($i = 0; $i < $count; $i++) {
            $target = (int) round($i * (count($available) - 1) / ($count - 1));
            $target = max($target, $lastIndex + 1);
            $target = min($target, count($available) - 1);
            $picked[] = $available[$target];
            $lastIndex = $target;
        }

        return $picked;
    }

    /**
     * @param  array<int, string>  $chosen
     * @param  array<int, string>  $allDays
     */
    private function hasLongTrainingStreak(array $chosen, array $allDays): bool
    {
        $streak = 0;
        $longest = 0;

        foreach ($allDays as $day) {
            if (in_array($day, $chosen, true)) {
                $streak++;
                $longest = max($longest, $streak);
            } else {
                $streak = 0;
            }
        }

        return $longest > 3;
    }

    private function resolveFamily(string $split, int $sessions): string
    {
        $normalized = Str::lower($split);
        $hasPpl = Str::contains($normalized, ['push', 'pull']);
        $hasUpperLower = Str::contains($normalized, ['upper', 'lower']);

        if ($sessions <= 2) {
            return 'full_body';
        }

        if ($sessions === 3) {
            return $hasPpl && ! $hasUpperLower ? 'push_pull_legs' : 'full_body';
        }

        if ($sessions === 4) {
            return $hasUpperLower ? 'upper_lower' : 'push_pull_legs';
        }

        if ($sessions >= 6) {
            return $hasPpl ? 'ppl_twice' : 'upper_lower';
        }

        if ($hasPpl && $hasUpperLower) {
            return 'ppl_upper_lower';
        }

        return $hasPpl ? 'upper_lower_ppl' : 'upper_lower';
    }

    private function weekStart(?CarbonInterface $startDate): Carbon
    {
        $start = $startDate ? Carbon::parse($startDate) : Carbon::today();

        if (! $start->isMonday()) {
            $start = $start->copy()->next(Carbon::MONDAY);
        }

        return $start->copy()->startOfDay();
    }
}
