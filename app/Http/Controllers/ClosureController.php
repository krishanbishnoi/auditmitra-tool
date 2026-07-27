<?php



namespace App\Http\Controllers;
ini_set('max_execution_time', 380);



use App\OldScore;

use App\AuditAlertBox;

use App\AuditParameterResult;

use App\AuditResult;

use App\AuditCycle;

use App\SavedAudit;

use App\SavedQcAudit;

use App\Partner;

use App\QmSheet;

use App\RawData;

use App\RcaMode;

use App\RcaType;

use App\Reason;

use App\ReasonType;

use App\TypeBScoringOption;

use Auth;

use Crypt;

use Carbon\Carbon;

use Illuminate\Http\Request;

use App\Model\Branch;

use App\Model\Branchable;

use App\Model\ProductUser;
use App\Model\Productattribute;

use App\Agency;

use App\Yard;

use App\Qc;

use App\Model\Products;

use App\User;

use App\RedAlert;

use App\Model\BranchRepo;
use App\Model\AgencyMobileEmail;
use App\Model\AgencyRepo;

use Mail;

use App\Artifact;

use App\Model\AcrReportData;



use App\Model\CashDepositionData;

use App\Model\ReceiptCutData;

use App\Model\DelaySeconAllocData;


use ZipArchive;
use DB;

use App\AuditQc;
use App\Audit;

use App\QcParameterResult;

use App\QcResult;
use App\QmSheetParameter;
use App\QmSheetSubParameter;
ini_set('memory_limit', '-1');



use Maatwebsite\Excel\Facades\Excel;

use App\Exports\QcAndQaChangesExport;

use App\Imports\AcrImport;
use App\Imports\CashDespositionImport;
use App\Imports\ReceiptCutImport;
use App\Imports\SecondaryAllocationImport;
use App\Imports\OldscoreImport;
use Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;


class ClosureController extends Controller{

    
    
    public function showAuditClosureForm($closureId, $auditId,$link)
    {

        $audit_closure_link = DB::table('closure_audits')
            ->where('audit_id', $auditId)
            ->where('id', $closureId)
            ->where('link', $link)
            ->first();

            $client_id = DB::table('audits')->where('id', $auditId)->value('client_id');
            $tat =DB::table('users')->where('id', $client_id)->value('closure_link_days');

            
        if (!$audit_closure_link) {
            // Redirect back with an error message
            // return redirect()->back()->with('error', 'Some artifacts are still in Pending status. Submission is not allowed.');
            print('link expired. Submission is not allowed.'); die;
        }
        if (\Carbon\Carbon::parse($audit_closure_link->updated_at)->lt(now()->subDays($tat))) {
        print('Link expired. Submission is not allowed.');
        die;
        }
        // Define the excluded sub-parameter IDs
        $excludedSubParameterIds = [42, 62, 82, 133];
    
        // Fetch audit details for the given auditId
        $audit_details = DB::table('audits')->where('id', $auditId)->first();
    
        // Get all unsatisfactory sub-parameters with their remarks
        $unset_params = DB::table('audit_results')
            ->where('audit_id', $auditId)
            ->where('option_selected', 'Unsatisfactory')
            ->whereNotIn('sub_parameter_id', $excludedSubParameterIds) // Exclude specific sub-parameter IDs
            ->select('sub_parameter_id', 'remark') // Select both fields
            ->get()
            ->toArray();
    
        // Fetch already approved sub-parameters from audit_closure_artifacts
        $approved_params_ids = DB::table('audit_closure_artifacts')
            ->where('audit_closure_id', $closureId)
            ->where('approval_status', 'Approved')
            ->pluck('sub_parameter_id')
            ->toArray();
    
        // Filter out approved parameters
        $filtered_params = array_filter($unset_params, function ($param) use ($approved_params_ids) {
            return !in_array($param->sub_parameter_id, $approved_params_ids);
        });
    
        // Extract sub-parameter IDs
        $sub_parameter_ids = array_column($filtered_params, 'sub_parameter_id');
    
        // Fetch details of the unsatisfactory sub-parameters
        $unsetParams = DB::table('qm_sheet_sub_parameters')
            ->whereIn('id', $sub_parameter_ids)
            ->get();
    
        // Attach remarks to the fetched sub-parameters
        foreach ($unsetParams as $param) {
            foreach ($filtered_params as $unsatisfactory) {
                if ($param->id == $unsatisfactory->sub_parameter_id) {
                    // Check if there's an entry in audit_closure_artifacts for this sub-parameter
                    $closure_artifact = DB::table('audit_closure_artifacts')
                        ->where('audit_closure_id', $closureId)
                        ->where('sub_parameter_id', $param->id)
                        ->where('rejection_status', 1)
                        ->first();
    
                    if ($closure_artifact) {
                        // If found, use the rejection reason from the closure artifacts
                        $param->remark = $closure_artifact->rejection_reason;
                    } else {
                        // If not found, use the remark from audit_results
                        $param->remark = $unsatisfactory->remark;
                    }
                }
            }
        }
    
        // Return the view with the necessary data compacted
        return view('closure.audit_closure_form', compact('unsetParams', 'auditId', 'audit_details', 'closureId','link'));
    }

    public function submitAuditClosure(Request $request, $closureId, $auditId,$link)
    {

     //   echo '<pre>'; print_r($request->all()); die;
        // Check if any artifacts are in "Pending" status
        $pendingArtifacts = DB::table('audit_closure_artifacts')
            ->where('audit_id', $auditId)
            ->where('audit_closure_id', $closureId)
            ->where('approval_status', 'Pending') // Check for artifacts with 'Pending' status
            ->exists();
    
        if ($pendingArtifacts) {
            // Redirect back with an error message
            // return redirect()->back()->with('error', 'Some artifacts are still in Pending status. Submission is not allowed.');
            print('Some artifacts are still in Pending status. Submission is not allowed.'); die;
        }
    
        // Fetch unsatisfactory parameters related to the audit
        $unset_params_ids = DB::table('audit_results')
            ->where('audit_id', $auditId)
            ->where('option_selected', 'Unsatisfactory')
            ->pluck('sub_parameter_id')
            ->toArray();
    
        $unsetParams = DB::table('qm_sheet_sub_parameters')
            ->whereIn('id', $unset_params_ids)
            ->get();
    
        foreach ($unsetParams as $param) {
            // Check if the 'justification' and 'action_taken' fields exist in the request
            $justification = $request->input('justification_' . $param->id);
            $actionTaken = $request->input('action_taken_' . $param->id);
    
            // Only process the parameters that have 'justification' and 'action_taken' in the request
            if ($justification && $actionTaken) {
                $artifact = $request->file('artifact_' . $param->id); // File upload
    
                // Store the uploaded artifact if it exists
                $artifactPath = $artifact ? $artifact->store('artifacts') : null;
    
                // Disable all previous entries for the same combination, regardless of approval status
                DB::table('audit_closure_artifacts')
                    ->where('audit_id', $auditId)
                    ->where('audit_closure_id', $closureId)
                    ->where('sub_parameter_id', $param->id)
                    ->update(['rejection_status' => 0]);
    
                // Insert the new entry with rejection_status set to 1
                DB::table('audit_closure_artifacts')
                    ->insert([
                        'audit_id' => $auditId,
                        'audit_closure_id' => $closureId,
                        'sub_parameter_id' => $param->id,
                        'artifact' => $artifactPath,
                        'action_taken' => $actionTaken,
                        'justification' => $justification,
                        'resent_count' => 1,
                        'rejection_status' => 1, // Mark this as active
                        'created_at' => now(),
                        // 'client_id' => auth()->user()->client_id,
                    ]);
            }
        }
    
      
        print('Audit Closure Submitted Successfully!'); 
        $audit_closure_link = DB::table('closure_audits')
        ->where('audit_id', $auditId)
        ->where('id', $closureId)
        ->where('link', $link)
        ->update(['link' => '']);
        
        die;
        return redirect()->route('audit.closure.success')->with('message', 'Audit Closure Submitted Successfully!');
    }


    // public function listAuditClosures($status)
    // {
    //     // Fetch closure audits based on the status passed from the URL
    //     $user_role = Auth::user()->roles()->first()->name;
    //     if($user_role == 'Admin'){
    //         $closureData = DB::table('closure_audits')
    //         ->join('audits', 'closure_audits.audit_id', '=', 'audits.id')
    //         ->select('closure_audits.id as closure_id', 'closure_audits.status as closure_status', 'audits.*') // Select fields from both tables
    //         ->where('closure_audits.status', $status) // Filter by status
    //         ->where('closure_audits.audit_agency_id', Auth::user()->id) // Filter by status
    //         ->orderBy('audits.audit_date_by_aud', 'desc')
    //         ->get()
    //         ->map(function ($audit) {
    //             // Convert each audit record into a model to load the relationships
    //             $auditModel = Audit::find($audit->id);  // Replace with your Audit model
    //             $auditModel->closure_id = $audit->closure_id;
    //             $auditModel->closure_status = $audit->closure_status;  // Use closure_status correctly
    //             return $auditModel->load(['qmsheet', 'product', 'agency.city.state', 'qa_qtl_detail']);
    //         });
    //     }else{
    //         $closureData = DB::table('closure_audits')
    //         ->where('closure_audits.client_id', Auth::user()->client_id)
    //         ->join('audits', 'closure_audits.audit_id', '=', 'audits.id')
    //         ->select('closure_audits.id as closure_id', 'closure_audits.status as closure_status', 'audits.*') // Select fields from both tables
    //         ->where('closure_audits.status', $status) // Filter by status
    //         ->orderBy('audits.audit_date_by_aud', 'desc')
    //         ->get()
    //         ->map(function ($audit) {
    //             // Convert each audit record into a model to load the relationships
    //             $auditModel = Audit::find($audit->id);  // Replace with your Audit model
    //             $auditModel->closure_id = $audit->closure_id;
    //             $auditModel->closure_status = $audit->closure_status;  // Use closure_status correctly
    //             return $auditModel->load(['qmsheet', 'product', 'agency.city.state', 'qa_qtl_detail']);
    //         });
    //     }

    //     $tat =DB::table('users')->where('id', auth()->user()->client_id)->value('closure_link_days');
        
    
    //    // echo '<pre>'; print_r($closureData); die;
    //     return view('closure.audit_closure_list', compact('closureData', 'status', 'tat'));
    // }

    public function listAuditClosures($status)
    {
        // Fetch closure audits based on the status passed from the URL
        $user_role = Auth::user()->roles()->first()->name;
        if ($user_role == 'Admin') {

            $received_closure_ids = DB::table('audit_closure_artifacts')
                ->select('audit_id')
                ->distinct()
                ->pluck('audit_id')
                ->toArray();
            if ($status == 2) {

                $closureData = DB::table('closure_audits')
                    ->join('audits', 'closure_audits.audit_id', '=', 'audits.id')
                    ->select('closure_audits.id as closure_id', 'closure_audits.status as closure_status', 'audits.*') // Select fields from both tables
                    ->whereIn('closure_audits.audit_id', $received_closure_ids) // Filter by status
                    ->where('closure_audits.audit_agency_id', Auth::user()->id) // Filter by status
                    ->get()
                    ->map(function ($audit) {
                        // Convert each audit record into a model to load the relationships
                        $auditModel = Audit::find($audit->id);  // Replace with your Audit model
                        $auditModel->closure_id = $audit->closure_id;
                        $auditModel->closure_status = $audit->closure_status;  // Use closure_status correctly
                        return $auditModel->load(['qmsheet', 'product', 'agency.city.state', 'qa_qtl_detail']);
                    });
            } else {
                $closureData = DB::table('closure_audits')
                    ->join('audits', 'closure_audits.audit_id', '=', 'audits.id')
                    ->select('closure_audits.id as closure_id', 'closure_audits.status as closure_status', 'audits.*') // Select fields from both tables
                    ->where('closure_audits.status', $status) // Filter by status
                    ->where('closure_audits.audit_agency_id', Auth::user()->id) // Filter by status
                    ->orderBy('audits.audit_date_by_aud', 'desc')
                    ->get()
                    ->map(function ($audit) {
                        // Convert each audit record into a model to load the relationships
                        $auditModel = Audit::find($audit->id);  // Replace with your Audit model
                        $auditModel->closure_id = $audit->closure_id;
                        $auditModel->closure_status = $audit->closure_status;  // Use closure_status correctly
                        return $auditModel->load(['qmsheet', 'product', 'agency.city.state', 'qa_qtl_detail']);
                    });
            }
        } else {

            if ($status == 2) {

                $received_closure_ids = DB::table('audit_closure_artifacts')
                    ->select('audit_id')
                    ->distinct()
                    ->pluck('audit_id')
                    ->toArray();
                //  echo '<pre>'; print_r($received_closure_ids); die;
                $closureData = DB::table('closure_audits')
                ->where('closure_audits.client_id', Auth::user()->client_id)
                    ->join('audits', 'closure_audits.audit_id', '=', 'audits.id')
                    ->select('closure_audits.id as closure_id', 'closure_audits.status as closure_status', 'audits.*') // Select fields from both tables
                    ->whereIn('closure_audits.audit_id', $received_closure_ids) // Filter by status
                    ->orderBy('audits.audit_date_by_aud', 'desc')
                    ->get()
                    ->map(function ($audit) {
                        // Convert each audit record into a model to load the relationships
                        $auditModel = Audit::find($audit->id);  // Replace with your Audit model
                        $auditModel->closure_id = $audit->closure_id;
                        $auditModel->closure_status = $audit->closure_status;  // Use closure_status correctly
                        return $auditModel->load(['qmsheet', 'product', 'agency.city.state', 'qa_qtl_detail']);
                    });


                //echo '<pre>'; print_r($closureData); die;
            } else {
                $closureData = DB::table('closure_audits')
                    ->where('closure_audits.client_id', Auth::user()->client_id)
                    ->join('audits', 'closure_audits.audit_id', '=', 'audits.id')
                    ->select('closure_audits.id as closure_id', 'closure_audits.status as closure_status', 'audits.*') // Select fields from both tables
                    ->where('closure_audits.status', $status) // Filter by status
                    ->orderBy('audits.audit_date_by_aud', 'desc')
                    ->get()
                    ->map(function ($audit) {
                        // Convert each audit record into a model to load the relationships
                        $auditModel = Audit::find($audit->id);  // Replace with your Audit model
                        $auditModel->closure_id = $audit->closure_id;
                        $auditModel->closure_status = $audit->closure_status;  // Use closure_status correctly
                        return $auditModel->load(['qmsheet', 'product', 'agency.city.state', 'qa_qtl_detail']);
                    });
            }
        }
        $tat = DB::table('users')->where('id', auth()->user()->client_id)->value('closure_link_days');


        // echo '<pre>'; print_r($closureData); die;
        return view('closure.audit_closure_list', compact('closureData', 'status', 'tat'));
    }
    public function viewAuditClosure($closureId){


        $closureData = DB::table('closure_audits')->where('id', $closureId)->first();
        $unsetParams = DB::table('audit_results')
            ->where('audit_id', $closureData->audit_id)
            ->where('option_selected', 'Unsatisfactory')
            ->join('qm_sheet_sub_parameters', 'audit_results.sub_parameter_id', '=', 'qm_sheet_sub_parameters.id')
            ->select('qm_sheet_sub_parameters.sub_parameter', 'audit_results.remark', 'audit_results.created_at') // Assuming 'parameter_name' is the field for the parameter name
            ->get()
            ->toArray();
     
        // dd($unsetParams);
       $closureDetails = DB::table('audit_closure_artifacts')
            ->leftjoin('qm_sheet_sub_parameters', 'audit_closure_artifacts.sub_parameter_id','qm_sheet_sub_parameters.id')
            ->where('audit_closure_id', $closureId) // Use the provided audit_id directly
            ->select('audit_closure_artifacts.*','qm_sheet_sub_parameters.sub_parameter')
            ->get();

    
        
            return view('closure.view', compact('closureDetails','unsetParams'));


    }

    // V Download All Artifacts Files like jpg, excel, pdf etc. working fine, with error message
    public function downloadArtifacts($closure_id)
    {
        // Set max execution time for large file operations
        ini_set('max_execution_time', 380);

        $artifacts = DB::table('audit_closure_artifacts')
            ->where('audit_closure_id', $closure_id)
            ->where('rejection_status',1)
            ->get();

        // Check if no artifacts found
        if ($artifacts->isEmpty()) {
            Log::info("No artifacts found for closure ID: {$closure_id}");
            return back()->with('error', 'No artifacts attached.');
        }

        $zipFileName = 'Artifacts_Closure_' . $closure_id . '.zip';
        $zip = new ZipArchive();

        // Temporary file path for the ZIP file
        $tempZipPath = tempnam(sys_get_temp_dir(), 'zip');
        Log::info("Temporary ZIP file path: {$tempZipPath}");

        if ($zip->open($tempZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            Log::error("Failed to open ZIP archive at {$tempZipPath}");
            return back()->with('error', 'Failed to create ZIP file.');
        }

        // Add each artifact to the ZIP file
        foreach ($artifacts as $artifact) {
            $artifactPath = 'artifacts/' . $artifact->artifact; // Path relative to storage/app
            $absolutePath = storage_path('app/' . $artifact->artifact); // Correct absolute path

            // Check if the file exists
            if (file_exists($absolutePath) && is_file($absolutePath)) {
                Log::info("File exists and is valid: {$artifactPath} - Absolute Path: {$absolutePath}");
                $zip->addFile($absolutePath, basename($artifact->artifact));
            } else {
                Log::warning("File missing or not valid: {$artifactPath} - Absolute Path: {$absolutePath}");
            }
        }

        $zip->close();

        if (!file_exists($tempZipPath) || filesize($tempZipPath) === 0) {
            Log::error("Temporary ZIP file creation failed or is empty: {$tempZipPath}");
            return back()->with('error', 'Failed to generate ZIP file.');
        }

        // Stream the ZIP file to the user
        $response = response()->stream(function () use ($tempZipPath) {
            try {
                readfile($tempZipPath);
            } catch (\Exception $e) {
                Log::error("Error during file streaming: {$e->getMessage()}");
                abort(500, 'Error occurred while preparing the download.');
            }
        }, 200, [
            'Content-Type' => 'application/zip',
            'Content-Disposition' => 'attachment; filename="' . $zipFileName . '"',
            'Cache-Control' => 'no-store',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'Content-Transfer-Encoding' => 'binary',
            'Connection' => 'keep-alive',
        ]);

        $response->send();
        if (file_exists($tempZipPath)) {
            unlink($tempZipPath); // Delete the temporary ZIP file
        }

        return $response;
    }

   public function changeClosureStatus(Request $request, $id, $status){
        // Fetch closure audit details
        $closureAudit = DB::table('closure_audits')
            ->join('agencies', 'closure_audits.agency_id', '=', 'agencies.id')
            ->where('closure_audits.id', $id)
            ->select('closure_audits.*', 'agencies.name', 'agencies.email as agency_email')
            ->first();

        $audit_cycle_id = Audit::where('id',$closureAudit->audit_id)->pluck('audit_cycle_id')->first();
        $audit_cycle_name = DB::table('audit_cycles')->where('id',$audit_cycle_id)->pluck('name')->first();
    // echo '<pre>'; print_r($audit_cycle_name); die;
        if (!$closureAudit) {
            return redirect()->back()->with('error', 'Closure audit not found.');
        }

        // Update closure audit status
        DB::table('closure_audits')
            ->where('id', $id)
            ->update(['status' => $status]);

        // Set email details based on status
        if ($status == 1) {
            $message = 'Audit Action Plan Request successfully approved!';
            $subject = "Closure Status of Process Review for {$audit_cycle_name} SoC";
            $body = "
                <p>Dear {$closureAudit->name},</p>
                <p>We are pleased to inform you that we have completed our review of the closures on the unsatisfactory points for the process review month of <strong> {$audit_cycle_name} [SoC]</strong>.</p>
                <p>Your timely responses and efforts to address the identified areas for improvement are greatly appreciated. After a thorough assessment, we confirm that there are no pending closures from your end for this review period.</p>
                <p>Thank you for your cooperation and commitment to ensuring process adherence and continuous improvement.</p>
                <br>
                <p>Best regards,</p>
                <p><strong>The Audit Team</strong></p>
            ";
        } elseif ($status == 2) {
            $message = 'Audit Action Plan Request rejected!';
            $subject = "Review of Justifications for Unsatisfactory Points – Further Action Required for {$audit_cycle_name}";

            $body = "
                <p>Dear {$closureAudit->name},</p>
                <p>We have reviewed the justifications provided in response to the unsatisfactory points raised during the recent process review month of {$audit_cycle_name}. While we appreciate your prompt submission, there are still some discrepancies and areas where the provided justifications do not fully address the reasons for the unsatisfactory points.</p>
                <p>To ensure comprehensive closure, we kindly request you to review the auditor’s remarks on the rejected closure points in detail. We ask that you share revised justifications that thoroughly address these issues at your earliest convenience.</p>
                <br>
                <p>Best regards,</p>
                <p><strong>The Audit Team</strong></p>
            ";
        }
        $rbl_bank_email = 'CollectionProcessReview@rblbank.com';

        // Send email with HTML formatting
        Mail::send([], [], function ($message) use ($closureAudit, $subject, $body) {
            $message->from('auditmitr@qdegrees.com', 'Audit Team')
                    ->to($closureAudit->agency_email) // Primary recipient
                    // ->cc($rbl_bank_email) // Add only the RBL Bank email as CC
                    ->subject($subject)
                    ->html($body, 'text/html'); // Set email content as HTML
        });

        // Flash success message and redirect back
        return redirect()->back()->with('success', $message);
    }
    public function approveArtifact(Request $request, $closureId)
    {
        // Fetch the artifact details
        $artifact = DB::table('audit_closure_artifacts')->where('id', $closureId)->first();
    
        if (!$artifact) {
            return redirect()->back()->with('error', 'Artifact not found.');
        }
    
        $auditId = $artifact->audit_id;
    
        // Update the artifact's approval status
        DB::table('audit_closure_artifacts')
            ->where('id', $closureId)
            ->update(['approval_status' => 'Approved']);
    
        // Fetch all artifacts for the audit
        $artifacts = DB::table('audit_closure_artifacts')
            ->where('audit_id', $auditId)
            ->get();
    
        // Count all artifacts and approved artifacts
        $totalArtifacts = $artifacts->count();
        $approvedArtifacts = $artifacts->where('approval_status', 'Approved')->count();
    
        // Fetch unset parameters for the audit
       
        // Fetch unset parameters for the audit
        $excludedSubParameterIds = [42, 62, 82, 133]; // Define excluded sub-parameter IDs

        $unsetParams = DB::table('audit_results')
            ->where('audit_id', $auditId)
            ->where('option_selected', 'Unsatisfactory')
            ->whereNotIn('audit_results.sub_parameter_id', $excludedSubParameterIds) // Exclude specific sub-parameter IDs
            ->join('qm_sheet_sub_parameters', 'audit_results.sub_parameter_id', '=', 'qm_sheet_sub_parameters.id')
            ->select('qm_sheet_sub_parameters.sub_parameter', 'audit_results.remark', 'audit_results.created_at')
            ->get();

        $unsetParamsCount = $unsetParams->count();

        if ($approvedArtifacts == $unsetParamsCount) {
            // Update closure status to approved (status = 1) and send an email
            $this->changeClosureStatus($request, $artifact->audit_closure_id, 1);

            //create and save pdf of audit closue history

            $closureHistory = DB::table('audit_closure_artifacts')
            ->join('qm_sheet_sub_parameters', 'audit_closure_artifacts.sub_parameter_id', '=', 'qm_sheet_sub_parameters.id')
            ->where('audit_closure_artifacts.audit_closure_id', $artifact->audit_closure_id)
            ->where('audit_closure_artifacts.approval_status','Approved')
            ->select('audit_closure_artifacts.*', 'qm_sheet_sub_parameters.sub_parameter as parameter_name')
            ->get();


           // echo '<pre>'; print_r($closureHistory ); die;
           $audit_details = DB::table('audits')->where('id', $auditId)->first();
           $agency_details = DB::table('agencies')->where('id', $audit_details->agency_id)->first();
         
           $process_review_month = DB::table('audit_cycles')->where('id', $audit_details->audit_cycle_id)->pluck('name')->first();
           $getAuditClient=\App\User::find($audit_details->audited_by_id);
        $clientName = \App\User::where('id', $getAuditClient->client_id)->first();
            
           $pdfData = [
                'closureHistory' => $closureHistory,
                'audit_id' => $auditId,
                'agency_details' => $agency_details,
                'audit_details' => $audit_details,
                'process_review_month' => $process_review_month,
                'client_name' => $clientName,
            ];
            
            // Generate the PDF for the closure history
            $pdf = PDF::loadView('closure.closure_history_pdf',$pdfData);
            $pdfContent = $pdf->output();

            
            // Create a month-wise folder for storing the PDF
            $folderPath = 'audit_reports/' . date('Y') . '/' . date('F');
            $fileName = 'audit_approved_history_' . time() . '.pdf';
            $fullPath = $folderPath . '/' . $fileName;

            // Store the PDF file in the public storage
            Storage::disk('public')->put($fullPath, $pdfContent);
    

            // Now, save the path of the generated PDF in the report table

            DB::table('audit_reports')
            ->where('audit_id', $auditId)
            ->update([
               'closure_history_pdf' => $fullPath,
                'updated_at' => now(),
            ]);




        //   echo 'ravi'; die;
            // Redirect to closure list page with a success message
            return redirect()->route('audit.closure.list', ['status' => 1])
                ->with('success', 'All artifacts approved. Closure status updated and email sent.');
        }
    
    
        // Redirect back if not all artifacts are approved
        return redirect()->back()->with('success', 'Artifact approved successfully.');
    }
    
    public function rejectArtifact(Request $request, $closureId)
    {
        // Validate rejection reason
        $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        // Update the artifact's approval status and rejection reason
        DB::table('audit_closure_artifacts')
            ->where('id', $closureId)
            ->update([
                'approval_status' => 'Rejected',
                'rejection_reason' => $request->rejection_reason,
            ]);

        // Resend the closure form if rejected
        $this->resendClosureForm($closureId); // You need to create this function

        return redirect()->back()->with('success', 'Artifact rejected and closure form resent.');
    }

    public function resendClosureForm($closureId)
    {
        // Increment the resend count
        DB::table('audit_closure_artifacts')
            ->where('id', $closureId)
            ->increment('resent_count');

        // Resend logic for unresolved artifacts
        // Fetch the artifact details and send the form again (similar to what you have done before)
    }


    public function resendAuditClosure(Request $request, $auditId){

        //echo '<pre>'; print_r( $auditId); die;
        // Fetch the unresolved (non-approved) artifacts
        $unsetParams = DB::table('audit_closure_artifacts')
        ->join('qm_sheet_sub_parameters', 'audit_closure_artifacts.sub_parameter_id', '=', 'qm_sheet_sub_parameters.id')
        ->where('audit_closure_artifacts.audit_id', $auditId)
        ->where('audit_closure_artifacts.approval_status', '!=', 'Approved')
        ->where('audit_closure_artifacts.rejection_status',1)
        ->select('qm_sheet_sub_parameters.sub_parameter as parameter_name', 'audit_closure_artifacts.*')
        ->get();

        $randomString = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 25);
       // echo '<pre>'; print_r($unsetParams); die;
     
        // Fetch the agency details
        $agency_id = DB::table('audits')->where('id', $auditId)->pluck('agency_id')->first();
        $agency_name = DB::table('agencies')->where('id', $agency_id)->pluck('name')->first();
        $agencyEmail = DB::table('agencies')->where('id', $agency_id)->pluck('email')->first();

        if ($unsetParams->isNotEmpty()) {

            $agency_details = DB::table('agencies')->where('id', $agency_id)->first();
            $audit_details = DB::table('audits')->where('id', $auditId )->first();
        $process_review_month = DB::table('audit_cycles')->where('id',$audit_details->audit_cycle_id)->pluck('name')->first();

      
     
            // Fetch closure ID or create a new one if not exists
            $closureId = DB::table('closure_audits')
                ->where('audit_id', $auditId)
                ->pluck('id')
                ->first();

                if (!$closureId) {
                    $closureId = DB::table('closure_audits')->insertGetId([
                        'audit_id' => $auditId,
                        'agency_id' => $agency_id,
                        'link' => $randomString,
                        'remarks' => '',
                        'created_at' => now(),
                        'audit_agency_id' => 1,
                    ]);
                }
                $audit_closure_link = DB::table('closure_audits')
                                    ->where('audit_id', $auditId)
                                    ->where('id', $closureId)
                                    ->update(['link' => $randomString]);
                $client_name = DB::table('users')->where('id', auth()->user()->client_id)->value('name');
                $products = DB::table('products')->where('id', $audit_details->product_id )->value('name');
            $pdfData = [
                'unsetParams' => $unsetParams,
                'audit_id' => $auditId,
                'agency_details' => $agency_details, // Replace with actual agency name if available
                'audit_details' => $audit_details, // Replace with actual agency name if available
                'process_review_month'=>$process_review_month,
                'audit_agency_id' => 1,  // Save audit_agency_id
                'client_name' => $client_name,
                'product' => $products,
            ];

            //   echo '<pre>'; print_r($pdfData); die;


            // Generate the PDF for the unresolved artifacts
            $pdf = PDF::loadView('closure.resend_audit_closure_pdf', $pdfData);
            $pdfContent = $pdf->output();

            // Prepare the closure form link
            $closureFormLink = url("audit-closure-justification/{$closureId}/{$auditId}/{$randomString}");

            // Prepare email subject
            $subject = "Audit Closure Form for Audit Agency: {$agency_details->name}";
            $agency_name=$agency_details->name;
            $tat =DB::table('users')->where('id', auth()->user()->client_id)->value('closure_link_days');
            // Send email with the PDF attached using a view for the email body
            Mail::send('closure.audit_closure_mail', ['closureFormLink' => $closureFormLink,'process_review_month'=>$process_review_month,'agency_name'=>$agency_name, 'tat'=>$tat], function ($message) use ($agencyEmail, $subject, $pdfContent) {
                $message->from('auditmitr@qdegrees.com', 'Audit Team')
                    ->to($agencyEmail)
                    ->subject($subject)
                    ->attachData($pdfContent, 'UNSATISFACTORY_PARAMETERS.pdf', [
                        'mime' => 'application/pdf',
                    ]);
            });

            return redirect()->back()->with('success', 'Audit closure form resent successfully.');
        } else {
            return redirect()->back()->with('info', 'All artifacts are already approved.');
        }
    }

}


