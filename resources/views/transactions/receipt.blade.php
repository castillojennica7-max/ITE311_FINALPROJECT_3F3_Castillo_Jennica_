<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Receipt</h2>
    </x-slot>

    <style>
        @media print {
            nav, header { display: none !important; }
            body { background: #fff !important; }
        }
    </style>

    @php
        $user = auth()->user();
        $back = $user->isSalonOwner()
            ? route('owner.transactions.index')
            : ($user->isCustomer() ? route('appointments.index') : route('dashboard'));
        $backLabel = $user->isSalonOwner() ? 'Back to Transactions' : ($user->isCustomer() ? 'Back to My Appointments' : 'Back to Dashboard');
    @endphp

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4 print:hidden"><x-back-link :href="$back" :label="$backLabel" /></div>

            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded print:hidden">{{ session('success') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-8">
                <div class="text-center">
                    <p class="text-2xl font-bold text-[#7b2d5f]">{{ $transaction->salon->name }}</p>
                    @if($transaction->salon->address)
                        <p class="text-sm text-gray-500">{{ $transaction->salon->address }}</p>
                    @endif
                    @if($transaction->salon->phone)
                        <p class="text-sm text-gray-500">{{ $transaction->salon->phone }}</p>
                    @endif
                    <p class="mt-4 text-sm tracking-widest text-gray-400 uppercase">Official Receipt</p>
                    <p class="inline-block mt-1 px-3 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">PAID</p>
                </div>

                <dl class="mt-6 text-sm divide-y">
                    <div class="flex justify-between py-2"><dt class="text-gray-500">Reference no</dt><dd class="font-medium">{{ $transaction->reference_no }}</dd></div>
                    <div class="flex justify-between py-2"><dt class="text-gray-500">Date paid</dt><dd>{{ $transaction->paid_at->format('M d, Y h:i A') }}</dd></div>
                    <div class="flex justify-between py-2"><dt class="text-gray-500">Customer</dt><dd>{{ $transaction->customer?->name ?? 'Walk-in customer' }}</dd></div>
                    <div class="flex justify-between py-2"><dt class="text-gray-500">Service</dt><dd>{{ $transaction->appointment?->service_name ?? '-' }}</dd></div>
                    <div class="flex justify-between py-2"><dt class="text-gray-500">Stylist</dt><dd>{{ $transaction->appointment?->staff?->name ?? 'Any available' }}</dd></div>
                    @if($transaction->appointment)
                        <div class="flex justify-between py-2"><dt class="text-gray-500">Appointment</dt><dd>{{ $transaction->appointment->appointment_at->format('M d, Y h:i A') }}</dd></div>
                    @endif
                    <div class="flex justify-between py-2"><dt class="text-gray-500">Payment method</dt><dd>{{ $transaction->methodLabel() }}</dd></div>
                    @if($transaction->notes)
                        <div class="flex justify-between py-2"><dt class="text-gray-500">Notes</dt><dd>{{ $transaction->notes }}</dd></div>
                    @endif
                    <div class="flex justify-between py-3 text-lg font-bold">
                        <dt>Total paid</dt><dd>₱{{ number_format($transaction->amount, 2) }}</dd>
                    </div>
                </dl>

                <p class="mt-4 text-center text-xs text-gray-400">Thank you for choosing {{ $transaction->salon->name }}. Powered by SalonSync.</p>
            </div>

            <div class="mt-4 text-center print:hidden">
                <button type="button" onclick="window.print()"
                        class="inline-flex items-center px-4 py-2 bg-[#7b2d5f] rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#5f2249] transition">
                    Print receipt
                </button>
            </div>
        </div>
    </div>
</x-app-layout>