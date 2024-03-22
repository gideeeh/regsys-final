<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Program;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use PDF;

class PrintablesController extends Controller
{
    public function printGradeSlip($enrollmentId)
    {
        $user = Auth::user(); 
        $user_firstname = $user->first_name; 
        $user_lastname = $user->last_name;
        $full_name = $user_firstname .' '. $user_lastname;

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

        $gradedSubjectsCount = 0;
        $gradesSum = 0;
        $total_units_enrollment = 0;

        $enrolledSubjectsData = $enrollment->enrolledSubjects->map(function ($enrolledSubject) use (&$gradesSum, &$gradedSubjectsCount, &$total_units_enrollment) {
            $total_units = $enrolledSubject->subject->units_lec + $enrolledSubject->subject->units_lab;

            if ($enrolledSubject->final_grade !== null && is_numeric($enrolledSubject->final_grade)) {
                $gradesSum += $enrolledSubject->final_grade;
                $gradedSubjectsCount++;
            }

            $total_units_enrollment +=$total_units;

            return [
                'program_code' => $enrolledSubject->enrollment->program->program_code ?? 'N/A',
                'subject_code' => $enrolledSubject->subject->subject_code,
                'subject_name' => $enrolledSubject->subject->subject_name,
                'final_grade' => $enrolledSubject->final_grade ?? 'Not Graded',
                'remarks' => $enrolledSubject->remarks !== null && $enrolledSubject->remarks !== '' ? $enrolledSubject->remarks : 'No remarks',
                'total_units' => $total_units,
            ];
        });

        $averageGrade = $gradedSubjectsCount > 0 ? number_format($gradesSum / $gradedSubjectsCount, 2) : 'No Grades';
        $dateToday = \Carbon\Carbon::parse(now())->format('M d, Y');

    
        // Generate PDF using the prepared data
        $pdf = PDF::loadView('admin.printable_templates.gradeslip-template', [
            'student' => $student,
            'enrollment' => $enrollment,
            'student_name' => $student_name,
            'enrolledSubjectsData' => $enrolledSubjectsData,
            'averageGrade' => $averageGrade,
            'dateToday' => $dateToday,
            'full_name' => $full_name,
            'total_units_enrollment' => $total_units_enrollment,
        ]);
    
        $filename = 'Gradeslip-' . $enrollment->academic_year . '-T' . $enrollment->term . '-(' . $student->student_number . ')' . $student->last_name . ',' . substr($student->first_name, 0, 1) . '.pdf';
    
        return $pdf->download($filename);
    }
    

    public function view_gradeslip($enrollmentId)
    {
        $user = Auth::user(); 
        $user_firstname = $user->first_name; 
        $user_lastname = $user->last_name;
        $full_name = $user_firstname .' '. $user_lastname;

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

        $gradedSubjectsCount = 0;
        $gradesSum = 0;
        $total_units_enrollment = 0;
    
        // Prepare enrolled subjects data
        $enrolledSubjectsData = $enrollment->enrolledSubjects->map(function ($enrolledSubject) use (&$gradesSum, &$gradedSubjectsCount, &$total_units_enrollment) {
            $total_units = $enrolledSubject->subject->units_lec + $enrolledSubject->subject->units_lab;

            if ($enrolledSubject->final_grade !== null && is_numeric($enrolledSubject->final_grade)) {
                $gradesSum += $enrolledSubject->final_grade;
                $gradedSubjectsCount++;
            }

            $total_units_enrollment +=$total_units;

            return [
                'program_code' => $enrolledSubject->enrollment->program->program_code ?? 'N/A',
                'subject_code' => $enrolledSubject->subject->subject_code,
                'subject_name' => $enrolledSubject->subject->subject_name,
                'final_grade' => $enrolledSubject->final_grade ?? 'Not Graded',
                'remarks' => $enrolledSubject->remarks ?? 'No remarks',
                'total_units' => $total_units,
            ];
        });

        $averageGrade = $gradedSubjectsCount > 0 ? number_format($gradesSum / $gradedSubjectsCount, 2) : 'No Grades';
        $dateToday = \Carbon\Carbon::parse(now())->format('M d, Y');
    
        return view('admin.printable_templates.gradeslip-template', compact(
            'student', 
            'enrollment', 
            'enrolledSubjectsData',
            'total_units_enrollment',
            'student_name',
            'averageGrade',
            'dateToday',
            'full_name' 
        ));
    }

    public function view_tor($student_id, $program_id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'You are not authorized to perform this action.');
        }
        
        $student = Student::with([
            'enrollments' => function($query) use ($program_id) {
                $query->where('program_id', $program_id)
                      ->with('program', 'enrolledSubjects.subject', 'enrolledSubjects.sectionSubject.subjectSectionSchedule.professor');
            }
        ])->findOrFail($student_id);
    
        // You may still need to organize enrollments by year and term if needed
        $organizedEnrollments = [];
        foreach ($student->enrollments as $enrollment) {
            $yearLevel = $enrollment->year_level;
            $term = $enrollment->term;
    
            if (!isset($organizedEnrollments[$yearLevel])) {
                $organizedEnrollments[$yearLevel] = [];
            }
            if (!isset($organizedEnrollments[$yearLevel][$term])) {
                $organizedEnrollments[$yearLevel][$term] = [];
            }
    
            foreach ($enrollment->enrolledSubjects as $subject) {
                $organizedEnrollments[$yearLevel][$term][] = $subject;
            }
            
            // Sort subjects within each term by their code
            usort($organizedEnrollments[$yearLevel][$term], function ($a, $b) {
                return strcmp($a->subject->subject_code, $b->subject->subject_code);
            });
        }

        ksort($organizedEnrollments); 
        foreach ($organizedEnrollments as $yearLevel => &$terms) {
            ksort($terms); 
        }
    
        // Fetch the program details separately if needed
        $program = Program::findOrFail($program_id);
    
        return view('admin.printable_templates.tor-template', [
            'student' => $student,
            'program' => $program,
            'organizedEnrollments' => $organizedEnrollments,
        ]);
    }
    
    public function print_tor($student_id, $program_id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'You are not authorized to perform this action.');
        }
        
        $student = Student::with([
            'enrollments' => function($query) use ($program_id) {
                $query->where('program_id', $program_id)
                      ->with('program', 'enrolledSubjects.subject', 'enrolledSubjects.sectionSubject.subjectSectionSchedule.professor');
            }
        ])->findOrFail($student_id);
    
        // You may still need to organize enrollments by year and term if needed
        $organizedEnrollments = [];
        foreach ($student->enrollments as $enrollment) {
            $yearLevel = $enrollment->year_level;
            $term = $enrollment->term;
    
            if (!isset($organizedEnrollments[$yearLevel])) {
                $organizedEnrollments[$yearLevel] = [];
            }
            if (!isset($organizedEnrollments[$yearLevel][$term])) {
                $organizedEnrollments[$yearLevel][$term] = [];
            }
    
            foreach ($enrollment->enrolledSubjects as $subject) {
                $organizedEnrollments[$yearLevel][$term][] = $subject;
            }
            
            // Sort subjects within each term by their code
            usort($organizedEnrollments[$yearLevel][$term], function ($a, $b) {
                return strcmp($a->subject->subject_code, $b->subject->subject_code);
            });
        }

        ksort($organizedEnrollments); 
        foreach ($organizedEnrollments as $yearLevel => &$terms) {
            ksort($terms); 
        }
        
        
        $program = Program::findOrFail($program_id);
    
        $pdf = PDF::loadView('admin.printable_templates.tor-template', [
            'student' => $student,
            'program' => $program,
            'organizedEnrollments' => $organizedEnrollments,
        ]);
    
        $filename = 'TOR-' . $program->program_code . '-' . $student->student_number . '-' . $student->last_name . '.pdf';
    
        return $pdf->download($filename);
    }

    public function layout()
    {
        return view('admin.printable_templates.layout-tool');
    }
}
