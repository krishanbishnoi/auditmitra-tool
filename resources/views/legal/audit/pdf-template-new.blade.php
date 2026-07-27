<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Process Review Legal Audit Report</title>

    <style>
        img {
            max-width: 100%;
            height: auto;
        }

        /* Page margins */
        @page {
            margin: 120px 25px 60px 25px;
        }
    </style>
</head>

<body>

    <!-- HEADER (REPEATS ON EVERY PAGE) -->
    <div
        style=" position: fixed;
            top: -100px;
            left: 0;
            right: 0;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;">
        <table width="100%">
            <tr>
                <td>
                    <img src="https://auditmitr.qdegrees.com/public/images/app_logo.png" width="100">
                </td>
                <td align="right">
                    <img src="https://auditmitr.qdegrees.com/storage/app/public/logos/mV2UGcRoHde7iIYTS7Cpg9AZ9W4i0XvzeuPBnDti.jpg"
                        width="100">
                </td>
            </tr>
        </table>
    </div>

    <!-- MAIN CONTENT -->

    <!-- TITLE -->
    <div style="text-align:center; border-bottom:2px solid #1f4e79; padding-bottom:10px; margin-bottom:20px;">
        <h2 style="margin-top:2px ; color:#1f4e79;">Process Review Legal Audit Report</h2>
    </div>

    <!-- BASIC INFO TABLE -->
    <table width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse; margin-bottom:20px;">
        <tr>
            <td style="padding:14px 15px; background:#f2f6fb; font-weight:bold; border:1px solid #ddd;">
                Name of Advocate
            </td>
            <td style="padding:14px 15px; border:1px solid #ddd;">
                {{ $audit->advocate_name ?? 'N/A' }}
            </td>
            <td style="padding:14px 15px; background:#f2f6fb; font-weight:bold; border:1px solid #ddd;">
                Name of Auditor
            </td>
            <td style="padding:14px 15px; border:1px solid #ddd;">
                {{ $audit->auditor_name ?? 'N/A' }}
            </td>
        </tr>

        <tr>
            <td style="padding:14px 15px; background:#f2f6fb; font-weight:bold; border:1px solid #ddd;">
                Location
            </td>
            <td style="padding:14px 15px; border:1px solid #ddd;">
                {{ $audit->location ?? 'N/A' }}, {{ $audit->state ?? 'N/A' }}
            </td>
            <td style="padding:14px 15px; background:#f2f6fb; font-weight:bold; border:1px solid #ddd;">
                Audit Date
            </td>
            <td style="padding:14px 15px; border:1px solid #ddd;">
                {{ date('d M Y', strtotime($audit->audit_date)) ?? 'N/A' }}
            </td>
        </tr>

        <tr>
            <td style="padding:14px 15px; background:#f2f6fb; font-weight:bold; border:1px solid #ddd;">
                Legal Manager
            </td>
            <td style="padding:14px 15px; border:1px solid #ddd;">
                {{ $audit->legal_manager ?? 'N/A' }}
            </td>
            <td style="padding:14px 15px; background:#f2f6fb; font-weight:bold; border:1px solid #ddd;">
                Empanelled From
            </td>
            <td style="padding:14px 15px; border:1px solid #ddd;">
                {{ $audit->empanelled_from ?? 'N/A' }}
            </td>
        </tr>
    </table>

    <!-- AUDITOR IMAGE -->
    <div style="text-align: center">
        @if (!empty($audit->auditor_artifact_image))
            @php
                $imagePath = storage_path('app/public/' . $audit->auditor_artifact_image);
            @endphp

            @if (file_exists($imagePath))
                @php
                    $ext = pathinfo($imagePath, PATHINFO_EXTENSION);
                    $src = 'data:image/' . $ext . ';base64,' . base64_encode(file_get_contents($imagePath));
                @endphp
                <img src="{{ $src }}" style="margin-bottom:20px; ">
            @endif
        @endif
    </div>
    <div style="page-break-after: always"></div>

    <!-- EXECUTIVE SUMMARY -->
    <div style="margin-bottom:20px;">
        <h3 style="background:#1f4e79; color:#fff; padding:12px; margin:0;">Executive Summary</h3>
        <table width="100%" style="border-collapse:collapse;">
            <tr style="background:#f2f6fb;">
                <th style="padding:14px; border:1px solid #ddd; text-align:left;">Category</th>
                <th style="padding:14px; border:1px solid #ddd; text-align:left;">Findings</th>
            </tr>

            @forelse ($parameters as $parameter)
                @if (!empty($parameter['summary']))
                    <tr>
                        <td style="padding:14px; border:1px solid #ddd;">
                            {{ $parameter['parameter_name'] }}
                        </td>
                        <td style="padding:14px; border:1px solid #ddd;">
                            {{ $parameter['summary'] }}
                        </td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="2" style="padding:14px; border:1px solid #ddd; text-align:center;">
                        No executive summary available.
                    </td>
                </tr>
            @endforelse
        </table>
    </div>

    <div style="page-break-after: always"></div>

    <!-- DETAILED OBSERVATIONS -->
    <div style="border:1px solid #ddd;">
        <h3 style="background:#1f4e79; color:#fff; padding:12px; margin:0;">Detailed Observations</h3>

        <div style="padding:15px;">
            @foreach ($parameters as $pid => $parameter)
                <div style="margin-bottom:25px; background:#f5f5f5;">
                    <p style="background:#dce4eb; padding:15px; font-weight:bold; margin:0;">
                        PARAMETER : {{ $parameter['parameter_name'] }}
                    </p>

                    <div style="padding:12px;">
                        @foreach ($parameter['sub_parameters'] as $sub)
                            <p><b>Question:</b> {{ $sub['sub_parameter'] }}</p>
                            <p><b>Findings:</b> {{ $sub['remark'] ?? 'N/A' }}</p>
                            <p><b>Compliance Status:</b> {{ $sub['compliance_status'] ?? 'N/A' }}</p>

                            @if (!empty($sub['artifacts']))
                                <p><b>Supporting Documents:</b></p> <br>
                                <div style="text-align: center">
                                    @foreach ($sub['artifacts'] as $artifact)
                                        @php
                                            $extension = strtolower(
                                                pathinfo($artifact['filename'], PATHINFO_EXTENSION),
                                            );
                                            $imageExtensions = ['jpg', 'jpeg', 'png'];

                                            // Absolute filesystem path (for base64 rendering)
                                            $artifactPath = $artifact['path'];

                                            // Public URL (for clicking)
                                            $artifactUrl = asset(
                                                'storage/' .
                                                    ltrim(
                                                        str_replace(storage_path('app/public'), '', $artifactPath),
                                                        '/',
                                                    ),
                                            );
                                        @endphp

                                        @if (file_exists($artifactPath))
    @php
        $relativePath = str_replace(
            storage_path('app/public') . '/',
            '',
            $artifactPath
        );

        $artifactUrl = asset('storage/app/public/' . $relativePath);
        $base64 = 'data:image/' . $extension . ';base64,' .
                  base64_encode(file_get_contents($artifactPath));
    @endphp

    <a href="{{ $artifactUrl }}" target="_blank" style="display: inline-block; text-decoration:none">
        <img src="{{ $base64 }}"
             width="500"
             style="margin:5px; page-break-inside:avoid; cursor:pointer;">
    </a>
@endif

                                    @endforeach
                                </div>
                            @endif

                            <hr>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div style="
    page-break-inside: avoid;
    break-inside: avoid;
    margin-bottom:20px;
">
        <h3 style="background:#1f4e79; color:#fff; padding:12px; margin:0;">
            Recommendations
        </h3>

        <div
            style="
        padding:12px;
        border:1px solid #ddd;
        font-size:13px;
        line-height:1.6;
    ">
            @if (!empty($audit->recommendations))
                {!! nl2br(e($audit->recommendations)) !!}
            @else
                <em>No recommendations available.</em>
            @endif
        </div>
    </div>


</body>

</html>
