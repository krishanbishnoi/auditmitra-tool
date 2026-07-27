<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Yard;
use App\User;
use App\Agency;
use App\Model\Branch;
use Validator;
use App\Imports\YardImport;
use App\Exports\YardExport;
use Maatwebsite\Excel\Facades\Excel;

use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class NCMController extends Controller
{   

    public function index(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Default date logic if no dates are provided
        if (!$startDate || !$endDate) {
            $today = Carbon::now();

            if ($today->day < 10) {
                $startDate = $today->copy()->subMonth()->startOfMonth()->day(10)->toDateString();
                $endDate = $today->copy()->startOfMonth()->day(9)->toDateString();
            } else {
                $startDate = $today->copy()->startOfMonth()->day(10)->toDateString();
                $endDate = $today->copy()->addMonth()->startOfMonth()->day(9)->toDateString();
            }
        }

        // Trim whitespace from the input dates
        $startDate = trim($startDate);
        $endDate = trim($endDate);

        // Try to convert the dates into Carbon objects
        try {
            // Adjusted format to match the "Y-m-d" format received
            $startDate = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay(); // Start of the day
            $endDate = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay(); // End of the day
        } catch (\Carbon\Exceptions\InvalidFormatException $e) {
            // Handle invalid format exception (you can log the error or return a response if needed)
            return response()->json(['error' => 'Invalid date format: ' . $e->getMessage()], 400);
        }
        

       $nationalCollManagerAuditData = DB::table('audits')
        ->leftJoin('closure_audits', 'audits.id', '=', 'closure_audits.audit_id')
        ->leftJoin('saved_audits', 'audits.id', '=', 'saved_audits.audit_id') // Join to include saved audits
        ->join('users', function ($join) {
            $join->on(DB::raw('FIND_IN_SET(users.id, audits.lavel_5)'), '>', DB::raw('0'));
        })
        ->join('products', 'audits.product_id', '=', 'products.id')
        ->select(
            'users.name as manager_name',
            // Adjust the audit count by excluding saved audits
            DB::raw('COUNT(DISTINCT audits.id) - COUNT(DISTINCT saved_audits.audit_id) as audit_count'),
            
            // Retail product status counts
            DB::raw('SUM(CASE WHEN products.name = "Retail" AND closure_audits.status = 0 THEN 1 ELSE 0 END) as retail_pending'),
            DB::raw('SUM(CASE WHEN products.name = "Retail" AND closure_audits.status = 1 THEN 1 ELSE 0 END) as retail_approved'),
            DB::raw('SUM(CASE WHEN products.name = "Retail" AND closure_audits.status = 2 THEN 1 ELSE 0 END) as retail_rejected'),
            
            // Credit Card product status counts
            DB::raw('SUM(CASE WHEN products.name = "Card" AND closure_audits.status = 0 THEN 1 ELSE 0 END) as credit_card_pending'),
            DB::raw('SUM(CASE WHEN products.name = "Card" AND closure_audits.status = 1 THEN 1 ELSE 0 END) as credit_card_approved'),
            DB::raw('SUM(CASE WHEN products.name = "Card" AND closure_audits.status = 2 THEN 1 ELSE 0 END) as credit_card_rejected'),

            // Retail + Card product status counts
            DB::raw('SUM(CASE WHEN products.name = "Retail/Card" AND closure_audits.status = 0 THEN 1 ELSE 0 END) as retail_card_pending'),
            DB::raw('SUM(CASE WHEN products.name = "Retail/Card" AND closure_audits.status = 1 THEN 1 ELSE 0 END) as retail_card_approved'),
            DB::raw('SUM(CASE WHEN products.name = "Retail/Card" AND closure_audits.status = 2 THEN 1 ELSE 0 END) as retail_card_rejected')
        )
        ->groupBy('users.name')
        ->whereBetween('audits.created_at', [$startDate, $endDate])
        ->get();

        return view('ncm.index', compact('nationalCollManagerAuditData'));
    }




    // V Filter
    // public function collectiongencytrend(Request $request)
    // {
    //     $agencyName = $request->input('agency_name');

    //     $query = CollectionAgency::query();

    //     if ($agencyName) {
    //         $query->where('agency_name', 'LIKE', "%{$agencyName}%");
    //     }

    //     $agencies = $query->get();

    //     // Return partial view for AJAX requests
    //     if ($request->ajax()) {
    //         return view('collection-agency-trend.index', ['agencies' => $agencies]);
    //     }

    //     // Return full view for regular requests
    //     return view('collection-agency-trend.index', compact('agencies'));
    // }


   
    



   

    






    public function create()
    {
        // $user=User::get(['id', 'name']);
        $user=[];
        $branch=Branch::get(['id', 'name']);
        // $agency=Agency::get(['id', 'name','branch_id','agency_manager']);
        $agency=[];
        return view('yard.create', 
        compact('user','branch','agency')
        );
    }

    public function getAgency($id){
        $data=Agency::where('branch_id',$id)->get(['id', 'name','branch_id']);
        return response()->json($data);
    }

    public function getAgencyManager($id){
        $agency=Agency::find($id,['id','agency_manager']);
        $data=User::where('id',$agency->agency_manager)->get(['id', 'name']);
        return response()->json($data);
    }

   
    public function store(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'branch_name' => 'required',
            'agency_name' => 'required',
            'yard_id' => 'required',
            'agency_manager' => 'required',
            'location' => 'required',
            'address' => 'required',
        ]);

        if ($validator->fails()) {

            return redirect()->back()->with('error', [$validator->errors()->all()])->withInput();
        } else {
        $yard=Yard::create(
            ['name'=>$request->name,
            'branch_id'=>$request->branch_name,
            'agency_id'=>$request->agency_name,
             'yard_id'=>$request->yard_id,
            'agency_manager'=>$request->agency_manager,
            'location'=>$request->location,
            'addresss'=>$request->address]
        );
        if($yard){
            return redirect('yard')->with('success', ['Yard created successfully.']);
        }
        else{
            return redirect()->back()->with('error', ['Yard creation unsuccessfully.']);
        }
        }
    }

   
    public function show($id)
    {
        $data=Yard::where('id',Crypt::decrypt($id))->delete();
        if($data){
            return redirect('yard')->with('success', ['Yard deleted successfully.']);
        }
        else{
            return redirect()->back()->with('error', ['Yard deletion unsuccessfully.']);
        }
    }

  
    public function edit($id)
    {
        $data=Yard::find(Crypt::decrypt($id));
        $user=User::get(['id', 'name']);
        $branch=Branch::get(['id', 'name']);
        $agency=Agency::get(['id', 'name','branch_id','agency_manager']);
        return view('yard.edit', 
        compact('data','user','branch','agency')
    );
    }

   
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'branch_name' => 'required',
            'agency_name' => 'required',
            'yard_id' => 'required',
            'agency_manager' => 'required',
            'location' => 'required',
            'address' => 'required',
        ]);

        if ($validator->fails()) {

            return redirect()->back()->with('error', [$validator->errors()->all()])->withInput();
        } else {
        $yard=Yard::where('id',Crypt::decrypt($id))->update(
            ['name'=>$request->name,
            'branch_id'=>$request->branch_name,
            'agency_id'=>$request->agency_name,
             'yard_id'=>$request->yard_id,
            'agency_manager'=>$request->agency_manager,
            'location'=>$request->location,
            'addresss'=>$request->address]
        );
        if($yard){
            return redirect('yard')->with('success', ['Yard updated successfully.']);
        }
        else{
            return redirect()->back()->with('error', ['Yard updation unsuccessfully.']);
        }
        }
    }

   
    public function destroy($id)
    {
        //
    }
    public function excelDownloadYard(){
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3000);
        return Excel::download(new YardExport, 'Yard.xlsx');
    }


    //V Upload Page Show
    public function showYardImport()
    {
        return view('yard.upload');
    }


    //V Upload Excel Data
    public function yardImport()
    {
        Excel::import(new YardImport, request()->file('file'));
        return redirect()->back()->with('success', 'Excel file imported successfully.');
    }

}
