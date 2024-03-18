<?php

namespace App\Http\Controllers;

use App\Models\Enrolled_Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class GradesController extends Controller
{
    public function submitGrades(Request $request)
    {
        $grades = $request->input('grades');

        if (is_null($grades)) {
            return response()->json(['message' => 'No grades provided'], 400);
        }

        DB::beginTransaction();
        try {
            foreach ($grades as $grade) {
                $enrolledSubjectCode = $grade['enrolledSubject_code'];
                $finalGrade = $grade['final_grade'];

                $enrolledSubject = Enrolled_Subject::where('enrolledSubject_code', $enrolledSubjectCode)->first();

                if ($enrolledSubject) {
                    // Update the final grade
                    $enrolledSubject->update([
                        'final_grade' => $finalGrade,
                    ]);
                } else {
                    Log::warning("Enrolled subject with code {$enrolledSubjectCode} not found.");
                }
            }

            DB::commit();
            return response()->json(['message' => 'Grades updated successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update grades: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to update grades'], 500);
        }
    }
}
