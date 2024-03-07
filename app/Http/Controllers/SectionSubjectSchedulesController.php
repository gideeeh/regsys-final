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
}
