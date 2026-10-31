@extends('layouts.app')

@section('title', 'Akses Ditolak')

@section('content')
<div class="mx-auto max-w-2xl py-2">

    {{-- KARTU 403 --}}
    <div class="card overflow-hidden">
        <div class="relative bg-slate-950 px-6 py-10 text-center text-white sm:px-10 sm:py-12">
            <div class="panel-grid pointer-events-none absolute inset-0"></div>

            <div class="relative">
                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl border border-white/10 bg-white/5 text-red-300">
                    <x-icon name="lock" class="h-5 w-5" />
                </span>

                <p class="mt-5 text-[10px] font-semibold uppercase tracking-[0.2em] text-slate-400">Error 403</p>

                <h1 class="mt-2 text-2xl font-semibold tracking-tight text-white">Akses ditolak</h1>

                <p class="mx-auto mt-3 max-w-md text-sm leading-6 text-slate-400">
                    {{ isset($exception) && $exception->getMessage() ? $exception->getMessage() : 'Anda tidak memiliki hak akses untuk membuka halaman ini.' }}
                </p>
            </div>
        </div>

        <div class="p-6 sm:p-8">
            <div class="flex items-start gap-3 rounded-xl border border-slate-200/80 bg-slate-50/70 px-4 py-4">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white text-slate-400 ring-1 ring-slate-200">
                    <x-icon name="user" class="h-4 w-4" />
                </span>

                <div>
                    <p class="text-[13px] font-semibold text-slate-800">Halaman ini membutuhkan peran tertentu</p>
                    <p class="mt-1 text-xs leading-5 text-slate-500">
                        Hanya pengguna dengan hak akses yang sesuai dapat membuka halaman ini. Silakan kembali ke dashboard atau masuk dengan akun yang memiliki akses.
                    </p>
                </div>
            </div>

            <div class="mt-6 flex flex-col gap-2.5 sm:flex-row">
                <a href="{{ route('dashboard') }}" class="btn btn-primary flex-1">
                    <x-icon name="arrow-left" class="h-4 w-4" stroke="2" />
                    Kembali ke Dashboard
                </a>

                @guest
                    <a href="{{ route('login') }}" class="btn btn-secondary flex-1">
                        Masuk
                    </a>
                @endguest
            </div>
        </div>
    </div>

    <p class="mt-4 text-center text-[11px] text-slate-400">
        Jika Anda merasa seharusnya memiliki akses, hubungi administrator InventaHub.
    </p>
</div>
@endsection
