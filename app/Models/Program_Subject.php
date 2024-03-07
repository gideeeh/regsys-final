<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program_Subject extends Model
{
    use HasFactory;

    protected $table = 'program_subjects';
    protected $primaryKey = 'id';
    protected $fillable = [
        'program_id',
        'year',
        'term',
        'subject_id',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }
}
