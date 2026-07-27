
<div class="col-md-6 px-md-2">
    <div class="cardBox h-100">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="m-0">
                <span>Parameter Compliance View</span>
                <i class="fa fa-info-circle text-primary ms-2"
                    data-toggle="tooltip"
                    data-placement="top"
                    title="Chart showing parameters compliance.">
                </i>
            </h6>
        </div>
        <div class="mb-3 row align-items-center">
          <label for="parameterSelect" class="col-form-label col-sm-4">
            Select Parameter
          </label>
          <div class="col-sm-8">
            <select class="form-select" id="parameterSelect" name="parameter" onchange="getNewComplianceData(this.value);">
              @foreach($parameters as $param)
                <option value="{{ $param->id }}" {{ ($selectedParameterId == $param->id) ? 'selected' : '' }}>
                  {{ $param->parameter }}
                </option>
              @endforeach
            </select>
          </div>
        </div>


        <div id="india-map" style="height:500px;"></div>
    </div>
</div>

<div class="col-md-6 px-md-2">
    <div class="cardBox h-100">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="m-0">
                <span>Parameter Compliance View</span>
                <i class="fa fa-info-circle text-primary ms-2"
                    data-toggle="tooltip"
                    data-placement="top"
                    title="Chart showing parameters compliance.">
                </i>
            </h6>
        </div>
        <div id="state-bar" style="height:500px;">
          <ul class="nav nav-tabs" id="complianceTab" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="compliant-tab" data-bs-toggle="tab" data-bs-target="#compliant" type="button" role="tab" aria-controls="compliant" aria-selected="true">
                Compliant
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="noncompliant-tab" data-bs-toggle="tab" data-bs-target="#noncompliant" type="button" role="tab" aria-controls="noncompliant" aria-selected="false">
                Non-Compliant
              </button>
            </li>
          </ul>
          <div class="tab-content mt-3" id="complianceTabContent">
            
          </div>
        </div>
    </div>
</div>



<script>
  const complianceData = @json($stateCompliance);
// const complianceData = {
//   'Maharashtra': { compliant: 34, non: 1 },
//   'Gujarat': { compliant: 86, non: 10 },
//   'Karnataka': { compliant: 23, non: 15 },
//   'Delhi': { compliant: 48, non: 5 },
//   'Punjab': { compliant: 71, non: 17 },
//   'Bihar': { compliant: 56, non: 13 },
//   'Odisha': { compliant: 23, non: 6 },
//   'Assam': { compliant: 10, non: 72 },
//   'Jharkhand': { compliant: 48, non: 8 },
//   'Chhattisgarh': { compliant: 56, non: 13 }
// };

const stateKeyMap = {
  'Maharashtra': 'maharashtra',
  'Gujarat': 'gujarat',
  'Karnataka': 'karnataka',
  'Delhi': 'nct of delhi',
  'Punjab': 'punjab',
  'Bihar': 'bihar',
  'Odisha': 'odisha',
  'Assam': 'assam',
  'Jharkhand': 'jharkhand',
  'Rajasthan': 'rajasthan',
  'Uttar Pradesh': 'uttar pradesh',
  'Haryana': 'haryana',
  'West Bengal': 'west bengal',
  'Tamil Nadu': 'tamil nadu',
  'Kerala': 'kerala',
  'Andhra Pradesh': 'Andhra pradesh',
  'Telangana': 'telangana',
  'Madhya Pradesh': 'madhya pradesh',
  'Himachal Pradesh': 'himachal pradesh',  
};

const mapData = Highcharts.maps['countries/in/custom/in-all-disputed'];

const mapSeriesData = Object.entries(complianceData).map(([state, c]) => {
  const total = (c.compliant ?? 0) + (c.non ?? 0);
  const ratio = total ? c.compliant / total : 0;
  return {
    'hc-key': stateKeyMap[state],
    name: state,
    compliant: c.compliant,
    non: c.non,
    value: ratio
  };
});

// === MAP CHART ===
const mapChart = Highcharts.mapChart('india-map', {
  chart: { map: mapData },
  title: { text: 'Compliance Ratio by State' },
  colorAxis: { 
    min: 0,
    max: 1,
    stops: [
      [0, '#ff4d4d'],   // 0% compliance → red
      [0.5, '#ffff66'], // 50% compliance → yellow
      [1, '#4caf50']    // 100% compliance → green
    ]
},
  tooltip: {
    useHTML: true,
    formatter: function () {
      const p = this.point;
      const pct = (p.value * 100).toFixed(1);
      return `<b>${p.name}</b><br>
              ✅ Compliant: ${p.compliant}<br>
              ❌ Non-Compliant: ${p.non}<br>
              Compliance: ${pct}%`;
    }
  },
  plotOptions: {
    series: {
      allowPointSelect: true,
      point: {
        events: {
          click: function () {
            const stateName = this.name;
            $.ajax({
                    type: 'POST',
                    url: "{{ route('param_compliance_table_view') }}",
                    data: {'currentCycleId':<?php echo $currentCycleId; ?>,'stateName': stateName,'parameterId': $('#parameterSelect').val()},
                    success: function(response) {
                      $("#complianceTabContent").html(response);
                      //modal.show();
                    },
                    error: function(xhr) {
                        console.log("error in showing param_compliance_table_view detail");
                    }
  });
          }
        }
      }
    }
  },
  series: [{
    data: mapSeriesData,
    mapData: mapData,
    joinBy: 'hc-key',
    borderColor: '#333',
    states: { hover: { color: '#a4edba' } }
  }]
});


function getNewComplianceData(parameterId) {
  $.ajax({
    type: 'POST',
    url: "{{ route('param_compliance_data') }}",
    data: {'currentCycleId':<?php echo $currentCycleId; ?>,'parameter_id': parameterId,'isAjax': true},
    success: function(response) {
      // Update the map chart with new data
      const newComplianceData = response.stateCompliance; // Assuming the response contains updated compliance data
      const newMapSeriesData = Object.entries(newComplianceData).map(([state, c]) => {
        const total = (c.compliant ?? 0) + (c.non ?? 0);
        const ratio = total ? c.compliant / total : 0;
        return {
          'hc-key': stateKeyMap[state],
          name: state,
          compliant: c.compliant,
          non: c.non,
          value: ratio
        };
      });
      mapChart.series[0].setData(newMapSeriesData);
      // Clear the compliance table content
      $("#complianceTabContent").html('');
    },
    error: function(xhr) {
      console.log("error in fetching new compliance data");
    }
  });
}


</script>


