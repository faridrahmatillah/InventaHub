@extends('layouts.app')

@section('title', 'Tambah Pemasok')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <x-page-header
        title="Tambah Pemasok"
        subtitle="Simpan informasi pemasok untuk mencatat sumber barang masuk."
        :breadcrumbs="[
            ['label' => 'Pemasok', 'url' => route('suppliers.index')],
            ['label' => 'Tambah Pemasok'],
        ]"
    >
        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
            <x-icon name="arrow-left" class="h-4 w-4" />
            Kembali
        </a>
    </x-page-header>

    @include('suppliers._form', [
        'action' => route('suppliers.store'),
        'method' => 'POST',
        'supplier' => null,
        'cancelUrl' => route('suppliers.index'),
        'submitLabel' => 'Simpan Pemasok',
    ])
</div>
@endsection
