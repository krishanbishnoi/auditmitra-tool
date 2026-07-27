<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QcResult extends Model
{
    // protected $fillable=['remark'];
    public function reason_type()
    {
        return $this->belongsTo('App\ReasonType','reason_type_id');
    }
    public function reason()
    {
        return $this->belongsTo('App\Reason','reason_id');
    }
    public function audit()
    {
        return $this->belongsTo('App\AuditQc');
    }
    public function parameter_detail()
    {
        return $this->belongsTo('App\QmSheetParameter','parameter_id','id');
    }
    public function sub_parameter_detail()
    {
        return $this->belongsTo('App\QmSheetSubParameter','sub_parameter_id','id');
    }
}
