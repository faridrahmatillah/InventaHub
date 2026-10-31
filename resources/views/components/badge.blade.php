{{--
    Badge status reusable.

    Contoh:
    <x-badge tone="success" dot>Stok aman</x-badge>
    <x-badge tone="warning">Menipis</x-badge>
    <x-badge tone="danger" dot>Habis</x-badge>
--}}
@props([
    'tone' => 'neutral',
    'dot' => false,
])

@php
    $tones = [
        'neutral' => 'badge-neutral',
        'info' => 'badge-info',
        'success' => 'badge-success',
        'warning' => 'badge-warning',
        'danger' => 'badge-danger',
    ];
@endphp

<span {{ $attributes->class(['badge', $tones[$tone] ?? $tones['neutral']]) }}>
    @if ($dot)
        <span class="badge-dot"></span>
    @endif

    {{ $slot }}
</span>
