<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Project;
use App\Models\Department;
use App\Models\DepartmentUser;
class ProjectDepartment extends Model
{
    use HasFactory;
    protected $table = 'project_departments';
    protected $fillable = [         
        'project_id',
        'department_id',
        'action',
        'description',
    ];
    public function project_details()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
    
}
