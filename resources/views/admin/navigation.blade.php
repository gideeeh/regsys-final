<nav x-data="{ openFunctions: false, openEnrollments:false, openRecords: false, openAppointments: false, notifs: false, }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('admin.dashboard') }}">
                        <x-application-logo class="block h-9 w-4 fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <div @mouseover="openRecords = true" @mouseleave="openRecords = false" class="relative sm:flex sm:items-center">
                        <x-nav-link 
                            class="sm:flex sm:items-center h-full" 
                            :href="route('student-records')" 
                            :active="request()->routeIs([
                                'student-records',
                                'student.add',
                                'student-records.show',
                                'faculty-records',
                                'faculty-records.show',
                                ])">
                            {{ __('Records') }}
                        </x-nav-link>
                        <div class="dropdown-content absolute bg-white shadow-md z-50 w-40" 
                            x-cloak 
                            x-show="openRecords"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 transform scale-90"
                            x-transition:enter-end="opacity-100 transform scale-100">
                            <!-- Dropdown links -->
                            <x-dropdown-link :href="route('student-records')">
                                        {{ __('Student Records') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('faculty-records')">
                                        {{ __('Faculty Records') }}
                            </x-dropdown-link>
                        </div>
                    </div>
                    <div @mouseover="openEnrollments = true" @mouseleave="openEnrollments = false" class="relative sm:flex sm:items-center">
                        <x-nav-link 
                            class="sm:flex sm:items-center h-full" 
                            :href="route('enrollment-records')" 
                            :active="request()->routeIs([
                                'enrollment-records',
                                'enrollments.enroll',
                                ])">
                            {{ __('Enrollments') }}
                        </x-nav-link>
                        <div class="dropdown-content absolute bg-white shadow-md z-50 w-40" 
                            x-cloak 
                            x-show="openEnrollments" 
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 transform scale-90"
                            x-transition:enter-end="opacity-100 transform scale-100">
                            <!-- Dropdown links -->
                            <x-dropdown-link :href="route('enrollment-records')">
                                        {{ __('Enrollment Records') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('enrollments.enroll')">
                                        {{ __('Enroll Student') }}
                            </x-dropdown-link>
                        </div>
                    </div>

                    <div @mouseover="openFunctions = true" @mouseleave="openFunctions = false" class="relative sm:flex sm:items-center">
                        <x-nav-link 
                            class="sm:flex sm:items-center h-full" 
                            :href="route('program-list')" 
                            :active="request()->routeIs([
                                'program-list',
                                'subject-catalog',
                                'academic-calendar',
                                'academic-year',
                                'sections',
                                'sections.show',
                                'departments',
                                'program-list.show',
                                ])">
                            {{ __('Functions') }}
                        </x-nav-link>
                        <div class="dropdown-content absolute bg-white shadow-md z-50 w-40" 
                            x-cloak 
                            x-show="openFunctions"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 transform scale-90"
                            x-transition:enter-end="opacity-100 transform scale-100">
                            <!-- Dropdown links -->
                            <x-dropdown-link :href="route('program-list')">
                                        {{ __('Program Management') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('program-list')">
                                        {{ __('User Access') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('program-list')">
                                        {{ __('InfoSystems') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('program-list')">
                                        {{ __('Contacts') }}
                            </x-dropdown-link>
                        </div>
                    </div>
                    <div @mouseover="openAppointments = true" @mouseleave="openAppointments = false" class="relative sm:flex sm:items-center">
                        <x-nav-link 
                            class="sm:flex sm:items-center h-full" 
                            :href="route('appointments.dashboard')" 
                            :active="request()->routeIs([
                                'appointments.dashboard',
                                'appointments',
                                'appointments.manage',
                                'appointments.services',
                                ])">
                            {{ __('Student Appointments') }}
                        </x-nav-link>
                        <div class="dropdown-content absolute bg-white shadow-md z-50 w-40" 
                            x-cloak 
                            x-show="openAppointments"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 transform scale-90"
                            x-transition:enter-end="opacity-100 transform scale-100">
                            <!-- Dropdown links -->
                            <x-dropdown-link :href="route('appointments.dashboard')">
                                        {{ __('Appointments Dashboard') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('appointments')">
                                        {{ __('Appointments Management') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('appointments.services')">
                                        {{ __('Services') }}
                            </x-dropdown-link>
                        </div>
                    </div>
                    <!-- <x-nav-link :href="route('appointments.dashboard')" :active="request()->routeIs('appointments.dashboard')">
                        {{ __('Student Appointments') }}
                    </x-nav-link> -->
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <div class="relative sm:flex sm:items-center sm:justify-end">
                    <x-clarity-notification-solid 
                        @click="notifs=!notifs"
                        class="text-slate-400 w-5 mr-2 hover:text-slate-900 transition-colors duration-200 cursor-pointer" />
                    <!-- Notification Modal -->
                    <div class="flex flex-col dropdown-content absolute bg-slate-300 shadow-md z-50 mt-2 ml-[-8rem] w-40" x-cloak x-show="notifs">
                        <span>Notif 1</span>
                        <span>Notif 2</span>
                        <span>Notif 3</span>
                        <span>See more</span>
                    </div>
                </div>
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
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

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
