<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\BeatPlans;
use App\Model\BeatPlanSubParts;
use App\Model\Branch;
use Validator;
use Auth;
use Crypt;
//use Mail;
use DB;
use App\Agency;
use App\User;
use App\Model\Products;
use App\Model\Productattribute;
use App\Model\IntimationMail;
use App\Model\AuditorAssign;
use App\Model\AuditAllocation;
use App\Audit;
use Illuminate\Support\Facades\Mail;
use App\Mail\AuditorAssignForAudit;

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AuditAllocationImport;
use App\Mail\AuditAllocationImported;
use Illuminate\Validation\ValidationException;

use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AuditAllocationController extends Controller
{
    
    public function index()
    {
        
        // $today = Carbon::now();

        // // Calculate custom start and end dates
        // if ($today->day < 10) {
        //     // If before the 10th
        //     $startDate = $today->copy()->subMonth()->day(10)->startOfDay()->toDateTimeString();
        //     $endDate = $today->copy()->day(9)->endOfDay()->toDateTimeString();
        // } else {
        //     // If on or after the 10th
        //     $startDate = $today->copy()->day(10)->startOfDay()->toDateTimeString();
        //     $endDate = $today->copy()->addMonth()->day(9)->endOfDay()->toDateTimeString();
        // }

        // $auditAllocations = AuditAllocation::whereBetween('created_at', [$startDate, $endDate])
        // ->where('status', 1)
        // ->orderBy('created_at', 'desc')
        // ->get();


        // Step 1: Fetch distinct periods from the database
        $periods = AuditAllocation::select('process_review_period')
        ->distinct()->where('client_id',auth()->user()->client_id)
        ->pluck('process_review_period');

        // Step 2: Convert periods to a comparable format
        $latestPeriod = $periods
        ->map(function ($period) {
            // Ensure the format matches "Oct'24" (M'y)
            return Carbon::createFromFormat("M\\'y", $period);
        })
        ->sort() 
        ->last(); 

        // Step 3: Calculate the date range
        $today = Carbon::now();
        if ($today->day < 10) {
            // If before the 10th, use the previous month's range
            $startDate = $today->copy()->subMonth()->day(10)->startOfDay()->toDateTimeString();
            $endDate = $today->copy()->day(9)->endOfDay()->toDateTimeString();
        } else {
            // If on or after the 10th, use the current month's range
            $startDate = $today->copy()->day(10)->startOfDay()->toDateTimeString();
            $endDate = $today->copy()->addMonth()->day(9)->endOfDay()->toDateTimeString();
        }

        if ($latestPeriod) {
            $latestPeriodString = $latestPeriod->format("M'y");

            $auditAllocations = AuditAllocation::where('process_review_period', $latestPeriodString)
                ->where('audit_allocation.status', 1)
                ->where('client_id', auth()->user()->client_id)
                // ->whereBetween('audit_allocation.created_at', [$startDate, $endDate])
                ->orderBy('audit_allocation.created_at', 'desc')
                ->get();
        } else {
            $auditAllocations = collect();  // Empty collection
        }

       //echo "<pre>"; print_r($auditAllocations); exit();

        return view('audit_allocation.list', compact('auditAllocations'));
    }

    
    //V Bulk Allocation Uplaod Page Page Show
    public function showBulkAllocation()
    {
        return view('audit_allocation.aaupload');
    }


      
    //V Audit Allocation Bulk Upload Excel Data with Email single okay 
    public function auditAllocationImport(Request $request)
    {
        $userId = auth()->id();
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv',
        ]);

        try {
            $import = new AuditAllocationImport($userId);
            Excel::import($import, $request->file('file'));

            $today = Carbon::today();
            $auditAllocations = AuditAllocation::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take(100)
            ->get();

            $agencyEmails = $auditAllocations->pluck('process_review_agency_email')->unique();
           
            //echo "<pre>"; print_r($auditAllocations); die();    

            $sentEmails = [];

            foreach ($agencyEmails as $agencyEmail) {
                $emails = array_map('trim', explode(',', $agencyEmail));

                foreach ($emails as $email) {
                    if (filter_var($email, FILTER_VALIDATE_EMAIL) && !in_array($email, $sentEmails)) {
                        // Send email only if it hasn't been sent yet
                        Mail::to($email)->send(new AuditAllocationImported($userId, $email, $auditAllocations));
                        $sentEmails[] = $email;
                    }
                }
            }

            return redirect()->route('audit_allocation.index')->with('success', 'Audit Allocation imported successfully.');

        } catch (ValidationException $e) {
            $errors = $e->errors();
            
            // Check if the keys exist before trying to access them
            $errorMessage = '';
            if (isset($errors['agency_code'])) {
                $errorMessage .= $errors['agency_code'][0] . ' ';
            }
            if (isset($errors['product'])) {
                $errorMessage .= $errors['product'][0] . ' ';
            }
            if (isset($errors['sub_product'])) {
                $errorMessage .= $errors['sub_product'][0] . ' ';
            }
            if (isset($errors['final_agency_name'])) {
                $errorMessage .= $errors['final_agency_name'][0] . ' ';
            }

            
            return redirect()->route('audit_allocation.index')->with('error', trim($errorMessage));

        } catch (\Maatwebsite\Excel\ExcelException $e) {
            return redirect()->route('audit_allocation.index')->with('error', 'Error importing Audit Allocation: ' . $e->getMessage());

        } catch (\Exception $e) {
            return redirect()->route('audit_allocation.index')->with('error', 'Error importing Audit Allocation: ' . $e->getMessage());
        }
    }










    // V Download Sample Excel for Audit Allocation Upload
    public function aaDownloadsampleExcel()
    {
        //echo "Hello"; die();
        $file= public_path(). "/download/audit-allocation-sample.xlsx";
        return response()->download($file, 'audit-allocation-sample.xlsx');
    }


    
    
    public function show($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return redirect()->route('audit_allocation.index')->with('error', 'Invalid ID');
        }

        $data = AuditAllocation::find($decryptedId);
        
        if (!$data) {
            return redirect()->route('audit_allocation.index')->with('error', 'Data not found');
        }

        return view('audit_allocation.show', compact('data'));
    }

   

    // public function destroy($id)
    // {
    //     try {
    //         $decryptedId = Crypt::decrypt($id);
    //         $record = AuditAllocation::findOrFail($decryptedId);
    //         $record->delete();

    //         return redirect()->route('audit_allocation.index')->with('success', 'Audit allocation deleted successfully.');
    //     } catch (DecryptException $e) {
    //         return redirect()->route('audit_allocation.index')->with('error', 'Invalid ID');
    //     }
    // }


    
    public function destroy($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            // Find the record in the AuditAllocation table
            $record = AuditAllocation::findOrFail($decryptedId);

           // echo "<pre>"; print_r( $record); die;
             //echo '<pre>'; print_r( $record); die;
            // Extract the fields for matching
            $agencyId = $record->agency_id;
            $agencyCode = $record->agency_code;
            $location = $record->location;
            $productId = $record->product_id;
            $audit_cycle_id = $record->audit_cycle_id;

            
            // $process_review_period = DB::table('audit_cycles')->where('name',$record->process_review_period)->pluck('id')->first();


            // Check if any record exists in the audits table with the same details
            $auditExists = Audit::where('agency_id', $agencyId)
               // ->where('agency_code', $agencyCode)
                ->where('agency_location', $location)
                ->where('product_id', $productId)
               ->where('audit_cycle_id', $audit_cycle_id)
                ->exists();

               // echo '<pre>'; print_r($auditExists); die;
            if ($auditExists) {
                // Redirect back with an error if an audit has been performed
                return redirect()->route('audit_allocation.index')->with('error', 'An audit has been performed on this allocation. You cannot delete it.');
            }

            // Find matching records in the AuditorAssign table
            $matchingRecords = AuditorAssign::where('agency_id', $agencyId)
                ->where('agency_code', $agencyCode)
                ->where('location', $location)
                ->where('product_id', $productId)
                ->where('audit_cycle_id', $audit_cycle_id)
                ->get();

                //echo '<pre>'; print_r($matchingRecords); die;
            // Delete matching records from AuditorAssign
            foreach ($matchingRecords as $matchingRecord) {
                $matchingRecord->delete();
            }

            // Agency::where('id',$agencyId)->delete();
            // Delete the record from AuditAllocation
            $record->delete();

            return redirect()->back()->with('success', 'Audit allocation and matching assignments deleted successfully.');
        } catch (DecryptException $e) {
            return redirect()->back()->with('error', 'Invalid ID');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }


    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids'); // Get IDs from the request
    
        if (!empty($ids)) {
            try {
                // Fetch the records from AuditAllocation
                $allocations = AuditAllocation::whereIn('id', $ids)->get();
    
                $errorAllocations = []; // To keep track of records that can't be deleted
                $deletableAllocations = []; // To keep track of records that can be deleted
    
                foreach ($allocations as $allocation) {
                    // Extract fields for matching
                    $agencyId = $allocation->agency_id;
                    $agencyCode = $allocation->agency_code;
                    $location = $allocation->location;
                    $productId = $allocation->product_id;
                    $auditCycleId = $allocation->audit_cycle_id;
    
                    // Check if an audit exists for the allocation
                    $auditExists = Audit::where('agency_id', $agencyId)
                        ->where('agency_location', $location)
                        ->where('product_id', $productId)
                        ->where('audit_cycle_id', $auditCycleId)
                        ->exists();
    
                    if ($auditExists) {
                        // Add to error list if an audit exists
                        $errorAllocations[] = $allocation->id;
                    } else {
                        // Add to deletable list if no audit exists+
                        $deletableAllocations[] = $allocation;
                    }
                }
    
                // Perform deletions for the records that can be safely deleted
                if (!empty($deletableAllocations)) {
                    $deletableIds = collect($deletableAllocations)->pluck('id');
                    $agencyIds = collect($deletableAllocations)->pluck('agency_id');
    
                    // Delete related records from AuditorAssign
                    AuditorAssign::whereIn('agency_id', $agencyIds)
                        ->whereIn('product_id', collect($deletableAllocations)->pluck('product_id'))
                        ->delete();
    
                    // Delete related records from Agency (if necessary)
                    Agency::whereIn('id', $agencyIds)->delete();
    
                    // Delete from AuditAllocation
                    AuditAllocation::whereIn('id', $deletableIds)->delete();
                }
    
                // Return response based on results
                if (!empty($errorAllocations)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Some allocations could not be deleted as audits have been performed on them.',
                        'errors' => $errorAllocations,
                    ]);
                }
    
                return response()->json([
                    'success' => true,
                    'message' => 'Selected audit allocations deleted successfully.',
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred: ' . $e->getMessage(),
                ]);
            }
        }
    
        return response()->json([
            'success' => false,
            'message' => 'No IDs provided for deletion.',
        ]);
    }
    
}
