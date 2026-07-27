<?php



namespace App\Http\Controllers;



use Illuminate\Http\Request;

// use PDF;

use Barryvdh\Snappy\Facades\SnappyPdf;

use App\Model\State;

use App\Model\City;
use App\User;

use App\Model\Branch;

use App\Model\Branchable;

use App\Agency;

use App\Model\AgencyRepo;

use App\Model\BranchRepo;

use App\Yard;

use App\Audit;

use App\Model\Products;
use Auth;
use Carbon\Carbon;
use DB;

// use mikehaertl\wkhtmlto\Pdf;

class AuditReportController extends Controller

{

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index(Request $request)

    {
      //dd(Auth::user()->id);
      if(isset($request['state']) && isset($request['branch']))
     {
        $state=isset($request['state'])? $request['state'] :'';
        $branch=isset($request['branch'])? $request['branch'] :'';
        $year=isset($request['year'])? $request['year'] :'';
        $quarter=isset($request['quarter'])? $request['quarter'] :'';
        $report_list=DB::table('audit_reports_data')->where('state_id',$state)->where('branch_id',$branch)->where('quarter_val',$quarter)->where('year_val',$year)->orderby('id','desc')->first();
        if(!empty($report_list))
        {
         $getState=DB::table('states')->select('name','id')->where('id',$state)->first();
         $getBranch=DB::table('branches')->select('name','id')->where('id',$branch)->first();
         $getUser='';
         if(!empty($report_list->user_id))
         {
          $getUser=User::select('name')->where('id',$report_list->user_id)->first();
         }
        }
        return view('audit_report.index',compact('state','branch','year','quarter','getState','getBranch','getUser','report_list'));
     }

    }



    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create(Request $request)
    {
       //dd('dcdcd');
    }

    public function createReports(Request $request)
    {
        $state=isset($request['state'])? $request['state'] :'';
        $branch=isset($request['branch'])? $request['branch'] :'';
        $year=isset($request['year'])? $request['year'] :'';
        $quarter=isset($request['quarter'])? $request['quarter'] :'';
        if($quarter == 1){
            $quarter='Q1_Apr';
        }
        elseif($quarter == 2){
            $quarter='Q2_July';
        }
        elseif($quarter == 3){
            $quarter='Q1_Oct';
        }else
        {
           $quarter='Q1_Jan'; 
        }
        $getState=DB::table('states')->select('name','id')->where('id',$state)->first();
        $getBranch=DB::table('branches')->select('name','id','created_at')->where('id',$branch)->first();
        return view('audit_report.create',compact('getState','getBranch','quarter','year')); 
    }

}

