@extends('layouts.master')

@section('content')
<div class="card">
    <div class="card-header">
        <h4>Advocates</h4>
        <a href="{{ route('legal.advocates.create') }}" class="btn btn-primary btn-sm float-right">
            Add Advocate
        </a>
    </div>

    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Status</th>
                    <th width="180">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($advocates as $advocate)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $advocate->name }}</td>
                    <td>
                        <span class="badge {{ $advocate->is_active ? 'badge-success' : 'badge-danger' }}">
                            {{ $advocate->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('legal.advocates.edit', $advocate->id) }}"
                           class="btn btn-warning btn-sm">
                           Edit
                        </a>

                        <a href="{{ route('legal.advocates.toggle', $advocate->id) }}"
                           class="btn btn-sm {{ $advocate->is_active ? 'btn-danger' : 'btn-success' }}">
                           {{ $advocate->is_active ? 'Deactivate' : 'Activate' }}
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
