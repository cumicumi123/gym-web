@extends('layouts.app')

@section('title', 'Progres Latihan — GymSense')
@section('description', 'Check-in mingguan, grafik berat, sesi selesai, dan konsistensi latihan.')

@section('content')
    <section class="section-tight">
        <div class="site-container">
            @if (! $program)
                <div class="max-w-2xl">
                    <span class="eyebrow">Progres</span>
                    <h1 class="mt-5 text-3xl font-semibold tracking-[-0.04em] md:text-4xl">
                        Belum ada <span class="hl">data progres</span>.
                    </h1>
                    <p class="mt-3 text-sm leading-relaxed text-muted">
                        Simpan program dulu, lalu isi check-in mingguan. Grafik akan muncul setelah
                        ada data yang bisa dibandingkan.
                    </p>
                    <x-button :href="route('rekomendasi.create')" variant="accent" size="lg" icon="sparkle" class="mt-6">
                        Mulai rekomendasi
                    </x-button>
                </div>
            @else
                @php
                    $ringRadius = 52;
                    $ringCircumference = 2 * M_PI * $ringRadius;
                    $ringOffset = $ringCircumference * (1 - ($circular['percent'] / 100));

                    $donutRadius = 48;
                    $donutCircumference = 2 * M_PI * $donutRadius;
                    $donutSegments = [
                        ['label' => 'Protein', 'value' => $macroShares['protein'], 'color' => 'var(--accent)'],
                        ['label' => 'Karbohidrat', 'value' => $macroShares['carbs'], 'color' => 'var(--accent-soft)'],
                        ['label' => 'Lemak', 'value' => $macroShares['fat'], 'color' => '#c7d2ff'],
                    ];
                    $donutCursor = 0;
                @endphp

                <div class="flex flex-wrap items-end justify-between gap-5">
                    <div class="max-w-2xl">
                        <span class="eyebrow">Progres</span>
                        <h1 class="mt-5 text-3xl font-semibold tracking-[-0.04em] md:text-4xl">
                            Minggu {{ $currentWeek }} · <span class="hl">{{ $circular['percent'] }}% sesi</span>
                        </h1>
                        <p class="mt-3 text-sm leading-relaxed text-muted">
                            Catat berat dan lingkar pinggang sekali seminggu. Bandingkan pola, bukan
                            angka harian. Sesi yang ditandai selesai di Jadwal ikut terhitung di sini.
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <x-button :href="route('jadwal.index')" variant="accent" icon="calendar">Buka jadwal</x-button>
                        <x-button :href="route('program.show')" variant="secondary" icon="layers">Ringkasan program</x-button>
                    </div>
                </div>

                <x-card class="mt-8">
                    <div class="flex flex-col gap-6 lg:flex-row lg:items-center">
                        <div class="ring-wrap">
                            <svg viewBox="0 0 120 120" class="progress-ring" role="img" aria-label="Progres sesi minggu ini {{ $circular['percent'] }} persen">
                                <defs>
                                    <linearGradient id="ringGradient" x1="0" y1="0" x2="1" y2="1">
                                        <stop offset="0%" stop-color="#6366f1" />
                                        <stop offset="100%" stop-color="#8fb4ff" />
                                    </linearGradient>
                                </defs>
                                <circle cx="60" cy="60" r="{{ $ringRadius }}" fill="none" stroke="rgba(255,255,255,.07)" stroke-width="10" />
                                <circle
                                    cx="60"
                                    cy="60"
                                    r="{{ $ringRadius }}"
                                    fill="none"
                                    stroke="url(#ringGradient)"
                                    stroke-width="10"
                                    stroke-linecap="round"
                                    stroke-dasharray="{{ $ringCircumference }}"
                                    stroke-dashoffset="{{ $ringOffset }}"
                                    transform="rotate(-90 60 60)"
                                />
                                <text x="60" y="58" text-anchor="middle" class="ring-value">{{ $circular['percent'] }}%</text>
                                <text x="60" y="74" text-anchor="middle" class="ring-label">{{ $circular['completed'] }}/{{ $circular['total'] }} sesi</text>
                            </svg>

                            <div class="ring-copy lg:max-w-sm">
                                <p>Progres ke target sesi minggu ini. Tandai sesi di halaman Jadwal setiap kali selesai.</p>
                                <a href="{{ route('jadwal.index') }}" class="text-link mt-2 inline-flex">
                                    Tandai sesi
                                    <x-icon name="arrow-right" class="h-3.5 w-3.5" />
                                </a>
                            </div>
                        </div>
                    </div>
                </x-card>

                <div class="mt-6 grid gap-6 lg:grid-cols-[0.9fr_1.1fr]">
                    <x-card class="flex flex-col">
                        <div class="flex items-center gap-3">
                            <x-badge-icon name="scale" color="teal" />
                            <div>
                                <h2 class="text-base font-semibold">Check-in mingguan</h2>
                                <p class="mt-1 text-[11px] text-muted">Minggu ke-{{ $currentWeek }} dari 4.</p>
                            </div>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger mt-5" role="alert">
                                <x-icon name="alert" class="mt-0.5 h-4 w-4 flex-none" />
                                <ul class="list-inside list-disc space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('progres.checkin') }}" class="form-stack mt-5">
                            @csrf
                            <input type="hidden" name="minggu_ke" value="{{ $currentWeek }}">

                            <div class="form-grid">
                                <div>
                                    <label for="checkin-berat" class="field-label">
                                        Berat badan
                                        <span class="field-hint">30–200 kg</span>
                                    </label>
                                    <div class="field-wrap">
                                        <input id="checkin-berat" type="number" name="berat_kg" min="30" max="200" step="0.1" class="field" value="{{ old('berat_kg', $currentCheckin?->berat_kg ?? $program->berat_kg) }}" required>
                                        <span class="field-unit">kg</span>
                                    </div>
                                </div>

                                <div>
                                    <label for="checkin-pinggang" class="field-label">
                                        Lingkar pinggang
                                        <span class="field-hint">Opsional</span>
                                    </label>
                                    <div class="field-wrap">
                                        <input id="checkin-pinggang" type="number" name="pinggang_cm" min="40" max="200" step="0.1" class="field" value="{{ old('pinggang_cm', $currentCheckin?->pinggang_cm) }}" placeholder="82">
                                        <span class="field-unit">cm</span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label for="checkin-energi" class="field-label">
                                    Level energi
                                    <span class="field-hint">Opsional</span>
                                </label>
                                <select id="checkin-energi" name="energi" class="field">
                                    <option value="">Pilih</option>
                                    @for ($level = 1; $level <= 5; $level++)
                                        <option value="{{ $level }}" @selected((string) old('energi', $currentCheckin?->energi) === (string) $level)>{{ $level }} — {{ ['Sangat rendah', 'Rendah', 'Cukup', 'Baik', 'Sangat baik'][$level - 1] }}</option>
                                    @endfor
                                </select>
                            </div>

                            <div>
                                <label for="checkin-catatan" class="field-label">
                                    Catatan
                                    <span class="field-hint">Opsional</span>
                                </label>
                                <textarea id="checkin-catatan" name="catatan" rows="3" class="field field-textarea" placeholder="Tidur, stres, atau hal yang ingin kamu ingat minggu ini.">{{ old('catatan', $currentCheckin?->catatan) }}</textarea>
                            </div>

                            <x-button type="submit" variant="accent" icon="check" class="w-full">
                                {{ $currentCheckin ? 'Perbarui check-in minggu ini' : 'Simpan check-in' }}
                            </x-button>
                        </form>

                        <p class="mt-4 text-[11px] leading-relaxed text-muted">
                            Berat bisa berubah karena air dan makanan. Lihat tren 2–4 minggu, bukan
                            satu angka.
                        </p>
                    </x-card>

                    <x-card>
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <h2 class="text-base font-semibold">Makro harian</h2>
                                <p class="mt-1 text-[11px] text-muted">Perkiraan pembagian kalori dari target program.</p>
                            </div>
                            <x-badge-icon name="flame" color="orange" />
                        </div>

                        <div class="donut-wrap mt-5">
                            <svg viewBox="0 0 120 120" class="macro-donut" role="img" aria-label="Pembagian kalori protein {{ $macroShares['protein'] }} persen, karbohidrat {{ $macroShares['carbs'] }} persen, lemak {{ $macroShares['fat'] }} persen">
                                <circle cx="60" cy="60" r="{{ $donutRadius }}" fill="none" stroke="rgba(255,255,255,.06)" stroke-width="14" />
                                @foreach ($donutSegments as $segment)
                                    @php
                                        $dash = $donutCircumference * ($segment['value'] / 100);
                                        $offset = $donutCircumference * ($donutCursor / 100);
                                        $donutCursor += $segment['value'];
                                    @endphp
                                    <circle
                                        cx="60"
                                        cy="60"
                                        r="{{ $donutRadius }}"
                                        fill="none"
                                        stroke="{{ $segment['color'] }}"
                                        stroke-width="14"
                                        stroke-dasharray="{{ $dash }} {{ $donutCircumference - $dash }}"
                                        stroke-dashoffset="{{ -$offset }}"
                                        transform="rotate(-90 60 60)"
                                    />
                                @endforeach
                                <text x="60" y="58" text-anchor="middle" class="ring-value">{{ $program->kalori }}</text>
                                <text x="60" y="74" text-anchor="middle" class="ring-label">kkal</text>
                            </svg>

                            <div class="macro-legend">
                                @foreach ($donutSegments as $segment)
                                    <div class="macro-legend-row">
                                        <span class="macro-dot" style="background: {{ $segment['color'] }};"></span>
                                        <span>{{ $segment['label'] }}</span>
                                        <strong>{{ $segment['value'] }}%</strong>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-5 space-y-4">
                            <div class="progress-row">
                                <div class="progress-head"><span>Protein</span><span>{{ $program->protein_g }} g</span></div>
                                <div class="progress-track" role="progressbar" aria-label="Target protein harian" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $macroShares['protein'] }}">
                                    <div class="progress-fill" style="width: {{ $macroShares['protein'] }}%"></div>
                                </div>
                            </div>
                            <div class="progress-row">
                                <div class="progress-head"><span>Karbohidrat</span><span>{{ $program->karbo_g }} g</span></div>
                                <div class="progress-track" role="progressbar" aria-label="Target karbohidrat harian" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $macroShares['carbs'] }}">
                                    <div class="progress-fill" style="width: {{ $macroShares['carbs'] }}%"></div>
                                </div>
                            </div>
                            <div class="progress-row">
                                <div class="progress-head"><span>Lemak</span><span>{{ $program->lemak_g }} g</span></div>
                                <div class="progress-track" role="progressbar" aria-label="Target lemak harian" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $macroShares['fat'] }}">
                                    <div class="progress-fill" style="width: {{ $macroShares['fat'] }}%"></div>
                                </div>
                            </div>
                            <div class="progress-row">
                                <div class="progress-head"><span>Progres program</span><span>Minggu {{ $currentWeek }} / 4</span></div>
                                <div class="progress-track" role="progressbar" aria-label="Progres program 4 minggu" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $macroShares['program'] }}">
                                    <div class="progress-fill is-teal" style="width: {{ $macroShares['program'] }}%"></div>
                                </div>
                            </div>
                        </div>
                    </x-card>
                </div>

                <div class="mt-6 grid gap-6 lg:grid-cols-2">
                    <x-card>
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <h2 class="text-base font-semibold">Tracking berat</h2>
                                <p class="mt-1 text-[11px] text-muted">Area berat aktual per minggu.</p>
                            </div>
                            <x-badge-icon name="chart" color="purple" />
                        </div>

                        @if ($hasCheckins)
                            <div class="chart-box mt-5">
                                <canvas class="chart-canvas" data-chart="weight" data-payload='@json($charts['weight'])' role="img" aria-label="Grafik berat badan aktual per minggu"></canvas>
                            </div>

                            <table class="sr-only">
                                <caption>Ringkasan berat badan per minggu</caption>
                                <thead><tr><th>Minggu</th><th>Aktual</th></tr></thead>
                                <tbody>
                                    @foreach ($charts['weight']['labels'] as $index => $label)
                                        <tr>
                                            <td>{{ $label }}</td>
                                            <td>{{ $charts['weight']['actual'][$index] !== null ? $charts['weight']['actual'][$index].' kg' : 'Belum ada' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <p class="mt-3 text-[11px] leading-relaxed text-muted">{{ $charts['weight']['summary'] }}</p>
                        @else
                            <x-chart-placeholder id="weight-placeholder" class="mt-5" />
                            <div class="alert alert-accent mt-4">
                                <x-icon name="info" class="mt-0.5 h-4 w-4 flex-none" />
                                <div>
                                    <strong class="block font-semibold">Isi check-in minggu pertama.</strong>
                                    Grafik akan muncul setelah check-in pertamamu tersimpan.
                                </div>
                            </div>
                        @endif
                    </x-card>

                    <x-card>
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <h2 class="text-base font-semibold">Lingkar tubuh</h2>
                                <p class="mt-1 text-[11px] text-muted">Garis {{ $charts['combination']['lineLabel'] }} per minggu.</p>
                            </div>
                            <x-badge-icon name="activity" color="teal" />
                        </div>

                        @if ($hasCheckins)
                            <div class="chart-box mt-5">
                                <canvas class="chart-canvas" data-chart="combination" data-payload='@json($charts['combination'])' role="img" aria-label="Grafik {{ $charts['combination']['lineLabel'] }} per minggu"></canvas>
                            </div>

                            <table class="sr-only">
                                <caption>Ringkasan {{ $charts['combination']['lineLabel'] }} per minggu</caption>
                                <thead><tr><th>Minggu</th><th>{{ $charts['combination']['lineLabel'] }}</th></tr></thead>
                                <tbody>
                                    @foreach ($charts['combination']['labels'] as $index => $label)
                                        <tr>
                                            <td>{{ $label }}</td>
                                            <td>{{ $charts['combination']['line'][$index] !== null ? $charts['combination']['line'][$index] : 'Belum ada' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <p class="mt-3 text-[11px] leading-relaxed text-muted">{{ $charts['combination']['summary'] }}</p>
                        @else
                            <x-chart-placeholder id="combination-placeholder" class="mt-5" />
                            <div class="alert alert-accent mt-4">
                                <x-icon name="info" class="mt-0.5 h-4 w-4 flex-none" />
                                <div>Isi check-in dulu untuk melihat grafik lingkar tubuh.</div>
                            </div>
                        @endif
                    </x-card>
                </div>

                <div class="mt-6">
                    <x-card>
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <h2 class="text-base font-semibold">Konsistensi 12 minggu</h2>
                                <p class="mt-1 text-[11px] text-muted">Selesai, terlewat, terjadwal, atau istirahat.</p>
                            </div>
                            <x-badge-icon name="calendar" color="purple" />
                        </div>

                        <div class="heatmap-scroll mt-5">
                            <div class="heatmap-grid">
                                <span class="heatmap-corner"></span>
                                @foreach ($heatmap['weekLabels'] as $label)
                                    <span class="heatmap-week">{{ $label }}</span>
                                @endforeach

                                @foreach ($heatmap['rows'] as $row)
                                    <span class="heatmap-day">{{ $row['day'] }}</span>
                                    @foreach ($row['cells'] as $cell)
                                        <span
                                            class="heat-cell is-{{ $cell['status'] }}"
                                            title="{{ $cell['label'] }} · {{ $cell['status_label'] }}{{ $cell['fokus'] ? ' · '.$cell['fokus'] : '' }}"
                                            aria-label="{{ $cell['label'] }}: {{ $cell['status_label'] }}{{ $cell['fokus'] ? ', '.$cell['fokus'] : '' }}"
                                        ></span>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>

                        <div class="heatmap-legend">
                            <span><i class="is-selesai"></i> Selesai</span>
                            <span><i class="is-terlewat"></i> Terlewat</span>
                            <span><i class="is-terjadwal"></i> Terjadwal</span>
                            <span><i class="is-istirahat"></i> Istirahat</span>
                        </div>
                    </x-card>
                </div>

                <div class="disclaimer mt-6">
                    <x-icon name="shield" class="mt-0.5 h-4 w-4 flex-none" />
                    <div>
                        <strong class="block font-semibold text-[#f2dfc2]">Bukan saran medis.</strong>
                        Grafik ini membantu melihat pola, bukan mendiagnosis kondisi. Hentikan latihan
                        dan cari bantuan profesional bila muncul nyeri tajam, pusing, atau keluhan lain.
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection
