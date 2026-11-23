{{--
    Layout halaman pengelola: dashboard, daftar & detail laporan, informasi panduan, profil pengelola.
    Contoh:
      <x-layouts.pengelola title="Dashboard">
          <x-page-header title="Dashboard" description="Ringkasan laporan destinasi Anda." />
          ...
      </x-layouts.pengelola>
--}}
@props([
    'title' => null,
])

<!DOCTYPE html>
<html lang="id">
<head>
    <x-head :title="$title" />
</head>
<body class="bg-page font-sans text-ink antialiased">
    <div class="flex min-h-screen">
        <x-sidebar.pengelola />

        <div class="flex min-w-0 flex-1 flex-col">
            <x-topbar.pengelola />

            <main class="flex-1 px-4 py-8 sm:px-10">
                <x-flash />
                {{ $slot }}
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
