<div class="tab-pane fade show active" id="compliant" role="tabpanel" aria-labelledby="compliant-tab">
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>Agency</th>
                <th>Code</th>
                <th>Score</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($compliantAgencies as $data)
            <tr>
                <td>{{$data['final_agency_name']}}</td>
                <td>{{$data['agency_code']}}</td>
                <td>{{$data['compliance_score']}}</td>
                <td>{{$data['created_at']}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="tab-pane fade" id="noncompliant" role="tabpanel" aria-labelledby="noncompliant-tab">
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>Agency</th>
                <th>Code</th>
                <th>Score</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($nonCompliantAgencies as $data)
            <tr>
                <td>{{$data['final_agency_name']}}</td>
                <td>{{$data['agency_code']}}</td>
                <td>{{$data['compliance_score']}}</td>
                <td>{{$data['created_at']}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>