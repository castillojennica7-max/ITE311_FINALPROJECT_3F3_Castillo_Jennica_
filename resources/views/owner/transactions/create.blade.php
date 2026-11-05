<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Record Payment</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4"><x-back-link :href="route('owner.appointments.index')" label="Back to Appointments" /></div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="mb-4 p-3 bg-gray-50 rounded text-sm text-gray-700">
                    <p class="font-semibold">{{ $appointment->service_name }}</p>
                    <p>{{ $appointment->user->name }} &middot; {{ $appointment->appointment_at->format('M d, Y h:i A') }}</p>
                    <p>Stylist: {{ $appointment->staff?->name ?? 'Any available' }}</p>
                </div>

                <form method="POST" action="{{ route('owner.transactions.store', $appointment) }}">
                    @csrf

                    <div>
                        <x-input-label for="amount" value="Amount received (₱)" />
                        <x-text-input id="amount" name="amount" type="number" step="0.01" class="block mt-1 w-full"
                                      :value="old('amount', $appointment->service_price)" required />
                        <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="method" value="Payment method" />
                        <select id="method" name="method" required class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                            @foreach($methods as $m)
                                <option value="{{ $m }}" @selected(old('method', 'cash') === $m)>
                                    {{ $m === 'gcash' ? 'GCash' : ucfirst($m) }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('method')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="notes" value="Notes (optional, e.g. GCash reference number)" />
                        <x-text-input id="notes" name="notes" type="text" class="block mt-1 w-full" :value="old('notes')" />
                        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                    </div>

                    <p class="mt-4 text-sm text-gray-500">The appointment will be marked as completed.</p>

                    <div class="mt-6">
                        <x-primary-button>Record Payment</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>