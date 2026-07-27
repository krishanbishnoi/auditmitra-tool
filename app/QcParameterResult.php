<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QcParameterResult extends Model
{
    public function qa_qtl_detail()
    {
        return $this->belongsTo('App\User','audited_by_id','id');
    }
    public function parameter_detail()
    {
        return $this->belongsTo('App\QmSheetParameter','parameter_id','id');
    }
    public function result()
    {
        return $this->hasMany('App\QcResult','parameter_id','parameter_id');
    }
    public function result2()
    {
        return $this->hasMany('App\QcResult','audit_id','audit_id');
    }
    public function finalResult()
    {
        return $this->result->merge($this->result2);
    }
}
