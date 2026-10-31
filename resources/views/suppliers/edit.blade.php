@extends('layouts.app')

@section('title', 'Edit Pemasok')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <x-page-header
        title="Edit Pemasok"
        subtitle="Perbarui informasi kontak pemasok."
        :breadcrumbs="[
            ['label' => 'Pemasok', 'url' => route('suppliers.index')],
            ['label' => $supplier->name, 'url' => route('suppliers.show', $supplier)],
            ['label' => 'Edit'],
        ]"
    >
        <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-secondary">
            <x-icon name="arrow-left" class="h-4 w-4" />
            Kembali
        </a>
    </x-page-header>

    @include('suppliers._form', [
        'action' => route('suppliers.update', $supplier),
        'method' => 'PUT',
        'supplier' => $supplier,
        'cancelUrl' => route('suppliers.show', $supplier),
        'submitLabel' => 'Simpan Perubahan',
    ])
</div>
@endsection
