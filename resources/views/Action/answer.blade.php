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
					<strong class="card-title">Answer Action Plan List</strong>
				</div>
				<div class="card-body">
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
						<th>
							Action Question
						</th>
						<th>
							Action Answer
						</th>
						<th title="Field #7">
							Action
						</th>
				</tr>
			</thead>
			<tbody>
				@foreach($data as $row)
				@php
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
					$subs='';
					$ans='';
					foreach($row->answers as $k=>$val){
						$ans=$ans."<li>".$val->answer."</li>";
						$subs=$subs."<li>".$val->sub->question."</li>";
					}
				@endphp
				<tr>
					<td>{{$loop->iteration}}</td>
					<td>{{$row->audit->created_at}}</td>
					<td>{{$row->audit->qmsheet->lob ?? ''}}</td>
					<td>{{$row->audit->qmsheet->type ?? ''}}</td>
					<td>{{$name}}</td>
					<td>{{$row->audit->product->name ?? ''}}</td>
					<td><ul>{!! $subs !!}</ul></td>
					<td><ul>{!! $ans !!}</ul></td>
					<td nowrap>
						@if($row->status<3)
						<a href="{{url('action/'.Crypt::encrypt($row->id).'/view')}}" class="btn btn-sm btn-clean btn-icon btn-icon-md" title="Rise Action Alert">
								<i class="fa fa-eye"></i>
						</a>
						@endif

                    </div>

                </td>
			</tr>
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