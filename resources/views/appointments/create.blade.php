<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Book an Appointment</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                @unless($cmsOnline)
                    <p class="mb-4 text-red-600">The service list is currently unavailable. Please try again later.</p>
                @endunless

                <form method="POST" action="{{ route('appointments.store') }}">
                    @csrf

                    <div>
                        <x-input-label for="service_document_id" value="Service" />
                        <select id="service_document_id" name="service_document_id" required
                                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">Select a service</option>
                            @foreach($services as $service)
                                <option value="{{ $service['documentId'] }}"
                                    @selected(old('service_document_id') === $service['documentId'])>
                                    {{ $service['name'] }} - ₱{{ number_format($service['price'], 2) }}
                                    ({{ $service['duration_minutes'] }} min)
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('service_document_id')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="staff_id" value="Stylist" />
                        <select id="staff_id" name="staff_id"
                                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">Any available stylist</option>
                            @foreach($staff as $member)
                                <option value="{{ $member->id }}" @selected(old('staff_id') == $member->id)>
                                    {{ $member->name }} ({{ $member->role }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('staff_id')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="appointment_at" value="Date and Time" />
                        <input id="appointment_at" type="datetime-local" name="appointment_at" required
                               min="{{ now()->format('Y-m-d\TH:i') }}"
                               value="{{ old('appointment_at') }}"
                               class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                        <x-input-error :messages="$errors->get('appointment_at')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="notes" value="Notes (optional)" />
                        <textarea id="notes" name="notes" rows="3"
                                  class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('notes') }}</textarea>
                        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                    </div>

                    <div class="mt-6">
                        <x-primary-button>Book Appointment</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>