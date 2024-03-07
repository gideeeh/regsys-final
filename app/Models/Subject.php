<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $table = 'subjects';

    protected $primaryKey = 'subject_id';

    protected $fillable = [
        'subject_code',
        'subject_name',
        'subject_description',
        'units_lec',
        'units_lab',
        'prerequisite_1',
        'prerequisite_2',
        'prerequisite_3',
    ];

    public function prerequisite1()
    {
        return $this->belongsTo(Subject::class, 'prerequisite_1');
    }

    public function prerequisite2()
    {
        return $this->belongsTo(Subject::class, 'prerequisite_2');
    }

    public function prerequisite3()
    {
        return $this->belongsTo(Subject::class, 'prerequisite_3');
    }

    public function setPrerequisite1Attribute($value)
    {
        $this->attributes['prerequisite_1'] = $value !== '' ? $value : null;
    }

    public function setPrerequisite2Attribute($value)
    {
        $this->attributes['prerequisite_2'] = $value !== '' ? $value : null;
    }

    public function setPrerequisite3Attribute($value)
    {
        $this->attributes['prerequisite_3'] = $value !== '' ? $value : null;
    }

    public function enrolledSubjects()
    {
        return $this->hasMany(Enrolled_Subject::class, 'subject_id');
    }

    public function program_semester_subjects()
    {
        return $this->hasMany(Program_Semester_Subject::class, 'subject_id');
    }

    public function sectionsSubject()
    {
        return $this->hasMany(Section::class, 'subject_id');
    }
}
