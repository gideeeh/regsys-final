<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ScrapingController;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403); 
        }

        $chartController = new ChartController();
        $trendData = $chartController->enrollmentTrendsPerTerm();
        $programData = $chartController->enrollmentsByProgram();

        return view('admin.dashboard', [
            'trendData' => $trendData,
            'programData' => $programData,
        ]);
    }

    protected function scrapedNews()
    {
        $scrapingController = new ScrapingController();
        $news = $scrapingController->scrape();

        return response()->json($news);
    }

    public function getActiveClasses()
    {
        $now = Carbon::now();
        $today = $now->format('l'); // Get the current day name, e.g., "Wednesday"
    
        // Fetch all classes without filtering them by day
        $allClasses = DB::table('section_subject_schedules as sss')
            ->join('section_subjects as ss', 'sss.sec_sub_id', '=', 'ss.id')
            ->join('sections as sec', 'ss.section_id', '=', 'sec.section_id')
            ->join('subjects as sub', 'ss.subject_id', '=', 'sub.subject_id')
            ->join('professors as prof', 'sss.prof_id', '=', 'prof.prof_id')
            ->select(
                'sub.subject_code',
                'sub.subject_name',
                'sec.section_name',
                'prof.first_name',
                'prof.last_name',
                'sss.start_time_f2f',
                'sss.end_time_f2f',
                'sss.class_days_f2f',
                'sss.start_time_online',
                'sss.end_time_online',
                'sss.class_days_online'
            )
            ->get();
    
        // Separate and filter F2F and Online classes based on time and day
        $activeF2FClasses = $allClasses->filter(function ($class) use ($now, $today) {
            $classDays = json_decode($class->class_days_f2f);
            return in_array($today, $classDays) && $now->between($class->start_time_f2f, $class->end_time_f2f);
        });
    
        $activeOnlineClasses = $allClasses->filter(function ($class) use ($now, $today) {
            $classDays = json_decode($class->class_days_online);
            return in_array($today, $classDays) && $now->between($class->start_time_online, $class->end_time_online);
        });
    
        // // Format the output for active classes to include the necessary details
        $formattedF2FClasses = $activeF2FClasses->map(function ($class) {
            return [
                'subject_code' => $class->subject_code,
                'subject_name' => $class->subject_name,
                'section_name' => $class->section_name,
                'professor' => substr($class->first_name,0,1) . '. ' . $class->last_name,
                'time' => Carbon::parse($class->start_time_f2f)->format('h:i A') . ' - ' . Carbon::parse($class->end_time_f2f)->format('h:i A'),
            ];
        });
    
        $formattedOnlineClasses = $activeOnlineClasses->map(function ($class) {
            return [
                'subject_code' => $class->subject_code,
                'subject_name' => $class->subject_name,
                'section_name' => $class->section_name,
                'professor' => $class->first_name . ' ' . $class->last_name,
                'time' => Carbon::parse($class->start_time_online)->format('h:i A') . ' - ' . Carbon::parse($class->end_time_online)->format('h:i A'),
            ];
        });
    
        return response()->json([
            'activeF2FClasses' => $formattedF2FClasses,
            'activeOnlineClasses' => $formattedOnlineClasses,
            'dayToday' => $today,
            'now' => $now ,
        ]);
    }
    

    public function calendarEvents()
    {
        $this_day = Carbon::now('Asia/Singapore');
        $startOfDay = (clone $this_day)->startOfDay();
        $endOfDay = (clone $this_day)->endOfDay();
        $startOfWeek = (clone $this_day)->startOfWeek()->format('Y-m-d H:i:s');
        $endOfWeek = (clone $this_day)->format('Y-m-d H:i:s');
        $startOfMonth = (clone $this_day)->startOfMonth()->format('Y-m-d H:i:s');
        $endOfMonth = (clone $this_day)->endOfMonth()->format('Y-m-d H:i:s');

        $eventsToday = DB::table('calendar_events')
            ->where(function ($query) use ($startOfDay, $endOfDay) {
                $query->whereBetween('start_time', [$startOfDay, $endOfDay])
                    ->orWhereBetween('end_time', [$startOfDay, $endOfDay]);
            })
            ->orWhere(function ($query) use ($startOfDay) {
                $query->where('start_time', '<=', $startOfDay)
                    ->where('end_time', '>=', $startOfDay);
            })
            ->get();

        $eventsThisWeek = DB::table('calendar_events')
            ->whereBetween('start_time', [$startOfWeek, $endOfWeek])
            ->orWhereBetween('end_time', [$startOfWeek, $endOfWeek])
            ->get();

        $eventsThisMonth = DB::table('calendar_events')
            ->whereBetween('start_time', [$startOfMonth, $endOfMonth])
            ->orWhereBetween('end_time', [$startOfMonth, $endOfMonth])
            ->get();

        $important = DB::table('calendar_events')
        ->where(function($query) {
            $query->where('title', 'like', '%exam%')
                    ->orWhere('title', 'like', '%important%')
                    ->orWhere('title', 'like', '%enroll%');
        })
        ->get();
        // Log::debug('Now from variable', ['now' => $this_day]);
        // Log::debug('Now from Carbon', ['now' => Carbon::now()]);
        // Log::debug('Now from startOfDay', ['now' => $startOfDay]);
        // Log::debug('Now from endOfDay', ['now' => $endOfDay]);

        return response()->json([
            // 'dayToday' => $this_day,
            // 'startOfDay' => $startOfDay,
            // 'endOfDay' => $endOfDay,
            'today' => $eventsToday,
            'this_week' => $eventsThisWeek,
            'this_month' => $eventsThisMonth,
            'important' => $important,
        ]);
    }

    public function enrollmentData()
    {
        $currentTermDetails = $this->getCurrentTermDetails();
        $academicYear = $currentTermDetails['academic_year'];
        $term = $currentTermDetails['term'];
    
        // Total Students Enrolled in the Current Term
        $totalStudentsEnrolledCurrentTerm = DB::table('enrollments')
            ->where('academic_year', $academicYear)
            ->where('term', $term)
            ->count();
    
        // Enrollments Per Program for the Current Term
        $enrollmentsPerProgramCurrentTerm = DB::table('enrollments')
            ->join('programs', 'enrollments.program_id', '=', 'programs.program_id')
            ->where('enrollments.academic_year', $academicYear)
            ->where('enrollments.term', $term)
            ->select('programs.program_name', DB::raw('count(*) as total'))
            ->groupBy('programs.program_name')
            ->get();
    
        // Enrollments Per Year Level for the Current Term
        $enrollmentsPerYearLevelCurrentTerm = DB::table('enrollments')
            ->where('academic_year', $academicYear)
            ->where('term', $term)
            ->select('year_level', DB::raw('count(*) as total'))
            ->groupBy('year_level')
            ->get();
    
        // Enrollments Per Program Per Year Level for the Current Term
        $enrollmentsPerProgramPerYearLevelCurrentTerm = DB::table('enrollments')
            ->join('programs', 'enrollments.program_id', '=', 'programs.program_id')
            ->where('enrollments.academic_year', $academicYear)
            ->where('enrollments.term', $term)
            ->select('programs.program_name', 'enrollments.year_level', DB::raw('count(*) as total'))
            ->groupBy('programs.program_name', 'enrollments.year_level')
            ->get();
    
        
        return [
            'totalStudentsEnrolledCurrentTerm' => $totalStudentsEnrolledCurrentTerm,
            'enrollmentsPerProgramCurrentTerm' => $enrollmentsPerProgramCurrentTerm,
            'enrollmentsPerYearLevelCurrentTerm' => $enrollmentsPerYearLevelCurrentTerm,
            'enrollmentsPerProgramPerYearLevelCurrentTerm' => $enrollmentsPerProgramPerYearLevelCurrentTerm,
        ];
    }

    private function getCurrentTermDetails()
    {
        $today = Carbon::now();

        // Fetch the current academic year based on today's date. Adjust the logic if your academic year spans two calendar years
        $currentAcademicYear = DB::table('academic_years')
            ->where('term_1_start', '<=', $today)
            ->where('term_3_end', '>=', $today)
            ->first();

        if (!$currentAcademicYear) {
            return null; 
        }

        $currentTerm = null;
        if ($today->between($currentAcademicYear->term_1_start, $currentAcademicYear->term_1_end)) {
            $currentTerm = 'Term 1';
        } elseif ($today->between($currentAcademicYear->term_2_start, $currentAcademicYear->term_2_end)) {
            $currentTerm = 'Term 2';
        } elseif ($today->between($currentAcademicYear->term_3_start, $currentAcademicYear->term_3_end)) {
            $currentTerm = 'Term 3';
        }

        return [
            'academic_year' => $currentAcademicYear->acad_year,
            'term' => $currentTerm,
        ];
    }
}
