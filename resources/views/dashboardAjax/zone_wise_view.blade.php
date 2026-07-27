<div class="d-flex justify-content-between">
    <h6 class="m-0">
        <span> Zone Wise</span>
        <i class="fa fa-info-circle text-primary"
            data-toggle="tooltip"
            data-placement="top"
            title="Zone Wise data shows the breakdown by geographical zones.">
        </i>
    </h6>

    <div class="w-70">
        <span class="d-block mb-1">Select Zone </span>
        <ul class="nav nav-tabs zoneTabs">
            @php $s=0; @endphp
            @foreach($allocationZoneProduct as $index => $zoneData)
            <li class="nav-item" role="presentation">
                <button style="color:white;" class="nav-link {{ $s == 0 ? 'active' : '' }}" data-toggle="tab" data-target="#{{ strtolower($index) }}" type="button">
                    {{ $index }}
                </button>
            </li>
            @php $s++; @endphp
            @endforeach
        </ul>
    </div>
</div>
<div class="tab-content">
    @php $s=0; @endphp
    @foreach($allocationZoneProduct as $index => $zoneData)
    <div class="tab-pane fade {{ $s == 0 ? 'show active' : '' }}" id="{{ strtolower($index) }}">
        <div class="d-flex align-items-center">
            <div class="leftSec">
                <div id="container_{{$index}}" style="width: 250px; height: 250px;"></div>
            </div>
            <div class="rightSec">
                <div class="d-flex align-items-center justify-content-between mb-2 mt-3">
                </div>
                <div class="">
                    @php 
                        $key=array_search($index, array_column($databyZoneRegionAudits, 'region_name'));
                    @endphp
                    <div class="d-flex align-items-center  mb-4">
                        <span class="auditScore py-3 mr-5">Audit Average Score <b id="auditScore{{ $index }}">{{ ($key !== false) ? round($databyZoneRegionAudits[$key]['average_score']) : 0 }}</b></span>
                        <span class="auditScore py-3">Audit Average Score Percentage <b id="auditScore{{ $index }}">{{ ($key !== false) ? round($databyZoneRegionAudits[$key]['average_score_percentage']) : 0 }}%</b></span>
                    </div>
                    
                    <div class="row datalMain">
                        <div class="col-6 border-end mb-3">
                            <span>Assigned Agency <b id="assignedAgency{{ $index }}"><?= (array_sum(array_column($zoneData, 'product_count')) == 0) ? 0 : array_sum(array_column($zoneData, 'product_count'))  ?></b></span>
                        </div>
                        <div class="col-6">
                            <span class="completed">Audit Submitted <b id="auditCount{{ $index }}">{{ ($key !== false) ? $databyZoneRegionAudits[$key]['audit_count']  : 0 }}</b></span>
                        </div>
                        <div class="col-6 border-end mb-3">
                            <span>Sent for Action Planning <b id="sentForClosureCount{{ $index }}">{{ ($key !== false) ? $databyZoneRegionAudits[$key]['sent_for_closure_count']  : 0 }}</b></span>
                        </div>
                        <div class="col-6 mb-3">
                            <span class="completed">Action Planning Completed <b id="closureCompletedCount{{ $index }}">{{ ($key !== false) ? $databyZoneRegionAudits[$key]['closure_completed_count']  : 0 }}</b></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @php $s++; @endphp
    @endforeach
</div>


<!----- V Zone Wise Get Data --------------------------------------->

@foreach($allocationZoneProduct as $index => $zoneData)
<script>
    const totalAllocation{{ $index }} = {!! (array_sum(array_column($zoneData, 'product_count')) == 0) ? 0 : array_sum(array_column($zoneData, 'product_count')) !!};
    
    Highcharts.chart('container_{{ $index  }}', {
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
            data: [
                <?php   
                    $i=0;
                    foreach($zoneData as $k=>$pro_data) { ?>

                        {
                            name: "{{$pro_data['product']}}",
                            y: {{$pro_data['product_count']}}
                            
                        } 

                        <?php if($i != count($zoneData)) { echo ","; } ?>



                <?php $i++; } ?>
                
              ]
        }]
    });
</script>

@endforeach

<!--- V Zone Wise one Wise Get Data End ---------------------------->