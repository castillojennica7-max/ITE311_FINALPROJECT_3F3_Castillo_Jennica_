<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Salons</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4"><x-back-link /></div>

            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif

            <form method="GET" action="{{ route('admin.salons.index') }}" class="mb-4 flex gap-2 max-w-md">
                <input type="text" name="q" value="{{ $q }}" placeholder="Search salon or location"
                       class="flex-1 rounded-full border-gray-300 px-4 py-2 text-sm focus:border-[#7b2d5f] focus:ring-[#7b2d5f]">
                <button type="submit" class="px-5 py-2 rounded-full bg-[#7b2d5f] text-white text-sm font-semibold hover:bg-[#5f2249]">Search</button>
            </form>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-4 py-3">Salon</th>
                            <th class="px-4 py-3">Owner</th>
                            <th class="px-4 py-3">Plan</th>
                            <th class="px-4 py-3">Valid until</th>
                            <th class="px-4 py-3">Staff</th>
                            <th class="px-4 py-3">Appointments</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salons as $salon)
                            @php
                                $sub            = $salon->subscriptions->first();
                                $ownerSuspended = (bool) $salon->owner->suspended_at;
                            @endphp
                            <tr class="border-t">
                                <td class="px-4 py-3">
                                    <a href="{{ route('salons.show', $salon) }}" class="font-medium text-[#7b2d5f] hover:underline">{{ $salon->name }}</a>
                                    @if($salon->address)
                                        <div class="text-xs text-gray-500">{{ $salon->address }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    {{ $salon->owner->name }}
                                    <div class="text-xs text-gray-500">{{ $salon->owner->email }}</div>
                                </td>
                                <td class="px-4 py-3">{{ $sub?->plan_name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $sub ? $sub->ends_at->format('M d, Y') : '-' }}</td>
                                <td class="px-4 py-3">{{ $salon->staff_count }}</td>
                                <td class="px-4 py-3">{{ $salon->appointments_count }}</td>
                                <td class="px-4 py-3">
                                    @if($salon->suspended_at)
                                        <span class="px-2 py-1 rounded-full bg-red-100 text-red-700 text-xs font-semibold">Suspended</span>
                                    @elseif($ownerSuspended)
                                        <span class="px-2 py-1 rounded-full bg-red-100 text-red-700 text-xs font-semibold">Owner suspended</span>
                                    @elseif($sub)
                                        <span class="px-2 py-1 rounded-full bg-green-100 text-green-800 text-xs font-semibold">Active</span>
                                    @else
                                        <span class="px-2 py-1 rounded-full bg-gray-100 text-gray-600 text-xs font-semibold">No subscription</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($salon->suspended_at)
                                        <form method="POST" action="{{ route('admin.salons.restore', $salon) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-green-700 underline">Activate</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.salons.suspend', $salon) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-red-600 underline"
                                                    onclick="return confirm('Suspend this salon? It will be hidden from customers and cannot take bookings.')">Suspend</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-6 text-center text-gray-500">No salons found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $salons->links() }}</div>
        </div>
    </div>
</x-app-layout>
