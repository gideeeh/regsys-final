<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\Subject;
use Illuminate\Http\Request;

class CourseListingsController extends Controller
{
    public function index(Request $request)
    {
        $query = Program::query()
            ->select('programs.program_id','programs.program_code','programs.program_name', 'programs.degree_type', 'departments.dept_name', 'programs.total_units','programs.status')
            ->join('departments', 'programs.dept_id', '=', 'departments.dept_id');
    
        $searchTerm = $request->query('query');
        if ($searchTerm) {
            $query->where(function ($query) use ($searchTerm) {
                $query->where('programs.program_code', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('programs.program_name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('programs.degree_type', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('departments.dept_name', 'LIKE', "%{$searchTerm}%");
            });
        }
    
        $courseListings = $query->paginate(10)->withQueryString(); 
    
        return view('admin.course-listings', [
            'courses' => $courseListings,
            'searchTerm' => $searchTerm 
        ]);
    }

    public function subject_catalog(Request $request)
    {
        $query = Program::query()
            ->select('programs.program_id','programs.program_code','programs.program_name', 'programs.degree_type', 'departments.dept_name', 'programs.total_units','programs.status')
            ->join('departments', 'programs.dept_id', '=', 'departments.dept_id');
    
        $searchTerm = $request->query('query');
        if ($searchTerm) {
            $query->where(function ($query) use ($searchTerm) {
                $query->where('programs.program_code', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('programs.program_name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('programs.degree_type', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('departments.dept_name', 'LIKE', "%{$searchTerm}%");
            });
        }
    
        $courseListings = $query->paginate(10)->withQueryString(); 
    
        return view('admin.subject-catalog', [
            'courses' => $courseListings,
            'searchTerm' => $searchTerm 
        ]);

    }

    public function store(Request $request)
    {
        $subject = new Subject();
        $subject->subject_code = $request->subject_code;
        $subject->subject_name = $request->subject_name;
        $subject->subject_description = $request->subject_description;
        $subject->units_lec = $request->units_lec;
        $subject->units_lab = $request->units_lab;
        $subject->prerequisite1 = $request->prereq1_subject_id;
        $subject->prerequisite2 = $request->prereq2_subject_id;
        $subject->prerequisite3 = $request->prereq3_subject_id;
        $subject->save();
    
        return redirect()->back()->with('success', 'Program added successfully!');
    }
}
