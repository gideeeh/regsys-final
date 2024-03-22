<?php

namespace App\Http\Controllers;

use App\Models\Enrolled_Subject;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;

class InfosystemsController extends Controller
{
    public function grading_system_get_data(Request $request)
    {
        $data = [
            'students' => Student::select('student_id', 'first_name', 'middle_name', 'last_name', 'suffix', 'student_number')->get(),
            'enrollments' => Enrollment::all(),
            'programs' => Program::all(),
            'enrolled_subjects' => Enrolled_Subject::select('en_subjects_id', 'enrollment_id', 'subject_id', 'final_grade', 'remarks')->get(),
            'subjects' => Subject::select('subject_id', 'subject_code', 'subject_name', 'units_lec', 'units_lab')->get(),
        ];

        return response()->json($data);
    }
}
