<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserStructureMappingHistories extends Model
{
    use HasFactory, SoftDeletes;

    protected $table      = 'usm_histories';
    protected $primaryKey = 'id';
    protected $dates      = ['deleted_at'];
    protected $fillable   = [
        'user_structure_mapping_id',
        'revision_no',
        'valid_date',
        'updated_date',
        'authorized_date',
        'approval_date',
        'acknowledged_date',
        'created_date',
        'distribution_date',
        'withdrawal_date',
        'logs',

    ];

    public function userStructureMapping()
    {
        return $this->belongsTo(UserStructureMapping::class);
    }
}
