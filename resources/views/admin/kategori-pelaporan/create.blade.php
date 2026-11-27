@php
    $crumbTataKelola = new class implements \Illuminate\Contracts\Support\Htmlable {
        public function toHtml()
        {
            return '<a href="'.e(route('admin.dashboard')).'" class="transition hover:text-primary">Tata Kelola</a>';
        }
    };

    $crumbKategori = new class implements \Illuminate\Contracts\Support\Htmlable {
        public function toHtml()
        {
            return '<a href="'.e(route('admin.kategori.index')).'" class="transition hover:text-primary">Kategori Pelaporan</a>';
        }
    };
@endphp

<x-layouts.admin
    title="Tambah Kategori"
    :breadcrumbs="[$crumbTataKelola, $crumbKategori, 'Tambah Kategori']"
>
    <div class="mx-auto max-w-5xl space-y-6">

        <x-page-header
            title="Tambah Kategori Pelaporan"
            description="Tambahkan kategori kerusakan fasilitas wisata baru ke dalam sistem SwaraWisata."
        />

        <form
            method="POST"
            action="{{ route('admin.kategori.store') }}"
            class="space-y-6"
        >
            @csrf

            <x-card>
                <div class="space-y-5">
                    <div>
                        <h2 class="font-heading text-lg font-semibold text-ink">
                            Informasi Kategori
                        </h2>
                        <p class="mt-1 text-sm text-muted">
                            Lengkapi informasi kategori kerusakan yang akan ditambahkan.
                        </p>
                    </div>

                    <x-form.input
                        name="nama"
                        label="Nama Kategori Kerusakan"
                        placeholder="Contoh: Jalan Rusak"
                        maxlength="100"
                        required
                        counter
                    />

                    <x-form.textarea
                        name="deskripsi"
                        label="Deskripsi Cakupan Fasilitas"
                        placeholder="Jelaskan jenis kerusakan atau fasilitas yang termasuk dalam kategori ini..."
                        rows="5"
                    />
                </div>
            </x-card>

            <div class="flex flex-col-reverse justify-end gap-3 sm:flex-row">
                <x-button
                    href="{{ route('admin.kategori.index') }}"
                    variant="outline"
                >
                    Batal
                </x-button>

                <x-button type="submit" variant="primary">
                    Simpan Kategori
                </x-button>
            </div>
        </form>

    </div>
</x-layouts.admin>
