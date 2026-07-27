<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OldScore extends Model
{
    public $timestamps=true;
    public $table='old_scores';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'branch_id','previous_1','previous_2','previous_3','previous_4','current_score','uploaded_by','product_id','type'
    ];
    
}
