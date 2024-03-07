<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $table = 'sections';
    protected $primaryKey = 'section_id';
    protected $fillable = [
        'section_name',
        'academic_year',
        'term',
        'year_level',
        'program_id',
    ];

    public function sectionSubject()
    {
        return $this->hasMany(SectionSubject::class, 'section_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }
}
