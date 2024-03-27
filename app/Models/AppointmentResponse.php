<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppointmentResponse extends Model
{
    use HasFactory;

    protected $table = 'appointment_responses';
    protected $primaryKey = 'id';

    protected $fillable = [
        'appointment_id',
        'user_id',
        'response_message',
        'file_path',
        'file_name',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
