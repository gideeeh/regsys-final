<div x-data='{requestForm: false}'>
<x-app-layout>
    <x-alert-message />
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Appointments') }}
        </h2>
        <span>Got any concerns? Click here: </span>
        <button @click="requestForm=true">Request</button>
    </x-slot>
    
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg sm:min-h-[49vh] md:min-h-[55vh] lg:min-h-[61vh] xl:min-h-[67vh]">
                <div class="border-collapse table-auto w-full whitespace-no-wrap bg-white table-striped relative">
                    <h3>Registrar Request History</h3>
                    <button id="btnPending" class="bg-red-500 text-white text-md px-2 py-2 rounded hover:bg-red-600 transition ease-in-out duration-150">Pending</button>
                    <button id="btnCompleted" class="bg-gray-500 text-white px-2 py-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">Completed</button>
                </div>
                <table class="border-collapse table-auto w-full whitespace-no-wrap bg-white table-striped relative rounded-lg">
                    <thead>
                        <th class="bg-blue-500 text-white p-2">Request</th>
                        <th class="bg-blue-500 text-white p-2">Date</th>
                        <th class="bg-blue-500 text-white p-2">Status</th>
                    </thead>
                    <tbody>
                        <tr class="border-b hover:bg-gray-100 cursor-pointer">
                            <td class="border-dashed border-t border-gray-300 p-2">&nbsp;</td>
                            <td class="border-dashed border-t border-gray-300 p-2">&nbsp;</td>
                            <td class="border-dashed border-t border-gray-300 p-2">&nbsp;</td>
                        </tr>
                        <tr class="border-b hover:bg-gray-100 cursor-pointer">
                            <td class="border-dashed border-t border-gray-300 p-2">&nbsp;</td>
                            <td class="border-dashed border-t border-gray-300 p-2">&nbsp;</td>
                            <td class="border-dashed border-t border-gray-300 p-2">&nbsp;</td>
                        </tr>
                        <tr class="border-b hover:bg-gray-100 cursor-pointer">
                            <td class="border-dashed border-t border-gray-300 p-2">&nbsp;</td>
                            <td class="border-dashed border-t border-gray-300 p-2">&nbsp;</td>
                            <td class="border-dashed border-t border-gray-300 p-2">&nbsp;</td>
                        </tr>
                    </tbody>
                </table>
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
