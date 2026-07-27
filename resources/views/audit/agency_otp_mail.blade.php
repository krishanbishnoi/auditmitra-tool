<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification and Audit Check Sheet</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap');
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f4f7;
            margin: 0;
            padding: 0;
            color: #51545e;
        }

        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .email-header {
            background-color: {{$client_color}};
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }

        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 500;
        }

        .email-body {
            padding: 30px;
        }

        .email-body p {
            font-size: 16px;
            line-height: 1.6;
            margin: 0 0 15px;
        }

        .otp {
            display: inline-block;
            background-color: #f0f0f5;
            color: #333333;
            padding: 10px 15px;
            font-size: 22px;
            letter-spacing: 4px;
            border-radius: 5px;
            margin-top: 10px;
        }

        .email-footer {
            padding: 20px;
            text-align: center;
            background-color: #f4f4f7;
            color: #888888;
            font-size: 14px;
        }

        .email-footer p {
            margin: 5px 0;
        }

        @media (max-width: 600px) {
            .email-container {
                width: 100%;
                padding: 0 15px;
            }

            .email-body {
                padding: 20px;
            }

            .email-header h1 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Email Header -->
        <div class="email-header">
            <h1>OTP Verification & 
            {{ $clientId == 285 ? 'Assessment' : 'Audit' }}
             Check Sheet</h1>
        </div>

        <!-- Email Body -->
        <div class="email-body">
            <p>Dear Agency,</p>
            <p>Please find attached the concern sheet for the {{ $clientId == 285 ? 'assessment' : 'audit' }} conducted at <strong>{{ $agency_details->name }}</strong> on <strong>{{date("d-m-Y", strtotime($audit_date))}}</strong> at <strong>{{ $agency_details->location }}</strong>. We kindly request you to review the document and validate the same by sharing the OTP received herewith to the {{ $clientId == 285 ? 'assessment officer' : 'auditor' }} as an acceptance of the concern sheet.</p>

            <p class="otp">{{ $otp }}</p>

            <p><strong>Important:</strong> In case there are any errors or rectifications required in the concern sheet, please do not share the OTP with the {{ $clientId == 285 ? 'assessment officer' : 'auditor' }}. Instead, seek clarification from the {{ $clientId == 285 ? 'assessment officer' : 'auditor' }} before proceeding.</p>

            <p>Please Note: Once the OTP is shared, the concern sheet will be considered final, and no further changes or amendments will be made.</p>

            <p>Thank you for your cooperation.</p>

            <p>Warm Regards,<br>The {{ $clientId == 285 ? 'Assessment' : 'Audit' }} Team <br><a href="https://www.qdegrees.com/">QDegrees Services</a></p>
        </div>

        <!-- Email Footer -->
        {{-- <div class="email-footer">
            <p>{{$client_name}} Team</p>
            <p>&copy; {{ date('Y') }} {{$client_name}}. All rights reserved.</p>
        </div> --}}
    </div>
</body>
</html>
