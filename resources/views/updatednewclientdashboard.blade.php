@extends('layouts.master')

@section('title', 'AuditFlow – Dashboard')

@section('content')
    <script>
        const downloadRoute = "{{ route('audit.downloadReports', ['audit_id' => '__ID__']) }}";
    </script>

    <!-- ══ SELF-CONTAINED STYLES ══════════════════════════════════════ -->
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">




    <style>
        /* ── RESET & TOKENS ── */
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --blue: #2563EB;
            --blue-lt: #EFF6FF;
            --blue-dk: #1D4ED8;
            --green: #16A34A;
            --green-lt: #DCFCE7;
            --orange: #F59E0B;
            --orange-lt: #FEF3C7;
            --red: #DC2626;
            --red-lt: #FEE2E2;
            --purple: #7C3AED;
            --gray-50: #F8FAFC;
            --gray-100: #F1F5F9;
            --gray-200: #E2E8F0;
            --gray-300: #CBD5E1;
            --gray-400: #94A3B8;
            --gray-500: #64748B;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1E293B;
            --gray-900: #0F172A;
            --white: #ffffff;
            --radius: 10px;
            --shadow: 0 1px 3px rgba(0, 0, 0, .08), 0 1px 2px rgba(0, 0, 0, .05);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, .10);
        }

        .af-root {
            font-family: 'DM Sans', sans-serif;
            background: var(--gray-50);
            color: var(--gray-900);
            font-size: 14px;
            line-height: 1.5;
            min-height: 100vh;
        }

        /* ── WRAP ── */
        .af-wrap {
            max-width: 1280px;
            margin: 0 auto;
            padding: 28px 24px 60px;
        }

        /* ── TAB NAV ── */
        .af-tab-nav {
            display: flex;
            gap: 4px;
            border-bottom: 2px solid var(--gray-200);
            margin-bottom: 32px;
        }

        .af-tab-btn {
            padding: 10px 22px;
            font-size: .875rem;
            font-weight: 600;
            border: none;
            background: none;
            cursor: pointer;
            color: var(--gray-600);
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
            border-radius: 6px 6px 0 0;
            display: flex;
            align-items: center;
            gap: 7px;
            transition: .15s;
            font-family: 'DM Sans', sans-serif;
        }

        .af-tab-btn:hover {
            color: var(--blue);
            background: var(--blue-lt);
        }

        .af-tab-btn.active {
            color: var(--blue);
            border-bottom-color: var(--blue);
        }

        .af-section {
            display: none;
        }

        .af-section.active {
            display: block;
        }

        /* ── PAGE HEADER ── */
        .af-page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 14px;
            margin-bottom: 24px;
        }

        .af-page-header h2 {
            font-size: 1.45rem;
            font-weight: 700;
        }

        .af-page-header p {
            color: var(--gray-600);
            font-size: .8125rem;
            margin-top: 3px;
        }

        .af-header-right {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        /* ── BUTTONS ── */
        .af-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: .8125rem;
            font-weight: 600;
            cursor: pointer;
            border: 1.5px solid transparent;
            transition: .15s;
            white-space: nowrap;
            font-family: 'DM Sans', sans-serif;
        }

        .af-btn-outline {
            background: var(--white);
            border-color: var(--gray-200);
            color: var(--gray-700);
        }

        .af-btn-outline:hover {
            border-color: var(--blue);
            color: var(--blue);
        }

        .af-btn-primary {
            background: var(--blue);
            color: #fff;
            border-color: var(--blue);
        }

        .af-btn-primary:hover {
            background: var(--blue-dk);
        }

        /* ── KPI ROW ── */
        .af-kpi-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        @media(max-width:900px) {
            .af-kpi-row {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media(max-width:520px) {
            .af-kpi-row {
                grid-template-columns: 1fr;
            }
        }

        .af-kpi {
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid var(--gray-200);
            padding: 18px 20px;
            box-shadow: var(--shadow);
            position: relative;
            overflow: hidden;
        }

        .af-kpi-label {
            font-size: .78rem;
            color: var(--gray-600);
            font-weight: 500;
        }

        .af-kpi-value {
            font-size: 1.75rem;
            font-weight: 700;
            margin: 6px 0 0;
            letter-spacing: -.5px;
        }

        .af-kpi-badge {
            position: absolute;
            top: 14px;
            right: 14px;
            font-size: .7rem;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 20px;
            white-space: nowrap;
        }

        .badge-up {
            background: var(--green-lt);
            color: var(--green);
        }

        .badge-down {
            background: var(--red-lt);
            color: var(--red);
        }

        .badge-stable {
            background: var(--gray-100);
            color: var(--gray-600);
        }

        /* ── CARD ── */
        .af-card {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 22px 24px;
        }

        .af-card-title {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .af-card-sub {
            font-size: .78rem;
            color: var(--gray-500);
            margin-bottom: 18px;
        }

        .af-two-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 24px;
        }

        @media(max-width:780px) {
            .af-two-col {
                grid-template-columns: 1fr;
            }
        }

        /* ── LIFECYCLE BARS ── */
        .lifecycle-row {
            margin-bottom: 14px;
        }

        .lifecycle-label {
            font-size: .78rem;
            color: var(--gray-700);
            margin-bottom: 5px;
            font-weight: 500;
        }

        .lifecycle-bar-bg {
            background: var(--gray-100);
            border-radius: 4px;
            height: 22px;
            overflow: hidden;
        }

        .lifecycle-bar-fill {
            height: 100%;
            background: var(--blue);
            border-radius: 4px;
        }

        .lifecycle-count {
            font-size: .72rem;
            color: var(--gray-500);
            margin-top: 3px;
            text-align: right;
        }

        /* ── CALENDAR ── */
        .af-cal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .cal-nav {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .cal-nav-btn {
            width: 30px;
            height: 30px;
            border-radius: 6px;
            border: 1px solid var(--gray-200);
            background: var(--white);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .85rem;
            color: var(--gray-600);
            transition: .15s;
        }

        .cal-nav-btn:hover {
            border-color: var(--blue);
            color: var(--blue);
        }

        .cal-month {
            font-weight: 600;
            font-size: .9rem;
        }

        .af-calendar {
            width: 100%;
            border-collapse: collapse;
        }

        .af-calendar th {
            text-align: center;
            font-size: .72rem;
            font-weight: 600;
            text-transform: uppercase;
            color: var(--gray-500);
            padding: 8px 4px;
            letter-spacing: .05em;
        }

        .af-calendar td {
            vertical-align: top;
            border: 1px solid var(--gray-100);
            padding: 6px;
            width: 14.28%;
            font-size: .78rem;
            color: var(--gray-600);
            min-height: 70px;
        }

        .af-calendar td.today .day-num {
            background: var(--blue);
            color: #fff;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        .af-calendar td.other-month {
            color: var(--gray-300);
            background: var(--gray-50);
        }

        .cal-event {
            font-size: .66rem;
            border-radius: 4px;
            padding: 2px 5px;
            margin-top: 3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-weight: 500;
        }

        .ev-branch {
            background: #DBEAFE;
            color: #1D4ED8;
        }

        .ev-agency {
            background: #D1FAE5;
            color: #065F46;
        }

        .ev-yard {
            background: #FEF3C7;
            color: #92400E;
        }

        /* ── SUBMISSIONS ── */
        .sub-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 13px 16px;
            border-radius: 8px;
            border: 1px solid var(--gray-100);
            margin-bottom: 8px;
            background: var(--white);
            transition: box-shadow .15s;
        }

        .sub-row:hover {
            box-shadow: var(--shadow-md);
        }

        .sub-num {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--blue-lt);
            color: var(--blue);
            font-weight: 700;
            font-size: .8rem;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sub-info {
            flex: 1;
            margin: 0 14px;
        }

        .sub-name {
            font-weight: 600;
            font-size: .875rem;
        }

        .sub-agency {
            font-size: .75rem;
            color: var(--gray-500);
        }

        .sub-meta {
            text-align: right;
        }

        .badge-qc {
            background: #FEF3C7;
            color: #92400E;
            font-size: .7rem;
            font-weight: 600;
            padding: 2px 10px;
            border-radius: 20px;
        }

        .sub-time {
            font-size: .7rem;
            color: var(--gray-400);
            margin-top: 3px;
        }

        /* ── CHART WRAP ── */
        .af-chart-wrap {
            position: relative;
            height: 240px;
        }

        /* ── MAP PLACEHOLDER ── */
        .af-map-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 0;
        }

        @media(max-width:780px) {
            .af-map-section {
                grid-template-columns: 1fr;
            }
        }

        .af-map-placeholder {
            background: var(--gray-100);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 220px;
            color: var(--gray-400);
            font-size: .85rem;
            border: 2px dashed var(--gray-300);
            flex-direction: column;
            gap: 8px;
            text-align: center;
        }

        .af-map-placeholder i {
            font-size: 2.5rem;
        }

        .city-row {
            padding: 12px 0;
            border-bottom: 1px solid var(--gray-100);
        }

        .city-row:last-child {
            border-bottom: none;
        }

        .city-name {
            font-weight: 600;
            font-size: .85rem;
        }

        .city-pct {
            font-size: .78rem;
            color: var(--gray-600);
            float: right;
            font-weight: 600;
        }

        .city-bar-bg {
            background: var(--gray-100);
            border-radius: 4px;
            height: 7px;
            margin-top: 6px;
            overflow: hidden;
        }

        .city-bar {
            height: 100%;
            border-radius: 4px;
            background: var(--blue);
        }

        .city-counts {
            display: flex;
            gap: 14px;
            margin-top: 4px;
            font-size: .7rem;
            color: var(--gray-500);
        }

        .city-counts .ok {
            color: var(--green);
            font-weight: 600;
        }

        .city-counts .no {
            color: var(--red);
            font-weight: 600;
        }

        /* ── PARETO + PERFORMERS ── */
        .af-bottom-row {
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 20px;
            margin-bottom: 24px;
        }

        @media(max-width:780px) {
            .af-bottom-row {
                grid-template-columns: 1fr;
            }
        }

        .perf-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid var(--gray-100);
        }

        .perf-row:last-child {
            border-bottom: none;
        }

        .perf-entity {
            font-weight: 600;
            font-size: .85rem;
        }

        .perf-state {
            font-size: .72rem;
            color: var(--gray-500);
        }

        .perf-score {
            font-weight: 700;
            color: var(--blue);
            font-size: .95rem;
        }

        .perf-badge {
            font-size: .7rem;
            font-weight: 600;
            padding: 2px 10px;
            border-radius: 20px;
            margin-left: 8px;
        }

        .perf-excellent {
            background: var(--green-lt);
            color: var(--green);
        }

        .perf-good {
            background: var(--blue-lt);
            color: var(--blue);
        }

        /* ── ACTION PLANNING ── */
        .af-filters {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius);
            padding: 14px 18px;
            margin-bottom: 22px;
            box-shadow: var(--shadow);
        }

        .af-filters label {
            font-size: .78rem;
            color: var(--gray-600);
            font-weight: 500;
        }

        .af-select {
            padding: 7px 12px;
            border-radius: 7px;
            border: 1px solid var(--gray-200);
            background: var(--white);
            font-size: .8rem;
            color: var(--gray-800);
            cursor: pointer;
            font-family: 'DM Sans', sans-serif;
        }

        .af-input-date {
            padding: 7px 12px;
            border-radius: 7px;
            border: 1px solid var(--gray-200);
            background: var(--white);
            font-size: .8rem;
            color: var(--gray-800);
            font-family: 'DM Sans', sans-serif;
        }

        .af-btn-reset {
            padding: 7px 14px;
            border-radius: 7px;
            border: 1px solid var(--gray-200);
            background: var(--white);
            font-size: .8rem;
            color: var(--gray-700);
            cursor: pointer;
            font-family: 'DM Sans', sans-serif;
            font-weight: 500;
        }

        .af-btn-apply {
            padding: 7px 18px;
            border-radius: 7px;
            border: none;
            background: var(--blue);
            color: #fff;
            font-size: .8rem;
            cursor: pointer;
            font-family: 'DM Sans', sans-serif;
            font-weight: 600;
        }

        .af-action-kpi-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1px;
            background: var(--gray-200);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius);
            overflow: hidden;
            margin-bottom: 22px;
            box-shadow: var(--shadow);
        }

        @media(max-width:780px) {
            .af-action-kpi-row {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media(max-width:480px) {
            .af-action-kpi-row {
                grid-template-columns: 1fr;
            }
        }

        .af-action-kpi {
            background: var(--white);
            padding: 18px 20px;
            border-left: 3px solid transparent;
        }

        .af-action-kpi.bl-blue {
            border-left-color: var(--blue);
        }

        .af-action-kpi.bl-orange {
            border-left-color: var(--orange);
        }

        .af-action-kpi.bl-green {
            border-left-color: var(--green);
        }

        .af-action-kpi.bl-red {
            border-left-color: var(--red);
        }

        .ak-label {
            font-size: .75rem;
            color: var(--gray-600);
            font-weight: 500;
        }

        .ak-value {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 4px 0 2px;
        }

        .ak-sub {
            font-size: .72rem;
        }

        .ak-sub.up {
            color: var(--orange);
        }

        .ak-sub.good {
            color: var(--green);
        }

        .ak-sub.rev {
            color: var(--red);
        }

        .af-inner-tabs {
            display: flex;
            gap: 4px;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 14px;
        }

        .af-inner-tab {
            padding: 7px 16px;
            border-radius: 7px;
            font-size: .8rem;
            font-weight: 600;
            border: 1.5px solid var(--gray-200);
            background: var(--white);
            color: var(--gray-600);
            cursor: pointer;
            transition: .15s;
            font-family: 'DM Sans', sans-serif;
        }

        .af-inner-tab.active {
            background: var(--gray-900);
            border-color: var(--gray-900);
            color: #fff;
        }

        /* ── ISSUES TABLE ── */
        .af-table {
            width: 100%;
            border-collapse: collapse;
        }

        .af-table th {
            font-size: .72rem;
            font-weight: 600;
            color: var(--gray-600);
            text-transform: uppercase;
            letter-spacing: .05em;
            padding: 10px 12px;
            text-align: left;
            border-bottom: 1px solid var(--gray-200);
            background: var(--gray-50);
        }

        .af-table td {
            padding: 12px;
            font-size: .8125rem;
            border-bottom: 1px solid var(--gray-100);
            vertical-align: middle;
        }

        .af-table tr:last-child td {
            border-bottom: none;
        }

        .af-table tr:hover td {
            background: var(--gray-50);
        }

        .act-id {
            color: var(--blue);
            font-weight: 600;
        }

        .risk-badge {
            display: inline-flex;
            padding: 2px 9px;
            border-radius: 20px;
            font-size: .7rem;
            font-weight: 700;
        }

        .risk-critical {
            background: #FEE2E2;
            color: #991B1B;
        }

        .risk-high {
            background: #FEF3C7;
            color: #92400E;
        }

        .risk-medium {
            background: #E0F2FE;
            color: #0369A1;
        }

        .risk-low {
            background: #DCFCE7;
            color: #166534;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: .75rem;
            font-weight: 600;
        }

        .status-open {
            color: var(--orange);
        }

        .status-approved {
            color: var(--green);
        }

        .status-rejected {
            color: var(--red);
        }

        .tbl-pager {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            padding-top: 12px;
        }

        .tbl-pager-btn {
            padding: 5px 14px;
            border-radius: 6px;
            border: 1px solid var(--gray-200);
            background: var(--white);
            font-size: .78rem;
            cursor: pointer;
            font-family: 'DM Sans', sans-serif;
        }

        .tbl-pager-btn:hover {
            border-color: var(--blue);
            color: var(--blue);
        }

        /* ── QUICK LINKS ── */
        .af-quick-links {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-top: 24px;
        }

        @media(max-width:520px) {
            .af-quick-links {
                grid-template-columns: 1fr;
            }
        }

        .af-quick-link {
            display: flex;
            align-items: center;
            gap: 16px;
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius);
            padding: 18px 20px;
            cursor: pointer;
            transition: .15s;
            box-shadow: var(--shadow);
        }

        .af-quick-link:hover {
            box-shadow: var(--shadow-md);
            border-color: var(--blue);
        }

        .af-quick-link-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: var(--blue);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .af-quick-link-title {
            font-weight: 700;
            font-size: .875rem;
        }

        .af-quick-link-sub {
            font-size: .75rem;
            color: var(--gray-600);
        }

        /* ── LEGEND DOT ── */
        .leg-dot {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 2px;
            margin-right: 5px;
            vertical-align: middle;
        }

        .leg-line {
            display: inline-block;
            width: 24px;
            height: 3px;
            border-radius: 2px;
            margin-right: 6px;
            vertical-align: middle;
        }

        /* ── FOOTER ── */
        .af-footer {
            text-align: center;
            font-size: .72rem;
            color: var(--gray-400);
            border-top: 1px solid var(--gray-200);
            padding-top: 20px;
            margin-top: 40px;
        }

        /* ── SEARCH BOX ── */
        .af-searchbox {
            display: flex;
            align-items: center;
            gap: 6px;
            background: var(--gray-50);
            border: 1px solid var(--gray-200);
            border-radius: 7px;
            padding: 7px 12px;
        }

        .af-searchbox input {
            border: none;
            background: none;
            outline: none;
            font-size: .8rem;
            font-family: 'DM Sans', sans-serif;
            width: 180px;
            color: var(--gray-800);
        }
    </style>
    <style>
        .ev-assigned {
            background: #ffe9b3;
            color: #b26a00;
        }

        .ev-completed {
            background: #d4f8d4;
            color: #1a7f1a;
        }

        .af-calendar {
            table-layout: fixed;
            width: 100%;
        }

        /* .af-calendar td {
                                                                height: 60px;
                                                                width: 14.28%;
                                                                vertical-align: top;
                                                                border: 1px solid #eee;
                                                            } */
        .af-calendar {
            table-layout: fixed;
            width: 100%;
            border-collapse: collapse;
        }

        .af-calendar td {
            height: 90px;
            width: 14.28%;
            vertical-align: top;
            border: 1px solid #e5e7eb;
            padding: 6px;
            position: relative;
            transition: 0.2s ease;
            background: #fff;
        }

        .calendar-day:hover {
            background-color: #f3f4f6;
            cursor: pointer;
        }

        .sunday-date {
            background-color: #fff5f5;
        }

        .has-entry {
            background-color: #f0fdf4;
        }

        .today-date {
            background-color: #eff6ff !important;
            border: 2px solid #2563eb !important;
        }

        .day-num {
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 6px;
        }

        .ev-assigned {
            background: #fde68a;
            color: #92400e;
            padding: 3px 6px;
            border-radius: 4px;
            font-size: 12px;
            margin-bottom: 4px;
        }

        .ev-completed {
            background: #bbf7d0;
            color: #166534;
            padding: 3px 6px;
            border-radius: 4px;
            font-size: 12px;
        }


        /* Modal container */
        .custom-modal {
            border-radius: 14px;
            overflow: hidden;
        }

        /* Header */
        .custom-header {
            background: linear-gradient(135deg, #2563eb, #1e40af);
            color: #fff;
            padding: 16px 20px;
        }

        /* Date styling */
        .modal-date {
            font-weight: 600;
            margin-left: 8px;
            font-size: 15px;
            opacity: 0.9;
        }

        /* Section wrapper */
        .audit-section {
            background: #f8fafc;
            padding: 16px;
            border-radius: 10px;
        }

        /* Section title */
        .section-title {
            font-weight: 600;
            margin-bottom: 10px;
        }

        /* List style */
        .audit-section ul {
            padding-left: 18px;
            margin-bottom: 0;
        }

        .audit-section li {
            padding: 6px 0;
            font-size: 14px;
            color: #334155;
        }

        /* Scroll if content too large */
        .modal-body {
            max-height: 60vh;
            overflow-y: auto;
        }
    </style>


    <style>
        /* for city view scroller */
        .city-scroll-box {
            max-height: 400px;
            /* adjust height */
            overflow-y: auto;
            padding-right: 6px;
        }

        /* Optional: nicer scrollbar */
        .city-scroll-box::-webkit-scrollbar {
            width: 6px;
        }

        .city-scroll-box::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 4px;
        }

        .city-scroll-box::-webkit-scrollbar-thumb:hover {
            background: #94A3B8;
        }
    </style>


    {{-- for calender model --}}
    <style>
        .audit-card {
            padding: 12px 15px;
            border: 1px solid #e6e6e6;
            border-radius: 8px;
            margin-bottom: 10px;
            background: #fff;
            transition: 0.2s;
        }

        .audit-card:hover {
            background: #f8fbff;
            border-color: #cfe2ff;
        }

        .audit-info div {
            font-size: 14px;
        }

        .audit-action {
            min-width: 100px;
            text-align: right;
        }

        .assigned-card {
            background: #fffaf2;
        }

        .submitted-card {
            background: #f6fff8;
        }

        .modal-body {
            max-height: 500px;
            overflow-y: auto;
        }
        .dashboardExportBtn{
            padding: 0px 7px 0px 7px;
            border-radius: 8px
        }
       
    </style>


    <!-- ══ ROOT ══════════════════════════════════════════════════════ -->
    <div class="af-root">
        <div class="af-wrap">

            <!-- TAB NAV -->
            <nav class="af-tab-nav">
                <button class="af-tab-btn active" data-tab="dashboard">
                    <i class="fa-solid fa-table-columns"></i> Dashboard
                </button>
                <button class="af-tab-btn" data-tab="trend">
                    <i class="fa-solid fa-chart-line"></i> Trend Analysis
                </button>
                <button class="af-tab-btn" data-tab="action">
                    <i class="fa-solid fa-clipboard-list"></i> Action Planning
                </button>
                <button type="button" class="btn btn-outline-primary btn-sm dashboardExportBtn" data-toggle="modal" data-target="#myModal">
                    Raw Dump
                </button>

                <button type="button" class="btn btn-outline-secondary btn-sm dashboardExportBtn" data-toggle="modal"
                    data-target="#ScheduleAuditExport">
                    Schedule Audits
                </button>
                @if (auth()->user()->client_id == 74 || auth()->user()->client_id == 13)
                    <button type="button" class="btn btn-outline-success btn-sm me-3 dashboardExportBtn" data-toggle="modal"
                        data-target="#openPointersModal">
                        Open Pointers Dump
                    </button>
                @endif
                <div style="align-items: flex-end">
                    <form method="GET" action="{{ route('getNewClientDashboard') }}"
                        class="d-flex align-items-center gap-2 mb-0" id="filter-form">

                        {{-- Cycle Dropdown --}}
                        <select name="audit_cycle_id" class="af-btn af-btn-outline" style="border:none; cursor:pointer;"
                            onchange="document.getElementById('filter-form').submit();">

                            @foreach ($auditCycle as $ac)
                                <option value="{{ $ac->id }}" {{ $currentCycleId == $ac->id ? 'selected' : '' }}>
                                    <i class="fa-regular fa-calendar"></i>
                                    {{ $ac->name }}
                                </option>
                            @endforeach

                        </select>

                        {{-- Audit Type Dropdown --}}
                        <select name="audit_type" class="af-btn af-btn-outline" style="border:none; cursor:pointer;"
                            onchange="document.getElementById('filter-form').submit();">

                            <option value="all" {{ request('audit_type') == 'all' ? 'selected' : '' }}>All</option>
                            <option value="agency" {{ request('audit_type') == 'agency' ? 'selected' : '' }}>Agency</option>
                            <option value="agency_repo" {{ request('audit_type') == 'agency_repo' ? 'selected' : '' }}>
                                Agency Repo</option>
                            <option value="branch" {{ request('audit_type') == 'branch' ? 'selected' : '' }}>Branch</option>
                            <option value="branch_repo" {{ request('audit_type') == 'branch_repo' ? 'selected' : '' }}>
                                Branch Repo</option>
                            <option value="yard" {{ request('audit_type') == 'yard' ? 'selected' : '' }}>Yard</option>
                            <option value="yard_repo" {{ request('audit_type') == 'yard_repo' ? 'selected' : '' }}>Yard
                                Repo</option>
                        </select>

                        {{-- Optional Filter Button (if you still want it) --}}
                        <button type="submit" class="af-btn af-btn-outline">
                            <i class="fa-solid fa-filter"></i>
                        </button>

                    </form>
                </div>
            </nav>

            <!-- ══════════════════════════════════════════
                                                                                         SECTION 1 — DASHBOARD
                                                                                    ══════════════════════════════════════════ -->
            <div id="tab-dashboard" class="af-section active">

                <div class="af-page-header">
                    <div>
                        <h2>Operational Overview</h2>
                        <p>Monitor audit volumes, distributions, and schedules in real-time.</p>
                    </div>
                    {{-- <div class="af-header-right">
                        <button class="af-btn af-btn-outline">
                            <i class="fa-regular fa-calendar"></i> Feb'26
                        </button>
                        <button class="af-btn af-btn-outline">
                            All Partners <i class="fa-solid fa-chevron-down" style="font-size:.65rem"></i>
                        </button>
                        <button class="af-btn af-btn-outline"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </div> --}}
                    {{-- <form method="GET" action="{{ route('getNewClientDashboard') }}"
                        class="d-flex align-items-center gap-2 mb-0" id="filter-form">

                        
                        <select name="audit_cycle_id" class="af-btn af-btn-outline" style="border:none; cursor:pointer;"
                            onchange="document.getElementById('filter-form').submit();">

                            @foreach ($auditCycle as $ac)
                                <option value="{{ $ac->id }}" {{ $currentCycleId == $ac->id ? 'selected' : '' }}>
                                    <i class="fa-regular fa-calendar"></i>
                                    {{ $ac->name }}
                                </option>
                            @endforeach

                        </select>

                        
                        <select name="audit_type" class="af-btn af-btn-outline" style="border:none; cursor:pointer;"
                            onchange="document.getElementById('filter-form').submit();">

                            <option value="all" {{ request('audit_type') == 'all' ? 'selected' : '' }}>All</option>
                            <option value="agency" {{ request('audit_type') == 'agency' ? 'selected' : '' }}>Agency</option>
                            <option value="agency_repo" {{ request('audit_type') == 'agency_repo' ? 'selected' : '' }}>
                                Agency Repo</option>
                            <option value="branch" {{ request('audit_type') == 'branch' ? 'selected' : '' }}>Branch</option>
                            <option value="branch_repo" {{ request('audit_type') == 'branch_repo' ? 'selected' : '' }}>
                                Branch Repo</option>
                            <option value="yard" {{ request('audit_type') == 'yard' ? 'selected' : '' }}>Yard</option>
                            <option value="yard_repo" {{ request('audit_type') == 'yard_repo' ? 'selected' : '' }}>Yard
                                Repo</option>
                        </select>

                        
                        <button type="submit" class="af-btn af-btn-outline">
                            <i class="fa-solid fa-filter"></i>
                        </button>

                    </form> --}}
                </div>

                <!-- KPI -->
                <div class="af-kpi-row">
                    @php
                        // Helper to compute percentage change
                        function getChange($current, $previous)
                        {
                            if ($previous == 0) {
                                return 0;
                            }
                            return (($current - $previous) / $previous) * 100;
                        }

                        // Format badge class and arrow
                        function getBadge($change)
                        {
                            if ($change > 0) {
                                return [
                                    'class' => 'badge-up',
                                    'arrow' => '↑',
                                    'formatted' => '+' . number_format($change, 1) . '%',
                                ];
                            } elseif ($change < 0) {
                                return [
                                    'class' => 'badge-down',
                                    'arrow' => '↓',
                                    'formatted' => number_format($change, 1) . '%',
                                ];
                            } else {
                                return ['class' => 'badge-stable', 'arrow' => '→', 'formatted' => '0%'];
                            }
                        }

                        // 1. Audit Allocation
                        $allocCurrent = $auditData['allocation_data']['currentCycleCount'] ?? 0;
                        $allocPrev = $auditData['allocation_data']['previousCycleCount'] ?? 0;
                        $allocChange = getChange($allocCurrent, $allocPrev);
                        $allocBadge = getBadge($allocChange);

                        // 2. Audit Submitted (handle client 74 specially)
                        if (auth()->user()->client_id == 74 && isset($auditData['tabs'])) {
                            $pPrev = $auditData['tabs']['physical']['previous']['audit_count'] ?? 0;
                            $pCurr = $auditData['tabs']['physical']['current']['audit_count'] ?? 0;
                            $vPrev = $auditData['tabs']['virtual']['previous']['audit_count'] ?? 0;
                            $vCurr = $auditData['tabs']['virtual']['current']['audit_count'] ?? 0;
                            $submittedPrev = $pPrev + $vPrev;
                            $submittedCurrent = $pCurr + $vCurr;
                        } else {
                            $submittedPrev = $auditData['previous_cycle_count'] ?? 0;
                            $submittedCurrent = $auditData['current_cycle_count'] ?? 0;
                        }
                        $submittedChange = getChange($submittedCurrent, $submittedPrev);
                        $submittedBadge = getBadge($submittedChange);

                        // 3. Audit Score
                        $scorePrev = $auditData['previous_cycle_score'] ?? 0;
                        $scoreCurrent = $auditData['current_cycle_score'] ?? 0;
                        $scorePrevRounded = round($scorePrev);
                        $scoreCurrentRounded = round($scoreCurrent);
                        $scoreChange = getChange($scoreCurrentRounded, $scorePrevRounded);
                        $scoreBadge = getBadge($scoreChange);

                        // 4. Action Planning (renamed from Overall Performance)
                        $actionPrev = $auditData['getActionPlanningData']['previous_cycle']['approved'] ?? 0;
                        $actionCurrent = $auditData['getActionPlanningData']['current_cycle']['approved'] ?? 0;
                        $actionChange = getChange($actionCurrent, $actionPrev);
                        $actionBadge = getBadge($actionChange);
                    @endphp

                    <!-- Audit Allocation -->
                    <div class="af-kpi">
                        <div class="af-kpi-label">
                            <i class="fa-solid fa-users" style="color:var(--blue);margin-right:5px"></i>Audit Allocation
                        </div>
                        <div class="af-kpi-value">{{ number_format($allocCurrent) }}</div>
                        <span class="af-kpi-badge {{ $allocBadge['class'] }}">
                            {{ $allocBadge['arrow'] }} {{ $allocBadge['formatted'] }}
                        </span>
                    </div>

                    <!-- Audit Submitted -->
                    <div class="af-kpi">
                        <div class="af-kpi-label">
                            <i class="fa-regular fa-file-lines" style="color:var(--blue);margin-right:5px"></i>Audit
                            Submitted
                        </div>
                        <div class="af-kpi-value">{{ number_format($submittedCurrent) }}</div>
                        <span class="af-kpi-badge {{ $submittedBadge['class'] }}">
                            {{ $submittedBadge['arrow'] }} {{ $submittedBadge['formatted'] }}
                        </span>
                    </div>

                    <!-- Avg. Audit Score -->
                    <div class="af-kpi">
                        <div class="af-kpi-label">
                            <i class="fa-solid fa-circle-check" style="color:var(--blue);margin-right:5px"></i>Avg. Audit
                            Score
                        </div>
                        <div class="af-kpi-value">{{ $scoreCurrentRounded }}%</div>
                        <span class="af-kpi-badge {{ $scoreBadge['class'] }}">
                            {{ $scoreBadge['arrow'] }} {{ $scoreBadge['formatted'] }}
                        </span>
                    </div>

                    <!-- Action Planning (replaces Overall Performance) -->
                    <div class="af-kpi">
                        <div class="af-kpi-label">
                            <i class="fa-regular fa-clock" style="color:var(--blue);margin-right:5px"></i>Action Planning
                        </div>
                        <div class="af-kpi-value">{{ number_format($actionCurrent) }}</div>
                        <span class="af-kpi-badge {{ $actionBadge['class'] }}">
                            {{ $actionBadge['arrow'] }} {{ $actionBadge['formatted'] }}
                        </span>
                    </div>
                </div>

                <!-- Charts Row -->
                {{-- <div class="af-two-col">
                    <div class="af-card">
                        <div class="af-card-title">Audit Type Distribution</div>
                        <div class="af-card-sub">Breakdown by audit domain and location type</div>
                        <div class="af-chart-wrap" style="height:220px;">
                            <canvas id="chartDonut"></canvas>
                        </div>
                        <div
                            style="display:flex;flex-wrap:wrap;gap:8px 16px;margin-top:14px;font-size:.72rem;font-weight:600;">
                            <span><span class="leg-dot" style="background:#2563EB"></span>Branch</span>
                            <span><span class="leg-dot" style="background:#60A5FA"></span>Branch Repo</span>
                            <span><span class="leg-dot" style="background:#16A34A"></span>Agency</span>
                            <span><span class="leg-dot" style="background:#F59E0B"></span>Agency Repo</span>
                            <span><span class="leg-dot" style="background:#7C3AED"></span>Yard Repo</span>
                        </div>
                    </div>

                    <div class="af-card">
                        <div class="af-card-title">Audit Lifecycle Status</div>
                        <div class="af-card-sub">Volume of audits across current operational stages</div>
                        @php
                            $lifecycle = [
                                ['QC Approved', 680, 900],
                                ['Submitted', 540, 900],
                                ['Rejected', 210, 900],
                                ['Drafted', 160, 900],
                                ['Pending', 90, 900],
                            ];
                        @endphp
                        @foreach ($lifecycle as [$lbl, $val, $mx])
                            <div class="lifecycle-row">
                                <div class="lifecycle-label">{{ $lbl }}</div>
                                <div class="lifecycle-bar-bg">
                                    <div class="lifecycle-bar-fill" style="width:{{ round(($val / $mx) * 100) }}%"></div>
                                </div>
                                <div class="lifecycle-count">{{ number_format($val) }}</div>
                            </div>
                        @endforeach
                    </div>
                </div> --}}
                @php
                    use Carbon\Carbon;

                    $currentMonth = Carbon::create($year, $month, 1);
                    $startDay = $currentMonth->copy()->startOfMonth()->startOfWeek();
                    $endDay = $currentMonth->copy()->endOfMonth()->endOfWeek();
                @endphp
                <!-- Calendar -->
                <div class="af-card" style="margin-bottom:24px;">
                    <div class="af-cal-header">
                        <div>
                            <div class="af-card-title">Monthly Audit Schedule</div>
                            <div style="font-size:.78rem;color:var(--gray-500)">Visual timeline of upcoming and past audit
                                allocations</div>
                        </div>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div class="cal-nav">
                                {{-- <button class="cal-nav-btn"><i class="fa-solid fa-chevron-left"
                                        style="font-size:.7rem"></i></button> --}}
                                <span class="cal-month">February 2026</span>
                                {{-- <button class="cal-nav-btn"><i class="fa-solid fa-chevron-right"
                                        style="font-size:.7rem"></i></button> --}}
                            </div>
                            {{-- <button class="af-btn af-btn-primary"><i class="fa-solid fa-plus"></i> Schedule Audit</button> --}}
                        </div>
                    </div>
                    <div style="overflow-x:auto;">
                        <table class="af-calendar">
                            <thead>
                                <tr>
                                    @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $d)
                                        <th>{{ $d }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @for ($date = $startDay; $date <= $endDay; $date->addDay())
                                    @if ($date->dayOfWeek == 0)
                                        <tr>
                                    @endif

                                    @php
                                        $formatted = $date->format('Y-m-d');
                                        $isOtherMonth = $date->month != $month;
                                        $isToday = $formatted == now()->format('Y-m-d');
                                        $isSunday = $date->dayOfWeek == 0;

                                        $assignedCount = isset($assignments[$formatted])
                                            ? count($assignments[$formatted])
                                            : 0;
                                        $submittedCount = isset($submitted[$formatted])
                                            ? count($submitted[$formatted])
                                            : 0;
                                        $hasEntry = $assignedCount > 0 || $submittedCount > 0;
                                    @endphp

                                    <td class="
                                        calendar-day
                                        {{ $isOtherMonth ? 'other-month' : '' }}
                                        {{ $isToday ? 'today-date' : '' }}
                                        {{ $isSunday ? 'sunday-date' : '' }}
                                        {{ $hasEntry ? 'has-entry' : '' }}
                                    "
                                        data-date="{{ $formatted }}">

                                        <!-- ✅ DAY NUMBER -->
                                        <div class="day-num">{{ $date->day }}</div>

                                        <!-- ✅ Assigned -->
                                        @if ($assignedCount > 0)
                                            <div class="cal-event ev-assigned">
                                                Assigned ({{ $assignedCount }})
                                            </div>
                                        @endif

                                        <!-- ✅ Submitted -->
                                        @if ($submittedCount > 0)
                                            <div class="cal-event ev-completed">
                                                Submitted ({{ $submittedCount }})
                                            </div>
                                        @endif

                                    </td>

                                    @if ($date->dayOfWeek == 6)
                                        </tr>
                                    @endif
                                @endfor
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal fade" id="auditModal" tabindex="-1">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content custom-modal">

                            <div class="modal-header custom-header">
                                <h5 class="modal-title">
                                    📅 Audit Details
                                    <span id="modalDate" class="modal-date"></span>
                                </h5>
                                {{-- <button type="button" class="btn-close btn-close-white"
                                    data-bs-dismiss="modal"></button> --}}
                            </div>

                            <div class="modal-body">

                                <div class="audit-section">
                                    <h6 class="section-title text-warning">
                                        Assigned Audits
                                    </h6>
                                    <div id="assignedList"></div>
                                </div>

                                <div class="audit-section mt-4">
                                    <h6 class="section-title text-success">
                                        Submitted Audits
                                    </h6>
                                    <div id="submittedList"></div>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
                <!-- Recent Submissions -->
                <div class="af-card">
                    <div class="af-card-title">Recent Audit Submissions</div>
                    <div class="af-card-sub">Last 5 audits uploaded by partners</div>

                    @forelse ($latestAudits as $i => $audit)
                        <div class="sub-row">
                            <div class="sub-num">{{ $i + 1 }}</div>

                            <div class="sub-info">
                                <div class="sub-name">
                                    {{ $audit->name ?? 'N/A' }} – {{ $audit->agency_code ?? 'N/A' }}
                                </div>
                                <div class="sub-agency">
                                    Submitted by: {{ $audit->present_auditor ?? 'N/A' }}
                                </div>
                            </div>

                            <div class="sub-meta">
                                {{-- Download Button --}}
                                <a href="{{ route('audit.downloadReports', ['audit_id' => $audit->id]) }}"
                                    class="af-btn af-btn-outline"
                                    style="text-decoration:none; padding:4px 10px; font-size:.75rem;">
                                    <i class="fa-solid fa-download"></i> Download
                                </a>

                                {{-- Time Ago --}}
                                <div class="sub-time">
                                    {{ \Carbon\Carbon::parse($audit->audit_date_by_aud ?? now())->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-muted p-2">No recent audits found</div>
                    @endforelse
                </div>
            </div><!-- /dashboard -->


            <!-- ══════════════════════════════════════════
                                                                                         SECTION 2 — TREND ANALYSIS
                                                                                    ══════════════════════════════════════════ -->
            <div id="tab-trend" class="af-section">

                <div class="af-page-header">
                    <div>
                        <h2>Strategic Performance Insights</h2>
                        <p>Analyze audit trends across multiple dimensions. Drill down into geographical compliance
                            patterns, identify key risk zones, and recognize top-performing regions.</p>
                    </div>
                    <div class="af-header-right">
                        <div style="text-align:right;">
                            <div
                                style="font-size:.7rem;color:var(--gray-500);font-weight:500;text-transform:uppercase;letter-spacing:.05em;">
                                Reporting Period</div>
                            <div style="font-size:.85rem;font-weight:700;">{{ $ac->name }}</div>
                        </div>
                        {{-- <button class="af-btn af-btn-primary" style="background:var(--green);border-color:var(--green);">
                            <i class="fa-solid fa-circle" style="font-size:.45rem"></i> Live Updates
                        </button> --}}
                    </div>
                </div>
                @php
                    $kpi = $auditData['kpiStats'];

                    $complianceChange =
                        $kpi['compliancePrevious'] > 0
                            ? round(
                                (($kpi['complianceCurrent'] - $kpi['compliancePrevious']) /
                                    $kpi['compliancePrevious']) *
                                    100,
                                1,
                            )
                            : 0;

                    $deviationChange =
                        $kpi['deviationPrevious'] > 0
                            ? round(
                                (($kpi['deviationCurrent'] - $kpi['deviationPrevious']) / $kpi['deviationPrevious']) *
                                    100,
                                1,
                            )
                            : 0;

                    $topZone = $auditData['topZone'];
                @endphp
                <div class="af-kpi-row">

                    <div class="af-kpi">
                        <div class="af-kpi-label">Total Audits Conducted</div>
                        <div class="af-kpi-value">{{ $kpi['totalCurrent'] }}</div>
                        <span class="af-kpi-badge badge-stable">
                            {{ $kpi['totalPrevious'] }} last cycle
                        </span>
                    </div>

                    <div class="af-kpi">
                        <div class="af-kpi-label">Overall Compliance</div>
                        <div class="af-kpi-value">{{ $kpi['complianceCurrent'] }}</div>
                        <span class="af-kpi-badge badge-up">
                            {{ $complianceChange }}% vs last cycle
                        </span>
                    </div>



                    <div class="af-kpi">
                        <div class="af-kpi-label">Critical Deviations</div>
                        <div class="af-kpi-value">{{ $kpi['deviationCurrent'] }}</div>
                        <span class="af-kpi-badge badge-down">
                            {{ $deviationChange }}% vs last cycle
                        </span>
                    </div>



                    <div class="af-kpi">
                        <div class="af-kpi-label">Top Zone Performance</div>
                        <div class="af-kpi-value" style="font-size:1.3rem;">
                            {{ $topZone['region_name'] }} ({{ $topZone['avg_score'] }}%)
                        </div>
                        <span class="af-kpi-badge badge-stable">
                            {{ $topZone['audit_count'] }} audits
                        </span>
                    </div>
                </div>

                <div class="af-two-col">
                    @php
                        $products = [];
                        $avgScores = [];
                        $auditCounts = [];

                        if (!empty($auditData['productDistribution'])) {
                            foreach ($auditData['productDistribution'] as $row) {
                                $products[] = $row->product_name;
                                $avgScores[] = round($row->avg_score, 1);
                                $auditCounts[] = $row->audit_count;
                            }
                        }

                        /* Normalize Audit Count (important for radar) */
                        $maxCount = max($auditCounts ?: [1]);
                        $normalizedCounts = [];

                        foreach ($auditCounts as $count) {
                            $normalizedCounts[] = round(($count / $maxCount) * 100, 1);
                        }
                    @endphp
                    @php
                        $zones = [];
                        $auditCounts = [];
                        $avgScores = [];

                        if (!empty($auditData['zonePerformance'])) {
                            foreach ($auditData['zonePerformance'] as $row) {
                                $zones[] = $row->zone_name;
                                $auditCounts[] = $row->audit_count;
                                $avgScores[] = round($row->avg_score, 1);
                            }
                        }
                    @endphp
                    <div class="af-card">
                        <div class="af-card-title">Product-wise Distribution</div>
                        <div class="af-card-sub">Performance comparison across loan categories</div>
                        <div class="af-chart-wrap" style="height:240px;">
                            <canvas id="chartRadar"></canvas>
                        </div>
                        <div style="display:flex;gap:16px;margin-top:10px;font-size:.72rem;font-weight:600;">
                            <span><span class="leg-dot"
                                    style="background:#2563EB55;border:2px solid #2563EB;width:12px;height:12px;border-radius:2px;display:inline-block;vertical-align:middle;margin-right:4px;"></span>Average
                                Score</span>
                            <span><span class="leg-dot"
                                    style="background:#60A5FA55;border:2px solid #60A5FA;width:12px;height:12px;border-radius:2px;display:inline-block;vertical-align:middle;margin-right:4px;"></span>Audit
                                Count</span>
                        </div>
                    </div>
                    <div class="af-card">
                        <div class="af-card-title">Zone-wise Performance</div>
                        <div class="af-card-sub">Volume and quality by geographic zones</div>
                        <div class="af-chart-wrap" style="height:240px;">
                            <canvas id="chartZone"></canvas>
                        </div>
                        <div style="display:flex;gap:16px;margin-top:10px;font-size:.72rem;font-weight:600;">
                            <span><span class="leg-dot" style="background:#2563EB"></span>Audits</span>
                            <span><span class="leg-dot" style="background:#16A34A"></span>Score %</span>
                        </div>
                    </div>
                </div>

                <div class="af-card" style="margin-bottom:24px;">
                    <div class="af-card-title">Parameter Compliance Overview</div>
                    <div class="af-card-sub">State-wise view</div>
                    <div class="af-map-section">
                        <div class="af-card">

                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                                <strong>India State-wise Compliance Map</strong>
                            </div>

                            <div id="indiaComplianceMap" style="height:260px;width:100%;"></div>

                            <div style="display:flex;align-items:center;gap:8px;margin-top:8px;">
                                <span style="font-size:.68rem;color:#DC2626;">0</span>
                                <div
                                    style="width:120px;height:8px;border-radius:4px;background:linear-gradient(to right,#DC2626,#F59E0B,#16A34A);">
                                </div>
                                <span style="font-size:.68rem;color:#16A34A;">1</span>
                            </div>

                        </div>
                        <div>
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
                                <div>
                                    <div style="font-weight:700;font-size:.9rem;">City-wise Compliance Details</div>
                                    <div style="font-size:.72rem;color:var(--gray-500);">Overview of complaints across
                                        major hubs</div>
                                </div>
                                <div style="display:flex;gap:6px;">
                                    <div style="display:flex;gap:6px;position:relative;">
                                        <button class="cal-nav-btn" id="filterToggle">
                                            <i class="fa-solid fa-filter" style="font-size:.65rem"></i>
                                        </button>

                                        <div id="filterDropdown"
                                            style="display:none;position:absolute;top:30px;right:0;
                background:#fff;border:1px solid #eee;
                padding:8px;border-radius:6px;
                box-shadow:0 4px 12px rgba(0,0,0,0.08);
                font-size:.75rem;z-index:10;">

                                            <div class="filter-option" data-filter="all">All</div>
                                            <div class="filter-option" data-filter="100">100% Compliant</div>
                                            <div class="filter-option" data-filter="50">≥ 50%</div>
                                            <div class="filter-option" data-filter="30">≥ 30%</div>
                                            <div class="filter-option" data-filter="0">Non-Compliant</div>
                                        </div>
                                    </div>
                                    <div style="position:relative;">
                                        <button class="cal-nav-btn" id="sortToggle">
                                            <i class="fa-solid fa-ellipsis-vertical" style="font-size:.65rem"></i>
                                        </button>

                                        <div id="sortDropdown"
                                            style="display:none;position:absolute;right:0;top:28px;
                background:#fff;border:1px solid #eee;
                padding:8px;border-radius:6px;
                box-shadow:0 4px 12px rgba(0,0,0,0.08);
                font-size:.75rem;z-index:10;">

                                            <div class="sort-option" data-sort="desc">Top → Bottom</div>
                                            <div class="sort-option" data-sort="asc">Bottom → Top</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @php
                                $cities = $auditData['cityCompliance'] ?? [];
                            @endphp

                            <div id="cityContainer" class="city-scroll-box">

                                @foreach ($cities as $city)
                                    <div class="city-row city-item" data-compliance="{{ $city->compliance_pct ?? 0 }}">

                                        <div>
                                            <span class="city-name">
                                                <i class="fa-solid fa-location-dot"
                                                    style="color:var(--blue);font-size:.75rem;margin-right:4px"></i>
                                                {{ $city->city }}
                                            </span>
                                            <span class="city-pct">
                                                {{ $city->compliance_pct ?? 0 }}% Compliance
                                            </span>
                                        </div>

                                        <div class="city-bar-bg">
                                            <div class="city-bar" style="width:{{ $city->compliance_pct ?? 0 }}%"></div>
                                        </div>

                                        <div class="city-counts">
                                            <span class="ok">✓ {{ $city->compliant }} Compliant</span>
                                            <span class="no">✗ {{ $city->non_compliant }} Non-Compliant</span>
                                        </div>

                                    </div>
                                @endforeach

                            </div>

                            <div style="font-size:.72rem;color:var(--gray-600);margin-top:8px;">
                                Total tracked cities: <strong>{{ count($cities) }}</strong>
                            </div>
                            {{-- @foreach ($cities as $city)
                                <div class="city-row">
                                    <div>
                                        <span class="city-name">
                                            <i class="fa-solid fa-location-dot"
                                                style="color:var(--blue);font-size:.75rem;margin-right:4px"></i>
                                            {{ $city->city }}
                                        </span>
                                        <span class="city-pct">
                                            {{ $city->compliance_pct ?? 0 }}% Compliance
                                        </span>
                                    </div>

                                    <div class="city-bar-bg">
                                        <div class="city-bar" style="width:{{ $city->compliance_pct ?? 0 }}%"></div>
                                    </div>

                                    <div class="city-counts">
                                        <span class="ok">✓ {{ $city->compliant }} Compliant</span>
                                        <span class="no">✗ {{ $city->non_compliant }} Non-Compliant</span>
                                    </div>
                                </div>
                            @endforeach

                            <div
                                style="font-size:.72rem;color:var(--gray-600);margin-top:12px;display:flex;justify-content:space-between;align-items:center;">
                                <span>Total tracked cities: <strong>{{ count($cities) }}</strong></span>
                                <a href="#" style="color:var(--blue);font-weight:600;text-decoration:none;">
                                    View Full Report →
                                </a>
                            </div> --}}
                        </div>
                    </div>
                </div>

                <div class="af-bottom-row">
                    @php
                        $states = [];
                        $stateScores = [];
                        $cumulative = [];

                        if (!empty($auditData['statePerformance'])) {
                            $total = 0;

                            foreach ($auditData['statePerformance'] as $row) {
                                $states[] = $row->state_name;
                                $stateScores[] = $row->avg_score;
                                $total += $row->avg_score;
                            }

                            $running = 0;
                            foreach ($stateScores as $score) {
                                $running += $score;
                                $cumulative[] = $total > 0 ? round(($running / $total) * 100, 1) : 0;
                            }
                        }
                    @endphp
                    <div class="af-card">
                        <div class="af-card-title">Pareto Analysis: State-wise Score</div>
                        <div class="af-card-sub">Identifying the vital contributors to overall audit quality</div>
                        <div class="af-chart-wrap" style="height:220px;"><canvas id="chartPareto"></canvas></div>
                        <div style="display:flex;gap:16px;margin-top:10px;font-size:.72rem;font-weight:600;">
                            <span><span class="leg-dot" style="background:#2563EB"></span>State Score</span>
                            <span><span class="leg-dot" style="background:#DC2626"></span>Cumulative %</span>
                        </div>
                    </div>
                    <div class="af-card">
                        <div class="af-card-title"><i class="fa-solid fa-trophy"
                                style="color:var(--orange);margin-right:5px"></i>Top Performers</div>
                        <div class="af-card-sub">Highest scores by City & State</div>
                        <div
                            style="display:flex;justify-content:space-between;font-size:.72rem;color:var(--gray-500);font-weight:600;padding:0 0 8px;border-bottom:1px solid var(--gray-200);text-transform:uppercase;letter-spacing:.04em;">
                            <span>Entity</span><span>Score</span><span>Status</span>
                        </div>
                        @php
                            $perf = $auditData['topPerformers'] ?? [];
                        @endphp

                        @foreach ($perf as $row)
                            @php
                                $score = $row->avg_score;
                                $status = 'Average';
                                $cls = 'perf-average';

                                if ($score >= 97) {
                                    $status = 'Excellent';
                                    $cls = 'perf-excellent';
                                } elseif ($score >= 90) {
                                    $status = 'Good';
                                    $cls = 'perf-good';
                                }
                            @endphp

                            <div class="perf-row">
                                <div>
                                    <div class="perf-entity">{{ $row->city }}</div>
                                    <div class="perf-state">{{ $row->state }}</div>
                                </div>
                                <div class="perf-score">{{ $score }}%</div>
                                <span class="perf-badge {{ $cls }}">{{ $status }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="af-quick-links">
                    <div class="af-quick-link" onclick="switchTab('dashboard')">
                        <div class="af-quick-link-icon"><i class="fa-solid fa-table-columns"></i></div>
                        <div style="flex:1;">
                            <div class="af-quick-link-title">Review Detailed Metrics</div>
                            <div class="af-quick-link-sub">Head back to the Partner Dashboard for granular audit-level
                                data.</div>
                        </div>
                        <i class="fa-solid fa-arrow-up-right-from-square" style="color:var(--gray-400);"></i>
                    </div>
                    <div class="af-quick-link" onclick="switchTab('action')">
                        <div class="af-quick-link-icon"><i class="fa-solid fa-clipboard-list"></i></div>
                        <div style="flex:1;">
                            <div class="af-quick-link-title">Start Action Planning</div>
                            <div class="af-quick-link-sub">Resolve non-compliance issues identified in these trends.</div>
                        </div>
                        <i class="fa-solid fa-arrow-up-right-from-square" style="color:var(--gray-400);"></i>
                    </div>
                </div>
            </div><!-- /trend -->


            <!-- ══════════════════════════════════════════
                                                                                         SECTION 3 — ACTION PLANNING
                                                                                    ══════════════════════════════════════════ -->
            <div id="tab-action" class="af-section">

                <div class="af-page-header">
                    <div>
                        <h2>Action Planning</h2>
                    </div>
                </div>

                {{-- <div class="af-filters">
                    <label><i class="fa-solid fa-filter" style="margin-right:4px;"></i>Filters:</label>
                    <input class="af-input-date" type="text" value="Sep 01 – Sep 30, 2023" readonly>
                    <select class="af-select">
                        <option>All Zones</option>
                        <option>North</option>
                        <option>South</option>
                        <option>East</option>
                        <option>West</option>
                    </select>
                    <select class="af-select">
                        <option>All States</option>
                        <option>Maharashtra</option>
                        <option>Karnataka</option>
                        <option>Tamil Nadu</option>
                    </select>
                    <select class="af-select">
                        <option>All Agencies</option>
                        <option>North Axis Agency</option>
                        <option>Prime Financials</option>
                    </select>
                    <button class="af-btn-reset">Reset</button>
                    <button class="af-btn-apply">Apply Filters</button>
                </div> --}}

                @php
                    $kpis = $auditData['actionKpis'] ?? [
                        'total' => 0,
                        'open' => 0,
                        'approved' => 0,
                        'rejected' => 0,
                    ];
                @endphp

                <div class="af-action-kpi-row">

                    <!-- Total Issues -->
                    <div class="af-action-kpi bl-blue">
                        <div class="ak-label">
                            Total Issues
                            <span
                                style="font-size:.65rem;background:var(--gray-100);
                         padding:1px 7px;border-radius:10px;
                         margin-left:4px;font-weight:700;">
                                Total
                            </span>
                        </div>
                        <div class="ak-value">
                            {{ number_format($kpis['total']) }}
                        </div>
                        <div class="ak-sub">
                            Based on unsatisfactory findings
                        </div>
                    </div>

                    <!-- Open Backlog -->
                    <div class="af-action-kpi bl-orange">
                        <div class="ak-label">
                            Open Backlog
                            <i class="fa-regular fa-clock" style="margin-left:4px;"></i>
                        </div>
                        <div class="ak-value" style="color:var(--orange);">
                            {{ number_format($kpis['open']) }}
                        </div>
                        <div class="ak-sub">
                            Pending approvals
                        </div>
                    </div>

                    <!-- Approved -->
                    <div class="af-action-kpi bl-green">
                        <div class="ak-label">
                            Approved / Resolved
                            <i class="fa-solid fa-circle-check" style="margin-left:4px;color:var(--green);"></i>
                        </div>
                        <div class="ak-value" style="color:var(--green);">
                            {{ number_format($kpis['approved']) }}
                        </div>
                        <div class="ak-sub good">
                            Successfully resolved
                        </div>
                    </div>

                    <!-- Rejected -->
                    <div class="af-action-kpi bl-red">
                        <div class="ak-label">
                            Rejected Actions
                            <i class="fa-solid fa-circle-xmark" style="margin-left:4px;color:var(--red);"></i>
                        </div>
                        <div class="ak-value" style="color:var(--red);">
                            {{ number_format($kpis['rejected']) }}
                        </div>
                        <div class="ak-sub rev">
                            Need revision
                        </div>
                    </div>

                </div>

                <div class="af-card" style="margin-bottom:22px;">
                    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:16px;">
                        <div class="af-inner-tabs">
                            <div class="af-inner-tabs">
                                <button class="af-inner-tab active" data-status="open">
                                    Open Issues ({{ number_format($kpis['open'] ?? 0) }})
                                </button>

                                <button class="af-inner-tab" data-status="approved">
                                    Approved ({{ number_format($kpis['approved'] ?? 0) }})
                                </button>

                                <button class="af-inner-tab" data-status="rejected">
                                    Rejected ({{ number_format($kpis['rejected'] ?? 0) }})
                                </button>
                            </div>
                        </div>
                        <div style="flex:1;"></div>
                        <div class="af-searchbox">
                            <i class="fa-solid fa-magnifying-glass" style="color:var(--gray-400);font-size:.8rem;"></i>
                            <input placeholder="Search agency ID or agency name">
                        </div>
                        <button class="af-btn af-btn-outline" type="button" data-toggle="modal"
                            data-target="#openPointersModal"><i class="fa-solid fa-download"></i> Export</button>
                        {{-- <button  class="btn btn-primary btn-sm me-3 h-100" >
                        Open Pointers Dump
                    </button> --}}
                        {{-- <button class="af-btn af-btn-primary"><i class="fa-solid fa-plus"></i> New Action</button> --}}
                    </div>

                    <div style="overflow:auto;max-height:350px;">
                        <table class="af-table">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Audit ID</th>
                                    <th>Agency ID</th>
                                    <th>Agency Name</th>
                                    <th>Issue Description</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="issueTableBody">
                                @forelse($auditData['actionIssues'] as $issue)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $issue->audit_id }}</td>
                                        <td>{{ $issue->agency_id }}</td>
                                        <td>{{ $issue->agency_name }}</td>
                                        <td>{{ $issue->issue_description }}</td>
                                        <td>{{ $issue->due_date ?? '-' }}</td>
                                        <td>
                                            <span class="status-badge status-{{ strtolower($issue->status) }}">
                                                {{ ucfirst($issue->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <button
                                                style="border:none;background:none;cursor:pointer;color:var(--gray-400);font-size:1.1rem;">
                                                <div class="mt-2">
                                                    <a href="/audit-closure/download/{{ $issue->audit_id }}"
                                                        class="af-btn af-btn-outline"
                                                        style="text-decoration:none; padding:4px 10px; font-size:.75rem;">
                                                        <i class="fa-solid fa-download"></i> Download
                                                    </a>
                                                </div>
                                            </button>
                                        </td>
                                    </tr>

                                @empty
                                    <tr>
                                        <td colspan="6" style="text-align:center;padding:20px;">
                                            No issues found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{-- <div
                        style="display:flex;align-items:center;justify-content:space-between;padding-top:12px;flex-wrap:wrap;gap:8px;">
                        <span style="font-size:.75rem;color:var(--gray-600);">
                            Showing {{ $auditData['actionIssues']->firstItem() ?? 0 }}
                            –
                            {{ $auditData['actionIssues']->lastItem() ?? 0 }}
                            of {{ $auditData['actionIssues']->total() ?? 0 }} issues
                        </span>
                        <div class="tbl-pager">
                            {{ $auditData['actionIssues']->links() }}
                        </div>
                    </div> --}}
                </div>

                <div class="af-card">
                    <div
                        style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:16px;">
                        <div>
                            <div class="af-card-title">5-Month Resolution Trend</div>
                            <div style="font-size:.78rem;color:var(--gray-500);">Visualizing the progression of action
                                items across statuses</div>
                        </div>
                        {{-- <button class="af-btn af-btn-outline" style="font-size:.75rem;padding:6px 12px;">Monthly Macro
                            View</button> --}}
                    </div>
                    <div style="position:relative;height:220px;"><canvas id="chartResolution"></canvas></div>
                    <div style="display:flex;gap:20px;margin-top:12px;font-size:.72rem;font-weight:600;flex-wrap:wrap;">
                        <span><span class="leg-line" style="background:#2563EB;"></span>Open Issues</span>
                        <span><span class="leg-line" style="background:#16A34A;"></span>Approved Solutions</span>
                        <span><span class="leg-line" style="background:#DC2626;"></span>Rejected Actions</span>
                    </div>
                </div>
            </div><!-- /action -->

            <div class="af-footer">
                © 2026 AuditFlow. All rights reserved. System Status:
                <span style="color:var(--green);font-weight:600;">Operational</span>
            </div>

        </div><!-- /af-wrap -->
    </div><!-- /af-root -->



    {{-- model for dump --}}
    <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="myModalLabel">  
        <div class="modal-dialog modal-xl modal-dialog-centered"> <!-- Made modal extra large -->
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="myModalLabel">Audit Data Download</h5>
                    <button type="button" class="btn-close btn-close-white" data-dismiss="modal"
                        aria-label="Close"></button> <!-- Add Close button -->
                </div>
                <form method="GET" action="{{ route('auditdumpdownload') }}" autocomplete="off">
                    <div class="modal-body">
                        <div class="row">
                            @csrf
                            <div class="col-md-3 form-group">
                                <label>Start Date*</label>
                                <input name="start_date" type="date" data-date-format="yyyy-mm-dd"
                                    class="form-control" placeholder="Select Start Date" required />
                            </div>

                            <div class="col-md-3 form-group">
                                <label>End Date*</label>
                                <input name="end_date" type="date" data-date-format="yyyy-mm-dd" class="form-control"
                                    placeholder="Select End Date" required />
                            </div>

                            <div class="col-md-3 form-group">
                                <label>Action*</label>
                                <button type="submit" class="btn btn-primary form-control">Download</button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-end">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="openPointersModal" tabindex="-1" aria-labelledby="openPointerslLabel">
        <div class="modal-dialog modal-xl modal-dialog-centered"> <!-- Made modal extra large -->
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="openPointerslLabel">Open Pointers (unsatisfactory sub-parameters)
                        sheet</h5>
                    <button type="button" class="btn-close btn-close-white" data-dismiss="modal"
                        aria-label="Close"></button> <!-- Add Close button -->
                </div>
                <form method="GET" action="{{ route('openPointersDump') }}" autocomplete="off">
                    <div class="modal-body">
                        <div class="row">
                            @csrf
                            <div class="col-md-3 form-group">
                                <label>Start Date*</label>
                                <input name="start_date" type="date" data-date-format="yyyy-mm-dd"
                                    class="form-control" placeholder="Select Start Date" required />
                            </div>

                            <div class="col-md-3 form-group">
                                <label>End Date*</label>
                                <input name="end_date" type="date" data-date-format="yyyy-mm-dd" class="form-control"
                                    placeholder="Select End Date" required />
                            </div>

                            <div class="col-md-3 form-group">
                                <label>Action*</label>
                                <button type="submit" class="btn btn-primary form-control">Download</button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-end">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ScheduleAuditExport" tabindex="-1" aria-labelledby="ScheduleAuditExportModalLabel">
        <div class="modal-dialog modal-xl modal-dialog-centered"> <!-- Made modal extra large -->
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="ScheduleAuditExportModalLabel">Schedule Audits Data Download</h5>
                    <button type="button" class="btn-close btn-close-white" data-dismiss="modal"
                        aria-label="Close"></button> <!-- Add Close button -->
                </div>
                <form method="GET" action="{{ route('scheduleAuditDownload') }}" autocomplete="off">
                    <div class="modal-body">
                        <div class="row">
                            @csrf
                            <div class="col-md-3 form-group">
                                <label>Start Date*</label>
                                <input name="start_date" type="date" data-date-format="yyyy-mm-dd"
                                    class="form-control" placeholder="Select Start Date" required />
                            </div>

                            <div class="col-md-3 form-group">
                                <label>End Date*</label>
                                <input name="end_date" type="date" data-date-format="yyyy-mm-dd" class="form-control"
                                    placeholder="Select End Date" required />
                            </div>

                            <div class="col-md-3 form-group">
                                <label>Action*</label>
                                <button type="submit" class="btn btn-primary form-control">Download</button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-end">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://code.highcharts.com/highmaps.js"></script>
    <script src="https://code.highcharts.com/mapdata/countries/in/in-all.js"></script>
    <script>
        console.log("Chart loaded:", typeof Chart);
    </script>


    <script>
        let complianceData = @json($auditData['stateCompliance']);
    </script>

  <script>
$(document).on('click', '.af-inner-tab', function () {

    $('.af-inner-tab').removeClass('active');
    $(this).addClass('active');

    let status = $(this).data('status');

    $.ajax({
        url: "{{ url('get-action-issues') }}",
        type: "GET",
        data: {
            status: status,
            cycle_id: "{{ $currentCycleId }}"
        },
        success: function(res) {

            let rows = '';

            if (res.length > 0) {

                res.forEach(function(issue, index) {

                    rows += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${issue.audit_id}</td>
                        <td>${issue.agency_id}</td>
                        <td>${issue.agency_name}</td>
                        <td>${issue.issue_description ?? '-'}</td>
                        <td>${issue.due_date ?? '-'}</td>
                        <td>
                            <span class="status-badge status-${issue.status.toLowerCase()}">
                                ${issue.status}
                            </span>
                        </td>
                        <td>
                            <a href="/audit-closure/download/${issue.audit_id}"
                               class="af-btn af-btn-outline"
                               style="text-decoration:none;padding:4px 10px;font-size:.75rem;">
                                <i class="fa-solid fa-download"></i> Download
                            </a>
                        </td>
                    </tr>`;
                });

            } else {

                rows = `
                <tr>
                    <td colspan="8" style="text-align:center;padding:20px;">
                        No issues found
                    </td>
                </tr>`;
            }

            $('#issueTableBody').html(rows);
        }
    });

});
</script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const mapData = Highcharts.maps['countries/in/in-all'];

            Highcharts.mapChart('indiaComplianceMap', {

                chart: {
                    map: mapData
                },

                title: {
                    text: null
                },

                colorAxis: {
                    min: 0,
                    max: 1,
                    stops: [
                        [0, '#DC2626'], // red
                        [0.5, '#F59E0B'], // yellow
                        [1, '#16A34A'] // green
                    ]
                },

                series: [{
                    data: mapDataValues, // your compliance data array
                    mapData: mapData,
                    joinBy: ['name', 'name'],
                    name: 'Compliance',

                    states: {
                        hover: {
                            color: '#2563EB'
                        }
                    },

                    dataLabels: {
                        enabled: false
                    }
                }]

            });

        });
    </script>
    <!-- ══ SCRIPTS ══════════════════════════════════════════════════ -->
    <script>
        // Tab switching
        function switchTab(name) {
            document.querySelectorAll('.af-tab-btn').forEach(b => b.classList.toggle('active', b.dataset.tab === name));
            document.querySelectorAll('.af-section').forEach(s => s.classList.toggle('active', s.id === 'tab-' + name));
        }
        document.querySelectorAll('.af-tab-btn').forEach(btn => btn.addEventListener('click', () => switchTab(btn.dataset
            .tab)));

        // Inner tabs
        document.querySelectorAll('.af-inner-tab').forEach(btn => btn.addEventListener('click', function() {
            document.querySelectorAll('.af-inner-tab').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        }));

        // Chart defaults
        Chart.defaults.font.family = "'DM Sans',sans-serif";
        Chart.defaults.font.size = 11;
        Chart.defaults.color = '#475569';

        // 1. Donut
        new Chart(document.getElementById('chartDonut'), {
            type: 'doughnut',
            data: {
                labels: ['Branch', 'Branch Repo', 'Agency', 'Agency Repo', 'Yard Repo'],
                datasets: [{
                    data: [35, 25, 15, 14, 11],
                    backgroundColor: ['#2563EB', '#60A5FA', '#16A34A', '#F59E0B', '#7C3AED'],
                    borderWidth: 0,
                    hoverOffset: 8
                }]
            },
            options: {
                cutout: '65%',
                plugins: {
                    legend: {
                        display: false
                    }
                },
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // 2. Radar
        // new Chart(document.getElementById('chartRadar'), {
        //     type: 'radar',
        //     data: {
        //         labels: ['Home Loan', 'Auto Loan', 'Gold Loan', 'Personal Loan', 'Business Loan'],
        //         datasets: [{
        //                 label: 'Average Score',
        //                 data: [82, 75, 88, 70, 78],
        //                 backgroundColor: 'rgba(37,99,235,.15)',
        //                 borderColor: '#2563EB',
        //                 pointBackgroundColor: '#2563EB',
        //                 borderWidth: 2
        //             },
        //             {
        //                 label: 'Audit Count',
        //                 data: [70, 65, 80, 60, 68],
        //                 backgroundColor: 'rgba(96,165,250,.10)',
        //                 borderColor: '#60A5FA',
        //                 pointBackgroundColor: '#60A5FA',
        //                 borderWidth: 2
        //             }
        //         ]
        //     },
        //     options: {
        //         responsive: true,
        //         maintainAspectRatio: false,
        //         scales: {
        //             r: {
        //                 beginAtZero: true,
        //                 max: 100,
        //                 ticks: {
        //                     stepSize: 25,
        //                     display: false
        //                 },
        //                 grid: {
        //                     color: '#E2E8F0'
        //                 },
        //                 pointLabels: {
        //                     font: {
        //                         size: 11
        //                     }
        //                 }
        //             }
        //         },
        //         plugins: {
        //             legend: {
        //                 display: false
        //             }
        //         }
        //     }
        // });

        // 3. Zone Bar+Line
        // new Chart(document.getElementById('chartZone'), {
        //     type: 'bar',
        //     data: {
        //         labels: ['North', 'South', 'East', 'West', 'Central'],
        //         datasets: [{
        //                 label: 'Audits',
        //                 data: [450, 530, 310, 280, 310],
        //                 backgroundColor: '#2563EB',
        //                 borderRadius: 5,
        //                 yAxisID: 'y'
        //             },
        //             {
        //                 label: 'Score %',
        //                 data: [78, 89, 75, 72, 80],
        //                 type: 'line',
        //                 borderColor: '#16A34A',
        //                 backgroundColor: 'transparent',
        //                 pointBackgroundColor: '#16A34A',
        //                 borderWidth: 2.5,
        //                 yAxisID: 'y1',
        //                 tension: .35
        //             }
        //         ]
        //     },
        //     options: {
        //         responsive: true,
        //         maintainAspectRatio: false,
        //         scales: {
        //             y: {
        //                 beginAtZero: true,
        //                 max: 600,
        //                 grid: {
        //                     color: '#F1F5F9'
        //                 },
        //                 position: 'left'
        //             },
        //             y1: {
        //                 beginAtZero: true,
        //                 max: 100,
        //                 grid: {
        //                     display: false
        //                 },
        //                 position: 'right'
        //             }
        //         },
        //         plugins: {
        //             legend: {
        //                 display: false
        //             }
        //         }
        //     }
        // });

        // 4. Pareto
        // new Chart(document.getElementById('chartPareto'), {
        //     type: 'bar',
        //     data: {
        //         labels: ['Maharashtra', 'Karnataka', 'Tamil Nadu', 'Gujarat', 'Delhi', 'Others'],
        //         datasets: [{
        //                 label: 'State Score',
        //                 data: [92, 89, 86, 84, 82, 45],
        //                 backgroundColor: ['#2563EB', '#2563EB', '#2563EB', '#2563EB', '#2563EB', '#DC2626'],
        //                 borderRadius: 5,
        //                 yAxisID: 'y'
        //             },
        //             {
        //                 label: 'Cumulative %',
        //                 data: [18, 36, 53, 68, 82, 100],
        //                 type: 'line',
        //                 borderColor: '#DC2626',
        //                 backgroundColor: 'transparent',
        //                 pointBackgroundColor: '#DC2626',
        //                 borderWidth: 2.5,
        //                 yAxisID: 'y1',
        //                 tension: .2
        //             }
        //         ]
        //     },
        //     options: {
        //         responsive: true,
        //         maintainAspectRatio: false,
        //         scales: {
        //             y: {
        //                 beginAtZero: true,
        //                 max: 100,
        //                 grid: {
        //                     color: '#F1F5F9'
        //                 },
        //                 position: 'left'
        //             },
        //             y1: {
        //                 beginAtZero: true,
        //                 max: 100,
        //                 grid: {
        //                     display: false
        //                 },
        //                 position: 'right'
        //             }
        //         },
        //         plugins: {
        //             legend: {
        //                 display: false
        //             }
        //         }
        //     }
        // });

        // 5. Resolution Trend
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            if (typeof Chart === "undefined") {
                console.error("Chart.js not loaded");
                return;
            }

            const canvas = document.getElementById('chartPareto');
            if (!canvas) {
                console.warn("chartPareto not found");
                return;
            }

            const labels = @json($states ?? []);
            const scores = @json($stateScores ?? []);
            const cumulative = @json($cumulative ?? []);

            if (!labels.length) return;

            new Chart(canvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                            label: 'State Score',
                            data: scores,
                            backgroundColor: '#2563EB',
                            borderRadius: 5,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Cumulative %',
                            data: cumulative,
                            type: 'line',
                            borderColor: '#DC2626',
                            backgroundColor: 'transparent',
                            pointBackgroundColor: '#DC2626',
                            borderWidth: 2.5,
                            yAxisID: 'y1',
                            tension: 0.2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            position: 'left'
                        },
                        y1: {
                            beginAtZero: true,
                            max: 100,
                            position: 'right',
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            if (typeof Chart === "undefined") {
                console.error("Chart.js not loaded");
                return;
            }

            const canvas = document.getElementById('chartResolution');

            if (!canvas) {
                console.warn("chartResolution canvas not found");
                return;
            }

            const ctx = canvas.getContext('2d');

            if (!ctx) {
                console.warn("Canvas context not available");
                return;
            }

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($auditData['resolutionTrend']['months'] ?? []),
                    datasets: [{
                            label: 'Open Issues',
                            data: @json($auditData['resolutionTrend']['open'] ?? []),
                            borderColor: '#2563EB',
                            tension: 0.4
                        },
                        {
                            label: 'Approved Solutions',
                            data: @json($auditData['resolutionTrend']['approved'] ?? []),
                            borderColor: '#16A34A',
                            tension: 0.4
                        },
                        {
                            label: 'Rejected Actions',
                            data: @json($auditData['resolutionTrend']['rejected'] ?? []),
                            borderColor: '#DC2626',
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            if (typeof $ === 'undefined') {
                console.error('jQuery is not loaded!');
                return;
            }

            $(document).on('click', '.calendar-day', function() {

                let date = $(this).data('date');

                if (!date) return;

                $('#modalDate').text(date);

                $.ajax({
                    url: "{{ route('get.audit.by.date') }}",
                    type: "GET",
                    data: {
                        date: date
                    },
                    success: function(response) {

                        let assignedHtml = '';
                        let submittedHtml = '';

                        // 🔵 Assigned Audits
                        if (response.assigned?.length > 0) {

                            response.assigned.forEach(a => {
                                assignedHtml += `
                <div class="audit-card assigned-card">
            <div class="row w-100">

                <div class="col-md-4">
                    <strong>Agency</strong><br>
                    ${a.final_agency_name ?? '-'}
                </div>

                <div class="col-md-4">
                    <strong>Product</strong><br>
                    ${a.product_name ?? '-'}
                </div>

                <div class="col-md-4">
                    <strong>Location</strong><br>
                    ${a.location ?? '-'}
                </div>

            </div>
        </div>
            `;
                            });

                        } else {
                            assignedHtml = '<p class="text-muted">No Assigned Audits</p>';
                        }

                        // 🟢 Submitted Audits
                        if (response.submitted?.length > 0) {

                            response.submitted.forEach(a => {
                                submittedHtml += `
                 <div class="audit-card submitted-card d-flex justify-content-between align-items-center">
            
            <div class="audit-info">
                <div><strong>Agency:</strong> ${a.agency_name ?? '-'}</div>
                <div><strong>Score:</strong> 
                    <span class="badge ">${a.overall_score ?? '-'}</span>
                </div>
                <div><strong>Auditor:</strong> ${a.present_auditor ?? '-'}</div>
            </div>

            <div class="audit-action">
                <a href="/audit-reports/download/${a.id}"
                   class="btn btn-sm btn-outline-primary">
                   <i class="fa-solid fa-download"></i> Download
                </a>
            </div>

        </div>
    `;
                            });

                        } else {
                            submittedHtml = '<p class="text-muted">No Submitted Audits</p>';
                        }

                        $('#assignedList').html(assignedHtml);
                        $('#submittedList').html(submittedHtml);

                        let modal = new bootstrap.Modal(document.getElementById('auditModal'));
                        modal.show();
                    },
                    error: function(xhr) {
                        console.error('AJAX Error:', xhr.responseText);
                    }
                });

            });

        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            if (typeof Chart === "undefined") return;

            const canvas = document.getElementById('chartRadar');
            if (!canvas) return;

            new Chart(canvas, {
                type: 'radar',
                data: {
                    labels: @json($products),
                    datasets: [{
                            label: 'Average Score',
                            data: @json($avgScores),
                            backgroundColor: 'rgba(37,99,235,.15)',
                            borderColor: '#2563EB',
                            pointBackgroundColor: '#2563EB',
                            borderWidth: 2
                        },
                        {
                            label: 'Audit Count (Normalized)',
                            data: @json($auditCounts),
                            backgroundColor: 'rgba(96,165,250,.15)',
                            borderColor: '#60A5FA',
                            pointBackgroundColor: '#60A5FA',
                            borderWidth: 2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        r: {
                            beginAtZero: true,
                            max: 100,
                            grid: {
                                color: '#E2E8F0'
                            },
                            angleLines: {
                                color: '#E2E8F0'
                            },
                            pointLabels: {
                                font: {
                                    size: 11
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            if (typeof Chart === "undefined") return;

            const canvas = document.getElementById('chartZone');
            if (!canvas) return;

            const labels = @json($zones ?? []);
            const audits = @json($auditCounts ?? []);
            const scores = @json($avgScores ?? []);

            if (!labels.length) return;

            new Chart(canvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                            label: 'Audits',
                            data: audits,
                            backgroundColor: '#2563EB',
                            borderRadius: 5,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Score %',
                            data: scores,
                            type: 'line',
                            borderColor: '#16A34A',
                            backgroundColor: 'transparent',
                            borderWidth: 2.5,
                            yAxisID: 'y1',
                            tension: 0.35
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            position: 'left'
                        },
                        y1: {
                            beginAtZero: true,
                            max: 100,
                            position: 'right',
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const container = document.getElementById('cityContainer');
            const cityItems = document.querySelectorAll('.city-item');

            const filterToggle = document.getElementById('filterToggle');
            const filterDropdown = document.getElementById('filterDropdown');
            const filterOptions = document.querySelectorAll('.filter-option');

            const sortToggle = document.getElementById('sortToggle');
            const sortDropdown = document.getElementById('sortDropdown');
            const sortOptions = document.querySelectorAll('.sort-option');

            /* ===========================
               FILTER DROPDOWN
            ============================ */

            if (filterToggle && filterDropdown) {
                filterToggle.addEventListener('click', function() {
                    filterDropdown.style.display =
                        filterDropdown.style.display === 'none' ? 'block' : 'none';
                });
            }

            filterOptions.forEach(option => {
                option.addEventListener('click', function() {

                    const filterValue = this.dataset.filter;

                    cityItems.forEach(item => {

                        const compliance = parseFloat(item.dataset.compliance);
                        let show = false;

                        switch (filterValue) {
                            case 'all':
                                show = true;
                                break;
                            case '100':
                                show = compliance === 100;
                                break;
                            case '50':
                                show = compliance >= 50;
                                break;
                            case '30':
                                show = compliance >= 30;
                                break;
                            case '0':
                                show = compliance < 100;
                                break;
                        }

                        item.style.display = show ? 'block' : 'none';
                    });

                    filterDropdown.style.display = 'none';
                });
            });

            /* ===========================
               SORT DROPDOWN
            ============================ */

            if (sortToggle && sortDropdown) {
                sortToggle.addEventListener('click', function() {
                    sortDropdown.style.display =
                        sortDropdown.style.display === 'none' ? 'block' : 'none';
                });
            }

            sortOptions.forEach(option => {
                option.addEventListener('click', function() {

                    const sortType = this.dataset.sort;

                    const items = Array.from(container.querySelectorAll('.city-item'));

                    items.sort((a, b) => {
                        const valA = parseFloat(a.dataset.compliance);
                        const valB = parseFloat(b.dataset.compliance);

                        return sortType === 'asc' ?
                            valA - valB :
                            valB - valA;
                    });

                    items.forEach(item => container.appendChild(item));

                    sortDropdown.style.display = 'none';
                });
            });

        });
    </script>
@endsection
