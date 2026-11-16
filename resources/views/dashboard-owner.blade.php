<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-[#7b2d5f] leading-tight">Dashboard</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('error'))
                <div class="p-3 bg-red-100 text-red-800 rounded-xl border border-red-200">{{ session('error') }}</div>
            @endif

            {{-- Hero --}}
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#5f2249] via-[#7b2d5f] to-[#c45a9a] text-white p-6 sm:p-8 shadow-lg">
                <div class="absolute -top-12 -right-12 w-56 h-56 rounded-full bg-white/10"></div>
                <div class="absolute -bottom-20 right-32 w-44 h-44 rounded-full bg-white/10"></div>
                <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <p class="text-sm opacity-80">Welcome back, {{ auth()->user()->name }}</p>
                        <p class="mt-1 text-3xl font-extrabold">{{ $salon ? $salon->name : 'Your salon' }}</p>
                        <div class="mt-3 flex flex-wrap items-center gap-2 text-xs font-semibold">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15">
                                <x-icon name="scissors" size="w-3.5 h-3.5" /> Salon owner
                            </span>
                            @if($subscription)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15">
                                    <x-icon name="star" size="w-3.5 h-3.5" /> {{ $subscription->plan_name }} plan
                                </span>
                            @endif
                        </div>
                    </div>
                    @if($salon)
                        <a href="{{ route('salons.show', $salon) }}" target="_blank"
                           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-white text-[#7b2d5f] text-sm font-semibold shadow hover:bg-[#f3e3ee] transition self-start sm:self-center">
                            <x-icon name="pin" size="w-4 h-4" /> View my public page
                        </a>
                    @endif
                </div>
            </div>

            @if(! $subscription)
                <div class="bg-white rounded-2xl border border-[#f0dce8] border-t-4 border-t-amber-400 shadow-md p-6 flex flex-col sm:flex-row sm:items-center gap-4">
                    <span class="shrink-0 w-12 h-12 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center">
                        <x-icon name="sparkle" size="w-6 h-6" />
                    </span>
                    <div class="flex-1">
                        <p class="font-bold text-gray-900">Subscribe to start using SalonSync</p>
                        <p class="text-gray-600 mt-1">
                            Choose a plan to unlock staff, services, appointment, and customer management for your salon.
                        </p>
                    </div>
                    <a href="{{ route('home') }}#plans"
                       class="inline-flex items-center justify-center px-5 py-2.5 rounded-full bg-[#7b2d5f] text-white text-sm font-semibold shadow hover:bg-[#5f2249] transition">
                        View plans
                    </a>
                </div>
            @else
                @php
                    $cards = [
                        ["Today's appointments", $stats['today'], 'calendar', 'bg-violet-100 text-violet-700'],
                        ['Pending appointments', $stats['pending'], 'clock', 'bg-amber-100 text-amber-700'],
                        ['Staff members', $stats['staff'], 'users', 'bg-pink-100 text-pink-700'],
                        ['Customers', $stats['customers'], 'star', 'bg-emerald-100 text-emerald-700'],
                        ['Revenue this month', '₱' . number_format($stats['revenue'], 2), 'receipt', 'bg-rose-100 text-rose-700'],
                        ['Current plan', $subscription->plan_name, 'card', 'bg-sky-100 text-sky-700'],
                    ];

                    $checklist = [
                        ['Add a tagline and a short description of your salon', filled($salon->tagline) && filled($salon->about), route('owner.settings.edit')],
                        ['Upload your logo and cover photo', filled($salon->logo_path) || filled($salon->cover_path), route('owner.settings.edit')],
                        ['Set your opening hours', $salon->opening_hours !== null, route('owner.settings.edit')],
                        ['Add your services and prices', $salon->services()->exists(), route('owner.services.index')],
                        ['Add your stylists', $salon->staff()->exists(), route('owner.staff.index')],
                        ['Create a promo or bundle', $salon->promotions()->exists(), route('owner.promotions.index')],
                    ];
                    $done = collect($checklist)->where(1, true)->count();
                    $percent = round($done / count($checklist) * 100);

                    $actions = [
                        ['Add a service', 'scissors', route('owner.services.create')],
                        ['Add a stylist', 'users', route('owner.staff.create')],
                        ['Create a promo', 'tag', route('owner.promotions.create')],
                        ['View appointments', 'calendar', route('owner.appointments.index')],
                    ];
                @endphp

                {{-- Mga bilang --}}
                <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach($cards as [$label, $value, $icon, $tint])
                        <div class="bg-white rounded-2xl border border-[#f0dce8] shadow-sm hover:shadow-md transition p-5 flex items-center gap-4">
                            <span class="shrink-0 w-12 h-12 rounded-xl flex items-center justify-center {{ $tint }}">
                                <x-icon :name="$icon" size="w-6 h-6" />
                            </span>
                            <div class="min-w-0">
                                <p class="text-sm text-gray-500">{{ $label }}</p>
                                <p class="text-2xl font-extrabold text-gray-900 truncate">{{ $value }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="grid lg:grid-cols-3 gap-6">

                    {{-- Mga paparating na appointment --}}
                    <div class="lg:col-span-2 bg-white rounded-2xl border border-[#f0dce8] shadow-sm overflow-hidden">
                        <div class="flex items-center justify-between px-5 pt-5">
                            <p class="font-bold text-[#7b2d5f]">Upcoming appointments</p>
                            <a href="{{ route('owner.appointments.index') }}" class="text-sm font-semibold text-[#7b2d5f] hover:underline">View all</a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm text-left mt-3">
                                <thead class="bg-[#f3e3ee] text-[#7b2d5f]">
                                    <tr>
                                        <th class="px-5 py-3">Customer</th>
                                        <th class="px-5 py-3">Service</th>
                                        <th class="px-5 py-3">Stylist</th>
                                        <th class="px-5 py-3">Date and Time</th>
                                        <th class="px-5 py-3">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($upcoming as $a)
                                        <tr class="border-t border-[#f0dce8] hover:bg-[#fdf6fa]">
                                            <td class="px-5 py-3 font-medium text-gray-900">{{ $a->user->name }}</td>
                                            <td class="px-5 py-3">{{ $a->service_name }}</td>
                                            <td class="px-5 py-3">{{ $a->staff?->name ?? 'Any available' }}</td>
                                            <td class="px-5 py-3 whitespace-nowrap">{{ $a->appointment_at->format('M d, Y h:i A') }}</td>
                                            <td class="px-5 py-3"><x-status-badge :status="$a->status" /></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-5 py-10 text-center text-gray-500">No upcoming appointments.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="space-y-6">

                        {{-- Setup checklist --}}
                        @if($done < count($checklist))
                            <div class="bg-white rounded-2xl border border-[#f0dce8] shadow-sm p-5">
                                <div class="flex items-center justify-between">
                                    <p class="font-bold text-[#7b2d5f]">Set up your salon page</p>
                                    <p class="text-sm font-semibold text-gray-500">{{ $done }}/{{ count($checklist) }}</p>
                                </div>
                                <div class="mt-3 h-2 rounded-full bg-[#f3e3ee] overflow-hidden">
                                    <div class="h-2 rounded-full bg-gradient-to-r from-[#7b2d5f] to-[#c45a9a]" style="width: {{ $percent }}%"></div>
                                </div>
                                <ul class="mt-4 space-y-2.5 text-sm">
                                    @foreach($checklist as [$text, $ok, $url])
                                        <li class="flex items-start gap-3">
                                            <span class="mt-0.5 shrink-0 w-5 h-5 rounded-full flex items-center justify-center {{ $ok ? 'bg-green-500 text-white' : 'border-2 border-gray-300 text-transparent' }}"><x-icon name="check" size="w-3 h-3" /></span>
                                            @if($ok)
                                                <span class="text-gray-400 line-through">{{ $text }}</span>
                                            @else
                                                <a href="{{ $url }}" class="text-[#7b2d5f] hover:underline">{{ $text }}</a>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            <div class="bg-white rounded-2xl border border-[#f0dce8] shadow-sm p-5 flex items-center gap-3">
                                <span class="shrink-0 w-10 h-10 rounded-xl bg-green-100 text-green-700 flex items-center justify-center"><x-icon name="check" size="w-5 h-5" /></span>
                                <div>
                                    <p class="font-bold text-gray-900">Your salon page is complete</p>
                                    <a href="{{ route('salons.show', $salon) }}" target="_blank" class="text-sm font-semibold text-[#7b2d5f] hover:underline">View my public page</a>
                                </div>
                            </div>
                        @endif

                        {{-- Mabilis na aksyon --}}
                        <div class="bg-white rounded-2xl border border-[#f0dce8] shadow-sm p-5">
                            <p class="font-bold text-[#7b2d5f]">Quick actions</p>
                            <div class="mt-3 grid grid-cols-2 gap-3">
                                @foreach($actions as [$label, $icon, $url])
                                    <a href="{{ $url }}" class="flex flex-col items-center gap-2 p-3 rounded-xl border border-[#f0dce8] hover:bg-[#fdf6fa] hover:border-[#7b2d5f]/40 transition text-center">
                                        <span class="w-10 h-10 rounded-xl bg-[#f3e3ee] text-[#7b2d5f] flex items-center justify-center"><x-icon :name="$icon" size="w-5 h-5" /></span>
                                        <span class="text-xs font-semibold text-gray-700">{{ $label }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
