@extends('layouts.app')

@section('title', 'Catat Stok Opname')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <x-page-header
        title="Catat Stok Opname"
        subtitle="Masukkan hasil hitung fisik. Stok produk akan disesuaikan setelah catatan berhasil disimpan."
        :breadcrumbs="[
            ['label' => 'Stok Opname', 'url' => route('stock-opnames.index')],
            ['label' => 'Catat Opname'],
        ]"
    >
        <a href="{{ route('stock-opnames.index') }}" class="btn btn-secondary">
            <x-icon name="arrow-left" class="h-4 w-4" />
            Kembali
        </a>
    </x-page-header>

    @if($products->isEmpty())
        <section class="card p-6">
            <x-empty-state
                title="Belum ada produk aktif"
                description="Tambahkan produk sebelum melakukan stok opname."
                icon="box"
            >
                <a href="{{ route('products.create') }}" class="btn btn-primary">Tambah Produk</a>
            </x-empty-state>
        </section>
    @else
        <form action="{{ route('stock-opnames.store') }}" method="POST" class="card divide-y divide-slate-100 overflow-hidden">
            @csrf
            <div class="space-y-5 p-5 sm:p-6">
                <div class="info-note">
                    <x-icon name="user" class="mt-0.5 h-4 w-4 shrink-0" />
                    <p>
                        Catatan ini akan mencantumkan akun Anda sebagai pencatat. Hasil opname disimpan sebagai riwayat tetap.
                    </p>
                </div>

                <x-input name="product_id" type="select" label="Produk" required>
                    <option value="">Pilih produk</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" @selected(old('product_id') == $product->id)>
                            {{ $product->name }} ({{ $product->code }}) — stok sistem {{ $product->stock }}
                        </option>
                    @endforeach
                </x-input>

                <div class="grid gap-5 sm:grid-cols-2">
                    <x-input
                        name="counted_stock"
                        type="number"
                        label="Jumlah fisik yang dihitung"
                        min="0"
                        max="2147483647"
                        required
                    />
                    <x-input
                        name="counted_at"
                        type="date"
                        label="Tanggal penghitungan"
                        :value="now()->toDateString()"
                        max="{{ now()->toDateString() }}"
                        required
                    />
                </div>

                <x-input
                    name="note"
                    type="textarea"
                    label="Catatan (opsional)"
                    rows="3"
                    placeholder="Contoh: stok rusak, selisih hitung, atau lokasi penyimpanan."
                />
            </div>

            <div class="flex flex-col-reverse gap-3 bg-slate-50/70 px-5 py-4 sm:flex-row sm:justify-between sm:px-6">
                <p class="text-xs leading-5 text-slate-500">
                    Catatan bersifat permanen. Koreksi berikutnya dibuat sebagai opname baru.
                </p>
                <button type="submit" class="btn btn-primary">
                    <x-icon name="check" class="h-4 w-4" />
                    Simpan dan Sesuaikan Stok
                </button>
            </div>
        </form>
    @endif
</div>
@endsection
