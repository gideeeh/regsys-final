<x-app-layout>
    <div class="stu-records py-6 max-h-full">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" >
            <div class="flex overflow-hidden min-h-screen">
            <!-- Sidebar / Navigation -->
                <aside class="w-1/5 min-h-screen sm:rounded-lg bg-white shadow p-4">
                    <!-- Navigation Links -->
                    <nav class="registrar-functions-nav">
                        <ul class="mt-4" x-data="{ open: false }">
                            <li><a href="{{ route('appointments.dashboard') }}" class="{{ request()->routeIs('appointments.dashboard') ? 'active-main' : '' }} block py-2 hover:bg-gray-200">Appointments Dashboard</a></li>
                            <li><a href="{{ route('appointments.services') }}" class="{{ request()->routeIs('appointments.services') ? 'active-main' : '' }} block py-2 hover:bg-gray-200">Manage Services</a></li>
                        </ul>
                    </nav>
                </aside>

                <!-- Main Content Area -->
                <main class="w-4/5 bg-white shadow overflow-hidden sm:rounded-lg p-6 ml-4">
                    <!-- Main content goes here -->
                    @yield('content')
                </main>
            </div>
        </div>
    </div>
        
</x-app-layout>