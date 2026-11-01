<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Appointments</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4"><x-back-link /></div>

            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif

            <div class="mb-4 flex flex-wrap gap-2 text-sm">
                <a href="{{ route('owner.appointments.index') }}"
                   class="px-3 py-1 rounded border {{ $status ? 'bg-white text-gray-700' : 'bg-[#7b2d5f] text-white' }}">
                    All
                </a>
                @foreach($statuses as $s)
                    <a href="{{ route('owner.appointments.index', ['status' => $s]) }}"
                       class="px-3 py-1 rounded border capitalize {{ $status === $s ? 'bg-[#7b2d5f] text-white' : 'bg-white text-gray-700' }}">
                        {{ $s }}
                    </a>
                @endforeach
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-4 py-3">Customer</th>
                            <th class="px-4 py-3">Service</th>
                            <th class="px-4 py-3">Stylist</th>
                            <th class="px-4 py-3">Date and Time</th>
                            <th class="px-4 py-3">Price</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($appointments as $appointment)
                            <tr class="border-t">
                                <td class="px-4 py-3">
                                    {{ $appointment->user->name }}
                                    <div class="text-xs text-gray-500">{{ $appointment->user->email }}</div>
                                </td>
                                <td class="px-4 py-3">{{ $appointment->service_name }}</td>
                                <td class="px-4 py-3">{{ $appointment->staff?->name ?? 'Any available' }}</td>
                                <td class="px-4 py-3">{{ $appointment->appointment_at->format('M d, Y h:i A') }}</td>
                                <td class="px-4 py-3">₱{{ number_format($appointment->service_price, 2) }}</td>
                                <td class="px-4 py-3">
                                    <form method="POST" action="{{ route('owner.appointments.status', $appointment) }}">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" onchange="this.form.submit()"
                                                class="border-gray-300 rounded-md text-sm capitalize">
                                            @foreach($statuses as $s)
                                                <option value="{{ $s }}" @selected($appointment->status === $s)>{{ $s }}</option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-gray-500">No appointments found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $appointments->links() }}</div>
        </div>
    </div>
</x-app-layout>