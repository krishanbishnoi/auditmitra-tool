<?php

namespace App\Http\Controllers\Legal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Storage;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use Auth;
use App\QmSheetParameter;
use Illuminate\Support\Facades\Validator;


class AuditController extends Controller
{
    // use DB;

    public function create()
    {
        $auditorId = auth()->user()->id;


        // Advocates assigned to this auditor
        $advocates = DB::table('legal_audit_assignments')
            ->where('auditor_id', $auditorId)
            ->whereNotIn('advocate_id', function ($query) {
                $query->select('advocate_id')
                    ->from('legal_audits');
            })
            ->select('advocate_id', 'advocate_name')
            ->get();


        // Get LEGAL QM Sheet
        $legalQmSheet = DB::table('qm_sheets')->where('client_id', auth()->user()->client_id)
            ->where('type', 'legal')
            ->first();

        if (!$legalQmSheet) {
            abort(404, 'Legal QM Sheet not found');
        }

        // Get parameters of Legal QM Sheet
        $parameters = DB::table('qm_sheet_parameters')
            ->where('qm_sheet_id', $legalQmSheet->id)
            ->select('id', 'parameter', 'qm_sheet_id')
            ->get();

        return view('legal.audit.create', compact(
            'advocates',
            'parameters',
            'legalQmSheet'
        ));
    }

    public function edit($id)
    {
        $auditorId = auth()->user()->id;

        // Get the audit
        $audit = DB::table('legal_audits')->where('id', $id)->first();

        if (!$audit) {
            abort(404, 'Audit not found');
        }

        // Get advocates assigned to this auditor
        $advocates = DB::table('legal_audit_assignments')
            ->where('auditor_id', $auditorId)
            ->select('advocate_id', 'advocate_name')
            ->get();

        // Get LEGAL QM Sheet
        $legalQmSheet = DB::table('qm_sheets')
            ->where('type', 'legal')->where('client_id', auth()->user()->client_id)
            ->first();

        if (!$legalQmSheet) {
            abort(404, 'Legal QM Sheet not found');
        }

        // Get parameters of Legal QM Sheet
        $parameters = DB::table('qm_sheet_parameters')
            ->where('qm_sheet_id', $legalQmSheet->id)
            ->select('id', 'parameter', 'qm_sheet_id')
            ->get();

        // Get already selected parameters for this audit
        $selectedParameters = DB::table('legal_audit_parameters')
            ->where('legal_audit_id', $id)
            ->pluck('parameter_id')
            ->toArray();

        return view('legal.audit.create', compact(
            'audit',
            'advocates',
            'parameters',
            'legalQmSheet',
            'selectedParameters'
        ));
    }

    // AuditController.php
    public function getSubParameters(Request $request)
    {
        $parameterIds = $request->parameter_ids;

        $data = DB::table('qm_sheet_sub_parameters as sp')
            ->join('qm_sheet_parameters as p', 'p.id', '=', 'sp.qm_sheet_parameter_id')
            ->whereIn('sp.qm_sheet_parameter_id', $parameterIds)
            ->select(
                'sp.id',
                'sp.sub_parameter',
                'sp.weight',
                'sp.qm_sheet_parameter_id',
                'p.parameter as parameter_name'
            )
            ->orderBy('p.parameter')
            ->orderBy('sp.id')
            ->get()
            ->groupBy('qm_sheet_parameter_id');

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }



    public function storeAudit(Request $request)
    {
        // ✅ VALIDATION
        $validator = Validator::make($request->all(), [
            'advocate_id'   => 'required',
            'advocate_name' => 'required',
            'audit_date'    => 'required|date',
            'address'       => 'required',
            'state'         => 'required',
            'location'      => 'required',
            'auditor_name'  => 'required',
            'empanelled_from' => 'required|date',
            'auditor_artifact_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // ✅ HANDLE FILE UPLOAD
        $artifactPath = null;

        $legal_cycle = DB::table('legal_cycles')->where('client_id', auth()->user()->client_id)->where('status', 1)->first();

        if ($request->hasFile('auditor_artifact_image')) {
            $artifactPath = $request->file('auditor_artifact_image')
                ->store('auditor_artifacts', 'public');
        }

        // 🔑 UPDATE
        if ($request->filled('audit_id')) {

            $updateData = [
                'advocate_id'   => $request->advocate_id,
                'advocate_name' => $request->advocate_name,
                'audit_date'    => $request->audit_date,
                'address'       => $request->address,
                'state'         => $request->state,
                'location'      => $request->location,
                'legal_manager' => $request->legal_manager,
                'auditor_name'  => $request->auditor_name,
                'empanelled_from' => $request->empanelled_from,
                'updated_at'    => now(),
                'client_id'     => auth()->user()->client_id,
                'audit_agency_id' => auth()->user()->audit_agency_id,
            ];

            // ✅ Only update image if new file uploaded
            if ($request->hasFile('auditor_artifact_image')) {
                $updateData['auditor_artifact_image'] = $artifactPath;
            }

            DB::table('legal_audits')
                ->where('id', $request->audit_id)
                ->update($updateData);

            return response()->json([
                'status'   => true,
                'audit_id' => $request->audit_id,
                'mode'     => 'updated'
            ]);
        }

        // 🆕 INSERT
        $auditId = DB::table('legal_audits')->insertGetId([
            'advocate_id'   => $request->advocate_id,
            'advocate_name' => $request->advocate_name,
            'audit_date'    => $request->audit_date,
            'address'       => $request->address,
            'state'         => $request->state,
            'location'      => $request->location,
            'legal_manager' => $request->legal_manager,
            'auditor_name'  => $request->auditor_name,
            'empanelled_from' => $request->empanelled_from,
            'auditor_artifact_image' => $artifactPath,
            'status'        => 'saved',
            'audit_status'  => 0,
            'auditor_id'    => auth()->user()->id,
            'created_at'    => now(),
            'updated_at'    => now(),
            'client_id'   => auth()->user()->client_id,
            'audit_agency_id'   => auth()->user()->audit_agency_id,
            'legal_cycle_id'    => $legal_cycle->id,
            'legal_cycle'       => $legal_cycle->name,
        ]);

        return response()->json([
            'status'   => true,
            'audit_id' => $auditId,
            'mode'     => 'created'
        ]);
    }



    /**
     * STEP 2
     * Save selected parameters
     */
    public function storeParameters(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'audit_id'   => 'required',
            'parameters' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $auditId = (int) $request->audit_id;

        // ✅ FORCE INTEGER IDS (CRITICAL FIX)
        $incomingParamIds = collect($request->parameters)
            ->pluck('parameter_id')
            ->map(fn($id) => (int) $id)
            ->values()
            ->toArray();

        $existingParamIds = DB::table('legal_audit_parameters')
            ->where('legal_audit_id', $auditId)
            ->pluck('parameter_id')
            ->map(fn($id) => (int) $id)
            ->values()
            ->toArray();

        // ✅ CORRECT DIFF
        $removedParamIds = array_values(array_diff($existingParamIds, $incomingParamIds));

        DB::transaction(function () use ($auditId, $removedParamIds, $request) {

            if (!empty($removedParamIds)) {

                /* ================= DELETE ARTIFACT FILES ================= */
                $artifacts = DB::table('legal_parameter_artifacts')
                    ->where('legal_audit_id', $auditId)
                    ->whereIn('parameter_id', $removedParamIds)
                    ->get();

                // need to correct this
                // foreach ($artifacts as $artifact) {
                //     if (Storage::disk('public')->exists($artifact->artifact_path)) {
                //         Storage::disk('public')->delete($artifact->artifact_path);
                //     }
                // }

                /* ================= DELETE ARTIFACT DB ================= */
                DB::table('legal_parameter_artifacts')
                    ->where('legal_audit_id', $auditId)
                    ->whereIn('parameter_id', $removedParamIds)
                    ->delete();

                /* ================= DELETE RESULTS ================= */
                DB::table('legal_parameter_results')
                    ->where('legal_audit_id', $auditId)
                    ->whereIn('parameter_id', $removedParamIds)
                    ->delete();

                /* ================= DELETE PARAMETER MAP ================= */
                DB::table('legal_audit_parameters')
                    ->where('legal_audit_id', $auditId)
                    ->whereIn('parameter_id', $removedParamIds)
                    ->delete();
            }

            /* ================= INSERT NEW PARAMETERS ================= */
            foreach ($request->parameters as $row) {

                $exists = DB::table('legal_audit_parameters')
                    ->where('legal_audit_id', $auditId)
                    ->where('parameter_id', (int) $row['parameter_id'])
                    ->exists();

                if (!$exists) {
                    DB::table('legal_audit_parameters')->insert([
                        'legal_audit_id' => $auditId,
                        'qm_sheet_id'    => $row['qm_sheet_id'],
                        'parameter_id'   => (int) $row['parameter_id'],
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ]);
                }
            }
        });

        return response()->json([
            'status'  => true,
            'message' => 'Parameters updated successfully'
        ]);
    }


    /**
     * STEP 3
     * Get sub-parameters for selected parameters
     */

    // public function getAuditSubParameters(Request $request)
    // {
    //     $request->validate([
    //         'audit_id' => 'required'
    //     ]);

    //     $data = DB::table('legal_audit_parameters as lap')
    //         ->join('qm_sheet_parameters as p', 'p.id', '=', 'lap.parameter_id')
    //         ->join(
    //             'qm_sheet_sub_parameters as sp',
    //             'sp.qm_sheet_parameter_id',
    //             '=',
    //             'p.id'
    //         )
    //         ->leftJoin('legal_parameter_results as lpr', function ($join) use ($request) {
    //             $join->on('lpr.sub_parameter_id', '=', 'sp.id')
    //                 ->on('lpr.parameter_id', '=', 'p.id')
    //                 ->where('lpr.legal_audit_id', '=', $request->audit_id);
    //         })
    //         ->where('lap.legal_audit_id', $request->audit_id)
    //         ->select(
    //             'p.id as parameter_id',
    //             'p.parameter as parameter_name',
    //             'sp.id as sub_parameter_id',
    //             'sp.sub_parameter',
    //             'lpr.remark'
    //         )
    //         ->orderBy('p.id')
    //         ->orderBy('sp.id')
    //         ->get()
    //         ->groupBy('parameter_id');

    //     return response()->json([
    //         'status' => true,
    //         'data'   => $data
    //     ]);
    // }
    public function getAuditSubParameters(Request $request)
    {
        $request->validate([
            'audit_id' => 'required'
        ]);

        $auditId = $request->audit_id;

        // Get sub-parameters with results
        $data = DB::table('legal_audit_parameters as lap')
            ->join('qm_sheet_parameters as p', 'p.id', '=', 'lap.parameter_id')
            ->join('qm_sheet_sub_parameters as sp', 'sp.qm_sheet_parameter_id', '=', 'p.id')
            ->leftJoin('legal_parameter_results as lpr', function ($join) use ($auditId) {
                $join->on('lpr.sub_parameter_id', '=', 'sp.id')
                    ->on('lpr.parameter_id', '=', 'p.id')
                    ->where('lpr.legal_audit_id', '=', $auditId);
            })
            ->where('lap.legal_audit_id', $auditId)
            ->select(
                'p.id as parameter_id',
                'p.parameter as parameter_name',
                'sp.id as sub_parameter_id',
                'sp.sub_parameter',
                'lpr.remark',
                'lpr.compliance_status' // ADD THIS LINE
            )
            ->orderBy('p.id')
            ->orderBy('sp.id')
            ->get();

        // Group by parameter_id
        $groupedData = $data->groupBy('parameter_id')->toArray();

        // Get all artifacts for this audit
        $artifacts = DB::table('legal_parameter_artifacts')
            ->where('legal_audit_id', $auditId)
            ->select('parameter_id', 'sub_parameter_id', 'artifact_path')
            ->get();

        // Group artifacts by parameter_id and sub_parameter_id
        $artifactGroups = [];
        foreach ($artifacts as $artifact) {
            $artifactGroups[$artifact->parameter_id][$artifact->sub_parameter_id][] = $artifact->artifact_path;
        }

        // Add artifacts to each sub-parameter
        foreach ($groupedData as $parameterId => &$subParameters) {
            foreach ($subParameters as &$subParam) {
                $subParam = (array) $subParam; // Convert to array

                // Get artifacts for this sub-parameter
                $subParam['artifacts'] = $artifactGroups[$parameterId][$subParam['sub_parameter_id']] ?? [];
            }
        }

        return response()->json([
            'status' => true,
            'data'   => $groupedData
        ]);
    }



    public function storeParameterResult(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'audit_id'           => 'required|exists:legal_audits,id',
            'parameter_id'       => 'required|exists:qm_sheet_parameters,id',
            'remarks'            => 'required|array',
            'compliance_status'  => 'required|array',
            'artifact_actions'   => 'array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            foreach ($request->remarks as $subParameterId => $remark) {
                // Validate that compliance status exists for this sub-parameter
                if (!isset($request->compliance_status[$subParameterId])) {
                    throw new \Exception("Compliance status missing for sub-parameter: {$subParameterId}");
                }

                /* ================= SAVE REMARK AND COMPLIANCE STATUS ================= */
                DB::table('legal_parameter_results')->updateOrInsert(
                    [
                        'legal_audit_id'   => $request->audit_id,
                        'parameter_id'     => $request->parameter_id,
                        'sub_parameter_id' => $subParameterId,
                    ],
                    [
                        'remark'             => $remark,
                        'compliance_status'  => $request->compliance_status[$subParameterId],
                        'updated_at'         => now(),
                        'created_at'         => DB::raw('COALESCE(created_at, NOW())'),
                    ]
                );

                /* ================= FILE HANDLING ================= */
                if ($request->hasFile("artifacts.$subParameterId")) {
                    // Get the action for this sub-parameter (default to 'append')
                    $action = $request->artifact_actions[$subParameterId] ?? 'append';

                    if ($action === 'replace') {
                        // 🔥 Delete OLD artifacts from table + storage
                        $oldArtifacts = DB::table('legal_parameter_artifacts')
                            ->where('legal_audit_id', $request->audit_id)
                            ->where('parameter_id', $request->parameter_id)
                            ->where('sub_parameter_id', $subParameterId)
                            ->get();

                        foreach ($oldArtifacts as $old) {
                            if (Storage::disk('public')->exists($old->artifact_path)) {
                                Storage::disk('public')->delete($old->artifact_path);
                            }
                        }

                        // Remove DB records
                        DB::table('legal_parameter_artifacts')
                            ->where('legal_audit_id', $request->audit_id)
                            ->where('parameter_id', $request->parameter_id)
                            ->where('sub_parameter_id', $subParameterId)
                            ->delete();
                    }

                    // 🔥 Insert NEW artifacts
                    foreach ($request->file("artifacts.$subParameterId") as $file) {
                        $path = $file->store(
                            "legal_audit_artifacts/{$request->audit_id}",
                            'public'
                        );

                        DB::table('legal_parameter_artifacts')->insert([
                            'legal_audit_id'   => $request->audit_id,
                            'parameter_id'     => $request->parameter_id,
                            'sub_parameter_id' => $subParameterId,
                            'artifact_path'    => $path,
                            'created_at'       => now(),
                            'updated_at'       => now(),
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Parameter results saved successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving parameter results: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Failed to save results. Please try again.'
            ], 500);
        }
    }

    /**
     * STEP 4
     * Update audit status (save / draft / submit)
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'audit_id' => 'required',
            'status'   => 'required|in:draft,saved,submitted'
        ]);
        $audit_status = ($request->status == 'submitted') ? 1 : 0;
        if (in_array($request->status, ['saved', 'submitted'])) {
            $this->generateAuditPdf($request->audit_id, true);
        }
        DB::table('legal_audits')
            ->where('id', $request->audit_id)
            ->update([
                'status'     => $request->status,
                'audit_status' => $audit_status,
                'updated_at' => now(),
            ]);

        if ($request->status === 'saved') {
            $redirectUrl = route('legal.saved.audit.list');
        } elseif ($request->status === 'submitted') {
            $redirectUrl = route('legal.submitted.audit.list');
        } else {
            $redirectUrl = null;
        }

        return response()->json([
            'status'   => true,
            'message'  => 'Audit status updated successfully',
            'redirect' => $redirectUrl
        ]);
    }


    public function leagl_saved_audit_list()
    {
        $query =  DB::table('legal_audits')->where('audit_status', 0);

        $user = auth()->user();

        if ($user->hasrole('Quality Auditor')) {
            $query->where('auditor_id', $user->id);
        } elseif ($user->hasrole('Admin')) {
            $query->where('audit_agency_id', $user->id);
        } elseif ($user->hasrole('Client')) {
            $query->where('client_id', $user->id);
        }

        $legal_audits = $query->orderBy('id', 'desc')->get();


        return view('legal.audit.saved_legal_audit', compact('legal_audits', 'user'));
    }

    public function leagl_submitted_audit_list()
    {
        $query =  DB::table('legal_audits')->where('audit_status', 1);

        $user = auth()->user();

        if ($user->hasRole('Quality Auditor')) {
            // Example condition for quality auditor only:
            // You may want to restrict audits to only those assigned to this auditor
            $query->where('auditor_id', $user->id); // replace `assigned_to` with your actual field name
        } elseif ($user->hasRole('Admin')) {
            $query->where('audit_agency_id', $user->id);
        } elseif ($user->hasRole('Client')) {
            $query->where('client_id', $user->id);
        }
        $legal_audits = $query->orderBy('id', 'desc')->get();
        // dd($legal_audits);

        return view('legal.audit.submitted_legal_audit', compact('legal_audits'));
    }



    public function viewAuditPage($audit_id)
    {
        $auditId = $audit_id;

        /* ================= AUDIT DETAILS ================= */
        $audit = DB::table('legal_audits')
            ->where('id', $auditId)
            ->select(
                'id',
                'advocate_id',
                'advocate_name',
                'audit_date',
                'address',
                'state',
                'location',
                'legal_manager',
                'auditor_name',
                'empanelled_from',
                'auditor_artifact_image',
                'status',
                'updated_at',
                'recommendations'
            )
            ->first();

        /* ================= PARAMETERS ================= */
        $rows = DB::table('legal_audit_parameters as lap')
            ->join('qm_sheet_parameters as p', 'p.id', '=', 'lap.parameter_id')
            ->join('qm_sheet_sub_parameters as sp', 'sp.qm_sheet_parameter_id', '=', 'p.id')
            ->leftJoin('legal_parameter_results as lpr', function ($join) use ($auditId) {
                $join->on('lpr.sub_parameter_id', '=', 'sp.id')
                    ->on('lpr.parameter_id', '=', 'p.id')
                    ->where('lpr.legal_audit_id', '=', $auditId);
            })
            ->where('lap.legal_audit_id', $auditId)
            ->select(
                'p.id as parameter_id',
                'p.parameter as parameter_name',
                'sp.id as sub_parameter_id',
                'sp.sub_parameter',
                'lpr.remark'
            )
            ->orderBy('p.id')
            ->orderBy('sp.id')
            ->get();

        /* ================= ARTIFACTS ================= */
        $artifacts = DB::table('legal_parameter_artifacts')
            ->where('legal_audit_id', $auditId)
            ->select(
                'parameter_id',
                'sub_parameter_id',
                'artifact_path'
            )
            ->get();

        $artifactMap = [];
        foreach ($artifacts as $artifact) {
            $artifactMap[$artifact->parameter_id][$artifact->sub_parameter_id][] =
                asset('storage/app/public/' . $artifact->artifact_path);
        }

        /* ================= PARAMETER SUMMARIES ================= */
        $parameterSummaries = DB::table('legal_parameter_summaries')
            ->where('legal_audit_id', $auditId)
            ->get()
            ->keyBy('parameter_id');

        /* ================= GROUP DATA ================= */
        $parameters = [];
        foreach ($rows as $row) {
            $pid = $row->parameter_id;

            if (!isset($parameters[$pid])) {
                $parameters[$pid] = [
                    'parameter_name' => $row->parameter_name,
                    'summary' => $parameterSummaries[$pid]->summary ?? null, // Add summary
                    'sub_parameters' => []
                ];
            }

            $parameters[$pid]['sub_parameters'][] = [
                'sub_parameter' => $row->sub_parameter,
                'remark'        => $row->remark,
                'artifacts'     => $artifactMap[$pid][$row->sub_parameter_id] ?? []
            ];
        }

        return view('legal.audit.done', [
            'audit'           => $audit,
            'parameters'      => $parameters,
            'recommendations' => $audit->recommendations ?? ''
        ]);
    }



    private function getAuditData($auditId)
    {

        // Get audit details
        $audit = DB::table('legal_audits')
            ->where('id', $auditId)
            ->select(
                'id',
                'advocate_id',
                'advocate_name',
                'audit_date',
                'address',
                'state',
                'location',
                'legal_manager',
                'auditor_name',
                'empanelled_from',
                'auditor_artifact_image',
                'status',
                'updated_at',
                'recommendations'
            )
            ->first();

        $summaries = DB::table('legal_parameter_summaries')
            ->where('legal_audit_id', $auditId)
            ->pluck('summary', 'parameter_id');

        if (!$audit) {
            return ['audit' => null, 'parameters' => []];
        }

        // Get parameters with results
        $rows = DB::table('legal_audit_parameters as lap')
            ->join('qm_sheet_parameters as p', 'p.id', '=', 'lap.parameter_id')
            ->join('qm_sheet_sub_parameters as sp', 'sp.qm_sheet_parameter_id', '=', 'p.id')
            ->leftJoin('legal_parameter_results as lpr', function ($join) use ($auditId) {
                $join->on('lpr.sub_parameter_id', '=', 'sp.id')
                    ->on('lpr.parameter_id', '=', 'p.id')
                    ->where('lpr.legal_audit_id', '=', $auditId);
            })
            ->where('lap.legal_audit_id', $auditId)
            ->select(
                'p.id as parameter_id',
                'p.parameter as parameter_name',
                'sp.id as sub_parameter_id',
                'sp.sub_parameter',
                'lpr.remark',
                'lpr.compliance_status'
            )
            ->orderBy('p.id')
            ->orderBy('sp.id')
            ->get();

        // Get artifacts
        $artifacts = DB::table('legal_parameter_artifacts')
            ->where('legal_audit_id', $auditId)
            ->select(
                'parameter_id',
                'sub_parameter_id',
                'artifact_path'
            )
            ->get();

        // Create artifact map
        $artifactMap = [];
        foreach ($artifacts as $artifact) {
            // Use public URL for web access
            $artifactUrl = asset('storage/app/public/' . $artifact->artifact_path);
            // Use full path for file_exists check
            $filePath = storage_path('app/public/' . $artifact->artifact_path);

            $artifactMap[$artifact->parameter_id][$artifact->sub_parameter_id][] = [
                'url' => $artifactUrl,
                'path' => $filePath,
                'filename' => basename($artifact->artifact_path)
            ];
        }

        // Group data by parameter
        $parameters = [];
        foreach ($rows as $row) {
            $pid = $row->parameter_id;

            if (!isset($parameters[$pid])) {
                $parameters[$pid] = [
                    'parameter_name' => $row->parameter_name,
                    'summary'        => $summaries[$pid] ?? null,
                    'sub_parameters' => []
                ];
            }

            $parameters[$pid]['sub_parameters'][] = [
                'sub_parameter' => $row->sub_parameter,
                'remark'        => $row->remark,
                'compliance_status' => $row->compliance_status,
                'artifacts'     => $artifactMap[$pid][$row->sub_parameter_id] ?? []
            ];
        }

        return [
            'audit'      => $audit,
            'parameters' => $parameters
        ];
    }





    // for pdf 
    public function generateAuditPdf($audit_id, $saveToDb = false)
    {
        // Get audit data

        $data = $this->getAuditData($audit_id);

        if (!$data['audit']) {
            abort(404, 'Audit not found');
        }

        // Generate PDF
        // $pdf = Pdf::loadView('legal.audit.pdf-template-new', $data);
        $pdf = Pdf::loadView('legal.audit.pdf-template-new', $data);

        // Set PDF options
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'dejavu sans',
            'margin_top' => 25,
            'margin_bottom' => 20,
            'margin_left' => 15,
            'margin_right' => 15
        ]);

        // If saveToDb is true, save to database and storage
        if ($saveToDb) {
            return $this->savePdfToDatabase($pdf, $audit_id, $data);
        }

        // Return PDF for download
        return $pdf->download("legal-audit-report-{$audit_id}.pdf");
    }

    private function savePdfToDatabase($pdf, $audit_id, $data)
    {
        try {
            // Generate new filename
            $filename = "legal-audit-report-{$data['audit']->advocate_name}_{$data['audit']->audit_date}" . ".pdf";
            $path = "legal-audit-reports/{$filename}";

            // Save new PDF


            // Full path stored in DB
            $fullPath = "{$path}";

            // Fetch existing record
            $existing = DB::table('legal_audit_reports')
                ->where('legal_audit_id', $audit_id)
                ->where('client_id', auth()->user()->client_id)
                ->first();

            if ($existing) {

                // 🔥 DELETE OLD PDF FROM STORAGE
                if (!empty($existing->report_path)) {
                    $oldPath = str_replace('storage/', '', $existing->report_path);

                    if (Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }

                // UPDATE DB (created_at remains unchanged)
                DB::table('legal_audit_reports')
                    ->where('id', $existing->id)
                    ->update([
                        'advocate_id'   => $data['audit']->advocate_id,
                        'advocate_name' => $data['audit']->advocate_name,
                        'audit_date'    => $data['audit']->audit_date,
                        'report_path'   => $fullPath,
                        'filename'      => $filename,
                        'generated_at'  => now(),
                        'updated_at'    => now(),
                    ]);
            } else {
                // INSERT new record
                DB::table('legal_audit_reports')->insert([
                    'legal_audit_id' => $audit_id,
                    'client_id'      => auth()->user()->client_id,
                    'advocate_id'    => $data['audit']->advocate_id,
                    'advocate_name'  => $data['audit']->advocate_name,
                    'audit_date'     => $data['audit']->audit_date,
                    'report_path'    => $fullPath,
                    'filename'       => $filename,
                    'generated_at'   => now(),
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }
            Storage::disk('public')->put($path, $pdf->output());
            return [
                'success'      => true,
                'message'      => 'PDF saved successfully',
                'path'         => $fullPath,
                'download_url' => asset($fullPath),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to save PDF: ' . $e->getMessage(),
            ];
        }
    }

    // public function downloadAuditReport($audit_id)
    // {
    //     $report = DB::table('legal_audit_reports')
    //         ->where('legal_audit_id', $audit_id)
    //         ->where('client_id', auth()->user()->client_id)
    //         ->first();

    //     if (!$report || empty($report->report_path)) {
    //         abort(404, 'Audit report not found');
    //     }

    //     // Convert "storage/xxx.pdf" → "xxx.pdf"
    //     $storagePath = str_replace('storage/', '', $report->report_path);

    //     if (!Storage::disk('public')->exists($storagePath)) {
    //         abort(404, 'File not found on server');
    //     }

    //     return Storage::disk('public')->download(
    //         $storagePath,
    //         $report->filename ?? basename($storagePath)
    //     );
    // }


    // New method to download saved PDF
    public function downloadSavedPdf($id)
    {
        $report = DB::table('legal_audit_reports')
            ->where('legal_audit_id', $id)
            ->first();

        if (!$report || !Storage::disk('public')->exists($report->report_path)) {
            abort(404, 'Report not found');
        }

        $filePath = storage_path('app/public/' . $report->report_path);

        return response()->download(
            $filePath,
            $report->filename,
            [
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma'        => 'no-cache',
                'Expires'       => '0',
            ]
        );
    }


    // Get all saved reports for an audit
    // public function getAuditReports($audit_id)
    // {
    //     $reports = DB::table('legal_audit_reports')
    //         ->where('legal_audit_id', $audit_id)
    //         ->orderBy('generated_at', 'desc')
    //         ->get();

    //     return response()->json($reports);
    // }


    public function getParameterSummaries(Request $request)
    {
        $request->validate([
            'audit_id' => 'required|exists:legal_audits,id'
        ]);

        $auditId = $request->audit_id;

        // Get parameters for this audit with their names
        $parameters = DB::table('legal_audit_parameters as lap')
            ->join('qm_sheet_parameters as p', 'p.id', '=', 'lap.parameter_id')
            ->where('lap.legal_audit_id', $auditId)
            ->select(
                'p.id as parameter_id',
                'p.parameter as parameter_name'
            )
            ->orderBy('p.id')
            ->get();

        // Get existing summaries
        $summaries = DB::table('legal_parameter_summaries')
            ->where('legal_audit_id', $auditId)
            ->get()
            ->keyBy('parameter_id');

        // Get recommendations
        $recommendations = DB::table('legal_audits')
            ->where('id', $auditId)
            ->value('recommendations');

        return response()->json([
            'status' => true,
            'parameters' => $parameters,
            'summaries' => $summaries,
            'recommendations' => $recommendations
        ]);
    }

    /**
     * Save parameter summaries and recommendations
     */
    public function saveSummariesAndRecommendations(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'audit_id' => 'required|exists:legal_audits,id',
            'summaries' => 'array',
            'recommendations' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $auditId = $request->audit_id;

            // Save parameter summaries
            if ($request->has('summaries')) {
                foreach ($request->summaries as $parameterId => $summary) {
                    if (!empty($summary)) {
                        DB::table('legal_parameter_summaries')->updateOrInsert(
                            [
                                'legal_audit_id' => $auditId,
                                'parameter_id' => $parameterId
                            ],
                            [
                                'summary' => $summary,
                                'updated_at' => now(),
                                'created_at' => DB::raw('COALESCE(created_at, NOW())')
                            ]
                        );
                    }
                }
            }

            // Save recommendations
            if ($request->has('recommendations')) {
                DB::table('legal_audits')
                    ->where('id', $auditId)
                    ->update([
                        'recommendations' => $request->recommendations,
                        'updated_at' => now()
                    ]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Summaries and recommendations saved successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to save: ' . $e->getMessage()
            ], 500);
        }
    }


    public function sendDraftPdf($auditId)
    {
        // 1️⃣ Fetch audit
        $audit = DB::table('legal_audits')->where('id', $auditId)->first();

        if (!$audit) {
            return redirect()->back()->with('error', 'Audit not found');
        }

        // 2️⃣ Fetch latest report
        $report = DB::table('legal_audit_reports')
            ->where('legal_audit_id', $audit->id)
            ->latest('id')
            ->first();

        if (!$report || empty($report->report_path)) {
            return redirect()->back()->with('error', 'Draft PDF not found');
        }

        // 3️⃣ Build full file path
        $filePath = storage_path('app/public/' . $report->report_path);

        if (!file_exists($filePath)) {
            \Log::error('PDF missing at path: ' . $filePath);
            return redirect()->back()->with('error', 'PDF file missing from storage');
        }

        // 4️⃣ Collect recipients
        $toEmails = ['raghav.badaya@qdegrees.com'];

        if (empty($toEmails)) {
            return redirect()->back()->with('error', 'No recipient email found');
        }

        // 🔴 DEBUG: confirm function reached
        \Log::info('sendDraftPdf reached', [
            'audit_id' => $audit->id,
            'file' => $filePath,
            'to' => $toEmails
        ]);

        // 5️⃣ Send Mail with PDF attachment
        try {
            Mail::send([], [], function ($message) use ($toEmails, $filePath, $audit) {
                $message->from(
                    config('mail.from.address'),
                    config('mail.from.name')
                )
                    ->to($toEmails)
                    ->subject('Draft Legal Audit Report – Audit ID ' . $audit->id)
                    ->html(
                        '<p>Please find attached the <b>draft legal audit report</b>.</p>
                     <p><b>Audit ID:</b> ' . $audit->id . '</p>
                     <p>This is a draft copy for review.</p>'
                    )
                    ->attach($filePath, [
                        'as'   => 'Draft_Legal_Audit_Report_' . $audit->id . '.pdf',
                        'mime' => 'application/pdf',
                    ]);
            });

            \Log::info('Mail send executed successfully for audit ' . $audit->id);
        } catch (\Exception $e) {
            \Log::error('Mail sending failed', [
                'audit_id' => $audit->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Mail sending failed. Check logs.');
        }

        return redirect()->back()->with('success', 'Draft PDF sent successfully');
    }
}
