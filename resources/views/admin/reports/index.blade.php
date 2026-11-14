<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Reports</h2>
    </x-slot>

    @php
        $statuses = ['pending', 'confirmed', 'completed', 'cancelled'];
        $cards = [
            ['New users', $usersByRole->sum()],
            ['Subscriptions sold', $subCount],
            ['Subscription revenue', '₱' . number_format($subRevenue, 2)],
            ['Appointments', $apptTotal],
            ['Customer payments', '₱' . number_format($paymentTotal, 2)],
            ['Payment records', $paymentCount],
        ];
        $exports = [
            'appointments'  => 'Appointments',
            'subscriptions' => 'Subscriptions',
            'transactions'  => 'Transactions',
            'users'         => 'Users',
            'salons'        => 'Salons',
        ];
    @endphp

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div><x-back-link /></div>

            <div class="bg-white rounded-xl shadow-sm p-5">
                <x-report-filter :range="$range" :action="route('admin.reports.index')" />
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
                <p class="font-semibold text-gray-800 mb-4">Appointments per day</p>
                <x-bar-chart :series="$series" />
            </div>

            <div class="grid lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-xl shadow-sm overflow-x-auto">
                    <p class="px-5 pt-5 font-semibold text-gray-800">Subscriptions by plan</p>
                    <table class="min-w-full text-sm text-left mt-3">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr><th class="px-5 py-3">Plan</th><th class="px-5 py-3">Sold</th><th class="px-5 py-3">Revenue</th></tr>
                        </thead>
                        <tbody>
                            @forelse($subsByPlan as $row)
                                <tr class="border-t">
                                    <td class="px-5 py-3">{{ $row->plan_name }}</td>
                                    <td class="px-5 py-3">{{ $row->total }}</td>
                                    <td class="px-5 py-3">₱{{ number_format($row->revenue, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-5 py-6 text-center text-gray-500">No subscriptions in this period.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="bg-white rounded-xl shadow-sm overflow-x-auto">
                    <p class="px-5 pt-5 font-semibold text-gray-800">Appointments by status</p>
                    <table class="min-w-full text-sm text-left mt-3">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr><th class="px-5 py-3">Status</th><th class="px-5 py-3">Count</th><th class="px-5 py-3">Value</th></tr>
                        </thead>
                        <tbody>
                            @foreach($statuses as $status)
                                @php $row = $apptByStatus->get($status); @endphp
                                <tr class="border-t">
                                    <td class="px-5 py-3 capitalize">{{ $status }}</td>
                                    <td class="px-5 py-3">{{ $row?->total ?? 0 }}</td>
                                    <td class="px-5 py-3">₱{{ number_format((float) ($row?->value ?? 0), 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm overflow-x-auto">
                <p class="px-5 pt-5 font-semibold text-gray-800">Top salons</p>
                <table class="min-w-full text-sm text-left mt-3">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-5 py-3">Salon</th>
                            <th class="px-5 py-3">Appointments</th>
                            <th class="px-5 py-3">Payments received</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topSalons as $salon)
                            <tr class="border-t">
                                <td class="px-5 py-3">{{ $salon->name }}</td>
                                <td class="px-5 py-3">{{ $salon->appointments_in_range }}</td>
                                <td class="px-5 py-3">₱{{ number_format((float) $salon->revenue_in_range, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-5 py-6 text-center text-gray-500">No salons yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-5">
                <p class="font-semibold text-gray-800">Download as CSV</p>
                <p class="text-sm text-gray-500 mb-4">Exports use the date range selected above and open in Excel.</p>
                <div class="flex flex-wrap gap-3">
                    @foreach($exports as $type => $label)
                        <a href="{{ route('admin.reports.export', array_merge(['type' => $type], $range->params())) }}"
                           class="px-4 py-2 rounded-full border-2 border-[#7b2d5f] text-[#7b2d5f] text-sm font-semibold hover:bg-[#7b2d5f] hover:text-white transition">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
