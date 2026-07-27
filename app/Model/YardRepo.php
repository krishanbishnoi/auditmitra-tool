<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class YardRepo extends Model
{
    //
    protected $fillable = ['name','location','branch_id','product_id'];
    public function branch()
    {
        return $this->hasOne('App\Model\branch', 'id','branch_id');
    }
}
