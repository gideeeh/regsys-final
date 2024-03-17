<x-app-layout>
<div x-data="{
    manageSchedule:false,
    addSubjectToSection:false,
    selectedSubjectId: null,
    selectedSectionId: null,
    selectedSecSubId: null,
    selectedSubjectCode: '',
    selectedSubjectName: ''
}">

    <div>
        <h1>{{$section->section_name}}</h1>
        <h2>Section id: {{$section->section_id}}</h2>
        <p>Year Level: {{$section->year_level}}</p>
        <p>Academic Year: {{$section->academic_year}}</p>
        <p>Term: {{$section->term}}</p>
        <p>Section Type: {{$section->section_type->section_type}}</p>
    </div>
    @if($section->section_type->section_type==='block')
    <h1>Block</h1>
    <h2>Subjects Schedules</h2>
    <table>
        <thead>
            <tr>
                <th>Subject Code</th>
                <th>Subject Name</th>
                <th>Start Time F2F</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($blockSubjects as $blockSubject)
            <tr>
                <td>{{$blockSubject->subject->subject_code}}</td>
                <td>{{$blockSubject->subject->subject_name}}</td>
                <td>{{$blockSubject->subject->subject_name}}</td>
                <td>
                    <button @click="
                        manageSchedule=true; 
                        selectedSectionId={{$section->section_id}}; 
                        selectedSubjectId={{$blockSubject->subject->subject_id}}; 
                        selectedSubjectCode='{{$blockSubject->subject->subject_code}}'; 
                        selectedSubjectName='{{$blockSubject->subject->subject_name}}';" 
                        class='bg-green-500 text-white text-xs px-1 py-1 rounded hover:bg-green-600 transition ease-in-out duration-150'>Set Details</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else

    <!-- IF ITS FREEEEEEEEEEEEEEEEEE -->
    <h1>Not block</h1>
    <h2>Subjects Schedules</h2>
    <button @click="addSubjectToSection=true" class="bg-green-500 text-white text-xs px-1 py-1 rounded hover:bg-green-600 transition ease-in-out duration-150">Add Subject</button>
    <table>
        <thead>
            <tr>
                <th>Subject Code</th>
                <th>Subject Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sectionSubjects as $sectionSubject)
            <tr>
                <td>{{$sectionSubject->subject?->subject_code}}</td>
                <td>{{$sectionSubject->subject?->subject_name}}</td>
                <td>
                    <button @click="
                        manageSchedule=true; 
                        selectedSectionId={{$section->section_id}}; 
                        selectedSecSubId={{$sectionSubject->id}}; 
                        selectedSubjectId={{$sectionSubject->subject_id}};" 
                        class='bg-green-500 text-white text-xs px-1 py-1 rounded hover:bg-green-600 transition ease-in-out duration-150'>Set Details</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <h1>Section Details</h1>
    <table>
        <thead>
            <tr>
                <th>Subject Code</th>
                <th>Subject Name</th>
                <th>Start Time F2F</th>
                <th>End Time F2F</th>
                <th>Start Time OL</th>
                <th>End Time OL</th>
                <th>Professor</th>
                <th>Room</th>
                <th>Class Limit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sectionSubjects as $sectionSubject)
            <tr>
                <td>{{$sectionSubject->subject?->subject_code}}</td>
                <td>{{$sectionSubject->subject?->subject_name}}</td>
                <td>{{\Carbon\Carbon::parse($sectionSubject->subjectSectionSchedule?->start_time_f2f)->format('h:i A') ?? 'No recorded time.'}}</td>
                <td>{{\Carbon\Carbon::parse($sectionSubject->subjectSectionSchedule?->end_time_f2f)->format('h:i A') ?? 'No recorded time.'}}</td>
                <td>{{\Carbon\Carbon::parse($sectionSubject->subjectSectionSchedule?->start_time_online)->format('h:i A') ?? 'No recorded time.'}}</td>
                <td>{{\Carbon\Carbon::parse($sectionSubject->subjectSectionSchedule?->end_time_online)->format('h:i A') ?? 'No recorded time.'}}</td>
                <td>{{$sectionSubject->subjectSectionSchedule?->professor->first_name}} {{$sectionSubject->subjectSectionSchedule?->professor->last_name}}</td>
                <td>{{$sectionSubject->subjectSectionSchedule?->room}}</td>
                <td>{{$sectionSubject->subjectSectionSchedule?->class_limit}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>









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
    <!-- Manage Schedule Modal -->
    <div x-cloak id="manageSchedule" x-show="manageSchedule" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50" data-section-id="${subject.section_id}">
        <div class="modal-content bg-white p-8 rounded-lg shadow-lg overflow-auto min-wd-lg max-w-xl w-full min-h-[85vh] max-h-[90vh]">
            <h2>Manage Section</h2>
            @if($section->section_type->section_type==='free')
            <h1>Freeee</h1>
            <h1 >SecSubId: <span x-text="selectedSecSubId"></span></h1>

            @endif
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
                <h1 x-text="selectedSubjectCode"></h1>
                <h1 x-text="selectedSubjectName"></h1>
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
                                        <input type="checkbox" name="online_days[]" value="Wednesday" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                        <span class="text-gray-700">Thursday</span>
                                    </label>
                                </div>
                                <div>
                                    <label class="flex items-center space-x-3">
                                        <input type="checkbox" name="online_days[]" value="Wednesday" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                        <span class="text-gray-700">Friday</span>
                                    </label>
                                </div>
                                <div>
                                    <label class="flex items-center space-x-3">
                                        <input type="checkbox" name="online_days[]" value="Wednesday" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
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