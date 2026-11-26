/*
 | Interaksi halaman Admin > Kelola Destinasi.
 | Mengikuti pola laporan.js: fetch halaman, ambil container daftar,
 | perbarui history, lalu tangani popstate. Tidak menambah dependency.
 */
document.addEventListener('DOMContentLoaded', () => {
    const formFilter = document.getElementById('form-filter-destinasi');
    const daftar = document.getElementById('daftar-destinasi-container');

    if (formFilter && daftar) {
        let timerCari = null;
        let pengendali = null;

        const bangunUrlFilter = () => {
            const url = new URL(formFilter.action, window.location.origin);

            new FormData(formFilter).forEach((value, key) => {
                const teks = String(value).trim();

                if (teks !== '') {
                    url.searchParams.set(key, teks);
                }
            });

            // Filter baru selalu mulai dari halaman pertama.
            url.searchParams.delete('page');

            return url;
        };

        const sinkronkanFormDariUrl = (url) => {
            ['search', 'id_kabupaten', 'status', 'sort'].forEach((nama) => {
                const field = formFilter.elements.namedItem(nama);

                if (field) {
                    field.value = url.searchParams.get(nama) ?? '';
                }
            });
        };

        const muatDaftar = async (url, { riwayat = 'push' } = {}) => {
            if (pengendali) pengendali.abort();

            pengendali = new AbortController();

            try {
                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    signal: pengendali.signal,
                });

                if (!response.ok) {
                    throw new Error('Gagal memuat daftar destinasi');
                }

                const html = await response.text();
                const dokumen = new DOMParser().parseFromString(html, 'text/html');
                const daftarBaru = dokumen.getElementById('daftar-destinasi-container');

                if (!daftarBaru) {
                    throw new Error('Container daftar destinasi tidak ditemukan');
                }

                daftar.innerHTML = daftarBaru.innerHTML;

                if (window.location.href !== url.href) {
                    if (riwayat === 'push') {
                        history.pushState({}, '', url.href);
                    } else if (riwayat === 'replace') {
                        history.replaceState({}, '', url.href);
                    }
                }
            } catch (error) {
                if (error.name === 'AbortError') return;

                // Fallback aman bila AJAX gagal: tetap buka halaman yang diminta.
                window.location.href = url.href;
            }
        };

        const terapkanFilter = (riwayat = 'push') => {
            muatDaftar(bangunUrlFilter(), { riwayat });
        };

        formFilter.querySelectorAll('select').forEach((select) => {
            select.addEventListener('change', () => {
                if (timerCari) {
                    clearTimeout(timerCari);
                    timerCari = null;
                }

                terapkanFilter('push');
            });
        });

        const kolomCari = formFilter.elements.namedItem('search');

        kolomCari?.addEventListener('input', () => {
            if (timerCari) clearTimeout(timerCari);

            timerCari = setTimeout(() => {
                timerCari = null;
                terapkanFilter('replace');
            }, 450);
        });

        // Enter tetap bekerja, tetapi tidak diperlukan.
        formFilter.addEventListener('submit', (event) => {
            event.preventDefault();

            if (timerCari) {
                clearTimeout(timerCari);
                timerCari = null;
            }

            terapkanFilter('push');
        });

        // Hanya tautan pagination yang dimuat tanpa reload penuh;
        // tautan Ubah/Lihat tetap navigasi normal.
        daftar.addEventListener('click', (event) => {
            const link = event.target.closest('nav[aria-label="Navigasi halaman"] a[href]');

            if (!link || link.target || link.hasAttribute('download')) return;

            event.preventDefault();

            const url = new URL(link.href, window.location.origin);

            sinkronkanFormDariUrl(url);
            muatDaftar(url);
        });

        window.addEventListener('popstate', () => {
            const url = new URL(window.location.href);

            sinkronkanFormDariUrl(url);
            muatDaftar(url, { riwayat: 'none' });
        });
    }

    // Modal konfirmasi hapus destinasi.
    const modalHapus = document.getElementById('modal-hapus-destinasi');
    const formHapus = document.getElementById('form-hapus-destinasi');
    const namaHapus = document.getElementById('nama-destinasi-hapus');
    const tombolKonfirmasi = document.getElementById('konfirmasi-hapus');

    const kembalikanTombolKonfirmasi = () => {
        if (!tombolKonfirmasi) return;

        tombolKonfirmasi.disabled = false;
        tombolKonfirmasi.textContent = 'Ya, Hapus';
    };

    const bukaModalHapus = (tombol) => {
        if (!modalHapus || !formHapus) return;

        if (formHapus.action && tombol.dataset.url) {
            formHapus.action = tombol.dataset.url;
        }

        if (namaHapus) {
            const nama = tombol.dataset.nama || `#${tombol.dataset.id || ''}`;
            namaHapus.textContent = nama;
        }

        kembalikanTombolKonfirmasi();
        modalHapus.classList.remove('hidden');
        modalHapus.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    };

    const tutupModalHapus = () => {
        if (!modalHapus) return;

        modalHapus.classList.add('hidden');
        modalHapus.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
        kembalikanTombolKonfirmasi();
    };

    document.addEventListener('click', (event) => {
        const tombolHapus = event.target.closest('[data-hapus-destinasi]');

        if (tombolHapus) {
            bukaModalHapus(tombolHapus);
            return;
        }

        if (event.target.closest('[data-tutup-hapus]')) {
            tutupModalHapus();
        }
    });

    modalHapus?.addEventListener('click', (event) => {
        if (event.target === modalHapus) {
            tutupModalHapus();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            tutupModalHapus();
        }
    });

    formHapus?.addEventListener('submit', () => {
        if (!tombolKonfirmasi) return;

        tombolKonfirmasi.disabled = true;
        tombolKonfirmasi.textContent = 'Menghapus...';
    });
});
