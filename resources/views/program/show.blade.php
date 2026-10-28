@extends('layouts.app')

@section('title', 'Program Aktif — GymSense')
@section('description', 'Ringkasan program, perkiraan nutrisi, dan langkah berikutnya.')

@section('content')
    <section class="section-tight">
        <div class="site-container">
            @if (! $program)
                <div class="max-w-2xl">
                    <span class="eyebrow">Program</span>
                    <h1 class="mt-5 text-3xl font-semibold tracking-[-0.04em] md:text-4xl">
                        Belum ada <span class="hl">program aktif</span>.
                    </h1>
                    <p class="mt-3 text-sm leading-relaxed text-muted">
                        Hitung rekomendasi dulu, lalu simpan hasilnya sebagai program. Program yang
                        tersimpan berisi level, perkiraan nutrisi, dan jadwal 4 minggu.
                    </p>
                    <x-button :href="route('rekomendasi.create')" variant="accent" size="lg" icon="sparkle" class="mt-6">
                        Mulai rekomendasi
                    </x-button>
                </div>
            @else
                @php
                    $goal = config("programs.goals.{$program->goal}");
                    $levelLabel = __('level.'.$program->level);
                    $calorieText = number_format($nutrition['calories_min'], 0, ',', '.').'–'.number_format($nutrition['calories_max'], 0, ',', '.').' kkal';
                @endphp

                <div class="flex flex-wrap items-end justify-between gap-5">
                    <div class="max-w-2xl">
                        <span class="eyebrow">Program aktif</span>
                        <h1 class="mt-5 text-3xl font-semibold tracking-[-0.04em] md:text-4xl">
                            {{ $goal['label'] ?? ucfirst($program->goal) }} · <span class="hl">{{ $levelLabel }}</span>
                        </h1>
                        <p class="mt-3 text-sm leading-relaxed text-muted">
                            Minggu ke-{{ $currentWeek }} dari 4. Mulai {{ $program->mulai_pada->translatedFormat('j F Y') }}.
                            Sesi yang kamu tandai selesai di halaman Jadwal otomatis masuk ke Progres.
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <x-button :href="route('jadwal.index')" variant="accent" icon="calendar">Lihat jadwal</x-button>
                        <x-button :href="route('progres.index')" variant="secondary" icon="chart">Lihat progres</x-button>
                    </div>
                </div>

                <div class="mt-8 grid gap-6 lg:grid-cols-[1.1fr_0.9fr] lg:items-start">
                    <div class="space-y-6">
                        <x-card>
                            <div class="flex items-center gap-3">
                                <x-badge-icon name="flame" color="orange" />
                                <div>
                                    <h2 class="text-base font-semibold">Perkiraan kebutuhan harian</h2>
                                    <p class="mt-1 text-[11px] text-muted">Dihitung dari profil, tujuan, dan frekuensi latihan.</p>
                                </div>
                            </div>

                            <div class="mt-6 grid gap-3 sm:grid-cols-2">
                                <div class="detail-cell">
                                    <small>Kalori</small>
                                    <strong>{{ $calorieText }}</strong>
                                    <span class="mt-1 block text-[11px] text-muted">Target utama {{ number_format($nutrition['calories'], 0, ',', '.') }} kkal</span>
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
                                    <p>{{ number_format($nutrition['tdee'], 0, ',', '.') }} kkal</p>
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

                            <p class="mt-5 text-[11px] leading-relaxed text-muted">{{ $nutrition['disclaimer'] }} Angka ini perkiraan, bukan angka pasti.</p>
                        </x-card>

                        <x-card>
                            <div class="flex items-center gap-3">
                                <x-badge-icon name="layers" color="purple" />
                                <div>
                                    <h2 class="text-base font-semibold">Ringkasan program</h2>
                                    <p class="mt-1 text-[11px] text-muted">{{ $program->split ?: 'Split mengikuti rekomendasi' }}</p>
                                </div>
                            </div>

                            <div class="mt-5 grid gap-3 sm:grid-cols-2">
                                <div class="detail-cell">
                                    <small>Frekuensi</small>
                                    <strong>{{ $program->sesi_per_minggu }} sesi / minggu</strong>
                                </div>
                                <div class="detail-cell">
                                    <small>Hari latihan</small>
                                    <strong>{{ implode(', ', $program->schedule_days ?? []) ?: 'Mengikuti jadwal default' }}</strong>
                                </div>
                                <div class="detail-cell">
                                    <small>Pengalaman</small>
                                    <strong>{{ $program->pengalaman_bulan }} bulan</strong>
                                </div>
                                <div class="detail-cell">
                                    <small>Berat awal</small>
                                    <strong>{{ number_format((float) $program->berat_kg, 1, ',', '.') }} kg</strong>
                                </div>
                            </div>
                        </x-card>
                    </div>

                    <aside class="space-y-4 lg:sticky lg:top-28">
                        <x-card>
                            <h2 class="text-sm font-semibold">Langkah berikutnya</h2>
                            <ul class="check-list mt-4">
                                <li><x-icon name="check" class="h-3.5 w-3.5" /> Buka Jadwal dan tandai sesi yang sudah selesai</li>
                                <li><x-icon name="check" class="h-3.5 w-3.5" /> Isi check-in mingguan di halaman Progres</li>
                                <li><x-icon name="check" class="h-3.5 w-3.5" /> Bandingkan berat dan lingkar pinggang, bukan hanya timbangan</li>
                                <li><x-icon name="check" class="h-3.5 w-3.5" /> Evaluasi ulang setelah 4 minggu</li>
                            </ul>
                        </x-card>

                        @if ($scheduleNotes !== [])
                            <x-card>
                                <h2 class="text-sm font-semibold">Catatan jadwal</h2>
                                <ul class="check-list mt-4">
                                    @foreach ($scheduleNotes as $note)
                                        <li><x-icon name="info" class="h-3.5 w-3.5" /> {{ $note }}</li>
                                    @endforeach
                                </ul>
                            </x-card>
                        @endif

                        <div class="disclaimer">
                            <x-icon name="shield" class="mt-0.5 h-4 w-4 flex-none" />
                            <div>
                                <strong class="block font-semibold text-[#f2dfc2]">Bukan saran medis.</strong>
                                Konsultasikan dengan dokter atau pelatih bila kamu punya kondisi kesehatan,
                                cedera, atau sedang hamil.
                            </div>
                        </div>

                        <x-card>
                            <h2 class="text-sm font-semibold">Privasi</h2>
                            <p class="mt-2 text-[12px] leading-relaxed text-muted">
                                Data program dan check-in disimpan di akunmu. Kamu bisa mengekspor atau
                                menghapusnya dari halaman Akun.
                            </p>
                            <a href="{{ route('akun.index') }}" class="text-link mt-3 inline-flex">
                                Kelola akun
                                <x-icon name="arrow-right" class="h-3.5 w-3.5" />
                            </a>
                        </x-card>
                    </aside>
                </div>
            @endif
        </div>
    </section>
@endsection
