<x-public-layout>
@php
    $user = auth()->user();

    $heroTitle = $home['hero_title'] ?? 'Beauty starts here at SalonSync';
    $heroSub   = $home['hero_subtitle'] ?? 'Discover trusted salons, compare services and prices, and book your next appointment in minutes.';

    $aboutTitle = $home['about_title'] ?? 'About SalonSync';
    $aboutText  = $home['about_text'] ?? 'SalonSync is a web-based SaaS platform that helps salon businesses manage customers, services, appointments, staff, and transactions in one system. Salon businesses subscribe to the platform and use its management tools to improve their daily operations, while customers discover salons, compare prices, and book online.';

    $aboutPoints = collect(preg_split('/\r\n|\r|\n/', (string) ($home['about_points'] ?? '')))
        ->map(fn ($line) => trim($line))
        ->filter()
        ->values();

    if ($aboutPoints->isEmpty()) {
        $aboutPoints = collect([
            'Manage customers, staff, and services in one place',
            'Online booking with real-time opening hours',
            'Payments, receipts, and revenue reports',
            'Promos and bundles that bring customers back',
        ]);
    }

    $aboutIcons = ['scissors', 'calendar', 'card', 'gift', 'chart', 'star'];
@endphp

    @unless($cmsOnline)
        <div class="bg-amber-100 text-amber-900 text-sm text-center px-4 py-2">
            Some content is temporarily unavailable. Please try again later.
        </div>
    @endunless

    {{-- ============ HERO ============ --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-[#5f2249] via-[#7b2d5f] to-[#c45a9a] text-white">
        <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full bg-white/10"></div>
        <div class="absolute -bottom-32 -right-16 w-[28rem] h-[28rem] rounded-full bg-white/10"></div>

        <div class="relative max-w-6xl mx-auto px-4 py-20 lg:py-28 grid lg:grid-cols-2 gap-12 items-center">
            <div>
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 text-sm font-medium mb-5">
                    <x-icon name="sparkle" size="w-4 h-4" /> The all-in-one platform for salons
                </span>
                <h1 class="text-4xl sm:text-5xl font-extrabold leading-tight">{{ $heroTitle }}</h1>
                <p class="mt-5 text-lg text-white/85 max-w-xl">{{ $heroSub }}</p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('salons.index') }}"
                       class="px-6 py-3 rounded-full bg-white text-[#7b2d5f] font-semibold shadow hover:bg-[#f3e3ee] transition">
                        Find a salon
                    </a>
                    @guest
                        <a href="{{ route('register', ['type' => 'salon_owner']) }}"
                           class="px-6 py-3 rounded-full border-2 border-white font-semibold hover:bg-white hover:text-[#7b2d5f] transition">
                            Register your salon
                        </a>
                    @endguest
                </div>

                <div class="mt-10 grid grid-cols-3 gap-4 max-w-md">
                    <div>
                        <p class="text-3xl font-extrabold">{{ number_format($stats['salons']) }}</p>
                        <p class="text-sm text-white/75">Partner salons</p>
                    </div>
                    <div>
                        <p class="text-3xl font-extrabold">{{ number_format($stats['services']) }}</p>
                        <p class="text-sm text-white/75">Services listed</p>
                    </div>
                    <div>
                        <p class="text-3xl font-extrabold">{{ number_format($stats['bookings']) }}</p>
                        <p class="text-sm text-white/75">Bookings made</p>
                    </div>
                </div>
            </div>

            {{-- Mock cards --}}
            <div class="hidden lg:block relative h-80">
                <div class="absolute top-0 right-6 w-80 bg-white text-gray-800 rounded-2xl shadow-2xl p-5 rotate-2">
                    <p class="text-xs uppercase tracking-wide text-gray-400">Upcoming appointment</p>
                    <p class="mt-1 text-lg font-bold">Hair Color &amp; Spa</p>
                    <p class="text-sm text-gray-500">Saturday &middot; 2:00 PM</p>
                    <div class="mt-4 flex items-center justify-between">
                        <span class="px-3 py-1 rounded-full bg-green-100 text-green-800 text-xs font-semibold">Confirmed</span>
                        <span class="font-bold text-[#7b2d5f]">₱1,800</span>
                    </div>
                </div>
                <div class="absolute bottom-0 left-4 w-72 bg-white text-gray-800 rounded-2xl shadow-2xl p-5 -rotate-3">
                    <p class="text-xs uppercase tracking-wide text-gray-400">Payment received</p>
                    <p class="mt-1 text-2xl font-extrabold text-[#7b2d5f]">₱1,200.00</p>
                    <p class="text-sm text-gray-500">Receipt sent to the customer</p>
                    <div class="mt-3 h-2 rounded-full bg-[#f3e3ee] overflow-hidden"><div class="h-2 w-4/5 bg-[#7b2d5f] rounded-full"></div></div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ ABOUT (puting banda) ============ --}}
    <section id="about" class="bg-white/85 backdrop-blur py-20 border-b border-[#f0dce8]">
        <div class="max-w-6xl mx-auto px-4 grid lg:grid-cols-2 gap-12 items-center">
            <div>
                <p class="text-sm font-bold tracking-widest text-[#c45a9a] uppercase">Who we are</p>
                <h2 class="mt-2 text-3xl sm:text-4xl font-extrabold text-gray-900">{{ $aboutTitle }}</h2>
                <p class="mt-5 text-gray-600 leading-relaxed whitespace-pre-line">{{ $aboutText }}</p>
            </div>
            <div class="grid sm:grid-cols-2 gap-4">
                @foreach($aboutPoints->take(6) as $i => $point)
                    <div class="bg-[#fbf3f8] rounded-2xl border border-[#f0dce8] p-5">
                        <div class="w-11 h-11 rounded-xl bg-[#7b2d5f] text-white flex items-center justify-center">
                            <x-icon :name="$aboutIcons[$i % count($aboutIcons)]" size="w-5 h-5" />
                        </div>
                        <p class="mt-3 font-semibold text-gray-800 leading-snug">{{ $point }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ HOW IT WORKS (may tuldok na disenyo) ============ --}}
    <section class="py-20" style="background-color: #f6e6f0; background-image: radial-gradient(#e2bcd3 1.3px, transparent 1.3px); background-size: 24px 24px;">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center max-w-2xl mx-auto">
                <p class="text-sm font-bold tracking-widest text-[#c45a9a] uppercase">How it works</p>
                <h2 class="mt-2 text-3xl sm:text-4xl font-extrabold text-gray-900">Simple for customers. Powerful for salons.</h2>
            </div>

            <div class="mt-10 grid md:grid-cols-2 gap-6">
                <div class="bg-white rounded-2xl border border-[#f0dce8] p-6 shadow-sm">
                    <p class="font-bold text-[#7b2d5f]">For customers</p>
                    <ol class="mt-4 space-y-4">
                        @foreach([['Browse salons', 'Compare services, prices, opening hours, and promos.'], ['Book online', 'Pick a service, a stylist, and a time that fits the salon schedule.'], ['Get pampered', 'Receive email updates and a receipt for every payment.']] as $i => [$t, $d])
                            <li class="flex gap-4">
                                <span class="shrink-0 w-9 h-9 rounded-full bg-[#7b2d5f] text-white font-bold flex items-center justify-center">{{ $i + 1 }}</span>
                                <div><p class="font-semibold text-gray-900">{{ $t }}</p><p class="text-sm text-gray-500">{{ $d }}</p></div>
                            </li>
                        @endforeach
                    </ol>
                </div>
                <div class="bg-white rounded-2xl border border-[#f0dce8] p-6 shadow-sm">
                    <p class="font-bold text-[#7b2d5f]">For salon owners</p>
                    <ol class="mt-4 space-y-4">
                        @foreach([['Register and subscribe', 'Choose a plan that fits the size of your team.'], ['Set up your salon', 'Add your photos, hours, services, prices, staff, and promos.'], ['Run your business', 'Manage appointments, customers, payments, and revenue.']] as $i => [$t, $d])
                            <li class="flex gap-4">
                                <span class="shrink-0 w-9 h-9 rounded-full bg-[#c45a9a] text-white font-bold flex items-center justify-center">{{ $i + 1 }}</span>
                                <div><p class="font-semibold text-gray-900">{{ $t }}</p><p class="text-sm text-gray-500">{{ $d }}</p></div>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ ANNOUNCEMENTS ============ --}}
    <section class="py-20">
        <div class="max-w-6xl mx-auto px-4">
            <p class="text-sm font-bold tracking-widest text-[#c45a9a] uppercase">What's new</p>
            <h2 class="mt-2 text-3xl sm:text-4xl font-extrabold text-gray-900">Latest announcements</h2>

            <div class="mt-8 grid md:grid-cols-3 gap-5">
                @forelse($announcements as $a)
                    <article class="bg-white rounded-2xl border border-[#f0dce8] border-t-4 border-t-[#7b2d5f] p-6 shadow-sm hover:shadow-lg transition">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-[#f3e3ee] text-[#7b2d5f] text-xs font-bold">
                                <x-icon name="megaphone" size="w-3.5 h-3.5" /> NEWS
                            </span>
                            @if($a['date'])
                                <span class="text-xs text-gray-400">{{ $a['date'] }}</span>
                            @endif
                        </div>
                        <h3 class="mt-4 text-lg font-bold text-gray-900 leading-snug">{{ $a['title'] }}</h3>
                        <p class="mt-2 text-sm text-gray-600 leading-relaxed">{!! nl2br(e(\Illuminate\Support\Str::limit($a['text'], 180))) !!}</p>
                    </article>
                @empty
                    <p class="md:col-span-3 text-gray-500">No announcements yet. Please check back soon.</p>
                @endforelse
            </div>
        </div>
    </section>

    {{-- ============ PROMOTIONS AND BUNDLES (may kulay na banda) ============ --}}
    <section class="py-20 bg-gradient-to-br from-[#f3dff0] via-[#ecd5ec] to-[#e4cbe9]">
        <div class="max-w-6xl mx-auto px-4">
            <p class="text-sm font-bold tracking-widest text-[#7b2d5f] uppercase">Save more</p>
            <h2 class="mt-2 text-3xl sm:text-4xl font-extrabold text-gray-900">Promotions and bundles</h2>
            <p class="mt-2 text-gray-600">Current deals from our partner salons.</p>

            <div class="mt-8 grid md:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse($promotions as $promo)
                    <div class="bg-white rounded-2xl border border-[#f0dce8] shadow-sm hover:shadow-xl hover:-translate-y-1 transition duration-300 overflow-hidden flex flex-col">
                        <div class="px-6 py-5 text-white bg-gradient-to-r {{ $promo->isBundle() ? 'from-[#2d5f7b] to-[#5ac4b8]' : 'from-[#7b2d5f] to-[#c45a9a]' }}">
                            <p class="inline-flex items-center gap-1.5 text-xs uppercase tracking-widest text-white/80">
                                <x-icon :name="$promo->isBundle() ? 'gift' : 'flame'" size="w-4 h-4" />
                                {{ $promo->isBundle() ? 'Bundle' : 'Promo' }}
                            </p>
                            <p class="mt-1 text-2xl font-extrabold">{{ $promo->badge() }}</p>
                        </div>
                        <div class="p-6 flex-1 flex flex-col">
                            <h3 class="font-bold text-gray-900 text-lg leading-snug">{{ $promo->title }}</h3>
                            <a href="{{ route('salons.show', $promo->salon) }}" class="text-sm text-[#7b2d5f] font-medium hover:underline">{{ $promo->salon->name }}</a>
                            @if($promo->description)
                                <p class="mt-2 text-sm text-gray-600">{{ $promo->description }}</p>
                            @endif
                            @if($promo->includedList())
                                <ul class="mt-3 space-y-1 text-sm text-gray-700">
                                    @foreach($promo->includedList() as $item)
                                        <li class="flex items-start gap-2"><x-icon name="check" size="w-4 h-4 mt-0.5 shrink-0" class="text-[#7b2d5f]" /><span>{{ $item }}</span></li>
                                    @endforeach
                                </ul>
                            @endif
                            <div class="mt-auto pt-4 flex items-center justify-between text-xs text-gray-500">
                                <span>{{ $promo->valid_until ? 'Until ' . $promo->valid_until->format('M d, Y') : 'Ongoing' }}</span>
                                <a href="{{ route('salons.show', $promo->salon) }}" class="font-semibold text-[#7b2d5f] hover:underline">View salon &rarr;</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="md:col-span-3 text-gray-600">No promotions right now. Check back soon.</p>
                @endforelse
            </div>
        </div>
    </section>

    {{-- ============ PARTNER SALONS (puting banda) ============ --}}
    <section class="py-20 bg-white/85 backdrop-blur border-y border-[#f0dce8]">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex items-end justify-between flex-wrap gap-3">
                <div>
                    <p class="text-sm font-bold tracking-widest text-[#c45a9a] uppercase">Our partners</p>
                    <h2 class="mt-2 text-3xl sm:text-4xl font-extrabold text-gray-900">Featured salons</h2>
                    <p class="mt-2 text-gray-500">Every salon sets its own services, prices, and opening hours.</p>
                </div>
                <a href="{{ route('salons.index') }}" class="text-[#7b2d5f] font-semibold hover:underline">View all salons &rarr;</a>
            </div>

            <div class="mt-8 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($salons as $salon)
                    <x-salon-card :salon="$salon" />
                @empty
                    <p class="sm:col-span-3 text-gray-500">No salons yet. Be the first to register your salon.</p>
                @endforelse
            </div>
        </div>
    </section>

    {{-- ============ PLANS ============ --}}
    <section id="plans" class="py-20" style="background-color: #f6e6f0; background-image: radial-gradient(#e2bcd3 1.3px, transparent 1.3px); background-size: 24px 24px;">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center max-w-2xl mx-auto">
                <p class="text-sm font-bold tracking-widest text-[#7b2d5f] uppercase">For salon owners</p>
                <h2 class="mt-2 text-3xl sm:text-4xl font-extrabold text-gray-900">Choose the plan that fits your salon</h2>
                <p class="mt-3 text-gray-600">Simple monthly pricing. Upgrade or cancel anytime.</p>
            </div>

            <div class="mt-12 grid md:grid-cols-3 gap-6 items-stretch">
                @forelse($plans as $plan)
                    @php
                        $featured   = (bool) ($plan['is_featured'] ?? false);
                        $staffLimit = $plan['max_staff'] ?? null;
                        $apptLimit  = $plan['max_monthly_appointments'] ?? null;
                        $features   = collect(preg_split('/\r\n|\r|\n/', (string) ($plan['features'] ?? '')))
                            ->map(fn ($l) => trim($l))->filter()->values();
                    @endphp
                    <div class="relative flex flex-col rounded-3xl p-8 {{ $featured ? 'bg-gradient-to-b from-[#7b2d5f] to-[#5f2249] text-white shadow-2xl md:-translate-y-3' : 'bg-white border border-[#f0dce8] shadow-sm' }}">
                        @if($featured)
                            <span class="absolute -top-3 left-1/2 -translate-x-1/2 px-4 py-1 rounded-full bg-[#ffd166] text-[#5f2249] text-xs font-extrabold tracking-wide">MOST POPULAR</span>
                        @endif

                        <h3 class="text-xl font-bold">{{ $plan['name'] }}</h3>
                        <p class="mt-1 text-sm {{ $featured ? 'text-white/80' : 'text-gray-500' }}">{{ $plan['description'] ?? '' }}</p>

                        <p class="mt-6 flex items-end gap-1">
                            <span class="text-4xl font-extrabold">₱{{ number_format($plan['price']) }}</span>
                            <span class="mb-1 text-sm {{ $featured ? 'text-white/70' : 'text-gray-400' }}">/ month</span>
                        </p>

                        <div class="mt-5 flex flex-wrap gap-2 text-xs font-semibold">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full {{ $featured ? 'bg-white/15' : 'bg-[#f3e3ee] text-[#7b2d5f]' }}">
                                <x-icon name="users" size="w-3.5 h-3.5" /> {{ $staffLimit ? $staffLimit . ' staff' : 'Unlimited staff' }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full {{ $featured ? 'bg-white/15' : 'bg-[#f3e3ee] text-[#7b2d5f]' }}">
                                <x-icon name="calendar" size="w-3.5 h-3.5" /> {{ $apptLimit ? number_format($apptLimit) . ' bookings/mo' : 'Unlimited bookings' }}
                            </span>
                        </div>

                        <ul class="mt-6 space-y-3 text-sm flex-1">
                            @foreach($features as $feature)
                                <li class="flex gap-3">
                                    <span class="shrink-0 w-5 h-5 rounded-full flex items-center justify-center {{ $featured ? 'bg-white text-[#7b2d5f]' : 'bg-[#7b2d5f] text-white' }}">
                                        <x-icon name="check" size="w-3 h-3" />
                                    </span>
                                    <span>{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>

                        <div class="mt-8">
                            @guest
                                <a href="{{ route('register', ['type' => 'salon_owner']) }}"
                                   class="block text-center px-6 py-3 rounded-full font-semibold transition {{ $featured ? 'bg-white text-[#7b2d5f] hover:bg-[#f3e3ee]' : 'bg-[#7b2d5f] text-white hover:bg-[#5f2249]' }}">
                                    Get started
                                </a>
                            @else
                                @if($user->isSalonOwner())
                                    <a href="{{ route('subscriptions.create', $plan['documentId']) }}"
                                       class="block text-center px-6 py-3 rounded-full font-semibold transition {{ $featured ? 'bg-white text-[#7b2d5f] hover:bg-[#f3e3ee]' : 'bg-[#7b2d5f] text-white hover:bg-[#5f2249]' }}">
                                        Subscribe
                                    </a>
                                @else
                                    <p class="text-center text-sm {{ $featured ? 'text-white/70' : 'text-gray-400' }}">Plans are for salon owners.</p>
                                @endif
                            @endguest
                        </div>
                    </div>
                @empty
                    <p class="md:col-span-3 text-center text-gray-600">Plans are not available right now.</p>
                @endforelse
            </div>

            <div class="mt-10 grid sm:grid-cols-2 lg:grid-cols-4 gap-4 text-sm text-gray-700">
                @foreach([['calendar', 'Online booking with your own opening hours'], ['receipt', 'Payment records and printable receipts'], ['mail', 'Automatic email notifications'], ['lock', 'Your data stays separate from other salons']] as [$icon, $text])
                    <div class="flex items-center gap-3 bg-white rounded-xl border border-[#f0dce8] px-4 py-3">
                        <span class="shrink-0 w-9 h-9 rounded-lg bg-[#f3e3ee] text-[#7b2d5f] flex items-center justify-center"><x-icon :name="$icon" size="w-5 h-5" /></span>
                        <span>{{ $text }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ CTA ============ --}}
    <section class="max-w-6xl mx-auto px-4 pt-20">
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-[#7b2d5f] to-[#c45a9a] text-white px-8 py-12 text-center">
            <div class="absolute -top-10 -left-10 w-48 h-48 rounded-full bg-white/10"></div>
            <div class="absolute -bottom-12 -right-8 w-56 h-56 rounded-full bg-white/10"></div>
            <h2 class="relative text-3xl font-extrabold">Ready to grow your salon?</h2>
            <p class="relative mt-2 text-white/85">Join SalonSync and manage everything in one place.</p>
            <div class="relative mt-6 flex flex-wrap justify-center gap-3">
                @guest
                    <a href="{{ route('register', ['type' => 'salon_owner']) }}" class="px-6 py-3 rounded-full bg-white text-[#7b2d5f] font-semibold hover:bg-[#f3e3ee] transition">Register your salon</a>
                @endguest
                <a href="{{ route('salons.index') }}" class="px-6 py-3 rounded-full border-2 border-white font-semibold hover:bg-white hover:text-[#7b2d5f] transition">Browse salons</a>
            </div>
        </div>
    </section>

</x-public-layout>
