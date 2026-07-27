<?php

namespace App\Http\Controllers;

use App\Audit;
use App\User;
use App\AuditCycle;
use App\Models\QmSheet;
use App\Models\Agency;
use App\Models\City;
use App\Models\State;
use Auth;
use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NewDashboardController extends Controller
{
    public function getNewClientDashboard(Request $request)
    {
        $user = Auth::user();
        $clientId = $user->client_id;

        // Get audit cycles
        $auditCycle = AuditCycle::where('client_id', $clientId)
            ->orderBy('id', 'desc')
            ->limit(6)
            ->get()
            ->values();

        $audit_type = ($request->audit_type && $request->audit_type != 'all') ? $request->audit_type : 'all';

        // Determine current and previous cycle IDs (similar to getNewClientDashboard)
        if ($request->audit_cycle_id && $request->audit_cycle_id != 0) {
            $currentCycleId = $request->audit_cycle_id;
            $auditCyclePre = AuditCycle::where('client_id', $clientId)
                ->where('id', '<', $currentCycleId)
                ->orderBy('id', 'desc')
                ->first();
            $previousCycleId = $auditCyclePre ? $auditCyclePre->id : null;
        } else {
            $currentIndex = $auditCycle->search(function ($cycle) {
                return $cycle->status == 1;
            });
            if ($auditCycle->count() <= 1) {
                $previousIndex = false;
            } else {
                $previousIndex = ($currentIndex !== false && $currentIndex + 1 < $auditCycle->count()) ? $currentIndex + 1 : false;
            }
            $currentCycleId = ($currentIndex !== false) ? $auditCycle[$currentIndex]->id : null;
            $previousCycleId = ($previousIndex !== false) ? $auditCycle[$previousIndex]->id : null;
            if ($currentCycleId && $previousCycleId && $currentCycleId == $previousCycleId) {
                $previousCycleId = null;
            }
        }

        // Initialize auditData
        $auditData = [];

        // Get cycle data (counts and scores)
        if ($currentCycleId || $previousCycleId) {
            $cycleData = $this->getCycleDataForTwoCycles($currentCycleId, $previousCycleId, $clientId, $audit_type, null, null);
            $auditData['current_cycle_count'] = $cycleData['current']['audit_count'] ?? 0;
            $auditData['current_cycle_score'] = $cycleData['current']['audit_score'] ?? 0;
            $auditData['previous_cycle_count'] = $cycleData['previous']['audit_count'] ?? 0;
            $auditData['previous_cycle_score'] = $cycleData['previous']['audit_score'] ?? 0;
        } else {
            $auditData['current_cycle_count'] = 0;
            $auditData['current_cycle_score'] = 0;
            $auditData['previous_cycle_count'] = 0;
            $auditData['previous_cycle_score'] = 0;
        }

        // Special handling for client 74
        if ($clientId == 74) {
            $auditData['tabs'] = [
                'physical' => $this->getCycleDataForTwoCycles($currentCycleId, $previousCycleId, $clientId, $audit_type, 0),
                'virtual' => $this->getCycleDataForTwoCycles($currentCycleId, $previousCycleId, $clientId, $audit_type, 1),
            ];
        }

        // Get allocation data
        $auditData['allocation_data'] = $this->getAllocationData($clientId, $currentCycleId, $previousCycleId, $audit_type,  null);

        $auditData['kpiStats'] = $this->getKpiAuditStats(
            $clientId,
            $currentCycleId,
            $previousCycleId,
            $audit_type,
            null
        );

        // Get action planning data
        $auditData['getActionPlanningData'] = $this->getActionPlanningData($clientId, $currentCycleId, $previousCycleId, $audit_type);

        // Get audit type distribution (optional, maybe for charts)
        $auditData['audit_type_distribution'] = $this->getAuditTypeDistribution($clientId, $currentCycleId, $audit_type);

        // --- Additional dynamic data for static sections ---

        // Lifecycle data (for current cycle)
        // $lifecycle = $this->getLifecycleStats($clientId, $currentCycleId, $audit_type);

        // Recent submissions (last 5 audits)
        // $recentSubmissions = $this->getRecentSubmissions($clientId, $audit_type);

        // City compliance data (for trend tab)
        $cityCompliance = $this->getCityCompliance($clientId, $audit_type, $currentCycleId);

        // Top performers (for trend tab)
        $topPerformers = $this->getTopPerformers($clientId, $audit_type, $currentCycleId);

        // Action issues (for action planning tab)

        $latestAudits = DB::table('audits as a')
            ->join('agencies as ag', 'a.agency_id', '=', 'ag.id')
            ->where('a.client_id', auth()->user()->client_id)
            ->where('a.status', 1)
            ->orderBy('a.audit_date_by_aud', 'desc')
            ->limit(5)
            ->select(
                'a.*',
                'ag.agency_id as agency_code',
                'ag.name'
            )
            ->get();

        $auditData['topZone'] = $this->getTopPerformingZone(
            $clientId,
            $currentCycleId,
            $audit_type
        );

        $auditData['productDistribution'] = $this->getProductWiseDistribution(
            $clientId,
            $currentCycleId,
            $audit_type
        );

        $auditData['zonePerformance'] = $this->getZoneWisePerformance(
            $clientId,
            $currentCycleId,
            $audit_type
        );
        // dd($auditData['zonePerformance']);
        $auditData['cityCompliance'] = $this->getCityCompliancedata(
            $clientId,
            $currentCycleId,
            $audit_type
        );

        $auditData['topPerformers'] = $this->getTopPerformersData(
            $clientId,
            $currentCycleId,
            $audit_type,
        );

        $auditData['bottomPerformers'] = $this->getBottomPerformersData(
            $clientId,
            $currentCycleId,
            $audit_type
        );

        $auditData['actionKpis'] = $this->getActionKpis(
            $clientId,
            $currentCycleId,
            $audit_type
        );

        $auditData['actionIssues'] = $this->getActionIssues(
            $clientId,
            $currentCycleId,
            $audit_type,
            'open'
        );

        $auditData['resolutionTrend'] = $this->getResolutionTrend(
            $clientId,
            $currentCycleId,
            $audit_type
        );
        // dd($auditData['resolutionTrend']);
        $auditData['statePerformance'] = $this->getStatePerformance(
            $clientId,
            $currentCycleId,
            $audit_type
        );

        $month = now()->month;
        $year  = now()->year;

        $start = Carbon::create($year, $month)->startOfMonth();
        $end   = Carbon::create($year, $month)->endOfMonth();

        // Assigned
        $assignments = DB::table('auditor_assigns')
            ->whereBetween('audit_date', [$start, $end])
            ->where('client_id', auth()->user()->client_id)
            ->select(
                DB::raw('DATE(audit_date) as date'),
                'audit_cycle_id',
                'agency_id'
            )
            ->get()
            ->groupBy('date');

        // Submitted
        $submitted = DB::table('audits')
            ->whereBetween('audit_date_by_aud', [$start, $end])
            ->where('client_id', auth()->user()->client_id)
            ->where('status', 1)
            ->select(
                DB::raw('DATE(audit_date_by_aud) as date'),
                'audit_cycle_id',
                'agency_id'
            )
            ->get()
            ->groupBy('date');

        $stateCompliance = DB::table('audits as a')
            ->join('agencies as ag', 'ag.id', '=', 'a.agency_id')
            ->join('states as s', 's.id', '=', 'ag.state')
            ->selectRaw('
        s.name as state,
        AVG(a.overall_score)/100 as compliance_score
    ')
            ->groupBy('s.id', 's.name')
            ->get();
        // dd($stateCompliance);
        $auditData['stateCompliance'] = $stateCompliance;


        // dd($latestAudits);

        return view('updatednewclientdashboard', compact(
            'auditData',
            'currentCycleId',
            'audit_type',
            'auditCycle',
            // 'actionIssues',
            // 'recentSubmissions',
            'cityCompliance',
            'topPerformers',
            // 'actionIssues',
            'latestAudits',
            'assignments',
            'submitted',
            'month',
            'year',

        ));
    }

    public function newdashboard(Request $request)
    {
        $user = Auth::user();
        $clientId = $user->client_id;

        // Get audit cycles
        $auditCycle = AuditCycle::where('client_id', $clientId)
            ->orderBy('id', 'desc')
            ->limit(6)
            ->get()
            ->values();

        $auditpartner = User::where('client_id', $clientId)
            ->role('Admin')
            ->orderBy('id', 'desc')
            ->limit(6)
            ->get()
            ->values();

        $audit_type = ($request->audit_type && $request->audit_type != 'all') ? $request->audit_type : 'all';

        // Determine current and previous cycle IDs (similar to getNewClientDashboard)
        if ($request->audit_cycle_id && $request->audit_cycle_id != 0) {
            $currentCycleId = $request->audit_cycle_id;
            $auditCyclePre = AuditCycle::where('client_id', $clientId)
                ->where('id', '<', $currentCycleId)
                ->orderBy('id', 'desc')
                ->first();
            $previousCycleId = $auditCyclePre ? $auditCyclePre->id : null;
        } else {
            $currentIndex = $auditCycle->search(function ($cycle) {
                return $cycle->status == 1;
            });
            if ($auditCycle->count() <= 1) {
                $previousIndex = false;
            } else {
                $previousIndex = ($currentIndex !== false && $currentIndex + 1 < $auditCycle->count()) ? $currentIndex + 1 : false;
            }
            $currentCycleId = ($currentIndex !== false) ? $auditCycle[$currentIndex]->id : null;
            $previousCycleId = ($previousIndex !== false) ? $auditCycle[$previousIndex]->id : null;
            if ($currentCycleId && $previousCycleId && $currentCycleId == $previousCycleId) {
                $previousCycleId = null;
            }
        }
        $audit_partner = $request->filled('audit_partner')
            ? $request->audit_partner
            : null;
        // Initialize auditData
        $auditData = [];

        // Get cycle data (counts and scores)
        if ($currentCycleId || $previousCycleId) {
            $cycleData = $this->getCycleDataForTwoCycles($currentCycleId, $previousCycleId, $clientId, $audit_type, null, $audit_partner);
            $auditData['current_cycle_count'] = $cycleData['current']['audit_count'] ?? 0;
            $auditData['current_cycle_score'] = $cycleData['current']['audit_score'] ?? 0;
            $auditData['previous_cycle_count'] = $cycleData['previous']['audit_count'] ?? 0;
            $auditData['previous_cycle_score'] = $cycleData['previous']['audit_score'] ?? 0;
        } else {
            $auditData['current_cycle_count'] = 0;
            $auditData['current_cycle_score'] = 0;
            $auditData['previous_cycle_count'] = 0;
            $auditData['previous_cycle_score'] = 0;
        }

        // Special handling for client 74
        if ($clientId == 74) {
            $auditData['tabs'] = [
                'physical' => $this->getCycleDataForTwoCycles($currentCycleId, $previousCycleId, $clientId, $audit_type, 0, $audit_partner),
                'virtual' => $this->getCycleDataForTwoCycles($currentCycleId, $previousCycleId, $clientId, $audit_type, 1, $audit_partner),
            ];
        }

        // Get allocation data
        $auditData['allocation_data'] = $this->getAllocationData($clientId, $currentCycleId, $previousCycleId, $audit_type, $audit_partner);

        $auditData['kpiStats'] = $this->getKpiAuditStats(
            $clientId,
            $currentCycleId,
            $previousCycleId,
            $audit_type,
            $audit_partner
        );

        // Get action planning data
        $auditData['getActionPlanningData'] = $this->getActionPlanningData($clientId, $currentCycleId, $previousCycleId, $audit_type);

        // Get audit type distribution (optional, maybe for charts)
        $auditData['audit_type_distribution'] = $this->getAuditTypeDistribution($clientId, $currentCycleId, $audit_type);

        // --- Additional dynamic data for static sections ---

        // Lifecycle data (for current cycle)
        // $lifecycle = $this->getLifecycleStats($clientId, $currentCycleId, $audit_type);

        // Recent submissions (last 5 audits)
        // $recentSubmissions = $this->getRecentSubmissions($clientId, $audit_type);

        // City compliance data (for trend tab)
        $cityCompliance = $this->getCityCompliance($clientId, $audit_type, $currentCycleId);

        // Top performers (for trend tab)
        $topPerformers = $this->getTopPerformers($clientId, $audit_type, $currentCycleId);

        // Action issues (for action planning tab)

        $latestAudits = DB::table('audits as a')
            ->join('agencies as ag', 'a.agency_id', '=', 'ag.id')
            ->where('a.client_id', auth()->user()->client_id)
            ->where('a.status', 1)
            ->orderBy('a.audit_date_by_aud', 'desc')
            ->limit(5)
            ->select(
                'a.*',
                'ag.agency_id as agency_code',
                'ag.name'
            )
            ->get();

        $auditData['topZone'] = $this->getTopPerformingZone(
            $clientId,
            $currentCycleId,
            $audit_type
        );
        if (auth()->user()->client_id == 298 || auth()->user()->client_id == 288 ) {

            $auditData['productDistribution'] = $this->getCategoryWiseDistribution(
                $clientId,
                $currentCycleId,
                $audit_type,
                $audit_partner
            );
        } else {

            $auditData['productDistribution'] = $this->getProductWiseDistribution(
                $clientId,
                $currentCycleId,
                $audit_type,
                $audit_partner
            );
        }

        $auditData['zonePerformance'] = $this->getZoneWisePerformance(
            $clientId,
            $currentCycleId,
            $audit_type,
            $audit_partner
        );
        // dd($auditData['zonePerformance']);
        $auditData['cityCompliance'] = $this->getCityCompliancedata(
            $clientId,
            $currentCycleId,
            $audit_type
        );

        $auditData['topPerformers'] = $this->getTopPerformersData(
            $clientId,
            $currentCycleId,
            $audit_type,
            $audit_partner
        );

        $auditData['bottomPerformers'] = $this->getBottomPerformersData(
            $clientId,
            $currentCycleId,
            $audit_type,
            $audit_partner
        );

        $auditData['topAgencies'] = $this->getTopAgencyData(
            $clientId,
            $currentCycleId,
            $audit_type,
            $audit_partner
        );

        $auditData['bottomAgencies'] = $this->getBottomAgencyData(
            $clientId,
            $currentCycleId,
            $audit_type,
            $audit_partner
        );
        // dd($auditData['topAgency']);

        $auditData['actionKpis'] = $this->getActionKpis(
            $clientId,
            $currentCycleId,
            $audit_type,
            $audit_partner
        );

        $auditData['actionIssues'] = $this->getActionIssues(
            $clientId,
            $currentCycleId,
            $audit_type,
            'open'
        );

        $auditData['resolutionTrend'] = $this->getResolutionTrend(
            $clientId,
            $currentCycleId,
            $audit_type,
            $audit_partner
        );
        // dd($auditData['resolutionTrend']);
        $auditData['statePerformance'] = $this->getStatePerformance(
            $clientId,
            $currentCycleId,
            $audit_type
        );

        $auditData['getAuditAgencyWiseData'] = $this->getAuditAgencyWiseData(Auth::user()->client_id, $currentCycleId, $previousCycleId, $audit_type);

        $month = now()->month;
        $year  = now()->year;

        $start = Carbon::create($year, $month)->startOfMonth();
        $end   = Carbon::create($year, $month)->endOfMonth();

        // Assigned
        $assignments = DB::table('auditor_assigns')
            ->whereBetween('audit_date', [$start, $end])
            ->where('client_id', auth()->user()->client_id)
            ->select(
                DB::raw('DATE(audit_date) as date'),
                'audit_cycle_id',
                'agency_id'
            )
            ->get()
            ->groupBy('date');

        // Submitted
        $submitted = DB::table('audits')
            ->whereBetween('audit_date_by_aud', [$start, $end])
            ->where('client_id', auth()->user()->client_id)
            ->where('status', 1)
            ->select(
                DB::raw('DATE(audit_date_by_aud) as date'),
                'audit_cycle_id',
                'agency_id'
            )
            ->get()
            ->groupBy('date');

        $stateCompliance = DB::table('audits as a')
            ->join('agencies as ag', 'ag.id', '=', 'a.agency_id')
            ->join('states as s', 's.id', '=', 'ag.state')
            ->selectRaw('
        s.name as state,
        AVG(a.overall_score)/100 as compliance_score
        ')
            ->groupBy('s.id', 's.name')
            ->get();
        // dd($stateCompliance);
        $auditData['stateCompliance'] = $stateCompliance;


        // dd($latestAudits);

        return view('newdashboard', compact(
            'auditData',
            'currentCycleId',
            'audit_type',
            'auditCycle',
            'auditpartner',
            // 'actionIssues',
            // 'recentSubmissions',
            'cityCompliance',
            'topPerformers',
            // 'actionIssues',
            'latestAudits',
            'assignments',
            'submitted',
            'month',
            'year',

        ));
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
    private function getResolutionTrend($clientId, $currentCycleId = null, $audit_type = 'all', $audit_partner = null)
    {
        // Last 5 cycles
        $cycles = DB::table('audit_cycles')
            ->where('client_id', $clientId)
            ->orderByDesc('id')
            ->limit(5)
            ->pluck('id')
            ->toArray();

        $query = DB::table('audit_cycles as ac')
            ->join('audits as a', function ($join) {
                $join->on('a.audit_cycle_id', '=', 'ac.id');
            })

            ->leftJoin('audit_results as ar', 'ar.audit_id', '=', 'a.id')

            ->leftJoin('audit_closure_artifacts as aca', function ($join) {
                $join->on('aca.audit_id', '=', 'ar.audit_id')
                    ->on('aca.sub_parameter_id', '=', 'ar.sub_parameter_id');
            })

            ->where('a.client_id', $clientId)
            ->where('a.status', 1)
            ->whereIn('ac.id', $cycles);

        if (!empty($audit_partner)) {
            $query->where('a.audit_agency_id', $audit_partner);
        }

        if ($audit_type !== 'all' && !empty($audit_type)) {
            $query->where('a.audit_type', $audit_type);
        }

        $trend = $query->selectRaw("
        ac.id as cycle_id,
        ac.name,

        COUNT(DISTINCT CASE
            WHEN LOWER(TRIM(ar.option_selected)) = 'unsatisfactory'
             AND aca.id IS NULL
            THEN CONCAT(ar.audit_id,'-',ar.sub_parameter_id)
        END) as open,

        COUNT(DISTINCT CASE
            WHEN LOWER(TRIM(ar.option_selected)) = 'unsatisfactory'
             AND aca.approval_status = 'Approved'
            THEN CONCAT(ar.audit_id,'-',ar.sub_parameter_id)
        END) as approved,

        COUNT(DISTINCT CASE
            WHEN LOWER(TRIM(ar.option_selected)) = 'unsatisfactory'
             AND aca.approval_status = 'Rejected'
            THEN CONCAT(ar.audit_id,'-',ar.sub_parameter_id)
        END) as rejected
    ")
            ->groupBy('ac.id', 'ac.name')
            ->orderBy('ac.id')
            ->get();

        $months = [];
        $open = [];
        $approved = [];
        $rejected = [];

        foreach ($trend as $row) {
            $months[] = $row->name;
            $open[] = (int) $row->open;
            $approved[] = (int) $row->approved;
            $rejected[] = (int) $row->rejected;
        }

        return [
            'months'   => $months,
            'open'     => $open,
            'approved' => $approved,
            'rejected' => $rejected,
        ];
    }
    private function getStatePerformance($clientId, $cycleId = null, $audit_type = 'all')
    {
        $query = DB::table('audits as a')
            ->join('agencies as ag', 'a.agency_id', '=', 'ag.id')
            ->join('cities as c', 'ag.city_id', '=', 'c.id')
            ->join('states as s', 'c.state_id', '=', 's.id')
            ->where('a.client_id', $clientId)
            ->where('a.status', 1);

        if (!empty($cycleId)) {
            $query->where('a.audit_cycle_id', $cycleId);
        }

        // if ($audit_type != 'all') {

        //     if ($audit_type == "agency_repo") $audit_type = 'agencyrepo';
        //     if ($audit_type == "branch_repo") $audit_type = 'branchrepo';
        //     if ($audit_type == "yard_repo") $audit_type = 'yardrepo';

        //     $query->where('a.audit_type', $audit_type);
        // }

        return $query->select(
            's.name as state_name',
            DB::raw('ROUND(AVG(a.overall_score),1) as avg_score')
        )
            ->groupBy('s.name')
            ->orderByDesc('avg_score')
            ->limit(6)
            ->get();
    }
    private function getActionKpis($clientId, $currentCycleId = null, $audit_type = 'all', $audit_partner = null)
    {
        $query = DB::table('audits as a')
            ->join('audit_results as ar', 'ar.audit_id', '=', 'a.id')
            ->leftJoin('audit_closure_artifacts as aca', 'aca.audit_id', '=', 'a.id')
            ->where('a.client_id', $clientId)
            ->where('ar.option_selected', 'unsatisfactory');

        // ✅ Filter by current cycle (MANDATORY)
        if (!empty($currentCycleId)) {
            $query->where('a.audit_cycle_id', $currentCycleId);
        }

        // ✅ Audit type filter
        if ($audit_type != 'all') {

            if ($audit_type == "agency_repo") $audit_type = 'agencyrepo';
            if ($audit_type == "branch_repo") $audit_type = 'branchrepo';
            if ($audit_type == "yard_repo") $audit_type = 'yardrepo';

            $query->where('a.audit_type', $audit_type);
        }
        if (!empty($audit_partner)) {
            $query->where('a.audit_agency_id', $audit_partner);
        }

        // 🔵 TOTAL (Current Cycle Only)
        $total = (clone $query)
            // ->distinct('ar.id')
            ->count('ar.id');

        // 🟠 OPEN
        $total = (clone $query)
            // ->distinct('a.id')
            ->count('a.id');

        $open = (clone $query)
            ->where(function ($q) {
                $q->whereNull('aca.approval_status')
                    ->orWhere('aca.approval_status', 'Pending');
            })
            // ->distinct('a.id')
            ->count('a.id');

        $approved = (clone $query)
            ->where('aca.approval_status', 'Approved')
            // ->distinct('a.id')
            ->count('a.id');

        $rejected = (clone $query)
            ->where('aca.approval_status', 'Rejected')
            // ->distinct('a.id')
            ->count('a.id');

        return [
            'total' => $total,
            'open' => $open,
            'approved' => $approved,
            'rejected' => $rejected
        ];
    }

    private function getActionTrend($clientId, $currentCycleId = null, $audit_type = 'all')
    {
        $query = DB::table('audits as a')
            ->join('audit_results as ar', 'ar.audit_id', '=', 'a.id')
            ->leftJoin('audit_closure_artifacts as aca', 'aca.audit_id', '=', 'a.id')
            ->where('a.client_id', $clientId)
            ->where('ar.option_selected', 'unsatisfactory');

        // Cycle filter
        if (!empty($currentCycleId)) {
            $query->where('a.audit_cycle_id', $currentCycleId);
        }

        // Audit type filter
        if ($audit_type != 'all') {

            if ($audit_type == "agency_repo") $audit_type = 'agencyrepo';
            if ($audit_type == "branch_repo") $audit_type = 'branchrepo';
            if ($audit_type == "yard_repo") $audit_type = 'yardrepo';

            $query->where('a.audit_type', $audit_type);
        }

        // Last 5 months trend
        $trend = (clone $query)
            ->selectRaw("
            DATE_FORMAT(a.created_at,'%b') as month,
            SUM(CASE WHEN aca.approval_status IS NULL OR aca.approval_status='Pending' THEN 1 ELSE 0 END) as open,
            SUM(CASE WHEN aca.approval_status='Approved' THEN 1 ELSE 0 END) as approved,
            SUM(CASE WHEN aca.approval_status='Rejected' THEN 1 ELSE 0 END) as rejected
        ")
            ->where('a.created_at', '>=', now()->subMonths(5))
            ->groupBy(DB::raw("MONTH(a.created_at), DATE_FORMAT(a.created_at,'%b')"))
            ->orderByRaw("MONTH(a.created_at)")
            ->get();

        return $trend;
    }
    private function getTopPerformersData($clientId, $currentCycleId = null, $audit_type = 'all', $audit_partner = null)
    {
        $query = DB::table('audits as a')
            ->join('agencies as ag', 'a.agency_id', '=', 'ag.id')
            ->join('regions as r', 'ag.region_id', '=', 'r.id')
            ->where('a.client_id', $clientId)
            ->where('a.status', 1);

        if ($currentCycleId) {
            $query->where('a.audit_cycle_id', $currentCycleId);
        }

        if ($audit_type != 'all') {

            if ($audit_type == "agency_repo") $audit_type = 'agencyrepo';
            if ($audit_type == "branch_repo") $audit_type = 'branchrepo';
            if ($audit_type == "yard_repo") $audit_type = 'yardrepo';

            $query->where('a.audit_type', $audit_type);
        }

        if (!empty($audit_partner)) {
            $query->where('a.audit_agency_id', $audit_partner);
        }

        return $query->select(
            'ag.location as city',
            'r.name as state',
            DB::raw('ROUND(AVG(a.score_percentage), 1) as avg_score')
        )
            ->groupBy('ag.location', 'r.name')
            ->orderByDesc('avg_score')
            ->limit(5)
            ->get();
    }
    private function getBottomPerformersData($clientId, $currentCycleId = null, $audit_type = 'all', $audit_partner = null)
    {
        $query = DB::table('audits as a')
            ->join('agencies as ag', 'a.agency_id', '=', 'ag.id')
            ->join('regions as r', 'ag.region_id', '=', 'r.id')
            ->where('a.client_id', $clientId)
            ->where('a.status', 1);

        if ($currentCycleId) {
            $query->where('a.audit_cycle_id', $currentCycleId);
        }

        if ($audit_type != 'all') {

            if ($audit_type == "agency_repo") $audit_type = 'agencyrepo';
            if ($audit_type == "branch_repo") $audit_type = 'branchrepo';
            if ($audit_type == "yard_repo") $audit_type = 'yardrepo';

            $query->where('a.audit_type', $audit_type);
        }
        if (!empty($audit_partner)) {
            $query->where('a.audit_agency_id', $audit_partner);
        }

        return $query->select(
            'ag.location as city',
            'r.name as state',
            DB::raw('ROUND(AVG(a.score_percentage), 1) as avg_score')
        )
            ->groupBy('ag.location', 'r.name')
            ->orderBy('avg_score', 'asc')
            ->limit(5)
            ->get();
    }

    private function getTopAgencyData($clientId, $currentCycleId = null, $audit_type = 'all', $audit_partner = null)
    {
        $query = DB::table('audits as a')
            ->join('auditor_assigns as aa', function ($join) use ($currentCycleId) {
                $join->whereRaw("
                CASE aa.type_of_agency
                    WHEN 'agency' THEN aa.agency_id = a.agency_id
                    WHEN 'branch' THEN aa.agency_id = a.branch_id
                    WHEN 'yard' THEN aa.agency_id = a.yard_id
                    WHEN 'branchrepo' THEN aa.agency_id = a.branch_repo_id
                    WHEN 'agencyrepo' THEN aa.agency_id = a.agency_repo_id
                    WHEN 'yardrepo' THEN aa.agency_id = a.yard_repo_id
                END
            ");

                if ($currentCycleId) {
                    $join->where('aa.audit_cycle_id', $currentCycleId);
                }
            })
            ->where('a.client_id', $clientId)
            ->where('a.status', '<=', 2);

        if ($currentCycleId) {
            $query->where('a.audit_cycle_id', $currentCycleId);
        }

        if ($audit_type != 'all') {
            $query->leftJoin('qm_sheets as qs', 'a.qm_sheet_id', '=', 'qs.id')
                ->where('qs.type', $audit_type);
        }
        if (!empty($audit_partner)) {
            $query->where('a.audit_agency_id', $audit_partner);
        }

        return $query->select(
            'aa.final_agency_name as agency_name',
            DB::raw('ROUND(AVG(CASE WHEN a.overall_score > 0 THEN a.overall_score ELSE 0 END), 1) as avg_score')
        )
            ->groupBy('aa.final_agency_name')
            ->orderBy('avg_score', 'desc')
            ->limit(5)
            ->get();
    }

    private function getBottomAgencyData($clientId, $currentCycleId = null, $audit_type = 'all', $audit_partner = null)
    {
        $query = DB::table('audits as a')
            ->join('auditor_assigns as aa', function ($join) use ($currentCycleId) {
                $join->whereRaw("
                CASE aa.type_of_agency
                    WHEN 'agency' THEN aa.agency_id = a.agency_id
                    WHEN 'branch' THEN aa.agency_id = a.branch_id
                    WHEN 'yard' THEN aa.agency_id = a.yard_id
                    WHEN 'branchrepo' THEN aa.agency_id = a.branch_repo_id
                    WHEN 'agencyrepo' THEN aa.agency_id = a.agency_repo_id
                    WHEN 'yardrepo' THEN aa.agency_id = a.yard_repo_id
                END
            ");

                if ($currentCycleId) {
                    $join->where('aa.audit_cycle_id', $currentCycleId);
                }
            })
            ->where('a.client_id', $clientId)
            ->where('a.status', '<=', 2);

        if ($currentCycleId) {
            $query->where('a.audit_cycle_id', $currentCycleId);
        }

        if ($audit_type != 'all') {
            $query->leftJoin('qm_sheets as qs', 'a.qm_sheet_id', '=', 'qs.id')
                ->where('qs.type', $audit_type);
        }
        if (!empty($audit_partner)) {
            $query->where('a.audit_agency_id', $audit_partner);
        }

        return $query->select(
            'aa.final_agency_name as agency_name',
            DB::raw('ROUND(AVG(CASE WHEN a.overall_score > 0 THEN a.overall_score ELSE 0 END), 1) as avg_score')
        )
            ->groupBy('aa.final_agency_name')
            ->orderBy('avg_score', 'asc')
            ->limit(5)
            ->get();
    }
    private function getCityCompliancedata($clientId, $currentCycleId = null, $audit_type = 'all')
    {
        $query = DB::table('audits as a')
            ->join('agencies as ag', 'a.agency_id', '=', 'ag.id')
            ->where('a.client_id', $clientId)
            ->where('a.status', 1);

        if ($currentCycleId) {
            $query->where('a.audit_cycle_id', $currentCycleId);
        }

        if ($audit_type != 'all') {

            if ($audit_type == "agency_repo") $audit_type = 'agencyrepo';
            if ($audit_type == "branch_repo") $audit_type = 'branchrepo';
            if ($audit_type == "yard_repo") $audit_type = 'yardrepo';

            $query->where('a.audit_type', $audit_type);
        }

        return $query->select(
            'ag.location as city',
            DB::raw('COUNT(a.id) as total'),
            DB::raw('SUM(CASE WHEN a.score_percentage >= 100 THEN 1 ELSE 0 END) as compliant'),
            DB::raw('SUM(CASE WHEN a.score_percentage < 100 THEN 1 ELSE 0 END) as non_compliant'),
            DB::raw('ROUND(
                (SUM(CASE WHEN a.score_percentage >= 100 THEN 1 ELSE 0 END) / COUNT(a.id)) * 100, 
                1
            ) as compliance_pct')
        )
            ->groupBy('ag.location')
            ->orderByDesc('compliance_pct')
            ->get();
    }
    public function getAuditByDate(Request $request)
    {
        $date = $request->date;

        // Assigned audits
        $assigned = DB::table('auditor_assigns as aa')
            ->join('agencies as ag', 'aa.agency_id', '=', 'ag.id')
            ->join('products as p', 'aa.product_id', '=', 'p.id')
            ->where('aa.client_id', auth()->user()->client_id)
            ->whereDate('aa.audit_date', $date)
            ->select(
                'aa.final_agency_name',
                'p.name as product_name',
                'ag.location'
            )
            ->get();

        // Submitted audits
        $submitted = DB::table('audits as a')
            ->join('agencies as ag', 'a.agency_id', '=', 'ag.id')
            ->where('a.client_id', auth()->user()->client_id)
            ->where('a.status', 1)
            ->whereDate('a.audit_date_by_aud', $date)
            ->select(
                'a.id',
                'ag.name as agency_name',
                'a.overall_score',
                'a.present_auditor'
            )
            ->get();

        return response()->json([
            'assigned' => $assigned,
            'submitted' => $submitted
        ]);
    }

    // new dashboard 
    private function getCycleDataForTwoCycles($currentCycleId, $previousCycleId, $clientId, $audit_type, $virtualAudit = null, $audit_partner = null)
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
        if (!empty($audit_partner)) {
            $data->where('audits.audit_agency_id', $audit_partner);
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

    private function getAllocationData($clientId, $currentCycleId = null, $previousCycleId = null, $audit_type, $audit_partner)
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

        if (!empty($audit_partner)) {
            $result->where('process_review_agency_id', $audit_partner);
        }

        $result = $result->where('audit_allocation.client_id', $clientId)
            ->first();

        return [
            'currentCycleCount' => $result->currentCycleCount,
            'previousCycleCount' => $result->previousCycleCount
        ];
    }

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
                ELSE NULL END), 1) as avg_score')
        )
            ->where('audits.status', '<=', 2)
            ->groupBy('qm_sheets.type')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->type => [
                    'count' => $item->count,
                    'score' => $item->avg_score ?? 0
                ]];
            })->toArray();
    }

    private function getKpiAuditStats($clientId, $currentCycleId = null, $previousCycleId = null, $audit_type = 'all', $audit_partner)
    {
        $query = DB::table('audits')
            ->selectRaw("
            SUM(CASE WHEN audit_cycle_id = ? THEN 1 ELSE 0 END) as totalCurrent,
            SUM(CASE WHEN audit_cycle_id = ? THEN 1 ELSE 0 END) as totalPrevious,

            SUM(CASE WHEN audit_cycle_id = ? AND score_percentage >= 100 THEN 1 ELSE 0 END) as complianceCurrent,
            SUM(CASE WHEN audit_cycle_id = ? AND score_percentage >= 100 THEN 1 ELSE 0 END) as compliancePrevious,

            SUM(CASE WHEN audit_cycle_id = ? AND score_percentage < 100 THEN 1 ELSE 0 END) as deviationCurrent,
            SUM(CASE WHEN audit_cycle_id = ? AND score_percentage < 100 THEN 1 ELSE 0 END) as deviationPrevious
        ", [
                $currentCycleId,
                $previousCycleId,

                $currentCycleId,
                $previousCycleId,

                $currentCycleId,
                $previousCycleId
            ])
            ->where('client_id', $clientId)
            ->where('status', 1);

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

            $query->where('audit_type', $audit_type);
        }
        if (!empty($audit_partner)) {
            $query->where('audit_agency_id', $audit_partner);
        }

        $result = $query->first();

        return [
            'totalCurrent'       => $result->totalCurrent ?? 0,
            'totalPrevious'      => $result->totalPrevious ?? 0,
            'complianceCurrent'  => $result->complianceCurrent ?? 0,
            'compliancePrevious' => $result->compliancePrevious ?? 0,
            'deviationCurrent'   => $result->deviationCurrent ?? 0,
            'deviationPrevious'  => $result->deviationPrevious ?? 0,
        ];
    }

    private function getTopPerformingZone($clientId, $currentCycleId = null, $audit_type = 'all')
    {
        $query = DB::table('audits as a')
            ->join('agencies as ag', 'a.agency_id', '=', 'ag.id')
            ->join('regions as r', 'ag.region_id', '=', 'r.id')
            ->where('a.client_id', $clientId)
            ->where('a.status', 1);

        if ($currentCycleId) {
            $query->where('a.audit_cycle_id', $currentCycleId);
        }

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

            $query->where('a.audit_type', $audit_type);
        }

        $result = $query->select(
            'r.name as region_name',
            DB::raw('AVG(a.score_percentage) as avg_score'),
            DB::raw('COUNT(a.id) as audit_count')
        )
            ->groupBy('r.id', 'r.name')
            ->orderByDesc('avg_score')
            ->first();

        return $result ? [
            'region_name' => $result->region_name,
            'avg_score'   => round($result->avg_score, 1),
            'audit_count' => $result->audit_count
        ] : [
            'region_name' => 'N/A',
            'avg_score'   => 0,
            'audit_count' => 0
        ];
    }

    private function getProductWiseDistribution($clientId, $currentCycleId = null, $audit_type = 'all', $audit_partner = null)
    {
        $query = DB::table('audits as a')
            ->join('products as p', 'a.product_id', '=', 'p.id')
            ->where('a.client_id', $clientId)
            ->where('a.status', 1);

        if ($currentCycleId) {
            $query->where('a.audit_cycle_id', $currentCycleId);
        }

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

            $query->where('a.audit_type', $audit_type);
        }
        if (!empty($audit_partner)) {
            $query->where('a.audit_agency_id', $audit_partner);
        }

        $result = $query->select(
            'p.name as product_name',
            DB::raw('AVG(a.score_percentage) as avg_score'),
            DB::raw('COUNT(a.id) as audit_count')
        )
            ->groupBy('p.id', 'p.name')
            ->orderBy('p.name')
            ->get();

        return $result;
    }

    private function getCategoryWiseDistribution($clientId, $currentCycleId = null, $audit_type = 'all', $audit_partner = null)
    {
        $query = DB::table('audits as a')
            ->join('audit_category_scores as acs', 'a.id', '=', 'acs.audit_id')
            ->where('a.client_id', $clientId)
            ->where('a.status', 1);

        if ($currentCycleId) {
            $query->where('a.audit_cycle_id', $currentCycleId);
        }

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

            $query->where('a.audit_type', $audit_type);
        }

        if (!empty($audit_partner)) {
            $query->where('a.audit_agency_id', $audit_partner);
        }

        $result = $query->select(
            DB::raw('acs.category_name as product_name'),
            DB::raw('AVG(acs.overall_percentage) as avg_score'),
            DB::raw('COUNT(DISTINCT a.id) as audit_count')
        )
            ->groupBy('acs.category_name')
            ->orderBy('acs.category_name')
            ->get();

        return $result;
    }

    private function getZoneWisePerformance($clientId, $currentCycleId = null, $audit_type = 'all', $audit_partner = null)
    {
        $query = DB::table('audits as a')
            ->join('agencies as ag', 'a.agency_id', '=', 'ag.id')
            ->join('regions as r', 'ag.region_id', '=', 'r.id')
            ->where('a.client_id', $clientId)
            ->where('a.status', 1);

        if ($currentCycleId) {
            $query->where('a.audit_cycle_id', $currentCycleId);
        }

        if ($audit_type != 'all') {

            if ($audit_type == "agency_repo") $audit_type = 'agencyrepo';
            if ($audit_type == "branch_repo") $audit_type = 'branchrepo';
            if ($audit_type == "yard_repo") $audit_type = 'yardrepo';

            $query->where('a.audit_type', $audit_type);
        }
        if (!empty($audit_partner)) {
            $query->where('a.audit_agency_id', $audit_partner);
        }

        return $query->select(
            'r.name as zone_name',
            DB::raw('COUNT(a.id) as audit_count'),
            DB::raw('AVG(a.score_percentage) as avg_score')
        )
            ->groupBy('r.id', 'r.name')
            ->orderBy('r.name')
            ->get();
    }
    private function getLifecycleStats($clientId, $cycleId, $audit_type)
    {
        $query = Audit::where('client_id', $clientId)
            ->where('audit_cycle_id', $cycleId)
            ->where('status', '<=', 2); // exclude drafts? drafts are status 5, we'll handle separately

        if ($audit_type != 'all') {
            $query->whereHas('qmsheet', function ($q) use ($audit_type) {
                $q->where('type', $audit_type);
            });
        }

        $stats = [
            ['QC Approved', 0, 900],
            ['Submitted', 0, 900],
            ['Rejected', 0, 900],
            ['Drafted', 0, 900],
            ['Pending', 0, 900],
        ];

        // Map statuses to your application's status codes
        // Adjust these based on your actual status values
        $stats[0][1] = (clone $query)->where('status', 2)->count();          // QC Approved
        $stats[1][1] = (clone $query)->whereIn('status', [0, 1])->count();    // Submitted
        $stats[2][1] = (clone $query)->where('status', 3)->count();          // Rejected
        $stats[3][1] = (clone $query)->where('status', 5)->count();          // Drafted (Saved)
        $stats[4][1] = (clone $query)->where('status', 4)->count();          // Pending

        $total = (clone $query)->count();
        foreach ($stats as &$stat) {
            $stat[2] = $total ?: 900; // fallback max for percentage bar
        }

        return $stats;
    }

    /**
     * Last 5 audit submissions
     */
    private function getRecentSubmissions($clientId, $audit_type)
    {
        $query = Audit::with('agency')
            ->where('client_id', $clientId)
            ->whereIn('status', [0, 1, 2]) // submitted or approved
            ->orderBy('created_at', 'desc')
            ->limit(5);

        if ($audit_type != 'all') {
            $query->whereHas('qmsheet', function ($q) use ($audit_type) {
                $q->where('type', $audit_type);
            });
        }

        $audits = $query->get();

        $subs = [];
        foreach ($audits as $audit) {
            $name   = 'Audit #' . $audit->id; // or use a custom name field if available
            $agency = $audit->agency ? $audit->agency->name : 'Unknown';
            $subs[] = [$name, $agency];
        }

        return $subs;
    }

    /**
     * City‑wise compliance data for the trend tab
     */
    private function getCityCompliance($clientId, $audit_type, $cycleId)
    {
        $query = Audit::join('agencies', 'audits.agency_id', '=', 'agencies.id')
            ->join('cities', 'agencies.city_id', '=', 'cities.id')
            ->where('audits.client_id', $clientId)
            ->where('audits.status', '<=', 2)
            ->where('audits.audit_cycle_id', $cycleId);

        if ($audit_type != 'all') {
            $query->whereHas('qmsheet', function ($q) use ($audit_type) {
                $q->where('type', $audit_type);
            });
        }

        $cityData = $query->select(
            'cities.name as city_name',
            DB::raw('COUNT(*) as total_audits'),
            DB::raw('AVG(audits.overall_score) as avg_score'),
            DB::raw('SUM(CASE WHEN audits.overall_score >= 60 THEN 1 ELSE 0 END) as compliant'),
            DB::raw('SUM(CASE WHEN audits.overall_score < 60 THEN 1 ELSE 0 END) as non_compliant')
        )
            ->groupBy('cities.name')
            ->orderBy('total_audits', 'desc')
            ->limit(5)
            ->get();

        $cities = [];
        foreach ($cityData as $row) {
            $cities[] = [
                $row->city_name,
                round($row->avg_score),
                $row->compliant,
                $row->non_compliant
            ];
        }

        return $cities;
    }

    /**
     * Top performing cities (by score) for the trend tab
     */
    private function getTopPerformers($clientId, $audit_type, $cycleId)
    {
        $query = Audit::join('agencies', 'audits.agency_id', '=', 'agencies.id')
            ->join('cities', 'agencies.city_id', '=', 'cities.id')
            ->join('states', 'cities.state_id', '=', 'states.id')
            ->where('audits.client_id', $clientId)
            ->where('audits.status', '<=', 2)
            ->where('audits.audit_cycle_id', $cycleId);

        if ($audit_type != 'all') {
            $query->whereHas('qmsheet', function ($q) use ($audit_type) {
                $q->where('type', $audit_type);
            });
        }

        $performers = $query->select(
            'cities.name as city',
            'states.name as state',
            DB::raw('AVG(audits.overall_score) as avg_score')
        )
            ->groupBy('cities.name', 'states.name')
            ->orderBy('avg_score', 'desc')
            ->limit(5)
            ->get();

        $top = [];
        foreach ($performers as $p) {
            $score  = round($p->avg_score, 1) . '%';
            $status = $p->avg_score >= 90 ? 'Excellent' : ($p->avg_score >= 80 ? 'Good' : 'Average');
            $class  = $p->avg_score >= 90 ? 'perf-excellent' : ($p->avg_score >= 80 ? 'perf-good' : 'perf-average');
            $top[] = [$p->city, $p->state, $score, $status, $class];
        }

        return $top;
    }

    /**
     * Action issues (open/approved/rejected) for the action planning tab
     */
    private function getActionIssues($clientId, $currentCycleId = null, $audit_type = 'all', $status = 'open')
    {
        $query = DB::table('audit_results as ar')
            ->join('audits as a', 'ar.audit_id', '=', 'a.id')
            ->join('agencies as ag', 'a.agency_id', '=', 'ag.id')
            ->leftJoin('audit_closure_artifacts as aca', 'aca.audit_id', '=', 'a.id')
            ->where('a.client_id', $clientId)
            ->where('ar.option_selected', 'unsatisfactory');

        if (!empty($currentCycleId)) {
            $query->where('a.audit_cycle_id', $currentCycleId);
        }

        // if ($audit_type != 'all') {

        //     if ($audit_type == "agency_repo") $audit_type = 'agencyrepo';
        //     if ($audit_type == "branch_repo") $audit_type = 'branchrepo';
        //     if ($audit_type == "yard_repo") $audit_type = 'yardrepo';

        //     $query->where('a.audit_type', $audit_type);
        // }

        // 🔥 Status Filter
        if ($status == 'approved') {
            $query->where('aca.approval_status', 'Approved');
        } elseif ($status == 'rejected') {
            $query->where('aca.approval_status', 'Rejected');
        } else {
            $query->where(function ($q) {
                $q->where('aca.approval_status', 'Pending')
                    ->orWhereNull('aca.audit_id');
            });
        }

        return $query->select(
            'a.id as audit_id',
            'ag.agency_id',
            'ag.name as agency_name',
            'ar.remark as issue_description',
            'aca.created_at as due_date',
            DB::raw("COALESCE(aca.approval_status,'Open') as status")
        )
            ->orderByDesc('ar.id')
            ->get();
    }
    public function auditSchedule(Request $request)
    {
        $currentCycleId = $request->input('currentCycleId');
        $auditType = $request->input('audit_type');

        // Fetch audits scheduled in this cycle (adjust field name if different)
        $audits = Audit::where('audit_cycle_id', $currentCycleId)
            ->where('status', '<=', 2) // exclude drafts
            ->orderBy('audit_date')    // use the actual date field
            ->get();

        // Group by date (assuming audit_date field exists)
        $events = $audits->groupBy(function ($audit) {
            return $audit->audit_date ? Carbon::parse($audit->audit_date)->format('Y-m-d') : null;
        })->filter();

        // Render the partial view
        return view('partials.audit_schedule_rows', compact('events'))->render();
    }

    public function getActionIssuesAjax(Request $request)
    {
        $client_id = auth()->user()->client_id;
        $data = $this->getActionIssues(
            $client_id,
            $request->cycle_id,
            'all',
            $request->status
        );
        // dd($data);

        return response()->json($data);
    }
}
