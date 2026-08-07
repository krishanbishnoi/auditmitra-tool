<?php

namespace App;

use App\Model\AuditAllocation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use App\Model\ClosureAudit;

class Client extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function user()
    {
        return $this->hasOne('App\User', 'id', 'client_id');
    }

    public function auditAllocations()
    {
        return $this->hasMany(AuditAllocation::class, 'client_id', 'client_id');
    }
    public function audits()
    {
        return $this->hasMany(Audit::class, 'client_id', 'client_id');
    }

    public function closureArtifacts()
    {
        return $this->hasManyThrough(
            // ClosureAudit::class,
            Audit::class,
            'client_id',
            'audit_id',
            'id',
            'id'
        );
    }
}
