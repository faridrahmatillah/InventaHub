@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0 text-primary fw-bold">Detail Kategori</h5>
                <span class="badge bg-secondary">{{ $category->products->count() }} Produk</span>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-4">
                    <tr>
                        <th width="30%">Nama Kategori</th>
                        <td>{{ $category->name }}</td>
                    </tr>
                    <tr>
                        <th>Deskripsi</th>
                        <td>{{ $category->description ?? '-' }}</td>
                    </tr>
                </table>

                <h6 class="fw-bold">Daftar Produk</h6>
                @if($category->products->isEmpty())
                    <p class="text-muted mb-0">Belum ada produk pada kategori ini.</p>
                @else
                    <ul class="list-group">
                        @foreach($category->products as $product)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>{{ $product->code }} - {{ $product->name }}</span>
                                <span class="badge bg-info text-dark">Stok: {{ $product->stock }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('categories.index') }}" class="btn btn-secondary">Kembali</a>
                    <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-warning">Edit</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
