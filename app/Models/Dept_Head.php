<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dept_Head extends Model
{
    use HasFactory;
    protected $table = 'dept_heads';
    protected $primaryKey = 'dept_head_id';
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
}
