@extends('admin.appointments-partials')
@section('content')
<div x-data="{searchTerm: '{{ $searchTerm ?? '' }}'}">
    <div class="w-full bg-white shadow-sm sm:rounded-lg min-h-[80vh] p-6">
        <h3 class="flex w-full justify-center bg-sky-950 px-4 rounded-md text-white mb-6 cursor-default">Appointment Records</h3>
        <div class="flex justify-end items-center">
            <x-search-form action="{{ route('appointments') }}" placeholder="Search Appointment" />
        </div>
        <div class="my-6">
            {{ $appointments->links() }}
        </div>
        <div>
            <div class="overflow-x-auto bg-white rounded-lg shadow relative">
                <table class="border-collapse table-auto w-full whitespace-no-wrap bg-white table-striped relative">
                    <thead >
                        <tr class="cursor-default text-left text-sm">
                            <th class="bg-blue-500 text-white p-1 pl-4 w-2/12">Student Number</th>
                            <th class="bg-blue-500 text-white p-1 w-3/12">Name</th>
                            <th class="bg-blue-500 text-white p-1 w-2/12">Course & Year</th>
                            <th class="bg-blue-500 text-white p-1 w-2/12">Service Request</th>
                            <th class="bg-blue-500 text-white p-1 w-1/12">Status</th>
                            <th class="w-2/12 bg-blue-500 text-white p-1">Date of Request</th>
                            <!-- <th class="bg-blue-500 text-white p-2 w-3/12">Actions</th> -->
                        </tr>
                    </thead>
                    <tbody>
                    @if($appointments->isNotEmpty())
                        @foreach($appointments as $appointment)
                        <tr class="border-b hover:bg-gray-100 cursor-pointer text-sm" @click="window.location.href='{{ route('appointments.manage', $appointment->user_id) }}?highlight={{ $appointment->id }}'">
                            <td class="border-dashed border-t border-gray-200 pl-4 p-1 py-2">{{$appointment->student_number}}</td>
                            <td class="border-dashed border-t border-gray-200 p-1 py-2">{{$appointment->student_first_name}} {{$appointment->student_last_name}}</td>
                            @if($appointment->program_code && $appointment->year_level)
                            <td class="border-dashed border-t border-gray-200 p-1 py-2">{{$appointment->program_code}} - {{$appointment->year_level}}</td>
                            @else
                            <td class="border-dashed border-t border-gray-200 p-1 py-2">No record</td>
                            @endif                            
                            <td class="border-dashed border-t border-gray-200 p-1 py-2">{{$appointment->service_name}}</td>
                            <td class="border-dashed border-t border-gray-200 p-1 py-2">{{ucfirst($appointment->status)}}</td>
                            <td class="border-dashed border-t border-gray-200 p-1 py-2">{{ $appointment->created_at->format('M j, Y g:i A') }}</td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="w-full mt-16 text-rose-600 text-center bg-slate-100 py-12">No Appointment Records Available</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection