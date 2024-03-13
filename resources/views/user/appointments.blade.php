<div x-data='{requestForm: false}'>
<x-app-layout>
    <x-alert-message />
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Appointments Dashboard') }}
        </h2>
        <span class="text-sm">Got any concerns? </span>
        <button @click="requestForm=true" class="bg-sky-500 text-white text-sm p-1 rounded hover:bg-sky-600 transition ease-in-out duration-150">Request Appointment</button>
    </x-slot>
    
    <div x-data="{ selectedAppointment: @js($mostRecentAppointment) }">
        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex gap-4 overflow-hidden sm:min-h-[48vh] md:min-h-[54vh] lg:min-h-[60vh] xl:min-h-[66vh]">
                    <div class="w-8/12 p-4 bg-white shadow-sm sm:rounded-lg">
                        <div id="user-appointment-content" class="flex flex-col justify-between sm:min-h-[48vh] md:min-h-[54vh] lg:min-h-[60vh] xl:min-h-[66vh]">
                            <div>
                                <div>
                                    <h3 class="flex justify-center mb-6 text-lg">Appointment Details</h3>
                                    @if($mostRecentAppointment)
                                    <div class="flex gap-4 mb-6">
                                        <div class="w-1/2 text-sm">
                                            <p><strong>Service ID: </strong> {{ $mostRecentAppointment->service->service_name }}</p>
                                            <p><strong>Date & Time: </strong> {{ \Carbon\Carbon::parse($mostRecentAppointment->appointment_datetime)->format('M d, Y g:i A') }}</p>
                                        </div>
                                        <div class="w-1/2">
                                            <p><strong>Status: </strong> {{ ucfirst($mostRecentAppointment->status) }}</p>
                                        </div>
                                    </div>
                                    @else
                                    <div>No appointment data</div>
                                    @endif
                                </div>
                                <div class="mb-4">
                                    <p><strong>Registrar's Response:</strong></p>
                                    <p>{{$mostRecentAppointment->response_notes ?? 'No available notes.'}}</p>
                                </div>
                                <div>
                                    <p><strong>File(s) Received:</strong></p>
                                    <!-- List of Files -->
                                </div>
                            </div>
                            <div class="flex justify-end gap-2">
                                <button @click="responseModal=true" class="bg-sky-500 text-white text-sm p-2 rounded hover:bg-sky-600 transition ease-in-out duration-150">Respond</button>
                                <button class="bg-green-500 text-white text-sm p-2 rounded hover:bg-green-600 transition ease-in-out duration-150">Mark as Complete</button><!-- Disabled until there is a response -->
                            </div>
                        </div>
                    </div>
                    <div class="w-4/12 p-4 bg-white shadow-sm sm:rounded-lg">
                        <div class="border-collapse table-auto w-full whitespace-no-wrap bg-white table-striped relative mb-2">
                            <h3>Registrar Request History</h3>
                            <button id="btnPending" class="bg-red-500 text-white text-sm px-2 py-1 rounded hover:bg-red-600 transition ease-in-out duration-150">Ongoing</button>
                            <button id="btnCompleted" class="bg-gray-500 text-white text-sm px-2 py-1 rounded hover:bg-gray-600 transition ease-in-out duration-150">Completed</button>
                        </div>
                        
                        <div id="user-appointment-history" class="border-solid border-2 border-round sm:min-h-[43vh]">
                            @foreach($appointments as $appointment)
                            <div @click="selectedAppointment = {service_name: '{{ $appointment->service->service_name }}', appointment_datetime: '{{ \Carbon\Carbon::parse($appointment->appointment_datetime)->format('M d, Y g:i A') }}'}" class="cursor-pointer hover:bg-gray-100 p-2 overflow-y-auto nice-scroll">
                                <p><strong>Service: </strong> {{ $appointment->service->service_name }}</p>
                                <p><strong>Appt. Sched: </strong>{{ \Carbon\Carbon::parse($appointment->appointment_datetime)->format('M d, Y g:i A') }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Request Form Modal -->
    <div x-cloak x-show="requestForm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
        <div class="modal-content bg-white p-8 rounded-lg shadow-lg overflow-auto max-w-md w-full max-h-[80vh]">
            <h3 class="text-lg font-bold mb-4">Registrar Request Form</h3>
            
            <form action="{{route('appointments.request')}}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="user_id" value="{{session('user_id')}}">
                <div>
                    <select id="request-service" name="service_id" style="width: 100%;"></select>
                </div>
                <div>
                <label for="notes" class="block text-sm font-medium text-gray-700">Additional Notes:</label>
                    <textarea name="notes" id="notes" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                </div>
                <div class="flex justify-end space-x-4">
                    <button type="button" @click="requestForm = false" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">Cancel</button>
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition ease-in-out duration-150">Submit Request</button>
                </div>
            </form>
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
<script src="{{ asset('js/appointments.js') }}"></script>
