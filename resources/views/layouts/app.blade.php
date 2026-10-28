<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0A0A0A">
    <meta name="description" content="@yield('description', 'GymSense membantu kamu memilih level dan program latihan yang sesuai berdasarkan profil tubuh, pengalaman gym, dan tujuan.')">
    <title>@yield('title', 'GymSense — Level & Program Latihan')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @php
        $globalStars = [
            [6, 16, 0.4], [13, 68, 0.5], [22, 9, 0.35], [31, 42, 0.55],
            [39, 12, 0.3], [47, 57, 0.45], [54, 26, 0.4], [62, 8, 0.5],
            [69, 48, 0.35], [76, 21, 0.55], [83, 64, 0.3], [91, 14, 0.45],
            [8, 84, 0.4], [18, 55, 0.3], [27, 91, 0.5], [44, 81, 0.35],
            [58, 72, 0.45], [73, 88, 0.3], [88, 39, 0.5], [96, 74, 0.35],
            [3, 33, 0.35], [11, 44, 0.3], [17, 24, 0.5], [25, 60, 0.4],
            [29, 78, 0.3], [35, 27, 0.55], [41, 70, 0.35], [45, 15, 0.4],
            [50, 40, 0.5], [53, 88, 0.3], [57, 51, 0.45], [60, 33, 0.35],
            [64, 18, 0.5], [67, 62, 0.3], [71, 76, 0.45], [75, 12, 0.4],
            [79, 35, 0.5], [81, 82, 0.3], [86, 55, 0.4], [89, 8, 0.45],
            [93, 47, 0.3], [95, 91, 0.5], [98, 25, 0.4], [2, 72, 0.3],
        ];
    @endphp

    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[99] focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:text-[#0A0A0A]">
        Lewati ke konten
    </a>

    <div class="page-glow" aria-hidden="true"></div>

    <div class="stars" aria-hidden="true">
        @foreach ($globalStars as [$left, $top, $opacity])
            <span class="star" style="left: {{ $left }}%; top: {{ $top }}%; opacity: {{ $opacity }}; animation-delay: {{ $loop->index * 0.28 }}s;"></span>
        @endforeach
    </div>

    <header class="nav-shell">
        <div class="site-container">
            <nav class="nav-inner" aria-label="Navigasi utama">
                <a href="{{ route('home') }}" class="brand" aria-label="GymSense, beranda">
                    <span class="brand-mark">
                        <x-icon name="dumbbell" class="h-5 w-5" />
                    </span>
                    <span>GymSense</span>
                </a>

                <div class="nav-links">
                    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'is-active' : '' }}" @if (request()->routeIs('home')) aria-current="page" @endif>Beranda</a>
                    <a href="{{ route('rekomendasi.create') }}" class="{{ request()->routeIs('rekomendasi.*') ? 'is-active' : '' }}" @if (request()->routeIs('rekomendasi.*')) aria-current="page" @endif>Rekomendasi</a>
                    <a href="{{ route('home') }}#panduan-level">Panduan Latihan</a>
                    <a href="{{ route('tentang') }}" class="{{ request()->routeIs('tentang') ? 'is-active' : '' }}" @if (request()->routeIs('tentang')) aria-current="page" @endif>Tentang</a>
                </div>

                <div class="nav-actions">
                    @auth
                        <a href="{{ route('program.show') }}" class="nav-quiet-link">Program</a>
                        <x-button :href="route('progres.index')" variant="primary" size="sm" icon="chart">Progres</x-button>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-button type="submit" variant="ghost" size="sm">Keluar</x-button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="nav-quiet-link">Masuk</a>
                        <x-button :href="route('rekomendasi.create')" variant="primary" size="sm" icon="arrow-right">Coba sekarang</x-button>
                    @endauth
                    <button
                        type="button"
                        class="nav-toggle"
                        data-nav-toggle
                        aria-expanded="false"
                        aria-controls="mobile-menu"
                        aria-label="Buka menu navigasi"
                    >
                        <x-icon name="menu" class="h-5 w-5" />
                    </button>
                </div>
            </nav>

            <div class="mobile-menu" id="mobile-menu" data-mobile-menu>
                <a href="{{ route('home') }}">Beranda</a>
                <a href="{{ route('rekomendasi.create') }}">Rekomendasi</a>
                <a href="{{ route('home') }}#panduan-level">Panduan latihan</a>
                @auth
                    <a href="{{ route('program.show') }}">Program</a>
                    <a href="{{ route('jadwal.index') }}">Jadwal</a>
                    <a href="{{ route('progres.index') }}">Progres</a>
                    <a href="{{ route('akun.index') }}">Akun</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="mobile-menu-logout">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}">Masuk</a>
                    <a href="{{ route('register') }}">Daftar</a>
                @endauth
                <a href="{{ route('tentang') }}">Tentang</a>
                <a href="{{ route('privasi') }}">Privasi</a>
            </div>
        </div>
    </header>

    <main id="main">
        @if (session('status'))
            <div class="site-container mt-6">
                <div class="alert alert-accent" role="status">
                    <x-icon name="info" class="mt-0.5 h-4 w-4 flex-none" />
                    <div>{{ session('status') }}</div>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="footer">
        <div class="site-container">
            <div class="footer-grid">
                <div>
                    <a href="{{ route('home') }}" class="brand">
                        <span class="brand-mark">
                            <x-icon name="dumbbell" class="h-5 w-5" />
                        </span>
                        <span>GymSense</span>
                    </a>
                    <p class="mt-4 max-w-sm">
                        Rekomendasi level dan program latihan yang jelas, dibuat untuk
                        membantumu mulai dengan percaya diri. Untuk edukasi, bukan pengganti
                        pelatih atau dokter.
                    </p>
                </div>

                <div>
                    <h4>Jelajahi</h4>
                    <div class="footer-links">
                        <a href="{{ route('rekomendasi.create') }}">Mulai rekomendasi</a>
                        <a href="{{ route('home') }}#panduan-level">Panduan latihan</a>
                        <a href="{{ route('tentang') }}">Tentang GymSense</a>
                    </div>
                </div>

                <div>
                    <h4>Yang kamu dapat</h4>
                    <div class="footer-links">
                        <span>Level dan fokus program</span>
                        <span>Frekuensi dan contoh jadwal</span>
                        <span>Tips teknik dan pemulihan</span>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <span>&copy; {{ date('Y') }} GymSense. Panduan latihan untuk edukasi.</span>
                <span>Rekomendasi umum untuk edukasi — bukan saran medis.</span>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
