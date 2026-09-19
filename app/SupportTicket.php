<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use HasFactory;
    protected $fillable = [
        'support_id','help_topic', 'issue_type', 'subject', 'priority', 'description','status','closure_feedback'
    ];
}
