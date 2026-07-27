<div id="productwisechart" style="min-height: 400px;"></div>


<script>
Highcharts.chart('productwisechart', {
    chart: {
        zoomType: 'xy',
        backgroundColor: '#f9f9f9',
        style: {
            fontFamily: 'Segoe UI, Roboto, sans-serif'
        }
    },
    title: {
        text: 'Product-wise Audit Overview',
        style: {
            fontSize: '20px',
            fontWeight: 'bold'
        }
    },
    xAxis: {
        categories:{!! json_encode($agencyProducts) !!},
        crosshair: true,
        labels: {
            style: {
                fontSize: '13px'
            }
        }
    },
    yAxis: [{
        title: {
            text: 'Audit Score (%)',
            style: {
                color: '#0071A7',
                fontWeight: 'bold'
            }
        },
        labels: {
            format: '{value}%',
            style: {
                color: '#0071A7'
            }
        },
        max: 100,           
        min: 0,             
        endOnTick: false,   
        tickAmount: 6  
    }, {
        title: {
            text: 'Audit Count',
            style: {
                color: '#FF5733',
                fontWeight: 'bold'
            }
        },
        labels: {
            style: {
                color: '#FF5733'
            }
        },
        opposite: true
    }],
    legend: {
        layout: 'horizontal',
        align: 'center',
        verticalAlign: 'bottom',
        itemStyle: {
            fontWeight: 'normal',
            fontSize: '13px'
        }
    },
    tooltip: {
        shared: true,
        backgroundColor: '#ffffff',
        borderColor: '#ccc',
        style: {
            fontSize: '13px'
        }
    },
    plotOptions: {
        column: {
            colorByPoint: true,
            borderRadius: 5,
            dataLabels: {
                enabled: true,
                format: '{y}%',
                style: {
                    fontSize: '11px'
                }
            }
        },
        spline: {
            marker: {
                enabled: true,
                symbol: 'circle',
                radius: 4
            },
            lineWidth: 2,
            dataLabels: {
                enabled: true,
                format: '{y}',
                style: {
                    fontSize: '11px'
                }
            }
        }
    },
    series: [{
        name: 'Audit Score',
        type: 'column',
        yAxis: 0,
        data: {!! json_encode($auditScoreProducts) !!},
        color: '#0071A7'
    }, {
        name: 'Audit Count',
        type: 'spline',
        yAxis: 1,
        data: {!! json_encode($auditCountProducts) !!},
        color: '#FF5733'
    }]
});
</script>
