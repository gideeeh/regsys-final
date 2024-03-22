<x-app-layout>
    <div class="stu-records py-6 max-h-full">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" >
            <div class="flex overflow-hidden min-h-screen">
            <!-- Sidebar / Navigation -->
                <aside class="w-1/5 min-h-screen sm:rounded-lg bg-white shadow p-4">
                    <!-- Navigation Links -->
                    <nav class="registrar-functions-nav">
                        <ul class="mt-4" x-data="{ open: false }">
                            <li class="menu-nav">
                                <a href="{{ route('enrollment-records') }}" class="{{  request()->routeIs('enrollment-records') || request()->routeIs('enrollments.credit-subject') || request()->routeIs('enrollments.enroll') ? 'font-semibold' : '' }} block rounded-md py-2 px-4 hover:bg-gray-200">
                                Enrollments
                                </a>
                            </li>
                            <li x-data="{ open: {{ request()->routeIs('enrollment-records') || request()->routeIs('enrollments.enroll') || request()->routeIs('enrollments.credit-subject') ? 'true' : 'false' }} }">
                                <!-- Submenu -->
                                <ul x-show="open" class="submenu">
                                    <li><a href="{{ route('enrollment-records') }}" class="{{ request()->routeIs('enrollment-records') ? 'active-main' : '' }} block py-2 hover:bg-gray-200">Enrollment Records</a></li>
                                    <li><a href="{{ route('enrollments.enroll') }}" class="{{ request()->routeIs('enrollments.enroll') ? 'active-main' : '' }} block py-2 px-6 hover:bg-gray-200">Enroll Student</a></li>
                                    <!-- <li><a href="{{ route('enrollments.credit-subject') }}" class="{{ request()->routeIs('enrollments.credit-subject') ? 'active-main' : '' }} block py-2 px-6 hover:bg-gray-200">Credit Subject</a></li> -->
                                </ul>
                                <!-- Section for further updates -->
                            </li>
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