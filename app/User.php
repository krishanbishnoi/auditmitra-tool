<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{

    use Notifiable,HasRoles;

   
    // protected $connection = 'mysql'; 
    protected $fillable = [
        'user_role_id', 'name', 'email', 'password', 'employee_id', 'mobile', 'status', 'team', 'audit_agency' , 'logo','client_id','created_by','audit_agency_id','levels','last_login_at','cycle_start_date','cycle_end_date', 'is_legal', 'is_compliance'
    ];
   

    protected $hidden = [

        'password', 'remember_token'

    ];



    /**

     * The attributes that should be cast to native types.

     *

     * @var array

     */

    protected $casts = [

        'email_verified_at' => 'datetime',
        'levels' => 'array',

    ];

}

