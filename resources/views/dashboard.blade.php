<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-[#7b2d5f] leading-tight">Dashboard</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Welcome banner --}}
            <div class="rounded-xl bg-[#7b2d5f] text-white p-6 shadow">
                <p class="text-sm opacity-80">Welcome back,</p>
                <p class="text-2xl font-bold">{{ auth()->user()->name }}</p>
                <p class="text-sm mt-1 opacity-80 capitalize">{{ auth()->user()->role }} account</p>
            </div>

            @if($isAdmin)

                {{-- Mga bilang --}}
                @php
                    $cards = [
                        ['Customers', $stats['customers']],
                        ['Total appointments', $stats['appointments']],
                        ['Pending appointments', $stats['pending']],
                        ['Completed revenue', '₱' . number_format($stats['revenue'], 2)],
                        ['Active subscriptions', $stats['subscriptions']],
                        ['Monthly recurring revenue', '₱' . number_format($stats['mrr'], 2)],
                    ];
                @endphp
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($cards as [$label, $value])
                        <div class="bg-white rounded-xl shadow-sm border-l-4 border-[#7b2d5f] p-5">
                            <p class="text-sm text-gray-500">{{ $label }}</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $value }}</p>
                        </div>
                    @endforeach
                </div>

                {{-- Mga paparating na appointment --}}
                <div class="bg-white rounded-xl shadow-sm overflow-x-auto">
                    <p class="px-5 pt-5 font-semibold text-gray-800">Upcoming appointments</p>
                    <table class="min-w-full text-sm text-left mt-3">
                        <thead class="bg-gray-50 text-gray-600">
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
                                <tr class="border-t">
                                    <td class="px-5 py-3">{{ $a->user->name }}</td>
                                    <td class="px-5 py-3">{{ $a->service_name }}</td>
                                    <td class="px-5 py-3">{{ $a->staff?->name ?? 'Any available' }}</td>
                                    <td class="px-5 py-3">{{ $a->appointment_at->format('M d, Y h:i A') }}</td>
                                    <td class="px-5 py-3 capitalize">{{ $a->status }}</td>
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

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Susunod na appointment --}}
                    <div class="bg-white rounded-xl shadow-sm border-l-4 border-[#7b2d5f] p-5">
                        <p class="text-sm text-gray-500">Next appointment</p>
                        @if($next)
                            <p class="text-xl font-bold text-gray-900">{{ $next->service_name }}</p>
                            <p class="text-gray-700">{{ $next->appointment_at->format('M d, Y h:i A') }}</p>
                            <p class="text-sm text-gray-500">
                                Stylist: {{ $next->staff?->name ?? 'Any available' }} &middot;
                                <span class="capitalize">{{ $next->status }}</span>
                            </p>
                        @else
                            <p class="text-gray-700 mt-1">You have no upcoming appointments.</p>
                        @endif
                    </div>

                    {{-- Subscription --}}
                    <div class="bg-white rounded-xl shadow-sm border-l-4 border-[#7b2d5f] p-5">
                        <p class="text-sm text-gray-500">Subscription</p>
                        @if($subscription)
                            <p class="text-xl font-bold text-gray-900">{{ $subscription->plan_name }}</p>
                            <p class="text-gray-700">{{ $subscription->salon_name }}</p>
                            <p class="text-sm text-gray-500">Valid until {{ $subscription->ends_at->format('M d, Y') }}</p>
                        @else
                            <p class="text-gray-700 mt-1">No active subscription.</p>
                            <a class="text-[#7b2d5f] underline text-sm" href="{{ route('home') }}#pricing">View plans</a>
                        @endif
                    </div>
                </div>

                {{-- Kamakailang appointments --}}
                <div class="bg-white rounded-xl shadow-sm overflow-x-auto">
                    <p class="px-5 pt-5 font-semibold text-gray-800">Recent appointments</p>
                    <table class="min-w-full text-sm text-left mt-3">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr>
                                <th class="px-5 py-3">Service</th>
                                <th class="px-5 py-3">Date and Time</th>
                                <th class="px-5 py-3">Price</th>
                                <th class="px-5 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent as $a)
                                <tr class="border-t">
                                    <td class="px-5 py-3">{{ $a->service_name }}</td>
                                    <td class="px-5 py-3">{{ $a->appointment_at->format('M d, Y h:i A') }}</td>
                                    <td class="px-5 py-3">₱{{ number_format($a->service_price, 2) }}</td>
                                    <td class="px-5 py-3 capitalize">{{ $a->status }}</td>
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