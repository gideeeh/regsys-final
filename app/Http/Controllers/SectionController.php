<?php

namespace App\Http\Controllers;

use App\Models\Academic_Year;
use App\Models\Program;
use App\Models\Section;
use App\Models\SectionSubject;
use App\Services\AcademicYearService;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    protected $academicYearService;

    public function __construct(AcademicYearService $academicYearService)
    {
        $this->academicYearService = $academicYearService;
    }
    public function index()
    {
        $acad_years = Academic_Year::all();
        $activeAcadYearAndTerm  = $this->academicYearService->determineActiveAcademicYearAndTerm();
        if (!$activeAcadYearAndTerm ) {
            return redirect()->back()->with('error', 'No active academic year or term found.');
        }
        $activeAcadYear = $activeAcadYearAndTerm['activeAcadYear'];
        $activeTerm = $activeAcadYearAndTerm['activeTerm'];
        session([
            'active_academic_year' => $activeAcadYear->id,
            'active_term' => $activeTerm,
        ]);

        $uniqueSections = Section::query()
            ->distinct('section_name')
            ->get(['section_id','section_name']);

        $initial_sections = Section::query()
            ->where('term',$activeTerm)
            ->where('academic_year','2023-2024')
            ->get();

        $programs = Program::all();
        return view('admin.sections', [
            'programs' => $programs,
            'acad_years' => $acad_years,
            'activeAcadYear' => $activeAcadYear,
            'activeTerm' => $activeTerm,
            'uniqueSections' => $uniqueSections,
            'initial_sections' => $initial_sections,
        ]);
    }

    // public function create_section()
    // {
    //     $acad_years = Academic_Year::all();
    //     $programs = Program::all();
    //     $activeAcadYear = $this->academicYearService->determineActiveAcademicYear();
    //     if (!$activeAcadYear) {
    //         return redirect()->back()->with('error', 'No active academic year found.');
    //     }
    //     session(['active_academic_year' => $activeAcadYear->id]);
    //     return view('admin.create-section', [
    //         'programs' => $programs,
    //         'activeAcadYear' => $activeAcadYear,
    //         'acad_years' => $acad_years,
    //     ]);
    // }

    public function store(Request $request)
    {
        $section = Section::firstOrCreate([
            'section_name' => $request->create_sec_section_name,
            'academic_year' => $request->create_sec_acad_year,
            'term' => $request->create_sec_term,
            'year_level' => $request->create_sec_year_level,
            'program_id' => $request->create_sec_program,
        ]);

        if ($section->wasRecentlyCreated) {
            return redirect()->back()->with('success', 'Section successfully created.');
        } else {
            return redirect()->back()->with('error', 'Section already exists.');
        }
    }

    public function fetchSections(Request $request)
    {
        $query = Section::query();
        
        // Filter based on the provided input, ensuring to validate and sanitize input as necessary
        if ($request->has('acad_year') && $request->acad_year != 'all') {
            $query->where('academic_year', $request->acad_year);
        }
        if ($request->has('term') && $request->term != 'all') {
            $query->where('term', $request->term);
        }
        if ($request->has('program') && $request->program != 'all') {
            $query->where('program_id', $request->program);
        }
        if ($request->has('year_level') && $request->year_level != 'all') {
            $query->where('year_level', $request->year_level);
        }

        $sections = $query->get(['section_id', 'section_name', 'year_level']);

        if ($sections->isEmpty()) {
            return response()->json([
                'error' => 'No sections found for the specified criteria.',
                'sections' => []
            ], 404); 
        }

        return response()->json(['sections' => $sections]);
    }

    public function searchSection(Request $request)
    {

    }
}
