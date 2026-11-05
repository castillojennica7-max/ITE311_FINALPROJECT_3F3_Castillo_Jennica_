<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Transactions</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4"><x-back-link /></div>

            @php
                $cards = [
                    ['Today', '₱' . number_format($totals['today'], 2)],
                    ['This month', '₱' . number_format($totals['month'], 2)],
                    ['All time', '₱' . number_format($totals['all'], 2)],
                    ['Transactions', $totals['count']],
                ];
            @endphp
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                @foreach($cards as [$label, $value])
                    <div class="bg-white rounded-xl shadow-sm border-l-4 border-[#7b2d5f] p-5">
                        <p class="text-sm text-gray-500">{{ $label }}</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $value }}</p>
                    </div>
                @endforeach
            </div>

            <div class="mb-4 flex flex-wrap gap-2 text-sm">
                <a href="{{ route('owner.transactions.index') }}"
                   class="px-3 py-1 rounded border {{ $method ? 'bg-white text-gray-700' : 'bg-[#7b2d5f] text-white' }}">All</a>
                @foreach($methods as $m)
                    <a href="{{ route('owner.transactions.index', ['method' => $m]) }}"
                       class="px-3 py-1 rounded border capitalize {{ $method === $m ? 'bg-[#7b2d5f] text-white' : 'bg-white text-gray-700' }}">
                        {{ $m === 'paypal' ? 'PayPal' : $m }}
                    </a>
                @endforeach
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Reference</th>
                            <th class="px-4 py-3">Customer</th>
                            <th class="px-4 py-3">Service</th>
                            <th class="px-4 py-3">Method</th>
                            <th class="px-4 py-3">Amount</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $t)
                            <tr class="border-t">
                                <td class="px-4 py-3">{{ $t->paid_at->format('M d, Y h:i A') }}</td>
                                <td class="px-4 py-3">{{ $t->reference_no }}</td>
                                <td class="px-4 py-3">{{ $t->customer?->name ?? 'Walk-in' }}</td>
                                <td class="px-4 py-3">{{ $t->appointment?->service_name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $t->methodLabel() }}</td>
                                <td class="px-4 py-3">₱{{ number_format($t->amount, 2) }}</td>
                                <td class="px-4 py-3">
                                    <a class="text-[#7b2d5f] underline" href="{{ route('transactions.receipt', $t) }}">Receipt</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                                    No transactions yet. Record a payment from the Appointments page.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $transactions->links() }}</div>
        </div>
    </div>
</x-app-layout>