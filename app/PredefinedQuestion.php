<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PredefinedQuestion extends Model
{
    protected $connection='mysql';
    protected $fillable = ['question', 'answer', 'role'];
    protected $table = 'predefined_questions';

   

    // Scope to filter questions by role
    public function scopeForRole($query, $role)
    {
        return $query->where('role', $role);
    }
}
