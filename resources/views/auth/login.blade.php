@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
<div>

    {{-- HEADING --}}
    <div class="mb-8">
        <div class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.16em] text-slate-500">
            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
            Ruang kerja inventaris
        </div>

        <h1 class="mt-4 text-[28px] font-semibold leading-tight tracking-[-0.03em] text-slate-950">
            Selamat datang kembali
        </h1>

        <p class="mt-2 text-sm leading-6 text-slate-500">
            Masuk untuk memantau stok, mencatat transaksi, dan mengelola produk gudang.
        </p>
    </div>

    <form action="{{ route('login.post') }}" method="POST">
        @csrf

        <x-input
            name="email"
            type="email"
            label="Email"
            placeholder="nama@email.com"
            autocomplete="email"
            autofocus
            required
        />

        <x-input
            name="password"
            type="password"
            label="Password"
            placeholder="Masukkan password"
            autocomplete="current-password"
            toggle
            required
        />

        <button type="submit" data-loading-text="Memproses..." class="btn btn-primary mt-2 h-11 w-full">
            Masuk ke Dashboard
            <x-icon name="arrow-right" class="h-4 w-4" stroke="2" />
        </button>
    </form>

    <div class="my-7 flex items-center gap-3">
        <span class="h-px flex-1 bg-slate-100"></span>
        <span class="text-[10px] font-medium uppercase tracking-[0.14em] text-slate-400">Belum punya akun</span>
        <span class="h-px flex-1 bg-slate-100"></span>
    </div>

    <a href="{{ route('register') }}" class="btn btn-secondary h-11 w-full">
        <x-icon name="user" class="h-4 w-4" />
        Daftar sebagai staff
    </a>

    <div class="mt-8 flex items-start gap-2.5 rounded-xl border border-slate-200/80 bg-slate-50/70 px-4 py-3.5">
        <x-icon name="lock" class="mt-0.5 h-4 w-4 shrink-0 text-slate-400" />
        <p class="text-[11px] leading-5 text-slate-500">
            Akses aman dengan proteksi CSRF dan pembatasan percobaan login. Hubungi administrator bila akun Anda belum memiliki akses yang sesuai.
        </p>
    </div>
</div>
@endsection
