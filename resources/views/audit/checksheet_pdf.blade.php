<!DOCTYPE html>
<html>
<head>
    <title>{{$agency_details->name}} Audit Checksheet</title>
    <style>
        @page {
            margin: 80px 5mm 50px 5mm;
        }

        body {
            margin: 0;
            padding: 0;
        }

        /* PDF Header Styling */
        .pdf-header {
            position: fixed;
            top: -70px;
            left: 0;
            right: 0;
            height: 60px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
        }
        
        .pdf-header table {
            width: 100%;
            border: none !important;
            margin: 0;
            border-collapse: collapse;
        }
        
        .pdf-header td {
            border: none !important;
            padding: 0;
        }

        /* PDF Footer Styling */
        .pdf-footer {
            position: fixed;
            bottom: -40px;
            left: 0;
            right: 0;
            height: 30px;
            border-top: 1px solid #ccc;
            padding-top: 5px;
            font-size: 10px;
            display: table;
            width: 100%;
        }
        
        .footer-left {
            display: table-cell;
            text-align: left;
            padding-left: 10px;
        }
        
        .footer-right {
            display: table-cell;
            text-align: right;
            padding-right: 10px;
        }

        /* General table styling */
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

        /* Responsive design for smaller screens */
        @media screen and (max-width: 600px) {
            table, thead, tbody, th, td, tr {
                display: block;
            }

            /* Hide headers on mobile */
            thead tr {
                display: none;
            }

            tr {
                margin-bottom: 10px;
                border: 1px solid #ccc;
            }

            td {
                border: none;
                position: relative;
                padding-left: 50%;
            }

            /* Add labels for each row */
            td:before {
                content: attr(data-label);
                position: absolute;
                left: 10px;
                top: 10px;
                white-space: nowrap;
                font-weight: bold;
            }
        }

        /* Additional styling */
        .info-section {
            background-color: #eaf7ea;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            line-height: 1.6;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .info-section p {
            margin: 5px 0;
        }
    </style>
</head>
<body>

<!-- PDF Header -->
<div class="pdf-header">
    <table>
        <tr>
            <td style="width: 50%; text-align: left;">
                <img src="https://auditmitr.qdegrees.com/public/images/qdegrees.png" width="150">
            </td>
            <td style="width: 50%; text-align: right;">
                <img src="https://auditmitr.qdegrees.com/public/images/app_logo.png" width="150">
            </td>
        </tr>
    </table>
</div>

<!-- PDF Footer -->
<div class="pdf-footer">
        <div class="footer-left">
            Confidential
        </div>
        <div class="footer-right">
           © {{ date('Y') }} QDegrees Services. All rights reserved
        </div>
    </div>

<h1>Process Review Audit Checksheet</h1>
<table>
    <thead>
        <tr class="header-row">
            <th colspan="2">Entity Name</th>
            <th colspan="2">{{$client_name}}</th>
        </tr>
    </thead>
    <tbody>
        <tr class="section-header">
            <th>Process Review Period</th>
            <td>{{ $audit_cycle }}</td>
            <th>Process Review Agency</th>
            <td>{{$audit_agency_name}}</td>
        </tr>
        <tr class="section-row">
            <td>Agency Name</td>
            <td>{{ $agency_details->name }}</td>
            <td>Agency Location</td>
            <td>{{ $agency_details->location }}</td>
        </tr>
        <tr class="section-row">
            <th>Product</th>
            <td>{{ $product_details->name }}</td>
            <th>Audit Date</th>
            <td>{{date("d-m-Y", strtotime($audit_date))}}</td>
        </tr>
    </tbody>
</table>

    <h2>Parameters</h2>
    <table> 
        <thead>
            <tr>
                <th>Parameter</th>
                <th>Sub-Parameter</th>
                <th>Status</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($parameters as $param)
                <tr>
                    <td data-label="Parameter">{{ $param['name'] }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                @foreach($param['subparameters'] as $subparam)
                <tr>
                    <td></td>
                    <td class="subparam" data-label="Sub-Parameter">{{ $subparam['name'] }}</td>
                    <td data-label="Status">{{ $subparam['status'] }}</td>
                    <td data-label="Remarks">{{ $subparam['remarks'] }}</td>
                </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
    
    <!-- <p><strong>Overall Score:</strong> {{ $score }}</p> -->
</body>
</html>