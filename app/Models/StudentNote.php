<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentNote extends Model
{
    use HasFactory;

    protected $table = 'student_notes';
    protected $primaryKey = 'id';
    protected $fillable = [
        'note_title',
        'student_id',
        'note',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
