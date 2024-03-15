<x-app-layout>
    <div class="stu-records py-6 max-h-full">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" >
            <div class="flex overflow-hidden lg:min-h-[87h] md:min-h-[80vh] sm:min-h-[73vh]">
            <!-- Sidebar / Navigation -->
            <aside class="w-1/5 lg:min-h-[87h] md:min-h-[80vh] sm:min-h-[73vh] sm:rounded-lg bg-white shadow p-4">
                <nav class="registrar-functions-nav">
                    <ul class="mt-4">
                        <li class="menu-nav" @click.prevent="openStudents = !openStudents">
                            <a href="{{ route('student-records') }}" class="{{  request()->routeIs('student-records') || request()->routeIs('student.add') ? 'font-semibold' : '' }} block rounded-md py-2 px-4 hover:bg-gray-200">
                            Student Records
                            </a>
                        </li>
                        <li>
                            <ul x-show="openStudents" class="submenu">
                                <li>
                                    <a href="{{ route('student-records') }}" class="{{ request()->routeIs('student-records') ? 'active-main' : '' }} block rounded-md py-2 hover:bg-gray-200">
                                        Student Records
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('student.add') }}" class="{{ request()->routeIs('student.add') ? 'active-main' : '' }} block rounded-md py-2 px-6 hover:bg-gray-200">
                                        Add Student
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="menu-nav">
                            <a href="{{ route('faculty-records') }}" class="{{ request()->routeIs('faculty-records') ? 'active-main' : '' }} block rounded-md py-2 px-4 hover:bg-gray-200">
                                Faculty Records
                            </a>
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