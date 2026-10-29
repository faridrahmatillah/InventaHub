{{--
    Komponen flash message reusable.
    Dipanggil di layout utama dengan: <x-flash />

    Membaca pesan dari session yang dikirim controller lewat ->with('success', '...')
    atau ->with('error', '...'). Semua output memakai {{ }} sehingga otomatis aman dari XSS.
--}}
@foreach ([
    'success' => 'alert-success',
    'error'   => 'alert-danger',
    'warning' => 'alert-warning',
    'info'    => 'alert-info',
] as $type => $alertClass)
    @if (session()->has($type))
        <div class="alert {{ $alertClass }} alert-dismissible fade show" role="alert">
            {{ session($type) }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif
@endforeach
