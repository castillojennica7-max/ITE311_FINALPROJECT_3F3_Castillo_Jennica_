<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">Dashboard</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Welcome banner --}}
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#5f2249] via-[#7b2d5f] to-[#c45a9a] text-white p-6 sm:p-8 shadow-lg">
                <div class="absolute -top-10 -right-10 w-48 h-48 rounded-full bg-white/10"></div>
                <div class="absolute -bottom-16 right-24 w-40 h-40 rounded-full bg-white/10"></div>
                <div class="relative flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm opacity-80">Welcome back,</p>
                        <p class="text-3xl font-extrabold">{{ auth()->user()->name }}</p>
                        <p class="text-sm mt-1 opacity-80 capitalize">{{ auth()->user()->role }} account</p>
                    </div>
                    @unless($isAdmin)
                        <a href="{{ route('appointments.create') }}"
                           class="hidden sm:inline-flex items-center gap-2 px-5 py-3 rounded-full bg-white text-[#7b2d5f] font-semibold shadow hover:bg-[#f3e3ee] transition">
                            <x-icon name="calendar" size="w-5 h-5" /> Book an appointment
                        </a>
                    @endunless
                </div>
            </div>

            @if($isAdmin)

                {{-- Mga bilang --}}
                @php
                    $cards = [
                        ['Customers', $stats['customers'], 'users'],
                        ['Total appointments', $stats['appointments'], 'calendar'],
                        ['Pending appointments', $stats['pending'], 'clock'],
                        ['Active salons', $stats['salons'], 'scissors'],
                        ['Active subscriptions', $stats['subscriptions'], 'card'],
                        ['Monthly recurring revenue', '₱' . number_format($stats['mrr'], 2), 'chart'],
                    ];
                @endphp
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($cards as [$label, $value, $icon])
                        <div class="bg-white rounded-2xl border border-[#f0dce8] border-t-4 border-t-[#7b2d5f] shadow-sm hover:shadow-md transition p-5 flex items-center gap-4">
                            <span class="shrink-0 w-12 h-12 rounded-xl bg-[#f3e3ee] text-[#7b2d5f] flex items-center justify-center">
                                <x-icon :name="$icon" size="w-6 h-6" />
                            </span>
                            <div>
                                <p class="text-sm text-gray-500">{{ $label }}</p>
                                <p class="text-2xl font-extrabold text-gray-900">{{ $value }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Mga paparating na appointment --}}
                <div class="bg-white rounded-2xl border border-[#f0dce8] shadow-sm overflow-x-auto">
                    <p class="px-5 pt-5 font-bold text-[#7b2d5f]">Upcoming appointments</p>
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
                                    <td class="px-5 py-3">{{ $a->user->name }}</td>
                                    <td class="px-5 py-3">{{ $a->service_name }}</td>
                                    <td class="px-5 py-3">{{ $a->staff?->name ?? 'Any available' }}</td>
                                    <td class="px-5 py-3">{{ $a->appointment_at->format('M d, Y h:i A') }}</td>
                                    <td class="px-5 py-3"><x-status-badge :status="$a->status" /></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-6 text-center text-gray-500">No upcoming appointments.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            @else

                {{-- Susunod na appointment --}}
                <div class="bg-white rounded-2xl border border-[#f0dce8] border-t-4 border-t-[#7b2d5f] shadow-md p-5 sm:p-6">
                    <div class="flex items-start gap-4">
                        <span class="shrink-0 w-12 h-12 rounded-xl bg-[#f3e3ee] text-[#7b2d5f] flex items-center justify-center">
                            <x-icon name="calendar" size="w-6 h-6" />
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-[#7b2d5f]">Next appointment</p>
                            @if($next)
                                <p class="text-xl font-extrabold text-gray-900">{{ $next->service_name }}</p>
                                <p class="text-gray-700 flex items-center gap-1.5 mt-1">
                                    <x-icon name="clock" size="w-4 h-4" class="text-[#7b2d5f]" />
                                    {{ $next->appointment_at->format('M d, Y h:i A') }}
                                </p>
                                <p class="text-sm text-gray-500 mt-2 flex flex-wrap items-center gap-2">
                                    <span>Stylist: {{ $next->staff?->name ?? 'Any available' }}</span>
                                    <x-status-badge :status="$next->status" />
                                </p>
                            @else
                                <p class="text-gray-700 mt-1">You have no upcoming appointments.</p>
                                <a href="{{ route('appointments.create') }}"
                                   class="inline-flex items-center gap-2 mt-3 px-4 py-2 rounded-full bg-[#7b2d5f] text-white text-sm font-semibold hover:bg-[#5f2249] transition">
                                    Book an appointment
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Kamakailang appointments --}}
                <div class="bg-white rounded-2xl border border-[#f0dce8] shadow-sm overflow-x-auto">
                    <p class="px-5 pt-5 font-bold text-[#7b2d5f]">Recent appointments</p>
                    <table class="min-w-full text-sm text-left mt-3">
                        <thead class="bg-[#f3e3ee] text-[#7b2d5f]">
                            <tr>
                                <th class="px-5 py-3">Service</th>
                                <th class="px-5 py-3">Date and Time</th>
                                <th class="px-5 py-3">Price</th>
                                <th class="px-5 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent as $a)
                                <tr class="border-t border-[#f0dce8] hover:bg-[#fdf6fa]">
                                    <td class="px-5 py-3 font-medium text-gray-900">{{ $a->service_name }}</td>
                                    <td class="px-5 py-3">{{ $a->appointment_at->format('M d, Y h:i A') }}</td>
                                    <td class="px-5 py-3 font-semibold text-[#7b2d5f]">₱{{ number_format($a->service_price, 2) }}</td>
                                    <td class="px-5 py-3"><x-status-badge :status="$a->status" /></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-6 text-center text-gray-500">No appointments yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            @endif
        </div>
    </div>
</x-app-layout>
