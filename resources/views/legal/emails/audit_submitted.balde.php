<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>
    <p>Hello Raghav,</p>

    <p>The legal audit has been <strong>successfully submitted</strong>.</p>

    <p><strong>Audit Details:</strong></p>
    <ul>
        <li><strong>Advocate:</strong> {{ $audit->advocate_name }}</li>
        <li><strong>Auditor:</strong> {{ $audit->auditor_name }}</li>
        <li><strong>Audit Date:</strong> {{ \Carbon\Carbon::parse($audit->audit_date)->format('d-m-Y') }}</li>
        <li><strong>Location:</strong> {{ $audit->location }}, {{ $audit->state }}</li>
    </ul>

    <p>The detailed audit report is attached as a PDF.</p>

    <p>Regards,<br>
    Legal Audit System</p>
</body>
</html>
