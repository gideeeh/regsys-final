<?php

namespace App\Http\Controllers;

use App\Models\Enrolled_Subject;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\TemplateProcessor;
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

    public function print_cor_sample()
    {
        $templateProcessor = new TemplateProcessor(public_path('templates/sample_template.docx'));
        $templateProcessor->setValue('stu_num', '123456');
        $templateProcessor->setValue('fname', 'Joe');
        $lastNameValue = (isset($lastName) && !empty($lastName)) ? $lastName : '';
        $templateProcessor->setValue('lname', $lastNameValue);

        $fileName = 'filled-template.docx';
        $templateProcessor->saveAs(storage_path("app/public/{$fileName}"));

        return response()->download(storage_path("app/public/{$fileName}"));

    }

    public function print_cor($enrollment_id)
    {
        $enrollment = Enrollment::with(['program', 'student'])
                                ->where('enrollment_id', $enrollment_id)
                                ->first();
        if (!$enrollment) {
            return response()->json(['message' => 'Enrollment not found'], 404);
        }

        $enrolledSubjects = Enrolled_Subject::with(['subject','sectionSubject','sectionSubject.subjectSectionSchedule','sectionSubject.section'])
                                            ->where('enrollment_id', $enrollment_id)
                                            ->get();
        
        if (!$enrolledSubjects) {
            return response()->json(['message' => 'No enrolled subjects'], 404);
        }

        $student_number = $enrollment->student->student_number ?? '-';
        $status = ucfirst($enrollment->enrollment_method) ?? '-';
        $validation = '-';
        $batch = '-';

        $term = $enrollment->term ?? '-';
        $school_year = $enrollment->academic_year ?? '-';
        $term_sy = $term . ' / '. $school_year;
        $program_code = $enrollment->program->program_code ?? '-';
        $year_level = $enrollment->year_level ?? '-';
        $year = '';

        switch ($year_level) {
            case 1:
                $year = "1st";
                break;
            case 2:
                $year = "2nd";
                break;
            case 3:
                $year = "3rd";
                break;
            case 4:
                $year = "4th";
                break;
            default:
                $year = "-";
                break;
        }
        $gender = $enrollment->student->gender ?? '-';
        $scholarship = ucfirst($enrollment->scholarship_type) ?? '-';
        $first_name = $enrollment->student->first_name ?? ' ';
        $middle_name = $enrollment->student->middle_name ?? '';
        $middle_initial = !empty($middle_name) ? substr($middle_name, 0, 1) . '.' : '';
        $last_name = $enrollment->student->last_name ?? ' ';
        $name = $last_name . ', ' . $first_name . ' ' . $middle_initial;
        $name = strtoupper($name);
        $addressParts = [
            $enrollment->student->house_num,
            $enrollment->student->street,
            $enrollment->student->brgy,
            $enrollment->student->city_municipality,
            $enrollment->student->province,
            $enrollment->student->zipcode,
        ];
        $address = implode(', ', array_filter($addressParts, function($value) { 
            return !empty($value); 
        }));
        $contact = $enrollment->student->phone_number ?? '-';

        
        $user = Auth::user(); 
        $user_firstname = $user->first_name; 
        $user_lastname = $user->last_name;
        // $registrar = $user_firstname .' '. $user_lastname;
        $registrar = 'JANETH M. PARAFINA MBA';
        $cashier = 'RALPH ALITAGTAG';
        if($enrollment->date_validation_cashier) {
            $date_validation_cashier = \Carbon\Carbon::parse($enrollment->$enrollment->date_validation_cashier )->format('m/d/Y');
        } else {
            $date_validation_cashier = '-';
        }

        if($enrollment->date_validation_registrar) {
            $date_validation_registrar = \Carbon\Carbon::parse($enrollment->date_validation_registrar )->format('m/d/Y');
        } else {
            $date_validation_registrar = '-';
        }
        
        /* End of Details */
        $templateProcessor = new TemplateProcessor(public_path('templates/sample_template.docx'));
        $templateProcessor->setValue('stu_num', $student_number);
        $templateProcessor->setValue('status', $status);
        $templateProcessor->setValue('validation', $validation);
        $templateProcessor->setValue('batch', $batch);

        $templateProcessor->setValue('sem_sy', $term_sy);
        $templateProcessor->setValue('course_code', $program_code);
        $templateProcessor->setValue('year', $year);
        $templateProcessor->setValue('gender', $gender);
        $templateProcessor->setValue('scholarship', $scholarship);

        $templateProcessor->setValue('stu_name', $name);
        $templateProcessor->setValue('stu_address', $address);
        $templateProcessor->setValue('stu_contact', $contact);
        $templateProcessor->setValue('registrar', $registrar);
        $templateProcessor->setValue('cashier', $cashier);
        $templateProcessor->setValue('date_validation_cashier', $date_validation_cashier);
        $templateProcessor->setValue('date_validation_registrar', $date_validation_registrar);

        for ($i = 0; $i < 11; $i++) {
            // Check if there is an enrolled subject at this index
            if (isset($enrolledSubjects[$i])) {
                $enrolledSubject = $enrolledSubjects[$i];
                $subject_code = $enrolledSubject->subject->subject_code ?? '';
                $description = $enrolledSubject->subject->subject_name ?? '';
                $units_lec = $enrolledSubject->subject->units_lec ?? 0;
                $units_lab = $enrolledSubject->subject->units_lab ?? 0;
                $units = $units_lec + $units_lab;

                $daysF2F = json_decode($enrolledSubject->sectionSubject->subjectSectionSchedule?->class_days_f2f, true) ?? [];
                $dayMap = [
                    'Monday' => 'Mon',
                    'Tuesday' => 'Tue',
                    'Wednesday' => 'Wed',
                    'Thursday' => 'Thu',
                    'Friday' => 'Fri',
                    'Saturday' => 'Sat',
                    'Sunday' => 'Sun',
                ];
                $abbreviatedDaysF2F = array_map(function($day) use ($dayMap) {
                    return $dayMap[$day] ?? $day;
                }, $daysF2F);

                if($enrolledSubject->sectionSubject->subjectSectionSchedule?->class_days_f2f)
                    $day = implode(', ', $abbreviatedDaysF2F) ;
                else {
                    $day = '-';
                }

                if($enrolledSubject->sectionSubject->subjectSectionSchedule?->start_time_f2f && $enrolledSubject->sectionSubject->subjectSectionSchedule?->end_time_f2f)
                    $time = \Carbon\Carbon::parse($enrolledSubject->sectionSubject->subjectSectionSchedule?->start_time_f2f)->format('h:i A') . '-'. \Carbon\Carbon::parse($enrolledSubject->sectionSubject->subjectSectionSchedule?->end_time_f2f)->format('h:i A');
                else {
                    $time = '-';
                }

                $section = str_replace("Section ", "", $enrolledSubject->sectionSubject->section->section_name);
            } else {
                $subject_code = '';
                $description = '';
                $units = '';
                $day = '';
                $time = '';
                $section = '';
            }
        
            $placeholderIndex = $i + 1;
        
            $templateProcessor->setValue("code$placeholderIndex", $subject_code);
            $templateProcessor->setValue("desc$placeholderIndex", $description);
            $templateProcessor->setValue("unit$placeholderIndex", $units);
            $templateProcessor->setValue("day$placeholderIndex", $day);
            $templateProcessor->setValue("time$placeholderIndex", $time);
            $templateProcessor->setValue("sect$placeholderIndex", $section);
        }

        // $student_number = $enrollment->student->student_number ?? '-';
        // $status = ucfirst($enrollment->enrollment_method) ?? '-';
        // $validation = '-';
        // $batch = '-';

        // $term = $enrollment->term ?? '-';
        // $school_year = $enrollment->academic_year ?? '-';
        // $term_sy = $term . ' / '. $school_year;
        // $program_code = $enrollment->program->program_code ?? '-';
        // $year_level = $enrollment->year_level ?? '-';
        // $year = '';
        // $gender = $enrollment->student->gender ?? '-';
        // $scholarship = ucfirst($enrollment->scholarship_type) ?? '-';
        // $first_name = $enrollment->student->first_name ?? ' ';
        // $middle_name = $enrollment->student->middle_name ?? '';
        // $middle_initial = !empty($middle_name) ? substr($middle_name, 0, 1) . '.' : '';
        // $last_name = $enrollment->student->last_name ?? ' ';
        // $name = $last_name . ', ' . $first_name . ' ' . $middle_initial;
        // $name = strtoupper($name);
        $fileName = 'COR_' . $last_name .'_'. $first_name . '_' . $school_year . '_' . 'T' . $term . '_' . $student_number . '.docx';
        $fileName = preg_replace('/[^A-Za-z0-9.\-_]/', '', $fileName); // Basic sanitization
        $templateProcessor->saveAs(storage_path("app/public/{$fileName}"));

        return response()->download(storage_path("app/public/{$fileName}"))->deleteFileAfterSend(true);
    }
}
