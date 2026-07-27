@extends('layouts.master')
@section('title', '| Users')

@section('content')

<div class="animated fadeIn">
    <div class="row">
        <div class="col-lg-12">

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong class="card-title">Audit Agency List</strong>

                    {{-- Filter by Client - Right aligned --}}
                    @hasrole('Super Admin')
                    <div style="width: 300px;">
                        <select id="clientFilter" class="form-control select2" style="width: 100%;">
                            <option value="">🔍 Filter by Client</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endhasrole
                </div>

                <div class="card-body">
                    <table class="table table-striped table-bordered table-hover table-checkable" id="kt_table_1">
                        <thead>
                            <tr>
                                <th>Sr.No.</th>
                                <th class="font-weight-bold">Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($auditagency as $row)
                                <tr data-client-id="{{ $row->client_id }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="font-weight-bold">{{ $row->name }}</td>
                                    <td>{{ $row->mobile }}</td>
                                    <td>{{ $row->email }}</td>
                                    <td nowrap>
                                        <div class="btn-group">
                                            <a href="{{ url('audit_agency/' . Crypt::encrypt($row->id) . '/edit') }}" class="btn btn-xs btn-info mr-1" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        </div>
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
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet" />
@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>

<script>
$(document).ready(function () {
    $('.select2').select2();

    var table = $('#kt_table_1').DataTable();

    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        var selectedClientId = $('#clientFilter').val();
        if (!selectedClientId || selectedClientId === '') {
            return true;
        }

        var rowNode = table.row(dataIndex).node();
        var rowClientId = $(rowNode).data('client-id');

        return rowClientId == selectedClientId;
    });

    $('#clientFilter').on('change', function () {
        table.draw();
    });
});
</script>
@endsection
