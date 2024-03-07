<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index() {
        $subjects = Subject::with(['prerequisite1', 'prerequisite2', 'prerequisite3'])
            ->get();
            
        return view('subjects', ['subjects' => $subjects]);
    }

    public function search(Request $request)
    {
        $search = $request->get('q');

        $subjects = Subject::where('subject_name','like','%'.$search.'%')
                            ->orWhere('subject_code','like','%'.$search.'%')
                            ->get([
                                'subject_id as subject_id',
                                'subject_name as subject_name',
                                'subject_code as subject_code',
                                'subject_description as subject_description',
                                'units_lec as units_lec',
                                'units_lab as units_lab',
                                'prerequisite_1 as prerequisite_1',
                                'prerequisite_2 as prerequisite_2',
                                'prerequisite_3 as prerequisite_3',
                            ]);
        
        return response()->json($subjects);
    }
}
