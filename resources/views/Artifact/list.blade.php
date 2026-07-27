@extends('layouts.master')
<link rel="stylesheet" href="{{ asset('css/jquery.dataTables.min.css') }}">
@section('title', '| Red alerts')



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

					<strong class="card-title">Artifact List</strong>

				</div>

				<div class="card-body">
                    <div class="table-responsive">
		              <table id="master_1" class="table table-bordered data-table">
		                <thead>
		                 <tr>
		                  <th>
		                    #
		                  </th>
		                  <th>Sheet Name</th>
		                  <th>Parameter name</th>
		                  <th>Sub parameter name</th>
		                  <th>Download Files</th>
		                  <th>Action</th>
		                </tr>
		              </thead>
		            </table>
		          </div>
				</div>

			</div>

		</div>

	</div>

</div>

@endsection

@section('js')

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>
<!-- 
    <link href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css" rel="stylesheet"/> -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>

  <!--   <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script> -->
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
<script type="text/javascript">
 var url = "{{ URL::to('artifact')}}";
 var table = $('#master_1').DataTable({
  "paging": true,
  "ordering": true,
  "info": true,
  "lengthMenu": [[10, 15, 25, -1], [10, 15, 25, "All"]],
  "pageLength": 15,
  "processing": true,
  "serverSide": true,
  "ajax": {"url": url},
  columnDefs: [
  {"orderable": false,"targets":  [0]},
  {"orderable": true, "targets":  [1]},
  {"orderable": true, "targets":  [2]},
  {"orderable": true, "targets":  [3]},
  {"orderable": true, "targets":  [4]},
  {"orderable": false, "targets": [5]}
  ],
  columns: [
   {"data": "DT_RowIndex","searchable": false},
  {"data": "qsheet","name":'qm_sheets.name'},
  {"data": "parameter","name":'qm_sheet_parameters.parameter'},
  {"data": "sub_parameter","name":'qm_sheet_sub_parameters.sub_parameter'},
  {"data": "file","name":'artifacts.file'},
  {"data": "id","name":'artifacts.id'},
  ],
  "rowCallback": function (row, data, index) {
        if (data.file == null){
        $('td:eq(4)', row).html(
          '<a href="#">File not Uploaded</a>'
          );
    }
    else{
      $('td:eq(4)', row).html(

      	

      	 '<a href="{{URL::to('/')}}/storage/app/'+data.file+'" target="_blank">Download</a>'
      	
        // '<a href="'+ 'categorymasterchangestatus'+'/'+ data.id+'/'+data.status+'" class="btn btn-sm btn-danger">Inactive</a>'
        );
  }
    $('td:last-of-type', row).html(
      '<a class="btn btn-icon-only green" title="Edit" href="' + 'artifact' + '/' + data.id +'"><i class="mdi mdi-table-edit"></i></a>'
      );
  },
});


</script>


@endsection