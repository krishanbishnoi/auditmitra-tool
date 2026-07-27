<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Audit Intimation - Moneyview</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0; padding:0; background-color:#f4f6f8; ont-family:'Segoe UI', Arial, Helvetica, sans-serif; color:#333;">
  <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color:#f4f6f8; padding:25px 10px;">
    <tr>
      <td align="center">
        <table cellpadding="0" cellspacing="0" border="0" width="600" style="max-width:600px; background-color:#ffffff; border-radius:8px; padding:25px; box-shadow:0 2px 10px rgba(0,0,0,0.05);">
          <tr>
            <td style="font-size:15px; line-height:1.6;">
              <p>Hi Team,</p>
              <p>Greetings!</p>
              <p>
                We would like to inform you that an upcoming audit of the compliance of collection processes
                has been scheduled for your agency. The purpose of this audit is to ensure that all collection
                activities strictly comply with company policies, internal guidelines, and applicable laws
                and regulations.
              </p>
              <p>
                It has been our constant endeavor at
                <strong>Whizdm Finance (Moneyview)</strong> to ensure continual improvement, efficacy,
                and compliance of our collection processes.
              </p>

              <h2 style="color:#00723f; border-bottom:2px solid #00723f; display:inline-block; margin:20px 0 10px;">Audit Details</h2>

              <table cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse; font-size:15px;">
                <tr>
                  <th align="left" width="40%" style="background:#f2f2f2; padding:8px 10px; border:1px solid #ddd;">Agency Name</th>
                  <td style="background:#fafafa; padding:8px 10px; border:1px solid #ddd;">{{ ucwords(strtolower($agency->name)) }}</td>
                </tr>
                <tr>
                  <th align="left" style="background:#f2f2f2; padding:8px 10px; border:1px solid #ddd;">Location</th>
                  <td style="background:#fafafa; padding:8px 10px; border:1px solid #ddd;">{{ ucwords(strtolower($agency->location)) }}</td>
                </tr>
                <tr>
                  <th align="left" style="background:#f2f2f2; padding:8px 10px; border:1px solid #ddd;">State</th>
                  <td style="background:#fafafa; padding:8px 10px; border:1px solid #ddd;">{{ ucwords(strtolower($state)) }}</td>
                </tr>
                <tr>
                  <th align="left" style="background:#f2f2f2; padding:8px 10px; border:1px solid #ddd;">Agency SPOC</th>
                  <td style="background:#fafafa; padding:8px 10px; border:1px solid #ddd;">{{ ucwords(strtolower($agency_spoc)) }}</td>
                </tr>
                <tr>
                  <th align="left" style="background:#f2f2f2; padding:8px 10px; border:1px solid #ddd;">Agency SPOC Number</th>
                  <td style="background:#fafafa; padding:8px 10px; border:1px solid #ddd;">{{ $agency_spoc_number }}</td>
                </tr>
                <tr>
                  <th align="left" style="background:#f2f2f2; padding:8px 10px; border:1px solid #ddd;">Agency SPOC Email</th>
                  <td style="background:#fafafa; padding:8px 10px; border:1px solid #ddd;">{{ $agency_email }}</td>
                </tr>
                <tr>
                  <th align="left" style="background:#f2f2f2; padding:8px 10px; border:1px solid #ddd;">Audit Date</th>
                  <td style="background:#fafafa; padding:8px 10px; border:1px solid #ddd;">{{ $audit_date }}</td>
                </tr>
                <tr>
                  <th align="left" style="background:#f2f2f2; padding:8px 10px; border:1px solid #ddd;">Audit Type-1</th>
                  <td style="background:#fafafa; padding:8px 10px; border:1px solid #ddd;">Operational Audit</td>
                </tr>
                <tr>
                  <th align="left" style="background:#f2f2f2; padding:8px 10px; border:1px solid #ddd;">Auditor Name</th>
                  <td style="background:#fafafa; padding:8px 10px; border:1px solid #ddd;">{{ ucwords(strtolower($auditor_name)) }}</td>
                </tr>
                <tr>
                  <th align="left" style="background:#f2f2f2; padding:8px 10px; border:1px solid #ddd;">Audit Type-2</th>
                  <td style="background:#fafafa; padding:8px 10px; border:1px solid #ddd;">Call Quality Audit</td>
                </tr>
                <tr>
                  <th align="left" style="background:#f2f2f2; padding:8px 10px; border:1px solid #ddd;">Auditor Name</th>
                  <td style="background:#fafafa; padding:8px 10px; border:1px solid #ddd;">Pawan Saini</td>
                </tr>
              </table>

              <p style="margin-top:20px;">
                We look forward to your support with the required data and relevant documents as per the
                attached checklist. Kindly ensure that all concerned personnel are available during the audit
                for seamless coordination.
              </p>

              <h3 style="color:#00723f; margin-top:25px;">Call Recording Format</h3>

              <table cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse; font-size:12px;">
                <thead>
                  <tr style="background-color:#00723f; color:#fff;">
                    <th style="padding:8px; border:1px solid #ddd;">Loan Number</th>
                    <th style="padding:8px; border:1px solid #ddd;">Customer Name</th>
                    <th style="padding:8px; border:1px solid #ddd;">Customer Contact Number</th>
                    <th style="padding:8px; border:1px solid #ddd;">Agency Name</th>
                    <th style="padding:8px; border:1px solid #ddd;">Agency Code</th>
                    <th style="padding:8px; border:1px solid #ddd;">Agency City</th>
                    <th style="padding:8px; border:1px solid #ddd;">Agent Name</th>
                    <th style="padding:8px; border:1px solid #ddd;">Agent Calling Number</th>
                    <th style="padding:8px; border:1px solid #ddd;">Call Date & Time</th>
                    <th style="padding:8px; border:1px solid #ddd;">Call Duration</th>
                    <th style="padding:8px; border:1px solid #ddd;">Agent DOJ</th>
                    <th style="padding:8px; border:1px solid #ddd;">Language</th>
                    <th style="padding:8px; border:1px solid #ddd;">Circle</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td style="padding:8px; border:1px solid #ddd;">Loan Number</td>
                    <td style="padding:8px; border:1px solid #ddd;">Customer Name</td>
                    <td style="padding:8px; border:1px solid #ddd;">Customer Contact Number</td>
                    <td style="padding:8px; border:1px solid #ddd;">Agency Name</td>
                    <td style="padding:8px; border:1px solid #ddd;">Agency Code</td>
                    <td style="padding:8px; border:1px solid #ddd;">Agency City</td>
                    <td style="padding:8px; border:1px solid #ddd;">Agent Name</td>
                    <td style="padding:8px; border:1px solid #ddd;">Agent Calling Number</td>
                    <td style="padding:8px; border:1px solid #ddd;">Call Date & Time</td>
                    <td style="padding:8px; border:1px solid #ddd;">Call Duration</td>
                    <td style="padding:8px; border:1px solid #ddd;">Agent Joining Date (DD-MMM-YY)</td>
                    <td style="padding:8px; border:1px solid #ddd;">Language</td>
                    <td style="padding:8px; border:1px solid #ddd;">State</td>
                  </tr>
                </tbody>
              </table>

              <p style="font-size:15px; margin-top:20px;">
                Your cooperation is crucial for the successful completion of this process.
                Thank you for your attention and support. Please reach out to the audit team
                for any queries or clarifications.
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
            <td align="center" style="font-size:15px; color:#999;">© {{ date('Y') }} QDegrees. All rights reserved.</td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
