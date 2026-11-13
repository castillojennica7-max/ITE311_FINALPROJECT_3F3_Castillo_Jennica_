<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $promotion ? 'Edit Promo or Bundle' : 'Add Promo or Bundle' }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4"><x-back-link :href="route('owner.promotions.index')" label="Back to Promos and Bundles" /></div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST"
                      action="{{ $promotion ? route('owner.promotions.update', $promotion) : route('owner.promotions.store') }}">
                    @csrf
                    @if($promotion) @method('PUT') @endif

                    <div>
                        <x-input-label for="type" value="Type" />
                        <select id="type" name="type" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                            <option value="promo" @selected(old('type', $promotion?->type ?? 'promo') === 'promo')>Promo (discount)</option>
                            <option value="bundle" @selected(old('type', $promotion?->type ?? 'promo') === 'bundle')>Bundle (package price)</option>
                        </select>
                        <x-input-error :messages="$errors->get('type')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="title" value="Title" />
                        <x-text-input id="title" name="title" type="text" class="block mt-1 w-full" :value="old('title', $promotion?->title)" required autofocus />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="description" value="Description" />
                        <textarea id="description" name="description" rows="3"
                                  class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('description', $promotion?->description) }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div>
                            <x-input-label for="discount_percent" value="Discount % (for promos)" />
                            <x-text-input id="discount_percent" name="discount_percent" type="number" class="block mt-1 w-full" :value="old('discount_percent', $promotion?->discount_percent)" />
                            <x-input-error :messages="$errors->get('discount_percent')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="bundle_price" value="Bundle price ₱ (for bundles)" />
                            <x-text-input id="bundle_price" name="bundle_price" type="number" step="0.01" class="block mt-1 w-full" :value="old('bundle_price', $promotion?->bundle_price)" />
                            <x-input-error :messages="$errors->get('bundle_price')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-4">
                        <x-input-label for="included_services" value="What is included (one item per line)" />
                        <textarea id="included_services" name="included_services" rows="4"
                                  class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('included_services', $promotion?->included_services) }}</textarea>
                        <x-input-error :messages="$errors->get('included_services')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="valid_until" value="Valid until (optional)" />
                        <x-text-input id="valid_until" name="valid_until" type="date" class="block mt-1 w-full"
                                      :value="old('valid_until', $promotion?->valid_until?->format('Y-m-d'))" />
                        <x-input-error :messages="$errors->get('valid_until')" class="mt-2" />
                    </div>

                    <label class="mt-4 flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300"
                               @checked(old('is_active', $promotion?->is_active ?? true))>
                        Show to customers
                    </label>

                    <div class="mt-6">
                        <x-primary-button>{{ $promotion ? 'Save changes' : 'Add' }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
