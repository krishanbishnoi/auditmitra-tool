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

    @php
        $segment1 = request()->segment(1);
        $allocatedmodule = App\Helpers\Helper::allocatedmodulelist();
    @endphp

    <div class="animated fadeIn">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        @if ($segment1 == 'submit_audited_list')
                            <strong class="card-title">Submitted Audited List</strong>
                        @else
                            <strong class="card-title">Saved Audited List</strong>
                        @endif
                    </div>
                    <div class="card-body">

                        @php $user = Auth::user(); @endphp

                        @if ($user->hasRole(['Quality Control']))
                            <form method="post" action="{{ route('audited_list') }}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-3 form-group">
                                        <label>Lob Name*</label>
                                        <select name="lob" class="form-control">
                                            <option value="">Choose Lob Name</option>
                                            <option value="collection">Collection</option>
                                            <option value="commercial_vehicle">Commercial Vehicle</option>
                                            <option value="rural">Rural</option>
                                            <option value="alliance">Alliance</option>
                                            <option value="credit_card">Credit Card</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label>Start Date*</label>
                                        <input name="start_date" type="text" class="form-control" />
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label>End Date*</label>
                                        <input name="end_date" type="text" class="form-control" />
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <input name="search" type="submit" class="btn btn-sm btn-primary mt-4"
                                            value="Search" />
                                    </div>
                                </div>
                            </form>
                        @endif

                        <table class="table table-bordered" id="kt_table_1">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Audit ID</th>
                                    @if (Auth::user()->client_id == 74)
                                        <th>Audit Type</th>
                                    @endif
                                    <th>Month</th>
                                    <th>Audit Date</th>
                                    <th>Lob</th>
                                    <th>Agency Location</th>
                                    <th>State</th>
                                    <th>Product</th>
                                    <th>Sheet Type</th>
                                    <th>Agency Name</th>
                                    <th>Agency Code</th>
                                    <th>Collection Manager</th>
                                    <th>Collection Manager Email</th>
                                    <th>Auditor Name</th>
                                    <th>Visited Date & Time</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $row)
                                    @php
                                        $name = '';
                                        $agency_code = '';
                                        $state = '';
                                        switch ($row->qmsheet->type) {
                                            case 'agency':
                                                $name = $row->agency->name ?? '';
                                                $agency_code = $row->agency->agency_id ?? '';
                                                $state = $row->agency->stateName->name ?? '';
                                                break;
                                            case 'branch':
                                                $state = $row->branch->stateName->name ?? '';
                                                break;
                                            case 'yard':
                                                $name = $row->yard->name ?? '';
                                                $state = $row->yard->stateName->name ?? '';
                                                break;
                                            case 'branch_repo':
                                                $name = $row->branchRepo->name ?? '';
                                                $state = $row->branchRepo->stateName->name ?? '';
                                                break;
                                            case 'agency_repo':
                                                $name = $row->agencyRepo->name ?? '';
                                                $state = $row->agencyRepo->stateName->name ?? '';
                                                break;
                                            case 'yard_repo':
                                                $name = $row->yardRepo->name ?? '';
                                                $state = $row->yardRepo->stateName->name ?? '';
                                                break;
                                        }
                                        $status = $segment1 == 'submit_audited_list' ? 'Submited' : 'Saved';
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>00{{ $row->id }}</td>
                                        @if (Auth::user()->client_id == 74)
                                            <td>
                                                {{ $row->virtual_audit == 1 ? 'Virtual' : 'Physical' }}
                                            </td>
                                        @endif
                                        <td>{{ \Carbon\Carbon::parse($row->created_at)->formatLocalized("%b'%y") }}</td>
                                        <td>{{ $row->created_at }}</td>
                                        <td>{{ ucfirst($row->qmsheet->lob ?? '') }}</td>
                                        <td>{{ ucfirst($row->agency_location ?? '') }}</td>
                                        <td>{{ ucfirst(strtolower($state ?? '')) }}</td>
                                        <td>{{ $row->product->name ?? '' }}</td>
                                        <td>{{ ucfirst($row->qmsheet->type ?? '') }}</td>
                                        <td>{{ ucwords(strtolower($name)) }}</td>
                                        <td>{{ $agency_code }}</td>
                                        <td>{{ $row->user->name ?? '' }}</td>
                                        <td>{{ $row->user->email ?? '' }}</td>
                                        <td>{{ $row->qa_qtl_detail->name ?? '' }}</td>
                                        <td>{{ $row->created_at ?? '' }}</td>
                                        <td>{{ $status ?? '' }}</td>
                                        <td nowrap>
                                            @if ($status == 'Saved' && $user->hasRole(['Admin', 'Quality Auditor']))
                                                <a href="{{ url('audit_sheet/' . Crypt::encrypt($row->id) . '/edit') }}"
                                                    class="btn btn-sm btn-clean btn-icon btn-icon-md" title="edit"><i
                                                        class="fa fa-edit"></i></a>
                                            @elseif($status != 'Saved')
                                                @if (in_array(28, $allocatedmodule))
                                                    <a href="{{ url('submitted-audit-data-v2/' . $row->id) }}"
                                                        class="btn btn-sm btn-clean btn-icon btn-icon-md" title="View"><i
                                                            class="fa fa-eye"></i></a>
                                                    <a href="{{ route('audit.v2.remark-update_view', $row->id) }}"
                                                        class="btn btn-sm btn-clean btn-icon btn-icon-md"
                                                        title="Update Remarks">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    {{-- <a href="{{ url('audit_detail/'.Crypt::encrypt($row->id).'/view') }}" class="btn btn-sm btn-clean btn-icon btn-icon-md" title="View"><i class="fa fa-eye"></i></a> --}}
                                                @else
                                                    <a href="{{ url('audit_detail/' . Crypt::encrypt($row->id) . '/view') }}"
                                                        class="btn btn-sm btn-clean btn-icon btn-icon-md" title="View"><i
                                                            class="fa fa-eye"></i></a>
                                                @endif
                                                {{-- <a href="{{ url('submitted-audit-data-v2/1436') }}" class="btn btn-sm btn-clean btn-icon btn-icon-md" title="View"><i class="fa fa-eye"></i></a> --}}
                                                <!-- <a href="{{ url('duplicate_sheet/' . Crypt::encrypt($row->id)) }}" onclick="return confirm('Duplicate this audit sheet?')" class="btn btn-sm btn-clean btn-icon btn-icon-md" title="Duplicate"><i class="fa fa-copy"></i></a> -->
                                                {{-- @if ($user->hasRole(['Admin', 'Quality Control', 'Client']))
                                                <a href="{{ url('audit_sheet/'.Crypt::encrypt($row->id).'/qcedit') }}" class="btn btn-sm btn-clean btn-icon btn-icon-md" title="QC Edit"><i class="fa fa-edit"></i></a>
                                            @endif --}}
                                              <a href="{{ route('audit.v2.remark-qc', $row->id) }}"
                                                        class="btn btn-sm btn-clean btn-icon btn-icon-md"
                                                        title="Update Remarks">
                                                       QC
                                                    </a>
                                                <a href="{{ route('audit.downloadArtifactsall', $row->id) }}"
                                                    class="btn btn-sm btn-warning">
                                                    Download Artifacts
                                                </a>
                                            @endif

                                            {{-- for qc --}}
                                            @if (auth()->user()->client_id == 285 && $user->hasRole(['Admin']))
                                                @if ($row->is_qc_approved == 1)
                                                    <span class="badge badge-success">Approved</span>
                                                @else
                                                    <form action="{{ route('audit.qc.approve', $row->id) }}" method="POST"
                                                        style="display:inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm"
                                                            onclick="return confirm('Are you sure you want to approve this audit?')">
                                                            QC Approve
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
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
