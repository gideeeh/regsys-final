<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Program;
use App\Models\Program_Subject;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProgramController extends Controller
{
    public function store(Request $request)
    {
        $program = new Program();
        $program->program_code = $request->program_code;
        $program->program_name = $request->program_name;
        $program->program_major = $request->program_major;
        $program->program_desc = $request->program_description;
        $program->degree_type = $request->degree_type;
        $program->dept_id = $request->department;
        $program->program_coordinator = $request->program_coordinator;
        $program->total_units = $request->total_units;
        $program->save();
    
        return redirect()->back()->with('success', 'Program added successfully!');
    }

    public function index()
    {
        $programs = DB::table('programs as p')
            ->leftJoin('program_subjects as ps', 'p.program_id', '=', 'ps.program_id')
            ->leftJoin('subjects as s', 'ps.subject_id', '=', 's.subject_id')
            ->leftJoin('departments as d', 'p.dept_id', '=', 'd.dept_id')
            ->select(
                'p.program_id',
                'p.program_code',
                'p.program_desc',
                'p.program_major',
                'p.program_name',
                'p.degree_type',
                'd.dept_name',
                'd.dept_id',
                'p.program_coordinator',
                DB::raw('SUM(s.units_lec) + SUM(s.units_lab) AS total_units')
            )
            ->groupBy('p.program_id', 'p.program_code', 'p.program_major', 'p.program_desc', 'd.dept_id', 'p.program_name', 'p.degree_type', 'd.dept_name', 'p.program_coordinator')
            ->get();

        $programsPaginate = DB::table('programs as p')
            ->leftJoin('program_subjects as ps', 'p.program_id', '=', 'ps.program_id')
            ->leftJoin('subjects as s', 'ps.subject_id', '=', 's.subject_id')
            ->leftJoin('departments as d', 'p.dept_id', '=', 'd.dept_id')
            ->select(
                'p.program_id',
                'p.program_code',
                'p.program_desc',
                'p.program_major',
                'p.program_name',
                'p.degree_type',
                'd.dept_name',
                'd.dept_id',
                'p.program_coordinator',
                DB::raw('SUM(s.units_lec) + SUM(s.units_lab) AS total_units')
            )
            ->groupBy('p.program_id', 'p.program_code', 'p.program_major', 'p.program_desc', 'd.dept_id', 'p.program_name', 'p.degree_type', 'd.dept_name', 'p.program_coordinator');

        $programRecords = $programsPaginate->paginate(10)->withQueryString();

        $departments = Department::all();

        // return view('admin.program-list', [
        //     'departments' => $departments,
        //     'programs' => $programs
        // ]);
        // $programs = Program::all();
        return view('admin.program-list', [
            'programs' => $programRecords,
            'departments' => $departments,
        ]);
    }
    
    public function destroy($program_id)
    {
        $program = Program::find($program_id);
        if ($program) {
            $program->delete();
            return redirect()->back()->with('success', 'Program deleted successfully!');
        } else {
            return redirect()->back()->with('error', 'Program not found!');
        }
    }

    public function update(Request $request, $id)
    {
        $program = Program::find($id);
        if ($program) {
            $program->program_code = $request->program_code;
            $program->program_name = $request->program_name;
            $program->program_major = $request->program_major;
            $program->program_desc = $request->program_desc;
            $program->degree_type = $request->degree_type;
            $program->dept_id = $request->department;
            $program->program_coordinator = $request->program_coordinator;
            $program->save();
    
            return redirect()->back()->with('success', 'Program updated successfully!');
        } else {
            return redirect()->back()->with('error', 'Program not found!');
        }
    }

    public function show($program_id)
    {
        $program = Program::findOrFail($program_id);
        $subjects = Subject::all();
        
        $totalsByYearTerm = Program_Subject::where('program_id', $program_id)
        ->join('subjects', 'program_subjects.subject_id', '=', 'subjects.subject_id')
        ->groupBy(['year', 'term'])
        ->get([
            'year',
            'term',
            DB::raw('SUM(subjects.units_lec) as totalLec'),
            DB::raw('SUM(subjects.units_lab) as totalLab')
        ])
        ->reduce(function ($carry, $item) {
            $carry[$item->year][$item->term] = [
                'lec' => $item->totalLec,
                'lab' => $item->totalLab,
                'total' => $item->totalLec + $item->totalLab,
            ];
            return $carry;
        }, []);
        
        $program_subjects = Program_Subject::with('subject')
                        ->join('subjects as s', 'program_subjects.subject_id', '=', 's.subject_id')
                        ->leftJoin('subjects as pr1', 's.prerequisite_1', '=', 'pr1.subject_id')
                        ->leftJoin('subjects as pr2', 's.prerequisite_2', '=', 'pr2.subject_id')
                        ->where('program_subjects.program_id', $program_id)
                        ->select([
                            'program_subjects.*', 
                            'pr1.subject_name as prerequisite_1',
                            'pr2.subject_name as prerequisite_2', 
                        ])
                        ->get()
                        ->groupBy(['year', 'term']);


        $selectedSubjectsIds = Program_Subject::where('program_id', $program_id)
        ->pluck('subject_id')->toArray();

        $total_units = Program_Subject::where('program_id', $program_id)
                    ->join('subjects', 'program_subjects.subject_id', '=', 'subjects.subject_id')
                    ->sum(DB::raw('subjects.units_lec + subjects.units_lab'));
        
        return view('admin.program-profile', [
            'program' => $program,
            'subjects' => $subjects,
            'program_subjects' => $program_subjects,
            'program_id' => $program_id,
            'selectedSubjects' => $selectedSubjectsIds,
            'total_units' => $total_units,
            'totalsByYearTerm' => $totalsByYearTerm,
        ]);
    }

    public function program_json() {
        $programs = Program::all();

        return response()->json([
            'success' => true,
            'data' => $programs,
        ]);
    }

    public function fetch_program_json($program_id) {
        $program = Program::findOrFail($program_id);

        return response()->json([
            'success' => true,
            'data' =>$program,
        ]);
    }
}
