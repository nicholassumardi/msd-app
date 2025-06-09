<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserEmployeeNumber extends Model
{
    use HasFactory, SoftDeletes;


    protected $table      = 'user_employee_numbers';
    protected $primaryKey = 'id';
    protected $dates      = ['deleted_at'];
    protected $fillable   = [
        'user_id',
        'employee_number',
        'registry_date',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
