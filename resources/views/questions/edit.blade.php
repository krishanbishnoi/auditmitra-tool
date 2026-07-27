@extends('layouts.master')

@section('content')
<div class="container">
    <h4>Edit Question for Sub Parameter: <strong>{{ $subParam->sub_parameter }}</strong></h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('questions.update', $question->id) }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Question Text</label>
            <textarea name="question_text" class="form-control" rows="4" required>{{ $question->question_text }}</textarea>
        </div>

       <button type="submit" class="btn btn-success">
    <i class="fa fa-check"></i> Update Question
</button>

<a href="{{ url()->previous() }}" class="btn btn-secondary">
    <i class="fa fa-arrow-left"></i> Back
</a>

    </form>
</div>
@endsection
