<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Professor extends Model
{
    use HasFactory;

    protected $table = 'professors';
    protected $primaryKey = 'prof_id';
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'dept_id',
        'personal_email',
        'school_email',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'dept_id');
    }

    public function sectionSchedules()
    {
        return $this->hasMany(Section_Schedule::class, 'prof_id');
    }
}
