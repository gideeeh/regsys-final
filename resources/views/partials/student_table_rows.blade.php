
@foreach ($students as $student)
    <tr class="border-b hover:bg-gray-100" x-data="{}" @click="window.location.href='{{ route('student-records.show', $student->student_id) }}'" style="cursor: pointer;">
        <td class="px-6 py-4 text-left">{{$student->student_number}}</td>
        <td class="px-6 py-4 text-left">{{$student->first_name}}</td>
        <td class="px-6 py-4 text-left">{{$student->last_name.' '.$student->suffix}}</td>
        <td class="px-6 py-4 text-left">{{$student->program_code}}</td>
        <td class="px-6 py-4 text-left">{{$student->year_level}}</td>
    </tr>
@endforeach