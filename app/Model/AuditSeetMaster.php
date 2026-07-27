<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditSeetMaster extends Model
{
    use HasFactory;

    protected $table = 'audit_sheet_masters';

    protected $fillable = [
        'type',
        'name',
        'possible_values',
        'show_add_button',
        'weight',
        'is_active',
    ];
}