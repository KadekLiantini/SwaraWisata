{{-- Isi <head> bersama untuk semua layout. Halaman bisa menambah lewat @push('head'). --}}
@props([
    'title' => null,
])

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $title ? $title.' · SwaraWisata' : 'SwaraWisata · Pelaporan Kerusakan Infrastruktur Wisata Bali' }}</title>
<link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}">
@vite(['resources/css/app.css', 'resources/js/app.js'])
@stack('head')
