@extends('layouts.app')

@section('title', 'Data Produk')

@section('content')

@php
    $visibleProducts = method_exists($products, 'getCollection')
        ? $products->getCollection()
        : collect($products);

    $outOfStock = $visibleProducts->filter(fn ($item) => $item->stock <= 0)->count();
    $lowStock = $visibleProducts->filter(fn ($item) => $item->stock > 0 && $item->stock < 5)->count();
    $safeStock = $visibleProducts->filter(fn ($item) => $item->stock >= 5)->count();

    $maxStock = max(1, (int) $visibleProducts->max('stock'));

    $statusOf = fn ($product) => $product->stock <= 0
        ? 'out'
        : ($product->stock < 5 ? 'low' : 'safe');
@endphp

<div class="space-y-6">

    {{-- HEADER --}}
    <x-page-header
        eyebrow="Inventaris"
        title="Data Produk"
        subtitle="Pantau kondisi stok dan kelola seluruh produk gudang dalam satu tempat."
    >
        <a href="{{ route('transactions.create') }}" class="btn btn-secondary">
            <x-icon name="transaction" class="h-4 w-4" />
            Catat Transaksi
        </a>

        <a href="{{ route('products.create') }}" class="btn btn-primary">
            <x-icon name="plus" class="h-4 w-4" stroke="2" />
            Tambah Produk
        </a>
    </x-page-header>

    {{-- RINGKASAN STOK --}}
    <section aria-label="Ringkasan stok" class="card overflow-hidden">
        <div class="grid grid-cols-2 gap-px bg-slate-100 sm:grid-cols-4">
            <x-stat-item
                label="Produk ditampilkan"
                :value="$visibleProducts->count()"
                hint="Jumlah produk pada daftar ini"
                icon="box"
                tone="blue"
            />

            <x-stat-item
                label="Stok aman"
                :value="$safeStock"
                hint="Stok minimal 5 unit"
                icon="check"
                tone="emerald"
            />

            <x-stat-item
                label="Stok menipis"
                :value="$lowStock"
                hint="Stok antara 1 sampai 4 unit"
                icon="alert"
                tone="amber"
            />

            <x-stat-item
                label="Stok habis"
                :value="$outOfStock"
                hint="Produk tanpa stok tersedia"
                icon="x-circle"
                tone="red"
            />
        </div>
    </section>

    {{-- DAFTAR PRODUK --}}
    <section class="card overflow-hidden">

        {{-- JUDUL + PENCARIAN --}}
        <div class="flex flex-col gap-4 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <h2 class="text-base font-semibold tracking-tight text-slate-950">Daftar produk</h2>
                <p class="mt-0.5 text-xs text-slate-500">
                    Kelola stok, harga, dan status seluruh produk gudang.
                </p>
            </div>

            <div class="relative w-full sm:w-72">
                <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" stroke="2" />

                <input type="search"
                       data-product-search
                       placeholder="Cari nama, kode, atau kategori..."
                       aria-label="Cari produk"
                       class="form-control py-2 pl-9 pr-3 text-sm">
            </div>
        </div>

        {{-- FILTER STATUS --}}
        <div class="flex items-center gap-4 overflow-x-auto border-y border-slate-100 px-4 sm:px-6">
            <div class="flex shrink-0 items-center gap-4 sm:gap-5" role="group" aria-label="Filter status stok">
                <button type="button" data-product-filter="all" class="filter-tab filter-tab-active">
                    Semua
                    <span class="filter-count">{{ $visibleProducts->count() }}</span>
                </button>

                <button type="button" data-product-filter="safe" class="filter-tab">
                    Aman
                    <span class="filter-count">{{ $safeStock }}</span>
                </button>

                <button type="button" data-product-filter="low" class="filter-tab">
                    Menipis
                    <span class="filter-count">{{ $lowStock }}</span>
                </button>

                <button type="button" data-product-filter="out" class="filter-tab">
                    Habis
                    <span class="filter-count">{{ $outOfStock }}</span>
                </button>
            </div>
        </div>

        @if($visibleProducts->isEmpty())

            <div class="p-5 sm:p-6">
                <x-empty-state
                    title="Belum ada produk"
                    description="Data inventaris masih kosong. Tambahkan produk pertama untuk mulai mengelola stok gudang."
                    icon="box"
                >
                    <a href="{{ route('products.create') }}" class="btn btn-primary">
                        <x-icon name="plus" class="h-4 w-4" stroke="2" />
                        Tambah Produk
                    </a>
                </x-empty-state>
            </div>

        @else

            {{-- TABEL DESKTOP --}}
            <div class="table-wrap hidden lg:block">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Kategori</th>
                            <th class="text-right">Harga</th>
                            <th>Stok</th>
                            <th>Status</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($products as $product)
                            @php $status = $statusOf($product); @endphp

                            <tr data-product-item
                                data-id="{{ $product->id }}"
                                data-status="{{ $status }}"
                                data-search="{{ $product->name }} {{ $product->code }} {{ $product->category?->name ?? '' }}">

                                <td>
                                    <div class="flex items-center gap-3">
                                        <span class="avatar-initials h-10 w-10 text-[11px]">
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

                                <td>
                                    <span class="inline-flex items-center rounded-md bg-slate-50 px-2 py-1 text-[11px] font-medium text-slate-600 ring-1 ring-inset ring-slate-200">
                                        {{ $product->category?->name ?? 'Tanpa kategori' }}
                                    </span>
                                </td>

                                <td class="text-right">
                                    <span class="whitespace-nowrap text-sm font-semibold text-slate-900 tabular-nums">
                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                    </span>
                                </td>

                                <td>
                                    <div class="w-32">
                                        <div class="flex items-baseline justify-between gap-2">
                                            <span class="text-sm font-semibold tabular-nums {{ $status === 'out' ? 'text-red-600' : ($status === 'low' ? 'text-amber-600' : 'text-slate-900') }}">
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
                                        <x-badge tone="danger" dot>Stok habis</x-badge>
                                    @elseif($status === 'low')
                                        <x-badge tone="warning" dot>Stok menipis</x-badge>
                                    @else
                                        <x-badge tone="success" dot>Stok aman</x-badge>
                                    @endif
                                </td>

                                <td>
                                    <div class="flex items-center justify-end gap-0.5">
                                        <a href="{{ route('products.show', $product->id) }}"
                                           class="btn btn-ghost btn-icon-sm"
                                           title="Lihat detail"
                                           aria-label="Lihat detail {{ $product->name }}">
                                            <x-icon name="eye" class="h-4 w-4" />
                                        </a>

                                        <a href="{{ route('products.edit', $product->id) }}"
                                           class="btn btn-ghost btn-icon-sm"
                                           title="Edit produk"
                                           aria-label="Edit {{ $product->name }}">
                                            <x-icon name="edit" class="h-4 w-4" />
                                        </a>

                                        @if(auth()->user()->role === 'admin')
                                            <form action="{{ route('products.destroy', $product->id) }}"
                                                  method="POST"
                                                  data-confirm="Produk akan dinonaktifkan (soft delete) dan tidak lagi tampil di daftar. Produk dengan stok tersisa tidak dapat dihapus."
                                                  data-confirm-title="Nonaktifkan produk?"
                                                  data-confirm-action="Ya, nonaktifkan">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        title="Nonaktifkan produk"
                                                        aria-label="Nonaktifkan {{ $product->name }}"
                                                        class="btn btn-ghost btn-icon-sm text-slate-400 hover:bg-red-50 hover:text-red-600 focus-visible:ring-red-100">
                                                    <x-icon name="trash" class="h-4 w-4" />
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- KARTU MOBILE / TABLET --}}
            <div class="grid gap-3 p-4 sm:grid-cols-2 lg:hidden">
                @foreach($products as $product)
                    @php $status = $statusOf($product); @endphp

                    <article class="rounded-xl border border-slate-200/80 bg-white p-4 shadow-sm"
                             data-product-item
                             data-id="{{ $product->id }}"
                             data-status="{{ $status }}"
                             data-search="{{ $product->name }} {{ $product->code }} {{ $product->category?->name ?? '' }}">

                        <div class="flex items-start justify-between gap-3">
                            <div class="flex min-w-0 items-center gap-3">
                                <span class="avatar-initials h-9 w-9 text-[10px]">
                                    {{ mb_strtoupper(mb_substr($product->name, 0, 2)) }}
                                </span>

                                <div class="min-w-0">
                                    <a href="{{ route('products.show', $product->id) }}"
                                       class="block truncate text-sm font-semibold text-slate-900">
                                        {{ $product->name }}
                                    </a>
                                    <p class="mt-0.5 font-mono text-[10px] text-slate-400">{{ $product->code }}</p>
                                </div>
                            </div>

                            @if($status === 'out')
                                <x-badge tone="danger" dot>Habis</x-badge>
                            @elseif($status === 'low')
                                <x-badge tone="warning" dot>Menipis</x-badge>
                            @else
                                <x-badge tone="success" dot>Aman</x-badge>
                            @endif
                        </div>

                        <dl class="mt-4 grid grid-cols-2 gap-x-4 gap-y-3 border-t border-slate-100 pt-3">
                            <div>
                                <dt class="text-[10px] font-medium uppercase tracking-wider text-slate-400">Kategori</dt>
                                <dd class="mt-0.5 truncate text-xs font-medium text-slate-700">{{ $product->category?->name ?? 'Tanpa kategori' }}</dd>
                            </div>

                            <div>
                                <dt class="text-[10px] font-medium uppercase tracking-wider text-slate-400">Harga</dt>
                                <dd class="mt-0.5 text-xs font-semibold text-slate-900 tabular-nums">Rp {{ number_format($product->price, 0, ',', '.') }}</dd>
                            </div>

                            <div class="col-span-2">
                                <dt class="text-[10px] font-medium uppercase tracking-wider text-slate-400">Stok tersedia</dt>
                                <dd class="mt-1 flex items-center gap-2">
                                    <span class="text-xs font-semibold tabular-nums {{ $status === 'out' ? 'text-red-600' : ($status === 'low' ? 'text-amber-600' : 'text-slate-900') }}">
                                        {{ number_format($product->stock) }} unit
                                    </span>

                                    <span class="h-1.5 flex-1 overflow-hidden rounded-full bg-slate-100">
                                        <span class="block h-full rounded-full {{ $status === 'out' ? 'bg-red-500' : ($status === 'low' ? 'bg-amber-500' : 'bg-emerald-500') }}"
                                              style="width: {{ $product->stock > 0 ? max(6, round(($product->stock / $maxStock) * 100)) : 0 }}%"></span>
                                    </span>
                                </dd>
                            </div>
                        </dl>

                        <div class="mt-4 flex items-center justify-end gap-1 border-t border-slate-100 pt-3">
                            <a href="{{ route('products.show', $product->id) }}" class="btn btn-secondary btn-sm flex-1">
                                Detail
                            </a>

                            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-secondary btn-sm flex-1">
                                Edit
                            </a>

                            @if(auth()->user()->role === 'admin')
                                <form action="{{ route('products.destroy', $product->id) }}"
                                      method="POST"
                                      data-confirm="Produk akan dinonaktifkan (soft delete) dan tidak lagi tampil di daftar. Produk dengan stok tersisa tidak dapat dihapus."
                                      data-confirm-title="Nonaktifkan produk?"
                                      data-confirm-action="Ya, nonaktifkan">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            title="Nonaktifkan produk"
                                            aria-label="Nonaktifkan {{ $product->name }}"
                                            class="btn btn-danger btn-icon-sm">
                                        <x-icon name="trash" class="h-4 w-4" />
                                    </button>
                                </form>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- HASIL PENCARIAN KOSONG --}}
            <div data-product-empty class="hidden p-5 sm:p-6">
                <div class="rounded-xl border border-dashed border-slate-300 px-6 py-12 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-slate-400">
                        <x-icon name="search" class="h-6 w-6" />
                    </div>

                    <h3 class="mt-4 text-sm font-semibold text-slate-900">Produk tidak ditemukan</h3>
                    <p class="mt-1 text-xs leading-5 text-slate-500">Coba kata kunci lain atau ubah filter status stok.</p>

                    <button type="button" data-product-reset class="btn btn-secondary btn-sm mt-5">
                        Reset pencarian
                    </button>
                </div>
            </div>

            {{-- FOOTER TABEL --}}
            <div class="flex flex-wrap items-center justify-between gap-2 border-t border-slate-100 bg-slate-50/60 px-5 py-3.5 sm:px-6">
                <p class="text-[11px] text-slate-500">
                    <span data-product-count>{{ $visibleProducts->count() }}</span> produk ditampilkan
                </p>

                <p class="text-[11px] text-slate-400">Ambang batas stok menipis: di bawah 5 unit.</p>
            </div>

        @endif
    </section>
</div>
@endsection

@push('scripts')
<script>
    // Pencarian dan filter berjalan di browser saja (tanpa mengubah backend).
    (function () {
        const searchInput = document.querySelector('[data-product-search]');
        const filterButtons = Array.from(document.querySelectorAll('[data-product-filter]'));
        const items = Array.from(document.querySelectorAll('[data-product-item]'));
        const countElements = Array.from(document.querySelectorAll('[data-product-count]'));
        const emptyElement = document.querySelector('[data-product-empty]');
        const resetButton = document.querySelector('[data-product-reset]');

        if (!items.length) return;

        let activeFilter = 'all';

        function syncButtons() {
            filterButtons.forEach((button) => {
                button.classList.toggle('filter-tab-active', button.dataset.productFilter === activeFilter);
            });
        }

        function applyFilters() {
            const keyword = (searchInput?.value || '').toLowerCase().trim();

            // Set dipakai agar baris tabel dan kartu mobile untuk produk yang sama
            // tidak dihitung dua kali.
            const visibleIds = new Set();

            items.forEach((item) => {
                const matchesKeyword = keyword === '' || (item.dataset.search || '').toLowerCase().includes(keyword);
                const matchesStatus = activeFilter === 'all' || item.dataset.status === activeFilter;
                const shouldShow = matchesKeyword && matchesStatus;

                item.classList.toggle('hidden', !shouldShow);

                if (shouldShow) visibleIds.add(item.dataset.id || item.dataset.search);
            });

            const visible = visibleIds.size;

            countElements.forEach((element) => {
                element.textContent = visible;
            });

            emptyElement?.classList.toggle('hidden', visible > 0);
        }

        filterButtons.forEach((button) => {
            button.addEventListener('click', () => {
                activeFilter = button.dataset.productFilter || 'all';
                syncButtons();
                applyFilters();
            });
        });

        searchInput?.addEventListener('input', applyFilters);

        resetButton?.addEventListener('click', () => {
            activeFilter = 'all';
            if (searchInput) searchInput.value = '';
            syncButtons();
            applyFilters();
            searchInput?.focus();
        });

        syncButtons();
        applyFilters();
    })();
</script>
@endpush
