<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Subscribe to {{ $plan['name'] }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <p class="text-2xl font-bold">
                    ₱{{ number_format($plan['price'], 2) }}
                    <span class="text-sm font-normal text-gray-500">/ month</span>
                </p>
                <p class="mt-1 text-gray-600">{{ $plan['description'] ?? '' }}</p>

                <div class="mt-4 p-3 bg-yellow-50 text-yellow-800 text-sm rounded">
                    Demo payment: no real payment is processed and no card details are collected.
                </div>

                <form method="POST" action="{{ route('subscriptions.store', $plan['documentId']) }}" class="mt-6">
                    @csrf

                    <x-input-label for="salon_name" value="Salon name" />
                    <x-text-input id="salon_name" name="salon_name" type="text"
                                  class="block mt-1 w-full" :value="old('salon_name')" required autofocus />
                    <x-input-error :messages="$errors->get('salon_name')" class="mt-2" />

                    <div class="mt-6">
                        <x-primary-button>Confirm Subscription (Demo Payment)</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>