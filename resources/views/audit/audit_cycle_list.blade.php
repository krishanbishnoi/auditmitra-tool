@extends('layouts.master')

@section('title', '| Audit Cycle')

@section('content')

<div class="row">
    <div class="col-lg-12" style="margin-top:10px"></div>
</div>

<div class="animated fadeIn">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <strong class="card-title">Audit Cycle List</strong>
                    <a class="btn btn-primary btn-sm float-right" href="{{ url('create-audit-cycle') }}">Create Audit Cycle</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="kt_table_1">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Created At</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $row)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $row->name }}</td>
                                        <td>{{ $row->created_at->format('Y-m-d h:i:s') }}</td>
                                        <td>
                                            <span id="status-container-{{ $row->id }}">
                                                @if($row->status == '0' || $row->status == '2')
                                                    <button class="toggle-status btn btn-sm btn-danger" data-id="{{ $row->id }}" data-status="1">Click To Activate</button>
                                                @else
                                                    <button class="toggle-status btn btn-sm btn-success" data-id="{{ $row->id }}" data-status="2">Click To Deactivate</button>
                                                @endif
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ url('edit-audit-cycle/' . $row->id) }}" class="btn btn-sm btn-clean btn-icon" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div> <!-- table-responsive -->
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#kt_table_1').DataTable({
                pageLength: 10,
                responsive: true,
                order: [[0, 'asc']]
            });
        });

        $(document).on('click', '.toggle-status', function () {
            const button = $(this);
            const id = button.data('id');
            const newStatus = button.data('status');

            $.ajax({
                url: '{{ route('toggle.status') }}',
                type: 'POST',
                data: {
                    id: id,
                    status: newStatus,
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Failed to update status.');
                    }
                },
                error: function () {
                    alert('An error occurred.');
                }
            });
        });
    </script>
@endsection
