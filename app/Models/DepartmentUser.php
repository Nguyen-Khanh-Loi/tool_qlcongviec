<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class DepartmentUser extends Model
{
    use HasFactory;
    
    protected $table = 'department_user';
    protected $fillable = [
        'user_id',         
        'department_id',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
