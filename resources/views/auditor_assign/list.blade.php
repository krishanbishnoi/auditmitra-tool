@extends('layouts.master')
@section('title', '| Users')

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
					<strong class="card-title">Auditor Assign For Audit List</strong>
					<a class="btn btn-info btn-sm float-right" style="margin-right: 5px" href="{{route('auditorassignupload')}}">Auditor Assign Upload Sheet</a>
				</div>

				<div class="card-body">
					@if(session('error'))
					    <div class="alert alert-danger">
					        {{ session('error') }}
					    </div>
					@endif
				
					<table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">
						<thead>
							<tr>
								<th scope="col">Sr.No.</th>
								<th class="font-weight-bold" scope="col">Final Agency Name</th>
								<th scope="col">Agency Code</th>
								<!-- <th scope="col">Product</th> -->
								<th scope="col">Location</th>
								<th scope="col">Process Review Agency</th>
								<th scope="col">Process Review Period</th>
								<th scope="col">Auditor Name</th>
								<th scope="col">Audit Date</th>	
								<!-- <th scope="col">Auditor Email</th> -->
								<th scope="col">Actions</th>
							</tr>
						</thead>

						<tbody>
						@foreach($auditorAssign as $row)
							<tr scope="row">
								<td>{{$loop->iteration}}</td>
								<td class="font-weight-bold" style="width: 18%">{{$row->final_agency_name}}</td>
								<td>{{$row->agency_code}}</td>
								<td>{{$row->location}}</td>
								<td>{{$row->process_review_agency}}</td>
								<td>{{$row->process_review_period}}</td>
								<td>{{$row->auditor_name}}</td>
								<td>{{$row->audit_date}}</td>
								
								
								<td nowrap>
								    <div class="btn-group">
								    	<a href="{{url('auditor_assign/'.Crypt::encrypt($row->id).'/edit')}}" class="btn btn-xs btn-info mr-1" title="View">
										<i class="fa fa-edit"></i>
                                    	</a>

                                    	<a href="{{url('auditor_assign/'.Crypt::encrypt($row->id))}}" class="btn btn-xs btn-success mr-1" title="View">
										<i class="fa fa-eye"></i>
                                    	</a>

								        <!-- <form action="{{ route('auditor_assign.destroy', Crypt::encrypt($row->id)) }}" method="POST" style="display:inline;" onsubmit="return confirmDelete();">
								            @csrf
								            @method('DELETE')
								            <button type="submit" class="btn btn-xs btn-danger mr-1" title="Delete">
								                <i class="fa fa-trash"></i>
								            </button>
								        </form> -->
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

<script>
    function confirmDelete() {
        return confirm('Are you sure you want to delete this item?');
    }
</script>

@endsection