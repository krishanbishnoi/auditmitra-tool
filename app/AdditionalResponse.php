<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdditionalResponse extends Model
{
    // Specify the table name since it's not the default Laravel naming convention
    protected $table = 'additionalResponse';

    // Allow mass assignment for these fields
    protected $fillable = [
        'audit_id',
        'parameter_id',
        'sub_parameter_id',
        'questions_id',
        'questions',
        'remark',
    ];
}
