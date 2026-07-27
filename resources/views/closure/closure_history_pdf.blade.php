<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Closure History Report</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .subparam {
            padding-left: 20px;
            font-style: italic;
            background-color: #fafafa;
        }
    </style>
</head>
<body>
    <h1>Audit Closure History Report</h1>
    <table>
        <thead>
            <tr class="header-row">
                <th colspan="2">Bank Name</th>
                <th colspan="2">{{$client_name}}</th>
            </tr>
        </thead>
        <tbody>
            <tr class="section-header">
                <th>Process Review Period</th>
                <td>{{ $process_review_month }}</td>
                <th>Product</th>
                <td>{{ $audit_details->agency_product }}</td>
            </tr>
            <tr class="section-row">
                <td>Agency Name</td>
                <td>{{ $agency_details->name }}</td>
                <td>Agency Location</td>
                <td>{{ $audit_details->agency_location }}</td>
            </tr>
           
        </tbody>
    </table>
    <h2>Closure History</h2>
    <table>
        <thead>
            <tr>
                <th>Parameter Name</th>
                <th>Approval Status</th>
                <th>Justification</th>
                <th>Action Taken</th>
                <th>Artifact</th>
            </tr>
        </thead>
        <tbody>
            @foreach($closureHistory as $artifact)
                <tr>
                    <td>{{ $artifact->parameter_name }}</td>
                    <td>{{ $artifact->approval_status }}</td>
                    <td>{{ $artifact->justification }}</td>
                    <td>{{ $artifact->action_taken }}</td>
                    <td>
                        @if($artifact->artifact)
                            <a href="{{ asset('storage/app/' . $artifact->artifact) }}" download="{{ basename($artifact->artifact) }}">
                                Download Artifact
                            </a>
                        @else
                            No artifact available
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
