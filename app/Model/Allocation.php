<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Allocation extends Model
{
    //
    protected $fillable = ['user_id','sheet_id'];

    public function user()
    {
        return $this->hasOne('App\User','id', 'user_id');
    }
    public function sheet()
    {
        return $this->hasOne('App\QmSheet', 'id','sheet_id');
    }
}
