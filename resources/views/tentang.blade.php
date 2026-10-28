@extends('layouts.app')

@section('title', 'Tentang GymSense — Panduan Latihan')
@section('description', 'GymSense membantu kamu memilih level dan program latihan yang masuk akal, dengan penjelasan sederhana soal cara kerja dan panduan level.')

@section('content')
    @php
        $levels = [
            [
                'key' => 'Beginner',
                'badge' => 'green',
                'frequency' => '2–3 sesi / minggu',
                'split' => 'Full body A/B',
                'intensity' => 'Ringan–sedang, fokus teknik',
                'description' => 'Prioritas kebiasaan rutin, teknik gerakan dasar, dan volume yang mudah dipulihkan.',
            ],
            [
                'key' => 'Intermediate',
                'badge' => 'orange',
                'frequency' => '3–4 sesi / minggu',
                'split' => 'Upper/Lower atau PPL',
                'intensity' => 'Sedang, volume bertahap',
                'description' => 'Sudah punya fondasi; siap menambah volume dan variasi gerakan dengan arah jelas.',
            ],
            [
                'key' => 'Advanced',
                'badge' => 'purple',
                'frequency' => '4–6 sesi / minggu',
                'split' => 'Push/Pull/Legs',
                'intensity' => 'Sedang–tinggi, periodisasi',
                'description' => 'Fokus pada pengaturan beban, pemulihan, dan deload yang terencana.',
            ],
        ];
    @endphp

    <section class="pt-16 pb-8">
        <div class="site-container">
            <div class="grid gap-10 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
                <div>
                    <span class="eyebrow">Tentang GymSense</span>
                    <h1 class="mt-5 text-4xl font-semibold tracking-[-0.04em] md:text-5xl">
                        Latihan yang masuk akal untuk <span class="hl">setiap tahap</span>.
                    </h1>
                    <p class="mt-4 max-w-xl text-[15px] leading-relaxed text-muted">
                        GymSense dibuat untuk membantu kamu memulai dengan arah yang jelas:
                        level yang sesuai, program yang bisa dijalankan, dan langkah kecil
                        untuk menjaga konsistensi.
                    </p>
                    <div class="mt-7 flex flex-wrap gap-3">
                        <x-button :href="route('rekomendasi.create')" variant="accent" icon="arrow-right">Coba rekomendasi</x-button>
                        <x-button :href="route('home').'#panduan-level'" variant="secondary" icon="dumbbell">Lihat panduan level</x-button>
                    </div>
                </div>

                <div class="dumbbell-card reveal is-visible">
                    <div
                        class="dumbbell-stage"
                        data-dumbbell-stage
                        tabindex="0"
                        role="button"
                        aria-label="Putar dumbbell. Geser untuk memutar, tekan untuk spin."
                    >
                        <div class="dumbbell-glow" aria-hidden="true"></div>
                        <div class="dumbbell-3d" data-dumbbell-rotator>
                            @for ($layer = 8; $layer >= 1; $layer--)
                                <div class="dumbbell-layer" style="transform: translateZ(-{{ $layer * 3 }}px);">
                                    <x-dumbbell gid="db-{{ $layer }}" />
                                </div>
                            @endfor
                            <div class="dumbbell-layer dumbbell-layer-top">
                                <x-dumbbell gid="db-top" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="site-container">
            <div class="mx-auto max-w-2xl text-center reveal">
                <span class="eyebrow">Cara kerja</span>
                <h2 class="section-title">Dari profil ke rencana latihan.</h2>
                <p class="section-copy">
                    Kamu cukup mengisi profil, lalu GymSense menyusun saran level dan program
                    yang bisa langsung kamu bawa ke gym.
                </p>
            </div>

            <div class="mt-12 grid gap-4 md:grid-cols-3">
                <x-card class="reveal">
                    <x-badge-icon name="user" color="purple" />
                    <h3 class="mt-4 text-sm font-semibold">1. Isi profil</h3>
                    <p class="mt-2 text-xs leading-relaxed text-muted">Usia, berat, tinggi, pengalaman gym, dan tujuan latihan. Data tambahan bersifat opsional.</p>
                </x-card>
                <x-card class="reveal" style="transition-delay: 60ms;">
                    <x-badge-icon name="activity" color="orange" />
                    <h3 class="mt-4 text-sm font-semibold">2. Dapat level dan program</h3>
                    <p class="mt-2 text-xs leading-relaxed text-muted">Kamu melihat level, fokus program, frekuensi, durasi, dan catatan pemulihan.</p>
                </x-card>
                <x-card class="reveal" style="transition-delay: 120ms;">
                    <x-badge-icon name="dumbbell" color="teal" />
                    <h3 class="mt-4 text-sm font-semibold">3. Mulai latihan</h3>
                    <p class="mt-2 text-xs leading-relaxed text-muted">Jalankan rencananya, catat beban, dan evaluasi progres setiap empat minggu.</p>
                </x-card>
            </div>
        </div>
    </section>

    <section class="section-tight" id="panduan-level">
        <div class="site-container">
            <div class="mx-auto max-w-2xl text-center reveal">
                <span class="eyebrow">Panduan level</span>
                <h2 class="section-title">Tiga tahap, tiga pendekatan.</h2>
                <p class="section-copy">
                    Gunakan level yang paling dekat dengan pengalamanmu. Tidak masalah memulai
                    dari bawah dan naik perlahan.
                </p>
            </div>

            <div class="mt-12 grid gap-4 lg:grid-cols-3">
                @foreach ($levels as $item)
                    <x-card class="reveal" style="transition-delay: {{ $loop->index * 60 }}ms;">
                        <div class="flex items-center justify-between gap-3">
                            <x-badge-icon name="dumbbell" :color="$item['badge']" />
                            <span class="pill">{{ __('level.'.$item['key']) }}</span>
                        </div>
                        <h3 class="mt-4 text-sm font-semibold">{{ $item['description'] }}</h3>
                        <div class="mt-5 space-y-2 text-[11.5px]">
                            <div class="flex items-center justify-between rounded-lg border border-white/5 bg-[#0A0A0A]/20 px-3 py-2">
                                <span class="text-muted">Frekuensi</span>
                                <span>{{ $item['frequency'] }}</span>
                            </div>
                            <div class="flex items-center justify-between rounded-lg border border-white/5 bg-[#0A0A0A]/20 px-3 py-2">
                                <span class="text-muted">Split</span>
                                <span>{{ $item['split'] }}</span>
                            </div>
                            <div class="flex items-center justify-between rounded-lg border border-white/5 bg-[#0A0A0A]/20 px-3 py-2">
                                <span class="text-muted">Intensitas</span>
                                <span>{{ $item['intensity'] }}</span>
                            </div>
                        </div>
                    </x-card>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section-tight">
        <div class="site-container">
            <div class="card card-pad flex flex-col items-start justify-between gap-6 md:flex-row md:items-center reveal" style="border-color: rgba(99, 102, 241, .35); background: radial-gradient(ellipse at 15% 0%, rgba(99, 102, 241, .18), transparent 58%), var(--card);">
                <div>
                    <span class="eyebrow">Mulai dari sini</span>
                    <h2 class="mt-3 text-2xl font-semibold tracking-tight">Temukan level dan programmu.</h2>
                    <p class="mt-2 max-w-xl text-sm text-muted">
                        Isi profil singkat, lihat rencananya, lalu mulai dari sesi pertama
                        dengan beban yang nyaman.
                    </p>
                </div>
                <x-button :href="route('rekomendasi.create')" variant="accent" size="lg" icon="sparkle">
                    Mulai sekarang
                </x-button>
            </div>
        </div>
    </section>
@endsection
