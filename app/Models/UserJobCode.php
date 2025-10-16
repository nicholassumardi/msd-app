<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserJobCode extends Model
{
    use HasFactory, SoftDeletes;
    protected $table      = 'user_job_codes';
    protected $primaryKey = 'id';
    protected $dates      = ['deleted_at'];
    protected $fillable   = [
        'user_id',
        'parent_id',
        'job_code_id',
        'user_structure_mapping_id',
        'id_structure',
        'id_staff',
        'position_code_structure',
        'group',
        'employee_type',
        'assign_date',
        'reassign_date',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jobCode()
    {
        return $this->belongsTo(JobCode::class);
    }

    public function userStructureMapping()
    {
        return $this->belongsTo(UserStructureMapping::class);
    }

    public function UserStructureMappingRequest()
    {
        return $this->hasOne(UserStructureMappingRequest::class);
    }

    public function parent()
    {
        return $this->belongsTo(UserJobCode::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(UserJobCode::class, 'parent_id');
    }
}
