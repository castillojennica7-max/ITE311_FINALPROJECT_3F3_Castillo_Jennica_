<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">My Appointments</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-4"><x-back-link /></div>

            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-xl border border-green-200">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-3 bg-red-100 text-red-800 rounded-xl border border-red-200">{{ session('error') }}</div>
            @endif

            @php $paypalEnabled = app(\App\Services\PayPalService::class)->enabled(); @endphp

            <div class="mb-4">
                <a href="{{ route('appointments.create') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#7b2d5f] rounded-full font-semibold text-sm text-white shadow hover:bg-[#5f2249] transition">
                    <x-icon name="calendar" size="w-4 h-4" /> Book an Appointment
                </a>
            </div>

            <div class="bg-white rounded-2xl border border-[#f0dce8] border-t-4 border-t-[#7b2d5f] shadow-md overflow-x-auto">
                <table class="min-w-full text-sm text-left">
                    <thead class="bg-[#f3e3ee] text-[#7b2d5f]">
                        <tr>
                            <th class="px-4 py-3">Salon</th>
                            <th class="px-4 py-3">Service</th>
                            <th class="px-4 py-3">Stylist</th>
                            <th class="px-4 py-3">Date and Time</th>
                            <th class="px-4 py-3">Price</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Payment</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($appointments as $appointment)
                            <tr class="border-t border-[#f0dce8] hover:bg-[#fdf6fa]">
                                <td class="px-4 py-3 font-semibold text-gray-900">{{ $appointment->salon->name }}</td>
                                <td class="px-4 py-3">{{ $appointment->service_name }}</td>
                                <td class="px-4 py-3">{{ $appointment->staff?->name ?? 'Any available' }}</td>
                                <td class="px-4 py-3">{{ $appointment->appointment_at->format('M d, Y h:i A') }}</td>
                                <td class="px-4 py-3 font-semibold text-[#7b2d5f]">₱{{ number_format($appointment->service_price, 2) }}</td>
                                <td class="px-4 py-3"><x-status-badge :status="$appointment->status" /></td>
                                <td class="px-4 py-3">
                                    @if($appointment->transaction)
                                        <a class="inline-flex items-center gap-1.5 text-green-700 font-semibold hover:underline" href="{{ route('transactions.receipt', $appointment->transaction) }}">
                                            <x-icon name="receipt" size="w-4 h-4" /> Paid &middot; Receipt
                                        </a>
                                    @elseif($appointment->status === 'cancelled')
                                        <span class="text-gray-400">-</span>
                                    @elseif($paypalEnabled)
                                        <form method="POST" action="{{ route('payments.checkout', $appointment) }}">
                                            @csrf
                                            <button type="submit"
                                                    class="px-3 py-1.5 bg-[#7b2d5f] text-white rounded-full text-xs font-semibold hover:bg-[#5f2249] transition">
                                                Pay with PayPal
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-gray-500">Pay at the salon</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if(in_array($appointment->status, ['pending', 'confirmed']) && ! $appointment->transaction)
                                        <form method="POST" action="{{ route('appointments.cancel', $appointment) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="px-3 py-1.5 rounded-full border border-red-300 text-red-600 text-xs font-semibold hover:bg-red-50 transition"
                                                    onclick="return confirm('Cancel this appointment?')">Cancel</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-10 text-center text-gray-500">
                                    You have no appointments yet.
                                    <a href="{{ route('appointments.create') }}" class="block mt-2 font-semibold text-[#7b2d5f] hover:underline">Book your first appointment</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
