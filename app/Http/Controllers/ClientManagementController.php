<?php

namespace App\Http\Controllers;

use App\Language;

use App\Mail\UserCreated;

use App\Process;

use App\Region;

use Illuminate\Support\Facades\Crypt;

use Spatie\Permission\Models\Role;

use App\User;

use Carbon\Carbon;

use App\UsersMaster;

use Auth;

use DB;

//use Crypt;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Facades\Storage;

use Validator;

use Maatwebsite\Excel\Facades\Excel;

use App\Exports\UsersExport;

use App\Exports\QcAndQaChangesExport;
use App\Imports\UsersImport;
use Illuminate\Support\Str;
use App\Client;
use App\Helpers\Helper;


class ClientManagementController extends Controller
{
    // public function index()
    // {
    //     $userRole = auth()->user()->roles->first();
    //     // dd($userRole);
    //     $authUserEmail = auth()->user()->email; 
    //     $check=User::select('is_approved')->get();
    //      $query = User::with('roles')
    //             ->where('active_status', 0)
    //             ->whereDoesntHave('roles', function ($query) {
    //                 $query->where('name', 'Client');
    //             });

    //     // dd($query);
    //     if ($userRole->name == 'Admin') {
    //         $query->where("created_by", $authUserEmail);
    //     }

    //     $query->where(function ($subQuery) {
    //         $subQuery->whereDoesntHave("roles", function ($roleQuery) {
    //             $roleQuery->where("name", "Quality Auditor")->where("is_approved", 0);
    //         })->orWhereHas("roles", function ($roleQuery) {
    //             $roleQuery->where("name", "Quality Auditor")->where("is_approved", 1);
    //         });
    //     });   

    //     $data = $query->get();
    //     // dd($data);
    //     return view("acl.client.list", ["data" => $data]);
    // }

    public function index()
    {
        $data = User::where('active_status', 0)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'Client');
            })
            ->with('roles')
            ->get();

        return view("acl.client.list", ["data" => $data]);
    }


    public function create()
    {
        $userRole = auth()->user()->roles->first();

        if ($userRole->name == 'Super Admin') {
            $roles = Role::where('name', 'Client')->pluck('name', 'id')->toArray();
        } elseif ($userRole->name == 'Admin') {
            $roles = Role::where('name', 'Quality Auditor')->pluck('name', 'id')->toArray();
        } else {
            $roles = Role::where('name', '!=', 'Quality Auditor')->where('name', '!=', 'Client')->pluck('name', 'id')->toArray();
        }

        return view("acl.client.create", compact("roles"));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "email" => "required|email|unique:users,email",
            "mobile" => "required|numeric|digits:10",
            "logo" => "nullable|image|mimes:jpg,jpeg,png,gif|max:2048",
            "color_code" => "nullable|regex:/^#[0-9A-Fa-f]{6}$/", // hex code validation
            "levels" => "nullable|array",
            "levels.*" => "nullable|string|max:255", // Each level entry
        ]);

        $validator->sometimes("password", "required|confirmed", function ($input) {
            return $input->auto != "automatic";
        });

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->with("error", [$validator->errors()->all()])
                ->withInput();
        }

        $data = new User();
        $data->name = $request->name;
        $data->email = $request->email;
        $data->mobile = $request->mobile;
        $data->created_by = Auth::user()->email;
        $data->audit_agency_id = Auth::user()->id;
        $data->color_code = $request->color_code ?? '#000000';
        $data->cycle_start_date = $request->start_date;
        $data->cycle_end_date = $request->end_date;
        $password = $request->auto == "automatic" ? 'Admin@123' : $request->password;
        $data->password = bcrypt($password);
        $data->is_legal = $request->has('is_legal') ? 1 : 0;
        $data->is_compliance = $request->has('is_compliance') ? 1 : 0;
        $path = "";

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $path = $file->store('logos', 'public');
            $data->logo = $path;
        }

        // Process and save levels
        $levelsArray = [];
        if ($request->filled('levels')) {
            foreach ($request->levels as $index => $value) {
                if (!empty($value)) {
                    $levelsArray['Level ' . ($index + 1)] = $value;
                }
            }
        }
        $data->levels = $levelsArray; // Make sure 'levels' column exists and is json cast

        $data->save();

        $data->client_id = $data->id;
        $data->save();

        $getClientInfo = Client::where('client_id', $data->id)->first();

        $cinfodata['client_id'] = $data->id;
        $cinfodata['client_code'] = Helper::generateRandomCode(6);
        $cinfodata['logo'] = $path;
        $cinfodata['client_name'] = $request->name;
        $cinfodata['action_plan_tat'] = $request->action_plan_tat;
        $cinfodata['client_email'] = $request->client_email;
        $cinfodata['sidebar_color_code'] = $data->color_code;
        $cinfodata['negative_score_range'] = $request->negative_score_range;

        if ($getClientInfo) {
            unset($cinfodata["client_code"]);
            $getClientInfo->update($cinfodata);
        } else {
            Client::create($cinfodata);
        }

        $clientRole = Role::where("name", "Client")->first();
        if ($clientRole) {
            $data->assignRole($clientRole);
        }

        return redirect("client")->with("success", "User created successfully.");
    }




    // for disable USER
    public function disable($id)
    {
        if (
            Auth::user()
            ->roles()
            ->first()->name == "Admin"
        ) {
            $user = User::find(Crypt::decrypt($id));
            $user->active_status = 1;
            $user->password = "fgfgfggfgdgdfgfdgdffhgfhreterfghh5y55";
            $user->disable_date = now();
            $user->update();
            return back();
        }
        return "Unauthorised";
    }

    public function edit($id)
    {
        $userRole = auth()->user()->roles->first();

        if ($userRole->name == 'Super Admin') {
            $roles = Role::where('name', 'Client')->pluck('name', 'id')->toArray();
        } elseif ($userRole->name == 'Admin') {
            $roles = Role::where('name', 'Quality Auditor')->pluck('name', 'id')->toArray();
        } else {
            $roles = Role::where('name', '!=', 'Quality Auditor')->where('name', '!=', 'Client')->pluck('name', 'id')->toArray();
        }
        $data = User::find(Crypt::decrypt($id));

        $rdata = User::find(Crypt::decrypt($id))
            ->roles->pluck("id")
            ->toArray();

        return view("acl.client.edit", compact("roles", "data", "rdata"));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "email" => "required|email",
            "mobile" => "required|numeric|digits:10",
            "role" => "required",
            "color_code" => "nullable|regex:/^#[0-9A-Fa-f]{6}$/",
            "logo" => "nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048",
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->with("error", [$validator->errors()->all()])
                ->withInput();
        } else {
            $user = User::find(Crypt::decrypt($id));

            $user->name = $request->name;
            $user->mobile = $request->mobile;
            $user->color_code = $request->color_code ?? $user->color_code;
            $user->levels = $request->input('levels') ?? [];
            $user->is_legal = $request->has('is_legal') ? 1 : 0;
            $user->is_compliance = $request->has('is_compliance') ? 1 : 0;


            if ($request->hasFile('logo')) {
                if ($user->logo && file_exists(storage_path('app/public/' . $user->logo))) {
                    unlink(storage_path('app/public/' . $user->logo));
                }

                $logoPath = $request->file('logo')->store('logos', 'public');
                $user->logo = $logoPath;
            }

            $user->save();

            $getClientInfo = Client::where('client_id', $user->id)->first();

            $cinfodata['client_id'] = $user->id;
            $cinfodata['client_code'] = Helper::generateRandomCode(6);
            $cinfodata['logo'] = $user->logo;
            $cinfodata['client_name'] = $request->name;
            $cinfodata['action_plan_tat'] = $request->action_plan_tat;
            $cinfodata['client_email'] = $request->client_email;
            $cinfodata['sidebar_color_code'] = $user->color_code;
            $cinfodata['negative_score_range'] = $request->negative_score_range;

            if ($getClientInfo) {
                unset($cinfodata["client_code"]);
                $getClientInfo->update($cinfodata);
            } else {
                Client::create($cinfodata);
            }

            $roles = $request["role"];
            if (isset($roles)) {
                $user->roles()->sync($roles);
            } else {
                $user->roles()->detach();
            }
            return redirect("client")->with("success", "Client Updated Successfully");
        }
    }



    public function change_user_status($user_id, $status)
    {
        $user = User::find(Crypt::decrypt($user_id));

        $user->status = $status;

        $user->save();

        return redirect("client")->with(
            "success",
            "User status updated successfully"
        );
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $userId = Crypt::decrypt($request->user_id);

        $user = User::find($userId);

        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();

            return response()->json(['success' => true, 'message' => 'Password updated successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'User not found.']);
    }
    public function customer_profile()
    {
        $user = Auth::User();

        if ($user->avatar) {
            $final_data["user"] = Storage::url($user->avatar);
        } else {
            $final_data["user"] = "http://via.placeholder.com/150x150";
        }

        return response()->json(
            ["status" => 200, "message" => "Success", "data" => $user],
            200
        );
    }

    public function profile()
    {
        $id = Auth::user()->id;

        $roles = "";

        $data = User::find($id);

        $rdata = User::find($id);

        // echo '<pre>'; print_r( $rdata); die;

        //print_r($rdata);

        return view("acl.client.profile", [
            "data" => $data,
            "roles" => $roles,
            "rdata" => $rdata,
        ]);

        //return view('acl.client.profile',compact($rdata));
    }

    public function delImage($filePath)
    {
        $url =
            "https://" .
            env("AWS_BUCKET") .
            ".s3." .
            env("AWS_DEFAULT_REGION") .
            ".amazonaws.com" .
            "/";

        $filePath = str_replace($url, "", $filePath);

        Storage::disk("s3")->delete($filePath);
    }



    // public function updateprofile(Request $request, $id)
    // {
    //     $validator = Validator::make($request->all(), [
    //         "name" => "required",

    //         "email" => "required",

    //         "mobile" => "required",
    //     ]);

    //     if ($validator->fails()) {
    //         return redirect()
    //             ->back()

    //             ->withErrors($validator)

    //             ->withInput();
    //     } else {
    //         $data = User::find(Crypt::decrypt($id));

    //         $data->name = $request->name;

    //         $data->email = $request->email;

    //         $data->mobile = $request->mobile;

    //         if ($request->avatar) {
    //             if ($data->avatar) {
    //                 Storage::delete(
    //                     "company/_" .
    //                         Auth::user()->company_id .
    //                         "/user/_" .
    //                         Auth::Id() .
    //                         "/avatar/" .
    //                         $data->avatar
    //                 );
    //             }

    //             $request->avatar->store(
    //                 "company/_" .
    //                     Auth::user()->company_id .
    //                     "/user/_" .
    //                     Auth::Id() .
    //                     "/avatar"
    //             );

    //             $data->avatar = $request->avatar->hashName();
    //         }

    //         $data->save();

    //         return redirect("profile")->with(
    //             "success",
    //             "User Updated Successfully"
    //         );
    //     }
    // }

    public function updateprofile(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "email" => "required|email",
            "mobile" => "nullable|numeric|digits:10",
            "avatar" => "nullable|image|mimes:jpg,jpeg,png|max:2048",
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        } else {
            $data = User::find(Crypt::decrypt($id));
            $data->name = $request->name;
            $data->email = $request->email;
            $data->mobile = $request->mobile;

            if ($request->hasFile('avatar')) {
                // Delete old avatar if it exists
                $oldAvatarPath = public_path("images/avatar/" . $data->avatar);
                if (file_exists($oldAvatarPath) && is_file($oldAvatarPath)) {
                    unlink($oldAvatarPath); // Remove the old file if it's a file, not a directory
                }

                // Store the new avatar in public/images/avatar folder
                $avatar = $request->file('avatar');
                $avatarName = time() . '_' . $avatar->getClientOriginalName();
                $avatar->move(public_path('images/avatar'), $avatarName);
                $data->avatar = $avatarName;
            }

            $data->save();
            return redirect("profile")->with("success", "User Updated Successfully");
        }
    }


    public function destroy($id)
    {
        //Find a user with a given id and delete

        $user = User::findOrFail(Crypt::decrypt($id));

        $user->delete();

        return redirect()
            ->route("client.index")

            ->with(
                "success",

                "User successfully deleted."
            );
    }

    public function excelDownloadUser()
    {
        ini_set("memory_limit", "-1");

        ini_set("max_execution_time", 3000);

        return Excel::download(new clientExport(), "client.xlsx");

        // return Excel::download(new QcAndQaChangesExport, 'client.xlsx');
    }

    public function userImport(Request $request)
    {
        // Validate the request to ensure the file is present
        $validator = Validator::make($request->all(), [
            'user_excel' => 'required|file|mimes:xlsx,xls',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if ($request->hasFile('user_excel')) {
            $path1 = $request->file('user_excel')->store('temp');
            $dacpath = storage_path('app/' . $path1);

            // Create an instance of clientImport without the role
            $exampleImport = new clientImport();

            try {
                Excel::import($exampleImport, $dacpath);
            } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                $failures = $e->failures();
                return redirect()->back()->withErrors($failures)->withInput();
            }
        }

        return redirect('user');
    }


    public function auditor_status($user_id, Request $request)
    {
        $user = User::find(Crypt::decrypt($user_id));

        $user->is_approved = $request->input('is_approved');
        if ($user->is_approved == 1) {
            $user->approval_date = Carbon::now();
        }
        $user->update();

        return redirect("user")->with(
            "success",
            "Auditor status updated successfully"
        );
    }

    public function show()
    {
        $userRole = auth()->user()->roles->first();
        $authUserEmail = auth()->user()->email;

        $data = User::whereHas('roles', function ($query) {
            $query->where('name', 'Quality Auditor');
        })->where('is_approved', 0)
            ->get();



        return view("acl.client.auditorRequest", ["data" => $data]);
    }

    public function masterqa_list()
    {
        $data = User::where('active_status', 0)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'Master QA');
            })
            ->with('roles')
            ->get();

        return view("acl.client.master_qa_list", ["data" => $data]);
    }
    public function masterqa_client_list($id)
    {
        $masterQA = User::findOrFail($id);

        // Existing mappings of this Master QA
        $selectedAuditors = DB::table('master_qa_mappings')
            ->where('master_qa_id', $id)
            ->pluck('quality_auditor_id', 'client_id');

        // Auditors assigned to OTHER Master QAs
        $assignedAuditors = DB::table('master_qa_mappings')
            ->where('master_qa_id', '!=', $id)
            ->pluck('quality_auditor_id');

        $clients = User::where('active_status', 0)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'Client');
            })
            ->get();

        foreach ($clients as $client) {
            $client->quality_auditor_ids = $mappings
                ->where('client_id', $client->id)
                ->pluck('quality_auditor_id');

            $client->quality_auditors = User::whereIn(
                'id',
                $client->quality_auditor_ids
            )->get();
        }

        return view('acl.client.client_list', [
            'users'             => $clients,
            'id'                => $id,
            'masterQA'          => $masterQA,
            'selectedAuditors'  => $selectedAuditors,
        ]);
    }

    public function masterqa_save(Request $request)
    {
        $masterQaId = $request->masterqa_id;
        $auditors = $request->quality_auditor ?? [];

        // Existing mappings for this Master QA (key = client_id)
        $existingMappings = DB::table('master_qa_mappings')
            ->where('master_qa_id', $masterQaId)
            ->get()
            ->keyBy('client_id');

        foreach ($auditors as $clientId => $auditorId) {

            // Existing mapping for this client
            $existing = $existingMappings->get($clientId);

            // If dropdown is blank, remove existing mapping (if any)
            if (empty($auditorId)) {

                if ($existing) {
                    DB::table('master_qa_mappings')
                        ->where('id', $existing->id)
                        ->delete();
                }

                continue;
            }

            // Mapping already exists
            if ($existing) {

                // Update only if auditor changed
                if ($existing->quality_auditor_id != $auditorId) {

                    DB::table('master_qa_mappings')
                        ->where('id', $existing->id)
                        ->update([
                            'quality_auditor_id' => $auditorId,
                            'updated_at' => now()
                        ]);
                }
            } else {

                // New mapping
                DB::table('master_qa_mappings')->insert([
                    'master_qa_id'       => $masterQaId,
                    'client_id'          => $clientId,
                    'quality_auditor_id' => $auditorId,
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ]);
            }
        }

        return redirect()->back()->with('success', 'Mapping saved successfully.');
    }

    public function masterQaDashboard()
    {
        $user = auth()->user();

        $mappings = DB::table('master_qa_mappings')
            ->where('master_qa_id', $user->id)
            ->get();

        $clientIds = $mappings->pluck('client_id');

        $clients = User::whereIn('id', $clientIds)->get();

        foreach ($clients as $client) {
            $client->quality_auditor_ids = $mappings
                ->where('client_id', $client->id)
                ->pluck('quality_auditor_id');

            // Optional: fetch auditor details instead of just IDs
            $client->quality_auditors = User::whereIn(
                'id',
                $client->quality_auditor_ids
            )->get();
        }

        return view('acl.client.masterqa.dashboard', [
            'user'    => $user,
            'clients' => $clients,
        ]);
    }

    public function loginClient(Request $request)
    {
        $request->validate([
            'quality_auditor_id' => 'required|integer',
            'client_id' => 'required|integer',
        ]);

        $masterQaId = auth()->id();

        // Verify that the QA belongs to this Master QA
        $mapping = DB::table('master_qa_mappings')
            ->where('master_qa_id', $masterQaId)
            ->where('client_id', $request->client_id)
            ->where('quality_auditor_id', $request->quality_auditor_id)
            ->exists();

        if (!$mapping) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        // Store original Master QA details
        session([
            'master_qa_id' => $masterQaId,
            'master_qa_name' => auth()->user()->name,
        ]);

        // Login as Quality Auditor
        Auth::loginUsingId($request->quality_auditor_id);

        // Regenerate session after login
        $request->session()->regenerate();

        return redirect()->route('dashboard'); // Change to your dashboard route
    }

    public function backToMasterQa(Request $request)
    {
        if (!session()->has('master_qa_id')) {
            return redirect()->route('login');
        }

        $masterQaId = session('master_qa_id');

        // Preserve the ID before login regenerates the session
        Auth::loginUsingId($masterQaId);

        // Remove the session values after switching back
        session()->forget(['master_qa_id', 'master_qa_name']);

        return redirect()->route('masterqa.dashboard');
    }
}
