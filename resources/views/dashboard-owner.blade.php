<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-[#7b2d5f] leading-tight">Dashboard</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div class="rounded-xl bg-[#7b2d5f] text-white p-6 shadow">
                <p class="text-sm opacity-80">Welcome back,</p>
                <p class="text-2xl font-bold">{{ auth()->user()->name }}</p>
                <p class="text-sm mt-1 opacity-80">Salon owner account</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white rounded-xl shadow-sm border-l-4 border-[#7b2d5f] p-5">
                    <p class="text-sm text-gray-500">Salon</p>
                    @if($salon)
                        <p class="text-xl font-bold text-gray-900">{{ $salon->name }}</p>
                    @else
                        <p class="text-gray-700 mt-1">You have not set up your salon yet.</p>
                    @endif
                </div>

                <div class="bg-white rounded-xl shadow-sm border-l-4 border-[#7b2d5f] p-5">
                    <p class="text-sm text-gray-500">Subscription</p>
                    @if($subscription)
                        <p class="text-xl font-bold text-gray-900">{{ $subscription->plan_name }}</p>
                        <p class="text-sm text-gray-500">Valid until {{ $subscription->ends_at->format('M d, Y') }}</p>
                    @else
                        <p class="text-gray-700 mt-1">No active subscription.</p>
                        <a class="text-[#7b2d5f] underline text-sm" href="{{ route('home') }}#pricing">View plans</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>