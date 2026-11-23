{{--
    Layout halaman publik dan wisatawan: landing page, login, registrasi, detail laporan, profil wisatawan.

    Props:
      title   judul tab browser
      cta     tombol header untuk tamu: login | register | none
      glow    tampilkan cahaya hijau-biru di latar (seperti halaman Registrasi)
      footer  tampilkan footer (default true)

    Contoh:
      <x-layouts.public title="Daftar" cta="login" glow :footer="false">
          ...isi halaman...
      </x-layouts.public>

    Slot "nav" opsional untuk tautan menu di header (landing page):
      <x-slot:nav><a href="#cara-kerja">Cara Kerja</a></x-slot:nav>
--}}
@props([
    'title' => null,
    'cta' => 'login',
    'glow' => false,
    'footer' => true,
])

<!DOCTYPE html>
<html lang="id">
<head>
    <x-head :title="$title" />
</head>
<body class="flex min-h-screen flex-col bg-page font-sans text-ink antialiased">
    <x-navbar.public :cta="$cta">{{ $nav ?? '' }}</x-navbar.public>

    <main class="relative flex-1 overflow-x-clip pt-20">
        @if ($glow)
            <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-0 overflow-hidden">
                <div class="absolute -top-32 left-1/2 h-[340px] w-[900px] max-w-full -translate-x-1/2 rounded-full bg-[rgba(152,244,211,0.3)] blur-[64px]"></div>
                <div class="absolute top-[384px] -right-[100px] size-[450px] rounded-full bg-[rgba(222,232,255,0.6)] blur-[64px]"></div>
            </div>
        @endif

        <div class="relative">
            @if (session()->hasAny(['success', 'error', 'info']))
                <div class="mx-auto w-full max-w-[1280px] px-4 pt-6 sm:px-8">
                    <x-flash />
                </div>
            @endif
            {{ $slot }}
        </div>
    </main>

    @if ($footer)
        <x-footer />
    @endif

    @stack('scripts')
</body>
</html>
