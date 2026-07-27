<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Branchable extends Model
{
    protected $fillable = ['branch_id', 'product_id','manager_id','type','bucket','cm_id','acm_id','zcm_id','rcm_id','ncm_id','gph_id'];
    public function product()
    {
        return $this->hasOne('App\Model\Products', 'id','product_id');
    }
    public function Branch()
    {
        return $this->hasOne('App\Model\Branch', 'id','branch_id');
    }
    public function user()
    {
        return $this->hasOne('App\User', 'id','manager_id')->latest('id');
    }
    
    public function rcm()
    {
        return $this->hasOne('App\User', 'id','rcm_id')->latest('id');
    }
    
    public function ncm()
    {
        return $this->hasOne('App\User', 'id','ncm_id')->latest('id');
    }
    
    public function ghead()
    {
        return $this->hasOne('App\User', 'id','gph_id')->latest('id');
    }
    
    public function zcm()
    {
        return $this->hasOne('App\User', 'id','zcm_id')->latest('id');
    }
    
    public function acm()
    {
        return $this->hasOne('App\User', 'id','acm_id')->latest('id');
    }
}
