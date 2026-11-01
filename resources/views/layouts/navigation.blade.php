<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-2">
                        <x-application-logo class="block h-9 w-9" />
                        <span class="text-xl font-bold text-[#7b2d5f]">SalonSync</span>
                    </a>
                </div>

                <!-- Tabs -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        Dashboard
                    </x-nav-link>

                    @if(auth()->user()->isAdmin())
                        @php
                            $manageActive = request()->routeIs('admin.*', 'customers.*', 'services.*', 'staff.*');
                        @endphp
                        <div class="flex items-center">
                            <x-dropdown align="left" width="48">
                                <x-slot name="trigger">
                                    <button type="button"
                                            class="inline-flex items-center h-16 px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out focus:outline-none {{ $manageActive ? 'border-[#7b2d5f] text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                        Manage
                                        <svg class="ms-1 fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <x-dropdown-link :href="route('admin.appointments.index')">Appointments</x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.subscriptions.index')">Subscriptions</x-dropdown-link>
                                    <x-dropdown-link :href="route('customers.index')">Customers</x-dropdown-link>
                                    <x-dropdown-link :href="route('services.index')">Services</x-dropdown-link>
                                    <x-dropdown-link :href="route('staff.index')">Staff</x-dropdown-link>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @elseif(auth()->user()->isSalonOwner())
                        <x-nav-link :href="route('subscriptions.index')" :active="request()->routeIs('subscriptions.*')">
                            My Subscription
                        </x-nav-link>
                    @else
                        <x-nav-link :href="route('appointments.create')" :active="request()->routeIs('appointments.create')">
                            Book Appointment
                        </x-nav-link>
                        <x-nav-link :href="route('appointments.index')" :active="request()->routeIs('appointments.index')">
                            My Appointments
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Profile dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">Profile</x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                Log Out
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger (mobile) -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Dashboard</x-responsive-nav-link>

            @if(auth()->user()->isAdmin())
                <div class="px-4 pt-3 text-xs font-semibold text-gray-400 uppercase">Manage</div>
                <x-responsive-nav-link :href="route('admin.appointments.index')">Appointments</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.subscriptions.index')">Subscriptions</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('customers.index')">Customers</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('services.index')">Services</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('staff.index')">Staff</x-responsive-nav-link>
            @elseif(auth()->user()->isSalonOwner())
                <x-responsive-nav-link :href="route('subscriptions.index')" :active="request()->routeIs('subscriptions.*')">My Subscription</x-responsive-nav-link>
            @else
                <x-responsive-nav-link :href="route('appointments.create')" :active="request()->routeIs('appointments.create')">Book Appointment</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('appointments.index')" :active="request()->routeIs('appointments.index')">My Appointments</x-responsive-nav-link>
            @endif
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">Profile</x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        Log Out
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>