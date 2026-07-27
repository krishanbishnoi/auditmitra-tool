<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;


class AuditAllocation extends Model
{
    protected $table ='audit_allocation';

    protected $fillable = 
        [  
            'id',
            'user_id',
            'final_agency_name',
            'agency_id',
            'agency_code',
            'type_of_agency',
            'sub_product_id',
            'sub_product',
            'product_id',
            'product',
            'location',
            'city_id',
            'state',
            'state_id',
            'region',
            'region_id',
            'process_review_agency',
            'process_review_agency_email',
            'process_review_agency_id',
            'process_review_period',
            'audit_cycle_id',
            'agency_address',
            'contact',
            'agency_email',
            'status',
            'assign_status',
            'client_id',
        ];
    
   
}
