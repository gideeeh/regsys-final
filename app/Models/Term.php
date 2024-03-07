<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    use HasFactory;

    protected $table = 'terms';

    protected $primaryKey = 'id';

    protected $fillable = [ 
        'acad_year_id',
        'term', 
        'term_start', 
        'term_end', 
    ];

    public function academicYear() {
        return $this->belongsTo(Academic_Year::class, 'acad_year_id');
    }
}
