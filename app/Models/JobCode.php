<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobCode extends Model
{
    use HasFactory, SoftDeletes;
    protected $table      = 'job_codes';
    protected $primaryKey = 'id';
    protected $dates      = ['deleted_at'];
    protected $fillable   = [
        'category_id',
        'org_level',
        'job_family',
        'code',
        'full_code',
        'position',
        'level',
    ];


    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
