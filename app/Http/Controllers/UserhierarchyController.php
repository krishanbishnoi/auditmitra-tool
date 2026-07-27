<?php

namespace App\Http\Controllers;

use App\Language;

use App\Mail\UserCreated;
use App\Process;
use App\Region;
use Illuminate\Support\Facades\Crypt;
use Spatie\Permission\Models\Role;
use App\User;
use App\Model\UserHierarchy;
use App\UsersMaster;
use Auth;
//use Crypt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UserHierarchyImport;
use App\Exports\UsersExport;
use App\Exports\QcAndQaChangesExport;
use DB;
use Maatwebsite\Excel\Validators\ValidationException;


class UserhierarchyController extends Controller
{
    public function index()
    {
        $id = Auth::user()->id;
        $data = DB::table('user_hierarchies')
        ->leftJoin('users as cm_users', 'cm_users.id', '=', 'user_hierarchies.cm_id')
        ->leftJoin('users as qa_users', 'qa_users.id', '=', 'user_hierarchies.qa_id')
        ->leftJoin('users as qc_users', 'qc_users.id', '=', 'user_hierarchies.qc_id')
        ->leftJoin('users as acm_users', 'acm_users.id', '=', 'user_hierarchies.acm_id')
        ->leftJoin('users as rcm_users', 'rcm_users.id', '=', 'user_hierarchies.rcm_id')
        ->leftJoin('users as zcm_users', 'zcm_users.id', '=', 'user_hierarchies.zcm_id')
        ->leftJoin('users as ncm_users', 'ncm_users.id', '=', 'user_hierarchies.ncm_id')
        ->leftJoin('users as gph_users', 'gph_users.id', '=', 'user_hierarchies.gph_id')
        ->leftJoin('users as hc_users', 'hc_users.id', '=', 'user_hierarchies.hc_id')
        ->leftJoin('roles', 'roles.id', '=', 'cm_users.user_role_id')
        ->select(
            'cm_users.name as cm_name',
            'qa_users.name as qa_name',
            'qc_users.name as qc_name',
            'acm_users.name as acm_name',
            'rcm_users.name as rcm_name',
            'zcm_users.name as zcm_name',
            'ncm_users.name as ncm_name',
            'gph_users.name as gph_name',
            'hc_users.name as hc_name',
            'roles.name as cmrolename',
            'user_hierarchies.*'
        )
        ->where('user_hierarchies.created_by', $id)
        ->where('user_hierarchies.status', 1)
        ->get();

        //echo '<pre>'; print_r($data); die();

        return view('userheirarchy.list', ['data' => $data]);
    }

    public function create()
    {
               
        $User = User::pluck('name','id')->toArray();
        $col_manager_roles = User::where('user_role_id','3')->pluck('name','id')->toArray();
        $quality_auditor_roles = User::where('user_role_id','2')->pluck('name','id')->toArray();
        $quality_control_roles = User::where('user_role_id','10')->pluck('name','id')->toArray();
        $area_col_manager_roles = User::where('user_role_id','4')->pluck('name','id')->toArray();
        $reg_col_manager_roles = User::where('user_role_id','5')->pluck('name','id')->toArray();
        $zonal_col_manager_roles = User::where('user_role_id','6')->pluck('name','id')->toArray();
        $national_col_manager_roles = User::where('user_role_id','7')->pluck('name','id')->toArray();
        $group_product_head_roles = User::where('user_role_id','8')->pluck('name','id')->toArray();
        $head_collection_roles = User::where('user_role_id','9')->pluck('name','id')->toArray();

        // $col_manager_roles = User::where('user_role_id','3')->get();
        //$quality_auditor_roles = User::where('user_role_id','2')->get();
        //$quality_control_roles = User::where('user_role_id','10')->get();
        //$area_col_manager_roles = User::where('user_role_id','4')->get();
        //$reg_col_manager_roles = User::where('user_role_id','5')->get();
        //$zonal_col_manager_roles = User::where('user_role_id','6')->get();
        //$national_col_manager_roles = User::where('user_role_id','7')->get();
        //$group_product_head_roles = User::where('user_role_id','8')->get();
        //$head_collection_roles = User::where('user_role_id','9')->get();
        
        // echo '<pre>'; print_r($col_manager_roles); die;

        $roles = Role::all();
        //call masters
        return view("userheirarchy.create", compact('quality_auditor_roles','col_manager_roles','area_col_manager_roles','reg_col_manager_roles','zonal_col_manager_roles','national_col_manager_roles','group_product_head_roles','head_collection_roles','quality_control_roles',));
    }
 

    public function store(Request $request)
    { 
        $id = Auth::user()->id;
        $validator = Validator::make($request->all(), [
            //'name' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', [$validator->errors()->all()])->withInput();
        } else {
            $data=UserHierarchy::Create(
            [
                'cm_id'     =>$request->cm_id,  
                'qa_id'     =>$request->qa_id, 
                'qc_id'     =>$request->qc_id, 
                'acm_id'    =>$request->acm_id, 
                'rcm_id'     =>$request->rcm_id, 
                'zcm_id'    =>$request->zcm_id,  
                'ncm_id'    =>$request->ncm_id, 
                'gph_id'    =>$request->gph_id, 
                'hc_id'     =>$request->hc_id,
                'created_by'=>$id,  
            ]);
            if($data){
                return redirect()->route('userhierarchy.index')->with('success', ['User Heirarchy created successfully.']);  
            }
            else{
                return redirect()->back()->with('error', ['User Heirarchy creation unsuccessfully.']);
            }
        }
    }



    public function edit($id)
    {
        $roles = Role::pluck("name", "id")->all();
        $data = User::find(Crypt::decrypt($id));
                  
        $userdata=UserHierarchy::find(Crypt::decrypt($id));
        $col_manager_roles = User::where('user_role_id', '3')->get();
        $quality_auditor_roles = User::where('user_role_id', '2')->get();
        $quality_control_roles = User::where('user_role_id', '10')->get();
        $area_col_manager_roles = User::where('user_role_id', '4')->get();
        $reg_col_manager_roles = User::where('user_role_id', '5')->get();
        $zonal_col_manager_roles = User::where('user_role_id', '6')->get();
        $national_col_manager_roles = User::where('user_role_id', '7')->get();
        $group_product_head_roles = User::where('user_role_id', '8')->get();
        $head_collection_roles = User::where('user_role_id', '9')->get();

        //echo '<pre>'; print_r($col_manager_roles); die();
        return view("userheirarchy.edit", compact("roles", "data", 'userdata', 
        'col_manager_roles','quality_auditor_roles','quality_control_roles','area_col_manager_roles',
        'reg_col_manager_roles','zonal_col_manager_roles','national_col_manager_roles','group_product_head_roles','head_collection_roles'));
    }


    public function update(Request $request, $id)
    {
        $id = Auth::user()->id;
        $validator = Validator::make($request->all(), [
            //"name" => "required",
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->with("error", [$validator->errors()->all()])
                ->withInput();
        } else {
                $userhierarchy = UserHierarchy::find(Crypt::decrypt($id));
                $userhierarchy->cm_id       = $request->cm_id;
                $userhierarchy->qa_id       = $request->qa_id;  
                $userhierarchy->qc_id       = $request->qc_id;  
                $userhierarchy->acm_id      = $request->acm_id;  
                $userhierarchy->rcm_id      = $request->rcm_id;  
                $userhierarchy->zcm_id      = $request->zcm_id;  
                $userhierarchy->ncm_id      = $request->ncm_id;  
                $userhierarchy->gph_id      = $request->gph_id;  
                $userhierarchy->hc_id       = $request->hc_id;
                $userhierarchy->created_by  = $id;                
                $userhierarchy->save();
            return redirect()->route('userhierarchy.index')->with('success', ['User Heirarchy updated successfully.']);    
        }
    }

    public function destroy($id)
    {
        $id = Crypt::decrypt($id); // Decrypt the ID
        $userhierarchy = UserHierarchy::findOrFail($id);
        $userhierarchy->delete();
        return redirect()->back()->with('success', 'User Hierarchy deleted successfully.');
    }

    //V Upload Page Show
    public function showUserHeirarchyImport()
    {
        return view('userheirarchy.upload');
    }

    //V Upload Excel Data
    public function userHierarchyImport(Request $request)
    {
        try {
            $dataArray = Excel::toArray(new UserHierarchyImport(), $request->file('file'));

            if (empty($dataArray)) {
                return redirect()->route('userhierarchy.index')->with('error', 'Empty file or invalid format.');
            }
            $cmIdsInExcel = collect($dataArray[0])->pluck('cm_id')->filter()->unique()->toArray();
            $existingCmIds = UserHierarchy::whereIn('cm_id', $cmIdsInExcel)->pluck('cm_id')->toArray();

            if (!empty($existingCmIds)) {
                $message = 'Import aborted. The following cm_ids already exist in the database: ' . implode(', ', $existingCmIds);
                return redirect()->route('userhierarchy.index')->with('error', $message);
            }

            Excel::import(new UserHierarchyImport(), $request->file('file'));

            return redirect()->route('userhierarchy.index')->with('success', 'User Hierarchy imported successfully.');
        } catch (\Exception $e) {
            return redirect()->route('userhierarchy.index')->with('error', 'Error importing User Hierarchy: ' . $e->getMessage());
        }
    }





    public function downloadsampleExcelUH()
    {
        $file= public_path(). "/download/user-hierarchy-sample.xlsx";
        return response()->download($file, 'user-hierarchy-sample.xlsx');
    }




       

   

    

    
    
    // public function change_user_status($user_id, $status)
    // {
    //     $user = User::find(Crypt::decrypt($user_id));

    //     $user->status = $status;

    //     $user->save();

    //     return redirect("user")->with(
    //         "success",
    //         "User status updated successfully"
    //     );
    // }

 

    

    // public function excelDownloadUser()
    // {
    //     ini_set("memory_limit", "-1");

    //     ini_set("max_execution_time", 3000);

    //     return Excel::download(new UsersExport(), "users.xlsx");

    //     // return Excel::download(new QcAndQaChangesExport, 'users.xlsx');
    // }
}
