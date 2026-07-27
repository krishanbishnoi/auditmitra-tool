@extends('layouts.master')

@section('title', 'Edit Predefined Question')

@section('sh-detail')
    Edit Predefined Question
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8 offset-lg-2">
        <div class="card">
            <div class="card-header">
                <strong>Edit Question</strong>
            </div>
            <div class="card-body">
                <form action="{{ route('predefined_question.update', $question->id) }}" method="POST">
                @csrf
                @method('PUT')

                    <div class="mb-3">
                        <label for="question" class="form-label">Question</label>
                        <input type="text" name="question" class="form-control" value="{{ old('question', $question->question) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="answer" class="form-label">Answer</label>
                        <textarea name="answer" class="form-control" rows="4" required>{{ old('answer', $question->answer) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select name="role" class="form-control" required>
                            <option value="">-- Select Role --</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role }}" {{ old('role', $question->role) == $role ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Question</button>
                    <a href="{{ route('predefined_questions.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
