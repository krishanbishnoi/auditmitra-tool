<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;

class ClientModuleAllocation extends Model
{
    use HasFactory;

    // Optional if using default naming conventions
    protected $table = 'client_module_allocations';

    protected $fillable = [
        'module_id',
        'client_id',
    ];

    public $timestamps = true;

    // Optional: Define relationships if needed
    public function module()
    {
        return $this->belongsTo(ModulePermission::class, 'module_id');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
