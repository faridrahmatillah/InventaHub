@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 text-primary fw-bold">Catat Transaksi Baru</h5>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('transactions.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Barang</label>
                        <select name="product_id" class="form-select" required>
                            <option value="">-- Pilih Barang --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->code }} - {{ $product->name }} (Sisa Stok: {{ $product->stock }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Jenis Transaksi</label>
                            <select name="type" class="form-select" required>
                                <option value="in" {{ old('type') == 'in' ? 'selected' : '' }}>Barang Masuk (Tambah Stok)</option>
                                <option value="out" {{ old('type') == 'out' ? 'selected' : '' }}>Barang Keluar (Kurangi Stok)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tanggal Transaksi</label>
                            <input type="date" name="transaction_date" class="form-control" value="{{ old('transaction_date', date('Y-m-d')) }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Jumlah Barang</label>
                        <input type="number" name="quantity" class="form-control" value="{{ old('quantity') }}" min="1" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Keterangan (Opsional)</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Contoh: Tambahan dari supplier / Terjual ke pelanggan">{{ old('description') }}</textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection