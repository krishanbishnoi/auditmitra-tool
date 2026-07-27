@extends('layouts.master')

@section('sh-title')
Audited
@endsection

@section('sh-detail')
Call
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
				<div class="card-header">
					<strong class="card-title">Action Plan List</strong>
				</div>
				<div class="card-body">
					{{-- <form method="post" action="{{route('audited_list')}}">
						<div class="row">
							@csrf
							<div class="col-md-3 form-group">
								<label>Lob Name*</label>
								<select name="lob" class="form-control">
								<option value="">Choose Lob Name</option>
								<option value="collection">Collection</option>
								<option value="commercial_vehicle">Commercial Vehicle</option>
								<option value="rural">Rural</option>
								<option value="alliance">Alliance</option>
								<option value="credit_card">Credit Card</option>
								</select>
							</div>
							<div class="col-md-3 form-group">
								<label>Start Date*</label>
								<input name="start_date" type="text" class="form-control"/>
							</div>
							<div class="col-md-3 form-group">
								<label>End Date*</label>
								<input name="end_date" type="text" class="form-control"/>
							</div>
							<div class="col-md-3 form-group">
								<input name="search" type="submit" class="btn btn-sm btn-primary mt-4" value="Search"/>
							</div>
						</div>
					</form> --}}
		<!--begin: Datatable -->
		<table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">
			<thead>
				<tr>
						<th title="Field #1">#</th>
						<th title="Field #7">
							Audit Date
						</th>
						<th title="Field #2">
							lob
						</th>
						<th title="Field #2">
							Audit for
						</th>
						<th title="Field #2">
							Branch Name
						</th>
						<th title="Field #2">
							Product Name
						</th>
						<th title="Field #7">
							Score
						</th>
						<th title="Field #7">
							Percentage
						</th>
						<th>
							Qc status
						</th>
						<th>
							Checked By
						</th>
						<th title="Field #7">
							Action
						</th>
				</tr>
			</thead>
			<tbody>
				@foreach($data as $row)
					@if($row->audit!=null)
						@php
						ini_set('memory_limit', '-1');
							$name='';
							switch ($row->audit->qmsheet->type) {
								case 'agency':
									$name=$row->audit->agency->name ?? '';
									break;
								case 'branch':
									$name=$row->audit->branch->name ?? '';
									break;
								case 'repo_yard':
									$name=$row->audit->yard->name ?? '';
									break;
								
							}
							$status='';
							switch ($row->status) {
								case '1':
									$status='Pass With Edit';
									break;
								case '2':
									$status='Pass';
									break;
								case '3':
									$status='Faild';
									break;
								
							}
							$total=0;
							$point=0;
							$per=0;
							foreach($row->audit->audit_results as $value){
								$total=$total+(($value->score!='N/A')?(int)($value->sub_parameter_detail->weight ?? 0) : 0);
								$point=$point+(($value->score!='N/A')?(int)$value->score : 0);
								// dump($point,$total);
							}
							// dd($point,$total);
							if($total!=0){
								$per=($point/$total)*100;
							}
						@endphp
						<tr>
							<td>{{$loop->iteration}}</td>
							<td>{{$row->audit->created_at}}</td>
							<td>{{$row->audit->qmsheet->lob ?? ''}}</td>
							<td>{{$row->audit->qmsheet->type ?? ''}}</td>
							<td>{{$name}}</td>
							<td>{{$row->audit->product->name ?? ''}}</td>
							{{-- <td>{{$row->audit->raw_data->agent_name}}</td> --}}
							<td>{{($row->audit->is_critical==1)?0:$row->audit->overall_score.""}}</td>
							<td>{{round($per,2)}} %</td>
							{{-- <td>{{$row->audit->overall_score}} </td> --}}
							<td>{{$status}} </td>
							<td>{{$row->user->name ?? ''}} </td>
							<td nowrap>
								@if($row->status<3)
								<a href="{{url('action/'.Crypt::encrypt($row->audit_id).'/alert')}}" class="btn btn-sm btn-clean btn-icon btn-icon-md" title="Rise Action Alert">
										<i class="fa fa-bell"></i>
								</a>
								@endif

							</div>

						</td>
					</tr>
					@endif
				@endforeach
			@if(count($data)==0)
				<tr>
					<td  colspan="9" class="text-center">No Record found</td>
				</tr>
			@endif
        </tbody>
    </table>
</div>
</div>
</div>
</div>
</div>
@endsection
@section('css')
@include('shared.table_css');
@endsection
@section('js')
@include('shared.table_js');
<script>
	jQuery(document).on('ready',function(){
		jQuery('input[name=start_date]').datepicker();
		jQuery('input[name=end_date]').datepicker();
	})
</script>
@endsection