<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrolled_Subject extends Model
{
    use HasFactory;

    protected $table = 'enrolled_subjects';

    protected $primaryKey = 'en_subjects_id';

    protected $fillable = [
        'enrollment_id',
        'subject_id',
        'sec_sub_id',
        'final_grade',
        'enrolledSubject_code',
        'remarks',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function sectionSubject()
    {
        return $this->belongsTo(SectionSubject::class, 'sec_sub_id');
    }

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class, 'enrollment_id');
    }


}
