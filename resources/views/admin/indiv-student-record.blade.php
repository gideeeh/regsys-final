<div x-data="{ searchTerm: '{{ $searchTerm ?? '' }}', showModal: false, showMore: false, showNotesModal: false, showNoteForm: false }">
<x-app-layout> 
    <div class="py-1 max-h-full">
        <div class="max-w-7xl py-4 mx-auto sm:px-6 lg:px-8" >
            <div class="flex overflow-hidden">
                <main class="indiv-student-panel">
                    <!-- Student info section -->
                    <div class="relative stu-info-background stu-info flex bg-white border border-1 rounded-lg p-6 gap-4 cursor-default" style="background-image: url('{{ asset('images/stu-profile-background.webp') }}'); background-color: rgba(255, 255, 255, 0.5); background-blend-mode: lighten; background-size: cover; background-repeat: no-repeat; background-position: center bottom -25vh;">                        
                        <div class="img-frame w-3/12 flex justify-center items-center border border-1">
                            <img class="w-full" src="{{ asset('images/profile_pic_sample.jpg') }}" alt="{{$student->last_name}}">
                        </div>
                        <div class="stu-details w-9/12 flex flex-col justify-between">
                            <div class="mb-2">
                                <h1>{{$student->first_name}} {{$student->middle_name}} {{$student->last_name}} {{$student->suffix}}</h1>
                                <div class="flex justify-between gap-4">
                                    <div class="w-1/2">
                                        <p class="mb-1"><strong class="text-slate-600">Student Number:</strong> <span class="text-sm">{{ $student->student_number }}</span></p>
                                        <p><strong class="text-slate-600">Course:</strong> <span class="text-sm">{{ $latestEnrollment->latestEnrollment->program->program_code ?? 'No enrollment history'}}</span></p>
                                    </div>
                                    <div class="w-1/2">
                                        <p class="mb-1"><strong class="text-slate-600">Year Level:</strong> <span class="text-sm">{{ $latestEnrollment->latestEnrollment->year_level ?? 'No enrollment history' }}</span></p>
                                        <p><strong class="text-slate-600">Scholarship:</strong> <span class="text-sm">{{ $latestEnrollment->latestEnrollment->scholarship_type ?? 'No enrollment history' }}</span></p>
                                    </div>
                                </div>
                            </div>
                            <div>
                                @if($student->city_municipality &&  $student->province &&  $student->brgy)
                                <span class="text-sm text-slate-600">{{$student->brgy}}, {{$student->city_municipality}}, {{$student->province}}</span>
                                @else
                                <span class="text-sm text-slate-600">No recorded address</span>
                                @endif
                                <span class="mx-1">&bull;</span>
                                <a href="#" @click="showModal = true" >
                                    <span class="mt-4 bg-sky-500 text-white text-sm p-1 rounded hover:bg-sky-600 transition ease-in-out duration-150">Personal Information</span>
                                </a>
                            </div>
                            <a onclick="event.stopPropagation(); window.open('{{ route('student.edit', $student->student_id) }}', '_blank');" class="cursor-pointer absolute top-2 right-2 text-xs p-1 rounded bg-slate-300 text-white hover:bg-slate-400 transition ease-in-out duration-150">Update</a>
                        </div>
                    </div>
                    <!-- Academic Info Section -->
                     <div class="stu-academic-info mt-4 bg-white border border-1 rounded-lg p-6 gap-4 cursor-default lg:min-h-[45h] md:min-h-[38vh] sm:min-h-[31vh]">
                        <h3 class="flex w-full justify-center bg-sky-600 px-4 rounded-md text-white mb-6">Academic History</h3>
                        @if($enrollmentDetails->isNotEmpty())
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
                        @else
                        <h3 class="mt-16 text-rose-600 text-center bg-slate-100">Student Has No Enrollment History.</h3>
                        @endif
                    </div>
                </main>
                <aside class="indiv-student-sidepanel cursor-default">
                    <div class="stu-notes bg-white border border-1 rounded-lg p-6 mb-4">
                        <div class="flex justify-between mb-6">
                            <span class="font-semibold">Student Notes</span>
                            <button @click="showNotesModal = true" class="cursor-pointer bg-green-500 font-semibold text-xs rounded-md text-white p-1 hover:bg-green-600 transition ease-in-out duration-150">Manage</button>
                        </div>
                        <div class="display-notes text-sm mb-4 overflow-y-auto nice-scroll">
                            @if($notes)
                            @foreach($notes as $note)
                            <div>
                                <p><strong>Title:</strong> {{$note->note_title}}</p>
                                <p>{{$note->note}}</p>
                            </div>
                            @endforeach
                            @else
                            <div>
                                <p>No available notes</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="bg-white border border-1 rounded-lg p-6 lg:min-h-[45h] md:min-h-[38vh] sm:min-h-[31vh]">
                        <div class="flex justify-between mb-6 xl:min-h-[24h]">
                            <span class="font-semibold">File Records</span>
                            <button class="cursor-pointer bg-purple-500 font-semibold text-xs rounded-md text-white p-1 hover:bg-purple-600 transition ease-in-out duration-150">+ Add File</button>
                        </div>
                        <div class="overflow-y-auto nice-scroll">

                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
    <!-- Show Personal Information Modal -->
    <div x-cloak x-show="showModal" @click.away="showModal = false" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full" id="my-modal">
        <div class="relative top-20 mx-auto p-5 border w-3/5 shadow-lg rounded-md bg-white cursor-default">
            <div class="mt-3 text-left modal-content">
                <div class="px-7">
                    <h3 class="bg-sky-600 text-center text-white rounded-md mb-4">Contact Information</h3>
                    <div class="flex justify-between gap-4">
                        <div class="mb-6 w-1/2">
                            <p class="mb-1"><strong>Personal Email:</strong> <span class="text-sm">{{$student->personal_email ?? 'Record not found'}}</span></p>
                            <p><strong>School Email:</strong> <span class="text-sm">{{$student->school_email ?? 'Record not found'}}</span></p>
                        </div>
                        <div class="w-1/2">
                            <p><strong>Phone Number:</strong> <span class="text-sm">{{$student->phone_number ?? 'Record not found'}}</span></p>
                        </div>
                    </div>
                    <h3 class="bg-sky-600 text-center text-white rounded-md mb-4">Personal Information</h3>
                    <div class="mb-1">
                        <span><strong>Address:</strong> </span>
                        @if(!$student->house_num && !$student->street  && !$student->brgy  && !$student->city_municipality  && !$student->province  && !$student->zipcode )
                        <span class="text-sm">No address data recorded.</span>
                        @else
                        @if($student->house_num)
                        <span class="text-sm">{{$student->house_num}} </span>
                        @endif
                        @if($student->street)
                        <span class="text-sm">{{$student->street}}, </span>
                        @endif
                        @if($student->brgy)
                        <span class="text-sm">{{$student->brgy}}, </span>
                        @endif
                        @if($student->city_municipality)
                        <span class="text-sm">{{$student->city_municipality}}, </span>
                        @endif
                        @if($student->province)
                        <span class="text-sm">{{$student->province}} </span>
                        @endif
                        @if($student->zipcode)
                        <span class="text-sm">{{$student->zipcode}} </span>
                        @endif
                        @endif
                    </div>
                    <p class="mb-1"><strong>Birthdate:</strong> <span class="text-sm">{{$student->birthdate ?? 'No birthdate record.'}}</span></p>
                    <p class="mb-1"><strong>Guardian Name:</strong> <span class="text-sm">{{$student->guardian_name ?? 'Record not found'}}</span></p>
                    <p class="mb-1"><strong>Guardian Contact:</strong> <span class="text-sm">{{$student->guardian_contact ?? 'Record not found' }}</span></p>
                    <!-- Click to Show More -->
                    <!-- <div class="flex justify-center">
                        <button 
                        @click="showMore = !showMore" 
                        class="w-full mt-4 px-4 bg-blue-500 text-white text-sm font-medium rounded hover:bg-blue-700 focus:outline-none focus:bg-blue-700"
                        x-text="showMore ? 'Show Less' : 'Show More'"
                        >
                        </button>
                    </div> -->

                    <!-- Extra Information (conditionally rendered) -->
                    <!-- <div x-show="showMore" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none;"> -->
                    <div class="my-6 flex justify-between gap-4">
                        <div class="w-1/2">
                            <p><strong>Marital Status:</strong> <span class="text-sm">{{$student->civil_status ?? 'Record not found'}}</span></p>
                            <p><strong>Nationality:</strong> <span class="text-sm">{{$student->nationality ?? 'Record not found'}}</span></p>
                        </div>
                        <div class="w-1/2">
                            <p><strong>Sex:</strong> <span class="text-sm">{{$student->sex ?? 'Record not found'}}</span></p>
                            <p><strong>Religion:</strong> <span class="text-sm">{{$student->religion ?? 'Record not found'}}</span></p>
                        </div>
                    </div>    
                    <h3 class="bg-sky-600 text-center text-white rounded-md mb-4">Education Background</h3>
                    <div>
                        <div class="mb-4">
                            <p class="w-1/2"><strong>Elementary:</strong> <span class="text-sm">{{$student->elementary ?? 'Record not found'}}</span></p>
                            <p class="w-1/2"><strong>Year Grad:</strong> <span class="text-sm">{{$student->elem_yr_grad ?? 'Record not found'}}</span></p>
                        </div>
                        <div class="mb-4">
                            <p class="w-1/2"><strong>Jr. High School:</strong> <span class="text-sm">{{$student->hr_highschool ?? 'Record not found'}}</span></p>
                            <p class="w-1/2"><strong>Year Grad:</strong> <span class="text-sm">{{$student->jr_hs_yr_grad ?? 'Record not found'}}</span></p>
                        </div>
                        <div class="mb-4">
                            <p class="w-1/2"><strong>Sr. High School:</strong> <span class="text-sm">{{$student->sr_highschool ?? 'Record not found'}}</span></p>
                            <p class="w-1/2"><strong>Year Grad:</strong> <span class="text-sm">{{$student->sr_hs_yr_grad ?? 'Record not found'}}</span></p>
                        </div>
                        <div class="mb-4">
                            <p class="w-1/2"><strong>College:</strong> <span class="text-sm">{{$student->college ?? 'Record not found'}}</span></p>
                            <p class="w-1/2"><strong>Final Year/Grad:</strong> <span class="text-sm">{{$student->collge_year_ended ?? 'Record not found'}}</span></p>
                        </div>
                    </div>
                </div>
                <div class="flex justify-center px-7 mt-12">
                    <button 
                        @click="showModal = false" 
                        id="ok-btn" 
                        class="px-4 py-2 bg-gray-800 text-white font-medium rounded-md w-full shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Student Notes Modal -->
    <!-- <div x-show="showNotesModal" @click.away="showNotesModal = false" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full"> -->
    <div x-cloak x-show="showNotesModal" id="showNotesModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
        <div class="modal-content bg-white p-8 rounded-lg shadow-lg overflow-auto max-w-md w-full xl:min-h-[64h] lg:min-h-[57h] md:min-h-[50vh] sm:min-h-[43vh]">
            <form action="{{ route('student-notes.store', ['student_id' => $student->student_id]) }}" method="POST">
                <h2 class="mb-4 text-2xl">Create a Note</h2>
                @csrf <!-- Laravel's CSRF token input -->
                <div class="mb-6">
                    <label for="note_title" class="block text-sm font-medium text-gray-700">Note Title</label>
                    <input type="text" id="note_title" name="note_title" placeholder="Note title" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md" required autocomplete="off">
                    <label for="note" class="block text-sm font-medium text-gray-700 mt-2">Note Content</label>
                    <textarea id="note" name="note" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md" required placeholder="Enter note here..."></textarea>
                </div>
                <div class="flex justify-end gap-4">
                    <button type="submit" id="submitBtn" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition ease-in-out duration-150">Create Note</button>
                    <button type="button" @click="showNotesModal = false" class="modal-close-btn bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">Close</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
</div>
