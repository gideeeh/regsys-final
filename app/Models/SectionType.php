<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectionType extends Model
{
    use HasFactory;

    public function sections()
    {
        return $this->hasMany(Section::class, 'section_type_id');
    }
}
