<?php

namespace App\Http\Controllers;
ini_set('max_execution_time', '0');
use Illuminate\Http\Request;
use App\Model\Allocation;
use App\QmSheet;
use App\User;
use App\Audit;
use App\SavedAudit;
use App\Qc;
use Validator;
use Crypt;
use Auth;
use App\Exports\AllocationExport;
use Maatwebsite\Excel\Facades\Excel;
class AllocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data=Allocation::with('user','sheet')->get();
        return view('allocation.list',compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $ids=Allocation::all()->pluck('sheet_id');
        // $sheet=QmSheet::whereNotIn('id',$ids)->get();
        $sheet=QmSheet::all();
        $user=User::role('Quality Auditor')->get();
        return view('allocation.create',compact('sheet','user'));
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
            'sheet_id' => 'required',
            'user_id' => 'required',
            ]);

        if($validator->fails())
        {
            return redirect('allocation/create')
                        ->with('error',[$validator->error()->all()])
                        ->withInput();
        }else
        {
            $allocation=Allocation::create(['sheet_id'=>$request->sheet_id,'user_id'=>$request->user_id]);
            if($allocation){
                return redirect('allocation')->with('success', 'QM Sheet Allocation successfully.');
            }
            else{
                return redirect('allocation/create')->with('error', 'QM Sheet Allocation faild.');
            }
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
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $delete=Allocation::find(Crypt::decrypt($id))->delete();
        if($delete){
            return redirect('allocation')->with('success', 'QM Sheet Allocation deleted.');
        }   
        else{
            return redirect('allocation')->with('error', 'QM Sheet Allocation deletation faild.');
        }
    }

    public function getSheets($status = null)
    {
        if ($status == 1) {
            return redirect('auditor_list')->with('success', 'Audit submitted successfully !!');
        }

        $authUserClientId = auth()->user()->client_id;
        // Sirf wahi sheets jinka client_id authenticated user ke client_id se match kare
        $data = QmSheet::where('client_id', $authUserClientId)->where('is_active', 1)->get();

        return view('qa.list', compact('data'));
    }


    public function done_audited_list()
    {
        // Fetch audits with related data
        $user = Auth::user();

        $data = Audit::with([
            'qmsheet',
            'product',
            'branch.city.state',
            'branch.branchable',
            'yard.branch.city.state',
            'agency.branch.city.state',
            'qa_qtl_detail'
        ])
            ->where('status', '<=', 1)
            ->where('client_id', $user->client_id);

        // ➕ Check user role
        if ($user->hasRole('Quality Auditor')) {
            // Example condition for quality auditor only:
            // You may want to restrict audits to only those assigned to this auditor
            $data->where('audited_by_id', $user->id); // replace `assigned_to` with your actual field name
        }

        $data = $data->orderBy('id', 'desc')
            ->limit(500)
            ->get();
    
        // Return the view with data
        return view('audit.audit_list_qa', compact('data'));
    }
    
    
    public function save_audited_list()
    {   
        // Fetch audits with related data
        $user = Auth::user();
        // Fetch audits with related data
        $data = Audit::with([
            'qmsheet',
            'product',
            'branch.city.state',
            'branch.branchable',
            'yard.branch.city.state',
            'agency.branch.city.state',
            'qa_qtl_detail'
        ])
            ->where('status', 5);

        if ($user->hasRole('Quality Auditor')) {
            // Example condition for quality auditor only:
            // You may want to restrict audits to only those assigned to this auditor
            $data->where('audited_by_id', $user->id); // replace `assigned_to` with your actual field name
        }
        elseif($user->hasRole('Admin')) {
            $data->where('audit_agency_id', $user->id);
            }

        $data = $data->orderBy('id', 'desc')
            ->limit(500)
            ->get();
    
        // Return the view with data
        return view('audit.audit_list_qa', compact('data'));
    }
    
    public function excelDownloadAllocation(){
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3000);
        return Excel::download(new AllocationExport, 'Allocation.xlsx');
    }
}
