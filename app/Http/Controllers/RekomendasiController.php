<?php

namespace App\Http\Controllers;

use App\Exceptions\MlServiceException;
use App\Http\Requests\RekomendasiRequest;
use App\Services\MlService;
use App\Services\NutritionCalculator;
use App\Support\Bmi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class RekomendasiController extends Controller
{
    public function create(): View
    {
        return view('rekomendasi.form');
    }

    public function store(RekomendasiRequest $request, MlService $ml, NutritionCalculator $nutritionCalculator): View|RedirectResponse
    {
        $data = $request->validated();
        $payload = collect($data)
            ->reject(fn ($value) => $value === null || $value === '')
            ->all();

        try {
            $result = $ml->predict($payload);
        } catch (MlServiceException $exception) {
            Log::error('Prediksi rekomendasi gagal.', ['message' => $exception->getMessage()]);

            return back()
                ->withInput()
                ->with('ml_error', $exception->getMessage());
        }

        $classes = ['Beginner', 'Intermediate', 'Advanced'];
        $levelKey = in_array($result['kategori'] ?? null, $classes, true)
            ? $result['kategori']
            : 'Beginner';

        $probabilities = $result['probabilities'] ?? [];
        foreach ($classes as $class) {
            $probabilities[$class] = round((float) ($probabilities[$class] ?? 0), 4);
        }
        $result['probabilities'] = $probabilities;

        $bmi = Bmi::value((float) $data['berat'], (float) $data['tinggi']);
        $program = config("programs.matrix.{$data['tujuan']}.{$levelKey}");
        $sessionsPerWeek = $this->sessionsPerWeek($data, $program);

        $nutrition = $nutritionCalculator->calculate(
            (float) $data['berat'],
            (float) $data['tinggi'],
            (int) $data['usia'],
            $data['gender'] ?? null,
            $data['tujuan'],
            $sessionsPerWeek,
        );

        return view('rekomendasi.hasil', [
            'data' => $data,
            'result' => $result,
            'bmi' => $bmi,
            'bmiCategory' => Bmi::category($bmi),
            'goal' => config("programs.goals.{$data['tujuan']}"),
            'level' => config("programs.levels.{$levelKey}"),
            'program' => $program,
            'levelKey' => $levelKey,
            'nutrition' => $nutrition,
            'sessionsPerWeek' => $sessionsPerWeek,
            'defaultDays' => config("workout_templates.default_days.{$sessionsPerWeek}", []),
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $program
     */
    private function sessionsPerWeek(array $data, array $program): int
    {
        if (! empty($data['workout_frequency'])) {
            return max(1, min(7, (int) $data['workout_frequency']));
        }

        if (preg_match('/(\d+)\s*sesi/i', (string) ($program['frequency'] ?? ''), $matches) === 1) {
            return max(1, min(7, (int) $matches[1]));
        }

        return 3;
    }
}
