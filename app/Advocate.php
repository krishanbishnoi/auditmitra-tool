<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Advocate extends Model
{
    protected $table = 'advocates';

    protected $fillable = [
        'name',
        'is_active',
        'client_id'
    ];
}
