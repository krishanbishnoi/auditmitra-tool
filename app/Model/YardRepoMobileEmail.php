<?php



namespace App\Model;



use Illuminate\Database\Eloquent\Model;



class YardRepoMobileEmail extends Model

{

    protected $fillable = ['agency_id','mobile_number','email'];

    //

    public function state()

    {

        return $this->hasOne('App\Model\State', 'id','state_id');

    }

}

