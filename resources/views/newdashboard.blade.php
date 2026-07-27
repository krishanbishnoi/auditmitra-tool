@extends('layouts.master')

@section('title', 'AuditFlow – Dashboard')
@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ URL::asset('/public/assets/css/dashboard-style.css') }}">
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

        .dashboardExportBtn {
            padding: 0px 7px 0px 7px;
            border-radius: 8px
        }

        #indiaMap {
            width: 100%;
            height: 500px;
            min-height: 500px;
        }
    </style>

    <style>
        .tableDiv {
            max-height: 340px;
            /* approx 5-6 rows */
            overflow-y: auto;
            overflow-x: auto;
        }

        .blur-content {
            filter: blur(2px);
            pointer-events: none;
            user-select: none;
            opacity: 0.8;
        }

        .blur-overlay {
            position: absolute;
            inset: 0;
            /* background: rgba(255, 255, 255, 0.6); */
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 100;
            border-radius: 10px;
        }

        .overlay-message {
            text-align: center;
            background: #fff;
            padding: 25px 35px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, .15);
        }
    </style>

@endsection

@section('content')
    <script>
        const downloadRoute = "{{ route('audit.downloadReports', ['audit_id' => '__ID__']) }}";
    </script>
     @php
        $allocatedmodule = App\Helpers\Helper::allocatedmodulelist();
    @endphp
    {{-- <script>
        let complianceData = @json($auditData['stateCompliance']);
    </script> --}}

    <div class="main ml-0">
        <div class="topBar gap-5">
            <select class="form-select d-block d-lg-none w-50 fs-8" id="mobileTabSelect">
                <option value="#dashboard-tab-pane">Dashboard</option>
                <option value="#trend-tab-pane">Trend Analysis</option>
                <option value="#planning-tab-pane">Action Planning</option>
            </select>
            <ul class="nav nav-tabs d-none d-lg-flex border-0">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#dashboard-tab-pane"
                        type="button">Dashboard</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#trend-tab-pane" type="button">Trend
                        Analysis</button>
                </li>
                <li class="nav-item">
                    @if (auth()->user()->client_id == 298)
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#planning-tab-pane" type="button"
                            disabled>Action Planning</button>
                    @else
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#planning-tab-pane"
                            type="button">Action Planning</button>
                    @endif
                </li>
            </ul>

            <div class="d-flex align-items-center gap-2">

                @if (auth()->check() && auth()->user()->client_id == 13)
                    <button type="submit" class="btn btn-primary btn-sm"
                        style="padding-top: 7px;padding-bottom: 7px;line-height:normal;" onclick="switchUser(1);">
                        Audit Agency
                    </button>
                    <button type="submit" class="btn btn-danger btn-sm"
                        style="padding-top: 7px;padding-bottom: 7px;line-height:normal;" onclick="switchUser(2);">
                        Quality Auditor
                    </button>
                @endif

                @if (auth()->check() && auth()->user()->client_id == 2)
                    <button type="submit" class="btn btn-primary btn-sm"
                        style="padding-top: 7px;padding-bottom: 7px;line-height:normal;" onclick="switchUser(3);">
                        Audit Agency
                    </button>
                    <button type="submit" class="btn btn-danger btn-sm"
                        style="padding-top: 7px;padding-bottom: 7px;line-height:normal;" onclick="switchUser(4);">
                        Quality Auditor
                    </button>
                @endif
                <div class="mb-3">
                    @if (request('audit_type') && request('audit_type') != 'all')
                        <span class="badge text-bg-secondary">
                            Audit Type: {{ ucwords(str_replace('_', ' ', request('audit_type'))) }}
                        </span>
                    @endif

                    @if (request('audit_partner'))
                        @php
                            $selectedPartner = $auditpartner->firstWhere('id', request('audit_partner'));
                        @endphp

                        @if ($selectedPartner)
                            <span class="badge text-bg-secondary">
                                Audit Partner: {{ $selectedPartner->name }}
                            </span>
                        @endif
                    @endif

                    @if (request('audit_cycle_id'))
                        @php
                            $selectedCycle = $auditCycle->firstWhere('id', request('audit_cycle_id'));
                        @endphp

                        @if ($selectedCycle)
                            <span class="badge text-bg-secondary">
                                Cycle: {{ $selectedCycle->name }}
                            </span>
                        @endif
                    @endif
                </div>
                <button class="filterToggleBtn btn btn-link text-nowrap" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#filterOffcanvas">
                    <i class="fas fa-filter"></i> Filters & Export
                </button>
            </div>

        </div>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="dashboard-tab-pane">
                <div class="p-lg-3">
                    {{-- <div class="cardBox mb-3">
                        <div class="row gy-3">
                            <div class="col-12 col-sm d-flex flex-column justify-content-center ">
                                <h3 class="fs-4 fw-semibold">Welcome back, <span class="text-primary">Alex
                                        Kim</span></h3>
                                <p class="text-muted mb-0 fs-7">You have 5 new messages and 2 new notifications.</p>
                            </div>
                            <div class="col-12 col-sm-auto d-flex justify-content-center">
                                <img src="images/welcome-img.svg" alt="" width="170">
                            </div>
                        </div>
                    </div> --}}
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
                                    'arrow' => '▲',
                                    'formatted' => '+' . number_format($change, 1) . '%',
                                ];
                            } elseif ($change < 0) {
                                return [
                                    'class' => 'badge-down',
                                    'arrow' => '▼',
                                    'formatted' => number_format($change, 1) . '%',
                                ];
                            } else {
                                return [
                                    'class' => 'badge-stable',
                                    'arrow' => '→',
                                    'formatted' => '0%',
                                ];
                            }
                        }

                        // Audit Allocation
                        $allocCurrent = $auditData['allocation_data']['currentCycleCount'] ?? 0;
                        $allocPrev = $auditData['allocation_data']['previousCycleCount'] ?? 0;
                        $allocChange = getChange($allocCurrent, $allocPrev);
                        $allocBadge = getBadge($allocChange);

                        // Audit Submitted
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

                        // Avg Audit Score
                        $scorePrev = round($auditData['previous_cycle_score'] ?? 0);
                        $scoreCurrent = round($auditData['current_cycle_score'] ?? 0);
                        $scoreChange = getChange($scoreCurrent, $scorePrev);
                        $scoreBadge = getBadge($scoreChange);

                        // Action Planning
                        $actionPrev = $auditData['getActionPlanningData']['previous_cycle']['approved'] ?? 0;
                        $actionCurrent = $auditData['getActionPlanningData']['current_cycle']['approved'] ?? 0;
                        $actionChange = getChange($actionCurrent, $actionPrev);
                        $actionBadge = getBadge($actionChange);
                    @endphp

                    <div class="row px-md-1">

                        <!-- Audit Allocation -->
                        <div class="col-md-6 col-lg-3 px-md-2 mb-3">
                            <div class="cardBox">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="kpi-title">AUDIT ALLOCATION</div>
                                        <div class="kpi-value">{{ number_format($allocCurrent) }}</div>
                                    </div>
                                    <div class="icon-box blue">
                                        <i class="fa-solid fa-users"></i>
                                    </div>
                                </div>

                                <div class="kpi-sub {{ $allocChange >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $allocBadge['arrow'] }}
                                    {{ $allocBadge['formatted'] }}
                                    <span class="text-muted">vs previous cycle</span>
                                </div>
                            </div>
                        </div>

                        <!-- Audit Submitted -->
                        <div class="col-md-6 col-lg-3 px-md-2 mb-3">
                            <div class="cardBox">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="kpi-title">AUDIT SUBMITTED</div>
                                        <div class="kpi-value">{{ number_format($submittedCurrent) }}</div>
                                    </div>
                                    <div class="icon-box red">
                                        <i class="fa-regular fa-file-lines"></i>
                                    </div>
                                </div>

                                <div class="kpi-sub {{ $submittedChange >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $submittedBadge['arrow'] }}
                                    {{ $submittedBadge['formatted'] }}
                                    <span class="text-muted">vs previous cycle</span>
                                </div>
                            </div>
                        </div>

                        <!-- Average Audit Score -->
                        <div class="col-md-6 col-lg-3 px-md-2 mb-3">
                            <div class="cardBox">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="kpi-title">AVG. AUDIT SCORE</div>
                                        <div class="kpi-value">{{ $scoreCurrent }}%</div>
                                    </div>
                                    <div class="icon-box green">
                                        <i class="fa-solid fa-circle-check"></i>
                                    </div>
                                </div>

                                <div class="kpi-sub {{ $scoreChange >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $scoreBadge['arrow'] }}
                                    {{ $scoreBadge['formatted'] }}
                                    <span class="text-muted">vs previous cycle</span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Planning -->
                        <div class="col-md-6 col-lg-3 px-md-2 mb-3">
                            <div class="cardBox">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="kpi-title">ACTION PLANNING</div>
                                        <div class="kpi-value">{{ number_format($actionCurrent) }}</div>
                                    </div>
                                    <div class="icon-box orange">
                                        <i class="fa-regular fa-clock"></i>
                                    </div>
                                </div>

                                <div class="kpi-sub {{ $actionChange >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $actionBadge['arrow'] }}
                                    {{ $actionBadge['formatted'] }}
                                    <span class="text-muted">vs previous cycle</span>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div>
                        <div id="audit_agency_wise_data">

                            <div class="table-responsive tableDiv af-card mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="af-card-title">Audit Partners Wise View </div>
                                </div>
                                <table class="table text-center">
                                    <thead>
                                        <tr>
                                            <th>Audit Partners</th>
                                            <th>Audit Allocated</th>
                                            <th>Audit Submitted</th>
                                            <th>Audit Score</th>
                                            <th>Audit Action Planning</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($auditData['getAuditAgencyWiseData']['allocation'] as $alloc)
                                            <tr>
                                                <td>{{ App\Helpers\Helper::getUser($alloc->agency_id) }}</td>
                                                <td>{{ $alloc->previousCycleCount }}
                                                    |
                                                    {{ $alloc->currentCycleCount }}
                                                    @if ($alloc->currentCycleCount > $alloc->previousCycleCount)
                                                        <img src="{{ URL::asset('/public/clientdashboard/images/downarrow-green.svg') }}"
                                                            style="vertical-align:middle; margin: 0 3px;" alt="Up"
                                                            width="8">
                                                    @else
                                                        <img src="{{ URL::asset('/public/clientdashboard/images/downarrow.svg') }}"
                                                            style="vertical-align:middle; margin: 0 3px;" alt="Down"
                                                            width="8">
                                                    @endif
                                                </td>
                                                @foreach ($auditData['getAuditAgencyWiseData']['auditCountAndScore'] as $agendata)
                                                    @if ($agendata->agency_id == $alloc->agency_id)
                                                        <td>{{ $agendata->previousCycleCount }}
                                                            |
                                                            {{ $agendata->currentCycleCount }}
                                                            @if ($agendata->currentCycleCount > $agendata->previousCycleCount)
                                                                <img src="{{ URL::asset('/public/clientdashboard/images/downarrow-green.svg') }}"
                                                                    style="vertical-align:middle; margin: 0 3px;"
                                                                    alt="Up" width="8">
                                                            @else
                                                                <img src="{{ URL::asset('/public/clientdashboard/images/downarrow.svg') }}"
                                                                    style="vertical-align:middle; margin: 0 3px;"
                                                                    alt="Down" width="8">
                                                            @endif
                                                        </td>
                                                        <td>{{ $agendata->previousCycleScore ?? 0 }}
                                                            |
                                                            {{ $agendata->currentCycleScore }}
                                                            @if ($agendata->currentCycleScore > $agendata->previousCycleScore)
                                                                <img src="{{ URL::asset('/public/clientdashboard/images/downarrow-green.svg') }}"
                                                                    style="vertical-align:middle; margin: 0 3px;"
                                                                    alt="Up" width="8">
                                                            @else
                                                                <img src="{{ URL::asset('/public/clientdashboard/images/downarrow.svg') }}"
                                                                    style="vertical-align:middle; margin: 0 3px;"
                                                                    alt="Down" width="8">
                                                            @endif
                                                        </td>
                                                        <td>{{ $agendata->actionPlanPreviousCycle }}
                                                            |
                                                            {{ $agendata->actionPlanCurrentCycle }}
                                                            @if ($agendata->actionPlanCurrentCycle > $agendata->actionPlanPreviousCycle)
                                                                <img src="{{ URL::asset('/public/clientdashboard/images/downarrow-green.svg') }}"
                                                                    style="vertical-align:middle; margin: 0 3px;"
                                                                    alt="Up" width="8">
                                                            @else
                                                                <img src="{{ URL::asset('/public/clientdashboard/images/downarrow.svg') }}"
                                                                    style="vertical-align:middle; margin: 0 3px;"
                                                                    alt="Down" width="8">
                                                            @endif
                                                        </td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    @php
                        use Carbon\Carbon;

                        $currentMonth = Carbon::create($year, $month, 1);
                        $startDay = $currentMonth->copy()->startOfMonth()->startOfWeek();
                        $endDay = $currentMonth->copy()->endOfMonth()->endOfWeek();
                    @endphp
                    <!-- Calendar -->
                    <div class="af-card mb-4">
                        <div class="af-cal-header">
                            <div>
                                <div class="af-card-title">Monthly Audit Schedule</div>
                                <div style="font-size:.78rem;color:var(--gray-500)">Visual timeline of upcoming and past
                                    audit
                                    allocations</div>
                            </div>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div class="cal-nav">
                                    {{-- <button class="cal-nav-btn"><i class="fa-solid fa-chevron-left"
                                        style="font-size:.7rem"></i></button> --}}
                                    <span class="cal-month">
                                        {{-- {{ optional($auditCycle->firstWhere('id', $currentCycleId))->name }} --}}
                                        Jun'26
                                    </span>
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
                                    {{-- <button type="button" class="btn-close"
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
                    <div class="cardBox mb-3">
                        <div class="mb-3">
                            <h2 class="card-title fw-semibold mb-0">Recent Audit Submissions</h2>
                            <small>Last 5 audits uploaded by partners</small>
                        </div>

                        <div class="table-responsive tableDiv">
                            <table class="table align-middle mb-0">
                                <tbody>

                                    @forelse ($latestAudits as $audit)
                                        <tr>
                                            <td class="text-black">
                                                {{ $audit->name ?? 'N/A' }} – {{ $audit->agency_code ?? 'N/A' }}
                                                <small class="d-block text-muted">
                                                    Submitted by: {{ $audit->present_auditor ?? 'N/A' }}
                                                </small>
                                            </td>

                                            <td class="text-nowrap">
                                                {{ \Carbon\Carbon::parse($audit->audit_date_by_aud ?? now())->diffForHumans() }}
                                            </td>

                                            <td class="text-center">
                                                <a href="{{ route('audit.downloadReports', ['audit_id' => $audit->id]) }}"
                                                    class="btn btn-primary py-1 mx-auto">
                                                    Download
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-3">
                                                No recent audits found
                                            </td>
                                        </tr>
                                    @endforelse

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="row px-md-1">
                            <div class="col-12 px-md-2">
                                <div class="cardBox h-100 p-3">
                                    <!-- Row 1: Title -->
                                    <div class="row mb-3">
                                        <div class="col-12 d-flex align-items-center">
                                            <h2 class="card-title fw-semibold mb-0">
                                                Cross Tab
                                                {{-- <i class="fa fa-info-circle text-primary ms-2" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Cross Tab.">
                                    </i> --}}
                                            </h2>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="row g-3 formBox">
                                            <div class="col-md-4">
                                                <label for="match_case" class="form-label">Metrics Type</label>
                                                <select id="match_case" class="form-select">
                                                    <option value="1">Audit Score & Count</option>
                                                    <option value="2">Repeat Issues</option>
                                                    <option value="3">Action Planning</option>
                                                </select>
                                            </div>

                                            <div class="col-md-4">
                                                <label for="match_field" class="form-label">Row</label>
                                                <select id="match_field" class="form-select">
                                                    <option value="1">Audit Cycle</option>
                                                    <option value="2">Audit Type</option>
                                                    <option value="3">Products</option>
                                                    <option value="4">Zone</option>
                                                    <option value="5">State</option>
                                                    <option value="6">City</option>
                                                    <option value="7">Parameters</option>
                                                    <option value="8">Sub Parameters</option>
                                                    <option value="9">List of Agencies/Branch/Yard/Repos</option>
                                                    <option value="10">Regulatory Parameter</option>
                                                </select>
                                            </div>

                                            <div class="col-md-4">
                                                <label for="match_field_other" class="form-label">Audit Type</label>
                                                <select id="match_field_other" class="form-select">
                                                    <option value="0">All</option>
                                                    <option value="agency">Agency</option>
                                                    <option value="branch">Branch</option>
                                                    <option value="yard">Yard</option>
                                                    <option value="agency_repo">Agency Repo</option>
                                                    <option value="branch_repo">Branch Repo</option>
                                                    <option value="yard_repo">Yard Repo</option>
                                                </select>
                                            </div>

                                            <div class="col-md-12" style="text-align:center;">
                                                <button type="button" onclick="getCrossTabData();"
                                                    class="btn btn-primary">Search</button>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Crosstab Result Section -->
                                    <div id="crosstabResult" class="table-responsive tableDiv">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="trend-tab-pane">
                <div class="p-lg-3">
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
                                    (($kpi['deviationCurrent'] - $kpi['deviationPrevious']) /
                                        $kpi['deviationPrevious']) *
                                        100,
                                    1,
                                )
                                : 0;

                        $topZone = $auditData['topZone'];
                    @endphp

                    {{-- <div class="row px-md-1">

                        <!-- Total Audits Conducted -->
                        <div class="col-md-6 col-lg-3 px-md-2 mb-3">
                            <div class="cardBox">
                                <div class="d-flex justify-content-between gap-2">
                                    <div>
                                        <div class="kpi-title">Total Audits Conducted</div>
                                        <div class="kpi-value">{{ $kpi['totalCurrent'] }}</div>
                                    </div>
                                    <div class="icon-box blue">
                                        <i class="far fa-file"></i>
                                    </div>
                                </div>

                                <div class="kpi-sub text-muted">
                                    {{ $kpi['totalPrevious'] }} <span class="text-muted">last cycle</span>
                                </div>
                            </div>
                        </div>

                        <!-- Overall Compliance -->
                        <div class="col-md-6 col-lg-3 px-md-2 mb-3">
                            <div class="cardBox">
                                <div class="d-flex justify-content-between gap-2">
                                    <div>
                                        <div class="kpi-title">Overall Compliance</div>
                                        <div class="kpi-value">{{ $kpi['complianceCurrent'] }}</div>
                                    </div>
                                    <div class="icon-box red">
                                        <i class="fas fa-exclamation"></i>
                                    </div>
                                </div>

                                <div class="kpi-sub {{ $complianceChange >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $complianceChange >= 0 ? '▲' : '▼' }}
                                    {{ abs($complianceChange) }}%
                                    <span class="text-muted">vs last cycle</span>
                                </div>
                            </div>
                        </div>

                        <!-- Critical Deviations -->
                        <div class="col-md-6 col-lg-3 px-md-2 mb-3">
                            <div class="cardBox">
                                <div class="d-flex justify-content-between gap-2">
                                    <div>
                                        <div class="kpi-title">Critical Deviations</div>
                                        <div class="kpi-value">{{ $kpi['deviationCurrent'] }}</div>
                                    </div>
                                    <div class="icon-box green">
                                        <i class="fas fa-shield-alt"></i>
                                    </div>
                                </div>

                                <div class="kpi-sub {{ $deviationChange <= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $deviationChange >= 0 ? '▲' : '▼' }}
                                    {{ abs($deviationChange) }}%
                                    <span class="text-muted">vs last cycle</span>
                                </div>
                            </div>
                        </div>

                        <!-- Top Zone Performance -->
                        <div class="col-md-6 col-lg-3 px-md-2 mb-3">
                            <div class="cardBox">
                                <div class="d-flex justify-content-between gap-2">
                                    <div>
                                        <div class="kpi-title">Top Zone Performance</div>
                                        <div class="kpi-value" style="font-size:1.1rem;">
                                            {{ $topZone['region_name'] ?? 'N/A' }}
                                        </div>
                                    </div>
                                    <div class="icon-box orange">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                </div>

                                <div class="kpi-sub text-muted">
                                    {{ $topZone['avg_score'] ?? 0 }}% Score •
                                    {{ $topZone['audit_count'] ?? 0 }} Audits
                                </div>
                            </div>
                        </div>

                    </div> --}}
                    @php
                        $products = [];
                        $productScores = [];
                        $productAudits = [];

                        if (!empty($auditData['productDistribution'])) {
                            foreach ($auditData['productDistribution'] as $row) {
                                $products[] = $row->product_name;
                                $productScores[] = round($row->avg_score, 1);
                                $productAudits[] = (int) $row->audit_count;
                            }
                        }

                        $zones = [];
                        $zoneAudits = [];
                        $zoneScores = [];

                        if (!empty($auditData['zonePerformance'])) {
                            foreach ($auditData['zonePerformance'] as $row) {
                                $zones[] = $row->zone_name;
                                $zoneAudits[] = (int) $row->audit_count;
                                $zoneScores[] = round($row->avg_score, 1);
                            }
                        }

                        $totalZoneAudits = array_sum($zoneAudits);
                        $avgCompliance = count($zoneScores) ? round(array_sum($zoneScores) / count($zoneScores)) : 0;
                    @endphp
                    <div class="row px-lg-1">
                        <div class="col-lg-8 mb-3 px-lg-2">
                            <div class="cardBox h-100">
                                <div class="mb-3">
                                    @if (auth()->user()->client_id == 298 || auth()->user()->client_id == 288)
                                        <h2 class="card-title fw-semibold mb-0">
                                            Category Distribution & Risk
                                        </h2>
                                        <small>Volume vs Average Risk Score by Category Line</small>
                                    @else
                                        <h2 class="card-title fw-semibold mb-0">
                                            Product Distribution & Risk
                                        </h2>
                                        <small>Volume vs Average Risk Score by Product Line</small>
                                    @endif
                                </div>

                                <div id="productChart" style="height:300px;"></div>
                            </div>
                        </div>

                        <div class="col-lg-4 mb-3 px-lg-2">
                            <div class="cardBox h-100">
                                <div class="mb-3">
                                    <h2 class="card-title fw-semibold mb-0">
                                        Zone Performance
                                    </h2>
                                    <small>Compliance by Region</small>
                                </div>

                                <div id="zoneChart" style="height:240px;"></div>

                                <div class="mt-3">
                                    @php
                                        $colors = ['#2f63d8', '#7c3aed', '#10b981', '#f59e0b', '#ef4444'];
                                    @endphp

                                    @foreach ($zones as $index => $zone)
                                        @php
                                            $percentage =
                                                $totalZoneAudits > 0
                                                    ? round(($zoneAudits[$index] / $totalZoneAudits) * 100)
                                                    : 0;
                                        @endphp

                                        <div class="audit-legend-item">
                                            <div>
                                                <span class="dot"
                                                    style="background:{{ $colors[$index % count($colors)] }}"></span>
                                                {{ $zone }}
                                            </div>
                                            <div>{{ $percentage }}%</div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    @if(!(auth()->user()->client_id == 288 || auth()->user()->client_id == 288))
                    <div class="mb-3" id="param_geographical_view">
                        <div class="mb-3">
                            <div class="row px-md-1" id="param_compliance_data">
                            </div>
                        </div>
                    </div>
                    @endif
                    {{-- <div class="row px-lg-1">
                        <div class="col-lg-8 mb-3 px-lg-2">
                            <div class="cardBox ">
                                <div class="mb-3">
                                    <h2 class="card-title fw-semibold mb-0">Regional Heatmap </h2>
                                    <small>Live Data</small>
                                </div>
                                <div id="indiaMap"></div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-3 px-lg-2">
                            <div class="cardBox h-100">
                                <h2 class="card-title fw-semibold mb-3">City Compliance </h2>
                                <div class="auditProgress">
                                    <div class="audit-label d-flex justify-content-between gap-2">
                                        <span>Mumbai</span><span>98%</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-primary" style="width:98%"></div>
                                    </div>
                                </div>
                                <div class="auditProgress">
                                    <div class="audit-label d-flex justify-content-between gap-2">
                                        <span>Delhi</span><span>85%</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar" style="width:85%; background:#6d28d9"></div>
                                    </div>
                                </div>
                                <div class="auditProgress">
                                    <div class="audit-label d-flex justify-content-between gap-2">
                                        <span>Bangalore</span><span>92%</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-primary" style="width:92%"></div>
                                    </div>
                                </div>
                                <div class="auditProgress">
                                    <div class="audit-label d-flex justify-content-between gap-2">
                                        <span>Chennai</span><span>76%</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-danger" style="width:76%"></div>
                                    </div>
                                </div>
                                <div class="auditProgress">
                                    <div class="audit-label d-flex justify-content-between gap-2">
                                        <span>Hyderabad</span><span>89%</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar" style="width:89%; background:#4f46e5"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                    <div class="row px-lg-1">
                        <div class="col-lg-12 col-xl-6 mb-3 px-lg-2">
                            <div class="cardBox h-100">

                                @php
                                    $perf = $auditData['topPerformers'] ?? [];
                                @endphp

                                <!-- Header -->
                                <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div>
                                        <h2 class="card-title fw-semibold mb-0">
                                            🏆 Top Performing Locations
                                        </h2>
                                        <small class="text-muted">
                                            Highest scoring cities across all audits
                                        </small>
                                    </div>

                                    <span class="badge bg-light text-dark px-3 py-2">
                                        {{ count($perf) }} Locations
                                    </span>
                                </div>

                                @forelse($perf as $index => $row)
                                    @php
                                        $score = round($row->avg_score, 1);

                                        if ($score >= 97) {
                                            $status = 'Excellent';
                                            $badgeClass = 'audit-green';
                                            $rankBg = '#f59e0b';
                                            $gradient = 'linear-gradient(135deg,#f59e0b,#f97316)';
                                        } elseif ($score >= 90) {
                                            $status = 'Good';
                                            $badgeClass = 'audit-blue';
                                            $rankBg = '#3b82f6';
                                            $gradient = 'linear-gradient(135deg,#3b82f6,#6366f1)';
                                        } else {
                                            $status = 'Average';
                                            $badgeClass = 'audit-orange';
                                            $rankBg = '#94a3b8';
                                            $gradient = 'linear-gradient(135deg,#64748b,#94a3b8)';
                                        }

                                        $initials = strtoupper(substr($row->city ?? 'N', 0, 1));
                                    @endphp

                                    <div class="audit-user"
                                        style="
                           padding:14px 0;
                           border-bottom:{{ !$loop->last ? '1px solid #eef2f7' : '0' }};
                       ">

                                        <div class="audit-user-left">

                                            <!-- Rank Badge -->
                                            <div class="me-3">
                                                <div
                                                    style="
                            width:32px;
                            height:32px;
                            border-radius:50%;
                            background:{{ $rankBg }};
                            color:#fff;
                            font-size:12px;
                            font-weight:700;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                            box-shadow:0 4px 10px rgba(0,0,0,.12);
                        ">
                                                    #{{ $index + 1 }}
                                                </div>
                                            </div>

                                            <!-- Avatar -->
                                            {{-- <div class="audit-avatar d-flex align-items-center justify-content-center"
                        style="
                            background:{{ $gradient }};
                            color:#fff;
                            font-weight:700;
                            font-size:15px;
                            box-shadow:0 4px 12px rgba(0,0,0,.12);
                        ">
                        {{ $initials }}
                        </div> --}}

                                            <!-- City & State -->
                                            <div>
                                                <div class="audit-name fw-semibold">
                                                    {{ $row->city }}
                                                </div>

                                                <div class="audit-role">
                                                    <i class="fa-solid fa-location-dot text-danger me-1"></i>
                                                    {{ $row->state }}
                                                </div>
                                            </div>

                                        </div>

                                        <!-- Score -->
                                        <div class="audit-score text-end">

                                            <div class="audit-percent"
                                                style="
                            font-size:1.35rem;
                            font-weight:700;
                            color:#111827;
                        ">
                                                {{ $score }}%
                                            </div>

                                            <div class="audit-label mt-1">
                                                Avg. Score

                                                <span class="audit-badge {{ $badgeClass }}" style="margin-left:6px;">
                                                    {{ $status }}
                                                </span>
                                            </div>

                                        </div>

                                    </div>

                                @empty

                                    <div class="text-center py-5">
                                        <i class="fa-solid fa-chart-line fs-1 text-muted mb-3"></i>
                                        <div class="text-muted">
                                            No performance data available
                                        </div>
                                    </div>
                                @endforelse

                            </div>
                        </div>

                        <div class="col-lg-12 col-xl-6 mb-3 px-lg-2">
                            <div class="cardBox h-100">

                                @php
                                    $perf = $auditData['bottomPerformers'] ?? [];
                                @endphp

                                <!-- Header -->
                                <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div>
                                        <h2 class="card-title fw-semibold mb-0">
                                            ⚠️ Bottom Performing Locations
                                        </h2>
                                        <small class="text-muted">
                                            Locations requiring immediate attention
                                        </small>
                                    </div>

                                    <span class="badge bg-light text-dark px-3 py-2">
                                        {{ count($perf) }} Locations
                                    </span>
                                </div>

                                @forelse($perf as $index => $row)
                                    @php
                                        $score = round($row->avg_score, 1);

                                        if ($score < 70) {
                                            $status = 'Critical';
                                            $badgeClass = 'audit-red';
                                            $rankBg = '#dc2626';
                                            $gradient = 'linear-gradient(135deg,#dc2626,#ef4444)';
                                        } elseif ($score < 85) {
                                            $status = 'Needs Improvement';
                                            $badgeClass = 'audit-orange';
                                            $rankBg = '#f59e0b';
                                            $gradient = 'linear-gradient(135deg,#f59e0b,#f97316)';
                                        } else {
                                            $status = 'Average';
                                            $badgeClass = 'audit-blue';
                                            $rankBg = '#64748b';
                                            $gradient = 'linear-gradient(135deg,#64748b,#94a3b8)';
                                        }

                                        $initials = strtoupper(substr($row->city ?? 'N', 0, 1));
                                    @endphp

                                    <div class="audit-user"
                                        style="
                            padding:14px 0;
                            border-bottom:{{ !$loop->last ? '1px solid #eef2f7' : '0' }};
                        ">

                                        <div class="audit-user-left">

                                            <!-- Rank -->
                                            <div class="me-3">
                                                <div
                                                    style="
                                                    width:32px;
                                                    height:32px;
                                                    border-radius:50%;
                                                    background:{{ $rankBg }};
                                                    color:#fff;
                                                    font-size:12px;
                                                    font-weight:700;
                                                    display:flex;
                                                    align-items:center;
                                                    justify-content:center;
                                                    box-shadow:0 4px 10px rgba(0,0,0,.12);
                                                ">
                                                    #{{ $index + 1 }}
                                                </div>
                                            </div>

                                            <!-- Avatar -->
                                            {{-- <div class="audit-avatar d-flex align-items-center justify-content-center"
                                                style="
                                                    background:{{ $gradient }};
                                                    color:#fff;
                                                    font-weight:700;
                                                    font-size:15px;
                                                    box-shadow:0 4px 12px rgba(0,0,0,.12);
                                                ">
                                                {{ $initials }}
                                            </div> --}}

                                            <!-- Location -->
                                            <div>
                                                <div class="audit-name fw-semibold">
                                                    {{ $row->city }}
                                                </div>

                                                <div class="audit-role">
                                                    <i class="fa-solid fa-location-dot text-danger me-1"></i>
                                                    {{ $row->state }}
                                                </div>
                                            </div>

                                        </div>

                                        <!-- Score -->
                                        <div class="audit-score text-end">

                                            <div class="audit-percent"
                                                style="
                                                font-size:1.35rem;
                                                font-weight:700;
                                                color:{{ $score < 70 ? '#dc2626' : '#f59e0b' }};
                                            ">
                                                {{ $score }}%
                                            </div>

                                            <div class="audit-label mt-1">
                                                Avg. Score

                                                <span class="audit-badge {{ $badgeClass }}" style="margin-left:6px;">
                                                    {{ $status }}
                                                </span>
                                            </div>

                                        </div>

                                    </div>

                                @empty

                                    <div class="text-center py-5">
                                        <i class="fa-solid fa-triangle-exclamation fs-1 text-muted mb-3"></i>
                                        <div class="text-muted">
                                            No performance data available
                                        </div>
                                    </div>
                                @endforelse

                            </div>
                        </div>
                        {{-- <div class="col-lg-6 col-xl-3 mb-3 px-lg-2">
                            <div class="audit-dark-card">
                                <div class="audit-icon">📊</div>
                                <h6>Review Detailed Metrics</h6>
                                <p style="font-size:12px;color:#cbd5f5;">
                                    Dive deep into branch-level compliance and historical trends.
                                </p>
                                <button class="audit-btn">
                                    Open Pointer Dump →
                                </button>
                            </div>
                        </div>
                        <div class="col-lg-6 col-xl-3 mb-3 px-lg-2">
                            <div class="audit-purple-card">
                                <div class="audit-icon">⚡</div>
                                <h6>Start Action Planning</h6>
                                <p style="font-size:12px;color:#e0e7ff;">
                                    Create remediation tasks for high-risk zones identified today.
                                </p>
                                <button class="audit-btn-light">
                                    Create Plan →
                                </button>
                            </div>
                        </div> --}}
                    </div>
                    <div class="row px-lg-1">
                        <div class="col-lg-12 col-xl-6 mb-3 px-lg-2">
                            <div class="cardBox h-100">

                                @php
                                    $agencies = $auditData['topAgencies'] ?? [];
                                @endphp

                                <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div>
                                        <h2 class="card-title fw-semibold mb-0">
                                            🏆 Top Performing Agencies
                                        </h2>
                                        <small class="text-muted">
                                            Highest scoring agencies across all audits
                                        </small>
                                    </div>

                                    <span class="badge bg-light text-dark px-3 py-2">
                                        {{ count($agencies) }} Agencies
                                    </span>
                                </div>

                                @forelse($agencies as $index => $row)
                                    @php
                                        $score = round($row->avg_score, 1);

                                        if ($score >= 97) {
                                            $status = 'Excellent';
                                            $badgeClass = 'audit-green';
                                            $rankBg = '#f59e0b';
                                            $gradient = 'linear-gradient(135deg,#f59e0b,#f97316)';
                                        } elseif ($score >= 90) {
                                            $status = 'Great';
                                            $badgeClass = 'audit-blue';
                                            $rankBg = '#3b82f6';
                                            $gradient = 'linear-gradient(135deg,#3b82f6,#6366f1)';
                                        } else {
                                            $status = 'Good';
                                            $badgeClass = 'audit-orange';
                                            $rankBg = '#64748b';
                                            $gradient = 'linear-gradient(135deg,#64748b,#94a3b8)';
                                        }

                                        $initials = collect(explode(' ', $row->agency_name))
                                            ->take(2)
                                            ->map(fn($word) => strtoupper(substr($word, 0, 1)))
                                            ->implode('');
                                    @endphp

                                    <div class="audit-user"
                                        style="
                    padding:14px 0;
                    border-bottom:{{ !$loop->last ? '1px solid #eef2f7' : '0' }};
                ">

                                        <div class="audit-user-left">

                                            <!-- Rank -->
                                            <div class="me-3">
                                                <div
                                                    style="
                            width:32px;
                            height:32px;
                            border-radius:50%;
                            background:{{ $rankBg }};
                            color:#fff;
                            font-size:12px;
                            font-weight:700;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                            box-shadow:0 4px 10px rgba(0,0,0,.12);
                        ">
                                                    #{{ $index + 1 }}
                                                </div>
                                            </div>

                                            <!-- Agency Avatar -->
                                            {{-- <div class="audit-avatar d-flex align-items-center justify-content-center"
                        style="
                            background:{{ $gradient }};
                            color:#fff;
                            font-weight:700;
                            font-size:14px;
                            box-shadow:0 4px 12px rgba(0,0,0,.12);
                        ">
                        {{ $initials }}
                    </div> --}}

                                            <!-- Agency Name -->
                                            <div>
                                                <div class="audit-name fw-semibold">
                                                    {{ $row->agency_name }}
                                                </div>

                                                <div class="audit-role">
                                                    <i class="fa-solid fa-building me-1 text-primary"></i>
                                                    Agency Performance Ranking
                                                </div>
                                            </div>

                                        </div>

                                        <!-- Score -->
                                        <div class="audit-score text-end">

                                            <div class="audit-percent"
                                                style="
                            font-size:1.4rem;
                            font-weight:700;
                            color:#111827;
                        ">
                                                {{ $score }}%
                                            </div>

                                            <div class="audit-label mt-1">
                                                Compliance Score

                                                <span class="audit-badge {{ $badgeClass }}" style="margin-left:6px;">
                                                    {{ $status }}
                                                </span>
                                            </div>

                                        </div>

                                    </div>

                                @empty

                                    <div class="text-center py-5">
                                        <i class="fa-solid fa-building fs-1 text-muted mb-3"></i>
                                        <div class="text-muted">
                                            No agency performance data available
                                        </div>
                                    </div>
                                @endforelse

                            </div>
                        </div>

                        <div class="col-lg-12 col-xl-6 mb-3 px-lg-2">
                            <div class="cardBox h-100">

                                @php
                                    $agencies = $auditData['bottomAgencies'] ?? [];
                                @endphp

                                <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div>
                                        <h2 class="card-title fw-semibold mb-0">
                                            ⚠️ Bottom Performing Agencies
                                        </h2>
                                        <small class="text-muted">
                                            Agencies requiring immediate attention
                                        </small>
                                    </div>

                                    <span class="badge bg-light text-dark px-3 py-2">
                                        {{ count($agencies) }} Agencies
                                    </span>
                                </div>

                                @forelse($agencies as $index => $row)
                                    @php
                                        $score = round($row->avg_score, 1);

                                        if ($score < 70) {
                                            $status = 'Critical';
                                            $badgeClass = 'audit-red';
                                            $rankBg = '#dc2626';
                                            $gradient = 'linear-gradient(135deg,#dc2626,#ef4444)';
                                            $scoreColor = '#dc2626';
                                        } elseif ($score < 85) {
                                            $status = 'Needs Improvement';
                                            $badgeClass = 'audit-orange';
                                            $rankBg = '#f59e0b';
                                            $gradient = 'linear-gradient(135deg,#f59e0b,#f97316)';
                                            $scoreColor = '#f59e0b';
                                        } else {
                                            $status = 'Average';
                                            $badgeClass = 'audit-blue';
                                            $rankBg = '#64748b';
                                            $gradient = 'linear-gradient(135deg,#64748b,#94a3b8)';
                                            $scoreColor = '#64748b';
                                        }

                                        $initials = collect(explode(' ', $row->agency_name))
                                            ->take(2)
                                            ->map(fn($word) => strtoupper(substr($word, 0, 1)))
                                            ->implode('');
                                    @endphp

                                    <div class="audit-user"
                                        style="
                    padding:14px 0;
                    border-bottom:{{ !$loop->last ? '1px solid #eef2f7' : '0' }};
                ">

                                        <div class="audit-user-left">

                                            <!-- Rank -->
                                            <div class="me-3">
                                                <div
                                                    style="
                            width:32px;
                            height:32px;
                            border-radius:50%;
                            background:{{ $rankBg }};
                            color:#fff;
                            font-size:12px;
                            font-weight:700;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                            box-shadow:0 4px 10px rgba(0,0,0,.12);
                        ">
                                                    #{{ $index + 1 }}
                                                </div>
                                            </div>

                                            <!-- Avatar -->
                                            {{-- <div class="audit-avatar d-flex align-items-center justify-content-center"
                        style="
                            background:{{ $gradient }};
                            color:#fff;
                            font-weight:700;
                            font-size:14px;
                            box-shadow:0 4px 12px rgba(0,0,0,.12);
                        ">
                        {{ $initials }}
                    </div> --}}

                                            <!-- Agency -->
                                            <div>
                                                <div class="audit-name fw-semibold">
                                                    {{ $row->agency_name }}
                                                </div>

                                                <div class="audit-role">
                                                    <i class="fa-solid fa-triangle-exclamation text-danger me-1"></i>
                                                    Performance Needs Review
                                                </div>
                                            </div>

                                        </div>

                                        <!-- Score -->
                                        <div class="audit-score text-end">

                                            <div class="audit-percent"
                                                style="
                            font-size:1.4rem;
                            font-weight:700;
                            color:{{ $scoreColor }};
                        ">
                                                {{ $score }}%
                                            </div>

                                            <div class="audit-label mt-1">
                                                Compliance Score

                                                <span class="audit-badge {{ $badgeClass }}" style="margin-left:6px;">
                                                    {{ $status }}
                                                </span>
                                            </div>

                                        </div>

                                    </div>

                                @empty

                                    <div class="text-center py-5">
                                        <i class="fa-solid fa-triangle-exclamation fs-1 text-muted mb-3"></i>
                                        <div class="text-muted">
                                            No agency performance data available
                                        </div>
                                    </div>
                                @endforelse

                            </div>
                        </div>
                        {{-- </div> --}}
                        {{-- <div class="col-lg-6 col-xl-3 mb-3 px-lg-2">
                            <div class="audit-dark-card">
                                <div class="audit-icon">📊</div>
                                <h6>Review Detailed Metrics</h6>
                                <p style="font-size:12px;color:#cbd5f5;">
                                    Dive deep into branch-level compliance and historical trends.
                                </p>
                                <button class="audit-btn">
                                    Open Pointer Dump →
                                </button>
                            </div>
                        </div>
                        <div class="col-lg-6 col-xl-3 mb-3 px-lg-2">
                            <div class="audit-purple-card">
                                <div class="audit-icon">⚡</div>
                                <h6>Start Action Planning</h6>
                                <p style="font-size:12px;color:#e0e7ff;">
                                    Create remediation tasks for high-risk zones identified today.
                                </p>
                                <button class="audit-btn-light">
                                    Create Plan →
                                </button>
                            </div>
                        </div> --}}
                    </div>
                </div>
            </div>
            <div class="tab-pane fade position-relative" id="planning-tab-pane">
                @if (in_array(23, $allocatedmodule))
                    <div class="blur-overlay">
                        <div class="overlay-message">
                            <i class="fas fa-lock fa-2x mb-2"></i>
                            <h5>Action Planning</h5>
                            <p>This feature is not available for your client.</p>
                        </div>
                    </div>
                @endif
                <div class="p-lg-3 {{ in_array(23, $allocatedmodule) ? 'blur-content' : '' }} ">
                    @php
                        $kpis = $auditData['actionKpis'] ?? [
                            'total' => 0,
                            'open' => 0,
                            'approved' => 0,
                            'rejected' => 0,
                        ];
                    @endphp

                    <div class="row px-md-1">

                        <!-- Total Issues -->
                        <div class="col-md-6 col-lg-3 px-md-2 mb-3">
                            <div class="cardBox">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="kpi-title">Total Issues</div>
                                        <div class="kpi-value">{{ number_format($kpis['total']) }}</div>
                                    </div>
                                    <div class="icon-box blue">
                                        <i class="fas fa-list-check"></i>
                                    </div>
                                </div>

                                <div class="kpi-sub text-muted">
                                    Based on unsatisfactory findings
                                </div>
                            </div>
                        </div>

                        <!-- Open Backlog -->
                        <div class="col-md-6 col-lg-3 px-md-2 mb-3">
                            <div class="cardBox">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="kpi-title">Open Backlog</div>
                                        <div class="kpi-value">{{ number_format($kpis['open']) }}</div>
                                    </div>
                                    <div class="icon-box orange">
                                        <i class="fas fa-hourglass-half"></i>
                                    </div>
                                </div>

                                <div class="kpi-sub text-warning">
                                    Pending approvals
                                </div>
                            </div>
                        </div>

                        <!-- Approved / Resolved -->
                        <div class="col-md-6 col-lg-3 px-md-2 mb-3">
                            <div class="cardBox">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="kpi-title">Approved / Resolved</div>
                                        <div class="kpi-value">{{ number_format($kpis['approved']) }}</div>
                                    </div>
                                    <div class="icon-box green">
                                        <i class="fas fa-check"></i>
                                    </div>
                                </div>

                                <div class="kpi-sub text-success">
                                    Successfully resolved
                                </div>
                            </div>
                        </div>

                        <!-- Rejected Actions -->
                        <div class="col-md-6 col-lg-3 px-md-2 mb-3">
                            <div class="cardBox">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="kpi-title">Rejected Actions</div>
                                        <div class="kpi-value">{{ number_format($kpis['rejected']) }}</div>
                                    </div>
                                    <div class="icon-box red">
                                        <i class="fas fa-xmark"></i>
                                    </div>
                                </div>

                                <div class="kpi-sub text-danger">
                                    Need revision
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="cardBox mb-3">

                        <div class="d-md-flex justify-content-between align-items-center mb-3 gap-2">

                            <div class="auditTabs">
                                <button type="button" class="auditTabBtn active" data-status="open">
                                    Open Issues ({{ number_format($kpis['open'] ?? 0) }})
                                </button>

                                <button type="button" class="auditTabBtn" data-status="approved">
                                    Approved ({{ number_format($kpis['approved'] ?? 0) }})
                                </button>

                                <button type="button" class="auditTabBtn" data-status="rejected">
                                    Rejected ({{ number_format($kpis['rejected'] ?? 0) }})
                                </button>
                            </div>

                            <div class="auditActionsDiv">
                                <div class="auditSearch">
                                    <i class="fa fa-search text-muted"></i>
                                    <input type="text" id="issueSearch" class="w-100"
                                        placeholder="Search agency ID or agency name">
                                </div>

                                <button class="auditExportBtn text-nowrap" type="button" data-bs-toggle="modal"
                                    data-bs-target="#openPointersModal">
                                    <i class="fa fa-download me-1"></i> Export
                                </button>
                            </div>

                        </div>

                        <div class="table-responsive tableDiv">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th class="text-nowrap">S.NO</th>
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
                                        <tr data-status="{{ strtolower($issue->status) }}">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $issue->audit_id }}</td>
                                            <td>{{ $issue->agency_id }}</td>
                                            <td>{{ $issue->agency_name }}</td>
                                            <td>{{ $issue->issue_description }}</td>
                                            <td>{{ $issue->due_date ?? '-' }}</td>

                                            <td>
                                                @if (strtolower($issue->status) == 'open')
                                                    <span class="badge-pill progress-status">Open</span>
                                                @elseif(strtolower($issue->status) == 'approved')
                                                    <span class="badge-pill completed-status">Approved</span>
                                                @elseif(strtolower($issue->status) == 'rejected')
                                                    <span class="badge-pill rejected-status">Rejected</span>
                                                @else
                                                    <span class="badge-pill">{{ ucfirst($issue->status) }}</span>
                                                @endif
                                            </td>

                                            <td>
                                                <a href="/audit-closure/download/{{ $issue->audit_id }}"
                                                    class="audit-download">
                                                    <i class="fa fa-download me-1"></i> Download
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                No issues found
                                            </td>
                                        </tr>
                                    @endforelse

                                </tbody>
                            </table>
                        </div>

                    </div>
                    <div class="cardBox mb-3">
                        <div class="mb-3">
                            <h2 class="card-title fw-semibold mb-0">
                                5-Month Resolution Trend
                            </h2>
                            <small>
                                Visualizing the progression of action items across statuses
                            </small>
                        </div>

                        <div id="chartResolution" style="height:300px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="offcanvas offcanvas-end" tabindex="-1" id="filterOffcanvas" aria-labelledby="offcanvasExampleLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasExampleLabel">Filter & Export</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-outline-primary btn-sm dashboardExportBtn" data-bs-toggle="modal"
                    data-bs-target="#myModal">
                    Raw Dump
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm dashboardExportBtn" data-bs-toggle="modal"
                    data-bs-target="#ScheduleAuditExport">
                    Schedule Audits
                </button>
                @if (!(in_array(23, $allocatedmodule)))
                    <button type="button" class="btn btn-outline-success btn-sm dashboardExportBtn"
                        data-bs-toggle="modal" data-bs-target="#openPointersModal">
                        Open Pointers Dump
                    </button>
                @endif
            </div>
            <hr>

            <form class="formBox" method="GET" action="{{ route('newdashboard') }}">
                <div class="mb-3">
                    <label for="audit_cycle" style="font-size: 13px !important">Audit Cycle</label>
                    <select class="form-select" name="audit_cycle_id" id="cycle_id"
                        onchange="getNewComplianceData(this.value)">
                        @foreach ($auditCycle as $ac)
                            <option value="{{ $ac->id }}" {{ $currentCycleId == $ac->id ? 'selected' : '' }}>
                                {{ $ac->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3 d-none">
                    <label for="audit_cycle" style="font-size: 13px !important">Audit Cycle</label>
                    <select class="form-select" name="audit_type">
                        <option value="all" selected>All</option>
                        <option value="agency">Agency</option>
                        <option value="agency_repo">Agency Repo</option>
                        <option value="branch">Branch</option>
                        <option value="branch_repo">Branch Repo</option>
                        <option value="yard">Yard</option>
                        <option value="yard_repo">Yard Repo</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="audit_cycle" style="font-size: 13px !important">Partner</label>
                    <select class="form-select" name="audit_partner">
                        <option value="">All</option>
                        @foreach ($auditpartner as $partner)
                            <option value="{{ $partner->id }}"
                                {{ request('audit_partner') == $partner->id ? 'selected' : '' }}>
                                {{ $partner->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="d-flex justify-content-center gap-2 mt-3">
                    <a href="{{ route('newdashboard') }}" class="btn btn-secondary">
                        Clear
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="auditModal">
        <div class="modal-dialog modal-md">
            <div class="modal-content border-0 rounded-1">

                <!-- Header -->
                <div class="modal-header">
                    <h5 class="modal-title">Audit Data Download</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- Body -->
                <div class="modal-body formBox">
                    <div class="row align-items-end px-md-1">

                        <!-- Start Date -->
                        <div class="col-md-6 mb-4 px-md-2">
                            <label class="form-label">Start Date*</label>
                            <div class="position-relative">
                                <input type="date" class="form-control mb-0">
                            </div>
                        </div>

                        <!-- End Date -->
                        <div class="col-md-6 mb-4 px-md-2">
                            <label class="form-label">End Date*</label>
                            <input type="date" class="form-control mb-0">
                        </div>

                        <!-- Button -->
                        <div class="col-md-12 mb-3 px-md-2">
                            <button class="btn w-50 btn-primary mx-auto">
                                Download
                            </button>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="modal fade" id="myModal">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Audit Data Download</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="GET"
                    action="{{ auth()->user()->client_id == 298 || auth()->user()->client_id == 288 ? route('v2report') : route('auditdumpdownload') }}"
                    autocomplete="off">
                    <div class="modal-body formBox">
                        <div class="row">
                            @csrf
                            <div class="col-md-6 form-group">
                                <label class="form-label">Start Date*</label>
                                <input name="start_date" type="date" data-date-format="yyyy-mm-dd"
                                    class="form-control" placeholder="Select Start Date" required />
                            </div>

                            <div class="col-md-6 form-group">
                                <label class="form-label">End Date*</label>
                                <input name="end_date" type="date" data-date-format="yyyy-mm-dd" class="form-control"
                                    placeholder="Select End Date" required />
                            </div>

                            <div class="col-md-12 form-group">
                                <button type="submit" class="w-50 btn btn-primary mx-auto form-control">Download</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="openPointersModal">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header">
                    <h5 class="modal-title">Open Pointers (unsatisfactory sub-parameters)
                        sheet</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="GET" action="{{ route('openPointersDump') }}" autocomplete="off">
                    <div class="modal-body formBox">
                        <div class="row">
                            @csrf
                            <div class="col-md-6 form-group">
                                <label class="form-label">Start Date*</label>
                                <input name="start_date" type="date" data-date-format="yyyy-mm-dd"
                                    class="form-control" placeholder="Select Start Date" required />
                            </div>

                            <div class="col-md-6 form-group">
                                <label class="form-label">End Date*</label>
                                <input name="end_date" type="date" data-date-format="yyyy-mm-dd" class="form-control"
                                    placeholder="Select End Date" required />
                            </div>

                            <div class="col-md-12 form-group">
                                {{-- <label>Action*</label> --}}
                                <button type="submit" class="w-50 btn btn-primary mx-auto ">Download</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="ScheduleAuditExport">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Schedule Audits Data Download</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="GET" action="{{ route('scheduleAuditDownload') }}" autocomplete="off">
                    <div class="modal-body formBox">
                        <div class="row">
                            @csrf
                            <div class="col-md-6 form-group">
                                <label class="form-label">Start Date*</label>
                                <input name="start_date" type="date" data-date-format="yyyy-mm-dd"
                                    class="form-control" placeholder="Select Start Date" required />
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="form-label">End Date*</label>
                                <input name="end_date" type="date" data-date-format="yyyy-mm-dd" class="form-control"
                                    placeholder="Select End Date" required />
                            </div>
                            <div class="col-md-12 form-group">
                                <button type="submit" class="w-50 btn btn-primary mx-auto">Download</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <form action="{{ route('switch.user') }}" method="POST" id="switchUserForm">
        @csrf
        <input type="hidden" name="email" id="switchEmail">
    </form>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js"></script>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/highcharts/11.4.0/highcharts.js"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highcharts/11.4.0/highmaps.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highcharts/11.4.0/modules/map.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
    <script src="https://code.highcharts.com/mapdata/countries/in/in-all.js"></script>


    {{-- <script src="js/in-all.js"></script> --}}
    <script src="{{ URL::asset('/public/assets/js/in-all.js') }}"></script>
    <script>
        /* LEFT CHART */
        Highcharts.chart('productChart', {
            chart: {
                backgroundColor: 'transparent'
            },

            title: {
                text: null
            },

            credits: {
                enabled: false
            },

            xAxis: {
                categories: @json($products)
            },

            yAxis: [{
                title: {
                    text: null
                }
            }, {
                title: {
                    text: null
                },
                opposite: true,
                max: 100
            }],

            legend: {
                align: 'right',
                verticalAlign: 'top'
            },

            series: [{
                name: 'Audit Volume',
                type: 'column',
                data: @json($productAudits),
                color: '#8aa6df'
            }, {
                name: 'Risk Score',
                type: 'spline',
                yAxis: 1,
                data: @json($productScores),
                color: '#7c3aed',
                marker: {
                    enabled: true
                }
            }]
        });


        /* RIGHT DONUT */
        Highcharts.chart('zoneChart', {
            chart: {
                type: 'pie',
                backgroundColor: 'transparent'
            },

            title: {
                text: '{{ $avgCompliance }}%<br><span style="font-size:12px;color:#888">Avg Compliance</span>',
                align: 'center',
                verticalAlign: 'middle',
                y: 10
            },

            credits: {
                enabled: false
            },

            plotOptions: {
                pie: {
                    innerSize: '70%',
                    dataLabels: {
                        enabled: false
                    },
                    borderWidth: 0
                }
            },

            series: [{
                name: 'Audit Count',
                data: [
                    @foreach ($zones as $index => $zone)
                        {
                            name: '{{ $zone }}',
                            y: {{ $zoneAudits[$index] }},
                            color: '{{ $colors[$index % count($colors)] }}'
                        },
                    @endforeach
                ]
            }]
        });
        /* WORLD MAP */
        // Highcharts.chart('chartResolution', {
        //     chart: {
        //         type: 'spline',
        //         backgroundColor: 'transparent'
        //     },

        //     title: {
        //         text: null
        //     },
        //     credits: {
        //         enabled: false
        //     },

        //     xAxis: {
        //         categories: ['Aug\'25', 'Sep\'25', 'Oct\'25', 'Dec\'25'],
        //         lineColor: '#e5e7eb',
        //         tickColor: '#e5e7eb'
        //     },

        //     yAxis: {
        //         title: {
        //             text: null
        //         },
        //         gridLineColor: '#e5e7eb'
        //     },

        //     legend: {
        //         align: 'center',
        //     },

        //     plotOptions: {
        //         spline: {
        //             marker: {
        //                 enabled: true,
        //                 radius: 4
        //             },
        //             lineWidth: 3
        //         }
        //     },

        //     series: [{
        //             name: 'Open Issues',
        //             data: [52, 3, 23, 0],
        //             color: '#3b82f6'
        //         },
        //         {
        //             name: 'Approved Solutions',
        //             data: [0, 0, 16, 0],
        //             color: '#16a34a'
        //         },
        //         {
        //             name: 'Rejected Actions',
        //             data: [0, 0, 14, 0],
        //             color: '#dc2626'
        //         }
        //     ]
        // });

        document.getElementById('mobileTabSelect').addEventListener('change', function() {
            let target = this.value;

            let triggerEl = document.querySelector(`[data-bs-target="${target}"]`);
            let tab = new bootstrap.Tab(triggerEl);
            tab.show();
        });

        document.getElementById("menuBtn").addEventListener("click", function() {
            document.querySelector(".sidebar").classList.toggle("active");
        });

        document.addEventListener('DOMContentLoaded', function() {
            var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
                initialView: 'dayGridMonth',
                height: 'auto',

                headerToolbar: {
                    left: 'title',
                    center: '',
                    right: 'prev,next'
                },

                events: [{
                        title: 'Audit: 2',
                        start: '2026-04-01',
                        className: 'audit-blue'
                    },
                    {
                        title: 'Score: 1',
                        start: '2026-04-03',
                        className: 'audit-green'
                    },
                    {
                        title: 'Audit: 1',
                        start: '2026-04-06',
                        className: 'audit-blue'
                    },
                    {
                        title: 'Score: 3',
                        start: '2026-04-06',
                        className: 'audit-green'
                    },
                    {
                        title: 'Audit: 4',
                        start: '2026-04-08',
                        className: 'audit-blue'
                    },
                    {
                        title: 'Score: 2',
                        start: '2026-04-09',
                        className: 'audit-green'
                    },
                    {
                        title: 'Audit: 1',
                        start: '2026-04-14',
                        className: 'audit-blue'
                    },
                    {
                        title: 'Score: 4',
                        start: '2026-04-17',
                        className: 'audit-green'
                    }
                ],

                dayCellClassNames: function(arg) {
                    if (arg.isToday) {
                        return ['custom-today'];
                    }
                }
            });

            calendar.render();
        });
    </script>

    {{-- <script>
        //new Choices('#parameterSelect', { searchEnabled: true, shouldSort: false });  

        const complianceData = {
            "Andhra Pradesh": {
                "compliant": 130,
                "non": 5
            },
            "Chhattisgarh": {
                "compliant": 25,
                "non": 2
            },
            "Gujarat": {
                "compliant": 124,
                "non": 11
            },
            "Karnataka": {
                "compliant": 278,
                "non": 19
            },
            "Kerala": {
                "compliant": 53,
                "non": 1
            },
            "Madhya Pradesh": {
                "compliant": 24,
                "non": 3
            },
            "Maharashtra": {
                "compliant": 51,
                "non": 3
            },
            "Odisha": {
                "compliant": 24,
                "non": 3
            },
            "Tamil Nadu": {
                "compliant": 130,
                "non": 5
            },
            "Telangana": {
                "compliant": 147,
                "non": 15
            },
            "West Bengal": {
                "compliant": 25,
                "non": 2
            }
        };
        const stateKeyMap = {
            'Maharashtra': 'maharashtra',
            'Gujarat': 'gujarat',
            'Karnataka': 'karnataka',
            'Delhi': 'nct of delhi',
            'Punjab': 'punjab',
            'Bihar': 'bihar',
            'Odisha': 'odisha',
            'Assam': 'assam',
            'Jharkhand': 'jharkhand',
            'Rajasthan': 'rajasthan',
            'Uttar Pradesh': 'uttar pradesh',
            'Haryana': 'haryana',
            'West Bengal': 'west bengal',
            'Tamil Nadu': 'tamil nadu',
            'Kerala': 'kerala',
            'Andhra Pradesh': 'andhra pradesh',
            'Telangana': 'telangana',
            'Madhya Pradesh': 'madhya pradesh',
            'Himachal Pradesh': 'himachal pradesh',
            'Arunanchal Pradesh': 'arunanchal pradesh',
            'Chandigarh': 'chandigarh',
            'Chhattisgarh': 'chhattisgarh',
            'Andaman and Nicobar': 'andaman and nicobar',
            'Daman and Diu': 'daman and diu',
            'Goa': 'goa',
            'Jammu and Kashmir': 'jammu and kashmir',
            'Ladakh': 'ladakh',
            'Manipur': 'manipur',
            'Meghalaya': 'meghalaya',
            'Mizoram': 'mizoram',
            'Nagaland': 'nagaland',
            'Puducherry': 'puducherry',
            'Sikkim': 'sikkim',
            'Tripura': 'tripura',
            'Uttarakhand': 'uttarakhand',
        };

        const mapData = Highcharts.maps['countries/in/custom/in-all-disputed'];

        const mapSeriesData = Object.entries(complianceData).map(([state, c]) => {
            const total = (c.compliant ?? 0) + (c.non ?? 0);
            const ratio = total ? c.compliant / total : 0;
            return {
                'hc-key': stateKeyMap[state],
                name: state,
                compliant: c.compliant,
                non: c.non,
                value: ratio
            };
        });

        // === MAP CHART ===
        const mapChart = Highcharts.mapChart('indiaMap', {
            chart: {
                map: mapData
            },
            title: null,
            colorAxis: {
                min: 0,
                max: 1,
                stops: [
                    [0, '#f10808ff'], // 0% compliance → red
                    [0.5, '#f79c0aff'], // 50% compliance → yellow
                    [1, '#0be012ff'] // 100% compliance → green
                ]
            },
            tooltip: {
                useHTML: true,
                formatter: function() {
                    const p = this.point;
                    const pct = (p.value * 100).toFixed(1);
                    return `<b>${p.name}</b><br>
              ✅ Compliant: ${p.compliant}<br>
              ❌ Non-Compliant: ${p.non}<br>
              Compliance: ${pct}%`;
                }
            },
            plotOptions: {
                series: {
                    allowPointSelect: true,
                }
            },
            series: [{
                data: mapSeriesData,
                mapData: mapData,
                joinBy: 'hc-key',
                borderColor: '#333',
                states: {
                    hover: {
                        color: '#a4edba'
                    }
                }
            }]
        });
    </script> --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            document.querySelectorAll('.auditTabBtn').forEach(button => {

                button.addEventListener('click', function() {

                    document.querySelectorAll('.auditTabBtn').forEach(btn => {
                        btn.classList.remove('active');
                    });

                    this.classList.add('active');

                    const status = this.dataset.status;

                    fetch(
                            `{{ url('get-action-issues') }}?status=${status}&cycle_id={{ $currentCycleId }}`
                        )
                        .then(response => response.json())
                        .then(res => {

                            let rows = '';

                            if (res && res.length > 0) {

                                res.forEach((issue, index) => {

                                    let statusText = issue.status || 'Open';
                                    let statusClass = 'progress-status';

                                    if (statusText.toLowerCase() === 'approved') {
                                        statusClass = 'completed-status';
                                    } else if (statusText.toLowerCase() ===
                                        'rejected') {
                                        statusClass = 'rejected-status';
                                    }

                                    rows += `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${issue.audit_id ?? '-'}</td>
                                    <td>${issue.agency_id ?? '-'}</td>
                                    <td>${issue.agency_name ?? '-'}</td>
                                    <td>${issue.issue_description ?? '-'}</td>
                                    <td>${issue.due_date ?? '-'}</td>
                                    <td>
                                        <span class="badge-pill ${statusClass}">
                                            ${statusText}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="/audit-closure/download/${issue.audit_id}"
                                           class="audit-download">
                                            <i class="fa fa-download me-1"></i> Download
                                        </a>
                                    </td>
                                </tr>
                            `;
                                });

                            } else {

                                rows = `
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    No issues found
                                </td>
                            </tr>
                        `;
                            }

                            document.getElementById('issueTableBody').innerHTML = rows;
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });

                });

            });

        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const months = @json($auditData['resolutionTrend']['months'] ?? []);
            const openIssues = @json(array_map('intval', $auditData['resolutionTrend']['open'] ?? []));
            const approvedIssues = @json(array_map('intval', $auditData['resolutionTrend']['approved'] ?? []));
            const rejectedIssues = @json(array_map('intval', $auditData['resolutionTrend']['rejected'] ?? []));

            console.log(months);
            console.log(openIssues);
            console.log(approvedIssues);
            console.log(rejectedIssues);

            if (!document.getElementById('chartResolution')) {
                console.error('chartResolution div not found');
                return;
            }

            Highcharts.chart('chartResolution', {
                chart: {
                    type: 'spline',
                    backgroundColor: 'transparent',
                    height: 300
                },

                title: {
                    text: null
                },

                credits: {
                    enabled: false
                },

                accessibility: {
                    enabled: false
                },

                xAxis: {
                    categories: months,
                    lineColor: '#e5e7eb',
                    tickColor: '#e5e7eb'
                },

                yAxis: {
                    min: 0,
                    title: {
                        text: null
                    },
                    gridLineColor: '#e5e7eb'
                },

                legend: {
                    align: 'center',
                    verticalAlign: 'bottom'
                },

                tooltip: {
                    shared: true
                },

                plotOptions: {
                    spline: {
                        lineWidth: 3,
                        marker: {
                            enabled: true,
                            radius: 4
                        }
                    }
                },

                series: [{
                        name: 'Open Issues',
                        data: openIssues,
                        color: '#3b82f6'
                    },
                    {
                        name: 'Approved Solutions',
                        data: approvedIssues,
                        color: '#16a34a'
                    },
                    {
                        name: 'Rejected Actions',
                        data: rejectedIssues,
                        color: '#dc2626'
                    }
                ]
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
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "<?php echo csrf_token(); ?>"
            }
        });

        $.ajax({
            type: 'POST',
            url: "{{ route('param_compliance_data') }}",
            data: {
                'currentCycleId': <?php echo $currentCycleId; ?>,
                'audit_type': "<?php echo $audit_type; ?>"
            },
            success: function(response) {
                $("#param_compliance_data").html(response);
            },
            error: function(xhr) {
                console.log("error in showing param_compliance_data");
            }
        });

        function getCrossTabData() {
            $.ajax({
                type: 'POST',
                url: "{{ route('getCrossTabData') }}",
                data: {
                    'currentCycleId': <?php echo $currentCycleId; ?>,
                    'audit_type': "<?php echo $audit_type; ?>",
                    'match_field_other': $("#match_field_other").val(),
                    'match_case': $("#match_case").val(),
                    'match_field': $("#match_field").val()

                },
                success: function(response) {
                    $("#crosstabResult").html(response);
                },
                error: function(xhr) {
                    console.log("error in showing crosstabResult");
                }
            });
        }

        function switchUser(val) {
            var email = "";
            if (val == 1) {
                email = "qd@mailinator.com";
            }
            if (val == 2) {
                email = "auditorqd@mailinator.com";
            }
            if (val == 3) {
                email = "qdegrees1@mailinator.com";
            }
            if (val == 4) {
                email = "raghav@mailinator.com";
            }
            $("#switchEmail").val(email);
            $("#switchUserForm").submit();
        }
    </script>
    <script>
        $(document).ready(function() {

            // Search functionality
            $("#issueSearch").on("keyup", function() {
                const value = $(this).val().toLowerCase();

                $("#issueTableBody tr").filter(function() {
                    const agencyId = $(this).find("td:eq(2)").text().toLowerCase();
                    const agencyName = $(this).find("td:eq(3)").text().toLowerCase();

                    $(this).toggle(
                        agencyId.indexOf(value) > -1 ||
                        agencyName.indexOf(value) > -1
                    );
                });

                applyStatusFilter();
            });

            // Status tabs
            $(".auditTabBtn").on("click", function() {
                $(".auditTabBtn").removeClass("active");
                $(this).addClass("active");
                applyStatusFilter();
            });

            function applyStatusFilter() {

                const selectedStatus = $(".auditTabBtn.active").data("status");
                const searchText = $("#issueSearch").val().toLowerCase();

                $("#issueTableBody tr").each(function() {

                    const rowStatus = ($(this).data("status") || "").toString().toLowerCase();

                    const agencyId = $(this).find("td:eq(2)").text().toLowerCase();
                    const agencyName = $(this).find("td:eq(3)").text().toLowerCase();

                    const matchesSearch =
                        agencyId.includes(searchText) ||
                        agencyName.includes(searchText);

                    const matchesStatus = rowStatus === selectedStatus;

                    $(this).toggle(matchesSearch && matchesStatus);
                });
            }

            // Initial filter
            applyStatusFilter();

        });
    </script>
@endsection
