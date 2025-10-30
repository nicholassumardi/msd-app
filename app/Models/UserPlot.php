<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPlot extends Model
{
    use HasFactory, SoftDeletes;
    protected $table      = 'user_plots';
    protected $primaryKey = 'id';
    protected $dates      = ['deleted_at'];
    protected $fillable   = [
        'structure_plot_id',
        'user_id',
        'parent_id',
        'id_staff',
        'employee_type',
        'assign_date',
        'reassign_date',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function structurePlot()
    {
        return $this->belongsTo(StructurePlot::class);
    }

    public function UserPlotRequest()
    {
        return $this->hasOne(UserPlotRequest::class);
    }

    public function parent()
    {
        return $this->belongsTo(UserPlot::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(UserPlot::class, 'parent_id');
    }
}
