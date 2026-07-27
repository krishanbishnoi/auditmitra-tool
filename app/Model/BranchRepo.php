<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class BranchRepo extends Model
{
    //
    protected $fillable = ['branch_id', 'name', 'product_id', 'location', 'branch_repo_id'];

 
    public function branch()
    {
        return $this->hasOne('App\Model\Branch', 'id','branch_id');
    }
}
