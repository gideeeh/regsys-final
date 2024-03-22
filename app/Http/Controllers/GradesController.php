<?php

namespace App\Http\Controllers;

use App\Models\Enrolled_Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class GradesController extends Controller
{
    // From API Grading System
    public function submitGrades(Request $request)
    {
        $gradesData = $request->input('grades');
    
        if (is_null($gradesData)) {
            return response()->json(['message' => 'No grades provided'], 400);
        }
    
        // Validate each grades entry
        $validator = Validator::make($gradesData, [
            '*.enrolledSubject_code' => 'required|exists:enrolled_subjects,enrolledSubject_code',
            '*.final_grade' => 'required|numeric',
            '*.remarks' => 'sometimes|string'
        ]);
    
        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid grades data', 'errors' => $validator->errors()], 422);
        }
    
        DB::beginTransaction();
        try {
            foreach ($gradesData as $gradeData) {
                $enrolledSubject = Enrolled_Subject::where('enrolledSubject_code', $gradeData['enrolledSubject_code'])->first();
    
                $enrolledSubject->update([
                    'final_grade' => $gradeData['final_grade'],
                    'remarks' => $gradeData['remarks'] ?? null,
                ]);
            }
    
            DB::commit();
            return response()->json(['message' => 'Grades updated successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update grades: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to update grades', 'error' => $e->getMessage()], 500);
        }
    }

    public function update_grade(Request $request, $enrollment_id, $enrolledSubject_id)
    {
        // Check if user is 'admin'
        if (!Auth::user()->role === 'admin') { 
            return back()->with('error', 'Unauthorized');
        }

        // Verify if password is correct
        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return back()->with('error', 'Verification failed');
        }

        // Update Enrolled_Subject model
        $enrolledSubject = Enrolled_Subject::where('enrollment_id', $enrollment_id)
                            ->where('en_subjects_id', $enrolledSubject_id) 
                            ->first();

        if ($enrolledSubject) {
            $enrolledSubject->final_grade = $request->grade;
            $enrolledSubject->remarks = $request->remarks;
            $enrolledSubject->save();

            return back()->with('success','Grade successfully updated!');
        }

        return back()->with('error', 'Error in updating grade');
    }
}
