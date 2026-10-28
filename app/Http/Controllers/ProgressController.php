<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCheckinRequest;
use App\Models\Program;
use App\Models\WeeklyCheckin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ProgressController extends Controller
{
    public function index(Request $request): View
    {
        $program = $request->user()->activeProgram();

        if (! $program) {
            return view('progres.index', ['program' => null]);
        }

        $program->load([
            'checkins' => fn ($query) => $query->orderBy('minggu_ke'),
            'planSessions' => fn ($query) => $query->orderBy('minggu_ke')->orderBy('tanggal'),
        ]);

        $currentWeek = $program->currentWeek();
        $checkins = $program->checkins;
        $currentCheckin = $checkins->firstWhere('minggu_ke', $currentWeek);

        return view('progres.index', [
            'program' => $program,
            'currentWeek' => $currentWeek,
            'currentCheckin' => $currentCheckin,
            'hasCheckins' => $checkins->isNotEmpty(),
            'charts' => $this->charts($checkins),
            'circular' => $this->circular($program, $currentWeek),
            'macroShares' => $this->macroShares($program),
            'heatmap' => $this->heatmap($program),
        ]);
    }

    public function store(StoreCheckinRequest $request): RedirectResponse
    {
        $program = $request->user()->activeProgram();

        if (! $program) {
            return redirect()
                ->route('program.show')
                ->with('status', 'Simpan program dulu sebelum mengisi check-in.');
        }

        $data = $request->validated();

        $program->checkins()->updateOrCreate(
            ['minggu_ke' => $data['minggu_ke']],
            [
                'berat_kg' => $data['berat_kg'],
                'pinggang_cm' => $data['pinggang_cm'] ?? null,
                'energi' => $data['energi'] ?? null,
                'catatan' => $data['catatan'] ?? null,
            ],
        );

        return redirect()
            ->route('progres.index')
            ->with('status', 'Check-in minggu '.$data['minggu_ke'].' tersimpan.');
    }

    /**
     * @param  Collection<int, WeeklyCheckin>  $checkins
     * @return array<string, mixed>
     */
    private function charts(Collection $checkins): array
    {
        $weeks = [1, 2, 3, 4];
        $labels = array_map(fn (int $week): string => 'Minggu '.$week, $weeks);

        $actual = array_map(function (int $week) use ($checkins): ?float {
            $checkin = $checkins->firstWhere('minggu_ke', $week);

            return $checkin ? (float) $checkin->berat_kg : null;
        }, $weeks);

        $waist = array_map(function (int $week) use ($checkins): ?float {
            $checkin = $checkins->firstWhere('minggu_ke', $week);

            return $checkin?->pinggang_cm !== null ? (float) $checkin->pinggang_cm : null;
        }, $weeks);

        $weightLine = array_map(function (int $week) use ($checkins): ?float {
            $checkin = $checkins->firstWhere('minggu_ke', $week);

            return $checkin ? (float) $checkin->berat_kg : null;
        }, $weeks);

        $hasWaist = collect($waist)->filter(fn ($value) => $value !== null)->isNotEmpty();

        return [
            'weight' => [
                'labels' => $labels,
                'actual' => $actual,
                'summary' => $this->weightSummary($labels, $actual),
            ],
            'combination' => [
                'labels' => $labels,
                'line' => $hasWaist ? $waist : $weightLine,
                'lineLabel' => $hasWaist ? 'Lingkar pinggang (cm)' : 'Berat badan (kg)',
                'summary' => $this->combinationSummary($labels, $hasWaist ? $waist : $weightLine, $hasWaist),
            ],
        ];
    }

    /**
     * @param  array<int, string>  $labels
     * @param  array<int, float|null>  $actual
     */
    private function weightSummary(array $labels, array $actual): string
    {
        $parts = [];

        foreach ($labels as $index => $label) {
            if ($actual[$index] !== null) {
                $parts[] = $label.': '.$this->number($actual[$index]).' kg';
            }
        }

        if ($parts === []) {
            return 'Belum ada data berat badan.';
        }

        return 'Berat aktual '.implode(', ', $parts).'.';
    }

    /**
     * @param  array<int, string>  $labels
     * @param  array<int, float|null>  $line
     */
    private function combinationSummary(array $labels, array $line, bool $isWaist): string
    {
        $lineParts = [];

        foreach ($labels as $index => $label) {
            if ($line[$index] !== null) {
                $lineParts[] = $label.' '.$this->number($line[$index]).' '.($isWaist ? 'cm' : 'kg');
            }
        }

        if ($lineParts === []) {
            return 'Belum ada data '.($isWaist ? 'lingkar pinggang' : 'berat badan').'.';
        }

        return ($isWaist ? 'Lingkar pinggang' : 'Berat badan').': '.implode(', ', $lineParts).'.';
    }

    /**
     * @return array{total: int, completed: int, percent: int, week: int}
     */
    private function circular(Program $program, int $currentWeek): array
    {
        $sessions = $program->planSessions
            ->where('minggu_ke', $currentWeek)
            ->where('tipe', 'latihan');

        $total = $sessions->count();
        $completed = $sessions->filter(fn ($session): bool => $session->completed_at !== null)->count();

        return [
            'total' => $total,
            'completed' => $completed,
            'percent' => $total > 0 ? min(100, (int) round(($completed / $total) * 100)) : 0,
            'week' => $currentWeek,
        ];
    }

    /**
     * @return array{protein: int, fat: int, carbs: int, total: int, program: int}
     */
    private function macroShares(Program $program): array
    {
        $protein = (int) $program->protein_g * 4;
        $fat = (int) $program->lemak_g * 9;
        $carbs = (int) $program->karbo_g * 4;
        $total = max(1, $protein + $fat + $carbs);

        return [
            'protein' => (int) round(($protein / $total) * 100),
            'fat' => (int) round(($fat / $total) * 100),
            'carbs' => (int) round(($carbs / $total) * 100),
            'total' => $total,
            'program' => min(100, (int) round(($program->currentWeek() / 4) * 100)),
        ];
    }

    /**
     * @return array{weekLabels: array<int, string>, rows: array<int, array<string, mixed>>}
     */
    private function heatmap(Program $program): array
    {
        $dayNames = config('workout_templates.days', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu']);
        $currentWeekStart = now()->startOfWeek();
        $programEnd = $program->mulai_pada->copy()->startOfWeek()->addWeeks(3);
        $anchor = $programEnd->greaterThan($currentWeekStart) ? $programEnd : $currentWeekStart;
        $start = $anchor->copy()->subWeeks(11);
        $sessions = $program->planSessions->keyBy(
            fn ($session): string => $session->tanggal->toDateString(),
        );

        $weekLabels = [];
        $grid = [];

        for ($week = 0; $week < 12; $week++) {
            $weekStart = $start->copy()->addWeeks($week);
            $weekLabels[] = $weekStart->format('d/m');
            $cells = [];

            for ($day = 0; $day < 7; $day++) {
                $date = $weekStart->copy()->addDays($day);
                $session = $sessions->get($date->toDateString());
                $status = 'istirahat';

                if ($session !== null) {
                    if ($session->tipe === 'istirahat') {
                        $status = 'istirahat';
                    } elseif ($session->completed_at !== null) {
                        $status = 'selesai';
                    } elseif ($date->lt(today())) {
                        $status = 'terlewat';
                    } else {
                        $status = 'terjadwal';
                    }
                }

                $cells[] = [
                    'date' => $date->toDateString(),
                    'label' => $date->format('d/m/Y'),
                    'status' => $status,
                    'status_label' => $this->statusLabel($status),
                    'fokus' => $session?->fokus,
                ];
            }

            $grid[] = $cells;
        }

        $rows = [];

        foreach ($dayNames as $dayIndex => $dayName) {
            $cells = [];

            foreach ($grid as $weekCells) {
                $cells[] = $weekCells[$dayIndex];
            }

            $rows[] = ['day' => $dayName, 'cells' => $cells];
        }

        return ['weekLabels' => $weekLabels, 'rows' => $rows];
    }

    private function statusLabel(string $status): string
    {
        return match ($status) {
            'selesai' => 'Selesai',
            'terlewat' => 'Terlewat',
            'terjadwal' => 'Terjadwal',
            default => 'Istirahat',
        };
    }

    private function number(float $value): string
    {
        return number_format($value, 1, ',', '.');
    }
}
