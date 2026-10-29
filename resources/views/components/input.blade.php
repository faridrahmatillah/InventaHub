{{--
    Komponen input reusable: label + kontrol + pesan error validasi.

    Contoh pemakaian:
    <x-input name="code" label="Kode Barang" placeholder="Contoh: BRG-001" required />
    <x-input name="stock" type="number" label="Stok" :value="0" min="0" required />
    <x-input name="description" type="textarea" label="Deskripsi" :rows="3" />
    <x-input name="category_id" type="select" label="Kategori" required>
        <option value="">-- Pilih --</option>
    </x-input>
--}}
@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'rows' => 3,
])

@php
    // Nilai isi field: prioritaskan old() supaya input tidak hilang saat validasi gagal
    $current = old($name, $value);

    // Tambahkan class is-invalid otomatis kalau field ini punya pesan error
    $stateClass = $errors->has($name) ? ' is-invalid' : '';
@endphp

<div class="mb-3">
    @if ($label)
        <label for="{{ $name }}" class="form-label fw-bold">{{ $label }}</label>
    @endif

    @if ($type === 'textarea')
        <textarea
            id="{{ $name }}"
            name="{{ $name }}"
            rows="{{ $rows }}"
            placeholder="{{ $placeholder }}"
            @required($required)
            {{ $attributes->class('form-control' . $stateClass) }}
        >{{ $current }}</textarea>

    @elseif ($type === 'select')
        <select
            id="{{ $name }}"
            name="{{ $name }}"
            @required($required)
            {{ $attributes->class('form-select' . $stateClass) }}
        >
            {{ $slot }}
        </select>

    @else
        <input
            type="{{ $type }}"
            id="{{ $name }}"
            name="{{ $name }}"
            value="{{ $current }}"
            placeholder="{{ $placeholder }}"
            @required($required)
            {{ $attributes->class('form-control' . $stateClass) }}
        >
    @endif

    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
