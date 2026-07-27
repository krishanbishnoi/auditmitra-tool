<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Artifact;
use App\Audit;
use App\AuditParameterResult;
use App\AuditResult;
use App\QmSheet;
use App\Qc;
use App\SavedQcAudit;
use App\User;
use App\Model\Branch;
use App\Model\Branchable;
use App\Model\City;
use Illuminate\Support\Facades\Artisan;
use App\Exports\CityExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Agency;
// use App\Http\Controllers\DashboardController;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }
    function updateArtifact(){
        $auditids=Audit::wherein('id',[10,11])->get();
        $from=date('2020-06-04 17:11:35');
        $to=date('2020-06-04 17:29:20');
        $sheetIds=Artifact::all();
        // foreach($sheetIds as $sheet_id=>$auditId)
        //     {
        //         echo $auditId->id.'=>'.$auditId->audit_id;
        //         echo '<br>';
        //         if($auditId->audit_id==null){
        //             dd($auditId);
        //         }
        //     }
        // $sheetIds=Artifact::whereBetween('created_at', [$from, $to])->get();
        // Artifact::whereIn('id',$sheetIds->pluck('id')->toArray())->update(['audit_id'=>10]);
            dd($auditids,$sheetIds,$sheetIds->pluck('created_at'));
    }
    function runmigration(){
        // $qc=Qc::all();
        // Artisan::call('migrate');
        // \DB::statement("UPDATE `cities` SET `state_id` = '28' WHERE `cities`.`id` = 104;");
        // \DB::statement("INSERT INTO `cities` (`id`, `name`, `state_id`, `created_at`, `updated_at`) VALUES (NULL, 'Chandigarh', '28', NULL, NULL);");
        // $audit=Audit::where('id',25)->update(['collection_manager_id'=>'309']);
        // $audit=Audit::where('id',26)->update(['collection_manager_id'=>'311']);
        // $audit=Audit::where('id',27)->update(['collection_manager_id'=>'316']);
        // $auditids=Audit::with('qa_qtl_detail')->where('id',6)->first();
        // $auditids=Audit::with('qa_qtl_detail')->where('id',7)->update(['audited_by_id'=>203]);
        // $ids=BranchAble::where('branch_id',7)->where('product_id',6)->where('type','Collection_Manager')->get();
        // $data2=\DB::table('agency_repos')->where('branch_id',9)->get();
        // // 203
        // $audit=Audit::where('id',71)->update(['product_id'=>6,
        // 'collection_manager_id'=>364]);
        // $user=User::find($ids->pluck('manager_id')->toArray());
        // $user=User::find(211);
        // $audit=Audit::where('id',18)->first();
        // $audit=Audit::where('id',18)->update(["audited_by_id" => 211]);
        // // $audit=Audit::where('audited_by_id',203)->where('agency_repo_id','!=',null)->get();
        // dd($audit,$data2,$ids,$user);
        // dd($auditids);
        // $result=Audit::with(['audit_parameter_result','audit_results','user'])->where('id',79)->first();
        // $data = QmSheet::with('parameter.qm_sheet_sub_parameter')->find($result->qm_sheet_id);
        // $main=[];
        // foreach($data->parameter as $k=>$item){
        //     foreach($item->qm_sheet_sub_parameter as $key=>$value){
        //         $main[$value->id]=$item->id;
        //     }
        // }
        // foreach($main as $ke=>$val){
        //     AuditResult::where(['audit_id'=>13,'sub_parameter_id'=>$ke])->update(['parameter_id'=>$val]);
        // }471
        // $data=\DB::table('branchables')->where('manager_id',471)->get();
        // $data2=\DB::table('agency_repos')->where('branch_id',9)->get();
        // dd($result,$data,$data2);
        // $ids=BranchAble::all()->pluck('branch_id')->toArray();
        // $brach=Branch::where('id',22)->delete();
        // dd($brach);
        // dd(app('App\Http\Controllers\DashboardController')->getTopCollectionManager());
        // return Excel::download(new CityExport, 'city.xlsx');
        ini_set('memory_limit', '-1');
        // $user=User::where('email','vivek.sharma3@idfcfirstbank.com')->get(['id','name','employee_id']);
        // // $data=[];
        // foreach($user as $item){
        //     if($item->employee_id!=null){
        //         $data=$item;
        //     }
        // }
        // dd($user);
        // $audit=Audit::whereIn('id',[246,247,248,249,250,252,271,272,274,275,276,277,283])->delete();
        // $AuditParameterResult=AuditParameterResult::whereIn('audit_id',[246,247,248,249,250,252,271,272,274,275,276,277,283])->delete();
        // $id=[];
        // for($i=7063;$i<7104;$i++){
        //     $id[]=$i;
        // }
        // dd($id);
        // $AuditResult=AuditResult::whereIn('id',[8000,8001])->delete();
        // $AuditResult=AuditResult::whereIn('audit_id',[318])->get()->pluck('sub_parameter_id','id');
        // dd($AuditResult);
        // $ids=Qc::with('user')->get()->keyBy('audit_id');
        // $savedQcIds=SavedQcAudit::all()->pluck('audit_id')->toArray();
        // if($ids->count()>0){
        //     $data = Audit::with(['qmsheet','audit_results.parameter_detail','audit_results.sub_parameter_detail','product','branch.city.state.region','branch.branchable','yard.branch.city.state.region','agency.branch.city.state.region','qa_qtl_detail','branchRepo.branch.city.state.region','agencyRepo.branch.city.state.region','user'])->withCount('artifact')->whereIn('id',$ids->pluck('audit_id'))->whereNotIn('id',$savedQcIds)->get();
        // }
        // else{
        //     $data = Audit::with(['qmsheet','audit_results.parameter_detail','audit_results.sub_parameter_detail','product','branch.city.state.region','branch.branchable','yard.branch.city.state.region','agency.branch.city.state.region','qa_qtl_detail','branchRepo.branch.city.state.region','agencyRepo.branch.city.state.region','user'])->withCount('artifact')->whereNotIn('id',$savedQcIds)->get();
        // }
        // // dd($data->first());
        // $final=[];
        // foreach($data as $item){
        //     switch($item->qmsheet->type){
        //         case 'branch':
        //             $count=Audit::where(['branch_id'=>$item->branch_id,'product_id'=>$item->product_id,'collection_manager_id'=>$item->collection_manager_id])->get();
        //             // $final[$item->id]=[$item->qmsheet->type=>$item->audit_results->count()];
        //             break;
        //         case 'agency':
        //             $final[$item->id]=[$item->qmsheet->type=>$item->audit_results->count(),'id'=>$item->qmsheet->name,];
        //             $count=Audit::where(['agency_id'=>$item->agency_id,'product_id'=>$item->product_id,'collection_manager_id'=>$item->collection_manager_id])->get();
        //             break;
        //         case 'repo_yard':
        //             // $final[$item->id]=[$item->qmsheet->type=>$item->audit_results->count()];
        //             $count=Audit::where(['yard_id'=>$item->yard_id,'product_id'=>$item->product_id,'collection_manager_id'=>$item->collection_manager_id])->get();
        //             break;
        //         case 'branch_repo':
        //             // $final[$item->id]=[$item->qmsheet->type=>$item->audit_results->count()];
        //             $count=Audit::where(['branch_repo_id'=>$item->branch_repo_id,'product_id'=>$item->product_id,'collection_manager_id'=>$item->collection_manager_id])->get();
        //             break;
        //         case 'agency_repo':
        //             // $final[$item->id]=[$item->qmsheet->type=>$item->audit_results->count()];
        //             $count=Audit::where(['agency_repo_id'=>$item->agency_repo_id,'product_id'=>$item->product_id,'collection_manager_id'=>$item->collection_manager_id])->get();
        //             break;
        //     }
        
        //     // if($count->count()>2)
            
        // }
        // $data=Artifact::where('parameter_id',null)->delete();
        // $data=Audit::with(['qmsheet','audit_results.parameter_detail','audit_results.sub_parameter_detail','product','branch.city.state.region','branch.branchable','yard.branch.city.state.region','agency.branch.city.state.region','qa_qtl_detail','branchRepo.branch.city.state.region','agencyRepo.branch.city.state.region','user'])->find(54);
        // dd($data);
        $data=[];
        $qcIds=Qc::get(['id','audit_id'])->unique('audit_id');
        // $data=Qc::whereIn('id',[])->delete();
        dd($qcIds,$data);
    }
    public function getparameter(){
        $qc=Qc::all()->pluck('audit_id')->toArray();
        $audit=Audit::with(['audit_parameter_result.parameter_detail','audit_parameter_result.result2.sub_parameter_detail'])->take(10)->get();
        $audit=$audit->whereIn('id',$qc);
        $data=[];
        // dd($audit->first());
        foreach($audit as $k=>$item){
            // dd($item->audit_parameter_result);
            foreach($item->audit_parameter_result as $k=>$value){
                if($value->parameter_detail!=null && $value->orignal_weight!=null || $value->orignal_weight!=0 ){
                    $totalWeight=0;
                    $weight=0;
                    $resultData=$value->result2->where('parameter_id',$value->parameter_id);
                    foreach($resultData as $k=>$rd){
                        $weight=$weight+((int)$rd->score);
                        if($rd->score!='N/A'){
                            $totalWeight=$totalWeight+((int)$rd->sub_parameter_detail->weight);
                        }
                    }
                    // dd($weight,$totalWeight);
                    $per=0;
                    if($totalWeight>0){
                        $per=($weight/$totalWeight)*100;
                    }
                    $data[]=['name'=>$value->parameter_detail->parameter,'point'=>round($per,2),'id'=>$value->audit_id];
                    // $data[]=['name'=>$value->parameter_detail->parameter,'point'=>$value->with_fatal_score_per,'id'=>$value->audit_id];
                }
            }
        }
    }
    public function checkRavindra(){
        // $data=Agency::where('branch_id',126)->get();
     $data=Audit::withCount('audit_results')->with('product')->where('agency_id',530)->get()->toArray();
     dd($data);   
    }
}
