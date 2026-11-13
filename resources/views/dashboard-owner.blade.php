<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-[#7b2d5f] leading-tight">Dashboard</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('error'))
                <div class="p-3 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
            @endif

            <div class="rounded-xl bg-[#7b2d5f] text-white p-6 shadow">
                <p class="text-sm opacity-80">Welcome back,</p>
                <p class="text-2xl font-bold">{{ auth()->user()->name }}</p>
                <p class="text-sm mt-1 opacity-80">
                    Salon owner{{ $salon ? ' · ' . $salon->name : '' }}
                </p>
            </div>

            @if(! $subscription)
                <div class="bg-white rounded-xl shadow-sm border-l-4 border-yellow-400 p-5">
                    <p class="font-semibold text-gray-900">Subscribe to start using SalonSync</p>
                    <p class="text-gray-600 mt-1">
                        Choose a plan to unlock staff, services, appointment, and customer management for your salon.
                    </p>
                    <a href="{{ route('home') }}#plans"
                       class="inline-block mt-3 px-4 py-2 bg-[#7b2d5f] text-white rounded-md text-sm font-semibold hover:bg-[#5f2249]">
                        View plans
                    </a>
                </div>
            @else
                @php
                    $cards = [
                        ["Today's appointments", $stats['today']],
                        ['Pending appointments', $stats['pending']],
                        ['Staff members', $stats['staff']],
                        ['Customers', $stats['customers']],
                        ['Revenue this month', '₱' . number_format($stats['revenue'], 2)],
                        ['Current plan', $subscription->plan_name],
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
                @endphp

                <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($cards as [$label, $value])
                        <div class="bg-white rounded-xl shadow-sm border-l-4 border-[#7b2d5f] p-5">
                            <p class="text-sm text-gray-500">{{ $label }}</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $value }}</p>
                        </div>
                    @endforeach
                </div>

                @if($done < count($checklist))
                    <div class="bg-white rounded-xl shadow-sm p-5">
                        <div class="flex items-center justify-between">
                            <p class="font-semibold text-gray-900">Set up your salon page</p>
                            <p class="text-sm text-gray-500">{{ $done }} of {{ count($checklist) }} done</p>
                        </div>
                        <div class="mt-2 h-2 rounded-full bg-[#f3e3ee] overflow-hidden">
                            <div class="h-2 bg-[#7b2d5f] rounded-full" style="width: {{ round($done / count($checklist) * 100) }}%"></div>
                        </div>
                        <ul class="mt-4 space-y-2 text-sm">
                            @foreach($checklist as [$text, $ok, $url])
                                <li class="flex items-center gap-3">
                                    <span class="w-5 h-5 rounded-full flex items-center justify-center text-xs font-bold {{ $ok ? 'bg-green-500 text-white' : 'border-2 border-gray-300 text-transparent' }}">✓</span>
                                    @if($ok)
                                        <span class="text-gray-400 line-through">{{ $text }}</span>
                                    @else
                                        <a href="{{ $url }}" class="text-[#7b2d5f] hover:underline">{{ $text }}</a>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                        <a href="{{ route('salons.show', $salon) }}" target="_blank" class="inline-block mt-4 text-sm font-semibold text-[#7b2d5f] hover:underline">View my public salon page &rarr;</a>
                    </div>
                @else
                    <a href="{{ route('salons.show', $salon) }}" target="_blank" class="inline-block text-sm font-semibold text-[#7b2d5f] hover:underline">View my public salon page &rarr;</a>
                @endif

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
            @endif
        </div>
    </div>
</x-app-layout>
