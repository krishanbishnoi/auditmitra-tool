@php
  $mainParams = [];
  $drilldownSeries = [];
  // Only use unique parameters for the main chart
  foreach(array_merge($positiveParameters, $negativeParameters) as $param) {
    $mainParameterId = isset($param['parameter_ids'][0]) ? $param['parameter_ids'][0] : null;
    $mainParams[] = [
      'name' => $param['parameter_name'],
      'y' => $param['avg_score'],
      'color' => $param['avg_score'] >= 100 ? '#28a745' : '#dc3545',
      'drilldown' => $mainParameterId
    ];
    $subData = [];
    foreach($param['subparameters'] as $sub) {
      $subData[] = [
        'name' => $sub['sub_parameter_name'],
        'y' => $sub['avg_score'],
        'color' => $sub['avg_score'] >= 100 ? '#28a745' : '#dc3545'
      ];
    }
    $drilldownSeries[] = [
      'id' => $mainParameterId,
      'name' => $param['parameter_name'] . ' - Sub Parameters',
      'data' => $subData
    ];
  }
@endphp

<div style="text-align:center; margin-bottom: 10px;">
  <button id="backBtn" style="display:none; padding: 8px 16px; background-color: #007bff; color: #fff; border: none; border-radius: 5px; cursor: pointer;">
    ← Back to Main Chart
  </button>
</div>

<div id="param_wise" style="height: 400px; width: 100%;"></div>

<script>
  const chart = Highcharts.chart('param_wise', {
    chart: {
      type: 'waterfall',
      animation: true,
      events: {
        drilldown() {
          document.getElementById('backBtn').style.display = 'inline-block';
        },
        drillup() {
          document.getElementById('backBtn').style.display = 'none';
        }
      }
    },

    title: {
      text: 'Audit Parameters Score Analysis (%)',
      style: {
        fontSize: '22px'
      }
    },

    exporting: {
      enabled: true
    },

    xAxis: {
      type: 'category',
      labels: {
        style: {
          fontWeight: 'bold',
          fontSize: '13px'
        }
      }
    },

    yAxis: {
      title: {
        text: 'Impact on Score (%)'
      },
      labels: {
        format: '{value}%',
        style: {
          fontSize: '12px'
        }
      }
    },

    tooltip: {
      shared: false,
      useHTML: true,
      formatter: function() {
        return `<strong>${this.point.name}</strong><br>Impact: <b>${this.point.y}%</b>`;
      }
    },

    legend: {
      enabled: false
    },

    plotOptions: {
      series: {
        borderWidth: 0,
        dataLabels: {
          enabled: true,
          format: '{y}%',
          style: {
            fontWeight: 'bold'
          },
          inside: false,         
          overflow: 'none',      
          crop: false  
        },
        point: {
          events: {
            click: function() {
              const isLastLevel = !this.drilldown;
              const isNegative = this.y < 0;
              const isRed = this.color === '#dc3545';
              const paramName = this.name;
              if (isLastLevel && (isNegative || isRed)) {
                const modal = new bootstrap.Modal(document.getElementById('agencyModal'));
                $.ajax({
                    type: 'POST',
                    url: "{{ route('param_detail_modal_view') }}",
                    data: {'currentCycleId':<?php echo $currentCycleId; ?>,'paramName': paramName},
                    success: function(response) {
                      $("#tableContent").html(response);
                      //modal.show();
                    },
                    error: function(xhr) {
                        console.log("error in showing audit_schedule detail");
                    }
                });
                modal.show();                
              }
            }
          }
        }
      }
    },

    // Dynamically generate chart data from backend variables
    series: [
      {
        name: 'Main Parameters',
        data: {!! json_encode($mainParams) !!}
      }
    ],
    drilldown: {
      animation: true,
      series: {!! json_encode($drilldownSeries) !!}
    }
  });

  document.getElementById('backBtn').addEventListener('click', function() {
    chart.drillUp();
  });
</script>



<!-- Bootstrap Modal for Negative Score -->
<div class="modal fade" id="agencyModal" tabindex="-1" aria-labelledby="agencyModalLabel">
  <div class="modal-dialog modal-xl modal-dialog-centered"> <!-- Made modal extra large -->
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="agencyModalLabel">Negative Score Details</h5>
        <button type="button" class="btn-close btn-close-white" data-dismiss="modal" aria-label="Close"></button> <!-- Add Close button -->
      </div>
      <div class="modal-body">
        <p>This parameter/sub-parameter has a negative impact on the score. Please investigate further.</p>
        <div class="table-responsive">
          <table class="table table-bordered table-sm table-striped text-center" id="tableContent">
            <thead>
              <tr>
                <th rowspan="2">Agency Name</th>
                <th rowspan="2">Agency Code</th>
                <th rowspan="2">Location</th>
                <th rowspan="2">Repeat Issue</th>
                <th colspan="3">Three Month Trend(score)</th>                
                <th colspan="3">Action Planning</th>
              </tr>
              <tr>
                <th>May</th>
                <th>Jun</th>
                <th>Jul</th>
                <th>May</th>
                <th>Jun</th>
                <th>Jul</th>
              </tr>
            </thead>
            <tbody id="agencyTableBody">
              <tr>
                <td>Demo Agency</td>
                <td>AG123</td>
                <td>Mumbai</td>
                <td>2</td>
                <td>85%</td>
                <td>78%</td>
                <td>82%</td>
                <td><a href="{{url('storage/app/public/audit_reports/2025/May/audit_closure_1748253884.pdf')}}" target="_blank" class="text-primary" title="clouser report">
                    <i class="fas fa-eye"></i>
                  </a>
                </td>
                <td><a href="{{url('storage/app/public/audit_reports/2025/June/audit_closure_1750309420.pdf')}}" target="_blank" class="text-primary" title="clouser report">
                    <i class="fas fa-eye"></i>
                  </a>
                </td>
                <td><a href="{{url('storage/app/public/audit_reports/2025/July/audit_closure_1751888383.pdf')}}" target="_blank" class="text-primary" title="clouser report">
                    <i class="fas fa-eye"></i>
                  </a>
                </td>                
              </tr>
              <!-- Add more rows as needed -->
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer justify-content-end">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>