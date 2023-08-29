<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Tasks;
use App\Models\User;

class ChatTasks extends Model
{
    use HasFactory;
    protected $fillable = [         
        // 'id',
        'task_id',
        'user_id',
        'content',
        // 'create_at',
        // 'updated_at',
    ];
    protected $table = 'chattasks';

    public function details_tasks()
    {
        return $this->belongsTo(Tasks::class, 'task_id');
    }
}
