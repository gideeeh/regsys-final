{{-- resources/views/admin/printable_templates/gradeslip-template.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gradeslip</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap');
        
        @font-face {
        font-family: 'Lato';
        src: url('https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap') format('truetype');
        }
        body {
            font-family: 'Lato', sans-serif;
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
        thead {
            background-color: #00A9FF;
            color: white;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        .header {
            text-align: center;
            margin-bottom: 1rem;
        }
        h2 {
            text-align: center;
        }
        .student-information-container, footer {
            width: 100%;
            margin-bottom: 20px; 
        }
        .student-information, .academic-information, .admin-information, .grade-information {
            width: 49%; 
            display: inline-block;
            vertical-align: top;
            margin-right: 1%;
        }
       
        .academic-information, .grade-information {
            margin-right: 0;
        }
        footer {
            display: block; 
            line-height: 0.4rem;
            margin-top: 1rem;
        }
        .info-logo {
            width: 7rem;
            margin-bottom: 20px; 
        }
        .issued-by {
            display: inline-block;
            text-align: center;
            vertical-align: bottom;
            margin-left: 1rem;
        }
        .issued-by .full-name {
            border-bottom: 1px solid black;
            padding-bottom: 0.2rem;
        }
    </style>

</head>
<body>
    <div class="gradeslip-container">
        <div class="header" style="line-height: 0.5rem;">
            <p>Informatics College Northgate Inc.</p>
            <p>Cyberzone Filinvest, Indo China Drive, Corporate Ave.</p>
            <p>Alabang, Muntinlupa, Metro Manila</p>
            <!-- <h3>Gradeslip for {{ $student->first_name }} {{ $student->last_name }}</h3> -->
        </div>
        <h2>Student Grade Slip</h2>
        <div class="student-information-container" style="line-height: 0.5rem;">
            <div class="student-information">
                <p><strong>Name:</strong> {{ $student_name}}</p>
                <p><strong>Student No:</strong> {{$student->student_number}}</p>
            </div>
            <div class="academic-information">
                <p><strong>Program:</strong> {{$enrollment->program->program_code}} - {{$enrollment->year_level}}</p>
                <p><strong>S.Y.:</strong> {{$enrollment->academic_year}} - Term {{$enrollment->term}}</p>
                <p><strong>Date issued:</strong> {{$dateToday}}</p>
            </div>
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
                    <td>{{ ucfirst($data['remarks']) ?? 'No remarks'}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <footer>
            <div class="grade-information" style="margin-top: 1rem;">
                <p><strong>Total Units:</strong> {{$total_units_enrollment}}</p>
                <p><strong>General Weighted Average:</strong> {{$averageGrade}}</p>
            </div>
            <div class="admin-information">
                <span><strong>Issued by:</strong></span>
                <div class="issued-by">
                    <p class="full-name">{{$full_name}}</p>
                    <p class="label"><strong>Registrar</strong></p>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
