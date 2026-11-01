<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Add Service</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4"><x-back-link :href="route('services.index')" label="Back to Services" /></div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('services.store') }}">
                    @csrf

                    <div>
                        <x-input-label for="name" value="Service name" />
                        <x-text-input id="name" name="name" type="text" class="block mt-1 w-full" :value="old('name')" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="price" value="Price" />
                        <x-text-input id="price" name="price" type="number" step="0.01" class="block mt-1 w-full" :value="old('price')" required />
                        <x-input-error :messages="$errors->get('price')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="duration_minutes" value="Duration (minutes)" />
                        <x-text-input id="duration_minutes" name="duration_minutes" type="number" class="block mt-1 w-full" :value="old('duration_minutes')" required />
                        <x-input-error :messages="$errors->get('duration_minutes')" class="mt-2" />
                    </div>

                    <div class="mt-6">
                        <x-primary-button>Save</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>