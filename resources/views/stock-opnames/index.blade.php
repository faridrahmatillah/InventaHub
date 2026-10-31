@extends('layouts.app')

@section('title', 'Stok Opname')

@section('content')
<div class="space-y-6">
    <x-page-header
        eyebrow="Pengelolaan"
        title="Stok Opname"
        subtitle="Bandingkan catatan sistem dengan hasil hitung fisik dan rekonsiliasi stok."
    >
        <a href="{{ route('stock-opnames.create') }}" class="btn btn-primary">
            <x-icon name="plus" class="h-4 w-4" />
            Catat Opname
        </a>
    </x-page-header>

    <section class="card overflow-hidden">
        <div class="card-header">
            <div>
                <h2 class="section-title">Riwayat opname</h2>
                <p class="section-subtitle">Hasil hitung fisik dan selisih terhadap stok yang tercatat di sistem.</p>
            </div>
            <x-badge tone="neutral">{{ number_format($opnames->total()) }} catatan</x-badge>
        </div>

        @if($opnames->isEmpty())
            <div class="p-6">
                <x-empty-state
                    title="Belum ada catatan opname"
                    description="Catat hasil hitung fisik untuk menyesuaikan stok produk."
                    icon="chart"
                >
                    <a href="{{ route('stock-opnames.create') }}" class="btn btn-primary">
                        <x-icon name="plus" class="h-4 w-4" />
                        Catat Opname
                    </a>
                </x-empty-state>
            </div>
        @else
            <div class="table-wrap hidden border-t border-slate-100 lg:block">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Produk</th>
                            <th class="text-right">Stok sistem</th>
                            <th class="text-right">Stok fisik</th>
                            <th class="text-right">Selisih</th>
                            <th>Dicatat oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($opnames as $opname)
                            <tr>
                                <td>
                                    <a href="{{ route('stock-opnames.show', $opname) }}" class="font-medium text-emerald-700 hover:text-emerald-900">
                                        <x-date :value="$opname->counted_at" />
                                    </a>
                                </td>
                                <td>{{ $opname->product?->name ?? 'Produk dihapus' }}</td>
                                <td class="text-right">{{ number_format($opname->system_stock) }}</td>
                                <td class="text-right">{{ number_format($opname->counted_stock) }}</td>
                                <td class="text-right font-semibold {{ $opname->difference < 0 ? 'text-red-600' : ($opname->difference > 0 ? 'text-emerald-600' : 'text-slate-500') }}">
                                    {{ $opname->difference > 0 ? '+' : '' }}{{ number_format($opname->difference) }}
                                </td>
                                <td>{{ $opname->user?->name ?? 'Pengguna tidak tersedia' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="divide-y divide-slate-100 border-t border-slate-100 lg:hidden">
                @foreach($opnames as $opname)
                    <a href="{{ route('stock-opnames.show', $opname) }}" class="block space-y-3 px-5 py-4 transition hover:bg-emerald-50/30">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-slate-900">{{ $opname->product?->name ?? 'Produk dihapus' }}</p>
                                <p class="mt-0.5 text-xs text-slate-500"><x-date :value="$opname->counted_at" /></p>
                            </div>
                            <span class="shrink-0 rounded-lg bg-slate-50 px-2.5 py-1 text-xs font-semibold tabular-nums {{ $opname->difference < 0 ? 'text-red-600' : ($opname->difference > 0 ? 'text-emerald-700' : 'text-slate-600') }}">
                                {{ $opname->difference > 0 ? '+' : '' }}{{ number_format($opname->difference) }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <p class="text-slate-500">Stok sistem <span class="ml-1 font-semibold text-slate-800">{{ number_format($opname->system_stock) }}</span></p>
                            <p class="text-slate-500">Stok fisik <span class="ml-1 font-semibold text-slate-800">{{ number_format($opname->counted_stock) }}</span></p>
                        </div>

                        <p class="text-xs text-slate-500">Dicatat oleh <span class="font-medium text-slate-700">{{ $opname->user?->name ?? 'Pengguna tidak tersedia' }}</span></p>
                    </a>
                @endforeach
            </div>

            @if($opnames->hasPages())
                <div class="flex flex-col gap-3 border-t border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <p class="text-xs text-slate-500">
                        Menampilkan
                        <span class="font-semibold text-slate-700">{{ $opnames->firstItem() }}</span>
                        sampai
                        <span class="font-semibold text-slate-700">{{ $opnames->lastItem() }}</span>
                        dari
                        <span class="font-semibold text-slate-700">{{ $opnames->total() }}</span>
                        catatan opname
                    </p>

                    <div class="self-end sm:self-auto">
                        {{ $opnames->links() }}
                    </div>
                </div>
            @endif
        @endif
    </section>
</div>
@endsection
