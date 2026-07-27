<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body>
    <p>Dear Advocate,</p>

    <p>
        This is to inform you that a legal audit has been scheduled as per details below:
    </p>

    <p>
        <strong>Audit Assign ID:</strong> {{ $auditAssignId }} <br>
        <strong>Audit Date:</strong> {{ \Carbon\Carbon::parse($auditDate)->format('d-m-Y') }} <br>
        <strong>Auditor Name:</strong> {{ $auditorName }}
    </p>

    <p>
        Kindly ensure your availability and keep required documents ready.
    </p>

    <p>
        Regards,<br>
        <strong>Legal Audit Team</strong>
    </p>
</body>
</html>
