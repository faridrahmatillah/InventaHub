{{--
    Menampilkan tanggal dengan aman, termasuk jika nilainya masih berupa string
    dari database. Jangan memanggil ->format() langsung pada nilai string.

    Contoh:
    <x-date :value="$transaction->transaction_date" />
    <x-date :value="$product->created_at" format="d M Y, H:i" />
--}}
@props([
    'value' => null,
    'format' => 'd M Y',
])

@if ($value)
    {{ \Carbon\Carbon::parse($value)->locale('id')->translatedFormat($format) }}
@else
    -
@endif
