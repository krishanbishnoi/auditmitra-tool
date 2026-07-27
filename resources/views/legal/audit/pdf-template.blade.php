<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Legal Audit Report </title>
    <style>
        @page {
            margin: 25mm 20mm 20mm 20mm;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #333;
        }

        /* Page 1: Cover Page */
        .cover-page {
            text-align: center;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .company-logo {
            max-width: 150px;
            margin-bottom: 30px;
        }

        .report-title {
            font-size: 28px;
            color: #2c3e50;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .report-subtitle {
            font-size: 18px;
            color: #667eea;
            margin-bottom: 40px;
        }

        .audit-id {
            font-size: 16px;
            color: #6c757d;
            margin-bottom: 50px;
        }

        .audit-details-box {
            width: 80%;
            margin: 40px auto;
            padding: 20px;
            border: 2px solid #667eea;
            border-radius: 8px;
            background-color: #f8f9fa;
        }

        .detail-item {
            margin: 10px 0;
            text-align: left;
            font-size: 14px;
        }

        .detail-label {
            font-weight: bold;
            color: #2c3e50;
            display: inline-block;
            width: 160px;
        }

        .detail-value {
            color: #495057;
        }

        .footer-note {
            position: absolute;
            bottom: 20mm;
            width: 100%;
            text-align: center;
            color: #6c757d;
            font-size: 10px;
        }

        /* Page 2: Findings Summary */
        .page-title {
            text-align: center;
            font-size: 20px;
            color: #2c3e50;
            font-weight: bold;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }

        .section-title {
            background-color: #f8f9fa;
            color: #2c3e50;
            font-size: 14px;
            font-weight: bold;
            padding: 8px 12px;
            border-left: 4px solid #667eea;
            margin: 20px 0 15px 0;
        }

        .findings-summary {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .findings-summary th {
            background-color: #2c3e50;
            color: white;
            font-weight: bold;
            padding: 10px;
            text-align: left;
            border: 1px solid #dee2e6;
        }

        .findings-summary td {
            padding: 10px;
            border: 1px solid #dee2e6;
        }

        .status-badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-compliant {
            background-color: #d4edda;
            color: #155724;
        }

        .status-noncompliant {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        /* Page 3: Detailed Parameters */
        .parameter-container {
            margin-bottom: 25px;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            overflow: hidden;
            page-break-inside: avoid;
        }

        .parameter-header {
            background-color: #2c3e50;
            color: white;
            padding: 10px 15px;
            font-weight: bold;
            font-size: 13px;
        }

        .parameter-content {
            padding: 15px;
        }

        .sub-parameter-item {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px dashed #e9ecef;
        }

        .sub-parameter-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .sub-param-header {
            display: flex;
            align-items: flex-start;
            margin-bottom: 8px;
        }

        .sub-param-number {
            width: 24px;
            height: 24px;
            background-color: #667eea;
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 24px;
            font-size: 11px;
            font-weight: bold;
            margin-right: 10px;
            flex-shrink: 0;
        }

        .sub-param-title {
            font-weight: bold;
            color: #495057;
            flex: 1;
            padding-top: 2px;
        }

        .remarks-box {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 10px;
            border-left: 3px solid #28a745;
        }

        .remarks-label {
            font-weight: bold;
            color: #495057;
            margin-bottom: 5px;
            font-size: 11px;
        }

        .remarks-content {
            color: #6c757d;
            font-size: 11px;
        }

        .artifacts-section {
            margin-top: 10px;
        }

        .artifacts-label {
            font-weight: bold;
            color: #495057;
            margin-bottom: 8px;
            font-size: 11px;
        }

        .artifacts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 8px;
        }

        .artifact-card {
            background-color: #e9ecef;
            padding: 8px;
            border-radius: 4px;
            border-left: 3px solid #2196f3;
            font-size: 10px;
        }

        .artifact-type {
            display: inline-block;
            padding: 2px 6px;
            background-color: #2196f3;
            color: white;
            border-radius: 3px;
            font-size: 9px;
            margin-right: 5px;
        }

        .artifact-type.image {
            background-color: #28a745;
        }

        .artifact-type.document {
            background-color: #6c757d;
        }

        .artifact-type.pdf {
            background-color: #dc3545;
        }

        /* General Styles */
        .page-break {
            page-break-before: always;
        }

        .no-data {
            text-align: center;
            padding: 20px;
            color: #6c757d;
            font-style: italic;
            background-color: #f8f9fa;
            border-radius: 4px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .mb-3 {
            margin-bottom: 15px;
        }

        .mt-3 {
            margin-top: 15px;
        }

        .confidential-watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 60px;
            color: rgba(0, 0, 0, 0.1);
            font-weight: bold;
            pointer-events: none;
            z-index: -1;
        }

        .summary-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-box {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 15px;
            text-align: center;
        }

        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
        }

        .stat-box.completed {
            border-top: 4px solid #28a745;
        }

        .stat-box.pending {
            border-top: 4px solid #ffc107;
        }

        .stat-box.total {
            border-top: 4px solid #667eea;
        }

        /* Image styles */
        .auditor-image {
            max-width: 280px;
            max-height: 180px;
            width: auto;
            height: auto;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 6px;
            background-color: #ffffff;
            display: block;
            margin: 0 auto;
        }

        .artifact-images-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }

        .artifact-image-container {
            text-align: center;
        }

        .artifact-image {
            max-width: 100%;
            max-height: 200px;
            height: auto;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 4px;
            background-color: #ffffff;
        }

        .image-filename {
            font-size: 9px;
            color: #6c757d;
            margin-top: 4px;
            word-break: break-all;
        }

        .document-link {
            display: block;
            font-size: 10px;
            color: #2196f3;
            text-decoration: none;
            padding: 5px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            background-color: #f8f9fa;
            margin-top: 5px;
        }

        .document-link:hover {
            background-color: #e9ecef;
        }
    </style>
    <style>
        .cover-page {
            text-align: center;
        }

        .auditor-evidence-section {
            margin-top: 30px;
        }

        .auditor-evidence-title {
            font-weight: bold;
            margin-bottom: 12px;
            font-size: 14px;
        }

        .auditor-image {
            width: 100%;
            /* FULL WIDTH */
            max-width: 650px;
            /* CONTROL MAX SIZE */
            height: auto;
            border: 1px solid #ccc;
            border-radius: 6px;
            padding: 6px;
        }
    </style>

</head>

<body>
    <!-- Confidential Watermark -->
    {{-- <div class="confidential-watermark">COMPUTER GENERATED</div> --}}

    <!-- Page 1: Cover Page -->
    <div class="cover-page">
        <div style="margin-bottom: 50px;">
            <!-- Add your company logo here -->
            <!-- <img src="{{ storage_path('app/public/logo.png') }}" class="company-logo" alt="Company Logo"> -->
            <div class="report-title">LEGAL AUDIT REPORT</div>
            <div class="report-subtitle">Comprehensive Audit Findings</div>
            {{-- <div class="audit-id">Report ID: AUDIT-{{ str_pad($audit->id, 6, '0', STR_PAD_LEFT) }}</div> --}}
        </div>

        <div class="audit-details-box">
            <div class="detail-item">
                <span class="detail-label">Advocate Name:</span>
                <span class="detail-value">{{ $audit->advocate_name ?? 'N/A' }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Auditor Name:</span>
                <span class="detail-value">{{ $audit->auditor_name ?? 'N/A' }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Audit Date:</span>
                <span class="detail-value">{{ date('d M Y', strtotime($audit->audit_date)) ?? 'N/A' }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Location:</span>
                <span class="detail-value">{{ $audit->location ?? 'N/A' }}, {{ $audit->state ?? 'N/A' }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Legal Manager:</span>
                <span class="detail-value">{{ $audit->legal_manager ?? 'N/A' }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Empanelled From:</span>
                <span class="detail-value">{{ $audit->empanelled_from ?? 'N/A' }}</span>
            </div>
        </div>

        <!-- Auditor Image Section -->
        @if (!empty($audit->auditor_artifact_image))
            @php
                $imagePath = storage_path('app/public/' . $audit->auditor_artifact_image);
            @endphp

            @if (file_exists($imagePath))
                @php
                    $ext = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
                    $data = file_get_contents($imagePath);
                    $src = 'data:image/' . $ext . ';base64,' . base64_encode($data);
                @endphp

                <div class="auditor-evidence-section">
                    <div class="auditor-evidence-title">
                        Auditor Evidence
                    </div>

                    <img src="{{ $src }}" alt="Auditor Artifact" class="auditor-image">
                </div>
            @endif
        @endif



        <div class="footer-note">
            <div>COMPUTER GENERATED REPORT</div>
            <div style="margin-top: 5px;">This is a system-generated document. No physical signature required.</div>
        </div>
    </div>

    <!-- Page 2: Findings Summary -->
    <div class="page-break"></div>
    <div>
        <div class="page-title">AUDIT FINDINGS SUMMARY</div>

        <!-- Executive Summary -->
        <div class="section-title">EXECUTIVE SUMMARY</div>
        <div style="margin-bottom: 20px;">
            <p>This audit was conducted on <strong>{{ date('d M Y', strtotime($audit->audit_date)) }}</strong>
                at the office of <strong>{{ $audit->advocate_name ?? 'the Advocate' }}</strong> located in
                <strong>{{ $audit->location ?? 'N/A' }}, {{ $audit->state ?? 'N/A' }}</strong>. The audit was
                performed by
                <strong>{{ $audit->auditor_name ?? 'the Auditor' }}</strong>.
            </p>


        </div>

        <!-- Statistics Overview -->
        <div class="section-title">STATISTICS OVERVIEW</div>
        <div class="summary-stats">
            @php
                $totalSubParams = 0;
                $totalArtifacts = 0;
                $parametersWithRemarks = 0;

                foreach ($parameters as $parameter) {
                    $totalSubParams += count($parameter['sub_parameters']);
                    foreach ($parameter['sub_parameters'] as $sp) {
                        $totalArtifacts += count($sp['artifacts'] ?? []);
                        if (!empty($sp['remark'])) {
                            $parametersWithRemarks++;
                        }
                    }
                }
            @endphp

            <div class="stat-box total">
                <div class="stat-value">{{ count($parameters) }}</div>
                <div class="stat-label">Total Parameters</div>
            </div>

            <div class="stat-box completed">
                <div class="stat-value">{{ $totalSubParams }}</div>
                <div class="stat-label">Sub-Parameters</div>
            </div>

            <div class="stat-box pending">
                <div class="stat-value">{{ $totalArtifacts }}</div>
                <div class="stat-label">Artifacts Collected</div>
            </div>
        </div>

        <!-- Parameter Details Table -->
        <div class="section-title">PARAMETER DETAILS</div>
        <table class="findings-summary">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="55%">Parameter</th>
                    <th width="20%">Sub-Parameters</th>
                    <th width="20%">Artifacts</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($parameters as $index => $parameter)
                    @php
                        $subParamCount = count($parameter['sub_parameters']);
                        $artifactCount = 0;
                        foreach ($parameter['sub_parameters'] as $sp) {
                            $artifactCount += count($sp['artifacts'] ?? []);
                        }
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $parameter['parameter_name'] }}</strong>
                        </td>
                        <td class="text-center">{{ $subParamCount }}</td>
                        <td class="text-center">{{ $artifactCount }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Key Findings -->
        <div class="section-title">KEY FINDINGS</div>
        <div style="margin-bottom: 20px;">
            <ul style="padding-left: 20px; margin-bottom: 20px;">
                <li style="margin-bottom: 8px;"><strong>Audit Date:</strong>
                    {{ date('d M Y', strtotime($audit->audit_date)) ?? 'N/A' }}</li>
                <li style="margin-bottom: 8px;"><strong>Advocate:</strong> {{ $audit->advocate_name ?? 'N/A' }}</li>
                <li style="margin-bottom: 8px;"><strong>Auditor:</strong> {{ $audit->auditor_name ?? 'N/A' }}</li>
                <li style="margin-bottom: 8px;"><strong>Location:</strong> {{ $audit->location ?? 'N/A' }},
                    {{ $audit->state ?? 'N/A' }}</li>
                <li style="margin-bottom: 8px;"><strong>Total Parameters Audited:</strong> {{ count($parameters) }}
                </li>
                <li style="margin-bottom: 8px;"><strong>Total Sub-Parameters Reviewed:</strong> {{ $totalSubParams }}
                </li>
                <li style="margin-bottom: 8px;"><strong>Total Artifacts Collected:</strong> {{ $totalArtifacts }}</li>
                <li style="margin-bottom: 8px;"><strong>Report Generated:</strong> {{ date('d M Y, h:i A') }}</li>
            </ul>
        </div>

        <!-- Audit Scope -->
        {{-- <div class="section-title">AUDIT SCOPE</div>
        <div style="margin-bottom: 30px;">
            <p>The audit scope included verification of:</p>
            <ul style="padding-left: 20px;">
                <li>Documentation completeness and accuracy</li>
                <li>Compliance with legal requirements</li>
                <li>Office procedures and protocols</li>
                <li>Evidence collection and documentation</li>
                <li>Record keeping and filing systems</li>
            </ul>
        </div> --}}
    </div>

    <!-- Page 3: Detailed Parameters -->
    <div>

        @if (count($parameters) > 0)
            @foreach ($parameters as $parameterIndex => $parameter)
                <div class="parameter-container">
                    <!-- Parameter Header -->
                    <div class="parameter-header">
                        PARAMETER {{ $parameterIndex + 1 }}: {{ $parameter['parameter_name'] }}
                    </div>

                    <!-- Parameter Content -->
                    <div class="parameter-content">
                        @foreach ($parameter['sub_parameters'] as $subIndex => $sp)
                            <div class="sub-parameter-item">
                                <!-- Sub-Parameter Header -->
                                <div class="sub-param-header">
                                    <div class="sub-param-number">{{ $subIndex + 1 }}</div>
                                    <div class="sub-param-title">{{ $sp['sub_parameter'] }}</div>
                                </div>

                                <!-- Remarks -->
                                <div class="remarks-box">
                                    <div class="remarks-label">FINDINGS:</div>
                                    <div class="remarks-content">
                                        {{ $sp['remark'] ?? 'No specific findings recorded for this sub-parameter.' }}
                                    </div>
                                </div>

                                <!-- Artifacts Section -->
                                @if (!empty($sp['artifacts']))
                                    <div class="artifacts-section">
                                        <div class="artifacts-label">SUPPORTING DOCUMENTS / EVIDENCE:</div>

                                        @php
                                            $imageExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp', 'tiff'];

                                            $imageArtifacts = array_filter($sp['artifacts'], function ($artifact) use (
                                                $imageExtensions,
                                            ) {
                                                return in_array(
                                                    strtolower(pathinfo($artifact['filename'], PATHINFO_EXTENSION)),
                                                    $imageExtensions,
                                                );
                                            });

                                            $documentArtifacts = array_filter($sp['artifacts'], function (
                                                $artifact,
                                            ) use ($imageExtensions) {
                                                return !in_array(
                                                    strtolower(pathinfo($artifact['filename'], PATHINFO_EXTENSION)),
                                                    $imageExtensions,
                                                );
                                            });
                                        @endphp

                                        {{-- ================= IMAGE ARTIFACTS ================= --}}
                                        @if (count($imageArtifacts) > 0)
                                            <div class="artifact-images-grid">
                                                @foreach ($imageArtifacts as $artifact)
                                                    @php
                                                        if (!file_exists($artifact['path'])) {
                                                            continue;
                                                        }

                                                        $ext = strtolower(
                                                            pathinfo($artifact['filename'], PATHINFO_EXTENSION),
                                                        );
                                                        $data = file_get_contents($artifact['path']);
                                                        $base64 =
                                                            'data:image/' . $ext . ';base64,' . base64_encode($data);
                                                    @endphp

                                                    <div class="artifact-image-container">
                                                        <img src="{{ $base64 }}"
                                                            alt="{{ $artifact['filename'] }}" class="artifact-image">
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif


                                        {{-- ================= DOCUMENT ARTIFACTS ================= --}}
                                        @if (count($documentArtifacts) > 0)
                                            <div class="artifacts-grid" style="margin-top:10px;">
                                                @foreach ($documentArtifacts as $artifact)
                                                    @php
                                                        $extension = strtolower(
                                                            pathinfo($artifact['filename'], PATHINFO_EXTENSION),
                                                        );
                                                        $typeClass = $extension === 'pdf' ? 'pdf' : 'document';
                                                    @endphp

                                                    <div class="artifact-card">
                                                        <span class="artifact-type {{ $typeClass }}">
                                                            {{ strtoupper($extension === 'pdf' ? 'PDF' : 'DOC') }}
                                                        </span>
                                                        {{ $artifact['filename'] }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="no-data" style="margin-top:10px; font-size:10px;">
                                        No supporting documents uploaded for this sub-parameter
                                    </div>
                                @endif

                            </div>
                        @endforeach
                    </div>
                </div>

                @if (!$loop->last)
                    <div style="height: 15px;"></div> <!-- Spacing between parameters -->
                @endif
            @endforeach

            <!-- Report Footer -->
            <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #dee2e6;">
                <div class="text-center" style="font-size: 10px; color: #6c757d;">
                    <div><strong>COMPUTER GENERATED REPORT</strong></div>
                    <div style="margin-top: 5px;">This document is automatically generated by the system and does not
                        require physical signatures.</div>
                    {{-- <div style="margin-top: 5px;">Report ID: AUDIT-{{ str_pad($audit->id, 6, '0', STR_PAD_LEFT) }} |
                        Generated on: {{ date('d M Y, h:i A') }}</div> --}}
                </div>
            </div>
        @else
            <div class="no-data">
                <h3>No Audit Parameters Found</h3>
                <p>This audit does not contain any parameters. Please check the audit configuration.</p>
            </div>
        @endif
    </div>
</body>

</html>
