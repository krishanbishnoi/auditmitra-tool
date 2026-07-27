@extends('layouts.master')

@section('css')

{{-- <link rel="stylesheet" href="{{URL::asset('base/style.bundle.css')}}"> --}}

	<style>

		.sp-row .row {

			margin-bottom: 15px;

		}





		.sp-row .row {

			margin-bottom: 15px;

		}



		.flex-container {

			display: flex;

			align-items: center;

		}



		.flex-container {

			display: flex;

			align-items: center;

		}

		.kt-font-bolder {

			font-weight: 600 !important;

		}

		#seprator {

			margin: 2.5rem 0 0 0;

		}

		#seprator {

			margin: 2.5rem 0 0 0;

		}

		.kt-separator.kt-separator--space-lg {

			margin: 2.5rem 0;

		}

		.kt-separator.kt-separator--border-dashed {

			border-bottom: 1px dashed #ebedf2;

		}

		.kt-separator {

			height: 0;

			margin: 20px 0;

			border-bottom: 1px solid #ebedf2;

		}

		.kt-font-primary {

			color: #5867dd !important;

		}



		.kt-font-bolder {

			font-weight: 600 !important;

		}

		.kt-font-bold {

			font-weight: 500 !important;

		}

		.centerparameter{

			display: flex;

			justify-content: center;

			align-items: center

		}

	</style>

@endsection

@section('title')

Audit 

@endsection



@section('sh-detail')

Messages

@endsection



@section('content')



<div class="row">

	<div class="col-lg-12" style="margin-top:10x">

	</div>

</div>

<div class="animated fadeIn">

	<div class="row">

		<div class="col-lg-12">

			<div class="card"> 

				<div class="card-header" style="background-image: linear-gradient(to right, rgb(132, 94, 194), rgb(144, 109, 198), rgb(156, 125, 201), rgb(168, 140, 205), rgb(179, 156, 208));color:#fff">

					<strong class="card-title">{{($data->lob=='commercial_vehicle')?'Commercial Vehicle':ucfirst($data->lob)}} | {{ucfirst(str_replace('_',' ',$data->type))}}</strong>

				</div>

				<div class="card-body">

					<div class="row">

						@if($data->type=='branch')

							<div class="col-md-3 form-group">

								<label>Branch*</label>

								<select name="branch" class="form-control branch" disabled>

								<option value="">Choose Branch</option>

								@foreach ($branch as $item)  

									<option value="{{$item->id}}" {{($item->id==$result->branch_id)?'selected':''}}>{{$item->name}}</option>

								@endforeach

								</select>

							</div>

						@elseif($data->type=='agency')

							<div class="col-md-3 form-group">

								<label>Agency*</label>

								<select name="agency" class="form-control agency" disabled>

								<option value="">Choose Agency</option>

								@foreach ($agency as $item)  

									<option value="{{$item->id}}" {{($item->id==$result->agency_id)?'selected':''}}>{{$item->name}}</option>

								@endforeach

								</select>

							</div>

						@elseif($data->type=='repo_yard')

							<div class="col-md-3 form-group">

								<label>Yard*</label>

								<select name="yard" class="form-control yard" disabled>

								<option value="">Choose Yard</option>

								@foreach ($yard as $item)  

									<option value="{{$item->id}}" {{($item->id==$result->yard_id)?'selected':''}}>{{$item->name}}</option>

								@endforeach

								</select>

							</div>

							@elseif($data->type=='branch_repo')

							<div class="col-md-3 form-group">

								<label>Branch Repo*</label>

								<select name="branch_repo" class="form-control branch_repo" disabled>

								<option value="">Choose Branch Repo</option>

								@foreach ($branchRepo as $item)  

									<option value="{{$item->id}}" {{($item->id==$result->branch_repo_id)?'selected':''}}>{{$item->name}}</option>

								@endforeach

								</select>

							</div>

							@elseif($data->type=='agency_repo')

							<div class="col-md-3 form-group">

								<label>Agency Repo*</label>

								<select name="agency_repo" class="form-control agency_repo" disabled>

								<option value="">Choose Yard</option>

								@foreach ($agencyRepo as $item)  

									<option value="{{$item->id}}" {{($item->id==$result->agency_repo_id)?'selected':''}}>{{$item->name}}</option>

								@endforeach

								</select>

							</div>

						@endif

						<div class="col-md-3 form-group" id="product" style="display:none;">

							<label>Product*</label>

							<select name="product" class="form-control product" id="productSelect" disabled>

							<option value="">Choose Product</option>

							</select>

						</div>

					</div>

					<div class="row" id="data">

					</div>

				</div>

			</div>

			<div class="card">

				<div class="card-header"  style="background-image: linear-gradient(to right, rgb(132, 94, 194), rgb(144, 109, 198), rgb(156, 125, 201), rgb(168, 140, 205), rgb(179, 156, 208));color:#fff">

					<strong class="card-title">Audit</strong>

				</div>

				<div class="card-body">

					

					<div class="row">

						<div class="col-md-2 kt-font-bolder">

							Parameter

						</div>

						<div class="col-md-10 kt-font-bolder">

							<div class="row">

								<div class="col-md-2 kt-font-bolder">Sub Parameter</div> 

								<div class="col-md-2 kt-font-bolder">Observation</div> 

								<div class="col-md-2 kt-font-bolder">Scored</div> 

								<div class="col-md-2 kt-font-bolder">Remarks</div>

								<div class="col-md-2 kt-font-bolder">Action</div>

							</div>

						</div>

					</div>

					<div id="seprator" class="kt-separator kt-separator--border-dashed kt-separator--space-lg"></div>

					@php

						$total=0;

					@endphp

					@foreach ($data->parameter as $item)

					<div class="row flex-container" style="border-bottom: 1px solid rgb(204, 204, 204); padding: 20px 0px; height: 100%;">

						<div class="col-md-2 kt-font-bolder kt-font-primary flex-item centerparameter">

							{{$item->parameter}}

						</div>

						<div class="col-md-10 sp-row">

							@foreach ($item->qm_sheet_sub_parameter as $value)

							<div class="row flex-container mb-2">

							    <div class="col-md-2 kt-font-bold" style="color:red;">

								@else

								<div class="col-md-2 kt-font-bold">

								@endif

								

									{{$value->sub_parameter}} <i title="sdfdf" class="la la-info-circle kt-font-warning sp-details-top"></i>

								</div>

								<div class="col-md-2">

								<select class="form-control 0bervation" id="obs{{$value->id}}" data-id="{{$value->id}}" data-parameterId="{{$item->id}}" data-point="{{$value->weight}}" disabled>

										<option value="">Choose type</option>

									@if(isset($resultSubPar[$value->id]) && $resultSubPar[$value->id]->option_selected==null)

										@if($value->pass==1)<option value="{{$value->weight}}" {{(isset($resultSubPar[$value->id]) && ($resultSubPar[$value->id]->selected_option==$value->weight))?'selected':''}}>Satisfactory</option>@endif

										@if($value->fail==1)<option value="0"  {{(isset($resultSubPar[$value->id]) && ($resultSubPar[$value->id]->is_critical==0 && $resultSubPar[$value->id]->selected_option==0))?'selected':''}}>Unsatisfactory</option>@endif

									@else

										@if($value->pass==1)<option value="{{$value->weight}}" {{(isset($resultSubPar[$value->id]) && ($resultSubPar[$value->id]->option_selected=='Pass'))?'selected':''}}>Satisfactory</option>@endif

										@if($value->fail==1)<option value="0"  {{(isset($resultSubPar[$value->id]) && ($resultSubPar[$value->id]->is_critical==0 && $resultSubPar[$value->id]->option_selected=='Fail'))?'selected':''}}>Unsatisfactory</option>@endif

									@endif

									@if($value->critical==1)<option value="Critical"  {{(isset($resultSubPar[$value->id]) && ($resultSubPar[$value->id]->is_critical==1))?'selected':''}}>Critical</option>@endif

									@if($value->na==1)<option value="N/A"  {{(isset($resultSubPar[$value->id]) && ($resultSubPar[$value->id]->is_critical==0 && $resultSubPar[$value->id]->selected_option=='N/A'))?'selected':''}}>N/A</option>@endif

									@if($value->pwd==1)<option value="{{round(($value->weight)/2,2)}}"  {{(isset($resultSubPar[$value->id]) && ($resultSubPar[$value->id]->selected_option==round(($value->weight)/2,2)))?'selected':''}}>PWD</option>@endif

									@if($value->per==1)<option value="{{round(($value->weight))}}" data-type="rating" {{(isset($resultSubPar[$value->id]) && ($resultSubPar[$value->id]->is_percentage==1))?'selected':''}}>Percentage</option>@endif

								</select>

									<span style="display:none" id="org{{$value->id}}">{{$value->weight}}</span>

								</div>

								<div class="col-md-2">

								<select class="form-control ratingSelect" name="ratingSelect" id="ratingSelect{{$value->id}}"  style="{{(isset($resultSubPar[$value->id]) && ($resultSubPar[$value->id]->is_percentage!=1))?'display:none':'display:block'}}" data-id="{{$value->id}}" data-parameterId="{{$item->id}}" disabled>

									<option>select percentage</option>

									@for($counting=10;$counting<=100;$counting=$counting+10)

									<option value="{{$counting}}" {{(isset($resultSubPar[$value->id]) && ($resultSubPar[$value->id]->selected_per==$counting))?'selected':''}}>{{$counting}}%</option>

									@endfor

								</select>

								@if(isset($resultSubPar[$value->id]) && ($resultSubPar[$value->id]->is_percentage!=1))

								<input type="text" id="{{$value->id}}" readonly="readonly" class="form-control" value="{{ (isset($resultSubPar[$value->id]) && ($resultSubPar[$value->id]->is_critical==1))?'Critical':($resultSubPar[$value->id]->score ?? '')}}"  style="{{(isset($resultSubPar[$value->id]) && ($resultSubPar[$value->id]->is_percentage==1))?'display:none':'display:block'}}">

								@else

								<input type="text" id="{{$value->id}}" readonly="readonly" class="form-control" value="rating"  style="{{(isset($resultSubPar[$value->id]) && ($resultSubPar[$value->id]->is_percentage==1))?'display:none':'display:block'}}">

								@endif

								</div>

								<div class="col-md-2">

								{{-- <!-- <textarea type="text" class="form-control" id="remark{{$value->id}}" value="{{ $resultSubPar[$value->id]->remark}}">{{$resultSubPar[$value->id]->remark}}</textarea> --> --}}

								</div>

								<div class="col-md-2">

									{{-- <!-- <button class="btn btn-danger btn-sm alertModal" data-parameterid="{{$item->id}}" data-id="{{$value->id}}">Alert</button>

									<button class="btn btn-info btn-sm artifactModal mr-1" data-parameterid="{{$item->id}}" data-id="{{$value->id}}">Artifact</button> --> --}}

								</div>

							</div>

							<div class="col-md-12 row">

								<div class="col-md-10">

									<textarea class="form-control" id="remark{{$value->id}}" placeholder="Enter Remark Here" disabled>{{$resultSubPar[$value->id]->remark ?? ''}}</textarea>

								</div>

								@if(in_array($value->id,$redalertIds))

												<img src="{{URL::asset('/public/assets/images/flag.png')}}" style="width:30px;height:30px;" data-id="">

								@endif

							</div>

							<div class="col-md-12 row">

								<div class="col-md-10 preview{{$value->id}}">

									@foreach($value->artifact as $art)

										@if(in_array($art->id,$artifactIds))

											<div class="img-wrap art{{$art->id}}" style="position: relative;display: inline-block;font-size: 0;">

												<!-- <span class="close">&times;</span> -->

												<img src="{{URL::asset('storage/app/'.$art->file)}}" style="width:100px;height:100px;" data-id="{{$art->id}}">

											</div>

										@endif

									@endforeach

								</div>

							</div>

							<div id="seprator" class="kt-separator kt-separator--border-dashed "></div>

							@php

								$total=$total+$value->weight;

							@endphp

							@endforeach

							<span style="display:none" id="total{{$item->id}}">{{$total}}</span>		

						</div>

					</div>

					@endforeach

					

					<div>

						

				</div>

			</div>

		</div>



		<div class="card">

			<div class="card-header" style="background-image: linear-gradient(to right, rgb(255, 199, 95), rgb(255, 211, 97), rgb(254, 223, 101), rgb(252, 236, 106), rgb(249, 248, 113));color:#fff">

				<strong class="card-title">Result</strong>

			</div>

			<div class="card-body">

		<div class="row" style="border-bottom: 1px solid rgb(204, 204, 204);">

			<div class="col-lg-3 kt-font-bolder">Parameter</div>

			<div class="col-lg-3 kt-font-bolder">Scorable</div>

			<div class="col-lg-3 kt-font-bolder">Scored</div>

			<div class="col-lg-3 kt-font-bolder">Scores%</div>

		</div>

		

		@foreach ($data->parameter as $item)

			<div class="row" style="border-bottom: 1px solid rgb(204, 204, 204); padding: 20px 0px; height: 100%;">

				<div class="col-lg-3 kt-font-bold kt-font-primary">{{$item->parameter}}</div>

				<div class="col-lg-3" id="scroable{{$item->id}}">0</div>

				<div class="col-lg-3 kt-font-danger" id="wfatal{{$item->id}}">0</div>

				<div class="col-lg-3" id="wfatalper{{$item->id}}">0</div>

			</div>

		@endforeach



		<div class="row" style="padding: 20px 0px; height: 100%;">

			<div class="col-lg-3 kt-font-bold kt-font-success">Over All</div>

			<div class="col-lg-3 kt-font-bold" id="scroable">0</div>

			<div class="col-lg-3 kt-font-bold kt-font-danger" id="wfatal">0</div>

			<div class="col-lg-3 kt-font-bold" id="wfatalper">0</div>

		</div>

	</div>

</div>

	</div>



	<div class="col-md-12">

		{{-- <form method="post" action="{{route('saveStatus')}}"> --}}

		<div class="card">

			@csrf

			<div class="card-header" style="background-image: linear-gradient(to right, rgb(132, 94, 194), rgb(144, 109, 198), rgb(156, 125, 201), rgb(168, 140, 205), rgb(179, 156, 208));color:#fff">

				<h5>Update QC Status</h5>

			</div>

			<div class="card-body">

				<div class="row">

					<input type="hidden" name="qm_sheet_id" value="{{$result->qm_sheet_id}}">

					<input type="hidden" name="audit_id" value="{{$result->id}}">

					<div class="col-md-6 form-group">

						<label>Status*</label>

						<select class="form-control" name="status" required disabled>

							<option>Select one!</option> 

							<option value="1" {{($qc->status==1)?'selected':''}}>Pass with edit</option> 

							<option value="2" {{($qc->status==2)?'selected':''}}>Pass</option> 

							<option value="3" {{($qc->status==3)?'selected':''}}>Failed</option>

						</select>

					</div>

					<div class="col-md-6 form-group">

						<label>Feedback</label>

						<textarea class="form-control" name="feedback" disabled>{{$qc->feedback}}</textarea>

					</div>

				</div>

			</div>

			<div class="card-footer">

				

			</div>

		{{-- </form> --}}

		</div>

	</div>

</div>

	

</div>

@endsection

@section('css')

@include('shared.table_css');

<style>

.img-wrap .close {

    position: absolute;

    top: 2px;

    right: 2px;

    z-index: 100;

    background-color: #FFF;

    padding: 5px 2px 2px;

    color: #000;

    font-weight: bold;

    cursor: pointer;

    opacity: .2;

    text-align: center;

    font-size: 22px;

    line-height: 10px;

    border-radius: 50%;

}

.img-wrap:hover .close {

    opacity: 1;

}

</style>

@endsection

@section('js')

<script>

jQuery('#collection_manager-select').on('change',function(e){

	var code =jQuery(this).data('code');

	var bucket =jQuery(this).data('bucket');

	jQuery('input[name=Collection_Manager_bucket]').val(bucket)

	jQuery('input[name=Collection_Managercode]').val(code)

})

jQuery(document).on('change', '.artifact', function(e){

	// console.log(e)

	if(e.target.files.length>0){

		jQuery('#moreArtifact').append('<input type="file" id="file" name="artifactfile[]" class="form-control-file artifact file">')

	}

})

jQuery(document).on('click', '.close', function() {

		var id = jQuery(this).closest('.img-wrap').find('img').data('id');

		var data={'id':id,'_token':'{{ csrf_token() }}'}

		var saveData = jQuery.ajax({

				type: 'DELETE',

				url: "{{url('artifact')}}/"+id,

				data: data,

				success: function(resultData) { 

					console.log(resultData)

					jQuery('.art'+id).remove();

				}

			});

		saveData.error(function() { alert("Something went wrong"); });

});

	var result={};

	var par={};

	var subpar={};

	@php

	foreach($data->parameter as $item)

	{

		@endphp

		result[{{$item->id}}]={};

		@php

	}

	

	foreach($resultSubPar as $k=>$v){

		$subValue=($v->is_critical==1)?"Critical":$v->score;

		@endphp	

		subpar[{{$k}}]={{$v->id}}

		resultFun('{{$subValue}}', {{$k}},{{$v->parameter_id}})

	@php

	}

	foreach($resultPar as $k=>$v){

	@endphp

		par[{{$k}}]={{$v->id}}

	@php

	}

	@endphp

	function sum( obj ) {

		var sum = 0;

		for( var el in obj ) {

			if( obj.hasOwnProperty( el ) ) {

				sum += parseFloat( obj[el] );

			}

		}

		return sum;

	}

	function totalfun( obj ){

		var total=0;

		var parmeterTotal=0;

		for( var el in obj ) {

			if( obj.hasOwnProperty( el ) ) {

				var subtotal=0

				for( var item in obj[el] ) {

				if( obj[el].hasOwnProperty( item ) ) {

					if(obj[el][item]!='N/A'){

						paramterValue=jQuery('#org'+item).html();

					}

					else{

						paramterValue=0;

					}

					if(obj[el][item]!='Critical'){

						subtotal=parseFloat(subtotal)+parseFloat((obj[el][item]=='N/A'?0:obj[el][item]));

						parmeterTotal=parmeterTotal+parseFloat(paramterValue);

					}

					else{

						subtotal=0;

						parmeterTotal=parmeterTotal+parseFloat(paramterValue);

						break;

					}

				}

			}

			total=parseFloat(subtotal)+parseFloat(total);

				// total +=sum(obj[el])

			}

		}

		jQuery('#scroable').text(parmeterTotal)

		jQuery('#wfatal').text(total)

		jQuery('#wnfatal').text(total)

		var wfatalper=(total!=0)?(total/parmeterTotal)*100:0;

		// var wnfatalper=(total!=0)?(total/total)*100:0;

		jQuery('#wfatalper').text(wfatalper.toFixed(2)+'%')

		// jQuery('#wnfatalper').text(wnfatalper+'%')

	}

	var total=0;

	function resultFun(value, id,parameterId){

		console.log(value, id,parameterId)

		result[parameterId][id]=value;

		var total=0;

		var parmeterTotal=0;

			for( var el in result[parameterId] ) {

				if( result[parameterId].hasOwnProperty( el ) ) {

					var paramterValue=0;

					if(result[parameterId][el]!='N/A'){

						paramterValue=jQuery('#org'+el).html();

					}

					else{

							paramterValue=0;

						}

					if(result[parameterId][el]!='Critical'){

						total=parseFloat(total)+parseFloat((result[parameterId][el]=='N/A'?0:result[parameterId][el]));

						parmeterTotal=parmeterTotal+parseFloat(paramterValue);

					}

					else{

						total=0;

						parmeterTotal=parmeterTotal+parseFloat(paramterValue);

						break;

					}

				}

			}

			console.log(total)

		jQuery('#scroable'+parameterId).text(parmeterTotal)

		jQuery('#wfatal'+parameterId).text(total)

		// jQuery('#wnfatal'+parameterId).text(total)

		var wfatalper=(total!=0)?(total/parmeterTotal)*100:0;

		// var wnfatalper=(total!=0)?(total/total)*100:0;

		jQuery('#wfatalper'+parameterId).text(wfatalper.toFixed(2)+'%')

		// jQuery('#wnfatalper'+parameterId).text(wnfatalper+'%')

		totalfun(result)

		

	}

	

	jQuery(document).on('ready',function(e){

		jQuery('#collection_manager-select').val("{{$result->collection_manager_id}}")

		var type="{{$data->type}}"

		console.log(type)

		if(type=='branch'){

			gerProduct('{{$result->branch_id}}','branch')

			editBranch('{{$result->branch_id}}','{{$result->product_id}}','branch')

		}

		else if(type=='agency'){

			gerProduct('{{$result->agency_id}}','agency')

			editBranch('{{$result->agency_id}}','{{$result->product_id}}','agency')

		}

		else if(type=='branch_repo'){

			gerProduct('{{$result->branch_repo_id}}','branch_repo')

			editBranch('{{$result->branch_repo_id}}','{{$result->product_id}}','branch_repo')

		}

		else if(type=='agency_repo'){

			gerProduct('{{$result->agency_repo_id}}','agency_repo')

			editBranch('{{$result->agency_repo_id}}','{{$result->product_id}}','agency_repo')

		}

		else{

			gerProduct('{{$result->yard_id}}','yard')

			editBranch('{{$result->yard_id}}','{{$result->product_id}}','yard')

		}

	})

	

	function gerProduct(id,type){

		var saveData = jQuery.ajax({

			type: 'get',

			url: "{{url('get_product')}}/"+id+'/'+type,

			dataType: "text",

			success: function(resultData) { 

				var data='';

				var obj=JSON.parse(resultData)

				obj.data.forEach(function(item, index){

					data=data+'<option value="'+item.id+'"'+(item.id=={{$result->product_id}}?'selected':'')+' >'+item.name+'</option>'

					// data=data+'<option value="'+item.id+'">'+item.name+'</option>'

					// data=dadata=data+'<option value="'+item.product.id+'">'+item.product.name+'</option>'

				});

				jQuery('#product').show();

				jQuery('#productSelect').attr('data-type',type)

				jQuery('#productSelect').attr('data-id',id)

				jQuery('#productSelect').html(data)

				// window.location='{{ url("audited_list")}}'

			}

		});

		saveData.error(function() { alert("Something went wrong"); });

	}

	function editBranch(id,product_id,type){

		var saveData = jQuery.ajax({

			type: 'get',

			url: "{{url('get_branch_detail_qc')}}/"+id+'/'+type+'/'+product_id,

			dataType: "text",

			success: function(resultData) { 

				console.log(resultData)

				jQuery('#data').html(resultData)

				jQuery('#collection_manager-select').val("{{$result->collection_manager_id}}")

				var code =jQuery('#collection_manager-select').find(':selected').data('code');

				var bucket =jQuery('#collection_manager-select').find(':selected').data('bucket');

				jQuery('input[name=Collection_Manager_bucket]').val(bucket)

				jQuery('input[name=Collection_Managercode]').val(code)

				

				@if($result->collection_manager_email != '')

					var html='<div>\

						<div>\

						{{$result->collectionuser->name}} ({{$result->collection_manager_email}}) change by {{$result->qa_qtl_detail->name}}\

						</div>\

						<div>\

							<button class="btn btn-sm btn-success" onclick="SaveData(`{{$result->collection_manager_email}}`,{{$result->id}},`collection`)">Accept</button>\

							<button class="btn btn-sm btn-danger" onclick="RejectData(`{{$result->collection_manager_email}}`,{{$result->id}},`collection`)">Reject</button>\

						</div>\

					</div>'

					jQuery('#error').show();

					jQuery('.error').show();

					jQuery('#error').html(html)

				@endif

				@if($result->agency_manager_email != '')

				var htmla='<div>\

						<div>\

							{{$result->agencyuser->name}} ({{($result->agency_manager_email)}}) change by {{$result->qa_qtl_detail->name}}\

						</div>\

						<div>\

							<button class="btn btn-sm btn-success"  onclick="SaveData(`{{$result->agency_manager_email}}`,{{$result->id}},`agency`);">Accept</button>\

							<button class="btn btn-sm btn-danger" onclick="RejectData(`{{$result->agency_manager_email}}`,{{$result->id}},`agency`);">Reject</button>\

						</div>\

					</div>'

					jQuery('#agency_error').show();

					jQuery('.agency_error').show();

					jQuery('#agency_error').html(htmla)

				@endif

				@if($result->yard_manager_email != '')

				var htmlb='<div>\

						<div>\

							{{$result->yarduser->name}} ({{($result->yard_manager_email)}}) change by {{$result->qa_qtl_detail->name}}\

						</div>\

						<div>\

							<button class="btn btn-sm btn-success"  onclick="SaveData(`{{$result->yard_manager_email}}`,{{$result->id}},`yard`)">Accept</button>\

							<button class="btn btn-sm btn-danger" onclick="RejectData(`{{$result->yard_manager_email}}`,{{$result->id}},`yard`)">Reject</button>\

						</div>\

					</div>'

					jQuery('#yard_error').show();

					jQuery('.yard_error').show();

					jQuery('#yard_error').html(htmlb)

				@endif

			}

		});

		saveData.error(function() { alert("Something went wrong"); });

	}

	
	jQuery('select[name="agency"], select[name="level_4[]"], select[name="level_5[]"], select[name="product"], select[name="sub_product[]"]').each(function() {
    if (!$(this).attr('id')) { // Check if id is not present
        $(this).on('mousedown', function(event) {
            event.preventDefault(); // Prevent default behavior
        });
        $(this).css({
            'pointer-events': 'none',
            'cursor': 'not-allowed'
        });
    }
});
</script>

@endsection