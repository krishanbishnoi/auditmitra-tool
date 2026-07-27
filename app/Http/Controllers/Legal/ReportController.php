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

class ReportController extends Controller
{


    public function reportList()
    {
        $audit_reports = DB::table('legal_audit_reports')
            ->join('legal_audits', 'legal_audit_reports.legal_audit_id', '=', 'legal_audits.id')
            ->join('legal_cycles', 'legal_audits.legal_cycle_id', '=', 'legal_cycles.id')
            ->where('legal_audit_reports.client_id', auth()->user()->client_id)
            ->where('legal_audits.audit_status', 1)
            ->select(
                'legal_audit_reports.*',
                'legal_cycles.name as legal_cycle_name'
            )
            ->orderBy('legal_audit_reports.id', 'desc')
            ->get();


        //echo '<pre>'; print_r($audit_reports); die;
        // Return the view with grouped data
        return view('legal.reports.list', compact('audit_reports'));
    }



    // public function downloadReports($audit_id)
    // {
    //     // echo '<pre>'; print_r($audit_id); die;
    //     // Fetch the report for the given audit_id
    //     $audit = DB::table('audits')->where('id', $audit_id)->first();
    //     $report = DB::table('audit_reports')->where('audit_id', $audit_id)->first();
    //     $agency = DB::table('agencies')->where('id', $report->agency_id)->first();

    //     if (!$report) {
    //         return back()->with('error', 'No reports found for this Audit ID.');
    //     }

    //     // Create a temporary directory for the ZIP file
    //     $zipFileName = 'Audit_Reports_00' . $audit_id . '_' . $agency->name . '_' . $audit->agency_location . '.zip';
    //     $tempZipPath = storage_path($zipFileName);
    //     $zip = new ZipArchive();

    //     // Open the ZIP file for creating
    //     if ($zip->open($tempZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {

    //         // Array of PDF paths to include in the ZIP file
    //         $pdfPaths = [
    //             $report->audit_result_pdf,
    //             $report->checksheet_pdf,
    //             $report->closure_pdf,
    //             $report->closure_history_pdf
    //         ];

    //         // Add each PDF file to the ZIP, if the file exists
    //         foreach ($pdfPaths as $path) {
    //             if ($path && Storage::disk('public')->exists($path)) {
    //                 $zip->addFile(storage_path('app/public/' . $path), basename($path));
    //             }
    //         }

    //         // Close the ZIP file
    //         $zip->close();

    //         // Return the ZIP file for download and delete the file after sending
    //         return response()->download($tempZipPath)->deleteFileAfterSend(true);
    //     }

    //     return back()->with('error', 'Failed to create ZIP file. Please try again.');
    // }


    // public function bulkDownloadForm()
    // {
    //     // Cycle list database se laa rahe hain (adjust table/column name as per your DB)
    //     $cycles = DB::table('audit_cycles')->where('client_id', auth()->user()->client_id)->pluck('name', 'id');

    //     return view('bulkdownloadform', compact('cycles'));
    // }
    // public function downloadBulkReports(Request $request)
    // {
    //     // Fetch all reports for the client
    //     $cycle = DB::table('audit_cycles')->where('id', $request->input('cycle'))->value('name');
    //     $list = $request->input('sheet_type');
    //     // dd($list);
    //     $client_id = auth()->user()->client_id;
    //     $reports = DB::table('audit_reports')
    //         ->where('client_id', $client_id)
    //         ->where('process_review_period', $cycle)
    //         ->get();

    //     if ($reports->isEmpty()) {
    //         return back()->with('error', 'No reports found for this client.');
    //     }

    //     // Create a temporary ZIP file
    //     $zipFileName = 'BulkReport' . time() . '.zip';
    //     $tempZipPath = storage_path($zipFileName);
    //     $zip = new \ZipArchive();

    //     if ($zip->open($tempZipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
    //         foreach ($reports as $report) {
    //             $agency = DB::table('agencies')->where('id', $report->agency_id)->value('name');
    //             $pdfPath = $report->{$list};

    //             if (!empty($pdfPath) && Storage::disk('public')->exists($pdfPath)) {
    //                 $fullPath = storage_path('app/public/' . $pdfPath);
    //                 $fileNameInZip = $list . '_' . $report->audit_id . '_' . $agency . '.pdf'; // Optional: Rename inside zip

    //                 $zip->addFile($fullPath, $fileNameInZip);
    //             }
    //         }

    //         $zip->close();

    //         return response()->download($tempZipPath)->deleteFileAfterSend(true);
    //     }

    //     return back()->with('error', 'Failed to create ZIP file. Please try again.');
    // }
}
