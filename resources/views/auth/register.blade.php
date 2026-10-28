@extends('layouts.app')

@section('title', 'Daftar — GymSense')
@section('description', 'Buat akun GymSense untuk menyimpan program, jadwal, dan progres latihan.')

@section('content')
    <section class="section-tight">
        <div class="site-container max-w-md">
            <span class="eyebrow">Akun baru</span>
            <h1 class="mt-5 text-3xl font-semibold tracking-[-0.04em] md:text-4xl">
                Simpan program, <span class="hl">lihat progres</span>.
            </h1>
            <p class="mt-3 text-sm leading-relaxed text-muted">
                Akun dipakai untuk menyimpan program dan check-in mingguan. Data bisa diekspor atau
                dihapus kapan saja dari halaman Akun.
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

                <form method="POST" action="{{ route('register.store') }}" class="form-stack">
                    @csrf

                    <div>
                        <label for="register-name" class="field-label">Nama</label>
                        <input id="register-name" type="text" name="name" class="field" value="{{ old('name') }}" autocomplete="name" required autofocus>
                        @error('name')<p class="field-error">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="register-email" class="field-label">Email</label>
                        <input id="register-email" type="email" name="email" class="field" value="{{ old('email') }}" autocomplete="email" required>
                        @error('email')<p class="field-error">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="register-password" class="field-label">
                            Kata sandi
                            <span class="field-hint">Minimal 8 karakter</span>
                        </label>
                        <input id="register-password" type="password" name="password" class="field" autocomplete="new-password" required>
                        @error('password')<p class="field-error">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="register-password-confirmation" class="field-label">Ulangi kata sandi</label>
                        <input id="register-password-confirmation" type="password" name="password_confirmation" class="field" autocomplete="new-password" required>
                    </div>

                    <x-button type="submit" variant="accent" size="lg" icon="arrow-right" class="w-full">
                        Buat akun
                    </x-button>
                </form>

                <p class="mt-5 text-center text-xs text-muted">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="text-accent-soft underline-offset-4 hover:underline">Masuk</a>
                </p>
            </x-card>
        </div>
    </section>
@endsection
