<form action="{{ $action }}" method="POST" class="card divide-y divide-slate-100 overflow-hidden">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-5 p-5 sm:grid-cols-2 sm:p-6">
        <x-input name="name" label="Nama pemasok" :value="$supplier?->name" required autofocus />
        <x-input name="contact_name" label="Nama kontak" :value="$supplier?->contact_name" />
        <x-input name="phone" label="Nomor telepon" :value="$supplier?->phone" />
        <x-input name="email" type="email" label="Email" :value="$supplier?->email" />
        <div class="sm:col-span-2">
            <x-input name="address" type="textarea" label="Alamat" :value="$supplier?->address" rows="3" />
        </div>
    </div>

    <div class="flex justify-end gap-2 bg-slate-50/70 px-5 py-4 sm:px-6">
        <a href="{{ $cancelUrl }}" class="btn btn-secondary">Batal</a>
        <button type="submit" class="btn btn-primary">
            <x-icon name="check" class="h-4 w-4" />
            {{ $submitLabel }}
        </button>
    </div>
</form>
