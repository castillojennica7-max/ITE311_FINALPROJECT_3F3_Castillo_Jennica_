<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $service ? 'Edit Service' : 'Add Service' }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4"><x-back-link :href="route('owner.services.index')" label="Back to Services" /></div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" enctype="multipart/form-data"
                      action="{{ $service ? route('owner.services.update', $service) : route('owner.services.store') }}">
                    @csrf
                    @if($service) @method('PUT') @endif

                    <div>
                        <x-input-label for="name" value="Service name" />
                        <x-text-input id="name" name="name" type="text" class="block mt-1 w-full" :value="old('name', $service?->name)" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="category" value="Category" />
                        <select id="category" name="category" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                            @foreach($categories as $key => $label)
                                <option value="{{ $key }}" @selected(old('category', $service?->category ?? 'hair') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('category')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div>
                            <x-input-label for="price" value="Price (₱)" />
                            <x-text-input id="price" name="price" type="number" step="0.01" class="block mt-1 w-full" :value="old('price', $service?->price)" required />
                            <x-input-error :messages="$errors->get('price')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="duration_minutes" value="Duration (minutes)" />
                            <x-text-input id="duration_minutes" name="duration_minutes" type="number" class="block mt-1 w-full" :value="old('duration_minutes', $service?->duration_minutes ?? 30)" required />
                            <x-input-error :messages="$errors->get('duration_minutes')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-4">
                        <x-input-label for="description" value="Description" />
                        <textarea id="description" name="description" rows="3"
                                  class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('description', $service?->description) }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="image" value="Photo (optional)" />
                        <div class="mt-2 flex items-center gap-3">
                            <img src="{{ $service ? $service->imageUrl() : asset('images/services/other.svg') }}" alt="" class="w-16 h-16 rounded-lg object-cover bg-[#f3e3ee]">
                            <input id="image" name="image" type="file" accept="image/*" class="text-sm">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">If you do not upload a photo, a default picture for the category is used.</p>
                        @if($service?->image_path)
                            <label class="mt-2 flex items-center gap-2 text-sm text-gray-600">
                                <input type="checkbox" name="remove_image" value="1" class="rounded border-gray-300"> Remove my uploaded photo
                            </label>
                        @endif
                        <x-input-error :messages="$errors->get('image')" class="mt-2" />
                    </div>

                    <label class="mt-4 flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300"
                               @checked(old('is_active', $service?->is_active ?? true))>
                        Visible to customers
                    </label>

                    <div class="mt-6">
                        <x-primary-button>{{ $service ? 'Save changes' : 'Add service' }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
