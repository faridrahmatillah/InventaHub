@extends('layouts.app')

@section('title', 'Detail Stok Opname')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <x-page-header
        title="Detail Stok Opname"
        subtitle="Catatan audit ini tidak dapat diubah atau dihapus."
        :breadcrumbs="[
            ['label' => 'Stok Opname', 'url' => route('stock-opnames.index')],
            ['label' => 'Detail'],
        ]"
    >
        <a href="{{ route('stock-opnames.index') }}" class="btn btn-secondary">Kembali</a>
        <a href="{{ route('stock-opnames.create') }}" class="btn btn-primary">Catat Opname Baru</a>
    </x-page-header>

    <section class="card grid gap-5 p-5 sm:grid-cols-2 sm:p-6">
        <div>
            <p class="text-xs text-slate-500">Produk</p>
            <p class="mt-1 font-semibold text-slate-900">{{ $stockOpname->product?->name ?? 'Produk dihapus' }}</p>
            <p class="text-xs text-slate-400">{{ $stockOpname->product?->code ?? '—' }}</p>
        </div>
        <div>
            <p class="text-xs text-slate-500">Tanggal dihitung</p>
            <p class="mt-1 font-medium text-slate-900"><x-date :value="$stockOpname->counted_at" /></p>
        </div>
        <div>
            <p class="text-xs text-slate-500">Stok menurut sistem</p>
            <p class="mt-1 text-xl font-semibold text-slate-900">{{ number_format($stockOpname->system_stock) }}</p>
        </div>
        <div>
            <p class="text-xs text-slate-500">Hasil hitung fisik</p>
            <p class="mt-1 text-xl font-semibold text-slate-900">{{ number_format($stockOpname->counted_stock) }}</p>
        </div>
        <div>
            <p class="text-xs text-slate-500">Selisih stok</p>
            <p class="mt-1 text-xl font-semibold {{ $stockOpname->difference < 0 ? 'text-red-600' : ($stockOpname->difference > 0 ? 'text-emerald-600' : 'text-slate-500') }}">
                {{ $stockOpname->difference > 0 ? '+' : '' }}{{ number_format($stockOpname->difference) }}
            </p>
        </div>
        <div>
            <p class="text-xs text-slate-500">Dicatat oleh</p>
            <div class="mt-1 flex flex-wrap items-center gap-2">
                <p class="font-medium text-slate-900">{{ $stockOpname->user?->name ?? 'Pengguna tidak tersedia' }}</p>
                @if($stockOpname->user)
                    <x-badge tone="neutral">
                        {{ $stockOpname->user->role === 'admin' ? 'Admin' : 'Staff' }}
                    </x-badge>
                @endif
            </div>
        </div>
        <div class="sm:col-span-2">
            <p class="text-xs text-slate-500">Catatan</p>
            <p class="mt-1 whitespace-pre-line text-sm text-slate-800">{{ $stockOpname->note ?: 'Tidak ada catatan.' }}</p>
        </div>
    </section>
</div>
@endsection
