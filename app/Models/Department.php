<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Project;
use App\Models\Tasks;

class Department extends Model
{
    use HasFactory;
    protected $table = 'departments';

    public function project()
    {
        return $this->belongsToMany(Project::class);
    }

    public function tasks()
    {
        return $this->hasMany(Tasks::class, 'department_id');
    }
}
