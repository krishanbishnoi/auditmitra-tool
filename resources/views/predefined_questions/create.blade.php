@extends('layouts.master')

@section('content')
<div class="container mt-5">
    <h2>Add Predefined Question</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('predefined_questions.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="question" class="form-label">Question</label>
            <input
                type="text"
                name="question"
                id="question"
                class="form-control @error('question') is-invalid @enderror"
                value="{{ old('question') }}"
                required
            >
            @error('question')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="answer" class="form-label">Answer</label>
            <textarea
                name="answer"
                id="answer"
                rows="5"
                class="form-control @error ('answer') is-invalid @enderror"
                required
            >{{ old('answer') }}</textarea>
            @error('answer')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="role" class="form-label">Role</label>
           <select
    name="role"
    id="role"
    class="form-control @error('role') is-invalid @enderror"
    required
>
    <option value="">-- Select Role --</option>
    @foreach ($roles as $role)
        <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
            {{ ucfirst($role->name) }}
        </option>
    @endforeach
</select>
            @error('role')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Add Question</button>
    </form>
</div>
@endsection
