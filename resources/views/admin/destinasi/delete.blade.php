<x-layouts.admin
    title="Hapus Destinasi"
    :breadcrumbs="['Tata Kelola', 'Kelola Destinasi', 'Hapus Destinasi']"
>
    <div class="mx-auto max-w-3xl space-y-6">

        <x-page-header
            title="Konfirmasi Hapus Destinasi"
            description="Pastikan kembali destinasi yang akan dihapus."
        />

        <x-card>
            <div class="space-y-5">
                <div class="flex items-start gap-4">
                    <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-danger/10">
                        <x-icon name="warning" class="text-danger" />
                    </div>

                    <div>
                        <h2 class="font-heading text-lg font-semibold text-ink">
                            Apakah Anda yakin ingin menghapus destinasi ini?
                        </h2>
                        <p class="mt-2 text-sm leading-6 text-muted">
                            Destinasi yang dihapus tidak dapat dikembalikan.
                            Pastikan informasi destinasi sudah benar sebelum melanjutkan.
                        </p>
                    </div>
                </div>

                <div class="rounded-xl border border-line bg-soft/50 p-5">
                    <p class="text-xs font-medium uppercase tracking-wide text-muted">
                        Destinasi yang akan dihapus
                    </p>

                <h3 class="mt-2 font-heading text-lg font-semibold text-ink">
                    {{ $destinasi->nama }}
                </h3>

                <p class="mt-1 text-sm text-muted">
                    #{{ $destinasi->id_destinasi }} —
                    {{ $destinasi->kecamatan?->nama ?? '-' }},
                    {{ $destinasi->kecamatan?->kabupaten?->nama ?? '-' }}
                </p>

                <div class="mt-3">
                    <span class="inline-flex rounded-full bg-primary/10 px-3 py-1 text-xs font-medium text-primary">
                        {{ $destinasi->kategoriWisata?->nama ?? '-' }}
                    </span>
                </div>

                <div class="flex flex-col-reverse justify-end gap-3 sm:flex-row">
                    <x-button
                        href="{{ route('admin.destinasi.index') }}"
                        variant="outline"
                    >
                        Batal
                    </x-button>

                    <form
                        method="POST"
                        action="{{ route('admin.destinasi.destroy', ['id_destinasi' => $destinasi->id_destinasi]) }}"
                    >
                        @csrf
                        @method('DELETE')

                        <x-button type="submit" variant="danger">
                            <x-icon name="delete" class="mr-2" />
                            Hapus Destinasi
                        </x-button>
                    </form>
                </div>
            </div>
        </x-card>

    </div>
</x-layouts.admin>