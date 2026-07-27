<!DOCTYPE html>
<html>

<head>
    <title>{{ $agency_details->name }} Audit Checksheet</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 80px 5mm 50px 5mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
            color: #333;
            margin: 0;
            line-height: 1.5;
        }

        /* PDF Header Styling */
        .pdf-header {
            position: fixed;
            top: -80px;
            left: 0;
            right: 0;
            height: 60px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
        }

        .pdf-header table {
            width: 100%;
            border: none !important;
            margin: 0;
            border-spacing: 0;
        }

        .pdf-header td {
            border: none !important;
            padding: 0;
        }

        /* PDF Footer Styling */
        .pdf-footer {
            position: fixed;
            bottom: -40px;
            left: 0;
            right: 0;
            height: 30px;
            border-top: 1px solid #ccc;
            padding-top: 5px;
            font-size: 10px;
            display: table;
            width: 100%;
        }

        .footer-left {
            display: table-cell;
            text-align: left;
            padding-left: 10px;
        }

        .footer-right {
            display: table-cell;
            text-align: right;
            padding-right: 10px;
        }

        h1,
        h2,
        h3 {
            color: #1a3e72;
            page-break-after: avoid;
        }

        h1 {
            margin-bottom: 15px;
        }

        h3 {
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px;
            margin-bottom: 20px;
            table-layout: fixed;
            word-wrap: break-word;
            page-break-inside: avoid;
        }

        thead {
            display: table-header-group;
        }

        thead tr {
            background-color: #1a3e72;
            color: white;
            font-weight: 600;
            font-size: 15px;
            text-transform: uppercase;
        }

        thead th:first-child {
            border-top-left-radius: 8px;
        }

        thead th:last-child {
            border-top-right-radius: 8px;
        }

        th,
        td {
            padding: 10px 15px;
            border: 1px solid #ccc;
            vertical-align: middle;
            word-break: break-word;
        }

        tbody tr {
            background: #f9fafd;
        }

        tbody tr:nth-child(even) {
            background: white;
        }

        tbody th {
            font-weight: 600;
            width: 25%;
            background: transparent;
        }

        tr.parameter-row td {
            background-color: #dde5f0 !important;
            color: #1a3e72 !important;
            font-weight: 700;
            font-size: 14px;
            padding-left: 15px;
            text-transform: uppercase;
            border: 1px solid #666 !important;
        }

        .subparam {
            font-style: italic;
            color: #444;
            padding-left: 15px;
        }

        td.numeric {
            text-align: center;
            width: 9%;
        }

        td.text {
            text-align: center;
            width: 13%;
        }

        thead th:nth-child(1),
        tbody td:nth-child(1) {
            width: 30%;
        }

        thead th:nth-child(6),
        tbody td:nth-child(6) {
            width: 30%;
        }

        td[data-label="Remarks"] {
            max-width: 250px;
            color: #222;
            hyphens: auto;
        }

        tr {
            page-break-inside: avoid;
        }

        /* Artifacts Styles */
        .artifact-section {
            padding: 10px;
            page-break-before: always;
        }

        .artifact-param {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 16px;
            color: #2c3e50;
        }

        .artifact-subparam {
            font-weight: 600;
            font-size: 14px;
            color: #34495e;
            padding: 8px 15px;
        }

        .artifact-img-container {
            width: 150px;
            height: 150px;
            border: 1px solid #ddd;
            border-radius: 6px;
            background-color: #fff;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            display: flex;
        }

        .artifact-img-container img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            background-color: #fafafa;
        }

        .pdf-icon-container {
            width: 200px;
            height: 200px;
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            border-radius: 6px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
        }

        .pdf-icon-container img {
            width: 50px;
            height: 50px;
            margin-bottom: 8px;
        }

        .pdf-label {
            font-size: 13px;
            color: #555;
        }
    </style>
</head>

<body>
    @php
        $allocatedmodule = App\Helpers\Helper::allocatedmodulelist();
    @endphp
    <!-- PDF Header -->
    <div class="pdf-header">
        <table>
            <tr>
                <td style="width: 50%; text-align: left;">
                    <img src="https://auditmitr.qdegrees.com/public/images/qdegrees.png" width="150">
                </td>
                <td style="width: 50%; text-align: right;">
                    <img src="https://auditmitr.qdegrees.com/public/images/app_logo.png" width="150">
                </td>
            </tr>
        </table>
    </div>

    <!-- PDF Footer -->
    <div class="pdf-footer">
        <div class="footer-left">
            Confidential
        </div>
        <div class="footer-right">
            © {{ date('Y') }} QDegrees Services. All rights reserved
        </div>
    </div>

    <h1>Process Review Audit Checksheet</h1>

    <!-- Agency Details Table -->
    <table>
        <thead>
            <tr>
                {{-- <th colspan="2">Bank Name</th> --}}
                <th colspan="4">{{ $client_name }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th style="text-align:left;">Process Review Period</th>
                <td style="text-align:left;">{{ $audit_cycle }}</td>
                <th style="text-align:left;">Process Review Agency</th>
                <td style="text-align:left;">{{ $audit_agency_name }}</td>
            </tr>
            <tr>
                <th style="text-align:left;">Agency Name</th>
                <td style="text-align:left;">{{ $agency_details->name }}</td>
                <th style="text-align:left;">Agency Location</th>
                <td style="text-align:left;">{{ $agency_details->location }}</td>
            </tr>
            <tr>
                <th style="text-align:left;">Product</th>
                <td style="text-align:left;">{{ $product_details->name }}</td>
                <th style="text-align:left;">Audit Date</th>
                <td style="text-align:left;">{{ date('d-m-Y', strtotime($audit_date)) }}</td>
            </tr>
            <tr>
                @if (in_array(26, $allocatedmodule))
                    <th style="text-align:left;">Score</th>
                    <td style="text-align:left;">
                        {{ number_format((float) $overall_category_score, 2) }}
                    </td>

                    <th style="text-align:left;">Score Percentage</th>
                    <td style="text-align:left;">
                        {{ number_format((float) $overall_category_percentage, 2) }}%
                    </td>
                @else
                    <th style="text-align:left;">Score</th>
                    <td style="text-align:left;">{{ $score }}</td>
                    <th style="text-align:left;">Score Percentage</th>
                    <td style="text-align:left;">{{ $score_percentage }}</td>
                @endif
            </tr>
            {{-- <tr>
        <th style="text-align:left;">Audit Type</th>
        <td style="text-align:left;">{{ $audit_type }}</td>
        <th style="text-align:left;">Auditor</th>
        <td style="text-align:left;">{{ $present_auditor }}</td>
    </tr>
    <tr>
        <th style="text-align:left;">Agency Address</th>
        <td colspan="3" style="text-align:left;">{{ $agency_details->address }}</td>
    </tr> --}}
            <tr>
                <th style="text-align:left;">Agency Address</th>
                <td style="text-align:left;">{{ $agency_details->address }}</td>
                <th style="text-align:left;">Auditor</th>
                <td style="text-align:left;">{{ $present_auditor }}</td>
            </tr>
        </tbody>
    </table>

    <h2>Parameters</h2>

    <!-- Parameters Table -->
    <table>
        <thead>
            <tr>
                <th>Sub-Parameter</th>
                <th class="text">Allocated Score</th>
                <th class="numeric">Severity</th>
                <th class="text">Status</th>
                <th class="numeric">Scored</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($parameters as $param)
                <tr class="parameter-row">
                    <td colspan="6">{{ $param['name'] }}</td>
                </tr>

                @foreach ($param['subparameters'] as $subparam)
                    <tr>
                        <td>{{ $subparam['name'] ?? 'N/A' }}</td>
                        <td>{{ $subparam['weight'] ?? 'N/A' }}</td>
                        <td>{{ $subparam['severity'] ?? 'N/A' }}</td>
                        <td>
                            @if (($subparam['status'] ?? '') == 'Unsatisfactory')
                                No
                            @elseif(($subparam['status'] ?? '') == 'Satisfactory')
                                Yes
                            @elseif(($subparam['status'] ?? '') == 'N/A')
                                N/A
                            @else
                                N/A
                            @endif
                        </td>
                        <td>{{ $subparam['score'] ?? 'N/A' }}</td>
                        <td>{{ $subparam['remarks'] ?? '' }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    <!-- Artifact Section -->
    {{-- <div class="artifact-section">
    <h3>Artifacts</h3>

    <table>
        @foreach ($parameters as $param)
            @php
                $subparamsWithArtifacts = collect($param['subparameters'])->filter(fn($sub) => !empty($sub['artifact_image_urls']));
            @endphp

            @if ($subparamsWithArtifacts->isNotEmpty())
                <tr><td colspan="6" class="artifact-param">{{ $param['name'] }}</td></tr>

                @foreach ($subparamsWithArtifacts as $subparam)
                    <tr><td colspan="6" class="artifact-subparam">{{ $subparam['name'] }}</td></tr>

                    <tr>
                        <td colspan="6">
                            <table style="width: 100%; border-spacing: 7px;">
                                <tr>
                                    @foreach ($subparam['artifact_image_urls'] as $imgUrl)
                                        @php
                                            $extension = strtolower(pathinfo($imgUrl, PATHINFO_EXTENSION));
                                        @endphp
                                        <td style="width: 200px;">
                                            @if ($extension === 'pdf')
                                                <a href="{{ $imgUrl }}" target="_blank">
                                                    <div class="artifact-img-container">
                                                        <img src="{{ asset('public/images/pdf-icon.png') }}" alt="PDF Icon">
                                                        
                                                    </div>
                                                </a>
                                            @else
                                                <a href="{{ $imgUrl }}" target="_blank">
                                                    <div class="artifact-img-container">
                                                        <img src="{{ $imgUrl }}" alt="Artifact Image">
                                                    </div>
                                                </a>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            </table>
                        </td>
                    </tr>
                @endforeach
            @endif
        @endforeach
    </table>
</div> --}}

</body>

</html>
