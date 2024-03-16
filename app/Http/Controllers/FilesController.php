<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FilesController extends Controller
{
    public function uploadFile(Request $request, $studentId)
    {
        $user = Auth::user();
        $student = Student::findOrFail($studentId);
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $file = $request->file('file');
            $filename = $file->getClientOriginalName();
            $path = $file->storeAs($student->file_path, $filename); 
            
            $new_file =  new File();
            $new_file->student_id = $studentId;
            $new_file->file_name = $filename;
            $new_file->file_extension = $file->getClientOriginalExtension();
            $new_file->uploaded_by = $user->id;
            $new_file->uploaded_at = now();
            $new_file->save();

            // File::create([
            //     'student_id' => $studentId,
            //     'file_name' => $filename,
            //     'file_extension' => $file->getClientOriginalExtension(),
            //     'uploaded_by' => auth()->id(), 
            //     'uploaded_at' => now(), 
            // ]);
    
            return back()->with('success', 'File uploaded successfully.');
        }
    
        return back()->with('error', 'There was a problem uploading the file.');
    }

    public function download($studentId, $filename)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'You are not authorized to perform this action.');
        }
    
        $student = Student::findOrFail($studentId);
        $filePath = $student->file_path . '/' . $filename;
    
        if (Storage::exists($filePath)) {
            return Storage::download($filePath, $filename);
        } else {
            abort(404, 'File not found.');
        }
    }

    public function destroy($id)
    {
        $file = File::find($id);
        if ($file) {
            $file->delete();
            return redirect()->back()->with('success', 'File deleted!');
        } else {
            return redirect()->back()->with('error', 'File not found!');
        }
    }
}
