<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Student;
use PDF;

class PrintablesController extends Controller
{
    public function printGradeSlip($enrollmentId)
    {
        $enrollment = Enrollment::with([
            'program',
            'student',
            'enrolledSubjects.subject',
        ])->findOrFail($enrollmentId);
    
        $student = $enrollment->student;
        $student_name = $student->first_name. ' ';
        if($student->middle_name)
        {
            $student_name += $student->middle_name.' ';
        }
        $student_name .= $student->last_name;
    
        // Prepare enrolled subjects data for PDF rendering
        $enrolledSubjectsData = $enrollment->enrolledSubjects->map(function ($enrolledSubject) {
            $total_units = $enrolledSubject->subject->units_lec + $enrolledSubject->subject->units_lab;
            return [
                'program_code' => $enrolledSubject->enrollment->program->program_code ?? 'N/A',
                'subject_code' => $enrolledSubject->subject->subject_code,
                'subject_name' => $enrolledSubject->subject->subject_name,
                'final_grade' => $enrolledSubject->final_grade ?? 'Not Graded',
                'remarks' => $enrolledSubject->remarks ?? 'No remarks',
                'total_units' => $total_units,
            ];
        });
    
        // Generate PDF using the prepared data
        $pdf = PDF::loadView('admin.printable_templates.gradeslip-template', [
            'student' => $student,
            'enrollment' => $enrollment,
            'student_name' => $student_name,
            'enrolledSubjectsData' => $enrolledSubjectsData,
        ]);
    
        $filename = 'Gradeslip-' . $enrollment->academic_year . '-T' . $enrollment->term . '-(' . $student->student_number . ')' . $student->last_name . ',' . substr($student->first_name, 0, 1) . '.pdf';
    
        return $pdf->download($filename);
    }
    

    public function view_gradeslip($enrollmentId)
    {
        $enrollment = Enrollment::with([
            'program',
            'student',
            'enrolledSubjects.subject',
        ])->findOrFail($enrollmentId);
    
        $student = $enrollment->student;
        $student_name = $student->first_name. ' ';
        if($student->middle_name)
        {
            $student_name += $student->middle_name.' ';
        }
        $student_name .= $student->last_name;
    
        // Prepare enrolled subjects data
        $enrolledSubjectsData = $enrollment->enrolledSubjects->map(function ($enrolledSubject) {
            $total_units = $enrolledSubject->subject->units_lec + $enrolledSubject->subject->units_lab;
            return [
                'program_code' => $enrolledSubject->enrollment->program->program_code ?? 'N/A',
                'subject_code' => $enrolledSubject->subject->subject_code,
                'subject_name' => $enrolledSubject->subject->subject_name,
                'final_grade' => $enrolledSubject->final_grade ?? 'Not Graded',
                'remarks' => $enrolledSubject->remarks ?? 'No remarks',
                'total_units' => $total_units,
            ];
        });
    
        return view('admin.printable_templates.gradeslip-template', compact(
            'student', 
            'enrollment', 
            'enrolledSubjectsData',
            'student_name'
        ));
    }
}
