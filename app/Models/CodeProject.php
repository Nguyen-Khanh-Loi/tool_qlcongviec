<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CodeProject extends Model
{
    use HasFactory;
    protected $table = 'code_projects';
    protected $fillable = [
        'code_id',
        'project_id',
        'code_project',
    ];
    
}
