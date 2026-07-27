@extends('layouts.master')
@section('title', '| Users')


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Paretor High Chart</title>
  
    <!-- V Paretor Hight Chart -->
   
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/pareto.js"></script>
    <!-- <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script> -->
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>


</head>
<style>
    .site-footer{
        display: none;
    }
    
    .content {
        height: 100px !important;
    }

</style>
<body>


<div class="content" style="bottom:0; margin-left:278px; width:78%; margin-top: 40px;">
    <div class="row">
        <div class="col-lg-12" style="margin-top:0x">

        </div>
    </div>
    <div class="animated fadeIn">
        <div class="row">
            <div class="col-lg-12">

        <!---- V Pareto Hight Chart Start ---------------------------------->
                <div class="card">
                    <div class="card-header">
                        <div class="row">   
                            <div class="col-11">
                            <h4 class="card-title">Pareto Chart</h4>
                            </div>
                            <div class="col-1">
                                <a href="{{ route('dashboard') }}" class="">
                                    <i class="fa fa-arrow-circle-left" style="font-size: 34px;"></i>
                                </a>
                            </div>              
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="collection_agency_trend">
                            <div class="mb-3">
                                <div class="cardBox" style="padding: 5px;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="form-group">
                                            <label for="agencyDropdown">Select Agency</label>
                                            <select id="agencyDropdown" class="form-control">
                                                <option value="all">All Agencies</option>
                                                @foreach($agencies as $agency)
                                                    <option value="{{ $agency->id }}">{{ $agency->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>   
                                    </div>
                                    <div class="col-md-12">
                                        <figure class="highcharts-figure">
                                            <div id="pareto_id" style="min-height: 350px; width: 870px;"></div>
                                            <p class="highcharts-description">
                                            </p>
                                        </figure>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>    
                </div>
            </div>
        </div>
    </div> 
</div>                                       
    <!---- V Paretor Chart End ----------------------------------------->





<!--V Paretor HighChart Sample Start ----------------------------------->
<!-- <script type="text/javascript">
    Highcharts.chart('pareto_id', {
        chart: {
            renderTo: 'pareto_id',
            type: 'column'
        },
        title: {
            text: 'Audit Report for Unsatisfactory Parameters'
        },
        tooltip: {
            shared: true
        },
        xAxis: {
            categories: [
                'Overpriced',
                'Small portions',
                'Wait time',
                'Food is tasteless',
                'No atmosphere',
                'Not clean',
                'Too noisy',
                'Unfriendly staff'
            ],
            crosshair: true
        },
        yAxis: [{
            title: {
                text: ''
            }
        }, {
            title: {
                text: ''
            },
            minPadding: 0,
            maxPadding: 0,
            max: 100,
            min: 0,
            opposite: true,
            labels: {
                format: '{value}%'
            }
        }],
        series: [{
            type: 'pareto',
            name: 'Pareto',
            yAxis: 1,
            zIndex: 10,
            baseSeries: 1,
            tooltip: {
                valueDecimals: 2,
                valueSuffix: '%'
            }
        }, {
            name: 'Complaints',
            type: 'column',
            zIndex: 2,
            data: [755, 222, 151, 86, 72, 51, 36, 10]
        }]
    });
</script> -->
<!--V Paretor HighChart Sample End ------------------------------------->


<!--V Paretor HighChart Start ------------------------------------------>
<!-- <script type="text/javascript">
    $(document).ready(function () {
        // Set default locale settings (if needed)
        Highcharts.setOptions({
            lang: {
                thousandsSep: ',', // Thousand separator, adjust if needed
                decimalPoint: '.'   // Decimal point, adjust if needed
            }
        });

        $.ajax({
            url: '{{ route('paretochartdata') }}',  // Ensure this URL returns the correct data
            method: 'GET',
            success: function(response) {
                console.log(response);  // Log the response to ensure data is coming in correctly

                // Check if all necessary data is present
                if (!response.categories || !response.data || !response.cumulative) {
                    console.error("Missing data for categories, data, or cumulative.");
                    return;
                }

                // Create the Highcharts Pareto chart
                Highcharts.chart('pareto_id', {
                    chart: { type: 'column' },
                    title: { text: 'Audit Report for Unsatisfactory Sub Parameters' },
                    xAxis: {
                        categories: response.categories,  // X-axis categories
                        crosshair: true
                    },
                    yAxis: [{
                        title: { text: 'Number of Complaints' }
                    }, {
                        title: { text: 'Cumulative Percentage' },
                        opposite: true,
                        max: 100,  // Set max value for the cumulative percentage axis
                        labels: {
                            formatter: function () {
                                return Highcharts.numberFormat(this.value, 2) + '%';
                            }
                        }
                    }],
                    series: [{
                        type: 'column',
                        name: 'Complaints',
                        data: response.data  // Complaints data
                    }, {
                        type: 'line',  // Use 'line' to display the cumulative percentage
                        name: 'Cumulative Percentage',
                        yAxis: 1,  // Link to the cumulative percentage Y-axis
                        data: response.cumulative,  // Cumulative data
                        tooltip: {
                            pointFormatter: function () {
                                return '<span style="color:' + this.color + '">●</span> ' + this.series.name + ': <b>' + Highcharts.numberFormat(this.y, 2) + '%</b><br/>';
                            }
                        }
                    }]
                });
            },
            error: function(err) {
                console.error('Error fetching Pareto data:', err);  // Log error in case of failure
            }
        });
    });
</script>
 -->


<script type="text/javascript">
    $(document).ready(function () {
        // Set default locale settings (if needed)
        Highcharts.setOptions({
            lang: {
                thousandsSep: ',', // Thousand separator, adjust if needed
                decimalPoint: '.'   // Decimal point, adjust if needed
            }
        });

        // Function to fetch and render the chart
        function fetchParetoData(agencyId) {
            $.ajax({
                url: '{{ route('paretochartdata') }}', // Ensure this URL returns the correct data
                method: 'GET',
                data: { agency_id: agencyId }, // Pass the selected agency ID
                success: function (response) {
                    console.log(response); // Log the response to ensure data is coming in correctly

                    // Check if all necessary data is present
                    if (!response.categories || !response.data || !response.cumulative) {
                        console.error("Missing data for categories, data, or cumulative.");
                        return;
                    }

                    // Create or update the Highcharts Pareto chart
                    Highcharts.chart('pareto_id', {
                        chart: { type: 'column' },
                        title: { text: 'Audit Report for Unsatisfactory Sub Parameters' },
                        xAxis: {
                            categories: response.categories, // X-axis categories
                            crosshair: true
                        },
                        yAxis: [{
                            title: { text: 'Number of Unsatisfactory' }
                        }, {
                            title: { text: 'Cumulative Percentage' },
                            opposite: true,
                            max: 100, // Set max value for the cumulative percentage axis
                            labels: {
                                formatter: function () {
                                    return Highcharts.numberFormat(this.value, 2) + '%';
                                }
                            }
                        }],
                        series: [{
                            type: 'column',
                            name: 'Unsatisfactory',
                            data: response.data // Complaints data
                        }, {
                            type: 'line', // Use 'line' to display the cumulative percentage
                            name: 'Cumulative Percentage',
                            yAxis: 1, // Link to the cumulative percentage Y-axis
                            data: response.cumulative, // Cumulative data
                            tooltip: {
                                pointFormatter: function () {
                                    return '<span style="color:' + this.color + '">●</span> ' + this.series.name + ': <b>' + Highcharts.numberFormat(this.y, 2) + '%</b><br/>';
                                }
                            }
                        }]
                    });
                },
                error: function (err) {
                    console.error('Error fetching Pareto data:', err); // Log error in case of failure
                }
            });
        }

        // Initial chart load with "all" agencies
        fetchParetoData('all');

        // Update chart on agency dropdown change
        $('#agencyDropdown').on('change', function () {
            const selectedAgencyId = $(this).val(); // Get selected agency ID
            fetchParetoData(selectedAgencyId); // Fetch and render data for the selected agency
        });
    });
</script>


<!--V Paretor HighChart End -------------------------------------------->

</body>
</html>

