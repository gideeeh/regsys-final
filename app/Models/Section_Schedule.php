<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section_Schedule extends Model
{
    use HasFactory;
    
    protected $table = 'section_schedules';

    protected $primaryKey = 'section_sched_id';

    protected $fillable = [
        'section_id',
        'prof_id',
        'class_day',
        'start_time',
        'end_time',
        'room',
    ];

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function professor()
    {
        return $this->belongsTo(Professor::class, 'prof_id');
    }
}
