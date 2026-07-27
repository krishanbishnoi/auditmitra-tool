@extends('layouts.master')

@section('sh-title')
Audit Alert Box
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
				<div class="card-header">
					<strong class="card-title">Beat plan List</strong>
				</div>
				<div class="card-body">

					<!--begin: Datatable -->
					<table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">
						<thead>
							<tr>
								<th title="Field #1">#</th>
								<th title="Field #2">
									Name
								</th>
								<th title="Field #2">
									Visit List
								</th>
								<th title="Field #2">
									Agency
								</th>
								<th title="Field #2">
									Products
								</th>
								<th title="Field #2">
									Collection Manager
								</th>
								<th title="Field #7">
									Actions
								</th>

							</tr>
						</thead>
						<tbody>
							@foreach($beatplan as $row)
								<tr>
									<td>{{$loop->iteration}}</td>
									<td>
										{{$row->name}}
									</td>
									<td nowrap>
										@foreach($row->sub as $value)
											{{($value->branch != null) ? $value->branch->name : ''}}({{$value->date}} to
											{{$value->to_date}})
										@endforeach

									</td>
									<td>
										@foreach($agency as $value)
											@foreach($row->sub as $val)
												@if($val->agencies == $value->id)
													{{$value->name}}
												@endif
											@endforeach
										@endforeach

									</td>
									<td>
										@foreach($product as $value)
											@foreach($row->sub as $val)
												@if(json_decode($val->product) == $value->id)
													{{$value->name}}
												@endif
											@endforeach
										@endforeach
									</td>
									<td>
									@foreach($users as $value)
											@foreach($row->sub as $val)
												@if($val->collection_manager == $value->id)
													{{$value->name}}
												@endif
											@endforeach
										@endforeach
									</td>
									<td nowrap>
										<div style="display: flex;">
											{{Form::open(['method' => 'delete', 'route' => ['beat_plan.destroy', Crypt::encrypt($row->id)], 'onsubmit' => "delete_confirm()"])}}
											<button class="btn btn-sm btn-clean btn-icon btn-icon-md" title="View">
												<i class="fa fa-trash"></i>
											</button>
											</form>
											<a href="{{url('beat_plan/' . Crypt::encrypt($row->id) . '/edit')}}"
												class="btn btn-sm btn-clean btn-icon btn-icon-md" title="View">
												<i class="fa fa-edit"></i>
											</a>

										</div>

									</td>
								</tr>
							@endforeach

						</tbody>
					</table>
					<!--end: Datatable -->
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
@endsection