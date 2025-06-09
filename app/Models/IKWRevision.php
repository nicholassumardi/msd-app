<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IKWRevision extends Model
{
    use HasFactory, SoftDeletes;
    protected $table      = 'ikw_revisions';
    protected $primaryKey = 'id';
    protected $dates      = ['deleted_at'];
    protected $fillable = [
        'ikw_id',
        'revision_no',
        'ikw_code',
        'reason',
        'process_status',
        'ikw_fix_status',
        'confirmation',
        'change_description',
        'submission_no',
        'submission_received_date',
        'submission_mr_date',
        'backoffice_return_date',
        'revision_status',
        'print_date',
        'handover_date',
        'signature_mr_date',
        'distribution_date',
        'document_return_date',
        'document_disposal_date',
        'document_location_description',
        'revision_description',
        'status_check',
    ];


    public function ikw()
    {
        return $this->belongsTo(IKW::class, 'ikw_id');
    }

    public function ikwMeeting()
    {
        return $this->hasMany(IkwMeeting::class, 'ikw_revision_id', 'id');
    }

    public function ikwPosition()
    {
        return $this->hasMany(IkwPosition::class, 'ikw_revision_id', 'id');
    }

    public function training()
    {
        return $this->hasMany(Training::class, 'ikw_revision_id', 'id');
    }
}
