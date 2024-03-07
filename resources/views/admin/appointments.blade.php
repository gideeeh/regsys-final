<x-app-layout>
<x-alert-message />
<div class="flex gap-4">
    <div class="w-2/12 bg-white shadow-sm sm:rounded-lg max-h-[80vh]">
        
        </div>
            <!-- Sidebar / Navigation -->
                <!-- <aside class="w-1/5 min-h-screen sm:rounded-lg bg-white shadow p-4">
                    <nav class="registrar-functions-nav">
                        <ul class="mt-4" x-data="{ open: false }">
                            <li><a href="{{ route('appointments.dashboard') }}" class="{{ request()->routeIs('appointments.dashboard') ? 'active-main' : '' }} block py-2 hover:bg-gray-200">Appointments Dashboard</a></li>
                            <li><a href="{{ route('appointments.services') }}" class="{{ request()->routeIs('appointments.services') ? 'active-main' : '' }} block py-2 hover:bg-gray-200">Manage Services</a></li>
                        </ul>
                    </nav>
                </aside> -->

                <!-- Main Content Area -->
    <div>
        <!-- Main content goes here -->
        @yield('content')
    </div>
</div>
</x-app-layout>