{{--
    Layout halaman admin.
    Contoh:
      <x-layouts.admin title="Kelola Destinasi" :breadcrumbs="['Tata Kelola', 'Kelola Destinasi']">
          ...
      </x-layouts.admin>
--}}
@props([
    'title' => null,
    'breadcrumbs' => ['Tata Kelola', 'SwaraWisata Pusat'],
])

<!DOCTYPE html>
<html lang="id">
<head>
    <x-head :title="$title" />
</head>
<body class="bg-page font-sans text-ink antialiased">
    <div class="flex min-h-screen">
        <x-sidebar.admin />

        <div class="flex min-w-0 flex-1 flex-col">
            <x-topbar.admin :breadcrumbs="$breadcrumbs" />

            <main class="flex-1 px-4 py-8 sm:px-10">
                <x-flash />
                {{ $slot }}
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
