@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 text-primary fw-bold">Edit Data Barang</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('products.update', $product->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <x-input name="category_id" type="select" label="Kategori Barang" required>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </x-input>

                    <div class="row">
                        <div class="col-md-6">
                            <x-input name="code" label="Kode Barang / SKU" :value="$product->code" required />
                        </div>
                        <div class="col-md-6">
                            <x-input name="name" label="Nama Barang" :value="$product->name" required />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <x-input name="stock" type="number" label="Jumlah Stok" :value="$product->stock" min="0" required />
                        </div>
                        <div class="col-md-6">
                            <x-input name="price" type="number" label="Harga Barang (Rp)" :value="$product->price" step="0.01" required />
                        </div>
                    </div>

                    <x-input name="description" type="textarea" label="Deskripsi (Opsional)" :value="$product->description" />

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('products.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Update Barang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
