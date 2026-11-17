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
    <body class="font-sans antialiased bg-[#f6e6f0]">

        {{-- Background: malambot na gradient at mga malabong hugis --}}
        {{-- Background: pink na may tuldok, kapareho ng landing page --}}
<div class="fixed inset-0 -z-10 pointer-events-none" aria-hidden="true"
     style="background-color: #f6e6f0; background-image: radial-gradient(#e2bcd3 1.3px, transparent 1.3px); background-size: 24px 24px;"></div>

        @php
            $role = auth()->check() ? auth()->user()->role : null;
            $panel = in_array($role, ['admin', 'salon_owner'], true);
        @endphp

        @if($panel)
            {{-- ============ PANEL (admin at salon owner): sidebar + topbar ============ --}}
            @php
                $links = $role === 'admin'
                    ? [
                        ['dashboard', 'dashboard', 'Dashboard', 'home'],
                        ['admin.users.index', 'admin.users.*', 'Users', 'users'],
                        ['admin.salons.index', 'admin.salons.*', 'Salons', 'scissors'],
                        ['admin.appointments.index', 'admin.appointments.*', 'Appointments', 'calendar'],
                        ['admin.subscriptions.index', 'admin.subscriptions.*', 'Subscriptions', 'card'],
                        ['admin.reports.index', 'admin.reports.*', 'Reports', 'chart'],
                    ]
                    : [
                        ['dashboard', 'dashboard', 'Dashboard', 'home'],
                        ['owner.appointments.index', 'owner.appointments.*', 'Appointments', 'calendar'],
                        ['owner.services.index', 'owner.services.*', 'Services', 'scissors'],
                        ['owner.promotions.index', 'owner.promotions.*', 'Promotions', 'tag'],
                        ['owner.staff.index', 'owner.staff.*', 'Staff', 'users'],
                        ['owner.customers.index', 'owner.customers.*', 'Customers', 'star'],
                        ['owner.transactions.index', 'owner.transactions.*', 'Transactions', 'receipt'],
                        ['owner.reports.index', 'owner.reports.*', 'Reports', 'chart'],
                        ['owner.settings.edit', 'owner.settings.*', 'Salon profile', 'cog'],
                    ];
            @endphp

            <div x-data="{ sidebar: false }" class="min-h-screen lg:flex">

                {{-- Madilim na overlay sa phone --}}
                <div x-show="sidebar" x-transition.opacity @click="sidebar = false" style="display: none;"
                     class="fixed inset-0 z-30 bg-black/40 lg:hidden"></div>

                {{-- Sidebar --}}
                <aside :class="sidebar ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
                       class="fixed inset-y-0 left-0 z-40 w-64 flex flex-col bg-gradient-to-b from-[#4a1a39] via-[#5f2249] to-[#7b2d5f] text-white shadow-xl transition-transform duration-200 lg:sticky lg:top-0 lg:h-screen lg:shrink-0">

                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-5 h-16 shrink-0 border-b border-white/10">
                        <span class="w-9 h-9 rounded-xl bg-white text-[#7b2d5f] flex items-center justify-center">
                            <x-icon name="scissors" size="w-5 h-5" />
                        </span>
                        <span class="text-lg font-extrabold tracking-tight">SalonSync</span>
                    </a>

                    <div class="px-5 pt-5 pb-2 text-[11px] font-bold uppercase tracking-widest text-white/50">
                        {{ $role === 'admin' ? 'Admin panel' : 'Salon panel' }}
                    </div>

                    <nav class="flex-1 overflow-y-auto px-3 pb-4 space-y-1">
                        @foreach($links as [$routeName, $pattern, $label, $icon])
                            @php $active = request()->routeIs($pattern); @endphp
                            <a href="{{ route($routeName) }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ $active ? 'bg-white text-[#7b2d5f] shadow' : 'text-white/75 hover:bg-white/10 hover:text-white' }}">
                                <x-icon :name="$icon" size="w-5 h-5" />
                                {{ $label }}
                            </a>
                        @endforeach
                    </nav>

                    <div class="px-3 py-4 border-t border-white/10 space-y-1">
                        <a href="{{ route('home') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-white/75 hover:bg-white/10 hover:text-white transition">
                            <x-icon name="pin" size="w-5 h-5" /> Back to website
                        </a>
                    </div>
                </aside>

                {{-- Kanang bahagi: topbar + nilalaman --}}
                <div class="flex-1 min-w-0 flex flex-col">

                    <header class="sticky top-0 z-20 h-16 bg-white/90 backdrop-blur border-b border-[#f0dce8] flex items-center gap-3 px-4 sm:px-6 lg:px-8">
                        <button type="button" @click="sidebar = true" class="lg:hidden p-2 -ml-2 rounded-lg text-[#7b2d5f] hover:bg-[#f3e3ee]" aria-label="Open menu">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>

                        <div class="flex-1 min-w-0 truncate [&_h1]:!text-[#7b2d5f] [&_h1]:!text-xl [&_h1]:!font-extrabold [&_h2]:!text-[#7b2d5f] [&_h2]:!text-xl [&_h2]:!font-extrabold">
                            @isset($header)
                                {{ $header }}
                            @endisset
                        </div>

                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="flex items-center gap-2 pl-1 pr-3 py-1 rounded-full hover:bg-[#f3e3ee] transition">
                                    <span class="w-8 h-8 rounded-full bg-[#7b2d5f] text-white text-sm font-bold flex items-center justify-center uppercase">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                                    <span class="hidden sm:block text-sm font-semibold text-gray-700">{{ auth()->user()->name }}</span>
                                    <svg class="w-4 h-4 text-gray-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.3 7.3a1 1 0 0 1 1.4 0L10 10.6l3.3-3.3a1 1 0 1 1 1.4 1.4l-4 4a1 1 0 0 1-1.4 0l-4-4a1 1 0 0 1 0-1.4z" clip-rule="evenodd"/></svg>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('profile.edit')">Profile</x-dropdown-link>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Log Out</x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </header>

                    <main class="flex-1">
                        {{ $slot }}
                    </main>
                </div>
            </div>
        @else
            {{-- ============ CUSTOMER at bisita: top navigation ============ --}}
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
        @endif
    </body>
</html>
