<?php

namespace App\Model;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class CashDepositionData extends Authenticatable
{
    use Notifiable;
    public $timestamps=true;
    public $table='cash_deposition_datas';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'branch_id','agency_id','agent_name','agent_id','receipt_no','receipt_date','month','reference_no','product_1','total_rec_amt','deposite_date','bb_pay_batch_date','delay_deposite_bucket','date','created_at','updated_at','uploaded_by'
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
