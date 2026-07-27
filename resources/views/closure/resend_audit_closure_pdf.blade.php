<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Audit Observations</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        h1 {
            text-align: center;
            background: linear-gradient(to right, #ffcc00, #ff9966);
            padding: 10px;
            color: white;
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
            background-color: #4CAF50;
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
    <h1>Audit Observations</h1>

    <table>
        <tr class="header-row">
            <th colspan="2">Name of NBFC</td>
            <th colspan="2">{{$client_name}}</td>
        </tr>
        <tr class="section-header">
            <th>Product</th>
            <th>{{ $product }}</th>
            <th>Agency Type</th>
            <th>Agency</th>
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
                <th>Action Taken By Agency</th>
                <th>Rejection Reason</th>
            </tr>
        </thead>
        <tbody>
            @foreach($unsetParams as $index => $param)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $param->parameter_name }}</td>
                    <td>{{ $param->action_taken }}</td>
                   
                    <td>{{ $param->rejection_reason ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
