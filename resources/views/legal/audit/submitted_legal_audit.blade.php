@extends('layouts.master')

@section('sh-title')
    Audited
@endsection

@section('sh-detail')
    Call
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12" style="margin-top:10px"></div>
    </div>


    <div class="animated fadeIn">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">

                        <strong class="card-title">Submitted Audited List</strong>
                    </div>
                    <div class="card-body">


                        <table class="table table-bordered" id="kt_table_1">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Audit ID</th>

                                    <th>Audit Type</th>

                                    <th>Month</th>
                                    <th>Audit Date</th>
                                    <th>Advocate Name</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($legal_audits as $row)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>00{{ $row->id }}</td>

                                        <td>
                                            Legal
                                        </td>

                                        <td>Month</td>
                                        <td>{{ $row->audit_date }}</td>

                                        <td>{{ $row->advocate_name }}</td>

                                        <td>{{ $row->status }}</td>
                                        <td nowrap>
                                            {{-- @if ($status == 'Saved' && $user->hasRole(['Admin', 'Quality Auditor']))
                                        @elseif($status != 'Saved')
                                            <a href="{{ url('audit_detail/'.Crypt::encrypt($row->id).'/view') }}" class="btn btn-sm btn-clean btn-icon btn-icon-md" title="View"><i class="fa fa-eye"></i></a>
                                            <a href="{{ url('duplicate_sheet/'.Crypt::encrypt($row->id)) }}" onclick="return confirm('Duplicate this audit sheet?')" class="btn btn-sm btn-clean btn-icon btn-icon-md" title="Duplicate"><i class="fa fa-copy"></i></a>
                                            @if ($user->hasRole(['Admin', 'Quality Control', 'Client']))
                                                <a href="{{ url('audit_sheet/'.Crypt::encrypt($row->id).'/qcedit') }}" class="btn btn-sm btn-clean btn-icon btn-icon-md" title="QC Edit"><i class="fa fa-edit"></i></a>
                                            @endif
                                            <a href="{{ route('audit.downloadArtifactsall', $row->id) }}"
                                                    class="btn btn-sm btn-warning">
                                                    Download Artifacts
                                                </a>
                                        @endif --}}

                                            <a href="{{ url('/legal/legal-audit/view/' . $row->id) }}"
                                                class="btn btn-sm btn-clean btn-icon btn-icon-md" title="View"><i
                                                    class="fa fa-eye"></i></a>
                                            <a href="{{ url('legal/audit/' . $row->id . '/generate-pdf') }}"
                                                class="btn btn-sm btn-success btn-icon btn-icon-md"
                                                title="Download">Download</a>


                                            {{-- <a href="{{ url('/legal/audit/'.$row->id.'/pdf') }}"
                                                    class="btn btn-sm btn-warning">
                                                    Download 
                                                </a> --}}
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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#kt_table_1').DataTable({
                pageLength: 10,
                responsive: true,
                order: [
                    [0, 'asc']
                ]
            });
        });
    </script>
@endsection
