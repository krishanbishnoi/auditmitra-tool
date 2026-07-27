<?php

namespace App\Model;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class DelaySeconAllocData extends Authenticatable
{
    use Notifiable;
    public $timestamps=true;
    public $table='delay_secon_alloc_datas';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'branch_id','uploaded_by','date','agency_id','bucket_name','allocation','amount'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        //'last_login' => 'datetime',
    ];

    public function branch()
    {
        return $this->hasOne('App\Model\Branch', 'id','branch_id')->with('city','city.state');
    }

    public function agency()
    {
        return $this->hasOne('App\Agency', 'agency_id','agency_id');
    }
    
}
