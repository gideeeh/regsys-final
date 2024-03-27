<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ApptMgmtSettings;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AppointmentsController extends Controller
{
    public function index()
    {
        $settings = ApptMgmtSettings::findOrFail(1);
        $settings->available_schedules = json_decode($settings->available_schedules, true);

        if (Auth::check()) {
            $user = Auth::user();
            
            if ($user->role === 'admin') {
                return view('admin.appointments-dashboard', [
                    
                    'settings' => $settings,
                ]);
            } 
            // else if ($user->role === 'user') {
            //     return view('user.appointments');
            // }
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
            ->leftJoin('programs', 'enrollments.program_id', '=', 'programs.program_id')
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
                        'user_id' => $appointment->user_id,
                    ],
                ];
            });
    
        $appointmentsJson = json_encode($formattedAppointments);

        return response()->json($formattedAppointments);
    }

    public function manage($id, Request $request) {
        $latestEnrollmentDatesSubquery = DB::table('enrollments')
            ->selectRaw('MAX(enrollment_date) as latest_enrollment_date, student_id')
            ->groupBy('student_id');

        $latestEnrollmentsSubquery = DB::table('enrollments')
            ->joinSub($latestEnrollmentDatesSubquery, 'latest_dates', function($join) {
                $join->on('enrollments.student_id', '=', 'latest_dates.student_id')
                    ->on('enrollments.enrollment_date', '=', 'latest_dates.latest_enrollment_date');
            })
            ->select('enrollments.student_id', 'enrollments.program_id', 'enrollments.year_level');

        $student = DB::table('students')
            ->leftJoinSub($latestEnrollmentsSubquery, 'latest_enrollments', function($join) {
                $join->on('students.student_id', '=', 'latest_enrollments.student_id');
            })
            ->leftJoin('programs', 'latest_enrollments.program_id', '=', 'programs.program_id')
            ->select([
                'students.student_id',
                'students.user_id',
                'students.student_number',
                'students.first_name',
                'students.middle_name',
                'students.last_name',
                'students.suffix',
                'students.personal_email',
                'latest_enrollments.year_level',
                'latest_enrollments.program_id',
                'programs.program_name',
                'programs.program_code'
            ])
            ->where('students.user_id', $id)
            ->first();

        $user = User::findOrFail($id);
        // $student = Student::where('user_id', $id)->first();
        $appointments = Appointment::query()
            ->select([
                'appointments.id',
                'appointments.user_id',
                'appointments.status',
                'appointments.viewed_date',
                'appointments.complete_date',
                'appointments.service_id',
                'appointments.appointment_datetime',
                'appointments.notes',
                'appointments.created_at',
                'services.service_name',
            ])
            ->join('services','appointments.service_id','=','services.id')
            ->where('user_id', $id)
            ->get();

        $highlightId = $request->query('highlight');
        $highlightedAppointment = null;
        if ($highlightId) {
            $highlightedAppointment = Appointment::query()
                ->select([
                    'appointments.id',
                    'appointments.user_id',
                    'appointments.status',
                    'appointments.viewed_date',
                    'appointments.complete_date',
                    'appointments.service_id',
                    'appointments.appointment_datetime',
                    'appointments.notes',
                    'appointments.created_at',
                    'services.service_name',
                ])
                ->join('services', 'appointments.service_id', '=', 'services.id')
                ->where('appointments.user_id', $id)
                ->where('appointments.id', $highlightId) // Specific to the highlighted appointment
                ->first();
                
            if ($highlightedAppointment && $highlightedAppointment->status === 'pending') {
                $highlightedAppointment->update([
                    'status' => 'viewed',
                    'viewed_date' => now(), 
                ]);

                $highlightedAppointment = $highlightedAppointment->fresh();
            }
        }

        return view('admin.manage-appointment', [
            'appointments' => $appointments,
            'user' => $user,
            'highlightedAppointment' => $highlightedAppointment,
            'student' => $student,
        ]);
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
                'appointments.id',
                'appointments.appointment_code',
                'students.student_id',
                'students.first_name as student_first_name',
                'students.last_name as student_last_name',
                'users.email',
                'students.student_number',
                'services.service_name',
                'appointments.status',
                'appointments.created_at',
                DB::raw('programs.program_name, programs.program_code, enrollments.year_level, enrollments.enrollment_date')
            ])
            ->join('users', 'appointments.user_id', '=', 'users.id')
            ->leftJoin('students', 'users.id', '=', 'students.user_id')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->leftJoin(DB::raw("({$latestEnrollmentsSubquery}) as enrollments"), function($join) {
                $join->on('students.student_id', '=', 'enrollments.student_id')
                    ->where('enrollments.rn', '=', 1);
            })
            ->leftJoin('programs', 'enrollments.program_id', '=', 'programs.program_id')
            ->mergeBindings(DB::table('enrollments'))
            ->orderBy('appointments.created_at', 'desc');; 
        
        $searchTerm = $request->query('query');
        if ($searchTerm) {
            $appointmentsQuery->where(function ($query) use ($searchTerm) {
                $query->where('students.first_name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('students.last_name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('students.student_number', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('programs.program_name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('appointments.appointment_code', 'LIKE', "%{$searchTerm}%");
            });
        }

        $appointments = $appointmentsQuery->paginate(6)->withQueryString();

        return view('admin.appointments', [
            'appointments' => $appointments,
            'appointmentsJson' => $appointments->toJson(),
            'searchTerm' => $searchTerm,
        ]);
    }
    
    public function request_appointment(Request $request)
    {
        $student = Student::where('user_id', $request->user_id)->first();
    
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }
    
        $datetime_code = \Carbon\Carbon::now()->format('mdyHis');
        $appointment_code = $student->student_number . '_' . $request->service_id . '_' . $datetime_code;
        $appointment = Appointment::create([
            'user_id' => $request->user_id,
            'service_id' => $request->service_id,
            'notes' => $request->notes ?? null,
            'appointment_datetime' => now(),
            'appointment_code' => $appointment_code,
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

    public function sample_qr()
    {
        $code = '1234-5678_1_032324114312'; 
        $qrCode = QrCode::size(300)->generate($code);
    
        return response($qrCode)->header('Content-Type', 'image/svg+xml');
    }

    public function generate_qr($qr_code)
    {
        $qrCode = QrCode::size(300)->generate(url('appointments/retrieve_qr/' . $qr_code));
    
        return response($qrCode)->header('Content-Type', 'image/svg+xml');
    }

    public function retrieveByQRCode($appointment_code)
    {
        $appointment = Appointment::where('appointment_code', $appointment_code)->first();
    
        if (!$appointment) {
            return redirect('/')->with('error', 'Appointment not found.');
        }
    
        $user = auth()->user(); 
    
        if ($user->id !== $appointment->user_id && $user->role !== 'admin') {
            return redirect('/')->with('error', 'Unauthorized to access this appointment.');
        }
    
        $student = Student::where('user_id', $appointment->user_id)->first();
    
        if (!$student) {
            return redirect('/')->with('error', 'Student not found.');
        }
    
        return view('admin.appointments-details', compact('appointment', 'student'));
    }
    
}
