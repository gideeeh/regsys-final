@extends('admin.records')
@section('content')
<div x-data="{ deleteModal: false, searchTerm: '{{ $searchTerm ?? '' }}', selectedStudent: null }">
    <div class="flex justify-between items-center h-10">
        <a href="{{ route('faculty-records') }}" class="font-semibold text-xl text-gray-800 leading-tight no-underline hover:underline">
            {{ __('Faculty Records') }}
        </a>
        <x-search-form action="{{ route('faculty-records') }}" placeholder="Search Faculty" />
    </div>
    <div class="mt-6">
        {{ $professors->links() }}
    </div>
    <div class="py-4">
        <div class="overflow-x-auto bg-white rounded-lg shadow overflow-y-auto relative" style="min-height: 405px;">   
            <table class="border-collapse table-auto w-full whitespace-no-wrap bg-white table-striped relative">
                <thead >
                    <tr class="cursor-default">
                        <th class="bg-blue-500 text-white p-2 w-4/12">Name</th>
                        <th class="bg-blue-500 text-white p-2 w-1/12">Department</th>
                        <th class="bg-blue-500 text-white p-2 w-3/12">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($professors as $professor)
                    <tr class="border-b hover:bg-gray-100 cursor-pointer">
                        <td class="border-dashed border-t border-gray-200 p-2 py-4">{{$professor->first_name}} {{ substr($professor->middle_name, 0, 1)}}.  {{$professor->last_name.' '.$professor->suffix}}</td>
                        <td class="border-dashed border-t border-gray-200 p-2 py-4">{{$professor->dept_name ?? 'Not Available' }}</td>
                        <td class="border-dashed border-t border-gray-200 p-2 py-4">
                            <div class="flex justify-start space-x-4">
                                <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Update</button>
                                <button @click.stop="deleteModal = true" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition ease-in-out duration-150">Delete</button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <!-- Delete Modal -->
    <div x-cloak x-show="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
        <div class="modal-content bg-white p-8 rounded-lg shadow-lg overflow-auto max-w-md w-full">
            <h3 class="text-lg font-bold mb-4">Confirm Deletion</h3>
            <p>Are you sure you want to delete this record?</p>
            <div class="flex justify-end space-x-4 mt-4">
                <button @click="deleteModal = false" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">Cancel</button>
            </div>
        </div>
    </div>
</div>
@endsection