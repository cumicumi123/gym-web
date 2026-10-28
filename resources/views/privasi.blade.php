@extends('layouts.app')

@section('title', 'Privasi — GymSense')
@section('description', 'Apa yang disimpan GymSense, kenapa disimpan, dan cara mengekspor atau menghapusnya.')

@section('content')
    <section class="section-tight">
        <div class="site-container max-w-3xl">
            <span class="eyebrow">Privasi</span>
            <h1 class="mt-5 text-3xl font-semibold tracking-[-0.04em] md:text-4xl">
                Sesedikit mungkin, <span class="hl">sejelas mungkin</span>.
            </h1>
            <p class="mt-3 text-sm leading-relaxed text-muted">
                GymSense hanya menyimpan data yang kamu pilih untuk disimpan. Tanpa akun, hasil
                rekomendasi diproses untuk satu permintaan dan tidak disimpan sebagai program.
            </p>

            <div class="mt-8 grid gap-5">
                <x-card>
                    <div class="flex items-start gap-3">
                        <x-badge-icon name="lock" color="teal" />
                        <div>
                            <h2 class="text-base font-semibold">Kalau kamu tidak masuk</h2>
                            <p class="mt-2 text-[12.5px] leading-relaxed text-muted">
                                Kamu tetap bisa mengisi profil dan melihat rekomendasi. Data isian dipakai
                                untuk menghitung hasil saat itu juga dan tidak membuat program di akun.
                            </p>
                        </div>
                    </div>
                </x-card>

                <x-card>
                    <div class="flex items-start gap-3">
                        <x-badge-icon name="database" color="purple" />
                        <div>
                            <h2 class="text-base font-semibold">Kalau kamu menyimpan program</h2>
                            <p class="mt-2 text-[12.5px] leading-relaxed text-muted">
                                Kami menyimpan profil program (usia, berat, tinggi, pengalaman, gender,
                                tujuan, level), perkiraan nutrisi, jadwal 4 minggu, status sesi selesai,
                                dan check-in mingguan. Persetujuan diminta sebelum program disimpan.
                            </p>
                            <p class="mt-2 text-[12.5px] leading-relaxed text-muted">
                                Tidak ada foto, data pembayaran, atau data medis sensitif di versi ini.
                            </p>
                        </div>
                    </div>
                </x-card>

                <x-card>
                    <div class="flex items-start gap-3">
                        <x-badge-icon name="refresh" color="orange" />
                        <div>
                            <h2 class="text-base font-semibold">Ekspor dan hapus</h2>
                            <p class="mt-2 text-[12.5px] leading-relaxed text-muted">
                                Dari halaman Akun, kamu bisa mengunduh seluruh data program dan check-in
                                dalam format JSON. Kamu juga bisa menghapus akun beserta seluruh datanya.
                                Penghapusan bersifat permanen.
                            </p>
                            <div class="mt-4 flex flex-wrap gap-3">
                                @auth
                                    <x-button :href="route('akun.index')" variant="secondary" icon="user">Buka halaman Akun</x-button>
                                @else
                                    <x-button :href="route('login')" variant="secondary" icon="user">Masuk ke akun</x-button>
                                @endauth
                            </div>
                        </div>
                    </div>
                </x-card>

                <div class="disclaimer">
                    <x-icon name="shield" class="mt-0.5 h-4 w-4 flex-none" />
                    <div>
                        <strong class="block font-semibold text-[#f2dfc2]">Bukan saran medis.</strong>
                        GymSense memberi panduan umum untuk edukasi. Untuk kondisi kesehatan, cedera,
                        atau kehamilan, konsultasikan dengan dokter atau pelatih.
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
