<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;


class UserHierarchy extends Model
{

    protected $table = 'user_hierarchies';

   
    protected $fillable = [
        'cm_id', 
        'qa_id', 
        'qc_id', 
        'acm_id', 
        'rcm_id', 
        'zcm_id', 
        'ncm_id', 
        'gph_id', 
        'hc_id', 
        'status',
        'created_by'
    ];


   


   
}

