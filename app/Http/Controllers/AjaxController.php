<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;
use App\Audit;
use App\AuditCycle;
use Illuminate\Support\Arr;

class AjaxController extends Controller
{
    public function getproductdata(Request $request)
    {
        $audit_type=$request->audit_type;

        $data = DB::table('audits')
                ->join('products', 'audits.product_id', '=', 'products.id')
                ->where('audits.audit_cycle_id', $request->currentCycleId)
                ->where('audits.client_id', Auth::user()->client_id)
                ->where('audits.status', '<=', 2);

        if($audit_type != 'all') {
           $data->leftJoin('qm_sheets', 'audits.qm_sheet_id', '=', 'qm_sheets.id');
           $data->where('qm_sheets.type', $audit_type);
        }

        $data=$data->select(
                    'products.name as agency_product',
                    DB::raw('COUNT(DISTINCT audits.id) as audit_count'),
                    DB::raw('ROUND(AVG(
                            CASE 
                                WHEN audits.overall_score > 0 
                                THEN audits.overall_score 
                                ELSE 0
                            END
                        ), 1) AS audit_score'),
                    DB::raw('ROUND(AVG(
                            CASE 
                                WHEN audits.score_percentage > 0 
                                THEN audits.score_percentage 
                                ELSE 0
                            END
                        ), 1) AS audit_score_percentage')
                )
                ->groupBy('products.id', 'products.name')
                ->get()
                ->map(function ($row) {
                                return [
                                    'agency_product' => $row->agency_product,
                                    'audit_count' => (int) $row->audit_count,
                                    'audit_score' => (int) $row->audit_score,
                                    'audit_score_percentage' => (int) $row->audit_score_percentage
                                ];
                            })
                ->toArray();
        // dd($data);
        $agencyProducts = array_column($data, 'agency_product');
        $auditCountProducts = array_column($data, 'audit_count');
        $auditScoreProducts = array_column($data, 'audit_score');
        $auditScorePercentageProducts = array_column($data, 'audit_score_percentage');
        // dd($auditScorePercentageProducts);
        return view('dashboardAjax.product_wise_view',compact('data','agencyProducts','auditCountProducts','auditScoreProducts','auditScorePercentageProducts'));
    }

    public function getzonedata(Request $request)
    {
        $audit_type=$request->audit_type;
        $currCycle=$request->currentCycleId;

        $databyZoneRegionAudits = DB::table('audits')
            ->where('audits.client_id', auth()->user()->client_id);
        
        if($audit_type != 'all') {
           $databyZoneRegionAudits->leftJoin('qm_sheets', 'audits.qm_sheet_id', '=', 'qm_sheets.id');
           $databyZoneRegionAudits->where('qm_sheets.type', $audit_type);
        }


           $databyZoneRegionAudits->join('audit_allocation', function ($join) use ($currCycle) {
                $join->whereRaw("
                    CASE audit_allocation.type_of_agency
                        WHEN 'agency' THEN audit_allocation.agency_id = audits.agency_id
                        WHEN 'branch' THEN audit_allocation.agency_id = audits.branch_id
                        WHEN 'yard' THEN audit_allocation.agency_id = audits.yard_id
                        WHEN 'branchrepo' THEN audit_allocation.agency_id = audits.branch_repo_id
                        WHEN 'agencyrepo' THEN audit_allocation.agency_id = audits.agency_repo_id
                        WHEN 'yardrepo' THEN audit_allocation.agency_id = audits.yard_repo_id
                    END
                ")
                ->where('audit_allocation.audit_cycle_id', $currCycle);
            });      
            
            

            $databyZoneRegionAudits=$databyZoneRegionAudits->join('regions', 'audit_allocation.region_id', '=', 'regions.id')->leftJoin('closure_audits', 'audits.id', '=', 'closure_audits.audit_id')
               ->select('regions.name as region_name',
                DB::raw('AVG(audits.overall_score) as average_score'),
                DB::raw('AVG(audits.score_percentage) as average_score_percentage'),
                DB::raw('COUNT(DISTINCT audits.id) as audit_count'),
                DB::raw('COUNT(DISTINCT CASE WHEN closure_audits.status = 0 THEN closure_audits.audit_id END) as sent_for_closure_count'),
                DB::raw('COUNT(DISTINCT CASE WHEN closure_audits.status = 1 THEN closure_audits.audit_id END) as closure_completed_count')
            )
            ->where('audits.audit_cycle_id', $request->currentCycleId)
            ->where('audits.status', '<=', 2)
            ->groupBy('regions.name')
            ->get()
            ->map(function ($row) {
                return [
                    'region_name' => $row->region_name,
                    'average_score' => (float) $row->average_score,
                    'average_score_percentage' => (float) $row->average_score_percentage,
                    'audit_count' => (int) $row->audit_count,
                    'sent_for_closure_count' => (int) $row->sent_for_closure_count,
                    'closure_completed_count' => (int) $row->closure_completed_count,
                ];
            })
            ->toArray();


        //echo "<pre>"; print_r($databyZoneRegionAudits); die;

        $allocationZoneProduct = DB::table('audit_allocation')
            ->join('regions', 'audit_allocation.region_id', '=', 'regions.id')
            ->select('regions.name as region_name', 'audit_allocation.product', DB::raw('COUNT(*) as product_count'));

        if($audit_type != 'all') {
           if($audit_type == "agency_repo") { $audit_type='agencyrepo'; }
           if($audit_type == "branch_repo") { $audit_type='branchrepo'; }
           if($audit_type == "yard_repo") { $audit_type='yardrepo'; }
           $allocationZoneProduct->where('type_of_agency', $audit_type);
        }  

            $allocationZoneProduct=$allocationZoneProduct->where('audit_allocation.client_id', Auth::user()->client_id)
            ->where('audit_allocation.audit_cycle_id', $request->currentCycleId)
            ->groupBy('regions.name', 'audit_allocation.product')
            ->orderBy('regions.name', 'asc')
            ->get()
            ->groupBy('region_name')
            ->map(function ($items) {
                return $items->map(function ($item) {
                    return [
                        'product' => $item->product,
                        'product_count' => $item->product_count,
                    ];
                })->toArray();
            })
            ->toArray();             

        return view('dashboardAjax.zone_wise_view', compact('databyZoneRegionAudits','allocationZoneProduct'));
    }

    public function audit_schedule(Request $request)
    {
        $currentCycleId=$request->currentCycleId;
        $audit_type=$request->audit_type;

        $allocatedAudits = DB::table('auditor_assigns')
                            ->select(
                                DB::raw("DATE(audit_date) as created_date,process_review_period"),
                                DB::raw("COUNT(*) as total_allocated")
                            );

                        if($audit_type != 'all') {
                            if($audit_type == "agency_repo") { $audit_type='agencyrepo'; }
                            if($audit_type == "branch_repo") { $audit_type='branchrepo'; }
                            if($audit_type == "yard_repo") { $audit_type='yardrepo'; }
                            $allocatedAudits->where('type_of_agency', $audit_type);
                        }  

                        $allocatedAudits=$allocatedAudits->where('audit_cycle_id', $request->currentCycleId)
                            ->where('client_id', Auth::user()->client_id)
                            ->whereNotNull('audit_date')
                            ->groupBy(DB::raw("DATE(audit_date),process_review_period")) 
                            ->orderBy('created_date', 'asc')                                                      
                            ->get();

        $submittedAudits = DB::table('audits')
                            ->select(
                                DB::raw("DATE(audits.created_at) as created_date"),
                                DB::raw("COUNT(*) as total_submitted")
                            );

                            if($audit_type != 'all') {
                                 $submittedAudits->leftJoin('qm_sheets', 'audits.qm_sheet_id', '=', 'qm_sheets.id');
                                $submittedAudits->where('qm_sheets.type', $audit_type);
                            }

                           $submittedAudits=$submittedAudits->where('audits.audit_cycle_id',        $request->currentCycleId)
                            ->where('audits.client_id',Auth::user()->client_id)
                            ->where('audits.status', '<=', 2)
                            ->groupBy(DB::raw("DATE(audits.created_at)")) 
                            ->orderBy('created_date', 'asc')                                                 
                            ->get()
                            ->map(function ($row) {
                                return [
                                    'created_date' => $row->created_date,
                                    'total_submitted' => (int) $row->total_submitted,
                                ];
                            })
                            ->toArray();

        // echo "<pre>"; print_r($submittedAudits); //die;
        // echo "<pre>"; print_r($allocatedAudits); die;
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        $dateArray = [];
        $current = $startDate->copy();
        while ($current->lte($endDate)) {
            $dateArray[] = $current->format('Y-m-d');
            $current->addDay();
        }

        
        // Prepare lookup arrays for quick access
        $allocatedLookup = [];
        foreach ($allocatedAudits as $a) {
            $allocatedLookup[$a->created_date] = [
            'process_review_period' => $a->process_review_period,
            'total_allocated' => $a->total_allocated
            ];
        }

        $submittedLookup = [];
        foreach ($submittedAudits as $s) {
            $submittedLookup[$s['created_date']] = $s['total_submitted'];
        }

        // Now, for each date in $dateArray, plot data if available, otherwise zero
        $finalArray = [];
        $cycle_name = "";
        foreach ($dateArray as $date) {
            $assigned = isset($allocatedLookup[$date]) ? $allocatedLookup[$date]['total_allocated'] : 0;
            $cycle_name = isset($allocatedLookup[$date]) ? $allocatedLookup[$date]['process_review_period'] : $cycle_name;
            $submitted = isset($submittedLookup[$date]) ? $submittedLookup[$date] : 0;
            $finalArray[] = [
            'date' => $date,
            'audit_count' => $submitted,
            'assigned_count' => $assigned
            ];
        }
        // echo "<pre>"; print_r($finalArray); die;
        
        return view('dashboardAjax.audit_schedule',compact('finalArray','cycle_name','currentCycleId','audit_type'));
    }

    public function audit_schedule_detail(Request $request) {

        $audit_type=$request->audit_type;
        $currCycle=$request->currentCycleId;

        $allocatedAudits = DB::table('auditor_assigns')
                            ->select('auditor_assigns.id as allocID','auditor_assigns.final_agency_name','auditor_assigns.agency_code','auditor_assigns.location','auditor_assigns.audit_date');

                            if($audit_type != 'all') {
                                if($audit_type == "agency_repo") { $audit_type='agencyrepo'; }
                                if($audit_type == "branch_repo") { $audit_type='branchrepo'; }
                                if($audit_type == "yard_repo") { $audit_type='yardrepo'; }
                                $allocatedAudits->where('type_of_agency', $audit_type);
                            }  

                            $allocatedAudits=$allocatedAudits->where('audit_cycle_id', $request->currentCycleId)
                            ->where('client_id', Auth::user()->client_id)
                            ->whereNotNull('audit_date') 
                            ->where(DB::raw("audit_date"),$request->date)                                                                     
                            ->get();

            $submittedAudits = DB::table('audits');

            $submittedAudits->join('audit_allocation', function ($join) use ($currCycle) {
                $join->whereRaw("
                    CASE audit_allocation.type_of_agency
                        WHEN 'agency' THEN audit_allocation.agency_id = audits.agency_id
                        WHEN 'branch' THEN audit_allocation.agency_id = audits.branch_id
                        WHEN 'yard' THEN audit_allocation.agency_id = audits.yard_id
                        WHEN 'branchrepo' THEN audit_allocation.agency_id = audits.branch_repo_id
                        WHEN 'agencyrepo' THEN audit_allocation.agency_id = audits.agency_repo_id
                        WHEN 'yardrepo' THEN audit_allocation.agency_id = audits.yard_repo_id
                    END
                ")
                ->where('audit_allocation.audit_cycle_id', $currCycle);
            });
      

            $submittedAudits = $submittedAudits->select('audits.id as allocID','audit_allocation.final_agency_name as agencies_name','audit_allocation.agency_code as agency_id','audit_allocation.location','audits.audit_date_by_aud','audits.overall_score', 'audits.score_percentage');
                           
                            if($audit_type != 'all') {
                                $submittedAudits->leftJoin('qm_sheets', 'audits.qm_sheet_id', '=', 'qm_sheets.id');
                                $submittedAudits->where('qm_sheets.type', $audit_type);
                            }

                            $submittedAudits=$submittedAudits->where('audits.audit_cycle_id', $request->currentCycleId)
                            ->where('audits.client_id',Auth::user()->client_id) 
                            ->where('audits.status', '<=', 2)                          
                            ->orderBy('audits.id', 'asc') 
                            ->where(DB::raw("DATE(audits.created_at)"),$request->date)                                                    
                            ->get()->map(function ($item) {
                                return (array) $item;   
                            })
                            ->toArray();
        // dd($submittedAudits);
       // echo "<pre>";print_r($submittedAudits); die;
       
        $html='';

        foreach($allocatedAudits as $alloc) {
            // $index = array_search($alloc->agency_code, array_column($submittedAudits, 'agency_id'));
            // if($index !== false) {
            //     continue; // Skip this allocated audit as it has a corresponding submitted audit
            // }
            $html.="<tr>";
            $html.="<td>".$alloc->final_agency_name."</td>";
            $html.="<td>".$alloc->agency_code."</td>";
            $html.="<td>".$alloc->location."</td>";
            $html.="<td>Allocated</td>";
            $html.="</tr>";            
        }

        $html.="<tr><td colspan='4' style='font-size:16px;'><b>Submitted Audit List</b></td></tr>";

        foreach($submittedAudits as $alloc) {
            $html.="<tr>";
            $html.="<td>".$alloc['agencies_name']."</td>";
            $html.="<td>".$alloc['agency_id']."</td>";
            $html.="<td>".$alloc['location']."</td>";
            $html.="<td>Submitted : ".$alloc['score_percentage']."</td>";
            $html.="</tr>";            
        }
        return $html;
    }

    public function state_wise_data_top(Request $request)
    {
        $audit_type=$request->audit_type;
        
        $data = DB::table('audits')
                ->join('auditor_assigns', function ($join) use ($request) {
                    $join->whereRaw("
                        CASE auditor_assigns.type_of_agency
                            WHEN 'agency' THEN auditor_assigns.agency_id = audits.agency_id
                            WHEN 'branch' THEN auditor_assigns.agency_id = audits.branch_id
                            WHEN 'yard' THEN auditor_assigns.agency_id = audits.yard_id
                            WHEN 'branchrepo' THEN auditor_assigns.agency_id = audits.branch_repo_id
                            WHEN 'agencyrepo' THEN auditor_assigns.agency_id = audits.agency_repo_id
                            WHEN 'yardrepo' THEN auditor_assigns.agency_id = audits.yard_repo_id
                        END
                    ")
                    ->where('auditor_assigns.audit_cycle_id', $request->currentCycleId);
                })            
            ->where('audits.audit_cycle_id', $request->currentCycleId)
            ->where('audits.client_id', Auth::user()->client_id)
            ->where('audits.status', '<=', 2);

                    if($audit_type != 'all') {
                        $data->leftJoin('qm_sheets', 'audits.qm_sheet_id', '=', 'qm_sheets.id');
                        $data->where('qm_sheets.type', $audit_type);
                    }

            $data=$data->select(
                'auditor_assigns.state as state_name',
                'auditor_assigns.location as city_name',
                DB::raw('COUNT(DISTINCT audits.id) as audit_count'),
                DB::raw('ROUND(AVG(CASE WHEN audits.overall_score > 0 THEN audits.overall_score ELSE 0 END), 1) AS audit_score'),
                DB::raw('ROUND(AVG(CASE WHEN audits.score_percentage > 0 THEN audits.score_percentage ELSE 0 END), 1) AS score_percentage')
            )
            ->groupBy('auditor_assigns.state', 'auditor_assigns.location')
            ->get()
            ->groupBy('state_name')
            ->map(function ($cities, $state) {
                $topCities = $cities->sortByDesc('audit_count')->take(5)->values();
                $stateAuditCount = $topCities->sum('audit_count');
                $stateAuditScore = $topCities->avg('audit_score');
                $stateAuditScorePercentage = $topCities->avg('score_percentage');
                return [
                    'state_name' => $state,
                    'audit_count' => $stateAuditCount,
                    'audit_score' => round($stateAuditScore, 2),
                    'score_percentage' => round($stateAuditScorePercentage, 2),
                    'cities' => $topCities->map(function ($city) {
                        return [
                            'city_name' => $city->city_name,
                            'audit_count' => (int) $city->audit_count,
                            'audit_score' => (float) $city->audit_score,
                            'score_percentage' => (float) $city->score_percentage,
                        ];
                    })->toArray(),
                ];
            })
            ->sortByDesc('score_percentage')
            ->take(5)
            ->values()
            ->toArray();
        $cityWiseArray = [];
        foreach ($data as $state) {
            $stateName = ucfirst(strtolower($state['state_name'])); // normalize case
            $cities = [];

            foreach ($state['cities'] as $city) {
                $cities[] = [
                    'name'  => ucfirst(strtolower($city['city_name'])),
                    'y'     => $city['score_percentage'],
                    'color' => '#28a745', // static color like your example
                ];
            }
            $cityWiseArray[$stateName] = $cities;
        }
    

        $stateWiseArray = [];

        foreach ($data as $state) {
            $stateWiseArray[] = [
                'name'  => ucfirst(strtolower($state['state_name'])),
                'y'     => $state['score_percentage'],
                'color' => '#28a745',
            ];
        }

        $states = array_map(function ($state) {
            return ucfirst(strtolower($state['state_name']));
        }, $data);

        return view('dashboardAjax.state_wise_data_top', compact('stateWiseArray','cityWiseArray','states','audit_type'));       
    }

    public function state_wise_data_bot(Request $request)
    {
        $audit_type=$request->audit_type;
        $data = DB::table('audits')
             ->join('auditor_assigns', function ($join) use ($request) {
                    $join->whereRaw("
                        CASE auditor_assigns.type_of_agency
                            WHEN 'agency' THEN auditor_assigns.agency_id = audits.agency_id
                            WHEN 'branch' THEN auditor_assigns.agency_id = audits.branch_id
                            WHEN 'yard' THEN auditor_assigns.agency_id = audits.yard_id
                            WHEN 'branchrepo' THEN auditor_assigns.agency_id = audits.branch_repo_id
                            WHEN 'agencyrepo' THEN auditor_assigns.agency_id = audits.agency_repo_id
                            WHEN 'yardrepo' THEN auditor_assigns.agency_id = audits.yard_repo_id
                        END
                    ")
                    ->where('auditor_assigns.audit_cycle_id', $request->currentCycleId);
                })  
            ->where('audits.audit_cycle_id', $request->currentCycleId)
            ->where('audits.client_id', Auth::user()->client_id)
            ->where('audits.status', '<=', 2);
                    if($audit_type != 'all') {
                        $data->leftJoin('qm_sheets', 'audits.qm_sheet_id', '=', 'qm_sheets.id');
                        $data->where('qm_sheets.type', $audit_type);
                    }

            $data=$data->select(
            'auditor_assigns.state as state_name',
            'auditor_assigns.location as city_name',
            DB::raw('COUNT(DISTINCT audits.id) as audit_count'),
            DB::raw('ROUND(AVG(CASE WHEN audits.overall_score > 0 THEN audits.overall_score ELSE 0 END), 1) AS audit_score'),
            DB::raw('ROUND(AVG(CASE WHEN audits.score_percentage > 0 THEN audits.score_percentage ELSE 0 END), 1) AS score_percentage')
            )
            ->groupBy('auditor_assigns.state', 'auditor_assigns.location')
            ->get()
            ->groupBy('state_name')
            ->map(function ($cities, $state) {
            $bottomCities = $cities->sortBy('audit_score')->take(5)->values();
            $stateAuditCount = $bottomCities->sum('audit_count');
            $stateAuditScore = $bottomCities->avg('audit_score');
            $stateAuditScorePercentage = $bottomCities->avg('score_percentage');
            return [
                'state_name' => $state,
                'audit_count' => $stateAuditCount,
                'audit_score' => round($stateAuditScore, 2),
                'score_percentage' => round($stateAuditScorePercentage, 2),
                'cities' => $bottomCities->map(function ($city) {
                return [
                    'city_name' => $city->city_name,
                    'audit_count' => (int) $city->audit_count,
                    'audit_score' => (float) $city->audit_score,
                    'score_percentage' => (float) $city->score_percentage,
                ];
                })->toArray(),
            ];
            })
            ->sortBy('score_percentage')
            ->take(5)
            ->values()
            ->toArray();

        $cityWiseArray = [];
        foreach ($data as $state) {
            $stateName = ucfirst(strtolower($state['state_name']));
            $cities = [];
            foreach ($state['cities'] as $city) {
            $cities[] = [
                'name'  => ucfirst(strtolower($city['city_name'])),
                'y'     => $city['score_percentage'],
                'color' => '#dc3545', // red for bottom
            ];
            }
            $cityWiseArray[$stateName] = $cities;
        }

        $stateWiseArray = [];
        foreach ($data as $state) {
            $stateWiseArray[] = [
            'name'  => ucfirst(strtolower($state['state_name'])),
            'y'     => $state['score_percentage'],
            'color' => '#dc3545',
            ];
        }

        $states = array_map(function ($state) {
            return ucfirst(strtolower($state['state_name']));
        }, $data);

        // Pass arrays to the view
        return view('dashboardAjax.state_wise_data_bot', compact('stateWiseArray','cityWiseArray','states','audit_type'));
        
    }

    public function param_wise_data(Request $request)
    {
        $audit_type=$request->audit_type;
        // Fetch parameters and their subparameters with scores
        $parameters = DB::table('audits')
            ->join('audit_results', 'audit_results.audit_id', '=', 'audits.id')
            ->join('qm_sheet_parameters', 'audit_results.parameter_id', '=', 'qm_sheet_parameters.id')
            ->join('qm_sheet_sub_parameters', 'audit_results.sub_parameter_id', '=', 'qm_sheet_sub_parameters.id')
            ->where('audits.audit_cycle_id', $request->currentCycleId)
            ->where('audits.client_id', Auth::user()->client_id)
            ->where('audits.status', '<=', 2);

                    if($audit_type != 'all') {
                        $parameters->leftJoin('qm_sheets', 'audits.qm_sheet_id', '=', 'qm_sheets.id');
                        $parameters->where('qm_sheets.type', $audit_type);
                    }

            $parameters=$parameters->select(               
                'qm_sheet_parameters.parameter as parameter_name',               
                'qm_sheet_sub_parameters.sub_parameter as sub_parameter_name',                
                DB::raw('sum(audit_results.score) as total_score'),
                DB::raw('sum(qm_sheet_sub_parameters.weight) as total_weight')
            )
            ->groupBy('qm_sheet_parameters.parameter', 'qm_sheet_sub_parameters.sub_parameter')
            ->orderBy('parameter_name')           
            ->get();

        $mainParam = DB::table('audits')
            ->join('audit_results', 'audit_results.audit_id', '=', 'audits.id')
            ->join('qm_sheet_parameters', 'audit_results.parameter_id', '=', 'qm_sheet_parameters.id')
            ->join('qm_sheet_sub_parameters', 'audit_results.sub_parameter_id', '=', 'qm_sheet_sub_parameters.id')
            ->where('audits.audit_cycle_id', $request->currentCycleId)
            ->where('audits.client_id', Auth::user()->client_id)
            ->where('audits.status', '<=', 2);

                    if($audit_type != 'all') {
                        $mainParam->leftJoin('qm_sheets', 'audits.qm_sheet_id', '=', 'qm_sheets.id');
                        $mainParam->where('qm_sheets.type', $audit_type);
                    }

            $mainParam=$mainParam->select(               
                'qm_sheet_parameters.parameter as parameter_name',                    
                DB::raw('sum(audit_results.score) as total_score'),
                DB::raw('sum(qm_sheet_sub_parameters.weight) as total_weight')
            )
            ->groupBy('qm_sheet_parameters.parameter')
            ->orderBy('parameter_name')           
            ->get();

        $allParam=array();       
                
        foreach($mainParam as $mp) {
            $array=array();
            $array['parameter_name']=$mp->parameter_name;
            $array['avg_score']=($mp->total_weight != 0) ? round(($mp->total_score/$mp->total_weight) * 100) : 0;
            $array['subparameters']=array();
            foreach($parameters as $sp) {
                if($sp->parameter_name == $mp->parameter_name) {
                    $subArray=array();
                    $subArray['sub_parameter_name']=$sp->sub_parameter_name;
                    $subArray['avg_score']=($sp->total_weight != 0) ? round(($sp->total_score/$sp->total_weight) * 100) : 0;
                    $array['subparameters'][]=$subArray;
                }
            }
            $allParam[]=$array;
        }

        //echo "<pre>"; print_r($mainParam); die;

        return view('dashboardAjax.param_wise_data', [            
            'allParam' => $allParam,         
            'currentCycleId' => $request->currentCycleId,
            'audit_type' => $audit_type
        ]);
    }

    public function param_compliance_data(Request $request){
        $audit_type=$request->audit_type;
        // Get all parameters for dropdown/filter based on sheet_id from audits
        $getSheets=DB::table('qm_sheets');
                    if($audit_type != 'all') {
                        $getSheets->where('type', $audit_type);
                    }
        $getSheets=$getSheets->where('client_id',Auth::user()->client_id)
                    //->where('sheet_type',1)
                    ->pluck('id')
                    ->toArray();
       
        
        $sheetId = DB::table('audits')
            ->where('audits.audit_cycle_id', $request->currentCycleId)
            ->where('audits.client_id', Auth::user()->client_id)
            ->where('audits.status', '<=', 2)
            ->whereIn('audits.qm_sheet_id', $getSheets)
           ->first();

        // If no audits found, return empty view
        // if (!$sheetId) {
        //     return view('dashboardAjax.param_compliance_data', [    
        //         'parameters' => [],
        //         'selectedParameterId' => null,
        //         'stateCompliance' => [], 
        //         'compliantStates' => [],
        //         'nonCompliantStates' => []
        //     ]);
        // }
        
        $parameters = DB::table('qm_sheet_parameters')
            ->whereIn('qm_sheet_id', $getSheets)
            ->select('id', 'parameter')
            ->orderBy('parameter')
            ->get();      

        // Get selected parameter from request (default: first parameter)
        $selectedParameterId = $request->input('parameter_id');
        if (!$selectedParameterId && count($parameters)) {
            $selectedParameterId = 'all';
        }

        // Main parameter-wise compliance counts grouped by states, filtered by selected parameter
        $paramStateData = DB::table('audits')
            ->join('audit_results', 'audit_results.audit_id', '=', 'audits.id');
                    if($audit_type != 'all') {
                        $paramStateData->leftJoin('qm_sheets', 'audits.qm_sheet_id', '=', 'qm_sheets.id');
                        $paramStateData->where('qm_sheets.type', $audit_type);
                    }
        $paramStateData=$paramStateData->join('qm_sheet_parameters', 'audit_results.parameter_id', '=', 'qm_sheet_parameters.id')
             ->join('auditor_assigns', function ($join) use ($request) {
                    $join->whereRaw("
                        CASE auditor_assigns.type_of_agency
                            WHEN 'agency' THEN auditor_assigns.agency_id = audits.agency_id
                            WHEN 'branch' THEN auditor_assigns.agency_id = audits.branch_id
                            WHEN 'yard' THEN auditor_assigns.agency_id = audits.yard_id
                            WHEN 'branchrepo' THEN auditor_assigns.agency_id = audits.branch_repo_id
                            WHEN 'agencyrepo' THEN auditor_assigns.agency_id = audits.agency_repo_id
                            WHEN 'yardrepo' THEN auditor_assigns.agency_id = audits.yard_repo_id
                        END
                    ")
                    ->where('auditor_assigns.audit_cycle_id', $request->currentCycleId);
                })  
            ->where('audits.audit_cycle_id', $request->currentCycleId)
            ->where('audits.client_id', Auth::user()->client_id)
            ->where('audits.status', '<=', 2);
            
            if($request->input('parameter_id') && $request->input('parameter_id') != 'all') {
                $paramStateData=$paramStateData->where('qm_sheet_parameters.id', $selectedParameterId);
            }

            $paramStateData=$paramStateData->select(
            'auditor_assigns.state as state_name',
            DB::raw("SUM(CASE WHEN audit_results.option_selected = 'Satisfactory' THEN 1 ELSE 0 END) as compliant_count"),
            DB::raw("SUM(CASE WHEN audit_results.option_selected = 'Unsatisfactory' THEN 1 ELSE 0 END) as non_compliant_count")
            )
            ->groupBy('auditor_assigns.state')
            ->orderBy('state_name')
            ->get();

        // Structure: [state] => counts
        $stateCompliance = [];
        foreach ($paramStateData as $row) {
            $state = ucfirst($row->state_name);
            $stateCompliance[$state] = [
            'compliant' => (int)$row->compliant_count,
            'non' => (int)$row->non_compliant_count
            ];
        }
      
        if($request->has('isAjax') && $request->isAjax == true){
            return response()->json([
                'parameters' => $parameters,
                'selectedParameterId' => $selectedParameterId,
                'stateCompliance' => $stateCompliance,
                'currentCycleId' => $request->currentCycleId,
                'audit_type'=>$audit_type        
            ]);
        }
       
        // Pass to view
        return view('dashboardAjax.param_compliance_data', [
            'parameters' => $parameters,
            'selectedParameterId' => $selectedParameterId,
            'stateCompliance' => $stateCompliance,
            'currentCycleId' => $request->currentCycleId,
            'audit_type' => $audit_type        
        ]);  

    }

    public function param_compliance_table_view(Request $request) {
        $audit_type=$request->audit_type;
        $stateName = $request->input('stateName');
        $currentCycleId = $request->input('currentCycleId');
        $parameterId = $request->input('paramId');
        $compliantAgencies = [];
        $nonCompliantAgencies = [];    

        $query = DB::table('audits')
                ->leftJoin('audit_results', 'audits.id', '=', 'audit_results.audit_id')
                ->join('auditor_assigns', function ($join) use ($request) {
                    $join->whereRaw("
                        CASE auditor_assigns.type_of_agency
                            WHEN 'agency' THEN auditor_assigns.agency_id = audits.agency_id
                            WHEN 'branch' THEN auditor_assigns.agency_id = audits.branch_id
                            WHEN 'yard' THEN auditor_assigns.agency_id = audits.yard_id
                            WHEN 'branchrepo' THEN auditor_assigns.agency_id = audits.branch_repo_id
                            WHEN 'agencyrepo' THEN auditor_assigns.agency_id = audits.agency_repo_id
                            WHEN 'yardrepo' THEN auditor_assigns.agency_id = audits.yard_repo_id
                        END
                    ")
                    ->where('auditor_assigns.audit_cycle_id', $request->currentCycleId);
                })  
                ->leftJoin('audit_reports','audits.id', '=', 'audit_reports.audit_id');
                    if($audit_type != 'all') {
                        $query->leftJoin('qm_sheets', 'audits.qm_sheet_id', '=', 'qm_sheets.id');
                        $query->where('qm_sheets.type', $audit_type);
                    }
        $query=$query->select(
                    'audits.id as audit_id',
                    'audits.overall_score',
                    'audits.created_at',
                    'auditor_assigns.state as state_name',
                    'auditor_assigns.location',
                    'auditor_assigns.final_agency_name',
                    'auditor_assigns.agency_code',
                    'audit_reports.checksheet_pdf',
                    DB::raw("SUM(CASE WHEN audit_results.option_selected = 'Satisfactory' THEN 1 ELSE 0 END) as compliant_count"),
                    DB::raw("SUM(CASE WHEN audit_results.option_selected = 'Unsatisfactory' THEN 1 ELSE 0 END) as non_compliant_count")
                )
                ->where('audits.audit_cycle_id', $currentCycleId)
                ->where('audits.client_id', Auth::user()->client_id)
                ->where('audits.status', '<=', 2);

                if ($stateName != "all") {
                    $query = $query->where('auditor_assigns.state', $stateName);
                }

                if ($parameterId != "all") {
                    $query = $query->where('audit_results.parameter_id', $parameterId);
                }

                $query = $query->groupBy(
                    'audits.id',
                    'audits.overall_score',
                    'audits.created_at',
                    'auditor_assigns.state',
                    'auditor_assigns.location',
                    'auditor_assigns.final_agency_name',
                    'auditor_assigns.agency_code',
                    'audit_reports.checksheet_pdf'
                )->get();

       // echo '<pre>'; print_r($query); die;
           
        foreach ($query as $row) {  
            $total=$row->compliant_count + $row->non_compliant_count;
            $compliance_score=($total != 0) ? round(($row->compliant_count/$total)*100) : 0;
            $nonCompliance_score=($total != 0) ? round(($row->non_compliant_count/$total)*100) : 0;              
            $agencyData = [
                'audit_id' => $row->audit_id,
                'overall_score' => $row->overall_score,
                'created_at' => $row->created_at,
                'state_name' => $row->state_name,
                'location' => $row->location,
                'final_agency_name' => $row->final_agency_name,
                'agency_code' => $row->agency_code,                    
                'compliance_score' => $compliance_score,
                'nonCompliance_score' => $nonCompliance_score,
                'compliance_count' => $row->compliant_count,
                'nonCompliance_count' => $row->non_compliant_count,
                'total'=>$total,
                'checksheet_pdf'=> $row->checksheet_pdf
            ];
            if ($compliance_score >= 85) {
                $compliantAgencies[] = $agencyData;
            } else {
                $nonCompliantAgencies[] = $agencyData;
            }
        }
         
      
        return view('dashboardAjax.param_compliance_table_view', [
            'compliantAgencies' => $compliantAgencies,
            'nonCompliantAgencies' => $nonCompliantAgencies,
            'stateName' => $stateName,
            'parameterId' => $parameterId,
            'currentCycleId' => $currentCycleId,
            'audit_type' => $audit_type
        ]); 
        
    }

    public function param_detail_modal_view(Request $request) {
        $audit_type=$request->audit_type;
        $currentCycleId = $request->input('currentCycleId');
        $paramName = $request->input('paramName');
        //find previous 3 cycle
        $cycleData=AuditCycle::where('client_id',Auth::user()->client_id)
                    ->where('id','<=',$currentCycleId)
                    ->orderBy('id','desc')
                    ->limit(3)
                    ->pluck('id','name')
                    ->toArray();
        $cycleList = array_values($cycleData);
        //echo $paramName; die;
        // Fetch sheet IDs from audits in the current cycle   
        $sheetId = DB::table('audits');
                    if($audit_type != 'all') {
                        $sheetId->leftJoin('qm_sheets', 'audits.qm_sheet_id', '=', 'qm_sheets.id');
                        $sheetId->where('qm_sheets.type', $audit_type);
                    }
        $sheetId=$sheetId->select(DB::raw('distinct(qm_sheet_id)'))
            ->where('audits.audit_cycle_id', $currentCycleId)
            ->where('audits.client_id', Auth::user()->client_id)
            ->where('audits.status', '<=', 2)
            ->pluck('audits.qm_sheet_id')->toArray();
             
        // Fetch parameter ID based on name
        $parameter = DB::table('qm_sheet_sub_parameters')
            ->where('sub_parameter', $paramName)
            ->whereIn('qm_sheet_id', $sheetId)
            ->pluck('id')->toArray();
        
        // Fetch audits with sub-parameters under this parameter
        $audits = DB::table('audits')
            ->select('audits.id as audit_id', 'audits.agency_id','audit_allocation.final_agency_name as agency_name', 'audit_allocation.agency_code as agency_code', 'audit_allocation.location')
            ->join('audit_results', 'audits.id', '=', 'audit_results.audit_id')
            ->join('audit_allocation', function ($join) use ($currentCycleId) {
                $join->whereRaw("
                    CASE audit_allocation.type_of_agency
                        WHEN 'agency' THEN audit_allocation.agency_id = audits.agency_id
                        WHEN 'branch' THEN audit_allocation.agency_id = audits.branch_id
                        WHEN 'yard' THEN audit_allocation.agency_id = audits.yard_id
                        WHEN 'branchrepo' THEN audit_allocation.agency_id = audits.branch_repo_id
                        WHEN 'agencyrepo' THEN audit_allocation.agency_id = audits.agency_repo_id
                        WHEN 'yardrepo' THEN audit_allocation.agency_id = audits.yard_repo_id
                    END
                ")
                ->where('audit_allocation.audit_cycle_id', $currentCycleId);
            })           
            ->where('audits.audit_cycle_id', $currentCycleId)
            ->where('audits.client_id', Auth::user()->client_id)    
            ->where('audits.status', '<=', 2)
            ->whereIn('audit_results.sub_parameter_id', $parameter) 
            ->whereIn('audit_results.option_selected', ['Unsatisfactory','N/A'])          
            ->get();

        $finalArray=array();

        foreach($audits as $audit) {
            // For each audit, fetch last 3 audits of the same agency up to current cycle
            // and include audit_results and audit_reports relationships
            $getOtherData=Audit::with(['audit_reports',
                                'audit_results' => function ($query) use ($parameter) {
                                    $query->whereIn('sub_parameter_id', $parameter);
                                }
                            ])                       
                        ->where('agency_id', $audit->agency_id)
                        ->whereIn('audit_cycle_id',$cycleList)
                        ->where('client_id', Auth::user()->client_id)    
                        ->where('status', '<=', 2)
                        ->orderBy('audit_cycle_id', 'desc')
                        ->get();  
            
                    
            $createData = [];
            $createData['agency_name'] = $audit->agency_name;
            $createData['agency_code'] = $audit->agency_code;
            $createData['location'] = $audit->location;
            $is_repeat=0;
            $curre_repeat=0;

            foreach($cycleData as $key=>$val) {
                $createData['cycle_score_'.$val]=0;
                $createData['cycle_closure_link_'.$val]='#';
            }

            foreach($getOtherData as $other){               
                foreach($other->audit_results as $res){                   
                    if($res->option_selected == 'Unsatisfactory' && $curre_repeat == 0){                       
                       $curre_repeat=1;
                       continue;
                    }
                    if($res->option_selected == 'Unsatisfactory' && $curre_repeat == 1) {
                        $is_repeat+=1;
                    }
                }
                $key='cycle_score_'.$other->audit_cycle_id;
                if(array_key_exists($key,$createData)) {
                   $createData['cycle_score_'.$other->audit_cycle_id] = round((float) str_replace('%', '', $other->score_percentage));
                    if($other->audit_reports && $other->audit_reports->closure_pdf != "") {
                        $createData['cycle_closure_link_'.$other->audit_cycle_id]=$other->audit_reports->closure_pdf;
                    }
                }
            }

            $createData['is_repeat'] = $is_repeat; 
            $finalArray[] = $createData;               
        }

        

        $html='<thead>';
        $html.='<tr>
                    <th rowspan="2">Name</th>
                    <th rowspan="2">Code</th>
                    <th rowspan="2">Location</th>
                    <th rowspan="2">Repeat Issue</th>
                    <th colspan="'.count($cycleData).'">'.count($cycleData).' Month Trend(score)</th>                
                    <th colspan="'.count($cycleData).'">Action Planning</th>
                </tr>';
        $html.='<tr>';
        foreach($cycleData as $key=>$val) {
            $html.='<th>'.$key.'</th>';
        }
        foreach($cycleData as $key=>$val) {
            $html.='<th>'.$key.'</th>';
        }
        $html.='</tr>';
        $html.='</thead>';
        $html.='<tbody>';
        foreach($finalArray as $row) {
            $html.='<tr>';
            $html.='<td>'.$row['agency_name'].'</td>';  
            $html.='<td>'.$row['agency_code'].'</td>';
            $html.='<td>'.$row['location'].'</td>';
            $html.='<td>'.$row['is_repeat'].'</td>';
            foreach($cycleData as $key=>$val) {
                $html.='<td>'.(isset($row['cycle_score_'.$val]) ? $row['cycle_score_'.$val] : 0).'%</td>';
            }
            foreach($cycleData as $key=>$val) {
                if(isset($row['cycle_closure_link_'.$val]) && $row['cycle_closure_link_'.$val] != '#') {
                    $html.='<td><a href="'.url('storage/app/public/').'/'.$row['cycle_closure_link_'.$val].'" target="_blank" class="btn btn-sm btn-primary">View</a></td>';
                } else {
                    $html.='<td><a href="#" class="btn btn-sm btn-primary">View</a></td>';
                }       
            }
            $html.='</tr>';
        }
        $html.='</tbody>';
        return $html;    
    }

    public function agency_wise_data_top(Request $request)
    {
        $audit_type=$request->audit_type;
        $data = DB::table('audits')
             ->join('auditor_assigns', function ($join) use ($request) {
                    $join->whereRaw("
                        CASE auditor_assigns.type_of_agency
                            WHEN 'agency' THEN auditor_assigns.agency_id = audits.agency_id
                            WHEN 'branch' THEN auditor_assigns.agency_id = audits.branch_id
                            WHEN 'yard' THEN auditor_assigns.agency_id = audits.yard_id
                            WHEN 'branchrepo' THEN auditor_assigns.agency_id = audits.branch_repo_id
                            WHEN 'agencyrepo' THEN auditor_assigns.agency_id = audits.agency_repo_id
                            WHEN 'yardrepo' THEN auditor_assigns.agency_id = audits.yard_repo_id
                        END
                    ")
                    ->where('auditor_assigns.audit_cycle_id', $request->currentCycleId);
                });
                    if($audit_type != 'all') {
                        $data->leftJoin('qm_sheets', 'audits.qm_sheet_id', '=', 'qm_sheets.id');
                        $data->where('qm_sheets.type', $audit_type);
                    }
        $data=$data->where('audits.audit_cycle_id', $request->currentCycleId)
            ->where('audits.client_id', Auth::user()->client_id)
            ->where('audits.status', '<=', 2)
            ->select(
                'auditor_assigns.final_agency_name as agency_name',      
                DB::raw('ROUND(AVG(CASE WHEN audits.overall_score > 0 THEN audits.overall_score ELSE 0 END), 1) AS audit_score'),
                DB::raw('ROUND(AVG(CASE WHEN audits.score_percentage > 0 THEN audits.score_percentage ELSE 0 END), 1) AS score_percentage')
            )
            ->groupBy('auditor_assigns.final_agency_name')
            ->get()
            ->sortByDesc('score_percentage')
            ->take(5);
            
            
       $stateWiseArray = [];
       $states = [];
        foreach ($data as $state) {
            $states[] = ucfirst(strtolower($state->agency_name));
            $stateWiseArray[] = [
                'name'  => ucfirst(strtolower($state->agency_name)),
                'y'     => $state->score_percentage,
                'color' => '#28a745',
            ];
        }      

        return view('dashboardAjax.agency_wise_data_top', compact('stateWiseArray','states','audit_type'));       
    }

    public function agency_wise_data_bot(Request $request)
    {
        $audit_type=$request->audit_type;
        $data = DB::table('audits')
            ->join('auditor_assigns', function ($join) use ($request) {
                    $join->whereRaw("
                        CASE auditor_assigns.type_of_agency
                            WHEN 'agency' THEN auditor_assigns.agency_id = audits.agency_id
                            WHEN 'branch' THEN auditor_assigns.agency_id = audits.branch_id
                            WHEN 'yard' THEN auditor_assigns.agency_id = audits.yard_id
                            WHEN 'branchrepo' THEN auditor_assigns.agency_id = audits.branch_repo_id
                            WHEN 'agencyrepo' THEN auditor_assigns.agency_id = audits.agency_repo_id
                            WHEN 'yardrepo' THEN auditor_assigns.agency_id = audits.yard_repo_id
                        END
                    ")
                    ->where('auditor_assigns.audit_cycle_id', $request->currentCycleId);
                });
                    if($audit_type != 'all') {
                        $data->leftJoin('qm_sheets', 'audits.qm_sheet_id', '=', 'qm_sheets.id');
                        $data->where('qm_sheets.type', $audit_type);
                    }
            $data=$data->where('audits.audit_cycle_id', $request->currentCycleId)
            ->where('audits.client_id', Auth::user()->client_id)
            ->where('audits.status', '<=', 2)
            ->select(
                'auditor_assigns.final_agency_name as agency_name',     
                DB::raw('ROUND(AVG(CASE WHEN audits.overall_score > 0 THEN audits.overall_score ELSE 0 END), 1) AS audit_score'),
                DB::raw('ROUND(AVG(CASE WHEN audits.score_percentage > 0 THEN audits.score_percentage ELSE 0 END), 1) AS score_percentage')
            )
            ->groupBy('auditor_assigns.final_agency_name')
            ->get()
            ->sortBy('score_percentage')
            ->take(5);
           

        $stateWiseArray = [];
        $states = [];
        foreach ($data as $state) {
            $states[] = ucfirst(strtolower($state->agency_name));
            $stateWiseArray[] = [
            'name'  => ucfirst(strtolower($state->agency_name)),
            'y'     => $state->score_percentage,
            'color' => '#dc3545',
            ];
        }      

        // Pass arrays to the view
        return view('dashboardAjax.agency_wise_data_bot', compact('stateWiseArray','states','audit_type'));
        
    }

    public function pareto_state_wise(Request $request) {
        $audit_type=$request->audit_type;
        $data = DB::table('audits')
            ->join('audit_results', 'audit_results.audit_id', '=', 'audits.id');
                    if($audit_type != 'all') {
                        $data->leftJoin('qm_sheets', 'audits.qm_sheet_id', '=', 'qm_sheets.id');
                        $data->where('qm_sheets.type', $audit_type);
                    }
            $data=$data->join('qm_sheet_parameters', 'audit_results.parameter_id', '=', 'qm_sheet_parameters.id')
             ->join('auditor_assigns', function ($join) use ($request) {
                    $join->whereRaw("
                        CASE auditor_assigns.type_of_agency
                            WHEN 'agency' THEN auditor_assigns.agency_id = audits.agency_id
                            WHEN 'branch' THEN auditor_assigns.agency_id = audits.branch_id
                            WHEN 'yard' THEN auditor_assigns.agency_id = audits.yard_id
                            WHEN 'branchrepo' THEN auditor_assigns.agency_id = audits.branch_repo_id
                            WHEN 'agencyrepo' THEN auditor_assigns.agency_id = audits.agency_repo_id
                            WHEN 'yardrepo' THEN auditor_assigns.agency_id = audits.yard_repo_id
                        END
                    ")
                    ->where('auditor_assigns.audit_cycle_id', $request->currentCycleId);
                })  
            ->where('audits.audit_cycle_id', $request->currentCycleId)
            ->where('audits.client_id', Auth::user()->client_id)
            ->where('audits.status', '<=', 2);
            
            if($request->input('parameter_id') && $request->input('parameter_id') != 'all') {
                $data=$data->where('qm_sheet_parameters.id', $selectedParameterId);
            }

            $data=$data->select(
            'auditor_assigns.state as state_name',
            DB::raw("SUM(CASE WHEN audit_results.option_selected = 'Satisfactory' THEN 1 ELSE 0 END) as compliant_count"),
            DB::raw("SUM(CASE WHEN audit_results.option_selected = 'Unsatisfactory' THEN 1 ELSE 0 END) as non_compliant_count")
            )            
            ->groupBy('auditor_assigns.state')
            ->orderBy('non_compliant_count','desc')
            ->get()
            ->map(function ($row) {
                return [
                    'state_name' => $row->state_name,
                    'compliant_count' => (int) $row->compliant_count,
                    'non_compliant_count' => (float) $row->non_compliant_count
                ];
            })
            ->toArray();  
        
        //echo "<pre>"; print_r($data); die;
        
        return view('dashboardAjax.pareto_state_wise_view',[
            'data' => $data,            
            'currentCycleId' => $request->currentCycleId,
            'audit_type' => $audit_type
        ]);
        
    }

    public function pareto_param_view(Request $request) {
        $audit_type=$request->audit_type;
        $data = DB::table('audits')
            ->join('audit_results', 'audit_results.audit_id', '=', 'audits.id');
                    if($audit_type != 'all') {
                        $data->leftJoin('qm_sheets', 'audits.qm_sheet_id', '=', 'qm_sheets.id');
                        $data->where('qm_sheets.type', $audit_type);
                    }
            $data=$data->join('qm_sheet_parameters', 'audit_results.parameter_id', '=', 'qm_sheet_parameters.id')
             ->join('auditor_assigns', function ($join) use ($request) {
                    $join->whereRaw("
                        CASE auditor_assigns.type_of_agency
                            WHEN 'agency' THEN auditor_assigns.agency_id = audits.agency_id
                            WHEN 'branch' THEN auditor_assigns.agency_id = audits.branch_id
                            WHEN 'yard' THEN auditor_assigns.agency_id = audits.yard_id
                            WHEN 'branchrepo' THEN auditor_assigns.agency_id = audits.branch_repo_id
                            WHEN 'agencyrepo' THEN auditor_assigns.agency_id = audits.agency_repo_id
                            WHEN 'yardrepo' THEN auditor_assigns.agency_id = audits.yard_repo_id
                        END
                    ")
                    ->where('auditor_assigns.audit_cycle_id', $request->currentCycleId);
                })  
            ->where('audits.audit_cycle_id', $request->currentCycleId)
            ->where('audits.client_id', Auth::user()->client_id)
            ->where('audits.status', '<=', 2);
            
            if($request->stateName && $request->stateName != '') {
                $data=$data->where('auditor_assigns.state', $request->stateName);
            }

        $data=$data->select(
            'qm_sheet_parameters.parameter as parameter_name',
            'qm_sheet_parameters.id as parameter_id',
            DB::raw("SUM(CASE WHEN audit_results.option_selected = 'Satisfactory' THEN 1 ELSE 0 END) as compliant_count"),
            DB::raw("SUM(CASE WHEN audit_results.option_selected = 'Unsatisfactory' THEN 1 ELSE 0 END) as non_compliant_count")
            )
            ->groupBy('qm_sheet_parameters.parameter','qm_sheet_parameters.id')
            ->orderBy('non_compliant_count','desc')
            ->get()
            ->map(function ($row) {
                return [
                    'parameter' => $row->parameter_name,                   
                    //'compliant_count' => (int) $row->compliant_count,
                    'non_compliant_count' => (float) $row->non_compliant_count,
                    'parameter_id' => $row->parameter_id
                ];
            })
            ->toArray();  

        $formatted = array_map(function($item) {
            return [$item['parameter'], (int)$item['non_compliant_count'],(int)$item['parameter_id']];
        }, $data);

        return $formatted;
        //return json_encode($formatted, JSON_PRETTY_PRINT);
    }

    public function getCrossTabData(Request $request) {

        $audit_type=$request->match_field_other;
        $currCycle=$request->currentCycleId;
        $match_field=$request->match_field;
        $match_case=$request->match_case;

        $lastSixCycles = DB::table('audit_cycles')
            ->where('client_id', Auth::user()->client_id)
            ->latest('id')
            ->limit(3)
            ->get()
            ->sortBy('id')
            ->pluck('id', 'name');
        
        //echo "<pre>"; print_r($lastSixCycles); die;  [Aug'25] => 4 [Jul'25] => 1

        if($request->match_case == 1 && $request->match_field == 1) {
            $result=$this->getScoreAndCount_1_1($lastSixCycles,$audit_type);
        }  
        
        if($request->match_case == 1 && $request->match_field == 2) {
            $result=$this->getScoreAndCount_1_2($lastSixCycles,$audit_type);
        }  

        if($request->match_case == 1 && $request->match_field == 3) {
            $result=$this->getScoreAndCount_1_3($lastSixCycles,$audit_type);
        }  

        if($request->match_case == 1 && $request->match_field == 4) {
            $result=$this->getScoreAndCount_1_4($lastSixCycles,$audit_type);
        }  

        if($request->match_case == 1 && $request->match_field == 5) {
            $result=$this->getScoreAndCount_1_5($lastSixCycles,$audit_type);
        }  

        if($request->match_case == 1 && $request->match_field == 6) {
            $result=$this->getScoreAndCount_1_6($lastSixCycles,$audit_type);
        } 
        
        if($request->match_case == 1 && $request->match_field == 7) {
            $result=$this->getScoreAndCount_1_7($lastSixCycles,$audit_type);
        } 

        if($request->match_case == 1 && $request->match_field == 8) {
            $result=$this->getScoreAndCount_1_8($lastSixCycles,$audit_type);
        } 

        if($request->match_case == 1 && $request->match_field == 9) {
            $result=$this->getScoreAndCount_1_9($lastSixCycles,$audit_type);
        } 

        if($request->match_case == 1 && $request->match_field == 10) {
            $result=$this->getScoreAndCount_1_10($lastSixCycles,$audit_type);
        } 

        if($request->match_case == 2 && $request->match_field == 1) {
            $result=$this->getRepeatIssueCount_2_1($lastSixCycles,$audit_type);
        } 

        if($request->match_case == 2 && $request->match_field == 2) {
            $result=$this->getRepeatIssueCount_2_2($lastSixCycles,$audit_type);
        } 

        if($request->match_case == 2 && $request->match_field == 3) {
            $result=$this->getRepeatIssueCount_2_3($lastSixCycles,$audit_type);
        } 

        if($request->match_case == 2 && $request->match_field == 4) {
            $result=$this->getRepeatIssueCount_2_4($lastSixCycles,$audit_type);
        } 

        if($request->match_case == 2 && $request->match_field == 5) {
            $result=$this->getRepeatIssueCount_2_5($lastSixCycles,$audit_type);
        } 

        if($request->match_case == 2 && $request->match_field == 6) {
            $result=$this->getRepeatIssueCount_2_6($lastSixCycles,$audit_type);
        } 

        if($request->match_case == 2 && $request->match_field == 7) {
            $result=$this->getRepeatIssueCount_2_7($lastSixCycles,$audit_type);
        } 

        if($request->match_case == 2 && $request->match_field == 8) {
            $result=$this->getRepeatIssueCount_2_8($lastSixCycles,$audit_type);
        } 

        if($request->match_case == 2 && $request->match_field == 9) {
            $result=$this->getRepeatIssueCount_2_9($lastSixCycles,$audit_type);
        } 

        if($request->match_case == 2 && $request->match_field == 10) {
            $result=$this->getRepeatIssueCount_2_10($lastSixCycles,$audit_type);
        } 
        if($request->match_case == 3 && $request->match_field == 4) {
            $result=$this->getActionPlanningCount_3_4($lastSixCycles,$audit_type);
        } 
        if($request->match_case == 3 && $request->match_field == 5) {
            $result=$this->getActionPlanningCount_3_5($lastSixCycles,$audit_type);
        } 
        if($request->match_case == 3 && $request->match_field == 9) {
            $result=$this->getActionPlanningCount_3_9($lastSixCycles,$audit_type);
        } 
                
        return view('dashboardAjax.crosstab',compact('result','match_field','lastSixCycles','match_case'));
        
    }

    private function getActionPlanningCount_3_9($lastSixCycles, $audit_type)
{
    $clientId = Auth::user()->client_id;

    $result = DB::table('audits')
        ->join('audit_results', 'audit_results.audit_id', '=', 'audits.id')
        ->join('audit_cycles', 'audit_cycles.id', '=', 'audits.audit_cycle_id')
        ->leftJoin('audit_closure_artifacts as aca', function ($join) {
            $join->on('aca.audit_id', '=', 'audits.id')
                 ->on('aca.sub_parameter_id', '=', 'audit_results.sub_parameter_id');
        });

    // Audit type filter
    if ($audit_type != 0) {
        $result->leftJoin('qm_sheets', 'audits.qm_sheet_id', '=', 'qm_sheets.id');
        $result->where('qm_sheets.type', $audit_type);
    }

    // Same audit_allocation join as Score function
    $result->join('audit_allocation', function ($join) {
        $join->on('audit_allocation.audit_cycle_id', '=', 'audits.audit_cycle_id')
            ->whereRaw("
                CASE audit_allocation.type_of_agency
                    WHEN 'agency' THEN audit_allocation.agency_id = audits.agency_id
                    WHEN 'branch' THEN audit_allocation.agency_id = audits.branch_id
                    WHEN 'yard' THEN audit_allocation.agency_id = audits.yard_id
                    WHEN 'branchrepo' THEN audit_allocation.agency_id = audits.branch_repo_id
                    WHEN 'agencyrepo' THEN audit_allocation.agency_id = audits.agency_repo_id
                    WHEN 'yardrepo' THEN audit_allocation.agency_id = audits.yard_repo_id
                END
            ");
    });

    $result->whereIn('audits.audit_cycle_id', $lastSixCycles)
        ->where('audits.client_id', $clientId)
        ->where('audits.status', '<=', 2)
        ->where('audit_results.option_selected', 'Unsatisfactory')
        ->select('audit_allocation.final_agency_name as row_name');

    // Cycle-wise Action Planning columns
    foreach ($lastSixCycles as $cycleId => $cycleName) {

        // TOTAL actions
        $result->addSelect(DB::raw("
            SUM(
                CASE 
                    WHEN audit_cycles.id = {$cycleName}
                    THEN 1 ELSE 0
                END
            ) AS total_{$cycleName}
        "));

        // CLOSED actions (Approved)
        $result->addSelect(DB::raw("
            SUM(
                CASE 
                    WHEN audit_cycles.id = {$cycleName}
                         AND aca.approval_status = 'Approved'
                    THEN 1 ELSE 0
                END
            ) AS closed_{$cycleName}
        "));

        // OPEN actions = Total − Closed
        $result->addSelect(DB::raw("
            SUM(
                CASE 
                    WHEN audit_cycles.id = {$cycleName}
                    THEN 1 ELSE 0
                END
            )
            -
            SUM(
                CASE 
                    WHEN audit_cycles.id = {$cycleName}
                         AND aca.approval_status = 'Approved'
                    THEN 1 ELSE 0
                END
            ) AS open_{$cycleName}
        "));
    }

    return $result
        ->groupBy('audit_allocation.final_agency_name')
        ->get();
}

    private function getActionPlanningCount_3_5($lastSixCycles, $audit_type)
{
    $clientId = Auth::user()->client_id;

    $result = DB::table('audits')
        ->join('audit_results', 'audit_results.audit_id', '=', 'audits.id')
        ->join('audit_cycles', 'audit_cycles.id', '=', 'audits.audit_cycle_id')
        ->leftJoin('audit_closure_artifacts as aca', function ($join) {
            $join->on('aca.audit_id', '=', 'audits.id')
                 ->on('aca.sub_parameter_id', '=', 'audit_results.sub_parameter_id');
        });

    // Audit type filter
    if ($audit_type != 0) {
        $result->leftJoin('qm_sheets', 'audits.qm_sheet_id', '=', 'qm_sheets.id');
        $result->where('qm_sheets.type', $audit_type);
    }

    // Auditor assigns join (STATE)
    $result->join('auditor_assigns', function ($join) {
        $join->on('auditor_assigns.audit_cycle_id', '=', 'audits.audit_cycle_id')
            ->whereRaw("
                CASE auditor_assigns.type_of_agency
                    WHEN 'agency' THEN auditor_assigns.agency_id = audits.agency_id
                    WHEN 'branch' THEN auditor_assigns.agency_id = audits.branch_id
                    WHEN 'yard' THEN auditor_assigns.agency_id = audits.yard_id
                    WHEN 'branchrepo' THEN auditor_assigns.agency_id = audits.branch_repo_id
                    WHEN 'agencyrepo' THEN auditor_assigns.agency_id = audits.agency_repo_id
                    WHEN 'yardrepo' THEN auditor_assigns.agency_id = audits.yard_repo_id
                END
            ");
    });

    $result->whereIn('audits.audit_cycle_id', $lastSixCycles)
        ->where('audits.client_id', $clientId)
        ->where('audits.status', '<=', 2)
        ->where('audit_results.option_selected', 'Unsatisfactory')
        ->select('auditor_assigns.state as row_name');

    // Cycle-wise Action Planning columns
    foreach ($lastSixCycles as $cycleId => $cycleName) {

        // TOTAL actions
        $result->addSelect(DB::raw("
            SUM(
                CASE 
                    WHEN audit_cycles.id = {$cycleName}
                    THEN 1 ELSE 0
                END
            ) AS total_{$cycleName}
        "));

        // CLOSED actions (Approved)
        $result->addSelect(DB::raw("
            SUM(
                CASE 
                    WHEN audit_cycles.id = {$cycleName}
                         AND aca.approval_status = 'Approved'
                    THEN 1 ELSE 0
                END
            ) AS closed_{$cycleName}
        "));

        // OPEN actions = Total − Closed
        $result->addSelect(DB::raw("
            SUM(
                CASE 
                    WHEN audit_cycles.id = {$cycleName}
                    THEN 1 ELSE 0
                END
            )
            -
            SUM(
                CASE 
                    WHEN audit_cycles.id = {$cycleName}
                         AND aca.approval_status = 'Approved'
                    THEN 1 ELSE 0
                END
            ) AS open_{$cycleName}
        "));
    }

    return $result
        ->groupBy('auditor_assigns.state')
        ->get();
}


    private function getActionPlanningCount_3_4($lastSixCycles, $audit_type)
{
    $clientId = Auth::user()->client_id;

    $result = DB::table('audits')
        ->join('audit_results', 'audit_results.audit_id', '=', 'audits.id')
        ->join('audit_cycles', 'audit_cycles.id', '=', 'audits.audit_cycle_id')
        ->join('qm_sheets', 'audits.qm_sheet_id', '=', 'qm_sheets.id')
        ->leftJoin('audit_closure_artifacts as aca', function ($join) {
            $join->on('aca.audit_id', '=', 'audits.id')
                 ->on('aca.sub_parameter_id', '=', 'audit_results.sub_parameter_id');
        });

    if ($audit_type != 0) {
        $result->where('qm_sheets.type', $audit_type);
    }

    // Allocation → Region
    $result->join('audit_allocation', function ($join) {
        $join->on('audit_allocation.audit_cycle_id', '=', 'audits.audit_cycle_id')
            ->whereRaw("
                CASE audit_allocation.type_of_agency
                    WHEN 'agency' THEN audit_allocation.agency_id = audits.agency_id
                    WHEN 'branch' THEN audit_allocation.agency_id = audits.branch_id
                    WHEN 'yard' THEN audit_allocation.agency_id = audits.yard_id
                    WHEN 'branchrepo' THEN audit_allocation.agency_id = audits.branch_repo_id
                    WHEN 'agencyrepo' THEN audit_allocation.agency_id = audits.agency_repo_id
                    WHEN 'yardrepo' THEN audit_allocation.agency_id = audits.yard_repo_id
                END
            ");
    });

    $result->join('regions', 'audit_allocation.region_id', '=', 'regions.id')
        ->whereIn('audits.audit_cycle_id', $lastSixCycles)
        ->where('audits.client_id', $clientId)
        ->where('audits.status', '<=', 2)
        ->where('audit_results.option_selected', 'Unsatisfactory')
        ->select('regions.name as row_name');

    foreach ($lastSixCycles as $cycleId => $cycleName) {

        // TOTAL actions
        $result->addSelect(DB::raw("
            SUM(CASE WHEN audit_cycles.id = {$cycleName} THEN 1 ELSE 0 END)
            AS total_{$cycleName}
        "));

        // CLOSED actions
        $result->addSelect(DB::raw("
            SUM(
                CASE 
                    WHEN audit_cycles.id = {$cycleName}
                         AND aca.approval_status = 'Approved'
                    THEN 1 ELSE 0
                END
            ) AS closed_{$cycleName}
        "));

        // OPEN actions
        $result->addSelect(DB::raw("
            SUM(CASE WHEN audit_cycles.id = {$cycleName} THEN 1 ELSE 0 END)
            -
            SUM(
                CASE 
                    WHEN audit_cycles.id = {$cycleName}
                         AND aca.approval_status = 'Approved'
                    THEN 1 ELSE 0
                END
            ) AS open_{$cycleName}
        "));
    }

    return $result->groupBy('regions.name')->get();
}


    private function getScoreAndCount_1_1($lastSixCycles,$audit_type) {
        $result = DB::table('audits');        

        if($audit_type != 0) {
            $result->leftJoin('qm_sheets', 'audits.qm_sheet_id', '=', 'qm_sheets.id');
            $result->where('qm_sheets.type', $audit_type);
        }

        $result=$result->join('audit_cycles', 'audit_cycles.id', '=', 'audits.audit_cycle_id')
                ->whereIn('audits.audit_cycle_id', $lastSixCycles)
                ->where('audits.client_id', Auth::user()->client_id)
                ->where('audits.status', '<=', 2)
                ->select(
                    'audits.audit_cycle_id',
                    'audit_cycles.name as row_name',
                    DB::raw('COUNT(DISTINCT audits.id) as audit_count'),
                    DB::raw('ROUND(AVG(CASE 
                            WHEN audits.overall_score > 0 
                            THEN audits.overall_score 
                            ELSE 0 
                        END), 1) AS audit_score'),
                    DB::raw('ROUND(AVG(CASE 
                            WHEN audits.score_percentage > 0 
                            THEN audits.score_percentage 
                            ELSE 0 
                        END), 1) AS score_percentage')
                )
                ->groupBy('audits.audit_cycle_id', 'audit_cycles.name')
                ->orderBy('audits.audit_cycle_id', 'asc') // show oldest → latest
                ->get();

        return $result;
    }

    private function getScoreAndCount_1_2($lastSixCycles,$audit_type) {

        $result = DB::table('audits')
            ->leftJoin('qm_sheets', 'audits.qm_sheet_id', '=', 'qm_sheets.id')
            ->join('audit_cycles', 'audit_cycles.id', '=', 'audits.audit_cycle_id')
            ->whereIn('audits.audit_cycle_id', $lastSixCycles)
            ->where('audits.client_id', Auth::user()->client_id)
            ->where('audits.status', '<=', 2)
            ->select('qm_sheets.type as row_name');

        // Build pivot columns dynamically
        foreach ($lastSixCycles as $cycleId => $cycleName) {
            $safeName = $cycleName; // remove quotes for alias

            // Audit Count
            $result->addSelect(DB::raw("
                SUM(CASE WHEN audit_cycles.id = {$cycleName} THEN 1 ELSE 0 END) 
                AS `count_{$safeName}`
            "));

            // Audit Score
            $result->addSelect(DB::raw("
                ROUND(AVG(CASE 
                    WHEN audit_cycles.id = {$cycleName} AND audits.score_percentage > 0 
                    THEN audits.score_percentage 
                END),1) 
                AS `score_{$safeName}`
            "));
        }

        $result = $result->groupBy('qm_sheets.type')->get();       

        return $result;
    }

    private function getScoreAndCount_1_3($lastSixCycles,$audit_type) {

        $result = DB::table('audits');

        if ($audit_type != 0) {
            $result->leftJoin('qm_sheets', 'audits.qm_sheet_id', '=', 'qm_sheets.id');
            $result->where('qm_sheets.type', $audit_type);
        }

        $result = $result->join('audit_cycles', 'audit_cycles.id', '=', 'audits.audit_cycle_id')
            ->join('products', 'audits.product_id', '=', 'products.id')
            ->whereIn('audits.audit_cycle_id', $lastSixCycles)
            ->where('audits.client_id', Auth::user()->client_id)
            ->where('audits.status', '<=', 2)
            ->select('products.name as row_name');

        // Build pivot columns dynamically
        foreach ($lastSixCycles as $cycleId => $cycleName) {
            $safeName = $cycleName; // remove quotes/apostrophes

            // Audit Count
            $result->addSelect(DB::raw("
                SUM(CASE WHEN audit_cycles.id = {$cycleName} THEN 1 ELSE 0 END) 
                AS `count_{$safeName}`
            "));

            // Audit Score
            $result->addSelect(DB::raw("
                ROUND(AVG(CASE 
                    WHEN audit_cycles.id = {$cycleName} AND audits.score_percentage > 0 
                    THEN audits.score_percentage 
                END),1) 
                AS `score_{$safeName}`
            "));
        }

        $result = $result->groupBy('products.name')->get();
       // echo "<pre>"; print_r($result); die;
        return $result;
    }

    private function getScoreAndCount_1_4($lastSixCycles,$audit_type) {

        $result = DB::table('audits');

        if ($audit_type != 0) {
            $result->leftJoin('qm_sheets', 'audits.qm_sheet_id', '=', 'qm_sheets.id');
            $result->where('qm_sheets.type', $audit_type);
        }

        // Fix audit_allocation join
        $result->join('audit_allocation', function ($join) {
            $join->on('audit_allocation.audit_cycle_id', '=', 'audits.audit_cycle_id')
                ->whereRaw("
                    CASE audit_allocation.type_of_agency
                        WHEN 'agency' THEN audit_allocation.agency_id = audits.agency_id
                        WHEN 'branch' THEN audit_allocation.agency_id = audits.branch_id
                        WHEN 'yard' THEN audit_allocation.agency_id = audits.yard_id
                        WHEN 'branchrepo' THEN audit_allocation.agency_id = audits.branch_repo_id
                        WHEN 'agencyrepo' THEN audit_allocation.agency_id = audits.agency_repo_id
                        WHEN 'yardrepo' THEN audit_allocation.agency_id = audits.yard_repo_id
                    END
                ");
        });        

        $result = $result->join('regions', 'audit_allocation.region_id', '=', 'regions.id')
            ->join('audit_cycles', 'audit_cycles.id', '=', 'audits.audit_cycle_id')
            ->whereIn('audits.audit_cycle_id', $lastSixCycles) 
            ->where('audits.client_id', Auth::user()->client_id)
            ->where('audits.status', '<=', 2)
            ->select('regions.name as row_name');

        // Build pivot columns dynamically
        foreach ($lastSixCycles as $cycleId => $cycleName) {
            $safeName = $cycleName; // clean cycle name
            // Audit Count
            $result->addSelect(DB::raw("
                SUM(CASE WHEN audit_cycles.id = {$cycleName} THEN 1 ELSE 0 END) AS `count_{$safeName}`
            "));

            // Audit Score
            $result->addSelect(DB::raw("
                ROUND(AVG(CASE 
                    WHEN audit_cycles.id = {$cycleName} AND audits.score_percentage > 0 
                    THEN audits.score_percentage 
                END),1) AS `score_{$safeName}`
            "));
        }

        $result = $result->groupBy('regions.name')->get();
        //echo "<pre>"; print_r($result); die;
        return $result;
    }

    private function getScoreAndCount_1_5($lastSixCycles,$audit_type) {

        $result = DB::table('audits');

        if ($audit_type != 0) {
            $result->leftJoin('qm_sheets', 'audits.qm_sheet_id', '=', 'qm_sheets.id');
            $result->where('qm_sheets.type', $audit_type);
        }

        // Fix audit_allocation join
        $result->join('auditor_assigns', function ($join) {
            $join->on('auditor_assigns.audit_cycle_id', '=', 'audits.audit_cycle_id')
                ->whereRaw("
                    CASE auditor_assigns.type_of_agency
                        WHEN 'agency' THEN auditor_assigns.agency_id = audits.agency_id
                        WHEN 'branch' THEN auditor_assigns.agency_id = audits.branch_id
                        WHEN 'yard' THEN auditor_assigns.agency_id = audits.yard_id
                        WHEN 'branchrepo' THEN auditor_assigns.agency_id = audits.branch_repo_id
                        WHEN 'agencyrepo' THEN auditor_assigns.agency_id = audits.agency_repo_id
                        WHEN 'yardrepo' THEN auditor_assigns.agency_id = audits.yard_repo_id
                    END
                ");
        });        

        $result = $result->join('audit_cycles', 'audit_cycles.id', '=', 'audits.audit_cycle_id')
            ->whereIn('audits.audit_cycle_id', $lastSixCycles) 
            ->where('audits.client_id', Auth::user()->client_id)
            ->where('audits.status', '<=', 2)
            ->select('auditor_assigns.state as row_name');

        // Build pivot columns dynamically
        foreach ($lastSixCycles as $cycleId => $cycleName) {
            $safeName = $cycleName; // clean cycle name
            // Audit Count
            $result->addSelect(DB::raw("
                SUM(CASE WHEN audit_cycles.id = {$cycleName} THEN 1 ELSE 0 END) AS `count_{$safeName}`
            "));

            // Audit Score
            $result->addSelect(DB::raw("
                ROUND(AVG(CASE 
                    WHEN audit_cycles.id = {$cycleName} AND audits.score_percentage > 0 
                    THEN audits.score_percentage 
                END),1) AS `score_{$safeName}`
            "));
        }

        $result = $result->groupBy('auditor_assigns.state')->get();
        //echo "<pre>"; print_r($result); die;
        // dd($result);
        return $result;
    }

    private function getScoreAndCount_1_6($lastSixCycles,$audit_type) {

        $result = DB::table('audits');

        if ($audit_type != 0) {
            $result->leftJoin('qm_sheets', 'audits.qm_sheet_id', '=', 'qm_sheets.id');
            $result->where('qm_sheets.type', $audit_type);
        }

        // Fix audit_allocation join
        $result->join('auditor_assigns', function ($join) {
            $join->on('auditor_assigns.audit_cycle_id', '=', 'audits.audit_cycle_id')
                ->whereRaw("
                    CASE auditor_assigns.type_of_agency
                        WHEN 'agency' THEN auditor_assigns.agency_id = audits.agency_id
                        WHEN 'branch' THEN auditor_assigns.agency_id = audits.branch_id
                        WHEN 'yard' THEN auditor_assigns.agency_id = audits.yard_id
                        WHEN 'branchrepo' THEN auditor_assigns.agency_id = audits.branch_repo_id
                        WHEN 'agencyrepo' THEN auditor_assigns.agency_id = audits.agency_repo_id
                        WHEN 'yardrepo' THEN auditor_assigns.agency_id = audits.yard_repo_id
                    END
                ");
        });        

        $result = $result->join('audit_cycles', 'audit_cycles.id', '=', 'audits.audit_cycle_id')
            ->whereIn('audits.audit_cycle_id', $lastSixCycles) 
            ->where('audits.client_id', Auth::user()->client_id)
            ->where('audits.status', '<=', 2)
            ->select('auditor_assigns.location as row_name');

        // Build pivot columns dynamically
        foreach ($lastSixCycles as $cycleId => $cycleName) {
            $safeName = $cycleName; // clean cycle name
            // Audit Count
           $result->addSelect(DB::raw("
                COUNT(DISTINCT CASE WHEN audit_cycles.id = {$cycleName} THEN audits.id END) 
                AS `count_{$safeName}`
            "));

            // Audit Score
            $result->addSelect(DB::raw("
                ROUND(AVG(CASE 
                    WHEN audit_cycles.id = {$cycleName} AND audits.score_percentage > 0 
                    THEN audits.score_percentage 
                END),1) AS `score_{$safeName}`
            "));
        }

        $result = $result->groupBy('auditor_assigns.location')->get();
        //echo "<pre>"; print_r($result); die;
        return $result;
    }

    private function getScoreAndCount_1_7($lastSixCycles,$audit_type) {

        $result = DB::table('audits')->join('audit_results','audit_results.audit_id', '=', 'audits.id')
                ->join('qm_sheet_parameters', 'audit_results.parameter_id', '=', 'qm_sheet_parameters.id')
                ->join('qm_sheet_sub_parameters', 'audit_results.sub_parameter_id', '=', 'qm_sheet_sub_parameters.id');

        if ($audit_type != 0) {
            $result->leftJoin('qm_sheets', 'audits.qm_sheet_id', '=', 'qm_sheets.id');
            $result->where('qm_sheets.type', $audit_type);
        }

        $result = $result->join('audit_cycles', 'audit_cycles.id', '=', 'audits.audit_cycle_id')            
            ->whereIn('audits.audit_cycle_id', $lastSixCycles)
            ->where('audits.client_id', Auth::user()->client_id)
            ->where('audits.status', '<=', 2)
            ->select('qm_sheet_parameters.parameter as row_name');

        // Build pivot columns dynamically
        foreach ($lastSixCycles as $cycleId => $cycleName) {
            $safeName = $cycleName; // remove quotes/apostrophes
            // Audit Count
            $result->addSelect(DB::raw("
                COUNT(DISTINCT CASE WHEN audit_cycles.id = {$cycleName} THEN audits.id END) 
                AS `count_{$safeName}`
            "));

            // Audit Score
            $result->addSelect(DB::raw("
                ROUND(SUM(CASE WHEN audit_cycles.id = {$cycleName} THEN audit_results.score END) 
                    / NULLIF(SUM(CASE WHEN audit_cycles.id = {$cycleName} THEN qm_sheet_sub_parameters.weight END), 0) 
                    * 100, 1) 
                AS `score_{$safeName}`
            "));

        }

        $result = $result->groupBy('qm_sheet_parameters.parameter')->get();
       // echo "<pre>"; print_r($result); die;
        return $result;
    }

    private function getScoreAndCount_1_8($lastSixCycles,$audit_type) {

        $result = DB::table('audits')->join('audit_results','audit_results.audit_id', '=', 'audits.id')
                ->join('qm_sheet_parameters', 'audit_results.parameter_id', '=', 'qm_sheet_parameters.id')
                ->join('qm_sheet_sub_parameters', 'audit_results.sub_parameter_id', '=', 'qm_sheet_sub_parameters.id');

        if ($audit_type != 0) {
            $result->leftJoin('qm_sheets', 'audits.qm_sheet_id', '=', 'qm_sheets.id');
            $result->where('qm_sheets.type', $audit_type);
        }

        $result = $result->join('audit_cycles', 'audit_cycles.id', '=', 'audits.audit_cycle_id')            
            ->whereIn('audits.audit_cycle_id', $lastSixCycles)
            ->where('audits.client_id', Auth::user()->client_id)
            ->where('audits.status', '<=', 2)
            ->select('qm_sheet_sub_parameters.sub_parameter as row_name');

        // Build pivot columns dynamically
        foreach ($lastSixCycles as $cycleId => $cycleName) {
            $safeName = $cycleName; // remove quotes/apostrophes
            // Audit Count
            $result->addSelect(DB::raw("
                COUNT(DISTINCT CASE WHEN audit_cycles.id = {$cycleName} THEN audits.id END) 
                AS `count_{$safeName}`
            "));

            // Audit Score
            $result->addSelect(DB::raw("
                ROUND(SUM(CASE WHEN audit_cycles.id = {$cycleName} THEN audit_results.score END) 
                    / NULLIF(SUM(CASE WHEN audit_cycles.id = {$cycleName} THEN qm_sheet_sub_parameters.weight END), 0) 
                    * 100, 1) 
                AS `score_{$safeName}`
            "));

        }

        $result = $result->groupBy('qm_sheet_sub_parameters.sub_parameter')->get();
       // echo "<pre>"; print_r($result); die;
        return $result;
    }

    private function getScoreAndCount_1_9($lastSixCycles,$audit_type) {

        $result = DB::table('audits');

        if ($audit_type != 0) {
            $result->leftJoin('qm_sheets', 'audits.qm_sheet_id', '=', 'qm_sheets.id');
            $result->where('qm_sheets.type', $audit_type);
        }

        // Fix audit_allocation join
        $result->join('audit_allocation', function ($join) {
            $join->on('audit_allocation.audit_cycle_id', '=', 'audits.audit_cycle_id')
                ->whereRaw("
                    CASE audit_allocation.type_of_agency
                        WHEN 'agency' THEN audit_allocation.agency_id = audits.agency_id
                        WHEN 'branch' THEN audit_allocation.agency_id = audits.branch_id
                        WHEN 'yard' THEN audit_allocation.agency_id = audits.yard_id
                        WHEN 'branchrepo' THEN audit_allocation.agency_id = audits.branch_repo_id
                        WHEN 'agencyrepo' THEN audit_allocation.agency_id = audits.agency_repo_id
                        WHEN 'yardrepo' THEN audit_allocation.agency_id = audits.yard_repo_id
                    END
                ");
        });        

        $result = $result->join('audit_cycles', 'audit_cycles.id', '=', 'audits.audit_cycle_id')
            ->whereIn('audits.audit_cycle_id', $lastSixCycles) 
            ->where('audits.client_id', Auth::user()->client_id)
            ->where('audits.status', '<=', 2)
            ->select('audit_allocation.final_agency_name as row_name');

        // Build pivot columns dynamically
        foreach ($lastSixCycles as $cycleId => $cycleName) {
            $safeName = $cycleName; // clean cycle name
            // Audit Count
            $result->addSelect(DB::raw("
                SUM(CASE WHEN audit_cycles.id = {$cycleName} THEN 1 ELSE 0 END) AS `count_{$safeName}`
            "));

            // Audit Score
            $result->addSelect(DB::raw("
                ROUND(AVG(CASE 
                    WHEN audit_cycles.id = {$cycleName} AND audits.score_percentage > 0 
                    THEN audits.score_percentage 
                END),1) AS `score_{$safeName}`
            "));
        }

        $result = $result->groupBy('audit_allocation.final_agency_name')->get();
        //echo "<pre>"; print_r($result); die;
        return $result;
    }

    private function getScoreAndCount_1_10($lastSixCycles,$audit_type) {

        $result = DB::table('audits')->join('audit_results','audit_results.audit_id', '=', 'audits.id')
                ->join('qm_sheet_parameters', 'audit_results.parameter_id', '=', 'qm_sheet_parameters.id')
                ->join('qm_sheet_sub_parameters', 'audit_results.sub_parameter_id', '=', 'qm_sheet_sub_parameters.id');

        if ($audit_type != 0) {
            $result->leftJoin('qm_sheets', 'audits.qm_sheet_id', '=', 'qm_sheets.id');
            $result->where('qm_sheets.type', $audit_type);
        }

        $result = $result->join('audit_cycles', 'audit_cycles.id', '=', 'audits.audit_cycle_id')            
            ->whereIn('audits.audit_cycle_id', $lastSixCycles)
            ->where('audits.client_id', Auth::user()->client_id)
            ->where('audits.status', '<=', 2)
            ->where('qm_sheet_sub_parameters.is_regulatory_param',1)
            ->select('qm_sheet_sub_parameters.sub_parameter as row_name');

        // Build pivot columns dynamically
        foreach ($lastSixCycles as $cycleId => $cycleName) {
            $safeName = $cycleName; // remove quotes/apostrophes
            // Audit Count
            $result->addSelect(DB::raw("
                COUNT(DISTINCT CASE WHEN audit_cycles.id = {$cycleName} THEN audits.id END) 
                AS `count_{$safeName}`
            "));

            // Audit Score
            $result->addSelect(DB::raw("
                ROUND(SUM(CASE WHEN audit_cycles.id = {$cycleName} THEN audit_results.score END) 
                    / NULLIF(SUM(CASE WHEN audit_cycles.id = {$cycleName} THEN qm_sheet_sub_parameters.weight END), 0) 
                    * 100, 1) 
                AS `score_{$safeName}`
            "));

        }

        $result = $result->groupBy('qm_sheet_sub_parameters.sub_parameter')->get();
       // echo "<pre>"; print_r($result); die;
        return $result;
    }

    private function getRepeatIssueCount_2_1($lastSixCycles,$audit_type) {
        $clientId = Auth::user()->client_id;

        $result = DB::table('audits')
            ->join('audit_results', 'audit_results.audit_id', '=', 'audits.id')
            ->join('audit_cycles', 'audit_cycles.id', '=', 'audits.audit_cycle_id')
            ->join('qm_sheets', 'audits.qm_sheet_id', '=', 'qm_sheets.id');

        if ($audit_type != 0) {            
            $result->where('qm_sheets.type', $audit_type);
        }

        $result->whereIn('audits.audit_cycle_id', $lastSixCycles)
            ->where('audits.client_id', $clientId)
            ->where('audits.status', '<=', 2)
            ->where('audit_results.option_selected', 'Unsatisfactory')
            ->select('audit_cycles.name as row_name','audit_cycles.id as row_id');

        // For each cycle, add repeat issue count
        foreach ($lastSixCycles as $cycleId => $cycleName) {
            $safeName = $cycleName;
             $result->addSelect(DB::raw("
                                SUM(
                                    CASE 
                                        WHEN audit_cycles.id = {$cycleName} 
                                        THEN (
                                            SELECT COUNT(DISTINCT ar2.audit_id) 
                                            FROM audits a2
                                            JOIN audit_results ar2 ON a2.id = ar2.audit_id
                                            WHERE 
                                                CASE qm_sheets.type
                                                    WHEN 'agency' THEN a2.agency_id = audits.agency_id
                                                    WHEN 'branch' THEN a2.branch_id = audits.branch_id
                                                    WHEN 'yard'   THEN a2.yard_id   = audits.yard_id
                                                    WHEN 'branch_repo' THEN a2.branch_repo_id = audits.branch_repo_id
                                                    WHEN 'agency_repo' THEN a2.agency_repo_id = audits.agency_repo_id
                                                    WHEN 'yard_repo' THEN a2.yard_repo_id   = audits.yard_repo_id
                                                END
                                            AND a2.client_id = {$clientId}
                                            AND a2.status <= 2
                                            AND a2.audit_cycle_id < {$cycleName}
                                            AND ar2.option_selected = 'Unsatisfactory'
                                            AND ar2.sub_parameter_id = audit_results.sub_parameter_id
                                        )
                                        ELSE 0
                                    END
                                ) AS `repeat_{$safeName}`
                            "));
        }

        $result = $result->groupBy('audit_cycles.name','audit_cycles.id')->get();

        return $result;
    }

    private function getRepeatIssueCount_2_2($lastSixCycles,$audit_type) {
        $clientId = Auth::user()->client_id;

        $result = DB::table('audits')
            ->join('audit_results', 'audit_results.audit_id', '=', 'audits.id')
            ->join('audit_cycles', 'audit_cycles.id', '=', 'audits.audit_cycle_id')
            ->join('qm_sheets', 'audits.qm_sheet_id', '=', 'qm_sheets.id');

        if ($audit_type != 0) {            
            $result->where('qm_sheets.type', $audit_type);
        }

        $result->whereIn('audits.audit_cycle_id', $lastSixCycles)
            ->where('audits.client_id', $clientId)
            ->where('audits.status', '<=', 2)
            ->where('audit_results.option_selected', 'Unsatisfactory')
            ->select('qm_sheets.type as row_name');

        // For each cycle, add repeat issue count
        foreach ($lastSixCycles as $cycleId => $cycleName) {
            $safeName = $cycleName;
             $result->addSelect(DB::raw("
                                SUM(
                                    CASE 
                                        WHEN audit_cycles.id = {$cycleName} 
                                        THEN (
                                            SELECT COUNT(DISTINCT ar2.audit_id) 
                                            FROM audits a2
                                            JOIN audit_results ar2 ON a2.id = ar2.audit_id
                                            WHERE 
                                                CASE qm_sheets.type
                                                    WHEN 'agency' THEN a2.agency_id = audits.agency_id
                                                    WHEN 'branch' THEN a2.branch_id = audits.branch_id
                                                    WHEN 'yard'   THEN a2.yard_id   = audits.yard_id
                                                    WHEN 'branch_repo' THEN a2.branch_repo_id = audits.branch_repo_id
                                                    WHEN 'agency_repo' THEN a2.agency_repo_id = audits.agency_repo_id
                                                    WHEN 'yard_repo' THEN a2.yard_repo_id   = audits.yard_repo_id
                                                END
                                            AND a2.client_id = {$clientId}
                                            AND a2.status <= 2
                                            AND a2.audit_cycle_id < {$cycleName}
                                            AND ar2.option_selected = 'Unsatisfactory'
                                            AND ar2.sub_parameter_id = audit_results.sub_parameter_id
                                        )
                                        ELSE 0
                                    END
                                ) AS `repeat_{$safeName}`
                            "));
        }

        $result = $result->groupBy('qm_sheets.type')->get();

        return $result;
    }

    private function getRepeatIssueCount_2_3($lastSixCycles,$audit_type) {

        $clientId = Auth::user()->client_id;

        $result = DB::table('audits')
            ->join('audit_results', 'audit_results.audit_id', '=', 'audits.id')
            ->join('audit_cycles', 'audit_cycles.id', '=', 'audits.audit_cycle_id')
            ->join('products', 'audits.product_id', '=', 'products.id')
            ->join('qm_sheets', 'audits.qm_sheet_id', '=', 'qm_sheets.id'); 

        if ($audit_type != 0) {                      
            $result->where('qm_sheets.type', $audit_type);
        }

        $result->whereIn('audits.audit_cycle_id', $lastSixCycles)
            ->where('audits.client_id', $clientId)
            ->where('audits.status', '<=', 2)
            ->where('audit_results.option_selected', 'Unsatisfactory')
            ->select('products.name as row_name');

        // For each cycle, add repeat issue count
        foreach ($lastSixCycles as $cycleId => $cycleName) {
            $safeName = $cycleName;
             $result->addSelect(DB::raw("
                                SUM(
                                    CASE 
                                        WHEN audit_cycles.id = {$cycleName} 
                                        THEN (
                                            SELECT COUNT(DISTINCT ar2.audit_id) 
                                            FROM audits a2
                                            JOIN audit_results ar2 ON a2.id = ar2.audit_id
                                            WHERE 
                                                CASE qm_sheets.type
                                                    WHEN 'agency' THEN a2.agency_id = audits.agency_id
                                                    WHEN 'branch' THEN a2.branch_id = audits.branch_id
                                                    WHEN 'yard'   THEN a2.yard_id   = audits.yard_id
                                                    WHEN 'branch_repo' THEN a2.branch_repo_id = audits.branch_repo_id
                                                    WHEN 'agency_repo' THEN a2.agency_repo_id = audits.agency_repo_id
                                                    WHEN 'yard_repo' THEN a2.yard_repo_id   = audits.yard_repo_id
                                                END
                                            AND a2.client_id = {$clientId}
                                            AND a2.status <= 2
                                            AND a2.audit_cycle_id < {$cycleName}
                                            AND ar2.option_selected = 'Unsatisfactory'
                                            AND ar2.sub_parameter_id = audit_results.sub_parameter_id
                                        )
                                        ELSE 0
                                    END
                                ) AS `repeat_{$safeName}`
                            "));
        }

        $result = $result->groupBy('products.name')->get();

        return $result;
    }

    private function getRepeatIssueCount_2_4($lastSixCycles,$audit_type) {

        $clientId = Auth::user()->client_id;

        $result = DB::table('audits')
            ->join('audit_results', 'audit_results.audit_id', '=', 'audits.id')
            ->join('audit_cycles', 'audit_cycles.id', '=', 'audits.audit_cycle_id')           
            ->join('qm_sheets', 'audits.qm_sheet_id', '=', 'qm_sheets.id'); 

        if ($audit_type != 0) {                      
            $result->where('qm_sheets.type', $audit_type);
        }

        $result->join('audit_allocation', function ($join) {
            $join->on('audit_allocation.audit_cycle_id', '=', 'audits.audit_cycle_id')
                ->whereRaw("
                    CASE audit_allocation.type_of_agency
                        WHEN 'agency' THEN audit_allocation.agency_id = audits.agency_id
                        WHEN 'branch' THEN audit_allocation.agency_id = audits.branch_id
                        WHEN 'yard' THEN audit_allocation.agency_id = audits.yard_id
                        WHEN 'branchrepo' THEN audit_allocation.agency_id = audits.branch_repo_id
                        WHEN 'agencyrepo' THEN audit_allocation.agency_id = audits.agency_repo_id
                        WHEN 'yardrepo' THEN audit_allocation.agency_id = audits.yard_repo_id
                    END
                ");
        }); 
        
        $result->join('regions', 'audit_allocation.region_id', '=', 'regions.id')->whereIn('audits.audit_cycle_id', $lastSixCycles)
            ->where('audits.client_id', $clientId)
            ->where('audits.status', '<=', 2)
            ->where('audit_results.option_selected', 'Unsatisfactory')
            ->select('regions.name as row_name');

        // For each cycle, add repeat issue count
        foreach ($lastSixCycles as $cycleId => $cycleName) {
            $safeName = $cycleName;
            $result->addSelect(DB::raw("
                                SUM(
                                    CASE 
                                        WHEN audit_cycles.id = {$cycleName} 
                                        THEN (
                                            SELECT COUNT(DISTINCT ar2.audit_id) 
                                            FROM audits a2
                                            JOIN audit_results ar2 ON a2.id = ar2.audit_id
                                            WHERE 
                                                CASE qm_sheets.type
                                                    WHEN 'agency' THEN a2.agency_id = audits.agency_id
                                                    WHEN 'branch' THEN a2.branch_id = audits.branch_id
                                                    WHEN 'yard'   THEN a2.yard_id   = audits.yard_id
                                                    WHEN 'branch_repo' THEN a2.branch_repo_id = audits.branch_repo_id
                                                    WHEN 'agency_repo' THEN a2.agency_repo_id = audits.agency_repo_id
                                                    WHEN 'yard_repo' THEN a2.yard_repo_id   = audits.yard_repo_id
                                                END
                                            AND a2.client_id = {$clientId}
                                            AND a2.status <= 2
                                            AND a2.audit_cycle_id < {$cycleName}
                                            AND ar2.option_selected = 'Unsatisfactory'
                                            AND ar2.sub_parameter_id = audit_results.sub_parameter_id
                                        )
                                        ELSE 0
                                    END
                                ) AS `repeat_{$safeName}`
                            "));
        }

        $result = $result->groupBy('regions.name')->get();

        return $result;
    }

    private function getRepeatIssueCount_2_5($lastSixCycles,$audit_type) {

        $clientId = Auth::user()->client_id;

        $result = DB::table('audits')
            ->join('audit_results', 'audit_results.audit_id', '=', 'audits.id')
            ->join('audit_cycles', 'audit_cycles.id', '=', 'audits.audit_cycle_id')           
            ->join('qm_sheets', 'audits.qm_sheet_id', '=', 'qm_sheets.id'); 

        if ($audit_type != 0) {                      
            $result->where('qm_sheets.type', $audit_type);
        }

        $result->join('audit_allocation', function ($join) {
            $join->on('audit_allocation.audit_cycle_id', '=', 'audits.audit_cycle_id')
                ->whereRaw("
                    CASE audit_allocation.type_of_agency
                        WHEN 'agency' THEN audit_allocation.agency_id = audits.agency_id
                        WHEN 'branch' THEN audit_allocation.agency_id = audits.branch_id
                        WHEN 'yard' THEN audit_allocation.agency_id = audits.yard_id
                        WHEN 'branchrepo' THEN audit_allocation.agency_id = audits.branch_repo_id
                        WHEN 'agencyrepo' THEN audit_allocation.agency_id = audits.agency_repo_id
                        WHEN 'yardrepo' THEN audit_allocation.agency_id = audits.yard_repo_id
                    END
                ");
        }); 
        
        $result->whereIn('audits.audit_cycle_id', $lastSixCycles)
            ->where('audits.client_id', $clientId)
            ->where('audits.status', '<=', 2)
            ->where('audit_results.option_selected', 'Unsatisfactory')
            ->select('audit_allocation.state as row_name');

        // For each cycle, add repeat issue count
        foreach ($lastSixCycles as $cycleId => $cycleName) {
            $safeName = $cycleName;
            $result->addSelect(DB::raw("
                                SUM(
                                    CASE 
                                        WHEN audit_cycles.id = {$cycleName} 
                                        THEN (
                                            SELECT COUNT(DISTINCT ar2.audit_id) 
                                            FROM audits a2
                                            JOIN audit_results ar2 ON a2.id = ar2.audit_id
                                            WHERE 
                                                CASE qm_sheets.type
                                                    WHEN 'agency' THEN a2.agency_id = audits.agency_id
                                                    WHEN 'branch' THEN a2.branch_id = audits.branch_id
                                                    WHEN 'yard'   THEN a2.yard_id   = audits.yard_id
                                                    WHEN 'branch_repo' THEN a2.branch_repo_id = audits.branch_repo_id
                                                    WHEN 'agency_repo' THEN a2.agency_repo_id = audits.agency_repo_id
                                                    WHEN 'yard_repo' THEN a2.yard_repo_id   = audits.yard_repo_id
                                                END
                                            AND a2.client_id = {$clientId}
                                            AND a2.status <= 2
                                            AND a2.audit_cycle_id < {$cycleName}
                                            AND ar2.option_selected = 'Unsatisfactory'
                                            AND ar2.sub_parameter_id = audit_results.sub_parameter_id
                                        )
                                        ELSE 0
                                    END
                                ) AS `repeat_{$safeName}`
                            "));
        }

        $result = $result->groupBy('audit_allocation.state')->get();

        return $result;
    }

    private function getRepeatIssueCount_2_6($lastSixCycles,$audit_type) {

        $clientId = Auth::user()->client_id;

        $result = DB::table('audits')
            ->join('audit_results', 'audit_results.audit_id', '=', 'audits.id')
            ->join('audit_cycles', 'audit_cycles.id', '=', 'audits.audit_cycle_id')           
            ->join('qm_sheets', 'audits.qm_sheet_id', '=', 'qm_sheets.id'); 

        if ($audit_type != 0) {                      
            $result->where('qm_sheets.type', $audit_type);
        }

        $result->join('audit_allocation', function ($join) {
            $join->on('audit_allocation.audit_cycle_id', '=', 'audits.audit_cycle_id')
                ->whereRaw("
                    CASE audit_allocation.type_of_agency
                        WHEN 'agency' THEN audit_allocation.agency_id = audits.agency_id
                        WHEN 'branch' THEN audit_allocation.agency_id = audits.branch_id
                        WHEN 'yard' THEN audit_allocation.agency_id = audits.yard_id
                        WHEN 'branchrepo' THEN audit_allocation.agency_id = audits.branch_repo_id
                        WHEN 'agencyrepo' THEN audit_allocation.agency_id = audits.agency_repo_id
                        WHEN 'yardrepo' THEN audit_allocation.agency_id = audits.yard_repo_id
                    END
                ");
        }); 
        
        $result->whereIn('audits.audit_cycle_id', $lastSixCycles)
            ->where('audits.client_id', $clientId)
            ->where('audits.status', '<=', 2)
            ->where('audit_results.option_selected', 'Unsatisfactory')
            ->select('audit_allocation.location as row_name');

        // For each cycle, add repeat issue count
        foreach ($lastSixCycles as $cycleId => $cycleName) {
            $safeName = $cycleName;
            $result->addSelect(DB::raw("
                                SUM(
                                    CASE 
                                        WHEN audit_cycles.id = {$cycleName} 
                                        THEN (
                                            SELECT COUNT(DISTINCT ar2.audit_id) 
                                            FROM audits a2
                                            JOIN audit_results ar2 ON a2.id = ar2.audit_id
                                            WHERE 
                                                CASE qm_sheets.type
                                                    WHEN 'agency' THEN a2.agency_id = audits.agency_id
                                                    WHEN 'branch' THEN a2.branch_id = audits.branch_id
                                                    WHEN 'yard'   THEN a2.yard_id   = audits.yard_id
                                                    WHEN 'branch_repo' THEN a2.branch_repo_id = audits.branch_repo_id
                                                    WHEN 'agency_repo' THEN a2.agency_repo_id = audits.agency_repo_id
                                                    WHEN 'yard_repo' THEN a2.yard_repo_id   = audits.yard_repo_id
                                                END
                                            AND a2.client_id = {$clientId}
                                            AND a2.status <= 2
                                            AND a2.audit_cycle_id < {$cycleName}
                                            AND ar2.option_selected = 'Unsatisfactory'
                                            AND ar2.sub_parameter_id = audit_results.sub_parameter_id
                                        )
                                        ELSE 0
                                    END
                                ) AS `repeat_{$safeName}`
                            "));
        }

        $result = $result->groupBy('audit_allocation.location')->get();

        return $result;
    }

    private function getRepeatIssueCount_2_7($lastSixCycles,$audit_type) {

        $clientId = Auth::user()->client_id;

        $result = DB::table('audits')
            ->join('audit_results', 'audit_results.audit_id', '=', 'audits.id')
            ->join('audit_cycles', 'audit_cycles.id', '=', 'audits.audit_cycle_id')           
            ->join('qm_sheets', 'audits.qm_sheet_id', '=', 'qm_sheets.id')
            ->join('qm_sheet_parameters', 'audit_results.parameter_id', '=', 'qm_sheet_parameters.id'); 

        if ($audit_type != 0) {                      
            $result->where('qm_sheets.type', $audit_type);
        }

        $result->join('audit_allocation', function ($join) {
            $join->on('audit_allocation.audit_cycle_id', '=', 'audits.audit_cycle_id')
                ->whereRaw("
                    CASE audit_allocation.type_of_agency
                        WHEN 'agency' THEN audit_allocation.agency_id = audits.agency_id
                        WHEN 'branch' THEN audit_allocation.agency_id = audits.branch_id
                        WHEN 'yard' THEN audit_allocation.agency_id = audits.yard_id
                        WHEN 'branchrepo' THEN audit_allocation.agency_id = audits.branch_repo_id
                        WHEN 'agencyrepo' THEN audit_allocation.agency_id = audits.agency_repo_id
                        WHEN 'yardrepo' THEN audit_allocation.agency_id = audits.yard_repo_id
                    END
                ");
        }); 
        
        $result->whereIn('audits.audit_cycle_id', $lastSixCycles)
            ->where('audits.client_id', $clientId)
            ->where('audits.status', '<=', 2)
            ->where('audit_results.option_selected', 'Unsatisfactory')
            ->select('qm_sheet_parameters.parameter as row_name');

        // For each cycle, add repeat issue count
        foreach ($lastSixCycles as $cycleId => $cycleName) {
            $safeName = $cycleName;
            $result->addSelect(DB::raw("
                                SUM(
                                    CASE 
                                        WHEN audit_cycles.id = {$cycleName} 
                                        THEN (
                                            SELECT COUNT(DISTINCT ar2.audit_id) 
                                            FROM audits a2
                                            JOIN audit_results ar2 ON a2.id = ar2.audit_id
                                            WHERE 
                                                CASE qm_sheets.type
                                                    WHEN 'agency' THEN a2.agency_id = audits.agency_id
                                                    WHEN 'branch' THEN a2.branch_id = audits.branch_id
                                                    WHEN 'yard'   THEN a2.yard_id   = audits.yard_id
                                                    WHEN 'branch_repo' THEN a2.branch_repo_id = audits.branch_repo_id
                                                    WHEN 'agency_repo' THEN a2.agency_repo_id = audits.agency_repo_id
                                                    WHEN 'yard_repo' THEN a2.yard_repo_id   = audits.yard_repo_id
                                                END
                                            AND a2.client_id = {$clientId}
                                            AND a2.status <= 2
                                            AND a2.audit_cycle_id < {$cycleName}
                                            AND ar2.option_selected = 'Unsatisfactory'
                                            AND ar2.sub_parameter_id = audit_results.sub_parameter_id
                                        )
                                        ELSE 0
                                    END
                                ) AS `repeat_{$safeName}`
                            "));
        }

        $result = $result->groupBy('qm_sheet_parameters.parameter')->get();

        return $result;
    }

    private function getRepeatIssueCount_2_8($lastSixCycles,$audit_type) {

        $clientId = Auth::user()->client_id;

        $result = DB::table('audits')
            ->join('audit_results', 'audit_results.audit_id', '=', 'audits.id')
            ->join('audit_cycles', 'audit_cycles.id', '=', 'audits.audit_cycle_id')           
            ->join('qm_sheets', 'audits.qm_sheet_id', '=', 'qm_sheets.id')
            ->join('qm_sheet_sub_parameters', 'audit_results.sub_parameter_id', '=', 'qm_sheet_sub_parameters.id');

        if ($audit_type != 0) {                      
            $result->where('qm_sheets.type', $audit_type);
        }

        $result->join('audit_allocation', function ($join) {
            $join->on('audit_allocation.audit_cycle_id', '=', 'audits.audit_cycle_id')
                ->whereRaw("
                    CASE audit_allocation.type_of_agency
                        WHEN 'agency' THEN audit_allocation.agency_id = audits.agency_id
                        WHEN 'branch' THEN audit_allocation.agency_id = audits.branch_id
                        WHEN 'yard' THEN audit_allocation.agency_id = audits.yard_id
                        WHEN 'branchrepo' THEN audit_allocation.agency_id = audits.branch_repo_id
                        WHEN 'agencyrepo' THEN audit_allocation.agency_id = audits.agency_repo_id
                        WHEN 'yardrepo' THEN audit_allocation.agency_id = audits.yard_repo_id
                    END
                ");
        }); 
        
        $result->whereIn('audits.audit_cycle_id', $lastSixCycles)
            ->where('audits.client_id', $clientId)
            ->where('audits.status', '<=', 2)
            ->where('audit_results.option_selected', 'Unsatisfactory')
            ->select('qm_sheet_sub_parameters.sub_parameter as row_name');

        // For each cycle, add repeat issue count
        foreach ($lastSixCycles as $cycleId => $cycleName) {
            $safeName = $cycleName;
            $result->addSelect(DB::raw("
                                SUM(
                                    CASE 
                                        WHEN audit_cycles.id = {$cycleName} 
                                        THEN (
                                            SELECT COUNT(DISTINCT ar2.audit_id) 
                                            FROM audits a2
                                            JOIN audit_results ar2 ON a2.id = ar2.audit_id
                                            WHERE 
                                                CASE qm_sheets.type
                                                    WHEN 'agency' THEN a2.agency_id = audits.agency_id
                                                    WHEN 'branch' THEN a2.branch_id = audits.branch_id
                                                    WHEN 'yard'   THEN a2.yard_id   = audits.yard_id
                                                    WHEN 'branch_repo' THEN a2.branch_repo_id = audits.branch_repo_id
                                                    WHEN 'agency_repo' THEN a2.agency_repo_id = audits.agency_repo_id
                                                    WHEN 'yard_repo' THEN a2.yard_repo_id   = audits.yard_repo_id
                                                END
                                            AND a2.client_id = {$clientId}
                                            AND a2.status <= 2
                                            AND a2.audit_cycle_id < {$cycleName}
                                            AND ar2.option_selected = 'Unsatisfactory'
                                            AND ar2.sub_parameter_id = audit_results.sub_parameter_id
                                        )
                                        ELSE 0
                                    END
                                ) AS `repeat_{$safeName}`
                            "));
        }

        $result = $result->groupBy('qm_sheet_sub_parameters.sub_parameter')->get();

        return $result;
    }

    private function getRepeatIssueCount_2_9($lastSixCycles,$audit_type) {

        $clientId = Auth::user()->client_id;

        $result = DB::table('audits')
            ->join('audit_results', 'audit_results.audit_id', '=', 'audits.id')
            ->join('audit_cycles', 'audit_cycles.id', '=', 'audits.audit_cycle_id')           
            ->join('qm_sheets', 'audits.qm_sheet_id', '=', 'qm_sheets.id');           

        if ($audit_type != 0) {                      
            $result->where('qm_sheets.type', $audit_type);
        }

        $result->join('audit_allocation', function ($join) {
            $join->on('audit_allocation.audit_cycle_id', '=', 'audits.audit_cycle_id')
                ->whereRaw("
                    CASE audit_allocation.type_of_agency
                        WHEN 'agency' THEN audit_allocation.agency_id = audits.agency_id
                        WHEN 'branch' THEN audit_allocation.agency_id = audits.branch_id
                        WHEN 'yard' THEN audit_allocation.agency_id = audits.yard_id
                        WHEN 'branchrepo' THEN audit_allocation.agency_id = audits.branch_repo_id
                        WHEN 'agencyrepo' THEN audit_allocation.agency_id = audits.agency_repo_id
                        WHEN 'yardrepo' THEN audit_allocation.agency_id = audits.yard_repo_id
                    END
                ");
        }); 
        
        $result->whereIn('audits.audit_cycle_id', $lastSixCycles)
            ->where('audits.client_id', $clientId)
            ->where('audits.status', '<=', 2)
            ->where('audit_results.option_selected', 'Unsatisfactory')
            ->select('audit_allocation.final_agency_name as row_name');

        // For each cycle, add repeat issue count
        foreach ($lastSixCycles as $cycleId => $cycleName) {
            $safeName = $cycleName;
            $result->addSelect(DB::raw("
                                SUM(
                                    CASE 
                                        WHEN audit_cycles.id = {$cycleName} 
                                        THEN (
                                            SELECT COUNT(DISTINCT ar2.audit_id) 
                                            FROM audits a2
                                            JOIN audit_results ar2 ON a2.id = ar2.audit_id
                                            WHERE 
                                                CASE qm_sheets.type
                                                    WHEN 'agency' THEN a2.agency_id = audits.agency_id
                                                    WHEN 'branch' THEN a2.branch_id = audits.branch_id
                                                    WHEN 'yard'   THEN a2.yard_id   = audits.yard_id
                                                    WHEN 'branch_repo' THEN a2.branch_repo_id = audits.branch_repo_id
                                                    WHEN 'agency_repo' THEN a2.agency_repo_id = audits.agency_repo_id
                                                    WHEN 'yard_repo' THEN a2.yard_repo_id   = audits.yard_repo_id
                                                END
                                            AND a2.client_id = {$clientId}
                                            AND a2.status <= 2
                                            AND a2.audit_cycle_id < {$cycleName}
                                            AND ar2.option_selected = 'Unsatisfactory'
                                            AND ar2.sub_parameter_id = audit_results.sub_parameter_id
                                        )
                                        ELSE 0
                                    END
                                ) AS `repeat_{$safeName}`
                            "));
        }

        $result = $result->groupBy('audit_allocation.final_agency_name')->get();

        return $result;
    }

    private function getRepeatIssueCount_2_10($lastSixCycles,$audit_type) {

        $clientId = Auth::user()->client_id;

        $result = DB::table('audits')
            ->join('audit_results', 'audit_results.audit_id', '=', 'audits.id')
            ->join('audit_cycles', 'audit_cycles.id', '=', 'audits.audit_cycle_id')           
            ->join('qm_sheets', 'audits.qm_sheet_id', '=', 'qm_sheets.id')
            ->join('qm_sheet_sub_parameters', 'audit_results.sub_parameter_id', '=', 'qm_sheet_sub_parameters.id');

        if ($audit_type != 0) {                      
            $result->where('qm_sheets.type', $audit_type);
        }

        $result->join('audit_allocation', function ($join) {
            $join->on('audit_allocation.audit_cycle_id', '=', 'audits.audit_cycle_id')
                ->whereRaw("
                    CASE audit_allocation.type_of_agency
                        WHEN 'agency' THEN audit_allocation.agency_id = audits.agency_id
                        WHEN 'branch' THEN audit_allocation.agency_id = audits.branch_id
                        WHEN 'yard' THEN audit_allocation.agency_id = audits.yard_id
                        WHEN 'branchrepo' THEN audit_allocation.agency_id = audits.branch_repo_id
                        WHEN 'agencyrepo' THEN audit_allocation.agency_id = audits.agency_repo_id
                        WHEN 'yardrepo' THEN audit_allocation.agency_id = audits.yard_repo_id
                    END
                ");
        }); 
        
        $result->whereIn('audits.audit_cycle_id', $lastSixCycles)
            ->where('audits.client_id', $clientId)
            ->where('audits.status', '<=', 2)
            ->where('audit_results.option_selected', 'Unsatisfactory')
            ->where('qm_sheet_sub_parameters.is_regulatory_param',1)
            ->select('qm_sheet_sub_parameters.sub_parameter as row_name');

        // For each cycle, add repeat issue count
        foreach ($lastSixCycles as $cycleId => $cycleName) {
            $safeName = $cycleName;
            $result->addSelect(DB::raw("
                                SUM(
                                    CASE 
                                        WHEN audit_cycles.id = {$cycleName} 
                                        THEN (
                                            SELECT COUNT(DISTINCT ar2.audit_id) 
                                            FROM audits a2
                                            JOIN audit_results ar2 ON a2.id = ar2.audit_id
                                            WHERE 
                                                CASE qm_sheets.type
                                                    WHEN 'agency' THEN a2.agency_id = audits.agency_id
                                                    WHEN 'branch' THEN a2.branch_id = audits.branch_id
                                                    WHEN 'yard'   THEN a2.yard_id   = audits.yard_id
                                                    WHEN 'branch_repo' THEN a2.branch_repo_id = audits.branch_repo_id
                                                    WHEN 'agency_repo' THEN a2.agency_repo_id = audits.agency_repo_id
                                                    WHEN 'yard_repo' THEN a2.yard_repo_id   = audits.yard_repo_id
                                                END
                                            AND a2.client_id = {$clientId}
                                            AND a2.status <= 2
                                            AND a2.audit_cycle_id < {$cycleName}
                                            AND ar2.option_selected = 'Unsatisfactory'
                                            AND ar2.sub_parameter_id = audit_results.sub_parameter_id
                                        )
                                        ELSE 0
                                    END
                                ) AS `repeat_{$safeName}`
                            "));
        }

        $result = $result->groupBy('qm_sheet_sub_parameters.sub_parameter')->get();

        return $result;
    }
   
}
