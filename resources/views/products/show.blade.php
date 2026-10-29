@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0 text-primary fw-bold">Detail Barang</h5>
                <span class="badge bg-info text-dark">{{ $product->category->name }}</span>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-4">
                    <tr>
                        <th width="30%">Kode / SKU</th>
                        <td>{{ $product->code }}</td>
                    </tr>
                    <tr>
                        <th>Nama Barang</th>
                        <td>{{ $product->name }}</td>
                    </tr>
                    <tr>
                        <th>Stok Saat Ini</th>
                        <td>{{ $product->stock }}</td>
                    </tr>
                    <tr>
                        <th>Harga</th>
                        <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Deskripsi</th>
                        <td>{{ $product->description ?? '-' }}</td>
                    </tr>
                </table>

                <h6 class="fw-bold">Riwayat Transaksi Terakhir</h6>
                @if($product->transactions->isEmpty())
                    <p class="text-muted mb-0">Belum ada transaksi untuk barang ini.</p>
                @else
                    <ul class="list-group">
                        @foreach($product->transactions->sortByDesc('transaction_date')->take(5) as $transaction)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>
                                    {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d M Y') }}
                                    -
                                    {{ $transaction->type == 'in' ? 'Masuk' : 'Keluar' }}
                                </span>
                                <span class="badge {{ $transaction->type == 'in' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $transaction->quantity }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">Kembali</a>
                    <a href="{{ route('products.edit', $product->id) }}" class="btn btn-warning">Edit</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
