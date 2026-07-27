@extends('layouts.master')

@section('title', '| View')

<!-- @section('sh-detail')
        Users
@endsection -->

@section('content')
    <div class="row">
        <div class="col-lg-12" style="margin-top:10x">
        </div>
    </div>
    <div class="animated fadeIn">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <strong class="card-title">View</strong>
                    </div>
                    <div class="card-body">
                        <div class="card-header">
                            <strong class="card-title">Unsatisfactory Parameters</strong>
                        </div>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Parameter</th>
                                    <th>Remark</th>
                                    <th>Submitted At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($unsetParams as $param)
                                    <tr>
                                        <td>{{ $param->sub_parameter }}</td>  <!-- Use parameter_name here -->
                                        <td>{{ $param->remark }}</td>  <!-- Use remark from the query -->
                                        <td>{{ $param->created_at }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <hr>
                    <div class="card-body">
                        <div class="card-header">
                            <strong class="card-title">Recevied Closer Parameters</strong>
                        </div>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Parameter</th>
                                    <th>Artifact</th>
                                    <th>Justification</th>
                                    <th>Action Taken</th>
                                    <th>Submitted At</th>
                                    <th>Approval Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($closureDetails as $closure)
                                    <tr>
                                        <td>{{ $closure->sub_parameter }}</td>
                                        <td>
                                        @if ($closure->artifact)
                                            <a href="{{ asset('storage/app/' . $closure->artifact) }}" target="_blank">View Artifact</a>
                                        @else
                                            No Artifact
                                        @endif
                                        </td>
                                        <td>{{ $closure->justification }}</td>
                                        <td>{{ $closure->action_taken }}</td>
                                        <td>{{ $closure->created_at }}</td>
                                        <td>{{ $closure->approval_status }}</td>
                                        <td style="width: 150px;">
                                            @if ($closure->approval_status == 'Pending')
                                            <!-- Approve Button -->
                                            <form action="{{ route('artifact.approve', $closure->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                            </form>

                                                <!-- Reject Button with Modal for Reason -->
                                            <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#rejectModal{{ $closure->id }}">Reject
                                            </button>

                                                <!-- Reject Modal -->
                                                <div class="modal fade" id="rejectModal{{ $closure->id }}" tabindex="-1"
                                                    role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="rejectModalLabel">Reject
                                                                    Artifact</h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form action="{{ route('artifact.reject', $closure->id) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    <div class="form-group">
                                                                        <label for="rejection_reason">Reason for
                                                                            Rejection</label>
                                                                        <textarea name="rejection_reason" class="form-control" required></textarea>
                                                                    </div>
                                                                    <button type="submit" class="btn btn-danger">Submit
                                                                        Rejection</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @elseif ($closure->approval_status == 'Approved')
                                                <span class="text-success">Approved</span>
                                            @else
                                                <span class="text-danger">Rejected</span>
                                                <br><strong>Reason:</strong> {{ $closure->rejection_reason }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                       <!-- Check if $closureDetails is not empty -->
                            @if ($closureDetails->isNotEmpty())

                            <!-- Check if any artifacts are pending approval -->
                            @php
                                // Get unique parameters
                                $uniqueParameters = $closureDetails->pluck('sub_parameter')->unique();

                                // Check approval status for each unique parameter
                                $allApproved = true; // Assume all are approved initially
                                foreach ($uniqueParameters as $parameter) {
                                    // Check if at least one entry for this parameter is approved
                                    $isApproved = $closureDetails->where('sub_parameter', $parameter)->contains('approval_status', 'Approved');
                                    if (!$isApproved) {
                                        $allApproved = false; // If any parameter is not approved, set to false
                                        break; // No need to check further
                                    }
                                }
                            @endphp

                            @if ($allApproved)
                                <!-- All artifacts approved message -->
                                <button class="btn btn-success" disabled>All Artifacts Approved</button>
                            @elseif ($closureDetails->where('approval_status', 'Pending')->isNotEmpty())
                                <!-- Don't show Resend Audit Closure button if there's a pending approval -->
                                <button class="btn btn-warning" disabled>Pending Approvals Exist</button>
                            @else
                                <!-- Resend Audit Closure button -->
                                <form action="{{ route('audit.resend_closure', $closureDetails->first()->audit_id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-warning">Resend Audit Closure</button>
                                </form>
                            @endif

                            @else
                            <!-- Do not show anything if $closureDetails is empty -->
                            @endif

                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection
@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.2/css/buttons.dataTables.min.css">
@endsection
@section('js')
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.html5.min.js"></script>
    <script>
        jQuery(document).on('ready', function() {
            jQuery('#kt_table_1').DataTable();
            // {
            // 	dom: 'Bfrtip',
            // buttons: [
            //     'excelHtml5',
            // ]
            // }
        })
    </script>
@endsection
