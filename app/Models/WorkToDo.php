<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Tasks;
use App\Models\CheckList;

class WorkToDo extends Model
{
    use HasFactory;
    protected $table = 'work_todo';
    protected $fillable = [         
        'task_id',
        'title',
        'slug',
        'deadline'
    ];

    public function tasks()
    {
        return $this->belongsTo(Tasks::class, 'task_id');
    }


    public function checklist()
    {
        return $this->hasMany(CheckList::class, 'work_todo_id');
    }
}
