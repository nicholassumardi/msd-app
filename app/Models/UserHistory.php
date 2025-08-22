<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserHistory extends Model
{
    use HasFactory;
    protected $fillable = [
        'history_log_id',
        'name',
        'company_id',
        'department_id',
        'date_of_birth',
        'identity_card',
        'gender',
        'religion',
        'email',
        'photo',
        'education',
        'marital_status',
        'address',
        'phone',
        'status',
        'employee_type',
        'section',
        'position_code',
        'status_twiji',
        'schedule_type',
        'employee_number',
        'join_date',
        'leave_date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function historyLog()
    {
        return $this->belongsTo(HistoryLog::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function userJobCode()
    {
        return $this->hasMany(UserJobCode::class)->with('jobCode');
    }
}
