<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Tasks;
use App\Models\Project;
use App\Models\DepartmentUser;

class Card extends Model
{
    use HasFactory;

    public function department()
    {
        return $this->hasMany(DepartmentUser::class, 'department_id');
    }
}
