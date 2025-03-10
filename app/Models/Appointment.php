<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'viewed_date',
        'complete_date',
        'service_id',
        'appointment_datetime',
        'notes',
        'appointment_code',
        'concern',
        'file_path',
    ];
    protected $casts = [
        'appointment_datetime' => 'datetime',
    ];
    // Relationship to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship to a service
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function responses()
    {
        return $this->hasMany(AppointmentResponse::class);
    }

    public function getQrCodeUrlAttribute()
    {
        return route('appointments.generate-qr', ['qr_code' => $this->appointment_code]);
    }
}
