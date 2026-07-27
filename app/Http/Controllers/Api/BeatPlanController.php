<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Http\Controllers\Controller;
use App\Model\BeatPlans;
use App\Model\BeatPlanSubParts;
use App\Model\Products;
use App\Model\Branch;
use App\User;


class BeatPlanController extends Controller
{
    public function list(Request $request)
    {
        $output = array();
        if(!$request->header('Authorizations') || $request->header('Authorizations') == "") {
           $data=array('status'=>0,'message'=>'Authorizations key is required in api headers.','data' => array());
           return response(json_encode($data), 200); 
        }
         $user=User::select('id','auth_key')->where('auth_key',$request->header('Authorizations'))->first();
        if(!$user) {
            $data=array('status'=>0,'message'=>'User not found','data' => array());
           return response(json_encode($data), 200); 
        }
        else{
            $beatplan=BeatPlans::with('sub')->where('user_id', $user->id)->get();
            $output = array();
            if(count($beatplan)>0){
                foreach ($beatplan as $key => $value) {
                    // $data[] = $value; 
                    $output[] = $value;
                }

                $response=array('status'=>1,'message'=>'Beat Plan List',
                                'data' => $output);       
                return response(json_encode($response), 200);    

            }else{
                $response=array('status'=>0,'message'=>'No Beat Plan Found',
                                'data' => array());       
                return response(json_encode($response), 200);    
             
 
    	}
            }

    }



    public function beatPlanFormField(Request $request){

        if(!$request->header('Authorizations') || $request->header('Authorizations') == "") {
           $data=array('status'=>0,'message'=>'Authorizations key is required in api headers.','data' => array());
           return response(json_encode($data), 200); 
        }
         $user=User::select('id','auth_key')->where('auth_key',$request->header('Authorizations'))->first();
        if(!$user) {
            $data=array('status'=>0,'message'=>'User not found','data' => array());
           return response(json_encode($data), 200); 
        }
        
        $branch=Branch::get(['id', 'name']);
        $user=User::role('Quality Auditor')->get();
        $product = Products::get(['id', 'name']);
        $outputJson = ['branch'=>$branch, 'product'=>$product, 'user'=>$user];
        $response=array('status'=>1,'message'=>'Beat plan form field', 'data'=>$outputJson);
        return response(json_encode($response), 200);
    }





    public function createBeatPlan(Request $request){
        if(!$request->header('Authorizations') || $request->header('Authorizations') == "") {
           $data=array('status'=>0,'message'=>'Authorizations key is required in api headers.','data' => array());
           return response(json_encode($data), 200); 
        }
         $user=User::select('id','auth_key')->where('auth_key',$request->header('Authorizations'))->first();
        if(!$user) {
            $data=array('status'=>0,'message'=>'User not found','data' => array());
           return response(json_encode($data), 200); 
        }

         $validator = Validator::make($request->all(), [
            // 'company_id' => 'required',
            // 'date' => 'required',
            // 'branch_id' => 'required',
            'name' => 'required',
            // 'user_id'=>'required',
            'subs'=>'required'
        ]);
        if($validator->fails())
        {
            $data = array('status'=>0, 'message'=>'Validation Error', 'data'=>$validator->errors());
            return response(json_encode($data),200);
        }else
        {
            // dd($request->all());
            // for check beatplan is already create between this date range
            foreach($request->subs as $key => $value){
            $check_exists = BeatPlanSubParts::whereDate('date','>=', $value['date'])->whereDate('to_date','<=',$value['to_date'])->get();
                if(count($check_exists) > 0){
                    $data = array('status'=>0, 'message'=>'Validation Error', 'data'=>$validator->errors()->add('error', 'Beat Plan already created between for this date interval'));
                    return response(json_encode($data),200);
                }
                if(empty($value['product'])){
                    $data = array('status'=>0, 'message'=>'Validation Error', 'data'=>$validator->errors()->add('error', 'Please Select a Product'));
                    return response(json_encode($data),200);
                }
            }
            $new=BeatPlans::create(['name'=>$request->name,'user_id'=>$user->id]);
            $data=[];
            foreach ($request->subs as $key => $value) {
                $data[]=[
                    'branch_id'=>$value['branch_id'],
                    'date'=>$value['date'],
                    'to_date'=>$value['to_date'],
                    'agencies'=>$value['agencies'],
                    'branch_repo'=>$value['branch_repo'],
                    'agency_repo'=>$value['agency_repo'],
                    'yard'=>$value['yard'],
                    'yard_repo'=>$value['yard_repo'],
                    'product'=>json_encode($value['product']),
                    'collection_manager'=>$value['collection_manager'],
                    'description'=>$value['description'],
                    'beat_id'=>$new->id,
                ];
 
            }
            $new_rc = BeatPlanSubParts::insert($data);
            if($new_rc){
                $data=BeatPlanSubParts::with('branch.branchable')->where('beat_id',$new->id)->get();
                foreach($data as $item){
                    // $emails=[];
                    // $emails=$item->branch->branchable->pluck('user.email')->toArray();
                    // Mail::send('emails.beatPlan', ['data' => $item], function ($m) use ($emails){
                    //     $m->to($emails)->subject('Beat plan');
                    // });
                        //$m->from('hello@app.com', 'Your Application');
                    }
                } 

            }
            // return redirect('beat_plan')->with('success', 'Beat Plan created successfully');
            $outputJson = ['beat_plan'=>$new, 'beat_plan_subs'=>$data];
            $response=array('status'=>1,'message'=>'Beat plan Created Successfully', 'data'=>$outputJson);
            return response(json_encode($response), 200);
    }



    public function editBeatPlan(Request $request){
        if(!$request->header('Authorizations') || $request->header('Authorizations') == "") {
           $data=array('status'=>0,'message'=>'Authorizations key is required in api headers.','data' => array());
           return response(json_encode($data), 200); 
        }
         $user=User::select('id','auth_key')->where('auth_key',$request->header('Authorizations'))->first();
        if(!$user) {
            $data=array('status'=>0,'message'=>'User not found','data' => array());
           return response(json_encode($data), 200); 
        }
         $validator = Validator::make($request->all(), [
            // 'company_id' => 'required',
            // 'date' => 'required',
            // 'branch_id' => 'required',
            'id' => 'required'
        ]);
        if($validator->fails())
        {
            $data = array('status'=>0, 'message'=>'Beat Plan Id required', 'data'=>$validator->errors());
            return response(json_encode($data),200);
        }
        // $data = BeatPlans::with('sub')->find(Crypt::decrypt($id));
        $data = BeatPlans::with('sub')->find($request->id);
        $branch=Branch::get(['id', 'name']);
        //$agencies=Agency::get(['id', 'name']);
        $user=User::role('Quality Auditor')->get(['id', 'name']);
        $product = Products::get(['id', 'name']);


        $outputJson = ["data"=>$data,"branch"=>$branch,"product"=>$product, "user"=>$user];
        $response=array('status'=>1,'message'=>'Beat plan Edit data', 'data'=>$outputJson);
        return response(json_encode($response), 200);

    }

    public function updateBeatPlan(Request $request){
        if(!$request->header('Authorizations') || $request->header('Authorizations') == "") {
           $data=array('status'=>0,'message'=>'Authorizations key is required in api headers.','data' => array());
           return response(json_encode($data), 200); 
        }
         $user=User::select('id','auth_key')->where('auth_key',$request->header('Authorizations'))->first();
        if(!$user) {
            $data=array('status'=>0,'message'=>'User not found','data' => array());
           return response(json_encode($data), 200); 
        }
         // dd($request->all());
        $validator = Validator::make($request->all(), [
            // 'company_id' => 'required',
            // 'date' => 'required',
            // 'branch_id' => 'required',
            'name' => 'required',
            'id'=>'required',
            'subs'=>'required'
        ]);
        if($validator->fails())
        {
            $data = array('status'=>0, 'message'=>'Validation Error', 'data'=>$validator->errors());
            return response(json_encode($data),200);

        }else
        {
            // dd($request->all());
            // $id=Crypt::decrypt($id);
            $id=$request->id;
            $new=BeatPlans::where('id',$id)->update(['name'=>$request->name]);
            $data=[];
            foreach ($request->subs as $key => $value) {
                BeatPlanSubParts::updateOrCreate(['beat_id'=>$id,'id'=>$value['sub_id']],[
                    'branch_id'=>$value['branch_id'],
                    'date'=>$value['date'],
                    'to_date'=>$value['to_date'],
                    'agencies'=>$value['agencies'],
                    'branch_repo'=>$value['branch_repo'],
                    'agency_repo'=>$value['agency_repo'],
                    'yard'=>$value['yard'],
                    'yard_repo'=>$value['yard_repo'],
                    'product'=>json_encode($value['product']),
                    'collection_manager'=>$value['collection_manager'],
                    'description'=>$value['description'],
                    'beat_id'=>$id,
                    // 'user_id'=>Auth::user()->id,

                ]);

            }
            // dd($data);
            // $new_rc = BeatPlanSubParts::insert($data);
            // return redirect('beat_plan')->with('success', 'Beat Plan update successfully');
            $data = array('status'=>1, 'message'=>'Beat Plan updated successfully', 'data'=>array());
            return response(json_encode($data),200);
        }

    }


    public function deleteBeatPlan(Request $request){
        if(!$request->header('Authorizations') || $request->header('Authorizations') == "") {
           $data=array('status'=>0,'message'=>'Authorizations key is required in api headers.','data' => array());
           return response(json_encode($data), 200); 
        }
         $user=User::select('id','auth_key')->where('auth_key',$request->header('Authorizations'))->first();
        if(!$user) {
            $data=array('status'=>0,'message'=>'User not found','data' => array());
           return response(json_encode($data), 200); 
        }
        $validator = Validator::make($request->all(), [
            'id' => 'required'
        ]);
        if($validator->fails())
        {
            $data = array('status'=>0, 'message'=>'Beat Plan Id required', 'data'=>$validator->errors());
            return response(json_encode($data),200);
        }else{
             // BeatPlanSubParts::where('beat_id',Crypt::decrypt($id))->delete();
            BeatPlanSubParts::where('beat_id',$request->id)->delete();
            // BeatPlans::find(Crypt::decrypt($id))->delete();
            BeatPlans::find($request->id)->delete();

            $data = array('status'=>1, 'message'=>'Beat Plan deleted successfully', 'data'=>array());
            return response(json_encode($data),200);

        }
       
    }
}
