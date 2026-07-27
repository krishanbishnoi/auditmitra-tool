<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\BeatPlans;
use App\Model\BeatPlanSubParts;
use App\Model\Branch;
use Validator;
use Auth;
use Crypt;
use Mail;
use DB;
use App\Agency;
use App\User;
use App\Model\Products;
class BeatPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $beatplan = BeatPlans::with('sub')->get();
        $product = Products::get(['id', 'name']);
        $agency = Agency::get(['id', 'name']);
        $users = User::get(['id', 'name']);
        // dd($beatplan);
        return view('beat_plan.list', compact('beatplan', 'agency', 'product', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $branch=Branch::get(['id', 'name']);
        // added by nisha for collection manager list
        $user = User::role('Quality Auditor')->get();
        $product = Products::get(['id', 'name']);
        $agency = Agency::get(['id', 'name']);
        $users = User::get(['id', 'name']);

        return view('beat_plan.create', compact('user', 'product', 'agency', 'users'));
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
            // 'company_id' => 'required',
            // 'date' => 'required',
            // 'branch_id' => 'required',
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect('beat_plan/create')
                ->with('error', [$validator->errors()->all()])
                ->withInput();
        } else {
            // echo '<pre>'; print_r($request->all()); die;

            // for check beatplan is already create between this date range
            // foreach($request->subs as $key => $value){
            //     $check_exists = BeatPlanSubParts::whereDate('date','>=', $value['date'])->whereDate('to_date','<=',$value['to_date'])->get();
            //     /*if(count($check_exists) > 0){

            //         $validator->errors()->add('error', 'Beat Plan already created between for this date interval');
            //         return redirect()->back()->with('error', [$validator->errors()->all()])->withInput();
            //     }*/
            //     if(empty($value['product'])){
            //         $validator->errors()->add('error', 'Please Select a Product');
            //         return redirect()->back()->with('error', [$validator->errors()->all()])->withInput();
            //     }
            // }

            $new = BeatPlans::create(['name' => $request->name, 'user_id' => Auth::user()->id]);

            // $product = Products::get(['id', 'name']);
            // $agency=Agency::get(['id','name']);

            $data = [
                // 'branch_id'=>$value['branch_id'],
                'date' => $request['date'],
                'to_date' => $request['to_date'],
                'agencies' => $request['agencies'],
                // 'branch_repo'=>$value['branch_repo'],
                // 'agency_repo'=>$value['agency_repo'],
                // 'yard'=>$value['yard'],
                // 'yard_repo'=>$value['yard_repo'],
                'product' => json_encode($request['product']),
                'collection_manager' => $request['collection_manager'],
                'description' => $request['description'],
                'beat_id' => $new->id,
            ];

            // dd($data);

            $new_rc = BeatPlanSubParts::insert($data);
            if ($new_rc) {
                // $data = BeatPlanSubParts::with('branch.branchable')->where('beat_id', $new->id)->get();
                // foreach($data as $item){
                //     $emails=[];
                //     $emails=$item->branch->branchable->pluck('user.email')->toArray();
                //     Mail::send('emails.beatPlan', ['data' => $item], function ($m) use ($emails) {
                //         //$m->from('hello@app.com', 'Your Application');
                //         $m->to($emails)->subject('Beat plan');
                //     });
                // } 
                // $data=[
                //     "name"=>"Abhi",
                //     "mssg"=>"Testing the mail."
                // ];
                // // $agency = Agency::find($request['agencies']);
                // $testEmail = 'abhishek.chakraborty@qdegrees.org';
                // Mail::send('emails.beatPlan', ['data' => $data], function ($m) use ($testEmail) {
                //     $m->to($testEmail)->subject('Test Beat Plan Email');
                // });

            // Ensure the agency exists and has an email
            // if ($agency && $agency->email) {
                // Send an email to the selected agency using the Blade template
                // Mail::send('emails.beatPlan', ['data' => $data], function ($m) use ($agency) {
                //     $m->to($agency->email)->subject('Beat Plan');
                // });
            // }
            }
            return redirect('beat_plan')->with('success', 'Beat Plan created successfully');
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
        $data = BeatPlans::with('sub')->find(Crypt::decrypt($id));
        $branch = Branch::get(['id', 'name']);
        //$agencies=Agency::get(['id', 'name']);
        $user = User::role('Quality Auditor')->get(['id', 'name']);

        $product = Products::get(['id', 'name']);
        $users = User::get(['id', 'name']);
        $agency = Agency::get(['id', 'name']);

        return view('beat_plan.edit', compact('data', 'agency', 'user', 'product','users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){
    
       
    $validator = Validator::make($request->all(), [
        'name' => 'required'
    ]);

   
    if ($validator->fails()) {
        return redirect('beat_plan/create')
            ->with('error', $validator->errors()->all())
            ->withInput();
    }

   
    $id = Crypt::decrypt($id);


    BeatPlans::where('id', $id)->update(['name' => $request->name]);

    // echo '<pre>'; print_r($request->date);die;

    // if (!empty($request->subs) && is_array($request->subs)) {
        
        foreach ($request->subs as $key => $value) {
            BeatPlanSubParts::updateOrCreate(
                ['beat_id' => $id, 'id' => $value['id'] ?? null], 
                [
                    'date' => $value['date'],
                    'to_date' => $value['to_date'],
                    'agencies' => $value['agencies'],
                    'product' => json_encode($value['product']),
                    'collection_manager' => $value['collection_manager'],
                    'description' => $value['description'],
                    'beat_id' => $id,
                ]
            );
        }
    // }

   
    return redirect('beat_plan')->with('success', 'Beat Plan updated successfully');
}


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        BeatPlanSubParts::where('beat_id', Crypt::decrypt($id))->delete();
        BeatPlans::find(Crypt::decrypt($id))->delete();
        return redirect('beat_plan')->with('success', 'Beat plan deleted successfully.');
    }

    //added by nisha for get agencies according to branch

    public function getBranchWiseAgencies($id)
    {
        $setAgency = Agency::select('id', 'name')->where('branch_id', $id)->get()->toArray();
        return response()->json(['status' => true, 'data' => $setAgency]);

    }
}
