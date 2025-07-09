<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobTask extends Model
{
    use HasFactory, SoftDeletes;

    protected $table      = 'job_tasks';
    protected $primaryKey = 'id';
    protected $dates      = ['deleted_at'];
    protected $fillable   = [
        'job_description_id',
        'description',
    ];

    public function userStructureMapping()
    {
        return $this->belongsTo(UserStructureMapping::class);
    }

    public function jobTaskDetail()
    {
        return $this->hasMany(JobTaskDetail::class, 'job_task_id', 'id');
    }
}
