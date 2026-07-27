<?php





namespace App;





use Illuminate\Database\Eloquent\Model;





class TempArtifact extends Model


{


    protected $fillable=['sheet_id','parameter_id','sub_parameter_id','file','temp_audit_id','client_id'];

    protected $table = 'temp_artifact';


    public function sheet()


    {


        return $this->hasOne('App\QmSheet','id','sheet_id');


    }


    public function parameter()


    {


        return $this->hasOne('App\QmSheetParameter','id','parameter_id');


    }


    public function subParameter()


    {


        return $this->hasOne('App\QmSheetSubParameter','id','sub_parameter_id');


    }


}


