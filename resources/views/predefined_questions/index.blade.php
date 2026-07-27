@extends('layouts.master')

@section('title', '| Predefined Questions')

@section('sh-detail')
List of Predefined Questions
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <strong>Predefined Questions</strong>
                <a href="{{ route('predefined_questions.create') }}" class="btn btn-primary btn-sm float-right">
                    <i class="fa fa-plus"></i> Add New Question
                </a>
            </div>

            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Question</th>
                            <th>Answer</th>
                            <th>Role</th>
                           
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($questions as $key => $question)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $question->question }}</td>
                                <td>{!! nl2br(e($question->answer)) !!}</td>
                                <td>{{ ucfirst($question->role) }}</td>
                               
                                <td>
    <a href="{{ route('predefined_questions.edit', $question->id) }}" class="fa fa-edit btn btn-sm btn-info">
    
    </a>

    <form action="{{ route('predefined_questions.destroy', $question->id) }}"
          method="POST"
          class="d-inline"
          onsubmit="return confirm('Are you sure you want to delete this question?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="fa fa-trash btn btn-sm btn-danger">
        </button>
    </form>
</td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No predefined questions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                
            </div>
        </div>
    </div>
</div>
@endsection
