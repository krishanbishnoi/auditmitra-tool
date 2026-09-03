@extends('layouts.master')

@section('title')
    Submitted Audit Data
@endsection

@section('content')
    <style>
        /* ─── Base ─────────────────────────────────────────────── */
        .audit-page {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: #f0f2f5;
            min-height: 100vh;
            padding: 28px 24px;
            box-sizing: border-box;
        }

        /* ─── Page Title ─────────────────────────────────────── */
        .page-title {
            font-size: 20px;
            font-weight: 700;
            color: #111827;
            letter-spacing: -.3px;
            margin-bottom: 20px;
        }

        .page-title span {
            font-size: 13px;
            font-weight: 400;
            color: #6b7280;
            margin-left: 8px;
        }

        /* ─── Cards ─────────────────────────────────────────── */
        .card {
            background: #fff;
            border-radius: 14px;
            border: 1px solid #e5e7eb;
            margin-bottom: 18px;
            overflow: hidden;
        }

        .card-head {
            padding: 14px 20px;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-head-title {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #9ca3af;
        }

        .card-body {
            padding: 20px;
        }

        /* ─── Audit Meta Grid ─────────────────────────────── */
        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px 40px;
        }

        .meta-item {
            display: flex;
            align-items: baseline;
            gap: 6px;
            font-size: 13.5px;
            color: #374151;
        }

        .meta-item .lbl {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: #9ca3af;
            white-space: nowrap;
            min-width: 70px;
        }

        .meta-item .val {
            font-weight: 600;
            color: #111827;
        }

        /* ─── Stat Cards ─────────────────────────────────── */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
            margin-bottom: 18px;
        }

        .stat-card {
            background: #fff;
            border-radius: 14px;
            border: 1px solid #e5e7eb;
            padding: 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
        }

        .stat-card.blue::before {
            background: #2563eb;
        }

        .stat-card.green::before {
            background: #16a34a;
        }

        .stat-card.purple::before {
            background: #7c3aed;
        }

        .stat-label {
            font-size: 11.5px;
            font-weight: 600;
            letter-spacing: .04em;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 10px;
        }

        .stat-value {
            font-size: 30px;
            font-weight: 700;
            line-height: 1;
            letter-spacing: -.5px;
        }

        .stat-value.blue {
            color: #2563eb;
        }

        .stat-value.green {
            color: #16a34a;
        }

        .stat-value.purple {
            color: #7c3aed;
        }

        /* ─── Pillar Table ───────────────────────────────── */
        .pillar-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
        }

        .pillar-table thead tr {
            background: #f9fafb;
        }

        .pillar-table th {
            text-align: left;
            padding: 10px 14px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: #9ca3af;
            border-bottom: 1px solid #e5e7eb;
        }

        .pillar-table td {
            padding: 12px 14px;
            color: #374151;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }

        .pillar-table tbody tr:last-child td {
            border-bottom: none;
        }

        .pillar-table tbody tr:hover {
            background: #fafafa;
        }

        .score-strong {
            font-size: 14px;
            font-weight: 700;
            color: #1d4ed8;
        }

        /* ─── Progress Bar ───────────────────────────────── */
        .progress-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .progress-bar {
            flex: 1;
            height: 6px;
            background: #f3f4f6;
            border-radius: 99px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #2563eb, #60a5fa);
            border-radius: 99px;
        }

        .progress-pct {
            font-size: 12px;
            font-weight: 600;
            color: #2563eb;
            white-space: nowrap;
        }

        /* ─── Location Block ─────────────────────────────── */
        .location-block {
            background: #fff;
            border-radius: 14px;
            border: 1px solid #e5e7eb;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .location-header {
            background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
            color: #fff;
            padding: 14px 22px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .location-header .loc-name {
            font-size: 15px;
            font-weight: 700;
            letter-spacing: -.2px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .location-header .campus-badge {
            font-size: 11.5px;
            font-weight: 600;
            background: rgba(255, 255, 255, .18);
            border: 1px solid rgba(255, 255, 255, .25);
            border-radius: 20px;
            padding: 4px 12px;
        }

        .location-body {
            padding: 18px 20px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        /* ─── Parameter Card ─────────────────────────────── */
        .param-card {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
        }

        .param-card-header {
            background: #f9fafb;
            padding: 14px 18px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
        }

        .param-name {
            font-size: 14.5px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 8px;
            line-height: 1.4;
        }

        .param-score-row {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .score-chip {
            font-size: 12px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 6px;
        }

        .score-chip.achieved {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .score-chip.total {
            background: #f3f4f6;
            color: #6b7280;
            border: 1px solid #e5e7eb;
        }

        .score-chip.pct {
            background: #dcfce7;
            color: #15803d;
        }

        .param-card-body {
            padding: 14px 18px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        /* ─── Sub Parameter Card ─────────────────────────── */
        .sub-card {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 14px 16px;
            background: #fff;
        }

        .sub-card:hover {
            border-color: #c7d2fe;
            box-shadow: 0 1px 6px rgba(99, 102, 241, .07);
        }

        .sub-name {
            font-size: 13.5px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 10px;
            line-height: 1.4;
        }

        /* ─── Chips ──────────────────────────────────────── */
        .chip-row {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 10px;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11.5px;
            font-weight: 500;
            padding: 3px 9px;
            border-radius: 6px;
            white-space: nowrap;
            border: 1px solid transparent;
        }

        .chip .chip-lbl {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .05em;
            text-transform: uppercase;
            opacity: .65;
        }

        .chip-pillar {
            background: #ede9fe;
            color: #5b21b6;
            border-color: #ddd6fe;
        }

        .chip-touch {
            background: #dbeafe;
            color: #1e40af;
            border-color: #bfdbfe;
        }

        .chip-option {
            background: #f3f4f6;
            color: #374151;
            border-color: #e5e7eb;
        }

        .chip-score {
            background: #dcfce7;
            color: #15803d;
            border-color: #bbf7d0;
        }

        .chip-scorable {
            background: #fef9c3;
            color: #854d0e;
            border-color: #fde68a;
        }

        .chip-compliance {
            background: #ffedd5;
            color: #9a3412;
            border-color: #fed7aa;
        }

        /* ─── Remark ─────────────────────────────────────── */
        .remark-block {
            background: #f9fafb;
            border-left: 3px solid #d1d5db;
            border-radius: 0 6px 6px 0;
            padding: 8px 12px;
            margin-bottom: 10px;
            font-size: 13px;
            color: #4b5563;
            line-height: 1.5;
        }

        .remark-block .remark-lbl {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #9ca3af;
            margin-bottom: 3px;
        }

        /* ─── Artifacts ──────────────────────────────────── */
        .artifact-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .artifact-grid a {
            display: block;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
            transition: transform .15s, box-shadow .15s;
        }

        .artifact-grid a:hover {
            transform: scale(1.04);
            box-shadow: 0 4px 12px rgba(0, 0, 0, .12);
        }

        .artifact-grid img {
            width: 72px;
            height: 72px;
            object-fit: cover;
            display: block;
        }

        .no-artifact {
            font-size: 12px;
            color: #9ca3af;
            font-style: italic;
        }

        /* ─── Responsive ─────────────────────────────────── */
        @media (max-width: 640px) {
            .stat-grid {
                grid-template-columns: 1fr;
            }

            .meta-grid {
                grid-template-columns: 1fr;
            }

            .audit-page {
                padding: 16px 12px;
            }
        }
    </style>

    <div class="audit-page">

        {{-- ── Page Title ─────────────────────────────────────── --}}
        <div class="page-title">
            Audit Report
            <span>ID #{{ request()->route('audit_id') ?? '' }}</span>
        </div>

        {{-- ── Agency / Audit Details ──────────────────────────── --}}
        <div class="card">
            <div class="card-head">
                <span class="card-head-title">Audit Details</span>
            </div>
            <div class="card-body">
                <div class="meta-grid">
                    <div class="meta-item">
                        <span class="lbl">Agency</span>
                        <span class="val">{{ $auditDetails->agency_name ?? '—' }}</span>
                    </div>
                    <div class="meta-item">
                        <span class="lbl">City</span>
                        <span class="val">{{ $auditDetails->agency_city ?? '—' }}</span>
                    </div>
                    <div class="meta-item">
                        <span class="lbl">Audit Date</span>
                        <span class="val">{{ $auditDetails->audit_date_by_aud ?? '—' }}</span>
                    </div>
                    <div class="meta-item">
                        <span class="lbl">Location</span>
                        <span class="val">{{ $auditDetails->agency_location ?? '—' }}</span>
                    </div>
                    <div class="meta-item">
                        <span class="lbl">Auditor</span>
                        <span class="val">{{ $auditDetails->present_auditor ?? '—' }}</span>
                    </div>
                    <div class="meta-item">
                        <span class="lbl">Lat / Long</span>
                        <span class="val">{{ $auditDetails->lat_long ?? '—' }}</span>
                    </div>
                </div>
            </div>
        </div>


        {{-- ── Overall Stats ──────────────────────────────────── --}}
        @php
            $pillarResult = $overallResult['pillar_wise_result']['overall_pillar_result'];
        @endphp
        <div class="stat-grid">
            <div class="stat-card blue">
                <div class="stat-label">Final Score</div>
                <div class="stat-value blue">{{ $pillarResult['final_percentage'] }}%</div>
            </div>
            <div class="stat-card green">
                <div class="stat-label">Weighted Score</div>
                <div class="stat-value green">{{ $pillarResult['total_weighted_score'] }}</div>
            </div>
            <div class="stat-card purple">
                <div class="stat-label">Total Pillar Weight</div>
                <div class="stat-value purple">{{ $pillarResult['total_pillar_weight'] }}</div>
            </div>
        </div>

        {{-- ── Pillar-wise Result ──────────────────────────────── --}}
        <div class="card">
            <div class="card-head">
                <span class="card-head-title">Pillar-wise Result</span>
            </div>
            <div class="card-body" style="padding:0;">
                <table class="pillar-table">
                    <thead>
                        <tr>
                            <th>Pillar</th>
                            <th>Score</th>
                            <th>Scorable</th>
                            <th>Percentage</th>
                            <th>Pillar Weight</th>
                            <th>Weighted Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($overallResult['pillar_wise_result']['pillar_data'] as $pillar)
                            <tr>
                                <td>
                                    <span class="chip chip-pillar">{{ $pillar->pillar }}</span>
                                </td>
                                <td>{{ $pillar->total_score }}</td>
                                <td>{{ $pillar->total_scorable }}</td>
                                <td>
                                    <div class="progress-wrap">
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: {{ min($pillar->percentage, 100) }}%">
                                            </div>
                                        </div>
                                        <span class="progress-pct">{{ $pillar->percentage }}%</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="score-chip total"
                                        style="font-size:13px;">{{ $pillar->pillar_weight }}</span>
                                </td>
                                <td><span class="score-strong">{{ $pillar->weighted_score }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ── Location Blocks ──────────────────────────────────── --}}
        @foreach ($submittedAuditData as $location)
            <div class="location-block">

                <div class="location-header">
                    <div class="loc-name">
                        📍 {{ ucfirst($location['location']) }}
                    </div>
                    <span class="campus-badge">{{ ucfirst($location['campus_type']) }}</span>
                </div>
                <form action="{{ route('audit.v2.remark-update') }}" method="POST">
                    @csrf
                    <div class="location-body">
                        @foreach ($location['data'] as $parameter)
                            @php
                                $paramPct =
                                    $parameter['total_scorable'] > 0
                                        ? round(($parameter['total_score'] / $parameter['total_scorable']) * 100, 1)
                                        : 0;
                            @endphp

                            <div class="param-card">

                                {{-- Parameter Header --}}
                                <div class="param-card-header">
                                    <div style="flex:1;">
                                        <div class="param-name">
                                            {{ $parameter['parameter_name'] }}
                                            @if (!empty($parameter['parameter_tag']))
                                                &mdash; <span
                                                    style="font-weight:500;color:#6b7280;">{{ $parameter['parameter_tag'] }}</span>
                                            @endif
                                        </div>
                                        <div class="param-score-row">
                                            <span class="score-chip achieved">Score: {{ $parameter['total_score'] }}</span>
                                            <span class="score-chip total">/ {{ $parameter['total_scorable'] }}</span>
                                            <span class="score-chip pct">{{ $paramPct }}%</span>
                                        </div>
                                    </div>
                                    <div style="text-align:right;">
                                        <div class="progress-wrap" style="min-width:120px;">
                                            <div class="progress-bar">
                                                <div class="progress-fill" style="width:{{ min($paramPct, 100) }}%"></div>
                                            </div>
                                            <span class="progress-pct">{{ $paramPct }}%</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Sub Parameters --}}
                                <div class="param-card-body">
                                    @foreach ($parameter['subparameter'] as $sub)
                                        <div class="sub-card">

                                            <div class="sub-name">{{ $sub['subparam_name'] }}</div>

                                            <div class="chip-row">
                                                <span class="chip chip-pillar">
                                                    <span class="chip-lbl">Pillar</span>
                                                    {{ $sub['pillar'] }}
                                                </span>
                                                <span class="chip chip-touch">
                                                    <span class="chip-lbl">Touch Point</span>
                                                    {{ $sub['touch_point'] }}
                                                </span>
                                                <span class="chip chip-option">
                                                    <span class="chip-lbl">Option</span>
                                                    {{ $sub['option_selected'] }}
                                                </span>
                                                <span class="chip chip-score">
                                                    <span class="chip-lbl">Score</span>
                                                    {{ $sub['score'] }}
                                                </span>
                                                <span class="chip chip-scorable">
                                                    <span class="chip-lbl">Scorable</span>
                                                    {{ $sub['scorable'] }}
                                                </span>
                                                @if (!empty($sub['compliance_experience']))
                                                    <span
                                                        class="chip chip-compliance">{{ $sub['compliance_experience'] }}</span>
                                                @endif
                                            </div>

                                            @if (!empty($sub['remark']))
                                                <div class="remark-block">
                                                    <div class="remark-lbl">Remark</div>

                                                    <input type="hidden" name="que_remark_id[]"
                                                        value="{{ $sub['que_remark_id'] }}">

                                                    <textarea name="remark[]" class="form-control remark-textarea" rows="3">{{ $sub['remark'] }}</textarea>
                                                </div>
                                            @endif

                                            @if (count($sub['artifacts']) > 0)
                                                <div class="artifact-grid">
                                                    @foreach ($sub['artifacts'] as $artifact)
                                                        <a href="{{ $artifact->file }}" target="_blank">
                                                            <img src="{{ $artifact->file }}" alt="Artifact photo">
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="no-artifact">No attachments</div>
                                            @endif

                                        </div>
                                    @endforeach
                                </div>

                            </div>
                        @endforeach
                    </div>
                    <div class="text-left m-3">
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-save"></i> Update Remarks
                        </button>
                    </div>
                </form>
            </div>
        @endforeach

    </div>
@endsection
