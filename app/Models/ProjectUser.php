<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Project;
use App\Models\User;

class ProjectUser extends Model
{
    use HasFactory;
    protected $table = 'project_users';
    protected $fillable = [         
        'project_id',
        'user_id',
        'action',
        'created_at',
        'updated_at',
    ];

    public function details_project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function details_user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
