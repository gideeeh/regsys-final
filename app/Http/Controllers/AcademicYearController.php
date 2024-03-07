<?php

namespace App\Http\Controllers;

use App\Models\Academic_Year;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    public function index()
    {
        $acad_years = Academic_Year::all()->map(function ($year) {
            $today = \Carbon\Carbon::now();
            $year->isActive = (
                $today->between(new \Carbon\Carbon($year->term_1_start), new \Carbon\Carbon($year->term_1_end)) ||
                $today->between(new \Carbon\Carbon($year->term_2_start), new \Carbon\Carbon($year->term_2_end)) ||
                ($year->term_3_start && $year->term_3_end && $today->between(new \Carbon\Carbon($year->term_3_start), new \Carbon\Carbon($year->term_3_end)))
            );
            return $year;
        });
        return view('admin.academic-year', [
            'acad_years' => $acad_years,
        ]);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'acad_year' => 'required|unique:academic_years,acad_year',
            'term_1_start' => 'required',
            'term_1_end' => 'required',
            'term_2_start' => 'required',
            'term_2_end' => 'required',
            'term_3_start' => 'required',
            'term_3_end' => 'required',
        ]);

        $acad_year = new Academic_Year($validated);
        $acad_year->acad_year = $request->acad_year;
        $acad_year->term_1_start = $request->term_1_start;
        $acad_year->term_1_end = $request->term_1_end;
        $acad_year->term_2_start = $request->term_2_start;
        $acad_year->term_2_end = $request->term_2_end;
        $acad_year->term_3_start = $request->term_3_start;
        $acad_year->term_3_end = $request->term_3_end;
        $acad_year->save();
        return redirect()->back()->with('success', 'Academic year has been set!');
    }

    public function update(Request $request, $id)
    {
        $acad_year = Academic_Year::find($id);
        if($acad_year) {
            $acad_year->term_1_start = $request->term_1_start;
            $acad_year->term_2_start = $request->term_2_start;
            $acad_year->term_3_start = $request->term_3_start;
            $acad_year->term_1_end = $request->term_1_end;
            $acad_year->term_2_end = $request->term_2_end;
            $acad_year->term_3_end = $request->term_3_end;
            $acad_year->save();
            return redirect()->back()->with('success', 'Academic Year Record has been updated.');
        }
        else {
            return redirect()->back()->with('error', 'Academic Year Record not found!.');
        }
    }

    public function destroy($id)
    {
        $acad_year = Academic_Year::find($id);
        if ($acad_year) {
            $acad_year->delete();
            return redirect()->back()->with('success', 'Academic Year Record has been deleted.');
        }
        else {
            return redirect()->back()->with('error', 'Academic Year Record not found!.');
        }
    }
}
