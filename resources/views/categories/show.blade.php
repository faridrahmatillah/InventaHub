@extends('layouts.app')

@section('title', 'Detail Kategori')

@section('content')

@php
    $productCount = $category->products->count();
    $safeCount = $category->products->where('stock', '>=', 5)->count();
    $lowCount = $category->products->where('stock', '>', 0)->where('stock', '<', 5)->count();
    $outCount = $category->products->where('stock', '<=', 0)->count();
    $maxStock = max(1, (int) $category->products->max('stock'));
@endphp

<div class="mx-auto max-w-6xl space-y-6">

    {{-- HEADER --}}
    <x-page-header
        :title="$category->name"
        subtitle="Detail kategori dan produk yang terdaftar di dalamnya."
        :breadcrumbs="[
            ['label' => 'Kategori', 'url' => route('categories.index')],
            ['label' => $category->name],
        ]"
    >
        <a href="{{ route('categories.index') }}" class="btn btn-secondary">
            <x-icon name="arrow-left" class="h-4 w-4" stroke="2" />
            Kembali
        </a>

        <a href="{{ route('categories.edit', $category) }}" class="btn btn-primary">
            <x-icon name="edit" class="h-4 w-4" />
            Edit Kategori
        </a>

        @if($productCount === 0)
            <form action="{{ route('categories.destroy', $category) }}"
                  method="POST"
                  data-confirm="Kategori akan dinonaktifkan (soft delete) dan tidak lagi tampil di daftar."
                  data-confirm-title="Hapus kategori?"
                  data-confirm-action="Ya, hapus">
                @csrf
                @method('DELETE')

                <button type="submit" class="btn btn-danger">
                    <x-icon name="trash" class="h-4 w-4" />
                    Hapus
                </button>
            </form>
        @endif
    </x-page-header>

    {{-- HERO KATEGORI --}}
    <section class="card overflow-hidden" aria-label="Ringkasan kategori">
        <div class="grid gap-6 p-5 sm:p-6 lg:grid-cols-[minmax(0,1.45fr)_minmax(0,1fr)] lg:gap-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
                <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700">
                    <x-icon name="category" class="h-6 w-6" />
                </span>

                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-xl font-semibold tracking-tight text-slate-950">{{ $category->name }}</h2>

                        @if($productCount > 0)
                            <x-badge tone="info" dot>{{ $productCount }} produk</x-badge>
                        @else
                            <x-badge tone="neutral" dot>Kategori kosong</x-badge>
                        @endif
                    </div>

                    <p class="mt-1 font-mono text-[11px] font-medium text-slate-400">Kategori #{{ $category->id }}</p>

                    <div class="mt-3 flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-md bg-slate-50 px-2 py-1 text-[11px] font-medium text-slate-600 ring-1 ring-inset ring-slate-200">
                            <x-icon name="calendar" class="h-3.5 w-3.5 text-slate-400" />
                            Dibuat <x-date :value="$category->created_at" format="d M Y" />
                        </span>

                        <span class="inline-flex items-center gap-1.5 rounded-md bg-slate-50 px-2 py-1 text-[11px] font-medium text-slate-600 ring-1 ring-inset ring-slate-200">
                            <x-icon name="clock" class="h-3.5 w-3.5 text-slate-400" />
                            Diubah <x-date :value="$category->updated_at" format="d M Y" />
                        </span>
                    </div>

                    <p class="mt-4 max-w-xl text-sm leading-6 text-slate-500">
                        {{ $category->description ?: 'Belum ada deskripsi untuk kategori ini.' }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 lg:border-l lg:border-slate-100 lg:pl-8">
                <div class="rounded-xl bg-slate-50/80 p-3.5">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">Produk terdaftar</p>
                    <p class="mt-1.5 text-lg font-semibold tracking-tight text-slate-950 tabular-nums">{{ number_format($productCount) }}</p>
                    <p class="mt-0.5 text-[10px] text-slate-400">dalam kategori ini</p>
                </div>

                <div class="rounded-xl bg-slate-50/80 p-3.5">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">Perlu perhatian</p>
                    <p class="mt-1.5 text-lg font-semibold tracking-tight {{ $lowCount + $outCount > 0 ? 'text-amber-600' : 'text-emerald-600' }} tabular-nums">
                        {{ number_format($lowCount + $outCount) }}
                    </p>
                    <p class="mt-0.5 text-[10px] text-slate-400">stok menipis / habis</p>
                </div>

                <a href="{{ route('products.create') }}" class="btn btn-primary col-span-2">
                    <x-icon name="plus" class="h-4 w-4" stroke="2" />
                    Tambah Produk
                </a>
            </div>
        </div>

        {{-- STATISTIK PRODUK DALAM KATEGORI --}}
        <div class="grid grid-cols-2 gap-px border-t border-slate-100 bg-slate-100 sm:grid-cols-4">
            <div class="bg-white px-5 py-4">
                <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">Total produk</p>
                <p class="mt-1.5 text-lg font-semibold text-slate-900 tabular-nums">{{ number_format($productCount) }}</p>
            </div>

            <div class="bg-white px-5 py-4">
                <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">Stok aman</p>
                <p class="mt-1.5 text-lg font-semibold text-emerald-600 tabular-nums">{{ number_format($safeCount) }}</p>
            </div>

            <div class="bg-white px-5 py-4">
                <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">Stok menipis</p>
                <p class="mt-1.5 text-lg font-semibold text-amber-600 tabular-nums">{{ number_format($lowCount) }}</p>
            </div>

            <div class="bg-white px-5 py-4">
                <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">Stok habis</p>
                <p class="mt-1.5 text-lg font-semibold text-red-600 tabular-nums">{{ number_format($outCount) }}</p>
            </div>
        </div>
    </section>

    <div class="grid items-start gap-6 lg:grid-cols-3">

        {{-- KOLOM UTAMA --}}
        <div class="space-y-6 lg:col-span-2">

            {{-- PRODUK DALAM KATEGORI --}}
            <section class="card overflow-hidden">
                <div class="card-header">
                    <div class="flex items-start gap-3">
                        <span class="icon-box bg-slate-100 text-slate-500">
                            <x-icon name="box" class="h-5 w-5" />
                        </span>

                        <div>
                            <h2 class="section-title">Produk dalam kategori</h2>
                            <p class="section-subtitle">{{ number_format($productCount) }} produk aktif terdaftar.</p>
                        </div>
                    </div>

                    <a href="{{ route('products.index') }}" class="btn btn-ghost btn-sm">
                        Semua produk
                        <x-icon name="chevron-right" class="h-3.5 w-3.5" stroke="2" />
                    </a>
                </div>

                @if($productCount === 0)

                    <div class="border-t border-slate-100 p-5 sm:p-6">
                        <x-empty-state
                            title="Belum ada produk"
                            description="Kategori ini belum memiliki produk aktif. Tambahkan produk dan pilih kategori ini."
                            icon="box"
                            compact
                        >
                            <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm">
                                <x-icon name="plus" class="h-4 w-4" stroke="2" />
                                Tambah Produk
                            </a>
                        </x-empty-state>
                    </div>

                @else

                    {{-- TABEL DESKTOP --}}
                    <div class="table-wrap hidden border-t border-slate-100 sm:block">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th class="text-right">Harga</th>
                                    <th>Stok</th>
                                    <th>Status</th>
                                    <th class="text-right">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($category->products as $product)
                                    @php
                                        $status = $product->stock <= 0 ? 'out' : ($product->stock < 5 ? 'low' : 'safe');
                                    @endphp

                                    <tr>
                                        <td>
                                            <div class="flex items-center gap-3">
                                                <span class="avatar-initials h-9 w-9 text-[10px]">
                                                    {{ mb_strtoupper(mb_substr($product->name, 0, 2)) }}
                                                </span>

                                                <div class="min-w-0">
                                                    <a href="{{ route('products.show', $product->id) }}"
                                                       class="block truncate text-sm font-semibold text-slate-900 transition hover:text-emerald-700">
                                                        {{ $product->name }}
                                                    </a>
                                                    <p class="mt-0.5 font-mono text-[10px] font-medium text-slate-400">{{ $product->code }}</p>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="text-right">
                                            <span class="whitespace-nowrap text-[13px] font-semibold text-slate-900 tabular-nums">
                                                Rp {{ number_format($product->price, 0, ',', '.') }}
                                            </span>
                                        </td>

                                        <td>
                                            <div class="w-24">
                                                <div class="flex items-baseline justify-between gap-2">
                                                    <span class="text-[13px] font-semibold tabular-nums {{ $status === 'out' ? 'text-red-600' : ($status === 'low' ? 'text-amber-600' : 'text-slate-900') }}">
                                                        {{ number_format($product->stock) }}
                                                    </span>
                                                    <span class="text-[10px] text-slate-400">unit</span>
                                                </div>

                                                <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-slate-100">
                                                    <span class="block h-full rounded-full {{ $status === 'out' ? 'bg-red-500' : ($status === 'low' ? 'bg-amber-500' : 'bg-emerald-500') }}"
                                                          style="width: {{ $product->stock > 0 ? max(6, round(($product->stock / $maxStock) * 100)) : 0 }}%"></span>
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            @if($status === 'out')
                                                <x-badge tone="danger" dot>Habis</x-badge>
                                            @elseif($status === 'low')
                                                <x-badge tone="warning" dot>Menipis</x-badge>
                                            @else
                                                <x-badge tone="success" dot>Aman</x-badge>
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

                    {{-- DAFTAR MOBILE --}}
                    <ul class="divide-y divide-slate-100 border-t border-slate-100 sm:hidden">
                        @foreach($category->products as $product)
                            <li class="px-5 py-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex min-w-0 items-center gap-3">
                                        <span class="avatar-initials h-9 w-9 text-[10px]">
                                            {{ mb_strtoupper(mb_substr($product->name, 0, 2)) }}
                                        </span>

                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-semibold text-slate-900">{{ $product->name }}</p>
                                            <p class="mt-0.5 font-mono text-[10px] text-slate-400">{{ $product->code }}</p>
                                        </div>
                                    </div>

                                    @if($product->stock <= 0)
                                        <x-badge tone="danger" dot>Habis</x-badge>
                                    @elseif($product->stock < 5)
                                        <x-badge tone="warning" dot>Menipis</x-badge>
                                    @else
                                        <x-badge tone="success" dot>Aman</x-badge>
                                    @endif
                                </div>

                                <div class="mt-3 flex items-center justify-between gap-3 border-t border-slate-100 pt-3">
                                    <div>
                                        <p class="text-[10px] font-medium uppercase tracking-wider text-slate-400">Harga</p>
                                        <p class="mt-0.5 text-xs font-semibold text-slate-800 tabular-nums">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                    </div>

                                    <div class="text-right">
                                        <p class="text-[10px] font-medium uppercase tracking-wider text-slate-400">Stok</p>
                                        <p class="mt-0.5 text-xs font-semibold tabular-nums {{ $product->stock <= 0 ? 'text-red-600' : ($product->stock < 5 ? 'text-amber-600' : 'text-slate-900') }}">
                                            {{ number_format($product->stock) }} unit
                                        </p>
                                    </div>

                                    <a href="{{ route('products.show', $product->id) }}" class="btn btn-secondary btn-sm">
                                        Detail
                                    </a>
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    <div class="flex flex-wrap items-center justify-between gap-2 border-t border-slate-100 bg-slate-50/60 px-5 py-3.5 sm:px-6">
                        <p class="text-[11px] text-slate-500">{{ number_format($productCount) }} produk dalam kategori ini.</p>
                        <p class="text-[11px] text-slate-400">Ambang batas stok menipis: di bawah 5 unit.</p>
                    </div>

                @endif
            </section>
        </div>

        {{-- SIDEBAR --}}
        <aside class="space-y-5">

            {{-- DESKRIPSI --}}
            <section class="card overflow-hidden">
                <div class="px-5 py-4">
                    <h2 class="section-title">Deskripsi kategori</h2>
                    <p class="section-subtitle">Penjelasan singkat kategori ini.</p>
                </div>

                <div class="border-t border-slate-100 p-5">
                    <p class="whitespace-pre-line text-sm leading-6 text-slate-600">
                        {{ $category->description ?: 'Belum ada deskripsi untuk kategori ini.' }}
                    </p>
                </div>
            </section>

            {{-- INFORMASI PENCATATAN --}}
            <section class="card overflow-hidden">
                <div class="px-5 py-4">
                    <h2 class="section-title">Informasi pencatatan</h2>
                    <p class="section-subtitle">Detail administrasi kategori.</p>
                </div>

                <dl class="divide-y divide-slate-100 border-t border-slate-100">
                    <div class="flex items-center justify-between gap-3 px-5 py-3.5">
                        <dt class="text-[11px] text-slate-500">ID kategori</dt>
                        <dd class="text-[11px] font-semibold text-slate-700">#{{ $category->id }}</dd>
                    </div>

                    <div class="flex items-center justify-between gap-3 px-5 py-3.5">
                        <dt class="text-[11px] text-slate-500">Jumlah produk</dt>
                        <dd><x-badge tone="neutral">{{ $productCount }} produk</x-badge></dd>
                    </div>

                    <div class="flex items-center justify-between gap-3 px-5 py-3.5">
                        <dt class="text-[11px] text-slate-500">Ditambahkan</dt>
                        <dd class="text-right text-[11px] font-semibold text-slate-700">
                            <x-date :value="$category->created_at" format="d M Y, H:i" />
                        </dd>
                    </div>

                    <div class="flex items-center justify-between gap-3 px-5 py-3.5">
                        <dt class="text-[11px] text-slate-500">Diperbarui</dt>
                        <dd class="text-right text-[11px] font-semibold text-slate-700">
                            <x-date :value="$category->updated_at" format="d M Y, H:i" />
                        </dd>
                    </div>
                </dl>
            </section>

            <div class="info-note">
                <x-icon name="info" class="mt-0.5 h-4 w-4 shrink-0" />
                <p>
                    Kategori hanya dapat dihapus jika tidak memiliki produk aktif. Data yang dihapus menggunakan soft delete sehingga masih tersimpan di database.
                </p>
            </div>
        </aside>
    </div>
</div>
@endsection
