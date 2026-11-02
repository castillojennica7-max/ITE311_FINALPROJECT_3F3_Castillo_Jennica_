<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Salons</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4"><x-back-link /></div>

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
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salons as $salon)
                            @php $sub = $salon->subscriptions->first(); @endphp
                            <tr class="border-t">
                                <td class="px-4 py-3">
                                    {{ $salon->name }}
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
                                    @if($sub)
                                        <span class="text-green-700">Active</span>
                                    @else
                                        <span class="text-gray-500">No active subscription</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-6 text-center text-gray-500">No salons yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $salons->links() }}</div>
        </div>
    </div>
</x-app-layout>