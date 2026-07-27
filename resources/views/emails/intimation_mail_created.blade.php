<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <style>
        body {
            font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
            background-color: #f1f1f1;
            padding: 10px 0;
            margin: 0;
            font-size: 14px;
        }

        table {
            background-color: #fff;
            width: 100%;
            margin: auto;
            padding: 0 30px;
            border-spacing: 0;
            border-collapse: collapse;
        }

        .content {
            line-height: 1.6;
            font-size: 16px;
        }

        .content b {
            font-weight: 600;
        }

        .header {
            padding: 0 0 10px;
        }

        .section {
            padding: 20px 0 0;
        }

        .list-section {
            padding: 20px 0 0;
            line-height: 1.8;
        }

        .footer {
            padding: 25px 0 0;
            line-height: 24px;
        }

    </style>
</head>

<body>
    <table align="center">
        <tr>
            <td>
                <table align="center">
                    <tr>
                        <td class="header">
                            <b>Dear {{$agency->name}},</b>
                        </td>
                    </tr>
                    <tr>
                        <td class="content">
                            I hope this message finds you well.
                        </td>
                    </tr>
                    <tr>
                    @php
                        $formatted_audit_date = \Carbon\Carbon::parse($audit_date)->format('d-m-Y'); // Format to dd-mm-yyyy
                    @endphp

                    <td class="section">
                        This is to formally inform you that an audit will be conducted for the process review period <b>{{ $process_review_month }}</b>. This audit is scheduled for the <b>{{ $formatted_audit_date }}</b> and will encompass the following areas:
                    </td>

                    </tr>
                    <tr>
                        <td class="list-section">
                            <ol>
                                <li>Agency Management</li>
                                <li>Process Management</li>
                                <li>Information Security (INFOSEC) Management</li>
                                <li>Tele Calling Management</li>
                                <li>Cash Risk Management</li>
                            </ol>
                        </td>
                    </tr>
                    @if (!empty($description))
                    <tr>
                        <td class="section">
                            <b>Note: {{ $description }} </b>
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <td class="section">
                            We kindly request you to keep all relevant documents and records readily available to ensure a smooth and efficient audit process. Should you require any clarification or additional information, please feel free to reach out to us at:
                        </td>
                    </tr>
                    <tr>
                        <td class="footer">
                            Email ID – {{Auth::user()->email}}<br>
                            Contact No – {{Auth::user()->mobile}}<br><br>
                            Warm Regards,<br>
                            <b>{{Auth::user()->name}}</b>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
