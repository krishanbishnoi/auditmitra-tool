@extends('layouts.master')

@section('title', '| Modules')

@section('content')
    <div class="row">
        <div class="col-lg-12" style="margin-top:10px">
        </div>
    </div>

    <div class="animated fadeIn">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <strong class="card-title">Modules List</strong>
                    </div>

                    <div class="card-body">
                        <table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Module ID</th>
                                    <th scope="col">Module Name</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($permissions as $permission)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $permission->id }}</td>
                                        <td>{{ $permission->module_name }}</td>
                                        <td nowrap>
                                            <a href="{{ route('module_permissions.edit', $permission->id) }}"
                                               class="btn btn-sm btn-info" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a href="{{ route('module_permissions.show', $permission->id) }}"
                                               class="btn btn-sm btn-secondary" title="View">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <form action="{{ route('module_permissions.destroy', $permission->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div> <!-- card-body -->
                </div> <!-- card -->
            </div> <!-- col -->
        </div> <!-- row -->
    </div> <!-- animated -->
@endsection

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
@endsection

@section('js')
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script>
    jQuery(document).ready(function () {
        jQuery('#kt_table_1').DataTable({
            paging: false,
            searching: false,
            info: false
        });
    });
</script>
@endsection
