{{--
    Komponen flash message reusable.
    Dipanggil di layout utama dengan: <x-flash />
    Membaca pesan dari session: ->with('success', '...') atau ->with('error', '...').
--}}
@php
    $variants = [
        'success' => [
            'icon' => 'check-circle',
            'iconColor' => 'text-emerald-600',
            'label' => 'Berhasil',
        ],
        'error' => [
            'icon' => 'x-circle',
            'iconColor' => 'text-red-600',
            'label' => 'Terjadi kesalahan',
        ],
        'warning' => [
            'icon' => 'alert',
            'iconColor' => 'text-amber-600',
            'label' => 'Perhatian',
        ],
        'info' => [
            'icon' => 'info',
            'iconColor' => 'text-blue-600',
            'label' => 'Informasi',
        ],
    ];
@endphp

<div class="pointer-events-none fixed top-4 right-4 z-[9999] flex w-[calc(100%-2rem)] max-w-sm flex-col gap-3">
@foreach ($variants as $type => $variant)
    @if (session()->has($type))
        <div data-flash
             data-autohide="{{ in_array($type, ['error', 'warning']) ? 9000 : 5000 }}"
             class="pointer-events-auto flex items-start gap-3 rounded-xl border border-slate-700 bg-slate-950 px-4 py-3.5 text-white shadow-xl shadow-slate-950/20"
             role="alert">
            <x-icon :name="$variant['icon']" class="mt-0.5 h-5 w-5 shrink-0 {{ $variant['iconColor'] }}" stroke="2" />

            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold">{{ $variant['label'] }}</p>
                <p class="mt-0.5 text-sm leading-5 text-slate-300">{{ session($type) }}</p>
            </div>

            <button type="button"
                    onclick="this.parentElement.remove()"
                    class="rounded-md p-1 text-slate-400 transition hover:bg-white/10 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-current"
                    aria-label="Tutup pesan">
                <x-icon name="close" class="h-4 w-4" stroke="2" />
            </button>
        </div>
    @endif
@endforeach
</div>
