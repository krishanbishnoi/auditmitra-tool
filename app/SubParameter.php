<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubParameter extends Model
{
    protected $table = 'sub_parameters'; // or 'qm_sheet_sub_parameters' if that's your table name

    protected $fillable = [
        'parameter_id',
        'name',
        'client_id'
    ];

    public function parameter()
    {
        return $this->belongsTo(Parameter::class);
    }
}
 