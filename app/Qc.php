<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Qc extends Model
{
    protected $fillable=['qm_sheet_id','audit_id','status','feedback','qc_by_id'];
    public function audit()
    {
        return $this->belongsTo('App\Audit','audit_id','id')->with(['qmsheet','product','branch','yard','agency']);
    }
    public function user()
    {
        return $this->belongsTo('App\User','qc_by_id','id');
    }

}
