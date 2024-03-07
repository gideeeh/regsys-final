<?php

namespace App\Http\Controllers;

use App\Models\StudentNote;
use Illuminate\Http\Request;

class StudentNoteController extends Controller
{
    public function store(Request $request, $studentId)
    {
        $request->validate([
            'note' => 'required',
        ]);
    
        $note = new StudentNote();
        $note->student_id = $studentId;
        $note->note_title = $request->note_title;
        $note->note = $request->note;
        $note->save();
    
        return back()->with('success', 'Note added successfully.');
    }

    public function show($student_id, $note_id)
    {
        $note = StudentNote::where('student_id',$student_id)->findOrFail($note_id);

        return view('admin.indiv-student-record', compact('note'));
    }

    public function destroy($student_id, $note_id)
    {
        $note = StudentNote::where('student_id', $student_id)->findOrFail($note_id);
        $note->delete();

        return back()->with('success', 'Note deleted successfully.');
    }
}
