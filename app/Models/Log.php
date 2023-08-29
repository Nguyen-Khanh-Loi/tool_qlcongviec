<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TaskUser;
use App\Models\Tasks;
use App\Models\User;
class Log extends Model
{
    use HasFactory;
    protected $table = 'logs';
    protected $fillable = [         
        'target_id',
        'action',
        'type',
        'user_id',
    ];
    
    public function tasks()
    {
        return $this->belongsTo(Tasks::class, 'id');
    }

    public function users()
    {
        return $this->hasOne(User::class, 'user_id');
    }
}
