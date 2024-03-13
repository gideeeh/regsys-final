<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\User;

class AppointmentsUser extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $appointments = $user->appointments()->with('service')->orderBy('appointment_datetime', 'desc')->get();
        $mostRecentAppointment = $appointments->first();
    
        return view('user.appointments', [
            'appointments' => $appointments,
            'mostRecentAppointment' => $mostRecentAppointment,
        ]);
    }
}
