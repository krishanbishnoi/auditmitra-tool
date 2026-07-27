<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\Validator;
use App\Model\Allocation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Audit;
use App\Qc;
use App\SavedAudit;
use App\User;
use App\SavedQcAudit;
 


class DummyController extends Controller
{
  public function checkuser($id)
    {
        $data=User::where("id",$id)->first();
        return response()->json(['status'=>200,'message'=>"Success","data"=>$data]);
        
    }    


  public function getdata($id)
  {

   $data = User::find($id);
   return response()->json(['status'=>200,'message'=>"Success",'data'=> $data],200);

  }

}