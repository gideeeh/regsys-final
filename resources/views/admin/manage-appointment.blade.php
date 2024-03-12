@extends('admin.appointments-partials')
@section('content')
<div x-data={responseModal:true} class="flex gap-4">
    <div class="w-8/12 flex flex-col justify-between overflow-y-auto nice-scroll max-h-[80vh]">
        <div class="mb-3 bg-white shadow-sm sm:rounded-lg p-6 min-h-[20vh]">
            <div class="flex mb-2">
                <div class="w-1/2">
                    <p><strong>Name:</strong> {{ $student->first_name }} {{$student->last_name}}</p>
                    <p><strong>Student No.:</strong> {{ $student->student_number }}</p>
                </div>
                <div class="w-1/2">
                    <p><strong>Program & Year:</strong> {{ $student->program_code }}-{{ $student->year_level }}</p>
                    <p><strong>Email:</strong> {{ $student->personal_email }}</p>
                </div>
            </div>
            <a href="/admin/student-records/{{$student->student_id}}" class="underline text-xs" target="_blank">Visit Student Profile</a>
        </div>
        <div class="flex flex-col justify-between highlight-appointment w-full bg-white shadow-sm sm:rounded-lg p-6 rounded-md min-h-[58vh]">
            <!-- Show the selected appointment here -->
            @if($highlightedAppointment)
            <div id="selected-appointment" class="selected-appointment">
                <h3 class="flex justify-center mb-6 text-lg">Appointment Details</h3>
                <div class="flex gap-4 mb-6">
                    <div class="w-1/2 text-sm">
                        <p><strong>Service ID:</strong> {{ $highlightedAppointment->service_name }}</p>
                        <p><strong>Date & Time:</strong> {{ \Carbon\Carbon::parse($highlightedAppointment->appointment_datetime)->format('M d, Y g:i A') }}</p>
                    </div>
                    <div class="w-1/2 text-sm">
                        <p><strong>Status:</strong> {{ ucfirst($highlightedAppointment->status) }}</p>
                    </div>
                </div>
                <div class="mb-4">
                    <p><strong>Notes:</strong></p>
                    <p>{{$highlightedAppointment->notes ?? 'No available notes.'}}</p>
                </div>
                <p><strong>File(s) Submitted:</strong></p>
                <div>
                    <!-- List of Files -->
                </div>
            </div>
            @else
            <p>Select an appointment to view its details.</p>
            @endif
            <div class="flex justify-end gap-2">
                <button @click="responseModal=true" class="bg-sky-500 text-white text-sm p-2 rounded hover:bg-sky-600 transition ease-in-out duration-150">Respond</button>
                <button class="bg-green-500 text-white text-sm p-2 rounded hover:bg-green-600 transition ease-in-out duration-150">Complete</button><!-- Disabled until there is a response -->
            </div>
        </div>
    </div>

    <div class="w-4/12 max-h-[80vh] min-h-[80vh] flex flex-col justify-between">
        <div class="queue-container bg-white shadow-sm max-h-[39vh] min-h-[39vh] sm:rounded-lg p-2 overflow-y-auto nice-scroll cursor-default">
            <h3 class="text-md">Appointment History</h3>
            <div class="mb-2 text-sm">
                <button activeClassSelect=true class="actv-queue-btn today-button active-queueSched bg-red-500 text-white text-md px-2 rounded hover:bg-red-600 transition ease-in-out duration-150">Pending</button>
                <button activeClassSelec=false class="actv-queue-btn tomorrow-button bg-gray-500 text-white px-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">Complete</button>
            </div>
            <div class="appointments-history">
                <div>
                @foreach($appointments as $appointment)
                <div id="appointment-{{ $appointment->id }}" class="appointment p-2 text-sm">
                    <!-- <p>Status: {{ ucfirst($appointment->status) }}</p> -->
                    <p><strong>Service:</strong> {{ $appointment->service_name }}</p>
                    <p><strong>Appt. Sched:</strong> {{ \Carbon\Carbon::parse($appointment->appointment_datetime)->format('M d, Y g:i A') }}</p>
                </div>
                @endforeach
                </div>
            </div>
        </div>
        <div class="pending-container bg-white shadow-sm max-h-[39vh] min-h-[39vh] sm:rounded-lg p-2 overflow-y-auto nice-scroll cursor-default">
            <h3 class="text-md mb-2">Files Submitted</h3>
            <!-- <div class="mb-2 text-sm">
                <button activeClassSelect=true class="actv-pending-btn one-day-button active-pendingDay bg-red-500 text-white text-md px-2 rounded hover:bg-red-600 transition ease-in-out duration-150'">1 Day</button>
                <button activeClassSelec=false class="actv-pending-btn two-days-button bg-gray-500 text-white px-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">2 Days</button>
                <button activeClassSelec=false class="actv-pending-btn beyond-two-days-button bg-gray-500 text-white px-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">2 Days +</button>
            </div> -->
            <div class="files-submitted">
                <!-- List of files submitted -->
            </div>
        </div>
    </div>
    <!-- Response Modal -->
    <div x-cloak x-show="responseModal" id="responseModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
        <div class="flex flex-col justify-between modal-content bg-white p-8 rounded-lg shadow-lg overflow-auto max-w-md w-full min-h-[90vh]">
            <div>
                <form action="#" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="appointment_id" value="{{ $highlightedAppointment->id }}">
    
                    <div class="mb-4">
                        <label for="response_file" class="block text-gray-700 text-sm font-bold mb-2">Attach Response File:</label>
                        <input type="file" id="response_file" name="response_file" class="shadow border rounded py-2 px-3 text-gray-700">
                    </div>

                    <!-- Other form fields like text input for additional comments can go here -->
                </form>
            </div>
            <div class="flex justify-end space-x-4">
                <button type="button" @click="responseModal = false" class="modal-close-btn bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">Close</button>
                <button type="submit" id="submitBtn" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition ease-in-out duration-150">Send Response</button>
            </div>
        </div>
    </div>
</div>

@endsection

<script src="{{asset('js/manage_appt.js')}}" defer></script>

