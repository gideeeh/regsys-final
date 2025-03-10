@extends('admin.functions')

@section('content')
    <div x-data="{ 
        showModal: false, 
        updateModal: false, 
        deleteModal: false, 
        selectedProgram: null, 
        selectedProgramCode: '', 
        selectedProgramName: '', 
        selectedProgramMajor: '', 
        selectedProgramDesc: '', 
        selectedDegreeType: '', 
        selectedDepartment: '', 
        selectedProgramCoordinator: '', 
        selectedTotalUnits: 0 }"
        @keydown.escape.window="showModal = false;updateModal= false;deleteModal= false">
        <h3 class="flex w-full justify-center bg-sky-950 px-4 rounded-md text-white mb-6 cursor-default">Program Management</h3>
        <button @click="showModal = true" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition ease-in-out duration-150">+ Add Program</button>
        
        <div class="mt-6">
            {{ $programs->links() }}
        </div>
        <div class="py-4">
            <div class="overflow-x-auto bg-white rounded-lg shadow relative">
                <!-- Table -->
                <table class="border-collapse table-auto w-full whitespace-no-wrap bg-white table-striped relative">
                    <thead>
                        <tr class="text-left text-sm">
                            <th class="w-1/12 bg-blue-500 text-white p-1 pl-4">Code</th>
                            <th class="bg-blue-500 text-white p-1 py-2">Name</th>
                            <th class="bg-blue-500 text-white p-1">Major</th>
                            <th class="bg-blue-500 text-white p-1">Degree Type</th>
                            <th class="bg-blue-500 text-white p-1">Department</th>
                            <th class="bg-blue-500 text-white p-1">Coordinator</th>
                            <th class="w-1/12 bg-blue-500 text-white p-1 text-center">Units</th>
                            <th class="w-1/12 bg-blue-500 text-white p-1 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($programs as $program)
                        @if(Auth::check() && Auth::user()->role === 'admin')
                        <tr class="text-sm border-b hover:bg-gray-100 cursor-pointer" x-data="{}" @click="window.location.href='{{ route('program-list.show', $program->program_id) }}'">
                        @elseif((Auth::check() && Auth::user()->role === 'dean'))
                        <tr class="text-sm border-b hover:bg-gray-100 cursor-pointer" x-data="{}" @click="window.location.href='{{ route('dean-access.program-list.show', $program->program_id) }}'">
                        @endif
                            <td class="border-dashed border-t border-gray-200 py-2 pl-4">{{ $program->program_code }}</td>
                            <td class="border-dashed border-t border-gray-200 p-1">{{ $program->program_name }}</td>
                            <td class="border-dashed border-t border-gray-200 p-1">{{ $program->program_major }}</td>
                            <td class="border-dashed border-t border-gray-200 p-1">{{ $program->degree_type }}</td>
                            <td class="border-dashed border-t border-gray-200 p-1">{{ $program->dept_name ?? '-' }}</td>
                            <td class="border-dashed border-t border-gray-200 p-1">{{ $program->program_coordinator ?? '-'}}</td>
                            <td class="border-dashed border-t border-gray-200 p-1 text-center">{{ $program->total_units ?? '-'}}</td>
                            <td class="border-dashed border-t border-gray-200 p-1">
                                <div class="flex justify-between gap-1">
                                    <button 
                                        @click.stop="updateModal = true; 
                                                selectedProgram = {{ $program->program_id }}; 
                                                selectedProgramCode = '{{ $program->program_code }}'; 
                                                selectedProgramName = '{{ $program->program_name }}';
                                                selectedProgramMajor = '{{ $program->program_major }}'; 
                                                selectedDegreeType = '{{ $program->degree_type }}';
                                                selectedProgramDesc = '{{ $program->program_desc }}';
                                                selectedDepartment = '{{ $program->dept_id }}';
                                                selectedProgramCoordinator = '{{ $program->program_coordinator }}';" 
                                        class="bg-blue-500 text-white text-xs rounded hover:bg-blue-600 transition ease-in-out p-1 duration-150">Update</button>
                                    <button @click.stop="deleteModal = true; selectedProgram = {{ $program->program_id }}" class="text-xs bg-red-500 text-white rounded p-1 hover:bg-red-600 transition ease-in-out duration-150">Delete</button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <!-- Add Program Modal -->
                <div x-cloak x-show="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
                    <div class="modal-content bg-white p-8 rounded-lg shadow-lg overflow-auto max-w-lg w-full min-h-[85vh] max-h-[85vh]">
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
                                    <option value="Tesda">Tesda</option>
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
                    <div class="modal-content bg-white p-8 rounded-lg shadow-lg overflow-auto max-w-lg w-full min-h-[85vh] max-h-[85vh]">
                        <h3 class="text-lg font-bold mb-4">Update Program</h3>
                        <form :action="'/admin/functions/program-course-management/program_list/update-program/' + selectedProgram" method="POST" class="space-y-4">
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
                                <label for="program_major" class="block text-sm font-medium text-gray-700">Program Major:</label>
                                <input type="text" name="program_major" x-model="selectedProgramMajor" placeholder="Leave blank if n/a" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <!-- Enum supposedly but insufficient info from registrar so not hardcoded option selections for major -->
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
                                    <option value="Tesda">Tesda</option>
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
                            <div class="mt-4 flex justify-end space-x-4">
                                <button type="button" @click="updateModal = false" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">Cancel</button>
                                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition ease-in-out duration-150">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>        
    </div>
@endsection
