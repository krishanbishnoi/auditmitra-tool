


<div id="topChartAgency" style="width: 100%; height: 400px; margin: 50px 0px 10px -2px;"></div>


<script>  

    Highcharts.chart('topChartAgency', {
        chart: { type: 'column' , marginTop: 60 },
        title: { text: '' },
        xAxis: {
            categories: {!! json_encode($states) !!},
            title: { text: 'Agencies' },
            labels: { rotation: -45 , style: {
                        whiteSpace: 'nowrap', // prevent overlap
                        textOverflow: 'ellipsis' // show ... if too long
                        }  }
        },
        yAxis: {
            min: 0,
            max: 100,
            title: { text: 'Audit Score (%)' },
            maxPadding: 0.2
            
        },
        legend: { enabled: false },
        tooltip: {
            backgroundColor: '#2a2a2a',
            style: { color: '#fff' },
            pointFormat: '<b>{point.y}%</b>'
        },
        plotOptions: {
            column: {
                dataLabels: {
                    enabled: true,
                    format: '{y}%',
                    style: { fontWeight: 'bold' },
                    inside: false,         
                    overflow: 'none',      
                    crop: false   
                }     
            }
        },
        series: [{
            name: 'Audit Score',
            data: {!! json_encode($stateWiseArray) !!}
        }]
    });
    
</script>

