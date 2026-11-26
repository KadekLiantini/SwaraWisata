document.addEventListener('DOMContentLoaded', () => {
    const kabupatenSelect = document.getElementById('id_kabupaten');
    const kecamatanSelect = document.getElementById('id_kecamatan');

    if (!kabupatenSelect || !kecamatanSelect) return;

    const kecamatanUrl = kabupatenSelect.dataset.kecamatanUrl;
    const oldKecamatan = kecamatanSelect.dataset.oldValue || '';

    async function muatKecamatan(idKabupaten, selectedId = '') {
        kecamatanSelect.innerHTML =
            '<option value="">Memuat kecamatan...</option>';
        kecamatanSelect.disabled = true;

        if (!idKabupaten) {
            kecamatanSelect.innerHTML =
                '<option value="">Pilih kabupaten terlebih dahulu</option>';
            return;
        }

        try {
            const response = await fetch(
                kecamatanUrl.replace('__ID__', idKabupaten),
                { headers: { Accept: 'application/json' } }
            );

            if (!response.ok) {
                throw new Error('Gagal mengambil data kecamatan.');
            }

            const kecamatan = await response.json();

            kecamatanSelect.innerHTML =
                '<option value="">Pilih Kecamatan...</option>';

            kecamatan.forEach(item => {
                const option = document.createElement('option');
                option.value = item.id_kecamatan;
                option.textContent = item.nama;
                option.selected =
                    String(item.id_kecamatan) === String(selectedId);

                kecamatanSelect.appendChild(option);
            });

            kecamatanSelect.disabled = false;
        } catch (error) {
            kecamatanSelect.innerHTML =
                '<option value="">Gagal memuat kecamatan</option>';
        }
    }

    kabupatenSelect.addEventListener('change', () => {
        muatKecamatan(kabupatenSelect.value);
    });

    if (kabupatenSelect.value) {
        muatKecamatan(kabupatenSelect.value, oldKecamatan);
    }
});