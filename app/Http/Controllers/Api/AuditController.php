<?php

namespace App\Http\Controllers\Api;

use App\Model\Productattribute;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\SavedAudit;
use App\SavedQcAudit;
use App\Audit;
use App\Qc;
use App\User;
use App\AdditionalResponse;
use App\QmSheet;
use App\Model\Branch;
use App\Agency;
use App\Model\BranchRepo;
use App\TempArtifact;
use App\Model\AgencyRepo;
use App\Yard;
use Auth;
use DB;
use App\AuditQc;
use App\AuditParameterResult;
use App\QcParameterResult;
use App\QmSheetParameter;
use App\QmSheetSubParameter;
use Mail;
use App\Model\City;
use App\Artifact;
use App\AuditResult;
use App\Model\AuditAllocation;
use App\QcResult;
use App\Model\Branchable;
use App\Model\Products;
use App\Model\AuditorAssign;
use App\Model\YardRepo;
use URL;
use App\RedAlert;
use Carbon\Carbon;
use Storage;
use Barryvdh\DomPDF\Facade\Pdf;


class AuditController extends Controller
{
    public function render_audit_sheet(Request $request)
    {

        //dd(all_non_scoring_obs_options(1));

        if (!$request->header('Authorizations') || $request->header('Authorizations') == "") {
            $data = array('status' => 0, 'message' => 'Authorizations key is required in api headers.', 'data' => array());
            return response(json_encode($data), 200);
        }
        $user = User::where('auth_key', $request->header('Authorizations'))->first();
        if (!$user) {
            $data = array('status' => 0, 'message' => 'User not found', 'data' => array());
            return response(json_encode($data), 200);
        }
        // $validator = Validator::make($request->all(), [
        //     'qm_sheet_id' => 'required'
        // ]);
        // if($validator->fails()) {
        //     $data=array('status'=>0,'message'=>'Validation Errors','data' => $validator->errors());
        //     return response(json_encode($data), 200);
        // }
        else {
            // $data = QmSheet::with('parameter.qm_sheet_sub_parameter')->find(Crypt::decrypt($qm_sheet_id));
            // id = 1
            $qm_sheet_id = $request->qm_sheet_id;
            $data = QmSheet::with('parameter.qm_sheet_sub_parameter')->find($qm_sheet_id);
            // $branch=Branch::all();
            // $agency=Agency::all();
            // $yard=Yard::all();
            // if($user_role = Auth::user()->roles()->first()->name == 'Quality Auditor'){
            //     $brancid_data =Branchable::distinct()->where('auditor_id',Auth::user()->id)->where('status',1)->get()->pluck('branch_id');
            //     $branch=Branch::where('lob',$data->lob)->whereIn('id',$brancid_data)->get();
            // }
            // else{

            //     $branch=Branch::where('lob',$data->lob)->get();

            // }

            $branch = Branch::where('lob', $data->lob)->orderBy('name', 'ASC')->get();

            $agency = Agency::whereIn('branch_id', $branch->pluck('id'))->orderBy('name', 'ASC')->get();

            /* print_r($agency);
            die; */

            $yard = Yard::whereIn('branch_id', $branch->pluck('id'))->orderBy('name', 'ASC')->get();

            $branchRepo = BranchRepo::whereIn('branch_id', $branch->pluck('id'))->orderBy('name', 'ASC')->get();

            $agencyRepo = AgencyRepo::whereIn('branch_id', $branch->pluck('id'))->orderBy('name', 'ASC')->get();

            // return view('audit.render_sheet',compact('qm_sheet_id','data','branch','agency','yard','branchRepo','agencyRepo'));

            $output = [
                'qm_sheet_id' => $qm_sheet_id,
                'data' => $data,
                'branch' => $branch,
                'agency' => $agency,
                'yard' => $yard,
                'branchRepo' => $branchRepo,
                'agencyRepo' => $agencyRepo
            ];

            $jsonOutput = array('status' => 1, 'message' => "Submitted Audit List.", 'data' => $output);
            return response(json_encode($jsonOutput), 200);
            // return view('audit.render_sheet',compact('qm_sheet_id','data','branch'));
        }
    }


    public function render_audit_sheet_edit(Request $request)
    {
        logger($request);
        if (!$request->header('Authorizations') || $request->header('Authorizations') == "") {
            $data = array('status' => 0, 'message' => 'Authorizations key is required in api headers.', 'data' => array());
            return response(json_encode($data), 200);
        }
        $user = User::where('auth_key', $request->header('Authorizations'))->first();
        if (!$user) {
            $data = array('status' => 0, 'message' => 'User not found', 'data' => array());
            return response(json_encode($data), 200);
        }

        $validator = Validator::make($request->all(), [
            'audit_id' => 'required'
        ]);
        if ($validator->fails()) {
            $data = array('status' => 0, 'message' => 'Validation Errors', 'data' => $validator->errors());
            return response(json_encode($data), 200);
        }

        //dd(all_non_scoring_obs_options(1));

        $result = Audit::where('id', $request->audit_id)->first();
        $subProducts = Productattribute::where('product_id', $result->product_id)
            ->pluck('product_attribute_name', 'id')->toArray();
        //   echo '<pre>'; print_r($subProducts); die;
        if (!empty($subProducts)) {
            $subProducts =  $subProducts;
        } else {
            $subProducts =  [];
        }
        $full_data = array();
        $full_data['sheet_detail'] = QmSheet::find($result->qm_sheet_id);
        /* $full_data['sheet_detail'][''] = $result-> */
        $parameters = QmSheetParameter::where('qm_sheet_id', $result->qm_sheet_id)->get();

        foreach ($parameters as $key => $para) {
            $full_data['sheet_detail']['parameter'][$key] = $para;
            $audit_parameter_result = AuditParameterResult::where('parameter_id', $para['id'])->where('audit_id', $request->audit_id)->first();
            $full_data['sheet_detail']['parameter'][$key]['orignal_weight'] = $audit_parameter_result->orignal_weight ?? '';
            $full_data['sheet_detail']['parameter'][$key]['temp_weight'] = $audit_parameter_result->temp_weight ?? '';
            $full_data['sheet_detail']['parameter'][$key]['with_fatal_score'] = $audit_parameter_result->with_fatal_score ?? '';
            $full_data['sheet_detail']['parameter'][$key]['without_fatal_score'] = $audit_parameter_result->without_fatal_score ?? '';
            $full_data['sheet_detail']['parameter'][$key]['with_fatal_score_per'] = $audit_parameter_result->with_fatal_score_per ?? '';
            $full_data['sheet_detail']['parameter'][$key]['without_fatal_score_pre'] = $audit_parameter_result->without_fatal_score_pre ?? '';
            $full_data['sheet_detail']['parameter'][$key]['is_critical'] = $audit_parameter_result->is_critical ?? '';

            $sub_parameter = QmSheetSubParameter::where('qm_sheet_parameter_id', $para['id'])->get();
            $sub = array();
            foreach ($sub_parameter as $key1 => $sub_para) {
                $sub['subparameter'][$key1] = $sub_para;
                $audit_result = AuditResult::where('sub_parameter_id', $sub_para['id'])->where('audit_id', $request->audit_id)->first();
                $sub['subparameter'][$key1]['selected_option'] = $audit_result['selected_option'] ?? '';
                $sub['subparameter'][$key1]['is_critical'] = $audit_result['is_critical'] ?? '';
                $sub['subparameter'][$key1]['score'] = $audit_result['score'] ?? '';
                $sub['subparameter'][$key1]['failure_reason'] = $audit_result['failure_reason'] ?? '';
                $sub['subparameter'][$key1]['remark'] = $audit_result['remark'] ?? '';
                $sub['subparameter'][$key1]['is_percentage'] = $audit_result['is_percentage'] ?? '';
                $sub['subparameter'][$key1]['selected_per'] = $audit_result['selected_per'] ?? '';
                $sub['subparameter'][$key1]['option_selected'] = $audit_result['option_selected'] ?? '';
            }
            /* echo json_encode($sub);
            die; */
            $full_data['sheet_detail']['parameter'][$key]['subparameter'] = $sub['subparameter'] ?? '';
        }

        $output = [
            'audit_details' => $result,
            'subProducts' => $subProducts,
            'sheet_details' => $full_data['sheet_detail']
        ];

        $jsonOutput = array('status' => 1, 'message' => "Submitted Audit List.", 'data' => $output);
        return response(json_encode($jsonOutput), 200);
    }



    public function auditResult(Request $request)
    {
        // Validate Authorization header
        $authKey = $request->header('Authorizations');
        if (!$authKey) {
            return response()->json([
                'status' => 0,
                'message' => 'Authorizations key is required in API headers.',
                'data' => []
            ], 400);
        }

        // Validate user
        $getUser = User::where('auth_key', $authKey)->first();
        if (!$getUser) {
            return response()->json([
                'status' => 0,
                'message' => 'User not found',
                'data' => []
            ], 404);
        }

        // Validate audit_id param
        if (!$request->has('audit_id')) {
            return response()->json([
                'status' => 0,
                'message' => 'audit_id is required in the request.',
                'data' => []
            ], 400);
        }

        $audit = DB::table('audits')->where('id', $request->audit_id)->first();
        if (!$audit) {
            return response()->json([
                'status' => 0,
                'message' => 'Audit not found',
                'data' => []
            ], 404);
        }

        $toEmails = $audit->agency_email;
        $audit_cycle = DB::table('audit_cycles')->where('id', $audit->audit_cycle_id)->value('name');
        $audit_type = DB::table('qm_sheets')->where('id', $audit->qm_sheet_id)->value('type');

        $typeTableMap = [
            'agency'       => ['table' => 'agencies',      'column' => 'agency_id'],
            'branch'       => ['table' => 'branch',        'column' => 'branch_id'],
            'yard'         => ['table' => 'yard',          'column' => 'yard_id'],
            'agency_repo'  => ['table' => 'agency_repo',   'column' => 'agency_repo_id'],
            'branch_repo'  => ['table' => 'branch_repo',   'column' => 'branch_repo_id'],
            'yard_repo'    => ['table' => 'yard_repo',     'column' => 'yard_repo_id'],
        ];

        if (isset($typeTableMap[$audit_type])) {
            $table = $typeTableMap[$audit_type]['table'];
            $column = $typeTableMap[$audit_type]['column'];
            $id = $audit->{$column};
            $agency_name = DB::table($table)->where('id', $id)->value('name');
        } else {
            $agency_name = '-';
        }

        $product_name = DB::table('products')->where('id', $audit->product_id)->value('name');
        $subject = "Process Review Rating Period for {$audit_cycle} ({$agency_name} - {$product_name})";
        $clientName = User::where('id', $getUser->client_id)->value('name');

        $lavel4Emails = [];
        $lavel5Emails = [];

        if (!empty($audit->lavel_4)) {
            $lavel4Ids = explode(',', $audit->lavel_4);
            $lavel4Users = User::whereIn('id', $lavel4Ids)->pluck('email')->toArray();
            $lavel4Emails = $lavel4Users;
        }
        if (!empty($audit->lavel_5)) {
            $lavel5Ids = explode(',', $audit->lavel_5);
            $lavel5Users = User::whereIn('id', $lavel5Ids)->pluck('email')->toArray();
            $lavel5Emails = $lavel5Users;
        }

        // Get email of created_by user for CC (if any)
        $process_review_agency_email = null;
        if ($getUser->created_by) {
            $process_review_agency_email = User::where('id', $getUser->created_by)->value('email');
        }


        if ($audit->client_id ==  15) {
            $mv = ['arjun.verma@qdegrees.com', 'vaibhav.amer@qdegrees.com',  'anup.tiwari@moneyview.in',  'oberoi.puneet@moneyview.in',  'mohammed.danish@moneyview.in', 'manvi.ojha@moneyview.in'];
        } elseif ($audit->client_id ==  74) {
            // $mv = [];
            $mv = ['Snehal.Nimkar@fibe.in', 'piyali.banerjee@fibe.in', 'Pradip.kumar@fibe.in', 'abhishek.gupta@qdegrees.com', 'joicy.kunjuman@qdegrees.com', 'Vaibhav.Amer@qdegrees.com'];
        } else {
            $mv = [];
        }
        $ccEmails = array_filter(array_merge($lavel4Emails, $lavel5Emails, [$process_review_agency_email]));
        $allCcEmails = array_merge((array) $ccEmails, $mv);



        // Get PDF path
        $pdfContent = DB::table('audit_reports')->where('audit_id', $audit->id)->value('audit_result_pdf');

        if (!$pdfContent) {
            return response()->json([
                'status' => 0,
                'message' => 'Audit result PDF not found.',
                'data' => []
            ], 404);
        }

        $pdfFullPath = storage_path('app/public/' . $pdfContent);

        if (!file_exists($pdfFullPath)) {
            return response()->json([
                'status' => 0,
                'message' => 'PDF file not found on server.',
                'data' => []
            ], 404);
        }

        try {
            Mail::send([], [], function ($message) use ($toEmails, $allCcEmails, $subject, $pdfFullPath, $clientName, $agency_name) {
                $message->from('auditmitr@qdegrees.com', 'Audit Team')
                    ->to($toEmails)
                    ->cc($allCcEmails)
                    ->subject($subject)
                    ->attach($pdfFullPath, ['as' => 'Audit_Result.pdf'])
                    ->html("
                    <p>Dear {$agency_name},</p>
                    <p>Greetings!</p>
                    <p>Thank you for your co-operation & assistance extended to the Process Reviewer in conducting the process review successfully.</p>
                    <p>Please find attached the audit result for your agency.</p>
                    <p>Warm Regards,<br>
                        Audit Team<br>
                        <a href='https://www.qdegrees.com/' target='_blank'>QDegrees Services</a></p>
                ",);
            });
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Failed to send email: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'status' => 1,
            'message' => 'Audit result email sent successfully.',
        ], 200);
    }

    public function auditClosure(Request $request)
    {
        // Validate Authorization header
        $authKey = $request->header('Authorizations');
        if (!$authKey) {
            return response()->json([
                'status' => 0,
                'message' => 'Authorizations key is required in API headers.',
                'data' => []
            ], 400);
        }

        // Validate user
        $user = User::where('auth_key', $authKey)->first();
        if (!$user) {
            return response()->json([
                'status' => 0,
                'message' => 'User not found',
                'data' => []
            ], 404);
        }

        // Validate audit_id presence in request
        if (!$request->has('audit_id')) {
            return response()->json([
                'status' => 0,
                'message' => 'audit_id is required in request.',
                'data' => []
            ], 400);
        }

        // Get audit info
        $audit = DB::table('audits')->where('id', $request->audit_id)->first();
        if (!$audit) {
            return response()->json([
                'status' => 0,
                'message' => 'Audit not found',
                'data' => []
            ], 404);
        }

        // Get related info
        $process_review_month = DB::table('audit_cycles')->where('id', $audit->audit_cycle_id)->value('name');
        $agency_details = DB::table('agencies')->where('id', $audit->agency_id)->first();
        $agencyEmail = $audit->agency_email;
        $closureId = DB::table('closure_audits')->where('audit_id', $audit->id)->value('id');
        $pdfContent = DB::table('audit_reports')->where('audit_id', $audit->id)->value('closure_pdf');
        $lavel4Emails = [];
        $lavel5Emails = [];

        if (!empty($audit->lavel_4)) {
            $lavel4Ids = explode(',', $audit->lavel_4);
            $lavel4Users = User::whereIn('id', $lavel4Ids)->pluck('email')->toArray();
            $lavel4Emails = $lavel4Users;
        }
        if (!empty($audit->lavel_5)) {
            $lavel5Ids = explode(',', $audit->lavel_5);
            $lavel5Users = User::whereIn('id', $lavel5Ids)->pluck('email')->toArray();
            $lavel5Emails = $lavel5Users;
        }

        // Get email of created_by user for CC (if any)
        // $process_review_agency_email = null;
        // if ($getUser->created_by) {
        //     $process_review_agency_email = User::where('id', $getUser->created_by)->value('email');
        // }

        if (!$pdfContent) {
            return response()->json([
                'status' => 0,
                'message' => 'Closure PDF not found for the audit.',
                'data' => []
            ], 404);
        }

        // Construct full URL or path to PDF
        // Assuming $pdfContent stores relative path like 'reports/closure.pdf' in storage/app/public/reports/closure.pdf
        $pdfFullPath = storage_path('app/public/' . $pdfContent);

        if (!file_exists($pdfFullPath)) {
            return response()->json([
                'status' => 0,
                'message' => 'PDF file not found on server.',
                'data' => []
            ], 404);
        }

        // Prepare email variables
        $subject = "Audit Closure Form for Audit Agency: {$agency_details->name}";
        $randomString = DB::table('closure_audits')->where('audit_id', $audit->id)->value('link');
        $closureFormLink = url("audit-closure-justification/{$closureId}/{$audit->id}/{$randomString}");
        if ($audit->client_id == 15) {
            $client = ['mohammed.danish@moneyview.in',  'manvi.ojha@moneyview.in'];
        } elseif ($audit->client_id == 74) {
            $client = ['Snehal.Nimkar@fibe.in', 'piyali.banerjee@fibe.in', 'Pradip.kumar@fibe.in','abhishek.gupta@qdegrees.com', 'joicy.kunjuman@qdegrees.com', 'Vaibhav.Amer@qdegrees.com'];
        } else {
            $client = [];
        }
        $ccEmails = array_filter(array_merge($lavel4Emails, $lavel5Emails));
        $allCcEmails = array_merge((array) $ccEmails, $client);

        $client_id = $audit->client_id;
        $tat = DB::table('users')->where('id', $client_id)->value('closure_link_days');

        // Send email
        try {
            Mail::send('closure.audit_closure_mail', [
                'closureFormLink' => $closureFormLink,
                'process_review_month' => $process_review_month,
                'agency_name' => $agency_details->name,
                'tat' => $tat,
            ], function ($message) use ($agencyEmail, $subject, $pdfFullPath, $allCcEmails) {
                $message->from('auditmitr@qdegrees.com', 'Audit Team')
                    ->to($agencyEmail)
                    ->cc($allCcEmails)
                    ->subject($subject)
                    ->attach($pdfFullPath, ['as' => 'UNSATISFACTORY_PARAMETERS.pdf']);
            });
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Failed to send email: ' . $e->getMessage(),
            ], 500);
        }

        // Success response
        return response()->json([
            'status' => 1,
            'message' => 'Audit closure email sent successfully.',
        ], 200);
    }


    public function getProduct(Request $request)
    {
        if (!$request->header('Authorizations') || $request->header('Authorizations') == "") {
            $data = array('status' => 0, 'message' => 'Authorization key is required in api headers.', 'data' => array());
            return response(json_encode($data), 200);
        }
        $user = User::where('auth_key', $request->header('Authorizations'))->first();
        if (!$user) {
            $data = array('status' => 0, 'message' => 'User not found', 'data' => array());
            return response(json_encode($data), 200);
        }
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            $data = array('status' => 0, 'message' => 'Validation Errors', 'data' => $validator->errors());
            return response(json_encode($data), 200);
        } else {

            $id = $request->id;
            $agency = Agency::with('user')->find($id);
            $productIds = Branchable::where('agency_id', $agency->id)->get()->pluck('product_id')->toArray();
            $branchable = Products::whereIn('id', array_unique($productIds))->get();
            $jsonOutput = array('status' => 1, 'message' => 'Product List for for dropdown', 'data' => $branchable);

            return response(json_encode($jsonOutput), 200);
            // return response()->json(['data'=>$branchable]);
        }
    }

    public function getSubProduct(Request $request)
    {
        if (!$request->header('Authorizations') || $request->header('Authorizations') == "") {
            $data = array('status' => 0, 'message' => 'Authorization key is required in api headers.', 'data' => array());
            return response(json_encode($data), 200);
        }
        $user = User::where('auth_key', $request->header('Authorizations'))->first();
        if (!$user) {
            $data = array('status' => 0, 'message' => 'User not found', 'data' => array());
            return response(json_encode($data), 200);
        }
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
        ]);
        if ($validator->fails()) {
            $data = array('status' => 0, 'message' => 'Validation Errors', 'data' => $validator->errors());
            return response(json_encode($data), 200);
        } else {
            $product_id = $request->product_id;
            $productattributes = DB::table('productattributes')->where('product_id', $product_id)->get();
            $jsonOutput = array('status' => 1, 'message' => 'Sub Product List for for dropdown', 'data' => $productattributes);
            return response(json_encode($jsonOutput), 200);
        }
    }


    public function renderBranch(Request $request)
    {
        if (!$request->header('Authorizations') || $request->header('Authorizations') == "") {
            $data = array('status' => 0, 'message' => 'Authorizations key is required in api headers.', 'data' => array());
            return response(json_encode($data), 200);
        }
        $user = User::where('auth_key', $request->header('Authorizations'))->first();
        if (!$user) {
            $data = array('status' => 0, 'message' => 'User not found', 'data' => array());
            return response(json_encode($data), 200);
        }
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'type' => 'required',
            'product_id' => 'required'
        ]);
        if ($validator->fails()) {
            $data = array('status' => 0, 'message' => 'Validation Errors', 'data' => $validator->errors());
            return response(json_encode($data), 200);
        } else {

            $id = $request->id;
            $type = $request->type;
            $product_id = $request->product_id;
            $agency = [];
            $yard = [];
            $AgencyRepo = [];
            $BranchRepo = $myData = $managerData = [];
            $code = '';
            $bucket = '';
            // dd($type);
            if ($type == 'branch') {
                $branchable = Branch::with(['branchable' => function ($q) use ($product_id) {
                    $q->where('product_id', $product_id)->latest();
                }, 'city'])->where('id', $id)->first();
            } else if ($type == 'agency') {
                $agency = Agency::with('user')->find($id);
                $branchable = Branch::with(['branchable' => function ($q) use ($product_id) {
                    $q->where('product_id', $product_id);
                }, 'city'])->where('id', $agency->branch_id)->first();
            } else if ($type == 'yard' || $type == 'repo_yard') {
                $yard = Yard::with('user')->find($id);
                $agency = Agency::with('user')->find($yard->agency_id);
                $branchable = Branch::with(['branchable' => function ($q) use ($product_id) {
                    $q->where('product_id', $product_id);
                }, 'city'])->where('id', $yard->branch_id)->first();
            } else if ($type == 'branch_repo') {
                $BranchRepo = BranchRepo::find($id);
                $branchable = Branch::with(['branchable' => function ($q) use ($product_id) {
                    $q->where('product_id', $product_id)->latest();
                }, 'city'])->where('id', $BranchRepo->branch_id)->first();
            } else if ($type == 'agency_repo') {
                $AgencyRepo = AgencyRepo::find($id);
                $branchable = Branch::with(['branchable' => function ($q) use ($product_id) {
                    $q->where('product_id', $product_id);
                }, 'city'])->where('id', $AgencyRepo->branch_id)->first();
            }
            foreach ($branchable->branchable as $item) {
                $mydata[$item->type] = $item;
                foreach ($mydata as $collectionmanager) {
                    $managerData[$item->type] = array('id' => $collectionmanager->user->id, 'name' => $collectionmanager->user->name, 'employee_id' => $collectionmanager->user->employee_id, 'bucket' => $collectionmanager->bucket);
                }
            }

            $output = [
                'branchable' => $branchable,
                'type' => $type,
                'agency' => $agency,
                'yard' => $yard,
                'agencyRepo' => $AgencyRepo,
                'branchRepo' => $BranchRepo,
                'managers_data' => $managerData
            ];

            $jsonOutput = array('status' => 1, 'message' => "Render Branch", 'data' => $output);
            return response(json_encode($jsonOutput), 200);
        }
    }

    public function store_audit(Request $request)
    {

        if (!$request->header('Authorizations') || $request->header('Authorizations') == "") {
            $data = array('status' => 0, 'message' => 'Authorizations key is required in api headers.', 'data' => array());
            return response(json_encode($data), 200);
        }
        $getUser = User::where('auth_key', $request->header('Authorizations'))->first();
        if (!$getUser) {
            $data = array('status' => 0, 'message' => 'User not found', 'data' => array());
            return response(json_encode($data), 200);
        }

        $audit_agency_id = User::where('email', $getUser->created_by)->pluck('id')->first();
        $process_review_agency_email  = $getUser->created_by;

        DB::beginTransaction();
        // dd($request->submission_data);

        try {
            if (isset($request->submission_data['audit_id'])) {
                logger($request);
                $user_role = $getUser->roles()->first()->name;
                $latlong = explode(" ", $request->submission_data['geotag']);
                // dd($request->all());
                // calculate score following code done by shailendra kumar
                $total = 0;
                $parameter_total = 0;
                $audit_crital = 0;
                $pera_score = [];
                foreach ($request->parameters as $key => $para) {
                    $pera_total = 0;
                    $pera_fatal = 0;
                    $pera_without_fatal = 0;
                    $pera_is_critical = 0;
                    $total_temp_weight = 0;
                    $pera_fatal_per = 0;
                    $pera_without_fatal_par = 0;
                    $is_critical_para = 0;
                    $subtotal = 0;
                    foreach ($para['subs'] as $sub_para) {
                        $paramterValue = 0;
                        if ($sub_para['temp_weight'] != "N/A") {
                            $total_temp_weight = $total_temp_weight + $sub_para['temp_weight'];
                        } else {
                            $total_temp_weight = $total_temp_weight + 0;
                        }
                        if ($sub_para['option'] != 'N/A') {
                            $paramterValue = $sub_para['score'];
                            if ($sub_para['score'] == 'N/A') {
                                $pera_fatal = $pera_fatal + 0;
                            } else {
                                $pera_fatal = $pera_fatal + $sub_para['score'];
                            }
                        }
                        if ($sub_para['option'] != 'Critical') {
                            if ($sub_para['score'] == 'N/A') {
                                $subtotal = $subtotal + 0;
                            } else {
                                $subtotal = $subtotal + $sub_para['score'];
                                $parameter_total = $parameter_total + $paramterValue;
                            }
                        } else {
                            $subtotal = 0;
                            $is_critical_para = 1;
                            $audit_crital = 1;
                            $parameter_total = $parameter_total + $paramterValue;
                            break;
                        }
                    }
                    $total = $total + $subtotal;
                    if ($total_temp_weight != 0) {
                        $fat_score = round(($pera_fatal / $total_temp_weight) * 100);
                        $wfat_score = round(($subtotal / $total_temp_weight) * 100);
                    } else {
                        $fat_score = 0;
                        $wfat_score = 0;
                    }
                    $para_id = $para['id'];
                    $pera_score[$para_id]['pera_total'] = $subtotal;
                    $pera_score[$para_id]['total_temp_weight'] = $total_temp_weight;
                    $pera_score[$para_id]['pera_fatal'] = $pera_fatal;
                    $pera_score[$para_id]['pera_without_fatal'] = $subtotal;
                    $pera_score[$para_id]['pera_fatal_per'] = $fat_score;
                    $pera_score[$para_id]['pera_without_fatal_par'] = $wfat_score;
                    $pera_score[$para_id]['pera_is_critical'] = $is_critical_para;
                }
                $overall_score = $total;
                $new_ar = Audit::find($request->submission_data['audit_id']);
                $new_ar->latitude = $latlong[0];
                $new_ar->longitude = $latlong[2];
                $new_ar->qm_sheet_id = $request->submission_data['qm_sheet_id'];
                $new_ar->audit_date_by_aud = $request->submission_data['audit_date_by_aud'];
                $new_ar->audit_cycle_id = $request->submission_data['audit_cycle_id'];
                $new_ar->process_review_agency_email = $process_review_agency_email;
                $new_ar->audit_agency_id = $audit_agency_id;
                $new_ar->audited_by_id = $getUser->id;
                $new_ar->agency_mobile = $request->submission_data['agency_phone'];
                $new_ar->agency_email = $request->submission_data['agency_email'];
                $new_ar->is_critical = $audit_crital;
                $new_ar->overall_score = $overall_score;
                if (!empty($request->submission_data['present_auditor'])) {
                    $new_ar->present_auditor = $request->submission_data['present_auditor'];
                } else {
                    $new_ar->present_auditor = '';
                }


                $new_ar->branch_id = (isset($request->submission_data['branch_id'])) ? $request->submission_data['branch_id'] : null;
                $new_ar->agency_id = (isset($request->submission_data['agency_id'])) ? $request->submission_data['agency_id'] : null;
                $new_ar->yard_id = (isset($request->submission_data['yard_id'])) ? $request->submission_data['yard_id'] : null;
                $new_ar->branch_repo_id = (isset($request->submission_data['branch_repo_id'])) ? $request->submission_data['branch_repo_id'] : null;
                $new_ar->agency_repo_id = (isset($request->submission_data['agency_repo_id'])) ? $request->submission_data['agency_repo_id'] : null;
                $new_ar->product_id = (isset($request->submission_data['product_id'])) ? $request->submission_data['product_id'] : null;
                $new_ar->collection_manager_email = (isset($request->submission_data['collection_manager_email'])) ? $request->submission_data['collection_manager_email'] : null;
                $new_ar->agency_manager_email = (isset($request->submission_data['agency_manager_email'])) ? $request->submission_data['agency_manager_email'] : null;
                $new_ar->yard_manager_email = (isset($request->submission_data['yard_manager_email'])) ? $request->submission_data['yard_manager_email'] : null;
                $new_ar->collection_manager_id = (isset($request->submission_data['collection_manager_id'])) ? $request->submission_data['collection_manager_id'] : null;
                $new_ar->client_id = $getUser->client_id;
                $new_ar->status = ($request->submission_data['status'] == 'save') ? 5 : 1;
                $new_ar->save();
                // added for qc audit records
                if (isset($request->submission_data['agency_id'])) {
                    Agency::where('id', $new_ar->agency_id)->update([
                        'agency_manager' => $request->submission_data['agency_manager'],
                        'agency_phone'   => $request->submission_data['agency_phone'],
                        'email'          => $request->submission_data['agency_email'],
                    ]);
                }

                if (isset($request->submission_data['agency_id'])) {
                    // Get the agency_id from the request
                    $agencyId = $request->submission_data['agency_id'];
                    // Fetch only the necessary fields from the agencies table
                    $agency = Agency::where('id', $agencyId)
                        ->select('address', 'location', 'city_id', 'product_id', 'sub_product_id')
                        ->first();
                    if ($agency) {
                        $city = City::where('id', $agency->city_id)->first();
                        // Fetch product name from the products table
                        $product = Products::where('id', $agency->product_id)->first();
                        // Fetch sub-product names (product attributes) from productattributes table
                        $subProductIds = explode(',', $agency->sub_product_id); // Assuming it's a comma-separated string
                        $subProducts = ProductAttribute::whereIn('id', $subProductIds)->pluck('product_attribute_name')->toArray();
                        // Format sub-product names as a comma-separated string
                        $subProductNames = implode(', ', $subProducts);
                        // Update the audits table with fetched details
                        Audit::where('id', $new_ar->id)->update([
                            'agency_address' => $agency->address,
                            'agency_location' => $agency->location,
                            'agency_city' => $city ? $city->name : null, // City name
                            'agency_product' => $product ? $product->name : null, // Product name
                            'agency_sub_products' => $subProductNames, // Sub-product names
                        ]);
                    }
                }
                if ($request->submission_data['status'] == 'submit') {
                    SavedAudit::where(['audit_id' => $new_ar->id, 'status' => 1])->delete();
                }

                /* if(isset($request->submission_data['agency_id']) && $request->submission_data['agency_manager'] != '')
                {
                    Agency::where('id',$new_ar->agency_id)->update(['agency_manager'=>$request->submission_data['agency_manager'],'agency_phone'=>$request->submission_data['agency_phone']]);
                } */
                //added by  nisha for change status in branchable if audit submitting

                if ($request->submission_data['status'] == 'save') {
                    SavedAudit::create(['audit_id' => $new_ar->id, 'status' => 1]);
                }
                // DB::enableQueryLog();
                if (isset($idupdate)) {
                    $id4update_branchable_status = DB::table('branchables')
                        ->where('branch_id', $idupdate)->where('status', 1)->where('product_id', $request->submission_data['product_id'])
                        ->where('type', 'Collection_Manager')
                        ->where('manager_id', $request->submission_data['collection_manager_id'])
                        ->update(['status' => 2]);
                }

                if (isset($request->submission_data['artifactIds'])) {
                    $artifactIds = json_decode($request->submission_data['artifactIds']);
                    foreach ($artifactIds as $item) {
                        Artifact::where('id', $item)->update(['audit_id' => $new_ar->id]);
                    }
                }

                if ($request->submission_data['status'] == 'submit') {
                    $user = User::find($new_ar->lavel_3); // Assuming lavel_3 is the recipient
                    if ($user && $user->email) {
                        $agency_email = $request->submission_data['agency_email'];
                        $toEmails = [];
                        if ($agency_email) {
                            $toEmails[] = $agency_email;
                        }
                        $toEmails[] = $user->email; // Corrected line: Add lavel_3 email

                        // Fetch emails for lavel_4 and lavel_5
                        $lavel4Emails = [];
                        $lavel5Emails = [];

                        // Fetch users based on lavel_4 if it exists
                        if (!empty($new_ar->lavel_4)) {
                            $lavel4Ids = explode(',', $new_ar->lavel_4);
                            $lavel4Users = User::whereIn('id', $lavel4Ids)->get(['email']);
                            $lavel4Emails = $lavel4Users->pluck('email')->toArray();
                        }

                        // Fetch users based on lavel_5 if it exists
                        if (!empty($new_ar->lavel_5)) {
                            $lavel5Ids = explode(',', $new_ar->lavel_5);
                            $lavel5Users = User::whereIn('id', $lavel5Ids)->get(['email']);
                            $lavel5Emails = $lavel5Users->pluck('email')->toArray();
                        }

                        // Merge lavel_4 and lavel_5 emails
                        // $rbl_bank_email = 'CollectionProcessReview@rblbank.com';
                        $ccEmails = array_merge($lavel4Emails, $lavel5Emails, [$process_review_agency_email]);

                        // Prepare data for the PDF
                        $agency = DB::table('agencies')->where('id', $request->submission_data['agency_id'])->first();
                        $product = DB::table('products')->where('id', $request->submission_data['product_id'])->first();
                        $audit_cycles = DB::table('audit_cycles')->where('id', $new_ar->audit_cycle_id)->first();
                        $clientName = User::where('id', $getUser->client_id)->value('name');
                        $clientEmail = User::where('id', $getUser->client_id)->value('email');
                        $audit_agency_email = $getUser->created_by;
                        $audit_agency_name = User::where('email', $audit_agency_email)->value('name');

                        $pdfData = [
                            'Audit_agency' => 'QDegrees',
                            'grade' => $request->submission_data['grade'] ?? null,
                            'score_percentage' => $request->submission_data['with_fatal_score_per'] ?? null,
                            'overall_score' => $request->submission_data['overall_score'] ?? null,
                            'agency_name' => $agency->name ?? null,
                            'location' => $agency->location ?? null,
                            'product_name' => $product->name ?? null,
                            'auditor_id' => $getUser->name,
                            'audit_cycle' => $audit_cycles->name ?? null,
                            'audit_date' => date('F Y', strtotime($request->submission_data['audit_date_by_aud'])),
                            'client_name' => $clientName,
                            'client_email' => $clientEmail,
                            'audit_agency_name' => $audit_agency_name,
                            'manager_email' => $getUser->client_id == 15 ? 'mohammed.danish@moneyview.in' : '',
                        ];

                        // Generate PDF
                        $pdf = Pdf::loadView('audit.audit_result_pdf', $pdfData);
                        $pdfContent = $pdf->output();

                        // Create a month-wise folder for storing the PDF
                        $folderPath = 'audit_reports/' . date('Y') . '/' . date('F');
                        $fileName = 'Audit_Result_' . time() . '.pdf';
                        $fullPath = $folderPath . '/' . $fileName;

                        // Store the PDF file in the public storage
                        Storage::disk('public')->put($fullPath, $pdfContent);

                        $existingReport = DB::table('audit_reports')->where('audit_id', $new_ar->id)->first();

                        // Save the file path and audit details to the database
                        if ($existingReport) {
                            // If the entry exists, update the file path for audit_result_pdf
                            DB::table('audit_reports')
                                ->where('audit_id', $new_ar->id)
                                ->update([
                                    'audit_result_pdf' => $fullPath,
                                    'updated_at' => now(),
                                ]);
                        } else {
                            // If the entry does not exist, insert a new entry
                            DB::table('audit_reports')->insert([
                                'audit_id' => $new_ar->id,
                                'audit_date' => $request->submission_data['audit_date_by_aud'],
                                'agency_id' => $request->submission_data['agency_id'],
                                'checksheet_pdf' => null,
                                'audit_result_pdf' => $fullPath,
                                'closure_pdf' => null,
                                'process_review_period' => $audit_cycles->name ?? null,
                                'user_id' => $getUser->id,
                                'created_at' => now(),
                                'updated_at' => now(),
                                'client_id' => $getUser->client_id,
                            ]);
                        }


                        $subject = "Process Review Rating Period for " . $pdfData['audit_cycle'] . " (" . $pdfData['agency_name'] . " - " . $pdfData['product_name'] . ")";
                        $clientName = User::where('id', $getUser->client_id)->value('name');

                        // Send the email with CC
                        Mail::send([], [], function ($message) use ($toEmails, $ccEmails, $subject, $pdfContent, $clientName) {
                            $message->from('auditmitr@qdegrees.com', 'Audit Team')
                                ->to($toEmails)
                                ->cc($ccEmails) // Add CC recipients
                                ->subject($subject)
                                ->attachData($pdfContent, 'Audit_Result.pdf', [
                                    'mime' => 'application/pdf',
                                ])
                                ->html(
                                    "<p>Dear Associate,</p>
                                         <p>Greetings!</p>
                                         <p>Thank you for your co-operation & assistance extended to the Process Reviewer in conducting the process review successfully.</p>
                                         <p>Please find attached the audit result for your agency.</p>
                                         <p>Warm Regards,<br>
                        Audit Team<br>
                        <a href='https://www.qdegrees.com/' target='_blank'>QDegrees Services</a></p>"
                                    //       'text/html'
                                );
                        });
                    }
                }

                if ($new_ar->id) {
                    // store parameter wise data
                    foreach ($request->parameters as $key => $value) {
                        $para = AuditParameterResult::where('audit_id', $request->submission_data['audit_id'])
                            ->where('parameter_id', $value['id'])->first();

                        $new_arb = AuditParameterResult::find($para->id);
                        $new_arb->audit_id =  $new_ar->id;
                        $new_arb->parameter_id = $value['id'];
                        $new_arb->qm_sheet_id = $request->submission_data['qm_sheet_id'];
                        $new_arb->orignal_weight = ($value['parameter_weight'] != null) ? $value['parameter_weight'] : 0;
                        $new_arb->temp_weight = $pera_score[$value['id']]['total_temp_weight'];
                        $new_arb->with_fatal_score = $pera_score[$value['id']]['pera_fatal'];
                        $new_arb->without_fatal_score = $pera_score[$value['id']]['pera_without_fatal'];
                        $new_arb->is_critical = $pera_score[$value['id']]['pera_is_critical'];
                        // $new_arb->without_fatal_score = $value['score_without_fatal'];

                        if ($pera_score[$value['id']]['total_temp_weight'] != 0) {
                            $new_arb->with_fatal_score_per = ($pera_score[$value['id']]['pera_fatal'] / $pera_score[$value['id']]['total_temp_weight']) * 100;
                            // $new_arb->without_fatal_score_pre = ($value['score_without_fatal'] / $value['temp_total_weightage'])*100;
                            $new_arb->without_fatal_score_pre = ($pera_score[$value['id']]['pera_without_fatal'] / $pera_score[$value['id']]['total_temp_weight']) * 100;
                        }

                        $new_arb->save();

                        if (isset($value['subs']) && count($value['subs']) > 0)
                        // store sub parameter wise data
                        {
                            foreach ($value['subs'] as $key_sb => $value_sb) {
                                if ($value_sb['temp_weight']); {
                                    $sub_para = AuditResult::where('audit_id', $request->submission_data['audit_id'])
                                        ->where('sub_parameter_id', $value_sb['id'])->first();
                                    $new_arc = AuditResult::find($sub_para->id);
                                    $new_arc->audit_id =  $new_ar->id;
                                    $new_arc->parameter_id = $value['id'];
                                    $new_arc->sub_parameter_id = $value_sb['id'];
                                    $new_arc->selected_option = ($value_sb['temp_weight'] != 'Critical') ? $value_sb['temp_weight'] : 0;
                                    $new_arc->option_selected = (isset($value_sb['option'])) ? $value_sb['option'] : null;
                                    $new_arc->is_critical = ($value_sb['temp_weight'] != 'Critical') ? 0 : 1;
                                    if ($value_sb['score'] != 'rating') {
                                        $new_arc->score = ($value_sb['score'] != 'Critical') ? $value_sb['score'] : 0;
                                        $new_arc->is_percentage = $value_sb['is_percentage'];
                                        $new_arc->selected_per = ($value_sb['selected_per'] != 'select percentage') ? $value_sb['selected_per'] : null;
                                    } else {

                                        $new_arc->score = ($value_sb['score'] != 'Critical') ? $value_sb['temp_weight'] : 0;

                                        $new_arc->is_percentage = $value_sb['is_percentage'];

                                        $new_arc->selected_per = ($value_sb['selected_per'] != 'select percentage') ? $value_sb['selected_per'] : null;
                                    }



                                    $new_arc->remark = $value_sb['remark'];

                                    $new_arc->save();
                                }
                            }
                        }
                    }
                }

                // Commit Transaction
                DB::commit();

                if ($request->submission_data['status'] == 'submit') {

                    //closure mail work
                    $auditId = $new_ar->id;
                    $excludedSubParameterIds = [42, 62, 82, 133];

                    // Fetch all unset parameters (including the excluded ones for validation)
                    $allUnsetParams = DB::table('audit_results')
                        ->join('qm_sheet_sub_parameters', 'audit_results.sub_parameter_id', '=', 'qm_sheet_sub_parameters.id')
                        ->where('audit_results.audit_id', $auditId)
                        ->where('audit_results.option_selected', 'Unsatisfactory')
                        ->select('qm_sheet_sub_parameters.sub_parameter as parameter_name', 'audit_results.*', 'audit_results.remark as remarks')
                        ->get();

                    // Filter out the excluded parameters for the PDF and further processing
                    $unsetParamsForPdf = $allUnsetParams->filter(function ($param) use ($excludedSubParameterIds) {
                        return !in_array($param->sub_parameter_id, $excludedSubParameterIds);
                    });
                    $agency_id = DB::table('audits')->where('id', $auditId)->pluck('agency_id')->first();
                    if ($unsetParamsForPdf->isNotEmpty()) {

                        $randomString = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 25);


                        $agency_details = DB::table('agencies')->where('id', $agency_id)->first();
                        $audit_details = DB::table('audits')->where('id', $auditId)->first();
                        $process_review_month = DB::table('audit_cycles')->where('id', $audit_details->audit_cycle_id)->pluck('name')->first();

                        // Check if audit already exists in closure_audits
                        $auditExists = DB::table('closure_audits')
                            ->where('audit_id', $auditId)
                            ->first(); // Use first() instead of exists()

                        if (!$auditExists) {  // If no record is found, $auditExists will be null
                            $closureId = DB::table('closure_audits')->insertGetId([
                                'audit_id' => $auditId,
                                'agency_id' => $agency_id, // Save agency_id
                                'link' => $randomString,
                                'remarks' => '',
                                'created_at' => now(),
                                'audit_agency_id' => $audit_agency_id,
                                'client_id' => $getUser->client_id,
                            ]);
                        } else {
                            $closureId = $auditExists->id;
                        }
                        // Insert the audit into closure_audits and get the closure_id

                        // Assuming you have the agency email stored somewhere
                        $agencyEmail = $request->submission_data['agency_email']; // Replace this with the actual agency email
                        $clientName = User::where('id', $getUser->client_id)->value('name');

                        // Prepare data for the PDF
                        $background_color = User::where('id', $getUser->client_id)->value('color_code');
                        $pdfData = [
                            'unsetParams' => $unsetParamsForPdf,
                            'audit_id' => $auditId,
                            'agency_details' => $agency_details, // Replace with actual agency name if available
                            'audit_details' => $audit_details, // Replace with actual agency name if available
                            'process_review_month' => $process_review_month,
                            'audit_agency_id' => $audit_agency_id,  // Save audit_agency_id
                            'client_name' => $clientName,
                            'background_color' => $background_color,
                        ];

                        // Generate PDF for unset parameters
                        $pdf = PDF::loadView('closure.audit_closure_pdf', $pdfData);
                        $pdfContent = $pdf->output();

                        // Create a month-wise folder for storing the PDF
                        $folderPath = 'audit_reports/' . date('Y') . '/' . date('F');
                        $fileName = 'audit_closure_' . time() . '.pdf';
                        $fullPath = $folderPath . '/' . $fileName;

                        // Store the PDF file in the public storage
                        Storage::disk('public')->put($fullPath, $pdfContent);

                        // Save the file path and audit details to the database
                        $existingReport = DB::table('audit_reports')->where('audit_id', $new_ar->id)->first();

                        // Save the file path and audit details to the database
                        if ($existingReport) {
                            // If the entry exists, update the file path for audit_result_pdf
                            DB::table('audit_reports')
                                ->where('audit_id', $new_ar->id)
                                ->update([
                                    'closure_pdf' => $fullPath,
                                    'updated_at' => now(),
                                ]);
                        } else {
                            // If the entry does not exist, insert a new entry
                            DB::table('audit_reports')->insert([
                                'audit_id' => $new_ar->id,
                                'audit_date' => $request->submission_data['audit_date_by_aud'],
                                'agency_id' => $request->submission_data['agency_id'],
                                'process_review_period' => $audit_cycles->name ?? null,
                                'checksheet_pdf' => null,
                                'audit_result_pdf' => null,
                                'closure_pdf' => $fullPath,
                                'user_id' => $getUser->id,
                                'created_at' => now(),
                                'updated_at' => now(),
                                'client_id' => $getUser->client_id,
                            ]);
                        }

                        // Prepare the closure form link with closure_id
                        $closureFormLink = url("audit-closure-justification/{$closureId}/{$auditId}/{$randomString}");

                        // Prepare email subject
                        $subject = "Audit Closure Form for Audit Agency: {$agency_details->name}";
                        $agency_name = $agency_details->name;
                        // Send email with the PDF attached using a view for the email body
                        Mail::send('closure.audit_closure_mail', ['closureFormLink' => $closureFormLink, 'process_review_month' => $process_review_month, 'agency_name' => $agency_name], function ($message) use ($agencyEmail, $subject, $pdfContent) {
                            $message->from('auditmitr@qdegrees.com', 'Audit Team')
                                ->to($agencyEmail)
                                ->subject($subject)
                                ->attachData($pdfContent, 'UNSATISFACTORY_PARAMETERS.pdf', [
                                    'mime' => 'application/pdf',
                                ]);
                        });
                    } else {
                        $closureId = DB::table('closure_audits')
                            ->insertGetId([
                                'audit_id' => $auditId,
                                'agency_id' => $agency_id,  // Save agency_id
                                'audit_agency_id' => $audit_agency_id,  // Save audit_agency_id
                                'remarks' => '',
                                'status' => '1',
                                'created_at' => now(),
                                'client_id' => $getUser->client_id,
                            ]);
                    }



                    //saving checksheet of audit 


                    $agency_details = DB::table('agencies')->where('id', $agency_id)->first();
                    $product_details = DB::table('products')->where('id', $request->submission_data['product_id'])->first();
                    $audit_cycle = DB::table('audit_cycles')->where('id', $request->submission_data['audit_cycle_id'])->first();


                    $parameters = $this->fetchParametersWithStatus($request->parameters);

                    // echo '<pre>'; print_r($parameters); die;
                    // Prepare data for PDF\
                    $clientName = User::where('id', $getUser->client_id)->value('name');
                    $audit_agency_email = $getUser->created_by;
                    $audit_agency_name = User::where('email', $audit_agency_email)->value('name');
                    $pdfData = [
                        'agency_name' => 'Sample Agency',
                        'audit_date' => Carbon::now()->toDateString(),
                        'parameters' => $parameters,
                        'agency_details' => $agency_details,
                        'product_details' => $product_details,
                        'audit_cycle' => $audit_cycle->name,
                        'score' => $request->submission_data['overall_score'],
                        'audit_agency_name' => $audit_agency_name,
                        'client_name' => $clientName,
                    ];

                    // Generate PDF
                    $pdf = Pdf::loadView('audit.checksheet_pdf', $pdfData);
                    $pdfContent = $pdf->output();

                    // Create a month-wise folder for storing the PDF
                    $folderPath = 'audit_reports/' . date('Y') . '/' . date('F');
                    $fileName = 'audit_checksheet_' . time() . '.pdf';
                    $fullPath = $folderPath . '/' . $fileName;

                    // Store the PDF file in the public storage
                    Storage::disk('public')->put($fullPath, $pdfContent);

                    // Save the file path and audit details to the database
                    $existingReport = DB::table('audit_reports')->where('audit_id', $new_ar->id)->first();

                    // Save the file path and audit details to the database
                    if ($existingReport) {
                        // If the entry exists, update the file path for audit_result_pdf
                        DB::table('audit_reports')
                            ->where('audit_id', $new_ar->id)
                            ->update([
                                'checksheet_pdf' => $fullPath,
                                'updated_at' => now(),
                            ]);
                    } else {
                        // If the entry does not exist, insert a new entry
                        DB::table('audit_reports')->insert([
                            'audit_id' => $new_ar->id,
                            'audit_date' => $request->submission_data['audit_date_by_aud'],
                            'agency_id' => $request->submission_data['agency_id'],
                            'checksheet_pdf' => $fullPath,
                            'audit_result_pdf' => null,
                            'closure_pdf' => null,
                            'user_id' => $getUser->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                            'client_id' => $getUser->client_id,
                        ]);
                    }
                }
            } else {
                // echo "string";
                // die();
                logger($request);
                $user_role = $getUser->roles()->first()->name;
                $latlong = explode(" ", $request->submission_data['geotag']);
                // dd($request->all());
                // calculate score following code done by shailendra kumar
                $total = 0;
                $parameter_total = 0;
                $audit_crital = 0;
                $pera_score = [];

                // print_r($request);
                // die();
                foreach ($request->parameters as $key => $para) {
                    $pera_total = 0;
                    $pera_fatal = 0;
                    $pera_without_fatal = 0;
                    $pera_is_critical = 0;
                    $total_temp_weight = 0;
                    $pera_fatal_per = 0;
                    $pera_without_fatal_par = 0;
                    $is_critical_para = 0;
                    $subtotal = 0;
                    foreach ($para['subs'] as $sub_para) {
                        $paramterValue = 0;
                        if ($sub_para['temp_weight'] != "N/A") {
                            $total_temp_weight = $total_temp_weight + $sub_para['temp_weight'];
                        } else {
                            $total_temp_weight = $total_temp_weight + 0;
                        }
                        if ($sub_para['option'] != 'N/A') {
                            $paramterValue = $sub_para['score'];
                            if ($sub_para['score'] == 'N/A') {
                                $pera_fatal = $pera_fatal + 0;
                            } else {
                                $pera_fatal = $pera_fatal + $sub_para['score'];
                            }
                        }
                        if ($sub_para['option'] != 'Critical') {
                            if ($sub_para['score'] == 'N/A') {
                                $subtotal = $subtotal + 0;
                            } else {
                                $subtotal = $subtotal + $sub_para['score'];
                                $parameter_total = $parameter_total + $paramterValue;
                            }
                        } else {
                            $subtotal = 0;
                            $is_critical_para = 1;
                            $audit_crital = 1;
                            $parameter_total = $parameter_total + $paramterValue;
                            break;
                        }
                    }
                    $total = $total + $subtotal;
                    if ($total_temp_weight != 0) {
                        $fat_score = round(($pera_fatal / $total_temp_weight) * 100);
                        $wfat_score = round(($subtotal / $total_temp_weight) * 100);
                    } else {
                        $fat_score = 0;
                        $wfat_score = 0;
                    }
                    $para_id = $para['id'];
                    $pera_score[$para_id]['pera_total'] = $subtotal;
                    $pera_score[$para_id]['total_temp_weight'] = $total_temp_weight;
                    $pera_score[$para_id]['pera_fatal'] = $pera_fatal;
                    $pera_score[$para_id]['pera_without_fatal'] = $subtotal;
                    $pera_score[$para_id]['pera_fatal_per'] = $fat_score;
                    $pera_score[$para_id]['pera_without_fatal_par'] = $wfat_score;
                    $pera_score[$para_id]['pera_is_critical'] = $is_critical_para;
                }
                $overall_score = $total;
                $new_ar = new Audit;
                $new_ar->latitude = $latlong[0];
                $new_ar->longitude = $latlong[2];
                $new_ar->process_review_agency_email = $process_review_agency_email;
                $new_ar->audit_agency_id = $audit_agency_id;
                $new_ar->qm_sheet_id = $request->submission_data['qm_sheet_id'];
                if (!empty($request->submission_data['present_auditor'])) {
                    $new_ar->present_auditor = $request->submission_data['present_auditor'];
                } else {
                    $new_ar->present_auditor = '';
                }
                $new_ar->audit_date_by_aud = $request->submission_data['audit_date_by_aud'];
                $new_ar->audit_cycle_id = $request->submission_data['audit_cycle_id'];

                $new_ar->audited_by_id = $getUser->id;
                $new_ar->is_critical = $audit_crital;
                $new_ar->overall_score = $overall_score;
                $new_ar->sub_product_ids = $request->submission_data['sub_product_ids'];
                $new_ar->agency_mobile = $request->submission_data['agency_phone'];
                $new_ar->agency_email = $request->submission_data['agency_email'];
                $new_ar->branch_id = (isset($request->submission_data['branch_id'])) ? $request->submission_data['branch_id'] : null;
                $new_ar->agency_id = (isset($request->submission_data['agency_id'])) ? $request->submission_data['agency_id'] : null;
                $new_ar->yard_id = (isset($request->submission_data['yard_id'])) ? $request->submission_data['yard_id'] : null;
                $new_ar->branch_repo_id = (isset($request->submission_data['branch_repo_id'])) ? $request->submission_data['branch_repo_id'] : null;
                $new_ar->agency_repo_id = (isset($request->submission_data['agency_repo_id'])) ? $request->submission_data['agency_repo_id'] : null;
                $new_ar->product_id = (isset($request->submission_data['product_id'])) ? $request->submission_data['product_id'] : null;
                $new_ar->collection_manager_email = (isset($request->submission_data['collection_manager_email'])) ? $request->submission_data['collection_manager_email'] : null;
                $new_ar->agency_manager_email = (isset($request->submission_data['agency_manager_email'])) ? $request->submission_data['agency_manager_email'] : null;
                $new_ar->yard_manager_email = (isset($request->submission_data['yard_manager_email'])) ? $request->submission_data['yard_manager_email'] : null;
                $new_ar->collection_manager_id = (isset($request->submission_data['collection_manager_id'])) ? $request->submission_data['collection_manager_id'] : null;
                $new_ar->lavel_3 = (isset($request->submission_data['collection_manager_id'])) ? $request->submission_data['collection_manager_id'] : null;
                $new_ar->lavel_4 = isset($request->submission_data['lavel_4'])
                    ? implode(',', $request->submission_data['lavel_4'])
                    : null;
                $new_ar->lavel_5 = isset($request->submission_data['lavel_5'])
                    ? implode(',', $request->submission_data['lavel_5'])
                    : null;
                $new_ar->grade = (isset($request->submission_data['grade'])) ? $request->submission_data['grade'] : null;
                $new_ar->score_percentage = (isset($request->submission_data['with_fatal_score_per'])) ? $request->submission_data['with_fatal_score_per'] : null;
                $new_ar->client_id = $getUser->client_id;
                $new_ar->status = ($request->submission_data['status'] == 'save') ? 5 : 1;
                $new_ar->save();

                // added by sumeet to store artifact

                $artifact_data = [];
                $artifact_data = TempArtifact::all()
                    ->where('temp_audit_id', ($request->submission_data['temp_audit_id']))->all();
                foreach ($artifact_data as $key => $value) {
                    $movedata = new Artifact;
                    $movedata->sheet_id = $value->sheet_id;
                    $movedata->parameter_id = $value->parameter_id;
                    $movedata->sub_parameter_id = $value->sub_parameter_id;
                    $movedata->file = $value->file;
                    $movedata->audit_id = $new_ar->id;
                    $movedata->save();
                }

                // $data = TempArtifact::delete(where('temp_audit_id',($request->temp_audit_id)));
                foreach ($artifact_data as $value) {
                    $value->delete();
                }

                if ($request->submission_data['status'] == 'submit') {
                    $user = User::find($new_ar->lavel_3); // Assuming lavel_3 is the recipient
                    if ($user && $user->email) {
                        $agency_email = $request->submission_data['agency_email'];
                        $toEmails = []; // Initialize the 'To' email array

                        // Add the agency email as the primary recipient
                        if ($agency_email) {
                            $toEmails[] = $agency_email;
                        }

                        // Add the email from lavel_3 to 'To' emails if it exists

                        if ($user && $user->email) {
                            $toEmails[] = $user->email; // Add lavel_3 email
                        }


                        // Fetch emails for lavel_4 and lavel_5
                        $lavel4Emails = [];
                        $lavel5Emails = [];

                        // Fetch users based on lavel_4 if it exists
                        if (!empty($new_ar->lavel_4)) {
                            $lavel4Ids = explode(',', $new_ar->lavel_4);
                            $lavel4Users = User::whereIn('id', $lavel4Ids)->get(['email']);
                            $lavel4Emails = $lavel4Users->pluck('email')->toArray();
                        }

                        // Fetch users based on lavel_5 if it exists
                        if (!empty($new_ar->lavel_5)) {
                            $lavel5Ids = explode(',', $new_ar->lavel_5);
                            $lavel5Users = User::whereIn('id', $lavel5Ids)->get(['email']);
                            $lavel5Emails = $lavel5Users->pluck('email')->toArray();
                        }

                        // Merge lavel_4 and lavel_5 emails

                        $rbl_bank_email = 'CollectionProcessReview@rblbank.com';
                        $ccEmails = array_merge($lavel4Emails, $lavel5Emails, [$process_review_agency_email]);
                        // Prepare data for the PDF
                        $agency = DB::table('agencies')->where('id', $request->submission_data['agency_id'])->first();
                        $product = DB::table('products')->where('id', $request->submission_data['product_id'])->first();
                        $audit_cycles = DB::table('audit_cycles')->where('id', $new_ar->audit_cycle_id)->first();
                        $clientName = User::where('id', $getUser->client_id)->value('name');
                        $clientEmail = User::where('id', $getUser->client_id)->value('email');
                        $audit_agency_email = $getUser->created_by;
                        $audit_agency_name = User::where('email', $audit_agency_email)->value('name');

                        $pdfData = [
                            'Audit_agency' => 'QDegrees',
                            'grade' => $request->submission_data['grade'] ?? null,
                            'score_percentage' => $request->submission_data['with_fatal_score_per'] ?? null,
                            'overall_score' => $request->submission_data['overall_score'] ?? null,
                            'agency_name' => $agency->name ?? null,
                            'location' => $agency->location ?? null,
                            'product_name' => $product->name ?? null,
                            'auditor_id' => $getUser->name,
                            'audit_cycle' => $audit_cycles->name ?? null,
                            'audit_date' => date('F Y', strtotime($request->submission_data['audit_date_by_aud'])),
                            'client_name' => $clientName,
                            'client_email' => $clientEmail,
                            'audit_agency_name' => $audit_agency_name,
                            'manager_email' => $getUser->client_id == 15 ? 'mohammed.danish@moneyview.in' : '',
                        ];

                        // Generate PDF
                        $pdf = Pdf::loadView('audit.audit_result_pdf', $pdfData);
                        $pdfContent = $pdf->output();

                        // Create a month-wise folder for storing the PDF
                        $folderPath = 'audit_reports/' . date('Y') . '/' . date('F');
                        $fileName = 'Audit_Result_' . time() . '.pdf';
                        $fullPath = $folderPath . '/' . $fileName;

                        // Store the PDF file in the public storage
                        Storage::disk('public')->put($fullPath, $pdfContent);

                        $existingReport = DB::table('audit_reports')->where('audit_id', $new_ar->id)->first();

                        // Save the file path and audit details to the database
                        if ($existingReport) {
                            // If the entry exists, update the file path for audit_result_pdf
                            DB::table('audit_reports')
                                ->where('audit_id', $new_ar->id)
                                ->update([
                                    'audit_result_pdf' => $fullPath,
                                    'updated_at' => now(),
                                ]);
                        } else {
                            // If the entry does not exist, insert a new entry
                            DB::table('audit_reports')->insert([
                                'audit_id' => $new_ar->id,
                                'audit_date' => $request->submission_data['audit_date_by_aud'],
                                'agency_id' => $request->submission_data['agency_id'],
                                'checksheet_pdf' => null,
                                'audit_result_pdf' => $fullPath,
                                'closure_pdf' => null,
                                'process_review_period' => $audit_cycles->name ?? null,
                                'user_id' => $getUser->id,
                                'created_at' => now(),
                                'updated_at' => now(),
                                'client_id' => $getUser->client_id,
                            ]);
                        }

                        $subject = "Process Review Rating Period for " . $pdfData['audit_cycle'] . " (" . $pdfData['agency_name'] . " - " . $pdfData['product_name'] . ")";

                        // Send the email with CC
                        $clientName = User::where('id', $getUser->client_id)->value('name');
                        Mail::send([], [], function ($message) use ($toEmails, $ccEmails, $subject, $pdfContent, $clientName) {
                            $message->from('auditmitr@qdegrees.com', 'Audit Team')
                                ->to($toEmails)
                                ->cc($ccEmails) // Add CC recipients
                                ->subject($subject)
                                ->attachData($pdfContent, 'Audit_Result.pdf', [
                                    'mime' => 'application/pdf',
                                ])
                                ->html(
                                    "<p>Dear Associate,</p>
                                         <p>Greetings!</p>
                                         <p>Thank you for your co-operation & assistance extended to the Process Reviewer in conducting the process review successfully.</p>
                                         <p>Please find attached the audit result for your agency.</p>
                                         <p>Warm Regards,<br>
                        Audit Team<br>
                        <a href='https://www.qdegrees.com/' target='_blank'>QDegrees Services</a></p>"
                                    //       'text/html'
                                );
                        });
                    }
                }
                if ($request->submission_data['status'] == 'save') {
                    SavedAudit::create(['audit_id' => $new_ar->id, 'status' => 1]);
                }
                if (isset($request->submission_data['agency_id'])) {
                    Agency::where('id', $new_ar->agency_id)->update([
                        'agency_manager' => $request->submission_data['agency_manager'],
                        'agency_phone'   => $request->submission_data['agency_phone'],
                        'email'          => $request->submission_data['agency_email'],
                    ]);
                }

                if (isset($request->submission_data['agency_id'])) {
                    // Get the agency_id from the request
                    $agencyId = $request->submission_data['agency_id'];

                    // Fetch only the necessary fields from the agencies table
                    $agency = Agency::where('id', $agencyId)
                        ->select('address', 'location', 'city_id', 'product_id', 'sub_product_id')
                        ->first();

                    if ($agency) {
                        $city = City::where('id', $agency->city_id)->first();

                        // Fetch product name from the products table
                        $product = Products::where('id', $agency->product_id)->first();

                        // Fetch sub-product names (product attributes) from productattributes table
                        $subProductIds = explode(',', $agency->sub_product_id); // Assuming it's a comma-separated string
                        $subProducts = ProductAttribute::whereIn('id', $subProductIds)->pluck('product_attribute_name')->toArray();

                        // Format sub-product names as a comma-separated string
                        $subProductNames = implode(', ', $subProducts);

                        // Update the audits table with fetched details
                        Audit::where('id', $new_ar->id)->update([
                            'agency_address' => $agency->address,
                            'agency_location' => $agency->location,
                            'agency_city' => $city ? $city->name : null, // City name
                            'agency_product' => $product ? $product->name : null, // Product name
                            'agency_sub_products' => $subProductNames, // Sub-product names
                        ]);
                    }
                }
                if ($new_ar->id) {
                    // store parameter wise data
                    foreach ($request->parameters as $key => $value) {
                        $new_arb = new AuditParameterResult;
                        $new_arb->audit_id =  $new_ar->id;
                        $new_arb->parameter_id = $value['id'];
                        $new_arb->qm_sheet_id = $request->submission_data['qm_sheet_id'];
                        $new_arb->orignal_weight = ($value['parameter_weight'] != null) ? $value['parameter_weight'] : 0;
                        $new_arb->temp_weight = $pera_score[$value['id']]['total_temp_weight'];
                        $new_arb->with_fatal_score = $pera_score[$value['id']]['pera_fatal'];
                        $new_arb->without_fatal_score = $pera_score[$value['id']]['pera_without_fatal'];
                        $new_arb->is_critical = $pera_score[$value['id']]['pera_is_critical'];
                        // $new_arb->without_fatal_score = $value['score_without_fatal'];

                        if ($pera_score[$value['id']]['total_temp_weight'] != 0) {
                            $new_arb->with_fatal_score_per = ($pera_score[$value['id']]['pera_fatal'] / $pera_score[$value['id']]['total_temp_weight']) * 100;
                            // $new_arb->without_fatal_score_pre = ($value['score_without_fatal'] / $value['temp_total_weightage'])*100;
                            $new_arb->without_fatal_score_pre = ($pera_score[$value['id']]['pera_without_fatal'] / $pera_score[$value['id']]['total_temp_weight']) * 100;
                        }

                        $new_arb->save();

                        // if($request->submission_data['status'] == 'submit'){
                        //     $qc_arb->save();
                        //     }

                        if (isset($value['subs']) && count($value['subs']) > 0)
                        // store sub parameter wise data
                        {
                            foreach ($value['subs'] as $key_sb => $value_sb) {
                                if ($value_sb['temp_weight']); {
                                    $new_arc = new AuditResult;
                                    $new_arc->audit_id =  $new_ar->id;
                                    $new_arc->parameter_id = $value['id'];
                                    $new_arc->sub_parameter_id = $value_sb['id'];
                                    $new_arc->selected_option = ($value_sb['temp_weight'] != 'Critical') ? $value_sb['temp_weight'] : 0;
                                    $new_arc->option_selected = (isset($value_sb['option'])) ? $value_sb['option'] : null;
                                    $new_arc->is_critical = ($value_sb['temp_weight'] != 'Critical') ? 0 : 1;
                                    if ($value_sb['score'] != 'rating') {
                                        $new_arc->score = ($value_sb['score'] != 'Critical') ? $value_sb['score'] : 0;
                                        $new_arc->is_percentage = $value_sb['is_percentage'];
                                        $new_arc->selected_per = ($value_sb['selected_per'] != 'select percentage') ? $value_sb['selected_per'] : null;
                                    } else {
                                        $new_arc->score = ($value_sb['score'] != 'Critical') ? $value_sb['temp_weight'] : 0;
                                        $new_arc->is_percentage = $value_sb['is_percentage'];
                                        $new_arc->selected_per = ($value_sb['selected_per'] != 'select percentage') ? $value_sb['selected_per'] : null;
                                    }
                                    $new_arc->remark = $value_sb['remark'];
                                    $new_arc->save();
                                }
                            }
                        }
                    }
                }

                // Commit Transaction
                DB::commit();

                if ($request->submission_data['status'] == 'submit') {
                    //closure mail work
                    $auditId = $new_ar->id;
                    $excludedSubParameterIds = [42, 62, 82, 133];

                    // Fetch all unset parameters (including the excluded ones for validation)
                    $allUnsetParams = DB::table('audit_results')
                        ->join('qm_sheet_sub_parameters', 'audit_results.sub_parameter_id', '=', 'qm_sheet_sub_parameters.id')
                        ->where('audit_results.audit_id', $auditId)
                        ->where('audit_results.option_selected', 'Unsatisfactory')
                        ->select('qm_sheet_sub_parameters.sub_parameter as parameter_name', 'audit_results.*', 'audit_results.remark as remarks')
                        ->get();

                    // Filter out the excluded parameters for the PDF and further processing
                    $unsetParamsForPdf = $allUnsetParams->filter(function ($param) use ($excludedSubParameterIds) {
                        return !in_array($param->sub_parameter_id, $excludedSubParameterIds);
                    });

                    $agency_id = DB::table('audits')->where('id', $auditId)->pluck('agency_id')->first();

                    if ($unsetParamsForPdf->isNotEmpty()) {

                        $randomString = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 25);
                        $agency_details = DB::table('agencies')->where('id', $agency_id)->first();
                        $audit_details = DB::table('audits')->where('id', $auditId)->first();
                        $process_review_month = DB::table('audit_cycles')->where('id', $audit_details->audit_cycle_id)->pluck('name')->first();

                        // Check if audit already exists in closure_audits
                        $auditExists = DB::table('closure_audits')
                            ->where('audit_id', $auditId)
                            ->first(); // Use first() instead of exists()

                        if (!$auditExists) {  // If no record is found, $auditExists will be null
                            $closureId = DB::table('closure_audits')->insertGetId([
                                'audit_id' => $auditId,
                                'agency_id' => $agency_id, // Save agency_id
                                'link' => $randomString, // Save agency_id
                                'remarks' => '',
                                'created_at' => now(),
                                'audit_agency_id' => $audit_agency_id,
                            ]);
                        } else {
                            $closureId = $auditExists->id;
                        }
                        // Insert the audit into closure_audits and get the closure_id

                        // Assuming you have the agency email stored somewhere
                        $agencyEmail = $request->submission_data['agency_email']; // Replace this with the actual agency email

                        // Prepare data for the PDF
                        $clientName = User::where('id', $getUser->client_id)->value('name');
                        $background_color = User::where('id', $getUser->client_id)->value('color_code');
                        $pdfData = [
                            'unsetParams' => $unsetParamsForPdf,
                            'audit_id' => $auditId,
                            'agency_details' => $agency_details, // Replace with actual agency name if available
                            'audit_details' => $audit_details, // Replace with actual agency name if available
                            'process_review_month' => $process_review_month,
                            'audit_agency_id' => $audit_agency_id,  // Save audit_agency_id
                            'client_name' => $clientName,
                            'background_color' => $background_color,
                        ];

                        // Generate PDF for unset parameters
                        $pdf = PDF::loadView('closure.audit_closure_pdf', $pdfData);
                        $pdfContent = $pdf->output();

                        // Create a month-wise folder for storing the PDF
                        $folderPath = 'audit_reports/' . date('Y') . '/' . date('F');
                        $fileName = 'audit_closure_' . time() . '.pdf';
                        $fullPath = $folderPath . '/' . $fileName;

                        // Store the PDF file in the public storage
                        Storage::disk('public')->put($fullPath, $pdfContent);

                        // Save the file path and audit details to the database
                        $existingReport = DB::table('audit_reports')->where('audit_id', $new_ar->id)->first();

                        // Save the file path and audit details to the database
                        if ($existingReport) {
                            // If the entry exists, update the file path for audit_result_pdf
                            DB::table('audit_reports')
                                ->where('audit_id', $new_ar->id)
                                ->update([
                                    'closure_pdf' => $fullPath,
                                    'updated_at' => now(),
                                ]);
                        } else {
                            // If the entry does not exist, insert a new entry
                            DB::table('audit_reports')->insert([
                                'audit_id' => $new_ar->id,
                                'audit_date' => $request->submission_data['audit_date_by_aud'],
                                'agency_id' => $request->submission_data['agency_id'],
                                'process_review_period' => $audit_cycles->name ?? null,
                                'checksheet_pdf' => null,
                                'audit_result_pdf' => null,
                                'closure_pdf' => $fullPath,
                                'user_id' => $getUser->id,
                                'created_at' => now(),
                                'updated_at' => now(),
                                'client_id' => $getUser->client_id,
                            ]);
                        }
                        // Prepare the closure form link with closure_id
                        $closureFormLink = url("audit-closure-justification/{$closureId}/{$auditId}/{$randomString}");

                        // Prepare email subject
                        $subject = "Audit Closure Form for Audit Agency: {$agency_details->name}";
                        $agency_name = $agency_details->name;
                        // Send email with the PDF attached using a view for the email body
                        Mail::send('closure.audit_closure_mail', ['closureFormLink' => $closureFormLink, 'process_review_month' => $process_review_month, 'agency_name' => $agency_name], function ($message) use ($agencyEmail, $subject, $pdfContent) {
                            $message->from('auditmitr@qdegrees.com', 'Audit Team')
                                ->to($agencyEmail)
                                ->subject($subject)
                                ->attachData($pdfContent, 'UNSATISFACTORY_PARAMETERS.pdf', [
                                    'mime' => 'application/pdf',
                                ]);
                        });
                    } else {
                        $closureId = DB::table('closure_audits')
                            ->insertGetId([
                                'audit_id' => $auditId,
                                'agency_id' => $agency_id,  // Save agency_id
                                'audit_agency_id' => $audit_agency_id,  // Save audit_agency_id
                                'remarks' => '',
                                'status' => '1',
                                'created_at' => now(),
                                'client_id' => $getUser->client_id,
                            ]);
                    }

                    //saving checksheet of audit 
                    $agency_details = DB::table('agencies')->where('id', $agency_id)->first();
                    $product_details = DB::table('products')->where('id', $request->submission_data['product_id'])->first();
                    $audit_cycle = DB::table('audit_cycles')->where('id', $request->submission_data['audit_cycle_id'])->first();
                    $parameters = $this->fetchParametersWithStatus($request->parameters);
                    // echo '<pre>'; print_r($parameters); die;
                    // Prepare data for PDF
                    $audit_agency_email = $getUser->created_by;
                    $audit_agency_name = User::where('email', $audit_agency_email)->value('name');
                    $clientName = User::where('id', $getUser->client_id)->value('name');

                    $pdfData = [
                        'agency_name' => 'Sample Agency',
                        'audit_date' => Carbon::now()->toDateString(),
                        'parameters' => $parameters,
                        'agency_details' => $agency_details,
                        'product_details' => $product_details,
                        'audit_cycle' => $audit_cycle->name,
                        'score' => $request->submission_data['overall_score'],
                        'audit_agency_name' => $audit_agency_name,
                        'client_name' => $clientName,
                    ];

                    // Generate PDF
                    $pdf = Pdf::loadView('audit.checksheet_pdf', $pdfData);
                    $pdfContent = $pdf->output();
                    // Create a month-wise folder for storing the PDF
                    $folderPath = 'audit_reports/' . date('Y') . '/' . date('F');
                    $fileName = 'audit_checksheet_' . time() . '.pdf';
                    $fullPath = $folderPath . '/' . $fileName;
                    // Store the PDF file in the public storage
                    Storage::disk('public')->put($fullPath, $pdfContent);
                    // Save the file path and audit details to the database
                    $existingReport = DB::table('audit_reports')->where('audit_id', $new_ar->id)->first();
                    // Save the file path and audit details to the database
                    if ($existingReport) {
                        // If the entry exists, update the file path for audit_result_pdf
                        DB::table('audit_reports')
                            ->where('audit_id', $new_ar->id)
                            ->update([
                                'checksheet_pdf' => $fullPath,
                                'updated_at' => now(),
                            ]);
                    } else {
                        // If the entry does not exist, insert a new entry
                        DB::table('audit_reports')->insert([
                            'audit_id' => $new_ar->id,
                            'audit_date' => $request->submission_data['audit_date_by_aud'],
                            'agency_id' => $request->submission_data['agency_id'],
                            'checksheet_pdf' => $fullPath,
                            'audit_result_pdf' => null,
                            'closure_pdf' => null,
                            'user_id' => $getUser->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                            'client_id' => $getUser->client_id,
                        ]);
                    }
                }
            }
        } catch (Exception $e) {
            // Rollback Transaction
            DB::rollback();
            // return redirect('user')->with('success', ['Retry Again!']);
            $response = array(
                'status' => 0,
                'message' => 'Retry Again.',
                'audit_id' => array()
            );
            return response(json_encode($response), 200);
        }
        $p = 'submitted';
        if ($request->submission_data['status'] == 'save') {
            $p = 'saved';
        }
        $response = array(
            'status' => 1,
            'message' => 'Audit ' . $p . ' successfully.',
            'audit_id' => $new_ar->id
        );
        return response(json_encode($response), 200);
    }

    public function saveBasicInfoaudit(Request $request)
    {
        if (!$request->header('Authorizations') || $request->header('Authorizations') == "") {
            $data = array('status' => 0, 'message' => 'Authorizations key is required in api headers.', 'data' => array());
            return response(json_encode($data), 200);
        }
        $getUser = User::where('auth_key', $request->header('Authorizations'))->first();
        if (!$getUser) {
            $data = array('status' => 0, 'message' => 'User not found', 'data' => array());
            return response(json_encode($data), 200);
        }

        $audit_agency_id = User::where('email', $getUser->created_by)->pluck('id')->first();
        $process_review_agency_email  = $getUser->created_by;

        if ($request->audit_id) {
            $new_ar = Audit::find($request->audit_id);
        } else {
            $new_ar = new Audit;
        }

        $getSheet = QmSheet::find($request->qm_sheet_id);

        $tablefriendlyData = $getSheet->type;
        if ($getSheet->type == "branch_repo") {
            $tablefriendlyData = "branchrepo";
        }
        if ($getSheet->type == "agency_repo") {
            $tablefriendlyData = "agencyrepo";
        }
        if ($getSheet->type == "yard_repo") {
            $tablefriendlyData = "yardrepo";
        }

        $getAllocationId = AuditAllocation::where(['agency_id' => $request->agency_id, 'type_of_agency' => $tablefriendlyData, 'audit_cycle_id' => $request->audit_cycle_id, 'client_id' => $getUser->client_id])->first();

        $new_ar->audit_cycle_id = $request->audit_cycle_id;
        $new_ar->audit_allocation_id = $getAllocationId->id ?? null;
        $new_ar->qm_sheet_id = $request->qm_sheet_id;

        $latlong = explode(',', $request->lat_long);
        $new_ar->latitude = $latlong[0];
        $new_ar->longitude = $latlong[1];
        $new_ar->audited_by_id = $getUser->id;
        $new_ar->is_critical = 0;
        $new_ar->last_updated_by = $getUser->id;
        $new_ar->audit_date_by_aud = date('Y-m-d');
        $new_ar->process_review_agency_email = $process_review_agency_email;
        $new_ar->audit_agency_id = $audit_agency_id;
        $new_ar->agency_manager_name = $request->agency_manager_name;

        $new_ar->agency_id = ($getSheet->type == "agency") ? $request->agency_id : null;
        $new_ar->branch_id = ($getSheet->type == "branch") ? $request->agency_id : null;
        $new_ar->yard_id = ($getSheet->type == "yard") ? $request->agency_id : null;
        $new_ar->branch_repo_id = ($getSheet->type == "branch_repo") ? $request->agency_id : null;
        $new_ar->agency_repo_id = ($getSheet->type == "agency_repo") ? $request->agency_id : null;
        $new_ar->yard_repo_id = ($getSheet->type == "yard_repo") ? $request->agency_id : null;

        $new_ar->agency_mobile = $request->agency_phone;
        $new_ar->agency_email = $request->agency_email;
        $new_ar->present_auditor = $request->present_auditor ?? '';
        $new_ar->product_id = $request->product_id ?? null;
        $new_ar->sub_product_ids = $request->sub_product_id ?? null;
        if ($getSheet->type == "agency") {
            $touchpoint = Agency::where('id', $request->agency_id)
                ->select('address', 'location', 'city_id', 'product_id', 'sub_product_id')
                ->first();
        }
        if ($getSheet->type == "branch") {
            $touchpoint = Branch::where('id', $request->agency_id)
                ->select('address', 'location', 'city_id', 'product_id', 'sub_product_id')
                ->first();
        }
        if ($getSheet->type == "yard") {
            $touchpoint = Yard::where('id', $request->agency_id)
                ->select('address', 'location', 'city_id', 'product_id', 'sub_product_id')
                ->first();
        }
        if ($getSheet->type == "agency_repo") {
            $touchpoint = AgencyRepo::where('id', $request->agency_id)
                ->select('address', 'location', 'city_id', 'product_id', 'sub_product_id')
                ->first();
        }
        if ($getSheet->type == "branch_repo") {
            $touchpoint = BranchRepo::where('id', $request->agency_id)
                ->select('address', 'location', 'city_id', 'product_id', 'sub_product_id')
                ->first();
        }
        if ($getSheet->type == "yard_repo") {
            $touchpoint = YardRepo::where('id', $request->agency_id)
                ->select('address', 'location', 'city_id', 'product_id', 'sub_product_id')
                ->first();
        }

        $city = City::where('id', $touchpoint->city_id)->first();
        $product = Products::where('id', $touchpoint->product_id)->first();

        $subProductIds = explode(',', $touchpoint->sub_product_id); // Assuming it's a comma-separated string
        $subProducts = ProductAttribute::whereIn('id', $subProductIds)->pluck('product_attribute_name')->toArray();
        // Format sub-product names as a comma-separated string
        $subProductNames = implode(', ', $subProducts);

        $new_ar->agency_address = $touchpoint->address;
        $new_ar->agency_location = $touchpoint->location;
        $new_ar->agency_city = $city ? $city->name : null;
        $new_ar->agency_product = $product ? $product->name : null;
        $new_ar->agency_sub_products = $subProductNames;


        $new_ar->collection_manager_email = $request->collection_manager_email ?? null;
        $new_ar->agency_manager_email = $request->agency_manager_email ?? null;
        $new_ar->yard_manager_email = $request->yard_manager_email ?? null;
        $new_ar->collection_manager_id = $request->collection_manager_id ?? null;
        $new_ar->lavel_3 = (isset($request->collection_manager_id)) ? $request->collection_manager_id : null;
        $new_ar->lavel_4 = isset($request->lavel_4)
            ? implode(',', $request->lavel_4)
            : null;
        $new_ar->lavel_5 = isset($request->lavel_5)
            ? implode(',', $request->lavel_5)
            : null;
        $new_ar->client_id = $getUser->client_id;
        if ($request->has('is_virtual_audit')) {
            $new_ar->virtual_audit = $request->is_virtual_audit;
        }
        $new_ar->status = 5; // saved status;
        $new_ar->save();

        $response = array(
            'status' => 1,
            'message' => 'Basic Info saved successfully.',
            'audit_id' => $new_ar->id
        );
        return response(json_encode($response), 200);
    }

    public function saveQuestionWiseData(Request $request)
    {
        if (!$request->header('Authorizations') || $request->header('Authorizations') == "") {
            return response()->json(['status' => 0, 'message' => 'Authorizations key is required in api headers.', 'data' => []], 200);
        }
        $user = User::where('auth_key', $request->header('Authorizations'))->first();
        if (!$user) {
            return response()->json(['status' => 0, 'message' => 'User not found', 'data' => []], 200);
        }

        $validator = Validator::make($request->all(), [
            'parameters' => 'required|array',
            'audit_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => 'Validation Errors', 'data' => $validator->errors()], 200);
        }

        $getAudit = Audit::find($request->audit_id);
        $agencyID = null;
        if (!is_null($getAudit->agency_id) && $getAudit->agency_id != 0) {
            $agencyID = $getAudit->agency_id;
        }
        if (!is_null($getAudit->branch_id) && $getAudit->branch_id != 0) {
            $agencyID = $getAudit->branch_id;
        }
        if (!is_null($getAudit->yard_id) && $getAudit->yard_id != 0) {
            $agencyID = $getAudit->yard_id;
        }
        if (!is_null($getAudit->agency_repo_id) && $getAudit->agency_repo_id != 0) {
            $agencyID = $getAudit->agency_repo_id;
        }
        if (!is_null($getAudit->branch_repo_id) && $getAudit->branch_repo_id != 0) {
            $agencyID = $getAudit->branch_repo_id;
        }
        if (!is_null($getAudit->yard_repo_id) && $getAudit->yard_repo_id != 0) {
            $agencyID = $getAudit->yard_repo_id;
        }


        $message = '';
        foreach ($request->parameters as $common) {
            $data = [
                'audit_id' => $request->audit_id,
                'qm_sheet_id' => $getAudit->qm_sheet_id,
                'parameter_id' => $common['parameter_id'],
                'sub_parameter_id' => $common['sub_parameter_id'],
                'selected_option' => $common['selected_option'] ?? null,
                'option_selected' => $common['option_selected'] ?? null,
                'score' => $common['score'] ?? null,
                'remark' => $common['remark'] ?? null,
                'is_critical' => $common['is_critical'] ?? 0,
                'is_percentage' => $common['is_percentage'] ?? 0,
                'selected_per' => $common['selected_per'] ?? null,
                'client_id' => $user->client_id,
                'is_alert' => $common['is_alert'] ?? null,
                'agency_id' => $agencyID,
                'client_id' =>  $getAudit->client_id,
            ];
            if (isset($common['error_count'])) {
                $data['error_count'] = $common['error_count'];
            }

            //unset_parameter=array(array('question_id'=>3,'answer'=>'Yes/No'));
            // Check if record exists
            $auditResult = AuditResult::where('audit_id', $request->audit_id)
                ->where('parameter_id', $common['parameter_id'])
                ->where('sub_parameter_id', $common['sub_parameter_id'])
                ->first();

            if ($auditResult) {
                $auditResult->update($data);
                $message = 'Record updated successfully.';
            } else {
                AuditResult::create($data);
                $message = 'Record added successfully.';
            }
        }

        $saveMainParameter = $this->saveParameterWiseData($request->audit_id, $getAudit->qm_sheet_id, $getAudit->client_id);

        return response()->json(['status' => 1, 'message' => $message, 'audit_id' => $request->audit_id], 200);
    }
    public function saveParameterWiseData($audit_id, $qm_sheet_id, $client_id)
    {
        // ✅ Step 1: Get distinct parameter IDs only (lightweight query)
        $distinctParameterIds = AuditResult::where('audit_id', $audit_id)
            ->distinct()
            ->pluck('parameter_id');

        if ($distinctParameterIds->isEmpty()) {
            return "No audit results found for audit_id: {$audit_id}";
        }

        // ✅ Step 2: Load weightage once into memory as a keyed map (small table, fine to load fully)
        $weightageMap = QmSheetSubParameter::where('qm_sheet_id', $qm_sheet_id)
            ->get()
            ->keyBy('id');

        // ✅ Step 3: Load existing AuditParameterResult records for this audit (for upsert check)
        $existingResults = AuditParameterResult::where('audit_id', $audit_id)
            ->pluck('id', 'parameter_id'); // [parameter_id => id]

        // ✅ Step 4: Process ONE parameter at a time — DB does the filtering, not PHP
        foreach ($distinctParameterIds as $parameterId) {

            // ✅ Fetch only rows for THIS parameter — no full dataset in memory
            $rowsForParam = AuditResult::where('audit_id', $audit_id)
                ->where('parameter_id', $parameterId)
                ->get();

            $param = [
                'audit_id'                 => $audit_id,
                'qm_sheet_id'              => $qm_sheet_id,
                'parameter_id'             => $parameterId,
                'orignal_weight'           => 0,
                'temp_weight'              => 0,
                'with_fatal_score'         => 0,
                'without_fatal_score'      => 0,
                'is_critical'              => 0,
                'client_id'                => $client_id,
                'with_fatal_score_per'     => 0,
                'without_fatal_score_pre'  => 0,
            ];

            foreach ($rowsForParam as $row) {

                // ✅ Skip if no weightage found for this sub-parameter
                if (!isset($weightageMap[$row->sub_parameter_id])) {
                    continue;
                }

                $weight = (float) $weightageMap[$row->sub_parameter_id]->weight;

                // Always accumulate original weight
                $param['orignal_weight'] += $weight;

                // Skip N/A from score calculation
                if ($row->option_selected === 'N/A') {
                    continue;
                }

                $param['temp_weight'] += $weight;

                // ✅ Mutually exclusive scoring — no double addition
                if ($row->is_percentage == 1 && $row->selected_per != 0) {
                    $score = ($weight / 100) * (float) $row->selected_per;
                    $param['without_fatal_score'] += $score;
                    $param['with_fatal_score']    += $score;
                } elseif ($row->option_selected === 'Satisfactory') {
                    $param['without_fatal_score'] += $weight;
                    $param['with_fatal_score']    += $weight;
                }

                if ($row->is_critical == 1) {
                    $param['is_critical'] = 1;
                }
            }

            // ✅ Round only at the end
            $param['with_fatal_score_per'] = $param['temp_weight'] != 0
                ? round(($param['with_fatal_score'] / $param['temp_weight']) * 100, 2)
                : 0;

            $param['without_fatal_score_pre'] = $param['temp_weight'] != 0
                ? round(($param['without_fatal_score'] / $param['temp_weight']) * 100, 2)
                : 0;

            $param['orignal_weight']      = round($param['orignal_weight'], 2);
            $param['temp_weight']         = round($param['temp_weight'], 2);
            $param['with_fatal_score']    = round($param['with_fatal_score'], 2);
            $param['without_fatal_score'] = round($param['without_fatal_score'], 2);

            // ✅ Efficient upsert using pre-loaded existing results
            if (isset($existingResults[$parameterId])) {
                AuditParameterResult::where('id', $existingResults[$parameterId])->update($param);
            } else {
                AuditParameterResult::create($param);
            }

            // ✅ Free memory for this parameter's rows immediately
            unset($rowsForParam);
        }

        return "Param updated/added successfully";
    }

    // public function saveParameterWiseData($audit_id, $qm_sheet_id, $client_id)
    // {

    //     $auditResult = AuditResult::where('audit_id', $audit_id);
    //     // Get distinct parameter_ids
    //     $getdistinct = $auditResult->distinct()->pluck('parameter_id');
    //     // Get all data for that audit_id
    //     $all_data = $auditResult->get();

    //     $getWeightage = QmSheetSubParameter::where('qm_sheet_id', $qm_sheet_id)->get()->toArray();

    //     foreach ($getdistinct as $mp) {
    //         $param = array();
    //         $param['audit_id'] = $audit_id;
    //         $param['qm_sheet_id'] = $qm_sheet_id;
    //         $param['parameter_id'] = $mp;
    //         $param['orignal_weight'] = 0;
    //         $param['temp_weight'] = 0;
    //         $param['with_fatal_score'] = 0;
    //         $param['without_fatal_score'] = 0;
    //         $param['is_critical'] = 0;
    //         $param['client_id'] = $client_id;

    //         foreach ($all_data as $d) {
    //             if ($d->parameter_id == $mp) {
    //                 $index = array_search($d->sub_parameter_id, array_column($getWeightage, 'id'));
    //                 if ($index !== false) {
    //                     $row = $getWeightage[$index];
    //                     $param['orignal_weight'] += round($row['weight']);
    //                     if ($d->option_selected != "N/A") {
    //                         $param['temp_weight'] += round($row['weight']);
    //                         $param['without_fatal_score'] += ($d->option_selected == "Satisfactory") ? round($row['weight']) : 0;
    //                         $param['with_fatal_score'] += ($d->option_selected == "Satisfactory") ? round($row['weight']) : 0;
    //                         if ($d->is_percentage == 1 && $d->selected_per != 0) {
    //                             $param['without_fatal_score'] += round(($row['weight'] / 100) * $d->selected_per);
    //                             $param['with_fatal_score'] += round(($row['weight'] / 100) * $d->selected_per);
    //                         }
    //                     }
    //                 }
    //                 if ($d->is_critical == 1) {
    //                     $param['is_critical'] = 1;
    //                 }
    //             }
    //         }

    //         $param['with_fatal_score_per'] = ($param['temp_weight'] != 0) ? round(($param['with_fatal_score'] / $param['temp_weight']) * 100) : 0;
    //         $param['without_fatal_score_pre'] = ($param['temp_weight'] != 0) ? round(($param['without_fatal_score'] / $param['temp_weight']) * 100) : 0;

    //         $auditParamResult = AuditParameterResult::where('audit_id', $audit_id)->where('parameter_id', $mp)->first();

    //         if ($auditParamResult) {
    //             $auditParamResult->update($param);
    //             $message = 'Record updated successfully.';
    //         } else {
    //             AuditParameterResult::create($param);
    //             $message = 'Record added successfully.';
    //         }
    //     }

    //     return "Param updated/added successfully";
    // }

    public function submitAudit(Request $request)
    {
        if (!$request->header('Authorizations') || $request->header('Authorizations') == "") {
            return response()->json(['status' => 0, 'message' => 'Authorizations key is required in api headers.', 'data' => []], 200);
        }
        $user = User::where('auth_key', $request->header('Authorizations'))->first();
        if (!$user) {
            return response()->json(['status' => 0, 'message' => 'User not found', 'data' => []], 200);
        }

        $validator = Validator::make($request->all(), [
            'audit_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => 'Validation Errors', 'data' => $validator->errors()], 200);
        }

        $new_ar = Audit::find($request->audit_id);
        $getScoring = $this->calculateScore($request->audit_id, $new_ar->client_id);
        $new_ar->overall_score = round($getScoring->scored);
        $new_ar->score_percentage = round($getScoring->scored_per) . "%";
        if (round($getScoring->scored_per) >= 90) {
            $grade = 'A';
        } elseif (round($getScoring->scored_per) <= 89 && round($getScoring->scored_per) >= 75) {
            $grade = 'B';
        } elseif (round($getScoring->scored_per) <= 74 && round($getScoring->scored_per) >= 60) {
            $grade = 'C';
        } else {
            $grade = 'D';
        }
        $new_ar->grade = $grade;
        $new_ar->status = 1;
        $new_ar->save();

        //Mails code for sharing pdf's
        $getPDFStatus = $this->generateReports($request->audit_id);
        try {
            $this->sendAuditResultEmail($request->audit_id, $user->client_id);
        } catch (\Exception $e) {
            // Log error or handle as needed
            dd($e);
        }

        try {
            $this->sendAuditClosureEmail($request->audit_id);
        } catch (\Exception $e) {
            // Log error or handle as needed
        }

        $message = 'Record saved successfully.';
        return response()->json(['status' => 1, 'message' => $message, 'audit_id' => $request->audit_id], 200);
    }
    private function sendAuditResultEmail($audit_id, $client_id)
    {
        $audit = DB::table('audits')->where('id', $audit_id)->first();
        if (!$audit) return false;
        // $level_3 =$audit->lavel_3;
        // $toEmails = $audit->agency_email;
        $agency_email = $audit->agency_email;
        $toEmails = [];
        if ($agency_email) {
            $toEmails[] = $agency_email;
        }
        $user = User::find($audit->lavel_3);
        if ($user && $user->email) {
            $toEmails[] = $user->email; // Add lavel_3 email
        }
        $audit_cycle = DB::table('audit_cycles')->where('id', $audit->audit_cycle_id)->value('name');
        $audit_type = DB::table('qm_sheets')->where('id', $audit->qm_sheet_id)->value('type');

        $typeTableMap = [
            'agency'       => ['table' => 'agencies',      'column' => 'agency_id'],
            'branch'       => ['table' => 'branch',        'column' => 'branch_id'],
            'yard'         => ['table' => 'yard',          'column' => 'yard_id'],
            'agency_repo'  => ['table' => 'agency_repo',   'column' => 'agency_repo_id'],
            'branch_repo'  => ['table' => 'branch_repo',   'column' => 'branch_repo_id'],
            'yard_repo'    => ['table' => 'yard_repo',     'column' => 'yard_repo_id'],
        ];

        $agency_name = '-';
        if (isset($typeTableMap[$audit_type])) {
            $table = $typeTableMap[$audit_type]['table'];
            $column = $typeTableMap[$audit_type]['column'];
            $id = $audit->{$column};
            $agency_name = DB::table($table)->where('id', $id)->value('name');
            $agency = DB::table($table)->where('id', $id)->first();
        }

        $product_name = DB::table('products')->where('id', $audit->product_id)->value('name');
        $subject = "Process Review Rating Period for {$audit_cycle} ({$agency_name} - {$product_name})";
        $clientName = User::where('id', $client_id)->value('name');

        $lavel4Emails = [];
        $lavel5Emails = [];

        if (!empty($audit->lavel_4)) {
            $lavel4Ids = explode(',', $audit->lavel_4);
            $lavel4Emails = User::whereIn('id', $lavel4Ids)->pluck('email')->toArray();
        }

        if (!empty($audit->lavel_5)) {
            $lavel5Ids = explode(',', $audit->lavel_5);
            $lavel5Emails = User::whereIn('id', $lavel5Ids)->pluck('email')->toArray();
        }

        $process_review_agency_email = null;
        if ($audit->audit_agency_id) {
            $process_review_agency_email = User::where('id', $audit->audit_agency_id)->value('email');
        }

        $ccEmails = array_filter(array_merge($lavel4Emails, $lavel5Emails));
        $zone = [];
        if ($client_id ==  15) {
            $mv = ['arjun.verma@qdegrees.com', 'vaibhav.amer@qdegrees.com',  'anup.tiwari@moneyview.in',  'oberoi.puneet@moneyview.in',  'mohammed.danish@moneyview.in', 'manvi.ojha@moneyview.in'];
        } elseif ($client_id ==  74) {
            // $mv = [];
            $mv = ['Snehal.Nimkar@fibe.in', 'piyali.banerjee@fibe.in', 'Pradip.kumar@fibe.in', 'abhishek.gupta@qdegrees.com', 'joicy.kunjuman@qdegrees.com', 'Vaibhav.Amer@qdegrees.com'];
            if ($agency->region_id == 2 || $agency->region_id == 4) {
                $zone = ['abir.sengupta@fibe.in', 'ashok.nowdu@fibe.in', 'bharath.v@fibe.in'];
            }
        } else {
            $mv = [];
        }
        $allCcEmails = array_merge((array) $ccEmails, $mv, $zone);

        $pdfContent = DB::table('audit_reports')->where('audit_id', $audit->id)->value('audit_result_pdf');
        if (!$pdfContent) return false;

        $pdfFullPath = storage_path('app/public/' . $pdfContent);
        if (!file_exists($pdfFullPath)) return false;

        $checksheet = DB::table('audit_reports')->where('audit_id', $audit->id)->value('checksheet_pdf');
        if (!$checksheet) return false;
        $checksheetPdf = storage_path('app/public/' . $checksheet);
        if (!file_exists($checksheetPdf)) return false;

        if ($audit->client_id == 74) {
            $scoreString = $audit->score_percentage;
            $score = floatval(str_replace('%', '', $scoreString));

            if ($score == 100) {
                $rating_grade = 5;
            } elseif ($score >= 91 && $score <= 99) {
                $rating_grade = 4;
            } elseif ($score >= 81 && $score <= 90) {
                $rating_grade = 3;
            } elseif ($score >= 71 && $score <= 80) {
                $rating_grade = 2;
            } elseif ($score >= 61 && $score <= 70) {
                $rating_grade = 1;
            } else {
                $rating_grade = 0;
            }
        } else {
            $rating_grade = $audit->grade ?? null; // use existing grade variable
        }

        Mail::send([], [], function ($message) use ($toEmails, $allCcEmails, $subject, $pdfFullPath, $clientName, $checksheetPdf, $audit_cycle, $rating_grade, $agency_name) {
            $message->from('auditmitr@qdegrees.com', 'Audit Team')
                ->to($toEmails)
                ->cc($allCcEmails)
                ->subject($subject)
                ->attach($pdfFullPath, ['as' => 'Audit_Result.pdf'])
                ->attach($checksheetPdf, ['as' => 'Checksheet.pdf'])
                ->html(
                    "
                <p>Dear {$agency_name},</p>
                <p>Greetings!</p>
                <p>Thank you for your co-operation & assistance extended to the Process Reviewer in conducting the process review successfully.</p>
                <p>We are glad to communicate the Process Review rating which was conducted in the month of {$audit_cycle} at your agency. You have been graded as ‘{$rating_grade}’ rated agency.</p>
                <p>Warm Regards,<br>
                        Audit Team<br>
                        <a href='https://www.qdegrees.com/' target='_blank'>QDegrees Services</a></p>"
                    //     'text/html'
                );
        });

        return true;
    }

    private function sendAuditClosureEmail($audit_id)
    {
        $audit = DB::table('audits')->where('id', $audit_id)->first();
        if (!$audit) return false;

        $process_review_month = DB::table('audit_cycles')->where('id', $audit->audit_cycle_id)->value('name');
        $agency_details = DB::table('agencies')->where('id', $audit->agency_id)->first();
        $agencyEmail = $audit->agency_email;
        $agency_email = $audit->agency_email;
        $toEmails = [];
        if ($agency_email) {
            $toEmails[] = $agency_email;
        }
        $user = User::find($audit->lavel_3);
        if ($user && $user->email) {
            $toEmails[] = $user->email; // Add lavel_3 email
        }

        $closureId = DB::table('closure_audits')->where('audit_id', $audit->id)->value('id');
        $randomString = DB::table('closure_audits')->where('audit_id', $audit->id)->value('link');
        $pdfContent = DB::table('audit_reports')->where('audit_id', $audit->id)->value('closure_pdf');

        if (!$pdfContent) return false;

        $pdfFullPath = storage_path('app/public/' . $pdfContent);
        if (!file_exists($pdfFullPath)) return false;

        $subject = "Audit Closure Form for Audit Agency: {$agency_details->name}";
        $closureFormLink = url("audit-closure-justification/{$closureId}/{$audit->id}/{$randomString}");

        $lavel4Emails = [];
        $lavel5Emails = [];

        if (!empty($audit->lavel_4)) {
            $lavel4Ids = explode(',', $audit->lavel_4);
            $lavel4Emails = User::whereIn('id', $lavel4Ids)->pluck('email')->toArray();
        }

        if (!empty($audit->lavel_5)) {
            $lavel5Ids = explode(',', $audit->lavel_5);
            $lavel5Emails = User::whereIn('id', $lavel5Ids)->pluck('email')->toArray();
        }

        $process_review_agency_email = null;
        if ($audit->audit_agency_id) {
            $process_review_agency_email = User::where('id', $audit->audit_agency_id)->value('email');
        }

        $ccEmails = array_filter(array_merge($lavel4Emails, $lavel5Emails));
        $zone = [];
        if ($audit->client_id ==  15) {
            $mv = ['arjun.verma@qdegrees.com', 'vaibhav.amer@qdegrees.com', 'mohammed.danish@moneyview.in',  'manvi.ojha@moneyview.in'];
        } elseif ($audit->client_id ==  74) {
            // $mv = [];
            $mv = ['Snehal.Nimkar@fibe.in', 'piyali.banerjee@fibe.in', 'Pradip.kumar@fibe.in', 'abhishek.gupta@qdegrees.com', 'joicy.kunjuman@qdegrees.com', 'Vaibhav.Amer@qdegrees.com'];

            if ($agency_details->region_id == 2 || $agency_details->region_id == 4) {
                $zone = ['abir.sengupta@fibe.in', 'ashok.nowdu@fibe.in', 'bharath.v@fibe.in'];
            }
        } else {
            $mv = [];
        }
        $allCcEmails = array_merge((array) $ccEmails, $mv, $zone);

        $tat = DB::table('users')->where('id', $audit->client_id)->value('closure_link_days');

        Mail::send('closure.audit_closure_mail', [
            'closureFormLink' => $closureFormLink,
            'process_review_month' => $process_review_month,
            'agency_name' => $agency_details->name,
            'tat'   =>  $tat,
        ], function ($message) use ($toEmails, $subject, $pdfFullPath, $allCcEmails) {
            $message->from('auditmitr@qdegrees.com', 'Audit Team')
                ->to($toEmails)
                ->cc($allCcEmails)
                ->subject($subject)
                ->attach($pdfFullPath, ['as' => 'UNSATISFACTORY_PARAMETERS.pdf']);
        });

        return true;
    }

    public function calculateScore($auditID, $client_id)
    {
        $overallScore = AuditParameterResult::where([
            'audit_id'  => $auditID,
            'client_id' => $client_id
        ])
            ->selectRaw('
            SUM(without_fatal_score) as scored,
            SUM(temp_weight) as scorable,
            ROUND((SUM(without_fatal_score) / NULLIF(SUM(temp_weight), 0)) * 100, 2) as scored_per
        ')
            ->first();
        return $overallScore;
    }

    public function getAuditParameterResults(Request $request)
    {
        // Validate the request to ensure audit_id is provided
        $request->validate([
            'audit_id' => 'required|integer'
        ]);

        // Retrieve the audit_id from the request
        $audit_id = $request->input('audit_id');

        // Fetch the parameter results for the given audit_id
        $results = AuditParameterResult::where('audit_id', $audit_id)
            ->select('parameter_id', 'temp_weight', 'with_fatal_score', 'with_fatal_score_per')
            ->get();

        // Check if there are results, if not return an error message
        if ($results->isEmpty()) {
            return response()->json([
                'status' => 0,
                'message' => 'No records found for this audit_id'
            ], 404);
        }

        // Initialize variables to calculate overall scores
        $totalScorable = 0;
        $totalScore = 0;

        // Prepare the response structure for individual parameter data
        $response = [];

        foreach ($results as $result) {
            // Fetch the parameter name using the parameter_id from qm_sheet_parameter table
            $parameter = DB::table('qm_sheet_parameters')
                ->where('id', $result->parameter_id)
                ->select('parameter')  // Assuming the 'name' column stores the parameter name
                ->first();

            // If a parameter name is found, add it to the response
            $parameterName = $parameter ? $parameter->parameter : 'Unknown';

            // Add the parameter-wise result to the response array
            $response[] = [
                'parameter_id' => $result->parameter_id,
                'parameter_name' => $parameterName,  // Include the parameter name
                'scorable' => $result->temp_weight,
                'score' => $result->with_fatal_score,
                'score_per' => $result->with_fatal_score_per
            ];

            // Accumulate totals for overall calculation
            $totalScorable += $result->temp_weight;
            $totalScore += $result->with_fatal_score;
        }

        // Calculate overall score%
        $overallScorePercent = 0;
        if ($totalScorable > 0) {
            $overallScorePercent = ($totalScore / $totalScorable) * 100;
        }

        // Determine grade based on the overall score%
        $grade = '';
        $roundedScore = round($overallScorePercent, 2); // Rounded to 2 decimal places

        if ($roundedScore >= 90) {
            $grade = 'A';
        } elseif ($roundedScore >= 75) {
            $grade = 'B';
        } elseif ($roundedScore >= 60) {
            $grade = 'C';
        } else {
            $grade = 'D';
        }

        // Add the overall stats to the main data array
        $final_score  = [];
        $final_score['final_scorable'] = $totalScorable;
        $final_score['final_scored'] = $totalScore;
        $final_score['final_score_per'] = $roundedScore;
        $final_score['grade'] = $grade;




        // Return the response as JSON with status 1 and data inside
        return response()->json([
            'status' => 1,
            'data' => $response,
            'final_score' => $final_score,
        ]);
    }
    // public function getAuditParameterResults(Request $request)
    // {
    //     $request->validate([
    //         'audit_id' => 'required|integer'
    //     ]);

    //     $audit_id = $request->audit_id;

    //     $results = AuditParameterResult::where('audit_id', $audit_id)
    //         ->select(
    //             'parameter_id',
    //             'temp_weight',
    //             'with_fatal_score',
    //             'with_fatal_score_per'
    //         )
    //         ->get();

    //     if ($results->isEmpty()) {
    //         return response()->json([
    //             'status' => 0,
    //             'message' => 'No records found for this audit_id'
    //         ], 404);
    //     }

    //     $parameterIds = $results->pluck('parameter_id')->unique()->toArray();

    //     // Get parameter details
    //     $parameters = DB::table('qm_sheet_parameters')
    //         ->whereIn('id', $parameterIds)
    //         ->select(
    //             'id',
    //             'parameter',
    //             'category',
    //             'category_weight'
    //         )
    //         ->get()
    //         ->keyBy('id');

    //     // Get total weight (scorable) from sub parameters
    //     $parameterWeights = DB::table('qm_sheet_sub_parameters')
    //         ->whereIn('qm_sheet_parameter_id', $parameterIds)
    //         ->select(
    //             'qm_sheet_parameter_id',
    //             DB::raw('SUM(weight) as total_weight')
    //         )
    //         ->groupBy('qm_sheet_parameter_id')
    //         ->pluck('total_weight', 'qm_sheet_parameter_id')
    //         ->toArray();

    //     $response = [];

    //     $totalScorable = 0;
    //     $totalScore = 0;

    //     $categoryWise = [];

    //     foreach ($results as $result) {

    //         $parameter = $parameters[$result->parameter_id] ?? null;

    //         $parameterName = $parameter->parameter ?? 'Unknown';
    //         $category = $parameter->category ?? 'Unknown';
    //         $categoryWeight = $parameter->category_weight ?? 0;

    //         // Scorable from sub parameter weights
    //         $parameterScorable = $parameterWeights[$result->parameter_id] ?? 0;

    //         $response[] = [
    //             'parameter_id'   => $result->parameter_id,
    //             'parameter_name' => $parameterName,
    //             'category'       => $category,
    //             'scorable'       => $parameterScorable,
    //             'score'          => $result->with_fatal_score,
    //             'score_per'      => $result->with_fatal_score_per
    //         ];

    //         // Overall totals
    //         $totalScorable += $parameterScorable;
    //         $totalScore += $result->with_fatal_score;

    //         // Category aggregation
    //         if (!isset($categoryWise[$category])) {
    //             $categoryWise[$category] = [
    //                 'category' => $category,
    //                 'category_weight' => $categoryWeight,
    //                 'total_scorable' => 0,
    //                 'total_score' => 0
    //             ];
    //         }

    //         $categoryWise[$category]['total_scorable'] += $parameterScorable;
    //         $categoryWise[$category]['total_score'] += $result->with_fatal_score;
    //     }

    //     // Category calculations
    //     $totalCategoryWeight = 0;
    //     $totalWeightedScore = 0;

    //     foreach ($categoryWise as &$category) {

    //         $category['score_per'] =
    //             $category['total_scorable'] > 0
    //             ? round(
    //                 ($category['total_score'] / $category['total_scorable']) * 100,
    //                 2
    //             )
    //             : 0;

    //         $category['weighted_score'] = round(
    //             ($category['score_per'] * $category['category_weight']) / 100,
    //             2
    //         );

    //         $totalCategoryWeight += $category['category_weight'];
    //         $totalWeightedScore += $category['weighted_score'];
    //     }

    //     unset($category);

    //     // Overall category result
    //     $overallCategoryScorePer =
    //         $totalCategoryWeight > 0
    //         ? round(
    //             ($totalWeightedScore / $totalCategoryWeight) * 100,
    //             2
    //         )
    //         : 0;

    //     $category_overall_result = [
    //         'total_category_weight' => $totalCategoryWeight,
    //         'total_weighted_score' => round($totalWeightedScore, 2),
    //         'overall_category_score_per' => $overallCategoryScorePer
    //     ];

    //     // Overall audit score
    //     $overallScorePercent =
    //         $totalScorable > 0
    //         ? ($totalScore / $totalScorable) * 100
    //         : 0;

    //     $roundedScore = round($overallScorePercent, 2);

    //     if ($roundedScore >= 90) {
    //         $grade = 'A';
    //     } elseif ($roundedScore >= 75) {
    //         $grade = 'B';
    //     } elseif ($roundedScore >= 60) {
    //         $grade = 'C';
    //     } else {
    //         $grade = 'D';
    //     }

    //     $final_score = [
    //         'final_scorable' => $totalScorable,
    //         'final_scored' => $totalScore,
    //         'final_score_per' => $roundedScore,
    //         'grade' => $grade
    //     ];

    //     return response()->json([
    //         'status' => 1,
    //         'data' => $response,
    //         'category_wise_result' => array_values($categoryWise),
    //         'category_overall_result' => $category_overall_result,
    //         'final_score' => $final_score
    //     ]);
    // }







    public function generateReports($auditId)
    {

        $getAudit = Audit::with('audit_parameter_result', 'audit_results')->find($auditId);

        $sheetData = QmSheet::find($getAudit->qm_sheet_id);

        $agency_id = 0;

        if ($sheetData->type == "agency") {
            $agency_details = Agency::where('id', $getAudit->agency_id)->first();
            $agency_id = $getAudit->agency_id;
        }
        if ($sheetData->type == "branch") {
            $agency_details = Branch::where('id', $getAudit->branch_id)->first();
            $agency_id = $getAudit->branch_id;
        }
        if ($sheetData->type == "yard") {
            $agency_details = Yard::where('id', $getAudit->yard_id)->first();
            $agency_id = $getAudit->yard_id;
        }
        if ($sheetData->type == "agency_repo") {
            $agency_details = AgencyRepo::where('id', $getAudit->agency_repo_id)->first();
            $agency_id = $getAudit->agency_repo_id;
        }
        if ($sheetData->type == "branch_repo") {
            $agency_details = BranchRepo::where('id', $getAudit->branch_repo_id)->first();
            $agency_id = $getAudit->branch_repo_id;
        }
        if ($sheetData->type == "yard_repo") {
            $agency_details = YardRepo::where('id', $getAudit->yard_repo_id)->first();
            $agency_id = $getAudit->yard_repo_id;
        }

        $audit_agency_name = DB::table('users')
            ->where('id', $getAudit->audit_agency_id)
            ->value('name');

        $audit_cycle = DB::table('audit_cycles')->where('id', $getAudit->audit_cycle_id)->first();
        $auditorname = DB::table('users')
            ->where('id', $getAudit->audited_by_id)
            ->value('name');
        // $parameters = $this->fetchParametersWithStatusnew($getAudit->audit_results, $getAudit->audit_parameter_result);
        $fetchResult = $this->fetchParametersWithStatusnew($getAudit->audit_results, $getAudit->audit_parameter_result);
        $parameters = $fetchResult['parameters'];
        $zeroToleranceUnsetParameters = $fetchResult['zeroToleranceUnsetParameters'];
        $artifacts = Artifact::where('audit_id', $getAudit->id)
            ->get(['id', 'sub_parameter_id', 'file']);
        foreach ($artifacts as $artifact) {
            $subParamId = $artifact->sub_parameter_id;

            if (!isset($groupedArtifacts[$subParamId])) {
                $groupedArtifacts[$subParamId] = [];
            }

            $groupedArtifacts[$subParamId][] = asset('storage/app/' . $artifact->file); // adjust if path differs
        }

        // Now attach artifacts to each subparameter
        foreach ($parameters as &$param) {
            foreach ($param['subparameters'] as &$subparam) {
                $id = $subparam['id'];
                $subparam['artifact_image_urls'] = $groupedArtifacts[$id] ?? [];
            }
        }

        $product_details = DB::table('products')->where('id', $getAudit->product_id)->first();
        $clientName = \App\User::where('id', $getAudit->client_id)->first();
        $getScoring = $this->calculateScore($getAudit->id, $getAudit->client_id);
        // $getScoring=$this->calculateScore($request->audit_id,$user->client_id);
        $cm = DB::table('users')->where('id', $getAudit->lavel_3)->value('name');
        // dd($cm);
        $rcm = '';
        if (isset($getAudit->lavel_4)) {
            $firstId = explode(',', $getAudit->lavel_4)[0];

            $rcm = DB::table('users')
                ->where('id', $firstId)
                ->value('name');
        }

        $checksheetpdfData = [
            'agency_name' => $agency_details->name,
            'audit_date' => Carbon::parse($getAudit->created_at)->toDateString(),
            'parameters' => $parameters,
            'agency_details' => $agency_details,
            'product_details' => $product_details,
            'audit_cycle' => $audit_cycle->name,
            'audit_agency_name' => $audit_agency_name,
            'score' => $getAudit->overall_score,
            'sheetData' => $sheetData,
            // 'audit_agency_name' => $audit_agency_name,
            'client_name' => $clientName->name,
            'client_id' => $getAudit->client_id,
            'temp_total_weightage' => $getScoring->scorable,
            'present_auditor' => $getAudit->present_auditor ? $getAudit->present_auditor : '',
            'audit_type' => (isset($request->is_virtual_audit) && $request->is_virtual_audit == 1) ? 'Virtual Audit' : 'Physical Audit',
            'score_percentage' => round($getScoring->scored_per) . "%",
            'zeroToleranceUnsetParameters' => $zeroToleranceUnsetParameters,
            'rcm' => $rcm,
            'cm' => $cm,
            'agency_manager' => isset($getAudit->agency_manager_name) ? $getAudit->agency_manager_name : '',
        ];
        // Generate PDF
        if ($getAudit->client_id == 15) {
            $pdf = Pdf::loadView('audit.checksheet_pdf', $checksheetpdfData);
        } else {
            $pdf = Pdf::loadView('audit.checksheet_pdf_fibe_new', $checksheetpdfData);
        }
        $pdfContent = $pdf->output();
        $folderPath = 'audit_reports/' . date('Y') . '/' . date('F');
        $fileName = 'audit_checksheet_' . time() . '.pdf';
        $ChecksheetfullPath = $folderPath . '/' . $fileName;

        // Store the PDF file in the public storage
        Storage::disk('public')->put($ChecksheetfullPath, $pdfContent);

        if ($getAudit->client_id == 74) {
            $scoreString = $getAudit->score_percentage;
            $score = floatval(str_replace('%', '', $scoreString));

            if ($score == 100) {
                $rating_grade = 5;
            } elseif ($score >= 91 && $score <= 99) {
                $rating_grade = 4;
            } elseif ($score >= 81 && $score <= 90) {
                $rating_grade = 3;
            } elseif ($score >= 71 && $score <= 80) {
                $rating_grade = 2;
            } elseif ($score >= 61 && $score <= 70) {
                $rating_grade = 1;
            } else {
                $rating_grade = 0;
            }
        } else {
            $rating_grade = $getAudit->grade ?? null; // use existing grade variable
        }

        $auditResultpdfData = [
            'Audit_agency' => $audit_agency_name,
            'grade' => $getAudit->grade ?? null,
            'rating_grade' => $rating_grade ?? null,
            'score_percentage' => $getAudit->score_percentage ?? null,
            'overall_score' => $getAudit->overall_score ?? null,
            'agency_name' => $agency_details->name ?? null,
            'location' => $agency_details->location ?? null,
            'product_name' => $product_details->name ?? null,
            'auditor_id' => $auditorname,
            'auditor_name' => $getAudit->present_auditor ?? null,
            'audit_cycle' => $audit_cycle->name,
            'audit_date' => date('F Y', strtotime($getAudit->created_at)),
            'client_name' => $clientName->name,
            'client_email' => $clientName->email,
            'audit_agency_name' => $audit_agency_name,
            'manager_email' => $getAudit->client_id == 15 ? 'mohammed.danish@moneyview.in' : '',
            'manager_email2' => $getAudit->client_id == 15 ? 'thimmaiah.b.m@moneyview.in' : '',
            'client_id' => $getAudit->client_id,
        ];

        $pdf = Pdf::loadView('audit.audit_result_pdf', $auditResultpdfData);
        $pdfContent = $pdf->output();

        $fileName = 'Audit_Result_' . time() . '.pdf';
        $aufullPath = $folderPath . '/' . $fileName;
        Storage::disk('public')->put($aufullPath, $pdfContent);

        $excludedSubParameterIds = [];
        $allUnsetParams = DB::table('audit_results')
            ->join('qm_sheet_sub_parameters', 'audit_results.sub_parameter_id', '=', 'qm_sheet_sub_parameters.id')
            ->where('audit_results.audit_id', $auditId)
            ->where('audit_results.option_selected', 'Unsatisfactory')
            ->select('qm_sheet_sub_parameters.sub_parameter as parameter_name', 'audit_results.*', 'audit_results.remark as remarks')
            ->get();
        // Filter out the excluded parameters for the PDF and further processing
        $unsetParamsForPdf = $allUnsetParams->filter(function ($param) use ($excludedSubParameterIds) {
            return !in_array($param->sub_parameter_id, $excludedSubParameterIds);
        });
        $usfullPath = null;

        if ($unsetParamsForPdf->isNotEmpty()) {

            $randomString = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 25);
            // Check if audit already exists in closure_audits
            $auditExists = DB::table('closure_audits')
                ->where('audit_id', $auditId)
                ->first();

            $closureId = $auditExists
                ? $auditExists->id
                : DB::table('closure_audits')->insertGetId([
                    'audit_id' => $auditId,
                    'agency_id' => $agency_id,
                    'remarks' => '',
                    'link' =>  $randomString,
                    'created_at' => now(),
                    'audit_agency_id' => $getAudit->audit_agency_id,
                    'client_id' => $clientName->id,
                ]);

            $uspdfData = [
                'unsetParams' => $unsetParamsForPdf,
                'audit_id' => $auditId,
                'agency_details' => $agency_details,
                'audit_details' => $getAudit,
                'process_review_month' => $audit_cycle->name,
                // 'audit_agency_id' => $audit_agency_id,
                'client_name' => $clientName->name,
                'background_color' => $clientName->color_code,
                'product_name' => $product_details->name ?? null,
                'sheetData' => $sheetData
            ];

            // Generate PDF
            $pdf = PDF::loadView('closure.audit_closure_pdf', $uspdfData);
            $pdfContent = $pdf->output();

            // Create a month-wise folder for storing the PDF           
            $fileName = 'audit_closure_' . time() . '.pdf';
            $usfullPath = $folderPath . '/' . $fileName;

            // Store the PDF file in the public storage
            Storage::disk('public')->put($usfullPath, $pdfContent);
        } else {
            $closureId = DB::table('closure_audits')
                ->insertGetId([
                    'audit_id' => $auditId,
                    'agency_id' => $agency_id,
                    'remarks' => '',
                    'created_at' => now(),
                    'status' => '1',
                    'audit_agency_id' => $getAudit->audit_agency_id,
                    'client_id' => $clientName->id,
                ]);
        }

        // Save the file path and audit details to the database
        $existingReport = DB::table('audit_reports')->where('audit_id', $auditId)->first();

        if ($existingReport) {
            //If the entry exists,update the file path for audit_result_pdf
            $report = DB::table('audit_reports')
                ->where('audit_id', $auditId)
                ->update([
                    'checksheet_pdf' => $ChecksheetfullPath,
                    'audit_result_pdf' => $aufullPath,
                    'closure_pdf' => $usfullPath,
                    'updated_at' => now(),
                ]);
        } else {
            $report = DB::table('audit_reports')->insert([
                'audit_id' => $auditId,
                'audit_date' => date('Y-m-d', strtotime($getAudit->created_at)),
                'agency_id' => $agency_details->id,
                'checksheet_pdf' => $ChecksheetfullPath,
                'audit_result_pdf' => $aufullPath,
                'closure_pdf' => $usfullPath,
                'process_review_period' => $audit_cycle->name,
                'user_id' => $getAudit->audited_by_id,
                'created_at' => now(),
                'updated_at' => now(),
                'client_id' => $getAudit->client_id,
            ]);
        }



        return $report;
    }

    public function fetchParametersWithStatusnew($audit_result, $param_result)
    {

        $parameters = [];
        $zeroToleranceUnsetParameters = 0;
        foreach ($param_result as $param) {
            $parameter = QmSheetParameter::find($param->parameter_id);
            if (!$parameter) {
                continue; // Skip if the parameter is not found
            }
            $parameterArray = [
                'name' => $parameter->parameter, // Name of the parameter
                'subparameters' => [], // Initialize sub-parameters array
            ];

            foreach ($audit_result as $sub_param) {
                if ($sub_param->parameter_id == $param->parameter_id) {
                    $subParameter = QmSheetSubParameter::find($sub_param->sub_parameter_id);

                    if (!$subParameter) {
                        continue; // Skip if sub-parameter is not found
                    }
                    if (
                        strtolower($sub_param['option_selected']) === 'unsatisfactory' &&
                        strtolower($subParameter->Severity) === 'zero tolerance'
                    ) {
                        $zeroToleranceUnsetParameters++;
                    }

                    // Add sub-parameter data to array
                    $parameterArray['subparameters'][] = [
                        'id' => $subParameter->id,
                        'name' => $subParameter->sub_parameter, // Name of the sub-parameter
                        'status' => $sub_param->option_selected, // Status (Satisfactory/Unsatisfactory)
                        'remarks' => $sub_param->remark,
                        'severity' => $subParameter->Severity,
                        'weight' => $subParameter->weight,
                        'score' => $sub_param['score'],
                    ];
                }
            }

            $parameters[] = $parameterArray;
        }
        //  echo "<pre>"; print_r($parameters); die;
        // Return the structured parameter data
        return [
            'parameters' => $parameters,
            'zeroToleranceUnsetParameters' => $zeroToleranceUnsetParameters
        ];
    }


    public function update_audit(Request $request)
    {

        if (!$request->header('Authorizations') || $request->header('Authorizations') == "") {
            $data = array('status' => 0, 'message' => 'Authorizations key is required in api headers.', 'data' => array());
            return response(json_encode($data), 200);
        }
        $getUser = User::where('auth_key', $request->header('Authorizations'))->first();
        if (!$getUser) {
            $data = array('status' => 0, 'message' => 'User not found', 'data' => array());
            return response(json_encode($data), 200);
        }

        logger($request);

        DB::beginTransaction();
        try {
            //create audit record
            $new_ar = Audit::find($request->submission_data['id']);
            $new_ar->qm_sheet_id = $request->submission_data['qm_sheet_id'];
            //$new_ar->audited_by_id = Auth::user()->id;
            $new_ar->is_critical = isset($request->submission_data['is_critical']) ? ($request->submission_data['is_critical']) : 0;
            $new_ar->overall_score = $request->submission_data['overall_score'];
            // $new_ar->audit_date = Carbon::now()->format('Y-m-d');
            // $new_ar->with_fatal_score_per = $request->submission_data['overall_score'];
            $new_ar->branch_id = (isset($request->submission_data['branch_id'])) ? $request->submission_data['branch_id'] : null;
            $new_ar->agency_id = (isset($request->submission_data['agency_id'])) ? $request->submission_data['agency_id'] : null;
            $new_ar->yard_id = (isset($request->submission_data['yard_id'])) ? $request->submission_data['yard_id'] : null;
            $new_ar->product_id = (isset($request->submission_data['product_id'])) ? $request->submission_data['product_id'] : null;
            $new_ar->branch_repo_id = (isset($request->submission_data['branch_repo_id'])) ? $request->submission_data['branch_repo_id'] : null;
            $new_ar->agency_repo_id = (isset($request->submission_data['agency_repo_id'])) ? $request->submission_data['agency_repo_id'] : null;
            $new_ar->lavel_3 = (isset($request->submission_data['collection_manager_id'])) ? $request->submission_data['collection_manager_id'] : null;
            $new_ar->lavel_4 = isset($request->submission_data['lavel_4'])
                ? implode(',', $request->submission_data['lavel_4'])
                : null;
            $new_ar->lavel_5 = isset($request->submission_data['lavel_5'])
                ? implode(',', $request->submission_data['lavel_5'])
                : null;
            $new_ar->grade = (isset($request->submission_data['grade'])) ? $request->submission_data['grade'] : null;
            $new_ar->score_percentage = (isset($request->submission_data['with_fatal_score_per'])) ? $request->submission_data['with_fatal_score_per'] : null;
            $new_ar->update();

            if (isset($request->submission_data['agency_id']) && isset($request->submission_data['agency_manager']) && $request->submission_data['agency_manager'] != '') {
                Agency::where('id', $new_ar->agency_id)->update(['agency_manager' => $request->submission_data['agency_manager'], 'agency_phone' => $request->submission_data['agency_phone']]);
            }

            if (isset($request->submission_data['status']) && $request->submission_data['status'] == 'submit') {
                SavedAudit::where('audit_id', $new_ar->id)->delete();
            }

            if ($new_ar->id) {
                // store parameter wise data
                foreach ($request->parameters as $key => $value) {

                    $new_arb = AuditParameterResult::find($value['id']);
                    $new_arb->audit_id =  $new_ar->id;
                    $new_arb->parameter_id = $key;
                    $new_arb->qm_sheet_id = $request->submission_data['qm_sheet_id'];
                    $new_arb->orignal_weight = $value['parameter_weight'];
                    $new_arb->temp_weight = $value['temp_total_weightage'];
                    $new_arb->with_fatal_score = $value['score_with_fatal'];
                    // $new_arb->without_fatal_score = $value['score_without_fatal'];
                    $new_arb->without_fatal_score = $value['score_with_fatal'];
                    if ($value['temp_total_weightage'] != 0) {
                        $new_arb->with_fatal_score_per = ($value['score_with_fatal'] / $value['temp_total_weightage']) * 100;
                        // $new_arb->without_fatal_score_pre = ($value['score_without_fatal'] / $value['temp_total_weightage'])*100;
                        $new_arb->without_fatal_score_pre = ($value['score_with_fatal'] / $value['temp_total_weightage']) * 100;
                    }
                    // $new_arb->is_critical = $value['is_fatal'];
                    $new_arb->update();
                    // store sub parameter wise data
                    if (isset($value['subs'])) {
                        foreach ($value['subs'] as $key_sb => $value_sb) {
                            if ($value_sb['temp_weight']); {
                                if (isset($value_sb['id'])) {
                                    $new_arc = AuditResult::find($value_sb['id']);
                                    $new_arc->audit_id =  $new_ar->id;
                                    $new_arc->parameter_id = $key;
                                    $new_arc->sub_parameter_id = $key_sb;
                                    // $new_arc->is_critical = $value_sb['is_fatal'];
                                    // $new_arc->is_non_scoring = $value_sb['is_non_scoring'];
                                    // $temp_selected_opt = explode("_",$value_sb['selected_option_model']);
                                    $new_arc->selected_option = ($value_sb['temp_weight'] != 'Critical') ? $value_sb['temp_weight'] : 0;
                                    $new_arc->option_selected = (isset($value_sb['option'])) ? $value_sb['option'] : null;
                                    $new_arc->is_critical = ($value_sb['temp_weight'] != 'Critical') ? 0 : 1;
                                    // $new_arc->score = ($value_sb['score']!='Critical')?$value_sb['score']:0;
                                    if ($value_sb['score'] != 'rating') {
                                        $new_arc->score = ($value_sb['score'] != 'Critical') ? $value_sb['score'] : 0;
                                        $new_arc->is_percentage = $value_sb['is_percentage'];
                                        $new_arc->selected_per = (isset($value_sb['selected_per']) && $value_sb['selected_per'] != 'select percentage') ? $value_sb['selected_per'] : null;
                                    } else {
                                        $new_arc->score = ($value_sb['score'] != 'Critical') ? $value_sb['temp_weight'] : 0;
                                        $new_arc->is_percentage = $value_sb['is_percentage'];
                                        $new_arc->selected_per = ($value_sb['selected_per'] != 'select percentage') ? $value_sb['selected_per'] : null;
                                    }

                                    $new_arc->remark = $value_sb['remark'];
                                    $new_arc->update();
                                } else {

                                    $new_arc = new AuditResult;
                                    $new_arc->audit_id =  $new_ar->id;
                                    $new_arc->parameter_id = $key;
                                    $new_arc->sub_parameter_id = $key_sb;
                                    $new_arc->selected_option = ($value_sb['temp_weight'] != 'Critical') ? $value_sb['temp_weight'] : 0;
                                    $new_arc->option_selected = (isset($value_sb['option'])) ? $value_sb['option'] : null;
                                    $new_arc->is_critical = ($value_sb['temp_weight'] != 'Critical') ? 0 : 1;
                                    if ($value_sb['score'] != 'rating') {
                                        $new_arc->score = ($value_sb['score'] != 'Critical') ? $value_sb['score'] : 0;
                                        $new_arc->is_percentage = $value_sb['is_percentage'];
                                        $new_arc->selected_per = (isset($value_sb['selected_per']) && $value_sb['selected_per'] != 'select percentage') ? $value_sb['selected_per'] : null;
                                    } else {
                                        $new_arc->score = ($value_sb['score'] != 'Critical') ? $value_sb['temp_weight'] : 0;
                                        $new_arc->is_percentage = $value_sb['is_percentage'];
                                        $new_arc->selected_per = ($value_sb['selected_per'] != 'select percentage') ? $value_sb['selected_per'] : null;
                                    }
                                    $new_arc->remark = $value_sb['remark'];
                                    $new_arc->save();
                                }
                            }
                        }
                    }
                }
            }
            // Commit Transaction
            DB::commit();
        } catch (Exception $e) {
            // Rollback Transaction
            DB::rollback();
            // return redirect('user')->with('success', ['Retry Again!']);
            $response = array(
                'status' => 0,
                'message' => 'Retry Again.',
                'audit_id' => array()
            );
            return response(json_encode($response), 200);
        }
        $response = array(
            'status' => 1,
            'message' => 'Audit saved successfully.',
            'audit_id' => $new_ar->id
        );
        return response(json_encode($response), 200);
        // return response()->json(['status'=>200,'message'=>"Audit saved successfully."], 200); 
    }

    public function artifact_audit_file_links(Request $request)
    {
        if (!$request->header('Authorizations') || $request->header('Authorizations') == "") {
            $data = array('status' => 0, 'message' => 'Authorizations key is required in api headers.', 'data' => array());
            return response()->json(['status' => 0, 'message' => "Error", 'data' => $data], 400);
        }
        $validator = Validator::make($request->all(), [
            'audit_id' => 'required | exists:artifacts,audit_id',
            'parameter_id' => 'required | exists:artifacts,parameter_id',
            'sub_parameter_id' => 'required | exists:artifacts,sub_parameter_id'
        ]);
        if ($validator->fails()) {
            $data = array('status' => 0, 'message' => 'Validation Errors', 'data' => $validator->errors());
            return response()->json(['status' => 0, 'message' => "Error", 'data' => $data], 400);
        } else {
            $data = Artifact::where('audit_id', $request->audit_id)
                ->where('parameter_id', $request->parameter_id)
                ->where('sub_parameter_id', $request->sub_parameter_id)
                ->get();
            $data1 = [];
            if (!empty($data)) {
                foreach ($data as $key => $getArtifacts_values) {
                    $p = array();
                    $url = URL::to('/');
                    $p['id'] = $getArtifacts_values->id;
                    $p['sheet_id'] = $getArtifacts_values->sheet_id;
                    $p['parameter_id'] = $getArtifacts_values->parameter_id;
                    $p['sub_parameter_id'] = $getArtifacts_values->sub_parameter_id;
                    $p['file'] = $url . '/public/artifects/' . $getArtifacts_values->file;
                    $p['created_at'] = (string)$getArtifacts_values->created_at;
                    $p['updated_at'] = (string)$getArtifacts_values->updated_at;
                    $p['audit_id'] = $getArtifacts_values->audit_id;
                    $data1[] = $p;
                }
            }
            return response()->json(['status' => 1, 'message' => "Success", 'data' => $data1], 200);
            // return response(json_encode($data), 200);
        }
    }

    #getaudit cycle api
    public function get_audit_cycle(Request $request)
    {

        if (!$request->header('Authorizations') || $request->header('Authorizations') == "") {
            $data = array('status' => 0, 'message' => 'Authorizations key is required in api headers.', 'data' => array());
            return response(json_encode($data), 200);
        }
        $getUser = User::where('auth_key', $request->header('Authorizations'))->first();
        if (!$getUser) {
            $data = array('status' => 0, 'message' => 'User not found', 'data' => array());
            return response(json_encode($data), 200);
        }
        // echo "string";
        // die();
        $get_audits_cycle = DB::table('audit_cycles')
            ->where('status', 1)->where('client_id', $getUser->client_id)
            ->limit(3)
            ->select('id', 'name')
            ->get();

        // print_r($get_audits_cycle);
        // die();
        return response()->json(['status' => True, 'message' => 'Complete Audit Cycle is Here', 'details' => $get_audits_cycle]);
    }



    public function formet_user_list(Request $request)
    {
        // if (!$request->header('Authorizations')) {
        //     return response()->json(['status' => 0, 'message' => 'Authorization key is required in API headers.', 'data' => []], 200);
        // }

        // // Authenticate user with auth key
        // $user = User::where('auth_key', $request->header('Authorizations'))->first();
        // if (!$user) {
        //     return response()->json(['status' => 0, 'message' => 'User not found', 'data' => []], 200);
        // }

        // $users = User::with('roles')
        //     ->where('users.active_status', 0)
        //     ->where('client_id', $user->client_id)
        //     ->whereDoesntHave('roles', function ($query) {
        //         $query->whereIn('name', ['Admin', 'Client', 'Quality Auditor']);
        //     })
        //     ->get()
        //     ->toArray();

        $users = User::with('roles')
            ->where('users.active_status', 0)
            // ->where('client_id', 74) // this is used for now but remove this when in header Authorizations send by APP Developer and upper code uncommented
            ->whereDoesntHave('roles', function ($query) {
                $query->whereIn('name', ['Admin', 'Client', 'Quality Auditor']);
            })
            ->get()
            ->toArray();
        $formattedUsers = [];
        $Level_5 = [];

        if (!empty($users)) {
            foreach ($users as $user) {
                if (!empty($user['roles'])) {
                    $roleNamesArray = array_column($user['roles'], 'name');
                    $roleNames = implode(', ', $roleNamesArray);

                    // Check if the user has any of the specified roles for Level 5
                    $level5Roles = [
                        'National Collection Manager',
                        'Group Product Head',
                        'Head - Credit Card Collection',
                        'Head - Tele-Calling - Credit Card Collection',
                        'Head - Credit Card Collection - RBL Supercard',
                        'Head of the Collections',
                        'Zonal Collection Manager',
                        'Internal',
                    ];

                    if (array_intersect($level5Roles, $roleNamesArray)) {
                        $Level_5[$user['id']] = $user['name'] . ' - ' . $user['employee_id'] . ' - ' . $roleNames;
                    } else {
                        $formattedUsers[$user['id']] = $user['name'] . ' - ' . $user['employee_id'] . ' - ' . $roleNames;
                    }
                } else {
                    $formattedUsers[$user['id']] = $user['name'] . ' - ' . $user['employee_id'] . ' - No Role';
                }
            }
        } else {
            $formattedUsers = [];
        }

        return response()->json(['status' => True, 'message' => 'Complete Audit Cycle is Here', 'details' => $formattedUsers, 'Level_5' => $Level_5]);
    }


    public function sendAgencyOtp(Request $request)
    {
        //  echo 'dsfs'; die;
        // Validate the incoming request
        if (!$request->header('Authorizations')) {
            return response()->json(['status' => 0, 'message' => 'Authorization key is required in API headers.', 'data' => []], 200);
        }

        // Authenticate user with auth key
        $user = User::where('auth_key', $request->header('Authorizations'))->first();
        if (!$user) {
            return response()->json(['status' => 0, 'message' => 'User not found', 'data' => []], 200);
        }

        $request->validate([
            'agency_email' => 'required|email',
            'agency_id' => 'required|integer',
            'parameters' => 'required|array',
            'overall_score' => 'nullable|numeric',
        ]);
        $audit = Audit::where('id', $request->audit_id)->first();
        // Extract email and agency details
        $email = $request->agency_email;
        $agency_details = DB::table('agencies')->where('id', $request->agency_id)->first();
        $product_details = DB::table('products')->where('id', $request->product_id)->first();
        $audit_cycle = DB::table('audit_cycles')->where('id', $request->audit_cycle)->first();

        if (!$agency_details) {
            return response()->json(['message' => 'Agency not found'], 404);
        }

        // Generate OTP
        $otp = rand(100000, 999999);

        // Use updateOrInsert for OTP data
        $otpData = [
            'otp' => $otp,
            'mobile_number' => null,
            'type' => 'agency',
            'email' => $email,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
        DB::table('otp_verifications')->updateOrInsert(
            [
                'audit_id' => 1,
                'agency_id' => $request->agency_id,
                'type' => 'agency',
                'email' => $email,
            ],
            $otpData
        );

        // Fetch parameters with status
        $fetchResult = $this->fetchParametersWithStatus($request->parameters);
        $parameters = $fetchResult['parameters'];
        $zeroToleranceUnsetParameters = $fetchResult['zeroToleranceUnsetParameters'];
        $artifacts = Artifact::where('audit_id', $request->audit_id)
            ->get(['id', 'sub_parameter_id', 'file']);
        foreach ($artifacts as $artifact) {
            $subParamId = $artifact->sub_parameter_id;

            if (!isset($groupedArtifacts[$subParamId])) {
                $groupedArtifacts[$subParamId] = [];
            }

            $groupedArtifacts[$subParamId][] = asset('storage/app/' . $artifact->file); // adjust if path differs
        }

        // Now attach artifacts to each subparameter
        foreach ($parameters as &$param) {
            foreach ($param['subparameters'] as &$subparam) {
                $id = $subparam['id'];
                $subparam['artifact_image_urls'] = $groupedArtifacts[$id] ?? [];
            }
        }
        $audit_date = Carbon::now()->toDateString();
        $clientName = User::where('id', $user->client_id)->value('name');
        $audit_agency_email = $user->created_by;
        $audit_agency_name = User::where('email', $audit_agency_email)->value('name');
        $getScoring = $this->calculateScore($request->audit_id, $user->client_id);
        $cm = DB::table('users')->where('id', $audit->lavel_3)->value('name');
        // dd($cm);
        $rcm = '';
        if (isset($audit->lavel_4)) {
            $firstId = explode(',', $audit->lavel_4)[0];

            $rcm = DB::table('users')
                ->where('id', $firstId)
                ->value('name');
        }
        // Prepare data for PDF
        $pdfData = [
            'agency_name' => $agency_details->name,
            'audit_date' => Carbon::now()->toDateString(),
            'parameters' => $parameters,
            'zeroToleranceUnsetParameters' => $zeroToleranceUnsetParameters,
            'agency_details' => $agency_details,
            'score' => $request->input('overall_score', 0),
            'product_details' => $product_details,
            'audit_cycle' => $audit_cycle->name,
            'client_name' => $clientName,
            'audit_agency_name' => $audit_agency_name,
            'client_id' => $user->client_id,
            'temp_total_weightage' => $getScoring->scorable,
            'present_auditor' => $audit->present_auditor ? $audit->present_auditor : '',
            'audit_type' => (isset($request->is_virtual_audit) && $request->is_virtual_audit == 1) ? 'Virtual Audit' : 'Physical Audit',
            'score_percentage' => round($getScoring->scored_per) . "%",
            'rcm' => $rcm,
            'cm' => $cm,
            'agency_manager' => isset($audit->agency_manager_name) ? $audit->agency_manager_name : '',
        ];

        // Generate PDF
        if ($user->client_id == 15) {
            $pdf = Pdf::loadView('audit.checksheet_pdf', $pdfData);
        } else {
            $pdf = Pdf::loadView('audit.checksheet_pdf_fibe_new', $pdfData);
        }


        if ($user->client_id == 15) {
            $client_mail = ['mohammed.danish@moneyview.in', 'manvi.ojha@moneyview.in'];
            // $bccEmails = ['aditya.diwakar@qdegrees.com', 'sakshi.bhargava@qdegrees.com'];
            $bccEmails = ['arjun.verma@qdegrees.com', 'vaibhav.amer@qdegrees.com'];
        } else {
            $client_mail = null;
            $bccEmails = [];
        }
        // $pdf = Pdf::loadView('audit.checksheet_pdf', $pdfData);
        $pdfContent = $pdf->output();
        $clientColor = User::where('id', $user->client_id)->value('color_code');
$clientId = $user->client_id;
        // Send email with OTP and PDF attached
        $subject = "Your OTP for Audit Confirmation";
        Mail::send('audit.agency_otp_mail', ["client_name" => $clientName,"clientId" => $clientId,  "client_color" => $clientColor, "otp" => $otp, "agency_details" => $agency_details, "audit_date" => $audit_date], function ($message) use ($email, $subject, $pdfContent, $client_mail, $bccEmails) {
            $message->from('auditmitr@qdegrees.com', 'Audit Team')
                ->to($email)
                ->subject($subject)
                ->attachData($pdfContent, 'Audit_Checksheet.pdf', [
                    'mime' => 'application/pdf',
                ]);

            if ($client_mail) {
                $message->cc($client_mail);
            }
            if (!empty($bccEmails)) {
                $message->bcc($bccEmails);
            }
        });

        return response()->json(['message' => 'OTP and PDF sent successfully', 'otp' => $otp]);
    }

    public function sendCollectionManagerOtp(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            //  'type' => 'required',//co
            'agency_email' => 'required|email',
            'agency_id' => 'required|integer',
            'collection_manager_id' => 'required|integer',
            'parameters' => 'required|array',
            'overall_score' => 'nullable|numeric',
        ]);
        //  echo 'adsa'; die;
        // Extract email and agency details
        $collection_manager_id = $request->collection_manager_id;

        $collection_manager_email = User::where('id', $collection_manager_id)->pluck('email')->first();
        $email = $collection_manager_email;
        $agency_details = DB::table('agencies')->where('id', $request->agency_id)->first();
        $product_details = DB::table('products')->where('id', $request->product_id)->first();
        $audit_cycle = DB::table('audit_cycles')->where('id', $request->audit_cycle)->first();

        if (!$agency_details) {
            return response()->json(['message' => 'Agency not found'], 404);
        }

        // Generate OTP
        $otp = rand(100000, 999999);

        // Use updateOrInsert for OTP data
        $otpData = [
            'otp' => $otp,
            'mobile_number' => null,
            'type' => 'collection_manager',
            'email' => $email,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
        DB::table('otp_verifications')->updateOrInsert(
            [
                'audit_id' => 1,
                'agency_id' => $request->agency_id,
                'type' => 'collection_manager',
                'email' => $email,
            ],
            $otpData
        );

        // Fetch parameters with status
        $parameters = $this->fetchParametersWithStatus($request->parameters);
        $audit_date = Carbon::now()->toDateString();
        // Prepare data for PDF
        $pdfData = [
            'agency_name' => $agency_details->name,
            'audit_date' => Carbon::now()->toDateString(),
            'parameters' => $parameters,
            'agency_details' => $agency_details,
            'score' => $request->input('overall_score', 0),
            'product_details' => $product_details,
            'audit_cycle' => $audit_cycle->name,
        ];

        // Generate PDF
        $pdf = Pdf::loadView('audit.checksheet_pdf', $pdfData);
        $pdfContent = $pdf->output();

        // Send email with OTP and PDF attached
        $subject = "Your OTP for Audit Confirmation";
        Mail::send('audit.collection_manager_otp_mail', ["otp" => $otp, "agency_details" => $agency_details, "audit_date" => $audit_date], function ($message) use ($email, $subject, $pdfContent) {
            $message->from('noreplyall@qdegrees.org', 'Audit Team')
                ->to($email)
                ->subject($subject)
                ->attachData($pdfContent, 'Audit_Checksheet.pdf', [
                    'mime' => 'application/pdf',
                ]);
        });

        return response()->json(['message' => 'OTP and PDF sent successfully', 'otp' => $otp]);
    }

    public function verifyAgencyOtp(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'otp' => 'required|numeric',
            'agency_email' => 'required',
        ]);

        $otp = $request->input('otp');
        $agency_email = $request->input('agency_email');
        // Check if OTP exists in the database and is valid
        $otpRecord = DB::table('otp_verifications')
            ->where('otp', $otp)
            ->where('email', $agency_email)
            ->where('type', 'agency')
            ->where('created_at', '>', Carbon::now()->subMinutes(10)) // OTP expiry (10 minutes)
            ->first();

        if ($otpRecord) {
            // OTP is valid
            return response()->json(['status' => 'valid']);
        } else {
            // OTP is invalid
            return response()->json(['status' => 'invalid'], 400);
        }
    }

    public function verifyCollectionManagerOtp(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'otp' => 'required|numeric',
            'collection_manager_id' => 'required',
        ]);

        $otp = $request->input('otp');
        $collection_manager_id = $request->input('collection_manager_id');

        $collection_manager_email = User::where('id', $collection_manager_id)->pluck('email')->first();
        // Check if OTP exists in the database and is valid
        $otpRecord = DB::table('otp_verifications')
            ->where('otp', $otp)
            ->where('email', $collection_manager_email)
            ->where('type', 'collection_manager')
            ->where('created_at', '>', Carbon::now()->subMinutes(10)) // OTP expiry (10 minutes)
            ->first();

        if ($otpRecord) {
            // OTP is valid
            return response()->json(['status' => 'valid']);
        } else {
            // OTP is invalid
            return response()->json(['status' => 'invalid'], 400);
        }
    }

    public function fetchParametersWithStatus($parametersData)
    {
        $parameters = [];
        $zeroToleranceUnsetParameters = 0; // Initialize counter

        foreach ($parametersData as $parameterGroup) {
            foreach ($parameterGroup as $parameterId => $parameterInfo) {
                $parameter = QmSheetParameter::find($parameterId);

                if (!$parameter) {
                    continue;
                }

                $parameterArray = [
                    'name' => $parameter->parameter,
                    'subparameters' => []
                ];


                if (isset($parameterInfo['subs']) && is_array($parameterInfo['subs'])) {

                    foreach ($parameterInfo['subs'] as $subId => $subInfo) {
                        $subParameter = QmSheetSubParameter::find($subId);

                        if (!$subParameter) {
                            continue;
                        }
                        if (
                            strtolower($subInfo['option']) === 'unsatisfactory' &&
                            strtolower($subParameter->Severity) === 'zero tolerance'
                        ) {
                            $zeroToleranceUnsetParameters++;
                        }

                        $parameterArray['subparameters'][] = [
                            'id' => $subParameter->id,
                            'name' => $subParameter->sub_parameter,
                            'status' => $subInfo['option'],
                            'remarks' => $subInfo['remark'],
                            'severity' => $subParameter->Severity,
                            'weight' => $subParameter->weight,
                            'score' => $subInfo['score'],
                        ];
                    }
                }


                $parameters[] = $parameterArray;
            }
        }

        return [
            'parameters' => $parameters,
            'zeroToleranceUnsetParameters' => $zeroToleranceUnsetParameters
        ];
    }

    public function auditor_assign_case_details(Request $request)
    {
        // Check for Authorization header
        if (!$request->header('Authorizations')) {
            return response()->json(['status' => 0, 'message' => 'Authorization key is required in API headers.', 'data' => []], 200);
        }

        // Authenticate user with auth key
        $user = User::where('auth_key', $request->header('Authorizations'))->first();
        if (!$user) {
            return response()->json(['status' => 0, 'message' => 'User not found', 'data' => []], 200);
        }

        // Fetch assignment details by assign_id
        $data = AuditorAssign::where('id', $request->assign_id)->first();

        // Handle case where assignment is not found
        if (!$data) {
            return response()->json(['status' => 0, 'message' => 'Assignment not found', 'data' => []], 200);
        }

        return response()->json(['status' => 1, 'message' => 'Assignment Details', 'data' => $data], 200);
    }


    public function auditor_assign_case_list(Request $request)
    {
        // Check for Authorization header
        if (!$request->header('Authorizations')) {
            return response()->json(['status' => 0, 'message' => 'Authorization key is required in API headers.', 'data' => []], 200);
        }

        // Authenticate user with auth key
        $user = User::where('auth_key', $request->header('Authorizations'))->first();
        if (!$user) {
            return response()->json(['status' => 0, 'message' => 'User not found', 'data' => []], 200);
        }

        // Fetch assignments for the authenticated user
        $auditorAssignCases = AuditorAssign::where('auditor_email', $request->email)
            ->get();

        // Prepare response
        $response = [
            'status' => count($auditorAssignCases) > 0 ? 1 : 0,
            'message' => count($auditorAssignCases) > 0 ? 'Assignment List' : 'No Assignments Found',
            'data' => $auditorAssignCases
        ];

        return response()->json($response, 200);
    }

    public function checkUnsatObservation(Request $request)
    {
        $agencyId       = $request->agency_id;
        $parameterId    = $request->parameter_id;
        $subParameterId = $request->sub_parameter_id;
        $observation    = $request->observation;

        //Only check if observation is "Unsatisfactory"
        if (strtolower(trim($observation)) !== 'unsatisfactory') {
            return response()->json(['status' => '1', 'message' => 'Observation is satisfactory. No further action needed.']);
        }

        // Step 1: Fetch questions for this sub_parameter
        $questions = DB::table('questions')
            ->where('sub_parameter_id', $subParameterId)
            ->select('id', 'question_text')
            ->get();

        $questionsList = $questions->isNotEmpty() ? $questions : [];

        //Step 2: Fetch last observation from audit_results
        $lastEntry = DB::table('audit_results')
            ->where('agency_id', $agencyId)
            ->where('parameter_id', $parameterId)
            ->where('sub_parameter_id', $subParameterId)
            ->orderBy('created_at', 'desc')
            ->first();

        $repeatIssue = null;

        if (!empty($lastEntry) && strtolower(trim($lastEntry->option_selected)) === 'unsatisfactory') {
            // Fetch artifact if available
            $artifact = DB::table('artifacts')
                ->where('audit_id', $lastEntry->audit_id)
                ->where('parameter_id', $parameterId)
                ->where('sub_parameter_id', $subParameterId)
                ->first();

            $artifactLink = $artifact
                ?  asset('storage/app/' . $artifact->file)
                : null;

            $repeatIssue = [
                'remark' => $lastEntry->remark,
                'artifact' => $artifactLink
            ];
        }

        return response()->json([
            'status'        => '1', // Because observation is unsatisfactory
            'questions'     => $questionsList, // With id + text
            'repeat_issue'  => $repeatIssue   // Null if not repeat
        ]);
    }


    public function saveUnsatIssueResponse(Request $request)
    {
        // Authorization Header Check


        // Validate main fields
        $request->validate([
            'audit_id' => 'required|integer',
            'qm_sheet_id' => 'required|integer',
            'issues' => 'required|array',
            'issues.*.parameter_id' => 'required|integer',
            'issues.*.sub_parameter_id' => 'required|integer',
            'issues.*.question_id' => 'required|string', // comma-separated
            'issues.*.remark' => 'nullable|string',
        ]);

        try {
            $responses = [];

            foreach ($request->input('issues') as $issue) {
                // Parse the question_id into a clean, comma-separated string of integers
                $questionIds = array_filter(array_map('trim', explode(',', $issue['question_id'])));
                foreach ($questionIds as $qid) {
                    if (!is_numeric($qid)) {
                        return response()->json([
                            'status' => 0,
                            'message' => "Invalid question_id: {$qid}. Must be numeric.",
                            'data' => []
                        ], 400);
                    }
                }

                // Save to DB
                $response = new AdditionalResponse();
                $response->audit_id = $request->input('audit_id');
                $response->parameter_id = $issue['parameter_id'];
                $response->sub_parameter_id = $issue['sub_parameter_id'];
                $response->questions_id = implode(',', $questionIds); // cleaned up string
                // $response->questions = $request->input('questions');
                $response->remark = $issue['remark'] ?? null;
                $response->save();

                $responses[] = $response;
            }

            return response()->json([
                'status' => 1,
                'message' => 'All responses saved successfully.',

            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Failed to save responses. ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }


    // function to send the audit result using api in postman 
    public function sendAuditResultEmailApi(Request $request)
    {
        try {
            $audit_id = $request->input('audit_id');
            $client_id = $request->input('client_id');

            if (!$audit_id || !$client_id) {
                return response()->json([
                    'status' => false,
                    'message' => 'audit_id and client_id are required'
                ], 400);
            }

            $result = $this->sendAuditResultEmail($audit_id, $client_id);

            if ($result) {
                return response()->json([
                    'status' => true,
                    'message' => 'Email sent successfully'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to send email'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
