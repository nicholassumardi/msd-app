<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StructureHistories extends Model
{
    use HasFactory, SoftDeletes;

    protected $table      = 'structure_histories';
    protected $primaryKey = 'id';
    protected $dates      = ['deleted_at'];
    protected $fillable   = [
        'structure_id',
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

    public function structure()
    {
        return $this->belongsTo(Structure::class);
    }
}
