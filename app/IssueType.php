<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssueType extends Model
{ 
    use HasFactory;

    protected $fillable = ['name', 'help_topic_id'];

    // An Issue Type belongs to one Help Topic
    public function helpTopic()
    {
        return $this->belongsTo(HelpTopic::class);
    }
}
