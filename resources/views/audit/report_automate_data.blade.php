
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



?>





<?php



if($has_data == 1)

{	$bCount=0;

	$agenC=0;

	$yardC=0;

	

	$q1Scored=0;

	$q1Scorable=0;

	$q2Scored=0;

	$q2Scorable=0;

	$q3Scored=0;

	$q3Scorable=0;

	$q4Scored=0;

	$q4Scorable=0;



	$product=array();

	$tempProAr=array();





	$currentQuart=$auditCycle[0]['id'];

	$prevQuarter=(array_key_exists(1,$auditCycle)) ? $auditCycle[1]['id'] : 0;
	
	$prevQuarter1=(array_key_exists(2,$auditCycle)) ? $auditCycle[2]['id'] : 0;
	
	$prevQuarter2=(array_key_exists(3,$auditCycle)) ? $auditCycle[3]['id'] : 0;

	$branchFinding=array();

	
	$collecCount=0;

	$yardC=0;

	$agenC=0;

	$auCount=0;
	
	$arcParaID=array();
	
	$collCou=array();
	
	$agenCou=array();
	
	$agenRepoCou=array();
	
	$yardCou=array();
	
	

	foreach($data as $d) {
		if($d->qmsheet->type == "branch"){
			if($d->qmsheet->qm_sheet_sub_parameter) {
				foreach($d->qmsheet->qm_sheet_sub_parameter as $sp) {
					if (strpos($sp->sub_parameter, 'ACR') !== false && !in_array($sp->id, $arcParaID)) {
						$arcParaID[]=$sp->id;
					}
				}
			}
		}
		
		if($d->collection_manager_id != 0 && !is_null($d->collection_manager_id) && !in_array($d->collection_manager_id,$collCou)){
		    $collCou[]=$d->collection_manager_id;
		}
		
		if($d->agency_id != 0 && !is_null($d->agency_id) && !in_array($d->agency_id,$agenCou)){
		    $agenCou[]=$d->agency_id;
		}
		
		if($d->agency_repo_id != 0 && !is_null($d->agency_repo_id) && !in_array($d->agency_repo_id,$agenRepoCou)){
		    $agenRepoCou[]=$d->agency_repo_id;
		}
		
		if($d->yard_id != 0 && !is_null($d->yard_id) && !in_array($d->yard_id,$yardCou)){
		    $yardCou[]=$d->yard_id;
		}
		
		
		
	}
	
    $startAuDate="";
    $endAuDate="";
	$n=0;
	foreach($data as $d) {
	    
	        $auCount++;

		    if($n==0){
		        $endAuDate=$d->audit_date_by_aud;
		    }
		    
		    $pp=count($data)-1;
		    
		    if($n==$pp) {
		        $startAuDate=$d->audit_date_by_aud;
		    }

			$p=$d->product_id;	

			if(!is_null($d->product_id) && !in_array($d->product_id, $tempProAr)) {

				$product[$p]['id']=$d->product_id;

				$product[$p]['name']=$d->productnew->name;
				
				$product[$p]['is_recovery']=$d->productnew->is_recovery;
				
				$product[$p]['bucket']=$d->productnew->bucket;	

				$product[$p]['branch_and_repo_scored']=0;

				$product[$p]['branch_and_repo_scorable']=0;

				$product[$p]['agency_and_repo_scored']=0;

				$product[$p]['agency_and_repo_scorable']=0;

				$product[$p]['current_wave_scored']=0;

				$product[$p]['current_wave_scorable']=0;

				$product[$p]['pre_wave_scored']=0;

				$product[$p]['pre_wave_scorable']=0;
				
				$product[$p]['pre_wave_static_scorable']=0;

				$product[$p]['finding']=array();

				$product[$p]['acrfinding']=array();

				
				$tempProAr[]=$d->product_id;		

			}

			$month = date("n",strtotime($d->created_at));
			//echo $month; die;

			if($d->qmsheet->type == "branch")

			{	

				

				

				$yardC=count($d->branch->yard);

				$agenC=count($d->branch->agency);

				$collecCount=count($d->branch->branchableCollection);

				$RMName="";

				if($d->branch->branchable) {



					$getRid=0;				

					foreach($d->branch->branchable as $br) {

						

						if($d->branch->manager_id == $br->manager_id) {

							if(!is_null($br->acm_id) && $getRid == 0) {

								$getRid=$br->acm_id;

							}

							if(!is_null($br->zcm_id) && $getRid == 0) {

								$getRid=$br->zcm_id;

							}

							if(!is_null($br->rcm_id) && $getRid == 0) {

								$getRid=$br->rcm_id;

							}

							if(!is_null($br->ncm_id) && $getRid == 0) {

								$getRid=$br->ncm_id;

							}

							if(!is_null($br->gph_id) && $getRid == 0) {

								$getRid=$br->gph_id;

							}						

						}

					}



					foreach($d->branch->branchable as $br) {

						if($getRid == $br->manager_id) {

							$RMName=$br->type;						

						}

					}

				}

						

			}
			
			
			if($d->qmsheet->type == "branch" || $d->qmsheet->type == "branch_repo")

			{	
    			foreach($d->audit_results as $ar) {
    
    				if(in_array($ar->sub_parameter_id, $arcParaID)) {
    
    					if(!is_null($ar->remark) && !in_array(trim(strtolower($ar->remark)),$product[$p]['acrfinding'])) {
    						$product[$p]['acrfinding'][]=trim(strtolower($ar->remark));
    					}	
    				}
    
    				if($ar->option_selected == "Fail" || ($ar->option_selected == 'Percentage' && $ar->selected_per != 100)) {
    
    					$string=trim($d->collectionManagerData->name).'/'.$d->productnew->name.'/'.$d->qmsheet->type.'/'.trim($ar->remark);										
    
    					if(!in_array($string,$branchFinding)) {
    
    						$branchFinding[]=$string;
    
    					}
    
    					if($ar->remark != "" && array_key_exists($d->product_id, $product) && !in_array(trim(strtolower($ar->remark)), $product[$d->product_id]['finding'])) {
    						$product[$d->product_id]['finding'][]=trim(strtolower($ar->remark));
    					}				
    
    				}
    
    			}
			
			}

			$yearQuarter = ceil($month / 3);

			$ovWeight=0;

			$ovScored=0;
			
			$ov_s=0;
			$ovc=0;
			
			
			    foreach($d->audit_parameter_result as $ap) {
    			    $ov_s+=$ap->without_fatal_score;
    			    $ovc=$ap->temp_weight;
    			}
			
			
			
			
			// temprorary code applied for statis scores
			
			if($d->productnew->is_recovery == 0) {
    			$q1Scored+=$ov_s;
    
    			$q1Scorable+=$ovc;
			}
			
			
			if($currentQuart == $d->audit_cycle_id) {
    				    
			 //   $q1Scored+=$ov_s;

				// $q1Scorable+=$ovc;
    
    		}
    		
    		if($prevQuarter == $d->audit_cycle_id) {
    
				// $q2Scored+=$ov_s;

				// $q2Scorable+=$ovc;

			}
    
			if($prevQuarter1 == $d->audit_cycle_id) {

			 //    $q3Scored+=$ov_s;

				// $q3Scorable+=$ovc;
				
			}

			if($prevQuarter2 == $d->audit_cycle_id) {

				// $q4Scored+=$ov_s;

				// $q4Scorable+=$ovc;

			}
            
		
                
           

			$ovScored+=$ov_s;
			
			
			
			if($p != "" && !is_null($d->product_id) && array_key_exists($p, $product) && (!is_null($d->branch_id) || !is_null($d->branch_repo_id))) {

				$product[$p]['branch_and_repo_scored']+=$ovScored;

				$product[$p]['branch_and_repo_scorable']+=$ovc;

			}
			if(!is_null($d->product_id) && array_key_exists($p, $product) && (!is_null($d->agency_id) || !is_null($d->agency_repo_id) || !is_null($d->yard_id))) {

				$product[$p]['agency_and_repo_scored']+=$ovScored;

				$product[$p]['agency_and_repo_scorable']+=$ovc;

			}
			
			// Commented for static scores
			
// 			if(!is_null($d->product_id) && array_key_exists($p, $product) && $d->audit_cycle_id == $currentQuart) {

// 				$product[$p]['current_wave_scored']+=$ovScored;

// 				$product[$p]['current_wave_scorable']+=$ovc;

// 			}
			
			if(!is_null($d->product_id) && array_key_exists($p, $product)) {

				$product[$p]['current_wave_scored']+=$ovScored;

				$product[$p]['current_wave_scorable']+=$ovc;

			}
			if(!is_null($d->product_id) && array_key_exists($p, $product) && $d->audit_cycle_id == $prevQuarter) {

				$product[$p]['pre_wave_scored']+=$ovScored;

				$product[$p]['pre_wave_scorable']+=$ovc;

			}
			
			if(!is_null($d->product_id) && array_key_exists($p, $product)) {
			    foreach($proWiseSco as $prws){
			        if($p == $prws->product_id) {
			            $product[$p]['pre_wave_static_scorable']=$prws->previous_1;
			        } 
			    }
			}
		
            $n++;
	}

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

					<input style="float: right; margin-left: 5px;" id="COLLECTION" name="COLLECTION" type="button" class="btn btn-sm btn-secondary" value="COLLECTION MANAGER" onClick="getDiv('collecMana');" />

					<input style="float: right; background: tomato;" id="SUMMARY" name="SUMMARY" type="button" class="btn btn-sm btn-secondary" value="SUMMARY"  onClick="getDiv('summary');"/>

				</div>

				<div id="summary" class="card-body card-block">

					<div class="row">

						<div class="col-md-4">

							<h4 style="color:red;">{{$getBranch->city->state->name}} – Branch Covered</h4>

						</div>

					</div>

					<br/>

					<div class="row">

						<div class="col-md-2">

							<h5 style="color:red;"><img src="{{asset('images/location.jpg')}}" height="50px" width="50px" />

							{{$getBranch->city->name}}</h5>							

						</div>

						<div class="col-md-2">

							<div style="display:block; margin-left:0px; border-radius: 20px; background: #d9d9d9;color: black; padding: 5px; height:170px; font-family: calibri !important;">

								<b>Branch Address:</b><br/>{{$getBranch->address}}

							</div>

						</div>

						<div class="col-md-2">

							<div style="background: #ff704d; display:block; border-radius: 20px; margin-left:0px; color: black; height:170px; padding: 5px; font-family: calibri !important;">

								<b>Location:</b><br/>
								{{$getBranch->location}}

							</div>

						</div>

						<div class="col-md-2">

							<div style="background: #999999;display:block;border-radius: 20px; margin-left:0px; color: black;height:170px; padding: 5px; font-family: calibri !important;">

								<b>Stake Holders:</b><br/>
									Branch office<br/>
									Collection Agency<br/>
									Yard Agency<br/>
									Repo Agency
							</div>

						</div>

						<div class="col-md-2">

							<div style="background: #ffcc00;display:block; border-radius: 20px; margin-left:0px; color: black; height:170px; padding: 5px; font-family: calibri !important;">

								<b>Audit Dates:</b><br/>
								{{$startAuDate}}  to {{$endAuDate}}
							</div>

						</div>

						<div class="col-md-2">

							<div style="background: #668cff;display:block;border-radius: 20px; margin-left:0px; color: black;height:170px; padding: 5px; font-family: calibri !important;">

								<b>Audits Performed:</b><br/>

								

									{{count($collCou)}} Collection Manager<br/>

									{{count($agenCou)}} Agencies<br/>
									
									{{count($agenRepoCou)}} Agency Repo<br/>

									{{count($yardCou)}} Yard<br/>

									{{$auCount}} Audits					

							</div>

						</div>

					</div>

					<br/>

					<div class="row">

						<div class="col-md-6" >

							<div id="zone_wise_chart_1" >

								

							</div>

						</div>

						<div class="col-md-6" style="height: 500px; overflow-y: scroll;">

							<table class="table">

							  <thead style="background:#9D1D27; color:white;">

							    <tr>

							      <th scope="col">Product Type</th>

							      <th scope="col">Product</th>

							      <th scope="col">Branch & Branch Repo</th>

							      <th scope="col">Agency & Agency Repo & Yard</th>

							      <th scope="col">Overall Score - Current Cycle</th>

							      <th scope="col">Previous Cycle</th>

							      <th scope="col">Delta</th>

							    </tr>

							  </thead>

							  <tbody>

							  	<?php 

							  		$brscor=0;

							  		$brscorab=0;

							  		$agencyscor=0;

							  		$agencyScoab=0;

							  		$ovcurrentWaveSco=0;

							  		$ovCurrentWaveScorab=0;

							  		$ovpreWaveSco=0;

							  		$ovpreWaveScorab=0;

							  		$delta=0;
							  		
							  		$hasFlow=array();

							  	?>

							  	@foreach($product as $pro)



							  	<?php 



							  		if($pro['is_recovery'] == 0) {

                                    $hasFlow[]=$pro['id'];
                                    
							  	   	$brscor+=$pro['branch_and_repo_scored'];

							  		$brscorab+=$pro['branch_and_repo_scorable'];

							  		$agencyscor+=$pro['agency_and_repo_scored'];

							  		$agencyScoab+=$pro['agency_and_repo_scorable'];

							  		$ovCurrentWaveScorab+=$pro['current_wave_scorable'];

							  		$ovpreWaveScorab+=1;

							  		

							  	?>

							    <tr>

							      <td>Flow</td>

							      <td>{{$pro['name']}}</td>

							      <td><?=($pro['branch_and_repo_scorable'] != 0) ? round(($pro['branch_and_repo_scored']/$pro['branch_and_repo_scorable'])*100) : 0 ; ?>%</td>

							      <td><?=($pro['agency_and_repo_scorable'] != 0) ? round(($pro['agency_and_repo_scored']/$pro['agency_and_repo_scorable'])*100) : 0 ; ?>%</td>

							      <td><?php echo $sco1=($pro['current_wave_scorable'] != 0) ? round(($pro['current_wave_scored']/$pro['current_wave_scorable'])*100) : 0 ; ?>%</td>

							      <!--<td><?php //echo $sco2=($pro['pre_wave_scorable'] != 0) ? round(($pro['pre_wave_scored']/$pro['pre_wave_scorable'])*100) : 0 ; ?>%</td> -->
							      
							      <?php $sco2=$pro['pre_wave_static_scorable']; ?>
							      
							      <td><?=$sco2?>%</td>
							      
							      <?php $ovcurrentWaveSco+=$pro['current_wave_scored']; $ovpreWaveSco+=$sco2; ?>

							      <td><?php echo $del=($sco1-$sco2); ?>%</td>

							    </tr>

								<?php } ?>

							    @endforeach	

						    	<tr style="background: #F8CBAD;">

						    		<th  scope="row">Flow Total</th>

						    		<td></td>

						    		<td><?=($brscorab != 0) ? round(($brscor/$brscorab)*100) : 0 ; ?>%</td>

						    		<td><?=($agencyScoab != 0) ? round(($agencyscor/$agencyScoab)*100) : 0 ; ?>%</td>

						    		<td><?=($ovCurrentWaveScorab != 0) ? round(($ovcurrentWaveSco/$ovCurrentWaveScorab)*100) : 0 ; ?>%</td>

						    		<td><?=($ovpreWaveScorab != 0) ? round(($ovpreWaveSco/$ovpreWaveScorab)) : 0 ; ?>%</td>
                                    
                                    <?php 
                                        $sc=($ovCurrentWaveScorab != 0) ? round(($ovcurrentWaveSco/$ovCurrentWaveScorab)*100) : 0 ;
                                        $sco=($ovpreWaveScorab != 0) ? round(($ovpreWaveSco/$ovpreWaveScorab)) : 0 ;
                                        $delta=$sc-$sco;
                                    ?>
						    		<td><?php echo $delta; ?>%</td>

						    	</tr>



						    	<?php 

							  		$brscor=0;

							  		$brscorab=0;

							  		$agencyscor=0;

							  		$agencyScoab=0;

							  		$ovcurrentWaveSco=0;

							  		$ovCurrentWaveScorab=0;

							  		$ovpreWaveSco=0;

							  		$ovpreWaveScorab=0;

							  		$delta=0;

							  	?>



						    	@foreach($product as $pro)



							  	<?php 



							  		if(!in_array($pro['id'],$hasFlow)) {

							  			

							  	   	$brscor+=$pro['branch_and_repo_scored'];

							  		$brscorab+=$pro['branch_and_repo_scorable'];

							  		$agencyscor+=$pro['agency_and_repo_scored'];

							  		$agencyScoab+=$pro['agency_and_repo_scorable'];

							  	    $ovCurrentWaveScorab+=$pro['current_wave_scorable'];
							  		$ovpreWaveScorab+=1;

							  		

							  	?>

							    <tr>

							      <td>Recovery</td>

							      <td>{{$pro['name']}}</td>

							      <td><?=($pro['branch_and_repo_scorable'] != 0) ? round(($pro['branch_and_repo_scored']/$pro['branch_and_repo_scorable'])*100) : 0 ; ?>%</td>

							      <td><?=($pro['agency_and_repo_scorable'] != 0) ? round(($pro['agency_and_repo_scored']/$pro['agency_and_repo_scorable'])*100) : 0 ; ?>%</td>

							      <td><?php echo $sco1=($pro['current_wave_scorable'] != 0) ? round(($pro['current_wave_scored']/$pro['current_wave_scorable'])*100) : 0 ; ?>%</td>

							      <!--<td><?php //echo $sco2=($pro['pre_wave_scorable'] != 0) ? round(($pro['pre_wave_scored']/$pro['pre_wave_scorable'])*100) : 0 ; ?>%</td>-->
							      
							      <?php $sco2=$pro['pre_wave_static_scorable']; ?>
							      <td><?=$sco2?>%</td>
							      
                                    <?php $ovcurrentWaveSco+=$pro['current_wave_scored']; $ovpreWaveSco+=$sco2; ?>
							      <td><?php echo $del=($sco1-$sco2); ?>%</td>

							    </tr>

								<?php } ?>

							    @endforeach	

						    	<tr style="background: #F8CBAD;">

						    		<th scope="row">Recovery Total</th>

						    		<td></td>

						    		<td><?=($brscorab != 0) ? round(($brscor/$brscorab)*100) : 0 ; ?>%</td>

						    		<td><?=($agencyScoab != 0) ? round(($agencyscor/$agencyScoab)*100) : 0 ; ?>%</td>

						    		<td><?=($ovCurrentWaveScorab != 0) ? round(($ovcurrentWaveSco/$ovCurrentWaveScorab)*100) : 0 ; ?>%</td>

						    		<td><?=($ovpreWaveScorab != 0) ? round(($ovpreWaveSco/$ovpreWaveScorab)) : 0 ; ?>%</td>

						    		<?php 
                                        $sc=($ovCurrentWaveScorab != 0) ? round(($ovcurrentWaveSco/$ovCurrentWaveScorab)*100) : 0 ;
                                        $sco=($ovpreWaveScorab != 0) ? round(($ovpreWaveSco/$ovpreWaveScorab)) : 0 ;
                                        $delta=$sc-$sco;
                                    ?>
						    		<td><?php echo $delta; ?>%</td>

						    	</tr>					    

							  </tbody>

							</table>

						</div>

					</div>

					<br/>

					<div class="row" style="margin-top:20px;">

						<div class="col-md-6" style="margin-left: 20px;  border: 1px solid black;">
							<br/>
							<h4 style="text-align: center;"><u><b>Key Issue @ Branch</b></u></h4>

							<br/>

							<div style="height:300px; overflow-y: scroll;">

							<ul style="list-style: none;">

								<?php $n=1; ?>

								@foreach($branchFinding as $b)

							  	<li>{{$n.') '.ucfirst($b)}}</li>

							  	<?php $n++; ?>

							  	@endforeach							  

							</ul>

							</div>
							<br/>
							<button type="button" style="background:#9D1D27; color:white;" class="btn btn-info" data-toggle="modal" data-target="#modal-fullscreen-xl">Click here for ACR detail</button>

														
							<button type="button" style="background:#9D1D27; color:white;" class="btn btn-danger" data-toggle="modal" data-target="#modal-fullscreen-x2">Click here for details Branch findings</button>

							<br/>
							<br/>

						</div>

					</div>

				</div>

				

			</div>

		</div>

	</div>



	<!-- Modal Fullscreen xl -->

<div class="modal modal-fullscreen-xl" id="modal-fullscreen-xl" tabindex="-1" role="dialog" aria-hidden="true">

  <div class="modal-dialog" role="document">

    <div class="modal-content" style="margin-left:-350px;width:1210px;">

      <div class="modal-header">

        <h4 class="modal-title"><b>ACR</b></h4>

        <button type="button" class="close" data-dismiss="modal" aria-label="Close">

          <span aria-hidden="true">&times;</span>

        </button>

      </div>

      <div class="modal-body">

      	<div class="row">
        <div class="col-md-6" style="height:500px;overflow-y: scroll;">

        <table class="table" border="1">

          <thead style="background:#9D1D27; color:white;">

            <tr>

              <th colspan="7" scope="col">ACR Details</th>

            </tr>

            <tr>

              <th rowspan="2" scope="col">Product</th>


              @foreach($getAcr['pre_mon'] as $p) 
                <th colspan="2" scope="col">{{$p}}</th>
              @endforeach             

            </tr>

            <tr>              

              <th>Maintained</th>

              <th>Crossed</th>

              <th>Maintained</th>

              <th>Crossed</th>

              <th>Maintained</th>

              <th>Crossed</th>

            </tr>           

          </thead>

          <tbody>
              <?php $main[0]=0; $main[1]=0;  $main[2]=0; $cross[0]=0; $cross[1]=0; $cross[2]=0;?>
              @if(count($getAcr['pro_detail']) > 0)
                @foreach($getAcr['pro_detail'] as $p)
                    <?php 
                        $main[0]+=$p['main'][0];
                        $cross[0]+=$p['crossed'][0];
                        $main[1]+=$p['main'][1];
                        $cross[1]+=$p['crossed'][1];
                        $main[2]+=$p['main'][2];
                        $cross[2]+=$p['crossed'][2];
                    ?>
                 <tr>
                     <td>{{$p['pro_name']}}</td>
                     <td>{{$p['main'][0]}}</td>
                     <td>{{$p['crossed'][0]}}</td>
                     <td>{{$p['main'][1]}}</td>
                     <td>{{$p['crossed'][1]}}</td>
                     <td>{{$p['main'][2]}}</td>
                     <td>{{$p['crossed'][2]}}</td>
                 </tr>
                @endforeach
          		<tr>
          			<td><b>Grand Total</b></td>
          			<td><b>{{$main[0]}}</b></td>
          			<td><b>{{$cross[0]}}</b></td>
          			<td><b>{{$main[1]}}</b></td>
          			<td><b>{{$cross[1]}}</b></td>
          			<td><b>{{$main[2]}}</b></td>
          			<td><b>{{$cross[2]}}</b></td>
          		</tr>
          	@endif
          </tbody>

        </table>

        </div>

        <div class="col-md-6" style="height:500px;overflow-y: scroll;">
        	<table class="table" border="1">
        		<thead style="background:#9D1D27; color:white;">
        			<tr>
        				<th colspan="2" scope="col">ACR Remarks</th>
	            	</tr>
	            	<tr>
        				<th scope="col">Products</th>
        				<th scope="col">Remarks</th>
	            	</tr>
	            </thead>
	            <tbody>
	            	@foreach($product as $pr)
	            		@foreach($pr['acrfinding'] as $acr)
		            	<tr>
		            		<td>{{$pr['name']}}</td>
		            		<td>{{$acr}}</td>
		            	</tr>
		            	@endforeach
	            	@endforeach
	            </tbody>
        	</table>
        </div>

    	</div>
    	<br/>
    	<br/>
    	<div class="row">
    		<div class="col-md-12" style="height:500px;overflow-y: scroll;">
    			<table class="table" border="1">
	        		<thead style="background:#9D1D27; color:white;">
	        			<tr>
	        				<th colspan="11" scope="col">Allocation Capacity</th>
		            	</tr>
		            	<tr>
	        				<th rowspan="2" scope="col">Agency Name</th>
	        				@foreach($getAcr['pre_mon'] as $g)
	        						<th colspan="2" scope="col">{{$g}}</th>
	        				@endforeach
	        				<th rowspan="2" scope="col">Avg Allocation Count</th>
	        				<th rowspan="2" scope="col">Avg FOS</th>
	        				<th rowspan="2" scope="col">Capacity</th>
	        				<th rowspan="2" scope="col">Gap</th>
		            	</tr>
		            	<tr>
		            		<th scope="col">Allocation Count</th>
		            		<th scope="col">FOS Count</th>
		            		<th scope="col">Allocation Count</th>
		            		<th scope="col">FOS Count</th>
		            		<th scope="col">Allocation Count</th>
		            		<th scope="col">FOS Count</th>
		            	</tr>
		            </thead>
		            <tbody>
		            	
		            	@if(count($getAcr['allocation_capacity']) > 0)
		            		@foreach($getAcr['allocation_capacity'] as $gt)
		            		    <tr>
		            		        <td>{{$gt['agency_name']}}</td>
		            		        <td>{{$gt['alloc_count'][0]}}</td>
		            		        <td>{{$gt['fos_count'][0]}}</td>
		            		        <td>{{$gt['alloc_count'][1]}}</td>
		            		        <td>{{$gt['fos_count'][1]}}</td>
		            		        <td>{{$gt['alloc_count'][2]}}</td>
		            		        <td>{{$gt['fos_count'][2]}}</td>
		            		        <td>{{$gt['avg_alloc_count']}}</td>
		            		        <td>{{$gt['avg_fos_count']}}</td>
		            		        <td>{{$gt['capacity']}}</td>
		            		        <td>{{$gt['gap']}}</td>
		            		    </tr>
		            		@endforeach
		            	@endif
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




	<!-- Modal Fullscreen xl -->

<div class="modal modal-fullscreen-xl" id="modal-fullscreen-x2" tabindex="-1" role="dialog" aria-hidden="true">

  <div class="modal-dialog" role="document">

    <div class="modal-content" style="margin-left:-200px;width:900px;">

      <div class="modal-header">

        <h4 class="modal-title"><b>This section provides detail deviations found in the {{$getBranch->name}} branch during audit process:</b></h4>

        <button type="button" class="close" data-dismiss="modal" aria-label="Close">

          <span aria-hidden="true">&times;</span>

        </button>

      </div>
      
      <div class="modal-body">

        <div class="col-md-12">
        	<dl>
        		@foreach($product as $p)       	
		  			<dt><a href="javascript:getbranchFind(<?=$p['id'];?>);">{{$p['name']}}</a></dt>
		  			@foreach($p['finding'] as $f)
		  				<dd class="pro_<?=$p['id'];?>" style="display: none;">- {{ucfirst($f)}}</dd>
		  			@endforeach		  
				@endforeach 			

			</dl>   

    </div>

      </div>

      <div class="modal-footer">

        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>        

      </div>

    </div>

  </div>

</div>



	

  </div>

	@endif



</div>

<form method="get" id="coll" action="{{url('/reportAutomationDataColl')}}">
	<input type="hidden" name="branch" value="{{$getBranch->id}}" />
	<input type="hidden" name="start_date" value="{{$start_date}}" />
	<input type="hidden" name="end_date" value="{{$end_date}}" />
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

function getbranchFind(id) {
	$(".pro_"+id).toggle();	
}

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

Highcharts.chart('zone_wise_chart_1', {

    chart: {

        type: 'column'

    },

    title: {

        text: 'Performance Trend'

    },

    subtitle: {

        text: ''

    },

    xAxis: {

        categories: ['Previous-3','Previous-2','Previous-1','Current'],

        title: {

            text: ''

        }

    },

    yAxis: {

        min: 0,

        max:110,

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

        name: 'Score(%)',

        data:[

        

            {

                y:<?=($oldSco) ? $oldSco->previous_3 : 0 ?>,

                //s:<?php //echo $z['audit_count']; ?>,

                color:'#9D1D27'



            },

            {

            	y:<?=($oldSco) ? $oldSco->previous_2 : 0 ?>,
            	color:'#9D1D27'

            },

            {

            	y:<?=($oldSco) ? $oldSco->previous_1 : 0 ?>,
            	color:'#9D1D27'

            },

            {

            	y:<?=($q1Scorable != 0) ? round(($q1Scored/$q1Scorable)*100) : 0 ?>,
            	color:'#9D1D27'

            }

        

        ]

        

    }]

});

</script>

@endif



@endsection