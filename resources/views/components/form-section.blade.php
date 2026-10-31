{{--
    Satu bagian form dengan judul dan deskripsi di atas field terkait.
    Dipakai di halaman create/edit agar alur pengisian konsisten dan mudah dipindai.
--}}
@props([
    'title',
    'description' => null,
    'icon' => 'box',
    'step' => null,
    'tone' => 'slate',
])

@php
    $tones = [
        'blue' => 'bg-emerald-50 text-emerald-700',
        'emerald' => 'bg-emerald-50 text-emerald-700',
        'violet' => 'bg-emerald-50 text-emerald-700',
        'amber' => 'bg-amber-50 text-amber-600',
        'slate' => 'bg-slate-100 text-slate-500',
    ];
@endphp

<div {{ $attributes->class('space-y-5 px-5 py-6 sm:px-7 sm:py-7') }}>
    <div class="min-w-0 border-b border-slate-100 pb-4">
        <div class="flex items-start gap-3">
            <span class="icon-box-sm {{ $tones[$tone] ?? $tones['slate'] }}">
                <x-icon :name="$icon" class="h-4 w-4" />
            </span>

            <div class="min-w-0">
                @if ($step)
                    <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-slate-400">
                        Langkah {{ $step }}
                    </p>
                @endif

                <h2 class="mt-1 text-base font-semibold tracking-tight text-slate-900">{{ $title }}</h2>
            </div>
        </div>

        @if ($description)
            <p class="mt-1.5 max-w-2xl text-sm leading-6 text-slate-500">{{ $description }}</p>
        @endif
    </div>

    <div class="min-w-0">
        {{ $slot }}
    </div>
</div>
