@extends('layouts.app')

@section('title', 'Data Pemasok')

@section('content')
<div class="space-y-6">
    <x-page-header
        eyebrow="Pengelolaan"
        title="Data Pemasok"
        subtitle="Kelola mitra pemasok yang menjadi sumber barang masuk."
    >
        <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
            <x-icon name="plus" class="h-4 w-4" />
            Tambah Pemasok
        </a>
    </x-page-header>

    <section class="card overflow-hidden">
        <div class="card-header">
            <div>
                <h2 class="section-title">Daftar pemasok</h2>
                <p class="section-subtitle">Informasi kontak dan riwayat barang masuk dari setiap pemasok.</p>
            </div>
            <x-badge tone="neutral">{{ number_format($suppliers->total()) }} pemasok</x-badge>
        </div>

        @if($suppliers->isEmpty())
            <div class="p-6">
                <x-empty-state
                    title="Belum ada pemasok"
                    description="Tambahkan pemasok sebelum mencatat transaksi barang masuk."
                    icon="user"
                >
                    <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
                        <x-icon name="plus" class="h-4 w-4" />
                        Tambah Pemasok
                    </a>
                </x-empty-state>
            </div>
        @else
            <div class="table-wrap hidden border-t border-slate-100 lg:block">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nama pemasok</th>
                            <th>Kontak</th>
                            <th>Email / telepon</th>
                            <th class="text-right">Barang masuk tercatat</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($suppliers as $supplier)
                            <tr>
                                <td>
                                    <a href="{{ route('suppliers.show', $supplier) }}" class="font-semibold text-slate-900 hover:text-emerald-700">
                                        {{ $supplier->name }}
                                    </a>
                                </td>
                                <td>{{ $supplier->contact_name ?: '—' }}</td>
                                <td>
                                    <p>{{ $supplier->email ?: '—' }}</p>
                                    <p class="text-xs text-slate-400">{{ $supplier->phone ?: '—' }}</p>
                                </td>
                                <td class="text-right">{{ number_format($supplier->transactions_count) }}</td>
                                <td class="text-right">
                                    <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-secondary btn-sm">Edit</a>
                                    <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-ghost btn-sm">Detail</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="divide-y divide-slate-100 border-t border-slate-100 lg:hidden">
                @foreach($suppliers as $supplier)
                    <article class="space-y-3 px-5 py-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <a href="{{ route('suppliers.show', $supplier) }}" class="block truncate text-sm font-semibold text-slate-900 hover:text-emerald-700">
                                    {{ $supplier->name }}
                                </a>
                                <p class="mt-0.5 text-xs text-slate-500">{{ $supplier->contact_name ?: 'Kontak belum diisi' }}</p>
                            </div>
                            <x-badge tone="neutral">{{ number_format($supplier->transactions_count) }} transaksi</x-badge>
                        </div>

                        <div class="space-y-1 text-xs text-slate-500">
                            <p>{{ $supplier->email ?: 'Email belum diisi' }}</p>
                            <p>{{ $supplier->phone ?: 'Telepon belum diisi' }}</p>
                        </div>

                        <div class="flex gap-2">
                            <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-secondary btn-sm">Detail</a>
                            <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-secondary btn-sm">Edit</a>
                        </div>
                    </article>
                @endforeach
            </div>

            @if($suppliers->hasPages())
                <div class="flex flex-col gap-3 border-t border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <p class="text-xs text-slate-500">
                        Menampilkan
                        <span class="font-semibold text-slate-700">{{ $suppliers->firstItem() }}</span>
                        sampai
                        <span class="font-semibold text-slate-700">{{ $suppliers->lastItem() }}</span>
                        dari
                        <span class="font-semibold text-slate-700">{{ $suppliers->total() }}</span>
                        pemasok
                    </p>

                    <div class="self-end sm:self-auto">
                        {{ $suppliers->links() }}
                    </div>
                </div>
            @endif
        @endif
    </section>
</div>
@endsection
