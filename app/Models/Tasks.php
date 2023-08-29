<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Card;
use App\Models\Project;
use App\Models\WorkToDo;
use App\Models\Department;
use App\Models\ChatTasks;
use App\Models\User;
use App\Models\TaskUser;
use App\Models\Log;
use App\Models\Notification;

class Tasks extends Model
{
    use HasFactory;
    protected $fillable = [         
        'project_id',
        'card_id',
        'department_id',
        'user_id',
        'list_user_ids',
        'slug',
        'description',
        'title',
        'deadline',
        'status',
        'active',
        'reject'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function projects()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

     /**
     * List worktodo.
     *
     * @var task_id
     */
    public function worktodo()
    {
        return $this->hasMany(WorkToDo::class, 'task_id');
    }

    /**
     * card id.
     *
     * @var card_id
     */
    public function card()
    {
        return $this->hasOne(Card::class, 'card_id');
    }

     /**
     * messengers.
     *
     * @var task_id
     */
    public function messengers()
    {
        return $this->hasMany(ChatTasks::class, 'task_id')->latest();      
    }
    /**
     * messengers.
     *
     * @var task_id
     */
    public function task_users()
    {
        return $this->hasMany(TaskUser::class, 'task_id');      
    }

    /**
     * messengers.
     *
     * @var task_id
     */
    public function request_logs()
    {
        return $this->hasOne(Notification::class, 'task_id');      
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');    
    }

    public function views()
    {
        return $this->hasMany(Log::class, 'target_id');    
    }
}
