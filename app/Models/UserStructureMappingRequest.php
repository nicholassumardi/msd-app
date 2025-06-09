<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserStructureMappingRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $table      = 'user_structure_mapping_requests';
    protected $primaryKey = 'id';
    protected $dates      = ['deleted_at'];
    protected $fillable   = [
        'user_job_code_id',
        'group',
        'description',
        'request_date',
        'status_slot',
    ];

    public function userJobCode()
    {
        return $this->belongsTo(UserJobCode::class);
    }
}
