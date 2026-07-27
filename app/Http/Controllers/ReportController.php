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

use Barryvdh\DomPDF\Facade\Pdf;

use Storage;
use ZipArchive;

class ReportController extends Controller{

    
    public function reportList()
    {
        $audit_reports = DB::table('audit_reports')->where('audit_reports.client_id', auth()->user()->client_id)->where('audit_reports.audit_id', '!=', null)
        ->join('agencies', 'audit_reports.agency_id', '=', 'agencies.id') // Join with the agencies table
        ->select('audit_reports.audit_id','audit_reports.audit_date', 'audit_reports.closure_pdf', 'audit_reports.audit_result_pdf', 
                 'audit_reports.checksheet_pdf', 'audit_reports.agency_id', 'audit_reports.user_id', 
                 'agencies.name as agency_name','agencies.agency_id','agencies.location','audit_reports.process_review_period', 'audit_reports.created_at') // Select agency name as 'agency_name'
        // ->where('audit_reports.client_id', auth()->user()->id) 
        ->orderBy('audit_reports.audit_date', 'desc')
        ->get();

        $tat =DB::table('users')->where('id', auth()->user()->client_id)->value('closure_link_days');

           //echo '<pre>'; print_r($audit_reports); die;
        // Return the view with grouped data
        return view('reports.list', compact('audit_reports', 'tat'));
    }
  


    public function downloadReports($audit_id)
    {
        // echo '<pre>'; print_r($audit_id); die;
        // Fetch the report for the given audit_id
        $audit = DB::table('audits')->where('id', $audit_id)->first();
        $report = DB::table('audit_reports')->where('audit_id', $audit_id)->first();
        $agency = DB::table('agencies')->where('id', $report->agency_id)->first();
    
        if (!$report) {
            return back()->with('error', 'No reports found for this Audit ID.');
        }
    
        // Create a temporary directory for the ZIP file
        $zipFileName = 'Audit_Reports_00' . $audit_id .'_'. $agency->name.'_'.$audit->agency_location. '.zip';
        $tempZipPath = storage_path($zipFileName);
        $zip = new ZipArchive();
    
        // Open the ZIP file for creating
        if ($zip->open($tempZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
    
            // Array of PDF paths to include in the ZIP file
            $pdfPaths = [
                $report->audit_result_pdf,
                $report->checksheet_pdf,
                $report->closure_pdf,
                $report->closure_history_pdf,
                $report->include_checksheet_pdf
            ];
    
            // Add each PDF file to the ZIP, if the file exists
            foreach ($pdfPaths as $path) {
                if ($path && Storage::disk('public')->exists($path)) {
                    $zip->addFile(storage_path('app/public/' . $path), basename($path));
                }
            }
    
            // Close the ZIP file
            $zip->close();
    
            // Return the ZIP file for download and delete the file after sending
            return response()->download($tempZipPath)->deleteFileAfterSend(true);
        }
    
        return back()->with('error', 'Failed to create ZIP file. Please try again.');
    }

    public function downloadClosureReports($audit_id)
    {
        // echo '<pre>'; print_r($audit_id); die;
        // Fetch the report for the given audit_id
        $audit = DB::table('audits')->where('id', $audit_id)->first();
        
        $report = DB::table('audit_reports')->where('audit_id', $audit_id)->first();
        $agency = DB::table('agencies')->where('id', $audit->agency_id)->first();
    
        if (!$report) {
            return back()->with('error', 'No reports found for this Audit ID.');
        }
    
        // Create a temporary directory for the ZIP file
        $zipFileName = 'Audit_Closure_Reports_00' . $audit_id .'_'. $agency->name.'_'.$audit->agency_location. '.zip';
        $tempZipPath = storage_path($zipFileName);
        $zip = new ZipArchive();
    
        // Open the ZIP file for creating
        if ($zip->open($tempZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
    
            // Array of PDF paths to include in the ZIP file
            $pdfPaths = [
                // $report->audit_result_pdf,
                // $report->checksheet_pdf,
                $report->closure_pdf,
                $report->closure_history_pdf
            ];
    
            // Add each PDF file to the ZIP, if the file exists
            foreach ($pdfPaths as $path) {
                if ($path && Storage::disk('public')->exists($path)) {
                    $zip->addFile(storage_path('app/public/' . $path), basename($path));
                }
            }
    
            // Close the ZIP file
            $zip->close();
    
            // Return the ZIP file for download and delete the file after sending
            return response()->download($tempZipPath)->deleteFileAfterSend(true);
        }
    
        return back()->with('error', 'Failed to create ZIP file. Please try again.');
    }


    public function bulkDownloadForm()
    {
        // Cycle list database se laa rahe hain (adjust table/column name as per your DB)
        $cycles = DB::table('audit_cycles')->where('client_id' , auth()->user()->client_id)->pluck('name', 'id'); 

        return view('bulkdownloadform', compact('cycles'));
    }
public function downloadBulkReports(Request $request )
{
    // Fetch all reports for the client
    $cycle = DB::table('audit_cycles')->where('id',$request->input('cycle') )->value('name');
    $list = $request->input('sheet_type');
    // dd($list);
    $client_id = auth()->user()->client_id;
    $reports = DB::table('audit_reports')
        ->where('client_id', $client_id)
        ->where('process_review_period' , $cycle)
        ->get();

    if ($reports->isEmpty()) {
        return back()->with('error', 'No reports found for this client.');
    }

    // Create a temporary ZIP file
    $zipFileName = 'BulkReport' . time() . '.zip';
    $tempZipPath = storage_path($zipFileName);
    $zip = new \ZipArchive();

    if ($zip->open($tempZipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
        foreach ($reports as $report) {
            $agency = DB::table('agencies')->where('id', $report->agency_id)->value('name');
            $pdfPath = $report->{$list};

            if (!empty($pdfPath) && Storage::disk('public')->exists($pdfPath)) {
                $fullPath = storage_path('app/public/' . $pdfPath);
                $fileNameInZip = $list. '_'. $report->audit_id . '_' . $agency . '.pdf'; // Optional: Rename inside zip

                $zip->addFile($fullPath, $fileNameInZip);
            }
        }

        $zip->close();

        return response()->download($tempZipPath)->deleteFileAfterSend(true);
    }

    return back()->with('error', 'Failed to create ZIP file. Please try again.');
}


}


