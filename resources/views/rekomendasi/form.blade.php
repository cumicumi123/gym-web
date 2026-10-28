@extends('layouts.app')

@section('title', 'Rekomendasi Workout — GymSense')
@section('description', 'Isi profil tubuh, pengalaman gym, dan tujuan latihan untuk mendapatkan rekomendasi level serta program workout.')

@section('content')
    <section class="pt-16 pb-8">
        <div class="site-container">
            <div class="max-w-2xl">
                <span class="eyebrow">Formulir profil</span>
                <h1 class="mt-5 text-4xl font-semibold tracking-[-0.04em] md:text-5xl">
                    Mulai dari <span class="hl">dirimu sendiri</span>.
                </h1>
                <p class="mt-4 max-w-xl text-[15px] leading-relaxed text-muted">
                    Lima input utama sudah cukup. Bagian data latihan tambahan opsional bisa kamu isi
                    jika punya data dari smartwatch atau timbangan tubuh untuk hasil yang lebih presisi.
                </p>
            </div>
        </div>
    </section>

    <section class="pb-20">
        <div class="site-container grid gap-6 lg:grid-cols-[1.35fr_0.65fr] lg:items-stretch">
            <x-card class="reveal is-visible h-full flex flex-col">
                @if (session('ml_error'))
                    <div class="alert alert-danger mb-6" role="alert">
                        <x-icon name="alert" class="mt-0.5 h-4 w-4 flex-none" />
                        <div>
                            <strong class="block font-semibold">Layanan rekomendasi belum bisa dihubungi.</strong>
                            <span>{{ session('ml_error') }}</span>
                        </div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger mb-6" role="alert">
                        <x-icon name="alert" class="mt-0.5 h-4 w-4 flex-none" />
                        <div>
                            <strong class="block font-semibold">Periksa kembali isianmu.</strong>
                            <ul class="mt-1 list-inside list-disc space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('rekomendasi.store') }}" data-predict-form class="form-stack flex-1 content-between">
                    @csrf

                    <div>
                        <div class="mb-5 flex items-center justify-between gap-4">
                            <div>
                                <h2 class="text-lg font-semibold tracking-tight">Profil utama</h2>
                                <p class="mt-1 text-xs text-muted">Data dasar untuk menghitung BMI dan menyusun program.</p>
                            </div>
                            <span class="pill"><x-icon name="lock" class="h-3 w-3" /> Simpan opsional</span>
                        </div>

                        <div class="form-grid">
                            <div>
                                <label for="input-usia" class="field-label">
                                    Usia
                                    <span class="field-hint">14–80 tahun</span>
                                </label>
                                <div class="field-wrap">
                                    <input
                                        id="input-usia"
                                        type="number"
                                        name="usia"
                                        min="14"
                                        max="80"
                                        step="1"
                                        inputmode="numeric"
                                        class="@class(['field', 'is-invalid' => $errors->has('usia')])"
                                        value="{{ old('usia') }}"
                                        placeholder="28"
                                        required
                                    >
                                    <span class="field-unit">tahun</span>
                                </div>
                                @error('usia')<p class="field-error">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="input-berat" class="field-label">
                                    Berat badan
                                    <span class="field-hint">30–200 kg</span>
                                </label>
                                <div class="field-wrap">
                                    <input
                                        id="input-berat"
                                        type="number"
                                        name="berat"
                                        min="30"
                                        max="200"
                                        step="0.1"
                                        inputmode="decimal"
                                        class="@class(['field', 'is-invalid' => $errors->has('berat')])"
                                        value="{{ old('berat') }}"
                                        placeholder="72"
                                        required
                                    >
                                    <span class="field-unit">kg</span>
                                </div>
                                @error('berat')<p class="field-error">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="input-tinggi" class="field-label">
                                    Tinggi badan
                                    <span class="field-hint">120–220 cm</span>
                                </label>
                                <div class="field-wrap">
                                    <input
                                        id="input-tinggi"
                                        type="number"
                                        name="tinggi"
                                        min="120"
                                        max="220"
                                        step="0.1"
                                        inputmode="decimal"
                                        class="@class(['field', 'is-invalid' => $errors->has('tinggi')])"
                                        value="{{ old('tinggi') }}"
                                        placeholder="175"
                                        required
                                    >
                                    <span class="field-unit">cm</span>
                                </div>
                                @error('tinggi')<p class="field-error">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="input-pengalaman" class="field-label">
                                    Pengalaman gym
                                    <span class="field-hint">0–600 bulan</span>
                                </label>
                                <div class="field-wrap">
                                    <input
                                        id="input-pengalaman"
                                        type="number"
                                        name="pengalaman_bulan"
                                        min="0"
                                        max="600"
                                        step="1"
                                        inputmode="numeric"
                                        class="@class(['field', 'is-invalid' => $errors->has('pengalaman_bulan')])"
                                        value="{{ old('pengalaman_bulan') }}"
                                        placeholder="18"
                                        required
                                    >
                                    <span class="field-unit">bulan</span>
                                </div>
                                @error('pengalaman_bulan')<p class="field-error">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>

                    <x-segmented
                        name="tujuan"
                        legend="Tujuan utama"
                        :selected="'cutting'"
                        :options="[
                            'cutting' => ['label' => 'Cutting', 'hint' => 'Turunkan lemak', 'icon' => 'flame'],
                            'bulking' => ['label' => 'Bulking', 'hint' => 'Bangun massa', 'icon' => 'trending'],
                            'strength' => ['label' => 'Strength', 'hint' => 'Naikkan beban', 'icon' => 'bolt'],
                        ]"
                    />

                    <details class="advanced-panel" @if ($errors->hasAny(['gender', 'workout_type', 'max_bpm', 'avg_bpm', 'resting_bpm', 'session_duration', 'calories_burned', 'fat_percentage', 'water_intake', 'workout_frequency'])) open @endif>
                        <summary>
                            <span class="flex items-center gap-2">
                                <x-icon name="activity" class="h-4 w-4 text-accent-soft" />
                                Data latihan tambahan <span class="text-muted">(opsional)</span>
                            </span>
                        </summary>

                        <div class="advanced-body">
                            <p class="pt-4 text-xs leading-relaxed text-muted">
                                Kosongkan untuk perkiraan otomatis dari usia, berat, BMI, tujuan, dan
                                pengalaman. Isi jika kamu punya data pengukuran agar saran program
                                lebih pas dengan kondisi latihanmu.
                            </p>

                            <div class="form-grid">
                                <div class="md:col-span-2">
                                    <x-pill-group
                                        name="gender"
                                        legend="Gender"
                                        :selected="''"
                                        :options="[
                                            '' => ['label' => 'Otomatis (Male)', 'icon' => 'refresh'],
                                            'Male' => ['label' => 'Male', 'icon' => 'user'],
                                            'Female' => ['label' => 'Female', 'icon' => 'user'],
                                        ]"
                                    />
                                </div>

                                <div class="md:col-span-2">
                                    <x-pill-group
                                        name="workout_type"
                                        legend="Tipe latihan"
                                        :selected="''"
                                        :options="[
                                            '' => ['label' => 'Otomatis dari tujuan', 'icon' => 'refresh'],
                                            'Strength' => ['label' => 'Strength', 'icon' => 'bolt'],
                                            'Cardio' => ['label' => 'Cardio', 'icon' => 'heart'],
                                            'Yoga' => ['label' => 'Yoga', 'icon' => 'moon'],
                                            'HIIT' => ['label' => 'HIIT', 'icon' => 'flame'],
                                        ]"
                                    />
                                </div>

                                <div>
                                    <label for="input-max-bpm" class="field-label">Max BPM</label>
                                    <div class="field-wrap">
                                        <input id="input-max-bpm" type="number" name="max_bpm" min="100" max="230" step="1" class="@class(['field', 'is-invalid' => $errors->has('max_bpm')])" value="{{ old('max_bpm') }}" placeholder="192">
                                        <span class="field-unit">bpm</span>
                                    </div>
                                    @error('max_bpm')<p class="field-error">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label for="input-avg-bpm" class="field-label">Avg BPM</label>
                                    <div class="field-wrap">
                                        <input id="input-avg-bpm" type="number" name="avg_bpm" min="80" max="210" step="1" class="@class(['field', 'is-invalid' => $errors->has('avg_bpm')])" value="{{ old('avg_bpm') }}" placeholder="154">
                                        <span class="field-unit">bpm</span>
                                    </div>
                                    @error('avg_bpm')<p class="field-error">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label for="input-resting-bpm" class="field-label">Resting BPM</label>
                                    <div class="field-wrap">
                                        <input id="input-resting-bpm" type="number" name="resting_bpm" min="35" max="120" step="1" class="@class(['field', 'is-invalid' => $errors->has('resting_bpm')])" value="{{ old('resting_bpm') }}" placeholder="61">
                                        <span class="field-unit">bpm</span>
                                    </div>
                                    @error('resting_bpm')<p class="field-error">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label for="input-session" class="field-label">Durasi sesi</label>
                                    <div class="field-wrap">
                                        <input id="input-session" type="number" name="session_duration" min="0.25" max="4" step="0.25" class="@class(['field', 'is-invalid' => $errors->has('session_duration')])" value="{{ old('session_duration') }}" placeholder="1.25">
                                        <span class="field-unit">jam</span>
                                    </div>
                                    @error('session_duration')<p class="field-error">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label for="input-calories" class="field-label">Kalori terbakar</label>
                                    <div class="field-wrap">
                                        <input id="input-calories" type="number" name="calories_burned" min="100" max="3000" step="1" class="@class(['field', 'is-invalid' => $errors->has('calories_burned')])" value="{{ old('calories_burned') }}" placeholder="832">
                                        <span class="field-unit">kkal</span>
                                    </div>
                                    @error('calories_burned')<p class="field-error">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label for="input-fat" class="field-label">Persentase lemak</label>
                                    <div class="field-wrap">
                                        <input id="input-fat" type="number" name="fat_percentage" min="3" max="60" step="0.1" class="@class(['field', 'is-invalid' => $errors->has('fat_percentage')])" value="{{ old('fat_percentage') }}" placeholder="18.5">
                                        <span class="field-unit">%</span>
                                    </div>
                                    @error('fat_percentage')<p class="field-error">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label for="input-water" class="field-label">Asupan air</label>
                                    <div class="field-wrap">
                                        <input id="input-water" type="number" name="water_intake" min="0.5" max="6" step="0.1" class="@class(['field', 'is-invalid' => $errors->has('water_intake')])" value="{{ old('water_intake') }}" placeholder="3.0">
                                        <span class="field-unit">liter</span>
                                    </div>
                                    @error('water_intake')<p class="field-error">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label for="input-frequency" class="field-label">Frekuensi latihan</label>
                                    <div class="field-wrap">
                                        <input id="input-frequency" type="number" name="workout_frequency" min="0" max="7" step="1" class="@class(['field', 'is-invalid' => $errors->has('workout_frequency')])" value="{{ old('workout_frequency') }}" placeholder="4">
                                        <span class="field-unit">hari/minggu</span>
                                    </div>
                                    @error('workout_frequency')<p class="field-error">{{ $message }}</p>@enderror
                                </div>
                            </div>
                        </div>
                    </details>

                    <div class="flex flex-col gap-3 border-t border-white/5 pt-5 sm:flex-row sm:items-center sm:justify-between">
                        <p class="max-w-sm text-[11px] leading-relaxed text-muted">
                            Rekomendasi bersifat umum untuk edukasi, bukan saran medis.
                            Data profil hanya disimpan jika kamu memilih menyimpan program setelah masuk.
                        </p>
                        <x-button type="submit" variant="accent" size="lg" icon="sparkle" data-submit-button class="w-full sm:w-auto">
                            Hitung rekomendasi
                        </x-button>
                    </div>
                </form>
            </x-card>

            <aside class="space-y-4 lg:sticky lg:top-28">
                <x-card class="reveal is-visible">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <div class="text-[10px] font-medium uppercase tracking-[0.14em] text-muted">Indeks massa tubuh</div>
                            <div class="bmi-head mt-2">
                                <span class="bmi-value" id="bmi-value">—</span>
                                <span class="text-xs text-muted" id="bmi-category">Belum dihitung</span>
                            </div>
                        </div>
                        <x-badge-icon name="scale" color="teal" />
                    </div>
                    <div class="bmi-track" aria-hidden="true">
                        <div class="bmi-fill" id="bmi-fill"></div>
                    </div>
                    <div class="bmi-scale">
                        <span>Kurang</span>
                        <span>Normal</span>
                        <span>Berlebih</span>
                        <span>Obesitas</span>
                    </div>
                    <p class="mt-4 text-[11px] leading-relaxed text-muted">
                        BMI hanya salah satu pertimbangan. Usia, pengalaman, dan data latihan
                        latihan lain juga ikut dipakai, bukan BMI saja.
                    </p>
                </x-card>

                <x-card class="reveal is-visible" style="transition-delay: 60ms;">
                    <div class="flex items-center gap-3">
                        <x-badge-icon name="sparkle" color="purple" />
                        <h2 class="text-sm font-semibold">Yang kamu dapat</h2>
                    </div>
                    <ul class="check-list mt-5">
                        <li><x-icon name="check" class="h-3.5 w-3.5" /> Level dan kecocokan program</li>
                        <li><x-icon name="check" class="h-3.5 w-3.5" /> Frekuensi, durasi, dan split program</li>
                        <li><x-icon name="check" class="h-3.5 w-3.5" /> Fokus latihan, kardio, dan catatan gizi</li>
                        <li><x-icon name="check" class="h-3.5 w-3.5" /> Tanda data yang diperkirakan</li>
                    </ul>
                </x-card>

                <x-card class="reveal is-visible" style="transition-delay: 120ms;">
                    <div class="flex items-center gap-3">
                        <x-badge-icon name="info" color="orange" />
                        <h2 class="text-sm font-semibold">Tips mengisi profil</h2>
                    </div>
                    <ul class="check-list mt-5">
                        <li><x-icon name="check" class="h-3.5 w-3.5" /> Isi berat dan tinggi dengan satuan yang benar</li>
                        <li><x-icon name="check" class="h-3.5 w-3.5" /> Gunakan pengalaman gym dalam bulan, bukan tahun</li>
                        <li><x-icon name="check" class="h-3.5 w-3.5" /> Isi BPM atau durasi sesi jika punya smartwatch</li>
                    </ul>
                </x-card>
            </aside>
        </div>
    </section>
@endsection
