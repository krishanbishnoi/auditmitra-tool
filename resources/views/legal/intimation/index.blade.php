@extends('layouts.master')

@section('content')
<div class="container mt-4">

    <div class="row">
        <div class="col-12">

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Legal Audit Intimations Sent</h5>
                </div>

                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    {{-- <th>Legal Audit Assign ID</th> --}}
                                    <th>Legal Cycle</th>
                                    <th>Advocate</th>
                                    <th>Advocate Email</th>
                                    <th>Auditor Name</th>
                                    <th>Audit Date</th>
                                    <th>Sent On</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($intimations as $key => $row)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        {{-- <td>
                                            <span class="badge bg-secondary">
                                                {{ $row->legal_audit_assign_id }}
                                            </span>
                                        </td> --}}
                                        <td>{{ $row->legal_cycle_name ?? '-' }}</td>
                                        <td class="fw-semibold">{{ $row->advocate_name }}</td>
                                        <td>{{ $row->advocate_email }}</td>
                                        <td>{{ $row->auditor_name }}</td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($row->audit_date)->format('d-m-Y') }}
                                        </td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($row->sent_on)->format('d-m-Y H:i') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            No intimations sent yet
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </div>

</div>
@endsection
