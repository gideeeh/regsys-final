<?php

namespace App\Http\Controllers;

use App\Exports\ProfessorScheduleExport;
use App\Models\Department;
use App\Models\Professor;
use App\Models\SectionSubject;
use App\Services\AcademicYearService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class FacultyRecordsController extends Controller
{
    protected $academicYearService;

    public function __construct(AcademicYearService $academicYearService)
    {
        $this->academicYearService = $academicYearService;
    }
    
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
    
    public function show(Request $request, $prof_id)
    {
        $activeAcadYearAndTerm  = $this->academicYearService->determineActiveAcademicYearAndTerm();
        if (!$activeAcadYearAndTerm) {
            return redirect()->back()->with('error', 'No active academic year found.');
        }
        $activeAcadYearDetails = $activeAcadYearAndTerm['activeAcadYear'];
        $activeAcadYear = $activeAcadYearDetails->acad_year;
        $activeTerm = $activeAcadYearAndTerm['activeTerm'];

        $professorRecord = Professor::findOrFail($prof_id);
        $classes = SectionSubject::with(['subjectSectionSchedule', 'section', 'subject'])
            ->whereHas('section', function($query) use ($activeAcadYear, $activeTerm) {
                $query->where('academic_year', $activeAcadYear)
                    ->where('term', $activeTerm);
            })
            ->whereHas('subjectSectionSchedule', function($query) use ($prof_id) {
                $query->where('prof_id', $prof_id);
            })
            ->get();

        return view('admin.faculty-records-show', compact('classes', 'professorRecord', 'activeAcadYear', 'activeTerm'));
    }

    public function faculty_json(Request $request)
    {
        $search = $request->get('q');

        // $subjects = Subject::where('subject_name','like','%'.$search.'%')
        //                     ->orWhere('subject_code','like','%'.$search.'%')
        //                     ->get([
        //                         'subject_id as subject_id',
        //                         'subject_name as subject_name',
        //                         'subject_code as subject_code',
        //                         'subject_description as subject_description',
        //                         'units_lec as units_lec',
        //                         'units_lab as units_lab',
        //                         'prerequisite_1 as prerequisite_1',
        //                         'prerequisite_2 as prerequisite_2',
        //                         'prerequisite_3 as prerequisite_3',
        //                     ]);
        $professors = Professor::where('first_name','like','%'.$search.'%')
                                ->orWhere('last_name','like','%'.$search.'%')
                                ->get();
        
        // $professors = Professor::all();
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

    public function exportSchedule($prof_id)
    {
        $activeAcadYearAndTerm = $this->academicYearService->determineActiveAcademicYearAndTerm();
        if (!$activeAcadYearAndTerm) {
            return redirect()->back()->with('error', 'No active academic year found.');
        }

        $activeAcadYear = $activeAcadYearAndTerm['activeAcadYear']->acad_year;
        $activeTerm = $activeAcadYearAndTerm['activeTerm'];

        $professor = Professor::find($prof_id);
        $professorName = "{$professor->first_name} {$professor->last_name}";
        $filenameFriendlyProfessorName = str_replace([' ', '/'], '_', $professorName);
        $formatTerm = 'T' . $activeTerm;
        $filename = "{$filenameFriendlyProfessorName}_{$formatTerm}_{$activeAcadYear}.xlsx";

        return Excel::download(new ProfessorScheduleExport($prof_id, $activeAcadYear, $activeTerm), $filename);
    }
}
