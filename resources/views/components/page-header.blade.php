{{--
    Header halaman reusable.

    Contoh:
    <x-page-header title="Data Produk" subtitle="..." eyebrow="Inventaris">
        <a href="...">Tambah Produk</a>
    </x-page-header>
--}}
@props([
    'title',
    'subtitle' => null,
    'eyebrow' => null,
    'breadcrumbs' => [],
])

<section {{ $attributes->class('flex flex-col gap-4 border-b border-slate-200/70 pb-5 sm:pb-6 lg:flex-row lg:items-end lg:justify-between') }}>
    <div class="min-w-0">
        @if (count($breadcrumbs) > 0)
            <nav class="mb-3 flex flex-wrap items-center gap-1.5 text-[11px] font-medium text-slate-400" aria-label="Breadcrumb">
                @foreach ($breadcrumbs as $crumb)
                    @if (! $loop->first)
                        <x-icon name="chevron-right" class="h-3 w-3 text-slate-300" stroke="2" />
                    @endif

                    @if (! empty($crumb['url']))
                        <a href="{{ $crumb['url'] }}" class="transition-colors hover:text-slate-700">{{ $crumb['label'] }}</a>
                    @else
                        <span class="text-slate-600" aria-current="page">{{ $crumb['label'] }}</span>
                    @endif
                @endforeach
            </nav>
        @elseif ($eyebrow)
            <div class="mb-3 inline-flex items-center gap-2 rounded-full border border-emerald-100 bg-emerald-50/80 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.14em] text-emerald-800">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-600"></span>
                {{ $eyebrow }}
            </div>
        @endif

        <h1 class="text-[27px] font-semibold leading-tight tracking-[-0.035em] text-slate-950 sm:text-[34px]">
            {{ $title }}
        </h1>

        @if ($subtitle)
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500 sm:text-[15px]">{{ $subtitle }}</p>
        @endif
    </div>

    @if (trim((string) $slot) !== '')
        <div class="flex shrink-0 flex-wrap items-center gap-2.5">
            {{ $slot }}
        </div>
    @endif
</section>
