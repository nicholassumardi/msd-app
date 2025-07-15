<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class JobDescription extends Model
{
    use HasFactory, SoftDeletes, Searchable;
    protected $table      = 'job_descriptions';
    protected $primaryKey = 'id';
    protected $dates      = ['deleted_at'];
    protected $fillable   = [
        'code',
        'description',
    ];

    public function jobDescDetails()
    {
        return $this->hasMany(JobDescDetail::class, 'job_description_id', 'id');
    }

    public function jobTask()
    {
        return $this->hasMany(JobTask::class);
    }
}
