<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IkwJobDesc extends Model
{
    use HasFactory, SoftDeletes;

    protected $table      = 'ikw_job_desc';
    protected $primaryKey = 'id';
    protected $dates      = ['deleted_at'];
    protected $fillable   = [
        'ikw_id',
        'job_description_id',
    ];

    public function jobDescription()
    {
        return $this->belongsTo(JobDescription::class);
    }

    public function ikw()
    {
        return $this->belongsTo(IKW::class);
    }
}
