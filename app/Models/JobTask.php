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
        'job_code_id',
        'description',
    ];

    public function jobCode()
    {
        return $this->belongsTo(JobCode::class);
    }
}
