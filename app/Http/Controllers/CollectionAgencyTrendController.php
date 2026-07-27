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


class CollectionAgencyTrendController extends Controller
{   

    public function index(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $agencyName = $request->input('agency_name'); // Capture the agency name filter from the request

        if (!$startDate || !$endDate) {
            $today = Carbon::now();

            // Determine if today is before or after the 10th
            if ($today->day < 10) {
                // If today is before the 10th, adjust to the previous "month range"
                $startDate = $today->copy()->subMonth()->startOfMonth()->day(10)->toDateString();
                $endDate = $today->copy()->startOfMonth()->day(9)->toDateString();
            } else {
                // If today is on or after the 10th, use the current "month range"
                $startDate = $today->copy()->startOfMonth()->day(10)->toDateString();
                $endDate = $today->copy()->addMonth()->startOfMonth()->day(9)->toDateString();
            }
        }

    $collAgencyTrendData = DB::table('audits')
    ->leftJoin('closure_audits', 'audits.id', '=', 'closure_audits.audit_id')
    ->leftJoin('saved_audits', 'audits.id', '=', 'saved_audits.audit_id') // Join with saved_audits
    ->join('users', function ($join) {
        $join->on(DB::raw('FIND_IN_SET(users.id, audits.lavel_3)'), '>', DB::raw('0'));
    })
    ->join('products', 'audits.product_id', '=', 'products.id')
    ->join('agencies', 'audits.agency_id', '=', 'agencies.id')
    ->whereNull('saved_audits.audit_id') // Exclude drafts
    ->whereBetween(DB::raw('DATE(audits.created_at)'), [$startDate, $endDate])
    ->select(
        'agencies.name as agency_name',
        'agencies.location',
        'audits.agency_id',
        'audits.lavel_3',
        'users.id as user_id',
        'users.name as collection_manager_name',
        DB::raw("
            DATE_FORMAT(
                DATE_SUB(audits.created_at, INTERVAL IF(DAY(audits.created_at) < 10, 1, 0) MONTH), 
                '%b %y'
            ) as month_year
        "),
        DB::raw('COUNT(DISTINCT audits.id) as audit_count'),
        DB::raw('AVG(CASE WHEN audits.overall_score >= 0 AND audits.overall_score <= 100 THEN audits.overall_score ELSE NULL END) as average_score_percentage'),
        DB::raw('SUM(CASE WHEN products.name = "Retail" THEN audits.score_percentage ELSE 0 END) as retail_score_percentage'),
        DB::raw('SUM(CASE WHEN products.name = "Card" THEN audits.score_percentage ELSE 0 END) as card_score_percentage'),
        DB::raw('SUM(CASE WHEN products.name = "Retail/Card" THEN audits.score_percentage ELSE 0 END) as retails_card_score_percentage'),
        DB::raw('AVG(CASE WHEN products.name = "Retail" AND audits.overall_score >= 0 AND audits.overall_score <= 100 THEN audits.overall_score ELSE NULL END) as retail_average_score_percentage'),
        DB::raw('AVG(CASE WHEN products.name = "Card" AND audits.overall_score >= 0 AND audits.overall_score <= 100 THEN audits.overall_score ELSE NULL END) as card_average_score_percentage'),
        DB::raw('AVG(CASE WHEN products.name = "Retail/Card" AND audits.overall_score >= 0 AND audits.overall_score <= 100 THEN audits.overall_score ELSE NULL END) as retails_card_average_score_percentage')
    )
    ->groupBy(
        'audits.agency_id',
        'audits.lavel_3',
        'agencies.name',
        'agencies.location',
        'users.id',
        'users.name',
        DB::raw("
            DATE_FORMAT(
                DATE_SUB(audits.created_at, INTERVAL IF(DAY(audits.created_at) < 10, 1, 0) MONTH), 
                '%b %y'
            )
        ")
    )
    ->orderBy('month_year', 'asc')
    ->paginate(10);


        // Fetch six months range
        $sixMonthTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $startOfMonth = Carbon::now()->subMonths($i)->startOfMonth()->day(10)->toDateString();
            $endOfMonth = Carbon::now()->subMonths($i - 1)->startOfMonth()->day(9)->toDateString();
            $monthYear = Carbon::now()->subMonths($i)->format('M y');
            $sixMonthTrend[$monthYear] = [
                'month_year' => $monthYear,
                'average_score' => 0,
                'retail_score_percentage' => 0,
                'card_score_percentage' => 0,
                'retails_card_score_percentage' => 0,
                'retail_average_score_percentage' => 0,
                'card_average_score_percentage' => 0,
                'retails_card_average_score_percentage' => 0,
            ];
        }

        // Process trend data for agencies and products
        $finalDataAgencyTrend = $collAgencyTrendData->groupBy('agency_id')->map(function ($agencyData) use ($sixMonthTrend) {
            $trendData = [];
            foreach ($sixMonthTrend as $month => $default) {
                $data = $agencyData->firstWhere('month_year', $month);
                $trendData[] = [
                    'month_year' => $month,
                    'average_score' => $data->average_score_percentage ?? 0,
                    'retail_score_percentage' => $data->retail_score_percentage ?? 0,
                    'card_score_percentage' => $data->card_score_percentage ?? 0,
                    'retails_card_score_percentage' => $data->retails_card_score_percentage ?? 0,
                    'retail_average_score_percentage' => $data->retail_average_score_percentage ?? 0,
                    'card_average_score_percentage' => $data->card_average_score_percentage ?? 0,
                    'retails_card_average_score_percentage' => $data->retails_card_average_score_percentage ?? 0,
                ];
            }

            usort($trendData, function ($a, $b) {
                return strtotime($b['month_year']) - strtotime($a['month_year']);
            });

            $agency = $agencyData->first();
            return (object) [
                'agency_id' => $agency->agency_id,
                'agency_name' => $agency->agency_name ?? '',
                'location' => $agency->location ?? '',
                'collection_manager_name' => $agency->collection_manager_name ?? '',
                'audit_count' => $agency->audit_count ?? 0,
                'average_score_percentage' => $agency->average_score_percentage ?? 0,
                'six_month_data' => $trendData,
            ];
        });

        return view('collection_agency_trend.index', compact('collAgencyTrendData', 'finalDataAgencyTrend'));
    }


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
