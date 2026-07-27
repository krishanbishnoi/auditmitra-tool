@extends('layouts.master')
@section('title', '| Users')
@section('css')

<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">

@endsection

@section('content')

<div class="row">
	<div class="col-lg-12" style="margin-top:10px"></div>
</div>

<div class="animated fadeIn">
	<div class="row">
		<div class="col-lg-12">
			<div class="card">
				<div class="card-header">
					<strong class="card-title">National Collection Manager List</strong>
					<a href="javascript:history.back()" class="float-right">
						<i class="fa fa-arrow-circle-left" style="font-size: 34px;"></i>
					</a>
				</div>

				<div class="card-body">
					@if(session('error'))
					    <div class="alert alert-danger">
					        {{ session('error') }}
					    </div>
					@endif

					<table class="table table-striped table-bordered table-hover" id="kt_table_1">
	                    <thead>
	                        <tr>
	                            <th>Manager Name</th>
	                            <th>Audit Count of Issues</th>
	                            <th>Retail Reject</th>
	                            <th>Retail Pending</th>
	                            <th>Retail Approved</th>
	                            <th>Credit Card Reject</th>
	                            <th>Credit Card Pending</th>
	                            <th>Credit Card Approved</th>
	                            <th>Retail/Card Reject</th>
	                            <th>Retail/Card Pending</th>
	                            <th>Retail/Card Approved</th>
	                        </tr>
	                    </thead>
	                    <tbody>
	                    @if($nationalCollManagerAuditData->isEmpty())
	                        <tr>
	                            <td colspan="11" class="text-center text-danger">No data available.</td>
	                        </tr>
	                    @else
	                        @foreach($nationalCollManagerAuditData as $data)
	                        <tr>
	                            <td>{{ $data->manager_name }}</td>
	                            <td>{{ $data->audit_count }}</td>
	                            <td>{{ $data->retail_rejected ?? '' }}</td>
	                            <td>{{ $data->retail_pending ?? '' }}</td>
	                            <td>{{ $data->retail_approved ?? '' }}</td>
	                            <td>{{ $data->credit_card_rejected ?? '' }}</td>
	                            <td>{{ $data->credit_card_pending ?? '' }}</td>
	                            <td>{{ $data->credit_card_approved ?? '' }}</td>
	                            <td>{{ $data->retail_card_rejected ?? '' }}</td>
	                            <td>{{ $data->retail_card_pending ?? '' }}</td>
	                            <td>{{ $data->retail_card_approved ?? '' }}</td>
	                        </tr>
	                        @endforeach
	                    @endif    
	                    </tbody>	
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection

@section('js')

<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Include DataTables JS -->
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        $('#kt_table_1').DataTable({
            "paging": true,       
            "searching": true,    
            "info": true,     
            "autoWidth": false,  
            "responsive": true
        });
    });
</script>

<script>
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
