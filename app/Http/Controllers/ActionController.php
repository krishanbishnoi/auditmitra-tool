<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Qc;
use App\ActionPlan;
use App\ActionPlanSub;
use App\ActionPlanAnswer;
use Crypt;
use Carbon\Carbon;
use App\Audit;
use App\Agency;
use App\Model\Branchable;
use App\Model\Branch;
use App\Yard;
use App\User;
use App\SavedQcAudit;
use Mail;
use Response;
use Storage;
use Auth;

class ActionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        ini_set('memory_limit', '-1');
        $savedQcIds = SavedQcAudit::all()->pluck('audit_id')->toArray();
        $data = Qc::with('audit.audit_results.sub_parameter_detail', 'user:id,name')->whereNotIn('audit_id', $savedQcIds)->get();
        // dd($data->first());
        return view('Action.list', compact('data'));
    }
    public function list()
    {
        ini_set('memory_limit', '-1');
        $ids = ActionPlanAnswer::all(['action_id'])->pluck('action_id')->toArray();
        $data = ActionPlan::with('answers.sub', 'audit')->whereIn('id', $ids)->get();
        // dd($data);
        return view('Action.answer', compact('data'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        //$id is audit id.
        $did = Crypt::decrypt($id);
        // added by nisha 

        $branch = Branch::get(['id', 'name']);

        return view('Action.create', compact('did', 'branch'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        // dd($request->all());
        $i = 1;
        //sheet_id is Audited id
        $action = ActionPlan::create(['date' => Carbon::now(), 'sheet_id' => $request->sheet_id, 'send_to' => 'Collection_manager']);
        if ($request->has('artifact')) {
            if (in_array($request->file('artifact')->getClientOriginalExtension(), ['xls', 'xlsx', 'XLS', 'XLSX'])) {
                $path = Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix();
                move_uploaded_file($_FILES['artifact']["tmp_name"], $path . Carbon::now()->timestamp . '.' . $request->file('artifact')->getClientOriginalExtension());
                $store = $path . Carbon::now()->timestamp . '.' . $request->file('artifact')->getClientOriginalExtension();
            } else {
                $path1 = $request->file('artifact')->store('public');
                $store = storage_path('app') . '/' . $path1;
            }
        } else {
            $store = null;
        }

        //$user_role = Auth::user()->roles()->first()->name;
        $data[] = ['question' => $request->question, 'artifact' => $store, 'action_id' => $action->id, 'branch_id' => $request->branch_id, 'created_by' => Auth::user()->id];
        //dd($data);
        while (isset($request['question' . $i]) || isset($request['artifact' . $i])) {
            if ($request->has('artifact' . $i)) {
                if (in_array($request->file('artifact' . $i)->getClientOriginalExtension(), ['xls', 'xlsx', 'XLS', 'XLSX'])) {
                    $path = Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix();
                    move_uploaded_file($_FILES['artifact' . $i]["tmp_name"], $path . Carbon::now()->timestamp . '.' . $request->file('artifact' . $i)->getClientOriginalExtension());
                    $store = $path . Carbon::now()->timestamp . '.' . $request->file('artifact' . $i)->getClientOriginalExtension();
                } else {
                    $path1 = $request->file('artifact' . $i)->store('public');
                    $store = storage_path('app') . '/' . $path1;
                }
            } else {
                $store = null;
            }
            $data[] = ['question' => $request['question' . $i], 'artifact' => $store, 'action_id' => $action->id, 'branch_id' => $request['branch_id' . $i], 'created_by' => Auth::user()->id];
            $i++;
        }
        // dd($data);
        $check = ActionPlanSub::insert($data);
        if ($check) {
            $audit = Audit::find($request->sheet_id);
            $url = url('action/') . '/' . Crypt::encrypt($action->id);
            if ($audit->branch_id != null) {
                $ids = Branchable::where(['branch_id' => $audit->branch_id, 'product_id' => $audit->product_id])->get(['id', 'manager_id'])->pluck('manager_id');
            } else if ($audit->agency_id != null) {
                $agency = Agency::find($audit->agency_id);
                $ids = Branchable::where(['branch_id' => $agency->branch_id, 'product_id' => $audit->product_id])->get(['id', 'manager_id'])->pluck('manager_id');
            } else {
                $agency = Yard::find($audit->agency_id);
                $ids = Branchable::where(['branch_id' => $agency->branch_id, 'product_id' => $audit->product_id])->get(['id', 'manager_id'])->pluck('manager_id');
            }
            $emails = User::whereIn('id', $ids)->role('Collection Manager')->get(['id', 'email'])->pluck('email')->toArray();
            //    $emails=User::whereIn('id',$ids)->get(['id','email'])->pluck('email')->toArray();
            /*Mail::send('emails.action', ['data' => $audit,'url'=>$url], function ($m) use ($emails) {
                //$m->from('hello@app.com', 'Your Application');
                $m->to($emails)->subject('Action Plan');
            });*/
            // dd($emails,$url);
            return redirect('action')->with('success', 'Action Plan created successfully');
        }
        return redirect('action')->with('danger', 'Action Plan creation Unsuccessfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $action = ActionPlan::with('sub', 'audit.qmsheet', 'audit.qa_qtl_detail', 'audit.branch.branchable', 'audit.agency.branch.branchable', 'audit.yard.branch.branchable', 'audit.branchRepo.branch.branchable', 'audit.agencyRepo.branch.branchable')->find(Crypt::decrypt($id));
        $collection = [];
        $name = "";
        switch ($action->audit->qmsheet->type) {
            case 'branch':
                foreach ($action->audit->branch->branchable as $k => $e) {
                    if ($e->type == 'Collection_Manager' && $e->product_id == $action->audit->product_id) {
                        $collection = $e;
                    }
                }
                $name = $action->audit->branch->name;
                break;
            case 'agency':
                foreach ($action->audit->agency->branch->branchable as $k => $e) {
                    if ($e->type == 'Collection_Manager' && $e->product_id == $action->audit->product_id) {
                        $collection = $e;
                    }
                }
                $name = $action->audit->agency->name;
                break;
            case 'Yard':
                foreach ($action->audit->yard->branch->branchable as $k => $e) {
                    if ($e->type == 'Collection_Manager' && $e->product_id == $action->audit->product_id) {
                        $collection = $e;
                    }
                }
                $name = $action->audit->yard->name;
                break;
            case 'branch_repo':
                foreach ($action->audit->branchRepo->branch->branchable as $k => $e) {
                    if ($e->type == 'Collection_Manager' && $e->product_id == $action->audit->product_id) {
                        $collection = $e;
                    }
                }
                $name = $action->audit->branchRepo->name;
                break;
            case 'agency_repo':
                foreach ($action->audit->agencyRepo->branch->branchable as $k => $e) {
                    if ($e->type == 'Collection_Manager' && $e->product_id == $action->audit->product_id) {
                        $collection = $e;
                    }
                }
                $name = $action->audit->agencyRepo->name;
                break;
        }
        // dd($action,$collection);
        return view('Action.show', compact('action', 'collection', 'name'));
    }
    public function view($id)
    {
        $action = ActionPlan::with('sub.actionPlanAnswer', 'audit.qmsheet', 'audit.qa_qtl_detail', 'audit.branch.branchable', 'audit.agency.branch.branchable', 'audit.yard.branch.branchable', 'audit.branchRepo.branch.branchable', 'audit.agencyRepo.branch.branchable')->find(Crypt::decrypt($id));
        // $action=ActionPlan::with('sub.actionPlanAnswer','audit.qmsheet','audit.qa_qtl_detail','audit.branch.branchable','audit.agency.branch.branchable','audit.yard.branch.branchable','audit.branchRepo.branch.branchable','audit.agencyRepo.branch.branchable')->find($id);
        $collection = [];
        $name = "";
        switch ($action->audit->qmsheet->type) {
            case 'branch':
                foreach ($action->audit->branch->branchable as $k => $e) {
                    if ($e->type == 'Collection_Manager' && $e->product_id == $action->audit->product_id) {
                        $collection = $e;
                    }
                }
                $name = $action->audit->branch->name;
                break;
            case 'agency':
                foreach ($action->audit->agency->branch->branchable as $k => $e) {
                    if ($e->type == 'Collection_Manager' && $e->product_id == $action->audit->product_id) {
                        $collection = $e;
                    }
                }
                $name = $action->audit->agency->name;
                break;
            case 'Yard':
                foreach ($action->audit->yard->branch->branchable as $k => $e) {
                    if ($e->type == 'Collection_Manager' && $e->product_id == $action->audit->product_id) {
                        $collection = $e;
                    }
                }
                $name = $action->audit->yard->name;
                break;
            case 'branch_repo':
                foreach ($action->audit->branchRepo->branch->branchable as $k => $e) {
                    if ($e->type == 'Collection_Manager' && $e->product_id == $action->audit->product_id) {
                        $collection = $e;
                    }
                }
                $name = $action->audit->branchRepo->name;
                break;
            case 'agency_repo':
                foreach ($action->audit->agencyRepo->branch->branchable as $k => $e) {
                    if ($e->type == 'Collection_Manager' && $e->product_id == $action->audit->product_id) {
                        $collection = $e;
                    }
                }
                $name = $action->audit->agencyRepo->name;
                break;
        }
        // dd($action,$collection);
        return view('Action.view', compact('action', 'collection', 'name'));
    }
    public function downloadFile($id)
    {
        $data = ActionPlanSub::find(Crypt::decrypt($id));
        // dd($data);
        return Response::download($data->artifact);
        // return Storage::disk('public_uploads')->download($data->artifact);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
        // dd($request->all(), $id);
        $sub = ActionPlanSub::where('action_id', crypt::decrypt($id))->get();
        foreach ($sub as $item) {
            ActionPlanAnswer::create(['action_id' => $item->action_id, 'action_sub_id' => $item->id, 'answer' => $request['answer' . $item->id]]);
        }
        echo 'thank you';
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function sendNextReport()
    {
        $actionIds = ActionPlanAnswer::all()->pluck('action_id')->toArray();
        $action = ActionPlan::whereNotIn('id', $actionIds)->get();
        foreach ($action as $row) {
            $audit = Audit::find($row->sheet_id);
            //    $url=route('action.index').'/'.Crypt::encrypt($row->id);
            $url = 'http://3.12.35.243/audit/action/' . Crypt::encrypt($row->id);
            if ($audit != null && $audit->branch_id != null) {
                $ids = Branchable::where(['branch_id' => $audit->branch_id, 'product_id' => $audit->product_id])->get(['id', 'manager_id'])->pluck('manager_id');
            } else if ($audit != null && $audit->agency_id != null) {
                $agency = Agency::find($audit->agency_id);
                $ids = Branchable::where(['branch_id' => $agency->branch_id, 'product_id' => $audit->product_id])->get(['id', 'manager_id'])->pluck('manager_id');
            } else if ($audit != null) {
                $agency = Yard::find($audit->agency_id);
                $ids = Branchable::where(['branch_id' => $agency->branch_id, 'product_id' => $audit->product_id])->get(['id', 'manager_id'])->pluck('manager_id');
            }

            //    $role=$this->sendto($audit->send_to);
            //    $emails=User::whereIn('id',$ids)->role($role)->get(['id','email'])->pluck('email')->toArray();
            //    $emails=User::whereIn('id',$ids)->get(['id','email'])->pluck('email')->toArray();
            $emails = ['ravindra.swami9@gmail.com'];
            Mail::send('emails.action', ['data' => $audit, 'url' => $url], function ($m) use ($emails) {
                //$m->from('hello@app.com', 'Your Application');
                $m->to($emails)->subject('Action Plan');
            });
            print_r($emails);
        }
    }
    function sendto($type)
    {
        switch ($type) {
            case 'Collection Manager':
                return 'Area Collection Manager';
                break;
            case 'Area Collection Manager':
                return 'Regional Collection Manager';
                break;
            case 'Regional Collection Manager':
                return 'Zonal Collection Manager';
                break;
            case 'Zonal Collection Manager':
                return 'National Collection Manager';
                break;
            case 'National Collection Manager':
                return 'Group Product Head';
                break;
            case 'Group Product Head':
                return 'Group Product Head';
                break;
            default:
                return 'Area Collection Manager';
                break;
        }
    }
}
