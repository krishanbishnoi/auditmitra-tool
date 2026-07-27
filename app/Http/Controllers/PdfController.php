<?php



namespace App\Http\Controllers;



use Illuminate\Http\Request;

// use PDF;

use Barryvdh\Snappy\Facades\SnappyPdf;

use App\Model\State;

use App\Model\City;

use App\Model\Branch;

use App\Model\Branchable;

use App\Agency;

use App\Model\AgencyRepo;

use App\Model\BranchRepo;

use App\Yard;

use App\Audit;

use App\Model\Products;

use Carbon\Carbon;

// use mikehaertl\wkhtmlto\Pdf;

class PdfController extends Controller

{

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index(Request $request)

    {

        $data=$this->getPdfData($request);

        $pdf = SnappyPdf::loadView('pdf.index', $data)->setPaper('a4')->setOrientation('landscape');

        return $pdf->download('report.pdf');

       // echo 'asda';

        // $pdf = new Pdf($this->create($request)->render());

        // $pdf = new Pdf(url('pdf?state=29&branch_id=5'));

        // $pdf->binary = 'C:\wkhtmltopdf\bin\wkhtmltopdf.exe';

        // if (!$pdf->saveAs(public_path('/uploads/test2.pdf'))) {

            // $error = $pdf->getError();

            // dd($error);

            // ... handle error here

        // }

        

        // $pdf = \App::make('dompdf.wrapper');



        // $pdf->loadHTML($this->create($request)->render())->setPaper('a4', 'landscape');

        // $pdf = PDF::loadView('pdf.index', $data)->setPaper('a4', 'landscape');

        // return $pdf->download('sample.pdf');

        

        // return $pdf->stream();

    }



    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create(Request $request)

    {
        $data=$this->getPdfData($request);
       // dd($data);
        return view('pdf.index', $data);

    }

    public function getPdfData(Request $request){
        //print_r($request->all()); die;
        
        $data['state']=State::where('id',$request->state)->first();

        $data['city']=City::where('state_id',$data['state']->id)->get();

        if($request->has('branch_id') && $request->branch_id!='all' && $request->branch_id!=null){

            $data['branch']=Branch::with('city')->whereIn('city_id',$data['city']->pluck('id'))->where('id',$request->branch_id)->get();

        }

        else{

            $data['branch']=Branch::with('city')->whereIn('city_id',$data['city']->pluck('id'))->get();

        }

        $data['product_name']=Products::all()->pluck('name','id');

        // $audit=Audit::with('audit_results')->get();
        $date=$this->getCycle(Carbon::now()->month);
        
        if($request->start_date && $request->end_date) {
            $date['start']=$request->start_date;
            $date['end']=$request->end_date;
        }
        
        $audit=Audit::with('audit_results')->whereBetween('created_at',[$date['start'],$date['end']])->get()->where('product_id','!=',null)->where('product_id','!=',null)->where('collection_manager_id','!=',null);
        // $audit=Audit::with('audit_results')->get()->where('product_id','!=',null)->where('product_id','!=',null)->where('collection_manager_id','!=',null);

        // dd($audit->count(),$audit->where('product_id','!=',null)->where('collection_manager_id','!=',null)->count());
        foreach($data['branch'] as $item){

            $data['collection'][$item->id]=Branchable::with('user')->where('branch_id',$item->id)->where('type','Collection_Manager')->get();

            $data['agency'][$item->id]=Agency::where('branch_id',$item->id)->get();

            $data['branchrepo'][$item->id]=BranchRepo::where('branch_id',$item->id)->get();

            $data['agencyrepo'][$item->id]=AgencyRepo::where('branch_id',$item->id)->get();

            $data['yard'][$item->id]=Yard::where('branch_id',$item->id)->get();

            $data['collectioncount'][$item->id]=0;

            // $data['collectioncount'][$item->id]=$audit->whereIn('collection_manager_id',$data['collection'][$item->id]->pluck('manager_id'))->unique('collection_manager_id')->count();

            $data['agencycount'][$item->id]=$audit->whereIn('agency_id',$data['agency'][$item->id]->pluck('id'))->unique('agency_id')->count();

            $data['branchrepocount'][$item->id]=$audit->whereIn('branch_repo_id',$data['branchrepo'][$item->id]->pluck('id'))->unique('branch_repo_id')->count();

            $data['agencyrepocount'][$item->id]=$audit->whereIn('agency_repo_id',$data['agencyrepo'][$item->id]->pluck('id'))->unique('agency_repo_id')->count();

            $data['yardcount'][$item->id]=$audit->whereIn('yard_id',$data['yard'][$item->id]->pluck('id'))->unique('yard_id')->count();

            $data['product'][$item->id]=Branchable::where('branch_id',$item->id)->get()->pluck('product_id')->unique();

            $data['productAgency'][$item->id]=$audit->whereIn('agency_id',$data['agency'][$item->id]->pluck('id'))->pluck('product_id')->unique();

            $branchProduct=Audit::with('audit_results.sub_parameter_detail','user')

            ->where('branch_id',$item->id)

            ->orWhereIn('agency_id',$data['agency'][$item->id]->pluck('id'))

            ->orWhereIn('branch_repo_id',$data['branchrepo'][$item->id]->pluck('id'))

            ->orWhereIn('agency_repo_id',$data['agencyrepo'][$item->id]->pluck('id'))

            ->orWhereIn('yard_id',$data['yard'][$item->id]->pluck('id'))

            ->get();

            // dd($data);

            $branchProduct=$branchProduct->where('product_id','!=',null);

            $data['date'][$item->id]=['start'=>$branchProduct->min('created_at'),'end'=>$branchProduct->max('created_at')];

            $total=1;

            foreach($branchProduct as $val){

                $report=$this->getReportingManager($item->id,$val->product_id);

                foreach($val->audit_results as $value){

                    if(isset($data['branchProduct'][$item->id][$val->product_id][$val->collection_manager_id])){

                        if($value->option_selected!='N/A'){

                        $data['branchProduct'][$item->id][$val->product_id][$val->collection_manager_id]['scored']=$data['branchProduct'][$item->id][$val->product_id][$val->collection_manager_id]['scored']+(float)$value->score;

                        $data['branchProduct'][$item->id][$val->product_id][$val->collection_manager_id]['scoreable']=$data['branchProduct'][$item->id][$val->product_id][$val->collection_manager_id]['scoreable']+(float)$value->sub_parameter_detail->weight;

                        }

                        else{

                            $data['branchProduct'][$item->id][$val->product_id][$val->collection_manager_id]['scored']=$data['branchProduct'][$item->id][$val->product_id][$val->collection_manager_id]['scored']+0;

                            $data['branchProduct'][$item->id][$val->product_id][$val->collection_manager_id]['scoreable']=$data['branchProduct'][$item->id][$val->product_id][$val->collection_manager_id]['scoreable']+0;

                        }

                        // $data['branchProduct'][$item->id][$val->product_id][$val->collection_manager_id]['weightage']=$data['branchProduct'][$item->id][$val->product_id][$val->collection_manager_id]['weightage']+(int)$value->sub_parameter_detail->weight ?? '0';

                    }

                    else{

                        $data['collectioncount'][$item->id]=$data['collectioncount'][$item->id]+1;

                        $data['branchProduct'][$item->id][$val->product_id][$val->collection_manager_id]=[

                            'collection_manager'=>$val->user->name ?? '',

                            'reporting_manager'=>$report['name'],

                            'role'=>$report['degination'],
                            'base_location'=>$report['base_location'],

                        ];

                        if($value->option_selected!='N/A'){

                            $data['branchProduct'][$item->id][$val->product_id][$val->collection_manager_id]

                            ['scored']=(float)$value->score;

                            $data['branchProduct'][$item->id][$val->product_id][$val->collection_manager_id]

                            ['scoreable']=(float)$value->sub_parameter_detail->weight;

                        }

                        else{

                            $data['branchProduct'][$item->id][$val->product_id][$val->collection_manager_id]

                            ['scored']=0;

                            $data['branchProduct'][$item->id][$val->product_id][$val->collection_manager_id]

                            ['scoreable']=0;

                        }

                    }

                    //chnages
                    if($val->branch_id!=null){
                    
                        if(isset($data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->branch_id])){

                            if($value->option_selected!='N/A'){

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->branch_id]['scored']=

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->branch_id]['scored']+(float)$value->score;

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->branch_id]['scoreable']=

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->branch_id]['scoreable']+(float)$value->sub_parameter_detail->weight;

                            }

                            else{

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->branch_id]['scored']=

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->branch_id]['scored']+0;

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->branch_id]['scoreable']=

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->branch_id]['scoreable']+0; 

                            }

                        }

                        else{

                            if(isset($data['collectionmanager'][$item->id][$val->collection_manager_id]['collection_manager'])==false && isset($data['collectionmanager'][$item->id][$val->collection_manager_id]['collection_manager_empid'])==false){

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]=[

                                    'collection_manager'=>$val->user->name ?? '',

                                    'collection_manager_empid'=>$val->user->employee_id ?? '',
                                    'collection_manager_baselocation'=>$val->user->base_location ?? '',

                                ];

                            }

                            if($value->option_selected!='N/A'){

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->branch_id]=[

                                    'scored'=>(float)$value->score,

                                    'scoreable'=>(float)$value->sub_parameter_detail->weight,

                                ];

                            }

                            else{

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->branch_id]=[

                                    'scored'=>0,

                                    'scoreable'=>0,

                                ];

                            }

                            $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->branch_id]['branch']

                            =$data['product'][$item->id]->where('id',$val->branch_id)->first()->name ?? '';

                            $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->branch_id]['type']='branch';            

                        }

                    }
                   // dd($data);

                    //end chnages

                    if($val->agency_id!=null){

                        if(isset($data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->agency_id])){

                            if($value->option_selected!='N/A'){

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->agency_id]['scored']=

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->agency_id]['scored']+(float)$value->score;

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->agency_id]['scoreable']=

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->agency_id]['scoreable']+(float)$value->sub_parameter_detail->weight;

                            }

                            else{

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->agency_id]['scored']=

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->agency_id]['scored']+0;

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->agency_id]['scoreable']=

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->agency_id]['scoreable']+0; 

                            }

                        }

                        else{

                            if(isset($data['collectionmanager'][$item->id][$val->collection_manager_id]['collection_manager'])==false && isset($data['collectionmanager'][$item->id][$val->collection_manager_id]['collection_manager_empid'])==false){

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]=[

                                    'collection_manager'=>$val->user->name ?? '',

                                    'collection_manager_empid'=>$val->user->employee_id ?? '',
                                    'collection_manager_baselocation'=>$val->user->base_location ?? '',

                                ];

                            }

                            if($value->option_selected!='N/A'){

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->agency_id]=[

                                    'scored'=>(float)$value->score,

                                    'scoreable'=>(float)$value->sub_parameter_detail->weight,

                                ];

                            }

                            else{

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->agency_id]=[

                                    'scored'=>0,

                                    'scoreable'=>0,

                                ];

                            }

                            $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->agency_id]['agency']

                            =$data['agency'][$item->id]->where('id',$val->agency_id)->first()->name ?? '';

                            $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->agency_id]['type']='agency';            

                        }

                    }

                    if($val->branch_repo_id!=null){

                        if(isset($data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->branch_repo_id])){

                            if($value->option_selected!='N/A'){

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->branch_repo_id]['scored']=

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->branch_repo_id]['scored']+(float)$value->score;

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->branch_repo_id]['scoreable']=

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->branch_repo_id]['scoreable']+(float)$value->sub_parameter_detail->weight;

                            }

                            else{

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->branch_repo_id]['scored']=

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->branch_repo_id]['scored']+0;

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->branch_repo_id]['scoreable']=

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->branch_repo_id]['scoreable']+0; 

                            }

                        }

                        else{

                            if(isset($data['collectionmanager'][$item->id][$val->collection_manager_id]['collection_manager'])==false && isset($data['collectionmanager'][$item->id][$val->collection_manager_id]['collection_manager_empid'])==false){

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]=[

                                    'collection_manager'=>$val->user->name ?? '',

                                    'collection_manager_empid'=>$val->user->employee_id ?? '',
                                    'collection_manager_baselocation'=>$val->user->base_location ?? '',

                                ];

                            }

                            

                            if($value->option_selected!='N/A'){

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->branch_repo_id]=[

                                    'scored'=>(float)$value->score,

                                    'scoreable'=>(float)$value->sub_parameter_detail->weight,

                                ];

                            }

                            else{

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->branch_repo_id]=[

                                    'scored'=>0,

                                    'scoreable'=>0,

                                ];

                            }

                            $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->branch_repo_id]['agency']

                            =$data['branchrepo'][$item->id]->where('id',$val->branch_repo_id)->first()->name ?? '';

                            $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->branch_repo_id]['type']='branchRepo';

                            

                        }

                    }

                    if($val->agency_repo_id!=null){

                        if(isset($data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->agency_repo_id])){

                            if($value->option_selected!='N/A'){

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->agency_repo_id]['scored']=

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->agency_repo_id]['scored']+(float)$value->score;

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->agency_repo_id]['scoreable']=

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->agency_repo_id]['scoreable']+(float)$value->sub_parameter_detail->weight;

                            }

                            else{

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->agency_repo_id]['scored']=

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->agency_repo_id]['scored']+0;

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->agency_repo_id]['scoreable']=

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->agency_repo_id]['scoreable']+0; 

                            }

                        }

                        else{

                            if(isset($data['collectionmanager'][$item->id][$val->collection_manager_id]['collection_manager'])==false && isset($data['collectionmanager'][$item->id][$val->collection_manager_id]['collection_manager_empid'])==false){

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]=[

                                    'collection_manager'=>$val->user->name ?? '',

                                    'collection_manager_empid'=>$val->user->employee_id ?? '',
                                    'collection_manager_baselocation'=>$val->user->base_location ?? '',

                                ];

                            }

                            

                            if($value->option_selected!='N/A'){

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->agency_repo_id]=[

                                    'scored'=>(float)$value->score,

                                    'scoreable'=>(float)$value->sub_parameter_detail->weight,

                                ];

                            }

                            else{

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->agency_repo_id]=[

                                    'scored'=>0,

                                    'scoreable'=>0,

                                ];

                            }

                            $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->agency_repo_id]['agency']

                            =$data['agencyrepo'][$item->id]->where('id',$val->agency_repo_id)->first()->name ?? '';

                            $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->agency_repo_id]['type']='agencyrepo';

                            

                        }

                    }

                    if($val->yard_id!=null){

                        if(isset($data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->yard_id])){

                            if($value->option_selected!='N/A'){

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->yard_id]['scored']=

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->yard_id]['scored']+(float)$value->score;

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->yard_id]['scoreable']=

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->yard_id]['scoreable']+(float)$value->sub_parameter_detail->weight;

                            }

                            else{

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->yard_id]['scored']=

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->yard_id]['scored']+0;

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->yard_id]['scoreable']=

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->yard_id]['scoreable']+0; 

                            }

                        }

                        else{

                            if(isset($data['collectionmanager'][$item->id][$val->collection_manager_id]['collection_manager'])==false && isset($data['collectionmanager'][$item->id][$val->collection_manager_id]['collection_manager_empid'])==false){

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]=[

                                    'collection_manager'=>$val->user->name ?? '',

                                    'collection_manager_empid'=>$val->user->employee_id ?? '',
                                    'collection_manager_baselocation'=>$val->user->base_location ?? '',

                                ];

                            }

                            

                            if($value->option_selected!='N/A'){

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->yard_id]=[

                                    'scored'=>(float)$value->score,

                                    'scoreable'=>(float)$value->sub_parameter_detail->weight,

                                ];

                            }

                            else{

                                $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->yard_id]=[

                                    'scored'=>0,

                                    'scoreable'=>0,

                                ];

                            }

                            $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->yard_id]['agency']

                            =$data['yard'][$item->id]->where('id',$val->yard_id)->first()->name ?? '';

                            $data['collectionmanager'][$item->id][$val->collection_manager_id]['product'][$val->product_id][$val->yard_id]['type']='yard';

                            

                        }

                    }

                    

                }

                // dump($data['collectionmanager']);

            }
            //die;

            // dd($data['collectionmanager'][$item->id],$data['product'][$item->id]);

        }

        // dd($data);
        $data['date2']=$date;
        return $data;

    }

    public function getReportingManager($branch_id,$product){

        // $ids=Branchable::where(['branch_id'=>$branch_id,'product_id'=>$product])->get()->pluck('manager_id');

        $ids=Branchable::with('user')->where(['branch_id'=>$branch_id,'product_id'=>$product])->get();

        $email='';

        $degination='';

        if($ids->where('type','Area_Collection_Manager')->count()>0){

            $user=$ids->where('type','Area_Collection_Manager')->first();

            $email=$user->user->name ?? '';
            $base_location=$user->user->base_location ?? '';

            // $degination='Area Collection Manager';

            $degination='ACM';

        }

        elseif($ids->where('type','Regional_Collection_Manager')->count()>0){

            $user=$ids->where('type','Regional_Collection_Manager')->first();

            $email=$user->user->name ?? '';
            $base_location=$user->user->base_location ?? '';

            // $degination='Regional Collection Manager';

            $degination='RCM';

        }

        elseif($ids->where('type','Zonal_Collection_Manager')->count()>0){

            $user=$ids->where('type','Zonal_Collection_Manager')->first();

            $email=$user->user->name ?? '';
            $base_location=$user->user->base_location ?? '';

            // $degination='Zonal Collection Manager';

            $degination='ZCM';

        }

        elseif($ids->where('type','National_Collection_Manager')->count()>0){

            $user=$ids->where('type','National_Collection_Manager')->first();

            $email=$user->user->name ?? '';
            $base_location=$user->user->base_location ?? '';

            // $degination='National Collection Manager';

            $degination='NCM';

        }

        elseif($ids->where('type','Group_Product_Head')->count()>0){

            $user=$ids->where('type','Group_Product_Head')->first();

            $email=$user->user->name ?? '';
            $base_location=$user->user->base_location ?? '';

            // $degination='Group Product Head';

            $degination='GPH';

        }

        else{

            $user=$ids->where('type','Head_of_the_Collections')->first();

            $email=$user->user->name ?? '';
            $base_location=$user->user->base_location ?? '';

            // $degination='Head of the Collections';

            $degination='HOTC';

        }

        // dd($degination,$email);

        return ['name'=>$email,'degination'=>$degination,'base_location'=>$base_location];

    }

    function getCycle($month){

        $startdate='';

        $enddate='';

        $year=Carbon::now()->year;

        $startMonth='';

        $endMonth='';

        if($month>0 && $month<4){

            $startdate=Carbon::parse('1-1-'.$year)->toDateString();

            $enddate=Carbon::parse('31-3-'.$year)->toDateString();

            // $startMonth=0;

            // $endMonth=3;

        }

        else if($month>3 && $month<7){

            $startdate=Carbon::parse('1-4-'.$year)->toDateString();

            $enddate=Carbon::parse('30-6-'.$year)->toDateString();

            // $startMonth=3;

            // $endMonth=6;

        }

        else if($month>6 && $month<10){

            $startdate=Carbon::parse('1-7-'.$year)->toDateString();

            $enddate=Carbon::parse('30-9-'.$year)->toDateString();

            // $startMonth=6;

            // $endMonth=9;

        }

        else if($month>9 && $month<13){

            $startdate=Carbon::parse('1-10-'.$year)->toDateString();

            $enddate=Carbon::parse('31-12-'.$year)->toDateString();

            // $startMonth=9;

            // $endMonth=12;

        }

        return ['start'=>$startdate,'end'=>$enddate];



    }    

}

