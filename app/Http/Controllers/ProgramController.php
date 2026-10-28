<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProgramRequest;
use App\Services\NutritionCalculator;
use App\Services\ScheduleGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProgramController extends Controller
{
    public function show(Request $request, NutritionCalculator $nutritionCalculator, ScheduleGenerator $scheduleGenerator): View
    {
        $program = $request->user()->activeProgram();

        if (! $program) {
            return view('program.show', ['program' => null]);
        }

        $program->load(['planSessions', 'checkins']);

        $nutrition = $nutritionCalculator->calculate(
            (float) $program->berat_kg,
            (float) $program->tinggi_cm,
            (int) $program->usia,
            $program->gender,
            $program->goal,
            (int) $program->sesi_per_minggu,
        );

        $schedule = $scheduleGenerator->generate(
            $program->level,
            $program->goal,
            (int) $program->sesi_per_minggu,
            $program->split,
            $program->schedule_days ?? [],
            $program->mulai_pada,
        );

        return view('program.show', [
            'program' => $program,
            'nutrition' => $nutrition,
            'scheduleNotes' => $schedule['notes'],
            'currentWeek' => $program->currentWeek(),
        ]);
    }

    public function store(
        StoreProgramRequest $request,
        NutritionCalculator $nutritionCalculator,
        ScheduleGenerator $scheduleGenerator,
    ): RedirectResponse {
        $data = $request->validated();
        $user = $request->user();

        $nutrition = $nutritionCalculator->calculate(
            (float) $data['berat_kg'],
            (float) $data['tinggi_cm'],
            (int) $data['usia'],
            $data['gender'] ?? null,
            $data['goal'],
            (int) $data['sesi_per_minggu'],
        );

        $startDate = Carbon::today()->isMonday()
            ? Carbon::today()
            : Carbon::today()->next(Carbon::MONDAY);

        $schedule = $scheduleGenerator->generate(
            $data['level'],
            $data['goal'],
            (int) $data['sesi_per_minggu'],
            $data['split'] ?? null,
            $data['schedule_days'] ?? [],
            $startDate,
        );

        DB::transaction(function () use ($data, $nutrition, $schedule, $startDate, $user): void {
            $user->programs()->where('aktif', true)->update(['aktif' => false]);

            $program = $user->programs()->create([
                'level' => $data['level'],
                'goal' => $data['goal'],
                'usia' => $data['usia'],
                'berat_kg' => $data['berat_kg'],
                'tinggi_cm' => $data['tinggi_cm'],
                'pengalaman_bulan' => $data['pengalaman_bulan'],
                'gender' => $data['gender'] ?? null,
                'split' => $data['split'] ?? $schedule['split_label'],
                'kalori' => $nutrition['calories'],
                'protein_g' => $nutrition['protein_g'],
                'lemak_g' => $nutrition['fat_g'],
                'karbo_g' => $nutrition['carbs_g'],
                'air_ml' => $nutrition['water_ml'],
                'sesi_per_minggu' => $schedule['effective_sessions'],
                'schedule_days' => $schedule['chosen_days'],
                'mulai_pada' => $startDate->toDateString(),
                'aktif' => true,
                'consent_at' => now(),
            ]);

            foreach ($schedule['weeks'] as $week => $sessions) {
                foreach ($sessions as $session) {
                    $program->planSessions()->create([
                        'minggu_ke' => $week,
                        'hari' => $session['hari'],
                        'tanggal' => $session['tanggal'],
                        'fokus' => $session['fokus'],
                        'tipe' => $session['tipe'],
                        'template_key' => $session['template_key'],
                        'detail' => $session['detail'],
                    ]);
                }
            }
        });

        return redirect()
            ->route('program.show')
            ->with('status', 'Program tersimpan. Data profil dan jadwal sekarang ada di akunmu.');
    }
}
