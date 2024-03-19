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
            {{ $students->links() }}
        </div>
        <div class="py-4">
            <div class="overflow-x-auto bg-white rounded-lg shadow overflow-y-auto relative">   
                <table class="border-collapse table-auto w-full whitespace-no-wrap bg-white table-striped relative">
                    <thead >
                        <tr class="cursor-default text-left text-sm">
                            <th class="bg-blue-500 text-white p-1 pl-4 w-2/12">Student Number</th>
                            <th class="bg-blue-500 text-white p-1 w-3/12">Name</th>
                            <th class="bg-blue-500 text-white p-1 w-3/12">Program</th>
                            <th class="bg-blue-500 text-white p-1 w-2/12">Year Level</th>
                            <!-- <th class="bg-blue-500 text-white p-1 pr-4 w-1/12 text-center">Actions</th> -->
                        </tr>
                    </thead>
                    <tbody>
                    @if($students->isNotEmpty())
                        @foreach ($students as $student)
                        <tr class="text-left text-sm border-b hover:bg-gray-100 cursor-pointer" x-data="{}" @click="window.location.href='{{ route('enrollment-records.show', $student->student_id) }}'">
                            <td class="border-dashed border-t border-gray-200 py-2 pl-4"><strong>{{$student->student_number}}</strong></td>
                            <td class="border-dashed border-t border-gray-200 p-1 py-2">
                                {{$student->first_name}} 
                                {{ $student->middle_name ? substr($student->middle_name, 0, 1) . '. ' : '' }} 
                                {{$student->last_name}} {{$student->suffix ?? ''}}
                            </td>
                            @if(!$student->program_major)
                            <td class="border-dashed border-t border-gray-200 p-1 py-2">{{$student->program_code ?? 'Not Available' }}</td>
                            @else
                            <td class="border-dashed border-t border-gray-200 p-1 py-2">{{$student->program_code.' Major in '. ucfirst($student->program_major)  ?? 'Not Available' }}</td>
                            @endif
                            <td class="border-dashed border-t border-gray-200 p-1 py-2">{{$student->year_level ?? '-'}}</td>
                            <!-- <td class="border-dashed border-t border-gray-200 pr-4 py-2">
                                <div class="flex justify-between gap-1">
                                    <button onclick="event.stopPropagation(); window.open('{{ route('student.edit', $student->student_id) }}', '_blank');" class="bg-blue-500 text-sm text-white p-1 rounded hover:bg-blue-600">Update</button>
                                    <button @click.stop="deleteModal = true; selectedStudent = {{ $student->student_id }}" class="bg-red-500 text-sm text-white p-1 rounded hover:bg-red-600 transition ease-in-out duration-150">Delete</button>
                                </div>
                            </td> -->
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="9" class="w-full mt-16 text-rose-600 text-center bg-slate-100 py-12">No Student Records Available</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection