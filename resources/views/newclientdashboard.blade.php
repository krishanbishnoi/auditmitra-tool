<?php // echo "<pre>"; print_r($auditData); die;
?>

@extends('layouts.master')
@section('css')
    <!-- {{ asset('clientdashboard/images/overall-audit.svg') }} -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ URL::asset('/public/clientdashboard/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('/public/clientdashboard/css/style.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
    </script>
    <style>
        body {
            display: block;
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

        #barChart {
            width: 90%;
            margin-left: 10px;
            max-width: 600px;
            margin-top: 10px;
            margin-bottom: 50px;
            float: left;
            max-height: 250px;
        }

        .vdate {
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
            margin: 0;/
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


        .stat-card {
            border-radius: 16px;
            padding: 20px;
            color: #fff;
            background: #ccc;
            height: 100%;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.2);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .stat-header h5 {
            font-size: 18px;
            font-weight: 600;
            margin: 0;
        }

        .stat-header .fa-info-circle {
            font-size: 16px;
            opacity: 0.85;
            cursor: pointer;
        }

        .stat-value {
            font-size: 30px;
            font-weight: bold;
            margin-top: 8px;
        }

        .stat-chip {
            background: rgba(255, 255, 255, 0.15);
            padding: 6px 12px;
            border-radius: 10px;
            font-size: 14px;
            display: flex;
            justify-content: space-between;
            color: #000000;
            margin-bottom: 10px;
            /* Add bottom space */
        }

        .stat-chip:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        .small-stats div {
            background: rgba(255, 255, 255, 0.15);
            padding: 6px 10px;
            font-size: 13px;
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .small-stats div:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        .tbleDiv tr:hover td {
            background-color: #f8f9fa;
        }

        /* 👇 Individual card colors */

        .stat-allocation {
            background: linear-gradient(135deg, #bbdefb, #64b5f6);
            color: #0d47a1;
        }

        .stat-submitted {
            background: linear-gradient(135deg, #ffcdd2, #ef9a9a);
            color: #b71c1c;
        }

        .stat-action {
            background: linear-gradient(135deg, #c8e6c9, #81c784);
            color: #1b5e20;
        }

        .stat-closed {
            background: linear-gradient(135deg, #f3e5f5, #ce93d8);
            color: #6a1b9a;
        }

        .table th,
        .table td {
            padding: 1rem;
            font-size: 16px;
        }

        .table thead th {
            font-weight: 600;
        }

        .stat-body {
            padding-top: 8px;
        }

        .stat-value {
            font-size: 18px;
        }



        /* Chart improvements */
        .chart-container {
            position: relative;
            height: 400px;
            width: 100%;
            margin: 0 auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .chart-container canvas {
            max-height: 100% !important;
            max-width: 100% !important;
        }






        /* Chart/Table container */
        .view-container {
            position: relative;
            min-height: 400px;
        }

        .chart-view,
        .table-view {
            transition: opacity 0.3s ease;
        }

        .chart-view.hidden,
        .table-view.hidden {
            display: none;
            opacity: 0;
        }

        .chart-view.visible,
        .table-view.visible {
            display: block;
            opacity: 1;
        }

        /* Enhanced card styling */
        .cardBox {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }



        /* Loading animation */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Responsive chart */
        @media (max-width: 768px) {
            .chart-container {
                height: 300px;
                padding: 10px;
            }


        }

        @media (max-width: 576px) {
            .chart-container {
                height: 250px;
                padding: 5px;
            }
        }

        .table td,
        .table th {
            white-space: normal !important;
            /* allow wrapping */
            word-wrap: break-word;
            /* break long words */
            word-break: break-word;
            /* extra fallback */
        }

        /* new style */

        .btn+.btn {
            margin-left: -0.50rem !important;
            /* smaller spacing */
        }
    </style>

    <script>
        $(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endsection
@section('content')



    <div class="mb-3">


        <div class="toolbar-wrapper position-fixed w-95 bg-white border-bottom shadow-sm"
            style="top:65px; z-index:900; padding:10px;margin-left:2px;width:1020px;border-radius:10px;left:61%; transform:translateX(-50%);">
            <div class="row align-items-center">
                <!-- Left side: 3 buttons -->
                <div class="col d-flex flex-wrap align-items-center">
                    <button type="button" class="btn btn-primary btn-sm me-3 h-100" data-bs-toggle="modal"
                        data-bs-target="#myModal">
                        Raw Dump
                    </button>
                    @if (auth()->user()->client_id == 74 || auth()->user()->client_id == 13)
                        <button type="button" class="btn btn-primary btn-sm me-3 h-100" data-bs-toggle="modal"
                            data-bs-target="#openPointersModal">
                            Open Pointers Dump
                        </button>
                    @endif
                    <button type="button" class="btn btn-secondary btn-sm h-100" data-bs-toggle="modal"
                        data-bs-target="#ScheduleAuditExport">
                        Schedule Audits
                    </button>
                </div>

                <!-- Right side: 2 buttons + filters -->
                <div class="col-auto d-flex align-items-center">
                    @if (auth()->check() && (auth()->user()->id == 15 || auth()->user()->id == 13))
                        <a @if (auth()->user()->id == 15) href="https://qmtool.qdegrees.com/moneyviewdashboard" 
                        @else 
                            href="https://qmtool.qdegrees.com/demodashboard" @endif
                            target="_blank" class="btn btn-info btn-sm me-3 h-100">
                            Calls Dashboard
                        </a>
                    @endif
                    @if (auth()->check() && auth()->user()->client_id == 13)
                        <button type="submit" class="btn btn-primary btn-sm me-3 h-100" onclick="switchUser(1);">
                            Audit Agency
                        </button>
                        <button type="submit" class="btn btn-danger btn-sm me-3 h-100" onclick="switchUser(2);">
                            Quality Auditor
                        </button>
                    @endif

                     @if (auth()->check() && auth()->user()->client_id == 2)
                        <button type="submit" class="btn btn-primary btn-sm me-3 h-100" onclick="switchUser(3);">
                            Audit Agency
                        </button>
                        <button type="submit" class="btn btn-danger btn-sm me-3 h-100" onclick="switchUser(4);">
                            Quality Auditor
                        </button>
                    @endif

                    

                    <form method="GET" action="{{ route('getClientDashboard') }}" class="d-flex align-items-center mb-0"
                        id="filter-form">
                        <select class="form-control form-control-sm me-3 h-100" name="audit_cycle_id"
                            style="width:auto; min-width:120px;">
                            @foreach ($auditCycle as $ac)
                                <option value="{{ $ac->id }}" {{ $currentCycleId == $ac->id ? 'selected' : '' }}>
                                    {{ $ac->name }}
                                </option>
                            @endforeach
                            <option value="0">Go Back</option>
                        </select>

                        <select class="form-control form-control-sm me-3 h-100" name="audit_type"
                            style="width:auto; min-width:120px;">
                            <option value="all">All</option>
                            <option value="agency" selected>Agency</option>
                            <option value="agency_repo">Agency Repo</option>
                            <option value="branch">Branch</option>
                            <option value="branch_repo">Branch Repo</option>
                            <option value="yard">Yard</option>
                            <option value="yard_repo">Yard Repo</option>
                        </select>

                        <button type="submit" class="btn btn-outline-secondary btn-sm h-100 p-1">
                            <img src="{{ URL::asset('/public/images/filter-icon.svg') }}" alt="Filter" width="20"
                                height="20">
                        </button>
                    </form>
                </div>
            </div>
        </div>





        <!-- V Model For Audit Dump Download Start ---------->

        <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-xl modal-dialog-centered"> <!-- Made modal extra large -->
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="myModalLabel">Audit Data Download</h5>
                        <button type="button" class="btn-close btn-close-white" data-dismiss="modal"
                            aria-label="Close"></button> <!-- Add Close button -->
                    </div>
                    <form method="GET" action="{{ route('auditdumpdownload') }}" autocomplete="off">
                        <div class="modal-body">
                            <div class="row">
                                @csrf
                                <div class="col-md-3 form-group">
                                    <label>Start Date*</label>
                                    <input name="start_date" type="date" data-date-format="yyyy-mm-dd"
                                        class="form-control" placeholder="Select Start Date" required />
                                </div>

                                <div class="col-md-3 form-group">
                                    <label>End Date*</label>
                                    <input name="end_date" type="date" data-date-format="yyyy-mm-dd" class="form-control"
                                        placeholder="Select End Date" required />
                                </div>

                                <div class="col-md-3 form-group">
                                    <label>Action*</label>
                                    <button type="submit" class="btn btn-primary form-control">Download</button>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-end">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="openPointersModal" tabindex="-1" aria-labelledby="openPointerslLabel">
            <div class="modal-dialog modal-xl modal-dialog-centered"> <!-- Made modal extra large -->
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="openPointerslLabel">Open Pointers (unsatisfactory sub-parameters)
                            sheet</h5>
                        <button type="button" class="btn-close btn-close-white" data-dismiss="modal"
                            aria-label="Close"></button> <!-- Add Close button -->
                    </div>
                    <form method="GET" action="{{ route('openPointersDump') }}" autocomplete="off">
                        <div class="modal-body">
                            <div class="row">
                                @csrf
                                <div class="col-md-3 form-group">
                                    <label>Start Date*</label>
                                    <input name="start_date" type="date" data-date-format="yyyy-mm-dd"
                                        class="form-control" placeholder="Select Start Date" required />
                                </div>

                                <div class="col-md-3 form-group">
                                    <label>End Date*</label>
                                    <input name="end_date" type="date" data-date-format="yyyy-mm-dd"
                                        class="form-control" placeholder="Select End Date" required />
                                </div>

                                <div class="col-md-3 form-group">
                                    <label>Action*</label>
                                    <button type="submit" class="btn btn-primary form-control">Download</button>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-end">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>



        <!-- V Model For ScheduleAuditExport Start ---------->

        <div class="modal fade" id="ScheduleAuditExport" tabindex="-1" aria-labelledby="ScheduleAuditExportModalLabel">
            <div class="modal-dialog modal-xl modal-dialog-centered"> <!-- Made modal extra large -->
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="ScheduleAuditExportModalLabel">Schedule Audits Data Download</h5>
                        <button type="button" class="btn-close btn-close-white" data-dismiss="modal"
                            aria-label="Close"></button> <!-- Add Close button -->
                    </div>
                    <form method="GET" action="{{ route('scheduleAuditDownload') }}" autocomplete="off">
                        <div class="modal-body">
                            <div class="row">
                                @csrf
                                <div class="col-md-3 form-group">
                                    <label>Start Date*</label>
                                    <input name="start_date" type="date" data-date-format="yyyy-mm-dd"
                                        class="form-control" placeholder="Select Start Date" required />
                                </div>

                                <div class="col-md-3 form-group">
                                    <label>End Date*</label>
                                    <input name="end_date" type="date" data-date-format="yyyy-mm-dd"
                                        class="form-control" placeholder="Select End Date" required />
                                </div>

                                <div class="col-md-3 form-group">
                                    <label>Action*</label>
                                    <button type="submit" class="btn btn-primary form-control">Download</button>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-end">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="row g-2" style="margin-top:70px;">
            <!-- 🚀 Total Allocation -->
            <div class="col-md-3 col-xl-3 mb-2">
                <div class="stat-card stat-submitted" id="audit_allocation" data-toggle="tooltip"
                    title=" Total allocation for Last Month (LM) and Current Month (CM)">
                    <div class="stat-header">
                        <h5>Total Allocation</h5>
                        <i class="fa fa-info-circle" data-toggle="tooltip" title="Total tasks/cases allocated"></i>
                    </div>
                    <div class="stat-value" style="padding-top: 10px ">
                        LM:<span class="text-dark">{{ $auditData['allocation_data']['previousCycleCount'] }}</span>
                        |
                        CM:<span class="text-dark">{{ $auditData['allocation_data']['currentCycleCount'] }}</span>
                        @if ($auditData['allocation_data']['currentCycleCount'] > $auditData['allocation_data']['previousCycleCount'])
                            <img src="{{ URL::asset('/public/clientdashboard/images/downarrow-green.svg') }}"
                                style="vertical-align:middle; margin: 0 3px;" alt="Up" width="10">
                        @else
                            <img src="{{ URL::asset('/public/clientdashboard/images/downarrow.svg') }}"
                                style="vertical-align:middle; margin: 0 3px;" alt="Down" width="10">
                        @endif

                    </div>
                </div>
            </div>

            <div class="col-md-3 col-xl-3 mb-2">
                <div class="stat-card stat-closed" id="audit_submitted" data-toggle="tooltip"
                    title=" Audits submitted for Last Month (LM) and Current Month (CM)">

                    @php
                        if (auth()->user()->client_id == 74 && isset($auditData['tabs'])) {
                            $pPrev = $auditData['tabs']['physical']['previous']['audit_count'];
                            $pCurr = $auditData['tabs']['physical']['current']['audit_count'];

                            $vPrev = $auditData['tabs']['virtual']['previous']['audit_count'];
                            $vCurr = $auditData['tabs']['virtual']['current']['audit_count'];

                            $tPrev = $pPrev + $vPrev;
                            $tCurr = $pCurr + $vCurr;
                        }
                    @endphp

                    <div class="stat-header d-flex justify-content-between align-items-center">
                        <h5>Audits Submitted</h5>

                        @if (auth()->user()->client_id == 74 && isset($auditData['tabs']))
                            <div class="d-flex align-items-center">
                                <span class="fw-bold text-dark me-1">
                                    LM:{{ $tPrev }} | CM:{{ $tCurr }}
                                </span>

                                @if ($tCurr > $tPrev)
                                    <img src="{{ URL::asset('/public/clientdashboard/images/downarrow-green.svg') }}"
                                        width="10" style="margin-left:3px;">
                                @elseif ($tCurr < $tPrev)
                                    <img src="{{ URL::asset('/public/clientdashboard/images/downarrow.svg') }}"
                                        width="10" style="margin-left:3px;">
                                @else
                                @endif
                            </div>
                            <i class="fa fa-info-circle" data-toggle="tooltip" title="Total audits submitted"></i>
                        @else
                            <i class="fa fa-info-circle" data-toggle="tooltip" title="Total audits submitted"></i>
                        @endif
                    </div>


                    @if (auth()->user()->client_id == 74 && isset($auditData['tabs']))
                        <div class="stat-value d-flex justify-content-between">

                            {{-- Physical --}}
                            <div class="text-center">
                                <small class="text-muted">Physical</small><br>

                                @php
                                    $pPrev = $auditData['tabs']['physical']['previous']['audit_count'];
                                    $pCurr = $auditData['tabs']['physical']['current']['audit_count'];
                                @endphp

                                <span class="text-dark fw-bold">
                                    {{ $pPrev }} | {{ $pCurr }}
                                </span>

                                @if ($pCurr > $pPrev)
                                    <img src="{{ URL::asset('/public/clientdashboard/images/downarrow-green.svg') }}"
                                        style="vertical-align:middle; margin-left:3px;" width="10">
                                @elseif ($pCurr < $pPrev)
                                    <img src="{{ URL::asset('/public/clientdashboard/images/downarrow.svg') }}"
                                        style="vertical-align:middle; margin-left:3px;" width="10">
                                @endif
                            </div>

                            {{-- Virtual --}}
                            <div class="text-center">
                                <small class="text-muted">Virtual</small><br>

                                @php
                                    $vPrev = $auditData['tabs']['virtual']['previous']['audit_count'];
                                    $vCurr = $auditData['tabs']['virtual']['current']['audit_count'];
                                @endphp

                                <span class="text-dark fw-bold">
                                    {{ $vPrev }} | {{ $vCurr }}
                                </span>

                                @if ($vCurr > $vPrev)
                                    <img src="{{ URL::asset('/public/clientdashboard/images/downarrow-green.svg') }}"
                                        style="vertical-align:middle; margin-left:3px;" width="10">
                                @elseif ($pCurr < $pPrev)
                                    <img src="{{ URL::asset('/public/clientdashboard/images/downarrow.svg') }}"
                                        style="vertical-align:middle; margin-left:3px;" width="10">
                                @endif
                            </div>

                        </div>
                    @else
                        {{-- Existing UI --}}
                        <div class="stat-value" style="padding-top:10px">
                            LM:<span class="text-dark">{{ $auditData['previous_cycle_count'] }}</span>
                            |
                            CM:<span class="text-dark">{{ $auditData['current_cycle_count'] ?? 0 }}</span>

                            @if ($auditData['current_cycle_count'] > $auditData['previous_cycle_count'])
                                <img src="{{ URL::asset('/public/clientdashboard/images/downarrow-green.svg') }}"
                                    style="vertical-align:middle; margin: 0 3px;" width="10">
                            @else
                                <img src="{{ URL::asset('/public/clientdashboard/images/downarrow.svg') }}"
                                    style="vertical-align:middle; margin: 0 3px;" width="10">
                            @endif
                        </div>
                    @endif


                </div>
            </div>

            <div class="col-md-3 col-xl-3 mb-2">
                <div class="stat-card stat-action"data-toggle="tooltip"
                    title="Audit score for Last Month (LM) and Current Month (CM)">
                    <div class="stat-header">
                        <h5>Audit Score</h5>
                        <i class="fa fa-info-circle" data-toggle="tooltip" title="audit score"></i>
                    </div>
                    <div class="stat-value" style="padding-top: 10px ">
                        LM:<span class="text-dark">{{ round($auditData['previous_cycle_score']) }}</span>
                        |
                        CM:<span class="text-dark">{{ round($auditData['current_cycle_score'] ?? 0) }}</span>
                        @if ($auditData['current_cycle_score'] > $auditData['previous_cycle_score'])
                            <img src="{{ URL::asset('/public/clientdashboard/images/downarrow-green.svg') }}"
                                style="vertical-align:middle; margin: 0 3px;" alt="Up" width="10">
                        @else
                            <img src="{{ URL::asset('/public/clientdashboard/images/downarrow.svg') }}"
                                style="vertical-align:middle; margin: 0 3px;" alt="Down" width="10">
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-xl-3 mb-2">
                <div class="stat-card stat-allocation" id="action_planning_count" data-toggle="tooltip"
                    title=" Action Planning for Last Month (LM) and Current Month (CM)">
                    <div class="stat-header">
                        <h5>Action Planning</h5>
                        <i class="fa fa-info-circle" data-toggle="tooltip" title="Total action plan"></i>
                    </div>
                    <div class="stat-value" style="padding-top: 10px ">
                        LM:<span
                            class="text-dark">{{ $auditData['getActionPlanningData']['previous_cycle']['approved'] }}</span>
                        |
                        CM: <span
                            class="text-dark">{{ $auditData['getActionPlanningData']['current_cycle']['approved'] ?? 0 }}</span>
                        @if (
                            $auditData['getActionPlanningData']['current_cycle']['approved'] >
                                $auditData['getActionPlanningData']['previous_cycle']['approved']
                        )
                            <img src="{{ URL::asset('/public/clientdashboard/images/downarrow-green.svg') }}"
                                style="vertical-align:middle; margin: 0 3px;" alt="Up" width="10">
                        @elseif (
                            $auditData['getActionPlanningData']['current_cycle']['approved'] <
                                $auditData['getActionPlanningData']['previous_cycle']['approved']
                        )
                            <img src="{{ URL::asset('/public/clientdashboard/images/downarrow.svg') }}"
                                style="vertical-align:middle; margin: 0 3px;" alt="Down" width="10">
                        @endif
                    </div>
                </div>
            </div>


        </div>

        <div class="mb-3" id="audit_agency_wise_view">
            <div class="row px-md-1">
                <div class="col-md-12 px-md-2">
                    <div class="cardBox h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="m-0">
                                <span>Audit Partners Wise View</span>
                                <i class="fa fa-info-circle text-primary ms-2" data-toggle="tooltip" data-placement="top"
                                    title="Audit agency wise combined view.">
                                </i>
                            </h6>
                        </div>
                        <div id="audit_agency_wise_data">
                            <div class="table-responsive tbleDiv">
                                <table class="table table-bordered align-middle text-center shadow-sm rounded"
                                    style="font-size: 16px;">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Audit Partners</th>
                                            <th>Audit Allocated</th>
                                            <th>Audit Submitted</th>
                                            <th>Audit Score</th>
                                            <th>Audit Action Planning</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($auditData['getAuditAgencyWiseData']['allocation'] as $alloc)
                                            <tr>
                                                <td>{{ App\Helpers\Helper::getUser($alloc->agency_id) }}</td>
                                                <td>{{ $alloc->previousCycleCount }}
                                                    |
                                                    {{ $alloc->currentCycleCount }}
                                                    @if ($alloc->currentCycleCount > $alloc->previousCycleCount)
                                                        <img src="{{ URL::asset('/public/clientdashboard/images/downarrow-green.svg') }}"
                                                            style="vertical-align:middle; margin: 0 3px;" alt="Up"
                                                            width="8">
                                                    @else
                                                        <img src="{{ URL::asset('/public/clientdashboard/images/downarrow.svg') }}"
                                                            style="vertical-align:middle; margin: 0 3px;" alt="Down"
                                                            width="8">
                                                    @endif
                                                </td>
                                                @foreach ($auditData['getAuditAgencyWiseData']['auditCountAndScore'] as $agendata)
                                                    @if ($agendata->agency_id == $alloc->agency_id)
                                                        <td>{{ $agendata->previousCycleCount }}
                                                            |
                                                            {{ $agendata->currentCycleCount }}
                                                            @if ($agendata->currentCycleCount > $agendata->previousCycleCount)
                                                                <img src="{{ URL::asset('/public/clientdashboard/images/downarrow-green.svg') }}"
                                                                    style="vertical-align:middle; margin: 0 3px;"
                                                                    alt="Up" width="8">
                                                            @else
                                                                <img src="{{ URL::asset('/public/clientdashboard/images/downarrow.svg') }}"
                                                                    style="vertical-align:middle; margin: 0 3px;"
                                                                    alt="Down" width="8">
                                                            @endif
                                                        </td>
                                                        <td>{{ $agendata->previousCycleScore }}
                                                            |
                                                            {{ $agendata->currentCycleScore }}
                                                            @if ($agendata->currentCycleScore > $agendata->previousCycleScore)
                                                                <img src="{{ URL::asset('/public/clientdashboard/images/downarrow-green.svg') }}"
                                                                    style="vertical-align:middle; margin: 0 3px;"
                                                                    alt="Up" width="8">
                                                            @else
                                                                <img src="{{ URL::asset('/public/clientdashboard/images/downarrow.svg') }}"
                                                                    style="vertical-align:middle; margin: 0 3px;"
                                                                    alt="Down" width="8">
                                                            @endif
                                                        </td>
                                                        <td>{{ $agendata->actionPlanPreviousCycle }}
                                                            |
                                                            {{ $agendata->actionPlanCurrentCycle }}
                                                            @if ($agendata->actionPlanCurrentCycle > $agendata->actionPlanPreviousCycle)
                                                                <img src="{{ URL::asset('/public/clientdashboard/images/downarrow-green.svg') }}"
                                                                    style="vertical-align:middle; margin: 0 3px;"
                                                                    alt="Up" width="8">
                                                            @else
                                                                <img src="{{ URL::asset('/public/clientdashboard/images/downarrow.svg') }}"
                                                                    style="vertical-align:middle; margin: 0 3px;"
                                                                    alt="Down" width="8">
                                                            @endif
                                                        </td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-3" id="audit_schedule">
            <div class="row px-md-1">
                <div class="col-md-12 px-md-2">
                    <div class="cardBox h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="m-0">
                                <span>Audit Schedule</span>
                                <i class="fa fa-info-circle text-primary ms-2" data-toggle="tooltip" data-placement="top"
                                    title="Audit Schedule showing audit allocations.">
                                </i>
                            </h6>
                        </div>
                        <div id="audit_schedule_wise_data">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-3">
            <div class="row px-md-1">
                <div class="col-12 px-md-2">
                    <div class="cardBox h-100 p-3">
                        <!-- Row 1: Title -->
                        <div class="row mb-3">
                            <div class="col-12 d-flex align-items-center">
                                <h6 class="m-0">
                                    <span>Action Planning</span>
                                    <i class="fa fa-info-circle text-primary ms-2" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Action Planning.">
                                    </i>
                                </h6>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="match_case2" class="form-label">Metrics Type</label>
                                    <select id="match_case2" class="form-select">
                                        <option value="3" selected>Action Planning</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="match_field2" class="form-label">Row</label>
                                    <select id="match_field2" class="form-select">
                                        <option value="4">Zone</option>
                                        <option value="5">State</option>
                                        <option value="9">List of Agencies</option>
                                    </select>
                                </div>

                                <div class="col-md-1" style="display:none">
                                    <label for="match_field_other2" class="form-label">Audit Type</label>
                                    <select id="match_field_other2" class="form-select">
                                        <option value="0">All</option>
                                        <option value="agency">Agency</option>
                                        <option value="branch">Branch</option>
                                        <option value="yard">Yard</option>
                                        <option value="agency_repo">Agency Repo</option>
                                        <option value="branch_repo">Branch Repo</option>
                                        <option value="yard_repo">Yard Repo</option>
                                    </select>
                                </div>

                                <div class="col-md-3 mt-5">
                                    <button type="button" onclick="getActionPlanningTabData();"
                                        class="btn btn-primary">Search</button>
                                </div>
                            </div>
                        </div>
                        <!-- Crosstab Result Section -->
                        <div id="actionPlanningTabResult" class="table-responsive">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-3">
            <div class="row px-md-1">
                <div class="col-md-6 px-md-2">
                    <div class="cardBox h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="m-0">
                                <span>Audit Status Distribution - Current Cycle</span>
                                <i class="fa fa-info-circle text-primary ms-2" data-toggle="tooltip" data-placement="top"
                                    title="Distribution of audits by status for current cycle">
                                </i>
                            </h6>
                        </div>

                        <div class="chart-container" style="position: relative; height:400px;width:400px;"
                            id="statusDistributionChart">

                        </div>
                    </div>
                </div>

                <div class="col-md-6 px-md-2">
                    <div class="cardBox h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="m-0">
                                <span>Audit Type Distribution - Current Cycle</span>
                                <i class="fa fa-info-circle text-primary ms-2" data-toggle="tooltip" data-placement="top"
                                    title="Distribution of audits by status for current cycle">
                                </i>
                            </h6>
                        </div>
                        <div class="chart-container" id="auditTypeDistributionChart"
                            style="position: relative; height:400px;width:400px;">

                        </div>
                    </div>
                </div>
            </div>
        </div>



        <div class="mb-3">
            <div class="row px-md-1">
                <div class="col-md-12 px-md-2">
                    <div class="cardBox h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="m-0">
                                <span>Product-wise View</span>
                                <i class="fa fa-info-circle text-primary ms-2" data-toggle="tooltip" data-placement="top"
                                    title="Bar chart showing audit product wise view.">
                                </i>
                            </h6>
                        </div>
                        <div id="product_wise_data"></div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Zone-wise Distribution Map -->

        <div class="mb-3">
            <div class="row px-md-1">
                <div class="col-md-12 px-md-2">
                    <div class="cardBox h-100" id="zone_wise_data">
                    </div>
                </div>
            </div>
        </div>



        <div class="mb-3">
            <div class="row px-md-1">
                <div class="col-md-12 px-md-2">
                    <div class="cardBox h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="m-0">
                                <span>Parameter Wise View</span>
                                <i class="fa fa-info-circle text-primary ms-2" data-toggle="tooltip" data-placement="top"
                                    title="Chart showing parameters.">
                                </i>
                            </h6>
                        </div>
                        <div id="param_wise_data"></div>
                    </div>
                </div>
            </div>
        </div>



        <!-- Action Planning (Audit Closures) -->
        @if (!empty($auditData['getActionPlanningData']))
            <div class="mb-3" id="action_planning_detail_view">
                <div class="row px-md-1">
                    <div class="col-md-12 px-md-2">
                        <div class="cardBox h-100">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="m-0">
                                    <span>Action Planning - Audit Closure Comparison</span>
                                    <i class="fa fa-info-circle text-primary ms-2" data-toggle="tooltip"
                                        data-placement="top"
                                        title="Comparison of audit closures between current and previous cycles">
                                    </i>
                                </h6>
                            </div>
                            <div class="chart-container" style="position: relative; height:300px; width:100%;"
                                id="actionPlanningChart">

                            </div>

                            <!-- Summary Table -->
                            <div class="mt-3">
                                <div class="table-responsive tbleDiv">
                                    <table class="table table-bordered align-middle text-center shadow-sm rounded"
                                        style="font-size: 16px;">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Closure Status</th>
                                                <th>Previous Cycle</th>
                                                <th>Current Cycle</th>
                                                <th>Change</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Sent for Closure</td>
                                                <td>{{ $auditData['getActionPlanningData']['previous_cycle']['sent_for_closure'] ?? 0 }}
                                                </td>
                                                <td>{{ $auditData['getActionPlanningData']['current_cycle']['sent_for_closure'] ?? 0 }}
                                                </td>
                                                <td>
                                                    @php
                                                        $current =
                                                            $auditData['getActionPlanningData']['current_cycle'][
                                                                'sent_for_closure'
                                                            ] ?? 0;
                                                        $previous =
                                                            $auditData['getActionPlanningData']['previous_cycle'][
                                                                'sent_for_closure'
                                                            ] ?? 0;
                                                        $change = $current - $previous;
                                                    @endphp
                                                    @if ($change > 0)
                                                        <span class="badge bg-success">+{{ $change }}</span>
                                                    @elseif($change < 0)
                                                        <span class="badge bg-danger">{{ $change }}</span>
                                                    @else
                                                        <span class="badge bg-secondary">0</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Approved</td>
                                                <td>{{ $auditData['getActionPlanningData']['previous_cycle']['approved'] ?? 0 }}
                                                </td>
                                                <td>{{ $auditData['getActionPlanningData']['current_cycle']['approved'] ?? 0 }}
                                                </td>

                                                <td>
                                                    @php
                                                        $current =
                                                            $auditData['getActionPlanningData']['current_cycle'][
                                                                'approved'
                                                            ] ?? 0;
                                                        $previous =
                                                            $auditData['getActionPlanningData']['previous_cycle'][
                                                                'approved'
                                                            ] ?? 0;
                                                        $change = $current - $previous;
                                                    @endphp
                                                    @if ($change > 0)
                                                        <span class="badge bg-success">+{{ $change }}</span>
                                                    @elseif($change < 0)
                                                        <span class="badge bg-danger">{{ $change }}</span>
                                                    @else
                                                        <span class="badge bg-secondary">0</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Rejected</td>
                                                <td>{{ $auditData['getActionPlanningData']['previous_cycle']['rejected'] ?? 0 }}
                                                </td>
                                                <td>{{ $auditData['getActionPlanningData']['current_cycle']['rejected'] ?? 0 }}
                                                </td>

                                                <td>
                                                    @php
                                                        $current =
                                                            $auditData['getActionPlanningData']['current_cycle'][
                                                                'rejected'
                                                            ] ?? 0;
                                                        $previous =
                                                            $auditData['getActionPlanningData']['previous_cycle'][
                                                                'rejected'
                                                            ] ?? 0;
                                                        $change = $current - $previous;
                                                    @endphp
                                                    @if ($change > 0)
                                                        <span class="badge bg-danger">+{{ $change }}</span>
                                                    @elseif($change < 0)
                                                        <span class="badge bg-success">{{ $change }}</span>
                                                    @else
                                                        <span class="badge bg-secondary">0</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="mb-3">
            <div class="row px-md-1">
                <div class="col-md-6 px-md-2">
                    <div class="cardBox h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="m-0">
                                <span>Top Performers (States & Cities)</span>
                                <i class="fa fa-info-circle text-primary ms-2" data-toggle="tooltip" data-placement="top"
                                    title="Chart showing Top Performers (States & Cities).">
                                </i>
                            </h6>
                        </div>
                        <div id="state_wise_data_top"></div>
                    </div>
                </div>
                <div class="col-md-6 px-md-2">
                    <div class="cardBox h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="m-0">
                                <span>Bottom Performers (States & Cities)</span>
                                <i class="fa fa-info-circle text-primary ms-2" data-toggle="tooltip" data-placement="top"
                                    title="Chart showing Bottom Performers (States & Cities).">
                                </i>
                            </h6>
                        </div>
                        <div id="state_wise_data_bot"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <div class="row px-md-1">
                <div class="col-md-6 px-md-2">
                    <div class="cardBox h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="m-0">
                                <span>Top Performers<?php if ($audit_type != 'all') {
                                    echo '(' . $audit_type . ')';
                                } else {
                                    echo '(Audits)';
                                } ?></span>
                                <i class="fa fa-info-circle text-primary ms-2" data-toggle="tooltip" data-placement="top"
                                    title="Chart showing Top Performers.">
                                </i>
                            </h6>
                        </div>
                        <div id="agency_wise_data_top"></div>
                    </div>
                </div>
                <div class="col-md-6 px-md-2">
                    <div class="cardBox h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="m-0">
                                <span>Bottom Performers<?php if ($audit_type != 'all') {
                                    echo '(' . $audit_type . ')';
                                } else {
                                    echo '(Audits)';
                                } ?></span>
                                <i class="fa fa-info-circle text-primary ms-2" data-toggle="tooltip" data-placement="top"
                                    title="Chart showing Bottom Performers.">
                                </i>
                            </h6>
                        </div>
                        <div id="agency_wise_data_bot"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- State Pareto -->
        <div class="mb-3">
            <div class="row px-md-1">
                <div class="col-md-12 px-md-2">
                    <div class="cardBox h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="m-0">
                                <span>Pareto State Wise View</span>
                                <i class="fa fa-info-circle text-primary ms-2" data-toggle="tooltip" data-placement="top"
                                    title="Chart showing states.">
                                </i>
                            </h6>
                        </div>
                        <div id="pareto_state_wise_data"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-3" id="param_geographical_view">
            <div class="mb-3">
                <div class="row px-md-1" id="param_compliance_data">
                </div>
            </div>
        </div>

        <div class="mb-3">
            <div class="row px-md-1">
                <div class="col-12 px-md-2">
                    <div class="cardBox h-100 p-3">
                        <!-- Row 1: Title -->
                        <div class="row mb-3">
                            <div class="col-12 d-flex align-items-center">
                                <h6 class="m-0">
                                    <span>Cross Tab</span>
                                    <i class="fa fa-info-circle text-primary ms-2" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Cross Tab.">
                                    </i>
                                </h6>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="match_case" class="form-label">Metrics Type</label>
                                    <select id="match_case" class="form-select">
                                        <option value="1">Audit Score & Count</option>
                                        <option value="2">Repeat Issues</option>
                                        <option value="3">Action Planning</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="match_field" class="form-label">Row</label>
                                    <select id="match_field" class="form-select">
                                        <option value="1">Audit Cycle</option>
                                        <option value="2">Audit Type</option>
                                        <option value="3">Products</option>
                                        <option value="4">Zone</option>
                                        <option value="5">State</option>
                                        <option value="6">City</option>
                                        <option value="7">Parameters</option>
                                        <option value="8">Sub Parameters</option>
                                        <option value="9">List of Agencies/Branch/Yard/Repos</option>
                                        <option value="10">Regulatory Parameter</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="match_field_other" class="form-label">Audit Type</label>
                                    <select id="match_field_other" class="form-select">
                                        <option value="0">All</option>
                                        <option value="agency">Agency</option>
                                        <option value="branch">Branch</option>
                                        <option value="yard">Yard</option>
                                        <option value="agency_repo">Agency Repo</option>
                                        <option value="branch_repo">Branch Repo</option>
                                        <option value="yard_repo">Yard Repo</option>
                                    </select>
                                </div>

                                <div class="col-md-12" style="text-align:center;">
                                    <button type="button" onclick="getCrossTabData();"
                                        class="btn btn-primary">Search</button>
                                </div>
                            </div>
                        </div>
                        <!-- Crosstab Result Section -->
                        <div id="crosstabResult" class="table-responsive">
                        </div>
                    </div>
                </div>
            </div>
        </div>




        <form action="{{ route('switch.user') }}" method="POST" id="switchUserForm">
            @csrf
            <input type="hidden" name="email" id="switchEmail">
        </form>

    @endsection

    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/drilldown.js"></script>
    <script src="https://code.highcharts.com/modules/heatmap.js"></script>
    <script src="https://code.highcharts.com/highcharts-more.js"></script>
    <script src="https://code.highcharts.com/themes/adaptive.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/proj4js/2.8.0/proj4.js"></script>
    <script src="https://code.highcharts.com/maps/modules/map.js"></script>
    <script src="https://code.highcharts.com/maps/highmaps.js"></script>
    <script src="https://code.highcharts.com/maps/modules/marker-clusters.js"></script>
    <script src="https://code.highcharts.com/mapdata/countries/in/custom/in-all-disputed.js"></script>
    <script src="https://code.highcharts.com/modules/pareto.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>


    <script type="text/javascript">
        jQuery(document).ready(function() {
            jQuery('.datepicker').datepicker({
                dateFormat: "yy-mm-dd",
                onSelect: function() {
                    jQuery(this).datepicker('hide');
                }
            });

            new Choices('#match_case', {
                searchEnabled: true,
                shouldSort: false
            });
            new Choices('#match_field', {
                searchEnabled: true,
                shouldSort: false
            });
            new Choices('#match_field_other', {
                searchEnabled: true,
                shouldSort: false
            });
            new Choices('#match_case2', {
                searchEnabled: true,
                shouldSort: false
            });
            new Choices('#match_field2', {
                searchEnabled: true,
                shouldSort: false
            });
            new Choices('#match_field_other2', {
                searchEnabled: true,
                shouldSort: false
            });

            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip();

            @if (!empty($auditData['getActionPlanningData']))
                const actionPlanningData = @json($auditData['getActionPlanningData']);
                Highcharts.chart('actionPlanningChart', {
                    chart: {
                        zoomType: 'xy',
                        backgroundColor: '#f9f9f9',
                        style: {
                            fontFamily: 'Segoe UI, Roboto, sans-serif'
                        }
                    },
                    title: {
                        text: 'Action Planning - Audit Closure Comparison',
                        style: {
                            fontSize: '14px',
                            fontWeight: 'bold'
                        }
                    },
                    xAxis: {
                        categories: ['Sent for Closure', 'Approved', 'Rejected'],
                        crosshair: true,
                        labels: {
                            style: {
                                fontSize: '12px'
                            }
                        }
                    },
                    yAxis: [{
                        title: {
                            text: 'Count',
                            style: {
                                color: '#3a33ffff',
                                fontWeight: 'bold'
                            }
                        },
                        labels: {
                            format: '{value}',
                            style: {
                                color: '#3a33ffff'
                            }
                        },
                        max: 100,
                        min: 0,
                        endOnTick: false,
                        tickAmount: 6
                    }],
                    legend: {
                        layout: 'horizontal',
                        align: 'center',
                        verticalAlign: 'bottom',
                        itemStyle: {
                            fontWeight: 'normal',
                            fontSize: '12px'
                        }
                    },
                    tooltip: {
                        shared: true,
                        backgroundColor: '#ffffff',
                        borderColor: '#ccc',
                        style: {
                            fontSize: '12px'
                        }
                    },
                    plotOptions: {
                        column: {
                            colorByPoint: false,
                            borderRadius: 5,
                            dataLabels: {
                                enabled: true,
                                format: '{y}',
                                style: {
                                    fontSize: '12px'
                                }
                            }
                        }
                    },
                    series: [{
                        name: 'Previous Cycle',
                        type: 'column',
                        yAxis: 0,
                        data: [
                            actionPlanningData.previous_cycle.sent_for_closure || 0,
                            actionPlanningData.previous_cycle.approved || 0,
                            actionPlanningData.previous_cycle.rejected || 0
                        ],
                        color: '#ff9633ff'
                    }, {
                        name: 'Current Cycle',
                        type: 'column',
                        yAxis: 0,
                        data: [
                            actionPlanningData.current_cycle.sent_for_closure || 0,
                            actionPlanningData.current_cycle.approved || 0,
                            actionPlanningData.current_cycle.rejected || 0
                        ],
                        color: '#2ab70eff'
                    }]
                });
            @endif

            document.getElementById('action_planning_count').addEventListener('click', function() {
                document.getElementById('action_planning_detail_view').scrollIntoView({
                    behavior: 'smooth', // smooth scroll
                    block: 'start' // align to top
                });
            });

            ['audit_submitted', 'audit_allocation'].forEach(function(id) {
                document.getElementById(id).addEventListener('click', function() {
                    document.getElementById('audit_schedule').scrollIntoView({
                        behavior: 'smooth', // smooth scroll
                        block: 'start' // align to top
                    });
                });
            });

            // Status Distribution Chart
            @if (!empty($auditData['status_distribution']))

                Highcharts.chart('statusDistributionChart', {
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
                                            'Total<br/>' +
                                            '<strong>' + @php echo array_sum($auditData['status_distribution']); @endphp + '</strong>'
                                        )
                                        .css({
                                            color: 'var(--highcharts-neutral-color-100, #000)',
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
                                    fontSize: `${series.center[2] / 12}px`
                                });
                            }
                        }
                    },
                    accessibility: {
                        point: {
                            valueSuffix: ''
                        }
                    },
                    title: {
                        text: 'Audit Status Distribution - Current Cycle',
                        style: {
                            fontSize: '14px',
                            fontWeight: 'bold'
                        }
                    },
                    subtitle: {
                        text: ''
                    },
                    tooltip: {
                        pointFormat: '{series.name}: <b>{point.y}</b>'
                    },
                    legend: {
                        enabled: false
                    },
                    plotOptions: {
                        series: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            borderRadius: 8,
                            dataLabels: [{
                                enabled: true,
                                distance: 20,
                                format: '{point.name}'
                            }, {
                                enabled: true,
                                distance: -15,
                                format: '{point.y}',
                                style: {
                                    fontSize: '0.9em'
                                }
                            }],
                            showInLegend: true
                        }
                    },
                    series: [{
                        name: 'Status Distribution',
                        colorByPoint: true,
                        innerSize: '60%',
                        data: [{
                                name: 'Submitted',
                                y: @php echo $auditData['status_distribution']['Submitted'] ?? 0;@endphp
                            }, {
                                name: 'Submitted via OTP approval',
                                y: @php echo $auditData['status_distribution']['Submitted_via_OTP_approval'] ?? 0;@endphp
                            }, {
                                name: 'QC Approved',
                                y: @php echo $auditData['status_distribution']['QC_Approved'] ?? 0;@endphp
                            }, {
                                name: 'Rejected',
                                y: @php echo $auditData['status_distribution']['Rejected'] ?? 0;@endphp
                            },
                            {
                                name: 'Pending',
                                y: @php echo $auditData['status_distribution']['Pending'] ?? 0;@endphp
                            },
                            {
                                name: 'Saved',
                                y: @php echo $auditData['status_distribution']['Saved'] ?? 0;@endphp
                            }
                        ]
                    }]
                });
            @endif

            const colors = [
                ['#FF9933', '#FFD580'], // Orange gradient for Branch
                ['#4CAF50', '#A5D6A7'], // Green gradient for Branch Repo
                ['#2196F3', '#90CAF9'], // Blue gradient for Agency
                ['#9C27B0', '#E1BEE7'], // Purple gradient for Agency Repo
                ['#F44336', '#FFCDD2'], // Red gradient for Yard
                ['#e1af17ff', '#FFE082'] // Yellow gradient for Yard Repo
            ];

            // Create gradient definitions
            const gradients = colors.map((color, i) => ({
                radialGradient: {
                    cx: 0.5,
                    cy: 0.5,
                    r: 0.5
                },
                stops: [
                    [0, color[0]],
                    [1, color[1]]
                ]
            }));

            Highcharts.chart('auditTypeDistributionChart', {
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
                                        'Total<br/>' +
                                        '<strong>' + @php echo array_sum(array_column($auditData['audit_type_distribution'], 'count')); @endphp + '</strong>'
                                    )
                                    .css({
                                        color: 'var(--highcharts-neutral-color-100, #000)',
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
                            customLabel.css({
                                fontSize: `${series.center[2] / 12}px`
                            });
                        }
                    }
                },
                title: {
                    text: 'Audit Type Distribution',
                    style: {
                        fontSize: '14px',
                        fontWeight: 'bold', // Optional
                        // color: '#333333',   // Optional
                        // fontFamily: 'Arial, sans-serif' // Optional
                    }
                },
                plotOptions: {
                    series: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        borderRadius: 8,
                        dataLabels: [{
                            enabled: true,
                            distance: 20,
                            format: '{point.name}'
                        }, {
                            enabled: true,
                            distance: -15,
                            format: '{point.y}',
                            style: {
                                fontSize: '0.9em'
                            }
                        }],
                        showInLegend: true
                    }
                },
                series: [{
                    name: 'Average Score Percentage',
                    colorByPoint: true,
                    innerSize: '60%',
                    data: [{
                        name: 'Branch',
                        y: @php echo $auditData['audit_type_distribution']['branch']['score_percentage'] ?? 0;@endphp
                    }, {
                        name: 'Branch Repo',
                        y: @php echo $auditData['audit_type_distribution']['branch_repo']['score_percentage'] ?? 0;@endphp
                    }, {
                        name: 'Agency',
                        y: @php echo $auditData['audit_type_distribution']['agency']['score_percentage'] ?? 0;@endphp
                    }, {
                        name: 'Agency Repo',
                        y: @php echo $auditData['audit_type_distribution']['agency_repo']['score_percentage'] ?? 0;@endphp
                    }, {
                        name: 'Yard',
                        y: @php echo $auditData['audit_type_distribution']['yard']['score_percentage'] ?? 0;@endphp
                    }, {
                        name: 'Yard Repo',
                        y: @php echo $auditData['audit_type_distribution']['yard_repo']['score_percentage'] ?? 0;@endphp
                    }]
                }]
            });
        });
    </script>


    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "<?php echo csrf_token(); ?>"
            }
        });

        $.ajax({
            type: 'POST',
            url: "{{ route('getproductdata') }}",
            data: {
                'currentCycleId': <?php echo $currentCycleId; ?>,
                'audit_type': "<?php echo $audit_type; ?>"
            },
            success: function(response) {
                $("#product_wise_data").html(response);
            },
            error: function(xhr) {
                console.log("error in showing product wise chart");
            }
        });

        $.ajax({
            type: 'POST',
            url: "{{ route('getzonedata') }}",
            data: {
                'currentCycleId': <?php echo $currentCycleId; ?>,
                'audit_type': "<?php echo $audit_type; ?>"
            },
            success: function(response) {
                $("#zone_wise_data").html(response);
            },
            error: function(xhr) {
                console.log("error in showing zone wise chart");
            }
        });

        $.ajax({
            type: 'POST',
            url: "{{ route('audit_schedule') }}",
            data: {
                'currentCycleId': <?php echo $currentCycleId; ?>,
                'audit_type': "<?php echo $audit_type; ?>"
            },
            success: function(response) {
                $("#audit_schedule_wise_data").html(response);
            },
            error: function(xhr) {
                console.log("error in showing audit_schedule");
            }
        });

        $.ajax({
            type: 'POST',
            url: "{{ route('state_wise_data_top') }}",
            data: {
                'currentCycleId': <?php echo $currentCycleId; ?>,
                'audit_type': "<?php echo $audit_type; ?>"
            },
            success: function(response) {
                $("#state_wise_data_top").html(response);
            },
            error: function(xhr) {
                console.log("error in showing state_wise_data_top");
            }
        });

        $.ajax({
            type: 'POST',
            url: "{{ route('state_wise_data_bot') }}",
            data: {
                'currentCycleId': <?php echo $currentCycleId; ?>,
                'audit_type': "<?php echo $audit_type; ?>"
            },
            success: function(response) {
                $("#state_wise_data_bot").html(response);
            },
            error: function(xhr) {
                console.log("error in showing state_wise_data_bottom");
            }
        });

        $.ajax({
            type: 'POST',
            url: "{{ route('agency_wise_data_top') }}",
            data: {
                'currentCycleId': <?php echo $currentCycleId; ?>,
                'audit_type': "<?php echo $audit_type; ?>"
            },
            success: function(response) {
                $("#agency_wise_data_top").html(response);
            },
            error: function(xhr) {
                console.log("error in showing agency_wise_data_top");
            }
        });

        $.ajax({
            type: 'POST',
            url: "{{ route('agency_wise_data_bot') }}",
            data: {
                'currentCycleId': <?php echo $currentCycleId; ?>,
                'audit_type': "<?php echo $audit_type; ?>"
            },
            success: function(response) {
                $("#agency_wise_data_bot").html(response);
            },
            error: function(xhr) {
                console.log("error in showing agency_wise_data_bottom");
            }
        });


        $.ajax({
            type: 'POST',
            url: "{{ route('param_wise_data') }}",
            data: {
                'currentCycleId': <?php echo $currentCycleId; ?>,
                'audit_type': "<?php echo $audit_type; ?>"
            },
            success: function(response) {
                $("#param_wise_data").html(response);
            },
            error: function(xhr) {
                console.log("error in showing param_wise_data");
            }
        });

        $.ajax({
            type: 'POST',
            url: "{{ route('param_compliance_data') }}",
            data: {
                'currentCycleId': <?php echo $currentCycleId; ?>,
                'audit_type': "<?php echo $audit_type; ?>"
            },
            success: function(response) {
                $("#param_compliance_data").html(response);
            },
            error: function(xhr) {
                console.log("error in showing param_compliance_data");
            }
        });

        $.ajax({
            type: 'POST',
            url: "{{ route('pareto_state_wise') }}",
            data: {
                'currentCycleId': <?php echo $currentCycleId; ?>,
                'audit_type': "<?php echo $audit_type; ?>"
            },
            success: function(response) {
                $("#pareto_state_wise_data").html(response);
            },
            error: function(xhr) {
                console.log("error in showing pareto_state_wise");
            }
        });

        function switchUser(val) {
            var email = "";
            if (val == 1) {
                email = "qd@mailinator.com";
            }
            if (val == 2) {
                email = "auditorqd@mailinator.com";
            }
            if (val == 3) {
                email = "qdegrees1@mailinator.com";
            }
            if (val == 4) {
                email = "raghav@mailinator.com";
            }
            $("#switchEmail").val(email);
            $("#switchUserForm").submit();
        }

        function getCrossTabData() {
            $.ajax({
                type: 'POST',
                url: "{{ route('getCrossTabData') }}",
                data: {
                    'currentCycleId': <?php echo $currentCycleId; ?>,
                    'audit_type': "<?php echo $audit_type; ?>",
                    'match_field_other': $("#match_field_other").val(),
                    'match_case': $("#match_case").val(),
                    'match_field': $("#match_field").val()

                },
                success: function(response) {
                    $("#crosstabResult").html(response);
                },
                error: function(xhr) {
                    console.log("error in showing crosstabResult");
                }
            });
        }

        function getActionPlanningTabData() {
            $.ajax({
                type: 'POST',
                url: "{{ route('getCrossTabData') }}",
                data: {
                    'currentCycleId': <?php echo $currentCycleId; ?>,
                    'audit_type': "<?php echo $audit_type; ?>",
                    'match_field_other': $("#match_field_other2").val(),
                    'match_case': $("#match_case2").val(),
                    'match_field': $("#match_field2").val()

                },
                success: function(response) {
                    $("#actionPlanningTabResult").html(response);
                },
                error: function(xhr) {
                    console.log("error in showing actionPlanningTabResult");
                }
            });
        }
    </script>
