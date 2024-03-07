<div x-data="{ searchTerm: '{{ $searchTerm ?? '' }}' }">
    <x-app-layout>
        <x-slot name="header">
            <div class="flex justify-between items-center h-10">
                <a href="{{ route('course-listings') }}" class="font-semibold text-xl text-gray-800 leading-tight no-underline hover:underline">
                    {{ __('Course Listings') }}
                </a>
                <x-search-form action="{{ route('course-listings') }}" placeholder="Search Course" />
            </div>
        </x-slot>

        <div class="stu-records py-6 max-h-full">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" >
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="overflow-hidden sm:rounded-lg shadow-sm">
                        <table class="min-w-full">
                            <thead >
                                <tr class="cursor-default">
                                    <th class="px-6 py-3 text-left">Course Code</th>
                                    <th class="px-6 py-3 text-left">Course Name</th>
                                    <th class="px-6 py-3 text-left">Degree Type</th>
                                    <th class="px-6 py-3 text-left">Department Name</th>
                                    <th class="px-6 py-3 text-left">Total Units</th>
                                    <th class="px-6 py-3 text-left">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($courses as $course)
                            <tr class="border-b hover:bg-gray-100" x-data="{}" @click="window.location.href='{{ route('course-listings.show', $course->program_id) }}'" style="cursor: pointer;">
                                    <td class="px-6 py-4 text-left">{{$course->program_code}}</td>
                                    <td class="px-6 py-4 text-left">{{$course->program_name}}</td>
                                    <td class="px-6 py-4 text-left">{{$course->degree_type}}</td>
                                    <td class="px-6 py-4 text-left">{{$course->dept_name}}</td>
                                    <td class="px-6 py-4 text-left">{{$course->total_units}}</td>
                                    <td class="px-6 py-4 text-left">{{$course->status}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $courses->links() }}
                    </div>
                </div>
            </div>
        </div>
    </x-app-layout>
</div>