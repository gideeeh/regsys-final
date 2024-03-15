<?php

namespace App\Http\Controllers;

use App\Models\Dept_Head;
use Illuminate\Http\Request;

class DeptHeadsController extends Controller
{
    public function store(Request $request)
    {
        $deptHead = new Dept_Head();
        $deptHead->first_name = $request->first_name;
        $deptHead->middle_name = $request->middle_name;
        $deptHead->last_name = $request->last_name;
        $deptHead->suffix = $request->suffix;
        $deptHead->dept_id = $request->department;
        $deptHead->personal_email = $request->personal_email;
        $deptHead->school_email = $request->school_email;
        $deptHead->save();
    
        return redirect()->back()->with('success', 'Department Head record created successfully!');
    }

    public function update(Request $request, $id)
    {
        $deptHead = Dept_Head::find($id);
        if($deptHead)
        {
            $deptHead->first_name = $request->first_name;
            $deptHead->middle_name = $request->middle_name;
            $deptHead->last_name = $request->last_name;
            $deptHead->suffix = $request->suffix;
            $deptHead->dept_id = $request->department;
            $deptHead->personal_email = $request->personal_email;
            $deptHead->school_email = $request->school_email;
            $deptHead->save();

            return redirect()->back()->with('success', 'Department Head record successfully updated!');
        }
        else {
            return redirect()->back()->with('error', 'There seems to be an error in updating the record.');
        }
    }

    public function destroy($id)
    {
        $deptHead = Dept_Head::find($id);
        if ($deptHead) {
            $deptHead->delete();
            return redirect()->back()->with('success', 'Department Head record successfully deleted!');
        } else {
            return redirect()->back()->with('error', 'Department Head record not found!');
        }
    }
}
