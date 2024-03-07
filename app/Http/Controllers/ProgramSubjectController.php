<?php

namespace App\Http\Controllers;

use App\Models\Program_Subject;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProgramSubjectController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'program_id' => 'required|exists:programs,program_id', 
            'year' => 'required',
            'term' => 'required',
            'subject_ids' => 'sometimes|array',
            'subject_ids.*' => 'exists:subjects,subject_id' 
        ]);
        
        $programId = $request->program_id;
        $year = $request->year;
        $term = $request->term;
        
        $currentSubjectIds = Program_Subject::where('program_id', $programId)
                                             ->where('year', $year)
                                             ->where('term', $term)
                                             ->pluck('subject_id')->toArray();

        $submittedSubjectIds = $request->subject_ids ?? [];
    
        $subjectsToAdd = array_diff($submittedSubjectIds, $currentSubjectIds);
        
        $subjectsToRemove = array_diff($currentSubjectIds, $submittedSubjectIds);
    
        foreach ($subjectsToAdd as $subjectId) {
            try {
                Program_Subject::create([
                    'program_id' => $programId,
                    'subject_id' => $subjectId,
                    'year' => $year,
                    'term' => $term,
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
                // Check if the error code is for a duplicate entry
                if ($e->errorInfo[1] == 1062) {

                    $subject = Subject::find($subjectId);
                    $subjectName = $subject ? $subject->subject_name : 'Unknown Subject';
                    $subjectCode = $subject ? $subject->subject_code : 'Unknown Code';
                    // $subjectName = Subject::where('subject_id', $subjectId)->value('subject_name');
                    // $subjectCode = Subject::where('subject_code', $subjectId)->value('subject_code');
                    return back()->with('error', "The subject '{$subjectCode} - {$subjectName}' is already assigned to the program for the specified term and year.");
                } else {
                    // Log or handle other database errors
                    Log::error('Database error: ' . $e->getMessage());
                    return back()->with('error', 'An unexpected error occurred. Please try again.');
                }
            }
        }
    
        if (!empty($subjectsToRemove)) {
            Program_Subject::where('program_id', $programId)
                           ->where('year', $year)
                           ->where('term', $term)
                           ->whereIn('subject_id', $subjectsToRemove)
                           ->delete();
        }
    
        Log::info('Request data:', $request->all());
        return back()->with('success', 'Subjects updated successfully.');
    }

    public function fetchSubjects(Request $request, $program_id, $year, $term)
    {
        $selectedSubjects = Program_Subject::where('program_id', $program_id)
                                        ->where('year', $year)
                                        ->where('term', $term)
                                        ->pluck('subject_id');

        return response()->json($selectedSubjects);
    }

    public function fetchProgramSubjects(Request $request)
    {
        // Extract request parameters
        $program = $request->input('program');
    
        // Base query
        $query = DB::table('program_subjects as ps')
                    ->join('subjects as s', 'ps.subject_id', '=', 's.subject_id');
    
        // If a specific program is requested
        if ($program && $program !== 'all') {
            $query = $query->join('programs as p', 'ps.program_id', '=', 'p.program_id')
                           ->select('p.program_code','s.subject_id as subject_id', 's.subject_code as subject_code', 's.subject_name as subject_name')
                           ->where('p.program_id', $program);
        } else {
            // If "Select All" or no program is specified
            $query = $query->select('s.subject_id as subject_id', 's.subject_code as subject_code', 's.subject_name as subject_name')
                           ->distinct();
        }

        if ($request->has('term')) {
            $query->where('term', $request->term);
        }
        if ($request->has('year_level')) {
            $query->where('year', $request->year_level);
        }
    
        $subjects = $query->get();
    
        return response()->json($subjects);
    }
    
    
    public function program_subjects_json() {
        $program_subjects = Program_Subject::all();

        return response()->json($program_subjects);
    }
}
