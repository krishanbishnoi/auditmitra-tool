<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Audit Closure Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .email-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
        }
        p {
            font-size: 16px;
            line-height: 1.6;
            margin: 0 0 15px;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .note {
            font-size: 14px;
            color: #555;
        }
        .signature {
            margin-top: 30px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <p>Dear {{$agency_name}},</p>
        <p>Please find attached report highlighting the unsatisfactory points identified during our process review audit conducted for the month of {{$process_review_month}}. We kindly request you to review the report and address each observation by providing appropriate closures, justification, and supporting evidence through the link below:</p>
        <p><a href="{{ $closureFormLink }}">(Click here to submit the response)</a></p>
        {{-- <p class="note">NOTE: The link will remain active for the next {{ $tat }} days. Ensure that the closures are submitted within this timeframe to avoid any escalation or compliance concerns.</p> --}}
        <p>NOTE: The link will remain active for the next {{ $tat }} days. Ensure that the closures are submitted within this timeframe to avoid any escalation or compliance concerns.</p>
        <p>We appreciate your immediate attention and cooperation.</p>
        <p class="signature">Warm Regards,<br>Audit Team <br><a style="text-decoration: none" href="https://www.qdegrees.com/">QDegrees Services</p>
    </div>
    </div>
</body>
</html>
