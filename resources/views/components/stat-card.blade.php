{{--
    Kartu statistik reusable.

    Contoh:
    <x-stat-card label="Total Produk" :value="12" hint="Jenis barang terdaftar" icon="box" tone="blue" />
    <x-stat-card label="Stok Habis" :value="2" icon="alert" tone="red" value-tone="red" />
    <x-stat-card label="Transaksi" :value="8" icon="transaction" tone="violet"
                 :href="route('transactions.index')" link-label="Lihat transaksi" />
--}}
@props([
    'label',
    'value',
    'hint' => null,
    'icon' => 'box',
    'tone' => 'blue',
    'valueTone' => null,
    'href' => null,
    'linkLabel' => 'Lihat detail',
])

@php
    $tones = [
        'blue' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700'],
        'emerald' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600'],
        'violet' => ['bg' => 'bg-violet-50', 'text' => 'text-violet-600'],
        'amber' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600'],
        'red' => ['bg' => 'bg-red-50', 'text' => 'text-red-600'],
        'slate' => ['bg' => 'bg-slate-100', 'text' => 'text-slate-600'],
    ];

    $valueTones = [
        'blue' => 'text-emerald-700',
        'emerald' => 'text-emerald-600',
        'violet' => 'text-violet-600',
        'amber' => 'text-amber-600',
        'red' => 'text-red-600',
        'slate' => 'text-slate-900',
    ];

    $style = $tones[$tone] ?? $tones['blue'];
    $valueClass = $valueTone ? ($valueTones[$valueTone] ?? 'text-slate-900') : 'text-slate-900';
@endphp

<article {{ $attributes->class('card flex h-full flex-col p-4 transition-shadow hover:shadow-md sm:p-5') }}>
    <div class="flex items-start justify-between gap-2 sm:gap-3">
        <div class="min-w-0">
            <p class="text-xs font-medium text-slate-500 sm:text-sm">{{ $label }}</p>

            <p class="mt-1.5 text-xl font-semibold tracking-tight {{ $valueClass }} sm:mt-2 sm:text-2xl">
                {{ $value }}
            </p>

            @if ($hint)
                <p class="mt-1 hidden text-xs leading-5 text-slate-400 sm:block">{{ $hint }}</p>
            @endif
        </div>

        <span class="icon-box hidden shrink-0 sm:flex {{ $style['bg'] }} {{ $style['text'] }}">
            <x-icon :name="$icon" class="h-5 w-5" />
        </span>
    </div>

    @if ($href)
        <a href="{{ $href }}"
           class="mt-auto inline-flex items-center gap-1.5 pt-3 text-xs font-semibold text-slate-500 transition-colors hover:text-emerald-700 sm:pt-4">
            {{ $linkLabel }}
            <x-icon name="arrow-right" class="h-3.5 w-3.5" stroke="2" />
        </a>
    @endif
</article>
