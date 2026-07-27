
@extends('layouts.master_new')

@section('sh-title')

Automated Report Section

@endsection

@section('sh-detail')

Call

@endsection



@section('content')

<style type="text/css">

#SUMMARY:focus {     

    background-color:tomato;    

}	

#COLLECTION:focus {     

    background-color:tomato;    

}	

#GAPS:focus {     

    background-color:tomato;    

}	

</style>



<div class="row">

	<div class="col-lg-12" style="margin-top:10x">

	</div>

</div>

<?php

$user=Auth::user();

$agencyList=array();

$audit_type=array();

$collectionManagerName="";

$emp_id="";

$allPara=array();

$allColMan=array();

$ukcode=array("A"=>"N","B"=>"N","C"=>"N","D"=>"N","E"=>"N","F"=>"N","G"=>"N");

$cal=array("A"=>array("scored"=>0,"scorable"=>0),"B"=>array("scored"=>0,"scorable"=>0),"C"=>array("scored"=>0,"scorable"=>0),"D"=>array("scored"=>0,"scorable"=>0),"E"=>array("scored"=>0,"scorable"=>0),"F"=>array("scored"=>0,"scorable"=>0),"G"=>array("scored"=>0,"scorable"=>0));


foreach($data as $d) {
    
    
    if(!is_null($d->collection_manager_id) && !empty($d->collection_manager_id) && $d->collection_manager_id != "" && !in_array($d->collection_manager_id,$allColMan)){
        $allColMan[]=$d->collection_manager_id;
    }
    
    
    
	if($d->collection_manager_id == $selecollMan) {
		$collectionManagerName=$d->collectionManagerData->name.' - '.$d->collectionManagerData->employee_id;
		$emp_id=$d->collectionManagerData->employee_id;
		$key="";
		
		
		
		if($d->qmsheet->type == "branch"){
		    if($d->productnew->id == 10 || $d->productnew->id == 3) {
		        $ukcode['A']="Y";
		    }else {
		        $ukcode['B']="Y";
		    }
		     $key='branch_'.$getBranch->name."_".$d->productnew->id;
		    if(!in_array("Branch", $audit_type)) {
				$audit_type[]="Branch";
			}
		}
		
		if($d->qmsheet->type == "branch_repo"){
		    $ukcode['G']="Y";
		    $key='branch_repo_'.$d->branchRepo->name."_".$d->productnew->id;
		    if(!in_array("Branch Repo", $audit_type)) {
				$audit_type[]="Branch Repo";
			}
		}
		
		if($d->qmsheet->type == "agency"){
		    if($d->productnew->id == 10 || $d->productnew->id == 3) {
		        $ukcode['C']="Y";
		    }else {
		        $ukcode['D']="Y";
		    }
		     $key='agency_'.$d->agency->name."_".$d->productnew->id;
		    if(!in_array("Agency", $audit_type)) {
				$audit_type[]="Agency";
			}
		}
		
		if($d->qmsheet->type == "agency_repo"){
		    $ukcode['F']="Y";
		    $key='agency_repo_'.$d->agencyRepo->name."_".$d->productnew->id;
		    if(!in_array("Agency Repo", $audit_type)) {
				$audit_type[]="Agency Repo";
			}
		}
		
		if($d->qmsheet->type == "repo_yard"){
		    $ukcode['E']="Y";
		    $key='repo_yard_'.$d->yard->name."_".$d->productnew->id;
		    if(!in_array("Yard Agency", $audit_type)) {
				$audit_type[]="Yard Agency";
			}
		}
		
	
		
		
			if($d->qmsheet->type == "branch"){
			   
			    $agencyList[$key]['name']=$getBranch->name;
    		    $agencyList[$key]['type']='Branch-'.$d->productnew->name;
    		    
    		    
    		}
    		
    		if($d->qmsheet->type == "branch_repo"){
    		    
    		    $agencyList[$key]['name']=$d->branchRepo->name;
    		    $agencyList[$key]['type']='Branch Repo-'.$d->productnew->name;
    		}
    		
    		if($d->qmsheet->type == "agency"){
    		   
    		    $agencyList[$key]['name']=$d->agency->name;
    		    $agencyList[$key]['type']='Agency-'.$d->productnew->name;
    		}
    		
    		if($d->qmsheet->type == "agency_repo"){
    		    
    		    $agencyList[$key]['name']=$d->agencyRepo->name;
    		    $agencyList[$key]['type']='Agency Repo-'.$d->productnew->name;
    		}
    		
    		if($d->qmsheet->type == "repo_yard"){
    		    
    		    $agencyList[$key]['name']=$d->yard->name;
    		    $agencyList[$key]['type']='Yard Agency-'.$d->productnew->name;
    		}
    		
    		$agencyList[$key]['pro_id']=$d->productnew->id;
    		
    		if(!array_key_exists($key, $agencyList)){
    		    $agencyList[]=$key;
    		}
			
			
			$agencyList[$key]['scored']=0;	
			$agencyList[$key]['scorable']=0;
			foreach($d->audit_parameter_result as $apr) {
				$agencyList[$key]['scored']+=$apr->without_fatal_score;
				$agencyList[$key]['scorable']=$apr->temp_weight;
			}
			
			if($d->qmsheet->type == "branch"){
    		    if($d->productnew->id == 10 || $d->productnew->id == 3) {
    		        $cal['A']['scored']+=$agencyList[$key]['scored'];
    		        $cal['A']['scorable']+=$agencyList[$key]['scorable'];
    		    }else {
    		        $cal['B']['scored']+=$agencyList[$key]['scored'];
    		        $cal['B']['scorable']+=$agencyList[$key]['scorable'];
    		    }
			}
			
			if($d->qmsheet->type == "agency"){
    		    if($d->productnew->id == 10 || $d->productnew->id == 3) {
    		        $cal['C']['scored']+=$agencyList[$key]['scored'];
    		        $cal['C']['scorable']+=$agencyList[$key]['scorable'];
    		    }else {
    		        $cal['D']['scored']+=$agencyList[$key]['scored'];
    		        $cal['D']['scorable']+=$agencyList[$key]['scorable'];
    		    }
			}
			
			if($d->qmsheet->type == "repo_yard"){
		        $cal['E']['scored']+=$agencyList[$key]['scored'];
    		    $cal['E']['scorable']+=$agencyList[$key]['scorable'];
			}
			
			if($d->qmsheet->type == "agency_repo"){
    		    $cal['F']['scored']+=$agencyList[$key]['scored'];
    		    $cal['F']['scorable']+=$agencyList[$key]['scorable'];
			}
			
			if($d->qmsheet->type == "branch_repo"){
		        $cal['G']['scored']+=$agencyList[$key]['scored'];
    		    $cal['G']['scorable']+=$agencyList[$key]['scorable'];
			}
			
			$agencyList[$key]['per']=($agencyList[$key]['scorable'] != 0) ? round(($agencyList[$key]['scored']/$agencyList[$key]['scorable'])*100) : 0;	

			$agencyList[$key]['detail_view']=array();

			$agencyList[$key]['para_view']=array();

			foreach($d->audit_results as $apr) {

				if(!array_key_exists($apr->sub_parameter_id,$agencyList[$key]['para_view'])) {
					$agencyList[$key]['para_view'][$apr->sub_parameter_id]['main_name']=$apr->parameter_detail->parameter;
					$agencyList[$key]['para_view'][$apr->sub_parameter_id]['name']=$apr->sub_parameter_detail->sub_parameter;
					if($apr->option_selected == "Pass" || ($apr->is_percentage == 1 && $apr->selected_per == 100)) {
						$agencyList[$key]['para_view'][$apr->sub_parameter_id]['score']=100;
					}
					if($apr->option_selected == "N/A") {
						$agencyList[$key]['para_view'][$apr->sub_parameter_id]['score']="N/A";
					}
					if($apr->option_selected != "Pass" && $apr->option_selected != "N/A" || ($apr->is_percentage == 1 && $apr->selected_per < 100)) {
						$agencyList[$key]['para_view'][$apr->sub_parameter_id]['score']=0;
					}
					$agencyList[$key]['para_view'][$apr->sub_parameter_id]['per']=0;
					$agencyList[$key]['para_view'][$apr->sub_parameter_id]['hasper']=0;
					
					if($apr->option_selected == "Percentage") {
						$agencyList[$key]['para_view'][$apr->sub_parameter_id]['hasper']=1;
						$agencyList[$key]['para_view'][$apr->sub_parameter_id]['per']=$apr->selected_per;
					}
				}


				if($apr->option_selected == "Fail" || ($apr->option_selected == 'Percentage' && $apr->selected_per != 100)) 
				{   
				    if(!array_key_exists($apr->parameter_id,$agencyList[$key]['detail_view'])) {
					    $agencyList[$key]['detail_view'][$apr->parameter_id]=array();
						$agencyList[$key]['detail_view'][$apr->parameter_id]['remark']=array();
						$agencyList[$key]['detail_view'][$apr->parameter_id]['para_name']=$apr->parameter_detail->parameter;
						if(!array_key_exists($apr->parameter_id, $allPara)) {
							$allPara[$apr->parameter_id]=$apr->parameter_detail->parameter;
						}
					}

					if(array_key_exists($apr->parameter_id,$agencyList[$key]['detail_view'])) {
						$agencyList[$key]['detail_view'][$apr->parameter_id]['remark'][]=$apr->remark;
					}
					
				}

				

			}	
		
	}
}



$final_cal=implode("",$ukcode);
//echo $final_cal; die;
// echo "<pre>"; print_r($audit_type); 
// echo "<pre>"; print_r($agencyList);  die;
$c60="";
$c40="";
$c100="";
$pk="";
foreach($calculation as $c) {
    if($c->uk == $final_cal) {
        $c60=$c->c60;
        $c40=$c->c40;
        $c100=$c->c100;
        $pk=$c->pk;
    }
}

$to_scored=0;
$to_scorb=0;
$typeScore=array();
foreach($audit_type as $at) {
	$ar=array();
	$ar['name']=$at;
	$ar['scored']=0;
	$ar['scorable']=0;
	foreach($agencyList as $al) {
		$explode=explode("-", $al['type']);
		if($explode[0] == $at) {
		 	$ar['scored']+=$al['scored'];
			$ar['scorable']+=$al['scorable'];
			$to_scored+=$al['scored'];
            $to_scorb+=$al['scorable'];
		}
	}
	
	$ar['per']=($ar['scorable'] != 0) ? ($ar['scored']/$ar['scorable'])*100 : 0;
	$typeScore[]=$ar;
}



//echo "<pre>"; print_r($agencyList); die;

$sum_40_score=0;
$sum_40_srob=0;
$sum_60_score=0;
$sum_60_srob=0;
$sum_100_score=0;
$sum_100_srob=0;

if($c40 != "") {
    $e=explode(",",$c40);
    foreach($e as $c) {
        if($c == "A"){
            $sum_40_score+=($cal['A']['scored']/100)*40;
            $sum_40_srob+=($cal['A']['scorable']/100)*40;
        }
        if($c == "B"){
            $sum_40_score+=($cal['B']['scored']/100)*40;
            $sum_40_srob+=($cal['B']['scorable']/100)*40;
        }
        if($c == "C"){
            $sum_40_score+=($cal['C']['scored']/100)*40;
            $sum_40_srob+=($cal['C']['scorable']/100)*40;
        }
        if($c == "D"){
            $sum_40_score+=($cal['D']['scored']/100)*40;
            $sum_40_srob+=($cal['D']['scorable']/100)*40;
        }
        if($c == "E"){
            $sum_40_score+=($cal['E']['scored']/100)*40;
            $sum_40_srob+=($cal['E']['scorable']/100)*40;
        }
        if($c == "F"){
            $sum_40_score+=($cal['F']['scored']/100)*40;
            $sum_40_srob+=($cal['F']['scorable']/100)*40;
        }
        if($c == "G"){
            $sum_40_score+=($cal['G']['scored']/100)*40;
            $sum_40_srob+=($cal['G']['scorable']/100)*40;
        }
    }
}

if($c60 != "") {
    $e=explode(",",$c60);
    foreach($e as $c) {
        if($c == "A"){
            $sum_60_score+=($cal['A']['scored']/100)*60;
            $sum_60_srob+=($cal['A']['scorable']/100)*60;
        }
        if($c == "B"){
            $sum_60_score+=($cal['B']['scored']/100)*60;
            $sum_60_srob+=($cal['B']['scorable']/100)*60;
        }
        if($c == "C"){
            $sum_60_score+=($cal['C']['scored']/100)*60;
            $sum_60_srob+=($cal['C']['scorable']/100)*60;
        }
        if($c == "D"){
            $sum_60_score+=($cal['D']['scored']/100)*60;
            $sum_60_srob+=($cal['D']['scorable']/100)*60;
        }
        if($c == "E"){
            $sum_60_score+=($cal['E']['scored']/100)*60;
            $sum_60_srob+=($cal['E']['scorable']/100)*60;
        }
        if($c == "F"){
            $sum_60_score+=($cal['F']['scored']/100)*60;
            $sum_60_srob+=($cal['F']['scorable']/100)*60;
        }
        if($c == "G"){
            $sum_60_score+=($cal['G']['scored']/100)*60;
            $sum_60_srob+=($cal['G']['scorable']/100)*60;
        }
    }
}

if($c100 != "") {
    $e=explode(",",$c100);
    foreach($e as $c) {
        if($c == "A"){
            $sum_100_score+=$cal['A']['scored'];
            $sum_100_srob+=$cal['A']['scorable'];
        }
        if($c == "B"){
            $sum_100_score+=$cal['B']['scored'];
            $sum_100_srob+=$cal['B']['scorable'];
        }
        if($c == "C"){
            $sum_100_score+=$cal['C']['scored'];
            $sum_100_srob+=$cal['C']['scorable'];
        }
        if($c == "D"){
            $sum_100_score+=$cal['D']['scored'];
            $sum_100_srob+=$cal['D']['scorable'];
        }
        if($c == "E"){
            $sum_100_score+=$cal['E']['scored'];
            $sum_100_srob+=$cal['E']['scorable'];
        }
        if($c == "F"){
            $sum_100_score+=$cal['F']['scored'];
            $sum_100_srob+=$cal['F']['scorable'];
        }
        if($c == "G"){
            $sum_100_score+=$cal['G']['scored'];
            $sum_100_srob+=$cal['G']['scorable'];
        }
    }
}

$sumTotal=0;

$tot_scor=0;
$tot_cor=0;
if($c40 != "") {
    $tot_scor+=$sum_40_score;
    $tot_cor+=$sum_40_srob;
}

if($c60 != "") {
    $tot_scor+=$sum_60_score;
    $tot_cor+=$sum_60_srob;
}

if($c100 != "") {
    $tot_scor+=$sum_100_score;
    $tot_cor+=$sum_100_srob;
}



if($pk == "") {
    $sumTotal=($to_scorb != 0) ? (($to_scored/$to_scorb)*100) :0;
} else {
    $sumTotal=($tot_cor != 0) ? (($tot_scor/$tot_cor)*100) :0;
}


?>







<div class="animated fadeIn">

	@if($has_data == 1)

	<div class="row" >

		<div class="col-lg-12">

			<div class="card">

				<div class="card-header">					

					<img src="{{asset('images/idfc_bank_logo.png')}}">

					<input style="float: right; margin-left: 5px;" id="GAPS" name="GAPS" type="button" class="btn btn-sm btn-secondary" value="MAJOR GAPS" onClick="getDiv('gaps');" />

					<input style="background: tomato; float: right; margin-left: 5px;" id="COLLECTION" name="COLLECTION" type="button" class="btn btn-sm btn-secondary" value="COLLECTION MANAGER" onClick="getDiv('collecMana');" />

					<input style="float: right;" id="SUMMARY" name="SUMMARY" type="button" class="btn btn-sm btn-secondary" value="SUMMARY"  onClick="getDiv('summary');"/>

				</div>

				
				
				<div  id="collecMana" class="card-body card-block">
					<div class="row">
						<div class="col-md-4">
							<label>Select Collection Manager</label>
							<select name="cmanager" id="cmanager" class="form-control" onchange="getPost(this.value);">
								<option value=""> Select Collection Manager </option>
								<?php $alhas=array(); ?>
								@foreach($getBranch->branchable as $br)
									@if($br->status == 2 && !in_array($br->manager_id,$alhas) && in_array($br->manager_id,$allColMan))
									<?php $alhas[]=$br->manager_id; ?>
									<option value="{{$br->manager_id}}" <?php if($selecollMan == $br->manager_id) {echo "selected"; } ?>>{{$br->user->name." - ".$br->user->employee_id}}</option>
									@endif
								@endforeach
							</select>
						</div>
					</div>
					<br/><br/>
					<div class="row">
						<div class="col-md-6">
							<table class="table" border="1">
								<thead style="background:#9D1D27; color:white;">
									<tr>
										<th colspan="2" scope="col">Name Of Collection Manager</th>
										<th colspan="3" scope="col">{{$collectionManagerName}}</th>
									</tr>
									<tr>
										<th colspan="2"></th>
										<th colspan="3"></th>
									</tr>
									<tr>
										<th scope="col">Type</th>
										<th scope="col">Branch Name</th>
										<th scope="col">Scored</th>
										<th scope="col">Scorable</th>
										<th scope="col">Score %</th>
									</tr>
								</thead>
								<tbody>
									@foreach($agencyList as $al)
										<tr>
											<td>{{$al['type']}}</td>
											<td>{{$al['name']}}</td>
											<td>{{$al['scored']}}</td>
											<td>{{$al['scorable']}}</td>
											<td>{{$al['per']}}%</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>

						<div class="col-md-6">
							<table class="table" border="1">
								<thead style="background:#9D1D27; color:white;">
									<tr>
										<th colspan="4" scope="col">{{$collectionManagerName}}</th>
										
									</tr>
									<tr>
										<th colspan="4"></th>
										
									</tr>
									<tr>
										<th scope="col">Audit Type</th>
										<th scope="col">Scored</th>
										<th scope="col">Scorable</th>
										<th scope="col">Score %</th>
									</tr>
								</thead>
								<tbody>
									<?php $scored=0; $scorable=0; ?>
									@foreach($typeScore as $ts)
										<?php $scored+=$ts['scored']; $scorable+=$ts['scorable']; ?>
										<tr>
											<td>{{$ts['name']}}</td>
											<td>{{$ts['scored']}}</td>
											<td>{{$ts['scorable']}}</td>
											<td>{{round($ts['per'])}}%</td>
										</tr>
									@endforeach 
									<tr>
										<td><b>Grand Total</b></td>
										<td>{{$scored}}</td>
										<td>{{$scorable}}</td>
										<td><?=round($sumTotal); ?>%</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="row">
						<div class="col-md-4">
							<button data-target="#modal-fullscreen-xl" style="background:#9D1D27; color:white;" class="btn btn-info" data-toggle="modal">Click here for {{$collectionManagerName}}</button>
						</div>
						
					</div>
					<br/>
					<div class="row">
					    <div class="col-md-12 card">
							<div id="chart" style="height: 400px;">
							</div>
						</div>
					</div>
					<br/>
					<div class="row">
						<div class="col-md-6" style="height:600px;overflow-y: scroll;">
							<table class="table" border="1">
								<thead style="background:#9D1D27; color:white;">
				        			<tr>
				        				<th scope="col">Name Of Agency</th>
				        				<th scope="col">Product</th>
				        				<th scope="col">Collection Manager</th>
				        				<th scope="col">Main Parameter</th>
				        				<th scope="col">Sub Parameter</th>
				        				<th scope="col">Score %</th>
				        			</tr>
			        			</thead>
			        			<tbody>
			        				@foreach($agencyList as $al)
			        				@foreach($al['para_view'] as $pv)
			        				@if($pv['hasper'] == 0)
			        				<tr>
			        					<td><b>{{$al['name']}}</b></td>
			        					<td><b>{{$al['type']}}</b></td>
			        					<td>{{$collectionManagerName}}</td>
			        					<td><b>{{$pv['main_name']}}</b></td>
			        					<td>{{$pv['name']}}</td>
			        					<td><?=($pv['score'] != 'N/A') ? $pv['score'].'%' : $pv['score'] ?></td>
			        				</tr>
			        				@endif
			        				@endforeach
			        				@endforeach
			        			</tbody>
							</table>
						</div>
						<div class="col-md-6" style="height:600px;overflow-y: scroll;">
							<table class="table" border="1">
								<thead style="background:#9D1D27; color:white;">
				        			<tr>
				        				<th scope="col">Branch</th>
				        				<th scope="col">Main Parameter</th>
				        				<th scope="col">Sub Parameter</th>
				        				<th scope="col">Score%</th>
				        			</tr>
			        			</thead>
			        			<tbody>
			        				@foreach($agencyList as $al)
			        				@foreach($al['para_view'] as $pv)
			        				@if($pv['hasper'] == 1)
			        				<tr>
			        					<td><b>{{$al['name']}}</b></td>
			        					<td><b>{{$pv['main_name']}}</b></td>
			        					<td>{{$pv['name']}}</td>
			        					<td>{{$pv['per']}}%</td>
			        				</tr>
			        				@endif
			        				@endforeach
			        				@endforeach
			        			</tbody>
							</table>
						</div>
					</div>
				</div>

			</div>

		</div>

	</div>










	

  </div>

	@endif



</div>

<!-- Modal Fullscreen xl -->

<div class="modal modal-fullscreen-xl" id="modal-fullscreen-xl" tabindex="-1" role="dialog" aria-hidden="true">

  <div class="modal-dialog" role="document">

    <div class="modal-content" style="margin-left:-350px;width:1210px;">

      <div class="modal-header">

           <button type="button" class="close" data-dismiss="modal" aria-label="Close">

          <span aria-hidden="true">&times;</span>

        </button>

      </div>

      <div class="modal-body">

      	 	<div class="row">
    		<div class="col-md-12" style="height:600px;overflow-y: scroll;">
    			<table class="table" border="1">
	        		<thead style="background:#9D1D27; color:white;">
	        			<tr>
	        				<th scope="col">Sr.No.</th>
	        				<th scope="col">Collection Manager</th>
	        				<th scope="col">Emp Code</th>
	        				<th scope="col">Agency/Repo Agency Names</th>
	        				<th scope="col">Product</th>
	        				@foreach($allPara as $p)
	        					<th scope="col">{{$p}}</th>
	        				@endforeach
		            	</tr>		            	
		            </thead>
		            <tbody>	
		            	<?php $m=1; ?>            	
		            	@foreach($agencyList as $al) 
		            		<?php $es=explode("-", $al['type']); ?>
		            		@if($es[0] != "Branch")
		            		<tr>
		            			<td>{{$m}}</td>
		            			<td>{{$collectionManagerName}}</td>
		            			<td>{{$emp_id}}</td>
		            			<td>{{$al['name']}}</td>
		            			<td>{{$al['type']}}</td>
		            			@foreach($allPara as $kn=>$p)
	        						<td>
	        							@foreach($al['detail_view'] as $k=>$dv)
	        								@if($k == $kn)
	        									@foreach($dv['remark'] as $r)
	        										<?="->".$r."<br/>"; ?>
	        									@endforeach
	        								@endif
	        							@endforeach
	        						</td>
	        					@endforeach
		            		</tr>
		            		<?php $m++; ?>
		            		@endif
		            	@endforeach
		            </tbody>
	        	</table>
    		</div>
    	</div>

      </div>

      <div class="modal-footer">

        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>        

      </div>

    </div>

  </div>

</div>

<form method="get" id="coll" action="{{url('/reportAutomationDataColl')}}">
	<input type="hidden" name="branch" value="{{$getBranch->id}}" />
	<input type="hidden" name="start_date" value="{{$start_date}}" />
	<input type="hidden" name="end_date" value="{{$end_date}}" />
</form>

<form method="get" id="coll2" action="{{url('/reportAutomationDataColl')}}">
	<input type="hidden" name="branch" value="{{$getBranch->id}}" />
	<input type="hidden" name="start_date" value="{{$start_date}}" />
	<input type="hidden" name="end_date" value="{{$end_date}}" />
	<input type="hidden" id="cmid" name="cmid" value="{{$selecollMan}}" />
</form>

<form method="get" id="majgap" action="{{url('/reportAutomationDatagap')}}">
	<input type="hidden" name="branch" value="{{$getBranch->id}}" />
	<input type="hidden" name="start_date" value="{{$start_date}}" />
	<input type="hidden" name="end_date" value="{{$end_date}}" />
</form>

<form method="get" action="{{url('/reportAutomationData')}}" id="summ">
	<input type="hidden" name="branch" value="{{$getBranch->id}}" />
	<input type="hidden" name="start_date" value="{{$start_date}}" />
	<input type="hidden" name="end_date" value="{{$end_date}}" />
</form>

@if($has_data == 1)

<script src="https://code.highcharts.com/highcharts.js"></script>

<script src="https://code.highcharts.com/maps/modules/map.js"></script>

<script src="https://code.highcharts.com/maps/modules/exporting.js"></script>

<script src="https://code.highcharts.com/mapdata/countries/in/custom/in-all-disputed.js"></script>

<script src="https://code.highcharts.com/highcharts-more.js"></script>

<script src="https://code.highcharts.com/modules/solid-gauge.js"></script>

<script src="https://code.highcharts.com/modules/exporting.js"></script>

<script src="https://code.highcharts.com/modules/export-data.js"></script>

<script src="https://code.highcharts.com/modules/accessibility.js"></script>

<script src="https://code.highcharts.com/modules/pareto.js"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script type="text/javascript">

Highcharts.chart('chart', {

    chart: {

        type: 'column'

    },

    title: {

        text: 'CM Scores%'

    },

    subtitle: {

        text: ''

    },

    xAxis: {

        categories: [<?php foreach($typeScore as $t) { ?> "<?=$t['name']; ?>", <?php } ?> 'Grand Total'],

        title: {

            text: 'Audit Type'

        }

    },

    yAxis: {

        min: 0,

        max:120,

        title: {

            text: 'Score (%)',

            align: 'high'

        },

        labels: {

            overflow: 'justify'

        },

        visible: false,

    },

    tooltip: {

        valueSuffix: ' %'

    },

    plotOptions: {

        column: {

          dataLabels: {

            color:'black',

            enabled: true,

            format: '{point.y} %'

          }

        },

        series: {
            
            pointPadding: 0.25,

                    point: {

                        events: {

                            click: function () {

                                    //$("#region").val(this.category);

                                    // $("#filterForm").submit();

                            }

                        }

                    }

                }

    },

    legend: {

        layout: 'vertical',

        align: 'right',

        verticalAlign: 'top',

        x: -40,

        y: 80,

        floating: true,

        borderWidth: 1,

        backgroundColor:'#FFFFFF',

        shadow: true

    },

    credits: {

        enabled: false

    },

    series: [{
        
        showInLegend: false, 

        name: 'Score(%)',

        data:[

        	<?php foreach($typeScore as $t) { ?>

            {

                y:<?=round($t['per']); ?>,

            },

        	<?php  } ?>
            
            {

            	y:<?=round($sumTotal); ?>,

            }

        

        ]

        

    }]

});

function getDiv(id){
	if(id == "summary") {
		$("#summ").submit();		
	}
	if(id == "gaps") {
		$("#majgap").submit();		
	}
	if(id == "collecMana") {
		$("#coll").submit();		
	}
}

function getPost(id) {
	$("#cmid").val(id);
	$("#coll2").submit();	
}



</script>

@endif



@endsection