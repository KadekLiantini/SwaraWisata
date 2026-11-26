document.addEventListener('DOMContentLoaded', () => {
    const inputFoto = document.getElementById('foto');
    const previewFoto = document.getElementById('preview-foto');
    const areaUpload = document.getElementById('area-upload-foto');

    // Modal Edit Laporan
    const modalEdit = document.getElementById('modal-edit-laporan');
    const tombolEdit = document.getElementById('btn-edit-laporan');
    const tombolTutup = document.getElementById('btn-tutup-modal-edit');
    const tombolBatal = document.getElementById('btn-batal-edit');

    function bukaModalEdit() {
        if (!modalEdit) return;

        modalEdit.classList.remove('hidden');
        modalEdit.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }

    function tutupModalEdit() {
        if (!modalEdit) return;

        modalEdit.classList.add('hidden');
        modalEdit.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    }

    tombolEdit?.addEventListener('click', bukaModalEdit);
    tombolTutup?.addEventListener('click', tutupModalEdit);
    tombolBatal?.addEventListener('click', tutupModalEdit);

    modalEdit?.addEventListener('click', event => {
        if (event.target === modalEdit) {
            tutupModalEdit();
        }
    });

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') {
            tutupModalEdit();
        }
    });

    // Validasi jumlah foto baru pada modal edit
    const inputFotoEdit = document.getElementById('foto-edit');

    inputFotoEdit?.addEventListener('change', function () {
        const maksimalFotoBaru = Number(this.dataset.maxFotoBaru);

        if (this.files.length > maksimalFotoBaru) {
            alert(`Kamu hanya dapat menambahkan maksimal ${maksimalFotoBaru} foto lagi.`);
            this.value = '';
        }
    });

    if (!inputFoto || !previewFoto || !areaUpload) return;

    let daftarFoto = [];

    function perbaruiInput() {
        const transfer = new DataTransfer();

        daftarFoto.forEach(({ file }) => {
            transfer.items.add(file);
        });

        inputFoto.files = transfer.files;
    }

    function tampilkanPreview() {
        previewFoto.innerHTML = '';

        daftarFoto.forEach((item, index) => {
            const elemen = document.createElement('div');
            elemen.className = 'flex items-center gap-3 rounded-xl border border-black/5 bg-white p-3';

            const gambar = document.createElement('img');
            gambar.src = item.url;
            gambar.alt = 'Preview foto';
            gambar.className = 'size-[72px] rounded-lg object-cover';

            const informasi = document.createElement('div');
            informasi.className = 'min-w-0 flex-1';

            const nama = document.createElement('p');
            nama.className = 'break-all text-sm font-semibold text-ink';
            nama.textContent = item.file.name;

            const ukuran = document.createElement('p');
            ukuran.className = 'text-xs text-muted';
            ukuran.textContent = `${(item.file.size / 1024).toFixed(0)} KB`;

            informasi.append(nama, ukuran);

            const tombolHapus = document.createElement('button');
            tombolHapus.type = 'button';
            tombolHapus.className = 'flex size-8 shrink-0 items-center justify-center rounded-full bg-red-50 text-red-600';
            tombolHapus.textContent = '×';
            tombolHapus.addEventListener('click', () => {
                URL.revokeObjectURL(item.url);
                daftarFoto.splice(index, 1);
                perbaruiInput();
                tampilkanPreview();
            });

            const wadahGambar = document.createElement('div');
            wadahGambar.className = 'relative size-[72px] shrink-0';

            gambar.className = 'size-full rounded-lg object-cover';

            tombolHapus.className =
                'absolute -right-2 -top-2 flex size-6 items-center justify-center rounded-full bg-red-500 text-sm font-bold text-white shadow-md transition hover:bg-red-600';

            wadahGambar.append(gambar, tombolHapus);
            elemen.append(wadahGambar, informasi);
            previewFoto.appendChild(elemen);
        });
    }

    function tambahkanFoto(files) {
        Array.from(files).forEach(file => {
            if (daftarFoto.length >= 3) return;
            if (!['image/jpeg', 'image/png'].includes(file.type)) return;

            daftarFoto.push({
                file: file,
                url: URL.createObjectURL(file)
            });
        });

        perbaruiInput();
        tampilkanPreview();
    }

    inputFoto.addEventListener('change', function () {
        tambahkanFoto(this.files);
    });

    areaUpload.addEventListener('dragenter', event => {
        event.preventDefault();
        areaUpload.classList.add(
            'border-primary',
            'bg-blue-50',
            'ring-2',
            'ring-primary/20'
        );
    });

    areaUpload.addEventListener('dragover', event => {
        event.preventDefault();
    });

    areaUpload.addEventListener('dragleave', event => {
        if (!areaUpload.contains(event.relatedTarget)) {
            areaUpload.classList.remove(
                'border-primary',
                'bg-blue-50',
                'ring-2',
                'ring-primary/20'
            );
        }
    });

    areaUpload.addEventListener('drop', event => {
        event.preventDefault();

        areaUpload.classList.remove(
            'border-primary',
            'bg-blue-50',
            'ring-2',
            'ring-primary/20'
        );

        tambahkanFoto(event.dataTransfer.files);
    });
});

async function muatDaftarLaporan(url, simpanRiwayat = true) {
    const container = document.getElementById('daftar-laporan-container');

    if (!container) return;

    try {
        const response = await fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) throw new Error('Gagal memuat laporan');

        const html = await response.text();
        const dokumen = new DOMParser().parseFromString(html, 'text/html');
        const containerBaru = dokumen.getElementById('daftar-laporan-container');

        if (!containerBaru) throw new Error('Daftar laporan tidak ditemukan');

        container.innerHTML = containerBaru.innerHTML;

        if (simpanRiwayat) {
            history.pushState({}, '', url);
        }

        const posisi = container.getBoundingClientRect().top + window.scrollY;

        window.scrollTo({
            top: posisi - 120,
            behavior: 'smooth'
        });
    } catch (error) {
        window.location.href = url;
    }
}

// Sorting
document.addEventListener('change', event => {
    const select = event.target.closest('#daftar-laporan-container #urutan');

    if (!select) return;

    const form = select.closest('form');
    const url = new URL(form.action, window.location.origin);

    new FormData(form).forEach((value, key) => {
        url.searchParams.set(key, value);
    });

    muatDaftarLaporan(url);
});

// Filter dan pagination
document.addEventListener('click', event => {
    const link = event.target.closest('#daftar-laporan-container a[href]');

    if (!link || link.target || link.hasAttribute('download')) return;

    event.preventDefault();
    muatDaftarLaporan(link.href);
});

// Tombol Back dan Forward browser
window.addEventListener('popstate', () => {
    muatDaftarLaporan(window.location.href, false);
});

