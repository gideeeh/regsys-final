@extends('admin.functions')

@section('content')
    <div x-data="{ 
        showModal: false, 
        updateModal: false, 
        deleteModal: false, 
        selectedProgram: null, 
        selectedProgramCode: '', 
        selectedProgramName: '', 
        selectedProgramDesc: '', 
        selectedDegreeType: '', 
        selectedDepartment: '', 
        selectedProgramCoordinator: '', 
        selectedTotalUnits: 0 }"
        @keydown.escape.window="showModal = false;updateModal= false;deleteModal= false">
        <h2 class="text-2xl font-semibold mb-4">Program Management</h2>
        <button @click="showModal = true" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition ease-in-out duration-150">+ Add Program</button>
        
        <!-- Table -->
        <div class="py-4">
            <div class="overflow-x-auto bg-white rounded-lg shadow overflow-y-scroll nice-scroll relative">
                <table class="border-collapse table-auto w-full whitespace-no-wrap bg-white table-striped relative">
                    <thead>
                        <tr class="text-left text-sm">
                            <th class="bg-blue-500 text-white p-2">Code</th>
                            <th class="bg-blue-500 text-white p-2">Name</th>
                            <th class="bg-blue-500 text-white p-2">Major</th>
                            <th class="bg-blue-500 text-white p-2">Degree Type</th>
                            <th class="bg-blue-500 text-white p-2">Department</th>
                            <th class="bg-blue-500 text-white p-2">Coordinator</th>
                            <th class="bg-blue-500 text-white p-2">Total Units</th>
                            <th class="bg-blue-500 text-white p-2 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($programs as $program)
                        <tr class="text-sm border-b hover:bg-gray-100 cursor-pointer" x-data="{}" @click="window.location.href='{{ route('program-list.show', $program->program_id) }}'">
                            <td class="border-dashed border-t border-gray-200 p-1">{{ $program->program_code }}</td>
                            <td class="border-dashed border-t border-gray-200 p-1">{{ $program->program_name }}</td>
                            <td class="border-dashed border-t border-gray-200 p-1">{{ $program->program_major }}</td>
                            <td class="border-dashed border-t border-gray-200 p-1">{{ $program->degree_type }}</td>
                            <td class="border-dashed border-t border-gray-200 p-1">{{ $program->department->dept_name ?? '-' }}</td>
                            <td class="border-dashed border-t border-gray-200 p-1">{{ $program->program_coordinator ?? '-'}}</td>
                            <td class="border-dashed border-t border-gray-200 p-1">{{ $program->total_units ?? '-'}}</td>
                            <td class="border-dashed border-t border-gray-200 p-1">
                                <div class="flex flex-col justify-between gap-2">
                                    <button 
                                        @click.stop="updateModal = true; 
                                                selectedProgram = {{ $program->program_id }}; 
                                                selectedProgramCode = '{{ $program->program_code }}'; 
                                                selectedProgramName = '{{ $program->program_name }}'; 
                                                selectedDegreeType = '{{ $program->degree_type }}';
                                                selectedDepartment = '{{ $program->dept_id }}';
                                                selectedProgramCoordinator = '{{ $program->program_coordinator }}';" 
                                        class="bg-blue-500 text-white text-xs rounded hover:bg-blue-600 transition ease-in-out duration-150">Update</button>
                                    <button @click.stop="deleteModal = true; selectedProgram = {{ $program->program_id }}" class="text-xs bg-red-500 text-white rounded hover:bg-red-600 transition ease-in-out duration-150">Delete</button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <!-- Add Program Modal -->
                <div x-cloak x-show="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
                    <div class="modal-content bg-white p-8 rounded-lg shadow-lg overflow-auto max-w-md w-full max-h-[80vh]">
                        <h3 class="text-lg font-bold mb-4">Add New Program</h3>
                        
                        <form action="{{ route('program-lists-new-program') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label for="program_code" class="block text-sm font-medium text-gray-700">Program Code:</label>
                                <input type="text" id="program_code" name="program_code" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                            <div>
                                <label for="program_name" class="block text-sm font-medium text-gray-700">Program Name:</label>
                                <input type="text" id="program_name" name="program_name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                            <div>
                                <label for="program_major" class="block text-sm font-medium text-gray-700">Program Major:</label>
                                <input type="text" id="program_major" name="program_major" placeholder="Leave blank if n/a" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <!-- Enum supposedly but insufficient info from registrar so not hardcoded option selections for major -->
                            </div>
                            <div>
                                <label for="program_description" class="block text-sm font-medium text-gray-700">Program Description:</label>
                                <textarea id="program_description" name="program_description" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                            </div>
                            <div>
                                <label for="degree_type" class="block text-sm font-medium text-gray-700">Degree Type:</label>
                                <select id="degree_type" name="degree_type" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="Bachelor">Bachelor</option>
                                    <option value="Associate">Associate</option>
                                    <option value="Graduate">Graduate</option>
                                    <option value="Graduate">Tesda</option>
                                </select>
                            </div>
                            <div>
                                <label for="department" class="block text-sm font-medium text-gray-700">Department:</label>
                                <select id="department" name="department" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                    <option value="">Select Department</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->dept_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="program_coordinator" class="block text-sm font-medium text-gray-700">Program Coordinator:</label>
                                <input type="text" id="program_coordinator" name="program_coordinator" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                            <div class="flex justify-end space-x-4">
                                <button type="button" @click="showModal = false" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">Cancel</button>
                                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition ease-in-out duration-150">Save Program</button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- Delete Modal -->
                <div x-cloak x-show="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
                    <div class="modal-content bg-white p-8 rounded-lg shadow-lg overflow-auto max-w-md w-full">
                        <h3 class="text-lg font-bold mb-4">Confirm Deletion</h3>
                        <p>Are you sure you want to delete this program?</p>
                        <div class="flex justify-end space-x-4 mt-4">
                            <button @click="deleteModal = false" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">Cancel</button>
                            <form :action="'/admin/functions/program-course-management/program_list/delete-program/' + selectedProgram" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition ease-in-out duration-150">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Update Modal -->
                <div x-cloak x-show="updateModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
                    <div class="modal-content bg-white p-8 rounded-lg shadow-lg overflow-auto max-w-md w-full">
                        <h3 class="text-lg font-bold mb-4">Update Program</h3>
                        <form :action="'/admin/functions/program-course-management/program_list/update-program/' + selectedProgram" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="id" x-model="selectedProgram">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Program Code:</label>
                                <input type="text" name="program_code" x-model="selectedProgramCode" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Program Name:</label>
                                <input type="text" name="program_name" x-model="selectedProgramName" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            </div>
                            <div>
                                <label for="program_desc" class="block text-sm font-medium text-gray-700">Program Description:</label>
                                <textarea name="program_desc" x-model="selectedProgramDesc" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                            </div>
                            <div>
                                <label for="degree_type" class="block text-sm font-medium text-gray-700">Degree Type:</label>
                                <select name="degree_type" x-model="selectedDegreeType" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="Bachelor">Bachelor</option>
                                    <option value="Associate">Associate</option>
                                    <option value="Graduate">Graduate</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="department" class="block text-sm font-medium text-gray-700">Department:</label>
                                <select x-model="selectedDepartment" name="department" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">Select Department</option>
                                    @foreach ($departments as $department)
                                    <option value="{{ $department->dept_id }}">{{ $department->dept_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="program_coordinator" class="block text-sm font-medium text-gray-700">Program Coordinator:</label>
                                <input type="text" x-model="selectedProgramCoordinator" name="program_coordinator" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                            <div class="mt-4">
                                <button type="button" @click="updateModal = false" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">Cancel</button>
                                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition ease-in-out duration-150">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>        
    </div>
@endsection
