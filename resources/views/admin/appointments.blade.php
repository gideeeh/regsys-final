@extends('admin.appointments-partials')
@section('content')
<div x-data="{searchTerm: '{{ $searchTerm ?? '' }}'}">
    <div class="w-full bg-white shadow-sm sm:rounded-lg min-h-[80vh] p-6">
        <h3 class="flex w-full justify-center bg-sky-950 px-4 rounded-md text-white mb-6 border-b-4 border-amber-300">Appointments Management</h3>
        <div class="flex justify-end items-center">
            <x-search-form action="{{ route('appointments') }}" placeholder="Search Appointment" />
        </div>
        <div class="mt-6">
            {{ $appointments->links() }}
        </div>
        <div>
            <table class="border-collapse table-auto w-full whitespace-no-wrap bg-white table-striped relative">
                <thead >
                    <tr class="cursor-default">
                        <th class="bg-blue-500 text-white p-2 w-3/12">Name</th>
                        <th class="bg-blue-500 text-white p-2 w-1/12">Course</th>
                        <th class="bg-blue-500 text-white p-2 w-1/12">Year Level</th>
                        <th class="bg-blue-500 text-white p-2 w-2/12">Service Request</th>
                        <th class="bg-blue-500 text-white p-2 w-2/12">Date of Request</th>
                        <!-- <th class="bg-blue-500 text-white p-2 w-3/12">Actions</th> -->
                    </tr>
                </thead>
                <tbody>
                    @foreach($appointments as $appointment)
                    <tr class="border-b hover:bg-gray-100 cursor-pointer">
                        <td class="border-dashed border-t border-gray-200 p-1 py-4">{{$appointment->student_first_name}} {{$appointment->student_last_name}}</td>
                        <td class="border-dashed border-t border-gray-200 p-1 py-4">{{$appointment->program_name}}</td>
                        <td class="border-dashed border-t border-gray-200 p-1 py-4">{{$appointment->year_level}}</td>
                        <td class="border-dashed border-t border-gray-200 p-1 py-4">{{$appointment->service_name}}</td>
                        <td class="border-dashed border-t border-gray-200 p-1 py-4">{{ $appointment->created_at->format('M j, Y g:i A') }}</td>
                        <!-- <td class="border-dashed border-t border-gray-200 p-2 py-4">
                            <div class="flex justify-start space-x-4">
                                <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition ease-in-out duration-150">Update</button>
                                <button class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition ease-in-out duration-150">Delete</button>
                            </div>
                        </td> -->
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection