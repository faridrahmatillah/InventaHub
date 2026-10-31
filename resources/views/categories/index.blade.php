@extends('layouts.app')

@section('title', 'Kategori Produk')

@section('content')

@php
    $totalCategories = $categories->count();
    $totalGroupedProducts = $categories->sum('products_count');
    $emptyCategories = $categories->where('products_count', 0)->count();
@endphp

<div class="space-y-6">

    {{-- HEADER --}}
    <x-page-header
        eyebrow="Pengelolaan"
        title="Kategori Produk"
        subtitle="Kelola kategori untuk mengelompokkan produk gudang agar data lebih rapi."
    >
        <a href="{{ route('products.index') }}" class="btn btn-secondary">
            <x-icon name="box" class="h-4 w-4" />
            Data Produk
        </a>

        <a href="{{ route('categories.create') }}" class="btn btn-primary">
            <x-icon name="plus" class="h-4 w-4" stroke="2" />
            Tambah Kategori
        </a>
    </x-page-header>

    {{-- RINGKASAN --}}
    <section aria-label="Ringkasan kategori" class="card overflow-hidden">
        <div class="grid grid-cols-2 gap-px bg-slate-100 sm:grid-cols-3">
            <x-stat-item
                label="Total kategori"
                :value="$totalCategories"
                hint="Kategori aktif terdaftar"
                icon="category"
                tone="blue"
            />

            <x-stat-item
                label="Produk terkelompok"
                :value="$totalGroupedProducts"
                hint="Produk aktif dalam kategori"
                icon="box"
                tone="emerald"
            />

            <x-stat-item
                label="Kategori kosong"
                :value="$emptyCategories"
                hint="Belum memiliki produk aktif"
                icon="alert"
                tone="amber"
                class="col-span-2 sm:col-span-1"
            />
        </div>
    </section>

    {{-- DAFTAR KATEGORI --}}
    <section class="card overflow-hidden">

        <div class="flex flex-col gap-4 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <h2 class="text-base font-semibold tracking-tight text-slate-950">Daftar kategori</h2>
                <p class="mt-0.5 text-xs text-slate-500">
                    <span data-category-count>{{ $totalCategories }}</span> kategori aktif
                </p>
            </div>

            <div class="relative w-full sm:w-72">
                <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" stroke="2" />

                <input type="search"
                       data-category-search
                       placeholder="Cari kategori..."
                       aria-label="Cari kategori"
                       class="form-control py-2 pl-9 pr-3 text-sm">
            </div>
        </div>

        @if($categories->isEmpty())

            <div class="border-t border-slate-100 p-5 sm:p-6">
                <x-empty-state
                    title="Belum ada kategori"
                    description="Tambahkan kategori pertama agar produk dapat dikelompokkan dengan rapi."
                    icon="category"
                >
                    <a href="{{ route('categories.create') }}" class="btn btn-primary">
                        <x-icon name="plus" class="h-4 w-4" stroke="2" />
                        Tambah Kategori
                    </a>
                </x-empty-state>
            </div>

        @else

            {{-- TABEL DESKTOP --}}
            <div class="table-wrap hidden border-t border-slate-100 lg:block">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Kategori</th>
                            <th>Deskripsi</th>
                            <th>Produk</th>
                            <th>Dibuat</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($categories as $category)
                            <tr data-category-item data-id="{{ $category->id }}" data-search="{{ $category->name }} {{ $category->description }}">
                                <td>
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                                            <x-icon name="category" class="h-4 w-4" />
                                        </span>

                                        <div class="min-w-0">
                                            <a href="{{ route('categories.show', $category) }}"
                                               class="block truncate text-sm font-semibold text-slate-900 transition hover:text-emerald-700">
                                                {{ $category->name }}
                                            </a>
                                            <p class="mt-0.5 text-[10px] font-medium text-slate-400">Kategori #{{ $category->id }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="max-w-md">
                                    <p class="truncate text-[13px] text-slate-500" title="{{ $category->description }}">
                                        {{ $category->description ?: 'Tidak ada deskripsi' }}
                                    </p>
                                </td>

                                <td>
                                    <span class="inline-flex items-center gap-1.5 rounded-md bg-slate-50 px-2 py-1 text-[11px] font-semibold text-slate-600 ring-1 ring-inset ring-slate-200 tabular-nums">
                                        {{ $category->products_count }} produk
                                    </span>
                                </td>

                                <td>
                                    <span class="whitespace-nowrap text-[13px] text-slate-500">
                                        <x-date :value="$category->created_at" />
                                    </span>
                                </td>

                                <td>
                                    <div class="flex items-center justify-end gap-0.5">
                                        <a href="{{ route('categories.show', $category) }}"
                                           class="btn btn-ghost btn-icon-sm"
                                           title="Lihat detail"
                                           aria-label="Lihat detail {{ $category->name }}">
                                            <x-icon name="eye" class="h-4 w-4" />
                                        </a>

                                        <a href="{{ route('categories.edit', $category) }}"
                                           class="btn btn-ghost btn-icon-sm"
                                           title="Edit kategori"
                                           aria-label="Edit {{ $category->name }}">
                                            <x-icon name="edit" class="h-4 w-4" />
                                        </a>

                                        @if($category->products_count > 0)
                                            <button type="button" disabled
                                                    title="Kategori masih memiliki produk aktif"
                                                    class="btn btn-ghost btn-icon-sm cursor-not-allowed text-slate-300">
                                                <x-icon name="trash" class="h-4 w-4" />
                                                <span class="sr-only">Tidak dapat menghapus {{ $category->name }}</span>
                                            </button>
                                        @else
                                            <form action="{{ route('categories.destroy', $category) }}"
                                                  method="POST"
                                                  data-confirm="Kategori akan dinonaktifkan (soft delete) dan tidak lagi tampil di daftar."
                                                  data-confirm-title="Hapus kategori?"
                                                  data-confirm-action="Ya, hapus">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        title="Hapus kategori"
                                                        aria-label="Hapus {{ $category->name }}"
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
            <div class="grid gap-3 border-t border-slate-100 p-4 sm:grid-cols-2 lg:hidden">
                @foreach ($categories as $category)
                    <article class="rounded-xl border border-slate-200/80 bg-white p-4 shadow-sm"
                             data-category-item
                             data-id="{{ $category->id }}"
                             data-search="{{ $category->name }} {{ $category->description }}">

                        <div class="flex items-start justify-between gap-3">
                            <div class="flex min-w-0 items-start gap-3">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700">
                                    <x-icon name="category" class="h-4 w-4" />
                                </span>

                                <div class="min-w-0">
                                    <a href="{{ route('categories.show', $category) }}"
                                       class="block truncate text-sm font-semibold text-slate-900">
                                        {{ $category->name }}
                                    </a>
                                    <p class="mt-0.5 text-[10px] text-slate-400">Ditambahkan <x-date :value="$category->created_at" /></p>
                                </div>
                            </div>

                            <x-badge tone="neutral">{{ $category->products_count }} produk</x-badge>
                        </div>

                        <p class="mt-3 line-clamp-2 text-xs leading-5 text-slate-500">
                            {{ $category->description ?: 'Tidak ada deskripsi' }}
                        </p>

                        <div class="mt-3 flex items-center justify-end gap-1 border-t border-slate-100 pt-3">
                            <a href="{{ route('categories.show', $category) }}" class="btn btn-secondary btn-sm flex-1">
                                Detail
                            </a>

                            <a href="{{ route('categories.edit', $category) }}" class="btn btn-secondary btn-sm flex-1">
                                Edit
                            </a>

                            @if($category->products_count > 0)
                                <button type="button" disabled
                                        title="Kategori masih memiliki produk aktif"
                                        class="btn btn-ghost btn-icon-sm cursor-not-allowed text-slate-300">
                                    <x-icon name="trash" class="h-4 w-4" />
                                    <span class="sr-only">Tidak dapat menghapus {{ $category->name }}</span>
                                </button>
                            @else
                                <form action="{{ route('categories.destroy', $category) }}"
                                      method="POST"
                                      data-confirm="Kategori akan dinonaktifkan (soft delete) dan tidak lagi tampil di daftar."
                                      data-confirm-title="Hapus kategori?"
                                      data-confirm-action="Ya, hapus">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            title="Hapus kategori"
                                            aria-label="Hapus {{ $category->name }}"
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
            <div data-category-empty class="hidden p-5 sm:p-6">
                <div class="rounded-xl border border-dashed border-slate-300 px-6 py-12 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-slate-400">
                        <x-icon name="search" class="h-6 w-6" />
                    </div>

                    <h3 class="mt-4 text-sm font-semibold text-slate-900">Kategori tidak ditemukan</h3>
                    <p class="mt-1 text-xs leading-5 text-slate-500">Coba gunakan kata kunci lain.</p>
                </div>
            </div>

            {{-- FOOTER --}}
            <div class="flex flex-wrap items-center justify-between gap-2 border-t border-slate-100 bg-slate-50/60 px-5 py-3.5 sm:px-6">
                <p class="text-[11px] text-slate-500">
                    <span data-category-count>{{ $totalCategories }}</span> kategori ditampilkan
                </p>

                <p class="text-[11px] text-slate-400">Kategori yang masih memiliki produk aktif tidak dapat dihapus.</p>
            </div>

        @endif
    </section>
</div>
@endsection

@push('scripts')
<script>
    // Pencarian kategori berjalan di browser saja.
    (function () {
        const searchInput = document.querySelector('[data-category-search]');
        const items = Array.from(document.querySelectorAll('[data-category-item]'));
        const countElements = Array.from(document.querySelectorAll('[data-category-count]'));
        const emptyElement = document.querySelector('[data-category-empty]');

        if (!searchInput || !items.length) return;

        searchInput.addEventListener('input', () => {
            const keyword = searchInput.value.toLowerCase().trim();

            // Set dipakai agar baris tabel dan kartu mobile untuk kategori yang sama
            // tidak dihitung dua kali.
            const visibleIds = new Set();

            items.forEach((item) => {
                const matches = keyword === '' || (item.dataset.search || '').toLowerCase().includes(keyword);
                item.classList.toggle('hidden', !matches);

                if (matches) visibleIds.add(item.dataset.id || item.dataset.search);
            });

            const visible = visibleIds.size;

            countElements.forEach((element) => {
                element.textContent = visible;
            });

            emptyElement?.classList.toggle('hidden', visible > 0);
        });
    })();
</script>
@endpush
