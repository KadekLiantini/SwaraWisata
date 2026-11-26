const MAKS_FOTO = 3;
const FORMAT_FOTO = ["image/jpeg", "image/png", "image/webp"];

function pasangModalEdit(modal) {
    if (!modal || modal.dataset.terpasang) return;
    modal.dataset.terpasang = "ya";

    const buka = () => {
        modal.classList.remove("hidden");
        modal.classList.add("flex");
        document.body.classList.add("overflow-hidden");
    };

    const tutup = () => {
        modal.classList.add("hidden");
        modal.classList.remove("flex");
        document.body.classList.remove("overflow-hidden");
    };

    modal.buka = buka;
    modal.tutup = tutup;

    modal
        .querySelector("#btn-tutup-modal-edit")
        ?.addEventListener("click", tutup);
    modal.querySelector("#btn-batal-edit")?.addEventListener("click", tutup);
    modal.addEventListener("click", (event) => {
        if (event.target === modal) tutup();
    });

    // ---------- Foto lama & foto baru ----------
    const inputFotoEdit = modal.querySelector("#foto-edit");
    const daftarFotoEdit = modal.querySelector("#daftar-foto-edit");
    const containerFotoHapus = modal.querySelector("#foto-hapus-container");
    let fotoBaruEdit = [];

    const jumlahFotoLamaTersisa = () =>
        daftarFotoEdit.querySelectorAll("[data-foto-lama]").length;

    const perbaruiFileInputEdit = () => {
        const transfer = new DataTransfer();
        fotoBaruEdit.forEach((item) => transfer.items.add(item.file));
        inputFotoEdit.files = transfer.files;
    };

    const tampilkanPreviewFotoEdit = () => {
        daftarFotoEdit
            .querySelectorAll("[data-foto-baru]")
            .forEach((item) => item.remove());

        fotoBaruEdit.forEach((item, index) => {
            const wadah = document.createElement("div");
            wadah.className =
                "relative overflow-hidden rounded-xl border border-black/5 bg-white p-2";
            wadah.dataset.fotoBaru = "";

            const gambar = document.createElement("img");
            gambar.src = item.url;
            gambar.alt = "Preview foto baru";
            gambar.className = "h-28 w-full rounded-lg object-cover";

            const nama = document.createElement("p");
            nama.className = "mt-2 truncate text-xs font-medium text-ink";
            nama.textContent = item.file.name;

            const tombolHapus = document.createElement("button");
            tombolHapus.type = "button";
            tombolHapus.textContent = "×";
            tombolHapus.title = "Hapus foto";
            tombolHapus.className =
                "absolute right-3 top-3 flex size-6 items-center justify-center rounded-full bg-red-500 text-sm font-bold text-white shadow-md transition hover:bg-red-600";
            tombolHapus.addEventListener("click", () => {
                URL.revokeObjectURL(item.url);
                fotoBaruEdit.splice(index, 1);
                perbaruiFileInputEdit();
                tampilkanPreviewFotoEdit();
            });

            wadah.append(gambar, nama, tombolHapus);
            daftarFotoEdit.appendChild(wadah);
        });
    };

    // Hapus foto lama: kartunya hilang, ID-nya dikirim lewat input foto_dihapus[]
    daftarFotoEdit
        ?.querySelectorAll(".btn-hapus-foto-lama")
        .forEach((tombol) => {
            tombol.addEventListener("click", () => {
                const kartuFoto = tombol.closest("[data-foto-lama]");
                if (!kartuFoto || !tombol.dataset.id) return;

                const inputHapus = document.createElement("input");
                inputHapus.type = "hidden";
                inputHapus.name = "foto_dihapus[]";
                inputHapus.value = tombol.dataset.id;

                containerFotoHapus.appendChild(inputHapus);
                kartuFoto.remove();
                perbaruiFileInputEdit();
            });
        });

    // Pilih foto baru
    inputFotoEdit?.addEventListener("change", function () {
        const maksimalFotoBaru = MAKS_FOTO - jumlahFotoLamaTersisa();
        const files = Array.from(this.files);

        if (fotoBaruEdit.length + files.length > maksimalFotoBaru) {
            alert(
                `Kamu hanya dapat menambahkan maksimal ${maksimalFotoBaru - fotoBaruEdit.length} foto lagi.`,
            );
            perbaruiFileInputEdit();
            return;
        }

        if (files.some((file) => !FORMAT_FOTO.includes(file.type))) {
            alert("Format foto harus JPG, PNG, atau WEBP.");
            perbaruiFileInputEdit();
            return;
        }

        files.forEach((file) =>
            fotoBaruEdit.push({ file, url: URL.createObjectURL(file) }),
        );
        perbaruiFileInputEdit();
        tampilkanPreviewFotoEdit();
    });

    // Buka otomatis bila sebelumnya gagal validasi
    if (modal.hasAttribute("data-buka-otomatis")) buka();
}

// Halaman detail laporan & profil (saat gagal validasi): modal sudah ada di halaman
document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("modal-edit-laporan");
    pasangModalEdit(modal);
    document
        .getElementById("btn-edit-laporan")
        ?.addEventListener("click", () => modal?.buka?.());
});

// Halaman profil: ambil modal dari server saat ikon edit diklik
document.addEventListener("click", async (event) => {
    const link = event.target.closest("a[data-buka-modal-edit]");
    if (!link) return;
    event.preventDefault();

    try {
        const respons = await fetch(link.href, {
            headers: { "X-Requested-With": "XMLHttpRequest" },
        });
        if (!respons.ok)
            throw new Error(
                `Gagal memuat form edit (status ${respons.status})`,
            ); // DIUBAH

        const halaman = new DOMParser().parseFromString(
            await respons.text(),
            "text/html",
        );
        const modalBaru = halaman.getElementById("modal-edit-laporan");
        if (!modalBaru) throw new Error("Modal tidak ditemukan");

        document.getElementById("modal-edit-laporan")?.remove();
        document.body.appendChild(modalBaru);

        const inputKembali = modalBaru.querySelector('input[name="kembali"]');
        if (inputKembali) inputKembali.value = window.location.href;

        pasangModalEdit(modalBaru);
        modalBaru.buka();
    } catch (error) {
        console.error("[modal-edit]", error); // BARU: tampilkan alasan gagal di Console
        window.location.href = link.href; // cadangan: buka halaman detail laporan
    }
});

// Tombol Escape menutup modal edit
document.addEventListener("keydown", (event) => {
    if (event.key === "Escape")
        document.getElementById("modal-edit-laporan")?.tutup?.();
});
