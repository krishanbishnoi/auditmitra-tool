<?php

namespace App\Http\Controllers;

use App\Audit;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Model\AuditAllocation;
use Illuminate\Support\Facades\DB;

class GovernanceDashboard extends Controller
{
    public function gDashboard(Request $request)
    {
$month = now()->subMonths(1)->format('m');

$clientId = $request->client_id;
$startDate = $request->start_date;
$endDate = $request->end_date;

$query = AuditAllocation::query();

if(!empty($clientId) && $clientId != 'all'){
    $query->where('client_id', $clientId);
}

if(!empty($startDate) && !empty($endDate)){
    $query->whereBetween('created_at', [
          Carbon::parse($startDate)->startOfDay(),
        Carbon::parse($endDate)->endOfDay(),
    ]);
}else{
    $query->whereMonth('created_at', now()->month)
          ->whereYear('created_at', now()->year);
    }
    $totalAllocation = $query->count();
// dd($startDate, $endDate); 

        $auditScored = AuditAllocation::whereMonth('created_at', $month)
            ->whereYear('created_at', now()->year)
            ->count();

        $achivedScore = Audit::where('status', 1)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', now()->year)
            ->count();

        $achievedPercent = $auditScored > 0 ? round(($achivedScore / $auditScored) * 100, 2) : 0;

        $actionPlan = DB::table('audit_closure_artifacts')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', now()->year)
            ->distinct()
            ->count('audit_id');

        $overall_score = DB::table('audits')->where('status', 1)->whereMonth('created_at', $month)
            ->whereYear('created_at', now()->year)->sum('overall_score');


        //  Table data  

        // fetching client name 
        $clients = DB::table('clients')
            ->select('client_id', 'client_name')
            ->get();

        // getting the total allocation based on the client id    
        $totalAllocation = DB::table('audit_allocation')
            ->join('clients', 'clients.client_id', '=', 'audit_allocation.client_id')
            ->whereMonth('audit_allocation.created_at', $month)
            ->whereYear('audit_allocation.created_at', now()->year)
            ->select(
                'audit_allocation.client_id',
                DB::raw('COUNT(*) as totalAllocation')
            )
            ->groupBy('audit_allocation.client_id')
            ->pluck('totalAllocation', 'audit_allocation.client_id');


        // getting the total achievement or done by auditor where status = 1
        $totalAchievement = DB::table('audits')
            ->join('clients', 'clients.client_id', '=', 'audits.client_id')
            ->select(
                'audits.client_id',
                DB::raw('COUNT(*) as totalAchievement')
            )->whereMonth('audits.created_at', $month)
            ->whereYear('audits.created_at', now()->year)
            ->where('audits.status', 1)
            ->groupBy('audits.client_id')
            ->pluck('totalAchievement', 'audits.client_id');


        // calculate the percentage of achieveMentPercentage
        $achieveMentPercentage = [];
        foreach ($clients as $client) {
            $allocation = $totalAllocation[$client->client_id] ?? 0;
            $achievement = $totalAchievement[$client->client_id] ?? 0;
            $achieveMentPercentage[$client->client_id] = $allocation > 0 ? round(($achievement / $allocation) * 100, 2) : 0;
        }

        //calculate the overall score where status = 1 in audit table 
        $overallScore = DB::table('audits')
            ->select(
                'client_id',
                DB::raw('SUM(overall_score) as overallScore')
            )->whereMonth('created_at', $month)
            ->whereYear('created_at', now()->year)
            ->where('status', 1)
            ->groupBy('client_id')
            ->pluck('overallScore', 'client_id');


        // calculate the overall score Percentage

        $overallScorePercentage = DB::table('audits')
            ->select(
                'client_id',
                DB::raw('ROUND(AVG(score_percentage), 2) as scorePercentage')
            )
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', now()->year)
            ->where('status', 1)
            ->groupBy('client_id')
            ->pluck('scorePercentage', 'client_id');


 $actionPlanning = DB::table('audit_closure_artifacts')
            ->join('audits', 'audit_closure_artifacts.audit_id', '=', 'audits.id')
            ->select(
                'audits.client_id',
                DB::raw('COUNT(DISTINCT audit_closure_artifacts.audit_id) as actionPlanningCount')
            )
            ->whereMonth('audit_closure_artifacts.created_at', $month)
            ->whereYear('audit_closure_artifacts.created_at', now()->year)
            ->groupBy('audits.client_id')
            ->pluck('actionPlanningCount', 'audits.client_id');

        return view('governanceDashboard', compact('auditScored', 'achivedScore', 'achievedPercent', 'actionPlan', 'clients', 'overall_score', 'totalAllocation', 'totalAchievement', 'achieveMentPercentage', 'overallScore', 'overallScorePercentage', 'actionPlanning'));
    }
}
