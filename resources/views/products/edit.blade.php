@extends('layouts.app')

@section('title', 'Edit Produk')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">

    {{-- HEADER --}}
    <x-page-header
        title="Edit Produk"
        subtitle="Perbarui informasi produk yang sudah terdaftar."
        :breadcrumbs="[
            ['label' => 'Data Produk', 'url' => route('products.index')],
            ['label' => $product->name, 'url' => route('products.show', $product->id)],
            ['label' => 'Edit Produk'],
        ]"
    >
        <a href="{{ route('products.show', $product->id) }}" class="btn btn-secondary">
            <x-icon name="arrow-left" class="h-4 w-4" stroke="2" />
            Kembali
        </a>
    </x-page-header>

    <form action="{{ route('products.update', $product->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="space-y-5">

            {{-- FORM --}}
            <div class="card divide-y divide-slate-100 overflow-hidden">

                <x-form-section
                    step="01"
                    title="Identitas produk"
                    description="Perbarui nama, kode, atau kategori yang digunakan untuk mengenali produk."
                    icon="box"
                    tone="amber"
                >
                    <div class="space-y-5">
                        <x-input
                            name="category_id"
                            type="select"
                            label="Kategori Barang"
                            required
                            :margin="false"
                        >
                            <option value="">Pilih kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </x-input>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <x-input
                                name="code"
                                label="Kode Barang / SKU"
                                :value="old('code', $product->code)"
                                required
                                :margin="false"
                            />

                            <x-input
                                name="name"
                                label="Nama Barang"
                                :value="old('name', $product->name)"
                                required
                                :margin="false"
                            />
                        </div>
                    </div>
                </x-form-section>

                <x-form-section
                    step="02"
                    title="Harga dan stok"
                    description="Stok dikunci di halaman ini agar riwayat pergerakan barang tetap konsisten."
                    icon="chart"
                    tone="emerald"
                >
                    <div class="space-y-5">
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <span class="form-label">Jumlah Stok Saat Ini</span>
                                <p class="form-control bg-slate-100 font-semibold text-slate-500">
                                    {{ number_format($product->stock) }}
                                </p>
                                <p class="form-hint">Stok hanya dapat berubah melalui transaksi atau opname.</p>
                            </div>

                            <x-input
                                name="price"
                                type="number"
                                label="Harga Barang (Rp)"
                                :value="old('price', $product->price)"
                                min="0"
                                step="0.01"
                                hint="Masukkan angka tanpa titik atau koma pemisah ribuan."
                                required
                                :margin="false"
                            />
                        </div>

                        <div class="info-note">
                            <x-icon name="info" class="mt-0.5 h-4 w-4 shrink-0" />
                            <p>
                                Perubahan stok tidak dilakukan dari halaman ini. Gunakan menu transaksi agar riwayat inventaris tetap akurat.
                            </p>
                        </div>
                    </div>
                </x-form-section>

                <x-form-section
                    step="03"
                    title="Deskripsi produk"
                    description="Perbarui informasi pendukung produk bila diperlukan."
                    icon="edit"
                    tone="slate"
                >
                    <x-input
                        name="description"
                        type="textarea"
                        label="Deskripsi Produk (Opsional)"
                        :value="old('description', $product->description)"
                        placeholder="Tuliskan informasi tambahan mengenai produk..."
                        rows="4"
                        :margin="false"
                    />
                </x-form-section>

                {{-- FOOTER AKSI --}}
                <div class="flex flex-col-reverse gap-3 bg-slate-50/70 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <p class="text-[11px] leading-5 text-slate-500">
                        Perubahan langsung terlihat pada detail produk.
                    </p>

                    <div class="flex flex-col-reverse gap-2.5 sm:flex-row">
                        <a href="{{ route('products.show', $product->id) }}" class="btn btn-secondary">
                            Batalkan
                        </a>

                        <button type="submit" data-loading-text="Menyimpan..." class="btn btn-primary">
                            <x-icon name="check" class="h-4 w-4" stroke="2" />
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>

            {{-- INFORMASI PENDUKUNG --}}
            <aside class="grid gap-5 md:grid-cols-2">

                <section class="card overflow-hidden" aria-label="Ringkasan produk">
                    <div class="px-5 py-4">
                        <h2 class="section-title">Ringkasan produk</h2>
                        <p class="section-subtitle">Data yang tersimpan saat ini.</p>
                    </div>

                    <div class="border-t border-slate-100 p-5">
                        <div class="flex items-center gap-3">
                            <span class="avatar-initials h-11 w-11 text-xs">
                                {{ mb_strtoupper(mb_substr($product->name, 0, 2)) }}
                            </span>

                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-slate-900">{{ $product->name }}</p>
                                <p class="mt-0.5 font-mono text-[10px] font-medium text-slate-400">{{ $product->code }}</p>
                            </div>
                        </div>

                        <dl class="mt-5 space-y-3 border-t border-slate-100 pt-4 text-xs">
                            <div class="flex items-center justify-between gap-3">
                                <dt class="text-slate-500">Kategori</dt>
                                <dd class="max-w-[160px] truncate text-right font-semibold text-slate-800">{{ $product->category?->name ?? '-' }}</dd>
                            </div>

                            <div class="flex items-center justify-between gap-3">
                                <dt class="text-slate-500">Stok saat ini</dt>
                                <dd class="text-right font-semibold {{ $product->stock <= 0 ? 'text-red-600' : ($product->stock < 5 ? 'text-amber-600' : 'text-emerald-600') }}">
                                    {{ number_format($product->stock) }} unit
                                </dd>
                            </div>

                            <div class="flex items-center justify-between gap-3">
                                <dt class="text-slate-500">Harga</dt>
                                <dd class="text-right font-semibold text-slate-800 tabular-nums">Rp {{ number_format($product->price, 0, ',', '.') }}</dd>
                            </div>

                            <div class="flex items-center justify-between gap-3">
                                <dt class="text-slate-500">Terakhir diubah</dt>
                                <dd class="text-right font-semibold text-slate-800">
                                    <x-date :value="$product->updated_at" />
                                </dd>
                            </div>
                        </dl>

                        <a href="{{ route('products.show', $product->id) }}" class="btn btn-secondary btn-sm mt-5 w-full">
                            Lihat detail produk
                            <x-icon name="chevron-right" class="h-3.5 w-3.5" stroke="2" />
                        </a>
                    </div>
                </section>

                <div class="warning-note">
                    <x-icon name="alert" class="mt-0.5 h-4 w-4 shrink-0" />
                    <p>
                        Perubahan nama kategori akan langsung terlihat pada produk yang menggunakannya.
                    </p>
                </div>
            </aside>
        </div>
    </form>
</div>
@endsection
