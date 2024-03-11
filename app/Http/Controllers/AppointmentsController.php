<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AppointmentsController extends Controller
{
    public function index()
    {
    
        if (Auth::check()) {
            $user = Auth::user();
            
            if ($user->role === 'admin') {
                return view('admin.appointments-dashboard');
            } else if ($user->role === 'user') {
                return view('user.appointments');
            }
        }
        return redirect('/');
    }

    public function appointmentsCalendarJson()
    {
        $latestEnrollmentsSubquery = DB::table('enrollments')
            ->selectRaw('student_id, program_id, year_level, enrollment_date, ROW_NUMBER() OVER (PARTITION BY student_id ORDER BY enrollment_date DESC) as rn')
            ->toSql();
    
        $formattedAppointments = Appointment::query()
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
                DB::raw('programs.program_name, enrollments.year_level, enrollments.enrollment_date')
            ])
            ->join('users', 'appointments.user_id', '=', 'users.id')
            ->join('students', 'users.id', '=', 'students.user_id')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->leftJoin(DB::raw("({$latestEnrollmentsSubquery}) as enrollments"), function($join) {
                $join->on('students.student_id', '=', 'enrollments.student_id')
                    ->where('enrollments.rn', '=', 1);
            })
            ->join('programs', 'enrollments.program_id', '=', 'programs.program_id')
            ->mergeBindings(DB::table('enrollments'))
            ->get() 
            ->map(function ($appointment) {
                $createdAt = $appointment->created_at instanceof \Illuminate\Support\Carbon
                            ? $appointment->created_at->toDateTimeString()
                            : $appointment->created_at;
    
                return [
                    'title' => $appointment->student_number . ' ' . $appointment->student_last_name . ', ' . substr($appointment->student_first_name, 0, 1) . '.', // Format title
                    'start' => $createdAt,
                    'end' => $createdAt,
                    'extendedProps' => [
                        'service_name' => $appointment->service_name,
                        'student_number' => $appointment->student_number,
                        'id' => $appointment->id,
                    ],
                ];
            });
    
        $appointmentsJson = json_encode($formattedAppointments);

        return response()->json($formattedAppointments);
    }

    public function manage($id) {
        $appointment = Appointment::find($id);
    
        if (!$appointment) {
            return redirect()->back()->with('error', 'Appointment not found.');
        }
    
        return view('admin.manage-appointment', compact('appointment'));
    }

    public function appointments(Request $request)
    {
        // $appointmentsQuery = Appointment::query()
        //     ->select('appointments.user_id', 'students.first_name as student_first_name','services.created_at', 'students.last_name as student_last_name', 'users.email', 'students.student_number', 'services.service_name', 'appointments.status', DB::raw('programs.program_name, enrollments.year_level, enrollments.enrollment_date'))
        //     ->join('users', 'appointments.user_id', '=', 'users.id')
        //     ->join('students', 'users.id', '=', 'students.user_id')
        //     ->join('services', 'appointments.service_id', '=', 'services.id')
        //     ->leftJoin(DB::raw('(SELECT *, ROW_NUMBER() OVER(PARTITION BY student_id ORDER BY enrollment_date DESC) AS rn FROM enrollments) as enrollments'), function($join) {
        //         $join->on('students.student_id', '=', 'enrollments.student_id')->where('enrollments.rn', '=', 1);
        //     })
        //     ->join('programs', 'enrollments.program_id', '=', 'programs.program_id')
        //     ->get();

        $latestEnrollmentsSubquery = DB::table('enrollments')
        ->selectRaw('student_id, program_id, year_level, enrollment_date, ROW_NUMBER() OVER (PARTITION BY student_id ORDER BY enrollment_date DESC) as rn')
        ->toSql();

        $appointmentsQuery = Appointment::query()
            ->select([
                'appointments.user_id',
                'students.first_name as student_first_name',
                'students.last_name as student_last_name',
                'users.email',
                'students.student_number',
                'services.service_name',
                'appointments.status',
                'appointments.created_at',
                DB::raw('programs.program_name, enrollments.year_level, enrollments.enrollment_date')
            ])
            ->join('users', 'appointments.user_id', '=', 'users.id')
            ->join('students', 'users.id', '=', 'students.user_id')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->leftJoin(DB::raw("({$latestEnrollmentsSubquery}) as enrollments"), function($join) {
                $join->on('students.student_id', '=', 'enrollments.student_id')
                    ->where('enrollments.rn', '=', 1);
            })
            ->join('programs', 'enrollments.program_id', '=', 'programs.program_id')
            ->mergeBindings(DB::table('enrollments')); 
        
        $searchTerm = $request->query('query');
        if ($searchTerm) {
            $appointmentsQuery->where(function ($query) use ($searchTerm) {
                $query->where('students.first_name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('students.last_name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('students.student_number', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('programs.program_name', 'LIKE', "%{$searchTerm}%");
            });
        }

        $appointments = $appointmentsQuery->paginate(10)->withQueryString();

        return view('admin.appointments', [
            'appointments' => $appointments,
            'appointmentsJson' => $appointments->toJson(),
            'searchTerm' => $searchTerm,
        ]);
    }
    
    public function request_appointment(Request $request)
    {
        $appointment = Appointment::create([
            'user_id' => $request->user_id,
            'service_id' => $request->service_id,
            'notes' => $request->notes,
            'appointment_datetime' => now(),
        ]);
        
        return redirect()->back()->with('success', 'Request sent! Please wait for a response from the registrar.');

    }

    public function getUserAppointments(Request $request)
{
    $userId = Auth::id();

    $appointments = Appointment::join('services', 'appointments.service_id', '=', 'services.id')
                        ->where('appointments.user_id', $userId)
                        ->where('appointments.status', '!=', 'complete')
                        ->get([
                            'appointments.user_id',
                            'services.service_name', 
                            'appointments.status',
                            'appointments.viewed_date',
                            'appointments.complete_date',
                        ])
                        ->map(function ($appointment) {
                            return [
                                'user_id' => $appointment->user_id,
                                'service_name' => $appointment->service_name, 
                                'status' => $appointment->status,
                                'viewed_date' => $appointment->viewed_date,
                                'complete_date' => $appointment->complete_date,
                            ];
                        });

    return response()->json($appointments);
}

public function getUserCompletedAppointments(Request $request)
{
    $userId = Auth::id();

    $appointments = Appointment::join('services', 'appointments.service_id', '=', 'services.id')
                        ->where('appointments.user_id', $userId)
                        ->where('appointments.status', '=', 'complete')
                        ->get([
                            'appointments.user_id',
                            'services.service_name', 
                            'appointments.status',
                            'appointments.viewed_date',
                            'appointments.complete_date',
                        ])
                        ->map(function ($appointment) {
                            return [
                                'user_id' => $appointment->user_id,
                                'service_name' => $appointment->service_name, 
                                'status' => $appointment->status,
                                'viewed_date' => $appointment->viewed_date,
                                'complete_date' => $appointment->complete_date,
                            ];
                        });

    return response()->json($appointments);
}

}
