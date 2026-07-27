
<div style="text-align: center; margin-bottom: 10px;">
    <button id="backTotopStatesBtn" style="display:none; padding: 8px 16px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">
        ← Back to States
    </button>
</div>

<div id="topChart" style="width: 100%; height: 400px; margin: 50px 0px 10px -2px;"></div>


<script>
    const cityDataTop = {!! json_encode($cityWiseArray) !!};

    function renderTopStateChart() {
        document.getElementById('backTotopStatesBtn').style.display = 'none';

        Highcharts.chart('topChart', {
            chart: { type: 'column', marginTop: 60},
            title: { text: '' },
            xAxis: {
                categories: {!! json_encode($states) !!},
                title: { text: 'States' },
                labels: { rotation: -45 , style: {
                        whiteSpace: 'nowrap', // prevent overlap
                        textOverflow: 'ellipsis' // show ... if too long
                        }
                    }
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
                    },
                    cursor: 'pointer',
                    point: {
                        events: {
                            click: function () {
                                const state = this.name;
                                if (cityDataTop[state]) {
                                    renderCityChartTop(state);
                                } else {
                                    alert(`No city data available for ${state}`);
                                }
                            }
                        }
                    }
                }
            },
            series: [{
                name: 'Audit Score',
                data: {!! json_encode($stateWiseArray) !!}
            }]
        });
    }

    function renderCityChartTop(state) {
        document.getElementById('backTotopStatesBtn').style.display = 'inline-block';

        Highcharts.chart('topChart', {
            chart: { type: 'column' , marginTop: 60 },
            title: { text: `` },
            xAxis: {
                categories: cityDataTop[state].map(c => c.name),
                title: { text: 'Cities' },
                labels: { rotation: -45 , style: {
                        whiteSpace: 'nowrap', // prevent overlap
                        textOverflow: 'ellipsis' // show ... if too long
                        } }
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
                data: cityDataTop[state]
            }]
        });
    }

    // Event for "Back to States" button
    document.getElementById('backTotopStatesBtn').addEventListener('click', function () {
        renderTopStateChart();
    });

    // Initial chart render
    renderTopStateChart();
</script>

