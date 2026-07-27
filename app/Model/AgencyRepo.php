<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AgencyRepo extends Model
{
    //
    protected $fillable = ['name','location','branch_id','product_id'];
    public function branch()
    {
        return $this->hasOne('App\Model\Branch', 'id','branch_id');
    }
}
