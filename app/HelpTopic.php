<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelpTopic extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // A Help Topic can have many Issue Types
    public function issueTypes()
    {
        return $this->hasMany(IssueType::class);
    }
}
