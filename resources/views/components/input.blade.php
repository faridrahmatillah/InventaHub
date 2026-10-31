{{--
    Komponen input reusable: label + kontrol + pesan error validasi.

    Contoh pemakaian:
    <x-input name="code" label="Kode Barang" placeholder="Contoh: BRG-001" required />
    <x-input name="stock" type="number" label="Stok" :value="0" min="0" required />
    <x-input name="description" type="textarea" label="Deskripsi" :rows="3" />
    <x-input name="category_id" type="select" label="Kategori" required>
        <option value="">-- Pilih --</option>
    </x-input>
    <x-input name="password" type="password" label="Password" toggle required />

    Properti tambahan:
    - hint   : teks bantuan kecil di bawah field.
    - toggle : tampilkan tombol lihat/sembunyikan untuk input password.
    - margin : matikan margin bawah (false) jika memakai container space-y.
--}}
@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'rows' => 3,
    'hint' => null,
    'toggle' => false,
    'margin' => true,
])

@php
    // Nilai isi field: prioritaskan old() supaya input tidak hilang saat validasi gagal.
    $current = old($name, $value);

    // Border/ring merah kalau field ini punya pesan error.
    $hasError = $errors->has($name);

    $controlClass = 'form-control' . ($hasError ? ' form-control-error' : '');

    $describedBy = collect([
        $hasError ? $name . '-error' : null,
        $hint ? $name . '-hint' : null,
    ])->filter()->implode(' ');
@endphp

<div class="{{ $margin ? 'mb-4' : '' }}">
    @if ($label)
        <label for="{{ $name }}" class="form-label">
            {{ $label }}
            @if ($required)
                <span class="text-red-500" aria-hidden="true">*</span>
            @endif
        </label>
    @endif

    @if ($type === 'textarea')
        <textarea
            id="{{ $name }}"
            name="{{ $name }}"
            rows="{{ $rows }}"
            placeholder="{{ $placeholder }}"
            @required($required)
            @if ($hasError) aria-invalid="true" @endif
            @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
            {{ $attributes->class($controlClass) }}
        >{{ $current }}</textarea>

    @elseif ($type === 'select')
        <div class="relative">
            <select
                id="{{ $name }}"
                name="{{ $name }}"
                @required($required)
                @if ($hasError) aria-invalid="true" @endif
                @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
                {{ $attributes->class($controlClass . ' appearance-none pr-10') }}
            >
                {{ $slot }}
            </select>

            <x-icon name="chevron-down" class="pointer-events-none absolute right-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
        </div>

    @elseif ($type === 'password' && $toggle)
        <div class="relative">
            <input
                type="password"
                id="{{ $name }}"
                name="{{ $name }}"
                value="{{ $current }}"
                placeholder="{{ $placeholder }}"
                @required($required)
                @if ($hasError) aria-invalid="true" @endif
                @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
                {{ $attributes->class($controlClass . ' pr-12') }}
            >

            <button type="button"
                    data-password-toggle
                    data-target="{{ $name }}"
                    class="absolute inset-y-0 right-0 flex w-11 items-center justify-center rounded-r-lg text-slate-400 transition hover:text-emerald-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500"
                    aria-label="Tampilkan password">
                <x-icon name="eye" class="h-5 w-5" />
            </button>
        </div>

    @else
        <input
            type="{{ $type }}"
            id="{{ $name }}"
            name="{{ $name }}"
            value="{{ $current }}"
            placeholder="{{ $placeholder }}"
            @required($required)
            @if ($hasError) aria-invalid="true" @endif
            @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
            {{ $attributes->class($controlClass . ($type === 'number' ? ' appearance-none' : '')) }}
        >
    @endif

    @error($name)
        <p id="{{ $name }}-error" class="form-error">
            <x-icon name="x-circle" class="mt-0.5 h-3.5 w-3.5 shrink-0" stroke="2" />
            {{ $message }}
        </p>
    @enderror

    @if ($hint && ! $hasError)
        <p id="{{ $name }}-hint" class="form-hint">{{ $hint }}</p>
    @endif
</div>
