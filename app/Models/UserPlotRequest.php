<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPlotRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $table      = 'structure_plot_requests';
    protected $primaryKey = 'id';
    protected $dates      = ['deleted_at'];
    protected $fillable   = [
        'user_plot_id',
        'group',
        'description',
        'request_date',
        'status_slot',
    ];

    public function userPlot()
    {
        return $this->belongsTo(UserPlot::class);
    }
}
