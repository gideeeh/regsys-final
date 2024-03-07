<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calendar_Event extends Model
{
    use HasFactory;
    protected $table = 'calendar_events';
    protected $primaryKey = 'id';
    // protected $dates = ['start_time', 'end_time'];
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];
    protected $fillable = [
        'start_time',
        'title',
        'end_time',
        'comments',
    ];
}
