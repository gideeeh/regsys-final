<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $table = 'enrollments';
    protected $primaryKey = 'enrollment_id';
    protected $fillable = [
        'student_id',
        'program_id',
        'academic_year',
        'term',
        'year_level',
        'batch',
        'enrollment_date',
        'scholarship_type',
        'status',
        'enrollment_method',
        'enrollment_code',
    ];

    public function enrolledSubjects()
    {
        return $this->hasMany(Enrolled_Subject::class, 'enrollment_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }
}
