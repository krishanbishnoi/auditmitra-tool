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
                    <div class="col-md-4">


                        <div class="col-lg-2 col-md-4">
                            <select class="form-select">
                                <option>Client</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">

                        <input type="date" class="form-input"+value="{{ today() }}">
                    </div>
                    <div class="col-md-4">

                        <input type="date" value="{{ today() }}">
                    </div>
                    <div class="col-12 text-end">
                        <button class="btn btn-primary"><i class="bi bi-download"></i> Export</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            @php
                $cards = [
                    ['AUDITS SCOPED (MTD)', $auditScored],
                    ['ACHIEVED', $achivedScore],
                    ['ACHIEVEMENT', "{$achievedPercent}%"],
                    ['OVERALL SCORE', '912'],
                    ['ACTION PLANNING', '6'],
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
                            <tr>
                                <td>Fibe</td>
                                <td>Banking</td>
                                <td>In-store</td>
                                <td class="text-success">45/48</td>
                                <td>49/52</td>
                                <td>47/50</td>
                                <td>41/50</td>
                                <td>9%</td>
                            </tr>
                            <tr>
                                <td>RBL</td>
                                <td>Telecom</td>
                                <td>App</td>
                                <td class="text-danger">30/38</td>
                                <td>26/37</td>
                                <td>22/38</td>
                                <td>20/37</td>
                                <td class="text-danger">35%</td>
                            </tr>
                            <tr>
                                <td>TATA Capital</td>
                                <td>Banking</td>
                                <td>In-store</td>
                                <td>43/45</td>
                                <td>42/45</td>
                                <td>43/45</td>
                                <td>43/45</td>
                                <td>5%</td>
                            </tr>
                            <tr>
                                <td>Sunstone</td>
                                <td>QSR</td>
                                <td>Delivery</td>
                                <td class="text-danger">22/30</td>
                                <td>20/30</td>
                                <td>17/30</td>
                                <td>15/30</td>
                                <td class="text-danger">38%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
