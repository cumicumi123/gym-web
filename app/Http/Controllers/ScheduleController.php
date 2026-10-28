<?php

namespace App\Http\Controllers;

use App\Models\PlanSession;
use App\Services\ScheduleGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function index(Request $request, ScheduleGenerator $scheduleGenerator): View
    {
        $program = $request->user()->activeProgram();

        if (! $program) {
            return view('jadwal.index', ['program' => null]);
        }

        $program->load(['planSessions' => fn ($query) => $query->orderBy('minggu_ke')->orderBy('tanggal')]);

        $schedule = $scheduleGenerator->generate(
            $program->level,
            $program->goal,
            (int) $program->sesi_per_minggu,
            $program->split,
            $program->schedule_days ?? [],
            $program->mulai_pada,
        );

        $sessionsByWeek = $program->planSessions->groupBy('minggu_ke');
        $currentWeek = $program->currentWeek();

        return view('jadwal.index', [
            'program' => $program,
            'sessionsByWeek' => $sessionsByWeek,
            'currentWeek' => $currentWeek,
            'scheduleNotes' => $schedule['notes'],
        ]);
    }

    public function complete(PlanSession $planSession): RedirectResponse
    {
        Gate::authorize('update', $planSession->program);

        $currentWeek = $planSession->program->currentWeek();
        $allowedWeeks = [max(1, $currentWeek - 1), $currentWeek];

        if (! in_array($planSession->minggu_ke, $allowedWeeks, true)) {
            return back()->with(
                'status',
                'Sesi hanya bisa ditandai untuk minggu ini dan minggu sebelumnya.',
            );
        }

        $planSession->update([
            'completed_at' => $planSession->completed_at ? null : now(),
        ]);

        return back()->with(
            'status',
            $planSession->completed_at
                ? 'Sesi ditandai selesai. Progresmu ikut diperbarui.'
                : 'Tanda selesai dibatalkan.',
        );
    }
}
