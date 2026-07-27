<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Audit Intimation - Credit Saison</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body
    style="margin:0; padding:0; background-color:#f4f6f8; ont-family:'Segoe UI', Arial, Helvetica, sans-serif; color:#333;">
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
        style="background-color:#f4f6f8; padding:25px 10px;">
        <tr>
            <td align="center">
                <table cellpadding="0" cellspacing="0" border="0" width="600"
                    style="max-width:600px; background-color:#ffffff; border-radius:8px; padding:25px; box-shadow:0 2px 10px rgba(0,0,0,0.05);">
                    <tr>
                        <td style="font-size:15px; line-height:1.6;">
                            <p>Dear Team,</p>
                            <p>Greetings!</p>
                            <p>
                                We would like to inform you that an upcoming <strong>audit of the collection process
                                    compliance</strong>
                                has been scheduled for your agency. The objective of this audit is to ensure that all
                                collection activities are being conducted in strict adherence to company policies,
                                internal guidelines, and all applicable laws and regulations
                            </p>

                            <p>
                                At <strong>Credit Saison</strong>,it is our constant endeavor to ensure continuous
                                improvement, operational efficiency, and compliance within our collection processes.
                                This audit is a part of our ongoing efforts to maintain the highest standards of
                                governance and operational integrity.
                            </p>


                            <h2
                                style="color:#2b1579; border-bottom:2px solid #00723f; display:inline-block; margin:20px 0 10px;">
                                Audit Details</h2>

                            <table cellpadding="0" cellspacing="0" border="0" width="100%"
                                style="border-collapse:collapse; font-size:15px;">
                                <tr>
                                    <th align="left" width="40%"
                                        style="background:#f2f2f2; padding:8px 10px; border:1px solid #ddd;">Agency Name
                                    </th>
                                    <td style="background:#fafafa; padding:8px 10px; border:1px solid #ddd;">
                                        {{ ucwords(strtolower($agency->name)) }}</td>
                                </tr>
                                <tr>
                                    <th align="left" width="40%"
                                        style="background:#f2f2f2; padding:8px 10px; border:1px solid #ddd;">Vendor Code
                                    </th>
                                    <td style="background:#fafafa; padding:8px 10px; border:1px solid #ddd;">
                                        {{ ucwords(strtolower($agency->agency_id)) }}</td>
                                </tr>
                                <tr>
                                    <th align="left"
                                        style="background:#f2f2f2; padding:8px 10px; border:1px solid #ddd;">Location
                                    </th>
                                    <td style="background:#fafafa; padding:8px 10px; border:1px solid #ddd;">
                                        {{ ucwords(strtolower($agency->location)) }}</td>
                                </tr>
                                <tr>
                                    <th align="left"
                                        style="background:#f2f2f2; padding:8px 10px; border:1px solid #ddd;">State</th>
                                    <td style="background:#fafafa; padding:8px 10px; border:1px solid #ddd;">
                                        {{ ucwords(strtolower($state)) }}</td>
                                </tr>
                                <tr>
                                    <th align="left"
                                        style="background:#f2f2f2; padding:8px 10px; border:1px solid #ddd;">Zone</th>
                                    <td style="background:#fafafa; padding:8px 10px; border:1px solid #ddd;">
                                        {{ ucwords(strtolower($zone)) }}</td>
                                </tr>
                                <tr>
                                    <th align="left"
                                        style="background:#f2f2f2; padding:8px 10px; border:1px solid #ddd;">Client SPOC
                                    </th>
                                    <td style="background:#fafafa; padding:8px 10px; border:1px solid #ddd;">
                                        {{ ucwords(strtolower($client_spoc)) }}</td>
                                </tr>
                                <tr>
                                    <th align="left"
                                        style="background:#f2f2f2; padding:8px 10px; border:1px solid #ddd;">Agency SPOC
                                    </th>
                                    <td style="background:#fafafa; padding:8px 10px; border:1px solid #ddd;">
                                        {{ ucwords(strtolower($agency_spoc)) }}</td>
                                </tr>
                                <tr>
                                    <th align="left"
                                        style="background:#f2f2f2; padding:8px 10px; border:1px solid #ddd;">Agency SPOC
                                        Number</th>
                                    <td style="background:#fafafa; padding:8px 10px; border:1px solid #ddd;">
                                        {{ $agency_spoc_number }}</td>
                                </tr>
                                <tr>
                                    <th align="left"
                                        style="background:#f2f2f2; padding:8px 10px; border:1px solid #ddd;">Agency SPOC
                                        Email</th>
                                    <td style="background:#fafafa; padding:8px 10px; border:1px solid #ddd;">
                                        {{ $agency_email }}</td>
                                </tr>
                                <tr>
                                    <th align="left"
                                        style="background:#f2f2f2; padding:8px 10px; border:1px solid #ddd;">Audit Date
                                    </th>
                                    <td style="background:#fafafa; padding:8px 10px; border:1px solid #ddd;">
                                        {{ $audit_date }}</td>
                                </tr>
                                {{-- <tr>
                                    <th align="left"
                                        style="background:#f2f2f2; padding:8px 10px; border:1px solid #ddd;">Audit
                                        Type-1</th>
                                    <td style="background:#fafafa; padding:8px 10px; border:1px solid #ddd;">Operational
                                        Audit</td>
                                </tr> --}}
                                <tr>
                                    <th align="left"
                                        style="background:#f2f2f2; padding:8px 10px; border:1px solid #ddd;">Auditor
                                        Name</th>
                                    <td style="background:#fafafa; padding:8px 10px; border:1px solid #ddd;">
                                        {{ ucwords(strtolower($auditor_name)) }}</td>
                                </tr>
                                {{-- <tr>
                                    <th align="left"
                                        style="background:#f2f2f2; padding:8px 10px; border:1px solid #ddd;">Audit
                                        Type-2</th>
                                    <td style="background:#fafafa; padding:8px 10px; border:1px solid #ddd;">Call
                                        Quality Audit</td>
                                </tr>
                                <tr>
                                    <th align="left"
                                        style="background:#f2f2f2; padding:8px 10px; border:1px solid #ddd;">Auditor
                                        Name</th>
                                    <td style="background:#fafafa; padding:8px 10px; border:1px solid #ddd;">Pawan Saini
                                    </td>
                                </tr> --}}
                            </table>

                            <p style="margin-top:20px;">
                                We request your cooperation in sharing the required data and relevant documents as per
                                the <strong>attached checklist</strong>. Kindly ensure that all concerned personnel are
                                available on the
                                scheduled audit date to facilitate smooth coordination and timely completion of the
                                audit.
                            </p>

                            

                           

                <p style="margin-top:20px;">
                    Your support and cooperation are highly appreciated and will contribute to the successful completion
                    of this process. In case of any queries or clarifications, please feel free to reach out to the
                    audit team.
                </p>

                <p style="font-size:15px; margin-top:20px;">
                    Thank you for your continued support.
                </p>

                <p style="font-size:15px; ">
                    Best Regards,<br>
                    <strong style="color:#00723f;">QDegrees Audit Team</strong>
                </p>
            </td>
        </tr>
    </table>

    <!-- Mobile style fallback -->
    <table role="presentation" width="100%" style="margin-top:15px;">
        <tr>
            <td align="center" style="font-size:15px; color:#999;">© {{ date('Y') }} QDegrees. All
                rights reserved.</td>
        </tr>
    </table>
    </td>
    </tr>
    </table>
</body>

</html>
