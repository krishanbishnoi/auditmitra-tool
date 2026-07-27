<?php



namespace App\Http\Controllers;



use Illuminate\Http\Request;

use Illuminate\Support\Facades\Crypt;

use App\YardRepo;
use auth;
use App\User;
use DB,Response;

use App\Model\Branch;
use App\Model\Products;

use Validator;
use App\Imports\YardRepoImport;
use App\Exports\YardRepoExport;
use Session;
use Maatwebsite\Excel\Facades\Excel;


class YardRepoController extends Controller

{
    
    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */
   

    public function index()

    {
        
        $data=YardRepo::with('User')->where('client_id', auth()->user()->client_id)->where('status',0)->get();


       // echo '<pre>'; print_r($data); die;
        return view('yardrepo.list', compact('data'));

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
        $products=Products::where('status','0')->pluck('name', 'id')->toArray();
        return view('yardrepo.create',compact('user','branch','regions','products') );

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
             $YardRepo = YardRepo::create([
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
             if ($YardRepo) {
                 // Insert emails and mobile numbers into the agency_mobile_emails table
                 foreach ($request->emails as  $email) {
                     \DB::table('yard_repo_mobile_emails')->insert([
                         'agency_id' => $YardRepo->id, // Foreign key to Agency
                         'email' => $email,
                         'mobile_number' => '', // Assumes there are equal numbers of emails and mobile numbers
                     ]);
                 }
                 foreach ($request->mobile_numbers as $mobile_number) {
                    \DB::table('yard_repo_mobile_emails')->insert([
                        'agency_id' => $YardRepo->id, // Foreign key to Agency
                        'email' => '',
                        'mobile_number' => $mobile_number, // Assumes there are equal numbers of emails and mobile numbers
                    ]);
                }
    
                 return redirect('yardrepo')->with('success', ['Agency created successfully.']);
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
        $data=YardRepo::where('id',Crypt::decrypt($id))->update(['status'=>1]);

        if($data){

            return redirect('yardrepo')->with('success', ['Agency deleted successfully.']);

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
        
        
         $data = YardRepo::with(['city.state.region', 'emails', 'mobileNumbers'])->find(Crypt::decrypt($id));
     
         
         $region = $data->city->state->region->id ?? '';
     
         
         $user = User::get(['id', 'name']);
     
        
         $regions = DB::table("regions")->get();
     
         
         $branch = Branch::get(['id', 'name']);
         $products=Products::where('status','0')->pluck('name', 'id')->toArray();
        
         return view('yardrepo.edit', compact('data', 'user', 'branch', 'regions', 'region','products'));
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
     
         $yard = YardRepo::where('id', Crypt::decrypt($id))->first();
         $yard->update([
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
         $yard->emails()->delete();  // Clear previous emails
         foreach ($request->emails as $email) {
             $yard->emails()->create(['email' => $email]);
         }
     
         // Update Mobile Numbers
         $yard->mobileNumbers()->delete();  // Clear previous mobile numbers
         foreach ($request->mobile_numbers as $mobile_number) {
             $yard->mobileNumbers()->create(['mobile_number' => $mobile_number]);
         }
     
         return redirect('yardrepo')->with('success', ['Agency updated successfully.']);
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

    public function excelDownloadYardRepo(){

        ini_set('memory_limit', '-1');

        ini_set('max_execution_time', 3000);

        return Excel::download(new YardRepoExport, 'YardRepo.xlsx');

    }


    //V Upload Page Show
    public function showYardRepoImport()
    {
        return view('yardrepo.upload');
    }


    //V Upload Excel Data
    public function yardRepoImport()
    {
     //  echo 'dfdsf'; die;AgencyImport
        Excel::import(new YardRepoImport, request()->file('file'));

        return redirect()->back()->with('success', 'Excel file imported successfully.');
    }

    public function downloadYardRepoSample(){
      //  echo "jh"; die;
       $file= public_path(). "/download/yardrepo_import.xlsx";
        $headers = array(
                  'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                );
        return Response::download($file, 'yardrepo_import.xlsx',$headers);
    }

}