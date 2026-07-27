<?php

namespace App\Http\Controllers\Legal;

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
use DB;
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
use App\Exports\OpenPointersDump;
use App\Exports\OpenPointersWithSummaryExport;
use App\Exports\ScheduleAuditExport;
use App\Exports\LegalAuditsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{


    public function getClientDashboard(Request $request)
    {


        $user = auth()->user();


        $legalCycle = DB::table('legal_cycles')->where('client_id', Auth::user()->client_id)
            ->orderBy('id', 'desc')
            ->limit(6)
            ->get();    // Common dashboard data
        $activelegalcycle = DB::table('legal_cycles')->where('client_id', Auth::user()->client_id)->where('status', 1)

            ->first();

        $selectedCycleId = $request->cycle_id ?? ($activelegalcycle->id ?? null);
        if ($user->hasRole('Client')) {



            $auditCycle = DB::table('legal_cycles')->where('client_id', Auth::user()->client_id)
                ->orderBy('id', 'desc')
                ->limit(6)
                ->get()
                ->values();

            // $audit_type = ($request->audit_type && $request->audit_type != 'all') ? $request->audit_type : 'all';

            if ($request->audit_cycle_id && $request->audit_cycle_id != 0) {
                $currentCycleId = $request->audit_cycle_id;
                $auditCyclePre = DB::table('legal_cycles')->where('client_id', Auth::user()->client_id)
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
                $cycleData = $this->getCycleDataForTwoCycles($currentCycleId, $previousCycleId, Auth::user()->client_id);

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



            // Get overall audit statistics
            $overallStats = $this->getOverallAuditStats(Auth::user()->client_id);

            $auditData = array_merge($auditData, $overallStats);

            $auditData['status_distribution'] = $this->getStatusDistribution(Auth::user()->client_id, $currentCycleId);
            $auditData['allocation_data'] = $this->getAllocationData(Auth::user()->client_id, $currentCycleId, $previousCycleId);

            $auditData['getAuditAgencyWiseData'] = $this->getAuditAgencyWiseData(Auth::user()->client_id, $currentCycleId, $previousCycleId);



            // dd($auditData);
            return view('legal.dashboard_client', compact('auditCycle', 'currentCycleId', 'previousCycleId', 'auditData'));
        }
        // If Quality Auditor
        if ($user->hasRole('Quality Auditor')) {

            $totalAssign  = DB::table('legal_audit_assignments')->where('auditor_id', auth()->user()->id)->where('legal_cycle', $activelegalcycle->id)->count();
            $totalCompletedAudits  = DB::table('legal_audits')->where('auditor_id', auth()->user()->id)->where('audit_status', 1)->count();
            // 'submitted'    = DB::table('legal_audits')->where('client_id', $user->client_id)->where('status', 1)->count(),
            $totalSavedAudits      = DB::table('legal_audits')->where('auditor_id', auth()->user()->id)->where('audit_status', 0)->count();

            return view('legal.dashboard_auditor', compact('totalAssign', 'totalCompletedAudits', 'totalSavedAudits', 'legalCycle'));
        }
        if ($user->hasRole('Admin')) {

            $totalAssign = DB::table('legal_audit_assignments')
                ->where('audit_agency_id', $user->id)
                ->where('legal_cycle', $selectedCycleId)
                ->count();

            $totalCompletedAudits = DB::table('legal_audits')
                ->where('audit_agency_id', $user->id)
                ->where('audit_status', 1)
                ->where('legal_cycle_id', $selectedCycleId)
                ->count();

            $totalSavedAudits = DB::table('legal_audits')
                ->where('audit_agency_id', $user->id)
                ->where('audit_status', 0)
                ->where('legal_cycle_id', $selectedCycleId)
                ->count();

            return view('legal.dashboard_admin', compact(
                'totalAssign',
                'totalCompletedAudits',
                'totalSavedAudits',
                'legalCycle',
                'selectedCycleId'
            ));
        }

        return view('legal.dashbaord');
    }



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
        // return Excel::download(new LegalAuditsExport($startDate, $endDate, $client_id), 'Legal_audits.xlsx');
        return Excel::download(new LegalAuditsExport($startDate, $endDate, $client_id), 'Legal_audits.xlsx');
    }


    private function getCycleDataForTwoCycles($currentCycleId, $previousCycleId, $clientId)
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


        $data = DB::table('legal_audits')
            ->whereIn('legal_audits.legal_cycle_id', $cycleIds)
            ->where('legal_audits.client_id', $clientId)
            ->where('legal_audits.audit_status', 1)
            ->select(
                'legal_audits.legal_cycle_id',
                DB::raw('COUNT(DISTINCT legal_audits.id) as audit_count'),

            );





        $data = $data->groupBy('legal_audits.legal_cycle_id')
            ->get()
            ->keyBy('legal_cycle_id');

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


    private function getOverallAuditStats($clientId)
    {

        $stats = DB::table('legal_audits')->where('legal_audits.client_id', auth()->user()->id)
            ->where('legal_audits.audit_status', '<=', 2)
            ->select(
                DB::raw('COUNT(DISTINCT legal_audits.id) as total_audits'),
                DB::raw('COUNT(DISTINCT CASE WHEN legal_audits.audit_status = 1 THEN legal_audits.id END) as completed_audits'),
                DB::raw('COUNT(DISTINCT CASE WHEN legal_audits.audit_status = 0 THEN legal_audits.id END) as pending_audits'),
            )
            ->first();

        return [
            'total_audits' => $stats->total_audits ?? 0,
            'completed_audits' => $stats->completed_audits ?? 0,
            'pending_audits' => $stats->pending_audits ?? 0,
            'failed_audits' => $stats->failed_audits ?? 0,
        ];
    }

    private function getStatusDistribution($clientId, $currentCycleId = null)
    {

        $query = DB::table('legal_audits')
            ->where('legal_audits.client_id', auth()->user()->client_id);

        // Filter by current cycle if provided
        if ($currentCycleId) {
            $query->where('legal_cycle_id', $currentCycleId);
        }



        return $query->select(
            'audit_status',
            DB::raw('COUNT(*) as count')
        )
            ->groupBy('audit_status')
            ->get()
            ->mapWithKeys(function ($item) {
                $statusLabels = [
                    0 => 'Saved',
                    1 => 'Submitted',

                ];
                return [$statusLabels[$item->audit_status] ?? 'Unknown' => $item->count];
            })->toArray();;
    }


    private function getAllocationData($clientId, $currentCycleId = null, $previousCycleId = null)
    {
        $result = DB::table('legal_audit_assignments')
            ->selectRaw("
                SUM(CASE WHEN legal_cycle = ? THEN 1 ELSE 0 END) as currentCycleCount,
                SUM(CASE WHEN legal_cycle = ? THEN 1 ELSE 0 END) as previousCycleCount
            ", [$currentCycleId, $previousCycleId]);



        $result = $result->where('legal_audit_assignments.client_id', auth()->user()->client_id)
            ->first();

        return [
            'currentCycleCount' => $result->currentCycleCount,
            'previousCycleCount' => $result->previousCycleCount
        ];
    }


    private function getAuditAgencyWiseData($clientId, $currentCycleId = null, $previousCycleId = null)
    {
        // Allocation data
        $allocation = DB::table('legal_audit_assignments')
            ->selectRaw(
                "
            audit_agency_id as agency_id,
            SUM(CASE WHEN legal_cycle = ? THEN 1 ELSE 0 END) as currentCycleCount,
            SUM(CASE WHEN legal_cycle = ? THEN 1 ELSE 0 END) as previousCycleCount
            ",
                [$currentCycleId, $previousCycleId]
            )
            ->where('legal_audit_assignments.client_id', $clientId)
            ->groupBy('legal_audit_assignments.audit_agency_id')
            ->get();

        // Audit count & score
        $auditCountAndScore = DB::table('legal_audits')
            ->selectRaw(
                "
            legal_audits.audit_agency_id as agency_id,
            SUM(CASE WHEN legal_audits.legal_cycle_id = ? THEN 1 ELSE 0 END) as currentCycleCount,
            SUM(CASE WHEN legal_audits.legal_cycle_id = ? THEN 1 ELSE 0 END) as previousCycleCount
            ",
                [$currentCycleId, $previousCycleId]
            )
            ->where('legal_audits.audit_status', 1)
            ->where('legal_audits.client_id', $clientId)
            ->groupBy('legal_audits.audit_agency_id')
            ->get();

        return [
            'allocation' => $allocation,
            'auditCountAndScore' => $auditCountAndScore
        ];
    }
}
