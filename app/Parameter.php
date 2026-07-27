<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parameter extends Model
{
    protected $fillable = [
        'name','client_id'
    ];

    public function subParameters()
    {
        return $this->hasMany(SubParameter::class);
    }
    
} 