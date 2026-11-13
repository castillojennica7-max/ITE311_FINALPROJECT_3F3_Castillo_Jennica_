@props(['title' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ? $title . ' - ' : '' }}{{ config('app.name', 'SalonSync') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="font-sans antialiased text-gray-800 bg-[#faf3f8]">

    {{-- Background: malambot na gradient at mga hugis --}}
    <div class="fixed inset-0 -z-10 pointer-events-none" aria-hidden="true">
        <div class="absolute inset-0 bg-gradient-to-b from-[#fdf6fa] via-[#f8e9f2] to-[#f1d9e8]"></div>
        <div class="absolute top-24 -left-32 w-[26rem] h-[26rem] rounded-full bg-[#e9b8d4]/40 blur-3xl"></div>
        <div class="absolute top-1/2 -right-32 w-[30rem] h-[30rem] rounded-full bg-[#c9a7e0]/35 blur-3xl"></div>
        <div class="absolute -bottom-24 left-1/4 w-[24rem] h-[24rem] rounded-full bg-[#f4c2a8]/30 blur-3xl"></div>
    </div>

    {{-- Header --}}
    <header x-data="{ open: false }" class="sticky top-0 z-40 bg-white/90 backdrop-blur border-b border-[#f0dce8]">
        <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <x-application-logo class="h-9 w-9" />
                <span class="text-xl font-extrabold text-[#7b2d5f]">SalonSync</span>
            </a>

            <nav class="hidden md:flex items-center gap-7 text-sm font-medium text-gray-600">
                <a href="{{ route('home') }}" class="hover:text-[#7b2d5f]">Home</a>
                <a href="{{ route('salons.index') }}" class="{{ request()->routeIs('salons.*') ? 'text-[#7b2d5f]' : '' }} hover:text-[#7b2d5f]">Salons</a>
                <a href="{{ route('home') }}#plans" class="hover:text-[#7b2d5f]">Plans</a>
                <a href="{{ route('home') }}#about" class="hover:text-[#7b2d5f]">About</a>
            </nav>

            <div class="hidden md:flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}"
                       class="px-5 py-2 rounded-full bg-[#7b2d5f] text-white text-sm font-semibold hover:bg-[#5f2249] transition">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="px-5 py-2 rounded-full border-2 border-[#7b2d5f] text-[#7b2d5f] text-sm font-semibold hover:bg-[#7b2d5f] hover:text-white transition">
                            Log out
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                       class="px-5 py-2 rounded-full border-2 border-[#7b2d5f] text-[#7b2d5f] text-sm font-semibold hover:bg-[#7b2d5f] hover:text-white transition">Log in</a>
                    <a href="{{ route('register') }}"
                       class="px-5 py-2 rounded-full bg-[#7b2d5f] text-white text-sm font-semibold hover:bg-[#5f2249] transition">Sign up</a>
                @endauth
            </div>

            <button @click="open = ! open" class="md:hidden p-2 rounded-md text-gray-600 hover:bg-gray-100" aria-label="Menu">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="! open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path x-show="open" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div x-show="open" x-cloak class="md:hidden border-t border-[#f0dce8] bg-white px-4 py-4 space-y-3 text-sm font-medium">
            <a href="{{ route('home') }}" class="block text-gray-700">Home</a>
            <a href="{{ route('salons.index') }}" class="block text-gray-700">Salons</a>
            <a href="{{ route('home') }}#plans" class="block text-gray-700">Plans</a>
            <a href="{{ route('home') }}#about" class="block text-gray-700">About</a>
            <div class="flex gap-3 pt-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-5 py-2 rounded-full bg-[#7b2d5f] text-white font-semibold">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="px-5 py-2 rounded-full border-2 border-[#7b2d5f] text-[#7b2d5f] font-semibold">Log in</a>
                    <a href="{{ route('register') }}" class="px-5 py-2 rounded-full bg-[#7b2d5f] text-white font-semibold">Sign up</a>
                @endauth
            </div>
        </div>
    </header>

    <main>
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="mt-20 bg-[#3d1630] text-white/80">
        <div class="max-w-6xl mx-auto px-4 py-12 grid gap-10 md:grid-cols-4">
            <div class="md:col-span-2">
                <div class="flex items-center gap-2">
                    <x-application-logo class="h-9 w-9" />
                    <span class="text-xl font-extrabold text-white">SalonSync</span>
                </div>
                <p class="mt-4 text-sm leading-relaxed max-w-md">
                    A web-based SaaS management platform for salon businesses. Manage customers, services,
                    appointments, staff, and transactions in one system, while customers discover salons and book online.
                </p>
            </div>
            <div>
                <p class="font-semibold text-white mb-3">Explore</p>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('salons.index') }}" class="hover:text-white">Browse salons</a></li>
                    <li><a href="{{ route('home') }}#plans" class="hover:text-white">Subscription plans</a></li>
                    <li><a href="{{ route('home') }}#about" class="hover:text-white">About SalonSync</a></li>
                </ul>
            </div>
            <div>
                <p class="font-semibold text-white mb-3">Get started</p>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('register') }}" class="hover:text-white">Create a customer account</a></li>
                    <li><a href="{{ route('register', ['type' => 'salon_owner']) }}" class="hover:text-white">Register your salon</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-white">Log in</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-white/10 py-4 text-center text-xs text-white/50">
            &copy; {{ date('Y') }} SalonSync. All rights reserved.
        </div>
    </footer>
</body>
</html>
