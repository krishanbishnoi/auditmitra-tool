<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\SavedAudit;
use App\AuditCycle;
use App\Audit;
use Carbon\Carbon;
use App\AuditResult;
use App\Qc;
use DB;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        if (!$request->header('Authorizations') || $request->header('Authorizations') == "") {
            $data = array('status' => 0, 'message' => 'Authorizations key is required in api headers.', 'data' => array());
            return response(json_encode($data), 200);
        }
        $getUser = User::select('id', 'auth_key')->where('auth_key', $request->header('Authorizations'))->first();
        if (!$getUser) {
            $data = array('status' => 0, 'message' => 'User not found', 'data' => array());
            return response(json_encode($data), 200);
        }
        //
        //set_time_limit(0);

        // ini_set('memory_limit', '-1');
        // ini_set('max_execution_time', 600);
        $data = $this->qaDashboardMeta($request, $getUser);
        $totalAudit = $data['totalAudit'];
        $old = $data['old'];
        $totalsaved = $data['totalsaved'];
        $totalpending = $data['totalpending'];
        $totalpass = $data['totalpass'];
        $totalfaild = $data['totalfaild'];
        $jsonOutput = ['totalAudit' => $totalAudit, 'old' => $old, 'totalsaved' => $totalsaved, 'totalpending' => $totalpending, 'totalpass' => $totalpass, 'totalfailed' => $totalfaild];

        // return view('dashboardQa',compact('totalAudit','old','totalsaved','totalpending','totalpass','totalfaild'));

        $response = array(
            'status' => 1,
            'message' => 'Audit Sheet List',
            'data' => $jsonOutput
        );
        return response(json_encode($response), 200);
    }



    public function qaDashboardMeta($request, $user)
    {
        $ids = [];
        $totalpass = 0;
        $totalfaild = 0;
        $totalsaved = 0;
        $totalpending = 0;
        $cycle = ['start' => '1970-01-01', 'end' => '2120-12-31'];
        // $cycle=['start'=>'17-06-2020','end'=>'17-06-2020'];
        if ($request->has('lob_audit_cycle') && $request->lob_audit_cycle != 'custom') {
            $cycle = $this->getAuditCycle($request->lob_audit_cycle);
        } else if ($request->has('lob_audit_cycle') && $request->lob_audit_cycle == 'custom') {
            $dates = explode(' - ', $request->lob_audit_cycle_custom);
            $cycle = ['start' => Carbon::parse($dates[0])->toDateString(), 'end' => Carbon::parse($dates[1])->toDateString()];
        }
        $query = Audit::with('qmsheet');
        if ($user->hasRole('Admin') != true) {
            $query->where('audited_by_id', $user->id);
        }

        if (isset($request->product)) {
            if ($request->product == "all" || $request->product == "All" || $request->product == "ALL") {

            } else {

                $query->where('product_id', $request->product);
            }

        }

        $audit = $query->get();
        // dd($audit);
        $audit = $audit->filter(function ($item) use ($cycle) {
            return (($item->created_at >= $cycle['start'] . " 00:00:00") && ($item->created_at <= $cycle['end'] . " 23:59:59"));
        });
        if ($request->has('productlob') && $request->productlob != 'all') {
            $audit = $audit->filter(function ($item) use ($request) {
                return ($item->qmsheet->lob == $request->productlob);
            });
        }
        if (count($audit) > 0) {
            $query = Qc::whereIn('audit_id', $audit->pluck('id'))->get()->unique('audit_id');
            // dd($query->pluck('audit_id','id'),$audit->pluck('id'));
            $totalpass = $query->whereIn('status', [1, 2])->count();
            $totalfaild = $query->whereIn('status', [3])->count();
        }
        $savedIds = SavedAudit::whereIn('audit_id', $audit->pluck('id'))->get()->pluck('audit_id')->toArray();
        $totalsaved = Audit::whereIn('id', $savedIds)->count();
        $totalAudit = $audit->count();
        $totalpending = $totalAudit - ($totalpass + $totalfaild);
        $old = $request->all();
        return ['totalAudit' => $totalAudit, 'old' => $old, 'totalsaved' => $totalsaved, 'totalpending' => $totalpending, 'totalpass' => $totalpass, 'totalfaild' => $totalfaild];
    }


    public function getAuditCycle($type)
    {
        $month = Carbon::now()->month;
        $cycle = $this->getCycle($month);
        switch ($type) {
            case 'current':
                return $cycle;
                break;
            case 'last_2':
                $start = $cycle['start'];
                $end = $cycle['end'];
                $cycle['start'] = Carbon::parse($start)->modify('-6 month')->toDateString();
                $cycle['end'] = Carbon::parse($end)->modify('-3 month')->toDateString();
                return $cycle;
                break;
            case 'last_3':
                $start = $cycle['start'];
                $end = $cycle['end'];
                $cycle['start'] = Carbon::parse($start)->modify('-9 month')->toDateString();
                $cycle['end'] = Carbon::parse($end)->modify('-3 month')->toDateString();
                return $cycle;
                break;
            case 'last_4':
                $start = $cycle['start'];
                $end = $cycle['end'];
                $cycle['start'] = Carbon::parse($start)->modify('-12 month')->toDateString();
                $cycle['end'] = Carbon::parse($end)->modify('-3 month')->toDateString();
                return $cycle;
                break;
        }
    }


    function getCycle($month)
    {
        $startdate = '';
        $enddate = '';
        $year = Carbon::now()->year;
        $startMonth = '';
        $endMonth = '';
        if ($month > 0 && $month < 4) {
            $startdate = Carbon::parse('1-1-' . $year)->toDateString();
            $enddate = Carbon::parse('31-3-' . $year)->toDateString();
            // $startMonth=0;
            // $endMonth=3;
        } else if ($month > 3 && $month < 7) {
            $startdate = Carbon::parse('1-4-' . $year)->toDateString();
            $enddate = Carbon::parse('30-6-' . $year)->toDateString();
            // $startMonth=3;
            // $endMonth=6;
        } else if ($month > 6 && $month < 10) {
            $startdate = Carbon::parse('1-7-' . $year)->toDateString();
            $enddate = Carbon::parse('30-9-' . $year)->toDateString();
            // $startMonth=6;
            // $endMonth=9;
        } else if ($month > 9 && $month < 13) {
            $startdate = Carbon::parse('1-10-' . $year)->toDateString();
            $enddate = Carbon::parse('31-12-' . $year)->toDateString();
            // $startMonth=9;
            // $endMonth=12;
        }
        return ['start' => $startdate, 'end' => $enddate];

    }

    

    public function client_dashboard_counts(Request $request)
    {
        if (!$request->header('Authorizations') || $request->header('Authorizations') == "") {
            $data = array('status' => 0, 'message' => 'Authorizations key is required in api headers.', 'data' => array());
            return response(json_encode($data), 200);
        }
        $getUser = User::select('id', 'auth_key','client_id')->where('auth_key', $request->header('Authorizations'))->first();
        if (!$getUser) {
            $data = array('status' => 0, 'message' => 'User not found', 'data' => array());
            return response(json_encode($data), 200);
        }

        $lastMonth = Carbon::now()->subMonth();
        $overallScore = Audit::whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->avg('overall_score');

        //V Old Date was issue for current date filter
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


//--- 1 V Client Dashboard -- Audit Allocation 4 Boxes ----------------------

        //-- Box 1 Total Allocation ----------------
        $totalAllocation = DB::table('audit_allocation')
        ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
            return $query->whereBetween('created_at', [$startDate, $endDate]);
        })
        ->where('client_id', $getUser->client_id) // ✅ Filter by client
        ->distinct()
        ->count('id');


        $totalAllocationByAgency = DB::table('audit_allocation')
        ->join('users', 'audit_allocation.process_review_agency_email', '=', 'users.email')
        ->select(
            'audit_allocation.process_review_agency_email', 
            'users.name as process_review_agency',
            DB::raw('COUNT(audit_allocation.id) as total')
        )
        ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
            return $query->whereBetween('audit_allocation.created_at', [$startDate, $endDate]);
        })
        ->where('audit_allocation.client_id', $getUser->client_id) // ✅ Filter by client
        ->groupBy('audit_allocation.process_review_agency_email', 'users.name')
        ->get();


        //-- Box 2 Total Submitted Audits -------------------------------------
        $totalAudits = DB::table('audits')
                ->where('audits.client_id', $getUser->client_id)
                ->where('status','<=',1)
                ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    return $query->whereBetween('audits.created_at', [$startDate, $endDate]); // Filter by date range
                })
                ->count();

        $savedAudits = DB::table('saved_audits')
        ->join('audits', 'audits.id', '=', 'saved_audits.audit_id')
        ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
        return $query->whereBetween('audits.created_at', [$startDate, $endDate]);
        })
        ->orderBy('audits.audit_agency_id', 'asc')
        ->count('saved_audits.audit_id');

        $totalCompletedAudits = DB::table('audits')
        ->leftJoin('saved_audits', 'audits.id', '=', 'saved_audits.audit_id') // Join with saved_audits
        ->whereNull('saved_audits.audit_id') // Exclude drafts
        ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
            return $query->whereBetween('audits.created_at', [$startDate, $endDate]); // Filter by date range
        })
        ->count('audits.id');


        //$totalCompletedAudits = $totalAudits - $savedAudits;

       
        //-------Total Audit Completed By Agency-----------------------------------
        $totalAuditCompletedByAgency = DB::table('audits')
        ->where('audits.client_id', $getUser->client_id)
        ->join('users', 'audits.audit_agency_id', '=', 'users.id')
        ->where('audits.status','<=',1)
        ->select(
            'audits.audit_agency_id',
            'users.name as agency_name',
            DB::raw('COUNT(audits.id) as total_audits')
        )
        ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
            return $query->whereBetween('audits.created_at', [$startDate, $endDate]);
        })
        ->groupBy('audits.audit_agency_id', 'users.name')
        ->get();
       // ------------Action Plan Start --------------------------------------------->

        $auditsSendForActionPlan = DB::table('closure_audits')->where('status',0)
        ->where('client_id', $getUser->client_id)
        ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('created_at', [$startDate, $endDate]);
            })
        ->count();

        // Total Performed Audits (Status 0 + Status 1 + Status 2)
        $totalPerfomedAudits = DB::table('closure_audits')
        ->where('client_id', $getUser->client_id)
            ->whereIn('status', [0, 1])
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->count();

         // Pending audit status 0 
        $totalPendingAuditsActionPlan = DB::table('closure_audits')->where('status',0)
        ->where('closure_audits.client_id', $getUser->client_id)
        ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('created_at', [$startDate, $endDate]);
            })
        ->count();

        //Approved/Closed audit status 1 
        $totalApprovedAudits = DB::table('closure_audits')->where('status',1)
        ->where('closure_audits.client_id', $getUser->client_id)
        ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('created_at', [$startDate, $endDate]);
            })
        ->count();

        //Pending Count of Audit Agency-Table: audit_closure_artifacts
        $totalPendingAuditsofAA = DB::table('audit_closure_artifacts')
        ->select('audit_id') // Select only the grouped column
        ->where('approval_status', 'Pending')
        ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
            return $query->whereBetween('created_at', [$startDate, $endDate]);
        })
        ->groupBy('audit_closure_artifacts.audit_id')
        ->get()
        ->count();

        $totalPendingAuditsofCA = $totalPendingAuditsActionPlan - $totalPendingAuditsofAA;

//------------Action Plan End ---------------------------------------------->

//----- Box 4 Closed/Overall Audit ----------------------------------------->      
       
        //Last month range (10th of last month to 9th of this month)
        $today = Carbon::now();

        if ($today->day < 10) {
            // If today is before the 10th, consider the range for two months back
            $lastMonthStartDate = $today->copy()->subMonth(2)->day(10)->startOfDay();
            $lastMonthEndDate = $today->copy()->subMonth()->day(9)->endOfDay();
        } else {
            // If today is on or after the 10th, consider the range for last month
            $lastMonthStartDate = $today->copy()->subMonth()->day(10)->startOfDay();
            $lastMonthEndDate = $today->copy()->day(9)->endOfDay();
        }

        $overallAuditlastMonth = DB::table('closure_audits')->where('status', 1)
        ->where('client_id',$getUser->client_id)
        ->whereBetween('created_at', [$lastMonthStartDate, $lastMonthEndDate])
        ->count();
     

        // V Overall Score Last month
        $overallScorelastMonth = DB::table('closure_audits')
        ->where('closure_audits.client_id', $getUser->client_id)
        ->join('audits', 'audits.id', '=', 'closure_audits.audit_id')
        ->where('closure_audits.status', 1)
        ->whereBetween('closure_audits.created_at', [$lastMonthStartDate, $lastMonthEndDate])
        ->avg('audits.overall_score');
        //echo '<pre>'; print_r($overallScorelastMonth); die();

        // Set to 0 if null
        $overallScorelastMonth = $overallScorelastMonth !== null ? $overallScorelastMonth : 0; 


    //-----Closed/Current month's Overall Audit Count------------------------------
        
        $overallAuditcurrentMonth = DB::table('closure_audits')->where('status', 1)
        ->where('client_id', $getUser->client_id)
        ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('created_at', [$startDate, $endDate]);
            })
        ->count();

        //Overall Score Current Month
        //$startDate = Carbon::now()->startOfMonth()->setDay(10);
        //$endDate = Carbon::now()->addMonth()->startOfMonth()->setDay(9)->endOfDay();

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

        //echo "<pre>Start Date: $startDate\nEnd Date: $endDate</pre>"; die();


        //Overall Score Current Months
        $overallScorecurrentMonth = DB::table('closure_audits')
        ->where('closure_audits.client_id',$getUser->client_id)
        ->join('audits', 'audits.id', '=', 'closure_audits.audit_id')
        ->where('closure_audits.status', 1)
        ->whereBetween('audits.created_at', [$startDate, $endDate])
        ->avg('audits.overall_score');

        // Set to 0 if null
        $overallScorecurrentMonth = $overallScorecurrentMonth !== null ? $overallScorecurrentMonth : 0;   

        // V For Green and Red Arrow Calculate percentage difference
        if ($overallAuditlastMonth> 0) {
            $percentageDifference = round((($overallAuditcurrentMonth - $overallAuditlastMonth) / $overallAuditlastMonth) * 100);
        } else {
            $percentageDifference = $overallAuditcurrentMonth > 0 ? 100 : 0;
        }  


        // Sum the overall scores
        $totalOverallScore = $overallScorecurrentMonth + $overallScorelastMonth;

        // Calculate percentage difference
        if ($overallScorelastMonth > 0) {
            $percentageScoreDifference = round((($overallScorecurrentMonth - $overallScorelastMonth) / $overallScorelastMonth) * 100);
        } else {
            $percentageScoreDifference = $overallScorecurrentMonth > 0 ? 100 : 0; // If last month count is 0
        }
        //echo '<pre>'; print_r($percentageScoreDifference); die(); 

//---  4 Boxes Complete ----------------------------------------------------------

       

        $databyZoneRegion = DB::table('audits')
            ->join('agencies', 'audits.agency_id', '=', 'agencies.id')
            ->join('regions', 'agencies.region_id', '=', 'regions.id')
            ->leftJoin('audit_allocation', 'audits.agency_id', '=', 'audit_allocation.agency_id')  // Join on audit_allocation
            ->leftJoin('closure_audits', 'audits.id', '=', 'closure_audits.audit_id')  // Join on closure_audits
            ->select(
                'regions.name as region_name',
                DB::raw('AVG(audits.score_percentage) as average_score'),  // Calculate average score
                DB::raw('COUNT(DISTINCT audits.id) as audit_count'),  // Count of unique audit IDs
                DB::raw('COUNT(DISTINCT audit_allocation.agency_id) as audit_allocation_count'),  // Count of unique agency_id in audit_allocation
                DB::raw('COUNT(DISTINCT audit_allocation.process_review_agency_id) as process_review_agency_count'),  // Count of unique process_review_agency_id
                DB::raw('COUNT(DISTINCT CASE WHEN closure_audits.status = 0 THEN closure_audits.audit_id END) as sent_for_closure_count'),  // Distinct count of status 0
                DB::raw('COUNT(DISTINCT CASE WHEN closure_audits.status = 1 THEN closure_audits.audit_id END) as closure_completed_count')  // Distinct count of status 1
            )
            ->groupBy('regions.name')  // Group by region name
            ->get();

        $auditAgenciesData = DB::table('audit_allocation')
            ->select(
                'process_review_agency',
                DB::raw('SUM(CASE WHEN product = "Card" THEN 1 ELSE 0 END) as assign_card_count'),
                DB::raw('SUM(CASE WHEN product = "Retail" THEN 1 ELSE 0 END) as assign_retail_count'),
                DB::raw('SUM(CASE WHEN product = "RetailCard" THEN 1 ELSE 0 END) as assing_retail_card_count'),
                DB::raw('SUM(CASE WHEN product IN ("Card", "Retail", "RetailCard") THEN 1 ELSE 0 END) as total_assign_count') // Total count of all three
            )
            ->groupBy('process_review_agency')
            ->get();

        $auditAgencyCompleteAudit = DB::table('audits')
            ->select('audit_agency_id', 'product_id', DB::raw('COUNT(*) as product_count'))
            ->groupBy('audit_agency_id', 'product_id')
            ->get();

        $nationalCollManagerAuditData = DB::table('audits')
            ->join('products', 'audits.product_id', '=', 'products.id')
            ->leftJoin('closure_audits', 'audits.id', '=', 'closure_audits.audit_id')
            ->select(
                'audits.lavel_5',
                'users.name as manager_name',
                'products.name as product_name',
                DB::raw('COUNT(DISTINCT audits.id) as audit_count'), // Use DISTINCT here
                DB::raw('SUM(CASE WHEN closure_audits.status = 2 THEN 1 ELSE 0 END) as rejected_count'),
                DB::raw('SUM(CASE WHEN closure_audits.status = 1 THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN closure_audits.status = 0 THEN 1 ELSE 0 END) as pending_count')
            )
            ->join('users', function ($join) {
                $join->on(DB::raw('FIND_IN_SET(users.id, audits.lavel_5)'), '>', DB::raw('0'));
            })
            ->groupBy('users.name', 'products.name', 'audits.lavel_5')
            ->get();



        $auditsByManager = [];

        // Process the results to group by manager and product
        foreach ($nationalCollManagerAuditData as $audit) {
            // Use the lavel_5 directly for processing
            $managerIdsFromAudit = explode(',', $audit->lavel_5);

            foreach ($managerIdsFromAudit as $managerId) {
                $managerId = trim($managerId);

                if (!isset($auditsByManager[$managerId])) {
                    $auditsByManager[$managerId] = [
                        'name' => $audit->manager_name,
                        'total_audit_count' => 0,
                        'products' => [
                            'Retail' => ['rejected' => 0, 'approved' => 0, 'pending' => 0],
                            'Credit Card' => ['rejected' => 0, 'approved' => 0, 'pending' => 0],
                            'Retail/Credit Card' => ['rejected' => 0, 'approved' => 0, 'pending' => 0],
                        ],
                    ];
                }

                // Increment count by 1 for each manager per unique audit
                $auditsByManager[$managerId]['total_audit_count'] += 1; // Now counting 1 for each unique audit

                switch ($audit->product_name) {
                    case 'Retail':
                        $auditsByManager[$managerId]['products']['Retail']['rejected'] += $audit->rejected_count;
                        $auditsByManager[$managerId]['products']['Retail']['approved'] += $audit->approved_count;
                        $auditsByManager[$managerId]['products']['Retail']['pending'] += $audit->pending_count;
                        break;
                    case 'Credit Card':
                        $auditsByManager[$managerId]['products']['Credit Card']['rejected'] += $audit->rejected_count;
                        $auditsByManager[$managerId]['products']['Credit Card']['approved'] += $audit->approved_count;
                        $auditsByManager[$managerId]['products']['Credit Card']['pending'] += $audit->pending_count;
                        break;
                    case 'Retail/Credit Card':
                        $auditsByManager[$managerId]['products']['Retail/Credit Card']['rejected'] += $audit->rejected_count;
                        $auditsByManager[$managerId]['products']['Retail/Credit Card']['approved'] += $audit->approved_count;
                        $auditsByManager[$managerId]['products']['Retail/Credit Card']['pending'] += $audit->pending_count;
                        break;
                }
            }
        }



        $jsonOutput = ['overallScore' => $overallScore, 
        'totalAllocation' => $totalAllocation, 
        'totalAllocationByAgency' => $totalAllocationByAgency, 
        'totalCompletedAudits' => $totalCompletedAudits, 
        'auditsSendForActionPlan' => $auditsSendForActionPlan, 
        'totalPerfomedAudits' =>$totalPerfomedAudits, 
        'totalPendingAuditsActionPlan'=>$totalPendingAuditsActionPlan,
        'totalApprovedAudits'=>$totalApprovedAudits,
        'totalPendingAuditsofAA'=>$totalPendingAuditsofAA,
        'totalPendingAuditsofCA'=>$totalPendingAuditsofCA,
        'overallAuditcurrentMonth' => $overallAuditcurrentMonth,
        'overallScorecurrentMonth'=>$overallScorecurrentMonth, 
        'overallAuditlastMonth' => $overallAuditlastMonth,
        'overallScorelastMonth'=>$overallScorelastMonth,
        'auditAgencyCompleteAudit' => $auditAgencyCompleteAudit, 
        'auditAgenciesData' => $auditAgenciesData, 
        'databyZoneRegion' => $databyZoneRegion, 
        'totalAudits' => $totalAudits, 
        'totalAuditCompletedByAgency' => $totalAuditCompletedByAgency, 
        'auditsByManager' => $auditsByManager];

        $response = array(
            'status' => 1,
            'message' => 'Client data',
            'data' => $jsonOutput
        );
        return response(json_encode($response), 200);
    }


    //V Overall Audit and Overall Score 6 Months Client Dashboard --------->
    public function getOverallAuditandScore(Request $request)
    {
        if (!$request->header('Authorizations') || $request->header('Authorizations') == "") {
            $data = array('status' => 0, 'message' => 'Authorizations key is required in API headers.', 'data' => array());
            return response()->json($data, 200);
        }

        $getUser = User::select('id', 'auth_key')->where('auth_key', $request->header('Authorizations'))->first();
        if (!$getUser) {
            $data = array('status' => 0, 'message' => 'User not found', 'data' => array());
            return response()->json($data, 200);
        }

        $overallAuditLastSixMonths = [];

        for ($i = 0; $i < 6; $i++) {
            // Start on the 10th of the current month
            $startDate = Carbon::now()->subMonths($i)->startOfMonth()->day(10);
            // End on the 9th of the next month
            $endDate = Carbon::now()->subMonths($i - 1)->startOfMonth()->day(9)->endOfDay();

            // Total audits submitted in the date range, excluding drafts
            $auditCount = DB::table('audits')
                ->leftJoin('saved_audits', 'audits.id', '=', 'saved_audits.audit_id') // Join with saved_audits
                ->whereNull('saved_audits.audit_id') // Exclude drafts
                ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    return $query->whereBetween('audits.created_at', [$startDate, $endDate]); // Filter by date range
                })
                ->count('audits.id');

            // Total sum of overall scores for the submitted audits
            $totalSumOverallScored = DB::table('audits')
                ->leftJoin('saved_audits', 'audits.id', '=', 'saved_audits.audit_id') // Join with saved_audits
                ->whereNull('saved_audits.audit_id') // Exclude drafts
                ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    return $query->whereBetween('audits.created_at', [$startDate, $endDate]);
                })
                ->sum(DB::raw('CAST(audits.overall_score AS DECIMAL(10,2))')); // Cast the overall_score as numeric (decimal)

            // Calculate the total scorable points for audits (assuming 100 is the max score per audit)
            $totalScorable = $auditCount * 100;

            // Calculate the overall score if there are any scorable audits
            $overallScore = $totalScorable > 0 ? ($totalSumOverallScored / $totalScorable) * 100 : 0;

            // Count audit allocations in the date range
            $totalAllocations = DB::table('audit_allocation')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count('id');

            // Count pending closure audits in the date range
            $totalPendingAudits = DB::table('closure_audits')
                ->leftJoin('saved_audits', 'closure_audits.audit_id', '=', 'saved_audits.audit_id')
                ->where('closure_audits.status', 0) // Pending status
                ->whereNull('saved_audits.audit_id') // Ensure no match in saved_audits table
                ->whereBetween('closure_audits.created_at', [$startDate, $endDate])
                ->count();

            // Add data for the month to the results
            $overallAuditLastSixMonths[] = [
                'month' => $startDate->format('F Y'),
                'audit_count' => $auditCount,
                'overall_score' => round($overallScore), // Round the overall score
                'allocation_count' => $totalAllocations,
                'pending_count' => $totalPendingAudits,
            ];
        }

        $response = [
            'status' => 1,
            'message' => 'Overall Audit and Score for the Last Six Months',
            'data' => $overallAuditLastSixMonths
        ];

        return response()->json($response, 200);
    }


    //V Overall Audit and Overall Score 6 Months Agency wise Client Dashboard ---->
    public function getoverallAuditScoreAgencyWise(Request $request)
    {
        if (!$request->header('Authorizations') || $request->header('Authorizations') == "") {
            return response()->json([
                'status' => 0,
                'message' => 'Authorizations key is required in API headers.',
                'data' => []
            ], 200);
        }

        $getUser = User::select('id', 'auth_key')->where('auth_key', $request->header('Authorizations'))->first();
        if (!$getUser) {
            return response()->json([
                'status' => 0,
                'message' => 'User not found',
                'data' => []
            ], 200);
        }

        // Fetch unique agency IDs from audit_allocation table
        $auditAgencyIds = DB::table('audit_allocation')->distinct()->pluck('process_review_agency_id');

        $overallAuditLastSixMonthsByAgency = [];

        for ($i = 0; $i < 6; $i++) {
            $startDate = Carbon::now()->subMonths($i)->startOfMonth()->day(10); // Start on the 10th of the current month
            $endDate = Carbon::now()->subMonths($i - 1)->startOfMonth()->day(9)->endOfDay(); // End on the 9th of the next month

            // Overall data (excluding drafts)
            $overallCount = DB::table('audits')
                ->leftJoin('saved_audits', 'audits.id', '=', 'saved_audits.audit_id') // Join with saved_audits
                ->whereNull('saved_audits.audit_id') // Exclude drafts
                ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    return $query->whereBetween('audits.created_at', [$startDate, $endDate]); // Filter by date range
                })
                ->count('audits.id');

            $overallScore = DB::table('audits')
                ->leftJoin('saved_audits', 'audits.id', '=', 'saved_audits.audit_id') // Join with saved_audits
                ->whereNull('saved_audits.audit_id') // Exclude drafts
                ->whereBetween('audits.created_at', [$startDate, $endDate]) // Filter by date range
                ->avg('audits.overall_score');

            $overallScore = $overallScore !== null ? $overallScore : 0;

            // Data for the current month
            $dataForMonth = [
                'month' => $startDate->format('F Y'),
                'overall' => [
                    'audit_count' => $overallCount,
                    'overall_score' => round($overallScore), // Round the overall score
                ],
                'agencies' => [],
            ];

            // Agency-specific data
            foreach ($auditAgencyIds as $agencyId) {
                // Get the agency name
                $agencyName = DB::table('users')
                    ->where('id', $agencyId)
                    ->value('name');

                // Total audits submitted by agency (excluding drafts)
                $agencyCount = DB::table('audits')
                    ->leftJoin('saved_audits', 'audits.id', '=', 'saved_audits.audit_id') // Join with saved_audits
                    ->where('audits.audit_agency_id', $agencyId)
                    ->whereBetween('audits.created_at', [$startDate, $endDate])
                    ->whereNull('saved_audits.audit_id') // Exclude drafts
                    ->count('audits.id');

                // Average score for this agency
                $agencyScore = DB::table('audits')
                    ->leftJoin('saved_audits', 'audits.id', '=', 'saved_audits.audit_id') // Join with saved_audits
                    ->whereNull('saved_audits.audit_id') // Exclude drafts
                    ->where('audits.audit_agency_id', $agencyId) // Filter by agency ID
                    ->whereBetween('audits.created_at', [$startDate, $endDate]) // Filter by date range
                    ->avg('audits.overall_score'); 

                $agencyScore = $agencyScore !== null ? $agencyScore : 0;

                // Total audit allocations for this agency
                $agencyAllocations = DB::table('audit_allocation')
                    ->where('process_review_agency_id', $agencyId)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count('id');

                // Pending audits for this agency from the closure_audits table
                $pendingAudits = DB::table('closure_audits')
                    ->leftJoin('saved_audits', 'closure_audits.audit_id', '=', 'saved_audits.audit_id')
                    ->where('closure_audits.status', 0) // Pending status
                    ->whereNull('saved_audits.audit_id') // saved_audits (exclude drafts)
                    ->whereBetween('closure_audits.created_at', [$startDate, $endDate])
                    ->where('closure_audits.audit_agency_id', $agencyId)
                    ->count('closure_audits.audit_id');

                // Add agency data to the month data
                $dataForMonth['agencies'][] = [
                    'agency_id' => $agencyId,
                    'name' => $agencyName,
                    'audit_count' => $agencyCount,
                    'overall_score' => round($agencyScore), // Round the score
                    'allocation_count' => $agencyAllocations, // Add allocation count
                    'pending_count' => $pendingAudits,       // Add pending audits count
                ];
            }

            // Add the month's data to the overall results
            $overallAuditLastSixMonthsByAgency[] = $dataForMonth;
        }

        // Return the data in response
        return response()->json([
            'status' => 1,
            'message' => 'Overall Audit and Score for the Last Six Months (Agency-Wise)',
            'data' => $overallAuditLastSixMonthsByAgency
        ], 200);
    }






    public function getProductCount_details(Request $request)
    {

        if (!$request->header('Authorizations') || $request->header('Authorizations') == "") {
            $data = array('status' => 0, 'message' => 'Authorizations key is required in api headers.', 'data' => array());
            return response(json_encode($data), 200);
        }
        $getUser = User::select('id', 'auth_key')->where('auth_key', $request->header('Authorizations'))->first();
        if (!$getUser) {
            $data = array('status' => 0, 'message' => 'User not found', 'data' => array());
            return response(json_encode($data), 200);
        }
        $productsCount = Audit::distinct('product_id')->count('product_id');

        $productsData = DB::table('audits')
            ->join('products', 'audits.product_id', '=', 'products.id')
            ->select(
                'products.name',
                'audits.product_id',
                DB::raw('COUNT(*) as total'),
                DB::raw('AVG(audits.score_percentage) as average_score')
            )
            ->groupBy('products.name', 'audits.product_id')
            ->get();

        $jsonOutput = ['productsCount' => $productsCount, 'productsData' => $productsData];

        $response = array(
            'status' => 1,
            'message' => 'Product Count With Details',
            'data' => $jsonOutput
        );
        return response(json_encode($response), 200);
    }


    // V zone wise Data API
    public function getZoneWiseData(Request $request)
    {
        if (!$request->header('Authorizations') || $request->header('Authorizations') == "") {
            $data = array('status' => 0, 'message' => 'Authorizations key is required in api headers.', 'data' => array());
            return response(json_encode($data), 200);
        }

        $getUser = User::select('id', 'auth_key')->where('auth_key', $request->header('Authorizations'))->first();
        if (!$getUser) {
            $data = array('status' => 0, 'message' => 'User not found', 'data' => array());
            return response(json_encode($data), 200);
        }

        $validator = Validator::make($request->all(), [
            //'productId' => 'required',
            //'region' => 'required'
        ]);

        if ($validator->fails()) {
            $data = array('status' => 0, 'message' => 'Validation Error', 'data' => $validator->errors());
            return response(json_encode($data), 200);
        }

        $productId = $request->input('productId');
        $region = $request->input('region');
        $startDatezone = $request->input('start_date');
        $endDatezone = $request->input('end_date');

        // Default date logic if no dates are provided
        if (!$startDatezone || !$endDatezone) {
            $today = Carbon::now();

            if ($today->day < 10) {
                $startDatezone = $today->copy()->subMonth()->startOfMonth()->day(10)->toDateString();
                $endDatezone = $today->copy()->startOfMonth()->day(9)->toDateString();
            } else {
                $startDatezone = $today->copy()->startOfMonth()->day(10)->toDateString();
                $endDatezone = $today->copy()->addMonth()->startOfMonth()->day(9)->toDateString();
            }
        }

        $startDatezone = trim($startDatezone);
        $endDatezone = trim($endDatezone);

        try {
            $startDatezone = Carbon::createFromFormat('Y-m-d', $startDatezone)->startOfDay();
            $endDatezone = Carbon::createFromFormat('Y-m-d', $endDatezone)->endOfDay();
        } catch (\Carbon\Exceptions\InvalidFormatException $e) {
            return response()->json(['error' => 'Invalid date format: ' . $e->getMessage()], 400);
        }

        // Query for audits data
        $databyZoneRegionAudits = DB::table('audits')
            ->join('agencies', 'audits.agency_id', '=', 'agencies.id')
            ->join('regions', 'agencies.region_id', '=', 'regions.id')
            ->leftJoin('closure_audits', 'audits.id', '=', 'closure_audits.audit_id')
            ->leftJoin('saved_audits', 'audits.id', '=', 'saved_audits.audit_id')
            ->select(
                'regions.name as region_name',
                DB::raw('AVG(audits.overall_score) as average_score'),
                DB::raw('COUNT(DISTINCT audits.id) as audit_count'),
                DB::raw('COUNT(DISTINCT CASE WHEN closure_audits.status = 0 THEN closure_audits.audit_id END) as sent_for_closure_count'),
                DB::raw('COUNT(DISTINCT CASE WHEN closure_audits.status = 1 THEN closure_audits.audit_id END) as closure_completed_count')
            )
            ->whereNull('saved_audits.audit_id')
            ->whereBetween('audits.created_at', [$startDatezone, $endDatezone])
            ->groupBy('regions.name')
            ->get();

        // Query for allocation data
        $allocationZoneProduct = DB::table('audit_allocation')
            ->join('regions', 'audit_allocation.region_id', '=', 'regions.id')
            ->selectRaw('
                regions.name as region_name,
                SUM(CASE WHEN product = "Card" THEN 1 ELSE 0 END) as card_count,
                SUM(CASE WHEN product = "Retail" THEN 1 ELSE 0 END) as retail_count,
                SUM(CASE WHEN product = "Retail/Card" THEN 1 ELSE 0 END) as retail_card_count,
                SUM(CASE WHEN product IN ("Card", "Retail", "Retail/Card") THEN 1 ELSE 0 END) as total_allocation
            ')
            ->whereBetween('audit_allocation.created_at', [$startDatezone, $endDatezone])
            ->groupBy('regions.name')
            ->orderBy('regions.name', 'asc')
            ->get();

        // Merge datasets
        $databyZoneRegion = $allocationZoneProduct->map(function ($allocationData) use ($databyZoneRegionAudits) {
            $auditData = $databyZoneRegionAudits->firstWhere('region_name', $allocationData->region_name);

            return (object) [
                'region_name' => $allocationData->region_name,
                'average_score' => $auditData->average_score ?? 0,
                'audit_count' => $auditData->audit_count ?? 0,
                'sent_for_closure_count' => $auditData->sent_for_closure_count ?? 0,
                'closure_completed_count' => $auditData->closure_completed_count ?? 0,
                'total_allocation' => $allocationData->total_allocation ?? 0,
                'card_count' => $allocationData->card_count ?? 0,
                'retail_count' => $allocationData->retail_count ?? 0,
                'retail_card_count' => $allocationData->retail_card_count ?? 0,
            ];
        });

        $response = [
            'status' => 1,
            'message' => 'Zone Wise Data',
            'data' => ['databyZoneRegion' => $databyZoneRegion]
        ];

        return response(json_encode($response), 200);
    }



    public function gradeWiseData(Request $request)
    {
        if (!$request->header('Authorizations') || $request->header('Authorizations') == "") {
            $data = array('status' => 0, 'message' => 'Authorizations key is required in api headers.', 'data' => array());
            return response(json_encode($data), 200);
        }
        $getUser = User::select('id', 'auth_key')->where('auth_key', $request->header('Authorizations'))->first();
        if (!$getUser) {
            $data = array('status' => 0, 'message' => 'User not found', 'data' => array());
            return response(json_encode($data), 200);
        }

        $grade=$request->grade;
        $validator = Validator::make($request->all(), [
           'grade' =>'required'
        ]);
        if($validator->fails())
        {
            $data = array('status'=>0, 'message'=>'Validation Error', 'data'=>$validator->errors());
            return response(json_encode($data),200);
        }else
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


        $data = DB::table('audits')
        ->join('agencies', 'audits.agency_id', '=', 'agencies.id')
        ->join('regions', 'agencies.region_id', '=', 'regions.id')
        ->leftJoin('saved_audits', 'audits.id', '=', 'saved_audits.audit_id')
        ->select(
            'audits.grade',
            'regions.name as region_name',
            DB::raw('COUNT(audits.grade) as grade_count')
        )
        ->where('audits.grade', $grade)
        ->whereNull('saved_audits.audit_id') // Exclude drafts
        ->whereBetween('audits.created_at', [$startDate, $endDate])
        ->groupBy('audits.grade', 'regions.name')
        ->get();


        $jsonOutput = ['data'=>$data];

        $response = array(
            'status' => 1,
            'message' => 'Grade Wise Data',
            'data' => $jsonOutput
        );
        return response(json_encode($response), 200);
        }
    }


    //V Audit Agency API
    public function getAuditAgencyData(Request $request)
    {
        if (!$request->header('Authorizations') || $request->header('Authorizations') == "") {
            return response()->json([
                'status' => 0,
                'message' => 'Authorization key is required in API headers.',
                'data' => []
            ], 200);
        }

        // Authenticate User
        $getUser = User::select('id', 'auth_key')
            ->where('auth_key', $request->header('Authorizations'))
            ->first();

        if (!$getUser) {
            return response()->json([
                'status' => 0,
                'message' => 'User not found',
                'data' => []
            ], 200);
        }

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


        // For Assigned Audits
        $assignAgencyForAuditAgency = DB::table('audit_allocation')
        ->select(
            'users.name as audit_agency_name',
            'audit_allocation.process_review_agency_id as audit_agency_id',
            DB::raw('SUM(CASE WHEN product = "Card" THEN 1 ELSE 0 END) as assign_card_count'),
            DB::raw('SUM(CASE WHEN product = "Retail" THEN 1 ELSE 0 END) as assign_retail_count'),
            DB::raw('SUM(CASE WHEN product = "Retail/Card" THEN 1 ELSE 0 END) as assign_retail_card_count'),
            DB::raw('SUM(CASE WHEN product IN ("Card", "Retail", "Retail/Card") THEN 1 ELSE 0 END) as total_assign_count')
        )
        ->leftJoin('users', 'audit_allocation.process_review_agency_email', '=', 'users.email')
        ->whereBetween(DB::raw('DATE(audit_allocation.created_at)'), [$startDate, $endDate])
        ->groupBy('users.name', 'audit_allocation.process_review_agency_id')
        ->get();

        // For Scheduled Audits
        $scheduledAuditsForAuditAgency = DB::table('auditor_assigns')
        ->select(
            'users.name as audit_agency_name',
            'auditor_assigns.process_review_agency_id as audit_agency_id',
            DB::raw('SUM(CASE WHEN product = "Card" THEN 1 ELSE 0 END) as scheduled_card_count'),
            DB::raw('SUM(CASE WHEN product = "Retail" THEN 1 ELSE 0 END) as scheduled_retail_count'),
            DB::raw('SUM(CASE WHEN product = "Retail/Card" THEN 1 ELSE 0 END) as scheduled_retail_card_count'),
            DB::raw('SUM(CASE WHEN product IN ("Card", "Retail", "Retail/Card") THEN 1 ELSE 0 END) as total_scheduled_count')
        )
        ->leftJoin('users', 'auditor_assigns.process_review_agency_email', '=', 'users.email')
        ->whereBetween(DB::raw('DATE(auditor_assigns.created_at)'), [$startDate, $endDate])
        ->groupBy('users.name', 'auditor_assigns.process_review_agency_id')
        ->get();

        // For Completed and Closure Audits
        $auditsAndCloserCompleteForAuditAgency = DB::table('audits')
        ->select(
            'audits.audit_agency_id',
            'users.name as audit_agency_name',
            DB::raw('COUNT(audits.id) as total_audits'),
            DB::raw('SUM(CASE WHEN products.name = "Card" AND saved_audits.audit_id IS NULL THEN 1 ELSE 0 END) as complete_audits_card'),
            DB::raw('SUM(CASE WHEN products.name = "Retail" AND saved_audits.audit_id IS NULL THEN 1 ELSE 0 END) as complete_audits_retail'),
            DB::raw('SUM(CASE WHEN products.name = "Retail/Card" AND saved_audits.audit_id IS NULL THEN 1 ELSE 0 END) as complete_audits_retail_card'),
            DB::raw('SUM(CASE WHEN products.name = "Card" AND closure_audits.status = 0 THEN 1 ELSE 0 END) as audits_sent_to_closer_card'),
            DB::raw('SUM(CASE WHEN products.name = "Retail" AND closure_audits.status = 0 THEN 1 ELSE 0 END) as audits_sent_to_closer_retail'),
            DB::raw('SUM(CASE WHEN products.name = "Retail/Card" AND closure_audits.status = 0 THEN 1 ELSE 0 END) as audits_sent_to_closer_retail_card'),
            DB::raw('SUM(CASE WHEN products.name = "Card" AND closure_audits.status = 1 THEN 1 ELSE 0 END) as audits_closure_complete_card'),
            DB::raw('SUM(CASE WHEN products.name = "Retail" AND closure_audits.status = 1 THEN 1 ELSE 0 END) as audits_closure_complete_retail'),
            DB::raw('SUM(CASE WHEN products.name = "Retail/Card" AND closure_audits.status = 1 THEN 1 ELSE 0 END) as audits_closure_complete_retail_card')
        )
        ->leftJoin('saved_audits', 'audits.id', '=', 'saved_audits.audit_id')
        ->leftJoin('products', 'audits.product_id', '=', 'products.id')
        ->leftJoin('users', 'audits.audit_agency_id', '=', 'users.id')
        ->leftJoin('closure_audits', function ($join) {
            $join->on('audits.id', '=', 'closure_audits.audit_id')
                ->on('audits.audit_agency_id', '=', 'closure_audits.audit_agency_id');
        })
        ->groupBy('audits.audit_agency_id', 'users.name')
        ->whereBetween(DB::raw('DATE(audits.created_at)'), [$startDate, $endDate])
        ->get()
        ->map(function ($item) {
            // Calculate totals for each group
            $item->complete_audit_total = $item->complete_audits_card + $item->complete_audits_retail + $item->complete_audits_retail_card;
            $item->audits_sent_to_closer_total = $item->audits_sent_to_closer_card + $item->audits_sent_to_closer_retail + $item->audits_sent_to_closer_retail_card;
            $item->audits_closure_complete_total = $item->audits_closure_complete_card + $item->audits_closure_complete_retail + $item->audits_closure_complete_retail_card;
            return $item;
        });

        // Combine Data
        $auditAgenciesData = collect([$assignAgencyForAuditAgency, $scheduledAuditsForAuditAgency])
        ->flatten()
        ->map(function ($item) {
            // Ensure audit_agency_name exists in all collections
            $item->audit_agency_name = $item->audit_agency_name ?? 'Unknown Agency'; // Default if missing
            return $item;
        })
        ->merge($auditsAndCloserCompleteForAuditAgency)
        ->groupBy(function ($item) {
            // Use audit_agency_id and audit_agency_name for grouping
            return $item->audit_agency_name . '_' . $item->audit_agency_id;
        });


        return response()->json([
            'status' => 1,
            'message' => 'Audit agency data retrieved successfully.',
            'data' => $auditAgenciesData
        ], 200);
    }

   //V Collection Agency Trend API
    public function getCollectionAgencyTrend(Request $request)
    {
        if (!$request->header('Authorizations') || $request->header('Authorizations') == "") {
            return response()->json(['status' => 0, 'message' => 'Authorization key is required in API headers.', 'data' => []], 200);
        }

        $getUser = User::select('id', 'auth_key')->where('auth_key', $request->header('Authorizations'))->first();
        if (!$getUser) {
            return response()->json(['status' => 0, 'message' => 'User not found', 'data' => []], 200);
        }
        try {
            
            // Retrieve start_date and end_date from request
           $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

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
            ->get();




            // Fetch six months range
            $sixMonthTrend = [];
            for ($i = 5; $i >= 0; $i--) {
            $startOfMonth = Carbon::now()->subMonths($i)->startOfMonth()->day(10)->toDateString();
            $endOfMonth = Carbon::now()->subMonths($i - 1)->startOfMonth()->day(9)->toDateString();
            //$monthYear = Carbon::now()->subMonths($i)->format('F Y');
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


            // Final JSON Output
            $jsonOutput = [
                'collectionAgencyScores' => $finalDataAgencyTrend->values(),
            ];

            return response()->json(['status' => 1, 'message' => 'Collection Agency Data', 'data' => $jsonOutput], 200);

        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'message' => $e->getMessage(), 'data' => []], 500);
        }
    }



    // public function getCollectionAgencyTrend(Request $request)
    // {
    //     if (!$request->header('Authorizations') || $request->header('Authorizations') == "") {
    //         return response()->json(['status' => 0, 'message' => 'Authorization key is required in API headers.', 'data' => []], 200);
    //     }

    //     $getUser = User::select('id', 'auth_key')->where('auth_key', $request->header('Authorizations'))->first();
    //     if (!$getUser) {
    //         return response()->json(['status' => 0, 'message' => 'User not found', 'data' => []], 200);
    //     }

    //     try {
    //         $startDate = Carbon::now()->subMonths(6)->startOfMonth()->toDateString();
    //         $endDate = Carbon::now()->endOfMonth()->toDateString();

    //          $collAgencyTrendData = DB::table('audits')
    //         ->leftJoin('closure_audits', 'audits.id', '=', 'closure_audits.audit_id')
    //         ->join('users', function ($join) {
    //             $join->on(DB::raw('FIND_IN_SET(users.id, audits.lavel_3)'), '>', DB::raw('0'));
    //         })
    //         ->join('products', 'audits.product_id', '=', 'products.id')
    //         ->join('agencies', 'audits.agency_id', '=', 'agencies.id')
    //         ->whereBetween(DB::raw('DATE(audits.created_at)'), [$startDate, $endDate])
    //         ->select(
    //             'agencies.name as agency_name',
    //             'agencies.location',
    //             'audits.agency_id',
    //             'audits.lavel_3',
    //             'users.id as user_id',
    //             'users.name as collection_manager_name',
    //             DB::raw("DATE_FORMAT(audits.created_at, '%M %Y') as month_year"),
    //             DB::raw('COUNT(DISTINCT audits.id) as audit_count'),
    //             DB::raw('AVG(CASE WHEN audits.overall_score >= 0 AND audits.overall_score <= 100 THEN audits.overall_score ELSE NULL END) as average_score_percentage'),
    //             DB::raw('SUM(CASE WHEN products.name = "Retail" THEN audits.score_percentage ELSE 0 END) as retail_score_percentage'),
    //             DB::raw('SUM(CASE WHEN products.name = "Card" THEN audits.score_percentage ELSE 0 END) as card_score_percentage'),
    //             DB::raw('SUM(CASE WHEN products.name = "Retail/Card" THEN audits.score_percentage ELSE 0 END) as retails_card_score_percentage'),
    //             DB::raw('AVG(CASE WHEN products.name = "Retail" AND audits.overall_score >= 0 AND audits.overall_score <= 100 THEN audits.overall_score ELSE NULL END) as retail_average_score_percentage'),
    //             DB::raw('AVG(CASE WHEN products.name = "Card" AND audits.overall_score >= 0 AND audits.overall_score <= 100 THEN audits.overall_score ELSE NULL END) as card_average_score_percentage'),
    //             DB::raw('AVG(CASE WHEN products.name = "Retail/Card" AND audits.overall_score >= 0 AND audits.overall_score <= 100 THEN audits.overall_score ELSE NULL END) as retails_card_average_score_percentage')
    //         )
    //         ->groupBy(
    //             'audits.agency_id',
    //             'audits.lavel_3',
    //             'agencies.name',
    //             'agencies.location',
    //             'users.id',
    //             'users.name',
    //             DB::raw("DATE_FORMAT(audits.created_at, '%M %Y')"), // Add formatted date here
    //             'products.name' // Ensure all non-aggregated columns are in the GROUP BY
    //         )
    //         ->orderBy('month_year', 'asc')
    //         ->get();

    //         $sixMonthTrend = [];
    //         for ($i = 5; $i >= 0; $i--) {
    //             $monthYear = Carbon::now()->subMonths($i)->format('F Y');
    //             $sixMonthTrend[$monthYear] = [
    //                 'month_year' => $monthYear,
    //                 'average_score' => 0,
    //                 'retail_score_percentage' => 0,
    //                 'card_score_percentage' => 0,
    //                 'retails_card_score_percentage' => 0,
    //                 'retail_average_score_percentage' => 0,
    //                 'card_average_score_percentage' => 0,
    //                 'retails_card_average_score_percentage' => 0,
    //             ];
    //         }

    //         $finalDataAgencyTrend = $collAgencyTrendData->groupBy('agency_id')->map(function ($agencyData) use ($sixMonthTrend) {
    //             $trendData = [];
    //             foreach ($sixMonthTrend as $month => $default) {
    //                 $data = $agencyData->firstWhere('month_year', $month);
    //                 $trendData[] = [
    //                     'month_year' => $month,
    //                     'average_score' => $data->average_score_percentage ?? 0,
    //                     'retail_score_percentage' => $data->retail_score_percentage ?? 0,
    //                     'card_score_percentage' => $data->card_score_percentage ?? 0,
    //                     'retails_card_score_percentage' => $data->retails_card_score_percentage ?? 0,
    //                     'retail_average_score_percentage' => $data->retail_average_score_percentage ?? 0,
    //                     'card_average_score_percentage' => $data->card_average_score_percentage ?? 0,
    //                     'retails_card_average_score_percentage' => $data->retails_card_average_score_percentage ?? 0,
    //                 ];
    //             }
    //             usort($trendData, function ($a, $b) {
    //                 return strtotime($b['month_year']) - strtotime($a['month_year']);
    //             });

    //             $agency = $agencyData->first();
    //             return [
    //                 'agency_id' => $agency->agency_id,
    //                 'agency_name' => $agency->agency_name ?? '',
    //                 'location' => $agency->location ?? '',
    //                 'collection_manager_name' => $agency->collection_manager_name ?? '',
    //                 'audit_count' => $agency->audit_count ?? 0,
    //                 'average_score_percentage' => $agency->average_score_percentage ?? 0,
    //                 'six_month_data' => $trendData,
    //             ];
    //         });

    //         // Final JSON Output
    //         $jsonOutput = [
    //             'collectionAgencyScores' => $finalDataAgencyTrend->values(),
    //         ];

    //         return response()->json(['status' => 1, 'message' => 'Collection Agency Data', 'data' => $jsonOutput], 200);
    //     } catch (\Exception $e) {
    //         return response()->json(['status' => 0, 'message' => $e->getMessage(), 'data' => []], 500);
    //     }
    // }


    
    public function parameterWiseScore(Request $request)
    {
        // Check for Authorizations key in headers
        if (!$request->header('Authorizations') || $request->header('Authorizations') == "") {
            $response = [
                'status' => 0,
                'message' => 'Authorization key is required in API headers.',
                'data' => []
            ];
            return response()->json($response, 200);
        }

        // Validate user based on Authorization key
        $getUser = User::select('id', 'auth_key')
            ->where('auth_key', $request->header('Authorizations'))
            ->first();

        if (!$getUser) {
            $response = [
                'status' => 0,
                'message' => 'User not found.',
                'data' => []
            ];
            return response()->json($response, 200);
        }

        // Handle date range input
       $startDateGrade = $request->input('start_date');
    $endDateGrade = $request->input('end_date');

    // Default date logic if no dates are provided
    if (!$startDateGrade || !$endDateGrade) {
        $today = Carbon::now();

        if ($today->day < 10) {
            $startDateGrade = $today->copy()->subMonth()->startOfMonth()->day(10)->toDateString();
            $endDateGrade = $today->copy()->startOfMonth()->day(9)->toDateString();
        } else {
            $startDateGrade = $today->copy()->startOfMonth()->day(10)->toDateString();
            $endDateGrade = $today->copy()->addMonth()->startOfMonth()->day(9)->toDateString();
        }
    }

     // Trim whitespace from the input dates
    $startDateGrade = trim($startDateGrade);
    $endDateGrade = trim($endDateGrade);

    // Try to convert the dates into Carbon objects
    try {
        // Adjusted format to match the "Y-m-d" format received
        $startDateGrade = Carbon::createFromFormat('Y-m-d', $startDateGrade)->startOfDay(); // Start of the day
        $endDateGrade = Carbon::createFromFormat('Y-m-d', $endDateGrade)->endOfDay(); // End of the day
    } catch (\Carbon\Exceptions\InvalidFormatException $e) {
        // Handle invalid format exception (you can log the error or return a response if needed)
        return response()->json(['error' => 'Invalid date format: ' . $e->getMessage()], 400);
    }
                     
    // V Parameters score
   //$currentMonthParametersAverage 

    // Step 1: Get the total score (with_fatal_score)
    $totalScore = DB::table('audit_parameter_results')
    ->join('qm_sheet_parameters', 'qm_sheet_parameters.id', '=', 'audit_parameter_results.parameter_id') // Join with qm_sheet_parameters
    ->whereNotIn('audit_parameter_results.audit_id', function ($query) {
        $query->select('audit_id')->from('saved_audits'); // Exclude audit_ids present in saved_audits
    })
    ->select(
        'qm_sheet_parameters.parameter as parameter_name', // Select parameter_name for grouping
        DB::raw('SUM(audit_parameter_results.with_fatal_score) as total_score') // Compute the sum of with_fatal_score
    )
    ->whereBetween('audit_parameter_results.created_at', [$startDateGrade, $endDateGrade])
    ->groupBy('qm_sheet_parameters.parameter') // Group by parameter_name
    ->get();

    
    // Step 2: Get the total max score (weight from qm_sheet_sub_parameters)
    $totalMaxScore = DB::table('audit_parameter_results')
    ->join('qm_sheet_parameters', 'qm_sheet_parameters.id', '=', 'audit_parameter_results.parameter_id') 
    ->join('qm_sheet_sub_parameters', 'qm_sheet_parameters.id', '=', 'qm_sheet_sub_parameters.qm_sheet_parameter_id')
    ->whereNotIn('audit_parameter_results.audit_id', function ($query) {
        $query->select('audit_id')->from('saved_audits'); // Exclude audit_ids present in saved_audits
    })
    ->select(
        'qm_sheet_parameters.parameter as parameter_name', // Select parameter_name for grouping
        DB::raw('SUM(qm_sheet_sub_parameters.weight) as total_max_score')
    )
    ->whereBetween('audit_parameter_results.created_at', [$startDateGrade, $endDateGrade])
    ->groupBy('qm_sheet_parameters.parameter') // Group by parameter_name
    ->get();

    
    // Step 3: Combine results and calculate the percentage
    $currentMonthParametersAverage = []; // Initialize the variable

    foreach ($totalScore as $score) {
        $maxScore = $totalMaxScore->firstWhere('parameter_name', $score->parameter_name);

        // Calculate the percentage if totalMaxScore is not zero
        $parameterPercentage = 0;
        if ($maxScore && $maxScore->total_max_score != 0) {
            $parameterPercentage = ($score->total_score / $maxScore->total_max_score) * 100;
        }

        // Store the result in $parametersTotalScore
        $currentMonthParametersAverage[] = [
            'parameter_name' => $score->parameter_name,
            'total_score' => $score->total_score,
            'total_max_score' => $maxScore ? $maxScore->total_max_score : 0,
            'total_parameter_percentage' => $parameterPercentage
        ];
    }

        // Prepare API response
        $response = [
            'status' => 1,
            'message' => 'Parameter Wise Data for the specified date range',
            'data' => $currentMonthParametersAverage
        ];

        return response()->json($response, 200);
    }




    
    // V API Updated...
    public function nationalCollectionManagerData(Request $request)
    {
        if (!$request->header('Authorizations') || $request->header('Authorizations') == "") {
            $data = array('status' => 0, 'message' => 'Authorization key is required in API headers.', 'data' => array());
            return response(json_encode($data), 200);
        }

        $getUser = User::select('id', 'auth_key')->where('auth_key', $request->header('Authorizations'))->first();
        if (!$getUser) {
            $data = array('status' => 0, 'message' => 'User not found', 'data' => array());
            return response(json_encode($data), 200);
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

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
            ->whereBetween(DB::raw('DATE(audits.created_at)'), [$startDate, $endDate])
            ->get();

        $response = array(
            'status' => 1,
            'message' => 'National Collection Manager Data',
            'data' => $nationalCollManagerAuditData
        );

        return response(json_encode($response), 200);
    }




    public function filterByDate(Request $request)
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


        $startDate = trim($startDate);
        $endDate = trim($endDate);

        try {
            $startDate = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();
        } catch (\Carbon\Exceptions\InvalidFormatException $e) {
            return response()->json(['error' => 'Invalid date format: ' . $e->getMessage()], 400);
        }

        $data = YourModel::whereBetween('your_date_column', [$startDate, $endDate])->get();

        return response()->json(['data' => $data, 'start_date' => $startDate, 'end_date' => $endDate]);
    }




    //V  Audit Agency Dashboard API
    public function auditAgencyDashboardCount(Request $request)
    {
        if (!$request->header('Authorizations') || $request->header('Authorizations') == "") {
            $data = array('status' => 0, 'message' => 'Authorizations key is required in api headers.', 'data' => array());
            return response(json_encode($data), 200);
        }
        $getUser = User::select('id', 'auth_key','email')->where('auth_key', $request->header('Authorizations'))->first();
        if (!$getUser) {
            $data = array('status' => 0, 'message' => 'User not found', 'data' => array());
            return response(json_encode($data), 200);
        }


        //V Old Date was issue for current date filter
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


        //---  6 Boxes API Start ------------------------------------------

            $authEmail =$getUser->email;
            $totalAllocation = DB::table('audit_allocation')
            ->where('audit_allocation.process_review_agency_email', $authEmail)
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('audit_allocation.created_at', [$startDate, $endDate]);
            })
            ->distinct()
            ->count('id');

            $totalSubmittedAuditsbyAgency = DB::table('audits')
            ->leftJoin('saved_audits', 'audits.id', '=', 'saved_audits.audit_id')
            ->where('audits.process_review_agency_email', $authEmail)
            ->whereNull('saved_audits.audit_id') // Exclude drafts
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('audits.created_at', [$startDate, $endDate]);
            })
            ->count('audits.id');

            $totalSavedAuditsbyAgency = DB::table('audits')
            ->leftJoin('saved_audits', 'audits.id', '=', 'saved_audits.audit_id')
            ->where('audits.process_review_agency_email', $authEmail)
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('audits.created_at', [$startDate, $endDate]);
            })
            ->count('saved_audits.id');

            //Pending audit status 1
            $authId = $getUser->id;
            $auditSendForActionPlan = DB::table('closure_audits')
            ->where('status', 0) 
            ->where('audit_agency_id', $authId)
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('closure_audits.created_at', [$startDate, $endDate]);
            }) 
            ->count('id');

            //V Received Action Plan
            $authId = Auth::user()->id;
            $receivedforActionPlanAudits = DB::table('closure_audits')
            ->join('audit_closure_artifacts', 'closure_audits.audit_id', '=', 'audit_closure_artifacts.audit_id')
            ->where('closure_audits.status', 0) // Make sure status is 0
            ->where('audit_agency_id', $authId)
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('closure_audits.created_at', [$startDate, $endDate]); // Date filter
            })
            ->distinct('audit_closure_artifacts.audit_id')
            ->count('audit_closure_artifacts.audit_id');


            //Closed/Approved audit status 0 
            $authId = $getUser->id;
            $totalClosedAudits = DB::table('closure_audits')
            ->where('status', 1) 
            ->where('audit_agency_id', $authId)
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('closure_audits.created_at', [$startDate, $endDate]);
            })  
            ->count('id');
    
        //---  6 Boxes API Complete ------------------------------------------

       
        $jsonOutput = [ 'totalAllocation' => $totalAllocation, 
        'totalSubmittedAuditsbyAgency' => $totalSubmittedAuditsbyAgency,
        'totalSavedAuditsbyAgency' => $totalSavedAuditsbyAgency,
        'auditSendForActionPlan' => $auditSendForActionPlan,
        'receivedforActionPlanAudits' => $receivedforActionPlanAudits,
        'totalClosedAudits' => $totalClosedAudits];

        $response = array(
            'status' => 1,
            'message' => 'Client data',
            'data' => $jsonOutput
        );
        return response(json_encode($response), 200);
    }



    //V  Auditor Dashboard API
    public function auditorDashboardCount(Request $request)
    {
        // Check if Authorization header is provided
        if (!$request->header('Authorizations') || trim($request->header('Authorizations')) == "") {
            return response()->json([
                'status' => 0,
                'message' => 'Authorization key is required in API headers.',
                'data' => []
            ], 200);
        }

        // Fetch the user with the provided auth_key
        $getUser = User::select('id', 'auth_key', 'email')
            ->where('auth_key', $request->header('Authorizations'))
            ->first();

        if (!$getUser) {
            return response()->json([
                'status' => 0,
                'message' => 'User not found',
                'data' => []
            ], 200);
        }

        $auditCycle = AuditCycle::where('client_id', $getUser->client_id)
            ->orderBy('id', 'desc')
            ->limit(6)
            ->get()
            ->values();

        $selectedCycleId = $request->audit_cycle_id;

        if ($selectedCycleId) {

            $selectedCycle = AuditCycle::where('client_id', $getUser->client_id)
                ->where('id', $selectedCycleId)
                ->first();
        } else {

            $selectedCycle = AuditCycle::where('client_id', $getUser->client_id)
                ->where('status', 1)
                ->first();
        }

        // Handle date input
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Default date range if none is provided
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

        // Validate and parse dates
        try {
            $startDate = Carbon::createFromFormat('Y-m-d', trim($startDate))->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', trim($endDate))->endOfDay();
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Invalid date format. Use Y-m-d.',
                'data' => []
            ], 400);
        }

        // Fetch counts for the dashboard
        try {
            $authEmail = $getUser->email;
            $authId = $getUser->id;

            // Total audits assigned to the auditor
            $totalAssign = DB::table('auditor_assigns')
                ->where('auditor_email', $authEmail)
                ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    return $query->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->distinct()
                ->count('id');

            // Total completed audits
            $totalCompletedAudits = DB::table('audits')
                ->leftJoin('saved_audits', 'audits.id', '=', 'saved_audits.audit_id')
                ->whereNull('saved_audits.audit_id') // Exclude drafts
                ->where('audits.audited_by_id', $authId)
                ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    return $query->whereBetween('audits.created_at', [$startDate, $endDate]);
                })
                ->distinct()
                ->count('audits.id');

            // Total pending audits
            $totalPending = $totalAssign - $totalCompletedAudits;

            // Total saved audits (drafts)
            $totalSavedAudits = DB::table('saved_audits')
                ->join('audits', 'saved_audits.audit_id', '=', 'audits.id')
                ->where('audits.audited_by_id', $authId)
                ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    return $query->whereBetween('saved_audits.created_at', [$startDate, $endDate]);
                })
                ->count('saved_audits.id');

            // Prepare response data
            $jsonOutput = [
                'audit_cycle' => $auditCycle,
                'selected_cycle' => $selectedCycle,
                'totalAssign' => $totalAssign,
                'totalCompletedAudits' => $totalCompletedAudits,
                'totalPending' => $totalPending,
                'totalSavedAudits' => $totalSavedAudits,
            ];

            return response()->json([
                'status' => 1,
                'message' => 'Auditor dashboard data retrieved successfully.',
                'data' => $jsonOutput,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'An error occurred: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }


    



}
