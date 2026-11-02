<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Book an Appointment</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4"><x-back-link /></div>

            <p class="mb-4 text-gray-600">Step 1 of 2: choose a salon.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($salons as $salon)
                    <div class="bg-white rounded-xl shadow-sm border-l-4 border-[#7b2d5f] p-5">
                        <p class="text-xl font-bold text-gray-900">{{ $salon->name }}</p>
                        @if($salon->address)
                            <p class="text-gray-600">{{ $salon->address }}</p>
                        @endif
                        @if($salon->phone)
                            <p class="text-sm text-gray-500">{{ $salon->phone }}</p>
                        @endif
                        <a href="{{ route('appointments.book', $salon) }}"
                           class="inline-flex items-center mt-3 px-4 py-2 bg-[#7b2d5f] rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#5f2249] transition">
                            Book here
                        </a>
                    </div>
                @empty
                    <p class="text-gray-500">No salons are accepting bookings yet. Please check back soon.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>