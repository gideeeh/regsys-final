<?php

namespace App\Http\Controllers;

use App\Models\Academic_Year;
use App\Models\Program;
use App\Models\Program_Subject;
use App\Models\Section;
use App\Models\SectionSubject;
use App\Models\SectionSubjectSchedule;
use App\Models\SectionType;
use App\Services\AcademicYearService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SectionController extends Controller
{
    protected $academicYearService;

    public function __construct(AcademicYearService $academicYearService)
    {
        $this->academicYearService = $academicYearService;
    }
    public function index(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
        }

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
        $section_types = SectionType::all();

        $sections = Section::with('section_type')->get();
        $query = Section::query();

        if ($request->filled('filter_acad_year')) {
            $query->where('academic_year', $request->filter_acad_year);
        }
        if ($request->filled('filter_term')) {
            $query->where('term', $request->filter_term);
        }
        if ($request->filled('filter_section_type') && $request->filter_section_type != 'all') {
            $query->where('section_type_id', $request->filter_section_type);
        }
        if ($request->filled('filter_year_level') && $request->filter_year_level != 'all') {
            $query->where('year_level', $request->filter_year_level);
        }        
    
        $sections = $query->paginate(10);
    
        return view('admin.sections', compact('user', 'sections', 'programs', 'acad_years', 'activeAcadYear', 'activeTerm', 'uniqueSections', 'initial_sections', 'section_types'));
    }

    public function store(Request $request)
    {
        $section = Section::firstOrCreate([
            'section_name' => $request->create_sec_section_name,
            'academic_year' => $request->create_sec_acad_year,
            'term' => $request->create_sec_term,
            'year_level' => $request->create_sec_year_level,
            'program_id' => $request->create_sec_program,
            'section_type_id' => $request->create_section_type,
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

        $sections = Section::with(['section_type' => function($query) {
            $query->select(['id', 'section_type']); 
        }])->get(['section_id', 'section_name', 'year_level', 'section_type_id']);

        if ($sections->isEmpty()) {
            return response()->json([
                'error' => 'No sections found for the specified criteria.',
                'sections' => []
            ], 404); 
        }

        return response()->json(['sections' => $sections]);
    }

    public function show($id)
    {
        $section = Section::with('section_type')->findOrFail($id);
        
        $blockSubjects = Program_Subject::join('subjects', 'program_subjects.subject_id', '=', 'subjects.subject_id')
            ->select('program_subjects.*', 'subjects.subject_code', 'subjects.subject_name') // Include subject details
            ->where('year', $section->year_level)
            ->where('term', $section->term)
            ->orderBy('subjects.subject_code')
            ->get()
            ->unique('subject_id'); 
        
        $sectionSubjects = SectionSubject::with(['subjectSectionSchedule','subject','subjectSectionSchedule.professor'])
            ->where('section_id', $section->section_id)
            ->get();
    
        $sectionSubjectsIds = $sectionSubjects->pluck('subject_id')->unique();
    
        $blockSubjects = $blockSubjects->map(function ($blockSubject) use ($sectionSubjectsIds) {
            $isScheduleSet = $sectionSubjectsIds->contains($blockSubject->subject_id);
            $blockSubject->is_schedule_set = $isScheduleSet;
            return $blockSubject;
        })->sortByDesc('is_schedule_set'); 
    
        return view('admin.section-show', compact('section', 'blockSubjects', 'sectionSubjects'));
    }

    public function update_section(Request $request, $section_id)
    {
        $section = Section::findOrFail($section_id);

        if($section) {
            $section->section_name = $request->section_name;
            $section->save();
            return back()->with('success', 'Section successfully updated');
        } else {
            return back()->with('error', 'Error in updating section.');
        }
    }

    public function delete_section($section_id, Request $request)
    {
        
        if (!Auth::user()->role === 'admin') { 
            return back()->with('error', 'Unauthorized');
        }

        // Verify if password is correct
        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return back()->with('error', 'Verification failed');
        }

        $section = Section::find($section_id);
        if ($section) {
            $sectionSubjects = $section->sectionSubject()->get();
    
            foreach ($sectionSubjects as $sectionSubject) {
                $sectionSubject->subjectSectionSchedule()->delete();
                $sectionSubject->enrolledSubject()->delete();
            }
    
            $section->sectionSubject()->delete();
    
            $section->delete();
    
            return redirect()->back()->with('success', 'Section and related records deleted successfully!');
        } else {
            return redirect()->back()->with('error', 'Error in deleting section.');
        }
    }
}
