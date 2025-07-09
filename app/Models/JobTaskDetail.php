<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobTaskDetail extends Model
{
    use HasFactory, SoftDeletes;
    protected $table      = 'job_task_details';
    protected $primaryKey = 'id';
    protected $dates      = ['deleted_at'];
    protected $fillable   = [
        'user_structure_mapping_id',
        'ikw_id',
        'job_task_id',
    ];


    public function userStructureMapping()
    {
        return $this->belongsTo(UserStructureMapping::class);
    }

    public function jobTask()
    {
        return $this->belongsTo(JobTask::class);
    }

    public function ikw()
    {
        return $this->belongsTo(IKW::class);
    }
}
