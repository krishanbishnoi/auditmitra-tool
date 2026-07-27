<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

use App\Agency;
use App\User;
use DB, Response;
use App\Model\Branch;

use Validator;
use App\Imports\AgencyImport;
use App\Exports\AgencyExport;

use Spatie\Permission\Models\Role;
use App\UsersMaster;
//use Auth;
use Illuminate\Support\Facades\Auth;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Hash;

class AuditAgencyController extends Controller

{

    public function index()
    {
        $clients = User::role('Client')->get();
        $userRole = auth()->user()->roles->first();
        $roles = Role::all();
        $user = auth()->user();

        if ($user->hasRole('Client')) {
            $auditagency = User::role('Admin')
                ->where('client_id', $user->id)
                ->get();
        } elseif ($user->hasRole('Super Admin')) {
            $auditagency = User::role('Admin')->get();
        } else {
            $auditagency = collect();
        }
        return view('audit_agency.list', compact('auditagency', 'clients'));
    }



    public function create()
    {
        $clients = User::role('Client')->pluck('name', 'id');
        $roles = Role::all();

        return view("audit_agency.create", compact("roles", "clients"));
    }


    public function store(Request $request)
    {
        // Validate basic fields
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "email" => "required|email|unique:users,email",
            "mobile" => "required|digits:10",
            "role" => "required",
        ]);

        // Conditionally validate password if not automatic
        $validator->sometimes("password", "required|confirmed|min:8", function ($input) {
            return $input->auto != "automatic";
        });

        // Conditionally validate client_id if Super Admin
        $validator->sometimes("client_id", "required|exists:users,id", function () {
            return Auth::user()->hasRole('Super Admin');
        });

        // If validation fails, redirect back
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Create user instance
        $data = new User();
        $data->name = $request->name;
        $data->email = $request->email;
        $data->mobile = $request->mobile;

        // Agency-specific fields
        $data->agency_admin = $request->agency_admin;
        $data->agency_admin_email_one = $request->agency_admin_email_one;
        $data->agency_admin_email_two = $request->agency_admin_email_two;

        // Set who created the user
        $data->created_by = Auth::user()->email;

        // Assign client_id based on role
        if (Auth::user()->hasRole('Super Admin')) {
            $data->client_id = $request->client_id;
        } else {
            $data->client_id = Auth::user()->client_id;
        }

        // Handle password
        if ($request->auto == "automatic") {
            $password = Str::random(8);
            $data->password = bcrypt($password);
        } else {
            $password = $request->password;
            $data->password = bcrypt($password);
        }

        // Save the user
        $data->save();

        // Assign roles
        foreach ($request->role as $roleId) {
            $role = Role::findOrFail($roleId);
            $data->assignRole($role);
        }

        // Optionally send welcome email (commented out)
        try {
            // $url = url('/login'); // or any other link
            // Mail::send('emails.createUser', ['user' => $data, 'password' => $password, 'url' => $url], function ($m) use ($data) {
            //     $m->from('auditemail.noreply@qdegrees.org', 'Welcome QDegrees')
            //         ->to($data->email, $data->name)
            //         ->subject('Welcome to the Agency');
            // });
        } catch (Exception $e) {
            error_log('Email sending failed: ' . $e->getMessage());
        }

        return redirect("audit_agency")->with("success", "Audit Agency created successfully.");
    }










    public function show($id)
    {
        $data = Agency::where('id', Crypt::decrypt($id))->update(['status' => 1]);

        if ($data) {

            return redirect('agency')->with('success', ['Agency deleted successfully.']);
        } else {
            return redirect()->back()->with('error', ['Agency deletion unsuccessfully.']);
        }
    }




    public function edit($id)
    {
        $roles = Role::all();

        $data = User::find(Crypt::decrypt($id));
        return view('audit_agency.edit', compact('data', 'roles'));
    }



    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . Crypt::decrypt($id),
            'mobile' => 'required|string|max:15',
            'password' => 'nullable|string|min:6',
            'agency_admin' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->all())->withInput();
        }

        $userId = Crypt::decrypt($id);
        $user = User::findOrFail($userId);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'agency_admin' => $request->agency_admin,
            'agency_admin_email_one' => $request->agency_admin_email_one,
            'agency_admin_email_two' => $request->agency_admin_email_two,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = bcrypt($request->password);
        }

        $updated = User::where('id', $userId)->update($updateData);

        if ($updated) {
            return redirect('audit_agency')->with('success', ['Audit Agency updated successfully.']);
        } else {
            return redirect()->back()->with('error', ['Audit Agency update was unsuccessful.']);
        }
    }






    // public function update(Request $request, $id)
    // {

    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required',
    //         'email' => 'required',
    //         'mobile' => 'required',

    //     ]);
    //     if ($validator->fails()) {
    //         return redirect()->back()->with('error', [$validator->errors()->all()])->withInput();
    //     } else {
    //         $agency=User::where('id',Crypt::decrypt($id))->update(

    //             [
    //                 'name'=>$request->name,
    //                 'email'=>$request->email,
    //                 'mobile'=>$request->mobile,
    //                 'agency_admin'=>$request->agency_admin,
    //                 'agency_admin_email_one'=>$request->agency_admin_email_one,
    //                 'agency_admin_email_two'=>$request->agency_admin_email_two,
    //             ]
    //         );

    //         if($agency){
    //             return redirect('audit_agency')->with('success', ['Audit Agency updated successfully.']);
    //         }
    //         else{
    //                 return redirect()->back()->with('error', ['Audit Agency updation unsuccessfully.']);
    //         }
    //     }
    // }



    /**

     * Remove the specified resource from storage.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */


    public function destroy($id)
    {
        $user = User::findOrFail(Crypt::decrypt($id));
        $user->delete();

        return redirect()->route("audit_agency.index")->with("success", "Audit Agency successfully deleted.");
    }


    public function excelDownloadAgency()
    {

        ini_set('memory_limit', '-1');

        ini_set('max_execution_time', 3000);

        return Excel::download(new AgencyExport, 'Agency.xlsx');
    }


    //V Upload Page Show
    public function showAgencyImport()
    {
        return view('agency.upload');
    }


    //V Upload Excel Data
    public function agencyImport()
    {
        //  echo 'dfdsf'; die;AgencyImport
        Excel::import(new AgencyImport, request()->file('file'));

        return redirect()->back()->with('success', 'Excel file imported successfully.');
    }

    public function downloadAgencySample()
    {
        //  echo "jh"; die;
        $file = public_path() . "/download/agency_import.xlsx";
        $headers = array(
            'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        );
        return Response::download($file, 'agency_import.xlsx', $headers);
    }
}
