@extends('layouts.master')



@section('title', '| Users')



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
				<div class="card-header">
					<strong class="card-title">User Hierarchyt List</strong>
					<a class="btn btn-info btn-sm float-right" style="margin-right: 5px" href="{{route('userhierarchyBulkUpload')}}">Import User Hierarchy</a>

					<!-- <a class="btn btn-primary btn-sm float-right" href="{{route('bulkDeactivate')}}">De-Activate user(Bulk)</a>
					<a class="btn btn-primary btn-sm float-right" style="margin-right: 5px" href="{{route('userUpload')}}">Import Users (Create bulk user)</a>

					<a class="btn btn-primary btn-sm float-right" style="margin-right: 5px" href="{{route('excelDownloadUser')}}" target="_blank">Export Users</a> -->

				</div>

				<div class="card-body">

					<table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">

						<thead>
							<tr>
								<th scope="col">#</th>
								<th class="font-weight-bold" scope="col">Collection Manager</th>
								<th scope="col">Quality Auditor</th>
								<th scope="col">Quality Control</th>
								<th scope="col">Area Collection Manager</th>
								<th scope="col">Regional Collection Manager</th>
								<th scope="col">Zonal Collection Manager</th>
								<th scope="col">National Collection Manager</th>
								<th scope="col">Group Product Head</th>
								<th scope="col">Head of the Collections</th>
								<th scope="col">Status</th>
								<th scope="col">Actions</th>
							</tr>
						</thead>

						<tbody>

							@foreach($data as $row)
							<tr scope="row">
								<td>{{$loop->iteration}}</td>
								<td class="font-weight-bold">
									@if ($row->cm_name)
										{{$row->cm_name}}
									@else
										---
									@endif
								</td>
								<td>
									@if ($row->qa_name)
										{{$row->qa_name}}
									@else
										-
									@endif
								</td>
								<td>
									@if ($row->qc_name)
										{{$row->qc_name}}
									@else
										-
									@endif
								</td>
								<td>
									@if ($row->acm_name)
										{{$row->acm_name}}
									@else
										-
									@endif
								</td>
								<td>
									@if ($row->rcm_name)
										{{$row->rcm_name}}
									@else
										-
									@endif
								</td>
								<td>
									@if ($row->zcm_name)
										{{$row->zcm_name}}
									@else
										-
									@endif
								</td>
								<td>
									@if ($row->ncm_name)
										{{$row->ncm_name}}
									@else
										-
									@endif
								</td>
								<td>
									@if ($row->gph_name)
										{{$row->gph_name}}
									@else
										-
									@endif
								</td>
								<td>
									@if ($row->hc_name)
										{{$row->hc_name}}
									@else
										-
									@endif
								</td>
								<td>
									@if($row->status == 1)
										Activated
									@else 
										De-Activated
									@endif
								</td>

								<td nowrap>
									<div class="btn-group">
									<a href="{{url('userhierarchy/'.Crypt::encrypt($row->id).'/edit')}}" class="btn btn-xs btn-info mr-1" title="View">
										<i class="fa fa-edit"></i>
									</a>

									<form action="{{ url('userhierarchy/' . Crypt::encrypt($row->id)) }}" method="POST" style="display:inline;">
                                      @csrf
                                           @method('DELETE')
                                     <button type="submit" class="btn btn-xs btn-danger mr-1" title="Delete">
                                         <i class="fa fa-trash"></i>
                                       </button>
                                            </form>

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

<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">

@endsection

@section('js')

<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

<script>

	jQuery(document).on('ready',function(){

		jQuery('#kt_table_1').DataTable();

	})

</script>

<script type="text/javascript">
   function block_user(id) {
		if (confirm("Are you sure you want to block?")) {

			var link  = '/user/'+id+'/disable'
			location.href = link;
		}
	}
</script>

@endsection