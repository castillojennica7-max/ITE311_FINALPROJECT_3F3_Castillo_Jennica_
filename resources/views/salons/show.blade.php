<x-public-layout :title="$salon->name">
@php
    $user     = auth()->user();
    $hours    = $salon->hours();
    $todayKey = strtolower(now()->format('D'));
    $cover    = $salon->coverUrl();
    $logo     = $salon->logoUrl();
    $isOpen   = $salon->isOpenAt(now());
    $palettes = [
        'from-[#7b2d5f] to-[#c45a9a]',
        'from-[#2d5f7b] to-[#5ac4b8]',
        'from-[#7b5f2d] to-[#c4a05a]',
        'from-[#5f2d7b] to-[#9a5ac4]',
        'from-[#2d7b5f] to-[#5ac49a]',
        'from-[#7b2d3f] to-[#c45a6a]',
    ];
    $gradient = $palettes[$salon->id % count($palettes)];
@endphp

    {{-- Cover --}}
    <section class="relative h-56 sm:h-72 overflow-hidden {{ $cover ? '' : 'bg-gradient-to-br ' . $gradient }}">
        @if($cover)
            <img src="{{ $cover }}" alt="{{ $salon->name }}" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
        @else
            <div class="absolute -top-16 -right-10 w-72 h-72 rounded-full bg-white/15"></div>
            <div class="absolute -bottom-20 left-20 w-64 h-64 rounded-full bg-white/10"></div>
        @endif
    </section>

    {{-- Header card --}}
    <div class="max-w-6xl mx-auto px-4 -mt-16 relative">
        <div class="bg-white rounded-2xl shadow-lg border border-[#f0dce8] p-6 flex flex-col md:flex-row md:items-center gap-5">
            <div class="w-24 h-24 rounded-2xl bg-white shadow ring-4 ring-white overflow-hidden flex items-center justify-center shrink-0 -mt-14 md:mt-0 bg-[#f3e3ee]">
                @if($logo)
                    <img src="{{ $logo }}" alt="" class="w-full h-full object-cover">
                @else
                    <span class="text-3xl font-extrabold text-[#7b2d5f]">{{ $salon->initials() }}</span>
                @endif
            </div>

            <div class="flex-1">
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900">{{ $salon->name }}</h1>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $isOpen ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                        {{ $isOpen ? 'Open now' : 'Closed now' }}
                    </span>
                </div>
                @if($salon->tagline)
                    <p class="text-gray-500 mt-1">{{ $salon->tagline }}</p>
                @endif
                <ul class="mt-3 flex flex-wrap gap-x-6 gap-y-1 text-sm text-gray-600">
                    @if($salon->address)
                        <li class="flex items-center gap-2"><x-icon name="pin" size="w-4 h-4" class="text-[#c45a9a]" /> {{ $salon->address }}</li>
                    @endif
                    @if($salon->phone)
                        <li class="flex items-center gap-2"><x-icon name="phone" size="w-4 h-4" class="text-[#c45a9a]" /> {{ $salon->phone }}</li>
                    @endif
                    <li class="flex items-center gap-2"><x-icon name="clock" size="w-4 h-4" class="text-[#c45a9a]" /> {{ $salon->todayLabel() }}</li>
                </ul>
            </div>

            <div class="shrink-0">
                @guest
                    <a href="{{ route('login') }}" class="inline-block px-6 py-3 rounded-full bg-[#7b2d5f] text-white font-semibold hover:bg-[#5f2249] transition">Log in to book</a>
                @else
                    @if($user->isCustomer())
                        <a href="{{ route('appointments.book', $salon) }}" class="inline-block px-6 py-3 rounded-full bg-[#7b2d5f] text-white font-semibold hover:bg-[#5f2249] transition">Book an appointment</a>
                    @endif
                @endguest
            </div>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-4 pt-10 grid lg:grid-cols-3 gap-8">

        {{-- Left column --}}
        <div class="lg:col-span-2 space-y-10">

            @if($salon->about)
                <section>
                    <h2 class="text-xl font-extrabold text-gray-900">About {{ $salon->name }}</h2>
                    <p class="mt-3 text-gray-600 leading-relaxed whitespace-pre-line">{{ $salon->about }}</p>
                </section>
            @endif

            @if($promotions->isNotEmpty())
                <section>
                    <h2 class="text-xl font-extrabold text-gray-900">Promos and bundles</h2>
                    <div class="mt-4 grid sm:grid-cols-2 gap-4">
                        @foreach($promotions as $promo)
                            <div class="bg-white rounded-2xl border border-[#f0dce8] shadow-sm overflow-hidden">
                                <div class="px-5 py-3 text-white bg-gradient-to-r {{ $promo->isBundle() ? 'from-[#2d5f7b] to-[#5ac4b8]' : 'from-[#7b2d5f] to-[#c45a9a]' }}">
                                    <p class="inline-flex items-center gap-1.5 text-xs uppercase tracking-widest text-white/80">
                                        <x-icon :name="$promo->isBundle() ? 'gift' : 'flame'" size="w-4 h-4" />
                                        {{ $promo->isBundle() ? 'Bundle' : 'Promo' }}
                                    </p>
                                    <p class="text-xl font-extrabold">{{ $promo->badge() }}</p>
                                </div>
                                <div class="p-5">
                                    <h3 class="font-bold text-gray-900">{{ $promo->title }}</h3>
                                    @if($promo->description)
                                        <p class="mt-1 text-sm text-gray-600">{{ $promo->description }}</p>
                                    @endif
                                    @if($promo->includedList())
                                        <ul class="mt-3 space-y-1 text-sm text-gray-700">
                                            @foreach($promo->includedList() as $item)
                                                <li class="flex items-start gap-2"><x-icon name="check" size="w-4 h-4 mt-0.5 shrink-0" class="text-[#7b2d5f]" /><span>{{ $item }}</span></li>
                                            @endforeach
                                        </ul>
                                    @endif
                                    <p class="mt-3 text-xs text-gray-400">{{ $promo->valid_until ? 'Valid until ' . $promo->valid_until->format('M d, Y') : 'Ongoing offer' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
@if($salon->address)
    <section>
        <h2 class="text-xl font-extrabold text-gray-900">Location</h2>
        <p class="mt-1 text-gray-600 flex items-center gap-2">
            <x-icon name="pin" size="w-4 h-4" class="text-[#c45a9a]" /> {{ $salon->address }}
        </p>
        <div class="mt-4 overflow-hidden rounded-2xl border border-[#f0dce8] shadow-sm">
            <iframe title="Map of {{ $salon->name }}" class="w-full h-72" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    src="https://maps.google.com/maps?q={{ urlencode($salon->name . ', ' . $salon->address) }}&amp;output=embed"></iframe>
        </div>
        <a href="https://www.google.com/maps/search/?api=1&amp;query={{ urlencode($salon->name . ', ' . $salon->address) }}"
           target="_blank" rel="noopener"
           class="inline-block mt-3 text-sm font-semibold text-[#7b2d5f] hover:underline">Open in Google Maps</a>
    </section>
@endif
            <section>
                <h2 class="text-xl font-extrabold text-gray-900">Services and prices</h2>

                @forelse($services as $category => $items)
                    <h3 class="mt-6 mb-3 text-sm font-bold uppercase tracking-widest text-[#c45a9a]">
                        {{ \App\Models\SalonService::CATEGORIES[$category] ?? ucfirst($category) }}
                    </h3>
                    <div class="space-y-3">
                        @foreach($items as $service)
                            <div class="flex gap-4 bg-white rounded-2xl border border-[#f0dce8] shadow-sm p-3 hover:shadow-md transition">
                                <img src="{{ $service->imageUrl() }}" alt="{{ $service->name }}" class="w-24 h-24 rounded-xl object-cover shrink-0 bg-[#f3e3ee]">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-3">
                                        <h4 class="font-bold text-gray-900">{{ $service->name }}</h4>
                                        <p class="font-extrabold text-[#7b2d5f] whitespace-nowrap">₱{{ number_format($service->price) }}</p>
                                    </div>
                                    @if($service->description)
                                        <p class="mt-1 text-sm text-gray-600">{{ $service->description }}</p>
                                    @endif
                                    <div class="mt-2 flex items-center justify-between">
                                        <span class="inline-flex items-center gap-1 text-xs text-gray-500"><x-icon name="clock" size="w-3.5 h-3.5" /> {{ $service->duration_minutes }} minutes</span>
                                        @guest
                                            <a href="{{ route('login') }}" class="text-sm font-semibold text-[#7b2d5f] hover:underline">Log in to book</a>
                                        @else
                                            @if($user->isCustomer())
                                                <a href="{{ route('appointments.book', ['salon' => $salon, 'service' => $service->id]) }}"
                                                   class="text-sm font-semibold text-[#7b2d5f] hover:underline">Book this &rarr;</a>
                                            @endif
                                        @endguest
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @empty
                    <p class="mt-4 text-gray-500">This salon has not added its services yet.</p>
                @endforelse
            </section>
        </div>

        {{-- Right column --}}
        <aside class="space-y-6">
            <div class="bg-white rounded-2xl border border-[#f0dce8] shadow-sm p-6">
                <h2 class="font-extrabold text-gray-900">Opening hours</h2>
                <ul class="mt-4 divide-y text-sm">
                    @foreach(\App\Models\Salon::DAYS as $key => $label)
                        <li class="flex justify-between py-2 {{ $key === $todayKey ? 'font-bold text-[#7b2d5f]' : 'text-gray-600' }}">
                            <span>{{ $label }}</span>
                            <span>
                                @if($hours[$key]['closed'])
                                    Closed
                                @else
                                    {{ \App\Models\Salon::formatTime($hours[$key]['open']) }} - {{ \App\Models\Salon::formatTime($hours[$key]['close']) }}
                                @endif
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>

            @if($staff->isNotEmpty())
                <div class="bg-white rounded-2xl border border-[#f0dce8] shadow-sm p-6">
                    <h2 class="font-extrabold text-gray-900">Meet the team</h2>
                    <ul class="mt-4 space-y-3">
                        @foreach($staff as $member)
                            <li class="flex items-center gap-3">
                                <span class="w-10 h-10 rounded-full bg-[#f3e3ee] text-[#7b2d5f] font-bold flex items-center justify-center">
                                    {{ strtoupper(mb_substr($member->name, 0, 1)) }}
                                </span>
                                <div>
                                    <p class="font-semibold text-gray-900 leading-tight">{{ $member->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $member->role }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <a href="{{ route('salons.index') }}" class="block text-center text-sm font-semibold text-[#7b2d5f] hover:underline">&larr; Back to all salons</a>
        </aside>
    </div>
</x-public-layout>
