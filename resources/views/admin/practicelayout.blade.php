<x-app-layout>
<div x-data="{
    updateGradeModal:false,
    confirmModal:false,
    deleteEnrollmentModal:false,
    selectedEnrollmentId:null,
    selectedEnrolledSubjectId:null,
    selectedGrade: '',
    selectedRemarks:'',
    }"
>
    <div>
        <h1>Enrollment Records</h1>
        @if($student->middle_name)
        <h2>Student: {{$student->first_name .' '.substr($student->middle_name,0,1).' '.$student->last_name}}</h2>
        @else
        <h2>Student: {{$student->first_name .' '.$student->last_name}}</h2>
        @endif
        <h2>Student Number: {{$student->student_number}}</h2>
        <div>
            @foreach($groupedEnrollments as $programId => $enrollments)
                @php
                    $program = $enrollments->first()->program;
                    $enrollmentsByYear = $enrollments->groupBy('year_level');
                @endphp
                @if($program->program_major)
                <h3>Program: {{ $program->program_name.' Major in '.$program->program_major }}</h3>
                @else
                <h3>Program: {{ $program->program_name }}</h3>
                @endif
                @foreach($enrollmentsByYear as $year => $enrollmentsByTerm)
                    <h4>Year Level: {{ $year }}</h4>
                    @foreach($enrollmentsByTerm->groupBy('term') as $term => $enrollments)
                        @foreach($enrollments as $enrollment)
                            <a href="{{ url('/gradeslip/pdf/print/' . $enrollment->enrollment_id) }}">Print Gradeslip</a>
                            <h5>Acad Year: {{ $enrollment->academic_year }} - T{{ $term }}</h5>
                            <div>
                                <p>Enrollment Code: {{ $enrollment->enrollment_code }}</p>
                                <button @click="deleteEnrollmentModal=true; selectedEnrollmentId={{$enrollment->enrollment_id}}">Delete Enrollment</button>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Subject Code</th>
                                            <th>Subject Name</th>
                                            <th>Grade</th>
                                            <th>Remarks</th>
                                            <th>SecSubId</th>
                                            <th>Professor</th>
                                            <th>Enrolled Subject Code</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($enrollment->enrolledSubjects as $enrolledSubject)
                                            <tr>
                                                <td>{{ $enrolledSubject->subject->subject_code }}</td>
                                                <td>{{ $enrolledSubject->subject->subject_name }}</td>
                                                <td>{{ $enrolledSubject->final_grade ?? 'Not Graded' }}</td>
                                                <td>{{ $enrolledSubject->remarks ?? 'No remarks' }}</td>
                                                <td>{{ $enrolledSubject->sec_sub_id ?? 'No sec_sub' }}</td>
                                                <td>{{ $enrolledSubject->sectionSubject->subjectSectionSchedule->professor->first_name ?? 'No professor record' }}</td>
                                                <td>{{ $enrolledSubject->enrolledSubject_code ?? 'No enrolled subject code' }}</td>
                                                <td>
                                                    <button @click=" 
                                                        updateGradeModal=true;
                                                        selectedEnrolledSubjectId= {{$enrolledSubject->en_subjects_id}};
                                                        selectedGrade= '{{$enrolledSubject->final_grade}}';
                                                        selectedRemarks= '{{$enrolledSubject->remarks}}';
                                                        selectedEnrollmentId={{$enrolledSubject->enrollment_id}};">
                                                        Update Grade
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endforeach
                    @endforeach
                @endforeach
            @endforeach
        </div>
    </div>
    <!-- Update grade modal -->
    <div x-cloak x-show="updateGradeModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
        <div class="modal-content bg-white p-8 rounded-lg shadow-lg overflow-auto max-w-lg w-full min-h-[35vh] max-h-[35vh]">
            <h3>Update Grade</h3>
            <form :action="'/admin/enrollment-records/' + selectedEnrollmentId + '/' + selectedEnrolledSubjectId" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')
                <input type="hidden" name="email" value="{{$user->email}}">
                <div>
                    <label for="grade" class="block text-sm font-medium text-gray-700">Grade:</label>
                    <input type="number" id="grade" name="grade" step="0.01" min="0" x-model="selectedGrade" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div class="mb-4">
                    <label for="remarks" class="block text-sm font-medium text-gray-700">Enter remarks:</label>
                    <input type="text" id="remarks" name="remarks" x-model="selectedRemarks" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">Enter Password (Admin):</label>
                    <input type="password" id="password" name="password" @paste.prevent="" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div class="flex justify-end space-x-4">
                    <button type="button" @click="updateGradeModal=false" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">Cancel</button>
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition ease-in-out duration-150">Update Grade</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Delete Enrollment Modal -->
    <div x-cloak x-show="deleteEnrollmentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
        <div class="modal-content bg-white p-8 rounded-lg shadow-lg overflow-auto max-w-lg w-full min-h-[35vh] max-h-[35vh]">
            <h3>Delete Enrollment</h3>
            <p class="text-red-500">Warning: Deleted records cannot be retrieved. Continue?</p>
            <form :action="'/admin/faculty-records/delete/' + selectedEnrollmentId" method="POST" class="space-y-4">
                @csrf
                @method('DELETE')
                <input type="hidden" name="email" value="{{$user->email}}">
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">Enter Password (Admin):</label>
                    <input type="password" name="password" @paste.prevent="" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div class="flex justify-end space-x-4">
                    <button type="button" @click="deleteEnrollmentModal=false" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition ease-in-out duration-150">Cancel</button>
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition ease-in-out duration-150">Confirm Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>
</x-app-layout>
