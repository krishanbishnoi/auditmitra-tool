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
@endsection
@section('content')
{{-- <select class="form-control form-control-sm me-3 h-100" name="audit_cycle_id"
                            style="width:auto; min-width:120px;">
                            @foreach ($legalCycle as $ac)
                                <option value="{{ $ac->id }}" >
                                    {{ $ac->name }}
                                </option>
                            @endforeach
                            <option value="0">Go Back</option>
                        </select> --}}
 <div class="animated fadeIn">
            <div class="card">

                <div class="card-header">

                    <form action="{{ route('dashboard') }}" method="post">

                        @csrf

                        <div class="row">

                            <div class="col-md-3">
                                <h4>Overall Audit Details</h4>
                            </div>


                        </div>

                    </form>

                </div>

                <div class="card-body">

                    <div class="row">

                        <div class="col-lg-3 col-md-3">
                            <div class="card cardboxInner h-100">
                                <div class="card-body">
                                    <div class="stat-widget-five text-center">
                                        <div class="text-center dib">
                                            <div class="d-flex justify-content-center align-items-center mb-2">
                                                <strong class="mr-2">Total Assign Audits</strong>
                                                <i class="fa fa-info-circle text-primary" data-toggle="tooltip"
                                                    data-placement="top"
                                                    title="Number of audits that have been assigned for execution.">
                                                </i>
                                            </div>
                                            <div class="stat-text">
                                                <span class="count2">
                                                    {{ $totalAssign ?? 0 }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Submitted Audits -->
                        <div class="col-lg-3 col-md-3">
                            <div class="card cardboxInner h-100" style="background-color: #f2d6d1;">
                                <div class="card-body">
                                    <div class="stat-widget-five text-center">
                                        <div class="text-center dib">
                                            <div class="d-flex justify-content-center align-items-center mb-2">
                                                <strong class="mr-2">Total Submitted Audits</strong>
                                                <i class="fa fa-info-circle text-primary" data-toggle="tooltip"
                                                    data-placement="top"
                                                    title="Audits that have been completed and submitted by auditors.">
                                                </i>
                                            </div>
                                            <div class="stat-text">
                                                <span class="count2">
                                                    {{ isset($totalCompletedAudits) ? $totalCompletedAudits : 0 }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Pending -->
                        <div class="col-lg-3 col-md-3">
                            <div class="card cardboxInner score h-100" style="background-color: #F9D179;">
                                <div class="card-body">
                                    <div class="stat-widget-five text-center">
                                        <div class="text-center dib">
                                            <div class="d-flex justify-content-center align-items-center mb-2">
                                                <strong class="mr-2">Total Pending</strong>
                                                <i class="fa fa-info-circle text-primary" data-toggle="tooltip"
                                                    data-placement="top"
                                                    title="Audits that are assigned but not yet submitted.">
                                                </i>
                                            </div>
                                            <div class="stat-text">
                                                <span class="count2">
                                                    {{ $totalAssign - $totalCompletedAudits }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Saved Audits -->
                        <div class="col-lg-3 col-md-3">
                            <div class="card cardboxInner score h-100">
                                <div class="card-body">
                                    <div class="stat-widget-five text-center">
                                        <div class="text-center dib">
                                            <div class="d-flex justify-content-center align-items-center mb-2">
                                                <strong class="mr-2">Total Saved Audits</strong>
                                                <i class="fa fa-info-circle text-primary" data-toggle="tooltip"
                                                    data-placement="top"
                                                    title="Audits saved as draft but not submitted yet.">
                                                </i>
                                            </div>
                                            <div class="stat-text">
                                                <span class="count2">
                                                    {{ isset($totalSavedAudits) ? $totalSavedAudits : 0 }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <!-- <div class="col-lg-3 col-md-6">

                            <div class="card">

                                <div class="card-body">

                                    <div class="stat-widget-five text-center">

                                        <div class="text-center dib">

                                            <div class="stat-heading">Audit Submited - QC Pass</div>

                                            <div class="stat-text"><span class="count2">{{ $totalpass ?? 0 }}</span></div>

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

                                            <div class="stat-text"><span class="count2">{{ $totalfaild ?? 0 }}</span></div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div> -->

                    </div>

                </div>

            </div>

        </div>


@endsection
