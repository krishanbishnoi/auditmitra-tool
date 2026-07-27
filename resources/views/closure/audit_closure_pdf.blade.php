<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Audit Observations</title>
    <style>
        @page {
            margin: 80px 8mm 50px 8mm;
        }

        body {
            font-family: Arial, sans-serif;
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
            border-spacing: 0;
            border-collapse: collapse;
            box-shadow: none !important;
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

        h1 {
            text-align: center;
            background: linear-gradient(to right, #ffcc00, #ff9966);
            padding: 10px;
            color: rgb(18, 17, 17);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        th, td {
            border: 1px solid #000;
            padding: 12px;
            text-align: left;
            font-size: 14px;
        }
        th {
            background-color:  {{$background_color}} ;
            color: white;
        }
        /* Gradient Header Row */
        .header-row {
            background: linear-gradient(to right, #ffcc00, #ff9966);
              text-align: center;
            font-weight: bold;
            font-size: 16px;
        }
        /* Product and Agency Type Section */
        .section-header {
            background-color: #dff0d8; /* Light green */
            font-weight: bold;
        }
        /* Agency Name, Location, Process Review Section */
        .section-row {
            background-color: #d9edf7; /* Light blue */
        }
        /* Alternating row colors for parameters table */
        tbody tr:nth-child(odd) {
            background-color: #f2f2f2;
        }
        tbody tr:nth-child(even) {
            background-color: #e6f7ff;
        }
        /* Hover effect on table rows */
        tbody tr:hover {
            background-color: #cceeff;
            transition: background-color 0.3s;
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

    <h1>Audit Observations</h1>

    <table>
        <tr class="header-row">
            <th colspan="2">Name of Entity</td>
            <th colspan="2">{{$client_name}}</td>
        </tr>
        <tr class="section-header">
            <th>Product</th>
            <th>{{$product_name}}</th>
            <th>Agency Type</th>
            <th>{{$sheetData->type}}</th>
        </tr>
        <tr class="section-row">
            <td>Agency Name</td>
            <td>{{ $agency_details->name }}</td>
            <td>Agency Location</td>
            <td>{{ $agency_details->location }}</td>
        </tr>
        <tr class="section-row">
            <th>Process Review Month</th>
            <td>{{ $process_review_month }}</td>
            <th>Process Review Date</th>
            <td>{{ date("d-m-Y",strtotime($audit_details->created_at)) }}</td>
            </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>SR. NO</th>
                <th>Parameter Name</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($unsetParams as $index => $param)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $param->parameter_name }}</td>
                    <td>{{ $param->remarks ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>