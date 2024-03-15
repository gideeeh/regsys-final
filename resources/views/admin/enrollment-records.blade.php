@extends('admin.enrollments')
@section('content')
<div x-data="{ searchTerm: '{{ $searchTerm ?? '' }}' }">
    <div class="flex justify-between items-center h-10">
        <a href="{{ route('enrollment-records') }}" class="text-2xl font-semibold mb-4text-gray-800 leading-tight no-underline hover:underline">
            {{ __('Enrollment Records') }}
        </a>
        <x-search-form action="{{ route('enrollment-records') }}" placeholder="Search Enrollment" />
    </div>

    <div class="py-4">
        <div class="my-4">
            {{ $enrollments->links() }}
        </div>
        <div class="overflow-x-auto bg-white rounded-lg shadow overflow-y-auto relative">
            <table class="border-collapse table-auto w-full whitespace-no-wrap bg-white table-striped relative">
                <thead >
                    <tr class="cursor-default text-left text-sm">
                        <th class="w-2/12 bg-blue-500 text-white p-1">Student Number</th>
                        <th class="w-3/12 bg-blue-500 text-white p-1">Name</th>
                        <th class="w-1/12 bg-blue-500 text-white p-1">Program</th>
                        <th class="w-1/12 bg-blue-500 text-white p-1 text-xs">Year Level</th>
                        <th class="w-1/12 bg-blue-500 text-white p-1 text-xs">Acad Year</th>
                        <th class="w-1/12 bg-blue-500 text-white p-1">Term</th>
                        <th class="w-1/12 bg-blue-500 text-white p-1">Method</th>
                        <th class="w-1/12 bg-blue-500 text-white p-1">Status</th>
                        <th class="w-1/12 bg-blue-500 text-white p-1">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @if($enrollments->isNotEmpty())
                @foreach ($enrollments as $enrollment)
                    <tr class="border-b hover:bg-gray-100 cursor-pointer" >
                        <td class="border-dashed border-t border-gray-200 p-2 py-4"><strong>{{$enrollment->student_number}}</strong></td>
                        <td class="border-dashed border-t border-gray-200 p-2 py-4">{{$enrollment->first_name}} {{ substr($enrollment->middle_name, 0, 1)}}. {{$enrollment->last_name.' '.$enrollment->suffix}}</td>
                        <td class="border-dashed border-t border-gray-200 p-2 py-4">{{$enrollment->program_code}}</td>
                        <td class="border-dashed border-t border-gray-200 p-2 py-4">{{$enrollment->year_level}}</td>
                        <td class="border-dashed border-t border-gray-200 p-2 py-4">{{$enrollment->academic_year}}</td>
                        <td class="border-dashed border-t border-gray-200 p-2 py-4">{{$enrollment->term}}</td>
                        <td class="border-dashed border-t border-gray-200 p-2 py-4">{{Str::ucfirst($enrollment->enrollment_method)}}</td>
                        <td class="border-dashed border-t border-gray-200 p-2 py-4">{{Str::ucfirst($enrollment->status)}}</td>
                        <td class="border-dashed border-t border-gray-200 p-2 py-4">
                            <div class="flex flex-col space-y-2">
                                <button class="bg-blue-500 text-white rounded hover:bg-blue-600">Update</button>
                                <!-- <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Update</button> -->
                                <button @click.stop="deleteModal = true; selectedStudent = {{ $enrollment->enrollment_id }}" class="bg-red-500 text-white rounded hover:bg-red-600 transition ease-in-out duration-150">Delete</button>
                            </div>
                        </td>
                    </tr>
                @endforeach
                @else
                    <tr>
                        <td colspan="9" class="w-full mt-16 text-rose-600 text-center bg-slate-100 py-12">No Enrollment Records Available</td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection