<!DOCTYPE html>
<html>
<head>
    <title>{{ $agency_details->name }} Audit Checksheet</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @page {
            size: A4 landscape;
            margin: 80px 5mm 50px 5mm;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
            color: #333;
            background: #fff;
            line-height: 1.5;
        }

        /* ── PDF Header ── */
        .pdf-header {
            position: fixed;
            top: -80px; left: 0; right: 0;
            height: 60px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 8px;
        }
        .pdf-header table { width: 100%; border: none !important; margin: 0; border-spacing: 0; }
        .pdf-header td   { border: none !important; padding: 0; }

        /* ── PDF Footer ── */
        .pdf-footer {
            position: fixed;
            bottom: -40px; left: 0; right: 0;
            height: 30px;
            border-top: 1px solid #ddd;
            padding-top: 5px;
            font-size: 10px;
            display: table; width: 100%;
        }
        .footer-left  { display: table-cell; text-align: left;  padding-left:  10px; color: #888; }
        .footer-right { display: table-cell; text-align: right; padding-right: 10px; color: #888; }

        /* ── Page wrapper ── */
        .page { padding: 0 4px; }

        /* ── Page title ── */
        .page-title-row {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            margin-bottom: 16px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        .page-eyebrow { font-size: 10px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 3px; }
        .page-title   { font-size: 18px; font-weight: 700; color: #1a3e72; }
        .date-badge {
            background: #E6F1FB; color: #0C447C;
            font-size: 10px; font-weight: 600;
            padding: 4px 10px; border-radius: 20px;
        }

        /* ── Meta grid ── */
        .meta-grid {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px;
            margin-bottom: 16px;
        }
        .meta-row { display: table-row; }
        .meta-cell {
            display: table-cell;
            width: 33.33%;
            background: #f8f9fb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 8px 12px;
            vertical-align: top;
        }
        .meta-label { font-size: 10px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 2px; }
        .meta-value { font-size: 12px; font-weight: 600; color: #111827; }

        /* ── Score row ── */
        .score-row {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px;
            margin-bottom: 20px;
        }
        .score-cell {
            display: table-cell;
            width: 50%;
            background: #E6F1FB;
            border-radius: 8px;
            padding: 12px 16px;
            text-align: center;
            vertical-align: middle;
        }
        .score-num { font-size: 28px; font-weight: 700; color: #0C447C; }
        .score-lbl { font-size: 10px; color: #185FA5; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 2px; }

        /* ── Section label ── */
        .section-label {
            font-size: 10px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.06em;
            color: #6b7280; margin-bottom: 10px;
        }

        /* ── Parameter block ── */
        .param-block {
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 14px;
            page-break-inside: avoid;
        }
        .param-header {
            background: #dde5f0;
            padding: 8px 14px;
            font-size: 11px; font-weight: 700;
            color: #1a3e72;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid #b8c6de;
        }

        /* ── Sub-parameter table ── */
        .sub-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .sub-table th {
            font-size: 10px; font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 7px 12px;
            border-bottom: 1px solid #e5e7eb;
            background: #f9fafb;
            text-align: left;
        }
        .sub-table td {
            font-size: 12px;
            padding: 8px 12px;
            border-bottom: 1px solid #f0f0f0;
            color: #1f2937;
            vertical-align: top;
            word-wrap: break-word;
        }
        .sub-table tr:last-child td { border-bottom: none; }
        .sub-table tr:nth-child(even) td { background: #fafafa; }

        /* Column widths */
        .sub-table th:nth-child(1),
        .sub-table td:nth-child(1) { width: 35%; }
        .sub-table th:nth-child(2),
        .sub-table td:nth-child(2) { width: 12%; text-align: center; }
        .sub-table th:nth-child(3),
        .sub-table td:nth-child(3) { width: 12%; text-align: center; }
        .sub-table th:nth-child(4),
        .sub-table td:nth-child(4) { width: 41%; }

        /* ── Status badges ── */
        .badge {
            display: inline-block;
            font-size: 10px; font-weight: 700;
            padding: 2px 8px; border-radius: 20px;
        }
        .badge-yes { background: #EAF3DE; color: #3B6D11; }
        .badge-no  { background: #FCEBEB; color: #A32D2D; }
        .badge-na  { background: #f1f1f1; color: #6b7280; }

        /* ── Severity dots ── */
        .sev { font-size: 11px; font-weight: 700; }
        .sev-high { color: #A32D2D; }
        .sev-med  { color: #854F0B; }
        .sev-low  { color: #3B6D11; }

        .remarks-text { font-size: 11px; color: #6b7280; font-style: italic; }

        /* ── Page footer line ── */
        .page-footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            color: #9ca3af;
        }

        /* ── Responsive (screen only) ── */
        @media screen and (max-width: 680px) {
            .meta-grid, .meta-row, .meta-cell,
            .score-row, .score-cell { display: block; width: 100% !important; }
            .meta-cell, .score-cell { margin-bottom: 8px; }
            .sub-table th:nth-child(2),
            .sub-table td:nth-child(2) { display: none; }
            .sub-table th:nth-child(1), .sub-table td:nth-child(1) { width: 40%; }
            .sub-table th:nth-child(3), .sub-table td:nth-child(3) { width: 18%; }
            .sub-table th:nth-child(4), .sub-table td:nth-child(4) { width: 42%; }
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
            <td style="width:50%;text-align:left;">
                <img src="https://auditmitr.qdegrees.com/public/images/qdegrees.png" width="130">
            </td>
            <td style="width:50%;text-align:right;">
                <img src="https://auditmitr.qdegrees.com/public/images/app_logo.png" width="130">
            </td>
        </tr>
    </table>
</div>

<!-- PDF Footer -->
<div class="pdf-footer">
    <div class="footer-left">Confidential</div>
    <div class="footer-right">© {{ date('Y') }} QDegrees Services. All rights reserved</div>
</div>

<div class="page">

    <!-- Page title row -->
    <div class="page-title-row">
        <div>
            <div class="page-eyebrow">Process review audit checksheet</div>
            <div class="page-title">{{ $client_name }}</div>
        </div>
        <span class="date-badge">{{ date('d-m-Y', strtotime($audit_date)) }}</span>
    </div>

    <!-- Meta grid: 6 fields in 2 rows × 3 cols -->
    <div class="meta-grid">
        <div class="meta-row">
            <div class="meta-cell">
                <div class="meta-label">Agency</div>
                <div class="meta-value">{{ $agency_details->name }}</div>
            </div>
            <div class="meta-cell">
                <div class="meta-label">Location</div>
                <div class="meta-value">{{ $agency_details->location }}</div>
            </div>
            <div class="meta-cell">
                <div class="meta-label">Product</div>
                <div class="meta-value">{{ $product_details->name }}</div>
            </div>
        </div>
        <div class="meta-row">
            <div class="meta-cell">
                <div class="meta-label">Review period</div>
                <div class="meta-value">{{ $audit_cycle }}</div>
            </div>
            <div class="meta-cell">
                <div class="meta-label">Auditor</div>
                <div class="meta-value">{{ $present_auditor }}</div>
            </div>
            <div class="meta-cell">
                <div class="meta-label">Address</div>
                <div class="meta-value">{{ $agency_details->address }}</div>
            </div>
        </div>
    </div>

    <!-- Score cards (conditional) -->
    @if (!in_array(31, $allocatedmodule))
    <div class="score-row">
        <div class="score-cell">
            @if (in_array(26, $allocatedmodule))
                <div class="score-num">{{ number_format((float)$overall_category_score, 2) }}</div>
            @else
                <div class="score-num">{{ $score }}</div>
            @endif
            <div class="score-lbl">Overall score</div>
        </div>
        <div class="score-cell">
            @if (in_array(26, $allocatedmodule))
                <div class="score-num">{{ number_format((float)$overall_category_percentage, 2) }}%</div>
            @else
                <div class="score-num">{{ $score_percentage }}</div>
            @endif
            <div class="score-lbl">Score percentage</div>
        </div>
    </div>
    @endif

    <!-- Parameters -->
    <div class="section-label">Parameters</div>

    @foreach ($parameters as $param)
    <div class="param-block">
        <div class="param-header">{{ $param['name'] }}</div>
        <table class="sub-table">
            <thead>
                <tr>
                    <th>Sub-parameter</th>
                    <th>Severity</th>
                    <th>Status</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($param['subparameters'] as $subparam)
                @php $sev = strtolower($subparam['severity'] ?? ''); @endphp
                <tr>
                    <td>{{ $subparam['name'] ?? '—' }}</td>
                    <td>
                        @if(str_contains($sev,'high'))
                            <span class="sev sev-high">● High</span>
                        @elseif(str_contains($sev,'med'))
                            <span class="sev sev-med">● Medium</span>
                        @elseif(str_contains($sev,'low'))
                            <span class="sev sev-low">● Low</span>
                        @else
                            <span style="color:#9ca3af;font-size:11px;">{{ $subparam['severity'] ?? '—' }}</span>
                        @endif
                    </td>
                    <td>
                        @if (($subparam['status'] ?? '') == 'Satisfactory')
                            <span class="badge badge-yes">✓ Yes</span>
                        @elseif (($subparam['status'] ?? '') == 'Unsatisfactory')
                            <span class="badge badge-no">✕ No</span>
                        @else
                            <span class="badge badge-na">— N/A</span>
                        @endif
                    </td>
                    <td class="remarks-text">{{ $subparam['remarks'] ?? '' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endforeach

</div>
</body>
</html>
