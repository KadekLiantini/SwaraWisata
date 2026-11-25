import './laporan.js';

/*
 | Interaksi kecil untuk komponen form SwaraWisata.
 | Tidak perlu diubah oleh anggota tim; cukup pakai komponen <x-form.input>.
 */

// Tombol mata pada input password: <button data-toggle-password="id-input">
document.addEventListener("click", (event) => {
    const button = event.target.closest("[data-toggle-password]");
    if (!button) return;

    const input = document.getElementById(button.dataset.togglePassword);
    if (!input) return;

    const show = input.type === "password";
    input.type = show ? "text" : "password";
    button.setAttribute("aria-pressed", String(show));
    button.setAttribute(
        "aria-label",
        show ? "Sembunyikan password" : "Tampilkan password",
    );

    const icon = button.querySelector(".material-symbols-outlined");
    if (icon) icon.textContent = show ? "visibility_off" : "visibility";
});

document.addEventListener("DOMContentLoaded", () => {
    // Penghitung karakter: <span data-counter-for="id-input">0/20</span>
    document.querySelectorAll("[data-counter-for]").forEach((counter) => {
        const input = document.getElementById(counter.dataset.counterFor);
        if (!input) return;

        const update = () => {
            counter.textContent = `${input.value.length}/${input.maxLength}`;
        };
        input.addEventListener("input", update);
        update();
    });

    // Cek kecocokan: <input data-match="password"> (dipakai konfirmasi password)
    document.querySelectorAll("[data-match]").forEach((input) => {
        const target = document.getElementById(input.dataset.match);
        const hint = document.getElementById(`${input.id}-hint`);
        if (!target || !hint) return;

        const text = hint.querySelector("[data-hint-text]");
        const icon = hint.querySelector(".material-symbols-outlined");
        const original = {
            text: text.textContent,
            icon: icon?.textContent,
            className: hint.className,
        };

        const update = () => {
            if (input.value === "") {
                text.textContent = original.text;
                if (icon) icon.textContent = original.icon;
                hint.className = original.className;
                return;
            }

            const same = input.value === target.value;
            text.textContent = same
                ? "Password cocok."
                : "Password belum sama.";
            if (icon) icon.textContent = same ? "check_circle" : "error";
            hint.className = original.className.replace(
                "text-muted",
                same ? "text-primary" : "text-danger",
            );
        };

        input.addEventListener("input", update);
        target.addEventListener("input", update);
    });
});
