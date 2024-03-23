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
<div class="max-w-4xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold text-center mb-4">Enrollment Records</h1>
        <h2 class="text-xl text-gray-800 text-center mb-2">Student: {{$student->first_name}} {{$student->middle_name ? substr($student->middle_name, 0, 1).'.' : ''}} {{$student->last_name}}</h2>
        <h2 class="text-lg text-gray-700 text-center mb-6">Student Number: {{$student->student_number}}</h2>

        @foreach($groupedEnrollments as $programId => $enrollments)
            @php
                $program = $enrollments->first()->program;
                $enrollmentsByYear = $enrollments->groupBy('year_level');
            @endphp
            <div class="mb-4">
                <h3 class="text-lg font-semibold">{{ $program->program_name }} {{ $program->program_major ? 'Major in '.$program->program_major : '' }}</h3>

                @foreach($enrollmentsByYear as $year => $enrollmentsByTerm)
                    <div class="mt-2">
                        <h4 class="font-medium">Year Level: {{ $year }}</h4>

                        @foreach($enrollmentsByTerm->groupBy('term') as $term => $enrollments)
                            @foreach($enrollments as $enrollment)
                                <div class="bg-white shadow rounded-lg p-4 mt-4">
                                    <div class="flex justify-between items-center">
                                        <h5 class="text-gray-900 font-semibold">Acad Year: {{ $enrollment->academic_year }} - Term {{ $term }}</h5>
                                        <a href="{{ url('/gradeslip/pdf/print/' . $enrollment->enrollment_id) }}" class="text-blue-600 hover:text-blue-800 transition duration-150 ease-in-out">Print Gradeslip</a>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-2">Enrollment Code: {{ $enrollment->enrollment_code }}</p>
                                    <button @click="deleteEnrollmentModal = true; selectedEnrollmentId = {{$enrollment->enrollment_id}}" class="mt-2 text-white bg-red-500 hover:bg-red-700 transition duration-150 ease-in-out px-3 py-1 rounded text-sm">Delete Enrollment</button>

                                    <!-- Enrollment Table -->
                                    <div class="overflow-x-auto mt-4">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject Code</th>
                                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject Name</th>
                                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grade</th>
                                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>
                                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Professor</th>
                                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enrolled Subject Code</th>
                                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach($enrollment->enrolledSubjects as $enrolledSubject)
                                                    <tr>
                                                        <td class="px-3 py-2 whitespace-no-wrap">{{ $enrolledSubject->subject->subject_code }}</td>
                                                        <td class="px-3 py-2 whitespace-no-wrap">{{ $enrolledSubject->subject->subject_name }}</td>
                                                        <td class="px-3 py-2 whitespace-no-wrap">{{ $enrolledSubject->final_grade ?? 'Not Graded' }}</td>
                                                        <td class="px-3 py-2 whitespace-no-wrap">{{ ucfirst($enrolledSubject->remarks) ?? 'No remarks' }}</td>
                                                        <td class="px-3 py-2 whitespace-no-wrap">{{ $enrolledSubject->sectionSubject->subjectSectionSchedule->professor->first_name . ' ' . $enrolledSubject->sectionSubject->subjectSectionSchedule->professor->last_name ?? 'No professor record' }}</td>
                                                        <td class="px-3 py-2 whitespace-no-wrap">{{ $enrolledSubject->enrolledSubject_code ?? 'No enrolled subject code' }}</td>
                                                        <td class="px-3 py-2 whitespace-no-wrap">
                                                            <button @click="
                                                                updateGradeModal = true;
                                                                selectedEnrolledSubjectId = {{$enrolledSubject->en_subjects_id}};
                                                                selectedGrade = '{{$enrolledSubject->final_grade}}';
                                                                selectedRemarks = '{{$enrolledSubject->remarks}}';
                                                                selectedEnrollmentId = {{$enrolledSubject->enrollment_id}};
                                                                " class="text-white bg-blue-500 hover:bg-blue-700 transition duration-150 ease-in-out px-3 py-1 rounded text-sm">
                                                                Update Grade
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                @endforeach
            </div>
        @endforeach
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
            <p class="text-red-500">Warning: Deleting this record will delete all related enrolled subjects record for this enrollment period. Deleted data cannot be retrieved. Continue?</p>
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
