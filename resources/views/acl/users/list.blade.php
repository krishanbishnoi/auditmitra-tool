@extends('layouts.master')

@section('title', '| Users')

@section('css')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
@endsection

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-12 mt-2"></div>
    </div>

    <div class="animated fadeIn">
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <strong class="card-title">User List</strong>

                        <div>
                            <a class="btn btn-primary btn-sm" href="{{ route('bulkDeactivate') }}">De-Activate
                                user(Bulk)</a>
                            @auth
                                @hasanyrole('Admin')
                                    <a class="btn btn-primary btn-sm" href="{{ route('userUpload') }}">Import Users (Auditors Bulk
                                        Upload)</a>
                                @else
                                    <a class="btn btn-primary btn-sm" href="{{ route('userUpload') }}">Import Users (Create bulk
                                        user)</a>
                                @endhasanyrole
                            @endauth
                            <a class="btn btn-primary btn-sm" href="{{ route('excelDownloadUser') }}" target="_blank">Export
                                Users</a>
                        </div>
                    </div>

                    <div class="card-body">
                        <table class="table table-striped table-bordered" id="kt_table_1">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Audit {{$agencyLabel}}</th>
                                    <th>Role</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                    <th>Disable User</th>
                                    <th>Update Password</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $row)
                                    @if (($row->roles->isNotEmpty() && $row->roles->first()->name != 'Quality Auditor') || $row->is_approved == 1)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $row->name }}</td>
                                            <td>{{ App\User::where('email', $row->created_by)->pluck('name')->first() }}
                                            </td>
                                            <td>{{ $row->roles->first()->name ?? 'No Role Assigned' }}</td>
                                            <td>{{ $row->email }}</td>
                                            <td>{{ $row->mobile }}</td>
                                            <td>{{ $row->active_status == 0 ? 'Activated' : 'De-Activated' }}</td>
                                            <td>
                                                <a href="{{ url('user/' . Crypt::encrypt($row->id) . '/edit') }}"
                                                    class="btn btn-xs btn-info" title="Edit">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            </td>
                                            <td>
                                                <a class="btn btn-xs {{ $row->active_status == 0 ? 'btn-danger' : 'btn-success' }}"
                                                    style="cursor:pointer" data-toggle="tooltip"
                                                    title="{{ $row->active_status == 0 ? 'Click to Disable' : 'Click to Enable' }}"
                                                    onclick="block_user('{{ Crypt::encrypt($row->id) }}', {{ $row->active_status }})">
                                                    <i
                                                        class="fa {{ $row->active_status == 0 ? 'fa-ban' : 'fa-check' }}"></i>
                                                </a>

                                            </td>
                                            <td>
                                                <a class="btn btn-xs btn-warning"
                                                    onclick="openPasswordModal('{{ Crypt::encrypt($row->id) }}')"
                                                    title="Update Password">
                                                    <i class="fa fa-key"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div> <!-- card-body -->
                </div>
            </div>
        </div>
    </div>

    <!-- Password Update Modal -->
    <div class="modal fade" id="passwordModal" tabindex="-1" role="dialog" aria-labelledby="passwordModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Password</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="passwordForm">
                        @csrf
                        <input type="hidden" id="userId">
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" class="form-control" id="password" required>
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password" required>
                        </div>
                        <div class="alert alert-danger d-none" id="errorMessage"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="updatePassword()">Update Password</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <!-- DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#kt_table_1').DataTable({
                responsive: true,
                autoWidth: false,
            });
        });

        function block_user(id, status) {
            let message = status == 0 ?
                "Are you sure you want to block this user?" :
                "Are you sure you want to unblock this user?";

            if (confirm(message)) {
                window.location.href = '/user/' + id + '/disable';
            }
        }


        function openPasswordModal(userId) {
            $('#userId').val(userId);
            $('#password').val('');
            $('#confirm_password').val('');
            $('#errorMessage').addClass('d-none');
            $('#passwordModal').modal('show');
        }

        function updatePassword() {
            let userId = $('#userId').val();
            let password = $('#password').val();
            let confirmPassword = $('#confirm_password').val();

            $('#errorMessage').addClass('d-none').html('');

            // Frontend match check (quick UX)
            if (password !== confirmPassword) {
                $('#errorMessage')
                    .removeClass('d-none')
                    .text('Passwords do not match.');
                return;
            }

            $.ajax({
                url: '{{ route('update.password') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    user_id: userId,
                    password: password,
                    password_confirmation: confirmPassword
                },
                success: function(response) {
                    if (response.success) {
                        $('#successMessage').text('Password updated successfully').show();
                        $('#passwordModal').modal('hide');
                    } else {
                        $('#errorMessage')
                            .removeClass('d-none')
                            .text(response.message ?? 'Unable to update password.');
                    }
                },
                error: function(xhr) {
                    // ✅ Show Laravel validation errors
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let message = Object.values(errors).flat().join('<br>');
                        $('#errorMessage').removeClass('d-none').html(message);
                    } else {
                        $('#errorMessage')
                            .removeClass('d-none')
                            .text('Something went wrong. Please try again.');
                    }
                }
            });
        }
    </script>
@endsection
