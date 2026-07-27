<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Audit Intimation</title>
</head>

<body
    style="margin:0; padding:0; background-color:#f1f1f1; font-family:'Segoe UI', Arial, Helvetica, sans-serif; font-size:15px; line-height:1.6; color:#333333;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
        style="background-color:#f1f1f1; padding:20px 0;">
        <tr>
            <td align="center">
                <!-- Main Container -->
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                    style="max-width:700px; background-color:#ffffff; border-radius:6px; padding:25px 30px;">
                    <tr>
                        <td style="font-size:15px; line-height:1.8; text-align:justify;">

                            <p style="margin:0 0 10px 0;"><b>Dear {{ $agency->name }},</b></p>

                            <p style="margin:0 0 10px 0;">I hope this message finds you well.</p>

                            @php
                                $formatted_audit_date = \Carbon\Carbon::parse($audit_date)->format('d-m-Y');
                            @endphp

                            <p style="margin:0 0 10px 0;">
                                This is to formally inform you that a <b>{{ $mode }} audit</b> has been
                                scheduled to review the processes for the period of <b>{{ $process_review_month }}</b>.
                            </p>
                            <p style="margin:0 0 10px 0;">
                                @if ($mode === 'virtual')
                                    The audit will be conducted on <b>{{ $formatted_audit_date }}</b> via MS Teams.
                                @elseif ($mode === 'physical')
                                    The audit will be conducted on <b>{{ $formatted_audit_date }}</b> at your agency
                                    premises.
                                @else
                                    This audit is scheduled for <b>{{ $formatted_audit_date }}</b>.
                                @endif
                                Please find the attached checksheet for your reference.
                            <p>
                            <p style="margin:15px 0 5px 0;">The scope of the audit will include the following areas:
                            </p>

                            <ul style="padding-left:20px; margin:0 0 15px 0;">
                                <li>Agency & Agent Level with Training</li>
                                <li>Fire Safety</li>
                                <li>CCTV & Call Recordings</li>
                                <li>Data Security & Documentation</li>
                                <li>Escalations & Power Backup</li>
                            </ul>

                            @if (!empty($description))
                                <p style="margin:10px 0; font-weight:bold;">Note: {{ $description }}</p>
                            @endif
                            <p style="margin:10px 0; font-weight:bold;">You are requested to share the following
                                documents at joicy.kunjuman@qdegrees.com</p>

                            <ol style="padding-left:20px; margin:0 0 15px 0;">
                                <li>All-in-one Undertaking.</li>
                                <li>NDC</li>
                                <li>Agent details tracking sheet - tracking sheet must be submitted in the prescribed
                                    attached format.</li>
                            </ol>

                            <p style="margin:15px 0 10px 0;">Please feel free to contact us for any query or support
                                required.</p>

                            <p style="margin:0 0 15px 0;">Thank you for your attention and anticipated cooperation.</p>

                            <p style="margin:20px 0 0 0;">Warm Regards, <br><b style="color:#008080;">Joicy Kunjumon</b><br>
                            <b style="color:#008080;">QDegrees Audit Team</b><br><b
                                    style="color:#008080;">9352496904</b></p>

                        </td>
                    </tr>
                </table>

                <!-- Footer -->
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                    style="max-width:700px; margin-top:15px;">
                    <tr>
                        <td align="center" style="font-size:12px; color:#777;">© {{ date('Y') }} QDegrees. All
                            rights reserved.</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
