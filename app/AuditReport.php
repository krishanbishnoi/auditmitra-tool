<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditReport extends Model
{
    use HasFactory;
    protected $guarded = [];
    
    public function audit()
    {
        return $this->hasOne('App\Audit','id','audit_id');
    }
}
