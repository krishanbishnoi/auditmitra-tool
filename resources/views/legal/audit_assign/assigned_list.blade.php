@extends('layouts.master')

@section('title', '| Assigned Legal Audits')

@section('content')
<div class="card">
    <div class="card-header">
        <h4>My Assigned Legal Audits</h4>
    </div>

    <div class="card-body">
        @if($audits->count() > 0)
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Advocate</th>
                        <th>Audit Date</th>
                        <th>Assigned On</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($audits as $audit)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $audit->advocate_name }}</td>
                            <td>{{ \Carbon\Carbon::parse($audit->audit_date)->format('d-m-Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($audit->created_at)->format('d-m-Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-muted">No legal audits assigned yet.</p>
        @endif
    </div>
</div>
@endsection
