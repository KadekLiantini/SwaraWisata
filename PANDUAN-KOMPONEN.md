# Panduan Komponen UI SwaraWisata

Semua komponen ada di `resources/views/components/` dan dibuat dari Figma (halaman **DESIGN SYSTEM** dan **Registrasi**).
Pakai komponen ini, jangan menyalin kelas Tailwind panjang di setiap halaman.

## 1. Layout (pilih satu per halaman)

| Halaman | Layout |
|---|---|
| Landing, login, registrasi, detail laporan, profil wisatawan, form laporan | `<x-layouts.public>` |
| Semua halaman pengelola | `<x-layouts.pengelola>` |
| Semua halaman admin | `<x-layouts.admin>` |

```blade
<x-layouts.public title="Masuk" cta="register" glow :footer="false">
    ...isi halaman...
</x-layouts.public>

<x-layouts.pengelola title="Daftar Laporan">
    ...
</x-layouts.pengelola>

<x-layouts.admin title="Kelola Destinasi" :breadcrumbs="['Tata Kelola', 'Kelola Destinasi']">
    ...
</x-layouts.admin>
```

Props `x-layouts.public`: `title`, `cta` (`login` | `register` | `none`), `glow` (latar bercahaya), `footer` (default `true`).
Menu tengah di header (landing page):

```blade
<x-layouts.public>
    <x-slot:nav>
        <a href="#cara-kerja">Cara Kerja</a>
        <a href="#top3">Top 3 Destinasi</a>
    </x-slot:nav>
    ...
</x-layouts.public>
```

Pesan dari controller (`->with('success', '...')`, `'error'`, `'info'`) tampil otomatis di semua layout.

## 2. Komponen

| Komponen | Contoh |
|---|---|
| Tombol | `<x-button type="submit" size="lg" icon="send" block>Kirim Laporan</x-button>` |
| Tombol sebagai link | `<x-button :href="route('laporan.create')" icon-left="add">Laporkan</x-button>` |
| Ikon | `<x-icon name="flag" size="18" class="text-primary" />` |
| Kartu | `<x-card>...</x-card>` / `<x-card padding="md">` |
| Judul halaman | `<x-page-header title="Profil Saya" description="...">(tombol opsional)</x-page-header>` |
| Pemberitahuan | `<x-alert variant="notice" title="Perhatian">...</x-alert>` |
| Link kembali | `<x-back-link :href="route('home')" />` |
| Logo | `<x-logo />` / `<x-logo variant="white" />` |

Variant tombol: `primary` (default), `secondary` (kuning), `outline`, `ghost`, `danger`. Size: `sm`, `md`, `lg`.
Variant alert: `notice` (kuning, seperti halaman Registrasi), `info`, `success`, `error`.
Nama ikon: lihat https://fonts.google.com/icons (gaya **Outlined**). Tambah `filled` untuk ikon terisi.

## 3. Form

Label, tanda wajib (*), nilai lama `old()`, dan pesan error validasi muncul otomatis berdasarkan `name`.

```blade
<x-form.input name="judul" label="Judul Laporan" icon="title" required counter maxlength="100"
              placeholder="Contoh: Toilet rusak di area parkir" />

<x-form.input name="password" type="password" label="Password" icon="lock" required />

<x-form.input name="foto[]" type="file" label="Foto Pendukung" required multiple accept="image/*"
              hint="JPG/PNG, maksimal 2 MB per foto." hint-icon="image" />

<x-form.select name="id_destinasi" label="Destinasi" icon="location_on" required
               :options="$destinasi->pluck('nama', 'id_destinasi')" placeholder="Pilih destinasi" />

<x-form.textarea name="deskripsi" label="Deskripsi" rows="5" required counter maxlength="1000" />
```

Fitur tambahan:
- `counter` + `maxlength` → penghitung `0/20` di kanan label.
- `type="password"` → tombol mata untuk menampilkan password.
- `data-match="password"` pada input konfirmasi → pesan "Password cocok / belum sama" langsung saat mengetik.

## 4. Nama route yang dipakai menu

Menu di sidebar/header membaca nama route berikut. Selama route belum dibuat, menu mengarah ke `#`.

- Publik/wisatawan: `home`, `login`, `register`, `logout`, `wisatawan.profil`, `laporan.create`
- Pengelola: `pengelola.dashboard`, `pengelola.info`, `pengelola.laporan.index`, `pengelola.profil`
- Admin: `admin.dashboard`, `admin.pengajuan.index`, `admin.destinasi.index`, `admin.pengelola.index`, `admin.kategori.index`

## 5. Aturan tim

- Jangan mengubah file di `components/` tanpa memberi tahu pemegang tugas Fondasi; semua halaman ikut berubah.
- Butuh komponen baru? Buat di `components/` dengan komentar contoh pemakaian di baris paling atas.
- Warna dan font memakai token di `resources/css/app.css` (`bg-primary`, `text-ink`, `text-muted`, `font-heading`, dst.), bukan kode hex langsung.
