
<div style="text-align: center; margin-bottom: 10px;">
    <button id="backToStatesBtn" style="display:none; padding: 8px 16px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">
        ← Back to States
    </button>
</div>

<div id="topBottomChart" style="width: 100%; height: 400px; margin: auto;"></div>


<script>
    const cityData = {
        'Maharashtra': [
            { name: 'Mumbai', y: 94, color: '#28a745' },
            { name: 'Pune', y: 88, color: '#28a745' },
            { name: 'Nagpur', y: 82, color: '#28a745' },
            { name: 'Nashik', y: 78, color: '#28a745' },
            { name: 'Thane', y: 75, color: '#28a745' },

            { name: 'Aurangabad', y: 60, color: '#dc3545' },
            { name: 'Amravati', y: 57, color: '#dc3545' },
            { name: 'Solapur', y: 54, color: '#dc3545' },
            { name: 'Kolhapur', y: 52, color: '#dc3545' },
            { name: 'Latur', y: 49, color: '#dc3545' }
        ],
        'Gujarat': [
            { name: 'Ahmedabad', y: 90, color: '#28a745' },
            { name: 'Surat', y: 88, color: '#28a745' },
            { name: 'Vadodara', y: 85, color: '#28a745' },
            { name: 'Rajkot', y: 80, color: '#28a745' },
            { name: 'Gandhinagar', y: 78, color: '#28a745' },

            { name: 'Bhuj', y: 58, color: '#dc3545' },
            { name: 'Jamnagar', y: 56, color: '#dc3545' },
            { name: 'Anand', y: 53, color: '#dc3545' },
            { name: 'Bharuch', y: 51, color: '#dc3545' },
            { name: 'Mehsana', y: 48, color: '#dc3545' }
        ]
    };

    function renderStateChart() {
        document.getElementById('backToStatesBtn').style.display = 'none';

        Highcharts.chart('topBottomChart', {
            chart: { type: 'column' },
            title: { text: 'Top 5 and Bottom 5 Performers(States & Cities)' },
            xAxis: {
                categories: [
                    'Maharashtra', 'Gujarat', 'Karnataka', 'Tamil Nadu', 'Kerala',
                    'Bihar', 'Jharkhand', 'Chhattisgarh', 'Odisha', 'UP'
                ],
                title: { text: 'States' },
                labels: { rotation: -45 }
            },
            yAxis: {
                min: 0,
                max: 100,
                title: { text: 'Audit Score (%)' }
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
                        style: { fontWeight: 'bold' }
                    },
                    cursor: 'pointer',
                    point: {
                        events: {
                            click: function () {
                                const state = this.name;
                                if (cityData[state]) {
                                    renderCityChart(state);
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
                data: [
                    { name: 'Maharashtra', y: 92, color: '#28a745' },
                    { name: 'Gujarat', y: 89, color: '#28a745' },
                    { name: 'Karnataka', y: 87, color: '#28a745' },
                    { name: 'Tamil Nadu', y: 85, color: '#28a745' },
                    { name: 'Kerala', y: 83, color: '#28a745' },

                    { name: 'Bihar', y: 42, color: '#dc3545' },
                    { name: 'Jharkhand', y: 45, color: '#dc3545' },
                    { name: 'Chhattisgarh', y: 48, color: '#dc3545' },
                    { name: 'Odisha', y: 50, color: '#dc3545' },
                    { name: 'UP', y: 53, color: '#dc3545' }
                ]
            }]
        });
    }

    function renderCityChart(state) {
        document.getElementById('backToStatesBtn').style.display = 'inline-block';

        Highcharts.chart('topBottomChart', {
            chart: { type: 'column' },
            title: { text: `Top 5 and Bottom 5 Performing Cities in ${state}` },
            xAxis: {
                categories: cityData[state].map(c => c.name),
                title: { text: 'Cities' },
                labels: { rotation: -45 }
            },
            yAxis: {
                min: 0,
                max: 100,
                title: { text: 'Audit Score (%)' }
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
                        style: { fontWeight: 'bold' }
                    }
                }
            },
            series: [{
                name: 'Audit Score',
                data: cityData[state]
            }]
        });
    }

    // Event for "Back to States" button
    document.getElementById('backToStatesBtn').addEventListener('click', function () {
        renderStateChart();
    });

    // Initial chart render
    renderStateChart();
</script>

