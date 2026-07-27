<?php

namespace App\Model;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AcrReportData extends Authenticatable
{
    use Notifiable;
    public $timestamps=true;
    public $table='acr_report_datas';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'branch_id','agency_id','month','loan_acc_no','product_flag','product_group','p_out_orig','branch','b_state','b_city','region','agency_name','date_stamp','recovery_npa_stage','collection_manager','input_1','input_2','input_3','input_4','input_5','input_6','input_7',
        'input_8','input_9','input_10','agent_id','agent_name'
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
    
    public function product()
    {
        return $this->hasOne('App\Model\Products', 'name','product_group');
    }

    public function agency()
    {
        return $this->hasOne('App\Agency', 'agency_id','agency_id');
    }
    
}
