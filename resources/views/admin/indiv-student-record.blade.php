<div 
    x-data="{ 
        searchTerm: '{{ $searchTerm ?? '' }}', 
        showModal: false, 
        deleteFileModal:false, 
        showMore: false, 
        showNotesModal: false, 
        showNoteForm: false, 
        selectedFile: null, 
    }" 
    @keydown.escape.window="
        showModal=false;
        showNotesModal=false;
    ">
<x-app-layout> 
    <div class="py-1 max-h-full">
        <div class="max-w-7xl py-4 mx-auto sm:px-6 lg:px-8" >
            <div class="flex overflow-hidden">
                <main class="indiv-student-panel">
                    <!-- Student info section -->
                    <div class="relative stu-info-background stu-info flex bg-white border border-1 rounded-lg p-6 gap-6 cursor-default" style="background-image: url('{{ asset('images/stu-profile-background.webp') }}'); background-color: rgba(255, 255, 255, 0.5); background-blend-mode: lighten; background-size: cover; background-repeat: no-repeat; background-position: center bottom -25vh;">                        
                        <div class="img-frame w-3/12 flex justify-center items-center">
                            @php
                                $profileImageExists = false;
                                $preferredExtensions = ['jpeg', 'png', 'jpg'];
                                foreach ($preferredExtensions as $extension) {
                                    if (Storage::disk('local')->exists("{$student->file_path}/profile.{$extension}")) {
                                        $profileImageExists = true;
                                        $profileImagePath = route('student.image', ['studentId' => $student->student_id, 'filename' => "profile.{$extension}"]);
                                        break;
                                    }
                                }
                            @endphp
                            <div class="flex justify-center items-center border-none">
                                @if($profileImageExists)
                                <img src="{{ $profileImagePath }}" alt="{{ $student->last_name }}" class="rounded-full border border-1 border-sky-950 p-1" style="width: 100%;" >
                                @else
                                    <img class="w-full" src="{{ asset('images/profile_pic_sample.jpg') }}" alt="{{ $student->last_name }}">
                                @endif
                            </div>
                        </div>
                        <div class="stu-details w-9/12 flex flex-col justify-between">
                            <div class="mb-2">
                                @if($student->middle_name)
                                <h2>{{$student->first_name}} {{substr($student->middle_name,0,1)}}. {{$student->last_name}} {{$student->suffix}}</h2>
                                @else
                                <h2>{{$student->first_name}} {{$student->last_name}} {{$student->suffix}}</h2>
                                @endif
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
                            @if(Auth::check() && Auth::user()->role === 'admin')
                            <a 
                                onclick="event.stopPropagation(); 
                                window.open('{{ route('student.edit', $student->student_id) }}', '_blank');" 
                                class="cursor-pointer absolute top-2 right-2 text-xs p-1 rounded bg-slate-300 text-white hover:bg-slate-400 transition ease-in-out duration-150">
                                Update
                            </a>
                            @endif
                        </div>
                    </div>
                    <!-- Academic Info Section -->



                    <div class="stu-academic-info mt-4 bg-white border border-1 rounded-lg p-6 gap-4 cursor-default lg:min-h-[45h] md:min-h-[38vh] sm:min-h-[31vh]">
                        <h3 class="flex w-full justify-center bg-sky-600 px-4 rounded-md text-white mb-4">Academic History</h3>
                        @foreach($organizedEnrollments as $programName => $data)
                            <h2 class="text-center bg-sky-950 rounded-md text-white p-1">{{ $programName }}</h2> 
                            <div class="flex justify-end">
                            @if(Auth::check() && Auth::user()->role === 'admin')
                                <a 
                                    href="{{ url('/tor/pdf/print/'. $student->student_id .'/' . $data['programId']) }}" 
                                    class="bg-neutral-500 text-white text-sm text-lg p-1 rounded hover:bg-neutral-600 transition ease-in-out duration-150">
                                    Print TOR
                                </a>
                            @endif
                            </div>
                            @foreach($data['years'] as $yearLevel => $terms)
                                <div>
                                    @php
                                        switch($yearLevel) {
                                            case 1:
                                                $year_level_name = "1st";
                                                break;
                                            case 2:
                                                $year_level_name = "2nd";
                                                break;
                                            case 3:
                                                $year_level_name = "3rd";
                                                break;
                                            case 4:
                                                $year_level_name = "4th";
                                                break;
                                            default:
                                                $year_level_name = $yearLevel . "th"; // Fallback for other numbers
                                        }
                                    @endphp
                                    <h2 class="text-center"><strong>{{ $year_level_name }} Year</strong></h2>
                                    @foreach($terms as $term => $subjects)
                                    <div class="mb-6">
                                        <p class="text-md mb-2 pl-4"><strong>Term {{ $term }}</strong></p>
                                        <div class="border-2 border-slate-300 rounded-md shadow-md">
                                            <table class="min-w-full table-auto">
                                                <thead class="text-left bg-blue-500 text-white">
                                                    <tr>
                                                        <th class="border px-4 py-2">Subject Code</th>
                                                        <th class="border px-4 py-2">Subject Name</th>
                                                        <th class="border px-4 py-2">Grade</th>
                                                        <th class="border px-4 py-2">Remarks</th>
                                    
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($subjects as $enrolledSubject)
                                                        <tr>
                                                            <td class="border px-4 py-2">{{ $enrolledSubject->subject->subject_code }}</td>
                                                            <td class="border px-4 py-2">{{ $enrolledSubject->subject->subject_name }}</td>
                                                            <td class="border px-4 py-2">{{ $enrolledSubject->final_grade ?? 'Not Graded' }}</td>
                                                            <td class="border px-4 py-2">{{ $enrolledSubject->remarks !== null && $enrolledSubject->remarks !== '' ? $enrolledSubject->remarks : 'No remarks' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                </main>

                <aside class="indiv-student-sidepanel cursor-default">
                    <div class="stu-notes bg-white border border-1 rounded-lg p-6 mb-4 lg:min-h-[47h] md:min-h-[40vh] sm:min-h-[33vh] lg:max-h-[47h] md:max-h-[40vh] sm:max-h-[33vh] overflow-y-auto nice-scroll">
                        <div class="flex justify-between mb-6">
                            <span class="font-semibold">Student Notes</span>
                            @if(Auth::check() && Auth::user()->role === 'admin')
                            <button @click="showNotesModal = true" class="cursor-pointer bg-green-500 font-semibold text-xs rounded-md text-white p-1 hover:bg-green-600 transition ease-in-out duration-150">Manage</button>
                            @endif
                        </div>
                        <div class="display-notes text-sm space-y-4 mb-4">
                            @if($notes)
                            @foreach($notes as $note)
                            @if($notes->count() > 1)
                            <div class="border-b-2 border-slate-400 pb-4">
                            @else
                            <div>
                            @endif
                                <p><strong>Title:</strong> <span class="text-xs">{{$note->note_title}}</span></p>
                                <p><strong>Date:</strong> <span class="text-xs">{{\Carbon\Carbon::parse($note->created_at)->format('M d, Y') ?? 'No recorded note.'}}</span></p>
                                <p><strong>Note:</strong> <span class="text-xs">{{$note->note}}</span></p>
                            </div>
                            @endforeach
                            @else
                            <div>
                                <p>No available notes</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="bg-white border border-1 rounded-lg p-6 lg:min-h-[45h] md:min-h-[38vh] sm:min-h-[31vh] lg:max-h-[45h] md:max-h-[38vh] sm:max-h-[31vh] overflow-y-auto nice-scroll">
                        <div class="flex justify-between">
                            <span class="font-semibold">File Records</span>
                            @if(Auth::check() && Auth::user()->role === 'admin')
                            <form action="{{ route('student-files.upload', $student->student_id) }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="flex justify-between">
                                    <label class="cursor-pointer bg-purple-500 font-semibold text-xs rounded-md text-white p-1 hover:bg-purple-600 transition ease-in-out duration-150">
                                        + Add File
                                        <input type="file" name="file" style="display: none;" onchange="form.submit()">
                                    </label>
                                </div>
                            </form>
                            @endif
                            <!-- <button class="cursor-pointer bg-purple-500 font-semibold text-xs rounded-md text-white p-1 hover:bg-purple-600 transition ease-in-out duration-150">+ Add File</button> -->
                        </div>
                        <div>
                            @foreach($files as $file)
                                @php
                                    $displayName = strlen($file->file_name) > 15 ? substr($file->file_name, 0, 15) . '...' . $file->file_extension : $file->file_name;
                                @endphp
                                <div class="flex justify-between border-b-2 hover:rounded rounded-none hover:text-white hover:bg-sky-300 hover:border-sky-300 items-center pl-1"
                                @click.stop="window.location.href='{{ route('student-files.download', ['studentId' => $student->student_id, 'filename' => $file->file_name]) }}'"
                                    style="cursor:pointer;">
                                    <span class="text-xs text-sm rounded-md block">{{ $displayName }}</span>
                                    <div @click.stop="deleteFileModal=true; selectedFile={{$file->id}}" class="px-1 text-center text-sm text-red-500 hover:text-red-800 font-bold cursor-pointer" title="Delete file">
                                        <span>x</span>
                                    </div>
                                </div>
                            @endforeach
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
                    <h3 class="bg-sky-950 text-center text-white rounded-md mb-4">Contact Information</h3>
                    <div class="flex justify-between gap-4">
                        <div class="mb-6 w-1/2">
                            <p class="mb-1"><strong>Personal Email:</strong> <span class="text-sm">{{$student->personal_email ?? 'Record not found'}}</span></p>
                            <p><strong>School Email:</strong> <span class="text-sm">{{$student->school_email ?? 'Record not found'}}</span></p>
                        </div>
                        <div class="w-1/2">
                            <p><strong>Phone Number:</strong>
                                <span class="text-sm">
                                    @if(isset($student->phone_number) && strlen($student->phone_number) === 11)
                                        {{ substr($student->phone_number, 0, 4) }} {{ substr($student->phone_number, 4, 3) }} {{ substr($student->phone_number, 7) }}
                                    @else
                                        {{ $student->phone_number ?? 'Record not found' }}
                                    @endif
                                </span>
                            </p>
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
                    @if($student->birthdate)
                    <p class="mb-1"><strong>Birthdate:</strong> <span class="text-sm">{{ \Carbon\Carbon::parse($student->birthdate)->format('M d, Y') }}</span></p>
                    @else
                    <p class="mb-1"><strong>Birthdate:</strong> <span class="text-sm">Record not found</span></p>
                    @endif
                    <p class="mb-1"><strong>Guardian Name:</strong> <span class="text-sm">{{$student->guardian_name ?? 'Record not found'}}</span></p>
                    <p class="mb-1"><strong>Guardian Contact:</strong>
                        <span class="text-sm">
                            @if(isset($student->guardian_contact) && strlen($student->guardian_contact) === 11)
                                {{ substr($student->guardian_contact, 0, 4) }} {{ substr($student->guardian_contact, 4, 3) }} {{ substr($student->guardian_contact, 7) }}
                            @else
                                {{ $student->guardian_contact ?? 'Record not found' }}
                            @endif
                        </span>
                    </p>                    <!-- Click to Show More -->
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
                            @if($student)
                                @if($student->sex=='M')
                                    <p><strong>Sex:</strong> <span class="text-sm">{{'Male'}}</span></p>
                                @elseif($student->sex=='F')
                                    <p><strong>Sex:</strong> <span class="text-sm">{{'Female'}}</span></p>
                                @endif
                            @else
                                <p><strong>Sex:</strong> <span class="text-sm">{{'Record not found'}}</span></p>
                            @endif
                            <p><strong>Religion:</strong> <span class="text-sm">{{$student->religion ?? 'Record not found'}}</span></p>
                        </div>
                    </div>    
                    <h3 class="bg-sky-600 text-center text-white rounded-md mb-4">Education Background</h3>
                    <div>
                        <div class="mb-4">
                            <p><strong>Elementary:</strong> <span class="text-sm">{{$student->elementary ?? 'Record not found'}}</span></p>
                            <p><strong>Year Grad:</strong> <span class="text-sm">{{$student->elem_yr_grad ?? 'Record not found'}}</span></p>
                        </div>
                        <div class="mb-4">
                            <p><strong>Jr. High School:</strong> <span class="text-sm">{{$student->jr_highschool ?? 'Record not found'}}</span></p>
                            <p><strong>Year Grad:</strong> <span class="text-sm">{{$student->jr_hs_yr_grad ?? 'Record not found'}}</span></p>
                        </div>
                        <div class="mb-4">
                            <p><strong>Sr. High School:</strong> <span class="text-sm">{{$student->sr_highschool ?? 'Record not found'}}</span></p>
                            <p><strong>Year Grad:</strong> <span class="text-sm">{{$student->sr_hs_yr_grad ?? 'Record not found'}}</span></p>
                        </div>
                        <div class="mb-4">
                            <p><strong>College:</strong> <span class="text-sm">{{$student->college ?? 'Record not found'}}</span></p>
                            <p><strong>Final Year/Grad:</strong> <span class="text-sm">{{$student->college_year_ended ?? 'Record not found'}}</span></p>
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
                    <button type="button" @click="showNotesModal = false" class="modal-close-btn bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">Close</button>
                    <button type="submit" id="submitBtn" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition ease-in-out duration-150">Create Note</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Delete Modal -->

    <div x-cloak x-show="deleteFileModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
        <div class="modal-content bg-white p-8 rounded-lg shadow-lg overflow-auto max-w-md w-full">
            <h3 class="text-lg font-bold mb-4">Confirm Deletion</h3>
            <p>Are you sure you want to delete this Student?</p>
            <div class="flex justify-end space-x-4 mt-4">
                <form :action="'/admin/files/delete/' + selectedFile" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="deleteFileModal = false" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">Cancel</button>
                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition ease-in-out duration-150">Delete</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
</div>
