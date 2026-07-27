
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

$acrmonth = date("n",strtotime($end_date));

$year=date("Y",strtotime($end_date));

$acryearQuarter = ceil($acrmonth / 3);



function getMonths($quarter,$year){
    switch($quarter) {
        case 1: return array('Jan_'.$year, 'Feb_'.$year, 'Mar_'.$year);
        case 2: return array('Apr_'.$year, 'May_'.$year, 'Jun_'.$year);
        case 3: return array('Jul_'.$year, 'Aug_'.$year, 'Sep_'.$year);
        case 4: return array('Oct_'.$year, 'Nov_'.$year, 'Dec_'.$year);
    }
}


$getQuarterMonth=getMonths($acryearQuarter,$year);

$recAgent=array();

foreach($data as $d) {
	
	if(!is_null($d->agency_id) && !array_key_exists($d->agency_id, $agencyList))
	{
		$agencyList[$d->agency_id]=$d->agency->name;
	}

	if($selecollMan == "all") {
		if(!is_null($d->agency_id) && !array_key_exists($d->agency_id, $recAgent))
		{
			$recAgent[$d->agency_id]=$d->agency->name;
		}
	} else {
		$recAgent[$getAgency->agency_id]=$getAgency->name;
	}
		
}

$bucket=array();
foreach($depositionData as $d) {
	if($selecollMan == "all") {
		if(!array_key_exists($d->delay_deposite_bucket, $bucket)) {
			$bucket[$d->delay_deposite_bucket]['count']=0;
			$bucket[$d->delay_deposite_bucket]['rec_amt']=0;
		}	
	} else {
		if($selecollMan == $d->agency_id && !array_key_exists($d->delay_deposite_bucket, $bucket)) {
			$bucket[$d->delay_deposite_bucket]['count']=0;
			$bucket[$d->delay_deposite_bucket]['rec_amt']=0;
		}	
	}
		
}

foreach($bucket as $key=>$b) {
	foreach($depositionData as $d) {
		if($selecollMan == "all") {
			if($d->delay_deposite_bucket == $key) {
				$bucket[$key]['count']+=1;
				$bucket[$key]['rec_amt']+=$d->total_rec_amt;
			}	
		} else {
			if($d->delay_deposite_bucket == $key && $selecollMan == $d->agency_id) {
				$bucket[$key]['count']+=1;
				$bucket[$key]['rec_amt']+=$d->total_rec_amt;
			}	
		}		
	}	
}

$total=0;
foreach($secData as $s) {
	$total+=$s->allocation;
}

$recData=array();
foreach($recAgent as $k=>$a) {
	$re=array();
	$re['agency_name']=$a." ".$k;

	foreach($getQuarterMonth as $q) {
		$ex=explode("_", $q); 
		$re['ex_mon']=$ex[0];
		$re['mon']=$q;
		$re['count']=0;
		$re['res_amt']=0;
		foreach($receiptData as $r) {
			if($k == $r->agency_id) {
				$month = date("M",strtotime($r->date));
				if($re['ex_mon'] == $month) {
					$re['count']+=1;
					$re['res_amt']+=$r->total_rec_amt;
				}
			}
		}
		$recData[]=$re;
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

					<input style="background: tomato; float: right; margin-left: 5px;" id="GAPS" name="GAPS" type="button" class="btn btn-sm btn-secondary" value="MAJOR GAPS" onClick="getDiv('gaps');" />

					<input style="float: right; margin-left: 5px;" id="COLLECTION" name="COLLECTION" type="button" class="btn btn-sm btn-secondary" value="COLLECTION MANAGER" onClick="getDiv('collecMana');" />

					<input style="float: right;" id="SUMMARY" name="SUMMARY" type="button" class="btn btn-sm btn-secondary" value="SUMMARY"  onClick="getDiv('summary');"/>

				</div>

				<div id="gaps" class="card-body card-block">
					<div class="row">
						<div class="col-md-4">
							<label>Select Agency</label>
							<select name="agency_id" id="agency_id" class="form-control" onchange="getPost(this.value);">
								<option value="all" <?php if($selecollMan == "all") {echo "selected"; } ?>> All Agency </option>
								@foreach($agencyList as $key=>$br)			
									<option value="{{$key}}" <?php if($selecollMan == $key) {echo "selected"; } ?>>{{$br}}</option>
								@endforeach
							</select>
						</div>
					</div>
					<br/><br/>
					<div class="row">
						<div class="col-md-6" style="height: 300px; overflow-y: scroll;">
							<table class="table" border="1">
								<thead style="background:#9D1D27; color:white;">
									<tr>
										<th colspan="3" scope="col">Delay In Cash Deposition</th>			
									</tr>
									<tr>
										<th>Row Labels</th>
										<th>Count</th>
										<th>Total Receipt Amount</th>
									</tr>									
								</thead>
								<tbody>
									<?php $cTot=0; $recTot=0; ?>
									@foreach($bucket as $k=>$al)
										<?php $cTot+=$al['count']; $recTot+=$al['rec_amt']; ?>
										<tr>
											<td>{{$k}}</td>
											<td>{{$al['count']}}</td>
											<td>{{$al['rec_amt']}}</td>	
										</tr>
									@endforeach
									<tr>
										<td>Grand Total</td>
										<td>{{$cTot}}</td>
										<td>{{$recTot}}</td>	
									</tr>
								</tbody>
							</table>
						</div>
						<div class="col-md-2">
							<button style="background:#9D1D27; color:white;" data-target="#modal-fullscreen-xl" class="btn btn-info" data-toggle="modal">Click for details.</button>
						</div>
						<div class="col-md-4" style="height: 300px; overflow-y: scroll;">
							<table class="table" border="1">
								<thead style="background:#9D1D27; color:white;">
									<tr>
										<th colspan="4" scope="col">Delay in Secondary Allocation</th>			
									</tr>
									<tr>
										<th scope="col">Delay Gap Bucket</th>
										<th scope="col">POS Amount(Cr)</th>
										<th scope="col">POS Amount(Lkh)</th>
										<th scope="col">Count</th>
									</tr>									
								</thead>
								<tbody>

								</tbody>
							</table>
						</div>
					</div>
					<br/><br/>
					<div class="row">
						<div class="col-md-8" style="height: 500px; overflow-y: scroll;">
							<table class="table" border="1">
								<thead style="background:#9D1D27; color:white;">
									<tr>
										<th colspan="4" scope="col">Receipt cut in Non-Day light hour</th>			
									</tr>
									<tr>
										<th>Agency Name</th>
										<th>Month</th>
										<th>Count</th>
										<th>Total Receipt Amount</th>
									</tr>									
								</thead>
								<tbody>
									@foreach($recData as $r)
										<tr>
											<td>{{$r['agency_name']}}</td>
											<td>{{$r['mon']}}</td>
											<td>{{$r['count']}}</td>
											<td>{{$r['res_amt']}}</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
						<div class="col-md-4">
							<button  style="background:#9D1D27; color:white;" data-target="#modal-fullscreen-x2" class="btn btn-info" data-toggle="modal">Click for details.</button>
						</div>
					</div>
					<br/><br/>
					<div class="row">
						<div class="col-md-12" style="height: 500px; overflow-y: scroll;">
							<table class="table" border="1">
								<thead style="background:#9D1D27; color:white;">
									<tr>
										<th colspan="6" scope="col">Delay in Secondary Allocation-Top Contributing Agency</th>			
									</tr>
									<tr>
										<th>Agency Name</th>
										@foreach($getQuarterMonth as $q)
										<th>{{$q}}</th>
										@endforeach
										<th>Grand Total</th>
										<th>Contribution %</th>
									</tr>									
								</thead>
								<tbody>

									@foreach($secData as $s)
									<?php $gt=0; $month = date("M",strtotime($s->date));
										 
										?>
										<tr>
											<td>{{$s->agency->name}}</td>
											@foreach($getQuarterMonth as $q)
												<?php					
												$ex=explode("_", $q); 
												
												$d=($ex[0] == $month) ? $s->allocation : 0;
												$gt+=$d;
												?>
												<td><?=$d;?></td>
											@endforeach
											<td>{{$gt}}</td>
											<td><?=($total != 0) ? round(($gt/$total)*100) : 0 ; ?></td>
										</tr>
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

        <h4 class="modal-title">Delay In Cash Deposition Detail</h4>

        <button style="background:#9D1D27; color:white;" type="button" class="close" data-dismiss="modal" aria-label="Close">

          <span aria-hidden="true">&times;</span>

        </button>

      </div>

      <div class="modal-body">      	
        <div class="row">
    		<div class="col-md-12" style="height:500px;overflow-y: scroll;">
    			<table class="table" border="1">
	        		<thead style="background:#9D1D27; color:white;">
	        			<tr>
	        				<th>Location</th>
	        				<th>State</th>
	        				<th>Branch Name</th>
	        				<th>Agency ID</th>
	        				<th>Agency Name</th>
	        				<th>Agent Name</th>
	        				<th>Agent ID</th>
	        				<th>Receipt No</th>
	        				<th>Receipt Date</th>
	        				<th>Month</th>
	        				<th>Reference No</th>
	        				<th>Product 1</th>
	        				<th>Total Receipt Amount</th>
	        				<th>Deposite Date</th>
	        				<th>BBPayBatchAckDate</th>
	        				<th>Delay Deposit Buket</th>
	        			</tr>
		            </thead>
		            <tbody>
		            	@foreach($depositionData as $d)
		            		<tr>
		            			<td>{{$d->agency->location}}</td>
		            			<td>{{$d->branch->city->state->name}}</td>
		            			<td>{{$d->branch->name}}</td>
		            			<td>{{$d->agency_id}}</td>
		            			<td>{{$d->agency->name}}</td>
		            			<td>{{$d->agent_name}}</td>
		            			<td>{{$d->agent_id}}</td>
		            			<td>{{$d->receipt_no}}</td>
		            			<td>{{$d->receipt_date}}</td>
		            			<td>{{$d->month}}</td>
		            			<td>{{$d->reference_no}}</td>
		            			<td>{{$d->product_1}}</td>
		            			<td>{{$d->total_rec_amt}}</td>
		            			<td>{{$d->deposite_date}}</td>
		            			<td>{{$d->bb_pay_batch_date}}</td>
		            			<td>{{$d->delay_deposite_bucket}}</td>
		            		</tr>
		            	@endforeach		            	
		            </tbody>
	        	</table>
    		</div>
    	</div>

      </div>

      <div class="modal-footer">

        <button style="background:#9D1D27; color:white;" type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>        

      </div>

    </div>

  </div>

</div>

<div class="modal modal-fullscreen-xl" id="modal-fullscreen-x2" tabindex="-1" role="dialog" aria-hidden="true">

  <div class="modal-dialog" role="document">

    <div class="modal-content" style="margin-left:-350px;width:1210px;">

      <div class="modal-header">

        <h4 class="modal-title">Receipt Cut In Non-Day light hour Detail</h4>

        <button style="background:#9D1D27; color:white;" type="button" class="close" data-dismiss="modal" aria-label="Close">

          <span aria-hidden="true">&times;</span>

        </button>

      </div>

      <div class="modal-body">      	
        <div class="row">
    		<div class="col-md-12" style="height:500px;overflow-y: scroll;">
    			<table class="table" border="1">
	        		<thead style="background:#9D1D27; color:white;">
	        			<tr>
	        				<th>Location</th>
	        				<th>State</th>
	        				<th>Branch Name</th>
	        				<th>Agency ID</th>
	        				<th>Agency Name</th>
	        				<th>Agent Name</th>
	        				<th>Agent ID</th>
	        				<th>Receipt No</th>
	        				<th>Receipt Date</th>
	        				<th>Month</th>
	        				<th>Reference No</th>
	        				<th>Product 1</th>
	        				<th>Total Receipt Amount</th>
	        				<th>Deposite Date</th>
	        				<th>BBPayBatchAckDate</th>
	        				<th>Delay Deposit Buket</th>
	        				<th>Receipt Time_1</th>
	        				<th>Time_BKT</th>
	        			</tr>
		            </thead>
		            <tbody>
		            	@foreach($receiptData as $d)
		            		<tr>
		            			<td>{{$d->agency->location}}</td>
		            			<td>{{$d->branch->city->state->name}}</td>
		            			<td>{{$d->branch->name}}</td>
		            			<td>{{$d->agency_id}}</td>
		            			<td>{{$d->agency->name}}</td>
		            			<td>{{$d->agent_name}}</td>
		            			<td>{{$d->agent_id}}</td>
		            			<td>{{$d->receipt_no}}</td>
		            			<td>{{$d->receipt_date}}</td>
		            			<td>{{$d->month}}</td>
		            			<td>{{$d->reference_no}}</td>
		            			<td>{{$d->product_1}}</td>
		            			<td>{{$d->total_rec_amt}}</td>
		            			<td>{{$d->deposite_date}}</td>
		            			<td>{{$d->bb_pay_batch_date}}</td>
		            			<td>{{$d->delay_deposite_bucket}}</td>
		            			<td>{{$d->receipt_time_1}}</td>
		            			<td>{{$d->time_bkt}}</td>
		            		</tr>
		            	@endforeach		            	
		            </tbody>
	        	</table>
    		</div>
    	</div>

      </div>

      <div class="modal-footer">

        <button style="background:#9D1D27; color:white;" type="button" style="background:#9D1D27; color:white;" class="btn btn-secondary" data-dismiss="modal">Close</button>        

      </div>

    </div>

  </div>

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

<form method="get" id="majgap2" action="{{url('/reportAutomationDatagap')}}">
	<input type="hidden" name="branch" value="{{$getBranch->id}}" />
	<input type="hidden" name="start_date" value="{{$start_date}}" />
	<input type="hidden" name="end_date" value="{{$end_date}}" />
	<input type="hidden" name="cmid" id="cmid" value="{{$selecollMan}}" />
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
	$("#majgap2").submit();
}



</script>

@endif



@endsection