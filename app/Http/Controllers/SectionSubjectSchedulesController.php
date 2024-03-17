<?php

namespace App\Http\Controllers;

use App\Models\SectionSubject;
use App\Models\SectionSubjectSchedule;
use Illuminate\Http\Request;

class SectionSubjectSchedulesController extends Controller
{
    public function sec_sub_schedule_json()
    {
        $sec_sub_schedules = SectionSubjectSchedule::all();
        return response()->json($sec_sub_schedules);
    }

    public function fetchScheduleDetailsForSectionAndSubject(Request $request)
    {
        $secSubId = SectionSubject::where('section_id', $request->section_id)
                                ->where('subject_id', $request->subject_id)
                                ->first()
                                ->id;

        // $scheduleDetails = SectionSubjectSchedule::where('sec_sub_id', $secSubId)
        //                                         ->get(); 

        $scheduleDetails = SectionSubjectSchedule::with(['professor'])
            ->where('sec_sub_id', $secSubId)
            ->get()
            ->map(function ($schedule) {
                $profName = optional($schedule->professor)->first_name . ' ' . optional($schedule->professor)->last_name;
                $schedule->professor_name = $profName;
                return $schedule;
        });
        return response()->json($scheduleDetails);
    }

    public function store_schedule_free_section(Request $request)
    {

        $class_days_online = is_array($request->online_days) ? json_encode($request->online_days) : $request->online_days;
        $class_days_f2f = is_array($request->f2f_days) ? json_encode($request->f2f_days) : $request->f2f_days;

        $section_subject_schedules = SectionSubjectSchedule::firstOrCreate([
            'sec_sub_id' => $request->sec_sub_id,
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

        return redirect()->back()->with('success', 'Section successfully created.');
    }
}
