<?php



namespace App\Http\Controllers;



use Illuminate\Http\Request;

use Illuminate\Support\Facades\Crypt;

use App\Branch;

use App\User;
use DB,Response;
use Auth;


use App\Model\Products;

use Validator;
use App\Imports\BranchImport;
use App\Exports\BranchExport;
use Session;
use Maatwebsite\Excel\Facades\Excel;


class BranchController extends Controller

{
    
    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */
   

    public function index()

    {
        
        $data=Branch::with('User')->where('client_id', auth()->user()->client_id)->where('status',0)->get();


       // echo '<pre>'; print_r($data); die;
        return view('branch.list', compact('data'));

    }



    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()
    {
        

        $user=User::get(['id', 'name']);
        $regions = DB::table("regions")->get();

        $branch=Branch::get(['id', 'name']);
        $products=Products::where('status','0')->where('client_id' , auth()->user()->client_id)->pluck('name', 'id')->toArray();
        return view('branch.create',compact('user','branch','regions','products') );

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
             'agency_id' => 'required',
             'agency_manager' => 'required',
             'location' => 'required',
             'address' => 'required',
             'region_id' => 'required',
             'state' => 'required',
             'emails' => 'required|array',
             'emails.*' => 'email',
             'city_id' => 'required',
             'mobile_numbers' => 'required|array',
             'mobile_numbers.*' => 'required|digits_between:10,15',
         ]);
     
         if ($validator->fails()) {
             return redirect()->back()->with('error', [$validator->errors()->all()])->withInput();
         } else {
             // First, create the Agency record
             $Agency = Branch::create([
                 'name' => $request->name,
                 'agency_id' => $request->agency_id,
                 'agency_manager' => $request->agency_manager,
                 'location' => $request->location,
                 'address' => $request->address,
                 'region_id' => $request->region_id,
                 'state' => $request->state,
                 'city_id' => $request->city_id,
                 'product_id' => $request->product_id,
                 'client_id' => auth()->user()->client_id,
             ]);
     
             // If the Agency is created successfully, insert the emails and mobile numbers
             if ($Agency) {
                 // Insert emails and mobile numbers into the agency_mobile_emails table
                 foreach ($request->emails as  $email) {
                     \DB::table('branch_mobile_emails')->insert([
                         'agency_id' => $Agency->id, // Foreign key to Agency
                         'email' => $email,
                         'mobile_number' => '', // Assumes there are equal numbers of emails and mobile numbers
                     ]);
                 }
                 foreach ($request->mobile_numbers as $mobile_number) {
                    \DB::table('branch_mobile_emails')->insert([
                        'agency_id' => $Agency->id, // Foreign key to Agency
                        'email' => '',
                        'mobile_number' => $mobile_number, // Assumes there are equal numbers of emails and mobile numbers
                    ]);
                }
    
                 return redirect('branch')->with('success', ['Agency created successfully.']);
             } else {
                 return redirect()->back()->with('error', ['Agency creation failed.']);
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
        $data=Branch::where('id',Crypt::decrypt($id))->update(['status'=>1]);

        if($data){

            return redirect('branch')->with('success', ['Agency deleted successfully.']);

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
        
        
         $data = Branch::with(['city.state.region', 'emails', 'mobileNumbers'])->find(Crypt::decrypt($id));
     
         
         $region = $data->city->state->region->id ?? '';
     
         
         $user = User::get(['id', 'name']);
     
        
         $regions = DB::table("regions")->get();
     
         
         $branch = Branch::get(['id', 'name']);
         $products=Products::where('status','0')->pluck('name', 'id')->toArray();
        
         return view('branch.edit', compact('data', 'user', 'branch', 'regions', 'region','products'));
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
        
     //   echo "<pre>"; print_r($request->all()); die;
         $validator = Validator::make($request->all(), [
             'name' => 'required',
             'agency_id' => 'required',
             'agency_manager' => 'required',
             'location' => 'required',
             'address' => 'required',
             'region_id' => 'required',
             'state' => 'required',
             'city_id' => 'required',
             'emails' => 'required|array|min:1',
             'mobile_numbers' => 'required|array|min:1',

         ]);
     
         if ($validator->fails()) {
             return redirect()->back()->with('error', [$validator->errors()->all()])->withInput();
         }
     
         $agency = Branch::where('id', Crypt::decrypt($id))->first();
         $agency->update([
             'name' => $request->name,
             'agency_id' => $request->agency_id,
           //  'agency_manager' => $request->agency_manager,
             'location' => $request->location,
             'address' => $request->address,
             'region_id' => $request->region_id,
             'state' => $request->state,
             'city_id' => $request->city_id,
             'product_id' => $request->product_id,
         ]);
     
         // Update Emails
         $agency->emails()->delete();  // Clear previous emails
         foreach ($request->emails as $email) {
             $agency->emails()->create(['email' => $email]);
         }
     
         // Update Mobile Numbers
         $agency->mobileNumbers()->delete();  // Clear previous mobile numbers
         foreach ($request->mobile_numbers as $mobile_number) {
             $agency->mobileNumbers()->create(['mobile_number' => $mobile_number]);
         }
     
         return redirect('branch')->with('success', ['Agency updated successfully.']);
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

    public function excelDownloadBranch(){

        ini_set('memory_limit', '-1');

        ini_set('max_execution_time', 3000);

        return Excel::download(new BranchExport, 'Branch.xlsx');

    }


    //V Upload Page Show
    public function showBranchImport()
    {
        return view('branch.upload');
    }


    //V Upload Excel Data
    public function branchImport()
    {
     //  echo 'dfdsf'; die;AgencyImport
        Excel::import(new BranchImport, request()->file('file'));

        return redirect()->back()->with('success', 'Excel file imported successfully.');
    }

    public function downloadBranchSample(){
      //  echo "jh"; die;
       $file= public_path(). "/download/branch_import.xlsx";
        $headers = array(
                  'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                );
        return Response::download($file, 'branch_import.xlsx',$headers);
    }



    
    //For fetching states

    public function getStates($id)

    {
        

        $states = DB::table("states")

            ->where("region_id", $id)

            ->pluck("name", "id");

        return response()->json($states);

    }
 


//For fetching cities coorespont to states

    public function getCities($id)

    {
        

        $cities = DB::table("cities")

            ->where("state_id", $id)

            ->pluck("name", "id");

        return response()->json($cities);

    }







}