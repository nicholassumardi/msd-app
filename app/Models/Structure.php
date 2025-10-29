<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Structure extends Model
{
    use HasFactory, SoftDeletes;
    protected $table      = 'structures';
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

    public function structurePlot()
    {
        return $this->hasMany(StructurePlot::class);
    }

    public function parent()
    {
        return $this->belongsTo(Structure::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Structure::class, 'parent_id');
    }

    public function structureHistories()
    {
        return $this->hasMany(StructureHistories::class);
    }
}
