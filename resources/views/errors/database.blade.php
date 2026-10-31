<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layanan sementara tidak tersedia | InventaHub</title>
    @vite(['resources/css/app.css'])
</head>
<body class="flex min-h-screen items-center justify-center bg-[#f4f7f5] px-4 py-10 text-slate-600 antialiased">
    <main class="w-full max-w-lg">
        <div class="mb-6 flex items-center justify-center gap-3">
            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-700 text-white shadow-sm">
                <x-icon name="warehouse" class="h-5 w-5" />
            </span>
            <span class="text-lg font-bold tracking-tight text-slate-950">InventaHub</span>
        </div>

        <section class="rounded-2xl border border-slate-200/80 bg-white p-7 text-center shadow-[0_1px_2px_rgba(15,23,42,0.03),0_16px_40px_-30px_rgba(15,23,42,0.25)] sm:p-9">
            <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-50 text-amber-700">
                <x-icon name="alert" class="h-6 w-6" />
            </span>

            <p class="mt-5 text-xs font-semibold uppercase tracking-[0.16em] text-amber-700">Gangguan sementara</p>
            <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-950">Data belum dapat dimuat</h1>
            <p class="mx-auto mt-3 max-w-sm text-sm leading-6 text-slate-500">
                Sedang terjadi gangguan saat mengakses data. Silakan coba beberapa saat lagi.
            </p>

            <a href="{{ url()->current() }}" class="btn btn-primary mt-7 w-full sm:w-auto">
                <x-icon name="arrow-right" class="h-4 w-4" />
                Coba lagi
            </a>
        </section>

        <p class="mt-5 text-center text-xs text-slate-400">
            &copy; {{ date('Y') }} InventaHub. Inventory Management System.
        </p>
    </main>
</body>
</html>
