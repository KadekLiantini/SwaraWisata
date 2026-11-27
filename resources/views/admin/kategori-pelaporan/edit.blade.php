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
    title="Edit Kategori"
    :breadcrumbs="[$crumbTataKelola, $crumbKategori, 'Edit Kategori']"
>
    <div class="mx-auto max-w-5xl space-y-6">

        <div>
            <h1 class="font-heading text-2xl font-bold text-ink">
                Edit Kategori Pelaporan
            </h1>
            <p class="mt-1 text-sm text-muted">
                Perbarui informasi kategori kerusakan fasilitas wisata.
            </p>
        </div>

        <form
            method="POST"
            action="{{ route('admin.kategori.update', ['id_kategori_kerusakan' => $kategori->id_kategori_kerusakan]) }}"
            class="space-y-6"
        >
            @csrf
            @method('PUT')

            <div class="space-y-5 rounded-xl border border-line bg-white p-5 shadow-sm sm:p-6">
                <div>
                    <h2 class="font-heading text-lg font-semibold text-ink">
                        Informasi Kategori
                    </h2>
                    <p class="mt-1 text-sm text-muted">
                        Ubah nama dan deskripsi cakupan fasilitas kategori ini.
                    </p>
                </div>

                <div>
                    <label for="nama" class="mb-2 block text-sm font-semibold text-ink">
                        Nama Kategori Kerusakan
                    </label>

                    <input
                        type="text"
                        id="nama"
                        name="nama"
                        value="{{ old('nama', $kategori->nama) }}"
                        maxlength="100"
                        required
                        placeholder="Contoh: Jalan Rusak"
                        class="w-full rounded-lg border border-line bg-white px-3 py-2.5 text-sm text-ink outline-none transition placeholder:text-subtle focus:border-primary focus:ring-2 focus:ring-primary/10"
                    >

                    @error('nama')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="deskripsi" class="mb-2 block text-sm font-semibold text-ink">
                        Deskripsi Cakupan Fasilitas
                    </label>

                    <textarea
                        id="deskripsi"
                        name="deskripsi"
                        rows="5"
                        placeholder="Jelaskan jenis kerusakan atau fasilitas yang termasuk dalam kategori ini..."
                        class="w-full resize-y rounded-lg border border-line bg-white px-3 py-2.5 text-sm text-ink outline-none transition placeholder:text-subtle focus:border-primary focus:ring-2 focus:ring-primary/10"
                    >{{ old('deskripsi', $kategori->deskripsi) }}</textarea>

                    @error('deskripsi')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex flex-col-reverse justify-end gap-3 sm:flex-row">
                <x-button
                    href="{{ route('admin.kategori.index') }}"
                    variant="outline"
                >
                    Batal
                </x-button>

                <x-button type="submit" variant="primary">
                    Simpan Perubahan
                </x-button>
            </div>
        </form>

    </div>
</x-layouts.admin>
