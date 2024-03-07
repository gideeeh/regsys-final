<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentsController extends Controller
{
    public function index()
    {
        // Check if the user is authenticated
        if (Auth::check()) {
            // Get the currently authenticated user
            $user = Auth::user();
            
            // Determine the view based on the user's role
            if ($user->role === 'admin') {
                // Render the admin dashboard view
                return view('admin.appointments-dashboard');
            } else if ($user->role === 'user') {
                // Render a user-specific appointments view
                return view('user.appointments');
            }
        }
        return redirect('/'); 
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
