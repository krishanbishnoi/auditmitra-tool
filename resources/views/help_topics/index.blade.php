@extends('layouts.master')

@section('title', '| Help Topics')

@section('content')
    <div class="card">
        <div class="card-header">
            <strong>Help Topics</strong>
            <a href="{{ route('help_topics.create') }}" class="btn btn-primary btn-sm float-right">Create Help Topic</a>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($helpTopics as $topic)
                        <tr>
                            <td>{{ $topic->id }}</td>
                            <td>{{ $topic->name }}</td>
                            <td>
                                <a href="{{ route('help_topics.edit', $topic->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('help_topics.destroy', $topic->id) }}" method="POST" style="display:inline-block;">
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
