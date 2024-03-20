{{-- resources/views/admin/printable_templates/gradeslip-template.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gradeslip</title>
    <style>
        body {
            font-family: 'Georgia', 'Arial', sans-serif;
            padding: 20px;
        }
        .gradeslip-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        .header {
            text-align: center;
            position: relative;
        }
        .info-logo {
            width: 7rem;
            position: absolute;
            top: 0;
            left: 0;
        }
    </style>
</head>
<body>
    <div class="gradeslip-container">
        <div class="header">
            <p>Informatics College Northgate Inc.</p>
            <p>Cyberzone Filinvest, Indo China Drive, Corporate Ave.</p>
            <p>Alabang, Muntinlupa, Metro Manila</p>
            <p>Gradeslip</p>
            <!-- <h3>Gradeslip for {{ $student->first_name }} {{ $student->last_name }}</h3> -->
        </div>
        <div>
            <p>Name: {{ $student_name}}</p>
            <p>Student No: {{$student->student_number}}</p>
            <p>Academic Year: {{$enrollment->academic_year}}</p>
            <p>Term: {{$enrollment->term}}</p>
            <p>Program: {{$enrollment->program->program_code}}</p>
            <p>Year Level: {{$enrollment->year_level}}</p>
            <p>Enrollment Code: {{$enrollment->enrollment_code}}</p>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Subject Code</th>
                    <th>Subject Description</th>
                    <th>Total Units</th>
                    <th>Grade</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
            @foreach($enrolledSubjectsData as $data)
                <tr>
                    <td>{{ $data['subject_code'] }}</td>
                    <td>{{ $data['subject_name'] }}</td>
                    <td>{{ $data['total_units'] }}</td>
                    <td>{{ $data['final_grade'] }}</td>
                    <td>{{ $data['remarks'] ?? 'No remarks'}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
