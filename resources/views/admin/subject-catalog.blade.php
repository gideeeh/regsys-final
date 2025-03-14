@extends('admin.functions')
@section('content')
    <div x-data="{
        searchPreReq1: '', 
        filteredSubjects: [], 
        searchTerm: '{{ $searchTerm ?? '' }}',
        showModal:false,
        showUpdateModal:false, 
        showDeleteModal:false,
        selectedId:null,
        selectedSubjectCode:'', 
        selectedSubjectName:'',
        selectedSubjectDescription:'', 
        selectedSubjectUnitsLec:'',
        selectedSubjectUnitsLab:'',
        selectedPreReq1:'',
        selectedPreReq2:''}" 
        @keydown.escape.window="showModal = false;showUpdateModal=false">
        <x-alert-message />
        <h3 class="flex w-full justify-center bg-sky-950 px-4 rounded-md text-white mb-6 cursor-default">Subjects Catalog</h3>
        @if(Auth::check() && Auth::user()->role === 'admin')
        <div class="flex justify-between mb-6">
            <button @click="showModal = true" class="bg-green-500 text-white text-sm p-2 rounded hover:bg-green-600 transition ease-in-out duration-150">+ Add Subject</button>
            <!-- <a href="{{ route('subject-catalog') }}" class="font-semibold text-xl text-gray-800 leading-tight no-underline hover:underline">
                <span class="text-2xl font-semibold mb-4">Subjects Catalog</span>
            </a> -->
            <x-search-form action="{{ route('subject-catalog') }}" placeholder="Search Subject" />
        </div>
        @else 
        <div class="flex justify-end mb-6">
            <x-search-form action="{{ route('dean-access.subject-catalog') }}" placeholder="Search Subject" />
        </div>
        @endif
        <!-- Subjects Table -->
        <div>
            {{ $subjects->links() }}
        </div>
        <div class="py-4">
            <div class="overflow-x-auto bg-white rounded-lg shadow overflow-y-auto relative">
                <table class="border-collapse table-auto w-full whitespace-no-wrap bg-white table-striped relative">
                    <thead>
                        <tr class="text-left text-sm">
                            <th class="w-1/12 bg-blue-500 text-white pl-4 p-1">Subject Code</th>
                            <th class="w-3/12 bg-blue-500 text-white p-1">Subject Name</th>
                            <th class="w-1/12 bg-blue-500 text-white p-1">Units (Lec)</th>
                            <th class="w-1/12 bg-blue-500 text-white p-1">Units (Lab)</th>
                            <th class="w-2/12 bg-blue-500 text-white p-1">Pre-Req 1</th>
                            <th class="w-2/12 bg-blue-500 text-white p-1">Pre-Req 2</th>
                            <th class="w-2/12 bg-blue-500 text-white p-1">Co-Req</th>
                            @if(Auth::check() && Auth::user()->role === 'admin')
                            <th class="w-1/12 bg-blue-500 text-center text-white p-1">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @if($subjects->isNotEmpty())
                        @foreach ($subjects as $subject)
                        <tr class="border-b hover:bg-gray-100 cursor-pointer text-sm">
                            <td class="border-dashed border-t border-gray-300 pl-4 p-2"><strong>{{ $subject->subject_code }}</strong></td>
                            <td class="border-dashed border-t border-gray-300 p-2">{{ $subject->subject_name }}</td>
                            <td class="border-dashed border-t border-gray-300 p-2">{{ $subject->units_lec }}</td>
                            <td class="border-dashed border-t border-gray-300 p-2">{{ $subject->units_lab }}</td>
                            <td class="border-dashed border-t border-gray-300 p-2">{{ $subject->prereq1 ?? '-'}}</td>
                            <td class="border-dashed border-t border-gray-300 p-2">{{ $subject->prereq2 ?? '-'}}</td>
                            <td class="border-dashed border-t border-gray-300 p-2">{{ $subject->co_req ?? '-'}}</td>
                            @if(Auth::check() && Auth::user()->role === 'admin')
                            <td class="border-dashed border-t border-gray-300 p-2 pr-4">
                                <div class="flex gap-2">
                                    <button 
                                        @click.stop="showUpdateModal=true; 
                                        selectedId = {{$subject->subject_id}};
                                        selectedSubjectCode = '{{ $subject->subject_code }}'; 
                                        selectedSubjectName = '{{ $subject->subject_name }}'; 
                                        selectedSubjectDescription = '{{ $subject->subject_description }}'; 
                                        selectedSubjectUnitsLec = {{ $subject->units_lec }}; 
                                        selectedSubjectUnitsLab = {{ $subject->units_lab }}; 
                                        selectedPreReq1 = '{{ $subject->prereq1 }}'; 
                                        selectedPreReq2 = '{{ $subject->prereq2 }}'; 
                                        selectedPreReq2 = '{{ $subject->co_req }}';" 
                                    class="bg-blue-500 text-xs text-white p-1 rounded hover:bg-blue-600">Update</button>
                                    <button @click.stop="showDeleteModal=true; selectedId = {{$subject->subject_id}}" class="bg-red-500 text-xs text-white p-1 rounded hover:bg-red-600 transition ease-in-out duration-150">Delete</button>
                                </div>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="8" class="w-full mt-16 text-rose-600 text-center bg-slate-100 py-12">No Subject Records Available</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Add Subject Modal --> 
        <div x-cloak x-show="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
            <div class="modal-content bg-white p-8 rounded-lg shadow-lg overflow-auto max-w-lg w-full min-h-[85vh] max-h-[85vh]">
                <h3 class="text-lg font-bold mb-4">Add New Program</h3>
                <form action="{{ route('subject-catalog-new-subject') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="subject_code" class="block text-sm font-medium text-gray-700">Subject Code:</label>
                        <input type="text" id="subject_code" name="subject_code" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label for="subject_name" class="block text-sm font-medium text-gray-700">Subject Name:</label>
                        <input type="text" id="subject_name" name="subject_name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label for="subject_description" class="block text-sm font-medium text-gray-700">Subject Description:</label>
                        <textarea id="subject_description" name="subject_description" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                    </div>
                    <div>
                        <label for="units_lec" class="block text-sm font-medium text-gray-700">Units Lecture:</label>
                        <input type="number" id="units_lec" name="units_lec" step="0.1" min="0" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label for="units_lab" class="block text-sm font-medium text-gray-700">Units Lab:</label>
                        <input type="number" id="units_lab" name="units_lab" step="0.1" min="0" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label for="prereq1" class="block text-sm font-medium text-gray-700">Pre-Requisite 1:</label>
                        <select id="prereq1" name="prereq1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" style="width: 100%;"></select>
                    </div>
                    <div>
                        <label for="prereq2" class="block text-sm font-medium text-gray-700">Pre-Requisite 2:</label>
                        <select id="prereq2" name="prereq2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" style="width: 100%;"></select>
                    </div>
                    <div>
                        <label for="co_req" class="block text-sm font-medium text-gray-700">Co-Requisite:</label>
                        <select id="co_req" name="co_req" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" style="width: 100%;"></select>
                    </div>
                    <div class="flex justify-end space-x-4">
                        <button type="button" @click="showModal = false" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">Cancel</button>
                        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition ease-in-out duration-150">Create Subject</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Delete Modal -->
        <div x-cloak x-show="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
            <div class="modal-content bg-white p-8 rounded-lg shadow-lg overflow-auto max-w-md w-full">
                <h3 class="text-lg font-bold mb-4">Confirm Deletion</h3>
                <p>Are you sure you want to delete this subject?</p>
                <div class="flex justify-end mt-4">
                <div class="flex items-center">
                    <button @click="showDeleteModal = false" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition ease-in-out duration-150 mr-2">Cancel</button>
                    <form :action="'/admin/functions/program-course-management/subject_catalog/delete/' + selectedId" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition ease-in-out duration-150">Delete</button>
                    </form>
                </div>
                </div>
            </div>
        </div>
        <!-- Update Modal -->
        <div x-cloak x-show="showUpdateModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
            <div class="modal-content bg-white p-8 rounded-lg shadow-lg overflow-auto max-w-lg w-full min-h-[85vh] max-h-[85vh]">
                <h3 class="text-lg font-bold mb-4">Update Program</h3>
                <form :action="'/admin/functions/program-course-management/subject_catalog/update/' + selectedId" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="id" x-model="selectedId">
                    <div> 
                        <label for="subject_code" class="block text-sm font-medium text-gray-700">Subject Code:</label>
                        <input type="text" id="subject_code" name="subject_code" x-model="selectedSubjectCode" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label for="subject_name" class="block text-sm font-medium text-gray-700">Subject Name:</label>
                        <input type="text" id="subject_name" name="subject_name"  x-model="selectedSubjectName" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label for="subject_description" class="block text-sm font-medium text-gray-700">Subject Description:</label>
                        <textarea id="subject_description" name="subject_description" x-model="selectedSubjectDescription" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                    </div>
                    <div>
                        <label for="units_lec" class="block text-sm font-medium text-gray-700">Units Lecture:</label>
                        <input type="number" id="units_lec" name="units_lec" step="0.1" min="0" x-model="selectedSubjectUnitsLec" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label for="units_lab" class="block text-sm font-medium text-gray-700">Units Lab:</label>
                        <input type="number" id="units_lab" name="units_lab" step="0.1" min="0" x-model="selectedSubjectUnitsLab" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label for="prereqUpdate1" class="block text-sm font-medium text-gray-700">Pre-Requisite 1:</label>
                        <select id="prereqUpdate1" name="prereqUpdate1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" style="width: 100%;"></select>
                    </div>
                    <div>
                        <label for="prereqUpdate2" class="block text-sm font-medium text-gray-700">Pre-Requisite 2:</label>
                        <select id="prereqUpdate2" name="prereqUpdate2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" style="width: 100%;"></select>
                    </div>
                    <div>
                        <label for="co_req2" class="block text-sm font-medium text-gray-700">Co-Requisite:</label>
                        <select id="co_req2" name="co_req2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" style="width: 100%;"></select>
                    </div>
                    <div class="flex justify-end space-x-4">
                        <button type="button" @click="showUpdateModal = false" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">Cancel</button>
                        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition ease-in-out duration-150">Update Subject</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script>
$(document).ready(function() {
    $('#prereq1, #prereq2, #prereqUpdate1, #prereqUpdate2, #co_req, #co_req2').select2({
        width: 'resolve',
        placeholder: "Subject Code or Subject Name",
        allowClear: true,
        minimumInputLength: 1,
        ajax: {
            url: '/admin/functions/get-subjects',
            dataType: 'json',
            delay: 20,
            processResults: function (data) {
                return {
                    results: data.map(function (item) {
                        return {
                            id: item.subject_id,
                            text: item.subject_code + ' - ' + item.subject_name
                        };
                    })
                };
            },
            cache: true
        }
    }).each(function() {
        var $this = $(this);
        var selectedValue = $this.data('selected-value');
        if (selectedValue) {
            $this.val(selectedValue).trigger('change');
        }
    });
});

</script>
@endsection