<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppointmentsDashboardController extends Controller
{
    public function appointmentsQueue() {
        $now = Carbon::now('Asia/Singapore');
        $todayStart = (clone $now)->startOfDay(); 
        $todayEnd = (clone $now)->endOfDay();
        $tomorrowStart = (clone $now)->addDay()->startOfDay();
        $tomorrowEnd = (clone $now)->addDay()->endOfDay();
        $startOfWeek = (clone $now)->startOfWeek();
        $endOfWeek = (clone $now)->endOfWeek();

        // Pending Appointments

        // $now = Carbon::now('Asia/Singapore');
        $oneDayAgo = $now->copy()->subDay()->endOfDay();
        $twoDaysAgo = $now->copy()->subDays(2)->endOfDay();
        $threeDaysAgo = $now->copy()->subDays(3)->endOfDay();
    
        $latestEnrollmentsSubquery = DB::table('enrollments')
            ->selectRaw('student_id, program_id, year_level, enrollment_date, ROW_NUMBER() OVER (PARTITION BY student_id ORDER BY enrollment_date DESC) as rn')
            ->toSql();
    
        $baseQuery = Appointment::query()
            ->select([
                'appointments.id',
                'appointments.user_id',
                'students.first_name as student_first_name',
                'students.last_name as student_last_name',
                'users.email',
                'students.student_number',
                'services.service_name',
                'appointments.status',
                'appointments.created_at',
                'appointments.appointment_datetime',
                DB::raw('programs.program_name, enrollments.year_level, enrollments.enrollment_date')
            ])
            ->join('users', 'appointments.user_id', '=', 'users.id')
            ->join('students', 'users.id', '=', 'students.user_id')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->leftJoin(DB::raw("({$latestEnrollmentsSubquery}) as enrollments"), 'students.student_id', '=', 'enrollments.student_id')
            ->where('enrollments.rn', '=', 1)
            ->join('programs', 'enrollments.program_id', '=', 'programs.program_id')
            ->mergeBindings(DB::table('enrollments')); 
    
        $appointmentsToday = (clone $baseQuery)->whereBetween('appointments.appointment_datetime', [$todayStart, $todayEnd])->get();
        $appointmentsTomorrow = (clone $baseQuery)->whereBetween('appointments.appointment_datetime', [$tomorrowStart, $tomorrowEnd])->get();
        $appointmentsThisWeek = (clone $baseQuery)->whereBetween('appointments.appointment_datetime', [$startOfWeek, $endOfWeek])->get();

        $pendingOneDay = (clone $baseQuery)
            ->where('appointments.appointment_datetime', '<=', $oneDayAgo)
            ->where('appointments.appointment_datetime', '>', $twoDaysAgo)
            ->get();

        $pendingTwoDays = (clone $baseQuery)
            ->where('appointments.appointment_datetime', '<=', $twoDaysAgo)
            ->where('appointments.appointment_datetime', '>', $threeDaysAgo)
            ->get();
        // $pendingTwoDays = (clone $baseQuery)->where('appointments.created_at', '<=', $twoDaysAgo , '&', 'appointments.created_at', '<', $oneDayAgo)->get();
        $pendingBeyondTwoDays = (clone $baseQuery)->where('appointments.created_at', '<=', $threeDaysAgo)->get();
    
        $appointments = [
            'today' => $appointmentsToday,
            'tomorrow' => $appointmentsTomorrow,
            'thisWeek' => $appointmentsThisWeek,
            'pendingOneDay' => $pendingOneDay,
            // 'oneDayAgo' => $oneDayAgo,
            'pendingTwoDays' => $pendingTwoDays,
            'pendingBeyondTwoDays' => $pendingBeyondTwoDays
        ];
    
        return response()->json($appointments);
    }

    public function pendingAppointments()
    {
        $now = Carbon::now('Asia/Singapore');
        $oneDayAgo = $now->copy()->subDay();
        $twoDaysAgo = $now->copy()->subDays(2);

        $latestEnrollmentsSubquery = DB::table('enrollments')
            ->selectRaw('student_id, program_id, year_level, enrollment_date, ROW_NUMBER() OVER (PARTITION BY student_id ORDER BY enrollment_date DESC) as rn')
            ->toSql();
    
        $baseQuery = Appointment::query()
            ->select([
                'appointments.id',
                'appointments.user_id',
                'students.first_name as student_first_name',
                'students.last_name as student_last_name',
                'users.email',
                'students.student_number',
                'services.service_name',
                'appointments.status',
                'appointments.created_at',
                'appointments.appointment_datetime',
                DB::raw('programs.program_name, enrollments.year_level, enrollments.enrollment_date')
            ])
            ->join('users', 'appointments.user_id', '=', 'users.id')
            ->join('students', 'users.id', '=', 'students.user_id')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->leftJoin(DB::raw("({$latestEnrollmentsSubquery}) as enrollments"), 'students.student_id', '=', 'enrollments.student_id')
            ->where('enrollments.rn', '=', 1)
            ->join('programs', 'enrollments.program_id', '=', 'programs.program_id')
            ->mergeBindings(DB::table('enrollments')); 

        $pendingOneDay = (clone $baseQuery)->where('appointments.created_at', '<=', $oneDayAgo)->get();
        $pendingTwoDays = (clone $baseQuery)->where('appointments.created_at', '<=', $twoDaysAgo)->get();
        $pendingBeyondTwoDays = (clone $baseQuery)->where('appointments.created_at', '<', $twoDaysAgo)->get();
    
        $pendingAppointments = [
            'pendingOneDay' => $pendingOneDay,
            'pendingTwoDays' => $pendingTwoDays,
            'pendingBeyondTwoDays' => $pendingBeyondTwoDays
        ];
    
        return response()->json($pendingAppointments);
    }
}
