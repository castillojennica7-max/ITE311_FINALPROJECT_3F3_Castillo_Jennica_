<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manage Subscriptions</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm text-gray-500">Active subscriptions</p>
                    <p class="text-3xl font-bold">{{ $stats['active'] }}</p>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm text-gray-500">Monthly recurring revenue</p>
                    <p class="text-3xl font-bold">₱{{ number_format($stats['mrr'], 2) }}</p>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm text-gray-500">Total collected</p>
                    <p class="text-3xl font-bold">₱{{ number_format($stats['collected'], 2) }}</p>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6">
                <p class="font-semibold mb-2">Active subscriptions by plan</p>
                @forelse($byPlan as $row)
                    <p>{{ $row->plan_name }}: {{ $row->total }} salon(s) &middot; ₱{{ number_format($row->revenue, 2) }} / month</p>
                @empty
                    <p class="text-gray-500">No active subscriptions yet.</p>
                @endforelse
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-4 py-3">Reference</th>
                            <th class="px-4 py-3">Salon</th>
                            <th class="px-4 py-3">Owner</th>
                            <th class="px-4 py-3">Plan</th>
                            <th class="px-4 py-3">Amount</th>
                            <th class="px-4 py-3">Valid until</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subscriptions as $s)
                            <tr class="border-t">
                                <td class="px-4 py-3">{{ $s->reference_no }}</td>
                                <td class="px-4 py-3">{{ $s->salon_name }}</td>
                                <td class="px-4 py-3">
                                    {{ $s->user->name }}
                                    <div class="text-xs text-gray-500">{{ $s->user->email }}</div>
                                </td>
                                <td class="px-4 py-3">{{ $s->plan_name }}</td>
                                <td class="px-4 py-3">₱{{ number_format($s->price, 2) }}</td>
                                <td class="px-4 py-3">{{ $s->ends_at->format('M d, Y') }}</td>
                                <td class="px-4 py-3 capitalize">{{ $s->isActive() ? 'active' : ($s->status === 'active' ? 'expired' : $s->status) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-6 text-center text-gray-500">No subscriptions yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $subscriptions->links() }}
            </div>
        </div>
    </div>
</x-app-layout>