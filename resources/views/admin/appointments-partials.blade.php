<x-app-layout>
<div class="w-full p-6 flex gap-4 items-start">
    <aside class="w-2/12 bg-white shadow-sm sm:rounded-lg min-h-[80vh] p-4">
        <nav class="">
            <ul class="mt-4" x-data="{ open: false }">
                <li class="menu-nav">
                    <a href="{{ request()->routeIs('appointments.dashboard') ? '#' : route('appointments.dashboard') }}" 
                    class="{{ request()->routeIs('appointments.dashboard') ? 'active-main' : '' }} block rounded-md py-2 px-4">
                        Appointments Dashboard
                    </a>
                </li>
                <li class="menu-nav">
                    <a href="{{ route('appointments') }}" 
                    class="{{ request()->routeIs('appointments') || request()->routeIs('appointments.manage') ? 'active-main' : '' }} block rounded-md py-2 px-4">
                        Appointments
                    </a>
                </li>
                <!-- <li class="menu-nav">
                    <a href="{{ request()->routeIs('appointments.services') ? '#' : route('appointments.services') }}" 
                    class="{{ request()->routeIs('appointments.services') ? 'active-main' : '' }} block rounded-md py-2 px-4">
                        Manage Services
                    </a>
                </li> -->
            </ul>
        </nav>
    </aside>
    <div class="w-10/12">
        @yield('content')
    </div>
</div>
</x-app-layout>

