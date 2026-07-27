@extends('layouts.master')

@section('title', '| Support Tickets')

@section('content')

<!-- Display Validation Errors -->
@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<!-- Create New Ticket Button -->
<div class="row">
    <div class="col-lg-12" style="margin-top:10px">
        <a href="{{ route('support_tickets.create') }}" class="btn btn-primary">Create New Ticket</a>
    </div> 
</div>

<!-- Filters -->
<div class="row mb-3" style="margin-top: 15px;">
    <div class="col-md-3">
        <label for="filterPriority">Filter by Priority:</label>
        <select id="filterPriority" class="form-control">
            <option value="">All</option>
            <option value="Low">Low</option>
            <option value="Medium">Medium</option>
            <option value="High">High</option>
        </select>
    </div>

    <div class="col-md-3">
        <label for="filterStatus">Filter by Status:</label>
        <select id="filterStatus" class="form-control">
            <option value="">All</option>
            <option value="Open">Open</option>
            <option value="Closed">Closed</option>
        </select>
    </div>
</div>

<!-- Support Tickets Table -->
<div class="animated fadeIn">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <table class="table table-striped table-bordered" id="kt_table_1" style="width:100%;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Help Topic</th>
                                <th>Issue Type</th>
                                <th>Subject</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tickets as $ticket)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $ticket->help_topic }}</td>
                                <td>{{ $ticket->issue_type }}</td>
                                <td>{{ $ticket->subject }}</td>
                                <td>{{ $ticket->priority }}</td>
                                <td>
                                    @if($ticket->status === 'Open')
                                    <span class="badge bg-danger">{{ $ticket->status }}</span>
                                    @elseif($ticket->status === 'Closed')
                                    <span class="badge bg-success">{{ $ticket->status }}</span>
                                    @else
                                    <span class="badge bg-secondary">{{ $ticket->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('support_tickets.show', $ticket->id) }}" class="btn btn-sm btn-info" title="View">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    @if($ticket->status !== 'Closed')
                                    <a href="{{ route('support_tickets.edit', $ticket->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <form action="{{ route('support_tickets.destroy', $ticket->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                    @role('Super Admin')
                                    
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal-{{ $ticket->id}}">
                                        Close
                                    </button>
                                    <div class="modal fade" id="exampleModal-{{ $ticket->id}}" tabindex="-1" aria-labelledby="feedbackModalLabel{{ $ticket->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <form method="POST" action="{{ route('support_tickets.close', $ticket->id) }}">
                                                @csrf
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="feedbackModalLabel{{ $ticket->id }}">Close Ticket - Feedback</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <textarea name="feedback" class="form-control" rows="4" placeholder="Enter feedback (optional)..."></textarea>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger">Close Ticket</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    
                                    @endrole
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('css')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
<!-- Bootstrap 5 CSS (Optional, if not already included in your layout) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
@endpush

@push('scripts')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<!-- Bootstrap 5 JS Bundle (with Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(function() {
        try {
            let table = $('#kt_table_1').DataTable();

            // Custom search filters for Priority and Status columns
            $.fn.dataTable.ext.search.push(function(settings, data) {
                let priorityFilter = $('#filterPriority').val();
                let statusFilter = $('#filterStatus').val();

                let rowPriority = data[4]; // Priority column index (0-based)
                let rowStatusHtml = data[5]; // Status column with badge HTML

                // Strip HTML tags to get plain text status
                let rowStatus = $('<div>').html(rowStatusHtml).text().trim();

                if (priorityFilter && priorityFilter !== rowPriority) {
                    return false;
                }
                if (statusFilter && statusFilter !== rowStatus) {
                    return false;
                }
                return true;
            });

            // Redraw table on filter change
            $('#filterPriority, #filterStatus').on('change', function() {
                table.draw();
            });

        } catch (e) {
            console.error('Error initializing support tickets page:', e);
        }
    });
</script>
@endpush
