<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Audit Report</title>
    <style>
        /* ============================================================
           PAGE SETUP (dompdf supports @page margins + running header/footer
           via position:fixed elements placed outside normal flow)
        ============================================================ */
        @page {
            margin: 80px 20px 70px 20px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10.5px;
            color: #1f2933;
            line-height: 1.45;
        }

        /* ---------- Running header on every page ---------- */
        .pdf-header {
            position: fixed;
            top: -70px;
            left: 0px;
            right: 0px;
            height: 80px;
        }

        .pdf-header table {
            width: 100%;
            border: none;
        }

        .pdf-header td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }

        .pdf-header .brand-name {
            font-size: 18px;
            font-weight: bold;
            color: #1a3c34;
        }

        .pdf-header .brand-sub {
            font-size: 9px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .pdf-header .meta-right {
            text-align: right;
            font-size: 9px;
            color: #6b7280;
        }

        .pdf-header .rule {
            border-bottom: 3px solid #1a3c34;
            margin-top: 8px;
        }

        /* ---------- Running footer on every page ---------- */
        .pdf-footer {
            position: fixed;
            bottom: -50px;
            left: 0px;
            right: 0px;
            height: 40px;
            font-size: 8.5px;
            color: #6b7280;
            border-top: 1px solid #c6ccd1;
            padding-top: 6px;
        }

        .pdf-footer table {
            width: 100%;
            border: none;
        }

        .pdf-footer td {
            border: none;
            padding: 0;
        }

        .pdf-footer .page-num:before {
            content: "Page " counter(page) " of " counter(pages);
        }

        /* ============================================================
           GENERIC TABLE STYLES
        ============================================================ */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        table,
        th,
        td {
            border: 1px solid #c9cfd4;
        }

        th {
            background-color: #1a3c34;
            color: #ffffff;
            padding: 6px 7px;
            text-align: center;
            font-weight: bold;
            font-size: 9.5px;
        }

        td {
            padding: 5px 7px;
            text-align: center;
            vertical-align: middle;
        }

        .text-left {
            text-align: left;
        }

        /* ---------- Title ---------- */
        h2.report-title {
            text-align: center;
            margin: 0 0 16px 0;
            font-size: 17px;
            color: #1a3c34;
            letter-spacing: 0.5px;
        }

        /* ---------- Audit Details ---------- */
        .audit-details th {
            background-color: #eef3f1;
            color: #1a3c34;
            text-align: left;
            width: 15%;
        }

        .audit-details td {
            text-align: left;
            width: 35%;
        }

        /* ---------- Section / hierarchy headers ---------- */
        .section-title {
            background: #1a3c34;
            color: #ffffff;
            padding: 7px 10px;
            font-weight: bold;
            font-size: 11.5px;
        }

        .section-title .tag {
            background: rgba(255, 255, 255, 0.18);
            padding: 2px 7px;
            margin-left: 5px;
            font-size: 9px;
            font-weight: normal;
        }

        .parameter-title {
            background: #dcebd6;
            color: #1a3c34;
            padding: 5px 10px;
            font-weight: bold;
            font-size: 10px;
            border: 1px solid #c9cfd4;
            border-top: none;
        }

        .location-block {
            border: 1px solid #c9cfd4;
            margin-bottom: 18px;
            page-break-inside: avoid;
        }

        .param-block {
            page-break-inside: avoid;
        }

        .sub-table {
            margin-bottom: 0;
        }

        .sub-table th {
            background-color: #eef3f1;
            color: #1a3c34;
        }

        .score-good {
            color: #1a7a3c;
            font-weight: bold;
        }

        .score-bad {
            color: #c0392b;
            font-weight: bold;
        }

        /* ============================================================
           OVERALL RESULT / CHART (built with TABLE cells, not inline-block,
           since dompdf renders table-based bars far more reliably)
        ============================================================ */
        .overall-wrap {
            border: 1px solid #c9cfd4;
            border-top: none;
            padding: 12px;
            margin-bottom: 20px;
        }

        .gauge-row {
            border: none;
            margin-bottom: 0;
        }

        .gauge-row td {
            border: none;
            padding: 0 5px;
            vertical-align: top;
        }

        .gauge-card {
            border: 1px solid #c9cfd4;
            border-radius: 6px;
            text-align: center;
            padding: 12px 8px;
        }

        .gauge-pillar-name {
            font-size: 9px;
            font-weight: bold;
            color: #1a3c34;
            margin-top: 6px;
            min-height: 22px;
        }

        .gauge-score-line {
            font-size: 9.5px;
            font-weight: bold;
            color: #374151;
            margin-top: 2px;
        }

        .gauge-score-line span {
            font-weight: normal;
            color: #9aa3ab;
        }

        .final-card {
            background: #1a3c34;
            border: 1px solid #1a3c34;
            padding: 16px 8px;
        }

        .final-card .lbl {
            font-size: 8.5px;
            color: #cfe3da;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .final-card .pct {
            display: block;
            font-size: 26px;
            font-weight: bold;
            color: #ffffff;
            margin: 8px 0 4px 0;
        }

        .final-card .final-sub {
            font-size: 9px;
            color: #cfe3da;
        }
    </style>
</head>

<body>

    <!-- ======================= RUNNING HEADER ======================= -->
    <div class="pdf-header">
        <table>
            <tr>
                <td class="meta-left" style="width:20%;">
                </td>
                <td style="width:60%; text-align:center">
                    <div class="brand-name">Audit Report</div>
                    {{-- <div class="brand-sub">{{ $auditDetails->agency_name ?? '' }}
    </div> --}}
    </td>
    <td class="meta-right" style="width:20%;">
        {{-- Audit ID: {{ $audit_id }}<br> --}}
        Date: {{ $auditDetails->audit_date_by_aud ?? '' }}
    </td>
    </tr>
    </table>
    <div class="rule"></div>
    </div>

    <!-- ======================= RUNNING FOOTER ======================= -->
    <div class="pdf-footer">
        <table>
            <tr>
                <td style="width:50%; text-align:left;">Audit Report &mdash; Confidential</td>
                <td style="width:50%; text-align:right;">© 2026 QDegrees Services. All rights reserved</td>
            </tr>
        </table>
    </div>

    <!-- ======================= AUDIT DETAILS ======================= -->
    <table class="audit-details">
        <tr>
            <th>Audit ID</th>
            <td>{{ $audit_id }}</td>
            <th>Name</th>
            <td>{{ $auditDetails->agency_name ?? '' }}</td>
        </tr>
        <tr>
            <th>Audit Date</th>
            <td>{{ $auditDetails->audit_date_by_aud ?? '' }}</td>
            <th>Auditor Name</th>
            <td>{{ $auditDetails->auditor_name ?? '' }}</td>
        </tr>
        <tr>
            <th>City</th>
            <td>{{ $auditDetails->agency_city ?? '' }}</td>
            <th>Location</th>
            <td>{{ $auditDetails->agency_location ?? '' }}</td>
        </tr>
        <tr>
            <th>Address</th>
            <td colspan="3">{{ $auditDetails->address ?? '' }}</td>
        </tr>
        <tr>
            <th>Latitude / Longitude</th>
            <td colspan="3">{{ $auditDetails->lat_long ?? '' }}</td>
        </tr>
    </table>

    <!-- ======================= OVERALL RESULT (TOP) ======================= -->
    <div class="section-title">Overall Result Summary</div>

    <div class="overall-wrap">

        @php
        $cardCount = count($pillarData) + 1; // +1 for the final overall card
        $cardWidth = round(100 / $cardCount, 2);
        $radius = 32;
        $circumference = round(2 * M_PI * $radius, 2);
        @endphp

        <table class="gauge-row">
            <tr>
                @foreach ($pillarData as $pillar)
                @php
                $achievedPct =
                $pillar->pillar_weight > 0
                ? round(($pillar->weighted_score / $pillar->pillar_weight) * 100)
                : 0;

                $displayPct = min(100, $achievedPct);

                $fillColor = $achievedPct >= 80 ? '#1a7a3c' : ($achievedPct >= 50 ? '#d98c0e' : '#c0392b');

                $dashOffset = round($circumference * (1 - $displayPct / 100), 2);
                @endphp
                <td style="width:{{ $cardWidth }}%;">
                    <div class="gauge-card">
                        <svg width="86" height="86" viewBox="0 0 86 86">
                            <circle cx="43" cy="43" r="{{ $radius }}" fill="none"
                                stroke="#e9edec" stroke-width="9" />
                            <circle cx="43" cy="43" r="{{ $radius }}" fill="none"
                                stroke="{{ $fillColor }}" stroke-width="9" stroke-linecap="round"
                                stroke-dasharray="{{ $circumference }}" stroke-dashoffset="{{ $dashOffset }}"
                                transform="rotate(-90 43 43)" />
                            <text x="43" y="40" text-anchor="middle" font-size="15" font-weight="bold"
                                fill="{{ $fillColor }}" font-family="DejaVu Sans">{{ $achievedPct }}%</text>
                            <!-- <text x="43" y="53" text-anchor="middle" font-size="7" fill="#9aa3ab"
                                font-family="DejaVu Sans">of weight</text> -->
                        </svg>
                        <div class="gauge-pillar-name">{{ $pillar->pillar }}</div>
                        <div class="gauge-score-line">{{ $pillar->weighted_score }} <span>/
                                {{ $pillar->pillar_weight }}</span></div>
                    </div>
                </td>
                @endforeach

                <!-- Final overall score as the last card in the same row -->
                <td style="width:{{ $cardWidth }}%;">
                    <div class="gauge-card final-card">
                        <span class="lbl">Final Audit<br>Score</span>
                        <span class="pct">{{ $overallPillarResult['final_percentage'] }}%</span>
                        <div class="final-sub">
                            {{ $overallPillarResult['total_weighted_score'] }} /
                            {{ $overallPillarResult['total_pillar_weight'] }}
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <table style="margin-top:14px; margin-bottom:0;">
            <thead>
                <tr>
                    <th>Pillar</th>
                    <th>Total Score</th>
                    <th>Total Scorable</th>
                    <th>Pillar Weight</th>
                    <th>Weighted Score</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pillarData as $pillar)
                <tr>
                    <td class="text-left">{{ $pillar->pillar }}</td>
                    <td>{{ $pillar->total_score }}</td>
                    <td>{{ $pillar->total_scorable }}</td>
                    <td>{{ $pillar->pillar_weight }}</td>
                    <td>{{ $pillar->weighted_score }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- ======================= DETAILED DATA ======================= -->
    <!-- Hierarchy: Location + Campus Type  ->  Parameter  ->  Sub Parameter -->

    @foreach ($submittedAuditData as $location)
    <div class="location-block">

        <div class="section-title">
            {{ $location['location'] }}
            <span class="tag">{{ $location['campus_type'] }}</span>
            {{-- <span class="tag">Brand: {{ $location['brand'] }}</span> --}}
            <span class="tag">Pillar: {{ $location['pillar'] }}</span>
        </div>

        @foreach ($location['data'] as $parameter)
        <div class="param-block">

            <div class="parameter-title">
                {{ $parameter['parameter_name'] }}
                @if ($parameter['parameter_index'])
                ({{ $parameter['parameter_index'] }})
                @endif
            </div>

            <table class="sub-table">
                <thead>
                    <tr>
                        <th style="width:12%;">Touch Point</th>
                        <th style="width:13%;">Sub Parameter</th>
                        <th style="width:16%;">Details</th>
                        <th style="width:13%;">Observation</th>
                        <th style="width:17%;">Remark</th>
                        <th style="width:7%;">Score</th>
                        <th style="width:7%;">Scorable</th>
                        <th style="width:10%;">Type</th>
                        <th style="width:5%;">Artifact</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($parameter['subparameter'] as $sub)
                    <tr>
                        <td class="text-left">{{ $sub['touch_point'] }}</td>
                        <td class="text-left">{{ $sub['subparam_name'] }}</td>
                        <td class="text-left">{{ $sub['details'] }}</td>
                        <td class="text-left">{{ $sub['option_selected'] }}</td>
                        <td class="text-left">{{ $sub['remark'] }}</td>
                        <td
                            class="{{ ($sub['scorable'] ?? 0) > 0 && $sub['score'] == $sub['scorable'] ? 'score-good' : (($sub['scorable'] ?? 0) > 0 && $sub['score'] < $sub['scorable'] ? 'score-bad' : '') }}">
                            {{ $sub['score'] }}
                        </td>
                        <td>{{ $sub['scorable'] }}</td>
                        <td class="text-left">{{ $sub['compliance_experience'] }}</td>
                        <td class="text-center">
                            @if (!empty($sub['artifacts']))
                            @foreach ($sub['artifacts'] as $index => $file)
                            <a href="{{ $file }}" target="_blank">
                                {{ $index + 1 }}
                            </a>
                            @endforeach
                            @else
                            -
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
        @endforeach

    </div>
    @endforeach

</body>

</html>