<div x-data="{ searchTerm: '{{ $searchTerm ?? '' }}', showModal: false, showMore: false, showNotesModal: false, showNoteForm: false }">
<x-app-layout> 
    <div class="py-6 max-h-full">
        <div class="max-w-7xl py-6 mx-auto sm:px-6 lg:px-8" >
            <div class="flex bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <main class="indiv-student-panel">
                    <div class="stu-info flex">
                        <div class="img-frame w-3/12">
                            <img class="w-full" src="{{ asset('images/profile_pic_sample.jpg') }}" alt="{{$student->last_name}}">
                        </div>
                        <div class="stu-details w-9/12">
                            <h1>{{$student->first_name}} {{$student->middle_name}} {{$student->last_name}} {{$student->suffix}}</h1>
                            @isset($latestEnrollment)
                            <p>Student Number: {{ $student->student_number }}</p>
                            @if($latestEnrollment->latestEnrollment) {{-- Checking if latestEnrollment is set --}}
                                <p>Course: {{ $latestEnrollment->latestEnrollment->program->program_code }}</p>
                                <p>Year Level: {{ $latestEnrollment->latestEnrollment->year_level }}</p>
                                <p>Scholarship: {{ $latestEnrollment->latestEnrollment->scholarship_type }}</p>
                            @else
                                <p>Not Yet Enrolled</p>
                            @endif
                            @endisset
                            <a href="#" @click="showModal = true">Personal Information</a>
                            <div x-show="showModal" @click.away="showModal = false" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full" id="my-modal">
                                <div class="relative top-20 mx-auto p-5 border w-1/2 shadow-lg rounded-md bg-white">
                                    <div class="mt-3 text-left modal-content"> 
                                        <h2 class="text-xl leading-6 px-7 font-medium text-gray-900">
                                            {{$student->first_name}} {{$student->middle_name}} {{$student->last_name}}
                                        </h2>
                                        <div class="mt-2 px-7 py-3">
                                            <h3 class="text-lg">Contact Information</h3>
                                            <p>School Email: {{$student->school_email}}</p>
                                            <p>Personal Email: {{$student->personal_email}}</p>
                                            <p>Phone Number: {{$student->phone_number}}</p>
                                            <h3>Personal Information</h3>
                                            <p>Address: {{$student->house_num}} {{$student->street}}, {{$student->brgy}}, {{$student->city_municipality}}, {{$student->province}} {{$student->zipcode}}</p>
                                            <p>Birthday: {{$student->birthdate}}</p>
                                            <h3>Emergency Contacts</h3>
                                            <p>Guardian Name: {{$student->guardian_name}}</p>
                                            <p>Guardian Contact: {{$student->guardian_contact}}</p>
                                            <!-- Click to Show More -->
                                            <button 
                                                @click="showMore = !showMore" 
                                                class="mt-4 px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded hover:bg-blue-700 focus:outline-none focus:bg-blue-700"
                                                x-text="showMore ? 'Show Less' : 'Show More'"
                                            >
                                            </button>

                                            <!-- Extra Information (conditionally rendered) -->
                                            <div x-show="showMore" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none;">
                                                <h3 class="text-lg">Additional Information</h3>
                                                <p>Marital Status: {{$student->civil_status}}</p>
                                                <p>Nationality: {{$student->nationality}}</p>
                                                <p>Sex: {{$student->sex}}</p>
                                                <p>Religion: {{$student->religion}}</p>
                                                <p>Elementary: {{$student->elementary}}</p>
                                                <p>Elem Year Grad: {{$student->elem_yr_grad}}</p>
                                                <p>High School: {{$student->highschool}}</p>
                                                <p>HS Year Grad: {{$student->hs_yr_grad}}</p>
                                                <p>College: {{$student->college}}</p>
                                                <p>College Year Final Year: {{$student->collge_year_ended}}</p>
                                                <!-- Add other additional fields here -->
                                            </div>

                                            <!-- Add more personal information fields here -->
                                        </div>
                                        <div class="items-center px-4 py-3">
                                            <button @click="showModal = false" id="ok-btn" class="px-4 py-2 bg-gray-800 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-300">
                                                Close
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                     <div class="stu-academic-info mt-4">
                        <h3 class="text-lg mb-2">Academic History</h3>
                        @foreach($enrollmentDetails->groupBy('year_level') as $yearLevel => $yearDetails)
                        <h1>{{ ordinal($yearLevel) }} Year</h1>
                            @foreach($yearDetails->groupBy('term') as $term => $details)
                            <h2>Term: {{ ordinal($term) }}</h2>
                            <table class="min-w-full border-collapse border border-gray-300">
                                <thead>
                                    <tr>
                                        <th class="border border-gray-300 px-4 py-2">Subject Code</th>
                                        <th class="border border-gray-300 px-4 py-2">Subject Name</th>
                                        <th class="border border-gray-300 px-4 py-2">Prerequisite 1</th>
                                        <th class="border border-gray-300 px-4 py-2">Prerequisite 2</th>
                                        <th class="border border-gray-300 px-4 py-2">Units (Lec)</th>
                                        <th class="border border-gray-300 px-4 py-2">Units (Lab)</th>
                                        <th class="border border-gray-300 px-4 py-2">Total Units</th>
                                        <th class="border border-gray-300 px-4 py-2">Final Grade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($details as $detail)
                                    <tr>
                                        <td class="border border-gray-300 px-4 py-2">{{ $detail->subject_code }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $detail->subject_name }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $detail->Prerequisite_Name_1 ?? '-' }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $detail->Prerequisite_Name_2 ?? '-' }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $detail->units_lec }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $detail->units_lab }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $detail->TOTAL }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $detail->final_grade }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            @endforeach
                        @endforeach
                    </div>
                </main>
                <aside class="indiv-student-sidepanel">
                    <div class="stu-notes">
                        <h3 @click="showNotesModal = true" class="cursor-pointer">Notes For This Student</h3>
                        <div x-show="showNotesModal" @click.away="showNotesModal = false" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
                            <div class="relative top-20 mx-auto p-5 border w-1/2 shadow-lg rounded-md bg-white">
                                <div class="modal-content"> 
                                    <h2 class="text-xl leading-6 px-7 font-medium text-gray-900">Notes for {{$student->first_name}}</h2>
                                    <div class="mt-2 px-7 py-3">
                                        <!-- Button to toggle note creation form -->
                                        <button @click="showNoteForm = !showNoteForm" class="mb-4 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                            Add Note
                                        </button>

                                        <!-- Form for adding a new note, hidden initially -->
                                        <div x-show="showNoteForm">
                                            <form action="{{ route('student-notes.store', ['student_id' => $student->student_id]) }}" method="POST">
                                                @csrf <!-- Laravel's CSRF token input -->
                                                <div class="mb-4">
                                                    <label for="note_title" class="block text-sm font-medium text-gray-700">Note Title</label>
                                                    <input type="text" id="note_title" name="note_title" placeholder="Note title" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md" autocomplete="off">
                                                    <label for="note" class="block text-sm font-medium text-gray-700 mt-2">Note Content</label>
                                                    <textarea id="note" name="note" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md" placeholder="Enter note here..."></textarea>
                                                </div>
                                                <div class="px-4 py-3 text-right sm:px-6">
                                                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                        Save Note
                                                    </button>
                                                </div>
                                            </form>
                                        </div>

                                        <!-- Display existing notes -->
                                        <div class="mt-4">
                                            <h3 class="text-lg font-medium text-gray-900">Existing Notes</h3>
                                            @forelse ($latestEnrollment->notes as $note)
                                                <div class="mt-2">
                                                    <p class="text-sm text-gray-600"><strong>{{ $note->note_title }}</strong>: {{ $note->note }}
                                                    <span class="text-xs text-gray-500">{{ $note->created_at->format('M. j, Y - g:i A') }}</span></p>
                                                </div>
                                            @empty
                                                <p>No notes available.</p>
                                            @endforelse
                                        </div>
                                    </div>
                                    <div class="items-center px-4 py-3">
                                        <button @click="showNotesModal = false" class="px-4 py-2 bg-gray-800 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-300">
                                            Close
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h3>Student's Concerns</h3>
                        <h3>Appointment Schedule</h3>
                        <p>First 50 characters. Clickable for show more</p>
                    </div>
                    <div class="stu-functions">
                        <a href="#">View Current COR</a>
                        <a href="#">View Files</a>
                        <a href="#">Released Records</a>
                        <a href="#">Issue Exam Permit</a>
                        <a href="#">Issue COR</a>
                        <a href="#">Issue Gradeslip</a>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</x-app-layout>
</div>
