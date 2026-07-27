<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RedAlert extends Model
{
    //
    protected $fillable=['sheet_id','parameter_id','sub_parameter_id','message','audit_id'];
    public function sheet()
    {
        return $this->hasOne('App\QmSheet','id','sheet_id');
    }
    public function parameter()
    {
        return $this->hasOne('App\QmSheetParameter','id','parameter_id');
    }
    public function subParameter()
    {
        return $this->hasOne('App\QmSheetSubParameter','id','sub_parameter_id');
    }
    public function answer()
    {
        return $this->hasOne('App\RedAlertAnswer','red_alert_id','id');
    }
    public function audit()
    {
        return $this->hasOne('App\Audit','id','audit_id');
    }
}
