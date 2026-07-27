<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;


class IntimationUser extends Model
{
    
    protected $table = 'intimation_users';

    // Define fillable properties for mass assignment
    protected $fillable = [
        'intimation_mail_id',
        'level_3',
        'level_4',
        'level_5',
    ];

    // Define the relationship with IntimationMail
    public function intimationMail()
    {
        return $this->belongsTo(IntimationMail::class, 'intimation_mail_id');
    }

    // Define relationship to User for levels (if necessary)
    public function level3User()
    {
        return $this->belongsTo(User::class, 'level_3');
    }

    public function level4User()
    {
        return $this->belongsTo(User::class, 'level_4');
    }

    public function level5User()
    {
        return $this->belongsTo(User::class, 'level_5');
    }

   
}
