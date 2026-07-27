
<div style="text-align: center; margin-bottom: 10px;">
    <button id="backToStatesBtnBot" style="display:none; padding: 8px 16px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">
        ← Back to States
    </button>
</div>

<div id="bottomChart" style="width: 100%; height: 400px; margin: 50px 0px 10px -2px;"></div>


<script>
    const cityDataBot = {!! json_encode($cityWiseArray) !!};

    function renderBotStateChart() {
        document.getElementById('backToStatesBtnBot').style.display = 'none';

        Highcharts.chart('bottomChart', {
            chart: { type: 'column' , marginTop: 60 },
            title: { text: '' },
            xAxis: {
                categories: {!! json_encode($states) !!},
                title: { text: 'States', style: {
                        fontSize: '12px',
                        fontWeight: 'bold'
                    } },
                labels: { rotation: -45 , style: {
                        whiteSpace: 'nowrap', // prevent overlap
                        textOverflow: 'ellipsis' // show ... if too long
                        }}
            },
            yAxis: {
                min: 0,
                max: 100,
                title: { text: 'Audit Score (%)', style: {
                        fontSize: '12px',
                        fontWeight: 'bold'
                    } },
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
                                if (cityDataBot[state]) {
                                    renderCityChartBot(state);
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
                data: {!! json_encode($stateWiseArray) !!},
            }]
        });
    }

    function renderCityChartBot(state) {
        document.getElementById('backToStatesBtnBot').style.display = 'inline-block';

        Highcharts.chart('bottomChart', {
            chart: { type: 'column' , marginTop: 60 },
            title: { text: `` },
            xAxis: {
                categories: cityDataBot[state].map(c => c.name),
                title: { text: 'Cities', style: {
                        fontSize: '12px',
                        fontWeight: 'bold'
                    } },
                labels: { rotation: -45 , style: {
                        whiteSpace: 'nowrap', // prevent overlap
                        textOverflow: 'ellipsis' // show ... if too long
                        }}
            },
            yAxis: {
                min: 0,
                max: 100,
                title: { text: 'Audit Score (%)', style: {
                        fontSize: '12px',
                        fontWeight: 'bold'
                    } },
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
                data: cityDataBot[state]
            }]
        });
    }

    // Event for "Back to States" button
    document.getElementById('backToStatesBtnBot').addEventListener('click', function () {
        renderBotStateChart();
    });

    // Initial chart render
    renderBotStateChart();
</script>

