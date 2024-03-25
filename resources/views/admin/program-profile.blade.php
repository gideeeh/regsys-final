@extends('admin.functions')
@section('content')
<div x-data="{
        manageProgramModal: false,
        currentYear: '',
        currentTerm: '',
        selectedSubjects: [],
        openModalAndFetchSubjects(year, term) {
            this.currentYear = year;
            this.currentTerm = term;
            this.manageProgramModal = true;
            let programId = {{ $program->program_id }};
            let fetchUrl = `/program/${programId}/subjects/${year}/${term}`;

            axios.get(fetchUrl)
                .then(response => {
                    $('#assign_subject').val(response.data).trigger('change');
                })
                .catch(error => console.error('Error fetching subjects:', error));
        }
    }">
    <x-alert-message />
    <h3 class="flex w-full justify-center bg-sky-950 px-4 rounded-md text-white mb-6 border-b-4 border-amber-300">Curriculum Details</h3>
    @if($program->program_major)
    <p class="mb-4"><span class="text-3xl font-bold mr-2">{{$program->program_name}}</span><span class="text-xl font-semibold text-slate-600">Major in {{$program->program_major}}</span></p>
    @else
    <h1>{{$program->program_name}} </h1>
    @endif
    <span class="py-8"><em>{{ trim($program->program_desc) ? $program->program_desc : 'No Description' }}</em></span>
    <div class="flex py-4 flex-col">
        <span><strong>Program Coordinator:</strong> {{ trim($program->program_coordinator) ? $program->program_coordinator : 'Not Set' }}</span>
        <!-- <span><strong>Total Enrolled Students:</strong></span> -->
        <span><strong>Total Units:</strong> {{$total_units}}</span>
    </div>
    <!-- Display subjects Year 1 to 3 -->
    @foreach (['1st Year', '2nd Year', '3rd Year'] as $yearKey => $yearValue)
    <div class="border-double border-4 border-sky-950 px-4 py-4 mb-6 hover:border-solid rounded-lg">
        <h2 class="text-center bg-sky-950 px-4 rounded-md shadow text-white mb-6">{{ $yearValue }}</h2>
        @foreach (['Term 1', 'Term 2', 'Term 3'] as $termKey => $termValue)
            <div class="mb-6">
                <div class="flex items-end justify-center mb-2">
                    <h2 class="mr-1 mb-0">{{ $termValue }}</h2>
                    @if(Auth::check() && Auth::user()->role === 'admin')
                    <span class="underline text-gray-500 text-xs pb-1.5 hover:text-blue-600 cursor-pointer" @click.prevent="openModalAndFetchSubjects({{ $yearKey + 1 }}, {{ $termKey + 1 }})">Manage</span>
                    @endif
                </div>
                <div class="overflow-x-auto bg-white rounded-lg shadow overflow-y-auto relative">
                    @php
                        $year = $yearKey + 1;
                        $term = $termKey + 1;
                    @endphp
                    @if(isset($program_subjects[$year][$term]))
                    <table class="border-solid table-auto w-full whitespace-no-wrap bg-white table-striped relative text-xs">
                        <thead>
                            <tr class="text-left">
                                <th class="w-1/12 bg-sky-600 text-white p-2 pl-4">Code</th>
                                <th class="w-3/12 bg-sky-600 text-white p-2">Name</th>
                                <th class="bg-sky-600 text-white p-2">Pre-req 1</th>
                                <th class="bg-sky-600 text-white p-2">Pre-req 2</th>
                                <th class="bg-sky-600 text-white p-2">Co req</th>
                                <th class="w-1/12 text-center bg-sky-600 text-white p-2">Units(Lec)</th>
                                <th class="w-1/12 text-center bg-sky-600 text-white p-2 pr-4">Units(Lab)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($program_subjects[$year][$term] as $program_subject)
                            <tr class="text-sm border-b hover:bg-gray-100 cursor-pointer">
                                <td class="border-dashed border-t border-gray-200 p-2 py-4 pl-4"><strong>{{$program_subject->subject->subject_code}}</strong></td>
                                <td class="border-dashed border-t border-gray-200 p-2 py-4">{{$program_subject->subject->subject_name}}</td>
                                <td class="border-dashed border-t border-gray-200 p-2 py-4">{{ $program_subject->prerequisite_1 ?? '-' }}</td>
                                <td class="border-dashed border-t border-gray-200 p-2 py-4">{{ $program_subject->prerequisite_2 ?? '-' }}</td>
                                <td class="border-dashed border-t border-gray-200 p-2 py-4">{{ $program_subject->co_req ?? '-' }}</td>
                                <td class="border-dashed text-center border-t border-gray-200 p-2 py-4">{{$program_subject->subject->units_lec}}</td>
                                <td class="border-dashed text-center border-t border-gray-200 p-2 py-4 pr-4">{{$program_subject->subject->units_lab}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="flex px-8 py-2 bg-gray-300 text-sm">
                        <span class="w-4/12"><strong>Total Units(Lec):</strong> {{$totalsByYearTerm[$year][$term]['lec'] ?? '0'}}</span>
                        <span class="w-4/12"><strong>Total Units(Lab):</strong> {{$totalsByYearTerm[$year][$term]['lab'] ?? '0'}}</span>
                        <span class="w-4/12"><strong>Total Units:</strong> {{$totalsByYearTerm[$year][$term]['total'] ?? '0' }}</span>
                    </div>
                    @else
                    <span class="px-3">No subjects for this program for this year and term.</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    @endforeach
    <!-- Display Year 4 -->
    <div class="border-double border-4 border-sky-950 px-4 py-4 mb-6 hover:border-solid rounded-lg">
        <h2 class="text-center bg-sky-950 px-4 rounded-md shadow text-white mb-6">4th Year</h2>
        <div class="flex items-end justify-center mb-2">
            <h2 class="mr-1 mb-0">Term 1</h2>
            @if(Auth::check() && Auth::user()->role === 'admin')
            <span class="underline text-gray-500 text-xs pb-0.5 hover:text-blue-600 cursor-pointer" @click.prevent="openModalAndFetchSubjects(4, 1)">Manage</span>
            @endif
        </div>
        <div class="overflow-x-auto bg-white rounded-lg shadow overflow-y-auto relative">
            @php
                $year = 4;
                $term = 1;
            @endphp
            @if(isset($program_subjects[$year][$term]))
            <table class="border-solid table-auto w-full whitespace-no-wrap bg-white table-striped relative text-xs">
                <thead>
                    <th class="w-1/12 bg-sky-600 text-white p-2">Code</th>
                    <th class="w-3/12 bg-sky-600 text-white p-2">Name</th>
                    <th class="w-3/12 bg-sky-600 text-white p-2">Pre-req 1</th>
                    <th class="w-3/12 bg-sky-600 text-white p-2">Pre-req 2</th>
                    <th class="w-3/12 bg-sky-600 text-white p-2">Co-req</th>
                    <th class="w-1/12 bg-sky-600 text-white p-2">Units(Lec)</th>
                    <th class="w-1/12 bg-sky-600 text-white p-2">Units(Lab)</th>
                </thead>
                <tbody>
                    @foreach($program_subjects[$year][$term] as $program_subject)
                    <tr class="border-b hover:bg-gray-100 cursor-pointer">
                        <td class="border-dashed border-t border-gray-200 p-2 py-4">{{$program_subject->subject->subject_code}}</td>
                        <td class="border-dashed border-t border-gray-200 p-2 py-4">{{$program_subject->subject->subject_name}}</td>
                        <td class="border-dashed border-t border-gray-200 p-2 py-4">{{$program_subject->prerequisite_1}}</td>
                        <td class="border-dashed border-t border-gray-200 p-2 py-4">{{$program_subject->prerequisite_2}}</td>
                        <td class="border-dashed border-t border-gray-200 p-2 py-4">{{$program_subject->co_req}}</td>
                        <td class="border-dashed border-t border-gray-200 p-2 py-4">{{$program_subject->subject->units_lec}}</td>
                        <td class="border-dashed border-t border-gray-200 p-2 py-4">{{$program_subject->subject->units_lab}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="flex px-8 py-2 bg-gray-300 text-sm">
                <span class="w-4/12"><strong>Total Units(Lec):</strong> {{$totalsByYearTerm[$year][$term]['lec'] ?? '0'}}</span>
                <span class="w-4/12"><strong>Total Units(Lab):</strong> {{$totalsByYearTerm[$year][$term]['lab'] ?? '0'}}</span>
                <span class="w-4/12"><strong>Total Units:</strong> {{$totalsByYearTerm[$year][$term]['total'] ?? '0' }}</span>
            </div>
            @else
            <span class="px-3">No subjects for this program for this year and term.</span>
            @endif
        </div>
    </div>
    <!-- Manage Program Modal -->
    <div x-cloak x-show="manageProgramModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
        <div class="modal-content bg-white p-8 rounded-lg shadow-lg overflow-auto max-w-md w-full max-h-[80vh]">
            <h3 class="text-lg font-bold mb-4" x-text="'Manage Subjects: Year - ' + currentYear + ' Term - ' + currentTerm"></h3>
            <form action="{{ route('program-subject.save', ['program_id' => $program_id]) }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="program_id" x-model="{{ $program_id }}">
                <input type="hidden" name="year" x-model="currentYear">
                <input type="hidden" name="term" x-model="currentTerm">
                <div>
                    <select id="assign_subject" name="subject_ids[]" multiple="multiple" x-model="selectedSubjects" style="width: 100%;" autofocus>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->subject_id }}">{{ $subject->subject_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-center space-x-4 pt-12">
                    <button type="button" @click="manageProgramModal = false" class="w-4/12 bg-gray-500 text-white px-2 py-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">Cancel</button>
                    <button type="submit" class="w-4/12 bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition ease-in-out duration-150">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Initialize Select2 first
        $('#assign_subject').select2({
            width: 'resolve',
            placeholder: "Select a subject",
            allowClear: true,
            minimumInputLength: 1,
            ajax: {
                url: '/admin/functions/get-subjects',
                dataType: 'json',
                delay: 250,
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
        });

        // Then set preselected subjects
    let preselectedSubjects = @json($selectedSubjectsIds ?? []);
    $('#assign_subject').val(preselectedSubjects).trigger('change');
    });
</script>

@endsection