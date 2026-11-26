<x-layouts.pengelola title="Detail Laporan">

    <div class="space-y-6">

        <x-page-header
            title="Detail Laporan"
            description="Informasi laporan kerusakan dan riwayat penanganan."
        />

        <x-card>
            <div class="space-y-3">
                <h2 class="font-heading text-xl font-bold text-ink">
                    {{ $laporan->judul }}
                </h2>

                <p class="text-sm text-muted">
                    ID Laporan: #{{ $laporan->id_laporan }}
                </p>

                <p class="text-sm text-muted">
                    Destinasi: {{ $laporan->destinasi?->nama ?? '-' }}
                </p>

                <p class="text-sm text-muted">
                    Kategori: {{ $laporan->kategoriKerusakan?->nama ?? '-' }}
                </p>

                <p class="text-sm text-ink">
                    {{ $laporan->deskripsi }}
                </p>

                <p class="text-sm text-muted">
                    Lokasi: {{ $laporan->lokasi_spesifik ?? '-' }}
                </p>
            </div>
        </x-card>

        <x-card>
            <h2 class="mb-4 font-heading text-lg font-bold text-ink">
                Riwayat Penanganan
            </h2>

            @forelse ($laporan->riwayat as $riwayat)
                <div class="border-b border-line py-4 last:border-0">
                    <p class="font-semibold text-ink">
                        {{ $riwayat->jenis_tindakan === 'penyelesaian'
                            ? 'Penyelesaian'
                            : 'Tindak Lanjut' }}
                    </p>

                    <p class="mt-1 text-sm text-muted">
                        {{ $riwayat->deskripsi }}
                    </p>

                    <p class="mt-1 text-xs text-subtle">
                        Pengelola: {{ $riwayat->pengelola?->username ?? '-' }}
                    </p>

                    <div class="mt-3 flex flex-wrap gap-3">
                        @foreach ($riwayat->foto as $foto)
                            <img
                                src="{{ $foto->url }}"
                                alt="Foto bukti penanganan"
                                class="h-24 w-24 rounded-lg object-cover"
                            />
                        @endforeach
                    </div>
                </div>
            @empty
                <p class="text-sm text-muted">
                    Belum ada riwayat penanganan.
                </p>
            @endforelse
        </x-card>

        {{-- Form tindak lanjut --}}
        <x-card>
            <h2 id="form-tindak-lanjut" class="mb-4 font-heading text-lg font-bold text-ink">
                Tambahkan Tindak Lanjut
            </h2>

            <form
                action="{{ route('pengelola.laporan.riwayat.store', $laporan->id_laporan) }}"
                method="POST"
                enctype="multipart/form-data"
                class="space-y-4"
            >
                @csrf

                <input
                    type="hidden"
                    name="jenis_tindakan"
                    value="tindak_lanjut"
                />

                <div>
                    <label class="mb-2 block text-sm font-semibold text-ink">
                        Deskripsi Tindak Lanjut
                    </label>

                    <textarea
                        name="deskripsi"
                        rows="4"
                        required
                        class="w-full rounded-lg border border-line p-3 text-sm focus:border-primary focus:outline-none"
                        placeholder="Jelaskan tindakan yang dilakukan..."
                    >{{ old('deskripsi') }}</textarea>

                    @error('deskripsi')
                        <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-ink">
                        Foto Pendukung (Opsional)
                    </label>

                    <input
                        type="file"
                        name="foto[]"
                        multiple
                        accept="image/*"
                        class="w-full text-sm"
                    />
                </div>

                <button
                    type="submit"
                    class="rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-hover"
                >
                    Simpan Tindak Lanjut
                </button>
            </form>
        </x-card>

        {{-- Form bukti penyelesaian --}}
        <x-card>
            <h2 id="form-penyelesaian" class="mb-4 font-heading text-lg font-bold text-ink">
                Update Bukti Penyelesaian
            </h2>

            <form
                action="{{ route('pengelola.laporan.riwayat.store', $laporan->id_laporan) }}"
                method="POST"
                enctype="multipart/form-data"
                class="space-y-4"
            >
                @csrf

                <input
                    type="hidden"
                    name="jenis_tindakan"
                    value="penyelesaian"
                />

                <div>
                    <label class="mb-2 block text-sm font-semibold text-ink">
                        Keterangan Penyelesaian
                    </label>

                    <textarea
                        name="deskripsi"
                        rows="4"
                        required
                        class="w-full rounded-lg border border-line p-3 text-sm focus:border-primary focus:outline-none"
                        placeholder="Jelaskan hasil penyelesaian kerusakan..."
                    >{{ old('deskripsi') }}</textarea>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-ink">
                        Foto Bukti Penyelesaian
                    </label>

                    <input
                        type="file"
                        name="foto[]"
                        multiple
                        accept="image/*"
                        required
                        class="w-full text-sm"
                    />
                </div>

                <button
                    type="submit"
                    class="rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-hover"
                >
                    Simpan Bukti Penyelesaian
                </button>
            </form>
        </x-card>

    </div>

</x-layouts.pengelola>