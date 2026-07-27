<?php



namespace App\Http\Controllers\Api\v2;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Crypt;

use App\Agency;
use App\AuditCycle;
use App\User;

use App\Model\Branch;
use Carbon\Carbon;
use Validator;
use DB;
use App\Model\AgencyMobileEmail;
use App\Exports\AgencyExport;

use Maatwebsite\Excel\Facades\Excel;

class AgencyController extends Controller

{

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index()
    {

        $data=Agency::with('branch')->where('status',0)->get();

        return view('agency.list', compact('data'));

    }



    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {

        $user=User::get(['id', 'name']);

        $branch=Branch::get(['id', 'name']);

        return view('agency.create',

        compact('user','branch')

    );

    }



    /**

     * Store a newly created resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    public function store(Request $request)

    {

        $validator = Validator::make($request->all(), [

            'name' => 'required',

            'branch_name' => 'required',

            'agency_id' => 'required',

            'agency_manager' => 'required',

            'location' => 'required',

            'address' => 'required',

        ]);



        if ($validator->fails()) {
            
            return redirect()->back()->with('error', [$validator->errors()->all()])->withInput();

        } else {

        $Agency=Agency::create(

            ['name'=>$request->name,

            'branch_id'=>$request->branch_name,

            'agency_id'=>$request->agency_id,

            'agency_manager'=>$request->agency_manager,

            'location'=>$request->location,

            'addresss'=>$request->address]

        );

        if($Agency){
            return redirect('agency')->with('success', ['Agency created successfully.']);
        }
        else{
            return redirect()->back()->with('error', ['Agency creation unsuccessfully.']);
        }

        }

    }



    /**

     * Display the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function show($id)

    {

        // $data=Agency::where('id',Crypt::decrypt($id))->delete();
        $data=Agency::where('id',Crypt::decrypt($id))->update(['status'=>1]);

        if($data){

            return redirect('agency')->with('success', ['Agency deleted successfully.']);

        }

        else{

            return redirect()->back()->with('error', ['Agency deletion unsuccessfully.']);

        }

    }



    /**

     * Show the form for editing the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function edit($id)

    {

        $data=Agency::find(Crypt::decrypt($id));

        $user=User::get(['id', 'name']);

        

        $branch=Branch::get(['id', 'name']);

        return view('agency.edit', compact('data','user','branch'));

    }



    /**

     * Update the specified resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function update(Request $request, $id)

    {

        $validator = Validator::make($request->all(), [

            'name' => 'required',

            'branch_name' => 'required',

            'agency_id' => 'required',

            'agency_manager' => 'required',

            'location' => 'required',

            'address' => 'required',

        ]);



        if ($validator->fails()) {



            return redirect()->back()->with('error', [$validator->errors()->all()])->withInput();

        } else {

        $agency=Agency::where('id',Crypt::decrypt($id))->update(

            ['name'=>$request->name,

            'branch_id'=>$request->branch_name,

            'agency_id'=>$request->agency_id,

            'agency_manager'=>$request->agency_manager,

            'location'=>$request->location,

            'addresss'=>$request->address]

        );

        if($agency){

            return redirect('agency')->with('success', ['Agency updated successfully.']);

        }

        else{

            return redirect()->back()->with('error', ['Agency updation unsuccessfully.']);

        }

        }

    }



    /**

     * Remove the specified resource from storage.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)

    {

        //

    }

    public function get_agencies_from_city(Request $request){
        if(!$request->header('Authorizations') || $request->header('Authorizations') == "") {
            $data=array('status'=>0,'message'=>'Authorizations key is required in api headers.','data' => array());
            return response(json_encode($data), 200); 
         }
        $user=User::select('id','auth_key','email','client_id')->where('auth_key',$request->header('Authorizations'))->first();
        
        if(!$user) {
            $data=array('status'=>0,'message'=>'User not found','data' => array());
            return response(json_encode($data), 200); 
        }
       
        $cycle = AuditCycle::where('client_id', $user->client_id)->where('status', 1)
        ->first();     
        $getcurrentCycle=$cycle->id;
         
        $assign_agency_ids = DB::table('auditor_assigns')
                ->where('auditor_email', $user->email)
                ->where('audit_cycle_id',$getcurrentCycle)
                ->pluck('agency_id')
                ->toArray();

            // Get agency_ids that are already in the audits table
        $auditedAgencyIds = DB::table('audits')->where('audit_cycle_id',$getcurrentCycle)->where('status', 1)->pluck('agency_id')->toArray();

            // dd($auditedAgencyIds);

            // Now get the agencies that are assigned, in date range, and NOT audited yet

            $agencyList = Agency::whereIn('id', $assign_agency_ids)
                ->whereNotIn('id', $auditedAgencyIds)

        
                ->orderBy('name', 'ASC')
                ->get();
      
        return response()->json(['status' => True, 'message' => 'Agency Details for Selected City is here.', 'details' => $agencyList]);        
    }

    public function excelDownloadAgency(){

        ini_set('memory_limit', '-1');

        ini_set('max_execution_time', 3000);

        return Excel::download(new AgencyExport, 'Agency.xlsx');

    }


    public function agencyDetails(Request $request){
         $validator = Validator::make($request->all(), [
             'agency_id' => 'required'
         ]);
         if($validator->fails())
         {
             $data = array('status'=>0, 'message'=>'Agency id is required', 'data'=>$validator->errors());
             return response(json_encode($data),200);
         }else{
                            // Fetch agency details
                $agency_details = DB::table('agencies')->where('agencies.id', $request->agency_id)->first();

                if (!empty($agency_details)) {

                    // Fetch agency emails with null mobile_number
                    $agency_email = AgencyMobileEmail::where('agency_id', $agency_details->id)
                        ->whereNull('mobile_number')
                        ->pluck('email', 'email')
                        ->toArray();

                    // Fetch agency mobile numbers with null email
                    $agency_mobile_number = AgencyMobileEmail::where('agency_id', $agency_details->id)
                        ->whereNull('email')
                        ->pluck('mobile_number', 'mobile_number')
                        ->toArray();

                    // Fetch product details
                    $product = DB::table('products')
                        ->select('id', 'name')
                        ->where('id', $agency_details->product_id)
                        ->first();

                    // Fetch sub-product (product attributes) details
                    $sub_product_ids = explode(',', $agency_details->sub_product_id);
                    $sub_products = DB::table('productattributes')
                        ->select('id', 'product_attribute_name')
                        ->whereIn('id', $sub_product_ids)
                        ->where('product_id', $agency_details->product_id)
                        ->get();

                } else {
                    $agency_email = [];
                    $agency_mobile_number = [];
                    $product = null;
                    $sub_products = [];
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Agency Details for Selected is here.',
                    'details' => $agency_details,
                    'agency_mobile_number' => $agency_mobile_number,
                    'agency_email' => $agency_email,
                    'product' => $product,
                    'sub_products' => $sub_products,
                ]);
 
         }

    }
}

