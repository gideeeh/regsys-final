@extends('admin.records')
@section('content')
<div x-data="{ deleteModal: false, searchTerm: '{{ $searchTerm ?? '' }}', selectedStudent: null }">
    <div class="flex justify-between items-center space-x-4">
        <a href="{{ route('student-records') }}" class="font-semibold text-xl text-gray-800 leading-tight no-underline hover:underline">
            <span class="text-2xl font-semibold mb-4">Student Records</span>
        </a>
        <x-search-form action="{{ route('student-records') }}" placeholder="Search Student" />
    </div>
    <div class="mt-6">
        {{ $students->links() }}
    </div>
    <div class="py-4">
        <div class="overflow-x-auto bg-white rounded-lg shadow overflow-y-auto relative" style="min-height: 405px;">   
            <table class="border-collapse table-auto w-full whitespace-no-wrap bg-white table-striped relative">
                <thead >
                    <tr class="cursor-default text-left text-sm">
                        <th class="bg-blue-500 text-white p-1 w-2/12">Student Number</th>
                        <th class="bg-blue-500 text-white p-1 w-3/12">Name</th>
                        <th class="bg-blue-500 text-white p-1 w-3/12">Program</th>
                        <th class="bg-blue-500 text-white p-1 w-2/12">Year Level</th>
                        <th class="bg-blue-500 text-white p-1 w-1/12 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($students as $student)
                    <tr class="text-left text-sm border-b hover:bg-gray-100 cursor-pointer" x-data="{}" @click="window.location.href='{{ route('student-records.show', $student->student_id) }}'">
                        <td class="border-dashed border-t border-gray-200 p-1"><strong>{{$student->student_number}}</strong></td>
                        @if($student->middle_name)
                        <td class="border-dashed border-t border-gray-200 p-1">{{$student->first_name}} {{ substr($student->middle_name, 0, 1)}}.  {{$student->last_name.' '.$student->suffix}}</td>
                        @else
                        <td class="border-dashed border-t border-gray-200 p-1">{{$student->first_name}} {{$student->last_name.' '.$student->suffix}}</td>
                        @endif
                        <td class="border-dashed border-t border-gray-200 p-1">{{$student->program_code ?? 'Not Available' }}</td>
                        <td class="border-dashed border-t border-gray-200 p-1">{{$student->year_level ?? '-'}}</td>
                        <td class="border-dashed border-t border-gray-200 p-1">
                            <div class="flex justify-between gap-1">
                                <button @click.stop="window.location.href='{{ route('student.edit', $student->student_id) }}'" class="bg-blue-500 text-sm text-white p-1 rounded hover:bg-blue-600">Update</button>
                                <button @click.stop="deleteModal = true; selectedStudent = {{ $student->student_id }}" class="bg-red-500 text-sm text-white p-1 rounded hover:bg-red-600 transition ease-in-out duration-150">Delete</button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <!-- Delete Modal -->
    <div x-cloak x-show="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
        <div class="modal-content bg-white p-8 rounded-lg shadow-lg overflow-auto max-w-md w-full">
            <h3 class="text-lg font-bold mb-4">Confirm Deletion</h3>
            <p>Are you sure you want to delete this Student?</p>
            <div class="flex justify-end space-x-4 mt-4">
                <button @click="deleteModal = false" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">Cancel</button>
                <form :action="'/student/student-records/delete_student/' + selectedStudent" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition ease-in-out duration-150">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection