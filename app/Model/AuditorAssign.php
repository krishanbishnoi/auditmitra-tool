<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;


class AuditorAssign extends Model
{
    
    protected $table = 'auditor_assigns';

    protected $fillable = 
        [  
            'user_id',
            'allocation_id',
            'final_agency_name',
            'agency_id',
            'agency_code',
            'type_of_agency',
            'sub_product',
            'sub_product_id',
            'product',
            'product_id',
            'location',
            'state',
            'region',
            'process_review_agency',
            'process_review_agency_id',
            'process_review_agency_email',
            'process_review_period',
            'audit_cycle_id',
            'agency_address',
            'contact',
            'agency_email',
            'auditor_name',
            'auditor_email',
            'audit_date',
            'status',
            'client_id',
        ];
    
   
}
