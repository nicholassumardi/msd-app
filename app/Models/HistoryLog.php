<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'modified_at',
        'table_name',
    ];

    public function userHistory()
    {
        return $this->hasMany(UserHistory::class);
    }
}
