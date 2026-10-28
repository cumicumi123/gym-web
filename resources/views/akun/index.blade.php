@extends('layouts.app')

@section('title', 'Akun — GymSense')
@section('description', 'Kelola akun, ekspor data, atau hapus akun beserta seluruh datanya.')

@section('content')
    <section class="section-tight">
        <div class="site-container max-w-3xl">
            <span class="eyebrow">Akun</span>
            <h1 class="mt-5 text-3xl font-semibold tracking-[-0.04em] md:text-4xl">
                Data milikmu, <span class="hl">kendali milikmu</span>.
            </h1>
            <p class="mt-3 text-sm leading-relaxed text-muted">
                Kamu bisa mengekspor data program dan check-in, atau menghapus akun beserta seluruh
                datanya kapan saja.
            </p>

            <div class="mt-8 grid gap-5">
                <x-card>
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <x-badge-icon name="user" color="purple" />
                            <div>
                                <h2 class="text-base font-semibold">{{ $user->name }}</h2>
                                <p class="mt-1 text-[12px] text-muted">{{ $user->email }}</p>
                            </div>
                        </div>
                        <span class="pill">{{ $user->programs_count }} program tersimpan</span>
                    </div>
                </x-card>

                <x-card>
                    <div class="flex items-start gap-3">
                        <x-badge-icon name="database" color="teal" />
                        <div>
                            <h2 class="text-base font-semibold">Data yang disimpan</h2>
                            <ul class="check-list mt-4">
                                <li><x-icon name="check" class="h-3.5 w-3.5" /> Profil program: usia, berat, tinggi, pengalaman, gender, tujuan, dan level</li>
                                <li><x-icon name="check" class="h-3.5 w-3.5" /> Perkiraan nutrisi dan jadwal 4 minggu</li>
                                <li><x-icon name="check" class="h-3.5 w-3.5" /> Status sesi selesai dan check-in mingguan</li>
                                <li><x-icon name="check" class="h-3.5 w-3.5" /> Tidak ada foto, data pembayaran, atau data medis sensitif</li>
                            </ul>
                        </div>
                    </div>

                    <div class="mt-5 flex flex-wrap gap-3">
                        <x-button :href="route('akun.export')" variant="secondary" icon="arrow-right">Ekspor data (JSON)</x-button>
                        <x-button :href="route('privasi')" variant="ghost" icon="shield">Baca kebijakan privasi</x-button>
                    </div>
                </x-card>

                <x-card>
                    <div class="flex items-start gap-3">
                        <x-badge-icon name="alert" color="orange" />
                        <div>
                            <h2 class="text-base font-semibold">Hapus akun</h2>
                            <p class="mt-2 text-[12.5px] leading-relaxed text-muted">
                                Semua program, jadwal, dan check-in akan ikut terhapus permanen. Tindakan
                                ini tidak bisa dibatalkan.
                            </p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('akun.destroy') }}" class="form-stack mt-5" onsubmit="return confirm('Hapus akun dan seluruh datanya? Tindakan ini tidak bisa dibatalkan.');">
                        @csrf
                        @method('DELETE')

                        <div>
                            <label for="hapus-password" class="field-label">Konfirmasi kata sandi</label>
                            <input id="hapus-password" type="password" name="password" class="field" autocomplete="current-password" required>
                            @error('password')<p class="field-error">{{ $message }}</p>@enderror
                        </div>

                        <x-button type="submit" variant="secondary" icon="alert" class="w-full sm:w-auto">
                            Hapus akun beserta datanya
                        </x-button>
                    </form>
                </x-card>
            </div>
        </div>
    </section>
@endsection
