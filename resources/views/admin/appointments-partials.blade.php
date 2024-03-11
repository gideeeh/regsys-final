<x-app-layout>
<div class="w-full p-6 flex gap-4 items-start">
    <aside class="w-2/12 bg-white shadow-sm sm:rounded-lg min-h-[80vh] min-h-[80vh] p-4">
        <nav class="">
            <ul class="mt-4" x-data="{ open: false }">
                <li>
                    <a href="{{ request()->routeIs('appointments.dashboard') ? '#' : route('appointments.dashboard') }}" 
                    class="{{ request()->routeIs('appointments.dashboard') ? 'active-main' : '' }} block py-2 hover:bg-gray-200">
                        Appointments Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ request()->routeIs('appointments') ? '#' : route('appointments') }}" 
                    class="{{ request()->routeIs('appointments') ? 'active-main' : '' }} block py-2 hover:bg-gray-200">
                        Appointments
                    </a>
                </li>
                <li>
                    <a href="{{ request()->routeIs('appointments.services') ? '#' : route('appointments.services') }}" 
                    class="{{ request()->routeIs('appointments.services') ? 'active-main' : '' }} block py-2 hover:bg-gray-200">
                        Manage Services
                    </a>
                </li>
            </ul>
        </nav>
    </aside>
    <div class="w-10/12">
        @yield('content')
    </div>
</div>
</x-app-layout>

