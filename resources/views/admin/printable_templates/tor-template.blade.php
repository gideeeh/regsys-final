<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transcript of Records</title>
    <style>
        @media (min-width: 1024px) { /* Large screens */
            .stu-academic-info {
                min-height: 45vh;
            }
        }
        @media (min-width: 768px) { /* Medium screens */
            .stu-academic-info {
                min-height: 38vh;
            }
        }
        @media (min-width: 640px) { /* Small screens */
            .stu-academic-info {
                min-height: 31vh;
            }
        }
    </style>
</head>
<body>
    <h1>TOR Template for {{ $program->program_name }}</h1>
    <div class="stu-academic-info" style="margin-top: 1rem; background-color: white; border: 1px solid; border-radius: 0.5rem; padding: 1.5rem; gap: 1rem; cursor: default;">
        <h3 style="display: flex; width: 100%; justify-content: center; background-color: #0ea5e9; padding: 0.5rem; border-radius: 0.375rem; color: white; margin-bottom: 1rem;">Academic History</h3>
        <!-- Start directly with year levels and terms since we're focusing on a single program -->
        @foreach($organizedEnrollments as $yearLevel => $terms)
            <div>
                <strong>{{ $yearLevel }} Year</strong>
                @foreach($terms as $term => $subjects)
                    <p>Term {{ $term }}</p>
                    <table style="min-width: 100%; table-layout: auto;">
                        <thead>
                            <tr>
                                <th style="border: 1px solid; padding: 0.5rem;">Subject Code</th>
                                <th style="border: 1px solid; padding: 0.5rem;">Subject Name</th>
                                <th style="border: 1px solid; padding: 0.5rem;">Grade</th>
                                <th style="border: 1px solid; padding: 0.5rem;">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subjects as $enrolledSubject)
                                <tr>
                                    <td style="border: 1px solid; padding: 0.5rem;">{{ $enrolledSubject->subject->subject_code }}</td>
                                    <td style="border: 1px solid; padding: 0.5rem;">{{ $enrolledSubject->subject->subject_name }}</td>
                                    <td style="border: 1px solid; padding: 0.5rem;">{{ $enrolledSubject->final_grade ?? 'Not Graded' }}</td>
                                    <td style="border: 1px solid; padding: 0.5rem;">{{ $enrolledSubject->remarks ?? 'No Remarks' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endforeach
            </div>
        @endforeach
    </div>
</body>
</html>
