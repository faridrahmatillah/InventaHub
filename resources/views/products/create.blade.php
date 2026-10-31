@extends('layouts.app')

@section('title', 'Tambah Produk')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">

    {{-- HEADER --}}
    <x-page-header
        title="Tambah Produk"
        subtitle="Daftarkan produk baru ke inventaris gudang."
        :breadcrumbs="[
            ['label' => 'Data Produk', 'url' => route('products.index')],
            ['label' => 'Tambah Produk'],
        ]"
    >
        <a href="{{ route('products.index') }}" class="btn btn-secondary">
            <x-icon name="arrow-left" class="h-4 w-4" stroke="2" />
            Kembali
        </a>
    </x-page-header>

    <form action="{{ route('products.store') }}" method="POST">
        @csrf

        <div class="space-y-5">

            {{-- FORM --}}
            <div class="card divide-y divide-slate-100 overflow-hidden">

                <x-form-section
                    step="01"
                    title="Identitas produk"
                    description="Nama, kode, dan kategori digunakan untuk mengenali produk di seluruh halaman inventaris."
                    icon="box"
                    tone="blue"
                >
                    <div class="space-y-5">
                        <x-input
                            name="category_id"
                            type="select"
                            label="Kategori Barang"
                            data-preview-category
                            required
                            :margin="false"
                        >
                            <option value="">Pilih kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </x-input>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <x-input
                                name="code"
                                label="Kode Barang / SKU"
                                placeholder="Contoh: BRG-001"
                                hint="Gunakan kode unik untuk setiap produk."
                                data-preview-code
                                required
                                :margin="false"
                            />

                            <x-input
                                name="name"
                                label="Nama Barang"
                                placeholder="Contoh: Sepatu Sneaker"
                                data-preview-name
                                required
                                :margin="false"
                            />
                        </div>
                    </div>
                </x-form-section>

                <x-form-section
                    step="02"
                    title="Harga dan stok awal"
                    description="Stok awal hanya diisi saat produk pertama kali didaftarkan. Perubahan berikutnya melalui transaksi."
                    icon="chart"
                    tone="emerald"
                >
                    <div class="space-y-5">
                        <div class="grid gap-5 sm:grid-cols-2">
                            <x-input
                                name="stock"
                                type="number"
                                label="Jumlah Stok Awal"
                                :value="old('stock', 0)"
                                min="0"
                                hint="Stok awal diisi sekali saat produk didaftarkan."
                                data-preview-stock
                                required
                                :margin="false"
                            />

                            <x-input
                                name="price"
                                type="number"
                                label="Harga Barang (Rp)"
                                placeholder="150000"
                                min="0"
                                step="0.01"
                                hint="Masukkan angka tanpa titik atau koma pemisah ribuan."
                                data-preview-price
                                required
                                :margin="false"
                            />
                        </div>

                        <div class="info-note">
                            <x-icon name="info" class="mt-0.5 h-4 w-4 shrink-0" />
                            <p>
                                Stok di atas adalah jumlah awal saat produk didaftarkan. Perubahan stok berikutnya dilakukan melalui transaksi gudang.
                            </p>
                        </div>
                    </div>
                </x-form-section>

                <x-form-section
                    step="03"
                    title="Deskripsi produk"
                    description="Tambahkan informasi pendukung seperti bahan, ukuran, atau catatan penyimpanan."
                    icon="edit"
                    tone="slate"
                >
                    <x-input
                        name="description"
                        type="textarea"
                        label="Deskripsi Produk (Opsional)"
                        placeholder="Tuliskan informasi tambahan mengenai produk..."
                        rows="4"
                        :margin="false"
                    />
                </x-form-section>

                {{-- FOOTER AKSI --}}
                <div class="flex flex-col-reverse gap-3 bg-slate-50/70 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <p class="text-[11px] leading-5 text-slate-500">
                        Produk langsung tersedia setelah data disimpan.
                    </p>

                    <div class="flex flex-col-reverse gap-2.5 sm:flex-row">
                        <a href="{{ route('products.index') }}" class="btn btn-secondary">
                            Batalkan
                        </a>

                        <button type="submit" data-loading-text="Menyimpan..." class="btn btn-primary">
                            <x-icon name="check" class="h-4 w-4" stroke="2" />
                            Simpan Produk
                        </button>
                    </div>
                </div>
            </div>

            {{-- INFORMASI PENDUKUNG --}}
            <aside class="grid gap-5 md:grid-cols-2">

                <section class="card overflow-hidden" aria-label="Pratinjau produk">
                    <div class="px-5 py-4">
                        <h2 class="section-title">Pratinjau produk</h2>
                        <p class="section-subtitle">Tampilan ringkas sebelum disimpan.</p>
                    </div>

                    <div class="border-t border-slate-100 p-5">
                        <div class="flex items-center gap-3">
                            <span id="preview-initials" class="avatar-initials h-11 w-11 text-xs">PR</span>

                            <div class="min-w-0">
                                <p id="preview-name" class="truncate text-sm font-semibold text-slate-900">Nama produk</p>
                                <p id="preview-code" class="mt-0.5 truncate font-mono text-[10px] font-medium text-slate-400">KODE-BARANG</p>
                            </div>
                        </div>

                        <dl class="mt-5 space-y-3 border-t border-slate-100 pt-4 text-xs">
                            <div class="flex items-center justify-between gap-3">
                                <dt class="text-slate-500">Kategori</dt>
                                <dd id="preview-category" class="max-w-[160px] truncate text-right font-semibold text-slate-800">Belum dipilih</dd>
                            </div>

                            <div class="flex items-center justify-between gap-3">
                                <dt class="text-slate-500">Harga</dt>
                                <dd id="preview-price" class="text-right font-semibold text-slate-800 tabular-nums">Rp 0</dd>
                            </div>

                            <div class="flex items-center justify-between gap-3">
                                <dt class="text-slate-500">Stok awal</dt>
                                <dd id="preview-stock" class="text-right font-semibold text-slate-800 tabular-nums">0 unit</dd>
                            </div>
                        </dl>
                    </div>
                </section>

                <section class="card p-5">
                    <h2 class="section-title">Sebelum menyimpan</h2>

                    <ul class="mt-4 space-y-3.5">
                        <li class="flex gap-2.5">
                            <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">
                                <x-icon name="check" class="h-3 w-3" stroke="2.4" />
                            </span>
                            <p class="text-xs leading-5 text-slate-500">Pastikan kode barang belum digunakan produk lain.</p>
                        </li>

                        <li class="flex gap-2.5">
                            <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">
                                <x-icon name="check" class="h-3 w-3" stroke="2.4" />
                            </span>
                            <p class="text-xs leading-5 text-slate-500">Pilih kategori yang paling sesuai agar laporan stok lebih rapi.</p>
                        </li>

                        <li class="flex gap-2.5">
                            <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">
                                <x-icon name="check" class="h-3 w-3" stroke="2.4" />
                            </span>
                            <p class="text-xs leading-5 text-slate-500">Periksa harga dan stok awal sebelum menyimpan.</p>
                        </li>
                    </ul>
                </section>

            </aside>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Pratinjau produk di panel kanan (hanya tampilan, tidak memengaruhi data).
    (function () {
        const nameInput = document.querySelector('[data-preview-name]');
        const codeInput = document.querySelector('[data-preview-code]');
        const stockInput = document.querySelector('[data-preview-stock]');
        const priceInput = document.querySelector('[data-preview-price]');
        const categorySelect = document.querySelector('[data-preview-category]');

        const nameOut = document.getElementById('preview-name');
        const codeOut = document.getElementById('preview-code');
        const stockOut = document.getElementById('preview-stock');
        const priceOut = document.getElementById('preview-price');
        const categoryOut = document.getElementById('preview-category');
        const initialsOut = document.getElementById('preview-initials');

        if (!nameInput || !nameOut) return;

        const rupiah = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0,
        });

        function updatePreview() {
            const name = nameInput.value.trim();
            const code = codeInput?.value.trim();

            nameOut.textContent = name || 'Nama produk';
            codeOut.textContent = code ? code.toUpperCase() : 'KODE-BARANG';
            initialsOut.textContent = name ? name.substring(0, 2).toUpperCase() : 'PR';

            const categoryOption = categorySelect?.options[categorySelect.selectedIndex];
            const hasCategory = categorySelect?.value && categoryOption;
            categoryOut.textContent = hasCategory ? categoryOption.textContent.trim() : 'Belum dipilih';

            const price = Number(priceInput?.value || 0);
            priceOut.textContent = rupiah.format(Number.isFinite(price) ? price : 0);

            const stock = Number(stockInput?.value || 0);
            stockOut.textContent = (Number.isFinite(stock) ? stock : 0).toLocaleString('id-ID') + ' unit';
        }

        [nameInput, codeInput, stockInput, priceInput].forEach((input) => {
            input?.addEventListener('input', updatePreview);
        });

        categorySelect?.addEventListener('change', updatePreview);

        updatePreview();
    })();
</script>
@endpush
