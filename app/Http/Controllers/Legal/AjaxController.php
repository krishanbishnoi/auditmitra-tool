<?php

namespace App\Http\Controllers\Legal;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;
use App\Audit;
use App\AuditCycle;
use Illuminate\Support\Arr;

class AjaxController extends Controller
{


    public function audit_schedule(Request $request)
    {
        $currentCycleId = $request->currentCycleId;
        // $audit_type=$request->audit_type;

        $allocatedAudits = DB::table('legal_audit_assignments')
            ->select(
                DB::raw("DATE(audit_date) as created_date,legal_cycle"),
                DB::raw("COUNT(*) as total_allocated")
            );



        $allocatedAudits = $allocatedAudits->where('legal_cycle', $request->currentCycleId)
            ->where('client_id', Auth::user()->client_id)
            ->whereNotNull('audit_date')
            ->groupBy(DB::raw("DATE(audit_date),legal_cycle"))
            ->orderBy('created_date', 'asc')
            ->get();

        $submittedAudits = DB::table('legal_audits')
            ->where('audit_status', 1)
            ->where('legal_audits.legal_cycle_id', $request->currentCycleId)
            ->where('legal_audits.client_id', Auth::user()->client_id)
            ->where('legal_audits.audit_status', 1)
            ->selectRaw('
        DATE(legal_audits.audit_date) as created_date,
        MIN(legal_audits.audit_date) as audit_date,
        COUNT(*) as total_submitted
    ')
            ->groupBy(DB::raw('DATE(legal_audits.audit_date)'))
            ->orderBy('audit_date', 'asc')
            ->get()
            ->map(function ($row) {
                return [
                    'created_date'    => $row->created_date,
                    'audit_date'      => $row->audit_date,
                    'total_submitted' => (int) $row->total_submitted,
                ];
            })
            ->toArray();



        // echo "<pre>"; print_r($submittedAudits); //die;
        // echo "<pre>"; print_r($allocatedAudits); die;
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        $dateArray = [];
        $current = $startDate->copy();
        while ($current->lte($endDate)) {
            $dateArray[] = $current->format('Y-m-d');
            $current->addDay();
        }


        // Prepare lookup arrays for quick access
        $allocatedLookup = [];
        foreach ($allocatedAudits as $a) {
            $allocatedLookup[$a->created_date] = [
                'legal_cycle' => $a->legal_cycle,
                'total_allocated' => $a->total_allocated
            ];
        }

        $submittedLookup = [];
        foreach ($submittedAudits as $s) {
            $submittedLookup[$s['audit_date']] = $s['total_submitted'];
        }

        // Now, for each date in $dateArray, plot data if available, otherwise zero
        $finalArray = [];
        $cycle_name = "";
        foreach ($dateArray as $date) {
            $assigned = isset($allocatedLookup[$date]) ? $allocatedLookup[$date]['total_allocated'] : 0;
            $cycle_name = isset($allocatedLookup[$date]) ? $allocatedLookup[$date]['legal_cycle'] : $cycle_name;
            $submitted = isset($submittedLookup[$date]) ? $submittedLookup[$date] : 0;
            $finalArray[] = [
                'date' => $date,
                'audit_count' => $submitted,
                'assigned_count' => $assigned
            ];
        }
        // echo "<pre>"; print_r($finalArray); die;

        return view('legal.dashboardAjax.audit_schedule', compact('finalArray', 'cycle_name', 'currentCycleId'));
    }

    public function audit_schedule_detail(Request $request)
{
    $currCycle = $request->currentCycleId;
    $date      = $request->date;
    $clientId  = Auth::user()->client_id;

    /*
    |--------------------------------------------------------------------------
    | 1. Allocated Audits (Not yet submitted)
    |--------------------------------------------------------------------------
    */
    $allocatedAudits = DB::table('legal_audit_assignments')
        ->select(
            'legal_audit_assignments.id as allocID',
            'legal_audit_assignments.advocate_name',
            'legal_audit_assignments.audit_date'
        )
        ->where('legal_audit_assignments.legal_cycle', $currCycle)
        ->where('legal_audit_assignments.client_id', $clientId)
        ->whereNotNull('legal_audit_assignments.audit_date')
        ->whereDate('legal_audit_assignments.audit_date', $date)
        ->get();


    /*
    |--------------------------------------------------------------------------
    | 2. Submitted Audits (Joined correctly to assignments)
    |--------------------------------------------------------------------------
    */
   $submittedAudits = DB::table('legal_audits')
    ->join('legal_audit_assignments', function ($join) use ($currCycle) {
        $join->on(
                'legal_audit_assignments.advocate_name',
                '=',
                'legal_audits.advocate_name'
            )
            ->on(
                'legal_audit_assignments.auditor_id',
                '=',
                'legal_audits.auditor_id'
            )
            ->on(
                'legal_audit_assignments.legal_cycle',
                '=',
                'legal_audits.legal_cycle_id'
            )
            ->where('legal_audit_assignments.legal_cycle', $currCycle);
    })
    ->select(
        'legal_audit_assignments.id as allocID',
        'legal_audit_assignments.advocate_name as agencies_name',
        'legal_audits.audit_date'
    )
    ->where('legal_audits.legal_cycle_id', $currCycle)
    ->where('legal_audits.client_id', $clientId)
    ->where('legal_audits.audit_status', 1)
    ->whereDate('legal_audits.audit_date', $date)
    ->orderBy('legal_audit_assignments.id', 'asc')
    ->get()
    ->toArray();



    /*
    |--------------------------------------------------------------------------
    | 3. HTML Rendering
    |--------------------------------------------------------------------------
    */
    $html = '';

    foreach ($allocatedAudits as $alloc) {
        $html .= '<tr>';
        $html .= '<td>' . e($alloc->advocate_name) . '</td>';
        $html .= '<td>Allocated</td>';
        $html .= '</tr>';
    }

    if (!empty($submittedAudits)) {
        $html .= "<tr><td colspan='2' style='font-size:16px;'><b>Submitted Audit List</b></td></tr>";

        foreach ($submittedAudits as $sub) {
            $html .= '<tr>';
            $html .= '<td>' . e($sub->agencies_name) . '</td>';
            $html .= '<td>Submitted</td>';
            $html .= '</tr>';
        }
    }

    return $html;
}
}
