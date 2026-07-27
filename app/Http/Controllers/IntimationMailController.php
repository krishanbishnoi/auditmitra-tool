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
use App\Model\IntimationUser;
use Illuminate\Support\Facades\Mail;
use App\Mail\IntimationMailCreated;

use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class IntimationMailController extends Controller
{

    public function index()
    {

        // $today = Carbon::now();
        // if ($today->day < 10) {
        //     // If before the 10th, use the previous month's range
        //     $startDate = $today->copy()->subMonth()->day(10)->startOfDay()->toDateTimeString();
        //     $endDate = $today->copy()->day(9)->endOfDay()->toDateTimeString();
        // } else {
        //     // If on or after the 10th, use the current month's range
        //     $startDate = $today->copy()->day(10)->startOfDay()->toDateTimeString();
        //     $endDate = $today->copy()->addMonth()->day(9)->endOfDay()->toDateTimeString();
        // }

        $intimationmail = IntimationMail::with('agency', 'product', 'productattribute')
            ->where("user_id", Auth::user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $product = Products::get(['id', 'name']);
        $agency = Agency::get(['id', 'name']);
        $users = User::get(['id', 'name']);

        return view('intimation_mail.list', compact('intimationmail', 'agency', 'product', 'users'));
    }



    public function create()
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

        $userRole = auth()->user()->roles->first();
        $authUserEmail = auth()->user()->email; // Get the authenticated user's email
        // $branch=Branch::get(['id', 'name']);
        // added by nisha for collection manager list
        $qa_list = User::role('Quality Auditor')
            ->where("active_status", 0)
            ->where("is_approved", 1)
            ->where("created_by", $authUserEmail) // Filter by created_by field
            ->get();
        $products = Products::get(['id', 'name']);
        //  $agency = Agency::get(['id', 'name', 'email','agency_id','location']);
        $users = User::get(['id', 'name']);

        $cycle = DB::table('audit_cycles')->where('status', 1)->orderBy('id', 'desc')->where('client_id', auth()->user()->client_id)->get();
        $current_cycle = DB::table('audit_cycles')->where('status', 1)->orderBy('id', 'desc')->where('client_id', auth()->user()->client_id)->value('name');
        if ($user_role = Auth::user()->roles()->first()->name == 'Admin') {
            $assign_agency_ids = DB::table('audit_allocation')->where('process_review_agency_email', Auth::user()->email)->where('process_review_period', $current_cycle)->pluck('agency_id')->toArray();
            $agency = Agency::orderBy('name', 'ASC')
                ->whereIn('id', $assign_agency_ids)
                // ->whereBetween('created_at', [$startDate, $endDate])
                ->get();
        }

        $now = Carbon::now();

        // Calculate the last three months excluding the current month
        $cycle = DB::table('audit_cycles')->where('status', 1)->orderBy('id', 'desc')->where('client_id', auth()->user()->client_id)->get();
        $months = [];
        // for ($i = 1; $i <= 3; $i++) {
        //     $date = $now->subMonth()->startOfMonth(); // Move one month back
        //     $months[] = [
        //         'value' => $date->format('M-Y'), // Format as Jan 2024 for display
        //         'label' => $date->format('M-Y')   // Format as Jan 2024 for display
        //     ];
        // }


        $formattedUsers = [];
        $Level_5 = [];

        $usersData = User::with('roles')
            ->where('users.active_status', 0)
            ->where('client_id', auth()->user()->client_id)
            ->whereDoesntHave('roles', function ($query) {
                $query->whereIn('name', ['Admin', 'Client', 'Quality Auditor']);
            })
            ->get()
            ->toArray();



        if (!empty($usersData)) {
            foreach ($usersData as $user) {
                if (!empty($user['roles'])) {
                    $roleNamesArray = array_column($user['roles'], 'name');
                    $roleNames = implode(', ', $roleNamesArray);

                    // Define Level 5 roles
                    $level5Roles = [
                        'National Collection Manager',
                        'Group Product Head',
                        'Head - Credit Card Collection',
                        'Head - Tele-Calling - Credit Card Collection',
                        'Head - Credit Card Collection - RBL Supercard',
                        'Head of the Collections',
                        'Internal',
                    ];

                    // Check for Level 5 roles
                    if (array_intersect($level5Roles, $roleNamesArray)) {
                        $Level_5[$user['id']] = $user['name'] . ' - ' . $user['employee_id'] . ' - ' . $roleNames;
                    } else {
                        // Check if user should be excluded from Level 3 and Level 4
                        if (
                            !in_array('National Collection Manager', $roleNamesArray) &&
                            !in_array('Group Product Head', $roleNamesArray) &&
                            !in_array('Head - Credit Card Collection', $roleNamesArray) &&
                            !in_array('Head - Tele-Calling - Credit Card Collection', $roleNamesArray) &&
                            !in_array('Head - Credit Card Collection - RBL Supercard', $roleNamesArray) &&
                            !in_array('Head of the Collections', $roleNamesArray)
                        ) {

                            $formattedUsers[$user['id']] = $user['name'] . ' - ' . $user['employee_id'] . ' - ' . $roleNames;
                        }
                    }
                } else {
                    $formattedUsers[$user['id']] = $user['name'] . ' - ' . $user['employee_id'] . ' - No Role';
                }
            }
        } else {
            $formattedUsers = [];
        }
        return view('intimation_mail.create', compact('formattedUsers', 'qa_list', 'products', 'agency', 'users', 'months', 'Level_5', 'cycle'));
    }
    public function getProductAttributes($id)
    {
        $attributes = ProductAttribute::where('product_id', $id)->get();
        return response()->json($attributes);
        //echo '<pre>'; print_r($attributes); exit();
    }

    public function viewMail($id)
    {

        $intimationDetails = IntimationMail::with(['user', 'agency', 'product', 'auditorUser'])
            ->findOrFail($id);
        $intimationUsers = DB::table('intimation_users')->where('intimation_mail_id', $id)->get();

        $level3Users = collect();
        $level4Users = collect();
        $level5Users = collect();

        foreach ($intimationUsers as $intimationUser) {

            $level3Users = $level3Users->merge(
                User::whereIn('id', explode(',', $intimationUser->level_3))->get()
            );


            $level4Users = $level4Users->merge(
                User::whereIn('id', explode(',', $intimationUser->level_4))->get()
            );


            $level5Users = $level5Users->merge(
                User::whereIn('id', explode(',', $intimationUser->level_5))->get()
            );
        }

        return view('intimation_mail.view', compact('intimationDetails', 'level3Users', 'level4Users', 'level5Users'));
    }

    public function store(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'agency' => 'required',
            // add other validation rules as needed
        ]);
        $agency = DB::table('agencies')->where('id', $request->agency)->first();
        $agency_name = DB::table('agencies')->where('id', $request->agency)->value('name');
        $agency_location = DB::table('agencies')->where('id', $request->agency)->value('location');
        // dd($agency_name);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Get logged-in user ID
        $userId = Auth::user()->id;

        $process_review_month = DB::table('audit_cycles')->where('id', $request->input('process_review_month'))->pluck('name')->first();
        // Save the intimation mail data
        $data = new IntimationMail;
        $data->name = $agency_name;
        $data->user_id = $userId;

        $data->process_review_month =  $process_review_month;
        $data->audit_date = $request->input('audit_date');
        $data->agency = $request->input('agency');
        $data->agency_email = isset($request['agency_email']) ? implode(',', $request['agency_email']) : null;
        $data->product_id = $request->input('product_id');
        $data->product_attribute_id = $request->input('product_attribute_id');
        $data->auditor = $request->input('auditor');
        if ($request->filled('auditor_name')) {
            $data->auditor_name = $request->auditor_name;
        }
        $data->executives = $request->filled('executives')
            ? json_encode($request->executives)
            : null;
        $data->description = $request->input('description');
        $data->client_id = auth()->user()->client_id;
        $data->save();
        $data->location = $agency_location;
        $data->mode = $request->input('mode');
        $data->mode = $request->input('mode');
        $data->agency_spoc = $request->input('agency_spoc_name');
        $data->client_spoc = $request->input('client_spoc_name');
        $data->agency_spoc_number = $request->input('agency_spoc_number');
        // Save each set of level_3, level_4, level_5 in the `intimation_users` table
        foreach ($request->input('levels') as $level) {
            $intimationUser = new IntimationUser;
            $intimationUser->intimation_mail_id = $data->id;
            $intimationUser->level_3 = $level['level_3'] ?? null;
            $intimationUser->level_4 = $level['level_4'] ?? null;
            $intimationUser->level_5 = $level['level_5'] ?? null;
            $intimationUser->save();
        }

        // Prepare the email addresses for level_3, level_4, and level_5
        $level3Emails = User::whereIn('id', array_column($request->input('levels'), 'level_3'))->pluck('email')->toArray();
        $level4Emails = User::whereIn('id', array_column($request->input('levels'), 'level_4'))->pluck('email')->toArray();
        $level5Emails = User::whereIn('id', array_column($request->input('levels'), 'level_5'))->pluck('email')->toArray();
        $agencyEmails = is_array($request['agency_email']) ? $request['agency_email'] : [$request['agency_email']];

        // Define the RBL Bank email
        // $rbl_bank_email = 'ravi.prajapat@qdegrees.org';
        $add_email = isset($request->additional_email) ? $request->additional_email : '';
        $zone = [];
        if (auth()->user()->client_id == 15) {
            $additional_email = ['anup.tiwari@moneyview.in', 'mohammed.danish@moneyview.in', 'manvi.ojha@moneyview.in', 'abhishek.gupta@qdegrees.com', 'aditya.diwakar@qdegrees.com', 'pawan.saini@qdegrees.com', 'arjun.verma@qdegrees.com', 'vaibhav.amer@qdegrees.com'];

            if ($agency->region_id == 1) {
                $zone[] = 'pradeep.singh@qdegrees.com';
            } elseif ($agency->region_id == 2) {
                $zone[] = 'junaid.kashif@qdegrees.com';
            } elseif ($agency->region_id == 3) {
                $zone[] = 'rohit.waghmare@qdegrees.com';
            } elseif ($agency->region_id == 4) {
                $zone[] = 'sreedhar.gajula@qdegrees.com';
            }
        } elseif (auth()->user()->client_id == 74) {
            // $additional_email = [];
            $additional_email = ['Snehal.Nimkar@fibe.in', 'piyali.banerjee@fibe.in', 'Pradip.kumar@fibe.in', 'abhishek.gupta@qdegrees.com', 'aditya.diwakar@qdegrees.com', 'joicy.kunjuman@qdegrees.com', 'Vaibhav.Amer@qdegrees.com'];
            if ($agency->region_id == 1) {
                $zone[] = 'pradeep.singh@qdegrees.com';
            } elseif ($agency->region_id == 2) {
                $zone = ['abir.sengupta@fibe.in', 'ashok.nowdu@fibe.in', 'bharath.v@fibe.in', 'junaid.kashif@qdegrees.com'];
            } elseif ($agency->region_id == 3) {
                // $zone [] = 'rohit.waghmare@qdegrees.com';
                $zone = [];
            } elseif ($agency->region_id == 4) {
                $zone = ['abir.sengupta@fibe.in', 'ashok.nowdu@fibe.in', 'bharath.v@fibe.in', 'sreedhar.gajula@qdegrees.com'];
            }
        } elseif (auth()->user()->client_id == 249) {
            $additional_email = ['aditya.diwakar@qdegrees.com', 'Sakshi.Bhargava@qdegrees.com'];
        } elseif (auth()->user()->client_id == 285) {
            $additional_email = ['sachin.basutkar@poonawallafincorp.com' , 'abhishek.gupta@qdegrees.com', 'aditya.diwakar@qdegrees.com', 'Prerna.Sharma@qdegrees.com'];

            if ($agency->region_id == 1) {
                $zone[] = 'pradeep.singh@qdegrees.com';
            } elseif ($agency->region_id == 2) {
                $zone[] = 'junaid.kashif@qdegrees.com';
            } elseif ($agency->region_id == 3) {
                $zone[] = 'rohit.waghmare@qdegrees.com';
            } elseif ($agency->region_id == 4) {
                $zone[] = 'sreedhar.gajula@qdegrees.com';
            }
        } else {
            $additional_email = [];
        }
        // Merge all level emails for CC, including the RBL Bank email
        $ccEmails_list = array_merge($level5Emails, $level4Emails, $level3Emails,  $additional_email, $zone, [$add_email]);
        // Remove empty string values from the $ccEmails array
        $newccEmails = array_filter($ccEmails_list, fn($email) => !empty($email));

        // Re-index the array (optional, to re-order the keys)
        $ccEmails = array_values($newccEmails);

        // Output the modified array (for debugging purposes)
        // dd($newccEmails);
        // dd($ccEmails);
        // Prepare the email data and send the email
        try {
            Mail::to($agencyEmails)
                ->cc($ccEmails)
                ->send(new IntimationMailCreated($data));  // Passing the IntimationMail model instance

            return redirect('intimation_mail')->with('success', 'Intimation Mail created and email sent successfully');
        } catch (\Exception $e) {
            // Log the error and return a response
            Log::error('Error sending email: ' . $e->getMessage());
            dd($e->getMessage());
            return redirect('intimation_mail')->with('error', 'Intimation Mail created but email could not be sent.');
        }
    }




    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //echo "Hello"; die();
        $decryptedId = Crypt::decrypt($id);
        $data = IntimationMail::findOrFail($decryptedId);

        $collection_manager = User::where('user_role_id', 3)->get();
        $users = User::get(['id', 'name']);
        $products = Products::get(['id', 'name']);
        $agency = Agency::get(['id', 'name', 'email']);
        $attributes = ProductAttribute::where('product_id', $data->product_id)->get(['id', 'product_attribute_name']);


        // Calculate the last three months excluding the current month
        $now = Carbon::now();
        $months = [];
        for ($i = 1; $i <= 3; $i++) {
            $date = $now->subMonth()->startOfMonth(); // Move one month back
            $months[] = [
                'value' => $date->format('Y-m'), // Internal value (e.g., 2024-01)
                'label' => $date->format('M Y')  // Display format (e.g., Jan 2024)
            ];
        }

        // Reverse the array to have the most recent month first
        $months = array_reverse($months);

        return view('intimation_mail.edit', compact('data', 'users', 'products', 'agency', 'attributes', 'months'));
    }


    public function update(Request $request, $id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $data = IntimationMail::findOrFail($decryptedId);
        } catch (DecryptException $e) {
            return redirect()->route('intimation_mail.index')->with('error', 'Invalid ID');
        }

        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            // Add other validation rules as needed
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Update the record with new data
        $data->name = $request['name'];
        $data->process_review_month = $request['process_review_month'];
        $data->audit_date = $request['audit_date'];
        $data->agency = $request['agency'];
        $data->agency_email = $request['email'];
        $data->product_id = $request['product_id'];
        $data->product_attribute_id = $request['product_attribute_id'];
        // $data->collection_manager = $request['collection_manager'];
        $data->auditor = $request['auditor'];
        $data->description = $request['description'];
        $data->lavel_3 = isset($request['level_3'])
            ? implode(',', $request['level_3'])
            : null;
        $data->lavel_4 = isset($request['level_4'])
            ? implode(',', $request['level_4'])
            : null;
        $data->lavel_5 = isset($request['level_5'])
            ? implode(',', $request['level_5'])
            : null;
        $data->save();
        //echo '<pre>'; print_r($request->date);die;

        return redirect('intimation_mail')->with('success', 'Intimation Mail updated successfully');
    }






    public function destroy($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);

            $record = IntimationMail::findOrFail($decryptedId);
            $record->delete();

            return redirect('intimation_mail')->with('success', 'Intimation mail deleted successfully.');
        } catch (DecryptException $e) {

            return redirect('intimation_mail')->with('error', 'Invalid ID');
        }
    }

    public function getAgencyEmails(Request $request)
    {
        $agencyId = $request->input('agency_id'); // Get agency ID from POST data

        // dd($agencyId);
        $emails = DB::table('agency_mobile_emails')
            ->where('agency_id', $agencyId)
            ->whereNotNull('email')
            ->pluck('email');

        return response()->json($emails);
    }
}
