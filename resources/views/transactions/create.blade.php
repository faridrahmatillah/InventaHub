@extends('layouts.app')

@section('title', 'Tambah Transaksi')

@section('content')
<div class="mx-auto max-w-6xl space-y-6">

    {{-- HEADER --}}
    <x-page-header
        title="Tambah Transaksi"
        subtitle="Catat aktivitas barang masuk atau keluar. Stok produk akan diperbarui otomatis setelah transaksi disimpan."
        :breadcrumbs="[
            ['label' => 'Transaksi', 'url' => route('transactions.index')],
            ['label' => 'Tambah Transaksi'],
        ]"
    >
        <a href="{{ route('transactions.index') }}" class="btn btn-secondary">
            <x-icon name="arrow-left" class="h-4 w-4" stroke="2" />
            Kembali
        </a>
    </x-page-header>

    @if($products->isEmpty())
        <x-empty-state
            title="Belum ada produk aktif"
            :description="$hasCategories
                ? 'Tambahkan produk terlebih dahulu sebelum mencatat pergerakan stok.'
                : 'Buat kategori dan produk terlebih dahulu sebelum mencatat pergerakan stok.'"
            icon="box"
        >
            @if($hasCategories)
                <a href="{{ route('products.create') }}" class="btn btn-primary">
                    <x-icon name="plus" class="h-4 w-4" stroke="2" />
                    Tambah produk
                </a>
            @elseif(auth()->user()->role === 'admin')
                <a href="{{ route('categories.create') }}" class="btn btn-primary">
                    <x-icon name="plus" class="h-4 w-4" stroke="2" />
                    Buat kategori pertama
                </a>
            @else
                <p class="text-sm text-slate-600">Hubungi administrator untuk menyiapkan kategori dan produk.</p>
            @endif
        </x-empty-state>
    @else
    <form action="{{ route('transactions.store') }}" method="POST">
        @csrf

        <div class="card divide-y divide-slate-100 overflow-hidden">

                <x-form-section
                    step="01"
                    title="Detail transaksi"
                    description="Pilih produk, tentukan jenis pergerakan, lalu isi jumlah dan tanggal transaksi."
                    icon="transaction"
                    tone="blue"
                >
                    <div class="flex w-full flex-col space-y-4">
                        <x-input
                            name="product_id"
                            type="select"
                            label="Pilih Produk"
                            data-transaction-product
                            hint="Stok pada pilihan mengikuti data produk saat ini."
                            required
                            :margin="false"
                        >
                            <option value="">Pilih produk</option>

                            @foreach($products as $product)
                                <option value="{{ $product->id }}"
                                        data-name="{{ $product->name }}"
                                        data-code="{{ $product->code }}"
                                        data-stock="{{ $product->stock }}"
                                        @selected(old('product_id') == $product->id)>
                                    {{ $product->name }} (Stok: {{ $product->stock }})
                                </option>
                            @endforeach
                        </x-input>

                        <x-input
                            name="supplier_id"
                            type="select"
                            label="Pemasok (wajib untuk barang masuk)"
                            :hint="$suppliers->isEmpty()
                                ? 'Belum ada pemasok aktif. Hubungi admin untuk menambah pemasok.'
                                : 'Pilih pemasok yang mengirimkan barang.'"
                            :margin="false"
                        >
                            <option value="">Pilih pemasok</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </x-input>

                        {{-- JENIS TRANSAKSI --}}
                        <div>
                            <span class="form-label">
                                Jenis Transaksi
                                <span class="text-red-500" aria-hidden="true">*</span>
                            </span>

                            <div class="flex w-full flex-col space-y-2">
                                <label class="flex w-full items-center gap-2 text-sm text-slate-700">
                                    <input type="radio"
                                           name="type"
                                           value="in"
                                           class="h-4 w-4 accent-emerald-700"
                                           @checked(old('type') === 'in')
                                           required>
                                    Barang masuk
                                </label>

                                <label class="flex w-full items-center gap-2 text-sm text-slate-700">
                                    <input type="radio"
                                           name="type"
                                           value="out"
                                           class="h-4 w-4 accent-emerald-700"
                                           @checked(old('type') === 'out')>
                                    Barang keluar
                                </label>
                            </div>

                            @error('type')
                                <p class="form-error">
                                    <x-icon name="x-circle" class="mt-0.5 h-3.5 w-3.5 shrink-0" stroke="2" />
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <x-input
                            name="quantity"
                            type="number"
                            label="Jumlah Barang"
                            placeholder="Masukkan jumlah barang"
                            min="1"
                            inputmode="numeric"
                            data-transaction-quantity
                            required
                            :margin="false"
                            class="w-full min-w-0"
                        />

                        <x-input
                            name="transaction_date"
                            type="date"
                            label="Tanggal Transaksi"
                            :value="old('transaction_date', now()->format('Y-m-d'))"
                            required
                            :margin="false"
                            class="w-full min-w-0"
                        />

                        <p id="quantity-warning"
                           class="hidden items-start gap-2 rounded-xl border border-red-100 bg-red-50 px-3.5 py-3 text-xs font-medium leading-5 text-red-700">
                            <x-icon name="alert" class="mt-0.5 h-4 w-4 shrink-0" />
                            <span>Jumlah barang keluar melebihi stok yang tersedia.</span>
                        </p>
                    </div>
                </x-form-section>

                {{-- RINGKASAN STOK --}}
                <section class="p-5 sm:p-6">
                    <div class="mb-4">
                        <h2 class="section-title">Ringkasan stok</h2>
                        <p class="section-subtitle">Pratinjau perubahan sebelum disimpan.</p>
                    </div>

                    <div id="summary-empty" class="rounded-xl border border-dashed border-slate-200 bg-slate-50/70 px-4 py-8 text-center">
                        <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-xl bg-white text-slate-400 ring-1 ring-slate-200">
                            <x-icon name="box" class="h-5 w-5" />
                        </div>
                        <p class="mt-3 text-sm leading-6 text-slate-600">Pilih produk untuk melihat ringkasan stok.</p>
                    </div>

                    <div id="summary-content" class="hidden">
                        <div class="flex items-center gap-3">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-500">
                                <x-icon name="box" class="h-5 w-5" />
                            </span>

                            <div class="min-w-0">
                                <p id="summary-name" class="truncate text-sm font-semibold text-slate-900">-</p>
                                <p id="summary-code" class="mt-0.5 font-mono text-xs font-medium text-slate-500">-</p>
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-2.5">
                            <div class="rounded-xl border border-slate-200/80 bg-white p-3">
                                <p class="text-[11px] font-medium uppercase tracking-wider text-slate-500">Stok saat ini</p>
                                <p id="summary-stock" class="mt-1 text-sm font-semibold text-slate-800 tabular-nums">0 unit</p>
                            </div>

                            <div class="rounded-xl border border-slate-200/80 bg-white p-3">
                                <p class="text-[11px] font-medium uppercase tracking-wider text-slate-500">Stok akhir</p>
                                <p id="summary-projected" class="mt-1 text-sm font-semibold text-slate-800 tabular-nums">0 unit</p>
                            </div>
                        </div>

                        <p id="summary-note" class="mt-4 rounded-xl border border-emerald-100 bg-emerald-50 px-3.5 py-3 text-xs leading-5 text-emerald-900">
                            Stok akhir akan dihitung otomatis sesuai jenis transaksi.
                        </p>
                    </div>
                </section>

                {{-- PANDUAN --}}
                <div class="p-5 sm:px-6">
                    <div class="warning-note">
                        <x-icon name="alert" class="mt-0.5 h-4 w-4 shrink-0" />
                        <div>
                            <p class="font-semibold text-amber-900">Penting</p>
                            <ul class="mt-2 space-y-1.5">
                                <li class="flex gap-2">
                                    <span class="mt-2 h-1 w-1 shrink-0 rounded-full bg-amber-500"></span>
                                    Barang masuk akan menambah stok produk.
                                </li>
                                <li class="flex gap-2">
                                    <span class="mt-2 h-1 w-1 shrink-0 rounded-full bg-amber-500"></span>
                                    Barang keluar akan mengurangi stok produk.
                                </li>
                                <li class="flex gap-2">
                                    <span class="mt-2 h-1 w-1 shrink-0 rounded-full bg-amber-500"></span>
                                    Jumlah barang keluar tidak boleh melebihi stok tersedia.
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <x-form-section
                    step="02"
                    title="Keterangan tambahan"
                    description="Catatan opsional untuk membantu penelusuran transaksi di kemudian hari."
                    icon="edit"
                    tone="slate"
                >
                    <x-input
                        name="description"
                        type="textarea"
                        label="Keterangan (Opsional)"
                        placeholder="Contoh: Pengadaan rutin bulan Oktober, permintaan divisi produksi..."
                        rows="3"
                        :margin="false"
                    />
                </x-form-section>

                {{-- FOOTER AKSI --}}
                <div class="flex flex-col-reverse gap-3 bg-slate-50/70 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <p class="text-[11px] leading-5 text-slate-500">
                        Stok diperbarui otomatis setelah transaksi disimpan.
                    </p>

                    <div class="flex flex-col-reverse gap-2.5 sm:flex-row">
                        <a href="{{ route('transactions.index') }}" class="btn btn-secondary">
                            Batalkan
                        </a>

                        <button type="submit" data-loading-text="Menyimpan..." class="btn btn-primary">
                            <x-icon name="check" class="h-4 w-4" stroke="2" />
                            Simpan Transaksi
                        </button>
                    </div>
                </div>
        </div>
    </form>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Ringkasan stok interaktif: hanya membaca data dari dropdown,
    // perhitungan final tetap dilakukan oleh controller saat menyimpan.
    (function () {
        const productSelect = document.querySelector('[data-transaction-product]');
        const typeInputs = Array.from(document.querySelectorAll('input[name="type"]'));
        const quantityInput = document.querySelector('[data-transaction-quantity]');

        const summaryEmpty = document.getElementById('summary-empty');
        const summaryContent = document.getElementById('summary-content');
        const summaryName = document.getElementById('summary-name');
        const summaryCode = document.getElementById('summary-code');
        const summaryStock = document.getElementById('summary-stock');
        const summaryProjected = document.getElementById('summary-projected');
        const summaryNote = document.getElementById('summary-note');
        const quantityWarning = document.getElementById('quantity-warning');

        if (!productSelect || !typeInputs.length || !quantityInput) {
            return;
        }

        const numberFormat = new Intl.NumberFormat('id-ID');

        function selectedType() {
            const checked = document.querySelector('input[name="type"]:checked');

            return checked ? checked.value : '';
        }

        function updateSummary() {
            const option = productSelect.options[productSelect.selectedIndex];
            const hasProduct = productSelect.value !== '' && option;

            if (!hasProduct) {
                summaryEmpty.classList.remove('hidden');
                summaryContent.classList.add('hidden');
                quantityWarning?.classList.add('hidden');
                quantityWarning?.classList.remove('flex');
                return;
            }

            const stock = Number(option.dataset.stock || 0);
            const quantity = parseInt(quantityInput.value, 10);
            const validQuantity = Number.isFinite(quantity) && quantity > 0 ? quantity : 0;
            const type = selectedType();
            const isIn = type === 'in';
            const isOut = type === 'out';
            const projected = isIn ? stock + validQuantity : (isOut ? stock - validQuantity : stock);

            summaryEmpty.classList.add('hidden');
            summaryContent.classList.remove('hidden');

            summaryName.textContent = option.dataset.name || option.textContent.trim();
            summaryCode.textContent = option.dataset.code || '-';
            summaryStock.textContent = numberFormat.format(stock) + ' unit';
            summaryProjected.textContent = numberFormat.format(projected) + ' unit';

            summaryProjected.classList.remove('text-emerald-600', 'text-amber-600', 'text-red-600', 'text-slate-800');

            if (projected < 0) {
                summaryProjected.classList.add('text-red-600');
            } else if (projected < 5) {
                summaryProjected.classList.add('text-amber-600');
            } else {
                summaryProjected.classList.add('text-emerald-600');
            }

            const exceedsStock = isOut && validQuantity > stock;

            summaryNote.classList.remove('bg-emerald-50', 'text-emerald-900', 'border-emerald-100', 'bg-red-50', 'text-red-700', 'border-red-100');

            if (exceedsStock) {
                summaryNote.textContent = 'Stok tidak mencukupi. Kurangi jumlah barang keluar maksimal ' + numberFormat.format(stock) + ' unit.';
                summaryNote.classList.add('bg-red-50', 'text-red-700', 'border-red-100');
            } else if (isIn) {
                summaryNote.textContent = 'Transaksi masuk akan menambah stok sebanyak ' + numberFormat.format(validQuantity) + ' unit.';
                summaryNote.classList.add('bg-emerald-50', 'text-emerald-900', 'border-emerald-100');
            } else if (isOut) {
                summaryNote.textContent = 'Transaksi keluar akan mengurangi stok sebanyak ' + numberFormat.format(validQuantity) + ' unit.';
                summaryNote.classList.add('bg-emerald-50', 'text-emerald-900', 'border-emerald-100');
            } else {
                summaryNote.textContent = 'Pilih jenis transaksi untuk melihat estimasi stok akhir.';
                summaryNote.classList.add('bg-emerald-50', 'text-emerald-900', 'border-emerald-100');
            }

            if (quantityWarning) {
                quantityWarning.classList.toggle('hidden', ! exceedsStock);
                quantityWarning.classList.toggle('flex', exceedsStock);
            }
        }

        productSelect.addEventListener('change', updateSummary);
        quantityInput.addEventListener('input', updateSummary);
        typeInputs.forEach((input) => input.addEventListener('change', updateSummary));

        updateSummary();
    })();
</script>
@endpush
