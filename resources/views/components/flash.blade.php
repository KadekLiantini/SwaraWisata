{{--
    Menampilkan pesan session otomatis. Sudah dipasang di semua layout.
    Dari controller: return back()->with('success', 'Laporan berhasil dikirim');
    Kunci yang didukung: success, error, info
--}}
@foreach (['success', 'error', 'info'] as $type)
    @if (session($type))
        <x-alert :variant="$type" class="mb-6">{{ session($type) }}</x-alert>
    @endif
@endforeach
