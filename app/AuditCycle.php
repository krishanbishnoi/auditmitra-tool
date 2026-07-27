<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AuditCycle extends Model
{
    //
    public $timestamps=true;
    protected $fillable=['name','created_by', 'client_id'];
    
}
