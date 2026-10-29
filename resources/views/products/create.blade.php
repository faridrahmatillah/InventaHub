@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 text-primary fw-bold">Tambah Barang Baru</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('products.store') }}" method="POST">
                    @csrf

                    <x-input name="category_id" type="select" label="Kategori Barang" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </x-input>

                    <div class="row">
                        <div class="col-md-6">
                            <x-input name="code" label="Kode Barang / SKU" placeholder="Contoh: BRG-001" required />
                        </div>
                        <div class="col-md-6">
                            <x-input name="name" label="Nama Barang" placeholder="Contoh: Sepatu Sneaker" required />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <x-input name="stock" type="number" label="Jumlah Stok" :value="0" min="0" required />
                        </div>
                        <div class="col-md-6">
                            <x-input name="price" type="number" label="Harga Barang (Rp)" placeholder="150000" step="0.01" required />
                        </div>
                    </div>

                    <x-input name="description" type="textarea" label="Deskripsi (Opsional)" placeholder="Keterangan tambahan barang" />

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('products.index') }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Simpan Barang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
