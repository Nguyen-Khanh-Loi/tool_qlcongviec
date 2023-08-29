<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $table = 'notifications';
    protected $fillable = [         
        'project_id',
        'task_id',
        'user_id',
        'author_id',
        'action',
        'description',
        'type',
        'status',
    ];
}
