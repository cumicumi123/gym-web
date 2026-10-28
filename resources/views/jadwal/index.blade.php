@extends('layouts.app')

@section('title', 'Jadwal Latihan — GymSense')
@section('description', 'Jadwal latihan 7 hari dan 4 minggu, termasuk minggu deload.')

@section('content')
    <section class="section-tight">
        <div class="site-container">
            @if (! $program)
                <div class="max-w-2xl">
                    <span class="eyebrow">Jadwal</span>
                    <h1 class="mt-5 text-3xl font-semibold tracking-[-0.04em] md:text-4xl">
                        Belum ada <span class="hl">jadwal</span>.
                    </h1>
                    <p class="mt-3 text-sm leading-relaxed text-muted">
                        Simpan program dari halaman hasil rekomendasi. Jadwal 4 minggu akan dibuat
                        otomatis sesuai level, tujuan, dan hari yang kamu pilih.
                    </p>
                    <x-button :href="route('rekomendasi.create')" variant="accent" size="lg" icon="sparkle" class="mt-6">
                        Mulai rekomendasi
                    </x-button>
                </div>
            @else
                @php
                    $currentSessions = ($sessionsByWeek->get($currentWeek) ?? collect())->keyBy('hari');
                    $allDays = config('workout_templates.days');
                    $totalSessions = $program->planSessions->where('tipe', 'latihan')->count();
                    $completedSessions = $program->planSessions->where('tipe', 'latihan')->filter(fn ($session) => $session->completed_at !== null)->count();
                @endphp

                <div class="flex flex-wrap items-end justify-between gap-5">
                    <div class="max-w-2xl">
                        <span class="eyebrow">Jadwal 4 minggu</span>
                        <h1 class="mt-5 text-3xl font-semibold tracking-[-0.04em] md:text-4xl">
                            {{ $program->split ?: 'Program latihan' }} · <span class="hl">minggu {{ $currentWeek }}</span>
                        </h1>
                        <p class="mt-3 text-sm leading-relaxed text-muted">
                            {{ $program->sesi_per_minggu }} sesi per minggu. Minggu ke-4 adalah deload dengan volume sekitar 60%.
                            Kardio di hari libur bersifat opsional dan tidak dihitung ke target sesi.
                            Tandai sesi selesai agar progres tercatat.
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <x-button :href="route('progres.index')" variant="accent" icon="chart">Lihat progres</x-button>
                        <x-button :href="route('program.show')" variant="secondary" icon="layers">Ringkasan program</x-button>
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap gap-2">
                    <span class="pill pill-accent">{{ $completedSessions }} / {{ $totalSessions }} sesi selesai</span>
                    <span class="pill">Minggu {{ $currentWeek }} dari 4</span>
                </div>

                @foreach ($scheduleNotes as $note)
                    <div class="alert alert-warn mt-4">
                        <x-icon name="alert" class="mt-0.5 h-4 w-4 flex-none" />
                        <div>{{ $note }}</div>
                    </div>
                @endforeach

                <x-card class="mt-6">
                    <div class="flex items-center gap-3">
                        <x-badge-icon name="calendar" color="purple" />
                        <div>
                            <h2 class="text-base font-semibold">Pola 7 hari</h2>
                            <p class="mt-1 text-[11px] text-muted">Minggu ke-{{ $currentWeek }}.</p>
                        </div>
                    </div>

                    <div class="week-grid mt-5">
                        @foreach ($allDays as $day)
                            @php $session = $currentSessions->get($day); @endphp
                            <div class="week-cell {{ $session && $session->tipe === 'latihan' ? 'is-training' : '' }} {{ $session && $session->tipe === 'kardio' ? 'is-cardio' : '' }}">
                                <span class="week-day">{{ $day }}</span>
                                <strong>{{ $session?->fokus ?? 'Istirahat' }}</strong>
                                @if ($session && $session->completed_at)
                                    <span class="pill pill-done">Selesai</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </x-card>

                <div class="mt-8 space-y-5">
                    @foreach ([1, 2, 3, 4] as $week)
                        @php
                            $weekSessions = $sessionsByWeek->get($week, collect());
                            $isDeload = $week === 4;
                            $canToggle = $week >= max(1, $currentWeek - 1) && $week <= $currentWeek;
                        @endphp

                        <x-card>
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <span class="week-badge {{ $week === $currentWeek ? 'is-current' : '' }}">Minggu {{ $week }}</span>
                                    @if ($isDeload)
                                        <span class="pill" style="border-color: rgba(231, 84, 184, .35); color: #f4b8e2; background: rgba(231, 84, 184, .08);">Deload · volume 60%</span>
                                    @endif
                                    @if ($week === $currentWeek)
                                        <span class="pill pill-accent">Minggu ini</span>
                                    @endif
                                </div>
                                <span class="text-[11px] text-muted">{{ $weekSessions->count() }} hari terjadwal</span>
                            </div>

                            <div class="session-list mt-4">
                                @foreach ($weekSessions as $session)
                                    <div class="session-row {{ $session->isRest() ? 'is-rest' : '' }}">
                                        <div class="session-date">
                                            <span>{{ $session->hari }}</span>
                                            <small>{{ $session->tanggal->format('d/m') }}</small>
                                        </div>

                                        <div class="session-main">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <strong>{{ $session->fokus }}</strong>
                                                @if ($session->isCardio())
                                                    <span class="pill">Kardio · opsional</span>
                                                @endif
                                                @if ($session->completed_at)
                                                    <span class="pill pill-done">Selesai</span>
                                                @endif
                                            </div>

                                            @if (! empty($session->detail['exercises']))
                                                <ul class="exercise-list">
                                                    @foreach ($session->detail['exercises'] as $exercise)
                                                        <li>
                                                            <span>{{ $exercise['nama'] }}</span>
                                                            <span class="exercise-meta">{{ $exercise['set'] }} × {{ $exercise['rep'] }} · RPE {{ $exercise['rpe'] }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <p class="mt-1 text-[11px] text-muted">Tidak ada latihan terjadwal. Fokus pada tidur dan pemulihan.</p>
                                            @endif

                                            @if (! empty($session->detail['catatan']))
                                                <p class="mt-2 text-[11px] text-muted">{{ $session->detail['catatan'] }}</p>
                                            @endif
                                        </div>

                                        <div class="session-action">
                                            @if (! $session->isRest())
                                                @if ($canToggle)
                                                    <form method="POST" action="{{ route('jadwal.complete', $session) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <x-button
                                                            type="submit"
                                                            variant="{{ $session->completed_at ? 'secondary' : 'accent' }}"
                                                            size="sm"
                                                            :aria-pressed="$session->completed_at ? 'true' : 'false'"
                                                        >
                                                            {{ $session->completed_at ? 'Batalkan' : 'Tandai selesai' }}
                                                        </x-button>
                                                    </form>
                                                @else
                                                    <span class="text-[11px] text-muted">
                                                        {{ $week > $currentWeek ? 'Belum waktunya' : 'Terkunci' }}
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-[11px] text-muted">Istirahat</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </x-card>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection
