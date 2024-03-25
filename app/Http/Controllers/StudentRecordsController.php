<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\File;
use App\Models\Program;
use App\Models\Student;
use App\Models\StudentNote;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use PHPUnit\Framework\MockObject\Builder\Stub;
use Illuminate\Support\Facades\Storage;

class StudentRecordsController extends Controller
{
    public function index(Request $request)
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
                DB::raw('MAX(e.year_level) as year_level')
            )
            ->groupBy('s.student_id', 's.student_number', 's.first_name', 's.middle_name', 's.last_name', 's.suffix', 'p.program_code');

        $searchTerm = $request->query('query');
        if ($searchTerm) {
            $studentsQuery->where(function ($query) use ($searchTerm) {
                $query->where('s.first_name', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('s.last_name', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('s.student_number', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('p.program_code', 'LIKE', "%{$searchTerm}%");
            });
        }

        $studentRecords = $studentsQuery->paginate(10)->withQueryString();

        return view('admin.student-records', [
            'students' => $studentRecords,
            'searchTerm' => $searchTerm,
        ]);
    }

    public function edit($student_id)
    {
        $student = Student::findOrFail($student_id);
        $years = [];
        $currentYear = date('Y');
        for ($year = $currentYear; $year >= $currentYear - 40; $year--) {
            $years[$year] = $year;
        }
                return view('admin.edit-student', [
            'student' => $student,
            'years' => $years,
        ]);
    }

    public function update_personal(Request $request, $studentId)
    {
        $student = Student::findOrFail($studentId);
    
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'suffix' => 'nullable|string|max:10',
            'student_number' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:255',
            'personal_email' => 'nullable|email|max:255',
            'school_email' => 'nullable|email|max:255',
            'birthdate' => 'nullable|date',
            'birthplace' => 'nullable|string|max:255',
            'nationality' => 'nullable|string|max:255',
            'civil_status' => 'nullable|string|in:Single,Married,Widowed,Divorced,Separated',
            'religion' => 'nullable|string|max:255',
            'house_num' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'brgy' => 'nullable|string|max:255',
            'city_municipality' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'zipcode' => 'nullable|string|max:255',
            'elementary' => 'nullable|string|max:255',
            'elem_yr_grad' => 'nullable|integer|min:1900|max:' . date('Y'),
            'highschool' => 'nullable|string|max:255',
            'hs_yr_grad' => 'nullable|integer|min:1900|max:' . date('Y'),
            'college' => 'nullable|string|max:255',
            'college_year_ended' => 'nullable|integer|min:1900|max:' . date('Y'),
            'guardian_name' => 'nullable|string|max:255',
            'guardian_contact' => 'nullable|string|max:255',
            'is_transferee' => 'sometimes|boolean',
            'is_irregular' => 'sometimes|boolean',
        ]);
    
        $student->update($validatedData);
    
        return redirect()->route('student-records.show', ['student' => $student->student_id])->with('success', 'Student information updated successfully.');
    }
    
    public function show($student_id)
    {

        if (!in_array(auth()->user()->role, ['admin', 'dean'])) {
            abort(403, 'You are not authorized to perform this action.');
        }

        $latestEnrollment = Student::with(['latestEnrollment', 'latestEnrollment.program', 'notes'])->findOrFail($student_id);
        $student = Student::findOrFail($student_id);
        // $latestYearLevel = $student->enrollments()->orderByDesc('year_level')->first()->year_level ?? null;

    
        $notes = StudentNote::where('student_id', $student_id)->get();

        $files = DB::table('files')->where('student_id', $student_id)->get();

        $enrollments = Enrollment::with(['program', 'enrolledSubjects'])
            ->where('student_id', $student_id)
            ->get();     
            
        $organizedEnrollments = [];
        foreach ($student->enrollments as $enrollment) {
            $programName = $enrollment->program->program_name;
            $programId = $enrollment->program->program_id;
            $yearLevel = $enrollment->year_level;
            $term = $enrollment->term;
        
            if (!isset($organizedEnrollments[$programName])) {
                $organizedEnrollments[$programName] = [
                    'programId' => $programId, // Store programId here
                    'years' => [],
                ];
            }
        
            if (!isset($organizedEnrollments[$programName]['years'][$yearLevel])) {
                $organizedEnrollments[$programName]['years'][$yearLevel] = [];
            }
            if (!isset($organizedEnrollments[$programName]['years'][$yearLevel][$term])) {
                $organizedEnrollments[$programName]['years'][$yearLevel][$term] = [];
            }
        
            foreach ($enrollment->enrolledSubjects as $subject) {
                $organizedEnrollments[$programName]['years'][$yearLevel][$term][] = $subject;
            }
        
            usort($organizedEnrollments[$programName]['years'][$yearLevel][$term], function ($a, $b) {
                return strcmp($a->subject->subject_code, $b->subject->subject_code);
            });
        }

        foreach ($organizedEnrollments as $programName => &$programData) {
            ksort($programData['years']); 
    
            foreach ($programData['years'] as $yearLevel => &$terms) {
                ksort($terms); 
            }
        }

        return view('admin.indiv-student-record', [
            'student' => $student,
            'enrollments' => $enrollments,
            'latestEnrollment' => $latestEnrollment,
            'organizedEnrollments' => $organizedEnrollments,
            'notes' => $notes,
            'files' => $files,
        ]);
    }

    public function student_json(Request $request)
    {
        $search = $request->get('q');

        $students = Student::where('first_name','like','%'.$search.'%')
                            ->orWhere('last_name','like','%'.$search.'%')
                            ->orWhere('student_number','like','%'.$search.'%')
                            ->get([
                                'student_id as student_id',
                                'first_name as first_name',
                                'middle_name as middle_name',
                                'last_name as last_name',
                                'suffix as suffix',
                                'student_number as student_number',
                                'personal_email as personal_email',
                                'phone_number as phone_number',
                            ]);
        return response()->json($students);    
    }

    public function fetch_student_json($student_id)
    {
        $student = Student::findOrFail($student_id);
        return response()->json([
            'student_id' => $student->student_id,
            'student_number' => $student->student_number,
            'personal_email' => $student->personal_email,
            'phone_number' => $student->phone_number,
            'first_name' => $student->first_name,
            'middle_name' => $student->middle_name,
            'last_name' => $student->last_name,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
           'student_number' => 'required|unique:students,student_number',
           'first_name' => 'required',
           'last_name' => 'required',
           'personal_email' => 'required|unique:students,personal_email',
        ]);


        $isTransferee = $request->has('is_transferee') ? true : false;
        $isIrregular = $request->has('is_irregular') ? true : false;

        $new_student = new Student($validated);
        $new_student->student_number = $request->student_number;
        $new_student->first_name = $request->first_name;
        $new_student->middle_name = $request->middle_name;
        $new_student->last_name = $request->last_name;
        $new_student->suffix = $request->suffix;
        $new_student->sex = $request->sex;
        $new_student->birthdate = $request->birthdate;
        $new_student->birthplace = $request->birthplace;
        $new_student->civil_status = $request->civil_status;
        $new_student->nationality = $request->nationality;
        $new_student->religion = $request->religion;
        $new_student->phone_number = $request->phone_number;
        $new_student->personal_email = $request->personal_email;
        $new_student->school_email = $request->school_email;
        $new_student->house_num = $request->house_num;
        $new_student->street = $request->street;
        $new_student->brgy = $request->brgy;
        $new_student->city_municipality = $request->city_municipality;
        $new_student->province = $request->province;
        $new_student->zipcode = $request->zipcode;
        $new_student->guardian_name = $request->guardian_name;
        $new_student->guardian_contact = $request->guardian_contact;
        $new_student->elementary = $request->elementary;
        $new_student->elem_yr_grad = $request->elem_yr_grad;
        $new_student->jr_highschool = $request->jr_highschool;
        $new_student->jr_hs_yr_grad = $request->jr_hs_yr_grad;
        $new_student->sr_highschool = $request->sr_highschool;
        $new_student->sr_hs_yr_grad = $request->sr_hs_yr_grad;
        $new_student->college = $request->college;
        $new_student->college_year_ended = $request->college_year_ended;
        $new_student->is_transferee = $isTransferee;
        $new_student->is_irregular = $isIrregular;
        
        $studentDirectory = 'students/' . $new_student->student_number . ' '. $new_student->last_name. ', ' .$new_student->first_name;
        Storage::makeDirectory($studentDirectory);
        
        $new_student->file_path = $studentDirectory;

        $new_student->save();

        $user = User::create([
            'email' => $validated['personal_email'],
            'password' => Hash::make('55Changemenow99'),
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'role' => 'user',
        ]);

        $new_student->update(['user_id' => $user->id]);

        return redirect()->back()->with('success', 'Student added successfully!');
    }

    public function destroy($student_id)
    {
        $student = Student::find($student_id);
        if ($student) {
            $student->delete();
            return redirect()->back()->with('success', 'Record deleted!');
        } else {
            return redirect()->back()->with('error', 'Record not found!');
        }
    }
}