const JEDA_KETIK = 400; // milidetik

function pasangLiveSearch(form) {
    const selector = form.dataset.liveTarget;
    const target = document.querySelector(selector);
    if (!target) return;

    let timer;
    let controller;

    const muat = async (alamat) => {
        controller?.abort();
        controller = new AbortController();
        target.setAttribute("aria-busy", "true");
        target.classList.add("opacity-50", "transition-opacity");

        try {
            const respons = await fetch(alamat, {
                headers: { "X-Requested-With": "XMLHttpRequest" },
                signal: controller.signal,
            });
            const halaman = new DOMParser().parseFromString(
                await respons.text(),
                "text/html",
            );
            const baru = halaman.querySelector(selector);
            if (baru) target.innerHTML = baru.innerHTML;
            history.replaceState(null, "", alamat);
        } catch (error) {
            if (error.name !== "AbortError") window.location.href = alamat; // cadangan: muat ulang biasa
        } finally {
            target.removeAttribute("aria-busy");
            target.classList.remove("opacity-50");
        }
    };

    // Alamat dari isi form (tanpa nomor halaman, jadi kembali ke halaman 1)
    const alamatForm = () => {
        const alamat = new URL(form.action, window.location.href);
        alamat.search = new URLSearchParams(new FormData(form)).toString();
        return alamat;
    };

    // Mengetik: tunggu sampai pengguna berhenti mengetik
    form.addEventListener("input", (event) => {
        if (!event.target.matches('input[type="search"], input[type="text"]'))
            return;
        clearTimeout(timer);
        timer = setTimeout(() => muat(alamatForm()), JEDA_KETIK);
    });

    // Dropdown: langsung muat
    form.addEventListener("change", (event) => {
        if (event.target.matches("select")) muat(alamatForm());
    });

    // Tekan Enter: tetap jalan, tanpa muat ulang halaman
    form.addEventListener("submit", (event) => {
        event.preventDefault();
        clearTimeout(timer);
        muat(alamatForm());
    });

    // Klik nomor halaman di dalam target: muat tanpa reload, lalu geser ke awal tabel
    target.addEventListener("click", async (event) => {
        const link = event.target.closest("a[data-live-link]");
        if (!link) return;
        event.preventDefault();
        await muat(new URL(link.href));
        target.scrollIntoView({ behavior: "smooth", block: "start" });
    });
}

document.addEventListener("DOMContentLoaded", () => {
    document
        .querySelectorAll("form[data-live-search]")
        .forEach(pasangLiveSearch);
});
