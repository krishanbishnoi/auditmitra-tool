<div id="audit_schedule_wise_chart" style="min-height: 500px; max-width: 900px; margin: auto;"></div>

<script>
    const data =  @json($finalArray);
    
    function generateChartData(data) {
        const chartData = [];
        const firstWeekday = new Date(data[0].date).getDay();

        for (let i = 0; i < firstWeekday; i++) {
            chartData.push({ x: i, y: 5, value: null, custom: { empty: true } });
        }

        data.forEach((d, idx) => {
            const day = new Date(d.date).getDay();
            const x = (firstWeekday + idx) % 7;
            const y = 5 - Math.floor((firstWeekday + idx) / 7);
            const dateval = new Date(d.date).getDate();
            chartData.push({
                x,
                y,
                value: d.audit_count,
                date: new Date(d.date).getTime(),                
                custom: {
                    monthDay: dateval,
                    assigned: d.assigned_count,
                    submitted: d.audit_count,
                    isSunday: day === 0,
                    isToday: new Date(d.date).toDateString() === new Date().toDateString()
                }
            });
        });

        while (chartData.length < 42) {
            chartData.push({
                x: chartData.length % 7,
                y: 0,
                value: null,
                custom: { empty: true }
            });
        }

        return chartData;
    }

    Highcharts.chart('audit_schedule_wise_chart', {
        chart: {
            type: 'heatmap',
            inverted: true,
            marginTop: 60,
            marginBottom: 80
        },
        title: {
            text: "📊Activity Calendar",
            style: { fontSize: '14px', fontWeight: 'bold' }
        },
        xAxis: {
            categories: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
            opposite: false,
            tickLength: 0,
            labels: {
                style: {
                    fontWeight: 'bold',
                    textTransform: 'uppercase',
                    color: null // default
                },
                formatter: function () {
                    return this.value === 'Sun'
                        ? `<span style="fill:red">${this.value}</span>`
                        : this.value;
                },
                useHTML: true
            }
        },
        yAxis: {
            categories: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6'],
            reversed: true,
            gridLineWidth: 0,
            labels: { enabled: false },
            title: null
        },
       tooltip: {
            outside: true,
            useHTML: true,
            backgroundColor: 'rgba(0, 0, 0, 0.85)',
            borderColor: '#333',
            style: {
                color: '#fff',
                fontSize: '13px',
                padding: '8px'
            },
            formatter: function () {
                if (this.point.custom?.empty) return 'No data';
                return `<b>${Highcharts.dateFormat('%A, %b %e, %Y', this.point.date)}</b><br>
                    <span style="color:#90caf9">Assigned:</span> <b>${this.point.custom.assigned}</b><br>
                    <span style="color:#a5d6a7">Submitted:</span> <b>${this.point.custom.submitted}</b>`;
            }
        },
        colorAxis: {
            min: 0,
            max: 50,
            stops: [
                [0, '#e3f2fd'],
                [0.5, '#64b5f6'],
                [1, '#1e88e5']
            ]
        },
        legend: {
            align: 'center',
            verticalAlign: 'bottom',
            layout: 'horizontal',
            itemStyle: { fontWeight: 'normal' }
        },
        series: [{
            name: 'Audits Submitted',
            data: generateChartData(data),
            borderWidth: 2,
            borderColor: '#fff',
            borderRadius: 6,
            nullColor: '#f1f1f1',
            dataLabels: [{
                enabled: true,
                formatter: function () {
                    if (this.point.custom?.empty) return '';
                    const isSun = this.point.custom?.isSunday;
                    const color = isSun ? 'red' : '#000';
                    return `<span style="color:${color}">A:${this.point.custom.assigned}<br>S:${this.point.custom.submitted}</span>`;
                },
                style: {
                    textOutline: 'none',
                    fontSize: '10px',
                    whiteSpace: 'pre-line',
                    textAlign: 'center'
                },
                y: 5,
                useHTML: true
            }, {
                enabled: true,
                formatter: function () {
                    if (this.point.custom?.empty) return '';
                    return `<span style="color:#888;font-size:9px;font-weight:bold">${this.point.custom.monthDay}</span>`;
                },
                align: 'left',
                verticalAlign: 'top',
                x: 3,
                y: 2,
                useHTML: true
            }]
        }],
        plotOptions: {
            series: {
                cursor: 'pointer',
                point: {
                    events: {
                        click: function () {
                            if (this.custom?.empty) return;

                            const date = Highcharts.dateFormat('%A, %b %e, %Y', this.date);
                            const assigned = this.custom.assigned;
                            const submitted = this.custom.submitted;
                            const actualdate = Highcharts.dateFormat('%Y-%m-%d', this.date);

                            const modalHtml = `
                                <p><strong>Date:</strong> ${date}</p>
                                <p><strong>Assigned Audits:</strong> ${assigned}</p>
                                <p><strong>Submitted Audits:</strong> ${submitted}</p>
                            `;

                            document.getElementById('modalContent').innerHTML = modalHtml;
                            const modal = new bootstrap.Modal(document.getElementById('auditDetailModal'));                          

                            $.ajax({
                                type: 'POST',
                                url: "{{ route('legal.audit_schedule_detail') }}",
                                data: {'currentCycleId':<?php echo $currentCycleId; ?>,'date': actualdate},
                                success: function(response) {
                                  document.getElementById('modalDetail').innerHTML = response;
                                    modal.show();
                                },
                                error: function(xhr) {
                                    console.log("error in showing audit_schedule detail");
                                }
                            });
                        }
                    }
                }
            }
        }

    });
</script>

<!-- Modal -->
<div class="modal fade" id="auditDetailModal" tabindex="-1" aria-labelledby="auditDetailLabel">
  <div class="modal-dialog modal-dialog-centered"
       style="max-width:700px;">
    <div class="modal-content"
         style="border-radius:6px;">
      
      <div class="modal-header bg-info text-white"
           style="padding:8px 12px;">
        <h5 class="modal-title"
            id="auditDetailLabel"
            style="font-size:15px;">
            Legal Audit Details
        </h5>
        <button type="button" class="btn-close"
                data-dismiss="modal"
                aria-label="Close"
                style="transform:scale(0.8);">
        </button>
      </div>

      <div class="modal-body"
           id="modalContent"
           style="padding:8px 12px; font-size:13px;">
        Loading...
      </div>

      <div class="modal-body"
           style="padding:8px 12px;">
        <div class="table-responsive"
             style="max-height:300px; overflow-y:auto;">
          <table class="table table-bordered table-sm table-striped text-center"
                 style="font-size:12px;">
            <thead>
              <tr>
                <th style="padding:6px;">Advocate Name</th>
                <th style="padding:6px;">Status</th>
              </tr>
            </thead>
            <tbody id="modalDetail">
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>


