<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Governance Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: #f4f6fb;
            font-family: Arial, sans-serif
        }

        .header {
            background: #1f3156;
            color: #fff;
            padding: 30px 0
        }

        .card-stat {
            border-left: 4px solid #1f3156
        }

        .nav-tabs .nav-link.active {
            border: none;
            border-bottom: 3px solid #1f3156;
            font-weight: bold
        }

        .table thead {
            background: #eef2f8
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="container">
            <h2>AuditMitr Governance Dashboard</h2>
            <p class="mb-0">Audit Governance Tracking</p>
        </div>
    </div>

    <div class="container py-4">

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <form action="{{ route('governanceDashboard') }}" method="get">
                        <div class="row align-items-end g-3">
                            @csrf
                            <div class="col-md-3">
                                <label>Client</label>
                                <select name="client_id" class="form-select">
                                    <option value="all" {{ request('client_id') == 'all' ? 'selected' : '' }}>
                                        All Clients
                                    </option>

                                    @foreach ($clientList as $client)
                                        <option value="{{ $client->client_id }}"
                                            {{ request('client_id') == $client->client_id ? 'selected' : '' }}>
                                            {{ $client->client_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label>Start Date</label>
                                <input type="date" class="form-control" name="start_date"
                                    value="{{ request('start_date') }}">
                            </div>

                            <div class="col-md-3">
                                <label>End Date</label>
                                <input type="date" class="form-control" name="end_date"
                                    value="{{ request('end_date') }}">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                {{-- <a href="{{ route('dashboard.export', request()->all()) }}" class="btn btn-success">
                                <i class="bi bi-download"></i> Export
                            </a> --}}
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            @php
                $cards = [
                    ['TOTAL ALLOCATION', $auditScored],
                    ['TOTAL ACHIEVEMENT', $achivedScore],
                    ['ACHIEVEMENT %', "{$achievedPercent}%"],
                    ['OVERALL SCORE', $overall_score],
                    ['ACTION PLANNING', $actionPlan],
                ];
            @endphp
            @foreach ($cards as $c)
                <div class="col-lg col-md-6">
                    <div class="card shadow-sm card-stat h-100">
                        <div class="card-body">
                            <small class="text-muted">{{ $c[0] }}</small>
                            <h2>{{ $c[1] }}</h2>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <ul class="nav nav-tabs mb-4">
            <li class="nav-item"><a class="nav-link active" href="#">Client View</a></li>
            <li class="nav-item"><a class="nav-link" href="#">QA Performance</a></li>
            
        </ul>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5>Scope vs Achieved</h5>
                    <div class="btn-group">
                        <button class="btn btn-dark btn-sm">Weekly</button>
                        <button class="btn btn-outline-secondary btn-sm">Daily</button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Total Allocation</th>
                                <th>Total Achievement</th>
                                <th>Achievement %</th>
                                <th>Overall Score</th>
                                <th>Overall Score %</th>
                                <th>Action Planning</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($clients as $client)
                                @php
                                    $achievementPercentage =
                                        $client->totalAllocation > 0
                                            ? round(($client->totalAchievement / $client->totalAllocation) * 100, 2)
                                            : 0;
                                @endphp
                                <tr>
                                    <td>{{ $client->client_name }}</td>
                                    <td>{{ $client->totalAllocation }}</td>
                                    <td>{{ $client->totalAchievement }}</td>
                                    <td>{{ $achievementPercentage }} % </td>
                                    <td>{{ $client->overallScore ?? 0 }}</td>
                                    <td>{{ round($client->overallScorePercentage ?? 0, 2) }} % </td>
                                    <td>{{ $client->actionPlanning }}</td>
                                    {{-- {{ $actionPlanning[$client->client_id] ?? 0 }} --}}
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
