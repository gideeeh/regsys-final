<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApptMgmtSettings extends Model
{
    use HasFactory;

    protected $casts = [
        'available_schedules' => 'array',
    ];

    protected $table = 'appt_mgmt_settings';
    protected $primaryKey = 'id';
    protected $fillable = [
        'request_limit',
        'buffer_time_minutes',
        'am_availability_start',
        'am_availability_end',
        'pm_availability_start',
        'pm_availability_end',
        'available_schedules',   
        'received_request_reply'
    ];
}
