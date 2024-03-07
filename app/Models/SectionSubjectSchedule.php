<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectionSubjectSchedule extends Model
{
    use HasFactory;
    protected $casts = [
        'class_days_f2f' => 'array',
        'class_days_online' => 'array',
    ];
    
    protected $table = 'section_subject_schedules'; 
    protected $primarykey = 'id';
    protected $fillable = [
        'sec_sub_id',
        'prof_id',
        'class_days_f2f',
        'class_days_online',
        'start_time_f2f',
        'end_time_f2f',
        'end_time_online',
        'start_time_online',
        'room',
        'class_limit',
    ];

    public function sectionSubject()
    {
        return $this->belongsTo(SectionSubject::class, 'sec_sub_id');
    }
    public function professor()
    {
        return $this->belongsTo(Professor::class, 'prof_id');
    }

}
