@extends('admin.appointments-partials')
@section('content')
<x-alert-message />
<div x-data="{manage: false}">
    <div class="flex gap-4">
        
        <!-- Make response modal? get data from json of appointments. Have appointments be dynamically generated using js and have values in them to attach data when clicking on a specific response -->
        <div class="w-8/12 bg-white shadow-sm sm:rounded-lg max-h-[80vh] overflow-x-hidden overlow-y-scroll text-xs calendar-scroll p-2">
            <div class="flex items-center justify-between">
                <button @click="manage=true" id="manageAppt" class="bg-slate-500 text-white px-4 py-2 rounded hover:bg-slate-600 transition ease-in-out duration-150">Manage</button>
                <div class="flex justify-end gap-2">
                    <input type="date" id="jumpToApptDate" class="rounded text-xs">
                    <button id="jumpToApptDateBtn" class="bg-slate-500 text-white px-4 py-2 rounded hover:bg-slate-600 transition ease-in-out duration-150">Go</button>
                </div>
            </div>
            <div id='calendar' class="py-4 h-[30vh]"></div>
        </div>
        <div class="w-4/12 max-h-[80vh] min-h-[80vh] flex flex-col justify-between">
            <div class="bg-white shadow-sm max-h-[39vh] min-h-[39vh] sm:rounded-lg p-2">
                <h3 class="text-md">Appointments Queue</h3>
                <div class="mb-2 text-sm">
                    <button activeClassSelect=true class="actv-class-btn active-f2f bg-red-500 text-white text-md px-2 rounded hover:bg-red-600 transition ease-in-out duration-150'">Today</button>
                    <button activeClassSelec=false class="actv-class-btn active-ol bg-gray-500 text-white px-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">Tomorrow</button>
                    <button activeClassSelec=false class="actv-class-btn active-ol bg-gray-500 text-white px-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">This Week</button>
                </div>
                <div class="appointments-container">
                
                </div>
            </div>
            <div class="bg-white shadow-sm max-h-[39vh] min-h-[39vh] sm:rounded-lg p-2">
                <h3 class="text-md">Pending Appointments</h3>
                <div class="mb-2 text-sm">
                    <button activeClassSelect=true class="actv-class-btn active-f2f bg-red-500 text-white text-md px-2 rounded hover:bg-red-600 transition ease-in-out duration-150'">1 Day</button>
                    <button activeClassSelec=false class="actv-class-btn active-ol bg-gray-500 text-white px-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">2 Days</button>
                    <button activeClassSelec=false class="actv-class-btn active-ol bg-gray-500 text-white px-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">2 Days +</button>
                </div>
                <div class="appointments-container">

                </div>
            </div>
        </div>
    </div>
    <!-- Manage Modal -->
    <div x-cloak x-show="manage" id="manage" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
        <div class="modal-content bg-white p-8 rounded-lg shadow-lg overflow-auto max-w-md w-full min-h-[90vh]">
            <h3 class="text-lg font-bold mb-4">Appointment Management Settings</h3>
            <form method="POST" action="{{route('academic-calendar-add-event')}}"  id="addEventForm" class="space-y-4">
                @csrf
                <label for="requestLimit" class="block text-sm font-medium text-gray-700">Request Limit Per Day</label>
                <input type="number" id="requestLimit" placeholder="Request Limit Per Day" value="10" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <label for="bufferTime" class="block text-sm font-medium text-gray-700">Buffer Time (minutes)</label>
                <input type="number" id="bufferTime" placeholder="Buffer time between requests e.g., 15" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <!-- AM Availability -->
                <div class="space-y-2">
                    <h4 class="text-md font-medium text-gray-700">AM Availability</h4>
                    <div class="flex gap-2">
                        <div class="w-1/2">
                            <label for="amStartTime" class="block text-sm font-medium text-gray-700">Start Time</label>
                            <input type="time" id="amStartTime" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div class="w-1/2">
                            <label for="amEndTime" class="block text-sm font-medium text-gray-700">End Time</label>
                            <input type="time" id="amEndTime" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                    </div>
                </div>

                <!-- PM Availability -->
                <div class="space-y-2 mt-4">
                    <h4 class="text-md font-medium text-gray-700">PM Availability</h4>
                    <div class="flex gap-2">
                        <div class="w-1/2">
                            <label for="pmStartTime" class="block text-sm font-medium text-gray-700">Start Time</label>
                            <input type="time" id="pmStartTime" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div class="w-1/2">
                            <label for="pmEndTime" class="block text-sm font-medium text-gray-700">End Time</label>
                            <input type="time" id="pmEndTime" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
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
                                    <input type="checkbox" name="available_days[]" value="Monday" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                    <span class="text-gray-700">Monday</span>
                                </label>
                            </div>
                            <div>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="available_days[]" value="Tuesday" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                    <span class="text-gray-700">Tuesday</span>
                                </label>
                            </div>
                            <div>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="available_days[]" value="Wednesday" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                    <span class="text-gray-700">Wednesday</span>
                                </label>
                            </div>
                        </div>
                        <!-- Thu to Sat -->
                        <div class="w-1/2 pl-4">
                            <div>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="available_days[]" value="Thursday" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                    <span class="text-gray-700">Thursday</span>
                                </label>
                            </div>
                            <div>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="available_days[]" value="Friday" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                    <span class="text-gray-700">Friday</span>
                                </label>
                            </div>
                            <div>
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="available_days[]" value="Saturday" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                    <span class="text-gray-700">Saturday</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <label for="customReceivedRequestReply" class="block text-sm font-medium text-gray-700">Custom Received Request Reply</label>
                <textarea id="customReceivedRequestReply" placeholder="Set custom reply upon receiving a request" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                <!-- <label for="eventTitle" class="block text-sm font-medium text-gray-700">Event Title</label>
                <input type="text" id="eventTitle" placeholder="Event Title" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <label for="startTime" class="block text-sm font-medium text-gray-700">Start Date & Time</label>
                <input type="datetime-local" id="startTime" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <label for="endTime" class="block text-sm font-medium text-gray-700">End Date & Time</label>
                <input type="datetime-local" id="endTime" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <textarea id="eventComments" placeholder="Comments" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea> -->
                <div class="flex justify-end space-x-4">
                    <button type="button" @click="manage = false" class="modal-close-btn bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">Close</button>
                    <button type="submit" id="submitBtn" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition ease-in-out duration-150">Save Changes</button>
                </div>
            </form>
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
        aspectRatio: 1.45,
        contentHeight: 650,
        height: 650,
        events: [
            { title: 'Event 1', start: 'YYYY-MM-DD' },
        ],
        eventClick: function(info) {
            alert('Event: ' + info.event.title);
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
});
</script>
