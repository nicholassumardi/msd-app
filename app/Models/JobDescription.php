<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobDescription extends Model
{
    use HasFactory, SoftDeletes;
    protected $table      = 'job_descriptions';
    protected $primaryKey = 'id';
    protected $dates      = ['deleted_at'];
    protected $fillable   = [
        'job_code_id',
        'code',
        'description',
    ];


    public function jobCode()
    {
        return $this->belongsTo(JobCode::class);
    }
}
