<?php

namespace App\Http\Controllers\Legal;

use App\Http\Controllers\Controller;
use App\Advocate;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditAssignController extends Controller
{
    public function index()
    {
        $assignments = DB::table('legal_audit_assignments')->where('client_id', auth()->user()->client_id)
            ->orderBy('audit_date', 'desc')
            ->get();

        return view('legal.audit_assign.index', compact('assignments'));
    }

    public function create()
    {
        $legalCycleId = DB::table('legal_cycles')->where('client_id', auth()->user()->client_id)->where('status', 1)->value('id');
        $advocates = Advocate::where('is_active', 1)
            ->where('client_id', auth()->user()->client_id)
            ->whereNotIn('id', function ($query) use ($legalCycleId) {
                $query->select('advocate_id')
                    ->from('legal_audit_assignments')
                    ->where('legal_cycle', $legalCycleId);
            })
            ->orderBy('name')
            ->pluck('name', 'id');

        $auditors = User::where('client_id', auth()->user()->client_id)->where('audit_agency_id', auth()->user()->id)->where('active_status', 0)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'Quality Auditor');
            })
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('legal.audit_assign.form', compact('advocates', 'auditors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'advocate_id' => 'required|exists:advocates,id',
            'auditor_id'  => 'required|exists:users,id',
            'audit_date'  => 'required|date',
        ]);

        $advocate = Advocate::findOrFail($request->advocate_id);
        $auditor  = User::findOrFail($request->auditor_id);
        $legal_cycle = DB::table('legal_cycles')->where('client_id', auth()->user()->client_id)->where('status', 1)->first();

        DB::table('legal_audit_assignments')->insert([
            'advocate_id'   => $advocate->id,
            'advocate_name' => $advocate->name,
            'auditor_id'    => $auditor->id,
            'auditor_name'  => $auditor->name,
            'audit_date'    => $request->audit_date,
            'created_at'    => now(),
            'updated_at'    => now(),
            'client_id'     => auth()->user()->client_id,
            'legal_cycle'   =>$legal_cycle->id,
        ]);

        return redirect()->route('legal.audit.assign.index')
            ->with('success', 'Legal audit assigned successfully');
    }

    public function edit($id)
    {
        $assignment = DB::table('legal_audit_assignments')->where('id', $id)->first();
        if (!$assignment) abort(404);

        $advocates = Advocate::where('is_active', 1)
            ->orderBy('name')
            ->pluck('name', 'id');

        $auditors = User::where('client_id', auth()->user()->client_id)
            ->where('audit_agency_id', auth()->user()->id)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'Quality Auditor');
            })
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('legal.audit_assign.form', compact('assignment', 'advocates', 'auditors'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'advocate_id' => 'required|exists:advocates,id',
            'auditor_id'  => 'required|exists:users,id',
            'audit_date'  => 'required|date',
        ]);

        $advocate = Advocate::findOrFail($request->advocate_id);
        $auditor  = User::findOrFail($request->auditor_id);

        DB::table('legal_audit_assignments')
            ->where('id', $id)
            ->update([
                'advocate_id'   => $advocate->id,
                'advocate_name' => $advocate->name,
                'auditor_id'    => $auditor->id,
                'auditor_name'  => $auditor->name,
                'audit_date'    => $request->audit_date,
                'updated_at'    => now(),
            ]);

        return redirect()->route('legal.audit.assign.index')
            ->with('success', 'Assignment updated successfully');
    }

    public function assigned__legal_audits()
    {
        $auditorId = auth()->user()->id;

        $audits = DB::table('legal_audit_assignments')
            ->where('auditor_id', $auditorId)
            ->orderBy('audit_date', 'desc')
            ->get();

        return view('legal.audit_assign.assigned_list', compact('audits'));
    }
}
