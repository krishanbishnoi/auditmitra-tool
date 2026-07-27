<div id="topBottomCitiesChart" style="width: 100%; height: 400px; margin: auto;"></div>

<script>
    Highcharts.chart('topBottomCitiesChart', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Top 5 and Bottom 5 Performing Cities by Audit Score'
        },
        xAxis: {
            categories: [
                'Mumbai', 'Bengaluru', 'Chennai', 'Pune', 'Hyderabad', // Top 5
                'Patna', 'Ranchi', 'Raipur', 'Kanpur', 'Agra'          // Bottom 5
            ],
            title: {
                text: 'Cities'
            },
            labels: {
                rotation: -45
            }
        },
        yAxis: {
            min: 0,
            max: 100,
            title: {
                text: 'Audit Score (%)'
            }
        },
        legend: {
            enabled: false
        },
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
                    style: {
                        fontWeight: 'bold'
                    }
                }
            }
        },
        series: [{
            name: 'Audit Score',
            data: [
                { name: 'Mumbai', y: 94, color: '#28a745' },
                { name: 'Bengaluru', y: 91, color: '#28a745' },
                { name: 'Chennai', y: 89, color: '#28a745' },
                { name: 'Pune', y: 88, color: '#28a745' },
                { name: 'Hyderabad', y: 85, color: '#28a745' },

                { name: 'Patna', y: 41, color: '#dc3545' },
                { name: 'Ranchi', y: 44, color: '#dc3545' },
                { name: 'Raipur', y: 46, color: '#dc3545' },
                { name: 'Kanpur', y: 49, color: '#dc3545' },
                { name: 'Agra', y: 52, color: '#dc3545' }
            ]
        }]
    });
</script>
