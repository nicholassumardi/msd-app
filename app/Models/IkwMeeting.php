<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IkwMeeting extends Model
{
    use HasFactory, SoftDeletes;
    protected $table      = 'ikw_meetings';
    protected $primaryKey = 'id';
    protected $dates      = ['deleted_at'];
    protected $fillable = [
        'ikw_revision_id',
        'department_id',
        'revision_no',
        'ikw_meeting_no',
        'ikw_code',
        'meeting_date',
        'meeting_duration',
        'revision_status',
    ];

    public function ikwRevision()
    {
        return $this->belongsTo(IKWRevision::class);
    }
}
