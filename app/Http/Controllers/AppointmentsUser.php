<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\ApptMgmtSettings;
use App\Models\Student;
use App\Models\User;
use DateInterval;
use DateTime;
use Illuminate\Support\Facades\Storage;

class AppointmentsUser extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $settings = ApptMgmtSettings::findOrFail(1);
        $appointments = $user->appointments()->orderBy('appointment_datetime', 'desc')->get();
        $mostRecentAppointment = $appointments->first();
    
        return view('user.appointments', compact(['appointments','mostRecentAppointment','settings']));
    }

    public function appointment_limit(Request $request)
    {
        $date = $request->query('date');
        $appointment_count = Appointment::whereDate('appointment_datetime', $date)->count();
        
        $settings = ApptMgmtSettings::findOrFail(1);

        // AM slots calculation
        $amStart = new DateTime($settings->am_availability_start);
        $amEnd = new DateTime($settings->am_availability_end);
        $amAvailabilityHours = $amEnd->diff($amStart)->h;
        $slotsPerPeriod = $settings->request_limit / 2;
        $timeIntervalAm = ($amAvailabilityHours / $slotsPerPeriod) * 60; // Convert hours to minutes for interval calculation
        
        $availableSlotsAm = [];
        for ($i = 0; $i < $slotsPerPeriod; $i++) {
            $slotTime = (clone $amStart)->add(new DateInterval("PT" . ($timeIntervalAm * $i) . "M"));
            $availableSlotsAm[] = $slotTime->format('H:i:s');
        }

        // PM slots calculation
        $pmStart = new DateTime($settings->pm_availability_start);
        $pmEnd = new DateTime($settings->pm_availability_end);
        $pmAvailabilityHours = $pmEnd->diff($pmStart)->h;
        $timeIntervalPm = ($pmAvailabilityHours / $slotsPerPeriod) * 60; // Convert hours to minutes for interval calculation
        
        $availableSlotsPm = [];
        for ($i = 0; $i < $slotsPerPeriod; $i++) {
            $slotTime = (clone $pmStart)->add(new DateInterval("PT" . ($timeIntervalPm * $i) . "M"));
            $availableSlotsPm[] = $slotTime->format('H:i:s');
        }

        $bookedAppointments = Appointment::whereDate('appointment_datetime', $date)->get();
        $bookedSlots = $bookedAppointments->map(function ($appointment) {
            // Ensure appointment_datetime is cast to Carbon instance in your Appointment model
            return $appointment->appointment_datetime->format('H:i:s');
        })->all();

        $availableSlotsAm = array_filter($availableSlotsAm, function($slot) use ($bookedSlots) {
            return !in_array($slot, $bookedSlots);
        });
    
        $availableSlotsPm = array_filter($availableSlotsPm, function($slot) use ($bookedSlots) {
            return !in_array($slot, $bookedSlots);
        });

        $availableTimeslots = array_values(array_merge($availableSlotsAm, $availableSlotsPm));
            
        $isFull = $appointment_count >= $settings->request_limit;

        return response()->json([
            'message' => $isFull ? 'No open slot available' : "Slots available: $appointment_count",
            'slots' => $availableTimeslots,
            'full' => $isFull
        ]);
    }

    public function request_appointment(Request $request)
    {
        $student = Student::where('user_id', $request->user_id)->first();
    
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }
        
        // $datetime_code = \Carbon\Carbon::now()->format('mdyHis');
        $date = \Carbon\Carbon::parse($request->appointment_date)->format('Y-m-d');
        $appointment_date = $date . ' ' . $request->timeslot;
        $datetime_code = \Carbon\Carbon::parse($appointment_date)->format('m_d_Y_Hi');
        $appointment_code = $student->student_number . '_' . $datetime_code;

        $appointmentDirectory = 'appointments/' . $appointment_code;
        Storage::makeDirectory($appointmentDirectory);

        if ($request->hasFile('add_file') && $request->file('add_file')->isValid()) {
            $file = $request->file('add_file');
            
            $filename = $file->getClientOriginalName();
            $filePath = $file->storeAs($appointmentDirectory, $filename);
        }

        $appointment = Appointment::create([
            'user_id' => $request->user_id,
            'concern' => $request->concern,
            'file_path' => $appointmentDirectory ?? null,
            'notes' => $request->notes ?? null,
            'appointment_datetime' => $appointment_date,
            'appointment_code' => $appointment_code,
        ]);
        
        $settings = ApptMgmtSettings::findOrFail(1);

        return redirect()->back()->with('success', "$settings->received_request_reply");
    }
}
