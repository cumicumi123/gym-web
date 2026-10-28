@extends('layouts.app')

@section('title', 'Masuk — GymSense')
@section('description', 'Masuk untuk menyimpan program latihan dan memantau progresmu.')

@section('content')
    <section class="section-tight">
        <div class="site-container max-w-md">
            <span class="eyebrow">Akun</span>
            <h1 class="mt-5 text-3xl font-semibold tracking-[-0.04em] md:text-4xl">
                Masuk untuk <span class="hl">menyimpan progres</span>.
            </h1>
            <p class="mt-3 text-sm leading-relaxed text-muted">
                Program, jadwal, dan check-in mingguan tersimpan di akunmu. Rekomendasi tetap bisa
                dihitung tanpa akun.
            </p>

            <x-card class="mt-7">
                @if ($errors->any())
                    <div class="alert alert-danger mb-5" role="alert">
                        <x-icon name="alert" class="mt-0.5 h-4 w-4 flex-none" />
                        <ul class="list-inside list-disc space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.store') }}" class="form-stack">
                    @csrf

                    <div>
                        <label for="login-email" class="field-label">Email</label>
                        <input id="login-email" type="email" name="email" class="field" value="{{ old('email') }}" autocomplete="email" required autofocus>
                        @error('email')<p class="field-error">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="login-password" class="field-label">Kata sandi</label>
                        <input id="login-password" type="password" name="password" class="field" autocomplete="current-password" required>
                        @error('password')<p class="field-error">{{ $message }}</p>@enderror
                    </div>

                    <label class="choice">
                        <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                        <span>Ingat saya di perangkat ini</span>
                    </label>

                    <x-button type="submit" variant="accent" size="lg" icon="arrow-right" class="w-full">
                        Masuk
                    </x-button>
                </form>

                <p class="mt-5 text-center text-xs text-muted">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="text-accent-soft underline-offset-4 hover:underline">Daftar gratis</a>
                </p>
            </x-card>
        </div>
    </section>
@endsection
