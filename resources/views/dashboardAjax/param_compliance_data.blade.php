
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
              <option value="all" {{ ($selectedParameterId == 'all') ? 'selected' : '' }}>Overall Parameter</option>
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
                <span id="setState">Parameter Compliance View </span>
                <i class="fa fa-info-circle text-primary ms-2"
                    data-toggle="tooltip"
                    data-placement="top"
                    title="Chart showing parameters compliance.">
                </i>
            </h6>
        </div>
        <div style="height:500px;" id="complianceTabContent">         
          
        </div>
    </div>
</div>



<script>
//new Choices('#parameterSelect', { searchEnabled: true, shouldSort: false });  

const complianceData = @json($stateCompliance);
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
  'Andhra Pradesh': 'andhra pradesh',
  'Telangana': 'telangana',
  'Madhya Pradesh': 'madhya pradesh',
  'Himachal Pradesh': 'himachal pradesh', 
  'Arunanchal Pradesh' : 'arunanchal pradesh',
  'Chandigarh' : 'chandigarh',
  'Chhattisgarh' : 'chhattisgarh',
  'Andaman and Nicobar' : 'andaman and nicobar',
  'Daman and Diu' : 'daman and diu',
  'Goa' : 'goa',
  'Jammu and Kashmir' : 'jammu and kashmir',
  'Ladakh' : 'ladakh',
  'Manipur' : 'manipur',
  'Meghalaya' : 'meghalaya',
  'Mizoram' : 'mizoram',
  'Nagaland' : 'nagaland',
  'Puducherry' : 'puducherry',
  'Sikkim' : 'sikkim',
  'Tripura' : 'tripura',
  'Uttarakhand' : 'uttarakhand',
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
  title: { text: 'Compliance Ratio by State' , style: {
                        fontSize: '14px',
                        fontWeight: 'bold'
                    } },
  colorAxis: { 
    min: 0,
    max: 1,
    stops: [
      [0, '#f10808ff'],   // 0% compliance → red
      [0.5, '#f79c0aff'], // 50% compliance → yellow
      [1, '#0be012ff']    // 100% compliance → green
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
            $("#complianceTabContent").html('');
            const stateName = this.name;
            
            $.ajax({
                    type: 'POST',
                    url: "{{ route('param_compliance_table_view') }}",
                    data: {'currentCycleId':<?php echo $currentCycleId; ?>,'stateName': stateName,'paramId': $('#parameterSelect').val(),'audit_type':"<?php echo $audit_type; ?>"},
                    success: function(response) {
                      $("#complianceTabContent").html(response);
                      $('#setState').html('Parameter Compliance View - ' + stateName);
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
    data: {'currentCycleId':<?php echo $currentCycleId; ?>,'parameter_id': parameterId,'isAjax': true,'audit_type':"<?php echo $audit_type; ?>"},
    success: function(response) {
      // Update the map chart with new data
      const newComplianceData = response.stateCompliance; // Assuming the response contains updated compliance data
      const newMapSeriesData = Object.entries(newComplianceData).map(([state, c]) => {
        const total = (c.compliant ?? 0) + (c.non ?? 0);
        const ratio = total ? c.compliant / total : 0;
        const keystate=state
    .toLowerCase()
    .replace(/\b[a-z]/g, c => c.toUpperCase());
        return {
          'hc-key': stateKeyMap[keystate],
          name: keystate,
          compliant: c.compliant,
          non: c.non,
          value: ratio
        };
      });
      mapChart.series[0].setData(newMapSeriesData);
      // Clear the compliance table content
      getComplieantAgency();
      //$("#complianceTabContent").html('');
    },
    error: function(xhr) {
      console.log("error in fetching new compliance data");
    }
  });
}

function getComplieantAgency() {
  $("#complianceTabContent").html('');
  $.ajax({
    type: 'POST',
    url: "{{ route('param_compliance_table_view') }}",
    data: {'currentCycleId':<?php echo $currentCycleId; ?>,'stateName': 'all','paramId': $('#parameterSelect').val(),'audit_type':"<?php echo $audit_type; ?>"},
    success: function(response) {
      $("#complianceTabContent").html(response);
      //modal.show();
    },
    error: function(xhr) {
        console.log("error in showing param_compliance_table_view detail");
    }
  });
}

getComplieantAgency();




</script>


