<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Response;
use Illuminate\Support\Facades\Crypt;

use App\Model\AuditorAssign;
use App\Audit;
use Illuminate\Support\Facades\Mail;
use App\Mail\AuditorAssignForAudit;

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AuditorAssignImport;

use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Auth;

class AuditorAssignController extends Controller
{
    public function index()
    {        
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
         // Step 1: Fetch distinct periods from the database
        $periods = AuditorAssign::select('process_review_period')
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
        $latestPeriodString = $latestPeriod->format("M'y");

        $auditorAssign = AuditorAssign::join('users', 'auditor_assigns.user_id', '=', 'users.id')
        ->where('auditor_assigns.process_review_period',$latestPeriodString)
        ->where('auditor_assigns.user_id',Auth::user()->id)
        ->select('auditor_assigns.*', 'users.name as user_name')
        // ->whereBetween('auditor_assigns.created_at', [$startDate, $endDate])
        ->get();
        
        // echo '<pre>'; print_r($auditorAssign); die;
        return view('auditor_assign.list', compact('auditorAssign'));
    }

    //V Bulk Uplaod Page Show For Auditor Assign Audit
    public function auditorBulkUpload()
    {
        return view('auditor_assign.auditor_assign_bulkupload');
    }


    // V Send Email to Auditors and Collection Agency Multiple
    // public function auditorAssignImport(Request $request)
    // {
    //     $userId = auth()->id();
    //     $authEmail = Auth::user()->email;

    //     $request->validate([
    //         'file' => 'required|file|mimes:xlsx,csv',
    //     ]);

    //     try {
           
    //         $import = new AuditorAssignImport($userId);
    //         Excel::import($import, $request->file('file'));

           
    //         $auditorAssigns = AuditorAssign::where('user_id', $userId)
    //             ->orderBy('created_at', 'desc')
    //             ->take(100)
    //             ->get();

          
    //         $agencyEmails = $auditorAssigns->pluck('auditor_email')->unique();

    //         $sentEmails = [];

          
    //         foreach ($agencyEmails as $agencyEmail) {
    //             $emails = array_map('trim', explode(',', $agencyEmail));

    //             foreach ($emails as $email) {
    //                 if (filter_var($email, FILTER_VALIDATE_EMAIL) && !in_array($email, $sentEmails)) {
                       
    //                     Mail::to($email)->send(new AuditorAssignForAudit($userId, $email, $auditorAssigns));
    //                     $sentEmails[] = $email;
    //                 }
    //             }
    //         }

    //         return redirect()->route('auditor_assign.index')->with('success', 'Auditor assignment imported successfully.');

    //     } catch (\Maatwebsite\Excel\ExcelException $e) {
    //         Log::error('Excel import error: ' . $e->getMessage());
    //         return redirect()->route('auditor_assign.index')->with('error', 'Error importing Auditor assignment: ' . $e->getMessage());

    //     } catch (\Exception $e) {
    //         Log::error('General error: ' . $e->getMessage());
    //         return redirect()->route('auditor_assign.index')->with('error', 'Error importing Auditor assignment: ' . $e->getMessage());
    //     }
    // }


    public function auditorAssignImport(Request $request)
    {
        $userId = auth()->id();
        $authEmail = Auth::user()->email;

        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv',
        ]);

        try {
            $import = new AuditorAssignImport($userId);
            Excel::import($import, $request->file('file'));
            $auditorAssigns = AuditorAssign::where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->take(100)
                ->get();

            $agencyEmails = $auditorAssigns->pluck('auditor_email')->unique();
            $sentEmails = [];

            foreach ($agencyEmails as $agencyEmail) {
                $emails = array_map('trim', explode(',', $agencyEmail));
                foreach ($emails as $email) {
                    if (filter_var($email, FILTER_VALIDATE_EMAIL) && !in_array($email, $sentEmails)) {
                        Mail::to($email)->send(new AuditorAssignForAudit($userId, $email, $auditorAssigns));
                        $sentEmails[] = $email;
                    }
                }
            }
            return redirect()->route('auditor_assign.index')->with('success', 'Auditor assignment imported successfully.');

        } catch (ValidationException $e) {
            $errors = $e->errors();
            // Construct a user-friendly error message
            $errorMessage = '';
            if (isset($errors['agency_code'])) {
                $errorMessage .= $errors['agency_code'][0] . ' ';
            }
            if (isset($errors['product'])) {
                $errorMessage .= $errors['product'][0] . ' ';
            }
            if (isset($errors['final_agency_name'])) {
                $errorMessage .= $errors['final_agency_name'][0] . ' ';
            }
            if (isset($errors['auditor_email'])) {
                $errorMessage .= $errors['auditor_email'][0] . ' ';
            }
            if (isset($errors['audit_date'])) {
                $errorMessage .= $errors['audit_date'][0] . ' ';
            }
            // Redirect with an error message
            return redirect()->route('auditor_assign.index')->with('error', trim($errorMessage));

        } catch (\Maatwebsite\Excel\ExcelException $e) {
            Log::error('Excel import error: ' . $e->getMessage());
            return redirect()->route('auditor_assign.index')->with('error', 'Error importing Auditor assignment: ' . $e->getMessage());

        } catch (\Exception $e) {
            Log::error('General error: ' . $e->getMessage());
            return redirect()->route('auditor_assign.index')->with('error', 'Error importing Auditor assignment: ' . $e->getMessage());
        }
    }



    public function show($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return redirect()->route('auditor_assign.index')->with('error', 'Invalid ID');
        }
        $data = AuditorAssign::find($decryptedId);        
        if (!$data) {
            return redirect()->route('auditor_assign.index')->with('error', 'Data not found');
        }
        return view('auditor_assign.show', compact('data'));
    }


    public function edit($id)
    {
        //$data = AuditorAssign::find($id);
        $data=AuditorAssign::find(Crypt::decrypt($id));
        if (!$data) {
            return redirect()->route('auditor_assign.index')->with('error', 'Data not found');
        }

        // V Check Audit Already Performed in db
        $auditExists = Audit::where('agency_id', $data->agency_id)
        ->where('audit_cycle_id', $data->audit_cycle_id)
        ->where('product_id', $data->product_id)
        ->exists();

        //echo '<pre>'; print_r($auditExists); die();

        return view('auditor_assign.edit', compact('data','auditExists'));
    }


    public function update(Request $request, $id)
    {        
        $validator = Validator::make($request->all(), [
            //'final_agency_name' => 'required',
            'auditor_email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', [$validator->errors()->all()])->withInput();
        } else {

            $userId = Auth::user()->id;
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
                    'process_review_agency_id' =>$userId,
                    'process_review_period' =>$request->process_review_period,
                    'agency_address'        =>$request->agency_address,
                    'contact'               =>$request->contact,
                    'agency_email'          =>$request->agency_email,
                    'auditor_name'          =>$request->auditor_name,
                    'auditor_email'         =>$request->auditor_email,
                    'audit_date'            =>$request->audit_date,
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




    public function destroy($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $record = AuditorAssign::findOrFail($decryptedId);
            $record->delete();

            return redirect()->route('auditor_assign.index')->with('success', 'Auditor assign deleted successfully.');
        } catch (DecryptException $e) {
            return redirect()->route('auditor_assign.index')->with('error', 'Invalid ID');
        }
    }


}
