<?php

namespace App\Http\Controllers\Api;

use App\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;
use App\User;
use App\Artifact;
use App\TempArtifact;
use Storage;
use URL;

class ArtifactController extends Controller
{
    public function storeArtifact(Request $request)
    {
        if (!$request->header('Authorizations') || $request->header('Authorizations') == "") {
            $data = ['status' => 0, 'message' => 'Authorizations key is required in api headers.', 'data' => []];
            return response()->json($data, 200);
        }

        $getUser = User::where('auth_key', $request->header('Authorizations'))->first();
        if (!$getUser) {
            $data = ['status' => 0, 'message' => 'User not found', 'data' => []];
            return response()->json($data, 200);
        }

        $validator = Validator::make($request->all(), [
            'sheet_id' => 'required',
            'parameter_id' => 'required',
            'sub_parameter_id' => 'required',
            'totalFile' => 'required|max:10000',
            'status' => 'required'  // if status 1 = getting audit id, 0 = temp audit id
        ]);

        if ($validator->fails()) {
            $data = ['status' => 0, 'message' => 'Validation Errors', 'data' => $validator->errors()];
            return response()->json($data, 200);
        }

        $data = [];

        if ($request->file('totalFile')) {
            $image = $request->file('totalFile');

            // Get the original name, trim spaces, and replace spaces with underscores
            $imageName = ($request->status == 1 ? $request->audit_id : $request->temp_audit_id)
                . '_'
                . $request->sub_parameter_id
                . '_'
                . time()
                . '_'
                . preg_replace('/\s+/', '_', trim($image->getClientOriginalName())); // Replacing spaces with underscores

            // Store the image in 'storage/app/public/artifacts'
            $path1 = $image->storeAs('public', $imageName);

            // Keep the path as is, with the public folder included
            $storedPath = $path1;

            if ($request->status == 1) {
                // Save as an Artifact
                $artifact = Artifact::create([
                    'sheet_id' => $request->sheet_id,
                    'parameter_id' => $request->parameter_id,
                    'sub_parameter_id' => $request->sub_parameter_id,
                    'file' => $storedPath,
                    'audit_id' => $request->audit_id ?? null,
                    'client_id' => $getUser->client_id
                ]);
                $data[] = $artifact;
            } else {
                // Save as a TempArtifact
                $artifact = TempArtifact::create([
                    'sheet_id' => $request->sheet_id,
                    'parameter_id' => $request->parameter_id,
                    'sub_parameter_id' => $request->sub_parameter_id,
                    'file' => $storedPath,
                    'temp_audit_id' => $request->temp_audit_id ?? null,
                    'client_id' => $getUser->client_id
                ]);
                $data[] = $artifact;
            }
        }

        if (count($data) > 0) {
            $response = ['status' => 1, 'message' => 'Artifact Saved', 'data' => $data];
            return response()->json($response, 200);
        } else {
            $response = ['status' => 0, 'message' => 'Artifact Not Saved', 'data' => []];
            return response()->json($response, 200);
        }
    }



    public function transfer_artifact_from_temp_to_main(Request $request)
    {
        //  echo"hello";
        //  die; 
        if (!$request->header('Authorizations') || $request->header('Authorizations') == "") {
            $data = array('status' => 0, 'message' => 'Authorizations key is required in api headers.', 'data' => array());
            return response(json_encode($data), 200);
        }
        $getUser = User::select('id', 'auth_key')->where('auth_key', $request->header('Authorizations'))->first();
        if (!$getUser) {
            $data = array('status' => 0, 'message' => 'User not found', 'data' => array());
            return response(json_encode($data), 200);
        }
        $validator = Validator::make($request->all(), [
            'temp_audit_id' => 'required',
            'audit_id' => 'required',
        ]);
        if ($validator->fails()) {
            $data = array('status' => 0, 'message' => 'Validation Errors', 'data' => $validator->errors());
            return response(json_encode($data), 200);
        } else {
            $data = [];
            $data = TempArtifact::all()
                ->where('temp_audit_id', ($request->temp_audit_id))->all();
            foreach ($data as $key => $value) {
                $movedata = new Artifact;
                $movedata->sheet_id = $value->sheet_id;
                $movedata->parameter_id = $value->parameter_id;
                $movedata->sub_parameter_id = $value->sub_parameter_id;
                $movedata->file = $value->file;
                $movedata->audit_id = $request->audit_id;
                $movedata->save();
            }
            // $data = TempArtifact::delete(where('temp_audit_id',($request->temp_audit_id)));
            foreach ($data as $value) {
                $value->delete();
            }
            return response()->json(['status' => 200, 'message' => "Artifact moved to main artifact successfully", 'data' => $movedata], 200);

        }


    }

    public function artifact_audit_update(Request $request)
    {


        if (!$request->header('Authorizations') || $request->header('Authorizations') == "") {
            $data = array('status' => 0, 'message' => 'Authorizations key is required in api headers.', 'data' => array());
            return response(json_encode($data), 200);
        }
        $getUser = User::select('id', 'auth_key')->where('auth_key', $request->header('Authorizations'))->first();
        if (!$getUser) {
            $data = array('status' => 0, 'message' => 'User not found', 'data' => array());
            return response(json_encode($data), 200);
        }
        $validator = Validator::make($request->all(), [
            'artifact_id' => 'required',
            'audit_id' => 'required'
        ]);
        if ($validator->fails()) {
            $data = array('status' => 0, 'message' => 'Validation Errors', 'data' => $validator->errors());
            return response(json_encode($data), 200);
        }
        $data = [];


        if ($request->file('totalFile')) {
            /* echo "hiii";
            die; */
            $path1 = $request->artifact_id;



        }
        // dd($data);
        if (count($data) > 0) {
            $response = array(
                'status' => 1,
                'message' => 'Artifact Saved',
                'data' => $data
            );
            return response(json_encode($response), 200);
            // return response()->json(['status'=>true,'msg'=>'artifact save','data'=>$data]);
        } else {
            $response = array(
                'status' => 0,
                'message' => 'Artifact Not Saved',
                'data' => $data
            );
            return response(json_encode($response), 200);
            // return response()->json(['status'=>false,'msg'=>'artifact not save','data'=>$data]);
        }

    }

    public function artifacts_list(Request $request)
    {
        if (!$request->header('Authorizations') || $request->header('Authorizations') == "") {
            $data = array('status' => 0, 'message' => 'Authorizations key is required in api headers.', 'data' => array());
            return response(json_encode($data), 200);
        }

        // Ensure the audit_id is present in the request and valid
        $auditId = Audit::where('id', $request->audit_id)->first();

        // Check if the audit exists
        if (!$auditId) {
            $data = array('status' => 0, 'message' => 'Invalid audit_id.', 'data' => array());
            return response(json_encode($data), 200);
        }

        // Fetch artifacts based on the audit_id and the provided parameters (sheet_id, parameter_id, sub_parameter_id)
        $query = Artifact::where('audit_id', $auditId->id);

        // Check if sheet_id, parameter_id, and sub_parameter_id are provided
        if (!empty($request->sheet_id) && !empty($request->parameter_id) && !empty($request->sub_parameter_id)) {
            $query->leftJoin('qm_sheets', 'artifacts.sheet_id', '=', 'qm_sheets.id')
                ->leftJoin('qm_sheet_parameters', 'artifacts.parameter_id', '=', 'qm_sheet_parameters.id')
                ->leftJoin('qm_sheet_sub_parameters', 'artifacts.sub_parameter_id', '=', 'qm_sheet_sub_parameters.id')
                ->where([
                    ['artifacts.sheet_id', '=', $request->sheet_id],
                    ['artifacts.parameter_id', '=', $request->parameter_id],
                    ['artifacts.sub_parameter_id', '=', $request->sub_parameter_id]
                ]);
        } else {
            // If parameters are missing, handle accordingly
            $data = array('status' => 0, 'message' => 'SheetId, ParameterId, or SubParameterId can not be empty', 'data' => array());
            return response(json_encode($data), 200);
        }

        // Execute the query
        $getArtifacts = $query->select('artifacts.*')->get();

        if (!empty($getArtifacts)) {
            // Loop through the results to format the file path
            foreach ($getArtifacts as &$getArtifacts_values) {
                $p = URL::to('/');
                $getArtifacts_values->file = $p . '/storage/app/' . $getArtifacts_values->file;
            }

            // Return the response with the artifacts
            $response = array('status' => 1, 'message' => 'Artifact List', 'data' => $getArtifacts);
            return response(json_encode($response), 200);
        } else {
            $data = array('status' => 0, 'message' => 'List not found', 'data' => array());
            return response(json_encode($data), 200);
        }
    }

    public function deleteArtifact(Request $request)
    {
        if (!$request->header('Authorizations') || $request->header('Authorizations') == "") {
            $data = array('status' => 0, 'message' => 'Authorizations key is required in api headers.', 'data' => array());
            return response(json_encode($data), 200);
        }
        $user = User::select('id', 'auth_key')->where('auth_key', $request->header('Authorizations'))->first();
        if (!$user) {
            $data = array('status' => 0, 'message' => 'User not found', 'data' => array());
            return response(json_encode($data), 200);
        }
        $validator = Validator::make($request->all(), [
            'id' => 'required'
        ]);
        if ($validator->fails()) {
            $data = array('status' => 0, 'message' => 'Artifact Id required', 'data' => $validator->errors());
            return response(json_encode($data), 200);
        } else {

            Artifact::find($request->id)->delete();

            $data = array('status' => 1, 'message' => 'Artifact deleted successfully', 'data' => array());
            return response(json_encode($data), 200);

        }

    }
}
