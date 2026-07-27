<!DOCTYPE html>
<html>
<head>
    <title>Auditor Assign for Audits</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
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

   
    @if($auditorAssigns->isNotEmpty())   
    <p>Dear  {!! $auditorAssigns->first()->auditor_name !!},</p>

    @else
        Auditor
    @endif

    <br>

   
    <p>Please check the Audit Auditor Details below:</p>

    <p>An audit for XYZ Bank has been assigned to you in the portal, based on the discussion concluded with the agency.</p>

    @if($auditorAssigns->isNotEmpty())  
    <p>The audit is scheduled for  {!! $auditorAssigns->first()->audit_date !!}.
    @else
        No Date Found
    @endif

    @if($auditorAssigns->isNotEmpty())  
    <p>Please ensure your availability to audit {!! $auditorAssigns->first()->final_agency_name !!} on {!! $auditorAssigns->first()->audit_date !!} to ensure compliance as per the checklist.</p>
     @else
        No Data Found
    @endif

    <p>If you encounter any difficulties, please feel free to reach out to your respective SPOC.</p>
    <br><br><br>
    

    <p>Best regards,</p>

    <p><strong>XYZ Bank</strong></p><br><br><br>
    

    





</body>
</html>
