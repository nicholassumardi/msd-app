<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RKI extends Model
{
    use HasFactory, SoftDeletes;
    protected $table      = 'rkis';
    protected $primaryKey = 'id';
    protected $dates      = ['deleted_at'];
    protected $fillable = [
        'position_job_code',
        'ikw_id',
        'training_time',
    ];


    public function ikw()
    {
        return $this->belongsTo(IKW::class);
    }

    public function userJobCode()
    {
        return $this->belongsToMany(UserJobCode::class)
            ->join('job_codes', 'job_codes.id', '=', 'user_job_code.job_code_id')
            ->whereRaw("CONCAT(job_codes.full_code, '-', user_job_code.position_code_structure) = rkis.position_job_code")
            ->select('user_job_code.*');
    }
}
