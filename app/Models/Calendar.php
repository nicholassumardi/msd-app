<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Calendar extends Model
{
    use HasFactory, SoftDeletes;
    protected $table      = 'calendars';
    protected $primaryKey = 'id';
    protected $dates      = ['deleted_at'];
    protected $fillable   = [
        'title',
        'link',
        'start_date',
        'end_date',
        'all_day',
    ];
}
