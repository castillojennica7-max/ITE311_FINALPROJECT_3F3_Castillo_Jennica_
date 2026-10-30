<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Subscription</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6">
                @if($active)
                    <p class="text-sm text-gray-500">Current plan</p>
                    <p class="text-2xl font-bold">{{ $active->plan_name }}</p>
                    <p>{{ $active->salon_name }}</p>
                    <p class="mt-2 text-gray-600">
                        ₱{{ number_format($active->price, 2) }} per month &middot;
                        Valid until {{ $active->ends_at->format('M d, Y') }}
                    </p>
                    <form method="POST" action="{{ route('subscriptions.cancel', $active) }}" class="mt-4">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="text-red-600 underline"
                                onclick="return confirm('Cancel your subscription?')">Cancel subscription</button>
                    </form>
                @else
                    <p>You do not have an active subscription.</p>
                    <a class="text-indigo-600 underline" href="{{ route('home') }}#pricing">View plans</a>
                @endif
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-4 py-3">Reference</th>
                            <th class="px-4 py-3">Plan</th>
                            <th class="px-4 py-3">Salon</th>
                            <th class="px-4 py-3">Amount</th>
                            <th class="px-4 py-3">Period</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subscriptions as $s)
                            <tr class="border-t">
                                <td class="px-4 py-3">{{ $s->reference_no }}</td>
                                <td class="px-4 py-3">{{ $s->plan_name }}</td>
                                <td class="px-4 py-3">{{ $s->salon_name }}</td>
                                <td class="px-4 py-3">₱{{ number_format($s->price, 2) }}</td>
                                <td class="px-4 py-3">{{ $s->starts_at->format('M d, Y') }} - {{ $s->ends_at->format('M d, Y') }}</td>
                                <td class="px-4 py-3 capitalize">{{ $s->isActive() ? 'active' : ($s->status === 'active' ? 'expired' : $s->status) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-gray-500">No subscriptions yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>