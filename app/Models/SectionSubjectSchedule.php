<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectionSubjectSchedule extends Model
{
    use HasFactory;
    // protected $casts = [
    //     'class_days_f2f' => 'array',
    //     'class_days_online' => 'array',
    // ];
    
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

    public function getScheduleF2FFormattedAttribute()
    {
        $daysF2F = json_decode($this->class_days_f2f, true) ?? [];
        $dayMap = [
            'Monday' => 'Mon',
            'Tuesday' => 'Tue',
            'Wednesday' => 'Wed',
            'Thursday' => 'Thu',
            'Friday' => 'Fri',
            'Saturday' => 'Sat',
            'Sunday' => 'Sun',
        ];

        $abbreviatedDaysF2F = array_map(function($day) use ($dayMap) {
            return $dayMap[$day] ?? $day;
        }, $daysF2F);

        $startTimeF2F = Carbon::parse($this->start_time_f2f)->format('h:i A');
        $endTimeF2F = Carbon::parse($this->end_time_f2f)->format('h:i A');

        return implode(', ', $abbreviatedDaysF2F) . " {$startTimeF2F} - {$endTimeF2F}";
    }

    public function getScheduleOLFormattedAttribute()
    {
        $daysOL = json_decode($this->class_days_online, true) ?? [];
        $dayMap = [
            'Monday' => 'Mon',
            'Tuesday' => 'Tue',
            'Wednesday' => 'Wed',
            'Thursday' => 'Thu',
            'Friday' => 'Fri',
            'Saturday' => 'Sat',
            'Sunday' => 'Sun',
        ];

        $abbreviatedDaysOL = array_map(function($day) use ($dayMap) {
            return $dayMap[$day] ?? $day;
        }, $daysOL);

        $startTimeOL = Carbon::parse($this->start_time_online)->format('h:i A');
        $endTimeOL = Carbon::parse($this->end_time_online)->format('h:i A');

        return implode(', ', $abbreviatedDaysOL) . " {$startTimeOL} - {$endTimeOL}";
    }
}
