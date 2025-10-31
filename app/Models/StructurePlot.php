<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StructurePlot extends Model
{
    use HasFactory, SoftDeletes;
    protected $table      = 'structure_plots';
    protected $primaryKey = 'id';
    protected $dates      = ['deleted_at'];
    protected $fillable   = [
        'structure_id',
        'parent_id',
        'id_structure',
        'suffix',
        'position_code_structure',
        'group',
    ];


    public function structure()
    {
        return $this->belongsTo(Structure::class);
    }

    public function userPlot()
    {
        return $this->hasMany(UserPlot::class);
    }

    public function userPlotRequest()
    {
        return $this->hasOne(UserPlotRequest::class);
    }

    public function parent()
    {
        return $this->belongsTo(StructurePlot::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(StructurePlot::class, 'parent_id');
    }
}
