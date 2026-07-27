<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MappingMaster extends Model
{
    protected $table = 'mapping_master';

    protected $fillable = [
        'location',
        'campus_type',
        'pillar',
        'touch_points',
        'checkpoint_parameters'
    ];
}