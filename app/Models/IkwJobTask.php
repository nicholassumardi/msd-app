<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IkwJobTask extends Model
{
    use HasFactory, SoftDeletes;

    protected $table      = 'ikw_job_task';
    protected $primaryKey = 'id';
    protected $dates      = ['deleted_at'];
    protected $fillable   = [
        'ikw_id',
        'job_task_id',
    ];

    public function jobTask()
    {
        return $this->belongsTo(JobTask::class);
    }
    
    public function ikw()
    {
        return $this->belongsTo(IKW::class);
    }
}
