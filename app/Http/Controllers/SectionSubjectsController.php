<?php

namespace App\Http\Controllers;

use App\Models\SectionSubject;
use App\Models\SectionSubjectSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SectionSubjectsController extends Controller
{
    public function store(Request $request) {
        $section_subjects = SectionSubject::firstOrCreate([
            'section_id' => $request->section_id,
            'subject_id' => $request->subject_id,
        ]);
    
        if ($section_subjects->wasRecentlyCreated) {
            $class_days_online = is_array($request->online_days) ? json_encode($request->online_days) : $request->online_days;
            $class_days_f2f = is_array($request->f2f_days) ? json_encode($request->f2f_days) : $request->f2f_days;
    
            $section_subject_schedules = SectionSubjectSchedule::firstOrCreate([
                'sec_sub_id' => $section_subjects->id,
                'prof_id' => $request->prof_id,
                'class_days_f2f' => $class_days_f2f,
                'class_days_online' => $class_days_online,
                'start_time_f2f' => $request->start_time_f2f, 
                'end_time_f2f' => $request->end_time_f2f,
                'start_time_online' => $request->start_time_online,
                'end_time_online' => $request->end_time_online,
                'room' => $request->room,
                'class_limit' => $request->class_limit,
            ]);
    
            if ($section_subject_schedules->wasRecentlyCreated) {
                return redirect()->back()->with('success', 'Section successfully created.');
            } else {
                return redirect()->back()->with('error', 'Section already exists.');
            }
        } else {
            return redirect()->back()->with('error', 'Section already exists.');
        }
    }
    

    public function search(Request $request)
    {
        $query = DB::table('section_subjects as ss')
            ->join('sections as s', 'ss.section_id', '=', 's.section_id')
            ->join('section_subject_schedules as sss', 'ss.id', '=', 'sss.sec_sub_id')
            ->select(
                's.section_id',
                'ss.id as sec_sub_id',
                's.section_name', 
                's.academic_year', 
                's.term', 
                'ss.subject_id', 
                's.year_level', 
                'sss.class_days_f2f',
                'sss.class_days_online',
                'sss.start_time_f2f',
                'sss.end_time_f2f',
                'sss.start_time_online',
                'sss.end_time_online',
            );
    
        if ($request->filled('acad_year')) {
            $query->where('s.academic_year', $request->input('acad_year'));
        }
        if ($request->filled('term')) {
            $query->where('s.term', $request->input('term'));
        }
        if ($request->filled('year_level')) {
            $query->where('s.year_level', $request->input('year_level'));
        }
        if ($request->filled('subject_id')) {
            $query->where('ss.subject_id', $request->input('subject_id'));
        }
    
        $result = $query->get();
    
        return response()->json($result);
    }
    
}
