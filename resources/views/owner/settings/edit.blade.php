<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Salon Profile and Hours</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4"><x-back-link /></div>

            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-3 bg-red-100 text-red-800 rounded text-sm">
                    Please fix the highlighted problems below.
                </div>
            @endif

            <form method="POST" action="{{ route('owner.settings.update') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PATCH')

                {{-- Basic info --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-extrabold text-gray-900">Basic information</h3>
                    <p class="text-sm text-gray-500 mb-4">Shown to customers on your salon page.</p>

                    <div>
                        <x-input-label for="name" value="Salon name" />
                        <x-text-input id="name" name="name" type="text" class="block mt-1 w-full" :value="old('name', $salon->name)" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="tagline" value="Tagline (short slogan)" />
                        <x-text-input id="tagline" name="tagline" type="text" class="block mt-1 w-full" :value="old('tagline', $salon->tagline)" />
                        <x-input-error :messages="$errors->get('tagline')" class="mt-2" />
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4 mt-4">
                        <div>
                            <x-input-label for="phone" value="Phone" />
                            <x-text-input id="phone" name="phone" type="text" class="block mt-1 w-full" :value="old('phone', $salon->phone)" />
                            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="address" value="Address" />
                            <x-text-input id="address" name="address" type="text" class="block mt-1 w-full" :value="old('address', $salon->address)" />
                            <x-input-error :messages="$errors->get('address')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-4">
                        <x-input-label for="about" value="About your salon" />
                        <textarea id="about" name="about" rows="4"
                                  class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('about', $salon->about) }}</textarea>
                        <x-input-error :messages="$errors->get('about')" class="mt-2" />
                    </div>
                </div>

                {{-- Images --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-extrabold text-gray-900">Photos</h3>
                    <p class="text-sm text-gray-500 mb-4">JPG or PNG. Logo up to 2 MB, cover up to 4 MB.</p>

                    <div class="grid sm:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="logo" value="Logo" />
                            @if($salon->logoUrl())
                                <img src="{{ $salon->logoUrl() }}" alt="" class="mt-2 w-20 h-20 rounded-xl object-cover border">
                                <label class="mt-2 flex items-center gap-2 text-sm text-gray-600">
                                    <input type="checkbox" name="remove_logo" value="1" class="rounded border-gray-300"> Remove current logo
                                </label>
                            @endif
                            <input id="logo" name="logo" type="file" accept="image/*" class="block mt-2 w-full text-sm">
                            <x-input-error :messages="$errors->get('logo')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="cover" value="Cover photo" />
                            @if($salon->coverUrl())
                                <img src="{{ $salon->coverUrl() }}" alt="" class="mt-2 w-full h-20 rounded-xl object-cover border">
                                <label class="mt-2 flex items-center gap-2 text-sm text-gray-600">
                                    <input type="checkbox" name="remove_cover" value="1" class="rounded border-gray-300"> Remove current cover
                                </label>
                            @endif
                            <input id="cover" name="cover" type="file" accept="image/*" class="block mt-2 w-full text-sm">
                            <x-input-error :messages="$errors->get('cover')" class="mt-2" />
                        </div>
                    </div>
                </div>

                {{-- Hours --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-extrabold text-gray-900">Opening hours</h3>
                    <p class="text-sm text-gray-500 mb-4">Customers can only book inside these hours.</p>

                    <div class="divide-y">
                        @foreach($days as $key => $label)
                            @php
                                $isClosed = session()->hasOldInput()
                                    ? (bool) old("hours.$key.closed")
                                    : $hours[$key]['closed'];
                            @endphp
                            <div class="py-3 flex flex-wrap items-center gap-3">
                                <span class="w-28 font-medium text-gray-700">{{ $label }}</span>
                                <input type="time" name="hours[{{ $key }}][open]" value="{{ old("hours.$key.open", $hours[$key]['open']) }}"
                                       class="border-gray-300 rounded-md text-sm">
                                <span class="text-gray-400">to</span>
                                <input type="time" name="hours[{{ $key }}][close]" value="{{ old("hours.$key.close", $hours[$key]['close']) }}"
                                       class="border-gray-300 rounded-md text-sm">
                                <label class="flex items-center gap-2 text-sm text-gray-600">
                                    <input type="checkbox" name="hours[{{ $key }}][closed]" value="1" class="rounded border-gray-300" @checked($isClosed)> Closed
                                </label>
                                <div class="w-full">
                                    <x-input-error :messages="array_merge($errors->get('hours.' . $key . '.open'), $errors->get('hours.' . $key . '.close'))" />
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <x-primary-button>Save changes</x-primary-button>
                    <a href="{{ route('salons.show', $salon) }}" target="_blank" class="text-sm text-[#7b2d5f] underline">View public salon page</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
