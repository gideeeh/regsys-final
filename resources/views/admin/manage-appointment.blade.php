@extends('admin.appointments-partials')
@section('content')
<div 
    x-data="{responseModal:false}"
    @keydown.escape.window="responseModal=false" 
    class="flex gap-4">
    <div class="w-8/12 flex flex-col justify-between overflow-y-auto nice-scroll max-h-[80vh]">
        <div class="mb-3 bg-white shadow-sm sm:rounded-lg p-4 min-h-[18vh]">
            <div class="flex mb-2">
                <div class="w-1/2">
                    <p><strong>Name:</strong> {{ $student->student->first_name }} {{$student->student->last_name}}</p>
                    <p><strong>Student No.:</strong> {{ $student->student->student_number }}</p>
                </div>
                <div class="w-1/2">
                    @if($student)
                    <p><strong>Program & Year:</strong><span class="text-center"> {{ $student->student->latestEnrollment->program->program_code }} - {{ $student->student->latestEnrollment->year_level }}</span></p>
                    @else
                    <p><strong>Program & Year:</strong> <span class="text-sm">No enrollment record</span></p>
                    @endif
                    <p><strong>Email:</strong> <span class="text-sm"> {{ $student->student->personal_email }}</span></p>
                </div>
            </div>
            <div class="space-x-6">
                <a href="/admin/student-records/{{$student->student_id}}" class="underline text-xs" target="_blank">Visit Student Profile</a>
                <a href="/admin/enrollment-records/{{$student->student_id}}" class="underline text-xs" target="_blank">Visit Enrollment Records</a>
            </div>
        </div>
        <div class="flex flex-col justify-between highlight-appointment w-full bg-white shadow-sm sm:rounded-lg p-4 rounded-md min-h-[60vh]">
            <!-- Show the selected appointment here -->
            <div id="selected-appointment" class="selected-appointment">
                <h3 class="flex justify-center mb-4 text-lg">Appointment Details</h3>
                <div class="flex gap-4 mb-4">
                    <div class="w-1/2 text-sm">
                        <p><strong>Concern:</strong> {{ $appointment->concern }}</p>
                        <p><strong>Date & Time:</strong> {{ \Carbon\Carbon::parse($appointment->appointment_datetime)->format('M d, Y g:i A') }}</p>
                    </div>
                    <div class="w-1/2 text-sm">
                        <p><strong>Status:</strong> {{ ucfirst($appointment->status) }}</p>
                        <p><strong>Appt Code:</strong> {{$appointment->appointment_code}}</p>
                    </div>
                </div>
                <div class="mb-4 space-y-1 text-sm">
                    <p><strong>Notes:</strong></p>
                    <p class="text-sm">{{$appointment->notes ?? 'No available notes.'}}</p>
                </div>
                <div class="text-sm">
                    <p><strong>Response Log(s): </strong></p>
                    <div class="mb-4 border sm:min-h-[11vh] md:min-h-[14vh] lg:min-h-[17vh] xl:min-h-[20vh] text-sm">
                        @foreach($appointment_responses as $response)
                        @if($response->user_id === $user->id)
                        <div class="mb-2 overflow-y-scroll nice-scroll p-2">
                            <p><span class="font-bold">You: </span><span class="text-xs text-gray-400">[Sent at: {{ \Carbon\Carbon::parse($response->created_at)->format('M d, Y g:i A') }}]</span></p>
                            <p>{{$response->response_message ?? 'No available notes.'}}</p>
                        </div>
                        @else
                        <div class="mb-2 overflow-y-scroll nice-scroll p-2">
                            <p><span class="font-bold">{{$response->user->first_name}} {{$response->user->last_name}}</span><span class="text-xs text-gray-400">[Sent at: {{$response->created_at}}]</span></p>
                            <p>{{$response->response_message ?? 'No available notes.'}}</p>
                        </div>
                        @endif
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-2">
                <button @click="responseModal=true" class="bg-sky-500 text-white text-sm p-1 rounded hover:bg-sky-600 transition ease-in-out duration-150">Respond</button>
                <button class="bg-green-500 text-white text-sm p-1 rounded hover:bg-green-600 transition ease-in-out duration-150">Complete</button><!-- Disabled until there is a response -->
            </div>
        </div>
    </div>

    <div class="w-4/12 max-h-[80vh] min-h-[80vh] flex flex-col justify-between">
        <div class="queue-container bg-white shadow-sm max-h-[39vh] min-h-[39vh] sm:rounded-lg p-4 overflow-y-auto nice-scroll cursor-default">
            <h3 class="text-md">Appointment History</h3>
            <div class="mb-2 text-sm">
                <button activeClassSelect=true class="bg-red-500 text-white text-md px-2 rounded hover:bg-red-600 transition ease-in-out duration-150">Ongoing</button>
                <button activeClassSelec=false class="bg-gray-500 text-white px-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">Complete</button>
            </div>
            <div class="appointments-history">
                <div>
                @foreach($appt_history as $appointment)
                <div id="appointment-{{ $appointment->id }}" class="appointment p-2 text-sm">
                    <!-- <p>Status: {{ ucfirst($appointment->status) }}</p> -->
                    <p><strong>Concern:</strong> {{ $appointment->concern }}</p>
                    <p><strong>Appt. Sched:</strong> {{ \Carbon\Carbon::parse($appointment->appointment_datetime)->format('M d, Y g:i A') }}</p>
                </div>
                @endforeach
                </div>
            </div>
        </div>
        <div class="pending-container bg-white shadow-sm max-h-[39vh] min-h-[39vh] sm:rounded-lg p-4 overflow-y-auto nice-scroll cursor-default">
            <h3 class="text-md mb-2">Shared File(s) List</h3>
            <!-- <div class="mb-2 text-sm">
                <button activeClassSelect=true class="actv-pending-btn one-day-button active-pendingDay bg-red-500 text-white text-md px-2 rounded hover:bg-red-600 transition ease-in-out duration-150'">1 Day</button>
                <button activeClassSelec=false class="actv-pending-btn two-days-button bg-gray-500 text-white px-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">2 Days</button>
                <button activeClassSelec=false class="actv-pending-btn beyond-two-days-button bg-gray-500 text-white px-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">2 Days +</button>
            </div> -->
            <!-- List of files submitted -->
            <div class="files-submitted">
                <div class="text-sm overflow-y-scroll nice-scroll p-2 border min-h-[26vh] cursor-pointer">
                    @forelse($files as $file)
                        @php
                            $fullName = $file->getFilename();
                            $extension = pathinfo($fullName, PATHINFO_EXTENSION);
                            $nameWithoutExtension = pathinfo($fullName, PATHINFO_FILENAME);
                            $shortName = strlen($nameWithoutExtension) > 25 ? substr($nameWithoutExtension, 0, 25) . '...' : $nameWithoutExtension;
                            $displayName = $shortName . '.' . $extension;
                        @endphp
                        <div class="flex justify-between border-b-2 hover:rounded rounded-none hover:text-white hover:bg-sky-300 hover:border-sky-300 items-center pl-1"
                            @click.stop="window.location.href='{{ route('appointments.download-file', ['appt_id' => $appointment->id, 'appt_code' => $appointment->appointment_code , 'file_name' => $fullName]) }}'">
                            <p>{{ $displayName }}</p>
                        </div>
                    @empty
                        <p>No files found.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    <!-- Response Modal -->
    <div x-cloak x-show="responseModal" id="responseModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
        <div class="modal-content bg-white p-8 rounded-lg shadow-lg overflow-y-auto nice-scroll max-w-md w-full min-h-[90vh] flex flex-col justify-between">
            <h3 class="text-lg font-bold mb-4">Appointment Response</h3>
            <form action="{{route('appointments.response')}}" method="POST" enctype="multipart/form-data" class="flex flex-col justify-between h-full">
                @csrf
                <div>
                    <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">
                    <input type="hidden" name="user_id" value="{{$user->id}}">
                    <input type="hidden" name="file_path" value="{{$appointment->file_path}}">
                    <div class="mb-4">
                        <label for="response_file" class="block text-gray-700 text-sm font-bold mb-2">Attach Response File:</label>
                        <input type="file" id="response_file" name="response_file" class="border rounded py-2 px-3 text-gray-700">
                    </div>
                    <div>
                        <label for="response_message" class="block text-sm font-medium text-gray-700 mb-2">Appointment Response Message</label>
                        <textarea id="response_message" name="response_message" placeholder="Enter response message" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" style="height: 40vh;"></textarea>
                    </div>
                </div>
                <div class="flex justify-end space-x-4 mt-4">
                    <button type="button" @click="responseModal = false" class="modal-close-btn bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">Close</button>
                    <button type="submit" id="submitBtn" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition ease-in-out duration-150">Send Response</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

<script src="{{asset('js/manage_appt.js')}}" defer></script>

