<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserServiceYear extends Model
{
    use HasFactory, SoftDeletes;


    protected $table      = 'user_service_years';
    protected $primaryKey = 'id';
    protected $dates      = ['deleted_at'];
    protected $fillable   = [
        'user_id',
        'join_date',
        'leave_date',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
