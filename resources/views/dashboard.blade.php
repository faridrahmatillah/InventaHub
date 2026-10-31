@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

@php
    $lowStockCount = $lowStockProducts->count();
    $isAdmin = auth()->user()->role === 'admin';

    $outOfStockCount = $lowStockProducts->where('stock', '<=', 0)->count();
    $lowOnlyCount = $lowStockCount - $outOfStockCount;
    $safeCount = max(0, $totalProducts - $lowStockCount);

    $percent = fn ($count) => $totalProducts > 0 ? round(($count / $totalProducts) * 100, 1) : 0;

    $safePercent = $percent($safeCount);
    $lowPercent = $percent($lowOnlyCount);
    $outPercent = $percent($outOfStockCount);
@endphp

<div class="space-y-5 sm:space-y-6">

    {{-- HEADER --}}
    <x-page-header
        eyebrow="Ikhtisar"
        title="Dashboard"
        subtitle="Ringkasan kondisi stok dan aktivitas gudang dalam satu tampilan."
    >
        @if($totalCategories > 0)
            <a href="{{ route('products.create') }}" class="btn btn-secondary">
                <x-icon name="plus" class="h-4 w-4" stroke="2" />
                Tambah Produk
            </a>
        @elseif($isAdmin)
            <a href="{{ route('categories.create') }}" class="btn btn-secondary">
                <x-icon name="plus" class="h-4 w-4" stroke="2" />
                Buat Kategori
            </a>
        @endif

        @if($totalProducts > 0)
            <a href="{{ route('transactions.create') }}" class="btn btn-primary">
                <x-icon name="transaction" class="h-4 w-4" />
                Catat Transaksi
            </a>
        @endif
    </x-page-header>

    {{-- BARIS ATAS: HERO STOK + TIGA METRIK --}}
    <section aria-label="Statistik inventaris" class="space-y-4">

        {{-- KESEHATAN STOK --}}
        <article class="panel-dark p-5 sm:p-7">
            <div class="panel-grid pointer-events-none absolute inset-0"></div>

            <div class="relative flex h-full flex-col">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-slate-400">
                        Kesehatan inventaris
                    </p>

                    @if($lowStockCount > 0)
                        <span class="inline-flex items-center gap-1.5 rounded-md border border-amber-400/20 bg-amber-400/10 px-2 py-0.5 text-[10px] font-semibold text-amber-300">
                            <span class="h-1.5 w-1.5 rounded-full bg-amber-400"></span>
                            {{ $lowStockCount }} perlu perhatian
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 rounded-md border border-emerald-400/20 bg-emerald-400/10 px-2 py-0.5 text-[10px] font-semibold text-emerald-300">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                            Kondisi aman
                        </span>
                    @endif
                </div>

                <div class="mt-5 flex items-end gap-4">
                    <p class="text-5xl font-semibold leading-none tracking-[-0.03em] text-white tabular-nums sm:text-6xl">
                        {{ number_format($totalProducts) }}
                    </p>

                    <div class="pb-1.5">
                        <p class="text-sm font-medium text-slate-200">produk terdaftar</p>
                        <p class="mt-0.5 text-[11px] text-slate-500">di seluruh inventaris</p>
                    </div>
                </div>

                <div class="mt-5">
                    <div class="flex h-2 overflow-hidden rounded-full bg-white/10" role="img" aria-label="Komposisi kondisi stok">
                        @if($safePercent > 0)
                            <span class="h-full bg-emerald-400" style="width: {{ $safePercent }}%"></span>
                        @endif
                        @if($lowPercent > 0)
                            <span class="h-full bg-amber-400" style="width: {{ $lowPercent }}%"></span>
                        @endif
                        @if($outPercent > 0)
                            <span class="h-full bg-red-500" style="width: {{ $outPercent }}%"></span>
                        @endif
                    </div>

                    <div class="mt-4 grid grid-cols-3 gap-3">
                        <div>
                            <p class="flex items-center gap-1.5 text-[10px] font-medium uppercase tracking-wider text-slate-400">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                                Aman
                            </p>
                            <p class="mt-1.5 text-sm font-semibold text-white tabular-nums">{{ number_format($safeCount) }}</p>
                        </div>

                        <div>
                            <p class="flex items-center gap-1.5 text-[10px] font-medium uppercase tracking-wider text-slate-400">
                                <span class="h-1.5 w-1.5 rounded-full bg-amber-400"></span>
                                Menipis
                            </p>
                            <p class="mt-1.5 text-sm font-semibold text-white tabular-nums">{{ number_format($lowOnlyCount) }}</p>
                        </div>

                        <div>
                            <p class="flex items-center gap-1.5 text-[10px] font-medium uppercase tracking-wider text-slate-400">
                                <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                Habis
                            </p>
                            <p class="mt-1.5 text-sm font-semibold text-white tabular-nums">{{ number_format($outOfStockCount) }}</p>
                        </div>
                    </div>
                </div>

                <div class="mt-5 flex flex-wrap items-center justify-between gap-3 border-t border-white/10 pt-4">
                    <p class="text-[11px] text-slate-500">Ambang batas stok menipis: di bawah 5 unit.</p>

                    <a href="{{ route('products.index') }}"
                       class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-white transition hover:text-emerald-300">
                        Kelola produk
                        <x-icon name="arrow-right" class="h-3.5 w-3.5" stroke="2" />
                    </a>
                </div>
            </div>
        </article>

        {{-- TIGA METRIK --}}
        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">

            @php
                $metrics = [
                    [
                        'label' => 'Total kategori',
                        'value' => number_format($totalCategories),
                        'hint' => 'Pengelompokan produk gudang',
                        'icon' => 'category',
                        'tone' => 'bg-emerald-50 text-emerald-700',
                        'href' => $isAdmin ? route('categories.index') : null,
                    ],
                    [
                        'label' => 'Total transaksi',
                        'value' => number_format($totalTransactions),
                        'hint' => 'Pergerakan stok tercatat',
                        'icon' => 'transaction',
                        'tone' => 'bg-emerald-50 text-emerald-700',
                        'href' => route('transactions.index'),
                    ],
                    [
                        'label' => 'Stok menipis',
                        'value' => number_format($lowStockCount),
                        'hint' => 'Produk di bawah 5 unit',
                        'icon' => 'alert',
                        'tone' => $lowStockCount > 0 ? 'bg-amber-50 text-amber-600' : 'bg-emerald-50 text-emerald-600',
                        'href' => route('products.index'),
                    ],
                ];
            @endphp

            @foreach($metrics as $metric)
                @if($metric['href'])
                    <a href="{{ $metric['href'] }}"
                       class="group flex min-h-24 items-center gap-4 rounded-2xl border border-slate-200/80 bg-white px-5 py-4 shadow-[0_1px_2px_rgba(15,23,42,0.04)] transition-all duration-200 hover:-translate-y-0.5 hover:border-emerald-200 hover:shadow-[0_18px_36px_-28px_rgba(15,23,42,0.35)]"
                       aria-label="{{ $metric['label'] }}">
                @else
                    <div class="flex min-h-24 items-center gap-4 rounded-2xl border border-slate-200/80 bg-white px-5 py-4 shadow-[0_1px_2px_rgba(15,23,42,0.04)]">
                @endif

                        <span class="icon-box {{ $metric['tone'] }}">
                            <x-icon :name="$metric['icon']" class="h-5 w-5" />
                        </span>

                        <div class="min-w-0 flex-1">
                            <p class="text-[13px] font-semibold text-slate-900">{{ $metric['label'] }}</p>
                            <p class="mt-0.5 text-[11px] text-slate-500">{{ $metric['hint'] }}</p>
                        </div>

                        <div class="flex shrink-0 items-center gap-3">
                            <p class="text-[26px] font-semibold leading-none tracking-tight text-slate-950 tabular-nums">
                                {{ $metric['value'] }}
                            </p>

                            @if($metric['href'])
                                <x-icon name="arrow-right"
                                        class="h-4 w-4 text-slate-300 transition-all duration-200 group-hover:translate-x-0.5 group-hover:text-slate-500" />
                            @endif
                        </div>

                @if($metric['href'])
                    </a>
                @else
                    </div>
                @endif
            @endforeach
        </div>
    </section>

    {{-- BARIS UTAMA: TABEL STOK MENIPIS --}}
    <section class="space-y-5">

        {{-- STOK MENIPIS (FOKUS UTAMA) --}}
        <section class="card overflow-hidden" aria-labelledby="low-stock-heading">
            <div class="flex flex-col gap-4 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <div class="flex items-start gap-3">
                    <span class="icon-box bg-amber-50 text-amber-600">
                        <x-icon name="alert" class="h-5 w-5" />
                    </span>

                    <div>
                        <h2 id="low-stock-heading" class="text-base font-semibold tracking-tight text-slate-950">
                            Stok menipis
                        </h2>
                        <p class="mt-0.5 text-xs text-slate-500">
                            Produk dengan stok di bawah 5 unit yang perlu segera ditindaklanjuti.
                        </p>
                    </div>
                </div>

                @if($lowStockCount > 0)
                    <x-badge tone="warning" dot>{{ $lowStockCount }} produk</x-badge>
                @else
                    <x-badge tone="success" dot>Semua aman</x-badge>
                @endif
            </div>

            @if($totalProducts == 0)

                <div class="border-t border-slate-100 px-6 py-14 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-slate-400">
                        <x-icon name="box" class="h-6 w-6" />
                    </div>

                    <h3 class="mt-4 text-sm font-semibold text-slate-900">Belum ada produk</h3>
                    <p class="mx-auto mt-1.5 max-w-sm text-xs leading-5 text-slate-500">
                        @if($totalCategories === 0)
                            @if($isAdmin)
                                Buat kategori terlebih dahulu, lalu tambahkan produk ke inventaris.
                            @else
                                Minta administrator membuat kategori sebelum produk pertama ditambahkan.
                            @endif
                        @else
                            Tambahkan produk pertama untuk mulai memantau kondisi stok gudang.
                        @endif
                    </p>

                    @if($totalCategories > 0)
                        <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm mt-5">
                            <x-icon name="plus" class="h-3.5 w-3.5" stroke="2" />
                            Tambah produk
                        </a>
                    @elseif($isAdmin)
                        <a href="{{ route('categories.create') }}" class="btn btn-primary btn-sm mt-5">
                            <x-icon name="plus" class="h-3.5 w-3.5" stroke="2" />
                            Buat kategori pertama
                        </a>
                    @endif
                </div>

            @elseif($lowStockProducts->isEmpty())

                <div class="border-t border-slate-100 px-6 py-14 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                        <x-icon name="check-circle" class="h-6 w-6" />
                    </div>

                    <h3 class="mt-4 text-sm font-semibold text-slate-900">Semua stok dalam kondisi aman</h3>
                    <p class="mx-auto mt-1.5 max-w-md text-xs leading-5 text-slate-500">
                        Tidak ada produk dengan stok habis atau menipis saat ini. Pantau terus pergerakan stok melalui transaksi.
                    </p>

                    <div class="mt-5 flex flex-wrap justify-center gap-2.5">
                        <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm">Lihat semua produk</a>
                        <a href="{{ route('transactions.create') }}" class="btn btn-primary btn-sm">
                            <x-icon name="plus" class="h-3.5 w-3.5" stroke="2" />
                            Catat transaksi
                        </a>
                    </div>
                </div>

            @else

                {{-- Tabel desktop --}}
                <div class="table-wrap hidden border-t border-slate-100 md:block">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Kategori</th>
                                <th>Sisa stok</th>
                                <th>Status</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($lowStockProducts as $product)
                                @php
                                    $stockWidth = max(4, min(100, ($product->stock / 5) * 100));
                                    $isOut = $product->stock <= 0;
                                @endphp

                                <tr>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <span class="avatar-initials h-9 w-9 text-[11px]">
                                                {{ mb_strtoupper(mb_substr($product->name, 0, 2)) }}
                                            </span>

                                            <div class="min-w-0">
                                                <a href="{{ route('products.show', $product->id) }}"
                                                   class="block truncate text-sm font-semibold text-slate-900 hover:text-emerald-700">
                                                    {{ $product->name }}
                                                </a>
                                                <p class="mt-0.5 font-mono text-[10px] font-medium text-slate-400">{{ $product->code }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <span class="inline-flex items-center rounded-md bg-slate-50 px-2 py-1 text-[11px] font-medium text-slate-600 ring-1 ring-inset ring-slate-200">
                                            {{ $product->category?->name ?? 'Tanpa kategori' }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="w-36">
                                            <div class="flex items-baseline justify-between gap-2">
                                                <span class="text-sm font-semibold tabular-nums {{ $isOut ? 'text-red-600' : 'text-amber-600' }}">
                                                    {{ number_format($product->stock) }} unit
                                                </span>
                                                <span class="text-[10px] text-slate-400">min. 5</span>
                                            </div>

                                            <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-slate-100">
                                                <span class="block h-full rounded-full {{ $isOut ? 'bg-red-500' : 'bg-amber-500' }}"
                                                      style="width: {{ $stockWidth }}%"></span>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        @if($isOut)
                                            <x-badge tone="danger" dot>Habis</x-badge>
                                        @else
                                            <x-badge tone="warning" dot>Menipis</x-badge>
                                        @endif
                                    </td>

                                    <td class="text-right">
                                        <a href="{{ route('products.show', $product->id) }}"
                                           class="btn btn-secondary btn-sm"
                                           aria-label="Lihat detail {{ $product->name }}">
                                            Detail
                                            <x-icon name="chevron-right" class="h-3.5 w-3.5" stroke="2" />
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Daftar mobile --}}
                <div class="divide-y divide-slate-100 border-t border-slate-100 md:hidden">
                    @foreach($lowStockProducts as $product)
                        <div class="px-5 py-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-slate-900">{{ $product->name }}</p>
                                    <p class="mt-0.5 font-mono text-[10px] text-slate-400">{{ $product->code }} · {{ $product->category?->name ?? 'Tanpa kategori' }}</p>
                                </div>

                                @if($product->stock <= 0)
                                    <x-badge tone="danger" dot>Habis</x-badge>
                                @else
                                    <x-badge tone="warning" dot>Menipis</x-badge>
                                @endif
                            </div>

                            <div class="mt-3 flex items-center justify-between gap-3">
                                <p class="text-sm font-semibold {{ $product->stock <= 0 ? 'text-red-600' : 'text-amber-600' }}">
                                    {{ number_format($product->stock) }} unit tersisa
                                </p>

                                <a href="{{ route('products.show', $product->id) }}" class="btn btn-secondary btn-sm">
                                    Detail
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

            @endif

            <div class="flex flex-wrap items-center justify-between gap-2 border-t border-slate-100 bg-slate-50/60 px-5 py-3.5 sm:px-6">
                <p class="text-[11px] text-slate-500">
                    {{ $lowStockCount > 0 ? number_format($lowStockCount) . ' produk membutuhkan perhatian.' : 'Tidak ada produk di bawah ambang batas.' }}
                </p>

                <a href="{{ route('products.index') }}"
                   class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-slate-600 transition hover:text-slate-900">
                    Lihat semua produk
                    <x-icon name="arrow-right" class="h-3.5 w-3.5" stroke="2" />
                </a>
            </div>
        </section>

        {{-- AKTIVITAS + RINGKASAN --}}
        <div class="grid items-start gap-5 lg:grid-cols-2">

            {{-- AKTIVITAS TERBARU --}}
            <section class="card overflow-hidden">
                <div class="card-header">
                    <div>
                        <h2 class="section-title">Aktivitas terbaru</h2>
                        <p class="section-subtitle">Pergerakan stok yang baru dicatat.</p>
                    </div>
                    @if($recentTransactions->isNotEmpty())
                        <a href="{{ route('transactions.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">
                            Lihat semua
                        </a>
                    @endif
                </div>

                @forelse($recentTransactions as $transaction)
                    <div class="flex items-center gap-3 border-t border-slate-100 px-5 py-3.5 sm:px-6">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $transaction->type === 'in' ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600' }}">
                            <x-icon :name="$transaction->type === 'in' ? 'arrow-down' : 'arrow-up'" class="h-4 w-4" stroke="2" />
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-slate-800">{{ $transaction->product?->name ?? 'Produk dihapus' }}</p>
                            <p class="mt-0.5 text-xs text-slate-500">
                                {{ $transaction->type === 'in' ? 'Barang masuk' : 'Barang keluar' }} ·
                                <x-date :value="$transaction->transaction_date" />
                            </p>
                        </div>
                        <p class="shrink-0 text-sm font-semibold tabular-nums {{ $transaction->type === 'in' ? 'text-emerald-700' : 'text-red-700' }}">
                            {{ $transaction->type === 'in' ? '+' : '-' }}{{ number_format($transaction->quantity) }}
                        </p>
                    </div>
                @empty
                    <div class="border-t border-slate-100 px-5 py-6 sm:px-6">
                        <p class="text-sm font-medium text-slate-700">Belum ada aktivitas stok.</p>
                        <p class="mt-1 text-xs leading-5 text-slate-500">Transaksi yang dicatat akan muncul di sini.</p>
                        <a href="{{ route('transactions.create') }}" class="btn btn-secondary btn-sm mt-4">
                            Catat transaksi pertama
                            <x-icon name="arrow-right" class="h-3.5 w-3.5" stroke="2" />
                        </a>
                    </div>
                @endforelse
            </section>

            {{-- RINGKASAN --}}
            <section class="card">
                <div class="card-header">
                    <div>
                        <h2 class="section-title">Ringkasan inventaris</h2>
                        <p class="section-subtitle">Kondisi data saat ini.</p>
                    </div>
                </div>

                <dl class="divide-y divide-slate-100 border-t border-slate-100">
                    <div class="flex items-center justify-between gap-3 px-5 py-3.5 sm:px-6">
                        <dt class="flex items-center gap-2.5 text-[13px] text-slate-600">
                            <x-icon name="box" class="h-4 w-4 text-slate-400" />
                            Produk terdaftar
                        </dt>
                        <dd class="text-[13px] font-semibold text-slate-900 tabular-nums">{{ number_format($totalProducts) }}</dd>
                    </div>

                    <div class="flex items-center justify-between gap-3 px-5 py-3.5 sm:px-6">
                        <dt class="flex items-center gap-2.5 text-[13px] text-slate-600">
                            <x-icon name="category" class="h-4 w-4 text-slate-400" />
                            Kategori aktif
                        </dt>
                        <dd class="text-[13px] font-semibold text-slate-900 tabular-nums">{{ number_format($totalCategories) }}</dd>
                    </div>

                    <div class="flex items-center justify-between gap-3 px-5 py-3.5 sm:px-6">
                        <dt class="flex items-center gap-2.5 text-[13px] text-slate-600">
                            <x-icon name="transaction" class="h-4 w-4 text-slate-400" />
                            Transaksi tercatat
                        </dt>
                        <dd class="text-[13px] font-semibold text-slate-900 tabular-nums">{{ number_format($totalTransactions) }}</dd>
                    </div>

                    <div class="flex items-center justify-between gap-3 px-5 py-3.5 sm:px-6">
                        <dt class="flex items-center gap-2.5 text-[13px] text-slate-600">
                            <x-icon name="alert" class="h-4 w-4 text-slate-400" />
                            Perlu perhatian
                        </dt>
                        <dd>
                            @if($lowStockCount > 0)
                                <x-badge tone="warning">{{ number_format($lowStockCount) }} produk</x-badge>
                            @else
                                <x-badge tone="success">Aman</x-badge>
                            @endif
                        </dd>
                    </div>
                </dl>
            </section>
        </div>
    </section>
</div>
@endsection
