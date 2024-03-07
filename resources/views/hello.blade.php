<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Departments</title>
</head>
<body>
    <h1>Departments</h1>
    <ul>
        @foreach ($departments as $department)
            <li>{{ $department->dept_name }}</li>
        @endforeach
    </ul>
</body>
</html>
