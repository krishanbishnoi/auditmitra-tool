@extends('layouts.master')
@section('title', '| Users')

@section('content')

<link rel="stylesheet" href="{{ URL::asset('/public/clientdashboard/css/owl.carousel.min.css') }}">
<link rel="stylesheet" href="{{ URL::asset('/public/clientdashboard/css/style.css') }}">

<div class="row">
	<div class="col-lg-12" style="margin-top:10x">

	</div>
</div>

<div class="animated fadeIn">
	<div class="row">
		<div class="col-lg-12">
			
			<div class="card">
				<div class="card-header">
				<div class="row">	
					<div class="col-11">
					<h4 class="card-title">Collection Agency Trend List</h4>
					</div>
					<div class="col-1">
						<a href="javascript:history.back()" class="">
						    <i class="fa fa-arrow-circle-left" style="font-size: 34px;"></i>
						</a>
					</div>
					<!-- <div class="col-3">
		               <div class="filter-container float-right">
		                    <label for="time-filter">
		                        <img src="{{ asset('/public/images/filter-icon.svg') }}" width="30px;" style="margin-top:-5px;">
		                    </label>
		                    <select class="form-select gradeSelect" id="time-filter">
		                        <option value="current" {{ request('start_date') ? '' : 'selected' }}>Current Month</option>
		                        <option value="custom" {{ request('start_date') ? 'selected' : '' }}>Custom Date</option>
		                    </select>
		                </div>
		            </div>  -->
				</div>
					<!-- V Filter Date Range Start ---------->
				    <div class="row align-items-center justify-content-center text-center mb-1"> 
				            <!-- Custom Date Range Inputs -->
				           <form method="GET" action="{{ route('collectiongencytrend') }}" class="align-items-center d-flex justify-content-end" id="filter-form">
				                <div id="custom-date-range" style="display: {{ request('start_date') ? 'block' : 'none' }};">
				                    <div class="d-flex align-items-center">
				                        <label for="start-date" class="mr-2 vdate">Start Date:</label>
				                        <input type="date" id="start-date" name="start_date" value="{{ request('start_date') }}" class="form-control mr-3" style="max-width: 200px;">

				                        <label for="end-date" class="mr-2 vdate">End Date:</label>
				                        <input type="date" id="end-date" name="end_date" value="{{ request('end_date') }}" class="form-control" style="max-width: 200px;">
				                    </div>
				                </div>
				                <button class="btn btn-primary ml-2" type="submit" id="filter-button" style="display: {{ request('start_date') ? 'inline-block' : 'none' }};">Filter
				                </button>

				                @if(request('start_date') || request('end_date'))
				                    <a href="{{ route('collectiongencytrend') }}" id="clear-link" class="btn btn-primary">Remove</a>
				                @endif
				            </form>
				    </div>   
				    <!-- V Filter Date Range End ---------->
				</div>


				<div class="card-body">
					<!-- @if(session('success'))
					    <div class="alert alert-success">
					        {{ session('success') }}
					    </div>
					@endif -->

					@if(session('error'))
					    <div class="alert alert-danger">
					        {{ session('error') }}
					    </div>
					@endif

					<div class="collection_agency_trend">
					<!--- V Collection Agency Trend Start --------------->
				    <div class="mb-3">
				        <div class="cardBox" style="padding: 5px;">
				            <div class="d-flex justify-content-between align-items-center mb-3">
				                <!-- Agency Name Filter -->
					            <div class="col-md-4">
					                <!-- Agency Name Filter -->
					                <form method="GET" action="{{ route('collectiongencytrend') }}" id="filter-form">
					                    <div class="form-group">
					                        <label for="agency_name">Agency Name</label>
									        <input 
									            type="text" 
									            name="agency_name" 
									            id="agency_name" 
									            class="form-control" 
									            value="{{ request('agency_name') }}" 
									            placeholder="Type agency name">

									            <!-- <span type="submit" class="btn btn-primary">Filter</span> -->
					                    </div>
					                    @if(request('agency_name'))
					                        <a href="{{ route('collectiongencytrend') }}" class="btn btn-secondary">Clear Filter</a>
					                    @endif
					                </form>
					            </div>

				            </div>

				            <div class="row m-0">
				                @if($collAgencyTrendData->isEmpty())
				                    <div class="col-md-12 px-2">
				                        <div class="cardBox managerTxt m-0">
				                            <p class="text-center text-danger">No data available.</p>
				                        </div>
				                    </div>
				                @else
				                    @foreach ($finalDataAgencyTrend as $data)
				                    <div class="col-md-4 px-2">
				                        <div class="cardBox managerTxt m-0">
				                            <div class="d-flex justify-content-between">
				                                <h6>{{ $data->agency_name ?? '' }}
				                                    <p>{{ $data->location ?? '' }}</p>
				                                </h6>
				                                <span class="auditScore">Score <b>{{ number_format($data->average_score_percentage, 2) }}%</b></span>
				                            </div>
				                            <hr class="my-2" />
				                            <div class="row justify-content-between px-1 py-1">
				                                <div class="col-8 px-2">
				                                    <p>
				                                        Collection Manager
				                                        <b>{{ $data->collection_manager_name ?? '' }}</b>
				                                    </p>
				                                </div>
				                                <div class="col-4 px-2 text-end">
				                                    <p class="countmain">
				                                        Audit Count
				                                        <b>{{ $data->audit_count ?? '' }}</b>
				                                    </p>
				                                </div>
				                            </div>

				                            <!-- Overall Scores -->
				                            <h5 class="text-primary fw-semibold mt-2">Overall</h5>
				                            <ul class="scoreList">
				                                @foreach($data->six_month_data as $trend)
				                                    <li>
				                                        <!-- Display the month and year -->
				                                        <span class="text-dark">{{ $trend['month_year'] }}</span>
				                                        
				                                        @if($trend['average_score'] > 50)
				                                            <span class="bg-success">
				                                                {{ number_format($trend['average_score']) }}%
				                                            </span>
				                                        @elseif($trend['average_score'] <= 50 && $trend['average_score'] > 0)
				                                            <span class="bg-danger">
				                                                {{ number_format($trend['average_score']) }}%
				                                            </span>
				                                        @else
				                                            <span class="bg-secondary">
				                                                0%
				                                            </span>
				                                        @endif
				                                    </li>
				                                @endforeach
				                            </ul>

				                            <hr class="my-2" />
				                            <!-- Product Tabs -->
				                            <ul class="nav nav-tabs zoneTabs">
				                                <li class="nav-item" role="presentation">
				                                    <button class="nav-link active" data-toggle="tab" data-target="#retail-{{ $data->agency_id }}" type="button">Retail</button>
				                                </li>
				                                <li class="nav-item" role="presentation">
				                                    <button class="nav-link" data-toggle="tab" data-target="#creditcard-{{ $data->agency_id }}" type="button">CreditCard</button>
				                                </li>
				                                <li class="nav-item" role="presentation">
				                                    <button class="nav-link" data-toggle="tab" data-target="#retailCard-{{ $data->agency_id }}" type="button">Retail + Credit Card</button>
				                                </li>
				                            </ul>

				                            <div class="tab-content">
				                                <!-- Retail Tab -->
				                                <div class="tab-pane fade active show" id="retail-{{ $data->agency_id }}">
				                                    <ul class="scoreList">
				                                        @foreach($data->six_month_data as $trend)
				                                            @if($trend['retail_average_score_percentage'] > 50)
				                                            <li>
				                                                <span class="text-dark">{{ $trend['month_year'] }}</span>
				                                                <span class="bg-success">
				                                                    {{ number_format($trend['retail_average_score_percentage']) }}%
				                                                </span>
				                                                </li>
				                                            @elseif($trend['retail_average_score_percentage'] <= 50 && $trend['retail_average_score_percentage'] > 0)
				                                                <li>
				                                                    <span class="text-dark">{{ $trend['month_year'] }}</span>
				                                                    <span class="bg-danger">
				                                                        {{ number_format($trend['retail_average_score_percentage']) }}%
				                                                    </span>
				                                                </li>
				                                            @else
				                                                <li>
				                                                    <span class="text-dark">{{ $trend['month_year'] }}</span>
				                                                    <span class="bg-secondary">
				                                                        0%
				                                                    </span>
				                                                </li>
				                                            @endif
				                                        @endforeach
				                                    </ul>
				                                </div>

				                                <!-- Credit Card Tab -->
				                                <div class="tab-pane fade" id="creditcard-{{ $data->agency_id }}">
				                                    <ul class="scoreList">
				                                        @foreach($data->six_month_data as $trend)
				                                            @if($trend['card_average_score_percentage'] > 50)
				                                            <li>
				                                                <span class="text-dark">{{ $trend['month_year'] }}</span>
				                                                <span class="bg-success">
				                                                    {{ number_format($trend['card_average_score_percentage']) }}%
				                                                </span>
				                                                </li>
				                                            @elseif($trend['card_average_score_percentage'] <= 50 && $trend['card_average_score_percentage'] > 0)
				                                                <li>
				                                                    <span class="text-dark">{{ $trend['month_year'] }}</span>
				                                                    <span class="bg-danger">
				                                                        {{ number_format($trend['card_average_score_percentage']) }}%
				                                                    </span>
				                                                </li>
				                                            @else
				                                                <li>
				                                                    <span class="text-dark">{{ $trend['month_year'] }}</span>
				                                                    <span class="bg-secondary">
				                                                        0%
				                                                    </span>
				                                                </li>
				                                            @endif
				                                        @endforeach
				                                    </ul>
				                                </div>

				                                <!-- Retail + Credit Card Tab -->
				                                <div class="tab-pane fade" id="retailCard-{{ $data->agency_id }}">
				                                    <ul class="scoreList">
				                                        @foreach($data->six_month_data as $trend)
				                                            @if($trend['retails_card_average_score_percentage'] > 50)
				                                            <li>
				                                                <span class="text-dark">{{ $trend['month_year'] }}</span>
				                                                <span class="bg-success">
				                                                    {{ number_format($trend['retails_card_average_score_percentage']) }}%
				                                                </span>
				                                                </li>
				                                            @elseif($trend['retails_card_average_score_percentage'] <= 50 && $trend['retails_card_average_score_percentage'] > 0)
				                                                <li>
				                                                    <span class="text-dark">{{ $trend['month_year'] }}</span>
				                                                    <span class="bg-danger">
				                                                        {{ number_format($trend['retails_card_average_score_percentage']) }}%
				                                                    </span>
				                                                </li>
				                                            @else
				                                                <li>
				                                                    <span class="text-dark">{{ $trend['month_year'] }}</span>
				                                                    <span class="bg-secondary">
				                                                        0%
				                                                    </span>
				                                                </li>
				                                            @endif
				                                        @endforeach
				                                    </ul>
				                                </div>
				                            </div>

				                        </div>
				                    </div>
				                    @endforeach
				                @endif
				            </div>

				        </div>
				    </div>
					<!--- V Collection Agency Trend Start --------------->
					<div class="row">
						 <div class="col-10">
						 	<a href="javascript:history.back()" class="btn btn-primary">
							   Back
							</a>
						 </div>

						 <div class="col-2 d-flex justify-content-end">
						    <ul class="pagination">
						        {{ $collAgencyTrendData->links('vendor.pagination.custom') }} 
						    </ul>
						</div>
					</div>	
					</div>

				</div>

			</div>

		</div>
	</div>
</div>

<div class="clearfix"></div>

@endsection

@section('css')

<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">

@endsection

@section('js')

<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

<script>
	jQuery(document).on('ready',function(){

		jQuery('#kt_table_1').DataTable();

	})
</script>

<script type="text/javascript">
   function block_user(id) {
		if (confirm("Are you sure you want to block?")) {

			var link  = '/user/'+id+'/disable'
			location.href = link;
		}
	}
</script>

<script>
    function confirmDelete() {
        return confirm('Are you sure you want to delete this item?');
    }
</script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-more.js"></script>
<script src="{{asset('clientdashboard/js/owl.carousel.min.js')}}"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

<!--V Filter Dashbaord Start -------------------------------------------->
<script>
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



<!--V Collection Agency Filter By Name Start ----------------------------->
<script>
    document.getElementById('agency_name').addEventListener('input', function () {
        const agencyName = this.value;

        // Make AJAX request to fetch filtered data
        fetch("{{ route('collectiongencytrend') }}?agency_name=" + agencyName, {
            method: "GET",
            headers: {
                "X-Requested-With": "XMLHttpRequest"
            }
        })
        .then(response => response.text()) // Expect HTML response
        .then(data => {
            document.getElementById('agency-data-container').innerHTML = data; // Update the container with the response
        })
        .catch(error => {
            console.error('Error fetching agency data:', error);
        });
    });
</script>
<!--V Collection Agency Filter By Name End ----------------------------->

@endsection