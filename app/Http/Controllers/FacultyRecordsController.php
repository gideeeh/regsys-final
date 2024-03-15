<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Professor;
use Illuminate\Http\Request;

class FacultyRecordsController extends Controller
{
    public function index(Request $request)
    {
        $query = Professor::query()
            ->select(
                'professors.prof_id',
                'professors.first_name',
                'professors.middle_name', 
                'professors.last_name', 
                'professors.suffix', 
                'professors.personal_email', 
                'professors.school_email', 
                'departments.dept_name',
                'departments.dept_id',
            )
            ->join('departments', 'professors.dept_id', '=', 'departments.dept_id');
    
        $searchTerm = $request->query('query');
        if ($searchTerm) {
            $query->where(function ($query) use ($searchTerm) {
                $query->where('professors.first_name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('professors.last_name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('departments.dept_name', 'LIKE', "%{$searchTerm}%");
            });
        }

        $departments = Department::all();
    
        $professorRecords = $query->paginate(10)->withQueryString(); 
    
        return view('admin.faculty-records', [
            'professors' => $professorRecords,
            'searchTerm' => $searchTerm,
            'departments' => $departments 
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

    public function store(Request $request)
    {
        $professor = new Professor();
        $professor->first_name = $request->first_name;
        $professor->middle_name = $request->middle_name;
        $professor->last_name = $request->last_name;
        $professor->suffix = $request->suffix;
        $professor->dept_id = $request->department;
        $professor->personal_email = $request->personal_email;
        $professor->school_email = $request->school_email;
        $professor->save();
    
        return redirect()->back()->with('success', 'Faculty record created successfully!');
    }

    public function update(Request $request, $id)
    {
        $professor = Professor::find($id);
        if($professor)
        {
            $professor->first_name = $request->first_name;
            $professor->middle_name = $request->middle_name;
            $professor->last_name = $request->last_name;
            $professor->suffix = $request->suffix;
            $professor->dept_id = $request->department;
            $professor->personal_email = $request->personal_email;
            $professor->school_email = $request->school_email;
            $professor->save();

            return redirect()->back()->with('success', 'Faculty record successfully updated!');
        }
        else {
            return redirect()->back()->with('error', 'There seems to be an error in updating the record.');
        }
    }

    public function destroy($id)
    {
        $professor = Professor::find($id);
        if ($professor) {
            $professor->delete();
            return redirect()->back()->with('success', 'Faculty record successfully deleted!');
        } else {
            return redirect()->back()->with('error', 'Faculty record not found!');
        }
    }
}
