@extends('layouts.app')

@section('title', 'Detail Pemasok')

@section('content')
<div class="mx-auto max-w-5xl space-y-6">
    <x-page-header
        :title="$supplier->name"
        subtitle="Informasi pemasok dan transaksi barang masuk terbaru."
        :breadcrumbs="[
            ['label' => 'Pemasok', 'url' => route('suppliers.index')],
            ['label' => $supplier->name],
        ]"
    >
        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Kembali</a>
        <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-primary">Edit Pemasok</a>
        <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST"
              data-confirm="Pemasok akan dinonaktifkan. Riwayat transaksi tetap tersimpan."
              data-confirm-title="Nonaktifkan pemasok?"
              data-confirm-action="Ya, nonaktifkan">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Nonaktifkan</button>
        </form>
    </x-page-header>

    <section class="card grid gap-5 p-5 sm:grid-cols-2 sm:p-6">
        <div>
            <p class="text-xs text-slate-500">Nama kontak</p>
            <p class="mt-1 font-medium text-slate-900">{{ $supplier->contact_name ?: '—' }}</p>
        </div>
        <div>
            <p class="text-xs text-slate-500">Telepon</p>
            <p class="mt-1 font-medium text-slate-900">{{ $supplier->phone ?: '—' }}</p>
        </div>
        <div>
            <p class="text-xs text-slate-500">Email</p>
            <p class="mt-1 font-medium text-slate-900">{{ $supplier->email ?: '—' }}</p>
        </div>
        <div>
            <p class="text-xs text-slate-500">Alamat</p>
            <p class="mt-1 whitespace-pre-line font-medium text-slate-900">{{ $supplier->address ?: '—' }}</p>
        </div>
    </section>

    <section class="card overflow-hidden">
        <div class="card-header">
            <div>
                <h2 class="section-title">Barang masuk terbaru</h2>
                <p class="section-subtitle">Maksimal 10 transaksi terbaru dari pemasok ini.</p>
            </div>
        </div>

        @if($transactions->isEmpty())
            <p class="border-t border-slate-100 p-5 text-sm text-slate-500">Belum ada transaksi barang masuk.</p>
        @else
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr><th>Tanggal</th><th>Produk</th><th>Jumlah</th><th>Dicatat oleh</th></tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                            <tr>
                                <td><x-date :value="$transaction->transaction_date" /></td>
                                <td>{{ $transaction->product?->name ?? 'Produk dihapus' }}</td>
                                <td>{{ number_format($transaction->quantity) }}</td>
                                <td>{{ $transaction->user?->name ?? 'Pengguna tidak tersedia' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</div>
@endsection
