<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmploymentDurationClassification extends Model
{
    use HasFactory, SoftDeletes;

    protected $table      = 'employment_duration_classifications';
    protected $primaryKey = 'id';
    protected $dates      = ['deleted_at'];
    protected $fillable   = [
        'rule',
        'label',
    ];
}
