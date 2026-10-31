@props([
    'label',
    'value',
    'hint' => null,
    'icon' => null,
    'tone' => 'blue',
])

@php
    $tones = [
        'blue' => 'bg-emerald-50 text-emerald-700',
        'emerald' => 'bg-emerald-50 text-emerald-600',
        'violet' => 'bg-violet-50 text-violet-600',
        'amber' => 'bg-amber-50 text-amber-600',
        'red' => 'bg-red-50 text-red-600',
        'slate' => 'bg-slate-100 text-slate-500',
    ];
@endphp

<div {{ $attributes->class('stat-cell') }}>
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <p class="stat-label">{{ $label }}</p>
            <p class="stat-value">{{ $value }}</p>
        </div>

        @if ($icon)
            <span class="icon-box-sm {{ $tones[$tone] ?? $tones['blue'] }}">
                <x-icon :name="$icon" class="h-4 w-4" />
            </span>
        @endif
    </div>

    @if ($hint)
        <p class="stat-hint">{{ $hint }}</p>
    @endif
</div>
