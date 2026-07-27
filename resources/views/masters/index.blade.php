@extends('layouts.master')

@section('title', '| Masters')

@section('content')

<div class="animated fadeIn">

    <div class="row">

        <div class="col-lg-12">

            <div class="card">

                <div class="card-header">

                    <strong class="card-title">Masters List</strong>

                    <a href="{{ route('masters.create') }}"
                       class="btn btn-primary btn-sm float-right">
                        Add Master
                    </a>

                </div>

                <div class="card-body">

                    <table class="table table-bordered" id="kt_table_1">

                        <thead>

                            <tr>
                                <th>#</th>
                                <th>Type</th>
                                <th>Name</th>
                                <th>Possible Values</th>
                                <th>Show Add Button</th>
                                <th>Weight</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>

                        </thead>

                        <tbody>

                            @foreach($data as $key => $row)

                            <tr>

                                <td>{{ $key + 1 }}</td>

                                <td>{{ $row->type }}</td>

                                <td>{{ $row->name }}</td>

                                <td>{{ $row->possible_values }}</td>

                                <td>{{ $row->show_add_button }}</td>

                                <td>{{ $row->weight }}</td>

                                <td>
                                    @if($row->is_active == 'Y')
                                        Active
                                    @else
                                        Inactive
                                    @endif
                                </td>

                                <td>

                                    <a href="{{ route('masters.edit', $row->id) }}"
                                       class="btn btn-info btn-sm">

                                        <i class="fa fa-edit"></i>

                                    </a>

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

@section('js')

<script>
$(document).ready(function() {
    $('#kt_table_1').DataTable();
});
</script>

@endsection