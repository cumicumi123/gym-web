@extends('layouts.app')

@section('title', 'Hasil Rekomendasi — GymSense')
@section('description', 'Level, fokus program, dan rencana latihan berdasarkan profil yang kamu isi.')

@section('content')
    @php
        $classes = ['Beginner', 'Intermediate', 'Advanced'];
        $probabilityPercent = fn ($value) => number_format(((float) $value) * 100, 1, ',', '.');
        $confidence = ((float) ($result['probabilitas'] ?? 0)) * 100;
        $estimated = $result['estimated'] ?? [];
        $adjusted = $result['adjusted'] ?? [];
        $levelLabel = __('level.'.$levelKey);
        $sourceStyles = [
            'input' => 'border-color: rgba(99, 102, 241, .35); color: #c7d2ff; background: rgba(99, 102, 241, .1);',
            'estimasi' => 'border-color: rgba(245, 160, 59, .3); color: #fde3bd; background: rgba(245, 160, 59, .07);',
            'turunan' => 'border-color: rgba(59, 184, 176, .3); color: #a9e7e3; background: rgba(59, 184, 176, .07);',
            'disesuaikan' => 'border-color: rgba(239, 90, 90, .3); color: #fecaca; background: rgba(239, 90, 90, .07);',
        ];
        $sourceLabels = [
            'input' => 'Isian',
            'estimasi' => 'Perkiraan',
            'turunan' => 'Dihitung',
            'disesuaikan' => 'Disesuaikan',
        ];
        $sourceTitles = [
            'estimasi' => 'Dihitung dari data profilmu',
            'turunan' => 'Dihitung dari data profilmu',
        ];
    @endphp

    <section class="result-hero">
        <div class="site-container">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <a href="{{ route('rekomendasi.create') }}" class="text-link mt-0!">
                    <x-icon name="arrow-right" class="h-3.5 w-3.5 rotate-180" />
                    Ubah profil
                </a>
                <span class="pill"><x-icon name="activity" class="h-3 w-3" /> Rekomendasi latihan</span>
            </div>

            <div class="result-card mt-6 reveal is-visible">
                <div class="grid gap-0 lg:grid-cols-[1.15fr_0.85fr]">
                    <div class="p-7 md:p-10">
                        <div class="result-label">Level kamu</div>
                        <h1 class="result-level">{{ $levelLabel }}</h1>
                        <p class="mt-4 max-w-lg text-sm leading-relaxed text-muted">
                            {{ $level['description'] ?? 'Level ini disarankan berdasarkan profil dan pengalaman latihan yang kamu berikan.' }}
                        </p>

                        <div class="result-meta">
                            <span class="pill pill-accent"><x-icon name="{{ $goal['icon'] ?? 'target' }}" class="h-3 w-3" /> {{ $goal['label'] ?? ucfirst($data['tujuan']) }}</span>
                            <span class="pill">{{ $level['intensity'] ?? 'Intensitas sesuai level' }}</span>
                            <span class="pill">BMI {{ number_format($bmi, 1, ',', '.') }} · {{ $bmiCategory['label'] }}</span>
                        </div>

                        <div class="confidence-row">
                            <div class="confidence-track" aria-hidden="true">
                                <div class="confidence-fill" style="width: {{ max(2, min(100, $confidence)) }}%;"></div>
                            </div>
                            <div class="confidence-value">{{ $probabilityPercent($result['probabilitas'] ?? 0) }}%</div>
                        </div>
                        <p class="mt-2 text-[11px] text-muted">Kecocokan program: {{ $probabilityPercent($result['probabilitas'] ?? 0) }}%.</p>

                        <div class="mt-7 flex flex-wrap gap-3">
                            <x-button :href="route('rekomendasi.create')" variant="secondary" icon="refresh">
                                Coba profil lain
                            </x-button>
                        </div>
                    </div>

                    <div class="border-t border-white/5 bg-[#0A0A0A]/20 p-7 md:p-10 lg:border-l lg:border-t-0">
                        <div class="text-[10px] font-medium uppercase tracking-[0.14em] text-muted">Perkiraan kecocokan tiap level</div>

                        <div class="prob-list">
                            @foreach ($classes as $class)
                                @php $probability = $result['probabilities'][$class] ?? 0; @endphp
                                <div class="prob-row">
                                    <span class="{{ $class === $levelKey ? 'text-accent-soft' : '' }}">{{ __('level.'.$class) }}</span>
                                    <div class="prob-track">
                                        <div class="prob-fill {{ $class === $levelKey ? '' : 'is-muted' }}" style="width: {{ max(1.5, min(100, $probability * 100)) }}%;"></div>
                                    </div>
                                    <span class="prob-value">{{ $probabilityPercent($probability) }}%</span>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-8 border-t border-white/5 pt-6">
                            <div class="text-[10px] font-medium uppercase tracking-[0.14em] text-muted">Ringkasan profil</div>
                            <div class="mt-4 grid grid-cols-2 gap-3">
                                <div class="detail-cell">
                                    <small>Usia</small>
                                    <strong>{{ $data['usia'] }} tahun</strong>
                                </div>
                                <div class="detail-cell">
                                    <small>Pengalaman</small>
                                    <strong>{{ $data['pengalaman_bulan'] }} bulan</strong>
                                </div>
                                <div class="detail-cell">
                                    <small>Berat</small>
                                    <strong>{{ number_format((float) $data['berat'], 1, ',', '.') }} kg</strong>
                                </div>
                                <div class="detail-cell">
                                    <small>Tinggi</small>
                                    <strong>{{ number_format((float) $data['tinggi'], 0, ',', '.') }} cm</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="pb-16">
        <div class="site-container grid gap-6 lg:grid-cols-[1.3fr_0.7fr] lg:items-start">
            <div class="space-y-6">
                <x-card class="reveal is-visible">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <div class="text-[10px] font-medium uppercase tracking-[0.14em] text-accent-soft">Rencana program</div>
                            <h2 class="mt-3 text-2xl font-semibold tracking-tight">
                                {{ $program['headline'] ?? 'Program sesuai level kamu' }}
                            </h2>
                            <p class="mt-3 max-w-2xl text-sm leading-relaxed text-muted">
                                {{ $program['summary'] ?? '' }}
                            </p>
                        </div>
                        <x-badge-icon name="{{ $goal['icon'] ?? 'target' }}" :color="$goal['badge'] ?? 'purple'" />
                    </div>

                    <div class="mt-7 grid grid-cols-2 gap-3 md:grid-cols-4">
                        <div class="detail-cell">
                            <small>Frekuensi</small>
                            <strong>{{ $program['frequency'] ?? '—' }}</strong>
                        </div>
                        <div class="detail-cell">
                            <small>Durasi</small>
                            <strong>{{ $program['duration'] ?? '—' }}</strong>
                        </div>
                        <div class="detail-cell">
                            <small>Split</small>
                            <strong>{{ $program['split'] ?? '—' }}</strong>
                        </div>
                        <div class="detail-cell">
                            <small>Intensitas</small>
                            <strong>{{ $program['intensity'] ?? '—' }}</strong>
                        </div>
                    </div>

                    <div class="mt-7 grid gap-6 md:grid-cols-2">
                        <div>
                            <h3 class="text-sm font-semibold">Fokus latihan</h3>
                            <ul class="check-list mt-4">
                                @foreach (($program['focus'] ?? []) as $item)
                                    <li><x-icon name="check" class="h-3.5 w-3.5" /> {{ $item }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="grid gap-3">
                            <div class="info-block">
                                <small>Kardio</small>
                                <p>{{ $program['cardio'] ?? '—' }}</p>
                            </div>
                            <div class="info-block">
                                <small>Nutrisi</small>
                                <p>{{ $program['nutrition'] ?? '—' }}</p>
                            </div>
                            <div class="info-block">
                                <small>Progresi</small>
                                <p>{{ $program['progression'] ?? '—' }}</p>
                            </div>
                        </div>
                    </div>

                    @if (! empty($program['caution']))
                        <div class="alert alert-warn mt-6">
                            <x-icon name="alert" class="mt-0.5 h-4 w-4 flex-none" />
                            <div>{{ $program['caution'] }}</div>
                        </div>
                    @endif
                </x-card>

                <x-card class="reveal is-visible">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <div class="text-[10px] font-medium uppercase tracking-[0.14em] text-accent-soft">Perkiraan nutrisi</div>
                            <h2 class="mt-3 text-2xl font-semibold tracking-tight">Bahan bakar harian</h2>
                            <p class="mt-3 max-w-2xl text-sm leading-relaxed text-muted">
                                Angka ini perkiraan dari berat, tinggi, usia, tujuan, dan frekuensi latihanmu.
                                Gunakan rentangnya sebagai panduan, bukan target yang harus pas setiap hari.
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="pill pill-accent">Perkiraan</span>
                            <x-badge-icon name="flame" color="orange" />
                        </div>
                    </div>

                    <div class="mt-7 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        <div class="detail-cell">
                            <small>Kalori</small>
                            <strong>{{ number_format($nutrition['calories_min'], 0, ',', '.') }}–{{ number_format($nutrition['calories_max'], 0, ',', '.') }} kkal</strong>
                            <span class="mt-1 block text-[11px] text-muted">Target {{ number_format($nutrition['calories'], 0, ',', '.') }} kkal</span>
                        </div>
                        <div class="detail-cell">
                            <small>Protein</small>
                            <strong>{{ $nutrition['protein_min_g'] }}–{{ $nutrition['protein_max_g'] }} g</strong>
                            <span class="mt-1 block text-[11px] text-muted">Target {{ $nutrition['protein_g'] }} g</span>
                        </div>
                        <div class="detail-cell">
                            <small>Lemak</small>
                            <strong>{{ $nutrition['fat_min_g'] }}–{{ $nutrition['fat_max_g'] }} g</strong>
                            <span class="mt-1 block text-[11px] text-muted">Target {{ $nutrition['fat_g'] }} g</span>
                        </div>
                        <div class="detail-cell">
                            <small>Karbohidrat</small>
                            <strong>{{ $nutrition['carbs_min_g'] }}–{{ $nutrition['carbs_max_g'] }} g</strong>
                            <span class="mt-1 block text-[11px] text-muted">Target {{ $nutrition['carbs_g'] }} g</span>
                        </div>
                        <div class="detail-cell">
                            <small>Serat</small>
                            <strong>{{ $nutrition['fiber_min_g'] }}–{{ $nutrition['fiber_max_g'] }} g</strong>
                        </div>
                        <div class="detail-cell">
                            <small>Air</small>
                            <strong>{{ number_format($nutrition['water_min_ml'] / 1000, 1, ',', '.') }}–{{ number_format($nutrition['water_max_ml'] / 1000, 1, ',', '.') }} L</strong>
                            <span class="mt-1 block text-[11px] text-muted">Target {{ number_format($nutrition['water_ml'] / 1000, 1, ',', '.') }} L</span>
                        </div>
                    </div>

                    <div class="mt-5 grid gap-3 sm:grid-cols-2">
                        <div class="info-block">
                            <small>BMR</small>
                            <p>{{ number_format($nutrition['bmr'], 0, ',', '.') }} kkal</p>
                        </div>
                        <div class="info-block">
                            <small>Perkiraan TDEE</small>
                            <p>{{ number_format($nutrition['tdee'], 0, ',', '.') }} kkal · faktor aktivitas {{ number_format($nutrition['activity_factor'], 2, ',', '.') }}</p>
                        </div>
                    </div>

                    @foreach ($nutrition['notes'] as $note)
                        <div class="alert alert-accent mt-4">
                            <x-icon name="info" class="mt-0.5 h-4 w-4 flex-none" />
                            <div>{{ $note }}</div>
                        </div>
                    @endforeach

                    @foreach ($nutrition['warnings'] as $warning)
                        <div class="alert alert-warn mt-3">
                            <x-icon name="alert" class="mt-0.5 h-4 w-4 flex-none" />
                            <div>{{ $warning }}</div>
                        </div>
                    @endforeach

                    <p class="mt-5 text-[11px] leading-relaxed text-muted">
                        {{ $nutrition['disclaimer'] }} Jangan menjadikan angka ini sebagai patokan pasti;
                        sesuaikan dengan rasa kenyang, performa latihan, dan kondisi tubuh.
                    </p>
                </x-card>

                <x-card class="reveal">
                    <div class="flex items-start gap-3">
                        <x-badge-icon name="eye" color="teal" />
                        <div>
                            <h2 class="text-base font-semibold">Cara membaca hasil</h2>
                            <p class="mt-2 text-sm leading-relaxed text-muted">
                                Level ini disarankan dari usia, berat, tinggi, tujuan, dan pengalaman
                                latihanmu. Anggap sebagai titik awal, lalu sesuaikan dengan kondisi
                                tubuhmu.
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-4 md:grid-cols-3">
                        <div class="info-block">
                            <small>Level kamu</small>
                            <p>{{ $levelLabel }}</p>
                        </div>
                        <div class="info-block">
                            <small>Fokus program</small>
                            <p>{{ $goal['tagline'] ?? $goal['label'] ?? 'Sesuai tujuanmu' }}</p>
                        </div>
                        <div class="info-block">
                            <small>Frekuensi per minggu</small>
                            <p>{{ $program['frequency'] ?? 'Sesuaikan dengan pemulihan' }}</p>
                        </div>
                    </div>
                </x-card>
            </div>

            <aside class="space-y-4 lg:sticky lg:top-28">
                <x-card class="reveal is-visible">
                    @auth
                        @php $allDays = config('workout_templates.days'); @endphp
                        <div class="flex items-center gap-3">
                            <x-badge-icon name="calendar" color="teal" />
                            <div>
                                <h2 class="text-sm font-semibold">Simpan sebagai program</h2>
                                <p class="mt-1 text-[11px] text-muted">Program, jadwal, dan progres tersimpan di akunmu.</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('program.store') }}" class="form-stack mt-5">
                            @csrf
                            <input type="hidden" name="level" value="{{ $levelKey }}">
                            <input type="hidden" name="goal" value="{{ $data['tujuan'] }}">
                            <input type="hidden" name="usia" value="{{ $data['usia'] }}">
                            <input type="hidden" name="berat_kg" value="{{ $data['berat'] }}">
                            <input type="hidden" name="tinggi_cm" value="{{ $data['tinggi'] }}">
                            <input type="hidden" name="pengalaman_bulan" value="{{ $data['pengalaman_bulan'] }}">
                            <input type="hidden" name="gender" value="{{ $data['gender'] ?? '' }}">
                            <input type="hidden" name="split" value="{{ $program['split'] ?? '' }}">
                            <input type="hidden" name="sesi_per_minggu" value="{{ $sessionsPerWeek }}">

                            <fieldset>
                                <legend class="field-label">Hari latihan</legend>
                                <p class="mb-3 text-[11px] leading-relaxed text-muted">
                                    Pilih hari yang realistis. Jadwal 4 minggu akan dibuat dari pilihan ini.
                                </p>
                                <div class="choice-group">
                                    @foreach ($allDays as $day)
                                        <label class="choice">
                                            <input type="checkbox" name="schedule_days[]" value="{{ $day }}" @checked(in_array($day, old('schedule_days', $defaultDays), true))>
                                            <span>{{ $day }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </fieldset>

                            <label class="choice choice-consent">
                                <input type="checkbox" name="consent" value="1" required @checked(old('consent'))>
                                <span>Saya setuju data profil, program, dan check-in disimpan di akun ini.</span>
                            </label>

                            <x-button type="submit" variant="accent" icon="check" class="w-full">
                                Simpan program
                            </x-button>

                            <p class="text-[11px] leading-relaxed text-muted">
                                Data bisa diekspor atau dihapus kapan saja dari halaman Akun.
                            </p>
                        </form>
                    @else
                        <div class="flex items-center gap-3">
                            <x-badge-icon name="lock" color="purple" />
                            <div>
                                <h2 class="text-sm font-semibold">Simpan sebagai program</h2>
                                <p class="mt-1 text-[11px] text-muted">Butuh akun agar data tidak hilang.</p>
                            </div>
                        </div>

                        <p class="mt-4 text-[12px] leading-relaxed text-muted">
                            Hasil rekomendasi tetap bisa dilihat tanpa akun. Untuk menyimpan program,
                            jadwal, dan check-in mingguan, masuk atau buat akun dulu.
                        </p>

                        <div class="mt-4 grid gap-2">
                            <x-button :href="route('login')" variant="accent" icon="user" class="w-full">Masuk</x-button>
                            <x-button :href="route('register')" variant="secondary" icon="arrow-right" class="w-full">Daftar gratis</x-button>
                        </div>
                    @endauth
                </x-card>

                <x-card class="reveal is-visible">
                    <div class="flex items-center gap-3">
                        <x-badge-icon name="database" color="purple" />
                        <div>
                            <h2 class="text-sm font-semibold">Data yang dipakai</h2>
                            <p class="mt-1 text-[11px] text-muted">Isianmu dan perkiraan pendukung.</p>
                        </div>
                    </div>

                    @if (! empty($estimated))
                        <div class="alert alert-accent mt-5">
                            <x-icon name="info" class="mt-0.5 h-4 w-4 flex-none" />
                            <div>
                                <strong class="block font-semibold">Ada {{ count($estimated) }} data yang kami perkirakan.</strong>
                                Lengkapi bagian “Data latihan tambahan” untuk hasil yang lebih pas.
                            </div>
                        </div>
                    @endif

                    @if (! empty($adjusted))
                        <div class="alert alert-warn mt-3">
                            <x-icon name="alert" class="mt-0.5 h-4 w-4 flex-none" />
                            <div>Beberapa angka disesuaikan agar tetap wajar: {{ implode(', ', $adjusted) }}.</div>
                        </div>
                    @endif

                    @if (! empty($result['catatan']))
                        <div class="alert alert-warn mt-3">
                            <x-icon name="alert" class="mt-0.5 h-4 w-4 flex-none" />
                            <div>BMI-mu di luar rentang umum, jadi kami memakai angka yang lebih wajar untuk menyusun program. BMI asli tetap ditampilkan di atas.</div>
                        </div>
                    @endif

                    <div class="mt-5 divide-y divide-white/5">
                        @foreach (($result['fitur_model'] ?? []) as $feature)
                            <div class="flex items-center justify-between gap-3 py-2.5 text-[11.5px]">
                                <span class="text-muted">{{ $feature['label'] }}</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-right text-[#d6d9e8]">
                                        {{ is_numeric($feature['value']) ? rtrim(rtrim(number_format((float) $feature['value'], 2, ',', '.'), '0'), ',') : $feature['value'] }}
                                    </span>
                                    <span class="pill" style="{{ $sourceStyles[$feature['source']] ?? '' }}" @if (isset($sourceTitles[$feature['source']])) title="{{ $sourceTitles[$feature['source']] }}" @endif>
                                        {{ $sourceLabels[$feature['source']] ?? $feature['source'] }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-card>

                <div class="disclaimer reveal is-visible">
                    <x-icon name="shield" class="mt-0.5 h-4 w-4 flex-none" />
                    <div>
                        <strong class="block font-semibold text-[#f2dfc2]">Bukan saran medis.</strong>
                        Ini rekomendasi umum untuk edukasi. Konsultasikan dengan dokter atau
                        pelatih bila kamu punya kondisi kesehatan, cedera, atau sedang hamil.
                    </div>
                </div>

                <x-card class="reveal is-visible">
                    <h2 class="text-sm font-semibold">Langkah berikutnya</h2>
                    <ul class="check-list mt-4">
                        <li><x-icon name="check" class="h-3.5 w-3.5" /> Mulai dari 2–3 sesi pertama dengan beban ringan</li>
                        <li><x-icon name="check" class="h-3.5 w-3.5" /> Catat beban dan repetisi setiap sesi</li>
                        <li><x-icon name="check" class="h-3.5 w-3.5" /> Evaluasi ulang setelah 4 minggu</li>
                    </ul>
                </x-card>
            </aside>
        </div>
    </section>
@endsection
