@extends('layouts.master')
@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="{{ URL::asset('public/clientdashboard/css/style.css') }}">

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
        .vdate {
            width: 105px;
        }
    </style>

    <style>
        .dashboard-header {
            border-radius: 10px;
        }

        .dashboard-title {
            font-weight: 500;
            color: rgb(21, 119, 199);
        }

        .filter-select {
            width: 150px;
            margin-left: 10px;
        }

        .dashboard-card {
            background: #e6e3e3;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 20px #08f5c214;
            transition: .3s;
        }

        .dashboard-card:hover {
            transform: translate(-5px);
        }

        .dashboard-card h6 {
            color: #362501;
        }

        .dashboard-card h2 {
            font-size: 28px;
            font-weight: 700;
            color: #834c0d;

        }

        .dashboard-icon {
            height: 40px;
            width: 40px;
            border-radius: 60%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 16px;
        }

        .header-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            flex-wrap: wrap;
            gap: 10px;
        }

        .header-actions-form {
            margin: 0;
        }
    </style>
@endsection
@section('content')
    <!-- Content -->
    <input type="hidden" name="url" id="url" value={{ url('/') }}>
    <input type="hidden" name="token" id="token" value={{ @csrf_token() }}>

    {{-- start development --}}
    <div class="container-fluid">
        <div class="card dashboard-header-clean shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="row align-items-center">

                    <div class="col-md-4">
                        <h2 class="dashboard-title">
                            Agency Dashboard
                        </h2>
                    </div>

                    {{-- new today update --}}

                    <div class="md-8">
                        <div class="header-actions">
                            <!-- Filter -->
                            <div class="filter-container d-flex align-items-center">
                                <label for="time-filter" class="mb-0">
                                    <img src="{{ URL::asset('public/images/filter-icon.svg') }}" width="25px">
                                </label>

                                <select class="form-select d-inline-block filter-select" id="time-filter">
                                    <option value="current" {{ request('start_date') ? '' : 'selected' }}>Current Month
                                    </option>
                                    <option value="custom" {{ request('start_date') ? 'selected' : '' }}>Custom Date
                                    </option>
                                </select>
                            </div>
                            <!-- Data Download -->
                            <button class="btn btn-success" data-toggle="modal" data-target="#myModal">
                                <i class="fa fa-download"></i> Data Download
                            </button>
                            @role('Admin')
                                @if (auth()->check() && auth()->user()->client_id == 13)
                                    <form action="{{ route('switch.user') }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="email" value="demo@mailinator.com">
                                        <button type="submit" class="btn btn-primary"><i class="fa fa-user"></i>Client</button>
                                    </form>

                                    <form action="{{ route('switch.user') }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="email" value="auditorqd@mailinator.com">
                                        <button type="submit" class="btn btn-danger"><i class="fa fa-user-shield"></i>Quality
                                            Auditor</button>
                                    </form>
                                @endif

                                @if (auth()->check() && auth()->user()->client_id == 2)
                                    <form action="{{ route('switch.user') }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="email" value="TATA@mailinator.com">
                                        <button class="btn btn-primary"><i class="fa fa-user"></i> Client</button>
                                    </form>
                                    <form action="{{ route('switch.user') }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="email" value="raghav@mailinator.com">
                                        <button class="btn btn-danger"><i class="fa fa-user-shield"></i> Quality
                                            Auditor</button>
                                    </form>
                                @endif
                            @endrole
                        </div>
                    </div>
                </div>
            </div>

            <!-- V Filter Date Range Start ---------->
            {{-- @if (request('start_date') || request('end_date'))
                <div class="card shadow-sm border-0 mb-4"> --}}
        <div class="card shadow-sm border-0 mb-4" id="custom-date-card"
    style="{{ request('start_date') ? '' : 'display:none;' }}">
                <div class="card-body">
                    <!-- Custom Date Range Inputs -->
                    <form method="GET" action="{{ route('dashboard') }}" id="filter-form">
                        <div id="custom-date-range" class="row align-items-end">
                            <div class="col-md-4">
                                <label for="start-date" class="mr-2 vdate">Start Date:</label>
                                <input type="text" id="start-date" name="start_date" value="{{ request('start_date') }}"
                                    class="form-control flatpickr">
                            </div>
                            <div class="col-md-4">
                                <label for="end-date" class="mr-2 vdate">End Date:</label>
                                <input type="text" id="end-date" name="end_date" value="{{ request('end_date') }}"
                                    class="form-control flatpickr">
                            </div>
                            <div class="col-md-4">
                                <button id="filter-button" class="btn btn-primary">
                                    Filter
                                </button>
                                <a href="{{ route('dashboard') }}" class="btn btn-primary">Reset</a>
                            </div>
                             </div>
                    </form>
                </div>
            </div>
            {{-- @endif --}}
            <!-- V Filter Date Range End ---------->

            <!-- V Filter Date Range End ---------->
            <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content" style="width: max-content; margin-left:-45px; width:181%;">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">Data Download</h4>
                        </div>
                        <form method="GET" action="{{ route('auditdumpdownload') }}" autocomplete="off">
                            <div class="modal-body">
                                <div class="row">
                                    @csrf
                                    <div class="col-md-4 form-group">
                                        <label for="agency_name">Select Agency</label>
                                        <select class="form-control" name="agency_name" id="agency_name">
                                            @if ($agency->isEmpty())
                                                <option value="all">Select Agency</option>
                                                <option class="text-danger" value="">Audit not performed</option>
                                            @else
                                                <option value="all">All Agencies</option>
                                                @foreach ($agency as $item)
                                                    <option value="{{ $item->id }}"
                                                        {{ old('agency_name') == $item->id ? 'selected' : '' }}>
                                                        {{ $item->name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label>Start Date*</label>
                                        <input name="start_date" type="text" data-date-format="yyyy-mm-dd"
                                            class="form-control flatpickr" placeholder="Select Start Date" required />
                                    </div>

                                    <div class="col-md-4 form-group">
                                        <label>End Date*</label>
                                        <input name="end_date" type="text" data-date-format="yyyy-mm-dd"
                                            class="form-control flatpickr" placeholder="Select End Date" required />
                                    </div>

                                </div>
                            </div>
                            <div class="modal-footer">
                                @if ($agency->isEmpty())
                                    <button type="submit" class="btn btn-danger disabled">Download</button>
                                @else
                                    <button type="submit" class="btn btn-primary">Download</button>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Dump Download Modal End----------->
        </div>
    </div>


    {{-- @role('Admin') --}}
    {{-- <div class="col-md-7 text-end"> --}}
    {{-- @if (auth()->check() && auth()->user()->client_id == 13) --}}
    {{-- <div class="d-flex justify-content-end mb-3"> --}}
    {{-- <form action="{{ route('switch.user') }}" method="POST" class="d-inline"> --}}
    {{-- @csrf --}}
    {{-- <input type="hidden" name="email" value="demo@mailinator.com">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-user"></i>Client</button>
                                </form> --}}

    {{-- <form action="{{ route('switch.user') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="email" value="auditorqd@mailinator.com">
                                    <button type="submit" class="btn btn-danger"><i class="fa fa-user-shield"></i>Quality
                                        Auditor</button>
                                </form> --}}
    {{-- </div>
                    @endif --}}

    {{-- @if (auth()->check() && auth()->user()->client_id == 2)
                        {{-- <div class="d-flex align-items-center"> i will change something as same as previous design --}}
    {{-- <form action="{{ route('switch.user') }}" o style="margin-right: 10px;">
                @csrf
                <input type="hidden" name="email" value="TATA@mailinator.com">

                <button class="btn btn-primary me-2"> <i class="fa fa-user"></i>Client</button>
            </form>

            <form action="{{ route('switch.user') }}" method="POST">
                @csrf
                <input type="hidden" name="email" value="raghav@mailinator.com">
                <button class="btn btn-danger me-2"><i class="fa fa-user-shield"></i>
                    Quality Auditor</button>
            </form>
            @endif  --}}

    {{-- <div class="filter-container mr-3"> --}}
    {{-- <label for="time-filter" class="mb-0">
                                <img src="{{ URL::asset('public/images/filter-icon.svg') }}" width="25px">
                            </label> --}}
    {{-- <select class="form-select gradeSelect" id="time-filter">
                            <option value="current" {{ request('start_date') ? '' : 'selected' }}>Current Month</option>
                            <option value="custom" {{ request('start_date') ? 'selected' : '' }}>Custom Date</option>
                        {{-- </select> --}}
    {{-- <select class="form-select d-inline-block filter-select">
                              <option value="current" {{ request('start_date') ? '' : 'selected' }}>Current Month</option>
                            <option value="custom" {{ request('start_date') ? 'selected' : '' }}>Custom Date</option>
                            </select>
                        </div>  --}}


    {{-- <div class="2">
                            <button class="btn btn-success" data-toggle="modal" data-target="#myModal">
                                <i class="fa fa-download"></i> Data Download
                            </button>
                        </div> --}}
    {{-- </div>
    </div> --}}

    <!-- V Filter Date Range Start ---------->
    {{-- @if (request('start_date') || request('end_date')) --}}
    {{-- <div class="card shadow-sm border-0 mb-4"> --}}
    {{-- <div class="card-body"> --}}
    <!-- Custom Date Range Inputs -->
    {{-- <form method="GET" action="{{ route('dashboard') }}" class="row align-items-end" id="filter-form"> --}}
    {{-- <div id="custom-date-range" style="display: {{ request('start_date') ? 'block' : 'none' }};">
                        <div class="col-md-4"> --}}
    {{-- <label for="start-date">Start Date:</label> --}}
    {{-- <input type="text" id="start-date" name="start_date" value="{{ request('start_date') }}"
                                class="form-control flatpickr">
                        </div> --}}
    {{-- <div class="col-md-4">
                            <label for="end-date">End Date:</label>
                            <input type="text" id="end-date" name="end_date" value="{{ request('end_date') }}"
                                class="form-control flatpickr">
                        </div> --}}
    {{-- <div class="col-md-4">

                            <button class="btn btn-primary">
                                Filter
                            </button>
                            <a href="{{ route('dashboard') }}" class="btn btn-primary">Reset</a>
                        </div> --}}
    {{-- </form>
            </div>
        </div>
    @endif --}}
    <!-- V Filter Date Range End ---------->

    <!-- Dump Download Modal Start -------->
    {{-- <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"> --}}
    {{-- <div class="modal-dialog" role="document"> --}}
    {{-- <div class="modal-content" style="width: max-content; margin-left:-45px; width:181%;"> --}}
    {{-- <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Data Download</h4>
                </div> --}}
    {{-- <form method="GET" action="{{ route('auditdumpdownload') }}" autocomplete="off">
                    <div class="modal-body">
                        <div class="row"> --}}
    {{-- @csrf
                            <div class="col-md-4 form-group"> --}}
    {{-- <label for="agency_name">Select Agency</label> --}}
    {{-- <select class="form-control" name="agency_name" id="agency_name"> --}}
    {{-- @if ($agency->isEmpty())
                                        <option value="all">Select Agency</option>
                                        <option class="text-danger" value="">Audit not performed</option>
                                    @else
                                        <option value="all">All Agencies</option>
                                        @foreach ($agency as $item)
                                            <option value="{{ $item->id }}"
                                                {{ old('agency_name') == $item->id ? 'selected' : '' }}>
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    @endif


                                </select>
                            </div> --}}

    {{-- <div class="col-md-4 form-group">
                                <label>Start Date*</label>
                                <input name="start_date" type="text" data-date-format="yyyy-mm-dd"
                                    class="form-control flatpickr" placeholder="Select Start Date" required />
                            </div>

                            <div class="col-md-4 form-group">
                                <label>End Date*</label>
                                <input name="end_date" type="text" data-date-format="yyyy-mm-dd"
                                    class="form-control flatpickr" placeholder="Select End Date" required />
                            </div>

                        </div>
                    </div> --}}
    {{-- <div class="modal-footer">
                        @if ($agency->isEmpty())
                            <button type="submit" class="btn btn-danger disabled">Download</button>
                        @else
                            <button type="submit" class="btn btn-primary">Download</button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Dump Download Modal End-----------> --}}
    {{-- 
    </div>
@endrole --}}



    {{-- START design for card box --}}
    <div class="content" style="font-size: 13px !important;">
        <!-- Animated -->
        <div class="animated fadeIn">
            {{-- ======================================================== --}}
            @role('Admin')
                <div class="card border-0 shadow-sm">
                    <div class="card-header">
                        <h4 class="mb-0">Overall Details</h4>
                    </div>

                    <div class="card-body">
                        <div class="row">

                            <div class="col-lg-4 col-md-6 mb-3">
                                @include('dashboard.card', [
                                    'title' => 'Total Allocation',
                                    'count' => $totalAllocation,
                                    'icon' => 'fa-list',
                                    'color' => 'primary',
                                    'route' => route('audit_allocation_assign.index'),
                                ])
                            </div>

                            {{-- <div class="card dashboard-card shadow-sm border-0">
                                        <div class="card-body">

                                            <div class="d-flex justify-content-between">
                                                <a href="{{ route('audit_allocation_assign.index') }}">
                                                    <div> --}}
                            {{-- <h6 class="text-muted">
                                                            Total Allocation
                                                        </h6> --}}
                            {{-- <div class="text-center dib"> --}}
                            {{-- <div class="d-flex justify-content-center align-items-center mb-2">
                                                        <strong class="mr-2" style="color:black !important;">Total
                                                            Allocation</strong>
                                                        <i class="fa fa-info-circle text-primary" data-toggle="tooltip"
                                                            data-placement="top"
                                                            title="Total Allocation shows the number of allocated tasks or cases grouped by agencies.">
                                                        </i>
                                                    </div> --}}
                            {{-- <h2 class="fw-bold">
                                                            {{ $totalAllocation }}
                                                        </h2>
                                                    </div> --}}
                            {{-- </div>
                                            </a> --}}
                            {{-- </div>
                                    </div>
                                </div> --}}
                            {{-- </div> --}}

                            <div class="col-lg-4 col-md-6 mb-3">
                                @include('dashboard.card', [
                                    'title' => 'Total Submitted Audits',
                                    'count' => $totalSubmittedAuditsbyAgency,
                                    'icon' => 'fa-check',
                                    'color' => 'success',
                                    'route' => route('submit_audited_list'),
                                ])
                            </div>
                            {{-- <div class="col-lg-4 col-md-4">
                                <div class="card cardboxInner h-100" style="background-color: #f2d6d1;">
                                    <div class="card-body">
                                        <div class="stat-widget-five text-center">
                                            <a href="{{ route('submit_audited_list') }}">
                                                <div class="text-center dib">
                                                    <div class="d-flex justify-content-center align-items-center mb-2">
                                                        <strong class="mr-2" style="color:black !important;">Total Submitted
                                                            Audits</strong>
                                                        <i class="fa fa-info-circle text-primary" data-toggle="tooltip"
                                                            data-placement="top"
                                                            title="This represents the total number of audits that have been submitted by agencies.">
                                                        </i>
                                                    </div>
                                                    <div class="stat-text">
                                                        <span class="count2">
                                                            {!! $totalSubmittedAuditsbyAgency > 0 ? $totalSubmittedAuditsbyAgency : 0 !!}
                                                        </span>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}

                            <div class="col-lg-4 col-md-6 mb-3">
                                @include('dashboard.card', [
                                    'title' => 'Saved Audits',
                                    'count' => $totalSavedAuditsbyAgency,
                                    'icon' => 'fa-save',
                                    'color' => 'warning',
                                    'route' => route('save_audited_list'),
                                ])
                            </div>

                            {{-- <div class="col-lg-4 col-md-4">
                                <div class="card cardboxInner score h-100" style="background-color: #d4e4bc;">
                                    <div class="card-body">
                                        <div class="stat-widget-five text-center">
                                            <a href="{{ route('save_audited_list') }}">
                                                <div class="text-center dib">
                                                    <div class="d-flex justify-content-center align-items-center mb-2">
                                                        <strong class="mr-2" style="color:black !important;">Saved
                                                            Audits</strong>
                                                        <i class="fa fa-info-circle text-primary" data-toggle="tooltip"
                                                            data-placement="top"
                                                            title="These are audits that have been saved but not yet submitted.">
                                                        </i>
                                                    </div>
                                                    <div class="stat-text">
                                                        <span class="count2">
                                                            {!! $totalSavedAuditsbyAgency > 0 ? $totalSavedAuditsbyAgency : 0 !!}
                                                        </span>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}


                            <div class="col-lg-4 col-md-6 mb-3">
                                @include('dashboard.card', [
                                    'title' => 'Audit Sent For Action Plan',
                                    'count' => $auditSendForActionPlan,
                                    'icon' => 'fa-paper-plane',
                                    'color' => 'info',
                                    'route' => route('audit.closure.list', 0),
                                ])
                            </div>


                            {{-- <!-- Audit Sent For Action Plan -->
                            <div class="col-lg-4 col-md-4 mt-1">
                                <div class="card cardboxInner h-100" style="background-color: #d4e4bc;">
                                    <div class="card-body">
                                        <div class="stat-widget-five text-center">
                                            <a href="{{ route('audit.closure.list', 0) }}">
                                                <div class="text-center
                                                dib">
                                                    <div class="d-flex justify-content-center align-items-center mb-2">
                                                        <strong class="mr-2" style="color:black !important;">Audit Sent For
                                                            Action Plan</strong>
                                                        <i class="fa fa-info-circle text-primary" data-toggle="tooltip"
                                                            data-placement="top"
                                                            title="Audits that have been sent to the agency/team for preparing an action plan.">
                                                        </i>
                                                    </div>
                                                    <div class="stat-text">
                                                        <span class="count2">
                                                            {!! $auditSendForActionPlan > 0 ? $auditSendForActionPlan : 0 !!}
                                                        </span>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}

                            <div class="col-lg-4 col-md-6 mb-3">
                                @include('dashboard.card', [
                                    'title' => 'Received Action Plan',
                                    'count' => $receivedforActionPlanAudits,
                                    'icon' => 'fa-file',
                                    'color' => 'secondary',
                                    'route' => route('audit.closure.list', 2),
                                ])
                            </div>

                            {{-- <!-- Received Action Plan -->
                            <div class="col-lg-4 col-md-4 mt-1">
                                <div class="card cardboxInner score h-100" style="background-color: #F9D179;">
                                    <div class="card-body">
                                        <div class="stat-widget-five text-center">
                                            <a href="{{ route('audit.closure.list', 2) }}">
                                                <div class="text-center
                                            dib">
                                                    <div class="d-flex justify-content-center align-items-center mb-2">
                                                        <strong class="mr-2" style="color:black !important;">Received Action
                                                            Plan</strong>
                                                        <i class="fa fa-info-circle text-primary" data-toggle="tooltip"
                                                            data-placement="top"
                                                            title="Audits for which the action plan has been submitted and received.">
                                                        </i>
                                                    </div>
                                                    <div class="stat-text">
                                                        <span class="count2">
                                                            {!! $receivedforActionPlanAudits > 0 ? $receivedforActionPlanAudits : 0 !!}
                                                        </span>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}

                            <div class="col-lg-4 col-md-6 mb-3">
                                @include('dashboard.card', [
                                    'title' => 'Closed Audits',
                                    'count' => $totalClosedAudits,
                                    'icon' => 'fa-check-circle',
                                    'color' => 'dark',
                                    'route' => route('audit.closure.list', 1),
                                ])

                            </div>
                        </div>
                    </div>
                </div>

                {{-- <!-- Closed Audits -->
                            <div class="col-lg-4 col-md-4 mt-1">
                                <div class="card cardboxInner score h-100">
                                    <div class="card-body">
                                        <div class="stat-widget-five text-center">
                                            <a href="{{ route('audit.closure.list', 1) }}">
                                                <div class="text-center dib">
                                                    <div class="d-flex justify-content-center align-items-center mb-2">
                                                        <strong class="mr-2" style="color:black !important;">Closed
                                                            Audits</strong>
                                                        <i class="fa fa-info-circle text-primary" data-toggle="tooltip"
                                                            data-placement="top"
                                                            title="Audits that have been reviewed and officially closed.">
                                                        </i>
                                                    </div>
                                                    <div class="stat-text">
                                                        <span class="count2">
                                                            {!! $totalClosedAudits > 0 ? $totalClosedAudits : 0 !!}

                                                        </span>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
 --}}


                {{-- QA Performance Table --}}

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header">
                        <h4 class="mb-0">QA Performance</h4>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>QA Name</th>
                                    <th>Audit Count</th>
                                    <th>Score</th>
                                    <th>Score Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- @php
                                    dd($auditors);
                                @endphp --}}
                                @forelse($auditors as $auditor)
                                    <tr>
                                        <td>{{ $auditor->name }}</td>
                                        <td>{{ $auditor->total_audits }}</td>
                                        <td>{{ $auditor->average_score }}</td>
                                        <td>{{ $auditor->score_percent }}%</td>
                                        {{-- <td>Test</td>
                        <td>8</td>
                        <td>34</td>
                        <td>56%</td> --}}
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No data available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>








                <!-- <div class="col-lg-3 col-md-6">
                                                                                                                                                                                                <div class="card">
                                                                                                                                                                                                    <div class="card-body">
                                                                                                                                                                                                        <div class="stat-widget-five text-center">
                                                                                                                                                                                                            <div class="text-center dib">
                                                                                                                                                                                                                <div class="stat-heading">Audit Submited - QC Pass</div>
                                                                                                                                                                                                                <div class="stat-text"><span class="count2">{{ $qa['totalpass'] ?? 0 }}</span></div>
                                                                                                                                                                                                            </div>
                                                                                                                                                                                                        </div>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                </div>
                                                                                                                                                                                            </div>
                                                                                                                                                                    
                                                                                                                                                                                            <div class="col-lg-3 col-md-6">
                                                                                                                                                                                                <div class="card">
                                                                                                                                                                                                    <div class="card-body">
                                                                                                                                                                                                        <div class="stat-widget-five text-center">
                                                                                                                                                                                                            <div class="text-center dib">
                                                                                                                                                                                                                <div class="stat-heading">Audit Submited - QC Fail</div>
                                                                                                                                                                                                                <div class="stat-text"><span class="count2">{{ $qa['totalfaild'] ?? 0 }}</span></div>
                                                                                                                                                                                                            </div>
                                                                                                                                                                                                        </div>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                </div>
                                                                                                                                                                                            </div> -->
            </div>
            <!-- <div class="row">
                                                                                                                                                                            <div class="col-lg-3 col-md-6">
                                                                                                                                                                                <div class="card">
                                                                                                                                                                                    <div class="card-body">
                                                                                                                                                                                        <div class="stat-widget-five text-center">
                                                                                                                                                                                            <div class="text-center dib">
                                                                                                                                                                                                <div class="stat-heading">Total Pending To Approved</div>
                                                                                                                                                                                                <div class="stat-text"><span class="count2">{{ $qc['totalpending'] ?? 0 }}</span>
                                                                                                                                                                                                </div>
                                                                                                                                                                                            </div>
                                                                                                                                                                                        </div>
                                                                                                                                                                                    </div>
                                                                                                                                                                                </div>
                                                                                                                                                                            </div>

                                                                                                                                                                            <div class="col-lg-3 col-md-6">
                                                                                                                                                                                <div class="card">
                                                                                                                                                                                    <div class="card-body">
                                                                                                                                                                                        <div class="stat-widget-five text-center">
                                                                                                                                                                                            <div class="text-center dib">
                                                                                                                                                                                                <div class="stat-heading">Audit Approved</div>
                                                                                                                                                                                                <div class="stat-text"><span
                                                                                                                                                                                                        class="count2">{{ $qc['totalApproved'] ?? 0 }}</span></div>
                                                                                                                                                                                            </div>
                                                                                                                                                                                        </div>
                                                                                                                                                                                    </div>
                                                                                                                                                                                </div>
                                                                                                                                                                            </div>
                                                                                                                                                                            <div class="col-lg-3 col-md-6">
                                                                                                                                                                                <div class="card">
                                                                                                                                                                                    <div class="card-body">
                                                                                                                                                                                        <div class="stat-widget-five text-center">
                                                                                                                                                                                            <div class="text-center dib">
                                                                                                                                                                                                <div class="stat-heading">Audit Approved Without Changes</div>
                                                                                                                                                                                                <div class="stat-text"><span class="count2">{{ $qc['totalpass'] ?? 0 }}</span>
                                                                                                                                                                                                </div>
                                                                                                                                                                                            </div>
                                                                                                                                                                                        </div>
                                                                                                                                                                                    </div>
                                                                                                                                                                                </div>
                                                                                                                                                                            </div>
                                                                                                                                                                            <div class="col-lg-3 col-md-6">
                                                                                                                                                                                <div class="card">
                                                                                                                                                                                    <div class="card-body">
                                                                                                                                                                                        <div class="stat-widget-five text-center">
                                                                                                                                                                                            <div class="text-center dib">
                                                                                                                                                                                                <div class="stat-heading">Audit Approved With Changes</div>
                                                                                                                                                                                                <div class="stat-text"><span
                                                                                                                                                                                                        class="count2">{{ $qc['totalpassChange'] ?? 0 }}</span></div>
                                                                                                                                                                                            </div>
                                                                                                                                                                                        </div>
                                                                                                                                                                                    </div>
                                                                                                                                                                                </div>
                                                                                                                                                                            </div> -->
            {{-- <div class="col-lg-3 col-md-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="stat-widget-five text-center">
                                                <div class="text-center dib">
                                                    <div class="stat-heading">Audit Saved</div>
                                                    <div class="stat-text"><span class="count2">{{$qc['totalsaved'] ?? 0}}</span>
                </div>
            </div>
        </div>
    </div>
</div>
</div> --}}

        </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4"></script>

        {{-- <div style="max-width: 90vw; margin: 4vh auto; display: flex; gap: 2vh; flex-wrap: wrap;">

    <!-- Audit Allocation Chart -->
    <div style="flex: 1; min-width: 40vh; background: #fff; border-radius: 1vh; padding: 2vh; box-shadow: 0px 0.4vh 2vh rgba(0,0,0,0.1);">
        <h3 style="text-align: center; margin-bottom: 2vh; font-family: Arial, sans-serif; color: #333;">
            Audit Allocation Overview
        </h3>
        <canvas id="auditChart" style="height: 40vh; width: 100%;"></canvas>
    </div>

    <!-- Audit Submission Chart -->
    <div style="flex: 1; min-width: 40vh; background: #fff; border-radius: 1vh; padding: 2vh; box-shadow: 0px 0.4vh 2vh rgba(0,0,0,0.1);">
        <h3 style="text-align: center; margin-bottom: 2vh; font-family: Arial, sans-serif; color: #333;">
            Audit Submission Overview
        </h3>
        <canvas id="auditChart1" style="height: 40vh; width: 100%;"></canvas>
    </div>

</div> --}}



        <!-- Modal -->
        <div id="auditModal"
            style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
    background:rgba(0,0,0,0.6); justify-content:center; align-items:center;">
            <div style="background:white; padding:20px; width:400px; border-radius:8px; position:relative;">
                <h3 id="modalTitle"></h3>
                <ul id="auditList"></ul>
                <button onclick="document.getElementById('auditModal').style.display='none'"
                    style="margin-top:10px;">Close</button>
            </div>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                let months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun"];
                let allocatedAudits = [12, 19, 3, 5, 2, 3];
                let assignedAudits = [8, 15, 2, 4, 1, 2];

                // Demo assigned audit details with user name
                let assignedDetails = {
                    Jan: [{
                            audit: "Audit 101",
                            user: "John Doe"
                        },
                        {
                            audit: "Audit 102",
                            user: "Jane Smith"
                        },
                        {
                            audit: "Audit 201",
                            user: "Michael Scott"
                        },
                        {
                            audit: "Audit 202",
                            user: "Dwight Schrute"
                        }
                    ],
                    Feb: [{
                            audit: "Audit 201",
                            user: "Michael Scott"
                        },
                        {
                            audit: "Audit 202",
                            user: "Dwight Schrute"
                        }
                    ],
                    Mar: [{
                        audit: "Audit 301",
                        user: "Jim Halpert"
                    }],
                    Apr: [{
                        audit: "Audit 401",
                        user: "Pam Beesly"
                    }],
                    May: [{
                        audit: "Audit 501",
                        user: "Stanley Hudson"
                    }],
                    Jun: [{
                        audit: "Audit 601",
                        user: "Kevin Malone"
                    }]
                };

                let ctx = document.getElementById('auditChart').getContext('2d');
                let auditChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: months,
                        datasets: [{
                                label: 'Allocated Audits',
                                data: allocatedAudits,
                                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Assigned Audits',
                                data: assignedAudits,
                                backgroundColor: 'rgba(255, 99, 132, 0.6)',
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        title: {
                            display: true,
                            text: 'Allocated vs Assigned Audits'
                        },
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                }
                            }]
                        }
                    }
                });

                // Click event for Chart.js v2
                document.getElementById('auditChart').onclick = function(evt) {
                    let activePoints = auditChart.getElementAtEvent(evt);
                    if (activePoints.length > 0) {
                        let firstPoint = activePoints[0];
                        let datasetIndex = firstPoint._datasetIndex;
                        let index = firstPoint._index;

                        // Only trigger for Assigned Audits (datasetIndex 1)
                        if (datasetIndex === 1) {
                            let month = months[index];
                            let details = assignedDetails[month] || [];

                            document.getElementById('modalTitle').textContent = `Assigned Audits - ${month}`;
                            let list = document.getElementById('auditList');
                            list.innerHTML = "";

                            if (details.length) {
                                details.forEach(item => {
                                    let li = document.createElement('li');
                                    li.textContent = `${item.audit} → Assigned to: ${item.user}`;
                                    list.appendChild(li);
                                });
                            } else {
                                let li = document.createElement('li');
                                li.textContent = "No assigned audits.";
                                list.appendChild(li);
                            }

                            document.getElementById('auditModal').style.display = "flex";
                        }
                    }
                };
            });
        </script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const ctx = document.getElementById("auditChart1").getContext("2d");

                let currentHour = new Date().getHours();

                // Generate dummy data only up to the current hour
                let labels = [];
                let data = [];

                for (let hour = 0; hour <= currentHour; hour++) {
                    labels.push(hour + ":00");
                    data.push(Math.floor(Math.random() * 10)); // random audits count
                }

                // Fill remaining hours with null (no future data)
                for (let hour = currentHour + 1; hour < 24; hour++) {
                    labels.push(hour + ":00");
                    data.push(null); // No audits in the future
                }

                new Chart(ctx, {
                    type: "line",
                    data: {
                        labels: labels,
                        datasets: [{
                            label: "Audits Performed",
                            data: data,
                            borderColor: "#4bc0c0",
                            backgroundColor: "rgba(75,192,192,0.3)",
                            fill: true,
                            tension: 0.4,
                            spanGaps: false
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: "Hour of Day"
                                }
                            },
                            y: {
                                title: {
                                    display: true,
                                    text: "Audits Count"
                                },
                                beginAtZero: true
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.parsed.y !== null ?
                                            context.parsed.y + " audits" :
                                            "No data yet";
                                    }
                                }
                            }
                        }
                    }
                });
            });
        </script>



        </div>
    @endrole

    {{-- ===================================================================== --}}
    <!-- Old Client Dashboard Here... -->
    {{-- ====================================================================== --}}

    </div>
    <!-- .animated -->
    </div>
    <!-- /.content -->
    <div class="clearfix"></div>

    <!-- for calender -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        // Shared onDayCreate to highlight Sundays
        function highlightSundays(_, __, ___, dayElem) {
            if (dayElem.dateObj.getDay() === 0) {
                dayElem.classList.add("sunday");
            }
        }

        // Initialize Start Date Picker
        // const startDatePicker = flatpickr("#start-date", {
        //     dateFormat: "Y-m-d",
        //     onChange: function(selectedDates, dateStr, instance) {
        //         endDatePicker.set('minDate', dateStr);
        //     },
        //     onDayCreate: highlightSundays
        // });

        // // Initialize End Date Picker
        // const endDatePicker = flatpickr("#end-date", {
        //     dateFormat: "Y-m-d",
        //     onDayCreate: highlightSundays
        // });
    </script>
@endsection
@section('js')
    <!-- <script src="{{ URL::asset('js/highmaps.js') }}"></script>
                                                                        <script src="{{ URL::asset('js/exporting.js') }}"></script>
                                                                        <script src="{{ URL::asset('js/in-all.js') }}"></script>
                                                                        <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
                                                                        <script src="{{ URL::asset('js/dashboard.js') }}"></script>
                                                                        <script src="https://code.highcharts.com/modules/pareto.js"></script>
                                                                        <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
                                                                        <script>
                                                                            jQuery(document).ready(function() {

                                                                                jQuery('.flatpickr').flatpickr({
                                                                                    dateFormat: "yyyy-mm-dd"
                                                                                });

                                                                                indiaMap([]);
                                                                                // jQuery('#add-product').modal('show')
                                                                                // pareto();
                                                                                jQuery('#collection_manager_table').DataTable()
                                                                                jQuery('#nationalResult').trigger('click');
                                                                            })
                                                                        </script> -->

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <!--V Date for DumpDownload Start --------------------------------------->
    <script>
        jQuery(document).ready(function() {
            // jQuery('.flatpickr').flatpickr({
            //     dateFormat: "yy-mm-dd",
            //     onSelect: function() {
            //         jQuery(this).flatpickr('hide');
            //     }
            // });

            indiaMap([]);
        });
    </script>
    <!--V Date for DumpDownload End-- --------------------------------------->

    <!--V Filter Dashbaord Start -------------------------------------------->
    <!-- <script>
        document.addEventListener("DOMContentLoaded", function() {
            const filterDropdown = document.getElementById("time-filter");
            const customDateContainer = document.getElementById("custom-date-range");
            const filterButton = document.getElementById("filter-button");

            // Initial check for the dropdown value on page load
            toggleCustomFilter(filterDropdown.value);

            // Listen for changes to the dropdown
            filterDropdown.addEventListener("change", function() {
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
        // document.addEventListener("DOMContentLoaded", function() {
        //     const filterDropdown = document.getElementById("time-filter");
        //     const customDateContainer = document.getElementById("custom-date-range");
        //     const filterButton = document.getElementById("filter-button");
        //     const startDateInput = document.getElementById("start-date");
        //     const endDateInput = document.getElementById("end-date");

        //     toggleCustomFilter(filterDropdown.value);

        //     filterDropdown.addEventListener("change", function() {
        //         toggleCustomFilter(filterDropdown.value);
        //     });

        //     startDateInput.addEventListener("change", function() {
        //         setMinEndDate(startDateInput.value);
        //     });

        //     function toggleCustomFilter(selectedValue) {
        //         if (selectedValue === "custom") {
        //             customDateContainer.style.display = "block";
        //             filterButton.style.display = "inline-block";
        //         } else {
        //             customDateContainer.style.display = "none";
        //             filterButton.style.display = "none";
        //         }
        //     }

        //     function setMinEndDate(startDate) {
        //         const formattedStartDate = new Date(startDate);
        //         const formattedStartDateString = formattedStartDate.toISOString().split('T')[0];

        //         endDateInput.setAttribute("min", formattedStartDateString);

        //         const formattedEndDate = new Date(endDateInput.value);
        //         if (formattedEndDate < formattedStartDate) {
        //             endDateInput.value = '';
        //         }
        //     }

        //     // jQuery('.flatpickr').flatpickr({
        //     //     dateFormat: "yy-mm-dd",
        //     //     onSelect: function() {
        //     //         jQuery(this).flatpickr('hide');
        //     //     }
        //     // });

        // });

        document.addEventListener("DOMContentLoaded", function() {

            const filterDropdown = document.getElementById("time-filter");
            const customDateContainer = document.getElementById("custom-date-card");
            const filterButton = document.getElementById("filter-button");
            const startDateInput = document.getElementById("start-date");
            const endDateInput = document.getElementById("end-date");

            // Initialize all Flatpickr inputs
            flatpickr(".flatpickr", {
                dateFormat: "Y-m-d"
            });

            // Run only if filter dropdown exists
            if (filterDropdown) {
                toggleCustomFilter(filterDropdown.value);
                filterDropdown.addEventListener("change", function() {
                    if (this.value === "current") {
                        window.location.href = "{{ route('dashboard') }}";
                    } else {
                        toggleCustomFilter(this.value);
                    }
                });
            }

            // Start Date Change
            if (startDateInput && endDateInput) {
                startDateInput.addEventListener("change", function() {
                    setMinEndDate(startDateInput.value);
                });
            }

            function toggleCustomFilter(selectedValue) {
                if (!customDateContainer) return;
                if (selectedValue === "custom") {
                          customDateContainer.style.display = "block";
                    if (filterButton) {
                        filterButton.style.display = "inline-block";
                    }
                } else {
                    customDateContainer.style.display = "none";
                    if (filterButton) {
                        filterButton.style.display = "none";
                    }
                }
            }

            function setMinEndDate(startDate) {
                if (!endDateInput || !startDate) return;
                endDateInput.setAttribute("min", startDate);
                if (endDateInput.value && endDateInput.value < startDate) {
                    endDateInput.value = "";
                }
            }

        });
    </script>

    <!--V Filter Dashbaord End ---------------------------------------------->

    <!--V Current Month Filter Refresh Start -------------------------------->
    {{-- <script type="text/javascript">
        // document.addEventListener("DOMContentLoaded", function() {
        //     const filterDropdown = document.getElementById("time-filter");
        //     const clearLink = document.getElementById("clear-link");

        //     // Handle dropdown change
        //     filterDropdown.addEventListener("change", function() {
        //         const selectedValue = filterDropdown.value;

        //         if (selectedValue === "current") {
        //             // Redirect to the dashboard route
        //             window.location.href = clearLink.href;
        //         } else if (selectedValue === "custom") {
        //             // Call toggleCustomFilter for custom date selection
        //             toggleCustomFilter();
        //         }
        //     });
        // });
    </script> --}}


    <script>
        $(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
    <!--V Current Month Filter Refresh End ---------------------------------->
@endsection
