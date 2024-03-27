<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Slip</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">
    <div class="container mx-auto my-8 p-5 bg-white rounded shadow-xl">
        <h2 class="text-2xl font-bold mb-2 text-gray-800">Appointment Slip</h2>
        <div class="border-b-2 border-gray-200 mb-4"></div>
        <div class="text-gray-700">
            <div class="mb-4">
                <strong>Name:</strong> {{$student->first_name}} {{ $student->middle_name ? substr($student->middle_name, 0, 1) . '.' : '' }} {{$student->last_name}}
            </div>
            <div class="mb-4">
                <strong>Student No.:</strong> {{$student->student_number}}
            </div>
            <div class="mb-4">
                <strong>Appointment Code:</strong> {{$appointment->appointment_code}}
            </div>
            <div class="mb-4">
                <strong>Date Created:</strong> {{\Carbon\Carbon::parse($appointment->appointment_datetime)->format('M d, Y')}}
            </div>
            <div class="mb-4">
                <strong>Request:</strong> {{$appointment->concern}}
            </div>
            <div>
                <strong>Notes:</strong> {{$appointment->notes ?? 'Notes unavailable.'}}
            </div>
        </div>
    </div>
</body>
</html>
