@extends('layouts.master')

@section('title', '| Issue Types')

@section('content')
    <div class="card">
        <div class="card-header">
            <strong>Issue Types</strong>
            <a href="{{ route('issue_types.create') }}" class="btn btn-primary btn-sm float-right">Create Issue Type</a>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Help Topic</th>
                        <th>Issue Type</th>
                        
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($issueTypes as $issueType)
                        <tr>
                            <td>{{ $issueType->id }}</td>
                            <td>{{ $issueType->helpTopic->name }}</td>
                            <td>{{ $issueType->name }}</td>
                             <!-- Display associated help topic -->
                            <td>
                                <a href="{{ route('issue_types.edit', $issueType->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('issue_types.destroy', $issueType->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
