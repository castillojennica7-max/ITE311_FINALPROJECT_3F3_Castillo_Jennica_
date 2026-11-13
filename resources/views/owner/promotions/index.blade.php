<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Promos and Bundles</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4"><x-back-link /></div>

            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif

            <div class="mb-4 flex items-center justify-between">
                <p class="text-sm text-gray-500">Shown on your salon page and on the SalonSync home page while active.</p>
                <a href="{{ route('owner.promotions.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-[#7b2d5f] rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#5f2249] transition">
                    + Add Promo or Bundle
                </a>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-4 py-3">Title</th>
                            <th class="px-4 py-3">Type</th>
                            <th class="px-4 py-3">Offer</th>
                            <th class="px-4 py-3">Valid until</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($promotions as $promo)
                            @php $expired = $promo->valid_until && $promo->valid_until->lt(today()); @endphp
                            <tr class="border-t">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $promo->title }}</td>
                                <td class="px-4 py-3">{{ $promo->isBundle() ? 'Bundle' : 'Promo' }}</td>
                                <td class="px-4 py-3">{{ $promo->badge() }}</td>
                                <td class="px-4 py-3">{{ $promo->valid_until ? $promo->valid_until->format('M d, Y') : 'Ongoing' }}</td>
                                <td class="px-4 py-3">
                                    @if($expired)
                                        <span class="px-2 py-1 rounded-full bg-red-100 text-red-700 text-xs font-semibold">Expired</span>
                                    @elseif($promo->is_active)
                                        <span class="px-2 py-1 rounded-full bg-green-100 text-green-800 text-xs font-semibold">Active</span>
                                    @else
                                        <span class="px-2 py-1 rounded-full bg-gray-100 text-gray-600 text-xs font-semibold">Hidden</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-4">
                                        <a href="{{ route('owner.promotions.edit', $promo) }}" class="text-[#7b2d5f] underline">Edit</a>
                                        <form method="POST" action="{{ route('owner.promotions.destroy', $promo) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 underline"
                                                    onclick="return confirm('Remove this promotion?')">Remove</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                                    No promos or bundles yet. Add one to attract more customers.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
