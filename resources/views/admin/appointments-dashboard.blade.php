@extends('admin.appointments-partials')
@section('content')
<x-alert-message />
<div x-data="{manage: false, clickEvent: false}">
    <div class="flex gap-4">
        
        <!-- Make response modal? get data from json of appointments. Have appointments be dynamically generated using js and have values in them to attach data when clicking on a specific response -->
        <div class="w-9/12 bg-white shadow-sm sm:rounded-lg max-h-[80vh] overflow-x-hidden overlow-y-scroll text-xs calendar-scroll p-2">
            <div class="flex items-center justify-between">
                <div>
                    <button @click="manage=true" id="manageAppt" class="bg-slate-500 text-white px-4 py-2 rounded hover:bg-slate-600 transition ease-in-out duration-150">Manage</button>
                    <button id="availability" class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800 transition ease-in-out duration-150">Available</button>
                </div>
                <div class="flex justify-end gap-2">
                    <input type="date" id="jumpToApptDate" class="rounded text-xs">
                    <button id="jumpToApptDateBtn" class="bg-slate-500 text-white px-4 py-2 rounded hover:bg-slate-600 transition ease-in-out duration-150">Go</button>
                </div>
            </div>
            <div id='calendar' class="py-4 h-[30vh]"></div>
        </div>
        <div class="w-3/12 max-h-[80vh] min-h-[80vh] flex flex-col justify-between">
            <div class="queue-container bg-white shadow-sm max-h-[39vh] min-h-[39vh] sm:rounded-lg p-2 overflow-y-auto nice-scroll cursor-default">
                <h3 class="text-md">Appointments Queue</h3>
                <div class="mb-2 text-sm">
                    <button activeClassSelect=true class="actv-queue-btn today-button active-queueSched bg-red-500 text-white text-md px-2 rounded hover:bg-red-600 transition ease-in-out duration-150'">Today</button>
                    <button activeClassSelec=false class="actv-queue-btn tomorrow-button bg-gray-500 text-white px-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">Tomorrow</button>
                    <button activeClassSelec=false class="actv-queue-btn thisWeek-button bg-gray-500 text-white px-1 rounded hover:bg-gray-600 transition ease-in-out duration-150">This Week</button>
                </div>
                <div class="appointments-queue">
                
                </div>
            </div>
            <div class="pending-container bg-white shadow-sm max-h-[39vh] min-h-[39vh] sm:rounded-lg p-2 overflow-y-auto nice-scroll cursor-default">
                <h3 class="text-md">Pending Appointments</h3>
                <div class="mb-2 text-sm">
                    <button activeClassSelect=true class="actv-pending-btn one-day-button active-pendingDay bg-red-500 text-white text-md px-2 rounded hover:bg-red-600 transition ease-in-out duration-150'">1 Day</button>
                    <button activeClassSelec=false class="actv-pending-btn two-days-button bg-gray-500 text-white px-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">2 Days</button>
                    <button activeClassSelec=false class="actv-pending-btn beyond-two-days-button bg-gray-500 text-white px-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">2 Days +</button>
                </div>
                <div class="appointments-pending">

                </div>
            </div>
        </div>
    </div>
    <!-- Manage Modal -->
    <div x-cloak x-show="manage" id="manage" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
        <div class="modal-content bg-white p-8 rounded-lg shadow-lg overflow-auto max-w-lg w-full min-h-[85vh] max-h-[85vh]">
            <h3 class="text-lg font-bold mb-4">Appointment Management Settings</h3>
            <form method="POST" action="{{route('appointments.save-mgmt-settings')}}"  id="saveMgmtSettings" class="space-y-4">
                @csrf
                @method('PATCH')
                <label for="requestLimit" class="block text-sm font-medium text-gray-700">Request Limit Per Day</label>
                <input type="number" id="requestLimit" name="requestLimit" placeholder="Request Limit Per Day" value="{{$settings->request_limit}}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <label for="bufferTime" class="block text-sm font-medium text-gray-700">Buffer Time (minutes)</label>
                <input type="number" id="bufferTime" name="bufferTime" placeholder="Buffer time between requests e.g., 15" value="{{$settings->buffer_time_minutes}}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <!-- AM Availability -->
                <div class="space-y-2">
                    <h4 class="text-md font-medium text-gray-700">AM Availability</h4>
                    <div class="flex gap-2">
                        <div class="w-1/2">
                            <label for="amStartTime" class="block text-sm font-medium text-gray-700">Start Time</label>
                            <input type="time" id="amStartTime" name="amStartTime" value="{{$settings->am_availability_start}}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div class="w-1/2">
                            <label for="amEndTime" class="block text-sm font-medium text-gray-700">End Time</label>
                            <input type="time" id="amEndTime" name="amEndTime" value="{{$settings->am_availability_end}}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                    </div>
                </div>

                <!-- PM Availability -->
                <div class="space-y-2 mt-4">
                    <h4 class="text-md font-medium text-gray-700">PM Availability</h4>
                    <div class="flex gap-2">
                        <div class="w-1/2">
                            <label for="pmStartTime" class="block text-sm font-medium text-gray-700">Start Time</label>
                            <input type="time" id="pmStartTime" name="pmStartTime" value="{{$settings->pm_availability_start}}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div class="w-1/2">
                            <label for="pmEndTime" class="block text-sm font-medium text-gray-700">End Time</label>
                            <input type="time" id="pmEndTime" name="pmEndTime" value="{{$settings->pm_availability_end}}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                    </div>
                </div>
                <label class="block text-md font-semibold mb-2">Available Schedules</label>
                <fieldset class="mb-4"> 
                    <legend class="text-base font-medium text-gray-900 mb-2">Day(s)</legend>
                    <div class="flex justify-content items-center">
                        <!-- Mon to Wed -->
                        <div class="w-1/2 pl-4">
                            <div>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="available_days[]" value="Monday" {{ in_array('Monday', $settings->available_schedules) ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                    <span class="text-gray-700">Monday</span>
                                </label>
                            </div>
                            <div>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="available_days[]" value="Tuesday" {{ in_array('Tuesday', $settings->available_schedules) ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                    <span class="text-gray-700">Tuesday</span>
                                </label>
                            </div>
                            <div>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="available_days[]" value="Wednesday" {{ in_array('Wednesday', $settings->available_schedules) ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                    <span class="text-gray-700">Wednesday</span>
                                </label>
                            </div>
                        </div>
                        <!-- Thu to Sat -->
                        <div class="w-1/2 pl-4">
                            <div>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="available_days[]" value="Thursday" {{ in_array('Thursday', $settings->available_schedules) ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                    <span class="text-gray-700">Thursday</span>
                                </label>
                            </div>
                            <div>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="available_days[]" value="Friday" {{ in_array('Friday', $settings->available_schedules) ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                    <span class="text-gray-700">Friday</span>
                                </label>
                            </div>
                            <div>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="available_days[]" value="Saturday" {{ in_array('Saturday', $settings->available_schedules) ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                    <span class="text-gray-700">Saturday</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <label for="customReceivedRequestReply" class="block text-sm font-medium text-gray-700">Custom Received Request Reply</label>
                <textarea id="customReceivedRequestReply" name="customReceivedRequestReply" placeholder="Set custom reply upon receiving a request" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{$settings->received_request_reply}}</textarea>
                <div class="flex justify-end space-x-4">
                    <button type="button" @click="manage = false" class="modal-close-btn bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">Close</button>
                    <button type="submit" id="submitBtn" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition ease-in-out duration-150">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Click Event Modal -->
    <div x-cloak x-show="clickEvent" id="clickEvent" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
        <div class="modal-content bg-white p-8 rounded-lg shadow-lg overflow-auto max-w-md w-full min-h-[90vh]">
            <button type="button" @click="clickEvent = false" class="modal-close-btn bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">Close</button>
        </div>
    </div>
</div>
@endsection
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            right: 'prev,next today',
            center: 'title',
            left: 'dayGridMonth,dayGridWeek,listWeek'
        },
        eventDidMount: function(info) {
            const dateTimeOptions = { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true };
            const startString = info.event.start ? new Date(info.event.start).toLocaleDateString('en-US', dateTimeOptions) : 'No start date';
            const endString = info.event.end ? new Date(info.event.end).toLocaleDateString('en-US', dateTimeOptions) : 'No end date';

            tippy(info.el, {
                content: `Student: ${info.event.title}<br>
                          Appt. Date: ${startString}<br>
                          Concern: ${info.event.extendedProps.concern}`, 
                allowHTML: true,
            });
        },
        aspectRatio: 1.45,
        contentHeight: 650,
        height: 650,
        events: '/admin/appointments/json',
        eventClick: function(info) {
            // alert('Event: ' + info.event.title + '\nService: ' + info.event.extendedProps.service_name);
            // alert('Event: ' + info.event.title + '\nService: ' + info.event.extendedProps.service_name + '\n \nManage this appointment?');
            // $('#clickEvent').show();
            // alert(info.event.extendedProps.id);
            var confirmManage = confirm('Appointment: ' + info.event.title + '\nService: ' + info.event.extendedProps.service_name + '\n\nManage this appointment?');

            if (confirmManage) {
                window.open('/admin/appointments/manage/' + info.event.extendedProps.user_id  + `?highlight=${info.event.extendedProps.id}`, '_blank');
            }
        }
    });
    calendar.setOption('contentHeight', 200);
    calendar.updateSize();
    calendar.render();

    $(window).resize(function() {
        $('#calendar').fullCalendar('option', 'height', window.innerHeight);
    });

    document.getElementById('jumpToApptDateBtn').addEventListener('click', function() {
        var dateInput = document.getElementById('jumpToApptDate').value;
        if (dateInput) {
            calendar.gotoDate(dateInput);
        }
    });

    $('#availability').on("click", function() {
        var button = $(this); 
        $.ajax({
            url: '/admin/appointments/set_availability',
            type: 'POST', 
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
            },
            success: function(response) {
                if (button.hasClass('bg-green-700')) {
                    button.removeClass('bg-green-700 hover:bg-green-800').addClass('bg-red-700 hover:bg-red-800');
                    button.text('Not Available');
                } else {
                    button.removeClass('bg-red-700 hover:bg-red-800').addClass('bg-green-700 hover:bg-green-800');
                    button.text('Available');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error occurred: " + error);
            }
        });
    });
});
</script>

<script>
    appointmentsQueueUrl = "{{url('/admin/appointments/queue-json')}}";
</script>
<!-- Note defer is used to load this inside the bladefile. Ensures jquery is loaded first before the custom script -->
<script src="{{ asset('js/adm_appt_dashboard.js') }}" defer></script>
