<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Book an Appointment</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4"><x-back-link /></div>

            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6">
                <p class="text-gray-600">Step 1 of 2: choose a salon. Every salon has its own services, prices, and hours.</p>

                <form method="GET" action="{{ route('appointments.create') }}" class="flex gap-2">
                    <input type="text" name="q" value="{{ $q }}" placeholder="Search salon or location"
                           class="rounded-full border-gray-300 px-4 py-2 text-sm focus:border-[#7b2d5f] focus:ring-[#7b2d5f]">
                    <button type="submit" class="px-5 py-2 rounded-full bg-[#7b2d5f] text-white text-sm font-semibold hover:bg-[#5f2249]">Search</button>
                </form>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($salons as $salon)
                    <x-salon-card :salon="$salon" :book="true" />
                @empty
                    <p class="sm:col-span-3 text-gray-500 py-10 text-center">No salons found.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
