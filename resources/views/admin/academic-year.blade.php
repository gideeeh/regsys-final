@extends('admin.functions')
@section('content')
<div x-data="{
    showAddAcadYearTerm: false,
    showUpdateAcadYearTerm: false,
    showDeleteAcadYearTerm: false,
    selectedAcadYearId: null,
    setSelectedAcadYear(acadYear) {
        this.selectedAcadYear = acadYear;
    }, 
    selectedAcadYear: '' }" 
    @keydown.escape.window="showModal = false; showAddAcadYearTerm = false; showUpdateAcadYearTerm = false; showDeleteAcadYearTerm = false;">

    <x-alert-message />
    <h3 class="flex w-full justify-center bg-sky-950 px-4 rounded-md text-white mb-6">Academic Year</h3>
    <button @click="showAddAcadYearTerm = true" id="showSetAcadYearTerm" class="mb-4 bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition ease-in-out duration-150">+ Add Academic Year Record</button>
    
    <div class="flex flex-wrap acad-yr-container justify-start">
    @if(isset($acad_years))
        @foreach($acad_years as $acad_year)
        <div class="cursor-default acad-year-card border-solid border-2 border-slate-400 rounded-md px-4 py-4 mb-6 mr-12 hover:border-sky-950 w-5/12">
            <div class="pb-4 relative">
                <div class="absolute right-0">
                @php
                    $today = \Carbon\Carbon::now();
                    $isActive = (
                        ($today->between(new \Carbon\Carbon($acad_year->term_1_start), new \Carbon\Carbon($acad_year->term_1_end))) ||
                        ($today->between(new \Carbon\Carbon($acad_year->term_2_start), new \Carbon\Carbon($acad_year->term_2_end))) ||
                        ($acad_year->term_3_start && $acad_year->term_3_end && $today->between(new \Carbon\Carbon($acad_year->term_3_start), new \Carbon\Carbon($acad_year->term_3_end)))
                    );
                @endphp
                @if($isActive)
                    <span class="text-green-400 font-semibold">Active</span>
                @else
                    <span class="text-gray-400 font-semibold">Not Active</span>
                @endif
                </div>
                <div>
                    <span class="font-semibold text-sm">Acad Year: </span>
                    <h2 class="text-sky-800">{{ $acad_year->acad_year }}</h2>
                    <p><strong class="text-slate-600">Start of Class:</strong> <span>{{ $acad_year->term_1_start ? \Carbon\Carbon::parse($acad_year->term_1_start)->format('M j, Y') : 'Start date not set' }}</span></p>
                    <p><strong class="text-red-600">End of Class:</strong> <span>{{ $acad_year->term_3_end ? \Carbon\Carbon::parse($acad_year->term_3_end)->format('M j, Y') : 'End date not set' }}</span></p>
                </div>
            </div>
            
            <div class="border-t-4 border-sky-400 py-2">
                <div class="pb-4">
                    <h3 class="text-lg">Term 1:</h3>
                    <p><strong class="text-slate-600">Start: </strong>{{ $acad_year->term_1_start ? \Carbon\Carbon::parse($acad_year->term_1_start)->format('M j, Y') : 'Start date not set' }}</p>
                    <p><strong class="text-red-600">End: </strong>{{ $acad_year->term_1_end ? \Carbon\Carbon::parse($acad_year->term_1_end)->format('M j, Y') : 'End date not set' }}</p>
                </div>
                <div class="border-dotted border-t-2 border-slate-400 py-2">
                    <h3 class="text-lg">Term 2:</h3>
                    <p><strong class="text-slate-600">Start: </strong>{{ $acad_year->term_2_start ? \Carbon\Carbon::parse($acad_year->term_2_start)->format('M j, Y') : 'Start date not set' }}</p>
                    <p><strong class="text-red-600">End: </strong>{{ $acad_year->term_2_end ? \Carbon\Carbon::parse($acad_year->term_2_end)->format('M j, Y') : 'End date not set' }}</p>
                </div>
                <div class="border-dotted border-t-2 border-slate-400 py-2">
                    <h3 class="text-lg">Term 3:</h3>
                    <p><strong class="text-slate-600">Start: </strong>{{ $acad_year->term_3_start ? \Carbon\Carbon::parse($acad_year->term_3_start)->format('M j, Y') : 'Start date not set' }}</p>
                    <p><strong class="text-red-600">End: </strong>{{ $acad_year->term_3_end ? \Carbon\Carbon::parse($acad_year->term_3_end)->format('M j, Y') : 'End date not set' }}</p>
                </div>
            </div>
            <span @click="showUpdateAcadYearTerm = true; selectedAcadYear={{$acad_year->acad_year}}; selectedAcadYearId = {{ $acad_year->id }}; setSelectedAcadYear('{{ $acad_year->acad_year }}'); setSelectedAcadYearId({{ $acad_year->id }})" class="underline text-gray-500 text-xs pb-1.5 hover:text-blue-600 cursor-pointer flex justify-end">Manage</span>
            <span @click="showDeleteAcadYearTerm = true; selectedAcadYearId = {{ $acad_year->id }}" class="underline text-red-400 text-xs pb-1.5 ml-4 hover:text-red-600 cursor-pointer flex justify-end">Delete</span>
        </div>
        @endforeach
    @endif
    </div>
    
    <!-- Add Acad Year Record Modal -->
    <div x-cloak x-show="showAddAcadYearTerm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50" x-data="academicYearSetup()" x-init="init()">
        <div class="modal-content bg-white p-8 rounded-lg shadow-lg overflow-auto max-w-md w-full min-h-[85vh] max-h-[86vh]">
            <h3 class="text-lg font-bold mb-4">Set School Year and Term</h3>
            <form action="{{ route('academic-year.store')}}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="acad_year" class="block text-sm font-medium text-gray-700">Academic Year:</label>
                    <select id="acad_year" name="acad_year" placeholder="Select Acad Year" required x-model="acadYear" @change="updateTermAvailability()" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">Select Acad Year</option>
                        @php
                        $currentYear = 2023;
                        $endYear = $currentYear + 10;
                        for ($year = $currentYear; $year < $endYear; $year++) {
                            $acad_year = $year . '-' . ($year + 1);
                            echo "<option value='{$acad_year}'>{$acad_year}</option>";
                        }
                        @endphp
                    </select>                
                </div>
                <div class="border-solid border-2 border-slate-400 rounded-md px-4 py-4 mb-6 hover:border-sky-950">
                    <h2>Term 1</h2>
                    <div>
                        <label for="term_1_start" class="block text-sm font-medium text-gray-700">Start Date:</label>
                        <input type="date" name="term_1_start" id="term_1_start" x-bind:disabled="term1Disabled" x-model="term1Start" @change="updateTermAvailability()" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label for="term_1_end" class="block text-sm font-medium text-gray-700">End Date:</label>
                        <input type="date" name="term_1_end" id="term_1_end" x-bind:disabled="term1Disabled" x-model="term1End" @change="updateTermAvailability()" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                </div>
                <div class="border-solid border-2 border-slate-400 rounded-md px-4 py-4 mb-6 hover:border-sky-950">
                    <h2>Term 2</h2>
                    <div>
                        <label for="term_2_start" class="block text-sm font-medium text-gray-700">Start Date:</label>
                        <input type="date" name="term_2_start" id="term_2_start" x-bind:disabled="term2Disabled" x-model="term2Start" @change="updateTermAvailability()" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label for="term_2_end" class="block text-sm font-medium text-gray-700">End Date:</label>
                        <input type="date" name="term_2_end" id="term_2_end" x-bind:disabled="term2Disabled" x-model="term2End" @change="updateTermAvailability()" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                </div>
                <div class="border-solid border-2 border-slate-400 rounded-md px-4 py-4 mb-6 hover:border-sky-950">
                    <h2>Term 3</h2>
                    <div>
                        <label for="term_3_start" class="block text-sm font-medium text-gray-700">Start Date:</label>
                        <input type="date" name="term_3_start" id="term_3_start" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">           
                    </div>
                    <div>
                        <label for="term_3_end" class="block text-sm font-medium text-gray-700">End Date:</label>
                        <input type="date" name="term_3_end" id="term_3_end" x-bind:disabled="term3Disabled" x-model="term3End" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                </div>
                <div class="flex justify-end space-x-4">
                    <button type="button" @click="showAddAcadYearTerm = false" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">Close</button>
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition ease-in-out duration-150">Set Acad Year & Term</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Modal -->
    <div x-cloak x-show="showDeleteAcadYearTerm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
        <div class="modal-content bg-white p-8 rounded-lg shadow-lg overflow-auto max-w-md w-full">
            <h3 class="text-lg font-bold mb-4">Confirm Deletion</h3>
            <p>Are you sure you want to delete this record?</p>
            <div class="flex justify-end mt-4">
            <div class="flex items-center">
                <button @click="showDeleteAcadYearTerm = false" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition ease-in-out duration-150 mr-2">Cancel</button>
                <form :action="'/admin/functions/program-course-management/academic_year/delete_acad_year/' + selectedAcadYearId" class="pt-4" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition ease-in-out duration-150">Delete</button>
                </form>
            </div>
            </div>
        </div>
    </div>

    <!-- Update Academic Year Modal -->
    <div x-cloak x-show="showUpdateAcadYearTerm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
        <div class="modal-content bg-white p-8 rounded-lg shadow-lg overflow-auto max-w-lg w-full min-h-[85vh] max-h-[85vh]">
            <h3 class="text-lg font-bold mb-4">Update School Year and Term</h3>
            <h2 class="mb-4"><span x-text="selectedAcadYear"></span></h2>
            <form :action="'/admin/functions/program-course-management/academic_year/update_acad_year/' + selectedAcadYearId" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')
                <div class="border-solid border-2 border-slate-400 rounded-md px-4 py-4 mb-6 hover:border-sky-950">
                    <h2>Term 1</h2>
                    <div>
                        <label for="term_1_start" class="block text-sm font-medium text-gray-700">Start Date:</label>
                        <input type="date" name="term_1_start" id="term_1_start" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">           
                    </div>
                    <div>
                        <label for="term_1_end" class="block text-sm font-medium text-gray-700">End Date:</label>
                        <input type="date" name="term_1_end" id="term_1_end" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">           
                    </div>
                </div>
                <div class="border-solid border-2 border-slate-400 rounded-md px-4 py-4 mb-6 hover:border-sky-950">
                    <h2>Term 2</h2>
                    <div>
                        <label for="term_2_start" class="block text-sm font-medium text-gray-700">Start Date:</label>
                        <input type="date" name="term_2_start" id="term_2_start" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">           
                    </div>
                    <div>
                        <label for="term_2_end" class="block text-sm font-medium text-gray-700">End Date:</label>
                        <input type="date" name="term_2_end" id="term_2_end" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">           
                    </div>
                </div>
                <div class="border-solid border-2 border-slate-400 rounded-md px-4 py-4 mb-6 hover:border-sky-950">
                    <h2>Term 3</h2>
                    <div>
                        <label for="term_3_start" class="block text-sm font-medium text-gray-700">Start Date:</label>
                        <input type="date" name="term_3_start" id="term_3_start" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">           
                    </div>
                    <div>
                        <label for="term_3_end" class="block text-sm font-medium text-gray-700">End Date:</label>
                        <input type="date" name="term_3_end" id="term_3_end" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">           
                    </div>
                </div>
                <div class="flex justify-end space-x-4">
                    <button type="button" @click="showUpdateAcadYearTerm = false" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">Cancel</button>
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition ease-in-out duration-150">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

<script>
    function academicYearSetup() {
        return {
            acadYear: '',
            term1Start: '',
            term1End: '',
            term2Start: '',
            term2End: '',
            term3Start: '',
            term3End: '',
            term1Disabled: true,
            term2Disabled: true,
            term3Disabled: true,
            setSelectedAcadYearId(acadYearId) {
                this.selectedAcadYearId = acadYearId;
            },
            setSelectedAcadYear(acadYear) {
                console.log("Selected Academic Year:", acadYear); 
                this.selectedAcadYear = acadYear;
            },
            init() {
            },
            updateTermAvailability() {
                this.term1Disabled = this.acadYear === '';
                this.term2Disabled = !this.term1End;
                this.term3Disabled = !this.term2End;

                if (this.term1End && !this.term2Start) {
                    this.term2Start = this.term1End;
                }

                if (this.term2End && !this.term3Start) {
                    this.term3Start = this.term2End;
                }
            },
        };
    }
</script>