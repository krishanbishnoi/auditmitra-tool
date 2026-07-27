<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Closure Form</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* General Form Styling */
        body {
            background-color: #f7f9fc;
            font-family: 'Arial', sans-serif;
        }

        h1 {
            color: #343a40;
            font-weight: 600;
        }

        .container {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        /* Table Styling */
        .table {
            margin-bottom: 0;
        }

        .table-bordered th, .table-bordered td {
            border: 1px solid #dee2e6;
            vertical-align: middle;
        }

        .thead-dark th {
            background-color: #343a40;
            color: #fff;
        }

        /* Form Controls */
        .form-control {
            border-radius: 6px;
            box-shadow: none;
            border: 1px solid #ced4da;
        }

        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .custom-file-input {
            cursor: pointer;
        }

        .custom-file-label {
            overflow: hidden;
        }

        /* Button Styling */
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            padding: 10px 20px;
            font-size: 18px;
            border-radius: 50px;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        /* Custom table row hover effect */
        .table-hover tbody tr:hover {
            background-color: #f1f3f5;
        }

        /* Margin adjustments for smaller screens */
        @media (max-width: 768px) {
            .form-control, .custom-file-label {
                margin-bottom: 15px;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<h1 class="text-center mb-4">Audit Closure Form</h1>

<div class="container">
<form action="{{ route('audit.closure.submit', ['closure_id' => $closureId, 'audit_id' => $auditId, 'link' => $link]) }}" method="POST" enctype="multipart/form-data">
@csrf
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>Parameter</th>
                        <th>Remark</th>
                        <th>Justification</th>
                        <th>Action Taken</th>
                        <th>Artifact Upload</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($unsetParams as $param)
                        <tr>
                            <td>{{ $param->sub_parameter }}</td>
                            <td>{{ $param->remark }}</td>
                            <td>
                      
                                <textarea name="justification_{{ $param->id }}" class="form-control" placeholder="Enter justification" rows="3" required></textarea>
                            </td>
                            <td>
                                <textarea name="action_taken_{{ $param->id }}" class="form-control" placeholder="Action Taken" rows="3" required></textarea>
                            </td>

                       
                            <td>
                                <div class="custom-file">
                                    <input type="file" name="artifact_{{ $param->id }}" class="custom-file-input" id="artifact_{{ $param->id }}">
                                    <label class="custom-file-label" for="artifact_{{ $param->id }}">Choose file</label>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary btn-lg">Submit Closure</button>
        </div>
        
    </form>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
