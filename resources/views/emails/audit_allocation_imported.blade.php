<!DOCTYPE html>
<html>
<head>
    <title>Audit Allocation</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    


    <p>Dear Team,</p><br>


    <p>The audit allocation for 
        @if($auditAllocations->isNotEmpty())
            <strong>{!! $auditAllocations->first()->process_review_period !!}</strong> has been uploaded to your respective agency portal.
        @else
            No audit allocations found for this period.
        @endif
    </p>

    <p>Please begin assigning audits to your auditors and ensure the completion of the scope.</p><br>



    <p>Best regards,</p>

    <p><strong>XYZ Bank</strong></p><br><br><br>




</body>
</html>

