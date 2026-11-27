document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('modal-konfirmasi-kategori');
    const namaKategori = document.getElementById('nama-kategori-status');
    const aksiStatus = document.getElementById('aksi-status-kategori');
    const tombolKonfirmasi = document.getElementById('konfirmasi-status-kategori');

    if (!modal || !tombolKonfirmasi) return;

    let formTertunda = null;

    const tutupModal = () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
        formTertunda = null;
    };

    // Tampilkan modal sebelum form dikirim.
    document.addEventListener('submit', (event) => {
        const form = event.target.closest('form[data-form-status-kategori]');

        if (!form) return;

        event.preventDefault();
        formTertunda = form;

        if (namaKategori) {
            namaKategori.textContent = form.dataset.nama || '';
        }

        if (aksiStatus) {
            aksiStatus.textContent = form.dataset.aksi || 'mengubah status';
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    });

    // Tombol batal.
    modal.querySelectorAll('[data-tutup-status-kategori]').forEach((tombol) => {
        tombol.addEventListener('click', tutupModal);
    });

    // Klik area luar modal.
    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            tutupModal();
        }
    });

    // Tombol Escape.
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
            tutupModal();
        }
    });

    // Konfirmasi perubahan status.
    tombolKonfirmasi.addEventListener('click', () => {
        if (!formTertunda) return;

        const form = formTertunda;
        formTertunda = null;

        tutupModal();
        form.submit();
    });
});
