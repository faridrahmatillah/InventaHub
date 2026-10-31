{{--
    Tampilan data kosong reusable.

    Contoh:
    <x-empty-state title="Belum ada produk" description="Tambahkan produk pertama." icon="box">
        <a href="...">Tambah Produk</a>
    </x-empty-state>
--}}
@props([
    'title',
    'description' => null,
    'icon' => 'box',
    'compact' => false,
])

@php
    $icons = [
        'box' => 'box',
        'category' => 'category',
        'transaction' => 'transaction',
        'check' => 'check-circle',
        'alert' => 'alert',
    ];
@endphp

<div {{ $attributes->class([
    'rounded-2xl border border-dashed border-slate-300 bg-slate-50/50 px-6 text-center',
    'py-14' => ! $compact,
    'py-9' => $compact,
]) }}>
    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700">
        <x-icon :name="$icons[$icon] ?? 'box'" class="h-6 w-6" />
    </div>

    <h3 class="mt-4 text-sm font-semibold text-slate-900">{{ $title }}</h3>

    @if ($description)
        <p class="mx-auto mt-1.5 max-w-sm text-sm leading-6 text-slate-500">{{ $description }}</p>
    @endif

    @if (trim((string) $slot) !== '')
        <div class="mt-5 flex flex-wrap items-center justify-center gap-3">
            {{ $slot }}
        </div>
    @endif
</div>
