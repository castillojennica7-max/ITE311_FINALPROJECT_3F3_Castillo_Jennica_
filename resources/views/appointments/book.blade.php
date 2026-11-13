<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Book at {{ $salon->name }}</h2>
    </x-slot>

    @php
        $hours    = $salon->hours();
        $todayKey = strtolower(now()->format('D'));
        $chosen   = (int) old('salon_service_id', $selected);
    @endphp

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4"><x-back-link :href="route('appointments.create')" label="Back to salons" /></div>

            <div class="grid lg:grid-cols-3 gap-8">
                <form method="POST" action="{{ route('appointments.store', $salon) }}" class="lg:col-span-2 space-y-8">
                    @csrf

                    {{-- Services --}}
                    <div>
                        <h3 class="font-extrabold text-gray-900">1. Choose a service</h3>

                        @if($services->isEmpty())
                            <p class="mt-3 p-4 rounded-lg bg-yellow-50 text-yellow-800 text-sm">
                                This salon has not added its services yet. Please check back later.
                            </p>
                        @else
                            <div class="mt-3 grid sm:grid-cols-2 gap-3">
                                @foreach($services as $service)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="salon_service_id" value="{{ $service->id }}" class="peer sr-only"
                                               @checked($chosen === $service->id) required>
                                        <div class="flex gap-3 bg-white rounded-xl border-2 border-[#f0dce8] p-3 peer-checked:border-[#7b2d5f] peer-checked:bg-[#fbf3f8] hover:shadow transition">
                                            <img src="{{ $service->imageUrl() }}" alt="" class="w-16 h-16 rounded-lg object-cover shrink-0 bg-[#f3e3ee]">
                                            <div class="min-w-0">
                                                <p class="font-bold text-gray-900 leading-tight">{{ $service->name }}</p>
                                                <p class="text-sm font-extrabold text-[#7b2d5f]">₱{{ number_format($service->price) }}</p>
                                                <p class="text-xs text-gray-500">{{ $service->categoryLabel() }} &middot; {{ $service->duration_minutes }} min</p>
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @endif
                        <x-input-error :messages="$errors->get('salon_service_id')" class="mt-2" />
                    </div>

                    {{-- Stylist --}}
                    <div>
                        <h3 class="font-extrabold text-gray-900">2. Choose a stylist</h3>
                        <select id="staff_id" name="staff_id" class="mt-3 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">Any available stylist</option>
                            @foreach($staff as $member)
                                <option value="{{ $member->id }}" @selected(old('staff_id') == $member->id)>
                                    {{ $member->name }} ({{ $member->role }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('staff_id')" class="mt-2" />
                    </div>

                    {{-- Date and time --}}
                    <div>
                        <h3 class="font-extrabold text-gray-900">3. Pick a date and time</h3>
                        <input id="appointment_at" type="datetime-local" name="appointment_at" required
                               min="{{ now()->format('Y-m-d\TH:i') }}"
                               value="{{ old('appointment_at') }}"
                               class="mt-3 block w-full border-gray-300 rounded-md shadow-sm">
                        <p class="mt-1 text-xs text-gray-500">Please choose a time within the salon opening hours shown on the right.</p>
                        <x-input-error :messages="$errors->get('appointment_at')" class="mt-2" />
                    </div>

                    {{-- Notes --}}
                    <div>
                        <h3 class="font-extrabold text-gray-900">4. Notes (optional)</h3>
                        <textarea id="notes" name="notes" rows="3"
                                  class="mt-3 block w-full border-gray-300 rounded-md shadow-sm">{{ old('notes') }}</textarea>
                        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                    </div>

                    <div>
                        <x-primary-button>Book Appointment</x-primary-button>
                    </div>
                </form>

                {{-- Salon info --}}
                <aside class="space-y-6">
                    <div class="bg-white rounded-2xl border border-[#f0dce8] shadow-sm p-6">
                        <p class="font-extrabold text-gray-900">{{ $salon->name }}</p>
                        @if($salon->address)<p class="text-sm text-gray-600 mt-1">📍 {{ $salon->address }}</p>@endif
                        @if($salon->phone)<p class="text-sm text-gray-600">📞 {{ $salon->phone }}</p>@endif
                        <a href="{{ route('salons.show', $salon) }}" class="inline-block mt-3 text-sm font-semibold text-[#7b2d5f] hover:underline">View salon page &rarr;</a>
                    </div>

                    <div class="bg-white rounded-2xl border border-[#f0dce8] shadow-sm p-6">
                        <p class="font-extrabold text-gray-900">Opening hours</p>
                        <ul class="mt-3 divide-y text-sm">
                            @foreach(\App\Models\Salon::DAYS as $key => $label)
                                <li class="flex justify-between py-2 {{ $key === $todayKey ? 'font-bold text-[#7b2d5f]' : 'text-gray-600' }}">
                                    <span>{{ $label }}</span>
                                    <span>
                                        @if($hours[$key]['closed'])
                                            Closed
                                        @else
                                            {{ \App\Models\Salon::formatTime($hours[$key]['open']) }} - {{ \App\Models\Salon::formatTime($hours[$key]['close']) }}
                                        @endif
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</x-app-layout>
