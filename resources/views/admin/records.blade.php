<x-app-layout>
    <div class="stu-records py-6 max-h-full">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" >
            <div class="flex overflow-hidden min-h-screen">
            <!-- Sidebar / Navigation -->
                <aside class="w-1/5 min-h-screen sm:rounded-lg bg-white shadow p-4">
                    <!-- Navigation Links -->
                    <nav class="registrar-functions-nav">
                        <ul class="mt-4" x-data="{ openStudents: false }">
                            <li x-data="{ openStudents: {{ request()->routeIs('student-records') || request()->routeIs('student.add') || request()->routeIs('academic-calendar') ? 'true' : 'false' }} }">
                                <div @click.prevent="openStudents = !openStudents" class="flex items-center cursor-pointer rounded-md py-4 px-4 hover:bg-gray-200">
                                    <span class="{{ request()->routeIs('student-records') || request()->routeIs('student.add') || request()->routeIs('academic-calendar') ? 'active-sub' : '' }}">Student Records</span>
                                </div>
                                <!-- Submenu -->
                                <ul x-show="openStudents" class="submenu">
                                    <li><a href="{{ route('student-records') }}" class="{{ request()->routeIs('student-records') ? 'active-main' : '' }} block rounded-md py-4 hover:bg-gray-200">Student Records</a></li>
                                    <li><a href="{{ route('student.add') }}" class="{{ request()->routeIs('student.add') ? 'active-main' : '' }} block rounded-md py-4 px-6 hover:bg-gray-200">Add Student</a></li>
                                </ul>
                            </li>
                            <li><a href="{{route('faculty-records')}}" class="block py-4 px-4 hover:bg-gray-200">Faculty Records</a></li>
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