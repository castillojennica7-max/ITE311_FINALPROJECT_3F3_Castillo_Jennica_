<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <p>Welcome, {{ auth()->user()->name }}!</p>
<p>Role: <strong>{{ auth()->user()->role }}</strong></p>

<div class="mt-4 space-x-4">
    <a class="text-indigo-600 underline" href="{{ route('appointments.create') }}">Book an Appointment</a>
    <a class="text-indigo-600 underline" href="{{ route('appointments.index') }}">My Appointments</a>
</div>

@if(auth()->user()->isAdmin())
    <p class="mt-6 font-semibold">Admin pages:</p>
    <ul class="list-disc ml-6 mt-2">
        <li><a class="text-indigo-600 underline" href="{{ route('customers.index') }}">Customers</a></li>
        <li><a class="text-indigo-600 underline" href="{{ route('services.index') }}">Services</a></li>
        <li><a class="text-indigo-600 underline" href="{{ route('staff.index') }}">Staff</a></li>
    </ul>
@endif
        </div>
    </div>
</x-app-layout>
