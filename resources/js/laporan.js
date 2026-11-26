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

    
    // Carousel dan Modal Perbesar Foto
    const carouselFoto = document.getElementById('carousel-foto-laporan');
    const tombolGeserKiri = document.getElementById('geser-foto-kiri');
    const tombolGeserKanan = document.getElementById('geser-foto-kanan');

    const modalFoto = document.getElementById('modal-foto');
    const gambarModal = document.getElementById('gambar-modal');
    const keteranganFotoModal = document.getElementById('keterangan-foto-modal');
    const tombolTutupFoto = document.getElementById('btn-tutup-foto');

    function bukaFoto(url, keterangan) {
        if (!modalFoto || !gambarModal) return;

        gambarModal.src = url;

        if (keteranganFotoModal) {
            keteranganFotoModal.textContent = keterangan;
        }

        modalFoto.classList.remove('hidden');
        modalFoto.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }

    function tutupFoto() {
        if (!modalFoto || !gambarModal) return;

        modalFoto.classList.add('hidden');
        modalFoto.classList.remove('flex');
        gambarModal.src = '';

        if (keteranganFotoModal) {
            keteranganFotoModal.textContent = '';
        }

        document.body.classList.remove('overflow-hidden');
    }

    // Klik foto mana pun untuk memperbesar
    carouselFoto?.addEventListener('click', event => {
        const tombolFoto = event.target.closest('[data-perbesar-foto]');

        if (!tombolFoto) return;

        bukaFoto(
            tombolFoto.dataset.fotoUrl,
            tombolFoto.dataset.keteranganFoto
        );
    });

    tombolTutupFoto?.addEventListener('click', tutupFoto);

    modalFoto?.addEventListener('click', event => {
        if (event.target === modalFoto) {
            tutupFoto();
        }
    });

    // Mengatur kondisi tombol panah
    function perbaruiTombolCarousel() {
        if (!carouselFoto) return;

        const maksimalScroll =
            carouselFoto.scrollWidth - carouselFoto.clientWidth;

        tombolGeserKiri.disabled = carouselFoto.scrollLeft <= 2;
        tombolGeserKanan.disabled =
            carouselFoto.scrollLeft >= maksimalScroll - 2;
    }

    // Geser satu foto ke kiri
    tombolGeserKiri?.addEventListener('click', () => {
        if (!carouselFoto) return;

        const lebarFoto =
            carouselFoto.querySelector('[data-perbesar-foto]')?.offsetWidth || 0;

        carouselFoto.scrollBy({
            left: -(lebarFoto + 12),
            behavior: 'smooth'
        });
    });

    // Geser satu foto ke kanan
    tombolGeserKanan?.addEventListener('click', () => {
        if (!carouselFoto) return;

        const lebarFoto =
            carouselFoto.querySelector('[data-perbesar-foto]')?.offsetWidth || 0;

        carouselFoto.scrollBy({
            left: lebarFoto + 12,
            behavior: 'smooth'
        });
    });

    carouselFoto?.addEventListener('scroll', perbaruiTombolCarousel);

    window.addEventListener('resize', perbaruiTombolCarousel);

    perbaruiTombolCarousel();


    modalEdit?.addEventListener('click', event => {
        if (event.target === modalEdit) {
            tutupModalEdit();
        }
    });

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') {
            tutupModalEdit();
            tutupFoto();
        }
    });

    // Preview dan Hapus Foto pada Modal Edit
    const inputFotoEdit = document.getElementById('foto-edit');
    const daftarFotoEdit = document.getElementById('daftar-foto-edit');
    const containerFotoHapus = document.getElementById('foto-hapus-container');

    let fotoBaruEdit = [];

    function jumlahFotoLamaTersisa() {
        return daftarFotoEdit.querySelectorAll('[data-foto-lama]').length;
    }

    function perbaruiFileInputEdit() {
        const transfer = new DataTransfer();

        fotoBaruEdit.forEach(item => {
            transfer.items.add(item.file);
        });

        inputFotoEdit.files = transfer.files;
    }

    function tampilkanPreviewFotoEdit() {
        // Hapus preview foto baru sebelumnya, tetapi pertahankan foto lama.
        daftarFotoEdit.querySelectorAll('[data-foto-baru]').forEach(item => {
            item.remove();
        });

        fotoBaruEdit.forEach((item, index) => {
            const wadah = document.createElement('div');
            wadah.className = 'relative overflow-hidden rounded-xl border border-black/5 bg-white p-2';
            wadah.dataset.fotoBaru = '';

            const gambar = document.createElement('img');
            gambar.src = item.url;
            gambar.alt = 'Preview foto baru';
            gambar.className = 'h-28 w-full rounded-lg object-cover';

            const nama = document.createElement('p');
            nama.className = 'mt-2 truncate text-xs font-medium text-ink';
            nama.textContent = item.file.name;

            const tombolHapus = document.createElement('button');
            tombolHapus.type = 'button';
            tombolHapus.textContent = '×';
            tombolHapus.title = 'Hapus foto';
            tombolHapus.className = 'absolute right-3 top-3 flex size-6 items-center justify-center rounded-full bg-red-500 text-sm font-bold text-white shadow-md transition hover:bg-red-600';

            tombolHapus.addEventListener('click', () => {
                URL.revokeObjectURL(item.url);
                fotoBaruEdit.splice(index, 1);

                perbaruiFileInputEdit();
                tampilkanPreviewFotoEdit();
            });

            wadah.append(gambar, nama, tombolHapus);
            daftarFotoEdit.appendChild(wadah);
        });
    }

    // Hapus foto lama
    daftarFotoEdit?.querySelectorAll('.btn-hapus-foto-lama').forEach(tombol => {
        tombol.addEventListener('click', function () {
            const kartuFoto = this.closest('[data-foto-lama]');
            const idFoto = this.dataset.id;

            if (!kartuFoto || !idFoto) return;

            const inputHapus = document.createElement('input');
            inputHapus.type = 'hidden';
            inputHapus.name = 'foto_dihapus[]';
            inputHapus.value = idFoto;

            containerFotoHapus.appendChild(inputHapus);
            kartuFoto.remove();

            // Perbarui jumlah foto baru yang boleh ditambahkan.
            inputFotoEdit.dataset.maxFotoBaru =
                3 - jumlahFotoLamaTersisa();

            perbaruiFileInputEdit();
        });
    });

    // Pilih foto baru
    inputFotoEdit?.addEventListener('change', function () {
        const maksimalFotoBaru = 3 - jumlahFotoLamaTersisa();
        const files = Array.from(this.files);

        const formatDiizinkan = [
            'image/jpeg',
            'image/png',
            'image/webp'
        ];

        if (fotoBaruEdit.length + files.length > maksimalFotoBaru) {
            alert(`Kamu hanya dapat menambahkan maksimal ${maksimalFotoBaru - fotoBaruEdit.length} foto lagi.`);
            this.value = '';
            return;
        }

        if (files.some(file => !formatDiizinkan.includes(file.type))) {
            alert('Format foto harus JPG, PNG, atau WEBP.');
            this.value = '';
            return;
        }

        files.forEach(file => {
            fotoBaruEdit.push({
                file: file,
                url: URL.createObjectURL(file)
            });
        });

        perbaruiFileInputEdit();
        tampilkanPreviewFotoEdit();
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

// Modal Perbesar Foto
function bukaFoto(url) {
    const modal = document.getElementById('modal-foto');
    const gambar = document.getElementById('gambar-modal');

    if (!modal || !gambar) return;

    gambar.src = url;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.classList.add('overflow-hidden');
}

function tutupFoto() {
    const modal = document.getElementById('modal-foto');
    const gambar = document.getElementById('gambar-modal');

    if (!modal || !gambar) return;

    modal.classList.add('hidden');
    modal.classList.remove('flex');
    gambar.src = '';
    document.body.classList.remove('overflow-hidden');
}

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

