<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $user->loadCount('programs');

        return view('akun.index', ['user' => $user]);
    }

    public function export(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->load(['programs.planSessions', 'programs.checkins']);

        $payload = [
            'akun' => [
                'nama' => $user->name,
                'email' => $user->email,
                'dibuat_pada' => $user->created_at?->toIso8601String(),
            ],
            'program' => $user->programs->map(fn ($program): array => [
                'level' => $program->level,
                'tujuan' => $program->goal,
                'usia' => $program->usia,
                'berat_kg' => (float) $program->berat_kg,
                'tinggi_cm' => (float) $program->tinggi_cm,
                'pengalaman_bulan' => $program->pengalaman_bulan,
                'gender' => $program->gender,
                'kalori' => $program->kalori,
                'protein_g' => $program->protein_g,
                'lemak_g' => $program->lemak_g,
                'karbo_g' => $program->karbo_g,
                'air_ml' => $program->air_ml,
                'sesi_per_minggu' => $program->sesi_per_minggu,
                'mulai_pada' => $program->mulai_pada?->toDateString(),
                'aktif' => $program->aktif,
                'sesi' => $program->planSessions->map(fn ($session): array => [
                    'minggu_ke' => $session->minggu_ke,
                    'hari' => $session->hari,
                    'tanggal' => $session->tanggal?->toDateString(),
                    'fokus' => $session->fokus,
                    'tipe' => $session->tipe,
                    'selesai_pada' => $session->completed_at?->toIso8601String(),
                ])->values(),
                'checkin' => $program->checkins->map(fn ($checkin): array => [
                    'minggu_ke' => $checkin->minggu_ke,
                    'berat_kg' => (float) $checkin->berat_kg,
                    'pinggang_cm' => $checkin->pinggang_cm !== null ? (float) $checkin->pinggang_cm : null,
                    'energi' => $checkin->energi,
                    'catatan' => $checkin->catatan,
                ])->values(),
            ])->values(),
        ];

        return response()->json(
            $payload,
            200,
            ['Content-Disposition' => 'attachment; filename="gymsense-data-'.now()->format('Ymd').'.json"'],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE,
        );
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ], [
            'password.current_password' => 'Kata sandi tidak cocok.',
        ]);

        $user = $request->user();

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $user->delete();

        return redirect()->route('home')->with('status', 'Akun dan seluruh datanya sudah dihapus.');
    }
}
