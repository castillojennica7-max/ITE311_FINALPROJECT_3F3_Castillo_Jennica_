<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Salon Settings</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4"><x-back-link /></div>

            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <p class="text-sm text-gray-500 mb-4">
                    These details are shown to customers when they choose a salon to book with.
                </p>

                <form method="POST" action="{{ route('owner.settings.update') }}">
                    @csrf
                    @method('PATCH')

                    <div>
                        <x-input-label for="name" value="Salon name" />
                        <x-text-input id="name" name="name" type="text" class="block mt-1 w-full" :value="old('name', $salon->name)" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="phone" value="Phone" />
                        <x-text-input id="phone" name="phone" type="text" class="block mt-1 w-full" :value="old('phone', $salon->phone)" />
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="address" value="Address" />
                        <x-text-input id="address" name="address" type="text" class="block mt-1 w-full" :value="old('address', $salon->address)" />
                        <x-input-error :messages="$errors->get('address')" class="mt-2" />
                    </div>

                    <div class="mt-6">
                        <x-primary-button>Save changes</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>