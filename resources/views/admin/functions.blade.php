<x-app-layout>
    <div class="stu-records py-6 max-h-full">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" >
            <div class="flex overflow-hidden min-h-screen">
            <!-- Sidebar / Navigation -->
                <aside class="w-1/5 min-h-screen sm:rounded-lg bg-white shadow p-4">
                    <!-- Navigation Links -->
                    <nav class="registrar-functions-nav">
                        <ul class="mt-4">                       
                            <li class="menu-nav" @click.prevent="open = !open">
                                <a 
                                    href="{{ route('program-list') }}" 
                                    class="{{  
                                        request()->routeIs('program-list') || 
                                        request()->routeIs('dean-access.program-list') || 
                                        request()->routeIs('subject-catalog') || 
                                        request()->routeIs('dean-access.subject-catalog') || 
                                        request()->routeIs('academic-calendar') || 
                                        request()->routeIs('dean-access.academic-calendar') || 
                                        request()->routeIs('program-list.show') || 
                                        request()->routeIs('dean-access.program-list.show') || 
                                        request()->routeIs('sections') || 
                                        request()->routeIs('dean-access.sections') || 
                                        request()->routeIs('departments') || 
                                        request()->routeIs('academic-year') ? 'font-semibold' : '' }} block rounded-md py-2 px-4 hover:bg-gray-200">
                                    Program Management
                                </a>
                            </li>
                            <li>
                                <ul x-show="open" class="submenu">
                                    @if(Auth::check() && Auth::user()->role === 'admin')
                                    <li><a href="{{ route('program-list') }}" class="{{ request()->routeIs('program-list') ||  request()->routeIs('program-list.show') ? 'active-main' : '' }} block py-2 hover:bg-gray-200">Program Management</a></li>
                                    @elseif((Auth::check() && Auth::user()->role === 'dean'))
                                    <li><a href="{{ route('dean-access.program-list') }}" class="{{ request()->routeIs('dean-access.program-list') || request()->routeIs('dean-access.program-list.show') ? 'active-main' : '' }} block py-2 hover:bg-gray-200">Program Management</a></li>
                                    @endif 
                                    <!-- dean-access.subject-catalog -->
                                    @if(Auth::check() && Auth::user()->role === 'admin')
                                    <li><a href="{{ route('subject-catalog') }}" class="{{ request()->routeIs('subject-catalog') ? 'active-main' : '' }} block py-2 px-6 hover:bg-gray-200">Subjects Catalog</a></li>
                                    @elseif((Auth::check() && Auth::user()->role === 'dean'))
                                    <li><a href="{{ route('dean-access.subject-catalog') }}" class="{{ request()->routeIs('dean-access.subject-catalog') ? 'active-main' : '' }} block py-2 px-6 hover:bg-gray-200">Subjects Catalog</a></li>
                                    @endif
                                    <!-- 'dean-access.sections -->
                                    @if(Auth::check() && Auth::user()->role === 'admin')
                                    <li><a href="{{ route('sections') }}" class="{{ request()->routeIs('sections') ? 'active-main' : '' }} block py-2 px-6 hover:bg-gray-200">Sections</a></li>
                                    @elseif((Auth::check() && Auth::user()->role === 'dean'))
                                    <li><a href="{{ route('dean-access.sections') }}" class="{{ request()->routeIs('dean-access.sections') ? 'active-main' : '' }} block py-2 px-6 hover:bg-gray-200">Sections</a></li>
                                    @endif
                                    <!-- dean-access.academic-calendar -->
                                    @if(Auth::check() && Auth::user()->role === 'admin')
                                    <li><a href="{{ route('academic-calendar') }}" class="{{ request()->routeIs('academic-calendar') ? 'active-main' : '' }} block py-2 px-6 hover:bg-gray-200">Academic Calendar</a></li>
                                    @elseif((Auth::check() && Auth::user()->role === 'dean'))
                                    <li><a href="{{ route('dean-access.academic-calendar') }}" class="{{ request()->routeIs('dean-access.academic-calendar') ? 'active-main' : '' }} block py-2 px-6 hover:bg-gray-200">Academic Calendar</a></li>
                                    @endif
                                    @if(Auth::check() && Auth::user()->role === 'admin')
                                    <li><a href="{{ route('academic-year') }}" class="{{ request()->routeIs('academic-year') ? 'active-main' : '' }} block py-2 px-6 hover:bg-gray-200">Academic Year</a></li>
                                    @endif
                                    @if(Auth::check() && Auth::user()->role === 'admin')
                                    <li><a href="{{ route('departments') }}" class="{{ request()->routeIs('departments') ? 'active-main' : '' }} block py-2 px-6 hover:bg-gray-200">Departments</a></li>
                                    @endif
                                </ul>
                            </li>
                            @if(Auth::check() && Auth::user()->role === 'admin')
                            <li class="menu-nav"><a href="#" class="block rounded-md py-2 px-4">User Access Management</a></li>
                            <li class="menu-nav"><a href="#" class="block rounded-md py-2 px-4">InfoSystems</a></li>
                            <li class="menu-nav"><a href="#" class="block rounded-md py-2 px-4">Contacts</a></li>
                            @endif
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