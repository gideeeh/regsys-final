<x-app-layout>
<div x-data="{
    manageSchedule:false,
    addSubjectToSection:false,
    showDeleteModal:false,
    selectedSubjectId: null,
    selectedSectionId: null,
    selectedSecSubId: null,
    selectedSectionSubjectId: null,
    selectedSubjectCode: '',
    selectedSubjectName: ''
    }"
    @keydown.escape.window = "
        manageSchedule=false;
        showDeleteModal=false;
        addSubjectToSection=false;">
    <h3 class="flex w-full justify-center bg-sky-950 px-4 rounded-md text-white mt-6 mb-2">Section Management</h3>
    <div class="p-6 flex justify-center">
        <div class="p-3 bg-white min-w-[98vw] rounded-md flex justify-between">
            <p><strong>{{$section->section_name}} - {{ucfirst($section->section_type->section_type)}}</strong></p>
                <p><strong>Academic Year:</strong> {{$section->academic_year}} T-{{$section->term}}</p>
                <p><strong>Year Level:</strong> {{$section->year_level}}</p>
        </div>
    </div>
    <div class="flex justify-center gap-2 bg-white rounded p-2">
        <div>
            @if($section->section_type->section_type==='block')
            <div class="text-center">
                <h3>Subjects Offered</h3>
            </div>
            <div class="max-h-[50vh] max-w-md overflow-y-scroll nice-scroll text-xs">
                <table class="table-auto">
                    <thead>
                        <tr class="text-left bg-blue-500 text-white">
                            <th>Subj Code</th>
                            <th>Subj Name</th>
                            <th>Action</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($blockSubjects as $blockSubject)
                        <tr>
                            <td class="border">{{$blockSubject->subject->subject_code}}</td>
                            <td class="border">{{$blockSubject->subject->subject_name}}</td>
                            <td class="border text-center cursor-default">
                                @php
                                $isScheduleSet = $sectionSubjects->first(function ($sectionSubject) use ($blockSubject) {
                                    return $sectionSubject->subject_id == $blockSubject->subject->subject_id;
                                });
                                @endphp
                                @if($isScheduleSet)
                                <p class="bg-green-100 text-green-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">Set</p>
                                @else
                                <p class="bg-red-100 text-red-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">Not Set</p>
                                @endif
                            </td>
                            <td class="border">
                                <button 
                                    @click="
                                        manageSchedule=true; 
                                        selectedSectionId={{$section->section_id}}; 
                                        selectedSubjectId={{$blockSubject->subject->subject_id}}; 
                                        selectedSubjectCode='{{$blockSubject->subject->subject_code}}'; 
                                        selectedSubjectName=`{{$blockSubject->subject->subject_name}};" 
                                    class='bg-sky-950 text-xs text-white text-xs p-1 rounded hover:bg-sky-800 transition ease-in-out duration-150'>
                                    Manage
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else

            <!-- IF ITS FREEEEEEEEEEEEEEEEEE -->
            <div class="flex justify-between px-2">
                <h3>Subjects Schedules</h3>
                <div class="flex items-center">
                    <button @click="addSubjectToSection=true" class="bg-green-500 text-white text-xs px-1 py-1 rounded hover:bg-green-600 transition ease-in-out duration-150">Add Subject</button>
                </div>
            </div>
            <div class="max-h-[50vh] max-w-md overflow-y-scroll nice-scroll text-xs">
                <table class="border-separate border-spacing-2">
                    <thead>
                        <tr>
                            <th>Subject Code</th>
                            <th>Subject Name</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sectionSubjects as $sectionSubject)
                        <tr>
                            <td>{{$sectionSubject->subject?->subject_code}}</td>
                            <td>{{$sectionSubject->subject?->subject_name}}</td>
                            <td class="cursor-default">
                                @if($sectionSubject->subjectSectionSchedule)
                                    <span class="bg-green-100 text-green-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">Set</span>
                                @else
                                    <span class="bg-red-100 text-red-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">Not Set</span>
                                @endif
                            </td>
                            <td>
                                <button @click="
                                    manageSchedule=true; 
                                    selectedSectionId={{$section->section_id}}; 
                                    selectedSecSubId={{$sectionSubject->id}}; 
                                    selectedSubjectName='{{$sectionSubject->subject?->subject_name}}'; 
                                    selectedSubjectId={{$sectionSubject->subject_id}};" 
                                    class='bg-sky-950 text-white text-xs px-1 py-1 rounded hover:bg-sky-800 transition ease-in-out duration-150'>Manage</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
        <!-- Section Details regardless if free or block section -->
        <div>
            <div class="text-center">
                <h3>Section Details</h3>
            </div>
            <div class="text-sm max-h-[50vh] overflow-y-scroll nice-scroll">
                <table class="table-auto">
                    <thead>
                        <tr class="text-left bg-blue-500 text-white">
                            <th class="border px-4 py-2">Code</th>
                            <th class="border px-4 py-2">Subject Name</th>
                            <th class="border px-4 py-2">Sched F2F</th>
                            <th class="border px-4 py-2">Sched OL</th>
                            <th class="border px-4 py-2">Prof</th>
                            <th class="border px-4 py-2">Room</th>
                            <th class="border px-4 py-2">Limit</th>
                            <th class="border px-4 py-2">Pax</th>
                            @if($section->section_type->section_type==='free')
                            <th>Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sectionSubjects as $sectionSubject)
                        <tr class="even:bg-gray-200 odd:bg-white">
                            <td class="border px-4 py-2">{{$sectionSubject->subject?->subject_code}}</td>
                            <td class="border px-4 py-2">{{$sectionSubject->subject?->subject_name}}</td>
                            @php
                                $daysF2F = json_decode($sectionSubject->subjectSectionSchedule?->class_days_f2f, true) ?? [];
                                $daysOL = json_decode($sectionSubject->subjectSectionSchedule?->class_days_online, true) ?? [];

                                $dayMap = [
                                    'Monday' => 'Mon',
                                    'Tuesday' => 'Tue',
                                    'Wednesday' => 'Wed',
                                    'Thursday' => 'Thu',
                                    'Friday' => 'Fri',
                                    'Saturday' => 'Sat',
                                    'Sunday' => 'Sun',
                                ];

                                $abbreviatedDaysF2F = array_map(function($day) use ($dayMap) {
                                    return $dayMap[$day] ?? $day;
                                }, $daysF2F);

                                $abbreviatedDaysOL = array_map(function($day) use ($dayMap) {
                                    return $dayMap[$day] ?? $day;
                                }, $daysOL);
                            @endphp
                            @if($sectionSubject->subjectSectionSchedule?->class_days_f2f && $sectionSubject->subjectSectionSchedule?->start_time_f2f)
                            <td class="border px-4 py-2 text-xs">{{ implode(', ', $abbreviatedDaysF2F) }} {{\Carbon\Carbon::parse($sectionSubject->subjectSectionSchedule?->start_time_f2f)->format('h:i A') ?? 'No recorded time.' }} - {{\Carbon\Carbon::parse($sectionSubject->subjectSectionSchedule?->end_time_f2f)->format('h:i A') ?? 'No recorded time.'}}</td>
                            @else
                            <td class="border px-4 py-2">-</td>
                            @endif
                            @if($sectionSubject->subjectSectionSchedule?->class_days_online && $sectionSubject->subjectSectionSchedule?->start_time_online)
                            <td class="border px-4 py-2 text-xs">{{ implode(', ', $abbreviatedDaysF2F) }} {{\Carbon\Carbon::parse($sectionSubject->subjectSectionSchedule?->start_time_f2f)->format('h:i A') ?? 'No recorded time.' }} - {{\Carbon\Carbon::parse($sectionSubject->subjectSectionSchedule?->end_time_f2f)->format('h:i A') ?? 'No recorded time.'}}</td>
                            @else
                            <td class="border px-4 py-2">-</td>
                            @endif
                            @if($sectionSubject->subjectSectionSchedule?->professor)
                            <td class="border px-4 py-2">{{substr($sectionSubject->subjectSectionSchedule?->professor->first_name,0,1).'.'}} {{$sectionSubject->subjectSectionSchedule?->professor->last_name}}</td>
                            @else
                            <td class="border px-4 py-2">-</td>
                            @endif
                            <td class="border px-4 py-2">{{$sectionSubject->subjectSectionSchedule?->room ?? '-' }}</td>
                            <td class="border px-4 py-2">{{$sectionSubject->subjectSectionSchedule?->class_limit ?? '-' }}</td>
                            @if($sectionSubject->subjectSectionSchedule?->class_limit)
                            <td class="border px-4 py-2 text-left">
                                @php
                                    $enrolledCount = $sectionSubject->enrolledStudentsCount();
                                    $classLimit = $sectionSubject->subjectSectionSchedule?->class_limit ?? 0;
                                @endphp

                                @if($enrolledCount >= $classLimit)
                                    <p class="bg-red-100 text-red-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">Full</p>
                                @else
                                    <p>{{ $enrolledCount }}</p>
                                @endif
                            </td>
                            @else
                            <td class="border px-4 py-2">-</td>
                            <td class="border px-4 py-2">
                                <button @click="showDeleteModal=true; selectedSectionSubjectId = {{ $sectionSubject->id }}" class="bg-red-500 text-white text-xs p-1 rounded hover:bg-red-600 transition ease-in-out duration-150">Remove</button>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>









    <!-- Add Subject -->
    <div x-cloak id="addSubjectToSection" x-show="addSubjectToSection" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50" data-section-id="${subject.section_id}">
        <div class="modal-content bg-white p-8 rounded-lg shadow-lg overflow-auto min-wd-lg max-w-xl w-full min-h-[55vh] max-h-[90vh] flex flex-col justify-between">
            <form method="POST" action="{{route('section-subject-free.store')}}">
            @csrf
                <input type="hidden" name="section_id" value="{{$section->section_id}}">
                <div>
                    <h1>Add Subject</h1>
                    <select id="subject_to_add" name="subject_to_add" x-model="subject_to_add" style="width: 100%;"></select>
                </div>
                <div class="flex justify-end space-x-4 pt-2">
                    <button type="button" @click="addSubjectToSection = false" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">Cancel</button>
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition ease-in-out duration-150">Add Subject</button>
                </div>
            </form>
        </div>        
    </div>        
    <!-- Delete Section Selection Modal for Free -->
    <div x-cloak x-show="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
        <div class="modal-content bg-white p-8 rounded-lg shadow-lg overflow-auto max-w-md w-full">
            <h3 class="text-lg font-bold mb-4">Confirm Deletion</h3>
            <p>Are you sure you want to remove this subject?</p>
            <div class="flex justify-end mt-4">
            <div class="flex items-center">
                <button @click="showDeleteModal = false" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition ease-in-out duration-150 mr-2">Cancel</button>
                <form :action="'/admin/functions/sections/store-subjects-free/remove/' + selectedSectionSubjectId" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition ease-in-out duration-150">Remove</button>
                </form>
            </div>
            </div>
        </div>
    </div>
            
    <!-- Manage Schedule Modal -->
    <div x-cloak id="manageSchedule" x-show="manageSchedule" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50" data-section-id="${subject.section_id}">
        <div class="modal-content bg-white p-8 rounded-lg shadow-lg overflow-auto min-wd-lg max-w-xl w-full min-h-[85vh] max-h-[90vh]">
            <h2>Manage Section</h2>
            <!-- F2F Class Schedule -->
            @if($section->section_type->section_type==='block')
            <form id="createSectionSchedule" action="{{route('section-subject.store')}}" method="POST">
            @else    
            <form id="createSectionSchedule" action="{{route('section-subject-free-schedule.store')}}" method="POST">
            @endif
                @csrf
                @if($section->section_type->section_type==='free')
                <input type="hidden" name="sec_sub_id" x-bind:value="selectedSecSubId">
                @endif
                <input type="hidden" name="subject_id" x-bind:value="selectedSubjectId">
                <input type="hidden" name="section_id" x-bind:value="selectedSectionId">
                <h3 class="mb-4"><span x-text="selectedSubjectCode"></span> - <span x-text="selectedSubjectName"></span></h3>
                <!-- <h1 x-text="selectedSectionId"></h1>
                <h1 x-text="selectedSubjectId"></h1> -->
                <div class="cursor-default w-full acad-year-card border-solid border-2 border-slate-400 rounded-md px-4 py-4 mb-6 mr-12 hover:border-sky-950">
                    <label class="block text-md font-semibold mb-2">F2F Class Schedule</label>
                    <fieldset class="mb-4"> 
                        <legend class="text-base font-medium text-gray-900 mb-2">Day(s)</legend>
                        <div class="flex justify-content items-center">
                            <!-- Mon to Wed -->
                            <div class="w-1/2 pl-4">
                                <div>
                                    <label class="flex items-center space-x-3">
                                        <input type="checkbox" name="f2f_days[]" value="Monday" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                        <span class="text-gray-700">Monday</span>
                                    </label>
                                </div>
                                <div>
                                    <label class="flex items-center space-x-3">
                                        <input type="checkbox" name="f2f_days[]" value="Tuesday" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                        <span class="text-gray-700">Tuesday</span>
                                    </label>
                                </div>
                                <div>
                                    <label class="flex items-center space-x-3">
                                        <input type="checkbox" name="f2f_days[]" value="Wednesday" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                        <span class="text-gray-700">Wednesday</span>
                                    </label>
                                </div>
                            </div>
                            <!-- Thu to Sat -->
                            <div class="w-1/2 pl-4">
                                <div>
                                    <label class="flex items-center space-x-3">
                                        <input type="checkbox" name="f2f_days[]" value="Thursday" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                        <span class="text-gray-700">Thursday</span>
                                    </label>
                                </div>
                                <div>
                                    <label class="flex items-center space-x-3">
                                        <input type="checkbox" name="f2f_days[]" value="Friday" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                        <span class="text-gray-700">Friday</span>
                                    </label>
                                </div>
                                <div>
                                    <label class="flex items-center space-x-3">
                                        <input type="checkbox" name="f2f_days[]" value="Saturday" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                        <span class="text-gray-700">Saturday</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <div class="flex gap-4 mb-4">
                        <div class="w-1/2">
                            <label for="f2f_start_time" class="block text-sm font-medium text-gray-700">Start Time:</label>
                            <input type="time" id="f2f_start_time" name="start_time_f2f" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div class="w-1/2">
                            <label for="f2f_end_time" class="block text-sm font-medium text-gray-700">End Time:</label>
                            <input type="time" id="f2f_end_time" name="end_time_f2f" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                    </div>
                </div>
                <!-- Online Class Schedule -->
                <div class="cursor-default w-full acad-year-card border-solid border-2 border-slate-400 rounded-md px-4 py-4 mb-6 mr-12 hover:border-sky-950">
                    <label class="block text-md font-semibold mb-2">Online Class Schedule</label>
                    <fieldset class="mb-4"> 
                        <legend class="text-base font-medium text-gray-900 mb-2">Day(s)</legend>
                        <div class="flex justify-content items-center">
                            <!-- Mon to Wed -->
                            <div class="w-1/2 pl-4">
                                <div>
                                    <label class="flex items-center space-x-3">
                                        <input type="checkbox" name="online_days[]" value="Monday" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                        <span class="text-gray-700">Monday</span>
                                    </label>
                                </div>
                                <div>
                                    <label class="flex items-center space-x-3">
                                        <input type="checkbox" name="online_days[]" value="Tuesday" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                        <span class="text-gray-700">Tuesday</span>
                                    </label>
                                </div>
                                <div>
                                    <label class="flex items-center space-x-3">
                                        <input type="checkbox" name="online_days[]" value="Wednesday" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                        <span class="text-gray-700">Wednesday</span>
                                    </label>
                                </div>
                            </div>
                            <!-- Thu to Sat -->
                            <div class="w-1/2 pl-4">
                                <div>
                                    <label class="flex items-center space-x-3">
                                        <input type="checkbox" name="online_days[]" value="Thursday" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                        <span class="text-gray-700">Thursday</span>
                                    </label>
                                </div>
                                <div>
                                    <label class="flex items-center space-x-3">
                                        <input type="checkbox" name="online_days[]" value="Friday" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                        <span class="text-gray-700">Friday</span>
                                    </label>
                                </div>
                                <div>
                                    <label class="flex items-center space-x-3">
                                        <input type="checkbox" name="online_days[]" value="Saturday" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                        <span class="text-gray-700">Saturday</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <div class="flex gap-4 mb-4">
                        <div class="w-1/2">
                            <label for="start_time" class="block text-sm font-medium text-gray-700">Start Time:</label>
                            <input type="time" id="start_time_online" name="start_time_online" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div class="w-1/2">
                            <label for="end_time" class="block text-sm font-medium text-gray-700">End Time:</label>
                            <input type="time" id="end_time_online" name="end_time_online" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="prof" class="block text-sm font-medium text-gray-700">Lecturer:</label>
                    <!-- <input type="text" id="prof_id" name="prof_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"> -->
                    <select id="prof_id" name="prof_id" x-model="prof_id" style="width: 100%;"></select>
                </div>
                <div class="mb-4">
                    <label for="room" class="block text-sm font-medium text-gray-700">Room:</label>
                    <input type="text" id="room" name="room" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="class_limit" class="block text-sm font-medium text-gray-700">Class Limit:</label>
                    <input type="number" id="class_limit" name="class_limit" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div class="flex justify-end space-x-4 pt-6">
                    <button type="button" @click="manageSchedule = false" class="hide-manage bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">Cancel</button>
                    <button class="assign_section bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition ease-in-out duration-150">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>
</x-app-layout>
<script>
    var getSubjectsUrl = "{{ url('/admin/functions/get-subjects') }}";
    var searchFacultyUrl = '/admin/functions/get-faculty';
</script>
<script src="{{asset('js/section-show.js')}}" defer></script>