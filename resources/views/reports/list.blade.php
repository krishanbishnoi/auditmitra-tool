@extends('layouts.master')

@section('title', '| Yards')

@section('content')
    <div class="row">
        <div class="col-lg-12" style="margin-top:10px"></div>
    </div>

    <div class="animated fadeIn">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    {{-- <div class="card-header">
                        <h3>Reports</h3>
                    </div> --}}
                    <div class="card-header d-flex justify-content-between align-items-center">
    <h6 class="card-title mb-0">
        <strong>Report List</strong>
    </h6>
    <a href="{{ route('bulkDownloadForm') }}" class="btn btn-xs btn-outline-primary py-0 px-2">
        <i class="fa fa-download mr-1"></i> Bulk Download
    </a>
                    </div>

                    <div class="card-body">
                        <table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Audit ID</th>
                                    <th>Process Review Period</th>
                                    <th>Agency Name</th>
                                    <th>Agency Code</th>
                                    <th>Location</th>
                                    <th>Audit Date</th>
                                    <th>Closure Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($audit_reports as $reports)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>00{{ $reports->audit_id }}</td>
                                        <td>{{ $reports->process_review_period ?? '' }}</td>
                                        <td>{{ $reports->agency_name }}</td>
                                        <td>{{ $reports->agency_id }}</td>
                                        <td>{{ $reports->location }}</td>
                                        <td>{{ $reports->audit_date }}</td>
                                        <td>
                                            @php
                                                $closure_status = DB::table('closure_audits')
                                                    ->where('audit_id', $reports->audit_id)
                                                    ->value('status');
                                            @endphp

                                            @if ($closure_status == 0)
                                                @if(\Carbon\Carbon::parse($reports->created_at)->lt(\Carbon\Carbon::now()->subDays($tat)))
                                                    <strong class="text-danger" >No Response</strong>
                                                @else
                                                    <strong class="text-warning">Pending</strong>
                                                @endif
                                            @elseif($closure_status == 1)
                                                <strong class="text-success">Approved</strong>
                                            @elseif($closure_status == 2)
                                                <strong class="text-danger">Rejected</strong>
                                            @else
                                                <strong class="text-info">-</strong>
                                            @endif

                                        </td>
                                        <td>
                                            <a href="{{ route('audit.downloadReports', ['audit_id' => $reports->audit_id]) }}"
                                                class="btn btn-sm btn-clean btn-icon btn-icon-md" title="Download">
                                                Download Reports
                                            </a>
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
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.2/css/buttons.dataTables.min.css">
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

    <!-- DataTables Buttons -->
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.html5.min.js"></script>

    <script>
        jQuery(document).ready(function() {
            jQuery('#kt_table_1').DataTable({
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'excelHtml5',
                        text: 'Excel',
                        title: 'Audit_Report',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        text: 'PDF',
                        title: 'Audit_Report',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        exportOptions: {
                            columns: ':visible'
                        },
                        customize: function(doc) {
                            doc.defaultStyle.fontSize = 8;
                        }
                    }
                ]
            });
        });
    </script>
@endsection
