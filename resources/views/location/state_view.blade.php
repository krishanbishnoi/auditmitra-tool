@extends('layouts.master')

@section('title', '| State List')

@section('content')

<div class="row">
    <div class="col-lg-12" style="margin-top:10px"></div>
</div>

<div class="animated fadeIn">
    <div class="row">
        <div class="col-lg-12">

            <div class="card">
                <div class="card-header">
                    <strong class="card-title">State List</strong>
                    <a href="{{ route('location.index') }}" class="btn btn-sm btn-primary float-right">Add New State</a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            <ul>
                                @foreach (session('success') as $msg)
                                    <li>{{ $msg }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">State Name</th>
                                <th scope="col">Region</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($states as $k => $state)
                            <tr scope="row">
                                <td>{{ $k + 1 }}</td>
                                <td>{{ $state->name }}</td>
                                <td>{{ $state->region_name }}</td>
                                <td nowrap>
                                    <a href="{{ route('location.state_edit', $state->id) }}" class="btn btn-xs btn-info mr-1" title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <form action="{{ route('location.destroy', $state->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this state?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-danger mr-1" title="Delete">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
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

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.2/css/buttons.dataTables.min.css">
@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.2/js/dataTables.buttons.min.js"></script>

<script>
    jQuery(document).ready(function () {
        jQuery('#kt_table_1').DataTable();
    });
</script>
@endsection
