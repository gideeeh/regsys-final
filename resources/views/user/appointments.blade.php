<div x-data='{
    requestForm: false,
    viewQRCodeModal: false,
    selectSchedule: false,
    viewMode: `ongoing`,
    selectedDate: null,
    selectedTimeSlot: null,
}'
    @keydown.escape.window="
    requestForm=false;
    viewQRCodeModal=false;
    selectSchedule= false;
">
<x-app-layout>
    <x-alert-message />
    @if($errors->has('add_file'))
    <div class="alert alert-danger">
        {{ $errors->first('add_file') }}
    </div>
    @endif
    <x-slot name="header">
        <div class="flex justify-between">
            <p class="font-bold text-xl">Appointments Dashboard</p>
            <div class="flex justify-end items-center gap-2">
                <span class="text-sm">Got any concerns? </span>
                @if($settings->isAvailable)
                <button @click="requestForm=true" class="bg-sky-500 text-white text-sm p-1 rounded hover:bg-sky-600 transition ease-in-out duration-150">Request Appointment</button>
                @else
                <button @click="requestForm=true" class="bg-slate-400 text-white text-sm p-1 rounded" disabled>Registrar Unavailable</button>
                @endif
            </div>
        </div>
    </x-slot>
    
    <div x-data="{ 
        selectedAppointment: @js($mostRecentAppointment),
        updateSelectedAppointment(appointment) {
        this.selectedAppointment = appointment;
        }
    }">
        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex gap-4 overflow-hidden sm:min-h-[48vh] md:min-h-[54vh] lg:min-h-[60vh] xl:min-h-[66vh]">
                    <div class="w-8/12 p-4 bg-white shadow-sm sm:rounded-lg">
                        <div id="user-appointment-content" class="flex flex-col justify-between sm:min-h-[48vh] md:min-h-[54vh] lg:min-h-[60vh] xl:min-h-[66vh]">
                            <div>
                                <div>
                                    <h3 class="flex justify-center mb-6 text-lg">Appointment Details</h3>
                                    @if($mostRecentAppointment)
                                    <div class="flex gap-4 mb-6 text-sm">
                                        <div class="w-1/2">
                                            <p><strong>Concern: </strong> {{ $mostRecentAppointment->concern }}</p>
                                            <p><strong>Appt. Sched: </strong> {{ \Carbon\Carbon::parse($mostRecentAppointment->appointment_datetime)->format('M d, Y g:i A') }}</p>
                                        </div>
                                        <div class="w-1/2">
                                            <p><strong>Status: </strong> {{ ucfirst($mostRecentAppointment->status) }}</p>
                                            <p><strong>Appt. Code: </strong> {{ ucfirst($mostRecentAppointment->appointment_code) }}</p>
                                        </div>
                                    </div>
                                    @else
                                    <div>No appointment data</div>
                                    @endif
                                </div>
                                <p class="mb-2"><strong>Response Log(s):</strong></p>
                                <div class="mb-4 border sm:min-h-[20vh] md:min-h-[23vh] lg:min-h-[26vh] xl:min-h-[29vh] text-sm">
                                    @foreach($appointment_responses as $response)
                                    @if($response->user_id === $user->id)
                                    <div class="mb-2 overflow-y-scroll nice-scroll p-2">
                                        <p><span class="font-bold">You: </span><span class="text-xs text-gray-400">[Sent at: {{$response->created_at}}]</span></p>
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
                            <div class="flex justify-end gap-2">
                                <button @click="viewQRCodeModal=true" class="bg-stone-500 text-white text-sm p-2 rounded hover:bg-stone-600 transition ease-in-out duration-150">QR Code</button>
                                <button @click="responseModal=true" class="bg-sky-500 text-white text-sm p-2 rounded hover:bg-sky-600 transition ease-in-out duration-150">Respond</button>
                                <button class="bg-green-500 text-white text-sm p-2 rounded hover:bg-green-600 transition ease-in-out duration-150">Mark as Complete</button><!-- Disabled until there is a response -->
                            </div>
                        </div>
                    </div>
                    <!-- Side -->
                    <div class="w-4/12 flex flex-col justify-between">
                        <div>
                            <div class="p-4 bg-white shadow-sm rounded-lg min-h-[32vh]">
                                <div class="mb-2 flex justify-between">
                                    <h3>Registrar Request History</h3>
                                    <div>
                                        <button 
                                            @click="viewMode = 'ongoing'" 
                                            :class="{ 'bg-red-600': viewMode === 'ongoing', 'bg-gray-500 hover:bg-gray-600': viewMode !== 'ongoing' }" 
                                            class="text-white text-xs p-1 rounded transition ease-in-out duration-150"
                                        >
                                            Ongoing
                                        </button>
                                        <button 
                                            @click="viewMode = 'complete'" 
                                            :class="{ 'bg-red-600': viewMode === 'complete', 'bg-gray-500 hover:bg-gray-600': viewMode !== 'complete' }" 
                                            class="text-white text-xs p-1 rounded transition ease-in-out duration-150"
                                        >
                                            Completed
                                        </button>
                                    </div>
                                </div>
                                <div id="user-appointment-history" class="border-solid border-2 border-round min-h-[20vh] max-h-[20vh] overflow-y-auto nice-scroll">
                                    <div x-show="viewMode === 'ongoing'">
                                        @if(!$appointments_ongoing->isEmpty())
                                        @foreach($appointments_ongoing as $appointment)
                                        <div class="cursor-pointer hover:bg-gray-100 p-2 text-xs">
                                            <p><strong>Concern: </strong>{{ $appointment->concern }}</p>
                                            <p><strong>Appt. Sched: </strong>{{ \Carbon\Carbon::parse($appointment->appointment_datetime)->format('M d, Y g:i A') }}</p>
                                        </div>
                                        @endforeach
                                        @else
                                        <div class="cursor-pointer hover:bg-gray-100 p-2 text-xs">
                                            <p>No ongoing appointments</p>
                                        </div>
                                        @endif
                                    </div>
                                    <div x-show="viewMode === 'complete'">
                                        @if(!$appointments_complete->isEmpty())
                                        @foreach($appointments_complete as $appointment)
                                        <div class="cursor-pointer hover:bg-gray-100 p-2 text-xs">
                                            <p><strong>Concern: </strong>{{ $appointment->concern }}</p>
                                            <p><strong>Appt. Sched: </strong>{{ \Carbon\Carbon::parse($appointment->appointment_datetime)->format('M d, Y g:i A') }}</p>
                                        </div>
                                        @endforeach
                                        @else
                                        <div class="cursor-pointer hover:bg-gray-100 p-2 text-xs">
                                            <p>No completed appointments</p>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="p-4 bg-white shadow-sm rounded-lg min-h-[30vh]">
                                <h3>Shared Files</h3>
                                <div class="text-sm overflow-y-scroll nice-scroll p-2 border min-h-[18vh] cursor-pointer">
                                    @forelse($files as $file)
                                        @php
                                            $fullName = $file->getFilename();
                                            $extension = pathinfo($fullName, PATHINFO_EXTENSION);
                                            $nameWithoutExtension = pathinfo($fullName, PATHINFO_FILENAME);
                                            $shortName = strlen($nameWithoutExtension) > 25 ? substr($nameWithoutExtension, 0, 25) . '...' : $nameWithoutExtension;
                                            $displayName = $shortName . '.' . $extension;
                                        @endphp
                                        <div class="flex justify-between border-b-2 hover:rounded rounded-none hover:text-white hover:bg-sky-300 hover:border-sky-300 items-center pl-1"
                                            @click.stop="window.location.href='{{ route('user.appointments.download-file', ['appt_id' => $mostRecentAppointment->id, 'appt_code' => $mostRecentAppointment->appointment_code , 'file_name' => $fullName]) }}'">
                                            <p>{{ $displayName }}</p>
                                        </div>
                                    @empty
                                        <p>No files found.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Request Form Modal -->
    <form action="{{route('appointments.submit-appt-request')}}" method="POST" enctype="multipart/form-data">
        <div x-cloak x-show="requestForm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
            <div class="modal-content bg-white p-8 rounded-lg shadow-lg overflow-auto max-w-md w-full min-h-[50vh]">
                <h3 class="text-lg font-bold mb-4">Registrar Request Form</h3>
                    @csrf
                    <input type="hidden" name="user_id" value="{{session('user_id')}}">
                    <div class="mb-4">
                        <label for="concern" class="block text-sm font-medium text-gray-700">Request:</label>
                        <input type="text" id="concern" name="concern" maxlength="25" placeholder="Enter request concern (Max. 25 char)" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <!-- <div>
                        <select id="request-service" name="service_id" style="width: 100%;" required></select>
                    </div> -->
                    <div class="mb-4">
                        <label for="add_file" class="block text-sm font-medium text-gray-700">Add Relevant File:</label>
                        <input type="file" id="add_file" name="add_file" class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 text-gray-700">
                    </div>
                    <div class="mb-4">
                        <label for="notes" class="block text-sm font-medium text-gray-700">Message/Notes:</label>
                        <textarea name="notes" id="notes" maxlength="50" placeholder="(Max. 50 char)" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                    </div>
                    <div class="flex justify-end space-x-4">
                        <button type="button" @click="requestForm = false" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">Cancel</button>
                        <!-- <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition ease-in-out duration-150">Submit Request</button> -->
                        <button type="button" @click="requestForm = false;selectSchedule=true" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition ease-in-out duration-150">Next</button>
                    </div>

            </div>
        </div>
        
        <!-- Select Schedule -->
        <div x-cloak x-show="selectSchedule" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
            <div class="modal-content bg-white p-8 rounded-lg shadow-lg overflow-auto max-w-lg w-full min-h-[85vh] max-h-[805h]">
                <h3>Select schedule</h3>
                {{-- Calendar Container --}}
                @php
                    $today = date('Y-m-d'); 
                    $availableSchedules = json_decode($settings->available_schedules);
                @endphp
                <div class="calendar-container cursor-default">
                    @php
                        $year = date('Y');
                        $month = date('m');
                        $monthName = date('M');
                        $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
                        $numberDays = date('t', $firstDayOfMonth);
                        $dateComponents = getdate($firstDayOfMonth);
                        $monthName = $dateComponents['month'];
                        $dayOfWeek = $dateComponents['wday'];
                    @endphp
                    <div class="mb-4">
                        <h3>{{$monthName . ' ' . $year}} </h3>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th>Sun</th>
                                <th>Mon</th>
                                <th>Tue</th>
                                <th>Wed</th>
                                <th>Thu</th>
                                <th>Fri</th>
                                <th>Sat</th>
                            </tr>
                        </thead>
                        <tbody>
                        @php
                            $currentDay = 1;
                            $dayOfWeek = $dateComponents['wday'];
                            $calendar = "";
                            
                            // Fill empty cells at the beginning of the first week
                            if ($dayOfWeek > 0) {
                                $calendar .= "<tr>";
                                for ($k = 0; $k < $dayOfWeek; $k++) {
                                    $calendar .= "<td class='border px-4 py-2'></td>";
                                }
                            }
                            
                            while ($currentDay <= $numberDays) {
                                if ($dayOfWeek == 7) {
                                    $dayOfWeek = 0;
                                    $calendar .= "</tr><tr>";
                                }

                                $currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);
                                $date = "$year-$month-$currentDayRel";
                                $dt = new DateTime($date);
                                $dayName = $dt->format('l');
                                $isPast = $date < $today;
                                $isAvailable = in_array($dayName, $availableSchedules) && !$isPast && $date !== $today;

                                // Styling classes
                                $dateClass = 'border px-4 py-2 text-xs ';
                                if ($date === $today || $isPast || !in_array($dayName, $availableSchedules)) {
                                    $dateClass .= 'bg-gray-200 text-gray-500 cursor-not-allowed';
                                } else {
                                    $dateClass .= 'cursor-pointer';
                                }

                                // Click handler adjustment
                                $onClick = $isAvailable ? "@click='selectedDate = \"$date\"'" : "";
                                $bindingClass = $isAvailable ? "x-bind:class=\"{'bg-red-500 text-white': selectedDate === '$date'}\"" : "";

                                $calendar .= "<td class='$dateClass clickable-date' data-date='$date' rel='$date' $onClick $bindingClass>$currentDay</td>";

                                $currentDay++;
                                $dayOfWeek++;
                            }

                            // Fill in the remaining days of the last week
                            if ($dayOfWeek != 7) {
                                $remainingDays = 7 - $dayOfWeek;
                                for ($l = 0; $l < $remainingDays; $l++) {
                                    $calendar .= "<td class='border px-4 py-2'></td>";
                                }
                            }

                            $calendar .= $dayOfWeek == 7 ? "" : "</tr>";
                        @endphp
                        {!! $calendar !!}
                        </tbody>
                    </table>
                </div>
                <div>
                    <h2>Select Timeslot</h2>
                    <input type="hidden" id="appointment_date" name="appointment_date">
                    <select name="timeslot" id="timeslot">
                        <option value="" hidden>Select a Timeslot</option>
                    </select>
                </div>

                <div class="flex justify-end space-x-4 mt-4">
                    <button type="button" @click="selectSchedule=false;requestForm=true" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">Go Back</button>
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition ease-in-out duration-150">Proceed</button>
                </div>
            </div>
        </div>
    </form>


    <!-- QR Code View -->
    <div x-cloak x-show="viewQRCodeModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
        @if($mostRecentAppointment)
        <div class="bg-white p-8 rounded-lg shadow-lg overflow-hidden max-w-md w-full min-h-[70vh]">
            <div class="flex justify-center mb-6">
                <img src="{{ $mostRecentAppointment->qrCodeUrl }}" alt="QR Code for Appointment">
            </div>
            @else
        <div class="bg-white p-8 rounded-lg shadow-lg overflow-hidden max-w-md w-full min-h-[40vh]">
            <div class="flex justify-center mb-6">
                <h2>No selected appointment.</h2>
            </div>
            @endif
            <div class="w-full">
                <button type="button" @click="viewQRCodeModal = false" class="w-full bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">Close</button>
            </div>
        </div>
    </div>
</x-app-layout>
</div>
<script>
    var servicesUrl = "{{url('/public/api/get_services')}}";
    var appointmentsPending = "{{url('/user/pending-requests')}}";
    var appointmentsComplete = "{{url('/user/complete-requests')}}";
    var getStudentsUrl;
    console.log(servicesUrl)
</script>
<script>
    $(document).ready(function() {
        $('body').on('click', 'td.clickable-date', function() {
            var selectedDate = $(this).attr('data-date');
            $('#appointment_date').val(selectedDate);
            console.log(selectedDate);
            $.ajax({
                url: "/user/appointments/limit/",
                type: "GET",
                data: {
                    date: selectedDate,
                },
                success: function(response) {
                    if(response.full) {
                        alert(response.message);
                    } else {
                        // Clear existing options
                        $('#timeslot').empty().append('<option value="">Select a Timeslot</option>');
                        // Populate dropdown with available timeslots
                        response.slots.forEach(function(slot) {
                            $('#timeslot').append(`<option value="${slot}">${slot}</option>`);
                        });

                        // console.log('Slots available for ' + selectedDate);
                        // console.log(response.slots); 
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error checking appointment limit:', error);
                }
            });
        });

        $('#timeslot').change(function() {
            var selectedTimeSlot = $(this).val();
            $('#hidden_timeslot').val(selectedTimeSlot);
            console.log(selectedTimeSlot);
        });
    });
</script>
