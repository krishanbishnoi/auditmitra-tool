<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use Illuminate\Support\Facades\Crypt;
use App\RedAlert;
use App\RedAlertAnswer;
use App\Yard;
use App\Agency;
use App\User;
use App\Model\Branchable;
use App\Model\Branch;
use Storage;
use Mail;
use App\Audit;
use App\AuditResult;
use App\BranchRepo;
use App\AgencyRepo;
class RedAlertController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data=RedAlert::with('sheet:id,name','parameter:id,parameter','subparameter:id,sub_parameter','audit.qa_qtl_detail:id,name')->get();
        // dd($data);
        return view('RedAlert.list',compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $ids=[];
        $insert=[];
        $idData=json_decode($request->ids);
        $data=[];
        // dd($ids);
        foreach($idData as $k=>$value){
            $name='file'.$value;
            if($request->has($name) && $request->file($name)!='undefined'){
                // $store=Storage::disk('public_uploads')->put('test', $request->file);
                $path1 = $request->file($name)->store('public'); 
                $store=storage_path('app').'/'.$path1;
            }
            else{
                $store=null;
            }
            $insert=[
                'sheet_id'=>$request['sheet_id'.$value],
                'parameter_id'=>$request['parameter_id'.$value],
                'sub_parameter_id'=>$request['id'.$value],
                'message'=>$request['msg'.$value],
                'file'=>$store,
                'type'=>$request->type,
                'type_id'=>$request->typeid,
                'lob'=>$request['lob'.$value],
                'audit_id'=>$request->audit_id
            ];
            // dd($insert);
            $data[]=RedAlert::create($insert);
        }
        
        if(count($data)>0){
           
            return response()->json(['status'=>true,'msg'=>'red alert save']);
        }
        else{
            return response()->json(['status'=>true,'msg'=>'red alert not save']);
        }
    }

    public function downloadFile($id){
        $data=RedAlert::find(Crypt::decrypt($id));
        // dd($data);
        // return Storage::download($data->file);
        return Response::download($data->file);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($audit_id)
    {
        //
        $audit=Audit::with(['qmsheet','redAlert.parameter','redAlert.subParameter','product'])->where('id',crypt::decrypt($audit_id))->first();
        return view('RedAlert.show',compact('audit'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($audit_id)
    {
        //
        $audit=Audit::with(['qmsheet','redAlert.answer','redAlert.parameter','redAlert.subParameter','product'])->where('id',crypt::decrypt($audit_id))->first();
        // dd($audit);
        return view('RedAlert.view',compact('audit'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $audit_id)
    {
        //
        $audit=Audit::with(['redAlert.parameter','redAlert.subParameter'])->where('id',crypt::decrypt($audit_id))->first();
        foreach($audit->redAlert as $item){
            RedAlertAnswer::create(['red_alert_id'=>$item->id,'answer'=>$request['answer'.$item->id]]);
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
    public function test(){
        $audit_id=17;
        $otherDetails=[];
        $audit=Audit::with(['qmsheet','redAlert.parameter','redAlert.subParameter','product'])->where('id',$audit_id)->first();
        $auditResult=AuditResult::where('audit_id',$audit_id)->get()->pluck('remark','sub_parameter_id');
        // dd($auditResult);
            switch($audit->qmsheet->type){
                case 'branch':
                    $branch=Branch::with('city.state.region')->find($audit->branch_id);
                    $otherDetails['region']=$branch->city->state->region->name ?? '';
                    $otherDetails['state']=$branch->city->state->name ?? '';
                    $otherDetails['city']=$branch->city->name ?? '';
                    $otherDetails['name']=$branch->name ?? '';
                   $ids= Branchable::where('branch_id',$audit->branch_id)->get(['id','manager_id'])->pluck('manager_id');
                break;
                case 'agency':
                    $agency=Agency::find($audit->agency_id);
                    $branch=Branch::with('city.state.region')->find($agency->branch_id);
                    $otherDetails['region']=$branch->city->state->region->name ?? '';
                    $otherDetails['state']=$branch->city->state->name ?? '';
                    $otherDetails['city']=$branch->city->name ?? '';
                    $otherDetails['name']=$agency->name ?? '';
                    $ids= Branchable::where('branch_id',$agency->branch_id)->get(['id','manager_id'])->pluck('manager_id');
                break;
                case 'yard':
                    $agency=Yard::find($audit->yard_id);
                    $branch=Branch::with('city.state.region')->find($agency->branch_id);
                    $otherDetails['region']=$branch->city->state->region->name ?? '';
                    $otherDetails['state']=$branch->city->state->name ?? '';
                    $otherDetails['city']=$branch->city->name ?? '';
                    $otherDetails['name']=$agency->name ?? '';
                    $ids= Branchable::where('branch_id',$agency->branch_id)->get(['id','manager_id'])->pluck('manager_id');
                break;
                case 'branch_repo':
                    $BranchRepo=BranchRepo::find($audit->branch_repo_id);
                    $branch=Branch::with('city.state.region')->find($BranchRepo->branch_id);
                    $otherDetails['region']=$branch->city->state->region->name ?? '';
                    $otherDetails['state']=$branch->city->state->name ?? '';
                    $otherDetails['city']=$branch->city->name ?? '';
                    $otherDetails['name']=$BranchRepo->name ?? '';
                    $ids= Branchable::where('branch_id',$BranchRepo->branch_id)->get(['id','manager_id'])->pluck('manager_id');
                break;
                case 'agency_repo':
                    $AgencyRepo=AgencyRepo::find($audit->agency_repo_id);
                    $branch=Branch::with('city.state.region')->find($AgencyRepo->branch_id);
                    $otherDetails['region']=$branch->city->state->region->name ?? '';
                    $otherDetails['state']=$branch->city->state->name ?? '';
                    $otherDetails['city']=$branch->city->name ?? '';
                    $otherDetails['name']=$AgencyRepo->name ?? '';
                    $ids= Branchable::where('branch_id',$AgencyRepo->branch_id)->get(['id','manager_id'])->pluck('manager_id');
                break;
            }
            $attach=[];
            foreach($audit->redAlert as $item){
                if($item->file!=null){
                    $attach[]=$item->file;
                }
            }
            // $emails=User::whereIn('id',$ids)->role('Area Collection Manager')->get(['id','email'])->pluck('email')->toArray();
            $otherDetails['collection']=User::whereIn('id',$ids)->role('Collection Manager')->first(['id','name'])->name;
            $emails[]='ravindra.swami9@gmail.com';
            $url=url('red-alert/').'/'.Crypt::encrypt($audit->id);
            Mail::send('emails.alert', ['data' => $audit,'otherDetails'=>$otherDetails,'auditResult'=>$auditResult,'url'=>$url], function ($m) use ($emails,$attach) {
                //$m->from('hello@app.com', 'Your Application');
                $m->to($emails)->subject('Alert');
                foreach($attach as $item){
                    $m->attach($item);
                }
            });
            // dd($audit);
            // $data=$audit;
            // return view('emails.alert',compact('data','otherDetails','auditResult'));
    }
}
