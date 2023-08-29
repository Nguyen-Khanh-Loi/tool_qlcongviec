<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\WorkToDo;

class CheckList extends Model
{
    use HasFactory;
    protected $table = 'checklists';
    protected $fillable = [         
        'title',
        'work_todo_id',
        'slug',
        'deadline',
        'status',
        'list_user_ids',
    ];

    public function worktodo()
    {
        return $this->belongsTo(WorkToDo::class, 'work_todo_id');
    }
}
