<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ProductUser extends Model
{
    protected $fillable = ['user_id', 'product_id','type'];
    public function user()
    {
        return $this->hasOne('App\User', 'id','user_id');
    }
    public function product()
    {
        return $this->hasOne('App\Model\Products', 'id','product_id');
    }
}
