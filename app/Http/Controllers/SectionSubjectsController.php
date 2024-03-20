<?php

namespace App\Http\Controllers;

use App\Models\SectionSubject;
use App\Models\SectionSubjectSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SectionSubjectsController extends Controller
{

    // Store free subejct here, block subjects are stored in SectionSubjectController
    public function store_free(Request $request)
    {
        $section_subjects = SectionSubject::firstOrCreate([
            'section_id' => $request->section_id,
            'subject_id' => $request->subject_to_add,
        ]);

        if ($section_subjects->wasRecentlyCreated) {
            $message = 'Subject successfully added.';
            return redirect()->back()->with('success', $message);
        } else {
            $message = 'This subject already exists.';
            return redirect()->back()->with('error', $message);
        }

    }


    public function store(Request $request) {
        $section_subject = SectionSubject::updateOrCreate([
            'section_id' => $request->section_id,
            'subject_id' => $request->subject_id,
        ]);

        $scheduleData = [
            'prof_id' => $request->prof_id,
            'class_days_f2f' => is_array($request->f2f_days) ? json_encode($request->f2f_days) : $request->f2f_days,
            'class_days_online' => is_array($request->online_days) ? json_encode($request->online_days) : $request->online_days,
            'start_time_f2f' => $request->start_time_f2f,
            'end_time_f2f' => $request->end_time_f2f,
            'start_time_online' => $request->start_time_online,
            'end_time_online' => $request->end_time_online,
            'room' => $request->room,
            'class_limit' => $request->class_limit,
        ];

        $f2fDays = is_array($request->f2f_days) ? $request->f2f_days : json_decode($request->f2f_days);
        $onlineDays = is_array($request->online_days) ? $request->online_days : json_decode($request->online_days);
    
        // Convert times to a comparable format
        $startTimeF2F = strtotime($request->start_time_f2f);
        $endTimeF2F = strtotime($request->end_time_f2f);
        $startTimeOnline = strtotime($request->start_time_online);
        $endTimeOnline = strtotime($request->end_time_online);
    
        // Query for potential conflicts within the same section oh yeaaa
        $potentialConflicts = SectionSubjectSchedule::whereHas('sectionSubject', function($query) use ($request) {
            $query->where('section_id', $request->section_id);
        })->get();
    
        foreach ($potentialConflicts as $conflict) {
            $conflictDaysF2F = json_decode($conflict->class_days_f2f);
            $conflictDaysOnline = json_decode($conflict->class_days_online);
    
            foreach ($f2fDays as $day) {
                if (in_array($day, $conflictDaysF2F)) {
                    // Check time overlap for F2F
                    if ($startTimeF2F < strtotime($conflict->end_time_f2f) && $endTimeF2F > strtotime($conflict->start_time_f2f)) {
                        return redirect()->back()->with('error', 'Schedule conflict detected for F2F.');
                    }
                }
            }
    
            foreach ($onlineDays as $day) {
                if (in_array($day, $conflictDaysOnline)) {
                    // Check time overlap for Online
                    if ($startTimeOnline < strtotime($conflict->end_time_online) && $endTimeOnline > strtotime($conflict->start_time_online)) {
                        return redirect()->back()->with('error', 'Schedule conflict detected for Online.');
                    }
                }
            }
        }
    
        // Proceed if no conflicts are found
        $section_subject_schedule = SectionSubjectSchedule::updateOrCreate([
            'sec_sub_id' => $section_subject->id,
        ], $scheduleData);
    
        return redirect()->back()->with('success', 'Schedule successfully updated or created.');
    }
    

    public function search(Request $request)
    {
        $query = DB::table('section_subjects as ss')
            ->join('sections as s', 'ss.section_id', '=', 's.section_id')
            ->join('section_types as st', 's.section_type_id', '=', 'st.id')
            ->join('section_subject_schedules as sss', 'ss.id', '=', 'sss.sec_sub_id')
            ->select(
                's.section_id',
                's.section_type_id',
                'st.section_type',
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
