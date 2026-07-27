<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class BeatPlans extends Model
{
    protected $fillable = ['name','user_id'];
    public function sub()
    {
        return $this->hasMany('App\Model\BeatPlanSubParts', 'beat_id','id')->with('branch');
    }
}
