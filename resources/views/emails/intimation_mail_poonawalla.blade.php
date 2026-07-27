<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Assessment Intimation</title>
</head>

<body style="font-family: Arial, Helvetica, sans-serif; font-size:14px; color:#333; line-height:1.6;">

    <p>Dear Team,</p>

    <p>Greetings!</p>

    <p>
        This is to inform you that the assessment for
        <strong>{{ $agency->name }}</strong>
        has been scheduled for
        <strong>{{ date('d-M-y', strtotime($audit_date)) }}</strong>
        and will be conducted by
        <strong>{{ !empty($auditor_name_real) ? $auditor_name_real : $auditor_name }}</strong>.
    </p>

    @if (!empty($executives) && count($executives) > 0)
        <p>
            We kindly request you to ensure the availability of the following staff members during the assessment.
            <strong>Their presence is mandatory to facilitate the assessment and ensure its successful completion.</strong>
        </p>

        <table  cellpadding="0" cellspacing="0" border="1"
            style="width:80%; border-collapse:collapse; margin:10px auto 15px auto; font-size:14px">

            <thead style="background:#034ea2;">
                <tr>
                    <th style="padding: 2px 10px;color:#ffffff;">Agency Code</th>
                    <th style="padding: 2px 10px;color:#ffffff;">Agency Name</th>
                    <th style="padding: 2px 10px;color:#ffffff;">Executive Name</th>
                    <th style="padding: 2px 10px;color:#ffffff;">ICE (Unique PFL Identity for Collection Executive)</th>
                </tr>
            </thead>

            <tbody>

                @foreach ($executives as $executive)
                    <tr>
                        <td style="padding: 2px 10px; text-align:center">{{ $agency->agency_id }}</td>
                        <td style="padding: 2px 10px;">{{ $agency->name }}</td>
                        <td style="padding: 2px 10px;">{{ $executive['name'] ?? '' }}</td>
                        <td style="padding: 2px 10px;text-align:center;">{{ $executive['ice'] ?? '' }}</td>
                    </tr>
                @endforeach

            </tbody>

        </table>
    @endif

    <p>
        We would also appreciate it, if you could <strong>kindly confirm the assessment address</strong> mentioned below:
    </p>

    <p>
        <strong>Address:</strong>
        {{ $agency->address ?? ($agency->location ?? '') }}
    </p>

    <p>
        Further, we request your support in ensuring that all relevant documents, records, and systems pertaining to
        Poonawalla Fincorp activities are readily available during the assessment. This will help facilitate a smooth
        and efficient assessment process.
    </p>

    <p>
        We appreciate your cooperation and look forward to your confirmation of the assessment address.
        Should you have any queries or require any clarification, please feel free to reach out.
    </p>

    <p>
        Thank you for your continued support.
    </p>

    <br>

    <p>
        Best Regards,<br>
        <strong>QDegrees Team</strong>
    </p>

</body>

</html>
