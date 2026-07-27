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

use App\Model\AuditAllocation;

use Illuminate\Support\Facades\Mail;
use App\Mail\IntimationMailCreated;

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AuditAllocationImport;

use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

use App\Exports\AuditAllocationAssignExport;


class AuditAllocationAssignController extends Controller
{
    
    public function index()
    {
                
        // Step 1: Fetch distinct periods from the database
        $periods = AuditAllocation::select('process_review_period')
        ->distinct()->where('client_id', auth()->user()->client_id)
        ->pluck('process_review_period');

        // Step 2: Convert periods to a comparable format
        $latestPeriod = $periods
        ->map(function ($period) {
            // Ensure the format matches "Oct'24" (M'y)
            return Carbon::createFromFormat("M\\'y", $period);
        })
        ->sort() 
        ->last(); 


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
            $authEmail = Auth::user()->email;
            $auditAllocationAssign = AuditAllocation::join('users', 'users.email', '=', 'audit_allocation.process_review_agency_email')
            ->where('audit_allocation.process_review_agency_email', $authEmail)
            ->where('process_review_period', $latestPeriodString)
            ->where('audit_allocation.status', 1)
            // ->whereBetween('audit_allocation.created_at', [$startDate, $endDate])
            ->select('audit_allocation.*', 'users.name as user_name')
            ->get();
        } else {
            $auditAllocationAssign = collect();
        }

        //echo "<pre>"; print_r($auditAllocationAssign); die();
         $finalagency=AuditAllocation::get(['id', 'final_agency_name']);
        
        return view('audit_allocation_assign.list', compact('auditAllocationAssign','finalagency'));
    }

    
    // V Audit Allocation Assign Export Excel Data Download
    // public function auditAllocationAssignExport(Request $request)
    // {

    //     $userId = auth()->id();
    //     return Excel::download(new AuditAllocationAssignExport($userId), 'audit_allocation_assign.xlsx');
    // }

    public function auditAllocationAssignExport()
    {
        //$userId = 1123;
        $authEmail = Auth::user()->email;

        $cycle = DB::table('audit_cycles')->where('status',1)->where('client_id', auth()->user()->client_id)->value('name');

        //echo "<pre>"; print_r($authEmail); die();

        return Excel::download(new AuditAllocationAssignExport($authEmail, $cycle), 'audit_allocations.xlsx');
    }

    
    
    
    public function show($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return redirect()->route('audit_allocation_assign.index')->with('error', 'Invalid ID');
        }

        $data = AuditAllocation::find($decryptedId);
        
        if (!$data) {
            return redirect()->route('audit_allocation_assign.index')->with('error', 'Data not found');
        }

        return view('audit_allocation_assign.show', compact('data'));
    }




    // public function destroy($id)
    // {
    //     try {
    //         $decryptedId = Crypt::decrypt($id);
    //         $record = AuditAllocation::findOrFail($decryptedId);
    //         $record->delete();

    //         return redirect()->route('audit_allocation_assign.index')->with('success', 'Auditor allocation assign deleted successfully.');
    //     } catch (DecryptException $e) {
    //         return redirect()->route('audit_allocation_assign.index')->with('error', 'Invalid ID');
    //     }
    // }

   

   


}
