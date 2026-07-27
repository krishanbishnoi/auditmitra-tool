<!DOCTYPE html>
<html>
<head>
    <title>{{$agency_details->name}} Audit Checksheet</title>
    <style>
        /* Page setup for PDF */
        @page {
            size: A4 landscape;
            margin: 20mm 15mm 20mm 15mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
            color: #333;
            margin: 0;
            line-height: 1.5;
        }
        h1, h2 {
            color: #1a3e72;
            margin-bottom: 15px;
            page-break-after: avoid;
        }
        /* Main table styling */
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px; /* vertical spacing between rows */
            margin-bottom: 20px;
            table-layout: fixed;
            word-wrap: break-word;
            page-break-inside: avoid;
        }
        /* Header rows */
        thead tr {
            background-color: #1a3e72;
            color: white;
            font-weight: 600;
            font-size: 15px;
            text-transform: uppercase;
            page-break-inside: avoid;
        }
        /* Header cells with rounded corners */
        thead th:first-child {
            border-top-left-radius: 8px;
            padding: 12px 15px;
        }
        thead th:last-child {
            border-top-right-radius: 8px;
            padding: 12px 15px;
        }
        /* Section rows */
        tbody tr {
            background: #f9fafd;
        }
        tbody tr:nth-child(even) {
            background: white;
        }
        tbody th, tbody td {
            padding: 10px 15px;
            border: 1px solid #ccc;
            vertical-align: middle;
            text-align: left;
            word-break: break-word;
            white-space: normal;
        }
        tbody th {
            font-weight: 600;
            width: 25%;
            background: transparent; /* transparent to keep alternating row colors */
        }
        /* Parameter row styling */
        tbody tr.parameter-row td {
            background-color: #dde5f0 !important;
            color: #1a3e72 !important;
            font-weight: 700 !important;
            font-size: 14px !important;
            padding-left: 15px !important;
            text-transform: uppercase !important;
            border: 1px solid #666 !important;
        }
        /* Subparameter italic styling */
        .subparam {
            font-style: italic;
            color: #444;
            padding-left: 15px;
            white-space: normal;
        }
        /* Numeric cells centered */
        td.numeric {
            text-align: center;
            width: 9%;
        }
        td.text {
            text-align: center;
            width: 13%;
        }
        /* Adjust widths for specific columns */
        thead th:nth-child(1), tbody td:nth-child(1) {
            width: 30%;
        }
        thead th:nth-child(6), tbody td:nth-child(6) {
            width: 30%;
        }
        /* Remarks column */
        td[data-label="Remarks"] {
            max-width: 250px;
            white-space: normal;
            word-break: break-word;
            color: #222;
            hyphens: auto;
        }
        /* Prevent row breaks inside page */
        tr {
            page-break-inside: avoid;
        }
        /* Prevent orphaned table headers */
        thead {
            display: table-header-group;
        }
    </style>
</head>
<body>
<h1>Process Review Audit Checksheet</h1>

<!-- Agency Details Table -->
<table>
    <thead>
        <tr>
            <th colspan="2">Bank Name</th>
            <th colspan="2">{{$client_name}}</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th>Process Review Period</th>
            <td>{{ $audit_cycle }}</td>
            <th>Process Review Agency</th>
            <td>{{$audit_agency_name}}</td>
        </tr>
        <tr>
            <th>Agency Name</th>
            <td>{{ $agency_details->name }}</td>
            <th>Agency Location</th>
            <td>{{ $agency_details->location }}</td>
        </tr>
        <tr>
            <th>Product</th>
            <td>{{ $product_details->name }}</td>
            <th>Audit Date</th>
            <td>{{date("d-m-Y", strtotime($audit_date))}}</td>
        </tr>
        <tr>
            <th>Score</th>
            <td>{{ $score }}</td>
            <th>Score Percentage</th>
            <td>{{$score_percentage}}</td>
        </tr>
        <tr>
            <th>Audit Type</th>
            <td>{{ $audit_type }}</td>
            <th>Auditor</th>
            <td>{{ $present_auditor}}</td>
        </tr>
        <tr>
            <th>Agency Address</th>
            <td colspan="3">{{ $agency_details->address }}</td>
        </tr>
    </tbody>
</table>

<h2>Parameters</h2>

<!-- Parameters Table -->
<table>
    <thead>
        <tr>
            <th>Sub-Parameter</th>
            <th class="text">Allocated Score</th>
            <th class="numeric">Severity</th>
            <th class="text">Status</th>
            <th class="numeric">Scored</th>
            <th>Remarks</th>
        </tr>
    </thead>
    <tbody>
        @foreach($parameters as $param)
            <tr class="parameter-row">
                <td colspan="6">{{ $param['name'] }}</td>
            </tr>
            @foreach($param['subparameters'] as $subparam)
            <tr>
                <td class="subparam" data-label="Sub-Parameter">{{ $subparam['name'] }}</td>
                <td class="text" data-label="Allocated Score">{{ $subparam['weight'] }}</td>
                <td class="numeric" data-label="Severity">{{ $subparam['severity'] }}</td>
                <td class="text" data-label="Status">{{ $subparam['status'] }}</td>
                <td class="numeric" data-label="Scored">{{ $subparam['score'] }}</td>
                <td data-label="Remarks">{{ $subparam['remarks'] }}</td>
            </tr>
            @endforeach
        @endforeach
    </tbody>
</table>

</body>
</html>
