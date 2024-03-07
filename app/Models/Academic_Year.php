<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Academic_Year extends Model
{
    use HasFactory;

    protected $table = 'academic_years';

    protected $primaryKey = 'id';

    protected $fillable = [ 
        'acad_year', 
        'term_1_start', 
        'term_1_end', 
        'term_2_start', 
        'term_2_end', 
        'term_3_start', 
        'term_3_end', 
    ];
}
