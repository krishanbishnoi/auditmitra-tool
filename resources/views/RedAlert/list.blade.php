@extends('layouts.master')



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

					<strong class="card-title">Red Alerts List</strong>

				</div>

				<div class="card-body">

					<table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">

						<thead>

							<tr>

									<th scope="col">#</th>

									<!-- <th scope="col">

										Sheet Name

									</th> -->
									<th scope="col">

										Audit Type

									</th>
									<th scope="col">

										Branch

									</th>

									<th scope="col">

										Collection Agency Name

									</th>

									<th scope="col">

										Alert Raised Date

									</th>

									<th scope="col">

										Alert Raised By

									</th>

									<th scope="col">

										Alert Raised By(QC/QA)

                                    </th>

                                    <th scope="col">

										Alert Feedback Received Date

                                    </th>

                                    <th scope="col">

										Alert Approved Date

                                    </th>

                                    <th scope="col">

										Collection Manager Name

                                    </th>

                                    <th>
                                    	Product
                                    </th>

                                    <th>
                                    	Recipient Name
                                    </th>

                                    <th>
                                    	Recipient Designation
                                    </th>

									<th scope="col">

										Parameter name

                                    </th>

                                    <th scope="col">

										Sub parameter name

                                    </th>

                                    <th scope="col">

										Remark

                                    </th>

                                    <th scope="col">

										download files

									</th>

									<!-- <th scope="col">

										Actions

									</th> -->

							</tr>

						</thead>

						<tbody>

                            @foreach($data as $k=>$row)

                            

							<tr scope="row">

								<td>{{$k+1}}</td>

								<td>

									{{$row->sheet->type}}

								</td>
								<td>

									{{ $row->audit->branch->name ?? '-'}}

								</td>

								<td>

									{{ $row->audit->agency->name ?? '-'}}

								</td>

								<td>

									{{ $row->created_at}}

								</td>

								<td>

									{{$row->audit->qa_qtl_detail->name ?? ''}}

								</td>
									
								<td>
									{{ '-' }}
								</td>

								<td>
									{{$row->answer->created_at ?? '-'}}
								</td>

								<td>
									{{ '-' }}
								</td>

								<td>
									{{$row->audit->user->name ?? ''}}
								</td>

								<td>
									{{$row->audit->product->name ?? ''}}
								</td>

								<td>
									{{ '-' }}
								</td>

								<td>
									{{ '-' }}
								</td>								

								<td>

									{{$row->parameter->parameter}}

                                </td>

                                <td>

									{{$row->subparameter->sub_parameter}}

                                </td>

                                <td>

									{{$row->message}}

                                </td>

                                <td class="text-center">

                                    @if($row->file==null)

                                        <a href="#">File not Uploaded</a>

                                    @else

                                        <a href="{{url('download-file/'.Crypt::encrypt($row->id))}}" target="_blank">Download</a>

                                    @endif

								</td>

                                <td nowrap>

									<!-- <div style="display: flex;">

										{{Form::open([ 'method'  => 'delete', 'route' => [ 'user.destroy', Crypt::encrypt($row->id) ],'onsubmit'=>"delete_confirm()"])}}

										<button class="btn btn-sm btn-clean btn-icon btn-icon-md" title="View">

											<i class="la la-trash"></i>

										</button>

									</form> -->

									 @if($row->audit_id!=0 || $row->audit_id!=null)

									{{-- <a href="{{url('red-alert/'.Crypt::encrypt($row->audit_id).'/edit')}}" class="btn btn-xs btn-info" title="View">

										<i class="fa fa-eye"></i>

                                    </a> --}}

									@endif 

									{{-- <a href="{{url('red-alert/'.Crypt::encrypt($row->id).'/edit')}}" class="btn btn-xs btn-info" title="View">

										<i class="fa fa-edit"></i>

                                    </a>

                                    <a href="{{url('red-alert/'.Crypt::encrypt($row->id))}}" class="btn btn-xs btn-danger" title="View">

										<i class="fa fa-trash"></i>

									</a> --}}



									<!-- </div> -->

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

@section('js')

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>

    <link href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css" rel="stylesheet"/>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>

    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>

<script>

jQuery(document).ready(function() {

    jQuery('#kt_table_1').DataTable();

});

</script>

@endsection