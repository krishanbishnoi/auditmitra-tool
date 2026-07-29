<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Model\AuditAllocation;

class GovernanceDashboard extends Controller
{
    public function gDashboard(Request $request)
    {
        $activeTab = $request->query('tab', 'clients');

        $auditScored = AuditAllocation::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $achivedScore = AuditAllocation::where('status', 1)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
$achievedPercent = $auditScored > 0 ? round(($achivedScore / $auditScored) *100 ,2) :0;

        return view('governanceDashboard', compact('auditScored', 'activeTab', 'achivedScore', 'achievedPercent'));
    }
}
