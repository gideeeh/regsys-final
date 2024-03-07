<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectionSubject extends Model
{
    use HasFactory;
    protected $table = 'section_subjects'; 
    protected $primarykey = 'id';
    protected $fillable = [
        'section_id',
        'subject_id',
    ];

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function subjectSectionSchedule()
    {
        return $this->hasOne(SectionSubjectSchedule::class, 'sec_sub_id');
    }

    public function enrolledSubject()
    {
        return $this->hasOne(Enrolled_Subject::class, 'sec_sub_id');
    }
}
