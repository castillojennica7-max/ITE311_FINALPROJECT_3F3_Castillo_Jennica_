<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">Book an Appointment</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-4"><x-back-link /></div>

            {{-- Hakbang at paghahanap --}}
            <div class="bg-white rounded-2xl border border-[#f0dce8] border-t-4 border-t-[#7b2d5f] shadow-md p-5 sm:p-6 mb-8">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                    <div>
                        <div class="flex items-center gap-2 text-sm font-semibold">
                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#7b2d5f] text-white">
                                <span class="w-5 h-5 rounded-full bg-white text-[#7b2d5f] text-xs font-extrabold flex items-center justify-center">1</span>
                                Choose a salon
                            </span>
                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#f3e3ee] text-[#7b2d5f]">
                                <span class="w-5 h-5 rounded-full bg-white text-[#7b2d5f] text-xs font-extrabold flex items-center justify-center">2</span>
                                Pick service and time
                            </span>
                        </div>
                        <p class="mt-3 text-gray-600">Every salon has its own services, prices, and hours.</p>
                    </div>

                    <form method="GET" action="{{ route('appointments.create') }}" class="flex gap-2 w-full lg:w-auto">
                        <input type="text" name="q" value="{{ $q }}" placeholder="Search salon or location"
                               class="flex-1 lg:w-72 rounded-full border-[#e5cddc] bg-[#fdf6fa] px-4 py-2.5 text-sm focus:border-[#7b2d5f] focus:ring-[#7b2d5f]">
                        <button type="submit"
                                class="px-6 py-2.5 rounded-full bg-[#7b2d5f] text-white text-sm font-semibold shadow hover:bg-[#5f2249] transition">Search</button>
                    </form>
                </div>
            </div>

            {{-- Mga salon --}}
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($salons as $salon)
                    <x-salon-card :salon="$salon" :book="true" />
                @empty
                    <div class="sm:col-span-2 lg:col-span-3 bg-white rounded-2xl border border-[#f0dce8] shadow-sm py-14 text-center">
                        <span class="mx-auto w-12 h-12 rounded-xl bg-[#f3e3ee] text-[#7b2d5f] flex items-center justify-center">
                            <x-icon name="scissors" size="w-6 h-6" />
                        </span>
                        <p class="mt-3 font-bold text-gray-800">No salons found.</p>
                        @if($q)
                            <a href="{{ route('appointments.create') }}" class="inline-block mt-2 font-semibold text-[#7b2d5f] hover:underline">Clear search</a>
                        @endif
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>