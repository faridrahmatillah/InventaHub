@extends('layouts.app')

@section('title', 'Detail Produk')

@section('content')

@php
    $stockTone = $product->stock <= 0 ? 'danger' : ($product->stock < 5 ? 'warning' : 'success');
    $stockLabel = $product->stock <= 0 ? 'Stok habis' : ($product->stock < 5 ? 'Stok menipis' : 'Stok aman');
    $stockValue = $product->stock <= 0 ? 'text-red-600' : ($product->stock < 5 ? 'text-amber-600' : 'text-emerald-600');
    $stockBar = $product->stock <= 0 ? 'bg-red-500' : ($product->stock < 5 ? 'bg-amber-500' : 'bg-emerald-500');
    $stockWidth = $product->stock > 0 ? max(6, min(100, ($product->stock / 5) * 100)) : 0;

    $recentTransactions = $product->transactions
        ->sortByDesc('transaction_date')
        ->take(5);

    $transactionCount = $product->transactions->count();
    $totalIn = $product->transactions->where('type', 'in')->sum('quantity');
    $totalOut = $product->transactions->where('type', 'out')->sum('quantity');
    $lastTransaction = $recentTransactions->first();
@endphp

<div class="mx-auto max-w-6xl space-y-6">

    {{-- HEADER --}}
    <x-page-header
        :title="$product->name"
        :subtitle="'Kode produk: ' . $product->code"
        :breadcrumbs="[
            ['label' => 'Data Produk', 'url' => route('products.index')],
            ['label' => $product->name],
        ]"
    >
        <a href="{{ route('products.index') }}" class="btn btn-secondary">
            <x-icon name="arrow-left" class="h-4 w-4" stroke="2" />
            Kembali
        </a>

        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-primary">
            <x-icon name="edit" class="h-4 w-4" />
            Edit Produk
        </a>

        @if(auth()->user()->role === 'admin' && $product->stock <= 0)
            <form action="{{ route('products.destroy', $product->id) }}"
                  method="POST"
                  data-confirm="Produk akan dinonaktifkan (soft delete) dan tidak lagi tampil di daftar produk."
                  data-confirm-title="Nonaktifkan produk?"
                  data-confirm-action="Ya, nonaktifkan">
                @csrf
                @method('DELETE')

                <button type="submit" class="btn btn-danger">
                    <x-icon name="trash" class="h-4 w-4" />
                    Nonaktifkan
                </button>
            </form>
        @endif
    </x-page-header>

    {{-- HERO PRODUK --}}
    <section class="card overflow-hidden" aria-label="Ringkasan produk">
        <div class="grid gap-6 p-5 sm:p-6 lg:grid-cols-[minmax(0,1.5fr)_minmax(0,1fr)] lg:gap-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
                <span class="avatar-initials h-14 w-14 text-base">
                    {{ mb_strtoupper(mb_substr($product->name, 0, 2)) }}
                </span>

                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-xl font-semibold tracking-tight text-slate-950">{{ $product->name }}</h2>
                        <x-badge :tone="$stockTone" dot>{{ $stockLabel }}</x-badge>
                    </div>

                    <p class="mt-1 font-mono text-[11px] font-medium text-slate-400">{{ $product->code }}</p>

                    <div class="mt-3 flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-md bg-slate-50 px-2 py-1 text-[11px] font-medium text-slate-600 ring-1 ring-inset ring-slate-200">
                            <x-icon name="category" class="h-3.5 w-3.5 text-slate-400" />
                            {{ $product->category?->name ?? 'Tanpa kategori' }}
                        </span>

                        <span class="inline-flex items-center gap-1.5 rounded-md bg-slate-50 px-2 py-1 text-[11px] font-medium text-slate-600 ring-1 ring-inset ring-slate-200">
                            <x-icon name="calendar" class="h-3.5 w-3.5 text-slate-400" />
                            Ditambahkan <x-date :value="$product->created_at" format="d M Y" />
                        </span>
                    </div>

                    <p class="mt-4 max-w-xl text-sm leading-6 text-slate-500">
                        {{ $product->description ?: 'Belum ada deskripsi untuk produk ini.' }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 lg:border-l lg:border-slate-100 lg:pl-8">
                <div class="rounded-xl bg-slate-50/80 p-3.5">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">Harga satuan</p>
                    <p class="mt-1.5 text-lg font-semibold tracking-tight text-slate-950 tabular-nums">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </p>
                </div>

                <div class="rounded-xl bg-slate-50/80 p-3.5">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">Stok tersedia</p>
                    <p class="mt-1.5 text-lg font-semibold tracking-tight {{ $stockValue }} tabular-nums">
                        {{ number_format($product->stock) }}
                        <span class="text-xs font-medium text-slate-400">unit</span>
                    </p>

                    <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-white">
                        <span class="block h-full rounded-full {{ $stockBar }}" style="width: {{ $stockWidth }}%"></span>
                    </div>
                </div>

                <a href="{{ route('transactions.create') }}" class="btn btn-primary col-span-2">
                    <x-icon name="plus" class="h-4 w-4" stroke="2" />
                    Catat Transaksi
                </a>
            </div>
        </div>

        {{-- STATISTIK PRODUK --}}
        <div class="grid grid-cols-2 gap-px border-t border-slate-100 bg-slate-100 sm:grid-cols-4">
            <div class="bg-white px-5 py-4">
                <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">Transaksi</p>
                <p class="mt-1.5 text-lg font-semibold text-slate-900 tabular-nums">{{ number_format($transactionCount) }}</p>
            </div>

            <div class="bg-white px-5 py-4">
                <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">Total masuk</p>
                <p class="mt-1.5 text-lg font-semibold text-emerald-600 tabular-nums">{{ $totalIn > 0 ? '+' : '' }}{{ number_format($totalIn) }}</p>
            </div>

            <div class="bg-white px-5 py-4">
                <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">Total keluar</p>
                <p class="mt-1.5 text-lg font-semibold text-red-600 tabular-nums">{{ $totalOut > 0 ? '-' : '' }}{{ number_format($totalOut) }}</p>
            </div>

            <div class="bg-white px-5 py-4">
                <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">Pergerakan terakhir</p>
                <p class="mt-1.5 text-sm font-semibold text-slate-900">
                    @if($lastTransaction)
                        <x-date :value="$lastTransaction->transaction_date" format="d M Y" />
                    @else
                        <span class="text-slate-400">Belum ada</span>
                    @endif
                </p>
            </div>
        </div>
    </section>

    <div class="grid items-start gap-6 lg:grid-cols-3">

        {{-- KOLOM UTAMA --}}
        <div class="space-y-6 lg:col-span-2">

            {{-- RIWAYAT TRANSAKSI --}}
            <section class="card overflow-hidden">
                <div class="card-header">
                    <div class="flex items-start gap-3">
                        <span class="icon-box bg-emerald-50 text-emerald-700">
                            <x-icon name="transaction" class="h-5 w-5" />
                        </span>
                        <div>
                            <h2 class="section-title">Riwayat transaksi</h2>
                            <p class="section-subtitle">5 pergerakan stok terakhir untuk produk ini.</p>
                        </div>
                    </div>

                    <a href="{{ route('transactions.index') }}" class="btn btn-ghost btn-sm">
                        Lihat semua
                        <x-icon name="chevron-right" class="h-3.5 w-3.5" stroke="2" />
                    </a>
                </div>

                @if($recentTransactions->isEmpty())
                    <div class="border-t border-slate-100 p-5 sm:p-6">
                        <x-empty-state
                            title="Belum ada transaksi"
                            description="Riwayat barang masuk dan keluar untuk produk ini akan muncul di sini."
                            icon="transaction"
                            compact
                        >
                            <a href="{{ route('transactions.create') }}" class="btn btn-primary btn-sm">
                                <x-icon name="plus" class="h-3.5 w-3.5" stroke="2" />
                                Catat Transaksi
                            </a>
                        </x-empty-state>
                    </div>
                @else
                    <div class="table-wrap hidden border-t border-slate-100 sm:block">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jenis</th>
                                    <th class="text-right">Jumlah</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($recentTransactions as $transaction)
                                    <tr>
                                        <td class="whitespace-nowrap text-[13px] text-slate-600">
                                            <x-date :value="$transaction->transaction_date" />
                                        </td>

                                        <td>
                                            @if($transaction->type === 'in')
                                                <x-badge tone="success" dot>Masuk</x-badge>
                                            @else
                                                <x-badge tone="danger" dot>Keluar</x-badge>
                                            @endif
                                        </td>

                                        <td class="text-right">
                                            <span class="whitespace-nowrap text-[13px] font-semibold tabular-nums {{ $transaction->type === 'in' ? 'text-emerald-600' : 'text-red-600' }}">
                                                {{ $transaction->type === 'in' ? '+' : '-' }}{{ number_format($transaction->quantity) }}
                                            </span>
                                        </td>

                                        <td>
                                            <p class="max-w-[220px] truncate text-[13px] text-slate-500" title="{{ $transaction->description }}">
                                                {{ $transaction->description ?: '—' }}
                                            </p>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <ul class="divide-y divide-slate-100 border-t border-slate-100 sm:hidden">
                        @foreach($recentTransactions as $transaction)
                            <li class="flex items-start justify-between gap-3 px-5 py-3.5">
                                <div class="min-w-0">
                                    <p class="text-[13px] font-medium text-slate-800">
                                        {{ $transaction->type === 'in' ? 'Barang masuk' : 'Barang keluar' }}
                                    </p>
                                    <p class="mt-0.5 text-[11px] text-slate-400">
                                        <x-date :value="$transaction->transaction_date" />
                                    </p>
                                </div>

                                <span class="shrink-0 text-sm font-semibold tabular-nums {{ $transaction->type === 'in' ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ $transaction->type === 'in' ? '+' : '-' }}{{ number_format($transaction->quantity) }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

            {{-- INFORMASI PRODUK --}}
            <section class="card overflow-hidden">
                <div class="card-header">
                    <div class="flex items-start gap-3">
                        <span class="icon-box bg-emerald-50 text-emerald-700">
                            <x-icon name="box" class="h-5 w-5" />
                        </span>
                        <div>
                            <h2 class="section-title">Informasi produk</h2>
                            <p class="section-subtitle">Data lengkap yang tersimpan di inventaris.</p>
                        </div>
                    </div>
                </div>

                <dl class="grid gap-x-8 gap-y-6 border-t border-slate-100 p-5 sm:grid-cols-2 sm:p-6">
                    <div>
                        <dt class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">Kode / SKU</dt>
                        <dd class="mt-2">
                            <span class="inline-flex rounded-md bg-slate-100 px-2.5 py-1.5 font-mono text-xs font-semibold text-slate-700">
                                {{ $product->code }}
                            </span>
                        </dd>
                    </div>

                    <div>
                        <dt class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">Kategori</dt>
                        <dd class="mt-2 text-sm font-medium text-slate-800">{{ $product->category?->name ?? 'Tanpa kategori' }}</dd>
                    </div>

                    <div>
                        <dt class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">Harga produk</dt>
                        <dd class="mt-2 text-base font-semibold text-slate-900 tabular-nums">Rp {{ number_format($product->price, 0, ',', '.') }}</dd>
                    </div>

                    <div>
                        <dt class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">Jumlah stok</dt>
                        <dd class="mt-2 text-base font-semibold {{ $stockValue }} tabular-nums">
                            {{ number_format($product->stock) }} <span class="text-xs font-medium text-slate-400">unit</span>
                        </dd>
                    </div>

                    <div class="sm:col-span-2">
                        <dt class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">Deskripsi</dt>
                        <dd class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-600">
                            {{ $product->description ?: 'Belum ada deskripsi untuk produk ini.' }}
                        </dd>
                    </div>
                </dl>
            </section>
        </div>

        {{-- SIDEBAR --}}
        <aside class="space-y-5">

            {{-- RINGKASAN STOK --}}
            <section class="card overflow-hidden">
                <div class="px-5 py-4">
                    <h2 class="section-title">Ringkasan stok</h2>
                    <p class="section-subtitle">Kondisi stok saat ini.</p>
                </div>

                <div class="border-t border-slate-100 p-5">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">Stok tersedia</p>

                    <div class="mt-1.5 flex items-baseline gap-2">
                        <span class="text-4xl font-semibold tracking-tight {{ $stockValue }} tabular-nums">{{ number_format($product->stock) }}</span>
                        <span class="text-sm text-slate-400">unit</span>
                    </div>

                    <div class="mt-4">
                        <div class="flex items-center justify-between gap-2 text-[11px] text-slate-500">
                            <span>Level stok</span>
                            <span class="font-semibold {{ $stockValue }}">{{ $stockLabel }}</span>
                        </div>

                        <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-slate-100">
                            <span class="block h-full rounded-full {{ $stockBar }}" style="width: {{ $stockWidth }}%"></span>
                        </div>

                        <p class="mt-2 text-[10px] leading-4 text-slate-400">Ambang batas perhatian: di bawah 5 unit.</p>
                    </div>

                    <a href="{{ route('transactions.create') }}" class="btn btn-primary mt-5 w-full">
                        <x-icon name="plus" class="h-4 w-4" stroke="2" />
                        Catat Transaksi
                    </a>
                </div>
            </section>

            {{-- INFORMASI PENCATATAN --}}
            <section class="card overflow-hidden">
                <div class="px-5 py-4">
                    <h2 class="section-title">Informasi pencatatan</h2>
                    <p class="section-subtitle">Detail administrasi produk.</p>
                </div>

                <dl class="divide-y divide-slate-100 border-t border-slate-100">
                    <div class="flex items-center justify-between gap-3 px-5 py-3.5">
                        <dt class="text-[11px] text-slate-500">ID produk</dt>
                        <dd class="text-[11px] font-semibold text-slate-700">#{{ $product->id }}</dd>
                    </div>

                    <div class="flex items-center justify-between gap-3 px-5 py-3.5">
                        <dt class="text-[11px] text-slate-500">Ditambahkan</dt>
                        <dd class="text-right text-[11px] font-semibold text-slate-700">
                            <x-date :value="$product->created_at" format="d M Y, H:i" />
                        </dd>
                    </div>

                    <div class="flex items-center justify-between gap-3 px-5 py-3.5">
                        <dt class="text-[11px] text-slate-500">Diperbarui</dt>
                        <dd class="text-right text-[11px] font-semibold text-slate-700">
                            <x-date :value="$product->updated_at" format="d M Y, H:i" />
                        </dd>
                    </div>
                </dl>
            </section>

            <div class="info-note">
                <x-icon name="info" class="mt-0.5 h-4 w-4 shrink-0" />
                <div>
                    <p>
                        Stok hanya berubah melalui transaksi barang masuk atau keluar. Halaman edit produk tidak digunakan untuk mengubah jumlah stok.
                    </p>

                    @if(auth()->user()->role === 'admin')
                        <p class="mt-2 border-t border-emerald-200/70 pt-2">
                            Produk hanya dapat dinonaktifkan ketika stoknya sudah 0 unit.
                        </p>
                    @endif
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection
