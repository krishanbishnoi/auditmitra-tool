@extends('layouts.master')



@section('title', '| Clients')


<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS and Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- @section('sh-detail')

Users

@endsection -->



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

    <div class="col-lg-12" style="margin-top:10x">

    </div>

</div>

<div class="animated fadeIn">

    <div class="row">

        <div class="col-lg-12">

            <div class="card">


                <div class="card-body">

                    <table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">

                        <thead>

                            <tr>

                                <th scope="col">#</th>

                                <th scope="col">

                                    Name

                                </th>

                                <th scope="col">

                                    Logo

                                </th>

                                <th scope="col">

                                    Role

                                </th>

                                <th scope="col">

                                    Email

                                </th>

                                <th scope="col">

                                    Phone

                                </th>
                                <th scope="col">

                                    Color Code

                                </th>
                                <th scope="col">

                                    Levels

                                </th>
                                <th scope="col">

                                    Status

                                </th>



                                <th scope="col">

                                    Actions

                                </th>

                                <th scope="col">
                                    Disable Client
                                </th>
                                <th scope="col">
                                    Update Password
                                </th>
                            </tr>

                        </thead>

                        <tbody>
                            @foreach($data as $row)
                            @if(($row->roles->isNotEmpty() && $row->roles->first()->name != 'Quality Auditor') || $row->is_approved == 1)
                            <tr scope="row">
                                <td>{{$loop->iteration}}</td>

                                <td>{{$row->name}}</td>

                                <td>@if($row->logo)
                                    <img src="{{ asset('storage/app/' . $row->logo) }}" alt="Client Logo" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">

                                    @endif
                                </td>


                                <td>
                                    @if($row->roles->isNotEmpty())
                                    {{ $row->roles->first()->name }}
                                    @else
                                    No Role Assigned
                                    @endif
                                </td>

                                <td>{{$row->email}}</td>

                                <td>{{$row->mobile}}</td>
                                <td style="color: {{ $row->color_code }};">
                                    {{$row->color_code}}
                                </td>

                                <td>
                                    @if(is_array($row->levels))
                                        @foreach($row->levels as $level => $name)
                                            <span class="badge badge-info">{{ $level }}: {{ $name }}</span><br>
                                        @endforeach
                                    @else
                                    <span class="text-muted">No Levels</span>
                                    @endif
                                </td>

                                <td>
                                    @if($row->active_status == 0)
                                    Activated
                                    @else
                                    De-Activated
                                    @endif
                                </td>

                               <td nowrap>
    <a href="{{ url('client/' . Crypt::encrypt($row->id) . '/edit') }}" class="btn btn-sm btn-info" title="View">
        <i class="fa fa-edit"></i>
    </a>
    <a href="{{ route('client_module_allocation.module_allocation_view', $row->id) }}" class="btn btn-sm btn-secondary" title="Module Permission">
        <i class="fa fa-eye"></i>
    </a>
</td>


                                <td nowrap>
                                    <a class="btn btn-xs btn-info" onclick="block_client('{{ Crypt::encrypt($row->id) }}')" title="View">
                                        <i class="fa fa-ban"></i>
                                    </a>
                                </td>

                                <td nowrap>
                                    <a class="btn btn-xs btn-warning" onclick="openPasswordModal('{{ Crypt::encrypt($row->id) }}')" title="Update Password">
                                        <i class="fa fa-key"></i>
                                    </a>
                                </td>

                            </tr>
                            @endif
                            @endforeach
                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection

@section('css')

<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">

@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

<script>
    jQuery(document).on('ready', function() {

        jQuery('#kt_table_1').DataTable();

    })
</script>

<script type="text/javascript">
    function block_client(id) {
        if (confirm("Are you sure you want to block?")) {

            var link = '/client/' + id + '/disable'
            location.href = link;
        }
    }


    function updatePassword() {
        var userId = jQuery('#userId').val();
        var password = jQuery('#password').val();
        var confirmPassword = jQuery('#confirm_password').val();

        if (password !== confirmPassword) {
            jQuery('#errorMessage').text('Passwords do not match.').removeClass('d-none');
            return;
        } else {
            jQuery('#errorMessage').addClass('d-none');
        }


        jQuery.ajax({
            url: '{{ route("update.password") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                user_id: userId,
                password: password
            },
            success: function(response) {
                if (response.success) {
                    alert('Password updated successfully.');
                    jQuery('#passwordModal').modal('hide');
                } else {
                    jQuery('#errorMessage').text(response.message).removeClass('d-none');
                }
            },
            error: function() {
                jQuery('#errorMessage').text('Something went wrong.').removeClass('d-none');
            }
        });
    }
</script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap CSS -->
<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet"> -->

<!-- Bootstrap JS and Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Button to Open Modal -->
<a class="btn btn-warning" onclick="openPasswordModal()">Update Password</a>

<!-- Password Update Modal -->
<div class="modal fade" id="passwordModal" tabindex="-1" role="dialog" aria-labelledby="passwordModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="passwordModalLabel">Update Password</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label for="password">New Password</label>
                        <input type="password" class="form-control" id="password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" class="form-control" id="confirm_password" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="updatePassword()">Update Password</button>
            </div>
        </div>
    </div>
</div>

<!-- jQuery Script to Show Modal -->
<script>
    function openPasswordModal() {
        jQuery('#passwordModal').modal('show');
    }
</script>



@endsection