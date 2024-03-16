<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $table = 'files';
    protected $primaryKey = 'id';
    protected $fillable = [
        'student_id',
        'file_name',
        'file_extension',
        'uploaded_at',
        'uploaded_by',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
