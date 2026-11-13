<x-public-layout title="Salons">
    <section class="bg-gradient-to-br from-[#5f2249] via-[#7b2d5f] to-[#c45a9a] text-white">
        <div class="max-w-6xl mx-auto px-4 py-14">
            <h1 class="text-3xl sm:text-4xl font-extrabold">Find your salon</h1>
            <p class="mt-2 text-white/85">Compare services, prices, and opening hours from our partner salons.</p>

            <form method="GET" action="{{ route('salons.index') }}" class="mt-6 flex flex-col sm:flex-row gap-3 max-w-xl">
                <input type="text" name="q" value="{{ $q }}" placeholder="Search by salon name or location"
                       class="flex-1 rounded-full border-0 px-5 py-3 text-gray-800 focus:ring-2 focus:ring-[#ffd166]">
                <button type="submit" class="px-6 py-3 rounded-full bg-white text-[#7b2d5f] font-semibold hover:bg-[#f3e3ee] transition">Search</button>
            </form>
        </div>
    </section>

    <section class="max-w-6xl mx-auto px-4 pt-10">
        @if($q !== '')
            <p class="mb-4 text-sm text-gray-500">
                {{ $salons->total() }} result(s) for "<span class="font-semibold">{{ $q }}</span>"
                &middot; <a href="{{ route('salons.index') }}" class="text-[#7b2d5f] underline">Clear search</a>
            </p>
        @endif

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($salons as $salon)
                <x-salon-card :salon="$salon" />
            @empty
                <p class="sm:col-span-3 text-gray-500 py-10 text-center">No salons found.</p>
            @endforelse
        </div>

        <div class="mt-8">{{ $salons->links() }}</div>
    </section>
</x-public-layout>
