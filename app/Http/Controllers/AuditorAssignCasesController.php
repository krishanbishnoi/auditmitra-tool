<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Response;
use Illuminate\Support\Facades\Crypt;

use App\Model\AuditorAssign;
use App\Model\AuditAllocation;

use Illuminate\Support\Facades\Mail;
use App\Mail\AuditorAssignForAudit;

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AuditorAssignImport;

use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Auth;

class AuditorAssignCasesController extends Controller
{
    

    public function index()
    {
        //V old Query
        // $authEmail = Auth::user()->email;
        // $auditorAssignCases = AuditorAssign::join('users', 'users.email', '=', 'auditor_assigns.auditor_email')
        // ->where('auditor_assigns.auditor_email', $authEmail)
        // ->select('auditor_assigns.*', 'users.name as user_name')
        // ->get();





        // V New Query show data by latest process_review_period
        // Step 1: Fetch distinct periods from the database
        $periods = AuditorAssign::select('process_review_period')->where('client_id', auth()->user()->client_id)
        ->distinct()
        ->pluck('process_review_period');
// dd($periods);
        // Step 2: Convert periods to a comparable format
        $latestPeriod = $periods
            ->map(function ($period) {
                // Ensure the format matches "Oct'24" (M'y)
                return Carbon::createFromFormat("M\\'y", $period);
            })
            ->sort() // Sort the periods
            ->last(); // Get the highest (latest) period
// dd($latestPeriod);
        // Step 3: Check if a valid period was found
        if ($latestPeriod) {
            // Convert the latest period back to the original format
            $latestPeriodString = $latestPeriod->format("M'y");


            $authEmail = Auth::user()->email;

            //Month filter
            $startDate = $latestPeriod->copy()->startOfMonth()->day(10)->toDateString();
            $endDate = $latestPeriod->copy()->addMonth()->startOfMonth()->day(9)->toDateString();
                       

            // Step 4: Query for the latest period
            $auditorAssignCases = AuditorAssign::join('users', 'users.email', '=', 'auditor_assigns.auditor_email')
            ->select('auditor_assigns.*', 'users.name as user_name')
            ->where('auditor_assigns.process_review_period', $latestPeriodString)
            ->where('auditor_assigns.auditor_email', $authEmail)
            // ->whereBetween('auditor_assigns.created_at', [$startDate, $endDate])
            ->get();

        } else {
            // Handle the case when no valid process review periods are found
            // You can return an empty collection or a message
            $auditorAssignCases = collect();  // Empty collectiondd(1)
            // Log::info('No process review periods found.');
        }
// dd($auditorAssignCases);
        // echo "<pre>"; print_r($auditorAssignCases); die();

        return view('auditor_assign_cases.list', compact('auditorAssignCases'));
    }

    


    

    

    
    // V Send Email to Auditors and Collection Agency Multiple
    public function auditorAssignImport(Request $request)
    {
        $userId = auth()->id();
        //$authEmail = Auth::user()->email;
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv',
        ]);

        try {
            $import = new AuditorAssignImport($userId);
            Excel::import($import, $request->file('file'));

        
            $today = Carbon::today();
            $auditorAssigns = AuditorAssign::where('user_id', $userId)
                ->whereDate('created_at', $today)
                ->get();

            // V Get unique auditor and agency emails
            $auditorEmails = $auditorAssigns->pluck('auditor_email')->unique();
            $agencyEmails = $auditorAssigns->pluck('agency_email')->unique();

            $sentEmails = [];

            // Function to send emails
            $sendEmails = function($emails, $userId, $auditorAssigns) use (&$sentEmails) {
                foreach ($emails as $email) {
                    $trimmedEmails = array_map('trim', explode(',', $email));

                    foreach ($trimmedEmails as $email) {
                        if (filter_var($email, FILTER_VALIDATE_EMAIL) && !in_array($email, $sentEmails)) {
                            // Send email only if it hasn't been sent yet
                            Mail::to($email)->send(new AuditorAssignForAudit($userId, $email, $auditorAssigns));
                            $sentEmails[] = $email;
                        }
                    }
                }
            };

            // V Send emails to both auditors and agencies
            $sendEmails($auditorEmails, $userId, $auditorAssigns);
            $sendEmails($agencyEmails, $userId, $auditorAssigns);

            return redirect()->route('auditor_assign.index')->with('success', 'Auditor assign imported successfully.');

        } catch (\Maatwebsite\Excel\ExcelException $e) {
            Log::error('Excel import error: ' . $e->getMessage());
            return redirect()->route('auditor_assign.index')->with('error', 'Error importing Auditor assign: ' . $e->getMessage());

        } catch (\Exception $e) {
            Log::error('General error: ' . $e->getMessage());
            return redirect()->route('auditor_assign.index')->with('error', 'Error importing Auditor assign: ' . $e->getMessage());
        }
    }










    public function show($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return redirect()->route('auditor_assign_cases.index')->with('error', 'Invalid ID');
        }

        $data = AuditorAssign::find($decryptedId);
        
        if (!$data) {
            return redirect()->route('auditor_assign_cases.index')->with('error', 'Data not found');
        }

        return view('auditor_assign_cases.show', compact('data'));
    }




    public function edit($id)
    {        
        //$data = AuditorAssign::find($id);
        $data=AuditorAssign::find(Crypt::decrypt($id));
        if (!$data) {
            return redirect()->route('auditor_assign.index')->with('error', 'Data not found');
        }
        return view('auditor_assign.edit', compact('data'));
    }


    public function update(Request $request, $id)
    {
        
        $validator = Validator::make($request->all(), [
            'final_agency_name' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', [$validator->errors()->all()])->withInput();
        } else {

            $yard=AuditorAssign::where('id',Crypt::decrypt($id))->update(
                [
                    'final_agency_name'     =>$request->final_agency_name,
                    'type_of_agency'        =>$request->type_of_agency,
                    'sub_product'           =>$request->sub_product,
                    'product'               =>$request->product,
                    'location'              =>$request->location,
                    'state'                 =>$request->state,
                    'region'                =>$request->region,
                    'process_review_agency' =>$request->process_review_agency,
                    'process_review_agency_id' =>$request->process_review_agency_id,
                    'process_review_period' =>$request->process_review_period,
                    'agency_address'        =>$request->agency_address,
                    'contact'               =>$request->contact,
                    'agency_email'          =>$request->agency_email,
                    'auditor_name'          =>$request->auditor_name,
                    'auditor_email'         =>$request->auditor_email,
                ]
            );
        if($yard){
              return redirect('auditor_assign')->with('success', ['Auditor assign updated successfully.']);
        }
            else{

                return redirect()->back()->with('error', ['Auditor assign updated unsuccessfully.']);
            }
        }

    }




    // public function destroy($id)
    // {
    //     try {
    //         $decryptedId = Crypt::decrypt($id);
    //         $record = AuditorAssign::findOrFail($decryptedId);
    //         $record->delete();

    //         return redirect()->route('auditor_assign.index')->with('success', 'Auditor assign deleted successfully.');
    //     } catch (DecryptException $e) {
    //         return redirect()->route('auditor_assign.index')->with('error', 'Invalid ID');
    //     }
    // }


}
