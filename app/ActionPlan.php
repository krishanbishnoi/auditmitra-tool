<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActionPlan extends Model
{
    //
    protected $fillable=['date','sheet_id','send_to'];
    public function sub()
    {
        return $this->hasMany('App\ActionPlanSub','action_id','id');
    }
    public function audit()
    {
        return $this->hasOne('App\Audit','id','sheet_id');
    }
    public function answers()
    {
        return $this->hasMany('App\actionPlanAnswer','action_id','id');
    }
}
