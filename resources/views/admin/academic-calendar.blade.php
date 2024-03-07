@extends('admin.functions')
@section('content')
<div x-data="{ showModal: false, showSetAcadYearTerm: false }" @keydown.escape.window="showModal = false; showSetAcadYearTerm = false">
    <a href="{{ route('academic-calendar') }}">
        <h2 class="text-2xl font-semibold mb-4">Academic Calendar</h2>
    </a>
    <div class="flex date-picker justify-between mt-8">
        <button @click="showModal = true" id="showModalButton" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition ease-in-out duration-150">+ Add Event</button>    
        <div>
            <input type="date" id="jumpToDate" class="rounded">
            <button id="jumpToDateBtn" class="bg-slate-500 text-white px-4 py-2 rounded hover:bg-slate-600 transition ease-in-out duration-150">Go</button>
        </div>
    </div>
    <div id='calendar' class="py-4"></div>
    <!-- Add Calendar Event Modal -->
    <div x-cloak x-show="showModal" id="addEventModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
        <div class="modal-content bg-white p-8 rounded-lg shadow-lg overflow-auto max-w-md w-full max-h-[80vh]">
            <h3 class="text-lg font-bold mb-4">Add New Event</h3>
            <form method="POST" action="{{route('academic-calendar-add-event')}}"  id="addEventForm" class="space-y-4">
                @csrf
                <label for="eventTitle" class="block text-sm font-medium text-gray-700">Event Title</label>
                <input type="text" id="eventTitle" placeholder="Event Title" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <label for="startTime" class="block text-sm font-medium text-gray-700">Start Date & Time</label>
                <input type="datetime-local" id="startTime" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <label for="endTime" class="block text-sm font-medium text-gray-700">End Date & Time</label>
                <input type="datetime-local" id="endTime" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <textarea id="eventComments" placeholder="Comments" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                <div class="flex justify-end space-x-4">
                    <button type="button" @click="showModal = false" class="modal-close-btn bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">Close</button>
                    <button type="submit" id="submitBtn" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition ease-in-out duration-150">Add Event</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let calendar; 

document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        dateClick: function(info) {
            const dateTime = info.dateStr + 'T00:00';
            document.getElementById('startTime').value = dateTime;
            // Adjust the 'endTime' value as necessary, this is just an example
            document.getElementById('endTime').value = dateTime;

            // Show the modal
            document.getElementById('addEventModal').style.display = 'flex'; // Make sure your modal is using flex
        },
        events: JSON.parse('@json($events)').map(event => ({
            id: event.id, 
            title: event.title,
            start: event.startTime,
            end: event.endTime,
            comments: event.comments,
        })),
        events: JSON.parse('@json($events)'),
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: true, 
            meridiem: 'short' 
        },
        eventDidMount: function(info) {
            const dateTimeOptions = {
                month: 'short', 
                day: '2-digit',
                year: 'numeric', 
                hour: '2-digit',
                minute: '2-digit',
                hour12: true, 
                timeZone: 'Asia/Manila' 
            };
            const startDate = info.event.start ? new Date(info.event.start) : null;
            const endDate = info.event.end ? new Date(info.event.end) : null;
            
            const startString = startDate ? `${startDate.toLocaleTimeString('en-PH', dateTimeOptions)}` : 'No start date';
            const endString = endDate ? `${endDate.toLocaleTimeString('en-PH', dateTimeOptions)}` : 'No end date';

            tippy(info.el, {
                content: `Title: ${info.event.title}<br>
                        Start: ${startString}<br>
                        End: ${endString}<br>
                        Comments: ${info.event.extendedProps.comments}`,
                allowHTML: true,
            });
        },
        eventClick: function(info) {
            let eventDetail = {
                title: info.event.title,
                start: info.event.start.toISOString(),
                end: info.event.end.toISOString(),
                comments: info.event.extendedProps.comments
            };
            document.dispatchEvent(new CustomEvent('open-event-modal', { detail: eventDetail }));
        },
        eventClick: function(info) {
            if (confirm("Are you sure you want to delete this event?")) {
                axios.delete(`/admin/functions/program-course-management/academic_calendar/delete-event/${info.event.id}`)
                    .then(function(response) {
                        info.event.remove(); // Remove the event from the calendar
                        alert('Event deleted successfully');
                    })
                    .catch(function(error) {
                        console.error('Error deleting event:', error);
                        alert('An error occurred while deleting the event.');
                    });
            }
        }
    });
    calendar.render();

    document.getElementById('jumpToDateBtn').addEventListener('click', function() {
        var dateInput = document.getElementById('jumpToDate').value;
        if (dateInput) {
            calendar.gotoDate(dateInput); // Jump to the specified date
        }
    });
    
    document.getElementById('addEventForm').addEventListener('submit', function(e) {

        var title = document.getElementById('eventTitle').value;
        var start = document.getElementById('startTime').value;
        var end = document.getElementById('endTime').value;
        var comments = document.getElementById('eventComments').value;

        axios.post('/admin/functions/program-course-management/academic_calendar/add-event', {
            title: title,
            start: start,
            end: end,
            comments: comments,
        })
        .then(function (response) {
            calendar.addEvent({
                title: title,
                start: start,
                end: end,
                comments: comments,
            });
            showModal = false; 

            document.getElementById('addEventForm').reset();
        })
        .catch(function (error) {
            console.log(error);
        });
    });

    const form = document.getElementById('addEventForm');
    form.addEventListener('submit', function() {
        document.getElementById('submitBtn').disabled = true;
        document.getElementById('submitBtn').innerText = 'Submitting...';
    });

    document.querySelectorAll('.modal-close-btn').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('addEventModal').style.display = 'none'; // Hide the modal
        });
    });

    document.getElementById('showModalButton').addEventListener('click', function() {
        document.getElementById('eventTitle').value = ''; 
        document.getElementById('startTime').value = ''; 
        document.getElementById('endTime').value = '';
        document.getElementById('eventComments').value = '';
    });
});
</script>