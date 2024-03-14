<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;
    
    protected $table = 'programs';

    protected $primaryKey = 'program_id';

    protected $fillable = [
        'program_code',
        'program_name',
        'program_desc',
        'program_major',
        'degree_type',
        'dept_id',
        'program_coordinator',
        'total_units',
        'status',
    ];

    public function department() {
        return $this->belongsTo(Department::class, 'dept_id');
    }

    public function enrollments() {
        return $this->hasMany(Enrollment::class, 'program_id');
    }

    public function programSemesterSubjects() {
        return $this->hasMany(Program_Subject::class, 'program_id');
    }

    public function sections()
    {
        return $this->hasMany(Section::class, 'program_id');
    }
}
