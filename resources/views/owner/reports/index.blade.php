<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Reports</h2>
    </x-slot>

    @php
        $statuses = ['pending', 'confirmed', 'completed', 'cancelled'];
        $cards = [
            ['Revenue', '₱' . number_format($revenue, 2)],
            ['Payments received', $count],
            ['Average payment', '₱' . number_format($average, 2)],
            ['Appointments', array_sum($apptByStatus->all())],
            ['Customers served', $newCustomers],
            ['Completed', $apptByStatus->get('completed', 0)],
        ];
        $exports = [
            'transactions' => 'Transactions',
            'appointments' => 'Appointments',
            'customers'    => 'Customers',
        ];
    @endphp

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div><x-back-link /></div>

            <div class="bg-white rounded-xl shadow-sm p-5">
                <x-report-filter :range="$range" :action="route('owner.reports.index')" />
                <p class="mt-3 text-sm text-gray-500">Showing {{ $range->label() }} ({{ $range->days() }} days)</p>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($cards as [$label, $value])
                    <div class="bg-white rounded-xl shadow-sm border-l-4 border-[#7b2d5f] p-5">
                        <p class="text-sm text-gray-500">{{ $label }}</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $value }}</p>
                    </div>
                @endforeach
            </div>

            <div class="bg-white rounded-xl shadow-sm p-5">
                <p class="font-semibold text-gray-800 mb-4">Revenue per day</p>
                <x-bar-chart :series="$series" :money="true" />
            </div>

            <div class="grid lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-xl shadow-sm overflow-x-auto">
                    <p class="px-5 pt-5 font-semibold text-gray-800">Revenue by service</p>
                    <table class="min-w-full text-sm text-left mt-3">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr><th class="px-5 py-3">Service</th><th class="px-5 py-3">Payments</th><th class="px-5 py-3">Revenue</th></tr>
                        </thead>
                        <tbody>
                            @forelse($byService as $row)
                                <tr class="border-t">
                                    <td class="px-5 py-3">{{ $row->name }}</td>
                                    <td class="px-5 py-3">{{ $row->total }}</td>
                                    <td class="px-5 py-3">₱{{ number_format((float) $row->revenue, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-5 py-6 text-center text-gray-500">No payments in this period.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="bg-white rounded-xl shadow-sm overflow-x-auto">
                    <p class="px-5 pt-5 font-semibold text-gray-800">Revenue by stylist</p>
                    <table class="min-w-full text-sm text-left mt-3">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr><th class="px-5 py-3">Stylist</th><th class="px-5 py-3">Payments</th><th class="px-5 py-3">Revenue</th></tr>
                        </thead>
                        <tbody>
                            @forelse($byStylist as $row)
                                <tr class="border-t">
                                    <td class="px-5 py-3">{{ $row->name }}</td>
                                    <td class="px-5 py-3">{{ $row->total }}</td>
                                    <td class="px-5 py-3">₱{{ number_format((float) $row->revenue, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-5 py-6 text-center text-gray-500">No payments in this period.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="grid lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-xl shadow-sm overflow-x-auto">
                    <p class="px-5 pt-5 font-semibold text-gray-800">Payments by method</p>
                    <table class="min-w-full text-sm text-left mt-3">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr><th class="px-5 py-3">Method</th><th class="px-5 py-3">Payments</th><th class="px-5 py-3">Revenue</th></tr>
                        </thead>
                        <tbody>
                            @forelse($byMethod as $row)
                                <tr class="border-t">
                                    <td class="px-5 py-3 capitalize">{{ $row->method === 'gcash' ? 'GCash' : $row->method }}</td>
                                    <td class="px-5 py-3">{{ $row->total }}</td>
                                    <td class="px-5 py-3">₱{{ number_format((float) $row->revenue, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-5 py-6 text-center text-gray-500">No payments in this period.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="bg-white rounded-xl shadow-sm overflow-x-auto">
                    <p class="px-5 pt-5 font-semibold text-gray-800">Appointments by status</p>
                    <table class="min-w-full text-sm text-left mt-3">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr><th class="px-5 py-3">Status</th><th class="px-5 py-3">Count</th></tr>
                        </thead>
                        <tbody>
                            @foreach($statuses as $status)
                                <tr class="border-t">
                                    <td class="px-5 py-3 capitalize">{{ $status }}</td>
                                    <td class="px-5 py-3">{{ $apptByStatus->get($status, 0) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-5">
                <p class="font-semibold text-gray-800">Download as CSV</p>
                <p class="text-sm text-gray-500 mb-4">Exports use the date range selected above and open in Excel.</p>
                <div class="flex flex-wrap gap-3">
                    @foreach($exports as $type => $label)
                        <a href="{{ route('owner.reports.export', array_merge(['type' => $type], $range->params())) }}"
                           class="px-4 py-2 rounded-full border-2 border-[#7b2d5f] text-[#7b2d5f] text-sm font-semibold hover:bg-[#7b2d5f] hover:text-white transition">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
