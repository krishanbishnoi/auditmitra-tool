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

<div class="animated fadeIn">

    {{-- Page Header --}}
    <div class="row align-items-center mb-3">
        <div class="col-md-6">
            <h3 class="heading text-black ml-3 mb-0">Dashboard</h3>
        </div>

        <div class="col-md-3 ml-auto">
            <form method="GET" action="{{ route('legal.dashboard') }}">
                <select name="cycle_id"
                        class="form-control form-control-sm"
                        onchange="this.form.submit()">
                    @foreach ($legalCycle as $cycle)
                        <option value="{{ $cycle->id }}"
                            {{ $selectedCycleId == $cycle->id ? 'selected' : '' }}>
                            {{ $cycle->name }}
                            {{ $cycle->status == 1 ? '(Active)' : '' }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="card">
        <div class="card-header">
            <h4 class="mb-0">Overall Audit Details</h4>
        </div>

        <div class="card-body">
            <div class="row">

                {{-- Total Assign --}}
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card cardboxInner h-100">
                        <div class="card-body">
                            <div class="stat-widget-five text-center">
                                <strong>Total Assign Audits</strong>
                                <div class="stat-text mt-2">
                                    <span class="count2">{{ $totalAssign ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Total Submitted --}}
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card cardboxInner h-100" style="background-color: #f2d6d1;">
                        <div class="card-body">
                            <div class="stat-widget-five text-center">
                                <strong>Total Submitted Audits</strong>
                                <div class="stat-text mt-2">
                                    <span class="count2">{{ $totalCompletedAudits ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Total Pending --}}
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card cardboxInner h-100" style="background-color: #F9D179;">
                        <div class="card-body">
                            <div class="stat-widget-five text-center">
                                <strong>Total Pending</strong>
                                <div class="stat-text mt-2">
                                    <span class="count2">
                                        {{ max(($totalAssign ?? 0) - ($totalCompletedAudits ?? 0), 0) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Total Saved --}}
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card cardboxInner h-100">
                        <div class="card-body">
                            <div class="stat-widget-five text-center">
                                <strong>Total Saved Audits</strong>
                                <div class="stat-text mt-2">
                                    <span class="count2">{{ $totalSavedAudits ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

@endsection

