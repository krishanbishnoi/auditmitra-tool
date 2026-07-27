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
					<strong class="card-title">Intimation List</strong>
				</div>
				<div class="card-body">

					<!--begin: Datatable -->
					<table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">
						<thead>
							<tr>
								<th title="Field #1">#</th>
								<th title="Field #2">
									Agency Name
								</th>
								<th title="Field #2">
									Audit Date
								</th>
								<th title="Field #2">
									Audit Month
								</th>
								<th title="Field #2">
									Auditor Name
								</th>
								<th title="Field #2">
									Agency
								</th>
								<th title="Field #2">
									Agency Email
								</th>
								<!-- <th title="Field #2">
									Products
								</th>
								<th title="Field #2">
									Products Attributes
								</th> -->
								<th title="Field #7">
									Actions
								</th>

							</tr>
						</thead>

						<tbody>

							@foreach($intimationmail as $row)							
							<tr>
								<td>{{$loop->iteration}}</td>
								<td>
									@php
									$name=DB::table('users')->where('id',$row->user_id)->pluck('name')->first();
									@endphp
									{{$name}}
								</td>
								<td nowrap>
									{{$row->audit_date}}
								</td>
								<td>
									{{$row->process_review_month}}
								</td>
								<td>
									{{$row->auditor}}
								</td>
								<td>
									@php 
										$agency_name = DB::table('agencies')->where('id', $row->agency)->pluck('name')->first();
										$agency_loc=DB::table('agencies')->where('id', $row->agency)->pluck('location')->first();
									@endphp
									{{$agency_name ?? '' }} ({{$agency_loc??''}})
								</td>
								<td>
									{{$row->agency_email ?? ''}}
								</td>
								<!-- <td>
									{{$row->product->name ?? '' }}
								</td>
								<td>
									{{$row->productattribute->product_attribute_name ?? ''}}
								</td> -->
								<td nowrap>
									<div style="display: flex;">
										<!-- {{Form::open(['method' => 'delete', 'route' => ['intimation_mail.destroy', Crypt::encrypt($row->id)], 'onsubmit' => "delete_confirm()"])}}
										<button class="btn btn-sm btn-clean btn-icon btn-icon-md" title="View">
											<i class="fa fa-trash"></i>
										</button>
										</form> -->

										<a href="{{ route('intimation_mail.viewMail', $row->id) }}"
											class="btn btn-sm btn-clean btn-icon btn-icon-md" title="View">
											<i class="fa fa-eye"></i>
										</a>
										<!-- <a href="{{url('intimation_mail/' . Crypt::encrypt($row->id) . '/edit')}}"
														class="btn btn-sm btn-clean btn-icon btn-icon-md" title="View">
														<i class="fa fa-edit"></i>
													</a> -->

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