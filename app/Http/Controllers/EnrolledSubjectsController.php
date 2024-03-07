<?php

namespace App\Http\Controllers;

use App\Models\Enrolled_Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EnrolledSubjectsController extends Controller
{
    public function store($enrollment_id)
    {
        $selectedSubjects = session('selectedSubjects');

        if (empty($selectedSubjects) || !is_array($selectedSubjects)) {
            Log::warning("Attempted to enroll subjects for enrollment_id $enrollment_id without selected subjects or in an incorrect format.");
            return redirect()->route('enrollments.enroll')->with('error', 'No subjects selected for enrollment or incorrect data format.');
        }

        DB::beginTransaction();

        try {
            foreach ($selectedSubjects as $subject) {
                if(isset($subject['subject_id']) && isset($subject['sec_sub_id'])) {
                    Enrolled_Subject::create([
                        'enrollment_id' => $enrollment_id,
                        'subject_id' => $subject['subject_id'],
                        'sec_sub_id' => $subject['sec_sub_id'], 
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('enrollments.enroll')->with('success', 'Student Enrolled Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to enroll subjects for enrollment_id $enrollment_id: " . $e->getMessage());
            return redirect()->route('enrollments.enroll')->with('error', 'Failed to enroll subjects. Please try again.');
        }
    }
}
