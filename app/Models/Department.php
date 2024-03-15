<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $table = 'departments';

    protected $primaryKey = 'dept_id';

    protected $fillable = [ 'dept_name', ];

    public function deptHead() {
        return $this->hasMany(Dept_Head::class, 'dept_id');
    }

    public function programs() {
        return $this->hasMany(Program::class, 'dept_id');
    }

    public function professors() {
        return $this->hasMany(Professor::class, 'dept_id');
    }

    public function deptHeads() {
        return $this->hasMany(Dept_Head::class, 'dept_id');
    }
}
