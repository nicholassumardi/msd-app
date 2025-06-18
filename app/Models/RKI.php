<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class RKI extends Model
{
    use HasFactory, SoftDeletes;
    protected $table      = 'rkis';
    protected $primaryKey = 'id';
    protected $dates      = ['deleted_at'];
    protected $fillable = [
        // 'position_job_code',
        'user_structure_mapping_id',
        'ikw_id',
        'training_time',
    ];


    public function ikw()
    {
        return $this->belongsTo(IKW::class);
    }

    public function userStructureMapping()
    {
        return $this->belongsTo(UserStructureMapping::class, 'user_structure_mapping_id', 'id');
    }

    // public function userJobCode()
    // {
    //     $positionCode = Str::after($this->position_job_code, '-');
    //     return $this->hasMany(UserJobCode::class, 'position_code_structure', $positionCode);
    // }
}
