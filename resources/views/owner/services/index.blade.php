<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Services and Prices</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4"><x-back-link /></div>

            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif

            <div class="mb-4 flex items-center justify-between">
                <p class="text-sm text-gray-500">Your own services and prices. Customers see these on your salon page.</p>
                <a href="{{ route('owner.services.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-[#7b2d5f] rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#5f2249] transition">
                    + Add Service
                </a>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-4 py-3">Service</th>
                            <th class="px-4 py-3">Category</th>
                            <th class="px-4 py-3">Price</th>
                            <th class="px-4 py-3">Duration</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($services as $service)
                            <tr class="border-t">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $service->imageUrl() }}" alt="" class="w-12 h-12 rounded-lg object-cover bg-[#f3e3ee]">
                                        <span class="font-medium text-gray-900">{{ $service->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">{{ $service->categoryLabel() }}</td>
                                <td class="px-4 py-3">₱{{ number_format($service->price, 2) }}</td>
                                <td class="px-4 py-3">{{ $service->duration_minutes }} min</td>
                                <td class="px-4 py-3">
                                    @if($service->is_active)
                                        <span class="px-2 py-1 rounded-full bg-green-100 text-green-800 text-xs font-semibold">Visible</span>
                                    @else
                                        <span class="px-2 py-1 rounded-full bg-gray-100 text-gray-600 text-xs font-semibold">Hidden</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-4">
                                        <a href="{{ route('owner.services.edit', $service) }}" class="text-[#7b2d5f] underline">Edit</a>
                                        <form method="POST" action="{{ route('owner.services.destroy', $service) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 underline"
                                                    onclick="return confirm('Remove this service?')">Remove</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                                    No services yet. Add your first service so customers can book.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
