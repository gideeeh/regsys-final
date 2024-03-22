@extends('admin.enrollments')
@section('content')
<div>
    <div>
        <h1>Credit Subject</h1>
        <form action="#" method="POST">
            @if(isset($students))
            <div class="mb-4">
                <label for="student" class="block text-sm font-medium text-gray-700">Student:</label>
                <select id="student" name="student" x-model="student" style="width: 100%;"></select>
            </div>
            @endif
            <div class="mb-4">
                <label for="subject" class="block text-sm font-medium text-gray-700">Subject:</label>
            <select id="subject" name="subject" x-model="subject" style="width: 100%;"></select>
            </div>
            <div class="mb-4">
                <label for="grade" class="block text-sm font-medium text-gray-700">Grade:</label>
                <input type="number" name="grade" step="0.01" min="0" x-model="selectedGrade" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div class="mb-4">
                <label for="completion" class="block text-sm font-medium text-gray-700">Completion:</label>
                <input type="text" id="completion" name="completion" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div class="mb-4">
                <label for="remarks" class="block text-sm font-medium text-gray-700">Remarks:</label>
                <input type="text" id="remarks" name="remarks" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <button type="button" class="w-1/4 enroll shadow bg-green-500 text-white px-2 py-1 rounded hover:bg-green-600 transition ease-in-out duration-150">Submit</button>
        </form>
        
    </div>
</div>
<script>
    var getSubjectsUrl = "{{ url('/admin/functions/get-subjects') }}";
    var getStudentsUrl = "{{ url('/admin/students/get-students') }}";
    var fetchSection = "{{url('/sections/fetch')}}"
    var searchSecSub = "{{url('/admin/functions/get-section-subjects')}}" 
</script>
<script src="{{ asset('js/credit_student.js') }}" defer></script>
@endsection