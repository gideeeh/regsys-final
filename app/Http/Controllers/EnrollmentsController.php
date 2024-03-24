<?php

namespace App\Http\Controllers;

use App\Http\Resources\EnrollmentResource;
use App\Models\Academic_Year;
use App\Models\Enrolled_Subject;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\SectionSubject;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use App\Services\AcademicYearService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EnrollmentsController extends Controller
{
    protected $academicYearService;

    public function __construct(AcademicYearService $academicYearService)
    {
        $this->academicYearService = $academicYearService;
    }
    
    public function index (Request $request)
    {

        $studentsQuery = DB::table('students as s')
            ->leftJoin('enrollments as e', 's.student_id', '=', 'e.student_id')
            ->leftJoin('programs as p', 'e.program_id', '=', 'p.program_id') 
            ->select(
                's.student_id',
                's.student_number',
                's.first_name',
                's.middle_name',
                's.last_name',
                's.suffix',
                'p.program_code',
                'p.program_major',
                DB::raw('MAX(e.year_level) as year_level')
            )
            ->groupBy('s.student_id', 's.student_number', 's.first_name', 's.middle_name', 's.last_name', 's.suffix', 'p.program_code', 'p.program_major');

        $query = Enrollment::query()
            ->select(
                'enrollments.enrollment_id', 
                'enrollments.academic_year', 
                'enrollments.term', 
                'enrollments.year_level',
                'enrollments.enrollment_date',
                'enrollments.enrollment_method',
                'enrollments.scholarship_type',
                'enrollments.status',
                'students.student_number',
                'students.first_name', 
                'students.middle_name', 
                'students.last_name',
                'students.suffix',
                'programs.program_code',
                'programs.program_major',
            )
            ->join('programs', 'programs.program_id', '=', 'enrollments.program_id')
            ->join('students', 'enrollments.student_id', '=', 'students.student_id');
            
        $searchTerm = $request->query('query');
        if($searchTerm)
        {
            $studentsQuery->where(function($query) use ($searchTerm) {
                $query->where('students.first_name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('students.last_name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('students.student_number', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('programs.program_code', 'LIKE', "%{$searchTerm}%");
            });
        }

        // $enrollmentRecords = $query->paginate(10)->withQueryString();
        $studentRecords = $studentsQuery->paginate(10)->withQueryString();


        return view('admin.enrollment-records', [
            // 'enrollments' => $enrollmentRecords,
            'students' => $studentRecords,
            'searchTerm' => $searchTerm
        ]);
    }

    public function show($student_id)
    {
        $student = Student::with([
            'enrollments.program',
            'enrollments.enrolledSubjects.subject',
            'enrollments.enrolledSubjects.sectionSubject.subjectSectionSchedule.professor',
            'enrollments' => function ($query) {
                $query->orderBy('year_level', 'desc')->orderBy('term');
            }
        ])->findOrFail($student_id);

        $user = Auth::user();
    
        // Group enrollments by program_id.
        $groupedEnrollments = $student->enrollments->groupBy('program_id');
    
        return view('admin.enrollment-records-show', compact('groupedEnrollments', 'student', 'user'));
    }
    
    
    

    public function enroll()
    {
        $activeAcadYearAndTerm  = $this->academicYearService->determineActiveAcademicYearAndTerm();
        if (!$activeAcadYearAndTerm) {
            return redirect()->back()->with('error', 'No active academic year found.');
        }
        $activeAcadYear = $activeAcadYearAndTerm['activeAcadYear'];
        $activeTerm = $activeAcadYearAndTerm['activeTerm'];
        session(['active_academic_year' => $activeAcadYear->id]);
        $programs = Program::all();
        $subjects = Subject::all();
        $students = Student::all();
        $acad_years = Academic_Year::all();
        return view('admin.enroll-student',[
            'students' => $students,
            'programs' => $programs,
            'subjects' => $subjects,
            'acad_years' => $acad_years,
            'activeAcadYear' => $activeAcadYear,
            'activeTerm' => $activeTerm,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,student_id',
            'program_id' => 'required|exists:programs,program_id',
            'acad_year' => 'required|string|max:9',
            'term' => 'required|string|max:10',
            'year_level' => 'required|string|max:10',
            'enrollment_date' => 'sometimes|date',
            'scholarship_type' => 'sometimes|string|max:45',
            'status' => 'sometimes|string|max:45',
            'enrollment_method' => 'required|string|max:45',
            'selectedSubjects' => 'required|json',
            'sec_sub_ids' => 'required|array',
            'sec_sub_ids.*' => 'required|exists:section_subjects,id',
        ]);

        $student_number_query = Student::query()
            ->select('student_number')
            ->where('student_id','=',$request->student_id)
            ->first();

        $student_number = json_decode($student_number_query, true)['student_number'];

        $enrollmentCode = $student_number . '_' . $request->acad_year . '_' . $request->term . '_' . $request->program_id;

        $selectedSubjects = json_decode($request->selectedSubjects, true);

        $existingEnrollment = Enrollment::where([
            'student_id' => $validated['student_id'],
            'program_id' => $validated['program_id'],
            'academic_year' => $validated['acad_year'],
            'term' => $validated['term'],
        ])->first();

        if ($existingEnrollment) {
            Log::warning("Duplicate enrollment attempt for student_id {$validated['student_id']} for academic year {$validated['acad_year']}, term {$validated['term']}.");
            return redirect()->back()->with('error', 'An enrollment record already exists for the selected academic year, term, and program.');
        }

        try {
            $enrollment = Enrollment::create([
                'student_id' => $validated['student_id'],
                'program_id' => $validated['program_id'],
                'academic_year' => $validated['acad_year'],
                'term' => $validated['term'],
                'year_level' => $validated['year_level'],
                'enrollment_date' => $validated['enrollment_date'] ?? now(),
                'scholarship_type' => $validated['scholarship_type'] ?? 'none',
                'status' => $validated['status'] ?? 'pending',
                'enrollment_method' => $validated['enrollment_method'],
                'enrollment_code' => $enrollmentCode,
            ]);

            session()->flash('selectedSubjects', $selectedSubjects);
            session()->flash('enrollment_id', $enrollment->enrollment_id);

            return view('admin.enrolled-subject');
        } catch (\Exception $e) {
            Log::error('Enrollment creation failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error in enrolling the student.');
        }
    }

    public function validateSelections(Request $request)
    {
        $selections = $request->input('selections');
        
        foreach ($selections as $selection) {
            $subjectId = $selection['subject_id'];
            $secSubId = $selection['sec_sub_id'];
            
            $secSub = SectionSubject::with('subjectSectionSchedule')->where('id', $secSubId)->first();
    
            if ($secSub && $secSub->subjectSectionSchedule) {
                $secSubCount = $secSub->subjectSectionSchedule->class_limit;
    
                $enrolledSubForThisSectionCount = Enrolled_Subject::where('sec_sub_id', $secSubId)->count();
    
                if ($secSubCount <= $enrolledSubForThisSectionCount) {
                    return response()->json(['message' => 'Section already full', 'sec_sub_id' => $secSubId]);
                }
            } else {
                // Handling cases where the section or its schedule might not be found
                return response()->json(['message' => 'Section or schedule not found', 'sec_sub_id' => $secSubId]);
            }
    
            \Log::info("Processing subject $subjectId with section-subject $secSubId");
        }
    
        return response()->json(['message' => 'Validation successful', 'data' => $selections]);
    }

    public function apiGradingIndex()
    {
        $enrollments = Enrollment::all();
        return EnrollmentResource::collection($enrollments);
        
    }

    public function destroy_enrollment(Request $request, $enrollment_id)
    {
        if (Auth::user()->role !== 'admin') { 
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Verify if password is correct
        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return response()->json(['message' => 'Password verification failed'], 403);
        }

        DB::beginTransaction();
        try {
            Enrolled_Subject::where('enrollment_id', $enrollment_id)->delete();
            $enrollment = Enrollment::find($enrollment_id);
            if ($enrollment) {
                $enrollment->delete();
                DB::commit();
                return back()->with('success', 'Enrollment Record has been deleted.');
            } else {
                DB::rollBack();
                return redirect()->back()->with('error', 'Error in deleting enrollment record!');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error in deleting enrollment record!');
        }
    }

    public function credit_student()
    {
        $students = Student::all();

        return view('admin.credit-subject',[
            'students' => $students,
        ]);
    }

    public function validate_enrollment($enrollment_id)
    {
        $enrollment = Enrollment::findOrFail($enrollment_id);


        $enrollment->date_validation_registrar = now();
        $enrollment->save();
        return back()->with('success', 'Successfully validated enrollment record.');
    }
}
