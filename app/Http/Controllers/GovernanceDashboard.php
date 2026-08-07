<?php

namespace App\Http\Controllers;

use App\Audit;
use Illuminate\Http\Request;
use App\Model\AuditAllocation;
use Illuminate\Support\Facades\DB;
use App\Client;
use Carbon\Carbon;

class GovernanceDashboard extends Controller
{
    public function gDashboard(Request $request)
    {
        // $month = now()->subMonths(1)->format('m');

        $clientId = $request->client_id;
        $startDate = $request->start_date;
        $endDate = $request->end_date;



        $dateFilter = function ($query, $column = 'created_at') use ($startDate, $endDate) {
            if (!empty($startDate) && !empty($endDate)) {
                $query->whereBetween($column,  [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay()
                ]);
            } else {
                $query->whereMonth($column, now()->month)
                    ->whereYear($column, now()->year);
            }
        };

        // CARDS DATA

        $auditScored = AuditAllocation::where(function ($q) use ($dateFilter) {
            $dateFilter($q);
        })
            ->count();

        $achivedScore = Audit::where('status', 1)
            ->where(function ($q) use ($dateFilter) {
                $dateFilter($q);
            })
            ->count();

        $achievedPercent = $auditScored > 0
            ? round(($achivedScore / $auditScored) * 100, 2)
            : 0;

        $actionPlanQuery = DB::table('audit_closure_artifacts');
        $dateFilter($actionPlanQuery, 'created_at');
        $actionPlan = $actionPlanQuery
            ->distinct()
            ->count('audit_id');

        $overallScoreQuery = DB::table('audits')
            ->where('status', 1);
        $dateFilter($overallScoreQuery, 'created_at');
        $overall_score = $overallScoreQuery->sum('overall_score');


        // TABLE DATA
        $clientList = Client::select('client_id', 'client_name')->get();
        $clients = Client::query()
            ->when($clientId && $clientId != 'all', function ($query) use ($clientId) {
                $query->where('client_id', $clientId);
            })

            ->withCount([
                'auditAllocations as totalAllocation' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('audit_allocation.created_at', [$startDate, $endDate]);
                },

                'audits as totalAchievement' => function ($query) use ($startDate, $endDate) {
                    $query->where('status', 1)
                        ->whereBetween('audits.created_at', [$startDate, $endDate]);
                },
            ])
            ->withSum([
                'audits as overallScore' => function ($query) use ($startDate, $endDate) {
                    $query->where('status', 1)
                        ->whereMonth('audits.created_at', [$startDate, $endDate]);
                }
            ], 'overall_score')
            ->withAvg([
                'audits as overallScorePercentage' => function ($query) use ($startDate, $endDate) {
                    $query->where('status', 1)
                        ->whereMonth('audits.created_at', [$startDate, $endDate]);
                }
            ], 'score_percentage')
            ->get();

        $actionPlanning = DB::table('audits as a')
            ->join('closure_audits as ca', 'ca.audit_id', '=', 'a.id')
            ->select(
                'a.client_id',
                DB::raw('COUNT(DISTINCT ca.audit_id) as actionPlanning')
            )
            ->whereBetween('ca.created_at', [$startDate, $endDate])
            ->when($clientId && $clientId != 'all', function ($query) use ($clientId) {
                $query->where('a.client_id', $clientId);
            })
            ->groupBy('a.client_id')
            ->pluck('actionPlanning', 'a.client_id');

        // Merge action planning count with clients
        $clients->transform(function ($client) use ($actionPlanning) {
            $client->actionPlanning = $actionPlanning[$client->client_id] ?? 0;
            return $client;
        });
        // dd(
        //     Client::withCount('auditAllocations')->get()->toArray()
        // );
        return view('governanceDashboard', compact(
            'auditScored',
            'achivedScore',
            'achievedPercent',
            'actionPlan',
            'clients',
            'overall_score',
            'clientList'
            // 'actionPlanning'
        ));
    }
}
