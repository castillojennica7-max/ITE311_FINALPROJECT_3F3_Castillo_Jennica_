<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Services</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4"><x-back-link /></div>

            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif

            <div class="mb-4">
                <a href="{{ route('services.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-[#7b2d5f] rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#5f2249] transition">
                    + Add Service
                </a>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-4 py-3">Service</th>
                            <th class="px-4 py-3">Price</th>
                            <th class="px-4 py-3">Duration (minutes)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($services as $service)
                            <tr class="border-t">
                                <td class="px-4 py-3">{{ $service->name }}</td>
                                <td class="px-4 py-3">₱{{ number_format($service->price, 2) }}</td>
                                <td class="px-4 py-3">{{ $service->duration_minutes }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-6 text-center text-gray-500">No services yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>