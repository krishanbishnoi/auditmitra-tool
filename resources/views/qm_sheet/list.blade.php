@extends('layouts.master')

@section('sh-title')
    QM - Sheet
@endsection

@section('sh-detail')
    All Client
@endsection

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
                        <strong class="card-title">Sheet List</strong>
                    </div>
                    <div id="successAlert" class="alert alert-success"
                        style="display:none; position:fixed; top:20px; right:20px; z-index:9999; min-width:300px;">
                    </div>
                    <div class="card-body">

                        <!--begin: Datatable -->
                        <table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">
                            <thead>
                                <tr>
                                    <th title="Field #1">#</th>

                                    {{-- <th title="Field #2">
										Process
									</th> --}}
                                    {{-- <th title="Field #2">
										Version
									</th> --}}
                                    <th title="Field #2">
                                        Name
                                    </th>
                                    <th title="Field #2">
                                        Lob
                                    </th>
                                    {{-- <th title="Field #2">
										Code
									</th> --}}
                                    <th title="Field #2">
                                        Type
                                    </th>
                                    <th title="Field #2">
                                        Total Parameters
                                    </th>
                                    <th title="Field #7">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $row)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>

                                        {{-- <td>
															{{$row->process->name}}
														</td> --}}
                                        {{-- <td>
															{{$row->version}}
														</td> --}}
                                        <td>
                                            {{ $row->name }}
                                        </td>
                                        <td>
                                            {{ $row->lob }}
                                        </td>
                                        {{-- <td>
															{{$row->code}}
														</td> --}}

                                        <td>
                                            {{ ucfirst($row->type) }}
                                        </td>
                                        <td>{{ $row->parameter->count() }}</td>
                                        {{-- <td nowrap>
                                            <div style="display: flex; gap: 5px;">

												{{ Form::open(['method' => 'delete', 'route' => ['qm_sheet.destroy', Crypt::encrypt($row->id)], 'onsubmit' => 'return delete_confirm()']) }}
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                                {{ Form::close() }}


												<a href="{{ url('qm_sheet/' . Crypt::encrypt($row->id) . '/edit') }}"
                                                    class="btn btn-sm btn-primary" title="Edit">
                                                    <i class="fa fa-edit"></i>
                                                </a>


												<a href="{{ url('qm_sheet/' . Crypt::encrypt($row->id) . '/parameter') }}"
                                                    class="btn btn-sm btn-warning" title="Manage Parameters">
                                                    <i class="fa fa-list"></i>
                                                </a>


												<a href="{{ url('audit_sheet/' . Crypt::encrypt($row->id)) }}"
                                                    class="btn btn-sm btn-success" title="Audit Sheet">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            </div>
                                        </td> --}}

                                        <td nowrap>
                                            <div style="display: flex; gap: 6px; align-items: center;">

                                                {{-- Delete --}}
                                                {{ Form::open([
                                                    'method' => 'delete',
                                                    'route' => ['qm_sheet.destroy', Crypt::encrypt($row->id)],
                                                    'onsubmit' => 'return delete_confirm()',
                                                    'style' => 'margin:0',
                                                ]) }}
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                                {{ Form::close() }}

                                                {{-- Edit --}}
                                                <a href="{{ url('qm_sheet/' . Crypt::encrypt($row->id) . '/edit') }}"
                                                    class="btn btn-sm btn-primary" title="Edit">
                                                    <i class="fa fa-edit"></i>
                                                </a>

                                                {{-- Manage Parameters --}}
                                                <a href="{{ url('qm_sheet/' . Crypt::encrypt($row->id) . '/parameter') }}"
                                                    class="btn btn-sm btn-warning" title="Manage Parameters">
                                                    <i class="fa fa-list"></i>
                                                </a>

                                                {{-- Audit Sheet --}}
                                                <a href="{{ url('audit_sheet/' . Crypt::encrypt($row->id)) }}"
                                                    class="btn btn-sm btn-success" title="Audit Sheet">
                                                    <i class="fa fa-eye"></i>
                                                </a>

                                                {{-- Active / Inactive --}}
                                                {{ Form::open([
                                                    'method' => 'post',
                                                    'route' => ['qm_sheet.activeStatus', $row->id],
                                                    'style' => 'margin:0',
                                                ]) }}
                                                <button type="submit"
                                                    class="btn btn-sm {{ $row->is_active ? 'btn-success' : 'btn-secondary' }}"
                                                    title="{{ $row->is_active ? 'Deactivate' : 'Activate' }}">
                                                    <i class="fa {{ $row->is_active ? 'fa-check' : 'fa-ban' }}"></i>
                                                </button>
                                                {{ Form::close() }}

                                                <button type="button" class="btn btn-sm btn-info assignSheetBtn"
                                                    data-id="{{ $row->id }}" data-name="{{ $row->name }}"
                                                    title="Assign Checksheet">
                                                    <i class="fa fa-user-plus"></i>
                                                </button>

                                            </div>
                                        </td>



                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>

                        <!--end: Datatable -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="assignModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <form id="assignForm">
                    @csrf

                    <input type="hidden" name="qm_sheet_id" id="qm_sheet_id">

                    <div class="modal-header">
                        <h5 class="modal-title">Assign Checksheet</h5>

                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <label>Select Auditors</label>

                        <select class="form-control select2" multiple id="users" name="users[]">

                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">
                                    {{ $user->name }}
                                </option>
                            @endforeach

                        </select>

                    </div>

                    <div class="modal-footer">

                        <button type="submit" class="btn btn-success">
                            Assign
                        </button>

                    </div>

                </form>

            </div>
        </div>
    </div>
@endsection
@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet">

    {{-- @include('shared.table_css'); --}}
@endsection

@section('js')
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

    <script>
        jQuery(document).ready(function() {

            jQuery('#kt_table_1').DataTable();

            jQuery('.select2').select2({
                width: '100%',
                dropdownParent: jQuery('#assignModal')
            });

            jQuery(document).on('click', '.assignSheetBtn', function() {

                var id = jQuery(this).data('id');

                jQuery('#qm_sheet_id').val(id);

                jQuery.ajax({

                    url: "{{ url('qm_sheet/assigned-users') }}/" + id,
                    type: "GET",

                    success: function(response) {

                        jQuery('#users').val(response).trigger('change');

                        jQuery('#assignModal').modal('show');

                    }

                });

            });

            jQuery('#assignForm').submit(function(e) {

                e.preventDefault();

                jQuery.ajax({

                    url: "{{ url('qm_sheet/assign') }}",

                    type: "POST",

                    data: jQuery(this).serialize(),

                    success: function(response) {

                        jQuery('#assignModal').modal('hide');

                        jQuery('#successAlert')
                            .text('Checksheet assigned successfully.')
                            .stop(true, true)
                            .fadeIn()
                            .delay(2000)
                            .fadeOut();
                    },

                    error: function(xhr) {

                        console.log(xhr);

                        alert('Something went wrong');

                    }

                });

            });

        });

        function delete_confirm() {

            return confirm("Are you sure you want to delete?");

        }
    </script>
@endsection
