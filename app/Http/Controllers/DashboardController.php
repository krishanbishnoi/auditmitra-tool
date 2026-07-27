<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Branch;
use App\Model\Branchable;
use App\Model\ProductUser;
use App\Agency;
use App\Yard;
use App\Qc;
use App\Model\Products;
use App\Audit;
use App\AuditAlertBox;
use App\AuditCycle;
use App\AuditParameterResult;
use App\AuditResult;
// use App\Model\ClosureAudit;
use App\QmSheet;
// use DB;
use Auth;
use Carbon\Carbon;
use App\SavedAudit;
use App\SavedQcAudit;
use App\Model\BranchRepo;
use App\Model\AgencyRepo;
use App\RedAlert;
use App\Model\Role;
use App\User;
use App\Exports\AuditsExport;
use App\Exports\AuditsExportCategory;
use App\Exports\OpenPointersDump;
use App\Exports\OpenPointersWithSummaryExport;
use App\Exports\ScheduleAuditExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    // getting QA Performance table record 

    // public function qaAuditTable()
    // {
    //    $auditors  = DB::table('audits')
    //         ->join('users', 'users.id', '=', 'audits.audited_by_id')
    //         ->select(
    //             'users.name',
    //             DB::raw('COUNT(audits.id) as total_audits'),
    //             DB::raw('ROUND(AVG(audits.overall_score),2) as average_score'),
    //             DB::raw('ROUND(AVG(audits.score_percentage),2) as score_percent')
    //         )
    //         ->groupBy('audits.audited_by_id')
    //         ->get();
    // dd($auditors);
    //     return view('dashboard', compact('auditors'));
    // }


    function getUserBranch()
    {
        $user = Auth::user();
        if (Auth::user()->hasRole('Client')) {
            $branchable = Branchable::get()->pluck('agency_id');
        } else {
            $branchable = Branchable::where('manager_id', $user->id)->get()->pluck('agency_id');
        }

        $branch = Agency::whereIn('id', $branchable)->get();
        return $branch->pluck('id');
    }

    public function index(Request $request)
    {
        //echo "Hello"; die();

        //set_time_limit(0);

        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 600);


        $user = Auth::user();
        $client = \App\User::find($user->client_id);

        // *****************For legal dashbaord *********************
        if ($client->is_legal == 1 && $client->is_compliance == 0) {
            return redirect()->route('legal.dashboard');
        }
        // *****************For legal dashbaord upper condition applied *********************



        $totalalert = RedAlert::count();

        // echo '<pre>'; print_r($user); die;
        if ($user->hasRole('Quality Auditor') && $user->hasRole('Admin') != true) {
            // echo '1'; die;
            return $this->qaDashboard($request);
        } else if ($user->hasRole('Quality Control') && $user->hasRole('Admin') != true) {
            //  echo '2'; die;
            return $this->qcDashboard($request);
        } elseif ($user->hasRole('Admin')) {
            // echo '3'; die;
            $qa = [];
            $qc = [];
            if ($user->hasRole('Admin')) {
                $qa = $this->qaDashboardMeta($request);
                $qc = $this->qcDashboardMeta($request);
            }
            $branch = Branch::get(['id', 'name']);
            // $agency = Agency::get(['id', 'name']);
            $current_cycle = DB::table('audit_cycles')
                ->where('status', '=', '1')
                ->where('client_id', auth()->user()->client_id)
                ->first();

            $authEmail = Auth::user()->email;
            $agency = DB::table('audits')
                ->join('agencies', 'audits.agency_id', '=', 'agencies.id')
                ->select('agencies.id', 'agencies.name')
                ->where('audits.process_review_agency_email', $authEmail)
                ->distinct()
                ->get();

            //---------V Audit Agency Dashboard Start -------------------------->
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

            $authEmail = Auth::user()->email;
            $totalAllocation = DB::table('audit_allocation')
                ->where('audit_allocation.process_review_agency_email', $authEmail)
                ->where('audit_cycle_id', $current_cycle->id)
                ->distinct()
                ->count('id');



            // $totalSubmittedAuditsbyAgency = DB::table('audits')
            //     ->leftJoin('saved_audits', 'audits.id', '=', 'saved_audits.audit_id')
            //     ->where('audits.process_review_agency_email', $authEmail)
            //     ->whereNull('saved_audits.audit_id') // Exclude drafts
            //     ->where('audits.audit_cycle_id', $current_cycle->id)
            //     ->count('audits.id');

            $totalSubmittedAuditsbyAgency = DB::table('audits')
                ->where('process_review_agency_email', $authEmail)
                ->where('status', '<=', 1)
                ->where('audits.audit_cycle_id', $current_cycle->id)
                ->count();

            //Temproary condition to show correct data in demo
            if ($totalSubmittedAuditsbyAgency > $totalAllocation) {
                //$totalPending=0;
                $totalSubmittedAuditsbyAgency = $totalAllocation;
            }



            $totalSavedAuditsbyAgency = DB::table('audits')
                ->where('process_review_agency_email', $authEmail)
                ->where('status', 5) // Only saved audits
                ->where('audits.audit_cycle_id', $current_cycle->id)
                ->count();


            //Pending audit status 1
            $authId = Auth::user()->id;
            $auditSendForActionPlan = DB::table('closure_audits')
                ->where('status', 0)
                ->where('audit_agency_id', $authId)
                ->whereIn('audit_id', function ($query) use ($current_cycle) {
                    $query->select('id')
                        ->from('audits')
                        ->where('audit_cycle_id', $current_cycle->id);
                })
                ->count('id');


            //V Received Action Plan
            $authId = Auth::user()->id;
            $receivedforActionPlanAudits = DB::table('closure_audits')
                ->join('audit_closure_artifacts', 'closure_audits.audit_id', '=', 'audit_closure_artifacts.audit_id')
                ->where('closure_audits.status', 0) // Make sure status is 0
                ->where('audit_agency_id', $authId)
                ->whereIn('closure_audits.audit_id', function ($query) use ($current_cycle) {
                    $query->select('id')
                        ->from('audits')
                        ->where('audit_cycle_id', $current_cycle->id);
                })
                ->distinct('audit_closure_artifacts.audit_id')
                ->count('audit_closure_artifacts.audit_id');


            //Closed/Approved audit status 0 
            $authId = Auth::user()->id;
            $totalClosedAudits = DB::table('closure_audits')
                ->where('status', 1)
                ->where('audit_agency_id', $authId)
                ->whereIn('audit_id', function ($query) use ($current_cycle) {
                    $query->select('id')
                        ->from('audits')
                        ->where('audit_cycle_id', $current_cycle->id);
                })
                ->count('closure_audits.id');

            //echo '<pre>'; print_r($totalSubmittedAuditsbyAgency); exit();

            // qa table data
            $auditors  = DB::table('audits')
                ->join('users', 'users.id', '=', 'audits.audited_by_id')
                ->select(
                    'users.name',
                    DB::raw('COUNT(audits.id) as total_audits'),
                    DB::raw('ROUND(AVG(audits.overall_score),2) as average_score'),
                    DB::raw('ROUND(AVG(audits.score_percentage),2) as score_percent')
                )
                ->groupBy('audits.audited_by_id')->
                orderByDesc('audited_by_id')->
                where('users.client_id', Auth::user()->client_id)                
                ->get();

            return view('dashboard', compact('totalalert', 'qa', 'qc', 'branch', 'agency', 'totalAllocation', 'totalSubmittedAuditsbyAgency', 'totalSavedAuditsbyAgency', 'auditSendForActionPlan', 'receivedforActionPlanAudits', 'totalClosedAudits', 'auditors'));
            //---------V Audit Agency Dashboard End ------------------------------------>


            


        } elseif ($user->hasRole('Client|Client(External)')) {

            // if ($user->client_id == 13 || $user->client_id == 15 || $user->client_id = 74) {
            if (auth()->user()->client_id == 2 || auth()->user()->client_id == 298 || auth()->user()->client_id == 288) {
                return redirect('newdashboard');
            }
            return redirect('getClientDashboard');
            // }
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



            //--- 1 V Client Dashboard -- Audit Allocation 4 Boxes --------------------

            //-- Box 1 Total Allocation ----------------


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
                ->where('audit_allocation.client_id', auth()->user()->client_id) // ✅ Filter by client
                ->groupBy('audit_allocation.process_review_agency_email', 'users.name')
                ->get();
            $totalAllocation = 0;
            foreach ($totalAllocationByAgency as $data) {
                $totalAllocation += $data->total;
            }


            //-- Box 2 Total Submitted Audits -------------------------------------

            $totalAuditCompletedByAgency = DB::table('audits')
                ->where('audits.client_id', auth()->user()->client_id)
                ->join('users', 'audits.audit_agency_id', '=', 'users.id')
                ->where('audits.status', '<=', 1)
                ->select(
                    'audits.audit_agency_id',
                    'users.name as agency_name',
                    DB::raw('COUNT(audits.id) as completed_audits')
                )
                ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    return $query->whereBetween('audits.created_at', [$startDate, $endDate]);
                })
                ->groupBy('audits.audit_agency_id', 'users.name')
                ->get();

            $totalSubmittedAudits = 0;
            foreach ($totalAuditCompletedByAgency as $data) {
                $totalSubmittedAudits += $data->completed_audits;
            }


            //echo '<pre>'; print_r($totalAuditCompletedByAgency); die();  

            //------ Box 3 Send for Action Planning ------------------------------------------- 

            // Total Performed Audits (Status 0 + Status 1 + Status 2)
            $totalPerfomedAudits = DB::table('closure_audits')
                ->where('client_id', auth()->user()->client_id)
                ->whereIn('status', [0, 1])
                ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    return $query->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->count();

            //Pending audit status 0 
            $totalPendingAuditsActionPlan = DB::table('closure_audits')->where('status', 0)
                ->where('closure_audits.client_id', auth()->user()->client_id)
                ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    return $query->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->count();

            //Closed/Approved audit status 1 
            $totalApprovedAudits = DB::table('closure_audits')->where('status', 1)
                ->where('closure_audits.client_id', auth()->user()->client_id)
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


            //----- Box 4 Closed Audit ----------------------------------------->      

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
                ->where('client_id', auth()->user()->client_id)
                ->whereBetween('created_at', [$lastMonthStartDate, $lastMonthEndDate])
                ->count();


            // V Overall Score Last month
            $overallScorelastMonth = DB::table('closure_audits')
                ->where('closure_audits.client_id', auth()->user()->client_id)
                ->join('audits', 'audits.id', '=', 'closure_audits.audit_id')
                ->where('closure_audits.status', 1)
                ->whereBetween('closure_audits.created_at', [$lastMonthStartDate, $lastMonthEndDate])
                ->avg('audits.overall_score');
            //echo '<pre>'; print_r($overallScorelastMonth); die();

            // Set to 0 if null
            $overallScorelastMonth = $overallScorelastMonth !== null ? $overallScorelastMonth : 0;


            //-----Closed/Current month Audit Count------------------------------>
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
                ->where('client_id', auth()->user()->client_id)
                ->whereBetween('created_at', [$lastMonthStartDate, $lastMonthEndDate])
                ->count();


            // V Overall Score Last month
            $overallScorelastMonth = DB::table('closure_audits')
                ->where('closure_audits.client_id', auth()->user()->client_id)
                ->join('audits', 'audits.id', '=', 'closure_audits.audit_id')
                ->where('closure_audits.status', 1)
                ->whereBetween('closure_audits.created_at', [$lastMonthStartDate, $lastMonthEndDate])
                ->avg('audits.overall_score');
            //echo '<pre>'; print_r($overallScorelastMonth); die();

            // Set to 0 if null
            $overallScorelastMonth = $overallScorelastMonth !== null ? $overallScorelastMonth : 0;


            //-----Closed/Current month's Overall Audit Count------------------------------

            $overallAuditcurrentMonth = DB::table('closure_audits')->where('status', 1)
                ->where('client_id', auth()->user()->client_id)
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
                ->where('closure_audits.client_id', auth()->user()->client_id)
                ->join('audits', 'audits.id', '=', 'closure_audits.audit_id')
                ->where('closure_audits.status', 1)
                ->whereBetween('audits.created_at', [$startDate, $endDate])
                ->avg('audits.overall_score');



            // Set to 0 if null
            $overallScorecurrentMonth = $overallScorecurrentMonth !== null ? $overallScorecurrentMonth : 0;


            // V For Green and Red Arrow Calculate percentage difference
            if ($overallAuditlastMonth > 0) {
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





            //----2 V Trend Last Six Months and Overall Score for Six Months Start--------

            $overallAuditLastSixMonths = [];

            $months = [];

            for ($i = 0; $i < 6; $i++) {
                $months[] = date('Y-m', mktime(0, 0, 0, date('m') - $i, 1, date('Y')));
            }



            foreach ($months as $m) {
                list($year, $month) = explode('-', $m); // e.g., '2025', '07'

                // Start date
                $startDateRaw = mktime(0, 0, 0, $month, 9, $year);
                $startDate = date('Y-m-d', $startDateRaw);       // e.g., 10-Jul-2025
                $startLabel = date('M-Y', $startDateRaw);        // e.g., Jul-2025

                // Previous month
                $prevMonthTimestamp = mktime(0, 0, 0, $month - 1, 10, $year);
                $endDate = date('Y-m-d', $prevMonthTimestamp);   // e.g., 09-Jun-2025
                $endLabel = date('M-Y', $prevMonthTimestamp);    // e.g., Jun-2025

                // echo "$startLabel (from $startDate) <----> $endLabel (to $endDate)<br/>";

                // Total audits submitted in the date range
                // Total audits submitted in the date range
                $auditCount = DB::table('audits')->where('audits.client_id', auth()->user()->client_id)
                    ->leftJoin('saved_audits', 'audits.id', '=', 'saved_audits.audit_id') // Join with saved_audits
                    ->whereNull('saved_audits.audit_id') // Exclude drafts
                    ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                        return $query->whereBetween('audits.created_at', [$startDate, $endDate]); // Filter by date range
                    })
                    ->count('audits.id');

                $totalSumOverallScored = DB::table('audits')->where('audits.client_id', auth()->user()->client_id)
                    ->leftJoin('saved_audits', 'audits.id', '=', 'saved_audits.audit_id') // Join with saved_audits
                    ->whereNull('saved_audits.audit_id') // Exclude drafts
                    ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                        return $query->whereBetween('audits.created_at', [$startDate, $endDate]);
                    })
                    ->sum(DB::raw('CAST(audits.overall_score AS DECIMAL(10,2))')); // Cast the overall_score as numeric (decimal)

                $totalScorable = $auditCount * 100;

                //$totalScorable= 115;

                if ($totalScorable > 0) {
                    $overallScore = ($totalSumOverallScored / $totalScorable) * 100;
                } else {
                    $overallScore = 0;
                }


                // Count audit allocations in the date range
                $totalAllocations = DB::table('audit_allocation')->where('client_id', auth()->user()->client_id)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count('id');

                // Count pending closure audits in the date range
                $totalPendingAudits = DB::table('closure_audits')->where('closure_audits.client_id', auth()->user()->client_id)
                    ->leftJoin('saved_audits', 'closure_audits.audit_id', '=', 'saved_audits.audit_id')
                    ->where('closure_audits.status', 0)
                    ->whereNull('saved_audits.audit_id') // Ensure no match in saved_audits table
                    ->whereBetween('closure_audits.created_at', [$startDate, $endDate])
                    ->count();

                // Add data for the month to the results
                $overallAuditLastSixMonths[] = [
                    'month' => $startLabel,
                    'audit_count' => $auditCount,
                    'overall_score' => round($overallScore),
                    'allocation_count' => $totalAllocations,
                    'pending_count' => $totalPendingAudits,
                ];
            }


            //--------Same 6 Months data by Agency Wise------------------------- 

            $overallAuditLastSixMonthsByAgency = [];
            //$auditAgencyIds = DB::table('audits')->distinct()->pluck('audit_agency_id');
            $auditAgencyIds = DB::table('audit_allocation')->where('client_id', auth()->user()->client_id)->distinct()->pluck('process_review_agency_id');

            foreach ($months as $m) {
                list($year, $month) = explode('-', $m); // e.g., '2025', '07'

                // Start date
                $startDateRaw = mktime(0, 0, 0, $month, 9, $year);
                $startDate = date('d-M-Y', $startDateRaw);       // e.g., 10-Jul-2025
                $startLabel = date('M-Y', $startDateRaw);        // e.g., Jul-2025

                // Previous month
                $prevMonthTimestamp = mktime(0, 0, 0, $month - 1, 10, $year);
                $endDate = date('d-M-Y', $prevMonthTimestamp);   // e.g., 09-Jun-2025
                $endLabel = date('M-Y', $prevMonthTimestamp);    // e.g., Jun-2025

                //echo "$startLabel (from $startDate) <----> $endLabel (to $endDate)<br/>";

                // Overall data
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




                $dataForMonth = [
                    'month' => $startLabel,
                    'overall' => [
                        'audit_count' => $overallCount,
                        'overall_score' => round($overallScore),
                    ],
                    'agencies' => [],
                ];

                // Agency-specific data with agency name
                foreach ($auditAgencyIds as $agencyId) {
                    // Get the agency name
                    $agencyName = DB::table('users')
                        ->where('id', $agencyId)
                        ->value('name');

                    // Total audits submitted by agency
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

                    // Pending audits for this agency from closure_audits table
                    $pendingAudits = DB::table('closure_audits')
                        ->leftJoin('saved_audits', 'closure_audits.audit_id', '=', 'saved_audits.audit_id')
                        ->where('closure_audits.status', 0)
                        ->whereNull('saved_audits.audit_id') // saved_audits (exclude drafts)
                        ->whereBetween('closure_audits.created_at', [$startDate, $endDate])
                        ->where('closure_audits.audit_agency_id', $agencyId)
                        ->count('closure_audits.audit_id');


                    // Add agency data with agency name to the month data
                    $dataForMonth['agencies'][$agencyId] = [
                        'name' => $agencyName,
                        'audit_count' => $agencyCount,
                        'overall_score' => round($agencyScore),
                        'allocation_count' => $agencyAllocations, // Added field for allocation count
                        'pending_count' => $pendingAudits,       // Added field for pending audits
                    ];
                }

                $overallAuditLastSixMonthsByAgency[] = $dataForMonth;
            }

            // Fetch agency names for mapping (optional)
            $agencyNames = DB::table('users')->whereIn('id', $auditAgencyIds)->pluck('name', 'id');

            //----2 V Overall Audit and Overall Score for Six Months Agency wise End--------  



            //--- 3 V Client Dashboard -- ZONE WISE DATA  ----------------------------------
            $averageScoresByGradeAndRegion = DB::table('audits')
                ->join('agencies', 'audits.agency_id', '=', 'agencies.id')
                ->join('regions', 'agencies.region_id', '=', 'regions.id')
                ->select('audits.grade', 'agencies.region_id', 'regions.name as region_name', DB::raw('AVG(audits.score_percentage) as average_score'))
                ->groupBy('audits.grade', 'agencies.region_id', 'regions.name')
                ->get();

            //echo '<pre>'; print_r($averageScoresByGradeAndRegion); die();


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

            // Trim whitespace from the input dates
            $startDatezone = trim($startDatezone);
            $endDatezone = trim($endDatezone);

            // Try to convert the dates into Carbon objects
            try {
                // Adjusted format to match the "Y-m-d" format received
                $startDatezone = Carbon::createFromFormat('Y-m-d', $startDatezone)->startOfDay(); // Start of the day
                $endDatezone = Carbon::createFromFormat('Y-m-d', $endDatezone)->endOfDay(); // End of the day
            } catch (\Carbon\Exceptions\InvalidFormatException $e) {
                // Handle invalid format exception (you can log the error or return a response if needed)
                return response()->json(['error' => 'Invalid date format: ' . $e->getMessage()], 400);
            }

            // Query for audits data
            // Query for audit data
            $databyZoneRegionAudits = DB::table('audits')->where('audits.client_id', auth()->user()->client_id)
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
                ->whereNull('saved_audits.audit_id') // saved_audits (exclude drafts)
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

            // Merging the datasets from both queries based on region
            $databyZoneRegion = $allocationZoneProduct->map(function ($allocationData) use ($databyZoneRegionAudits) {
                // Find matching audit data for the region
                $auditData = $databyZoneRegionAudits->firstWhere('region_name', $allocationData->region_name);

                // Prepare the final region data object
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


            //echo '<pre>'; print_r($allocationZoneProduct); die();


            //--- 3 V Client Dashboard -- ZONE WISE DATA  Completed --------------------



            //--- 4 V Client Dashboard -- Parameter Score ------------------------------ 
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
            $totalScore = DB::table('audit_parameter_results')->where('audit_parameter_results.client_id', auth()->user()->client_id)
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
            $parametersTotalScore = []; // Initialize the variable

            foreach ($totalScore as $score) {
                $maxScore = $totalMaxScore->firstWhere('parameter_name', $score->parameter_name);

                // Calculate the percentage if totalMaxScore is not zero
                $parameterPercentage = 0;
                if ($maxScore && $maxScore->total_max_score != 0) {
                    $parameterPercentage = ($score->total_score / $maxScore->total_max_score) * 100;
                }

                // Store the result in $parametersTotalScore
                $parametersTotalScore[] = [
                    'parameter_name' => $score->parameter_name,
                    'total_score' => $score->total_score,
                    'total_max_score' => $maxScore ? $maxScore->total_max_score : 0,
                    'total_parameter_percentage' => $parameterPercentage
                ];
            }

            //--- 5 V Audit Agency --------------------------------------------------------- 

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


            //echo '<pre>'; print_r($auditAgenciesData); die();





            // V currently not in use
            $auditAgenciesPrecentage = DB::table('audits')
                ->join('audit_allocation', 'audits.audit_agency_id', '=', 'audit_allocation.process_review_agency_id')
                ->select('audit_allocation.process_review_agency', 'audits.audit_agency_id', DB::raw('AVG(audits.score_percentage) as average_score_percentage'))
                ->groupBy('audit_allocation.process_review_agency', 'audits.audit_agency_id')
                ->get();


            //--- 5 V Audit Agency End -----------------------------------------------------


            //--- 6 V Collection Agency Trend Start --------------------------------------
            // Fetch input dates or set default range
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
                    'users.name  as collection_manager_name',
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

            //--- 6 V Collection Agency Trend End --------------------------------> 



            //--- 7 V National Collection Manager -------------------------------->

            // Fetch input dates or set default range
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

            //echo '<pre>'; print_r($nationalCollManagerAuditData); die();   



            //---V Audit Dump Download Start ------------------->
            $agency = Agency::get()->where('client_id', auth()->user()->client_id);

            $allAgencies = DB::table('agencies')->where('client_id', auth()->user()->client_id)->select('id', 'name')->get();



            $roles = Role::all();

            $auditAgencyName = DB::table('users')
                ->where('users.client_id', auth()->user()->client_id)
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->where('roles.name', 'Admin')
                ->select('users.id', 'users.name')
                ->get();

            $agencies = DB::table('agencies')
                ->join('audits', 'agencies.id', '=', 'audits.agency_id')->where('audits.client_id', auth()->user()->client_id)
                ->select('agencies.id', 'agencies.name')
                ->distinct()
                ->get();

            $closureData = [
                'send_for_closure' => DB::table('closure_audits')->where('status', 0)->where('client_id', auth()->user()->client_id)->count(),
                'approved' => DB::table('closure_audits')->where('status', 1)->where('client_id', auth()->user()->client_id)->count(),
                'rejected' => DB::table('closure_audits')->where('status', 2)->where('client_id', auth()->user()->client_id)->count(),
            ];



            $productAuditData = DB::table('audits')
                ->select('agency_product', DB::raw('COUNT(id) as audit_count'))
                ->where('client_id', auth()->user()->client_id)
                ->where('status', '<=', 2)
                ->groupBy('agency_product')
                ->get();


            $productNames = $productAuditData->pluck('agency_product');
            $auditCounts = $productAuditData->pluck('audit_count');



            $months = collect();
            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $months->push([
                    'month_number' => $date->format('m'),
                    'month_name' => $date->format('M'),
                    'year' => $date->year
                ]);
            }

            // Step 2: Get audit counts for those months
            $auditData = DB::table('audit_allocation')->where('client_id', auth()->user()->client_id)
                ->select(
                    DB::raw("MONTH(created_at) as month_number"),
                    DB::raw("YEAR(created_at) as year"),
                    DB::raw("COUNT(*) as total")
                )
                ->whereBetween('created_at', [now()->subMonths(5)->startOfMonth(), now()->endOfMonth()])
                ->groupBy(DB::raw("MONTH(created_at), YEAR(created_at)"))
                ->get();

            // Step 3: Merge with generated months to ensure 0s where missing
            $finalData = $months->map(function ($month) use ($auditData) {
                $record = $auditData->first(function ($item) use ($month) {
                    return $item->month_number == $month['month_number'] && $item->year == $month['year'];
                });

                return [
                    'month' => $month['month_name'],
                    'total' => $record ? $record->total : 0
                ];
            });

            // Step 4: Extract labels and data
            $labels = $finalData->pluck('month');
            $datamonthly = $finalData->pluck('total');


            return view('dashboardClient', compact('datamonthly', 'labels', 'productNames', 'auditCounts', 'closureData', 'agencies', 'totalAllocation', 'totalAllocationByAgency', 'totalSubmittedAudits', 'totalAuditCompletedByAgency', 'totalPendingAuditsActionPlan', 'totalApprovedAudits', 'totalPendingAuditsofCA', 'totalPendingAuditsofAA', 'totalPerfomedAudits', 'overallAuditcurrentMonth', 'overallAuditLastSixMonths', 'overallAuditLastSixMonthsByAgency', 'auditAgencyIds', 'agencyNames', 'overallAuditlastMonth', 'overallAuditlastMonth', 'percentageScoreDifference', 'overallScorelastMonth', 'percentageDifference', 'overallScorecurrentMonth', 'auditAgenciesPrecentage', 'auditAgenciesData', 'parametersTotalScore', 'databyZoneRegion', 'allocationZoneProduct', 'agency', 'auditAgencyName', 'auditAgencyName', 'allAgencies', 'collAgencyTrendData', 'finalDataAgencyTrend', 'nationalCollManagerAuditData'));
        } elseif ($user->hasRole('Super Admin')) {

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



            //--- 1 V Client Dashboard -- Audit Allocation 4 Boxes --------------------

            //-- Box 1 Total Allocation ----------------
            $totalAllocation = DB::table('audit_allocation')
                ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    return $query->whereBetween('created_at', [$startDate, $endDate]);
                })
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
                ->groupBy('audit_allocation.process_review_agency_email', 'users.name')
                ->get();


            //-- Box 2 Total Submitted Audits -------------------------------------
            $totalSubmittedAudits = DB::table('audits')
                ->where('status', '<=', 1)
                ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    return $query->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->count();


            //echo '<pre>'; print_r($totalCompletedAudits); exit();


            //-------Total Audit Completed By Agency-----------------------------------
            $totalAuditCompletedByAgency = DB::table('audits')
                ->join('users', 'audits.audit_agency_id', '=', 'users.id')
                ->leftJoin('saved_audits', 'audits.id', '=', 'saved_audits.audit_id') // Join with saved_audits
                ->whereNull('saved_audits.audit_id') // Exclude drafts
                ->select(
                    'audits.audit_agency_id',
                    'users.name as agency_name',
                    DB::raw('COUNT(audits.id) as completed_audits') // Count only completed audits
                )
                ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    return $query->whereBetween('audits.created_at', [$startDate, $endDate]);
                })
                ->groupBy('audits.audit_agency_id', 'users.name')
                ->get();


            //echo '<pre>'; print_r($totalAuditCompletedByAgency); die();  



            //------ Box 3 Send for Action Planning ------------------------------------------- 

            // Total Performed Audits (Status 0 + Status 1 + Status 2)
            $totalPerfomedAudits = DB::table('closure_audits')
                ->whereIn('status', [0, 1])
                ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    return $query->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->count();

            //Pending audit status 0 
            $totalPendingAuditsActionPlan = DB::table('closure_audits')->where('status', 0)
                ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    return $query->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->count();

            //Closed/Approved audit status 1 
            $totalApprovedAudits = DB::table('closure_audits')->where('status', 1)
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


            //----- Box 4 Closed Audit ----------------------------------------->      

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
                ->whereBetween('created_at', [$lastMonthStartDate, $lastMonthEndDate])
                ->count();


            // V Overall Score Last month
            $overallScorelastMonth = DB::table('closure_audits')
                ->join('audits', 'audits.id', '=', 'closure_audits.audit_id')
                ->where('closure_audits.status', 1)
                ->whereBetween('closure_audits.created_at', [$lastMonthStartDate, $lastMonthEndDate])
                ->avg('audits.overall_score');
            //echo '<pre>'; print_r($overallScorelastMonth); die();

            // Set to 0 if null
            $overallScorelastMonth = $overallScorelastMonth !== null ? $overallScorelastMonth : 0;


            //-----Closed/Current month Audit Count------------------------------>
            $today = Carbon::now();
            if ($today->day < 10) {
                // If before the 10th, use the previous month's range
                $startDate = $today->copy()->subMonth()->day(10)->startOfDay()->toDateTimeString();
                $endDate = $today->copy()->day(9)->endOfDay()->toDateTimeString();
            } else {
                // If on or after the 10th, use the current month's range
                $startDate = $today->copy()->day(10)->startOfDay()->toDateTimeString();
                $endDate = $today->copy()->addMonth()->day(9)->endOfDay()->toDateTimeString();
            }


            $overallAuditcurrentMonth = DB::table('closure_audits')->where('status', 1)
                ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    return $query->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->count();


            //Overall Score Current Month
            // $startDate = $request->input('start_date');
            // $endDate = $request->input('end_date');

            // if (!$startDate || !$endDate) {
            //     $today = Carbon::now();

            //     if ($today->day < 10) {
            //         $startDate = $today->copy()->subMonth()->startOfMonth()->day(10)->toDateString();
            //         $endDate = $today->copy()->startOfMonth()->day(9)->toDateString();
            //     } else {
            //         $startDate = $today->copy()->startOfMonth()->day(10)->toDateString();
            //         $endDate = $today->copy()->addMonth()->startOfMonth()->day(9)->toDateString();
            //     }
            // }

            // $startDate = trim($startDate);
            // $endDate = trim($endDate);

            // try {
            //     $startDate = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay(); // Start of the day
            //     $endDate = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();
            // } catch (\Carbon\Exceptions\InvalidFormatException $e) {
            //     return response()->json(['error' => 'Invalid date format: ' . $e->getMessage()], 400);
            // }

            $today = Carbon::now();
            if ($today->day < 10) {
                // If before the 10th, use the previous month's range
                $startDate = $today->copy()->subMonth()->day(10)->startOfDay()->toDateTimeString();
                $endDate = $today->copy()->day(9)->endOfDay()->toDateTimeString();
            } else {
                // If on or after the 10th, use the current month's range
                $startDate = $today->copy()->day(10)->startOfDay()->toDateTimeString();
                $endDate = $today->copy()->addMonth()->day(9)->endOfDay()->toDateTimeString();
            }


            // //Overall Score Current Months
            // Total audits submitted in the date range
            $auditCount = DB::table('closure_audits')
                ->join('audits', 'audits.id', '=', 'closure_audits.audit_id')
                ->where('closure_audits.status', 1)
                ->whereBetween('closure_audits.created_at', [$startDate, $endDate])
                ->count('audits.id');

            $totalSumOverallScored = DB::table('closure_audits')
                ->join('audits', 'audits.id', '=', 'closure_audits.audit_id')
                ->where('closure_audits.status', 1)
                ->whereBetween('closure_audits.created_at', [$startDate, $endDate])
                ->sum(DB::raw('CAST(audits.overall_score AS DECIMAL(10,2))'));

            //echo '<pre>'; print_r($totalSumOverallScored); die();

            $overallScorecurrentMonth = $auditCount * 100;

            //$overallScorecurrentMonth= 115;

            if ($overallScorecurrentMonth > 0) {
                $overallScorecurrentMonth = ($totalSumOverallScored / $overallScorecurrentMonth) * 100;
            } else {
                $overallScorecurrentMonth = 0;
            }


            // V For Green and Red Arrow Calculate percentage difference
            if ($overallAuditlastMonth > 0) {
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





            //----2 V Trend Last Six Months and Overall Score for Six Months Start--------

            $overallAuditLastSixMonths = [];

            for ($i = 0; $i < 6; $i++) {
                $startDate = Carbon::now()->subMonths($i)->startOfMonth()->day(10);
                // Start on the 10th of the current month
                $endDate = Carbon::now()->subMonths($i - 1)->startOfMonth()->day(9)->endOfDay();
                // End on the 9th of the next month

                // Total audits submitted in the date range
                // Total audits submitted in the date range
                $auditCount = DB::table('audits')
                    ->leftJoin('saved_audits', 'audits.id', '=', 'saved_audits.audit_id') // Join with saved_audits
                    ->whereNull('saved_audits.audit_id') // Exclude drafts
                    ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                        return $query->whereBetween('audits.created_at', [$startDate, $endDate]); // Filter by date range
                    })
                    ->count('audits.id');

                $totalSumOverallScored = DB::table('audits')
                    ->leftJoin('saved_audits', 'audits.id', '=', 'saved_audits.audit_id') // Join with saved_audits
                    ->whereNull('saved_audits.audit_id') // Exclude drafts
                    ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                        return $query->whereBetween('audits.created_at', [$startDate, $endDate]);
                    })
                    ->sum(DB::raw('CAST(audits.overall_score AS DECIMAL(10,2))')); // Cast the overall_score as numeric (decimal)

                $totalScorable = $auditCount * 100;

                //$totalScorable= 115;

                if ($totalScorable > 0) {
                    $overallScore = ($totalSumOverallScored / $totalScorable) * 100;
                } else {
                    $overallScore = 0;
                }


                // Count audit allocations in the date range
                $totalAllocations = DB::table('audit_allocation')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count('id');

                // Count pending closure audits in the date range
                $totalPendingAudits = DB::table('closure_audits')
                    ->leftJoin('saved_audits', 'closure_audits.audit_id', '=', 'saved_audits.audit_id')
                    ->where('closure_audits.status', 0)
                    ->whereNull('saved_audits.audit_id') // Ensure no match in saved_audits table
                    ->whereBetween('closure_audits.created_at', [$startDate, $endDate])
                    ->count();

                // Add data for the month to the results
                $overallAuditLastSixMonths[] = [
                    'month' => $startDate->format('F Y'),
                    'audit_count' => $auditCount,
                    'overall_score' => round($overallScore),
                    'allocation_count' => $totalAllocations,
                    'pending_count' => $totalPendingAudits,
                ];
            }


            //--------Same 6 Months data by Agency Wise------------------------- 

            $overallAuditLastSixMonthsByAgency = [];
            //$auditAgencyIds = DB::table('audits')->distinct()->pluck('audit_agency_id');
            $auditAgencyIds = DB::table('audit_allocation')->distinct()->pluck('process_review_agency_id');

            for ($i = 0; $i < 6; $i++) {
                $startDate = Carbon::now()->subMonths($i)->startOfMonth()->day(10);
                // Start on the 10th of the current month
                $endDate = Carbon::now()->subMonths($i - 1)->startOfMonth()->day(9)->endOfDay();

                // Overall data
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




                $dataForMonth = [
                    'month' => $startDate->format('F Y'),
                    'overall' => [
                        'audit_count' => $overallCount,
                        'overall_score' => round($overallScore),
                    ],
                    'agencies' => [],
                ];

                // Agency-specific data with agency name
                foreach ($auditAgencyIds as $agencyId) {
                    // Get the agency name
                    $agencyName = DB::table('users')
                        ->where('id', $agencyId)
                        ->value('name');

                    // Total audits submitted by agency
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

                    // Pending audits for this agency from closure_audits table
                    $pendingAudits = DB::table('closure_audits')
                        ->leftJoin('saved_audits', 'closure_audits.audit_id', '=', 'saved_audits.audit_id')
                        ->where('closure_audits.status', 0)
                        ->whereNull('saved_audits.audit_id') // saved_audits (exclude drafts)
                        ->whereBetween('closure_audits.created_at', [$startDate, $endDate])
                        ->where('closure_audits.audit_agency_id', $agencyId)
                        ->count('closure_audits.audit_id');


                    // Add agency data with agency name to the month data
                    $dataForMonth['agencies'][$agencyId] = [
                        'name' => $agencyName,
                        'audit_count' => $agencyCount,
                        'overall_score' => round($agencyScore),
                        'allocation_count' => $agencyAllocations, // Added field for allocation count
                        'pending_count' => $pendingAudits,       // Added field for pending audits
                    ];
                }

                $overallAuditLastSixMonthsByAgency[] = $dataForMonth;
            }

            // Fetch agency names for mapping (optional)
            $agencyNames = DB::table('users')->whereIn('id', $auditAgencyIds)->pluck('name', 'id');

            //----2 V Overall Audit and Overall Score for Six Months Agency wise End--------  



            //--- 3 V Client Dashboard -- ZONE WISE DATA  ----------------------------------
            $averageScoresByGradeAndRegion = DB::table('audits')
                ->join('agencies', 'audits.agency_id', '=', 'agencies.id')
                ->join('regions', 'agencies.region_id', '=', 'regions.id')
                ->select('audits.grade', 'agencies.region_id', 'regions.name as region_name', DB::raw('AVG(audits.score_percentage) as average_score'))
                ->groupBy('audits.grade', 'agencies.region_id', 'regions.name')
                ->get();

            //echo '<pre>'; print_r($averageScoresByGradeAndRegion); die();


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

            // Trim whitespace from the input dates
            $startDatezone = trim($startDatezone);
            $endDatezone = trim($endDatezone);

            // Try to convert the dates into Carbon objects
            try {
                // Adjusted format to match the "Y-m-d" format received
                $startDatezone = Carbon::createFromFormat('Y-m-d', $startDatezone)->startOfDay(); // Start of the day
                $endDatezone = Carbon::createFromFormat('Y-m-d', $endDatezone)->endOfDay(); // End of the day
            } catch (\Carbon\Exceptions\InvalidFormatException $e) {
                // Handle invalid format exception (you can log the error or return a response if needed)
                return response()->json(['error' => 'Invalid date format: ' . $e->getMessage()], 400);
            }

            // Query for audits data
            // Query for audit data
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
                ->whereNull('saved_audits.audit_id') // saved_audits (exclude drafts)
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

            // Merging the datasets from both queries based on region
            $databyZoneRegion = $allocationZoneProduct->map(function ($allocationData) use ($databyZoneRegionAudits) {
                // Find matching audit data for the region
                $auditData = $databyZoneRegionAudits->firstWhere('region_name', $allocationData->region_name);

                // Prepare the final region data object
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


            //echo '<pre>'; print_r($allocationZoneProduct); die();


            //--- 3 V Client Dashboard -- ZONE WISE DATA  Completed --------------------



            //--- 4 V Client Dashboard -- Parameter Score ------------------------------ 
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
            $parametersTotalScore = []; // Initialize the variable

            foreach ($totalScore as $score) {
                $maxScore = $totalMaxScore->firstWhere('parameter_name', $score->parameter_name);

                // Calculate the percentage if totalMaxScore is not zero
                $parameterPercentage = 0;
                if ($maxScore && $maxScore->total_max_score != 0) {
                    $parameterPercentage = ($score->total_score / $maxScore->total_max_score) * 100;
                }

                // Store the result in $parametersTotalScore
                $parametersTotalScore[] = [
                    'parameter_name' => $score->parameter_name,
                    'total_score' => $score->total_score,
                    'total_max_score' => $maxScore ? $maxScore->total_max_score : 0,
                    'total_parameter_percentage' => $parameterPercentage
                ];
            }



            // $combinedResults = [];

            // foreach ($totalScore as $score) {
            //     // Find the corresponding max score for the current parameter
            //     $maxScore = $totalMaxScore->firstWhere('parameter_name', $score->parameter_name);

            //     // Calculate the percentage if totalMaxScore is not zero
            //     $parameterPercentage = 0;
            //     if ($maxScore && $maxScore->total_max_score != 0) {
            //         $parameterPercentage = ($score->total_score / $maxScore->total_max_score) * 100;
            //     }

            //     // Store the result
            //     $parametersTotalScore[] = [
            //         'parameter_name' => $score->parameter_name,
            //         'total_score' => $score->total_score,
            //         'total_max_score' => $maxScore ? $maxScore->total_max_score : 0,
            //         'total_parameter_percentage' => $parameterPercentage
            //     ];
            // }
            //echo '<pre>'; print_r($currentMonthParametersAverage); die(); 



            //--- 4 V Parameter Score End  ------------------------------------------------ 




            //--- 5 V Audit Agency --------------------------------------------------------- 

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


            //echo '<pre>'; print_r($auditAgenciesData); die();





            // V currently not in use
            $auditAgenciesPrecentage = DB::table('audits')
                ->join('audit_allocation', 'audits.audit_agency_id', '=', 'audit_allocation.process_review_agency_id')
                ->select('audit_allocation.process_review_agency', 'audits.audit_agency_id', DB::raw('AVG(audits.score_percentage) as average_score_percentage'))
                ->groupBy('audit_allocation.process_review_agency', 'audits.audit_agency_id')
                ->get();


            //--- 5 V Audit Agency End -----------------------------------------------------


            //--- 6 V Collection Agency Trend Start --------------------------------------
            // Fetch input dates or set default range
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

            //--- 6 V Collection Agency Trend End --------------------------------> 



            //--- 7 V National Collection Manager -------------------------------->

            // Fetch input dates or set default range
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

            //echo '<pre>'; print_r($nationalCollManagerAuditData); die();   



            //---V Audit Dump Download Start ------------------->
            $agency = Agency::get();

            $allAgencies = DB::table('agencies')->select('id', 'name')->get();


            $roles = Role::all();

            $auditAgencyName = DB::table('users')
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->where('roles.name', 'Admin')
                ->select('users.id', 'users.name')
                ->get();




            return view('dashboardSuperAdmin', compact('totalAllocation', 'totalAllocationByAgency', 'totalSubmittedAudits', 'totalAuditCompletedByAgency', 'totalPendingAuditsActionPlan', 'totalApprovedAudits', 'totalPendingAuditsofCA', 'totalPendingAuditsofAA', 'totalPerfomedAudits', 'overallAuditcurrentMonth', 'overallAuditLastSixMonths', 'overallAuditLastSixMonthsByAgency', 'auditAgencyIds', 'agencyNames', 'overallAuditlastMonth', 'overallAuditlastMonth', 'percentageScoreDifference', 'overallScorelastMonth', 'percentageDifference', 'overallScorecurrentMonth', 'auditAgenciesPrecentage', 'auditAgenciesData', 'parametersTotalScore', 'databyZoneRegion', 'allocationZoneProduct', 'agency', 'auditAgencyName', 'auditAgencyName', 'allAgencies', 'collAgencyTrendData', 'finalDataAgencyTrend', 'nationalCollManagerAuditData'));
        } else {
            //   echo '4'; die;
            $qa = [];
            $qc = [];

            $request->merge(['lob_audit_cycle' => 'current']);
            // $lob=$this->lobBaseData($request);
            // $product=$this->getProductBaseData($request);
            // $productList=$this->getProduct();
            // $topCollectionManager=$this->getTopCollectionManager();
            // $topAgency=$this->getTopAgency();
            $bottomProductParameter = $this->bottomProductParameter();
            $lob = [];
            $product = [];
            $productList = [];
            $topCollectionManager = [];
            $topAgency = [];
            // $bottomProductParameter=$this->bottomProductParameter();
            if ($request->has('filterlob')) {
                $filterData = $this->getFilterData($request);
            } else {
                $request->merge([
                    "filterProduct" => "all",
                    "filterlob" => "all",
                    "filteraudit_cycle" => "All",
                    "filteraudit_cycle_custom" => null,
                    "filterzone" => "all",
                    "filterstate" => "all",
                    "filterbranch" => "all"
                ]);
                $filterData = $this->getFilterData($request);
                // $filterData=[];
            }
            $old = $request->all();
            // dd($topCollectionManager,$topAgency,$bottomProductParameter);

            return view('dashboard', compact('lob', 'totalalert', 'product', 'old', 'productList', 'topCollectionManager', 'topAgency', 'filterData', 'bottomProductParameter', 'qa', 'qc'));
        }
    }
    public function qaDashboardMeta($request)
    {
        $user = Auth::user();
        $ids = [];
        $totalpass = 0;
        $totalfaild = 0;
        $totalsaved = 0;
        $totalpending = 0;
        $cycle = ['start' => date('Y-m-01'), 'end' => date('Y-m-t')];
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
        //$totalpending = $totalAudit - ($totalpass + $totalfaild);

        //V Total Audit Assign
        $totalAssign = DB::table('auditor_assigns')
            ->where('auditor_email', auth()->user()->email)
            ->distinct()
            ->count('id');


        //V Total Completed Audits by Auditors
        $authId = Auth::user()->id;
        $totalCompletedAudits = DB::table('audits')
            ->leftJoin('saved_audits', 'audits.id', '=', 'saved_audits.audit_id')
            ->whereNull('saved_audits.audit_id') // Exclude drafts
            ->where('audits.audited_by_id', $authId)
            ->distinct() // Ensure distinct audits are counted
            ->count('audits.id');

        //echo '<pre>'; print_r($totalCompletedAudits); die();

        $totalPending = $totalAssign - $totalCompletedAudits;

        $old = $request->all();

        return [
            'totalAudit' => $totalAudit,
            'totalAssign' => $totalAssign,
            'totalCompletedAudits' => $totalCompletedAudits,
            'totalpending' => $totalpending,
            'old' => $old,
            'totalsaved' => $totalsaved,
            'totalpass' => $totalpass,
            'totalfaild' => $totalfaild
        ];
    }
    public function qaDashboard($request)
    {
        $data = $this->qaDashboardMeta($request);
        $totalAssign = $data['totalAssign'];
        $totalAudit = $data['totalAudit'];
        $old = $data['old'];
        $totalsaved = $data['totalsaved'];
        $totalpending = $data['totalpending'];
        $totalpass = $data['totalpass'];
        $totalfaild = $data['totalfaild'];


        // Auditor Dashboard Filter
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $current_cycle = DB::table('audit_cycles')
            ->where('status', '=', '1')
            ->where('client_id', auth()->user()->client_id)
            ->first();

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


        //V Total Audit Assign
        $totalAssign = DB::table('auditor_assigns')
            ->where('auditor_email', auth()->user()->email)
            ->where('auditor_assigns.audit_cycle_id', $current_cycle->id)
            ->distinct()
            ->count('id');


        //V Total Completed Audits by Auditors
        $authId = Auth::user()->id;
        // $totalCompletedAudits = DB::table('audits')
        // ->leftJoin('saved_audits', 'audits.id', '=', 'saved_audits.audit_id')
        // ->whereNull('saved_audits.audit_id') // Exclude drafts
        // ->where('audits.audited_by_id', $authId)
        // ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
        //         return $query->whereBetween('audits.created_at', [$startDate, $endDate]);
        //     })
        // ->distinct() // Ensure distinct audits are counted
        // ->count('audits.id');


        $totalCompletedAudits = DB::table('audits')
            ->where('audited_by_id', $authId)
            ->whereIn('status', [0, 1]) // Only status 0 or 1
            ->where('audits.audit_cycle_id', $current_cycle->id)
            ->count();

        //echo '<pre>'; print_r($totalCompletedAudits); die();

        $totalPending = $totalAssign - $totalCompletedAudits;
        //Temproary condition to show correct data in demo
        // if($totalCompletedAudits > $totalAssign) {
        //     $totalPending=0;
        //     $totalCompletedAudits=$totalAssign;
        // }


        $authId = Auth::user()->id;
        $totalSavedAudits = DB::table('audits')
            ->where('audited_by_id', $authId) // Filter by auditor
            ->where('status', 5)              // Status 5 means "saved"
            ->where('audits.audit_cycle_id', $current_cycle->id)
            ->count(); // Count all matching rows


        return view('dashboardQa', compact('totalAssign', 'totalAudit', 'old', 'totalsaved', 'totalpending', 'totalpass', 'totalfaild', 'totalCompletedAudits', 'totalPending', 'totalSavedAudits'));
    }


    public function qcDashboardMeta($request)
    {
        $ids = [];
        $totalpass = 0;
        $totalfaild = 0;
        $totalsaved = 0;
        $totalpending = 0;
        $saved = SavedAudit::all()->pluck('audit_id')->toArray();
        $savedIds = SavedQcAudit::all()->pluck('audit_id')->toArray();
        // dd($savedIds);
        //$cycle=['start'=>'1970-01-01','end'=>'2120-12-31'];
        $cycle = ['start' => date('Y-m-01'), 'end' => date('Y-m-t')];
        // $cycle=['start'=>'17-06-2020','end'=>'17-06-2020'];
        if ($request->has('lob_audit_cycle') && $request->lob_audit_cycle != 'custom') {
            $cycle = $this->getAuditCycle($request->lob_audit_cycle);
        } else if ($request->has('lob_audit_cycle') && $request->lob_audit_cycle == 'custom') {
            $dates = explode(' - ', $request->lob_audit_cycle_custom);
            $cycle = ['start' => Carbon::parse($dates[0])->toDateString(), 'end' => Carbon::parse($dates[1])->toDateString()];
        }
        $audit = Audit::with('qmsheet')->whereNotIn('id', $saved)->get();
        $audit = $audit->filter(function ($item) use ($cycle) {
            return (($item->created_at >= $cycle['start'] . " 00:00:00") && ($item->created_at <= $cycle['end'] . " 23:59:59"));
        });
        if ($request->has('productlob') && $request->productlob != 'all') {
            $audit = $audit->filter(function ($item) use ($request) {
                return ($item->qmsheet->lob == $request->productlob);
            });
        }
        if (count($audit) > 0) {
            $queryrow = Qc::whereIn('audit_id', $audit->pluck('id'))->get()->unique('audit_id');
            $query = $queryrow->whereNotIn('audit_id', $savedIds);
            // dd($query->pluck('status','audit_id'),$queryrow->pluck('status','audit_id'),$savedIds);
            $totalpass = $query->where('status', 1)->count();
            $totalpassChange = $query->where('status', 2)->count();
            $totalfaild = $query->where('status', 3)->count();
            // dd($totalpassChange,$totalpass,$totalfaild,$query->pluck('audit_id'));
        }
        if (count($audit) > 0) {
            $totalsaved = Audit::whereIn('id', $savedIds)->count();
            $totalAudit = $audit->whereIn('id', $queryrow->pluck('audit_id')->toArray())->count();
            $totalpending = $totalAudit - ($totalpass + $totalfaild + $totalpassChange);
            $totalApproved = ($totalpassChange + $totalpass);
            $totalPendingList = $audit->whereNotIn('id', $queryrow->pluck('audit_id')->toArray());
        } else {
            $totalsaved = 0;
            $totalAudit = 0;

            $totalApproved = 0;
            $totalpass = 0;
            $totalfaild = 0;
            $totalpassChange = 0;
            $totalpending = $totalAudit - ($totalpass + $totalfaild + $totalpassChange);
            $totalPendingList = $audit->where('id', 456778888876554);
        }

        // dd($totalAudit,$queryrow->pluck('audit_id')->toArray());

        $old = $request->all();
        return ['totalAudit' => $totalAudit, 'old' => $old, 'totalsaved' => $totalsaved, 'totalpending' => $totalpending, 'totalpass' => $totalpass, 'totalfaild' => $totalfaild, 'totalpassChange' => $totalpassChange, 'totalApproved' => $totalApproved, 'totalPendingList' => $totalPendingList];
    }
    public function qcDashboard($request)
    {
        $data = $this->qcDashboardMeta($request);
        $totalAudit = $data['totalAudit'];
        $old = $data['old'];
        $totalsaved = $data['totalsaved'];
        $totalpending = $data['totalpending'];
        $totalpass = $data['totalpass'];
        $totalfaild = $data['totalfaild'];
        $totalpassChange = $data['totalpassChange'];
        $totalApproved = $data['totalApproved'];
        $totalPendingList = $data['totalPendingList'];
        return view('dashboardQc', compact('totalAudit', 'old', 'totalsaved', 'totalpending', 'totalpass', 'totalfaild', 'totalpassChange', 'totalApproved', 'totalPendingList'));
    }
    public function getProduct()
    {
        $product = Products::all(['id', 'name']);
        return $product;
    }
    public function lobBaseData($request)
    {
        // dd($request->lob_audit_cycle);
        $cycle = ['start' => '1970-01-01', 'end' => '2120-12-31'];
        if ($request->has('lob_audit_cycle') && $request->lob_audit_cycle != 'custom') {
            $cycle = $this->getAuditCycle($request->lob_audit_cycle);
        } else if ($request->has('lob_audit_cycle') && $request->lob_audit_cycle == 'custom') {
            $dates = explode(' - ', $request->lob_audit_cycle_custom);
            $cycle = ['start' => Carbon::parse($dates[0])->toDateString(), 'end' => Carbon::parse($dates[1])->toDateString()];
        }
        $audit = [];
        if (!Auth::user()->hasRole('Admin') || !Auth::user()->hasRole('Client')) {
            $branch = $this->getUserBranch();
            if (count($branch) > 0) {
                $query = Audit::with([
                    'qmsheet.parameter.qm_sheet_sub_parameter',
                    // 'product','branch','yard','agency'
                ]);
                $agency = Agency::where('branch_id', $branch)->get('id')->pluck('id');
                $yard = Yard::where('branch_id', $branch)->get('id')->pluck('id');
                $query->whereIn('branch_id', $branch)->orWhereIn('agency_id', $agency)->orWhereIn('agency_id', $yard);
                $audit = $query->get();
            }
        } else {
            $query = Audit::with([
                'qmsheet.parameter.qm_sheet_sub_parameter',
                // 'product','branch','yard','agency'
            ]);
            $audit = $query->get();
        }
        $data = [];
        $qc = Qc::all()->pluck('audit_id')->toArray();
        if ($audit != null) {
            $audit = $audit->whereIn('id', $qc);
        }
        // dd($audit->pluck('id'));
        $dataPoint = [];
        foreach ($audit as $item) {
            $total = 0;
            // $total=$item->qmsheet->parameter->map(function($val) use($total){
            //     $total=$total+$val->qm_sheet_sub_parameter->sum('weight');
            //     return $total;
            // });
            // dd($cycle);
            $total = $this->getTotalPoint($item->id);
            $dataPoint[$item->id] = $total;
            if (($item->created_at >= $cycle['start'] . " 00:00:00") && ($item->created_at <= $cycle['end'] . " 23:59:59")) {
                if (isset($data[$item->qmsheet->lob])) {
                    // $data[$item->qmsheet->lob]=['point'=>$data[$item->qmsheet->lob]['point']+$item->overall_score,'total'=>$data[$item->qmsheet->lob]['total']+array_sum($total->toArray())];
                    $data[$item->qmsheet->lob] = ['point' => $data[$item->qmsheet->lob]['point'] + $item->overall_score, 'total' => $data[$item->qmsheet->lob]['total'] + $total];
                } else {
                    // $data[$item->qmsheet->lob]=['point'=>$item->overall_score,'total'=>array_sum($total->toArray())];
                    $data[$item->qmsheet->lob] = ['point' => $item->overall_score, 'total' => $total];
                }
            }
        }
        // dd($dataPoint);
        return $data;
    }

    public function getProductBaseData($request)
    {
        // dd($request->all());
        if (!Auth::user()->hasRole('Admin')) {
            $branchIds = $this->getUserBranch();
        } else {
            $branchIds = Branch::all()->pluck('id');
        }
        $qc = Qc::all()->pluck('audit_id')->toArray();
        $pids = Branchable::whereIn('agency_id', $branchIds)->get(['id', 'product_id'])->pluck('product_id');
        $query = Audit::with(['qmsheet', 'product'])->whereIn('product_id', $pids);
        $cycle = ['start' => '1970-01-01', 'end' => '2120-12-31'];
        if ($request->has('product_audit_cycle') && $request->product_audit_cycle != 'custom') {
            $cycle = $this->getAuditCycle($request->product_audit_cycle);
            // $query->whereBetween('created_at',[$cycle['start']." 00:00:00",$cycle['end']." 23:59:59"]);
        } else if ($request->has('product_audit_cycle') && $request->product_audit_cycle == 'custom') {
            $dates = explode(' - ', $request->product_audit_cycle_custom);
            $cycle = ['start' => Carbon::parse($dates[0])->toDateString(), 'end' => Carbon::parse($dates[1])->toDateString()];
        }
        $audit = $query->get();
        $audit = $audit->whereIn('id', $qc);
        $data = [];
        foreach ($audit as $item) {
            $total = 0;
            // $total=$item->qmsheet->parameter->map(function($val) use($total){
            //     $total=$total+$val->qm_sheet_sub_parameter->sum('weight');
            //     return $total;
            // });
            $total = $this->getTotalPoint($item->id);
            if (($item->created_at >= $cycle['start'] . " 00:00:00") && ($item->created_at <= $cycle['end'] . " 23:59:59")) {
                if ($request->has('productlob') && $request->productlob != 'all') {
                    if ($request->productlob == $item->qmsheet->lob) {
                        if (isset($data[$item->product_id])) {
                            // $data[$item->product_id]=['lob'=>$item->qmsheet->lob,'name'=>$item->product->name,'point'=>($data[$item->product_id]['point']+$item->overall_score)
                            // ,'total'=>($data[$item->product_id]['total']+array_sum($total->toArray()))];
                            $data[$item->product_id] = [
                                'lob' => $item->qmsheet->lob,
                                'name' => $item->product->name,
                                'point' => ($data[$item->product_id]['point'] + $item->overall_score),
                                'total' => ($data[$item->product_id]['total'] + $total)
                            ];
                        } else {
                            // $data[$item->product_id]=['lob'=>$item->qmsheet->lob,'name'=>$item->product->name,'point'=>$item->overall_score
                            // ,'total'=>array_sum($total->toArray())];
                            $data[$item->product_id] = [
                                'lob' => $item->qmsheet->lob,
                                'name' => $item->product->name,
                                'point' => $item->overall_score,
                                'total' => $total
                            ];
                        }
                    }
                } else {
                    if (isset($data[$item->product_id])) {
                        // $data[$item->product_id]=['lob'=>$item->qmsheet->lob,'name'=>$item->product->name,'point'=>($data[$item->product_id]['point']+$item->overall_score),
                        // 'total'=>($data[$item->product_id]['total']+array_sum($total->toArray()))];
                        $data[$item->product_id] = [
                            'lob' => $item->qmsheet->lob,
                            'name' => $item->product->name,
                            'point' => ($data[$item->product_id]['point'] + $item->overall_score),
                            'total' => ($data[$item->product_id]['total'] + $total)
                        ];
                    } else {
                        // $data[$item->product_id]=['lob'=>$item->qmsheet->lob,'name'=>$item->product->name,'point'=>$item->overall_score
                        // ,'total'=>array_sum($total->toArray())];
                        $data[$item->product_id] = [
                            'lob' => $item->qmsheet->lob,
                            'name' => $item->product->name,
                            'point' => $item->overall_score,
                            'total' => $total
                        ];
                    }
                }
            }
        }
        usort($data, function ($a, $b) {
            return (($b['point'] / $b['total']) * 100) - (($a['point'] / $a['total']) * 100);
        });
        $top3 = array_slice($data, 0, 4);
        // dd($data,$top3);
        return $top3;
    }
    function allProduct(Request $request)
    {
        if (!Auth::user()->hasRole('Admin')) {
            $branchIds = $this->getUserBranch();
        } else {
            $branchIds = Branch::all()->pluck('id');
        }
        $pids = Branchable::whereIn('agency_id', $branchIds)->get(['id', 'product_id'])->pluck('product_id');
        $audit = Audit::with(['qmsheet.parameter.qm_sheet_sub_parameter', 'product'])->whereIn('product_id', $pids)->get();
        $data = [];
        foreach ($audit as $item) {
            $total = 0;
            // $total=$item->qmsheet->parameter->map(function($val) use($total){
            //     $total=$total+$val->qm_sheet_sub_parameter->sum('weight');
            //     return $total;
            // });
            $total = $this->getTotalPoint($item->id);
            if ($request->has('productlob') && $request->productlob != 'all') {
                if ($request->productlob == $item->qmsheet->lob) {
                    if (isset($data[$item->product_id])) {
                        // $data[$item->product_id]=['lob'=>$item->qmsheet->lob,'name'=>$item->product->name,'point'=>($data[$item->product_id]['point']+$item->overall_score)
                        // ,'total'=>($data[$item->product_id]['total']+array_sum($total->toArray()))];
                        $data[$item->product_id] = [
                            'lob' => $item->qmsheet->lob,
                            'name' => $item->product->name,
                            'point' => ($data[$item->product_id]['point'] + $item->overall_score),
                            'total' => ($data[$item->product_id]['total'] + $total)
                        ];
                    } else {
                        // $data[$item->product_id]=['lob'=>$item->qmsheet->lob,'name'=>$item->product->name,'point'=>$item->overall_score
                        // ,'total'=>array_sum($total->toArray())];
                        $data[$item->product_id] = [
                            'lob' => $item->qmsheet->lob,
                            'name' => $item->product->name,
                            'point' => $item->overall_score,
                            'total' => $total
                        ];
                    }
                }
            } else {
                if (isset($data[$item->product_id])) {
                    // $data[$item->product_id]=['lob'=>$item->qmsheet->lob,'name'=>$item->product->name,'point'=>($data[$item->product_id]['point']+$item->overall_score),
                    // 'total'=>($data[$item->product_id]['total']+array_sum($total->toArray()))];
                    $data[$item->product_id] = [
                        'lob' => $item->qmsheet->lob,
                        'name' => $item->product->name,
                        'point' => ($data[$item->product_id]['point'] + $item->overall_score),
                        'total' => ($data[$item->product_id]['total'] + $total)
                    ];
                } else {
                    // $data[$item->product_id]=['lob'=>$item->qmsheet->lob,'name'=>$item->product->name,'point'=>$item->overall_score
                    // ,'total'=>array_sum($total->toArray())];
                    $data[$item->product_id] = [
                        'lob' => $item->qmsheet->lob,
                        'name' => $item->product->name,
                        'point' => $item->overall_score,
                        'total' => $total
                    ];
                }
            }
        }
        usort($data, function ($a, $b) {
            return (($b['point'] / $b['total']) * 100) - (($a['point'] / $a['total']) * 100);
        });
        return response()->json(['data' => $data]);
    }

    public function getBranch($state_id)
    {
        $cids = DB::table('cities')->where('state_id', $state_id)->get(['id', 'name'])->pluck('id');
        $branch = Branch::whereIn('city_id', $cids)->get(['id', 'name']);

        return response()->json(['data' => $branch]);
    }
    public function fetchMapData(Request $request)
    {
        $lob = ($request->lob == 'all') ? ['collection', 'commercial_vehicle', 'rural', 'alliance', 'credit_card',] : [$request->lob];
        $cids = [];
        if ($request->zone == 'all') {
            if ($request->state == 'all') {
                $state_id = DB::table('states')->get(['id', 'name'])->pluck('id');
                $cids = DB::table('cities')->whereIn('state_id', $state_id)->get(['id', 'name'])->pluck('id');
            } else {
                $state_id = DB::table('states')->where('id', $request->state)->get(['id', 'name'])->pluck('id');
                $cids = DB::table('cities')->whereIn('state_id', $state_id)->get(['id', 'name'])->pluck('id');
            }
        } else {
            if ($request->state == 'all') {
                $state_id = DB::table('states')->where('region_id', $request->zone)->get(['id', 'name'])->pluck('id');
                $cids = DB::table('cities')->whereIn('state_id', $state_id)->get(['id', 'name'])->pluck('id');
            } else {
                $state_id = DB::table('states')->where('region_id', $request->zone)->where('id', $request->state)->get(['id', 'name'])->pluck('id');
                $cids = DB::table('cities')->whereIn('state_id', $state_id)->get(['id', 'name'])->pluck('id');
            }
        }
        if ($request->branch == 'all') {
            $agency = Agency::whereIn('city_id', $cids)->whereIn('lob', $lob)->get()->pluck('id');
            //    $agency=Agency::whereIn('id',$branch)->get()->pluck('id');
            $yard = Yard::whereIn('branch_id', $branch)->get()->pluck('id');
            $branchrepo = BranchRepo::whereIn('branch_id', $branch)->get()->pluck('id');
            $agencyrepo = AgencyRepo::whereIn('branch_id', $branch)->get()->pluck('id');
        } else {
            $agency = Agency::where('id', $request->branch)->whereIn('lob', $lob)->first();
            //  $agency=Agency::where('branch_id',$branch)->get()->pluck('id');
            $yard = Yard::where('branch_id', $branch)->get()->pluck('id');
            $branchrepo = BranchRepo::whereIn('branch_id', $branch)->get()->pluck('id');
            $agencyrepo = AgencyRepo::whereIn('branch_id', $branch)->get()->pluck('id');
        }
        $qc = Qc::all()->pluck('audit_id')->toArray();
        $query = Audit::with(['qmsheet', 'branch.city.state', 'agency.branch.city.state', 'yard.branch.city.state', 'branchRepo.branch.city.state', 'agencyRepo.branch.city.state'])->whereIn('branch_id', $branch)->orWhereIn('yard_id', $yard)
            ->orWhereIn('agency_id', $agency)->orWhereIn('branch_repo_id', $branchrepo)->orWhereIn('agency_repo_id', $agencyrepo);
        $audit = $query->get();
        if ($request->product != 'all') {
            $audit = $audit->Where('product_id', $request->product);
        }
        $cycle = ['start' => '1970-01-01', 'end' => '2120-12-31'];
        if ($request->has('audit_cycle') && $request->audit_cycle != 'custom') {
            $cycle = $this->getAuditCycle($request->audit_cycle);
        } else if ($request->has('audit_cycle') && $request->audit_cycle == 'custom') {
            $dates = explode(' - ', $request->audit_cycle_custom);
            $cycle = ['start' => Carbon::parse($dates[0])->toDateString(), 'end' => Carbon::parse($dates[1])->toDateString()];
        }
        $audit = $audit->whereIn('id', $qc);
        // dd($audit->pluck('product_id','id'));
        $data = [];
        $dataTotal = [];
        $idTotal = [];
        foreach ($audit as $k => $item) {
            $state = '';
            switch ($item->qmsheet->type) {
                case 'branch':
                    $state = $item->branch->city->state->name;
                    break;
                case 'repo_yard':
                    $state = $item->yard->branch->city->state->name;
                    break;
                case 'agency':
                    $state = $item->agency->branch->city->state->name;
                    break;
                case 'branch_repo':
                    $state = $item->branchRepo->branch->city->state->name;
                    break;
                case 'agency_repo':
                    $state = $item->agencyRepo->branch->city->state->name;
                    break;
            }
            $key = $this->getKey($state);
            // dd($cycle);
            if (($item->created_at >= $cycle['start'] . " 00:00:00") && ($item->created_at <= $cycle['end'] . " 23:59:59")) {
                if (isset($data[$key])) {
                    $data[$key] = $data[$key] + $item->overall_score;
                    $dataTotal[$key] = $dataTotal[$key] + $this->getTotalPoint($item->id);
                } else {
                    $data[$key] = $item->overall_score;
                    $dataTotal[$key] = $this->getTotalPoint($item->id);
                }
                $idTotal[] = $item->id;
            }
        }
        $final = [];
        $total = 0;
        $per = 0;
        foreach ($data as $k => $val) {
            // $final[]=[$k,$val];
            // $total=$total+$val;
            $per = ($val / $dataTotal[$k]) * 100;
            $final[] = [$k, round($per, 2)];
            $total = $total + $val;
        }
        if (array_sum($dataTotal) > 0) {
            $totalper = ($total / array_sum($dataTotal)) * 100;
        } else {
            $totalper = 0;
        }
        return response()->json(['data' => $final, 'total' => round($totalper, 2), 'mainT' => $total, 'final' => array_sum($dataTotal), 'count' => $idTotal]);
    }
    public function getTotalPoint($id)
    {
        $resultAudit = AuditResult::where('audit_id', $id)->get(['id', 'audit_id', 'selected_option', 'sub_parameter_id'])->where('selected_option', 'N/A')->pluck('selected_option', 'sub_parameter_id');
        $item = Audit::with(['qmsheet.parameter.qm_sheet_sub_parameter'])->find($id);
        $total = 0;
        // dd($resultAudit);
        $total = $item->qmsheet->parameter->map(function ($val) use ($total, $resultAudit) {
            $subTotal = 0;
            // $total=$total+$val->qm_sheet_sub_parameter->sum('weight');
            $subTotal = $val->qm_sheet_sub_parameter->map(function ($value) use ($subTotal, $resultAudit) {
                if (!isset($resultAudit[$value->id])) {
                    return $value;
                }
            });
            $total = $total + $subTotal->sum('weight');
            return $total;
        });
        return $total->sum();
    }
    public function getKey($state)
    {
        $data = [
            'Puducherry' => 'in-py',
            'Lakshadweep' => 'in-ld',
            'West Bengal' => 'in-wb',
            'Orissa' => 'in-or',
            'Bihar' => 'in-br',
            'Sikkim' => 'in-sk',
            'Chhattisgarh' => 'in-ct',
            'Tamil Nadu' => 'in-tn',
            'Madhya Pradesh' => 'in-mp',
            'Gujarat' => 'in-2984',
            'Goa' => 'in-ga',
            'Nagaland' => 'in-nl',
            'Manipur' => 'in-mn',
            'Arunachal Pradesh' => 'in-ar',
            'Mizoram' => 'in-mz',
            'Tripura' => 'in-tr',
            'Daman and Diu' => 'in-3464',
            'Delhi' => 'in-dl',
            'Haryana' => 'in-hr',
            'Chandigarh' => 'in-ch',
            'Himachal Pradesh' => 'in-hp',
            'Jammu and Kashmir' => 'in-jk',
            'Kerala' => 'in-kl',
            'Karnataka' => 'in-ka',
            'Dadra and Nagar Haveli' => 'in-dn',
            'Maharashtra' => 'in-mh',
            'Assam' => 'in-as',
            'Andhra Pradesh' => 'in-ap',
            'Meghalaya' => 'in-ml',
            'Punjab' => 'in-pb',
            'Rajasthan' => 'in-rj',
            'Uttar Pradesh' => 'in-up',
            'Uttarkhand' => 'in-ut',
            'Jharkhand' => 'in-jh',
        ];
        // return $data[$state];
        return strtolower($state);
    }

    public function getStateData($state, Request $request)
    {
        $state_id = DB::table('states')->where('name', $state)->get(['id', 'name'])->pluck('id');
        $cids = DB::table('cities')->whereIn('state_id', $state_id)->get(['id', 'name'])->pluck('id');
        $branch = Branch::whereIn('city_id', $cids)->get()->pluck('id');
        $agency = Agency::whereIn('branch_id', $branch)->get()->pluck('id');
        $yard = Yard::whereIn('branch_id', $branch)->get()->pluck('id');
        // $br=BranchRepo::whereIn('branch_id',$branch)->get()->pluck('id');
        // $ar=AgencyRepo::whereIn('branch_id',$branch)->get()->pluck('id');
        $qc = Qc::all()->pluck('audit_id')->toArray();
        $audit = Audit::with(['qmsheet', 'branch.city.state', 'agency.branch.city.state', 'yard.branch.city.state', 'branchRepo.branch.city.state', 'agencyRepo.branch.city.state'])->whereIn('branch_id', $branch)
            ->orWhereIn('yard_id', $yard)
            ->orWhereIn('agency_id', $agency)
            // ->orWhereIn('agency_repo_id',$ar)->orWhereIn('branch_repo_id',$br)
            ->get();
        if ($audit != null) {
            $audit = $audit->whereIn('id', $qc);
        }
        $data = [];
        $dataTotal = [];
        foreach ($audit as $k => $item) {
            $state = '';
            switch ($item->qmsheet->type) {
                case 'branch':
                    $state = $item->branch->city->name;
                    break;
                case 'repo_yard':
                    $state = $item->yard->branch->city->name;
                    break;
                case 'agency':
                    $state = $item->agency->branch->city->name;
                    break;
                case 'branch_repo':
                    $state = $item->branchRepo->branch->city->name ?? '';
                    break;
                case 'agency_repo':
                    $state = $item->agencyRepo->branch->city->name ?? '';
                    break;
            }
            // dump($item->qmsheet->type,$state);
            if (isset($data[$state])) {
                $data[$state] = $data[$state] + $item->overall_score;
                $dataTotal[$state] = $dataTotal[$state] + $this->getTotalPoint($item->id);;
            } else {
                $data[$state] = $item->overall_score;
                $dataTotal[$state] = $this->getTotalPoint($item->id);
            }
        }
        $result = [];
        $total = 0;
        $per = 0;
        foreach ($data as $key => $item) {
            $per = ($item / $dataTotal[$key]) * 100;
            $result[$key] = round($per, 2);
            $total = $total + $item;
        }
        if (array_sum($dataTotal) > 0) {
            $totalper = ($total / array_sum($dataTotal)) * 100;
        } else {
            $totalper = 0;
        }
        // dd($data,$dataTotal,$audit);
        return response()->json(['data' => $result, 'total' => round($totalper, 2)]);
    }

    function getTopCollectionManager()
    {
        $qc = Qc::all()->pluck('audit_id')->toArray();
        $audit = Audit::with(['qmsheet.parameter.qm_sheet_sub_parameter', 'branch.branchable', 'agency.branch.branchable', 'yard.branch.branchable', 'branchRepo.branch.branchable', 'agencyRepo.branch.branchable'])->whereIn('id', $qc)->get();
        // dd($audit);
        $data = [];
        foreach ($audit as $k => $item) {
            $state = '';
            switch ($item->qmsheet->type) {
                case 'branch':
                    if ($item->branch != null) {
                        $user = array_filter($item->branch->branchable->toArray(), function ($val) use ($item) {
                            // dd($val);
                            // return($val['type']=='Collection_Manager' && $val['product_id']==$item->product_id);
                            return $val['manager_id'] == $item->collection_manager_id;
                        });
                        if (!empty($user)) {
                            $k = array_key_first($user);
                            $state = ['name' => $user[$k]['user']['name'], 'id' => $user[$k]['user']['id']];
                        }
                    }
                    break;
                case 'yard':
                    if ($item->yard != null) {
                        $user = array_filter($item->yard->branch->branchable->toArray(), function ($val) use ($item) {
                            // return($val['type']=='Collection_Manager' && $val['product_id']==$item->product_id);
                            return $val['manager_id'] == $item->collection_manager_id;
                        });
                        if (!empty($user)) {
                            $k = array_key_first($user);
                            $state = ['name' => $user[$k]['user']['name'], 'id' => $user[$k]['user']['id']];
                        }
                    }
                    break;
                case 'agency':
                    // $state=$item->agency->branch->name;
                    if ($item->agency != null) {
                        $user = array_filter($item->agency->branch->branchable->toArray(), function ($val) use ($item) {
                            // return($val['type']=='Collection_Manager' && $val['product_id']==$item->product_id);
                            return $val['manager_id'] == $item->collection_manager_id;
                        });
                        if (!empty($user)) {
                            $k = array_key_first($user);
                            $state = ['name' => $user[$k]['user']['name'], 'id' => $user[$k]['user']['id']];
                        }
                    }
                    break;
                case 'agency_repo':
                    // $state=$item->agency->branch->name;
                    $user = array_filter($item->agencyRepo->branch->branchable->toArray(), function ($val) use ($item) {
                        // return($val['type']=='Collection_Manager' && $val['product_id']==$item->product_id);
                        return $val['manager_id'] == $item->collection_manager_id;
                    });
                    if (!empty($user)) {
                        $k = array_key_first($user);
                        $state = ['name' => $user[$k]['user']['name'], 'id' => $user[$k]['user']['id']];
                    }
                    break;
                case 'branch_repo':
                    // $state=$item->agency->branch->name;
                    $user = array_filter($item->branchRepo->branch->branchable->toArray(), function ($val) use ($item) {
                        // return($val['type']=='Collection_Manager' && $val['product_id']==$item->product_id);
                        return $val['manager_id'] == $item->collection_manager_id;
                    });
                    if (!empty($user)) {
                        $k = array_key_first($user);
                        $state = ['name' => $user[$k]['user']['name'], 'id' => $user[$k]['user']['id']];
                    }
                    break;
            }
            $total = 0;

            $resultAudit = AuditResult::where('audit_id', $item->id)->get(['id', 'audit_id', 'selected_option', 'sub_parameter_id'])->where('selected_option', 'N/A')->pluck('selected_option', 'sub_parameter_id');
            $total = $item->qmsheet->parameter->map(function ($val) use ($total, $resultAudit) {
                $subTotal = 0;
                // $total=$total+$val->qm_sheet_sub_parameter->sum('weight');
                $subTotal = $val->qm_sheet_sub_parameter->map(function ($value) use ($subTotal, $resultAudit) {
                    if (!isset($resultAudit[$value->id])) {
                        return $value;
                    }
                });
                $total = $total + $subTotal->sum('weight');
                return $total;
            });
            // $total=$item->qmsheet->parameter->map(function($val) use($total){
            //     $total=$total+$val->qm_sheet_sub_parameter->sum('weight');
            //     return $total;
            // });
            if (isset($state['id'])) {
                if (isset($data[$state['id']])) {
                    $data[$state['id']] = [
                        'name' => $state['name'],
                        'point' => ($data[$state['id']]['point'] + $item->overall_score),
                        'total' => ($data[$state['id']]['total'] + array_sum($total->toArray()))
                    ];
                } else {
                    $data[$state['id']] = ['name' => $state['name'], 'point' => $item->overall_score, 'total' => array_sum($total->toArray())];
                }
            }
        }
        // dd($data);
        usort($data, function ($a, $b) {
            return (($b['point'] / $b['total']) * 100) - (($a['point'] / $a['total']) * 100);
        });
        $top10 = array_slice($data, 0, 10);
        usort($data, function ($a, $b) {
            return (($a['point'] / $a['total']) * 100) - (($b['point'] / $b['total']) * 100);
        });
        $bottom10 = array_slice($data, 0, 10);
        return ['top' => $top10, 'bottom' => $bottom10, 'data' => $data];
    }

    function getTopAgency()
    {
        $qc = Qc::all()->pluck('audit_id')->toArray();
        $audit = Audit::with(['qmsheet.parameter.qm_sheet_sub_parameter', 'branch.branchable', 'agency.branch.branchable', 'yard.branch.branchable'])->where('agency_id', '!=', null)->get();

        $audit = $audit->whereIn('id', $qc);
        // dd($audit);
        $data = [];
        foreach ($audit as $k => $item) {
            $total = 0;
            // $total=$item->qmsheet->parameter->map(function($val) use($total){
            //     $total=$total+$val->qm_sheet_sub_parameter->sum('weight');
            //     return $total;
            // });
            $total = $this->getTotalPoint($item->id);
            if (isset($data[$item->agency_id])) {
                // $data[$item->agency_id]=['name'=>$item->agency->name,'point'=>($data[$item->agency_id]['point']+$item->overall_score)
                // ,'total'=>($data[$item->agency_id]['total']+array_sum($total->toArray()))];
                $data[$item->agency_id] = [
                    'name' => $item->agency->name,
                    'point' => ($data[$item->agency_id]['point'] + $item->overall_score),
                    'total' => ($data[$item->agency_id]['total'] + $total)
                ];
            } else {
                // $data[$item->agency_id]=['name'=>$item->agency->name,'point'=>$item->overall_score,'total'=>array_sum($total->toArray())];
                $data[$item->agency_id] = ['name' => $item->agency->name, 'point' => $item->overall_score, 'total' => $total];
            }
        }
        usort($data, function ($a, $b) {
            return (($b['point'] / $b['total']) * 100) - (($a['point'] / $a['total']) * 100);
        });
        $top10 = array_slice($data, 0, 10);
        usort($data, function ($a, $b) {
            return (($a['point'] / $a['total']) * 100) - (($b['point'] / $b['total']) * 100);
        });
        $bottom10 = array_slice($data, 0, 10);
        return ['top' => $top10, 'bottom' => $bottom10];
    }


    public function getFilterData($request)
    {
        // dd($request->all());
        $lob = ($request->filterlob == 'all') ? ['collection', 'commercial_vehicle', 'rural', 'alliance', 'credit_card',] : [$request->filterlob];
        $cids = [];
        if ($request->filterzone == 'all') {
            if ($request->filterstate == 'all') {
                $state_id = DB::table('states')->get(['id', 'name'])->pluck('id');
                $cids = DB::table('cities')->whereIn('state_id', $state_id)->get(['id', 'name'])->pluck('id');
            } else {
                $state_id = DB::table('states')->where('id', $request->filterstate)->get(['id', 'name'])->pluck('id');
                $cids = DB::table('cities')->whereIn('state_id', $state_id)->get(['id', 'name'])->pluck('id');
            }
        } else {
            if ($request->filterstate == 'all') {
                $state_id = DB::table('states')->where('region_id', $request->filterzone)->get(['id', 'name'])->pluck('id');
                $cids = DB::table('cities')->whereIn('state_id', $state_id)->get(['id', 'name'])->pluck('id');
            } else {
                $state_id = DB::table('states')->where('region_id', $request->filterzone)->where('id', $request->filterstate)->get(['id', 'name'])->pluck('id');
                $cids = DB::table('cities')->whereIn('state_id', $state_id)->get(['id', 'name'])->pluck('id');
            }
        }
        if ($request->filterbranch == 'all') {
            $branch = Branch::whereIn('city_id', $cids)->whereIn('lob', $lob)->get()->pluck('id');
            $agency = Agency::whereIn('branch_id', $branch)->get()->pluck('id');
            $yard = Yard::whereIn('branch_id', $branch)->get()->pluck('id');
        } else {
            $branch = Branch::where('id', $request->filterbranch)->whereIn('lob', $lob)->first();
            $agency = Agency::where('branch_id', $branch)->get()->pluck('id');
            $yard = Yard::where('branch_id', $branch)->get()->pluck('id');
        }
        // dd($branch,$agency,$yard);
        if ($request->filterProduct == 'all') {
            $query = Audit::with(['qmsheet.parameter.qm_sheet_sub_parameter', 'branch.branchable', 'agency.branch.branchable', 'yard.branch.branchable'])->whereIn('branch_id', $branch)->orWhereIn('yard_id', $yard)
                ->orWhereIn('agency_id', $agency);
        } else {
            $query = Audit::with(['qmsheet.parameter.qm_sheet_sub_parameter', 'branch.branchable', 'agency.branch.branchable', 'yard.branch.branchable'])->whereIn('branch_id', $branch)->orWhereIn('agency_id', $agency)
                ->orWhereIn('yard_id', $yard)
                ->Where('product_id', $request->filterProduct);
        }
        $cycle = ['start' => '1970-01-01', 'end' => '2120-12-31'];
        if ($request->has('filteraudit_cycle') && $request->filteraudit_cycle != 'All' && $request->filteraudit_cycle != 'custom') {
            $cycle = $this->getAuditCycle($request->filteraudit_cycle);
            // $query->whereBetween('created_at',[$cycle['start']." 00:00:00",$cycle['end']." 23:59:59"]);
        } else if ($request->has('filteraudit_cycle') && $request->filteraudit_cycle == 'custom') {
            $dates = explode(' - ', $request->filteraudit_cycle_custom);
            $cycle = ['start' => Carbon::parse($dates[0])->toDateString(), 'end' => Carbon::parse($dates[1])->toDateString()];
        }
        $audit = $query->get();
        $qc = Qc::all()->pluck('audit_id')->toArray();
        $audit = $audit->whereIn('id', $qc);
        // dd($audit);
        $data = [];
        foreach ($audit as $k => $item) {
            $state = '';
            switch ($item->qmsheet->type) {
                case 'branch':
                    $user = array_filter($item->branch->branchable->toArray(), function ($val) use ($item) {
                        // dd($val);
                        // return($val['type']=='Collection_Manager' && $val['product_id']==$item->product_id);
                        return $val['manager_id'] == $item->collection_manager_id;
                    });
                    if (!empty($user)) {
                        $k = array_key_first($user);
                        $state = ['name' => $user[$k]['user']['name'], 'id' => $user[$k]['user']['id']];
                    }
                    break;
                case 'repo_yard':
                    // $state=$item->yard->branch->name;
                    $user = array_filter($item->yard->branch->branchable->toArray(), function ($val) use ($item) {
                        // return($val['type']=='Collection_Manager' && $val['product_id']==$item->product_id);
                        return $val['manager_id'] == $item->collection_manager_id;
                    });
                    if (!empty($user)) {
                        $k = array_key_first($user);
                        $state = ['name' => $user[$k]['user']['name'], 'id' => $user[$k]['user']['id']];
                    }
                    break;
                case 'agency':
                    // $state=$item->agency->branch->name;
                    $user = array_filter($item->agency->branch->branchable->toArray(), function ($val) use ($item) {
                        // return($val['type']=='Collection_Manager' && $val['product_id']==$item->product_id);
                        return $val['manager_id'] == $item->collection_manager_id;
                    });
                    if (!empty($user)) {
                        $k = array_key_first($user);
                        $state = ['name' => $user[$k]['user']['name'], 'id' => $user[$k]['user']['id']];
                    }
                    break;
                case 'agency_repo':
                    $user = array_filter($item->agencyRepo->branch->branchable->toArray(), function ($val) use ($item) {
                        // return($val['type']=='Collection_Manager' && $val['product_id']==$item->product_id);
                        return $val['manager_id'] == $item->collection_manager_id;
                    });
                    if (!empty($user)) {
                        $k = array_key_first($user);
                        $state = ['name' => $user[$k]['user']['name'], 'id' => $user[$k]['user']['id']];
                    }
                    break;
                case 'branch_repo':
                    $user = array_filter($item->branchRepo->branch->branchable->toArray(), function ($val) use ($item) {
                        // return($val['type']=='Collection_Manager' && $val['product_id']==$item->product_id);
                        return $val['manager_id'] == $item->collection_manager_id;
                    });
                    if (!empty($user)) {
                        $k = array_key_first($user);
                        $state = ['name' => $user[$k]['user']['name'], 'id' => $user[$k]['user']['id']];
                    }
                    break;
            }

            $total = 0;
            // $total=$item->qmsheet->parameter->map(function($val) use($total){
            //     $total=$total+$val->qm_sheet_sub_parameter->sum('weight');
            //     return $total;
            // });
            $total = $this->getTotalPoint($item->id);
            if (($item->created_at >= $cycle['start'] . " 00:00:00") && ($item->created_at <= $cycle['end'] . " 23:59:59")) {
                if ($request->filterProduct != 'all' && $item->product_id == $request->filterProduct) {
                    if (isset($data[$state['id']])) {
                        // $data[$state['id']]=['name'=>$state['name'],'point'=>($data[$state['id']]['point']+$item->overall_score)
                        // ,'total'=>($data[$state['id']]['total']+array_sum($total->toArray()))];
                        $data[$state['id']] = [
                            'name' => $state['name'],
                            'point' => ($data[$state['id']]['point'] + $item->overall_score),
                            'total' => ($data[$state['id']]['total'] + $total)
                        ];
                    } else {
                        // $data[$state['id']]=['name'=>$state['name'],'point'=>$item->overall_score,'total'=>array_sum($total->toArray())];
                        $data[$state['id']] = ['name' => $state['name'], 'point' => $item->overall_score, 'total' => $total];
                    }
                } else if ($request->filterProduct == 'all') {
                    if (isset($data[$state['id']])) {
                        // $data[$state['id']]=['name'=>$state['name'],'point'=>($data[$state['id']]['point']+$item->overall_score)
                        // ,'total'=>($data[$state['id']]['total']+array_sum($total->toArray()))];
                        $data[$state['id']] = [
                            'name' => $state['name'],
                            'point' => ($data[$state['id']]['point'] + $item->overall_score),
                            'total' => ($data[$state['id']]['total'] + $total)
                        ];
                    } else {
                        $data[$state['id']] = ['name' => $state['name'], 'point' => $item->overall_score, 'total' => $total];
                    }
                }
            }
        }
        // dd($data);
        return $data;
    }

    public function getagencyOfCollection($id)
    {
        $branchId = Branchable::where(['manager_id' => $id, 'type' => 'Collection_Manager'])->get(['id', 'branch_id'])->pluck('branch_id');
        $agency = Agency::whereIn('branch_id', $branchId)->get(['id', 'name']);
        // $branch=Branch::whereIn('id',$branchId)->get(['id','name']);
        // $br=BranchRepo::whereIn('branch_id',$branchId)->get(['id','name']);
        // $ar=AgencyRepo::whereIn('branch_id',$branchId)->get(['id','name']);
        $audit = Audit::whereIn('agency_id', $agency->pluck('id'))->get()->pluck('overall_score', 'agency_id');
        // $audit=Audit::whereIn('agency_id',$agency->pluck('id'))->where('collection_manager_id',$id)->get()->pluck('overall_score','agency_id');
        // $branchaudit=Audit::whereIn('branch_id',$branch->pluck('id'))->where('collection_manager_id',$id)->get()->pluck('overall_score','branch_id');
        // $braudit=Audit::whereIn('branch_repo_id',$br->pluck('id'))->where('collection_manager_id',$id)->get()->pluck('overall_score','branch_repo_id');
        // $araudit=Audit::whereIn('agency_repo_id',$ar->pluck('id'))->where('collection_manager_id',$id)->get()->pluck('overall_score','agency_repo_id');
        return response()->json([
            'data' => $agency,
            // 'branch'=>$branch,'br'=>$br,'ar'=>$ar,
            // 'branchpoint'=>$branchaudit,'brpoint'=>$braudit,'arpoint'=>$araudit,
            'point' => $audit
        ]);
    }
    public function getAgencyParameter($agency_id)
    {
        $audit = Audit::with(['audit_parameter_result.parameter_detail', 'audit_results'])->where('agency_id', $agency_id)->latest()->first();
        $data = ['id' => $audit->id, 'audit_parameter_result' => []];
        foreach ($audit->audit_parameter_result as $k => $item) {
            $data['audit_parameter_result'][$k] = $item;
            $data['audit_parameter_result'][$k]['orignal_weight'] = $audit->audit_results->where('parameter_id', $item->id)->sum('score');
        }
        // dd($data);
        return response()->json(['data' => $data]);
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
    public function bottomProductParameter()
    {
        ini_set('memory_limit', '-1');
        $qc = Qc::all()->pluck('audit_id')->toArray();
        $audit = Audit::with(['audit_parameter_result.parameter_detail', 'audit_parameter_result.result2.sub_parameter_detail'])->get(['id']);
        $audit = $audit->whereIn('id', $qc);
        $data = [];
        foreach ($audit as $k => $item) {
            foreach ($item->audit_parameter_result as $k => $value) {
                if ($value->parameter_detail != null && $value->orignal_weight != null || $value->orignal_weight != 0) {
                    $totalWeight = 0;
                    $weight = 0;
                    $resultData = $value->result2->where('parameter_id', $value->parameter_id);
                    foreach ($resultData as $k => $rd) {
                        $weight = $weight + ((int) $rd->score);
                        if ($rd->score != 'N/A') {
                            $totalWeight = $totalWeight + ((int) $rd->sub_parameter_detail->weight);
                        }
                    }
                    $per = 0;
                    if ($totalWeight > 0) {
                        $per = ($weight / $totalWeight) * 100;
                    }
                    if ($per > 0) {
                        $data[] = ['name' => $value->parameter_detail->parameter, 'point' => round($per, 2), 'id' => $value->audit_id];
                    }
                }
            }
        }
        usort($data, function ($a, $b) {
            return $b['point'] - $a['point'];
        });
        $top10 = array_slice($data, 0, 10);
        usort($data, function ($a, $b) {
            return $a['point'] - $b['point'];
        });
        $bottom10 = array_slice($data, 0, 10);
        // dd(['top'=>$top10,'bottom'=>$bottom10]);
        return ['top' => $top10, 'bottom' => $bottom10];
    }
    // public function auditDumpDownload(Request $request)
    //     {

    //         $request->validate([
    //             //'audit_agency_name' => 'nullable|exists:users,id', 
    //             'agency_name' => 'required',
    //             'start_date' => 'nullable|date',
    //             'end_date' => 'nullable|date|after_or_equal:start_date',
    //         ]);

    //         $auditAgencyId = $request->input('audit_agency_name');
    //         $agencyId = $request->input('agency_name');
    //         $startDate = $request->input('start_date');
    //         $endDate = $request->input('end_date');
    //         $client_id = auth()->user()->client_id;public function auditDumpDownload(Request $request)
    //     {

    //         $request->validate([
    //             //'audit_agency_name' => 'nullable|exists:users,id', 
    //             // 'agency_name' => 'required',
    //             'start_date' => 'nullable|date',
    //             'end_date' => 'nullable|date|after_or_equal:start_date',
    //         ]);
    //         //echo "<pre>"; print_r($request->all()); die;
    //         // $auditAgencyId = $request->input('audit_agency_name');
    //         // $agencyId = $request->input('agency_name');
    //         $startDate = $request->input('start_date');
    //         $endDate = $request->input('end_date');
    //         $client_id = auth()->user()->client_id;

    //         //return Excel::download(new AuditsExport($auditAgencyId, $agencyId, $startDate, $endDate, $client_id), 'audits.xlsx');
    //         return Excel::download(new AuditsExport($startDate, $endDate, $client_id), 'audits.xlsx');
    //     }
    public function auditDumpDownload(Request $request)
    {

        $request->validate([
            //'audit_agency_name' => 'nullable|exists:users,id', 
            // 'agency_name' => 'required',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);
        //echo "<pre>"; print_r($request->all()); die;
        // $auditAgencyId = $request->input('audit_agency_name');
        // $agencyId = $request->input('agency_name');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $client_id = auth()->user()->client_id;

        //return Excel::download(new AuditsExport($auditAgencyId, $agencyId, $startDate, $endDate, $client_id), 'audits.xlsx');
        if (auth()->user()->client_id == 285) {

            return Excel::download(new AuditsExportCategory($startDate, $endDate, $client_id), 'audits.xlsx');
        } else {

            return Excel::download(new AuditsExport($startDate, $endDate, $client_id), 'audits.xlsx');
        }
    }

    public function scheduleAuditDownload(Request $request)
    {
        // Validate input
        $request->validate([
            'audit_agency_name' => 'nullable|exists:users,id', // Ensure agency exists in users table
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $auditAgencyId = $request->input('audit_agency_name');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $client_id = auth()->user()->client_id;

        // Attempt to download the file
        try {
            return Excel::download(
                new ScheduleAuditExport($auditAgencyId, $startDate, $endDate, $client_id),
                'auditSchedule.xlsx'
            );
        } catch (\Exception $e) {
            \Log::error('Excel Download Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate audit report.');
        }
    }



    // V grade working fine without filter for Client Dashbaord
    public function getGradeData(Request $request, $grade)
    {
        try {

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
                ->select(
                    'audits.grade',
                    'regions.name as region_name',
                    DB::raw('COUNT(audits.grade) as grade_count')
                )
                ->where('audits.grade', $grade)
                ->where('audits.client_id', auth()->user()->client_id)
                ->where('audits.status', '<=', 1)
                ->whereBetween('audits.created_at', [$startDate, $endDate])
                ->groupBy('audits.grade', 'regions.name')
                ->get();

            //\Log::info("Query Result: ", $data->toArray());
            return response()->json($data);
        } catch (\Exception $e) {
            \Log::error('Error fetching grade data: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to retrieve data.'], 500);
        }
    }


    public function getAuditsByProductAndRegion($productId, $region)
    {
        try {
            $databyZoneRegion = DB::table('audits')
                ->join('agencies', 'audits.agency_id', '=', 'agencies.id')
                ->join('regions', 'agencies.region_id', '=', 'regions.id')
                ->leftJoin('audit_allocation', 'audits.agency_id', '=', 'audit_allocation.agency_id')
                ->leftJoin('closure_audits', 'audits.id', '=', 'closure_audits.audit_id')
                ->where('audits.product_id', $productId)
                ->where('regions.name', $region)
                ->select(
                    'regions.name as region_name',
                    DB::raw('AVG(audits.score_percentage) as average_score'),
                    DB::raw('COUNT(DISTINCT audits.id) as audit_count'),
                    DB::raw('COUNT(DISTINCT audit_allocation.agency_id) as process_review_agency_count'),
                    DB::raw('COUNT(DISTINCT CASE WHEN closure_audits.status = 0 THEN closure_audits.audit_id END) as sent_for_closure_count'),
                    DB::raw('COUNT(DISTINCT CASE WHEN closure_audits.status = 1 THEN closure_audits.audit_id END) as closure_completed_count')
                )
                ->groupBy('regions.name')
                ->get();

            return response()->json($databyZoneRegion);
        } catch (\Exception $e) {
            \Log::error('Error fetching audit data by product and region: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to retrieve data.'], 500);
        }
    }

    public function getClientDashboard(Request $request)
    {
        if ($request->audit_cycle_id && $request->audit_cycle_id == 0) {
            return redirect()->route('getClientDashboard');
        }
        // Get audit cycles for the client
        $auditCycle = AuditCycle::where('client_id', Auth::user()->client_id)
            ->orderBy('id', 'desc')
            ->limit(6)
            ->get()
            ->values();

        $audit_type = ($request->audit_type && $request->audit_type != 'all') ? $request->audit_type : 'all';

        if ($request->audit_cycle_id && $request->audit_cycle_id != 0) {
            $currentCycleId = $request->audit_cycle_id;
            $auditCyclePre = AuditCycle::where('client_id', Auth::user()->client_id)
                ->where('id', '<', $currentCycleId)
                ->orderBy('id', 'desc')
                ->first();
            if ($auditCyclePre) {
                $previousCycleId = $auditCyclePre->id;
            } else {
                $previousCycleId = null;
            }
        } else {
            // Find current and previous cycle indices
            $currentIndex = $auditCycle->search(function ($cycle) {
                return $cycle->status == 1;
            });

            // If there is only one cycle, set previousCycleId to null
            if ($auditCycle->count() <= 1) {
                $previousIndex = false;
            } else {
                $previousIndex = ($currentIndex !== false && $currentIndex + 1 < $auditCycle->count()) ? $currentIndex + 1 : false;
            }

            $currentCycleId = ($currentIndex !== false) ? $auditCycle[$currentIndex]->id : null;
            $previousCycleId = ($previousIndex !== false) ? $auditCycle[$previousIndex]->id : null;

            // If current and previous cycle IDs are the same, set previousCycleId to null
            if ($currentCycleId && $previousCycleId && $currentCycleId == $previousCycleId) {
                $previousCycleId = null;
            }
        }

        // Initialize audit data array with only current and previous cycle data
        $auditData = [
            'current_cycle_count' => 0,
            'current_cycle_score' => 0,
            'previous_cycle_count' => 0,
            'previous_cycle_score' => 0,
            'status_distribution' => [],
            'allocation_data' => []
        ];

        // Get data for both current and previous cycles in a single optimized query
        if ($currentCycleId || $previousCycleId) {
            $cycleData = $this->getCycleDataForTwoCycles($currentCycleId, $previousCycleId, Auth::user()->client_id, $audit_type);

            // Assign data - first value is current cycle, second is previous cycle
            if (isset($cycleData['current'])) {
                $auditData['current_cycle_count'] = $cycleData['current']['audit_count'];
                $auditData['current_cycle_score'] = $cycleData['current']['audit_score'];
            }

            if (isset($cycleData['previous'])) {
                $auditData['previous_cycle_count'] = $cycleData['previous']['audit_count'];
                $auditData['previous_cycle_score'] = $cycleData['previous']['audit_score'];
            }
        }

        if (Auth::user()->client_id == 74) {

            $auditData['tabs'] = [
                'physical' => $this->getCycleDataForTwoCycles(
                    $currentCycleId,
                    $previousCycleId,
                    Auth::user()->client_id,
                    $audit_type,
                    0 // Physical
                ),
                'virtual' => $this->getCycleDataForTwoCycles(
                    $currentCycleId,
                    $previousCycleId,
                    Auth::user()->client_id,
                    $audit_type,
                    1 // Virtual
                ),
            ];
        }

        // Get overall audit statistics
        $overallStats = $this->getOverallAuditStats(Auth::user()->client_id, $audit_type);
        $auditData = array_merge($auditData, $overallStats);

        // Get status distribution
        $auditData['status_distribution'] = $this->getStatusDistribution(Auth::user()->client_id, $currentCycleId, $audit_type);
        // echo "<pre>"; print_r($auditData['status_distribution']); die;
        // Get allocation data
        $auditData['allocation_data'] = $this->getAllocationData(Auth::user()->client_id, $currentCycleId, $previousCycleId, $audit_type);

        $auditData['getActionPlanningData'] = $this->getActionPlanningData(Auth::user()->client_id, $currentCycleId, $previousCycleId, $audit_type);

        $auditData['getAuditAgencyWiseData'] = $this->getAuditAgencyWiseData(Auth::user()->client_id, $currentCycleId, $previousCycleId, $audit_type);
        // echo $currentCycleId; echo $previousCycleId;
        // echo "<pre>"; print_r($auditData); die;
        $auditData['audit_type_distribution'] = $this->getAuditTypeDistribution(Auth::user()->client_id, $currentCycleId, $audit_type);
        if (Auth::user()->is_legal == 1 && Auth::user()->is_compliance == 0) {
            return view('legal.dashbaord');
        }
        return view('newclientdashboard', compact('auditCycle', 'currentCycleId', 'previousCycleId', 'auditData', 'audit_type'));
    }


    public function getNewClientDashboard(Request $request)
    {
        if ($request->audit_cycle_id && $request->audit_cycle_id == 0) {
            return redirect()->route('getNewClientDashboard');
        }
        // Get audit cycles for the client
        $auditCycle = AuditCycle::where('client_id', Auth::user()->client_id)
            ->orderBy('id', 'desc')
            ->limit(6)
            ->get()
            ->values();

        $audit_type = ($request->audit_type && $request->audit_type != 'all') ? $request->audit_type : 'all';

        if ($request->audit_cycle_id && $request->audit_cycle_id != 0) {
            $currentCycleId = $request->audit_cycle_id;
            $auditCyclePre = AuditCycle::where('client_id', Auth::user()->client_id)
                ->where('id', '<', $currentCycleId)
                ->orderBy('id', 'desc')
                ->first();
            if ($auditCyclePre) {
                $previousCycleId = $auditCyclePre->id;
            } else {
                $previousCycleId = null;
            }
        } else {
            // Find current and previous cycle indices
            $currentIndex = $auditCycle->search(function ($cycle) {
                return $cycle->status == 1;
            });

            // If there is only one cycle, set previousCycleId to null
            if ($auditCycle->count() <= 1) {
                $previousIndex = false;
            } else {
                $previousIndex = ($currentIndex !== false && $currentIndex + 1 < $auditCycle->count()) ? $currentIndex + 1 : false;
            }

            $currentCycleId = ($currentIndex !== false) ? $auditCycle[$currentIndex]->id : null;
            $previousCycleId = ($previousIndex !== false) ? $auditCycle[$previousIndex]->id : null;

            // If current and previous cycle IDs are the same, set previousCycleId to null
            if ($currentCycleId && $previousCycleId && $currentCycleId == $previousCycleId) {
                $previousCycleId = null;
            }
        }

        // Initialize audit data array with only current and previous cycle data
        $auditData = [
            'current_cycle_count' => 0,
            'current_cycle_score' => 0,
            'previous_cycle_count' => 0,
            'previous_cycle_score' => 0,
            'status_distribution' => [],
            'allocation_data' => []
        ];

        // Get data for both current and previous cycles in a single optimized query
        if ($currentCycleId || $previousCycleId) {
            $cycleData = $this->getCycleDataForTwoCycles($currentCycleId, $previousCycleId, Auth::user()->client_id, $audit_type);

            // Assign data - first value is current cycle, second is previous cycle
            if (isset($cycleData['current'])) {
                $auditData['current_cycle_count'] = $cycleData['current']['audit_count'];
                $auditData['current_cycle_score'] = $cycleData['current']['audit_score'];
            }

            if (isset($cycleData['previous'])) {
                $auditData['previous_cycle_count'] = $cycleData['previous']['audit_count'];
                $auditData['previous_cycle_score'] = $cycleData['previous']['audit_score'];
            }
        }

        if (Auth::user()->client_id == 74) {

            $auditData['tabs'] = [
                'physical' => $this->getCycleDataForTwoCycles(
                    $currentCycleId,
                    $previousCycleId,
                    Auth::user()->client_id,
                    $audit_type,
                    0 // Physical
                ),
                'virtual' => $this->getCycleDataForTwoCycles(
                    $currentCycleId,
                    $previousCycleId,
                    Auth::user()->client_id,
                    $audit_type,
                    1 // Virtual
                ),
            ];
        }

        // Get overall audit statistics
        $overallStats = $this->getOverallAuditStats(Auth::user()->client_id, $audit_type);
        $auditData = array_merge($auditData, $overallStats);

        // Get status distribution
        $auditData['status_distribution'] = $this->getStatusDistribution(Auth::user()->client_id, $currentCycleId, $audit_type);
        // echo "<pre>"; print_r($auditData['status_distribution']); die;
        // Get allocation data
        $auditData['allocation_data'] = $this->getAllocationData(Auth::user()->client_id, $currentCycleId, $previousCycleId, $audit_type);

        $auditData['getActionPlanningData'] = $this->getActionPlanningData(Auth::user()->client_id, $currentCycleId, $previousCycleId, $audit_type);

        $auditData['getAuditAgencyWiseData'] = $this->getAuditAgencyWiseData(Auth::user()->client_id, $currentCycleId, $previousCycleId, $audit_type);
        // echo $currentCycleId; echo $previousCycleId;
        // echo "<pre>"; print_r($auditData); die;
        $auditData['audit_type_distribution'] = $this->getAuditTypeDistribution(Auth::user()->client_id, $currentCycleId, $audit_type);

        return view('updatednewclientdashboard', compact('auditCycle', 'currentCycleId', 'previousCycleId', 'auditData', 'audit_type'));
    }

    /**
     * Get audit data for current and previous cycles in a single query
     */
    private function getCycleDataForTwoCycles($currentCycleId, $previousCycleId, $clientId, $audit_type, $virtualAudit = null)
    {
        $cycleIds = [];
        if ($currentCycleId) $cycleIds[] = $currentCycleId;
        if ($previousCycleId) $cycleIds[] = $previousCycleId;

        if (empty($cycleIds)) {
            return [
                'current' => ['audit_count' => 0, 'audit_score' => 0],
                'previous' => ['audit_count' => 0, 'audit_score' => 0]
            ];
        }


        $data = DB::table('audits')
            ->whereIn('audits.audit_cycle_id', $cycleIds)
            ->where('audits.client_id', $clientId)
            ->where('audits.status', '<=', 2)
            ->select(
                'audits.audit_cycle_id',
                DB::raw('COUNT(DISTINCT audits.id) as audit_count'),
                DB::raw('ROUND(AVG(
                        CASE 
                            WHEN audits.overall_score > 0 
                            THEN audits.overall_score 
                            ELSE 0
                        END
                    ), 1) AS audit_score')
            );

        if ($audit_type != 'all') {
            $data->leftJoin('qm_sheets', 'audits.qm_sheet_id', '=', 'qm_sheets.id');
            $data->where('qm_sheets.type', $audit_type);
        }

        if (!is_null($virtualAudit)) {

            // Virtual audits
            if ($virtualAudit == 1) {
                $data->where('audits.virtual_audit', 1);
            }

            // Physical audits (0 OR NULL)
            if ($virtualAudit == 0) {
                $data->where(function ($q) {
                    $q->where('audits.virtual_audit', 0)
                        ->orWhereNull('audits.virtual_audit');
                });
            }
        }



        $data = $data->groupBy('audits.audit_cycle_id')
            ->get()
            ->keyBy('audit_cycle_id');

        $result = [
            'current' => ['audit_count' => 0, 'audit_score' => 0],
            'previous' => ['audit_count' => 0, 'audit_score' => 0]
        ];

        // Assign data - first value is current cycle, second is previous cycle
        if ($currentCycleId && isset($data[$currentCycleId])) {
            $result['current'] = [
                'audit_count' => $data[$currentCycleId]->audit_count ?? 0,
                'audit_score' => $data[$currentCycleId]->audit_score ?? 0
            ];
        }

        if ($previousCycleId && isset($data[$previousCycleId])) {
            $result['previous'] = [
                'audit_count' => $data[$previousCycleId]->audit_count ?? 0,
                'audit_score' => $data[$previousCycleId]->audit_score ?? 0
            ];
        }

        return $result;
    }

    /**
     * Get overall audit statistics
     */
    private function getOverallAuditStats($clientId, $audit_type)
    {
        $stats = DB::table('audits')
            ->leftJoin('audit_parameter_results', 'audits.id', '=', 'audit_parameter_results.audit_id');

        if ($audit_type != 'all') {
            $stats->leftJoin('qm_sheets', 'audits.qm_sheet_id', '=', 'qm_sheets.id');
            $stats->where('qm_sheets.type', $audit_type);
        }

        $stats = $stats->where('audits.client_id', $clientId)
            ->where('audits.status', '<=', 2)
            ->select(
                DB::raw('COUNT(DISTINCT audits.id) as total_audits'),
                DB::raw('COUNT(DISTINCT CASE WHEN audits.status = 2 THEN audits.id END) as completed_audits'),
                DB::raw('COUNT(DISTINCT CASE WHEN audits.status = 1 THEN audits.id END) as pending_audits'),
                DB::raw('COUNT(DISTINCT CASE WHEN audits.status = 3 THEN audits.id END) as failed_audits'),
                DB::raw('CASE 
                    WHEN SUM(audit_parameter_results.temp_weight) > 0 
                    THEN ROUND((SUM(audit_parameter_results.without_fatal_score) / SUM(audit_parameter_results.temp_weight)) * 100, 2)
                    ELSE 0 
                END as average_score')
            )
            ->first();

        return [
            'total_audits' => $stats->total_audits ?? 0,
            'completed_audits' => $stats->completed_audits ?? 0,
            'pending_audits' => $stats->pending_audits ?? 0,
            'failed_audits' => $stats->failed_audits ?? 0,
            'average_score' => $stats->average_score ?? 0
        ];
    }

    /**
     * Get audit type distribution data with counts and scores
     */
    private function getAuditTypeDistribution($clientId, $currentCycleId = null, $audit_type)
    {
        $query = DB::table('audits')
            ->join('qm_sheets', 'audits.qm_sheet_id', '=', 'qm_sheets.id')
            ->where('audits.client_id', $clientId);

        // Filter by current cycle if provided
        if ($currentCycleId) {
            $query->where('audits.audit_cycle_id', $currentCycleId);
        }

        if ($audit_type != 'all') {
            $query->where('qm_sheets.type', $audit_type);
        }
        // Get count and average score for each audit type
        return $query->select(
            'qm_sheets.type as type',
            DB::raw('COUNT(*) as count'),
            DB::raw('ROUND(AVG(CASE 
                WHEN qm_sheets.type = "branch" AND audits.overall_score > 0 THEN overall_score
                WHEN qm_sheets.type = "branch_repo" AND audits.overall_score > 0 THEN overall_score  
                WHEN qm_sheets.type = "agency" AND audits.overall_score > 0 THEN overall_score
                WHEN qm_sheets.type = "agency_repo" AND audits.overall_score > 0 THEN overall_score
                WHEN qm_sheets.type = "yard" AND audits.overall_score > 0 THEN overall_score
                WHEN qm_sheets.type = "yard_repo" AND audits.overall_score > 0 THEN overall_score
                ELSE NULL END), 1) as avg_score'),
            DB::raw('ROUND(AVG(CASE 
                WHEN qm_sheets.type = "branch" AND audits.score_percentage > 0 THEN score_percentage
                WHEN qm_sheets.type = "branch_repo" AND audits.score_percentage > 0 THEN score_percentage  
                WHEN qm_sheets.type = "agency" AND audits.score_percentage > 0 THEN score_percentage
                WHEN qm_sheets.type = "agency_repo" AND audits.score_percentage > 0 THEN score_percentage
                WHEN qm_sheets.type = "yard" AND audits.score_percentage > 0 THEN score_percentage
                WHEN qm_sheets.type = "yard_repo" AND audits.score_percentage > 0 THEN score_percentage
                ELSE NULL END), 1) as avg_score_percentage')
        )
            ->where('audits.status', '<=', 2)
            ->groupBy('qm_sheets.type')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->type => [
                    'count' => $item->count,
                    'score' => $item->avg_score ?? 0,
                    'score_percentage' => $item->avg_score_percentage ?? 0
                ]];
            })->toArray();
    }

    /**
     * Get status distribution for current cycle only
     */
    private function getStatusDistribution($clientId, $currentCycleId = null, $audit_type)
    {

        $query = DB::table('audits')
            ->where('audits.client_id', $clientId);

        // Filter by current cycle if provided
        if ($currentCycleId) {
            $query->where('audit_cycle_id', $currentCycleId);
        }

        if ($audit_type != 'all') {
            $query->leftJoin('qm_sheets', 'audits.qm_sheet_id', '=', 'qm_sheets.id');
            $query->where('qm_sheets.type', $audit_type);
        }

        return $query->select(
            'status',
            DB::raw('COUNT(*) as count')
        )
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function ($item) {
                $statusLabels = [
                    0 => 'Submitted',
                    1 => 'Submitted_via_OTP_approval',
                    2 => 'QC_Approved',
                    3 => 'Rejected',
                    4 => 'Pending',
                    5 => 'Saved'
                ];
                return [$statusLabels[$item->status] ?? 'Unknown' => $item->count];
            })->toArray();;
    }

    /**
     * Get action planning data (audit closures) for current and previous cycles
     */
    private function getActionPlanningData($clientId, $currentCycleId = null, $previousCycleId = null, $audit_type)
    {
        //echo "Client ID: $clientId, Current Cycle ID: $currentCycleId, Previous Cycle ID: $previousCycleId\n"; die;
        $result = [
            'current_cycle' => [
                'sent_for_closure' => 0,
                'approved' => 0,
                'rejected' => 0
            ],
            'previous_cycle' => [
                'sent_for_closure' => 0,
                'approved' => 0,
                'rejected' => 0
            ]
        ];

        // Get data for current cycle
        if ($currentCycleId) {
            $currentCycleData = DB::table('audits')
                ->leftJoin('closure_audits', 'audits.id', '=', 'closure_audits.audit_id')
                ->where('audits.client_id', $clientId)
                ->where('audits.audit_cycle_id', $currentCycleId)
                ->where('audits.status', '<=', 2);
            if ($audit_type != 'all') {
                $currentCycleData->leftJoin('qm_sheets', 'audits.qm_sheet_id', '=', 'qm_sheets.id');
                $currentCycleData->where('qm_sheets.type', $audit_type);
            }

            $currentCycleData = $currentCycleData->select(
                DB::raw('COUNT(CASE WHEN closure_audits.status = 0 THEN 1 END) as sent_for_closure'),
                DB::raw('COUNT(CASE WHEN closure_audits.status = 1 THEN 1 END) as approved'),
                DB::raw('COUNT(CASE WHEN closure_audits.status = 2 THEN 1 END) as rejected')
            )
                ->first();

            $result['current_cycle'] = [
                'sent_for_closure' => $currentCycleData->sent_for_closure ?? 0,
                'approved' => $currentCycleData->approved ?? 0,
                'rejected' => $currentCycleData->rejected ?? 0
            ];
        }

        // Get data for previous cycle
        if ($previousCycleId) {
            $previousCycleData = DB::table('audits')
                ->leftJoin('closure_audits', 'audits.id', '=', 'closure_audits.audit_id')
                ->where('audits.client_id', $clientId)
                ->where('audits.audit_cycle_id', $previousCycleId)
                ->where('audits.status', '<=', 2);
            if ($audit_type != 'all') {
                $previousCycleData->leftJoin('qm_sheets', 'audits.qm_sheet_id', '=', 'qm_sheets.id');
                $previousCycleData->where('qm_sheets.type', $audit_type);
            }
            $previousCycleData = $previousCycleData->select(
                DB::raw('COUNT(CASE WHEN closure_audits.status = 0 THEN 1 END) as sent_for_closure'),
                DB::raw('COUNT(CASE WHEN closure_audits.status = 1 THEN 1 END) as approved'),
                DB::raw('COUNT(CASE WHEN closure_audits.status = 2 THEN 1 END) as rejected')
            )
                ->first();

            $result['previous_cycle'] = [
                'sent_for_closure' => $previousCycleData->sent_for_closure ?? 0,
                'approved' => $previousCycleData->approved ?? 0,
                'rejected' => $previousCycleData->rejected ?? 0
            ];
        }

        // echo "<pre>"; print_r($result); die;

        return $result;
    }

    private function getAllocationData($clientId, $currentCycleId = null, $previousCycleId = null, $audit_type)
    {
        $result = DB::table('audit_allocation')
            ->selectRaw("
                SUM(CASE WHEN audit_cycle_id = ? THEN 1 ELSE 0 END) as currentCycleCount,
                SUM(CASE WHEN audit_cycle_id = ? THEN 1 ELSE 0 END) as previousCycleCount
            ", [$currentCycleId, $previousCycleId]);

        if ($audit_type != 'all') {

            if ($audit_type == "agency_repo") {
                $audit_type = 'agencyrepo';
            }
            if ($audit_type == "branch_repo") {
                $audit_type = 'branchrepo';
            }
            if ($audit_type == "yard_repo") {
                $audit_type = 'yardrepo';
            }

            $result->where('type_of_agency', $audit_type);
        }

        $result = $result->where('audit_allocation.client_id', $clientId)
            ->first();

        return [
            'currentCycleCount' => $result->currentCycleCount,
            'previousCycleCount' => $result->previousCycleCount
        ];
    }

    private function getAuditAgencyWiseData($clientId, $currentCycleId = null, $previousCycleId = null, $audit_type)
    {

        $allocation = DB::table('audit_allocation')
            ->selectRaw("process_review_agency_id as agency_id,
                SUM(CASE WHEN audit_cycle_id = ? THEN 1 ELSE 0 END) as currentCycleCount,
                SUM(CASE WHEN audit_cycle_id = ? THEN 1 ELSE 0 END) as previousCycleCount
            ", [$currentCycleId, $previousCycleId]);

        if ($audit_type != 'all') {

            if ($audit_type == "agency_repo") {
                $audit_type = 'agencyrepo';
            }
            if ($audit_type == "branch_repo") {
                $audit_type = 'branchrepo';
            }
            if ($audit_type == "yard_repo") {
                $audit_type = 'yardrepo';
            }

            $allocation->where('type_of_agency', $audit_type);
        }
        $allocation = $allocation->where('audit_allocation.client_id', $clientId)
            ->groupBy('audit_allocation.process_review_agency_id')
            ->get();

        $auditCountAndScore = DB::table('audits')
            ->leftJoin('closure_audits', 'audits.id', '=', 'closure_audits.audit_id');

        if ($audit_type != 'all') {
            $auditCountAndScore->leftJoin('qm_sheets', 'audits.qm_sheet_id', '=', 'qm_sheets.id');
            $auditCountAndScore->where('qm_sheets.type', $audit_type);
        }
        $auditCountAndScore = $auditCountAndScore->selectRaw("
                    audits.audit_agency_id as agency_id,

                    -- Counts
                    SUM(CASE WHEN audits.audit_cycle_id = ? THEN 1 ELSE 0 END) as currentCycleCount,
                    SUM(CASE WHEN audits.audit_cycle_id = ? THEN 1 ELSE 0 END) as previousCycleCount,

                    -- Scores
                    ROUND(AVG(
                        CASE 
                            WHEN audits.audit_cycle_id = ? AND audits.overall_score > 0 
                            THEN audits.overall_score 
                            ELSE NULL
                        END
                    ), 1) AS currentCycleScore,

                    ROUND(AVG(
                        CASE 
                            WHEN audits.audit_cycle_id = ? AND audits.overall_score > 0 
                            THEN audits.overall_score 
                            ELSE NULL
                        END
                    ), 1) AS previousCycleScore,

                    -- Action plan counts
                    SUM(CASE WHEN audits.audit_cycle_id = ? AND closure_audits.status = 1 THEN 1 ELSE 0 END) as actionPlanCurrentCycle,
                    SUM(CASE WHEN audits.audit_cycle_id = ? AND closure_audits.status = 1 THEN 1 ELSE 0 END) as actionPlanPreviousCycle
                ", [
            $currentCycleId,
            $previousCycleId,
            $currentCycleId,
            $previousCycleId,
            $currentCycleId,
            $previousCycleId
        ])
            ->where('audits.status', '<=', 2)
            ->where('audits.client_id', $clientId)
            ->groupBy('audits.audit_agency_id')
            ->get();

        return [
            'allocation' => $allocation,
            'auditCountAndScore' => $auditCountAndScore
        ];

        // echo "<pre>"; print_r($auditCountAndScore); die;        

    }
    public function openPointersDump(Request $request)
    {

        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);


        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $client_id = auth()->user()->client_id;

        //return Excel::download(new AuditsExport($auditAgencyId, $agencyId, $startDate, $endDate, $client_id), 'audits.xlsx');
        // return Excel::download(new OpenPointersDump($startDate, $endDate, $client_id), 'open-pointers-unsatisfactory-sub-parameters.xlsx');
        return Excel::download(
            new OpenPointersWithSummaryExport($startDate, $endDate, $client_id),
            'open-pointers-unsatisfactory-sub-parameters.xlsx'
        );
    }
}
