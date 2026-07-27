@extends('layouts.master')

@section('content')
<div class="card">
    <div class="card-header">
        <h4>Legal Audit Assignments</h4>
        <a href="{{ route('legal.audit.assign.create') }}"
           class="btn btn-primary btn-sm float-right">
           Assign Audit
        </a>
    </div>

    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Advocate</th>
                    <th>Auditor</th>
                    <th>Audit Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($assignments as $row)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $row->advocate_name }}</td>
                    <td>{{ $row->auditor_name }}</td>
                    <td>{{ date('d-m-Y', strtotime($row->audit_date)) }}</td>
                    <td>
                        <a href="{{ route('legal.audit.assign.edit', $row->id) }}"
                           class="btn btn-warning btn-sm">
                           Edit
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
