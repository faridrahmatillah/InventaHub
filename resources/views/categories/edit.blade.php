@extends('layouts.app')

@section('title', 'Edit Kategori')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">

    {{-- HEADER --}}
    <x-page-header
        title="Edit Kategori"
        subtitle="Perbarui nama atau deskripsi kategori yang sudah terdaftar."
        :breadcrumbs="[
            ['label' => 'Kategori', 'url' => route('categories.index')],
            ['label' => $category->name, 'url' => route('categories.show', $category)],
            ['label' => 'Edit Kategori'],
        ]"
    >
        <a href="{{ route('categories.show', $category) }}" class="btn btn-secondary">
            <x-icon name="arrow-left" class="h-4 w-4" stroke="2" />
            Kembali
        </a>
    </x-page-header>

    <form action="{{ route('categories.update', $category) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="space-y-5">

            {{-- FORM --}}
            <div class="card divide-y divide-slate-100 overflow-hidden">

                <x-form-section
                    step="01"
                    title="Identitas kategori"
                    description="Perubahan nama akan langsung terlihat pada seluruh produk yang menggunakan kategori ini."
                    icon="category"
                    tone="amber"
                >
                    <x-input
                        name="name"
                        label="Nama Kategori"
                        :value="$category->name"
                        placeholder="Masukkan nama kategori"
                        hint="Gunakan nama yang singkat dan mudah dikenali."
                        autofocus
                        required
                        :margin="false"
                    />
                </x-form-section>

                <x-form-section
                    step="02"
                    title="Deskripsi kategori"
                    description="Perbarui penjelasan kategori bila cakupan produknya berubah."
                    icon="edit"
                    tone="slate"
                >
                    <x-input
                        name="description"
                        type="textarea"
                        label="Deskripsi (Opsional)"
                        :value="$category->description"
                        placeholder="Masukkan deskripsi kategori..."
                        rows="5"
                        :margin="false"
                    />
                </x-form-section>

                {{-- FOOTER AKSI --}}
                <div class="flex flex-col-reverse gap-3 bg-slate-50/70 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <p class="text-[11px] leading-5 text-slate-500">
                        Perubahan langsung terlihat pada halaman detail kategori.
                    </p>

                    <div class="flex flex-col-reverse gap-2.5 sm:flex-row">
                        <a href="{{ route('categories.show', $category) }}" class="btn btn-secondary">
                            Batalkan
                        </a>

                        <button type="submit" data-loading-text="Menyimpan..." class="btn btn-primary">
                            <x-icon name="check" class="h-4 w-4" stroke="2" />
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>

            {{-- INFORMASI PENDUKUNG --}}
            <aside class="grid gap-5 md:grid-cols-2">

                <section class="card overflow-hidden" aria-label="Ringkasan kategori">
                    <div class="px-5 py-4">
                        <h2 class="section-title">Ringkasan kategori</h2>
                        <p class="section-subtitle">Data yang tersimpan saat ini.</p>
                    </div>

                    <div class="border-t border-slate-100 p-5">
                        <div class="flex items-center gap-3">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                                <x-icon name="category" class="h-5 w-5" />
                            </span>

                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-slate-900">{{ $category->name }}</p>
                                <p class="mt-0.5 text-[10px] font-medium text-slate-400">Kategori #{{ $category->id }}</p>
                            </div>
                        </div>

                        <dl class="mt-5 space-y-3 border-t border-slate-100 pt-4 text-xs">
                            <div class="flex items-center justify-between gap-3">
                                <dt class="text-slate-500">Ditambahkan</dt>
                                <dd class="text-right font-semibold text-slate-800">
                                    <x-date :value="$category->created_at" />
                                </dd>
                            </div>

                            <div class="flex items-center justify-between gap-3">
                                <dt class="text-slate-500">Terakhir diubah</dt>
                                <dd class="text-right font-semibold text-slate-800">
                                    <x-date :value="$category->updated_at" />
                                </dd>
                            </div>
                        </dl>

                        <a href="{{ route('categories.show', $category) }}" class="btn btn-secondary btn-sm mt-5 w-full">
                            Lihat detail kategori
                            <x-icon name="chevron-right" class="h-3.5 w-3.5" stroke="2" />
                        </a>
                    </div>
                </section>

                <div class="warning-note">
                    <x-icon name="alert" class="mt-0.5 h-4 w-4 shrink-0" />
                    <p>
                        Perubahan nama kategori akan langsung terlihat pada seluruh produk yang menggunakannya.
                    </p>
                </div>
            </aside>
        </div>
    </form>
</div>
@endsection
