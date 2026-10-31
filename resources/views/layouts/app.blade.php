<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') | InventaHub</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#f4f7f5] text-slate-600 antialiased">
<x-flash />

<a href="#main-content"
   class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-[100] focus:rounded-lg focus:bg-slate-900 focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-white">
    Lewati ke konten utama
</a>

@php
    $user = auth()->user();
    $pageTitle = trim($__env->yieldContent('title')) ?: 'Dashboard';
    $roleLabel = $user?->role === 'admin' ? 'Administrator' : 'Staff';

    $initials = $user ? mb_substr(trim($user->name), 0, 1) : '';
    if ($user && preg_match('/\s+(\S)/u', trim($user->name), $match)) {
        $initials .= $match[1];
    }
    $initials = mb_strtoupper($initials);

    // pattern dipakai agar menu tetap aktif di halaman create/edit/show.
    $menus = [
        [
            'label' => 'Ruang kerja',
            'items' => [
                ['name' => 'Dashboard', 'route' => 'dashboard', 'pattern' => 'dashboard', 'icon' => 'dashboard'],
                ['name' => 'Data Barang', 'route' => 'products.index', 'pattern' => 'products.*', 'icon' => 'box'],
                ['name' => 'Transaksi', 'route' => 'transactions.index', 'pattern' => 'transactions.*', 'icon' => 'transaction'],
                ['name' => 'Stok Opname', 'route' => 'stock-opnames.index', 'pattern' => 'stock-opnames.*', 'icon' => 'chart'],
            ],
        ],
    ];

    if ($user && $user->role === 'admin') {
        $menus[] = [
            'label' => 'Pengelolaan',
            'items' => [
                ['name' => 'Kategori', 'route' => 'categories.index', 'pattern' => 'categories.*', 'icon' => 'category'],
                ['name' => 'Pemasok', 'route' => 'suppliers.index', 'pattern' => 'suppliers.*', 'icon' => 'user'],
            ],
        ];
    }
@endphp

@if($user)

<div class="min-h-screen">

    {{-- Overlay mobile --}}
    <div id="sidebar-overlay"
         class="fixed inset-0 z-40 hidden bg-slate-950/40 backdrop-blur-sm lg:hidden"></div>

    {{-- Sidebar --}}
    <aside id="sidebar"
           class="fixed inset-y-0 left-0 z-50 flex w-[272px] -translate-x-full flex-col border-r border-slate-200/80 bg-white transition-transform duration-300 ease-out lg:translate-x-0">

        {{-- Brand --}}
        <div class="flex h-[72px] shrink-0 items-center gap-3 px-4">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-700 text-white shadow-[0_10px_20px_-12px_rgba(4,120,87,0.7)]">
                <x-icon name="warehouse" class="h-5 w-5" />
            </div>

            <div class="min-w-0 flex-1">
                <p class="truncate text-[15px] font-bold tracking-tight text-slate-950">InventaHub</p>
                <p class="text-[11px] font-medium uppercase tracking-[0.14em] text-slate-500">Inventory workspace</p>
            </div>

            <button id="close-sidebar"
                    class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 lg:hidden"
                    aria-label="Tutup sidebar">
                <x-icon name="close" class="h-5 w-5" stroke="2" />
            </button>
        </div>

        {{-- Ruang kerja --}}
        <div class="mx-3 flex items-center gap-2.5 rounded-xl border border-slate-200/70 bg-slate-50/80 p-3">
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white text-[10px] font-bold text-slate-500 ring-1 ring-slate-200">
                GU
            </span>

            <div class="min-w-0 flex-1">
                <p class="truncate text-xs font-semibold text-slate-800">Gudang Utama</p>
                <p class="truncate text-[10px] text-slate-500">Ruang kerja inventaris</p>
            </div>

            <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-emerald-500" title="Ruang kerja aktif"></span>
        </div>

        {{-- Navigasi --}}
        <nav class="flex-1 overflow-y-auto px-3 py-5" aria-label="Navigasi utama">
            @foreach($menus as $menu)
                <div class="{{ $loop->first ? '' : 'mt-7' }}">
                    <p class="nav-section-label mb-2">{{ $menu['label'] }}</p>

                    <div class="space-y-1">
                        @foreach($menu['items'] as $item)
                            @php
                                $active = request()->routeIs($item['pattern']);
                            @endphp

                            <a href="{{ route($item['route']) }}"
                               @if($active) aria-current="page" @endif
                               class="nav-link group {{ $active ? 'nav-link-active' : '' }}">

                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg transition-colors {{ $active ? 'bg-white/10 text-white' : 'bg-slate-100/80 text-slate-400 group-hover:bg-white group-hover:text-slate-600' }}">
                                    <x-icon :name="$item['icon']" class="h-4 w-4" />
                                </span>

                                <span class="flex-1">{{ $item['name'] }}</span>

                                @if($active)
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-300" aria-hidden="true"></span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </nav>

        {{-- Footer sidebar --}}
        <div class="shrink-0 border-t border-slate-100 px-5 py-4">
            <p class="text-[11px] font-medium text-slate-500">InventaHub</p>
            <p class="mt-0.5 text-[10px] text-slate-400">Gudang Utama · Inventory workspace</p>
        </div>
    </aside>

    {{-- Konten --}}
    <div class="min-h-screen lg:pl-[272px]">

        {{-- Header --}}
        <header class="sticky top-0 z-30 border-b border-slate-200/70 bg-white/80 backdrop-blur-xl">
            <div class="flex h-[68px] items-center gap-3 px-4 sm:px-6 lg:px-8">

                <button id="open-sidebar"
                        class="rounded-xl border border-slate-200 bg-white p-2 text-slate-600 shadow-sm transition hover:bg-slate-50 lg:hidden"
                        aria-label="Buka sidebar" aria-controls="sidebar" aria-expanded="false">
                    <x-icon name="menu" class="h-5 w-5" stroke="2" />
                </button>

                <div class="min-w-0 flex-1">
                    <nav class="flex items-center gap-2 text-[13px]" aria-label="Breadcrumb">
                        <a href="{{ route('dashboard') }}"
                           class="hidden items-center gap-1.5 text-slate-400 transition hover:text-slate-700 sm:inline-flex">
                            <x-icon name="dashboard" class="h-3.5 w-3.5" />
                            InventaHub
                        </a>

                        <span class="hidden h-3.5 w-px bg-slate-200 sm:block"></span>

                        <span class="truncate font-semibold text-slate-800">{{ $pageTitle }}</span>
                    </nav>

                    <p class="mt-0.5 hidden text-[11px] text-slate-400 md:block">
                        {{ now()->locale('id')->translatedFormat('l, d F Y') }}
                    </p>
                </div>

                <div class="flex shrink-0 items-center gap-2.5">
                    @unless(request()->routeIs('transactions.create'))
                        <a href="{{ route('transactions.create') }}"
                           class="btn btn-primary btn-sm hidden sm:inline-flex">
                            <x-icon name="plus" class="h-3.5 w-3.5" stroke="2" />
                            Transaksi
                        </a>
                    @endunless

                    <span class="hidden h-6 w-px bg-slate-200 sm:block"></span>

                    {{-- Menu profil --}}
                    <div class="relative">
                        <button id="profile-button"
                                type="button"
                                class="flex items-center gap-2 rounded-full border border-slate-200 bg-white py-1 pl-1 pr-2.5 shadow-sm transition hover:bg-slate-50 focus:outline-none focus-visible:ring-4 focus-visible:ring-slate-200"
                                aria-haspopup="menu"
                                aria-expanded="false"
                                aria-controls="profile-menu">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-700 text-[11px] font-bold text-white">
                                {{ $initials }}
                            </span>

                            <span class="hidden text-left md:block">
                                <span class="block max-w-[130px] truncate text-xs font-semibold leading-4 text-slate-800">{{ $user->name }}</span>
                                <span class="block text-[10px] leading-4 text-slate-500">{{ $roleLabel }}</span>
                            </span>

                            <x-icon name="chevron-down" class="h-3.5 w-3.5 text-slate-400" />
                        </button>

                        <div id="profile-menu"
                             class="absolute right-0 z-50 mt-2 hidden w-64 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl"
                             role="menu">
                            <div class="border-b border-slate-100 px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-700 text-xs font-bold text-white">
                                        {{ $initials }}
                                    </span>

                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-semibold text-slate-900">{{ $user->name }}</p>
                                        <p class="truncate text-[11px] text-slate-500">{{ $user->email }}</p>
                                    </div>
                                </div>

                                <div class="mt-3 flex items-center justify-between">
                                    <span class="text-[10px] font-medium uppercase tracking-[0.14em] text-slate-400">Peran akun</span>
                                    <x-badge tone="info">{{ $roleLabel }}</x-badge>
                                </div>
                            </div>

                            <div class="p-2">
                                <a href="{{ route('dashboard') }}"
                                   class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-[13px] font-medium text-slate-600 transition hover:bg-slate-50 hover:text-slate-900"
                                   role="menuitem">
                                    <x-icon name="dashboard" class="h-4 w-4 text-slate-400" />
                                    Dashboard
                                </a>

                                <a href="{{ route('products.index') }}"
                                   class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-[13px] font-medium text-slate-600 transition hover:bg-slate-50 hover:text-slate-900"
                                   role="menuitem">
                                    <x-icon name="box" class="h-4 w-4 text-slate-400" />
                                    Data barang
                                </a>
                            </div>

                            <div class="border-t border-slate-100 p-2">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-[13px] font-medium text-red-600 transition hover:bg-red-50 focus:outline-none focus-visible:ring-4 focus-visible:ring-red-100"
                                            role="menuitem">
                                        <x-icon name="logout" class="h-4 w-4" />
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- Isi halaman --}}
        <main id="main-content" class="mx-auto w-full max-w-[1440px] px-4 py-6 sm:px-6 lg:px-10 lg:py-8">
            @yield('content')
        </main>

        <footer class="mx-auto w-full max-w-[1500px] px-4 pb-7 text-center text-[11px] text-slate-400 sm:px-6 lg:px-10">
            &copy; {{ date('Y') }} InventaHub. Inventory Management System.
        </footer>

    </div>
</div>

{{-- Modal konfirmasi global untuk aksi hapus/nonaktifkan --}}
<div id="confirm-modal" class="fixed inset-0 z-[70] hidden items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="confirm-title" aria-describedby="confirm-message">
    <div class="absolute inset-0 bg-slate-950/50 backdrop-blur-sm" data-confirm-cancel></div>

    <div class="relative w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl">
        <div class="flex items-start gap-4 p-6">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-red-50 text-red-600">
                <x-icon name="alert" class="h-5 w-5" />
            </div>

            <div class="min-w-0">
                <h2 id="confirm-title" class="text-base font-semibold text-slate-900">Konfirmasi tindakan</h2>
                <p id="confirm-message" class="mt-1 text-sm leading-6 text-slate-500">Apakah Anda yakin ingin melanjutkan?</p>
            </div>
        </div>

        <div class="flex flex-col-reverse gap-2 border-t border-slate-100 bg-slate-50 px-6 py-4 sm:flex-row sm:justify-end">
            <button type="button" data-confirm-cancel class="btn btn-secondary">
                Batal
            </button>

            <button type="button" id="confirm-submit" class="btn btn-danger-solid">
                Ya, lanjutkan
            </button>
        </div>
    </div>
</div>

@else

{{-- Tampilan khusus halaman login, register, dan welcome --}}
<div class="min-h-screen bg-[#f4f7f5] lg:grid lg:grid-cols-[minmax(0,1fr)_minmax(0,560px)]">

    {{-- Panel branding (desktop) --}}
    <aside class="relative hidden overflow-hidden bg-emerald-950 p-12 text-white lg:flex lg:flex-col lg:justify-between xl:p-16">
        <div class="panel-grid pointer-events-none absolute inset-0 opacity-80"></div>
        <div class="pointer-events-none absolute -right-24 top-1/4 h-80 w-80 rounded-full bg-emerald-500/20 blur-3xl"></div>
        <div class="pointer-events-none absolute -left-24 bottom-0 h-72 w-72 rounded-full bg-emerald-500/10 blur-3xl"></div>

        <div class="relative flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-400 text-emerald-950">
                <x-icon name="warehouse" class="h-5 w-5" />
            </div>

            <div>
                <p class="text-base font-bold tracking-tight text-white">InventaHub</p>
                <p class="text-[11px] font-medium uppercase tracking-[0.16em] text-slate-300">Inventory workspace</p>
            </div>

            <span class="ml-auto rounded-full border border-white/10 bg-white/5 px-2.5 py-1 text-[10px] font-medium uppercase tracking-[0.14em] text-slate-300">
                Sistem gudang
            </span>
        </div>

        <div class="relative max-w-lg py-12">
            <div class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-300">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                Kendali inventaris
            </div>

            <h1 class="mt-6 text-[38px] font-semibold leading-[1.08] tracking-[-0.035em] text-white xl:text-[44px]">
                Inventaris rapi.<br>
                Keputusan lebih cepat.
            </h1>

            <p class="mt-5 max-w-md text-sm leading-7 text-slate-400">
                Satu ruang kerja untuk mencatat pergerakan stok, memantau produk menipis, dan menjaga data gudang tetap akurat.
            </p>

            <div class="mt-10 space-y-3">
                <div class="flex items-start gap-3 rounded-xl border border-white/10 bg-white/[0.03] p-3.5">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/10 text-white">
                        <x-icon name="transaction" class="h-4 w-4" />
                    </span>
                    <div>
                        <p class="text-[13px] font-semibold text-white">Stok otomatis menyesuaikan</p>
                        <p class="mt-0.5 text-[11px] leading-5 text-slate-400">Setiap transaksi langsung memperbarui jumlah stok.</p>
                    </div>
                </div>

                <div class="flex items-start gap-3 rounded-xl border border-white/10 bg-white/[0.03] p-3.5">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/10 text-white">
                        <x-icon name="alert" class="h-4 w-4" />
                    </span>
                    <div>
                        <p class="text-[13px] font-semibold text-white">Peringatan stok menipis</p>
                        <p class="mt-0.5 text-[11px] leading-5 text-slate-400">Temukan produk yang perlu segera diisi ulang.</p>
                    </div>
                </div>

                <div class="flex items-start gap-3 rounded-xl border border-white/10 bg-white/[0.03] p-3.5">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/10 text-white">
                        <x-icon name="user" class="h-4 w-4" />
                    </span>
                    <div>
                        <p class="text-[13px] font-semibold text-white">Akses admin dan staff</p>
                        <p class="mt-0.5 text-[11px] leading-5 text-slate-400">Hak akses tiap peran sudah dipisahkan dengan jelas.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="relative flex items-center justify-between text-[11px] text-slate-500">
            <p>&copy; {{ date('Y') }} InventaHub</p>
            <p>Inventory Management System</p>
        </div>
    </aside>

    {{-- Panel form --}}
    <main class="flex min-h-screen flex-col bg-white">

        {{-- Brand mobile --}}
        <div class="flex h-16 items-center border-b border-slate-100 px-5 lg:hidden">
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-700 text-white">
                    <x-icon name="warehouse" class="h-4 w-4" />
                </div>
                <div>
                    <p class="text-sm font-bold tracking-tight text-slate-950">InventaHub</p>
                    <p class="text-[11px] font-medium uppercase tracking-[0.14em] text-slate-500">Inventory workspace</p>
                </div>
            </div>
        </div>

        <div class="flex flex-1 items-center justify-center px-5 py-10 sm:px-10">
            <div class="w-full max-w-[400px]">
                @yield('content')
            </div>
        </div>

        <footer class="px-5 pb-6 text-center text-[11px] text-slate-400 lg:hidden">
            &copy; {{ date('Y') }} InventaHub. Inventory Management System.
        </footer>
    </main>
</div>

@endif

<script>
    // ============================================================
    // Sidebar mobile
    // ============================================================
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    const openButton = document.getElementById('open-sidebar');
    const closeButton = document.getElementById('close-sidebar');

    function openSidebar() {
        sidebar?.classList.remove('-translate-x-full');
        overlay?.classList.remove('hidden');
        document.body.classList.add('overflow-hidden', 'lg:overflow-auto');
        openButton?.setAttribute('aria-expanded', 'true');
    }

    function closeSidebar() {
        sidebar?.classList.add('-translate-x-full');
        overlay?.classList.add('hidden');
        document.body.classList.remove('overflow-hidden', 'lg:overflow-auto');
        openButton?.setAttribute('aria-expanded', 'false');
    }

    openButton?.addEventListener('click', openSidebar);
    closeButton?.addEventListener('click', closeSidebar);
    overlay?.addEventListener('click', closeSidebar);

    // Tutup sidebar otomatis ketika layar berubah ke ukuran desktop.
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
            closeSidebar();
        }
    });

    // ============================================================
    // Menu profil header
    // ============================================================
    const profileButton = document.getElementById('profile-button');
    const profileMenu = document.getElementById('profile-menu');

    function closeProfileMenu() {
        if (!profileMenu || profileMenu.classList.contains('hidden')) return;

        profileMenu.classList.add('hidden');
        profileButton?.setAttribute('aria-expanded', 'false');
    }

    profileButton?.addEventListener('click', (event) => {
        event.stopPropagation();
        const isOpen = profileMenu?.classList.contains('hidden') === false;

        if (isOpen) {
            closeProfileMenu();
            return;
        }

        profileMenu?.classList.remove('hidden');
        profileButton.setAttribute('aria-expanded', 'true');
    });

    document.addEventListener('click', (event) => {
        if (!profileMenu || profileMenu.classList.contains('hidden')) return;
        if (profileMenu.contains(event.target) || profileButton?.contains(event.target)) return;

        closeProfileMenu();
    });

    // ============================================================
    // Modal konfirmasi hapus
    // ============================================================
    const confirmModal = document.getElementById('confirm-modal');
    const confirmTitle = document.getElementById('confirm-title');
    const confirmMessage = document.getElementById('confirm-message');
    const confirmSubmit = document.getElementById('confirm-submit');

    let pendingForm = null;
    let lastFocusedElement = null;

    function showConfirmModal(form) {
        if (!confirmModal) return;

        pendingForm = form;
        lastFocusedElement = document.activeElement;

        confirmTitle.textContent = form.dataset.confirmTitle || 'Konfirmasi tindakan';
        confirmMessage.textContent = form.dataset.confirm || 'Apakah Anda yakin ingin melanjutkan?';
        confirmSubmit.textContent = form.dataset.confirmAction || 'Ya, lanjutkan';

        confirmModal.classList.remove('hidden');
        confirmModal.classList.add('flex');
        confirmSubmit.focus();
    }

    function hideConfirmModal() {
        if (!confirmModal || confirmModal.classList.contains('hidden')) return;

        confirmModal.classList.add('hidden');
        confirmModal.classList.remove('flex');
        pendingForm = null;

        if (lastFocusedElement instanceof HTMLElement) {
            lastFocusedElement.focus();
        }
    }

    document.querySelectorAll('[data-confirm-cancel]').forEach((element) => {
        element.addEventListener('click', hideConfirmModal);
    });

    confirmSubmit?.addEventListener('click', () => {
        const form = pendingForm;

        if (!form) {
            hideConfirmModal();
            return;
        }

        // requestSubmit() tetap menjalankan validasi & event submit,
        // sehingga indikator loading pada tombol tetap bekerja.
        form.dataset.confirmed = '1';
        hideConfirmModal();
        form.requestSubmit();
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') return;

        hideConfirmModal();
        closeSidebar();
        closeProfileMenu();
    });

    // ============================================================
    // Interaksi form: konfirmasi & loading state
    // ============================================================
    const spinnerSvg = '<svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">'
        + '<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>'
        + '<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>'
        + '</svg>';

    document.addEventListener('submit', (event) => {
        const form = event.target;

        if (!(form instanceof HTMLFormElement)) {
            return;
        }

        // Form dengan data-confirm ditahan dulu, lalu dijalankan dari modal.
        if (form.dataset.confirm && form.dataset.confirmed !== '1') {
            event.preventDefault();
            showConfirmModal(form);
            return;
        }

        const button = event.submitter || form.querySelector('button[type="submit"]');

        if (!button || button.disabled) {
            return;
        }

        button.dataset.originalHtml = button.innerHTML;
        button.disabled = true;
        button.classList.add('cursor-wait', 'opacity-75');
        button.innerHTML = spinnerSvg + '<span>' + (button.dataset.loadingText || 'Memproses...') + '</span>';
    });

    // ============================================================
    // Tampilkan / sembunyikan password
    // ============================================================
    document.addEventListener('click', (event) => {
        const toggle = event.target.closest('[data-password-toggle]');

        if (!toggle) return;

        const input = document.getElementById(toggle.dataset.target);

        if (!input) return;

        const willShow = input.type === 'password';

        input.type = willShow ? 'text' : 'password';
        toggle.setAttribute('aria-label', willShow ? 'Sembunyikan password' : 'Tampilkan password');
        toggle.classList.toggle('text-blue-600', willShow);
    });

    // ============================================================
    // Flash toast: tutup otomatis untuk semua jenis pesan.
    // ============================================================
    document.querySelectorAll('[data-flash][data-autohide]').forEach((flash) => {
        const delay = Number(flash.dataset.autohide) || 6000;

        window.setTimeout(() => {
            flash.style.transition = 'opacity 300ms ease, transform 300ms ease';
            flash.style.opacity = '0';
            flash.style.transform = 'translateY(-4px)';

            window.setTimeout(() => flash.remove(), 320);
        }, delay);
    });
</script>

@stack('scripts')

</body>
</html>
