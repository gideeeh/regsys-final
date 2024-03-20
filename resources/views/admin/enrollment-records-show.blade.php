<x-app-layout>
<div x-data={updateGradeModal:false}>
    <div>
        <h1>Enrollment Records</h1>
        @if($student->middle_name)
        <h2>Student: {{$student->first_name .' '.substr($student->middle_name,0,1).' '.$student->last_name}}</h2>
        @else
        <h2>Student: {{$student->first_name .' '.$student->last_name}}</h2>
        @endif
        <h2>Student Number: {{$student->student_number}}</h2>
        <div>
            <h1>Enrollment Records</h1>
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
                        <h5>Term: {{ $term }}</h5>
                        <button>Print Gradeslip</button>
                        @foreach($enrollments as $enrollment)
                            <div>
                                <p>Enrollment Code: {{ $enrollment->enrollment_code }} - Academic Year: {{ $enrollment->academic_year }}</p>
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
                                                    <button>Update</button>
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
    
</div>
</x-app-layout>
