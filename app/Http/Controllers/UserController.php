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

//use Crypt;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Facades\Storage;

use Validator;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Hash;
use App\Exports\UsersExport;

use App\Exports\QcAndQaChangesExport;
use App\Imports\UsersImport;
use Illuminate\Support\Str;


class UserController extends Controller
{
    public function index()
    {
        $authUser = auth()->user();
        $userRole = $authUser->roles->first();
        $authUserEmail = $authUser->email;

        // Fetch clients (users with "Client" role) for the dropdown
        $clients = [];
        if ($userRole->name == 'Super Admin') {
            $clients = User::whereHas('roles', function ($query) {
                $query->where('name', 'Client');
            })->get(['id', 'name']);
        }

        // Base query
        $query = User::with('roles')
            // ->where('active_status', 0)
            ->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'Client');
            });

        // Restrict data if not Super Admin
        if ($userRole->name != 'Super Admin') {
            $query->where('client_id', $authUser->client_id);

            if ($userRole->name == 'Admin') {
                $query->where('created_by', $authUserEmail);
            }
        }

        // Handle Quality Auditor approval logic
        $query->where(function ($subQuery) {
            $subQuery->whereDoesntHave('roles', function ($roleQuery) {
                $roleQuery->where('name', 'Quality Auditor')->where('is_approved', 0);
            })->orWhereHas('roles', function ($roleQuery) {
                $roleQuery->where('name', 'Quality Auditor')->where('is_approved', 1);
            });
        });

        $data = $query->get();

        return view("acl.users.list", [
            "data" => $data,
            "clients" => $clients
        ]);
    }





    public function create()
    {

        $user = auth()->user();
        $userRole = $user->roles->first();

        if ($userRole->name == 'Admin') {
            $roles = Role::where('name', 'Quality Auditor')->pluck('name', 'id')->toArray();
        } else {
            $roles = Role::where('name', '!=', 'Quality Auditor')
                ->where('name', '!=', 'Client')
                ->pluck('name', 'id')->toArray();
        }

        $clients = User::role('Client')->pluck('name', 'id');

        return view("acl.users.create", compact("roles", "clients"));
    }


    public function store(Request $request)
    {
        // Base validation
        $validator = Validator::make($request->all(), [
            "name"      => "required|string|max:255",
            "email"     => "required|email|unique:users,email",
            "mobile"    => "nullable|numeric|digits:10",
            "role"      => "required",
            "client_id" => "nullable|exists:users,id",
            "auto"      => "required|in:automatic,manual",
        ]);

        // Conditional password validation (ONLY for manual entry)
        $validator->sometimes(
            'password',
            [
                'required',
                'confirmed',
                'min:8',
                'regex:/[A-Z]/',      // at least one uppercase
                'regex:/[a-z]/',      // at least one lowercase
                'regex:/[0-9]/',      // at least one number
                'regex:/[@$!%*#?&]/', // at least one special character
            ],
            function ($input) {
                return $input->auto === 'manual';
            }
        );

        // Custom error messages
        $validator->setCustomMessages([
            'password.required'  => 'Password is required when entered manually.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.min'       => 'Password must be at least 8 characters long.',
            'password.regex'     => 'Password must contain uppercase, lowercase, number, and special character.',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Create user
        $data = new User();
        $data->name = $request->name;
        $data->email = $request->email;
        $data->mobile = $request->mobile;
        $data->created_by = Auth::user()->email;
        $data->audit_agency_id = Auth::user()->id;

        // Assign client_id
        if (Auth::user()->hasRole('Super Admin')) {
            $data->client_id = $request->client_id;
        } else {
            $data->client_id = Auth::user()->client_id;
        }

        // Password handling
        if ($request->auto === "automatic") {
            $password = 'Admin@123'; // auto-generated password
        } else {
            $password = $request->password; // manual password
        }

        $data->password = bcrypt($password);
        $data->save();

        // Assign roles
        if (!empty($request->role)) {
            foreach ($request->role as $role) {
                $roleModel = Role::findOrFail($role);
                $data->assignRole($roleModel);
            }
        }

        return redirect("user")->with("success", ["User created successfully."]);
    }



    // for disable USER
    public function disable($id)
    {
        if (
            Auth::check() &&
            in_array(Auth::user()->roles()->first()->name, ['Admin', 'Client'])
        ) {
            $user = User::find(Crypt::decrypt($id));
            $user->active_status = $user->active_status == 1 ? 0 : 1;
            if ($user->active_status == 1) {
                // User is de-activated
                $user->disable_date = now();
            } else {
                // User is activated again
                $user->disable_date = null;
            }
            $user->save();

            return back();
        }

        return "Unauthorised";
    }

    public function edit($id)
    {
        $userRole = auth()->user()->roles->first();

        if ($userRole->name == 'Admin') {
            $roles = Role::where('name', 'Quality Auditor')->pluck('name', 'id')->toArray();
        } else {
            $roles = Role::where('name', '!=', 'Quality Auditor')->where('name', '!=', 'Client')->pluck('name', 'id')->toArray();
        }

        $data = User::find(Crypt::decrypt($id));

        $rdata = User::find(Crypt::decrypt($id))
            ->roles->pluck("id")
            ->toArray();

        return view("acl.users.edit", compact("roles", "data", "rdata"));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required",

            "email" => "required|email",
            "mobile" => "nullable|numeric|digits:10",
            // "mobile" => "required|numeric|digits:10",

            "role" => "required",
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->with("error", [$validator->errors()->all()])
                ->withInput();
        } else {
            $user = User::find(Crypt::decrypt($id));

            $user->name = $request->name;

            $user->email = $request->email;

            $user->mobile = $request->mobile;

            $user->save();

            $roles = $request["role"]; //Retreive all roles

            if (isset($roles)) {
                $user->roles()->sync($roles); //If one or more role is selected associate user to roles
            } else {
                $user->roles()->detach(); //If no role is selected remove exisiting role associated to a user
            }

            //return redirect('user')->with('success', ['User Updated successfully.']);

            return redirect("user")->with(
                "success",
                "User Updated Successfully"
            );
        }
    }

    public function change_user_status($user_id, $status)
    {
        $user = User::find(Crypt::decrypt($user_id));

        $user->status = $status;

        $user->save();

        return redirect("user")->with(
            "success",
            "User status updated successfully"
        );
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'password' => [
                'required',
                'confirmed',
                \Illuminate\Validation\Rules\Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ]);

        $userId = Crypt::decrypt($request->user_id);
        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ]);
        }

        // 🔒 Prevent using the same old password
        if (Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'New password cannot be the same as the old password.',
            ], 422);
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully.',
        ]);
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

        return view("acl.users.profile", [
            "data" => $data,
            "roles" => $roles,
            "rdata" => $rdata,
        ]);

        //return view('acl.users.profile',compact($rdata));
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
            // "email" => "required",
            // "mobile" => "required",
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
            // $data->email = $request->email;
            // $data->mobile = $request->mobile;

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
            ->route("users.index")

            ->with(
                "success",

                "User successfully deleted."
            );
    }

    public function excelDownloadUser()
    {
        ini_set("memory_limit", "-1");

        ini_set("max_execution_time", 3000);

        return Excel::download(new UsersExport(), "users.xlsx");

        // return Excel::download(new QcAndQaChangesExport, 'users.xlsx');
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

            // Create an instance of UsersImport without the role
            $exampleImport = new UsersImport(['authUser' => Auth::user(), 'client_id' => $request->client_id,]);

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
        $authUser = auth()->user();
        $authUserClientId = $authUser->client_id;

        // Fetch only those users with 'Quality Auditor' role and same client_id as authenticated user
        $data = User::whereHas('roles', function ($query) {
            $query->where('name', 'Quality Auditor');
        })
            ->where('is_approved', 0)
            ->where('client_id', $authUserClientId)
            ->get();

        return view("acl.users.auditorRequest", ["data" => $data]);
    }




    // chnage password user
    public function showChangePasswordForm()
    {
        return view('acl/users/changePassword');
    }
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Current password does not match.');
        }

        // 🔒 Prevent using the same old password
        if (Hash::check($request->password, $user->password)) {
            return back()->with('error', 'New password cannot be the same as the old password.');
        }
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('dashboard')->with('success', 'Password changed successfully!');
    }
    // chnage password user end


    public function switchUser(Request $request)
    {
        $email = $request->input('email');
        $defaultPassword = 'Admin@123';
        // Logout current user
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Attempt login with the given email and default password
        if (Auth::attempt(['email' => $email, 'password' => $defaultPassword])) {
            $request->session()->regenerate();
            return redirect()->route('dashboard')->with('success', 'Switched to ' . $email);
        }

        // If login fails
        return redirect()->route('login')->withErrors([
            'email' => 'Login failed for ' . $email,
        ]);
    }
}
