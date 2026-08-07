<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Artifact extends Model
{

protected $table = 'audit_closure_artifacts';

    protected $fillable=['sheet_id','parameter_id','sub_parameter_id','file','audit_id','client_id', 'parameter_index', 'brand'];
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
}
