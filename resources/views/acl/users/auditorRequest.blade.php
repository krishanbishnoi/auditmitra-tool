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

					<strong class="card-title">User List</strong>
<!-- 
					<a class="btn btn-primary btn-sm float-right" href="{{route('bulkDeactivate')}}">De-Activate
						user(Bulk)</a>
					<a class="btn btn-primary btn-sm float-right" style="margin-right: 5px"
						href="{{route('userUpload')}}">Import Users (Create bulk user)</a>

					<a class="btn btn-primary btn-sm float-right" style="margin-right: 5px"
						href="{{route('excelDownloadUser')}}" target="_blank">Export Users</a> -->

				</div>

				<div class="card-body">

					<table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">

						<thead>

							<tr>

								<th scope="col">#</th>

								<th scope="col">

									Name

								</th>

								<th scope="col">

									Audit {{$agencyLabel}}

								</th>

								<th scope="col">

									Role

								</th>

								<th scope="col">

									Email

								</th>

								<th scope="col">

									Phone

								</th>
								<th scope="col">

									Status

								</th>



								<th scope="col">

									Actions

								</th>

								<th scope="col">
									Disable User
								</th>

								<th>
									Is Approved
								</th>
							</tr>

						</thead>

						<tbody>

							@foreach($data as $row)

								<tr scope="row">

									<td>{{$loop->iteration}}</td>
									<td>{{$row->name}}</td>
									<td>{{App\User::where('email',$row->created_by)->pluck('name')->first()}}</td>
									<td>
										@if($row->roles->isNotEmpty())
											{{ $row->roles->first()->name }}
										@endif
									</td>
									<td>{{$row->email}}</td>
									<td>{{$row->mobile}}</td>
									{{-- <td>{{ $row->roles()->pluck('name')->implode(' ') }}</td>Retrieve array of roles
									associated to a user and convert to string --}}
									<td>
										@if($row->active_status == 0)
											Activated
										@else
											De-Activated
										@endif

									</td>

									<td nowrap>

										<!-- <div style="display: flex;">

													{{Form::open([ 'method'  => 'delete', 'route' => [ 'user.destroy', Crypt::encrypt($row->id) ],'onsubmit'=>"delete_confirm()"])}}

													<button class="btn btn-sm btn-clean btn-icon btn-icon-md" title="View">

														<i class="la la-trash"></i>

													</button>

												</form> -->

										<a href="{{url('user/' . Crypt::encrypt($row->id) . '/edit')}}"
											class="btn btn-xs btn-info" title="Edit">

											<i class="fa fa-edit"></i>

										</a>



										<!-- </div> -->

									</td>

									<td nowrap>
										<a class="btn btn-xs btn-info" onclick="block_user('{{Crypt::encrypt($row->id)}}')"
											title="View">
											<i class="fa fa-ban"></i>
										</a>
									</td>

									<td>
										
										<form action="{{ route('auditor.status', Crypt::encrypt($row->id)) }}"
											method="POST" style="display:inline;">
											@csrf
											<input type="hidden" name="is_approved" value="1"> 
											<button type="submit" class="btn btn-xs btn-success" title="Approve Auditor">Approve</button>
										</form>

										
										<form action="{{ route('auditor.status', Crypt::encrypt($row->id)) }}"
											method="POST" style="display:inline;">
											@csrf
											<input type="hidden" name="is_approved" value="2"> 
											<button type="submit"  class="btn btn-xs btn-danger" title="Reject Auditor">Reject</button>
										</form>
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

	jQuery(document).on('ready', function () {

		jQuery('#kt_table_1').DataTable();

	})

</script>

<script type="text/javascript">
	function block_user(id) {
		if (confirm("Are you sure you want to block?")) {

			var link = '/user/' + id + '/disable'
			location.href = link;
		}
	}
</script>

@endsection