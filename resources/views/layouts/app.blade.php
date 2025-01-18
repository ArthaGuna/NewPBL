<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

        <!-- Livewire Styles -->
        @livewireStyles

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen">
            @include('layouts.navigation')

            {{-- Ikon keranjang dengan badge jumlah produk --}}
            <div class="fixed top-5 right-5 z-50">
                <a href="{{ route('cart.index') }}" class="text-white">
                    <i class="fas fa-shopping-cart text-2xl"></i>
                    @if ($totalItems > 0)
                        <span class="absolute top-0 right-0 block text-xs font-semibold text-white bg-red-500 rounded-full w-5 h-5 flex items-center justify-center">
                            {{ $totalItems }}
                        </span>
                    @endif
                </a>
            </div>

            @if(session('success'))
                <div class="fixed top-0 right-0 m-4 p-4 bg-green-500 text-white rounded shadow-lg">
                    {{ session('success') }}
                </div>
            @endif
            
            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <!-- Livewire Scripts -->
        {{-- @livewireScripts --}}
    </body>
</html>
