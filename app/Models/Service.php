<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    protected $casts = [
        'allowed_file_extensions' => 'array',
    ];
    protected $table = 'services';
    protected $primaryKey = 'id';

    protected $fillable = [
        'service_name',
        'description',
        'requireUpload',
        'service_instructions',
        'allowed_file_extensions',
        'max_file_size',
        'isActive',
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
