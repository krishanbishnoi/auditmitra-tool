<?php

namespace App\Http\Controllers\Api;
use App\Agency;
use Illuminate\Support\Facades\Validator;
use App\Model\Allocation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Audit;
use App\Qc;
use App\AuditCycle;
use App\SavedAudit;
use App\User;
use App\SavedQcAudit;
use App\QmSheet;
use App\Model\ClientModuleAllocation;


class AllocationController extends Controller
{
    public function getSheets(Request $request){

      
        $authKey = $request->header('Authorizations') ?: $request->header('Authorizations');
       
        if(!$authKey || $authKey == "") {
           $data=array('status'=>0,'message'=>'Authorizations key is required in api headers.','data' => array());
           return response(json_encode($data), 200); 
        }
    
        // Debugging - Log or Output the Authorizationss Header
        \Log::info('Authorizations Header:', ['authKey' => $authKey]);
    
        $getUser = User::select('id','auth_key','client_id')->where('auth_key', $authKey)->first();
        
        if(!$getUser) {
            $data=array('status'=>0,'message'=>'User not found','data' => array());
           return response(json_encode($data), 200); 
        }
    
        // $data = Allocation::with('user','sheet')->where('user_id', $getUser->id)->get();
        $data=QmSheet::where('client_id',$getUser->client_id)->where('is_active', 1)->get();
        $response = array('status' => 1, 'message' => 'Audit Sheet List', 'data' => $data);
    
        return response(json_encode($response), 200);
    }
    

      public function savedAuditList(Request $request){
        if(!$request->header('Authorizations') || $request->header('Authorizations') == "") {
           $data=array('status'=>0,'message'=>'Authorizations key is required in api headers.','data' => array());
           return response(json_encode($data), 200); 
        }
         $getUser=User::select('id','auth_key')->where('auth_key',$request->header('Authorizations'))->first();
        if(!$getUser) {
            $data=array('status'=>0,'message'=>'User not found','data' => array());
           return response(json_encode($data), 200); 
        }
        // $validator = Validator::make($request->all(), [
        //      'user_id' => 'required'
        // ]);
        // if($validator->fails()){
        //  $data = array('status'=>0, 'message'=>'Validation Error', 'data'=>$validator->errors());
        //  return response(json_encode($data),200);
        // }
        else{ 


            $user=User::find($getUser->id);
         
            $data = Audit::with(['qmsheet','product','branch.city.state','branch.branchable','yard.branch.city.state','agency.branch.city.state','qa_qtl_detail'])->where('status', 5)->where('audited_by_id',$user->id) ->orderBy('id', 'DESC')->get();


            $output = array();

            foreach ($data as $key => $row) {
            
              $audit_date = (string)$row->created_at;
        
                    switch ($row->qmsheet->type) {

                        case 'agency':

                            $name=$row->agency->name ?? '';

                            $branch=$row->agency->branch->name ?? '';

                            $state=$row->agency->branch->city->state->name ?? '';

                            break;

                        case 'branch':

                            $name=$row->branch->name ?? '';

                            $branch=$row->branch->name ?? '';

                            $state=$row->branch->city->state->name ?? '';

                            break;
 
                        case 'repo_yard':

                            $name=$row->yard->name ?? '';

                            $branch=$row->yard->branch->name ?? '';

                            $state=$row->yard->branch->city->state->name ?? '';

                            break;

                        case 'branch_repo':

                            $name=$row->branchRepo->name ?? '';

                            $branch=$row->branchRepo->branch->name ?? '';

                            $state=$row->branchRepo->branch->city->state->name ?? '';

                            break;

                        case 'agency_repo':

                            $name=$row->agencyRepo->name ?? '';

                            $branch=$row->agencyRepo->branch->name ?? '';

                            $state=$row->agencyRepo->branch->city->state->name ?? '';

                            break;

                        

                    }

                                    
                    // print_r($row);
                    // die();
                    $rowOutput = ['month'=>\Carbon\Carbon::parse($row->created_at)->formatLocalized("%m'%y"),
                                    'audit_date'=>$audit_date,
                                    'lob'=>$row->qmsheet->lob ?? '',
                                    'state'=>$state ?? '',
                                    'qm_sheet_id'=>$row->qm_sheet_id,
                                    'audit_date_by_aud' => $row->audit_date_by_aud,
                                    'audit_cycle_id' =>$row->audit_cycle_id, 
                                    'audit_id'=>$row->id,
                                    'branch'=>$branch,
                                    'product'=>$row->product->name ?? '',
                                    'audit_type'=>$row->qmsheet->type ?? '',
                                    'agency_name'=>Agency::where('id',$row->agency_id)->pluck('name')->first(),
                                    'collection_manager'=>$row->user->name ?? '',
                                    'collection_manager_email'=>$row->user->email ?? '',
                                    'collection_manager_employee_id'=>$row->user->code ?? '',
                                    'auditor_name'=>$row->qa_qtl_detail->name ?? '',
                                    'visited_date_and_time'=>$audit_date,
                                    'status'=>'Saved',
                                    'audit_approved_on'=>$ids[$row->id]->created_at  ?? '',
                                    'audit_approved_name'=>$ids[$row->id]->user->name  ?? '',
                                    'artifact'=>$row->artifact_count ?? 0,
                                    'feedback'=>$ids[$row->id]->feedback  ?? '',
                                    'client_id' =>$getUser->client_id,
                                    // 'location'=> $row->latitude ??
                                    //             {$row->latitude.','.$row->longitude}

                                    ];
                    $output[] = $rowOutput;
                    }



            $jsonOutput=array('status'=>1,'message'=>"Saved Audit List.",'data'=> $output);
            return response(json_encode($jsonOutput),200);

        }   
       
    }


     public function submittedAuditList(Request $request){ 
        if(!$request->header('Authorizations') || $request->header('Authorizations') == "") {
           $data=array('status'=>0,'message'=>'Authorizations key is required in api headers.','data' => array());
           return response(json_encode($data), 200); 
        }
         $user=User::select('id','auth_key')->where('auth_key',$request->header('Authorizations'))->first();
        if(!$user) {
            $data=array('status'=>0,'message'=>'User not found','data' => array());
           return response(json_encode($data), 200); 
        } 
        // $validator = Validator::make($request->all(), [
        //      'user_id' => 'required'
        // ]);
        // if($validator->fails()){
        //  $data = array('status'=>0, 'message'=>'Validation Error', 'data'=>$validator->errors());
        //  return response(json_encode($data),200);
        // }
        else{
         
        // $savedQcIds=SavedQcAudit::all()->pluck('audit_id')->toArray();

            
        $ids=[];
        // $savedIds=SavedAudit::all()->pluck('audit_id')->toArray();

        if($user->hasRole('Admin')){
            $auditIds=Audit::where('status','<=',1)->get()->pluck('id');
        } 
        else{
            $auditIds=Audit::where('audited_by_id',$user->id)->where('status','<=',1)->get()->pluck('id');
        }

        
        if(count($auditIds)>0){
            $ids=Qc::with('user')->whereIn('audit_id',$auditIds)->get()->keyBy('audit_id');
        }
 

        $data = Audit::with(['qmsheet','product','branch.city.state','branch.branchable','yard.branch.city.state','agency.branch.city.state','qa_qtl_detail'])->whereIn('id',$auditIds)->where('audited_by_id',$user->id) ->orderBy('id', 'DESC')->get();

        


         $output = array();
            foreach ($data as $key => $row) {
                $audit_date = (string)$row->created_at;
               
            switch ($row->qmsheet->type) {

                        case 'agency':

                            $name=$row->agency->name ?? '';

                            $branch=$row->agency->branch->name ?? '';

                            $state=$row->agency->branch->city->state->name ?? '';

                            break;

                        case 'branch':

                            $name='';

                            $branch=$row->branch->name ?? '';

                            $state=$row->branch->city->state->name ?? '';

                            break;

                        case 'repo_yard':

                            $name=$row->yard->name ?? '';

                            $branch=$row->yard->branch->name ?? '';

                            $state=$row->yard->branch->city->state->name ?? '';

                            break;

                        case 'branch_repo':

                            $name=$row->branchRepo->name ?? '';

                            $branch=$row->branchRepo->branch->name ?? '';

                            $state=$row->branchRepo->branch->city->state->name ?? '';

                            break;

                        case 'agency_repo':

                            $name=$row->agencyRepo->name ?? '';

                            $branch=$row->agencyRepo->branch->name ?? '';

                            $state=$row->agencyRepo->branch->city->state->name ?? '';

                            break;

                    }
              
                   
                    // dd($row);
                    $rowOutput = ['month'=>\Carbon\Carbon::parse($row->created_at)            ->formatLocalized("%b'%y"),
                                    'audit_date'=>$audit_date,
                                    'audit_id'=>$row->id,
                                    'lob'=>$row->qmsheet->lob ?? '',
                                    'state'=>$state ?? '',
                                    'branch'=>$branch,
                                    'product'=>$row->product->name ?? '',
                                    'audit_type'=>$row->qmsheet->type ?? '',
                                    'agency_name'=>Agency::where('id',$row->agency_id)->pluck('name')->first(),
                                    'collection_manager'=>$row->user->name ?? '',
                                    'collection_manager_email'=>$row->user->email ?? '',
                                    'collection_manager_employee_id'=>$row->user->code ?? '',
                                    'auditor_name'=>$row->qa_qtl_detail->name ?? '',
                                    'visited_date_and_time'=>$audit_date,
                                    'status'=>$status ?? '',
                                    'audit_approved_on'=>$ids[$row->id]->created_at  ?? '',
                                    'audit_approved_name'=>$ids[$row->id]->user->name  ?? '',
                                    'artifact'=>$row->artifact_count ?? 0,
                                    'feedback'=>$ids[$row->id]->feedback  ?? '',
                                    // 'location'=> $row->latitude ??
                                    //             {$row->latitude.','.$row->longitude}


                                    'audit_date_by_aud' => $row->audit_date_by_aud,
                                    'overall_score'=>$row->overall_score,
                                    'grade' => $row->grade,
                                    'client_id' =>$user->client_id, 
                                    // 'audit_cycle_id' => $audit_cycle_name['name'], 

                                    ];
                    $output[] = $rowOutput;
                    }




       
        $jsonOutput=array('status'=>1,'message'=>"Submitted Audit List.",'data'=> $output);
        return response(json_encode($jsonOutput),200);

        } 

        

    }

    public function allocatedModuleList(Request $request)
{
    // Check if 'Authorizations' header exists and is not empty
    if (!$request->header('Authorizations') || trim($request->header('Authorizations')) === '') {
        return response()->json([
            'status' => 0,
            'message' => 'Authorizations key is required in API headers.',
            'data' => []
        ], 400); // 400 Bad Request
    }

    // Find user by auth_key from header
    $getUser = User::where('auth_key', $request->header('Authorizations'))->first();

    if (!$getUser) {
        return response()->json([
            'status' => 0,
            'message' => 'User not found',
            'data' => []
        ], 404); // 404 Not Found
    }

    // Fetch allocated modules for the user’s client_id
    $modules = ClientModuleAllocation::where('client_module_allocations.client_id', $getUser->client_id)
        ->join('module_permissions', 'client_module_allocations.module_id', '=', 'module_permissions.id')
        ->get([
            'client_module_allocations.module_id as key',
            'module_permissions.module_name as value'
        ]);

    return response()->json([
        'status' => 1,
        'message' => 'Allocated modules retrieved successfully',
        'allocated_module' => $modules
    ], 200);
}
}
