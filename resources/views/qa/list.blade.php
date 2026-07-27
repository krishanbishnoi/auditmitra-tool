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

					<strong class="card-title">Sheet List</strong>

				</div>

				<div class="card-body">



					<!--begin: Datatable -->

					<table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">

						<thead>

							<tr>

									<th title="Field #1">#</th>

									<th title="Field #2">

										 Sheet Name

									</th>

									<th title="Field #2">

										Lob

									</th>

									<th title="Field #2">

										Sheet Type

									</th>

									<th title="Field #7">

										Actions

									</th>

							</tr>

						</thead>

						<tbody>

							{{-- {{dd($data)}} --}}

							@foreach($data as $row)

							<tr>

								<td>{{$loop->iteration}}</td>

								<td>

									<!-- {{($row->sheet!=null)?$row->sheet->name:''}} -->
									{{$row->name ??''}}


                                </td>

								<td>

									<!-- {{($row->sheet!=null)?$row->sheet->lob:''}} -->
									{{$row->lob ??''}}

                                </td>

								<td>

									<!-- {{($row->sheet!=null)?$row->sheet->type:''}} -->

									{{$row->type ??''}}

                                </td>

                                {{-- <td>

									{{($row->user!=null)?$row->user->name:''}}

								</td> --}}

								<td nowrap>

									<div style="display: flex;">

										{{-- {{Form::open([ 'method'  => 'delete', 'route' => [ 'allocation.destroy', Crypt::encrypt($row->id) ],'onsubmit'=>"delete_confirm()"])}}

										<button class="btn btn-sm btn-clean btn-icon btn-icon-md" title="View">

											<i class="fa fa-trash"></i>

										</button>

									</form> --}}

									<a href="{{url('audit_sheet/'.Crypt::encrypt($row->id))}}" class="btn btn-sm btn-clean btn-icon btn-icon-md" title="View">

										<i class="fa fa-eye"></i>

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