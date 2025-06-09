<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class UserStructureMapping extends Model
{
    use HasFactory, SoftDeletes, Searchable;
    protected $table      = 'user_structure_mappings';
    protected $primaryKey = 'id';
    protected $dates      = ['deleted_at'];
    protected $fillable   = [
        'department_id',
        'job_code_id',
        'position_code_structure',
        'parent_id',
        'name',
        'quota',
        'structure_type',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function jobCode()
    {
        return $this->belongsTo(JobCode::class);
    }

    public function userJobCode()
    {
        return $this->hasMany(UserJobCode::class);
    }

    public function parent()
    {
        return $this->belongsTo(UserStructureMapping::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(UserStructureMapping::class, 'parent_id');
    }

    public function userStructureMappingHistories()
    {
        return $this->hasMany(UserStructureMappingHistories::class);
    }
}
