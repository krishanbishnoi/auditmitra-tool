@extends('layouts.master')
@section('css')

   <!-- {{asset('clientdashboard/images/overall-audit.svg')}} -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

        <link rel="stylesheet" href="{{URL::asset('/public/clientdashboard/css/owl.carousel.min.css')}}">
        <link rel="stylesheet" href="{{URL::asset('/public/clientdashboard/css/style.css')}}">

         <!-- for calender -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .flatpickr-day.sunday {
        background-color: #ffecec !important;
        color: #d00 !important;
        font-weight: bold;
        border-radius: 50% !important;
    }
</style>

   <style>
    body{
        display: block;
    }
   </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        #myChart {
            width: 90%; 
            margin-left: 10px; 
            max-width: 600px; 
            margin-top: 10px; 
            margin-bottom: 50px; 
            float: left; 
            max-height: 250px;
        }

         .vdate{
            width: 105px;
        }

        .tab {
          overflow: hidden;
    /*      border: 1px solid #ccc;*/
          background-color: #21317D;
          display: flex; 
          width: 86%;
          border-radius: 6px;
        }

        .tab button {
          background-color: inherit;
          border: none;
          outline: none;
          cursor: pointer;
          padding: 0 16px; 
          transition: 0.3s;
          font-size: 15px;
          color: #fff;
          border-right: 1px solid #ccc;
          margin: 0; /
        }

        .tab button:last-child {
          border-right: none; 
        }

        .tab button:hover {
          background-color: #ddd;
        }

        .tab button.active {
          background-color: #20A8D8;
        }
    </style>

@endsection
@section('content')
   
<div class="">

    <div class="mb-3">
        <div class="row mb-3">
    <div class="col-5">
        <h3 class="heading text-black">Dashboard</h3>
    </div>
    <div class="col-3">
        <div class="filter-container float-right">
            <label for="time-filter">
                <img src="{{ URL::asset('/public/images/filter-icon.svg') }}" width="35px;" style="margin-top:-5px;">
            </label>
            <select class="form-select gradeSelect" id="time-filter">
                <option value="current" {{ request('start_date') ? '' : 'selected' }}>Current Month</option>
                <option value="custom" {{ request('start_date') ? 'selected' : '' }}>Custom Date</option>
            </select>
        </div>
    </div>
    <div class="col-4" style="width: 65%; display: flex; justify-content: space-between;">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
            Dump Download
        </button>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ScheduleAuditExport">
            Schedule Audits Download
        </button>
    </div>
</div>


        <!-- V Filter Date Range Start ---------->
        <div class="row align-items-center justify-content-center text-center mb-3"> 
            <!-- Custom Date Range Inputs -->
           <form method="GET" action="{{ route('dashboard') }}" class="align-items-center d-flex justify-content-end" id="filter-form">
               <div id="custom-date-range" style="display: {{ request('start_date') ? 'block' : 'none' }};">
    <div class="d-flex align-items-center">
        <label for="start-date" class="mr-2 vdate">Start Date:</label>
        <input type="text" id="start-date" name="start_date" value="{{ request('start_date') }}" class="form-control mr-3 flatpickr" style="max-width: 200px;">

        <label for="end-date" class="mr-2 vdate">End Date:</label>
        <input type="text" id="end-date" name="end_date" value="{{ request('end_date') }}" class="form-control flatpickr" style="max-width: 200px;">
    </div>
</div>

                <button class="btn btn-primary ml-2" type="submit" id="filter-button" style="display: {{ request('start_date') ? 'inline-block' : 'none' }};">Filter
                </button>

                @if(request('start_date') || request('end_date'))
                    <a href="{{ route('dashboard') }}" id="clear-link" class="btn btn-primary">Remove</a>
                @endif
            </form>
        </div>    

<!-- V Model For Audit Dump Download Start ---------->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="width: max-content; margin-left:-45px; width:181%;">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Audit Dump Download</h4>
                </div>
                
                <form method="GET" action="{{route('auditdumpdownload')}}" autocomplete="off">
                    <div class="modal-body">
                        <div class="row">
                            @csrf
                            <div class="col-md-3 form-group">
                                <label>Select Audit Agency</label>
                                <select class="form-control" name="audit_agency_name" id="audit_agency_name">
                                    <option value="">Select Audit Agency</option>
                                    @foreach ($auditAgencyName as $auditagency)
                                        <option value="{{ $auditagency->id }}">
                                            {{ $auditagency->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 form-group">
                                <label>Select Collection Agency</label>
                                <select class="form-control" name="agency_name" id="agency_name" value="{{ old('agency_name') }}">
                                    <option value="all">All Agencies</option>
                                    @foreach ($allAgencies as $agency)
                                        <option value="{{ $agency->id }}">{{ $agency->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 form-group">
                                <label>Start Date*</label>
                                <input name="start_date" type="text" data-date-format="yyyy-mm-dd" class="form-control flatpickr" placeholder="Select Start Date"/>
                            </div>

                            <div class="col-md-3 form-group">
                                <label>End Date*</label>
                                <input name="end_date" type="text" data-date-format="yyyy-mm-dd" class="form-control flatpickr" placeholder="Select End Date"/>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Download</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- V Model For ScheduleAuditExport Start ---------->
    <div class="modal fade" id="ScheduleAuditExport" tabindex="-1" role="dialog" aria-labelledby="ScheduleAuditExportModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="width: max-content; margin-left:-45px; width:181%;">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="ScheduleAuditExportModalLabel"> Schedule Audits Dump Download</h4>
                </div>
                
                <form method="GET" action="{{route('scheduleAuditDownload')}}" autocomplete="off">
                    <div class="modal-body">
                        <div class="row">
                            @csrf
                            <div class="col-md-4 form-group">
                                <label>Select Audit Agency</label>
                                <select class="form-control" name="audit_agency_name" id="audit_agency_name">
                                    <option value="">Select Audit Agency</option>
                                    @foreach ($auditAgencyName as $auditagency)
                                        <option value="{{ $auditagency->id }}">
                                            {{ $auditagency->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 form-group">
                                <label>Start Date*</label>
                                <input name="start_date" type="text" data-date-format="yyyy-mm-dd" class="form-control flatpickr" placeholder="Select Start Date"/>
                            </div>

                            <div class="col-md-4 form-group">
                                <label>End Date*</label>
                                <input name="end_date" type="text" data-date-format="yyyy-mm-dd" class="form-control flatpickr" placeholder="Select End Date"/>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Download</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<!-- V Audit Audit Allocation ------------------------------------>
        <div class="row px-md-1">
            <div class="col-md-6 px-md-2 ">
                <div class="cardboxInner h-100">
                    <div class="">
                        <div class="namea align-items-start">
                            <strong>Total Clients</strong>                        
                        <span class="d-block">{!! $totalAllocation > 0 ? $totalAllocation : 0 !!}</span>
                        </div>
                        <div class="row mt-3">
                            @if($totalAllocationByAgency->isEmpty())
                                <div class="col-12">
                                    <p>Data not available</p>
                                </div>
                            @else
                            @foreach($totalAllocationByAgency as $data)
                                <div class="col-4 mb-2"><strong class="font-weight-normal d-block">{{$data->process_review_agency}}<b class="d-block font-weight-bold mt-2">{{$data->total}}</b></strong></div>
                            @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-md-6 px-md-2">
                <div class="cardboxInner cardboxInner h-100" style="background-color: #f2d6d1;">
                    <div class="">
                        <div class="namea">
                            <strong>Total Submitted Audits</strong>
                            <span class="d-block">{!! $totalSubmittedAudits > 0 ? $totalSubmittedAudits : 0 !!}</span>
                        </div>
                        <div class="row mt-3">
                            @if($totalAuditCompletedByAgency->isEmpty())
                                <div class="col-12">
                                    <p>Data not available</p>
                                </div>
                            @else
                            @foreach($totalAuditCompletedByAgency as $data)
                            <div class="col-4 mb-2">
                                <strong class="font-weight-normal">{{$data->agency_name}}
                                <b class="d-block font-weight-bold mt-2">{{$data->completed_audits}}</b>
                                </strong></div>
                            @endforeach
                            @endif
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row px-md-1 mb-3">
            <div class="col-md-6 px-md-2 ">
                <div class="cardboxInner cardboxInner h-100" style="background-color:rgb(225, 200, 230);">
                    <div class="">
                        <div class="namea align-items-start">
                            <strong>Total Users</strong>                        
                        <span class="d-block">{!! $totalAllocation > 0 ? $totalAllocation : 0 !!}</span>
                        </div>
                        <div class="row mt-3">
                            @if($totalAllocationByAgency->isEmpty())
                                <div class="col-12">
                                    <p>Data not available</p>
                                </div>
                            @else
                            @foreach($totalAllocationByAgency as $data)
                                <div class="col-4 mb-2"><strong class="font-weight-normal d-block">{{$data->process_review_agency}}<b class="d-block font-weight-bold mt-2">{{$data->total}}</b></strong></div>
                            @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-md-6 px-md-2">
                <div class="cardboxInner cardboxInner h-100" style="background-color:rgb(200, 230, 226);">
                    <div class="">
                        <div class="namea">
                            <strong>Total Auditors</strong>
                            <span class="d-block">{!! $totalSubmittedAudits > 0 ? $totalSubmittedAudits : 0 !!}</span>
                        </div>
                        <div class="row mt-3">
                            @if($totalAuditCompletedByAgency->isEmpty())
                                <div class="col-12">
                                    <p>Data not available</p>
                                </div>
                            @else
                            @foreach($totalAuditCompletedByAgency as $data)
                            <div class="col-4 mb-2">
                                <strong class="font-weight-normal">{{$data->agency_name}}
                                <b class="d-block font-weight-bold mt-2">{{$data->completed_audits}}</b>
                                </strong></div>
                            @endforeach
                            @endif
                        </div>
                        
                    </div>
                </div>
            </div>
        
    </div>
    </div>


<!-- V Action Planning ------------------------------------------->
     <div class="mb-3">
        <div class="row px-md-1">
            <div class="col-md-6 px-md-2 ">
                <div class="cardboxInner score cardboxInner h-100" style="background-color: #d4e4bc;">
                     <div class="">
                        <div class="namea">
                            <strong>Sent For Action Planning</strong>
                            <span class="d-block">{!! $totalPendingAuditsActionPlan > 0 ? $totalPendingAuditsActionPlan : 0 !!}</span>   
                        </div>
                        <div class="row mt-3">
                            <div class="col-5 mb-2"><strong class="font-weight-normal">Total Submitted Audits<b class="d-block font-weight-bold mt-2">{!! $totalSubmittedAudits > 0 ? $totalSubmittedAudits : 0 !!}</b></strong></div>
                            <div class="col-3 mb-2"><strong class="font-weight-normal">Pending<b class="d-block font-weight-bold mt-2">{!! $totalPendingAuditsActionPlan > 0 ? $totalPendingAuditsActionPlan : 0 !!}</b></strong></div>
                            <div class="col-3 mb-2"><strong class="font-weight-normal">Approved/Closed <b class="d-block  mt-2 font-weight-bold">{!! $totalApprovedAudits > 0 ? $totalApprovedAudits : 0 !!}</b></strong></div>
                            <div class="col-3 mb-2"><strong class="font-weight-normal">Pending (C.A.)<b class="d-block font-weight-bold mt-2">  {{ $totalPendingAuditsofCA > 0 ? $totalPendingAuditsofCA : 0 }}</b></strong></div>
                            <div class="col-4 mb-2"><strong class="font-weight-normal">Pending (A. A.)<b class="d-block font-weight-bold mt-2">{!! $totalPendingAuditsofAA > 0 ? $totalPendingAuditsofAA : 0 !!}</b></strong></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 px-md-2 ">
                <div class="cardboxInner score h-100">
                    <div class="">
                        <div class="namea">
                            <strong>Closed Audits</strong>
                            <span class="d-block">{!! $overallAuditcurrentMonth > 0 ? $overallAuditcurrentMonth : 0 !!}</span>
                        </div>

                        <div class="row mt-3">
                            <div class="col-6 mb-2">
                                <strong class="font-weight-normal">Last Month
                                    <b class="d-block font-weight-bold mt-2">
                                    Audits: {!! $overallAuditlastMonth > 0 ? $overallAuditlastMonth : 0 !!} <br><br>

                                    
                                    Score: {!! $overallScorelastMonth > 0 ? number_format($overallScorelastMonth, 0) : 0 !!}%
                                    </b>
                                </strong>
                            </div>

                            <div class="col-6 mb-2">
                                <strong class="font-weight-normal">Current Month
                                    <b class="d-block  mt-2 font-weight-bold">
                                        Audits: {!! $overallAuditcurrentMonth > 0 ? $overallAuditcurrentMonth : 0 !!} 
                                        <span class="ml-3"> 
                                            @if ($percentageDifference > 0)
                                                <b class="text-success">
                                                    <img src="{{URL::asset('/public/clientdashboard/images/downarrow-green.svg') }}" alt="" width="10">
                                                    
                                                </b>
                                            @elseif ($percentageDifference < 0)
                                                 <b class="text-danger">
                                                    <img src="{{URL::asset('/public/clientdashboard/images/downarrow.svg') }}" alt="" width="10">
                                                </b>
                                            @else
                                                <b>&nbsp;</b>
                                            @endif
                                        </span><br> <br>
                                        Score: {!! $overallScorecurrentMonth > 0 ? number_format($overallScorecurrentMonth, 0) : 0 !!}%
                                        <span class="ml-3">
                                            @if ($percentageScoreDifference > 0)
                                                <b class="text-success">
                                                    <img src="{{URL::asset('/public/clientdashboard/images/downarrow-green.svg') }}" alt="" width="10">
                                                </b>
                                            @elseif ($percentageScoreDifference < 0)
                                                <b class="text-danger">
                                                    <img src="{{URL::asset('/public/clientdashboard/images/downarrow.svg') }}" alt="" width="10">
                                                </b>
                                            @else
                                                <b>&nbsp;</b>
                                            @endif
                                        </span>
                                    </b>
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!---- V OverAll Audit and OverAll Score Six Months Start -------->
    <div class="mb-3">
        <div class="row px-md-1">
            <div class="col-md-12 px-md-2">
                <div class="cardBox">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="m-0"><span>Client Wise Trend </span>Last Six Months
                                </h6>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="dropdown float-right mb-1">
                                <select class="form-select gradeSelect" id="tabDropdown" onchange="openCity(event, this.value)">
                                    <option value="London" selected>All Trend Data</option> 
                                    @foreach ($auditAgencyIds as $agencyId)
                                        <option value="agency_{{ $agencyId }}">
                                            {{ isset($agencyNames[$agencyId]) ? $agencyNames[$agencyId] : 'Unknown Agency' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>

                    <div id="London" class="tabcontent">
                        <div class="table-responsive tbleDiv text-center">
                           <table class="table">
                                <thead>
                                    <tr>
                                        <th class="text-start border-0 bg-white border-top">Months</th>
                                        @foreach ($overallAuditLastSixMonths as $data)
                                            <th class="text-start border-0 bg-white border-top">{{ $data['month'] }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-start">Total Allocation</td>
                                        @foreach ($overallAuditLastSixMonths as $data)
                                            <td class="text-start">{{ $data['allocation_count'] }}</td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td class="text-start">Submitted Audits</td>
                                        @foreach ($overallAuditLastSixMonths as $data)
                                            <td class="text-start">{{ $data['audit_count'] }}</td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td class="text-start">Pending</td>
                                        @foreach ($overallAuditLastSixMonths as $data)
                                            <td class="text-start">{{ $data['pending_count'] }}</td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td class="text-start">Score</td>
                                        @foreach ($overallAuditLastSixMonths as $data)
                                            <td class="text-start">{{ $data['overall_score'] }}%</td>
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                <!-- Agency Wise Tabs -->
                @foreach ($auditAgencyIds as $agencyId)
                    <div id="agency_{{ $agencyId }}" class="tabcontent" style="display:none;">
                        <div class="table-responsive tbleDiv text-center mb-4">
                            <h3 class="heading font-weight-bold">
                                {{ isset($agencyNames[$agencyId]) ? $agencyNames[$agencyId] : 'Unknown Agency' }}
                            </h3>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th class="text-start border-0 bg-white border-top">Months</th>
                                        @foreach ($overallAuditLastSixMonthsByAgency as $data)
                                            <th class="text-start border-0 bg-white border-top">{{ $data['month'] }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-start">Total Allocation</td>
                                        @foreach ($overallAuditLastSixMonthsByAgency as $data)
                                            <td class="text-start">
                                                {{ isset($data['agencies'][$agencyId]) ? $data['agencies'][$agencyId]['allocation_count'] : 0 }}
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td class="text-start">Submitted Audits</td>
                                        @foreach ($overallAuditLastSixMonthsByAgency as $data)
                                            <td class="text-start">
                                                {{ isset($data['agencies'][$agencyId]) ? $data['agencies'][$agencyId]['audit_count'] : 0 }}
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td class="text-start">Pending</td>
                                        @foreach ($overallAuditLastSixMonthsByAgency as $data)
                                            <td class="text-start">
                                                {{ $data['agencies'][$agencyId]['pending_count'] ?? 0 }}
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td class="text-start">Score</td>
                                        @foreach ($overallAuditLastSixMonthsByAgency as $data)
                                            <td class="text-start">
                                                {{ isset($data['agencies'][$agencyId]) ? number_format($data['agencies'][$agencyId]['overall_score'], 0) : 0 }}%
                                            </td>
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach



                </div>

            </div>
        </div>
    </div>

<!---- V OverAll Audit and OverAll Score Six Months End ---------->  


<!-----V Zone Wise Data Start ------------------------------------------------>

<div class="mb-3">
    <div class="row px-md-1">
        <div class="col-md-8 px-md-2">
            <div class="cardBox h-100">
                <div class="d-flex justify-content-between">
                    <h6 class="m-0"><span> Zone</span> Wise</h6>
                    <div class="w-70">
                        <span class="d-block mb-1">Select Zone </span>
                        <ul class="nav nav-tabs zoneTabs">
                            @foreach($databyZoneRegion as $index => $zoneData)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $index == 0 ? 'active' : '' }}" data-toggle="tab" data-target="#{{ strtolower($zoneData->region_name) }}" type="button">
                                        {{ $zoneData->region_name }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="tab-content">
                    @foreach($databyZoneRegion as $index => $zoneData)
                        <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}" id="{{ strtolower($zoneData->region_name) }}">
                            <div class="d-flex align-items-center">
                                <div class="leftSec">
                                    <div id="container{{ $index + 1 }}" style="width: 200px; height: 250px;"></div>
                                </div>
                                <div class="rightSec">
                                    <div class="d-flex align-items-center justify-content-between mb-2 mt-3">
                                    </div>
                                    <div class="">
                                        <span class="auditScore py-3">Audit Score <b id="auditScore{{ $index }}">{{ number_format($zoneData->average_score ?? 0, 2) }}%</b></span>
                                        <div class="row datalMain">
                                            <div class="col-6 border-end mb-3">
                                                <span>Assigned Agency <b id="assignedAgency{{ $index }}">{{ $zoneData->total_allocation ?? 0 }}</b></span>
                                            </div>
                                            <div class="col-6">
                                                <span class="completed">Audit Submitted <b id="auditCount{{ $index }}">{{ $zoneData->audit_count ?? 0 }}</b></span>
                                            </div>
                                            <div class="col-6 border-end mb-3">
                                                <span>Sent for Closure <b id="sentForClosureCount{{ $index }}">{{ $zoneData->sent_for_closure_count ?? 0 }}</b></span>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <span class="completed">Closure Completed <b id="closureCompletedCount{{ $index }}">{{ $zoneData->closure_completed_count ?? 0 }}</b></span>
                                            </div>   
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

<!--V Zone Wise Data End-------------------------------------------->
    


              
       
<!-- V Grade Wise Chart --------------------------------------------->
            <div class="col-md-4 px-md-2">
                <div class="cardBox h-100">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="m-0"><span> Grade</span> Wise</h6>
                        <select class="form-select zoneGrade" aria-label="Default select example">
                            <option value="A" selected>Grade A</option>
                            <option value="B">Grade B</option>
                            <option value="C">Grade C</option>
                            <option value="D">Grade D</option>
                        </select>
                    </div>
                    <div class="gradeWisemain">
                        <div class="d-flex align-items-center justify-content-center"></div>
                        <div id="gradeWise" style="height: 220px;"></div>
                        <span class="d-block">Overall</span>
                        <!-- <a href="" class="listDown">List Download</a> -->
                    </div>
                </div>
            </div>
        </div>

    </div>

<!--V Zone Wise Data End------------------------------------------------------> 
 
<!--- National Collection Manager End ----------------------------------->
</div>


<!-- for calender -->
 <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    flatpickr(".flatpickr", {
        dateFormat: "Y-m-d",
        onDayCreate: function(_, __, ___, dayElem) {
            // Highlight Sundays
            if (dayElem.dateObj.getDay() === 0) {
                dayElem.classList.add("sunday");
            }
        }
    });
</script>



<!-- /.content -->
<div class="clearfix"></div>
    <!-- <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-more.js"></script>
    <script src="{{URL::asset('/public/clientdashboard/js/owl.carousel.min.js')}}"></script>
   

<!----- V Zone Wise Get Data --------------------------------------->
@foreach($databyZoneRegion as $index => $zoneData)
<script>
    const totalAllocation{{ $index }} = {!! $zoneData->total_allocation ?? 0 !!};
    const retailCount{{ $index }} = {!! $zoneData->retail_count ?? 0 !!};
    const cardCount{{ $index }} = {!! $zoneData->card_count ?? 0 !!};
    const retailCardCount{{ $index }} = {!! $zoneData->retail_card_count ?? 0 !!};

    Highcharts.chart('container{{ $index + 1 }}', {
        chart: {
            type: 'pie',
            custom: {},
            events: {
                render() {
                    const chart = this,
                        series = chart.series[0];
                    let customLabel = chart.options.chart.custom.label;

                    if (!customLabel) {
                        customLabel = chart.options.chart.custom.label =
                            chart.renderer.label(
                                '<strong>' + totalAllocation{{ $index }} + '</strong><br/>' +  // Number of allocations
                                '<span>Allocations</span>'      // Custom label text changed to 'Allocations'
                            )
                                .css({
                                    color: '#929292',
                                    textAnchor: 'middle'
                                })
                                .add();
                    }

                    const x = series.center[0] + chart.plotLeft,
                        y = series.center[1] + chart.plotTop - 
                            (customLabel.attr('height') / 2);

                    customLabel.attr({
                        x,
                        y
                    });

                    // Set font size based on chart diameter
                    customLabel.css({
                        fontSize: `${series.center[2] / 9}px`
                    });
                }
            }
        },
        accessibility: {
            point: {
                valueSuffix: '%'
            }
        },
        title: {
            text: null, // Set to null to remove the title
        },
        subtitle: {
            text: null,
        },
        tooltip: {
            pointFormat: '<b>{point.percentage:.0f}%</b>'
        },
        legend: {
            enabled: true
        },
        credits: {
            enabled: false
        },
        plotOptions: {
            series: {
                allowPointSelect: true,
                cursor: 'pointer',
                borderRadius: 0,
                dataLabels: [{
                    enabled: false,
                    distance: 20,
                    format: null
                }, {
                    enabled: null,
                    distance: -20,
                    format: null,
                    style: {
                        fontSize: '0.9em'
                    }
                }],
                showInLegend: true
            }
        },
        series: [{
            name: 'Allocation',
            colorByPoint: true,
            innerSize: '65%',
            data: [{
                name: 'Retail',
                y: retailCount{{ $index }},
                color: '#F48FB1'
            }, {
                name: 'Card',
                y: cardCount{{ $index }},
                color: '#FEC502'
            }, {
                name: 'Retail/Card',
                y: retailCardCount{{ $index }},
                color: '#4D93FD'
            }]
        }]
    });
</script>
@endforeach

<!--- V Zone Wise one Wise Get Data End ---------------------------->


<!--- V Grade Wise Get Data Start ---------------------------------->

<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function () {
    const gradeDropdown = document.querySelector('.zoneGrade');
    const startDateInput = document.querySelector('#start-date'); // Input field for start date
    const endDateInput = document.querySelector('#end-date');   // Input field for end date

    // Event listener for dropdown change
    gradeDropdown.addEventListener('change', fetchGradeData);

    // Event listeners for date input changes
    startDateInput.addEventListener('change', fetchGradeData);
    endDateInput.addEventListener('change', fetchGradeData);

    function fetchGradeData() {
        const selectedGrade = gradeDropdown.value; // Fetch the selected grade
        let startDate = startDateInput.value;    // Fetch start date value
        let endDate = endDateInput.value;        // Fetch end date value

        // If start or end date are not provided, set default date range (10th of last month to 9th of current month)
        if (!startDate || !endDate) {
            const today = new Date();
            const currentDay = today.getDate();
            
            let defaultStartDate, defaultEndDate;

            if (currentDay < 10) {
                // If the current day is before the 10th, set the range to 10th of the previous month to 9th of the current month
                today.setMonth(today.getMonth() - 1); // Go to previous month
                defaultStartDate = new Date(today.getFullYear(), today.getMonth(), 10); // 10th of previous month
                defaultEndDate = new Date(today.getFullYear(), today.getMonth() + 1, 9); // 9th of current month
            } else {
                // Otherwise, set the range to 10th of current month to 9th of next month
                defaultStartDate = new Date(today.getFullYear(), today.getMonth(), 10); // 10th of current month
                defaultEndDate = new Date(today.getFullYear(), today.getMonth() + 1, 9); // 9th of next month
            }

            // Convert dates to 'YYYY-MM-DD' format for the input fields
            startDate = formatDateToString(defaultStartDate);
            endDate = formatDateToString(defaultEndDate);
        }

        // Log the start and end dates (for debugging)
        console.log('Start Date:', startDate);
        console.log('End Date:', endDate);

        // Fetch the data for the selected grade with date filters
        fetch(`/grades/${selectedGrade}?start_date=${startDate}&end_date=${endDate}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Log the API response for debugging
                console.log('API Response:', data);

                // Map the data for the chart
                const chartData = data.map(item => ({
                    name: item.region_name, // Region name
                    y: item.grade_count,    // Grade count
                    color: getColorForRegion(item.region_name) // Assign region-specific color
                }));

                // Determine the chart title dynamically
                const chartTitle = `Grade Distribution Zone Wise for Grade ${selectedGrade}`;

                // Update or initialize the chart
                Highcharts.chart('gradeWise', {
                    chart: {
                        type: 'pie',
                        backgroundColor: chartData.length === 0 ? '#f0f0f0' : null // Gray background if no data
                    },
                    title: {
                        text: chartTitle,
                        style: {
                            fontSize: '15px',
                            fontFamily: 'Arial, sans-serif',
                            color: '#333'
                        }
                    },
                    tooltip: {
                        pointFormat: '<b>{point.name}</b>: {point.y}'
                    },
                    credits: {
                        enabled: false
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: chartData.length !== 0,
                                format: '<b>{point.name}</b>: {point.y}',
                                style: {
                                    fontSize: '12px',
                                    fontFamily: 'Arial, sans-serif',
                                    color: '#000'
                                }
                            },
                            showInLegend: true
                        }
                    },
                    legend: {
                        itemStyle: {
                            fontSize: '12px',
                            fontFamily: 'Arial, sans-serif',
                            color: '#333'
                        }
                    },
                    series: [{
                        name: chartData.length === 0 ? 'No data found' : 'Grade Count',
                        colorByPoint: true,
                        innerSize: chartData.length === 0 ? '0%' : '65%',
                        data: chartData.length === 0
                            ? [{ name: 'No Data', y: 1, color: '#D3D3D3' }]
                            : chartData
                    }]
                });
            })
            .catch(error => {
                console.error('Error fetching data:', error);
            });
    }

    // Function to assign colors to regions
    function getColorForRegion(regionName) {
        const colors = {
            'East': '#61C667',
            'West': '#FEC502',
            'North': '#4D93FD',
            'South': '#D81B60'
        };
        return colors[regionName] || '#000000'; // Default to black if region not found
    }

    // Utility function to format date to 'YYYY-MM-DD' string
    function formatDateToString(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0'); // Ensure 2 digits for month
        const day = String(date.getDate()).padStart(2, '0'); // Ensure 2 digits for day
        return `${year}-${month}-${day}`;
    }

    // Trigger the change event on page load
    gradeDropdown.dispatchEvent(new Event('change'));
});
</script>
<!--- V Grade Wise Get Data Start End ----------------------------->



<!----V Parameter Score Start -------------------------------------->
@if(!empty($parametersTotalScore))
    <script>
        const ctx = document.getElementById('myChart').getContext('2d');

        // Generate labels and data arrays dynamically using Blade
        const labels = [
            @foreach($parametersTotalScore as $dataItem)
                "{{ $dataItem['parameter_name'] }}",
            @endforeach
        ];

        const data = [
            @foreach($parametersTotalScore as $dataItem)
                {{ number_format($dataItem['total_parameter_percentage'], 2) }},
            @endforeach
        ];

        const myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: '', 
                    data: data,
                    backgroundColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(154, 62, 235, 1)'
                    ],
                    borderWidth: 1,
                    barThickness: 19, 
                    maxBarThickness: 35 
                }]
            },
            options: {
                indexAxis: 'y', 
                animation: {
                    tension: {
                        duration: 500,
                        easing: 'easeInOutQuad',
                        from: 0.5, 
                        to: 0, 
                        loop: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        max: 100, 
                        grid: {
                            display: false 
                        }
                    },
                    y: {
                        ticks: {
                            display: true 
                        },
                        grid: {
                            drawBorder: false 
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false 
                    },
                    datalabels: {
                        anchor: 'end', 
                        align: 'end',  
                        formatter: (value) => {
                            return value + '%'; 
                        },
                        color: 'white', 
                        font: {
                            weight: 'bold',
                        }
                    }
                },
                elements: {
                    bar: {
                        categoryPercentage: 0.6, 
                        barPercentage: 20 
                    }
                }
            },
            plugins: [ChartDataLabels]
        });
    </script>
@else
    <p class="text-center text-danger"></p>
@endif
<!-------V Parameter Score End ------------------------------------->
@endsection

@section('js')


<!-- V Date Picker For Audit Dump Download Start ---------------------------------------->

<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script>
    jQuery(document).ready(function() {
        jQuery('.flatpickr').flatpickr({
            dateFormat: "yy-mm-dd",
            onSelect: function() {
                jQuery(this).flatpickr('hide');
            }
        });

        indiaMap([]);
    });
</script>

<!--V Date Picker For Audit Dump Download End ------------------------------------------->




<!--V Filter Dashbaord Start -------------------------------------------->
<<!-- script>
    document.addEventListener("DOMContentLoaded", function () {
        const filterDropdown = document.getElementById("time-filter");
        const customDateContainer = document.getElementById("custom-date-range");
        const filterButton = document.getElementById("filter-button");
        
        // Initial check for the dropdown value on page load
        toggleCustomFilter(filterDropdown.value);

        // Listen for changes to the dropdown
        filterDropdown.addEventListener("change", function () {
            toggleCustomFilter(filterDropdown.value);
        });

        // Function to toggle custom date range visibility based on selected filter
        function toggleCustomFilter(selectedValue) {
            if (selectedValue === "custom") {
                customDateContainer.style.display = "block"; 
                filterButton.style.display = "inline-block"; 
            } else {
                customDateContainer.style.display = "none"; 
                filterButton.style.display = "none";
            }
        }
    });
</script> -->

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const filterDropdown = document.getElementById("time-filter");
        const customDateContainer = document.getElementById("custom-date-range");
        const filterButton = document.getElementById("filter-button");
        const startDateInput = document.getElementById("start-date");
        const endDateInput = document.getElementById("end-date");

        toggleCustomFilter(filterDropdown.value);

        filterDropdown.addEventListener("change", function () {
            toggleCustomFilter(filterDropdown.value);
        });

        startDateInput.addEventListener("change", function() {
            setMinEndDate(startDateInput.value); 
        });

        function toggleCustomFilter(selectedValue) {
            if (selectedValue === "custom") {
                customDateContainer.style.display = "block"; 
                filterButton.style.display = "inline-block"; 
            } else {
                customDateContainer.style.display = "none"; 
                filterButton.style.display = "none";
            }
        }

        function setMinEndDate(startDate) {
            const formattedStartDate = new Date(startDate);
            const formattedStartDateString = formattedStartDate.toISOString().split('T')[0]; 

            endDateInput.setAttribute("min", formattedStartDateString);

            const formattedEndDate = new Date(endDateInput.value);
            if (formattedEndDate < formattedStartDate) {
                endDateInput.value = ''; 
            }
        }

        jQuery('.flatpickr').flatpickr({
            dateFormat: "yy-mm-dd",
            onSelect: function() {
                jQuery(this).flatpickr('hide');
            }
        });

    });
</script>

<!--V Filter Dashbaord End ---------------------------------------------->

<!--V Current Month Filter Refresh Start -------------------------------->
<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function () {
    const filterDropdown = document.getElementById("time-filter");
    const clearLink = document.getElementById("clear-link");

    // Handle dropdown change
    filterDropdown.addEventListener("change", function () {
        const selectedValue = filterDropdown.value;

        if (selectedValue === "current") {
            // Redirect to the dashboard route
            window.location.href = clearLink.href;
        } else if (selectedValue === "custom") {
            // Call toggleCustomFilter for custom date selection
            toggleCustomFilter();
        }
    });
});

</script>
<!--V Current Month Filter Refresh End ----------------------------------->

<!--V Overall Audit and Overall Score Tab Start -------------------------->
<!-- <script>
function openCity(evt, cityName) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  document.getElementById(cityName).style.display = "block";
  evt.currentTarget.className += " active";
}
</script> -->
<!-- <script type="text/javascript">
function openCity(evt, cityName) {
    var i, tabcontent, tablinks;

    // Hide all tab contents
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    // Remove active class from all tab buttons
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

    // Show the selected tab content
    document.getElementById(cityName).style.display = "block";

    // Add active class to the clicked tab button if evt is defined
    if (evt) {
        evt.currentTarget.className += " active";
    }
}
</script> -->


<script type="text/javascript">
    function openCity(evt, cityName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";  // Hide all tabs
    }

    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

    document.getElementById(cityName).style.display = "block";  // Show the selected tab
    evt.currentTarget.className += " active";  // Mark the active tab
}

// Update the tab based on dropdown selection
document.getElementById("tabDropdown").addEventListener("change", function() {
    var value = this.value;
    openCity(event, value);
});

</script>
<!--V Overall Audit and Overall Score Tab End -------------------------->

@endsection







