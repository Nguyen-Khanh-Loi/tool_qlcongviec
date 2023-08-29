<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Card;
use App\Models\Tasks;
use App\Models\Department;
use App\Models\DepartmentUser;
use App\Models\ProjectDepartment;
use App\Models\ProjectUser;
use App\Models\Log;

class Project extends Model
{
    use HasFactory;
    protected $fillable = [
        'code', 
        'code_company_center', 
        'user_id',         
        'title',
        'slug',
        'url_image',
        'description',
        'status',
        'start_time',
        'end_time',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tasks()
    {
        return $this->hasMany(Tasks::class, 'project_id');
    }

     /**
     * List user in project.
     *
     * @var project_id
     */
    public function projectuser()
    {
        return $this->hasMany(ProjectUser::class, 'project_id');
    }

     /**
     * List user in project.
     *
     * @var project_id
     */
    public function department()
    {
        return $this->hasMany(Department::class);
    }

     /**
     * List user in project.
     *
     * @var department_id
     */
    public function listuser()
    {
        return $this->hasMany(DepartmentUser::class);
    }

    /**
     * List user in project.
     *
     * @var department_id
     */
    public function task_request()
    {
        return $this->hasMany(Tasks::class);
    }

    /**
     * lấy tất cả các bộ phận được thêm vào project
     *
     * @var department_id
     */
    public function project_department()
    {
        return $this->hasMany(ProjectDepartment::class, 'project_id');
    }
}
