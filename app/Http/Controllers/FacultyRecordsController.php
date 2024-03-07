<?php

namespace App\Http\Controllers;

use App\Models\Professor;
use Illuminate\Http\Request;

class FacultyRecordsController extends Controller
{
    public function index(Request $request)
    {
        $query = Professor::query()
            ->select('professors.prof_id','professors.first_name', 'professors.last_name', 'professors.suffix', 'departments.dept_name')
            ->join('departments', 'professors.dept_id', '=', 'departments.dept_id');
    
        $searchTerm = $request->query('query');
        if ($searchTerm) {
            $query->where(function ($query) use ($searchTerm) {
                $query->where('professors.first_name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('professors.last_name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('departments.dept_name', 'LIKE', "%{$searchTerm}%");
            });
        }
    
        $professorRecords = $query->paginate(10)->withQueryString(); 
    
        return view('admin.faculty-records', [
            'professors' => $professorRecords,
            'searchTerm' => $searchTerm 
        ]);
    }
    
    public function show($prof_id)
    {
        $professorRecord = Professor::findOrFail($prof_id);

        return view('admin.indiv-professor-record', ['professor' => $professorRecord]);
    }

    public function faculty_json()
    {
        $professors = Professor::all();
        return response()->json($professors);
    }

    public function fetch_faculty_json($prof_id)
    {
        $professor = Professor::findOrFail($prof_id);
        return response()->json([
            'prof_id' => $professor->prof_id,
            'first_name' => $professor->first_name,
            'middle_name' => $professor->middle_name,
            'last_name' => $professor->last_name,
            'suffix' => $professor->suffix,
            'dept_id' => $professor->dept_id,
            'personal_email' => $professor->personal_email,
            'school_email' => $professor->school_email,
        ]);
    }

    public function searchFaculty(Request $request)
    {
        $searchTerm = $request->input('q');

        // Fetch and filter faculty based on the search term
        $professors = Professor::where('first_name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('last_name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('prof_id', 'LIKE', "%{$searchTerm}%")
                    ->get([
                        'prof_id', 'first_name', 'middle_name', 'last_name', 'suffix'
                    ]);

        return response()->json($professors);
    }
}
