@extends('layouts.app')

@section('title', 'Perbandingan Model — GymSense')
@section('description', 'Perbandingan Accuracy, Precision, Recall, F1 macro, dan confusion matrix dari Logistic Regression, Random Forest, dan SVM.')

@section('content')
    @php
        $models = $metrics['models'] ?? [];
        $classes = data_get($metrics, 'dataset.classes', ['Beginner', 'Intermediate', 'Advanced']);
        $best = $metrics['best_model'] ?? [];
        $bestKey = $best['key'] ?? null;
        $bestModel = collect($models)->firstWhere('key', $bestKey) ?? ($models[0] ?? []);
        $metricKeys = ['accuracy' => 'Accuracy', 'precision' => 'Precision', 'recall' => 'Recall', 'f1' => 'F1 macro'];
        $bestValues = [];
        foreach ($metricKeys as $key => $label) {
            $bestValues[$key] = collect($models)->pluck($key)->filter(fn ($value) => $value !== null)->max();
        }
        $modelIcons = [
            'random_forest' => ['layers', 'teal'],
            'logistic_regression' => ['chart', 'purple'],
            'svm' => ['gauge', 'orange'],
        ];
        $sourceLabels = [
            'live' => ['Metrik live dari FastAPI', 'pill-accent'],
            'local' => ['Dibaca dari artifacts lokal', ''],
            'unavailable' => ['Metrik belum tersedia', ''],
        ];
        $sourceLabel = $sourceLabels[$source] ?? $sourceLabels['unavailable'];
        $formatPercent = fn ($value) => $value !== null ? number_format((float) $value * 100, 1, ',', '.') : '—';
        $formatPercentShort = fn ($value) => $value !== null ? number_format((float) $value * 100, 0, ',', '.') : '—';
    @endphp

    <section class="pt-16 pb-8">
        <div class="site-container">
            <div class="flex flex-wrap items-end justify-between gap-6">
                <div class="max-w-2xl">
                    <span class="eyebrow">Perbandingan model</span>
                    <h1 class="mt-5 text-4xl font-semibold tracking-[-0.04em] md:text-5xl">
                        Tiga algoritma, <span class="hl">satu test set</span>.
                    </h1>
                    <p class="mt-4 max-w-xl text-[15px] leading-relaxed text-muted">
                        Semua skor dihitung pada 20% data yang tidak dipakai untuk training.
                        F1 macro menjadi dasar pemilihan model karena kelas Advanced lebih sedikit.
                    </p>
                </div>
                <span class="pill {{ $sourceLabel[1] }}">
                    <x-icon name="server" class="h-3 w-3" />
                    {{ $sourceLabel[0] }}
                </span>
            </div>
        </div>
    </section>

    @if (empty($models))
        <section class="pb-20">
            <div class="site-container">
                <x-card class="reveal is-visible">
                    <div class="flex flex-col items-start gap-5 md:flex-row md:items-center">
                        <x-badge-icon name="info" color="orange" />
                        <div class="flex-1">
                            <h2 class="text-lg font-semibold">Metrik belum tersedia</h2>
                            <p class="mt-2 text-sm leading-relaxed text-muted">
                                Jalankan training untuk menghasilkan <code class="text-accent-soft">metrics.json</code>
                                dan artefak model. Laravel akan membaca hasilnya secara lokal meskipun
                                server FastAPI sedang mati.
                            </p>
                            <pre class="mt-4 overflow-x-auto rounded-lg border border-white/5 bg-[#0A0A0A]/30 p-4 text-[11px] text-accent-soft">ml-service/.venv/Scripts/python.exe ml-service/train.py</pre>
                        </div>
                    </div>
                </x-card>
            </div>
        </section>
    @else
        <div class="site-container reveal">
            <div class="stat-strip">
                <x-stat :value="number_format(data_get($metrics, 'dataset.rows', 0), 0, ',', '.')" label="Baris dataset" />
                <x-stat :value="data_get($metrics, 'dataset.features', '—')" label="Fitur model" />
                <x-stat :value="count($classes)" label="Kelas target" />
                <x-stat :value="number_format($best['value'] ?? 0, 4, ',', '.')" label="F1 macro terbaik" />
            </div>
        </div>

        <section class="section-tight">
            <div class="site-container grid gap-6 lg:grid-cols-[0.9fr_1.1fr] lg:items-stretch">
                <div class="card card-pad relative overflow-hidden reveal" style="border-color: rgba(99, 102, 241, .35); background: radial-gradient(ellipse at 80% 0%, rgba(99, 102, 241, .24), transparent 62%), var(--card);">
                    <div class="relative z-10">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <div class="text-[10px] font-medium uppercase tracking-[0.14em] text-accent-soft">Model terpilih</div>
                                <h2 class="mt-3 text-3xl font-semibold tracking-tight">{{ $best['name'] ?? '—' }}</h2>
                                <p class="mt-2 text-xs text-muted">Dipilih berdasarkan F1 macro tertinggi.</p>
                            </div>
                            <x-badge-icon name="sparkle" color="teal" />
                        </div>

                        <div class="mt-8 flex items-end gap-3">
                            <span class="text-6xl font-semibold tracking-[-0.06em] text-accent-soft">{{ $formatPercent($best['value'] ?? null) }}</span>
                            <span class="mb-2 text-sm text-muted">% F1 macro</span>
                        </div>

                        <div class="mt-7 grid grid-cols-2 gap-3">
                            <div class="detail-cell">
                                <small>Metric average</small>
                                <strong>Macro</strong>
                            </div>
                            <div class="detail-cell">
                                <small>Split</small>
                                <strong>80 / 20 stratified</strong>
                            </div>
                            <div class="detail-cell">
                                <small>Random state</small>
                                <strong>42</strong>
                            </div>
                            <div class="detail-cell">
                                <small>Preprocessing</small>
                                <strong>Scaler + OneHot</strong>
                            </div>
                        </div>

                        <p class="mt-6 text-[11px] leading-relaxed text-muted">
                            {{ collect($models)->firstWhere('key', $bestKey)['provenance'] ?? '' }}
                        </p>
                    </div>
                </div>

                <div class="card card-pad reveal">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-base font-semibold">Ringkasan metrik</h2>
                            <p class="mt-1 text-xs text-muted">Nilai terbaik per kolom diberi aksen.</p>
                        </div>
                        <x-icon name="chart" class="h-5 w-5 text-muted" />
                    </div>

                    <div class="mt-5 overflow-x-auto">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Model</th>
                                    @foreach ($metricKeys as $label)
                                        <th>{{ $label }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($models as $model)
                                    @php $icon = $modelIcons[$model['key']] ?? ['layers', 'purple']; @endphp
                                    <tr>
                                        <td>
                                            <span class="model-name">
                                                <x-icon :name="$icon[0]" class="h-3.5 w-3.5 text-accent-soft" />
                                                {{ $model['name'] }}
                                                @if ($model['key'] === $bestKey)
                                                    <span class="pill pill-accent">Best</span>
                                                @endif
                                            </span>
                                        </td>
                                        @foreach ($metricKeys as $key => $label)
                                            <td class="{{ ($bestValues[$key] ?? null) !== null && ($model[$key] ?? null) === $bestValues[$key] ? 'best' : '' }}">
                                                {{ $formatPercent($model[$key] ?? null) }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <p class="mt-4 text-[11px] leading-relaxed text-muted">
                        Precision, recall, dan F1 memakai rata-rata macro. Accuracy ditampilkan
                        sebagai pembanding, bukan satu-satunya dasar pemilihan.
                    </p>
                </div>
            </div>
        </section>

        <section class="pb-10">
            <div class="site-container">
                <div class="mb-6 flex flex-wrap items-end justify-between gap-4 reveal">
                    <div>
                        <span class="eyebrow">Grafik batang</span>
                        <h2 class="section-title text-3xl!">Selisih performa antar model.</h2>
                    </div>
                    <span class="pill"><x-icon name="info" class="h-3 w-3" /> Skala 0–100%</span>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    @foreach ($metricKeys as $key => $label)
                        <x-card class="reveal">
                            <div class="flex items-center justify-between gap-3">
                                <h3 class="text-sm font-semibold">{{ $label }}</h3>
                                <span class="text-[10px] uppercase tracking-wider text-muted">Macro</span>
                            </div>
                            <div class="chart mt-5">
                                @foreach ($models as $model)
                                    @php
                                        $value = $model[$key] ?? null;
                                        $isBest = $value !== null && $value === ($bestValues[$key] ?? null);
                                    @endphp
                                    <div class="chart-bar">
                                        <div class="flex items-center justify-between gap-2 text-[10.5px]">
                                            <span class="{{ $isBest ? 'text-accent-soft' : 'text-muted' }}">{{ $model['name'] }}</span>
                                            <span class="chart-value">{{ $formatPercent($value) }}%</span>
                                        </div>
                                        <div class="chart-track">
                                            <div class="chart-fill {{ $isBest ? 'is-best' : '' }}" style="width: {{ max(1, ((float) $value) * 100) }}%;"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </x-card>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="section-tight">
            <div class="site-container">
                <div class="mb-6 reveal">
                    <span class="eyebrow">Confusion matrix</span>
                    <h2 class="section-title text-3xl!">Di mana model masih tertukar.</h2>
                    <p class="section-copy mx-0!">
                        Baris = label aktual, kolom = prediksi. Diagonal adalah prediksi benar.
                        Warna indigo lebih terang berarti jumlah kasus lebih banyak.
                    </p>
                </div>

                <div class="grid gap-4 lg:grid-cols-3">
                    @foreach ($models as $model)
                        @php
                            $matrix = $model['confusion_matrix'] ?? [];
                            $maxCell = collect($matrix)->flatten()->max() ?: 1;
                            $icon = $modelIcons[$model['key']] ?? ['layers', 'purple'];
                        @endphp
                        <x-card class="reveal">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <x-badge-icon :name="$icon[0]" :color="$icon[1]" />
                                    <div>
                                        <h3 class="text-sm font-semibold">{{ $model['name'] }}</h3>
                                        <p class="mt-1 text-[10.5px] text-muted">F1 {{ $formatPercent($model['f1'] ?? null) }}%</p>
                                    </div>
                                </div>
                                @if ($model['key'] === $bestKey)
                                    <span class="pill pill-accent">Best</span>
                                @endif
                            </div>

                            <div class="heatmap-wrap mt-6">
                                <table class="heatmap">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            @foreach ($classes as $class)
                                                <th>{{ $class }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($classes as $rowIndex => $rowClass)
                                            <tr>
                                                <th class="text-right">{{ $rowClass }}</th>
                                                @foreach ($classes as $colIndex => $colClass)
                                                    @php
                                                        $value = (int) ($matrix[$rowIndex][$colIndex] ?? 0);
                                                        $alpha = 0.08 + (0.72 * ($value / max(1, $maxCell)));
                                                    @endphp
                                                    <td
                                                        class="{{ $rowIndex === $colIndex ? 'diag' : '' }}"
                                                        style="background: rgba(99, 102, 241, {{ number_format($alpha, 3, '.', '') }});"
                                                        title="Aktual {{ $rowClass }}, prediksi {{ $colClass }}: {{ $value }}"
                                                    >
                                                        {{ $value }}
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </x-card>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="pb-10">
            <div class="site-container">
                <div class="mb-6 flex flex-wrap items-end justify-between gap-4 reveal">
                    <div>
                        <span class="eyebrow">Classification report</span>
                        <h2 class="section-title text-3xl!">Per kelas, bukan hanya rata-rata.</h2>
                        <p class="section-copy mx-0!">
                            Metrik per kelas untuk model terbaik: {{ $bestModel['name'] ?? '—' }}.
                            Support adalah jumlah baris pada test set untuk kelas tersebut.
                        </p>
                    </div>
                    <span class="pill"><x-icon name="target" class="h-3 w-3" /> F1 per kelas</span>
                </div>

                <x-card class="reveal">
                    <div class="overflow-x-auto">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Kelas</th>
                                    <th>Precision</th>
                                    <th>Recall</th>
                                    <th>F1</th>
                                    <th>Support</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse (($bestModel['per_class'] ?? []) as $row)
                                    <tr>
                                        <td class="best">{{ $row['label'] ?? '—' }}</td>
                                        <td>{{ $formatPercent($row['precision'] ?? null) }}%</td>
                                        <td>{{ $formatPercent($row['recall'] ?? null) }}%</td>
                                        <td>{{ $formatPercent($row['f1'] ?? null) }}%</td>
                                        <td>{{ number_format((int) ($row['support'] ?? 0), 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-muted">Classification report tidak tersedia.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-card>
            </div>
        </section>

        <section class="pb-20">
            <div class="site-container grid gap-6 lg:grid-cols-[1fr_1fr]">
                <x-card class="reveal">
                    <div class="flex items-center gap-3">
                        <x-badge-icon name="eye" color="purple" />
                        <div>
                            <h2 class="text-base font-semibold">Catatan analisis</h2>
                            <p class="mt-1 text-xs text-muted">Dibaca dari artifacts/metrics.json.</p>
                        </div>
                    </div>

                    <div class="mt-5 space-y-3">
                        @forelse (($metrics['analysis']['frequently_confused'] ?? []) as $pair)
                            <div class="flex items-center justify-between gap-3 rounded-lg border border-white/5 bg-[#0A0A0A]/20 px-3 py-2.5 text-[11.5px]">
                                <span class="text-muted">{{ $pair['from'] }} → {{ $pair['to'] }}</span>
                                <span class="pill">{{ $pair['count'] }} kasus</span>
                            </div>
                        @empty
                            <p class="text-xs text-muted">Tidak ada kesalahan antar-kelas pada model terbaik.</p>
                        @endforelse
                    </div>

                    <div class="mt-6 space-y-3">
                        @foreach (($metrics['analysis']['notes'] ?? []) as $note)
                            <div class="flex items-start gap-2 text-[11.5px] leading-relaxed text-muted">
                                <x-icon name="info" class="mt-0.5 h-3.5 w-3.5 flex-none text-accent-soft" />
                                <span>{{ $note }}</span>
                            </div>
                        @endforeach
                    </div>
                </x-card>

                <x-card class="reveal">
                    <div class="flex items-center gap-3">
                        <x-badge-icon name="target" color="orange" />
                        <div>
                            <h2 class="text-base font-semibold">Fitur paling berpengaruh</h2>
                            <p class="mt-1 text-xs text-muted">Feature importance dari Random Forest.</p>
                        </div>
                    </div>

                    <div class="mt-5 space-y-3">
                        @forelse (($metrics['analysis']['top_features'] ?? []) as $feature)
                            <div>
                                <div class="flex items-center justify-between gap-3 text-[11px]">
                                    <span class="text-muted">{{ $feature['feature'] }}</span>
                                    <span class="text-[#d6d9e8]">{{ number_format($feature['importance'] * 100, 1, ',', '.') }}%</span>
                                </div>
                                <div class="chart-track mt-1.5">
                                    <div class="chart-fill" style="width: {{ min(100, max(2, $feature['importance'] * 100 * 2)) }}%;"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-muted">Feature importance tidak tersedia.</p>
                        @endforelse
                    </div>
                </x-card>
            </div>
        </section>

        <section class="pb-20">
            <div class="site-container">
                <x-card class="reveal" style="border-color: rgba(245, 160, 59, .24);">
                    <div class="flex flex-col gap-4 md:flex-row md:items-start">
                        <x-badge-icon name="alert" color="orange" />
                        <div class="flex-1">
                            <h2 class="text-base font-semibold">Keterbatasan yang perlu dibaca</h2>
                            <div class="mt-3 grid gap-3 md:grid-cols-2">
                                <div class="info-block">
                                    <small>Ukuran data</small>
                                    <p>973 baris dan rentang usia 18–59 tahun. Tidak mewakili semua populasi atau kondisi kesehatan.</p>
                                </div>
                                <div class="info-block">
                                    <small>Artifact Random Forest</small>
                                    <p>Model berasal dari artifact pengguna; proses split aslinya tidak terdokumentasi sehingga skornya bisa sedikit optimistis.</p>
                                </div>
                                <div class="info-block">
                                    <small>Fitur estimasi</small>
                                    <p>BPM, kalori, durasi, dan asupan air diestimasi bila tidak diisi. Isi metrik opsional untuk hasil lebih presisi.</p>
                                </div>
                                <div class="info-block">
                                    <small>Bukan diagnosis</small>
                                    <p>Level yang diprediksi adalah rekomendasi umum untuk edukasi, bukan diagnosis medis atau jaminan hasil.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-card>
            </div>
        </section>
    @endif

    <section class="pb-20">
        <div class="site-container">
            <div class="card card-pad flex flex-col items-start justify-between gap-6 md:flex-row md:items-center reveal" style="border-color: rgba(99, 102, 241, .35); background: radial-gradient(ellipse at 15% 0%, rgba(99, 102, 241, .18), transparent 58%), var(--card);">
                <div>
                    <span class="eyebrow">Siap mencoba?</span>
                    <h2 class="mt-3 text-2xl font-semibold tracking-tight">Lihat rekomendasi untuk profilmu.</h2>
                    <p class="mt-2 max-w-lg text-sm text-muted">
                        Model yang sama dipakai di halaman rekomendasi. Bandingkan hasilnya
                        dengan metrik di halaman ini.
                    </p>
                </div>
                <x-button :href="route('rekomendasi.create')" variant="accent" size="lg" icon="arrow-right">
                    Buka formulir
                </x-button>
            </div>
        </div>
    </section>
@endsection
