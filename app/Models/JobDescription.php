<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class JobDescription extends Model
{
    use HasFactory, SoftDeletes, Searchable;
    protected $table      = 'job_descriptions';
    protected $primaryKey = 'id';
    protected $dates      = ['deleted_at'];
    protected $fillable   = [
        'job_code_id',
        'ikw_id',
        'code',
        'description',
    ];


    public function jobCode()
    {
        return $this->belongsTo(JobCode::class);
    }

    public function ikwJobDesc()
    {
        return $this->hasMany(IkwJobDesc::class, 'job_description_id', 'id');
    }
}
