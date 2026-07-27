<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Report</title>
    <style>
        /* ✅ Page setup for A3 Landscape */
        @page {
            size: A3 landscape; /* Landscape A3 */
            margin: 80px 8mm 50px 8mm;
        }

        /* Base styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #222;
            font-size: 9px;
            line-height: 1.3;
            width: 100%;
            max-width: 420mm; /* Width of A3 Landscape */
            box-sizing: border-box;
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
            border-spacing: 0;
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

        h2 {
            text-align: center;
            color: #2c3e50;
            font-size: 14px;
            margin: 8px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
        }

        th,
        td {
            border: 1px solid #555;
            padding: 4px 6px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        /* Section titles */
        .section-title {
            background-color: #e9eef4;
            font-weight: bold;
            padding: 5px;
            font-size: 10px;
            border: 1px solid #bbb;
            text-transform: uppercase;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .section-header {
            background-color: #e9eef4;
            font-weight: bold;
            font-size: 15px;
            border: 1px solid #bbb;
            text-transform: uppercase;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Dual column layout */
        .two-column {
            width: 100%;
            margin-left: 20px;
            margin-bottom: 10px;
            border: none;
            margin-top: 5px;
        }

        .two-column td {
            width: 50%;
            border: none;
            vertical-align: top;
            padding: 0;
        }

        .info-table {
            width: 90%;
            border-collapse: collapse;
        }

        .info-table td {
            border: 1px solid #777;
            padding: 4px 6px;
        }

        .footer {
            margin-top: 10px;
            font-size: 8px;
            text-align: center;
            border-top: 1px solid #999;
            padding-top: 5px;
            color: #555;
        }

        /* Prevent page breaks inside tables */
        table,
        tr,
        td,
        th {
            page-break-inside: avoid;
        }

        @media print {
            html,
            body {
                width: 100%;
                height: 100%;
                margin: 0;
                padding: 0;
            }
        }

    </style>
</head>

<body>

    <!-- PDF Header -->
    <div class="pdf-header">
    <table style="width:100%;">
        <tr>
            <td style="width: 50%; text-align: left; vertical-align: middle;">
                <img src="https://auditmitr.qdegrees.com/public/images/qdegrees.png" width="150" style="display:block;">
            </td>
            <td style="width: 50%; text-align: right; vertical-align: middle;">
                <img src="https://auditmitr.qdegrees.com/public/images/app_logo.png" width="150" style="display:block;">
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

    <div class="section-header" style="
        display: flex; 
        align-items: center; 
        justify-content: center; 
        position: relative; 
        margin-bottom: 10px;
    ">
        <img src="{{ asset('public/fibelogo.png') }}" 
             alt="fibe logo" 
             style="height: 30px; width: 30px; position: absolute; left: 10px; padding: 5px;">
        <h2 style="margin: 0; padding: 10px;">Audit Report Summary</h2>
    </div>

    <!-- ✅ Combined Summary + Vendor Information Table -->
    <div class="section-title">Summary & Vendor Information</div>
    <table class="two-column">
        <tr>
            <!-- Left: Vendor Info -->
            <td>
                <table class="info-table">
                    <tr><td><strong>Vendor Name:</strong> {{ $agency_details->name ?? 'NA' }}</td></tr>
                    <tr><td><strong>Name of the Agency Owner:</strong> {{ $agency_manager ?? 'NA' }}</td></tr>
                    <tr><td><strong>Location:</strong> {{ $agency_details->location ?? 'NA' }}</td></tr>
                    <tr><td><strong>Review Month:</strong> {{ $audit_cycle ?? 'NA' }}</td></tr>
                    <tr><td><strong>Date of Audit:</strong> 
                        {{ isset($audit_date) ? date('d-m-Y', strtotime($audit_date)) : 'NA' }}</td></tr>
                    <tr><td ><strong>Address of the Agency:</strong> {{ $agency_details->address ?? 'NA' }}</td></tr>
                </table>
            </td>

            <!-- Right: Summary Info -->
            <td>
                <table class="info-table">
                    <tr><td><strong>Audit Score:</strong> {{ $score_percentage ?? 'NA' }}</td></tr>
                    <tr><td><strong>Total Zero Tolerance:</strong> {{ $zeroToleranceUnsetParameters ?? 'NA' }}</td></tr>
                    <tr><td><strong>Collection Manager:</strong> {{ $cm ?? 'NA' }}</td></tr>
                    <tr><td><strong>RCM Name:</strong> {{ $rcm ?? 'NA' }}</td></tr>
                    <tr><td><strong>Audit By:</strong> {{ $present_auditor ?? 'NA' }}</td></tr>
                    <tr><td><strong>Mode of Audit:</strong> {{ ucfirst($audit_type ?? 'NA') }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- ✅ Audit Parameters Section -->
    <div class="section-title">Audit Parameters</div>
    <table>
        <thead>
            <tr>
                <th>Category</th>
                <th>Parameter</th>
                <th>Allotted Score</th>
                <th>Severity</th>
                <th>Response</th>
                <th>Score Obtained</th>
                <th>Audit Observation</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($parameters as $param)
                @foreach ($param['subparameters'] as $subparam)
                    <tr>
                        <td>{{ $param['name'] ?? '' }}</td>
                        <td>{{ $subparam['name'] ?? '' }}</td>
                        <td>{{ $subparam['weight'] ?? '' }}</td>
                        <td>{{ $subparam['severity'] ?? '' }}</td>
                        <td>
                            {{ $subparam['status'] == 'Satisfactory' ? 'Yes' : ($subparam['status'] == 'Unsatisfactory' ? 'No' : $subparam['status']) }}
                        </td>
                        <td>{{ $subparam['score'] ?? '' }}</td>
                        <td>{{ $subparam['remarks'] ?? '' }}</td>
                        <td>{{ $subparam['status'] == 'Unsatisfactory' ? 'Open' : 'Closed' }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

</body>
</html>