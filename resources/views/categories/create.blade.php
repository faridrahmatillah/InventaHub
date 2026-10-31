@extends('layouts.app')

@section('title', 'Tambah Kategori')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">

    {{-- HEADER --}}
    <x-page-header
        title="Tambah Kategori"
        subtitle="Kelompokkan produk gudang ke dalam kategori yang mudah dikenali."
        :breadcrumbs="[
            ['label' => 'Kategori', 'url' => route('categories.index')],
            ['label' => 'Tambah Kategori'],
        ]"
    >
        <a href="{{ route('categories.index') }}" class="btn btn-secondary">
            <x-icon name="arrow-left" class="h-4 w-4" stroke="2" />
            Kembali
        </a>
    </x-page-header>

    <form action="{{ route('categories.store') }}" method="POST">
        @csrf

        <div class="space-y-5">

            {{-- FORM --}}
            <div class="card divide-y divide-slate-100 overflow-hidden">

                <x-form-section
                    step="01"
                    title="Identitas kategori"
                    description="Nama kategori akan muncul pada tabel produk, filter, dan halaman detail produk."
                    icon="category"
                    tone="blue"
                >
                    <x-input
                        name="name"
                        label="Nama Kategori"
                        placeholder="Contoh: Elektronik, ATK, Peralatan"
                        hint="Gunakan nama yang singkat, jelas, dan tidak duplikat."
                        data-category-name
                        autofocus
                        required
                        :margin="false"
                    />
                </x-form-section>

                <x-form-section
                    step="02"
                    title="Deskripsi kategori"
                    description="Deskripsi bersifat opsional dan membantu admin lain memahami cakupan kategori ini."
                    icon="edit"
                    tone="slate"
                >
                    <x-input
                        name="description"
                        type="textarea"
                        label="Deskripsi (Opsional)"
                        placeholder="Contoh: Perangkat elektronik dan aksesoris kantor."
                        rows="5"
                        hint="Tuliskan singkat jenis produk yang termasuk dalam kategori ini."
                        data-category-description
                        :margin="false"
                    />
                </x-form-section>

                {{-- FOOTER AKSI --}}
                <div class="flex flex-col-reverse gap-3 bg-slate-50/70 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <p class="text-[11px] leading-5 text-slate-500">
                        Kategori baru langsung dapat dipilih saat menambahkan produk.
                    </p>

                    <div class="flex flex-col-reverse gap-2.5 sm:flex-row">
                        <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                            Batalkan
                        </a>

                        <button type="submit" data-loading-text="Menyimpan..." class="btn btn-primary">
                            <x-icon name="check" class="h-4 w-4" stroke="2" />
                            Simpan Kategori
                        </button>
                    </div>
                </div>
            </div>

            {{-- INFORMASI PENDUKUNG --}}
            <aside class="grid gap-5 md:grid-cols-2">

                <section class="card overflow-hidden" aria-label="Pratinjau kategori">
                    <div class="px-5 py-4">
                        <h2 class="section-title">Pratinjau kategori</h2>
                        <p class="section-subtitle">Tampilan ringkas sebelum disimpan.</p>
                    </div>

                    <div class="border-t border-slate-100 p-5">
                        <div class="flex items-center gap-3">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                                <x-icon name="category" class="h-5 w-5" />
                            </span>

                            <div class="min-w-0">
                                <p id="category-preview-name" class="truncate text-sm font-semibold text-slate-900">Nama kategori</p>
                                <p class="mt-0.5 text-[10px] font-medium text-slate-400">Kategori baru · 0 produk</p>
                            </div>
                        </div>

                        <p id="category-preview-description" class="mt-4 border-t border-slate-100 pt-4 text-xs leading-5 text-slate-500">
                            Belum ada deskripsi.
                        </p>
                    </div>
                </section>

                <section class="card p-5">
                    <h2 class="section-title">Tips pengelompokan</h2>

                    <ul class="mt-4 space-y-3.5">
                        <li class="flex gap-2.5">
                            <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-emerald-700">
                                <x-icon name="category" class="h-3 w-3" />
                            </span>
                            <p class="text-xs leading-5 text-slate-500">Gunakan nama yang langsung menggambarkan jenis produk.</p>
                        </li>

                        <li class="flex gap-2.5">
                            <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">
                                <x-icon name="check" class="h-3 w-3" stroke="2.4" />
                            </span>
                            <p class="text-xs leading-5 text-slate-500">Periksa daftar kategori agar tidak membuat data duplikat.</p>
                        </li>

                        <li class="flex gap-2.5">
                            <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-emerald-700">
                                <x-icon name="box" class="h-3 w-3" />
                            </span>
                            <p class="text-xs leading-5 text-slate-500">Kategori siap dipakai tepat setelah data disimpan.</p>
                        </li>
                    </ul>
                </section>

                <div class="info-note md:col-span-2">
                    <x-icon name="info" class="mt-0.5 h-4 w-4 shrink-0" />
                    <p>
                        Kategori yang masih memiliki produk aktif tidak dapat dihapus. Pindahkan atau nonaktifkan produk terlebih dahulu.
                    </p>
                </div>
            </aside>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Pratinjau kategori di panel kanan (hanya tampilan, tidak memengaruhi data).
    (function () {
        const nameInput = document.querySelector('[data-category-name]');
        const descriptionInput = document.querySelector('[data-category-description]');
        const nameOut = document.getElementById('category-preview-name');
        const descriptionOut = document.getElementById('category-preview-description');

        if (!nameInput || !nameOut) return;

        function updatePreview() {
            nameOut.textContent = nameInput.value.trim() || 'Nama kategori';

            const description = descriptionInput?.value.trim();
            descriptionOut.textContent = description || 'Belum ada deskripsi.';
        }

        nameInput.addEventListener('input', updatePreview);
        descriptionInput?.addEventListener('input', updatePreview);

        updatePreview();
    })();
</script>
@endpush
