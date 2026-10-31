@extends('layouts.app')

@section('title', 'Selamat Datang')

@section('content')
<div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-[0_1px_2px_rgba(15,23,42,0.03),0_16px_40px_-30px_rgba(15,23,42,0.25)] sm:p-9">
    <div class="text-center">
        <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-700 text-white shadow-sm shadow-emerald-900/15">
            <x-icon name="warehouse" class="h-6 w-6" />
        </span>

        <h1 class="mt-5 text-2xl font-semibold tracking-tight text-slate-950">Selamat datang di InventaHub</h1>
        <p class="mx-auto mt-2 max-w-sm text-sm leading-6 text-slate-500">
            Sistem manajemen inventaris untuk mengelola kategori, produk, dan transaksi barang masuk maupun keluar.
        </p>
    </div>

    <div class="mt-6 space-y-2.5">
        <div class="flex items-center gap-3 rounded-xl border border-slate-100 bg-slate-50/70 px-3.5 py-3">
            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                <x-icon name="check" class="h-4 w-4" stroke="2.4" />
            </div>
            <p class="text-sm text-slate-700">Stok diperbarui otomatis dari transaksi</p>
        </div>

        <div class="flex items-center gap-3 rounded-xl border border-slate-100 bg-slate-50/70 px-3.5 py-3">
            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700">
                <x-icon name="check" class="h-4 w-4" stroke="2.4" />
            </div>
            <p class="text-sm text-slate-700">Riwayat barang masuk dan keluar</p>
        </div>

        <div class="flex items-center gap-3 rounded-xl border border-slate-100 bg-slate-50/70 px-3.5 py-3">
            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700">
                <x-icon name="check" class="h-4 w-4" stroke="2.4" />
            </div>
            <p class="text-sm text-slate-700">Hak akses Admin dan Staff yang jelas</p>
        </div>
    </div>

    <div class="mt-6 flex flex-col gap-3 sm:flex-row">
        @guest
            <a href="{{ route('login') }}" class="btn btn-primary flex-1">
                Masuk
            </a>
            <a href="{{ route('register') }}" class="btn btn-secondary flex-1">
                Daftar
            </a>
        @endguest

        @auth
            <a href="{{ route('dashboard') }}" class="btn btn-primary flex-1">
                Buka Dashboard
                <x-icon name="arrow-right" class="h-4 w-4" stroke="2" />
            </a>
        @endauth
    </div>
</div>
@endsection
