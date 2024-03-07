<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Subjects</h1>
    <table border="1">
        <thead>
            <tr>
                <th>Subject Code</th>
                <th>Subject Name</th>
                <th>Units Lec</th>
                <th>Units Lab</th>
                <th>Total Units</th>
                <th>Prerequisite 1</th>
                <th>Prerequisite 2</th>
                <th>Prerequisite 3</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($subjects as $subject)
            <tr>
                <td>{{ $subject->subject_code }}</td>
                <td>{{ $subject->subject_name }}</td>
                <td>{{ $subject->units_lec }}</td>
                <td>{{ $subject->units_lab }}</td>
                <td>{{ $subject->units_lab + $subject->units_lec}}</td>
                <td>{{ $subject->prerequisite1->subject_code ?? ' - ' }}</td>
                <td>{{ $subject->prerequisite2->subject_code ?? ' - ' }}</td>
                <td>{{ $subject->prerequisite3->subject_code ?? ' - ' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>