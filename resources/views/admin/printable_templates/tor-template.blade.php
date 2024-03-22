<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transcript of Records</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        .container {
            padding: 20px;
        }
        h1, h3 {
            text-align: center;
        }
        .stu-academic-info {
            background-color: white;
            border: 1px solid #0ea5e9;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-top: 1rem;
        }
        table {
            width: 100%;
            border-collapse: collapse; /* Removes space between borders */
        }
        th, td {
            border: 1px solid #ddd; /* Light grey border for better readability */
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #0ea5e9;
            color: white;
        }
        tbody tr:nth-child(odd) {
            background-color: #f2f2f2; /* Zebra striping for rows */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>TOR Template for {{ $program->program_name }}</h1>
        <div class="stu-academic-info">
            <h3>Academic History</h3>
            @foreach($organizedEnrollments as $yearLevel => $terms)
                <div>
                    @php
                        $year_level_name_map = ['1st', '2nd', '3rd', '4th'];
                        $year_level_name = $year_level_name_map[$yearLevel - 1] ?? $yearLevel . 'th';
                    @endphp
                    <strong>{{ $year_level_name }} Year</strong>
                    @foreach($terms as $term => $subjects)
                        <p>Term {{ $term }}</p>
                        <table>
                            <thead>
                                <tr>
                                    <th>Subject Code</th>
                                    <th>Subject Name</th>
                                    <th>Grade</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subjects as $enrolledSubject)
                                    <tr>
                                        <td>{{ $enrolledSubject->subject->subject_code }}</td>
                                        <td>{{ $enrolledSubject->subject->subject_name }}</td>
                                        <td>{{ $enrolledSubject->final_grade ?? 'Not Graded' }}</td>
                                        <td>{{ $enrolledSubject->remarks ?? 'No Remarks' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
</body>
</html>
