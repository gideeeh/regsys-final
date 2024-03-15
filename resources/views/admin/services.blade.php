@extends('admin.appointments-partials')
@section('content')
<div 
    x-data='{
        addService:false, 
        updateService:false, 
        deleteService:false, 
        serviceId: null, 
        selectedServiceName: "", 
        selectedDescription: "",
        selectedInstruction: "",
    }' 
    @keydown.escape ="addService=false; updateService=false; deleteService=false">
    <x-alert-message />
    <div class="w-full bg-white shadow-sm sm:rounded-lg min-h-[80vh] p-6">
        <h3 class="flex w-full justify-center bg-sky-950 px-4 rounded-md text-white mb-6">Registrar Services Management</h3>
        <button @click="addService = true" id="addService" class="mb-4 bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition ease-in-out duration-150">+ Add Service</button>
        <div class="mt-4">
            {{ $services->links() }}
        </div>
        <!-- Services Table -->
        <div class="py-4">
            <div class="overflow-x-auto bg-white rounded-lg shadow overflow-y-auto relative">
                <table class="border-collapse table-auto w-full whitespace-no-wrap bg-white table-striped relative">
                    <thead>
                        <tr class="cursor-default text-left text-sm">
                            <th class="bg-blue-500 text-white pl-4 p-1">Service Name</th>
                            <th class="bg-blue-500 text-white p-1">Service Description</th>
                            <th class="w-1/12 bg-blue-500 text-white p-1 text-center pr-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($services as $service)
                        <tr class="text-left text-sm border-b hover:bg-gray-100 cursor-pointer">
                            <td class="border-dashed border-t border-gray-300 p-2 pl-4">{{$service->service_name}}</td>
                            <td class="border-dashed border-t border-gray-300 p-2">{{$service->description}}</td>
                            <td class="border-dashed border-t border-gray-300 p-2 pr-4">
                                <div class="flex justify-center gap-1">
                                    <button 
                                        @click.stop="updateService = true; 
                                            serviceId = {{$service->id}}; 
                                            selectedServiceName='{{$service->service_name}}'; 
                                            selectedInstruction='{{$service->service_instructions}}'; 
                                            selectedDescription='{{$service->description}}';" 
                                        class="bg-blue-500 text-sm text-white p-1 rounded hover:bg-blue-600">Update</button>
                                    <button @click.stop="deleteService = true; serviceId = {{$service->id}}" class="bg-red-500 text-sm text-white p-1 rounded hover:bg-red-600 transition ease-in-out duration-150">Delete</button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Add Service Modal -->
        <div x-cloak x-show="addService" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
            <div class="modal-content bg-white p-8 rounded-lg shadow-lg overflow-auto max-w-lg w-full min-h-[85vh] max-h-[85vh]">
                <h3 class="text-lg font-bold mb-4">Add Service</h3>
                
                <form action="{{route('appointments.create-service')}}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="isActive" value="true">
                    <div>
                        <label for="service_name" class="block text-sm font-medium text-gray-700">Service Name:</label>
                        <input type="text" id="service_name" name="service_name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label for="service_description" class="block text-sm font-medium text-gray-700">Service Description:</label>
                        <textarea name="description" id="description" x-model="textarea" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                    </div>
                    <div>
                        <label for="service_instructions" class="block text-sm font-medium text-gray-700">Service Instructions:</label>
                        <textarea name="service_instructions" id="service_instructions" x-model="textarea" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                    </div>
                    <div>
                        <span>Require an Upload?</span>
                        <input type="hidden" name="requireUpload" value="0">
                        <input type="checkbox" name="requireUpload" id="requireUpload" value="1">
                    </div>
                    <div>
                        <label for="allowedFileExtension" class="block text-sm font-medium text-gray-700">Select allowed file extensions (can select multiple):</label>
                        <select id="allowedFileExtension" name="allowedFileExtension[]" multiple="multiple" disabled class="allowedFileExtension mt-1 block w-full border-gray-300 rounded-md shadow-sm" style="width: 100%;">
                            <option value="jpg/jpeg">jpeg/jpg</option>
                            <option value="png">png</option>
                            <option value="mp3">mp3</option>
                            <option value="mp4">mp4</option>
                            <option value="pdf">pdf</option>
                            <option value="docx">docx (Word)</option>
                            <option value="xlsx">xlsx (Excel)</option>
                        </select>
                    </div>
                    <div>
                        <label for="max-size" class="block text-sm font-medium text-gray-700">Max file size (mb)?</label>
                        <input type="number" name="max-size" id="max-size" readonly class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div class="flex justify-end space-x-4">
                        <button type="button" @click="addService = false" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">Cancel</button>
                        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition ease-in-out duration-150">Save Service</button>
                    </div>
                    
                </form>
            </div>
        </div>

        <!-- Update Modal -->
        <div x-cloak x-show="updateService" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
            <div class="modal-content bg-white p-8 rounded-lg shadow-lg overflow-auto max-w-md w-full max-h-[80vh]">
                <h3 class="text-lg font-bold mb-4">Update Service</h3>  
                <form :action="'/admin/appointments/services/update/' + serviceId" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label for="update_service_name" class="block text-sm font-medium text-gray-700">Service Name:</label>
                        <input type="text" id="update_service_name" name="update_service_name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" x-model="selectedServiceName">
                    </div>
                    <div>
                        <label for="update_description" class="block text-sm font-medium text-gray-700">Service Description:</label>
                        <textarea name="update_description" id="update_description" x-model="selectedDescription" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                    </div>
                    <div>
                        <label for="update_service_instructions" class="block text-sm font-medium text-gray-700">Service Instructions:</label>
                        <textarea name="update_service_instructions" id="update_service_instructions" x-model="selectedInstruction" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                    </div>
                    <div>
                        <span>Require an Upload?</span>
                        <input type="hidden" name="updateRequireUpload" value="0">
                        <input type="checkbox" name="updateRequireUpload" id="updateRequireUpload" value="1">
                    </div>
                    <div>
                        <label for="update_allowedFileExtension" class="block text-sm font-medium text-gray-700">Select allowed file extensions (can select multiple):</label>
                        <select id="update_allowedFileExtension" name="update_allowedFileExtension[]" multiple="multiple" disabled class="allowedFileExtension mt-1 block w-full border-gray-300 rounded-md shadow-sm" style="width: 100%;">
                            <option value="jpg/jpeg">jpeg/jpg</option>
                            <option value="png">png</option>
                            <option value="mp3">mp3</option>
                            <option value="mp4">mp4</option>
                            <option value="pdf">pdf</option>
                            <option value="docx">docx (Word)</option>
                            <option value="xlsx">xlsx (Excel)</option>
                        </select>
                    </div>
                    <div>
                        <label for="update_max-size" class="block text-sm font-medium text-gray-700">Max file size (mb)?</label>
                        <input type="number" name="update_max-size" id="update_max-size" readonly class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div class="flex justify-end space-x-4">
                        <button type="button" @click="updateService=false" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">Cancel</button>
                        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition ease-in-out duration-150">Update Service</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Modal -->
        <div x-cloak x-show="deleteService" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
            <div class="modal-content bg-white p-8 rounded-lg shadow-lg overflow-auto max-w-md w-full">
                <h3 class="text-lg font-bold mb-4">Confirm Deletion</h3>
                <p>Are you sure you want to delete this service?</p>
                <div class="flex justify-end mt-4">
                    <div class="flex items-center">
                        <button @click="deleteService = false" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition ease-in-out duration-150 mr-2">Cancel</button>
                        <form :action="'/admin/appointments/services/delete/' + serviceId" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition ease-in-out duration-150">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<script src="{{asset('js/services.js')}}" defer></script>