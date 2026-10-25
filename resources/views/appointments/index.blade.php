<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Appointments</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif

            <div class="mb-4">
                <a href="{{ route('appointments.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase tracking-widest">
                    + Book an Appointment
                </a>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-4 py-3">Service</th>
                            <th class="px-4 py-3">Stylist</th>
                            <th class="px-4 py-3">Date and Time</th>
                            <th class="px-4 py-3">Price</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($appointments as $appointment)
                            <tr class="border-t">
                                <td class="px-4 py-3">{{ $appointment->service_name }}</td>
                                <td class="px-4 py-3">{{ $appointment->staff?->name ?? 'Any available' }}</td>
                                <td class="px-4 py-3">{{ $appointment->appointment_at->format('M d, Y h:i A') }}</td>
                                <td class="px-4 py-3">₱{{ number_format($appointment->service_price, 2) }}</td>
                                <td class="px-4 py-3 capitalize">{{ $appointment->status }}</td>
                                <td class="px-4 py-3">
                                    @if(in_array($appointment->status, ['pending', 'confirmed']))
                                        <form method="POST" action="{{ route('appointments.cancel', $appointment) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-red-600 underline"
                                                    onclick="return confirm('Cancel this appointment?')">Cancel</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                                    You have no appointments yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>