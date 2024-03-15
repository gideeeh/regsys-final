@extends('admin.functions')

@section('content')
    <div x-data="{ 
        addDept: false, 
        addDeptHead: false, 
        updateDeptModal: false, 
        updateDeptHeadModal: false, 
        deleteDeptModal: false, 
        deleteDeptHeadModal: false, 
        selectedDept: null, 
        selectedDeptHead: null, 
        selectedDeptName: '', 
        selectedFirstName: '',  
        selectedMiddleName: '',  
        selectedLastName: '',  
        selectedSuffix: '',  
        selectedDepartment: '',  
        selectedPersonalEmail: '',  
        selectedSchoolEmail: ''}"
        @keydown.escape.window="
            addDept = false;
            addDeptHead = false;
            updateDeptModal= false;
            updateDeptHeadModal= false;
            deleteDeptModal= false;
            deleteDeptHeadModal= false"
        >
        <h3 class="flex w-full justify-center bg-sky-950 px-4 rounded-md text-white mb-6 cursor-default">Departments Management</h3>
        <button @click="addDept = true" class="mr-2 bg-green-500 text-sm text-white px-3 py-2 rounded hover:bg-green-600 transition ease-in-out duration-150">Create Dept Record</button>
        <button @click="addDeptHead = true" class="bg-stone-500 text-sm text-white px-3 py-2 rounded hover:bg-stone-600 transition ease-in-out duration-150">Create Dept Head Record</button>
        
        <div class="mt-6">
            {{ $departmentRecords->links() }}
        </div>
        <div class="py-4">
            <div class="overflow-x-auto bg-white rounded-lg shadow overflow-y-auto relative">   
                <table class="border-collapse table-auto w-full whitespace-no-wrap bg-white table-striped relative">
                    <thead >
                        <tr class="cursor-default text-left text-sm">
                            <th class="bg-blue-500 text-white p-1 pl-4 w-3/12">Department</th>
                            <th class="bg-blue-500 text-white p-1 w-4/12">Department Head</th>
                            <th class="bg-blue-500 text-center text-white p-1 pr-4 w-3/12">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($departmentsWithHeads as $departmentsWithHeads)
                        <tr class="border-b hover:bg-gray-100 cursor-pointer text-sm">
                            <td class="border-dashed border-t border-gray-200 pl-4 p-1 py-2">{{ $departmentsWithHeads->dept_name ?? 'Not Available' }}</td>
                            @if(!$departmentsWithHeads->dept_head_id)
                            <td class="border-dashed border-t border-gray-200 p-1 py-2">No assigned dept head</td>
                            @else
                            <td class="border-dashed border-t border-gray-200 p-1 py-2">
                                {{ $departmentsWithHeads->first_name }}
                                {{ $departmentsWithHeads->middle_name ? substr($departmentsWithHeads->middle_name, 0, 1) . '.' : '' }}
                                {{ $departmentsWithHeads->last_name }}
                                {{ $departmentsWithHeads->suffix }}
                            </td>
                            @endif
                            <td class="border-dashed border-t border-gray-200 p-1 py-2">
                                <div class="flex justify-center gap-1">
                                    <button
                                        @click.stop="updateDeptModal=true;
                                        selectedDept = {{$departmentsWithHeads->dept_id}};
                                        selectedDeptName= '{{$departmentsWithHeads->dept_name}}';" 
                                        class="bg-blue-500 text-xs text-white p-1 rounded hover:bg-blue-600">Update Dept</button>
                                    <button
                                        @click.stop="updateDeptHeadModal=true;
                                        selectedDeptHead = {{$departmentsWithHeads->dept_head_id}};
                                        selectedFirstName = '{{ $departmentsWithHeads->first_name }}'; 
                                        selectedMiddleName = '{{ $departmentsWithHeads->middle_name }}'; 
                                        selectedLastName = '{{ $departmentsWithHeads->last_name }}'; 
                                        selectedSuffix = '{{ $departmentsWithHeads->suffix }}'; 
                                        selectedDepartment = '{{ $departmentsWithHeads->dept_id }}'; 
                                        selectedPersonalEmail = '{{ $departmentsWithHeads->personal_email }}'; 
                                        selectedSchoolEmail = '{{ $departmentsWithHeads->school_email }}';" 
                                        class="bg-stone-500 text-xs text-white p-1 rounded hover:bg-stone-600">Manage Head</button>
                                    <button @click.stop="deleteDeptModal = true; selectedDept = {{$departmentsWithHeads->dept_id}}" class="bg-red-500 text-sm text-white p-1 rounded hover:bg-red-600 transition ease-in-out duration-150">Delete</button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Add Department Modal -->
        <div x-cloak x-show="addDept" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
            <div class="modal-content bg-white p-8 rounded-lg shadow-lg overflow-auto max-w-lg w-full min-h-[25vh] max-h-[25vh]">
                <h3 class="text-lg font-bold mb-4">Create Department Record</h3>   
                <form action="{{route('departments.store')}}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="dept_name" class="block text-sm font-medium text-gray-700">Department Name</label>
                        <input type="text" id="dept_name" name="dept_name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div class="flex justify-end space-x-4">
                        <button type="button" @click="addDept = false" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">Cancel</button>
                        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition ease-in-out duration-150">Create Record</button>
                    </div>
                </form>
            </div>
        </div>       
        <!-- Add Department Head Modal -->
        <div x-cloak x-show="addDeptHead" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
            <div class="modal-content bg-white p-8 rounded-lg shadow-lg overflow-auto max-w-lg w-full min-h-[85vh] max-h-[85vh]">
                <h3 class="text-lg font-bold mb-4">Create Department Record</h3>   
                <form action="{{route('deptHead.store')}}" method="POST" class="space-y-4">
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
        <!-- Update Department Modal -->
        <div x-cloak x-show="updateDeptModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
            <div class="modal-content bg-white p-8 rounded-lg shadow-lg overflow-auto max-w-lg w-full min-h-[25vh] max-h-[25vh]">
                <h3 class="text-lg font-bold mb-4">Update Department Record</h3>
                <form :action="'/admin/functions/program-course-management/departments/update-dept-record/' + selectedDept" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="id" x-model="selectedDept">
                    <div>
                        <label for="dept_name" class="block text-sm font-medium text-gray-700">Department Name</label>
                        <input type="text" x-model="selectedDeptName" name="dept_name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div class="flex justify-end space-x-4">
                        <button type="button" @click="updateDeptModal = false" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">Cancel</button>
                        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition ease-in-out duration-150">Update Record</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Update Department Head Modal -->
        <div x-cloak x-show="updateDeptHeadModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
            <div class="modal-content bg-white p-8 rounded-lg shadow-lg overflow-auto max-w-lg w-full min-h-[85vh] max-h-[85vh]">
                <h3 class="text-lg font-bold mb-4">Update Dept Head Record</h3>   
                <form :action="'/admin/functions/program-course-management/departments/update-deptHead-record/' + selectedDeptHead" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="id" x-model="selectedDeptHead">
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
                    <div class="flex justify-between space-x-4 items-center">
                        <span @click="updateDeptHeadModal=false;deleteDeptHeadModal=true; " class="underline text-sm cursor-pointer text-red-400 hover:text-red-700">Delete Record</span>
                        <div>
                            <button type="button" @click="updateDeptHeadModal = false" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">Cancel</button>
                            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition ease-in-out duration-150">Update Record</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>  
        <!-- Delete Department Modal -->
        <div x-cloak x-show="deleteDeptModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
            <div class="modal-content bg-white p-8 rounded-lg shadow-lg overflow-auto max-w-md w-full">
                <h3 class="text-lg font-bold mb-4">Confirm Department Deletion</h3>
                <p>Are you sure you want to delete this Department record?</p>
                <div class="flex justify-end space-x-4 mt-4">
                    <button @click="deleteDeptModal = false" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">Cancel</button>
                    <form :action="'/admin/functions/program-course-management/departments/delete-dept-record/' + selectedDept" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition ease-in-out duration-150">Delete Record</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- Delete Department Modal -->
        <div x-cloak x-show="deleteDeptHeadModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
            <div class="modal-content bg-white p-8 rounded-lg shadow-lg overflow-auto max-w-md w-full">
                <h3 class="text-lg font-bold mb-4">Confirm Dept HeadDeletion</h3>
                <p>Are you sure you want to delete this Department Head record?</p>
                <div class="flex justify-end space-x-4 mt-4">
                    <button @click="updateDeptHeadModal=true;deleteDeptHeadModal=false" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">Cancel</button>
                    <form :action="'/admin/functions/program-course-management/departments/delete-deptHead-record/' + selectedDeptHead" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition ease-in-out duration-150">Delete Record</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
