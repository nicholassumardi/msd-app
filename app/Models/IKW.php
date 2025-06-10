<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IKW extends Model
{
    use HasFactory, SoftDeletes;
    protected $table      = 'ikws';
    protected $primaryKey = 'id';
    protected $dates      = ['deleted_at'];
    protected $fillable = [
        'job_task_id',
        'department_id',
        'code',
        'name',
        'total_page',
        'registration_date',
        'print_by_back_office_date',
        'submit_to_department_date',
        'ikw_return_date',
        'ikw_creation_duration',
        'status_document',
        'last_update_date',
        'description',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function jobTask()
    {
        return $this->belongsTo(JobTask::class);
    }

    public function ikwRevision()
    {
        return $this->hasMany(IKWRevision::class, 'ikw_id', 'id');
    }

    public function ikwJobTask()
    {
        return $this->hasMany(IkwJobTask::class, 'ikw_id', 'id');
    }

    public function ikwJobDescription()
    {
        return $this->hasMany(IkwJobDesc::class, 'ikw_id', 'id');
    }
}
