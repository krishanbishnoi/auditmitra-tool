<?php

namespace App\Http\Controllers;

use App\AuditAlertBox;
use App\QmSheet;
use App\Category;
use App\Parameter;
use App\SubParameter;
use App\QmSheetParameter;
use App\QmSheetSubParameter;
// use App\ReasonType;
use Auth;
use Crypt;
use Illuminate\Http\Request;
use Validator;
use App\User;
use App\Helpers\Helper;
use App\MappingMaster;
use DB;

class QmSheetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::user()->hasRole('Super Admin')) {
            // Super Admin: show all sheets
            $data = QmSheet::with('parameter', 'user')->get();
        } else {
            // Other users: show sheets where client_id = current user id
            $data = QmSheet::with('parameter', 'user')
                ->where('client_id', Auth::id())
                ->get();
        }

        $users = User::where('active_status', 0)->where('client_id', auth()->user()->id)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'Quality Auditor');
            })
            ->get();

        return view('qm_sheet.list', compact('data', 'users'));
    }

    public function getAssignedUsers($id)
    {
        $users = DB::table('qm_sheet_assignments')
            ->where('qm_sheet_id', $id)
            ->pluck('user_id');

        return response()->json($users);
    }

    public function assignQmSheet(Request $request)
    {
        $newUsers = $request->users ?? [];

        // Existing assigned users
        $existingUsers = DB::table('qm_sheet_assignments')
            ->where('qm_sheet_id', $request->qm_sheet_id)
            ->pluck('user_id')
            ->toArray();

        // Users to add
        $usersToAdd = array_diff($newUsers, $existingUsers);

        // Users to remove
        $usersToRemove = array_diff($existingUsers, $newUsers);

        // Insert newly assigned users
        foreach ($usersToAdd as $userId) {

            DB::table('qm_sheet_assignments')->insert([
                'client_id'   => Auth::user()->client_id,
                'qm_sheet_id' => $request->qm_sheet_id,
                'user_id'     => $userId,
                'assigned_by' => Auth::id(),
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        // Remove unassigned users
        if (!empty($usersToRemove)) {
            DB::table('qm_sheet_assignments')
                ->where('qm_sheet_id', $request->qm_sheet_id)
                ->whereIn('user_id', $usersToRemove)
                ->delete();
        }

        return response()->json([
            'status' => true,
            'message' => 'Checksheet assignment updated successfully.'
        ]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $clients = User::role('Client')->pluck('name', 'id');

        return view('qm_sheet.create', compact('clients'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'type' => 'required',
            'lob' => 'required',
            // 'client_id' => 'required_if:super_admin,true', // optional strict validation
        ]);

        if ($validator->fails()) {
            return redirect('qm_sheet/create')
                ->withErrors($validator)
                ->withInput();
        } else {
            $new_rc = new QmSheet;
            $new_rc->fill($request->all());

            // Determine client_id based on role
            if (Auth::user()->hasRole('Super Admin')) {
                $new_rc->client_id = $request->client_id;
            } else {
                $new_rc->client_id = Auth::id();
            }

            $new_rc->save();

            return redirect('qm_sheet')
                ->with('success', 'QM Sheet created successfully, now please add all parameters and sub-parameters.');
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = QmSheet::find(Crypt::decrypt($id));
        $clients = User::role('Client')->pluck('name', 'id');
        // $all_client = Client::where('company_id',Auth::user()->company_id)->pluck('name','id');
        // $all_process = Process::where('company_id',Auth::user()->company_id)->pluck('name','id');
        // return view('qm_sheet.edit',compact('data','all_client','all_process'));
        return view('qm_sheet.edit', compact('data', 'clients'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            // 'company_id' => 'required',
            // 'client_id' => 'required',
            // 'process_id' => 'required',
            'name' => 'required',
            'type' => 'required',
            'lob' => 'required',
            // 'code' => 'required',
            // 'version' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return redirect('qm_sheet/create')
                ->withErrors($validator)
                ->withInput();
        } else {
            $new_rc =  QmSheet::find(Crypt::decrypt($id));
            $new_rc->fill($request->all());
            $new_rc->save();
            return redirect('qm_sheet')->with('success', 'QM - Sheet updated successfully.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $new_rc =  QmSheet::where('id', Crypt::decrypt($id))->delete();
        if ($new_rc)
            return redirect('qm_sheet')->with('success', 'QM - Sheet deleted successfully.');
        else
            return redirect('qm_sheet')->with('error', 'QM - Sheet not deleted.');
    }


    public function activeStatus($id)
    {
        $sheet = QmSheet::findOrFail($id);

        // Toggle the SAME column
        $sheet->is_active = $sheet->is_active == 1 ? 0 : 1;
        $sheet->save();

        return back()->with('success', 'Status updated successfully.');
    }

    public function add_parameter($sheet_id)
    {
        // Initialize variables
        $locations = collect();
        $campusTypes = collect();
        $brands = collect();
        $pillar = collect();
        $touch_points = collect();
        $compliance_experience = collect();

        // Check client ID
        if (in_array(27, Helper::allocatedmodulelist())) {
            $clientId = auth()->user()->client_id;
            // Locations
            $locations = MappingMaster::where('client_id', $clientId)
                ->whereNotNull('location')
                ->where('location', '!=', '')
                ->distinct()
                ->pluck('location');

            // Campus Types
            $campusTypes = MappingMaster::where('client_id', $clientId)
                ->whereNotNull('campus_type')
                ->where('campus_type', '!=', '')
                ->distinct()
                ->pluck('campus_type');

            // Brands
            $brands = MappingMaster::where('client_id', $clientId)
                ->whereNotNull('brand')
                ->where('brand', '!=', '')
                ->distinct()
                ->pluck('brand');

            // Pillars
            $pillar = MappingMaster::where('client_id', $clientId)
                ->whereNotNull('pillar')
                ->where('pillar', '!=', '')
                ->distinct()
                ->pluck('pillar');

            // Touch Points
            $touch_points = MappingMaster::where('client_id', $clientId)
                ->whereNotNull('touch_points')
                ->where('touch_points', '!=', '')
                ->distinct()
                ->pluck('touch_points');

            // Compliance / Experience
            $compliance_experience = MappingMaster::where('client_id', $clientId)
                ->whereNotNull('compliance_experience')
                ->where('compliance_experience', '!=', '')
                ->distinct()
                ->pluck('compliance_experience');
        }

        $category = Category::where('client_id', auth()->user()->client_id)->get();

        $parameters = Parameter::where('client_id', auth()->user()->client_id)->get();

        $qm_sheet_data = QmSheet::findOrFail(Crypt::decrypt($sheet_id));

        $all_alert_box_list = AuditAlertBox::pluck('name', 'id');

        $all_reason_types = [];

        $subParameters = SubParameter::all();

        return view(
            'qm_sheet.add_parameter',
            compact(
                'qm_sheet_data',
                'all_alert_box_list',
                'all_reason_types',
                'parameters',
                'subParameters',
                'category',
                'locations',
                'campusTypes',
                'brands',
                'pillar',
                'touch_points',
                'compliance_experience'
            )
        );
    }

    public function list_parameter($sheet_id)
    {
        $qm_sheet_data = QmSheet::find(Crypt::decrypt($sheet_id));
        $data = QmSheetParameter::where('qm_sheet_id', $qm_sheet_data->id)->with('qm_sheet_sub_parameter')->get();

        // dd($data);
        return view('qm_sheet.list_parameter', compact('data', 'qm_sheet_data'));
    }

    public function store_parameters(Request $request)
    {
        // dd($request->all());
        // dd($request->subs);
        // return "a";
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            // 'company_id' => 'required',
            'qm_sheet_id' => 'required',
            'parameter_id' => 'required'
        ]);
        $parameter = Parameter::find($request->parameter_id);
        // dd($parameter);
        if ($validator->fails()) {
            return redirect('qm_sheet/' . Crypt::encrypt($request->qm_sheet_id) . '/add_parameter')
                ->withErrors($validator)
                ->withInput();
        } else {
            // dd($request->all());
            $new_rc = new QmSheetParameter;
            $new_rc->qm_sheet_id = $request->qm_sheet_id;
            $new_rc->parameter = $parameter->name;
            $new_rc->is_non_scoring = (isset($request->non_scoring)) ? 1 : 0;
            $new_rc->parent_parameter_id = $request->parameter_id;
            $new_rc->client_id = auth()->user()->client_id;

            if (in_array(26, Helper::allocatedmodulelist())) {
                $category = DB::table('categories')->where('id', $request->category_id)->first();
                $new_rc->category_id = $request->category_id;
                $new_rc->category = $category->category;
                $new_rc->category_weight = $category->weight;
            }

            if (isset($request->locations)) {
                $new_rc->location = $request->locations;
            }

            if (isset($request->campusTypes)) {
                $new_rc->campus_type = $request->campusTypes;
            }

            $new_rc->save();

            if ($new_rc->id) {
                foreach ($request->subs as $key => $value) {
                    if ($value['sub_parameter_id']) {
                        $sub_parameter = SubParameter::find($value['sub_parameter_id']);

                        $new_sub_rc = new QmSheetSubParameter;
                        $new_sub_rc->qm_sheet_id = $request->qm_sheet_id;
                        $new_sub_rc->qm_sheet_parameter_id = $new_rc->id;

                        $new_sub_rc->sub_parameter = $sub_parameter->name;
                        $new_sub_rc->weight = $value['weight'];
                        $new_sub_rc->details = $value['details'];
                        $new_sub_rc->parent_parameter_id = $request->parameter_id;;
                        $new_sub_rc->parent_sub_Parameter_id = $value['sub_parameter_id'];

                        if (isset($request->non_scoring)) {
                            $new_sub_rc->non_scoring_option_group = $value['non_scoring_option_group'];
                        } else {

                            $new_sub_rc->pass = (isset($value['s_pass'])) ? 1 : 0;
                            $new_sub_rc->fail = (isset($value['s_fail'])) ? 1 : 0;
                            $new_sub_rc->critical = (isset($value['s_critical'])) ? 1 : 0;
                            $new_sub_rc->na = (isset($value['s_na'])) ? 1 : 0;
                            $new_sub_rc->pwd = (isset($value['s_pwd'])) ? 1 : 0;
                            $new_sub_rc->per = (isset($value['s_per'])) ? 1 : 0;
                            $new_sub_rc->collection_manager = (isset($value['s_collection'])) ? 1 : 0;
                            $new_sub_rc->use_error_count = (isset($value['error_scoring'])) ? 1 : 0;
                            $new_sub_rc->error_scoring = isset($value['error_scoring_json']) ? $value['error_scoring_json'] : null;
                            $new_sub_rc->is_regulatory_param = (isset($value['s_regulatory'])) ? 1 : 0;
                            $new_sub_rc->Severity = isset($value['risk_level']) ? $value['risk_level'] : null;
                            if (in_array(24, Helper::allocatedmodulelist())) {
                                $new_sub_rc->parameter_type = $value['parameter_type'];
                            }
                            if (isset($value['compliance_experience'])) {
                                $new_sub_rc->compliance_experience = $value['compliance_experience'];
                            }

                            if (isset($value['pillar'])) {
                                $new_sub_rc->pillar = $value['pillar'];
                            }

                            if (isset($value['touchpoint'])) {
                                $new_sub_rc->touch_point = $value['touchpoint'];
                            }
                            // $new_sub_rc->pass_alert_box_id = $value['s_pass_alert_box_id'];
                            // $new_sub_rc->fail_alert_box_id = $value['s_fail_alert_box_id'];
                            // $new_sub_rc->critical_alert_box_id = $value['s_critical_alert_box_id'];
                            // $new_sub_rc->na_alert_box_id = $value['s_na_alert_box_id'];
                            // $new_sub_rc->pwd_alert_box_id = $value['s_pwd_alert_box_id'];

                            if (isset($value['s_fail_reason_type_box_id']))
                                $new_sub_rc->fail_reason_types = implode(",", $value['s_fail_reason_type_box_id']);

                            if (isset($value['s_critical_reason_type_box_id']))
                                $new_sub_rc->critical_reason_types = implode(",", $value['s_critical_reason_type_box_id']);
                        }

                        $new_sub_rc->save();
                    }
                }
            }
            return redirect('qm_sheet/' . Crypt::encrypt($request->qm_sheet_id) . '/add_parameter')->with('success', 'QM - Sheet parameter created successfully.');
        }
    }
    public function delete_parameter($id)
    {
        $temp = QmSheetParameter::with('qm_sheet_sub_parameter')->find(Crypt::decrypt($id));
        QmSheetSubParameter::where('qm_sheet_parameter_id', $temp->id)->delete();
        $sheet_id = $temp->qm_sheet_id;
        $temp->delete();
        return redirect('qm_sheet/' . Crypt::encrypt($sheet_id) . '/list_parameter')->with('success', 'QM - Sheet parameter deleted successfully.');
    }
    public function edit_parameter($parameter_id)
    {
        // Initialize variables
        $locations = collect();
        $campusTypes = collect();
        $brands = collect();
        $pillar = collect();
        $touch_points = collect();
        $compliance_experience = collect();

        // Check client ID
        if (in_array(27, Helper::allocatedmodulelist())) {
            $clientId = auth()->user()->client_id;
            // Locations
            $locations = MappingMaster::where('client_id', $clientId)
                ->whereNotNull('location')
                ->where('location', '!=', '')
                ->distinct()
                ->pluck('location');

            // Campus Types
            $campusTypes = MappingMaster::where('client_id', $clientId)
                ->whereNotNull('campus_type')
                ->where('campus_type', '!=', '')
                ->distinct()
                ->pluck('campus_type');

            // Brands
            $brands = MappingMaster::where('client_id', $clientId)
                ->whereNotNull('brand')
                ->where('brand', '!=', '')
                ->distinct()
                ->pluck('brand');

            // Pillars
            $pillar = MappingMaster::where('client_id', $clientId)
                ->whereNotNull('pillar')
                ->where('pillar', '!=', '')
                ->distinct()
                ->pluck('pillar');

            // Touch Points
            $touch_points = MappingMaster::where('client_id', $clientId)
                ->whereNotNull('touch_points')
                ->where('touch_points', '!=', '')
                ->distinct()
                ->pluck('touch_points');

            // Compliance / Experience
            $compliance_experience = MappingMaster::where('client_id', $clientId)
                ->whereNotNull('compliance_experience')
                ->where('compliance_experience', '!=', '')
                ->distinct()
                ->pluck('compliance_experience');
        }
        $category = Category::where('client_id', auth()->user()->client_id)->get();
        $param_data = QmSheetParameter::find(Crypt::decrypt($parameter_id));

        $qm_sheet_data = QmSheet::find($param_data->qm_sheet_id);
        $all_alert_box_list = AuditAlertBox::all()->pluck('name', 'id');
        // $all_reason_types = ReasonType::where('company_id',Auth::user()->company_id)->pluck('label','id');
        $all_reason_types = [];
        $parameters = Parameter::all();
        $sub_parameters = SubParameter::all()->groupBy('parameter_id');

        return view('qm_sheet.edit_parameter', compact(
            'qm_sheet_data',
            'all_alert_box_list',
            'all_reason_types',
            'param_data',
            'parameters',
            'sub_parameters',
            'category',
            'locations',
            'campusTypes',
            'brands',
            'pillar',
            'touch_points',
            'compliance_experience'
        ));
    }
    public function update_parameter(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            // 'company_id' => 'required',
            'qm_sheet_id' => 'required',
            'parameter' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect('parameter/' . Crypt::encrypt($request->parameter_id) . '/edit')
                ->withErrors($validator)
                ->withInput();
        } else {
            $new_rc = QmSheetParameter::find($request->parameter_id);
            $new_rc->qm_sheet_id = $request->qm_sheet_id;
            $new_rc->parameter = $request->parameter;
            $new_rc->is_non_scoring = (isset($request->non_scoring)) ? 1 : 0;
            if (in_array(26, Helper::allocatedmodulelist())) {
                $category = DB::table('categories')->where('id', $request->category_id)->first();
                $new_rc->category_id = $request->category_id;
                $new_rc->category = $category->category;
                $new_rc->category_weight = $category->weight;
            }
            $new_rc->save();

            if ($new_rc->id) {
                foreach ($request->subs as $key => $value) {
                    if ($value['sub_parameter']) {

                        if ($value['sp_pm_id'])
                            $new_sub_rc = QmSheetSubParameter::find($value['sp_pm_id']);
                        else
                            $new_sub_rc = new QmSheetSubParameter;


                        $new_sub_rc->qm_sheet_id = $request->qm_sheet_id;
                        $new_sub_rc->qm_sheet_parameter_id = $new_rc->id;

                        $new_sub_rc->sub_parameter = $value['sub_parameter'];
                        $new_sub_rc->weight = $value['weight'];
                        $new_sub_rc->details = $value['details'];

                        if (isset($request->non_scoring)) {
                            $new_sub_rc->non_scoring_option_group = $value['non_scoring_option_group'];

                            $new_sub_rc->pass = 0;
                            $new_sub_rc->fail = 0;
                            $new_sub_rc->critical = 0;
                            $new_sub_rc->na = 0;
                            $new_sub_rc->pwd = 0;
                            $new_sub_rc->per = 0;
                            $new_sub_rc->pass_alert_box_id = null;
                            $new_sub_rc->fail_alert_box_id = null;
                            $new_sub_rc->critical_alert_box_id = null;
                            $new_sub_rc->na_alert_box_id = null;
                            $new_sub_rc->pwd_alert_box_id = null;

                            // $new_sub_rc->fail_reason_types = null;

                            // $new_sub_rc->critical_reason_types = null;

                        } else {

                            $new_sub_rc->pass = (isset($value['s_pass'])) ? 1 : 0;
                            $new_sub_rc->fail = (isset($value['s_fail'])) ? 1 : 0;
                            $new_sub_rc->critical = (isset($value['s_critical'])) ? 1 : 0;
                            $new_sub_rc->na = (isset($value['s_na'])) ? 1 : 0;
                            $new_sub_rc->pwd = (isset($value['s_pwd'])) ? 1 : 0;
                            $new_sub_rc->per = (isset($value['s_per'])) ? 1 : 0;
                            $new_sub_rc->collection_manager = (isset($value['s_collection'])) ? 1 : 0;
                            $new_sub_rc->is_regulatory_param = (isset($value['s_regulatory'])) ? 1 : 0;
                            $new_sub_rc->use_error_count = (isset($value['error_scoring'])) ? 1 : 0;
                            $new_sub_rc->error_scoring = isset($value['error_scoring_json']) ? $value['error_scoring_json'] : null;
                            $new_sub_rc->Severity = isset($value['risk_level']) ? $value['risk_level'] : null;

                            if (in_array(24, Helper::allocatedmodulelist())) {
                                $new_sub_rc->parameter_type = $value['parameter_type'];
                            }
                            if (isset($value['compliance_experience'])) {
                                $new_sub_rc->compliance_experience = $value['compliance_experience'];
                            }

                            if (isset($value['pillar'])) {
                                $new_sub_rc->pillar = $value['pillar'];
                            }

                            if (isset($value['touchpoint'])) {
                                $new_sub_rc->touch_point = $value['touchpoint'];
                            }
                            // $new_sub_rc->pass_alert_box_id = $value['s_pass_alert_box_id'];
                            // $new_sub_rc->fail_alert_box_id = $value['s_fail_alert_box_id'];
                            // $new_sub_rc->critical_alert_box_id = $value['s_critical_alert_box_id'];
                            // $new_sub_rc->na_alert_box_id = $value['s_na_alert_box_id'];
                            // $new_sub_rc->pwd_alert_box_id = $value['s_pwd_alert_box_id'];

                            // if(isset($value['s_fail_reason_type_box_id']))
                            //     $new_sub_rc->fail_reason_types = implode(",", $value['s_fail_reason_type_box_id']);
                            // else
                            //     $new_sub_rc->fail_reason_types = null;

                            // if(isset($value['s_critical_reason_type_box_id']))
                            //     $new_sub_rc->critical_reason_types = implode(",", $value['s_critical_reason_type_box_id']);
                            // else
                            //     $new_sub_rc->critical_reason_types = null;

                        }

                        $new_sub_rc->save();
                    }
                }
            }
            return redirect('qm_sheet/' . Crypt::encrypt($request->qm_sheet_id) . '/list_parameter')->with('success', 'Parameter and sub parameter updated successfully.');
        }
    }
    public function delete_sub_parameter($id)
    {
        QmSheetSubParameter::find($id)->delete();
        return response()->json(['status' => 200, 'message' => "Success, sub parameter deleted successfully."], 200);
    }
    public function get_client_process_based_qm_sheet(Request $request)
    {
        $all_sheet = QmSheet::where('client_id', $request->client_id)->where('process_id', $request->process_id)->orderBy('id', 'desc')->get();
        return response()->json(['status' => 200, 'message' => "Success", 'data' => $all_sheet], 200);
    }
}
