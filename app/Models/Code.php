<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CodeProject;

class Code extends Model
{
    use HasFactory;
    protected $table = 'codes';
    protected $fillable = [
        'title',
        'code',
    ];

    public function codeProjects()
    {
        return $this->hasMany(CodeProject::class, 'code_id');
    }
}
