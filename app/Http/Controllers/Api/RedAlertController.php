<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Response;
use App\RedAlert;
use App\RedAlertAnswer;
use App\Yard;
use App\Agency;
use App\User;
use App\Model\Branchable;
use App\Model\Branch;
use Storage;
use Mail;
use App\Audit;
use App\AuditResult;
use App\BranchRepo;
use App\AgencyRepo;

class RedAlertController extends Controller
{
    public function storeRedAlert(Request $request)
    {
       if(!$request->header('Authorizations') || $request->header('Authorizations') == "") {
           $data=array('status'=>0,'message'=>'Authorizations key is required in api headers.','data' => array());
           return response(json_encode($data), 200); 
        }
        $getUser=User::select('id','auth_key')->where('auth_key',$request->header('Authorizations'))->first();
        if(!$getUser) {
            $data=array('status'=>0,'message'=>'User not found','data' => array());
           return response(json_encode($data), 200); 
        }
        $validator = Validator::make($request->all(), [
        ]);
        if($validator->fails()) {
            $data=array('status'=>0,'message'=>'Validation Errors','data' => $validator->errors());
            return response(json_encode($data), 200);
        }
        $ids=[];
        $insert=[];
        $idData=json_decode($request->ids);
        $data=[];
        // dd($ids);
        foreach($idData as $k=>$value){
            $name='file'.$value;
            if($request->has($name) && $request->file($name)!='undefined'){
                // $store=Storage::disk('public_uploads')->put('test', $request->file);
                $path1 = $request->file($name)->store('public'); 
                $store=storage_path('app').'/'.$path1;
            }
            else{
                $store=null;
            }
            $insert=[
                'sheet_id'=>$request['sheet_id'.$value],
                'parameter_id'=>$request['parameter_id'.$value],
                'sub_parameter_id'=>$request['id'.$value],
                'message'=>$request['msg'.$value],
                'file'=>$store,
                'type'=>$request->type,
                'type_id'=>$request->typeid,
                'lob'=>$request['lob'.$value],
                'audit_id'=>$request->audit_id
            ];
            // dd($insert);
            $data[]=RedAlert::create($insert);
        }
        
        if(count($data)>0){
            $data=array('status'=>1,'message'=>'Red Alert Saved Successfully','data' => array());
            return response(json_encode($data), 200);
           
            // return response()->json(['status'=>true,'msg'=>'red alert save']);
        }
        else{
            $data=array('status'=>0,'message'=>'Red Alert Not Saved','data' => array());
            return response(json_encode($data), 200);

            // return response()->json(['status'=>true,'msg'=>'red alert not save']);
        }
    }
}
