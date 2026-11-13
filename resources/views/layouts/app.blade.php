<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SalonSync') }}</title>
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-[#faf3f8]">

        {{-- Background: malambot na gradient at mga malabong hugis --}}
        <div class="fixed inset-0 -z-10 pointer-events-none" aria-hidden="true">
            <div class="absolute inset-0 bg-gradient-to-b from-[#fdf6fa] via-[#f8e9f2] to-[#f1d9e8]"></div>
            <div class="absolute top-24 -left-32 w-[26rem] h-[26rem] rounded-full bg-[#e9b8d4]/40 blur-3xl"></div>
            <div class="absolute top-1/2 -right-32 w-[30rem] h-[30rem] rounded-full bg-[#c9a7e0]/35 blur-3xl"></div>
            <div class="absolute -bottom-24 left-1/4 w-[24rem] h-[24rem] rounded-full bg-[#f4c2a8]/30 blur-3xl"></div>
        </div>

        <div class="min-h-screen">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-gradient-to-r from-[#5f2249] via-[#7b2d5f] to-[#c45a9a] shadow [&_h1]:!text-white [&_h2]:!text-white">
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
    </body>
</html>
