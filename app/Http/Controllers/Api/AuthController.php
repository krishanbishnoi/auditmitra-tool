<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use DB;
use App\Http\Controllers\Controller;
use App\User;



class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
        if ($validator->fails()) {
            $data = array('status' => 0, 'message' => 'Validation Errors', 'data' => $validator->errors());
            return response(json_encode($data), 200);
        } else {
            $getUser = User::select('id', 'name', 'email', 'password', 'auth_key', 'client_id')->where(['email' => $request->email])->first();
            $modelId = DB::table('model_has_roles')->select('role_id')->where(['model_id' => $getUser->id])->first();
            $roleName = DB::table('roles')->where('id', $modelId->role_id)->value('name');
            // dd($roleName);
            if ($getUser) {
                if (!Hash::check(trim($request->password), $getUser->password)) {
                    $data = array('status' => 0, 'message' => 'Password not matched', 'data' => array());
                } else {
                    $getUser->email = $request->email;
                    $getUser->auth_key = Hash::make($request->email);
                    $getUser->save();
                    $finalData = array('auth_key' => $getUser->auth_key);
                    $url = asset('images/profile_pic');
                    if ($getUser->user_detail) {
                        $data = array(
                            'status' => 1,
                            'name' => $getUser->user_detail->full_name,
                            'mobile_no' => $getUser->mobile_no,
                            'auth_key' => $getUser->auth_key,
                            'user_id' => $getUser->id,
                            'user_role' => $roleName,
                            'profile_pic' => $getUser->user_detail->profile_pic,
                            'img_url' => $url . "/" . $getUser->user_detail->profile_pic,
                            'form_submit_level' => $getUser->form_submit_level,
                            'overall_status' => $getUser->status,
                            'client_id' => $getUser->client_id, //// client_id = 288 for client sunstone send this param
                            'message' => 'Login Successfully'
                        );
                    } else {
                        $data = array('status' => 1, 'name' => $getUser->name, 'email' => $getUser->email, 'auth_key' => $getUser->auth_key, 'user_id' => $getUser->id, 'user_role' => $roleName,  'client_id' => $getUser->client_id, 'message' => 'Login Successfully');
                    }
                }
            } else {
                $data = array('status' => 0, 'message' => "You don't have an account.Please signup.", 'data' => array());
            }
            return response(json_encode($data), 200);
        }
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
            'new_password' => 'required|string',
        ]);
        if ($validator->fails()) {
            $data = array('status' => 0, 'message' => 'Validation Errors', 'data' => $validator->errors());
            return response(json_encode($data), 200);
        } else {
            $getUser = User::select('id', 'name', 'email', 'password', 'auth_key')->where(['email' => $request->email])->first();
            if ($getUser) {
                if (!Hash::check(trim($request->password), $getUser->password)) {
                    $data = array('status' => 0, 'message' => 'Password not matched', 'data' => array());
                } else {
                    $update_user = User::find($getUser->id);
                    $update_user->password = Hash::make(trim($request->new_password));
                    $update_user->save();

                    $data = array('status' => 1, 'name' => $getUser->name, 'email' => $getUser->email, 'auth_key' => $getUser->auth_key, 'user_id' => $getUser->id, 'message' => 'Password updated Successfully');
                }
            } else {
                $data = array('status' => 0, 'message' => "You don't have an account.Please signup.", 'data' => array());
            }
            return response(json_encode($data), 200);
        }
    }


    // V Profile Image update
    public function updateProfileApi(Request $request)
    {
        // Check if the Authorization header exists and is valid
        if (!$request->header('Authorizations') || $request->header('Authorizations') == "") {
            return response()->json([
                "status" => 0,
                "message" => "Authorization key is required in API headers.",
                "data" => [],
            ], 200);
        }

        $getUser = User::select('id', 'auth_key', 'email', 'name', 'mobile', 'avatar')
            ->where('auth_key', $request->header('Authorizations'))
            ->first();

        if (!$getUser) {
            return response()->json([
                "status" => 0,
                "message" => "User not found.",
                "data" => [],
            ], 200);
        }

        // Validation
        $validator = Validator::make($request->all(), [
            "name" => "required|string|max:255",
            "email" => "required|email|max:255",
            "mobile" => "required|string|max:15",
            "avatar" => "nullable|image|mimes:jpg,jpeg,png|max:2048",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "message" => "Validation failed.",
                "errors" => $validator->errors(),
            ], 422);
        }

        try {
            // Update user information
            $getUser->name = $request->name;
            $getUser->email = $request->email;
            $getUser->mobile = $request->mobile;

            if ($request->hasFile('avatar')) {
                // Delete old avatar if it exists
                if ($getUser->avatar) {
                    $oldAvatarPath = public_path("public/images/avatar/" . $getUser->avatar);
                    if (file_exists($oldAvatarPath) && is_file($oldAvatarPath)) {
                        unlink($oldAvatarPath);
                    }
                }

                // Save new avatar
                $avatar = $request->file('avatar');
                $avatarName = time() . '_' . $avatar->getClientOriginalName();
                $avatar->move(public_path('public/images/avatar'), $avatarName);

                // Update avatar field
                $getUser->avatar = $avatarName;
            }

            $getUser->save();

            return response()->json([
                "status" => 1,
                "message" => "Profile updated successfully.",
                "data" => [
                    "name" => $getUser->name,
                    "email" => $getUser->email,
                    "mobile" => $getUser->mobile,
                    "avatar_url" => $getUser->avatar ? asset('public/images/avatar/' . $getUser->avatar) : null,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => 0,
                "message" => "An error occurred while updating the profile.",
                "error" => $e->getMessage(),
            ], 500);
        }
    }


    //V image Profile Get
    public function getProfileImage(Request $request)
    {
        // Check if the Authorization header exists and is valid
        if (!$request->header('Authorizations') || $request->header('Authorizations') == "") {
            return response()->json([
                "status" => 0,
                "message" => "Authorization key is required in API headers.",
                "data" => [],
            ], 200);
        }

        $getUser = User::select('id', 'auth_key', 'name', 'email', 'mobile', 'avatar')
            ->where('auth_key', $request->header('Authorizations'))
            ->first();

        if (!$getUser) {
            return response()->json([
                "status" => 0,
                "message" => "User not found.",
                "data" => [],
            ], 200);
        }

        try {
            return response()->json([
                "status" => 1,
                "message" => "Profile fetched successfully.",
                "data" => [
                    "name" => $getUser->name,
                    "email" => $getUser->email,
                    "mobile" => $getUser->mobile,
                    "avatar_url" => $getUser->avatar ? asset('public/images/avatar/' . $getUser->avatar) : null,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => 0,
                "message" => "An error occurred while fetching the profile.",
                "error" => $e->getMessage(),
            ], 500);
        }
    }
}
