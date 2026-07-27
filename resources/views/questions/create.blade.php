@extends('layouts.master')

@section('content')
<div class="container">
    <h4>Add Question for Sub Parameter: <strong>{{ $subParam->sub_parameter }}</strong></h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('store_question') }}" method="POST">
        @csrf
        <input type="hidden" name="sub_parameter_id" value="{{ $subParam->id }}">

        <div class="form-group">
            <label>Question Text</label>
            <textarea name="question_text" class="form-control" rows="4" required></textarea>
        </div>

       <button type="submit" class="btn btn-primary">
    <i class="fa fa-save"></i> Save Question
</button>

{{-- <a href="{{url('parameter/'.Crypt::encrypt($subParam->id).'/edit')}}" class="btn btn-secondary">
    <i class="fa fa-arrow-left"></i> Back
</a> --}}

    </form>

    {{-- Table of Existing Questions --}}
@if($questions && $questions->count())
    <div class="mt-5">
        <h5>Existing Questions</h5>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Question Text</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($questions as $index => $question)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $question->question_text }}</td>
                       <td>
    {{-- Edit Button with Icon --}}
    <a href="{{ route('questions.edit', $question->id) }}" class="btn btn-sm btn-warning">
        <i class="fa fa-pencil"></i> Edit
    </a>

    {{-- Delete Button with Icon --}}
    <form action="{{ route('questions.destroy', $question->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure to delete this question?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger">
            <i class="fa fa-trash"></i> Delete
        </button>
    </form>
</td>
      
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="mt-4 text-muted">No questions added yet.</div>
@endif

@endsection
