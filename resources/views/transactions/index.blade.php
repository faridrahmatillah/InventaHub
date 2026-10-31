@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')

@php
    $totalIn = $transactions->where('type', 'in')->sum('quantity');
    $totalOut = $transactions->where('type', 'out')->sum('quantity');
    $inCount = $transactions->where('type', 'in')->count();
    $outCount = $transactions->where('type', 'out')->count();
    $isAdmin = auth()->user()->role === 'admin';
@endphp

<div class="space-y-6">

    {{-- HEADER --}}
    <x-page-header
        eyebrow="Inventaris"
        title="Riwayat Transaksi"
        subtitle="Pantau seluruh aktivitas barang masuk dan keluar beserta perubahan stoknya."
    >
        <a href="{{ route('transactions.create') }}" class="btn btn-primary">
            <x-icon name="plus" class="h-4 w-4" stroke="2" />
            Tambah Transaksi
        </a>
    </x-page-header>

    {{-- RINGKASAN --}}
    <section aria-label="Ringkasan transaksi" class="card overflow-hidden">
        <div class="grid grid-cols-2 gap-px bg-slate-100 sm:grid-cols-3">
            <x-stat-item
                label="Total transaksi"
                :value="number_format($transactions->count())"
                hint="Catatan pada daftar ini"
                icon="transaction"
                tone="blue"
            />

            <x-stat-item
                label="Barang masuk"
                :value="number_format($totalIn) . ' unit'"
                hint="Akumulasi jumlah barang masuk"
                icon="arrow-down"
                tone="emerald"
            />

            <x-stat-item
                label="Barang keluar"
                :value="number_format($totalOut) . ' unit'"
                hint="Akumulasi jumlah barang keluar"
                icon="arrow-up"
                tone="red"
                class="col-span-2 sm:col-span-1"
            />
        </div>
    </section>

    {{-- DAFTAR TRANSAKSI --}}
    <section class="card overflow-hidden">

        <div class="flex flex-col gap-4 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <h2 class="text-base font-semibold tracking-tight text-slate-950">Daftar transaksi</h2>
                <p class="mt-0.5 text-xs text-slate-500">
                    <span data-transaction-count>{{ $transactions->count() }}</span> transaksi ditampilkan
                </p>
            </div>

            <div class="relative w-full sm:w-72">
                <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" stroke="2" />

                <input type="search"
                       data-transaction-search
                       placeholder="Cari produk atau keterangan..."
                       aria-label="Cari transaksi"
                       class="form-control py-2 pl-9 pr-3 text-sm">
            </div>
        </div>

        {{-- FILTER JENIS --}}
        <div class="flex items-center gap-4 overflow-x-auto border-y border-slate-100 px-4 sm:px-6">
            <div class="flex shrink-0 items-center gap-4 sm:gap-5" role="group" aria-label="Filter jenis transaksi">
                <button type="button" data-transaction-filter="all" class="filter-tab filter-tab-active">
                    Semua
                    <span class="filter-count">{{ $transactions->count() }}</span>
                </button>

                <button type="button" data-transaction-filter="in" class="filter-tab">
                    Masuk
                    <span class="filter-count">{{ $inCount }}</span>
                </button>

                <button type="button" data-transaction-filter="out" class="filter-tab">
                    Keluar
                    <span class="filter-count">{{ $outCount }}</span>
                </button>
            </div>
        </div>

        @if($transactions->isEmpty())

            <div class="p-5 sm:p-6">
                <x-empty-state
                    title="Belum ada transaksi"
                    description="Transaksi barang masuk dan keluar yang Anda catat akan muncul di halaman ini."
                    icon="transaction"
                >
                    <a href="{{ route('transactions.create') }}" class="btn btn-primary">
                        <x-icon name="plus" class="h-4 w-4" stroke="2" />
                        Catat Transaksi
                    </a>
                </x-empty-state>
            </div>

        @else

            {{-- TABEL DESKTOP --}}
            <div class="table-wrap hidden lg:block">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Produk</th>
                            <th>Jenis</th>
                            <th class="text-right">Jumlah</th>
                            <th>Keterangan</th>
                            @if($isAdmin)
                                <th class="text-right">Aksi</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($transactions as $transaction)
                            <tr data-transaction-item
                                data-id="{{ $transaction->id }}"
                                data-type="{{ $transaction->type }}"
                                data-search="{{ $transaction->product?->name ?? '' }} {{ $transaction->product?->code ?? '' }} {{ $transaction->supplier?->name ?? '' }} {{ $transaction->description }}">

                                <td class="whitespace-nowrap">
                                    <p class="text-[13px] font-semibold text-slate-800">
                                        <x-date :value="$transaction->transaction_date" />
                                    </p>
                                    <p class="mt-0.5 text-[10px] text-slate-400">Tanggal transaksi</p>
                                </td>

                                <td>
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $transaction->type === 'in' ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600' }}">
                                            <x-icon :name="$transaction->type === 'in' ? 'arrow-down' : 'arrow-up'" class="h-4 w-4" stroke="2" />
                                        </span>

                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-semibold text-slate-900">{{ $transaction->product?->name ?? 'Produk dihapus' }}</p>
                                            <p class="mt-0.5 font-mono text-[10px] font-medium text-slate-400">{{ $transaction->product?->code ?? '-' }}</p>
                                            @if($transaction->supplier)
                                                <p class="mt-0.5 truncate text-[10px] text-slate-500">Pemasok: {{ $transaction->supplier->name }}</p>
                                            @endif
                                            <p class="mt-0.5 truncate text-[10px] text-slate-400">Dicatat oleh: {{ $transaction->user?->name ?? 'Pengguna tidak tersedia' }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    @if($transaction->type === 'in')
                                        <x-badge tone="success" dot>Barang masuk</x-badge>
                                    @else
                                        <x-badge tone="danger" dot>Barang keluar</x-badge>
                                    @endif
                                </td>

                                <td class="text-right">
                                    <span class="whitespace-nowrap text-sm font-semibold tabular-nums {{ $transaction->type === 'in' ? 'text-emerald-600' : 'text-red-600' }}">
                                        {{ $transaction->type === 'in' ? '+' : '-' }}{{ number_format($transaction->quantity) }}
                                    </span>
                                    <span class="text-[10px] text-slate-400">unit</span>
                                </td>

                                <td class="max-w-xs">
                                    <p class="truncate text-[13px] text-slate-500" title="{{ $transaction->description }}">
                                        {{ $transaction->description ?: '—' }}
                                    </p>
                                </td>

                                @if($isAdmin)
                                    <td class="text-right">
                                        <form action="{{ route('transactions.destroy', $transaction) }}"
                                              method="POST"
                                              data-confirm="Catatan transaksi akan dihapus permanen. Stok produk tidak otomatis dikembalikan setelah transaksi dihapus."
                                              data-confirm-title="Hapus transaksi?"
                                              data-confirm-action="Ya, hapus">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    title="Hapus transaksi"
                                                    aria-label="Hapus transaksi {{ $transaction->product?->name ?? '' }}"
                                                    class="btn btn-ghost btn-icon-sm text-slate-400 hover:bg-red-50 hover:text-red-600 focus-visible:ring-red-100">
                                                <x-icon name="trash" class="h-4 w-4" />
                                            </button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- KARTU MOBILE / TABLET --}}
            <div class="grid gap-3 p-4 sm:grid-cols-2 lg:hidden">
                @foreach ($transactions as $transaction)
                    <article class="rounded-xl border border-slate-200/80 bg-white p-4 shadow-sm"
                             data-transaction-item
                             data-id="{{ $transaction->id }}"
                             data-type="{{ $transaction->type }}"
                             data-search="{{ $transaction->product?->name ?? '' }} {{ $transaction->product?->code ?? '' }} {{ $transaction->supplier?->name ?? '' }} {{ $transaction->description }}">

                        <div class="flex items-start justify-between gap-3">
                            <div class="flex min-w-0 items-start gap-3">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg {{ $transaction->type === 'in' ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600' }}">
                                    <x-icon :name="$transaction->type === 'in' ? 'arrow-down' : 'arrow-up'" class="h-4 w-4" stroke="2" />
                                </span>

                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-slate-900">{{ $transaction->product?->name ?? 'Produk dihapus' }}</p>
                                    <p class="mt-0.5 font-mono text-[10px] text-slate-400">{{ $transaction->product?->code ?? '-' }}</p>
                                    @if($transaction->supplier)
                                        <p class="mt-0.5 truncate text-[10px] text-slate-500">Pemasok: {{ $transaction->supplier->name }}</p>
                                    @endif
                                    <p class="mt-0.5 truncate text-[10px] text-slate-400">Dicatat oleh: {{ $transaction->user?->name ?? 'Pengguna tidak tersedia' }}</p>
                                </div>
                            </div>

                            @if($transaction->type === 'in')
                                <x-badge tone="success" dot>Masuk</x-badge>
                            @else
                                <x-badge tone="danger" dot>Keluar</x-badge>
                            @endif
                        </div>

                        <div class="mt-3 flex items-center justify-between gap-3 border-t border-slate-100 pt-3">
                            <div>
                                <p class="text-[10px] font-medium uppercase tracking-wider text-slate-400">Tanggal</p>
                                <p class="mt-0.5 text-xs font-medium text-slate-600"><x-date :value="$transaction->transaction_date" /></p>
                            </div>

                            <div class="text-right">
                                <p class="text-[10px] font-medium uppercase tracking-wider text-slate-400">Jumlah</p>
                                <p class="mt-0.5 text-sm font-semibold tabular-nums {{ $transaction->type === 'in' ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ $transaction->type === 'in' ? '+' : '-' }}{{ number_format($transaction->quantity) }} unit
                                </p>
                            </div>
                        </div>

                        @if($transaction->description)
                            <p class="mt-3 line-clamp-2 text-[11px] leading-5 text-slate-500">
                                {{ $transaction->description }}
                            </p>
                        @endif

                        @if($isAdmin)
                            <div class="mt-3 flex justify-end border-t border-slate-100 pt-3">
                                <form action="{{ route('transactions.destroy', $transaction) }}"
                                      method="POST"
                                      data-confirm="Catatan transaksi akan dihapus permanen. Stok produk tidak otomatis dikembalikan setelah transaksi dihapus."
                                      data-confirm-title="Hapus transaksi?"
                                      data-confirm-action="Ya, hapus">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            title="Hapus transaksi"
                                            aria-label="Hapus transaksi {{ $transaction->product?->name ?? '' }}"
                                            class="btn btn-danger btn-icon-sm">
                                        <x-icon name="trash" class="h-4 w-4" />
                                    </button>
                                </form>
                            </div>
                        @endif
                    </article>
                @endforeach
            </div>

            @if($transactions->hasPages())
                <div class="flex flex-col gap-3 border-t border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <p class="text-xs text-slate-500">
                        Menampilkan
                        <span class="font-semibold text-slate-700">{{ $transactions->firstItem() }}</span>
                        sampai
                        <span class="font-semibold text-slate-700">{{ $transactions->lastItem() }}</span>
                        dari
                        <span class="font-semibold text-slate-700">{{ $transactions->total() }}</span>
                        transaksi
                    </p>

                    <div class="self-end sm:self-auto">
                        {{ $transactions->links() }}
                    </div>
                </div>
            @endif

            {{-- HASIL FILTER KOSONG --}}
            <div data-transaction-empty class="hidden p-5 sm:p-6">
                <div class="rounded-xl border border-dashed border-slate-300 px-6 py-12 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-slate-400">
                        <x-icon name="search" class="h-6 w-6" />
                    </div>

                    <h3 class="mt-4 text-sm font-semibold text-slate-900">Transaksi tidak ditemukan</h3>
                    <p class="mt-1 text-xs leading-5 text-slate-500">Coba kata kunci lain atau ubah filter jenis transaksi.</p>
                </div>
            </div>

            {{-- FOOTER --}}
            <div class="flex flex-wrap items-center justify-between gap-2 border-t border-slate-100 bg-slate-50/60 px-5 py-3.5 sm:px-6">
                <p class="text-[11px] text-slate-500">
                    <span data-transaction-count>{{ $transactions->count() }}</span> transaksi ditampilkan
                </p>

                <p class="text-[11px] text-slate-400">Stok diperbarui otomatis saat transaksi disimpan.</p>
            </div>

        @endif
    </section>
</div>
@endsection

@push('scripts')
<script>
    // Pencarian dan filter jenis transaksi berjalan di browser saja.
    (function () {
        const searchInput = document.querySelector('[data-transaction-search]');
        const filterButtons = Array.from(document.querySelectorAll('[data-transaction-filter]'));
        const items = Array.from(document.querySelectorAll('[data-transaction-item]'));
        const countElements = Array.from(document.querySelectorAll('[data-transaction-count]'));
        const emptyElement = document.querySelector('[data-transaction-empty]');

        if (!items.length) return;

        let activeFilter = 'all';

        function syncButtons() {
            filterButtons.forEach((button) => {
                button.classList.toggle('filter-tab-active', button.dataset.transactionFilter === activeFilter);
            });
        }

        function applyFilters() {
            const keyword = (searchInput?.value || '').toLowerCase().trim();

            // Set dipakai agar baris tabel dan kartu mobile untuk transaksi yang sama
            // tidak dihitung dua kali.
            const visibleIds = new Set();

            items.forEach((item) => {
                const matchesKeyword = keyword === '' || (item.dataset.search || '').toLowerCase().includes(keyword);
                const matchesType = activeFilter === 'all' || item.dataset.type === activeFilter;
                const shouldShow = matchesKeyword && matchesType;

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
                activeFilter = button.dataset.transactionFilter || 'all';
                syncButtons();
                applyFilters();
            });
        });

        searchInput?.addEventListener('input', applyFilters);

        syncButtons();
        applyFilters();
    })();
</script>
@endpush
