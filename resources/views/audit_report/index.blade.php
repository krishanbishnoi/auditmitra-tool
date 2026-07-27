@extends('layouts.master')



@section('title', '| Audit Report')


@section('content')
@if(!isset($report_list))
<nav>
   <ol class="breadcrumb" style="background: #3A4248;">
       <li class="breadcrumb-item active" aria-current="page"><a  style="color:#20a8d8;" href="{{url('createReports?branch='. $branch .'&state='. $state .'&year=' . $year.'&quarter='.$quarter)}}">Create Report</a></li>
      </ol>
    </nav>
   @endif

 <div class="card-body">

					<table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">

						<thead>

							<tr>
									<th scope="col">State</th>
									<th scope="col">Branch</th>
									<th scope="col">Year</th>
                                    <th scope="col">Quarter</th>
                                    <th scope="col">User Name</th>
                                    <th scope="col">Last Updated Date</th>
									<th scope="col">Actions</th>

							</tr>

						</thead>

						<tbody>
							 @if(isset($report_list))
							<tr scope="row">
								<td>
									{{$getState->name}}
								</td>

								<td>{{$getBranch->name}}
                                </td>
								<td>{{isset($year)?$year:''}}</td>

								<td><?php if($quarter == 1){
									echo 'Aprl-June';}elseif($quarter == 2){echo 'July-Spt';}elseif($quarter == 3){echo 'Oct-Dec';}else{echo 'Jan-Mar';}?></td>
								<td>{{isset($getUser)? $getUser->name : ''}}</td>
								<td>{{$report_list->updated_at}}</td>
								<td nowrap>
									<a href="#" class="btn btn-xs btn-info" title="View">
										<i class="fa fa-edit"></i>

                                    </a>

								</td>

							</tr>
							@else
							<tr scope="row">NO Data Found</tr>
                             @endif
						</tbody>

					</table>

				</div>

@endsection