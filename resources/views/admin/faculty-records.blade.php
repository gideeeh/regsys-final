@extends('admin.records')
@section('content')
<div x-data="{ 
        deleteModal: false, 
        addProfessor:false , 
        updateModal:false, 
        selectedProf: null, 
        selectedFirstName: '',  
        selectedMiddleName: '',  
        selectedLastName: '',  
        selectedSuffix: '',  
        selectedDepartment: '',  
        selectedPersonalEmail: '',  
        selectedSchoolEmail: '',  
        searchTerm: '{{ $searchTerm ?? '' }}', 
        selectedStudent: null }" 
    @keydown.escape.window="addProfessor=false;updateModal=false">
    <h3 class="flex w-full justify-center bg-sky-950 px-4 rounded-md text-white mb-6 cursor-default">Faculty Records</h3>
    @if(Auth::check() && Auth::user()->role === 'admin')
    <div class="flex justify-between items-center h-10">
        <button @click="addProfessor = true" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition ease-in-out duration-150">Create Record</button>
        <x-search-form action="{{ route('faculty-records') }}" placeholder="Search Faculty" />
    @else
    <div class="flex justify-end items-center h-10">
        <x-search-form action="{{ route('dean-access.faculty-records') }}" placeholder="Search Faculty" />
        @endif
    </div>
    <div class="mt-6">
        {{ $professors->links() }}
    </div>
    <div class="py-4">
        <div class="overflow-x-auto bg-white rounded-lg shadow overflow-y-auto relative">   
            <table class="border-collapse table-auto w-full whitespace-no-wrap bg-white table-striped relative">
                <thead >
                    <tr class="cursor-default text-left text-sm">
                        <th class="bg-blue-500 text-white p-1 pl-4 w-3/12">Name</th>
                        <th class="bg-blue-500 text-white p-1 w-4/12">Department</th>
                        <th class="bg-blue-500 text-center text-white p-1 pr-4 w-1/12">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($professors as $professor)
                    @if(Auth::check() && Auth::user()->role === 'admin')
                    <tr class="border-b hover:bg-gray-100 cursor-pointer text-sm" @click="window.location.href='{{ route('faculty-records.show', $professor->prof_id) }}'">
                    @else
                    <tr class="border-b hover:bg-gray-100 cursor-pointer text-sm" @click="window.location.href='{{ route('dean-access.faculty-records.show', $professor->prof_id) }}'">
                    @endif
                        @if($professor->middle_name)
                        <td class="border-dashed border-t border-gray-200 p-1 pl-4 py-2">{{$professor->first_name}} {{ substr($professor->middle_name, 0, 1)}}.  {{$professor->last_name.' '.$professor->suffix}}</td>
                        @else
                        <td class="border-dashed border-t border-gray-200 p-1 pl-4 py-2">{{$professor->first_name}} {{$professor->last_name.' '.$professor->suffix}}</td>
                        @endif
                        <td class="border-dashed border-t border-gray-200 p-1 py-2">{{$professor->dept_name ?? 'Not Available' }}</td>
                        <td class="border-dashed border-t border-gray-200 p-1 py-2">
                            <div class="flex justify-center gap-1">
                                <button 
                                    @click.stop="updateModal=true; 
                                    selectedProf = {{$professor->prof_id}};
                                    selectedFirstName = '{{ $professor->first_name }}'; 
                                    selectedMiddleName = '{{ $professor->middle_name }}'; 
                                    selectedLastName = '{{ $professor->last_name }}'; 
                                    selectedSuffix = '{{ $professor->suffix }}'; 
                                    selectedDepartment = '{{ $professor->dept_id }}'; 
                                    selectedPersonalEmail = '{{ $professor->personal_email }}'; 
                                    selectedSchoolEmail = '{{ $professor->school_email }}';" 
                                    class="bg-blue-500 text-sm text-white p-1 rounded hover:bg-blue-600">Update</button>
                                <button @click.stop="deleteModal = true; selectedProf = {{$professor->prof_id}}" class="bg-red-500 text-sm text-white p-1 rounded hover:bg-red-600 transition ease-in-out duration-150">Delete</button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <!-- Add Professor Modal -->
    <div x-cloak x-show="addProfessor" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
        <div class="modal-content bg-white p-8 rounded-lg shadow-lg overflow-auto max-w-lg w-full min-h-[85vh] max-h-[85vh]">
            <h3 class="text-lg font-bold mb-4">Create Faculty Record</h3>   
            <form action="{{route('faculty-records.store')}}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                    <input type="text" id="first_name" name="first_name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="middle_name" class="block text-sm font-medium text-gray-700">Middle Name:</label>
                    <input type="text" id="middle_name" name="middle_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>    
                    <label for="suffix" class="block text-sm font-medium text-gray-700">Suffix:</label>
                    <select name="suffix" id="suffix" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="" selected></option>
                        <option value="Jr.">Jr.</option>
                        <option value="Sr.">Sr.</option>
                        <option value="II">II</option>
                        <option value="III">III</option>
                        <option value="IV">IV</option>
                    </select>
                </div>
                <div>
                    <label for="department" class="block text-sm font-medium text-gray-700">Department:</label>
                    <select id="department" name="department" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Select Department</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->dept_id }}">{{ $department->dept_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="personal_email" class="block text-sm font-medium text-gray-700">Personal Email Address:</label>
                    <input type="email" id="personal_email" name="personal_email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="school_email" class="block text-sm font-medium text-gray-700">School Email Address:</label>
                    <input type="email" id="school_email" name="school_email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div class="flex justify-end space-x-4">
                    <button type="button" @click="addProfessor = false" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">Cancel</button>
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition ease-in-out duration-150">Create Record</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Update Modal -->
    <div x-cloak x-show="updateModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
        <div class="modal-content bg-white p-8 rounded-lg shadow-lg overflow-auto max-w-lg w-full min-h-[85vh] max-h-[85vh]">
            <h3 class="text-lg font-bold mb-4">Update Faculty Record</h3>
            <form :action="'/admin/faculty-records/update-faculty-record/' + selectedProf" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')
                <input type="hidden" name="id" x-model="selectedProf">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                    <input type="text" name="first_name" x-model="selectedFirstName" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="middle_name" class="block text-sm font-medium text-gray-700">Middle Name:</label>
                    <input type="text" name="middle_name" x-model="selectedMiddleName" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name:</label>
                    <input type="text" name="last_name" x-model="selectedLastName" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>    
                    <label for="suffix" class="block text-sm font-medium text-gray-700">Suffix:</label>
                    <select name="suffix" x-model="selectedSuffix" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="" selected></option>
                        <option value="Jr.">Jr.</option>
                        <option value="Sr.">Sr.</option>
                        <option value="II">II</option>
                        <option value="III">III</option>
                        <option value="IV">IV</option>
                    </select>
                </div>
                <div>
                    <label for="department" class="block text-sm font-medium text-gray-700">Department:</label>
                    <select name="department" x-model="selectedDepartment" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Select Department</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->dept_id }}">{{ $department->dept_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="personal_email" class="block text-sm font-medium text-gray-700">Personal Email Address:</label>
                    <input type="email" name="personal_email" x-model="selectedPersonalEmail" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="school_email" class="block text-sm font-medium text-gray-700">School Email Address:</label>
                    <input type="email" name="school_email" x-model="selectedSchoolEmail" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div class="flex justify-end space-x-4">
                    <button type="button" @click="updateModal = false" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">Cancel</button>
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition ease-in-out duration-150">Update Record</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Delete Modal -->
    <div x-cloak x-show="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
        <div class="modal-content bg-white p-8 rounded-lg shadow-lg overflow-auto max-w-md w-full">
            <h3 class="text-lg font-bold mb-4">Confirm Deletion</h3>
            <p>Are you sure you want to delete this record?</p>
            <div class="flex justify-end space-x-4 mt-4">
                <button @click="deleteModal = false" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">Cancel</button>
                <form :action="'/admin/faculty-records/delete-faculty-record/' + selectedProf" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition ease-in-out duration-150">Delete Record</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection