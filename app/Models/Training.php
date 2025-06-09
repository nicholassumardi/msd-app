<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Training extends Model
{
    use HasFactory, SoftDeletes;
    protected $table      = 'trainings';
    protected $primaryKey = 'id';
    protected $dates      = ['deleted_at'];
    protected $fillable   = [
        'no_training',
        'trainee_id',
        'trainer_id',
        'assessor_id',
        'ikw_revision_id',
        'training_plan_date',
        'training_realisation_date',
        'training_duration',
        'ticket_return_date',
        'assessment_plan_date',
        'assessment_realisation_date',
        'assessment_duration',
        'status_fa_print',
        'assessment_result',
        'status',
        'description',
        'status_active',
    ];

    public function trainee()
    {
        return $this->belongsTo(User::class, 'trainee_id', 'id');
    }

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id', 'id');
    }

    public function assessor()
    {
        return $this->belongsTo(User::class, 'assessor_id', 'id');
    }

    public function ikwRevision()
    {
        return $this->belongsTo(IKWRevision::class, 'ikw_revision_id');
    }
}
