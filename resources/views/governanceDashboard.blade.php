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
                    <form action="{{ route('governanceDashboard')}}" method="get">
                        @csrf
                        <div class="col-md-3">
                            <div class="col-lg-2 col-md-4">
                                <select class="form-select">
                                    @foreach ($clients as $client)
                                    {{-- <option>Select Client</option> --}}
                                        <option value="{{ $client->client_id }}">{{ $client->client_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <input type="date" class="form-control" name="start_date" value="{{ today()->format('y-m-d') }}">
                        </div>
                        <div class="col-md-3">

                            <input type="date" class="form-control" name="end_date" value="{{ today()->format('y-m-d') }}">
                        </div>  
                        <div class="col-md-3">

                            <input type="SUBMIT" value="Submit">
                        </div>
                        <div class="col-12 text-end">
                            <button class="btn btn-primary"><i class="bi bi-download"></i> Export</button>
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
            <li class="nav-item"><a class="nav-link" href="#">Action Plannig</a></li>
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
                                <tr>

                                    <td>{{ $client->client_name }}</td>
                                    <td>{{ $totalAllocation[$client->client_id] ?? 0 }}</td>
                                    <td>{{ $totalAchievement[$client->client_id] ?? 0 }}</td>
                                    <td>{{ $achieveMentPercentage[$client->client_id] ?? 0 }} % </td>
                                    <td>{{ $overallScore[$client->client_id] ?? 0 }}</td>
                                    <td>{{ $overallScorePercentage[$client->client_id] ?? 0 }} % </td>
                                    <td>{{ $actionPlanning[$client->client_id] ?? 0 }}
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
