<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\AppointmentResponse;
use App\Models\ApptMgmtSettings;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
                'appointments.concern',
                'appointments.appointment_datetime',
                'appointments.status',
                'appointments.created_at',
                DB::raw('programs.program_name, enrollments.year_level, enrollments.enrollment_date')
            ])
            ->join('users', 'appointments.user_id', '=', 'users.id')
            ->join('students', 'users.id', '=', 'students.user_id')
            ->leftJoin(DB::raw("({$latestEnrollmentsSubquery}) as enrollments"), function($join) {
                $join->on('students.student_id', '=', 'enrollments.student_id')
                    ->where('enrollments.rn', '=', 1);
            })
            ->leftJoin('programs', 'enrollments.program_id', '=', 'programs.program_id')
            ->mergeBindings(DB::table('enrollments'))
            ->get() 
            ->map(function ($appointment) {
                $createdAt = $appointment->appointment_datetime instanceof \Illuminate\Support\Carbon
                            ? $appointment->appointment_datetime->toDateTimeString()
                            : $appointment->appointment_datetime;
    
                return [
                    'title' => $appointment->student_number . ' ' . $appointment->student_last_name . ', ' . substr($appointment->student_first_name, 0, 1) . '.', // Format title
                    'start' => $createdAt,
                    'end' => $createdAt,
                    'extendedProps' => [
                        'concern' => $appointment->concern,
                        'student_number' => $appointment->student_number,
                        'id' => $appointment->id,
                        'user_id' => $appointment->user_id,
                    ],
                ];
            });
    
        $appointmentsJson = json_encode($formattedAppointments);

        return response()->json($formattedAppointments);
    }

    public function manage($appointment_id, Request $request) {
        $appointment = Appointment::findOrFail($appointment_id);
        $user = Auth::user();
        $student_user = User::with(['student','student.latestEnrollment'])->findOrFail($appointment->user_id);
        // $current_program = Enrollment::where('student_id',$student_user->student->student_id)->orderBy('created_at','desc')->first();
        $appt_history = Appointment::where('user_id',$appointment->user_id)->get();
        $appointment_responses = AppointmentResponse::where('appointment_id', $appointment_id)->get();
        
        $file_path_user = 'app/' . $appointment->file_path;
        $directoryPath = storage_path($file_path_user);
        if (File::exists($directoryPath)) {
            $files = File::files($directoryPath); 
        } else {
            $files = []; 
        }

        return view('admin.manage-appointment', [
            'appt_history' => $appt_history,
            'appointment' => $appointment,
            'student' => $student_user,
            'user' => $user,
            'appointment_responses' => $appointment_responses,
            'files' => $files,
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
                'appointments.concern',
                'appointments.appointment_datetime',
                'appointments.status',
                'appointments.created_at',
                DB::raw('programs.program_name, programs.program_code, enrollments.year_level, enrollments.enrollment_date')
            ])
            ->join('users', 'appointments.user_id', '=', 'users.id')
            ->leftJoin('students', 'users.id', '=', 'students.user_id')
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
    
    public function appointment_response(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required',
            'user_id' => 'required',
            'response_file' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx',
            'response_message' => 'required'
        ]);

        if ($request->hasFile('response_file') && $request->file('response_file')->isValid()) {
            $file = $request->file('response_file');
            $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs($request->file_path, $filename); 
        }

        $response = AppointmentResponse::create([
            'appointment_id' => $request->appointment_id,
            'user_id' => $request->user_id,
            'response_message' => $request->response_message,
            'file_path' => isset($filePath) ? $filePath : null,
            'file_name' => isset($filename) ? $filename : null,
        ]);

        return back()->with('success', 'Response successfully completed.');
    }

    public function download_file($appt_id, $appt_code, $file_name)
    {
        $appointment = Appointment::findOrFail($appt_id);
        $currentUser = auth()->user();
    
        if ($currentUser->id !== $appointment->user_id && $currentUser->role !== 'admin') {
            abort(403, "You're not authorized to access this file.");
        }
    
        $filePath = $appointment->file_path . '/' . $file_name;
        Log::debug("File path: $filePath");
        if (Storage::exists($filePath)) {
            return Storage::download($filePath, $file_name);
        } else {
            abort(404, 'File not found.');
        }
    }
}
