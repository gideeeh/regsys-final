<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubjectCatalogController extends Controller
{
    public function index(Request $request)
    {
        // Corrected typo: changed 'sj' to 's' in join conditions
        $subjectsQuery = DB::table('subjects as s')
            ->leftJoin('subjects as pr1', 's.prerequisite_1', '=', 'pr1.subject_id')
            ->leftJoin('subjects as pr2', 's.prerequisite_2', '=', 'pr2.subject_id') 
            ->leftJoin('subjects as cr', 's.co_requisite', '=', 'cr.subject_id') 
            ->select(
                's.subject_id', 
                's.subject_code',
                's.subject_name',
                's.subject_description',
                's.units_lec',
                's.units_lab',
                'pr1.subject_name as prereq1',
                'pr2.subject_name as prereq2',
                'cr.subject_name as co_req',
            ); 

        // Search
        $searchTerm = $request->query('query');
        if ($searchTerm) {
            $subjectsQuery->where(function ($query) use ($searchTerm) {
                $query->where('s.subject_code', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('s.subject_name', 'LIKE', "%{$searchTerm}%");
            });
        }

        $subjects = $subjectsQuery->paginate(10)->withQueryString();
        $all_subjects = Subject::orderBy('subject_name')->get();

        return view('admin.subject-catalog', [
            'subjects' => $subjects,
            'all_subjects' => $all_subjects,
            'searchTerm' => $searchTerm,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_code' => 'required|unique:subjects,subject_code',
            'subject_name' => 'required|unique:subjects,subject_name',
            'units_lec' => 'required|numeric',
            'units_lab' => 'required|numeric',
        ]);

        $subject = new Subject($validated);
        $subject->subject_code = $request->subject_code;
        $subject->subject_name = $request->subject_name;
        $subject->subject_description = $request->subject_description;
        $subject->units_lec = $request->units_lec;
        $subject->units_lab = $request->units_lab;
        $subject->prerequisite_1 = $request->prereq1;
        $subject->prerequisite_2 = $request->prereq2;
        $subject->co_requisite = $request->co_req;
        $subject->save();
    
        return redirect()->back()->with('success', 'Program added successfully!');
    }

    public function delete($id)
    {
        $subject = Subject::find($id);
        if($subject)
        {
            $subject->delete();
            return redirect()->back()->with('success','Subject has been successfully deleted.');
        }
        else {
            return redirect()->back()->with('error','Subject not found');
        }
    }

    public function update(Request $request, $id)
    {
        $subject = Subject::find($id);
        if($subject)
        {
            $subject->subject_code = $request->subject_code;
            $subject->subject_name = $request->subject_name;
            $subject->subject_description = $request->subject_description;
            $subject->units_lec = $request->units_lec;
            $subject->units_lab = $request->units_lab;
            $subject->prerequisite_1 = $request->prereqUpdate1;
            $subject->prerequisite_2 = $request->prereqUpdate2;
            $subject->co_requisite = $request->co_req2;
            $subject->save();

            return redirect()->back()->with('success', 'Subject updated successfully!');
        }
        else {
            return redirect()->back()->with('error', 'Subject not found.');
        }
    }

    public function fetch_subject($subject_id)
    {
        $subject = Subject::findOrFail($subject_id);
        return response()->json([
            'subject' => $subject->subject_id,
            'subject_name' => $subject->subject_name,
            'subject_code' => $subject->subject_code,
            'subject_description' => $subject->subject_description,
            'units_lec' => $subject->units_lec,
            'units_lab' => $subject->units_lab,
            'prerequisite_1' => $subject->prerequisite_1,
            'prerequisite_2' => $subject->prerequisite_2,
            'prerequisite_3' => $subject->prerequisite_3,
        ]);
    }
}
