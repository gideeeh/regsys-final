<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ApptMgmtSettings;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
    
        $appointments = Appointment::query()
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
            ])
            ->join('users', 'appointments.user_id', '=', 'users.id')
            ->leftjoin('students', 'users.id', '=', 'students.user_id')
            ->join('services', 'appointments.service_id', '=', 'services.id');

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
            ->leftjoin('students', 'users.id', '=', 'students.user_id')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->leftJoin(DB::raw("({$latestEnrollmentsSubquery}) as enrollments"), 'students.student_id', '=', 'enrollments.student_id')
            ->where('enrollments.rn', '=', 1)
            ->leftjoin('programs', 'enrollments.program_id', '=', 'programs.program_id')
            ->mergeBindings(DB::table('enrollments')); 
    
        $appointmentsToday = (clone $appointments)->whereBetween('appointments.appointment_datetime', [$todayStart, $todayEnd])->get();
        $appointmentsTomorrow = (clone $appointments)->whereBetween('appointments.appointment_datetime', [$tomorrowStart, $tomorrowEnd])->get();
        $appointmentsThisWeek = (clone $appointments)->whereBetween('appointments.appointment_datetime', [$startOfWeek, $endOfWeek])->get();

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
            'appointments' => $appointments,
            'tomorrow' => $appointmentsTomorrow,
            'thisWeek' => $appointmentsThisWeek,
            'pendingOneDay' => $pendingOneDay,
            'oneDayAgo' => $oneDayAgo,
            'pendingTwoDays' => $pendingTwoDays,
            'pendingBeyondTwoDays' => $pendingBeyondTwoDays
        ];
    
        return response()->json($appointments);
    }

    public function latestAppointment() 
    {
        $latestAppointment = Appointment::query()
            ->select([
                'appointments.id',
                'appointments.user_id',
                'students.first_name as student_first_name',
                'students.last_name as student_last_name',
                'students.student_number',
                'services.service_name',
                'appointments.appointment_datetime',
            ])
            ->join('users', 'appointments.user_id', '=', 'users.id')
            ->join('students', 'users.id', '=', 'students.user_id')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->orderBy('appointment_datetime','desc')->first();
    
        return response()->json($latestAppointment);
    }
    

    public function saveMgmtSettings(Request $request) 
    {
        try {
            Log::info('Updating appointment management settings', $request->all());

            $validated = $request->validate([
                'requestLimit' => 'required|integer|min:1',
                'bufferTime' => 'required|integer|min:1',
                'available_days' => 'nullable|array',
                'customReceivedRequestReply' => 'nullable|string',
            ]);

            if(isset($validated['available_days'])) {
                $validated['available_days'] = json_encode($validated['available_days']);
            }

            $setting = ApptMgmtSettings::findOrFail(1);
            $setting->update([
                'request_limit' => $validated['requestLimit'],
                'buffer_time_minutes' => $validated['bufferTime'],
                'am_availability_start' => $request->amStartTime,
                'am_availability_end' => $request->amEndTime,
                'pm_availability_start' => $request->pmStartTime,
                'pm_availability_end' => $request->pmEndTime,
                'available_schedules' => $validated['available_days'],
                'received_request_reply' => $validated['customReceivedRequestReply'],
            ]);

            // Log success
            Log::info('Appointment management settings updated successfully.');

            return redirect()->back()->with('success', 'Settings Updated Successfully');
        } catch (\Exception $e) {
            // Log exception
            Log::error('Error updating appointment management settings: ' . $e->getMessage());

            return redirect()->back()->with('error', 'There was a problem updating the settings.');
        }
    }

}
