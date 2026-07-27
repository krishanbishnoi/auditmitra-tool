@extends('layouts.master')



@section('title', '| Yards')



<!-- @section('sh-detail')

Users

@endsection -->



@section('content')

<div class="row">

		<div class="col-lg-12" style="margin-top:10x">

		</div>

</div>

<div class="animated fadeIn">

	<div class="row">

		<div class="col-lg-12">

			<div class="card">

				@if(session('error'))
				    <div class="alert alert-danger">
				        {{ session('error') }}
				    </div>
				@endif

				@if(session('success'))
				    <div class="alert alert-success">
				        {{ session('success') }}
				    </div>
				@endif


				<div class="card-header">
				<h3>
					@if($status == 0)
						
						<strong class="card-title">Send for Closure Audits</strong>
					@elseif($status == 1)
					<strong class="card-title">Approved Audit List</strong>
						
					@elseif($status == 2)
					<strong class="card-title">Received Audit List</strong>
						
					@endif
				</h3>

				</div>

				<div class="card-body">

					<table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">
						<thead>
							<tr>
							<th title="Field #1">#</th>
							<th title="Field #1">Audit ID</th>
							<th title="Field #1">Month</th>

							<th title="Field #2">Audit Date</th>

							<th title="Field #3">Lob</th>

							<th title="Field #4">State</th>

							<th title="Field #4">Location</th>
							<th title="Field #6">Product</th>

							<th title="Field #7">Audit Type</th>

							<th title="Field #8">Agency Name</th>
							<th title="Field #8">Agency Code</th>
							<th title="Field #9">Collection Manager</th>

							<th title="Field #10">Collection Manager Email</th>

							<!-- <th title="Field #19">Collection Manager Emp id</th> -->

							<th title="Field #11">Auditor Name</th>

							<th title="Field #12">Last Modified Date</th>
							<th title="Field #19">Unsatisfactory Parameters Count</th>
							
							<th title="Field #18">Closure Status</th>
							<!-- @if($status == 0)
							<th title="Field #18">Update Status</th>
							@endif -->
							<th title="Field #18">Actions</th>
							</tr>

						</thead>

						<tbody>
							@foreach($closureData as $row )
							<tr scope="row">
							<td>{{$loop->iteration}}</td>
							<td>00{{$row->id}}</td>
							<td>{{\Carbon\Carbon::parse($row->created_at)->formatLocalized("%b'%y")}}</td>

							<td>{{$row->created_at}}</td>

							<td>{{$row->qmsheet->lob ?? ''}}</td>
							@if($row->agency && $row->agency->state)
    @php
        $state = DB::table('states')->where('id', $row->agency->state)->pluck('name')->first();
    @endphp
@else
    @php
        $state = '';
    @endphp
@endif

<td>{{ $state ?? '' }}</td>

							<td>{{$row->agency_location ?? ''}}</td>
							<td>{{$row->product->name ?? ''}}</td>

							<td>{{$row->qmsheet->type ?? ''}}</td>

							<td>{{$row->agency->name ?? ''}}</td>
							<td>{{$row->agency->agency_id ?? ''}}</td>
							<td>{{$row->user->name ?? ''}}</td>

							<td>{{$row->user->email ?? ''}}</td>

							<!-- <td>{{$row->user->code ?? ''}}</td> -->

							<td>{{$row->qa_qtl_detail->name ?? ''}}</td>

							<td>{{$row->updated_at ?? ''}}</td>
							
							<?php
							        $unsetParams = DB::table('audit_results')
									->where('audit_id', $row->id)
									->where('option_selected', 'Unsatisfactory')
									->join('qm_sheet_sub_parameters', 'audit_results.sub_parameter_id', '=', 'qm_sheet_sub_parameters.id')
									->select('qm_sheet_sub_parameters.sub_parameter', 'audit_results.remark', 'audit_results.created_at')
									->get();
							
							?>
							<td>{{count($unsetParams) ?? ''}}</td>
							<td>
							@if($row->closure_status == 0)
								@if($status == 2)
									<strong class="card-title">Pending</strong>
								@else
									@if(\Carbon\Carbon::parse($row->created_at)->lt(\Carbon\Carbon::now()->subDays($tat)))
                                	    <strong class="card-title" style="color: red;">No Response</strong>
                                	@else
                                	    <strong class="card-title">Pending</strong>
                                	@endif
								@endif
							@elseif($row->closure_status == 1)
								<strong class="card-title">Approved</strong>
								
							@elseif($row->closure_status == 2)
								<strong class="card-title">Rejected</strong>
								
							@endif
						</td>
<!-- 
							<td>{{$status ?? ''}}</td>

							<td>{{$ids[$row->id]->created_at  ?? ''}}</td>

							<td>{{($row->is_critical==1)?0:$row->overall_score.""}}</td>

							<td>{{$row->overall_score}} </td> -->
							
							<!-- @if($row->closure_status == 0)
							<td>
								<a  title="Click To Approve" 
									href='{{route("audit.closure.status", [$row->closure_id, 1])}}'
									onclick="return confirm('Are you sure you want to approve this audit?')"
									class="btn btn-success btn-small">
									<span class="fa fa-check"></span>
								</a>

								<a title="Click To Reject" 
									href='{{route("audit.closure.status", [$row->closure_id, 2])}}'
									onclick="return confirm('Are you sure you want to reject this audit?')"
									class="btn btn-warning btn-small">
									<span class="fa fa-ban"></span>
									
								</a> 
								</td>
							@endif -->
						
							<td nowrap="">
								<div class="btn-group">
									<a class="btn btn-xs btn-success mr-1" href="{{ route('audit.closure.view', ['closure_id' => $row->closure_id]) }}" class="btn btn-sm btn-clean btn-icon btn-icon-md" title="View">
										<i class="fa fa-eye" style="font-size:18px"></i>
									</a>
									
									@if(count($unsetParams) > 0)
									   <a class="btn btn-xs btn-warning" href="{{ route('audit.downloadArtifacts', ['closure_id' => $row->closure_id]) }}" class="btn btn-sm btn-clean btn-icon btn-icon-md" title="Download">
										    <i class="fa fa-download" style="font-size:18px"></i>
										</a>
									@endif
								</div>	
						
							</td>
							</tr>

							@endforeach

						</tbody>

					</table>

				</div>

			</div>

		</div>

	</div>

</div>

@endsection

@section('css')

<!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">

<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.2/css/buttons.dataTables.min.css"> -->

<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.2/css/buttons.dataTables.min.css">

@endsection
@section('js')

<!-- <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script> -->

<!-- <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.2/js/dataTables.buttons.min.js"></script>  -->

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<!--
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.html5.min.js"></script> -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.2/js/dataTables.buttons.min.js"></script>
<script>
	jQuery(document).ready( function () {
    jQuery('#kt_table_1').DataTable();
	} );

</script>

@endsection