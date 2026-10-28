@extends('layouts.app')

@section('title', 'GymSense — Level & Program Latihan')
@section('description', 'Isi profil singkat, lalu dapatkan level, fokus program, frekuensi, dan contoh jadwal latihan yang masuk akal untukmu.')

@section('content')
    <section class="hero">
        <div class="site-container hero-grid">
            <div class="reveal is-visible">
                <span class="eyebrow">Panduan latihan gym</span>
                <h1 class="hero-title">
                    Latihan yang tepat, dimulai dari level yang
                    <span class="hl">paling pas</span>.
                </h1>
                <p class="hero-copy">
                    Isi profil singkat, lalu dapatkan level, fokus program, dan frekuensi
                    latihan yang masuk akal untukmu. Rencananya bisa langsung dibawa ke gym.
                </p>

                <div class="hero-actions">
                    <x-button :href="route('rekomendasi.create')" variant="primary" size="lg" icon="arrow-right">
                        Dapatkan rekomendasi
                    </x-button>
                    <x-button :href="route('home').'#panduan-level'" variant="secondary" size="lg" icon="dumbbell">
                        Lihat panduan level
                    </x-button>
                </div>

                <div class="hero-meta">
                    <span><i></i> 3 level panduan</span>
                    <span><i></i> Program sesuai tujuan</span>
                    <span><i></i> Simpan program opsional</span>
                </div>
            </div>

            <div class="device-stage reveal is-visible">
                <div class="device-glow" aria-hidden="true"></div>
                <div class="device">
                    <div class="device-top">
                        <span>GymSense / {{ $profile ? 'Profilmu' : 'Contoh hasil' }}</span>
                        <div class="device-dots" aria-hidden="true">
                            <span></span><span></span><span></span>
                        </div>
                    </div>

                    <div class="device-body">
                        <div class="device-panel">
                            @if ($profile)
                                <div class="device-row">
                                    <span>Usia</span>
                                    <strong>{{ $profile->usia }} tahun</strong>
                                </div>
                                <div class="device-row">
                                    <span>Berat / tinggi</span>
                                    <strong>{{ rtrim(rtrim(number_format((float) $profile->berat_kg, 1, ',', '.'), '0'), ',') }} kg · {{ number_format((float) $profile->tinggi_cm, 0, ',', '.') }} cm</strong>
                                </div>
                                <div class="device-row">
                                    <span>Tujuan</span>
                                    <strong>{{ config('programs.goals.'.$profile->goal.'.label', ucfirst($profile->goal)) }}</strong>
                                </div>
                                <div class="device-row">
                                    <span>Pengalaman</span>
                                    <strong>{{ $profile->pengalaman_bulan }} bulan</strong>
                                </div>
                            @else
                                <div class="device-row">
                                    <span>Usia</span>
                                    <strong>28 tahun</strong>
                                </div>
                                <div class="device-row">
                                    <span>Berat / tinggi</span>
                                    <strong>72 kg · 175 cm</strong>
                                </div>
                                <div class="device-row">
                                    <span>Tujuan</span>
                                    <strong>Cutting</strong>
                                </div>
                                <div class="device-row">
                                    <span>Pengalaman</span>
                                    <strong>18 bulan</strong>
                                </div>
                            @endif
                        </div>

                        <div class="device-result">
                            <small>Level yang cocok</small>

                            @if ($profile)
                                <h4>{{ __('level.'.$profile->level) }}</h4>
                                <p>
                                    {{ $profile->split ?? config('programs.matrix.'.$profile->goal.'.'.$profile->level.'.split', 'Program sesuai level') }}
                                    · {{ $profile->sesi_per_minggu }} sesi/minggu
                                </p>

                                <div class="mt-3 flex flex-wrap gap-2">
                                    @foreach (['Beginner', 'Intermediate', 'Advanced'] as $class)
                                        <span class="pill {{ $profile->level === $class ? 'pill-accent' : '' }}">{{ __('level.'.$class) }}</span>
                                    @endforeach
                                </div>
                            @else
                                <h4>Menengah</h4>
                                <p>Upper/Lower · 4 sesi/minggu</p>

                                <div class="device-bars">
                                    <div class="device-bar">
                                        <span>Pemula</span>
                                        <div class="device-bar-track"><div class="device-bar-fill" style="width: 5%;"></div></div>
                                        <span>5%</span>
                                    </div>
                                    <div class="device-bar">
                                        <span>Menengah</span>
                                        <div class="device-bar-track"><div class="device-bar-fill" style="width: 51%;"></div></div>
                                        <span>51%</span>
                                    </div>
                                    <div class="device-bar">
                                        <span>Mahir</span>
                                        <div class="device-bar-track"><div class="device-bar-fill" style="width: 44%; opacity: .55;"></div></div>
                                        <span>44%</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="site-container reveal">
        <div class="stat-strip">
            <x-stat value="3" label="Level panduan" />
            <x-stat value="3" label="Tujuan program" />
            <x-stat value="4–6" label="Sesi per minggu" />
            <x-stat value="5" label="Input profil" />
        </div>
    </div>

    <section class="section-tight">
        <div class="site-container">
            <div class="mx-auto max-w-2xl text-center reveal">
                <span class="eyebrow">Cara kerja</span>
                <h2 class="section-title">Tiga langkah sebelum latihan pertama.</h2>
                <p class="section-copy">
                    Tidak perlu bingung memilih program. Mulai dari profilmu, lalu ikuti
                    rencana yang sudah disusun per level.
                </p>
            </div>

            <div class="step-line mt-12">
                <x-card class="step reveal" :padding="false">
                    <span class="step-number">01</span>
                    <h3>Isi profil</h3>
                    <p>Usia, berat, tinggi, pengalaman gym, dan tujuan latihan. Cukup lima input utama.</p>
                </x-card>
                <x-card class="step reveal" :padding="false" style="transition-delay: 80ms;">
                    <span class="step-number">02</span>
                    <h3>Dapat level dan program</h3>
                    <p>Kamu mendapat level, fokus program, frekuensi, durasi, dan catatan pemulihan.</p>
                </x-card>
                <x-card class="step reveal" :padding="false" style="transition-delay: 160ms;">
                    <span class="step-number">03</span>
                    <h3>Mulai latihan</h3>
                    <p>Bawa rencananya ke gym, catat beban, lalu evaluasi progres setiap empat minggu.</p>
                </x-card>
            </div>
        </div>
    </section>

    <section class="section" id="panduan-level">
        <div class="site-container">
            <div class="mx-auto max-w-2xl text-center reveal">
                <span class="eyebrow">Panduan level</span>
                <h2 class="section-title">Pemula, menengah, atau mahir.</h2>
                <p class="section-copy">
                    Setiap level punya titik berat berbeda. Pilih yang paling dekat dengan
                    kondisi dan pengalamanmu sekarang, bukan yang terdengar paling keren.
                </p>
            </div>

            <div class="feature-grid mt-12">
                <x-card class="feature-card card-hover reveal">
                    <div class="flex items-start justify-between gap-4">
                        <x-badge-icon name="user" color="green" />
                        <span class="pill">Pemula</span>
                    </div>
                    <div>
                        <h3>Bangun fondasi dan kebiasaan</h3>
                        <p>
                            Fokus pada teknik gerakan dasar dengan volume yang mudah dipulihkan.
                            Cocok untuk yang baru mulai atau kembali setelah lama berhenti.
                        </p>
                    </div>
                    <ul class="check-list">
                        <li><x-icon name="check" class="h-3.5 w-3.5" /> 2–3 sesi per minggu</li>
                        <li><x-icon name="check" class="h-3.5 w-3.5" /> Full body A/B</li>
                        <li><x-icon name="check" class="h-3.5 w-3.5" /> Intensitas ringan–sedang</li>
                    </ul>
                </x-card>

                <x-card class="feature-card card-hover reveal" style="transition-delay: 80ms;">
                    <div class="flex items-start justify-between gap-4">
                        <x-badge-icon name="activity" color="orange" />
                        <span class="pill pill-accent">Menengah</span>
                    </div>
                    <div>
                        <h3>Tambah volume dengan arah jelas</h3>
                        <p>
                            Sudah nyaman dengan gerakan utama dan siap menambah beban secara
                            bertahap. Program mulai dibagi per bagian tubuh.
                        </p>
                    </div>
                    <ul class="check-list">
                        <li><x-icon name="check" class="h-3.5 w-3.5" /> 3–4 sesi per minggu</li>
                        <li><x-icon name="check" class="h-3.5 w-3.5" /> Upper/Lower atau PPL</li>
                        <li><x-icon name="check" class="h-3.5 w-3.5" /> Intensitas sedang</li>
                    </ul>
                </x-card>

                <x-card class="feature-card card-hover reveal" style="transition-delay: 160ms;">
                    <div class="flex items-start justify-between gap-4">
                        <x-badge-icon name="bolt" color="purple" />
                        <span class="pill">Mahir</span>
                    </div>
                    <div>
                        <h3>Atur periode latihan dan pemulihan</h3>
                        <p>
                            Volume dan intensitas lebih tinggi, dengan deload terencana.
                            Teknik dan catatan latihan jadi kunci.
                        </p>
                    </div>
                    <ul class="check-list">
                        <li><x-icon name="check" class="h-3.5 w-3.5" /> 4–6 sesi per minggu</li>
                        <li><x-icon name="check" class="h-3.5 w-3.5" /> Push/Pull/Legs</li>
                        <li><x-icon name="check" class="h-3.5 w-3.5" /> Intensitas sedang–tinggi</li>
                    </ul>
                </x-card>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="site-container">
            <div class="mx-auto max-w-2xl text-center reveal">
                <span class="eyebrow">Contoh jadwal</span>
                <h2 class="section-title">Seminggu yang bisa kamu ikuti.</h2>
                <p class="section-copy">
                    Contoh untuk level menengah dengan tujuan cutting. Sesuaikan hari dan
                    pilihan gerakan dengan jadwal serta alat yang tersedia.
                </p>
            </div>

            <div class="mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <x-card class="reveal">
                    <span class="pill">Senin</span>
                    <h3 class="mt-4 text-sm font-semibold">Upper body</h3>
                    <p class="mt-2 text-xs leading-relaxed text-muted">Bench, row, overhead press, dan aksesori lengan.</p>
                </x-card>
                <x-card class="reveal" style="transition-delay: 60ms;">
                    <span class="pill">Selasa</span>
                    <h3 class="mt-4 text-sm font-semibold">Lower body</h3>
                    <p class="mt-2 text-xs leading-relaxed text-muted">Squat, hinge, leg press, dan core.</p>
                </x-card>
                <x-card class="reveal" style="transition-delay: 120ms;">
                    <span class="pill">Rabu</span>
                    <h3 class="mt-4 text-sm font-semibold">Pemulihan</h3>
                    <p class="mt-2 text-xs leading-relaxed text-muted">Jalan santai, mobilitas, dan tidur cukup.</p>
                </x-card>
                <x-card class="reveal" style="transition-delay: 180ms;">
                    <span class="pill">Kamis</span>
                    <h3 class="mt-4 text-sm font-semibold">Upper body</h3>
                    <p class="mt-2 text-xs leading-relaxed text-muted">Variasi gerakan dan fokus otot punggung.</p>
                </x-card>
                <x-card class="reveal" style="transition-delay: 60ms;">
                    <span class="pill">Jumat</span>
                    <h3 class="mt-4 text-sm font-semibold">Lower body</h3>
                    <p class="mt-2 text-xs leading-relaxed text-muted">Fokus kekuatan dan stabilitas.</p>
                </x-card>
                <x-card class="reveal" style="transition-delay: 120ms;">
                    <span class="pill">Sabtu</span>
                    <h3 class="mt-4 text-sm font-semibold">Kardio ringan</h3>
                    <p class="mt-2 text-xs leading-relaxed text-muted">30–40 menit sepeda atau jalan cepat.</p>
                </x-card>
                <x-card class="reveal" style="transition-delay: 180ms;">
                    <span class="pill">Minggu</span>
                    <h3 class="mt-4 text-sm font-semibold">Istirahat</h3>
                    <p class="mt-2 text-xs leading-relaxed text-muted">Evaluasi catatan latihan dan siapkan minggu berikutnya.</p>
                </x-card>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="site-container">
            <div class="mx-auto max-w-2xl text-center reveal">
                <span class="eyebrow">Kenapa GymSense</span>
                <h2 class="section-title">Rencana yang bisa langsung dijalankan.</h2>
                <p class="section-copy">
                    Bukan sekadar label level. Kamu mendapat arah latihan, porsi kardio,
                    dan langkah progresi yang realistis.
                </p>
            </div>

            <div class="feature-grid mt-12">
                <x-card class="feature-card card-hover reveal" style="transition-delay: 60ms;">
                    <x-badge-icon name="user" color="purple" />
                    <div>
                        <h3>Level yang menyesuaikan profil</h3>
                        <p>
                            Usia, berat, tinggi, pengalaman, dan tujuan dipakai untuk
                            menyarankan level yang paling dekat dengan kondisimu.
                        </p>
                    </div>
                    <a href="{{ route('rekomendasi.create') }}" class="text-link">
                        Mulai isi profil
                        <x-icon name="arrow-right" class="h-3.5 w-3.5" />
                    </a>
                </x-card>

                <x-card class="feature-card card-hover reveal" style="transition-delay: 120ms;">
                    <div class="flex items-start justify-between gap-4">
                        <x-badge-icon name="dumbbell" color="teal" />
                        <span class="pill pill-accent">Praktis</span>
                    </div>
                    <div>
                        <h3>Program yang bisa dijalankan</h3>
                        <p>
                            Dapatkan frekuensi, durasi, split, fokus latihan, catatan kardio,
                            dan cara menaikkan beban dengan aman.
                        </p>
                    </div>
                    <a href="{{ route('rekomendasi.create') }}" class="text-link">
                        Lihat contoh program
                        <x-icon name="arrow-right" class="h-3.5 w-3.5" />
                    </a>
                </x-card>

                <x-card class="feature-card card-hover reveal" style="transition-delay: 180ms;">
                    <x-badge-icon name="shield" color="orange" />
                    <div>
                        <h3>Tips teknik dan pemulihan</h3>
                        <p>
                            Setiap rencana menyertakan pengingat soal teknik, istirahat, tidur,
                            dan tanda-tanda yang perlu kamu waspadai.
                        </p>
                    </div>
                    <a href="{{ route('home') }}#faq" class="text-link">
                        Baca pertanyaan umum
                        <x-icon name="arrow-right" class="h-3.5 w-3.5" />
                    </a>
                </x-card>
            </div>
        </div>
    </section>

    <section class="section" id="faq">
        <div class="site-container grid gap-10 lg:grid-cols-[0.85fr_1.15fr] lg:items-start">
            <div class="reveal">
                <span class="eyebrow">FAQ</span>
                <h2 class="section-title">Pertanyaan yang sering muncul.</h2>
                <p class="section-copy mx-0!">
                    Belum ketemu jawabannya? Halaman Tentang GymSense menjelaskan cara kerja,
                    privasi, dan batasan rekomendasi dengan bahasa yang sederhana.
                </p>
                <x-button :href="route('tentang')" variant="ghost" icon="arrow-right" class="mt-6">
                    Tentang GymSense
                </x-button>
            </div>

            <div class="faq-list reveal">
                <x-faq-item
                    question="Apakah rekomendasi ini pengganti dokter atau pelatih?"
                    answer="Tidak. GymSense adalah alat edukasi yang memberi gambaran level dan program umum. Jika kamu punya kondisi jantung, cedera, kehamilan, atau riwayat penyakit lain, konsultasikan dulu dengan dokter atau pelatih bersertifikat."
                    :open="true"
                />
                <x-faq-item
                    question="Berapa kali sebaiknya latihan per minggu?"
                    answer="Pemula biasanya cukup 2–3 sesi per minggu. Menengah 3–4 sesi, dan mahir 4–6 sesi. Yang lebih penting adalah konsistensi dan pemulihan, bukan jumlah sesi sebanyak mungkin."
                />
                <x-faq-item
                    question="Bagaimana kalau alat di gym terbatas?"
                    answer="Gunakan versi gerakan yang tersedia, misalnya dumbbell sebagai pengganti barbell, atau mesin untuk gerakan utama. Jaga urutan pola gerak: dorong, tarik, squat, hinge, dan core."
                />
                <x-faq-item
                    question="Kapan sebaiknya menaikkan beban?"
                    answer="Saat semua set terasa terkontrol dan repetisi target tercapai dalam dua sesi berturut-turut. Naikkan sedikit demi sedikit, lalu pastikan teknik tetap rapi."
                />
                <x-faq-item
                    question="Apakah profil saya disimpan?"
                    answer="Tanpa akun, tidak. Profil hanya diproses untuk satu permintaan. Kalau kamu masuk dan memilih menyimpan program, data program dan check-in disimpan di akunmu dan bisa diekspor atau dihapus kapan saja."
                />
            </div>
        </div>
    </section>

    <section class="section-tight">
        <div class="site-container">
            <div class="card card-pad relative overflow-hidden reveal" style="border-color: rgba(99, 102, 241, .35); background: radial-gradient(ellipse at 20% 0%, rgba(99, 102, 241, .2), transparent 58%), var(--card);">
                <div class="relative z-10 flex flex-col items-start justify-between gap-8 md:flex-row md:items-center">
                    <div class="max-w-xl">
                        <span class="eyebrow">Mulai dari sini</span>
                        <h2 class="mt-4 text-3xl font-semibold tracking-tight md:text-4xl">
                            Siap menemukan titik mulai yang tepat?
                        </h2>
                        <p class="mt-3 text-sm leading-relaxed text-muted">
                            Butuh kurang dari satu menit. Isi profil, lihat level dan program
                            yang disarankan, lalu bawa rencananya ke gym.
                        </p>
                    </div>
                    <x-button :href="route('rekomendasi.create')" variant="accent" size="lg" icon="arrow-right">
                        Coba GymSense
                    </x-button>
                </div>
            </div>
        </div>
    </section>
@endsection
