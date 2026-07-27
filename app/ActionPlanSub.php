<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActionPlanSub extends Model
{
    //
    protected $fillable=['question','artifact','action_id'];
    public function actionPlanAnswer()
    {
        return $this->hasOne('App\ActionPlanAnswer','action_sub_id','id');
    }
}
