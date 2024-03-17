@extends('admin.functions')
@section('content')

<div x-data = "{
    createSection: false,
    filterByModal: false,
    }">
    <div>
        <button @click="createSection=true" class="bg-green-500 text-white text-md px-2 py-2 rounded hover:bg-green-600 transition ease-in-out duration-150">Create Section</button>
        <button @click="filterByModal=true" class="bg-sky-500 text-white text-md px-2 py-2 rounded hover:bg-sky-600 transition ease-in-out duration-150">Filter By</button> 
    </div>
    <!-- Table -->
    <div>
        <table>
            <thead>
                <tr>
                    <th>Section Name</th>
                    <th>Academic Year</th>
                    <th>Term</th>
                    <th>Year Level</th>
                    <th>Section Type</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sections as $section)
                <tr @click="window.location.href='{{ route('sections.show', $section->section_id) }}'">
                    <td>{{$section->section_name}}</td>
                    <td>{{$section->academic_year}}</td>
                    <td>{{$section->term}}</td>
                    <td>{{$section->year_level}}</td>
                    <td>{{ $section->section_type->section_type ?? 'N/A' }}</td> 
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
















    <!-- Create Section Modal -->
    <div x-cloak x-show="createSection" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
        <div class="modal-content bg-white p-8 rounded-lg shadow-lg overflow-auto max-w-md w-full min-h-[70vh] max-h-[90vh]">
            <h2>Create Section</h2>
            <form action="{{ route('section.create') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="create_sec_section_name" class="block text-md font-semibold text-gray-700 mr-2">Section Name:</label>
                    <input type="text" id="create_sec_section_name" name="create_sec_section_name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Section [Number]">
                </div>
                <div class="flex flex-col mb-4">
                    <label for="create_section_type" class="block text-md font-semibold text-gray-700 mr-2">Section Type:</label>
                    <select id="create_section_type" name="create_section_type" class="text-md border-gray-300 rounded-md shadow-sm" required>
                        @foreach($section_types as $section_type)
                        <option value="{{ $section_type->id }}">
                            {{ $section_type->section_type }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col mb-4">
                    <label for="create_sec_acad_year" class="block text-md font-semibold text-gray-700 mr-2">Academic Year:</label>
                    <select id="create_sec_acad_year" name="create_sec_acad_year" class="text-md border-gray-300 rounded-md shadow-sm" required>
                        @foreach($acad_years as $year)
                        <option value="{{ $year->acad_year }}" {{ (isset($activeAcadYear) && $activeAcadYear->id == $year->id) ? 'selected' : '' }}>
                            {{ $year->acad_year }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col mb-4">
                    <label for="create_sec_term" class="block text-md font-semibold text-gray-700 mr-2">Term:</label>
                    <select id="create_sec_term" name="create_sec_term" class="text-md w-full border-gray-300 rounded-md shadow-sm" placeholder="Select Term" required>
                        <option value="{{$activeTerm}}" hidden>{{$activeTerm}}</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                    </select>
                </div>
                <div class="flex flex-col mb-4">
                    <label for="create_sec_year_level" class="block text-md font-semibold text-gray-700 mr-2">Year Level:</label>
                    <select id="create_sec_year_level" name="create_sec_year_level" class="text-md w-full border-gray-300 rounded-md shadow-sm" placeholder="Select Year Level" required>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-4 pt-2">
                    <button type="button" @click="createSection = false" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">Cancel</button>
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition ease-in-out duration-150">Create Section</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Filter By Modal -->
    <div x-cloak x-show="filterByModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
        <div class="modal-content bg-white p-8 rounded-lg shadow-lg overflow-auto max-w-md w-full" style="display: flex; flex-direction: column; min-height: 85vh; max-height: 90vh;">
            <h2 class="mb-4">Filter By</h2>
            <form action="{{ route('sections') }}" method="GET" class="modal-content bg-white p-8 rounded-lg shadow-lg overflow-auto max-w-md w-full" style="flex-direction: column; min-height: 85vh; max-height: 90vh;">
                <div class="mb-4 mt-6">
                    <label for="filter_acad_year" class="w-full block text-md font-semibold text-gray-700">Academic Year:</label>
                    <select id="filter_acad_year" name="filter_acad_year" class="w-full text-md border-gray-300 rounded-md shadow-sm" required>
                        @foreach($acad_years as $year)
                        <option value="{{ $year->acad_year }}" {{ (isset($activeAcadYear) && $activeAcadYear->id == $year->id) ? 'selected' : '' }}>
                            {{ $year->acad_year }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label for="filter_term" class="w-full block text-md font-semibold text-gray-700">Term:</label>
                    <select id="filter_term" name="filter_term" class="text-md w-full border-gray-300 rounded-md shadow-sm" placeholder="Select Term" required>
                        <option value="{{$activeTerm}}" hidden>{{$activeTerm}}</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="filter_section_type" class="w-full block text-md font-semibold text-gray-700">Section Type:</label>
                    <select id="filter_section_type" name="filter_section_type" class="text-md w-full border-gray-300 rounded-md shadow-sm" placeholder="Select Term" required>
                        <option value="all">All</option>
                        @foreach($section_types as $section_type)
                        <option value="{{ $section_type->id }}">
                            {{ $section_type->section_type }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label for="filter_year_level" class="block text-md font-semibold text-gray-700 mr-2">Year Level:</label>
                    <select id="filter_year_level" name="filter_year_level" class="text-md w-full border-gray-300 rounded-md shadow-sm" placeholder="Select Year Level" required>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-4 pt-2" style="margin-top: auto;">
                    <button type="button" @click="filterByModal = false" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">Cancel</button>
                    <button id="filter-confirm-button" type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition ease-in-out duration-150">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection