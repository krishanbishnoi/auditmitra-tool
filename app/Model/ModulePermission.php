<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModulePermission extends Model
{
    use HasFactory;

    // Table name (optional, only if it differs from 'module_permissions')
    protected $connection='mysql';
    protected $table = 'module_permissions';

    // The attributes that are mass assignable
    protected $fillable = [
        'module_name',
    ];

    // Optionally you can define timestamps if needed (but default true)
    public $timestamps = true;
}
