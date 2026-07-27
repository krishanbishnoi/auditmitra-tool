<div id="pareto_state" style="height: 400px; width: 100%;"></div>

<script>
/* Build main data first so we can sort it before chart creation */
const mainData = [
    @foreach($data as $i => $state)
    {
        name: "{{ $state['state_name'] }}",
        y: {{ round($state['non_compliant_count']) }},
        drilldown: "{{ $state['state_name'] }}"
    },
    @endforeach
];

// sort descending so Pareto cumulative is correct
mainData.sort((a,b) => (b.y || 0) - (a.y || 0));

Highcharts.chart('pareto_state', {
    chart: {
        type: 'column',
        marginTop: 80 ,
        events: {
            // add initial Pareto after chart loads and main series is available
            load() {
                const chart = this;
                const mainIndex = chart.series.findIndex(s => s.options && s.options.id === 'main');
                if (mainIndex !== -1) {
                    chart.addSeries({
                        type: 'pareto',
                        name: 'Pareto',
                        yAxis: 1,
                        zIndex: 10,
                        baseSeries: mainIndex,
                        tooltip: {
                            valueDecimals: 0,
                            valueSuffix: '%'
                        },
                        dataLabels: { enabled: true, format: '{y:.0f}%' }
                    }, false);
                    chart.redraw();
                }
            },

            drilldown(e) {
                const chart = this;
                if (!e.seriesOptions) {
                    chart.showLoading('Loading data ...');

                    const stateName = e.point.drilldown;
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('pareto_param_view') }}",
                        dataType: 'json',
                        data: {
                            'currentCycleId': <?php echo $currentCycleId; ?>,
                            'stateName': stateName,'audit_type':"<?php echo $audit_type; ?>"
                        },
                        success: function (response) {
                            chart.hideLoading();

                            // sort response descending
                            response.sort((a, b) => b[1] - a[1]);

                            // add drilldown series
                            chart.addSeriesAsDrilldown(e.point, {
                                name: e.point.name + ' (Parameter Wise Pareto)',
                                id: stateName,
                                type: 'column',
                                colorByPoint: true, 
                                data: response.map(item => ({
                                    name: item[0],                 // parameter name
                                    y: item[1],                    // non_compliant_count
                                    parameter_id: item[2] ?? null  // parameter_id (make sure your response has it)
                                })),
                                point: {
                                    events: {
                                        click: function () {
                                            // this.name => parameter
                                            // this.parameter_id => parameter_id
                                            // this.y => non_compliant_count
                                            
                                            document.getElementById('param_geographical_view').scrollIntoView({
                                                    behavior: 'smooth', // smooth scroll
                                                    block: 'start' // align to top
                                            });
                                            
                                            $("#parameterSelect").val(this.parameter_id).trigger("change");
                                            getNewComplianceData(this.parameter_id);
                                            
                                        }
                                    }
                                }
                            });

                            // 🔥 Remove ALL old pareto lines
                            chart.series
                                .filter(s => s.type === 'pareto')
                                .forEach(s => s.remove(false));

                            // find new column series (drilldown one)
                            const newCol = chart.series.find(s => s.options.id === stateName);

                            // add fresh pareto line
                            chart.addSeries({
                                type: 'pareto',
                                name: 'Pareto',
                                yAxis: 1,
                                zIndex: 10,
                                baseSeries: chart.series.indexOf(newCol),
                                tooltip: {
                                    valueDecimals: 0,
                                    valueSuffix: '%'
                                },
                                dataLabels: { enabled: true, format: '{y:.0f}%' }
                            }, true);

                            chart.setTitle({ text: 'Parameter Wise Pareto' });
                        }
                    });
                }
            },

            drillup() {
                const chart = this;
                //  Remove ALL old pareto lines
                chart.series
                    .filter(s => s.type === 'pareto')
                    .forEach(s => s.remove(false));

                // find main column series
                const mainCol = chart.series.find(s => s.options.id === 'main');

                // add back top-level Pareto
                chart.addSeries({
                    type: 'pareto',
                    name: 'Pareto',
                    yAxis: 1,
                    zIndex: 10,
                    baseSeries: chart.series.indexOf(mainCol),
                    tooltip: {
                        valueDecimals: 0,
                        valueSuffix: '%'
                    },
                    dataLabels: { enabled: true, format: '{y:.0f}%' }
                }, true);

                chart.setTitle({ text: 'State Wise Pareto' });

            }
        }
    },

    title: { text: 'State Wise Pareto' , style: {
                        fontSize: '14px',
                        fontWeight: 'bold'
                    }},

    tooltip: { shared: true },

    xAxis: { type: 'category', crosshair: true },

    yAxis: [{
        title: { text: '' }
    }, {
        title: { text: '' },
        min: 0,
        max: 100,
        opposite: true,
        labels: { format: '{value}%' }
    }],

    legend: { enabled: true },

    plotOptions: {
        series: {
            borderWidth: 0,
            dataLabels: { enabled: true, format: '{y:.0f}' }
        }
    },

    series: [
        {
            name: 'States',
            type: 'column',
            zIndex: 2,
            id: 'main', // important: we use this id to re-link main pareto
            colorByPoint: true, 
            data: mainData
        }
        // NOTE: we intentionally do NOT define the initial pareto here;
        // it is added in chart.load so baseSeries is computed dynamically.
    ],

    drilldown: {
        breadcrumbs: { position: { align: 'right' } }
    }
});
</script>
