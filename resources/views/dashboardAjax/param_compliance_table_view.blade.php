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
<div class="tab-content mt-3">
    <div class="table-responsive tbleDiv tab-pane fade show active" id="compliant" role="tabpanel" aria-labelledby="compliant-tab" style="overflow-y: auto; height:500px;">
        <table class="table table-bordered align-middle text-center shadow-sm rounded" style="font-size: 16px;">
            <thead class="table-light">
                <tr>
                    <th><?php if($audit_type != 'all') { echo $audit_type;} else {echo "Audit";} ?></th>
                    <th>Location</th>                    
                    <th>Compliance Count</th>
                </tr>
            </thead>
            <tbody>
                @foreach($compliantAgencies as $data)
                <tr>
                    <td><a target="_blank" href="{{url('storage/app/public/').'/'.$data['checksheet_pdf']}}">{{$data['final_agency_name']}}</a></td>
                    <td>{{$data['location']}}</td>
                    <td><span style="color:green;">{{$data['compliance_count']}}</span>/<span>{{$data['total']}}</span></td>                   
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="tab-pane fade table-responsive tbleDiv" id="noncompliant" role="tabpanel" aria-labelledby="noncompliant-tab" style="overflow-y: auto; height:500px;">
        <table class="table table-bordered align-middle text-center shadow-sm rounded" style="font-size: 16px;">
            <thead class="table-light">
                <tr>
                    <th><?php if($audit_type != 'all') { echo $audit_type;} else {echo "Audit";} ?></th>
                    <th>Location</th>
                    <th>Non Compliance Count</th>
                </tr>
            </thead>
            <tbody>
                @foreach($nonCompliantAgencies as $data)
                <tr>
                    <td><a target="_blank" href="{{url('storage/app/public/').'/'.$data['checksheet_pdf']}}">{{$data['final_agency_name']}}</a></td>
                    <td>{{$data['location']}}</td>
                    <td><span style="color:red;">{{$data['nonCompliance_count']}}</span>/<span>{{$data['total']}}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

