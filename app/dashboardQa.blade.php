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
    @if (auth()->check() && auth()->user()->client_id == 13)
        <div class="d-flex justify-content-end mb-3">
            <form action="{{ route('switch.user') }}" method="POST" style="margin-right: 10px;">
                @csrf
                <input type="hidden" name="email" value="demo@mailinator.com">
                <button type="submit" class="btn btn-primary">Client</button>
            </form>

            <form action="{{ route('switch.user') }}" method="POST">
                @csrf
                <input type="hidden" name="email" value="qd@mailinator.com">
                <button type="submit" class="btn btn-danger">Audit Agency</button>
            </form>
        </div>
    @endif

    @if (auth()->check() && auth()->user()->client_id == 2)
        <div class="d-flex justify-content-end mb-3">
            <form action="{{ route('switch.user') }}" method="POST" style="margin-right: 10px;">
                @csrf
                <input type="hidden" name="email" value="TATA@mailinator.com">
                <button type="submit" class="btn btn-primary">Client</button>
            </form>

            <form action="{{ route('switch.user') }}" method="POST">
                @csrf
                <input type="hidden" name="email" value="qdegrees1@mailinator.com">
                <button type="submit" class="btn btn-danger">Audit Agency</button>
            </form>
        </div>
    @endif

    <!-- Content -->
    <div class="text-right">
        @if (session()->has('master_qa_id'))
            <a href="{{ route('back.masterqa') }}" class="btn btn-primary ">
                ← Back to Client List
            </a>
        @endif
    </div>
    <input type="hidden" name="url" id="url" value={{ url('/') }}>

    <input type="hidden" name="token" id="token" value={{ @csrf_token() }}>

    <div class="content" style="font-size: 13px !important;">

        <div class="row">
            <div class="col-9">
                <h3 class="heading text-black ml-2">Dashboard</h3>
            </div>
            <div class="col-3">
                <div class="filter-container float-right">
                    <label for="time-filter">
                        <img src="{{ URL::asset('public/images/filter-icon.svg') }}" width="30px;"
                            style="margin-top:-5px;">
                    </label>
                    <select class="form-select gradeSelect" id="time-filter">
                        <option value="current" {{ request('start_date') ? '' : 'selected' }}>Current Month</option>
                        <option value="custom" {{ request('start_date') ? 'selected' : '' }}>Custom Date</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- V Filter Date Range Start ---------->
        <div class="row align-items-center justify-content-center text-center mb-3 mt-3">
            <!-- Custom Date Range Inputs -->
            <form method="GET" action="{{ route('dashboard') }}" class="align-items-center d-flex justify-content-end"
                id="filter-form">
                <div id="custom-date-range" style="display: {{ request('start_date') ? 'block' : 'none' }};">
                    <div class="d-flex align-items-center">
                        <label for="start-date" class="mr-2 vdate">Start Date:</label>
                        <input type="text" id="start-date" name="start_date" value="{{ request('start_date') }}"
                            class="form-control mr-3 flatpickr" style="max-width: 200px;">

                        <label for="end-date" class="mr-2 vdate">End Date:</label>
                        <input type="text" id="end-date" name="end_date" value="{{ request('end_date') }}"
                            class="form-control flatpickr" style="max-width: 200px;">
                    </div>
                </div>

                <button class="btn btn-primary ml-2" type="submit" id="filter-button"
                    style="display: {{ request('start_date') ? 'inline-block' : 'none' }};">Filter
                </button>

                @if (request('start_date') || request('end_date'))
                    <a href="{{ route('dashboard') }}" id="clear-link" class="btn btn-primary">Remove</a>
                @endif
            </form>
        </div>
        <!-- V Filter Date Range End ---------->

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
                                <a href="{{ route('auditor_assign_cases.index') }}">
                                    <div class="card-body">
                                        <div class="stat-widget-five text-center">
                                            <div class="text-center dib">
                                                <div class="d-flex justify-content-center align-items-center mb-2">
                                                    <strong class="mr-2"style="color:black !important;">Total Assign
                                                        Audits</strong>
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
                                </a>

                            </div>
                        </div>

                        <!-- Total Submitted Audits -->
                        <div class="col-lg-3 col-md-3">
                            <div class="card cardboxInner h-100" style="background-color: #f2d6d1;">

                                <a href="{{ route('submit_audited_list') }}">
                                    <div class="card-body">
                                        <div class="stat-widget-five text-center">
                                            <div class="text-center dib">
                                                <div class="d-flex justify-content-center align-items-center mb-2">
                                                    <strong class="mr-2"style="color:black !important;">Total Submitted
                                                        Audits</strong>
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
                                </a>
                            </div>
                        </div>

                        <!-- Total Pending -->
                        <div class="col-lg-3 col-md-3">
                            <div class="card cardboxInner score h-100" style="background-color: #F9D179;">
                                <a href="{{ route('auditor_list') }}">
                                    <div class="card-body">
                                        <div class="stat-widget-five text-center">
                                            <div class="text-center dib">
                                                <div class="d-flex justify-content-center align-items-center mb-2">
                                                    <strong class="mr-2"style="color:black !important;">Total
                                                        Pending</strong>
                                                    <i class="fa fa-info-circle text-primary" data-toggle="tooltip"
                                                        data-placement="top"
                                                        title="Audits that are assigned but not yet submitted.">
                                                    </i>
                                                </div>
                                                <div class="stat-text">
                                                    <span class="count2">
                                                        {{ isset($totalPending) ? $totalPending : 0 }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- Total Saved Audits -->
                        <div class="col-lg-3 col-md-3">
                            <a href="{{ route('save_audited_list') }}">
                                <div class="card cardboxInner score h-100">
                                    <div class="card-body">
                                        <div class="stat-widget-five text-center">
                                            <div class="text-center dib">
                                                <div class="d-flex justify-content-center align-items-center mb-2">
                                                    <strong class="mr-2"style="color:black !important;">Total Saved
                                                        Audits</strong>
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
                            </a>
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
        const startDatePicker = flatpickr("#start-date", {
            dateFormat: "Y-m-d",
            onChange: function(selectedDates, dateStr, instance) {
                endDatePicker.set('minDate', dateStr);
            },
            onDayCreate: highlightSundays
        });

        // Initialize End Date Picker
        const endDatePicker = flatpickr("#end-date", {
            dateFormat: "Y-m-d",
            onDayCreate: highlightSundays
        });
    </script>
@endsection




@section('js')
    <script>
        jQuery(document).on('ready', function() {

            jQuery(window).on('load', function() {

                jQuery('.perSign').remove()

            })

        })
    </script>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

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
        document.addEventListener("DOMContentLoaded", function() {
            const filterDropdown = document.getElementById("time-filter");
            const customDateContainer = document.getElementById("custom-date-range");
            const filterButton = document.getElementById("filter-button");
            const startDateInput = document.getElementById("start-date");
            const endDateInput = document.getElementById("end-date");

            toggleCustomFilter(filterDropdown.value);

            filterDropdown.addEventListener("change", function() {
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
        document.addEventListener("DOMContentLoaded", function() {
            const filterDropdown = document.getElementById("time-filter");
            const clearLink = document.getElementById("clear-link");

            // Handle dropdown change
            filterDropdown.addEventListener("change", function() {
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

    <script>
        $(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
    <!--V Current Month Filter Refresh End ---------------------------------->
@endsection
