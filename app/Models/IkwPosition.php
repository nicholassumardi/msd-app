<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IkwPosition extends Model
{
    use HasFactory, SoftDeletes;
    protected $table      = 'ikw_positions';
    protected $primaryKey = 'id';
    protected $dates      = ['deleted_at'];
    protected $fillable = [
        'ikw_revision_id',
        'department_id',
        'revision_no',
        'ikw_code',
        'ikw_position_no',
        'position_call_number',
        'field_operator',
    ];


    public function ikwRevision()
    {
        return $this->belongsTo(IKWRevision::class);
    }
}
