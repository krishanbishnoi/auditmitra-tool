<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Audit;
use App\Agency;
use App\Model\Branchable;
use App\Model\Branch;
use App\Yard;
use App\User;
use Mail;
use App\ActionPlan;
use App\ActionPlanSub;
use App\ActionPlanAnswer;
use Crypt;
class SendActionPlan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'action:send-action-plan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Action Plan';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $actionIds=ActionPlanAnswer::all()->pluck('action_id')->toArray();
        $action=ActionPlan::whereNotIn('id',$actionIds)->get();
        foreach($action as $row){
            $audit=Audit::find($row->sheet_id);
            //    $url=route('action.index').'/'.Crypt::encrypt($row->id);
               $url='http://3.12.35.243/audit/action/'.Crypt::encrypt($row->id);
               if($audit!=null && $audit->branch_id!=null){
                $ids= Branchable::where(['branch_id'=>$audit->branch_id,'product_id'=>$audit->product_id])->get(['id','manager_id'])->pluck('manager_id');
               }
               else if($audit!=null && $audit->agency_id!=null){
                $agency=Agency::find($audit->agency_id);
                $ids= Branchable::where(['branch_id'=>$agency->branch_id,'product_id'=>$audit->product_id])->get(['id','manager_id'])->pluck('manager_id');
               }
               else if($audit!=null){
                $agency=Yard::find($audit->agency_id);
                $ids= Branchable::where(['branch_id'=>$agency->branch_id,'product_id'=>$audit->product_id])->get(['id','manager_id'])->pluck('manager_id');
               }
               
            //    $role=$this->sendto($audit->send_to);
            //    $emails=User::whereIn('id',$ids)->role($role)->get(['id','email'])->pluck('email')->toArray();
            //    $emails=User::whereIn('id',$ids)->get(['id','email'])->pluck('email')->toArray();
                $emails=['checkravindra2@mailsac.com'];
                Mail::send('emails.action', ['data' => $audit,'url'=>$url], function ($m) use ($emails) {
                    //$m->from('hello@app.com', 'Your Application');
                    $m->to($emails)->subject('Action Plan');
                });
                print_r($emails);
        }
        
    }
    function sendto($type){
        switch($type){
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
            default :
                return 'Area Collection Manager';
            break;
        }
    }
}
