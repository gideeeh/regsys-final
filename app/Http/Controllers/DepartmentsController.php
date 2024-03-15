<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepartmentsController extends Controller
{
    public function index()
    {
        $departmentsWithHeads = DB::table('departments as d')
            ->leftJoin('dept_heads as dh', 'd.dept_id', '=', 'dh.dept_id')
            ->select(
                'd.dept_id',
                'd.dept_name',
                'dh.dept_head_id',
                'dh.first_name',
                'dh.middle_name',
                'dh.last_name',
                'dh.suffix',
                'dh.personal_email',
                'dh.school_email'
            )
            ->get();

        $departments = Department::with('deptHead')->get(['dept_id', 'dept_name']);

        $departmentRecords = Department::with('deptHeads')->paginate(10);
        return view('admin.departments', [
            'departments' => $departments,
            'departmentRecords' => $departmentRecords,
            'departmentsWithHeads' => $departmentsWithHeads,
        ]);
    }

    public function store(Request $request)
    {
        $department = new Department();
        $department->dept_name = $request->dept_name;
        $department->save();
    
        return redirect()->back()->with('success', 'Department record successfully created!');
    }

    public function update(Request $request, $id)
    {
        $department = Department::find($id);
        if($department)
        {
            $department->dept_name = $request->dept_name;
            $department->save();

            return redirect()->back()->with('success', 'Department record successfully updated!');
        }
        else {
            return redirect()->back()->with('error', 'There seems to be an error in updating the record.');
        }
    }

    public function destroy($id)
    {
        $department = Department::find($id);
        if ($department) {
            $department->delete();
            return redirect()->back()->with('success', 'Department record successfully deleted!');
        } else {
            return redirect()->back()->with('error', 'Department record not found!');
        }
    }
}
