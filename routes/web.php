<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ModelController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\RekomendasiController;
use App\Http\Controllers\ScheduleController;
use App\Http\Middleware\EnsureMlReportVisible;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/rekomendasi', [RekomendasiController::class, 'create'])->name('rekomendasi.create');
Route::post('/rekomendasi', [RekomendasiController::class, 'store'])->name('rekomendasi.store');

Route::get('/privasi', [PageController::class, 'privacy'])->name('privasi');

Route::middleware('guest')->group(function (): void {
    Route::get('/masuk', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/masuk', [AuthenticatedSessionController::class, 'store'])->name('login.store');
    Route::get('/daftar', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/daftar', [RegisteredUserController::class, 'store'])->name('register.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/keluar', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/program', [ProgramController::class, 'show'])->name('program.show');
    Route::post('/program', [ProgramController::class, 'store'])->name('program.store');

    Route::get('/jadwal', [ScheduleController::class, 'index'])->name('jadwal.index');
    Route::patch('/jadwal/sesi/{planSession}/selesai', [ScheduleController::class, 'complete'])->name('jadwal.complete');

    Route::get('/progres', [ProgressController::class, 'index'])->name('progres.index');
    Route::post('/progres/checkin', [ProgressController::class, 'store'])->name('progres.checkin');

    Route::get('/akun', [AccountController::class, 'index'])->name('akun.index');
    Route::get('/akun/ekspor', [AccountController::class, 'export'])->name('akun.export');
    Route::delete('/akun', [AccountController::class, 'destroy'])->name('akun.destroy');
});

Route::get('/model', fn () => redirect()->route('rekomendasi.create', [], 301));

Route::get('/admin/evaluasi-model', [ModelController::class, 'index'])
    ->name('model.index')
    ->middleware(EnsureMlReportVisible::class);

Route::get('/tentang', [PageController::class, 'about'])->name('tentang');
