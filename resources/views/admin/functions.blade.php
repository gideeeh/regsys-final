<x-app-layout>
    <div class="stu-records py-6 max-h-full">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" >
            <div class="flex overflow-hidden min-h-screen">
            <!-- Sidebar / Navigation -->
                <aside class="w-1/5 min-h-screen sm:rounded-lg bg-white shadow p-4">
                    <!-- Navigation Links -->
                    <nav class="registrar-functions-nav">
                        <ul class="mt-4" x-data="{ open: false }">
                            <li x-data="{ open: {{ 
                                request()->routeIs('program-list') || 
                                request()->routeIs('subject-catalog') || 
                                request()->routeIs('academic-calendar') || 
                                request()->routeIs('program-list.show') || 
                                request()->routeIs('sections') || 
                                request()->routeIs('academic-year')  
                                ? 'true' : 'false' }} }">
                                <div @click.prevent="open = !open" class="flex items-center cursor-pointer py-4 px-4 hover:bg-gray-200">
                                    <span class="{{ 
                                        request()->routeIs('program-list') || 
                                        request()->routeIs('subject-catalog') || 
                                        request()->routeIs('academic-calendar') || 
                                        request()->routeIs('academic-year') || 
                                        request()->routeIs('sections') || 
                                        request()->routeIs('program-list.show') ? 'active-sub' : '' }}">
                                        Program Management
                                    </span>
                                </div>
                                <!-- Submenu -->
                                <ul x-show="open" class="submenu">
                                    <li><a href="{{ route('program-list') }}" class="{{ request()->routeIs('program-list') || request()->routeIs('program-list.show') ? 'active-main' : '' }} block py-2 hover:bg-gray-200">Program Management</a></li>
                                    <li><a href="{{ route('subject-catalog') }}" class="{{ request()->routeIs('subject-catalog') ? 'active-main' : '' }} block py-2 px-6 hover:bg-gray-200">Subjects Catalog</a></li>
                                    <li><a href="{{ route('sections') }}" class="{{ request()->routeIs('sections') ? 'active-main' : '' }} block py-2 px-6 hover:bg-gray-200">Sections</a></li>
                                    <li><a href="{{ route('academic-calendar') }}" class="{{ request()->routeIs('academic-calendar') ? 'active-main' : '' }} block py-2 px-6 hover:bg-gray-200">Academic Calendar</a></li>
                                    <li><a href="{{ route('academic-year') }}" class="{{ request()->routeIs('academic-year') ? 'active-main' : '' }} block py-2 px-6 hover:bg-gray-200">Academic Year</a></li>
                                </ul>
                            </li>
                            <li><a href="#" class="block py-4 px-4 hover:bg-gray-200">User Access Management</a></li>
                            <li><a href="#" class="block py-4 px-4 hover:bg-gray-200">InfoSystems</a></li>
                            <li><a href="#" class="block py-4 px-4 hover:bg-gray-200">Important Contacts</a></li>
                        </ul>
                    </nav>
                </aside>

                <!-- Main Content Area -->
                <main class="w-4/5 bg-white shadow-lg overflow-hidden sm:rounded-lg p-6 ml-4">
                    <!-- Main content goes here -->
                    @yield('content')
                </main>
            </div>
        </div>
    </div>
        
</x-app-layout>