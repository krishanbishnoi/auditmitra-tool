<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SavedAudit extends Model
{
    //
    protected $fillable=['id','audit_id','status','client_id'];
}
   