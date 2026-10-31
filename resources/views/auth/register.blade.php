@extends('layouts.app')

@section('title', 'Registrasi')

@section('content')
<div>

    {{-- HEADING --}}
    <div class="mb-8">
        <div class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.16em] text-slate-500">
            <span class="h-1.5 w-1.5 rounded-full bg-emerald-600"></span>
            Akun baru
        </div>

        <h1 class="mt-4 text-[28px] font-semibold leading-tight tracking-[-0.03em] text-slate-950">
            Mulai kelola inventaris
        </h1>

        <p class="mt-2 text-sm leading-6 text-slate-500">
            Buat akun untuk mencatat pergerakan stok dan memantau produk gudang.
        </p>
    </div>

    <div class="mb-6 flex items-start gap-2.5 rounded-xl border border-emerald-100 bg-emerald-50/70 px-4 py-3.5">
        <x-icon name="info" class="mt-0.5 h-4 w-4 shrink-0 text-emerald-700" />
        <p class="text-[11px] leading-5 text-emerald-900">
            Akun baru otomatis terdaftar sebagai <span class="font-semibold">Staff</span>. Hak akses Admin hanya diberikan oleh administrator.
        </p>
    </div>

    <form action="{{ route('register.post') }}" method="POST">
        @csrf

        <x-input
            name="name"
            label="Nama Lengkap"
            placeholder="Nama lengkap Anda"
            autocomplete="name"
            autofocus
            required
        />

        <x-input
            name="email"
            type="email"
            label="Email"
            placeholder="nama@email.com"
            autocomplete="email"
            required
        />

        <div class="grid gap-x-5 sm:grid-cols-2">
            <x-input
                name="password"
                type="password"
                label="Password"
                placeholder="Minimal 8 karakter"
                autocomplete="new-password"
                hint="Minimal 8 karakter."
                toggle
                required
            />

            <x-input
                name="password_confirmation"
                type="password"
                label="Konfirmasi"
                placeholder="Ulangi password"
                autocomplete="new-password"
                toggle
                required
            />
        </div>

        <button type="submit" data-loading-text="Mendaftarkan..." class="btn btn-primary mt-2 h-11 w-full">
            Daftar Sekarang
            <x-icon name="arrow-right" class="h-4 w-4" stroke="2" />
        </button>
    </form>

    <div class="my-7 flex items-center gap-3">
        <span class="h-px flex-1 bg-slate-100"></span>
        <span class="text-[10px] font-medium uppercase tracking-[0.14em] text-slate-400">Sudah punya akun</span>
        <span class="h-px flex-1 bg-slate-100"></span>
    </div>

    <a href="{{ route('login') }}" class="btn btn-secondary h-11 w-full">
        Masuk ke akun Anda
    </a>
</div>
@endsection
