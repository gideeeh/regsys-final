<x-app-layout>
<div class="p-6">
    <div class="mb-4 flex w-full justify-between">
        <div class="flex flex-col w-9/12 justify-between">
            <div class="w-full flex bg-white shadow-sm sm:rounded-lg min-h-[38vh] max-h-[38vh] overflow-y-auto nice-scroll cursor-default mb-auto p-2">
                <!-- Need content here but what? -->
                <div class="w-8/12 p-2 flex flex-col justify-between">
                    <h2>Welcome, {{Auth::user()->first_name}}!</h2>
                    <div class="quote-container">
                        <h3 class="quote"></h3>
                        <p class="author"></p>
                    </div>
                    <div class="apptQueue-container text-xs text-slate-600">
                        <p><span><strong>Next Appt Queue: </strong></span></p>
                        <p><span class="nextAppt-queue"></span></p>
                    </div>
                </div>
                <div class="w-4/12 overflow-hidden relative">
                    <img class="absolute md:-bottom-8 lg:-bottom-10" src="{{asset('images/dashboard_img.png')}}" alt="dashboard-img">
                </div>
            </div>
            <div class="w-full flex gap-2 min-h-[35vh] max-h-[35vh]">
                <div class="w-6/12 bg-white shadow-sm sm:rounded-lg p-2 flex justify-center">
                    <canvas id="programChart"></canvas>
                </div>
                <div class="w-6/12 bg-white shadow-sm sm:rounded-lg p-2 flex justify-center">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
        <!-- Div below is shrinked idk why -->
        <div class="w-3/12 ml-4">
            <div class="active-classes-container w-full bg-white shadow-sm sm:rounded-lg min-h-[38vh] max-h-[38vh] overflow-y-auto nice-scroll cursor-default p-4 mb-6">
                <h3 class="text-md"><a href="{{route('sections')}}" target="_blank" title="Click to manage class schedules">Active Classes</a></h3>
                <div class="mb-2 text-sm">
                    <button activeClassSelect=true class="actv-class-btn active-f2f bg-red-500 text-white text-md px-2 rounded hover:bg-red-600 transition ease-in-out duration-150'">F2F</button>
                    <button activeClassSelec=false class="actv-class-btn active-ol bg-gray-500 text-white px-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">Online</button>
                </div>
                <div class="active-classes"></div>
            </div>
            <!-- Upcoming Calendar Events -->
            <div class="upcoming-events-container w-full bg-white shadow-sm sm:rounded-lg min-h-[35vh] max-h-[35vh] overflow-y-auto nice-scroll cursor-default p-4">
                <h3 class="text-md"><a href="{{route('academic-calendar')}}" target="_blank" title="Go to Academic Calendar">Upcoming Events</a></h3>
                <div class="mb-2 text-sm">
                    <button data-event-type="important" activeEventPeriod=true class="actv-event-btn active-event bg-red-500 text-white text-md px-2 rounded hover:bg-red-600 transition ease-in-out duration-150'">Important</button>
                    <button data-event-type="today" activeEventPeriod=false class="actv-event-btn inactive_event bg-gray-500 text-white text-md px-2 rounded hover:bg-red-600 transition ease-in-out duration-150'">Today</button>
                    <button data-event-type="this_week" activeEventPeriod=false class="actv-event-btn inactive_event bg-gray-500 text-white px-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">This Week</button>
                    <!-- <button data-event-type="this_month" activeEventPeriod=false class="actv-event-btn inactive_event bg-gray-500 text-white px-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">This Month</button> -->
                </div>
                <div class="active-events"></div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
<script>
    activeClassesUrl = "{{url('/admin/dashboard/get-active-classes')}}";
    activeEventsUrl = "{{url('/admin/dashboard/get-calendar-events')}}";
    activeQuoteUrl = "{{url('/admin/dashboard/get-daily-quote')}}";
    apptQueueUrl = "{{url('/admin/appointments/latest-appt-json')}}";
    academicCalendarLink = "{{url('/admin/functions/program-course-management/academic_calendar')}}";
    sectionsLink = "{{url('/admin/functions/program-course-management/sections')}}";
    window.programData = @json($programData);
    window.trendData = @json($trendData);
</script>
<script src="{{asset('js/dashboard.js')}}" ></script>
<script src="{{asset('js/charts.js')}}" ></script>
<script src="{{asset('js/calendar_events.js')}}" ></script>
<!-- <script src="{{asset('js/quotes.js')}}" ></script> -->