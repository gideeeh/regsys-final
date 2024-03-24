<?php

namespace App\Http\Controllers;

use App\Models\Enrolled_Subject;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

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

    public function submitStudentRecords(Request $request)
    {
        $studentRecordsData = $request->input('students');

        if (is_null($studentRecordsData)) {
            return response()->json(['message' => 'No student records provided'], 400);
        }

        // Validate each grades entry
        $validator = Validator::make($studentRecordsData, [
            '*.student_number' => 'required|string|max:45',
            '*.first_name' => 'required|string|max:45',
            '*.middle_name' => 'nullable|string|max:45',
            '*.last_name' => 'required|string|max:45',
            '*.suffix' => 'nullable|string|max:10',
            '*.sex' => 'required|string|max:10',
            '*.birthdate' => 'required|date',
            '*.birthplace' => 'required|string|max:255',
            '*.civil_status' => 'required|string|max:45',
            '*.nationality' => 'required|string|max:45',
            '*.religion' => 'nullable|string|max:255',
            '*.phone_number' => 'required|string|max:255',
            '*.personal_email' => 'required|email|max:255',
            '*.school_email' => 'required|email|max:255',
            '*.house_num' => 'nullable|string|max:45',
            '*.street' => 'nullable|string|max:255',
            '*.brgy' => 'required|string|max:255',
            '*.city_municipality' => 'required|string|max:255',
            '*.province' => 'required|string|max:255',
            '*.zipcode' => 'required|string|max:45',
            '*.guardian_name' => 'required|string|max:255',
            '*.guardian_contact' => 'required|string|max:45',
            '*.elementary' => 'nullable|string|max:255',
            '*.elem_yr_grad' => 'nullable|string|max:45',
            '*.jr_highschool' => 'nullable|string|max:255',
            '*.jr_hs_yr_grad' => 'nullable|string|max:45',
            '*.sr_highschool' => 'nullable|string|max:255',
            '*.sr_hs_yr_grad' => 'nullable|string|max:45',
            '*.college' => 'nullable|string|max:255',
            '*.college_year_ended' => 'nullable|string|max:45',
            '*.is_transferee' => 'required|boolean',
            '*.is_irregular' => 'required|boolean',
            '*.file_path' => 'nullable|string|max:255',
        ]);

        // testing
        // $validator = Validator::make($studentRecordsData, [
        //     '*.student_number' => 'required|string|max:45',
        //     '*.first_name' => 'required|string|max:45',
        //     '*.middle_name' => 'nullable|string|max:45',
        //     '*.last_name' => 'required|string|max:45',
        //     '*.suffix' => 'nullable|string|max:10',
        // ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid student records data', 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            foreach ($studentRecordsData as $studentRecordData) {

                if (isset($studentRecordData['is_transferee'])) {
                    $studentRecordData['is_transferee'] = $studentRecordData['is_transferee'] === "1" ? 1 : 0;
                }
                if (isset($studentRecordData['is_irregular'])) {
                    $studentRecordData['is_irregular'] = $studentRecordData['is_irregular'] === "1" ? 1 : 0;
                }
                
                $student = Student::where('student_number', $studentRecordData['student_number'])->first();
            
                if (!$student) {
                    $student = new Student($studentRecordData);
                    $studentDirectory = 'students/' . $student->student_number . ' ' . $student->last_name . ', ' . $student->first_name;
                    Storage::makeDirectory($studentDirectory);
                    
                    // Set the file_path for the student
                    $student->file_path = $studentDirectory;
                    $student->save();
                    $student->save();
                
                    $user = User::create([
                        'email' => $studentRecordData['personal_email'],
                        'password' => Hash::make('55Changemenow99'), 
                        'first_name' => $studentRecordData['first_name'],
                        'last_name' => $studentRecordData['last_name'],
                        'role' => 'user', 
                    ]);
    
                    $student->user_id = $user->id;
                    $student->save();
                } else {
                    $student->update($studentRecordData);
                }
            }
    
            DB::commit();
            return response()->json(['message' => 'Student records updated successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update student records: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to update student records', 'error' => $e->getMessage()], 500);
        }
    }

    /* Cashier System Get Data */

    public function cashier_system_get_data(Request $request)
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
    
    public function submitEnrollmentPaymentRecords(Request $request)
    {
        $enrollmentRecordsData = $request->input('enrollments');

        if (is_null($enrollmentRecordsData)) {
            return response()->json(['message' => 'No enrollment payment data provided'], 400);
        }

        $validator = Validator::make($enrollmentRecordsData, [
            '*.enrollment_code' => 'required|exists:enrollments,enrollment_code',
            '*.paid_enrollment' => 'required',
            '*.date_validation_cashier' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid enrollment payment records data', 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            foreach ($enrollmentRecordsData as $enrollmentRecordData) {

                if (isset($enrollmentRecordData['paid_clearance'])) {
                    $enrollmentRecordData['paid_clearance'] = $enrollmentRecordData['paid_clearance'] === "1" ? 1 : 0;
                }
                if (isset($enrollmentRecordData['paid_enrollment'])) {
                    $enrollmentRecordData['paid_enrollment'] = $enrollmentRecordData['paid_enrollment'] === "1" ? 1 : 0;
                }
                
                $enrollment = Enrollment::where('enrollment_code', $enrollmentRecordData['enrollment_code'])->first();
                $enrollment->update($enrollmentRecordData);
            }
    
            DB::commit();
            return response()->json(['message' => 'Enrollment payment data updated successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update enrollment payment data: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to update enrollment payment data', 'error' => $e->getMessage()], 500);
        }
    }
}
