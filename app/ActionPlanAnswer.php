<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActionPlanAnswer extends Model
{
    //
    protected $fillable=['action_id','action_sub_id','answer'];
    public function sub()
    {
        return $this->hasOne('App\ActionPlanSub','id','action_sub_id');
    }
}
